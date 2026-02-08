<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\SkpHeaderModel;
use App\Models\SkpModel; // <--- JANGAN LUPA USE INI

class Skp extends BaseController
{
    protected $skpHeaderModel;
    protected $skpModel; // <--- TAMBAHKAN PROPERTI INI

    public function __construct()
    {
        // Inisialisasi kedua Model
        $this->skpHeaderModel = new SkpHeaderModel();
        $this->skpModel       = new SkpModel(); // <--- INISIALISASI DISINI
    }

    public function index()
    {
        $userId = session()->get('id');

        $data = [
            'title' => 'Daftar Sasaran Kinerja Pegawai',
            'list_skp' => $this->skpHeaderModel->where('user_id', $userId)
                ->orderBy('tahun', 'DESC')
                ->findAll()
        ];

        return view('User/skp/index', $data);
    }

    // UPDATE FUNGSI STORE
    public function store()
    {
        $userId = session()->get('id');
        $role   = session()->get('role');
        $tahun  = $this->request->getPost('tahun');

        // Ambil inputan model SKP
        $modelSkp = $this->request->getPost('model_skp');

        if (!$tahun) {
            return redirect()->back()->with('error', 'Tahun tidak boleh kosong.');
        }

        // 1. Cek Duplikasi
        $exist = $this->skpHeaderModel->where('user_id', $userId)->where('tahun', $tahun)->first();
        if ($exist) {
            return redirect()->back()->with('error', 'Anda sudah membuat SKP untuk tahun ' . $tahun);
        }

        // 2. Cek Role Direktur (Logic validasi tetap sama)
        if ($role !== 'direktur') {
            $direkturSudahBuat = $this->skpHeaderModel->isDirekturSkpExists($tahun);
            if (!$direkturSudahBuat) {
                return redirect()->back()->with('error_modal', 'Maaf, Anda belum bisa membuat SKP Tahun ' . $tahun . ' karena Direktur belum membuat SKP.');
            }
        }

        // 3. Simpan Data Header (UPDATE DISINI)
        $this->skpHeaderModel->save([
            'user_id'       => $userId,
            'tahun'         => $tahun,
            'model_skp'     => $modelSkp, // <--- Simpan Pilihan
            'periode_awal'  => $tahun . '-01-01',
            'periode_akhir' => $tahun . '-12-31',
            'status'        => 'Draft'
        ]);

        return redirect()->to('/user/skp')->with('success', 'SKP Tahun ' . $tahun . ' (' . $modelSkp . ') berhasil dibuat.');
    }

    // ---------------------------------------------------------
    // TAMBAHKAN FUNGSI DELETE INI
    // ---------------------------------------------------------
    public function delete($id)
    {
        // Cari data berdasarkan ID
        $skp = $this->skpHeaderModel->find($id);

        // 1. Cek apakah data ada
        if (!$skp) {
            return redirect()->to('/user/skp')->with('error', 'Data SKP tidak ditemukan.');
        }

        // 2. Security Check: Pastikan yang menghapus adalah pemilik data (User ID sama)
        if ($skp['user_id'] != session()->get('id')) {
            return redirect()->to('/user/skp')->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // 3. Cek Status: Hanya boleh hapus jika status 'Draft'
        if ($skp['status'] !== 'Draft') {
            return redirect()->to('/user/skp')->with('error', 'SKP yang sudah diajukan atau disetujui tidak dapat dihapus.');
        }

        // 4. Lakukan Penghapusan
        // Karena kita sudah set ON DELETE CASCADE di database (foreign key), 
        // maka menghapus header akan otomatis menghapus target-target di dalamnya.
        $this->skpHeaderModel->delete($id);

        return redirect()->to('/user/skp')->with('success', 'Data SKP berhasil dihapus.');
    }


    // ---------------------------------------------------------
    // FUNGSI DETAIL SKP
    // ---------------------------------------------------------
    public function detail($id)
    {
        // Ambil Header SKP
        $header = $this->skpHeaderModel->find($id);

        if (!$header) {
            return redirect()->to('/user/skp')->with('error', 'Data SKP tidak ditemukan.');
        }

        // Ambil Data Pegawai
        $userModel = new \App\Models\User();
        $pegawai = $userModel->find($header['user_id']);

        // Ambil Data Atasan (Pejabat Penilai)
        // Pastikan kolom 'atasan_id' ada di tabel users, atau sesuaikan logikanya
        $atasan = null;
        if (!empty($pegawai['atasan_id'])) {
            $atasan = $userModel->find($pegawai['atasan_id']);
        }

        // Ambil Target SKP (RHK) dari tabel skp_targets
        // INI YANG TADI ERROR KARENA $this->skpModel BELUM DI-LOAD
        $targets = $this->skpModel->where('skp_header_id', $id)
            ->orderBy('jenis', 'ASC')
            ->findAll();

        $data = [
            'title'   => 'Detail Sasaran Kinerja Pegawai',
            'header'  => $header,
            'pegawai' => $pegawai,
            'atasan'  => $atasan,
            'targets' => $targets
        ];

        return view('User/skp/detail', $data);
    }
}
