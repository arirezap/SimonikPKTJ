<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\AlumniProfilModel;
use App\Models\Ketarunaan\TracerStudyBundleModel;
use App\Models\Ketarunaan\TracerStudyBundlePertanyaanModel;
use App\Models\Ketarunaan\TracerStudyJawabanModel;
use App\Models\Ketarunaan\TracerStudyPeriodeModel;
use App\Models\Ketarunaan\TracerStudyPeriodePertanyaanModel;
use App\Models\Ketarunaan\TracerStudyResponsModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Service terpusat Modul Tracer Study (ECC 2.0).
 * Mengelola bundel pertanyaan reusable, snapshot pertanyaan per periode (freeze-on-open),
 * kontrol buka/tutup periode berbasis role pegawai, pengisian alumni/PIC,
 * serta pemantauan progres & analitik hasil setiap periode pembukaan.
 */
class TracerStudyService
{
    protected BaseConnection $db;
    protected TracerStudyBundleModel $bundleModel;
    protected TracerStudyBundlePertanyaanModel $bundlePertanyaanModel;
    protected TracerStudyPeriodeModel $periodeModel;
    protected TracerStudyPeriodePertanyaanModel $periodePertanyaanModel;
    protected TracerStudyResponsModel $responsModel;
    protected TracerStudyJawabanModel $jawabanModel;
    protected AlumniProfilModel $alumniModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?TracerStudyBundleModel $bundleModel = null,
        ?TracerStudyBundlePertanyaanModel $bundlePertanyaanModel = null,
        ?TracerStudyPeriodeModel $periodeModel = null,
        ?TracerStudyPeriodePertanyaanModel $periodePertanyaanModel = null,
        ?TracerStudyResponsModel $responsModel = null,
        ?TracerStudyJawabanModel $jawabanModel = null,
        ?AlumniProfilModel $alumniModel = null
    ) {
        $this->db                     = $db ?? Database::connect();
        $this->bundleModel            = $bundleModel ?? new TracerStudyBundleModel();
        $this->bundlePertanyaanModel  = $bundlePertanyaanModel ?? new TracerStudyBundlePertanyaanModel();
        $this->periodeModel           = $periodeModel ?? new TracerStudyPeriodeModel();
        $this->periodePertanyaanModel = $periodePertanyaanModel ?? new TracerStudyPeriodePertanyaanModel();
        $this->responsModel           = $responsModel ?? new TracerStudyResponsModel();
        $this->jawabanModel           = $jawabanModel ?? new TracerStudyJawabanModel();
        $this->alumniModel            = $alumniModel ?? new AlumniProfilModel();
        helper('ketarunaan');
    }

    /**
     * Referensi dasar untuk filter dan formulir Tracer Study.
     *
     * @return array<string, mixed>
     */
    public function getReferenceOptions(): array
    {
        $prodi = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $tahunLulusRows = $this->db->table('alumni_profil')
            ->select('DISTINCT tahun_lulus', false)
            ->where('status_profil', 'aktif')
            ->where('tahun_lulus IS NOT NULL', null, false)
            ->orderBy('tahun_lulus', 'DESC')
            ->get()
            ->getResultArray();

        $tahunLulus = [];
        foreach ($tahunLulusRows as $r) {
            $yr = (int) ($r['tahun_lulus'] ?? 0);
            if ($yr > 0) {
                $tahunLulus[] = $yr;
            }
        }
        $curYear = (int) date('Y');
        for ($y = $curYear; $y >= $curYear - 4; $y--) {
            if (!in_array($y, $tahunLulus, true)) {
                $tahunLulus[] = $y;
            }
        }
        rsort($tahunLulus);

        $bundles = $this->listBundles();

        $periodes = $this->db->table('tracer_study_periode')
            ->orderBy("FIELD(status_periode, 'dibuka', 'draf', 'ditutup')", '', false)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return [
            'prodi'              => $prodi,
            'tahun_lulus'        => $tahunLulus,
            'bundles'            => $bundles,
            'periodes'           => $periodes,
            'tipe_jawaban'       => ketarunaan_tracer_tipe_jawaban_catalog(),
            'status_transisi'    => ketarunaan_tracer_status_transisi_catalog(),
            'kesesuaian_bidang'  => ketarunaan_tracer_kesesuaian_bidang_catalog(),
            'kesesuaian_jenjang' => ketarunaan_tracer_kesesuaian_jenjang_catalog(),
            'sektor_industri'    => ketarunaan_tracer_sektor_catalog(),
            'bagian_presets'     => [
                'Pencarian & Transisi Kerja',
                'Evaluasi Pembelajaran & Kompetensi',
                'Layanan Karier & Pembinaan',
                'Masukan & Saran',
            ],
        ];
    }

    /**
     * Daftar seluruh bundel pertanyaan master beserta jumlah pertanyaan dan pemakaian.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listBundles(): array
    {
        return $this->db->table('tracer_study_bundle b')
            ->select("b.*,
                      (SELECT COUNT(*) FROM tracer_study_bundle_pertanyaan bp WHERE bp.bundle_id = b.id AND bp.is_aktif = 1) AS jumlah_pertanyaan,
                      (SELECT COUNT(*) FROM tracer_study_periode p WHERE p.sumber_bundle_id = b.id) AS jumlah_periode_pakai", false)
            ->orderBy('b.is_aktif', 'DESC')
            ->orderBy('b.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil rincian 1 bundel pertanyaan master beserta daftar pertanyaannya.
     *
     * @param int $bundleId
     * @return array<string, mixed>|null
     */
    public function getBundleDetail(int $bundleId): ?array
    {
        $bundle = $this->bundleModel->find($bundleId);
        if (!$bundle) {
            return null;
        }

        $pertanyaan = $this->bundlePertanyaanModel
            ->where('bundle_id', $bundleId)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($pertanyaan as $i => $q) {
            $pertanyaan[$i]['opsi_parsed'] = $this->decodeOpsiJawaban($q['opsi_jawaban_json'] ?? null);
        }

        return [
            'bundle'     => $bundle,
            'pertanyaan' => $pertanyaan,
        ];
    }

    /**
     * Menyimpan (tambah/ubah) bundel pertanyaan master.
     *
     * @param array<string, mixed> $input
     * @param string               $actor
     * @param int|null             $bundleId
     * @return array<string, mixed>
     */
    public function saveBundle(array $input, string $actor, ?int $bundleId = null): array
    {
        $nama = trim((string) ($input['nama_bundle'] ?? ''));
        if ($nama === '') {
            return ['status' => false, 'message' => 'Nama bundel pertanyaan wajib diisi.'];
        }

        $kode = strtoupper(trim((string) ($input['kode_bundle'] ?? '')));
        if ($kode === '') {
            $kode = 'BND-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }
        $kode = preg_replace('/[^A-Z0-9\-_]/', '', $kode) ?? $kode;

        $dup = $this->bundleModel->where('kode_bundle', $kode);
        if ($bundleId !== null && $bundleId > 0) {
            $dup->where('id !=', $bundleId);
        }
        if ($dup->first()) {
            return ['status' => false, 'message' => 'Kode bundel sudah digunakan. Gunakan kode lain.'];
        }

        $target = strtolower(trim((string) ($input['target_responden'] ?? 'alumni')));
        if (!in_array($target, ['alumni', 'mitra'], true)) {
            $target = 'alumni';
        }

        $payload = [
            'kode_bundle'      => $kode,
            'nama_bundle'      => $nama,
            'target_responden' => $target,
            'deskripsi'        => trim((string) ($input['deskripsi'] ?? '')),
            'is_aktif'         => isset($input['is_aktif']) ? ((int) $input['is_aktif'] === 1 ? 1 : 0) : 1,
            'diperbarui_oleh'  => $actor,
        ];

        try {
            if ($bundleId !== null && $bundleId > 0) {
                $this->bundleModel->update($bundleId, $payload);
                return ['status' => true, 'id' => $bundleId, 'message' => 'Bundel pertanyaan berhasil diperbarui.'];
            }

            $payload['dibuat_oleh'] = $actor;
            $newId = (int) $this->bundleModel->insert($payload, true);
            return ['status' => true, 'id' => $newId, 'message' => 'Bundel pertanyaan berhasil dibuat.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal menyimpan bundel pertanyaan. Silakan coba lagi.'];
        }
    }

    /**
     * Menduplikasi 1 bundel pertanyaan beserta seluruh item pertanyaannya.
     *
     * @param int    $sourceBundleId
     * @param string $actor
     * @return array<string, mixed>
     */
    public function duplicateBundle(int $sourceBundleId, string $actor): array
    {
        $detail = $this->getBundleDetail($sourceBundleId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Bundel sumber tidak ditemukan.'];
        }

        $src     = $detail['bundle'];
        $newKode = substr((string) $src['kode_bundle'], 0, 28) . '-CP' . date('His');
        $newNama = trim((string) $src['nama_bundle']) . ' (Salinan)';

        $this->db->transStart();
        try {
            $newId = (int) $this->bundleModel->insert([
                'kode_bundle'      => $newKode,
                'nama_bundle'      => $newNama,
                'target_responden' => $src['target_responden'] ?? 'alumni',
                'deskripsi'        => $src['deskripsi'] ?? '',
                'is_aktif'         => 1,
                'dibuat_oleh'      => $actor,
                'diperbarui_oleh'  => $actor,
            ], true);

            foreach ($detail['pertanyaan'] as $q) {
                $this->bundlePertanyaanModel->insert([
                    'bundle_id'          => $newId,
                    'kode_pertanyaan'    => $q['kode_pertanyaan'],
                    'bagian'             => $q['bagian'],
                    'urutan'             => (int) $q['urutan'],
                    'teks_pertanyaan'    => $q['teks_pertanyaan'],
                    'keterangan_bantuan' => $q['keterangan_bantuan'],
                    'tipe_jawaban'       => $q['tipe_jawaban'],
                    'opsi_jawaban_json'  => $q['opsi_jawaban_json'],
                    'is_wajib'           => (int) $q['is_wajib'],
                    'is_aktif'           => (int) $q['is_aktif'],
                ]);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menduplikasi bundel pertanyaan.'];
            }

            return ['status' => true, 'id' => $newId, 'message' => 'Bundel pertanyaan berhasil diduplikasi dan siap dimodifikasi.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal menduplikasi bundel pertanyaan.'];
        }
    }

    /**
     * Menyimpan (tambah/ubah) 1 pertanyaan di dalam bundel master.
     *
     * @param int                  $bundleId
     * @param array<string, mixed> $input
     * @param int|null             $questionId
     * @return array<string, mixed>
     */
    public function saveBundleQuestion(int $bundleId, array $input, ?int $questionId = null): array
    {
        $bundle = $this->bundleModel->find($bundleId);
        if (!$bundle) {
            return ['status' => false, 'message' => 'Bundel pertanyaan tidak ditemukan.'];
        }

        $parsed = $this->normalizeQuestionInput($input, $bundleId, 'bundle', $questionId);
        if (!$parsed['status']) {
            return $parsed;
        }

        $payload = $parsed['payload'];
        $payload['bundle_id'] = $bundleId;

        try {
            if ($questionId !== null && $questionId > 0) {
                $existing = $this->bundlePertanyaanModel->find($questionId);
                if (!$existing || (int) $existing['bundle_id'] !== $bundleId) {
                    return ['status' => false, 'message' => 'Pertanyaan tidak ditemukan pada bundel ini.'];
                }
                $this->bundlePertanyaanModel->update($questionId, $payload);
                return ['status' => true, 'id' => $questionId, 'message' => 'Pertanyaan bundel berhasil diperbarui.'];
            }

            $newId = (int) $this->bundlePertanyaanModel->insert($payload, true);
            return ['status' => true, 'id' => $newId, 'message' => 'Pertanyaan berhasil ditambahkan ke bundel.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal menyimpan pertanyaan bundel.'];
        }
    }

    /**
     * Menghapus 1 pertanyaan dari bundel master.
     */
    public function deleteBundleQuestion(int $bundleId, int $questionId): array
    {
        $existing = $this->bundlePertanyaanModel->find($questionId);
        if (!$existing || (int) $existing['bundle_id'] !== $bundleId) {
            return ['status' => false, 'message' => 'Pertanyaan tidak ditemukan pada bundel ini.'];
        }

        $this->bundlePertanyaanModel->delete($questionId);
        return ['status' => true, 'message' => 'Pertanyaan berhasil dihapus dari bundel.'];
    }

    /**
     * Daftar seluruh periode pembukaan Tracer Study lengkap dengan progres partisipasi.
     *
     * @param string|null $alumniNomor Jika diisi, menghitung kelayakan dan status pengisian alumni tersebut.
     * @return array<int, array<string, mixed>>
     */
    public function listPeriodes(?string $alumniNomor = null): array
    {
        $rows = $this->db->table('tracer_study_periode p')
            ->select("p.*, b.nama_bundle AS sumber_bundle_nama,
                      (SELECT COUNT(*) FROM tracer_study_periode_pertanyaan pp WHERE pp.periode_id = p.id AND pp.is_aktif = 1) AS jumlah_pertanyaan,
                      (SELECT COUNT(*) FROM tracer_study_respons r WHERE r.periode_id = p.id) AS jumlah_respons", false)
            ->join('tracer_study_bundle b', 'b.id = p.sumber_bundle_id', 'left')
            ->orderBy("FIELD(p.status_periode, 'dibuka', 'draf', 'ditutup')", '', false)
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();

        $alumniRow = null;
        $myRespMap = [];
        if ($alumniNomor !== null && trim($alumniNomor) !== '') {
            $alumniRow = $this->getAlumniBasicProfile($alumniNomor);
            $myResps   = $this->responsModel->where('nomor_taruna', trim($alumniNomor))->findAll();
            foreach ($myResps as $mr) {
                $myRespMap[(int) $mr['periode_id']] = $mr;
            }
        }

        foreach ($rows as $i => $p) {
            $pid        = (int) $p['id'];
            $sasaranCnt = $this->countTargetAlumniForPeriode($p);
            $respCnt    = (int) ($p['jumlah_respons'] ?? 0);
            $rate       = $sasaranCnt > 0 ? round(($respCnt / $sasaranCnt) * 100, 1) : 0.0;

            $rows[$i]['tahun_sasaran']  = $this->decodeIntList($p['tahun_lulus_sasaran_json'] ?? null);
            $rows[$i]['prodi_sasaran']  = $this->decodeIntList($p['prodi_sasaran_json'] ?? null);
            $rows[$i]['sasaran_total']  = $sasaranCnt;
            $rows[$i]['response_rate']  = $rate;
            $rows[$i]['is_open_now']    = ketarunaan_is_tracer_periode_open($p);
            $rows[$i]['can_edit_q']     = (($p['status_periode'] ?? '') === 'draf');
            $rows[$i]['can_revert']     = (($p['status_periode'] ?? '') !== 'draf' && $respCnt === 0);
            $rows[$i]['my_respons']     = $myRespMap[$pid] ?? null;
            $rows[$i]['is_eligible']    = $alumniRow !== null ? $this->isAlumniEligibleForPeriode($alumniRow, $p) : false;
        }

        return $rows;
    }

    /**
     * Rincian 1 periode beserta snapshot pertanyaan dan jumlah responsnya.
     *
     * @param int $periodeId
     * @return array<string, mixed>|null
     */
    public function getPeriodeDetail(int $periodeId): ?array
    {
        $periode = $this->db->table('tracer_study_periode p')
            ->select('p.*, b.nama_bundle AS sumber_bundle_nama')
            ->join('tracer_study_bundle b', 'b.id = p.sumber_bundle_id', 'left')
            ->where('p.id', $periodeId)
            ->get()
            ->getRowArray();

        if (!$periode) {
            return null;
        }

        $pertanyaan = $this->periodePertanyaanModel
            ->where('periode_id', $periodeId)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        foreach ($pertanyaan as $i => $q) {
            $pertanyaan[$i]['opsi_parsed'] = $this->decodeOpsiJawaban($q['opsi_jawaban_json'] ?? null);
        }

        $jumlahRespons = (int) $this->responsModel->where('periode_id', $periodeId)->countAllResults();
        $periode['tahun_sasaran'] = $this->decodeIntList($periode['tahun_lulus_sasaran_json'] ?? null);
        $periode['prodi_sasaran'] = $this->decodeIntList($periode['prodi_sasaran_json'] ?? null);
        $periode['is_open_now']   = ketarunaan_is_tracer_periode_open($periode);

        return [
            'periode'            => $periode,
            'pertanyaan'         => $pertanyaan,
            'jumlah_respons'     => $jumlahRespons,
            'can_edit_questions' => (($periode['status_periode'] ?? '') === 'draf'),
            'can_revert_draft'   => (($periode['status_periode'] ?? '') !== 'draf' && $jumlahRespons === 0),
        ];
    }

    /**
     * Membuat atau memperbarui periode pembukaan Tracer Study.
     * Saat membuat baru, otomatis menyalin pertanyaan dari bundel master atau periode lain (snapshot independen).
     *
     * @param array<string, mixed> $input
     * @param string               $actor
     * @param int|null             $periodeId
     * @return array<string, mixed>
     */
    public function savePeriode(array $input, string $actor, ?int $periodeId = null): array
    {
        $nama = trim((string) ($input['nama_periode'] ?? ''));
        if ($nama === '') {
            return ['status' => false, 'message' => 'Nama periode survei wajib diisi.'];
        }

        $kode = strtoupper(trim((string) ($input['kode_periode'] ?? '')));
        if ($kode === '') {
            $kode = 'TS-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        }
        $kode = preg_replace('/[^A-Z0-9\-_]/', '', $kode) ?? $kode;

        $dup = $this->periodeModel->where('kode_periode', $kode);
        if ($periodeId !== null && $periodeId > 0) {
            $dup->where('id !=', $periodeId);
        }
        if ($dup->first()) {
            return ['status' => false, 'message' => 'Kode periode sudah digunakan. Gunakan kode lain.'];
        }

        $tglMulai   = trim((string) ($input['tanggal_mulai'] ?? ''));
        $tglSelesai = trim((string) ($input['tanggal_selesai'] ?? ''));
        if ($tglMulai !== '' && $tglSelesai !== '' && $tglMulai > $tglSelesai) {
            return ['status' => false, 'message' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.'];
        }

        $targetPersenRaw = str_replace(',', '.', trim((string) ($input['target_partisipasi_persen'] ?? '80')));
        $targetPersen    = max(1, min(100, (float) $targetPersenRaw));

        $tahunList = $this->sanitizeIntArray($input['tahun_lulus_sasaran'] ?? []);
        $prodiList = $this->sanitizeIntArray($input['prodi_sasaran'] ?? []);

        $slugCheck = $this->normalizePublicSlug($input['public_token'] ?? null, $periodeId);
        if (!$slugCheck['status']) {
            return $slugCheck;
        }
        $publicSlug = (string) $slugCheck['slug'];

        $allowUnreg = isset($input['allow_unregistered_alumni']) ? ((int) $input['allow_unregistered_alumni'] === 1 ? 1 : 0) : 1;

        if ($periodeId !== null && $periodeId > 0) {
            $existing = $this->periodeModel->find($periodeId);
            if (!$existing) {
                return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
            }

            $update = [
                'kode_periode'              => $kode,
                'public_token'              => $publicSlug,
                'allow_unregistered_alumni' => $allowUnreg,
                'nama_periode'              => $nama,
                'tanggal_mulai'             => $tglMulai !== '' ? $tglMulai : null,
                'tanggal_selesai'           => $tglSelesai !== '' ? $tglSelesai : null,
                'target_partisipasi_persen' => $targetPersen,
                'deskripsi'                 => trim((string) ($input['deskripsi'] ?? '')),
            ];
            if (($existing['status_periode'] ?? '') === 'draf') {
                $update['tahun_lulus_sasaran_json'] = !empty($tahunList) ? json_encode($tahunList) : null;
                $update['prodi_sasaran_json']       = !empty($prodiList) ? json_encode($prodiList) : null;
            }

            $this->periodeModel->update($periodeId, $update);
            return ['status' => true, 'id' => $periodeId, 'message' => 'Periode survei berhasil diperbarui.'];
        }

        // Pembuatan periode baru (selalu berstatus draf) + salin pertanyaan dari bundel / periode lain
        $sumberTipe      = strtolower(trim((string) ($input['sumber_pertanyaan'] ?? 'bundle')));
        $sumberBundleId  = (int) ($input['sumber_bundle_id'] ?? 0);
        $sumberPeriodeId = (int) ($input['sumber_periode_id'] ?? 0);

        $sourceQuestions = [];
        if ($sumberTipe === 'bundle' && $sumberBundleId > 0) {
            $sourceQuestions = $this->bundlePertanyaanModel
                ->where('bundle_id', $sumberBundleId)
                ->where('is_aktif', 1)
                ->orderBy('urutan', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
            $sumberPeriodeId = 0;
        } elseif ($sumberTipe === 'periode' && $sumberPeriodeId > 0) {
            $sourceQuestions = $this->periodePertanyaanModel
                ->where('periode_id', $sumberPeriodeId)
                ->where('is_aktif', 1)
                ->orderBy('urutan', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
            $sumberBundleId = 0;
        } else {
            $sumberBundleId  = 0;
            $sumberPeriodeId = 0;
        }

        $this->db->transStart();
        try {
            $newId = (int) $this->periodeModel->insert([
                'kode_periode'              => $kode,
                'public_token'              => $publicSlug,
                'allow_public_link'         => 1,
                'allow_unregistered_alumni' => $allowUnreg,
                'nama_periode'              => $nama,
                'sumber_bundle_id'          => $sumberBundleId > 0 ? $sumberBundleId : null,
                'sumber_periode_id'         => $sumberPeriodeId > 0 ? $sumberPeriodeId : null,
                'target_responden'          => 'alumni',
                'tahun_lulus_sasaran_json'  => !empty($tahunList) ? json_encode($tahunList) : null,
                'prodi_sasaran_json'        => !empty($prodiList) ? json_encode($prodiList) : null,
                'tanggal_mulai'             => $tglMulai !== '' ? $tglMulai : null,
                'tanggal_selesai'           => $tglSelesai !== '' ? $tglSelesai : null,
                'target_partisipasi_persen' => $targetPersen,
                'status_periode'            => 'draf',
                'deskripsi'                 => trim((string) ($input['deskripsi'] ?? '')),
                'dibuat_oleh'               => $actor,
            ], true);

            foreach ($sourceQuestions as $idx => $sq) {
                $this->periodePertanyaanModel->insert([
                    'periode_id'                  => $newId,
                    'sumber_bundle_pertanyaan_id' => $sumberBundleId > 0 ? (int) $sq['id'] : ($sq['sumber_bundle_pertanyaan_id'] ?? null),
                    'kode_pertanyaan'             => $sq['kode_pertanyaan'],
                    'bagian'                      => $sq['bagian'],
                    'urutan'                      => (int) ($sq['urutan'] ?: ($idx + 1)),
                    'teks_pertanyaan'             => $sq['teks_pertanyaan'],
                    'keterangan_bantuan'          => $sq['keterangan_bantuan'],
                    'tipe_jawaban'                => $sq['tipe_jawaban'],
                    'opsi_jawaban_json'           => $sq['opsi_jawaban_json'],
                    'is_wajib'                    => (int) $sq['is_wajib'],
                    'is_aktif'                    => 1,
                ]);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal membuat periode survei baru.'];
            }

            $msg = !empty($sourceQuestions)
                ? 'Periode draf dibuat dengan ' . count($sourceQuestions) . ' pertanyaan salinan. Anda dapat memodifikasi pertanyaan sebelum membuka survei.'
                : 'Periode draf berhasil dibuat. Tambahkan pertanyaan terlebih dahulu sebelum membuka survei.';

            return ['status' => true, 'id' => $newId, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal membuat periode survei. Silakan coba lagi.'];
        }
    }

    /**
     * Menambah atau mengubah 1 pertanyaan pada periode tertentu (HANYA saat status = draf).
     *
     * @param int                  $periodeId
     * @param array<string, mixed> $input
     * @param int|null             $questionId
     * @return array<string, mixed>
     */
    public function savePeriodeQuestion(int $periodeId, array $input, ?int $questionId = null): array
    {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        if (($periode['status_periode'] ?? '') !== 'draf') {
            return ['status' => false, 'message' => 'Pertanyaan hanya dapat diubah sebelum survei dibuka (status Draf).'];
        }

        $parsed = $this->normalizeQuestionInput($input, $periodeId, 'periode', $questionId);
        if (!$parsed['status']) {
            return $parsed;
        }

        $payload = $parsed['payload'];
        $payload['periode_id'] = $periodeId;

        try {
            if ($questionId !== null && $questionId > 0) {
                $existing = $this->periodePertanyaanModel->find($questionId);
                if (!$existing || (int) $existing['periode_id'] !== $periodeId) {
                    return ['status' => false, 'message' => 'Pertanyaan tidak ditemukan pada periode ini.'];
                }
                $this->periodePertanyaanModel->update($questionId, $payload);
                return ['status' => true, 'id' => $questionId, 'message' => 'Pertanyaan periode berhasil diperbarui.'];
            }

            $newId = (int) $this->periodePertanyaanModel->insert($payload, true);
            return ['status' => true, 'id' => $newId, 'message' => 'Pertanyaan berhasil ditambahkan ke periode.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal menyimpan pertanyaan periode.'];
        }
    }

    /**
     * Menghapus 1 pertanyaan dari periode tertentu (HANYA saat status = draf).
     */
    public function deletePeriodeQuestion(int $periodeId, int $questionId): array
    {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        if (($periode['status_periode'] ?? '') !== 'draf') {
            return ['status' => false, 'message' => 'Pertanyaan tidak dapat dihapus setelah survei dibuka.'];
        }

        $existing = $this->periodePertanyaanModel->find($questionId);
        if (!$existing || (int) $existing['periode_id'] !== $periodeId) {
            return ['status' => false, 'message' => 'Pertanyaan tidak ditemukan pada periode ini.'];
        }

        $this->periodePertanyaanModel->delete($questionId);
        return ['status' => true, 'message' => 'Pertanyaan berhasil dihapus dari periode.'];
    }

    /**
     * Menyimpan susunan pertanyaan 1 periode menjadi Bundel Pertanyaan Master baru agar bisa dipakai ulang.
     */
    public function savePeriodeAsNewBundle(int $periodeId, string $namaBundle, string $actor): array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        if (empty($detail['pertanyaan'])) {
            return ['status' => false, 'message' => 'Periode belum memiliki pertanyaan untuk disimpan menjadi bundel.'];
        }

        $namaClean = trim($namaBundle);
        if ($namaClean === '') {
            $namaClean = 'Bundel dari ' . trim((string) $detail['periode']['nama_periode']);
        }
        $kodeBundle = 'BND-' . date('Ymd-His');

        $this->db->transStart();
        try {
            $newBundleId = (int) $this->bundleModel->insert([
                'kode_bundle'      => $kodeBundle,
                'nama_bundle'      => $namaClean,
                'target_responden' => $detail['periode']['target_responden'] ?? 'alumni',
                'deskripsi'        => 'Disimpan dari periode ' . (string) $detail['periode']['nama_periode'],
                'is_aktif'         => 1,
                'dibuat_oleh'      => $actor,
                'diperbarui_oleh'  => $actor,
            ], true);

            foreach ($detail['pertanyaan'] as $q) {
                $this->bundlePertanyaanModel->insert([
                    'bundle_id'          => $newBundleId,
                    'kode_pertanyaan'    => $q['kode_pertanyaan'],
                    'bagian'             => $q['bagian'],
                    'urutan'             => (int) $q['urutan'],
                    'teks_pertanyaan'    => $q['teks_pertanyaan'],
                    'keterangan_bantuan' => $q['keterangan_bantuan'],
                    'tipe_jawaban'       => $q['tipe_jawaban'],
                    'opsi_jawaban_json'  => $q['opsi_jawaban_json'],
                    'is_wajib'           => (int) $q['is_wajib'],
                    'is_aktif'           => (int) $q['is_aktif'],
                ]);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan bundel baru.'];
            }

            return ['status' => true, 'id' => $newBundleId, 'message' => 'Susunan pertanyaan berhasil disimpan sebagai bundel baru.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal menyimpan bundel baru.'];
        }
    }

    /**
     * Mengubah status periode survei (draf <-> dibuka <-> ditutup) dengan aturan pengaman integritas data.
     *
     * @param int    $periodeId
     * @param string $targetStatus draf|dibuka|ditutup
     * @param string $actor
     * @return array<string, mixed>
     */
    public function changePeriodeStatus(int $periodeId, string $targetStatus, string $actor): array
    {
        $target = strtolower(trim($targetStatus));
        if (!in_array($target, ['draf', 'dibuka', 'ditutup'], true)) {
            return ['status' => false, 'message' => 'Status tujuan tidak valid.'];
        }

        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }

        $current = (string) ($periode['status_periode'] ?? 'draf');
        if ($current === $target) {
            return ['status' => true, 'message' => 'Status periode tidak berubah.'];
        }

        $now = date('Y-m-d H:i:s');

        if ($target === 'dibuka') {
            $qCount = (int) $this->periodePertanyaanModel
                ->where('periode_id', $periodeId)
                ->where('is_aktif', 1)
                ->countAllResults();
            if ($qCount < 1) {
                return ['status' => false, 'message' => 'Tambahkan minimal 1 pertanyaan aktif sebelum membuka survei.'];
            }

            $this->periodeModel->update($periodeId, [
                'status_periode' => 'dibuka',
                'dibuka_oleh'    => $actor,
                'dibuka_pada'    => $now,
            ]);

            return ['status' => true, 'message' => 'Survei resmi dibuka. Daftar pertanyaan dikunci dan alumni dapat mulai mengisi.'];
        }

        if ($target === 'ditutup') {
            if ($current !== 'dibuka') {
                return ['status' => false, 'message' => 'Hanya periode yang sedang dibuka yang dapat ditutup.'];
            }

            $this->periodeModel->update($periodeId, [
                'status_periode' => 'ditutup',
                'ditutup_oleh'   => $actor,
                'ditutup_pada'   => $now,
            ]);

            return ['status' => true, 'message' => 'Survei resmi ditutup. Pengisian responden telah dihentikan.'];
        }

        // target === 'draf' (tarik kembali ke draf untuk revisi pertanyaan)
        $respCount = (int) $this->responsModel->where('periode_id', $periodeId)->countAllResults();
        if ($respCount > 0) {
            return ['status' => false, 'message' => 'Periode sudah memiliki ' . $respCount . ' respons sehingga tidak dapat dikembalikan ke draf.'];
        }

        $this->periodeModel->update($periodeId, [
            'status_periode' => 'draf',
        ]);

        return ['status' => true, 'message' => 'Periode dikembalikan ke Draf. Anda dapat mengubah pertanyaan kembali.'];
    }

    /**
     * Mengambil periode survei berdasarkan slug/token tautan publik.
     *
     * @param string $token
     * @return array<string, mixed>|null
     */
    public function getPeriodeByPublicToken(string $token): ?array
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '', trim($token)) ?? '');
        if (strlen($clean) < 6) {
            return null;
        }

        $row = $this->periodeModel->where('public_token', $clean)->first();
        if (!$row || (int) ($row['allow_public_link'] ?? 1) !== 1) {
            return null;
        }

        return $this->getPeriodeDetail((int) $row['id']);
    }

    /**
     * Membuat ulang slug tautan publik singkat agar tautan lama otomatis tidak berlaku.
     *
     * @param int $periodeId
     * @return array<string, mixed>
     */
    public function regeneratePublicToken(int $periodeId): array
    {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }

        $newSlug = 'tracerstudy' . date('Y') . '-' . strtolower(substr(bin2hex(random_bytes(2)), 0, 4));
        $this->periodeModel->update($periodeId, [
            'public_token'      => $newSlug,
            'allow_public_link' => 1,
        ]);

        return ['status' => true, 'token' => $newSlug, 'message' => 'Tautan singkat baru berhasil dibuat: /' . $newSlug];
    }

    /**
     * Validasi & normalisasi slug tautan singkat (contoh: tracerstudy2026).
     * Wajib diawali "tracer" agar rapi dibaca dan bebas bentrok dengan rute sistem.
     *
     * @param string|null $rawSlug
     * @param int|null    $excludePeriodeId
     * @return array<string, mixed>
     */
    private function normalizePublicSlug(?string $rawSlug, ?int $excludePeriodeId = null): array
    {
        $raw = strtolower(trim((string) ($rawSlug ?? '')));
        $raw = ltrim($raw, '/');

        if ($raw === '') {
            // Generate otomatis slug singkat yang rapi, mis. tracerstudy2026 atau tracerstudy2026-2
            $base = 'tracerstudy' . date('Y');
            $candidate = $base;
            $suffix = 2;
            while (true) {
                $q = $this->periodeModel->where('public_token', $candidate);
                if ($excludePeriodeId !== null && $excludePeriodeId > 0) {
                    $q->where('id !=', $excludePeriodeId);
                }
                if (!$q->first()) {
                    return ['status' => true, 'slug' => $candidate];
                }
                $candidate = $base . '-' . $suffix;
                $suffix++;
            }
        }

        $clean = preg_replace('/[^a-z0-9\-]/', '', $raw) ?? '';
        $clean = trim(preg_replace('/-+/', '-', $clean) ?? '', '-');

        if ($clean === '') {
            return ['status' => false, 'message' => 'Format tautan singkat tidak valid. Gunakan huruf kecil, angka, atau tanda hubung (contoh: tracerstudy2026).'];
        }

        // Pastikan diawali kata "tracer" agar selalu cocok dengan rute singkat bebas bentrok
        if (strpos($clean, 'tracer') !== 0) {
            $clean = 'tracer' . $clean;
        }

        if (strlen($clean) < 8 || strlen($clean) > 50) {
            return ['status' => false, 'message' => 'Panjang tautan singkat antara 8 sampai 50 karakter (contoh: tracerstudy2026).'];
        }

        $dup = $this->periodeModel->where('public_token', $clean);
        if ($excludePeriodeId !== null && $excludePeriodeId > 0) {
            $dup->where('id !=', $excludePeriodeId);
        }
        if ($dup->first()) {
            return ['status' => false, 'message' => 'Tautan singkat "/' . $clean . '" sudah dipakai periode lain. Gunakan nama lain.'];
        }

        return ['status' => true, 'slug' => $clean];
    }

    /**
     * Memverifikasi identitas alumni pada pengisian tautan publik (tanpa login)
     * menggunakan Nomor Taruna + Tanggal Lahir (atau Tahun Lulus jika tanggal lahir kosong di data lama).
     *
     * @param int    $periodeId
     * @param string $nomorTaruna
     * @param string $tanggalLahir Format Y-m-d
     * @param int    $tahunLulusFallback
     * @return array<string, mixed>
     */
    public function verifyPublicAlumniIdentity(int $periodeId, string $nomorTaruna, string $tanggalLahir, int $tahunLulusFallback = 0): array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        $periode = $detail['periode'];
        if (!ketarunaan_is_tracer_periode_open($periode)) {
            return ['status' => false, 'message' => 'Periode survei ini telah ditutup.'];
        }

        $nimRaw   = trim($nomorTaruna);
        $nimClean = preg_replace('/[\s\.]+/', '', $nimRaw) ?? $nimRaw;
        $dobClean = substr(trim($tanggalLahir), 0, 10);
        if ($nimClean === '' || !preg_match('/^[A-Za-z0-9\-]{6,20}$/', $nimClean)) {
            return ['status' => false, 'message' => 'Nomor Taruna (Notar) harus terdiri dari 6 sampai 20 karakter angka/huruf (contoh: 26012100).'];
        }
        if ($dobClean === '' && $tahunLulusFallback <= 0) {
            return ['status' => false, 'message' => 'Nomor Taruna (Notar) dan tanggal lahir wajib diisi.'];
        }

        $nimCandidates = array_values(array_unique(array_filter([$nimRaw, $nimClean], static fn (string $v): bool => $v !== '')));

        // 1. Cek apakah sudah terdaftar sebagai alumni aktif
        $alumni = $this->db->table('alumni_profil ap')
            ->select('ap.nomor_taruna, ap.tahun_lulus, ap.status_profil, mt.nama_lengkap, mt.tanggal_lahir, pt.program_studi_id, ps.kode_prodi, ps.nama_prodi')
            ->join('master_taruna mt', 'mt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->whereIn('ap.nomor_taruna', $nimCandidates)
            ->where('ap.status_profil', 'aktif')
            ->get()
            ->getRowArray();

        $genericError = 'Data tidak cocok atau Anda tidak termasuk sasaran periode survei ini.';
        if ($alumni) {
            $dbDob = trim((string) ($alumni['tanggal_lahir'] ?? ''));
            if ($dbDob !== '' && $dbDob !== '0000-00-00') {
                if ($dobClean === '' || substr($dbDob, 0, 10) !== $dobClean) {
                    return ['status' => false, 'message' => $genericError];
                }
            } else {
                if ($tahunLulusFallback <= 0 || (int) ($alumni['tahun_lulus'] ?? 0) !== $tahunLulusFallback) {
                    return ['status' => false, 'message' => $genericError];
                }
            }

            if (!$this->isAlumniEligibleForPeriode($alumni, $periode)) {
                return ['status' => false, 'message' => $genericError];
            }

            $alreadyResp = $this->responsModel
                ->where('periode_id', $periodeId)
                ->where('nomor_taruna', (string) $alumni['nomor_taruna'])
                ->where('status_validasi !=', 'ditolak')
                ->first();
            if ($alreadyResp) {
                return [
                    'status'            => false,
                    'already_submitted' => true,
                    'message'           => 'Anda sudah mengisi survei pada periode ini. Setiap alumni hanya dapat mengirim jawaban satu kali.',
                ];
            }

            return [
                'status' => true,
                'mode'   => 'registered',
                'alumni' => [
                    'is_alumni_terdaftar' => 1,
                    'nomor_taruna'        => (string) $alumni['nomor_taruna'],
                    'nama_lengkap'        => (string) ($alumni['nama_lengkap'] ?: $alumni['nomor_taruna']),
                    'program_studi_id'    => (int) ($alumni['program_studi_id'] ?? 0),
                    'kode_prodi'          => (string) ($alumni['kode_prodi'] ?: '-'),
                    'tahun_lulus'         => (int) ($alumni['tahun_lulus'] ?? 0),
                    'tanggal_lahir'       => substr((string) ($alumni['tanggal_lahir'] ?? $dobClean), 0, 10),
                ],
            ];
        }

        // 2. Tolak jika Notar milik taruna yang masih aktif / belum lulus di master_taruna
        $activeTaruna = $this->db->table('master_taruna')
            ->select('nomor_taruna, status_terkini')
            ->whereIn('nomor_taruna', $nimCandidates)
            ->get()
            ->getRowArray();

        if ($activeTaruna && strtolower(trim((string) ($activeTaruna['status_terkini'] ?? ''))) !== 'lulus') {
            return ['status' => false, 'message' => 'Nomor Taruna tersebut masih tercatat sebagai taruna aktif, bukan alumni.'];
        }

        // 3. Jika Notar belum ada di database & periode mengizinkan alumni belum terdaftar -> arahkan ke lengkapi identitas
        if ((int) ($periode['allow_unregistered_alumni'] ?? 1) !== 1) {
            return ['status' => false, 'message' => $genericError];
        }

        $alreadyUnreg = $this->responsModel
            ->where('periode_id', $periodeId)
            ->whereIn('nomor_taruna', $nimCandidates)
            ->where('status_validasi !=', 'ditolak')
            ->first();
        if ($alreadyUnreg) {
            return [
                'status'            => false,
                'already_submitted' => true,
                'message'           => 'Anda sudah mengisi survei pada periode ini. Setiap alumni hanya dapat mengirim jawaban satu kali.',
            ];
        }

        return [
            'status'        => true,
            'mode'          => 'needs_registration',
            'nomor_taruna'  => $nimClean,
            'tanggal_lahir' => $dobClean,
            'tahun_lulus'   => $tahunLulusFallback,
        ];
    }

    /**
     * Memvalidasi identitas alumni baru (belum ada di database) pada tautan publik sebelum membuka kuesioner.
     *
     * @param int                  $periodeId
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function registerPublicUnregisteredIdentity(int $periodeId, array $input): array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        $periode = $detail['periode'];
        if (!ketarunaan_is_tracer_periode_open($periode)) {
            return ['status' => false, 'message' => 'Periode survei ini telah ditutup.'];
        }
        if ((int) ($periode['allow_unregistered_alumni'] ?? 1) !== 1) {
            return ['status' => false, 'message' => 'Periode ini hanya menerima alumni yang sudah terdaftar di sistem.'];
        }

        $nimRaw   = trim((string) ($input['nomor_taruna'] ?? ''));
        $nimClean = preg_replace('/[\s\.]+/', '', $nimRaw) ?? $nimRaw;
        $nama     = trim((string) ($input['nama_lengkap'] ?? ''));
        $prodiId  = (int) ($input['program_studi_id'] ?? 0);
        $tahun    = (int) ($input['tahun_lulus'] ?? 0);
        $dob      = substr(trim((string) ($input['tanggal_lahir'] ?? '')), 0, 10);
        $noHp     = trim((string) ($input['no_hp'] ?? ''));
        $email    = trim((string) ($input['email'] ?? ''));

        if ($nimClean === '' || !preg_match('/^[A-Za-z0-9\-]{6,20}$/', $nimClean)) {
            return ['status' => false, 'message' => 'Nomor Taruna (Notar) harus terdiri dari 6 sampai 20 karakter (contoh: 26012100).'];
        }
        if ($nama === '' || mb_strlen($nama) < 3) {
            return ['status' => false, 'message' => 'Nama lengkap wajib diisi.'];
        }
        if ($prodiId <= 0) {
            return ['status' => false, 'message' => 'Program studi wajib dipilih.'];
        }
        $maxYear = (int) date('Y');
        if ($tahun < 1970 || $tahun > $maxYear) {
            return ['status' => false, 'message' => 'Tahun lulus tidak valid.'];
        }
        if ($dob === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return ['status' => false, 'message' => 'Tanggal lahir wajib diisi dengan format YYYY-MM-DD.'];
        }
        if ($noHp === '') {
            return ['status' => false, 'message' => 'Nomor HP / WhatsApp aktif wajib diisi.'];
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Format alamat email tidak sesuai.'];
        }

        // Pastikan bukan taruna aktif dan bukan alumni terdaftar yang seharusnya pakai jalur verifikasi
        $existingAlumni = $this->getAlumniBasicProfile($nimClean);
        if ($existingAlumni) {
            return ['status' => false, 'message' => 'Nomor Taruna ini sudah terdaftar di sistem. Gunakan menu Verifikasi Identitas.'];
        }
        $activeTaruna = $this->db->table('master_taruna')->select('status_terkini')->where('nomor_taruna', $nimClean)->get()->getRowArray();
        if ($activeTaruna && strtolower(trim((string) ($activeTaruna['status_terkini'] ?? ''))) !== 'lulus') {
            return ['status' => false, 'message' => 'Nomor Taruna tersebut masih tercatat sebagai taruna aktif, bukan alumni.'];
        }

        $prodiRow = $this->db->table('ref_program_studi')->select('id, kode_prodi, nama_prodi')->where('id', $prodiId)->get()->getRowArray();
        if (!$prodiRow) {
            return ['status' => false, 'message' => 'Program studi yang dipilih tidak valid.'];
        }

        $pseudoAlumni = [
            'tahun_lulus'      => $tahun,
            'program_studi_id' => $prodiId,
        ];
        if (!$this->isAlumniEligibleForPeriode($pseudoAlumni, $periode)) {
            return ['status' => false, 'message' => 'Tahun lulus atau program studi Anda tidak termasuk sasaran periode survei ini.'];
        }

        $alreadyReg = $this->responsModel
            ->where('periode_id', $periodeId)
            ->where('nomor_taruna', $nimClean)
            ->where('status_validasi !=', 'ditolak')
            ->first();
        if ($alreadyReg) {
            return [
                'status'            => false,
                'already_submitted' => true,
                'message'           => 'Anda sudah mengisi survei pada periode ini. Setiap alumni hanya dapat mengirim jawaban satu kali.',
            ];
        }

        return [
            'status' => true,
            'alumni' => [
                'is_alumni_terdaftar' => 0,
                'nomor_taruna'        => $nimClean,
                'nama_lengkap'        => $nama,
                'program_studi_id'    => $prodiId,
                'kode_prodi'          => (string) ($prodiRow['kode_prodi'] ?: '-'),
                'tahun_lulus'         => $tahun,
                'tanggal_lahir'       => $dob,
                'no_hp'               => $noHp,
                'email'               => $email,
            ],
        ];
    }

    /**
     * Konteks formulir pengisian survei untuk 1 periode & 1 alumni (mandiri alumni atau input PIC).
     *
     * @param int         $periodeId
     * @param string|null $nomorTaruna
     * @param bool        $isSelfAlumni
     * @return array<string, mixed>
     */
    public function getSurveyFormContext(int $periodeId, ?string $nomorTaruna, bool $isSelfAlumni, ?array $unregisteredIdentity = null): array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }

        $periode = $detail['periode'];
        if ($isSelfAlumni && !ketarunaan_is_tracer_periode_open($periode)) {
            return ['status' => false, 'message' => 'Periode survei ini sedang tidak dibuka untuk pengisian.'];
        }

        $alumniRow = null;
        $existing  = null;
        $answers   = [];
        $isUnreg   = ($unregisteredIdentity !== null && (int) ($unregisteredIdentity['is_alumni_terdaftar'] ?? 1) === 0);

        $nimClean = trim((string) ($nomorTaruna ?? ''));
        if ($nimClean !== '') {
            $alumniRow = $this->getAlumniBasicProfile($nimClean);
            if ($isSelfAlumni && !$alumniRow) {
                if ($isUnreg && (int) ($periode['allow_unregistered_alumni'] ?? 1) === 1) {
                    $alumniRow = [
                        'nomor_taruna'     => $nimClean,
                        'nama_lengkap'     => (string) ($unregisteredIdentity['nama_lengkap'] ?? $nimClean),
                        'program_studi_id' => (int) ($unregisteredIdentity['program_studi_id'] ?? 0),
                        'kode_prodi'       => (string) ($unregisteredIdentity['kode_prodi'] ?? '-'),
                        'tahun_lulus'      => (int) ($unregisteredIdentity['tahun_lulus'] ?? 0),
                    ];
                } else {
                    return ['status' => false, 'message' => 'Profil alumni Anda belum terdaftar aktif.'];
                }
            }
            if ($isSelfAlumni && $alumniRow && !$this->isAlumniEligibleForPeriode($alumniRow, $periode)) {
                return ['status' => false, 'message' => 'Periode survei ini tidak ditujukan untuk tahun lulus atau program studi Anda.'];
            }

            $existing = $this->responsModel
                ->where('periode_id', $periodeId)
                ->where('nomor_taruna', $nimClean)
                ->first();

            if ($existing && $isSelfAlumni && ($existing['status_validasi'] ?? '') !== 'ditolak') {
                return [
                    'status'            => false,
                    'already_submitted' => true,
                    'respons_id'        => (int) $existing['id'],
                    'message'           => 'Anda sudah mengisi survei pada periode ini. Jawaban yang telah dikirim bersifat final.',
                ];
            }

            if ($existing) {
                $ansRows = $this->jawabanModel->where('respons_id', (int) $existing['id'])->findAll();
                foreach ($ansRows as $ar) {
                    $answers[(int) $ar['periode_pertanyaan_id']] = [
                        'nilai_teks'  => $ar['nilai_teks'],
                        'nilai_angka' => $ar['nilai_angka'],
                        'nilai_opsi'  => $this->decodeOpsiJawaban($ar['nilai_opsi_json'] ?? null),
                    ];
                }
            }
        }

        // Daftar pilihan alumni untuk mode input PIC
        $alumniOptions = [];
        if (!$isSelfAlumni) {
            $alumniOptions = $this->getTargetAlumniList($periode);
        }

        // Kelompokkan pertanyaan aktif berdasarkan bagian
        $groupedQuestions = [];
        foreach ($detail['pertanyaan'] as $q) {
            if ((int) ($q['is_aktif'] ?? 1) !== 1) {
                continue;
            }
            $sec = trim((string) ($q['bagian'] ?? 'Umum')) ?: 'Umum';
            $groupedQuestions[$sec][] = $q;
        }

        return [
            'status'            => true,
            'periode'           => $periode,
            'pertanyaan'        => $detail['pertanyaan'],
            'grouped_questions' => $groupedQuestions,
            'alumni'            => $alumniRow,
            'alumni_options'    => $alumniOptions,
            'existing'          => $existing,
            'answers'           => $answers,
        ];
    }

    /**
     * Menyimpan respons Tracer Study alumni beserta seluruh jawaban pertanyaan dinamis.
     *
     * @param int                  $periodeId
     * @param string               $nomorTaruna
     * @param array<string, mixed> $input
     * @param string               $actor
     * @param bool                 $isSelfAlumni
     * @return array<string, mixed>
     */
    public function submitRespons(
        int $periodeId,
        string $nomorTaruna,
        array $input,
        string $actor,
        bool $isSelfAlumni = false,
        ?array $unregisteredIdentity = null
    ): array {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return ['status' => false, 'message' => 'Periode survei tidak ditemukan.'];
        }
        $periode = $detail['periode'];

        if (($periode['status_periode'] ?? '') === 'draf') {
            return ['status' => false, 'message' => 'Periode survei masih berstatus Draf dan belum dibuka.'];
        }
        if ($isSelfAlumni && !ketarunaan_is_tracer_periode_open($periode)) {
            return ['status' => false, 'message' => 'Periode survei ini telah ditutup.'];
        }

        $nimClean  = trim($nomorTaruna);
        $alumniRow = $this->getAlumniBasicProfile($nimClean);
        $isUnreg   = false;

        if (!$alumniRow) {
            if (
                $unregisteredIdentity !== null
                && (int) ($unregisteredIdentity['is_alumni_terdaftar'] ?? 1) === 0
                && (int) ($periode['allow_unregistered_alumni'] ?? 1) === 1
            ) {
                $isUnreg   = true;
                $alumniRow = [
                    'nomor_taruna'     => $nimClean,
                    'nama_lengkap'     => (string) ($unregisteredIdentity['nama_lengkap'] ?? $nimClean),
                    'program_studi_id' => (int) ($unregisteredIdentity['program_studi_id'] ?? 0),
                    'tahun_lulus'      => (int) ($unregisteredIdentity['tahun_lulus'] ?? 0),
                ];
            } else {
                return ['status' => false, 'message' => 'Data alumni aktif tidak ditemukan.'];
            }
        }
        if ($isSelfAlumni && !$this->isAlumniEligibleForPeriode($alumniRow, $periode)) {
            return ['status' => false, 'message' => 'Anda tidak termasuk sasaran periode survei ini.'];
        }

        $statusTransisi = strtolower(trim((string) ($input['status_transisi'] ?? '')));
        if (!array_key_exists($statusTransisi, ketarunaan_tracer_status_transisi_catalog())) {
            return ['status' => false, 'message' => 'Pilih status kegiatan utama setelah lulus.'];
        }

        $isWorking   = in_array($statusTransisi, ['bekerja', 'wirausaha'], true);
        $namaInstansi = trim((string) ($input['nama_instansi'] ?? ''));
        if ($isWorking && $namaInstansi === '') {
            return ['status' => false, 'message' => 'Nama instansi, perusahaan, atau usaha wajib diisi.'];
        }

        $masaTungguRaw = trim((string) ($input['masa_tunggu_bulan'] ?? ''));
        $masaTunggu    = ($masaTungguRaw !== '' && is_numeric($masaTungguRaw)) ? max(0, (int) $masaTungguRaw) : null;
        $tglMulaiKerja = trim((string) ($input['tanggal_mulai_kerja'] ?? ''));

        $kesesuaianBidang  = trim((string) ($input['kesesuaian_bidang'] ?? ''));
        if ($kesesuaianBidang !== '' && !array_key_exists($kesesuaianBidang, ketarunaan_tracer_kesesuaian_bidang_catalog())) {
            $kesesuaianBidang = '';
        }
        $kesesuaianJenjang = trim((string) ($input['kesesuaian_jenjang'] ?? ''));
        if ($kesesuaianJenjang !== '' && !array_key_exists($kesesuaianJenjang, ketarunaan_tracer_kesesuaian_jenjang_catalog())) {
            $kesesuaianJenjang = '';
        }

        // Validasi jawaban pertanyaan dinamis periode
        $rawAnswers     = is_array($input['jawaban'] ?? null) ? $input['jawaban'] : [];
        $answersToStore = [];

        foreach ($detail['pertanyaan'] as $q) {
            if ((int) ($q['is_aktif'] ?? 1) !== 1) {
                continue;
            }
            $qid     = (int) $q['id'];
            $tipe    = (string) $q['tipe_jawaban'];
            $isWajib = ((int) ($q['is_wajib'] ?? 1) === 1);
            $val     = $rawAnswers[$qid] ?? null;

            $nilaiTeks  = null;
            $nilaiAngka = null;
            $nilaiOpsi  = null;
            $hasValue   = false;

            if ($tipe === 'pilihan_ganda') {
                $arr = is_array($val) ? array_values(array_filter(array_map('trim', $val), static fn ($v) => $v !== '')) : [];
                if (!empty($arr)) {
                    $hasValue  = true;
                    $nilaiOpsi = json_encode($arr, JSON_UNESCAPED_UNICODE);
                    $nilaiTeks = implode(', ', $arr);
                }
            } elseif ($tipe === 'pilihan_tunggal') {
                $str = is_string($val) ? trim($val) : '';
                if ($str !== '') {
                    $hasValue  = true;
                    $nilaiOpsi = json_encode([$str], JSON_UNESCAPED_UNICODE);
                    $nilaiTeks = $str;
                }
            } elseif ($tipe === 'skala_likert') {
                $str = is_scalar($val) ? trim((string) $val) : '';
                if ($str !== '' && is_numeric($str)) {
                    $num = (int) $str;
                    if ($num < 1 || $num > 5) {
                        return ['status' => false, 'message' => 'Nilai skala harus antara 1 sampai 5 pada pertanyaan: ' . $q['teks_pertanyaan']];
                    }
                    $hasValue   = true;
                    $nilaiAngka = (float) $num;
                    $nilaiTeks  = (string) $num;
                }
            } elseif ($tipe === 'angka') {
                $str = is_scalar($val) ? str_replace(',', '.', trim((string) $val)) : '';
                if ($str !== '' && is_numeric($str)) {
                    $hasValue   = true;
                    $nilaiAngka = (float) $str;
                    $nilaiTeks  = $str;
                }
            } else {
                $str = is_scalar($val) ? trim((string) $val) : '';
                if ($str !== '') {
                    $hasValue  = true;
                    $nilaiTeks = $str;
                }
            }

            if ($isWajib && !$hasValue) {
                return ['status' => false, 'message' => 'Pertanyaan wajib belum diisi: "' . $q['teks_pertanyaan'] . '"'];
            }

            if ($hasValue) {
                $answersToStore[] = [
                    'periode_pertanyaan_id' => $qid,
                    'kode_pertanyaan'       => (string) $q['kode_pertanyaan'],
                    'nilai_teks'            => $nilaiTeks,
                    'nilai_angka'           => $nilaiAngka,
                    'nilai_opsi_json'       => $nilaiOpsi,
                ];
            }
        }

        $rawSumber = trim((string) ($input['sumber_pengisian'] ?? ''));
        if ($isSelfAlumni) {
            $sumber = ($rawSumber === 'tautan_publik') ? 'tautan_publik' : 'mandiri_alumni';
        } else {
            $sumber = in_array($rawSumber, ['telepon_pic', 'whatsapp', 'email'], true) ? $rawSumber : 'telepon_pic';
        }

        $existing = $this->responsModel
            ->where('periode_id', $periodeId)
            ->where('nomor_taruna', $nimClean)
            ->first();

        if ($existing && $isSelfAlumni && ($existing['status_validasi'] ?? '') !== 'ditolak') {
            return [
                'status'  => false,
                'message' => 'Anda sudah mengisi survei pada periode ini. Jawaban yang telah dikirim tidak dapat diubah.',
            ];
        }

        $now = date('Y-m-d H:i:s');
        $headerData = [
            'periode_id'              => $periodeId,
            'nomor_taruna'            => $nimClean,
            'is_alumni_terdaftar'     => $isUnreg ? 0 : 1,
            'nama_responden'          => $isUnreg ? trim((string) ($unregisteredIdentity['nama_lengkap'] ?? '')) : null,
            'prodi_id_responden'      => $isUnreg ? ((int) ($unregisteredIdentity['program_studi_id'] ?? 0) ?: null) : null,
            'tahun_lulus_responden'   => $isUnreg ? ((int) ($unregisteredIdentity['tahun_lulus'] ?? 0) ?: null) : null,
            'tanggal_lahir_responden' => $isUnreg ? (trim((string) ($unregisteredIdentity['tanggal_lahir'] ?? '')) ?: null) : null,
            'no_hp_responden'         => $isUnreg ? (trim((string) ($unregisteredIdentity['no_hp'] ?? '')) ?: null) : null,
            'email_responden'         => $isUnreg ? (trim((string) ($unregisteredIdentity['email'] ?? '')) ?: null) : null,
            'status_transisi'         => $statusTransisi,
            'masa_tunggu_bulan'       => $masaTunggu,
            'tanggal_mulai_kerja'     => $tglMulaiKerja !== '' ? $tglMulaiKerja : null,
            'nama_instansi'           => $namaInstansi !== '' ? $namaInstansi : null,
            'sektor_industri'         => trim((string) ($input['sektor_industri'] ?? '')) ?: null,
            'jabatan_posisi'          => trim((string) ($input['jabatan_posisi'] ?? '')) ?: null,
            'kota_kerja'              => trim((string) ($input['kota_kerja'] ?? '')) ?: null,
            'kesesuaian_bidang'       => $kesesuaianBidang !== '' ? $kesesuaianBidang : null,
            'kesesuaian_jenjang'      => $kesesuaianJenjang !== '' ? $kesesuaianJenjang : null,
            'sumber_pengisian'        => $sumber,
            'status_validasi'         => $isUnreg ? 'diajukan' : 'tervalidasi',
            'dicatat_oleh'            => $actor,
            'divalidasi_oleh'         => $isUnreg ? null : ($isSelfAlumni ? 'Mandiri Alumni' : $actor),
            'tanggal_pengisian'       => $now,
        ];

        $this->db->transStart();
        try {
            if ($existing) {
                $responsId = (int) $existing['id'];
                $this->responsModel->update($responsId, $headerData);
                $this->jawabanModel->where('respons_id', $responsId)->delete();
            } else {
                $responsId = (int) $this->responsModel->insert($headerData, true);
            }

            foreach ($answersToStore as $ans) {
                $ans['respons_id'] = $responsId;
                $this->jawabanModel->insert($ans);
            }

            // Sinkronkan ringkasan pekerjaan terbaru ke alumni_profil bila sudah terdaftar di master
            if (!$isUnreg && $isWorking && $namaInstansi !== '') {
                $syncData = ['instansi_saat_ini' => $namaInstansi];
                if (!empty($headerData['jabatan_posisi'])) {
                    $syncData['pekerjaan_saat_ini'] = $headerData['jabatan_posisi'];
                }
                if (!empty($headerData['kota_kerja'])) {
                    $syncData['kota_domisili'] = $headerData['kota_kerja'];
                }
                $this->alumniModel->update($nimClean, $syncData);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyimpan jawaban survei. Silakan coba lagi.'];
            }

            $msg = $isUnreg
                ? 'Jawaban survei tersimpan dan menunggu verifikasi data alumni oleh pengelola PKTJ.'
                : ($existing ? 'Jawaban Tracer Study berhasil diperbarui.' : 'Terima kasih, jawaban Tracer Study berhasil disimpan.');

            return [
                'status'  => true,
                'id'      => $responsId,
                'message' => $msg,
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal menyimpan jawaban survei. Silakan coba lagi.'];
        }
    }

    /**
     * Menyetujui respons alumni baru (belum terdaftar di DB), mendaftarkannya ke master_taruna,
     * pendidikan_taruna, dan alumni_profil, serta opsional membuatkan akun login ECC (role = alumni).
     *
     * @param int    $responsId
     * @param bool   $createAccount
     * @param string $actor
     * @return array<string, mixed>
     */
    public function approveUnregisteredRespons(int $responsId, bool $createAccount, string $actor): array
    {
        $row = $this->responsModel->find($responsId);
        if (!$row) {
            return ['status' => false, 'message' => 'Data respons Tracer Study tidak ditemukan.'];
        }
        if (($row['status_validasi'] ?? '') === 'tervalidasi' && (int) ($row['is_alumni_terdaftar'] ?? 1) === 1) {
            return ['status' => false, 'message' => 'Respons alumni ini sudah diverifikasi sebelumnya.'];
        }

        $notar    = trim((string) $row['nomor_taruna']);
        $nama     = trim((string) ($row['nama_responden'] ?: $notar));
        $prodiId  = (int) ($row['prodi_id_responden'] ?? 0);
        $tahun    = (int) ($row['tahun_lulus_responden'] ?? date('Y'));
        $dob      = trim((string) ($row['tanggal_lahir_responden'] ?? ''));
        $noHp     = trim((string) ($row['no_hp_responden'] ?? ''));
        $email    = trim((string) ($row['email_responden'] ?? ''));
        $today    = date('Y-m-d');
        $now      = date('Y-m-d H:i:s');

        // Cegah menimpa taruna aktif
        $existingMt = $this->db->table('master_taruna')->where('nomor_taruna', $notar)->get()->getRowArray();
        if ($existingMt && strtolower(trim((string) ($existingMt['status_terkini'] ?? ''))) !== 'lulus') {
            return ['status' => false, 'message' => 'Nomor Taruna ' . $notar . ' masih berstatus taruna aktif di Database Taruna.'];
        }

        // Resolusi prodi & angkatan
        if ($prodiId <= 0) {
            $firstProdi = $this->db->table('ref_program_studi')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
            $prodiId    = (int) ($firstProdi['id'] ?? 1);
        }
        $estAngkatan = max(1970, $tahun - 4);
        $angRow      = $this->db->table('ref_angkatan')->where('tahun_masuk', $estAngkatan)->get()->getRowArray();
        if (!$angRow) {
            $angRow = $this->db->table('ref_angkatan')->orderBy('tahun_masuk', 'ASC')->get()->getRowArray();
        }
        $angkatanId = (int) ($angRow['id'] ?? 1);

        $accountCreated = false;
        $initialPwdHint = '';

        $this->db->transStart();
        try {
            // 1. Master Taruna (status lulus)
            if (!$existingMt) {
                $this->db->table('master_taruna')->insert([
                    'nomor_taruna'   => $notar,
                    'nama_lengkap'   => $nama,
                    'jenis_kelamin'  => 'L',
                    'tanggal_lahir'  => $dob !== '' ? $dob : null,
                    'nomor_hp'       => $noHp !== '' ? $noHp : null,
                    'email'          => $email !== '' ? $email : null,
                    'status_terkini' => 'lulus',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }

            // 2. Pendidikan Taruna
            $existingPt = $this->db->table('pendidikan_taruna')->where('nomor_taruna', $notar)->get()->getRowArray();
            if (!$existingPt) {
                $this->db->table('pendidikan_taruna')->insert([
                    'nomor_taruna'     => $notar,
                    'program_studi_id' => $prodiId,
                    'angkatan_id'      => $angkatanId,
                    'tingkat'          => 4,
                    'semester_aktif'   => 8,
                    'jalur_penerimaan' => 'Reguler',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }

            // 3. Alumni Profil (aktif)
            $existingAp = $this->db->table('alumni_profil')->where('nomor_taruna', $notar)->get()->getRowArray();
            if (!$existingAp) {
                $this->db->table('alumni_profil')->insert([
                    'nomor_taruna'      => $notar,
                    'tahun_lulus'       => $tahun,
                    'pekerjaan_saat_ini'=> $row['jabatan_posisi'] ?: null,
                    'instansi_saat_ini' => $row['nama_instansi'] ?: null,
                    'email_alumni'      => $email !== '' ? $email : null,
                    'telepon_alumni'    => $noHp !== '' ? $noHp : null,
                    'kota_domisili'     => $row['kota_kerja'] ?: null,
                    'status_profil'     => 'aktif',
                    'sumber_konfirmasi' => 'mandiri_alumni',
                    'dicatat_oleh'      => $actor,
                    'divalidasi_oleh'   => $actor,
                    'tanggal_validasi'  => $today,
                    'catatan_profil'    => 'Didaftarkan melalui verifikasi Tracer Study.',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }

            // 4. Opsional buatkan akun login ECC (role = alumni, username = Notar, sandi awal = DDMMYYYY dari tanggal lahir)
            if ($createAccount) {
                $existingUser = $this->db->table('users')->where('username', $notar)->get()->getRowArray();
                if (!$existingUser) {
                    $rawPwd = ($dob !== '' && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m))
                        ? ($m[3] . $m[2] . $m[1])
                        : ($notar);
                    $initialPwdHint = $rawPwd;
                    $this->db->table('users')->insert([
                        'username'       => $notar,
                        'password'       => password_hash($rawPwd, PASSWORD_DEFAULT),
                        'nama_lengkap'   => $nama,
                        'email'          => $email !== '' ? $email : ($notar . '@alumni.pktj.ac.id'),
                        'role'           => 'alumni',
                        'status_pegawai' => 'aktif',
                        'nip'            => '',
                        'jabatan'        => 'Alumni',
                        'unit'           => 'Alumni PKTJ',
                        'no_hp'          => $noHp !== '' ? $noHp : null,
                    ]);
                    $newUserId = (int) $this->db->insertID();
                    if ($newUserId > 0 && $this->db->tableExists('user_roles')) {
                        $this->db->table('user_roles')->ignore(true)->insert([
                            'user_id'   => $newUserId,
                            'role_name' => 'alumni',
                        ]);
                    }
                    $accountCreated = true;
                }
            }

            // 5. Sahkan respons Tracer Study
            $this->responsModel->update($responsId, [
                'is_alumni_terdaftar' => 1,
                'status_validasi'     => 'tervalidasi',
                'divalidasi_oleh'     => $actor,
                'catatan_validasi'    => $accountCreated ? 'Disetujui & akun login ECC dibuat.' : 'Disetujui & didaftarkan ke Data Alumni.',
            ]);

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal memverifikasi dan mendaftarkan alumni.'];
            }

            $msg = 'Alumni ' . $nama . ' (' . $notar . ') berhasil diverifikasi dan masuk ke Data Alumni.';
            if ($accountCreated) {
                $msg .= ' Akun login ECC dibuat (Username: ' . $notar . ', Kata Sandi awal: ' . $initialPwdHint . ').';
            }

            return ['status' => true, 'message' => $msg];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'Gagal memverifikasi alumni. Silakan coba lagi.'];
        }
    }

    /**
     * Menolak respons alumni baru (mis. spam / data tidak valid).
     */
    public function rejectUnregisteredRespons(int $responsId, string $alasan, string $actor): array
    {
        $row = $this->responsModel->find($responsId);
        if (!$row) {
            return ['status' => false, 'message' => 'Data respons tidak ditemukan.'];
        }

        $this->responsModel->update($responsId, [
            'status_validasi'  => 'ditolak',
            'catatan_validasi' => trim($alasan) ?: 'Ditolak saat verifikasi alumni baru.',
            'divalidasi_oleh'  => $actor,
        ]);

        return ['status' => true, 'message' => 'Respons alumni baru telah ditolak dan dikeluarkan dari statistik.'];
    }

    /**
     * Analitik lengkap progres & hasil untuk 1 periode pembukaan.
     *
     * @param int                  $periodeId
     * @param array<string, mixed> $filters
     * @return array<string, mixed>|null
     */
    public function getPeriodAnalytics(int $periodeId, array $filters = []): ?array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return null;
        }
        $periode    = $detail['periode'];
        $pertanyaan = $detail['pertanyaan'];

        $prodiFilter = (int) ($filters['prodi_id'] ?? 0);
        $tahunFilter = (int) ($filters['tahun_lulus'] ?? 0);

        $targetAlumni = $this->getTargetAlumniList($periode, $prodiFilter, $tahunFilter);
        $targetByNim  = [];
        $prodiStats   = [];
        $tahunStats   = [];

        foreach ($targetAlumni as $al) {
            $nim   = (string) $al['nomor_taruna'];
            $pCode = (string) ($al['kode_prodi'] ?: 'Lainnya');
            $pName = (string) ($al['nama_prodi'] ?: $pCode);
            $yr    = (int) ($al['tahun_lulus'] ?? 0);

            $targetByNim[$nim] = $al;
            if (!isset($prodiStats[$pCode])) {
                $prodiStats[$pCode] = ['kode_prodi' => $pCode, 'nama_prodi' => $pName, 'sasaran' => 0, 'terisi' => 0];
            }
            $prodiStats[$pCode]['sasaran']++;

            if ($yr > 0) {
                if (!isset($tahunStats[$yr])) {
                    $tahunStats[$yr] = ['tahun_lulus' => $yr, 'sasaran' => 0, 'terisi' => 0];
                }
                $tahunStats[$yr]['sasaran']++;
            }
        }

        // Ambil seluruh respons tervalidasi pada periode ini (terfilter prodi / tahun jika dipilih)
        $respBuilder = $this->db->table('tracer_study_respons r')
            ->select('r.*, COALESCE(mt.nama_lengkap, r.nama_responden) AS nama_lengkap,
                      COALESCE(ap.tahun_lulus, r.tahun_lulus_responden) AS tahun_lulus,
                      COALESCE(ps.id, ps_r.id) AS prodi_id,
                      COALESCE(ps.kode_prodi, ps_r.kode_prodi) AS kode_prodi,
                      COALESCE(ps.nama_prodi, ps_r.nama_prodi) AS nama_prodi', false)
            ->join('master_taruna mt', 'mt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('alumni_profil ap', 'ap.nomor_taruna = r.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_program_studi ps_r', 'ps_r.id = r.prodi_id_responden', 'left')
            ->where('r.periode_id', $periodeId)
            ->where('r.status_validasi', 'tervalidasi');

        if ($prodiFilter > 0) {
            $respBuilder->where('COALESCE(pt.program_studi_id, r.prodi_id_responden) =', $prodiFilter, false);
        }
        if ($tahunFilter > 0) {
            $respBuilder->where('COALESCE(ap.tahun_lulus, r.tahun_lulus_responden) =', $tahunFilter, false);
        }

        $responses = $respBuilder->orderBy('r.tanggal_pengisian', 'DESC')->get()->getResultArray();

        $pendingVerifCount = (int) $this->db->table('tracer_study_respons')
            ->where('periode_id', $periodeId)
            ->where('status_validasi', 'diajukan')
            ->countAllResults();

        $transisiCounts = [];
        foreach (ketarunaan_tracer_status_transisi_catalog() as $k => $meta) {
            $transisiCounts[$k] = ['kode' => $k, 'label' => $meta['label'], 'count' => 0];
        }
        $kesesuaianCounts = [];
        foreach (ketarunaan_tracer_kesesuaian_bidang_catalog() as $k => $lbl) {
            $kesesuaianCounts[$k] = ['kode' => $k, 'label' => $lbl, 'count' => 0];
        }

        $respIds        = [];
        $terserapCount  = 0;
        $masaTungguSum  = 0;
        $masaTungguCnt  = 0;
        $sesuaiCnt      = 0;
        $kesesuaianTot  = 0;

        foreach ($responses as $r) {
            $respIds[] = (int) $r['id'];
            $pCode     = (string) ($r['kode_prodi'] ?: 'Lainnya');
            $pName     = (string) ($r['nama_prodi'] ?: $pCode);
            $yr        = (int) ($r['tahun_lulus'] ?? 0);

            if (!isset($prodiStats[$pCode])) {
                $prodiStats[$pCode] = ['kode_prodi' => $pCode, 'nama_prodi' => $pName, 'sasaran' => 0, 'terisi' => 0];
            }
            $prodiStats[$pCode]['terisi']++;

            if ($yr > 0) {
                if (!isset($tahunStats[$yr])) {
                    $tahunStats[$yr] = ['tahun_lulus' => $yr, 'sasaran' => 0, 'terisi' => 0];
                }
                $tahunStats[$yr]['terisi']++;
            }

            $st = (string) ($r['status_transisi'] ?? 'bekerja');
            if (isset($transisiCounts[$st])) {
                $transisiCounts[$st]['count']++;
            }
            if (in_array($st, ['bekerja', 'wirausaha', 'studi_lanjut'], true)) {
                $terserapCount++;
            }

            if ($r['masa_tunggu_bulan'] !== null && $r['masa_tunggu_bulan'] !== '') {
                $masaTungguSum += (int) $r['masa_tunggu_bulan'];
                $masaTungguCnt++;
            }

            $kb = (string) ($r['kesesuaian_bidang'] ?? '');
            if ($kb !== '' && isset($kesesuaianCounts[$kb])) {
                $kesesuaianCounts[$kb]['count']++;
                $kesesuaianTot++;
                if (in_array($kb, ['sangat_sesuai', 'sesuai'], true)) {
                    $sesuaiCnt++;
                }
            }
        }

        // Hitung persentase per prodi & per tahun
        foreach ($prodiStats as $k => $ps) {
            $prodiStats[$k]['belum']  = max(0, $ps['sasaran'] - $ps['terisi']);
            $prodiStats[$k]['persen'] = $ps['sasaran'] > 0 ? round(($ps['terisi'] / $ps['sasaran']) * 100, 1) : ($ps['terisi'] > 0 ? 100.0 : 0.0);
        }
        krsort($tahunStats);
        foreach ($tahunStats as $k => $ts) {
            $tahunStats[$k]['belum']  = max(0, $ts['sasaran'] - $ts['terisi']);
            $tahunStats[$k]['persen'] = $ts['sasaran'] > 0 ? round(($ts['terisi'] / $ts['sasaran']) * 100, 1) : ($ts['terisi'] > 0 ? 100.0 : 0.0);
        }

        // Agregasi jawaban untuk setiap pertanyaan dinamis pada periode ini
        $jawabanByQ = [];
        if (!empty($respIds)) {
            $jRows = $this->db->table('tracer_study_jawaban j')
                ->select('j.*, r.nomor_taruna, mt.nama_lengkap')
                ->join('tracer_study_respons r', 'r.id = j.respons_id', 'inner')
                ->join('master_taruna mt', 'mt.nomor_taruna = r.nomor_taruna', 'left')
                ->whereIn('j.respons_id', $respIds)
                ->orderBy('j.id', 'DESC')
                ->get()
                ->getResultArray();
            foreach ($jRows as $jr) {
                $jawabanByQ[(int) $jr['periode_pertanyaan_id']][] = $jr;
            }
        }

        $questionSummaries = [];
        foreach ($pertanyaan as $q) {
            if ((int) ($q['is_aktif'] ?? 1) !== 1) {
                continue;
            }
            $qid   = (int) $q['id'];
            $tipe  = (string) $q['tipe_jawaban'];
            $items = $jawabanByQ[$qid] ?? [];
            $cnt   = count($items);

            $agg = [
                'id'              => $qid,
                'kode_pertanyaan' => $q['kode_pertanyaan'],
                'bagian'          => $q['bagian'],
                'teks_pertanyaan' => $q['teks_pertanyaan'],
                'tipe_jawaban'    => $tipe,
                'total_jawaban'   => $cnt,
                'options'         => [],
                'likert_avg'      => 0.0,
                'likert_dist'     => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'angka_stats'     => ['avg' => 0.0, 'min' => 0.0, 'max' => 0.0],
                'text_samples'    => [],
            ];

            if (in_array($tipe, ['pilihan_tunggal', 'pilihan_ganda'], true)) {
                $optCounts = [];
                foreach (($q['opsi_parsed'] ?? []) as $optLbl) {
                    if (is_string($optLbl) && trim($optLbl) !== '') {
                        $optCounts[$optLbl] = 0;
                    }
                }
                foreach ($items as $it) {
                    $decoded = $this->decodeOpsiJawaban($it['nilai_opsi_json'] ?? null);
                    if (empty($decoded) && !empty($it['nilai_teks'])) {
                        $decoded = [trim((string) $it['nilai_teks'])];
                    }
                    foreach ($decoded as $sel) {
                        if (!isset($optCounts[$sel])) {
                            $optCounts[$sel] = 0;
                        }
                        $optCounts[$sel]++;
                    }
                }
                foreach ($optCounts as $lbl => $c) {
                    $agg['options'][] = [
                        'label'  => $lbl,
                        'count'  => $c,
                        'persen' => $cnt > 0 ? round(($c / $cnt) * 100, 1) : 0.0,
                    ];
                }
            } elseif ($tipe === 'skala_likert') {
                $sum = 0.0;
                $vCnt = 0;
                foreach ($items as $it) {
                    $val = (int) round((float) ($it['nilai_angka'] ?? $it['nilai_teks'] ?? 0));
                    if ($val >= 1 && $val <= 5) {
                        $agg['likert_dist'][$val]++;
                        $sum += $val;
                        $vCnt++;
                    }
                }
                $agg['likert_avg'] = $vCnt > 0 ? round($sum / $vCnt, 2) : 0.0;
            } elseif ($tipe === 'angka') {
                $nums = [];
                foreach ($items as $it) {
                    if ($it['nilai_angka'] !== null) {
                        $nums[] = (float) $it['nilai_angka'];
                    }
                }
                if (!empty($nums)) {
                    $agg['angka_stats'] = [
                        'avg' => round(array_sum($nums) / count($nums), 2),
                        'min' => min($nums),
                        'max' => max($nums),
                    ];
                }
            } else {
                foreach (array_slice($items, 0, 8) as $it) {
                    $txt = trim((string) ($it['nilai_teks'] ?? ''));
                    if ($txt !== '') {
                        $agg['text_samples'][] = [
                            'nama'         => (string) ($it['nama_lengkap'] ?: $it['nomor_taruna']),
                            'nomor_taruna' => (string) $it['nomor_taruna'],
                            'teks'         => $txt,
                        ];
                    }
                }
            }

            $questionSummaries[] = $agg;
        }

        $sasaranTotal  = count($targetAlumni);
        $respondenCnt  = count($responses);
        $responseRate  = $sasaranTotal > 0 ? round(($respondenCnt / $sasaranTotal) * 100, 1) : ($respondenCnt > 0 ? 100.0 : 0.0);
        $terserapPct   = $respondenCnt > 0 ? round(($terserapCount / $respondenCnt) * 100, 1) : 0.0;
        $avgMasaTunggu = $masaTungguCnt > 0 ? round($masaTungguSum / $masaTungguCnt, 1) : 0.0;
        $sesuaiPct     = $kesesuaianTot > 0 ? round(($sesuaiCnt / $kesesuaianTot) * 100, 1) : 0.0;

        return [
            'periode'            => $periode,
            'kpi'                => [
                'sasaran_total'       => $sasaranTotal,
                'responden_total'     => $respondenCnt,
                'belum_mengisi'       => max(0, $sasaranTotal - $respondenCnt),
                'response_rate'       => $responseRate,
                'target_persen'       => (float) ($periode['target_partisipasi_persen'] ?? 80),
                'terserap_count'      => $terserapCount,
                'terserap_persen'     => $terserapPct,
                'avg_masa_tunggu'     => $avgMasaTunggu,
                'sesuai_bidang_pct'   => $sesuaiPct,
                'jumlah_pertanyaan'   => count($questionSummaries),
                'pending_verif_count' => $pendingVerifCount,
            ],
            'prodi_progress'     => array_values($prodiStats),
            'tahun_progress'     => array_values($tahunStats),
            'transisi_counts'    => array_values($transisiCounts),
            'kesesuaian_counts'  => array_values($kesesuaianCounts),
            'question_summaries' => $questionSummaries,
        ];
    }

    /**
     * Perbandingan tren progres & hasil antar-periode pembukaan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCrossPeriodComparison(): array
    {
        $periodes = $this->listPeriodes();
        $result   = [];

        foreach ($periodes as $p) {
            $pid   = (int) $p['id'];
            $resps = $this->db->table('tracer_study_respons')
                ->select('status_transisi, masa_tunggu_bulan, kesesuaian_bidang')
                ->where('periode_id', $pid)
                ->where('status_validasi', 'tervalidasi')
                ->get()
                ->getResultArray();

            $cnt       = count($resps);
            $terserap  = 0;
            $waitSum   = 0;
            $waitCnt   = 0;
            $sesuai    = 0;
            $sesuaiTot = 0;

            foreach ($resps as $r) {
                if (in_array((string) $r['status_transisi'], ['bekerja', 'wirausaha', 'studi_lanjut'], true)) {
                    $terserap++;
                }
                if ($r['masa_tunggu_bulan'] !== null && $r['masa_tunggu_bulan'] !== '') {
                    $waitSum += (int) $r['masa_tunggu_bulan'];
                    $waitCnt++;
                }
                if (!empty($r['kesesuaian_bidang'])) {
                    $sesuaiTot++;
                    if (in_array((string) $r['kesesuaian_bidang'], ['sangat_sesuai', 'sesuai'], true)) {
                        $sesuai++;
                    }
                }
            }

            $result[] = [
                'id'               => $pid,
                'kode_periode'     => $p['kode_periode'],
                'nama_periode'     => $p['nama_periode'],
                'status_periode'   => $p['status_periode'],
                'sasaran_total'    => $p['sasaran_total'],
                'responden_total'  => $cnt,
                'response_rate'    => $p['response_rate'],
                'terserap_persen'  => $cnt > 0 ? round(($terserap / $cnt) * 100, 1) : 0.0,
                'avg_masa_tunggu'  => $waitCnt > 0 ? round($waitSum / $waitCnt, 1) : 0.0,
                'sesuai_bidang'    => $sesuaiTot > 0 ? round(($sesuai / $sesuaiTot) * 100, 1) : 0.0,
            ];
        }

        return $result;
    }

    /**
     * Daftar responden yang sudah mengisi beserta daftar alumni sasaran yang belum mengisi.
     *
     * @param int                  $periodeId
     * @param array<string, mixed> $filters
     * @return array<string, mixed>|null
     */
    public function listRespondenAndPending(int $periodeId, array $filters = []): ?array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return null;
        }
        $periode = $detail['periode'];

        $q           = trim((string) ($filters['q'] ?? ''));
        $prodiFilter = (int) ($filters['prodi_id'] ?? 0);
        $tahunFilter = (int) ($filters['tahun_lulus'] ?? 0);
        $transisi    = trim((string) ($filters['status_transisi'] ?? 'all'));

        $respBuilder = $this->db->table('tracer_study_respons r')
            ->select('r.*,
                      COALESCE(mt.nama_lengkap, r.nama_responden) AS nama_lengkap,
                      COALESCE(ap.tahun_lulus, r.tahun_lulus_responden) AS tahun_lulus,
                      COALESCE(ap.email_alumni, r.email_responden) AS email_alumni,
                      COALESCE(ap.telepon_alumni, r.no_hp_responden) AS telepon_alumni,
                      COALESCE(ps.kode_prodi, ps_r.kode_prodi) AS kode_prodi,
                      COALESCE(ps.nama_prodi, ps_r.nama_prodi) AS nama_prodi', false)
            ->join('master_taruna mt', 'mt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('alumni_profil ap', 'ap.nomor_taruna = r.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_program_studi ps_r', 'ps_r.id = r.prodi_id_responden', 'left')
            ->where('r.periode_id', $periodeId)
            ->where('r.status_validasi !=', 'ditolak');

        if ($prodiFilter > 0) {
            $respBuilder->where('COALESCE(pt.program_studi_id, r.prodi_id_responden) =', $prodiFilter, false);
        }
        if ($tahunFilter > 0) {
            $respBuilder->where('COALESCE(ap.tahun_lulus, r.tahun_lulus_responden) =', $tahunFilter, false);
        }
        if ($transisi !== '' && $transisi !== 'all') {
            $respBuilder->where('r.status_transisi', $transisi);
        }
        if ($q !== '') {
            $respBuilder->groupStart()
                ->like('mt.nama_lengkap', $q)
                ->orLike('r.nama_responden', $q)
                ->orLike('r.nomor_taruna', $q)
                ->orLike('r.nama_instansi', $q)
                ->groupEnd();
        }

        $allActiveRows = $respBuilder->orderBy('r.tanggal_pengisian', 'DESC')->get()->getResultArray();
        $respondedRows = [];
        $pendingVerif  = [];
        foreach ($allActiveRows as $row) {
            if (($row['status_validasi'] ?? '') === 'diajukan') {
                $pendingVerif[] = $row;
            } else {
                $respondedRows[] = $row;
            }
        }

        $allRespNims = [];
        $allRespRaw  = $this->db->table('tracer_study_respons')
            ->select('nomor_taruna')
            ->where('periode_id', $periodeId)
            ->where('status_validasi !=', 'ditolak')
            ->get()
            ->getResultArray();
        foreach ($allRespRaw as $rr) {
            $allRespNims[strtoupper(trim((string) $rr['nomor_taruna']))] = true;
        }

        $targetAlumni = $this->getTargetAlumniList($periode, $prodiFilter, $tahunFilter);
        $pendingRows  = [];
        foreach ($targetAlumni as $al) {
            $key = strtoupper(trim((string) $al['nomor_taruna']));
            if (isset($allRespNims[$key])) {
                continue;
            }
            if ($q !== '') {
                $hay = strtolower(($al['nama_lengkap'] ?? '') . ' ' . ($al['nomor_taruna'] ?? '') . ' ' . ($al['instansi_saat_ini'] ?? ''));
                if (strpos($hay, strtolower($q)) === false) {
                    continue;
                }
            }
            $pendingRows[] = $al;
        }

        return [
            'periode'       => $periode,
            'responded'     => $respondedRows,
            'pending_verif' => $pendingVerif,
            'pending'       => $pendingRows,
        ];
    }

    /**
     * Rincian jawaban 1 respons alumni.
     */
    public function getResponsDetail(int $responsId, ?string $viewerAlumniNim = null): ?array
    {
        $row = $this->db->table('tracer_study_respons r')
            ->select('r.*, p.kode_periode, p.nama_periode, p.status_periode,
                      COALESCE(mt.nama_lengkap, r.nama_responden) AS nama_lengkap,
                      COALESCE(ap.tahun_lulus, r.tahun_lulus_responden) AS tahun_lulus,
                      COALESCE(ap.email_alumni, r.email_responden) AS email_alumni,
                      COALESCE(ap.telepon_alumni, r.no_hp_responden) AS telepon_alumni,
                      COALESCE(ps.kode_prodi, ps_r.kode_prodi) AS kode_prodi,
                      COALESCE(ps.nama_prodi, ps_r.nama_prodi) AS nama_prodi', false)
            ->join('tracer_study_periode p', 'p.id = r.periode_id', 'inner')
            ->join('master_taruna mt', 'mt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('alumni_profil ap', 'ap.nomor_taruna = r.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = r.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->join('ref_program_studi ps_r', 'ps_r.id = r.prodi_id_responden', 'left')
            ->where('r.id', $responsId)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }
        if ($viewerAlumniNim !== null && strcasecmp(trim($viewerAlumniNim), trim((string) $row['nomor_taruna'])) !== 0) {
            return null;
        }

        $pertanyaan = $this->periodePertanyaanModel
            ->where('periode_id', (int) $row['periode_id'])
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $ansRows = $this->jawabanModel->where('respons_id', $responsId)->findAll();
        $ansMap  = [];
        foreach ($ansRows as $ar) {
            $ansMap[(int) $ar['periode_pertanyaan_id']] = $ar;
        }

        $qaList = [];
        foreach ($pertanyaan as $q) {
            $ans = $ansMap[(int) $q['id']] ?? null;
            $qaList[] = [
                'bagian'          => $q['bagian'],
                'kode_pertanyaan' => $q['kode_pertanyaan'],
                'teks_pertanyaan' => $q['teks_pertanyaan'],
                'tipe_jawaban'    => $q['tipe_jawaban'],
                'nilai_teks'      => $ans['nilai_teks'] ?? '-',
                'nilai_angka'     => $ans['nilai_angka'] ?? null,
            ];
        }

        return [
            'respons' => $row,
            'jawaban' => $qaList,
        ];
    }

    /**
     * Menghasilkan baris CSV ekspor hasil Tracer Study 1 periode lengkap dengan kolom pertanyaan dinamis.
     *
     * @param int                  $periodeId
     * @param array<string, mixed> $filters
     * @return array<int, array<int, string>>
     */
    public function getExportRows(int $periodeId, array $filters = []): array
    {
        $detail = $this->getPeriodeDetail($periodeId);
        if (!$detail) {
            return [];
        }

        $pertanyaan = array_values(array_filter(
            $detail['pertanyaan'],
            static fn (array $q): bool => (int) ($q['is_aktif'] ?? 1) === 1
        ));

        $list = $this->listRespondenAndPending($periodeId, $filters);
        $rows = $list['responded'] ?? [];

        $respIds = array_map(static fn (array $r): int => (int) $r['id'], $rows);
        $ansMap  = [];
        if (!empty($respIds)) {
            $ansRows = $this->jawabanModel->whereIn('respons_id', $respIds)->findAll();
            foreach ($ansRows as $ar) {
                $ansMap[(int) $ar['respons_id']][(int) $ar['periode_pertanyaan_id']] = (string) ($ar['nilai_teks'] ?? '');
            }
        }

        $header = [
            'Nomor Taruna',
            'Nama Alumni',
            'Prodi',
            'Tahun Lulus',
            'Status Transisi',
            'Masa Tunggu (Bulan)',
            'Instansi / Perusahaan',
            'Sektor Industri',
            'Jabatan / Posisi',
            'Kota Kerja',
            'Kesesuaian Bidang',
            'Kesesuaian Jenjang',
            'Sumber Pengisian',
            'Tanggal Pengisian',
        ];
        foreach ($pertanyaan as $q) {
            $header[] = '[' . $q['kode_pertanyaan'] . '] ' . $q['teks_pertanyaan'];
        }

        $csv           = [$header];
        $transisiMap   = ketarunaan_tracer_status_transisi_catalog();
        $bidangMap     = ketarunaan_tracer_kesesuaian_bidang_catalog();
        $jenjangMap    = ketarunaan_tracer_kesesuaian_jenjang_catalog();

        foreach ($rows as $r) {
            $rid  = (int) $r['id'];
            $line = [
                (string) ($r['nomor_taruna'] ?? ''),
                (string) ($r['nama_lengkap'] ?? ''),
                (string) ($r['kode_prodi'] ?? ''),
                (string) ($r['tahun_lulus'] ?? ''),
                (string) ($transisiMap[$r['status_transisi'] ?? '']['label'] ?? $r['status_transisi'] ?? ''),
                $r['masa_tunggu_bulan'] !== null ? (string) $r['masa_tunggu_bulan'] : '',
                (string) ($r['nama_instansi'] ?? ''),
                (string) ($r['sektor_industri'] ?? ''),
                (string) ($r['jabatan_posisi'] ?? ''),
                (string) ($r['kota_kerja'] ?? ''),
                (string) ($bidangMap[$r['kesesuaian_bidang'] ?? ''] ?? ''),
                (string) ($jenjangMap[$r['kesesuaian_jenjang'] ?? ''] ?? ''),
                (string) ($r['sumber_pengisian'] ?? ''),
                (string) ($r['tanggal_pengisian'] ?? ''),
            ];

            foreach ($pertanyaan as $q) {
                $line[] = $ansMap[$rid][(int) $q['id']] ?? '';
            }
            $csv[] = $line;
        }

        return $csv;
    }

    /**
     * Normalisasi dan validasi input 1 pertanyaan untuk bundel atau periode.
     *
     * @param array<string, mixed> $input
     * @param int                  $parentId
     * @param string               $scope bundle|periode
     * @param int|null             $questionId
     * @return array<string, mixed>
     */
    private function normalizeQuestionInput(array $input, int $parentId, string $scope, ?int $questionId = null): array
    {
        $teks = trim((string) ($input['teks_pertanyaan'] ?? ''));
        if ($teks === '') {
            return ['status' => false, 'message' => 'Teks pertanyaan wajib diisi.'];
        }

        $tipe = strtolower(trim((string) ($input['tipe_jawaban'] ?? 'pilihan_tunggal')));
        if (!array_key_exists($tipe, ketarunaan_tracer_tipe_jawaban_catalog())) {
            return ['status' => false, 'message' => 'Tipe jawaban tidak valid.'];
        }

        $bagian = trim((string) ($input['bagian'] ?? 'Umum')) ?: 'Umum';
        $kode   = strtoupper(trim((string) ($input['kode_pertanyaan'] ?? '')));
        if ($kode === '') {
            $kode = 'TS_Q_' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        }
        $kode = preg_replace('/[^A-Z0-9_]/', '_', $kode) ?? $kode;

        $urutan = (int) ($input['urutan'] ?? 0);
        if ($urutan <= 0) {
            $tbl = $scope === 'bundle' ? 'tracer_study_bundle_pertanyaan' : 'tracer_study_periode_pertanyaan';
            $col = $scope === 'bundle' ? 'bundle_id' : 'periode_id';
            $maxRow = $this->db->table($tbl)->selectMax('urutan')->where($col, $parentId)->get()->getRowArray();
            $urutan = ((int) ($maxRow['urutan'] ?? 0)) + 1;
        }

        $opsiJson = null;
        if (in_array($tipe, ['pilihan_tunggal', 'pilihan_ganda'], true)) {
            $rawLines = preg_split('/\r\n|\r|\n/', (string) ($input['opsi_jawaban_text'] ?? '')) ?: [];
            $opts     = [];
            foreach ($rawLines as $ln) {
                $clean = trim($ln);
                if ($clean !== '' && !in_array($clean, $opts, true)) {
                    $opts[] = $clean;
                }
            }
            if (count($opts) < 2) {
                return ['status' => false, 'message' => 'Pertanyaan pilihan wajib memiliki minimal 2 opsi jawaban (1 opsi per baris).'];
            }
            $opsiJson = json_encode($opts, JSON_UNESCAPED_UNICODE);
        } elseif ($tipe === 'skala_likert') {
            $minLbl   = trim((string) ($input['likert_min_label'] ?? 'Sangat Kurang')) ?: 'Sangat Kurang';
            $maxLbl   = trim((string) ($input['likert_max_label'] ?? 'Sangat Baik')) ?: 'Sangat Baik';
            $opsiJson = json_encode(['min_label' => $minLbl, 'max_label' => $maxLbl], JSON_UNESCAPED_UNICODE);
        }

        return [
            'status'  => true,
            'payload' => [
                'kode_pertanyaan'    => substr($kode, 0, 50),
                'bagian'             => substr($bagian, 0, 100),
                'urutan'             => $urutan,
                'teks_pertanyaan'    => $teks,
                'keterangan_bantuan' => trim((string) ($input['keterangan_bantuan'] ?? '')) ?: null,
                'tipe_jawaban'       => $tipe,
                'opsi_jawaban_json'  => $opsiJson,
                'is_wajib'           => !empty($input['is_wajib']) ? 1 : 0,
                'is_aktif'           => 1,
            ],
        ];
    }

    /**
     * Mengambil profil ringkas 1 alumni aktif.
     */
    private function getAlumniBasicProfile(string $nomorTaruna): ?array
    {
        $row = $this->db->table('alumni_profil ap')
            ->select('ap.*, mt.nama_lengkap, pt.program_studi_id, ps.kode_prodi, ps.nama_prodi')
            ->join('master_taruna mt', 'mt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->where('ap.nomor_taruna', trim($nomorTaruna))
            ->where('ap.status_profil', 'aktif')
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Menghitung jumlah alumni aktif yang menjadi sasaran 1 periode.
     */
    private function countTargetAlumniForPeriode(array $periode): int
    {
        return count($this->getTargetAlumniList($periode));
    }

    /**
     * Daftar alumni aktif yang termasuk sasaran periode tertentu.
     *
     * @param array<string, mixed> $periode
     * @param int                  $extraProdiId
     * @param int                  $extraTahun
     * @return array<int, array<string, mixed>>
     */
    private function getTargetAlumniList(array $periode, int $extraProdiId = 0, int $extraTahun = 0): array
    {
        $builder = $this->db->table('alumni_profil ap')
            ->select('ap.nomor_taruna, ap.tahun_lulus, ap.pekerjaan_saat_ini, ap.instansi_saat_ini, ap.email_alumni, ap.telepon_alumni,
                      mt.nama_lengkap, pt.program_studi_id, ps.kode_prodi, ps.nama_prodi')
            ->join('master_taruna mt', 'mt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('pendidikan_taruna pt', 'pt.nomor_taruna = ap.nomor_taruna', 'left')
            ->join('ref_program_studi ps', 'ps.id = pt.program_studi_id', 'left')
            ->where('ap.status_profil', 'aktif');

        $tahunSasaran = $this->decodeIntList($periode['tahun_lulus_sasaran_json'] ?? null);
        $prodiSasaran = $this->decodeIntList($periode['prodi_sasaran_json'] ?? null);

        if (!empty($tahunSasaran)) {
            $builder->whereIn('ap.tahun_lulus', $tahunSasaran);
        }
        if (!empty($prodiSasaran)) {
            $builder->whereIn('pt.program_studi_id', $prodiSasaran);
        }
        if ($extraProdiId > 0) {
            $builder->where('pt.program_studi_id', $extraProdiId);
        }
        if ($extraTahun > 0) {
            $builder->where('ap.tahun_lulus', $extraTahun);
        }

        return $builder->orderBy('ap.tahun_lulus', 'DESC')
            ->orderBy('mt.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Memeriksa apakah 1 alumni termasuk sasaran periode survei.
     */
    private function isAlumniEligibleForPeriode(array $alumniRow, array $periode): bool
    {
        $tahunSasaran = $this->decodeIntList($periode['tahun_lulus_sasaran_json'] ?? null);
        if (!empty($tahunSasaran) && !in_array((int) ($alumniRow['tahun_lulus'] ?? 0), $tahunSasaran, true)) {
            return false;
        }

        $prodiSasaran = $this->decodeIntList($periode['prodi_sasaran_json'] ?? null);
        if (!empty($prodiSasaran) && !in_array((int) ($alumniRow['program_studi_id'] ?? 0), $prodiSasaran, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return array<int, mixed>
     */
    private function decodeOpsiJawaban(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<int, int>
     */
    private function decodeIntList(?string $json): array
    {
        if ($json === null || trim($json) === '') {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        return array_values(array_unique(array_filter(array_map('intval', $decoded), static fn (int $v): bool => $v > 0)));
    }

    /**
     * @param mixed $raw
     * @return array<int, int>
     */
    private function sanitizeIntArray($raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        return array_values(array_unique(array_filter(array_map('intval', $raw), static fn (int $v): bool => $v > 0)));
    }
}
