<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\LaporanService;
use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller Modul 8.2 Laporan Lintas Modul & Ekspor Resmi (/ketarunaan/laporan).
 */
class LaporanController extends BaseController
{
    protected LaporanService $laporanService;
    protected TarunaService $tarunaService;

    public function __construct(?LaporanService $laporanService = null, ?TarunaService $tarunaService = null)
    {
        $this->laporanService = $laporanService ?? new LaporanService();
        $this->tarunaService  = $tarunaService ?? new TarunaService();
        helper('ketarunaan');
    }

    /**
     * Halaman pratinjau laporan lintas modul sesuai tanggal acuan, prodi, dan angkatan.
     *
     * @return string|ResponseInterface
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessLaporanModule()) {
            return redirect()->to(ketarunaan_home_url())
                ->with('error', 'Menu laporan hanya tersedia bagi staf dan pimpinan.');
        }

        $catalog = $this->laporanService->getAvailableCatalog();
        if (empty($catalog)) {
            return redirect()->to(ketarunaan_home_url())
                ->with('error', 'Anda tidak memiliki izin membuka laporan pada modul ini.');
        }

        $defaultJenis = array_key_first($catalog) ?: 'status_pendidikan';
        $rawJenis     = strtolower(trim((string) ($this->request->getGet('jenis') ?? $defaultJenis)));
        $tanggalAcuan = $this->resolveTanggalAcuan();
        $prodiId      = !empty($this->request->getGet('prodi_id')) ? (int) $this->request->getGet('prodi_id') : null;
        $angkatanId   = !empty($this->request->getGet('angkatan_id')) ? (int) $this->request->getGet('angkatan_id') : null;

        $payload = $this->laporanService->getReportPayload($rawJenis, $tanggalAcuan, $prodiId, $angkatanId);
        if (!$payload['allowed']) {
            return redirect()->to(site_url('ketarunaan/laporan'))
                ->with('error', (string) ($payload['reason'] ?? 'Laporan tidak dapat dibuka.'));
        }

        $refs = $this->tarunaService->getReferenceData();

        return view('ketarunaan/laporan/index', [
            'page_title'    => 'Laporan Ketarunaan',
            'catalog'       => $catalog,
            'payload'       => $payload,
            'prodi_list'    => $refs['prodi_list'],
            'angkatan_list' => $refs['angkatan_list'],
        ]);
    }

    /**
     * Mengunduh laporan resmi dalam format CSV UTF-8 dengan kepala metadata & satuan resmi.
     *
     * @return ResponseInterface
     */
    public function ekspor(): ResponseInterface
    {
        if (!KetarunaanPolicy::canAccessLaporanModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $rawJenis     = strtolower(trim((string) ($this->request->getGet('jenis') ?? 'status_pendidikan')));
        $tanggalAcuan = $this->resolveTanggalAcuan();
        $prodiId      = !empty($this->request->getGet('prodi_id')) ? (int) $this->request->getGet('prodi_id') : null;
        $angkatanId   = !empty($this->request->getGet('angkatan_id')) ? (int) $this->request->getGet('angkatan_id') : null;

        $payload = $this->laporanService->getReportPayload($rawJenis, $tanggalAcuan, $prodiId, $angkatanId);
        if (!$payload['allowed']) {
            return $this->response->setStatusCode((int) ($payload['status_code'] ?? 403), (string) $payload['reason']);
        }

        $csvLines = $this->laporanService->buildCsvLines($payload);
        log_audit('EXPORT', 'laporan_ecc2', $rawJenis, null, [
            'tanggal_acuan'   => $tanggalAcuan,
            'prodi_id'        => $prodiId,
            'angkatan_id'     => $angkatanId,
            'dashboard_count' => $payload['dashboard_count'],
        ]);

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($csvLines as $line) {
            fputcsv($fh, $line);
        }
        rewind($fh);
        $content = stream_get_contents($fh);
        fclose($fh);

        $safeDate = str_replace('-', '', $tanggalAcuan);
        $filename = 'laporan_ecc_' . $rawJenis . '_' . $safeDate . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody((string) $content);
    }

    private function resolveTanggalAcuan(): string
    {
        $defaultTanggal = date('Y-m-d');
        $rawTanggal     = trim((string) ($this->request->getGet('tanggal_acuan') ?? $defaultTanggal));
        $dtCheck        = \DateTimeImmutable::createFromFormat('Y-m-d', $rawTanggal);

        return ($dtCheck !== false && $dtCheck->format('Y-m-d') === $rawTanggal) ? $rawTanggal : $defaultTanggal;
    }
}
