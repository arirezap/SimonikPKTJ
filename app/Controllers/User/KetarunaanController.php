<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class KetarunaanController extends BaseController
{
    public function index()
    {
        // Data dummy untuk ditampilkan
        $data = [
            'page_title' => 'Data Ketarunaan',
            'catatan_taruna' => [
                ['nama' => 'Budi Santoso', 'tingkat' => 'II', 'jenis' => 'Prestasi', 'keterangan' => 'Juara 1 Lomba Debat Nasional'],
                ['nama' => 'Citra Lestari', 'tingkat' => 'III', 'jenis' => 'Pelanggaran', 'keterangan' => 'Terlambat apel pagi'],
                ['nama' => 'Agus Wijaya', 'tingkat' => 'I', 'jenis' => 'Prestasi', 'keterangan' => 'IPK Sempurna Semester 1'],
            ]
        ];

        return view('user/data/ketarunaan', $data);
    }
}
