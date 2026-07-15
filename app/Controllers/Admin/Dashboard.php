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
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? 'all'; 

        $user_id = session()->get('id') ?? session()->get('user_id');
        $role = session()->get('role');
        $canSeeAll = in_array($role, ['admin', 'direktur', 'wadir']);

        // ----------------------------------------------------------------
        // 1. DATA UNTUK SUMMARY CARDS & AGREGASI INDIKATOR
        // ----------------------------------------------------------------
        $rencanaQuery = $this->rencanaModel
            ->select('rencana_kinerja.*')
            ->join('users', 'users.id = rencana_kinerja.user_id')
            ->where('users.role !=', 'admin') 
            ->where('rencana_kinerja.tahun_anggaran', $tahun);
            
        if (!$canSeeAll) {
            $rencanaQuery->groupStart()
                         ->where('users.id', $user_id)
                         ->orWhere('users.atasan_id', $user_id)
                         ->groupEnd();
        }
        $allRencana = $rencanaQuery->findAll();

        $totalPersen = 0;
        $countRencana = 0;
        
        $indikatorData = []; 

        foreach ($allRencana as $row) {
            $target = (float) $row['target_utama'];
            $realisasi_bulanan = $this->parseRealisasi($row['realisasi_bulanan'] ?? []);

            $realisasi = 0;
            if ($bulan === 'all') {
                $realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
            } else {
                $idx = (int)$bulan - 1;
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
            $userQuery->groupStart()
                      ->where('id', $user_id)
                      ->orWhere('atasan_id', $user_id)
                      ->groupEnd();
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
                if ($bulan === 'all') {
                    $real = array_sum(array_map('floatval', $rb));
                } else {
                    $idx = (int)$bulan - 1;
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
        // 4. DATA ECC (MENGGUNAKAN TRAIT AGAR SAMA DENGAN USER)
        // ----------------------------------------------------------------
        // Ini menggantikan kode manual yang panjang sebelumnya
        $eccData = $this->getDashboardEccData($tahun);
        $prodiData = $eccData['prodiData']; // Hasilnya sudah format standar (label, data, ids)

        // Ambil daftar tahun dari trait agar konsisten dengan dashboard lain
        $daftar_tahun = $eccData['daftar_tahun'];

        // ----------------------------------------------------------------
        // 5. DATA KINERJA HARIAN PEGAWAI PER UNIT (KHUSUS DIREKTUR/WADIR DLL)
        // ----------------------------------------------------------------
        $logModel = new \App\Models\LogKegiatanHarian();
        $bulanAngka = date('n'); // Gunakan bulan berjalan atau sesuai filter bulan
        if ($bulan !== 'all') {
            $bulanAngka = (int)$bulan;
        }
        
        if ($canSeeAll) {
            $daftarSemuaUser = $this->userModel->where('role !=', 'admin')
                                               ->where('id !=', $user_id)
                                               ->orderBy('nama_lengkap', 'ASC')
                                               ->findAll();
        } else {
            $daftarSemuaUser = $this->userModel->getAllBawahan($user_id, $role);
            $me = $this->userModel->find($user_id);
            if ($me) {
                array_unshift($daftarSemuaUser, $me); // Tambahkan dirinya sendiri ke awal
            }
        }

        $rekapDashboard = [];
        $globalTepatWaktu = 0;
        $globalTerlambat = 0;
        $globalTotalDisiplin = 0;
        $globalTotalKerjasama = 0;
        $globalTotalDinilai = 0;
        
        foreach ($daftarSemuaUser as $bawahan) {
            $logs = $logModel->getLogByMonth($bawahan['id'], $bulanAngka, $tahun);
            $total_laporan = count($logs);
            
            $dinilai = 0;
            $total_nilai = 0;

            foreach ($logs as $l) {
                if (!empty($l['nilai_harian'])) {
                    $dinilai++;
                    $total_nilai += (float)$l['nilai_harian'];
                    $globalTotalDisiplin += (float)($l['disiplin'] ?? 0);
                    $globalTotalKerjasama += (float)($l['kerjasama'] ?? 0);
                    $globalTotalDinilai++;
                }
                if (($l['waktu_penyelesaian'] ?? '') === 'Tepat waktu') {
                    $globalTepatWaktu++;
                } elseif (($l['waktu_penyelesaian'] ?? '') === 'Terlambat') {
                    $globalTerlambat++;
                }
            }
            
            $rata_rata = $dinilai > 0 ? round($total_nilai / $dinilai, 2) : 0;
            
            $rekapDashboard[] = [
                'bawahan' => $bawahan,
                'total_laporan' => $total_laporan,
                'dinilai' => $dinilai,
                'rata_rata' => $rata_rata,
            ];
        }

        // ----------------------------------------------------------------
        // 6. TREN KINERJA BULANAN & LEADERBOARD (PRO MAX UI)
        // ----------------------------------------------------------------
        $db = \Config\Database::connect();
        $builder = $db->table('log_kegiatan_harian');
        $builder->select('MONTH(tanggal_kegiatan) as bulan, AVG(nilai_harian) as avg_nilai');
        $builder->where('YEAR(tanggal_kegiatan)', $tahun);
        $builder->where('nilai_harian IS NOT NULL');
        $builder->where('nilai_harian >', 0);
        $builder->groupBy('MONTH(tanggal_kegiatan)');
        $trendQuery = $builder->get()->getResultArray();
        
        $trendBulananData = array_fill(0, 12, 0); // Default 0 (index 0=Jan, 11=Des)
        foreach ($trendQuery as $tq) {
            $trendBulananData[(int)$tq['bulan'] - 1] = round($tq['avg_nilai'], 2);
        }

        // Sort for Leaderboard
        $leaderboardData = $rekapDashboard;
        usort($leaderboardData, function($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });
        
        $top5 = array_slice($leaderboardData, 0, 5);
        $bottom5 = array_slice(array_reverse($leaderboardData), 0, 5); // Reverse to get worst, but then slice

        $unitStats = [];
        $chartPegawaiUnitLabels = [];
        $chartPegawaiUnitData = [];
        
        if (!empty($rekapDashboard)) {
            foreach ($rekapDashboard as $rekap) {
                $unitName = trim($rekap['bawahan']['unit'] ?? '');
                if (empty($unitName)) {
                    $unitName = 'Tanpa Unit';
                }
                
                if (!isset($unitStats[$unitName])) {
                    $unitStats[$unitName] = ['total_rata' => 0, 'count' => 0, 'anggota' => []];
                }
                $unitStats[$unitName]['total_rata'] += $rekap['rata_rata'];
                $unitStats[$unitName]['count']++;
                $unitStats[$unitName]['anggota'][] = [
                    'nama' => $rekap['bawahan']['nama_lengkap'],
                    'jabatan' => $rekap['bawahan']['jabatan'] ?? '-',
                    'rata_rata' => $rekap['rata_rata'],
                    'dinilai' => $rekap['dinilai'],
                    'total_laporan' => $rekap['total_laporan']
                ];
            }
            
            // Sort by average score descending
            uasort($unitStats, function($a, $b) {
                $avgA = $a['count'] > 0 ? $a['total_rata'] / $a['count'] : 0;
                $avgB = $b['count'] > 0 ? $b['total_rata'] / $b['count'] : 0;
                return $avgB <=> $avgA;
            });
            
            foreach ($unitStats as $unitName => $stat) {
                $chartPegawaiUnitLabels[] = $unitName;
                $chartPegawaiUnitData[] = round($stat['total_rata'] / $stat['count'], 2);
            }
        }

        $data = [
            'page_title' => 'Dashboard Admin',
            'tahun_terpilih' => $tahun,
            'bulan_terpilih' => $bulan,
            'daftar_tahun' => $daftar_tahun, // Gunakan daftar tahun dari trait
            
            'totalIndikator' => $totalIndikator,
            'rataRataCapaianGlobal' => $rataRataCapaianGlobal,
            
            'chartLabels' => $chartUserLabels,
            'chartData' => $chartUserData,
            
            'chartIndikatorLabels' => $chartIndikatorLabels,
            'chartIndikatorPersen' => $chartIndikatorPersen,
            'chartIndikatorMeta' => $chartIndikatorMeta,

            'kinerja_per_user' => $kinerja_per_user,
            
            // Data Radar Chart dari Trait
            'prodiData' => $prodiData,

            // Data Advanced Analytics (Pro Max)
            'globalTepatWaktu' => $globalTepatWaktu,
            'globalTerlambat' => $globalTerlambat,
            'globalTotalDinilai' => $globalTotalDinilai,
            'avgGlobalDisiplin' => $globalTotalDinilai > 0 ? round($globalTotalDisiplin / $globalTotalDinilai, 2) : 0,
            'avgGlobalKerjasama' => $globalTotalDinilai > 0 ? round($globalTotalKerjasama / $globalTotalDinilai, 2) : 0,
            'trendBulananData' => $trendBulananData,
            'top5' => $top5,
            'bottom5' => $bottom5,

            // Data Grafik Kinerja Pegawai Per Unit
            'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels,
            'chartPegawaiUnitData' => $chartPegawaiUnitData,
            'unitStats' => $unitStats,
            
        ];

        return view('admin/dashboard', $data);
    }
}