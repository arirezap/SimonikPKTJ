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
            return redirect()->to(site_url('dashboard'));
        }

        helper('cookie');
        $savedUsername = get_cookie('saved_username') ?? '';

        return view('login', ['savedUsername' => $savedUsername]);
    }

    public function processLogin()
    {
        $session = session();
        
        $model = new User(); 
        
        $username = trim((string) $this->request->getVar('username'));
        $password = (string) $this->request->getVar('password');

        // Validasi input awal
        if (empty($username) || empty($password)) {
            $session->setFlashdata('error', 'Nama pengguna dan kata sandi wajib diisi.');
            return redirect()->to(site_url('login'))->withInput();
        }
        
        // --- PROTEKSI BRUTE FORCE ATTACK (THROTTLING) ---
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        $throttleKey = md5($ipAddress . '_' . strtolower($username) . '_login');
        if ($throttler->check($throttleKey, 10, MINUTE) === false) {
            log_audit('RATE_LIMIT_LOGIN', 'auth', null, null, [
                'username' => $username,
                'ip'       => $ipAddress,
                'reason'   => 'brute_force_throttled'
            ]);
            $session->setFlashdata('error', 'Terlalu banyak percobaan login yang gagal untuk akun ini. Silakan tunggu 1 menit.');
            return redirect()->to(site_url('login'))->withInput();
        }

        $data = $model->where('username', $username)->first();

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
                // PROTEKSI STATUS PEGAWAI (Khusus status 'nonaktif' diblokir, mutasi_keluar & pensiun tetap diizinkan menyelesaikan tanggung jawab)
                if (($data['status_pegawai'] ?? 'aktif') === 'nonaktif') {
                    log_audit('FAILED_LOGIN_DISABLED_ACCOUNT', 'users', $data['id'], null, [
                        'username'       => $username,
                        'reason'         => 'account_disabled',
                        'status_pegawai' => 'nonaktif'
                    ]);
                    $session->setFlashdata('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi bagian Kepegawaian.');
                    return redirect()->to(site_url('login'))->withInput();
                }

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

                // Regenerasi Session ID untuk Mencegah Serangan Session Fixation (dengan penghancuran sesi lama)
                $session->regenerate(true);

                // Simpan Session Lengkap (termasuk unit_id dan fingerprint peramban untuk proteksi anti session hijacking)
                $userAgent = (string) $this->request->getUserAgent();
                $fingerprint = hash('sha256', $userAgent);

                $ses_data = [
                    'id'                     => $data['id'],
                    'user_id'                => $data['id'], // Kompatibilitas mundur
                    'username'               => $data['username'],
                    'nama'                   => $data['nama_lengkap'], 
                    'nip'                    => $data['nip'],           
                    'role'                   => $role_aplikasi,   // Role primer (backward compatible)
                    'all_roles'              => $allRoles,        // Semua role (multi-role)
                    'unit'                   => $data['unit'] ?? '-', 
                    'unit_id'                => $data['unit_id'] ?? null,
                    'jabatan'                => $data['jabatan'] ?? '-',
                    'pangkat'                => $data['pangkat'] ?? '-',
                    'status_pegawai'         => $data['status_pegawai'] ?? 'aktif',
                    'foto'                   => $data['foto'] ?? null,
                    'user_agent_fingerprint' => $fingerprint,
                    'isLoggedIn'             => TRUE
                ];
                $session->set($ses_data);
                \App\Libraries\SurveiPemetaanPolicy::syncPendingProfileOnLogin((int) $data['id']);
                
                // --- FITUR REMEMBER ME (HMAC-SHA256 Token & HttpOnly Flag for Security) ---
                helper('cookie');
                $remember = $this->request->getVar('remember');
                if ($remember) {
                    // Buat token aman: id::hmac_sha256(id::username::password)
                    $tokenHash = hash_hmac('sha256', $data['id'] . '::' . $data['username'] . '::' . $data['password'], (string)config('App')->appTimezone ?: 'ecc_auth_token');
                    $tokenString = $data['id'] . '::' . $tokenHash;
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
                log_audit('LOGIN', 'users', $data['id'], null, [
                    'username'   => $data['username'],
                    'ip_address' => $ipAddress,
                    'user_agent' => (string) $this->request->getUserAgent()
                ]);

                return redirect()->to(site_url('dashboard'));
                
            } else {
                // Catat Log Percobaan Login Gagal (Password Salah)
                log_audit('FAILED_LOGIN', 'users', $data['id'], null, [
                    'username' => $username,
                    'reason'   => 'invalid_password'
                ]);

                $session->setFlashdata('error', 'Nama pengguna atau kata sandi yang Anda masukkan salah.');
                return redirect()->to(site_url('login'))->withInput();
            }
        } else {
            // Catat Log Percobaan Login Gagal (User Tidak Ditemukan)
            log_audit('FAILED_LOGIN', 'auth', null, null, [
                'username' => $username,
                'reason'   => 'user_not_found'
            ]);

            $session->setFlashdata('error', 'Nama pengguna atau kata sandi yang Anda masukkan salah.');
            return redirect()->to(site_url('login'))->withInput();
        }
    }

    public function logout()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        $username = session()->get('username') ?? 'unknown';
        
        if ($userId) {
            log_audit('LOGOUT', 'users', $userId, null, [
                'username'   => $username,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => (string)$this->request->getUserAgent()
            ]);
        }

        // Hancurkan data sesi dan hapus cookie remember_me
        session()->destroy();
        helper('cookie');
        delete_cookie('remember_me');

        // Kembalikan ke halaman login dengan flash message dan proteksi cache-control
        return redirect()->to(site_url('login'))
            ->with('success', 'Anda telah berhasil keluar dari sistem.')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }
}