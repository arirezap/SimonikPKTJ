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

        // Ambil filter tahun dan bulan dari URL
        $tahun_terpilih = $this->request->getGet('tahun') ?? date('Y');
        $bulan_terpilih = $this->request->getGet('bulan'); // Bisa null jika "Semua Bulan"

        // Ambil daftar semua Tim/Unit/Pokja untuk filter
        $unit_pokja = $userModel->whereIn('role', ['manajemen', 'aak', 'kuk'])->findAll();
        $daftar_tahun = $rencanaModel->select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'DESC')->findAll();
        
        // --- MULAI LOGIKA PENGOLAHAN DATA ---

        $query = $rencanaModel->where('tahun_anggaran', $tahun_terpilih);
        
        $all_kinerja_tahunan = $query->findAll();

        $kinerja_per_user = [];
        foreach ($unit_pokja as $user) {
            $kinerja_per_user[$user['id']] = [
                'nama' => $user['nama_lengkap'],
                'total_target' => 0,
                'total_realisasi' => 0,
                'jumlah_indikator' => 0,
                'persentase_capaian' => 0
            ];
        }

        $totalCapaianGlobal = 0;
        $userDenganKinerja = 0;

        foreach ($all_kinerja_tahunan as $kinerja) {
            $user_id = $kinerja['user_id'];
            if (isset($kinerja_per_user[$user_id])) {
                $target_utama = (float)$kinerja['target_utama'];

                // Kalkulasi realisasi berdasarkan filter bulan
                $realisasi_bulanan = json_decode($kinerja['realisasi_bulanan'], true) ?? [];
                $total_realisasi_periode = 0;

                if ($bulan_terpilih && $bulan_terpilih !== 'all') {
                    $total_realisasi_periode = (float)($realisasi_bulanan[$bulan_terpilih - 1] ?? 0);
                } else {
                    $total_realisasi_periode = array_sum(array_map('floatval', $realisasi_bulanan));
                }

                // Kalkulasi target berdasarkan filter bulan
                $target_bulanan = json_decode($kinerja['target_bulanan'], true) ?? [];
                $total_target_periode = 0;

                if ($bulan_terpilih && $bulan_terpilih !== 'all') {
                    $total_target_periode = (float)($target_bulanan[$bulan_terpilih - 1] ?? 0);
                } else {
                    $total_target_periode = $target_utama;
                }

                $kinerja_per_user[$user_id]['total_target'] += $total_target_periode;
                $kinerja_per_user[$user_id]['total_realisasi'] += $total_realisasi_periode;
                $kinerja_per_user[$user_id]['jumlah_indikator']++;
            }
        }
        
        // Hitung persentase capaian per user dan global
        foreach ($kinerja_per_user as &$user_data) { // Gunakan '&' untuk referensi
            if ($user_data['total_target'] > 0) {
                $capaian = ($user_data['total_realisasi'] / $user_data['total_target']) * 100;
                $user_data['persentase_capaian'] = $capaian;
                $totalCapaianGlobal += $capaian;
                $userDenganKinerja++;
            }
        }
        
        $rataRataCapaianGlobal = ($userDenganKinerja > 0) ? $totalCapaianGlobal / $userDenganKinerja : 0;
        $totalIndikatorGlobal = array_sum(array_column($kinerja_per_user, 'jumlah_indikator'));

        $data = [
            'page_title' => 'Admin Dashboard',
            'tahun_sekarang' => date('Y'),
            'daftar_tahun' => $daftar_tahun,
            'tahun_terpilih' => $tahun_terpilih,
            'bulan_terpilih' => $bulan_terpilih,
            'totalIndikator' => $totalIndikatorGlobal,
            'rataRataCapaianGlobal' => $rataRataCapaianGlobal,
            'kinerja_per_user' => $kinerja_per_user,
            'chartLabels' => array_column($kinerja_per_user, 'nama'),
            'chartData' => array_column($kinerja_per_user, 'persentase_capaian'),
        ];

        return view('admin/dashboard', $data);
    }
}

