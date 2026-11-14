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

        $filters = [
            'q'        => trim((string) $this->request->getGet('q')),
            'prodi'    => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'angkatan' => trim((string) ($this->request->getGet('angkatan') ?? 'all')),
            'status'   => trim((string) ($this->request->getGet('status') ?? 'all')),
            'periode'  => trim((string) ($this->request->getGet('periode') ?? 'all')),
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
            'title'              => 'Rekam Jejak ' . $detail['taruna']['nama_lengkap'] . ' - Evidence Command Center (ECC)',
            'active_menu'        => 'rekam_jejak',
            'active_tab'         => $tab,
            'filters'            => $filters,
            'refs'               => $refs,
            'detail'             => $detail,
            'canManageTaruna'    => KetarunaanPolicy::canManageTaruna(),
            'canValidate'        => KetarunaanPolicy::canValidate(),
            'canViewDiscipline'  => $canViewDiscipline,
        ]);
    }

    /**
     * Halaman Formulir Tambah Capaian Taruna.
     */
    public function createCapaian(?string $nomorTaruna = null)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data capaian.');
        }

        $refs       = $this->rekamJejakService->getReferenceOptions();
        $tarunaList = $this->rekamJejakService->getTarunaDropdownList();

        $selectedTaruna = null;
        if ($nomorTaruna !== null && $nomorTaruna !== '') {
            $detail = $this->rekamJejakService->getDetailRekamJejak($nomorTaruna);
            $selectedTaruna = $detail['taruna'] ?? null;
        }

        return view('ketarunaan/rekam_jejak/form_capaian', [
            'title'          => 'Tambah Capaian Taruna - Evidence Command Center (ECC)',
            'active_menu'    => 'rekam_jejak',
            'mode'           => 'create',
            'capaian'        => null,
            'selectedTaruna' => $selectedTaruna,
            'tarunaList'     => $tarunaList,
            'refs'           => $refs,
        ]);
    }

    /**
     * Proses penyimpanan data capaian baru.
     */
    public function storeCapaian()
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang menambah data capaian.');
        }

        $rules = [
            'nomor_taruna'     => 'required|max_length[30]',
            'kategori'         => 'required|in_list[kompetensi_sertifikasi,organisasi,pengembangan_diri,akademik]',
            'nama_capaian'     => 'required|min_length[3]|max_length[180]',
            'tanggal_kegiatan' => 'required|valid_date[Y-m-d]',
            'penyelenggara'    => 'required|min_length[2]|max_length[160]',
            'hasil_capaian'    => 'required|max_length[60]',
        ];

        $file = $this->request->getFile('bukti_berkas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['bukti_berkas'] = 'max_size[bukti_berkas,5120]|ext_in[bukti_berkas,pdf,jpg,jpeg,png]|mime_in[bukti_berkas,application/pdf,image/jpeg,image/jpg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi isian wajib dan pastikan format berkas PDF/JPG/PNG maksimal 5 MB.');
        }

        $uploadedFileName = null;
        $uploadedSizeKb   = 250;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/capaian';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadedFileName = $file->getRandomName();
            $uploadedSizeKb   = max(1, (int) round($file->getSize() / 1024));
            $file->move($uploadDir, $uploadedFileName);
        }

        $aksiSubmit = trim((string) ($this->request->getPost('aksi_submit') ?? 'diajukan'));
        $statusValidasi = ($aksiSubmit === 'draf') ? 'draf' : 'diajukan';

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? 'Ketarunaan'));
        $diajukanOleh = match ($unitPemilik) {
            'Pusbangkar'    => 'PIC Pusbangkar',
            'Program Studi' => 'PIC Prodi',
            default         => 'PIC Ketarunaan',
        };

        $nomorTaruna = trim((string) $this->request->getPost('nomor_taruna'));
        $result = $this->rekamJejakService->saveCapaian([
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
            'diajukan_oleh'     => $diajukanOleh,
        ]);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'Menambahkan capaian rekam jejak taruna ' . $nomorTaruna . ' (ID #' . ($result['id'] ?? 0) . ')');
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

        return view('ketarunaan/rekam_jejak/detail_capaian', [
            'title'           => 'Detail Capaian - Evidence Command Center (ECC)',
            'active_menu'     => 'rekam_jejak',
            'capaian'         => $capaian,
            'canManageTaruna' => KetarunaanPolicy::canManageTaruna(),
            'canValidate'     => KetarunaanPolicy::canValidate(),
        ]);
    }

    /**
     * Halaman Formulir Koreksi / Ubah Capaian Taruna.
     */
    public function editCapaian(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang mengubah data capaian.');
        }

        $capaian = $this->rekamJejakService->getCapaianDetail($id);
        if (!$capaian) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        $refs       = $this->rekamJejakService->getReferenceOptions();
        $tarunaList = $this->rekamJejakService->getTarunaDropdownList();

        return view('ketarunaan/rekam_jejak/form_capaian', [
            'title'          => 'Koreksi Capaian Taruna - Evidence Command Center (ECC)',
            'active_menu'    => 'rekam_jejak',
            'mode'           => 'edit',
            'capaian'        => $capaian,
            'selectedTaruna' => [
                'nomor_taruna'   => $capaian['nomor_taruna'],
                'nama_lengkap'   => $capaian['nama_lengkap'],
                'kode_prodi'     => $capaian['kode_prodi'],
                'angkatan'       => $capaian['angkatan'],
                'status_terkini' => $capaian['status_terkini'],
            ],
            'tarunaList'     => $tarunaList,
            'refs'           => $refs,
        ]);
    }

    /**
     * Proses penyimpanan koreksi / pembaruan data capaian.
     */
    public function updateCapaian(int $id)
    {
        if (!KetarunaanPolicy::canManageTaruna()) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Anda tidak memiliki wewenang mengubah data capaian.');
        }

        $existing = $this->rekamJejakService->getCapaianDetail($id);
        if (!$existing) {
            return redirect()->to(base_url('ketarunaan/rekam-jejak'))
                ->with('error', 'Data capaian tidak ditemukan.');
        }

        $rules = [
            'kategori'         => 'required|in_list[kompetensi_sertifikasi,organisasi,pengembangan_diri,akademik]',
            'nama_capaian'     => 'required|min_length[3]|max_length[180]',
            'tanggal_kegiatan' => 'required|valid_date[Y-m-d]',
            'penyelenggara'    => 'required|min_length[2]|max_length[160]',
            'hasil_capaian'    => 'required|max_length[60]',
        ];

        $file = $this->request->getFile('bukti_berkas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules['bukti_berkas'] = 'max_size[bukti_berkas,5120]|ext_in[bukti_berkas,pdf,jpg,jpeg,png]|mime_in[bukti_berkas,application/pdf,image/jpeg,image/jpg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', 'Mohon lengkapi isian wajib dan pastikan format berkas PDF/JPG/PNG maksimal 5 MB.');
        }

        $uploadedFileName = null;
        $uploadedSizeKb   = (int) ($existing['ukuran_berkas_kb'] ?? 250);

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'assets/uploads/ketarunaan/capaian';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $uploadedFileName = $file->getRandomName();
            $uploadedSizeKb   = max(1, (int) round($file->getSize() / 1024));
            $file->move($uploadDir, $uploadedFileName);

            if (!empty($existing['bukti_berkas'])) {
                $oldPath = $uploadDir . '/' . basename((string) $existing['bukti_berkas']);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $aksiSubmit     = trim((string) ($this->request->getPost('aksi_submit') ?? 'diajukan'));
        $statusValidasi = ($aksiSubmit === 'draf') ? 'draf' : 'diajukan';

        $unitPemilik = trim((string) ($this->request->getPost('unit_pemilik') ?? $existing['unit_pemilik']));
        $diajukanOleh = match ($unitPemilik) {
            'Pusbangkar'    => 'PIC Pusbangkar',
            'Program Studi' => 'PIC Prodi',
            default         => 'PIC Ketarunaan',
        };

        $result = $this->rekamJejakService->saveCapaian([
            'nomor_taruna'      => $existing['nomor_taruna'],
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
            'diajukan_oleh'     => $diajukanOleh,
        ], $id);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('UPDATE', 'Memperbarui capaian rekam jejak taruna ID #' . $id);
        }

        return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
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

        $keputusan = trim((string) ($this->request->getPost('keputusan') ?? 'tervalidasi'));
        $catatan   = trim((string) ($this->request->getPost('catatan_validasi') ?? ''));

        if ($keputusan === 'perlu_perbaikan' && $catatan === '') {
            return redirect()->back()
                ->with('error', 'Mohon isi alasan pengembalian agar petugas dapat memperbaiki data.');
        }

        $result = $this->rekamJejakService->processValidation(
            $id,
            $keputusan,
            $catatan,
            'Validator Unit'
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('APPROVE', 'Validasi capaian rekam jejak ID #' . $id . ' menjadi ' . $keputusan);
        }

        return redirect()->to(base_url('ketarunaan/rekam-jejak/capaian/' . $id))
            ->with('success', $result['message']);
    }

    /**
     * Unduh / Pratinjau berkas bukti rekam jejak dengan pemeriksaan hak akses.
     */
    public function unduhBukti(string $modul, int $id)
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $modulClean = strtolower(trim($modul));
        $nomorTaruna = null;
        $fileName    = 'Dokumen_Bukti.pdf';
        $judul       = 'Dokumen Bukti Rekam Jejak';

        $db = \Config\Database::connect();

        if ($modulClean === 'capaian') {
            $row = $this->rekamJejakService->getCapaianDetail($id);
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
            }
            $nomorTaruna = $row['nomor_taruna'];
            $fileName    = basename((string) ($row['bukti_berkas'] ?: 'Sertifikat.pdf'));
            $judul       = $row['nama_capaian'];

            $physicalPath = FCPATH . 'assets/uploads/ketarunaan/capaian/' . $fileName;
            if (is_file($physicalPath)) {
                $inline = ((int) $this->request->getGet('inline') === 1);
                $mime   = mime_content_type($physicalPath) ?: 'application/pdf';
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPath));
            }
        } elseif ($modulClean === 'prestasi') {
            $row = $db->table('taruna_prestasi')->where('id', $id)->get()->getRowArray();
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen prestasi tidak ditemukan.');
            }
            $fileName = basename((string) ($row['bukti_berkas'] ?: 'Piagam_Prestasi.pdf'));
            $judul    = $row['nama_prestasi'];

            $physicalPrestasi = FCPATH . 'assets/uploads/ketarunaan/prestasi/' . $fileName;
            if (is_file($physicalPrestasi)) {
                $inline = ((int) $this->request->getGet('inline') === 1);
                $mime   = mime_content_type($physicalPrestasi) ?: 'application/pdf';
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPrestasi));
            }
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
            $nomorTaruna = $row['nomor_taruna'];
            if (!KetarunaanPolicy::canViewRestrictedDocument('beasiswa', $nomorTaruna)) {
                return redirect()->back()->with('error', 'Dokumen ini bersifat terbatas untuk peran yang berwenang.');
            }
            $fileName = 'Keputusan_Beasiswa.pdf';
            $judul    = $row['nama_program'] ?? 'Beasiswa';
        } elseif ($modulClean === 'pelanggaran') {
            $row = $db->table('taruna_pelanggaran')->where('id', $id)->get()->getRowArray();
            if (!$row) {
                return redirect()->back()->with('error', 'Dokumen kasus tidak ditemukan.');
            }
            $nomorTaruna = $row['nomor_taruna'];
            if (!KetarunaanPolicy::canViewRestrictedDocument('pelanggaran', $nomorTaruna)) {
                return redirect()->back()->with('error', 'Dokumen ini bersifat terbatas untuk petugas berwenang.');
            }
            $fileName = basename((string) ($row['bukti_berkas'] ?: ('BA_' . $row['nomor_perkara'] . '.pdf')));
            $judul    = 'Kasus ' . $row['nomor_perkara'] . ' - ' . $row['jenis_pelanggaran'];

            $physicalPelanggaran = FCPATH . 'assets/uploads/ketarunaan/pelanggaran/' . $fileName;
            if (is_file($physicalPelanggaran)) {
                $inline = ((int) $this->request->getGet('inline') === 1);
                $mime   = mime_content_type($physicalPelanggaran) ?: 'application/pdf';
                return $this->response
                    ->setHeader('Content-Type', $mime)
                    ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
                    ->setBody((string) file_get_contents($physicalPelanggaran));
            }
        } else {
            return redirect()->back()->with('error', 'Modul dokumen tidak dikenali.');
        }

        // Hasilkan PDF ringkas resmi untuk berkas simulasi yang belum memiliki file fisik
        $inline = ((int) $this->request->getGet('inline') === 1);
        $pdfContent = ketarunaan_generate_simple_pdf('Dokumen Bukti Rekam Jejak Taruna', $judul, [
            'Nama Berkas'  => $fileName,
            'Nomor Taruna' => $nomorTaruna ?: '-',
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', ($inline ? 'inline' : 'attachment') . '; filename="' . $fileName . '"')
            ->setBody($pdfContent);
    }

    /**
     * Ekspor daftar direktori rekam jejak ke format CSV.
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canAccessModule()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Modul Ketarunaan.');
        }

        $filters = [
            'q'        => trim((string) $this->request->getGet('q')),
            'prodi'    => trim((string) ($this->request->getGet('prodi') ?? 'all')),
            'angkatan' => trim((string) ($this->request->getGet('angkatan') ?? 'all')),
            'status'   => trim((string) ($this->request->getGet('status') ?? 'all')),
            'periode'  => trim((string) ($this->request->getGet('periode') ?? 'all')),
        ];

        $dir = $this->rekamJejakService->getDirectoryData($filters, 1, 1000);

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
            $lines[] = implode(',', [
                '"' . str_replace('"', '""', (string) $r['nomor_taruna']) . '"',
                '"' . str_replace('"', '""', (string) $r['nama_lengkap']) . '"',
                '"' . str_replace('"', '""', (string) ($r['kode_prodi'] ?? '-')) . '"',
                '"' . str_replace('"', '""', (string) ($r['angkatan'] ?? '-')) . '"',
                '"' . str_replace('"', '""', $statusLabel) . '"',
                (int) $r['jumlah_capaian'],
                (int) $r['jumlah_prestasi'],
                '"' . str_replace('"', '""', (string) $r['tanggal_pembaruan']) . '"',
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

