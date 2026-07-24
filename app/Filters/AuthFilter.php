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
