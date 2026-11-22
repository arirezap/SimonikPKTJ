<?php

namespace App\Libraries\Ketarunaan;

use CodeIgniter\Database\BaseConnection;

/**
 * Service agregasi statistik Dashboard ECC 2.0 (Ketarunaan & Alumni).
 * Mematuhi Single Responsibility Principle (SRP), Dependency Inversion (DIP),
 * dan bebas dari angka/tanggal statis yang di-hardcode.
 */
class DashboardService
{
    protected BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? \Config\Database::connect();
        helper('ketarunaan');
    }

    /**
     * Menghitung seluruh statistik ringkasan berdasarkan tanggal acuan, prodi, dan angkatan.
     *
     * @param string      $tanggalAcuan Format Y-m-d
     * @param int|null    $prodiId
     * @param int|null    $angkatanId
     * @return array<string, mixed>
     */
    public function getAggregatedStats(string $tanggalAcuan, ?int $prodiId = null, ?int $angkatanId = null): array
    {
        // 1. Posisi 6 Status Pendidikan Taruna berdasar riwayat tervalidasi hingga tanggal acuan (UAT-02)
        $asOfResult = $this->getStatusCountsAsOf($tanggalAcuan, $prodiId, $angkatanId);
        $statusCounts = $asOfResult['status_counts'];

        $dalamPendidikan    = $statusCounts['aktif'] + $statusCounts['skorsing'] + $statusCounts['cuti'];
        $berhentiPendidikan = $statusCounts['mengundurkan_diri'] + $statusCounts['drop_out'];
        $lulusan            = $statusCounts['lulus'];
        $totalRekaman       = $dalamPendidikan + $berhentiPendidikan + $lulusan;

        // 2. Prestasi Tervalidasi & Taruna Berprestasi Unik
        $builderPrestasi = $this->db->table('taruna_prestasi p')
            ->where('p.status_validasi', 'tervalidasi');

        $builderPeserta = $this->db->table('taruna_peserta_prestasi pp')
            ->join('taruna_prestasi p', 'p.id = pp.prestasi_id')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pp.nomor_taruna', 'left')
            ->where('p.status_validasi', 'tervalidasi');

        if ($prodiId !== null && $prodiId > 0) {
            $builderPeserta->where('pt.program_studi_id', $prodiId);
            $builderPrestasi->join('taruna_peserta_prestasi ppf', 'ppf.prestasi_id = p.id')
                ->join('pendidikan_taruna ptf', 'ptf.nomor_taruna = ppf.nomor_taruna')
                ->where('ptf.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builderPeserta->where('pt.angkatan_id', $angkatanId);
            if ($prodiId === null || $prodiId <= 0) {
                $builderPrestasi->join('taruna_peserta_prestasi ppf', 'ppf.prestasi_id = p.id')
                    ->join('pendidikan_taruna ptf', 'ptf.nomor_taruna = ppf.nomor_taruna');
            }
            $builderPrestasi->where('ptf.angkatan_id', $angkatanId);
        }

        $totalPrestasi     = (int) $builderPrestasi->select('p.id')->distinct()->countAllResults();
        $tarunaBerprestasi = (int) $builderPeserta->select('pp.nomor_taruna')->distinct()->countAllResults();

        // 3. Pelanggaran Terbukti (Selesai vs Proses)
        $pelanggaranStats = $this->hitungPelanggaran($prodiId, $angkatanId);

        // 4. Penerima Beasiswa & Program Aktif
        $builderBeasiswa = $this->db->table('taruna_penerima_beasiswa pb')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pb.nomor_taruna', 'left')
            ->where('pb.status_penetapan', 'ditetapkan')
            ->where('pb.status_validasi', 'tervalidasi');

        if ($prodiId !== null && $prodiId > 0) {
            $builderBeasiswa->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builderBeasiswa->where('pt.angkatan_id', $angkatanId);
        }

        $penerimaBeasiswa     = (int) $builderBeasiswa->countAllResults();
        $programBeasiswaAktif = (int) $this->db->table('taruna_program_beasiswa')
            ->where('status_program', 'berjalan')
            ->countAllResults();

        // 5. Lowongan Kerja Aktif & Segera Berakhir (<= 7 hari dari tanggal acuan)
        $batasSegeraBerakhir = date('Y-m-d 23:59:59', strtotime($tanggalAcuan . ' +7 days'));
        $awalTanggalAcuan    = date('Y-m-d 00:00:00', strtotime($tanggalAcuan));

        $builderLowongan = $this->db->table('lowongan_pekerjaan l')
            ->where('l.status_publikasi', 'terbit')
            ->where('l.status_validasi', 'tervalidasi')
            ->where('l.batas_pendaftaran >=', $awalTanggalAcuan);

        if ($prodiId !== null && $prodiId > 0) {
            $builderLowongan->join('lowongan_sasaran_prodi lsp', 'lsp.lowongan_id = l.id')
                ->where('lsp.program_studi_id', $prodiId);
        }
        $lowonganAktif = (int) $builderLowongan->select('l.id')->distinct()->countAllResults();

        $builderLowonganSegera = $this->db->table('lowongan_pekerjaan l')
            ->where('l.status_publikasi', 'terbit')
            ->where('l.status_validasi', 'tervalidasi')
            ->where('l.batas_pendaftaran >=', $awalTanggalAcuan)
            ->where('l.batas_pendaftaran <=', $batasSegeraBerakhir);

        if ($prodiId !== null && $prodiId > 0) {
            $builderLowonganSegera->join('lowongan_sasaran_prodi lsp', 'lsp.lowongan_id = l.id')
                ->where('lsp.program_studi_id', $prodiId);
        }
        $lowonganSegeraBerakhir = (int) $builderLowonganSegera->select('l.id')->distinct()->countAllResults();

        // 6. Matriks Sebaran Status per Program Studi pada tanggal acuan
        $sebaranProdi = $this->getSebaranProdi($tanggalAcuan, $prodiId, $angkatanId);

        // 7. Antrean Validasi & Rencana Kembali Cuti/Skorsing
        $antreanValidasi = $this->getAntreanValidasi($tanggalAcuan);

        // 8. Kualitas Data (Dihitung dari kelengkapan atribut profil & rasio validasi)
        $kualitasData = $this->hitungKualitasData();

        // 9. Aktivitas Terbaru dari tabel transaksi ketarunaan
        $aktivitasTerbaru = $this->getAktivitasTerbaru();

        return [
            'status_counts'            => $statusCounts,
            'dalam_pendidikan'         => $dalamPendidikan,
            'berhenti_pendidikan'      => $berhentiPendidikan,
            'lulusan'                  => $lulusan,
            'total_rekaman'            => $totalRekaman,
            'tanpa_status_asof'        => $asOfResult['tanpa_status'],
            'data_mode'                => ketarunaan_data_mode_info(),
            'total_prestasi'           => $totalPrestasi,
            'taruna_berprestasi'       => $tarunaBerprestasi,
            'pelanggaran_terbukti'     => $pelanggaranStats['terbukti'],
            'pelanggaran_selesai'      => $pelanggaranStats['selesai'],
            'pelanggaran_proses'       => $pelanggaranStats['proses'],
            'penerima_beasiswa'        => $penerimaBeasiswa,
            'program_beasiswa_aktif'   => $programBeasiswaAktif,
            'lowongan_aktif'           => $lowonganAktif,
            'lowongan_segera_berakhir' => $lowonganSegeraBerakhir,
            'sebaran_prodi'            => $sebaranProdi,
            'kualitas_data'            => $kualitasData,
            'antrean_validasi'         => $antreanValidasi,
            'aktivitas_terbaru'        => $aktivitasTerbaru,
        ];
    }

    /**
     * Rekonstruksi jumlah 6 status pendidikan pada tanggal acuan dari riwayat tervalidasi (UAT-02, DSH-04).
     * Dipakai bersama oleh DashboardService dan LaporanService agar angka selalu identik.
     *
     * @return array{status_counts: array<string, int>, total_tercatat: int, tanpa_status: int}
     */
    public function getStatusCountsAsOf(string $tanggalAcuan, ?int $prodiId = null, ?int $angkatanId = null): array
    {
        $rows = $this->getStatusAsOfRows($tanggalAcuan, $prodiId, $angkatanId);

        $statusCounts = [
            'aktif'             => 0,
            'skorsing'          => 0,
            'cuti'              => 0,
            'mengundurkan_diri' => 0,
            'drop_out'          => 0,
            'lulus'             => 0,
        ];

        foreach ($rows as $row) {
            $code = (string) ($row['kode_status'] ?? '');
            if (array_key_exists($code, $statusCounts)) {
                $statusCounts[$code]++;
            }
        }

        $totalBuilder = $this->db->table('master_taruna m')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left');
        if ($prodiId !== null && $prodiId > 0) {
            $totalBuilder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $totalBuilder->where('pt.angkatan_id', $angkatanId);
        }
        $totalMaster = (int) $totalBuilder->countAllResults();
        $totalTercatat = array_sum($statusCounts);

        return [
            'status_counts'  => $statusCounts,
            'total_tercatat' => $totalTercatat,
            'tanpa_status'   => max(0, $totalMaster - $totalTercatat),
        ];
    }

    /**
     * Mengambil baris taruna dengan status pendidikan terakhir yang sudah tervalidasi hingga tanggal acuan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStatusAsOfRows(
        string $tanggalAcuan,
        ?int $prodiId = null,
        ?int $angkatanId = null,
        ?string $kodeStatus = null
    ): array {
        $sql = "
            SELECT
                m.nomor_taruna,
                m.nama_lengkap,
                m.jenis_kelamin,
                pt.program_studi_id,
                pt.angkatan_id,
                pt.tingkat,
                p.kode_prodi,
                p.nama_prodi,
                a.tahun_masuk,
                r.id AS riwayat_status_id,
                r.kode_status,
                r.mulai_berlaku,
                r.akhir_berlaku,
                r.rencana_kembali,
                r.nomor_keputusan,
                r.tanggal_keputusan,
                r.divalidasi_oleh
            FROM riwayat_status_taruna r
            INNER JOIN master_taruna m ON m.nomor_taruna = r.nomor_taruna
            LEFT JOIN pendidikan_taruna pt ON pt.nomor_taruna = m.nomor_taruna
            LEFT JOIN ref_program_studi p ON p.id = pt.program_studi_id
            LEFT JOIN ref_angkatan a ON a.id = pt.angkatan_id
            WHERE r.status_validasi = 'tervalidasi'
              AND r.mulai_berlaku <= ?
              AND r.id = (
                  SELECT r2.id
                  FROM riwayat_status_taruna r2
                  WHERE r2.nomor_taruna = r.nomor_taruna
                    AND r2.status_validasi = 'tervalidasi'
                    AND r2.mulai_berlaku <= ?
                  ORDER BY r2.mulai_berlaku DESC, r2.id DESC
                  LIMIT 1
              )
        ";

        $params = [$tanggalAcuan, $tanggalAcuan];

        if ($prodiId !== null && $prodiId > 0) {
            $sql .= ' AND pt.program_studi_id = ?';
            $params[] = $prodiId;
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $sql .= ' AND pt.angkatan_id = ?';
            $params[] = $angkatanId;
        }
        if ($kodeStatus !== null && $kodeStatus !== '') {
            $sql .= ' AND r.kode_status = ?';
            $params[] = $kodeStatus;
        }

        $sql .= ' ORDER BY p.id ASC, m.nomor_taruna ASC';

        return $this->db->query($sql, $params)->getResultArray();
    }

    /**
     * Menghitung rekap pelanggaran terbukti (selesai & proses).
     *
     * @param int|null $prodiId
     * @param int|null $angkatanId
     * @return array<string, int>
     */
    private function hitungPelanggaran(?int $prodiId, ?int $angkatanId): array
    {
        $builder = $this->db->table('taruna_pelanggaran pl')
            ->select("
                COUNT(*) AS terbukti,
                SUM(CASE WHEN pl.status_penyelesaian = 'selesai' THEN 1 ELSE 0 END) AS selesai,
                SUM(CASE WHEN pl.status_penyelesaian = 'proses' THEN 1 ELSE 0 END) AS proses
            ")
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pl.nomor_taruna', 'left')
            ->where('pl.status_penetapan', 'terbukti')
            ->where('pl.status_validasi', 'tervalidasi');

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builder->where('pt.angkatan_id', $angkatanId);
        }

        $row = $builder->get()->getRowArray();

        return [
            'terbukti' => (int) ($row['terbukti'] ?? 0),
            'selesai'  => (int) ($row['selesai'] ?? 0),
            'proses'   => (int) ($row['proses'] ?? 0),
        ];
    }

    /**
     * Mengambil matriks sebaran 6 status per Program Studi pada tanggal acuan.
     *
     * @param string   $tanggalAcuan
     * @param int|null $prodiId
     * @param int|null $angkatanId
     * @return array<int, array<string, mixed>>
     */
    private function getSebaranProdi(string $tanggalAcuan, ?int $prodiId, ?int $angkatanId): array
    {
        $prodiBuilder = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC');

        if ($prodiId !== null && $prodiId > 0) {
            $prodiBuilder->where('id', $prodiId);
        }

        $prodis = $prodiBuilder->get()->getResultArray();
        $matrix = [];
        foreach ($prodis as $p) {
            $matrix[(int) $p['id']] = [
                'id'         => (int) $p['id'],
                'kode_prodi' => $p['kode_prodi'],
                'nama_prodi' => $p['nama_prodi'],
                'aktif'      => 0,
                'skorsing'   => 0,
                'cuti'       => 0,
                'mundur'     => 0,
                'do_count'   => 0,
                'lulus'      => 0,
            ];
        }

        $rows = $this->getStatusAsOfRows($tanggalAcuan, $prodiId, $angkatanId);
        $columnMap = [
            'aktif'             => 'aktif',
            'skorsing'          => 'skorsing',
            'cuti'              => 'cuti',
            'mengundurkan_diri' => 'mundur',
            'drop_out'          => 'do_count',
            'lulus'             => 'lulus',
        ];

        foreach ($rows as $row) {
            $pid = (int) ($row['program_studi_id'] ?? 0);
            $col = $columnMap[(string) ($row['kode_status'] ?? '')] ?? null;
            if ($pid > 0 && $col !== null && isset($matrix[$pid])) {
                $matrix[$pid][$col]++;
            }
        }

        return array_values($matrix);
    }

    /**
     * Menghitung ringkasan antrean status validasi lintas modul ECC 2.0.
     *
     * @param bool $enforceRbac True untuk menyesuaikan hak akses antrean pada pengguna aktif
     * @return array{menunggu: int, perlu_perbaikan: int, total_transaksi: int}
     */
    private function collectValidationSummary(bool $enforceRbac): array
    {
        $sources = [
            ['table' => 'riwayat_status_taruna', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => true],
            ['table' => 'taruna_capaian', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => true],
            ['table' => 'taruna_prestasi', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => true],
            ['table' => 'lowongan_pekerjaan', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => true],
            ['table' => 'taruna_pelanggaran', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => !$enforceRbac || KetarunaanPolicy::canAccessPelanggaranModule()],
            ['table' => 'taruna_penerima_beasiswa', 'pending' => ['diajukan', 'perlu_perbaikan'], 'fix_status' => 'perlu_perbaikan', 'allowed' => !$enforceRbac || KetarunaanPolicy::canAccessBeasiswaModule()],
            ['table' => 'alumni_riwayat_perubahan', 'pending' => ['diajukan', 'ditolak'], 'fix_status' => 'ditolak', 'allowed' => !$enforceRbac || KetarunaanPolicy::canAccessAlumniModule()],
        ];

        $menunggu = 0;
        $perluPerbaikan = 0;
        $totalTransaksi = 0;

        foreach ($sources as $src) {
            if (!$src['allowed']) {
                continue;
            }

            $tbl = $src['table'];
            if (!$enforceRbac) {
                $totalTransaksi += (int) $this->db->table($tbl)->countAllResults();
            }

            $rows = $this->db->table($tbl)
                ->select('status_validasi, COUNT(*) AS cnt')
                ->whereIn('status_validasi', $src['pending'])
                ->groupBy('status_validasi')
                ->get()
                ->getResultArray();

            foreach ($rows as $r) {
                if ($r['status_validasi'] === 'diajukan') {
                    $menunggu += (int) $r['cnt'];
                } elseif ($r['status_validasi'] === $src['fix_status']) {
                    $perluPerbaikan += (int) $r['cnt'];
                }
            }
        }

        return [
            'menunggu'        => $menunggu,
            'perlu_perbaikan' => $perluPerbaikan,
            'total_transaksi' => $totalTransaksi,
        ];
    }

    /**
     * Menghitung antrean validasi lintas modul dan rencana kembali cuti/skorsing.
     *
     * @param string $tanggalAcuan
     * @return array<string, int>
     */
    private function getAntreanValidasi(string $tanggalAcuan): array
    {
        $summary = $this->collectValidationSummary(true);

        $rencanaKembali = (int) $this->db->table('riwayat_status_taruna')
            ->whereIn('kode_status', ['cuti', 'skorsing'])
            ->where('status_validasi', 'tervalidasi')
            ->where('akhir_berlaku IS NULL')
            ->where('rencana_kembali IS NOT NULL')
            ->where('rencana_kembali <=', $tanggalAcuan)
            ->countAllResults();

        return [
            'menunggu'        => $summary['menunggu'],
            'perlu_perbaikan' => $summary['perlu_perbaikan'],
            'rencana_kembali' => $rencanaKembali,
        ];
    }

    /**
     * Menghitung persentase kelengkapan profil dan tingkat validasi data.
     *
     * @return array<string, int>
     */
    private function hitungKualitasData(): array
    {
        $totalTaruna = (int) $this->db->table('master_taruna')->countAllResults();
        if ($totalTaruna === 0) {
            return ['persen_lengkap' => 0, 'persen_tervalidasi' => 0];
        }

        $lengkapCount = (int) $this->db->table('master_taruna')
            ->where('nik IS NOT NULL AND nik != ""')
            ->where('tempat_lahir IS NOT NULL AND tempat_lahir != ""')
            ->where('tanggal_lahir IS NOT NULL')
            ->where('nomor_hp IS NOT NULL AND nomor_hp != ""')
            ->where('email IS NOT NULL AND email != ""')
            ->where('nama_wali IS NOT NULL AND nama_wali != ""')
            ->countAllResults();

        $persenLengkap = (int) round(($lengkapCount / $totalTaruna) * 100);

        $globalSummary = $this->collectValidationSummary(false);
        $totalTransaksi = $globalSummary['total_transaksi'];
        $belumTervalidasi = $globalSummary['menunggu'] + $globalSummary['perlu_perbaikan'];
        $persenTervalidasi = $totalTransaksi > 0
            ? (int) round((($totalTransaksi - $belumTervalidasi) / $totalTransaksi) * 100)
            : 100;

        return [
            'persen_lengkap'     => min(100, max(0, $persenLengkap)),
            'persen_tervalidasi' => min(100, max(0, $persenTervalidasi)),
        ];
    }

    /**
     * Mengambil aktivitas terbaru dari catatan riwayat status, prestasi, dan beasiswa.
     *
     * @return array<int, array<string, string>>
     */
    private function getAktivitasTerbaru(): array
    {
        $items = [];

        $statusItem = $this->db->table('riwayat_status_taruna')
            ->select('kode_status, alasan_perubahan, status_validasi')
            ->where('status_validasi', 'diajukan')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($statusItem) {
            $meta = ketarunaan_validasi_meta($statusItem['status_validasi']);
            $items[] = [
                'modul'     => 'Status Taruna',
                'aktivitas' => 'Pengajuan ' . strtolower(ketarunaan_status_meta($statusItem['kode_status'])['label']),
                'status'    => $meta['label'],
                'badge'     => $meta['badge_class'],
                'url'       => site_url('ketarunaan/validasi'),
            ];
        }

        $prestasiItem = $this->db->table('taruna_prestasi')
            ->select('jenis_keikutsertaan, status_validasi')
            ->where('status_validasi', 'tervalidasi')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($prestasiItem) {
            $meta = ketarunaan_validasi_meta($prestasiItem['status_validasi']);
            $items[] = [
                'modul'     => 'Prestasi',
                'aktivitas' => 'Penetapan prestasi ' . $prestasiItem['jenis_keikutsertaan'],
                'status'    => $meta['label'],
                'badge'     => $meta['badge_class'],
                'url'       => site_url('ketarunaan/prestasi'),
            ];
        }

        $perbaikanItem = $this->db->table('riwayat_status_taruna')
            ->select('alasan_perubahan, status_validasi')
            ->where('status_validasi', 'perlu_perbaikan')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($perbaikanItem) {
            $meta = ketarunaan_validasi_meta('dikembalikan');
            $items[] = [
                'modul'     => 'Status Taruna',
                'aktivitas' => 'Perbaikan pengajuan status',
                'status'    => $meta['label'],
                'badge'     => $meta['badge_class'],
                'url'       => site_url('ketarunaan/validasi?status=perlu_perbaikan'),
            ];
        }

        $beasiswaItem = $this->db->table('taruna_penerima_beasiswa')
            ->select('status_validasi')
            ->where('status_validasi', 'tervalidasi')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($beasiswaItem) {
            $meta = ketarunaan_validasi_meta($beasiswaItem['status_validasi']);
            $items[] = [
                'modul'     => 'Beasiswa',
                'aktivitas' => 'Verifikasi penerima beasiswa',
                'status'    => $meta['label'],
                'badge'     => $meta['badge_class'],
                'url'       => site_url('ketarunaan/beasiswa'),
            ];
        }

        return $items;
    }
}

