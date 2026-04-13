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
    protected $helpers = [];

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
                        $expectedHash = md5($user['id'] . $user['username'] . $user['password']);
                        if (hash_equals($expectedHash, $hash)) {
                            $role_aplikasi = $user['role'];
                            if (strtolower(trim($user['unit'] ?? '')) === 'satuan penjaminan mutu') {
                                $role_aplikasi = 'spm';
                            }
                            $ses_data = [
                                'id'           => $user['id'],
                                'username'     => $user['username'],
                                'nama'         => $user['nama_lengkap'], 
                                'nip'          => $user['nip'],           
                                'role'         => $role_aplikasi,          
                                'unit'         => $user['unit'] ?? '-', 
                                'jabatan'      => $user['jabatan'] ?? '-',
                                'pangkat'      => $user['pangkat'] ?? '-',
                                'foto'         => $user['foto'] ?? null,
                                'isLoggedIn'   => TRUE
                            ];
                            $this->session->set($ses_data);
                        } else {
                            delete_cookie('remember_me');
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
}
