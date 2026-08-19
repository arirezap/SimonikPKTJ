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

        helper('cookie');
        $savedUsername = get_cookie('saved_username') ?? '';

        return view('login', ['savedUsername' => $savedUsername]);
    }

    public function processLogin()
    {
        $session = session();
        
        // PERBAIKAN DISINI: Gunakan User, bukan UserModel
        $model = new User(); 
        
        $username = trim((string) $this->request->getVar('username'));
        $password = (string) $this->request->getVar('password');
        
        $data = $model->where('username', $username)->first();
        
        // --- PROTEKSI BRUTE FORCE ATTACK (THROTTLING) ---
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        if ($throttler->check(md5($ipAddress . '_login'), 10, MINUTE) === false) {
            $session->setFlashdata('error', 'Terlalu banyak percobaan login yang gagal dari perangkat Anda. Silakan tunggu 1 menit.');
            return redirect()->to('/login');
        }

        if ($data) {
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            $isLegacyPass = false;
            
            // Fallback plain text (jika ada data lama)
            if (!$verify_pass && $pass === $password) {
                $verify_pass = true; 
                $isLegacyPass = true;
            }
            
            // Fallback MD5 (untuk data hasil import CSV yang menggunakan md5)
            if (!$verify_pass && $pass === md5($password)) {
                $verify_pass = true; 
                $isLegacyPass = true;
            }

            if ($verify_pass) {
                // AUTO PASSWORD HASH UPGRADE (Keamanan: Otomatis perbarui hash lemah ke BCRYPT)
                if ($isLegacyPass || password_needs_rehash($pass, PASSWORD_DEFAULT)) {
                    $model->update($data['id'], [
                        'password' => password_hash($password, PASSWORD_DEFAULT)
                    ]);
                }

                $role_aplikasi = $data['role'];

                // Load semua role dari tabel pivot user_roles
                $db = \Config\Database::connect();
                $userRoles = $db->table('user_roles')
                                ->where('user_id', $data['id'])
                                ->get()
                                ->getResultArray();
                $allRoles = array_column($userRoles, 'role_name');
                
                // Pastikan role primer selalu ada di daftar
                if (!in_array(strtolower($role_aplikasi), array_map('strtolower', $allRoles))) {
                    $allRoles[] = strtolower($role_aplikasi);
                }

                // Simpan Session Lengkap
                $ses_data = [
                    'id'           => $data['id'],
                    'user_id'      => $data['id'], // Kompatibilitas mundur
                    'username'     => $data['username'],
                    'nama'         => $data['nama_lengkap'], 
                    'nip'          => $data['nip'],           
                    'role'         => $role_aplikasi,   // Role primer (backward compatible)
                    'all_roles'    => $allRoles,        // Semua role (multi-role)
                    'unit'         => $data['unit'] ?? '-', 
                    'jabatan'      => $data['jabatan'] ?? '-',
                    'pangkat'      => $data['pangkat'] ?? '-',
                    'foto'         => $data['foto'] ?? null,
                    'isLoggedIn'   => TRUE
                ];
                $session->set($ses_data);
                
                // --- FITUR REMEMBER ME (HttpOnly Flag for Security) ---
                helper('cookie');
                $remember = $this->request->getVar('remember');
                if ($remember) {
                    // Buat token: id::md5(id+username+password)
                    $tokenString = $data['id'] . '::' . md5($data['id'] . $data['username'] . $data['password']);
                    $token = base64_encode($tokenString);
                    // Set cookie untuk 30 hari (2592000 detik) dengan HttpOnly=true
                    set_cookie('remember_me', $token, 2592000, '', '/', '', false, true);
                    // Simpan username agar otomatis terisi di form login
                    set_cookie('saved_username', $data['username'], 2592000, '', '/', '', false, true);
                } else {
                    delete_cookie('saved_username');
                }
                // -------------------------

                // Catat Log Audit Login
                log_audit('LOGIN', 'users', $data['id']);

                // Redirect Sesuai Role
                $isKabag = false;
                foreach ($allRoles as $r) {
                    if (str_contains($r, 'kabag')) {
                        $isKabag = true;
                        break;
                    }
                }

                if (hasAnyRole(['admin', 'direktur', 'wadir', 'manajemen']) || $isKabag) {
                    return redirect()->to('/dashboard');
                } else {
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
        $userId = session()->get('id') ?? session()->get('user_id');
        if ($userId) {
            log_audit('LOGOUT', 'users', $userId);
        }

        session()->destroy();
        helper('cookie');
        delete_cookie('remember_me');
        return redirect()->to('/login');
    }
}