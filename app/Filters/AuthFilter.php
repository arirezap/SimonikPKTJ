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

        // 2. Cek apakah rute ini memerlukan peran tertentu
        if (!empty($arguments)) {
            $userRole = session()->get('role');

            // PERBAIKAN:
            // Pecah string argumen (cth: 'admin,manajemen,aak') menjadi array
            $allowed_roles = [];
            if (is_array($arguments)) {
                // Jika argumen sudah array (jarang terjadi di rute, tapi untuk keamanan)
                $allowed_roles = $arguments;
            } else {
                // Jika argumen adalah string, pecah berdasarkan koma
                $allowed_roles = explode(',', (string) $arguments);
            }
            
            // Cek apakah peran pengguna ada di dalam daftar yang diizinkan
            if (!in_array($userRole, $allowed_roles)) {
                // Jika tidak diizinkan, kembalikan ke dashboard yang sesuai
                if (in_array(session()->get('role'), ['admin', 'manajemen'])) {
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->to('/user/dashboard');
                }
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
