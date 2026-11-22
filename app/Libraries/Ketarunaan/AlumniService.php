<?php

namespace App\Libraries\Ketarunaan;

use CodeIgniter\Database\BaseConnection;

/**
 * Service bisnis Modul 7: Alumni — Profil dasar (ECC 2.0).
 * Mencakup penetapan profil alumni dari kelulusan tervalidasi (ALM-01),
 * pembaruan kontak dan pekerjaan alumni terlacak (ALM-02),
 * serta alur koreksi kelulusan terkendali non-destruktif (ALM-03).
 */
class AlumniService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Mengambil data referensi dropdown untuk filter dan formulir Modul Alumni.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        ketarunaan_ensure_kalender_terkini($this->db);

        $prodi = $this->db->table('ref_program_studi')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $angkatan = $this->db->table('ref_angkatan')
            ->where('is_aktif', 1)
            ->orderBy('tahun_masuk', 'DESC')
            ->get()
            ->getResultArray();

        $tahunRows = $this->db->table('alumni_profil')
            ->select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus IS NOT NULL')
            ->orderBy('tahun_lulus', 'DESC')
            ->get()
            ->getResultArray();

        $tahunList = array_values(array_unique(array_filter(array_map(
            static fn (array $r): int => (int) ($r['tahun_lulus'] ?? 0),
            $tahunRows
        ))));
        if (!in_array((int) date('Y'), $tahunList, true)) {
            $tahunList[] = (int) date('Y');
            rsort($tahunList);
        }

        $statusPengganti = [];
        foreach (ketarunaan_status_catalog() as $kode => $meta) {
            if ($kode !== 'lulus') {
                $statusPengganti[$kode] = $meta['label'];
            }
        }

        return [
            'prodi'             => $prodi,
            'angkatan'          => $angkatan,
            'tahun_lulus'       => $tahunList,
            'sumber_konfirmasi' => ketarunaan_alumni_sumber_konfirmasi_catalog(),
            'status_pengganti'  => $statusPengganti,
        ];
    }

    /**
     * Mengambil daftar direktori alumni profil dasar dan 4 metrik KPI ringkas.
     *
     * @param array<string, mixed> $filters
     * @param bool                 $isSelfOnly
     * @param string|null          $selfNomor
     * @param bool                 $forExport
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters = [], bool $isSelfOnly = false, ?string $selfNomor = null, bool $forExport = false): array
    {
        $q               = trim((string) ($filters['q'] ?? ''));
        $prodiId         = trim((string) ($filters['prodi_id'] ?? 'all'));
        $angkatanId      = trim((string) ($filters['angkatan_id'] ?? 'all'));
        $tahunLulus      = trim((string) ($filters['tahun_lulus'] ?? 'all'));
        $statusProfil    = trim((string) ($filters['status_profil'] ?? 'aktif'));
        $pekerjaanStatus = trim((string) ($filters['pekerjaan_status'] ?? 'all'));

        $builder = $this->db->table('alumni_profil al')
            ->select('
                al.nomor_taruna,
                al.tanggal_lulus,
                al.nomor_keputusan_lulus,
                al.tahun_lulus,
                al.pekerjaan_saat_ini,
                al.instansi_saat_ini,
                al.kontak_terakhir,
                al.email_alumni,
                al.telepon_alumni,
                al.kota_domisili,
                al.status_profil,
                al.sumber_konfirmasi,
                al.tanggal_pembaruan_kontak,
                al.dicatat_oleh,
                al.divalidasi_oleh,
                m.nama_lengkap,
                m.jenis_kelamin,
                m.status_terkini,
                m.foto,
                pt.program_studi_id,
                pt.angkatan_id,
                pr.kode_prodi,
                pr.nama_prodi,
                pr.jenjang,
                an.tahun_masuk
            ')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = al.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan an', 'an.id = pt.angkatan_id', 'left');

        if ($isSelfOnly) {
            if ($selfNomor !== null && $selfNomor !== '') {
                $builder->where('al.nomor_taruna', $selfNomor);
            } else {
                $builder->where('1 = 0');
            }
        } else {
            if ($statusProfil === 'nonaktif_koreksi') {
                $builder->where('al.status_profil', 'nonaktif_koreksi');
            } elseif ($statusProfil !== 'all') {
                $builder->where('al.status_profil', 'aktif')
                    ->where('m.status_terkini', 'lulus');
            }

            if ($prodiId !== '' && $prodiId !== 'all' && ctype_digit($prodiId)) {
                $builder->where('pt.program_studi_id', (int) $prodiId);
            }
            if ($angkatanId !== '' && $angkatanId !== 'all' && ctype_digit($angkatanId)) {
                $builder->where('pt.angkatan_id', (int) $angkatanId);
            }
            if ($tahunLulus !== '' && $tahunLulus !== 'all' && ctype_digit($tahunLulus)) {
                $builder->where('al.tahun_lulus', (int) $tahunLulus);
            }
            if ($pekerjaanStatus === 'terisi') {
                $builder->where('al.pekerjaan_saat_ini IS NOT NULL')
                    ->where('al.pekerjaan_saat_ini !=', '');
            }
            if ($q !== '') {
                $builder->groupStart()
                    ->like('al.nomor_taruna', $q)
                    ->orLike('m.nama_lengkap', $q)
                    ->orLike('al.pekerjaan_saat_ini', $q)
                    ->orLike('al.instansi_saat_ini', $q)
                    ->orLike('al.kota_domisili', $q)
                    ->orLike('al.nomor_keputusan_lulus', $q)
                    ->groupEnd();
            }
        }

        $sortMap = [
            'nama_lengkap'  => 'm.nama_lengkap',
            'prodi'         => 'pr.kode_prodi',
            'tanggal_lulus' => 'al.tanggal_lulus',
            'pekerjaan'     => 'al.pekerjaan_saat_ini',
            'status_profil' => 'al.status_profil',
        ];
        $sortKey = trim((string) ($filters['sort'] ?? ''));
        $sortDir = strtolower(trim((string) ($filters['dir'] ?? 'desc'))) === 'asc' ? 'ASC' : 'DESC';

        if ($sortKey !== '' && isset($sortMap[$sortKey])) {
            $builder->orderBy($sortMap[$sortKey], $sortDir)
                ->orderBy('m.nama_lengkap', 'ASC');
        } else {
            $builder->orderBy('al.tahun_lulus', 'DESC')
                ->orderBy('al.tanggal_lulus', 'DESC')
                ->orderBy('m.nama_lengkap', 'ASC');
        }

        $allRows    = $builder->get()->getResultArray();
        $totalRows  = count($allRows);
        $perPage    = 15;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page       = max(1, min($totalPages, (int) ($filters['page'] ?? 1)));

        $rows = $forExport
            ? $allRows
            : array_slice($allRows, ($page - 1) * $perPage, $perPage);

        $kpi = $this->buildKpiSummary($isSelfOnly, $selfNomor);

        return [
            'kpi'        => $kpi,
            'rows'       => $rows,
            'pagination' => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total_rows'  => $totalRows,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Mengambil profil dasar lengkap 1-to-1 alumni terhubung ke Nomor Taruna lama beserta riwayat perubahan.
     *
     * @return array<string, mixed>|null
     */
    public function getAlumniDetail(string $nomorTaruna, bool $canViewSensitive = false): ?array
    {
        $nomorTaruna = trim($nomorTaruna);
        if ($nomorTaruna === '') {
            return null;
        }

        $row = $this->db->table('alumni_profil al')
            ->select('
                al.*,
                m.nama_lengkap,
                m.nama_panggilan,
                m.nik,
                m.jenis_kelamin,
                m.tempat_lahir,
                m.tanggal_lahir,
                m.agama,
                m.foto,
                m.nomor_hp AS hp_taruna,
                m.email AS email_taruna,
                m.alamat_asal,
                m.nama_wali,
                m.hubungan_wali,
                m.nomor_hp_wali,
                m.status_terkini,
                pt.program_studi_id,
                pt.angkatan_id,
                pt.kelas_id,
                pt.jalur_penerimaan,
                pt.asal_formasi,
                pr.kode_prodi,
                pr.nama_prodi,
                pr.jenjang,
                an.tahun_masuk,
                an.perkiraan_lulus,
                COALESCE(k.nama_kelas, "A") AS nama_kelas
            ')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = al.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan an', 'an.id = pt.angkatan_id', 'left')
            ->join('ref_kelas k', 'k.id = pt.kelas_id', 'left')
            ->where('al.nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        if (!$canViewSensitive) {
            $row['nik']           = null;
            $row['nama_wali']     = null;
            $row['hubungan_wali'] = null;
            $row['nomor_hp_wali'] = null;
            $row['alamat_asal']   = null;
        }

        $riwayatPerubahan = $this->db->table('alumni_riwayat_perubahan')
            ->where('nomor_taruna', $nomorTaruna)
            ->orderBy('tanggal_kejadian', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $pendingKoreksi = null;
        foreach ($riwayatPerubahan as $item) {
            if (($item['status_validasi'] ?? '') === 'diajukan') {
                $pendingKoreksi = $item;
                break;
            }
        }

        $riwayatStatusTaruna = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->orderBy('mulai_berlaku', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $jumlahPrestasi = (int) $this->db->table('taruna_peserta_prestasi pp')
            ->join('taruna_prestasi p', 'p.id = pp.prestasi_id')
            ->where('pp.nomor_taruna', $nomorTaruna)
            ->where('p.status_validasi', 'tervalidasi')
            ->countAllResults();

        $jumlahBeasiswa = (int) $this->db->table('taruna_penerima_beasiswa')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('status_validasi', 'tervalidasi')
            ->where('status_penetapan', 'ditetapkan')
            ->countAllResults();

        $totalAktifAlumni = (int) $this->db->table('alumni_profil al')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna')
            ->where('al.status_profil', 'aktif')
            ->where('m.status_terkini', 'lulus')
            ->countAllResults();

        return [
            'alumni'             => $row,
            'riwayat_perubahan'  => $riwayatPerubahan,
            'pending_koreksi'    => $pendingKoreksi,
            'riwayat_status'     => $riwayatStatusTaruna,
            'ringkas_rekam'      => [
                'prestasi' => $jumlahPrestasi,
                'beasiswa' => $jumlahBeasiswa,
            ],
            'dampak_proyeksi'    => [
                'alumni_aktif_saat_ini'   => $totalAktifAlumni,
                'alumni_aktif_jika_batal' => max(0, $totalAktifAlumni - (($row['status_profil'] ?? '') === 'aktif' ? 1 : 0)),
            ],
        ];
    }

    /**
     * Memperbarui kontak, domisili, dan pekerjaan alumni dengan pencatatan sumber konfirmasi & riwayat (ALM-02).
     *
     * @param array<string, mixed> $input
     * @return array{success: bool, message: string}
     */
    public function updateKontak(string $nomorTaruna, array $input, string $actorName, string $actorRole): array
    {
        $nomorTaruna = trim($nomorTaruna);
        $existing = $this->db->table('alumni_profil')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$existing) {
            return ['success' => false, 'message' => 'Profil dasar alumni tidak ditemukan.'];
        }
        if (($existing['status_profil'] ?? 'aktif') !== 'aktif') {
            return ['success' => false, 'message' => 'Profil alumni sedang nonaktif karena koreksi dan tidak dapat diperbarui.'];
        }

        $sumberCatalog    = ketarunaan_alumni_sumber_konfirmasi_catalog();
        $sumberKonfirmasi = trim((string) ($input['sumber_konfirmasi'] ?? ''));
        if (!array_key_exists($sumberKonfirmasi, $sumberCatalog)) {
            return ['success' => false, 'message' => 'Pilih sumber konfirmasi pembaruan yang sah.'];
        }

        $tanggalKejadian = trim((string) ($input['tanggal_kejadian'] ?? ''));
        if ($tanggalKejadian === '') {
            $tanggalKejadian = date('Y-m-d');
        } elseif (!$this->isValidDateYmd($tanggalKejadian)) {
            return ['success' => false, 'message' => 'Format tanggal konfirmasi tidak valid.'];
        }

        $pekerjaan = trim((string) ($input['pekerjaan_saat_ini'] ?? ($existing['pekerjaan_saat_ini'] ?? '')));
        $instansi  = trim((string) ($input['instansi_saat_ini'] ?? ($existing['instansi_saat_ini'] ?? '')));
        $email     = trim((string) ($input['email_alumni'] ?? ($existing['email_alumni'] ?? '')));
        $telepon   = trim((string) ($input['telepon_alumni'] ?? ($existing['telepon_alumni'] ?? '')));
        $kota      = trim((string) ($input['kota_domisili'] ?? ($existing['kota_domisili'] ?? '')));
        $alamat    = trim((string) ($input['alamat_domisili'] ?? ($existing['alamat_domisili'] ?? '')));
        $catatan   = trim((string) ($input['catatan_pembaruan'] ?? ''));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format alamat email alumni tidak valid.'];
        }
        if ($pekerjaan === '' && $instansi === '' && $email === '' && $telepon === '' && $kota === '') {
            return ['success' => false, 'message' => 'Isi minimal satu kolom kontak, domisili, atau pekerjaan.'];
        }

        $kontakTerakhir = $telepon !== '' ? $telepon : ($email !== '' ? $email : (string) ($existing['kontak_terakhir'] ?? ''));
        $now            = date('Y-m-d H:i:s');

        $beforeSnapshot = [
            'pekerjaan_saat_ini' => $existing['pekerjaan_saat_ini'] ?? '',
            'instansi_saat_ini'  => $existing['instansi_saat_ini'] ?? '',
            'email_alumni'       => $existing['email_alumni'] ?? '',
            'telepon_alumni'     => $existing['telepon_alumni'] ?? '',
            'kota_domisili'      => $existing['kota_domisili'] ?? '',
            'alamat_domisili'    => $existing['alamat_domisili'] ?? '',
            'sumber_konfirmasi'  => $existing['sumber_konfirmasi'] ?? '',
        ];

        $updatePayload = [
            'pekerjaan_saat_ini'       => $pekerjaan !== '' ? $pekerjaan : null,
            'instansi_saat_ini'        => $instansi !== '' ? $instansi : null,
            'email_alumni'             => $email !== '' ? $email : null,
            'telepon_alumni'           => $telepon !== '' ? $telepon : null,
            'kontak_terakhir'          => $kontakTerakhir !== '' ? $kontakTerakhir : null,
            'kota_domisili'            => $kota !== '' ? $kota : null,
            'alamat_domisili'          => $alamat !== '' ? $alamat : null,
            'sumber_konfirmasi'        => $sumberKonfirmasi,
            'tanggal_pembaruan_kontak' => $tanggalKejadian,
            'dicatat_oleh'             => $actorName,
            'updated_at'               => $now,
        ];

        $afterSnapshot = [
            'pekerjaan_saat_ini' => $pekerjaan,
            'instansi_saat_ini'  => $instansi,
            'email_alumni'       => $email,
            'telepon_alumni'     => $telepon,
            'kota_domisili'      => $kota,
            'alamat_domisili'    => $alamat,
            'sumber_konfirmasi'  => $sumberKonfirmasi,
        ];

        $judulInstansi = $instansi !== '' ? ' (' . $instansi . ')' : '';

        $this->db->transStart();
        $this->db->table('alumni_profil')
            ->where('nomor_taruna', $nomorTaruna)
            ->update($updatePayload);

        $this->db->table('alumni_riwayat_perubahan')->insert([
            'nomor_taruna'            => $nomorTaruna,
            'jenis_kejadian'          => 'pembaruan_kontak',
            'judul_ringkas'           => 'Pembaruan kontak & pekerjaan alumni' . $judulInstansi,
            'data_sebelum_json'       => json_encode($beforeSnapshot, JSON_UNESCAPED_UNICODE),
            'data_sesudah_json'       => json_encode($afterSnapshot, JSON_UNESCAPED_UNICODE),
            'sumber_konfirmasi'       => $sumberKonfirmasi,
            'tanggal_kejadian'        => $tanggalKejadian,
            'alasan_perubahan'        => $catatan !== '' ? $catatan : 'Pembaruan data kontak dan profil dasar alumni.',
            'dampak_ringkasan'        => 'Sumber konfirmasi: ' . ($sumberCatalog[$sumberKonfirmasi] ?? $sumberKonfirmasi),
            'status_validasi'         => 'tervalidasi',
            'catatan_validator'       => null,
            'diajukan_oleh'           => $actorName,
            'peran_pencatat'          => $actorRole,
            'divalidasi_oleh'         => $actorName,
            'tanggal_validasi'        => date('Y-m-d'),
            'created_at'              => $now,
            'updated_at'              => $now,
        ]);
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan pembaruan kontak alumni. Silakan coba lagi.'];
        }

        log_audit('UPDATE', 'alumni_profil', $nomorTaruna, $beforeSnapshot, $afterSnapshot);

        return ['success' => true, 'message' => 'Kontak dan pekerjaan alumni berhasil diperbarui.'];
    }

    /**
     * Mengambil antrean pengajuan kelulusan yang menunggu validasi beserta kandidat taruna aktif (ALM-01, UAT-04, UAT-05).
     *
     * @return array<string, mixed>
     */
    public function getKelulusanQueue(): array
    {
        $pending = $this->db->table('riwayat_status_taruna rst')
            ->select('
                rst.*,
                m.nama_lengkap,
                m.status_terkini,
                pr.kode_prodi,
                pr.nama_prodi,
                an.tahun_masuk
            ')
            ->join('master_taruna m', 'm.nomor_taruna = rst.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = rst.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan an', 'an.id = pt.angkatan_id', 'left')
            ->where('rst.kode_status', 'lulus')
            ->whereIn('rst.status_validasi', ['diajukan', 'perlu_perbaikan'])
            ->orderBy('rst.id', 'DESC')
            ->get()
            ->getResultArray();

        $pendingNomors = array_column($pending, 'nomor_taruna');

        $kandidatBuilder = $this->db->table('master_taruna m')
            ->select('
                m.nomor_taruna,
                m.nama_lengkap,
                m.status_terkini,
                pt.program_studi_id,
                pt.angkatan_id,
                pt.kelas_id,
                pr.kode_prodi,
                an.tahun_masuk,
                an.kode_romawi,
                k.nama_kelas,
                pt.tingkat
            ')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan an', 'an.id = pt.angkatan_id', 'left')
            ->join('ref_kelas k', 'k.id = pt.kelas_id', 'left')
            ->whereIn('m.status_terkini', ['aktif', 'cuti', 'skorsing']);

        if (!empty($pendingNomors)) {
            $kandidatBuilder->whereNotIn('m.nomor_taruna', $pendingNomors);
        }

        $kandidatTaruna = $kandidatBuilder
            ->orderBy('pt.tingkat', 'DESC')
            ->orderBy('an.tahun_masuk', 'ASC')
            ->orderBy('pr.kode_prodi', 'ASC')
            ->orderBy('m.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $prodiRefs = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $angkatanRefs = $this->db->table('ref_angkatan')
            ->select('id, tahun_masuk, kode_romawi')
            ->where('is_aktif', 1)
            ->orderBy('tahun_masuk', 'DESC')
            ->get()
            ->getResultArray();

        $recentValidated = $this->db->table('riwayat_status_taruna rst')
            ->select('
                rst.*,
                m.nama_lengkap,
                pr.kode_prodi,
                al.status_profil
            ')
            ->join('master_taruna m', 'm.nomor_taruna = rst.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = rst.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->join('alumni_profil al', 'al.nomor_taruna = rst.nomor_taruna', 'left')
            ->where('rst.kode_status', 'lulus')
            ->where('rst.status_validasi', 'tervalidasi')
            ->orderBy('rst.id', 'DESC')
            ->limit(12)
            ->get()
            ->getResultArray();

        $jumlahBerhenti = (int) $this->db->table('master_taruna')
            ->whereIn('status_terkini', ['mengundurkan_diri', 'drop_out'])
            ->countAllResults();

        $berhentiDiAlumni = (int) $this->db->table('alumni_profil al')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna')
            ->whereIn('m.status_terkini', ['mengundurkan_diri', 'drop_out'])
            ->where('al.status_profil', 'aktif')
            ->countAllResults();

        return [
            'pending'           => $pending,
            'kandidat_taruna'   => $kandidatTaruna,
            'prodi_refs'        => $prodiRefs,
            'angkatan_refs'     => $angkatanRefs,
            'recent_validated'  => $recentValidated,
            'jumlah_berhenti'   => $jumlahBerhenti,
            'berhenti_di_alumni'=> $berhentiDiAlumni,
        ];
    }

    /**
     * Mengajukan status kelulusan taruna tanpa langsung membuat profil alumni sebelum divalidasi (UAT-04, UAT-05).
     *
     * @param array<string, mixed> $input
     * @return array{success: bool, message: string, riwayat_id?: int}
     */
    public function ajukanKelulusan(array $input, string $actorName): array
    {
        $nomorTaruna = trim((string) ($input['nomor_taruna'] ?? ''));
        $tglLulus    = trim((string) ($input['tanggal_lulus'] ?? date('Y-m-d')));
        $noSk        = trim((string) ($input['nomor_keputusan'] ?? ''));
        $alasan      = trim((string) ($input['alasan_perubahan'] ?? ''));

        if ($nomorTaruna === '' || $noSk === '' || !$this->isValidDateYmd($tglLulus)) {
            return ['success' => false, 'message' => 'Pilih taruna, tanggal lulus yang valid, dan nomor SK kelulusan dengan lengkap.'];
        }

        $taruna = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$taruna) {
            return ['success' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        if (in_array($taruna['status_terkini'], ['mengundurkan_diri', 'drop_out'], true)) {
            return [
                'success' => false,
                'message' => 'Taruna berstatus berhenti (' . ketarunaan_status_meta($taruna['status_terkini'])['label'] . ') tidak dapat membentuk profil alumni.',
            ];
        }

        $existingAlumni = $this->db->table('alumni_profil')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if ($existingAlumni && ($existingAlumni['status_profil'] ?? '') === 'aktif' && $taruna['status_terkini'] === 'lulus') {
            return ['success' => false, 'message' => 'Taruna sudah memiliki profil dasar alumni aktif.'];
        }

        $openProposal = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('kode_status', 'lulus')
            ->where('status_validasi', 'diajukan')
            ->countAllResults();

        if ($openProposal > 0) {
            return ['success' => false, 'message' => 'Pengajuan kelulusan untuk taruna tersebut masih menunggu validasi.'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();
        $this->db->table('riwayat_status_taruna')->insert([
            'nomor_taruna'      => $nomorTaruna,
            'kode_status'       => 'lulus',
            'mulai_berlaku'     => $tglLulus,
            'alasan_perubahan'  => $alasan !== '' ? $alasan : 'Pengajuan kelulusan oleh ' . $actorName,
            'nomor_keputusan'   => $noSk,
            'tanggal_keputusan' => $tglLulus,
            'status_validasi'   => 'diajukan',
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);
        $insertId = (int) $this->db->insertID();
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal mengajukan kelulusan. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'riwayat_status_taruna', (string) $insertId, null, [
            'nomor_taruna'    => $nomorTaruna,
            'kode_status'     => 'lulus',
            'status_validasi' => 'diajukan',
        ]);

        return [
            'success'    => true,
            'message'    => 'Pengajuan kelulusan disimpan ke antrean validasi. Profil alumni baru terbentuk setelah disetujui.',
            'riwayat_id' => $insertId,
        ];
    }

    /**
     * Mengajukan kelulusan massal (batch) dengan 1 SK Kelulusan untuk banyak taruna sekaligus.
     *
     * @param array<int, mixed>    $nomorTarunas
     * @param array<string, mixed> $input
     * @return array{success: bool, message: string, submitted_count?: int, skipped_count?: int}
     */
    public function ajukanKelulusanBatch(array $nomorTarunas, array $input, string $actorName): array
    {
        $cleanNomors = array_values(array_unique(array_filter(array_map(
            static fn ($v): string => trim((string) $v),
            $nomorTarunas
        ), static fn (string $v): bool => $v !== '')));

        $tglLulus = trim((string) ($input['tanggal_lulus'] ?? date('Y-m-d')));
        $noSk     = trim((string) ($input['nomor_keputusan'] ?? ''));
        $alasan   = trim((string) ($input['alasan_perubahan'] ?? ''));

        if (empty($cleanNomors)) {
            return ['success' => false, 'message' => 'Pilih minimal 1 taruna untuk pengajuan kelulusan massal.'];
        }
        if ($noSk === '' || !$this->isValidDateYmd($tglLulus)) {
            return ['success' => false, 'message' => 'Tanggal lulus yang valid dan nomor SK kelulusan wajib diisi.'];
        }

        $tarunaRows = $this->db->table('master_taruna')
            ->select('nomor_taruna, status_terkini')
            ->whereIn('nomor_taruna', $cleanNomors)
            ->get()
            ->getResultArray();
        $tarunaMap = [];
        foreach ($tarunaRows as $tr) {
            $tarunaMap[(string) $tr['nomor_taruna']] = $tr;
        }

        $activeAlumniRows = $this->db->table('alumni_profil')
            ->select('nomor_taruna, status_profil')
            ->whereIn('nomor_taruna', $cleanNomors)
            ->where('status_profil', 'aktif')
            ->get()
            ->getResultArray();
        $activeAlumniSet = array_flip(array_column($activeAlumniRows, 'nomor_taruna'));

        $openProposalRows = $this->db->table('riwayat_status_taruna')
            ->select('nomor_taruna')
            ->whereIn('nomor_taruna', $cleanNomors)
            ->where('kode_status', 'lulus')
            ->where('status_validasi', 'diajukan')
            ->get()
            ->getResultArray();
        $openProposalSet = array_flip(array_column($openProposalRows, 'nomor_taruna'));

        $now        = date('Y-m-d H:i:s');
        $alasanText = $alasan !== '' ? $alasan : 'Pengajuan kelulusan massal oleh ' . $actorName;
        $toInsert   = [];
        $skipped    = 0;

        foreach ($cleanNomors as $nt) {
            $tr = $tarunaMap[$nt] ?? null;
            if (!$tr || in_array($tr['status_terkini'], ['mengundurkan_diri', 'drop_out'], true)) {
                $skipped++;
                continue;
            }
            if (isset($activeAlumniSet[$nt]) && $tr['status_terkini'] === 'lulus') {
                $skipped++;
                continue;
            }
            if (isset($openProposalSet[$nt])) {
                $skipped++;
                continue;
            }
            $toInsert[] = [
                'nomor_taruna'      => $nt,
                'kode_status'       => 'lulus',
                'mulai_berlaku'     => $tglLulus,
                'alasan_perubahan'  => $alasanText,
                'nomor_keputusan'   => $noSk,
                'tanggal_keputusan' => $tglLulus,
                'status_validasi'   => 'diajukan',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        if (empty($toInsert)) {
            return ['success' => false, 'message' => 'Tidak ada taruna terpilih yang memenuhi syarat pengajuan kelulusan.'];
        }

        $this->db->transStart();
        $this->db->table('riwayat_status_taruna')->insertBatch($toInsert);
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan pengajuan kelulusan massal. Silakan coba lagi.'];
        }

        $submittedCount = count($toInsert);
        log_audit('CREATE', 'riwayat_status_taruna', 'batch', null, [
            'jumlah'          => $submittedCount,
            'dilewati'        => $skipped,
            'nomor_keputusan' => $noSk,
            'tanggal_lulus'   => $tglLulus,
        ]);

        $msg = $submittedCount . ' taruna berhasil diajukan ke antrean validasi kelulusan.';
        if ($skipped > 0) {
            $msg .= ' (' . $skipped . ' taruna dilewati karena tidak memenuhi syarat atau sudah diajukan).';
        }

        return [
            'success'         => true,
            'message'         => $msg,
            'submitted_count' => $submittedCount,
            'skipped_count'   => $skipped,
        ];
    }

    /**
     * Memutuskan validasi status kelulusan dan membentuk profil dasar alumni secara atomik (ALM-01, UAT-04, UAT-05).
     *
     * @return array{success: bool, message: string}
     */
    public function validasiKelulusan(int $riwayatId, string $decision, string $catatan, string $validatorName): array
    {
        if (!in_array($decision, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['success' => false, 'message' => 'Keputusan validasi kelulusan tidak dikenal.'];
        }

        $riwayat = $this->db->table('riwayat_status_taruna')->where('id', $riwayatId)->get()->getRowArray();
        if (!$riwayat) {
            return ['success' => false, 'message' => 'Catatan pengajuan status tidak ditemukan.'];
        }

        if (strtolower((string) $riwayat['kode_status']) !== 'lulus') {
            return ['success' => false, 'message' => 'Status berhenti atau non-kelulusan dilarang membentuk profil dasar alumni.'];
        }

        if (($riwayat['status_validasi'] ?? '') === 'tervalidasi') {
            return ['success' => false, 'message' => 'Pengajuan kelulusan ini sudah divalidasi sebelumnya.'];
        }

        $nomorTaruna = (string) $riwayat['nomor_taruna'];
        $taruna      = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$taruna) {
            return ['success' => false, 'message' => 'Data induk taruna tidak ditemukan.'];
        }

        if (in_array($taruna['status_terkini'], ['mengundurkan_diri', 'drop_out'], true)) {
            return ['success' => false, 'message' => 'Taruna berstatus berhenti tidak dapat divalidasi menjadi alumni.'];
        }

        $catatanTrim = trim($catatan);
        if ($decision === 'perlu_perbaikan' && $catatanTrim === '') {
            return ['success' => false, 'message' => 'Alasan pengembalian pengajuan kelulusan wajib diisi.'];
        }

        $now = date('Y-m-d H:i:s');

        $this->db->transStart();
        if ($decision === 'perlu_perbaikan') {
            $alasanLama = trim((string) ($riwayat['alasan_perubahan'] ?? ''));
            $alasanBaru = ($alasanLama !== '' ? $alasanLama . ' | ' : '') . 'Catatan validator (' . $validatorName . '): ' . $catatanTrim;
            $this->db->table('riwayat_status_taruna')
                ->where('id', $riwayatId)
                ->update([
                    'status_validasi'  => 'perlu_perbaikan',
                    'alasan_perubahan' => $alasanBaru,
                    'updated_at'       => $now,
                ]);
        } else {
            $tanggalLulus = (string) ($riwayat['mulai_berlaku'] ?: date('Y-m-d'));
            $tahunLulus   = (int) substr($tanggalLulus, 0, 4);
            $noSk         = trim((string) ($riwayat['nomor_keputusan'] ?? 'SK.LULUS/PKTJ/' . $tahunLulus . '/' . $nomorTaruna));

            // Tutup riwayat status sebelumnya yang masih aktif
            $this->db->table('riwayat_status_taruna')
                ->where('nomor_taruna', $nomorTaruna)
                ->where('id !=', $riwayatId)
                ->where('status_validasi', 'tervalidasi')
                ->where('akhir_berlaku IS NULL')
                ->update([
                    'akhir_berlaku' => $tanggalLulus,
                    'updated_at'    => $now,
                ]);

            $this->db->table('riwayat_status_taruna')
                ->where('id', $riwayatId)
                ->update([
                    'status_validasi' => 'tervalidasi',
                    'updated_at'      => $now,
                ]);

            $this->db->table('master_taruna')
                ->where('nomor_taruna', $nomorTaruna)
                ->update([
                    'status_terkini' => 'lulus',
                    'updated_at'     => $now,
                ]);

            $existingAlumni = $this->db->table('alumni_profil')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
            $alumniData     = [
                'tanggal_lulus'            => $tanggalLulus,
                'nomor_keputusan_lulus'    => $noSk,
                'tahun_lulus'              => $tahunLulus,
                'status_profil'            => 'aktif',
                'sumber_konfirmasi'        => 'yudisium_akademik',
                'tanggal_pembaruan_kontak' => date('Y-m-d'),
                'dicatat_oleh'             => $validatorName,
                'divalidasi_oleh'          => $validatorName,
                'tanggal_validasi'         => date('Y-m-d'),
                'catatan_profil'           => $catatanTrim !== '' ? $catatanTrim : 'Terbentuk dari kelulusan tervalidasi.',
                'updated_at'               => $now,
            ];

            if ($existingAlumni) {
                $this->db->table('alumni_profil')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->update($alumniData);
            } else {
                $alumniData['nomor_taruna']    = $nomorTaruna;
                $alumniData['kontak_terakhir'] = $taruna['nomor_hp'] ?: ($taruna['email'] ?: null);
                $alumniData['email_alumni']    = $taruna['email'] ?: null;
                $alumniData['telepon_alumni']  = $taruna['nomor_hp'] ?: null;
                $alumniData['alamat_domisili'] = $taruna['alamat_domisili'] ?: null;
                $alumniData['created_at']      = $now;
                $this->db->table('alumni_profil')->insert($alumniData);
            }

            $this->db->table('alumni_riwayat_perubahan')->insert([
                'nomor_taruna'            => $nomorTaruna,
                'jenis_kejadian'          => 'kelulusan_awal',
                'judul_ringkas'           => 'Penetapan profil dasar alumni dari status lulus tervalidasi',
                'data_sebelum_json'       => json_encode(['status_terkini' => $taruna['status_terkini']], JSON_UNESCAPED_UNICODE),
                'data_sesudah_json'       => json_encode([
                    'status_terkini'        => 'lulus',
                    'tanggal_lulus'         => $tanggalLulus,
                    'nomor_keputusan_lulus' => $noSk,
                    'status_profil'         => 'aktif',
                ], JSON_UNESCAPED_UNICODE),
                'tanggal_lulus_baru'      => $tanggalLulus,
                'nomor_keputusan_baru'    => $noSk,
                'sumber_konfirmasi'       => 'yudisium_akademik',
                'tanggal_kejadian'        => date('Y-m-d'),
                'alasan_perubahan'        => $catatanTrim !== '' ? $catatanTrim : 'Validasi kelulusan resmi disetujui.',
                'dampak_ringkasan'        => 'Terbentuk 1 profil dasar alumni aktif (+1 Lulusan Dashboard).',
                'status_validasi'         => 'tervalidasi',
                'catatan_validator'       => $catatanTrim !== '' ? $catatanTrim : 'Disetujui sesuai SK kelulusan.',
                'diajukan_oleh'           => $validatorName,
                'peran_pencatat'          => 'validator',
                'divalidasi_oleh'         => $validatorName,
                'tanggal_validasi'        => date('Y-m-d'),
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal memproses keputusan validasi kelulusan.'];
        }

        if ($decision === 'perlu_perbaikan') {
            log_audit('UPDATE', 'riwayat_status_taruna', (string) $riwayatId, $riwayat, ['status_validasi' => 'perlu_perbaikan']);
            return ['success' => true, 'message' => 'Pengajuan kelulusan dikembalikan untuk perbaikan; profil alumni tidak dibentuk.'];
        }

        log_audit('APPROVE', 'alumni_profil', $nomorTaruna, ['status_terkini' => $taruna['status_terkini']], [
            'riwayat_id'    => $riwayatId,
            'status_profil' => 'aktif',
            'validator'     => $validatorName,
        ]);

        return ['success' => true, 'message' => 'Kelulusan divalidasi dan profil dasar alumni aktif berhasil dibentuk.'];
    }

    /**
     * Memutuskan validasi kelulusan secara massal (batch) dalam satu transaksi atomik.
     *
     * @param array<int, mixed> $riwayatIds
     * @return array{success: bool, message: string}
     */
    public function validasiKelulusanBatch(array $riwayatIds, string $decision, string $catatan, string $validatorName): array
    {
        if (!in_array($decision, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return ['success' => false, 'message' => 'Keputusan validasi kelulusan tidak dikenal.'];
        }

        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $riwayatIds), static fn (int $id): bool => $id > 0)));
        if (empty($cleanIds)) {
            return ['success' => false, 'message' => 'Pilih minimal 1 pengajuan kelulusan pada antrean.'];
        }

        $catatanTrim = trim($catatan);
        if ($decision === 'perlu_perbaikan' && $catatanTrim === '') {
            return ['success' => false, 'message' => 'Alasan pengembalian pengajuan kelulusan wajib diisi.'];
        }

        $processed = 0;
        $skipped   = 0;

        $this->db->transStart();
        foreach ($cleanIds as $rid) {
            $res = $this->validasiKelulusan($rid, $decision, $catatanTrim, $validatorName);
            if (!empty($res['success'])) {
                $processed++;
            } else {
                $skipped++;
            }
        }
        $this->db->transComplete();

        if (!$this->db->transStatus() || $processed === 0) {
            return ['success' => false, 'message' => 'Tidak ada pengajuan kelulusan yang berhasil diproses.'];
        }

        log_audit($decision === 'tervalidasi' ? 'APPROVE' : 'UPDATE', 'alumni_profil', 'batch', null, [
            'decision'  => $decision,
            'processed' => $processed,
            'skipped'   => $skipped,
            'validator' => $validatorName,
        ]);

        $msg = $decision === 'tervalidasi'
            ? $processed . ' kelulusan disetujui dan profil dasar alumni aktif berhasil dibentuk.'
            : $processed . ' pengajuan kelulusan dikembalikan untuk perbaikan.';
        if ($skipped > 0) {
            $msg .= ' (' . $skipped . ' entri dilewati).';
        }

        return ['success' => true, 'message' => $msg];
    }

    /**
     * Mengambil antrean usulan koreksi kelulusan serta riwayat koreksi sebelumnya (ALM-03, UAT-17).
     *
     * @return array<string, mixed>
     */
    public function getKoreksiQueue(): array
    {
        $pending = $this->db->table('alumni_riwayat_perubahan arp')
            ->select('
                arp.*,
                m.nama_lengkap,
                m.status_terkini,
                al.tanggal_lulus AS tanggal_lulus_saat_ini,
                al.nomor_keputusan_lulus AS sk_saat_ini,
                al.status_profil,
                pr.kode_prodi
            ')
            ->join('master_taruna m', 'm.nomor_taruna = arp.nomor_taruna', 'inner')
            ->join('alumni_profil al', 'al.nomor_taruna = arp.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = arp.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->where('arp.status_validasi', 'diajukan')
            ->whereIn('arp.jenis_kejadian', ['ralat_sk', 'batal_lulus'])
            ->orderBy('arp.id', 'DESC')
            ->get()
            ->getResultArray();

        $riwayat = $this->db->table('alumni_riwayat_perubahan arp')
            ->select('
                arp.*,
                m.nama_lengkap,
                pr.kode_prodi,
                al.status_profil
            ')
            ->join('master_taruna m', 'm.nomor_taruna = arp.nomor_taruna', 'inner')
            ->join('alumni_profil al', 'al.nomor_taruna = arp.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = arp.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->whereIn('arp.status_validasi', ['tervalidasi', 'ditolak'])
            ->whereIn('arp.jenis_kejadian', ['ralat_sk', 'batal_lulus'])
            ->orderBy('arp.id', 'DESC')
            ->limit(25)
            ->get()
            ->getResultArray();

        $activeAlumni = $this->db->table('alumni_profil al')
            ->select('al.nomor_taruna, m.nama_lengkap, pr.kode_prodi, al.tanggal_lulus, al.nomor_keputusan_lulus')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = al.nomor_taruna', 'left')
            ->join('ref_program_studi pr', 'pr.id = pt.program_studi_id', 'left')
            ->where('al.status_profil', 'aktif')
            ->where('m.status_terkini', 'lulus')
            ->orderBy('m.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        $jumlahAlumniAktif = (int) $this->db->table('alumni_profil al')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna')
            ->where('al.status_profil', 'aktif')
            ->where('m.status_terkini', 'lulus')
            ->countAllResults();

        return [
            'pending'             => $pending,
            'riwayat'             => $riwayat,
            'active_alumni'       => $activeAlumni,
            'jumlah_alumni_aktif' => $jumlahAlumniAktif,
        ];
    }

    /**
     * Mengajukan usulan koreksi kelulusan (ralat SK atau pembatalan kelulusan) tanpa langsung menimpa data aktif (ALM-03, UAT-17).
     *
     * @param array<string, mixed> $input
     * @return array{success: bool, message: string, koreksi_id?: int}
     */
    public function ajukanKoreksi(array $input, string $actorName, string $actorRole): array
    {
        $nomorTaruna = trim((string) ($input['nomor_taruna'] ?? ''));
        $jenis       = trim((string) ($input['jenis_kejadian'] ?? 'ralat_sk'));
        $alasan      = trim((string) ($input['alasan_perubahan'] ?? ''));
        $tglKejadian = trim((string) ($input['tanggal_kejadian'] ?? date('Y-m-d')));

        if (!in_array($jenis, ['ralat_sk', 'batal_lulus'], true)) {
            return ['success' => false, 'message' => 'Pilih jenis koreksi kelulusan yang sah.'];
        }
        if ($nomorTaruna === '' || mb_strlen($alasan) < 5) {
            return ['success' => false, 'message' => 'Pilih alumni dan isi alasan koreksi dengan jelas.'];
        }
        if ($tglKejadian === '') {
            $tglKejadian = date('Y-m-d');
        } elseif (!$this->isValidDateYmd($tglKejadian)) {
            return ['success' => false, 'message' => 'Format tanggal berlaku koreksi tidak valid.'];
        }

        $alumni = $this->db->table('alumni_profil al')
            ->select('al.*, m.nama_lengkap, m.status_terkini')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna', 'inner')
            ->where('al.nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$alumni || ($alumni['status_profil'] ?? '') !== 'aktif') {
            return ['success' => false, 'message' => 'Hanya profil alumni aktif yang dapat diajukan koreksi kelulusan.'];
        }

        $existingPending = $this->db->table('alumni_riwayat_perubahan')
            ->where('nomor_taruna', $nomorTaruna)
            ->where('status_validasi', 'diajukan')
            ->countAllResults();

        if ($existingPending > 0) {
            return ['success' => false, 'message' => 'Sudah ada usulan koreksi yang masih menunggu persetujuan untuk alumni ini.'];
        }

        $tglLulusBaru    = null;
        $skBaru          = trim((string) ($input['nomor_keputusan_baru'] ?? ''));
        $statusPengganti = null;
        $beforeJson      = [
            'status_terkini'        => $alumni['status_terkini'],
            'status_profil'         => $alumni['status_profil'],
            'tanggal_lulus'         => $alumni['tanggal_lulus'],
            'nomor_keputusan_lulus' => $alumni['nomor_keputusan_lulus'],
        ];

        if ($jenis === 'ralat_sk') {
            $tglLulusBaru = trim((string) ($input['tanggal_lulus_baru'] ?? ''));
            if (!$this->isValidDateYmd($tglLulusBaru) || $skBaru === '') {
                return ['success' => false, 'message' => 'Tanggal lulus baru yang valid dan nomor SK baru wajib diisi untuk ralat kelulusan.'];
            }
            $judulRingkas = 'Usulan ralat SK / tanggal kelulusan';
            $afterJson    = [
                'tanggal_lulus'         => $tglLulusBaru,
                'nomor_keputusan_lulus' => $skBaru,
                'status_profil'         => 'aktif',
            ];
            $dampakRingkasan = 'Jumlah alumni aktif tetap; nomor SK dan tanggal lulus diperbarui setelah disetujui.';
        } else {
            $statusPengganti = trim((string) ($input['status_taruna_pengganti'] ?? 'aktif'));
            $allowedStatus   = array_keys(array_diff_key(ketarunaan_status_catalog(), ['lulus' => true]));
            if (!in_array($statusPengganti, $allowedStatus, true)) {
                return ['success' => false, 'message' => 'Pilih status taruna pengganti yang valid.'];
            }
            $labelStatus     = ketarunaan_status_meta($statusPengganti)['label'];
            $judulRingkas    = 'Usulan pembatalan kelulusan -> ' . $labelStatus;
            $afterJson       = [
                'status_profil'  => 'nonaktif_koreksi',
                'status_terkini' => $statusPengganti,
                'nomor_sk_batal' => $skBaru !== '' ? $skBaru : null,
            ];
            $dampakRingkasan = 'Profil alumni akan dinonaktifkan (-1 alumni & -1 Lulusan Dashboard) dan status kembali ke ' . $labelStatus . '.';
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transStart();
        $this->db->table('alumni_riwayat_perubahan')->insert([
            'nomor_taruna'            => $nomorTaruna,
            'jenis_kejadian'          => $jenis,
            'judul_ringkas'           => $judulRingkas,
            'data_sebelum_json'       => json_encode($beforeJson, JSON_UNESCAPED_UNICODE),
            'data_sesudah_json'       => json_encode($afterJson, JSON_UNESCAPED_UNICODE),
            'tanggal_lulus_baru'      => $tglLulusBaru,
            'nomor_keputusan_baru'    => $skBaru !== '' ? $skBaru : null,
            'status_taruna_pengganti' => $statusPengganti,
            'sumber_konfirmasi'       => 'yudisium_akademik',
            'tanggal_kejadian'        => $tglKejadian,
            'alasan_perubahan'        => $alasan,
            'dampak_ringkasan'        => $dampakRingkasan,
            'status_validasi'         => 'diajukan',
            'diajukan_oleh'           => $actorName,
            'peran_pencatat'          => $actorRole,
            'created_at'              => $now,
            'updated_at'              => $now,
        ]);
        $koreksiId = (int) $this->db->insertID();
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal mengajukan koreksi kelulusan. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'alumni_riwayat_perubahan', (string) $koreksiId, $beforeJson, [
            'nomor_taruna'   => $nomorTaruna,
            'jenis_kejadian' => $jenis,
            'status'         => 'diajukan',
        ]);

        return [
            'success'    => true,
            'message'    => 'Usulan koreksi kelulusan diajukan ke validator. Data alumni aktif belum berubah sebelum disetujui.',
            'koreksi_id' => $koreksiId,
        ];
    }

    /**
     * Memutuskan usulan koreksi kelulusan (setujui atau tolak) dengan perubahan non-destruktif & sinkronisasi Dashboard (ALM-03, UAT-17).
     *
     * @return array{success: bool, message: string}
     */
    public function putuskanKoreksi(int $koreksiId, string $decision, string $catatanValidator, string $validatorName): array
    {
        if (!in_array($decision, ['tervalidasi', 'ditolak'], true)) {
            return ['success' => false, 'message' => 'Keputusan koreksi tidak dikenali.'];
        }

        $koreksi = $this->db->table('alumni_riwayat_perubahan')->where('id', $koreksiId)->get()->getRowArray();
        if (!$koreksi) {
            return ['success' => false, 'message' => 'Usulan koreksi kelulusan tidak ditemukan.'];
        }
        if (($koreksi['status_validasi'] ?? '') !== 'diajukan') {
            return ['success' => false, 'message' => 'Usulan koreksi ini sudah diputuskan sebelumnya.'];
        }

        $catatanTrim = trim($catatanValidator);
        if ($decision === 'ditolak' && $catatanTrim === '') {
            return ['success' => false, 'message' => 'Alasan penolakan koreksi wajib diisi.'];
        }

        $nomorTaruna = (string) $koreksi['nomor_taruna'];
        $alumni      = $this->db->table('alumni_profil')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        $taruna      = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();

        if (!$alumni || !$taruna) {
            return ['success' => false, 'message' => 'Data taruna atau profil alumni tidak ditemukan.'];
        }

        $now       = date('Y-m-d H:i:s');
        $today     = date('Y-m-d');
        $jenis     = (string) $koreksi['jenis_kejadian'];

        $this->db->transStart();
        if ($decision === 'ditolak') {
            $this->db->table('alumni_riwayat_perubahan')
                ->where('id', $koreksiId)
                ->update([
                    'status_validasi'   => 'ditolak',
                    'catatan_validator' => $catatanTrim,
                    'divalidasi_oleh'   => $validatorName,
                    'tanggal_validasi'  => $today,
                    'updated_at'        => $now,
                ]);
        } else {
            if ($jenis === 'ralat_sk') {
                $tglBaru = (string) ($koreksi['tanggal_lulus_baru'] ?: $alumni['tanggal_lulus']);
                $skBaru  = trim((string) ($koreksi['nomor_keputusan_baru'] ?: $alumni['nomor_keputusan_lulus']));
                $thnBaru = (int) substr($tglBaru, 0, 4);

                $this->db->table('alumni_profil')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->update([
                        'tanggal_lulus'         => $tglBaru,
                        'tahun_lulus'           => $thnBaru,
                        'nomor_keputusan_lulus' => $skBaru,
                        'divalidasi_oleh'       => $validatorName,
                        'tanggal_validasi'      => $today,
                        'catatan_profil'        => 'Ralat SK/tanggal kelulusan: ' . $koreksi['alasan_perubahan'],
                        'updated_at'            => $now,
                    ]);

                $latestLulus = $this->db->table('riwayat_status_taruna')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->where('kode_status', 'lulus')
                    ->where('status_validasi', 'tervalidasi')
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRowArray();

                if ($latestLulus) {
                    $this->db->table('riwayat_status_taruna')
                        ->where('id', (int) $latestLulus['id'])
                        ->update([
                            'mulai_berlaku'     => $tglBaru,
                            'nomor_keputusan'   => $skBaru,
                            'tanggal_keputusan' => $tglBaru,
                            'updated_at'        => $now,
                        ]);
                }
            } elseif ($jenis === 'batal_lulus') {
                $pengganti  = trim((string) ($koreksi['status_taruna_pengganti'] ?: 'aktif'));
                $tglBerlaku = (string) ($koreksi['tanggal_kejadian'] ?: $today);
                $skBatal    = trim((string) ($koreksi['nomor_keputusan_baru'] ?? 'KOREKSI/LULUS/' . $nomorTaruna));

                // Nonaktifkan profil alumni secara non-destruktif (riwayat tetap tersimpan)
                $this->db->table('alumni_profil')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->update([
                        'status_profil'    => 'nonaktif_koreksi',
                        'divalidasi_oleh'  => $validatorName,
                        'tanggal_validasi' => $today,
                        'catatan_profil'   => 'Kelulusan dibatalkan: ' . $koreksi['alasan_perubahan'],
                        'updated_at'       => $now,
                    ]);

                // Tutup status lulus & kembalikan status taruna terkini secara sinkron untuk Dashboard
                $this->db->table('riwayat_status_taruna')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->where('akhir_berlaku IS NULL')
                    ->where('status_validasi', 'tervalidasi')
                    ->update([
                        'akhir_berlaku' => $tglBerlaku,
                        'updated_at'    => $now,
                    ]);

                $this->db->table('riwayat_status_taruna')->insert([
                    'nomor_taruna'      => $nomorTaruna,
                    'kode_status'       => $pengganti,
                    'mulai_berlaku'     => $tglBerlaku,
                    'alasan_perubahan'  => 'Koreksi pembatalan kelulusan disetujui (' . $validatorName . '): ' . $koreksi['alasan_perubahan'],
                    'nomor_keputusan'   => $skBatal !== '' ? $skBatal : 'KOREKSI/' . $nomorTaruna,
                    'tanggal_keputusan' => $tglBerlaku,
                    'status_validasi'   => 'tervalidasi',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);

                $this->db->table('master_taruna')
                    ->where('nomor_taruna', $nomorTaruna)
                    ->update([
                        'status_terkini' => $pengganti,
                        'updated_at'     => $now,
                    ]);
            }

            $this->db->table('alumni_riwayat_perubahan')
                ->where('id', $koreksiId)
                ->update([
                    'status_validasi'   => 'tervalidasi',
                    'catatan_validator' => $catatanTrim !== '' ? $catatanTrim : 'Koreksi kelulusan disetujui.',
                    'divalidasi_oleh'   => $validatorName,
                    'tanggal_validasi'  => $today,
                    'updated_at'        => $now,
                ]);
        }
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal memproses keputusan koreksi kelulusan.'];
        }

        log_audit($decision === 'ditolak' ? 'REJECT' : 'APPROVE', 'alumni_riwayat_perubahan', (string) $koreksiId, $alumni, [
            'decision'      => $decision,
            'jenis'         => $jenis,
            'nomor_taruna'  => $nomorTaruna,
            'validator'     => $validatorName,
        ]);

        if ($decision === 'ditolak') {
            return ['success' => true, 'message' => 'Usulan koreksi ditolak; profil alumni aktif dan status lulus tetap berlaku.'];
        }

        return [
            'success' => true,
            'message' => $jenis === 'batal_lulus'
                ? 'Pembatalan kelulusan disetujui. Profil alumni dinonaktifkan (riwayat terjaga) dan status taruna disinkronkan.'
                : 'Ralat SK / tanggal kelulusan disetujui dan diselaraskan.',
        ];
    }

    /**
     * Menyiapkan baris CSV direktori alumni (profil dasar, tanpa NIK atau kontak wali sensitif).
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<int, string>>
     */
    public function getExportRows(array $filters = []): array
    {
        $directory = $this->getDirectoryData($filters, false, null, true);
        $sumberMap = ketarunaan_alumni_sumber_konfirmasi_catalog();

        $csv = [[
            'Nomor Taruna',
            'Nama Lengkap',
            'Program Studi',
            'Angkatan',
            'Tahun Lulus',
            'Tanggal Lulus',
            'Nomor Keputusan Lulus',
            'Pekerjaan Saat Ini',
            'Instansi / Perusahaan',
            'Kota Domisili',
            'Alamat Email Alumni',
            'Telepon Alumni',
            'Sumber Konfirmasi',
            'Status Profil',
        ]];

        foreach ($directory['rows'] as $row) {
            $sumberKey = (string) ($row['sumber_konfirmasi'] ?? '');
            $csv[]     = [
                $this->safeCsv((string) ($row['nomor_taruna'] ?? '')),
                $this->safeCsv((string) ($row['nama_lengkap'] ?? '')),
                $this->safeCsv((string) ($row['kode_prodi'] ?? '-') . ' - ' . (string) ($row['nama_prodi'] ?? '')),
                $this->safeCsv(ketarunaan_format_angkatan($row['tahun_masuk'] ?? null)),
                $this->safeCsv((string) ($row['tahun_lulus'] ?? '-')),
                $this->safeCsv(ketarunaan_format_tanggal($row['tanggal_lulus'] ?? null)),
                $this->safeCsv((string) ($row['nomor_keputusan_lulus'] ?? '-')),
                $this->safeCsv((string) ($row['pekerjaan_saat_ini'] ?? '-')),
                $this->safeCsv((string) ($row['instansi_saat_ini'] ?? '-')),
                $this->safeCsv((string) ($row['kota_domisili'] ?? '-')),
                $this->safeCsv((string) ($row['email_alumni'] ?? '-')),
                $this->safeCsv((string) ($row['telepon_alumni'] ?? ($row['kontak_terakhir'] ?? '-'))),
                $this->safeCsv($sumberMap[$sumberKey] ?? ($sumberKey !== '' ? $sumberKey : '-')),
                $this->safeCsv(ketarunaan_alumni_status_meta((string) ($row['status_profil'] ?? 'aktif'))['label']),
            ];
        }

        return $csv;
    }

    /**
     * Menghitung 4 metrik KPI ringkas untuk Modul Alumni.
     *
     * @return array<string, int>
     */
    private function buildKpiSummary(bool $isSelfOnly, ?string $selfNomor): array
    {
        $aktifBuilder = $this->db->table('alumni_profil al')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna')
            ->where('al.status_profil', 'aktif')
            ->where('m.status_terkini', 'lulus');

        $kerjaBuilder = $this->db->table('alumni_profil al')
            ->join('master_taruna m', 'm.nomor_taruna = al.nomor_taruna')
            ->where('al.status_profil', 'aktif')
            ->where('m.status_terkini', 'lulus')
            ->where('al.pekerjaan_saat_ini IS NOT NULL')
            ->where('al.pekerjaan_saat_ini !=', '');

        if ($isSelfOnly) {
            if ($selfNomor !== null && $selfNomor !== '') {
                $aktifBuilder->where('al.nomor_taruna', $selfNomor);
                $kerjaBuilder->where('al.nomor_taruna', $selfNomor);
            } else {
                $aktifBuilder->where('1 = 0');
                $kerjaBuilder->where('1 = 0');
            }
        }

        $totalAktif        = (int) $aktifBuilder->countAllResults();
        $mengisiPekerjaan  = (int) $kerjaBuilder->countAllResults();
        $menungguKelulusan = $isSelfOnly ? 0 : (int) $this->db->table('riwayat_status_taruna')
            ->where('kode_status', 'lulus')
            ->where('status_validasi', 'diajukan')
            ->countAllResults();
        $antreanKoreksi    = $isSelfOnly ? 0 : (int) $this->db->table('alumni_riwayat_perubahan')
            ->where('status_validasi', 'diajukan')
            ->countAllResults();

        return [
            'total_aktif'        => $totalAktif,
            'mengisi_pekerjaan'  => $mengisiPekerjaan,
            'menunggu_kelulusan' => $menungguKelulusan,
            'antrean_koreksi'    => $antreanKoreksi,
        ];
    }

    private function safeCsv(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $trimmed;
        }

        return $trimmed;
    }

    /**
     * Memvalidasi format tanggal ISO Y-m-d secara ketat (mencegah tanggal kalender tidak sah).
     */
    protected function isValidDateYmd(string $date): bool
    {
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        return $dt !== false && $dt->format('Y-m-d') === $date;
    }
}
