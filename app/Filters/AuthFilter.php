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
        // Cek apakah pengguna sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Cek apakah filter ini memerlukan peran tertentu (misal: 'admin')
        if (!empty($arguments)) {
            $userRole = session()->get('role');
            // Jika peran pengguna tidak ada di dalam daftar peran yang diizinkan
            if (!in_array($userRole, $arguments)) {
                // Redirect ke halaman yang sesuai atau tampilkan 'access denied'
                return redirect()->to('/'); 
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