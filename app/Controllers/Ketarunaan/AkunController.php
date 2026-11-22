<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Controller pengelolaan keamanan akun (Kata Sandi) khusus akun layanan mandiri taruna/alumni di portal ECC 2.0.
 */
class AkunController extends BaseController
{
    protected User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Halaman Ubah Kata Sandi di dalam shell Portal Ketarunaan (ECC 2.0).
     *
     * @return string|RedirectResponse
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard');
        }

        if (!KetarunaanPolicy::isSelfAccountOnly()) {
            return redirect()->to(site_url('profile'));
        }

        $userId = (int) (session()->get('id') ?? session()->get('user_id') ?? 0);
        $user   = $this->userModel->select('id, username, nama_lengkap, email, role')->find($userId);

        if (!$user) {
            return redirect()->to(site_url('logout'));
        }

        return view('ketarunaan/akun/keamanan', [
            'page_title' => 'Ubah Kata Sandi',
            'user'       => $user,
            'backUrl'    => site_url(ltrim(KetarunaanPolicy::getSelfPortalUrl(), '/')),
        ]);
    }

    /**
     * Memproses pembaruan kata sandi mandiri taruna/alumni secara aman.
     */
    public function updateKataSandi(): RedirectResponse
    {
        if (!KetarunaanPolicy::canAccessModule() || !KetarunaanPolicy::isSelfAccountOnly()) {
            return redirect()->to(site_url(ltrim(KetarunaanPolicy::getSelfPortalUrl(), '/')))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $userId = (int) (session()->get('id') ?? session()->get('user_id') ?? 0);
        $user   = $this->userModel->select('id, username, password')->find($userId);

        if (!$user) {
            return redirect()->to(site_url('logout'));
        }

        $rules = [
            'current_password' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Kata sandi saat ini wajib diisi.',
                ],
            ],
            'password' => [
                'rules'  => 'required|min_length[6]|max_length[100]',
                'errors' => [
                    'required'   => 'Kata sandi baru wajib diisi.',
                    'min_length' => 'Kata sandi baru minimal 6 karakter.',
                    'max_length' => 'Kata sandi baru maksimal 100 karakter.',
                ],
            ],
            'password_confirm' => [
                'rules'  => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi kata sandi wajib diisi.',
                    'matches'  => 'Konfirmasi kata sandi tidak cocok.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('ketarunaan/keamanan-akun'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('password');
        $storedHash      = (string) ($user['password'] ?? '');

        $isCurrentValid = password_verify($currentPassword, $storedHash)
            || ($storedHash === $currentPassword)
            || ($storedHash === md5($currentPassword));

        if (!$isCurrentValid) {
            return redirect()->to(site_url('ketarunaan/keamanan-akun'))
                ->with('errors', ['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $this->userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);

            log_audit('UPDATE', 'users', (string) $userId, null, [
                'password' => '[DIUBAH]',
                'konteks'  => 'keamanan_akun_mandiri',
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->to(site_url('ketarunaan/keamanan-akun'))
                    ->with('errors', ['save' => 'Gagal memperbarui kata sandi. Silakan coba lagi.']);
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal memperbarui kata sandi taruna ID ' . $userId . ': ' . $e->getMessage());
            return redirect()->to(site_url('ketarunaan/keamanan-akun'))
                ->with('errors', ['save' => 'Gagal memperbarui kata sandi. Silakan coba lagi.']);
        }

        return redirect()->to(site_url('ketarunaan/keamanan-akun'))
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
