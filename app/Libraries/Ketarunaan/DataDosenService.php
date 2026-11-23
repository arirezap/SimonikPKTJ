<?php

namespace App\Libraries\Ketarunaan;

use App\Models\Ketarunaan\DosenProfilModel;
use App\Models\Ketarunaan\DosenRiwayatKualifikasiModel;
use CodeIgniter\Database\BaseConnection;
use Config\Database;

/**
 * Service terpusat Modul Data Dosen — Executive Profiling Pimpinan (ECC 2.0).
 * Menggunakan users.id sebagai sumber identitas tunggal (DRY), mengelola profil akademik,
 * kepakaran, sertifikasi, riwayat kualifikasi, serta agregasi indikator keputusan pimpinan.
 */
class DataDosenService
{
    protected BaseConnection $db;
    protected DosenProfilModel $profilModel;
    protected DosenRiwayatKualifikasiModel $kualifikasiModel;

    public function __construct(
        ?BaseConnection $db = null,
        ?DosenProfilModel $profilModel = null,
        ?DosenRiwayatKualifikasiModel $kualifikasiModel = null
    ) {
        $this->db               = $db ?? Database::connect();
        $this->profilModel      = $profilModel ?? new DosenProfilModel();
        $this->kualifikasiModel = $kualifikasiModel ?? new DosenRiwayatKualifikasiModel();
        helper(['ketarunaan', 'kinerja']);
    }

    /**
     * Mengambil daftar referensi (Prodi, kandidat pegawai non-taruna belum terdaftar dosen, dan katalog).
     *
     * @param bool $includeEmployeeCandidates
     * @return array<string, mixed>
     */
    public function getReferenceOptions(bool $includeEmployeeCandidates = false): array
    {
        $prodi = $this->db->table('ref_program_studi')
            ->select('id, kode_prodi, nama_prodi, jenjang')
            ->where('is_aktif', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $candidates = [];
        if ($includeEmployeeCandidates) {
            $candidates = $this->db->table('users u')
                ->select('u.id, u.nama_lengkap, u.nip, u.jabatan, u.unit, u.status_pegawai')
                ->whereNotIn('u.role', ['taruna', 'alumni'])
                ->where('NOT EXISTS (SELECT 1 FROM dosen_profil dp WHERE dp.user_id = u.id)', null, false)
                ->orderBy('u.nama_lengkap', 'ASC')
                ->get()
                ->getResultArray();
        }

        return [
            'prodi'       => $prodi,
            'candidates'  => $candidates,
            'jafung'      => ketarunaan_dosen_jafung_catalog(),
            'pendidikan'  => ketarunaan_dosen_pendidikan_catalog(),
            'serdos'      => ketarunaan_dosen_serdos_catalog(),
            'ikatan'      => ketarunaan_dosen_ikatan_catalog(),
            'kualifikasi' => ketarunaan_dosen_kualifikasi_catalog(),
        ];
    }

    /**
     * Memastikan pegawai memiliki baris dosen_profil sekaligus peran sekunder 'dosen' di user_roles.
     *
     * @param int    $userId
     * @param string $actor
     * @return int|null ID dosen_profil
     */
    public function ensureProfilForUser(int $userId, string $actor = 'system'): ?int
    {
        if ($userId <= 0 || !$this->db->tableExists('dosen_profil')) {
            return null;
        }

        $existing = $this->db->table('dosen_profil')
            ->select('id')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if ($existing) {
            $this->ensureDosenRoleInUserRoles($userId);
            return (int) $existing['id'];
        }

        $userRow = $this->db->table('users')
            ->select('id, jabatan, unit, role, status_pegawai')
            ->where('id', $userId)
            ->whereNotIn('role', ['taruna', 'alumni'])
            ->get()
            ->getRowArray();

        if (!$userRow) {
            return null;
        }

        $prodiMap = $this->getProdiCodeMap();
        $defaults = $this->inferInitialProfileDefaults($userRow, $prodiMap);
        $defaults['user_id']    = $userId;
        $defaults['updated_by'] = $actor;

        $newId = (int) $this->profilModel->insert($defaults, true);
        $this->ensureDosenRoleInUserRoles($userId);

        return $newId > 0 ? $newId : null;
    }

    /**
     * Sinkronisasi otomatis pegawai berjabatan dosen atau berperan sekunder 'dosen' yang belum masuk dosen_profil.
     *
     * @param string $actor
     * @return array<string, mixed>
     */
    public function syncDosenFromUsers(string $actor): array
    {
        $prodiMap = $this->getProdiCodeMap();

        $builder = $this->db->table('users u')
            ->select('u.id, u.nama_lengkap, u.jabatan, u.unit, u.role, u.status_pegawai')
            ->whereNotIn('u.role', ['taruna', 'alumni'])
            ->where('NOT EXISTS (SELECT 1 FROM dosen_profil dp WHERE dp.user_id = u.id)', null, false)
            ->groupStart()
                ->like('u.jabatan', 'Dosen')
                ->orLike('u.jabatan', 'Lektor')
                ->orLike('u.jabatan', 'Asisten Ahli')
                ->orLike('u.jabatan', 'Guru Besar');

        if ($this->db->tableExists('user_roles')) {
            $builder->orWhere("EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = u.id AND ur.role_name = 'dosen')", null, false);
        }

        $rows = $builder->groupEnd()->get()->getResultArray();

        $added = 0;
        $this->db->transStart();
        try {
            foreach ($rows as $du) {
                $uid      = (int) $du['id'];
                $defaults = $this->inferInitialProfileDefaults($du, $prodiMap);
                $defaults['user_id']    = $uid;
                $defaults['updated_by'] = $actor;

                $this->profilModel->insert($defaults);
                $this->ensureDosenRoleInUserRoles($uid);
                $added++;
            }

            // Pastikan seluruh dosen_profil lama juga tercatat memiliki peran sekunder 'dosen' di user_roles
            $existingIds = $this->db->table('dosen_profil')->select('user_id')->get()->getResultArray();
            foreach ($existingIds as $ex) {
                $this->ensureDosenRoleInUserRoles((int) ($ex['user_id'] ?? 0));
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'added' => 0, 'message' => 'Gagal menyinkronkan data dosen.'];
            }

            if ($added === 0) {
                return ['status' => true, 'added' => 0, 'message' => 'Seluruh pegawai dosen sudah tersinkronkan.'];
            }

            return ['status' => true, 'added' => $added, 'message' => $added . ' dosen baru berhasil disinkronkan dari data pegawai.'];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['status' => false, 'added' => 0, 'message' => 'Gagal menyinkronkan data dosen.'];
        }
    }

    /**
     * Direktori Data Dosen beserta 4 kartu KPI eksekutif, sebaran prodi/jafung, dan filter keputusan pimpinan.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getDirectoryData(array $filters = []): array
    {
        $allRows = $this->db->table('dosen_profil dp')
            ->select("dp.*, u.nama_lengkap, u.nip, u.jabatan, u.pangkat, u.unit, u.email, u.no_hp, u.foto, u.status_pegawai,
                      ps.kode_prodi, ps.nama_prodi,
                      (SELECT COUNT(*) FROM dosen_riwayat_kualifikasi rk WHERE rk.dosen_profil_id = dp.id AND rk.status_verifikasi = 'terverifikasi') AS jumlah_kualifikasi,
                      (SELECT COUNT(*) FROM dosen_riwayat_kualifikasi rk2 WHERE rk2.dosen_profil_id = dp.id AND rk2.kategori = 'sertifikasi_kompetensi' AND rk2.status_verifikasi = 'terverifikasi') AS jumlah_sertifikasi,
                      (SELECT COUNT(*) FROM dosen_riwayat_kualifikasi rk3 WHERE rk3.dosen_profil_id = dp.id AND rk3.status_verifikasi = 'menunggu') AS jumlah_menunggu", false)
            ->join('users u', 'u.id = dp.user_id', 'inner')
            ->join('ref_program_studi ps', 'ps.id = dp.homebase_prodi_id', 'left')
            ->orderBy("FIELD(dp.jabatan_fungsional, 'guru_besar', 'lektor_kepala', 'lektor', 'asisten_ahli', 'tenaga_pengajar')", '', false)
            ->orderBy('u.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung KPI & sebaran institusi dari keseluruhan populasi dosen
        $totalDosen      = count($allRows);
        $siapDitugaskan  = 0;
        $tugasBelajar    = 0;
        $s3Count         = 0;
        $s2Count         = 0;
        $serdosSudah     = 0;
        $menungguVerif   = 0;
        $jafungDist      = [];
        $prodiDist       = [];

        foreach (ketarunaan_dosen_jafung_catalog() as $jk => $jm) {
            $jafungDist[$jk] = ['kode' => $jk, 'label' => $jm['label'], 'count' => 0];
        }

        foreach ($allRows as $r) {
            $isAktifPeg = strtolower((string) ($r['status_pegawai'] ?? 'aktif')) === 'aktif';
            if ((int) ($r['is_siap_ditugaskan'] ?? 0) === 1 && $isAktifPeg) {
                $siapDitugaskan++;
            }
            if (($r['status_ikatan'] ?? '') === 'tugas_belajar') {
                $tugasBelajar++;
            }
            if (($r['pendidikan_terakhir'] ?? '') === 'S3') {
                $s3Count++;
            } elseif (($r['pendidikan_terakhir'] ?? '') === 'S2') {
                $s2Count++;
            }
            if (($r['status_serdos'] ?? '') === 'sudah') {
                $serdosSudah++;
            }
            $menungguVerif += (int) ($r['jumlah_menunggu'] ?? 0);

            $jf = (string) ($r['jabatan_fungsional'] ?? 'tenaga_pengajar');
            if (isset($jafungDist[$jf])) {
                $jafungDist[$jf]['count']++;
            }

            $pCode = (string) ($r['kode_prodi'] ?: 'Unit / Non-Homebase');
            if (!isset($prodiDist[$pCode])) {
                $prodiDist[$pCode] = ['kode' => $pCode, 'count' => 0];
            }
            $prodiDist[$pCode]['count']++;
        }

        // Terapkan filter pencarian kandidat keputusan pimpinan
        $q           = strtolower(trim((string) ($filters['q'] ?? '')));
        $prodiId     = (int) ($filters['prodi_id'] ?? 0);
        $jafung      = trim((string) ($filters['jabatan_fungsional'] ?? 'all'));
        $pendidikan  = trim((string) ($filters['pendidikan_terakhir'] ?? 'all'));
        $serdos      = trim((string) ($filters['status_serdos'] ?? 'all'));
        $ikatan      = trim((string) ($filters['status_ikatan'] ?? 'all'));
        $kesiapan    = trim((string) ($filters['kesiapan'] ?? 'all'));

        $filtered = [];
        foreach ($allRows as $r) {
            if ($prodiId > 0 && (int) ($r['homebase_prodi_id'] ?? 0) !== $prodiId) {
                continue;
            }
            if ($jafung !== '' && $jafung !== 'all' && ($r['jabatan_fungsional'] ?? '') !== $jafung) {
                continue;
            }
            if ($pendidikan !== '' && $pendidikan !== 'all' && ($r['pendidikan_terakhir'] ?? '') !== $pendidikan) {
                continue;
            }
            if ($serdos !== '' && $serdos !== 'all' && ($r['status_serdos'] ?? '') !== $serdos) {
                continue;
            }
            if ($ikatan !== '' && $ikatan !== 'all' && ($r['status_ikatan'] ?? '') !== $ikatan) {
                continue;
            }
            if ($kesiapan === 'siap' && (int) ($r['is_siap_ditugaskan'] ?? 0) !== 1) {
                continue;
            }
            if ($kesiapan === 'tidak_siap' && (int) ($r['is_siap_ditugaskan'] ?? 0) === 1) {
                continue;
            }
            if ($q !== '') {
                $hay = strtolower(
                    ($r['nama_lengkap'] ?? '') . ' '
                    . ($r['nip'] ?? '') . ' '
                    . ($r['nidn'] ?? '') . ' '
                    . ($r['bidang_keilmuan'] ?? '') . ' '
                    . ($r['kepakaran_tags'] ?? '') . ' '
                    . ($r['unit'] ?? '') . ' '
                    . ($r['kode_prodi'] ?? '')
                );
                if (strpos($hay, $q) === false) {
                    continue;
                }
            }

            $r['kepakaran_list'] = $this->parseTags((string) ($r['kepakaran_tags'] ?? ''));
            $filtered[]          = $r;
        }

        return [
            'kpi' => [
                'total_dosen'     => $totalDosen,
                'siap_ditugaskan' => $siapDitugaskan,
                'tugas_belajar'   => $tugasBelajar,
                's3_count'        => $s3Count,
                's2_count'        => $s2Count,
                'serdos_sudah'    => $serdosSudah,
                'serdos_persen'   => $totalDosen > 0 ? round(($serdosSudah / $totalDosen) * 100, 1) : 0.0,
                'menunggu_verif'  => $menungguVerif,
            ],
            'jafung_dist' => array_values($jafungDist),
            'prodi_dist'  => array_values($prodiDist),
            'rows'        => $filtered,
        ];
    }

    /**
     * Profil eksekutif lengkap 1 dosen (sinkron users + kualifikasi + indikator kinerja ECC bila berwenang).
     *
     * @param int  $dosenProfilId
     * @param bool $includeExecutiveMetrics
     * @return array<string, mixed>|null
     */
    public function getDosenDetail(int $dosenProfilId, bool $includeExecutiveMetrics = false): ?array
    {
        $row = $this->db->table('dosen_profil dp')
            ->select('dp.*, u.nama_lengkap, u.nip, u.jabatan, u.pangkat, u.unit, u.email, u.no_hp, u.foto, u.status_pegawai,
                      ps.kode_prodi, ps.nama_prodi')
            ->join('users u', 'u.id = dp.user_id', 'inner')
            ->join('ref_program_studi ps', 'ps.id = dp.homebase_prodi_id', 'left')
            ->where('dp.id', $dosenProfilId)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        $row['kepakaran_list'] = $this->parseTags((string) ($row['kepakaran_tags'] ?? ''));
        if (!$includeExecutiveMetrics) {
            $row['catatan_pimpinan'] = null;
        }

        $kualifikasi = $this->kualifikasiModel
            ->where('dosen_profil_id', $dosenProfilId)
            ->orderBy('tahun_selesai', 'DESC')
            ->orderBy('tahun_mulai', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $kualifikasiByKat = [];
        foreach (array_keys(ketarunaan_dosen_kualifikasi_catalog()) as $katKey) {
            $kualifikasiByKat[$katKey] = [];
        }
        $sertifikasiCount = 0;
        $penugasanCount   = 0;
        foreach ($kualifikasi as $k) {
            $kat = (string) ($k['kategori'] ?? 'pendidikan');
            $kualifikasiByKat[$kat][] = $k;
            if ($kat === 'sertifikasi_kompetensi' && ($k['status_verifikasi'] ?? '') === 'terverifikasi') {
                $sertifikasiCount++;
            }
            if ($kat === 'penugasan_khusus' && ($k['status_verifikasi'] ?? '') === 'terverifikasi') {
                $penugasanCount++;
            }
        }

        // Indikator kinerja ECC (Read-Only, khusus Pimpinan/Kepegawaian/Admin)
        $kinerjaSummary = null;
        if ($includeExecutiveMetrics && $this->db->tableExists('target_kinerja_bulanan')) {
            $uid   = (int) $row['user_id'];
            $tahun = (int) date('Y');
            $tRows = $this->db->table('target_kinerja_bulanan')
                ->select('bulan, nilai_capaian')
                ->where('user_id', $uid)
                ->where('tahun', $tahun)
                ->where('status_penilaian', 'terbit')
                ->where('nilai_capaian IS NOT NULL', null, false)
                ->get()
                ->getResultArray();

            $byBulan = [];
            foreach ($tRows as $tr) {
                $b = (int) $tr['bulan'];
                $byBulan[$b][] = (float) $tr['nilai_capaian'];
            }

            $monthlyAvg = [];
            foreach ($byBulan as $b => $vals) {
                $monthlyAvg[$b] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 2) : 0.0;
            }
            $overallAvg = !empty($monthlyAvg) ? round(array_sum($monthlyAvg) / count($monthlyAvg), 2) : null;
            $predikat   = ($overallAvg !== null && function_exists('hitung_predikat_kinerja'))
                ? hitung_predikat_kinerja($overallAvg)
                : ['label' => 'Belum Dinilai', 'badge_class' => 'bg-secondary-subtle text-secondary-emphasis'];

            $kinerjaSummary = [
                'tahun'        => $tahun,
                'bulan_dinilai'=> count($monthlyAvg),
                'rata_capaian' => $overallAvg,
                'predikat'     => $predikat,
            ];
        }

        // Indikator objektif pertimbangan penugasan pimpinan
        $isAktifPeg = strtolower((string) ($row['status_pegawai'] ?? 'aktif')) === 'aktif';
        $isTubel    = (($row['status_ikatan'] ?? '') === 'tugas_belajar');
        $jafungKey  = (string) ($row['jabatan_fungsional'] ?? 'tenaga_pengajar');
        $pendKey    = (string) ($row['pendidikan_terakhir'] ?? 'S2');

        $readinessChecks = [
            [
                'label' => 'Ketersediaan Aktif',
                'ok'    => ($isAktifPeg && !$isTubel && (int) ($row['is_siap_ditugaskan'] ?? 0) === 1),
                'desc'  => $isTubel ? 'Sedang Tugas Belajar' : ((int) ($row['is_siap_ditugaskan'] ?? 0) === 1 ? 'Siap ditugaskan' : 'Ditandai belum siap ditugaskan'),
            ],
            [
                'label' => 'Kualifikasi Pendidikan',
                'ok'    => in_array($pendKey, ['S2', 'S3', 'spesialis'], true),
                'desc'  => ketarunaan_dosen_pendidikan_catalog()[$pendKey] ?? $pendKey,
            ],
            [
                'label' => 'Jabatan Fungsional',
                'ok'    => in_array($jafungKey, ['lektor', 'lektor_kepala', 'guru_besar'], true),
                'desc'  => ketarunaan_dosen_jafung_catalog()[$jafungKey]['label'] ?? $jafungKey,
            ],
            [
                'label' => 'Sertifikasi Dosen & Kompetensi',
                'ok'    => (($row['status_serdos'] ?? '') === 'sudah' || $sertifikasiCount > 0),
                'desc'  => (($row['status_serdos'] ?? '') === 'sudah' ? 'Sudah Serdos' : 'Belum Serdos') . ' • ' . $sertifikasiCount . ' sertifikasi kompetensi',
            ],
        ];

        return [
            'dosen'              => $row,
            'kualifikasi'        => $kualifikasi,
            'kualifikasi_by_kat' => $kualifikasiByKat,
            'sertifikasi_count'  => $sertifikasiCount,
            'penugasan_count'    => $penugasanCount,
            'kinerja_summary'    => $kinerjaSummary,
            'readiness_checks'   => $readinessChecks,
        ];
    }

    /**
     * Menambah atau memperbarui profil dosen.
     *
     * @param array<string, mixed> $input
     * @param string               $actor
     * @param int|null             $dosenProfilId
     * @param bool                 $isSelfUpdate
     * @param bool                 $canEditExecutiveNotes
     * @return array<string, mixed>
     */
    public function saveDosenProfil(
        array $input,
        string $actor,
        ?int $dosenProfilId = null,
        bool $isSelfUpdate = false,
        bool $canEditExecutiveNotes = false
    ): array {
        $scholarUrl = trim((string) ($input['google_scholar_url'] ?? ''));
        if ($scholarUrl !== '') {
            $cleanUrl = ketarunaan_sanitize_external_url($scholarUrl, false);
            if ($cleanUrl === null) {
                return ['status' => false, 'message' => 'Tautan Google Scholar harus diawali http:// atau https://.'];
            }
            $scholarUrl = $cleanUrl;
        }

        $nidn   = trim((string) ($input['nidn'] ?? ''));
        $nuptk  = trim((string) ($input['nuptk'] ?? ''));
        $bidang = trim((string) ($input['bidang_keilmuan'] ?? ''));
        $tags   = trim((string) ($input['kepakaran_tags'] ?? ''));
        $sinta  = trim((string) ($input['sinta_id'] ?? ''));
        $scopus = trim((string) ($input['scopus_id'] ?? ''));

        if ($isSelfUpdate) {
            if ($dosenProfilId === null || $dosenProfilId <= 0) {
                return ['status' => false, 'message' => 'Profil dosen tidak ditemukan.'];
            }
            $this->profilModel->update($dosenProfilId, [
                'nidn'               => $nidn !== '' ? $nidn : null,
                'nuptk'              => $nuptk !== '' ? $nuptk : null,
                'bidang_keilmuan'    => $bidang !== '' ? $bidang : null,
                'kepakaran_tags'     => $tags !== '' ? $tags : null,
                'sinta_id'           => $sinta !== '' ? $sinta : null,
                'google_scholar_url' => $scholarUrl !== '' ? $scholarUrl : null,
                'scopus_id'          => $scopus !== '' ? $scopus : null,
                'updated_by'         => $actor,
            ]);
            return ['status' => true, 'id' => $dosenProfilId, 'message' => 'Profil akademik dan kepakaran Anda berhasil diperbarui.'];
        }

        $jafung = strtolower(trim((string) ($input['jabatan_fungsional'] ?? 'tenaga_pengajar')));
        if (!array_key_exists($jafung, ketarunaan_dosen_jafung_catalog())) {
            $jafung = 'tenaga_pengajar';
        }
        $pendidikan = trim((string) ($input['pendidikan_terakhir'] ?? 'S2'));
        if (!array_key_exists($pendidikan, ketarunaan_dosen_pendidikan_catalog())) {
            $pendidikan = 'S2';
        }
        $serdos = strtolower(trim((string) ($input['status_serdos'] ?? 'belum')));
        if (!array_key_exists($serdos, ketarunaan_dosen_serdos_catalog())) {
            $serdos = 'belum';
        }
        $ikatan = strtolower(trim((string) ($input['status_ikatan'] ?? 'dosen_tetap')));
        if (!array_key_exists($ikatan, ketarunaan_dosen_ikatan_catalog())) {
            $ikatan = 'dosen_tetap';
        }

        $homebaseId = (int) ($input['homebase_prodi_id'] ?? 0);
        $tahunSerd  = (int) ($input['tahun_serdos'] ?? 0);
        $tmtJafung  = trim((string) ($input['tmt_jabatan_fungsional'] ?? ''));

        $payload = [
            'nidn'                    => $nidn !== '' ? $nidn : null,
            'nuptk'                   => $nuptk !== '' ? $nuptk : null,
            'homebase_prodi_id'       => $homebaseId > 0 ? $homebaseId : null,
            'jabatan_fungsional'      => $jafung,
            'tmt_jabatan_fungsional'  => $tmtJafung !== '' ? $tmtJafung : null,
            'pendidikan_terakhir'     => $pendidikan,
            'bidang_keilmuan'         => $bidang !== '' ? $bidang : null,
            'kepakaran_tags'          => $tags !== '' ? $tags : null,
            'status_serdos'           => $serdos,
            'tahun_serdos'            => ($tahunSerd >= 1990 && $tahunSerd <= (int) date('Y') + 1) ? $tahunSerd : null,
            'nomor_sertifikat_serdos' => trim((string) ($input['nomor_sertifikat_serdos'] ?? '')) ?: null,
            'status_ikatan'           => $ikatan,
            'is_siap_ditugaskan'      => !empty($input['is_siap_ditugaskan']) ? 1 : 0,
            'sinta_id'                => $sinta !== '' ? $sinta : null,
            'google_scholar_url'      => $scholarUrl !== '' ? $scholarUrl : null,
            'scopus_id'               => $scopus !== '' ? $scopus : null,
            'updated_by'              => $actor,
        ];

        if ($canEditExecutiveNotes && array_key_exists('catatan_pimpinan', $input)) {
            $payload['catatan_pimpinan'] = trim((string) $input['catatan_pimpinan']) ?: null;
        }

        try {
            if ($dosenProfilId !== null && $dosenProfilId > 0) {
                $existing = $this->profilModel->find($dosenProfilId);
                if (!$existing) {
                    return ['status' => false, 'message' => 'Data profil dosen tidak ditemukan.'];
                }
                $this->profilModel->update($dosenProfilId, $payload);
                return ['status' => true, 'id' => $dosenProfilId, 'message' => 'Profil dosen berhasil diperbarui.'];
            }

            $userId = (int) ($input['user_id'] ?? 0);
            if ($userId <= 0) {
                return ['status' => false, 'message' => 'Pilih pegawai terlebih dahulu.'];
            }
            $userRow = $this->db->table('users')
                ->select('id, role')
                ->where('id', $userId)
                ->whereNotIn('role', ['taruna', 'alumni'])
                ->get()
                ->getRowArray();
            if (!$userRow) {
                return ['status' => false, 'message' => 'Pegawai yang dipilih tidak valid.'];
            }
            if ($this->profilModel->where('user_id', $userId)->first()) {
                return ['status' => false, 'message' => 'Pegawai ini sudah terdaftar pada Data Dosen.'];
            }

            $payload['user_id'] = $userId;
            $newId              = (int) $this->profilModel->insert($payload, true);
            $this->ensureDosenRoleInUserRoles($userId);
            return ['status' => true, 'id' => $newId, 'message' => 'Data dosen berhasil ditambahkan.'];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'Gagal menyimpan profil dosen. Silakan coba lagi.'];
        }
    }

    /**
     * Menyimpan catatan pertimbangan strategis pimpinan dan indikator kesiapan penugasan.
     */
    public function saveCatatanPimpinan(int $dosenProfilId, string $catatan, bool $isSiapDitugaskan, string $actor): array
    {
        $existing = $this->profilModel->find($dosenProfilId);
        if (!$existing) {
            return ['status' => false, 'message' => 'Data dosen tidak ditemukan.'];
        }

        $this->profilModel->update($dosenProfilId, [
            'catatan_pimpinan'   => trim($catatan) ?: null,
            'is_siap_ditugaskan' => $isSiapDitugaskan ? 1 : 0,
            'updated_by'         => $actor,
        ]);

        return ['status' => true, 'message' => 'Catatan pimpinan dan status kesiapan berhasil disimpan.'];
    }

    /**
     * Menambah atau mengubah 1 riwayat kualifikasi / sertifikasi / penugasan strategis dosen.
     */
    public function saveKualifikasi(
        int $dosenProfilId,
        array $input,
        string $actor,
        ?int $kualifikasiId = null,
        bool $isSelfUpdate = false
    ): array {
        if (!$this->profilModel->find($dosenProfilId)) {
            return ['status' => false, 'message' => 'Data dosen tidak ditemukan.'];
        }

        $kategori = strtolower(trim((string) ($input['kategori'] ?? 'pendidikan')));
        if (!array_key_exists($kategori, ketarunaan_dosen_kualifikasi_catalog())) {
            return ['status' => false, 'message' => 'Kategori kualifikasi tidak valid.'];
        }

        $judul = trim((string) ($input['judul'] ?? ''));
        if ($judul === '') {
            return ['status' => false, 'message' => 'Judul kualifikasi atau kegiatan wajib diisi.'];
        }

        $tautan = trim((string) ($input['tautan_bukti'] ?? ''));
        if ($tautan !== '') {
            $cleanUrl = ketarunaan_sanitize_external_url($tautan, false);
            if ($cleanUrl === null) {
                return ['status' => false, 'message' => 'Tautan bukti harus diawali http:// atau https://.'];
            }
            $tautan = $cleanUrl;
        }

        $thnMulai   = (int) ($input['tahun_mulai'] ?? 0);
        $thnSelesai = (int) ($input['tahun_selesai'] ?? 0);

        $payload = [
            'dosen_profil_id'         => $dosenProfilId,
            'kategori'                => $kategori,
            'judul'                   => $judul,
            'institusi_penyelenggara' => trim((string) ($input['institusi_penyelenggara'] ?? '')) ?: null,
            'tahun_mulai'             => ($thnMulai >= 1970 && $thnMulai <= 2100) ? $thnMulai : null,
            'tahun_selesai'           => ($thnSelesai >= 1970 && $thnSelesai <= 2100) ? $thnSelesai : null,
            'nomor_dokumen'           => trim((string) ($input['nomor_dokumen'] ?? '')) ?: null,
            'tautan_bukti'            => $tautan !== '' ? $tautan : null,
            'keterangan'              => trim((string) ($input['keterangan'] ?? '')) ?: null,
            'status_verifikasi'       => $isSelfUpdate ? 'menunggu' : 'terverifikasi',
            'created_by'              => $actor,
        ];

        if ($kualifikasiId !== null && $kualifikasiId > 0) {
            $existing = $this->kualifikasiModel->find($kualifikasiId);
            if (!$existing || (int) $existing['dosen_profil_id'] !== $dosenProfilId) {
                return ['status' => false, 'message' => 'Riwayat kualifikasi tidak ditemukan.'];
            }
            $this->kualifikasiModel->update($kualifikasiId, $payload);
            return ['status' => true, 'id' => $kualifikasiId, 'message' => 'Riwayat kualifikasi berhasil diperbarui.'];
        }

        $newId = (int) $this->kualifikasiModel->insert($payload, true);
        return [
            'status'  => true,
            'id'      => $newId,
            'message' => $isSelfUpdate
                ? 'Usulan riwayat kualifikasi berhasil dikirim untuk diverifikasi pengelola.'
                : 'Riwayat kualifikasi berhasil ditambahkan.',
        ];
    }

    /**
     * Menghapus 1 riwayat kualifikasi dosen.
     */
    public function deleteKualifikasi(int $dosenProfilId, int $kualifikasiId): array
    {
        $existing = $this->kualifikasiModel->find($kualifikasiId);
        if (!$existing || (int) $existing['dosen_profil_id'] !== $dosenProfilId) {
            return ['status' => false, 'message' => 'Riwayat kualifikasi tidak ditemukan.'];
        }

        $this->kualifikasiModel->delete($kualifikasiId);
        return ['status' => true, 'message' => 'Riwayat kualifikasi berhasil dihapus.'];
    }

    /**
     * Memverifikasi usulan riwayat kualifikasi yang diajukan dosen.
     */
    public function verifyKualifikasi(int $dosenProfilId, int $kualifikasiId): array
    {
        $existing = $this->kualifikasiModel->find($kualifikasiId);
        if (!$existing || (int) $existing['dosen_profil_id'] !== $dosenProfilId) {
            return ['status' => false, 'message' => 'Riwayat kualifikasi tidak ditemukan.'];
        }

        $this->kualifikasiModel->update($kualifikasiId, ['status_verifikasi' => 'terverifikasi']);
        return ['status' => true, 'message' => 'Riwayat kualifikasi telah diverifikasi.'];
    }

    /**
     * Baris ekspor CSV direktori Data Dosen untuk kebutuhan pimpinan / akreditasi.
     *
     * @param array<string, mixed> $filters
     * @return array<int, array<int, string>>
     */
    public function getExportRows(array $filters = []): array
    {
        $data    = $this->getDirectoryData($filters);
        $jafMap  = ketarunaan_dosen_jafung_catalog();
        $pendMap = ketarunaan_dosen_pendidikan_catalog();
        $serdMap = ketarunaan_dosen_serdos_catalog();
        $ikatMap = ketarunaan_dosen_ikatan_catalog();

        $csv = [[
            'Nama Lengkap',
            'NIP',
            'NIDN',
            'NUPTK',
            'Pangkat / Golongan',
            'Homebase Prodi',
            'Unit Kerja',
            'Jabatan Fungsional',
            'Pendidikan Terakhir',
            'Bidang Keilmuan',
            'Kata Kunci Kepakaran',
            'Status Serdos',
            'Status Ikatan',
            'Kesiapan Penugasan',
            'Sertifikasi Kompetensi',
            'Email',
            'No. HP',
        ]];

        foreach ($data['rows'] as $r) {
            $csv[] = [
                (string) ($r['nama_lengkap'] ?? ''),
                (string) ($r['nip'] ?? ''),
                (string) ($r['nidn'] ?? ''),
                (string) ($r['nuptk'] ?? ''),
                (string) ($r['pangkat'] ?? ''),
                (string) ($r['kode_prodi'] ?: '-'),
                (string) ($r['unit'] ?? ''),
                (string) ($jafMap[$r['jabatan_fungsional'] ?? '']['label'] ?? $r['jabatan_fungsional'] ?? ''),
                (string) ($pendMap[$r['pendidikan_terakhir'] ?? ''] ?? $r['pendidikan_terakhir'] ?? ''),
                (string) ($r['bidang_keilmuan'] ?? ''),
                (string) ($r['kepakaran_tags'] ?? ''),
                (string) ($serdMap[$r['status_serdos'] ?? '']['label'] ?? $r['status_serdos'] ?? ''),
                (string) ($ikatMap[$r['status_ikatan'] ?? '']['label'] ?? $r['status_ikatan'] ?? ''),
                (int) ($r['is_siap_ditugaskan'] ?? 0) === 1 ? 'Siap Ditugaskan' : 'Belum Siap / Tugas Belajar',
                (string) ((int) ($r['jumlah_sertifikasi'] ?? 0)),
                (string) ($r['email'] ?? ''),
                (string) ($r['no_hp'] ?? ''),
            ];
        }

        return $csv;
    }

    /**
     * Memecah kepakaran_tags menjadi array rapi.
     *
     * @return array<int, string>
     */
    private function parseTags(string $raw): array
    {
        if (trim($raw) === '') {
            return [];
        }
        $parts = preg_split('/[,;\n]+/', $raw) ?: [];
        $out   = [];
        foreach ($parts as $p) {
            $clean = trim($p);
            if ($clean !== '' && !in_array($clean, $out, true)) {
                $out[] = $clean;
            }
        }
        return $out;
    }

    /**
     * Peta kode program studi aktif untuk deteksi otomatis homebase awal dosen.
     *
     * @return array<string, int>
     */
    private function getProdiCodeMap(): array
    {
        $map = [];
        if (!$this->db->tableExists('ref_program_studi')) {
            return $map;
        }
        foreach ($this->db->table('ref_program_studi')->select('id, kode_prodi')->get()->getResultArray() as $pr) {
            $map[strtoupper(trim((string) $pr['kode_prodi']))] = (int) $pr['id'];
        }
        return $map;
    }

    /**
     * Inferensi awal atribut dosen_profil dari data jabatan, unit kerja, dan status pegawai.
     *
     * @param array<string, mixed> $userRow
     * @param array<string, int>   $prodiMap
     * @return array<string, mixed>
     */
    private function inferInitialProfileDefaults(array $userRow, array $prodiMap): array
    {
        $jabLow  = strtolower((string) ($userRow['jabatan'] ?? ''));
        $unitUp  = strtoupper((string) ($userRow['unit'] ?? ''));
        $roleLow = strtolower((string) ($userRow['role'] ?? ''));

        $jafung = 'tenaga_pengajar';
        if (strpos($jabLow, 'guru besar') !== false) {
            $jafung = 'guru_besar';
        } elseif (strpos($jabLow, 'lektor kepala') !== false) {
            $jafung = 'lektor_kepala';
        } elseif (strpos($jabLow, 'lektor') !== false) {
            $jafung = 'lektor';
        } elseif (strpos($jabLow, 'asisten ahli') !== false) {
            $jafung = 'asisten_ahli';
        }

        $homebaseId = null;
        if (strpos($unitUp, 'RSTJ') !== false && isset($prodiMap['RSTJ'])) {
            $homebaseId = $prodiMap['RSTJ'];
        } elseif (strpos($unitUp, 'TRO') !== false && isset($prodiMap['TRO'])) {
            $homebaseId = $prodiMap['TRO'];
        } elseif ((strpos($unitUp, 'PRODI TO') !== false || strpos($unitUp, 'OTOMOTIF') !== false) && isset($prodiMap['TO'])) {
            $homebaseId = $prodiMap['TO'];
        }

        $isTubel = ($roleLow === 'tugas_belajar' || strpos($jabLow, 'tugas belajar') !== false);
        $isAktif = (strtolower((string) ($userRow['status_pegawai'] ?? 'aktif')) === 'aktif');

        return [
            'homebase_prodi_id'   => $homebaseId,
            'jabatan_fungsional'  => $jafung,
            'pendidikan_terakhir' => 'S2',
            'status_serdos'       => 'belum',
            'status_ikatan'       => $isTubel ? 'tugas_belajar' : 'dosen_tetap',
            'is_siap_ditugaskan'  => ($isAktif && !$isTubel) ? 1 : 0,
        ];
    }

    /**
     * Memastikan peran sekunder 'dosen' tercatat pada tabel user_roles (idempoten).
     */
    private function ensureDosenRoleInUserRoles(int $userId): void
    {
        if ($userId <= 0 || !$this->db->tableExists('user_roles')) {
            return;
        }

        $exists = $this->db->table('user_roles')
            ->select('id')
            ->where('user_id', $userId)
            ->where('role_name', 'dosen')
            ->get()
            ->getRowArray();

        if (!$exists) {
            $this->db->table('user_roles')->insert([
                'user_id'    => $userId,
                'role_name'  => 'dosen',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
