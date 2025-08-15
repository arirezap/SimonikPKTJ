<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\User as UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $userModel = new UserModel();
        $tahun_sekarang = date('Y');

        $all_kinerja = $rencanaModel->where('tahun_anggaran', $tahun_sekarang)->findAll();
        $users = $userModel->where('role', 'user')->findAll();

        $kinerja_per_user = [];
        $total_persentase_capaian = 0;
        $total_indikator_valid = 0;

        foreach ($users as $user) {
            $kinerja_per_user[$user['id']] = [
                'nama' => $user['nama_lengkap'],
                'total_target' => 0,
                'total_realisasi' => 0,
                'jumlah_indikator' => 0,
                'persentase_capaian' => 0
            ];
        }

        foreach ($all_kinerja as $kinerja) {
            $user_id = $kinerja['user_id'];
            if (isset($kinerja_per_user[$user_id])) {
                $target_utama = (float)$kinerja['target_utama'];
                $realisasi_bulanan = json_decode($kinerja['realisasi_bulanan'], true) ?? [];
                $total_realisasi = array_sum(array_map('floatval', $realisasi_bulanan));

                $kinerja_per_user[$user_id]['total_target'] += $target_utama;
                $kinerja_per_user[$user_id]['total_realisasi'] += $total_realisasi;
                $kinerja_per_user[$user_id]['jumlah_indikator']++;
            }
        }

        // --- Perhitungan Baru untuk Grafik Donat ---
        $performanceDistribution = [
            'Sangat Baik (>90%)' => 0,
            'Baik (75-90%)'      => 0,
            'Cukup (50-75%)'     => 0,
            'Perlu Perhatian (<50%)' => 0,
        ];

        foreach ($kinerja_per_user as $id => &$user_data) {
            if ($user_data['total_target'] > 0) {
                $capaian = ($user_data['total_realisasi'] / $user_data['total_target']) * 100;
                $user_data['persentase_capaian'] = $capaian;
                $total_persentase_capaian += $capaian;
                $total_indikator_valid++;

                // Kategorikan pengguna berdasarkan capaian
                if ($capaian > 90) $performanceDistribution['Sangat Baik (>90%)']++;
                elseif ($capaian >= 75) $performanceDistribution['Baik (75-90%)']++;
                elseif ($capaian >= 50) $performanceDistribution['Cukup (50-75%)']++;
                else $performanceDistribution['Perlu Perhatian (<50%)']++;
            }
        }

        $rata_rata_capaian_global = ($total_indikator_valid > 0) ? $total_persentase_capaian / $total_indikator_valid : 0;

        $data = [
            'page_title' => 'Admin Dashboard',
            'totalIndikator' => count($all_kinerja),
            'rataRataCapaianGlobal' => $rata_rata_capaian_global,
            'tahun_sekarang' => $tahun_sekarang,
            'kinerja_per_user' => $kinerja_per_user,
            'chartLabels' => array_column($kinerja_per_user, 'nama'),
            'chartData' => array_column($kinerja_per_user, 'persentase_capaian'),
            'distribusiLabels' => array_keys($performanceDistribution),
            'distribusiData' => array_values($performanceDistribution),
        ];

        return view('admin/dashboard', $data);
    }
}
