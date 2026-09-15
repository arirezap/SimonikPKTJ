<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah pengguna sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // 2. Pertahanan Pembajakan Sesi (Session Hijacking Defense via User-Agent Fingerprint)
        $savedFingerprint = session()->get('user_agent_fingerprint');
        $currentAgent = (string) $request->getUserAgent();
        $currentFingerprint = hash('sha256', $currentAgent);

        if ($savedFingerprint === null) {
            // Migrasi transparan: jika sesi aktif dibuat sebelum fitur ini aktif, rekam fingerprint sekarang
            session()->set('user_agent_fingerprint', $currentFingerprint);
        } elseif (!hash_equals($savedFingerprint, $currentFingerprint)) {
            // Terdeteksi ketidakcocokan identitas peramban (potensi session hijacking):
            helper('audit');
            $userId = session()->get('id') ?? session()->get('user_id');
            if (function_exists('log_audit')) {
                log_audit('SESSION_HIJACK_ATTEMPT', 'auth', $userId, null, [
                    'reason' => 'user_agent_mismatch',
                    'ip'     => $request->getIPAddress(),
                    'agent'  => $currentAgent
                ]);
            }

            session()->destroy();
            helper('cookie');
            delete_cookie('remember_me');

            session()->setFlashdata('error', 'Sesi Anda telah berakhir demi keamanan. Silakan masuk kembali.');
            return redirect()->to('/login');
        }

        // 3. Cek apakah rute ini memerlukan peran tertentu
        if (!empty($arguments)) {
            // PERBAIKAN:
            // Pecah string argumen (cth: 'admin,manajemen,aak') menjadi array
            $allowed_roles = [];
            if (is_array($arguments)) {
                $allowed_roles = $arguments;
            } else {
                $allowed_roles = explode(',', (string) $arguments);
            }
            
            // Gunakan hasAnyRole() untuk mendukung multi-role (tabel pivot)
            helper('role');
            if (!hasAnyRole($allowed_roles)) {
                // Jika tidak diizinkan, kembalikan ke dashboard
                return redirect()->to('/dashboard');
            }
        }
    }

    /**
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa setelah request
    }
}
