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
            'sort_by'             => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'          => strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc'))) === 'asc' ? 'asc' : 'desc',
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
            log_audit('CREATE', 'taruna_pelanggaran', (string) $kasusId, null, [
                'nomor_perkara'     => $this->request->getPost('nomor_perkara'),
                'jenis_pelanggaran' => $this->request->getPost('jenis_pelanggaran'),
            ]);
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
            log_audit('UPDATE', 'taruna_pelanggaran', (string) $id, null, [
                'nomor_perkara'     => $this->request->getPost('nomor_perkara'),
                'jenis_pelanggaran' => $this->request->getPost('jenis_pelanggaran'),
            ]);
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
     * Proses simpan Tahap 2 Pemeriksaan Kasus dan tautan BAP opsional (Opsi A - Google Drive).
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

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Lengkapi seluruh kolom wajib pada data pemeriksaan.');
        }

        $bapUrl = trim((string) ($this->request->getPost('berkas_bap') ?? ''));
        if ($bapUrl !== '' && !ketarunaan_is_http_url($bapUrl, 255)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tautan BAP harus diawali http:// atau https:// (maksimal 255 karakter).');
        }

        $result = $this->pelanggaranService->savePemeriksaan($id, [
            'tanggal_pemeriksaan'   => $this->request->getPost('tanggal_pemeriksaan'),
            'pemeriksa'             => $this->request->getPost('pemeriksa'),
            'dasar_peraturan'       => $this->request->getPost('dasar_peraturan'),
            'tingkat_pelanggaran'   => $this->request->getPost('tingkat_pelanggaran'),
            'ringkasan_klarifikasi' => $this->request->getPost('ringkasan_klarifikasi'),
            'hasil_pemeriksaan'     => $this->request->getPost('hasil_pemeriksaan'),
            'berkas_bap'            => $bapUrl !== '' ? $bapUrl : null,
            'bap_size_kb'           => 0,
        ], $this->resolveActorLabel('Tim Pemeriksa'));

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_pelanggaran', (string) $id, null, [
                'tahap'               => 'pemeriksaan',
                'tingkat_pelanggaran' => $this->request->getPost('tingkat_pelanggaran'),
            ]);
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
            log_audit('CREATE', 'taruna_pelanggaran_pihak', (string) $id, null, [
                'jenis_pihak' => $this->request->getPost('jenis_pihak'),
                'peran'       => $this->request->getPost('peran'),
            ]);
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
            log_audit('DELETE', 'taruna_pelanggaran_pihak', (string) $pihakId, null, [
                'pelanggaran_id' => $id,
            ]);
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
        ], $this->resolveActorLabel('PIC Ketarunaan'), KetarunaanPolicy::canDecidePelanggaran());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_pelanggaran', (string) $id, null, [
                'tahap'               => 'penetapan',
                'status_penetapan'    => $this->request->getPost('status_penetapan'),
                'status_penyelesaian' => $this->request->getPost('status_penyelesaian'),
            ]);
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
     * Menyimpan tautan dokumen bukti (Opsi A - Google Drive) dan opsional mengajukan kasus ke validasi pencatatan.
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

        $dokUrl      = trim((string) ($this->request->getPost('dokumen_url') ?? ''));
        $uploadedDoc = null;

        if ($dokUrl !== '') {
            if (!ketarunaan_is_http_url($dokUrl, 500)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tautan dokumen harus diawali http:// atau https:// (maksimal 500 karakter).');
            }

            $jenisDok    = trim((string) ($this->request->getPost('jenis_dokumen') ?? 'Laporan'));
            $klasifikasi = ($this->request->getPost('klasifikasi') === 'sangat_terbatas') ? 'sangat_terbatas' : 'terbatas';

            $uploadedDoc = [
                'nama_berkas'    => $dokUrl,
                'jenis_dokumen'  => in_array($jenisDok, ['Laporan', 'Pemeriksaan', 'Penetapan', 'Pendukung'], true) ? $jenisDok : 'Pendukung',
                'klasifikasi'    => $klasifikasi,
                'tanggal_unggah' => date('Y-m-d'),
                'ukuran_kb'      => 0,
            ];
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'draf'));
        if (!in_array($aksiSubmit, ['draf', 'diajukan'], true)) {
            $aksiSubmit = 'draf';
        }

        $result = $this->pelanggaranService->saveDokumenAndSubmit($id, [
            'uploaded_doc' => $uploadedDoc,
            'aksi_submit'  => $aksiSubmit,
            'pelaku'       => $this->resolveActorLabel((string) ($detail['kasus']['unit_pemilik'] ?? 'Ketarunaan')),
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_pelanggaran', (string) $id, null, [
                'tahap'       => 'dokumen',
                'aksi_submit' => $aksiSubmit,
                'has_new_doc' => $uploadedDoc !== null,
            ]);
        }

        if ($aksiSubmit === 'diajukan') {
            return redirect()->to(base_url('ketarunaan/pelanggaran/detail/' . $id))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/pelanggaran/dokumen/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menghapus satu tautan/berkas bukti kasus.
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
            log_audit('DELETE', 'taruna_pelanggaran', (string) $id, null, [
                'dokumen_dihapus' => $filename,
            ]);
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

        $rules = [
            'tanggal_tindakan'    => 'required|valid_date[Y-m-d]',
            'status_penyelesaian' => 'required|in_list[proses,selesai,banding]',
            'sanksi_tindakan'     => 'required|max_length[1000]',
            'nomor_dasar'         => 'permit_empty|max_length[100]',
            'pic'                 => 'permit_empty|max_length[120]',
            'target_selesai'      => 'permit_empty|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('error', 'Lengkapi tanggal tindakan, status penyelesaian, dan uraian tindak lanjut dengan benar.');
        }

        $tglTindakan   = trim((string) ($this->request->getPost('tanggal_tindakan') ?? ''));
        $targetSelesai = trim((string) ($this->request->getPost('target_selesai') ?? ''));
        if ($targetSelesai !== '' && $targetSelesai < $tglTindakan) {
            return redirect()->back()
                ->with('error', 'Target selesai tidak boleh lebih awal dari tanggal tindakan.');
        }

        $lampiranUrl = trim((string) ($this->request->getPost('berkas_pendukung') ?? ''));
        if ($lampiranUrl !== '' && !ketarunaan_is_http_url($lampiranUrl, 255)) {
            return redirect()->back()
                ->with('error', 'Tautan lampiran tindak lanjut harus diawali http:// atau https:// (maksimal 255 karakter).');
        }

        $result = $this->pelanggaranService->addTindakLanjutRecord($id, [
            'tanggal_tindakan'    => $tglTindakan,
            'status_penyelesaian' => $this->request->getPost('status_penyelesaian'),
            'nomor_dasar'         => $this->request->getPost('nomor_dasar'),
            'sanksi_tindakan'     => $this->request->getPost('sanksi_tindakan'),
            'pic'                 => $this->request->getPost('pic'),
            'target_selesai'      => $targetSelesai !== '' ? $targetSelesai : null,
            'berkas_pendukung'    => $lampiranUrl !== '' ? $lampiranUrl : null,
        ], $this->resolveActorLabel('Tim Pembina'));

        if ($result['status'] && function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_pelanggaran_tindak_lanjut', (string) $id, null, [
                'status_penyelesaian' => $this->request->getPost('status_penyelesaian'),
                'tanggal_tindakan'    => $tglTindakan,
            ]);
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

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? ''));
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if (!in_array($keputusan, ['tervalidasi', 'perlu_perbaikan'], true)) {
            return redirect()->back()
                ->with('error', 'Keputusan validasi tidak valid.');
        }

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()
                ->with('error', 'Isi catatan perbaikan sebelum mengembalikan data kasus.');
        }

        $validatorLabel = $this->resolveActorLabel('Validator');
        $result = $this->pelanggaranService->processValidation($id, $keputusan, $catatan, $validatorLabel);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $auditAction = ($keputusan === 'tervalidasi') ? 'APPROVE' : 'RETURN_PELANGGARAN';
            log_audit($auditAction, 'taruna_pelanggaran', (string) $id, null, [
                'keputusan' => $keputusan,
                'catatan'   => $catatan,
            ]);
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
            log_audit('DELETE', 'taruna_pelanggaran', (string) $id, null, [
                'aksi' => 'hapus_draf_kasus',
            ]);
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
            return $this->response->setStatusCode(403, 'Akses tidak diizinkan.')
                ->setBody('Akses ditolak. Dokumen kasus hanya untuk pejabat berwenang.');
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

        $requestedRaw = trim((string) ($this->request->getGet('file') ?? ''));
        $allowedItems = [];
        foreach ($detail['dokumen'] as $d) {
            $raw = trim((string) ($d['nama_berkas'] ?? ''));
            if ($raw !== '') {
                $allowedItems[] = $raw;
            }
        }
        foreach (['bukti_berkas', 'berkas_bap'] as $col) {
            $raw = trim((string) ($kasus[$col] ?? ''));
            if ($raw !== '') {
                $allowedItems[] = $raw;
            }
        }
        foreach (($detail['tindak_lanjut'] ?? []) as $tlItem) {
            $raw = trim((string) ($tlItem['berkas_pendukung'] ?? ''));
            if ($raw !== '') {
                $allowedItems[] = $raw;
            }
        }
        $allowedItems = array_values(array_unique($allowedItems));

        $matchedTarget = '';
        if ($requestedRaw !== '') {
            foreach ($allowedItems as $item) {
                if ($item === $requestedRaw || (!ketarunaan_is_http_url($item, 2000) && basename($item) === basename($requestedRaw))) {
                    $matchedTarget = $item;
                    break;
                }
            }
        } elseif (!empty($kasus['bukti_berkas'])) {
            $matchedTarget = trim((string) $kasus['bukti_berkas']);
        }

        if ($matchedTarget === '') {
            return redirect()->back()->with('error', 'Dokumen bukti kasus tidak ditemukan.');
        }

        if (ketarunaan_is_http_url($matchedTarget, 2000)) {
            return redirect()->to($matchedTarget);
        }

        $fileName     = basename($matchedTarget);
        $inline       = ((int) $this->request->getGet('inline') === 1);
        $physicalPath = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . $fileName;

        if (is_file($physicalPath)) {
            $mime = mime_content_type($physicalPath) ?: 'application/pdf';
            return $this->response
                ->setHeader('X-Content-Type-Options', 'nosniff')
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                ->setBody((string) file_get_contents($physicalPath));
        }

        return redirect()->back()->with('error', 'Berkas dokumen tidak tersedia di server.');
    }

    /**
     * Unduh Ringkasan Resmi Kasus Pelanggaran dalam format PDF (khusus petugas berwenang).
     */
    public function unduhRingkasan(int $id)
    {
        if (!KetarunaanPolicy::canViewDisciplinaryRecord()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Ringkasan kasus hanya dapat diunduh oleh petugas berwenang.');
        }

        $detail = $this->pelanggaranService->getDetail($id, true);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data kasus tidak ditemukan.');
        }

        $k      = $detail['kasus'];
        $angLbl = !empty($k['angkatan'])
            ? ketarunaan_format_angkatan([
                'tahun_masuk' => $k['angkatan'],
                'kode_romawi' => $k['angkatan_romawi'] ?? '',
            ])
            : '-';
        $pdf = ketarunaan_generate_simple_pdf('Ringkasan Resmi Kasus Pelanggaran', (string) $k['nomor_perkara'], [
            'Taruna'              => (string) ($k['nama_lengkap'] ?? '-') . ' (' . (string) $k['nomor_taruna'] . ')',
            'Program Studi'       => (string) ($k['kode_prodi'] ?? '-') . ' - Angkatan ' . $angLbl,
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
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Ekspor terbatas hanya diizinkan bagi petugas berwenang.');
        }

        $filters = [
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'            => trim((string) ($this->request->getGet('prodi_id') ?? '')),
            'tahun'               => trim((string) ($this->request->getGet('tahun') ?? 'all')),
            'status_penetapan'    => trim((string) ($this->request->getGet('status_penetapan') ?? 'all')),
            'status_penyelesaian' => trim((string) ($this->request->getGet('status_penyelesaian') ?? 'all')),
            'status_validasi'     => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
            'sort_by'             => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'          => strtolower(trim((string) ($this->request->getGet('sort_order') ?? 'desc'))) === 'asc' ? 'asc' : 'desc',
        ];

        $dataDir = $this->pelanggaranService->getDirectoryData($filters, 1, 0, true);

        if (function_exists('log_audit')) {
            log_audit('EXPORT_CSV_PELANGGARAN', 'taruna_pelanggaran', 'all', null, [
                'total_rows' => count($dataDir['rows']),
                'filters'    => $filters,
            ]);
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
                ketarunaan_csv_safe((string) ($row['nomor_perkara'] ?? '-')),
                ketarunaan_csv_safe((string) ($row['nomor_taruna'] ?? '-')),
                ketarunaan_csv_safe((string) ($row['nama_lengkap'] ?? '-')),
                ketarunaan_csv_safe((string) ($row['kode_prodi'] ?? '-')),
                ketarunaan_csv_safe((string) ($row['jenis_pelanggaran'] ?? '-')),
                ketarunaan_csv_safe(ucwords(str_replace('_', ' ', (string) ($row['tingkat_pelanggaran'] ?? '-')))),
                ketarunaan_csv_safe(substr((string) ($row['tanggal_kejadian'] ?? ''), 0, 10)),
                ketarunaan_csv_safe(ucwords(str_replace('_', ' ', (string) ($row['status_penetapan'] ?? '-')))),
                ketarunaan_csv_safe(ucfirst((string) ($row['status_penyelesaian'] ?? '-'))),
                ketarunaan_csv_safe(ucwords(str_replace('_', ' ', (string) ($row['status_validasi'] ?? '-')))),
                ketarunaan_csv_safe((string) ($row['nomor_keputusan'] ?: '-')),
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
}
