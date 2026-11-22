<?php

namespace App\Libraries\Ketarunaan;

use App\Models\SettingModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Service Modul 8.2 Laporan Lintas Modul & Ekspor Resmi (GOV-01, DSH-03, NFR-02, NFR-09, UAT-19).
 * Menggunakan DashboardService untuk rekonstruksi posisi status agar angka Dashboard dan Laporan selalu sama.
 */
class LaporanService
{
    protected BaseConnection $db;
    protected DashboardService $dashboardService;

    public function __construct(?BaseConnection $db = null, ?DashboardService $dashboardService = null)
    {
        $this->db               = $db ?? \Config\Database::connect();
        $this->dashboardService = $dashboardService ?? new DashboardService($this->db);
        helper('ketarunaan');
    }

    /**
     * Daftar jenis laporan yang boleh dibuka oleh pengguna saat ini sesuai otorisasi peran.
     *
     * @return array<string, array<string, string>>
     */
    public function getAvailableCatalog(): array
    {
        $catalog  = ketarunaan_laporan_catalog();
        $filtered = [];

        foreach ($catalog as $kode => $meta) {
            if (KetarunaanPolicy::canExportLaporanJenis($kode)) {
                $filtered[$kode] = $meta;
            }
        }

        return $filtered;
    }

    /**
     * Menghasilkan data pratinjau sekaligus bahan ekspor untuk satu jenis laporan resmi.
     *
     * @return array<string, mixed>
     */
    public function getReportPayload(
        string $jenis,
        string $tanggalAcuan,
        ?int $prodiId = null,
        ?int $angkatanId = null
    ): array {
        $jenisClean   = strtolower(trim($jenis));
        $catalog      = ketarunaan_laporan_catalog();
        $dtCheck      = \DateTimeImmutable::createFromFormat('Y-m-d', $tanggalAcuan);
        $tanggalClean = ($dtCheck !== false && $dtCheck->format('Y-m-d') === $tanggalAcuan) ? $tanggalAcuan : date('Y-m-d');

        if (!isset($catalog[$jenisClean])) {
            return ['allowed' => false, 'reason' => 'Jenis laporan tidak dikenali.', 'status_code' => 404];
        }

        if (!KetarunaanPolicy::canExportLaporanJenis($jenisClean)) {
            return ['allowed' => false, 'reason' => 'Anda tidak memiliki izin melihat atau mengunduh jenis laporan ini.', 'status_code' => 403];
        }

        $stats          = $this->dashboardService->getAggregatedStats($tanggalClean, $prodiId, $angkatanId);
        $dataMode       = ketarunaan_data_mode_info();
        $catatanOperasi = (string) SettingModel::getSetting(
            'ecc2_catatan_operasional',
            'Data operasional resmi ECC PKTJ.'
        );

        $prodiLabel = 'Semua Program Studi';
        if ($prodiId !== null && $prodiId > 0) {
            $prodi = $this->db->table('ref_program_studi')->select('kode_prodi, nama_prodi')->where('id', $prodiId)->get()->getRowArray();
            if ($prodi) {
                $prodiLabel = $prodi['kode_prodi'] . ' - ' . $prodi['nama_prodi'];
            }
        }

        $angkatanLabel = 'Semua Angkatan';
        if ($angkatanId !== null && $angkatanId > 0) {
            $angkatan = $this->db->table('ref_angkatan')->select('tahun_masuk, kode_romawi')->where('id', $angkatanId)->get()->getRowArray();
            if ($angkatan) {
                $angkatanLabel = 'Angkatan ' . ketarunaan_format_angkatan($angkatan);
            }
        }

        $dataset = match ($jenisClean) {
            'status_pendidikan' => $this->buildStatusPendidikanDataset($tanggalClean, $prodiId, $angkatanId, $stats),
            'prestasi'          => $this->buildPrestasiDataset($prodiId, $angkatanId, $stats),
            'pelanggaran'       => $this->buildPelanggaranDataset($prodiId, $angkatanId, $stats),
            'beasiswa'          => $this->buildBeasiswaDataset($prodiId, $angkatanId, $stats),
            'lowongan'          => $this->buildLowonganDataset($tanggalClean, $prodiId, $stats),
            'alumni'            => $this->buildAlumniDataset($prodiId, $angkatanId, $stats),
        };

        return [
            'allowed'          => true,
            'status_code'      => 200,
            'jenis'            => $jenisClean,
            'meta'             => $catalog[$jenisClean],
            'tanggal_acuan'    => $tanggalClean,
            'prodi_id'         => $prodiId,
            'angkatan_id'      => $angkatanId,
            'prodi_label'      => $prodiLabel,
            'angkatan_label'   => $angkatanLabel,
            'data_mode'        => $dataMode,
            'catatan_operasi'  => $catatanOperasi,
            'dashboard_count'  => $dataset['dashboard_count'],
            'ringkasan_badges' => $dataset['ringkasan_badges'],
            'columns'          => $dataset['columns'],
            'rows'             => $dataset['rows'],
        ];
    }

    /**
     * Mengonversi payload laporan ke baris-baris CSV resmi dengan kepala metadata & proteksi formula.
     *
     * @param array<string, mixed> $payload
     * @return array<int, array<int, string>>
     */
    public function buildCsvLines(array $payload): array
    {
        $meta = $payload['meta'];

        $lines = [
            ['Evidence Command Center (ECC) - Laporan Resmi Ketarunaan & Alumni'],
            ['Jenis Laporan', ketarunaan_safe_csv_cell((string) $meta['label'])],
            ['Penanda Lingkungan Data', ketarunaan_safe_csv_cell((string) $payload['data_mode']['label'])],
            ['Definisi Satuan', ketarunaan_safe_csv_cell((string) $meta['satuan'])],
            ['Tanggal Acuan', ketarunaan_safe_csv_cell(ketarunaan_format_tanggal((string) $payload['tanggal_acuan']))],
            ['Filter Program Studi', ketarunaan_safe_csv_cell((string) $payload['prodi_label'])],
            ['Filter Angkatan', ketarunaan_safe_csv_cell((string) $payload['angkatan_label'])],
            ['Total Data (Sesuai Dashboard)', (string) (int) $payload['dashboard_count']],
            ['Catatan Operasional', ketarunaan_safe_csv_cell((string) $payload['catatan_operasi'])],
            [],
            array_map(static fn (string $c): string => ketarunaan_safe_csv_cell($c), $payload['columns']),
        ];

        foreach ($payload['rows'] as $row) {
            $escaped = [];
            foreach ($row as $cell) {
                $escaped[] = ketarunaan_safe_csv_cell((string) $cell);
            }
            $lines[] = $escaped;
        }

        return $lines;
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildStatusPendidikanDataset(
        string $tanggalAcuan,
        ?int $prodiId,
        ?int $angkatanId,
        array $stats
    ): array {
        $rawRows = $this->dashboardService->getStatusAsOfRows($tanggalAcuan, $prodiId, $angkatanId);
        $rows    = [];

        foreach ($rawRows as $idx => $r) {
            $statusLabel = ketarunaan_status_meta((string) ($r['kode_status'] ?? ''))['label'];
            $rows[]      = [
                (string) ($idx + 1),
                (string) ($r['nomor_taruna'] ?? '-'),
                (string) ($r['nama_lengkap'] ?? '-'),
                (string) ($r['kode_prodi'] ?? '-'),
                ketarunaan_format_angkatan($r['tahun_masuk'] ?? null),
                $statusLabel,
                ketarunaan_format_tanggal((string) ($r['mulai_berlaku'] ?? ''), true),
                ketarunaan_format_tanggal((string) ($r['rencana_kembali'] ?? ''), true),
                (string) ($r['nomor_keputusan'] ?: '-'),
            ];
        }

        $sc = $stats['status_counts'];

        return [
            'dashboard_count'  => (int) $stats['total_rekaman'],
            'ringkasan_badges' => [
                ['label' => 'Aktif', 'nilai' => (int) $sc['aktif'], 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                ['label' => 'Skorsing', 'nilai' => (int) $sc['skorsing'], 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                ['label' => 'Cuti', 'nilai' => (int) $sc['cuti'], 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
                ['label' => 'Mundur', 'nilai' => (int) $sc['mengundurkan_diri'], 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
                ['label' => 'Drop Out', 'nilai' => (int) $sc['drop_out'], 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
                ['label' => 'Lulus', 'nilai' => (int) $sc['lulus'], 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
            ],
            'columns' => ['No', 'Nomor Taruna', 'Nama Lengkap', 'Program Studi', 'Angkatan', 'Status Pendidikan', 'Mulai Berlaku', 'Rencana Kembali', 'Nomor Keputusan'],
            'rows'    => $rows,
        ];
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildPrestasiDataset(?int $prodiId, ?int $angkatanId, array $stats): array
    {
        $builder = $this->db->table('taruna_prestasi p')
            ->select("p.id, p.nama_prestasi, p.jenis_prestasi, p.bidang_prestasi, p.tingkat_prestasi, p.peringkat, p.tanggal_mulai, p.jenis_keikutsertaan, p.penyelenggara,
                (SELECT GROUP_CONCAT(CONCAT(pp.nomor_taruna, ' - ', COALESCE(m.nama_lengkap, ''), ' (', COALESCE(pr.kode_prodi, '-'), ')') SEPARATOR '; ')
                 FROM taruna_peserta_prestasi pp
                 LEFT JOIN master_taruna m ON m.nomor_taruna = pp.nomor_taruna
                 LEFT JOIN pendidikan_taruna pt ON pt.nomor_taruna = pp.nomor_taruna
                 LEFT JOIN ref_program_studi pr ON pr.id = pt.program_studi_id
                 WHERE pp.prestasi_id = p.id) AS daftar_peserta")
            ->where('p.status_validasi', 'tervalidasi')
            ->orderBy('p.tanggal_mulai', 'DESC')
            ->orderBy('p.id', 'DESC');

        if (($prodiId !== null && $prodiId > 0) || ($angkatanId !== null && $angkatanId > 0)) {
            $conds = ['ppf.prestasi_id = p.id'];
            if ($prodiId !== null && $prodiId > 0) {
                $conds[] = 'ptf.program_studi_id = ' . (int) $prodiId;
            }
            if ($angkatanId !== null && $angkatanId > 0) {
                $conds[] = 'ptf.angkatan_id = ' . (int) $angkatanId;
            }
            $builder->where(
                'EXISTS (SELECT 1 FROM taruna_peserta_prestasi ppf INNER JOIN pendidikan_taruna ptf ON ptf.nomor_taruna = ppf.nomor_taruna WHERE ' . implode(' AND ', $conds) . ')',
                null,
                false
            );
        }

        $rawRows = $builder->get()->getResultArray();
        $rows    = [];

        foreach ($rawRows as $idx => $r) {
            $rows[] = [
                (string) ($idx + 1),
                (string) ($r['nama_prestasi'] ?? '-'),
                ucfirst((string) ($r['jenis_prestasi'] ?? '-')),
                ucwords(str_replace('_', ' ', (string) ($r['bidang_prestasi'] ?? '-'))),
                ucfirst((string) ($r['tingkat_prestasi'] ?? '-')),
                (string) ($r['peringkat'] ?? '-'),
                ketarunaan_format_tanggal((string) ($r['tanggal_mulai'] ?? ''), true),
                ucfirst((string) ($r['jenis_keikutsertaan'] ?? '-')),
                (string) ($r['daftar_peserta'] ?: '-'),
            ];
        }

        return [
            'dashboard_count'  => (int) $stats['total_prestasi'],
            'ringkasan_badges' => [
                ['label' => 'Prestasi Tervalidasi', 'nilai' => (int) $stats['total_prestasi'], 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                ['label' => 'Taruna Berprestasi Unik', 'nilai' => (int) $stats['taruna_berprestasi'], 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            ],
            'columns' => ['No', 'Nama Prestasi', 'Jenis', 'Bidang', 'Tingkat', 'Peringkat', 'Tanggal Mulai', 'Keikutsertaan', 'Peserta Terdaftar'],
            'rows'    => $rows,
        ];
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildPelanggaranDataset(?int $prodiId, ?int $angkatanId, array $stats): array
    {
        $builder = $this->db->table('taruna_pelanggaran pl')
            ->select('pl.nomor_perkara, pl.nomor_taruna, pl.tanggal_kejadian, pl.jenis_pelanggaran, pl.tingkat_pelanggaran, pl.status_penetapan, pl.status_penyelesaian, pl.nomor_keputusan, m.nama_lengkap, p.kode_prodi, a.tahun_masuk')
            ->join('master_taruna m', 'm.nomor_taruna = pl.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pl.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->where('pl.status_penetapan', 'terbukti')
            ->where('pl.status_validasi', 'tervalidasi')
            ->orderBy('pl.tanggal_kejadian', 'DESC');

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builder->where('pt.angkatan_id', $angkatanId);
        }

        $rawRows = $builder->get()->getResultArray();
        $rows    = [];

        foreach ($rawRows as $idx => $r) {
            $rows[] = [
                (string) ($idx + 1),
                (string) ($r['nomor_perkara'] ?: '-'),
                (string) ($r['nomor_taruna'] ?? '-'),
                (string) ($r['nama_lengkap'] ?? '-'),
                (string) ($r['kode_prodi'] ?? '-'),
                ketarunaan_format_angkatan($r['tahun_masuk'] ?? null),
                ketarunaan_format_tanggal((string) ($r['tanggal_kejadian'] ?? ''), true),
                ucwords(str_replace('_', ' ', (string) ($r['tingkat_pelanggaran'] ?? '-'))),
                (string) ($r['jenis_pelanggaran'] ?? '-'),
                ucfirst((string) ($r['status_penyelesaian'] ?? '-')),
                (string) ($r['nomor_keputusan'] ?: '-'),
            ];
        }

        return [
            'dashboard_count'  => (int) $stats['pelanggaran_terbukti'],
            'ringkasan_badges' => [
                ['label' => 'Perkara Terbukti', 'nilai' => (int) $stats['pelanggaran_terbukti'], 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
                ['label' => 'Selesai', 'nilai' => (int) $stats['pelanggaran_selesai'], 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
                ['label' => 'Dalam Proses', 'nilai' => (int) $stats['pelanggaran_proses'], 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            ],
            'columns' => ['No', 'Nomor Perkara', 'Nomor Taruna', 'Nama Taruna', 'Program Studi', 'Angkatan', 'Tanggal Kejadian', 'Tingkat', 'Jenis Pelanggaran', 'Penyelesaian', 'Nomor Keputusan'],
            'rows'    => $rows,
        ];
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildBeasiswaDataset(?int $prodiId, ?int $angkatanId, array $stats): array
    {
        $canViewEconomy = KetarunaanPolicy::canViewBeasiswaEconomy();

        $builder = $this->db->table('taruna_penerima_beasiswa pb')
            ->select('pb.nomor_taruna, pb.tanggal_penetapan, pb.nomor_keputusan, pb.ip_semester, pb.pekerjaan_orang_tua, pb.penghasilan_bulanan, pb.acuan_wilayah_3tp, prog.nama_program, prog.jenis_beasiswa, m.nama_lengkap, p.kode_prodi, a.tahun_masuk')
            ->join('taruna_program_beasiswa prog', 'prog.id = pb.program_beasiswa_id', 'left')
            ->join('master_taruna m', 'm.nomor_taruna = pb.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pb.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->where('pb.status_penetapan', 'ditetapkan')
            ->where('pb.status_validasi', 'tervalidasi')
            ->orderBy('pb.tanggal_penetapan', 'DESC');

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builder->where('pt.angkatan_id', $angkatanId);
        }

        $rawRows = $builder->get()->getResultArray();
        $columns = ['No', 'Program Beasiswa', 'Jenis', 'Nomor Taruna', 'Nama Taruna', 'Program Studi', 'Angkatan', 'Tanggal Penetapan', 'Nomor Keputusan'];
        if ($canViewEconomy) {
            $columns[] = 'Pekerjaan Orang Tua';
            $columns[] = 'Penghasilan Bulanan';
            $columns[] = 'Wilayah 3TP';
        }

        $rows = [];
        foreach ($rawRows as $idx => $r) {
            $row = [
                (string) ($idx + 1),
                (string) ($r['nama_program'] ?? '-'),
                ucwords(str_replace('_', ' ', (string) ($r['jenis_beasiswa'] ?? '-'))),
                (string) ($r['nomor_taruna'] ?? '-'),
                (string) ($r['nama_lengkap'] ?? '-'),
                (string) ($r['kode_prodi'] ?? '-'),
                ketarunaan_format_angkatan($r['tahun_masuk'] ?? null),
                ketarunaan_format_tanggal((string) ($r['tanggal_penetapan'] ?? ''), true),
                (string) ($r['nomor_keputusan'] ?: '-'),
            ];
            if ($canViewEconomy) {
                $row[] = (string) ($r['pekerjaan_orang_tua'] ?: '-');
                $row[] = $r['penghasilan_bulanan'] !== null ? ketarunaan_rupiah($r['penghasilan_bulanan']) : '-';
                $row[] = (string) ($r['acuan_wilayah_3tp'] ?: '-');
            }
            $rows[] = $row;
        }

        return [
            'dashboard_count'  => (int) $stats['penerima_beasiswa'],
            'ringkasan_badges' => [
                ['label' => 'Penerima Ditetapkan', 'nilai' => (int) $stats['penerima_beasiswa'], 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                ['label' => 'Program Berjalan', 'nilai' => (int) $stats['program_beasiswa_aktif'], 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            ],
            'columns' => $columns,
            'rows'    => $rows,
        ];
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildLowonganDataset(string $tanggalAcuan, ?int $prodiId, array $stats): array
    {
        $awalTanggalAcuan = date('Y-m-d 00:00:00', strtotime($tanggalAcuan));

        $builder = $this->db->table('lowongan_pekerjaan l')
            ->select("l.id, l.judul_lowongan, l.jenis_pekerjaan, l.jenjang_pendidikan, l.tahun_lulus_sasaran, l.jumlah_kebutuhan, l.lokasi, l.batas_pendaftaran, pm.nama_perusahaan, pm.kota,
                (SELECT GROUP_CONCAT(pr.kode_prodi ORDER BY pr.id ASC SEPARATOR ', ')
                 FROM lowongan_sasaran_prodi lsp
                 INNER JOIN ref_program_studi pr ON pr.id = lsp.program_studi_id
                 WHERE lsp.lowongan_id = l.id) AS prodi_sasaran")
            ->join('perusahaan_mitra pm', 'pm.id = l.perusahaan_id', 'left')
            ->where('l.status_publikasi', 'terbit')
            ->where('l.status_validasi', 'tervalidasi')
            ->where('l.batas_pendaftaran >=', $awalTanggalAcuan)
            ->orderBy('l.batas_pendaftaran', 'ASC');

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where(
                'EXISTS (SELECT 1 FROM lowongan_sasaran_prodi lspf WHERE lspf.lowongan_id = l.id AND lspf.program_studi_id = ' . (int) $prodiId . ')',
                null,
                false
            );
        }

        $rawRows = $builder->get()->getResultArray();
        $rows    = [];

        foreach ($rawRows as $idx => $r) {
            $rows[] = [
                (string) ($idx + 1),
                (string) ($r['judul_lowongan'] ?? '-'),
                (string) ($r['nama_perusahaan'] ?? '-'),
                ucwords(str_replace('_', ' ', (string) ($r['jenis_pekerjaan'] ?? '-'))),
                (string) ($r['lokasi'] ?: ($r['kota'] ?: '-')),
                (string) ($r['prodi_sasaran'] ?: 'Semua Prodi'),
                (string) ($r['jumlah_kebutuhan'] ?: '-'),
                ketarunaan_format_tanggal((string) ($r['batas_pendaftaran'] ?? ''), true),
            ];
        }

        return [
            'dashboard_count'  => (int) $stats['lowongan_aktif'],
            'ringkasan_badges' => [
                ['label' => 'Lowongan Aktif', 'nilai' => (int) $stats['lowongan_aktif'], 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                ['label' => 'Segera Berakhir (<=7 Hari)', 'nilai' => (int) $stats['lowongan_segera_berakhir'], 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            ],
            'columns' => ['No', 'Judul Lowongan', 'Perusahaan Mitra', 'Jenis', 'Lokasi', 'Sasaran Prodi', 'Kebutuhan', 'Batas Pendaftaran'],
            'rows'    => $rows,
        ];
    }

    /**
     * @return array{dashboard_count: int, ringkasan_badges: array<int, array<string, mixed>>, columns: array<int, string>, rows: array<int, array<int, string>>}
     */
    private function buildAlumniDataset(?int $prodiId, ?int $angkatanId, array $stats): array
    {
        $builder = $this->db->table('alumni_profil ap')
            ->select('ap.nomor_taruna, ap.tahun_lulus, ap.tanggal_lulus, ap.nomor_keputusan_lulus, ap.pekerjaan_saat_ini, ap.instansi_saat_ini, ap.kota_domisili, ap.sumber_konfirmasi, m.nama_lengkap, p.kode_prodi, a.tahun_masuk')
            ->join('master_taruna m', 'm.nomor_taruna = ap.nomor_taruna')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('ref_program_studi p', 'p.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan a', 'a.id = pt.angkatan_id', 'left')
            ->where('ap.status_profil', 'aktif')
            ->orderBy('ap.tahun_lulus', 'DESC')
            ->orderBy('ap.nomor_taruna', 'ASC');

        if ($prodiId !== null && $prodiId > 0) {
            $builder->where('pt.program_studi_id', $prodiId);
        }
        if ($angkatanId !== null && $angkatanId > 0) {
            $builder->where('pt.angkatan_id', $angkatanId);
        }

        $rawRows     = $builder->get()->getResultArray();
        $sumberMap   = ketarunaan_alumni_sumber_konfirmasi_catalog();
        $bekerja     = 0;
        $rows        = [];

        foreach ($rawRows as $idx => $r) {
            if (trim((string) ($r['pekerjaan_saat_ini'] ?? '')) !== '') {
                $bekerja++;
            }
            $sumberKey = (string) ($r['sumber_konfirmasi'] ?? '');
            $rows[]    = [
                (string) ($idx + 1),
                (string) ($r['nomor_taruna'] ?? '-'),
                (string) ($r['nama_lengkap'] ?? '-'),
                (string) ($r['kode_prodi'] ?? '-'),
                ketarunaan_format_angkatan($r['tahun_masuk'] ?? null),
                (string) ($r['tahun_lulus'] ?? '-'),
                ketarunaan_format_tanggal((string) ($r['tanggal_lulus'] ?? ''), true),
                (string) ($r['nomor_keputusan_lulus'] ?: '-'),
                (string) ($r['pekerjaan_saat_ini'] ?: '-'),
                (string) ($r['instansi_saat_ini'] ?: '-'),
                (string) ($r['kota_domisili'] ?: '-'),
                (string) ($sumberMap[$sumberKey] ?? ($sumberKey ?: '-')),
            ];
        }

        $totalAlumni = count($rawRows);

        return [
            'dashboard_count'  => $totalAlumni,
            'ringkasan_badges' => [
                ['label' => 'Alumni Tervalidasi', 'nilai' => $totalAlumni, 'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
                ['label' => 'Pekerjaan Tercatat', 'nilai' => $bekerja, 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            ],
            'columns' => ['No', 'Nomor Taruna', 'Nama Lengkap', 'Program Studi', 'Angkatan', 'Tahun Lulus', 'Tanggal Lulus', 'Nomor SK Lulus', 'Pekerjaan Saat Ini', 'Instansi', 'Kota Domisili', 'Sumber Konfirmasi'],
            'rows'    => $rows,
        ];
    }
}
