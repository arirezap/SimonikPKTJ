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
        // Jika user sudah login, arahkan ke dashboard yang sesuai
        if (session()->get('isLoggedIn')) {
            // PERUBAHAN: Cek apakah role ada di dalam array ['admin', 'manajemen']
            $isAdminOrManajemen = in_array(session()->get('role'), ['admin', 'manajemen']);
            return redirect()->to($isAdminOrManajemen ? '/admin/dashboard' : '/user/dashboard');
        }

        return view('login');
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
        $user = $userModel->getUserByUsername($this->request->getPost('username'));
        $password = $this->request->getPost('password');

        if ($user && password_verify($password, $user['password'])) {
            // ... (kode pembuatan session tidak berubah) ...
            $sessionData = [
                'user_id'    => $user['id'],
                'username'   => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role'       => $user['role'],
                'isLoggedIn' => true
            ];
            session()->set($sessionData);

            // PERUBAHAN: Cek apakah role adalah 'admin' ATAU 'manajemen'
            if (in_array($user['role'], ['admin', 'manajemen'])) {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/user/dashboard');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau Password salah.');
        }
    }

    /**
     * Proses Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}
