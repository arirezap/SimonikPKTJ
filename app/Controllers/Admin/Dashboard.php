<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\RencanaKinerja;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Controllers\Traits\EccDataTrait;
use App\Controllers\Traits\KinerjaBatchTrait;

class Dashboard extends BaseController
{
    use EccDataTrait, KinerjaBatchTrait;
    
    protected $db;
    protected $userModel;
    protected $rencanaModel;
    protected $sasaranModel;
    protected $indikatorModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new User();
        $this->rencanaModel = new RencanaKinerja();
        $this->sasaranModel = new Sasaran();
        $this->indikatorModel = new Indikator();
        // Model LED dihapus dari constructor karena ada di Trait
    }

    // Helper private untuk menangani format data realisasi
    private function parseRealisasi($data)
    {
        if (is_string($data)) {
            return json_decode($data, true) ?? [];
        }
        return is_array($data) ? $data : [];
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
        // 1. DATA UNTUK SUMMARY CARDS & AGREGASI INDIKATOR
        // ----------------------------------------------------------------
        $rencanaQuery = $this->rencanaModel
            ->select('rencana_kinerja.*')
            ->join('users', 'users.id = rencana_kinerja.user_id')
            ->where('users.role !=', 'admin') 
            ->where('rencana_kinerja.tahun_anggaran', $tahun_kinerja);
            
        $subordinateIds = [];
        if (!$canSeeAll) {
            $subordinates = $this->userModel->getAllStaf($user_id, $role);
            $subordinateIds = array_column($subordinates, 'id');
            $subordinateIds[] = $user_id; // Sertakan data atasan itu sendiri
            $rencanaQuery->whereIn('users.id', $subordinateIds);
        }
        $allRencana = $rencanaQuery->findAll();

        $totalPersen = 0;
        $countRencana = 0;
        
        $indikatorData = []; 

        foreach ($allRencana as $row) {
            $target = (float) $row['target_utama'];
            $realisasi_bulanan = $this->parseRealisasi($row['realisasi_bulanan'] ?? []);

            $realisasi = 0;
            if ($bulan_kinerja === 'all') {
                $realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
            } else {
                $idx = (int)$bulan_kinerja - 1;
                $realisasi = isset($realisasi_bulanan[$idx]) ? (float)$realisasi_bulanan[$idx] : 0;
            }

            $capaian = ($target > 0) ? ($realisasi / $target) * 100 : 0;
            
            $totalPersen += $capaian;
            $countRencana++;

            // AGREGASI DATA PER INDIKATOR
            $namaIndikator = $row['indikator_kinerja'];
            $satuan = $row['satuan'] ?? ''; 

            if (!isset($indikatorData[$namaIndikator])) {
                $indikatorData[$namaIndikator] = [
                    'target' => 0, 
                    'realisasi' => 0,
                    'satuan' => $satuan
                ];
            }
            $indikatorData[$namaIndikator]['target'] += $target;
            $indikatorData[$namaIndikator]['realisasi'] += $realisasi;
        }

        $rataRataCapaianGlobal = ($countRencana > 0) ? $totalPersen / $countRencana : 0;
        $totalIndikator = count($indikatorData);

        // ----------------------------------------------------------------
        // 2. DATA UNTUK GRAFIK PERBANDINGAN USER (TIM/UNIT)
        // ----------------------------------------------------------------
        $userQuery = $this->userModel->where('role !=', 'admin');
        if (!$canSeeAll) {
            $userQuery->whereIn('id', $subordinateIds);
        }
        $users = $userQuery->findAll();
        $chartUserLabels = [];
        $chartUserData = [];
        $kinerja_per_user = [];

        foreach ($users as $u) {
            $rencanaUser = array_filter($allRencana, function($item) use ($u) {
                return $item['user_id'] == $u['id'];
            });

            if (empty($rencanaUser)) continue;

            $userTotalCapaian = 0;
            $userItemCount = 0;
            $userTotalTarget = 0;
            $userTotalRealisasi = 0;

            foreach ($rencanaUser as $r) {
                $t = (float) $r['target_utama'];
                $rb = $this->parseRealisasi($r['realisasi_bulanan'] ?? []);
                
                $real = 0;
                if ($bulan_kinerja === 'all') {
                    $real = array_sum(array_map('floatval', $rb));
                } else {
                    $idx = (int)$bulan_kinerja - 1;
                    $real = isset($rb[$idx]) ? (float)$rb[$idx] : 0;
                }

                $c = ($t > 0) ? ($real / $t) * 100 : 0;
                
                $userTotalCapaian += $c;
                $userItemCount++;
                $userTotalTarget += $t;
                $userTotalRealisasi += $real;
            }

            $avgCapaian = ($userItemCount > 0) ? $userTotalCapaian / $userItemCount : 0;

            $chartUserLabels[] = $u['nama_lengkap'];
            $chartUserData[] = round($avgCapaian, 2);

            $kinerja_per_user[] = [
                'nama' => $u['nama_lengkap'],
                'jumlah_indikator' => $userItemCount,
                'total_target' => $userTotalTarget,
                'total_realisasi' => $userTotalRealisasi,
                'persentase_capaian' => $avgCapaian
            ];
        }

        // ----------------------------------------------------------------
        // 3. SIAPKAN DATA GRAFIK INDIKATOR
        // ----------------------------------------------------------------
        ksort($indikatorData, SORT_NATURAL);

        $chartIndikatorLabels = [];
        $chartIndikatorPersen = [];
        $chartIndikatorMeta = [];

        foreach ($indikatorData as $k => $v) {
            $chartIndikatorLabels[] = $k;
            $persen = ($v['target'] > 0) ? ($v['realisasi'] / $v['target'] * 100) : 0;
            $chartIndikatorPersen[] = round($persen, 2);
            $chartIndikatorMeta[] = [
                'target' => $v['target'],
                'realisasi' => $v['realisasi'],
                'satuan' => $v['satuan']
            ];
        }

        // ----------------------------------------------------------------
        // DATA ECC DIPINDAH KE ATAS
        // ----------------------------------------------------------------

        // ----------------------------------------------------------------
        // 5. DATA KINERJA HARIAN PEGAWAI PER UNIT (KHUSUS DIREKTUR/WADIR DLL)
        // ----------------------------------------------------------------
        if ($canSeeAll) {
            $daftarSemuaUser = $this->userModel->where('role !=', 'admin')
                                               ->where('id !=', $user_id)
                                               ->orderBy('nama_lengkap', 'ASC')
                                               ->findAll();
        } else {
            $daftarSemuaUser = $this->userModel->getAllStaf($user_id, $role);
            $me = $this->userModel->find($user_id);
            if ($me) {
                array_unshift($daftarSemuaUser, $me); // Tambahkan dirinya sendiri ke awal
            }
        }

        // BATCH LOAD: Ambil seluruh data kinerja tahun ini dalam 2 query ringkas (O(1) in-memory)
        [$batchTargets, $batchTambahan] = $this->loadBatchKinerjaData($tahun_kinerja);

        $rekapDashboard = [];
        $globalTotalDinilai = 0;
        
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
        }

        // ----------------------------------------------------------------
        // 6. TREN KINERJA BULANAN & LEADERBOARD (PRO MAX UI)
        // ----------------------------------------------------------------
        $allUsersForUnit = $this->userModel->where('role !=', 'admin')->findAll();
        $trendBulananData = array_fill(0, 12, 0);
        $monthUserCounts = array_fill(0, 12, 0);
        $monthUserSums = array_fill(0, 12, 0);

        // Hitung nilai per bulan 1-12 untuk seluruh pegawai (in-memory O(1))
        foreach ($allUsersForUnit as $u) {
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

        // Analitik Partisipasi & Sebaran
        $sebaranKinerja = ['sangat_baik' => 0, 'baik' => 0, 'butuh_perbaikan' => 0, 'kurang' => 0, 'sangat_kurang' => 0];
        $partisipasiAktif = 0;
        $totalPegawai = count($rekapDashboard);

        foreach ($rekapDashboard as $r) {
            if ($r['rata_rata'] > 0) {
                $partisipasiAktif++;
            }
            
            $rr = $r['rata_rata'];
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
        }

        // Sort for Top 5 (Kinerja Tertinggi): rata_rata DESC, dinilai DESC, total_laporan DESC
        $top5Data = $rekapDashboard;
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
        
        // Sort for Bottom 5 / Perlu Perhatian Khusus:
        // Pegawai yang tidak ikut mengerjakan (tidak buat target, tidak melapor harian, rata_rata = 0)
        // menjadi prioritas paling utama (Nomor 1), disusul pegawai dengan rata_rata terendah.
        // DIBATASI: Pegawai dengan nilai Baik ke atas (>= 90% s.d. 150%) TIDAK dimasukkan ke Perlu Perhatian Khusus.
        $bottom5Data = array_values(array_filter($rekapDashboard, function($r) {
            return (float)$r['rata_rata'] < 90;
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

        $unitStats = [];
        $chartPegawaiUnitLabels = [];
        $chartPegawaiUnitData = [];
        
        // Agregasi Kinerja per Unit Kerja secara Akurat (in-memory O(1))
        foreach ($allUsersForUnit as $u) {
            $statPegawai = $this->hitungKinerjaPegawai($u['id'], $bulan_kinerja, $tahun_kinerja, $batchTargets, $batchTambahan);
            $uName = trim($u['unit'] ?? '') ?: 'Tanpa Unit';
            
            if (!isset($unitStats[$uName])) {
                $unitStats[$uName] = ['total_rata' => 0, 'count' => 0, 'anggota' => []];
            }
            
            $unitStats[$uName]['total_rata'] += $statPegawai['rata_rata'];
            $unitStats[$uName]['count']++;
            $unitStats[$uName]['anggota'][] = [
                'nama'           => $u['nama_lengkap'],
                'jabatan'        => $u['jabatan'] ?? '-',
                'unit'           => $uName,
                'total_pokok'    => $statPegawai['total_pokok'],
                'total_tambahan' => $statPegawai['total_tambahan'],
                'dinilai'        => $statPegawai['dinilai'],
                'total_laporan'  => $statPegawai['total_laporan'],
                'rata_rata'      => $statPegawai['rata_rata']
            ];
        }

        $unitRanking = [];
        $totalSumPegawai = 0;
        $totalCountPegawai = 0;

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

                // Rata-rata unit dihitung terhadap SELURUH pegawai di unit tersebut (termasuk nilai 0.0/tidak mengerjakan)
                $avg = $totalAnggota > 0 ? round($unitTotal / $totalAnggota, 2) : 0;
                $unitData['rata_rata_unit'] = $avg;
                $unitRanking[] = [
                    'nama' => $unitName,
                    'rata' => $avg,
                    'total_aktif' => $unitCountAktif,
                    'total_anggota' => $totalAnggota
                ];
            }
            unset($unitData);

            // Rata-rata organisasi agregat seluruh pegawai
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
                    'totalIndikator' => $totalIndikator,
                    'rataRataCapaianGlobal' => $rataRataCapaianGlobal,
                    'rataRataValue' => $rataRataValue,
                    'top5Unit' => $top5Unit,
                    'chartLabels' => $chartUserLabels,
                    'chartData' => $chartUserData,
                    'chartIndikatorLabels' => $chartIndikatorLabels,
                    'chartIndikatorPersen' => $chartIndikatorPersen,
                    'chartIndikatorMeta' => $chartIndikatorMeta,
                    'kinerja_per_user' => $kinerja_per_user,
                    'globalTotalDinilai' => $globalTotalDinilai,
                    'top5' => $top5,
                    'bottom5' => $bottom5,
                    'trendBulananData' => $trendBulananData,
                    'sebaranKinerja' => $sebaranKinerja,
                    'partisipasiAktif' => $partisipasiAktif,
                    'totalPegawai' => $totalPegawai,
                    'isSuper' => $canSeeAll,
                    'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels,
                    'chartPegawaiUnitData' => $chartPegawaiUnitData,
                    'unitStats' => $unitStats,
                    'tahun_kinerja' => $tahun_kinerja,
                    'bulan_kinerja' => $bulan_kinerja
                ]);
            }
        }

        $data = [
            'page_title' => 'Dashboard',
            'tahun_ecc' => $tahun_ecc,
            'tahun_kinerja' => $tahun_kinerja,
            'bulan_kinerja' => $bulan_kinerja,
            'daftar_tahun' => $eccData['daftar_tahun'] ?? [date('Y')],
            
            'totalIndikator' => $totalIndikator ?? 0,
            'rataRataCapaianGlobal' => $rataRataCapaianGlobal ?? 0,
            'rataRataValue' => $rataRataValue ?? 0,
            'top5Unit' => $top5Unit ?? [],
            
            'chartLabels' => $chartUserLabels ?? [],
            'chartData' => $chartUserData ?? [],
            
            'chartIndikatorLabels' => $chartIndikatorLabels ?? [],
            'chartIndikatorPersen' => $chartIndikatorPersen ?? [],
            'chartIndikatorMeta' => $chartIndikatorMeta ?? [],

            'kinerja_per_user' => $kinerja_per_user ?? [],
            
            'prodiData' => $eccData['prodiData'] ?? [],

            'globalTotalDinilai' => $globalTotalDinilai ?? 0,
            
            'top5' => $top5 ?? [],
            'bottom5' => $bottom5 ?? [],
            'trendBulananData' => $trendBulananData ?? [],
            'sebaranKinerja' => $sebaranKinerja ?? ['sangat_baik' => 0, 'baik' => 0, 'cukup' => 0, 'kurang' => 0],
            'partisipasiAktif' => $partisipasiAktif ?? 0,
            'totalPegawai' => $totalPegawai ?? 0,
            'isSuper' => $canSeeAll ?? false,
            'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels ?? [],
            'chartPegawaiUnitData' => $chartPegawaiUnitData ?? [],
            'unitStats' => $unitStats ?? [],
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

        if ($mode === 'sebaran') {
            $allUsers = $this->userModel->where('role !=', 'admin')->findAll();
            $result = [];
            
            foreach ($allUsers as $u) {
                $stat = $this->hitungKinerjaPegawai($u['id'], $bulan, $tahun, $batchTargets, $batchTambahan);
                $rata_rata = $stat['rata_rata'];
                
                $match = false;
                if ($normalizedKey === 'sangat_baik' && $rata_rata > 100) $match = true;
                else if ($normalizedKey === 'baik' && $rata_rata > 90 && $rata_rata <= 100) $match = true;
                else if ($normalizedKey === 'butuh_perbaikan' && $rata_rata > 75 && $rata_rata <= 90) $match = true;
                else if ($normalizedKey === 'kurang' && $rata_rata > 25 && $rata_rata <= 75) $match = true;
                else if ($normalizedKey === 'sangat_kurang' && $rata_rata <= 25) $match = true;
                
                if ($match) {
                    $result[] = [
                        'nama' => $u['nama_lengkap'],
                        'jabatan' => $u['jabatan'] ?? '-',
                        'unit' => $u['unit'] ?? '-',
                        'dinilai' => $stat['dinilai'],
                        'total_laporan' => $stat['total_laporan'],
                        'rata_rata' => $rata_rata
                    ];
                }
            }
            
            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
            
        } else if ($mode === 'tren') {
            $allUsers = $this->userModel->where('role !=', 'admin')->findAll();
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