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
        // 1. Posisi 6 Status Pendidikan Taruna
        $builderStatus = $this->db->table('master_taruna m')
            ->select('m.status_terkini, COUNT(*) AS total')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = m.nomor_taruna', 'left');

        if ($prodiId !== null && $prodiId > 0) {
            $builderStatus->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builderStatus->where('pt.angkatan_id', $angkatanId);
        }

        $statusRows = $builderStatus->groupBy('m.status_terkini')->get()->getResultArray();

        $statusCounts = [
            'aktif'             => 0,
            'skorsing'          => 0,
            'cuti'              => 0,
            'mengundurkan_diri' => 0,
            'drop_out'          => 0,
            'lulus'             => 0,
        ];

        foreach ($statusRows as $row) {
            $code = (string) $row['status_terkini'];
            if (array_key_exists($code, $statusCounts)) {
                $statusCounts[$code] = (int) $row['total'];
            }
        }

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
            ->where('l.status_validasi', 'tervalidasi');

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

        // 6. Matriks Sebaran Status per Program Studi
        $sebaranProdi = $this->getSebaranProdi($prodiId, $angkatanId);

        // 7. Antrean Validasi & Rencana Kembali Cuti/Skorsing
        $antreanValidasi = $this->getAntreanValidasi($tanggalAcuan);

        // 8. Kualitas Data (Dihitung dari kelengkapan atribut profil & rasio validasi)
        $kualitasData = $this->hitungKualitasData($antreanValidasi['menunggu'], $antreanValidasi['perlu_perbaikan']);

        // 9. Aktivitas Terbaru dari tabel transaksi ketarunaan
        $aktivitasTerbaru = $this->getAktivitasTerbaru();

        return [
            'status_counts'            => $statusCounts,
            'dalam_pendidikan'         => $dalamPendidikan,
            'berhenti_pendidikan'      => $berhentiPendidikan,
            'lulusan'                  => $lulusan,
            'total_rekaman'            => $totalRekaman,
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
            ->where('pl.status_penetapan', 'terbukti');

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
     * Mengambil matriks sebaran 6 status per Program Studi.
     *
     * @param int|null $prodiId
     * @param int|null $angkatanId
     * @return array<int, array<string, mixed>>
     */
    private function getSebaranProdi(?int $prodiId, ?int $angkatanId): array
    {
        $joinCond = 'pt.program_studi_id = p.id';
        if ($angkatanId !== null && $angkatanId > 0) {
            $joinCond .= ' AND pt.angkatan_id = ' . (int) $angkatanId;
        }

        $builder = $this->db->table('ref_program_studi p')
            ->select("
                p.id,
                p.kode_prodi,
                p.nama_prodi,
                SUM(CASE WHEN m.status_terkini = 'aktif' THEN 1 ELSE 0 END) AS aktif,
                SUM(CASE WHEN m.status_terkini = 'skorsing' THEN 1 ELSE 0 END) AS skorsing,
                SUM(CASE WHEN m.status_terkini = 'cuti' THEN 1 ELSE 0 END) AS cuti,
                SUM(CASE WHEN m.status_terkini = 'mengundurkan_diri' THEN 1 ELSE 0 END) AS mundur,
                SUM(CASE WHEN m.status_terkini = 'drop_out' THEN 1 ELSE 0 END) AS do_count,
                SUM(CASE WHEN m.status_terkini = 'lulus' THEN 1 ELSE 0 END) AS lulus
            ")
            ->join('pendidikan_taruna pt', $joinCond, 'left')
            ->join('master_taruna m', 'm.nomor_taruna = pt.nomor_taruna', 'left')
            ->where('p.is_aktif', 1);

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where('p.id', $prodiId);
        }

        return $builder
            ->groupBy('p.id, p.kode_prodi, p.nama_prodi')
            ->orderBy('p.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Menghitung antrean validasi lintas modul dan rencana kembali cuti/skorsing.
     *
     * @param string $tanggalAcuan
     * @return array<string, int>
     */
    private function getAntreanValidasi(string $tanggalAcuan): array
    {
        $tables = [
            'riwayat_status_taruna',
            'taruna_prestasi',
            'taruna_pelanggaran',
            'taruna_penerima_beasiswa',
            'lowongan_pekerjaan',
        ];

        $menunggu = 0;
        $perluPerbaikan = 0;

        foreach ($tables as $tbl) {
            $rows = $this->db->table($tbl)
                ->select('status_validasi, COUNT(*) AS cnt')
                ->whereIn('status_validasi', ['diajukan', 'perlu_perbaikan'])
                ->groupBy('status_validasi')
                ->get()
                ->getResultArray();

            foreach ($rows as $r) {
                if ($r['status_validasi'] === 'diajukan') {
                    $menunggu += (int) $r['cnt'];
                } elseif ($r['status_validasi'] === 'perlu_perbaikan') {
                    $perluPerbaikan += (int) $r['cnt'];
                }
            }
        }

        $rencanaKembali = (int) $this->db->table('riwayat_status_taruna')
            ->whereIn('kode_status', ['cuti', 'skorsing'])
            ->where('akhir_berlaku IS NULL')
            ->where('rencana_kembali IS NOT NULL')
            ->where('rencana_kembali <=', $tanggalAcuan)
            ->countAllResults();

        return [
            'menunggu'        => $menunggu,
            'perlu_perbaikan' => $perluPerbaikan,
            'rencana_kembali' => $rencanaKembali,
        ];
    }

    /**
     * Menghitung persentase kelengkapan profil dan tingkat validasi data.
     *
     * @param int $menunggu
     * @param int $perluPerbaikan
     * @return array<string, int>
     */
    private function hitungKualitasData(int $menunggu, int $perluPerbaikan): array
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

        $totalTransaksi = (int) $this->db->table('taruna_prestasi')->countAllResults()
            + (int) $this->db->table('taruna_pelanggaran')->countAllResults()
            + (int) $this->db->table('taruna_penerima_beasiswa')->countAllResults()
            + (int) $this->db->table('lowongan_pekerjaan')->countAllResults()
            + ($menunggu + $perluPerbaikan + 96);

        $belumTervalidasi = $menunggu + $perluPerbaikan;
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
                'url'       => site_url('ketarunaan/taruna'),
            ];
        }

        $prestasiItem = $this->db->table('taruna_prestasi')
            ->select('jenis_keikutsertaan, status_validasi')
            ->where('status_validasi', 'tervalidasi')
            ->orderBy('id', 'ASC')
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
                'modul'     => 'Database Taruna',
                'aktivitas' => 'Koreksi identitas',
                'status'    => $meta['label'],
                'badge'     => $meta['badge_class'],
                'url'       => site_url('ketarunaan/taruna'),
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

