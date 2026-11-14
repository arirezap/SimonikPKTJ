<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\TarunaPelanggaranModel;
use App\Models\Ketarunaan\TarunaPelanggaranPihakModel;
use App\Models\Ketarunaan\TarunaPelanggaranTindakLanjutModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Service Layer Modul 4: Pelanggaran Taruna (ECC 2.0).
 * Mematuhi SRP, Privacy by Design (PEL-06 / NFR-02), pemisahan 3 status (PEL-03 / PEL-04),
 * dan integritas riwayat tindak lanjut / banding.
 */
class PelanggaranService
{
    protected BaseConnection $db;
    protected TarunaPelanggaranModel $pelanggaranModel;
    protected TarunaPelanggaranPihakModel $pihakModel;
    protected TarunaPelanggaranTindakLanjutModel $tindakLanjutModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?TarunaPelanggaranModel $pelanggaranModel = null,
        ?TarunaPelanggaranPihakModel $pihakModel = null,
        ?TarunaPelanggaranTindakLanjutModel $tindakLanjutModel = null
    ) {
        $this->db                = $db ?? \Config\Database::connect();
        $this->pelanggaranModel  = $pelanggaranModel ?? new TarunaPelanggaranModel();
        $this->pihakModel        = $pihakModel ?? new TarunaPelanggaranPihakModel();
        $this->tindakLanjutModel = $tindakLanjutModel ?? new TarunaPelanggaranTindakLanjutModel();
        helper('ketarunaan');
    }

    /**
     * Daftar opsi referensi untuk filter dan formulir Modul Pelanggaran.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        $prodiList = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $tahunRows = $this->db->table('taruna_pelanggaran')
            ->select('DISTINCT YEAR(tanggal_kejadian) AS tahun', false)
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();

        $tahunList = [];
        foreach ($tahunRows as $tr) {
            if (!empty($tr['tahun'])) {
                $tahunList[] = (int) $tr['tahun'];
            }
        }
        if (empty($tahunList)) {
            $tahunList = [2026, 2025];
        }

        return [
            'prodi_list'              => $prodiList,
            'tahun_list'              => $tahunList,
            'kategori_catalog'        => ketarunaan_kategori_pelanggaran_catalog(),
            'tingkat_catalog'         => ketarunaan_tingkat_pelanggaran_catalog(),
            'penetapan_options'       => [
                'dugaan'         => 'Dugaan',
                'terbukti'       => 'Terbukti',
                'tidak_terbukti' => 'Tidak Terbukti',
                'dibatalkan'     => 'Dibatalkan',
            ],
            'penyelesaian_options'    => [
                'proses'  => 'Proses',
                'selesai' => 'Selesai',
                'banding' => 'Banding',
            ],
            'validasi_options'        => [
                'draf'            => 'Draf',
                'diajukan'        => 'Diajukan',
                'perlu_perbaikan' => 'Dikembalikan',
                'tervalidasi'     => 'Tervalidasi',
            ],
            'dasar_peraturan_options' => [
                'Pedoman Tata Tertib Taruna PKTJ',
                'Peraturan Kedisiplinan Asrama',
                'Peraturan Akademik PKTJ',
                'Kode Etik Taruna PKTJ',
            ],
            'jenis_sanksi_options'    => [
                'Pembinaan',
                'Teguran Lisan',
                'Peringatan Tertulis',
                'Tugas Khusus Terukur',
                'Skorsing Pembinaan',
                'Pencabutan Izin Keluar',
                'Tidak Ada Sanksi / Dibebaskan',
            ],
            'unit_options'            => ['Ketarunaan', 'Pusbangkar', 'BAAK', 'Program Studi'],
            'pelapor_options'         => ['Unit Ketarunaan', 'Pusbangkar', 'Petugas Jaga Pusbangkar', 'Dosen / Instruktur', 'Laporan Masyarakat'],
            'sumber_options'          => ['Laporan petugas', 'Pengaduan', 'Observasi pembinaan', 'Berita acara jaga'],
            'pemeriksa_options'       => ['Tim Pemeriksa Unit', 'Tim Pusbangkar', 'Komisi Disiplin Taruna', 'Pembina Akademik'],
            'pic_options'             => ['Unit Ketarunaan', 'Pusbangkar', 'Dosen Wali', 'Pembina Asrama'],
            'peran_pihak_options'     => ['Terlapor', 'Saksi', 'Pelapor / Saksi', 'Korban / Pihak Terkait', 'Pendamping'],
            'jenis_dokumen_options'   => ['Laporan', 'Pemeriksaan', 'Penetapan', 'Pendukung'],
        ];
    }

    /**
     * Daftar ringkas taruna untuk dropdown pemilihan kasus/pihak terkait.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTarunaSelectList(): array
    {
        return $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, mt.status_terkini, ps.kode_prodi, ang.tahun_masuk AS angkatan')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->orderBy('mt.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Menghasilkan nomor perkara/kasus berikutnya yang disarankan (PKR/YYYY/XXXX).
     *
     * @return string
     */
    public function generateSuggestedNomorPerkara(): string
    {
        $tahun  = '2026';
        $prefix = 'PKR/' . $tahun . '/';
        $rows   = $this->db->table('taruna_pelanggaran')
            ->select('nomor_perkara')
            ->like('nomor_perkara', $prefix, 'after')
            ->get()
            ->getResultArray();

        $maxSeq = 0;
        foreach ($rows as $row) {
            $parts = explode('/', (string) ($row['nomor_perkara'] ?? ''));
            $num   = (int) end($parts);
            if ($num > $maxSeq) {
                $maxSeq = $num;
            }
        }

        return $prefix . sprintf('%04d', $maxSeq + 1);
    }

    /**
     * Mengambil data direktori Pelanggaran Taruna dengan 4 metrik resmi dan filter 6 parameter.
     * Identitas taruna disamarkan di server apabila pengguna bukan petugas berwenang disiplin.
     *
     * @param array<string, mixed> $filters
     * @param int                  $page
     * @param int                  $perPage
     * @param bool                 $canViewFull
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters = [], int $page = 1, int $perPage = 10, bool $canViewFull = false): array
    {
        // 4 Metrik Resmi (PEL-06: hanya terbukti + tervalidasi dihitung sebagai pelanggaran sah)
        $terbukti = (int) $this->db->table('taruna_pelanggaran')
            ->where('status_penetapan', 'terbukti')
            ->where('status_validasi', 'tervalidasi')
            ->countAllResults();

        $dugaan = (int) $this->db->table('taruna_pelanggaran')
            ->where('status_penetapan', 'dugaan')
            ->countAllResults();

        $terbuktiSelesai = (int) $this->db->table('taruna_pelanggaran')
            ->where('status_penetapan', 'terbukti')
            ->where('status_validasi', 'tervalidasi')
            ->where('status_penyelesaian', 'selesai')
            ->countAllResults();

        $terbuktiProses = (int) $this->db->table('taruna_pelanggaran')
            ->where('status_penetapan', 'terbukti')
            ->where('status_validasi', 'tervalidasi')
            ->where('status_penyelesaian', 'proses')
            ->countAllResults();

        $builder = $this->db->table('taruna_pelanggaran pl')
            ->select('pl.*, mt.nama_lengkap, ps.kode_prodi, ps.nama_prodi, ang.tahun_masuk AS angkatan')
            ->join('master_taruna mt', 'mt.nomor_taruna = pl.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pl.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left');

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $builder->groupStart()
                ->like('pl.nomor_perkara', $q)
                ->orLike('pl.nomor_taruna', $q);
            if ($canViewFull) {
                $builder->orLike('mt.nama_lengkap', $q);
            }
            $builder->groupEnd();
        }

        $prodiId = !empty($filters['prodi_id']) ? (int) $filters['prodi_id'] : 0;
        if ($prodiId > 0) {
            $builder->where('pt.program_studi_id', $prodiId);
        }

        $tahun = trim((string) ($filters['tahun'] ?? ''));
        if ($tahun !== '' && $tahun !== 'all') {
            $builder->where('YEAR(pl.tanggal_kejadian)', (int) $tahun);
        }

        $stPenetapan = trim((string) ($filters['status_penetapan'] ?? 'all'));
        if ($stPenetapan !== '' && $stPenetapan !== 'all') {
            $builder->where('pl.status_penetapan', $stPenetapan);
        }

        $stPenyelesaian = trim((string) ($filters['status_penyelesaian'] ?? 'all'));
        if ($stPenyelesaian !== '' && $stPenyelesaian !== 'all') {
            $builder->where('pl.status_penyelesaian', $stPenyelesaian);
        }

        $stValidasi = trim((string) ($filters['status_validasi'] ?? 'all'));
        if ($stValidasi !== '' && $stValidasi !== 'all') {
            $builder->where('pl.status_validasi', $stValidasi);
        }

        $totalFiltered = (int) $builder->countAllResults(false);
        $totalPages    = max(1, (int) ceil($totalFiltered / max(1, $perPage)));
        $page          = min(max(1, $page), $totalPages);
        $offset        = ($page - 1) * $perPage;

        $rows = $builder->orderBy('pl.tanggal_kejadian', 'DESC')
            ->orderBy('pl.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['display_nama'] = $this->formatDisplayName((string) ($row['nama_lengkap'] ?? ''), (int) $row['id'], $canViewFull);
            $row['display_nomor_taruna'] = ketarunaan_mask_nomor_taruna((string) ($row['nomor_taruna'] ?? ''), $canViewFull);
        }
        unset($row);

        return [
            'metrics' => [
                'terbukti'         => $terbukti,
                'dugaan'           => $dugaan,
                'terbukti_selesai' => $terbuktiSelesai,
                'terbukti_proses'  => $terbuktiProses,
            ],
            'rows'       => $rows,
            'pagination' => [
                'page'           => $page,
                'per_page'       => $perPage,
                'total_filtered' => $totalFiltered,
                'total_pages'    => $totalPages,
                'from'           => $totalFiltered > 0 ? $offset + 1 : 0,
                'to'             => min($offset + $perPage, $totalFiltered),
            ],
        ];
    }

    /**
     * Mengambil data lengkap satu kasus beserta pihak terkait, riwayat tindak lanjut, dokumen, dan checklist.
     *
     * @param int  $id
     * @param bool $canViewFull
     * @return array<string, mixed>|null
     */
    public function getDetail(int $id, bool $canViewFull = false): ?array
    {
        $kasus = $this->db->table('taruna_pelanggaran pl')
            ->select('pl.*, mt.nama_lengkap, mt.status_terkini, ps.kode_prodi, ps.nama_prodi, ang.tahun_masuk AS angkatan')
            ->join('master_taruna mt', 'mt.nomor_taruna = pl.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = pl.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->where('pl.id', $id)
            ->get()
            ->getRowArray();

        if (!$kasus) {
            return null;
        }

        $kasus['display_nama'] = $this->formatDisplayName((string) ($kasus['nama_lengkap'] ?? ''), $id, $canViewFull);
        $kasus['display_nomor_taruna'] = ketarunaan_mask_nomor_taruna((string) ($kasus['nomor_taruna'] ?? ''), $canViewFull);

        // Pihak Kasus
        $pihakRows = $this->pihakModel
            ->where('pelanggaran_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($pihakRows as $idx => &$p) {
            if (!$canViewFull && ($p['jenis_pihak'] ?? 'taruna') === 'taruna') {
                $p['display_nama'] = 'Taruna ' . chr(65 + ($idx % 26));
                $p['display_nomor'] = ketarunaan_mask_nomor_taruna((string) ($p['nomor_taruna'] ?? ''), false);
            } else {
                $p['display_nama'] = (string) ($p['nama_pihak'] ?? '-');
                $p['display_nomor'] = (string) ($p['nomor_taruna'] ?: '-');
            }
        }
        unset($p);

        // Riwayat Tindak Lanjut / Keputusan / Banding
        $tindakLanjutRows = $this->tindakLanjutModel
            ->where('pelanggaran_id', $id)
            ->orderBy('tanggal_tindakan', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        // Daftar Dokumen Bukti
        $dokumenList = $this->decodeDokumenList($kasus, $canViewFull);

        // Riwayat Validasi JSON
        $riwayat = json_decode((string) ($kasus['riwayat_validasi_json'] ?? '[]'), true);
        if (!is_array($riwayat) || empty($riwayat)) {
            $riwayat = [
                [
                    'tanggal' => substr((string) ($kasus['created_at'] ?: $kasus['tanggal_kejadian']), 0, 10),
                    'waktu'   => (string) ($kasus['created_at'] ?: $kasus['tanggal_kejadian']),
                    'pelaku'  => (string) ($kasus['diajukan_oleh'] ?: 'PIC Ketarunaan'),
                    'aksi'    => 'Menyimpan',
                    'status'  => (string) ($kasus['status_validasi'] ?? 'draf'),
                    'catatan' => 'Pencatatan kasus pada sistem.',
                ],
            ];
        }

        // Pemeriksaan kelengkapan dokumen (Checklist Tahap 4)
        $hasLaporan   = !empty($kasus['uraian']) && !empty($kasus['lokasi']) && !empty($kasus['tanggal_kejadian']);
        $hasBap       = !empty($kasus['berkas_bap']);
        $hasPenetapan = !empty($kasus['nomor_keputusan']) && !empty($kasus['bukti_berkas']);

        foreach ($dokumenList as $dok) {
            $jns = strtolower((string) ($dok['jenis_dokumen'] ?? ''));
            if ($jns === 'laporan') {
                $hasLaporan = true;
            } elseif ($jns === 'pemeriksaan') {
                $hasBap = true;
            } elseif ($jns === 'penetapan') {
                $hasPenetapan = true;
            }
        }

        return [
            'kasus'         => $kasus,
            'pihak'         => $pihakRows,
            'tindak_lanjut' => $tindakLanjutRows,
            'dokumen'       => $dokumenList,
            'riwayat'       => $riwayat,
            'checklist'     => [
                'laporan_ada'     => $hasLaporan,
                'bap_ada'         => $hasBap,
                'penetapan_ada'   => $hasPenetapan,
                'penetapan_wajib' => ($kasus['status_penetapan'] === 'terbukti'),
            ],
        ];
    }

    /**
     * Menyimpan Tahap 1 Pencatatan Dugaan Pelanggaran secara atomik.
     *
     * @param array<string, mixed> $input
     * @param int|null             $id
     * @return array{status: bool, message: string, id?: int}
     */
    public function savePencatatan(array $input, ?int $id = null): array
    {
        $nomorPerkara = strtoupper(trim((string) ($input['nomor_perkara'] ?? '')));
        $nomorTaruna  = trim((string) ($input['nomor_taruna'] ?? ''));

        $tarunaRow = $this->db->table('master_taruna')
            ->select('nomor_taruna, nama_lengkap')
            ->where('nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$tarunaRow) {
            return ['status' => false, 'message' => 'Taruna dengan Nomor Taruna tersebut tidak ditemukan.'];
        }

        // Cek keunikan nomor perkara
        $dupQuery = $this->db->table('taruna_pelanggaran')->where('nomor_perkara', $nomorPerkara);
        if ($id !== null && $id > 0) {
            $dupQuery->where('id !=', $id);
        }
        if ($dupQuery->countAllResults() > 0) {
            return ['status' => false, 'message' => 'Nomor kasus sudah terdaftar. Gunakan nomor kasus lain.'];
        }

        $tglKejadian = trim((string) ($input['tanggal_kejadian'] ?? ''));
        $jamKejadian = trim((string) ($input['waktu_kejadian'] ?? '08:00'));
        if (!preg_match('/^\d{2}:\d{2}$/', $jamKejadian)) {
            $jamKejadian = '08:00';
        }
        $dateTimeKejadian = $tglKejadian . ' ' . $jamKejadian . ':00';
        $pelakuLabel      = trim((string) ($input['diajukan_oleh'] ?? 'PIC Ketarunaan'));

        $payload = [
            'nomor_perkara'       => $nomorPerkara,
            'nomor_taruna'        => $nomorTaruna,
            'tanggal_kejadian'    => $dateTimeKejadian,
            'lokasi'              => trim((string) ($input['lokasi'] ?? '')),
            'jenis_pelanggaran'   => trim((string) ($input['jenis_pelanggaran'] ?? 'Kedisiplinan')),
            'pelapor'             => trim((string) ($input['pelapor'] ?? 'Unit Ketarunaan')),
            'uraian'              => trim((string) ($input['uraian'] ?? '')),
            'unit_pemilik'        => trim((string) ($input['unit_pemilik'] ?? 'Ketarunaan')),
            'sumber_data'         => trim((string) ($input['sumber_data'] ?? 'Laporan petugas')),
            'tingkat_kerahasiaan' => in_array($input['tingkat_kerahasiaan'] ?? '', ['terbatas', 'sangat_terbatas'], true)
                ? (string) $input['tingkat_kerahasiaan']
                : 'terbatas',
            'diajukan_oleh'       => $pelakuLabel,
        ];

        try {
            $this->db->transStart();

            if ($id !== null && $id > 0) {
                $existing = $this->pelanggaranModel->find($id);
                if (!$existing) {
                    return ['status' => false, 'message' => 'Data kasus pelanggaran tidak ditemukan.'];
                }

                // UAT-17: Koreksi atas data tervalidasi dikembalikan ke draf agar diajukan validasi ulang
                $wasValidated = ($existing['status_validasi'] === 'tervalidasi');
                $statusBaru   = $wasValidated ? 'draf' : $existing['status_validasi'];
                $payload['status_validasi']       = $statusBaru;
                $payload['riwayat_validasi_json'] = $this->prependRiwayat(
                    $existing['riwayat_validasi_json'] ?? null,
                    $pelakuLabel,
                    $wasValidated ? 'Mengajukan koreksi' : 'Memperbarui draf',
                    $statusBaru,
                    'Pembaruan pencatatan awal kasus.'
                );

                $this->pelanggaranModel->update($id, $payload);
                $savedId = $id;
            } else {
                $payload['tingkat_pelanggaran']   = 'belum_ditetapkan';
                $payload['status_penetapan']      = 'dugaan';
                $payload['status_penyelesaian']   = 'proses';
                $payload['status_pemantauan']     = 'proses';
                $payload['status_validasi']       = 'draf';
                $payload['riwayat_validasi_json'] = $this->prependRiwayat(
                    null,
                    $pelakuLabel,
                    'Menyimpan Draf',
                    'draf',
                    'Laporan awal dugaan pelanggaran dicatat.'
                );

                $this->pelanggaranModel->insert($payload);
                $savedId = (int) $this->pelanggaranModel->getInsertID();

                // Catat otomatis pihak terlapor utama pada tabel taruna_pelanggaran_pihak
                $this->pihakModel->insert([
                    'pelanggaran_id' => $savedId,
                    'nomor_taruna'   => $tarunaRow['nomor_taruna'],
                    'nama_pihak'     => $tarunaRow['nama_lengkap'],
                    'jenis_pihak'    => 'taruna',
                    'peran'          => 'Terlapor',
                    'keterangan'     => 'Taruna utama dalam laporan kasus.',
                ]);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan pencatatan kasus. Silakan coba lagi.'];
            }

            return [
                'status'  => true,
                'message' => 'Informasi pencatatan kasus berhasil disimpan.',
                'id'      => $savedId,
            ];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::savePencatatan] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan pencatatan kasus. Silakan coba lagi.'];
        }
    }

    /**
     * Menyimpan Tahap 2 Pemeriksaan Kasus dan unggahan BAP opsional.
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @param string               $pelakuLabel
     * @return array{status: bool, message: string}
     */
    public function savePemeriksaan(int $id, array $input, string $pelakuLabel): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $tingkatAllowed = array_keys(ketarunaan_tingkat_pelanggaran_catalog());
        $tingkat        = trim((string) ($input['tingkat_pelanggaran'] ?? 'belum_ditetapkan'));
        if (!in_array($tingkat, $tingkatAllowed, true)) {
            $tingkat = 'belum_ditetapkan';
        }

        $payload = [
            'tanggal_pemeriksaan'   => trim((string) ($input['tanggal_pemeriksaan'] ?? '')) ?: date('Y-m-d'),
            'pemeriksa'             => trim((string) ($input['pemeriksa'] ?? 'Tim Pemeriksa Unit')),
            'dasar_peraturan'       => trim((string) ($input['dasar_peraturan'] ?? '')),
            'tingkat_pelanggaran'   => $tingkat,
            'ringkasan_klarifikasi' => trim((string) ($input['ringkasan_klarifikasi'] ?? '')),
            'hasil_pemeriksaan'     => trim((string) ($input['hasil_pemeriksaan'] ?? '')),
        ];

        $dokList = $this->decodeRawDokumenList($existing);
        if (!empty($input['berkas_bap'])) {
            $namaBap = (string) $input['berkas_bap'];
            $payload['berkas_bap'] = $namaBap;
            $dokList[] = [
                'nama_berkas'    => $namaBap,
                'jenis_dokumen'  => 'Pemeriksaan',
                'klasifikasi'    => 'sangat_terbatas',
                'tanggal_unggah' => date('Y-m-d'),
                'ukuran_kb'      => (int) ($input['bap_size_kb'] ?? 100),
            ];
            $payload['dokumen_bukti_json'] = json_encode($dokList, JSON_UNESCAPED_UNICODE);
        }

        $wasValidated = ($existing['status_validasi'] === 'tervalidasi');
        $statusBaru   = $wasValidated ? 'draf' : $existing['status_validasi'];
        $payload['status_validasi']       = $statusBaru;
        $payload['riwayat_validasi_json'] = $this->prependRiwayat(
            $existing['riwayat_validasi_json'] ?? null,
            $pelakuLabel,
            $wasValidated ? 'Mengajukan koreksi' : 'Memperbarui pemeriksaan',
            $statusBaru,
            'Data klarifikasi dan pemeriksaan diperbarui.'
        );

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, $payload);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan data pemeriksaan. Silakan coba lagi.'];
            }

            return ['status' => true, 'message' => 'Detail pemeriksaan kasus berhasil disimpan.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::savePemeriksaan] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan data pemeriksaan. Silakan coba lagi.'];
        }
    }

    /**
     * Menambahkan pihak terkait kasus (Taruna atau pihak eksternal tanpa memaksakan Nomor Taruna).
     *
     * @param int                  $pelanggaranId
     * @param array<string, mixed> $input
     * @return array{status: bool, message: string}
     */
    public function addPihak(int $pelanggaranId, array $input): array
    {
        $kasus = $this->pelanggaranModel->find($pelanggaranId);
        if (!$kasus) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $jenisPihak = ($input['jenis_pihak'] ?? 'taruna') === 'eksternal' ? 'eksternal' : 'taruna';
        $peran      = trim((string) ($input['peran'] ?? 'Saksi'));
        $keterangan = trim((string) ($input['keterangan'] ?? ''));

        if ($jenisPihak === 'taruna') {
            $nomorTaruna = trim((string) ($input['nomor_taruna'] ?? ''));
            if ($nomorTaruna === '') {
                return ['status' => false, 'message' => 'Pilih taruna terlebih dahulu.'];
            }
            $taruna = $this->db->table('master_taruna')
                ->select('nomor_taruna, nama_lengkap')
                ->where('nomor_taruna', $nomorTaruna)
                ->get()
                ->getRowArray();

            if (!$taruna) {
                return ['status' => false, 'message' => 'Data taruna tidak ditemukan.'];
            }

            $exists = $this->pihakModel
                ->where('pelanggaran_id', $pelanggaranId)
                ->where('nomor_taruna', $nomorTaruna)
                ->countAllResults();

            if ($exists > 0) {
                return ['status' => false, 'message' => 'Taruna tersebut sudah tercatat pada daftar pihak kasus.'];
            }

            $namaPihak = $taruna['nama_lengkap'];
            $noFinal   = $taruna['nomor_taruna'];
        } else {
            $namaPihak = trim((string) ($input['nama_pihak'] ?? ''));
            $noFinal   = null;
            if ($namaPihak === '') {
                return ['status' => false, 'message' => 'Nama pihak eksternal wajib diisi.'];
            }
        }

        try {
            $this->pihakModel->insert([
                'pelanggaran_id' => $pelanggaranId,
                'nomor_taruna'   => $noFinal,
                'nama_pihak'     => $namaPihak,
                'jenis_pihak'    => $jenisPihak,
                'peran'          => $peran !== '' ? $peran : 'Saksi',
                'keterangan'     => $keterangan ?: null,
            ]);

            return ['status' => true, 'message' => 'Pihak terkait berhasil ditambahkan.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::addPihak] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menambah pihak terkait. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus pihak terkait dari suatu kasus dengan validasi kepemilikan (IDOR prevention).
     *
     * @param int $pelanggaranId
     * @param int $pihakId
     * @return array{status: bool, message: string}
     */
    public function removePihak(int $pelanggaranId, int $pihakId): array
    {
        $row = $this->pihakModel->find($pihakId);
        if (!$row || (int) $row['pelanggaran_id'] !== $pelanggaranId) {
            return ['status' => false, 'message' => 'Data pihak kasus tidak ditemukan.'];
        }

        $kasus = $this->pelanggaranModel->find($pelanggaranId);
        if ($kasus && $row['nomor_taruna'] === $kasus['nomor_taruna'] && $row['peran'] === 'Terlapor') {
            return ['status' => false, 'message' => 'Terlapor utama kasus tidak dapat dihapus dari daftar pihak.'];
        }

        $this->pihakModel->delete($pihakId);
        return ['status' => true, 'message' => 'Pihak terkait berhasil dihapus.'];
    }

    /**
     * Menyimpan Tahap 3 Penetapan Hasil & Tindak Lanjut serta mempertahankan riwayat keputusan/banding.
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @param string               $pelakuLabel
     * @return array{status: bool, message: string}
     */
    public function savePenetapan(int $id, array $input, string $pelakuLabel): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $stPenetapan    = trim((string) ($input['status_penetapan'] ?? 'dugaan'));
        $stPenyelesaian = trim((string) ($input['status_penyelesaian'] ?? 'proses'));
        $tingkat        = trim((string) ($input['tingkat_pelanggaran'] ?? ($existing['tingkat_pelanggaran'] ?: 'belum_ditetapkan')));

        if (!in_array($stPenetapan, ['dugaan', 'terbukti', 'tidak_terbukti', 'dibatalkan'], true)) {
            return ['status' => false, 'message' => 'Status penetapan tidak sesuai ketentuan.'];
        }
        if (!in_array($stPenyelesaian, ['proses', 'selesai', 'banding'], true)) {
            return ['status' => false, 'message' => 'Status penyelesaian tidak sesuai ketentuan.'];
        }

        $tglKeputusan = trim((string) ($input['tanggal_keputusan'] ?? '')) ?: null;
        $tglSelesai   = ($stPenyelesaian === 'selesai')
            ? (trim((string) ($input['tanggal_selesai'] ?? '')) ?: date('Y-m-d'))
            : null;

        if ($tglKeputusan !== null && $tglSelesai !== null && $tglSelesai < $tglKeputusan) {
            return ['status' => false, 'message' => 'Tanggal selesai tidak boleh lebih awal dari tanggal keputusan.'];
        }

        $jenisSanksi   = trim((string) ($input['jenis_sanksi'] ?? ''));
        $tindakLanjut  = trim((string) ($input['tindak_lanjut'] ?? ''));
        $uraianSanksi  = trim((string) ($input['sanksi'] ?? '')) ?: $tindakLanjut;
        $catPemantauan = trim((string) ($input['catatan_pemantauan'] ?? ''))
            ?: ($stPenyelesaian === 'selesai' ? 'Pembinaan dan tindak lanjut selesai dilaksanakan.' : 'Dalam proses pelaksanaan tindak lanjut.');

        $wasValidated = ($existing['status_validasi'] === 'tervalidasi');
        $statusBaru   = $wasValidated ? 'draf' : $existing['status_validasi'];

        $payload = [
            'status_penetapan'      => $stPenetapan,
            'tingkat_pelanggaran'   => $tingkat,
            'dasar_peraturan'       => trim((string) ($input['dasar_peraturan'] ?? $existing['dasar_peraturan'])),
            'jenis_sanksi'          => $jenisSanksi,
            'sanksi'                => $uraianSanksi,
            'nomor_keputusan'       => strtoupper(trim((string) ($input['nomor_keputusan'] ?? ''))),
            'tanggal_keputusan'     => $tglKeputusan,
            'tindak_lanjut'         => $tindakLanjut,
            'status_penyelesaian'   => $stPenyelesaian,
            'status_pemantauan'     => $stPenyelesaian,
            'tanggal_selesai'       => $tglSelesai,
            'pic_tindak_lanjut'     => trim((string) ($input['pic_tindak_lanjut'] ?? 'Unit Ketarunaan')),
            'catatan_pemantauan'    => $catPemantauan,
            'status_validasi'       => $statusBaru,
            'riwayat_validasi_json' => $this->prependRiwayat(
                $existing['riwayat_validasi_json'] ?? null,
                $pelakuLabel,
                $wasValidated ? 'Mengajukan koreksi' : 'Memperbarui penetapan',
                $statusBaru,
                'Penetapan (' . ucwords(str_replace('_', ' ', $stPenetapan)) . ') dan penyelesaian (' . ucfirst($stPenyelesaian) . ') dicatat.'
            ),
        ];

        $jenisTindakan = match ($stPenyelesaian) {
            'banding' => 'banding',
            'selesai' => 'selesai',
            default   => 'penetapan',
        };

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, $payload);

            // Rekam baris baru agar keputusan/banding sebelumnya tetap utuh (PEL-04)
            $this->tindakLanjutModel->insert([
                'pelanggaran_id'               => $id,
                'tanggal_tindakan'             => $tglKeputusan ?: date('Y-m-d'),
                'jenis_tindakan'               => $jenisTindakan,
                'status_penetapan_saat_itu'    => $stPenetapan,
                'status_penyelesaian_saat_itu' => $stPenyelesaian,
                'nomor_dasar'                  => $payload['nomor_keputusan'] ?: null,
                'sanksi_tindakan'              => $tindakLanjut ?: ($jenisSanksi . ': ' . $catPemantauan),
                'pic'                          => $payload['pic_tindak_lanjut'],
                'target_selesai'               => $tglSelesai,
                'berkas_pendukung'             => null,
                'dicatat_oleh'                 => $pelakuLabel,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan penetapan kasus. Silakan coba lagi.'];
            }

            return ['status' => true, 'message' => 'Penetapan dan rencana tindak lanjut berhasil disimpan.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::savePenetapan] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan penetapan kasus. Silakan coba lagi.'];
        }
    }

    /**
     * Mencatat progres pemantauan atau pengajuan banding baru dari halaman Detail Kasus (PEL-04 & PEL-05).
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @param string               $pelakuLabel
     * @return array{status: bool, message: string}
     */
    public function addTindakLanjutRecord(int $id, array $input, string $pelakuLabel): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $stPenyelesaian = trim((string) ($input['status_penyelesaian'] ?? $existing['status_penyelesaian']));
        if (!in_array($stPenyelesaian, ['proses', 'selesai', 'banding'], true)) {
            return ['status' => false, 'message' => 'Status penyelesaian tidak sesuai ketentuan.'];
        }

        $tglTindakan   = trim((string) ($input['tanggal_tindakan'] ?? '')) ?: date('Y-m-d');
        $targetSelesai = trim((string) ($input['target_selesai'] ?? '')) ?: null;
        $uraian        = trim((string) ($input['sanksi_tindakan'] ?? ''));

        if ($uraian === '') {
            return ['status' => false, 'message' => 'Uraian tindak lanjut atau catatan pemantauan wajib diisi.'];
        }

        $jenisTindakan = match ($stPenyelesaian) {
            'banding' => 'banding',
            'selesai' => 'selesai',
            default   => 'pemantauan',
        };

        $updateKasus = [
            'status_penyelesaian' => $stPenyelesaian,
            'status_pemantauan'   => $stPenyelesaian,
            'pic_tindak_lanjut'   => trim((string) ($input['pic'] ?? $existing['pic_tindak_lanjut'])) ?: 'Unit Ketarunaan',
            'catatan_pemantauan'  => $uraian,
        ];
        if ($stPenyelesaian === 'selesai') {
            $updateKasus['tanggal_selesai'] = $targetSelesai ?: $tglTindakan;
        }

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, $updateKasus);

            $this->tindakLanjutModel->insert([
                'pelanggaran_id'               => $id,
                'tanggal_tindakan'             => $tglTindakan,
                'jenis_tindakan'               => $jenisTindakan,
                'status_penetapan_saat_itu'    => $existing['status_penetapan'],
                'status_penyelesaian_saat_itu' => $stPenyelesaian,
                'nomor_dasar'                  => trim((string) ($input['nomor_dasar'] ?? $existing['nomor_keputusan'])) ?: null,
                'sanksi_tindakan'              => $uraian,
                'pic'                          => $updateKasus['pic_tindak_lanjut'],
                'target_selesai'               => $targetSelesai,
                'berkas_pendukung'             => !empty($input['berkas_pendukung']) ? (string) $input['berkas_pendukung'] : null,
                'dicatat_oleh'                 => $pelakuLabel,
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal mencatat tindak lanjut. Silakan coba lagi.'];
            }

            return ['status' => true, 'message' => 'Catatan tindak lanjut berhasil ditambahkan.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::addTindakLanjutRecord] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal mencatat tindak lanjut. Silakan coba lagi.'];
        }
    }

    /**
     * Menyimpan unggahan dokumen bukti kasus (Tahap 4) dan mengajukan validasi bila diminta.
     *
     * @param int                  $id
     * @param array<string, mixed> $input
     * @return array{status: bool, message: string}
     */
    public function saveDokumenAndSubmit(int $id, array $input): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $dokList = $this->decodeRawDokumenList($existing);
        $update  = [];

        if (!empty($input['uploaded_doc']) && is_array($input['uploaded_doc'])) {
            $newDoc    = $input['uploaded_doc'];
            $dokList[] = $newDoc;
            $update['dokumen_bukti_json'] = json_encode($dokList, JSON_UNESCAPED_UNICODE);

            if (($newDoc['jenis_dokumen'] ?? '') === 'Pemeriksaan') {
                $update['berkas_bap'] = $newDoc['nama_berkas'];
            }
            if (($newDoc['jenis_dokumen'] ?? '') === 'Penetapan' || empty($existing['bukti_berkas'])) {
                $update['bukti_berkas'] = $newDoc['nama_berkas'];
            }
        }

        $aksiSubmit  = trim((string) ($input['aksi_submit'] ?? 'draf'));
        $pelakuLabel = trim((string) ($input['pelaku'] ?? 'PIC Ketarunaan'));

        if ($aksiSubmit === 'diajukan') {
            // Cek aturan pengajuan: laporan wajib ada; jika terbukti maka nomor keputusan wajib tersedia
            if (empty($existing['uraian']) || empty($existing['lokasi'])) {
                return ['status' => false, 'message' => 'Lengkapi uraian laporan awal pada Tahap 1 sebelum mengajukan validasi.'];
            }
            if ($existing['status_penetapan'] === 'terbukti' && empty($existing['nomor_keputusan'])) {
                return ['status' => false, 'message' => 'Isi nomor keputusan pada Tahap 3 sebelum mengajukan kasus berstatus Terbukti.'];
            }

            $update['status_validasi']       = 'diajukan';
            $update['catatan_validasi']      = null;
            $update['diajukan_oleh']         = $pelakuLabel;
            $update['riwayat_validasi_json'] = $this->prependRiwayat(
                $existing['riwayat_validasi_json'] ?? null,
                $pelakuLabel,
                ($existing['status_validasi'] === 'perlu_perbaikan') ? 'Mengajukan ulang' : 'Mengajukan',
                'diajukan',
                'Dokumen dan catatan kasus diajukan untuk validasi pencatatan.'
            );
            $msg = 'Kasus pelanggaran berhasil diajukan untuk validasi pencatatan.';
        } else {
            if (!empty($input['uploaded_doc'])) {
                $update['riwayat_validasi_json'] = $this->prependRiwayat(
                    $existing['riwayat_validasi_json'] ?? null,
                    $pelakuLabel,
                    'Menyimpan Dokumen',
                    (string) $existing['status_validasi'],
                    'Menambahkan berkas bukti kasus.'
                );
            }
            $msg = 'Dokumen bukti kasus berhasil disimpan sebagai draf.';
        }

        if (empty($update)) {
            return ['status' => true, 'message' => 'Tidak ada perubahan dokumen untuk disimpan.'];
        }

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, $update);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan dokumen kasus. Silakan coba lagi.'];
            }

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::saveDokumenAndSubmit] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menyimpan dokumen kasus. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus satu berkas bukti dari daftar dokumen kasus secara aman.
     *
     * @param int    $id
     * @param string $filename
     * @return array{status: bool, message: string}
     */
    public function removeDokumen(int $id, string $filename): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $target  = basename($filename);
        $dokList = $this->decodeRawDokumenList($existing);

        $filtered = [];
        $found    = false;
        foreach ($dokList as $doc) {
            if (basename((string) ($doc['nama_berkas'] ?? '')) === $target) {
                $found = true;
                continue;
            }
            $filtered[] = $doc;
        }

        if (!$found) {
            return ['status' => false, 'message' => 'Berkas dokumen tidak ditemukan.'];
        }

        $update = [
            'dokumen_bukti_json' => !empty($filtered) ? json_encode(array_values($filtered), JSON_UNESCAPED_UNICODE) : null,
        ];
        if (basename((string) ($existing['bukti_berkas'] ?? '')) === $target) {
            $update['bukti_berkas'] = !empty($filtered) ? $filtered[0]['nama_berkas'] : null;
        }
        if (basename((string) ($existing['berkas_bap'] ?? '')) === $target) {
            $update['berkas_bap'] = null;
        }

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, $update);
            $this->db->transComplete();

            $filePath = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . $target;
            if (is_file($filePath)) {
                @unlink($filePath);
            }

            return ['status' => true, 'message' => 'Berkas dokumen berhasil dihapus.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::removeDokumen] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghapus berkas. Silakan coba lagi.'];
        }
    }

    /**
     * Memproses pengesahan atau pengembalian pencatatan kasus oleh validator berwenang.
     *
     * @param int         $id
     * @param string      $keputusan
     * @param string|null $catatan
     * @param string      $validatorLabel
     * @return array{status: bool, message: string}
     */
    public function processValidation(int $id, string $keputusan, ?string $catatan, string $validatorLabel): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        $isValid      = ($keputusan === 'tervalidasi');
        $statusBaru   = $isValid ? 'tervalidasi' : 'perlu_perbaikan';
        $aksiLabel    = $isValid ? 'Menyetujui' : 'Mengembalikan';
        $catatanClean = $isValid
            ? (trim((string) $catatan) ?: 'Data dan dokumen pemeriksaan sesuai ketentuan.')
            : trim((string) $catatan);

        try {
            $this->db->transStart();
            $this->pelanggaranModel->update($id, [
                'status_validasi'       => $statusBaru,
                'catatan_validasi'      => $isValid ? null : $catatanClean,
                'divalidasi_oleh'       => $validatorLabel,
                'tanggal_validasi'      => date('Y-m-d'),
                'riwayat_validasi_json' => $this->prependRiwayat(
                    $existing['riwayat_validasi_json'] ?? null,
                    $validatorLabel,
                    $aksiLabel,
                    $statusBaru,
                    $catatanClean
                ),
            ]);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.'];
            }

            return [
                'status'  => true,
                'message' => $isValid
                    ? 'Pencatatan kasus pelanggaran berhasil divalidasi.'
                    : 'Pencatatan kasus dikembalikan untuk perbaikan.',
            ];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::processValidation] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.'];
        }
    }

    /**
     * Menghapus kasus pelanggaran yang masih berstatus draf atau perlu perbaikan.
     *
     * @param int $id
     * @return array{status: bool, message: string}
     */
    public function deleteDraftKasus(int $id): array
    {
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data kasus tidak ditemukan.'];
        }

        if ($existing['status_validasi'] === 'tervalidasi') {
            return ['status' => false, 'message' => 'Kasus yang sudah tervalidasi tidak dapat dihapus.'];
        }

        $dokList = $this->decodeRawDokumenList($existing);

        try {
            $this->db->transStart();
            $this->pihakModel->where('pelanggaran_id', $id)->delete();
            $this->tindakLanjutModel->where('pelanggaran_id', $id)->delete();
            $this->pelanggaranModel->delete($id);
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menghapus data kasus. Silakan coba lagi.'];
            }

            foreach ($dokList as $doc) {
                $path = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . basename((string) ($doc['nama_berkas'] ?? ''));
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            return ['status' => true, 'message' => 'Draf kasus pelanggaran berhasil dihapus.'];
        } catch (\Throwable $e) {
            log_message('error', '[PelanggaranService::deleteDraftKasus] ' . $e->getMessage());
            return ['status' => false, 'message' => 'Gagal menghapus data kasus. Silakan coba lagi.'];
        }
    }

    /**
     * Mengambil daftar dokumen terdekode tanpa flag izin akses (untuk mutasi internal).
     *
     * @param array<string, mixed> $kasus
     * @return array<int, array<string, mixed>>
     */
    private function decodeRawDokumenList(array $kasus): array
    {
        $decoded = json_decode((string) ($kasus['dokumen_bukti_json'] ?? '[]'), true);
        if (is_array($decoded) && !empty($decoded)) {
            return $decoded;
        }

        $fallback = [];
        $tglBase  = substr((string) ($kasus['tanggal_kejadian'] ?? date('Y-m-d')), 0, 10);

        if (!empty($kasus['bukti_berkas'])) {
            $fallback[] = [
                'nama_berkas'    => (string) $kasus['bukti_berkas'],
                'jenis_dokumen'  => ($kasus['status_penetapan'] === 'dugaan') ? 'Laporan' : 'Penetapan',
                'klasifikasi'    => (string) ($kasus['tingkat_kerahasiaan'] ?? 'terbatas'),
                'tanggal_unggah' => $tglBase,
                'ukuran_kb'      => 420,
            ];
        }
        if (!empty($kasus['berkas_bap'])) {
            $fallback[] = [
                'nama_berkas'    => (string) $kasus['berkas_bap'],
                'jenis_dokumen'  => 'Pemeriksaan',
                'klasifikasi'    => 'sangat_terbatas',
                'tanggal_unggah' => (string) ($kasus['tanggal_pemeriksaan'] ?: $tglBase),
                'ukuran_kb'      => 1120,
            ];
        }

        return $fallback;
    }

    /**
     * Mengambil daftar dokumen lengkap beserta status otorisasi unduhan pengguna saat ini.
     *
     * @param array<string, mixed> $kasus
     * @param bool                 $canViewFull
     * @return array<int, array<string, mixed>>
     */
    private function decodeDokumenList(array $kasus, bool $canViewFull): array
    {
        $rawList   = $this->decodeRawDokumenList($kasus);
        $canAccess = KetarunaanPolicy::canViewRestrictedDocument('pelanggaran', (string) ($kasus['nomor_taruna'] ?? ''));

        foreach ($rawList as &$doc) {
            $doc['klasifikasi'] = ($doc['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas' ? 'sangat_terbatas' : 'terbatas';
            $doc['can_access']  = $canViewFull && $canAccess;
        }
        unset($doc);

        return $rawList;
    }

    /**
     * Menyamarkan nama taruna untuk peran pemantau tanpa hak penuh rekam disiplin.
     */
    private function formatDisplayName(string $namaLengkap, int $kasusId, bool $canViewFull): string
    {
        if ($canViewFull) {
            return $namaLengkap !== '' ? $namaLengkap : '-';
        }
        $inisial = $namaLengkap !== '' ? strtoupper(substr(trim($namaLengkap), 0, 1)) : (string) $kasusId;

        return 'Taruna ' . $inisial;
    }

    /**
     * Menambahkan entri baru ke awal riwayat validasi JSON.
     */
    private function prependRiwayat(?string $existingJson, string $pelaku, string $aksi, string $status, string $catatan): string
    {
        $list = json_decode((string) ($existingJson ?? '[]'), true);
        if (!is_array($list)) {
            $list = [];
        }

        array_unshift($list, [
            'tanggal' => date('Y-m-d'),
            'waktu'   => date('Y-m-d H:i'),
            'pelaku'  => $pelaku,
            'aksi'    => $aksi,
            'status'  => $status,
            'catatan' => $catatan,
        ]);

        return (string) json_encode($list, JSON_UNESCAPED_UNICODE);
    }
}
