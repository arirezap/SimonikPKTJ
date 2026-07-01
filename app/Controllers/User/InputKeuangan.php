<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class InputKeuangan extends BaseController
{
    /**
     * Menampilkan form untuk input progres keuangan.
     */
    public function index()
    {
        $data = [
            'page_title' => 'Input Progres Keuangan',
            'validation' => \Config\Services::validation()
        ];

        return view('user/keuangan/input_progres', $data);
    }

    /**
     * Menyimpan data progres keuangan.
     * (Catatan: Logika penyimpanan ini adalah contoh dan memerlukan tabel database baru)
     */
    public function store()
    {
        $rules = [
            'bulan' => 'required',
            'tahun' => 'required',
            'total_pendapatan' => 'required|numeric',
            'total_belanja' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        // Logika untuk menyimpan ke database akan ada di sini.
        // Contoh:
        // $keuanganModel = new \App\Models\ProgresKeuangan();
        // $keuanganModel->save([
        //     'user_id' => session()->get('user_id'),
        //     'bulan' => $this->request->getPost('bulan'),
        //     'tahun' => $this->request->getPost('tahun'),
        //     'total_pendapatan' => $this->request->getPost('total_pendapatan'),
        //     'total_belanja' => $this->request->getPost('total_belanja'),
        //     'catatan' => $this->request->getPost('catatan'),
        // ]);

        return redirect()->to('/keuangan/input')->with('success', 'Data progres keuangan berhasil disimpan!');
    }
}
