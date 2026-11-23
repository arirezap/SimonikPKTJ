<?php

namespace App\Libraries\Ketarunaan;

/**
 * Policy terpusat untuk otorisasi modul ECC 2.0 (Ketarunaan & Alumni).
 * Mematuhi prinsip Single Responsibility (SRP) dan Privacy by Design.
 */
class KetarunaanPolicy
{
    /**
     * Daftar peran yang diizinkan mengakses modul Ketarunaan & Alumni.
     */
    public const ALLOWED_MODULE_ROLES = [
        'admin',
        'direktur',
        'wadir',
        'kabag',
        'kabag_aak',
        'kabag_kuk',
        'manajemen',
        'ketarunaan',
        'ketarunaan_validator',
        'pusbangkar',
        'karir_alumni',
        'kerjasama',
        'kepegawaian',
        'taruna',
        'alumni',
    ];

    /**
     * Periksa apakah pengguna saat ini berhak mengakses modul Ketarunaan.
     *
     * @return bool
     */
    public static function canAccessModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole(self::ALLOWED_MODULE_ROLES);
    }

    /**
     * Periksa apakah pengguna berhak membuka Dashboard Ringkasan internal ECC 2.0.
     * Akun layanan mandiri (taruna/alumni) diarahkan ke portal miliknya sendiri.
     *
     * @return bool
     */
    public static function canAccessDashboard(): bool
    {
        return self::canAccessModule() && !self::isSelfAccountOnly();
    }

    /**
     * Periksa apakah pengguna berhak menambah/mengubah data taruna atau mengajukan perubahan status.
     *
     * @return bool
     */
    public static function canManageTaruna(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'kabag_aak',
            'manajemen',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak melakukan pengesahan/validasi resmi.
     *
     * @return bool
     */
    public static function canValidate(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan_validator',
            'pusbangkar',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak melihat data identitas sensitif (NIK penuh & kontak wali).
     * Sesuai PRD & aturan privasi: hanya petugas ketarunaan berwenang, validator, pusbangkar, admin,
     * atau pemilik data itu sendiri.
     *
     * @param string|null $nomorTaruna
     * @return bool
     */
    public static function canViewSensitiveData(?string $nomorTaruna = null): bool
    {
        if (function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'kabag_aak',
            'direktur',
        ])) {
            return true;
        }

        if ($nomorTaruna !== null && self::isSelfTarunaOnly() && self::isOwnNomorTaruna($nomorTaruna)) {
            return true;
        }

        return false;
    }

    /**
     * Periksa apakah pengguna berhak melihat catatan pembinaan/pelanggaran disiplin pada rekam jejak.
     *
     * @param string|null $nomorTaruna
     * @return bool
     */
    public static function canViewDisciplinaryRecord(?string $nomorTaruna = null): bool
    {
        if (function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'pusbangkar',
            'ketarunaan_validator',
        ])) {
            return true;
        }

        if ($nomorTaruna !== null && self::isSelfTarunaOnly() && self::isOwnNomorTaruna($nomorTaruna)) {
            return true;
        }

        return false;
    }

    /**
     * Periksa apakah pengguna berhak mengunduh/melihat dokumen berstatus Terbatas (mis. SK Beasiswa / BA Disiplin).
     *
     * @param string      $modulSumber
     * @param string|null $nomorTaruna
     * @return bool
     */
    public static function canViewRestrictedDocument(string $modulSumber, ?string $nomorTaruna = null): bool
    {
        $modul = strtolower(trim($modulSumber));

        if ($modul === 'pelanggaran') {
            return self::canViewDisciplinaryRecord($nomorTaruna);
        }

        if ($modul === 'beasiswa') {
            return self::canViewBeasiswaEconomy($nomorTaruna);
        }

        if ($modul === 'lowongan') {
            return self::canViewInternalLowonganProof();
        }

        return self::canAccessModule();
    }

    /**
     * Periksa apakah pengguna berhak mengakses modul Pelanggaran Taruna.
     * Hanya peran pimpinan, ketarunaan, validator, pusbangkar, dan admin.
     *
     * @return bool
     */
    public static function canAccessPelanggaranModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak mencatat dugaan pelanggaran dan mengelola pemeriksaan kasus.
     *
     * @return bool
     */
    public static function canManagePelanggaran(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'pusbangkar',
            'ketarunaan_validator',
            'ketarunaan',
            'manajemen',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak menetapkan sanksi/keputusan atau memvalidasi pencatatan kasus.
     *
     * @return bool
     */
    public static function canDecidePelanggaran(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'pusbangkar',
            'ketarunaan_validator',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak mengakses modul Beasiswa Taruna.
     *
     * @return bool
     */
    public static function canAccessBeasiswaModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'taruna',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak mengelola program beasiswa, impor IP, pemeringkatan, dan pengajuan atas nama taruna.
     *
     * @return bool
     */
    public static function canManageBeasiswa(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'ketarunaan',
            'ketarunaan_validator',
            'kabag_aak',
        ]);
    }

    /**
     * Periksa apakah pengguna dapat mengajukan klaim beasiswa (operator atau taruna pemilik akun).
     *
     * @return bool
     */
    public static function canSubmitBeasiswaClaim(): bool
    {
        return self::canManageBeasiswa() || (function_exists('hasRole') && hasRole('taruna'));
    }

    /**
     * Periksa apakah pengguna berhak melakukan pemeriksaan kelayakan & berkas klaim beasiswa.
     *
     * @return bool
     */
    public static function canVerifyBeasiswa(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak menetapkan penerima, mengesahkan validasi SK, atau membatalkan penetapan.
     *
     * @return bool
     */
    public static function canDecideBeasiswa(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan_validator',
        ]);
    }

    /**
     * Periksa hak akses terhadap rincian ekonomi & dokumen sensitif beasiswa (UAT-13 & NFR-02).
     *
     * @param string|null $nomorTaruna
     * @return bool
     */
    public static function canViewBeasiswaEconomy(?string $nomorTaruna = null): bool
    {
        if (function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
        ])) {
            return true;
        }

        if ($nomorTaruna !== null && self::isSelfTarunaOnly() && self::isOwnNomorTaruna($nomorTaruna)) {
            return true;
        }

        return false;
    }

    /**
     * Periksa apakah pengguna berhak mengakses direktori Lowongan Pekerjaan.
     *
     * @return bool
     */
    public static function canAccessLowonganModule(): bool
    {
        return self::canAccessModule();
    }

    /**
     * Periksa apakah pengguna berhak mengelola data lowongan, perusahaan mitra, dan dokumen sumber.
     *
     * @return bool
     */
    public static function canManageLowongan(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kerjasama',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak memvalidasi kelengkapan lowongan dan kontak perusahaan mitra.
     *
     * @return bool
     */
    public static function canValidateLowongan(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan_validator',
            'pusbangkar',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak menerbitkan atau menutup lowongan yang sudah tervalidasi.
     *
     * @return bool
     */
    public static function canPublishLowongan(): bool
    {
        return self::canManageLowongan();
    }

    /**
     * Periksa hak akses terhadap dokumen bukti internal & sumber konfirmasi lowongan (LOW-03 & NFR-02).
     * Alumni, taruna, dan pihak non-pengelola dilarang melihat bukti konfirmasi internal.
     *
     * @return bool
     */
    public static function canViewInternalLowonganProof(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kerjasama',
        ]);
    }

    /**
     * Akses Modul 7: Alumni — Profil dasar (ALM-01..03).
     * Taruna aktif dibatasi agar tidak mengakses direktori data pribadi alumni.
     */
    public static function canAccessAlumniModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kepegawaian',
            'alumni',
        ]);
    }

    /**
     * Hak mengelola direktori alumni, mencatat pembaruan kontak, dan mengajukan koreksi/kelulusan.
     */
    public static function canManageAlumni(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'pusbangkar',
            'karir_alumni',
        ]);
    }

    /**
     * Hak memutuskan validasi kelulusan dan menyetujui/menolak koreksi kelulusan terkendali (ALM-01, ALM-03).
     */
    public static function canValidateAlumni(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan_validator',
        ]);
    }

    /**
     * Daftar peran staf/pimpinan internal PKTJ yang bukan akun layanan mandiri.
     */
    private const STAFF_OR_LEADERSHIP_ROLES = [
        'admin',
        'direktur',
        'wadir',
        'kabag',
        'kabag_aak',
        'kabag_kuk',
        'manajemen',
        'ketarunaan',
        'ketarunaan_validator',
        'pusbangkar',
        'karir_alumni',
        'kerjasama',
        'kepegawaian',
    ];

    /**
     * Hak akses membuka Modul Tracer Study (pimpinan, pengelola, manajemen/kerjasama, serta alumni mandiri).
     * Akun taruna aktif secara eksplisit ditolak.
     */
    public static function canAccessTracerStudyModule(): bool
    {
        if (self::isSelfTarunaOnly()) {
            return false;
        }
        if (self::isSelfAlumniOnly()) {
            return true;
        }
        if (self::canManageTracerStudy()) {
            return true;
        }

        return function_exists('hasAnyRole') && hasAnyRole([
            'wadir',
            'kabag_kuk',
            'manajemen',
            'kerjasama',
            'kepegawaian',
        ]);
    }

    /**
     * Hak memantau progres, analitik, dan hasil Tracer Study setiap periode pembukaan.
     */
    public static function canViewTracerStudyAnalytics(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }

        return self::canAccessTracerStudyModule();
    }

    /**
     * Hak PIC Alumni & Tracer Study (Tim Ketarunaan & Alumni serta pimpinan terkait).
     */
    public static function canManageTracerStudy(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
            'karir_alumni',
        ]);
    }

    /**
     * Hak pegawai berwenang untuk membuka, menutup, atau mengembalikan periode Tracer Study ke draf.
     */
    public static function canToggleTracerStudyPeriode(): bool
    {
        return self::canManageTracerStudy();
    }

    /**
     * Hak melihat direktori Data Dosen (Executive Profiling untuk pengambilan keputusan pimpinan).
     * Diblokir penuh bagi akun taruna & alumni.
     */
    public static function canViewDataDosenDirectory(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }

        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'kepegawaian',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
        ]);
    }

    /**
     * Hak mengakses modul Data Dosen (direktori pimpinan/pengelola ATAU pemilik peran sekunder dosen).
     */
    public static function canAccessDataDosenModule(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }
        if (self::canViewDataDosenDirectory()) {
            return true;
        }
        if (function_exists('hasRole') && hasRole('dosen')) {
            return true;
        }

        return self::resolveOwnDosenProfilId() !== null;
    }

    /**
     * Hak mengelola (tambah/ubah/sinkron/verifikasi kualifikasi) seluruh data profil dosen.
     */
    public static function canManageDataDosen(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }

        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'kepegawaian',
        ]);
    }

    /**
     * Hak melihat Catatan Pimpinan & ringkasan indeks kinerja ECC pada kartu profil eksekutif dosen.
     */
    public static function canViewDosenExecutiveNotes(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }

        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'kepegawaian',
        ]);
    }

    /**
     * Hak menulis atau mengubah Catatan Pimpinan dan status kesiapan penugasan dosen.
     */
    public static function canEditDosenExecutiveNotes(): bool
    {
        if (self::isSelfAccountOnly()) {
            return false;
        }

        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
        ]);
    }

    /**
     * Mengambil ID dosen_profil milik pengguna yang sedang login (jika pegawai adalah dosen).
     */
    public static function resolveOwnDosenProfilId(): ?int
    {
        $userId = (int) (session()->get('id') ?? session()->get('user_id') ?? 0);
        if ($userId <= 0 || self::isSelfAccountOnly()) {
            return null;
        }

        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('dosen_profil')) {
                return null;
            }
            $row = $db->table('dosen_profil')->select('id')->where('user_id', $userId)->get()->getRowArray();
            if ($row) {
                return (int) $row['id'];
            }

            // Jika pegawai memegang peran sekunder 'dosen' namun baris dosen_profil belum ada, siapkan otomatis
            if (function_exists('hasRole') && hasRole('dosen')) {
                $actor = (string) (session()->get('nama_lengkap') ?: session()->get('username') ?: 'system');
                return (new DataDosenService($db))->ensureProfilForUser($userId, $actor);
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Hak mengelola kemitraan & membagikan tautan/survei ke mitra pengguna lulusan (Tim Kerjasama & Karir Alumni).
     */
    public static function canShareToMitraPenggunaLulusan(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'kerjasama',
            'ketarunaan',
            'ketarunaan_validator',
            'karir_alumni',
        ]);
    }

    /**
     * Menentukan apakah akun staf hanya memegang peran kerjasama tanpa peran pembinaan/akademik lain.
     */
    private static function isPureKerjasamaOnly(): bool
    {
        if (!function_exists('hasRole') || !hasRole('kerjasama')) {
            return false;
        }

        return !hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kepegawaian',
        ]);
    }

    /**
     * Menentukan apakah pengguna memiliki salah satu peran staf atau pimpinan PKTJ.
     */
    public static function hasStaffOrLeadershipRole(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole(self::STAFF_OR_LEADERSHIP_ROLES);
    }

    /**
     * Penanda akun taruna mandiri yang hanya berhak melihat/mengajukan data miliknya sendiri.
     */
    public static function isSelfTarunaOnly(): bool
    {
        if (!function_exists('hasRole') || !hasRole('taruna')) {
            return false;
        }

        return !self::hasStaffOrLeadershipRole();
    }

    /**
     * Penanda akun alumni mandiri yang hanya boleh melihat dan memperbarui kontaknya sendiri.
     */
    public static function isSelfAlumniOnly(): bool
    {
        if (!function_exists('hasRole') || !hasRole('alumni')) {
            return false;
        }

        return !self::hasStaffOrLeadershipRole();
    }

    /**
     * Penanda bahwa akun merupakan pengguna layanan mandiri (taruna atau alumni tanpa peran staf).
     */
    public static function isSelfAccountOnly(): bool
    {
        return self::isSelfTarunaOnly() || self::isSelfAlumniOnly();
    }

    /**
     * Tentukan URL beranda portal resmi untuk akun layanan mandiri (taruna / alumni).
     */
    public static function getSelfPortalUrl(): string
    {
        if (self::isSelfTarunaOnly()) {
            $ownNomor = self::resolveSelfNomorTaruna();
            return $ownNomor !== null
                ? '/ketarunaan/taruna/profil/' . urlencode($ownNomor)
                : '/ketarunaan/taruna';
        }

        if (self::isSelfAlumniOnly()) {
            $ownAlumni = self::resolveSelfAlumniNomorTaruna();
            return $ownAlumni !== null
                ? '/ketarunaan/alumni/detail/' . urlencode($ownAlumni)
                : '/ketarunaan/alumni';
        }

        return '/dashboard';
    }

    /**
     * Mengumpulkan kandidat Nomor Taruna dari sesi login aktif.
     *
     * @return array<int, string>
     */
    private static function getSessionNomorCandidates(): array
    {
        $session = session();
        return array_values(array_unique(array_filter([
            trim((string) ($session->get('nomor_taruna') ?? '')),
            trim((string) ($session->get('username') ?? '')),
            trim((string) ($session->get('nip') ?? '')),
        ], static fn (string $v): bool => $v !== '' && $v !== '-')));
    }

    /**
     * Resolusi Nomor Taruna milik akun taruna/alumni aktif dari master_taruna atau alumni_profil.
     */
    public static function resolveSelfNomorTaruna(): ?string
    {
        $candidates = self::getSessionNomorCandidates();
        if (empty($candidates)) {
            return null;
        }

        try {
            $db = \Config\Database::connect();
            $row = $db->table('master_taruna')
                ->select('nomor_taruna')
                ->whereIn('nomor_taruna', $candidates)
                ->get()
                ->getRowArray();
            if ($row && !empty($row['nomor_taruna'])) {
                return (string) $row['nomor_taruna'];
            }
        } catch (\Throwable $e) {
            // Abaikan bila tabel belum siap pada lingkungan tertentu
        }

        return self::resolveSelfAlumniNomorTaruna();
    }

    /**
     * Memeriksa apakah Nomor Taruna target merupakan milik akun yang sedang login.
     */
    public static function isOwnNomorTaruna(?string $nomorTaruna): bool
    {
        $target = trim((string) ($nomorTaruna ?? ''));
        if ($target === '') {
            return false;
        }

        $ownNomor = self::resolveSelfNomorTaruna();
        if ($ownNomor !== null && strcasecmp(trim($ownNomor), $target) === 0) {
            return true;
        }

        foreach (self::getSessionNomorCandidates() as $cand) {
            if (strcasecmp($cand, $target) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolusi Nomor Taruna milik akun alumni aktif dari sesi login atau kolom users terkait.
     */
    public static function resolveSelfAlumniNomorTaruna(): ?string
    {
        $candidates = self::getSessionNomorCandidates();

        if (empty($candidates)) {
            return null;
        }

        try {
            $db = \Config\Database::connect();
            $row = $db->table('alumni_profil')
                ->select('nomor_taruna')
                ->whereIn('nomor_taruna', $candidates)
                ->get()
                ->getRowArray();
            if ($row && !empty($row['nomor_taruna'])) {
                return (string) $row['nomor_taruna'];
            }
        } catch (\Throwable $e) {
            // Abaikan bila tabel belum siap pada lingkungan tertentu
        }

        return null;
    }

    /**
     * Memastikan pembaruan kontak hanya dilakukan pengelola resmi atau pemilik profil alumni itu sendiri (ALM-02).
     */
    public static function canUpdateOwnAlumniContact(?string $nomorTaruna): bool
    {
        if (self::canManageAlumni()) {
            return true;
        }

        if (self::isSelfAlumniOnly() && $nomorTaruna !== null) {
            $ownNomor = self::resolveSelfAlumniNomorTaruna();
            return $ownNomor !== null && strcasecmp(trim($ownNomor), trim($nomorTaruna)) === 0;
        }

        return false;
    }

    /**
     * Memastikan pembaruan profil pribadi taruna hanya dilakukan oleh akun taruna pemilik Nomor Taruna tersebut.
     */
    public static function canUpdateOwnTarunaProfile(?string $nomorTaruna): bool
    {
        return self::isSelfTarunaOnly() && self::isOwnNomorTaruna($nomorTaruna);
    }

    /**
     * Hak mengajukan data prestasi baru (pengelola atau akun taruna layanan mandiri).
     */
    public static function canSubmitPrestasiMandiri(): bool
    {
        return self::canManageTaruna() || self::isSelfTarunaOnly();
    }

    /**
     * Hak menyunting, melengkapi peserta/dokumen, atau menghapus draf prestasi.
     * Akun taruna hanya boleh mengubah prestasi yang diikuti ketika status masih draf atau perlu perbaikan.
     *
     * @param array<string, mixed> $detail Struktur detail prestasi dari PrestasiService::getDetail()
     */
    public static function canEditPrestasi(array $detail): bool
    {
        if (self::canManageTaruna()) {
            return true;
        }

        if (!self::isSelfTarunaOnly()) {
            return false;
        }

        $status = (string) ($detail['prestasi']['status_validasi'] ?? '');
        if (!in_array($status, ['draf', 'perlu_perbaikan'], true)) {
            return false;
        }

        foreach (($detail['peserta'] ?? []) as $peserta) {
            if (self::isOwnNomorTaruna((string) ($peserta['nomor_taruna'] ?? ''))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hak mengajukan capaian rekam jejak baru (pengelola atau taruna pemilik Nomor Taruna).
     */
    public static function canSubmitCapaianMandiri(?string $nomorTaruna = null): bool
    {
        if (self::canManageTaruna()) {
            return true;
        }

        if (!self::isSelfTarunaOnly()) {
            return false;
        }

        if ($nomorTaruna === null || trim($nomorTaruna) === '') {
            return self::resolveSelfNomorTaruna() !== null;
        }

        return self::isOwnNomorTaruna($nomorTaruna);
    }

    /**
     * Hak menyunting capaian rekam jejak; taruna hanya boleh mengubah miliknya yang berstatus draf atau perlu perbaikan.
     *
     * @param array<string, mixed> $capaian
     */
    public static function canEditCapaian(array $capaian): bool
    {
        if (self::canManageTaruna()) {
            return true;
        }

        if (!self::isSelfTarunaOnly()) {
            return false;
        }

        $status = (string) ($capaian['status_validasi'] ?? '');

        return in_array($status, ['draf', 'perlu_perbaikan'], true)
            && self::isOwnNomorTaruna((string) ($capaian['nomor_taruna'] ?? ''));
    }

    /**
     * Akses Modul 8.1: Pusat Antrean Validasi Data Lintas Modul (GOV-01..04).
     */
    public static function canAccessValidasiModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kepegawaian',
        ]);
    }

    /**
     * Akses Modul 8.2: Laporan Resmi & Ekspor Lintas Modul (GOV-01, NFR-02).
     */
    public static function canAccessLaporanModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'pusbangkar',
            'karir_alumni',
            'kerjasama',
            'kepegawaian',
        ]);
    }

    /**
     * Periksa apakah pengguna berhak melihat pratinjau/ekspor jenis laporan tertentu (RBAC per modul).
     */
    public static function canExportLaporanJenis(string $jenis): bool
    {
        if (!self::canAccessLaporanModule()) {
            return false;
        }

        return match (strtolower(trim($jenis))) {
            'status_pendidikan', 'prestasi' => !self::isPureKerjasamaOnly(),
            'lowongan'                      => self::canAccessLowonganModule(),
            'pelanggaran'                   => self::canAccessPelanggaranModule(),
            'beasiswa'                      => self::canAccessBeasiswaModule(),
            'alumni'                        => self::canAccessAlumniModule(),
            default                         => false,
        };
    }

    /**
     * Akses Modul 8.3: Pengaturan Data Rujukan, Matriks Peran & Parameter Operasional (GOV-05..07).
     */
    public static function canAccessPengaturanModule(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'wadir',
            'kabag',
            'kabag_aak',
            'kabag_kuk',
            'manajemen',
            'ketarunaan',
            'ketarunaan_validator',
            'kepegawaian',
        ]);
    }

    /**
     * Hak mengubah data rujukan (prodi, angkatan, tahun akademik, kelas, wilayah 3TP) secara non-destruktif (GOV-05).
     */
    public static function canManageReferensi(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan',
            'ketarunaan_validator',
        ]);
    }

    /**
     * Hak mengubah parameter operasional ECC 2.0 seperti penanda Data Simulasi/Operasional (NFR-09).
     */
    public static function canManageOperationalSettings(): bool
    {
        return function_exists('hasAnyRole') && hasAnyRole([
            'admin',
            'direktur',
            'kabag',
            'kabag_aak',
            'ketarunaan_validator',
        ]);
    }
}

