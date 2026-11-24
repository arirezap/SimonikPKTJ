<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UnitKerja;
use App\Controllers\Traits\EccDataTrait;
use App\Controllers\Traits\KinerjaBatchTrait;

class Dashboard extends BaseController
{
    use EccDataTrait, KinerjaBatchTrait;

    protected $db;
    protected $userModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new User();
    }

    /**
     * Memuat daftar pegawai sesuai cakupan peran dan menyelaraskan
     * jabatan/unit berdasarkan riwayat_atasan pada periode terkait (DRY).
     */
    private function loadScopedEmployees(bool $canSeeAll, $userId, ?string $role, $bulan, $tahun): array
    {
        $userSelect = 'id, nama_lengkap, nip, username, jabatan, unit, unit_id, role, status_pegawai, tmt_selesai, foto';
        $bulanCek   = ($bulan !== 'all' && !empty($bulan)) ? (int) $bulan : (int) date('n');
        $tahunInt   = (int) $tahun;

        if ($canSeeAll) {
            $userQuery = $this->userModel->select($userSelect)->where('role !=', 'admin');
            User::excludeSelfServiceFromQuery($userQuery);

            $adminPivotIds = array_column(
                $this->db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
                'user_id'
            );
            if (!empty($adminPivotIds)) {
                $userQuery->whereNotIn('id', $adminPivotIds);
            }

            if ($this->db->fieldExists('status_pegawai', 'users')) {
                if ($bulan !== 'all' && !empty($bulan)) {
                    $periodMonth = sprintf('%04d-%02d-01', $tahunInt, (int) $bulan);
                    $userQuery->groupStart()
                              ->where('status_pegawai', 'aktif')
                              ->orGroupStart()
                                  ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                                  ->where('tmt_selesai IS NOT NULL')
                                  ->where('tmt_selesai >=', $periodMonth)
                              ->groupEnd()
                              ->groupEnd();
                } else {
                    $userQuery->groupStart()
                              ->where('status_pegawai', 'aktif')
                              ->orGroupStart()
                                  ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                                  ->where('tmt_selesai IS NOT NULL')
                                  ->where('YEAR(tmt_selesai) >=', $tahunInt)
                              ->groupEnd()
                              ->groupEnd();
                }
            }

            $employees = $userQuery->orderBy('nama_lengkap', 'ASC')->findAll();
        } else {
            $periodDate = sprintf('%04d-%02d-01', $tahunInt, $bulanCek);
            $employees  = $this->userModel->getAllStaf($userId, $role, $periodDate);
            $me         = $this->userModel->select($userSelect)->find($userId);
            if ($me) {
                array_unshift($employees, $me);
            }
        }

        $penugasanMap = $this->userModel->getPenugasanPeriodeBatch(
            array_column($employees, 'id'),
            $bulanCek,
            $tahunInt
        );

        foreach ($employees as &$staf) {
            $penugasan = $penugasanMap[(int) $staf['id']] ?? null;
            if ($penugasan) {
                $staf['jabatan'] = !empty($penugasan['jabatan']) ? $penugasan['jabatan'] : $staf['jabatan'];
                $staf['unit']    = !empty($penugasan['unit']) ? $penugasan['unit'] : $staf['unit'];
                if (!empty($penugasan['unit_id'])) {
                    $staf['unit_id'] = (int) $penugasan['unit_id'];
                }
            }
        }
        unset($staf);

        return $employees;
    }

    /**
     * Membangun peta metadata hierarki unit kerja dari tabel unit_kerja
     * (penanggung jawab AAK/KUK/pimpinan, Unit Induk Bagian, dan Pokja -> Tim Substansi induk).
     */
    private function buildUnitHierarchyLookup(): array
    {
        $unitModel = new UnitKerja();
        $rows      = $unitModel->findAll();
        $byId      = [];
        foreach ($rows as $r) {
            $byId[(int) $r['id']] = $r;
        }

        $byLowerName = [];
        foreach ($rows as $r) {
            $nama     = trim((string) ($r['nama_unit'] ?? ''));
            $parent   = strtolower(trim((string) ($r['parent_unit'] ?? '')));
            $parentId = !empty($r['parent_unit_id']) ? (int) $r['parent_unit_id'] : null;
            $isPokja  = ($parentId !== null && isset($byId[$parentId]));

            $byLowerName[strtolower($nama)] = [
                'id'               => (int) $r['id'],
                'nama_unit'        => $nama,
                'bagian'           => $parent,
                'is_unit_induk'    => UnitKerja::isUnitIndukBagian($r),
                'is_pimpinan'      => ($parent === 'pimpinan'),
                'is_pokja'         => $isPokja,
                'parent_unit_name' => $isPokja ? (string) ($byId[$parentId]['nama_unit'] ?? '') : '',
            ];
        }

        return $byLowerName;
    }

    public function index()
    {
        $ajax_type = $this->request->getGet('ajax_type');
        $tahun_ecc = $this->request->getGet('tahun_ecc') ?? date('Y');

        $tahun_kinerja = $this->request->getGet('tahun_kinerja') ?? (string) date('Y');
        $bulan_kinerja = $this->request->getGet('bulan_kinerja') ?? (string) date('n');

        $user_id   = session()->get('id') ?? session()->get('user_id');
        $role      = session()->get('role');
        $canSeeAll = hasAnyRole(['admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk', 'kepegawaian']);

        // --- 1. PROSES DATA ECC ---
        $eccData = null;
        if (!$this->request->isAJAX() || $ajax_type === 'ecc') {
            $eccData = $this->getDashboardEccData($tahun_ecc);
            if ($this->request->isAJAX() && $ajax_type === 'ecc') {
                return $this->response->setJSON([
                    'prodiData' => $eccData['prodiData'],
                    'tahun'     => $tahun_ecc,
                ]);
            }
        }

        // --- 2. PROSES DATA KINERJA ---
        if (!$this->request->isAJAX() || $ajax_type === 'kinerja') {
            $daftarSemuaUser = $this->loadScopedEmployees($canSeeAll, $user_id, $role, $bulan_kinerja, $tahun_kinerja);
            $unitLookup      = $this->buildUnitHierarchyLookup();

            [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun_kinerja);

            $rekapDashboard     = [];
            $globalTotalDinilai = 0;
            $unitStats          = [];
            $trendBulananData   = array_fill(0, 12, null);
            $monthUserCounts    = array_fill(0, 12, 0);
            $monthUserSums      = array_fill(0, 12, 0);

            $orgBuckets = [
                'pimpinan' => [
                    'key'        => 'Top Manajemen',
                    'label'      => 'Top Manajemen',
                    'subtitle'   => 'Direktur & Wakil Direktur',
                    'bagian'     => 'pimpinan',
                    'total_rata' => 0.0,
                    'count'      => 0,
                    'aktif'      => 0,
                    'anggota'    => [],
                ],
                'aak' => [
                    'key'        => 'Bagian AAK',
                    'label'      => 'Bagian AAK',
                    'subtitle'   => 'Kabag AAK, unit pelaksana & Pokja AAK',
                    'bagian'     => 'aak',
                    'total_rata' => 0.0,
                    'count'      => 0,
                    'aktif'      => 0,
                    'anggota'    => [],
                ],
                'kuk' => [
                    'key'        => 'Bagian KUK',
                    'label'      => 'Bagian KUK',
                    'subtitle'   => 'Kabag KUK, unit pelaksana & Pokja KUK',
                    'bagian'     => 'kuk',
                    'total_rata' => 0.0,
                    'count'      => 0,
                    'aktif'      => 0,
                    'anggota'    => [],
                ],
            ];

            $totalSumPegawai   = 0.0;
            $totalCountPegawai = 0;

            foreach ($daftarSemuaUser as $staf) {
                $statPegawai = $this->hitungKinerjaPegawai($staf['id'], $bulan_kinerja, $tahun_kinerja, $batchTargets, $batchTambahan);
                $globalTotalDinilai += $statPegawai['dinilai'];

                $statYear = ($bulan_kinerja === 'all')
                    ? $statPegawai
                    : $this->hitungKinerjaPegawai($staf['id'], 'all', $tahun_kinerja, $batchTargets, $batchTambahan);
                if (!empty($statYear['monthly_averages'])) {
                    foreach ($statYear['monthly_averages'] as $mIdx => $mAvg) {
                        if ($mAvg !== null && $mAvg > 0 && $mIdx >= 1 && $mIdx <= 12) {
                            $monthUserCounts[$mIdx - 1]++;
                            $monthUserSums[$mIdx - 1] += $mAvg;
                        }
                    }
                }

                $rekapDashboard[] = [
                    'staf'           => $staf,
                    'total_pokok'    => $statPegawai['total_pokok'],
                    'total_tambahan' => $statPegawai['total_tambahan'],
                    'total_laporan'  => $statPegawai['total_laporan'],
                    'dinilai'        => $statPegawai['dinilai'],
                    'rata_rata'      => $statPegawai['rata_rata'],
                ];

                $totalSumPegawai += (float) $statPegawai['rata_rata'];
                $totalCountPegawai++;

                $uName = trim((string) ($staf['unit'] ?? '')) ?: 'Tanpa Unit';
                $uMeta = $unitLookup[strtolower($uName)] ?? [
                    'bagian'           => '',
                    'is_unit_induk'    => false,
                    'is_pimpinan'      => false,
                    'is_pokja'         => false,
                    'parent_unit_name' => '',
                ];

                $anggotaItem = [
                    'nama'             => $staf['nama_lengkap'],
                    'jabatan'          => $staf['jabatan'] ?? '-',
                    'unit'             => $uName,
                    'parent_unit_name' => $uMeta['parent_unit_name'],
                    'total_pokok'      => $statPegawai['total_pokok'],
                    'total_tambahan'   => $statPegawai['total_tambahan'],
                    'dinilai'          => $statPegawai['dinilai'],
                    'total_laporan'    => $statPegawai['total_laporan'],
                    'rata_rata'        => $statPegawai['rata_rata'],
                ];

                // 1. Agregasi ke 3 Ringkasan Struktur Induk (Top Manajemen, Bagian AAK, Bagian KUK)
                $bagianKey = $uMeta['bagian'];
                if ($canSeeAll && isset($orgBuckets[$bagianKey])) {
                    $orgBuckets[$bagianKey]['total_rata'] += (float) $statPegawai['rata_rata'];
                    $orgBuckets[$bagianKey]['count']++;
                    if ((float) $statPegawai['rata_rata'] > 0 && (int) $statPegawai['dinilai'] > 0) {
                        $orgBuckets[$bagianKey]['aktif']++;
                    }
                    $orgBuckets[$bagianKey]['anggota'][] = $anggotaItem;
                }

                // 2. Agregasi Unit Pelaksana & Kelompok Kerja (Pokja)
                // Untuk tampilan institusi ($canSeeAll), keluarkan Top Manajemen & Unit Induk Bagian (AAK/KUK)
                // agar tidak tercampur dengan peringkat unit pelaksana; Pokja TETAP dinilai mandiri.
                $skipFromUnitRanking = $canSeeAll && ($uMeta['is_pimpinan'] || $uMeta['is_unit_induk']);
                if ($skipFromUnitRanking) {
                    continue;
                }

                if (!isset($unitStats[$uName])) {
                    $unitStats[$uName] = [
                        'total_rata'       => 0.0,
                        'count'            => 0,
                        'bagian'           => $bagianKey,
                        'is_pokja'         => $uMeta['is_pokja'],
                        'parent_unit_name' => $uMeta['parent_unit_name'],
                        'is_org_group'     => false,
                        'anggota'          => [],
                    ];
                }
                $unitStats[$uName]['total_rata'] += (float) $statPegawai['rata_rata'];
                $unitStats[$uName]['count']++;
                $unitStats[$uName]['anggota'][] = $anggotaItem;
            }

            // --- Tren Bulanan ---
            for ($i = 0; $i < 12; $i++) {
                if ($monthUserCounts[$i] > 0) {
                    $trendBulananData[$i] = round($monthUserSums[$i] / $monthUserCounts[$i], 2);
                }
            }

            // --- Sebaran Predikat & Partisipasi ---
            $sebaranKinerja = [
                'sangat_baik'     => 0,
                'baik'            => 0,
                'butuh_perbaikan' => 0,
                'kurang'          => 0,
                'sangat_kurang'   => 0,
                'belum_dinilai'   => 0,
                'belum_mengisi'   => 0,
            ];
            $partisipasiAktif = 0;
            $totalPegawai     = count($rekapDashboard);

            foreach ($rekapDashboard as $r) {
                $rr      = (float) $r['rata_rata'];
                $dinilai = (int) ($r['dinilai'] ?? 0);

                if ($dinilai > 0 && $rr > 0) {
                    $partisipasiAktif++;
                    if ($rr > 100) {
                        $sebaranKinerja['sangat_baik']++;
                    } elseif ($rr > 90) {
                        $sebaranKinerja['baik']++;
                    } elseif ($rr > 75) {
                        $sebaranKinerja['butuh_perbaikan']++;
                    } elseif ($rr > 25) {
                        $sebaranKinerja['kurang']++;
                    } else {
                        $sebaranKinerja['sangat_kurang']++;
                    }
                } else {
                    $sebaranKinerja['belum_dinilai']++;
                    $sebaranKinerja['belum_mengisi']++;
                }
            }

            // --- Leaderboard Top 5 & Bottom 5 Pegawai ---
            $top5Data = array_values(array_filter($rekapDashboard, static function ($r) {
                return (float) $r['rata_rata'] > 0 && (int) ($r['dinilai'] ?? 0) > 0;
            }));
            usort($top5Data, static function ($a, $b) {
                if ($b['rata_rata'] != $a['rata_rata']) {
                    return $b['rata_rata'] <=> $a['rata_rata'];
                }
                if ($b['dinilai'] != $a['dinilai']) {
                    return $b['dinilai'] <=> $a['dinilai'];
                }
                return $b['total_laporan'] <=> $a['total_laporan'];
            });
            $top5 = array_slice($top5Data, 0, 5);

            $bottom5Data = array_values(array_filter($rekapDashboard, static function ($r) {
                $rr      = (float) $r['rata_rata'];
                $dinilai = (int) ($r['dinilai'] ?? 0);
                return $dinilai > 0 && $rr > 0 && $rr <= 25;
            }));
            usort($bottom5Data, static function ($a, $b) {
                if ($a['rata_rata'] != $b['rata_rata']) {
                    return $a['rata_rata'] <=> $b['rata_rata'];
                }
                if ($a['dinilai'] != $b['dinilai']) {
                    return $a['dinilai'] <=> $b['dinilai'];
                }
                if ($a['total_laporan'] != $b['total_laporan']) {
                    return $a['total_laporan'] <=> $b['total_laporan'];
                }
                return strcasecmp($a['staf']['nama_lengkap'] ?? '', $b['staf']['nama_lengkap'] ?? '');
            });
            $bottom5 = array_slice($bottom5Data, 0, 5);

            // --- Rata-Rata Global & Perangkingan Unit Pelaksana + Pokja ---
            $rataRataValue          = $totalCountPegawai > 0 ? round($totalSumPegawai / $totalCountPegawai, 2) : 0;
            $unitRanking            = [];
            $chartPegawaiUnitLabels = [];
            $chartPegawaiUnitData   = [];

            if (!empty($unitStats)) {
                foreach ($unitStats as $unitName => &$unitData) {
                    $unitTotal      = 0.0;
                    $unitCountAktif = 0;
                    $totalAnggota   = (int) $unitData['count'];

                    foreach ($unitData['anggota'] as $anggota) {
                        $unitTotal += (float) $anggota['rata_rata'];
                        if ((float) $anggota['rata_rata'] > 0) {
                            $unitCountAktif++;
                        }
                    }

                    $avg = $totalAnggota > 0 ? round($unitTotal / $totalAnggota, 2) : 0;
                    $unitData['rata_rata_unit'] = $avg;

                    $unitRanking[] = [
                        'nama'             => $unitName,
                        'rata'             => $avg,
                        'total_aktif'      => $unitCountAktif,
                        'total_anggota'    => $totalAnggota,
                        'bagian'           => $unitData['bagian'] ?? '',
                        'is_pokja'         => !empty($unitData['is_pokja']),
                        'parent_unit_name' => $unitData['parent_unit_name'] ?? '',
                    ];
                }
                unset($unitData);

                usort($unitRanking, static function ($a, $b) {
                    if ($b['rata'] != $a['rata']) {
                        return $b['rata'] <=> $a['rata'];
                    }
                    if ($b['total_aktif'] != $a['total_aktif']) {
                        return $b['total_aktif'] <=> $a['total_aktif'];
                    }
                    return strcasecmp($a['nama'], $b['nama']);
                });
            }

            $top5Unit = array_slice($unitRanking, 0, 5);

            foreach ($unitRanking as $ur) {
                $chartPegawaiUnitLabels[] = $ur['nama'];
                $chartPegawaiUnitData[]   = $ur['rata'];
            }

            // --- Finalisasi 3 Kartu Ringkasan Struktur Induk + Sertakan di cache modal ---
            $orgSummary = [];
            if ($canSeeAll) {
                foreach (['pimpinan', 'aak', 'kuk'] as $bk) {
                    $b   = $orgBuckets[$bk];
                    $cnt = (int) $b['count'];
                    $avg = $cnt > 0 ? round($b['total_rata'] / $cnt, 2) : 0;

                    $orgSummary[] = [
                        'key'           => $b['key'],
                        'label'         => $b['label'],
                        'subtitle'      => $b['subtitle'],
                        'bagian'        => $b['bagian'],
                        'rata'          => $avg,
                        'total_aktif'   => (int) $b['aktif'],
                        'total_anggota' => $cnt,
                    ];

                    $unitStats[$b['key']] = [
                        'total_rata'       => $b['total_rata'],
                        'count'            => $cnt,
                        'rata_rata_unit'   => $avg,
                        'bagian'           => $b['bagian'],
                        'is_pokja'         => false,
                        'parent_unit_name' => '',
                        'is_org_group'     => true,
                        'group_subtitle'   => $b['subtitle'],
                        'anggota'          => $b['anggota'],
                    ];
                }
            }

            if ($this->request->isAJAX() && $ajax_type === 'kinerja') {
                return $this->response->setJSON([
                    'rataRataValue'          => $rataRataValue,
                    'top5Unit'               => $top5Unit,
                    'orgSummary'             => $orgSummary,
                    'unitRanking'            => $unitRanking,
                    'globalTotalDinilai'     => $globalTotalDinilai,
                    'top5'                   => $top5,
                    'bottom5'                => $bottom5,
                    'trendBulananData'       => $trendBulananData,
                    'sebaranKinerja'         => $sebaranKinerja,
                    'partisipasiAktif'       => $partisipasiAktif,
                    'totalPegawai'           => $totalPegawai,
                    'isSuper'                => $canSeeAll,
                    'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels,
                    'chartPegawaiUnitData'   => $chartPegawaiUnitData,
                    'unitStats'              => $unitStats,
                    'tahun_kinerja'          => $tahun_kinerja,
                    'bulan_kinerja'          => $bulan_kinerja,
                ]);
            }
        }

        $data = [
            'page_title'             => 'Dashboard',
            'tahun_ecc'              => $tahun_ecc,
            'tahun_kinerja'          => $tahun_kinerja,
            'bulan_kinerja'          => $bulan_kinerja,
            'daftar_tahun'           => $eccData['daftar_tahun'] ?? [date('Y')],
            'rataRataValue'          => $rataRataValue ?? 0,
            'top5Unit'               => $top5Unit ?? [],
            'orgSummary'             => $orgSummary ?? [],
            'unitRanking'            => $unitRanking ?? [],
            'prodiData'              => $eccData['prodiData'] ?? [],
            'globalTotalDinilai'     => $globalTotalDinilai ?? 0,
            'top5'                   => $top5 ?? [],
            'bottom5'                => $bottom5 ?? [],
            'trendBulananData'       => $trendBulananData ?? [],
            'sebaranKinerja'         => $sebaranKinerja ?? ['sangat_baik' => 0, 'baik' => 0, 'butuh_perbaikan' => 0, 'kurang' => 0, 'sangat_kurang' => 0],
            'partisipasiAktif'       => $partisipasiAktif ?? 0,
            'totalPegawai'           => $totalPegawai ?? 0,
            'isSuper'                => $canSeeAll ?? false,
            'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels ?? [],
            'chartPegawaiUnitData'   => $chartPegawaiUnitData ?? [],
            'unitStats'              => $unitStats ?? [],
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * API AJAX untuk drilldown detail modal chart (Sebaran & Tren).
     */
    public function apiDetailChart()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        if (!hasAnyRole(['admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'spm', 'kepegawaian'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki wewenang untuk melihat data ini.']);
        }

        $mode          = $this->request->getVar('mode');
        $tahun         = (int) ($this->request->getVar('tahun') ?: date('Y'));
        $bulan         = $this->request->getVar('bulan') ?: 'all';
        $key           = $this->request->getVar('key') ?: $this->request->getVar('kategori');
        $normalizedKey = strtolower(str_replace(' ', '_', trim((string) $key)));

        $user_id   = session()->get('id') ?? session()->get('user_id');
        $role      = session()->get('role');
        $canSeeAll = hasAnyRole(['admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk', 'kepegawaian']);

        [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun);
        $allUsers   = $this->loadScopedEmployees($canSeeAll, $user_id, $role, $bulan, $tahun);
        $unitLookup = $this->buildUnitHierarchyLookup();

        $formatUnitDisplay = static function (?string $rawUnit) use ($unitLookup): string {
            $u = trim((string) $rawUnit);
            if ($u === '') {
                return '-';
            }
            $meta = $unitLookup[strtolower($u)] ?? null;
            if ($meta && !empty($meta['is_pokja']) && !empty($meta['parent_unit_name'])) {
                return $u . ' (' . $meta['parent_unit_name'] . ')';
            }
            return $u;
        };

        if ($mode === 'sebaran') {
            $result = [];

            foreach ($allUsers as $u) {
                $stat      = $this->hitungKinerjaPegawai($u['id'], $bulan, $tahun, $batchTargets, $batchTambahan);
                $rata_rata = $stat['rata_rata'];
                $dinilai   = $stat['dinilai'];

                $match = false;
                if (($normalizedKey === 'belum_dinilai' || $normalizedKey === 'belum_mengisi' || $normalizedKey === 'belum_ada_catatan') && ($dinilai == 0 || $rata_rata == 0)) {
                    $match = true;
                } elseif ($dinilai > 0 && $rata_rata > 0) {
                    if ($normalizedKey === 'sangat_baik' && $rata_rata > 100) {
                        $match = true;
                    } elseif ($normalizedKey === 'baik' && $rata_rata > 90 && $rata_rata <= 100) {
                        $match = true;
                    } elseif ($normalizedKey === 'butuh_perbaikan' && $rata_rata > 75 && $rata_rata <= 90) {
                        $match = true;
                    } elseif ($normalizedKey === 'kurang' && $rata_rata > 25 && $rata_rata <= 75) {
                        $match = true;
                    } elseif ($normalizedKey === 'sangat_kurang' && $rata_rata <= 25) {
                        $match = true;
                    }
                }

                if ($match) {
                    $result[] = [
                        'nama'           => $u['nama_lengkap'],
                        'jabatan'        => $u['jabatan'] ?? '-',
                        'unit'           => $formatUnitDisplay($u['unit'] ?? ''),
                        'dinilai'        => $stat['dinilai'],
                        'total_laporan'  => $stat['total_laporan'],
                        'rata_rata'      => $rata_rata,
                        'status_pegawai' => $u['status_pegawai'] ?? 'aktif',
                    ];
                }
            }

            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
        } elseif ($mode === 'tren') {
            $result = [];

            foreach ($allUsers as $u) {
                $stat = $this->hitungKinerjaPegawai($u['id'], $bulan, $tahun, $batchTargets, $batchTambahan);
                if ($stat['rata_rata'] > 0 || $stat['dinilai'] > 0) {
                    $result[] = [
                        'nama'           => $u['nama_lengkap'],
                        'jabatan'        => $u['jabatan'] ?? '-',
                        'unit'           => $formatUnitDisplay($u['unit'] ?? ''),
                        'dinilai'        => $stat['dinilai'],
                        'total_laporan'  => $stat['total_laporan'],
                        'rata_rata'      => $stat['rata_rata'],
                        'status_pegawai' => $u['status_pegawai'] ?? 'aktif',
                    ];
                }
            }

            usort($result, static function ($a, $b) {
                return $b['rata_rata'] <=> $a['rata_rata'];
            });

            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
    }
}
