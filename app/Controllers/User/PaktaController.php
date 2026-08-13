<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\User;

class PaktaController extends BaseController
{
    public function index()
    {
        helper('tanggal'); 

        $userModel = new User();
        $userId = session()->get('id') ?? session()->get('user_id'); 
        
        // Failsafe: Jika user menggunakan session versi lama (dimana id berisi NIP/Username)
        if (!is_numeric($userId) || strlen((string)$userId) > 10) {
            $userDb = $userModel->where('username', $userId)
                                ->orWhere('nip', $userId)
                                ->orWhere('id', $userId)
                                ->first();
            if ($userDb) {
                $userId = $userDb['id'];
            }
        }

        // 1. Ambil Data User yang Sedang Login
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        // 2. Ambil Data Direktur dari Database (Dinamis)
        $direktur = $userModel->where('role', 'direktur')->orWhere('jabatan', 'Direktur')->first();

        // Fallback: Jika data Direktur belum ada di database, gunakan dummy agar tidak error
        if (!$direktur) {
            $direktur = [
                'nama_lengkap' => '[DATA DIREKTUR BELUM DIINPUT]',
                'nip'          => '-',
                'jabatan'      => 'Direktur Politeknik Keselamatan Transportasi Jalan',
                'pangkat'      => '-'
            ];
        }

        // Format Tanggal Indonesia
        $tanggalIndo = date('d') . ' ' . bulan_indo(date('n')) . ' ' . date('Y');

        $data = [
            'title'    => 'Pakta Integritas',
            'user'     => $user,
            'direktur' => $direktur, 
            'tahun'    => date('Y'), 
            'tanggal'  => $tanggalIndo 
        ];

        return view('user/pakta/index', $data);
    }
}