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

        // Mengambil semua rencana kinerja untuk user ini & tahun sekarang
        $rencana_kinerja = $rencanaModel->where('user_id', $user_id)
                                        ->where('tahun_anggaran', $tahun_sekarang)
                                        ->findAll();

        // Menyiapkan data untuk grafik capaian tahunan
        $chartLabels = [];
        $chartTargets = [];
        $chartRealisasi = [];
        $totalIndikator = 0;

        if (!empty($rencana_kinerja)) {
            $totalIndikator = count($rencana_kinerja);
            foreach ($rencana_kinerja as $rencana) {
                // Ambil label dari nama indikator
                $chartLabels[] = $rencana['indikator_kinerja'];
                // Ambil data target tahunan
                $chartTargets[] = $rencana['target_utama'];

                // Hitung total realisasi dari data JSON bulanan
                $realisasiBulanan = json_decode($rencana['realisasi_bulanan'], true) ?? [];
                $totalRealisasi = array_sum($realisasiBulanan);
                $chartRealisasi[] = $totalRealisasi;
            }
        }

        // Menyiapkan data untuk dikirim ke view
        $data = [
            'page_title' => 'User Dashboard',
            'totalPengguna' => $userModel->countAllResults(), // Contoh data agregat
            'totalIndikator' => $totalIndikator,
            'tahun_sekarang' => $tahun_sekarang,
            'chartLabels' => $chartLabels,
            'chartTargets' => $chartTargets,
            'chartRealisasi' => $chartRealisasi,
        ];

        return view('user/dashboard', $data);
    }
}
