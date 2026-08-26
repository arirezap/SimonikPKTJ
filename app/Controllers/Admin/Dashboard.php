<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\RencanaKinerja;
use App\Models\Sasaran;
use App\Models\Indikator;
// Model LED tidak perlu di-load manual lagi karena sudah dihandle oleh Trait
use App\Controllers\Traits\EccDataTrait; // 1. Import Trait

class Dashboard extends BaseController
{
    use EccDataTrait; // 2. Gunakan Trait
    
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
        $tahun_kinerja = $this->request->getGet('tahun_kinerja') ?? date('Y');
        $bulan_kinerja = $this->request->getGet('bulan_kinerja') ?? 'all';

        $user_id = session()->get('id') ?? session()->get('user_id');
        $role = session()->get('role');
        $canSeeAll = hasAnyRole(['admin', 'direktur', 'wadir']);

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
        $logModel = new \App\Models\LogKegiatanHarian();
        $bulanAngka = date('n'); // Gunakan bulan berjalan atau sesuai filter bulan
        if ($bulan_kinerja !== 'all') {
            $bulanAngka = (int)$bulan_kinerja;
        }
        
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

        $rekapDashboard = [];
        $globalTotalDinilai = 0;
        
        $laporanModel = new \App\Models\LaporanHarian();

        foreach ($daftarSemuaUser as $staf) {
            $targets = $laporanModel->getTargetWithRealization($staf['id'], $bulanAngka, $tahun_kinerja);
            $total_laporan = count($targets);
            
            $dinilai = 0;
            $total_nilai = 0;

            foreach ($targets as $l) {
                if ($l['nilai_capaian'] !== null && trim($l['nilai_capaian']) !== '') {
                    $dinilai++;
                    $total_nilai += (float)$l['nilai_capaian'];
                    $globalTotalDinilai++;
                }
            }
            
            $rata_rata = $dinilai > 0 ? round($total_nilai / $dinilai, 2) : 0;
            
            $rekapDashboard[] = [
                'staf' => $staf,
                'total_laporan' => $total_laporan,
                'dinilai' => $dinilai,
                'rata_rata' => $rata_rata,
            ];
        }

        // ----------------------------------------------------------------
        // 6. TREN KINERJA BULANAN & LEADERBOARD (PRO MAX UI)
        // ----------------------------------------------------------------
        $db = \Config\Database::connect();
        $allTargets = $db->table('laporan_harian')
            ->select('bulan, nilai_capaian')
            ->where('tahun', $tahun_kinerja)
            ->where("nilai_capaian IS NOT NULL AND nilai_capaian != ''")
            ->get()->getResultArray();
            
        $trendBulananData = array_fill(0, 12, 0); 
        $monthCounts = array_fill(0, 12, 0);
        $monthSums = array_fill(0, 12, 0);
        
        foreach ($allTargets as $t) {
            $b = (int)$t['bulan'] - 1;
            if ($b >= 0 && $b < 12) {
                $monthCounts[$b]++;
                $monthSums[$b] += (float)$t['nilai_capaian'];
            }
        }
        
        for ($i = 0; $i < 12; $i++) {
            if ($monthCounts[$i] > 0) {
                $trendBulananData[$i] = round($monthSums[$i] / $monthCounts[$i], 2);
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

        // Sort for Leaderboard
        $leaderboardData = $rekapDashboard;
        usort($leaderboardData, function($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });
        
        $top5 = array_slice($leaderboardData, 0, 5);
        $bottom5 = array_slice(array_reverse($leaderboardData), 0, 5); // Lowest first

        $unitStats = [];
        $chartPegawaiUnitLabels = [];
        $chartPegawaiUnitData = [];
        
        // Optimize: Calculate unitStats for ALL users globally (not just subordinates)
        $query = $db->table('laporan_harian')
            ->select('users.unit, users.nama_lengkap, users.jabatan, laporan_harian.nilai_capaian, laporan_harian.user_id')
            ->join('users', 'users.id = laporan_harian.user_id')
            ->where('laporan_harian.tahun', $tahun_kinerja)
            ->where("laporan_harian.nilai_capaian IS NOT NULL AND laporan_harian.nilai_capaian != ''");
            
        if ($bulan_kinerja !== 'all') {
            $query->where('laporan_harian.bulan', $bulanAngka);
        }
        $allTargetsUnit = $query->get()->getResultArray();

        $targetQuery = $db->table('laporan_harian')
            ->select('user_id, COUNT(id) as total_laporan')
            ->where('tahun', $tahun_kinerja);
        if ($bulan_kinerja !== 'all') {
            $targetQuery->where('bulan', $bulanAngka);
        }
        $targetCounts = $targetQuery->groupBy('user_id')->get()->getResultArray();
        
        $userTotalLaporan = [];
        foreach ($targetCounts as $tc) {
            $userTotalLaporan[$tc['user_id']] = (int)$tc['total_laporan'];
        }

        $allUsersForUnit = $this->userModel->where('role !=', 'admin')->findAll();
        $userPerformance = [];
        foreach ($allUsersForUnit as $u) {
            $userPerformance[$u['id']] = [
                'nama' => $u['nama_lengkap'],
                'jabatan' => $u['jabatan'] ?? '-',
                'unit' => trim($u['unit'] ?? '') ?: 'Tanpa Unit',
                'dinilai' => 0,
                'total_nilai' => 0,
                'rata_rata' => 0,
                'total_laporan' => $userTotalLaporan[$u['id']] ?? 0
            ];
        }

        foreach ($allTargetsUnit as $t) {
            $uid = $t['user_id'];
            if (isset($userPerformance[$uid])) {
                $userPerformance[$uid]['dinilai']++;
                $userPerformance[$uid]['total_nilai'] += (float)$t['nilai_capaian'];
            }
        }

        foreach ($userPerformance as $uid => $up) {
            $up['rata_rata'] = $up['dinilai'] > 0 ? round($up['total_nilai'] / $up['dinilai'], 2) : 0;
            $uName = $up['unit'];
            if (!isset($unitStats[$uName])) {
                $unitStats[$uName] = ['total_rata' => 0, 'count' => 0, 'anggota' => []];
            }
            $unitStats[$uName]['total_rata'] += $up['rata_rata'];
            $unitStats[$uName]['count']++;
            $unitStats[$uName]['anggota'][] = $up;
        }

        uasort($unitStats, function($a, $b) {
            $avgA = $a['count'] > 0 ? $a['total_rata'] / $a['count'] : 0;
            $avgB = $b['count'] > 0 ? $b['total_rata'] / $b['count'] : 0;
            return $avgB <=> $avgA;
        });

        foreach ($unitStats as $unitName => $stat) {
            $chartPegawaiUnitLabels[] = $unitName;
            $chartPegawaiUnitData[] = $stat['count'] > 0 ? round($stat['total_rata'] / $stat['count'], 2) : 0;
        }

            if ($this->request->isAJAX() && $ajax_type === 'kinerja') {
                return $this->response->setJSON([
                    'totalIndikator' => $totalIndikator,
                    'rataRataCapaianGlobal' => $rataRataCapaianGlobal,
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
                    'isSuper' => $canSeeAll, // pass isSuper
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
            'daftar_tahun' => $this->getDashboardEccData(date('Y'))['daftar_tahun'],
            
            'totalIndikator' => $totalIndikator ?? 0,
            'rataRataCapaianGlobal' => $rataRataCapaianGlobal ?? 0,
            
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

    public function apiDetailChart()
    {
        $mode = $this->request->getGet('mode');
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan') ?: date('n');

        $user_id = session()->get('id');
        $role = session()->get('role');
        $canSeeAll = hasAnyRole(['direktur', 'admin']);
        
        $db = \Config\Database::connect();
        
        if ($mode === 'sebaran') {
            $kategori = $this->request->getGet('kategori'); // Sangat Baik, Baik, Cukup, Kurang
            
            if ($canSeeAll) {
                $users = $this->userModel->where('role !=', 'admin')->where('id !=', $user_id)->orderBy('nama_lengkap', 'ASC')->findAll();
            } else {
                $users = $this->userModel->getAllStaf($user_id, $role);
                $me = $this->userModel->find($user_id);
                if ($me) array_unshift($users, $me);
            }

            $laporanModel = new \App\Models\LaporanHarian();
            $result = [];

            foreach ($users as $u) {
                $targets = $laporanModel->getTargetWithRealization($u['id'], $bulan, $tahun);
                $dinilai = 0; $total_nilai = 0;
                foreach ($targets as $l) {
                    if ($l['nilai_capaian'] !== null && trim($l['nilai_capaian']) !== '') {
                        $dinilai++; $total_nilai += (float)$l['nilai_capaian'];
                    }
                }
                $rata_rata = $dinilai > 0 ? round($total_nilai / $dinilai, 2) : 0;

                $match = false;
                if ($kategori === 'Sangat Baik' && $rata_rata > 100) $match = true;
                else if ($kategori === 'Baik' && $rata_rata > 90 && $rata_rata <= 100) $match = true;
                else if ($kategori === 'Butuh Perbaikan' && $rata_rata > 75 && $rata_rata <= 90) $match = true;
                else if ($kategori === 'Kurang' && $rata_rata > 25 && $rata_rata <= 75) $match = true;
                else if ($kategori === 'Sangat Kurang' && $rata_rata <= 25) $match = true;

                if ($match) {
                    $result[] = [
                        'nama' => $u['nama_lengkap'],
                        'jabatan' => $u['jabatan'] ?? '-',
                        'unit' => $u['unit'] ?? '-',
                        'rata_rata' => $rata_rata
                    ];
                }
            }
            
            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
            
        } else if ($mode === 'tren') {
            // Trend is global
            $allTargetsUnit = $db->table('laporan_harian')
                ->select('users.unit, users.nama_lengkap, users.jabatan, laporan_harian.nilai_capaian, laporan_harian.user_id')
                ->join('users', 'users.id = laporan_harian.user_id')
                ->where('laporan_harian.tahun', $tahun)
                ->where('laporan_harian.bulan', $bulan)
                ->where("laporan_harian.nilai_capaian IS NOT NULL AND laporan_harian.nilai_capaian != ''")
                ->get()->getResultArray();

            $userPerformance = [];
            foreach ($allTargetsUnit as $t) {
                $uid = $t['user_id'];
                if (!isset($userPerformance[$uid])) {
                    $userPerformance[$uid] = [
                        'nama' => $t['nama_lengkap'],
                        'jabatan' => $t['jabatan'] ?? '-',
                        'unit' => $t['unit'] ?? '-',
                        'dinilai' => 0,
                        'total_nilai' => 0
                    ];
                }
                $userPerformance[$uid]['dinilai']++;
                $userPerformance[$uid]['total_nilai'] += (float)$t['nilai_capaian'];
            }

            $result = [];
            foreach ($userPerformance as $up) {
                $up['rata_rata'] = $up['dinilai'] > 0 ? round($up['total_nilai'] / $up['dinilai'], 2) : 0;
                $result[] = $up;
            }
            
            // Sort by rata_rata descending
            usort($result, function($a, $b) {
                return $b['rata_rata'] <=> $a['rata_rata'];
            });

            return $this->response->setJSON(['status' => 'success', 'data' => $result]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid mode']);
    }
}