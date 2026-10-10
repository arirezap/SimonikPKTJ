<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'role', 'avatar', 'tanggal', 'audit', 'kinerja'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');

        // Auto Cache-Control HTTP Headers: Paksa browser selalu mengambil HTML/JS versi terbaru dari server tanpa Ctrl+F5
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        // TAMBAHKAN BARIS INI UNTUK MENGATUR BAHASA KE INDONESIA
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'Indonesian');

        // --- AUTO LOGIN (REMEMBER ME) ---
        if (!$this->session->get('isLoggedIn')) {
            helper('cookie');
            $rememberToken = get_cookie('remember_me');
            if ($rememberToken) {
                $decoded = base64_decode($rememberToken);
                if (strpos($decoded, '::') !== false) {
                    list($userId, $hash) = explode('::', $decoded);
                    $db = \Config\Database::connect();
                    $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
                    if ($user) {
                        // Tolak auto-login khusus jika akun berstatus nonaktif (mutasi_keluar & pensiun tetap diizinkan)
                        if (($user['status_pegawai'] ?? 'aktif') === 'nonaktif') {
                            delete_cookie('remember_me');
                        } else {
                            $expectedHmac = hash_hmac('sha256', $user['id'] . '::' . $user['username'] . '::' . $user['password'], (string)config('App')->appTimezone ?: 'ecc_auth_token');
                            $expectedMd5 = md5($user['id'] . $user['username'] . $user['password']);
                            
                            if (hash_equals($expectedHmac, $hash) || hash_equals($expectedMd5, $hash)) {
                                $role_aplikasi = $user['role'];

                                // Load semua role dari tabel pivot user_roles
                                $userRoles = $db->table('user_roles')
                                                ->where('user_id', $user['id'])
                                                ->get()
                                                ->getResultArray();
                                $allRoles = array_column($userRoles, 'role_name');
                                if (!in_array(strtolower($role_aplikasi), array_map('strtolower', $allRoles))) {
                                    $allRoles[] = strtolower($role_aplikasi);
                                }

                                // Regenerasi Session ID untuk Mencegah Session Fixation
                                $this->session->regenerate(true);

                                $userAgent = (string) $this->request->getUserAgent();
                                $fingerprint = hash('sha256', $userAgent);

                                $ses_data = [
                                    'id'                     => $user['id'],
                                    'user_id'                => $user['id'], // Kompatibilitas mundur
                                    'username'               => $user['username'],
                                    'nama'                   => $user['nama_lengkap'], 
                                    'nip'                    => $user['nip'],           
                                    'role'                   => $role_aplikasi,          
                                    'all_roles'              => $allRoles,
                                    'unit'                   => $user['unit'] ?? '-', 
                                    'unit_id'                => $user['unit_id'] ?? null,
                                    'jabatan'                => $user['jabatan'] ?? '-',
                                    'pangkat'                => $user['pangkat'] ?? '-',
                                    'status_pegawai'         => $user['status_pegawai'] ?? 'aktif',
                                    'foto'                   => $user['foto'] ?? null,
                                    'user_agent_fingerprint' => $fingerprint,
                                    'isLoggedIn'             => TRUE
                                ];
                                $this->session->set($ses_data);

                                helper('audit');
                                if (function_exists('log_audit')) {
                                    log_audit('LOGIN_REMEMBER_ME', 'users', $user['id'], null, [
                                        'username'   => $user['username'],
                                        'ip_address' => $this->request->getIPAddress()
                                    ]);
                                }
                            } else {
                                delete_cookie('remember_me');
                            }
                        }
                    } else {
                        delete_cookie('remember_me');
                    }
                } else {
                    delete_cookie('remember_me');
                }
            }
        }
        // --------------------------------
    }

    /**
     * Memeriksa dan membatasi frekuensi ekspor berkas berat (PDF/Excel)
     * Menggunakan CodeIgniter 4 Throttler Service (Default: Maks 5 unduhan per menit per akun/IP)
     *
     * @param string $actionType Nama aksi ekspor untuk audit (cth: 'EXPORT_EXCEL_REKAP_KINERJA')
     * @param int $capacity Batas kapasitas unduhan dalam jendela waktu (default: 5)
     * @param int $seconds Durasi jendela waktu dalam detik (default: 60 / 1 menit)
     * @return bool True jika diizinkan, False jika terkena rate limit
     */
    protected function checkExportRateLimit(string $actionType = 'EXPORT_DOCUMENT', int $capacity = 5, int $seconds = 60): bool
    {
        $throttler = \Config\Services::throttler();
        $userId = (int) ($this->session->get('id') ?? $this->session->get('user_id') ?? 0);
        $ip = $this->request->getIPAddress();
        
        $key = 'export_' . md5($userId . '_' . $ip);

        if ($throttler->check($key, $capacity, $seconds) === false) {
            helper('audit');
            if (function_exists('log_audit')) {
                log_audit('RATE_LIMIT_EXPORT', 'export', (string)$userId, null, [
                    'action' => $actionType,
                    'ip'     => $ip,
                    'reason' => 'capacity_exceeded'
                ]);
            }
            return false;
        }

        return true;
    }
}
