<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\LowonganService;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Controller Modul 6: Lowongan Pekerjaan (ECC 2.0).
 * Mengatur manajemen perusahaan mitra, pengajuan & penyuntingan lowongan,
 * dokumen pendukung berizin terbatas (HTTP 403), verifikasi checklist vs publikasi terpisah,
 * serta kendali masa tayang dan penutupan manual beralasan.
 */
class LowonganController extends BaseController
{
    protected LowonganService $lowonganService;

    public function __construct(?LowonganService $lowonganService = null)
    {
        $this->lowonganService = $lowonganService ?? new LowonganService();
        helper(['ketarunaan', 'form', 'audit']);
    }

    /**
     * Halaman Daftar Lowongan Pekerjaan (/ketarunaan/lowongan).
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessLowonganModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Lowongan Pekerjaan.');
        }

        $canViewStaffBoard = $this->canViewStaffBoard();
        $waktuAcuan        = $this->resolveWaktuAcuan();
        $refs              = $this->lowonganService->getReferenceOptions();

        $filters = [
            'q'                => trim((string) ($this->request->getGet('q') ?? '')),
            'perusahaan_id'    => trim((string) ($this->request->getGet('perusahaan_id') ?? 'all')),
            'prodi_id'         => trim((string) ($this->request->getGet('prodi_id') ?? 'all')),
            'jenis_pekerjaan'  => trim((string) ($this->request->getGet('jenis_pekerjaan') ?? 'all')),
            'status_validasi'  => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
            'status_efektif'   => trim((string) ($this->request->getGet('status_efektif') ?? 'all')),
            'sort'             => trim((string) ($this->request->getGet('sort') ?? '')),
            'dir'              => strtolower(trim((string) ($this->request->getGet('dir') ?? 'asc'))) === 'desc' ? 'desc' : 'asc',
            'page'             => max(1, (int) ($this->request->getGet('page') ?? 1)),
        ];

        $directory = $this->lowonganService->getDirectoryData($filters, $canViewStaffBoard, $waktuAcuan);

        return view('ketarunaan/lowongan/index', [
            'title'             => 'Lowongan Pekerjaan - Evidence Command Center (ECC)',
            'activeTab'         => 'daftar',
            'refs'              => $refs,
            'filters'           => $filters,
            'kpi'               => $directory['kpi'],
            'rows'              => $directory['rows'],
            'pagination'        => $directory['pagination'],
            'waktuAcuan'        => $waktuAcuan,
            'canViewStaffBoard' => $canViewStaffBoard,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Formulir Tambah Lowongan Baru (/ketarunaan/lowongan/tambah).
     */
    public function create()
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Hanya staf karir atau pejabat terkait yang dapat mencatat lowongan.');
        }

        $refs = $this->lowonganService->getReferenceOptions();

        return view('ketarunaan/lowongan/form', [
            'title'             => 'Tambah Lowongan Pekerjaan - Evidence Command Center (ECC)',
            'activeTab'         => 'daftar',
            'isEdit'            => false,
            'lowongan'          => null,
            'refs'              => $refs,
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Menyimpan Lowongan Baru (POST /ketarunaan/lowongan/simpan).
     */
    public function store()
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $actionMode = $this->request->getPost('action_mode') === 'ajukan' ? 'ajukan' : 'draf';
        $prodiIds   = (array) ($this->request->getPost('prodi_ids') ?? []);
        $actorName  = $this->getActorName();

        $res = $this->lowonganService->saveLowongan(null, (array) $this->request->getPost(), $prodiIds, $actionMode, $actorName);
        if (!$res['status']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/detail/' . (int) $res['id']))
            ->with('success', $res['message']);
    }

    /**
     * Formulir Edit Lowongan (/ketarunaan/lowongan/edit/{id}).
     */
    public function edit(int $id)
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Anda tidak memiliki izin mengubah lowongan.');
        }

        $detail = $this->lowonganService->getLowonganDetail($id, true, true, $this->resolveWaktuAcuan());
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Data lowongan tidak ditemukan.');
        }

        return view('ketarunaan/lowongan/form', [
            'title'             => 'Ubah Lowongan - Evidence Command Center (ECC)',
            'activeTab'         => 'daftar',
            'isEdit'            => true,
            'lowongan'          => $detail,
            'refs'              => $this->lowonganService->getReferenceOptions(),
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Memperbarui Lowongan (/ketarunaan/lowongan/update/{id}).
     */
    public function update(int $id)
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $actionMode = $this->request->getPost('action_mode') === 'ajukan' ? 'ajukan' : 'draf';
        $prodiIds   = (array) ($this->request->getPost('prodi_ids') ?? []);
        $actorName  = $this->getActorName();

        $res = $this->lowonganService->saveLowongan($id, (array) $this->request->getPost(), $prodiIds, $actionMode, $actorName);
        if (!$res['status']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        $flashType = !empty($res['reverified']) ? 'warning' : 'success';

        return redirect()->to(base_url('ketarunaan/lowongan/detail/' . $id))
            ->with($flashType, $res['message']);
    }

    /**
     * Halaman Detail Lowongan & Kanal Lamaran Eksternal (/ketarunaan/lowongan/detail/{id}).
     */
    public function detail(int $id)
    {
        if (!KetarunaanPolicy::canAccessLowonganModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Lowongan Pekerjaan.');
        }

        $canViewStaffBoard    = $this->canViewStaffBoard();
        $canViewInternalProof = KetarunaanPolicy::canViewInternalLowonganProof();
        $waktuAcuan           = $this->resolveWaktuAcuan();

        $detail = $this->lowonganService->getLowonganDetail($id, $canViewStaffBoard, $canViewInternalProof, $waktuAcuan);
        if (!$detail) {
            if (!$canViewStaffBoard) {
                return redirect()->to(base_url('ketarunaan/lowongan'))
                    ->with('error', 'Lowongan belum ditayangkan atau sudah berakhir.');
            }

            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Data lowongan tidak ditemukan.');
        }

        return view('ketarunaan/lowongan/detail', [
            'title'                => esc($detail['judul_lowongan']) . ' - Lowongan Pekerjaan',
            'activeTab'            => 'daftar',
            'lowongan'             => $detail,
            'refs'                 => $this->lowonganService->getReferenceOptions(),
            'waktuAcuan'           => $waktuAcuan,
            'canViewStaffBoard'    => $canViewStaffBoard,
            'canViewInternalProof' => $canViewInternalProof,
            'perms'                => $this->getPermissions(),
        ]);
    }

    /**
     * Halaman Data Perusahaan Mitra & Verifikasi Kontak (/ketarunaan/lowongan/perusahaan).
     */
    public function perusahaan()
    {
        if (!$this->canViewStaffBoard()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Hanya staf dan pimpinan yang dapat mengakses data perusahaan mitra.');
        }

        $filters = [
            'q'           => trim((string) ($this->request->getGet('q') ?? '')),
            'is_verified' => trim((string) ($this->request->getGet('is_verified') ?? 'all')),
        ];
        $editId   = (int) ($this->request->getGet('edit') ?? 0);
        $selected = $editId > 0 ? $this->lowonganService->getPerusahaanById($editId) : null;

        return view('ketarunaan/lowongan/perusahaan', [
            'title'             => 'Perusahaan & Kontak Rekrutmen - Lowongan Pekerjaan',
            'activeTab'         => 'perusahaan',
            'filters'           => $filters,
            'companies'         => $this->lowonganService->listPerusahaan($filters, $this->resolveWaktuAcuan()),
            'selected'          => $selected,
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Menyimpan / Memperbarui Data Perusahaan Mitra (POST /ketarunaan/lowongan/perusahaan/simpan).
     */
    public function savePerusahaan()
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $idRaw = trim((string) ($this->request->getPost('perusahaan_id') ?? ''));
        $id    = ($idRaw !== '' && ctype_digit($idRaw) && (int) $idRaw > 0) ? (int) $idRaw : null;

        $res = $this->lowonganService->savePerusahaan(
            $id,
            (array) $this->request->getPost(),
            KetarunaanPolicy::canValidateLowongan(),
            $this->getActorName()
        );

        if (!$res['status']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/perusahaan?edit=' . (int) $res['id']))
            ->with('success', $res['message']);
    }

    /**
     * Halaman Dokumen dan Sumber Lowongan (/ketarunaan/lowongan/dokumen atau dokumen/{id}).
     */
    public function dokumen(?int $lowonganId = null)
    {
        if (!KetarunaanPolicy::canViewInternalLowonganProof()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Dokumen dan sumber internal hanya dapat diakses oleh staf pengelola dan pejabat terkait.');
        }

        $queryLowId = (int) ($this->request->getGet('lowongan_id') ?? 0);
        $targetId   = $lowonganId ?? ($queryLowId > 0 ? $queryLowId : null);
        $docData    = $this->lowonganService->getDokumenPageData($targetId);

        return view('ketarunaan/lowongan/dokumen', [
            'title'             => 'Dokumen & Sumber Lowongan - Evidence Command Center (ECC)',
            'activeTab'         => 'dokumen',
            'vacancies'         => $docData['vacancies'],
            'selected'          => $docData['selected'],
            'documents'         => $docData['documents'],
            'refs'              => $this->lowonganService->getReferenceOptions(),
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Menyimpan Dokumen / Tautan Sumber Baru (POST /ketarunaan/lowongan/dokumen/{id}/simpan).
     */
    public function saveDokumen(int $lowonganId)
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $savedFileName = null;
        $uploadedFile  = $this->request->getFile('berkas_dokumen');

        try {
            if ($uploadedFile instanceof UploadedFile && $uploadedFile->getError() !== UPLOAD_ERR_NO_FILE) {
                $savedFileName = $this->storeProofFile($uploadedFile);
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $res = $this->lowonganService->saveDokumen(
            $lowonganId,
            (array) $this->request->getPost(),
            $savedFileName,
            $this->getActorName()
        );

        if (!$res['status']) {
            if ($savedFileName !== null) {
                $cleanup = FCPATH . 'assets/uploads/ketarunaan/lowongan/' . basename($savedFileName);
                if (is_file($cleanup)) {
                    @unlink($cleanup);
                }
            }

            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/dokumen/' . $lowonganId))
            ->with('success', $res['message']);
    }

    /**
     * Menghapus Dokumen Lowongan (POST /ketarunaan/lowongan/dokumen/hapus/{docId}).
     */
    public function removeDokumen(int $docId)
    {
        if (!KetarunaanPolicy::canManageLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $res = $this->lowonganService->removeDokumen($docId);
        if (!$res['status']) {
            return redirect()->back()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/dokumen/' . (int) $res['lowongan_id']))
            ->with('success', $res['message']);
    }

    /**
     * Mengunduh Berkas Dokumen Lowongan dengan Pengamanan Bukti Internal HTTP 403 (LOW-03 & NFR-02).
     */
    public function unduhDokumen(int $docId)
    {
        if (!KetarunaanPolicy::canAccessLowonganModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $doc = $this->lowonganService->getDokumenForDownload($docId);
        if (!$doc) {
            return $this->response->setStatusCode(404, 'Dokumen tidak ditemukan.');
        }

        $isInternal           = (int) ($doc['is_internal'] ?? 1) === 1;
        $canViewInternalProof = KetarunaanPolicy::canViewInternalLowonganProof();

        if ($isInternal && !$canViewInternalProof) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak. Dokumen internal hanya dapat diakses oleh staf karir dan pejabat terkait.');
        }

        if (!$this->canViewStaffBoard() && ketarunaan_lowongan_status_efektif($doc, $this->resolveWaktuAcuan()) !== 'terbuka') {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak. Dokumen milik lowongan yang belum ditayangkan.');
        }

        $namaBerkas = trim((string) ($doc['nama_berkas'] ?? ''));
        if ($namaBerkas !== '') {
            if (ketarunaan_is_http_url($namaBerkas, 255)) {
                log_audit('EXPORT', 'lowongan_dokumen', (string) $docId, null, ['judul' => $doc['judul_dokumen'], 'url_sumber' => $namaBerkas]);
                return redirect()->to($namaBerkas);
            }
            $safePath = FCPATH . 'assets/uploads/ketarunaan/lowongan/' . basename($namaBerkas);
            if (is_file($safePath)) {
                log_audit('EXPORT', 'lowongan_dokumen', (string) $docId, null, ['judul' => $doc['judul_dokumen']]);
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->download($safePath, null)
                    ->setFileName(basename($safePath));
            }
        }

        $urlSumber = ketarunaan_sanitize_external_url($doc['url_sumber'] ?? null, false);
        if ($urlSumber !== null) {
            log_audit('EXPORT', 'lowongan_dokumen', (string) $docId, null, ['judul' => $doc['judul_dokumen'], 'url_sumber' => $urlSumber]);
            return redirect()->to($urlSumber);
        }

        return redirect()->back()->with('warning', 'Tautan atau berkas dokumen belum tersedia.');
    }

    /**
     * Halaman Antrean & Pemeriksaan Checklist Verifikasi vs Publikasi Terpisah (/ketarunaan/lowongan/verifikasi).
     */
    public function verifikasi(?int $lowonganId = null)
    {
        if (!KetarunaanPolicy::canManageLowongan() && !KetarunaanPolicy::canValidateLowongan()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Anda tidak memiliki izin membuka antrean verifikasi lowongan.');
        }

        $queryId  = (int) ($this->request->getGet('lowongan_id') ?? 0);
        $targetId = $lowonganId ?? ($queryId > 0 ? $queryId : null);
        $verData  = $this->lowonganService->getVerifikasiPageData($targetId, $this->resolveWaktuAcuan());

        return view('ketarunaan/lowongan/verifikasi', [
            'title'             => 'Verifikasi Lowongan - Evidence Command Center (ECC)',
            'activeTab'         => 'verifikasi',
            'queue'             => $verData['queue'],
            'selected'          => $verData['selected'],
            'selectedDocs'      => $verData['selectedDocs'],
            'refs'              => $this->lowonganService->getReferenceOptions(),
            'waktuAcuan'        => $this->resolveWaktuAcuan(),
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Memproses Keputusan Checklist Verifikasi Lowongan (POST /ketarunaan/lowongan/verifikasi/{id}/simpan).
     */
    public function saveVerifikasi(int $id)
    {
        if (!KetarunaanPolicy::canValidateLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak. Hanya validator yang berwenang menetapkan hasil pemeriksaan.');
        }

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? ''));
        $checklist = (array) ($this->request->getPost('checklist') ?? []);
        $catatan   = (string) ($this->request->getPost('catatan_pemeriksa') ?? '');

        $res = $this->lowonganService->saveVerifikasi($id, $checklist, $keputusan, $catatan, $this->getActorName());
        if (!$res['status']) {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/verifikasi/' . $id))
            ->with('success', $res['message']);
    }

    /**
     * Mengubah Status Publikasi Terpisah (POST /ketarunaan/lowongan/publikasi/{id}).
     */
    public function publikasi(int $id)
    {
        if (!KetarunaanPolicy::canPublishLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $aksi   = trim((string) ($this->request->getPost('aksi_publikasi') ?? 'terbit'));
        $alasan = trim((string) ($this->request->getPost('alasan_penutupan') ?? ''));
        $res    = $this->lowonganService->setPublikasi($id, $aksi, $alasan, $this->getActorName(), $this->resolveWaktuAcuan());

        if (!$res['status']) {
            return redirect()->back()->with('error', $res['message']);
        }

        return redirect()->back()->with('success', $res['message']);
    }

    /**
     * Penutupan Manual Lowongan dengan Alasan (POST /ketarunaan/lowongan/tutup/{id}).
     */
    public function tutup(int $id)
    {
        if (!KetarunaanPolicy::canPublishLowongan()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }

        $pilihan   = trim((string) ($this->request->getPost('alasan_pilihan') ?? ''));
        $tambahan  = trim((string) ($this->request->getPost('alasan_custom') ?? ''));
        $alasan    = $pilihan === 'Lainnya' ? $tambahan : ($pilihan !== '' ? $pilihan : $tambahan);

        $res = $this->lowonganService->setPublikasi($id, 'ditutup', $alasan, $this->getActorName(), $this->resolveWaktuAcuan());
        if (!$res['status']) {
            return redirect()->back()->with('error', $res['message']);
        }

        return redirect()->to(base_url('ketarunaan/lowongan/riwayat/' . $id))
            ->with('success', $res['message']);
    }

    /**
     * Halaman Riwayat Validasi, Publikasi & Pengendalian Masa Tayang (/ketarunaan/lowongan/riwayat).
     */
    public function riwayat(?int $lowonganId = null)
    {
        if (!$this->canViewStaffBoard()) {
            return redirect()->to(base_url('ketarunaan/lowongan'))
                ->with('error', 'Riwayat proses hanya tersedia untuk staf pengelola dan pimpinan.');
        }

        $queryId  = (int) ($this->request->getGet('lowongan_id') ?? 0);
        $targetId = $lowonganId ?? ($queryId > 0 ? $queryId : null);
        $riwayatData = $this->lowonganService->getRiwayatPageData($targetId, $this->resolveWaktuAcuan());

        return view('ketarunaan/lowongan/riwayat', [
            'title'             => 'Riwayat & Masa Tayang Lowongan - Evidence Command Center (ECC)',
            'activeTab'         => 'riwayat',
            'vacancies'         => $riwayatData['vacancies'],
            'selected'          => $riwayatData['selected'],
            'refs'              => $this->lowonganService->getReferenceOptions(),
            'waktuAcuan'        => $this->resolveWaktuAcuan(),
            'canViewStaffBoard' => true,
            'perms'             => $this->getPermissions(),
        ]);
    }

    /**
     * Ekspor Daftar Lowongan ke CSV sesuai kewenangan pengguna (/ketarunaan/lowongan/ekspor).
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessLowonganModule() || KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->denyAccess('Anda tidak memiliki hak akses untuk mengekspor data lowongan kerja.');
        }

        $filters = [
            'q'               => trim((string) ($this->request->getGet('q') ?? '')),
            'perusahaan_id'   => trim((string) ($this->request->getGet('perusahaan_id') ?? 'all')),
            'prodi_id'        => trim((string) ($this->request->getGet('prodi_id') ?? 'all')),
            'jenis_pekerjaan' => trim((string) ($this->request->getGet('jenis_pekerjaan') ?? 'all')),
            'status_validasi' => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
            'status_efektif'  => trim((string) ($this->request->getGet('status_efektif') ?? 'all')),
        ];

        $csvRows = $this->lowonganService->getExportRows($filters, $this->canViewStaffBoard(), $this->resolveWaktuAcuan());
        log_audit('EXPORT', 'lowongan_pekerjaan', 'list', null, ['rows' => max(0, count($csvRows) - 1)]);

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($csvRows as $line) {
            fputcsv($fh, $line);
        }
        rewind($fh);
        $content = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="lowongan_kerja_ecc_' . date('Ymd') . '.csv"')
            ->setBody((string) $content);
    }

    /**
     * Menyimpan berkas pendukung lowongan dengan penamaan acak dan validasi ketat (NFR-01).
     *
     * @throws \RuntimeException
     */
    private function storeProofFile(UploadedFile $file): string
    {
        if (!$file->isValid() || $file->hasMoved()) {
            throw new \RuntimeException('Berkas unggahan tidak valid atau gagal diproses.');
        }

        $size = (int) $file->getSize();
        $ext  = strtolower((string) $file->getClientExtension());
        $mime = strtolower((string) $file->getMimeType());

        if ($size > 2 * 1024 * 1024) {
            throw new \RuntimeException('Ukuran berkas melebihi batas maksimal 2 MB.');
        }
        if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            throw new \RuntimeException('Format berkas harus PDF, JPG, atau PNG.');
        }
        if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'], true)) {
            throw new \RuntimeException('Jenis konten berkas tidak diizinkan.');
        }

        $dir = FCPATH . 'assets/uploads/ketarunaan/lowongan';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move($dir, $newName);

        return $newName;
    }

    private function canViewStaffBoard(): bool
    {
        return KetarunaanPolicy::canManageLowongan()
            || (function_exists('hasAnyRole') && hasAnyRole(['wadir', 'manajemen', 'kabag_kuk', 'kaprodi', 'kanit', 'katim']));
    }

    private function resolveWaktuAcuan(): string
    {
        $raw = trim((string) ($this->request->getGet('tanggal_acuan') ?? ''));
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $raw);
            if ($dt !== false && $dt->format('Y-m-d') === $raw) {
                return $raw . ' 00:00:00';
            }
        }

        return date('Y-m-d H:i:s');
    }

    private function getActorName(): string
    {
        return trim((string) (session()->get('nama_lengkap') ?: session()->get('username') ?: 'Staf Karir'));
    }

    /**
     * @return array<string, bool>
     */
    private function getPermissions(): array
    {
        return [
            'can_manage'        => KetarunaanPolicy::canManageLowongan(),
            'can_validate'      => KetarunaanPolicy::canValidateLowongan(),
            'can_publish'       => KetarunaanPolicy::canPublishLowongan(),
            'can_view_internal' => KetarunaanPolicy::canViewInternalLowonganProof(),
            'can_share_mitra'   => KetarunaanPolicy::canShareToMitraPenggunaLulusan(),
            'can_view_staff'    => $this->canViewStaffBoard(),
        ];
    }
}
