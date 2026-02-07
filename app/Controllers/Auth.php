<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Auth extends BaseController
{
    public function login()
    {
        // Cek jika user sudah login, langsung lempar ke dashboard
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            if ($role === 'admin' || $role === 'manajemen' || str_contains($role, 'kabag')) {
                return redirect()->to('admin/dashboard');
            }
            return redirect()->to('user/dashboard');
        }

        // Tampilkan halaman login
        return view('login');
    }

    public function processLogin()
    {
        $session = session();
        $model = new User();
        
        // Ambil data dari form
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        // Cari user berdasarkan username
        $data = $model->where('username', $username)->first();
        
        if ($data) {
            $pass = $data['password'];
            
            // Verifikasi Password
            // Fallback: Support hash (password_verify) atau plain text (jika data lama/seeder manual)
            $verify_pass = password_verify($password, $pass);
            if (!$verify_pass && $pass === $password) {
                $verify_pass = true; 
            }

            if ($verify_pass) {
                // Set Session Data
                $ses_data = [
                    'id'       => $data['id'],
                    'username' => $data['username'],
                    'nama'     => $data['nama_lengkap'], // PERBAIKAN: Sesuai nama kolom di DB
                    'role'     => $data['role'],
                    'unit'     => $data['unit'] ?? '-',  // PERBAIKAN: Handle jika kolom unit kosong/belum ada
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                
                // Redirect sesuai Role
                if ($data['role'] === 'admin' || $data['role'] === 'manajemen' || str_contains($data['role'], 'kabag')) {
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->to('/user/dashboard');
                }
                
            } else {
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}