<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\DashboardService;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller Dashboard Ringkasan ECC 2.0 (Ketarunaan & Alumni).
 * Mematuhi Single Responsibility Principle (SRP): hanya mengatur request, validasi,
 * pemanggilan service, dan pengiriman response.
 */
class DashboardController extends BaseController
{
    protected DashboardService $dashboardService;
    protected TarunaService $tarunaService;

    public function __construct(?DashboardService $dashboardService = null, ?TarunaService $tarunaService = null)
    {
        $this->dashboardService = $dashboardService ?? new DashboardService();
        $this->tarunaService    = $tarunaService ?? new TarunaService();
        helper('ketarunaan');
    }

    /**
     * Tampilan utama Dashboard Ringkasan ECC 2.0.
     *
     * @return string|RedirectResponse
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard')->with('error', 'Anda belum memiliki akses ke Modul Ketarunaan.');
        }

        $rawTanggal   = trim((string) ($this->request->getGet('tanggal_acuan') ?? '2026-10-30'));
        $tanggalAcuan = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawTanggal) ? $rawTanggal : '2026-10-30';
        $prodiId      = !empty($this->request->getGet('prodi_id')) ? (int) $this->request->getGet('prodi_id') : null;
        $angkatanId   = !empty($this->request->getGet('angkatan_id')) ? (int) $this->request->getGet('angkatan_id') : null;

        $stats = $this->dashboardService->getAggregatedStats($tanggalAcuan, $prodiId, $angkatanId);
        $refs  = $this->tarunaService->getReferenceData();

        $data = array_merge($stats, [
            'page_title'    => 'Dashboard ECC 2.0',
            'tanggal_acuan' => $tanggalAcuan,
            'prodi_id'      => $prodiId,
            'angkatan_id'   => $angkatanId,
            'prodi_list'    => $refs['prodi_list'],
            'angkatan_list' => $refs['angkatan_list'],
        ]);

        return view('ketarunaan/dashboard', $data);
    }

    /**
     * Endpoint AJAX untuk pembaruan data filter secara dinamis.
     *
     * @return ResponseInterface
     */
    public function filterData(): ResponseInterface
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'    => 'error',
                'message'   => 'Akses tidak diizinkan.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $rawTanggal   = trim((string) ($this->request->getPost('tanggal_acuan') ?? '2026-10-30'));
        $tanggalAcuan = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawTanggal) ? $rawTanggal : '2026-10-30';
        $prodiId      = !empty($this->request->getPost('prodi_id')) ? (int) $this->request->getPost('prodi_id') : null;
        $angkatanId   = !empty($this->request->getPost('angkatan_id')) ? (int) $this->request->getPost('angkatan_id') : null;

        try {
            $stats = $this->dashboardService->getAggregatedStats($tanggalAcuan, $prodiId, $angkatanId);

            return $this->response->setJSON([
                'status'    => 'success',
                'data'      => $stats,
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memfilter Dashboard Ketarunaan: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status'    => 'error',
                'message'   => 'Gagal memuat data filter. Silakan coba lagi.',
                'csrf_hash' => csrf_hash(),
            ]);
        }
    }
}
