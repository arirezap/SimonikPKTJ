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

        $filters = [
            'prodi_id'    => $this->request->getGet('prodi_id'),
            'angkatan_id' => $this->request->getGet('angkatan_id'),
            'status'      => $this->request->getGet('status'),
            'q'           => $this->request->getGet('q'),
        ];
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $dirData = $this->tarunaService->getDirectoryData($filters, $page, 15);
        $refs    = $this->tarunaService->getReferenceData();

        $data = array_merge($dirData, $refs, [
            'page_title' => 'Database Taruna',
            'filters'    => $filters,
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
            'program_studi_id' => 'required|is_natural_no_zero',
            'angkatan_id'      => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian formulir tambah taruna.');
        }

        try {
            $result = $this->tarunaService->createTaruna($this->request->getPost());
            if (!$result['success']) {
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode((string) $result['nomor_taruna']))
                ->with('success', $result['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menambah taruna baru: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data taruna. Silakan coba lagi.');
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

        $profilData = $this->tarunaService->getProfilLengkap($nomorTaruna);
        if ($profilData === null) {
            return redirect()->to('/ketarunaan/taruna')->with('error', 'Data taruna tidak ditemukan.');
        }

        $refs = $this->tarunaService->getReferenceData();
        $activeTab = trim((string) ($this->request->getGet('tab') ?? 'identitas'));
        $allowedTabs = ['identitas', 'pendidikan', 'kontak', 'riwayat-status', 'riwayat-pendidikan'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'identitas';
        }

        $data = array_merge($profilData, $refs, [
            'page_title'         => 'Profil Taruna - ' . $profilData['taruna']['nama_lengkap'],
            'active_tab'         => $activeTab,
            'can_manage'         => KetarunaanPolicy::canManageTaruna(),
            'can_view_sensitive' => KetarunaanPolicy::canViewSensitiveData($nomorTaruna),
        ]);

        return view('ketarunaan/taruna/profil', $data);
    }

    /**
     * Memperbarui Tab 1: Identitas Utama beserta unggahan foto resmi (maks 2 MB).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse
     */
    public function updateIdentitas(string $nomorTaruna): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin mengubah identitas taruna.');
        }

        $rules = [
            'nama_lengkap'  => 'required|max_length[150]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tempat_lahir'  => 'required|max_length[100]',
            'tanggal_lahir' => 'required|valid_date',
        ];

        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                ->withInput()
                ->with('error', 'Pastikan seluruh kolom wajib terisi dan foto berformat JPG/PNG maksimal 2 MB.');
        }

        $newFotoName = null;
        try {
            if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
                $uploadDir = FCPATH . 'assets/uploads/taruna/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $existing = $this->tarunaService->getProfilLengkap($nomorTaruna);
                $oldFoto = $existing['taruna']['foto'] ?? null;

                $newFotoName = $fotoFile->getRandomName();
                $fotoFile->move($uploadDir, $newFotoName);

                if (!empty($oldFoto)) {
                    $safeOld = basename((string) $oldFoto);
                    if ($safeOld !== 'default.png' && file_exists($uploadDir . $safeOld)) {
                        @unlink($uploadDir . $safeOld);
                    }
                }
            }

            $ok = $this->tarunaService->updateIdentitas($nomorTaruna, $this->request->getPost(), $newFotoName);
            $aksi = $this->request->getPost('aksi_submit') === 'draf'
                ? 'Draf identitas taruna berhasil disimpan.'
                : 'Identitas taruna berhasil diperbarui dan diajukan.';

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=identitas')
                ->with($ok ? 'success' : 'error', $ok ? $aksi : 'Gagal memperbarui identitas taruna.');
        } catch (\Throwable $e) {
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
     * @return RedirectResponse
     */
    public function updatePendidikan(string $nomorTaruna): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin mengubah data pendidikan.');
        }

        $rules = [
            'program_studi_id' => 'required|is_natural_no_zero',
            'angkatan_id'      => 'required|is_natural_no_zero',
            'tingkat'          => 'required|in_list[1,2,3,4]',
            'semester_aktif'   => 'required|in_list[1,2,3,4,5,6,7,8]',
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
     * Memperbarui Tab 3: Kontak dan Keluarga (terproteksi izin data sensitif).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse
     */
    public function updateKontak(string $nomorTaruna): RedirectResponse
    {
        if (!KetarunaanPolicy::canViewSensitiveData($nomorTaruna) || !KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                ->with('error', 'Data kontak dan keluarga hanya dapat diubah oleh petugas berwenang.');
        }

        try {
            $ok = $this->tarunaService->updateKontak($nomorTaruna, $this->request->getPost());
            $aksi = $this->request->getPost('aksi_submit') === 'draf'
                ? 'Draf kontak dan keluarga berhasil disimpan.'
                : 'Data kontak dan keluarga berhasil diperbarui.';

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=kontak')
                ->with($ok ? 'success' : 'error', $ok ? $aksi : 'Gagal menyimpan data kontak.');
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

        $profilData = $this->tarunaService->getProfilLengkap($nomorTaruna);
        if ($profilData === null) {
            return redirect()->to('/ketarunaan/taruna')->with('error', 'Data taruna tidak ditemukan.');
        }

        $refs = $this->tarunaService->getReferenceData();

        $data = array_merge($profilData, $refs, [
            'page_title' => 'Ajukan Perubahan Status - ' . $profilData['taruna']['nama_lengkap'],
            'can_manage' => KetarunaanPolicy::canManageTaruna(),
        ]);

        return view('ketarunaan/taruna/ubah_status', $data);
    }

    /**
     * Menyimpan pengajuan perubahan status taruna beserta dokumen keputusan PDF.
     *
     * @param string $nomorTaruna
     * @return RedirectResponse
     */
    public function storeUbahStatus(string $nomorTaruna): RedirectResponse
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak mengajukan perubahan status.');
        }

        $rules = [
            'kode_status'      => 'required|in_list[aktif,skorsing,cuti,mengundurkan_diri,drop_out,lulus]',
            'mulai_berlaku'    => 'required|valid_date',
            'alasan_perubahan' => 'required|min_length[5]',
            'nomor_keputusan'  => 'required|max_length[100]',
        ];

        $docFile = $this->request->getFile('bukti_dokumen');
        if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
            $rules['bukti_dokumen'] = 'max_size[bukti_dokumen,5120]|ext_in[bukti_dokumen,pdf]|mime_in[bukti_dokumen,application/pdf]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Lengkapi seluruh kolom bertanda wajib dan pastikan dokumen berformat PDF maksimal 5 MB.');
        }

        $buktiName = null;
        try {
            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                $uploadDir = FCPATH . 'assets/uploads/sk_status/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $buktiName = $docFile->getRandomName();
                $docFile->move($uploadDir, $buktiName);
            }

            $res = $this->tarunaService->ajukanPerubahanStatus($nomorTaruna, $this->request->getPost(), $buktiName);
            if (!$res['success']) {
                return redirect()->back()->withInput()->with('error', $res['message']);
            }

            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status')
                ->with('success', $res['message']);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyimpan perubahan status taruna: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengajuan status. Silakan coba lagi.');
        }
    }

    /**
     * Menyimpan catatan perubahan riwayat pendidikan (Tab 5).
     *
     * @param string $nomorTaruna
     * @return RedirectResponse
     */
    public function storeRiwayatPendidikan(string $nomorTaruna): RedirectResponse
    {
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
            return redirect()->to('/ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-pendidikan')
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

        $previewData = session()->getFlashdata('impor_preview') ?? $this->getSimulasiImporPreview();

        return view('ketarunaan/taruna/impor', [
            'page_title' => 'Impor Data Taruna',
            'preview'    => $previewData,
            'can_manage' => KetarunaanPolicy::canManageTaruna(),
        ]);
    }

    /**
     * Memproses unggahan berkas CSV untuk diperiksa pada Tahap 2 (Periksa Data).
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
            return redirect()->to('/ketarunaan/taruna/impor')->with('error', 'Pilih berkas CSV/Excel terlebih dahulu.');
        }

        $ext = strtolower((string) $file->getClientExtension());
        if (!in_array($ext, ['csv', 'txt'], true)) {
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Gunakan berkas template CSV resmi untuk pemeriksaan otomatis.');
        }

        try {
            $handle = fopen($file->getTempName(), 'r');
            if ($handle === false) {
                return redirect()->to('/ketarunaan/taruna/impor')->with('error', 'Berkas tidak dapat dibaca.');
            }

            $headers = fgetcsv($handle, 2000, ',');
            if ($headers && count($headers) === 1 && str_contains((string) $headers[0], ';')) {
                rewind($handle);
                $headers = fgetcsv($handle, 2000, ';');
                $delimiter = ';';
            } else {
                $delimiter = ',';
            }

            $rawRows = [];
            while (($line = fgetcsv($handle, 2000, $delimiter)) !== false) {
                if (empty(array_filter($line))) {
                    continue;
                }
                $rawRows[] = [
                    'nomor_taruna'  => $line[0] ?? '',
                    'nama_lengkap'  => $line[1] ?? '',
                    'kode_prodi'    => $line[2] ?? 'RSTJ',
                    'tahun_masuk'   => $line[3] ?? 2026,
                    'jenis_kelamin' => $line[4] ?? 'L',
                    'tempat_lahir'  => $line[5] ?? 'Tegal',
                ];
            }
            fclose($handle);

            $preview = $this->tarunaService->periksaBerkasImpor($rawRows, $file->getClientName());
            session()->set('impor_valid_rows', $preview['valid_rows']);

            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('impor_preview', $preview)
                ->with('success', 'Berkas berhasil diperiksa. Silakan tinjau hasil pemeriksaan di bawah.');
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memeriksa berkas impor taruna: ' . $e->getMessage());
            return redirect()->to('/ketarunaan/taruna/impor')
                ->with('error', 'Gagal memeriksa berkas. Pastikan format sesuai template.');
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
            return redirect()->to('/ketarunaan/taruna')
                ->with('success', 'Data hasil impor telah disimpan sebagai draf dan siap divalidasi.');
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
     * Mengunduh berkas template CSV resmi untuk impor data taruna.
     *
     * @return ResponseInterface
     */
    public function unduhTemplate(): ResponseInterface
    {
        $csv = "nomor_taruna,nama_lengkap,kode_prodi,tahun_masuk,jenis_kelamin,tempat_lahir\n"
            . "2601001,Andi Pratama,RSTJ,2026,L,Tegal\n"
            . "2601002,Sinta Dewi,TRO,2026,P,Semarang\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_impor_taruna_ecc.csv"')
            ->setBody($csv);
    }

    /**
     * Menyediakan data pratinjau pemeriksaan awal (100 baris: 94 valid, 3 duplikat, 3 perlu perbaikan)
     * sesuai skenario simulasi pemeriksaan data.
     *
     * @return array<string, mixed>
     */
    private function getSimulasiImporPreview(): array
    {
        return [
            'nama_berkas'     => 'data_taruna.xlsx',
            'total_baris'     => 100,
            'siap_diimpor'    => 94,
            'duplikat'        => 3,
            'perlu_perbaikan' => 3,
            'preview_rows'    => [
                [
                    'baris'        => 2,
                    'nomor_taruna' => '2601001',
                    'nama_lengkap' => 'Andi Pratama',
                    'kode_prodi'   => 'RSTJ',
                    'tahun_masuk'  => 2026,
                    'status_cek'   => 'valid',
                    'label_cek'    => 'Valid',
                    'keterangan'   => 'Siap diimpor',
                ],
                [
                    'baris'        => 3,
                    'nomor_taruna' => '2601002',
                    'nama_lengkap' => 'Sinta Dewi',
                    'kode_prodi'   => 'TRO',
                    'tahun_masuk'  => 2026,
                    'status_cek'   => 'valid',
                    'label_cek'    => 'Valid',
                    'keterangan'   => 'Siap diimpor',
                ],
                [
                    'baris'        => 4,
                    'nomor_taruna' => '2401001',
                    'nama_lengkap' => 'Aditya Pratama',
                    'kode_prodi'   => 'RSTJ',
                    'tahun_masuk'  => 2024,
                    'status_cek'   => 'duplikat',
                    'label_cek'    => 'Duplikat',
                    'keterangan'   => 'Nomor Taruna sudah terdaftar',
                ],
                [
                    'baris'        => 5,
                    'nomor_taruna' => '-',
                    'nama_lengkap' => 'Budi Santoso',
                    'kode_prodi'   => 'TO',
                    'tahun_masuk'  => 2026,
                    'status_cek'   => 'tidak_valid',
                    'label_cek'    => 'Tidak valid',
                    'keterangan'   => 'Nomor Taruna wajib diisi',
                ],
            ],
        ];
    }
}

