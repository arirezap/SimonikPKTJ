<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SettingModel;

/**
 * Filter Mode Pemeliharaan (Maintenance Mode)
 * 
 * Mengalihkan seluruh pengguna selain Admin ke halaman pemeliharaan ketika
 * saklar Maintenance Mode diaktifkan oleh Administrator di menu Pengaturan Sistem.
 * Administrator tetap dapat masuk, bernavigasi, dan mengelola aplikasi secara normal.
 */
class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Abaikan eksekusi di CLI / Spark
        if (is_cli()) {
            return;
        }

        // 2. Ambil status mode pemeliharaan dari database (dengan fallback aman jika tabel belum siap)
        try {
            $settingModel = new SettingModel();
            $isMaintenance = $settingModel->getValue('enable_maintenance_mode', '0') === '1';
            $customMessage = $settingModel->getValue('maintenance_message', '');
        } catch (\Throwable $e) {
            $isMaintenance = false;
        }

        if (!$isMaintenance) {
            return;
        }

        // 3. Whitelist path yang tetap boleh diakses saat pemeliharaan
        $uri = $request->getUri();
        $path = trim($uri->getPath(), '/');

        // Whitelist file statis asset
        if (preg_match('/\.(css|js|png|jpg|jpeg|svg|gif|ico|woff|woff2|ttf|map)$/i', $path) || str_starts_with($path, 'assets/')) {
            return;
        }

        // Whitelist autentikasi agar Admin tetap bisa login & logout
        $authPaths = ['login', 'logout', 'auth'];
        foreach ($authPaths as $ap) {
            if ($path === $ap || str_starts_with($path, $ap . '/')) {
                return;
            }
        }

        // 4. Periksa apakah user yang sedang aktif memiliki role Administrator
        $session = session();
        $userRole = $session->get('role') ?? '';
        
        helper('auth');
        $isAdmin = ($userRole === 'admin') || (function_exists('hasRole') && hasRole('admin'));

        if ($isAdmin) {
            // Administrator diizinkan mengakses seluruh aplikasi saat maintenance mode aktif
            return;
        }

        // 5. Tangani permintaan AJAX / API saat maintenance
        $response = service('response');
        if ($request->isAJAX()) {
            $msg = !empty($customMessage) ? $customMessage : 'Sistem Evidence Command Center (ECC) sedang dalam pemeliharaan sementara.';
            return $response->setStatusCode(503)
                            ->setHeader('Retry-After', '30')
                            ->setJSON([
                                'status'  => 'maintenance',
                                'message' => $msg
                            ]);
        }

        // 6. Sajikan halaman Maintenance HTML Bento
        $maintenanceFile = FCPATH . 'maintenance.html';
        if (file_exists($maintenanceFile)) {
            $html = file_get_contents($maintenanceFile);
            if (!empty($customMessage)) {
                // Ganti teks deskripsi default dengan pesan kustom jika ada
                $html = preg_replace('/<p class="desc">.*?<\/p>/s', '<p class="desc">' . htmlspecialchars($customMessage, ENT_QUOTES, 'UTF-8') . '</p>', $html);
            }
        } else {
            $html = '<!DOCTYPE html><html><head><title>Pembaruan Sistem</title></head><body style="font-family:sans-serif;text-align:center;padding:50px;"><h1>Pembaruan Sistem Sedang Berlangsung</h1><p>Mohon tunggu beberapa saat, sistem akan segera kembali normal.</p></body></html>';
        }

        return $response->setStatusCode(503)
                        ->setHeader('Retry-After', '30')
                        ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->setBody($html);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada proses khusus setelah request
    }
}
