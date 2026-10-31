<?php

namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Libraries\SurveiPemetaanConfig;
use App\Libraries\SurveiPemetaanPolicy;
use App\Models\SettingModel;
use App\Models\SurveiDelegasiModel;
use App\Models\SurveiJawabanModel;
use App\Models\SurveiResponModel;
use App\Models\UnitKerja;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HasilSurveiController extends BaseController
{
    /**
     * Dashboard Hasil Survei Pemetaan SDM & Delegasi Akses Staf Kabag KUK.
     */
    public function index()
    {
        if (!SurveiPemetaanPolicy::canViewResults()) {
            return redirect()->to(site_url('dashboard'))
                             ->with('error', 'Anda tidak memiliki akses ke halaman Hasil Survei SDM.');
        }

        helper(['avatar', 'role']);

        $activeYear     = SurveiPemetaanPolicy::getActiveSurveyYear();
        $tahunFilter    = (int) ($this->request->getGet('tahun') ?: $activeYear);
        if ($tahunFilter < 2024 || $tahunFilter > 2035) {
            $tahunFilter = $activeYear;
        }
        $unitFilter     = trim((string) ($this->request->getGet('unit') ?? ''));
        $kelompokFilter = $this->normalizeKelompokFilter((string) ($this->request->getGet('kelompok') ?? 'all'));

        $dataset = $this->prepareAnalyticsDataset($tahunFilter, $unitFilter, $kelompokFilter);

        $canManage      = SurveiPemetaanPolicy::canManageSurvey();
        $canUnlock      = SurveiPemetaanPolicy::canUnlockSurvey();
        $canExport      = SurveiPemetaanPolicy::canExportResults();
        $isSurveyOpen   = SurveiPemetaanPolicy::isSurveyOpen();
        $delegationInfo = SurveiPemetaanPolicy::getActiveDelegation();

        $delegatesList     = [];
        $delegateCandidates = [];
        if ($canManage) {
            $delegasiModel      = new SurveiDelegasiModel();
            $delegatesList      = $delegasiModel->getActiveDelegatesList();
            $delegateCandidates = $this->getEligibleDelegateCandidates();
        }

        return view('kepegawaian/hasil_survei', array_merge($dataset, [
            'title'              => 'Hasil Survei Pemetaan SDM',
            'tahunFilter'        => $tahunFilter,
            'activeSurveyYear'   => $activeYear,
            'unitFilter'         => $unitFilter,
            'kelompokFilter'     => $kelompokFilter,
            'canManage'          => $canManage,
            'canUnlock'          => $canUnlock,
            'canExport'          => $canExport,
            'isSurveyOpen'       => $isSurveyOpen,
            'delegationInfo'     => $delegationInfo,
            'delegatesList'      => $delegatesList,
            'delegateCandidates' => $delegateCandidates,
            'shareLinkUrl'       => site_url('survei-pemetaan'),
            'sectionsMeta'       => SurveiPemetaanConfig::getSections(),
            'questionsMeta'      => SurveiPemetaanConfig::getQuestions(),
        ]));
    }

    /**
     * Ambil rincian lengkap 83 jawaban seorang responden untuk Modal Preview (AJAX GET).
     */
    public function detailResponden()
    {
        if (!SurveiPemetaanPolicy::canViewResults()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        $responId = (int) $this->request->getGet('respon_id');
        if ($responId <= 0) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data responden tidak valid.',
            ], 422);
        }

        $responModel = new SurveiResponModel();
        $respon      = $responModel->find($responId);

        if (!$respon || (int) ($respon['is_valid'] ?? 1) !== 1) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data responden tidak ditemukan.',
            ], 404);
        }

        $jawabanModel = new SurveiJawabanModel();
        $answersMap   = $jawabanModel->getAnswersMap($responId);
        $sections     = SurveiPemetaanConfig::getSections();
        $questions    = SurveiPemetaanConfig::getQuestions();

        $formattedSections = [];
        foreach ($sections as $stepNo => $sec) {
            $items = [];
            foreach ($sec['questions'] as $qNo) {
                $qMeta        = $questions[$qNo] ?? null;
                if (!$qMeta) {
                    continue;
                }
                $isApplicable = SurveiPemetaanConfig::isQuestionRequired($qNo, $answersMap);
                $row          = $answersMap[$qNo] ?? null;
                $displayVal   = $row ? SurveiPemetaanConfig::formatAnswerDisplay($qNo, $row) : ($isApplicable ? 'Belum diisi' : 'Tidak berlaku');

                if ($qNo === 10 && !empty($row['jawaban_lainnya'])) {
                    $displayVal = (string) $row['jawaban_lainnya'];
                }

                $items[] = [
                    'no'         => $qNo,
                    'label'      => $qMeta['label'],
                    'applicable' => $isApplicable,
                    'answered'   => $row !== null && SurveiPemetaanConfig::isQuestionAnswered($qNo, $answersMap),
                    'display'    => $displayVal,
                ];
            }

            $formattedSections[] = [
                'step'        => $stepNo,
                'roman'       => $sec['roman'],
                'short_title' => $sec['short_title'],
                'title'       => $sec['title'],
                'items'       => $items,
            ];
        }

        return $this->jsonResponse([
            'status' => 'success',
            'respon' => [
                'id'                 => (int) $respon['id'],
                'nama'               => $respon['snapshot_nama'] ?: 'Belum Mengisi Nama',
                'nomor_identitas'    => $respon['nomor_identitas'],
                'tipe_responden'     => $respon['tipe_responden'],
                'status_kepegawaian' => $respon['snapshot_status_kepegawaian'] ?: 'ASN',
                'pangkat'            => $respon['snapshot_pangkat'] ?: '-',
                'jabatan'            => $respon['snapshot_jabatan'] ?: '-',
                'unit'               => $respon['snapshot_unit'] ?: '-',
                'status'             => $respon['status'],
                'progress_percent'   => (float) $respon['progress_percent'],
                'total_answered'     => (int) $respon['total_answered'],
                'total_applicable'   => (int) $respon['total_applicable'],
            ],
            'sections' => $formattedSections,
        ]);
    }

    /**
     * Ambil rincian isian 83 pertanyaan untuk beberapa responden terpilih (Pegawai A, B, C...)
     * secara berdampingan untuk Modal Perbandingan (AJAX GET).
     */
    public function compareResponden()
    {
        if (!SurveiPemetaanPolicy::canViewResults()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        $rawIds = (string) ($this->request->getGet('respon_ids') ?? '');
        $idList = array_values(array_filter(array_map('intval', explode(',', $rawIds)), function ($id) {
            return $id > 0;
        }));

        if (empty($idList)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Pilih minimal satu pegawai untuk melihat isian lengkap.',
            ], 422);
        }

        $idList = array_slice($idList, 0, 10);

        $responModel  = new SurveiResponModel();
        $jawabanModel = new SurveiJawabanModel();
        $sections     = SurveiPemetaanConfig::getSections();
        $questions    = SurveiPemetaanConfig::getQuestions();

        $responRows = $responModel->whereIn('id', $idList)
                                  ->where('is_valid', 1)
                                  ->findAll();

        if (empty($responRows)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data responden terpilih tidak ditemukan.',
            ], 404);
        }

        $orderedRows = [];
        $indexedRows = [];
        foreach ($responRows as $r) {
            $indexedRows[(int) $r['id']] = $r;
        }
        foreach ($idList as $reqId) {
            if (isset($indexedRows[$reqId])) {
                $orderedRows[] = $indexedRows[$reqId];
            }
        }

        $responHeaderList = [];
        $answersByRespon  = [];

        foreach ($orderedRows as $r) {
            $rid = (int) $r['id'];
            $responHeaderList[] = [
                'id'                 => $rid,
                'nama'               => $r['snapshot_nama'] ?: 'Tanpa Nama',
                'nomor_identitas'    => $r['nomor_identitas'],
                'tipe_responden'     => $r['tipe_responden'],
                'status_kepegawaian' => $r['snapshot_status_kepegawaian'] ?: 'ASN',
                'pangkat'            => $r['snapshot_pangkat'] ?: '-',
                'jabatan'            => $r['snapshot_jabatan'] ?: '-',
                'unit'               => $r['snapshot_unit'] ?: '-',
                'status'             => $r['status'],
                'progress_percent'   => (float) $r['progress_percent'],
            ];
            $answersByRespon[$rid] = $jawabanModel->getAnswersMap($rid);
        }

        $comparisonSections = [];
        foreach ($sections as $stepNo => $sec) {
            $items = [];
            foreach ($sec['questions'] as $qNo) {
                $qMeta = $questions[$qNo] ?? null;
                if (!$qMeta) {
                    continue;
                }

                $answersByPegawai = [];
                foreach ($responHeaderList as $rh) {
                    $rid          = (int) $rh['id'];
                    $ansMap       = $answersByRespon[$rid] ?? [];
                    $isApplicable = SurveiPemetaanConfig::isQuestionRequired($qNo, $ansMap);
                    $row          = $ansMap[$qNo] ?? null;
                    $displayVal   = $row ? SurveiPemetaanConfig::formatAnswerDisplay($qNo, $row) : ($isApplicable ? 'Belum diisi' : 'Tidak berlaku');

                    if ($qNo === 10 && !empty($row['jawaban_lainnya'])) {
                        $displayVal = (string) $row['jawaban_lainnya'];
                    }

                    $answersByPegawai[$rid] = [
                        'applicable' => $isApplicable,
                        'answered'   => $row !== null && SurveiPemetaanConfig::isQuestionAnswered($qNo, $ansMap),
                        'display'    => $displayVal,
                    ];
                }

                $items[] = [
                    'no'      => $qNo,
                    'label'   => $qMeta['label'],
                    'pegawai' => $answersByPegawai,
                ];
            }

            $comparisonSections[] = [
                'step'        => $stepNo,
                'roman'       => $sec['roman'],
                'short_title' => $sec['short_title'],
                'title'       => $sec['title'],
                'items'       => $items,
            ];
        }

        return $this->jsonResponse([
            'status'    => 'success',
            'responden' => $responHeaderList,
            'sections'  => $comparisonSections,
        ]);
    }

    /**
     * Tambah / Perbarui Delegasi Staf oleh Kabag KUK / Direktur / Admin.
     */
    public function storeDelegasi()
    {
        if (!SurveiPemetaanPolicy::canManageSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Hanya Kabag KUK, Direktur, atau Administrator yang dapat mengatur delegasi staf.',
            ], 403);
        }

        helper(['audit', 'notification']);

        $stafUserId = (int) $this->request->getPost('user_id');
        $canExport  = (int) ($this->request->getPost('can_export') ? 1 : 0);
        $catatan    = trim((string) ($this->request->getPost('catatan') ?? ''));
        $catatan    = mb_substr(preg_replace('/[\p{C}]+/u', ' ', $catatan) ?? $catatan, 0, 255);
        $actorId    = SurveiPemetaanPolicy::getCurrentUserId();

        if ($stafUserId <= 0) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Pilih staf yang akan diberikan akses terlebih dahulu.',
            ], 422);
        }

        if ($stafUserId === $actorId) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Anda sudah memiliki akses penuh tanpa perlu mendelegasikan akun sendiri.',
            ], 422);
        }

        $userModel = new User();
        $stafUser  = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, status_pegawai')
                               ->find($stafUserId);

        if (!$stafUser || strtolower((string) ($stafUser['status_pegawai'] ?? 'aktif')) === 'nonaktif') {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akun staf tidak ditemukan atau sedang tidak aktif.',
            ], 404);
        }

        if (in_array(strtolower((string) ($stafUser['role'] ?? '')), ['admin', 'direktur', 'kabag_kuk'], true)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Pejabat tersebut sudah memiliki akses bawaan Hasil Survei SDM.',
            ], 422);
        }

        $delegasiModel = new SurveiDelegasiModel();
        $existing      = $delegasiModel->where('user_id', $stafUserId)->first();

        $payload = [
            'user_id'      => $stafUserId,
            'delegated_by' => $actorId,
            'can_export'   => $canExport,
            'catatan'      => $catatan !== '' ? $catatan : null,
            'is_active'    => 1,
        ];

        if ($existing) {
            $delegasiModel->update((int) $existing['id'], $payload);
            $delegasiId = (int) $existing['id'];
        } else {
            $delegasiId = (int) $delegasiModel->insert($payload, true);
        }

        SurveiPemetaanPolicy::clearDelegationCache();

        try {
            $actorName = (string) (session()->get('nama') ?? 'Kabag KUK');
            send_notification(
                $stafUserId,
                'Akses Hasil Survei SDM',
                $actorName . ' memberikan akses kepada Anda untuk membantu memantau Hasil Survei Pemetaan SDM.',
                'kepegawaian/survei-pemetaan'
            );
        } catch (\Throwable $e) {
            // Failsafe notifikasi
        }

        log_audit('GRANT_DELEGASI_SURVEI', 'survei_pemetaan_delegasi', (string) $delegasiId, null, [
            'staf_user_id' => $stafUserId,
            'staf_nama'    => $stafUser['nama_lengkap'],
            'can_export'   => $canExport,
        ]);

        return $this->jsonResponse([
            'status'    => 'success',
            'message'   => 'Akses hasil survei berhasil diberikan kepada ' . $stafUser['nama_lengkap'] . '.',
            'delegates' => $delegasiModel->getActiveDelegatesList(),
        ]);
    }

    /**
     * Cabut Akses Delegasi Staf.
     */
    public function revokeDelegasi()
    {
        if (!SurveiPemetaanPolicy::canManageSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        helper('audit');

        $delegasiId    = (int) $this->request->getPost('delegasi_id');
        $delegasiModel = new SurveiDelegasiModel();
        $row           = $delegasiModel->find($delegasiId);

        if (!$row) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data delegasi tidak ditemukan.',
            ], 404);
        }

        $delegasiModel->update($delegasiId, [
            'is_active' => 0,
        ]);

        SurveiPemetaanPolicy::clearDelegationCache();

        log_audit('REVOKE_DELEGASI_SURVEI', 'survei_pemetaan_delegasi', (string) $delegasiId, [
            'is_active' => 1,
        ], [
            'is_active'    => 0,
            'staf_user_id' => $row['user_id'],
        ]);

        return $this->jsonResponse([
            'status'    => 'success',
            'message'   => 'Akses delegasi staf berhasil dicabut.',
            'delegates' => $delegasiModel->getActiveDelegatesList(),
        ]);
    }

    /**
     * Buka / Tutup Periode Pengisian Tautan Survei.
     */
    public function togglePeriode()
    {
        if (!SurveiPemetaanPolicy::canManageSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        helper('audit');

        $newState = ((string) $this->request->getPost('is_open') === '1') ? '1' : '0';
        $oldState = SurveiPemetaanPolicy::isSurveyOpen() ? '1' : '0';

        SettingModel::saveSetting('enable_survei_pemetaan', $newState);

        log_audit('TOGGLE_PERIODE_SURVEI', 'settings', 'enable_survei_pemetaan', [
            'enable_survei_pemetaan' => $oldState,
        ], [
            'enable_survei_pemetaan' => $newState,
        ]);

        return $this->jsonResponse([
            'status'  => 'success',
            'is_open' => $newState === '1',
            'message' => $newState === '1'
                ? 'Tautan pengisian survei kini dibuka.'
                : 'Tautan pengisian survei telah ditutup.',
        ]);
    }

    /**
     * Terapkan Usulan Pelengkapan Profil Pegawai ASN (pending_profile_sync = 1) ke tabel users.
     */
    public function syncProfil()
    {
        if (!SurveiPemetaanPolicy::canManageSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        $mode        = (string) ($this->request->getPost('mode') ?: 'single');
        $responId    = (int) $this->request->getPost('respon_id');
        $responModel = new SurveiResponModel();

        if ($mode === 'all') {
            $rows = $responModel->where('tipe_responden', 'akun_ecc')
                                ->where('pending_profile_sync', 1)
                                ->where('is_valid', 1)
                                ->findAll();
        } else {
            $single = $responId > 0 ? $responModel->find($responId) : null;
            $rows   = ($single && ($single['tipe_responden'] ?? '') === 'akun_ecc') ? [$single] : [];
        }

        if (empty($rows)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Tidak ada antrean pembaruan profil yang perlu diterapkan.',
            ], 404);
        }

        $syncedCount = 0;
        foreach ($rows as $r) {
            $uid = (int) ($r['user_id'] ?? 0);
            if ($uid > 0) {
                $res = SurveiPemetaanPolicy::syncEmptyProfileFields($uid, $r, false, true);
                if (!empty($res['updated'])) {
                    $syncedCount++;
                }
            }
        }

        return $this->jsonResponse([
            'status'       => 'success',
            'synced_count' => $syncedCount,
            'message'      => $syncedCount > 0
                ? 'Data profil kosong pada ' . $syncedCount . ' akun pegawai berhasil diperbarui.'
                : 'Antrean sinkronisasi telah dibersihkan (profil pegawai sudah lengkap).',
        ]);
    }

    /**
     * Reset Draf (Hanya untuk status = 'draft') — membantu responden Non-ASN yang kehilangan sesi peramban.
     */
    public function resetDraf()
    {
        if (!SurveiPemetaanPolicy::canViewResults()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akses tidak diizinkan.',
            ], 403);
        }

        helper('audit');

        $responId    = (int) $this->request->getPost('respon_id');
        $responModel = new SurveiResponModel();
        $respon      = $responModel->find($responId);

        if (!$respon || (int) ($respon['is_valid'] ?? 1) !== 1) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Draf responden tidak ditemukan.',
            ], 404);
        }

        if (($respon['status'] ?? '') !== 'draft') {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Survei yang sudah terkirim tidak dapat direset dengan tombol ini.',
            ], 422);
        }

        $db           = \Config\Database::connect();
        $jawabanModel = new SurveiJawabanModel();

        try {
            $db->transStart();
            $jawabanModel->where('respon_id', $responId)->delete();
            $responModel->delete($responId);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->jsonResponse([
                    'status'  => 'error',
                    'message' => 'Gagal mereset draf. Silakan coba lagi.',
                ], 500);
            }

            log_audit('RESET_DRAF_SURVEI', 'survei_pemetaan_respon', (string) $responId, [
                'nomor_identitas' => $respon['nomor_identitas'],
                'tipe_responden'  => $respon['tipe_responden'],
            ], null);

            return $this->jsonResponse([
                'status'  => 'success',
                'message' => 'Draf berhasil direset. Pegawai dapat mulai mengisi kembali melalui tautan survei.',
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Gagal mereset draf. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Buka Edit Survei Terkirim (Mengembalikan status 'submitted' menjadi 'draft' agar responden dapat mengedit isiannya).
     * Diizinkan bagi Admin, Kabag KUK, Direktur, serta staf yang didelegasikan.
     */
    public function bukaKunci()
    {
        if (!SurveiPemetaanPolicy::canUnlockSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses untuk membuka edit survei.',
            ], 403);
        }

        helper(['audit', 'notification']);

        $responId    = (int) $this->request->getPost('respon_id');
        $responModel = new SurveiResponModel();
        $respon      = $responModel->find($responId);

        if (!$respon || (int) ($respon['is_valid'] ?? 1) !== 1) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data survei tidak ditemukan.',
            ], 404);
        }

        if (($respon['status'] ?? '') !== 'submitted') {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Survei ini sudah dalam status dapat diedit (draf).',
            ], 422);
        }

        $responModel->update($responId, [
            'status'       => 'draft',
            'last_step'    => 1,
            'submitted_at' => null,
        ]);

        if (!empty($respon['user_id'])) {
            try {
                send_notification(
                    (int) $respon['user_id'],
                    'Survei SDM Dapat Diedit Kembali',
                    'Isian Survei Pemetaan SDM Anda telah dibuka kembali agar dapat diperbaiki.',
                    'survei-pemetaan'
                );
            } catch (\Throwable $e) {
                // Failsafe notifikasi
            }
        }

        log_audit('UNLOCK_SURVEI_PEMETAAN', 'survei_pemetaan_respon', (string) $responId, [
            'status' => 'submitted',
        ], [
            'status'          => 'draft',
            'nomor_identitas' => $respon['nomor_identitas'],
            'via_delegasi'    => SurveiPemetaanPolicy::canManageSurvey() ? 0 : 1,
        ]);

        return $this->jsonResponse([
            'status'  => 'success',
            'message' => 'Survei berhasil dibuka kembali. Pegawai kini dapat mengedit isiannya.',
        ]);
    }

    /**
     * Arsipkan entri tidak valid / duplikat salah ketik NIK (is_valid = 0).
     */
    public function arsipkanRespon()
    {
        if (!SurveiPemetaanPolicy::canManageSurvey()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Hanya pengelola utama yang dapat mengarsipkan entri tidak valid.',
            ], 403);
        }

        helper('audit');

        $responId    = (int) $this->request->getPost('respon_id');
        $responModel = new SurveiResponModel();
        $respon      = $responModel->find($responId);

        if (!$respon) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data responden tidak ditemukan.',
            ], 404);
        }

        $responModel->update($responId, [
            'is_valid'             => 0,
            'pending_profile_sync' => 0,
        ]);

        log_audit('ARCHIVE_RESPON_SURVEI', 'survei_pemetaan_respon', (string) $responId, [
            'is_valid' => 1,
        ], [
            'is_valid'        => 0,
            'nomor_identitas' => $respon['nomor_identitas'],
            'nama'            => $respon['snapshot_nama'],
        ]);

        return $this->jsonResponse([
            'status'  => 'success',
            'message' => 'Entri berhasil diarsipkan dari rekapitulasi.',
        ]);
    }

    /**
     * Ekspor Rekapitulasi Multi-Sheet Excel (.xlsx):
     * - Sheet 1: Rekap Keterisian Unit Kerja
     * - Sheet 2: Peta Kontributor BLU & Talenta
     * - Sheet 3: Isian Lengkap Vertikal Pegawai (Pegawai A, B, C... berdampingan No. 1 - 83)
     * - Sheet 4: Master Jawaban 83 Butir (Format Matriks Horizontal)
     * - Sheet 5: Ringkasan Analitik SDM & Hambatan Produktivitas
     */
    public function exportExcel()
    {
        if (!SurveiPemetaanPolicy::canExportResults()) {
            return redirect()->to(site_url('kepegawaian/survei-pemetaan'))
                             ->with('error', 'Anda tidak memiliki izin mengunduh rekapitulasi Excel.');
        }

        helper('audit');

        $activeYear     = SurveiPemetaanPolicy::getActiveSurveyYear();
        $tahunFilter    = (int) ($this->request->getGet('tahun') ?: $activeYear);
        $unitFilter     = trim((string) ($this->request->getGet('unit') ?? ''));
        $kelompokFilter = $this->normalizeKelompokFilter((string) ($this->request->getGet('kelompok') ?? 'all'));

        $dataset   = $this->prepareAnalyticsDataset($tahunFilter, $unitFilter, $kelompokFilter, true);
        $questions = SurveiPemetaanConfig::getQuestions();

        // Dukungan Filter Responden Terpilih (Pegawai A, B, C...)
        $rawIds      = (string) ($this->request->getGet('respon_ids') ?? '');
        $selectedIds = array_values(array_filter(array_map('intval', explode(',', $rawIds)), function ($id) {
            return $id > 0;
        }));

        if (!empty($selectedIds)) {
            $dataset['daftarResponden'] = array_values(array_filter($dataset['daftarResponden'], function ($r) use ($selectedIds) {
                return in_array((int) $r['id'], $selectedIds, true);
            }));
            $dataset['talentPool'] = array_values(array_filter($dataset['talentPool'], function ($tp) use ($selectedIds) {
                return in_array((int) $tp['respon_id'], $selectedIds, true);
            }));
            $dataset['daftarKontributorBlu'] = array_values(array_filter($dataset['daftarKontributorBlu'], function ($kb) use ($selectedIds) {
                return in_array((int) $kb['respon_id'], $selectedIds, true);
            }));
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Evidence Command Center (ECC) - PKTJ Tegal')
                    ->setTitle('Rekap Survei Pemetaan SDM ASN PKTJ Tahun ' . $tahunFilter);

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ];

        $cellBorderStyle = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ];

        // =====================================================================
        // SHEET 1: REKAP KETERISIAN ASN PER UNIT KERJA
        // =====================================================================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Keterisian Unit');

        $sheet1->setCellValue('A1', 'REKAPITULASI KETERISIAN SURVEI PEMETAAN SDM ASN PKTJ TAHUN ' . $tahunFilter);
        $sheet1->mergeCells('A1:G1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $filterNote = 'Filter Unit: ' . ($unitFilter !== '' ? $unitFilter : 'Seluruh Unit Kerja')
                    . ' | Kelompok: ' . ($kelompokFilter !== 'all' ? strtoupper($kelompokFilter) : 'Semua ASN')
                    . (!empty($selectedIds) ? ' | Terpilih Khusus: ' . count($selectedIds) . ' Pegawai' : '');
        $sheet1->setCellValue('A2', $filterNote);
        $sheet1->mergeCells('A2:G2');

        $s1Headers = ['No', 'Unit Kerja', 'Pegawai Wajib Isi', 'Terkirim', 'Draf', 'Belum Mengisi', '% Keterisian'];
        foreach ($s1Headers as $idx => $hText) {
            $col = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet1->setCellValue($col . '4', $hText);
        }
        $sheet1->getStyle('A4:G4')->applyFromArray($headerStyle);
        $sheet1->getRowDimension(4)->setRowHeight(28);

        $rowNum = 5;
        $no     = 1;
        foreach ($dataset['rekapUnit'] as $ru) {
            $sheet1->setCellValue('A' . $rowNum, $no++);
            $sheet1->setCellValue('B' . $rowNum, $ru['nama_unit']);
            $sheet1->setCellValue('C' . $rowNum, $ru['asn_wajib']);
            $sheet1->setCellValue('D' . $rowNum, $ru['asn_submitted']);
            $sheet1->setCellValue('E' . $rowNum, $ru['asn_draft']);
            $sheet1->setCellValue('F' . $rowNum, $ru['asn_belum']);
            $sheet1->setCellValue('G' . $rowNum, $ru['asn_persen'] . '%');
            $rowNum++;
        }

        // Baris Total
        $kpi = $dataset['kpi'];
        $sheet1->setCellValue('A' . $rowNum, '');
        $sheet1->setCellValue('B' . $rowNum, 'TOTAL KESELURUHAN');
        $sheet1->setCellValue('C' . $rowNum, $kpi['asn_wajib']);
        $sheet1->setCellValue('D' . $rowNum, $kpi['asn_submitted']);
        $sheet1->setCellValue('E' . $rowNum, $kpi['asn_draft']);
        $sheet1->setCellValue('F' . $rowNum, $kpi['asn_belum']);
        $sheet1->setCellValue('G' . $rowNum, $kpi['asn_persen'] . '%');
        $sheet1->getStyle('A5:G' . $rowNum)->applyFromArray($cellBorderStyle);
        $sheet1->getStyle('A' . $rowNum . ':G' . $rowNum)->getFont()->setBold(true);

        $s1Widths = ['A' => 6, 'B' => 42, 'C' => 16, 'D' => 15, 'E' => 13, 'F' => 18, 'G' => 18];
        foreach ($s1Widths as $c => $w) {
            $sheet1->getColumnDimension($c)->setWidth($w);
        }

        // =====================================================================
        // SHEET 2: PETA KONTRIBUTOR LAYANAN BLU & PETA TALENTA
        // =====================================================================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Peta Kontributor BLU');

        $sheet2->setCellValue('A1', 'PEMETAAN KONTRIBUTOR LAYANAN BLU & PETA TALENTA ASN PKTJ TAHUN ' . $tahunFilter);
        $sheet2->mergeCells('A1:R1');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $filterNote2 = 'Filter Unit: ' . ($unitFilter !== '' ? $unitFilter : 'Seluruh Unit Kerja')
                     . ' | Kelompok: ' . ($kelompokFilter !== 'all' ? strtoupper($kelompokFilter) : 'Semua ASN')
                     . (!empty($selectedIds) ? ' | Terpilih Khusus: ' . count($selectedIds) . ' Pegawai' : '');
        $sheet2->setCellValue('A2', $filterNote2);
        $sheet2->mergeCells('A2:R2');

        $s2Headers = [
            'No',
            'Nama Lengkap',
            'NIP',
            'Unit Kerja',
            'Jabatan',
            'Prioritas BLU',
            'Skor Kesiapan (%)',
            'Minat Layanan BLU (Q60)',
            'Kesiapan Target BLU (Q66)',
            'Bidang Layanan BLU (Q61)',
            'Pengalaman Bisnis/Komersial (Q62)',
            'Kesediaan Cari Mitra (Q63)',
            'Jejaring Eksternal (Q64)',
            'Bentuk Kontribusi Realistis (Q67)',
            'Keahlian Tambahan (Q22)',
            'Skor Keahlian (Q23)',
            'Kapasitas Tambahan (Q70)',
            'Kesediaan Kerja Lebih (Q33)',
        ];

        foreach ($s2Headers as $idx => $hText) {
            $colStr = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet2->setCellValue($colStr . '3', $hText);
            $sheet2->getColumnDimension($colStr)->setWidth($idx === 0 ? 6 : 24);
        }
        $sheet2->getStyle('A3:R3')->applyFromArray($headerStyle);
        $sheet2->getRowDimension(3)->setRowHeight(32);

        $r2       = 4;
        $noTalent = 1;
        $kontributorList = !empty($dataset['daftarKontributorBlu']) ? $dataset['daftarKontributorBlu'] : $dataset['talentPool'];

        foreach ($kontributorList as $kb) {
            $sheet2->setCellValue('A' . $r2, $noTalent++);
            $sheet2->setCellValue('B' . $r2, $kb['nama']);
            $sheet2->setCellValueExplicit('C' . $r2, (string) ($kb['nip'] ?? ''), DataType::TYPE_STRING);
            $sheet2->setCellValue('D' . $r2, $kb['unit'] ?? '-');
            $sheet2->setCellValue('E' . $r2, $kb['jabatan'] ?? '-');
            $sheet2->setCellValue('F' . $r2, $kb['tier_label'] ?? '-');
            $sheet2->setCellValue('G' . $r2, ($kb['skor_blu'] ?? 0) . '%');
            $sheet2->setCellValue('H' . $r2, $kb['q60_minat'] ?? ($kb['q60_minat_blu'] ?? '-'));
            $sheet2->setCellValue('I' . $r2, $kb['q66_target'] ?? ($kb['q66_target_blu'] ?? '-'));
            $sheet2->setCellValue('J' . $r2, !empty($kb['q61_list']) ? implode('; ', $kb['q61_list']) : '-');
            $sheet2->setCellValue('K' . $r2, $kb['q62_bisnis'] ?? '-');
            $sheet2->setCellValue('L' . $r2, $kb['q63_mitra'] ?? '-');
            $sheet2->setCellValue('M' . $r2, $kb['q64_jejaring'] ?? '-');
            $sheet2->setCellValue('N' . $r2, $kb['q67_kontribusi'] ?? '-');
            $sheet2->setCellValue('O' . $r2, $kb['q22_keahlian'] ?: '-');
            $sheet2->setCellValue('P' . $r2, !empty($kb['q23_skor']) && $kb['q23_skor'] > 0 ? ($kb['q23_skor'] . '/5') : '-');
            $sheet2->setCellValue('Q' . $r2, $kb['q70_kapasitas'] ?? '-');
            $sheet2->setCellValue('R' . $r2, $kb['q33_lembur'] ?? ($kb['q33_jam_lebih'] ?? '-'));
            $r2++;
        }

        if ($r2 > 4) {
            $sheet2->getStyle('A4:R' . ($r2 - 1))->applyFromArray($cellBorderStyle);
        }

        // =====================================================================
        // SHEET 3: ISIAN LENGKAP VERTIKAL PER PEGAWAI (PEGAWAI A, B, C...)
        // =====================================================================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Isian Vertikal Pegawai');

        $sheet3->setCellValue('A1', 'REKAPITULASI ISIAN LENGKAP 83 PERTANYAAN SURVEI PEMETAAN SDM ASN PKTJ');
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $filterNote3 = 'Tahun Survei: ' . $tahunFilter . ' | Menampilkan isian lengkap seluruh butir pertanyaan secara vertikal untuk setiap pegawai.'
                     . (!empty($selectedIds) ? ' | Terpilih Khusus: ' . count($selectedIds) . ' Pegawai' : '');
        $sheet3->setCellValue('A2', $filterNote3);

        // Fixed columns A, B, C
        $sheet3->setCellValue('A3', 'NO');
        $sheet3->setCellValue('B3', 'BAGIAN');
        $sheet3->setCellValue('C3', 'BUTIR PERTANYAAN');
        $sheet3->getColumnDimension('A')->setWidth(6);
        $sheet3->getColumnDimension('B')->setWidth(18);
        $sheet3->getColumnDimension('C')->setWidth(46);

        // Header Pegawai (Col D onwards)
        $targetPegawaiList = $dataset['daftarResponden'];
        $colIdx = 4;
        foreach ($targetPegawaiList as $p) {
            $colStr = Coordinate::stringFromColumnIndex($colIdx);
            $sheet3->getColumnDimension($colStr)->setWidth(36);

            $headerText = $p['nama'] . "\n"
                        . 'NIP: ' . $p['nomor_identitas'] . "\n"
                        . $p['jabatan'] . "\n"
                        . $p['unit'] . "\n"
                        . '[' . ($p['status'] === 'submitted' ? 'Terkirim' : 'Draf') . ' - ' . $p['progress_percent'] . '%]';
            $sheet3->setCellValue($colStr . '3', $headerText);
            $colIdx++;
        }

        $lastColVertikal = Coordinate::stringFromColumnIndex(max(4, $colIdx - 1));
        $sheet3->getStyle('A3:' . $lastColVertikal . '3')->applyFromArray($headerStyle);
        $sheet3->getRowDimension(3)->setRowHeight(72);

        $vRow = 4;
        $allAnswersByRespon = $dataset['allAnswersByRespon'] ?? [];
        $sections = SurveiPemetaanConfig::getSections();

        foreach ($sections as $stepNo => $sec) {
            // Section Band Row
            $sheet3->setCellValue('A' . $vRow, 'BAGIAN ' . $sec['roman']);
            $sheet3->setCellValue('B' . $vRow, $sec['short_title']);
            $sheet3->setCellValue('C' . $vRow, $sec['title']);
            $sheet3->mergeCells('C' . $vRow . ':' . $lastColVertikal . $vRow);
            $sheet3->getStyle('A' . $vRow . ':' . $lastColVertikal . $vRow)->applyFromArray([
                'font'    => ['bold' => true, 'color' => ['rgb' => '1E40AF'], 'size' => 10],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
            ]);
            $sheet3->getRowDimension($vRow)->setRowHeight(24);
            $vRow++;

            foreach ($sec['questions'] as $qNo) {
                $qMeta = $questions[$qNo] ?? null;
                if (!$qMeta) {
                    continue;
                }

                $sheet3->setCellValue('A' . $vRow, $qNo);
                $sheet3->setCellValue('B' . $vRow, 'Bagian ' . $sec['roman']);
                $sheet3->setCellValue('C' . $vRow, 'Q' . $qNo . '. ' . $qMeta['label']);

                $pCol = 4;
                foreach ($targetPegawaiList as $p) {
                    $cStr   = Coordinate::stringFromColumnIndex($pCol++);
                    $rid    = (int) $p['id'];
                    $ansMap = $allAnswersByRespon[$rid] ?? [];
                    $qRow   = $ansMap[$qNo] ?? null;
                    $valStr = $qRow ? SurveiPemetaanConfig::formatAnswerDisplay($qNo, $qRow) : '-';
                    if ($qNo === 10 && !empty($qRow['jawaban_lainnya'])) {
                        $valStr = (string) $qRow['jawaban_lainnya'];
                    }

                    if ($qNo === 2) {
                        $sheet3->setCellValueExplicit($cStr . $vRow, (string) $valStr, DataType::TYPE_STRING);
                    } else {
                        $sheet3->setCellValue($cStr . $vRow, $valStr);
                    }
                }
                $vRow++;
            }
        }

        if ($vRow > 4) {
            $sheet3->getStyle('A4:' . $lastColVertikal . ($vRow - 1))->applyFromArray($cellBorderStyle);
        }

        // =====================================================================
        // SHEET 4: MASTER JAWABAN LENGKAP 83 PERTANYAAN (MATRIKS HORIZONTAL)
        // =====================================================================
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Master Jawaban 83 Butir');

        $fixedCols = [
            'No',
            'Status Pengiriman',
            'Status Kepegawaian',
            'Nama Lengkap',
            'NIP',
            'Pangkat / Golongan',
            'Jabatan',
            'Unit Kerja',
            'Progres (%)',
        ];

        $colIdx = 1;
        foreach ($fixedCols as $fc) {
            $colStr = Coordinate::stringFromColumnIndex($colIdx++);
            $sheet4->setCellValue($colStr . '1', $fc);
            $sheet4->getColumnDimension($colStr)->setWidth($colIdx === 5 ? 28 : 22);
        }
        $sheet4->getColumnDimension('A')->setWidth(6);

        for ($qNo = 1; $qNo <= 83; $qNo++) {
            $colStr = Coordinate::stringFromColumnIndex($colIdx++);
            $qLabel = $questions[$qNo]['label'] ?? ('Pertanyaan ' . $qNo);
            $sheet4->setCellValue($colStr . '1', 'Q' . $qNo . '. ' . $qLabel);
            $sheet4->getColumnDimension($colStr)->setWidth(30);
        }

        $lastColSheet4 = Coordinate::stringFromColumnIndex($colIdx - 1);
        $sheet4->getStyle('A1:' . $lastColSheet4 . '1')->applyFromArray($headerStyle);
        $sheet4->getRowDimension(1)->setRowHeight(36);

        $r4       = 2;
        $noMaster = 1;

        foreach ($dataset['daftarResponden'] as $resp) {
            $rid    = (int) $resp['id'];
            $ansMap = $allAnswersByRespon[$rid] ?? [];

            $sheet4->setCellValue('A' . $r4, $noMaster++);
            $sheet4->setCellValue('B' . $r4, $resp['status'] === 'submitted' ? 'Terkirim' : 'Draf');
            $sheet4->setCellValue('C' . $r4, $resp['status_kepegawaian']);
            $sheet4->setCellValue('D' . $r4, $resp['nama']);
            $sheet4->setCellValueExplicit('E' . $r4, (string) $resp['nomor_identitas'], DataType::TYPE_STRING);
            $sheet4->setCellValue('F' . $r4, $resp['pangkat']);
            $sheet4->setCellValue('G' . $r4, $resp['jabatan']);
            $sheet4->setCellValue('H' . $r4, $resp['unit']);
            $sheet4->setCellValue('I' . $r4, $resp['progress_percent'] . '%');

            $qCol = 10;
            for ($qNo = 1; $qNo <= 83; $qNo++) {
                $colStr = Coordinate::stringFromColumnIndex($qCol++);
                $qRow   = $ansMap[$qNo] ?? null;
                $valStr = $qRow ? SurveiPemetaanConfig::formatAnswerDisplay($qNo, $qRow) : '-';
                if ($qNo === 10 && !empty($qRow['jawaban_lainnya'])) {
                    $valStr = (string) $qRow['jawaban_lainnya'];
                }
                if ($qNo === 2) {
                    $sheet4->setCellValueExplicit($colStr . $r4, (string) $valStr, DataType::TYPE_STRING);
                } else {
                    $sheet4->setCellValue($colStr . $r4, $valStr);
                }
            }
            $r4++;
        }

        if ($r4 > 2) {
            $sheet4->getStyle('A2:' . $lastColSheet4 . ($r4 - 1))->applyFromArray($cellBorderStyle);
        }

        // =====================================================================
        // SHEET 5: RINGKASAN ANALITIK SDM & HAMBATAN PRODUKTIVITAS
        // =====================================================================
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('Ringkasan Analitik SDM');

        $sheet5->setCellValue('A1', 'RINGKASAN ANALITIK SDM & HAMBATAN PRODUKTIVITAS ASN PKTJ TAHUN ' . $tahunFilter);
        $sheet5->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet5->setCellValue('A3', '10 KOMPETENSI UTAMA (Q20)');
        $sheet5->mergeCells('A3:B3');
        $sheet5->setCellValue('A4', 'Bidang Kompetensi');
        $sheet5->setCellValue('B4', 'Jumlah');
        $sheet5->getStyle('A3:B4')->getFont()->setBold(true);

        $r5 = 5;
        foreach (($dataset['charts']['top_kompetensi'] ?? []) as $komp => $cnt) {
            $sheet5->setCellValue('A' . $r5, $komp);
            $sheet5->setCellValue('B' . $r5, $cnt);
            $r5++;
        }

        $r5 += 2;
        $sheet5->setCellValue('A' . $r5, 'PERINGKAT HAMBATAN PRODUKTIVITAS (Q57)');
        $sheet5->mergeCells('A' . $r5 . ':C' . $r5);
        $sheet5->setCellValue('A' . ($r5 + 1), 'Faktor Penghambat');
        $sheet5->setCellValue('B' . ($r5 + 1), 'Jumlah');
        $sheet5->setCellValue('C' . ($r5 + 1), 'Persentase');
        $sheet5->getStyle('A' . $r5 . ':C' . ($r5 + 1))->getFont()->setBold(true);

        $r5 += 2;
        foreach (($dataset['insights']['hambatan_ranked'] ?? []) as $h) {
            $sheet5->setCellValue('A' . $r5, $h['label']);
            $sheet5->setCellValue('B' . $r5, $h['count']);
            $sheet5->setCellValue('C' . $r5, $h['percent'] . '%');
            $r5++;
        }

        $sheet5->getColumnDimension('A')->setWidth(48);
        $sheet5->getColumnDimension('B')->setWidth(14);
        $sheet5->getColumnDimension('C')->setWidth(16);

        $spreadsheet->setActiveSheetIndex(0);

        log_audit('EXPORT_EXCEL_SURVEI_PEMETAAN', 'survei_pemetaan_respon', (string) $tahunFilter, null, [
            'tahun'        => $tahunFilter,
            'unit'         => $unitFilter ?: 'all',
            'kelompok'     => $kelompokFilter,
            'selected_ids' => $selectedIds,
        ]);

        $filename = !empty($selectedIds)
            ? 'Isian_Lengkap_Pegawai_Terpilih_' . $tahunFilter . '_' . date('Ymd_His') . '.xlsx'
            : 'Rekap_Survei_Pemetaan_SDM_PKTJ_' . $tahunFilter . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // HELPER PRIVAT: AGREGASI ANALITIK & POPULASI TANPA DISTORSI
    // =========================================================================

    private function jsonResponse(array $payload, int $statusCode = 200)
    {
        $payload['csrf_token'] = csrf_token();
        $payload['csrf_hash']  = csrf_hash();

        return $this->response->setStatusCode($statusCode)->setJSON($payload);
    }

    /**
     * Ambil daftar pegawai aktif (kolom selektif tanpa password) untuk dropdown Select2 Delegasi Staf.
     */
    private function getEligibleDelegateCandidates(): array
    {
        $db           = \Config\Database::connect();
        $adminUserIds = [];
        if ($db->tableExists('user_roles')) {
            $adminUserIds = array_column(
                $db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
                'user_id'
            );
        }

        $userModel = new User();
        $builder   = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, status_pegawai, tmt_selesai')
                               ->whereNotIn('role', ['admin', 'direktur', 'kabag_kuk'])
                               ->where('status_pegawai !=', 'nonaktif')
                               ->orderBy('nama_lengkap', 'ASC');

        if (!empty($adminUserIds)) {
            $builder->whereNotIn('id', $adminUserIds);
        }

        $rows = $builder->findAll();
        return array_values(array_filter($rows, static fn($u) => SurveiPemetaanPolicy::isUserEligibleForSurvey($u)));
    }

    /**
     * Menyusun dataset analitik lengkap untuk 4 Kartu KPI, 3 Tab Analitik, dan Ekspor Excel.
     */
    private function prepareAnalyticsDataset(int $tahun, string $unitFilter, string $kelompokFilter, bool $includeFullAnswerMap = false): array
    {
        $db        = \Config\Database::connect();
        $unitModel = new UnitKerja();
        $userModel = new User();

        // 1. Master Unit Kerja Resmi PKTJ
        $unitRows = $unitModel->select('id, nama_unit')->orderBy('nama_unit', 'ASC')->findAll();
        $unitList = array_values(array_unique(array_filter(array_map(static fn($u) => trim((string) $u['nama_unit']), $unitRows))));

        // 2. Populasi Pegawai ASN Wajib Isi (Kolom Selektif, Tanpa Admin & Tanpa Akun Nonaktif/Purna)
        $adminPivotIds = [];
        if ($db->tableExists('user_roles')) {
            $adminPivotIds = array_column(
                $db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
                'user_id'
            );
        }

        $uBuilder = $userModel->select('id, nama_lengkap, nip, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai, foto')
                              ->where('role !=', 'admin')
                              ->orderBy('nama_lengkap', 'ASC');
        if (!empty($adminPivotIds)) {
            $uBuilder->whereNotIn('id', $adminPivotIds);
        }

        $rawAsnUsers      = $uBuilder->findAll();
        $eligibleAsnById  = [];
        $eligibleAsnByNip = [];

        foreach ($rawAsnUsers as $u) {
            if (!SurveiPemetaanPolicy::isUserEligibleForSurvey($u)) {
                continue;
            }
            $roleLow = strtolower((string) ($u['role'] ?? 'user'));
            $uUnit   = trim((string) ($u['unit'] ?? ''));
            if (($uUnit === '' || $uUnit === '-') && in_array($roleLow, ['direktur', 'wadir'], true)) {
                $uUnit = 'Pimpinan Institusi';
            } elseif ($uUnit === '' || $uUnit === '-') {
                $uUnit = 'Belum Ditentukan';
            }
            $u['resolved_unit'] = $uUnit;

            if (!in_array($uUnit, $unitList, true) && $uUnit !== 'Belum Ditentukan') {
                $unitList[] = $uUnit;
            }

            if ($unitFilter !== '' && $uUnit !== $unitFilter) {
                continue;
            }

            $uid                    = (int) $u['id'];
            $eligibleAsnById[$uid]  = $u;
            $cleanNip               = preg_replace('/\D+/', '', (string) ($u['nip'] ?? ''));
            if ($cleanNip !== '') {
                $eligibleAsnByNip[$cleanNip] = $uid;
            }
        }

        // 3. Seluruh Respon Survei Pegawai ASN Valid pada Tahun Terpilih
        $responModel = new SurveiResponModel();
        $rawRespons  = $responModel->where('tahun_survei', $tahun)
                                   ->where('tipe_responden', 'akun_ecc')
                                   ->where('is_valid', 1)
                                   ->orderBy('updated_at', 'DESC')
                                   ->findAll();

        $asnResponByUserId  = [];
        $filteredResponRows = [];
        $pendingSyncList    = [];
        $submittedResponIds = [];
        $allValidResponIds  = [];

        // Siapkan kerangka rekap per unit kerja (100% Populasi ASN)
        $rekapUnitMap = [];
        foreach ($unitList as $uName) {
            if ($unitFilter !== '' && $uName !== $unitFilter) {
                continue;
            }
            $rekapUnitMap[$uName] = [
                'nama_unit'     => $uName,
                'asn_wajib'     => 0,
                'asn_submitted' => 0,
                'asn_draft'     => 0,
                'asn_belum'     => 0,
                'asn_persen'    => 0.0,
            ];
        }

        foreach ($eligibleAsnById as $asnU) {
            $uName = $asnU['resolved_unit'];
            if (!isset($rekapUnitMap[$uName])) {
                $rekapUnitMap[$uName] = [
                    'nama_unit'     => $uName,
                    'asn_wajib'     => 0,
                    'asn_submitted' => 0,
                    'asn_draft'     => 0,
                    'asn_belum'     => 0,
                    'asn_persen'    => 0.0,
                ];
            }
            $rekapUnitMap[$uName]['asn_wajib']++;
        }

        foreach ($rawRespons as $r) {
            $rid  = (int) $r['id'];
            $tipe = 'akun_ecc';
            $uid  = !empty($r['user_id']) ? (int) $r['user_id'] : 0;

            if ($uid === 0) {
                $cNip = preg_replace('/\D+/', '', (string) ($r['nomor_identitas'] ?? ''));
                if ($cNip !== '' && isset($eligibleAsnByNip[$cNip])) {
                    $uid = $eligibleAsnByNip[$cNip];
                }
            }

            $linkedUser = ($uid > 0 && isset($eligibleAsnById[$uid])) ? $eligibleAsnById[$uid] : null;
            $rUnit      = trim((string) ($r['snapshot_unit'] ?? ''));
            if ($rUnit === '' || $rUnit === '-') {
                $rUnit = $linkedUser ? $linkedUser['resolved_unit'] : 'Belum Ditentukan';
            }

            if ($unitFilter !== '' && $rUnit !== $unitFilter) {
                continue;
            }

            $statusKep = trim((string) ($r['snapshot_status_kepegawaian'] ?? ''));
            if ($statusKep === '' || strcasecmp($statusKep, 'Non-ASN') === 0) {
                $statusKep = 'ASN';
            }

            // Catat pemetaan ASN untuk perhitungan KPI keterisian
            if ($linkedUser) {
                $asnResponByUserId[$uid] = $r;
            }

            // Antrean sinkronisasi pelengkapan gelar nama & profil kosong milik ASN
            if ((int) ($r['pending_profile_sync'] ?? 0) === 1 && $linkedUser) {
                $proposedChanges = [];
                $snapNamaRow     = trim((string) ($r['snapshot_nama'] ?? ''));
                $currNamaUser    = trim((string) ($linkedUser['nama_lengkap'] ?? ''));
                if ($snapNamaRow !== '' && $snapNamaRow !== $currNamaUser) {
                    $proposedChanges[] = 'Nama: ' . $snapNamaRow;
                }
                if (SurveiPemetaanPolicy::isEmptyProfileValue($linkedUser['pangkat'] ?? '') && !empty($r['snapshot_pangkat'])) {
                    $proposedChanges[] = 'Pangkat: ' . $r['snapshot_pangkat'];
                }
                if (SurveiPemetaanPolicy::isEmptyProfileValue($linkedUser['jabatan'] ?? '') && !empty($r['snapshot_jabatan'])) {
                    $proposedChanges[] = 'Jabatan: ' . $r['snapshot_jabatan'];
                }
                if (SurveiPemetaanPolicy::isEmptyProfileValue($linkedUser['unit'] ?? '') && !empty($r['snapshot_unit'])) {
                    $proposedChanges[] = 'Unit: ' . $r['snapshot_unit'];
                }
                if (!empty($proposedChanges)) {
                    $pendingSyncList[] = [
                        'respon_id' => $rid,
                        'user_id'   => $uid,
                        'nama'      => $linkedUser['nama_lengkap'] ?: ($r['snapshot_nama'] ?? '-'),
                        'nip'       => $linkedUser['nip'] ?: $r['nomor_identitas'],
                        'changes'   => $proposedChanges,
                    ];
                }
            }

            // Filter kelompok ASN (Semua ASN / PNS / PPPK) untuk tampilan tabel & grafik analitik
            if (!$this->matchesKelompokFilter($statusKep, $kelompokFilter)) {
                continue;
            }

            $enriched = [
                'id'                   => $rid,
                'user_id'              => $uid ?: null,
                'tipe_responden'       => $tipe,
                'nomor_identitas'      => $r['nomor_identitas'],
                'nama'                 => $r['snapshot_nama'] ?: ($linkedUser['nama_lengkap'] ?? 'Belum Mengisi Nama'),
                'status_kepegawaian'   => $statusKep,
                'pangkat'              => $r['snapshot_pangkat'] ?: ($linkedUser['pangkat'] ?? '-'),
                'jabatan'              => $r['snapshot_jabatan'] ?: ($linkedUser['jabatan'] ?? '-'),
                'unit'                 => $rUnit,
                'status'               => $r['status'],
                'last_step'            => (int) ($r['last_step'] ?? 1),
                'total_answered'       => (int) ($r['total_answered'] ?? 0),
                'total_applicable'     => (int) ($r['total_applicable'] ?? 83),
                'progress_percent'     => (float) ($r['progress_percent'] ?? 0),
                'pending_profile_sync' => (int) ($r['pending_profile_sync'] ?? 0),
            ];

            $filteredResponRows[] = $enriched;
            $allValidResponIds[]  = $rid;
            if ($r['status'] === 'submitted') {
                $submittedResponIds[] = $rid;
            }
        }

        // 4. Hitung Keterisian Populasi ASN & Daftar ASN Belum Selesai
        $asnWajibTotal     = count($eligibleAsnById);
        $asnSubmittedCount = 0;
        $asnDraftCount     = 0;
        $daftarAsnBelum    = [];

        foreach ($eligibleAsnById as $uid => $asnU) {
            $uName = $asnU['resolved_unit'];
            $resp  = $asnResponByUserId[$uid] ?? null;

            if ($resp && ($resp['status'] ?? '') === 'submitted') {
                $asnSubmittedCount++;
                $rekapUnitMap[$uName]['asn_submitted']++;
            } elseif ($resp && ($resp['status'] ?? '') === 'draft') {
                $asnDraftCount++;
                $rekapUnitMap[$uName]['asn_draft']++;
                $daftarAsnBelum[] = [
                    'respon_id'        => (int) $resp['id'],
                    'user_id'          => $uid,
                    'nama'             => $asnU['nama_lengkap'],
                    'nip'              => $asnU['nip'],
                    'jabatan'          => $asnU['jabatan'] ?: '-',
                    'unit'             => $uName,
                    'state'            => 'draft',
                    'last_step'        => (int) ($resp['last_step'] ?? 1),
                    'progress_percent' => (float) ($resp['progress_percent'] ?? 0),
                ];
            } else {
                $rekapUnitMap[$uName]['asn_belum']++;
                $daftarAsnBelum[] = [
                    'respon_id'        => null,
                    'user_id'          => $uid,
                    'nama'             => $asnU['nama_lengkap'],
                    'nip'              => $asnU['nip'],
                    'jabatan'          => $asnU['jabatan'] ?: '-',
                    'unit'             => $uName,
                    'state'            => 'belum_mulai',
                    'last_step'        => 0,
                    'progress_percent' => 0.0,
                ];
            }
        }

        $asnBelumTotal = max(0, $asnWajibTotal - $asnSubmittedCount - $asnDraftCount);
        $asnPersen     = $asnWajibTotal > 0 ? round(($asnSubmittedCount / $asnWajibTotal) * 100, 1) : 0.0;

        foreach ($rekapUnitMap as &$ruItem) {
            $ruItem['asn_belum']  = max(0, $ruItem['asn_wajib'] - $ruItem['asn_submitted'] - $ruItem['asn_draft']);
            $ruItem['asn_persen'] = $ruItem['asn_wajib'] > 0
                ? round(($ruItem['asn_submitted'] / $ruItem['asn_wajib']) * 100, 1)
                : 0.0;
        }
        unset($ruItem);

        // 5. Ambil Jawaban secara Batch untuk Analitik Peta Talenta, Chart.js & Masukan SDM
        $targetFetchIds = $includeFullAnswerMap ? $allValidResponIds : $submittedResponIds;
        $answersByRespon = [];

        if (!empty($targetFetchIds)) {
            $jawabanModel = new SurveiJawabanModel();
            $rawAnswers   = $jawabanModel->whereIn('respon_id', $targetFetchIds)
                                         ->orderBy('nomor_pertanyaan', 'ASC')
                                         ->findAll();

            foreach ($rawAnswers as $ra) {
                $rid = (int) $ra['respon_id'];
                $qNo = (int) $ra['nomor_pertanyaan'];
                $arr = [];
                if (!empty($ra['jawaban_pilihan'])) {
                    $dec = json_decode((string) $ra['jawaban_pilihan'], true);
                    if (is_array($dec)) {
                        $arr = array_values($dec);
                    }
                }
                $ra['pilihan_array']         = $arr;
                $answersByRespon[$rid][$qNo] = $ra;
            }
        }

        // 6. Agregasi Analitik (Hanya dari Responden ASN yang Telah Submit di Filter Aktif)
        $questionsMeta          = SurveiPemetaanConfig::getQuestions();
        $talentPool             = [];
        $daftarKeahlianTambahan = [];
        $daftarKontributorBlu   = [];
        $bluStats               = [
            'tier1_count'         => 0,
            'tier2_count'         => 0,
            'tier3_count'         => 0,
            'has_experience_cnt'  => 0,
            'willing_partner_cnt' => 0,
            'has_network_cnt'     => 0,
        ];
        $q21Stats               = [
            'total_has_skill'      => 0,
            'beberapa_skill_count' => 0,
            'satu_skill_count'     => 0,
            'tidak_ada_count'      => 0,
            'has_product_count'    => 0,
            'has_portfolio_count'  => 0,
        ];
        $lintasUnitReady      = 0;
        $lintasJamCukupCnt    = 0;
        $overtimeWillingCnt   = 0;
        $kerjaLebihReadyCnt   = 0;
        $bluInterested        = 0;
        $bluTargetWillingCnt  = 0;
        $bluReadyCommittedCnt = 0;
        $extraCapacityCnt     = 0;
        $q20Freq              = [];
        $q28Freq              = [];
        $q29Freq              = [];
        $q31Freq              = [];
        $q32Freq              = [];
        $q34Freq              = [];
        $q35Freq              = [];
        $q37Freq              = [];
        $pelatihanPegawaiMap  = [];
        $kompetensiPegawaiMap = [];
        $bluPegawaiMap        = [];
        $q56Freq              = [];
        $q57Freq              = [];
        $q61Freq              = [];
        $q68Freq              = [
            'Sangat ringan' => 0,
            'Ringan'        => 0,
            'Sedang'        => 0,
            'Berat'         => 0,
            'Sangat berat'  => 0,
        ];
        $q69Freq              = [
            'Selalu dapat diselesaikan'                                          => 0,
            'Sebagian besar dapat diselesaikan'                                  => 0,
            'Kadang-kadang mengalami keterlambatan'                              => 0,
            'Sering mengalami keterlambatan'                                     => 0,
            'Tidak dapat diselesaikan karena keterbatasan waktu atau sumber daya' => 0,
        ];
        $q70Freq              = [
            'Ya, kapasitas masih cukup besar'            => 0,
            'Ya, tetapi terbatas'                        => 0,
            'Hanya untuk pekerjaan tertentu'             => 0,
            'Saat ini tidak memiliki kapasitas tambahan' => 0,
            'Belum dapat menentukan'                     => 0,
        ];
        $q72Freq              = [];
        $q74Freq              = [];
        $q75Freq              = [];

        $likertSums   = [];
        $likertCounts = [];
        foreach (array_merge([40], range(43, 55), [73, 76, 77, 82]) as $sNo) {
            $likertSums[$sNo]   = 0.0;
            $likertCounts[$sNo] = 0;
        }

        $aspirasi = [
            26 => [],
            42 => [],
            58 => [],
            65 => [],
            83 => [],
        ];

        $willingLintasValues = [
            'Sangat bersedia',
            'Bersedia',
            'Bersedia dengan mempertimbangkan jenis pekerjaan dan beban kerja',
        ];
        $adequateWeeklyJamValues = [
            '2–4 jam per minggu',
            '5–8 jam per minggu',
            'Lebih dari 8 jam per minggu',
        ];
        $willingOvertimeValues = [
            'Sangat bersedia, sesuai ketentuan',
            'Bersedia dengan pemberitahuan sebelumnya',
            'Bersedia dengan mempertimbangkan jenis dan durasi pekerjaan',
        ];
        $interestedBluValues = [
            'Sangat tertarik',
            'Tertarik',
            'Tertarik apabila sesuai kompetensi',
        ];
        $willingTargetBluValues = [
            'Sangat bersedia',
            'Bersedia',
            'Bersedia apabila diberikan pelatihan dan pendampingan',
            'Bersedia apabila tidak mengganggu pekerjaan utama',
        ];
        $hasCapacityValues = [
            'Ya, kapasitas masih cukup besar',
            'Ya, tetapi terbatas',
            'Hanya untuk pekerjaan tertentu',
        ];

        foreach ($filteredResponRows as $resp) {
            if ($resp['status'] !== 'submitted') {
                continue;
            }
            $rid = (int) $resp['id'];
            $ans = $answersByRespon[$rid] ?? [];

            $q20List = $ans[20]['pilihan_array'] ?? [];
            $q21Text = trim((string) ($ans[21]['jawaban_teks'] ?? '-'));
            $q22Text = trim((string) ($ans[22]['jawaban_teks'] ?? ''));
            $q23Skor = isset($ans[23]['jawaban_angka']) ? (int) round((float) $ans[23]['jawaban_angka']) : 0;
            $q24Text = trim((string) ($ans[24]['jawaban_teks'] ?? '-'));
            $q25Text = trim((string) ($ans[25]['jawaban_teks'] ?? '-'));
            $q26Text = trim((string) ($ans[26]['jawaban_teks'] ?? ''));
            $q27Text = trim((string) ($ans[27]['jawaban_teks'] ?? '-'));
            $q28List = $ans[28]['pilihan_array'] ?? [];
            $q29Text = trim((string) ($ans[29]['jawaban_teks'] ?? '-'));
            $q30Text = trim((string) ($ans[30]['jawaban_teks'] ?? ''));
            $q31List = $ans[31]['pilihan_array'] ?? [];
            $q32Text = trim((string) ($ans[32]['jawaban_teks'] ?? '-'));
            $q33Text = trim((string) ($ans[33]['jawaban_teks'] ?? '-'));
            $q34List = $ans[34]['pilihan_array'] ?? [];
            $q35Text = trim((string) ($ans[35]['jawaban_teks'] ?? '-'));
            $q37List = $ans[37]['pilihan_array'] ?? [];
            $q56List = $ans[56]['pilihan_array'] ?? [];
            $q57List = $ans[57]['pilihan_array'] ?? [];
            $q60Text = trim((string) ($ans[60]['jawaban_teks'] ?? '-'));
            $q61List = $ans[61]['pilihan_array'] ?? [];
            $q62Text = trim((string) ($ans[62]['jawaban_teks'] ?? '-'));
            $q63Text = trim((string) ($ans[63]['jawaban_teks'] ?? '-'));
            $q64Text = trim((string) ($ans[64]['jawaban_teks'] ?? '-'));
            $q65Text = trim((string) ($ans[65]['jawaban_teks'] ?? ''));
            $q66Text = trim((string) ($ans[66]['jawaban_teks'] ?? '-'));
            $q67Text = trim((string) ($ans[67]['jawaban_teks'] ?? ''));
            $q68Text = trim((string) ($ans[68]['jawaban_teks'] ?? '-'));
            $q69Text = trim((string) ($ans[69]['jawaban_teks'] ?? '-'));
            $q70Text = trim((string) ($ans[70]['jawaban_teks'] ?? '-'));
            $q71List = $ans[71]['pilihan_array'] ?? [];
            $q72Text = trim((string) ($ans[72]['jawaban_teks'] ?? '-'));
            $q74List = $ans[74]['pilihan_array'] ?? [];
            $q75List = $ans[75]['pilihan_array'] ?? [];

            // Evaluasi Kesiapan Kunci (Pak KBU)
            $hasSkill21 = in_array($q21Text, [
                'Ya, memiliki beberapa keahlian tambahan',
                'Ya, memiliki satu keahlian tambahan',
            ], true);

            if ($hasSkill21) {
                $q21Stats['total_has_skill']++;
                if ($q21Text === 'Ya, memiliki beberapa keahlian tambahan') {
                    $q21Stats['beberapa_skill_count']++;
                } else {
                    $q21Stats['satu_skill_count']++;
                }
                if ($q24Text === 'Ya') {
                    $q21Stats['has_product_count']++;
                }
                if (in_array($q25Text, [
                    'Ada portofolio hasil karya / pekerjaan',
                    'Ada sertifikat keahlian / pelatihan',
                    'Ada keduanya (portofolio dan sertifikat)',
                ], true)) {
                    $q21Stats['has_portfolio_count']++;
                }

                $scaleLabels23 = $questionsMeta[23]['scale_labels'] ?? [];
                $skorLabel23   = $scaleLabels23[$q23Skor] ?? ('Skor ' . $q23Skor);

                $daftarKeahlianTambahan[] = [
                    'respon_id'    => $rid,
                    'nama'         => $resp['nama'],
                    'nip'          => $resp['nomor_identitas'],
                    'unit'         => $resp['unit'],
                    'jabatan'      => $resp['jabatan'],
                    'q21_status'   => $q21Text,
                    'q22_keahlian' => $q22Text ?: 'Belum merinci',
                    'q23_skor'     => $q23Skor,
                    'q24_produk'   => $q24Text,
                    'q25_bukti'    => $q25Text,
                    // Alias keys untuk kompatibilitas tampilan
                    'keahlian'     => $q22Text ?: 'Belum merinci',
                    'skor'         => $q23Skor,
                    'skor_label'   => $skorLabel23,
                    'produk'       => $q24Text,
                    'portofolio'   => $q25Text,
                ];
            } elseif ($q21Text !== '-') {
                $q21Stats['tidak_ada_count']++;
            }

            $isLintasReady      = in_array($q27Text, $willingLintasValues, true);
            $hasAdequateJam     = in_array($q32Text, $adequateWeeklyJamValues, true);
            $isOvertimeWilling  = in_array($q33Text, $willingOvertimeValues, true);
            $isBluReady         = in_array($q60Text, $interestedBluValues, true);
            $isBluTargetWilling = in_array($q66Text, $willingTargetBluValues, true);
            $hasCap             = in_array($q70Text, $hasCapacityValues, true);

            $isKerjaLebihReady  = $isOvertimeWilling && $hasCap;
            $isBluCommitted     = $isBluReady && $isBluTargetWilling;

            if ($isLintasReady) {
                $lintasUnitReady++;
                if ($hasAdequateJam) {
                    $lintasJamCukupCnt++;
                }
            }
            if ($isOvertimeWilling) {
                $overtimeWillingCnt++;
            }
            if ($hasCap) {
                $extraCapacityCnt++;
            }
            if ($isKerjaLebihReady) {
                $kerjaLebihReadyCnt++;
            }
            if ($isBluReady) {
                $bluInterested++;
            }
            if ($isBluTargetWilling) {
                $bluTargetWillingCnt++;
            }
            if ($isBluCommitted) {
                $bluReadyCommittedCnt++;
            }

            // Kalkulasi Skor Kesiapan Kontributor BLU (0 - 100)
            $skorBlu = 0;

            // 1. Minat BLU (No. 60) - Maks 15
            if ($q60Text === 'Sangat tertarik') {
                $skorBlu += 15;
            } elseif ($q60Text === 'Tertarik') {
                $skorBlu += 12;
            } elseif ($q60Text === 'Tertarik apabila sesuai kompetensi') {
                $skorBlu += 8;
            }

            // 2. Kesiapan Target Kinerja BLU (No. 66) - Maks 15
            if ($q66Text === 'Sangat bersedia') {
                $skorBlu += 15;
            } elseif ($q66Text === 'Bersedia') {
                $skorBlu += 12;
            } elseif ($q66Text === 'Bersedia apabila diberikan pelatihan dan pendampingan') {
                $skorBlu += 8;
            } elseif ($q66Text === 'Bersedia apabila tidak mengganggu pekerjaan utama') {
                $skorBlu += 6;
            }

            // 3. Pengalaman Bisnis/Komersial (No. 62) - Maks 10
            $hasBizExp = in_array($q62Text, [
                'Memiliki pengalaman profesional',
                'Pernah terlibat dalam kegiatan tersebut',
                'Memiliki pengalaman pribadi atau usaha sampingan',
            ], true);
            if ($q62Text === 'Memiliki pengalaman profesional') {
                $skorBlu += 10;
            } elseif ($q62Text === 'Pernah terlibat dalam kegiatan tersebut') {
                $skorBlu += 8;
            } elseif ($q62Text === 'Memiliki pengalaman pribadi atau usaha sampingan') {
                $skorBlu += 6;
            }
            if ($hasBizExp) {
                $bluStats['has_experience_cnt']++;
            }

            // 4. Kesediaan Mencari Mitra (No. 63) - Maks 10
            $willingPartner = in_array($q63Text, [
                'Sangat bersedia',
                'Bersedia',
                'Bersedia apabila mendapatkan pembekalan',
            ], true);
            if ($q63Text === 'Sangat bersedia') {
                $skorBlu += 10;
            } elseif ($q63Text === 'Bersedia') {
                $skorBlu += 8;
            } elseif ($q63Text === 'Bersedia apabila mendapatkan pembekalan') {
                $skorBlu += 6;
            }
            if ($willingPartner) {
                $bluStats['willing_partner_cnt']++;
            }

            // 5. Jejaring Eksternal (No. 64) - Maks 10
            $hasNetwork = in_array($q64Text, [
                'Ya, memiliki jaringan yang relevan',
                'Ada, tetapi belum pernah dimanfaatkan',
            ], true);
            if ($q64Text === 'Ya, memiliki jaringan yang relevan') {
                $skorBlu += 10;
            } elseif ($q64Text === 'Ada, tetapi belum pernah dimanfaatkan') {
                $skorBlu += 5;
            }
            if ($hasNetwork) {
                $bluStats['has_network_cnt']++;
            }

            // 6. Kapasitas Tambahan (No. 70) - Maks 10
            if ($q70Text === 'Ya, kapasitas masih cukup besar') {
                $skorBlu += 10;
            } elseif ($q70Text === 'Ya, tetapi terbatas') {
                $skorBlu += 7;
            } elseif ($q70Text === 'Hanya untuk pekerjaan tertentu') {
                $skorBlu += 5;
            }

            // 7. Kesediaan Kerja Lebih / Lembur (No. 33) - Maks 10
            if ($q33Text === 'Sangat bersedia, sesuai ketentuan') {
                $skorBlu += 10;
            } elseif ($q33Text === 'Bersedia dengan pemberitahuan sebelumnya') {
                $skorBlu += 8;
            } elseif ($q33Text === 'Bersedia dengan mempertimbangkan jenis dan durasi pekerjaan') {
                $skorBlu += 6;
            }

            // 8. Keahlian Tambahan (No. 21 - 25) - Maks 15
            if ($hasSkill21) {
                $skorBlu += 5;
                if ($q23Skor >= 4) {
                    $skorBlu += 5;
                } elseif ($q23Skor === 3) {
                    $skorBlu += 3;
                }
                if ($q24Text === 'Ya' || in_array($q25Text, ['Ada portofolio hasil karya / pekerjaan', 'Ada sertifikat keahlian / pelatihan', 'Ada keduanya (portofolio dan sertifikat)'], true)) {
                    $skorBlu += 5;
                }
            }

            // 9. Memilih Bidang Layanan BLU (No. 61) - Maks 5
            if (!empty($q61List)) {
                $skorBlu += 5;
            }

            $skorBlu = min(100, $skorBlu);

            // Penentuan Kategori Prioritas Kontributor BLU
            if ($skorBlu >= 65 || ($isBluReady && $isBluTargetWilling && ($hasCap || $hasSkill21))) {
                $tierBlu        = 1;
                $tierLabel      = 'Prioritas I (Tim Inti)';
                $tierBadgeClass = 'bg-success-subtle text-success';
                $bluStats['tier1_count']++;
            } elseif ($skorBlu >= 40 || $isBluReady || $isBluTargetWilling) {
                $tierBlu        = 2;
                $tierLabel      = 'Prioritas II (Potensial)';
                $tierBadgeClass = 'bg-primary-subtle text-primary';
                $bluStats['tier2_count']++;
            } else {
                $tierBlu        = 3;
                $tierLabel      = 'Prioritas III (Pendukung)';
                $tierBadgeClass = 'bg-warning-subtle text-warning-emphasis';
                $bluStats['tier3_count']++;
            }

            $daftarKontributorBlu[] = [
                'respon_id'             => $rid,
                'nama'                  => $resp['nama'],
                'nip'                   => $resp['nomor_identitas'],
                'unit'                  => $resp['unit'],
                'jabatan'               => $resp['jabatan'],
                'skor_blu'              => $skorBlu,
                'tier_blu'              => $tierBlu,
                'tier_label'            => $tierLabel,
                'tier_badge_class'      => $tierBadgeClass,
                'q60_minat'             => $q60Text,
                'q61_list'              => $q61List,
                'q62_bisnis'            => $q62Text,
                'q63_mitra'             => $q63Text,
                'q64_jejaring'          => $q64Text,
                'q65_ide'               => $q65Text,
                'q66_target'            => $q66Text,
                'q67_kontribusi'        => $q67Text,
                'has_skill_21'          => $hasSkill21,
                'q22_keahlian'          => $q22Text,
                'q23_skor'              => $q23Skor,
                'q70_kapasitas'         => $q70Text,
                'q33_lembur'            => $q33Text,
                'is_blu_committed'      => $isBluCommitted,
            ];

            $this->accumulateListFreq($q20Freq, $q20List);
            $this->accumulateListFreq($q28Freq, $q28List);
            $this->accumulateListFreq($q31Freq, $q31List);
            $this->accumulateListFreq($q34Freq, $q34List);
            $this->accumulateListFreq($q37Freq, $q37List);
            $this->accumulateListFreq($q56Freq, $q56List);
            $this->accumulateListFreq($q57Freq, $q57List);
            $this->accumulateListFreq($q61Freq, $q61List);
            $this->accumulateListFreq($q74Freq, $q74List);
            $this->accumulateListFreq($q75Freq, $q75List);

            $pegBrief = [
                'nama'    => $resp['nama'],
                'nip'     => $resp['nomor_identitas'],
                'unit'    => $resp['unit'],
                'jabatan' => $resp['jabatan'],
            ];
            foreach ($q20List as $kItem) {
                if ($kItem !== '') {
                    $kompetensiPegawaiMap[$kItem][] = $pegBrief;
                }
            }
            foreach ($q61List as $bItem) {
                if ($bItem !== '') {
                    $bluPegawaiMap[$bItem][] = $pegBrief;
                }
            }
            foreach ($q37List as $pItem) {
                if ($pItem !== '') {
                    $pelatihanPegawaiMap[$pItem][] = $pegBrief;
                }
            }

            if ($q29Text !== '' && $q29Text !== '-') {
                $q29Freq[$q29Text] = ($q29Freq[$q29Text] ?? 0) + 1;
            }
            if ($q32Text !== '' && $q32Text !== '-') {
                $q32Freq[$q32Text] = ($q32Freq[$q32Text] ?? 0) + 1;
            }
            if ($q35Text !== '' && $q35Text !== '-') {
                $q35Freq[$q35Text] = ($q35Freq[$q35Text] ?? 0) + 1;
            }
            if (isset($q68Freq[$q68Text])) {
                $q68Freq[$q68Text]++;
            }
            if (isset($q69Freq[$q69Text])) {
                $q69Freq[$q69Text]++;
            }
            if (isset($q70Freq[$q70Text])) {
                $q70Freq[$q70Text]++;
            }
            if ($q72Text !== '' && $q72Text !== '-') {
                $q72Freq[$q72Text] = ($q72Freq[$q72Text] ?? 0) + 1;
            }

            foreach ($likertSums as $sNo => $_) {
                if (isset($ans[$sNo]['jawaban_angka']) && $ans[$sNo]['jawaban_angka'] !== null && $ans[$sNo]['jawaban_angka'] !== '') {
                    $likertSums[$sNo] += (float) $ans[$sNo]['jawaban_angka'];
                    $likertCounts[$sNo]++;
                }
            }

            foreach ([26, 42, 58, 65, 83] as $essayNo) {
                $eText = trim((string) ($ans[$essayNo]['jawaban_teks'] ?? ''));
                if ($eText !== '' && $eText !== '-' && mb_strlen($eText) >= 3) {
                    $aspirasi[$essayNo][] = [
                        'respon_id' => $rid,
                        'nama'      => $resp['nama'],
                        'kelompok'  => $resp['status_kepegawaian'],
                        'unit'      => $resp['unit'],
                        'teks'      => $eText,
                    ];
                }
            }

            $talentPool[] = [
                'respon_id'             => $rid,
                'nama'                  => $resp['nama'],
                'nip'                   => $resp['nomor_identitas'],
                'tipe_responden'        => $resp['tipe_responden'],
                'kelompok'              => $resp['status_kepegawaian'],
                'unit'                  => $resp['unit'],
                'jabatan'               => $resp['jabatan'],
                'q20_list'              => $q20List,
                'q21_status'            => $q21Text,
                'has_skill_21'          => $hasSkill21,
                'q22_keahlian'          => $q22Text,
                'q23_skor'              => $q23Skor,
                'q24_produk'            => $q24Text,
                'q25_bukti'             => $q25Text,
                'q26_keahlian_tambah'   => $q26Text,
                'q27_lintas_unit'       => $q27Text,
                'is_lintas_ready'       => $isLintasReady,
                'q28_list'              => $q28List,
                'q29_pengalaman'        => $q29Text,
                'q30_detail'            => $q30Text,
                'q31_pertimbangan'      => $q31List,
                'q32_jam'               => $q32Text,
                'has_adequate_jam'      => $hasAdequateJam,
                'q33_jam_lebih'         => $q33Text,
                'q34_faktor'            => $q34List,
                'q35_rotasi'            => $q35Text,
                'is_overtime_willing'   => $isOvertimeWilling,
                'is_kerja_lebih_ready'  => $isKerjaLebihReady,
                'q37_list'              => $q37List,
                'q60_minat_blu'         => $q60Text,
                'is_blu_ready'          => $isBluReady,
                'q61_list'              => $q61List,
                'q62_bisnis'            => $q62Text,
                'q63_mitra'             => $q63Text,
                'q64_jejaring'          => $q64Text,
                'q66_target_blu'        => $q66Text,
                'is_blu_target_willing' => $isBluTargetWilling,
                'is_blu_committed'      => $isBluCommitted,
                'skor_blu'              => $skorBlu,
                'tier_blu'              => $tierBlu,
                'tier_label'            => $tierLabel,
                'tier_badge_class'      => $tierBadgeClass,
                'q65_ide'               => $q65Text,
                'q67_kontribusi'        => $q67Text,
                'q68_beban'             => $q68Text,
                'q69_selesai'           => $q69Text,
                'q70_kapasitas'         => $q70Text,
                'q71_dukungan'          => $q71List,
                'q72_evaluasi'          => $q72Text,
                'has_capacity'          => $hasCap,
            ];
        }

        // Urutkan daftar kontributor BLU berdasarkan skor kesiapan tertinggi
        usort($daftarKontributorBlu, function ($a, $b) {
            if ($a['skor_blu'] === $b['skor_blu']) {
                return strcmp($a['nama'], $b['nama']);
            }
            return ($a['skor_blu'] > $b['skor_blu']) ? -1 : 1;
        });

        $totalSubmittedFiltered = count($submittedResponIds);

        arsort($q20Freq);
        arsort($q28Freq);
        arsort($q31Freq);
        arsort($q34Freq);
        arsort($q37Freq);
        arsort($q56Freq);
        arsort($q57Freq);
        arsort($q61Freq);
        arsort($q72Freq);
        arsort($q74Freq);
        arsort($q75Freq);

        $q21Stats['persen_memiliki_keahlian'] = $totalSubmittedFiltered > 0
            ? round(($q21Stats['total_has_skill'] / $totalSubmittedFiltered) * 100, 1)
            : 0.0;

        $hambatanRanked = [];
        foreach ($q57Freq as $hLbl => $hCnt) {
            $hambatanRanked[] = [
                'label'   => $hLbl,
                'count'   => (int) $hCnt,
                'percent' => $totalSubmittedFiltered > 0 ? round(($hCnt / $totalSubmittedFiltered) * 100, 1) : 0.0,
            ];
        }

        $likertPerilaku = [];
        $sumAllBehavior = 0.0;
        $cntAllBehavior = 0;
        for ($sNo = 43; $sNo <= 55; $sNo++) {
            $avg = $likertCounts[$sNo] > 0 ? round($likertSums[$sNo] / $likertCounts[$sNo], 2) : 0.0;
            if ($likertCounts[$sNo] > 0) {
                $sumAllBehavior += $avg;
                $cntAllBehavior++;
            }
            $likertPerilaku[] = [
                'no'    => $sNo,
                'label' => $questionsMeta[$sNo]['label'] ?? '',
                'avg'   => $avg,
                'count' => $likertCounts[$sNo],
            ];
        }
        $overallBehaviorAvg = $cntAllBehavior > 0 ? round($sumAllBehavior / $cntAllBehavior, 2) : 0.0;

        $skorPenghargaan = [];
        foreach ([73, 76, 77, 82] as $pNo) {
            $skorPenghargaan[$pNo] = [
                'no'    => $pNo,
                'label' => $questionsMeta[$pNo]['label'] ?? '',
                'avg'   => $likertCounts[$pNo] > 0 ? round($likertSums[$pNo] / $likertCounts[$pNo], 2) : 0.0,
                'count' => $likertCounts[$pNo],
            ];
        }

        return [
            'unitOptions' => $unitList,
            'kpi'         => [
                'asn_wajib'            => $asnWajibTotal,
                'asn_submitted'        => $asnSubmittedCount,
                'asn_draft'            => $asnDraftCount,
                'asn_belum'            => $asnBelumTotal,
                'asn_persen'           => $asnPersen,
                'submitted_filtered'   => $totalSubmittedFiltered,
                'total_submitted'      => $totalSubmittedFiltered,
                'overtime_willing'     => $overtimeWillingCnt,
                'extra_capacity_count' => $extraCapacityCnt,
                'kerja_lebih_ready'    => $kerjaLebihReadyCnt,
                'kerja_lebih_persen'   => $totalSubmittedFiltered > 0 ? round(($kerjaLebihReadyCnt / $totalSubmittedFiltered) * 100, 1) : 0.0,
                'lintas_unit_ready'    => $lintasUnitReady,
                'lintas_unit_persen'   => $totalSubmittedFiltered > 0 ? round(($lintasUnitReady / $totalSubmittedFiltered) * 100, 1) : 0.0,
                'lintas_jam_cukup'     => $lintasJamCukupCnt,
                'blu_interested'       => $bluInterested,
                'blu_persen'           => $totalSubmittedFiltered > 0 ? round(($bluInterested / $totalSubmittedFiltered) * 100, 1) : 0.0,
                'blu_target_willing'   => $bluTargetWillingCnt,
                'blu_ready_count'      => $bluReadyCommittedCnt,
                'blu_ready_persen'     => $totalSubmittedFiltered > 0 ? round(($bluReadyCommittedCnt / $totalSubmittedFiltered) * 100, 1) : 0.0,
                'overall_behavior_avg' => $overallBehaviorAvg,
            ],
            'rekapUnit'              => array_values($rekapUnitMap),
            'pendingSyncList'        => $pendingSyncList,
            'daftarResponden'        => $filteredResponRows,
            'daftarAsnBelum'         => $daftarAsnBelum,
            'talentPool'             => $talentPool,
            'q21Stats'               => $q21Stats,
            'daftarKeahlianTambahan' => $daftarKeahlianTambahan,
            'daftarKontributorBlu'   => $daftarKontributorBlu,
            'bluStats'               => $bluStats,
            'filterOptions'          => [
                'q20_options' => $questionsMeta[20]['options'] ?? [],
                'q28_options' => $questionsMeta[28]['options'] ?? [],
                'q61_options' => $questionsMeta[61]['options'] ?? [],
            ],
            'charts' => [
                'top_kompetensi'     => array_slice($q20Freq, 0, 10, true),
                'top_blu'            => array_slice($q61Freq, 0, 10, true),
                'top_pelatihan'      => array_slice($q37Freq, 0, 10, true),
                'beban_kerja'        => $q68Freq,
                'kapasitas'          => $q70Freq,
                'alokasi_jam'        => $q32Freq,
                'kompetensi_pegawai' => $kompetensiPegawaiMap,
                'blu_pegawai'        => $bluPegawaiMap,
                'pelatihan_pegawai'  => $pelatihanPegawaiMap,
            ],
            'insights' => [
                'likert_perilaku'    => $likertPerilaku,
                'top_motivasi'       => array_slice($q56Freq, 0, 8, true),
                'top_hambatan'       => array_slice($q57Freq, 0, 8, true),
                'hambatan_ranked'    => $hambatanRanked,
                'skor_penghargaan'   => $skorPenghargaan,
                'top_dasar_reward'   => array_slice($q74Freq, 0, 8, true),
                'top_bentuk_reward'  => array_slice($q75Freq, 0, 8, true),
                'q34_faktor_lembur'  => array_slice($q34Freq, 0, 6, true),
                'q69_kelancaran'     => $q69Freq,
                'q29_pengalaman'     => $q29Freq,
                'q31_pertimbangan'   => array_slice($q31Freq, 0, 6, true),
                'q35_rotasi'         => $q35Freq,
                'q72_evaluasi'       => $q72Freq,
                'aspirasi'           => $aspirasi,
            ],
            'allAnswersByRespon'     => $includeFullAnswerMap ? $answersByRespon : [],
        ];
    }

    private function normalizeKelompokFilter(string $rawFilter): string
    {
        $val = trim($rawFilter);
        if (strcasecmp($val, 'PNS') === 0) {
            return 'PNS';
        }
        if (strcasecmp($val, 'PPPK') === 0) {
            return 'PPPK';
        }
        return 'all';
    }

    private function matchesKelompokFilter(string $statusKepegawaian, string $filter): bool
    {
        if ($filter === '' || $filter === 'all') {
            return true;
        }
        return strcasecmp($statusKepegawaian, $filter) === 0;
    }

    private function accumulateListFreq(array &$freqMap, array $items): void
    {
        foreach ($items as $it) {
            $lbl = trim((string) $it);
            if ($lbl !== '') {
                $freqMap[$lbl] = ($freqMap[$lbl] ?? 0) + 1;
            }
        }
    }
}

