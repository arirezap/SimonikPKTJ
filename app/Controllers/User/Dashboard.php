<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\User as UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $userModel = new UserModel();
        $user_id = session()->get('user_id');
        $tahun_sekarang = date('Y');

        $rencana_kinerja = $rencanaModel->where('user_id', $user_id)
                                        ->where('tahun_anggaran', $tahun_sekarang)
                                        ->findAll();

        // Inisialisasi variabel untuk perhitungan
        $chartLabels = [];
        $chartTargets = [];
        $chartRealisasi = [];
        $totalIndikator = 0;
        $totalPersentaseCapaian = 0;
        $indikatorValidUntukRataRata = 0;

        $monthly_targets_sum = array_fill(0, 12, 0);
        $monthly_realisasi_sum = array_fill(0, 12, 0);

        if (!empty($rencana_kinerja)) {
            $totalIndikator = count($rencana_kinerja);
            foreach ($rencana_kinerja as $rencana) {
                $chartLabels[] = $rencana['indikator_kinerja'];
                $target_utama = (float)$rencana['target_utama'];
                $chartTargets[] = $target_utama;

                $realisasiBulanan = json_decode($rencana['realisasi_bulanan'], true) ?? [];
                // PERBAIKAN: Pastikan semua nilai adalah angka sebelum dijumlahkan
                $totalRealisasi = array_sum(array_map('floatval', $realisasiBulanan));
                $chartRealisasi[] = $totalRealisasi;

                if ($target_utama > 0) {
                    $totalPersentaseCapaian += ($totalRealisasi / $target_utama) * 100;
                    $indikatorValidUntukRataRata++;
                }

                $targetBulanan = json_decode($rencana['target_bulanan'], true) ?? [];
                for ($i = 0; $i < 12; $i++) {
                    // PERBAIKAN: Ubah nilai menjadi float untuk mencegah error
                    $monthly_targets_sum[$i] += (float)($targetBulanan[$i] ?? 0);
                    $monthly_realisasi_sum[$i] += (float)($realisasiBulanan[$i] ?? 0);
                }
            }
        }

        $rataRataCapaian = ($indikatorValidUntukRataRata > 0) ? $totalPersentaseCapaian / $indikatorValidUntukRataRata : 0;

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
            'totalPengguna' => $userModel->countAllResults(),
            'tahun_sekarang' => $tahun_sekarang,
            'chartLabels' => $chartLabels,
            'chartTargets' => $chartTargets,
            'chartRealisasi' => $chartRealisasi,
            'lineChartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'lineChartTargetData' => $cumulative_targets,
            'lineChartRealisasiData' => $cumulative_realisasi,
        ];

        return view('user/dashboard', $data);
    }
}
