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

        $kelasList = $this->db->table('ref_kelas')
            ->where('is_aktif', 1)
            ->orderBy('nama_kelas', 'ASC')
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
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters = [], int $page = 1, int $perPage = 15): array
    {
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
                a.tahun_masuk,
                COALESCE(k.nama_kelas, "A") AS nama_kelas
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

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $totalPages = max(1, (int) ceil($totalFiltered / $perPage));

        $rows = $listBuilder
            ->orderBy("CASE WHEN m.nomor_taruna = '2401001' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('m.nomor_taruna', 'ASC')
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
                a.perkiraan_lulus,
                COALESCE(k.nama_kelas, "A") AS nama_kelas,
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
                COALESCE(k.nama_kelas, "A") AS nama_kelas,
                COALESCE(ta.kode_tahun, "2026/2027 Ganjil") AS kode_tahun
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
     * @return bool
     */
    public function updateIdentitas(string $nomorTaruna, array $payload, ?string $newFoto = null): bool
    {
        $existing = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$existing) {
            return false;
        }

        $updateData = [
            'nama_lengkap'   => trim((string) ($payload['nama_lengkap'] ?? $existing['nama_lengkap'])),
            'nama_panggilan' => trim((string) ($payload['nama_panggilan'] ?? '')),
            'jenis_kelamin'  => in_array(($payload['jenis_kelamin'] ?? 'L'), ['L', 'P'], true) ? $payload['jenis_kelamin'] : 'L',
            'agama'          => trim((string) ($payload['agama'] ?? 'Islam')),
            'tempat_lahir'   => trim((string) ($payload['tempat_lahir'] ?? '')),
            'tanggal_lahir'  => !empty($payload['tanggal_lahir']) ? $payload['tanggal_lahir'] : null,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        if (!empty($payload['nik']) && !str_contains((string) $payload['nik'], '*')) {
            $updateData['nik'] = preg_replace('/[^0-9]/', '', (string) $payload['nik']);
        }

        if ($newFoto !== null) {
            $updateData['foto'] = $newFoto;
        }

        $this->db->transStart();
        $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->update($updateData);
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            log_audit('UPDATE', 'master_taruna', $nomorTaruna, $existing, $updateData);
            return true;
        }

        return false;
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

        $data = [
            'program_studi_id' => (int) ($payload['program_studi_id'] ?? ($existing['program_studi_id'] ?? 1)),
            'angkatan_id'      => (int) ($payload['angkatan_id'] ?? ($existing['angkatan_id'] ?? 1)),
            'kelas_id'         => !empty($payload['kelas_id']) ? (int) $payload['kelas_id'] : null,
            'tingkat'          => max(1, min(4, (int) ($payload['tingkat'] ?? 1))),
            'semester_aktif'   => max(1, min(8, (int) ($payload['semester_aktif'] ?? 1))),
            'jalur_penerimaan' => trim((string) ($payload['jalur_penerimaan'] ?? 'Pola Pembibitan')),
            'asal_formasi'     => trim((string) ($payload['asal_formasi'] ?? '')),
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
     * Memperbarui data Tab 3: Kontak dan Keluarga.
     *
     * @param string $nomorTaruna
     * @param array  $payload
     * @return bool
     */
    public function updateKontak(string $nomorTaruna, array $payload): bool
    {
        $existing = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$existing) {
            return false;
        }

        $updateData = [
            'nomor_hp'        => trim((string) ($payload['nomor_hp'] ?? '')),
            'email'           => trim((string) ($payload['email'] ?? '')),
            'alamat_domisili' => trim((string) ($payload['alamat_domisili'] ?? '')),
            'alamat_asal'     => trim((string) ($payload['alamat_asal'] ?? '')),
            'nama_wali'       => trim((string) ($payload['nama_wali'] ?? '')),
            'hubungan_wali'   => trim((string) ($payload['hubungan_wali'] ?? '')),
            'nomor_hp_wali'   => trim((string) ($payload['nomor_hp_wali'] ?? '')),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->transStart();
        $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->update($updateData);
        $this->db->transComplete();

        if ($this->db->transStatus()) {
            log_audit('UPDATE', 'master_taruna', $nomorTaruna, null, ['section' => 'kontak_keluarga']);
            return true;
        }

        return false;
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

        $now = date('Y-m-d H:i:s');
        $kodeStatus = trim((string) ($payload['status_terkini'] ?? 'aktif'));
        if (!array_key_exists($kodeStatus, ketarunaan_status_catalog())) {
            $kodeStatus = 'aktif';
        }

        $this->db->transStart();

        $this->db->table('master_taruna')->insert([
            'nomor_taruna'    => $nomorTaruna,
            'nama_lengkap'    => trim((string) ($payload['nama_lengkap'] ?? '')),
            'nik'             => !empty($payload['nik']) ? preg_replace('/[^0-9]/', '', (string) $payload['nik']) : null,
            'nama_panggilan'  => trim((string) ($payload['nama_panggilan'] ?? '')),
            'jenis_kelamin'   => in_array(($payload['jenis_kelamin'] ?? 'L'), ['L', 'P'], true) ? $payload['jenis_kelamin'] : 'L',
            'tempat_lahir'    => trim((string) ($payload['tempat_lahir'] ?? 'Tegal')),
            'tanggal_lahir'   => !empty($payload['tanggal_lahir']) ? $payload['tanggal_lahir'] : null,
            'agama'           => trim((string) ($payload['agama'] ?? 'Islam')),
            'nomor_hp'        => trim((string) ($payload['nomor_hp'] ?? '')),
            'email'           => trim((string) ($payload['email'] ?? '')),
            'alamat_domisili' => 'Asrama Taruna PKTJ Tegal',
            'status_terkini'  => $kodeStatus,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $prodiId    = (int) ($payload['program_studi_id'] ?? 1);
        $angkatanId = (int) ($payload['angkatan_id'] ?? 1);
        $tingkat    = max(1, min(4, (int) ($payload['tingkat'] ?? 1)));
        $semester   = max(1, min(8, (int) ($payload['semester_aktif'] ?? 1)));

        $this->db->table('pendidikan_taruna')->insert([
            'nomor_taruna'     => $nomorTaruna,
            'program_studi_id' => $prodiId,
            'angkatan_id'      => $angkatanId,
            'tingkat'          => $tingkat,
            'semester_aktif'   => $semester,
            'jalur_penerimaan' => trim((string) ($payload['jalur_penerimaan'] ?? 'Pola Pembibitan')),
            'asal_formasi'     => trim((string) ($payload['asal_formasi'] ?? 'Kemenhub RI')),
            'created_at'       => $now,
            'updated_at'       => $now,
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
            'nomor_taruna'     => $nomorTaruna,
            'program_studi_id' => $prodiId,
            'tingkat'          => $tingkat,
            'semester'         => $semester,
            'mulai_berlaku'    => date('Y-m-d'),
            'created_at'       => $now,
        ]);

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
     * Menyimpan pengajuan atau draf perubahan status pendidikan taruna.
     *
     * @param string      $nomorTaruna
     * @param array       $payload
     * @param string|null $buktiFilename
     * @return array{success: bool, message: string}
     */
    public function ajukanPerubahanStatus(string $nomorTaruna, array $payload, ?string $buktiFilename = null): array
    {
        $taruna = $this->db->table('master_taruna')->where('nomor_taruna', $nomorTaruna)->get()->getRowArray();
        if (!$taruna) {
            return ['success' => false, 'message' => 'Data taruna tidak ditemukan.'];
        }

        $kodeStatusBaru = trim((string) ($payload['kode_status'] ?? ''));
        $catalog = ketarunaan_status_catalog();
        if (!array_key_exists($kodeStatusBaru, $catalog)) {
            return ['success' => false, 'message' => 'Pilih status baru yang sah.'];
        }

        $mulaiBerlaku = trim((string) ($payload['mulai_berlaku'] ?? ''));
        if ($mulaiBerlaku === '') {
            return ['success' => false, 'message' => 'Tanggal mulai berlaku wajib diisi.'];
        }

        $rencanaKembali = !empty($payload['rencana_kembali']) ? trim((string) $payload['rencana_kembali']) : null;
        if (in_array($kodeStatusBaru, ['cuti', 'skorsing'], true) && empty($rencanaKembali)) {
            return ['success' => false, 'message' => 'Tanggal rencana kembali wajib diisi untuk status Cuti atau Skorsing.'];
        }

        $aksiSubmit = ($payload['aksi_submit'] ?? 'ajukan') === 'draf' ? 'draf' : 'diajukan';
        $now = date('Y-m-d H:i:s');

        $insertData = [
            'nomor_taruna'      => $nomorTaruna,
            'kode_status'       => $kodeStatusBaru,
            'mulai_berlaku'     => $mulaiBerlaku,
            'akhir_berlaku'     => null,
            'rencana_kembali'   => $rencanaKembali,
            'alasan_perubahan'  => trim((string) ($payload['alasan_perubahan'] ?? '')),
            'nomor_keputusan'   => trim((string) ($payload['nomor_keputusan'] ?? '')),
            'tanggal_keputusan' => !empty($payload['tanggal_keputusan']) ? $payload['tanggal_keputusan'] : null,
            'bukti_dokumen'     => $buktiFilename,
            'status_validasi'   => $aksiSubmit,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        $this->db->transStart();
        $this->db->table('riwayat_status_taruna')->insert($insertData);
        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Gagal menyimpan perubahan status. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'riwayat_status_taruna', $nomorTaruna, null, [
            'kode_status'     => $kodeStatusBaru,
            'mulai_berlaku'   => $mulaiBerlaku,
            'status_validasi' => $aksiSubmit,
        ]);

        $msg = $aksiSubmit === 'draf'
            ? 'Draf perubahan status berhasil disimpan.'
            : 'Pengajuan perubahan status berhasil dikirim ke antrean validasi.';

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
        $kelasId      = !empty($payload['kelas_id']) ? (int) $payload['kelas_id'] : null;
        $tingkat      = max(1, min(4, (int) ($payload['tingkat'] ?? 1)));
        $semester     = max(1, min(8, (int) ($payload['semester'] ?? 1)));
        $thnAkademik  = !empty($payload['tahun_akademik_id']) ? (int) $payload['tahun_akademik_id'] : null;

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
     * Memeriksa baris-baris data impor CSV/Excel sebelum disimpan sebagai draf.
     *
     * @param array<int, array<string, mixed>> $rawRows
     * @param string                           $namaBerkas
     * @return array<string, mixed>
     */
    public function periksaBerkasImpor(array $rawRows, string $namaBerkas = 'data_taruna.csv'): array
    {
        $prodiMap = [];
        foreach ($this->db->table('ref_program_studi')->get()->getResultArray() as $p) {
            $prodiMap[strtoupper($p['kode_prodi'])] = (int) $p['id'];
        }

        $angkatanMap = [];
        foreach ($this->db->table('ref_angkatan')->get()->getResultArray() as $a) {
            $angkatanMap[(int) $a['tahun_masuk']] = (int) $a['id'];
        }

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
            $barisKe     = $idx + 2;
            $nomor       = trim((string) ($r['nomor_taruna'] ?? ''));
            $nama        = trim((string) ($r['nama_lengkap'] ?? ''));
            $kodeProdi   = strtoupper(trim((string) ($r['kode_prodi'] ?? 'RSTJ')));
            $tahunMasuk  = (int) ($r['tahun_masuk'] ?? 2026);
            $jk          = strtoupper(trim((string) ($r['jenis_kelamin'] ?? 'L')));
            $tempatLahir = trim((string) ($r['tempat_lahir'] ?? 'Tegal'));

            if ($nomor === '' || $nama === '') {
                $perluPerbaikan++;
                $previewRows[] = [
                    'baris'        => $barisKe,
                    'nomor_taruna' => $nomor !== '' ? $nomor : '-',
                    'nama_lengkap' => $nama !== '' ? $nama : '-',
                    'kode_prodi'   => $kodeProdi,
                    'tahun_masuk'  => $tahunMasuk,
                    'status_cek'   => 'tidak_valid',
                    'label_cek'    => 'Tidak valid',
                    'keterangan'   => $nomor === '' ? 'Nomor Taruna wajib diisi' : 'Nama Lengkap wajib diisi',
                ];
                continue;
            }

            if (!isset($prodiMap[$kodeProdi])) {
                $perluPerbaikan++;
                $previewRows[] = [
                    'baris'        => $barisKe,
                    'nomor_taruna' => $nomor,
                    'nama_lengkap' => $nama,
                    'kode_prodi'   => $kodeProdi,
                    'tahun_masuk'  => $tahunMasuk,
                    'status_cek'   => 'tidak_valid',
                    'label_cek'    => 'Tidak valid',
                    'keterangan'   => 'Kode Program Studi tidak dikenali (' . $kodeProdi . ')',
                ];
                continue;
            }

            if (isset($existingSet[$nomor]) || isset($seenInFile[$nomor])) {
                $duplikat++;
                $previewRows[] = [
                    'baris'        => $barisKe,
                    'nomor_taruna' => $nomor,
                    'nama_lengkap' => $nama,
                    'kode_prodi'   => $kodeProdi,
                    'tahun_masuk'  => $tahunMasuk,
                    'status_cek'   => 'duplikat',
                    'label_cek'    => 'Duplikat',
                    'keterangan'   => isset($existingSet[$nomor])
                        ? 'Nomor Taruna sudah terdaftar'
                        : 'Nomor Taruna ganda di dalam berkas',
                ];
                continue;
            }

            $seenInFile[$nomor] = true;
            $siapDiimpor++;

            $previewRows[] = [
                'baris'        => $barisKe,
                'nomor_taruna' => $nomor,
                'nama_lengkap' => $nama,
                'kode_prodi'   => $kodeProdi,
                'tahun_masuk'  => $tahunMasuk,
                'status_cek'   => 'valid',
                'label_cek'    => 'Valid',
                'keterangan'   => 'Siap diimpor',
            ];

            $validRows[] = [
                'nomor_taruna'     => $nomor,
                'nama_lengkap'     => $nama,
                'jenis_kelamin'    => in_array($jk, ['L', 'P'], true) ? $jk : 'L',
                'tempat_lahir'     => $tempatLahir !== '' ? $tempatLahir : 'Tegal',
                'program_studi_id' => $prodiMap[$kodeProdi],
                'angkatan_id'      => $angkatanMap[$tahunMasuk] ?? reset($angkatanMap),
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
     * Menyimpan baris hasil pemeriksaan impor yang valid sebagai draf.
     *
     * @param array<int, array<string, mixed>> $validRows
     * @return array{success: bool, imported: int, message: string}
     */
    public function simpanImporValid(array $validRows): array
    {
        if (empty($validRows)) {
            return ['success' => false, 'imported' => 0, 'message' => 'Tidak ada baris valid untuk diimpor.'];
        }

        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $imported = 0;

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

            $nama = trim((string) ($r['nama_lengkap'] ?? ''));
            $parts = explode(' ', $nama);

            $this->db->table('master_taruna')->insert([
                'nomor_taruna'    => $nomor,
                'nama_lengkap'    => $nama,
                'nama_panggilan'  => $parts[0] ?? $nama,
                'jenis_kelamin'   => $r['jenis_kelamin'] ?? 'L',
                'tempat_lahir'    => $r['tempat_lahir'] ?? 'Tegal',
                'agama'           => 'Islam',
                'alamat_domisili' => 'Asrama Taruna PKTJ Tegal',
                'status_terkini'  => 'aktif',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $this->db->table('pendidikan_taruna')->insert([
                'nomor_taruna'     => $nomor,
                'program_studi_id' => (int) ($r['program_studi_id'] ?? 1),
                'angkatan_id'      => (int) ($r['angkatan_id'] ?? 1),
                'tingkat'          => 1,
                'semester_aktif'   => 1,
                'jalur_penerimaan' => 'Pola Pembibitan',
                'asal_formasi'     => 'Kemenhub RI',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            $this->db->table('riwayat_status_taruna')->insert([
                'nomor_taruna'      => $nomor,
                'kode_status'       => 'aktif',
                'mulai_berlaku'     => $today,
                'alasan_perubahan'  => 'Data hasil impor massal (menunggu validasi)',
                'nomor_keputusan'   => 'IMPOR/' . date('Y'),
                'tanggal_keputusan' => $today,
                'status_validasi'   => 'draf',
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $imported++;
        }

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'imported' => 0, 'message' => 'Gagal menyimpan data impor. Silakan coba lagi.'];
        }

        log_audit('CREATE', 'master_taruna', 'BATCH_IMPOR', null, ['jumlah_baris' => $imported]);

        return [
            'success'  => true,
            'imported' => $imported,
            'message'  => $imported . ' baris data taruna berhasil disimpan sebagai draf.',
        ];
    }
}

