<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class DiklatController extends BaseController
{
    public function index()
    {
        $data = [
            'page_title' => 'Data Diklat',
            'daftar_diklat' => [
                ['nama' => 'Diklat Teknis Audit Keselamatan Jalan', 'periode' => '1 - 15 Sep 2025', 'jumlah_peserta' => 30, 'status' => 'Akan Datang'],
                ['nama' => 'Pelatihan Manajemen Lalu Lintas', 'periode' => '5 - 20 Agu 2025', 'jumlah_peserta' => 25, 'status' => 'Berjalan'],
                ['nama' => 'Sertifikasi Penguji Kendaraan Bermotor', 'periode' => '10 - 30 Jul 2025', 'jumlah_peserta' => 40, 'status' => 'Selesai'],
            ]
        ];
        return view('user/data/diklat', $data);
    }
}
