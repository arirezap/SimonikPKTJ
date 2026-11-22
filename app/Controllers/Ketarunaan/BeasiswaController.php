<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\BeasiswaService;
use App\Libraries\Ketarunaan\KetarunaanPolicy;

/**
 * Controller Modul 5: Beasiswa Taruna (ECC 2.0).
 * Menegakkan pemisahan wewenang (Pengelola, Verifikator, Pejabat Penetap, Taruna),
 * kendali berkas ekonomi terbatas (HTTP 403), serta isolasi alur Apresiasi & Klaim.
 */
class BeasiswaController extends BaseController
{
    protected BeasiswaService $beasiswaService;

    public function __construct(?BeasiswaService $beasiswaService = null)
    {
        $this->beasiswaService = $beasiswaService ?? new BeasiswaService();
        helper(['ketarunaan', 'form', 'audit']);
    }

    /**
     * Halaman Ringkasan Beasiswa (/ketarunaan/beasiswa).
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Beasiswa.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;
        $refs         = $this->beasiswaService->getReferenceOptions(false, false);

        $rawTahun = trim((string) ($this->request->getGet('tahun_akademik_id') ?? ''));
        $tahunId  = ($rawTahun !== '' && $rawTahun !== 'all') ? (int) $rawTahun : null;

        $ringkasan = $this->beasiswaService->getRingkasanData($tahunId ?: null, $viewerNim);

        return view('ketarunaan/beasiswa/index', [
            'title'         => 'Beasiswa Taruna - Evidence Command Center (ECC)',
            'activeTab'     => 'ringkasan',
            'refs'          => $refs,
            'tahunId'       => $tahunId ? (string) $tahunId : 'all',
            'ringkasan'     => $ringkasan,
            'isSelfTaruna'  => $isSelfTaruna,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Daftar Program Beasiswa & Pengaturan Kuota (/ketarunaan/beasiswa/program).
     */
    public function program()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $filters = [
            'jenis_beasiswa'    => trim((string) ($this->request->getGet('jenis_beasiswa') ?? 'all')),
            'status_program'    => trim((string) ($this->request->getGet('status_program') ?? 'all')),
            'tahun_akademik_id' => trim((string) ($this->request->getGet('tahun_akademik_id') ?? 'all')),
            'q'                 => trim((string) ($this->request->getGet('q') ?? '')),
            'sort_by'           => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'        => trim((string) ($this->request->getGet('sort_order') ?? 'asc')),
        ];

        $programs = $this->beasiswaService->listPrograms($filters);

        return view('ketarunaan/beasiswa/program', [
            'title'        => 'Program Beasiswa - Evidence Command Center (ECC)',
            'activeTab'    => 'program',
            'filters'      => $filters,
            'programs'     => $programs,
            'refs'         => $this->beasiswaService->getReferenceOptions(false, false),
            'isSelfTaruna' => $this->isSelfTarunaOnly(),
            'perms'        => $this->getPermissions(),
        ]);
    }

    /**
     * Form Tambah / Ubah Program Beasiswa & Aturan Seleksi Apresiasi.
     */
    public function kelolaProgram(?int $id = null)
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/program'))
                ->with('error', 'Hanya pengelola ketarunaan yang berwenang mengatur program beasiswa.');
        }

        $program = null;
        if ($id !== null && $id > 0) {
            $program = $this->beasiswaService->getProgram($id);
            if (!$program) {
                return redirect()->to(base_url('ketarunaan/beasiswa/program'))
                    ->with('error', 'Program beasiswa tidak ditemukan.');
            }
        }

        return view('ketarunaan/beasiswa/program_form', [
            'title'        => ($program ? 'Ubah Program Beasiswa' : 'Tambah Program Beasiswa') . ' - Evidence Command Center (ECC)',
            'activeTab'    => 'program',
            'program'      => $program,
            'refs'         => $this->beasiswaService->getReferenceOptions(false, false),
            'isSelfTaruna' => false,
            'perms'        => $this->getPermissions(),
        ]);
    }

    /**
     * Proses simpan atau pembaruan program beasiswa.
     */
    public function saveProgram()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/program'))
                ->with('error', 'Anda tidak memiliki wewenang mengelola program beasiswa.');
        }

        $id = (int) ($this->request->getPost('id') ?? 0);

        $rules = [
            'nama_program'      => 'required|max_length[150]',
            'jenis_beasiswa'    => 'required|in_list[apresiasi,prestasi,daerah_3tp,tidak_mampu]',
            'pemberi'           => 'required|max_length[150]',
            'tahun_akademik_id' => 'required|is_natural_no_zero',
            'kuota'             => 'required|is_natural',
            'status_program'    => 'required|in_list[dibuka,seleksi,berjalan,selesai]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Periksa kembali data program, jenis beasiswa, periode, dan kuota.');
        }

        $oldProgram = ($id > 0) ? $this->beasiswaService->getProgram($id) : null;
        $payload = [
            'nama_program'      => $this->request->getPost('nama_program'),
            'jenis_beasiswa'    => $this->request->getPost('jenis_beasiswa'),
            'pemberi'           => $this->request->getPost('pemberi'),
            'tahun_akademik_id' => $this->request->getPost('tahun_akademik_id'),
            'periode_mulai'     => $this->request->getPost('periode_mulai'),
            'periode_selesai'   => $this->request->getPost('periode_selesai'),
            'kuota'             => $this->request->getPost('kuota'),
            'nominal'           => $this->request->getPost('nominal'),
            'persyaratan'       => $this->request->getPost('persyaratan'),
            'penanggung_jawab'  => $this->request->getPost('penanggung_jawab'),
            'periode_nilai_id'  => $this->request->getPost('periode_nilai_id'),
            'jumlah_peringkat'  => $this->request->getPost('jumlah_peringkat'),
            'aturan_seri'       => $this->request->getPost('aturan_seri'),
            'status_program'    => $this->request->getPost('status_program'),
        ];

        $result = $this->beasiswaService->saveProgram($payload, $id > 0 ? $id : null);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $aksi = $id > 0 ? 'UPDATE' : 'CREATE';
            log_audit($aksi, 'taruna_program_beasiswa', (string) $result['id'], $oldProgram, [
                'nama_program'   => $payload['nama_program'],
                'jenis_beasiswa' => $payload['jenis_beasiswa'],
                'kuota'          => $payload['kuota'],
                'status_program' => $payload['status_program'],
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/program'))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Seleksi Beasiswa Apresiasi (IP Semester per Kelas & Penanganan Seri).
     */
    public function apresiasi()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule() || $this->isSelfTarunaOnly()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Halaman pemeringkatan Apresiasi hanya untuk petugas dan pimpinan.');
        }

        $programId = (int) ($this->request->getGet('program_id') ?? 0);
        $periodeId = (int) ($this->request->getGet('periode_nilai_id') ?? 0);
        $prodiId   = (int) ($this->request->getGet('prodi_id') ?? 0);
        $kelasId   = (int) ($this->request->getGet('kelas_id') ?? 0);

        $data = $this->beasiswaService->getApresiasiData(
            $programId > 0 ? $programId : null,
            $periodeId > 0 ? $periodeId : null,
            $prodiId > 0 ? $prodiId : null,
            $kelasId > 0 ? $kelasId : null
        );

        $previewSession = session()->get('beasiswa_ip_preview');

        return view('ketarunaan/beasiswa/apresiasi', [
            'title'         => 'Seleksi Beasiswa Apresiasi - Evidence Command Center (ECC)',
            'activeTab'     => 'apresiasi',
            'data'          => $data,
            'refs'          => $this->beasiswaService->getReferenceOptions(false, false),
            'prodiId'       => $prodiId,
            'ipPreview'     => is_array($previewSession) ? $previewSession : null,
            'isSelfTaruna'  => false,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Menghitung ulang peringkat Beasiswa Apresiasi berbasis IP semester per kelas.
     */
    public function hitungApresiasi()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/apresiasi'))
                ->with('error', 'Anda tidak memiliki wewenang memproses pemeringkatan Apresiasi.');
        }

        $programId = (int) ($this->request->getPost('program_id') ?? 0);
        $periodeId = (int) ($this->request->getPost('periode_nilai_id') ?? 0);
        $kelasId   = (int) ($this->request->getPost('kelas_id') ?? 0);

        $result = $this->beasiswaService->hitungPemeringkatanApresiasi(
            $programId,
            $periodeId,
            $kelasId,
            $this->resolveActorLabel('Ketarunaan')
        );

        $redirectUrl = base_url('ketarunaan/beasiswa/apresiasi?program_id=' . $programId . '&periode_nilai_id=' . $periodeId . '&kelas_id=' . $kelasId);

        if (!$result['status']) {
            return redirect()->to($redirectUrl)->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_beasiswa_pemeringkatan', (string) $result['id'], null, [
                'program_beasiswa_id' => $programId,
                'tahun_akademik_id'   => $periodeId,
                'kelas_id'            => $kelasId,
                'held'                => !empty($result['held']),
            ]);
        }

        $flashType = !empty($result['held']) ? 'warning' : 'success';
        return redirect()->to($redirectUrl)->with($flashType, $result['message']);
    }

    /**
     * Mengajukan kandidat hasil pemeringkatan Apresiasi ke antrean verifikasi dan penetapan.
     */
    public function ajukanApresiasi(int $pemeringkatanId)
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/apresiasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengajukan kandidat pemeringkatan.');
        }

        $result = $this->beasiswaService->ajukanPemeringkatanApresiasi(
            $pemeringkatanId,
            $this->resolveActorLabel('Ketarunaan')
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_beasiswa_pemeringkatan', (string) $pemeringkatanId, null, [
                'aksi' => 'ajukan_kandidat_apresiasi',
            ]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Memeriksa dan menampilkan pratinjau CSV Nilai IP Semester (valid vs invalid).
     */
    public function previewIpCsv()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/apresiasi'))
                ->with('error', 'Anda tidak memiliki wewenang mengimpor nilai IP semester.');
        }

        $periodeId = (int) ($this->request->getPost('periode_nilai_id') ?? 0);
        $rules     = [
            'periode_nilai_id' => 'required|integer',
            'csv_nilai_ip'     => 'uploaded[csv_nilai_ip]|max_size[csv_nilai_ip,2048]|ext_in[csv_nilai_ip,csv,txt]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Unggah berkas CSV yang valid maksimal 2 MB dan pilih periode nilai.');
        }

        $file = $this->request->getFile('csv_nilai_ip');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Berkas CSV gagal dibaca.');
        }

        $preview = $this->beasiswaService->previewIpCsv($file->getTempName(), $periodeId);
        if (!$preview['status']) {
            return redirect()->back()->with('error', $preview['message']);
        }

        $token = bin2hex(random_bytes(12));
        $compactValidRows = array_map(static function (array $r): array {
            return [
                'nomor_taruna'     => (string) ($r['nomor_taruna'] ?? ''),
                'program_studi_id' => (int) ($r['program_studi_id'] ?? 0),
                'kelas_id'         => (int) ($r['kelas_id'] ?? 0),
                'tingkat'          => (int) ($r['tingkat'] ?? 2),
                'semester'         => (int) ($r['semester'] ?? 4),
                'ip_semester'      => (float) ($r['ip_semester'] ?? 0),
                'sks_lulus'        => (int) ($r['sks_lulus'] ?? 20),
            ];
        }, (array) ($preview['valid_rows'] ?? []));

        $previewSessionPayload = [
            'token'             => $token,
            'tahun_akademik_id' => (int) ($preview['tahun_akademik_id'] ?? $periodeId),
            'valid_count'       => (int) ($preview['valid_count'] ?? 0),
            'invalid_count'     => (int) ($preview['invalid_count'] ?? 0),
            'rows'              => array_slice((array) ($preview['rows'] ?? []), 0, 150),
            'valid_rows'        => $compactValidRows,
        ];
        session()->set('beasiswa_ip_preview', $previewSessionPayload);

        return redirect()->back()
            ->with('success', sprintf(
                'Pratinjau selesai: %d baris valid dan %d baris ditandai perlu perbaikan.',
                (int) $preview['valid_count'],
                (int) $preview['invalid_count']
            ));
    }

    /**
     * Menyimpan baris nilai IP Semester yang lolos validasi pratinjau ke basis data.
     */
    public function importIpCsv()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/apresiasi'))
                ->with('error', 'Anda tidak memiliki wewenang menyimpan impor nilai IP semester.');
        }

        $token   = trim((string) ($this->request->getPost('preview_token') ?? ''));
        $preview = session()->get('beasiswa_ip_preview');

        if (!is_array($preview) || $token === '' || !hash_equals((string) ($preview['token'] ?? ''), $token)) {
            return redirect()->back()->with('error', 'Sesi pratinjau nilai IP sudah berakhir. Silakan unggah ulang berkas CSV.');
        }

        $validRows = $preview['valid_rows'] ?? [];
        $periodeId = (int) ($preview['tahun_akademik_id'] ?? 0);

        $result = $this->beasiswaService->importIpCsv(
            is_array($validRows) ? $validRows : [],
            $periodeId,
            $this->resolveActorLabel('Ketarunaan')
        );

        session()->remove('beasiswa_ip_preview');

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'taruna_nilai_ip', (string) $periodeId, null, [
                'jumlah_baris'      => (int) ($result['count'] ?? 0),
                'tahun_akademik_id' => $periodeId,
            ]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Membatalkan sesi pratinjau impor CSV nilai IP semester.
     */
    public function cancelIpPreview()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/apresiasi'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        session()->remove('beasiswa_ip_preview');

        return redirect()->back()->with('success', 'Pratinjau impor nilai IP ditutup.');
    }

    /**
     * Mengunduh contoh templat CSV untuk impor nilai IP semester.
     */
    public function downloadIpCsvTemplate()
    {
        if (!KetarunaanPolicy::canManageBeasiswa()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }

        $csv = "nomor_taruna,ip_semester,sks_lulus\n"
             . "2201001,3.85,22\n"
             . "2201002,3.72,20\n";

        return $this->response
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="templat_impor_ip_semester_ecc.csv"')
            ->setBody("\xEF\xBB\xBF" . $csv);
    }

    /**
     * Daftar Pengajuan / Klaim Beasiswa (Prestasi, Daerah 3TP, Tidak Mampu).
     */
    public function klaim()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;

        $filters = [
            'jenis_beasiswa'      => trim((string) ($this->request->getGet('jenis_beasiswa') ?? 'all')),
            'sumber_klaim'        => trim((string) ($this->request->getGet('sumber_klaim') ?? 'all')),
            'hasil_verifikasi'    => trim((string) ($this->request->getGet('hasil_verifikasi') ?? 'all')),
            'program_beasiswa_id' => (int) ($this->request->getGet('program_beasiswa_id') ?? 0),
            'tahun_akademik_id'   => trim((string) ($this->request->getGet('tahun_akademik_id') ?? 'all')),
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'sort_by'             => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'          => trim((string) ($this->request->getGet('sort_order') ?? 'desc')),
            'page'                => max(1, (int) ($this->request->getGet('page') ?? 1)),
            'per_page'            => 15,
        ];

        $klaimData = $this->beasiswaService->listKlaim(
            $filters,
            KetarunaanPolicy::canViewBeasiswaEconomy(),
            $viewerNim
        );

        return view('ketarunaan/beasiswa/klaim', [
            'title'        => 'Klaim Beasiswa - Evidence Command Center (ECC)',
            'activeTab'    => 'klaim',
            'filters'      => $filters,
            'rows'         => $klaimData['rows'],
            'pagination'   => $klaimData['pagination'],
            'refs'         => $this->beasiswaService->getReferenceOptions(false, false),
            'isSelfTaruna' => $isSelfTaruna,
            'perms'        => $this->getPermissions(),
        ]);
    }

    /**
     * Formulir Ajukan atau Ubah Klaim Beasiswa berdasarkan jenis (prestasi | daerah_3tp | tidak_mampu).
     */
    public function formKlaim(string $jenis)
    {
        if (!KetarunaanPolicy::canSubmitBeasiswaClaim()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                ->with('error', 'Anda tidak memiliki wewenang mengajukan klaim beasiswa.');
        }

        $jenisClean = strtolower(trim($jenis));
        if (!in_array($jenisClean, ['prestasi', 'daerah_3tp', 'tidak_mampu'], true)) {
            return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                ->with('error', 'Jenis klaim beasiswa tidak tersedia.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;
        $editId       = (int) ($this->request->getGet('edit') ?? 0);
        $existing     = null;

        if ($editId > 0) {
            $detail = $this->beasiswaService->getDetail($editId, KetarunaanPolicy::canViewBeasiswaEconomy($viewerNim), $viewerNim);
            if (!$detail) {
                return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                    ->with('error', 'Data klaim beasiswa tidak ditemukan.');
            }
            $existing = $detail['penerima'];
            if ($isSelfTaruna && ($viewerNim === null || strcasecmp((string) $existing['nomor_taruna'], $viewerNim) !== 0)) {
                return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                    ->with('error', 'Anda hanya dapat mengubah klaim milik Anda sendiri.');
            }
            if (($existing['jenis_beasiswa'] ?? '') === 'apresiasi' || ($existing['sumber_klaim'] ?? '') === 'pemeringkatan') {
                return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $editId))
                    ->with('error', 'Kandidat hasil pemeringkatan Apresiasi tidak dapat diubah melalui formulir klaim.');
            }
            if (($existing['status_penetapan'] ?? '') !== 'kandidat' || !in_array((string) ($existing['status_validasi'] ?? ''), ['draf', 'perlu_perbaikan'], true)) {
                return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $editId))
                    ->with('error', 'Pengajuan ini tidak berada pada status Draf atau Perlu Perbaikan sehingga tidak dapat diubah.');
            }
            if (($existing['jenis_beasiswa'] ?? '') !== $jenisClean) {
                return redirect()->to(base_url('ketarunaan/beasiswa/klaim/ajukan/' . $existing['jenis_beasiswa'] . '?edit=' . $editId));
            }
        }

        $refs = $this->beasiswaService->getReferenceOptions(!$isSelfTaruna || $editId > 0, $jenisClean === 'daerah_3tp');
        $selectedNim = $isSelfTaruna
            ? (string) ($viewerNim ?? '')
            : (string) ($existing['nomor_taruna'] ?? ($this->request->getGet('nomor_taruna') ?? ''));

        $prestasiTaruna = ($jenisClean === 'prestasi' && $selectedNim !== '')
            ? $this->beasiswaService->getTarunaValidatedPrestasi($selectedNim)
            : [];

        return view('ketarunaan/beasiswa/form_klaim', [
            'title'          => 'Formulir Klaim ' . (ketarunaan_jenis_beasiswa_catalog()[$jenisClean]['label'] ?? 'Beasiswa') . ' - Evidence Command Center (ECC)',
            'activeTab'      => 'klaim',
            'jenis'          => $jenisClean,
            'existing'       => $existing,
            'refs'           => $refs,
            'selectedNim'    => $selectedNim,
            'prestasiTaruna' => $prestasiTaruna,
            'isSelfTaruna'   => $isSelfTaruna,
            'perms'          => $this->getPermissions(),
        ]);
    }

    /**
     * Proses simpan pengajuan klaim Beasiswa Prestasi, Daerah 3TP, atau Tidak Mampu.
     */
    public function saveKlaim(string $jenis)
    {
        if (!KetarunaanPolicy::canSubmitBeasiswaClaim()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                ->with('error', 'Anda tidak memiliki wewenang mengirim klaim beasiswa.');
        }

        $jenisClean = strtolower(trim($jenis));
        if (!in_array($jenisClean, ['prestasi', 'daerah_3tp', 'tidak_mampu'], true)) {
            return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                ->with('error', 'Jenis klaim beasiswa tidak valid.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;
        $nomorTaruna  = $isSelfTaruna
            ? (string) ($viewerNim ?? '')
            : trim((string) ($this->request->getPost('nomor_taruna') ?? ''));

        $editId     = (int) ($this->request->getPost('edit_id') ?? 0);
        $aksiSubmit = strtolower(trim((string) ($this->request->getPost('aksi_submit') ?? 'ajukan')));

        $filesInfo = [];
        try {
            if ($jenisClean === 'prestasi') {
                $filesInfo['sertifikat_berkas']  = $this->validateExternalProofUrl($this->request->getPost('sertifikat_link'), 'Tautan sertifikat prestasi');
                $filesInfo['dokumentasi_berkas'] = $this->validateExternalProofUrl($this->request->getPost('dokumentasi_link'), 'Tautan dokumentasi kegiatan');
            } elseif ($jenisClean === 'daerah_3tp') {
                $filesInfo['dokumen_domisili']      = $this->validateExternalProofUrl($this->request->getPost('dokumen_domisili_link'), 'Tautan dokumen domisili');
                $filesInfo['dokumen_pendukung_3tp'] = $this->validateExternalProofUrl($this->request->getPost('dokumen_pendukung_3tp_link'), 'Tautan dokumen pendukung 3TP');
            } else {
                $filesInfo['dokumen_ekonomi_list'] = $this->collectEconomyUrlLinks();
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $input = [
            'program_beasiswa_id'         => $this->request->getPost('program_beasiswa_id'),
            'nomor_taruna'                => $nomorTaruna,
            'sumber_klaim'                => $this->request->getPost('sumber_klaim'),
            'aksi_submit'                 => $aksiSubmit,
            'prestasi_id'                 => $this->request->getPost('prestasi_id'),
            'kategori_prestasi'           => $this->request->getPost('kategori_prestasi'),
            'nama_prestasi'               => $this->request->getPost('nama_prestasi'),
            'tingkat_prestasi'            => $this->request->getPost('tingkat_prestasi'),
            'peringkat_prestasi'          => $this->request->getPost('peringkat_prestasi'),
            'tanggal_prestasi'            => $this->request->getPost('tanggal_prestasi'),
            'penyelenggara_prestasi'      => $this->request->getPost('penyelenggara_prestasi'),
            'alamat_domisili'             => $this->request->getPost('alamat_domisili'),
            'provinsi'                    => $this->request->getPost('provinsi'),
            'kabupaten_kota'              => $this->request->getPost('kabupaten_kota'),
            'kecamatan'                   => $this->request->getPost('kecamatan'),
            'desa_kelurahan'              => $this->request->getPost('desa_kelurahan'),
            'kode_wilayah'                => $this->request->getPost('kode_wilayah'),
            'tanggal_acuan_domisili'      => $this->request->getPost('tanggal_acuan_domisili'),
            'jenis_dokumen_domisili'      => $this->request->getPost('jenis_dokumen_domisili'),
            'jenis_dokumen_pendukung_3tp' => $this->request->getPost('jenis_dokumen_pendukung_3tp'),
            'hapus_dokumen_pendukung_3tp' => $this->request->getPost('hapus_dokumen_pendukung_3tp'),
            'remove_ekonomi_indices'      => $this->request->getPost('remove_ekonomi_indices'),
            'pekerjaan_orang_tua'         => $this->request->getPost('pekerjaan_orang_tua'),
            'penghasilan_bulanan'         => $this->request->getPost('penghasilan_bulanan'),
            'jumlah_tanggungan'           => $this->request->getPost('jumlah_tanggungan'),
            'uraian_pengajuan'            => $this->request->getPost('uraian_pengajuan'),
        ];

        $actorLabel = $isSelfTaruna
            ? ((string) (session()->get('nama_lengkap') ?: $nomorTaruna))
            : $this->resolveActorLabel('Ketarunaan');

        $result = $this->beasiswaService->submitKlaim(
            $jenisClean,
            $input,
            $filesInfo,
            $actorLabel,
            $isSelfTaruna,
            $editId > 0 ? $editId : null
        );

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $aksiAudit = $editId > 0 ? 'UPDATE' : 'CREATE';
            log_audit($aksiAudit, 'taruna_penerima_beasiswa', (string) $result['id'], null, [
                'jenis_beasiswa' => $jenisClean,
                'nomor_taruna'   => $nomorTaruna,
                'aksi_submit'    => $aksiSubmit,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Antrean & Riwayat Verifikasi Beasiswa (/ketarunaan/beasiswa/verifikasi).
     */
    public function verifikasi()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule() || $this->isSelfTarunaOnly()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Halaman pemeriksaan verifikasi hanya tersedia untuk pengelola dan pimpinan.');
        }

        $filters = [
            'hasil_verifikasi'    => trim((string) ($this->request->getGet('hasil_verifikasi') ?? 'all')),
            'jenis_beasiswa'      => trim((string) ($this->request->getGet('jenis_beasiswa') ?? 'all')),
            'program_beasiswa_id' => (int) ($this->request->getGet('program_beasiswa_id') ?? 0),
            'tahun_akademik_id'   => trim((string) ($this->request->getGet('tahun_akademik_id') ?? 'all')),
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'sort_by'             => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'          => trim((string) ($this->request->getGet('sort_order') ?? 'desc')),
            'page'                => max(1, (int) ($this->request->getGet('page') ?? 1)),
            'per_page'            => 15,
        ];

        $data = $this->beasiswaService->listVerifikasi($filters, KetarunaanPolicy::canViewBeasiswaEconomy());

        return view('ketarunaan/beasiswa/verifikasi', [
            'title'         => 'Verifikasi Klaim Beasiswa - Evidence Command Center (ECC)',
            'activeTab'     => 'verifikasi',
            'filters'       => $filters,
            'summary'       => $data['summary'],
            'rows'          => $data['rows'],
            'pagination'    => $data['pagination'],
            'refs'          => $this->beasiswaService->getReferenceOptions(false, false),
            'isSelfTaruna'  => false,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Ruang Kerja Verifikasi Klaim Beasiswa (/ketarunaan/beasiswa/verifikasi/{id}).
     */
    public function verifikasiDetail(int $id)
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule() || $this->isSelfTarunaOnly()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $canViewEconomy = KetarunaanPolicy::canViewBeasiswaEconomy();
        $detail         = $this->beasiswaService->getDetail($id, $canViewEconomy);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/beasiswa/verifikasi'))
                ->with('error', 'Data klaim beasiswa tidak ditemukan.');
        }
        if (($detail['penerima']['status_validasi'] ?? '') === 'draf') {
            return redirect()->to(base_url('ketarunaan/beasiswa/verifikasi'))
                ->with('error', 'Pengajuan berstatus Draf belum dikirim oleh pemohon.');
        }

        return view('ketarunaan/beasiswa/verifikasi_detail', [
            'title'         => 'Periksa Kelayakan Beasiswa - Evidence Command Center (ECC)',
            'activeTab'     => 'verifikasi',
            'detail'        => $detail,
            'isSelfTaruna'  => false,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Proses simpan hasil pemeriksaan verifikasi klaim (Memenuhi | Perlu Perbaikan | Tidak Memenuhi).
     */
    public function saveVerifikasi(int $id)
    {
        if (!KetarunaanPolicy::canVerifyBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/verifikasi/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang melakukan verifikasi kelayakan beasiswa.');
        }

        $hasil = (string) ($this->request->getPost('hasil_verifikasi') ?? '');
        $result = $this->beasiswaService->saveVerifikasi($id, [
            'hasil_verifikasi'  => $hasil,
            'catatan_pemeriksa' => $this->request->getPost('catatan_pemeriksa'),
            'cek_identitas'     => $this->request->getPost('cek_identitas'),
            'cek_kelayakan'     => $this->request->getPost('cek_kelayakan'),
            'cek_dokumen'       => $this->request->getPost('cek_dokumen'),
        ], $this->resolveActorLabel('Verifikasi'));

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_penerima_beasiswa', (string) $id, null, [
                'tahap'            => 'verifikasi',
                'hasil_verifikasi' => $hasil,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/verifikasi/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Daftar Penerima & Kandidat Penetapan (/ketarunaan/beasiswa/penerima).
     */
    public function penerima()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;

        $filters = [
            'program_beasiswa_id' => (int) ($this->request->getGet('program_beasiswa_id') ?? 0),
            'jenis_beasiswa'      => trim((string) ($this->request->getGet('jenis_beasiswa') ?? 'all')),
            'status_penetapan'    => trim((string) ($this->request->getGet('status_penetapan') ?? 'all')),
            'status_validasi'     => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
            'prodi_id'            => (int) ($this->request->getGet('prodi_id') ?? 0),
            'tahun_akademik_id'   => trim((string) ($this->request->getGet('tahun_akademik_id') ?? 'all')),
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
            'sort_by'             => trim((string) ($this->request->getGet('sort_by') ?? '')),
            'sort_order'          => trim((string) ($this->request->getGet('sort_order') ?? 'desc')),
            'page'                => max(1, (int) ($this->request->getGet('page') ?? 1)),
            'per_page'            => 15,
        ];

        $data = $this->beasiswaService->listPenerima(
            $filters,
            KetarunaanPolicy::canViewBeasiswaEconomy(),
            $viewerNim
        );

        return view('ketarunaan/beasiswa/penerima', [
            'title'         => 'Penerima Beasiswa - Evidence Command Center (ECC)',
            'activeTab'     => 'penerima',
            'filters'       => $filters,
            'summary'       => $data['summary'],
            'rows'          => $data['rows'],
            'pagination'    => $data['pagination'],
            'refs'          => $this->beasiswaService->getReferenceOptions(false, false),
            'isSelfTaruna'  => $isSelfTaruna,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Halaman Detail Pengajuan / Penerima Beasiswa (/ketarunaan/beasiswa/detail/{id}).
     */
    public function detail(int $id)
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return redirect()->to(base_url('ketarunaan/beasiswa'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;
        $detail       = $this->beasiswaService->getDetail($id, KetarunaanPolicy::canViewBeasiswaEconomy($viewerNim), $viewerNim);

        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/beasiswa/penerima'))
                ->with('error', 'Data beasiswa tidak ditemukan.');
        }

        $rowNim = (string) ($detail['penerima']['nomor_taruna'] ?? '');
        if ($isSelfTaruna && ($viewerNim === null || strcasecmp($rowNim, $viewerNim) !== 0)) {
            return redirect()->to(base_url('ketarunaan/beasiswa/penerima'))
                ->with('error', 'Anda hanya dapat melihat rincian pengajuan milik Anda sendiri.');
        }

        return view('ketarunaan/beasiswa/detail', [
            'title'         => 'Detail Pengajuan & Penerima Beasiswa - Evidence Command Center (ECC)',
            'activeTab'     => 'penerima',
            'detail'        => $detail,
            'isSelfTaruna'  => $isSelfTaruna,
            'perms'         => $this->getPermissions(),
        ]);
    }

    /**
     * Menetapkan penerima beasiswa dengan nomor & tautan berkas SK (hanya jika hasil_verifikasi = memenuhi).
     */
    public function savePenetapan(int $id)
    {
        if (!KetarunaanPolicy::canDecideBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang menetapkan penerima beasiswa.');
        }

        $rules = [
            'nomor_keputusan'   => 'required|max_length[100]',
            'tanggal_penetapan' => 'required|valid_date[Y-m-d]',
            'pejabat_penetap'   => 'required|max_length[120]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Nomor keputusan, tanggal penetapan, dan pejabat penetap wajib diisi.');
        }

        $skLink = null;
        try {
            $skLink = $this->validateExternalProofUrl($this->request->getPost('dokumen_keputusan_link'), 'Tautan berkas surat keputusan');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $nomorSk = trim((string) ($this->request->getPost('nomor_keputusan') ?? ''));
        $result  = $this->beasiswaService->savePenetapan($id, [
            'nomor_keputusan'   => $nomorSk,
            'tanggal_penetapan' => $this->request->getPost('tanggal_penetapan'),
            'pejabat_penetap'   => $this->request->getPost('pejabat_penetap'),
            'catatan_penetapan' => $this->request->getPost('catatan_penetapan'),
            'aksi_penetapan'    => $this->request->getPost('aksi_penetapan') ?: 'tetapkan_validasi',
        ], $skLink, $this->resolveActorLabel('Penetapan'));

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('APPROVE', 'taruna_penerima_beasiswa', (string) $id, null, [
                'tahap'           => 'penetapan_sk',
                'nomor_keputusan' => $nomorSk,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Membatalkan penetapan penerima beasiswa dan mengeluarkannya dari hitungan resmi.
     */
    public function cancelPenetapan(int $id)
    {
        if (!KetarunaanPolicy::canDecideBeasiswa()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $id))
                ->with('error', 'Anda tidak memiliki wewenang membatalkan penetapan beasiswa.');
        }

        $alasan = trim((string) ($this->request->getPost('alasan_pembatalan') ?? ''));
        $result = $this->beasiswaService->cancelPenetapan($id, $alasan, $this->resolveActorLabel('Penetapan'));

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'taruna_penerima_beasiswa', (string) $id, null, [
                'aksi'   => 'batal_penetapan',
                'alasan' => $alasan,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/detail/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Menghapus pengajuan berstatus Draf.
     */
    public function deleteDraft(int $id)
    {
        if (!KetarunaanPolicy::canSubmitBeasiswaClaim()) {
            return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
                ->with('error', 'Akses tidak diizinkan.');
        }

        $viewerNim = $this->isSelfTarunaOnly() ? $this->resolveViewerNomorTaruna() : null;
        $result    = $this->beasiswaService->deleteDraft($id, $viewerNim);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'taruna_penerima_beasiswa', (string) $id, null, [
                'aksi' => 'hapus_draf_beasiswa',
            ]);
        }

        return redirect()->to(base_url('ketarunaan/beasiswa/klaim'))
            ->with('success', $result['message']);
    }

    /**
     * Mengunduh atau pratinjau dokumen lampiran klaim/SK secara aman (tautan eksternal Opsi A atau berkas fisik lama + nosniff).
     */
    public function unduhDokumen(int $id)
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }

        $isSelfTaruna = $this->isSelfTarunaOnly();
        $viewerNim    = $isSelfTaruna ? $this->resolveViewerNomorTaruna() : null;
        $canViewEko   = KetarunaanPolicy::canViewBeasiswaEconomy();

        // Ambil rincian penuh internal hanya untuk validasi kepemilikan & jenis dokumen di server
        $detail = $this->beasiswaService->getDetail($id, true, $viewerNim);
        if (!$detail) {
            return $this->response->setStatusCode(404)->setBody('Berkas beasiswa tidak ditemukan.');
        }

        $row    = $detail['penerima'];
        $rowNim = (string) ($row['nomor_taruna'] ?? '');
        if ($isSelfTaruna && ($viewerNim === null || strcasecmp($rowNim, $viewerNim) !== 0)) {
            return $this->response->setStatusCode(403)->setBody('Anda hanya dapat mengakses berkas milik Anda sendiri.');
        }

        $requestedRaw = trim((string) ($this->request->getGet('file') ?? ''));
        $requestedBasename = basename($requestedRaw);
        $matched   = null;
        foreach ($detail['dokumen'] as $dok) {
            $dokName = (string) ($dok['nama_berkas'] ?? '');
            if ($requestedRaw === '' || $dokName === $requestedRaw || basename($dokName) === $requestedBasename) {
                $matched = $dok;
                break;
            }
        }

        if (!$matched) {
            return $this->response->setStatusCode(404)->setBody('Berkas yang diminta tidak terdaftar.');
        }

        $isOwner = $viewerNim !== null && strcasecmp($viewerNim, $rowNim) === 0;
        $isSk    = ($matched['kategori'] ?? '') === 'Surat Keputusan Penerima';
        $isSensitiveFile = !$isSk && (!empty($matched['sensitif']) || ($row['jenis_beasiswa'] ?? '') === 'tidak_mampu');
        if ($isSensitiveFile && !$canViewEko && !$isOwner) {
            return $this->response->setStatusCode(403)
                ->setBody('Dokumen ini bersifat terbatas hanya bagi pengelola berwenang atau pemilik pengajuan.');
        }

        $rawTarget = trim((string) ($matched['nama_berkas'] ?? ''));
        if (ketarunaan_is_http_url($rawTarget, 2000)) {
            return redirect()->to($rawTarget);
        }

        $fileName = basename($rawTarget);
        $inline   = ((int) $this->request->getGet('inline') === 1);

        $pathsToCheck = [
            FCPATH . 'assets/uploads/ketarunaan/beasiswa/' . $fileName,
            FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $fileName,
        ];
        foreach ($pathsToCheck as $physicalPath) {
            if (is_file($physicalPath)) {
                $mime = mime_content_type($physicalPath) ?: 'application/pdf';
                return $this->response
                    ->setHeader('X-Content-Type-Options', 'nosniff')
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPath));
            }
        }

        return $this->response->setStatusCode(404)->setBody('Berkas fisik tidak ditemukan di penyimpanan.');
    }

    /**
     * Ekspor daftar penerima & kandidat beasiswa ke CSV (kolom ekonomi disembunyikan untuk peran terbatas).
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessBeasiswaModule() || $this->isSelfTarunaOnly()) {
            return $this->response->setStatusCode(403, 'Akses ditolak.')
                ->setBody('Anda tidak memiliki wewenang mengekspor rekap beasiswa.');
        }

        $filters = [
            'program_beasiswa_id' => (int) ($this->request->getGet('program_beasiswa_id') ?? 0),
            'jenis_beasiswa'      => trim((string) ($this->request->getGet('jenis_beasiswa') ?? 'all')),
            'status_penetapan'    => trim((string) ($this->request->getGet('status_penetapan') ?? 'all')),
            'status_validasi'     => trim((string) ($this->request->getGet('status_validasi') ?? 'all')),
            'prodi_id'            => (int) ($this->request->getGet('prodi_id') ?? 0),
            'tahun_akademik_id'   => trim((string) ($this->request->getGet('tahun_akademik_id') ?? 'all')),
            'q'                   => trim((string) ($this->request->getGet('q') ?? '')),
        ];

        $canViewEconomy = KetarunaanPolicy::canViewBeasiswaEconomy();
        $export         = $this->beasiswaService->getExportRows($filters, $canViewEconomy);

        if (function_exists('log_audit')) {
            log_audit('EXPORT_CSV_BEASISWA', 'taruna_penerima_beasiswa', 'all', null, [
                'filters'    => $filters,
                'total_rows' => count($export['rows']),
            ]);
        }

        $fh = fopen('php://temp', 'w+');
        fputcsv($fh, $export['headers']);
        foreach ($export['rows'] as $row) {
            $sanitized = array_map(static fn ($cell) => ketarunaan_csv_safe((string) $cell), $row);
            fputcsv($fh, $sanitized);
        }
        rewind($fh);
        $csvContent = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="rekap_beasiswa_ecc2_' . date('Ymd_His') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . (string) $csvContent);
    }

    /**
     * Endpoint JSON untuk memuat daftar prestasi tervalidasi milik taruna pada form Klaim Prestasi.
     */
    public function apiPrestasiTaruna(string $nomorTaruna)
    {
        if (!KetarunaanPolicy::canSubmitBeasiswaClaim()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'items' => []]);
        }

        $targetNim = $this->isSelfTarunaOnly()
            ? (string) ($this->resolveViewerNomorTaruna() ?? '')
            : trim($nomorTaruna);

        $items = $this->beasiswaService->getTarunaValidatedPrestasi($targetNim);

        return $this->response->setJSON([
            'status' => 'ok',
            'items'  => $items,
        ]);
    }

    /**
     * Memvalidasi tautan bukti eksternal Opsi A (Google Drive / Cloud http(s):// maksimal 255 karakter).
     *
     * @throws \RuntimeException
     */
    private function validateExternalProofUrl($rawUrl, string $label): ?string
    {
        $clean = trim((string) ($rawUrl ?? ''));
        if ($clean === '') {
            return null;
        }
        if (!ketarunaan_is_http_url($clean, 255)) {
            throw new \RuntimeException($label . ' harus berupa tautan http:// atau https:// yang valid (maksimal 255 karakter).');
        }

        return $clean;
    }

    /**
     * Mengumpulkan multi-dokumen bukti ekonomi (Opsi A tautan Google Drive / cloud) pada form Beasiswa Tidak Mampu.
     *
     * @return array<int, array<string, string>>
     * @throws \RuntimeException
     */
    private function collectEconomyUrlLinks(): array
    {
        $linksAll    = (array) ($this->request->getPost('dokumen_ekonomi_links') ?? []);
        $jenisArr    = (array) ($this->request->getPost('dokumen_ekonomi_jenis') ?? []);
        $nomorArr    = (array) ($this->request->getPost('dokumen_ekonomi_nomor') ?? []);
        $penerbitArr = (array) ($this->request->getPost('dokumen_ekonomi_penerbit') ?? []);

        $result = [];
        foreach ($linksAll as $idx => $rawLink) {
            $cleanUrl = trim((string) $rawLink);
            if ($cleanUrl === '') {
                continue;
            }
            $validatedUrl = $this->validateExternalProofUrl($cleanUrl, 'Tautan dokumen ekonomi #' . ($idx + 1));
            if ($validatedUrl !== null) {
                $result[] = [
                    'jenis_dokumen' => trim((string) ($jenisArr[$idx] ?? 'Dokumen Ekonomi')),
                    'nomor_dokumen' => trim((string) ($nomorArr[$idx] ?? '')),
                    'penerbit'      => trim((string) ($penerbitArr[$idx] ?? '')),
                    'nama_berkas'   => $validatedUrl,
                ];
            }
        }

        return $result;
    }

    /**
     * Merangkum seluruh hak akses Modul Beasiswa untuk diteruskan ke View.
     *
     * @return array<string, bool>
     */
    private function getPermissions(): array
    {
        return [
            'can_manage'       => KetarunaanPolicy::canManageBeasiswa(),
            'can_submit_claim' => KetarunaanPolicy::canSubmitBeasiswaClaim(),
            'can_verify'       => KetarunaanPolicy::canVerifyBeasiswa(),
            'can_decide'       => KetarunaanPolicy::canDecideBeasiswa(),
            'can_view_economy' => KetarunaanPolicy::canViewBeasiswaEconomy(),
        ];
    }

    private function isSelfTarunaOnly(): bool
    {
        return KetarunaanPolicy::isSelfTarunaOnly();
    }

    private function resolveViewerNomorTaruna(): ?string
    {
        $resolved = KetarunaanPolicy::resolveSelfNomorTaruna();
        if ($resolved !== null && $resolved !== '') {
            return $resolved;
        }
        $fallback = trim((string) (session()->get('username') ?? ''));
        return $fallback !== '' ? $fallback : null;
    }

    private function resolveActorLabel(string $fallbackUnit = 'Ketarunaan'): string
    {
        $nama = trim((string) (session()->get('nama_lengkap') ?? ''));
        return $nama !== '' ? $nama : ('PIC ' . $fallbackUnit);
    }
}
