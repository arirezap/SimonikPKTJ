<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\RekamJejakService;

/**
 * Controller Modul 2: Rekam Jejak Taruna (ECC 2.0).
 * Mengatur alur request -> validasi -> service -> response sesuai prinsip SRP.
 */
class RekamJejakController extends BaseController
{
    protected RekamJejakService $rekamJejakService;

    public function __construct(?RekamJejakService $rekamJejakService = null)
    {
        $this->rekamJejakService = $rekamJejakService ?? new RekamJejakService();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Halaman Direktori Rekam Jejak Taruna.
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly()) {
            $ownNomor = KetarunaanPolicy::resolveSelfNomorTaruna();
            if ($ownNomor !== null && KetarunaanPolicy::isSelfTarunaOnly()) {
                return redirect()->to(base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($ownNomor)));
            }
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Direktori rekam jejak hanya tersedia untuk staf dan pimpinan.');
        }

        $allowedSorts = ['nomor_taruna', 'nama_lengkap', 'prodi_angkatan', 'status_terkini', 'jumlah_capaian', 'jumlah_prestasi', 'pembaruan'];
        $sortByRaw    = trim((string) ($this->request->getGet('sort_by') ?? 'jumlah_capaian'));
        $sortBy       = in_array($sortByRaw, $allowedSorts, true) ? $sortByRaw : 'jumlah_capaian';
        $sortOrderRaw = strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc')));
        $sortOrder    = ($sortOrderRaw === 'asc') ? 'asc' : 'desc';

        $filters = [
            'q'          => trim((string) $this->request->getGet('q')),
            'prodi'      => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'angkatan'   => trim((string) ($this->request->getGet('angkatan') ?? 'all')),
            'status'     => trim((string) ($this->request->getGet('status') ?? 'all')),
            'periode'    => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'sort_by'    => $sortBy,
            'sort_order' => $sortOrder,
        ];
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $refs = $this->rekamJejakService->getReferenceOptions();
        $dir  = $this->rekamJejakService->getDirectoryData($filters, $page, 15);

        return view('ketarunaan/rekam_jejak/index', [
            'title'           => 'Rekam Jejak Taruna - Evidence Command Center (ECC)',
            'active_menu'     => 'rekam_jejak',
            'filters'         => $filters,
            'refs'            => $refs,
            'metrics'         => $dir['metrics'],
            'rows'            => $dir['rows'],
            'pagination'      => $dir['pagination'],
            'canManageTaruna' => KetarunaanPolicy::canManageTaruna(),
        ]);
    }

    /**
     * Halaman Detail Rekam Jejak Taruna (4 Tab: Ringkasan, Kronologi, Daftar Capaian, Dokumen Bukti).
     */
    public function detail(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly() && !KetarunaanPolicy::isOwnNomorTaruna($nomorTaruna)) {
            return $this->response->setStatusCode(403, 'Akses ke rekam jejak taruna lain ditolak.')
                ->setBody('Anda hanya dapat mengakses rekam jejak milik Anda sendiri.');
        }

        $tab = strtolower(trim((string) ($this->request->getGet('tab') ?? 'ringkasan')));
        if (!in_array($tab, ['ringkasan', 'kronologi', 'capaian', 'dokumen'], true)) {
            $tab = 'ringkasan';
        }

        $filters = [
            'kategori_kronologi' => trim((string) ($this->request->getGet('kategori_kronologi') ?? 'all')),
            'periode_kronologi'  => trim((string) ($this->request->getGet('periode_kronologi') ?? 'all')),
            'validasi_kronologi' => trim((string) ($this->request->getGet('validasi_kronologi') ?? 'all')),
            'kategori_capaian'   => trim((string) ($this->request->getGet('kategori_capaian') ?? 'all')),
            'periode_capaian'    => trim((string) ($this->request->getGet('periode_capaian') ?? 'all')),
            'validasi_capaian'   => trim((string) ($this->request->getGet('validasi_capaian') ?? 'all')),
            'modul_dokumen'      => trim((string) ($this->request->getGet('modul_dokumen') ?? $this->request->getGet('modul') ?? 'all')),
            'jenis_dokumen'      => trim((string) ($this->request->getGet('jenis_dokumen') ?? 'all')),
            'q_dokumen'          => trim((string) ($this->request->getGet('q_dokumen') ?? '')),
        ];

        $canViewDiscipline = KetarunaanPolicy::canViewDisciplinaryRecord($nomorTaruna);
        $canViewBeasiswa   = KetarunaanPolicy::canViewRestrictedDocument('beasiswa', $nomorTaruna);

        $detail = $this->rekamJejakService->getDetailRekamJejak(
            $nomorTaruna,
            $filters,
            $canViewDiscipline,
            $canViewBeasiswa
        );

        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data taruna tidak ditemukan.');
        }

        $refs = $this->rekamJejakService->getReferenceOptions();

        return view('ketarunaan/rekam_jejak/detail', [
            'title'             => 'Rekam Jejak ' . $detail['taruna']['nama_lengkap'] . ' - Evidence Command Center (ECC)',
            'active_menu'       => 'rekam_jejak',
            'active_tab'        => $tab,
            'filters'           => $filters,
            'refs'              => $refs,
            'detail'            => $detail,
            'canManageTaruna'   => KetarunaanPolicy::canManageTaruna(),
            'canSubmitCapaian'  => KetarunaanPolicy::canSubmitCapaianMandiri($nomorTaruna),
            'canValidate'       => KetarunaanPolicy::canValidate(),
            'canViewDiscipline' => $canViewDiscipline,
            'isSelfOnly'        => KetarunaanPolicy::isSelfAccountOnly(),
            'ownNomorTaruna'    => KetarunaanPolicy::resolveSelfNomorTaruna(),
        ]);
    }

    /**
     * Halaman Formulir Tambah Capaian Taruna.
     */
    public function createCapaian(?string $nomorTaruna = null)
    {
        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();

        if (!KetarunaanPolicy::canSubmitCapaianMandiri($nomorTaruna)) {
            if ($isSelfTaruna) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Anda hanya dapat mengajukan capaian untuk Nomor Taruna Anda sendiri.');
            }
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data capaian.');
        }

        if ($isSelfTaruna) {
            $nomorTaruna = KetarunaanPolicy::resolveSelfNomorTaruna();
            if ($nomorTaruna === null) {
                return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                    ->with('error', 'Nomor Taruna akun Anda tidak ditemukan.');
            }
        }

        $refs       = $this->rekamJejakService->getReferenceOptions();
        $tarunaList = $isSelfTaruna ? [] : $this->rekamJejakService->getTarunaDropdownList();

        $selectedTaruna = null;
        if ($nomorTaruna !== null && $nomorTaruna !== '') {
            $selectedTaruna = $this->rekamJejakService->getTarunaHeader($nomorTaruna);
        }

        return view('ketarunaan/rekam_jejak/form_capaian', [
            'title'          => 'Tambah Capaian Taruna - Evidence Command Center (ECC)',
            'active_menu'    => 'rekam_jejak',
            'mode'           => 'create',
            'capaian'        => null,
            'selectedTaruna' => $selectedTaruna,
            'tarunaList'     => $tarunaList,
            'refs'           => $refs,
            'isSelfTaruna'   => $isSelfTaruna,
        ]);
    }

    /**
     * Proses penyimpanan data capaian baru.
     */
    public function storeCapaian()
    {
        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();
        $postedNomor  = trim((string) $this->request->getPost('nomor_taruna'));

        if ($isSelfTaruna) {
            $ownNomor = KetarunaanPolicy::resolveSelfNomorTaruna();
            if ($ownNomor === null || ($postedNomor !== '' && strcasecmp($postedNomor, $ownNomor) !== 0)) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Anda hanya dapat mengajukan capaian untuk Nomor Taruna Anda sendiri.');
            }
            $nomorTaruna = $ownNomor;
        } else {
            $nomorTaruna = $postedNomor;
        }

        if (!KetarunaanPolicy::canSubmitCapaianMandiri($nomorTaruna)) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data capaian.');
        }

        $rules = $this->getCapaianValidationRules(true);

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Isian wajib belum lengkap atau tidak sesuai ketentuan.');
        }

        if (!$this->isValidOptionalNumeric($this->request->getPost('nilai_angka'))) {
            return redirect()->back()->withInput()
                ->with('error', 'Nilai angka harus berupa angka antara 0 sampai 999.999,99.');
        }

        $aksiSubmit     = trim((string) ($this->request->getPost('aksi_submit') ?? 'diajukan'));
        $statusValidasi = ($aksiSubmit === 'draf') ? 'draf' : 'diajukan';

        $buktiLink = trim((string) ($this->request->getPost('bukti_berkas') ?? ''));
        if ($buktiLink !== '' && !ketarunaan_is_http_url($buktiLink, 255)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tautan bukti pendukung harus berupa URL http:// atau https:// yang valid (maksimal 255 karakter).');
        }

        if ($isSelfTaruna && $statusValidasi === 'diajukan' && $buktiLink === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Masukkan tautan bukti pendukung (mis. Google Drive) sebelum mengajukan validasi.');
        }

        $unitPemilik = $isSelfTaruna
            ? 'Ketarunaan'
            : trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));

        $payload = $this->buildCapaianPayload(
            $nomorTaruna,
            $unitPemilik,
            $statusValidasi,
            $buktiLink !== '' ? $buktiLink : null,
            0
        );

        $result = $this->rekamJejakService->saveCapaian($payload);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $savedId = (int) ($result['id'] ?? 0);
        if (function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_capaian', (string) $savedId, null, [
                'nomor_taruna'    => $nomorTaruna,
                'nama_capaian'    => $payload['nama_capaian'],
                'status_validasi' => $statusValidasi,
                'diajukan_oleh'   => $payload['diajukan_oleh'],
            ]);
        }

        return redirect()->to(base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna) . '?tab=capaian'))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Detail Capaian Taruna.
     */
    public function showCapaian(int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $capaian = $this->rekamJejakService->getCapaianDetail($id);
        if (!$capaian) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly() && !KetarunaanPolicy::isOwnNomorTaruna((string) ($capaian['nomor_taruna'] ?? ''))) {
            return $this->response->setStatusCode(403, 'Akses ke rincian capaian taruna lain ditolak.')
                ->setBody('Anda hanya dapat mengakses capaian milik Anda sendiri.');
        }

        return view('ketarunaan/rekam_jejak/detail_capaian', [
            'title'           => 'Detail Capaian - Evidence Command Center (ECC)',
            'active_menu'     => 'rekam_jejak',
            'capaian'         => $capaian,
            'canManageTaruna' => KetarunaanPolicy::canManageTaruna(),
            'canEditCapaian'  => KetarunaanPolicy::canEditCapaian($capaian),
            'canValidate'     => KetarunaanPolicy::canValidate(),
            'isSelfOnly'      => KetarunaanPolicy::isSelfAccountOnly(),
        ]);
    }

    /**
     * Halaman Formulir Koreksi / Ubah Capaian Taruna.
     */
    public function editCapaian(int $id)
    {
        $capaian = $this->rekamJejakService->getCapaianDetail($id);
        if (!$capaian) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditCapaian($capaian)) {
            if (KetarunaanPolicy::isSelfTarunaOnly() && !KetarunaanPolicy::isOwnNomorTaruna((string) ($capaian['nomor_taruna'] ?? ''))) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Anda tidak dapat mengubah capaian milik taruna lain.');
            }
            return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
                ->with('error', 'Capaian tidak dapat diubah pada status atau peran ini.');
        }

        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();
        $refs         = $this->rekamJejakService->getReferenceOptions();
        $tarunaList   = $isSelfTaruna ? [] : $this->rekamJejakService->getTarunaDropdownList();

        return view('ketarunaan/rekam_jejak/form_capaian', [
            'title'          => $isSelfTaruna ? 'Perbaiki Capaian Saya - Evidence Command Center (ECC)' : 'Koreksi Capaian Taruna - Evidence Command Center (ECC)',
            'active_menu'    => 'rekam_jejak',
            'mode'           => 'edit',
            'capaian'        => $capaian,
            'selectedTaruna' => [
                'nomor_taruna'    => $capaian['nomor_taruna'],
                'nama_lengkap'    => $capaian['nama_lengkap'],
                'foto'            => $capaian['foto'] ?? null,
                'kode_prodi'      => $capaian['kode_prodi'],
                'angkatan'        => $capaian['angkatan'],
                'angkatan_romawi' => $capaian['angkatan_romawi'] ?? '',
                'status_terkini'  => $capaian['status_terkini'],
            ],
            'tarunaList'   => $tarunaList,
            'refs'         => $refs,
            'isSelfTaruna' => $isSelfTaruna,
        ]);
    }

    /**
     * Proses penyimpanan koreksi / pembaruan data capaian.
     */
    public function updateCapaian(int $id)
    {
        $existing = $this->rekamJejakService->getCapaianDetail($id);
        if (!$existing) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditCapaian($existing)) {
            if (KetarunaanPolicy::isSelfTarunaOnly() && !KetarunaanPolicy::isOwnNomorTaruna((string) ($existing['nomor_taruna'] ?? ''))) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Anda tidak dapat mengubah capaian milik taruna lain.');
            }
            return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
                ->with('error', 'Capaian tidak dapat diubah pada status atau peran ini.');
        }

        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();
        $rules        = $this->getCapaianValidationRules(false);

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Isian wajib belum lengkap atau tidak sesuai ketentuan.');
        }

        if (!$this->isValidOptionalNumeric($this->request->getPost('nilai_angka'))) {
            return redirect()->back()->withInput()
                ->with('error', 'Nilai angka harus berupa angka antara 0 sampai 999.999,99.');
        }

        $aksiSubmit     = trim((string) ($this->request->getPost('aksi_submit') ?? 'diajukan'));
        $statusValidasi = ($aksiSubmit === 'draf') ? 'draf' : 'diajukan';

        $buktiLink     = trim((string) ($this->request->getPost('bukti_berkas') ?? ''));
        $existingBukti = trim((string) ($existing['bukti_berkas'] ?? ''));
        $hadLegacyFile = ($existingBukti !== '' && !ketarunaan_is_http_url($existingBukti, 2000));

        if ($buktiLink !== '' && !ketarunaan_is_http_url($buktiLink, 255)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tautan bukti pendukung harus berupa URL http:// atau https:// yang valid (maksimal 255 karakter).');
        }

        if ($buktiLink !== '') {
            $finalBukti   = $buktiLink;
            $finalSizeKb  = 0;
        } elseif ($hadLegacyFile) {
            $finalBukti   = $existingBukti;
            $finalSizeKb  = (int) ($existing['ukuran_berkas_kb'] ?? 0);
        } else {
            $finalBukti   = null;
            $finalSizeKb  = 0;
        }

        if ($isSelfTaruna && $statusValidasi === 'diajukan' && ($finalBukti === null || $finalBukti === '')) {
            return redirect()->back()->withInput()
                ->with('error', 'Masukkan tautan bukti pendukung (mis. Google Drive) sebelum mengajukan validasi.');
        }

        $unitPemilik = $isSelfTaruna
            ? 'Ketarunaan'
            : trim((string) ($this->request->getPost('unit_pemilik') ?? $existing['unit_pemilik']));

        $payload = $this->buildCapaianPayload(
            (string) $existing['nomor_taruna'],
            $unitPemilik,
            $statusValidasi,
            $finalBukti,
            $finalSizeKb
        );

        $result = $this->rekamJejakService->saveCapaian($payload, $id);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if ($hadLegacyFile && $buktiLink !== '') {
            $this->unlinkCapaianFile($existingBukti);
        }

        if (function_exists('log_audit')) {
            log_audit(
                'UPDATE',
                'taruna_capaian',
                (string) $id,
                ['status_validasi' => $existing['status_validasi'] ?? 'draf'],
                ['status_validasi' => $statusValidasi, 'diajukan_oleh' => $payload['diajukan_oleh']]
            );
        }

        return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menghapus draf capaian atau capaian yang dikembalikan untuk perbaikan.
     */
    public function deleteCapaian(int $id)
    {
        $existing = $this->rekamJejakService->getCapaianDetail($id);
        if (!$existing) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditCapaian($existing)) {
            if (KetarunaanPolicy::isSelfTarunaOnly() && !KetarunaanPolicy::isOwnNomorTaruna((string) ($existing['nomor_taruna'] ?? ''))) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Anda tidak dapat menghapus capaian milik taruna lain.');
            }
            return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang menghapus capaian ini.');
        }

        $status = (string) ($existing['status_validasi'] ?? '');
        if (!in_array($status, ['draf', 'perlu_perbaikan', 'dikembalikan'], true)) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
                ->with('error', 'Hanya draf atau capaian yang dikembalikan yang dapat dihapus.');
        }

        $result = $this->rekamJejakService->deleteCapaian($id);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (!empty($existing['bukti_berkas'])) {
            $this->unlinkCapaianFile((string) $existing['bukti_berkas']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'taruna_capaian', (string) $id, [
                'nomor_taruna'    => $existing['nomor_taruna'] ?? null,
                'nama_capaian'    => $existing['nama_capaian'] ?? null,
                'status_validasi' => $status,
            ], null);
        }

        $nomorTaruna = (string) ($existing['nomor_taruna'] ?? '');
        return redirect()->to(base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna) . '?tab=capaian'))
            ->with('success', $result['message']);
    }

    /**
     * Proses validasi / pengembalian data capaian oleh validator.
     */
    public function validateCapaian(int $id)
    {
        if (!KetarunaanPolicy::canValidate()) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki wewenang memvalidasi data capaian.');
        }

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? ''));
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if (!in_array($keputusan, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return redirect()->back()
                ->with('error', 'Keputusan validasi tidak valid.');
        }

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()
                ->with('error', 'Isi catatan perbaikan terlebih dahulu.');
        }

        $result = $this->rekamJejakService->processValidation(
            $id,
            $keputusan,
            $catatan,
            $this->resolveValidatorLabel()
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $auditAction = ($keputusan === 'tervalidasi') ? 'APPROVE' : 'RETURN_CAPAIAN';
            log_audit($auditAction, 'taruna_capaian', (string) $id, null, [
                'keputusan' => $keputusan,
                'catatan'   => $catatan,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * @return array<string, string>
     */
    private function getCapaianValidationRules(bool $withNomorTaruna): array
    {
        $rules = [
            'kategori'          => 'required|in_list[kompetensi_sertifikasi,organisasi,pengembangan_diri,akademik]',
            'nama_capaian'      => 'required|min_length[3]|max_length[180]',
            'tanggal_kegiatan'  => 'required|valid_date[Y-m-d]',
            'tahun_akademik_id' => 'permit_empty|is_natural_no_zero',
            'semester'          => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[8]',
            'penyelenggara'     => 'required|min_length[2]|max_length[160]',
            'tingkat_kegiatan'  => 'permit_empty|in_list[Kampus / Internal,Daerah / Provinsi,Nasional,Internasional]',
            'uraian_capaian'    => 'permit_empty|max_length[2000]',
            'hasil_capaian'     => 'required|max_length[60]',
            'satuan_nilai'      => 'permit_empty|max_length[30]',
            'nomor_dokumen'     => 'permit_empty|max_length[100]',
            'unit_pemilik'      => 'permit_empty|in_list[Ketarunaan,Pusbangkar,Program Studi]',
            'sumber_dokumen'    => 'permit_empty|in_list[Sertifikat,Surat Tugas,Surat Keputusan (SK),Piagam]',
        ];

        if ($withNomorTaruna) {
            $rules['nomor_taruna'] = 'required|max_length[30]';
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCapaianPayload(
        string $nomorTaruna,
        string $unitPemilik,
        string $statusValidasi,
        ?string $uploadedFileName,
        ?int $uploadedSizeKb
    ): array {
        return [
            'nomor_taruna'      => $nomorTaruna,
            'kategori'          => $this->request->getPost('kategori'),
            'nama_capaian'      => $this->request->getPost('nama_capaian'),
            'tanggal_kegiatan'  => $this->request->getPost('tanggal_kegiatan'),
            'tahun_akademik_id' => $this->request->getPost('tahun_akademik_id'),
            'semester'          => $this->request->getPost('semester'),
            'penyelenggara'     => $this->request->getPost('penyelenggara'),
            'tingkat_kegiatan'  => $this->request->getPost('tingkat_kegiatan'),
            'uraian_capaian'    => $this->request->getPost('uraian_capaian'),
            'hasil_capaian'     => $this->request->getPost('hasil_capaian'),
            'nilai_angka'       => $this->request->getPost('nilai_angka'),
            'satuan_nilai'      => $this->request->getPost('satuan_nilai'),
            'nomor_dokumen'     => $this->request->getPost('nomor_dokumen'),
            'unit_pemilik'      => $unitPemilik,
            'sumber_dokumen'    => $this->request->getPost('sumber_dokumen'),
            'bukti_berkas'      => $uploadedFileName,
            'ukuran_berkas_kb'  => $uploadedSizeKb,
            'status_validasi'   => $statusValidasi,
            'diajukan_oleh'     => $this->resolveActorLabel($unitPemilik),
        ];
    }

    private function isValidOptionalNumeric($val): bool
    {
        $raw = trim((string) $val);
        if ($raw === '') {
            return true;
        }
        $normalized = str_replace(',', '.', $raw);
        if (!is_numeric($normalized)) {
            return false;
        }
        $num = (float) $normalized;

        return $num >= 0 && $num <= 999999.99;
    }

    private function resolveActorLabel(string $unitPemilik): string
    {
        if (KetarunaanPolicy::isSelfTarunaOnly()) {
            $ownNomor = KetarunaanPolicy::resolveSelfNomorTaruna() ?? '-';
            $nama     = trim((string) (session()->get('nama_lengkap') ?: session()->get('nama') ?: $ownNomor));

            return mb_substr('Taruna - ' . $nama . ' (' . $ownNomor . ')', 0, 100);
        }

        return match ($unitPemilik) {
            'Pusbangkar'    => 'PIC Pusbangkar',
            'Program Studi' => 'PIC Prodi',
            default         => 'PIC Ketarunaan',
        };
    }

    private function resolveValidatorLabel(): string
    {
        $nama = trim((string) (session()->get('nama_lengkap') ?: session()->get('nama') ?: session()->get('username') ?: 'Validator Unit'));

        return mb_substr($nama, 0, 100);
    }

    private function unlinkCapaianFile(?string $fileName): void
    {
        $raw = trim((string) $fileName);
        if ($raw === '' || ketarunaan_is_http_url($raw, 2000)) {
            return;
        }

        $clean = basename($raw);
        if ($clean === '') {
            return;
        }

        $path = FCPATH . 'assets/uploads/ketarunaan/capaian/' . $clean;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Unduh / Pratinjau berkas bukti rekam jejak dengan pemeriksaan hak akses.
     */
    public function unduhBukti(string $modul, int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $modulClean = strtolower(trim($modul));
        $db         = \Config\Database::connect();
        $inline     = ((int) $this->request->getGet('inline') === 1);

        if ($modulClean === 'capaian') {
            $row = $this->rekamJejakService->getCapaianDetail($id);
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
            }
            $nomorTaruna = (string) ($row['nomor_taruna'] ?? '');
            if (KetarunaanPolicy::isSelfAccountOnly() && !KetarunaanPolicy::isOwnNomorTaruna($nomorTaruna)) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Dokumen bukti milik taruna lain tidak dapat diakses.');
            }
            $rawBukti = trim((string) ($row['bukti_berkas'] ?? ''));
            if ($rawBukti === '') {
                return redirect()->back()->with('error', 'Belum ada bukti pendukung yang tersedia.');
            }
            if (ketarunaan_is_http_url($rawBukti, 2000)) {
                return redirect()->to($rawBukti);
            }
            $fileName     = basename($rawBukti);
            $physicalPath = FCPATH . 'assets/uploads/ketarunaan/capaian/' . $fileName;
            if (is_file($physicalPath)) {
                $mime = mime_content_type($physicalPath) ?: 'application/pdf';
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPath));
            }
            return redirect()->back()->with('error', 'Berkas bukti tidak ditemukan.');
        } elseif ($modulClean === 'prestasi') {
            $row = $db->table('taruna_prestasi')->where('id', $id)->get()->getRowArray();
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen prestasi tidak ditemukan.');
            }
            if (KetarunaanPolicy::isSelfAccountOnly()) {
                $pesertaRows = $db->table('taruna_peserta_prestasi')
                    ->select('nomor_taruna')
                    ->where('prestasi_id', $id)
                    ->get()
                    ->getResultArray();
                $isOwnParticipant = false;
                foreach ($pesertaRows as $pr) {
                    if (KetarunaanPolicy::isOwnNomorTaruna((string) ($pr['nomor_taruna'] ?? ''))) {
                        $isOwnParticipant = true;
                        break;
                    }
                }
                if (!$isOwnParticipant) {
                    return $this->response->setStatusCode(403, 'Akses ditolak.')
                        ->setBody('Dokumen prestasi milik taruna lain tidak dapat diakses.');
                }
            }
            $rawBukti = trim((string) ($row['bukti_berkas'] ?? ''));
            if ($rawBukti === '') {
                return redirect()->back()->with('error', 'Belum ada berkas bukti prestasi yang tersedia.');
            }
            if (ketarunaan_is_http_url($rawBukti, 2000)) {
                return redirect()->to($rawBukti);
            }
            $fileName         = basename($rawBukti);
            $physicalPrestasi = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $fileName;
            if (is_file($physicalPrestasi)) {
                $mime = mime_content_type($physicalPrestasi) ?: 'application/pdf';
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPrestasi));
            }
            return redirect()->back()->with('error', 'Berkas bukti prestasi tidak ditemukan.');
        } elseif ($modulClean === 'beasiswa') {
            $row = $db->table('taruna_penerima_beasiswa tpb')
                ->select('tpb.*, prog.nama_program')
                ->join('taruna_program_beasiswa prog', 'prog.id = tpb.program_beasiswa_id', 'left')
                ->where('tpb.id', $id)
                ->get()
                ->getRowArray();
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen beasiswa tidak ditemukan.');
            }
            $nomorTaruna = (string) ($row['nomor_taruna'] ?? '');
            if (!KetarunaanPolicy::canViewRestrictedDocument('beasiswa', $nomorTaruna)) {
                return $this->response->setStatusCode(403)
                    ->setBody('Dokumen ini bersifat terbatas untuk peran yang berwenang.');
            }
            $rawDok = trim((string) ($row['dokumen_keputusan'] ?? ''));
            if ($rawDok === '') {
                return redirect()->back()->with('error', 'Belum ada dokumen keputusan beasiswa yang tersedia.');
            }
            if (ketarunaan_is_http_url($rawDok, 2000)) {
                return redirect()->to($rawDok);
            }
            $fileName         = basename($rawDok);
            $physicalBeasiswa = FCPATH . 'assets/uploads/ketarunaan/beasiswa/' . $fileName;
            if (is_file($physicalBeasiswa)) {
                $mime = mime_content_type($physicalBeasiswa) ?: 'application/pdf';
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalBeasiswa));
            }
            return redirect()->back()->with('error', 'Berkas keputusan beasiswa tidak ditemukan.');
        } elseif ($modulClean === 'pelanggaran') {
            $row = $db->table('taruna_pelanggaran')->where('id', $id)->get()->getRowArray();
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen kasus tidak ditemukan.');
            }
            $nomorTaruna = (string) ($row['nomor_taruna'] ?? '');
            if (!KetarunaanPolicy::canViewRestrictedDocument('pelanggaran', $nomorTaruna)) {
                return $this->response->setStatusCode(403, 'Akses ditolak.')
                    ->setBody('Dokumen ini bersifat terbatas untuk petugas berwenang.');
            }
            $rawBukti = trim((string) ($row['bukti_berkas'] ?? ''));
            if ($rawBukti === '') {
                return redirect()->back()->with('error', 'Belum ada berkas dokumen kasus yang tersedia.');
            }
            if (ketarunaan_is_http_url($rawBukti, 2000)) {
                return redirect()->to($rawBukti);
            }
            $fileName            = basename($rawBukti);
            $physicalPelanggaran = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . $fileName;
            if (is_file($physicalPelanggaran)) {
                $mime = mime_content_type($physicalPelanggaran) ?: 'application/pdf';
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPelanggaran));
            }
            return redirect()->back()->with('error', 'Berkas dokumen kasus tidak ditemukan.');
        }

        return redirect()->back()->with('error', 'Modul dokumen tidak dikenali.');
    }

    /**
     * Ekspor daftar direktori rekam jejak ke format CSV.
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessModule() || KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Ekspor direktori rekam jejak tidak diizinkan untuk akun layanan mandiri.');
        }

        $allowedSorts = ['nomor_taruna', 'nama_lengkap', 'prodi_angkatan', 'status_terkini', 'jumlah_capaian', 'jumlah_prestasi', 'pembaruan'];
        $sortByRaw    = trim((string) ($this->request->getGet('sort_by') ?? 'jumlah_capaian'));
        $sortBy       = in_array($sortByRaw, $allowedSorts, true) ? $sortByRaw : 'jumlah_capaian';
        $sortOrderRaw = strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc')));
        $sortOrder    = ($sortOrderRaw === 'asc') ? 'asc' : 'desc';

        $filters = [
            'q'          => trim((string) $this->request->getGet('q')),
            'prodi'      => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'angkatan'   => trim((string) ($this->request->getGet('angkatan') ?? 'all')),
            'status'     => trim((string) ($this->request->getGet('status') ?? 'all')),
            'periode'    => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'sort_by'    => $sortBy,
            'sort_order' => $sortOrder,
        ];

        $dir = $this->rekamJejakService->getDirectoryData($filters, 1, 0);

        $lines = [];
        $lines[] = implode(',', [
            'Nomor Taruna',
            'Nama Taruna',
            'Program Studi',
            'Angkatan',
            'Status Pendidikan',
            'Capaian Tervalidasi',
            'Prestasi Tervalidasi',
            'Pembaruan Terakhir',
        ]);

        foreach ($dir['rows'] as $r) {
            $statusLabel = ketarunaan_status_meta($r['status_terkini'])['label'];
            $angkatanFormatted = !empty($r['angkatan'])
                ? ketarunaan_format_angkatan([
                    'tahun_masuk' => $r['angkatan'],
                    'kode_romawi' => $r['angkatan_romawi'] ?? '',
                ])
                : '-';
            $lines[] = implode(',', [
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['nomor_taruna'])) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['nama_lengkap'])) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) ($r['kode_prodi'] ?? '-'))) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe($angkatanFormatted)) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe($statusLabel)) . '"',
                (int) $r['jumlah_capaian'],
                (int) $r['jumlah_prestasi'],
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['tanggal_pembaruan'])) . '"',
            ]);
        }

        if (function_exists('log_audit')) {
            log_audit('EXPORT_CSV_REKAM_JEJAK', 'taruna_capaian', 'all', null, [
                'filters'    => $filters,
                'total_rows' => count($dir['rows']),
            ]);
        }

        $csvContent = "\xEF\xBB\xBF" . implode("\r\n", $lines);
        $fileName   = 'Rekam_Jejak_Taruna_ECC_' . date('Ymd') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($csvContent);
    }
}

