<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\User as UserModel;
use App\Controllers\Traits\EccDataTrait; 

class Dashboard extends BaseController
{
    use EccDataTrait; 

    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $userModel = new UserModel();
        $user_id = session()->get('user_id');

        $tahun_terpilih = $this->request->getGet('tahun') ?? date('Y');

        // 1. Ambil Data ECC (Radar Chart)
        $eccData = $this->getDashboardEccData($tahun_terpilih);

        // 2. Ambil Data Rencana Kinerja
        $rencana_kinerja = $rencanaModel->where('user_id', $user_id)
                                        ->where('tahun_anggaran', $tahun_terpilih) 
                                        ->findAll();

        // --- INISIALISASI ARRAY DATA ---
        $sasaranStats = [];   // Key: Nama Sasaran
        $indikatorStats = []; // Key: Nama Indikator
        
        $monthly_targets_sum = array_fill(0, 12, 0);
        $monthly_realisasi_sum = array_fill(0, 12, 0);

        $totalPersentaseGlobal = 0;
        $countItemGlobal = 0;

        if (!empty($rencana_kinerja)) {
            foreach ($rencana_kinerja as $rencana) {
                $target = (float)$rencana['target_utama'];
                
                $realisasiBulanan = $rencana['realisasi_bulanan'] ?? [];
                $realisasi = array_sum(array_map('floatval', $realisasiBulanan));

                // Hitung Persentase Capaian per Baris (untuk rata-rata)
                $persenCapaian = ($target > 0) ? ($realisasi / $target) * 100 : 0;
                
                // --- 1. GROUPING BERDASARKAN INDIKATOR KINERJA ---
                // (Menjumlahkan Target & Realisasi jika indikatornya sama)
                $namaIndikator = $rencana['indikator_kinerja'];
                if (!isset($indikatorStats[$namaIndikator])) {
                    $indikatorStats[$namaIndikator] = [
                        'target' => 0,
                        'realisasi' => 0
                    ];
                }
                $indikatorStats[$namaIndikator]['target'] += $target;
                $indikatorStats[$namaIndikator]['realisasi'] += $realisasi;

                // --- 2. GROUPING BERDASARKAN SASARAN PROGRAM ---
                // (Menghitung rata-rata capaian % dari semua indikator di bawah sasaran ini)
                $namaSasaran = $rencana['sasaran_program'];
                if (!isset($sasaranStats[$namaSasaran])) {
                    $sasaranStats[$namaSasaran] = [
                        'total_persen' => 0,
                        'count' => 0
                    ];
                }
                $sasaranStats[$namaSasaran]['total_persen'] += $persenCapaian;
                $sasaranStats[$namaSasaran]['count']++;

                // --- 3. DATA GLOBAL & TREN ---
                if ($target > 0) {
                    $totalPersentaseGlobal += $persenCapaian;
                    $countItemGlobal++;
                }

                $targetBulanan = $rencana['target_bulanan'] ?? [];
                for ($i = 0; $i < 12; $i++) {
                    $monthly_targets_sum[$i] += (float)($targetBulanan[$i] ?? 0);
                    $monthly_realisasi_sum[$i] += (float)($realisasiBulanan[$i] ?? 0);
                }
            }
        }

        // Siapkan Data View: Grafik Sasaran
        $chartSasaranLabels = [];
        $chartSasaranData = [];
        foreach ($sasaranStats as $nama => $stat) {
            $chartSasaranLabels[] = $nama;
            $avg = ($stat['count'] > 0) ? $stat['total_persen'] / $stat['count'] : 0;
            $chartSasaranData[] = round($avg, 2);
        }

        // Siapkan Data View: Grafik Indikator
        $chartIndikatorLabels = array_keys($indikatorStats);
        $chartIndikatorTargets = array_column($indikatorStats, 'target');
        $chartIndikatorRealisasi = array_column($indikatorStats, 'realisasi');

        // Statistik Kartu Atas
        $totalIndikator = count($indikatorStats); // Hitung jumlah indikator unik
        $rataRataCapaian = ($countItemGlobal > 0) ? $totalPersentaseGlobal / $countItemGlobal : 0;

        // Data Tren Kumulatif
        $cumulative_targets = [];
        $cumulative_realisasi = [];
        $last_target = 0;
        $last_realisasi = 0;
        for ($i = 0; $i < 12; $i++) {
            $last_target += $monthly_targets_sum[$i];
            $last_realisasi += $monthly_realisasi_sum[$i];
            $cumulative_targets[] = $last_target;
            $cumulative_realisasi[] = $last_realisasi;
        }

        // --- DASHBOARD REKAP PEGAWAI LOGIC ---
        $role = session()->get('role');
        $isSuper = in_array($role, ['admin', 'direktur', 'wadir', 'manajemen']);
        $logModel = new \App\Models\LogKegiatanHarian();
        $bulanTerpilih = date('n');

        if ($isSuper) {
            $daftarBawahan = $userModel->where('id !=', $user_id)->orderBy('nama_lengkap', 'ASC')->findAll();
            $isAtasan = true;
        } else {
            $daftarBawahan = $userModel->getBawahan($user_id);
            $isAtasan = !empty($daftarBawahan);
        }

        $rekapDashboard = [];
        if ($isAtasan) {
            foreach ($daftarBawahan as $bawahan) {
                $logs = $logModel->getLogByMonth($bawahan['id'], $bulanTerpilih, $tahun_terpilih);
                $total_laporan = count($logs);
                
                $dinilai = 0;
                $total_nilai = 0;
                $total_disiplin = 0;
                $total_kerjasama = 0;
                $tepat_waktu = 0;
                $terlambat = 0;

                foreach ($logs as $l) {
                    if (!empty($l['nilai_harian'])) {
                        $dinilai++;
                        $total_nilai += (float)$l['nilai_harian'];
                        $total_disiplin += (float)($l['disiplin'] ?? 0);
                        $total_kerjasama += (float)($l['kerjasama'] ?? 0);
                    }
                    if (($l['waktu_penyelesaian'] ?? '') === 'Tepat waktu') $tepat_waktu++;
                    elseif (($l['waktu_penyelesaian'] ?? '') === 'Terlambat') $terlambat++;
                }
                
                $rata_rata = $dinilai > 0 ? round($total_nilai / $dinilai, 2) : 0;
                $rata_disiplin = $dinilai > 0 ? round($total_disiplin / $dinilai, 2) : 0;
                $rata_kerjasama = $dinilai > 0 ? round($total_kerjasama / $dinilai, 2) : 0;
                
                $rekapDashboard[] = [
                    'bawahan' => $bawahan,
                    'total_laporan' => $total_laporan,
                    'dinilai' => $dinilai,
                    'belum_dinilai' => $total_laporan - $dinilai,
                    'rata_rata' => $rata_rata,
                    'rata_disiplin' => $rata_disiplin,
                    'rata_kerjasama' => $rata_kerjasama,
                    'tepat_waktu' => $tepat_waktu,
                    'terlambat' => $terlambat
                ];
            }
        }

        // --- AGREGASI DATA KINERJA PEGAWAI PER UNIT (KHUSUS DIREKTUR/WADIR) ---
        $unitStats = [];
        $chartPegawaiUnitLabels = [];
        $chartPegawaiUnitData = [];
        
        if ($isSuper && !empty($rekapDashboard)) {
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
            'page_title' => 'User Dashboard',
            'totalIndikator' => $totalIndikator,
            'rataRataCapaian' => $rataRataCapaian,
            'tahun_terpilih' => $tahun_terpilih,
            'daftar_tahun' => $eccData['daftar_tahun'],
            
            // Data Grafik 1 (Sasaran)
            'chartSasaranLabels' => $chartSasaranLabels,
            'chartSasaranData' => $chartSasaranData,

            // Data Grafik 2 (Indikator)
            'chartIndikatorLabels' => $chartIndikatorLabels,
            'chartIndikatorTargets' => $chartIndikatorTargets,
            'chartIndikatorRealisasi' => $chartIndikatorRealisasi,
            
            // Data Grafik 3 (Tren)
            'lineChartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'lineChartTargetData' => $cumulative_targets,
            'lineChartRealisasiData' => $cumulative_realisasi,

            'prodiData' => $eccData['prodiData'],

            // Data Grafik Kinerja Pegawai Per Unit (Direktur/Wadir)
            'isSuper' => $isSuper,
            'chartPegawaiUnitLabels' => $chartPegawaiUnitLabels,
            'chartPegawaiUnitData' => $chartPegawaiUnitData,
            'unitStats' => $unitStats,
            
        ];

        return view('user/dashboard', $data);
    }
}