<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\TracerStudyService;

/**
 * Controller Tautan Publik Tracer Study (/tracer/{token} atau /{tracerSlug}).
 * Mendukung dua alur sesuai dokumen resmi PKTJ 2025:
 * 1. Target Alumni (`alumni`): verifikasi Nomor Taruna + Tanggal Lahir (atau pendaftaran alumni baru) -> Formulir Survei Lulusan.
 * 2. Target Pengguna Lulusan (`mitra`): langsung ke Formulir Penilaian Atasan Langsung tanpa login, mendukung pengisian > 1 alumni.
 */
class TracerStudyPublicController extends BaseController
{
    protected TracerStudyService $service;

    public function __construct(?TracerStudyService $service = null)
    {
        $this->service = $service ?? new TracerStudyService();
        helper(['ketarunaan', 'audit']);
    }

    /**
     * Halaman utama tautan publik (/{tracerSlug}).
     */
    public function index(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return view('ketarunaan/tracer_study/public_status', [
                'title'   => 'Tautan Tidak Tersedia - Evidence Command Center (ECC)',
                'heading' => 'Tautan Survei Tidak Ditemukan',
                'message' => 'Tautan Tracer Study tidak valid atau sudah diperbarui. Silakan hubungi panitia alumni PKTJ.',
                'status'  => 'invalid',
            ]);
        }

        $periode = $detail['periode'];
        if (!ketarunaan_is_tracer_periode_open($periode)) {
            $isDraft = (($periode['status_periode'] ?? '') === 'draf');
            return view('ketarunaan/tracer_study/public_status', [
                'title'   => 'Survei Belum / Tidak Dibuka - Evidence Command Center (ECC)',
                'heading' => $isDraft ? 'Survei Belum Dibuka' : 'Periode Survei Telah Ditutup',
                'message' => $isDraft
                    ? 'Periode survei ini masih dalam persiapan. Silakan akses kembali setelah resmi dibuka.'
                    : 'Periode pengisian Tracer Study ini telah ditutup. Terima kasih atas partisipasi Anda.',
                'periode' => $periode,
                'status'  => $isDraft ? 'draf' : 'ditutup',
            ]);
        }

        $pid     = (int) $periode['id'];
        $isMitra = !empty($periode['is_mitra']);

        if ($isMitra) {
            $ctx = $this->service->getSurveyFormContext($pid, null, false);
            if (!$ctx['status']) {
                return view('ketarunaan/tracer_study/public_status', [
                    'title'   => 'Survei Tidak Tersedia - Evidence Command Center (ECC)',
                    'heading' => 'Formulir Tidak Tersedia',
                    'message' => (string) ($ctx['message'] ?? 'Survei sedang tidak tersedia.'),
                    'periode' => $periode,
                    'status'  => 'ditutup',
                ]);
            }

            $mitraMap  = session()->get('tracer_public_mitra_prefill');
            $mitraPrev = (is_array($mitraMap) && isset($mitraMap[$pid]) && is_array($mitraMap[$pid])) ? $mitraMap[$pid] : [];

            return view('ketarunaan/tracer_study/public_mitra_form', [
                'title'     => $periode['nama_periode'] . ' - Tracer Study ECC',
                'token'     => $token,
                'periode'   => $periode,
                'ctx'       => $ctx,
                'mitraPrev' => $mitraPrev,
                'refs'      => $this->service->getReferenceOptions(),
            ]);
        }

        $verified = $this->getVerifiedSession($pid);
        if (!$verified) {
            $allowUnreg = ((int) ($periode['allow_unregistered_alumni'] ?? 1) === 1);
            $showDaftar = $allowUnreg && ($this->request->getGet('daftar') === '1' || session()->getFlashdata('pub_show_daftar'));

            return view('ketarunaan/tracer_study/public_verify', [
                'title'      => 'Verifikasi Alumni - ' . $periode['nama_periode'],
                'periode'    => $periode,
                'token'      => $token,
                'allowUnreg' => $allowUnreg,
                'showDaftar' => $showDaftar,
                'refs'       => $this->service->getReferenceOptions(),
            ]);
        }

        $ctx = $this->service->getSurveyFormContext($pid, (string) $verified['nomor_taruna'], true, $verified);
        if (!$ctx['status']) {
            if (!empty($ctx['already_submitted'])) {
                return redirect()->to(ketarunaan_tracer_public_url($token, 'selesai'));
            }
            $this->clearVerifiedSession($pid);
            return redirect()->to(ketarunaan_tracer_public_url($token))
                ->with('error', $ctx['message']);
        }

        return view('ketarunaan/tracer_study/public_form', [
            'title'    => $periode['nama_periode'] . ' - Tracer Study ECC',
            'token'    => $token,
            'periode'  => $periode,
            'verified' => $verified,
            'ctx'      => $ctx,
            'refs'     => $this->service->getReferenceOptions(),
        ]);
    }

    /**
     * Proses verifikasi identitas alumni (POST /{slug}/verifikasi).
     */
    public function verify(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode = $detail['periode'];
        $pid     = (int) $periode['id'];

        if (!empty($periode['is_mitra'])) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        if (trim((string) ($this->request->getPost('website_url') ?? '')) !== '') {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $throttler = \Config\Services::throttler();
        $rateKey   = 'ts_pub_verify_' . md5((string) $this->request->getIPAddress() . '_' . $pid);
        if ($throttler->check($rateKey, 10, 600) === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak percobaan verifikasi. Tunggu beberapa menit lalu coba lagi.');
        }

        $nomorTaruna  = trim((string) ($this->request->getPost('nomor_taruna') ?? ''));
        $tanggalLahir = trim((string) ($this->request->getPost('tanggal_lahir') ?? ''));
        $tahunLulus   = (int) ($this->request->getPost('tahun_lulus') ?? 0);

        $check = $this->service->verifyPublicAlumniIdentity($pid, $nomorTaruna, $tanggalLahir, $tahunLulus);
        if (!$check['status']) {
            return redirect()->back()->withInput()->with('error', $check['message']);
        }

        if (($check['mode'] ?? 'registered') === 'needs_registration') {
            return redirect()->to(ketarunaan_tracer_public_url($token) . '?daftar=1')
                ->with('pub_show_daftar', true)
                ->with('pub_prefill_notar', (string) ($check['nomor_taruna'] ?? $nomorTaruna))
                ->with('pub_prefill_dob', (string) ($check['tanggal_lahir'] ?? $tanggalLahir))
                ->with('pub_prefill_tahun', (int) ($check['tahun_lulus'] ?? $tahunLulus))
                ->with('info', 'Nomor Taruna Anda belum tercatat di sistem. Silakan lengkapi identitas alumni di bawah untuk melanjutkan survei.');
        }

        $this->setVerifiedSession($pid, $check['alumni']);
        return redirect()->to(ketarunaan_tracer_public_url($token));
    }

    /**
     * Proses pendaftaran identitas alumni baru sebelum mengisi survei (POST /{slug}/daftar).
     */
    public function register(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode = $detail['periode'];
        $pid     = (int) $periode['id'];

        if (!empty($periode['is_mitra'])) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        if (trim((string) ($this->request->getPost('website_url') ?? '')) !== '') {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $throttler = \Config\Services::throttler();
        $rateKey   = 'ts_pub_reg_' . md5((string) $this->request->getIPAddress() . '_' . $pid);
        if ($throttler->check($rateKey, 10, 600) === false) {
            return redirect()->back()->withInput()
                ->with('pub_show_daftar', true)
                ->with('error', 'Terlalu banyak percobaan. Tunggu beberapa menit lalu coba lagi.');
        }

        $input = [
            'nomor_taruna'     => $this->request->getPost('nomor_taruna'),
            'nama_lengkap'     => $this->request->getPost('nama_lengkap'),
            'program_studi_id' => $this->request->getPost('program_studi_id'),
            'tahun_lulus'      => $this->request->getPost('tahun_lulus'),
            'tanggal_lahir'    => $this->request->getPost('tanggal_lahir'),
            'no_hp'            => $this->request->getPost('no_hp'),
            'email'            => $this->request->getPost('email'),
        ];

        $check = $this->service->registerPublicUnregisteredIdentity($pid, $input);
        if (!$check['status']) {
            return redirect()->back()->withInput()
                ->with('pub_show_daftar', true)
                ->with('error', $check['message']);
        }

        $this->setVerifiedSession($pid, $check['alumni']);
        return redirect()->to(ketarunaan_tracer_public_url($token));
    }

    /**
     * Proses kirim jawaban survei melalui tautan publik (POST /{slug}/simpan).
     */
    public function submit(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode = $detail['periode'];
        $pid     = (int) $periode['id'];
        $isMitra = !empty($periode['is_mitra']);

        if ($isMitra) {
            if (trim((string) ($this->request->getPost('website_url') ?? '')) !== '') {
                return redirect()->to(ketarunaan_tracer_public_url($token));
            }

            $throttler = \Config\Services::throttler();
            $rateKey   = 'ts_pub_mitra_' . md5((string) $this->request->getIPAddress() . '_' . $pid);
            if ($throttler->check($rateKey, 30, 600) === false) {
                return redirect()->back()->withInput()
                    ->with('error', 'Terlalu banyak pengiriman dalam waktu singkat. Tunggu beberapa menit lalu coba lagi.');
            }

            $rawJawaban = $this->request->getPost('jawaban') ?? [];
            $rawOther   = $this->request->getPost('jawaban_other') ?? [];
            $result     = $this->service->submitMitraRespons($pid, [
                'jawaban'       => $rawJawaban,
                'jawaban_other' => $rawOther,
            ], 'Pengguna Lulusan (Tautan Publik)', true);

            if (!$result['status']) {
                return redirect()->back()->withInput()->with('error', $result['message']);
            }

            $prefillMap = session()->get('tracer_public_mitra_prefill');
            if (!is_array($prefillMap)) {
                $prefillMap = [];
            }
            $prevCount = (int) ($prefillMap[$pid]['count_submitted'] ?? 0);
            $emailKeep = '';
            $hpKeep    = '';
            foreach ($detail['pertanyaan'] as $pq) {
                $pk   = strtoupper((string) ($pq['kode_pertanyaan'] ?? ''));
                $pqid = (int) $pq['id'];
                if ($pk === 'TSM25_EMAIL' && isset($rawJawaban[$pqid])) {
                    $emailKeep = trim((string) $rawJawaban[$pqid]);
                } elseif ($pk === 'TSM25_NO_HP' && isset($rawJawaban[$pqid])) {
                    $hpKeep = trim((string) $rawJawaban[$pqid]);
                }
            }

            $prefillMap[$pid] = [
                'TSM25_NAMA_ATASAN' => (string) ($result['nama_atasan'] ?? ''),
                'TSM25_INSTANSI'    => (string) ($result['instansi'] ?? ''),
                'TSM25_EMAIL'       => $emailKeep,
                'TSM25_NO_HP'       => $hpKeep,
                'last_alumni'       => (string) ($result['nama_alumni'] ?? ''),
                'last_prodi'        => (string) ($result['prodi_label'] ?? ''),
                'count_submitted'   => $prevCount + 1,
            ];
            session()->set('tracer_public_mitra_prefill', $prefillMap);

            if (function_exists('log_audit')) {
                log_audit('SUBMIT_TRACER_MITRA_PUBLIC', 'tracer_study_respons', (string) $result['id'], null, [
                    'periode_id'  => $pid,
                    'instansi'    => $result['instansi'] ?? '',
                    'nama_alumni' => $result['nama_alumni'] ?? '',
                ]);
            }

            return redirect()->to(ketarunaan_tracer_public_url($token, 'selesai'))
                ->with('success', $result['message']);
        }

        $verified = $this->getVerifiedSession($pid);
        if (!$verified || empty($verified['nomor_taruna'])) {
            return redirect()->to(ketarunaan_tracer_public_url($token))
                ->with('error', 'Sesi verifikasi berakhir. Silakan verifikasi ulang identitas Anda.');
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
            'sumber_pengisian'    => 'tautan_publik',
            'jawaban'             => $this->request->getPost('jawaban') ?? [],
            'jawaban_other'       => $this->request->getPost('jawaban_other') ?? [],
        ];

        $actorLabel = trim((string) ($verified['nama_lengkap'] ?? $verified['nomor_taruna'])) . ' (Tautan Publik)';
        $result     = $this->service->submitRespons($pid, (string) $verified['nomor_taruna'], $input, $actorLabel, true, $verified);

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        if (function_exists('log_audit')) {
            log_audit('SUBMIT_TRACER_PUBLIC', 'tracer_study_respons', (string) $result['id'], null, [
                'periode_id'   => $pid,
                'nomor_taruna' => $verified['nomor_taruna'],
            ]);
        }

        return redirect()->to(ketarunaan_tracer_public_url($token, 'selesai'))
            ->with('success', $result['message']);
    }

    /**
     * Halaman konfirmasi selesai setelah mengirim survei (/{slug}/selesai).
     */
    public function selesai(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode  = $detail['periode'];
        $pid      = (int) $periode['id'];
        $isMitra  = !empty($periode['is_mitra']);
        $verified = $this->getVerifiedSession($pid);

        $mitraLast = null;
        if ($isMitra) {
            $prefillMap = session()->get('tracer_public_mitra_prefill');
            if (is_array($prefillMap) && isset($prefillMap[$pid]) && is_array($prefillMap[$pid])) {
                $mitraLast = $prefillMap[$pid];
            }
        }

        return view('ketarunaan/tracer_study/public_status', [
            'title'     => 'Terima Kasih - Tracer Study ECC',
            'heading'   => $isMitra ? 'Penilaian Lulusan Tersimpan' : 'Jawaban Survei Tersimpan',
            'message'   => $isMitra
                ? 'Terima kasih atas kesediaan Bapak/Ibu menilai kinerja lulusan PKTJ Tegal. Apabila terdapat alumni PKTJ lain di instansi Bapak/Ibu, formulir dapat diisi kembali.'
                : 'Terima kasih telah berpartisipasi dalam Tracer Study PKTJ Tegal. Data Anda sangat bermanfaat bagi peningkatan mutu pendidikan dan layanan karier.',
            'periode'   => $periode,
            'verified'  => $verified,
            'isMitra'   => $isMitra,
            'mitraLast' => $mitraLast,
            'token'     => $token,
            'status'    => 'selesai',
        ]);
    }

    /**
     * Menghapus sesi verifikasi pada perangkat bersama (POST /{slug}/ganti-alumni).
     */
    public function resetSession(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if ($detail) {
            $pid = (int) $detail['periode']['id'];
            $this->clearVerifiedSession($pid);
            $mitraMap = session()->get('tracer_public_mitra_prefill');
            if (is_array($mitraMap) && isset($mitraMap[$pid])) {
                unset($mitraMap[$pid]);
                session()->set('tracer_public_mitra_prefill', $mitraMap);
            }
        }

        return redirect()->to(ketarunaan_tracer_public_url($token));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getVerifiedSession(int $periodeId): ?array
    {
        $map = session()->get('tracer_public_verified');
        if (!is_array($map) || empty($map[$periodeId]) || !is_array($map[$periodeId])) {
            return null;
        }
        return $map[$periodeId];
    }

    /**
     * @param array<string, mixed> $alumni
     */
    private function setVerifiedSession(int $periodeId, array $alumni): void
    {
        $map = session()->get('tracer_public_verified');
        if (!is_array($map)) {
            $map = [];
        }
        $map[$periodeId] = $alumni;
        session()->set('tracer_public_verified', $map);
    }

    private function clearVerifiedSession(int $periodeId): void
    {
        $map = session()->get('tracer_public_verified');
        if (is_array($map) && isset($map[$periodeId])) {
            unset($map[$periodeId]);
            session()->set('tracer_public_verified', $map);
        }
    }
}
