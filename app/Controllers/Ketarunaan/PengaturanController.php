<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\PengaturanService;
use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller Modul 8.3 Pengaturan Data Rujukan, Matriks Peran, & Parameter Operasional (/ketarunaan/pengaturan).
 */
class PengaturanController extends BaseController
{
    protected PengaturanService $pengaturanService;
    protected TarunaService $tarunaService;

    public function __construct(?PengaturanService $pengaturanService = null, ?TarunaService $tarunaService = null)
    {
        $this->pengaturanService = $pengaturanService ?? new PengaturanService();
        $this->tarunaService     = $tarunaService ?? new TarunaService();
        helper('ketarunaan');
    }

    /**
     * Halaman utama Pengaturan ECC 2.0 (Tab Data Rujukan, Matriks Kewenangan Peran, Parameter Operasional).
     *
     * @return string|ResponseInterface
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessPengaturanModule()) {
            return redirect()->to(ketarunaan_home_url())
                ->with('error', 'Menu pengaturan tidak tersedia untuk peran akun Anda.');
        }

        $activeTab = strtolower(trim((string) ($this->request->getGet('tab') ?? 'referensi')));
        if (!in_array($activeTab, ['referensi', 'peran', 'operasional'], true)) {
            $activeTab = 'referensi';
        }

        $activeRef = strtolower(trim((string) ($this->request->getGet('ref') ?? 'prodi')));
        if (!in_array($activeRef, ['prodi', 'angkatan', 'tahun_akademik', 'kelas', 'wilayah_3tp'], true)) {
            $activeRef = 'prodi';
        }

        $workspace = $this->pengaturanService->getWorkspaceData();
        $refs      = $this->tarunaService->getReferenceData();

        return view('ketarunaan/pengaturan/index', array_merge($workspace, [
            'page_title'    => 'Pengaturan Ketarunaan',
            'active_tab'    => $activeTab,
            'active_ref'    => $activeRef,
            'prodi_list'    => $refs['prodi_list'],
            'angkatan_list' => $refs['angkatan_list'],
            'perms'         => [
                'can_manage_referensi'   => KetarunaanPolicy::canManageReferensi(),
                'can_manage_operasional' => KetarunaanPolicy::canManageOperationalSettings(),
                'can_manage_users'       => function_exists('hasAnyRole') && hasAnyRole(['admin', 'kepegawaian']),
            ],
        ]));
    }

    /**
     * Tambah atau perbarui baris data rujukan secara non-destruktif.
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function simpanReferensi()
    {
        if (!KetarunaanPolicy::canManageReferensi()) {
            $target = KetarunaanPolicy::canAccessPengaturanModule() ? site_url('ketarunaan/pengaturan') : ketarunaan_home_url();
            return redirect()->to($target)->with('error', 'Anda tidak berwenang mengubah data rujukan.');
        }

        $jenis = strtolower(trim((string) ($this->request->getPost('jenis_referensi') ?? 'prodi')));
        $idRaw = trim((string) ($this->request->getPost('ref_id') ?? ''));
        $id    = ($idRaw !== '' && ctype_digit($idRaw) && (int) $idRaw > 0) ? (int) $idRaw : null;

        $res = $this->pengaturanService->saveReferensi($jenis, (array) $this->request->getPost(), $id);

        $redirect = redirect()->to(site_url('ketarunaan/pengaturan?tab=referensi&ref=' . urlencode($jenis)));
        if (!$res['success']) {
            return $redirect->withInput()->with('error', $res['message']);
        }

        return $redirect->with('success', $res['message']);
    }

    /**
     * Aktifkan atau nonaktifkan baris data rujukan tanpa menghapus data lama (GOV-05).
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function toggleReferensi(string $jenis, int $id)
    {
        if (!KetarunaanPolicy::canManageReferensi()) {
            $target = KetarunaanPolicy::canAccessPengaturanModule() ? site_url('ketarunaan/pengaturan') : ketarunaan_home_url();
            return redirect()->to($target)->with('error', 'Anda tidak berwenang mengubah status data rujukan.');
        }

        $res = $this->pengaturanService->toggleReferensiStatus($jenis, $id);

        return redirect()->to(site_url('ketarunaan/pengaturan?tab=referensi&ref=' . urlencode($jenis)))
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Menyimpan parameter operasional ECC 2.0 (penanda simulasi/operasional, skala IP, batas jam lowongan).
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function simpanOperasional()
    {
        if (!KetarunaanPolicy::canManageOperationalSettings()) {
            $target = KetarunaanPolicy::canAccessPengaturanModule() ? site_url('ketarunaan/pengaturan?tab=operasional') : ketarunaan_home_url();
            return redirect()->to($target)->with('error', 'Anda tidak berwenang mengubah parameter operasional.');
        }

        $res      = $this->pengaturanService->saveOperationalSettings((array) $this->request->getPost());
        $redirect = redirect()->to(site_url('ketarunaan/pengaturan?tab=operasional'));

        if (!$res['success']) {
            return $redirect->withInput()->with('error', $res['message']);
        }

        return $redirect->with('success', $res['message']);
    }
}
