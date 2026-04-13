<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User; // Pastikan ini mengarah ke file app/Models/User.php

class Auth extends BaseController
{
    public function login()
    {
        // Cek jika user sudah login
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            
            // ADMIN & KABAG -> Admin Dashboard
            if ($role === 'admin' || str_contains($role, 'kabag')) {
                return redirect()->to('admin/dashboard');
            }
            
            // DIREKTUR & PEGAWAI -> User Dashboard
            return redirect()->to('user/dashboard');
        }

        return view('login');
    }

    public function processLogin()
    {
        $session = session();
        
        // PERBAIKAN DISINI: Gunakan User, bukan UserModel
        $model = new User(); 
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $data = $model->where('username', $username)->first();
        
        if ($data) {
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            
            // Fallback plain text (jika ada data lama)
            if (!$verify_pass && $pass === $password) {
                $verify_pass = true; 
            }
            
            // Fallback MD5 (untuk data hasil import CSV yang menggunakan md5)
            if (!$verify_pass && $pass === md5($password)) {
                $verify_pass = true; 
            }

            if ($verify_pass) {
                // Otomatis jadikan role 'spm' jika unit kerjanya adalah Satuan Penjaminan Mutu
                $role_aplikasi = $data['role'];
                if (strtolower(trim($data['unit'] ?? '')) === 'satuan penjaminan mutu') {
                    $role_aplikasi = 'spm';
                }

                // Simpan Session Lengkap
                $ses_data = [
                    'id'           => $data['id'],
                    'username'     => $data['username'],
                    'nama'         => $data['nama_lengkap'], 
                    'nip'          => $data['nip'],           
                    'role'         => $role_aplikasi,          
                    'unit'         => $data['unit'] ?? '-', 
                    'jabatan'      => $data['jabatan'] ?? '-',
                    'pangkat'      => $data['pangkat'] ?? '-',
                    'foto'         => $data['foto'] ?? null,
                    'isLoggedIn'   => TRUE
                ];
                $session->set($ses_data);
                
                // Redirect Sesuai Role
                if ($role_aplikasi === 'admin' || str_contains($role_aplikasi, 'kabag')) {
                    return redirect()->to('/admin/dashboard');
                } else {
                    // Direktur & Pegawai masuk sini
                    return redirect()->to('/user/dashboard');
                }
                
            } else {
                $session->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan di sistem.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}