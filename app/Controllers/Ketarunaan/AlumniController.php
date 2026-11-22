<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\AlumniService;
use App\Libraries\Ketarunaan\KetarunaanPolicy;

/**
 * Controller Modul 7: Alumni — Profil dasar (ECC 2.0).
 * Mengatur direktori profil dasar alumni tervalidasi, pembaruan kontak dan pekerjaan terlacak,
 * validasi pembentukan profil dari kelulusan sah, serta koreksi kelulusan terkendali.
 */
class AlumniController extends BaseController
{
    protected AlumniService $alumniService;

    public function __construct(?AlumniService $alumniService = null)
    {
        $this->alumniService = $alumniService ?? new AlumniService();
        helper(['ketarunaan', 'form', 'audit']);
    }

    /**
     * Halaman Direktori Profil Dasar Alumni (/ketarunaan/alumni).
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessAlumniModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda belum memiliki akses ke Modul Alumni.');
        }

        $isSelfOnly = KetarunaanPolicy::isSelfAlumniOnly();
        $selfNomor  = $isSelfOnly ? KetarunaanPolicy::resolveSelfAlumniNomorTaruna() : null;
        $refs       = $this->alumniService->getReferenceOptions();

        $filters = [
            'q'                => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'         => trim((string) ($this->request->getGet('prodi_id') ?? 'all')),
            'angkatan_id'      => trim((string) ($this->request->getGet('angkatan_id') ?? 'all')),
            'tahun_lulus'      => trim((string) ($this->request->getGet('tahun_lulus') ?? 'all')),
            'status_profil'    => trim((string) ($this->request->getGet('status_profil') ?? 'aktif')),
            'pekerjaan_status' => trim((string) ($this->request->getGet('pekerjaan_status') ?? 'all')),
            'sort'             => trim((string) ($this->request->getGet('sort') ?? '')),
            'dir'              => strtolower(trim((string) ($this->request->getGet('dir') ?? 'desc'))) === 'asc' ? 'asc' : 'desc',
            'page'             => max(1, (int) ($this->request->getGet('page') ?? 1)),
        ];

        $directory = $this->alumniService->getDirectoryData($filters, $isSelfOnly, $selfNomor);

        return view('ketarunaan/alumni/index', [
            'title'      => 'Alumni - Evidence Command Center (ECC)',
            'activeTab'  => 'direktori',
            'refs'       => $refs,
            'filters'    => $filters,
            'kpi'        => $directory['kpi'],
            'rows'       => $directory['rows'],
            'pagination' => $directory['pagination'],
            'isSelfOnly' => $isSelfOnly,
            'selfNomor'  => $selfNomor,
            'perms'      => $this->getPermissions(),
        ]);
    }

    /**
     * Halaman Detail Profil Dasar Alumni 1-to-1 Nomor Taruna (/ketarunaan/alumni/detail/{nomorTaruna}).
     */
    public function detail(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canAccessAlumniModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda belum memiliki akses ke Modul Alumni.');
        }

        $isSelfOnly = KetarunaanPolicy::isSelfAlumniOnly();
        if ($isSelfOnly) {
            $ownNomor = KetarunaanPolicy::resolveSelfAlumniNomorTaruna();
            if ($ownNomor === null || strcasecmp($ownNomor, trim($nomorTaruna)) !== 0) {
                return redirect()->to(base_url('ketarunaan/alumni'))
                    ->with('error', 'Anda hanya dapat mengakses profil alumni milik Anda sendiri.');
            }
        }

        $canViewSensitive = !$isSelfOnly && KetarunaanPolicy::canViewSensitiveData($nomorTaruna);
        $detail           = $this->alumniService->getAlumniDetail($nomorTaruna, $canViewSensitive);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/alumni'))
                ->with('error', 'Profil dasar alumni tidak ditemukan.');
        }

        $refs = $this->alumniService->getReferenceOptions();

        return view('ketarunaan/alumni/detail', [
            'title'            => 'Profil Alumni ' . $detail['alumni']['nama_lengkap'] . ' - Evidence Command Center (ECC)',
            'activeTab'        => 'direktori',
            'detail'           => $detail,
            'refs'             => $refs,
            'canViewSensitive' => $canViewSensitive,
            'canUpdateContact' => KetarunaanPolicy::canUpdateOwnAlumniContact($nomorTaruna),
            'isSelfOnly'       => $isSelfOnly,
            'perms'            => $this->getPermissions(),
        ]);
    }

    /**
     * Menyimpan pembaruan kontak dan pekerjaan alumni terlacak (POST /ketarunaan/alumni/kontak/{nomorTaruna}).
     */
    public function updateKontak(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canUpdateOwnAlumniContact($nomorTaruna)) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang memperbarui kontak alumni ini.');
        }

        $payload = (array) $this->request->getPost();
        if (KetarunaanPolicy::isSelfAlumniOnly()) {
            $payload['sumber_konfirmasi'] = 'mandiri_alumni';
        }

        $res = $this->alumniService->updateKontak($nomorTaruna, $payload, $this->getActorName(), $this->getActorRole());

        return redirect()->to(base_url('ketarunaan/alumni/detail/' . rawurlencode($nomorTaruna)))
            ->withInput()
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Halaman Antrean Validasi Kelulusan Pembentuk Profil Alumni (/ketarunaan/alumni/kelulusan).
     */
    public function kelulusan()
    {
        if (!$this->canViewStaffWorkspace()) {
            return redirect()->to(base_url('ketarunaan/alumni'))
                ->with('error', 'Halaman validasi kelulusan khusus pengelola ketarunaan dan validator.');
        }

        $queue = $this->alumniService->getKelulusanQueue();

        return view('ketarunaan/alumni/kelulusan', [
            'title'     => 'Validasi Kelulusan Alumni - Evidence Command Center (ECC)',
            'activeTab' => 'kelulusan',
            'queue'     => $queue,
            'perms'     => $this->getPermissions(),
        ]);
    }

    /**
     * Mengajukan usulan kelulusan baru ke antrean validasi (POST /ketarunaan/alumni/kelulusan/ajukan).
     */
    public function ajukanKelulusan()
    {
        if (!KetarunaanPolicy::canManageAlumni()) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang mengajukan kelulusan taruna.');
        }

        $res = $this->alumniService->ajukanKelulusan((array) $this->request->getPost(), $this->getActorName());

        return redirect()->to(base_url('ketarunaan/alumni/kelulusan'))
            ->withInput()
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Mengajukan kelulusan massal (1 SK untuk banyak taruna) (POST /ketarunaan/alumni/kelulusan/ajukan-batch).
     */
    public function ajukanKelulusanBatch()
    {
        if (!KetarunaanPolicy::canManageAlumni()) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang mengajukan kelulusan taruna.');
        }

        $nomorTarunas = (array) ($this->request->getPost('nomor_taruna_list') ?? []);
        $res          = $this->alumniService->ajukanKelulusanBatch($nomorTarunas, (array) $this->request->getPost(), $this->getActorName());

        return redirect()->to(base_url('ketarunaan/alumni/kelulusan'))
            ->withInput()
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Memutuskan validasi kelulusan (setujui atau kembalikan) (POST /ketarunaan/alumni/kelulusan/validasi/{id}).
     */
    public function validasiKelulusan(int $riwayatId)
    {
        if (!KetarunaanPolicy::canValidateAlumni()) {
            return $this->response->setStatusCode(403, 'Hanya pejabat validator berwenang yang dapat mengesahkan kelulusan.');
        }

        $decision = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'perlu_perbaikan';
        $catatan  = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        $res = $this->alumniService->validasiKelulusan($riwayatId, $decision, $catatan, $this->getActorName());

        return redirect()->to(base_url('ketarunaan/alumni/kelulusan'))
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Memutuskan validasi kelulusan secara massal (POST /ketarunaan/alumni/kelulusan/validasi-batch).
     */
    public function validasiKelulusanBatch()
    {
        if (!KetarunaanPolicy::canValidateAlumni()) {
            return $this->response->setStatusCode(403, 'Hanya pejabat validator berwenang yang dapat mengesahkan kelulusan.');
        }

        $riwayatIds = (array) ($this->request->getPost('riwayat_ids') ?? []);
        $decision   = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'perlu_perbaikan';
        $catatan    = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        $res = $this->alumniService->validasiKelulusanBatch($riwayatIds, $decision, $catatan, $this->getActorName());

        return redirect()->to(base_url('ketarunaan/alumni/kelulusan'))
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Halaman Koreksi Kelulusan Terkendali (/ketarunaan/alumni/koreksi).
     */
    public function koreksi()
    {
        if (!$this->canViewStaffWorkspace()) {
            return redirect()->to(base_url('ketarunaan/alumni'))
                ->with('error', 'Halaman koreksi kelulusan khusus pengelola dan validator.');
        }

        $queue         = $this->alumniService->getKoreksiQueue();
        $refs          = $this->alumniService->getReferenceOptions();
        $selectedNomor = trim((string) ($this->request->getGet('nomor_taruna') ?? ''));

        return view('ketarunaan/alumni/koreksi', [
            'title'         => 'Koreksi Kelulusan Alumni - Evidence Command Center (ECC)',
            'activeTab'     => 'koreksi',
            'queue'         => $queue,
            'refs'          => $refs,
            'selectedNomor' => $selectedNomor,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Mengajukan koreksi kelulusan (POST /ketarunaan/alumni/koreksi/ajukan).
     */
    public function ajukanKoreksi()
    {
        if (!KetarunaanPolicy::canManageAlumni()) {
            return $this->response->setStatusCode(403, 'Anda tidak berwenang mengajukan koreksi kelulusan.');
        }

        $res = $this->alumniService->ajukanKoreksi((array) $this->request->getPost(), $this->getActorName(), $this->getActorRole());

        return redirect()->to(base_url('ketarunaan/alumni/koreksi'))
            ->withInput()
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Memutuskan usulan koreksi kelulusan (POST /ketarunaan/alumni/koreksi/putuskan/{id}).
     */
    public function putuskanKoreksi(int $koreksiId)
    {
        if (!KetarunaanPolicy::canValidateAlumni()) {
            return $this->response->setStatusCode(403, 'Hanya pejabat validator yang dapat memutuskan koreksi kelulusan.');
        }

        $decision = $this->request->getPost('keputusan') === 'tervalidasi' ? 'tervalidasi' : 'ditolak';
        $catatan  = trim((string) ($this->request->getPost('catatan_validator') ?? ''));

        $res = $this->alumniService->putuskanKoreksi($koreksiId, $decision, $catatan, $this->getActorName());

        return redirect()->to(base_url('ketarunaan/alumni/koreksi'))
            ->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Ekspor CSV Direktori Profil Dasar Alumni (/ketarunaan/alumni/ekspor).
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessAlumniModule() || KetarunaanPolicy::isSelfAlumniOnly()) {
            return $this->response->setStatusCode(403, 'Ekspor direktori alumni tidak diizinkan untuk akun ini.');
        }

        $filters = [
            'q'                => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'         => trim((string) ($this->request->getGet('prodi_id') ?? 'all')),
            'angkatan_id'      => trim((string) ($this->request->getGet('angkatan_id') ?? 'all')),
            'tahun_lulus'      => trim((string) ($this->request->getGet('tahun_lulus') ?? 'all')),
            'status_profil'    => trim((string) ($this->request->getGet('status_profil') ?? 'aktif')),
            'pekerjaan_status' => trim((string) ($this->request->getGet('pekerjaan_status') ?? 'all')),
        ];

        $csvRows = $this->alumniService->getExportRows($filters);
        log_audit('EXPORT', 'alumni_profil', 'list', null, ['rows' => max(0, count($csvRows) - 1)]);

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
            ->setHeader('Content-Disposition', 'attachment; filename="direktori_alumni_ecc_' . date('Ymd') . '.csv"')
            ->setBody((string) $content);
    }

    private function canViewStaffWorkspace(): bool
    {
        return KetarunaanPolicy::canManageAlumni()
            || KetarunaanPolicy::canValidateAlumni()
            || (function_exists('hasAnyRole') && hasAnyRole(['wadir', 'manajemen', 'kepegawaian']));
    }

    private function getActorName(): string
    {
        return trim((string) (session()->get('nama') ?: session()->get('nama_lengkap') ?: session()->get('username') ?: 'Pengelola ECC'));
    }

    private function getActorRole(): string
    {
        return trim((string) (session()->get('role') ?: 'pengelola'));
    }

    /**
     * @return array<string, bool>
     */
    private function getPermissions(): array
    {
        return [
            'can_manage'       => KetarunaanPolicy::canManageAlumni(),
            'can_validate'     => KetarunaanPolicy::canValidateAlumni(),
            'can_view_staff'   => $this->canViewStaffWorkspace(),
            'can_tracer_study' => KetarunaanPolicy::canManageTracerStudy(),
            'is_self_only'     => KetarunaanPolicy::isSelfAlumniOnly(),
        ];
    }
}
