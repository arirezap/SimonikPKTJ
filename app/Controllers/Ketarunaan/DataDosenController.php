<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\DataDosenService;
use App\Libraries\Ketarunaan\KetarunaanPolicy;

/**
 * Controller Modul Data Dosen — Executive Profiling Pimpinan (ECC 2.0).
 * Diblokir total bagi akun taruna dan alumni.
 */
class DataDosenController extends BaseController
{
    protected DataDosenService $service;

    public function __construct(?DataDosenService $service = null)
    {
        $this->service = $service ?? new DataDosenService();
        helper(['ketarunaan', 'audit', 'role']);
    }

    /**
     * Direktori & Filter Pencarian Kandidat Dosen (/ketarunaan/data-dosen).
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessDataDosenModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke modul Data Dosen.');
        }

        // Jika dosen biasa tanpa kewenangan direktori, arahkan ke profil dirinya sendiri
        if (!KetarunaanPolicy::canViewDataDosenDirectory()) {
            $ownId = KetarunaanPolicy::resolveOwnDosenProfilId();
            if ($ownId !== null) {
                return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . $ownId));
            }
            return $this->denyAccess('Anda tidak memiliki hak akses ke direktori Data Dosen.');
        }

        $filters = [
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'            => (int) ($this->request->getGet('prodi_id') ?? 0),
            'jabatan_fungsional'  => trim((string) ($this->request->getGet('jabatan_fungsional') ?? 'all')),
            'pendidikan_terakhir' => trim((string) ($this->request->getGet('pendidikan_terakhir') ?? 'all')),
            'status_serdos'       => trim((string) ($this->request->getGet('status_serdos') ?? 'all')),
            'status_ikatan'       => trim((string) ($this->request->getGet('status_ikatan') ?? 'all')),
            'kesiapan'            => trim((string) ($this->request->getGet('kesiapan') ?? 'all')),
        ];

        $canManage = KetarunaanPolicy::canManageDataDosen();
        $data      = $this->service->getDirectoryData($filters);
        $refs      = $this->service->getReferenceOptions($canManage);

        return view('ketarunaan/data_dosen/index', [
            'title'   => 'Data Dosen - Evidence Command Center (ECC)',
            'filters' => $filters,
            'kpi'     => $data['kpi'],
            'jafung'  => $data['jafung_dist'],
            'prodiD'  => $data['prodi_dist'],
            'rows'    => $data['rows'],
            'refs'    => $refs,
            'perms'   => $this->getPermissions(),
        ]);
    }

    /**
     * Sinkronisasi pegawai berjabatan dosen dari tabel users (POST /ketarunaan/data-dosen/sinkron).
     */
    public function sinkron()
    {
        if (!KetarunaanPolicy::canManageDataDosen()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menyinkronkan data dosen.');
        }

        $result = $this->service->syncDosenFromUsers($this->getActorName());
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit') && (int) ($result['added'] ?? 0) > 0) {
            log_audit('SYNC_DATA_DOSEN', 'dosen_profil', 'batch', null, ['added' => (int) $result['added']]);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen'))->with('success', $result['message']);
    }

    /**
     * Simpan (tambah/ubah) profil dosen (POST /ketarunaan/data-dosen/simpan).
     */
    public function simpan()
    {
        if (!KetarunaanPolicy::canAccessDataDosenModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses menyimpan data dosen.');
        }

        $dosenId       = (int) ($this->request->getPost('dosen_profil_id') ?? 0);
        $canManage     = KetarunaanPolicy::canManageDataDosen();
        $ownId         = KetarunaanPolicy::resolveOwnDosenProfilId();
        $isSelfUpdate  = (!$canManage && $ownId !== null && $dosenId === $ownId);

        if (!$canManage && !$isSelfUpdate) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengubah profil dosen ini.');
        }

        $input = [
            'user_id'                 => $this->request->getPost('user_id'),
            'nidn'                    => $this->request->getPost('nidn'),
            'nuptk'                   => $this->request->getPost('nuptk'),
            'homebase_prodi_id'       => $this->request->getPost('homebase_prodi_id'),
            'jabatan_fungsional'      => $this->request->getPost('jabatan_fungsional'),
            'tmt_jabatan_fungsional'  => $this->request->getPost('tmt_jabatan_fungsional'),
            'pendidikan_terakhir'     => $this->request->getPost('pendidikan_terakhir'),
            'bidang_keilmuan'         => $this->request->getPost('bidang_keilmuan'),
            'kepakaran_tags'          => $this->request->getPost('kepakaran_tags'),
            'status_serdos'           => $this->request->getPost('status_serdos'),
            'tahun_serdos'            => $this->request->getPost('tahun_serdos'),
            'nomor_sertifikat_serdos' => $this->request->getPost('nomor_sertifikat_serdos'),
            'status_ikatan'           => $this->request->getPost('status_ikatan'),
            'is_siap_ditugaskan'      => $this->request->getPost('is_siap_ditugaskan'),
            'sinta_id'                => $this->request->getPost('sinta_id'),
            'google_scholar_url'      => $this->request->getPost('google_scholar_url'),
            'scopus_id'               => $this->request->getPost('scopus_id'),
        ];

        $canEditNotes = KetarunaanPolicy::canEditDosenExecutiveNotes();
        if ($canEditNotes && $this->request->getPost('catatan_pimpinan') !== null) {
            $input['catatan_pimpinan'] = $this->request->getPost('catatan_pimpinan');
        }

        $result = $this->service->saveDosenProfil(
            $input,
            $this->getActorName(),
            $dosenId > 0 ? $dosenId : null,
            $isSelfUpdate,
            $canEditNotes
        );

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit($dosenId > 0 ? 'UPDATE' : 'CREATE', 'dosen_profil', (string) $result['id'], null, [
                'is_self_update' => $isSelfUpdate ? 1 : 0,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . (int) $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Kartu Profil Eksekutif 1 Dosen (/ketarunaan/data-dosen/detail/{id}).
     */
    public function detail(int $dosenProfilId)
    {
        if (!KetarunaanPolicy::canAccessDataDosenModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke profil dosen.');
        }

        $canViewDir = KetarunaanPolicy::canViewDataDosenDirectory();
        $ownId      = KetarunaanPolicy::resolveOwnDosenProfilId();
        $isOwn      = ($ownId !== null && $ownId === $dosenProfilId);

        if (!$canViewDir && !$isOwn) {
            return $this->denyAccess('Anda hanya dapat mengakses profil dosen milik Anda sendiri.');
        }

        $canViewExec = KetarunaanPolicy::canViewDosenExecutiveNotes();
        $detail      = $this->service->getDosenDetail($dosenProfilId, $canViewExec);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/data-dosen'))->with('error', 'Data dosen tidak ditemukan.');
        }

        return view('ketarunaan/data_dosen/detail', [
            'title'  => 'Profil Dosen: ' . $detail['dosen']['nama_lengkap'] . ' - Evidence Command Center (ECC)',
            'detail' => $detail,
            'refs'   => $this->service->getReferenceOptions(false),
            'isOwn'  => $isOwn,
            'perms'  => $this->getPermissions(),
        ]);
    }

    /**
     * Simpan Catatan Pimpinan & status kesiapan penugasan (POST /ketarunaan/data-dosen/detail/{id}/catatan-pimpinan).
     */
    public function saveCatatanPimpinan(int $dosenProfilId)
    {
        if (!KetarunaanPolicy::canEditDosenExecutiveNotes()) {
            return $this->denyAccess('Hanya pimpinan yang berwenang mengisi catatan pertimbangan pimpinan.');
        }

        $catatan = (string) ($this->request->getPost('catatan_pimpinan') ?? '');
        $isSiap  = !empty($this->request->getPost('is_siap_ditugaskan'));
        $result  = $this->service->saveCatatanPimpinan($dosenProfilId, $catatan, $isSiap, $this->getActorName());

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE_DOSEN_EXECUTIVE_NOTE', 'dosen_profil', (string) $dosenProfilId, null, [
                'is_siap_ditugaskan' => $isSiap ? 1 : 0,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . $dosenProfilId . '#tab-eksekutif'))
            ->with('success', $result['message']);
    }

    /**
     * Tambah/Ubah riwayat kualifikasi dosen (POST /ketarunaan/data-dosen/detail/{id}/kualifikasi/simpan).
     */
    public function saveKualifikasi(int $dosenProfilId)
    {
        $canManage    = KetarunaanPolicy::canManageDataDosen();
        $ownId        = KetarunaanPolicy::resolveOwnDosenProfilId();
        $isSelfUpdate = (!$canManage && $ownId !== null && $ownId === $dosenProfilId);

        if (!$canManage && !$isSelfUpdate) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengelola riwayat kualifikasi dosen ini.');
        }

        $kualId = (int) ($this->request->getPost('kualifikasi_id') ?? 0);
        $input  = [
            'kategori'                => $this->request->getPost('kategori'),
            'judul'                   => $this->request->getPost('judul'),
            'institusi_penyelenggara' => $this->request->getPost('institusi_penyelenggara'),
            'tahun_mulai'             => $this->request->getPost('tahun_mulai'),
            'tahun_selesai'           => $this->request->getPost('tahun_selesai'),
            'nomor_dokumen'           => $this->request->getPost('nomor_dokumen'),
            'tautan_bukti'            => $this->request->getPost('tautan_bukti'),
            'keterangan'              => $this->request->getPost('keterangan'),
        ];

        $result = $this->service->saveKualifikasi($dosenProfilId, $input, $this->getActorName(), $kualId > 0 ? $kualId : null, $isSelfUpdate);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit($kualId > 0 ? 'UPDATE' : 'CREATE', 'dosen_riwayat_kualifikasi', (string) $result['id'], null, [
                'dosen_profil_id' => $dosenProfilId,
                'kategori'        => $input['kategori'],
            ]);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . $dosenProfilId))
            ->with('success', $result['message']);
    }

    /**
     * Hapus 1 riwayat kualifikasi dosen.
     */
    public function deleteKualifikasi(int $dosenProfilId, int $kualifikasiId)
    {
        if (!KetarunaanPolicy::canManageDataDosen()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menghapus riwayat kualifikasi.');
        }

        $result = $this->service->deleteKualifikasi($dosenProfilId, $kualifikasiId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'dosen_riwayat_kualifikasi', (string) $kualifikasiId, null, [
                'dosen_profil_id' => $dosenProfilId,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . $dosenProfilId))
            ->with('success', $result['message']);
    }

    /**
     * Verifikasi usulan kualifikasi yang diajukan dosen.
     */
    public function verifyKualifikasi(int $dosenProfilId, int $kualifikasiId)
    {
        if (!KetarunaanPolicy::canManageDataDosen()) {
            return $this->denyAccess('Anda tidak memiliki wewenang memverifikasi kualifikasi dosen.');
        }

        $result = $this->service->verifyKualifikasi($dosenProfilId, $kualifikasiId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/data-dosen/detail/' . $dosenProfilId))
            ->with('success', $result['message']);
    }

    /**
     * Ekspor Direktori Data Dosen ke CSV (/ketarunaan/data-dosen/ekspor).
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canViewDataDosenDirectory()) {
            return $this->denyAccess('Anda tidak memiliki hak akses mengekspor Data Dosen.');
        }

        $filters = [
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'            => (int) ($this->request->getGet('prodi_id') ?? 0),
            'jabatan_fungsional'  => trim((string) ($this->request->getGet('jabatan_fungsional') ?? 'all')),
            'pendidikan_terakhir' => trim((string) ($this->request->getGet('pendidikan_terakhir') ?? 'all')),
            'status_serdos'       => trim((string) ($this->request->getGet('status_serdos') ?? 'all')),
            'status_ikatan'       => trim((string) ($this->request->getGet('status_ikatan') ?? 'all')),
            'kesiapan'            => trim((string) ($this->request->getGet('kesiapan') ?? 'all')),
        ];

        $csvRows = $this->service->getExportRows($filters);

        if (function_exists('log_audit')) {
            log_audit('EXPORT', 'dosen_profil', 'directory', null, ['rows' => max(0, count($csvRows) - 1)]);
        }

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($csvRows as $line) {
            fputcsv($fh, $line);
        }
        rewind($fh);
        $csvContent = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="data_dosen_pktj_' . date('Ymd_His') . '.csv"')
            ->setBody((string) $csvContent);
    }

    private function denyAccess(string $message)
    {
        return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403_portal', [
            'message'   => $message,
            'portalUrl' => site_url(ltrim(KetarunaanPolicy::getSelfPortalUrl(), '/')),
        ]));
    }

    private function getActorName(): string
    {
        return trim((string) (session()->get('nama') ?: session()->get('nama_lengkap') ?: session()->get('username') ?: 'Pengelola ECC'));
    }

    /**
     * @return array<string, bool>
     */
    private function getPermissions(): array
    {
        return [
            'can_view_directory' => KetarunaanPolicy::canViewDataDosenDirectory(),
            'can_manage'         => KetarunaanPolicy::canManageDataDosen(),
            'can_view_exec'      => KetarunaanPolicy::canViewDosenExecutiveNotes(),
            'can_edit_exec'      => KetarunaanPolicy::canEditDosenExecutiveNotes(),
        ];
    }
}
