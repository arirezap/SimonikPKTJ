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
            
            // ADMIN, MANAJEMEN, & KABAG -> Admin Dashboard
            if (in_array($role, ['admin', 'direktur', 'wadir', 'manajemen']) || str_contains($role, 'kabag')) {
                return redirect()->to('dashboard');
            }
            
            // DIREKTUR & PEGAWAI -> User Dashboard
            return redirect()->to('dashboard');
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
                
                // --- FITUR REMEMBER ME ---
                $remember = $this->request->getVar('remember');
                if ($remember) {
                    helper('cookie');
                    // Buat token: id::md5(id+username+password)
                    $tokenString = $data['id'] . '::' . md5($data['id'] . $data['username'] . $data['password']);
                    $token = base64_encode($tokenString);
                    // Set cookie untuk 30 hari (2592000 detik)
                    set_cookie('remember_me', $token, 2592000);
                }
                // -------------------------

                // Redirect Sesuai Role
                if (in_array($role_aplikasi, ['admin', 'direktur', 'wadir', 'manajemen']) || str_contains($role_aplikasi, 'kabag')) {
                    return redirect()->to('/dashboard');
                } else {
                    // Direktur & Pegawai masuk sini
                    return redirect()->to('/dashboard');
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
        helper('cookie');
        delete_cookie('remember_me');
        return redirect()->to('/login');
    }
}