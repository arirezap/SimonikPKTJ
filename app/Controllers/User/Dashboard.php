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
        ];

        return view('user/dashboard', $data);
    }
}