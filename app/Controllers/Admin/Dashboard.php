<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
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

    public function index()
    {
        $ajax_type = $this->request->getGet('ajax_type');
        $tahun_ecc = $this->request->getGet('tahun_ecc') ?? date('Y');

        $tahun_kinerja = $this->request->getGet('tahun_kinerja') ?? (string)date('Y');
        $bulan_kinerja = $this->request->getGet('bulan_kinerja') ?? (string)date('n');

        $user_id = session()->get('id') ?? session()->get('user_id');
        $role = session()->get('role');
        $canSeeAll = hasAnyRole(['admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk', 'kepegawaian']);

        // --- 1. PROSES DATA ECC ---
        $eccData = null;
        if (!$this->request->isAJAX() || $ajax_type === 'ecc') {
            $eccData = $this->getDashboardEccData($tahun_ecc);
            if ($this->request->isAJAX() && $ajax_type === 'ecc') {
                return $this->response->setJSON([
                    'prodiData' => $eccData['prodiData'],
                    'tahun' => $tahun_ecc
                ]);
            }
        }

        // --- 2. PROSES DATA KINERJA ---
        if (!$this->request->isAJAX() || $ajax_type === 'kinerja') {


        // ----------------------------------------------------------------
        // 1. DATA KINERJA HARIAN PEGAWAI & UNIT (SINGLE-PASS BATCH O(N))
        // ----------------------------------------------------------------
        $userSelect = 'id, nama_lengkap, nip, username, jabatan, unit, role, status_pegawai, tmt_selesai, foto';
        $userQuery = $this->userModel->select($userSelect)->where('role !=', 'admin');

        $adminPivotIds = array_column(
            $this->db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
            'user_id'
        );
        if (!empty($adminPivotIds)) {
            $userQuery->whereNotIn('id', $adminPivotIds);
        }

        if ($this->db->fieldExists('status_pegawai', 'users')) {
            if ($bulan_kinerja !== 'all' && !empty($bulan_kinerja)) {
                $periodMonth = sprintf('%04d-%02d-01', (int)$tahun_kinerja, (int)$bulan_kinerja);
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
                              ->where('YEAR(tmt_selesai) >=', (int)$tahun_kinerja)
                          ->groupEnd()
                          ->groupEnd();
            }
        }

        if ($canSeeAll) {
            $daftarSemuaUser = $userQuery->orderBy('nama_lengkap', 'ASC')->findAll();
        } else {
            $bulanCek = ($bulan_kinerja !== 'all' && !empty($bulan_kinerja)) ? (int)$bulan_kinerja : (int)date('n');
            $periodDate = sprintf('%04d-%02d-01', (int)$tahun_kinerja, $bulanCek);
            $daftarSemuaUser = $this->userModel->getAllStaf($user_id, $role, $periodDate);
            $me = $this->userModel->select($userSelect)->find($user_id);
            if ($me) {
                array_unshift($daftarSemuaUser, $me); // Sertakan pimpinan unit itu sendiri
            }
        }

        // Penyelarasan Jabatan & Unit Kerja Dinamis Berdasarkan Periode (Pindah Unit / Rotasi Jabatan)
        $bulanCek = ($bulan_kinerja !== 'all' && !empty($bulan_kinerja)) ? (int)$bulan_kinerja : (int)date('n');
        foreach ($daftarSemuaUser as &$staf) {
            $penugasan = $this->userModel->getPenugasanPeriode($staf['id'], $bulanCek, (int)$tahun_kinerja);
            if ($penugasan) {
                $staf['jabatan'] = $penugasan['jabatan'] ?: $staf['jabatan'];
                $staf['unit']    = $penugasan['unit'] ?: $staf['unit'];
            }
        }
        unset($staf);

        // BATCH LOAD: Ambil seluruh data target dan tugas tambahan tahun bersangkutan dalam 2 query efisien (in-memory O(1))
        [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun_kinerja);

        $rekapDashboard = [];
        $globalTotalDinilai = 0;
        $unitStats = [];
        
        // Single-pass: Sekaligus bangun rekap pegawai dan agregasi unit kerja
        foreach ($daftarSemuaUser as $staf) {
            $statPegawai = $this->hitungKinerjaPegawai($staf['id'], $bulan_kinerja, $tahun_kinerja, $batchTargets, $batchTambahan);
            $globalTotalDinilai += $statPegawai['dinilai'];
            
            $rekapDashboard[] = [
                'staf'           => $staf,
                'total_pokok'    => $statPegawai['total_pokok'],
                'total_tambahan' => $statPegawai['total_tambahan'],
                'total_laporan'  => $statPegawai['total_laporan'],
                'dinilai'        => $statPegawai['dinilai'],
                'rata_rata'      => $statPegawai['rata_rata'],
            ];

            $uName = trim($staf['unit'] ?? '') ?: 'Tanpa Unit';
            if (!isset($unitStats[$uName])) {
                $unitStats[$uName] = ['total_rata' => 0, 'count' => 0, 'anggota' => []];
            }
            $unitStats[$uName]['total_rata'] += $statPegawai['rata_rata'];
            $unitStats[$uName]['count']++;
            $unitStats[$uName]['anggota'][] = [
                'nama'           => $staf['nama_lengkap'],
                'jabatan'        => $staf['jabatan'] ?? '-',
                'unit'           => $uName,
                'total_pokok'    => $statPegawai['total_pokok'],
                'total_tambahan' => $statPegawai['total_tambahan'],
                'dinilai'        => $statPegawai['dinilai'],
                'total_laporan'  => $statPegawai['total_laporan'],
                'rata_rata'      => $statPegawai['rata_rata']
            ];
        }

        // ----------------------------------------------------------------
        // 2. TREN KINERJA BULANAN SELURUH PEGAWAI (1-12)
        // ----------------------------------------------------------------
        $trendBulananData = array_fill(0, 12, 0);
        $monthUserCounts = array_fill(0, 12, 0);
        $monthUserSums = array_fill(0, 12, 0);

        foreach ($daftarSemuaUser as $u) {
            $statYear = $this->hitungKinerjaPegawai($u['id'], 'all', $tahun_kinerja, $batchTargets, $batchTambahan);
            if (!empty($statYear['monthly_averages'])) {
                foreach ($statYear['monthly_averages'] as $mIdx => $mAvg) {
                    if ($mAvg !== null && $mAvg > 0) {
                        $monthUserCounts[$mIdx - 1]++;
                        $monthUserSums[$mIdx - 1] += $mAvg;
                    }
                }
            }
        }

        for ($i = 0; $i < 12; $i++) {
            if ($monthUserCounts[$i] > 0) {
                $trendBulananData[$i] = round($monthUserSums[$i] / $monthUserCounts[$i], 2);
            }
        }

        // ----------------------------------------------------------------
        // 3. ANALITIK SEBARAN PREDIKAT & TINGKAT PARTISIPASI
        // ----------------------------------------------------------------
        $sebaranKinerja = [
            'sangat_baik'     => 0,
            'baik'            => 0,
            'butuh_perbaikan' => 0,
            'kurang'          => 0,
            'sangat_kurang'   => 0,
            'belum_dinilai'   => 0,
            'belum_mengisi'   => 0
        ];
        $partisipasiAktif = 0;
        $totalPegawai = count($rekapDashboard);

        foreach ($rekapDashboard as $r) {
            $rr = (float)$r['rata_rata'];
            $dinilai = (int)($r['dinilai'] ?? 0);

            if ($dinilai > 0 && $rr > 0) {
                $partisipasiAktif++;
                if ($rr > 100) {
                    $sebaranKinerja['sangat_baik']++;
                } else if ($rr > 90) {
                    $sebaranKinerja['baik']++;
                } else if ($rr > 75) {
                    $sebaranKinerja['butuh_perbaikan']++;
                } else if ($rr > 25) {
                    $sebaranKinerja['kurang']++;
                } else {
                    $sebaranKinerja['sangat_kurang']++;
                }
            } else {
                $sebaranKinerja['belum_dinilai']++;
                $sebaranKinerja['belum_mengisi']++;
            }
        }

        // ----------------------------------------------------------------
        // 4. LEADERBOARD TOP 5 & PERLU PERHATIAN KHUSUS (BOTTOM 5)
        // ----------------------------------------------------------------
        $top5Data = array_values(array_filter($rekapDashboard, function($r) {
            return (float)$r['rata_rata'] > 0 && (int)($r['dinilai'] ?? 0) > 0;
        }));
        usort($top5Data, function($a, $b) {
            if ($b['rata_rata'] != $a['rata_rata']) {
                return $b['rata_rata'] <=> $a['rata_rata'];
            }
            if ($b['dinilai'] != $a['dinilai']) {
                return $b['dinilai'] <=> $a['dinilai'];
            }
            return $b['total_laporan'] <=> $a['total_laporan'];
        });
        $top5 = array_slice($top5Data, 0, 5);
        
        // Perlu Perhatian Khusus: Pegawai dengan nilai <= 25 (Sangat Kurang), tidak termasuk 0 / belum dinilai
        $bottom5Data = array_values(array_filter($rekapDashboard, function($r) {
            $rr = (float)$r['rata_rata'];
            $dinilai = (int)($r['dinilai'] ?? 0);
            return $dinilai > 0 && $rr > 0 && $rr <= 25;
        }));
        usort($bottom5Data, function($a, $b) {
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

        // ----------------------------------------------------------------
        // 5. PERANGKINGAN & RATA-RATA UNIT KERJA
        // ----------------------------------------------------------------
        $unitRanking = [];
        $totalSumPegawai = 0;
        $totalCountPegawai = 0;
        $rataRataValue = 0;
        $chartPegawaiUnitLabels = [];
        $chartPegawaiUnitData = [];

        if (!empty($unitStats)) {
            foreach ($unitStats as $unitName => &$unitData) {
                $unitTotal = 0;
                $unitTotalAktif = 0;
                $unitCountAktif = 0;
                $totalAnggota = $unitData['count'];

                if (isset($unitData['anggota'])) {
                    foreach ($unitData['anggota'] as $anggota) {
                        $unitTotal += $anggota['rata_rata'];
                        $totalSumPegawai += $anggota['rata_rata'];
                        $totalCountPegawai++;
                        if ($anggota['rata_rata'] > 0) {
                            $unitTotalAktif += $anggota['rata_rata'];
                            $unitCountAktif++;
                        }
                    }
                }

                $avg = $totalAnggota > 0 ? round($unitTotal / $totalAnggota, 2) : 0;
                $unitData['rata_rata_unit'] = $avg;
                $unitRanking[] = [
                    'nama'          => $unitName,
                    'rata'          => $avg,
                    'total_aktif'   => $unitCountAktif,
                    'total_anggota' => $totalAnggota
                ];
            }
            unset($unitData);

            $rataRataValue = $totalCountPegawai > 0 ? round($totalSumPegawai / $totalCountPegawai, 2) : 0;

            usort($unitRanking, function($a, $b) {
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
            $chartPegawaiUnitData[] = $ur['rata'];
        }

        if ($this->request->isAJAX() && $ajax_type === 'kinerja') {
            return $this->response->setJSON([
                'rataRataValue'          => $rataRataValue,
                'top5Unit'               => $top5Unit,
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
                'bulan_kinerja'          => $bulan_kinerja
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
     * API AJAX untuk drilldown detail modal chart
     */
    public function apiDetailChart()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        if (!hasAnyRole(['admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'spm', 'kepegawaian'])) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak. Anda tidak memiliki wewenang untuk melihat data ini.']);
        }

        $mode = $this->request->getVar('mode');
        $tahun = (int)($this->request->getVar('tahun') ?: date('Y'));
        $bulan = $this->request->getVar('bulan') ?: 'all';
        $key = $this->request->getVar('key') ?: $this->request->getVar('kategori');
        $normalizedKey = strtolower(str_replace(' ', '_', trim((string)$key)));

        [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun);

        $userBuilder = $this->userModel
            ->select('id, nama_lengkap, nip, username, jabatan, unit, role, status_pegawai, tmt_selesai')
            ->where('role !=', 'admin');

        $adminPivotIds = array_column(
            $this->db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
            'user_id'
        );
        if (!empty($adminPivotIds)) {
            $userBuilder->whereNotIn('id', $adminPivotIds);
        }

        if ($this->db->fieldExists('status_pegawai', 'users')) {
            if ($bulan !== 'all' && !empty($bulan)) {
                $periodMonth = sprintf('%04d-%02d-01', (int)$tahun, (int)$bulan);
                $userBuilder->groupStart()
                            ->where('status_pegawai', 'aktif')
                            ->orGroupStart()
                                ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                                ->where('tmt_selesai IS NOT NULL')
                                ->where('tmt_selesai >=', $periodMonth)
                            ->groupEnd()
                            ->groupEnd();
            } else {
                $userBuilder->groupStart()
                            ->where('status_pegawai', 'aktif')
                            ->orGroupStart()
                                ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                                ->where('tmt_selesai IS NOT NULL')
                                ->where('YEAR(tmt_selesai) >=', (int)$tahun)
                            ->groupEnd()
                            ->groupEnd();
            }
        }
        $allUsers = $userBuilder->findAll();

        $bulanCek = ($bulan !== 'all' && !empty($bulan)) ? (int)$bulan : (int)date('n');
        foreach ($allUsers as &$u) {
            $penugasan = $this->userModel->getPenugasanPeriode($u['id'], $bulanCek, $tahun);
            if ($penugasan) {
                $u['jabatan'] = $penugasan['jabatan'] ?: $u['jabatan'];
                $u['unit']    = $penugasan['unit'] ?: $u['unit'];
            }
        }
        unset($u);

        if ($mode === 'sebaran') {
            $result = [];
            
            foreach ($allUsers as $u) {
                $stat = $this->hitungKinerjaPegawai($u['id'], $bulan, $tahun, $batchTargets, $batchTambahan);
                $rata_rata = $stat['rata_rata'];
                $dinilai = $stat['dinilai'];
                
                $match = false;
                if (($normalizedKey === 'belum_dinilai' || $normalizedKey === 'belum_mengisi' || $normalizedKey === 'belum_ada_catatan') && ($dinilai == 0 || $rata_rata == 0)) {
                    $match = true;
                } else if ($dinilai > 0 && $rata_rata > 0) {
                    if ($normalizedKey === 'sangat_baik' && $rata_rata > 100) $match = true;
                    else if ($normalizedKey === 'baik' && $rata_rata > 90 && $rata_rata <= 100) $match = true;
                    else if ($normalizedKey === 'butuh_perbaikan' && $rata_rata > 75 && $rata_rata <= 90) $match = true;
                    else if ($normalizedKey === 'kurang' && $rata_rata > 25 && $rata_rata <= 75) $match = true;
                    else if ($normalizedKey === 'sangat_kurang' && $rata_rata <= 25) $match = true;
                }
                
                if ($match) {
                    $result[] = [
                        'nama'           => $u['nama_lengkap'],
                        'jabatan'        => $u['jabatan'] ?? '-',
                        'unit'           => $u['unit'] ?? '-',
                        'dinilai'        => $stat['dinilai'],
                        'total_laporan'  => $stat['total_laporan'],
                        'rata_rata'      => $rata_rata,
                        'status_pegawai' => $u['status_pegawai'] ?? 'aktif'
                    ];
                }
            }
            
            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
            
        } else if ($mode === 'tren') {
            $result = [];
            
            foreach ($allUsers as $u) {
                $stat = $this->hitungKinerjaPegawai($u['id'], $bulan, $tahun, $batchTargets, $batchTambahan);
                if ($stat['rata_rata'] > 0 || $stat['dinilai'] > 0) {
                    $result[] = [
                        'nama' => $u['nama_lengkap'],
                        'jabatan' => $u['jabatan'] ?? '-',
                        'unit' => $u['unit'] ?? '-',
                        'dinilai' => $stat['dinilai'],
                        'total_laporan' => $stat['total_laporan'],
                        'rata_rata' => $stat['rata_rata']
                    ];
                }
            }
            
            usort($result, function($a, $b) {
                return $b['rata_rata'] <=> $a['rata_rata'];
            });

            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid mode']);
    }
}