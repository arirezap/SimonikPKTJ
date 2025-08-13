<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Siapkan data dummy untuk ditampilkan di view
        $data = [
            'page_title' => 'User Dashboard',
            'jumlahTaruna' => 875,
            'persentaseSerapanLulusan' => 92.5,
            'totalPendapatan' => 5500000000,
            'realisasiAnggaran' => 9850000000,
            'labelsAnggaran' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            'dataTargetAnggaran' => [1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 1000],
            'dataRealisasiAnggaran' => [850, 900, 750, 1100, 950, 1000, 800, 1050, 900, 1150, 700, 800],
            'labelsSerapan' => ['PNS/ASN', 'BUMN', 'Swasta Nasional', 'Wirausaha', 'Studi Lanjut'],
            'dataSerapan' => [35, 25, 20, 15, 5],
        ];

        // Muat view dan kirimkan data ke dalamnya
        return view('user/dashboard', $data);
    }
}