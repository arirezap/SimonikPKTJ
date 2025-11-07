<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\User as UserModel;
// PERBAIKAN: Hapus model ECC, panggil Trait
use App\Controllers\Traits\EccDataTrait; 

class Dashboard extends BaseController
{
    use EccDataTrait; // Gunakan Trait

    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $userModel = new UserModel();
        $user_id = session()->get('user_id');

        $tahun_terpilih = $this->request->getGet('tahun') ?? date('Y');

        // Panggil fungsi dari Trait
        $eccData = $this->getDashboardEccData($tahun_terpilih);

        // --- AMBIL DATA KINERJA (Logika yang sudah ada) ---
        $rencana_kinerja = $rencanaModel->where('user_id', $user_id)
                                        ->where('tahun_anggaran', $tahun_terpilih) 
                                        ->findAll();

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

                $realisasiBulanan = $rencana['realisasi_bulanan'] ?? [];
                $totalRealisasi = array_sum(array_map('floatval', $realisasiBulanan));
                $chartRealisasi[] = $totalRealisasi;

                if ($target_utama > 0) {
                    $totalPersentaseCapaian += ($totalRealisasi / $target_utama) * 100;
                    $indikatorValidUntukRataRata++;
                }

                $targetBulanan = $rencana['target_bulanan'] ?? [];
                for ($i = 0; $i < 12; $i++) {
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
        // --- SELESAI DATA KINERJA ---

        $data = [
            'page_title' => 'User Dashboard',
            'totalIndikator' => $totalIndikator,
            'rataRataCapaian' => $rataRataCapaian,
            'totalPengguna' => $userModel->countAllResults(),
            'tahun_terpilih' => $tahun_terpilih,
            'daftar_tahun' => $eccData['daftar_tahun'],
            
            'chartLabels' => $chartLabels,
            'chartTargets' => $chartTargets,
            'chartRealisasi' => $chartRealisasi,
            'lineChartLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'lineChartTargetData' => $cumulative_targets,
            'lineChartRealisasiData' => $cumulative_realisasi,

            'prodiData' => $eccData['prodiData'],
        ];

        return view('user/dashboard', $data);
    }
}