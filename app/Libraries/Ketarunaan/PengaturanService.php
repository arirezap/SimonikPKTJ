<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\AngkatanModel;
use App\Models\Ketarunaan\KelasModel;
use App\Models\Ketarunaan\ProgramStudiModel;
use App\Models\Ketarunaan\RefWilayah3tpModel;
use App\Models\Ketarunaan\TahunAkademikModel;
use App\Models\SettingModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Service Modul 8.3 Pengaturan Data Rujukan, Matriks Peran, dan Parameter Operasional ECC 2.0 (GOV-05..07, NFR-09).
 * Perubahan rujukan bersifat non-destruktif (nonaktif/aktif, tidak pernah menghapus riwayat taruna).
 */
class PengaturanService
{
    protected BaseConnection $db;
    protected SettingModel $settingModel;

    public function __construct(?BaseConnection $db = null, ?SettingModel $settingModel = null)
    {
        $this->db           = $db ?? \Config\Database::connect();
        $this->settingModel = $settingModel ?? new SettingModel();
        helper('ketarunaan');
    }

    /**
     * Menghimpun seluruh data untuk halaman Pengaturan ECC 2.0.
     *
     * @return array<string, mixed>
     */
    public function getWorkspaceData(): array
    {
        ketarunaan_ensure_kalender_terkini($this->db);

        $prodiList = $this->db->table('ref_program_studi p')
            ->select('p.*, (SELECT COUNT(*) FROM pendidikan_taruna pt WHERE pt.program_studi_id = p.id) AS total_taruna')
            ->orderBy('p.is_aktif', 'DESC')
            ->orderBy('p.id', 'ASC')
            ->get()
            ->getResultArray();

        $angkatanList = $this->db->table('ref_angkatan a')
            ->select('a.*, (SELECT COUNT(*) FROM pendidikan_taruna pt WHERE pt.angkatan_id = a.id) AS total_taruna')
            ->orderBy('a.is_aktif', 'DESC')
            ->orderBy('a.tahun_masuk', 'DESC')
            ->get()
            ->getResultArray();

        $tahunAkademikList = $this->db->table('ref_tahun_akademik')
            ->select('id, kode_tahun, tahun, semester, tanggal_mulai, tanggal_selesai, is_aktif')
            ->orderBy('is_aktif', 'DESC')
            ->orderBy('tahun', 'DESC')
            ->orderBy('semester', 'ASC')
            ->get()
            ->getResultArray();

        $kelasList = $this->db->table('ref_kelas k')
            ->select('k.id, k.program_studi_id, k.angkatan_id, k.tingkat, k.nama_kelas, k.is_aktif, p.kode_prodi, p.nama_prodi, a.tahun_masuk, a.kode_romawi, (SELECT COUNT(*) FROM pendidikan_taruna pt WHERE pt.kelas_id = k.id) AS total_taruna')
            ->join('ref_program_studi p', 'p.id = k.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = k.angkatan_id', 'left')
            ->orderBy('k.is_aktif', 'DESC')
            ->orderBy('k.tingkat', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $wilayahList = $this->db->table('ref_wilayah_3tp')
            ->select('id, kode_wilayah, provinsi, kabupaten_kota, kategori_3tp, dasar_acuan, versi_acuan, tahun_berlaku, is_aktif')
            ->orderBy('is_aktif', 'DESC')
            ->orderBy('provinsi', 'ASC')
            ->orderBy('kabupaten_kota', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'referensi' => [
                'prodi'          => $prodiList,
                'angkatan'       => $angkatanList,
                'tahun_akademik' => $tahunAkademikList,
                'kelas'          => $kelasList,
                'wilayah_3tp'    => $wilayahList,
            ],
            'role_matrix'     => $this->getRoleAuthorityMatrix(),
            'user_directory'  => $this->getSelectiveUserRolesDirectory(),
            'operasional'     => $this->getOperationalSettings(),
            'data_mode'       => ketarunaan_data_mode_info(),
        ];
    }

    /**
     * Tambah atau perbarui baris data rujukan secara non-destruktif.
     *
     * @param string               $jenis
     * @param array<string, mixed> $payload
     * @param int|null             $id
     * @return array{success: bool, message: string, id?: int}
     */
    public function saveReferensi(string $jenis, array $payload, ?int $id = null): array
    {
        $jenisClean = strtolower(trim($jenis));

        return match ($jenisClean) {
            'prodi'          => $this->saveProdi($payload, $id),
            'angkatan'       => $this->saveAngkatan($payload, $id),
            'tahun_akademik' => $this->saveTahunAkademik($payload, $id),
            'kelas'          => $this->saveKelas($payload, $id),
            'wilayah_3tp'    => $this->saveWilayah3tp($payload, $id),
            default          => ['success' => false, 'message' => 'Jenis data rujukan tidak dikenali.'],
        };
    }

    /**
     * Mengubah status aktif/nonaktif data rujukan tanpa menghapus rekaman lama (GOV-05).
     *
     * @return array{success: bool, message: string}
     */
    public function toggleReferensiStatus(string $jenis, int $id): array
    {
        $tableMap = [
            'prodi'          => ['table' => 'ref_program_studi', 'has_updated' => true, 'label' => 'Program Studi'],
            'angkatan'       => ['table' => 'ref_angkatan', 'has_updated' => true, 'label' => 'Angkatan'],
            'tahun_akademik' => ['table' => 'ref_tahun_akademik', 'has_updated' => true, 'label' => 'Tahun Akademik'],
            'kelas'          => ['table' => 'ref_kelas', 'has_updated' => true, 'label' => 'Kelas'],
            'wilayah_3tp'    => ['table' => 'ref_wilayah_3tp', 'has_updated' => false, 'label' => 'Wilayah 3TP'],
        ];

        $jenisClean = strtolower(trim($jenis));
        if (!isset($tableMap[$jenisClean])) {
            return ['success' => false, 'message' => 'Jenis data rujukan tidak dikenali.'];
        }

        $cfg      = $tableMap[$jenisClean];
        $existing = $this->db->table($cfg['table'])->where('id', $id)->get()->getRowArray();
        if (!$existing) {
            return ['success' => false, 'message' => 'Data rujukan tidak ditemukan.'];
        }

        $newState = ((int) ($existing['is_aktif'] ?? 0) === 1) ? 0 : 1;
        $upd      = ['is_aktif' => $newState];
        if ($cfg['has_updated']) {
            $upd['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->transStart();
        $this->db->table($cfg['table'])->where('id', $id)->update($upd);
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal memperbarui status data rujukan. Silakan coba lagi.'];
        }

        log_audit(
            'UPDATE',
            $cfg['table'],
            (string) $id,
            ['is_aktif' => (int) ($existing['is_aktif'] ?? 0)],
            ['is_aktif' => $newState]
        );

        $statusText = $newState === 1 ? 'diaktifkan kembali' : 'dinonaktifkan';

        return ['success' => true, 'message' => $cfg['label'] . ' berhasil ' . $statusText . '.'];
    }

    /**
     * Mengambil nilai parameter operasional ECC 2.0 dari tabel settings.
     *
     * @return array<string, string>
     */
    public function getOperationalSettings(): array
    {
        return [
            'ecc2_data_mode'           => (string) $this->settingModel->getValue('ecc2_data_mode', 'simulasi'),
            'ecc2_skala_ip_maks'       => (string) $this->settingModel->getValue('ecc2_skala_ip_maks', '4.00'),
            'ecc2_batas_jam_lowongan'  => (string) $this->settingModel->getValue('ecc2_batas_jam_lowongan', '24'),
            'ecc2_catatan_operasional' => (string) $this->settingModel->getValue(
                'ecc2_catatan_operasional',
                'Data operasional resmi ECC PKTJ.'
            ),
        ];
    }

    /**
     * Menyimpan pembaruan parameter operasional ECC 2.0 (termasuk penanda Data Simulasi/Operasional).
     *
     * @param array<string, mixed> $payload
     * @return array{success: bool, message: string}
     */
    public function saveOperationalSettings(array $payload): array
    {
        $dataMode = strtolower(trim((string) ($payload['ecc2_data_mode'] ?? 'simulasi')));
        if (!in_array($dataMode, ['simulasi', 'produksi'], true)) {
            return ['success' => false, 'message' => 'Pilih penanda jenis data yang sah.'];
        }

        $rawIp      = str_replace(',', '.', trim((string) ($payload['ecc2_skala_ip_maks'] ?? '4.00')));
        $skalaIp    = is_numeric($rawIp) ? (float) $rawIp : 0.0;
        if ($skalaIp < 2.0 || $skalaIp > 4.5) {
            return ['success' => false, 'message' => 'Batas skala IP harus berada pada rentang 2,00 sampai 4,50.'];
        }

        $batasJam = (int) ($payload['ecc2_batas_jam_lowongan'] ?? 24);
        if ($batasJam < 1 || $batasJam > 720) {
            return ['success' => false, 'message' => 'Batas jam tayang lowongan harus antara 1 sampai 720 jam.'];
        }

        $catatan = trim((string) ($payload['ecc2_catatan_operasional'] ?? ''));
        if ($catatan === '') {
            return ['success' => false, 'message' => 'Catatan operasional wajib diisi.'];
        }
        if (mb_strlen($catatan) > 255) {
            return ['success' => false, 'message' => 'Catatan operasional maksimal 255 karakter.'];
        }

        $updates = [
            'ecc2_data_mode'           => $dataMode,
            'ecc2_skala_ip_maks'       => number_format($skalaIp, 2, '.', ''),
            'ecc2_batas_jam_lowongan'  => (string) $batasJam,
            'ecc2_catatan_operasional' => $catatan,
        ];

        $before = $this->getOperationalSettings();
        $now    = date('Y-m-d H:i:s');

        $this->db->transStart();
        foreach ($updates as $key => $val) {
            $exists = $this->db->table('settings')->where('setting_key', $key)->countAllResults();
            if ($exists > 0) {
                $this->db->table('settings')->where('setting_key', $key)->update([
                    'setting_value' => $val,
                    'updated_at'    => $now,
                ]);
            } else {
                $this->db->table('settings')->insert([
                    'setting_key'   => $key,
                    'setting_name'  => ucwords(str_replace('_', ' ', $key)),
                    'setting_value' => $val,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan parameter operasional. Silakan coba lagi.'];
        }

        SettingModel::clearCache();
        log_audit('UPDATE', 'settings', 'ecc2_operasional', $before, $updates);

        return ['success' => true, 'message' => 'Parameter operasional ketarunaan berhasil disimpan.'];
    }

    /**
     * Matriks resmi kewenangan peran pada ECC 2.0 (GOV-06).
     *
     * @return array<int, array<string, string>>
     */
    public function getRoleAuthorityMatrix(): array
    {
        return [
            [
                'peran'       => 'Admin Sistem',
                'kode'        => 'admin',
                'cakupan'     => 'Penuh lintas modul ketarunaan, alumni, tata kelola, dan pengguna.',
                'validasi'    => 'Semua pengajuan & koreksi',
                'pelanggaran' => 'Penuh + data sensitif',
                'referensi'   => 'Kelola penuh (non-destruktif)',
            ],
            [
                'peran'       => 'Validator Ketarunaan',
                'kode'        => 'ketarunaan_validator',
                'cakupan'     => 'Persetujuan akhir perubahan status, capaian, prestasi, beasiswa, dan koreksi.',
                'validasi'    => 'Setuju / kembalikan',
                'pelanggaran' => 'Penuh + penetapan',
                'referensi'   => 'Kelola data rujukan & parameter',
            ],
            [
                'peran'       => 'Staf Ketarunaan & Pusbangkar',
                'kode'        => 'ketarunaan / pusbangkar',
                'cakupan'     => 'Pencatatan data taruna, perubahan status, prestasi, dan lowongan mitra.',
                'validasi'    => 'Ajukan draf & tindak lanjut perbaikan',
                'pelanggaran' => 'Sesuai kewenangan pembinaan',
                'referensi'   => 'Lihat & ajukan pembaruan',
            ],
            [
                'peran'       => 'Pengelola Karir, Alumni & Tracer Study',
                'kode'        => 'karir_alumni',
                'cakupan'     => 'PIC utama Tracer Study, pembaruan kontak alumni, perusahaan mitra, dan lowongan kerja.',
                'validasi'    => 'Ajukan koreksi kontak alumni & lowongan',
                'pelanggaran' => 'Tertutup (tidak berhak)',
                'referensi'   => 'Terbatas sesuai modul karir & alumni',
            ],
            [
                'peran'       => 'Bidang Kerjasama & Mitra Pengguna Lulusan',
                'kode'        => 'kerjasama',
                'cakupan'     => 'Pengelolaan perusahaan mitra, kontak rekrutmen, lowongan mitra, dan distribusi ke mitra pengguna lulusan.',
                'validasi'    => 'Ajukan verifikasi kontak mitra & lowongan',
                'pelanggaran' => 'Tertutup (tidak berhak)',
                'referensi'   => 'Tertutup',
            ],
            [
                'peran'       => 'Pimpinan (Direktur / Wadir / Kabag)',
                'kode'        => 'direktur / wadir / kabag_aak',
                'cakupan'     => 'Pemantauan eksekutif Dashboard, pratinjau laporan resmi, dan persetujuan SK.',
                'validasi'    => 'Direktur & Kabag AAK penuh; Wadir pantau',
                'pelanggaran' => 'Ringkasan & perkara terbukti',
                'referensi'   => 'Pantau / kelola kebijakan',
            ],
            [
                'peran'       => 'Taruna / Alumni Mandiri',
                'kode'        => 'taruna / alumni',
                'cakupan'     => 'Akses terbatas rekam jejak pribadi, klaim beasiswa sendiri, atau kontak alumni sendiri.',
                'validasi'    => 'Tidak berhak memvalidasi',
                'pelanggaran' => 'Hanya milik sendiri (terbatas)',
                'referensi'   => 'Tertutup',
            ],
        ];
    }

    /**
     * Mengambil ringkasan akun pengguna dan peran aktif secara selektif (tanpa kata sandi).
     *
     * @return array<int, array<string, mixed>>
     */
    private function getSelectiveUserRolesDirectory(): array
    {
        $priorityRolesSql = "'admin','ketarunaan_validator','ketarunaan','pusbangkar','karir_alumni','kerjasama','pengguna_lulusan','direktur','wadir','kabag_aak','kabag_kuk'";
        $priorityExpr     = "(CASE WHEN u.role IN ({$priorityRolesSql}) OR EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = u.id AND ur.role_name IN ({$priorityRolesSql})) THEN 0 ELSE 1 END)";

        $users = $this->db->table('users u')
            ->select('u.id, u.nama_lengkap, u.nip, u.username, u.unit, u.role')
            ->whereNotIn('u.role', ['taruna', 'alumni'])
            ->orderBy($priorityExpr, 'ASC', false)
            ->orderBy('u.nama_lengkap', 'ASC')
            ->limit(100)
            ->get()
            ->getResultArray();

        if (empty($users)) {
            return [];
        }

        $userIds    = array_column($users, 'id');
        $extraRoles = [];

        $roleRows = $this->db->table('user_roles')
            ->select('user_id, role_name')
            ->whereIn('user_id', $userIds)
            ->get()
            ->getResultArray();

        foreach ($roleRows as $rr) {
            $uid                = (int) $rr['user_id'];
            $extraRoles[$uid][] = (string) $rr['role_name'];
        }

        foreach ($users as &$u) {
            $uid          = (int) $u['id'];
            $allRoles     = array_values(array_unique(array_filter(array_merge(
                [(string) ($u['role'] ?? '')],
                $extraRoles[$uid] ?? []
            ))));
            $u['peran_list'] = $allRoles;
        }
        unset($u);

        return $users;
    }

    /**
     * @return array{success: bool, message: string, id?: int}
     */
    private function saveProdi(array $payload, ?int $id): array
    {
        $kode    = strtoupper(trim((string) ($payload['kode_prodi'] ?? '')));
        $nama    = trim((string) ($payload['nama_prodi'] ?? ''));
        $jenjang = strtoupper(trim((string) ($payload['jenjang'] ?? '')));

        if ($kode === '' || $nama === '' || $jenjang === '') {
            return ['success' => false, 'message' => 'Kode, nama, dan jenjang program studi wajib diisi.'];
        }

        $dupQuery = $this->db->table('ref_program_studi')->where('kode_prodi', $kode);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Kode program studi "' . $kode . '" sudah terdaftar.'];
        }

        $model = new ProgramStudiModel();
        $data  = [
            'kode_prodi' => $kode,
            'nama_prodi' => $nama,
            'jenjang'    => $jenjang,
            'is_aktif'   => isset($payload['is_aktif']) ? ((int) $payload['is_aktif'] === 1 ? 1 : 0) : 1,
        ];

        $this->db->transStart();
        if ($id !== null && $id > 0) {
            $existing = $model->find($id);
            if (!$existing) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Data program studi tidak ditemukan.'];
            }
            $model->update($id, $data);
            $savedId = $id;
            $aksi    = 'UPDATE';
        } else {
            $model->insert($data);
            $savedId  = (int) $model->getInsertID();
            $existing = null;
            $aksi     = 'CREATE';
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan data program studi. Silakan coba lagi.'];
        }

        log_audit($aksi, 'ref_program_studi', (string) $savedId, $existing, $data);

        return ['success' => true, 'message' => 'Program studi berhasil disimpan.', 'id' => $savedId];
    }

    /**
     * @return array{success: bool, message: string, id?: int}
     */
    private function saveAngkatan(array $payload, ?int $id): array
    {
        $tahunMasuk     = (int) ($payload['tahun_masuk'] ?? 0);
        $perkiraanLulus = (int) ($payload['perkiraan_lulus'] ?? 0);

        if ($tahunMasuk < 1990 || $tahunMasuk > 2100) {
            return ['success' => false, 'message' => 'Tahun masuk angkatan tidak sesuai ketentuan (minimal 1990).'];
        }
        if ($perkiraanLulus < $tahunMasuk || $perkiraanLulus > ($tahunMasuk + 8)) {
            return ['success' => false, 'message' => 'Perkiraan tahun lulus tidak wajar terhadap tahun masuk.'];
        }

        $dupQuery = $this->db->table('ref_angkatan')->where('tahun_masuk', $tahunMasuk);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Angkatan ' . ketarunaan_format_angkatan($tahunMasuk) . ' sudah terdaftar.'];
        }

        $meta  = ketarunaan_angkatan_meta($tahunMasuk);
        $model = new AngkatanModel();
        $data  = [
            'tahun_masuk'     => $tahunMasuk,
            'angkatan_ke'     => $meta['angkatan_ke'],
            'kode_romawi'     => $meta['kode_romawi'] !== '' ? $meta['kode_romawi'] : null,
            'perkiraan_lulus' => $perkiraanLulus,
            'is_aktif'        => isset($payload['is_aktif']) ? ((int) $payload['is_aktif'] === 1 ? 1 : 0) : 1,
        ];

        $this->db->transStart();
        if ($id !== null && $id > 0) {
            $existing = $model->find($id);
            if (!$existing) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Data angkatan tidak ditemukan.'];
            }
            $model->update($id, $data);
            $savedId = $id;
            $aksi    = 'UPDATE';
        } else {
            $model->insert($data);
            $savedId  = (int) $model->getInsertID();
            $existing = null;
            $aksi     = 'CREATE';
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan data angkatan. Silakan coba lagi.'];
        }

        log_audit($aksi, 'ref_angkatan', (string) $savedId, $existing, $data);

        return ['success' => true, 'message' => 'Angkatan ' . $meta['label'] . ' berhasil disimpan.', 'id' => $savedId];
    }

    /**
     * @return array{success: bool, message: string, id?: int}
     */
    private function saveTahunAkademik(array $payload, ?int $id): array
    {
        $kode     = trim((string) ($payload['kode_tahun'] ?? ''));
        $tahun    = (int) ($payload['tahun'] ?? 0);
        $semester = strtolower(trim((string) ($payload['semester'] ?? 'ganjil')));
        $mulai    = trim((string) ($payload['tanggal_mulai'] ?? ''));
        $selesai  = trim((string) ($payload['tanggal_selesai'] ?? ''));

        if ($kode === '' || $tahun < 2000 || $tahun > 2100 || !in_array($semester, ['ganjil', 'genap'], true) || $mulai === '' || $selesai === '') {
            return ['success' => false, 'message' => 'Lengkapi kode tahun, tahun (2000–2100), semester, tanggal mulai, dan tanggal selesai.'];
        }
        $dtMulai   = \DateTimeImmutable::createFromFormat('Y-m-d', $mulai);
        $dtSelesai = \DateTimeImmutable::createFromFormat('Y-m-d', $selesai);
        if (
            !$dtMulai || $dtMulai->format('Y-m-d') !== $mulai
            || !$dtSelesai || $dtSelesai->format('Y-m-d') !== $selesai
        ) {
            return ['success' => false, 'message' => 'Format tanggal mulai dan tanggal selesai tidak sah.'];
        }
        if ($selesai <= $mulai) {
            return ['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai.'];
        }

        $dupQuery = $this->db->table('ref_tahun_akademik')->where('kode_tahun', $kode);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Kode tahun akademik "' . $kode . '" sudah terdaftar.'];
        }

        $model = new TahunAkademikModel();
        $data  = [
            'kode_tahun'      => $kode,
            'tahun'           => $tahun,
            'semester'        => $semester,
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'is_aktif'        => isset($payload['is_aktif']) ? ((int) $payload['is_aktif'] === 1 ? 1 : 0) : 1,
        ];

        $this->db->transStart();
        if ($id !== null && $id > 0) {
            $existing = $model->find($id);
            if (!$existing) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Data tahun akademik tidak ditemukan.'];
            }
            $model->update($id, $data);
            $savedId = $id;
            $aksi    = 'UPDATE';
        } else {
            $model->insert($data);
            $savedId  = (int) $model->getInsertID();
            $existing = null;
            $aksi     = 'CREATE';
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan tahun akademik. Silakan coba lagi.'];
        }

        log_audit($aksi, 'ref_tahun_akademik', (string) $savedId, $existing, $data);

        return ['success' => true, 'message' => 'Tahun akademik berhasil disimpan.', 'id' => $savedId];
    }

    /**
     * @return array{success: bool, message: string, id?: int}
     */
    private function saveKelas(array $payload, ?int $id): array
    {
        $prodiId    = (int) ($payload['program_studi_id'] ?? 0);
        $angkatanId = (int) ($payload['angkatan_id'] ?? 0);
        $tingkat    = (int) ($payload['tingkat'] ?? 1);
        $namaKelas  = trim((string) ($payload['nama_kelas'] ?? ''));

        if ($prodiId <= 0 || $angkatanId <= 0 || $tingkat < 1 || $tingkat > 4 || $namaKelas === '') {
            return ['success' => false, 'message' => 'Program studi, angkatan, tingkat (1-4), dan nama kelas wajib diisi.'];
        }

        if ($this->db->table('ref_program_studi')->where('id', $prodiId)->countAllResults() === 0) {
            return ['success' => false, 'message' => 'Program studi yang dipilih tidak ditemukan.'];
        }
        if ($this->db->table('ref_angkatan')->where('id', $angkatanId)->countAllResults() === 0) {
            return ['success' => false, 'message' => 'Angkatan yang dipilih tidak ditemukan.'];
        }

        $dupQuery = $this->db->table('ref_kelas')
            ->where('program_studi_id', $prodiId)
            ->where('angkatan_id', $angkatanId)
            ->where('nama_kelas', $namaKelas);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Nama kelas "' . $namaKelas . '" sudah terdaftar pada prodi dan angkatan tersebut.'];
        }

        $model = new KelasModel();
        $data  = [
            'program_studi_id' => $prodiId,
            'angkatan_id'      => $angkatanId,
            'tingkat'          => $tingkat,
            'nama_kelas'       => $namaKelas,
            'is_aktif'         => isset($payload['is_aktif']) ? ((int) $payload['is_aktif'] === 1 ? 1 : 0) : 1,
        ];

        $this->db->transStart();
        if ($id !== null && $id > 0) {
            $existing = $model->find($id);
            if (!$existing) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Data kelas tidak ditemukan.'];
            }
            $model->update($id, $data);
            $savedId = $id;
            $aksi    = 'UPDATE';
        } else {
            $model->insert($data);
            $savedId  = (int) $model->getInsertID();
            $existing = null;
            $aksi     = 'CREATE';
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan data kelas. Silakan coba lagi.'];
        }

        log_audit($aksi, 'ref_kelas', (string) $savedId, $existing, $data);

        return ['success' => true, 'message' => 'Data kelas berhasil disimpan.', 'id' => $savedId];
    }

    /**
     * @return array{success: bool, message: string, id?: int}
     */
    private function saveWilayah3tp(array $payload, ?int $id): array
    {
        $kode      = trim((string) ($payload['kode_wilayah'] ?? ''));
        $provinsi  = trim((string) ($payload['provinsi'] ?? ''));
        $kabKota   = trim((string) ($payload['kabupaten_kota'] ?? ''));
        $kategori  = trim((string) ($payload['kategori_3tp'] ?? 'Daerah Tertinggal'));
        $dasar     = trim((string) ($payload['dasar_acuan'] ?? ''));
        $versi     = trim((string) ($payload['versi_acuan'] ?? ''));
        $tahun     = (int) ($payload['tahun_berlaku'] ?? 2024);

        $kategoriAllowed = ['Daerah Tertinggal', 'Daerah Terdepan', 'Daerah Terluar', 'Perbatasan'];
        if (!in_array($kategori, $kategoriAllowed, true)) {
            return ['success' => false, 'message' => 'Kategori wilayah 3TP tidak sah.'];
        }
        if ($tahun < 2000 || $tahun > 2100) {
            return ['success' => false, 'message' => 'Tahun berlaku harus berada pada rentang 2000 sampai 2100.'];
        }
        if ($kode === '' || $provinsi === '' || $kabKota === '' || $dasar === '' || $versi === '') {
            return ['success' => false, 'message' => 'Kode wilayah, provinsi, kabupaten/kota, dasar acuan, dan versi acuan wajib diisi.'];
        }

        $dupQuery = $this->db->table('ref_wilayah_3tp')->where('kode_wilayah', $kode);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Kode wilayah 3TP "' . $kode . '" sudah terdaftar.'];
        }

        $model = new RefWilayah3tpModel();
        $data  = [
            'kode_wilayah'   => $kode,
            'provinsi'       => $provinsi,
            'kabupaten_kota' => $kabKota,
            'kategori_3tp'   => $kategori,
            'dasar_acuan'    => $dasar,
            'versi_acuan'    => $versi,
            'tahun_berlaku'  => $tahun > 1990 ? $tahun : 2024,
            'is_aktif'       => isset($payload['is_aktif']) ? ((int) $payload['is_aktif'] === 1 ? 1 : 0) : 1,
        ];

        $this->db->transStart();
        if ($id !== null && $id > 0) {
            $existing = $model->find($id);
            if (!$existing) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'Data wilayah 3TP tidak ditemukan.'];
            }
            $model->update($id, $data);
            $savedId = $id;
            $aksi    = 'UPDATE';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $model->insert($data);
            $savedId  = (int) $model->getInsertID();
            $existing = null;
            $aksi     = 'CREATE';
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan data wilayah 3TP. Silakan coba lagi.'];
        }

        log_audit($aksi, 'ref_wilayah_3tp', (string) $savedId, $existing, $data);

        return ['success' => true, 'message' => 'Data wilayah 3TP berhasil disimpan.', 'id' => $savedId];
    }
}
