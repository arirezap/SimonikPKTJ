<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\KetarunaanPolicy;
use App\Libraries\Ketarunaan\TracerStudyService;

/**
 * Controller Modul Tracer Study (ECC 2.0).
 * Mengelola bundel pertanyaan reusable, penyusunan pertanyaan periode sebelum dibuka,
 * kontrol buka/tutup periode berbasis role pegawai, pengisian survei alumni/PIC,
 * serta pemantauan progres dan hasil setiap periode pembukaan.
 */
class TracerStudyController extends BaseController
{
    protected TracerStudyService $service;

    public function __construct(?TracerStudyService $service = null)
    {
        $this->service = $service ?? new TracerStudyService();
        helper(['ketarunaan', 'audit', 'role']);
    }

    /**
     * Halaman Ringkasan Progres & Hasil per Periode (/ketarunaan/tracer-study).
     * Untuk akun alumni mandiri, menampilkan daftar survei terbuka dan status pengisian pribadi.
     */
    public function index()
    {
        if (!KetarunaanPolicy::canAccessTracerStudyModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke modul Tracer Study.');
        }

        $isSelfAlumni = KetarunaanPolicy::isSelfAlumniOnly();
        if ($isSelfAlumni) {
            $ownNim   = KetarunaanPolicy::resolveSelfAlumniNomorTaruna();
            $periodes = $this->service->listPeriodes($ownNim);

            return view('ketarunaan/tracer_study/alumni_portal', [
                'title'        => 'Tracer Study Alumni - Evidence Command Center (ECC)',
                'activeTab'    => 'ringkasan',
                'periodes'     => $periodes,
                'ownNim'       => $ownNim,
                'isSelfAlumni' => true,
                'perms'        => $this->getPermissions(),
            ]);
        }

        $refs         = $this->service->getReferenceOptions();
        $periodes     = $this->service->listPeriodes();
        $selectedPid  = (int) ($this->request->getGet('periode_id') ?? 0);
        if ($selectedPid <= 0 && !empty($periodes)) {
            $selectedPid = (int) $periodes[0]['id'];
        }

        $filters = [
            'prodi_id'    => (int) ($this->request->getGet('prodi_id') ?? 0),
            'tahun_lulus' => (int) ($this->request->getGet('tahun_lulus') ?? 0),
        ];

        $analytics  = $selectedPid > 0 ? $this->service->getPeriodAnalytics($selectedPid, $filters) : null;
        $comparison = $this->service->getCrossPeriodComparison();

        return view('ketarunaan/tracer_study/index', [
            'title'       => 'Ringkasan & Hasil Tracer Study - Evidence Command Center (ECC)',
            'activeTab'   => 'ringkasan',
            'refs'        => $refs,
            'periodes'    => $periodes,
            'selectedPid' => $selectedPid,
            'filters'     => $filters,
            'analytics'   => $analytics,
            'comparison'  => $comparison,
            'perms'       => $this->getPermissions(),
        ]);
    }

    /**
     * Halaman Manajemen Periode & Kontrol Buka/Tutup Survei (/ketarunaan/tracer-study/periode).
     */
    public function periode()
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses mengelola periode Tracer Study.');
        }

        $refs     = $this->service->getReferenceOptions();
        $periodes = $this->service->listPeriodes();

        return view('ketarunaan/tracer_study/periode', [
            'title'     => 'Periode & Kontrol Survei Tracer Study - Evidence Command Center (ECC)',
            'activeTab' => 'periode',
            'refs'      => $refs,
            'periodes'  => $periodes,
            'perms'     => $this->getPermissions(),
        ]);
    }

    /**
     * Simpan (buat baru / ubah) periode Tracer Study (POST /ketarunaan/tracer-study/periode/simpan).
     */
    public function savePeriode()
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengelola periode Tracer Study.');
        }

        $periodeId = (int) ($this->request->getPost('periode_id') ?? 0);
        $input     = [
            'kode_periode'              => $this->request->getPost('kode_periode'),
            'public_token'              => $this->request->getPost('public_token'),
            'nama_periode'              => $this->request->getPost('nama_periode'),
            'target_responden'          => $this->request->getPost('target_responden'),
            'sumber_pertanyaan'         => $this->request->getPost('sumber_pertanyaan'),
            'sumber_bundle_id'          => $this->request->getPost('sumber_bundle_id'),
            'sumber_periode_id'         => $this->request->getPost('sumber_periode_id'),
            'tahun_lulus_sasaran'       => $this->request->getPost('tahun_lulus_sasaran') ?? [],
            'prodi_sasaran'             => $this->request->getPost('prodi_sasaran') ?? [],
            'tanggal_mulai'             => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'           => $this->request->getPost('tanggal_selesai'),
            'target_partisipasi_persen' => $this->request->getPost('target_partisipasi_persen'),
            'allow_unregistered_alumni' => $this->request->getPost('allow_unregistered_alumni') ? 1 : 0,
            'deskripsi'                 => $this->request->getPost('deskripsi'),
        ];

        $result = $this->service->savePeriode($input, $this->getActorName(), $periodeId > 0 ? $periodeId : null);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit($periodeId > 0 ? 'UPDATE' : 'CREATE', 'tracer_study_periode', (string) $result['id'], null, [
                'nama_periode' => $input['nama_periode'],
            ]);
        }

        if ($periodeId === 0) {
            return redirect()->to(base_url('ketarunaan/tracer-study/periode/' . (int) $result['id'] . '/pertanyaan'))
                ->with('success', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/periode'))
            ->with('success', $result['message']);
    }

    /**
     * Kontrol Buka / Tutup / Tarik ke Draf Periode Survei (POST /ketarunaan/tracer-study/periode/{id}/status).
     */
    public function changePeriodeStatus(int $periodeId)
    {
        if (!KetarunaanPolicy::canToggleTracerStudyPeriode()) {
            return $this->denyAccess('Anda tidak memiliki wewenang membuka atau menutup survei Tracer Study.');
        }

        $targetStatus = strtolower(trim((string) ($this->request->getPost('status_periode') ?? '')));
        $result       = $this->service->changePeriodeStatus($periodeId, $targetStatus, $this->getActorName());

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            $action = match ($targetStatus) {
                'dibuka'  => 'OPEN_TRACER_PERIODE',
                'ditutup' => 'CLOSE_TRACER_PERIODE',
                default   => 'REVERT_TRACER_PERIODE_DRAFT',
            };
            log_audit($action, 'tracer_study_periode', (string) $periodeId, null, [
                'status_periode' => $targetStatus,
            ]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Buat ulang token tautan publik periode agar tautan lama tidak berlaku lagi.
     */
    public function resetPublicToken(int $periodeId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang memperbarui tautan publik survei.');
        }

        $result = $this->service->regeneratePublicToken($periodeId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('RESET_TRACER_PUBLIC_TOKEN', 'tracer_study_periode', (string) $periodeId, null, []);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Halaman Penyusunan Pertanyaan Periode (sebelum dibuka dapat diubah, setelah dibuka terkunci).
     */
    public function pertanyaanPeriode(int $periodeId)
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke pengaturan pertanyaan survei.');
        }

        $detail = $this->service->getPeriodeDetail($periodeId);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/tracer-study/periode'))
                ->with('error', 'Periode survei tidak ditemukan.');
        }

        return view('ketarunaan/tracer_study/periode_pertanyaan', [
            'title'     => 'Pertanyaan Periode ' . $detail['periode']['nama_periode'] . ' - Evidence Command Center (ECC)',
            'activeTab' => 'periode',
            'detail'    => $detail,
            'refs'      => $this->service->getReferenceOptions(),
            'perms'     => $this->getPermissions(),
        ]);
    }

    /**
     * Simpan (tambah/ubah) 1 pertanyaan pada periode berstatus Draf.
     */
    public function savePertanyaanPeriode(int $periodeId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengubah pertanyaan survei.');
        }

        $questionId = (int) ($this->request->getPost('question_id') ?? 0);
        $input      = [
            'kode_pertanyaan'    => $this->request->getPost('kode_pertanyaan'),
            'bagian'             => $this->request->getPost('bagian'),
            'urutan'             => $this->request->getPost('urutan'),
            'teks_pertanyaan'    => $this->request->getPost('teks_pertanyaan'),
            'keterangan_bantuan' => $this->request->getPost('keterangan_bantuan'),
            'tipe_jawaban'       => $this->request->getPost('tipe_jawaban'),
            'opsi_jawaban_text'  => $this->request->getPost('opsi_jawaban_text'),
            'render_as'          => $this->request->getPost('render_as'),
            'allow_other'        => $this->request->getPost('allow_other'),
            'likert_max_scale'   => $this->request->getPost('likert_max_scale'),
            'likert_min_label'   => $this->request->getPost('likert_min_label'),
            'likert_max_label'   => $this->request->getPost('likert_max_label'),
            'show_if_kode'       => $this->request->getPost('show_if_kode'),
            'show_if_values'     => $this->request->getPost('show_if_values'),
            'is_wajib'           => $this->request->getPost('is_wajib'),
        ];

        $result = $this->service->savePeriodeQuestion($periodeId, $input, $questionId > 0 ? $questionId : null);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit($questionId > 0 ? 'UPDATE' : 'CREATE', 'tracer_study_periode_pertanyaan', (string) $result['id'], null, [
                'periode_id' => $periodeId,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/periode/' . $periodeId . '/pertanyaan'))
            ->with('success', $result['message']);
    }

    /**
     * Hapus 1 pertanyaan dari periode berstatus Draf.
     */
    public function deletePertanyaanPeriode(int $periodeId, int $questionId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menghapus pertanyaan survei.');
        }

        $result = $this->service->deletePeriodeQuestion($periodeId, $questionId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('DELETE', 'tracer_study_periode_pertanyaan', (string) $questionId, null, [
                'periode_id' => $periodeId,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/periode/' . $periodeId . '/pertanyaan'))
            ->with('success', $result['message']);
    }

    /**
     * Simpan susunan pertanyaan periode ini menjadi Bundel Master baru untuk dipakai di periode lain.
     */
    public function savePeriodeAsBundle(int $periodeId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang membuat bundel pertanyaan.');
        }

        $namaBundle = trim((string) ($this->request->getPost('nama_bundle') ?? ''));
        $result     = $this->service->savePeriodeAsNewBundle($periodeId, $namaBundle, $this->getActorName());

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'tracer_study_bundle', (string) $result['id'], null, [
                'from_periode_id' => $periodeId,
                'nama_bundle'     => $namaBundle,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/bundel/' . (int) $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Pustaka Bundel Pertanyaan Master (/ketarunaan/tracer-study/bundel).
     */
    public function bundel()
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke bundel pertanyaan.');
        }

        return view('ketarunaan/tracer_study/bundel', [
            'title'     => 'Bundel Pertanyaan Tracer Study - Evidence Command Center (ECC)',
            'activeTab' => 'bundel',
            'bundles'   => $this->service->listBundles(),
            'perms'     => $this->getPermissions(),
        ]);
    }

    /**
     * Simpan (buat/ubah) Bundel Pertanyaan Master.
     */
    public function saveBundle()
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengelola bundel pertanyaan.');
        }

        $bundleId = (int) ($this->request->getPost('bundle_id') ?? 0);
        $input    = [
            'kode_bundle'      => $this->request->getPost('kode_bundle'),
            'nama_bundle'      => $this->request->getPost('nama_bundle'),
            'target_responden' => $this->request->getPost('target_responden') ?: 'alumni',
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'is_aktif'         => $this->request->getPost('is_aktif') ?? 1,
        ];

        $result = $this->service->saveBundle($input, $this->getActorName(), $bundleId > 0 ? $bundleId : null);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit($bundleId > 0 ? 'UPDATE' : 'CREATE', 'tracer_study_bundle', (string) $result['id'], null, [
                'nama_bundle' => $input['nama_bundle'],
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/bundel/' . (int) $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Duplikasi 1 bundel pertanyaan beserta seluruh isinya.
     */
    public function duplicateBundle(int $bundleId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menduplikasi bundel pertanyaan.');
        }

        $result = $this->service->duplicateBundle($bundleId, $this->getActorName());
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('CREATE', 'tracer_study_bundle', (string) $result['id'], null, [
                'duplicated_from' => $bundleId,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/bundel/' . (int) $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Rincian & Kelola Pertanyaan dalam 1 Bundel Master (/ketarunaan/tracer-study/bundel/{id}).
     */
    public function bundelDetail(int $bundleId)
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses ke detail bundel pertanyaan.');
        }

        $detail = $this->service->getBundleDetail($bundleId);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/tracer-study/bundel'))
                ->with('error', 'Bundel pertanyaan tidak ditemukan.');
        }

        return view('ketarunaan/tracer_study/bundel_detail', [
            'title'     => 'Detail Bundel: ' . $detail['bundle']['nama_bundle'] . ' - Evidence Command Center (ECC)',
            'activeTab' => 'bundel',
            'detail'    => $detail,
            'refs'      => $this->service->getReferenceOptions(),
            'perms'     => $this->getPermissions(),
        ]);
    }

    /**
     * Simpan (tambah/ubah) pertanyaan dalam Bundel Master.
     */
    public function savePertanyaanBundle(int $bundleId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang mengubah pertanyaan bundel.');
        }

        $questionId = (int) ($this->request->getPost('question_id') ?? 0);
        $input      = [
            'kode_pertanyaan'    => $this->request->getPost('kode_pertanyaan'),
            'bagian'             => $this->request->getPost('bagian'),
            'urutan'             => $this->request->getPost('urutan'),
            'teks_pertanyaan'    => $this->request->getPost('teks_pertanyaan'),
            'keterangan_bantuan' => $this->request->getPost('keterangan_bantuan'),
            'tipe_jawaban'       => $this->request->getPost('tipe_jawaban'),
            'opsi_jawaban_text'  => $this->request->getPost('opsi_jawaban_text'),
            'render_as'          => $this->request->getPost('render_as'),
            'allow_other'        => $this->request->getPost('allow_other'),
            'likert_max_scale'   => $this->request->getPost('likert_max_scale'),
            'likert_min_label'   => $this->request->getPost('likert_min_label'),
            'likert_max_label'   => $this->request->getPost('likert_max_label'),
            'show_if_kode'       => $this->request->getPost('show_if_kode'),
            'show_if_values'     => $this->request->getPost('show_if_values'),
            'is_wajib'           => $this->request->getPost('is_wajib'),
        ];

        $result = $this->service->saveBundleQuestion($bundleId, $input, $questionId > 0 ? $questionId : null);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/bundel/' . $bundleId))
            ->with('success', $result['message']);
    }

    /**
     * Hapus 1 pertanyaan dari Bundel Master.
     */
    public function deletePertanyaanBundle(int $bundleId, int $questionId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menghapus pertanyaan bundel.');
        }

        $result = $this->service->deleteBundleQuestion($bundleId, $questionId);
        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/bundel/' . $bundleId))
            ->with('success', $result['message']);
    }

    /**
     * Halaman Pemantauan Progres Responden & Alumni Belum Mengisi per Periode (/ketarunaan/tracer-study/responden).
     */
    public function responden()
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses melihat daftar responden.');
        }

        $refs        = $this->service->getReferenceOptions();
        $periodes    = $this->service->listPeriodes();
        $selectedPid = (int) ($this->request->getGet('periode_id') ?? 0);
        if ($selectedPid <= 0 && !empty($periodes)) {
            $selectedPid = (int) $periodes[0]['id'];
        }

        $filters = [
            'q'               => trim((string) ($this->request->getGet('q') ?? '')),
            'prodi_id'        => (int) ($this->request->getGet('prodi_id') ?? 0),
            'tahun_lulus'     => (int) ($this->request->getGet('tahun_lulus') ?? 0),
            'status_transisi' => trim((string) ($this->request->getGet('status_transisi') ?? 'all')),
            'sub'             => trim((string) ($this->request->getGet('sub') ?? 'sudah')),
        ];

        $data = $selectedPid > 0 ? $this->service->listRespondenAndPending($selectedPid, $filters) : null;

        return view('ketarunaan/tracer_study/responden', [
            'title'       => 'Progres & Responden Tracer Study - Evidence Command Center (ECC)',
            'activeTab'   => 'responden',
            'refs'        => $refs,
            'periodes'    => $periodes,
            'selectedPid' => $selectedPid,
            'filters'     => $filters,
            'data'        => $data,
            'perms'       => $this->getPermissions(),
        ]);
    }

    /**
     * Halaman Rincian Jawaban 1 Alumni (/ketarunaan/tracer-study/respons/{id}).
     */
    public function detailRespons(int $responsId)
    {
        if (!KetarunaanPolicy::canAccessTracerStudyModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses melihat jawaban Tracer Study.');
        }

        $isSelfAlumni = KetarunaanPolicy::isSelfAlumniOnly();
        $viewerNim    = $isSelfAlumni ? KetarunaanPolicy::resolveSelfAlumniNomorTaruna() : null;

        $detail = $this->service->getResponsDetail($responsId, $viewerNim);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/tracer-study'))
                ->with('error', 'Data jawaban Tracer Study tidak ditemukan atau di luar kewenangan Anda.');
        }

        return view('ketarunaan/tracer_study/respons_detail', [
            'title'        => 'Rincian Jawaban Tracer Study - Evidence Command Center (ECC)',
            'activeTab'    => $isSelfAlumni ? 'ringkasan' : 'responden',
            'detail'       => $detail,
            'isSelfAlumni' => $isSelfAlumni,
            'perms'        => $this->getPermissions(),
        ]);
    }

    /**
     * Menyetujui respons alumni baru & mendaftarkannya ke Data Alumni (+ opsional buatkan akun login ECC).
     */
    public function approveRespons(int $responsId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang memverifikasi respons alumni baru.');
        }

        $createAccount = !empty($this->request->getPost('create_account'));
        $result        = $this->service->approveUnregisteredRespons($responsId, $createAccount, $this->getActorName());

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('APPROVE_TRACER_ALUMNI', 'tracer_study_respons', (string) $responsId, null, [
                'create_account' => $createAccount ? 1 : 0,
            ]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Menolak respons alumni baru (data tidak valid / spam).
     */
    public function rejectRespons(int $responsId)
    {
        if (!KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Anda tidak memiliki wewenang menolak respons alumni baru.');
        }

        $alasan = trim((string) ($this->request->getPost('alasan_penolakan') ?? ''));
        $result = $this->service->rejectUnregisteredRespons($responsId, $alasan, $this->getActorName());

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('REJECT_TRACER_ALUMNI', 'tracer_study_respons', (string) $responsId, null, [
                'alasan' => $alasan,
            ]);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Formulir Pengisian Survei Tracer Study oleh Alumni atau Input PIC (/ketarunaan/tracer-study/isi/{periodeId}).
     */
    public function formIsi(int $periodeId)
    {
        if (!KetarunaanPolicy::canAccessTracerStudyModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses mengisi survei Tracer Study.');
        }

        $isSelfAlumni = KetarunaanPolicy::isSelfAlumniOnly();
        if (!$isSelfAlumni && !KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Hanya alumni sasaran atau pengelola Tracer Study yang dapat mengisi survei.');
        }

        $nomorTaruna = $isSelfAlumni
            ? KetarunaanPolicy::resolveSelfAlumniNomorTaruna()
            : trim((string) ($this->request->getGet('nomor_taruna') ?? ''));

        $ctx = $this->service->getSurveyFormContext($periodeId, $nomorTaruna, $isSelfAlumni);
        if (!$ctx['status']) {
            if (!empty($ctx['already_submitted']) && !empty($ctx['respons_id'])) {
                return redirect()->to(base_url('ketarunaan/tracer-study/respons/' . (int) $ctx['respons_id']))
                    ->with('warning', $ctx['message']);
            }
            return redirect()->to(base_url('ketarunaan/tracer-study'))
                ->with('warning', $ctx['message']);
        }

        return view('ketarunaan/tracer_study/form_isi', [
            'title'        => 'Pengisian ' . $ctx['periode']['nama_periode'] . ' - Evidence Command Center (ECC)',
            'activeTab'    => $isSelfAlumni ? 'ringkasan' : 'responden',
            'ctx'          => $ctx,
            'refs'         => $this->service->getReferenceOptions(),
            'isSelfAlumni' => $isSelfAlumni,
            'selectedNim'  => (string) ($nomorTaruna ?? ''),
            'perms'        => $this->getPermissions(),
        ]);
    }

    /**
     * Proses Simpan Jawaban Survei Alumni / PIC (POST /ketarunaan/tracer-study/isi/{periodeId}/simpan).
     */
    public function simpanRespons(int $periodeId)
    {
        if (!KetarunaanPolicy::canAccessTracerStudyModule()) {
            return $this->denyAccess('Anda tidak memiliki hak akses mengirim survei Tracer Study.');
        }

        $isSelfAlumni = KetarunaanPolicy::isSelfAlumniOnly();
        if (!$isSelfAlumni && !KetarunaanPolicy::canManageTracerStudy()) {
            return $this->denyAccess('Hanya alumni sasaran atau pengelola Tracer Study yang dapat menyimpan jawaban.');
        }

        $periodeDetail = $this->service->getPeriodeDetail($periodeId);
        if (!$periodeDetail) {
            return redirect()->to(base_url('ketarunaan/tracer-study'))->with('error', 'Periode survei tidak ditemukan.');
        }

        if (!empty($periodeDetail['periode']['is_mitra'])) {
            if ($isSelfAlumni) {
                return $this->denyAccess('Survei ini khusus untuk Pengguna Lulusan (Instansi).');
            }
            $mitraResult = $this->service->submitMitraRespons($periodeId, [
                'jawaban'       => $this->request->getPost('jawaban') ?? [],
                'jawaban_other' => $this->request->getPost('jawaban_other') ?? [],
            ], $this->getActorName(), false);

            if (!$mitraResult['status']) {
                return redirect()->back()->withInput()->with('error', $mitraResult['message']);
            }

            if (function_exists('log_audit')) {
                log_audit('SUBMIT_TRACER_MITRA', 'tracer_study_respons', (string) $mitraResult['id'], null, [
                    'periode_id'  => $periodeId,
                    'instansi'    => $mitraResult['instansi'] ?? '',
                    'nama_alumni' => $mitraResult['nama_alumni'] ?? '',
                ]);
            }

            return redirect()->to(base_url('ketarunaan/tracer-study/respons/' . (int) $mitraResult['id']))
                ->with('success', $mitraResult['message']);
        }

        $nomorTaruna = $isSelfAlumni
            ? (string) (KetarunaanPolicy::resolveSelfAlumniNomorTaruna() ?? '')
            : trim((string) ($this->request->getPost('nomor_taruna') ?? ''));

        if ($nomorTaruna === '') {
            return redirect()->back()->withInput()->with('error', 'Pilih alumni terlebih dahulu.');
        }

        $input = [
            'status_transisi'     => $this->request->getPost('status_transisi'),
            'masa_tunggu_bulan'   => $this->request->getPost('masa_tunggu_bulan'),
            'tanggal_mulai_kerja' => $this->request->getPost('tanggal_mulai_kerja'),
            'nama_instansi'       => $this->request->getPost('nama_instansi'),
            'sektor_industri'     => $this->request->getPost('sektor_industri'),
            'jabatan_posisi'      => $this->request->getPost('jabatan_posisi'),
            'kota_kerja'          => $this->request->getPost('kota_kerja'),
            'kesesuaian_bidang'   => $this->request->getPost('kesesuaian_bidang'),
            'kesesuaian_jenjang'  => $this->request->getPost('kesesuaian_jenjang'),
            'sumber_pengisian'    => $this->request->getPost('sumber_pengisian'),
            'jawaban'             => $this->request->getPost('jawaban') ?? [],
            'jawaban_other'       => $this->request->getPost('jawaban_other') ?? [],
        ];

        $result = $this->service->submitRespons($periodeId, $nomorTaruna, $input, $this->getActorName(), $isSelfAlumni);
        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('SUBMIT_TRACER_STUDY', 'tracer_study_respons', (string) $result['id'], null, [
                'periode_id'   => $periodeId,
                'nomor_taruna' => $nomorTaruna,
            ]);
        }

        return redirect()->to(base_url('ketarunaan/tracer-study/respons/' . (int) $result['id']))
            ->with('success', $result['message']);
    }

    /**
     * Ekspor Hasil Tracer Study per Periode ke CSV (/ketarunaan/tracer-study/ekspor).
     */
    public function ekspor()
    {
        if (!KetarunaanPolicy::canViewTracerStudyAnalytics()) {
            return $this->denyAccess('Anda tidak memiliki hak akses mengekspor hasil Tracer Study.');
        }

        $periodeId = (int) ($this->request->getGet('periode_id') ?? 0);
        if ($periodeId <= 0) {
            return redirect()->to(base_url('ketarunaan/tracer-study'))
                ->with('error', 'Pilih periode terlebih dahulu untuk diekspor.');
        }

        $detail = $this->service->getPeriodeDetail($periodeId);
        if (!$detail) {
            return redirect()->to(base_url('ketarunaan/tracer-study'))
                ->with('error', 'Periode survei tidak ditemukan.');
        }

        $filters = [
            'prodi_id'        => (int) ($this->request->getGet('prodi_id') ?? 0),
            'tahun_lulus'     => (int) ($this->request->getGet('tahun_lulus') ?? 0),
            'status_transisi' => trim((string) ($this->request->getGet('status_transisi') ?? 'all')),
            'q'               => trim((string) ($this->request->getGet('q') ?? '')),
        ];

        $csvRows = $this->service->getExportRows($periodeId, $filters);

        if (function_exists('log_audit')) {
            log_audit('EXPORT', 'tracer_study_respons', (string) $periodeId, null, [
                'rows' => max(0, count($csvRows) - 1),
            ]);
        }

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, "\xEF\xBB\xBF");
        foreach ($csvRows as $line) {
            fputcsv($fh, $line);
        }
        rewind($fh);
        $csvContent = stream_get_contents($fh);
        fclose($fh);

        $safeKode = preg_replace('/[^A-Za-z0-9\-_]/', '_', (string) ($detail['periode']['kode_periode'] ?? 'periode'));
        $filename = 'tracer_study_' . $safeKode . '_' . date('Ymd_His') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
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
            'can_manage'        => KetarunaanPolicy::canManageTracerStudy(),
            'can_toggle'        => KetarunaanPolicy::canToggleTracerStudyPeriode(),
            'can_view_analytic' => KetarunaanPolicy::canViewTracerStudyAnalytics(),
            'is_self_alumni'    => KetarunaanPolicy::isSelfAlumniOnly(),
        ];
    }
}
