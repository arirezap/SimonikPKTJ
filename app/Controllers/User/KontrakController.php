<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User;

class KontrakController extends BaseController
{
    public function index()
    {
        // 1. PENTING: Load Helper Tanggal Custom Anda
        helper('tanggal');

        $userModel = new User();
        $userId = session()->get('id'); 
        
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        // Ambil Data Atasan
        $atasan = null;
        if (!empty($user['atasan_id'])) {
            $atasan = $userModel->find($user['atasan_id']);
        }

        // Fallback jika atasan belum diset
        if (!$atasan) {
             $atasan = [
                'nama_lengkap' => '[ATASAN BELUM DISET]',
                'nip'          => '-',
                'jabatan'      => '[Hubungi Admin]',
                'pangkat'      => '-'
            ];
        }

        // 2. FORMAT TANGGAL INDONESIA
        // Menggunakan fungsi bulan_indo() dari helper
        // date('n') menghasilkan angka bulan 1-12 tanpa nol di depan
        $tanggalIndo = date('d') . ' ' . bulan_indo(date('n')) . ' ' . date('Y');

        $data = [
            'title'   => 'Kontrak Kinerja',
            'user'    => $user,
            'atasan'  => $atasan,
            'tahun'   => date('Y'), 
            
            // Masukkan tanggal yang sudah diformat ke view
            'tanggal' => $tanggalIndo 
        ];

        return view('user/kontrak/index', $data);
    }
}