<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\TarunaService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller Modul 1: Database Taruna (ECC 2.0).
 * Mengatur alur request -> validasi -> pemanggilan TarunaService -> response.
 */
class TarunaController extends BaseController
{
    protected TarunaService $tarunaService;

    public function __construct(?TarunaService $tarunaService = null)
    {
        $this->tarunaService = $tarunaService ?? new TarunaService();
        helper(['ketarunaan', 'form']);
    }

    /**
     * Halaman Direktori Database Taruna (/ketarunaan/taruna).
     *
     * @return string|RedirectResponse
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard')->with('error', 'Anda belum memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly()) {
            $ownNomor = KetarunaanPolicy::resolveSelfNomorTaruna();
            if ($ownNomor !== null && KetarunaanPolicy::isSelfTarunaOnly()) {
                return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($ownNomor));
            }
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Direktori Database Taruna hanya tersedia untuk staf dan pimpinan.');
        }

        $filters = [
            'prodi_id'    => $this->request->getGet('prodi_id'),
            'angkatan_id' => $this->request->getGet('angkatan_id'),
            'status'      => $this->request->getGet('status'),
            'q'           => $this->request->getGet('q'),
        ];
        $allowedSorts = ['nomor_taruna', 'nama_lengkap', 'prodi', 'angkatan', 'tingkat', 'jalur_penerimaan', 'status_terkini'];
        $sortByRaw    = trim((string) ($this->request->getGet('sort_by') ?? 'nomor_taruna'));
        $sortBy       = in_array($sortByRaw, $allowedSorts, true) ? $sortByRaw : 'nomor_taruna';
        $sortOrderRaw = strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'asc')));
        $sortOrder    = ($sortOrderRaw === 'desc') ? 'desc' : 'asc';
        $page         = max(1, (int) ($this->request->getGet('page') ?? 1));

        $dirData = $this->tarunaService->getDirectoryData($filters, $page, 15, $sortBy, $sortOrder);
        $refs    = $this->tarunaService->getReferenceData();

        $data = array_merge($dirData, $refs, [
            'page_title' => 'Database Taruna',
            'filters'    => $filters,
            'sort_by'    => $sortBy,
            'sort_order' => $sortOrder,
            'can_manage' => KetarunaanPolicy::canManageTaruna(),
        ]);

        return view('ketarunaan/taruna/index', $data);
    }

    /**
     * Menyimpan taruna baru dari formulir tambah taruna.
     *
     * @return RedirectResponse
     */
    public function store(): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to('/ketarunaan/taruna')->with('error', 'Anda tidak memiliki hak menambah data taruna.');
        }

        $rules = [
            'nomor_taruna'     => 'required|max_length[30]|alpha_numeric',
            'nama_lengkap'     => 'required|max_length[150]',
            'jenis_kelamin'    => 'required|in_list[L,P]',
            'tempat_lahir'     => 'permit_empty|max_length[100]',
            'tanggal_lahir'    => 'permit_empty|valid_date',
            'program_studi_id' => 'required|is_natural_no_zero',
            'angkatan_id'      => 'required|is_natural_no_zero',
            'status_terkini'   => 'permit_empty|in_list[aktif,cuti]',
            'tingkat'          => 'permit_empty|is_natural_no_zero|less_than_equal_to[4]',
            'kelas_id'         => 'permit_empty|is_natural_no_zero',
        ];

        $rawNik = trim((string) ($this->request->getPost('nik') ?? ''));
        if ($rawNik !== '') {
            $nikDigits = preg_replace('/\D/', '', ltrim($rawNik, "' "));
            if (!is_string($nikDigits) || strlen($nikDigits) !== 16) {
                return redirect()->to('/ketarunaan/taruna?modal=tambah')
                    ->withInput()
                    ->with('error', 'NIK harus terdiri dari tepat 16 digit angka.');
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/ketarunaan/taruna?modal=tambah')
                ->withInput()
                ->with('error', 'Periksa kembali isian formulir tambah taruna.');
        }

        try {
            $result = $this->tarunaService->createTaruna($this->request->getPost());
            if (!$result['success']) {
                return redirect()->to('/ketarunaan/taruna?modal=tambah')
                    ->withInput()
                    ->with('error', $result['message']);
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode((string) $result['nomor_taruna']))
                ->with('success', $result['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menambah taruna baru: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna?modal=tambah')
                ->withInput()
                ->with('error', 'Gagal menyimpan data taruna. Silakan coba lagi.');
        }
    }

    /**
     * Halaman Profil Taruna 5 Tab (/ketarunaan/taruna/profil/{nomorTaruna}).
     *
     * @param string $nomorTaruna
     * @return string|RedirectResponse
     */
    public function profil(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard')->with('error', 'Anda belum memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly() && !KetarunaanPolicy::isOwnNomorTaruna($nomorTaruna)) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda hanya dapat mengakses profil taruna milik Anda sendiri.');
        }

        $profilData = $this->tarunaService->getProfilLengkap($nomorTaruna);
        if ($profilData === null) {
            return redirect()->to('/ketarunaan/taruna')->with('error', 'Data taruna tidak ditemukan.');
        }

        $refs      = $this->tarunaService->getReferenceData();
        $activeTab = trim((string) ($this->request->getGet('tab') ?? 'identitas'));
        if ($activeTab === 'status') {
            $activeTab = 'riwayat-status';
        }
        $allowedTabs = ['identitas', 'pendidikan', 'kontak', 'riwayat-status', 'riwayat-pendidikan'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'identitas';
        }

        $data = array_merge($profilData, $refs, [
            'page_title'           => 'Profil Taruna - ' . $profilData['taruna']['nama_lengkap'],
            'active_tab'           => $activeTab,
            'can_manage'           => KetarunaanPolicy::canManageTaruna(),
            'can_edit_own_profile' => KetarunaanPolicy::canUpdateOwnTarunaProfile($nomorTaruna),
            'can_validate_foto'    => KetarunaanPolicy::canValidate() || KetarunaanPolicy::canManageTaruna(),
            'is_self_taruna'       => KetarunaanPolicy::isSelfTarunaOnly(),
            'can_view_sensitive'   => KetarunaanPolicy::canViewSensitiveData($nomorTaruna),
        ]);

        return view('ketarunaan/taruna/profil', $data);
    }

    /**
     * Memperbarui Tab 1: Identitas Utama beserta unggahan foto resmi (maks 2 MB).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse|ResponseInterface
     */
    public function updateIdentitas(string $nomorTaruna): RedirectResponse|ResponseInterface
    {
        $canManage = KetarunaanPolicy::canManageTaruna();
        $canOwn    = KetarunaanPolicy::canUpdateOwnTarunaProfile($nomorTaruna);
        if (!$canManage && !$canOwn) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki izin mengubah identitas taruna ini.');
        }

        $rules = [
            'nama_lengkap'  => 'required|max_length[150]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir'  => 'required|max_length[100]',
            'tanggal_lahir' => 'required|valid_date',
        ];

        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $rules['foto'] = 'max_size[foto,5120]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                ->withInput()
                ->with('error', 'Pastikan seluruh kolom wajib terisi dan foto berformat JPG/PNG yang sah.');
        }

        $rawNik = trim((string) ($this->request->getPost('nik') ?? ''));
        if ($rawNik !== '' && !str_contains($rawNik, '*')) {
            $nikDigits = preg_replace('/\D/', '', ltrim($rawNik, "' "));
            if (!is_string($nikDigits) || strlen($nikDigits) !== 16) {
                return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                    ->withInput()
                    ->with('error', 'NIK harus terdiri dari tepat 16 digit angka.');
            }
        }

        $newFotoName        = null;
        $oldFotoToRemove    = null;
        $oldPendingToRemove = null;
        $uploadDir          = FCPATH . 'assets/uploads/taruna/';
        try {
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                ketarunaan_ensure_upload_protection($uploadDir);

                $existing           = $this->tarunaService->getProfilLengkap($nomorTaruna);
                $oldFotoToRemove    = $existing['taruna']['foto'] ?? null;
                $oldPendingToRemove = $existing['taruna']['foto_pending'] ?? null;

                $randomBase  = pathinfo($fotoFile->getRandomName(), PATHINFO_FILENAME);
                $tmpExt      = $fotoFile->guessExtension() ?: 'jpg';
                $tmpName     = 'tmp_' . bin2hex(random_bytes(8)) . '.' . $tmpExt;
                $newFotoName = $randomBase . '.jpg';

                $fotoFile->move($uploadDir, $tmpName);
                $tmpPath = $uploadDir . $tmpName;

                $compressed = ketarunaan_compress_foto_taruna($tmpPath, $uploadDir . $newFotoName, 256000);
                if (is_file($tmpPath)) {
                    @unlink($tmpPath);
                }

                if (!$compressed) {
                    $newFotoName = null;
                    return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                        ->withInput()
                        ->with('error', 'Ukuran foto melebihi batas 250 KB setelah dikompres. Pilih foto lain.');
                }
            }

            $stageFotoPending = !$canManage;
            $ok = $this->tarunaService->updateIdentitas($nomorTaruna, $this->request->getPost(), $newFotoName, $canManage, $stageFotoPending);
            if ($ok && $newFotoName !== null) {
                if (!$stageFotoPending && !empty($oldFotoToRemove)) {
                    $safeOld = basename((string) $oldFotoToRemove);
                    if ($safeOld !== 'default.png' && is_file($uploadDir . $safeOld)) {
                        @unlink($uploadDir . $safeOld);
                    }
                }
                if (!empty($oldPendingToRemove)) {
                    $safeOldPending = basename((string) $oldPendingToRemove);
                    if ($safeOldPending !== $newFotoName && is_file($uploadDir . $safeOldPending)) {
                        @unlink($uploadDir . $safeOldPending);
                    }
                }
            } elseif (!$ok && $newFotoName !== null && is_file($uploadDir . $newFotoName)) {
                @unlink($uploadDir . $newFotoName);
            }

            if ($canManage) {
                $aksi = $this->request->getPost('aksi_submit') === 'draf'
                    ? 'Draf identitas taruna berhasil disimpan.'
                    : 'Identitas taruna berhasil diperbarui.';
            } elseif ($newFotoName !== null) {
                $aksi = 'Identitas pribadi diperbarui, dan foto baru menunggu validasi petugas.';
            } else {
                $aksi = 'Identitas pribadi Anda berhasil diperbarui.';
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                ->with($ok ? 'success' : 'error', $ok ? $aksi : 'Gagal memperbarui identitas taruna.');
        } catch (\Throwable $e) {
            if ($newFotoName !== null && file_exists($uploadDir . $newFotoName)) {
                @unlink($uploadDir . $newFotoName);
            }
            log_message('error', 'Gagal update identitas taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                ->withInput()
                ->with('error', 'Gagal menyimpan identitas taruna. Silakan coba lagi.');
        }
    }

    /**
     * Memperbarui Tab 2: Data Pendidikan.
     *
     * @param string $nomorTaruna
     * @return RedirectResponse|ResponseInterface
     */
    public function updatePendidikan(string $nomorTaruna): RedirectResponse|ResponseInterface
    {
        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Data pendidikan hanya dapat diperbarui oleh petugas berwenang.');
        }

        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin mengubah data pendidikan.');
        }

        $rules = [
            'program_studi_id' => 'required|is_natural_no_zero',
            'angkatan_id'      => 'required|is_natural_no_zero',
            'kelas_id'         => 'permit_empty|is_natural_no_zero',
            'tingkat'          => 'required|in_list[1,2,3,4]',
            'semester_aktif'   => 'required|in_list[1,2,3,4,5,6,7,8]',
            'jalur_penerimaan' => 'permit_empty|max_length[50]',
            'asal_formasi'     => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=pendidikan')
                ->withInput()
                ->with('error', 'Pilih Program Studi, Angkatan, Tingkat, dan Semester yang sah.');
        }

        try {
            $ok = $this->tarunaService->updatePendidikan($nomorTaruna, $this->request->getPost());
            $aksi = $this->request->getPost('aksi_submit') === 'draf'
                ? 'Draf data pendidikan berhasil disimpan.'
                : 'Data pendidikan taruna berhasil diperbarui.';

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=pendidikan')
                ->with($ok ? 'success' : 'error', $ok ? $aksi : 'Gagal menyimpan data pendidikan.');
        } catch (\Throwable $e) {
            log_message('error', 'Gagal update pendidikan taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=pendidikan')
                ->withInput()
                ->with('error', 'Gagal menyimpan data pendidikan. Silakan coba lagi.');
        }
    }

    /**
     * Memperbarui Tab 3: Kontak dan Keluarga (terproteksi izin data sensitif & layanan mandiri taruna).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse|ResponseInterface
     */
    public function updateKontak(string $nomorTaruna): RedirectResponse|ResponseInterface
    {
        $canManage = KetarunaanPolicy::canManageTaruna();
        $canOwn    = KetarunaanPolicy::canUpdateOwnTarunaProfile($nomorTaruna);
        if (!KetarunaanPolicy::canViewSensitiveData($nomorTaruna) || (!$canManage && !$canOwn)) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Data kontak dan keluarga hanya dapat diubah oleh petugas berwenang atau pemilik profil.');
        }

        if (!$this->validate([
            'email'         => 'permit_empty|valid_email|max_length[150]',
            'nomor_hp'      => 'permit_empty|regex_match[/^[0-9+\-\s()]{8,20}$/]',
            'nomor_hp_wali' => 'permit_empty|regex_match[/^[0-9+\-\s()]{8,20}$/]',
        ])) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                ->withInput()
                ->with('error', 'Pastikan format alamat email dan nomor HP (8–20 angka) benar.');
        }

        try {
            $res = $this->tarunaService->updateKontak($nomorTaruna, $this->request->getPost());
            if (empty($res['success'])) {
                return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                    ->withInput()
                    ->with('error', $res['message']);
            }

            if ($canManage) {
                $aksi = $this->request->getPost('aksi_submit') === 'draf'
                    ? 'Draf kontak dan keluarga berhasil disimpan.'
                    : $res['message'];
            } else {
                $aksi = 'Data kontak, domisili, dan keluarga Anda berhasil diperbarui.';
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                ->with('success', $aksi);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal update kontak taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                ->withInput()
                ->with('error', 'Gagal menyimpan data kontak. Silakan coba lagi.');
        }
    }

    /**
     * Halaman Form Pengajuan Perubahan Status (/ketarunaan/taruna/ubah-status/{nomorTaruna}).
     *
     * @param string $nomorTaruna
     * @return string|RedirectResponse
     */
    public function formUbahStatus(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard')->with('error', 'Anda belum memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Formulir pengajuan perubahan status hanya tersedia untuk staf berwenang.');
        }

        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                ->with('error', 'Anda tidak memiliki hak mengajukan perubahan status.');
        }

        $profilData = $this->tarunaService->getProfilLengkap($nomorTaruna);
        if ($profilData === null) {
            return redirect()->to('/ketarunaan/taruna')->with('error', 'Data taruna tidak ditemukan.');
        }

        $draftRow = null;
        $draftId  = (int) ($this->request->getGet('draft_id') ?? 0);
        if ($draftId > 0) {
            $candidate = $this->tarunaService->getRiwayatStatusById($draftId);
            if (!$candidate || (string) ($candidate['nomor_taruna'] ?? '') !== $nomorTaruna || ($candidate['status_validasi'] ?? '') !== 'draf') {
                return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                    ->with('error', 'Draf perubahan status tidak ditemukan atau sudah diajukan.');
            }
            $draftRow = $candidate;
        }

        $refs = $this->tarunaService->getReferenceData();

        $data = array_merge($profilData, $refs, [
            'page_title' => ($draftRow ? 'Ubah Draf Perubahan Status - ' : 'Ajukan Perubahan Status - ') . $profilData['taruna']['nama_lengkap'],
            'can_manage' => true,
            'draft_row'  => $draftRow,
        ]);

        return view('ketarunaan/taruna/ubah_status', $data);
    }

    /**
     * Menyimpan atau memperbarui pengajuan/draf perubahan status taruna beserta dokumen keputusan PDF.
     *
     * @param string $nomorTaruna
     * @return RedirectResponse|ResponseInterface
     */
    public function storeUbahStatus(string $nomorTaruna): RedirectResponse|ResponseInterface
    {
        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Pengajuan perubahan status hanya tersedia untuk petugas berwenang.');
        }

        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak mengajukan perubahan status.');
        }

        $aksiSubmit = $this->request->getPost('aksi_submit') === 'draf' ? 'draf' : 'ajukan';
        $draftId    = (int) ($this->request->getPost('draft_id') ?? 0);
        $hasExistingDoc = false;
        if ($draftId > 0) {
            $existingDraft = $this->tarunaService->getRiwayatStatusById($draftId);
            if (!$existingDraft || (string) ($existingDraft['nomor_taruna'] ?? '') !== $nomorTaruna || ($existingDraft['status_validasi'] ?? '') !== 'draf') {
                return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                    ->with('error', 'Draf perubahan status tidak ditemukan atau sudah diajukan.');
            }
            $hasExistingDoc = !empty($existingDraft['bukti_dokumen']);
        }

        $rules = [
            'kode_status'       => 'required|in_list[aktif,skorsing,cuti,mengundurkan_diri,drop_out,lulus]',
            'mulai_berlaku'     => 'required|valid_date',
            'tanggal_keputusan' => ($aksiSubmit === 'draf' ? 'permit_empty|valid_date' : 'required|valid_date'),
            'alasan_perubahan'  => 'required|min_length[5]',
            'nomor_keputusan'   => ($aksiSubmit === 'draf' ? 'permit_empty|max_length[100]' : 'required|max_length[100]'),
            'bukti_dokumen'     => 'permit_empty|max_length[255]',
        ];

        $rawLinkDoc = trim((string) ($this->request->getPost('bukti_dokumen') ?? ''));
        if ($rawLinkDoc !== '' && !ketarunaan_is_http_url($rawLinkDoc, 255)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tautan dokumen SK harus valid (awalan http:// atau https://, maksimal 255 karakter).');
        }

        if ($aksiSubmit === 'ajukan' && $rawLinkDoc === '' && !$hasExistingDoc) {
            return redirect()->back()->withInput()
                ->with('error', 'Masukkan tautan dokumen keputusan (https://...) sebelum mengajukan validasi.');
        }

        if (!$this->validate($rules)) {
            $msg = $aksiSubmit === 'draf'
                ? 'Lengkapi status baru, tanggal mulai berlaku, dan alasan perubahan.'
                : 'Lengkapi seluruh kolom bertanda wajib dan pastikan tautan dokumen SK valid.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $uploadDir = FCPATH . 'assets/uploads/sk_status/';
        try {
            $res = $this->tarunaService->ajukanPerubahanStatus(
                $nomorTaruna,
                $this->request->getPost(),
                $rawLinkDoc !== '' ? $rawLinkDoc : null
            );
            if (!$res['success']) {
                return redirect()->back()->withInput()->with('error', $res['message']);
            }

            if (!empty($res['old_bukti_dokumen']) && !ketarunaan_is_http_url((string) $res['old_bukti_dokumen'], 2000)) {
                $safeOld = basename((string) $res['old_bukti_dokumen']);
                if (is_file($uploadDir . $safeOld)) {
                    @unlink($uploadDir . $safeOld);
                }
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                ->with('success', $res['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan perubahan status taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                ->with('error', 'Gagal menyimpan pengajuan status. Silakan coba lagi.');
        }
    }

    /**
     * Membuka tautan dokumen SK perubahan status (URL eksternal atau berkas PDF lama).
     */
    public function unduhSkStatus(int $id): ResponseInterface|RedirectResponse
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $row = $this->tarunaService->getRiwayatStatusById($id);
        if (!$row || empty($row['bukti_dokumen'])) {
            return redirect()->back()->with('error', 'Dokumen SK keputusan tidak ditemukan.');
        }

        $nomorTaruna = (string) ($row['nomor_taruna'] ?? '');
        if (KetarunaanPolicy::isSelfAccountOnly() && !KetarunaanPolicy::isOwnNomorTaruna($nomorTaruna)) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak berwenang mengakses dokumen milik taruna lain.');
        }

        $dokumen = trim((string) $row['bukti_dokumen']);
        if (ketarunaan_is_http_url($dokumen, 2000)) {
            return redirect()->to($dokumen);
        }

        $safeName     = basename($dokumen);
        $physicalPath = FCPATH . 'assets/uploads/sk_status/' . $safeName;
        if (!is_file($physicalPath)) {
            return redirect()->back()->with('error', 'Dokumen SK keputusan tidak tersedia.');
        }

        $inline   = ((int) $this->request->getGet('inline') === 1);
        $mime     = mime_content_type($physicalPath) ?: 'application/pdf';
        $dispName = 'SK_Status_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $nomorTaruna) . '_' . $id . '.pdf';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $dispName . '"')
            ->setBody((string) file_get_contents($physicalPath));
    }

    /**
     * Mengajukan draf perubahan status menuju antrean validasi.
     */
    public function ajukanDraftStatus(int $id): RedirectResponse|ResponseInterface
    {
        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak mengajukan perubahan status.');
        }

        $res    = $this->tarunaService->ajukanDraftStatus($id);
        $target = !empty($res['nomor_taruna'])
            ? '/ketarunaan/taruna/profil/' . urlencode((string) $res['nomor_taruna']) . '?tab=riwayat-status'
            : '/ketarunaan/taruna';

        return redirect()->to($target)->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Menghapus draf perubahan status yang belum diajukan.
     */
    public function hapusDraftStatus(int $id): RedirectResponse|ResponseInterface
    {
        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.');
        }
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak menghapus draf status.');
        }

        $res = $this->tarunaService->hapusDraftStatus($id);
        if (!empty($res['success']) && !empty($res['bukti_dokumen']) && !ketarunaan_is_http_url((string) $res['bukti_dokumen'], 2000)) {
            $safeOld = basename((string) $res['bukti_dokumen']);
            $path    = FCPATH . 'assets/uploads/sk_status/' . $safeOld;
            if (is_file($path)) {
                @unlink($path);
            }
        }

        $target = !empty($res['nomor_taruna'])
            ? '/ketarunaan/taruna/profil/' . urlencode((string) $res['nomor_taruna']) . '?tab=riwayat-status'
            : '/ketarunaan/taruna';

        return redirect()->to($target)->with($res['success'] ? 'success' : 'error', $res['message']);
    }

    /**
     * Menyimpan catatan perubahan riwayat pendidikan (Tab 5).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse
     */
    public function storeRiwayatPendidikan(string $nomorTaruna): RedirectResponse|ResponseInterface
    {
        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Riwayat pendidikan hanya dapat dicatat oleh petugas berwenang.');
        }

        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin mencatat perubahan pendidikan.');
        }

        $rules = [
            'program_studi_id'  => 'required|is_natural_no_zero',
            'tingkat'           => 'required|in_list[1,2,3,4]',
            'semester'          => 'required|in_list[1,2,3,4,5,6,7,8]',
            'mulai_berlaku'     => 'required|valid_date',
            'tahun_akademik_id' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-pendidikan&modal=catat')
                ->withInput()
                ->with('error', 'Lengkapi Tahun Akademik, Tingkat, Semester, dan Tanggal Mulai Berlaku.');
        }

        try {
            $res = $this->tarunaService->catatRiwayatPendidikan($nomorTaruna, $this->request->getPost());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-pendidikan')
                ->with($res['success'] ? 'success' : 'error', $res['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal catat riwayat pendidikan: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-pendidikan')
                ->with('error', 'Gagal menyimpan riwayat pendidikan. Silakan coba lagi.');
        }
    }

    /**
     * Halaman Impor Data Taruna Excel/CSV 3 Tahap (/ketarunaan/taruna/impor).
     *
     * @return string|RedirectResponse
     */
    public function impor()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to('/dashboard')->with('error', 'Anda belum memiliki akses ke Modul Ketarunaan.');
        }

        if (KetarunaanPolicy::isSelfAccountOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Halaman impor data taruna hanya tersedia untuk staf berwenang.');
        }

        $previewData = session()->getFlashdata('impor_preview');

        return view('ketarunaan/taruna/impor', [
            'page_title' => 'Impor Data Taruna',
            'preview'    => is_array($previewData) ? $previewData : null,
            'can_manage' => KetarunaanPolicy::canManageTaruna(),
        ]);
    }

    /**
     * Memproses unggahan berkas Excel untuk diperiksa pada Tahap 2 (Periksa Data).
     *
     * @return RedirectResponse
     */
    public function previewImpor(): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to('/ketarunaan/taruna/impor')->with('error', 'Anda tidak memiliki hak mengimpor data.');
        }

        $file = $this->request->getFile('berkas_impor');
        if (!$file || !$file->isValid()) {
            return redirect()->to('/ketarunaan/taruna/impor')->with('error', 'Pilih berkas Excel terlebih dahulu.');
        }

        $ext = strtolower((string) $file->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'], true)) {
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Gunakan berkas Excel (.xlsx atau .xls).');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Ukuran berkas Excel maksimal 5 MB.');
        }

        $allowedMimes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'application/zip',
            'application/octet-stream',
            'text/html',
            'application/xhtml+xml',
        ];
        $mime = strtolower((string) $file->getMimeType());
        if ($mime !== '' && !in_array($mime, $allowedMimes, true)) {
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Format berkas Excel tidak valid.');
        }

        try {
            $parsed = $this->tarunaService->bacaBerkasImpor($file->getTempName(), $ext);
            if (empty($parsed['rows'])) {
                return redirect()->to('/ketarunaan/taruna/impor')
                    ->with('error', $parsed['message'] ?: 'Berkas tidak memuat daftar taruna yang dapat dibaca.');
            }
            if (count($parsed['rows']) > 2000) {
                return redirect()->to('/ketarunaan/taruna/impor')
                    ->with('error', 'Jumlah baris melebihi batas 2.000 baris per berkas. Bagi berkas menjadi beberapa bagian.');
            }

            $preview = $this->tarunaService->periksaBerkasImpor($parsed['rows'], $file->getClientName());
            session()->set('impor_valid_rows', $preview['valid_rows']);

            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('impor_preview', $preview)
                ->with('success', 'Berkas berhasil diperiksa. Silakan tinjau hasil pemeriksaan di bawah.');
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memeriksa berkas impor taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Gagal memeriksa berkas. Pastikan format dan isian berkas Excel sesuai.');
        }
    }

    /**
     * Menyimpan baris valid hasil pemeriksaan sebagai draf (Tahap 3).
     *
     * @return RedirectResponse
     */
    public function prosesImpor(): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to('/ketarunaan/taruna/impor')->with('error', 'Anda tidak memiliki hak mengimpor data.');
        }

        $validRows = session()->get('impor_valid_rows');
        if (empty($validRows) || !is_array($validRows)) {
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Belum ada data hasil pemeriksaan berkas untuk disimpan.');
        }

        try {
            $res = $this->tarunaService->simpanImporValid($validRows);
            session()->remove('impor_valid_rows');

            return redirect()->to('/ketarunaan/taruna')
                ->with($res['success'] ? 'success' : 'error', $res['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan impor taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Gagal menyimpan data impor. Silakan coba lagi.');
        }
    }

    /**
     * Mengunduh berkas template Excel (.xlsx) resmi untuk impor data taruna.
     *
     * @return ResponseInterface
     */
    public function unduhTemplate(): ResponseInterface
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Hanya petugas pengelola taruna yang dapat mengunduh template impor.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Evidence Command Center (ECC) - PKTJ Tegal')
            ->setTitle('Template Impor Data Taruna ECC');

        // Sheet 1: Data Taruna
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Taruna');

        $headers = ['nomor_taruna', 'nama_lengkap', 'kode_prodi', 'tahun_masuk', 'jenis_kelamin', 'tempat_lahir', 'tingkat', 'nama_kelas'];
        foreach ($headers as $idx => $hdr) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->setCellValue($col . '1', $hdr);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill'      => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EFF6FF'],
            ],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'BFDBFE'],
                ],
            ],
        ]);

        // Simpan nomor_taruna & tahun_masuk sebagai teks agar angka nol awal dan format "2022/XXXIII" aman
        $samples = [
            ['2601001', 'Andi Pratama', 'RSTJ', '2026', 'L', 'Tegal', 1, 'A'],
            ['2201002', 'Sinta Dewi', 'RSTJ', '2022/XXXIII', 'P', 'Semarang', 4, 'A'],
        ];
        foreach ($samples as $rowOffset => $rowData) {
            $rowNum = $rowOffset + 2;
            $sheet->setCellValueExplicit('A' . $rowNum, $rowData[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $rowNum, $rowData[1]);
            $sheet->setCellValue('C' . $rowNum, $rowData[2]);
            $sheet->setCellValueExplicit('D' . $rowNum, $rowData[3], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNum, $rowData[4]);
            $sheet->setCellValue('F' . $rowNum, $rowData[5]);
            $sheet->setCellValue('G' . $rowNum, $rowData[6]);
            $sheet->setCellValue('H' . $rowNum, $rowData[7]);
        }
        $sheet->getStyle('A2:A500')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('D2:D500')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Sheet 2: Panduan Pengisian
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Panduan Pengisian');
        $guideRows = [
            ['Kolom', 'Keterangan', 'Contoh'],
            ['nomor_taruna', 'Wajib diisi. 2 digit awal otomatis menandai tahun masuk.', '2601001 atau 2201014'],
            ['nama_lengkap', 'Wajib diisi sesuai nama resmi taruna.', 'Andi Pratama'],
            ['kode_prodi', 'Kode program studi aktif di ECC (RSTJ, TRO, atau TO).', 'RSTJ'],
            ['tahun_masuk', 'Isi tahun masuk (mis. 2026) atau format Tahun/Romawi (mis. 2022/XXXIII). Jika kosong, dibaca dari 2 digit awal nomor taruna dan dibuat otomatis jika belum terdaftar.', '2026 atau 2022/XXXIII'],
            ['jenis_kelamin', 'Isi L (Laki-laki) atau P (Perempuan).', 'L'],
            ['tempat_lahir', 'Kota atau kabupaten kelahiran.', 'Tegal'],
            ['tingkat', 'Tingkat berjalan saat ini (1 sampai 4). Untuk taruna tahan tingkat, isi tahun masuk asli dan tingkat berjalan saat ini.', '1'],
            ['nama_kelas', 'Nama kelas berjalan (A, B, C, D, E).', 'A'],
        ];
        foreach ($guideRows as $rIdx => $gRow) {
            $rNum = $rIdx + 1;
            $guide->setCellValue('A' . $rNum, $gRow[0]);
            $guide->setCellValue('B' . $rNum, $gRow[1]);
            $guide->setCellValue('C' . $rNum, $gRow[2]);
        }
        $guide->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EFF6FF'],
            ],
        ]);
        $guide->getColumnDimension('A')->setAutoSize(true);
        $guide->getColumnDimension('B')->setWidth(64);
        $guide->getColumnDimension('C')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $xlsxBinary = (string) ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="template_impor_taruna_ecc.xlsx"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($xlsxBinary);
    }
}

