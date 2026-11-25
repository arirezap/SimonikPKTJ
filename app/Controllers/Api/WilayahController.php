<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\WilayahService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Endpoint JSON internal untuk referensi wilayah Indonesia (Provinsi & Kabupaten/Kota)
 * berbasis tabel lokal ref_wilayah (https://github.com/cahyadsn/wilayah - Kepmendagri 2025).
 *
 * Read-only & aman diakses oleh seluruh formulir internal maupun tautan publik Tracer Study.
 */
class WilayahController extends BaseController
{
    protected WilayahService $service;

    public function __construct(?WilayahService $service = null)
    {
        $this->service = $service ?? new WilayahService();
    }

    /**
     * GET /api/wilayah/all
     * Mengembalikan 38 Provinsi + 514 Kabupaten/Kota sekaligus dalam 1 respons ringan ber-cache.
     */
    public function all(): ResponseInterface
    {
        if (!$this->allowRateLimit()) {
            return $this->response->setStatusCode(429)->setJSON([
                'status'  => false,
                'message' => 'Terlalu banyak permintaan. Coba beberapa saat lagi.',
            ]);
        }

        $payload = $this->service->getAllPayload();

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setJSON([
                'status'    => true,
                'source'    => 'Kepmendagri 2025 (cahyadsn/wilayah)',
                'provinsi'  => $payload['provinsi'],
                'kabupaten' => $payload['kabupaten'],
            ]);
    }

    /**
     * GET /api/wilayah/provinsi
     */
    public function provinsi(): ResponseInterface
    {
        if (!$this->allowRateLimit()) {
            return $this->response->setStatusCode(429)->setJSON([
                'status'  => false,
                'message' => 'Terlalu banyak permintaan. Coba beberapa saat lagi.',
            ]);
        }

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setJSON([
                'status' => true,
                'items'  => $this->service->getProvinsiList(),
            ]);
    }

    /**
     * GET /api/wilayah/kabupaten?provinsi=33&q=Tegal
     */
    public function kabupaten(): ResponseInterface
    {
        if (!$this->allowRateLimit()) {
            return $this->response->setStatusCode(429)->setJSON([
                'status'  => false,
                'message' => 'Terlalu banyak permintaan. Coba beberapa saat lagi.',
            ]);
        }

        $prov = trim((string) ($this->request->getGet('provinsi') ?? ''));
        $q    = trim((string) ($this->request->getGet('q') ?? ''));

        $provKode = $prov !== '' ? $this->service->resolveProvinsiKode($prov) : null;
        $items    = $this->service->getKabupatenList($prov !== '' ? $prov : null, $q !== '' ? $q : null);

        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setJSON([
                'status'        => true,
                'provinsi_kode' => $provKode,
                'items'         => $items,
            ]);
    }

    /**
     * GET /api/wilayah/kecamatan?kabupaten=33.76
     */
    public function kecamatan(): ResponseInterface
    {
        if (!$this->allowRateLimit()) {
            return $this->response->setStatusCode(429)->setJSON([
                'status'  => false,
                'message' => 'Terlalu banyak permintaan. Coba beberapa saat lagi.',
            ]);
        }

        $kab = trim((string) ($this->request->getGet('kabupaten') ?? ''));
        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setJSON([
                'status' => true,
                'items'  => $this->service->getKecamatanList($kab),
            ]);
    }

    /**
     * GET /api/wilayah/desa?kecamatan=33.76.01
     */
    public function desa(): ResponseInterface
    {
        if (!$this->allowRateLimit()) {
            return $this->response->setStatusCode(429)->setJSON([
                'status'  => false,
                'message' => 'Terlalu banyak permintaan. Coba beberapa saat lagi.',
            ]);
        }

        $kec = trim((string) ($this->request->getGet('kecamatan') ?? ''));
        return $this->response
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setJSON([
                'status' => true,
                'items'  => $this->service->getDesaList($kec),
            ]);
    }

    private function allowRateLimit(): bool
    {
        try {
            $throttler = \Config\Services::throttler();
            $key       = 'api_wilayah_' . md5((string) $this->request->getIPAddress());
            return $throttler->check($key, 120, 60) !== false;
        } catch (\Throwable $e) {
            return true;
        }
    }
}
