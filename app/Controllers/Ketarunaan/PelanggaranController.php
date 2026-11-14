<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\PelanggaranService;

/**
 * Controller Modul 4: Pelanggaran Taruna (ECC 2.0).
 * Mengatur alur request HTTP secara ramping (SRP), memisahkan status dugaan/terbukti/validasi,
 * dan menegakkan pembatasan akses identitas & dokumen terbatas di sisi server.
 */
class PelanggaranController extends BaseController
{
    protected PelanggaranService $pelanggaranService;

    public function __construct(?PelanggaranService $pelanggaranService = null)
    {
        $this->pelanggaranService = $pelanggaranService ?? new PelanggaranService();
        helper(['ketarunaan', 'form', 'audit']);
    }

    /**
     * Halaman Daftar Kasus Pelanggaran (/ketarunaan/pelanggaran).
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessPelanggaranModule()) {
            return redirect()->to(base_url('ketarunaan/dashboard'))
                ->with('error', 'Akses ke Modul Pelanggaran dibatasi hanya bagi petugas dan pimpinan berwenang.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $filters = [
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'            => trim((string) ($this->request->getGet('prodi_id') ?? '')),
            'tahun'               => trim((string) ($this->request->getGet('tahun') ?? 'all')),
            'status_penetapan'    => trim((string) ($this->request->getGet('status_penetapan') ?? 'all')),
            'status_penyelesaian' => trim((string) ($this->request->getGet('status_penyelesaian') ?? 'all')),
            'status_validasi'     => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
        ];
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $dataDir = $this->pelanggaranService->getDirectoryData($filters, $page, 10, $canViewFull);

        return view('ketarunaan/pelanggaran/index', [
            'title'           => 'Pelanggaran Taruna - Evidence Command Center (ECC)',
            'filters'         => $filters,
            'metrics'         => $dataDir['metrics'],
            'rows'            => $dataDir['rows'],
            'pagination'      => $dataDir['pagination'],
            'refs'            => $this->pelanggaranService->getReferenceOptions(),
            'canViewFull'     => $canViewFull,
            'canManage'       => KetarunaanPolicy::canManagePelanggaran(),
            'canDecide'       => KetarunaanPolicy::canDecidePelanggaran(),
        ]);
    }

    /**
     * Halaman Tahap 1: Catat Dugaan Pelanggaran.
     */
    public function create()
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mencatat kasus pelanggaran.');
        }

        return view('ketarunaan/pelanggaran/form_pencatatan', [
            'title'       => 'Catat Dugaan Pelanggaran - Evidence Command Center (ECC)',
            'mode'        => 'create',
            'kasus'       => ['nomor_perkara' => $this->pelanggaranService->generateSuggestedNomorPerkara()],
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'tarunaList'  => $this->pelanggaranService->getTarunaSelectList(),
            'canViewFull' => KetarunaanPolicy::canViewDisciplinaryRecord(),
        ]);
    }

    /**
     * Proses simpan Tahap 1 pencatatan kasus baru.
     */
    public function store()
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mencatat kasus pelanggaran.');
        }

        $rules = [
            'nomor_perkara'     => 'required|min_length[3]|max_length[100]',
            'nomor_taruna'      => 'required|max_length[30]',
            'tanggal_kejadian'  => 'required|valid_date[Y-m-d]',
            'lokasi'            => 'required|max_length[150]',
            'jenis_pelanggaran' => 'required|max_length[100]',
            'pelapor'           => 'required|max_length[100]',
            'uraian'            => 'required|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Lengkapi seluruh kolom wajib pada pencatatan dugaan pelanggaran.');
        }

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));
        $result = $this->pelanggaranService->savePencatatan([
            'nomor_perkara'       => $this->request->getPost('nomor_perkara'),
            'nomor_taruna'        => $this->request->getPost('nomor_taruna'),
            'tanggal_kejadian'    => $this->request->getPost('tanggal_kejadian'),
            'waktu_kejadian'      => $this->request->getPost('waktu_kejadian'),
            'lokasi'              => $this->request->getPost('lokasi'),
            'jenis_pelanggaran'   => $this->request->getPost('jenis_pelanggaran'),
            'pelapor'             => $this->request->getPost('pelapor'),
            'uraian'              => $this->request->getPost('uraian'),
            'unit_pemilik'        => $unitPemilik,
            'sumber_data'         => $this->request->getPost('sumber_data'),
            'tingkat_kerahasiaan' => $this->request->getPost('tingkat_kerahasiaan'),
            'diajukan_oleh'       => $this->resolveActorLabel($unitPemilik),
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $kasusId = (int) ($result['id'] ?? 0);
        if (function_exists('log_audit')) {
            log_audit('CREATE', 'Mencatat dugaan kasus pelanggaran ID #' . $kasusId);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'next'));
        if ($aksiSubmit === 'draf') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/edit/' . $kasusId))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/pemeriksaan/' . $kasusId))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 1: Edit Pencatatan Dugaan Pelanggaran.
     */
    public function edit(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengubah pencatatan kasus.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus tidak ditemukan.');
        }

        return view('ketarunaan/pelanggaran/form_pencatatan', [
            'title'       => 'Edit Pencatatan Kasus - Evidence Command Center (ECC)',
            'mode'        => 'edit',
            'kasus'       => $detail['kasus'],
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'tarunaList'  => $this->pelanggaranService->getTarunaSelectList(),
            'canViewFull' => $canViewFull,
        ]);
    }

    /**
     * Proses simpan perubahan Tahap 1 Pencatatan Kasus.
     */
    public function update(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mengubah pencatatan kasus.');
        }

        $rules = [
            'nomor_perkara'     => 'required|min_length[3]|max_length[100]',
            'nomor_taruna'      => 'required|max_length[30]',
            'tanggal_kejadian'  => 'required|valid_date[Y-m-d]',
            'lokasi'            => 'required|max_length[150]',
            'jenis_pelanggaran' => 'required|max_length[100]',
            'pelapor'           => 'required|max_length[100]',
            'uraian'            => 'required|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Lengkapi seluruh kolom wajib pada pencatatan dugaan pelanggaran.');
        }

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));
        $result = $this->pelanggaranService->savePencatatan([
            'nomor_perkara'       => $this->request->getPost('nomor_perkara'),
            'nomor_taruna'        => $this->request->getPost('nomor_taruna'),
            'tanggal_kejadian'    => $this->request->getPost('tanggal_kejadian'),
            'waktu_kejadian'      => $this->request->getPost('waktu_kejadian'),
            'lokasi'              => $this->request->getPost('lokasi'),
            'jenis_pelanggaran'   => $this->request->getPost('jenis_pelanggaran'),
            'pelapor'             => $this->request->getPost('pelapor'),
            'uraian'              => $this->request->getPost('uraian'),
            'unit_pemilik'        => $unitPemilik,
            'sumber_data'         => $this->request->getPost('sumber_data'),
            'tingkat_kerahasiaan' => $this->request->getPost('tingkat_kerahasiaan'),
            'diajukan_oleh'       => $this->resolveActorLabel($unitPemilik),
        ], $id);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui pencatatan awal kasus pelanggaran ID #' . $id);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'next'));
        if ($aksiSubmit === 'draf') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/edit/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/pemeriksaan/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 2: Pemeriksaan Kasus & Pihak Terkait.
     */
    public function pemeriksaan(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola pemeriksaan kasus.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus tidak ditemukan.');
        }

        return view('ketarunaan/pelanggaran/form_pemeriksaan', [
            'title'       => 'Pemeriksaan Kasus - Evidence Command Center (ECC)',
            'detail'      => $detail,
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'tarunaList'  => $this->pelanggaranService->getTarunaSelectList(),
            'canViewFull' => $canViewFull,
        ]);
    }

    /**
     * Proses simpan Tahap 2 Pemeriksaan Kasus dan unggahan BAP opsional.
     */
    public function savePemeriksaan(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola pemeriksaan kasus.');
        }

        $rules = [
            'tanggal_pemeriksaan'   => 'required|valid_date[Y-m-d]',
            'pemeriksa'             => 'required|max_length[120]',
            'dasar_peraturan'       => 'required|max_length[200]',
            'ringkasan_klarifikasi' => 'required|max_length[1000]',
            'hasil_pemeriksaan'     => 'required|max_length[1000]',
        ];

        $fileBap = $this->request->getFile('berkas_bap');
        if ($fileBap && $fileBap->isValid() && !$fileBap->hasMoved()) {
            $rules['berkas_bap'] = 'max_size[berkas_bap,5120]|ext_in[berkas_bap,pdf]|mime_in[berkas_bap,application/pdf]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Lengkapi data pemeriksaan dan pastikan berkas BAP berformat PDF maksimal 5 MB.');
        }

        $uploadedBapName = null;
        $uploadedBapSize = 0;

        if ($fileBap && $fileBap->isValid() && !$fileBap->hasMoved()) {
            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/pelanggaran';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadedBapName = $fileBap->getRandomName();
            $uploadedBapSize = max(1, (int) round($fileBap->getSize() / 1024));
            $fileBap->move($uploadDir, $uploadedBapName);
        }

        $result = $this->pelanggaranService->savePemeriksaan($id, [
            'tanggal_pemeriksaan'   => $this->request->getPost('tanggal_pemeriksaan'),
            'pemeriksa'             => $this->request->getPost('pemeriksa'),
            'dasar_peraturan'       => $this->request->getPost('dasar_peraturan'),
            'tingkat_pelanggaran'   => $this->request->getPost('tingkat_pelanggaran'),
            'ringkasan_klarifikasi' => $this->request->getPost('ringkasan_klarifikasi'),
            'hasil_pemeriksaan'     => $this->request->getPost('hasil_pemeriksaan'),
            'berkas_bap'            => $uploadedBapName,
            'bap_size_kb'           => $uploadedBapSize,
        ], $this->resolveActorLabel('Tim Pemeriksa'));

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui hasil pemeriksaan kasus pelanggaran ID #' . $id);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'next'));
        if ($aksiSubmit === 'draf') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/pemeriksaan/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/penetapan/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menambahkan pihak terkait kasus (Taruna atau Pihak Eksternal).
     */
    public function addPihak(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola pihak kasus.');
        }

        $result = $this->pelanggaranService->addPihak($id, [
            'jenis_pihak'  => $this->request->getPost('jenis_pihak'),
            'nomor_taruna' => $this->request->getPost('nomor_taruna'),
            'nama_pihak'   => $this->request->getPost('nama_pihak'),
            'peran'        => $this->request->getPost('peran'),
            'keterangan'   => $this->request->getPost('keterangan'),
        ]);

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('CREATE', 'Menambah pihak terkait pada kasus pelanggaran ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/pemeriksaan/' . $id))
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Menghapus pihak terkait dari suatu kasus.
     */
    public function removePihak(int $id, int $pihakId)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus pihak kasus.');
        }

        $result = $this->pelanggaranService->removePihak($id, $pihakId);

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('DELETE', 'Menghapus pihak ID #' . $pihakId . ' dari kasus pelanggaran ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/pemeriksaan/' . $id))
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Halaman Tahap 3: Penetapan dan Tindak Lanjut Kasus.
     */
    public function penetapan(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola penetapan kasus.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus tidak ditemukan.');
        }

        return view('ketarunaan/pelanggaran/form_penetapan', [
            'title'       => 'Penetapan & Tindak Lanjut - Evidence Command Center (ECC)',
            'detail'      => $detail,
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'canViewFull' => $canViewFull,
            'canDecide'   => KetarunaanPolicy::canDecidePelanggaran(),
        ]);
    }

    /**
     * Proses simpan Tahap 3 Penetapan Hasil dan Tindak Lanjut.
     */
    public function savePenetapan(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang menyimpan penetapan kasus.');
        }

        $rules = [
            'status_penetapan'    => 'required|in_list[dugaan,terbukti,tidak_terbukti,dibatalkan]',
            'status_penyelesaian' => 'required|in_list[proses,selesai,banding]',
            'tindak_lanjut'       => 'required|max_length[1000]',
            'pic_tindak_lanjut'   => 'required|max_length[120]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Lengkapi status penetapan, penyelesaian, rencana tindak lanjut, dan PIC.');
        }

        $result = $this->pelanggaranService->savePenetapan($id, [
            'status_penetapan'    => $this->request->getPost('status_penetapan'),
            'tingkat_pelanggaran' => $this->request->getPost('tingkat_pelanggaran'),
            'dasar_peraturan'     => $this->request->getPost('dasar_peraturan'),
            'jenis_sanksi'        => $this->request->getPost('jenis_sanksi'),
            'sanksi'              => $this->request->getPost('sanksi'),
            'nomor_keputusan'     => $this->request->getPost('nomor_keputusan'),
            'tanggal_keputusan'   => $this->request->getPost('tanggal_keputusan'),
            'tindak_lanjut'       => $this->request->getPost('tindak_lanjut'),
            'status_penyelesaian' => $this->request->getPost('status_penyelesaian'),
            'tanggal_selesai'     => $this->request->getPost('tanggal_selesai'),
            'pic_tindak_lanjut'   => $this->request->getPost('pic_tindak_lanjut'),
            'catatan_pemantauan'  => $this->request->getPost('catatan_pemantauan'),
        ], $this->resolveActorLabel('PIC Ketarunaan'));

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Menyimpan penetapan & tindak lanjut kasus pelanggaran ID #' . $id);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'next'));
        if ($aksiSubmit === 'draf') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/penetapan/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/dokumen/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 4: Dokumen Bukti Kasus & Pengajuan Validasi.
     */
    public function dokumen(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola dokumen bukti kasus.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus tidak ditemukan.');
        }

        return view('ketarunaan/pelanggaran/dokumen_bukti', [
            'title'       => 'Dokumen Bukti Kasus - Evidence Command Center (ECC)',
            'detail'      => $detail,
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'canViewFull' => $canViewFull,
        ]);
    }

    /**
     * Menyimpan dokumen unggahan dan opsional mengajukan kasus ke validasi pencatatan.
     */
    public function saveDokumen(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola dokumen kasus.');
        }

        $detail = $this->pelanggaranService->getDetail($id, true);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus tidak ditemukan.');
        }

        $fileBukti   = $this->request->getFile('dokumen_file');
        $uploadedDoc = null;

        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $ext  = strtolower($fileBukti->getExtension());
            $mime = strtolower((string) $fileBukti->getMimeType());
            $size = $fileBukti->getSize();

            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'], true)
                || !in_array($mime, ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'], true)
                || $size > 5 * 1024 * 1024
            ) {
                return redirect()->back()->withInput()
                    ->with('error', 'Berkas dokumen harus berformat PDF, JPG, atau PNG maksimal 5 MB.');
            }

            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/pelanggaran';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $randomName = $fileBukti->getRandomName();
            $fileBukti->move($uploadDir, $randomName);

            $jenisDok    = trim((string) ($this->request->getPost('jenis_dokumen') ?? 'Laporan'));
            $klasifikasi = ($this->request->getPost('klasifikasi') === 'sangat_terbatas') ? 'sangat_terbatas' : 'terbatas';

            $uploadedDoc = [
                'nama_berkas'    => $randomName,
                'jenis_dokumen'  => in_array($jenisDok, ['Laporan', 'Pemeriksaan', 'Penetapan', 'Pendukung'], true) ? $jenisDok : 'Pendukung',
                'klasifikasi'    => $klasifikasi,
                'tanggal_unggah' => date('Y-m-d'),
                'ukuran_kb'      => max(1, (int) round($size / 1024)),
            ];
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'draf'));
        $result = $this->pelanggaranService->saveDokumenAndSubmit($id, [
            'uploaded_doc' => $uploadedDoc,
            'aksi_submit'  => $aksiSubmit,
            'pelaku'       => $this->resolveActorLabel((string) ($detail['kasus']['unit_pemilik'] ?? 'Ketarunaan')),
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui dokumen bukti kasus pelanggaran ID #' . $id . ' (' . $aksiSubmit . ')');
        }

        if ($aksiSubmit === 'diajukan') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/dokumen/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menghapus satu file bukti kasus.
     */
    public function removeDokumen(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus dokumen kasus.');
        }

        $filename = trim((string) $this->request->getPost('filename'));
        $result   = $this->pelanggaranService->removeDokumen($id, $filename);

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('DELETE', 'Menghapus berkas dokumen pada kasus pelanggaran ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/dokumen/' . $id))
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Halaman Detail Kasus Pelanggaran 5 Tab (Ringkasan, Pemeriksaan, Penetapan & Tindak Lanjut, Dokumen Bukti, Riwayat Validasi).
     */
    public function detail(int $id)
    {
        if (!KetarunaanPolicy::canAccessPelanggaranModule()) {
            return redirect()->to(base_url('ketarunaan/dashboard'))
                ->with('error', 'Akses ke Modul Pelanggaran dibatasi hanya bagi petugas dan pimpinan berwenang.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Data kasus pelanggaran tidak ditemukan.');
        }

        $tab = strtolower(trim((string) ($this->request->getGet('tab') ?? 'ringkasan')));
        if (!in_array($tab, ['ringkasan', 'pemeriksaan', 'penetapan', 'dokumen', 'riwayat'], true)) {
            $tab = 'ringkasan';
        }

        return view('ketarunaan/pelanggaran/detail', [
            'title'       => 'Detail Kasus Pelanggaran - Evidence Command Center (ECC)',
            'active_tab'  => $tab,
            'detail'      => $detail,
            'refs'        => $this->pelanggaranService->getReferenceOptions(),
            'canViewFull' => $canViewFull,
            'canManage'   => KetarunaanPolicy::canManagePelanggaran(),
            'canDecide'   => KetarunaanPolicy::canDecidePelanggaran(),
        ]);
    }

    /**
     * Mencatat progres tindak lanjut atau pengajuan banding tanpa menghapus riwayat lama (PEL-04).
     */
    public function addTindakLanjut(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mencatat tindak lanjut.');
        }

        $fileBukti   = $this->request->getFile('berkas_pendukung');
        $uploadedDoc = null;

        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $ext  = strtolower($fileBukti->getExtension());
            $mime = strtolower((string) $fileBukti->getMimeType());
            $size = $fileBukti->getSize();
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'], true)
                || !in_array($mime, ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'], true)
                || $size > 5 * 1024 * 1024
            ) {
                return redirect()->back()
                    ->with('error', 'Lampiran tindak lanjut harus berformat PDF, JPG, atau PNG maksimal 5 MB.');
            }
            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/pelanggaran';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadedDoc = $fileBukti->getRandomName();
            $fileBukti->move($uploadDir, $uploadedDoc);
        }

        $result = $this->pelanggaranService->addTindakLanjutRecord($id, [
            'tanggal_tindakan'    => $this->request->getPost('tanggal_tindakan'),
            'status_penyelesaian' => $this->request->getPost('status_penyelesaian'),
            'nomor_dasar'         => $this->request->getPost('nomor_dasar'),
            'sanksi_tindakan'     => $this->request->getPost('sanksi_tindakan'),
            'pic'                 => $this->request->getPost('pic'),
            'target_selesai'      => $this->request->getPost('target_selesai'),
            'berkas_pendukung'    => $uploadedDoc,
        ], $this->resolveActorLabel('Tim Pembina'));

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('UPDATE', 'Mencatat tindak lanjut / pemantauan kasus pelanggaran ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id . '?tab=penetapan'))
            ->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Proses validasi pencatatan kasus oleh validator berwenang.
     */
    public function validasi(int $id)
    {
        if (!KetarunaanPolicy::canDecidePelanggaran()) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki wewenang memvalidasi pencatatan kasus.');
        }

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? 'tervalidasi'));
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()
                ->with('error', 'Isi catatan perbaikan sebelum mengembalikan data kasus.');
        }

        $result = $this->pelanggaranService->processValidation($id, $keputusan, $catatan, 'Validator Unit');
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('APPROVE', 'Validasi pencatatan kasus pelanggaran ID #' . $id . ' menjadi ' . $keputusan);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id . '?tab=riwayat'))
            ->with('success', $result['message']);
    }

    /**
     * Hapus draf kasus pelanggaran (khusus draf / perlu perbaikan).
     */
    public function delete(int $id)
    {
        if (!KetarunaanPolicy::canManagePelanggaran()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus kasus.');
        }

        $result = $this->pelanggaranService->deleteDraftKasus($id);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'Menghapus draf kasus pelanggaran ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran'))
            ->with('success', $result['message']);
    }

    /**
     * Unduh / Pratinjau dokumen bukti kasus secara aman dan terproteksi kewenangan (NFR-01 & NFR-02).
     */
    public function unduhDokumen(int $id)
    {
        if (!KetarunaanPolicy::canAccessPelanggaranModule()) {
            return redirect()->to(base_url('ketarunaan/dashboard'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $canViewFull = KetarunaanPolicy::canViewDisciplinaryRecord();
        $detail      = $this->pelanggaranService->getDetail($id, $canViewFull);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data kasus tidak ditemukan.');
        }

        $kasus = $detail['kasus'];
        if (!$canViewFull || !KetarunaanPolicy::canViewRestrictedDocument('pelanggaran', (string) ($kasus['nomor_taruna'] ?? ''))) {
            return $this->response->setStatusCode(403)
                ->setBody('Dokumen kasus bersifat terbatas hanya bagi petugas berwenang.');
        }

        $requested = basename(trim((string) ($this->request->getGet('file') ?? '')));
        $allowed   = array_map(static fn ($d) => basename((string) ($d['nama_berkas'] ?? '')), $detail['dokumen']);
        if (!empty($kasus['bukti_berkas'])) {
            $allowed[] = basename((string) $kasus['bukti_berkas']);
        }
        if (!empty($kasus['berkas_bap'])) {
            $allowed[] = basename((string) $kasus['berkas_bap']);
        }
        foreach (($detail['tindak_lanjut'] ?? []) as $tlItem) {
            if (!empty($tlItem['berkas_pendukung'])) {
                $allowed[] = basename((string) $tlItem['berkas_pendukung']);
            }
        }

        $fileName = ($requested !== '' && in_array($requested, $allowed, true))
            ? $requested
            : basename((string) ($kasus['bukti_berkas'] ?: ('Dokumen_Kasus_' . $kasus['id'] . '.pdf')));

        $inline       = ((int) $this->request->getGet('inline') === 1);
        $physicalPath = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . $fileName;

        if (is_file($physicalPath)) {
            $mime = mime_content_type($physicalPath) ?: 'application/pdf';
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                ->setBody((string) file_get_contents($physicalPath));
        }

        $pdf = ketarunaan_generate_simple_pdf('Dokumen Kasus Pelanggaran Terbatas', (string) $kasus['nomor_perkara'], [
            'Nama Berkas'        => $fileName,
            'Taruna'             => (string) ($kasus['nama_lengkap'] ?? '-') . ' (' . (string) $kasus['nomor_taruna'] . ')',
            'Kategori'           => (string) ($kasus['jenis_pelanggaran'] ?? '-'),
            'Status Penetapan'   => strtoupper((string) ($kasus['status_penetapan'] ?? '-')),
            'Status Penyelesaian'=> strtoupper((string) ($kasus['status_penyelesaian'] ?? '-')),
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
            ->setBody($pdf);
    }

    /**
     * Unduh Ringkasan Resmi Kasus Pelanggaran dalam format PDF (khusus petugas berwenang).
     */
    public function unduhRingkasan(int $id)
    {
        if (!KetarunaanPolicy::canViewDisciplinaryRecord()) {
            return redirect()->back()
                ->with('error', 'Ringkasan kasus hanya dapat diunduh oleh petugas berwenang.');
        }

        $detail = $this->pelanggaranService->getDetail($id, true);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data kasus tidak ditemukan.');
        }

        $k   = $detail['kasus'];
        $pdf = ketarunaan_generate_simple_pdf('Ringkasan Resmi Kasus Pelanggaran', (string) $k['nomor_perkara'], [
            'Taruna'              => (string) ($k['nama_lengkap'] ?? '-') . ' (' . (string) $k['nomor_taruna'] . ')',
            'Program Studi'       => (string) ($k['kode_prodi'] ?? '-') . ' - Angkatan ' . (string) ($k['angkatan'] ?? '-'),
            'Kategori & Tingkat'  => (string) $k['jenis_pelanggaran'] . ' (' . ucwords(str_replace('_', ' ', (string) $k['tingkat_pelanggaran'])) . ')',
            'Tanggal Kejadian'    => substr((string) $k['tanggal_kejadian'], 0, 10),
            'Status Penetapan'    => ucwords(str_replace('_', ' ', (string) $k['status_penetapan'])),
            'Status Penyelesaian' => ucfirst((string) $k['status_penyelesaian']),
            'Nomor Keputusan'     => (string) ($k['nomor_keputusan'] ?: '-'),
            'Status Validasi'     => ucwords(str_replace('_', ' ', (string) $k['status_validasi'])),
        ]);

        $safeSlug = preg_replace('/[^A-Za-z0-9\-_]/', '_', (string) $k['nomor_perkara']);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="Ringkasan_' . $safeSlug . '.pdf"')
            ->setBody($pdf);
    }

    /**
     * Ekspor Terbatas Daftar Kasus Pelanggaran ke CSV dengan proteksi CSV injection.
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canViewDisciplinaryRecord()) {
            return redirect()->to(base_url('ketarunaan/pelanggaran'))
                ->with('error', 'Ekspor terbatas hanya diizinkan bagi petugas berwenang.');
        }

        $filters = [
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'            => trim((string) ($this->request->getGet('prodi_id') ?? '')),
            'tahun'               => trim((string) ($this->request->getGet('tahun') ?? 'all')),
            'status_penetapan'    => trim((string) ($this->request->getGet('status_penetapan') ?? 'all')),
            'status_penyelesaian' => trim((string) ($this->request->getGet('status_penyelesaian') ?? 'all')),
            'status_validasi'     => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
        ];

        $dataDir = $this->pelanggaranService->getDirectoryData($filters, 1, 2000, true);

        if (function_exists('log_audit')) {
            log_audit('EXPORT', 'Ekspor terbatas daftar kasus pelanggaran taruna ECC 2.0');
        }

        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, [
            'Nomor Kasus',
            'Nomor Taruna',
            'Nama Taruna',
            'Prodi',
            'Kategori',
            'Tingkat',
            'Tanggal Kejadian',
            'Penetapan',
            'Penyelesaian',
            'Validasi',
            'Nomor Keputusan',
        ]);

        foreach ($dataDir['rows'] as $row) {
            fputcsv($fh, [
                $this->safeCsv((string) ($row['nomor_perkara'] ?? '-')),
                $this->safeCsv((string) ($row['nomor_taruna'] ?? '-')),
                $this->safeCsv((string) ($row['nama_lengkap'] ?? '-')),
                $this->safeCsv((string) ($row['kode_prodi'] ?? '-')),
                $this->safeCsv((string) ($row['jenis_pelanggaran'] ?? '-')),
                $this->safeCsv(ucwords(str_replace('_', ' ', (string) ($row['tingkat_pelanggaran'] ?? '-')))),
                substr((string) ($row['tanggal_kejadian'] ?? ''), 0, 10),
                $this->safeCsv(ucwords(str_replace('_', ' ', (string) ($row['status_penetapan'] ?? '-')))),
                $this->safeCsv(ucfirst((string) ($row['status_penyelesaian'] ?? '-'))),
                $this->safeCsv(ucwords(str_replace('_', ' ', (string) ($row['status_validasi'] ?? '-')))),
                $this->safeCsv((string) ($row['nomor_keputusan'] ?: '-')),
            ]);
        }

        rewind($fh);
        $csvContent = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="pelanggaran_taruna_ecc2_' . date('Ymd_His') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . (string) $csvContent);
    }

    private function resolveActorLabel(string $fallbackUnit = 'Ketarunaan'): string
    {
        $nama = trim((string) (session()->get('nama_lengkap') ?? ''));
        return $nama !== '' ? $nama : ('PIC ' . $fallbackUnit);
    }

    private function safeCsv(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $trimmed;
        }
        return $trimmed;
    }
}
