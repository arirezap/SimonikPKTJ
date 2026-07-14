<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // 1. Cek apakah User sudah Login (Session ada)
        if (session()->get('isLoggedIn')) {
            
            $role = session()->get('role');

            // 2. Cek Role untuk menentukan arah Dashboard
            // Role Admin, Manajemen (Wadir), dan Kabag (semua jenis kabag) masuk ke Dashboard Admin
            if (in_array($role, ['admin', 'direktur', 'wadir', 'manajemen']) || str_contains($role, 'kabag')) {
                return redirect()->to('dashboard');
            }

            // Role sisanya (AAK, KUK, SPM/User biasa) masuk ke Dashboard User
            return redirect()->to('dashboard');
        }

        // 3. Jika belum login, arahkan paksa ke halaman Login
        return redirect()->to('/login');
    }
}