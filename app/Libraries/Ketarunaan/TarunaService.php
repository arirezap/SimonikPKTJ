<?php

namespace App\Libraries\Ketarunaan;

use CodeIgniter\Database\BaseConnection;

/**
 * Service bisnis untuk Modul 1: Database Taruna (ECC 2.0).
 * Mematuhi prinsip Single Responsibility (SRP), transaksi basis data atomik,
 * dan perlindungan integritas Nomor Taruna unik.
 */
class TarunaService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Mengambil daftar data referensi untuk dropdown filter dan formulir.
     *
     * @return array<string, mixed>
     */
    public function getReferenceData(): array
    {
        ketarunaan_ensure_kalender_terkini($this->db);

        $prodiList = $this->db->table('ref_program_studi')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $angkatanList = $this->db->table('ref_angkatan')
            ->where('is_aktif', 1)
            ->orderBy('tahun_masuk', 'DESC')
            ->get()
            ->getResultArray();

        $tahunAkademikList = $this->db->table('ref_tahun_akademik')
            ->orderBy('tahun', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $kelasList = $this->db->table('ref_kelas k')
            ->select('k.*, p.kode_prodi, a.tahun_masuk, a.kode_romawi')
            ->join('ref_program_studi p', 'p.id = k.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = k.angkatan_id', 'left')
            ->where('k.is_aktif', 1)
            ->orderBy('p.kode_prodi', 'ASC')
            ->orderBy('a.tahun_masuk', 'DESC')
            ->orderBy('k.tingkat', 'ASC')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'prodi_list'          => $prodiList,
            'angkatan_list'       => $angkatanList,
            'tahun_akademik_list' => $tahunAkademikList,
            'kelas_list'          => $kelasList,
            'status_catalog'      => ketarunaan_status_catalog(),
        ];
    }

    /**
     * Mengambil daftar direktori taruna terpaginasi beserta rekap 6 status pendidikan.
     *
     * @param array<string, mixed> $filters
     * @param int                  $page
     * @param int                  $perPage
     * @param string               $sortBy
     * @param string               $sortOrder
     * @return array<string, mixed>
     */
    public function getDirectoryData(
        array $filters = [],
        int $page = 1,
        int $perPage = 15,
        string $sortBy = 'nomor_taruna',
        string $sortOrder = 'asc'
    ): array {
        $prodiId    = !empty($filters['prodi_id']) ? (int) $filters['prodi_id'] : null;
        $angkatanId = !empty($filters['angkatan_id']) ? (int) $filters['angkatan_id'] : null;
        $status     = !empty($filters['status']) ? trim((string) $filters['status']) : '';
        $keyword    = !empty($filters['q']) ? trim((string) $filters['q']) : '';

        // 1. Hitung jumlah per status (mengikuti filter prodi & angkatan)
        $statusBuilder = $this->db->table('master_taruna m')
            ->select('m.status_terkini, COUNT(*) AS total')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left');

        if ($prodiId !== null && $prodiId > 0) {
            $statusBuilder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $statusBuilder->where('pt.angkatan_id', $angkatanId);
        }

        $statusRows = $statusBuilder->groupBy('m.status_terkini')->get()->getResultArray();
        $statusCounts = [
            'aktif'             => 0,
            'skorsing'          => 0,
            'cuti'              => 0,
            'mengundurkan_diri' => 0,
            'drop_out'          => 0,
            'lulus'             => 0,
        ];
        foreach ($statusRows as $r) {
            $code = (string) $r['status_terkini'];
            if (array_key_exists($code, $statusCounts)) {
                $statusCounts[$code] = (int) $r['total'];
            }
        }

        // 2. Kueri daftar taruna dengan kolom selektif
        $listBuilder = $this->db->table('master_taruna m')
            ->select('
                m.nomor_taruna,
                m.nama_lengkap,
                m.nama_panggilan,
                m.jenis_kelamin,
                m.tempat_lahir,
                m.tanggal_lahir,
                m.foto,
                m.status_terkini,
                pt.program_studi_id,
                pt.angkatan_id,
                pt.tingkat,
                pt.semester_aktif,
                pt.jalur_penerimaan,
                pt.asal_formasi,
                p.kode_prodi,
                p.nama_prodi,
                p.jenjang,
                a.tahun_masuk,
                a.kode_romawi,
                k.nama_kelas AS nama_kelas
            ')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->join('ref_kelas k', 'k.id = pt.kelas_id', 'left');

        if ($prodiId !== null && $prodiId > 0) {
            $listBuilder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $listBuilder->where('pt.angkatan_id', $angkatanId);
        }
        if ($status !== '' && array_key_exists($status, $statusCounts)) {
            $listBuilder->where('m.status_terkini', $status);
        }
        if ($keyword !== '') {
            $listBuilder->groupStart()
                ->like('m.nomor_taruna', $keyword)
                ->orLike('m.nama_lengkap', $keyword)
                ->groupEnd();
        }

        $totalFiltered = (int) $listBuilder->countAllResults(false);

        $totalPages = max(1, (int) ceil($totalFiltered / $perPage));
        $page       = max(1, min($page, $totalPages));
        $offset     = ($page - 1) * $perPage;

        $dir = strtolower($sortOrder) === 'desc' ? 'DESC' : 'ASC';
        match ($sortBy) {
            'nama_lengkap'     => $listBuilder->orderBy('m.nama_lengkap', $dir),
            'prodi'            => $listBuilder->orderBy('p.kode_prodi', $dir),
            'angkatan'         => $listBuilder->orderBy('a.tahun_masuk', $dir),
            'tingkat'          => $listBuilder->orderBy('pt.tingkat', $dir)->orderBy('pt.semester_aktif', $dir),
            'jalur_penerimaan' => $listBuilder->orderBy('pt.jalur_penerimaan', $dir)->orderBy('pt.asal_formasi', $dir),
            'status_terkini'   => $listBuilder->orderBy('m.status_terkini', $dir),
            default            => $listBuilder->orderBy('m.nomor_taruna', $dir),
        };
        if ($sortBy !== 'nomor_taruna') {
            $listBuilder->orderBy('m.nomor_taruna', 'ASC');
        }

        $rows = $listBuilder
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return [
            'status_counts'  => $statusCounts,
            'total_rekaman'  => array_sum($statusCounts),
            'rows'           => $rows,
            'total_filtered' => $totalFiltered,
            'page'           => $page,
            'per_page'       => $perPage,
            'total_pages'    => $totalPages,
        ];
    }

    /**
     * Mengambil data lengkap profil taruna untuk 5 tab profil.
     *
     * @param string $nomorTaruna
     * @return array<string, mixed>|null
     */
    public function getProfilLengkap(string $nomorTaruna): ?array
    {
        $taruna = $this->db->table('master_taruna m')
            ->select('
                m.*,
                pt.id AS pendidikan_id,
                pt.program_studi_id,
                pt.angkatan_id,
                pt.kelas_id,
                pt.tingkat,
                pt.semester_aktif,
                pt.jalur_penerimaan,
                pt.asal_formasi,
                pt.tahun_akademik_id,
                p.kode_prodi,
                p.nama_prodi,
                p.jenjang,
                a.tahun_masuk,
                a.kode_romawi,
                a.perkiraan_lulus,
                k.nama_kelas AS nama_kelas,
                ta.kode_tahun AS nama_tahun_akademik
            ')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->join('ref_kelas k', 'k.id = pt.kelas_id', 'left')
            ->join('ref_tahun_akademik ta', 'ta.id = pt.tahun_akademik_id', 'left')
            ->where('m.nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$taruna) {
            return null;
        }

        $riwayatStatus = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->orderBy('mulai_berlaku', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $riwayatPendidikan = $this->db->table('riwayat_pendidikan_taruna rp')
            ->select('
                rp.*,
                p.kode_prodi,
                p.nama_prodi,
                k.nama_kelas AS nama_kelas,
                ta.kode_tahun AS kode_tahun
            ')
            ->join('ref_program_studi p', 'p.id = rp.program_studi_id', 'left')
            ->join('ref_kelas k', 'k.id = rp.kelas_id', 'left')
            ->join('ref_tahun_akademik ta', 'ta.id = rp.tahun_akademik_id', 'left')
            ->where('rp.nomor_taruna', $nomorTaruna)
            ->orderBy('rp.mulai_berlaku', 'DESC')
            ->orderBy('rp.id', 'DESC')
            ->get()
            ->getResultArray();

        // Cari status aktif yang sedang berlaku
        $statusAktifRow = null;
        $cutiRiwayatRow = null;
        foreach ($riwayatStatus as $rs) {
            if ($statusAktifRow === null && $rs['status_validasi'] === 'tervalidasi') {
                $statusAktifRow = $rs;
            }
            if ($cutiRiwayatRow === null && in_array($rs['kode_status'], ['cuti', 'skorsing'], true)) {
                $cutiRiwayatRow = $rs;
            }
        }

        return [
            'taruna'             => $taruna,
            'riwayat_status'     => $riwayatStatus,
            'riwayat_pendidikan' => $riwayatPendidikan,
            'status_aktif_row'   => $statusAktifRow,
            'cuti_riwayat_row'   => $cutiRiwayatRow,
        ];
    }

    /**
     * Memperbarui data Tab 1: Identitas Utama.
     *
     * @param string      $nomorTaruna
     * @param array       $payload
     * @param string|null $newFoto
     * @param bool        $allowFullNameEdit
     * @param bool        $stageFotoPending Bila true (taruna unggah mandiri), foto disimpan sementara menunggu validasi.
     * @return bool
     */
    public function updateIdentitas(
        string $nomorTaruna,
        array $payload,
        ?string $newFoto = null,
        bool $allowFullNameEdit = true,
        bool $stageFotoPending = false
    ): bool {
        $existing = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$existing) {
            return false;
        }

        $namaLengkap = $allowFullNameEdit
            ? trim((string) ($payload['nama_lengkap'] ?? $existing['nama_lengkap']))
            : (string) $existing['nama_lengkap'];
        if ($namaLengkap === '') {
            $namaLengkap = (string) $existing['nama_lengkap'];
        }

        $now        = date('Y-m-d H:i:s');
        $updateData = [
            'nama_lengkap'   => $namaLengkap,
            'nama_panggilan' => trim((string) ($payload['nama_panggilan'] ?? '')),
            'jenis_kelamin'  => in_array(($payload['jenis_kelamin'] ?? 'L'), ['L', 'P'], true) ? $payload['jenis_kelamin'] : 'L',
            'agama'          => $this->normalizeAgama((string) ($payload['agama'] ?? 'Islam')),
            'tempat_lahir'   => trim((string) ($payload['tempat_lahir'] ?? '')) ?: null,
            'tanggal_lahir'  => !empty($payload['tanggal_lahir']) ? $payload['tanggal_lahir'] : null,
            'updated_at'     => $now,
        ];

        if (array_key_exists('nik', $payload) && !str_contains((string) $payload['nik'], '*')) {
            $cleanNik          = $this->cleanNik((string) $payload['nik']);
            $updateData['nik'] = $cleanNik;
        }

        if ($newFoto !== null) {
            if ($stageFotoPending) {
                $updateData['foto_pending']         = $newFoto;
                $updateData['foto_pending_status']  = 'diajukan';
                $updateData['foto_pending_catatan'] = null;
                $updateData['foto_pending_at']      = $now;
            } else {
                $updateData['foto']                 = $newFoto;
                $updateData['foto_pending']         = null;
                $updateData['foto_pending_status']  = null;
                $updateData['foto_pending_catatan'] = null;
                $updateData['foto_pending_at']      = null;
            }
        }

        $this->db->transStart();
        $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->update($updateData);
        if ($allowFullNameEdit) {
            $this->db->table('users')
                ->where('username', $nomorTaruna)
                ->where('role', 'taruna')
                ->update(['nama_lengkap' => $namaLengkap]);
        }
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            log_audit('UPDATE', 'master_taruna', $nomorTaruna, $existing, $updateData);
            return true;
        }

        return false;
    }

    /**
     * Memvalidasi atau mengembalikan pengajuan foto profil baru dari akun mandiri taruna.
     *
     * @return array{success: bool, message: string}
     */
    public function validasiFotoPending(
        string $nomorTaruna,
        string $keputusan,
        string $catatan = '',
        string $validatorLabel = 'Validator'
    ): array {
        $keputusanClean = strtolower(trim($keputusan));
        if (!in_array($keputusanClean, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['success' => false, 'message' => 'Pilih keputusan validasi yang sah.'];
        }

        $taruna = $this->db->table('master_taruna')
            ->select('nomor_taruna, foto, foto_pending, foto_pending_status')
            ->where('nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$taruna) {
            return ['success' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        $pendingFile = basename((string) ($taruna['foto_pending'] ?? ''));
        if ($pendingFile === '' || ($taruna['foto_pending_status'] ?? '') !== 'diajukan') {
            return ['success' => false, 'message' => 'Tidak ada pengajuan foto yang menunggu validasi.'];
        }

        $catatanClean = trim($catatan);
        if ($keputusanClean === 'perlu_perbaikan' && $catatanClean === '') {
            return ['success' => false, 'message' => 'Catatan perbaikan wajib diisi saat menolak pengajuan foto.'];
        }

        $uploadDir   = FCPATH . 'assets/uploads/taruna/';
        $pendingPath = $uploadDir . $pendingFile;
        if ($keputusanClean === 'tervalidasi' && !is_file($pendingPath)) {
            return ['success' => false, 'message' => 'Berkas foto pengajuan tidak tersedia di server.'];
        }

        $oldOfficial = basename((string) ($taruna['foto'] ?? ''));
        $now         = date('Y-m-d H:i:s');

        $this->db->transStart();
        if ($keputusanClean === 'tervalidasi') {
            $this->db->table('master_taruna')
                ->where('nomor_taruna', $nomorTaruna)
                ->update([
                    'foto'                 => $pendingFile,
                    'foto_pending'         => null,
                    'foto_pending_status'  => null,
                    'foto_pending_catatan' => null,
                    'foto_pending_at'      => null,
                    'updated_at'           => $now,
                ]);
        } else {
            $this->db->table('master_taruna')
                ->where('nomor_taruna', $nomorTaruna)
                ->update([
                    'foto_pending'         => null,
                    'foto_pending_status'  => 'perlu_perbaikan',
                    'foto_pending_catatan' => $catatanClean,
                    'foto_pending_at'      => $now,
                    'updated_at'           => $now,
                ]);
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal memproses validasi foto. Silakan coba lagi.'];
        }

        if ($keputusanClean === 'tervalidasi') {
            if ($oldOfficial !== '' && $oldOfficial !== 'default.png' && $oldOfficial !== $pendingFile && is_file($uploadDir . $oldOfficial)) {
                @unlink($uploadDir . $oldOfficial);
            }
            log_audit('APPROVE', 'master_taruna', $nomorTaruna, ['foto' => $oldOfficial], ['foto' => $pendingFile, 'validator' => $validatorLabel]);
            return ['success' => true, 'message' => 'Foto baru taruna berhasil disetujui dan dipasang.'];
        }

        if (is_file($pendingPath)) {
            @unlink($pendingPath);
        }
        log_audit('REJECT', 'master_taruna', $nomorTaruna, ['foto_pending' => $pendingFile], ['catatan' => $catatanClean, 'validator' => $validatorLabel]);

        return ['success' => true, 'message' => 'Pengajuan foto taruna dikembalikan untuk perbaikan.'];
    }

    private function isEmailTakenByOtherUser(string $email, string $nomorTaruna): bool
    {
        $cleanEmail = strtolower(trim($email));
        if ($cleanEmail === '') {
            return false;
        }

        return $this->db->table('users')
            ->where('LOWER(email)', $cleanEmail)
            ->where('username !=', $nomorTaruna)
            ->countAllResults() > 0;
    }

    /**
     * Memperbarui data Tab 2: Data Pendidikan.
     *
     * @param string $nomorTaruna
     * @param array  $payload
     * @return bool
     */
    public function updatePendidikan(string $nomorTaruna, array $payload): bool
    {
        $existing = $this->db->table('pendidikan_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        $now = date('Y-m-d H:i:s');

        $asalFormasi = trim((string) ($payload['asal_formasi'] ?? ''));
        $prodiId     = (int) ($payload['program_studi_id'] ?? ($existing['program_studi_id'] ?? 1));
        $tingkat     = max(1, min(4, (int) ($payload['tingkat'] ?? 1)));
        $kelasId     = !empty($payload['kelas_id']) ? $this->resolveValidKelasId((int) $payload['kelas_id'], $prodiId, $tingkat) : null;

        $data = [
            'program_studi_id' => $prodiId,
            'angkatan_id'      => (int) ($payload['angkatan_id'] ?? ($existing['angkatan_id'] ?? 1)),
            'kelas_id'         => $kelasId,
            'tingkat'          => $tingkat,
            'semester_aktif'   => max(1, min(8, (int) ($payload['semester_aktif'] ?? 1))),
            'jalur_penerimaan' => trim((string) ($payload['jalur_penerimaan'] ?? '')) ?: $this->resolveJalurPenerimaan($nomorTaruna),
            'asal_formasi'     => $this->resolveAsalFormasi($nomorTaruna, $asalFormasi),
            'updated_at'       => $now,
        ];

        $this->db->transStart();
        if ($existing) {
            $this->db->table('pendidikan_taruna')->where('nomor_taruna', $nomorTaruna)->update($data);
        } else {
            $data['nomor_taruna'] = $nomorTaruna;
            $data['created_at']   = $now;
            $this->db->table('pendidikan_taruna')->insert($data);
        }
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            log_audit('UPDATE', 'pendidikan_taruna', $nomorTaruna, $existing, $data);
            return true;
        }

        return false;
    }

    /**
     * Memperbarui data Tab 3: Kontak dan Keluarga (oleh pengelola berwenang).
     *
     * @param string $nomorTaruna
     * @param array  $payload
     * @return array{success: bool, message: string}
     */
    public function updateKontak(string $nomorTaruna, array $payload): array
    {
        $existing = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$existing) {
            return ['success' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        $resolvedEmail = $this->resolveEmailTaruna($nomorTaruna, (string) ($payload['email'] ?? ''));
        if ($resolvedEmail !== null && $this->isEmailTakenByOtherUser($resolvedEmail, $nomorTaruna)) {
            return ['success' => false, 'message' => 'Alamat email tersebut sudah digunakan oleh akun lain.'];
        }

        $now = date('Y-m-d H:i:s');

        $updateData = [
            'nomor_hp'        => $this->normalizePhone((string) ($payload['nomor_hp'] ?? '')),
            'email'           => $resolvedEmail,
            'alamat_domisili' => trim((string) ($payload['alamat_domisili'] ?? '')) ?: null,
            'alamat_asal'     => trim((string) ($payload['alamat_asal'] ?? '')) ?: null,
            'nama_wali'       => trim((string) ($payload['nama_wali'] ?? '')) ?: null,
            'hubungan_wali'   => trim((string) ($payload['hubungan_wali'] ?? '')) ?: null,
            'nomor_hp_wali'   => $this->normalizePhone((string) ($payload['nomor_hp_wali'] ?? '')) ?? (trim((string) ($payload['nomor_hp_wali'] ?? '')) ?: null),
            'updated_at'      => $now,
        ];

        $this->db->transStart();
        $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->update($updateData);

        if ($resolvedEmail !== null) {
            $this->db->table('users')
                ->where('username', $nomorTaruna)
                ->where('role', 'taruna')
                ->update(['email' => $resolvedEmail]);
        }
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            log_audit('UPDATE', 'master_taruna', $nomorTaruna, null, ['section' => 'kontak_keluarga']);
            return ['success' => true, 'message' => 'Data kontak dan keluarga berhasil diperbarui.'];
        }

        return ['success' => false, 'message' => 'Gagal menyimpan data kontak.'];
    }

    /**
     * Menambah data taruna baru secara manual dalam transaksi atomik.
     *
     * @param array $payload
     * @return array{success: bool, message: string, nomor_taruna?: string}
     */
    public function createTaruna(array $payload): array
    {
        $nomorTaruna = trim((string) ($payload['nomor_taruna'] ?? ''));
        if ($nomorTaruna === '') {
            return ['success' => false, 'message' => 'Nomor Taruna wajib diisi.'];
        }

        $exists = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->countAllResults();
        if ($exists > 0) {
            return ['success' => false, 'message' => 'Nomor Taruna sudah terdaftar di sistem.'];
        }

        $prodiId    = (int) ($payload['program_studi_id'] ?? 0);
        $angkatanId = (int) ($payload['angkatan_id'] ?? 0);
        if ($prodiId <= 0 || $this->db->table('ref_program_studi')->where('id', $prodiId)->countAllResults() === 0) {
            return ['success' => false, 'message' => 'Program Studi yang dipilih tidak valid.'];
        }
        if ($angkatanId <= 0 || $this->db->table('ref_angkatan')->where('id', $angkatanId)->countAllResults() === 0) {
            return ['success' => false, 'message' => 'Angkatan yang dipilih tidak valid.'];
        }

        $now = date('Y-m-d H:i:s');
        $kodeStatus = trim((string) ($payload['status_terkini'] ?? 'aktif'));
        if (!array_key_exists($kodeStatus, ketarunaan_status_catalog())) {
            $kodeStatus = 'aktif';
        }

        $namaLengkap = trim((string) ($payload['nama_lengkap'] ?? ''));
        $emailTaruna = $this->resolveEmailTaruna($nomorTaruna, (string) ($payload['email'] ?? ''));

        $this->db->transStart();

        $this->db->table('master_taruna')->insert([
            'nomor_taruna'    => $nomorTaruna,
            'nama_lengkap'    => $namaLengkap,
            'nik'             => !empty($payload['nik']) ? preg_replace('/[^0-9]/', '', (string) $payload['nik']) : null,
            'nama_panggilan'  => trim((string) ($payload['nama_panggilan'] ?? '')),
            'jenis_kelamin'   => in_array(($payload['jenis_kelamin'] ?? 'L'), ['L', 'P'], true) ? $payload['jenis_kelamin'] : 'L',
            'tempat_lahir'    => ($tmpl = trim((string) ($payload['tempat_lahir'] ?? ''))) !== '' ? $tmpl : null,
            'tanggal_lahir'   => !empty($payload['tanggal_lahir']) ? $payload['tanggal_lahir'] : null,
            'agama'           => ($agm = trim((string) ($payload['agama'] ?? ''))) !== '' ? $agm : 'Islam',
            'nomor_hp'        => trim((string) ($payload['nomor_hp'] ?? '')),
            'email'           => $emailTaruna,
            'alamat_domisili' => trim((string) ($payload['alamat_domisili'] ?? '')) ?: null,
            'status_terkini'  => $kodeStatus,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $prodiId    = (int) ($payload['program_studi_id'] ?? 1);
        $angkatanId = (int) ($payload['angkatan_id'] ?? 1);
        $tingkat    = max(1, min(4, (int) ($payload['tingkat'] ?? 1)));
        $semester   = !empty($payload['semester_aktif'])
            ? max(1, min(8, (int) $payload['semester_aktif']))
            : max(1, min(8, ($tingkat * 2) - 1));

        $kelasId = !empty($payload['kelas_id']) ? $this->resolveValidKelasId((int) $payload['kelas_id'], $prodiId, $tingkat) : null;

        $activeTaRow = $this->db->table('ref_tahun_akademik')
            ->select('id')
            ->where('is_aktif', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        $activeTaId = !empty($activeTaRow['id']) ? (int) $activeTaRow['id'] : null;

        $asalFormasiRaw = trim((string) ($payload['asal_formasi'] ?? ''));
        $jalurRaw       = trim((string) ($payload['jalur_penerimaan'] ?? ''));

        $this->db->table('pendidikan_taruna')->insert([
            'nomor_taruna'      => $nomorTaruna,
            'program_studi_id'  => $prodiId,
            'angkatan_id'       => $angkatanId,
            'kelas_id'          => $kelasId,
            'tingkat'           => $tingkat,
            'semester_aktif'    => $semester,
            'jalur_penerimaan'  => $this->resolveJalurPenerimaan($nomorTaruna, $jalurRaw),
            'asal_formasi'      => $this->resolveAsalFormasi($nomorTaruna, $asalFormasiRaw),
            'tahun_akademik_id' => $activeTaId,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        $this->db->table('riwayat_status_taruna')->insert([
            'nomor_taruna'      => $nomorTaruna,
            'kode_status'       => $kodeStatus,
            'mulai_berlaku'     => date('Y-m-d'),
            'alasan_perubahan'  => 'Pendaftaran data awal taruna',
            'nomor_keputusan'   => 'SK/REG/' . date('Y'),
            'tanggal_keputusan' => date('Y-m-d'),
            'status_validasi'   => 'tervalidasi',
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        $this->db->table('riwayat_pendidikan_taruna')->insert([
            'nomor_taruna'      => $nomorTaruna,
            'program_studi_id'  => $prodiId,
            'kelas_id'          => $kelasId,
            'tingkat'           => $tingkat,
            'semester'          => $semester,
            'tahun_akademik_id' => $activeTaId,
            'mulai_berlaku'     => date('Y-m-d'),
            'created_at'        => $now,
        ]);

        $this->ensureTarunaPortalAccount($nomorTaruna, $namaLengkap, $emailTaruna, $now);

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan data taruna. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'master_taruna', $nomorTaruna, null, ['nama_lengkap' => $payload['nama_lengkap'] ?? '']);

        return [
            'success'      => true,
            'message'      => 'Data taruna berhasil ditambahkan.',
            'nomor_taruna' => $nomorTaruna,
        ];
    }

    /**
     * Menyimpan atau memperbarui draf/pengajuan perubahan status pendidikan taruna.
     *
     * @param string      $nomorTaruna
     * @param array       $payload
     * @param string|null $buktiFilename
     * @return array{success: bool, message: string, old_bukti_dokumen?: ?string}
     */
    public function ajukanPerubahanStatus(string $nomorTaruna, array $payload, ?string $buktiFilename = null): array
    {
        $taruna = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$taruna) {
            return ['success' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        $draftId = (int) ($payload['draft_id'] ?? 0);
        $draftRow = null;
        if ($draftId > 0) {
            $draftRow = $this->getRiwayatStatusById($draftId);
            if (!$draftRow || (string) ($draftRow['nomor_taruna'] ?? '') !== $nomorTaruna) {
                return ['success' => false, 'message' => 'Catatan draf status tidak ditemukan.'];
            }
            if (($draftRow['status_validasi'] ?? '') !== 'draf') {
                return ['success' => false, 'message' => 'Hanya pengajuan berstatus Draf yang dapat diubah.'];
            }
        }

        $kodeStatusBaru = trim((string) ($payload['kode_status'] ?? ''));
        $catalog = ketarunaan_status_catalog();
        if (!array_key_exists($kodeStatusBaru, $catalog)) {
            return ['success' => false, 'message' => 'Pilih status baru yang sah.'];
        }
        if (strtolower((string) ($taruna['status_terkini'] ?? '')) === $kodeStatusBaru) {
            return ['success' => false, 'message' => 'Status baru tidak boleh sama dengan status pendidikan saat ini.'];
        }

        $pendingQuery = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('status_validasi', 'diajukan');
        if ($draftId > 0) {
            $pendingQuery->where('id !=', $draftId);
        }
        if ($pendingQuery->countAllResults() > 0) {
            return ['success' => false, 'message' => 'Taruna ini masih memiliki pengajuan perubahan status yang menunggu validasi.'];
        }

        $mulaiBerlaku = trim((string) ($payload['mulai_berlaku'] ?? ''));
        if ($mulaiBerlaku === '') {
            return ['success' => false, 'message' => 'Tanggal mulai berlaku wajib diisi.'];
        }

        $rencanaKembali = null;
        if (in_array($kodeStatusBaru, ['cuti', 'skorsing'], true)) {
            $rencanaKembali = !empty($payload['rencana_kembali']) ? trim((string) $payload['rencana_kembali']) : null;
            if (empty($rencanaKembali)) {
                return ['success' => false, 'message' => 'Tanggal rencana kembali wajib diisi untuk status Cuti atau Skorsing.'];
            }
            if ($rencanaKembali < $mulaiBerlaku) {
                return ['success' => false, 'message' => 'Tanggal rencana kembali tidak boleh sebelum tanggal mulai berlaku.'];
            }
        }

        $aksiSubmit     = ($payload['aksi_submit'] ?? 'ajukan') === 'draf' ? 'draf' : 'diajukan';
        $nomorKeputusan = trim((string) ($payload['nomor_keputusan'] ?? ''));
        $tglKeputusan   = !empty($payload['tanggal_keputusan']) ? trim((string) $payload['tanggal_keputusan']) : null;
        $existingBukti  = ($draftRow && !empty($draftRow['bukti_dokumen'])) ? (string) $draftRow['bukti_dokumen'] : null;
        $finalBukti     = $buktiFilename ?? $existingBukti;

        if ($aksiSubmit === 'diajukan') {
            if ($nomorKeputusan === '') {
                return ['success' => false, 'message' => 'Nomor keputusan wajib diisi sebelum mengajukan validasi.'];
            }
            if (empty($tglKeputusan)) {
                return ['success' => false, 'message' => 'Tanggal keputusan wajib diisi sebelum mengajukan validasi.'];
            }
            if (empty($finalBukti)) {
                return ['success' => false, 'message' => 'Masukkan tautan dokumen keputusan sebelum mengajukan validasi.'];
            }
        }

        $now = date('Y-m-d H:i:s');
        $recordData = [
            'kode_status'       => $kodeStatusBaru,
            'mulai_berlaku'     => $mulaiBerlaku,
            'akhir_berlaku'     => null,
            'rencana_kembali'   => $rencanaKembali,
            'alasan_perubahan'  => trim((string) ($payload['alasan_perubahan'] ?? '')),
            'nomor_keputusan'   => $nomorKeputusan,
            'tanggal_keputusan' => $tglKeputusan,
            'bukti_dokumen'     => $finalBukti,
            'status_validasi'   => $aksiSubmit,
            'updated_at'        => $now,
        ];

        $this->db->transStart();
        if ($draftId > 0) {
            $this->db->table('riwayat_status_taruna')
                ->where('id', $draftId)
                ->update($recordData);
        } else {
            $recordData['nomor_taruna'] = $nomorTaruna;
            $recordData['created_at']   = $now;
            $this->db->table('riwayat_status_taruna')->insert($recordData);
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan perubahan status. Silakan coba lagi.'];
        }

        log_audit($draftId > 0 ? 'UPDATE' : 'CREATE', 'riwayat_status_taruna', $draftId > 0 ? (string) $draftId : $nomorTaruna, $draftRow, [
            'kode_status'     => $kodeStatusBaru,
            'mulai_berlaku'   => $mulaiBerlaku,
            'status_validasi' => $aksiSubmit,
        ]);

        $msg = $aksiSubmit === 'draf'
            ? ($draftId > 0 ? 'Draf perubahan status berhasil diperbarui.' : 'Draf perubahan status berhasil disimpan.')
            : 'Pengajuan perubahan status berhasil dikirim ke antrean validasi.';

        return [
            'success'           => true,
            'message'           => $msg,
            'old_bukti_dokumen' => ($buktiFilename !== null && $existingBukti !== null) ? $existingBukti : null,
        ];
    }

    /**
     * Memvalidasi atau mengembalikan pengajuan perubahan status pendidikan taruna (UAT-04..06).
     * Pengajuan berstatus 'lulus' didelegasikan penuh ke AlumniService agar pembentukan profil alumni terkendali.
     *
     * @return array{success: bool, message: string}
     */
    public function validasiPerubahanStatus(
        int $riwayatId,
        string $keputusan,
        string $catatan = '',
        string $validatorLabel = 'Validator'
    ): array {
        $keputusanClean = strtolower(trim($keputusan));
        if (!in_array($keputusanClean, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['success' => false, 'message' => 'Pilih keputusan validasi yang sah.'];
        }

        $riwayat = $this->db->table('riwayat_status_taruna')
            ->where('id', $riwayatId)
            ->get()
            ->getRowArray();

        if (!$riwayat) {
            return ['success' => false, 'message' => 'Pengajuan perubahan status tidak ditemukan.'];
        }

        if (strtolower((string) ($riwayat['kode_status'] ?? '')) === 'lulus') {
            return (new AlumniService($this->db))->validasiKelulusan(
                $riwayatId,
                $keputusanClean,
                $catatan,
                $validatorLabel
            );
        }

        if (($riwayat['status_validasi'] ?? '') === 'tervalidasi') {
            return ['success' => false, 'message' => 'Perubahan status ini sudah tervalidasi sebelumnya.'];
        }

        $catatanClean = trim($catatan);
        if ($keputusanClean === 'perlu_perbaikan' && $catatanClean === '') {
            return ['success' => false, 'message' => 'Catatan perbaikan wajib diisi saat mengembalikan pengajuan.'];
        }

        $nomorTaruna = (string) $riwayat['nomor_taruna'];
        $now         = date('Y-m-d H:i:s');
        $today       = date('Y-m-d');

        $this->db->transStart();

        $this->db->table('riwayat_status_taruna')
            ->where('id', $riwayatId)
            ->update([
                'status_validasi'  => $keputusanClean,
                'divalidasi_oleh'  => $validatorLabel,
                'catatan_validasi' => $catatanClean !== '' ? $catatanClean : null,
                'tanggal_validasi' => $today,
                'updated_at'       => $now,
            ]);

        if ($keputusanClean === 'tervalidasi') {
            $this->db->table('riwayat_status_taruna')
                ->where('nomor_taruna', $nomorTaruna)
                ->where('id !=', $riwayatId)
                ->where('status_validasi', 'tervalidasi')
                ->where('akhir_berlaku IS NULL')
                ->where('mulai_berlaku <=', $riwayat['mulai_berlaku'])
                ->update([
                    'akhir_berlaku' => $riwayat['mulai_berlaku'],
                    'updated_at'    => $now,
                ]);

            $latestValidated = $this->db->table('riwayat_status_taruna')
                ->select('kode_status')
                ->where('nomor_taruna', $nomorTaruna)
                ->where('status_validasi', 'tervalidasi')
                ->orderBy('mulai_berlaku', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if ($latestValidated) {
                $this->db->table('master_taruna')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->update([
                        'status_terkini' => $latestValidated['kode_status'],
                        'updated_at'     => $now,
                    ]);
            }
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal memproses validasi perubahan status. Silakan coba lagi.'];
        }

        log_audit(
            $keputusanClean === 'tervalidasi' ? 'APPROVE' : 'UPDATE',
            'riwayat_status_taruna',
            (string) $riwayatId,
            ['status_validasi' => $riwayat['status_validasi'], 'kode_status' => $riwayat['kode_status']],
            ['status_validasi' => $keputusanClean, 'catatan_validasi' => $catatanClean, 'validator' => $validatorLabel]
        );

        $msg = $keputusanClean === 'tervalidasi'
            ? 'Perubahan status pendidikan berhasil disetujui.'
            : 'Pengajuan perubahan status dikembalikan untuk perbaikan.';

        return ['success' => true, 'message' => $msg];
    }

    /**
     * Mencatat riwayat perubahan tingkat/semester/kelas pendidikan taruna.
     *
     * @param string $nomorTaruna
     * @param array  $payload
     * @return array{success: bool, message: string}
     */
    public function catatRiwayatPendidikan(string $nomorTaruna, array $payload): array
    {
        $mulaiBerlaku = trim((string) ($payload['mulai_berlaku'] ?? date('Y-m-d')));
        $prodiId      = (int) ($payload['program_studi_id'] ?? 1);
        $tingkat      = max(1, min(4, (int) ($payload['tingkat'] ?? 1)));
        $kelasId      = !empty($payload['kelas_id']) ? $this->resolveValidKelasId((int) $payload['kelas_id'], $prodiId, $tingkat) : null;
        $semester     = max(1, min(8, (int) ($payload['semester'] ?? 1)));
        $thnAkademik  = !empty($payload['tahun_akademik_id']) ? (int) $payload['tahun_akademik_id'] : null;

        $activePrev = $this->db->table('riwayat_pendidikan_taruna')
            ->select('mulai_berlaku')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('akhir_berlaku IS NULL')
            ->orderBy('mulai_berlaku', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($activePrev && !empty($activePrev['mulai_berlaku']) && $mulaiBerlaku < (string) $activePrev['mulai_berlaku']) {
            return [
                'success' => false,
                'message' => 'Tanggal mulai berlaku tidak boleh lebih awal dari periode berjalan (' . ketarunaan_format_tanggal((string) $activePrev['mulai_berlaku']) . ').',
            ];
        }

        $this->db->transStart();

        // Tutup periode riwayat pendidikan sebelumnya yang masih aktif (akhir_berlaku IS NULL)
        $this->db->table('riwayat_pendidikan_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('akhir_berlaku IS NULL')
            ->update(['akhir_berlaku' => $mulaiBerlaku]);

        $this->db->table('riwayat_pendidikan_taruna')->insert([
            'nomor_taruna'      => $nomorTaruna,
            'program_studi_id'  => $prodiId,
            'kelas_id'          => $kelasId,
            'tingkat'           => $tingkat,
            'semester'          => $semester,
            'tahun_akademik_id' => $thnAkademik,
            'mulai_berlaku'     => $mulaiBerlaku,
            'akhir_berlaku'     => null,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Sinkronkan pendidikan_taruna terkini
        $this->db->table('pendidikan_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->update([
                'program_studi_id'  => $prodiId,
                'kelas_id'          => $kelasId,
                'tingkat'           => $tingkat,
                'semester_aktif'    => $semester,
                'tahun_akademik_id' => $thnAkademik,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal mencatat riwayat pendidikan.'];
        }

        log_audit('CREATE', 'riwayat_pendidikan_taruna', $nomorTaruna, null, [
            'tingkat'  => $tingkat,
            'semester' => $semester,
        ]);

        return ['success' => true, 'message' => 'Riwayat pendidikan berhasil dicatat.'];
    }

    /**
     * Membaca berkas impor taruna dari Excel (.xlsx atau .xls, termasuk ekspor HTML sim.pktj.ac.id).
     *
     * @return array{rows: array<int, array<string, mixed>>, message: string}
     */
    public function bacaBerkasImpor(string $filePath, string $ext = 'xlsx'): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            return ['rows' => [], 'message' => 'Berkas tidak dapat dibaca.'];
        }

        $cleanExt = strtolower(trim($ext));
        if (in_array($cleanExt, ['csv', 'txt'], true)) {
            return ['rows' => [], 'message' => 'Format CSV tidak didukung. Gunakan berkas Excel (.xlsx atau .xls).'];
        }
        if (!in_array($cleanExt, ['xlsx', 'xls'], true)) {
            return ['rows' => [], 'message' => 'Format berkas tidak didukung. Gunakan berkas Excel (.xlsx atau .xls).'];
        }

        $rawContent = (string) file_get_contents($filePath);
        $headLower  = strtolower(substr(ltrim($rawContent), 0, 512));

        // Ekspor sim.pktj.ac.id berbentuk tabel HTML atau pembungkus frameset (.xls)
        if (str_contains($headLower, '<table') || str_contains($headLower, '<html') || str_contains($headLower, '<frameset')) {
            return $this->parseHtmlExportTable($rawContent);
        }

        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheetRows   = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);
            return $this->mapSpreadsheetRows($sheetRows);
        }

        return ['rows' => [], 'message' => 'Format berkas Excel tidak didukung.'];
    }

    /**
     * Memeriksa baris-baris data impor Excel sebelum disimpan, sekaligus menghitung Angkatan (Tahun/Romawi) dan deteksi tahan tingkat.
     *
     * @param array<int, array<string, mixed>> $rawRows
     * @param string                           $namaBerkas
     * @return array<string, mixed>
     */
    public function periksaBerkasImpor(array $rawRows, string $namaBerkas = 'data_taruna.xlsx'): array
    {
        $prodiMap = [];
        $prodiNameMap = [];
        foreach ($this->db->table('ref_program_studi')->get()->getResultArray() as $p) {
            $id    = (int) $p['id'];
            $kode  = strtoupper(trim((string) $p['kode_prodi']));
            $namaP = strtolower(trim((string) $p['nama_prodi']));
            $prodiMap[$kode]      = $id;
            $prodiNameMap[$namaP] = ['id' => $id, 'kode' => $kode];

            // Izinkan pencocokan nama prodi tanpa awalan jenjang (mis. "Rekayasa Sistem Transportasi Jalan")
            $stripped = preg_replace('/^(d-?(?:iii|iv)|sarjana terapan|diploma\s*(?:iii|iv)?)\s+/i', '', $namaP);
            if (is_string($stripped) && trim($stripped) !== '') {
                $prodiNameMap[trim($stripped)] = ['id' => $id, 'kode' => $kode];
            }
        }

        ketarunaan_ensure_kalender_terkini($this->db);

        $angkatanMap = [];
        foreach ($this->db->table('ref_angkatan')->get()->getResultArray() as $a) {
            $angkatanMap[(int) $a['tahun_masuk']] = (int) $a['id'];
        }

        $activeTa = $this->db->table('ref_tahun_akademik')
            ->where('is_aktif', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $activeTaId       = !empty($activeTa['id']) ? (int) $activeTa['id'] : null;
        $activeTaTahun    = !empty($activeTa['tahun']) ? (int) $activeTa['tahun'] : (int) date('Y');
        $activeTaSemester = strtolower((string) ($activeTa['semester'] ?? 'ganjil'));

        $existingNomors = array_column(
            $this->db->table('master_taruna')->select('nomor_taruna')->get()->getResultArray(),
            'nomor_taruna'
        );
        $existingSet = array_fill_keys($existingNomors, true);
        $seenInFile  = [];

        $previewRows    = [];
        $validRows      = [];
        $siapDiimpor    = 0;
        $duplikat       = 0;
        $perluPerbaikan = 0;

        foreach ($rawRows as $idx => $r) {
            $barisKe = $idx + 2;
            $nomor   = trim((string) ($r['nomor_taruna'] ?? $r['nim'] ?? ''));
            $nama    = trim((string) ($r['nama_lengkap'] ?? $r['nama'] ?? ''));

            $prodiInput = trim((string) ($r['kode_prodi'] ?? $r['program_studi'] ?? ''));
            $kodeProdi  = strtoupper($prodiInput);
            if ($kodeProdi !== '' && !isset($prodiMap[$kodeProdi])) {
                $lowerProdi = strtolower($prodiInput);
                if (isset($prodiNameMap[$lowerProdi])) {
                    $kodeProdi = $prodiNameMap[$lowerProdi]['kode'];
                }
            }
            if ($kodeProdi === '') {
                $kodeProdi = 'RSTJ';
            }

            $tingkatRaw = (int) ($r['tingkat'] ?? $r['kelas'] ?? 1);
            $tingkat    = max(1, min(4, $tingkatRaw > 0 ? $tingkatRaw : 1));
            $semester   = (int) ($r['semester_aktif'] ?? 0);
            if ($semester < 1 || $semester > 8) {
                $semester = $activeTaSemester === 'genap'
                    ? min(8, $tingkat * 2)
                    : min(8, max(1, ($tingkat * 2) - 1));
            }

            $tglMasuk      = $this->parseDateToYmd((string) ($r['tanggal_masuk'] ?? $r['tglmasuk'] ?? ''));
            $rawTahun      = trim((string) ($r['tahun_masuk'] ?? $r['angkatan'] ?? ''));
            $tahunMasuk    = $this->resolveTahunMasuk($nomor, $rawTahun, $tglMasuk, $activeTaTahun, $tingkat);
            $angkatanLabel = ketarunaan_format_angkatan($tahunMasuk);

            $jk        = strtoupper(trim((string) ($r['jenis_kelamin'] ?? 'L')));
            $rawTempat = array_key_exists('tempat_lahir', $r) ? (string) $r['tempat_lahir'] : (string) ($r['tmplahir'] ?? '');
            $hasTempat = array_key_exists('tempat_lahir', $r) || array_key_exists('tmplahir', $r);
            $tempatLhr = $this->cleanTempatLahir($rawTempat, !$hasTempat ? 'Tegal' : '');

            if ($nomor === '' || $nama === '') {
                $perluPerbaikan++;
                $previewRows[] = [
                    'baris'          => $barisKe,
                    'nomor_taruna'   => $nomor !== '' ? $nomor : '-',
                    'nama_lengkap'   => $nama !== '' ? $nama : '-',
                    'kode_prodi'     => $kodeProdi,
                    'tahun_masuk'    => $tahunMasuk,
                    'angkatan_label' => $angkatanLabel,
                    'status_cek'     => 'tidak_valid',
                    'label_cek'      => 'Tidak valid',
                    'keterangan'     => $nomor === '' ? 'Nomor Taruna wajib diisi' : 'Nama Lengkap wajib diisi',
                ];
                continue;
            }

            if (!isset($prodiMap[$kodeProdi])) {
                $perluPerbaikan++;
                $previewRows[] = [
                    'baris'          => $barisKe,
                    'nomor_taruna'   => $nomor,
                    'nama_lengkap'   => $nama,
                    'kode_prodi'     => $kodeProdi,
                    'tahun_masuk'    => $tahunMasuk,
                    'angkatan_label' => $angkatanLabel,
                    'status_cek'     => 'tidak_valid',
                    'label_cek'      => 'Tidak valid',
                    'keterangan'     => 'Program Studi tidak dikenali (' . $prodiInput . ')',
                ];
                continue;
            }

            if (isset($existingSet[$nomor]) || isset($seenInFile[$nomor])) {
                $duplikat++;
                $previewRows[] = [
                    'baris'          => $barisKe,
                    'nomor_taruna'   => $nomor,
                    'nama_lengkap'   => $nama,
                    'kode_prodi'     => $kodeProdi,
                    'tahun_masuk'    => $tahunMasuk,
                    'angkatan_label' => $angkatanLabel,
                    'status_cek'     => 'duplikat',
                    'label_cek'      => 'Duplikat',
                    'keterangan'     => isset($existingSet[$nomor])
                        ? 'Nomor Taruna sudah terdaftar'
                        : 'Nomor Taruna ganda di dalam berkas',
                ];
                continue;
            }

            $namaKelas          = strtoupper(trim((string) ($r['nama_kelas'] ?? $r['golongan'] ?? '')));
            $waliInfo           = $this->resolveWaliInfo($r);
            $nominalTahun       = $activeTaTahun - $tingkat + 1;
            $isTahanTingkat     = $tahunMasuk > 0 && $tahunMasuk < $nominalTahun;
            $willCreateAngkatan = !isset($angkatanMap[$tahunMasuk]);

            $seenInFile[$nomor] = true;
            $siapDiimpor++;

            $catatanList = [];
            if ($namaKelas !== '') {
                $catatanList[] = "Kelas {$tingkat}{$namaKelas}";
            }
            if ($isTahanTingkat) {
                $catatanList[] = "Tahan Tingkat (Angkatan {$angkatanLabel})";
            }
            if ($willCreateAngkatan) {
                $catatanList[] = "Angkatan {$angkatanLabel} dibuat otomatis";
            }
            $keteranganValid = 'Siap diimpor' . (!empty($catatanList) ? ' — ' . implode(' • ', $catatanList) : '');

            $previewRows[] = [
                'baris'          => $barisKe,
                'nomor_taruna'   => $nomor,
                'nama_lengkap'   => $nama,
                'kode_prodi'     => $kodeProdi,
                'tahun_masuk'    => $tahunMasuk,
                'angkatan_label' => $angkatanLabel,
                'status_cek'     => 'valid',
                'label_cek'      => 'Valid',
                'keterangan'     => $keteranganValid,
            ];

            $validRows[] = [
                'nomor_taruna'      => $nomor,
                'nama_lengkap'      => $nama,
                'nik'               => $this->cleanNik((string) ($r['nik'] ?? '')),
                'jenis_kelamin'     => in_array($jk, ['L', 'P'], true) ? $jk : 'L',
                'tempat_lahir'      => $tempatLhr,
                'tanggal_lahir'     => $this->parseDateToYmd((string) ($r['tanggal_lahir'] ?? $r['tgllahir'] ?? '')),
                'agama'             => $this->normalizeAgama((string) ($r['agama'] ?? '')),
                'nomor_hp'          => $this->normalizePhone((string) ($r['nomor_hp'] ?? $r['notelp'] ?? '')),
                'email'             => $this->resolveEmailTaruna($nomor, (string) ($r['email'] ?? '')),
                'alamat_domisili'   => trim((string) ($r['alamat_domisili'] ?? '')) ?: null,
                'alamat_asal'       => trim((string) ($r['alamat_asal'] ?? $r['alamat'] ?? '')) ?: null,
                'nama_wali'         => $waliInfo['nama'],
                'hubungan_wali'     => $waliInfo['hubungan'],
                'nomor_hp_wali'     => $this->normalizePhone((string) ($r['nomor_hp_wali'] ?? $r['notelp_ortu'] ?? '')),
                'status_terkini'    => $this->mapStatusKode((string) ($r['status_terkini'] ?? $r['status'] ?? 'aktif')),
                'program_studi_id'  => $prodiMap[$kodeProdi],
                'angkatan_id'       => $angkatanMap[$tahunMasuk] ?? null,
                'tahun_masuk'       => $tahunMasuk,
                'tingkat'           => $tingkat,
                'semester_aktif'    => $semester,
                'nama_kelas'        => $namaKelas,
                'jalur_penerimaan'  => $this->resolveJalurPenerimaan($nomor, (string) ($r['jalur_penerimaan'] ?? '')),
                'asal_formasi'      => $this->resolveAsalFormasi($nomor, (string) ($r['asal_formasi'] ?? '')),
                'tahun_akademik_id' => $activeTaId,
                'tanggal_masuk'     => $tglMasuk,
            ];
        }

        return [
            'nama_berkas'     => $namaBerkas,
            'total_baris'     => count($rawRows),
            'siap_diimpor'    => $siapDiimpor,
            'duplikat'        => $duplikat,
            'perlu_perbaikan' => $perluPerbaikan,
            'preview_rows'    => $previewRows,
            'valid_rows'      => $validRows,
        ];
    }

    /**
     * Menyimpan baris hasil pemeriksaan impor yang valid (draf untuk UI, atau tervalidasi untuk migrasi sumber resmi).
     *
     * @param array<int, array<string, mixed>> $validRows
     * @return array{success: bool, imported: int, message: string}
     */
    public function simpanImporValid(array $validRows, bool $tervalidasi = false, string $nomorKeputusan = ''): array
    {
        if (empty($validRows)) {
            return ['success' => false, 'imported' => 0, 'message' => 'Tidak ada baris valid untuk diimpor.'];
        }

        $now            = date('Y-m-d H:i:s');
        $today          = date('Y-m-d');
        $imported       = 0;
        $statusValidasi = $tervalidasi ? 'tervalidasi' : 'draf';
        $noKeputusan    = trim($nomorKeputusan) !== '' ? trim($nomorKeputusan) : ('IMPOR/' . date('Y'));

        $activeTa = $this->db->table('ref_tahun_akademik')
            ->select('tahun')
            ->where('is_aktif', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        $activeTaTahun = !empty($activeTa['tahun']) ? (int) $activeTa['tahun'] : (int) date('Y');

        $angkatanCache = [];
        foreach ($this->db->table('ref_angkatan')->select('id, tahun_masuk')->get()->getResultArray() as $aRow) {
            $angkatanCache[(int) $aRow['tahun_masuk']] = (int) $aRow['id'];
        }

        $this->db->transStart();

        foreach ($validRows as $r) {
            $nomor = trim((string) ($r['nomor_taruna'] ?? ''));
            if ($nomor === '') {
                continue;
            }
            $exists = $this->db->table('master_taruna')->where('nomor_taruna', $nomor)->countAllResults();
            if ($exists > 0) {
                continue;
            }

            $nama        = trim((string) ($r['nama_lengkap'] ?? ''));
            $parts       = preg_split('/\s+/', $nama) ?: [$nama];
            $statusKode  = (string) ($r['status_terkini'] ?? 'aktif');
            $prodiId     = (int) ($r['program_studi_id'] ?? 1);
            $tahunMasuk  = (int) ($r['tahun_masuk'] ?? 0);
            $angkatanId  = !empty($r['angkatan_id'])
                ? (int) $r['angkatan_id']
                : $this->resolveOrCreateAngkatanId($tahunMasuk, $angkatanCache);
            $tingkat     = max(1, min(4, (int) ($r['tingkat'] ?? 1)));
            $semester    = max(1, min(8, (int) ($r['semester_aktif'] ?? 1)));
            $namaKelas   = trim((string) ($r['nama_kelas'] ?? ''));
            $kelasId     = $namaKelas !== '' ? $this->resolveOrCreateKelasId($prodiId, $angkatanId, $tingkat, $namaKelas, $activeTaTahun) : null;
            $tglMasuk    = !empty($r['tanggal_masuk']) ? (string) $r['tanggal_masuk'] : $today;
            $tahunAkadId = !empty($r['tahun_akademik_id']) ? (int) $r['tahun_akademik_id'] : null;

            $nominalTahun   = $activeTaTahun - $tingkat + 1;
            $isTahanTingkat = $tahunMasuk > 0 && $tahunMasuk < $nominalTahun;
            $angkatanLabel  = ketarunaan_format_angkatan($tahunMasuk);
            $emailTaruna    = $this->resolveEmailTaruna($nomor, (string) ($r['email'] ?? ''));

            $this->db->table('master_taruna')->insert([
                'nomor_taruna'    => $nomor,
                'nama_lengkap'    => $nama,
                'nik'             => $r['nik'] ?? null,
                'nama_panggilan'  => $parts[0] ?? $nama,
                'jenis_kelamin'   => $r['jenis_kelamin'] ?? 'L',
                'tempat_lahir'    => ($r['tempat_lahir'] ?? '') !== '' ? $r['tempat_lahir'] : null,
                'tanggal_lahir'   => $r['tanggal_lahir'] ?? null,
                'agama'           => $r['agama'] ?? 'Islam',
                'nomor_hp'        => ($r['nomor_hp'] ?? '') !== '' ? $r['nomor_hp'] : null,
                'email'           => $emailTaruna,
                'alamat_domisili' => $r['alamat_domisili'] ?? null,
                'alamat_asal'     => $r['alamat_asal'] ?? null,
                'nama_wali'       => $r['nama_wali'] ?? null,
                'hubungan_wali'   => $r['hubungan_wali'] ?? null,
                'nomor_hp_wali'   => $r['nomor_hp_wali'] ?? null,
                'status_terkini'  => $statusKode,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $this->db->table('pendidikan_taruna')->insert([
                'nomor_taruna'      => $nomor,
                'program_studi_id'  => $prodiId,
                'angkatan_id'       => $angkatanId,
                'kelas_id'          => $kelasId,
                'tingkat'           => $tingkat,
                'semester_aktif'    => $semester,
                'jalur_penerimaan'  => $this->resolveJalurPenerimaan($nomor, (string) ($r['jalur_penerimaan'] ?? '')),
                'asal_formasi'      => $this->resolveAsalFormasi($nomor, (string) ($r['asal_formasi'] ?? '')),
                'tahun_akademik_id' => $tahunAkadId,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $this->db->table('riwayat_pendidikan_taruna')->insert([
                'nomor_taruna'      => $nomor,
                'program_studi_id'  => $prodiId,
                'kelas_id'          => $kelasId,
                'tingkat'           => $tingkat,
                'semester'          => $semester,
                'tahun_akademik_id' => $tahunAkadId,
                'mulai_berlaku'     => $tglMasuk,
                'created_at'        => $now,
            ]);

            $alasanStatus = $tervalidasi
                ? ($isTahanTingkat
                    ? "Taruna tahan tingkat dari angkatan {$angkatanLabel} (turun tingkat ke Tingkat {$tingkat})"
                    : 'Data taruna aktif bersumber dari sim.pktj.ac.id')
                : ($isTahanTingkat
                    ? "Data hasil impor massal — taruna tahan tingkat dari angkatan {$angkatanLabel} (menunggu validasi)"
                    : 'Data hasil impor massal (menunggu validasi)');

            $statusRow = [
                'nomor_taruna'      => $nomor,
                'kode_status'       => $statusKode,
                'mulai_berlaku'     => $tglMasuk,
                'alasan_perubahan'  => $alasanStatus,
                'nomor_keputusan'   => $noKeputusan,
                'tanggal_keputusan' => $tglMasuk,
                'status_validasi'   => $statusValidasi,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
            if ($tervalidasi) {
                $statusRow['divalidasi_oleh']  = 'Sistem Migrasi ECC';
                $statusRow['tanggal_validasi'] = $today;
            }
            $this->db->table('riwayat_status_taruna')->insert($statusRow);
            $this->ensureTarunaPortalAccount($nomor, $nama, $emailTaruna, $now);

            $imported++;
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'imported' => 0, 'message' => 'Gagal menyimpan data impor. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'master_taruna', 'BATCH_IMPOR', null, ['jumlah_baris' => $imported, 'status' => $statusValidasi]);

        return [
            'success'  => true,
            'imported' => $imported,
            'message'  => $imported . ($tervalidasi
                ? ' baris data taruna berhasil disimpan.'
                : ' baris data taruna berhasil disimpan sebagai draf.'),
        ];
    }

    /**
     * Aturan resmi jalur penerimaan: digit ke-5 Nomor Taruna bernilai '1' => Pola Pembibitan, selain itu => Mandiri.
     */
    public function resolveJalurPenerimaan(string $nomorTaruna, string $explicit = ''): string
    {
        $cleanExplicit = trim($explicit);
        if ($cleanExplicit !== '') {
            return $cleanExplicit;
        }

        $cleanNomor = trim($nomorTaruna);
        if (strlen($cleanNomor) >= 5 && $cleanNomor[4] === '1') {
            return 'Pola Pembibitan';
        }

        return 'Mandiri';
    }

    /**
     * Aturan resmi asal formasi: digit ke-5 Nomor Taruna bernilai '1' => Polbit, selain itu => Mandiri.
     */
    public function resolveAsalFormasi(string $nomorTaruna, string $explicit = ''): string
    {
        $cleanExplicit = trim($explicit);
        if ($cleanExplicit !== '') {
            return $cleanExplicit;
        }

        $cleanNomor = trim($nomorTaruna);
        if (strlen($cleanNomor) >= 5 && $cleanNomor[4] === '1') {
            return 'Polbit';
        }

        return 'Mandiri';
    }

    /**
     * Format email baku taruna PKTJ: {nomor_taruna}@student.pktj.ac.id bila belum tersedia di sumber.
     */
    public function resolveEmailTaruna(string $nomorTaruna, string $explicit = ''): ?string
    {
        $cleanExplicit = trim($explicit);
        if ($cleanExplicit !== '') {
            return $cleanExplicit;
        }

        $cleanNomor = trim($nomorTaruna);
        return $cleanNomor !== '' ? strtolower($cleanNomor) . '@student.pktj.ac.id' : null;
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, message: string}
     */
    private function parseCsvImporFile(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ['rows' => [], 'message' => 'Berkas CSV tidak dapat dibaca.'];
        }

        $headers = fgetcsv($handle, 4000, ',');
        if ($headers && count($headers) === 1 && str_contains((string) $headers[0], ';')) {
            rewind($handle);
            $headers   = fgetcsv($handle, 4000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $matrix = [];
        if (is_array($headers)) {
            $matrix[] = $headers;
        }
        while (($line = fgetcsv($handle, 4000, $delimiter)) !== false) {
            if (!empty(array_filter($line, static fn ($v) => trim((string) $v) !== ''))) {
                $matrix[] = $line;
            }
        }
        fclose($handle);

        return $this->mapSpreadsheetRows($matrix);
    }

    /**
     * Memparsing berkas .xls berbasis HTML ekspor sim.pktj.ac.id serta menolak aman file frameset/kosong.
     *
     * @return array{rows: array<int, array<string, mixed>>, message: string}
     */
    private function parseHtmlExportTable(string $html): array
    {
        $clean = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $clean);
        libxml_clear_errors();

        $matrix = [];
        foreach ($dom->getElementsByTagName('tr') as $tr) {
            $cells = [];
            foreach ($tr->childNodes as $child) {
                if ($child instanceof \DOMElement && in_array(strtolower($child->tagName), ['td', 'th'], true)) {
                    $cells[] = trim(html_entity_decode($child->textContent, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }
            }
            if (!empty(array_filter($cells, static fn ($v) => $v !== ''))) {
                $matrix[] = $cells;
            }
        }

        if (empty($matrix)) {
            return [
                'rows'    => [],
                'message' => 'Berkas tidak berisi tabel daftar taruna (kemungkinan file halaman indeks kosong).',
            ];
        }

        return $this->mapSpreadsheetRows($matrix);
    }

    /**
     * Memetakan matriks baris (baik format 39 kolom sim.pktj.ac.id maupun template 6 kolom ECC) ke key terstruktur.
     *
     * @param array<int, array<int, mixed>> $matrix
     * @return array{rows: array<int, array<string, mixed>>, message: string}
     */
    private function mapSpreadsheetRows(array $matrix): array
    {
        if (empty($matrix)) {
            return ['rows' => [], 'message' => 'Berkas impor kosong.'];
        }

        $headerRow    = array_map(static fn ($v): string => strtolower(trim((string) $v)), $matrix[0]);
        $firstCell    = $headerRow[0] ?? '';
        $hasHeaderRow = in_array($firstCell, ['nim', 'nomor_taruna', 'no_taruna'], true);

        $rows = [];
        if ($hasHeaderRow) {
            for ($i = 1; $i < count($matrix); $i++) {
                $line = $matrix[$i];
                if (empty(array_filter($line, static fn ($v) => trim((string) $v) !== ''))) {
                    continue;
                }
                $assoc = [];
                foreach ($headerRow as $colIdx => $colKey) {
                    // Kolom sensitif PASSWORD dari sistem lama wajib diabaikan
                    if ($colKey === '' || $colKey === 'password') {
                        continue;
                    }
                    $assoc[$colKey] = trim((string) ($line[$colIdx] ?? ''));
                }
                $rows[] = $assoc;
            }
        } else {
            // Fallback baris tanpa header menggunakan urutan 6-8 kolom template resmi ECC
            foreach ($matrix as $line) {
                if (empty(array_filter($line, static fn ($v) => trim((string) $v) !== ''))) {
                    continue;
                }
                $rowMap = [
                    'nomor_taruna'  => trim((string) ($line[0] ?? '')),
                    'nama_lengkap'  => trim((string) ($line[1] ?? '')),
                    'kode_prodi'    => trim((string) ($line[2] ?? 'RSTJ')),
                    'tahun_masuk'   => trim((string) ($line[3] ?? '')),
                    'jenis_kelamin' => trim((string) ($line[4] ?? 'L')),
                    'tempat_lahir'  => trim((string) ($line[5] ?? 'Tegal')),
                ];
                if (isset($line[6]) && trim((string) $line[6]) !== '') {
                    $rowMap['tingkat'] = trim((string) $line[6]);
                }
                if (isset($line[7]) && trim((string) $line[7]) !== '') {
                    $rowMap['nama_kelas'] = trim((string) $line[7]);
                }
                $rows[] = $rowMap;
            }
        }

        if (empty($rows)) {
            return [
                'rows'    => [],
                'message' => 'Berkas hanya memuat judul kolom tanpa baris data taruna.',
            ];
        }

        return ['rows' => $rows, 'message' => ''];
    }

    /**
     * Mengurai input tahun masuk dari angka tahun ("2022"), gabungan ("2022/XXXIII"), atau angka Romawi ("XXXIII").
     */
    private function parseTahunMasukInput(string $raw): int
    {
        $clean = trim($raw);
        if ($clean === '') {
            return 0;
        }

        if (preg_match('/^\s*(\d{4})\s*(?:\/\s*[IVXLCDM]+)?\s*$/i', $clean, $m)) {
            $yr = (int) $m[1];
            return ($yr >= 1990 && $yr <= 2100) ? $yr : 0;
        }

        $angkatanKe = ketarunaan_roman_to_int($clean);
        if ($angkatanKe > 0) {
            $yr = 1989 + $angkatanKe;
            return ($yr >= 1990 && $yr <= 2100) ? $yr : 0;
        }

        return 0;
    }

    /**
     * Menentukan tahun masuk taruna asli (termasuk taruna tahan tingkat dari 2 digit awal Nomor Taruna).
     */
    private function resolveTahunMasuk(
        string $nomor,
        string $explicitRaw,
        ?string $tglMasukYmd,
        ?int $activeTaTahun = null,
        int $tingkat = 1
    ): int {
        $parsedExplicit = $this->parseTahunMasukInput($explicitRaw);
        if ($parsedExplicit > 0) {
            return $parsedExplicit;
        }

        if (preg_match('/^(\d{2})/', $nomor, $m)) {
            $yy      = (int) $m[1];
            $fromNim = $yy >= 90 ? (1900 + $yy) : (2000 + $yy);
            if ($fromNim >= 1990 && $fromNim <= 2100) {
                return $fromNim;
            }
        }

        if ($tglMasukYmd !== null && preg_match('/^(\d{4})-/', $tglMasukYmd, $m)) {
            return (int) $m[1];
        }

        if ($activeTaTahun !== null && $activeTaTahun >= 1990) {
            return max(1990, $activeTaTahun - max(1, $tingkat) + 1);
        }

        return (int) date('Y');
    }

    /**
     * Mengambil atau membuat baris ref_angkatan secara otomatis lengkap dengan angka Romawi (Tahun Masuk - 1989).
     *
     * @param array<int, int> $cache
     */
    private function resolveOrCreateAngkatanId(int $tahunMasuk, array &$cache = []): int
    {
        $tahun = ($tahunMasuk >= 1990 && $tahunMasuk <= 2100) ? $tahunMasuk : (int) date('Y');
        if (isset($cache[$tahun])) {
            return $cache[$tahun];
        }

        $existing = $this->db->table('ref_angkatan')
            ->select('id')
            ->where('tahun_masuk', $tahun)
            ->get()
            ->getRowArray();

        if ($existing) {
            $cache[$tahun] = (int) $existing['id'];
            return $cache[$tahun];
        }

        $meta = ketarunaan_angkatan_meta($tahun);
        $now  = date('Y-m-d H:i:s');
        $row  = [
            'tahun_masuk'     => $tahun,
            'angkatan_ke'     => $meta['angkatan_ke'],
            'kode_romawi'     => $meta['kode_romawi'] !== '' ? $meta['kode_romawi'] : null,
            'perkiraan_lulus' => $tahun + 4,
            'is_aktif'        => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        $this->db->table('ref_angkatan')->insert($row);
        $newId         = (int) $this->db->insertID();
        $cache[$tahun] = $newId;

        log_audit('CREATE', 'ref_angkatan', (string) $newId, null, $row);

        return $newId;
    }

    private function resolveOrCreateKelasId(int $prodiId, int $angkatanId, int $tingkat, string $namaKelas, ?int $activeTaTahun = null): ?int
    {
        $cleanNama = strtoupper(trim($namaKelas));
        if ($cleanNama === '') {
            return null;
        }

        // Cocokkan kelas fisik aktif berdasarkan (prodi, tingkat, nama_kelas) agar taruna tahan tingkat tergabung di kelas berjalan
        $row = $this->db->table('ref_kelas')
            ->select('id')
            ->where('program_studi_id', $prodiId)
            ->where('tingkat', $tingkat)
            ->where('nama_kelas', $cleanNama)
            ->orderBy('angkatan_id', 'DESC')
            ->get()
            ->getRowArray();

        if ($row) {
            return (int) $row['id'];
        }

        $targetAngkatanId = $angkatanId;
        if ($activeTaTahun !== null && $activeTaTahun > 2000) {
            $nominalTahun     = $activeTaTahun - $tingkat + 1;
            $targetAngkatanId = $this->resolveOrCreateAngkatanId($nominalTahun);
        }

        $existingUnique = $this->db->table('ref_kelas')
            ->select('id')
            ->where('program_studi_id', $prodiId)
            ->where('angkatan_id', $targetAngkatanId)
            ->where('nama_kelas', $cleanNama)
            ->get()
            ->getRowArray();

        if ($existingUnique) {
            return (int) $existingUnique['id'];
        }

        $this->db->table('ref_kelas')->insert([
            'program_studi_id' => $prodiId,
            'angkatan_id'      => $targetAngkatanId,
            'tingkat'          => $tingkat,
            'nama_kelas'       => $cleanNama,
            'is_aktif'         => 1,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }

    private function cleanNik(string $rawNik): ?string
    {
        $digits = preg_replace('/\D/', '', ltrim(trim($rawNik), "' "));
        return is_string($digits) && $digits !== '' ? $digits : null;
    }

    private function normalizePhone(string $rawPhone): ?string
    {
        $stripped = str_replace(['?', "'", "\xc2\xa0"], '', trim($rawPhone));
        if ($stripped === '') {
            return null;
        }

        // Jika satu sel berisi lebih dari 1 nomor (dipisah '/', ';', ',', atau kata 'dan'), ambil nomor pertama yang valid
        $parts = preg_split('/[\/,;]+|\bdan\b/i', $stripped) ?: [$stripped];
        foreach ($parts as $part) {
            $digits = preg_replace('/\D/', '', $part);
            if (!is_string($digits) || strlen($digits) < 6) {
                continue;
            }
            if (preg_match('/^62(8\d+)$/', $digits, $m)) {
                $digits = '0' . $m[1];
            } elseif (preg_match('/^8\d{7,13}$/', $digits)) {
                $digits = '0' . $digits;
            }
            return substr($digits, 0, 30);
        }

        return null;
    }

    private function normalizeAgama(string $rawAgama): string
    {
        $val = trim($rawAgama);
        if ($val === '') {
            return 'Islam';
        }
        $lower = strtolower($val);
        if (str_contains($lower, 'katolik')) {
            return 'Katolik';
        }
        if (str_contains($lower, 'protestan') || $lower === 'kristen') {
            return 'Kristen';
        }
        foreach (['Islam', 'Hindu', 'Buddha', 'Konghucu'] as $valid) {
            if (strcasecmp($val, $valid) === 0) {
                return $valid;
            }
        }
        return 'Islam';
    }

    private function cleanTempatLahir(string $rawTempat, string $default = ''): string
    {
        $val = trim($rawTempat);
        if ($val === '') {
            return $default;
        }

        // Jika seluruh field justru berisi tanggal (mis. "18/Mei/2005" atau "27-07-2005"), biarkan kosong agar diisi akurat oleh unit
        if (preg_match('/^\d{1,2}[-\/\s]+[a-zA-Z0-9]+[-\/\s]+\d{4}$/', $val)) {
            return '';
        }

        // Bersihkan akhiran tanggal yang menempel di nama kota (mis. "Tegal/ 27-07-2005", "Indramayu, 31 Januari 2005", atau "BREBES 12 NOVEMBER 2006")
        $val = preg_replace('/(?:[\/,]|\s)+\d{1,2}\s+[a-zA-Z]+\s+\d{4}$/', '', $val) ?? $val;
        $val = preg_replace('/(?:[\/,]|\s)+\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}$/', '', $val) ?? $val;

        return trim($val, " \t\n\r\0\x0B,/");
    }

    private function parseDateToYmd(string $rawDate): ?string
    {
        $clean = trim($rawDate);
        if ($clean === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $clean)) {
            return $clean;
        }
        if (preg_match('/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/', $clean, $m)) {
            if (checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
                return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
            }
        }
        return null;
    }

    private function mapStatusKode(string $rawStatus): string
    {
        $lower = strtolower(trim($rawStatus));
        return match (true) {
            str_contains($lower, 'skorsing') => 'skorsing',
            str_contains($lower, 'cuti')     => 'cuti',
            str_contains($lower, 'undur')    => 'mengundurkan_diri',
            str_contains($lower, 'drop') || $lower === 'do' => 'drop_out',
            str_contains($lower, 'lulus')    => 'lulus',
            default                          => 'aktif',
        };
    }

    /**
     * @param array<string, mixed> $r
     * @return array{nama: ?string, hubungan: ?string}
     */
    private function resolveWaliInfo(array $r): array
    {
        $namaWaliExplicit = trim((string) ($r['nama_wali'] ?? ''));
        if ($namaWaliExplicit !== '') {
            return [
                'nama'     => $namaWaliExplicit,
                'hubungan' => trim((string) ($r['hubungan_wali'] ?? '')) ?: 'Wali',
            ];
        }

        $ayah = trim((string) ($r['ayah'] ?? ''));
        if ($ayah !== '') {
            return ['nama' => $ayah, 'hubungan' => 'Ayah'];
        }

        $ibu = trim((string) ($r['ibu'] ?? ''));
        if ($ibu !== '') {
            return ['nama' => $ibu, 'hubungan' => 'Ibu'];
        }

        return ['nama' => null, 'hubungan' => null];
    }

    /**
     * Mengambil satu baris riwayat perubahan status berdasarkan ID.
     */
    public function getRiwayatStatusById(int $riwayatId): ?array
    {
        $row = $this->db->table('riwayat_status_taruna')
            ->where('id', $riwayatId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Mengirimkan draf perubahan status menuju antrean validasi.
     *
     * @return array{success: bool, message: string, nomor_taruna?: string}
     */
    public function ajukanDraftStatus(int $riwayatId): array
    {
        $row = $this->getRiwayatStatusById($riwayatId);
        if (!$row) {
            return ['success' => false, 'message' => 'Catatan draf status tidak ditemukan.'];
        }
        if (($row['status_validasi'] ?? '') !== 'draf') {
            return ['success' => false, 'message' => 'Hanya pengajuan berstatus Draf yang dapat diajukan.', 'nomor_taruna' => (string) $row['nomor_taruna']];
        }
        if (empty($row['bukti_dokumen'])) {
            return ['success' => false, 'message' => 'Draf belum memiliki tautan dokumen SK. Lengkapi tautan dokumen terlebih dahulu.', 'nomor_taruna' => (string) $row['nomor_taruna']];
        }

        $nomorTaruna = (string) $row['nomor_taruna'];
        $taruna      = $this->db->table('master_taruna')->select('status_terkini')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if ($taruna && strtolower((string) $taruna['status_terkini']) === strtolower((string) ($row['kode_status'] ?? ''))) {
            return ['success' => false, 'message' => 'Status baru sama dengan status taruna saat ini.', 'nomor_taruna' => $nomorTaruna];
        }

        $pendingCount = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('status_validasi', 'diajukan')
            ->where('id !=', $riwayatId)
            ->countAllResults();
        if ($pendingCount > 0) {
            return ['success' => false, 'message' => 'Masih ada pengajuan status lain yang menunggu validasi.', 'nomor_taruna' => $nomorTaruna];
        }

        $this->db->transStart();
        $this->db->table('riwayat_status_taruna')
            ->where('id', $riwayatId)
            ->update([
                'status_validasi' => 'diajukan',
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal mengajukan draf status. Silakan coba lagi.', 'nomor_taruna' => $nomorTaruna];
        }

        log_audit('UPDATE', 'riwayat_status_taruna', (string) $riwayatId, ['status_validasi' => 'draf'], ['status_validasi' => 'diajukan']);

        return ['success' => true, 'message' => 'Draf perubahan status berhasil dikirim ke antrean validasi.', 'nomor_taruna' => $nomorTaruna];
    }

    /**
     * Menghapus draf perubahan status yang belum diajukan.
     *
     * @return array{success: bool, message: string, nomor_taruna?: string, bukti_dokumen?: ?string}
     */
    public function hapusDraftStatus(int $riwayatId): array
    {
        $row = $this->getRiwayatStatusById($riwayatId);
        if (!$row) {
            return ['success' => false, 'message' => 'Catatan draf status tidak ditemukan.'];
        }
        if (($row['status_validasi'] ?? '') !== 'draf') {
            return ['success' => false, 'message' => 'Hanya pengajuan berstatus Draf yang dapat dihapus.', 'nomor_taruna' => (string) $row['nomor_taruna']];
        }

        $nomorTaruna = (string) $row['nomor_taruna'];
        $this->db->transStart();
        $this->db->table('riwayat_status_taruna')->where('id', $riwayatId)->delete();
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menghapus draf status. Silakan coba lagi.', 'nomor_taruna' => $nomorTaruna];
        }

        log_audit('DELETE', 'riwayat_status_taruna', (string) $riwayatId, $row, null);

        return [
            'success'       => true,
            'message'       => 'Draf perubahan status berhasil dihapus.',
            'nomor_taruna'  => $nomorTaruna,
            'bukti_dokumen' => !empty($row['bukti_dokumen']) ? (string) $row['bukti_dokumen'] : null,
        ];
    }

    /**
     * Memastikan kelas yang dipilih benar-benar milik program studi (dan tingkat bila diberikan).
     */
    private function resolveValidKelasId(int $kelasId, int $prodiId, ?int $tingkat = null): ?int
    {
        if ($kelasId <= 0) {
            return null;
        }

        $builder = $this->db->table('ref_kelas')
            ->select('id')
            ->where('id', $kelasId)
            ->where('program_studi_id', $prodiId);

        if ($tingkat !== null && $tingkat >= 1 && $tingkat <= 4) {
            $builder->where('tingkat', $tingkat);
        }

        $row = $builder->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    /**
     * Menjamin akun login portal mandiri `taruna` terbuat di `users` & `user_roles`.
     */
    private function ensureTarunaPortalAccount(string $nomorTaruna, string $namaLengkap, ?string $email, string $now): void
    {
        $cleanNomor = trim($nomorTaruna);
        if ($cleanNomor === '') {
            return;
        }

        $existingUser = $this->db->table('users')
            ->select('id')
            ->where('username', $cleanNomor)
            ->get()
            ->getRowArray();

        if ($existingUser) {
            $userId = (int) $existingUser['id'];
        } else {
            $resolvedEmail = trim((string) $email) !== '' ? trim((string) $email) : (strtolower($cleanNomor) . '@student.pktj.ac.id');
            $this->db->table('users')->insert([
                'username'       => $cleanNomor,
                'password'       => password_hash($cleanNomor, PASSWORD_DEFAULT),
                'nama_lengkap'   => trim($namaLengkap) !== '' ? trim($namaLengkap) : $cleanNomor,
                'email'          => $resolvedEmail,
                'role'           => 'taruna',
                'status_pegawai' => 'aktif',
                'nip'            => '',
                'jabatan'        => 'Taruna PKTJ',
                'unit'           => 'Taruna',
                'foto'           => 'default.png',
            ]);
            $userId = (int) $this->db->insertID();
        }

        if ($userId > 0) {
            $hasRole = $this->db->table('user_roles')
                ->where('user_id', $userId)
                ->where('role_name', 'taruna')
                ->countAllResults();

            if ($hasRole === 0) {
                $this->db->table('user_roles')->insert([
                    'user_id'    => $userId,
                    'role_name'  => 'taruna',
                    'created_at' => $now,
                ]);
            }
        }
    }
}

