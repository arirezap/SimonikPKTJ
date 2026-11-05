<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\TarunaCapaianModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Service terpusat untuk logika bisnis Modul 2: Rekam Jejak Taruna (ECC 2.0).
 * Mematuhi prinsip Single Responsibility (SRP), DRY, dan Zero Duplicate Data.
 */
class RekamJejakService
{
    protected BaseConnection $db;
    protected TarunaCapaianModel $capaianModel;

    public function __construct(?BaseConnection $db = null, ?TarunaCapaianModel $capaianModel = null)
    {
        $this->db           = $db ?? Database::connect();
        $this->capaianModel = $capaianModel ?? new TarunaCapaianModel();
        helper('ketarunaan');
    }

    /**
     * Mengambil daftar referensi untuk dropdown filter dan formulir.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        $prodiList = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $angkatanList = $this->db->table('ref_angkatan')
            ->select('id, tahun_masuk, perkiraan_lulus')
            ->where('is_aktif', 1)
            ->orderBy('tahun_masuk', 'DESC')
            ->get()
            ->getResultArray();

        $tahunAkademikList = $this->db->table('ref_tahun_akademik')
            ->select('id, kode_tahun, tahun, semester, is_aktif')
            ->orderBy('tahun', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return [
            'prodi_list'          => $prodiList,
            'angkatan_list'       => $angkatanList,
            'tahun_akademik_list' => $tahunAkademikList,
            'status_catalog'      => ketarunaan_status_catalog(),
            'kategori_catalog'    => ketarunaan_capaian_kategori_catalog(),
        ];
    }

    /**
     * Mengambil data halaman Direktori Rekam Jejak Taruna beserta 4 metrik ringkasan.
     *
     * @param array<string, mixed> $filters
     * @param int                  $page
     * @param int                  $perPage
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $q        = trim((string) ($filters['q'] ?? ''));
        $prodi    = trim((string) ($filters['prodi'] ?? 'all'));
        $angkatan = trim((string) ($filters['angkatan'] ?? 'all'));
        $status   = trim((string) ($filters['status'] ?? 'all'));
        $periode  = trim((string) ($filters['periode'] ?? 'all'));

        // 1. Empat kartu metrik atas
        $tarunaTercatat = (int) $this->db->table('master_taruna')->countAllResults();

        $capaianTervalidasi = 0;
        $menungguValidasi   = 0;
        if ($this->db->tableExists('taruna_capaian')) {
            $capaianTervalidasi = (int) $this->db->table('taruna_capaian')
                ->where('status_validasi', 'tervalidasi')
                ->countAllResults();

            $menungguValidasi = (int) $this->db->table('taruna_capaian')
                ->where('status_validasi', 'diajukan')
                ->countAllResults();
        }

        $prestasiTervalidasi = (int) $this->db->table('taruna_prestasi')
            ->where('status_validasi', 'tervalidasi')
            ->countAllResults();

        // 2. Subkueri agregasi capaian dan prestasi per taruna
        $capaianSubSql = "(SELECT nomor_taruna, COUNT(*) AS jml_capaian, MAX(tanggal_kegiatan) AS tgl_capaian_terakhir
                           FROM taruna_capaian
                           WHERE status_validasi = 'tervalidasi'"
            . ($periode !== 'all' && ctype_digit($periode) ? ' AND tahun_akademik_id = ' . (int) $periode : '')
            . ' GROUP BY nomor_taruna) cap';

        $prestasiSubSql = "(SELECT tpp.nomor_taruna, COUNT(DISTINCT tpp.prestasi_id) AS jml_prestasi, MAX(tp.tanggal_mulai) AS tgl_prestasi_terakhir
                            FROM taruna_peserta_prestasi tpp
                            INNER JOIN taruna_prestasi tp ON tp.id = tpp.prestasi_id
                            WHERE tp.status_validasi = 'tervalidasi'
                            GROUP BY tpp.nomor_taruna) pres";

        $builder = $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, mt.status_terkini, mt.updated_at,
                      ps.kode_prodi, ps.nama_prodi, ang.tahun_masuk AS angkatan,
                      COALESCE(cap.jml_capaian, 0) AS jumlah_capaian,
                      COALESCE(pres.jml_prestasi, 0) AS jumlah_prestasi,
                      cap.tgl_capaian_terakhir, pres.tgl_prestasi_terakhir')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->join($capaianSubSql, 'cap.nomor_taruna = mt.nomor_taruna', 'left', false)
            ->join($prestasiSubSql, 'pres.nomor_taruna = mt.nomor_taruna', 'left', false);

        if ($q !== '') {
            $builder->groupStart()
                ->like('mt.nomor_taruna', $q)
                ->orLike('mt.nama_lengkap', $q)
                ->groupEnd();
        }
        if ($prodi !== '' && $prodi !== 'all') {
            $builder->where('ps.kode_prodi', $prodi);
        }
        if ($angkatan !== '' && $angkatan !== 'all') {
            $builder->where('ang.tahun_masuk', (int) $angkatan);
        }
        if ($status !== '' && $status !== 'all') {
            $builder->where('mt.status_terkini', $status);
        }
        if ($periode !== '' && $periode !== 'all' && ctype_digit($periode)) {
            $builder->where('cap.jml_capaian >', 0);
        }

        $totalFiltered = $builder->countAllResults(false);
        $totalPages    = max(1, (int) ceil($totalFiltered / $perPage));
        $page          = max(1, min($page, $totalPages));
        $offset        = ($page - 1) * $perPage;

        $rows = $builder
            ->orderBy('COALESCE(cap.jml_capaian, 0)', 'DESC', false)
            ->orderBy('mt.nomor_taruna', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $dates = array_filter([
                $row['tgl_capaian_terakhir'] ?? null,
                $row['tgl_prestasi_terakhir'] ?? null,
                !empty($row['updated_at']) ? substr((string) $row['updated_at'], 0, 10) : null,
            ]);
            $row['tanggal_pembaruan'] = !empty($dates) ? max($dates) : '2026-10-30';
        }
        unset($row);

        return [
            'metrics' => [
                'taruna_tercatat'      => $tarunaTercatat,
                'capaian_tervalidasi'  => $capaianTervalidasi,
                'prestasi_tervalidasi' => $prestasiTervalidasi,
                'menunggu_validasi'    => $menungguValidasi,
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
     * Mengambil seluruh data terintegrasi untuk 4 Tab Detail Rekam Jejak Taruna.
     *
     * @param string               $nomorTaruna
     * @param array<string, mixed> $filters
     * @param bool                 $canViewDiscipline
     * @param bool                 $canViewRestrictedBeasiswa
     * @return array<string, mixed>|null
     */
    public function getDetailRekamJejak(
        string $nomorTaruna,
        array $filters = [],
        bool $canViewDiscipline = false,
        bool $canViewRestrictedBeasiswa = false
    ): ?array {
        $taruna = $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, mt.nama_panggilan, mt.jenis_kelamin, mt.foto, mt.status_terkini,
                      pt.tingkat, pt.semester_aktif, pt.jalur_penerimaan, pt.asal_formasi,
                      ps.kode_prodi, ps.nama_prodi, ps.jenjang,
                      ang.tahun_masuk AS angkatan, kls.nama_kelas')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->join('ref_kelas kls', 'kls.id = pt.kelas_id', 'left')
            ->where('mt.nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$taruna) {
            return null;
        }

        // 1. Ambil seluruh Capaian taruna
        $allCapaian = $this->db->table('taruna_capaian tc')
            ->select('tc.*, ta.kode_tahun AS tahun_akademik_label')
            ->join('ref_tahun_akademik ta', 'ta.id = tc.tahun_akademik_id', 'left')
            ->where('tc.nomor_taruna', $nomorTaruna)
            ->orderBy('tc.tanggal_kegiatan', 'DESC')
            ->orderBy('tc.id', 'DESC')
            ->get()
            ->getResultArray();

        $capaianCounts = [
            'total'           => count($allCapaian),
            'tervalidasi'     => 0,
            'diajukan'        => 0,
            'perlu_perbaikan' => 0,
        ];
        $capaianTerbaru = [];

        foreach ($allCapaian as $cap) {
            $st = $cap['status_validasi'] ?? 'draf';
            if ($st === 'tervalidasi') {
                $capaianCounts['tervalidasi']++;
                if (count($capaianTerbaru) < 2) {
                    $capaianTerbaru[] = $cap;
                }
            } elseif ($st === 'diajukan') {
                $capaianCounts['diajukan']++;
            } elseif (in_array($st, ['perlu_perbaikan', 'dikembalikan'], true)) {
                $capaianCounts['perlu_perbaikan']++;
            }
        }

        // Filter daftar capaian untuk Tab 3
        $katFilterCap = trim((string) ($filters['kategori_capaian'] ?? 'all'));
        $perFilterCap = trim((string) ($filters['periode_capaian'] ?? 'all'));
        $valFilterCap = trim((string) ($filters['validasi_capaian'] ?? 'all'));

        $filteredCapaian = array_values(array_filter($allCapaian, static function (array $item) use ($katFilterCap, $perFilterCap, $valFilterCap): bool {
            if ($katFilterCap !== 'all' && $katFilterCap !== '' && $item['kategori'] !== $katFilterCap) {
                return false;
            }
            if ($perFilterCap !== 'all' && $perFilterCap !== '' && (string) ($item['tahun_akademik_id'] ?? '') !== $perFilterCap) {
                return false;
            }
            if ($valFilterCap !== 'all' && $valFilterCap !== '') {
                if ($valFilterCap === 'perlu_perbaikan' && !in_array($item['status_validasi'], ['perlu_perbaikan', 'dikembalikan'], true)) {
                    return false;
                }
                if ($valFilterCap !== 'perlu_perbaikan' && $item['status_validasi'] !== $valFilterCap) {
                    return false;
                }
            }
            return true;
        }));

        // 2. Ambil Riwayat Status Pendidikan & Riwayat Pendidikan
        $riwayatStatus = $this->db->table('riwayat_status_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->orderBy('mulai_berlaku', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        // 3. Ambil Prestasi Tervalidasi taruna
        $prestasiList = $this->db->table('taruna_peserta_prestasi tpp')
            ->select('tp.*, tpp.peran')
            ->join('taruna_prestasi tp', 'tp.id = tpp.prestasi_id', 'inner')
            ->where('tpp.nomor_taruna', $nomorTaruna)
            ->where('tp.status_validasi', 'tervalidasi')
            ->orderBy('tp.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();

        // 4. Ambil Beasiswa Ditetapkan taruna
        $beasiswaList = $this->db->table('taruna_penerima_beasiswa tpb')
            ->select('tpb.*, prog.nama_program, prog.jenis_beasiswa, prog.pemberi, prog.tahun')
            ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'inner')
            ->where('tpb.nomor_taruna', $nomorTaruna)
            ->where('tpb.status_penetapan', 'ditetapkan')
            ->where('tpb.status_validasi', 'tervalidasi')
            ->orderBy('tpb.tanggal_penetapan', 'DESC')
            ->get()
            ->getResultArray();

        // 5. Ambil Pelanggaran Terbukti (hanya jika pengguna punya otorisasi)
        $pelanggaranList = [];
        if ($canViewDiscipline) {
            $pelanggaranList = $this->db->table('taruna_pelanggaran')
                ->where('nomor_taruna', $nomorTaruna)
                ->where('status_penetapan', 'terbukti')
                ->where('status_validasi', 'tervalidasi')
                ->orderBy('tanggal_kejadian', 'DESC')
                ->get()
                ->getResultArray();
        }

        // 6. Susun Ringkasan Perjalanan (Tab 1) dari riwayat status tervalidasi
        $ringkasanPerjalanan = [];
        foreach ($riwayatStatus as $idx => $rs) {
            if (($rs['status_validasi'] ?? '') !== 'tervalidasi') {
                continue;
            }
            $kodeSt = $rs['kode_status'];
            $judulPerjalanan = match ($kodeSt) {
                'aktif'    => ($idx === 0) ? ('Registrasi awal ' . ($taruna['kode_prodi'] ?? '')) : 'Aktif kembali setelah cuti',
                'cuti'     => 'Cuti Akademik',
                'skorsing' => 'Skorsing Pembinaan',
                'lulus'    => 'Lulus Pendidikan',
                default    => ketarunaan_status_meta($kodeSt)['label'],
            };
            $kategoriPerjalanan = ($kodeSt === 'aktif') ? 'Pendidikan' : 'Status Pendidikan';

            $ringkasanPerjalanan[] = [
                'mulai_berlaku' => $rs['mulai_berlaku'],
                'akhir_berlaku' => $rs['akhir_berlaku'],
                'kategori'      => $kategoriPerjalanan,
                'judul'         => $judulPerjalanan,
                'uraian'        => $rs['alasan_perubahan'] ?: '-',
                'kode_status'   => $kodeSt,
            ];
        }

        // 7. Susun Kronologi Lintas Modul (Tab 2)
        $kronologiItems = [];

        foreach ($allCapaian as $cap) {
            $katMeta = ketarunaan_capaian_kategori_meta($cap['kategori']);
            $kronologiItems[] = [
                'tanggal'           => $cap['tanggal_kegiatan'],
                'tanggal_selesai'   => null,
                'modul'             => 'capaian',
                'kategori_kode'     => $cap['kategori'],
                'kategori_label'    => $katMeta['label'],
                'judul'             => $cap['nama_capaian'],
                'uraian'            => $cap['uraian_capaian'] ?: ('Penyelenggara: ' . $cap['penyelenggara']),
                'status_validasi'   => $cap['status_validasi'],
                'kode_status_didik' => null,
                'tahun_akademik_id' => (string) ($cap['tahun_akademik_id'] ?? ''),
                'detail_url'        => base_url('ketarunaan/rekam-jejak/capaian/' . $cap['id']),
                'action_label'      => 'Lihat Detail',
            ];
        }

        foreach ($riwayatStatus as $idx => $rs) {
            $kodeSt = $rs['kode_status'];
            $isAwalAtauKembali = ($kodeSt === 'aktif');
            $kronologiItems[] = [
                'tanggal'           => $rs['mulai_berlaku'],
                'tanggal_selesai'   => $rs['akhir_berlaku'],
                'modul'             => $isAwalAtauKembali ? 'pendidikan' : 'status_pendidikan',
                'kategori_kode'     => $isAwalAtauKembali ? 'pendidikan' : 'status_pendidikan',
                'kategori_label'    => $isAwalAtauKembali ? 'Pendidikan' : 'Status Pendidikan',
                'judul'             => ($idx === 0 && $kodeSt === 'aktif')
                    ? ('Registrasi awal ' . ($taruna['kode_prodi'] ?? ''))
                    : (($kodeSt === 'aktif') ? 'Aktif kembali setelah cuti' : ketarunaan_status_meta($kodeSt)['label']),
                'uraian'            => $rs['alasan_perubahan'] ?: '-',
                'status_validasi'   => $rs['status_validasi'],
                'kode_status_didik' => $kodeSt,
                'tahun_akademik_id' => '',
                'detail_url'        => base_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna) . '?tab=status'),
                'action_label'      => 'Lihat Riwayat Status',
            ];
        }

        foreach ($prestasiList as $pr) {
            $kronologiItems[] = [
                'tanggal'           => $pr['tanggal_mulai'],
                'tanggal_selesai'   => null,
                'modul'             => 'prestasi',
                'kategori_kode'     => 'prestasi',
                'kategori_label'    => 'Prestasi',
                'judul'             => $pr['nama_prestasi'],
                'uraian'            => sprintf('%s tingkat %s (%s).', $pr['peringkat'], ucfirst((string) $pr['tingkat_prestasi']), $pr['penyelenggara']),
                'status_validasi'   => $pr['status_validasi'],
                'kode_status_didik' => null,
                'tahun_akademik_id' => '',
                'detail_url'        => base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna) . '?tab=dokumen&modul=prestasi'),
                'action_label'      => 'Lihat Bukti',
            ];
        }

        foreach ($beasiswaList as $bs) {
            $kronologiItems[] = [
                'tanggal'           => $bs['tanggal_penetapan'] ?: '2025-10-01',
                'tanggal_selesai'   => null,
                'modul'             => 'beasiswa',
                'kategori_kode'     => 'beasiswa',
                'kategori_label'    => 'Beasiswa',
                'judul'             => $bs['nama_program'],
                'uraian'            => 'Penerima ' . $bs['nama_program'] . ' dari ' . $bs['pemberi'] . '.',
                'status_validasi'   => $bs['status_validasi'],
                'kode_status_didik' => null,
                'tahun_akademik_id' => '',
                'detail_url'        => base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna) . '?tab=dokumen&modul=beasiswa'),
                'action_label'      => 'Lihat Detail',
            ];
        }

        foreach ($pelanggaranList as $pl) {
            $kronologiItems[] = [
                'tanggal'           => substr((string) $pl['tanggal_kejadian'], 0, 10),
                'tanggal_selesai'   => null,
                'modul'             => 'pelanggaran',
                'kategori_kode'     => 'pelanggaran',
                'kategori_label'    => 'Pembinaan / Disiplin',
                'judul'             => $pl['jenis_pelanggaran'] . ' (' . ucfirst((string) $pl['tingkat_pelanggaran']) . ')',
                'uraian'            => $pl['uraian'],
                'status_validasi'   => $pl['status_validasi'],
                'kode_status_didik' => null,
                'tahun_akademik_id' => '',
                'detail_url'        => base_url('ketarunaan/pelanggaran'),
                'action_label'      => 'Buka Modul',
            ];
        }

        usort($kronologiItems, static function (array $a, array $b): int {
            return strcmp((string) $b['tanggal'], (string) $a['tanggal']);
        });

        $katFilterKron = trim((string) ($filters['kategori_kronologi'] ?? 'all'));
        $perFilterKron = trim((string) ($filters['periode_kronologi'] ?? 'all'));
        $valFilterKron = trim((string) ($filters['validasi_kronologi'] ?? 'all'));

        $filteredKronologi = array_values(array_filter($kronologiItems, static function (array $item) use ($katFilterKron, $perFilterKron, $valFilterKron): bool {
            if ($katFilterKron !== 'all' && $katFilterKron !== '' && $item['kategori_kode'] !== $katFilterKron) {
                return false;
            }
            if ($perFilterKron !== 'all' && $perFilterKron !== '' && $item['tahun_akademik_id'] !== '' && $item['tahun_akademik_id'] !== $perFilterKron) {
                return false;
            }
            if ($valFilterKron !== 'all' && $valFilterKron !== '' && $item['status_validasi'] !== $valFilterKron) {
                return false;
            }
            return true;
        }));

        // 8. Susun Dokumen Bukti Lintas Modul (Tab 4)
        $dokumenItems = [];

        foreach ($allCapaian as $cap) {
            if (empty($cap['bukti_berkas'])) {
                continue;
            }
            $dokumenItems[] = [
                'id_sumber'     => (int) $cap['id'],
                'nama_berkas'   => $cap['bukti_berkas'],
                'modul_sumber'  => 'capaian',
                'jenis_dokumen' => strtolower((string) ($cap['sumber_dokumen'] ?: 'sertifikat')),
                'kegiatan'      => $cap['nama_capaian'],
                'tanggal'       => $cap['tanggal_kegiatan'],
                'akses'         => 'internal',
                'can_access'    => true,
                'preview_url'   => base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $cap['id'] . '?inline=1'),
                'download_url'  => base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $cap['id']),
                'modul_url'     => base_url('ketarunaan/rekam-jejak/capaian/' . $cap['id']),
            ];
        }

        foreach ($prestasiList as $pr) {
            if (empty($pr['bukti_berkas'])) {
                continue;
            }
            $dokumenItems[] = [
                'id_sumber'     => (int) $pr['id'],
                'nama_berkas'   => $pr['bukti_berkas'],
                'modul_sumber'  => 'prestasi',
                'jenis_dokumen' => 'piagam',
                'kegiatan'      => $pr['nama_prestasi'],
                'tanggal'       => $pr['tanggal_mulai'],
                'akses'         => 'internal',
                'can_access'    => true,
                'preview_url'   => base_url('ketarunaan/rekam-jejak/unduh-bukti/prestasi/' . $pr['id'] . '?inline=1'),
                'download_url'  => base_url('ketarunaan/rekam-jejak/unduh-bukti/prestasi/' . $pr['id']),
                'modul_url'     => base_url('ketarunaan/prestasi'),
            ];
        }

        foreach ($beasiswaList as $bs) {
            $namaBerkasBs = !empty($bs['nomor_keputusan']) && str_ends_with(strtolower((string) $bs['nomor_keputusan']), '.pdf')
                ? $bs['nomor_keputusan']
                : 'Keputusan_Beasiswa.pdf';

            $dokumenItems[] = [
                'id_sumber'     => (int) $bs['id'],
                'nama_berkas'   => $namaBerkasBs,
                'modul_sumber'  => 'beasiswa',
                'jenis_dokumen' => 'surat_keputusan',
                'kegiatan'      => $bs['nama_program'],
                'tanggal'       => $bs['tanggal_penetapan'] ?: '2025-10-01',
                'akses'         => 'terbatas',
                'can_access'    => $canViewRestrictedBeasiswa,
                'preview_url'   => base_url('ketarunaan/rekam-jejak/unduh-bukti/beasiswa/' . $bs['id'] . '?inline=1'),
                'download_url'  => base_url('ketarunaan/rekam-jejak/unduh-bukti/beasiswa/' . $bs['id']),
                'modul_url'     => base_url('ketarunaan/beasiswa'),
            ];
        }

        usort($dokumenItems, static function (array $a, array $b): int {
            return strcmp((string) $b['tanggal'], (string) $a['tanggal']);
        });

        $modFilterDok = trim((string) ($filters['modul_dokumen'] ?? 'all'));
        $jnsFilterDok = trim((string) ($filters['jenis_dokumen'] ?? 'all'));
        $qFilterDok   = strtolower(trim((string) ($filters['q_dokumen'] ?? '')));

        $filteredDokumen = array_values(array_filter($dokumenItems, static function (array $doc) use ($modFilterDok, $jnsFilterDok, $qFilterDok): bool {
            if ($modFilterDok !== 'all' && $modFilterDok !== '' && $doc['modul_sumber'] !== $modFilterDok) {
                return false;
            }
            if ($jnsFilterDok !== 'all' && $jnsFilterDok !== '' && !str_contains($doc['jenis_dokumen'], strtolower($jnsFilterDok))) {
                return false;
            }
            if ($qFilterDok !== '' && !str_contains(strtolower($doc['nama_berkas'] . ' ' . $doc['kegiatan']), $qFilterDok)) {
                return false;
            }
            return true;
        }));

        return [
            'taruna'               => $taruna,
            'capaian_counts'       => $capaianCounts,
            'prestasi_count'       => count($prestasiList),
            'beasiswa_count'       => count($beasiswaList),
            'pelanggaran_count'    => count($pelanggaranList),
            'ringkasan_perjalanan' => $ringkasanPerjalanan,
            'capaian_terbaru'      => $capaianTerbaru,
            'kronologi_items'      => $filteredKronologi,
            'capaian_items'        => $filteredCapaian,
            'dokumen_items'        => $filteredDokumen,
        ];
    }

    /**
     * Mengambil satu rekaman capaian beserta identitas taruna terkait.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getCapaianDetail(int $id): ?array
    {
        $row = $this->db->table('taruna_capaian tc')
            ->select('tc.*, ta.kode_tahun AS tahun_akademik_label,
                      mt.nama_lengkap, mt.foto, mt.status_terkini,
                      ps.kode_prodi, ps.nama_prodi, ang.tahun_masuk AS angkatan')
            ->join('master_taruna mt', 'mt.nomor_taruna = tc.nomor_taruna', 'inner')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->join('ref_tahun_akademik ta', 'ta.id = tc.tahun_akademik_id', 'left')
            ->where('tc.id', $id)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Menyimpan capaian baru atau memperbarui capaian yang ada dalam transaksi atomik.
     *
     * @param array<string, mixed> $data
     * @param int|null             $id
     * @return array{status: bool, message: string, id?: int}
     */
    public function saveCapaian(array $data, ?int $id = null): array
    {
        $nomorTaruna = trim((string) ($data['nomor_taruna'] ?? ''));
        $tarunaExists = $this->db->table('master_taruna')
            ->select('nomor_taruna')
            ->where('nomor_taruna', $nomorTaruna)
            ->get()
            ->getRowArray();

        if (!$tarunaExists) {
            return [
                'status'  => false,
                'message' => 'Nomor Taruna tidak ditemukan dalam basis data.',
            ];
        }

        $nilaiRaw = trim((string) ($data['nilai_angka'] ?? ''));
        $nilaiSanitized = null;
        if ($nilaiRaw !== '') {
            $normalized = str_replace(',', '.', $nilaiRaw);
            if (is_numeric($normalized)) {
                $nilaiSanitized = round((float) $normalized, 2);
            }
        }

        $payload = [
            'nomor_taruna'      => $nomorTaruna,
            'kategori'          => trim((string) ($data['kategori'] ?? 'kompetensi_sertifikasi')),
            'nama_capaian'      => trim((string) ($data['nama_capaian'] ?? '')),
            'tanggal_kegiatan'  => trim((string) ($data['tanggal_kegiatan'] ?? date('Y-m-d'))),
            'tahun_akademik_id' => !empty($data['tahun_akademik_id']) ? (int) $data['tahun_akademik_id'] : null,
            'semester'          => max(1, min(8, (int) ($data['semester'] ?? 1))),
            'penyelenggara'     => trim((string) ($data['penyelenggara'] ?? '')),
            'tingkat_kegiatan'  => trim((string) ($data['tingkat_kegiatan'] ?? 'Kampus / Internal')),
            'uraian_capaian'    => trim((string) ($data['uraian_capaian'] ?? '')),
            'hasil_capaian'     => trim((string) ($data['hasil_capaian'] ?? 'Kompeten')),
            'nilai_angka'       => $nilaiSanitized,
            'satuan_nilai'      => trim((string) ($data['satuan_nilai'] ?? '')) ?: null,
            'nomor_dokumen'     => trim((string) ($data['nomor_dokumen'] ?? '')) ?: null,
            'unit_pemilik'      => trim((string) ($data['unit_pemilik'] ?? 'Ketarunaan')),
            'sumber_dokumen'    => trim((string) ($data['sumber_dokumen'] ?? 'Sertifikat')),
            'status_validasi'   => in_array(($data['status_validasi'] ?? ''), ['draf', 'diajukan', 'tervalidasi'], true)
                ? $data['status_validasi']
                : 'diajukan',
            'diajukan_oleh'     => trim((string) ($data['diajukan_oleh'] ?? 'PIC Ketarunaan')),
        ];

        if (!empty($data['bukti_berkas'])) {
            $payload['bukti_berkas']     = $data['bukti_berkas'];
            $payload['ukuran_berkas_kb'] = max(1, (int) ($data['ukuran_berkas_kb'] ?? 250));
            $payload['jumlah_berkas']    = 1;
        }

        if ($payload['status_validasi'] === 'diajukan') {
            $payload['catatan_validasi'] = null;
        }

        try {
            $this->db->transStart();

            if ($id !== null && $id > 0) {
                $this->capaianModel->update($id, $payload);
                $savedId = $id;
            } else {
                $this->capaianModel->insert($payload);
                $savedId = (int) $this->capaianModel->getInsertID();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'status'  => false,
                    'message' => 'Gagal menyimpan data capaian. Silakan coba lagi.',
                ];
            }

            return [
                'status'  => true,
                'message' => ($payload['status_validasi'] === 'draf')
                    ? 'Draf capaian berhasil disimpan.'
                    : 'Data capaian berhasil diajukan untuk validasi.',
                'id'      => $savedId,
            ];
        } catch (\Throwable $e) {
            log_message('error', '[RekamJejakService::saveCapaian] ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Gagal menyimpan data capaian. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Memproses pengesahan atau pengembalian data capaian oleh validator.
     *
     * @param int         $id
     * @param string      $keputusan
     * @param string|null $catatan
     * @param string      $validatorLabel
     * @return array{status: bool, message: string}
     */
    public function processValidation(int $id, string $keputusan, ?string $catatan, string $validatorLabel): array
    {
        $existing = $this->capaianModel->find($id);
        if (!$existing) {
            return [
                'status'  => false,
                'message' => 'Data capaian tidak ditemukan.',
            ];
        }

        $statusBaru = ($keputusan === 'tervalidasi') ? 'tervalidasi' : 'perlu_perbaikan';

        try {
            $this->db->transStart();

            $this->capaianModel->update($id, [
                'status_validasi'  => $statusBaru,
                'catatan_validasi' => ($statusBaru === 'perlu_perbaikan') ? trim((string) $catatan) : null,
                'divalidasi_oleh'  => $validatorLabel,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'status'  => false,
                    'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.',
                ];
            }

            return [
                'status'  => true,
                'message' => ($statusBaru === 'tervalidasi')
                    ? 'Capaian berhasil divalidasi.'
                    : 'Capaian dikembalikan untuk perbaikan.',
            ];
        } catch (\Throwable $e) {
            log_message('error', '[RekamJejakService::processValidation] ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Gagal memperbarui status validasi. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Mengambil daftar taruna ringkas untuk pilihan pada form tambah capaian.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTarunaDropdownList(): array
    {
        return $this->db->table('master_taruna mt')
            ->select('mt.nomor_taruna, mt.nama_lengkap, ps.kode_prodi, ang.tahun_masuk AS angkatan')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = mt.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_angkatan ang', 'ang.id = pt.angkatan_id', 'left')
            ->orderBy('mt.nomor_taruna', 'ASC')
            ->limit(150)
            ->get()
            ->getResultArray();
    }
}

