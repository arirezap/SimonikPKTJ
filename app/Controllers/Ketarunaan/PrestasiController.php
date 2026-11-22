<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\PrestasiService;

/**
 * Controller Modul 3: Prestasi Taruna (ECC 2.0).
 * Mengatur alur request -> validasi -> service -> response sesuai prinsip SRP.
 */
class PrestasiController extends BaseController
{
    protected PrestasiService $prestasiService;

    public function __construct(?PrestasiService $prestasiService = null)
    {
        $this->prestasiService = $prestasiService ?? new PrestasiService();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Halaman Direktori Prestasi Taruna.
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $allowedSorts = ['nama_prestasi', 'tingkat_prestasi', 'peringkat', 'jenis_keikutsertaan', 'jumlah_peserta', 'tanggal_mulai', 'status_validasi'];
        $sortByRaw    = trim((string) ($this->request->getGet('sort_by') ?? ''));
        $sortBy       = in_array($sortByRaw, $allowedSorts, true) ? $sortByRaw : '';
        $sortOrderRaw = strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc')));
        $sortOrder    = ($sortOrderRaw === 'asc') ? 'asc' : 'desc';

        $keikutsertaanRaw = strtolower(trim((string) ($this->request->getGet('keikutsertaan') ?? 'all')));
        $keikutsertaan    = in_array($keikutsertaanRaw, ['tim', 'individu'], true) ? $keikutsertaanRaw : 'all';

        $isSelfOnly     = KetarunaanPolicy::isSelfAccountOnly();
        $isSelfTaruna   = KetarunaanPolicy::isSelfTarunaOnly();
        $ownNomorTaruna = KetarunaanPolicy::resolveSelfNomorTaruna();
        $milikSaya      = ($isSelfOnly && trim((string) ($this->request->getGet('milik_saya') ?? '')) === '1') ? '1' : '';

        $filters = [
            'q'             => trim((string) $this->request->getGet('q')),
            'jenis'         => trim((string) ($this->request->getGet('jenis') ?? 'all')),
            'tingkat'       => trim((string) ($this->request->getGet('tingkat') ?? 'all')),
            'prodi'         => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'periode'       => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'status'        => trim((string) ($this->request->getGet('status') ?? 'all')),
            'keikutsertaan' => $keikutsertaan,
            'milik_saya'    => $milikSaya,
            'sort_by'       => $sortBy,
            'sort_order'    => $sortOrder,
        ];
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);
        if (!in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $refs = $this->prestasiService->getReferenceOptions();
        $dir  = $this->prestasiService->getDirectoryData(
            $filters,
            $page,
            $perPage,
            $isSelfOnly,
            $ownNomorTaruna
        );

        return view('ketarunaan/prestasi/index', [
            'title'             => 'Prestasi Taruna - Evidence Command Center (ECC)',
            'filters'           => $filters,
            'refs'              => $refs,
            'metrics'           => $dir['metrics'],
            'rows'              => $dir['rows'],
            'pagination'        => $dir['pagination'],
            'canManageTaruna'   => KetarunaanPolicy::canManageTaruna(),
            'canSubmitPrestasi' => KetarunaanPolicy::canSubmitPrestasiMandiri(),
            'canValidate'       => KetarunaanPolicy::canValidate(),
            'isSelfOnly'        => $isSelfOnly,
            'isSelfTaruna'      => $isSelfTaruna,
        ]);
    }

    /**
     * Halaman Tahap 1: Formulir Tambah Informasi Prestasi.
     */
    public function create()
    {
        if (!KetarunaanPolicy::canSubmitPrestasiMandiri()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data prestasi.');
        }

        return view('ketarunaan/prestasi/form_informasi', [
            'title'        => 'Tambah Prestasi - Evidence Command Center (ECC)',
            'mode'         => 'create',
            'prestasi'     => null,
            'refs'         => $this->prestasiService->getReferenceOptions(),
            'isSelfTaruna' => KetarunaanPolicy::isSelfTarunaOnly(),
        ]);
    }

    /**
     * Proses simpan Tahap 1 untuk prestasi baru.
     */
    public function store()
    {
        if (!KetarunaanPolicy::canSubmitPrestasiMandiri()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data prestasi.');
        }

        if (!$this->validate($this->getInformasiValidationRules())) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi seluruh kolom wajib pada informasi prestasi.');
        }

        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();
        $ownNomor     = null;
        if ($isSelfTaruna) {
            $ownNomor = KetarunaanPolicy::resolveSelfNomorTaruna();
            if ($ownNomor === null) {
                return redirect()->to(base_url('ketarunaan/prestasi'))
                    ->with('error', 'Data taruna akun Anda tidak ditemukan.');
            }
        }

        $unitPemilik = $isSelfTaruna
            ? 'Ketarunaan'
            : trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));

        $payload = $this->buildInformasiPayload($unitPemilik);
        if ($isSelfTaruna && $ownNomor !== null) {
            $payload['initial_peserta_nomor'] = $ownNomor;
        }

        $result = $this->prestasiService->saveInformasi($payload);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $newId = (int) ($result['id'] ?? 0);
        if (function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_prestasi', (string) $newId, null, [
                'nama_prestasi' => $payload['nama_prestasi'],
                'diajukan_oleh' => $payload['diajukan_oleh'],
            ]);
        }

        if ($isSelfTaruna && ($payload['jenis_keikutsertaan'] ?? 'individu') === 'individu') {
            return redirect()->to(base_url('ketarunaan/prestasi/dokumen/' . $newId))
                ->with('success', 'Informasi prestasi disimpan dan akun Anda otomatis terdaftar. Lengkapi tautan bukti utama lalu ajukan validasi.');
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $newId))
            ->with('success', $isSelfTaruna
                ? 'Informasi prestasi disimpan dan akun Anda otomatis terdaftar sebagai Ketua Tim. Tambahkan anggota tim bila diperlukan.'
                : 'Informasi prestasi disimpan. Silakan tambahkan peserta.');
    }

    /**
     * Halaman Tahap 1 (Mode Ubah / Koreksi): Informasi Prestasi.
     */
    public function edit(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Data prestasi tidak dapat diubah pada status atau peran ini.');
        }

        return view('ketarunaan/prestasi/form_informasi', [
            'title'        => 'Ubah Informasi Prestasi - Evidence Command Center (ECC)',
            'mode'         => 'edit',
            'prestasi'     => $detail['prestasi'],
            'refs'         => $this->prestasiService->getReferenceOptions(),
            'isSelfTaruna' => KetarunaanPolicy::isSelfTarunaOnly(),
        ]);
    }

    /**
     * Proses simpan perubahan Tahap 1 Informasi Prestasi.
     */
    public function update(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Data prestasi tidak dapat diubah pada status atau peran ini.');
        }

        if (!$this->validate($this->getInformasiValidationRules())) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi seluruh kolom wajib pada informasi prestasi.');
        }

        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();
        $unitPemilik  = $isSelfTaruna
            ? 'Ketarunaan'
            : trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));

        $payload = $this->buildInformasiPayload($unitPemilik);
        $result  = $this->prestasiService->saveInformasi($payload, $id);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_prestasi', (string) $id, null, [
                'nama_prestasi' => $payload['nama_prestasi'],
                'diajukan_oleh' => $payload['diajukan_oleh'],
            ]);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'next'));
        if ($aksiSubmit === 'draf') {
            return redirect()->to(base_url('ketarunaan/prestasi/edit/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 2: Kelola Peserta Prestasi.
     */
    public function peserta(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Peserta prestasi tidak dapat dikelola pada status atau peran ini.');
        }

        $isSelfTaruna = KetarunaanPolicy::isSelfTarunaOnly();

        return view('ketarunaan/prestasi/kelola_peserta', [
            'title'          => 'Kelola Peserta Prestasi - Evidence Command Center (ECC)',
            'detail'         => $detail,
            'refs'           => $this->prestasiService->getReferenceOptions(),
            'tarunaList'     => $this->prestasiService->getTarunaSelectList($isSelfTaruna),
            'isSelfTaruna'   => $isSelfTaruna,
            'ownNomorTaruna' => $isSelfTaruna ? KetarunaanPolicy::resolveSelfNomorTaruna() : null,
        ]);
    }

    /**
     * Tambah peserta taruna ke suatu prestasi.
     */
    public function addPeserta(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola peserta prestasi ini.');
        }

        if (
            KetarunaanPolicy::isSelfTarunaOnly()
            && ($detail['prestasi']['jenis_keikutsertaan'] ?? 'individu') === 'individu'
        ) {
            return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
                ->with('error', 'Prestasi individu otomatis memuat akun Anda saja.');
        }

        $nomorTaruna = trim((string) $this->request->getPost('nomor_taruna'));
        $peran       = trim((string) ($this->request->getPost('peran') ?? 'Anggota'));

        if ($nomorTaruna === '') {
            return redirect()->back()->with('error', 'Pilih taruna terlebih dahulu sebelum menambahkan peserta.');
        }

        $pelaku = $this->resolveActorLabel((string) ($detail['prestasi']['unit_pemilik'] ?? 'Ketarunaan'));
        $result = $this->prestasiService->addPeserta($id, $nomorTaruna, $peran, $pelaku);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_prestasi', (string) $id, null, [
                'aksi'         => 'tambah_peserta',
                'nomor_taruna' => $nomorTaruna,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Hapus peserta taruna dari suatu prestasi.
     */
    public function removePeserta(int $id, int $pesertaId)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang menghapus peserta prestasi ini.');
        }

        if (KetarunaanPolicy::isSelfTarunaOnly()) {
            foreach (($detail['peserta'] ?? []) as $ps) {
                if ((int) ($ps['peserta_id'] ?? 0) === $pesertaId
                    && KetarunaanPolicy::isOwnNomorTaruna((string) ($ps['nomor_taruna'] ?? ''))
                ) {
                    return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
                        ->with('error', 'Anda tidak dapat menghapus akun sendiri dari pengajuan prestasi Anda.');
                }
            }
        }

        $pelaku = $this->resolveActorLabel((string) ($detail['prestasi']['unit_pemilik'] ?? 'Ketarunaan'));
        $result = $this->prestasiService->removePeserta($id, $pesertaId, $pelaku);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_prestasi', (string) $id, null, [
                'aksi'       => 'hapus_peserta',
                'peserta_id' => $pesertaId,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 3: Dokumen Bukti & Pemeriksaan Pengajuan.
     */
    public function dokumen(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Dokumen bukti tidak dapat dikelola pada status atau peran ini.');
        }

        return view('ketarunaan/prestasi/dokumen_bukti', [
            'title'  => 'Dokumen Bukti Prestasi - Evidence Command Center (ECC)',
            'detail' => $detail,
        ]);
    }

    /**
     * Simpan dokumen bukti utama/dokumentasi dan opsional ajukan validasi.
     */
    public function saveDokumen(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Dokumen bukti tidak dapat diubah pada status atau peran ini.');
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'draf'));
        if (!in_array($aksiSubmit, ['draf', 'diajukan'], true)) {
            $aksiSubmit = 'draf';
        }

        $rules = [
            'nomor_sertifikat' => ($aksiSubmit === 'diajukan')
                ? 'required|min_length[2]|max_length[100]'
                : 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Nomor sertifikat wajib diisi (2–100 karakter) saat mengajukan validasi.');
        }

        $existingBukti = trim((string) ($detail['prestasi']['bukti_berkas'] ?? ''));
        $hadLegacyFile = ($existingBukti !== '' && !ketarunaan_is_http_url($existingBukti, 2000));
        $buktiLink     = trim((string) ($this->request->getPost('bukti_berkas') ?? ''));

        if ($buktiLink !== '' && !ketarunaan_is_http_url($buktiLink, 255)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tautan bukti utama harus berupa URL http:// atau https:// yang valid (maksimal 255 karakter).');
        }

        if ($buktiLink !== '') {
            $finalBukti  = $buktiLink;
            $finalSizeKb = 0;
        } elseif ($hadLegacyFile) {
            $finalBukti  = $existingBukti;
            $finalSizeKb = (int) ($detail['prestasi']['ukuran_berkas_kb'] ?? 0);
        } else {
            $finalBukti  = '';
            $finalSizeKb = 0;
        }

        // Validasi tautan dokumentasi tambahan (Opsi A: Google Drive / Cloud URL, maks. total 5 entri)
        $rawLinks    = $this->request->getPost('dokumentasi_links');
        $newDokLinks = [];
        $sisaSlot    = max(0, 5 - count($detail['dokumentasi']));
        if (is_array($rawLinks)) {
            foreach ($rawLinks as $rawLink) {
                $cleanLink = trim((string) $rawLink);
                if ($cleanLink === '') {
                    continue;
                }
                if ($sisaSlot <= 0) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Jumlah tautan dokumentasi tambahan melebihi batas maksimal 5 entri.');
                }
                if (!ketarunaan_is_http_url($cleanLink, 500)) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Setiap tautan dokumentasi harus berupa URL http:// atau https:// yang valid (maksimal 500 karakter).');
                }
                if (!in_array($cleanLink, $detail['dokumentasi'], true) && !in_array($cleanLink, $newDokLinks, true)) {
                    $newDokLinks[] = $cleanLink;
                    $sisaSlot--;
                }
            }
        }

        $result = $this->prestasiService->saveDokumenAndSubmit($id, [
            'nomor_sertifikat' => $this->request->getPost('nomor_sertifikat'),
            'bukti_berkas'     => $finalBukti,
            'ukuran_berkas_kb' => $finalSizeKb,
            'dokumentasi_baru' => $newDokLinks,
            'aksi_submit'      => $aksiSubmit,
            'pelaku'           => $this->resolveActorLabel((string) ($detail['prestasi']['unit_pemilik'] ?? 'Ketarunaan')),
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if ($hadLegacyFile && $buktiLink !== '') {
            $oldName = basename($existingBukti);
            if ($oldName !== '') {
                $oldFile = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $oldName;
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_prestasi', (string) $id, null, [
                'aksi'        => $aksiSubmit === 'diajukan' ? 'ajukan_validasi' : 'simpan_draf_dokumen',
                'status_baru' => $aksiSubmit,
            ]);
        }

        if ($aksiSubmit === 'diajukan') {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/dokumen/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menghapus satu file dokumentasi prestasi.
     */
    public function removeDokumentasi(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang menghapus dokumentasi.');
        }

        $file   = trim((string) $this->request->getPost('filename'));
        $pelaku = $this->resolveActorLabel((string) ($detail['prestasi']['unit_pemilik'] ?? 'Ketarunaan'));
        $result = $this->prestasiService->removeDokumentasi($id, $file, $pelaku);

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('DELETE', 'taruna_prestasi', (string) $id, null, [
                'aksi'        => 'hapus_dokumentasi',
                'dokumentasi' => $file,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/dokumen/' . $id))
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Halaman Detail Prestasi 4 Tab (Ringkasan, Peserta, Dokumen Bukti, Riwayat Validasi).
     */
    public function detail(int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (
            KetarunaanPolicy::isSelfAccountOnly()
            && ($detail['prestasi']['status_validasi'] ?? '') !== 'tervalidasi'
            && !$this->isPesertaPrestasi($detail)
        ) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Data prestasi yang belum tervalidasi hanya dapat diakses oleh petugas atau peserta terkait.');
        }

        $tab = strtolower(trim((string) ($this->request->getGet('tab') ?? 'ringkasan')));
        if (!in_array($tab, ['ringkasan', 'peserta', 'dokumen', 'riwayat'], true)) {
            $tab = 'ringkasan';
        }

        return view('ketarunaan/prestasi/detail', [
            'title'            => 'Detail Prestasi - Evidence Command Center (ECC)',
            'active_tab'       => $tab,
            'detail'           => $detail,
            'canManageTaruna'  => KetarunaanPolicy::canManageTaruna(),
            'canEditPrestasi'  => KetarunaanPolicy::canEditPrestasi($detail),
            'canValidate'      => KetarunaanPolicy::canValidate(),
            'isSelfOnly'       => KetarunaanPolicy::isSelfAccountOnly(),
            'isPesertaSendiri' => $this->isPesertaPrestasi($detail),
            'ownNomorTaruna'   => KetarunaanPolicy::resolveSelfNomorTaruna(),
        ]);
    }

    /**
     * Proses validasi / pengembalian prestasi oleh validator.
     */
    public function validasi(int $id)
    {
        if (!KetarunaanPolicy::canValidate()) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki wewenang memvalidasi prestasi.');
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

        $result = $this->prestasiService->processValidation($id, $keputusan, $catatan, $this->resolveValidatorLabel());
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $auditAction = ($keputusan === 'tervalidasi') ? 'APPROVE' : 'RETURN_PRESTASI';
            log_audit($auditAction, 'taruna_prestasi', (string) $id, null, [
                'keputusan' => $keputusan,
                'catatan'   => $catatan,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id . '?tab=riwayat'))
            ->with('success', $result['message']);
    }

    /**
     * Hapus draf prestasi (hanya untuk draf/perlu perbaikan).
     */
    public function delete(int $id)
    {
        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (!KetarunaanPolicy::canEditPrestasi($detail)) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Data prestasi tidak dapat dihapus pada status atau peran ini.');
        }

        $result = $this->prestasiService->deleteDraftPrestasi($id);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $this->cleanupPrestasiFiles($detail);

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'taruna_prestasi', (string) $id, null, [
                'nama_prestasi' => $detail['prestasi']['nama_prestasi'] ?? '-',
            ]);
        }

        return redirect()->to(base_url('ketarunaan/prestasi'))
            ->with('success', $result['message']);
    }

    /**
     * Unduh / Pratinjau dokumen bukti utama atau dokumentasi prestasi.
     */
    public function unduhDokumen(int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly() && !$this->isPesertaPrestasi($detail)) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Berkas bukti prestasi hanya dapat diakses oleh staf berwenang atau peserta terkait.');
        }

        $prestasi = $detail['prestasi'];
        $jenis    = trim((string) ($this->request->getGet('jenis') ?? 'utama'));
        $inline   = ((int) $this->request->getGet('inline') === 1);

        if ($jenis === 'foto') {
            $rawFile = trim((string) ($this->request->getGet('file') ?? ''));
            if ($rawFile !== '' && in_array($rawFile, $detail['dokumentasi'], true)) {
                if (ketarunaan_is_http_url($rawFile, 2000)) {
                    return redirect()->to($rawFile);
                }
                $fotoName     = basename($rawFile);
                $physicalFoto = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . $fotoName;
                if (is_file($physicalFoto)) {
                    $mime = mime_content_type($physicalFoto) ?: 'image/jpeg';
                    return $this->response
                        ->setHeader('X-Content-Type-Options', 'nosniff')
                        ->setHeader('Content-Type', $mime)
                        ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fotoName . '"')
                        ->setBody((string) file_get_contents($physicalFoto));
                }
            }
            return redirect()->back()->with('error', 'Berkas dokumentasi tidak ditemukan.');
        }

        $rawBukti = trim((string) ($prestasi['bukti_berkas'] ?? ''));
        if ($rawBukti === '') {
            return redirect()->back()->with('error', 'Belum ada bukti utama prestasi yang tersedia.');
        }
        if (ketarunaan_is_http_url($rawBukti, 2000)) {
            return redirect()->to($rawBukti);
        }

        $fileName     = basename($rawBukti);
        $physicalPath = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $fileName;
        if (is_file($physicalPath)) {
            $mime = mime_content_type($physicalPath) ?: 'application/pdf';
            return $this->response
                ->setHeader('X-Content-Type-Options', 'nosniff')
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                ->setBody((string) file_get_contents($physicalPath));
        }

        return redirect()->back()->with('error', 'Berkas bukti prestasi tidak ditemukan.');
    }

    /**
     * Unduh Ringkasan Prestasi dalam format PDF 1 halaman.
     */
    public function unduhRingkasan(int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data prestasi tidak ditemukan.');
        }

        if (
            KetarunaanPolicy::isSelfAccountOnly()
            && ($detail['prestasi']['status_validasi'] ?? '') !== 'tervalidasi'
            && !$this->isPesertaPrestasi($detail)
        ) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Ringkasan prestasi ini hanya tersedia untuk staf atau peserta terkait.');
        }

        $p           = $detail['prestasi'];
        $statusLabel = ketarunaan_validasi_meta((string) $p['status_validasi'])['label'];
        $tglMulai    = ketarunaan_format_tanggal((string) ($p['tanggal_mulai'] ?? ''), true);
        $tglSelesai  = ketarunaan_format_tanggal((string) ($p['tanggal_selesai'] ?? ''), true);
        $rentangTgl  = ($tglSelesai !== '-' && $tglSelesai !== $tglMulai) ? ($tglMulai . ' - ' . $tglSelesai) : $tglMulai;

        $pdf = ketarunaan_generate_simple_pdf('Ringkasan Data Prestasi Taruna', (string) $p['nama_prestasi'], [
            'Jenis / Bidang'      => ucfirst((string) $p['jenis_prestasi']) . ' / ' . ($p['bidang_prestasi'] ?: '-'),
            'Tingkat / Peringkat' => ucfirst((string) $p['tingkat_prestasi']) . ' / ' . $p['peringkat'],
            'Penyelenggara'       => (string) $p['penyelenggara'],
            'Lokasi & Tanggal'    => ($p['lokasi_kegiatan'] ?: '-') . ' (' . $rentangTgl . ')',
            'Keikutsertaan'       => ucfirst((string) $p['jenis_keikutsertaan']) . ($p['nama_tim'] ? ' - ' . $p['nama_tim'] : ''),
            'Jumlah Peserta'      => count($detail['peserta']) . ' taruna',
            'Status Validasi'     => $statusLabel . ($p['divalidasi_oleh'] ? ' oleh ' . $p['divalidasi_oleh'] : ''),
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="Ringkasan_Prestasi_' . $id . '.pdf"')
            ->setBody($pdf);
    }

    /**
     * Ekspor daftar prestasi terfilter ke format CSV.
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessModule() || KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Ekspor daftar prestasi tidak diizinkan untuk akun layanan mandiri.');
        }

        $allowedSorts = ['nama_prestasi', 'tingkat_prestasi', 'peringkat', 'jenis_keikutsertaan', 'jumlah_peserta', 'tanggal_mulai', 'status_validasi'];
        $sortByRaw    = trim((string) ($this->request->getGet('sort_by') ?? ''));
        $sortBy       = in_array($sortByRaw, $allowedSorts, true) ? $sortByRaw : '';
        $sortOrderRaw = strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc')));
        $sortOrder    = ($sortOrderRaw === 'asc') ? 'asc' : 'desc';

        $keikutsertaanRaw = strtolower(trim((string) ($this->request->getGet('keikutsertaan') ?? 'all')));
        $keikutsertaan    = in_array($keikutsertaanRaw, ['tim', 'individu'], true) ? $keikutsertaanRaw : 'all';

        $filters = [
            'q'             => trim((string) $this->request->getGet('q')),
            'jenis'         => trim((string) ($this->request->getGet('jenis') ?? 'all')),
            'tingkat'       => trim((string) ($this->request->getGet('tingkat') ?? 'all')),
            'prodi'         => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'periode'       => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'status'        => trim((string) ($this->request->getGet('status') ?? 'all')),
            'keikutsertaan' => $keikutsertaan,
            'sort_by'       => $sortBy,
            'sort_order'    => $sortOrder,
        ];

        $dir = $this->prestasiService->getDirectoryData($filters, 1, 0);

        $lines   = [];
        $lines[] = implode(',', [
            'Nama Prestasi',
            'Jenis',
            'Tingkat',
            'Peringkat',
            'Keikutsertaan',
            'Jumlah Peserta',
            'Tanggal Mulai',
            'Status Validasi',
        ]);

        foreach ($dir['rows'] as $r) {
            $statusLabel = ketarunaan_validasi_meta((string) $r['status_validasi'])['label'];
            $lines[] = implode(',', [
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['nama_prestasi'])) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe(ucfirst((string) $r['jenis_prestasi']))) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe(ucfirst((string) $r['tingkat_prestasi']))) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['peringkat'])) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe(ucfirst((string) $r['jenis_keikutsertaan']))) . '"',
                (int) $r['jumlah_peserta'],
                '"' . str_replace('"', '""', ketarunaan_csv_safe((string) $r['tanggal_mulai'])) . '"',
                '"' . str_replace('"', '""', ketarunaan_csv_safe($statusLabel)) . '"',
            ]);
        }

        if (function_exists('log_audit')) {
            log_audit('EXPORT_CSV_PRESTASI', 'taruna_prestasi', 'all', null, [
                'filters'    => $filters,
                'total_rows' => count($dir['rows']),
            ]);
        }

        $csvContent = "\xEF\xBB\xBF" . implode("\r\n", $lines);
        $fileName   = 'Prestasi_Taruna_ECC_' . date('Ymd') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($csvContent);
    }

    /**
     * Memeriksa apakah akun aktif merupakan salah satu peserta pada prestasi ini.
     *
     * @param array<string, mixed> $detail
     */
    private function isPesertaPrestasi(array $detail): bool
    {
        foreach (($detail['peserta'] ?? []) as $peserta) {
            if (KetarunaanPolicy::isOwnNomorTaruna((string) ($peserta['nomor_taruna'] ?? ''))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Aturan validasi bersama untuk Tahap 1 Informasi Prestasi.
     *
     * @return array<string, string>
     */
    private function getInformasiValidationRules(): array
    {
        return [
            'nama_prestasi'       => 'required|min_length[3]|max_length[200]',
            'jenis_prestasi'      => 'required|in_list[akademik,non_akademik,olahraga,seni]',
            'bidang_prestasi'     => 'permit_empty|max_length[100]',
            'tingkat_prestasi'    => 'required|in_list[internal,daerah,nasional,internasional]',
            'peringkat'           => 'required|max_length[50]',
            'penyelenggara'       => 'required|max_length[150]',
            'lokasi_kegiatan'     => 'required|max_length[150]',
            'tanggal_mulai'       => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'     => 'required|valid_date[Y-m-d]',
            'jenis_keikutsertaan' => 'required|in_list[individu,tim]',
            'nama_tim'            => 'permit_empty|max_length[120]',
            'pembimbing'          => 'permit_empty|max_length[120]',
            'unit_pemilik'        => 'permit_empty|in_list[Ketarunaan,Pusbangkar,Program Studi]',
            'sumber_data'         => 'permit_empty|max_length[100]',
        ];
    }

    /**
     * Menyusun payload Tahap 1 dari POST request.
     *
     * @return array<string, mixed>
     */
    private function buildInformasiPayload(string $unitPemilik): array
    {
        return [
            'nama_prestasi'       => $this->request->getPost('nama_prestasi'),
            'jenis_prestasi'      => $this->request->getPost('jenis_prestasi'),
            'bidang_prestasi'     => $this->request->getPost('bidang_prestasi'),
            'tingkat_prestasi'    => $this->request->getPost('tingkat_prestasi'),
            'peringkat'           => $this->request->getPost('peringkat'),
            'penyelenggara'       => $this->request->getPost('penyelenggara'),
            'lokasi_kegiatan'     => $this->request->getPost('lokasi_kegiatan'),
            'tanggal_mulai'       => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'     => $this->request->getPost('tanggal_selesai'),
            'jenis_keikutsertaan' => $this->request->getPost('jenis_keikutsertaan'),
            'nama_tim'            => $this->request->getPost('nama_tim'),
            'pembimbing'          => $this->request->getPost('pembimbing'),
            'unit_pemilik'        => $unitPemilik,
            'sumber_data'         => $this->request->getPost('sumber_data'),
            'diajukan_oleh'       => $this->resolveActorLabel($unitPemilik),
        ];
    }

    /**
     * Menghapus berkas unggahan saat draf prestasi dihapus.
     *
     * @param array<string, mixed> $detail
     */
    private function cleanupPrestasiFiles(array $detail): void
    {
        $prestasi = $detail['prestasi'] ?? [];
        $rawBukti = trim((string) ($prestasi['bukti_berkas'] ?? ''));
        if ($rawBukti !== '' && !ketarunaan_is_http_url($rawBukti, 2000)) {
            $buktiPath = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . basename($rawBukti);
            if (is_file($buktiPath)) {
                @unlink($buktiPath);
            }
        }

        foreach (($detail['dokumentasi'] ?? []) as $foto) {
            $rawFoto = trim((string) $foto);
            if ($rawFoto === '' || ketarunaan_is_http_url($rawFoto, 2000)) {
                continue;
            }
            $fotoPath = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . basename($rawFoto);
            if (is_file($fotoPath)) {
                @unlink($fotoPath);
            }
        }
    }

    /**
     * Menentukan label peran/identitas pelaku berdasarkan akun aktif.
     */
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

    /**
     * Menentukan nama pejabat validator dari sesi aktif.
     */
    private function resolveValidatorLabel(): string
    {
        $nama = trim((string) (session()->get('nama_lengkap') ?: session()->get('nama') ?: session()->get('username') ?: 'Validator Unit'));

        return mb_substr($nama, 0, 100);
    }
}
