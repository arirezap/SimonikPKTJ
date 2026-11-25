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
            if (\App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfAccountOnly()) {
                return redirect()->to(site_url(ltrim(\App\Libraries\Ketarunaan\KetarunaanPolicy::getSelfPortalUrl(), '/')));
            }
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
        
        // --- PROTEKSI BRUTE FORCE ATTACK (THROTTLING PER AKUN & PER IP) ---
        $throttler   = \Config\Services::throttler();
        $ipAddress   = $this->request->getIPAddress();
        $throttleKey = md5($ipAddress . '_' . strtolower($username) . '_login');
        $ipKey       = md5($ipAddress . '_login_ip');
        if ($throttler->check($throttleKey, 10, MINUTE) === false || $throttler->check($ipKey, 30, MINUTE) === false) {
            log_audit('RATE_LIMIT_LOGIN', 'auth', null, null, [
                'username' => $username,
                'ip'       => $ipAddress,
                'reason'   => 'brute_force_throttled'
            ]);
            $session->setFlashdata('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam 1 menit.');
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
                
                // --- FITUR REMEMBER ME (HMAC-SHA256 Secret Key, Secure, HttpOnly, SameSite=Lax) ---
                helper('cookie');
                $remember = $this->request->getVar('remember');
                $isSecure = $this->request->isSecure();
                if ($remember) {
                    $tokenHash = $this->buildRememberMeSignature($data);
                    $tokenString = $data['id'] . '::' . $tokenHash;
                    $token = base64_encode($tokenString);
                    // Set cookie untuk 30 hari (2592000 detik) dengan Secure (saat HTTPS), HttpOnly, SameSite=Lax
                    set_cookie('remember_me', $token, 2592000, '', '/', '', $isSecure, true, 'Lax');
                    // Simpan username agar otomatis terisi di form login
                    set_cookie('saved_username', $data['username'], 2592000, '', '/', '', $isSecure, true, 'Lax');
                } else {
                    set_cookie('remember_me', '', -3600, '', '/', '', $isSecure, true, 'Lax');
                    set_cookie('saved_username', '', -3600, '', '/', '', $isSecure, true, 'Lax');
                }
                // -------------------------

                // Catat Log Audit Login
                log_audit('LOGIN', 'users', $data['id'], null, [
                    'username'   => $data['username'],
                    'ip_address' => $ipAddress,
                    'user_agent' => (string) $this->request->getUserAgent()
                ]);

                // Arahkan kembali ke tautan tujuan yang sempat dibuka sebelum login (jika aman & sesuai peran)
                $intendedUrl = (string) ($session->get('intended_url') ?? '');
                $session->remove('intended_url');
                $isEmployee = \App\Libraries\Ketarunaan\KetarunaanPolicy::hasEmployeeRole();
                $isSelfOnly = \App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfAccountOnly();

                if (
                    $intendedUrl !== ''
                    && strpos($intendedUrl, '/') === 0
                    && strpos($intendedUrl, '//') !== 0
                    && !preg_match('/[\r\n]/', $intendedUrl)
                ) {
                    if ($isEmployee) {
                        return redirect()->to(site_url(ltrim($intendedUrl, '/')))->withCookies();
                    }

                    $blockedSelfPrefixes = [
                        '/ketarunaan/dashboard',
                        '/ketarunaan/data-dosen',
                        '/ketarunaan/validasi',
                        '/ketarunaan/laporan',
                        '/ketarunaan/pengaturan',
                    ];
                    $isBlockedSelfTarget = false;
                    foreach ($blockedSelfPrefixes as $prefix) {
                        if (strpos($intendedUrl, $prefix) === 0) {
                            $isBlockedSelfTarget = true;
                            break;
                        }
                    }
                    if ($isSelfOnly && strpos($intendedUrl, '/ketarunaan/') === 0 && !$isBlockedSelfTarget) {
                        return redirect()->to(site_url(ltrim($intendedUrl, '/')))->withCookies();
                    }
                }

                if ($isSelfOnly) {
                    return redirect()->to(site_url(ltrim(\App\Libraries\Ketarunaan\KetarunaanPolicy::getSelfPortalUrl(), '/')))->withCookies();
                }

                return redirect()->to(site_url('dashboard'))->withCookies();
                
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
        $session  = session();
        $userId   = $session->get('id') ?? $session->get('user_id');
        $username = $session->get('username') ?? 'unknown';
        
        if ($userId) {
            log_audit('LOGOUT', 'users', $userId, null, [
                'username'   => $username,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => (string) $this->request->getUserAgent()
            ]);
        }

        // Bersihkan seluruh data sesi terautentikasi & regenerasi ID sesi (hapus sesi lama di server)
        $session->remove([
            'id', 'user_id', 'username', 'nama', 'nip', 'role', 'all_roles',
            'unit', 'unit_id', 'jabatan', 'pangkat', 'status_pegawai', 'foto',
            'user_agent_fingerprint', 'isLoggedIn', 'intended_url',
            'penilaian_active_tab', 'nomor_taruna',
        ]);
        $session->regenerate(true);

        // Hapus cookie remember_me dengan atribut cocok agar benar-benar terhapus di browser
        helper('cookie');
        $isSecure = $this->request->isSecure();
        set_cookie('remember_me', '', -3600, '', '/', '', $isSecure, true, 'Lax');

        // Kembalikan ke halaman login beserta Set-Cookie penghapusan, flash message bersih, dan anti-cache
        return redirect()->to(site_url('login'))
            ->withCookies()
            ->with('success', 'Anda telah keluar dari ECC.')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }
}