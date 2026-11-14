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

        $filters = [
            'q'       => trim((string) $this->request->getGet('q')),
            'jenis'   => trim((string) ($this->request->getGet('jenis') ?? 'all')),
            'tingkat' => trim((string) ($this->request->getGet('tingkat') ?? 'all')),
            'prodi'   => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'periode' => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'status'  => trim((string) ($this->request->getGet('status') ?? 'all')),
        ];
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);
        if (!in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $refs = $this->prestasiService->getReferenceOptions();
        $dir  = $this->prestasiService->getDirectoryData($filters, $page, $perPage);

        return view('ketarunaan/prestasi/index', [
            'title'           => 'Prestasi Taruna - Evidence Command Center (ECC)',
            'filters'         => $filters,
            'refs'            => $refs,
            'metrics'         => $dir['metrics'],
            'rows'            => $dir['rows'],
            'pagination'      => $dir['pagination'],
            'canManageTaruna' => KetarunaanPolicy::canManageTaruna(),
            'canValidate'     => KetarunaanPolicy::canValidate(),
        ]);
    }

    /**
     * Halaman Tahap 1: Formulir Tambah Informasi Prestasi.
     */
    public function create()
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data prestasi.');
        }

        return view('ketarunaan/prestasi/form_informasi', [
            'title'    => 'Tambah Prestasi - Evidence Command Center (ECC)',
            'mode'     => 'create',
            'prestasi' => null,
            'refs'     => $this->prestasiService->getReferenceOptions(),
        ]);
    }

    /**
     * Proses simpan Tahap 1 untuk prestasi baru.
     */
    public function store()
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data prestasi.');
        }

        $rules = [
            'nama_prestasi'       => 'required|min_length[3]|max_length[200]',
            'jenis_prestasi'      => 'required|in_list[akademik,non_akademik,olahraga,seni]',
            'tingkat_prestasi'    => 'required|in_list[internal,daerah,nasional,internasional]',
            'peringkat'           => 'required|max_length[50]',
            'penyelenggara'       => 'required|max_length[150]',
            'lokasi_kegiatan'     => 'required|max_length[150]',
            'tanggal_mulai'       => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'     => 'required|valid_date[Y-m-d]',
            'jenis_keikutsertaan' => 'required|in_list[individu,tim]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi seluruh kolom wajib pada informasi prestasi.');
        }

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));
        $result = $this->prestasiService->saveInformasi([
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
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $newId = (int) ($result['id'] ?? 0);
        if (function_exists('log_audit')) {
            log_audit('CREATE', 'Menambahkan draf prestasi baru ID #' . $newId);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $newId))
            ->with('success', 'Informasi prestasi disimpan. Silakan tambahkan peserta.');
    }

    /**
     * Halaman Tahap 1 (Mode Ubah / Koreksi): Informasi Prestasi.
     */
    public function edit(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengubah data prestasi.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        return view('ketarunaan/prestasi/form_informasi', [
            'title'    => 'Ubah Informasi Prestasi - Evidence Command Center (ECC)',
            'mode'     => 'edit',
            'prestasi' => $detail['prestasi'],
            'refs'     => $this->prestasiService->getReferenceOptions(),
        ]);
    }

    /**
     * Proses simpan perubahan Tahap 1 Informasi Prestasi.
     */
    public function update(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengubah data prestasi.');
        }

        $rules = [
            'nama_prestasi'       => 'required|min_length[3]|max_length[200]',
            'jenis_prestasi'      => 'required|in_list[akademik,non_akademik,olahraga,seni]',
            'tingkat_prestasi'    => 'required|in_list[internal,daerah,nasional,internasional]',
            'peringkat'           => 'required|max_length[50]',
            'penyelenggara'       => 'required|max_length[150]',
            'lokasi_kegiatan'     => 'required|max_length[150]',
            'tanggal_mulai'       => 'required|valid_date[Y-m-d]',
            'tanggal_selesai'     => 'required|valid_date[Y-m-d]',
            'jenis_keikutsertaan' => 'required|in_list[individu,tim]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi seluruh kolom wajib pada informasi prestasi.');
        }

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));
        $result = $this->prestasiService->saveInformasi([
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
        ], $id);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui informasi prestasi ID #' . $id);
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
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola peserta prestasi.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        return view('ketarunaan/prestasi/kelola_peserta', [
            'title'      => 'Kelola Peserta Prestasi - Evidence Command Center (ECC)',
            'detail'     => $detail,
            'refs'       => $this->prestasiService->getReferenceOptions(),
            'tarunaList' => $this->prestasiService->getTarunaSelectList(),
        ]);
    }

    /**
     * Tambah peserta taruna ke suatu prestasi.
     */
    public function addPeserta(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola peserta prestasi.');
        }

        $nomorTaruna = trim((string) $this->request->getPost('nomor_taruna'));
        $peran       = trim((string) ($this->request->getPost('peran') ?? 'Anggota'));

        if ($nomorTaruna === '') {
            return redirect()->back()->with('error', 'Pilih taruna terlebih dahulu sebelum menambahkan peserta.');
        }

        $result = $this->prestasiService->addPeserta($id, $nomorTaruna, $peran);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Menambahkan peserta ' . $nomorTaruna . ' pada prestasi ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Hapus peserta taruna dari suatu prestasi.
     */
    public function removePeserta(int $id, int $pesertaId)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus peserta prestasi.');
        }

        $result = $this->prestasiService->removePeserta($id, $pesertaId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'Menghapus peserta ID #' . $pesertaId . ' dari prestasi ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/peserta/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Tahap 3: Dokumen Bukti & Pemeriksaan Pengajuan.
     */
    public function dokumen(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang mengelola dokumen bukti prestasi.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
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
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola dokumen bukti prestasi.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Data prestasi tidak ditemukan.');
        }

        $rules = [
            'nomor_sertifikat' => 'required|min_length[2]|max_length[100]',
        ];

        $fileBukti = $this->request->getFile('bukti_berkas');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $rules['bukti_berkas'] = 'max_size[bukti_berkas,10240]|ext_in[bukti_berkas,pdf]|mime_in[bukti_berkas,application/pdf]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Nomor sertifikat wajib diisi dan bukti utama harus berformat PDF maksimal 10 MB.');
        }

        $uploadedBuktiName = null;
        $uploadedSizeKb    = (int) ($detail['prestasi']['ukuran_berkas_kb'] ?? 420);

        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/prestasi';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadedBuktiName = $fileBukti->getRandomName();
            $uploadedSizeKb    = max(1, (int) round($fileBukti->getSize() / 1024));
            $fileBukti->move($uploadDir, $uploadedBuktiName);

            if (!empty($detail['prestasi']['bukti_berkas'])) {
                $oldFile = $uploadDir . '/' . basename((string) $detail['prestasi']['bukti_berkas']);
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
        }

        // Proses foto dokumentasi opsional (maks 5 file JPG/PNG @ 5 MB)
        $newFotos = [];
        $filesDok = $this->request->getFileMultiple('dokumentasi_files');
        if (is_array($filesDok)) {
            $dokDir = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi';
            $sisaSlot = max(0, 5 - count($detail['dokumentasi']));

            foreach ($filesDok as $foto) {
                if ($sisaSlot <= 0) {
                    break;
                }
                if (!$foto || !$foto->isValid() || $foto->hasMoved()) {
                    continue;
                }
                $ext  = strtolower($foto->getExtension());
                $mime = strtolower((string) $foto->getMimeType());
                $size = $foto->getSize();
                if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)
                    || !in_array($mime, ['image/jpeg', 'image/jpg', 'image/png'], true)
                    || $size > 5 * 1024 * 1024
                ) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Berkas dokumentasi harus berformat JPG/PNG dan maksimal 5 MB per berkas.');
                }

                if (!is_dir($dokDir)) {
                    mkdir($dokDir, 0755, true);
                }
                $randomFotoName = $foto->getRandomName();
                $foto->move($dokDir, $randomFotoName);
                $newFotos[] = $randomFotoName;
                $sisaSlot--;
            }
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'draf'));
        $result = $this->prestasiService->saveDokumenAndSubmit($id, [
            'nomor_sertifikat' => $this->request->getPost('nomor_sertifikat'),
            'bukti_berkas'     => $uploadedBuktiName,
            'ukuran_berkas_kb' => $uploadedSizeKb,
            'dokumentasi_baru' => $newFotos,
            'aksi_submit'      => $aksiSubmit,
            'pelaku'           => $this->resolveActorLabel((string) ($detail['prestasi']['unit_pemilik'] ?? 'Ketarunaan')),
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui dokumen bukti prestasi ID #' . $id . ' (' . $aksiSubmit . ')');
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
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus dokumentasi.');
        }

        $file = trim((string) $this->request->getPost('filename'));
        $result = $this->prestasiService->removeDokumentasi($id, $file);

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

        $tab = strtolower(trim((string) ($this->request->getGet('tab') ?? 'ringkasan')));
        if (!in_array($tab, ['ringkasan', 'peserta', 'dokumen', 'riwayat'], true)) {
            $tab = 'ringkasan';
        }

        return view('ketarunaan/prestasi/detail', [
            'title'           => 'Detail Prestasi - Evidence Command Center (ECC)',
            'active_tab'      => $tab,
            'detail'          => $detail,
            'canManageTaruna' => KetarunaanPolicy::canManageTaruna(),
            'canValidate'     => KetarunaanPolicy::canValidate(),
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

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? 'tervalidasi'));
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()
                ->with('error', 'Mohon isi catatan perbaikan sebelum mengembalikan data.');
        }

        $result = $this->prestasiService->processValidation($id, $keputusan, $catatan, 'Validator Unit');
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('APPROVE', 'Validasi prestasi ID #' . $id . ' menjadi ' . $keputusan);
        }

        return redirect()->to(base_url('ketarunaan/prestasi/detail/' . $id . '?tab=riwayat'))
            ->with('success', $result['message']);
    }

    /**
     * Hapus draf prestasi (hanya untuk draf/perlu perbaikan).
     */
    public function delete(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/prestasi'))
                ->with('error', 'Anda tidak memiliki wewenang menghapus data prestasi.');
        }

        $result = $this->prestasiService->deleteDraftPrestasi($id);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'Menghapus draf prestasi ID #' . $id);
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
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data prestasi tidak ditemukan.');
        }

        $prestasi = $detail['prestasi'];
        $jenis    = trim((string) ($this->request->getGet('jenis') ?? 'utama'));
        $inline   = ((int) $this->request->getGet('inline') === 1);

        if ($jenis === 'foto') {
            $fotoName = basename((string) ($this->request->getGet('file') ?? ''));
            if ($fotoName !== '' && in_array($fotoName, $detail['dokumentasi'], true)) {
                $physicalFoto = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . $fotoName;
                if (is_file($physicalFoto)) {
                    $mime = mime_content_type($physicalFoto) ?: 'image/jpeg';
                    return $this->response
                        ->setHeader('Content-Type', $mime)
                        ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fotoName . '"')
                        ->setBody((string) file_get_contents($physicalFoto));
                }
            }
            $pdfFoto = ketarunaan_generate_simple_pdf('Dokumentasi Kegiatan Prestasi', $prestasi['nama_prestasi'], [
                'Nama Berkas'   => $fotoName ?: 'Foto_Kegiatan.jpg',
                'Penyelenggara' => (string) $prestasi['penyelenggara'],
            ]);
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="Dokumentasi_Kegiatan.pdf"')
                ->setBody($pdfFoto);
        }

        $fileName = basename((string) ($prestasi['bukti_berkas'] ?: 'Piagam_Prestasi.pdf'));
        $physicalPath = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $fileName;
        if (is_file($physicalPath)) {
            $mime = mime_content_type($physicalPath) ?: 'application/pdf';
            return $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                ->setBody((string) file_get_contents($physicalPath));
        }

        $pesertaRingkas = array_map(static fn ($p) => $p['nomor_taruna'] . ' - ' . $p['nama_lengkap'], $detail['peserta']);
        $pdfContent = ketarunaan_generate_simple_pdf('Bukti Utama Prestasi Taruna', (string) $prestasi['nama_prestasi'], [
            'Tingkat / Peringkat' => ucfirst((string) $prestasi['tingkat_prestasi']) . ' - ' . $prestasi['peringkat'],
            'Keikutsertaan'       => ucfirst((string) $prestasi['jenis_keikutsertaan']) . (!empty($prestasi['nama_tim']) ? ' (' . $prestasi['nama_tim'] . ')' : ''),
            'Nomor Sertifikat'    => (string) ($prestasi['nomor_sertifikat'] ?: '-'),
            'Peserta'             => !empty($pesertaRingkas) ? implode('; ', array_slice($pesertaRingkas, 0, 3)) : '-',
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
            ->setBody($pdfContent);
    }

    /**
     * Unduh Ringkasan Prestasi dalam format PDF 1 halaman.
     */
    public function unduhRingkasan(int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $detail = $this->prestasiService->getDetail($id);
        if (!$detail) {
            return redirect()->back()->with('error', 'Data prestasi tidak ditemukan.');
        }

        $p = $detail['prestasi'];
        $statusLabel = ketarunaan_validasi_meta((string) $p['status_validasi'])['label'];

        $pdf = ketarunaan_generate_simple_pdf('Ringkasan Data Prestasi Taruna', (string) $p['nama_prestasi'], [
            'Jenis / Bidang'      => ucfirst((string) $p['jenis_prestasi']) . ' / ' . ($p['bidang_prestasi'] ?: '-'),
            'Tingkat / Peringkat' => ucfirst((string) $p['tingkat_prestasi']) . ' / ' . $p['peringkat'],
            'Penyelenggara'       => (string) $p['penyelenggara'],
            'Lokasi & Tanggal'    => ($p['lokasi_kegiatan'] ?: '-') . ' (' . $p['tanggal_mulai'] . ')',
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
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $filters = [
            'q'       => trim((string) $this->request->getGet('q')),
            'jenis'   => trim((string) ($this->request->getGet('jenis') ?? 'all')),
            'tingkat' => trim((string) ($this->request->getGet('tingkat') ?? 'all')),
            'prodi'   => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'periode' => trim((string) ($this->request->getGet('periode') ?? 'all')),
            'status'  => trim((string) ($this->request->getGet('status') ?? 'all')),
        ];

        $dir = $this->prestasiService->getDirectoryData($filters, 1, 1000);

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
            $statusLabel = ketarunaan_validasi_meta(
                $r['status_validasi'] === 'perlu_perbaikan' ? 'dikembalikan' : (string) $r['status_validasi']
            )['label'];
            $lines[] = implode(',', [
                '"' . str_replace('"', '""', (string) $r['nama_prestasi']) . '"',
                '"' . str_replace('"', '""', ucfirst((string) $r['jenis_prestasi'])) . '"',
                '"' . str_replace('"', '""', ucfirst((string) $r['tingkat_prestasi'])) . '"',
                '"' . str_replace('"', '""', (string) $r['peringkat']) . '"',
                '"' . str_replace('"', '""', ucfirst((string) $r['jenis_keikutsertaan'])) . '"',
                (int) $r['jumlah_peserta'],
                '"' . str_replace('"', '""', (string) $r['tanggal_mulai']) . '"',
                '"' . str_replace('"', '""', $statusLabel) . '"',
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
     * Menentukan label peran pelaku berdasarkan unit pemilik.
     */
    private function resolveActorLabel(string $unitPemilik): string
    {
        return match ($unitPemilik) {
            'Pusbangkar'    => 'PIC Pusbangkar',
            'Program Studi' => 'PIC Prodi',
            default         => 'PIC Ketarunaan',
        };
    }
}
