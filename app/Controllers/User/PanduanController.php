<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class PanduanController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Panduan Penggunaan',
            'page_title' => 'Panduan Penggunaan ECC Laporan Kinerja',
        ];

        return view('user/panduan/index', $data);
    }
}
