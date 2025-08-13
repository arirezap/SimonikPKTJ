<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Siapkan data dummy atau data asli untuk dashboard admin
        $data = [
            'page_title' => 'Admin Dashboard',
            'totalPengguna' => 58,
            'jumlahTaruna' => 875,
            'totalAnggaran' => 12000000000,
        ];

        // Muat view dan kirimkan data ke dalamnya
        return view('admin/dashboard', $data);
    }
}