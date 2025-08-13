<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User as UserModel; // Menggunakan alias 'UserModel' agar lebih jelas

class Auth extends BaseController
{
    /**
     * Menampilkan halaman form login.
     */
    public function index()
    {
        // Jika user sudah login, arahkan ke dashboard, jangan tampilkan form login lagi
        if (session()->get('isLoggedIn')) {
            return redirect()->to(session()->get('role') === 'admin' ? '/admin/dashboard' : '/user/dashboard');
        }

        return view('login'); // Memuat file di app/Views/login.php
    }

    /**
     * Memproses data dari form login.
     */
    public function prosesLogin()
    {
        // 1. Validasi Input
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan ke form login dengan input yang lama
            return redirect()->back()->withInput()->with('error', 'Username dan Password wajib diisi.');
        }

        // 2. Ambil data dari form
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // 3. Panggil Model untuk mencari user
        $userModel = new UserModel();
        $user = $userModel->getUserByUsername($username);

        // 4. Verifikasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            // Jika user ditemukan dan password cocok

            // 5. Buat data session
            $sessionData = [
                'user_id'    => $user['id'],
                'username'   => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ];
            session()->set($sessionData);

            // 6. Arahkan ke dashboard yang sesuai
            // di dalam Auth.php -> prosesLogin()
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard'); // Sesuai dengan route group
            } else {
                return redirect()->to('/user/dashboard'); // Sesuai dengan route group
            }
        } else {
            // Jika user tidak ditemukan atau password salah
            return redirect()->back()->with('error', 'Username atau Password salah.');
        }
    }

    /**
     * Proses Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
