<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Libraries\SurveiPemetaanConfig;
use App\Libraries\SurveiPemetaanPolicy;
use App\Models\SurveiJawabanModel;
use App\Models\SurveiResponModel;
use App\Models\UnitKerja;
use App\Models\User;

class SurveiPemetaanController extends BaseController
{
    private const COOKIE_NAME   = 'ecc_survei_resume';
    private const COOKIE_EXPIRE = 2592000; // 30 Hari

    /**
     * Halaman Utama Formulir Survei Pemetaan SDM (Eksklusif via Share Link, Tanpa Login,
     * serta otomatis mengenali sesi cookie aktif maupun sesi login ECC tanpa meminta NIP/Kata Sandi lagi).
     */
    public function index()
    {
        $this->applyNoIndexHeaders();

        $tahun             = SurveiPemetaanPolicy::getActiveSurveyYear();
        $isOpen            = SurveiPemetaanPolicy::isSurveyOpen();
        $forceManualSwitch = ((string) $this->request->getGet('ganti') === '1');
        $cookieRespon      = $this->resolveResponFromCookie($tahun);
        $loggedAsnUser     = !$forceManualSwitch ? $this->findEligibleLoggedInAsnUser() : null;
        $activeRespon      = null;
        $cookieJustIssued  = false;

        if ($loggedAsnUser !== null) {
            $loggedUid = (int) $loggedAsnUser['id'];
            $cleanNip  = preg_replace('/\D+/', '', (string) ($loggedAsnUser['nip'] ?? ''));

            if ($cookieRespon && (int) ($cookieRespon['user_id'] ?? 0) === $loggedUid) {
                $activeRespon = $cookieRespon;
                if ((int) ($activeRespon['filled_via_auth'] ?? 0) === 0) {
                    $responModel = new SurveiResponModel();
                    $responModel->update((int) $activeRespon['id'], ['filled_via_auth' => 1]);
                    $activeRespon['filled_via_auth'] = 1;
                }
            } else {
                $responModel = new SurveiResponModel();
                $ownRespon   = $responModel->findByUserTahun($loggedUid, $tahun)
                            ?: ($cleanNip !== '' ? $responModel->findByIdentitasTahun($cleanNip, $tahun) : null);

                if ($ownRespon && (int) ($ownRespon['is_valid'] ?? 1) === 1) {
                    if ((int) ($ownRespon['filled_via_auth'] ?? 0) === 0) {
                        $responModel->update((int) $ownRespon['id'], ['filled_via_auth' => 1]);
                        $ownRespon['filled_via_auth'] = 1;
                    }
                    if ((int) ($ownRespon['pending_profile_sync'] ?? 0) === 1) {
                        SurveiPemetaanPolicy::syncEmptyProfileFields($loggedUid, $ownRespon, true, true);
                        $ownRespon['pending_profile_sync'] = 0;
                    }
                    $this->issueResumeCookie($ownRespon);
                    $cookieJustIssued = true;
                    $activeRespon     = $ownRespon;
                } elseif ($isOpen && $cleanNip !== '') {
                    try {
                        $newRespon = $this->createOrResetAsnDraft($loggedAsnUser, $cleanNip, $tahun, true, $ownRespon);
                        $this->issueResumeCookie($newRespon);
                        $cookieJustIssued = true;
                        $activeRespon     = $newRespon;
                    } catch (\Throwable $e) {
                        log_message('error', 'SurveiPemetaanController::index auto-init ASN login error: ' . $e->getMessage());
                    }
                }
            }
        } else {
            $activeRespon = $cookieRespon;
        }

        if ($activeRespon && ($activeRespon['status'] ?? '') === 'submitted') {
            $redirect = redirect()->to(site_url('survei-pemetaan/ringkasan'));
            return $cookieJustIssued ? $redirect->withCookies() : $redirect;
        }

        $unitModel = new UnitKerja();
        $unitList  = $unitModel->select('id, nama_unit')
                               ->orderBy('nama_unit', 'ASC')
                               ->findAll();

        $loggedInNotice = null;
        if (session()->get('isLoggedIn')) {
            $uid = SurveiPemetaanPolicy::getCurrentUserId();
            if ($uid > 0) {
                $db   = \Config\Database::connect();
                $uRow = $db->table('users')
                           ->select('id, role, status_pegawai, tmt_selesai')
                           ->where('id', $uid)
                           ->get()
                           ->getRowArray();
                if ($uRow) {
                    $isAdmin = ($uRow['role'] === 'admin');
                    if (!$isAdmin && $db->tableExists('user_roles')) {
                        $isAdmin = $db->table('user_roles')
                                      ->where('user_id', $uid)
                                      ->where('role_name', 'admin')
                                      ->countAllResults() > 0;
                    }
                    if ($isAdmin) {
                        $loggedInNotice = [
                            'type'    => 'admin',
                            'message' => 'Akun Administrator dikecualikan dari sasaran responden survei pemetaan SDM institusi.',
                        ];
                    } elseif (!SurveiPemetaanPolicy::isUserEligibleForSurvey($uRow)) {
                        $loggedInNotice = [
                            'type'    => 'ineligible',
                            'message' => 'Status kepegawaian akun Anda tercatat telah menyelesaikan masa tugas pada periode survei ini.',
                        ];
                    }
                }
            }
        }

        $initialSession = null;
        if ($isOpen && $activeRespon && ($activeRespon['status'] ?? '') === 'draft') {
            $initialSession = $this->buildClientSessionPayload($activeRespon);
        }

        return view('user/survei_pemetaan/index', [
            'title'          => 'Survei Pemetaan Potensi, Kompetensi, dan Kapasitas Pegawai PKTJ',
            'tahunSurvei'    => $tahun,
            'isSurveyOpen'   => $isOpen,
            'sections'       => SurveiPemetaanConfig::getSections(),
            'questions'      => SurveiPemetaanConfig::getQuestions(),
            'unitList'       => $unitList,
            'initialSession' => $initialSession,
            'loggedInNotice' => $loggedInNotice,
        ]);
    }

    /**
     * Halaman Ringkasan Jawaban (Read-Only setelah survei terkirim).
     */
    public function ringkasan()
    {
        $this->applyNoIndexHeaders();

        $tahun        = SurveiPemetaanPolicy::getActiveSurveyYear();
        $activeRespon = $this->resolveActiveRespon($tahun);

        if (!$activeRespon || ($activeRespon['status'] ?? '') !== 'submitted') {
            return redirect()->to(site_url('survei-pemetaan'))->withCookies();
        }

        $jawabanModel = new SurveiJawabanModel();
        $answersMap   = $jawabanModel->getAnswersMap((int) $activeRespon['id']);

        return view('user/survei_pemetaan/ringkasan', [
            'title'       => 'Ringkasan Survei Pemetaan SDM PKTJ',
            'tahunSurvei' => $tahun,
            'respon'      => $activeRespon,
            'sections'    => SurveiPemetaanConfig::getSections(),
            'questions'   => SurveiPemetaanConfig::getQuestions(),
            'answersMap'  => $answersMap,
        ]);
    }

    /**
     * Endpoint ringan penyegar token CSRF untuk sesi pengisian panjang.
     */
    public function pingCsrf()
    {
        $this->applyNoIndexHeaders();

        return $this->response->setJSON([
            'status'     => 'success',
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash(),
        ]);
    }

    /**
     * Langkah 0: Cek Nomor Identitas (Khusus 18 Digit NIP Pegawai ASN PKTJ).
     */
    public function cekIdentitas()
    {
        $this->applyNoIndexHeaders();

        if (!SurveiPemetaanPolicy::isSurveyOpen()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Periode pengisian survei saat ini telah ditutup.',
            ], 403);
        }

        // Proteksi Honeypot anti-bot
        $honeypot = trim((string) $this->request->getPost('website_url'));
        if ($honeypot !== '') {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Permintaan tidak dapat diproses.',
            ], 400);
        }

        // Proteksi Throttling (10x per menit per IP)
        $throttler   = \Config\Services::throttler();
        $ipAddress   = (string) $this->request->getIPAddress();
        $throttleKey = md5('survei_cek_id_' . $ipAddress);
        if ($throttler->check($throttleKey, 10, MINUTE) === false) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Terlalu banyak percobaan. Silakan tunggu 1 menit lalu coba lagi.',
            ], 429);
        }

        $rawIdentitas   = trim((string) $this->request->getPost('nomor_identitas'));
        $nomorIdentitas = preg_replace('/\D+/', '', $rawIdentitas);

        if ($nomorIdentitas === '' || strlen($nomorIdentitas) !== 18) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Masukkan 18 digit NIP pegawai PKTJ.',
            ], 422);
        }

        $tahun       = SurveiPemetaanPolicy::getActiveSurveyYear();
        $responModel = new SurveiResponModel();

        // Cek apakah NIP milik akun Administrator
        if ($this->isNipBelongsToAdmin($nomorIdentitas)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Akun Administrator dikecualikan dari sasaran responden survei pemetaan.',
            ], 403);
        }

        // Cek kecocokan dengan akun pegawai aktif di tabel users (kolom selektif)
        $matchedUser = $this->findAsnUserByNip($nomorIdentitas);

        if (!$matchedUser) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Survei ini khusus bagi pegawai PKTJ. NIP 18 digit tidak ditemukan, silakan periksa kembali NIP Anda.',
            ], 404);
        }

        if (!SurveiPemetaanPolicy::isUserEligibleForSurvey($matchedUser)) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Data pegawai tersebut sudah tidak aktif pada periode survei ini.',
            ], 403);
        }

        $userId       = (int) $matchedUser['id'];
        $cleanUserNip = preg_replace('/\D+/', '', (string) $matchedUser['nip']) ?: $nomorIdentitas;
        $isSameLogin  = session()->get('isLoggedIn') && (SurveiPemetaanPolicy::getCurrentUserId() === $userId);

        $existing = $responModel->findByUserTahun($userId, $tahun)
                 ?: $responModel->findByIdentitasTahun($cleanUserNip, $tahun);

        if ($existing && (int) ($existing['is_valid'] ?? 1) === 1) {
            $existingId = (int) $existing['id'];

            if ($isSameLogin && (int) ($existing['filled_via_auth'] ?? 0) === 0) {
                $responModel->update($existingId, ['filled_via_auth' => 1]);
                $existing['filled_via_auth'] = 1;
            }
            if ($isSameLogin && (int) ($existing['pending_profile_sync'] ?? 0) === 1) {
                SurveiPemetaanPolicy::syncEmptyProfileFields($userId, $existing, true, true);
                $existing['pending_profile_sync'] = 0;
            }

            $this->issueResumeCookie($existing);

            if (($existing['status'] ?? '') === 'submitted') {
                return $this->jsonResponse([
                    'status'       => 'success',
                    'state'        => 'already_submitted_redirect',
                    'redirect_url' => site_url('survei-pemetaan/ringkasan'),
                ]);
            }

            return $this->jsonResponse([
                'status'  => 'success',
                'state'   => 'resumed',
                'message' => 'Progres survei Anda berhasil dimuat kembali.',
                'session' => $this->buildClientSessionPayload($existing),
            ]);
        }

        // Buat sesi draf baru untuk ASN (atau aktifkan ulang baris yang diarsipkan)
        try {
            $newRespon = $this->createOrResetAsnDraft($matchedUser, $cleanUserNip, $tahun, $isSameLogin, $existing);
            $this->issueResumeCookie($newRespon);

            return $this->jsonResponse([
                'status'  => 'success',
                'state'   => 'ready',
                'message' => 'Data akun pegawai ditemukan. Silakan lengkapi survei berikut.',
                'session' => $this->buildClientSessionPayload($newRespon),
            ]);
        } catch (\Throwable $e) {
            // Failsafe pemulihan jika terjadi tabrakan kueri unik ganda
            $retryExisting = $responModel->findByUserTahun($userId, $tahun)
                          ?: $responModel->findByIdentitasTahun($cleanUserNip, $tahun);
            if ($retryExisting && (int) ($retryExisting['is_valid'] ?? 1) === 1) {
                $this->issueResumeCookie($retryExisting);
                return $this->jsonResponse([
                    'status'  => 'success',
                    'state'   => 'ready',
                    'message' => 'Data akun pegawai ditemukan. Silakan lengkapi survei berikut.',
                    'session' => $this->buildClientSessionPayload($retryExisting),
                ]);
            }
            log_message('error', 'SurveiPemetaanController::cekIdentitas ASN draft error: ' . $e->getMessage());
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Gagal memulai sesi survei. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Simpan Progres Bagian (AJAX Single-Flight pada klik Lanjut maupun Simpan Draf).
     */
    public function saveStep()
    {
        $this->applyNoIndexHeaders();

        if (!SurveiPemetaanPolicy::isSurveyOpen()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Periode pengisian survei saat ini telah ditutup.',
            ], 403);
        }

        $tahun  = SurveiPemetaanPolicy::getActiveSurveyYear();
        $respon = $this->resolveActiveRespon($tahun);

        if (!$respon) {
            return $this->jsonResponse([
                'status'  => 'session_expired',
                'message' => 'Sesi pengisian perlu dipulihkan. Masukkan kembali 18 digit NIP Anda untuk melanjutkan.',
            ], 401);
        }

        $postedResponId = (int) $this->request->getPost('respon_id');
        if ($postedResponId > 0 && (int) ($respon['id'] ?? 0) !== $postedResponId) {
            return $this->jsonResponse([
                'status'  => 'session_expired',
                'message' => 'Sesi peramban Anda telah beralih ke responden lain. Masukkan kembali 18 digit NIP Anda.',
            ], 401);
        }

        if (($respon['status'] ?? '') === 'submitted') {
            return $this->jsonResponse([
                'status'       => 'already_submitted',
                'redirect_url' => site_url('survei-pemetaan/ringkasan'),
            ]);
        }

        $step       = max(1, min(10, (int) $this->request->getPost('step')));
        $targetStep = max(1, min(10, (int) ($this->request->getPost('target_step') ?: $step)));
        $mode       = (string) ($this->request->getPost('mode') ?: 'next');
        $rawJson    = (string) $this->request->getPost('answers_json');
        $submitted  = json_decode($rawJson, true);

        if (!is_array($submitted)) {
            $submitted = [];
        }

        $strictStep = ($mode === 'next');
        $saveResult = $this->processAndPersistAnswers($respon, $step, $targetStep, $submitted, $strictStep);

        if ($saveResult['status'] === 'error') {
            return $this->jsonResponse($saveResult, 422);
        }

        if ($saveResult['status'] === 'incomplete_step') {
            return $this->jsonResponse($saveResult, 200);
        }

        return $this->jsonResponse([
            'status'   => 'success',
            'message'  => $mode === 'draft' ? 'Draf berhasil disimpan.' : 'Bagian tersimpan.',
            'progress' => $saveResult['progress'],
            'answers'  => $saveResult['answers'],
            'step'     => $targetStep,
        ]);
    }

    /**
     * Kirim Final Survei Pemetaan SDM (Mengunci Jawaban & Mencatat Log Audit).
     */
    public function submit()
    {
        $this->applyNoIndexHeaders();
        helper('audit');

        if (!SurveiPemetaanPolicy::isSurveyOpen()) {
            return $this->jsonResponse([
                'status'  => 'error',
                'message' => 'Periode pengisian survei saat ini telah ditutup.',
            ], 403);
        }

        $tahun  = SurveiPemetaanPolicy::getActiveSurveyYear();
        $respon = $this->resolveActiveRespon($tahun);

        if (!$respon) {
            return $this->jsonResponse([
                'status'  => 'session_expired',
                'message' => 'Sesi pengisian perlu dipulihkan. Masukkan kembali 18 digit NIP Anda untuk melanjutkan.',
            ], 401);
        }

        $postedResponId = (int) $this->request->getPost('respon_id');
        if ($postedResponId > 0 && (int) ($respon['id'] ?? 0) !== $postedResponId) {
            return $this->jsonResponse([
                'status'  => 'session_expired',
                'message' => 'Sesi peramban Anda telah beralih ke responden lain. Masukkan kembali 18 digit NIP Anda.',
            ], 401);
        }

        if (($respon['status'] ?? '') === 'submitted') {
            return $this->jsonResponse([
                'status'       => 'already_submitted',
                'redirect_url' => site_url('survei-pemetaan/ringkasan'),
            ]);
        }

        $step      = max(1, min(10, (int) ($this->request->getPost('step') ?: 10)));
        $rawJson   = (string) $this->request->getPost('answers_json');
        $submitted = json_decode($rawJson, true);
        if (!is_array($submitted)) {
            $submitted = [];
        }

        // Simpan seluruh input terakhir terlebih dahulu
        $saveResult = $this->processAndPersistAnswers($respon, $step, 10, $submitted, false);
        if ($saveResult['status'] === 'error') {
            return $this->jsonResponse($saveResult, 422);
        }

        $progress = $saveResult['progress'];
        foreach ($progress['missing_by_step'] as $stepNo => $missingList) {
            if (!empty($missingList)) {
                $firstMissing = (int) $missingList[0];
                $missingMsg   = SurveiPemetaanConfig::describeMissingQuestion($firstMissing, $saveResult['answers'] ?? []);
                return $this->jsonResponse([
                    'status'        => 'incomplete_survey',
                    'step'          => (int) $stepNo,
                    'first_missing' => $firstMissing,
                    'missing'       => $missingList,
                    'progress'      => $progress,
                    'answers'       => $saveResult['answers'],
                    'message'       => 'Masih ada pertanyaan belum lengkap pada Bagian ' . $stepNo . ': ' . $missingMsg,
                ], 200);
            }
        }

        $pernyataanSetuju = trim((string) $this->request->getPost('pernyataan_setuju'));
        if ($pernyataanSetuju !== '1') {
            return $this->jsonResponse([
                'status'   => 'missing_declaration',
                'step'     => 10,
                'progress' => $progress,
                'answers'  => $saveResult['answers'],
                'message'  => 'Centang pernyataan tanggung jawab sebelum mengirim survei.',
            ], 422);
        }

        $responModel = new SurveiResponModel();
        $responId    = (int) $respon['id'];
        $now         = date('Y-m-d H:i:s');

        $responModel->update($responId, [
            'status'           => 'submitted',
            'last_step'        => 10,
            'progress_percent' => 100.00,
            'submitted_at'     => $now,
        ]);

        $freshRespon = $responModel->find($responId);
        $sessionKey  = 'survei_editable_profile_' . $responId;
        $editFields  = session()->get($sessionKey);
        if (!is_array($editFields)) {
            $editFields = [];
        }

        // Apabila ASN telah terverifikasi / sedang login, pastikan sinkronisasi profil tereksekusi
        if ($freshRespon && ($freshRespon['tipe_responden'] ?? '') === 'akun_ecc' && !empty($freshRespon['user_id'])) {
            $uid     = (int) $freshRespon['user_id'];
            $isAuthy = ((int) ($freshRespon['filled_via_auth'] ?? 0) === 1)
                    || (session()->get('isLoggedIn') && SurveiPemetaanPolicy::getCurrentUserId() === $uid);
            if ($isAuthy && (int) ($freshRespon['pending_profile_sync'] ?? 0) === 1) {
                SurveiPemetaanPolicy::syncEmptyProfileFields($uid, $freshRespon, true, true, $editFields);
            }
        }

        session()->remove($sessionKey);

        log_audit('SUBMIT_SURVEI_PEMETAAN', 'survei_pemetaan_respon', (string) $responId, null, [
            'nomor_identitas'           => $respon['nomor_identitas'],
            'tipe_responden'            => $respon['tipe_responden'],
            'tahun_survei'              => $tahun,
            'pernyataan_tanggung_jawab' => 1,
        ]);

        return $this->jsonResponse([
            'status'       => 'success',
            'message'      => 'Terima kasih, jawaban survei Anda telah terkirim.',
            'redirect_url' => site_url('survei-pemetaan/ringkasan'),
        ]);
    }

    /**
     * Bersihkan cookie sesi survei di peramban (untuk komputer bersama / ganti responden).
     */
    public function keluarSesi()
    {
        $this->applyNoIndexHeaders();

        $tahun        = SurveiPemetaanPolicy::getActiveSurveyYear();
        $activeRespon = $this->resolveResponFromCookie($tahun);
        if ($activeRespon && !empty($activeRespon['id'])) {
            $responModel = new SurveiResponModel();
            $responModel->update((int) $activeRespon['id'], [
                'resume_token_hash' => null,
            ]);
            session()->remove('survei_editable_profile_' . (int) $activeRespon['id']);
        }

        $this->clearResumeCookie();
        $switchUrl = site_url('survei-pemetaan?ganti=1');

        if ($this->request->isAJAX()) {
            return $this->jsonResponse([
                'status'       => 'success',
                'redirect_url' => $switchUrl,
            ]);
        }

        return redirect()->to($switchUrl)->withCookies();
    }

    // =========================================================================
    // HELPER PRIVAT: KEAMANAN COOKIE, VALIDASI, DAN PENYIMPANAN ATOMIK
    // =========================================================================

    private function applyNoIndexHeaders(): void
    {
        $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    private function jsonResponse(array $payload, int $statusCode = 200)
    {
        $payload['csrf_token'] = csrf_token();
        $payload['csrf_hash']  = csrf_hash();

        return $this->response->setStatusCode($statusCode)->setJSON($payload);
    }

    private function getCookieSigningSecret(): string
    {
        $encKey = (string) (config('Encryption')->key ?? '');
        if ($encKey !== '') {
            return hash('sha256', 'ecc_survei_cookie_v1::' . $encKey);
        }
        return hash('sha256', 'ecc_survei_cookie_v1::' . APPPATH . '::' . (string) (config('App')->baseURL ?? 'pktj'));
    }

    private function issueResumeCookie(array &$responRow): void
    {
        $responId = (int) ($responRow['id'] ?? 0);
        if ($responId <= 0) {
            return;
        }

        $rawToken  = bin2hex(random_bytes(24));
        $tokenHash = hash('sha256', $rawToken);

        $responModel = new SurveiResponModel();
        $responModel->update($responId, [
            'resume_token_hash' => $tokenHash,
        ]);
        $responRow['resume_token_hash'] = $tokenHash;

        $dataPart = $responId . '.' . $rawToken;
        $hmac     = hash_hmac('sha256', $dataPart, $this->getCookieSigningSecret());
        $encoded  = base64_encode($dataPart . '.' . $hmac);

        $this->response->setCookie([
            'name'     => self::COOKIE_NAME,
            'value'    => $encoded,
            'expire'   => self::COOKIE_EXPIRE,
            'path'     => '/',
            'secure'   => $this->request->isSecure(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function clearResumeCookie(): void
    {
        $this->response->deleteCookie(self::COOKIE_NAME, '', '/');
    }

    private function resolveResponFromCookie(int $tahun): ?array
    {
        $rawCookie = (string) ($this->request->getCookie(self::COOKIE_NAME) ?? '');
        if ($rawCookie === '') {
            return null;
        }

        $decoded = base64_decode($rawCookie, true);
        if (!is_string($decoded) || $decoded === '') {
            return null;
        }

        $parts = explode('.', $decoded);
        if (count($parts) !== 3) {
            return null;
        }

        [$idStr, $rawToken, $hmac] = $parts;
        if (!ctype_digit($idStr) || !preg_match('/^[a-f0-9]{48}$/', $rawToken)) {
            return null;
        }

        $expectedHmac = hash_hmac('sha256', $idStr . '.' . $rawToken, $this->getCookieSigningSecret());
        if (!hash_equals($expectedHmac, $hmac)) {
            return null;
        }

        $responModel = new SurveiResponModel();
        $row         = $responModel->find((int) $idStr);

        if (!$row || (int) ($row['tahun_survei'] ?? 0) !== $tahun || (int) ($row['is_valid'] ?? 1) !== 1) {
            return null;
        }

        $storedHash = (string) ($row['resume_token_hash'] ?? '');
        if ($storedHash === '' || !hash_equals($storedHash, hash('sha256', $rawToken))) {
            return null;
        }

        return $row;
    }

    /**
     * Resolusi sesi survei aktif: cek cookie peramban terlebih dahulu, lalu fallback otomatis
     * ke akun pegawai ASN yang sedang login di ECC tanpa perlu mengetik NIP atau Kata Sandi lagi.
     */
    private function resolveActiveRespon(int $tahun): ?array
    {
        $cookieRow = $this->resolveResponFromCookie($tahun);
        if ($cookieRow) {
            return $cookieRow;
        }

        $loggedAsn = $this->findEligibleLoggedInAsnUser();
        if (!$loggedAsn) {
            return null;
        }

        $uid         = (int) $loggedAsn['id'];
        $cleanNip    = preg_replace('/\D+/', '', (string) ($loggedAsn['nip'] ?? ''));
        $responModel = new SurveiResponModel();
        $ownRespon   = $responModel->findByUserTahun($uid, $tahun)
                    ?: ($cleanNip !== '' ? $responModel->findByIdentitasTahun($cleanNip, $tahun) : null);

        if (!$ownRespon || (int) ($ownRespon['is_valid'] ?? 1) !== 1) {
            return null;
        }

        if ((int) ($ownRespon['filled_via_auth'] ?? 0) === 0) {
            $responModel->update((int) $ownRespon['id'], ['filled_via_auth' => 1]);
            $ownRespon['filled_via_auth'] = 1;
        }

        $this->issueResumeCookie($ownRespon);
        return $ownRespon;
    }

    /**
     * Mengambil data pegawai ASN eligible apabila pengguna saat ini sudah login ke ECC
     * (termasuk sesi yang dipulihkan otomatis lewat cookie remember_me di BaseController).
     */
    private function findEligibleLoggedInAsnUser(): ?array
    {
        if (!session()->get('isLoggedIn')) {
            return null;
        }

        $uid = SurveiPemetaanPolicy::getCurrentUserId();
        if ($uid <= 0) {
            return null;
        }

        $db  = \Config\Database::connect();
        $row = $db->table('users')
                  ->select('id, nama_lengkap, nip, username, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai')
                  ->where('id', $uid)
                  ->where('role !=', 'admin')
                  ->where('nip IS NOT NULL', null, false)
                  ->where("TRIM(nip) != ''", null, false)
                  ->limit(1)
                  ->get()
                  ->getRowArray();

        if (!$row) {
            return null;
        }

        if ($db->tableExists('user_roles')) {
            $isAdminPivot = $db->table('user_roles')
                               ->where('user_id', $uid)
                               ->where('role_name', 'admin')
                               ->countAllResults() > 0;
            if ($isAdminPivot) {
                return null;
            }
        }

        if (!SurveiPemetaanPolicy::isUserEligibleForSurvey($row)) {
            return null;
        }

        $cleanNip = preg_replace('/\D+/', '', (string) ($row['nip'] ?? ''));
        if ($cleanNip === '') {
            return null;
        }

        return $row;
    }

    /**
     * Cari akun pegawai ASN berdasarkan NIP dengan kolom selektif (tanpa menyertakan password).
     */
    private function findAsnUserByNip(string $cleanNip): ?array
    {
        $cols = 'id, nama_lengkap, nip, username, jabatan, pangkat, unit, unit_id, role, status_pegawai, tmt_selesai';

        $db      = \Config\Database::connect();
        $builder = $db->table('users')
                      ->select($cols)
                      ->where('role !=', 'admin')
                      ->where('nip IS NOT NULL', null, false)
                      ->where("TRIM(nip) != ''", null, false)
                      ->groupStart()
                          ->where('nip', $cleanNip)
                          ->orWhere("REPLACE(REPLACE(TRIM(nip), ' ', ''), '-', '') =", $cleanNip)
                      ->groupEnd()
                      ->limit(1);

        $row = $builder->get()->getRowArray();
        if (!$row) {
            return null;
        }

        // Pastikan bukan akun admin lewat tabel pivot user_roles
        if ($db->tableExists('user_roles')) {
            $isAdminPivot = $db->table('user_roles')
                               ->where('user_id', (int) $row['id'])
                               ->where('role_name', 'admin')
                               ->countAllResults() > 0;
            if ($isAdminPivot) {
                return null;
            }
        }

        return $row;
    }

    /**
     * Mengecek apakah nomor identitas (NIP) merupakan akun Administrator (utama maupun multi-role).
     */
    private function isNipBelongsToAdmin(string $cleanNip): bool
    {
        $db   = \Config\Database::connect();
        $user = $db->table('users')
                   ->select('id, role')
                   ->where('nip IS NOT NULL', null, false)
                   ->where("TRIM(nip) != ''", null, false)
                   ->groupStart()
                       ->where('nip', $cleanNip)
                       ->orWhere("REPLACE(REPLACE(TRIM(nip), ' ', ''), '-', '') =", $cleanNip)
                   ->groupEnd()
                   ->limit(1)
                   ->get()
                   ->getRowArray();

        if (!$user) {
            return false;
        }

        if ($user['role'] === 'admin') {
            return true;
        }

        if ($db->tableExists('user_roles')) {
            return $db->table('user_roles')
                      ->where('user_id', (int) $user['id'])
                      ->where('role_name', 'admin')
                      ->countAllResults() > 0;
        }

        return false;
    }

    /**
     * Menentukan atribut Bagian I mana saja yang dikunci (read-only) karena sudah terisi di tabel users,
     * serta memastikan atribut yang masih kosong dibuka penuh agar dapat dilengkapi & memperbarui profil pengguna.
     */
    private function resolveProfileLocks(array $responRow): array
    {
        $default = [
            'q1_locked'                => false,
            'q2_locked'                => true,
            'q6_locked'                => false,
            'q7_locked'                => false,
            'q8_locked'                => false,
            'has_empty_profile_fields' => false,
            'editable_profile_fields'  => [],
            'locked_values'            => [
                2 => (string) ($responRow['nomor_identitas'] ?? ''),
            ],
        ];

        if (($responRow['tipe_responden'] ?? '') !== 'akun_ecc' || empty($responRow['user_id'])) {
            return $default;
        }

        $responId  = (int) ($responRow['id'] ?? 0);
        $userId    = (int) $responRow['user_id'];
        $userModel = new User();
        $user      = $userModel->select('id, nama_lengkap, nip, jabatan, pangkat, unit, unit_id, role')
                               ->find($userId);

        if (!$user) {
            return $default;
        }

        $roleLower    = strtolower((string) ($user['role'] ?? 'user'));
        $isLeadership = in_array($roleLower, ['direktur', 'wadir', 'admin'], true);

        $hasNama    = !SurveiPemetaanPolicy::isEmptyProfileValue($user['nama_lengkap'] ?? '');
        $hasPangkat = !SurveiPemetaanPolicy::isEmptyProfileValue($user['pangkat'] ?? '');
        $hasJabatan = !SurveiPemetaanPolicy::isEmptyProfileValue($user['jabatan'] ?? '');
        $hasUnit    = !SurveiPemetaanPolicy::isEmptyProfileValue($user['unit'] ?? '');

        // Pertahankan hak edit selama sesi draf berjalan bagi kolom profil yang awalnya kosong
        $sessionKey     = $responId > 0 ? ('survei_editable_profile_' . $responId) : '';
        $editableFields = [];
        if ($sessionKey !== '' && ($responRow['status'] ?? 'draft') === 'draft') {
            $savedEditable = session()->get($sessionKey);
            if (is_array($savedEditable)) {
                $editableFields = $savedEditable;
            }
            if (!$hasPangkat && !in_array('pangkat', $editableFields, true)) {
                $editableFields[] = 'pangkat';
            }
            if (!$hasJabatan && !in_array('jabatan', $editableFields, true)) {
                $editableFields[] = 'jabatan';
            }
            if (!$hasUnit && !$isLeadership && !in_array('unit', $editableFields, true)) {
                $editableFields[] = 'unit';
            }
            if (!empty($editableFields)) {
                session()->set($sessionKey, $editableFields);
            }
        }

        $unitModel = new UnitKerja();
        $unitValue = '';
        if ($isLeadership) {
            $unitValue = 'Pimpinan Institusi';
            $hasUnit   = true;
        } else {
            $matchedUnitRow = null;
            if (!empty($user['unit_id'])) {
                $matchedUnitRow = $unitModel->find((int) $user['unit_id']);
            }
            if (!$matchedUnitRow && $hasUnit) {
                $matchedUnitRow = $unitModel->where('nama_unit', trim((string) $user['unit']))->first();
            }

            if ($matchedUnitRow) {
                $unitValue = (string) $matchedUnitRow['nama_unit'];
                $hasUnit   = true;
            } else {
                $hasUnit   = false;
                $unitValue = '';
            }
        }

        // Nama lengkap (No. 1) selalu dibuka agar ASN dapat melengkapi gelar atau mengisi jika kosong.
        // Pangkat (No. 6), Jabatan (No. 7), dan Unit Kerja (No. 8) otomatis dibuka jika di profil masih kosong.
        $q1Locked = false;
        $q6Locked = $hasPangkat && !in_array('pangkat', $editableFields, true);
        $q7Locked = $hasJabatan && !in_array('jabatan', $editableFields, true);
        $q8Locked = ($hasUnit || $isLeadership) && !in_array('unit', $editableFields, true);

        $lockedValues = [
            2 => (string) ($responRow['nomor_identitas'] ?? ''),
        ];
        if ($q6Locked) {
            $lockedValues[6] = trim((string) $user['pangkat']);
        }
        if ($q7Locked && $hasJabatan) {
            $lockedValues[7] = trim((string) $user['jabatan']);
        }
        if ($q8Locked && $unitValue !== '') {
            $lockedValues[8] = $unitValue;
        }

        $hasEmptyToFill = (!$hasNama) || (!$q6Locked) || (!$q7Locked) || (!$q8Locked);

        return [
            'q1_locked'                => $q1Locked,
            'q2_locked'                => true,
            'q6_locked'                => $q6Locked,
            'q7_locked'                => $q7Locked && $hasJabatan,
            'q8_locked'                => $q8Locked && $unitValue !== '',
            'has_empty_profile_fields' => $hasEmptyToFill,
            'editable_profile_fields'  => $editableFields,
            'locked_values'            => $lockedValues,
            'user_row'                 => $user,
        ];
    }

    /**
     * Membuat sesi draf baru untuk pegawai ASN beserta autofill 5 atribut dasar & saran NIP 18 digit.
     */
    private function createOrResetAsnDraft(array $user, string $cleanNip, int $tahun, bool $filledViaAuth, ?array $archivedRow = null): array
    {
        $db           = \Config\Database::connect();
        $responModel  = new SurveiResponModel();
        $jawabanModel = new SurveiJawabanModel();
        $unitModel    = new UnitKerja();

        $userId       = (int) $user['id'];
        $roleLower    = strtolower((string) ($user['role'] ?? 'user'));
        $isLeadership = in_array($roleLower, ['direktur', 'wadir', 'admin'], true);

        $nama    = !SurveiPemetaanPolicy::isEmptyProfileValue($user['nama_lengkap'] ?? '') ? trim((string) $user['nama_lengkap']) : null;
        $pangkat = !SurveiPemetaanPolicy::isEmptyProfileValue($user['pangkat'] ?? '') ? trim((string) $user['pangkat']) : null;
        $jabatan = !SurveiPemetaanPolicy::isEmptyProfileValue($user['jabatan'] ?? '') ? trim((string) $user['jabatan']) : null;

        $unit   = null;
        $unitId = null;
        if ($isLeadership) {
            $unit = 'Pimpinan Institusi';
        } else {
            $matchedUnitRow = null;
            if (!empty($user['unit_id'])) {
                $matchedUnitRow = $unitModel->find((int) $user['unit_id']);
            }
            if (!$matchedUnitRow && !SurveiPemetaanPolicy::isEmptyProfileValue($user['unit'] ?? '')) {
                $matchedUnitRow = $unitModel->where('nama_unit', trim((string) $user['unit']))->first();
            }
            if ($matchedUnitRow) {
                $unit   = (string) $matchedUnitRow['nama_unit'];
                $unitId = (int) $matchedUnitRow['id'];
            }
        }

        $nipSuggest = SurveiPemetaanConfig::suggestFromNip18($cleanNip);

        // Deteksi saran awal jenis kepegawaian (PNS / PPPK)
        $suggestedStatus = $nipSuggest['status_kepegawaian'] ?? null;
        if ($pangkat !== null) {
            if (stripos($pangkat, 'PPPK') !== false || preg_match('/^(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII|XIII|XIV|XV|XVI|XVII)$/i', trim($pangkat)) || preg_match('/Golongan\s+(IX|X|XI|XII|VII|VIII)/i', $pangkat)) {
                $suggestedStatus = 'PPPK';
            } elseif (preg_match('/\((I|II|III|IV)\/[a-e]\)/i', $pangkat) || preg_match('/(Penata|Pembina|Pengatur|Juru)/i', $pangkat)) {
                $suggestedStatus = 'PNS';
            }
        }

        $db->transStart();

        $baseResponData = [
            'user_id'                      => $userId,
            'nomor_identitas'              => $cleanNip,
            'tipe_responden'               => 'akun_ecc',
            'tahun_survei'                 => $tahun,
            'status'                       => 'draft',
            'last_step'                    => 1,
            'filled_via_auth'              => $filledViaAuth ? 1 : 0,
            'pending_profile_sync'         => 0,
            'is_valid'                     => 1,
            'snapshot_nama'                => $nama,
            'snapshot_nip'                 => $cleanNip,
            'snapshot_status_kepegawaian'  => $suggestedStatus,
            'snapshot_pangkat'             => $pangkat,
            'snapshot_jabatan'             => $jabatan,
            'snapshot_unit'                => $unit,
            'snapshot_unit_id'             => $unitId,
            'submitted_at'                 => null,
        ];

        if ($archivedRow && !empty($archivedRow['id'])) {
            $responId = (int) $archivedRow['id'];
            $jawabanModel->where('respon_id', $responId)->delete();
            $responModel->update($responId, $baseResponData);
        } else {
            $responId = (int) $responModel->insert($baseResponData, true);
        }

        $seedItems = [
            [
                'nomor_pertanyaan' => 2,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $cleanNip,
            ],
        ];

        if ($nama !== null) {
            $seedItems[] = [
                'nomor_pertanyaan' => 1,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $nama,
            ];
        }
        if (!empty($nipSuggest['gender'])) {
            $seedItems[] = [
                'nomor_pertanyaan' => 3,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $nipSuggest['gender'],
            ];
        }
        if (!empty($nipSuggest['age'])) {
            $seedItems[] = [
                'nomor_pertanyaan' => 4,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => (string) $nipSuggest['age'],
                'jawaban_angka'    => (int) $nipSuggest['age'],
            ];
        }
        if ($suggestedStatus !== null) {
            $seedItems[] = [
                'nomor_pertanyaan' => 5,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $suggestedStatus,
            ];
        }
        if ($pangkat !== null) {
            $seedItems[] = [
                'nomor_pertanyaan' => 6,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $pangkat,
            ];
        }
        if ($jabatan !== null) {
            $seedItems[] = [
                'nomor_pertanyaan' => 7,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $jabatan,
            ];
        }
        if ($unit !== null) {
            $seedItems[] = [
                'nomor_pertanyaan' => 8,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $unit,
            ];
        }

        $jawabanModel->upsertAnswers($responId, $userId, $seedItems);

        $answersMap = $jawabanModel->getAnswersMap($responId);
        $progress   = SurveiPemetaanConfig::calculateProgress($answersMap);

        $responModel->update($responId, [
            'total_answered'   => $progress['total_answered'],
            'total_applicable' => $progress['total_applicable'],
            'progress_percent' => $progress['progress_percent'],
        ]);

        $db->transComplete();

        $freshRow = $responModel->find($responId);
        if ($db->transStatus() === false || !$freshRow) {
            throw new \RuntimeException('Transaksi pembuatan draf ASN gagal.');
        }

        return $freshRow;
    }

    /**
     * Format data sesi lengkap untuk dikirim ke aplikasi klien (JavaScript Wizard).
     */
    private function buildClientSessionPayload(array $responRow): array
    {
        $responId     = (int) $responRow['id'];
        $jawabanModel = new SurveiJawabanModel();
        $answersMap   = $jawabanModel->getAnswersMap($responId);
        $locks        = $this->resolveProfileLocks($responRow);
        $progress     = SurveiPemetaanConfig::calculateProgress($answersMap);

        return [
            'respon' => [
                'id'               => $responId,
                'tipe_responden'   => $responRow['tipe_responden'],
                'nomor_identitas'  => $responRow['nomor_identitas'],
                'tahun_survei'     => (int) $responRow['tahun_survei'],
                'status'           => $responRow['status'],
                'last_step'        => max(1, min(10, (int) ($responRow['last_step'] ?? 1))),
                'filled_via_auth'  => (int) ($responRow['filled_via_auth'] ?? 0),
                'snapshot_nama'    => $responRow['snapshot_nama'],
            ],
            'locks' => [
                'q1_locked'                => $locks['q1_locked'],
                'q2_locked'                => $locks['q2_locked'],
                'q6_locked'                => $locks['q6_locked'],
                'q7_locked'                => $locks['q7_locked'],
                'q8_locked'                => $locks['q8_locked'],
                'has_empty_profile_fields' => $locks['has_empty_profile_fields'],
            ],
            'answers'  => $this->serializeAnswersForClient($answersMap),
            'progress' => $progress,
        ];
    }

    private function serializeAnswersForClient(array $answersMap): array
    {
        $clientMap = [];
        foreach ($answersMap as $qNo => $row) {
            $rawAngka = $row['jawaban_angka'] ?? null;
            $clientMap[(int) $qNo] = [
                'teks'    => $row['jawaban_teks'] ?? '',
                'angka'   => ($rawAngka !== null && $rawAngka !== '') ? (float) $rawAngka : null,
                'pilihan' => $row['pilihan_array'] ?? [],
                'lainnya' => $row['jawaban_lainnya'] ?? '',
            ];
        }
        return $clientMap;
    }

    /**
     * Memvalidasi, menormalisasi, membersihkan orphan answers, menyimpan jawaban dalam transaksi atomik,
     * serta mengelola flag sinkronisasi profil pegawai ASN secara aman.
     */
    private function processAndPersistAnswers(array $responRow, int $currentStep, int $targetStep, array $submitted, bool $strictStepValidation): array
    {
        $responId     = (int) $responRow['id'];
        $userId       = !empty($responRow['user_id']) ? (int) $responRow['user_id'] : null;
        $questions    = SurveiPemetaanConfig::getQuestions();
        $jawabanModel = new SurveiJawabanModel();
        $responModel  = new SurveiResponModel();
        $unitModel    = new UnitKerja();

        $locks       = $this->resolveProfileLocks($responRow);
        $lockedVals  = $locks['locked_values'] ?? [];
        $allUnits    = $unitModel->select('id, nama_unit')->findAll();
        $unitNameMap = [];
        foreach ($allUnits as $u) {
            $unitNameMap[trim((string) $u['nama_unit'])] = (int) $u['id'];
        }

        $itemsToUpsert = [];
        $q9ValidDate   = null;

        foreach ($submitted as $qKey => $rawItem) {
            $qNo = (int) $qKey;
            if (!isset($questions[$qNo]) || $qNo === 10 || !is_array($rawItem)) {
                continue;
            }

            $meta    = $questions[$qNo];
            $type    = $meta['type'];
            $section = $meta['section'];

            // Paksa nilai atribut terkunci dari tabel users agar tidak bisa dimanipulasi dari klien
            if (isset($lockedVals[$qNo])) {
                $itemsToUpsert[$qNo] = [
                    'nomor_pertanyaan' => $qNo,
                    'kode_bagian'      => $section,
                    'jawaban_teks'     => $lockedVals[$qNo],
                    'jawaban_angka'    => null,
                    'jawaban_pilihan'  => null,
                    'jawaban_lainnya'  => null,
                ];
                continue;
            }

            $teks    = $this->cleanText((string) ($rawItem['teks'] ?? ''), $type === 'textarea', (int) ($meta['max_length'] ?? ($type === 'textarea' ? 2500 : 255)));
            $lainnya = $this->cleanText((string) ($rawItem['lainnya'] ?? ''), false, 300);
            $angka   = null;
            $pilihan = null;

            switch ($type) {
                case 'text':
                case 'identitas':
                case 'datalist':
                case 'textarea':
                    $lainnya = null;
                    break;

                case 'unit_select':
                    if ($teks !== '' && !isset($unitNameMap[$teks]) && $teks !== 'Pimpinan Institusi') {
                        $teks = '';
                    }
                    $lainnya = null;
                    break;

                case 'date':
                    if ($teks !== '') {
                        $tenure = SurveiPemetaanConfig::computeTenureFromDate($teks);
                        if ($tenure === null) {
                            return [
                                'status'  => 'error',
                                'message' => 'Tanggal mulai bekerja (Nomor 9) tidak valid atau melebihi hari ini.',
                            ];
                        }
                        $q9ValidDate = [
                            'date'   => $teks,
                            'tenure' => $tenure,
                        ];
                    }
                    $lainnya = null;
                    break;

                case 'number':
                    $rawNum = trim((string) ($rawItem['angka'] ?? $rawItem['teks'] ?? ''));
                    $rawNum = str_replace(',', '.', $rawNum);
                    if ($rawNum !== '' && is_numeric($rawNum)) {
                        $intVal = (int) round((float) $rawNum);
                        $minVal = (int) ($meta['min'] ?? 0);
                        $maxVal = (int) ($meta['max'] ?? 999);
                        if ($intVal < $minVal || $intVal > $maxVal) {
                            return [
                                'status'  => 'error',
                                'message' => 'Nilai pada Pertanyaan Nomor ' . $qNo . ' harus berada di antara ' . $minVal . ' s.d. ' . $maxVal . '.',
                            ];
                        }
                        $angka = $intVal;
                        $teks  = (string) $intVal;
                    } else {
                        $angka = null;
                        $teks  = '';
                    }
                    $lainnya = null;
                    break;

                case 'radio':
                case 'radio_optional_note':
                    $allowedOptions = $meta['options'] ?? [];
                    if ($teks !== '' && !in_array($teks, $allowedOptions, true)) {
                        $teks = '';
                    }
                    if (!empty($meta['has_other']) && $teks === ($meta['other_value'] ?? 'Lainnya')) {
                        // Pertahankan jawaban_lainnya
                    } elseif ($type === 'radio_optional_note' && $teks === ($meta['note_trigger_value'] ?? '')) {
                        // Pertahankan catatan opsional
                    } else {
                        $lainnya = null;
                    }
                    break;

                case 'radio_scored':
                    $allowedOptions = $meta['options'] ?? [];
                    $scoreMap       = $meta['score_map'] ?? [];
                    if ($teks !== '' && in_array($teks, $allowedOptions, true)) {
                        $angka = isset($scoreMap[$teks]) ? (int) $scoreMap[$teks] : null;
                    } else {
                        $teks  = '';
                        $angka = null;
                    }
                    $lainnya = null;
                    break;

                case 'likert_1_5':
                case 'scale_custom':
                    $rawScore = (int) ($rawItem['angka'] ?? 0);
                    $labels   = $meta['scale_labels'] ?? [];
                    if ($rawScore >= 1 && $rawScore <= 5 && isset($labels[$rawScore])) {
                        $angka = $rawScore;
                        $teks  = $labels[$rawScore];
                    } else {
                        $angka = null;
                        $teks  = '';
                    }
                    $lainnya = null;
                    break;

                case 'checkbox':
                    $rawArr         = is_array($rawItem['pilihan'] ?? null) ? $rawItem['pilihan'] : [];
                    $allowedOptions = $meta['options'] ?? [];
                    $validSelected  = [];
                    foreach ($rawArr as $opt) {
                        $optStr = trim((string) $opt);
                        if (in_array($optStr, $allowedOptions, true) && !in_array($optStr, $validSelected, true)) {
                            $validSelected[] = $optStr;
                        }
                    }

                    // Aturan eksklusif (misal No. 31 & No. 57)
                    if (!empty($meta['exclusive_option']) && in_array($meta['exclusive_option'], $validSelected, true)) {
                        $conflicts     = $meta['exclusive_conflicts'] ?? [];
                        $validSelected = array_values(array_filter(
                            $validSelected,
                            static fn($v) => !in_array($v, $conflicts, true)
                        ));
                    }

                    // Batas kuota maksimal pilihan (misal No. 31 maks 3, No. 56 maks 3, No. 74 maks 5)
                    if (!empty($meta['max_select']) && count($validSelected) > (int) $meta['max_select']) {
                        if ($strictStepValidation) {
                            return [
                                'status'  => 'error',
                                'message' => 'Pertanyaan Nomor ' . $qNo . ' maksimal memilih ' . (int) $meta['max_select'] . ' jawaban.',
                            ];
                        }
                        $validSelected = array_slice($validSelected, 0, (int) $meta['max_select']);
                    }

                    if (empty($meta['has_other']) || !in_array(($meta['other_value'] ?? 'Lainnya'), $validSelected, true)) {
                        $lainnya = null;
                    }

                    $pilihan = $validSelected;
                    $teks    = !empty($validSelected) ? implode('; ', $validSelected) : '';
                    break;

                case 'triple_text':
                    $rawArr = is_array($rawItem['pilihan'] ?? null) ? array_values($rawItem['pilihan']) : [];
                    $triple = [];
                    $lines  = [];
                    for ($i = 0; $i < 3; $i++) {
                        $val      = $this->cleanText((string) ($rawArr[$i] ?? ''), false, 300);
                        $triple[] = $val;
                        if ($val !== '') {
                            $lines[] = ($i + 1) . '. ' . $val;
                        }
                    }
                    $hasAnyTriple = !empty($lines);
                    $pilihan      = $hasAnyTriple ? $triple : [];
                    $teks         = $hasAnyTriple ? implode(' | ', $lines) : '';
                    $lainnya      = null;
                    break;
            }

            $itemsToUpsert[$qNo] = [
                'nomor_pertanyaan' => $qNo,
                'kode_bagian'      => $section,
                'jawaban_teks'     => $teks !== '' ? $teks : null,
                'jawaban_angka'    => $angka,
                'jawaban_pilihan'  => !empty($pilihan) ? $pilihan : null,
                'jawaban_lainnya'  => ($lainnya !== null && $lainnya !== '') ? $lainnya : null,
            ];
        }

        // Pastikan atribut terkunci selalu utuh
        foreach ($lockedVals as $lNo => $lVal) {
            if (!isset($itemsToUpsert[$lNo])) {
                $itemsToUpsert[$lNo] = [
                    'nomor_pertanyaan' => $lNo,
                    'kode_bagian'      => 'BAGIAN_1',
                    'jawaban_teks'     => $lVal,
                    'jawaban_angka'    => null,
                    'jawaban_pilihan'  => null,
                    'jawaban_lainnya'  => null,
                ];
            }
        }

        // Otomatis hitung Masa Kerja (Nomor 10) apabila Nomor 9 dikirimkan
        if ($q9ValidDate !== null) {
            $t = $q9ValidDate['tenure'];
            $itemsToUpsert[10] = [
                'nomor_pertanyaan' => 10,
                'kode_bagian'      => 'BAGIAN_1',
                'jawaban_teks'     => $t['bucket'],
                'jawaban_angka'    => $t['decimal_years'],
                'jawaban_pilihan'  => null,
                'jawaban_lainnya'  => $t['display'],
            ];
        }

        $db = \Config\Database::connect();

        try {
            $db->transStart();

            if (!empty($itemsToUpsert)) {
                $jawabanModel->upsertAnswers($responId, $userId, array_values($itemsToUpsert));
            }

            // Cek & bersihkan orphan answers pada cabang kondisional yang tidak berlaku
            $currentMap = $jawabanModel->getAnswersMap($responId);

            // Sinkronisasi Q10 jika Q9 sudah pernah tersimpan sebelumnya tetapi Q10 belum ada
            if ($q9ValidDate === null && !empty($currentMap[9]['jawaban_teks'])) {
                $reTenure = SurveiPemetaanConfig::computeTenureFromDate((string) $currentMap[9]['jawaban_teks']);
                if ($reTenure !== null) {
                    $jawabanModel->upsertAnswers($responId, $userId, [[
                        'nomor_pertanyaan' => 10,
                        'kode_bagian'      => 'BAGIAN_1',
                        'jawaban_teks'     => $reTenure['bucket'],
                        'jawaban_angka'    => $reTenure['decimal_years'],
                        'jawaban_pilihan'  => null,
                        'jawaban_lainnya'  => $reTenure['display'],
                    ]]);
                    $currentMap = $jawabanModel->getAnswersMap($responId);
                }
            }

            $orphans = SurveiPemetaanConfig::getOrphanQuestionNumbers($currentMap);
            if (!empty($orphans)) {
                $jawabanModel->deleteOrphanAnswers($responId, $orphans);
                foreach ($orphans as $oNo) {
                    unset($currentMap[$oNo]);
                }
            }

            $progress = SurveiPemetaanConfig::calculateProgress($currentMap);

            // Perbarui snapshot profil pada survei_pemetaan_respon
            $snapNama    = trim((string) ($currentMap[1]['jawaban_teks'] ?? ($responRow['snapshot_nama'] ?? '')));
            $snapStatus  = trim((string) ($currentMap[5]['jawaban_teks'] ?? ($responRow['snapshot_status_kepegawaian'] ?? '')));
            $snapPangkat = trim((string) ($currentMap[6]['jawaban_teks'] ?? ''));
            $snapJabatan = trim((string) ($currentMap[7]['jawaban_teks'] ?? ''));
            $snapUnit    = trim((string) ($currentMap[8]['jawaban_teks'] ?? ''));
            $snapUnitId  = isset($unitNameMap[$snapUnit]) ? $unitNameMap[$snapUnit] : null;

            $missingInCurrentStep = $progress['missing_by_step'][$currentStep] ?? [];
            $nextSavedStep        = ($strictStepValidation && !empty($missingInCurrentStep))
                ? $currentStep
                : $targetStep;

            $editableFields  = $locks['editable_profile_fields'] ?? [];
            $pendingSyncFlag = (int) ($responRow['pending_profile_sync'] ?? 0);
            if (($responRow['tipe_responden'] ?? '') === 'akun_ecc' && !empty($locks['user_row'])) {
                $uRow        = $locks['user_row'];
                $currentNama = trim((string) ($uRow['nama_lengkap'] ?? ''));
                $editedNama  = ($snapNama !== '' && $snapNama !== $currentNama);

                $filledEmptyAttr = $editedNama
                    || ((SurveiPemetaanPolicy::isEmptyProfileValue($uRow['pangkat'] ?? '') || in_array('pangkat', $editableFields, true)) && $snapPangkat !== '')
                    || ((SurveiPemetaanPolicy::isEmptyProfileValue($uRow['jabatan'] ?? '') || in_array('jabatan', $editableFields, true)) && $snapJabatan !== '')
                    || ((SurveiPemetaanPolicy::isEmptyProfileValue($uRow['unit'] ?? '') || in_array('unit', $editableFields, true)) && $snapUnit !== '' && $snapUnit !== 'Pimpinan Institusi');

                if ($filledEmptyAttr) {
                    $pendingSyncFlag = 1;
                }
            }

            $responUpdate = [
                'last_step'                    => $nextSavedStep,
                'total_answered'               => $progress['total_answered'],
                'total_applicable'             => $progress['total_applicable'],
                'progress_percent'             => $progress['progress_percent'],
                'pending_profile_sync'         => $pendingSyncFlag,
                'snapshot_nama'                => $snapNama !== '' ? $snapNama : null,
                'snapshot_status_kepegawaian'  => $snapStatus !== '' ? $snapStatus : null,
                'snapshot_pangkat'             => $snapPangkat !== '' ? $snapPangkat : null,
                'snapshot_jabatan'             => $snapJabatan !== '' ? $snapJabatan : null,
                'snapshot_unit'                => $snapUnit !== '' ? $snapUnit : null,
                'snapshot_unit_id'             => $snapUnitId,
            ];

            $responModel->update($responId, $responUpdate);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return [
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan progres survei. Silakan coba lagi.',
                ];
            }

            // Sinkronisasi langsung apabila sesi ASN sudah terverifikasi lewat login/kata sandi ECC
            if ($pendingSyncFlag === 1 && $userId !== null) {
                $isVerifiedAsn = ((int) ($responRow['filled_via_auth'] ?? 0) === 1)
                    || (session()->get('isLoggedIn') && SurveiPemetaanPolicy::getCurrentUserId() === $userId);
                if ($isVerifiedAsn) {
                    $freshRow = array_merge($responRow, $responUpdate, ['id' => $responId]);
                    SurveiPemetaanPolicy::syncEmptyProfileFields($userId, $freshRow, true, true, $editableFields);
                }
            }

            if ($strictStepValidation && !empty($missingInCurrentStep)) {
                $firstNo    = (int) $missingInCurrentStep[0];
                $missingMsg = SurveiPemetaanConfig::describeMissingQuestion($firstNo, $currentMap);
                return [
                    'status'        => 'incomplete_step',
                    'step'          => $currentStep,
                    'first_missing' => $firstNo,
                    'missing'       => $missingInCurrentStep,
                    'progress'      => $progress,
                    'answers'       => $this->serializeAnswersForClient($currentMap),
                    'message'       => $missingMsg,
                ];
            }

            return [
                'status'   => 'success',
                'progress' => $progress,
                'answers'  => $this->serializeAnswersForClient($currentMap),
            ];
        } catch (\Throwable $e) {
            log_message('error', 'SurveiPemetaanController::processAndPersistAnswers error: ' . $e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'Gagal menyimpan jawaban. Silakan coba lagi.',
            ];
        }
    }

    private function cleanText(string $input, bool $allowNewlines, int $maxLength): string
    {
        $str = trim($input);
        if ($str === '') {
            return '';
        }

        if ($allowNewlines) {
            $str = preg_replace('/[^\P{C}\n\r\t]+/u', '', $str) ?? $str;
            $str = str_replace(["\r\n", "\r"], "\n", $str);
        } else {
            $str = preg_replace('/[\p{C}]+/u', ' ', $str) ?? $str;
            $str = preg_replace('/\s+/u', ' ', $str) ?? $str;
        }

        $str = trim($str);
        if ($maxLength > 0 && mb_strlen($str) > $maxLength) {
            $str = mb_substr($str, 0, $maxLength);
        }

        return $str;
    }
}

