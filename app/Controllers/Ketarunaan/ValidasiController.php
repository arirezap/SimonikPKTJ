<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\RekamJejakService;
use App\Libraries\Ketarunaan\TarunaService;
use App\Libraries\Ketarunaan\ValidasiService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller Pusat Antrean Validasi Lintas Modul ECC 2.0 (/ketarunaan/validasi).
 */
class ValidasiController extends BaseController
{
    protected ValidasiService $validasiService;
    protected TarunaService $tarunaService;
    protected RekamJejakService $rekamJejakService;

    public function __construct(
        ?ValidasiService $validasiService = null,
        ?TarunaService $tarunaService = null,
        ?RekamJejakService $rekamJejakService = null
    ) {
        $this->validasiService   = $validasiService ?? new ValidasiService();
        $this->tarunaService     = $tarunaService ?? new TarunaService();
        $this->rekamJejakService = $rekamJejakService ?? new RekamJejakService();
        helper('ketarunaan');
    }

    /**
     * Halaman utama pusat antrean validasi, tinjauan rencana kembali, dan jejak aktivitas.
     *
     * @return string|RedirectResponse|ResponseInterface
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessValidasiModule()) {
            return redirect()->to(ketarunaan_home_url())
                ->with('error', 'Halaman validasi data khusus staf dan pejabat berwenang.');
        }

        $defaultTanggal = date('Y-m-d');
        $rawTanggal     = trim((string) ($this->request->getGet('tanggal_acuan') ?? $defaultTanggal));
        $dtCheck        = \DateTimeImmutable::createFromFormat('Y-m-d', $rawTanggal);
        $tanggalAcuan   = ($dtCheck !== false && $dtCheck->format('Y-m-d') === $rawTanggal) ? $rawTanggal : $defaultTanggal;
        $activeTab    = strtolower(trim((string) ($this->request->getGet('tab') ?? 'antrean')));
        if (!in_array($activeTab, ['antrean', 'rencana_kembali', 'jejak_aktivitas'], true)) {
            $activeTab = 'antrean';
        }

        $filters = [
            'sumber'        => trim((string) ($this->request->getGet('sumber') ?? 'semua')),
            'status'        => trim((string) ($this->request->getGet('status') ?? 'antrean')),
            'q'             => trim((string) ($this->request->getGet('q') ?? '')),
            'tanggal_acuan' => $tanggalAcuan,
            'entitas'       => trim((string) ($this->request->getGet('entitas') ?? '')),
        ];

        $workspace = $this->validasiService->getWorkspaceData($filters);

        return view('ketarunaan/validasi/index', array_merge($workspace, [
            'page_title' => 'Validasi Data',
            'active_tab' => $activeTab,
            'filters'    => $filters,
            'perms'      => [
                'can_validate'        => KetarunaanPolicy::canValidate(),
                'can_validate_alumni' => KetarunaanPolicy::canValidateAlumni(),
            ],
        ]));
    }

    /**
     * Keputusan validasi pengajuan perubahan status pendidikan taruna (termasuk delegasi kelulusan).
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function putuskanStatus(int $id)
    {
        if (!KetarunaanPolicy::canAccessValidasiModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $db      = \Config\Database::connect();
        $riwayat = $db->table('riwayat_status_taruna')->where('id', $id)->get()->getRowArray();
        if (!$riwayat) {
            return redirect()->to(site_url('ketarunaan/validasi'))->with('error', 'Data pengajuan status tidak ditemukan.');
        }

        $isLulus = strtolower((string) ($riwayat['kode_status'] ?? '')) === 'lulus';
        $allowed = $isLulus ? KetarunaanPolicy::canValidateAlumni() : KetarunaanPolicy::canValidate();
        if (!$allowed) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang memvalidasi perubahan status ini.');
        }

        $keputusan = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'perlu_perbaikan';
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()->withInput()->with('error', 'Catatan perbaikan wajib diisi saat mengembalikan pengajuan.');
        }

        $res = $this->tarunaService->validasiPerubahanStatus($id, $keputusan, $catatan, $this->getActorName());

        return redirect()->to(site_url('ketarunaan/validasi'))
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Keputusan validasi capaian taruna langsung dari antrean terpusat.
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function putuskanCapaian(int $id)
    {
        if (!KetarunaanPolicy::canAccessValidasiModule() || !KetarunaanPolicy::canValidate()) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang memvalidasi capaian taruna.');
        }

        $keputusan = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'perlu_perbaikan';
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()->withInput()->with('error', 'Catatan perbaikan wajib diisi saat mengembalikan capaian.');
        }

        $res = $this->rekamJejakService->processValidation($id, $keputusan, $catatan, $this->getActorName());
        $ok  = !empty($res['status']);

        return redirect()->to(site_url('ketarunaan/validasi'))
            ->with($ok ? 'success' : 'error', (string) ($res['message'] ?? 'Pembaruan validasi selesai.'));
    }

    /**
     * Keputusan validasi pengajuan foto profil baru dari akun taruna.
     *
     * @return RedirectResponse|ResponseInterface
     */
    public function putuskanFoto(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canValidate() && !KetarunaanPolicy::canManageTaruna()) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang memvalidasi foto taruna.');
        }

        $keputusan = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'perlu_perbaikan';
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()->withInput()->with('error', 'Catatan perbaikan wajib diisi saat menolak pengajuan foto.');
        }

        $res      = $this->tarunaService->validasiFotoPending($nomorTaruna, $keputusan, $catatan, $this->getActorName());
        $returnTo = trim((string) ($this->request->getPost('return_to') ?? ''));
        $target   = $returnTo === 'profil'
            ? site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
            : site_url('ketarunaan/validasi');

        return redirect()->to($target)
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    private function getActorName(): string
    {
        return trim((string) (session()->get('nama') ?: session()->get('nama_lengkap') ?: session()->get('username') ?: 'Validator ECC'));
    }
}
