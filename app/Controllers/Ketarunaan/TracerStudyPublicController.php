<?php

namespace App\Controllers\Ketarunaan;

use App\Controllers\BaseController;
use App\Libraries\Ketarunaan\TracerStudyService;

/**
 * Controller Tautan Publik Tracer Study (/tracer/{token}).
 * Memungkinkan alumni mengisi survei melalui tautan yang disebar tanpa login ECC,
 * dengan verifikasi identitas (Nomor Taruna + Tanggal Lahir), perlindungan CSRF,
 * rate limiting, dan penutupan otomatis saat periode ditutup oleh pegawai berwenang.
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
     * Halaman utama tautan publik (/tracer/{token}).
     * - Jika periode tidak valid / tutup -> halaman status.
     * - Jika alumni belum verifikasi -> Langkah 1 (Verifikasi Identitas).
     * - Jika sudah verifikasi di sesi -> Langkah 2 (Formulir Survei).
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

        $pid      = (int) $periode['id'];
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
     * Proses verifikasi identitas alumni (POST /tracer/{token}/verifikasi).
     */
    public function verify(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode = $detail['periode'];
        $pid     = (int) $periode['id'];

        // Honeypot anti-bot
        if (trim((string) ($this->request->getPost('website_url') ?? '')) !== '') {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        // Rate limiting per IP + periode untuk mencegah percobaan acak berulang
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
     * Proses pendaftaran identitas alumni baru (belum ada di database) sebelum mengisi survei (POST /{slug}/daftar).
     */
    public function register(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode = $detail['periode'];
        $pid     = (int) $periode['id'];

        // Honeypot anti-bot
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
     * Proses kirim jawaban survei melalui tautan publik (POST /tracer/{token}/simpan).
     */
    public function submit(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode  = $detail['periode'];
        $pid      = (int) $periode['id'];
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
     * Halaman konfirmasi selesai setelah alumni mengirim survei (/tracer/{token}/selesai).
     */
    public function selesai(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if (!$detail) {
            return redirect()->to(ketarunaan_tracer_public_url($token));
        }

        $periode  = $detail['periode'];
        $pid      = (int) $periode['id'];
        $verified = $this->getVerifiedSession($pid);

        return view('ketarunaan/tracer_study/public_status', [
            'title'    => 'Terima Kasih - Tracer Study ECC',
            'heading'  => 'Jawaban Survei Tersimpan',
            'message'  => 'Terima kasih telah berpartisipasi dalam Tracer Study PKTJ Tegal. Data Anda sangat bermanfaat bagi peningkatan mutu pendidikan dan layanan karier.',
            'periode'  => $periode,
            'verified' => $verified,
            'token'    => $token,
            'status'   => 'selesai',
        ]);
    }

    /**
     * Menghapus sesi verifikasi alumni pada perangkat bersama (POST /tracer/{token}/ganti-alumni).
     */
    public function resetSession(string $token)
    {
        $detail = $this->service->getPeriodeByPublicToken($token);
        if ($detail) {
            $this->clearVerifiedSession((int) $detail['periode']['id']);
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
