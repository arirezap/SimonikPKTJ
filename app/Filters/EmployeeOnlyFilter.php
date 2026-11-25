<?php

namespace App\Filters;

use App\Libraries\Ketarunaan\KetarunaanPolicy;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter isolasi modul ECC 1.0 (Kinerja, Kepegawaian, Akreditasi ECC, Master Data).
 * Memastikan akun layanan mandiri (taruna & alumni) tidak pernah dapat mengakses rute ECC Kinerja.
 */
class EmployeeOnlyFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return;
        }

        helper('role');
        if (KetarunaanPolicy::hasEmployeeRole()) {
            return;
        }

        try {
            helper('audit');
            $userId = session()->get('id') ?? session()->get('user_id');
            if (function_exists('log_audit')) {
                log_audit('BLOCKED_NON_EMPLOYEE_ECC1', 'security', (string) ($userId ?? ''), null, [
                    'role'   => (string) (session()->get('role') ?? ''),
                    'uri'    => uri_string(),
                    'method' => strtoupper($request->getMethod()),
                    'ip'     => $request->getIPAddress(),
                ]);
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan pemblokiran bila pencatatan audit terkendala
        }

        $response   = service('response');
        $accept     = strtolower($request->getHeaderLine('Accept'));
        $uriPath    = ltrim(uri_string(), '/');
        $isNonGet   = strtoupper($request->getMethod()) !== 'GET';
        $isApiRoute = str_starts_with($uriPath, 'notifications/')
                   || str_starts_with($uriPath, 'dashboard/api-')
                   || str_contains($uriPath, '/api-');

        if ($request->isAJAX() || $isNonGet || $isApiRoute || str_contains($accept, 'application/json')) {
            return $response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Akses ditolak. Halaman ini khusus untuk pegawai.',
            ]);
        }

        $portalUrl = KetarunaanPolicy::isSelfAccountOnly()
            ? site_url(ltrim(KetarunaanPolicy::getSelfPortalUrl(), '/'))
            : null;

        return $response->setStatusCode(403)->setBody(view('errors/html/error_403_portal', [
            'backUrl'  => $portalUrl,
            'isTaruna' => KetarunaanPolicy::isSelfTarunaOnly(),
        ]));
    }

    /**
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada pemrosesan lanjutan setelah request
    }
}
