<?php

namespace App\Libraries;

use App\Models\SettingModel;
use App\Models\SurveiDelegasiModel;
use App\Models\SurveiResponModel;
use App\Models\UnitKerja;
use App\Models\User;

class SurveiPemetaanPolicy
{
    /**
     * Cache per siklus request untuk menghindari kueri berulang pada sidebar & controller.
     *
     * @var array<int, array|null>
     */
    protected static array $delegationCache = [];

    /**
     * Ambil ID pengguna aktif dari sesi dengan normalisasi defensif.
     */
    public static function getCurrentUserId(): int
    {
        return (int) (session()->get('id') ?? session()->get('user_id') ?? 0);
    }

    /**
     * Cek apakah pengguna adalah pengelola utama survei (Kabag KUK, Direktur, atau Admin).
     */
    public static function canManageSurvey(): bool
    {
        if (!session()->get('isLoggedIn')) {
            return false;
        }
        helper('role');
        return hasAnyRole(['kabag_kuk', 'direktur', 'admin']);
    }

    /**
     * Ambil catatan delegasi aktif untuk user_id tertentu (dengan request-level cache).
     */
    public static function getActiveDelegation(?int $userId = null): ?array
    {
        $uid = $userId ?? self::getCurrentUserId();
        if ($uid <= 0) {
            return null;
        }

        if (array_key_exists($uid, self::$delegationCache)) {
            return self::$delegationCache[$uid];
        }

        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('survei_pemetaan_delegasi')) {
                self::$delegationCache[$uid] = null;
                return null;
            }

            $model = new SurveiDelegasiModel();
            $row   = $model->where('user_id', $uid)
                           ->where('is_active', 1)
                           ->first();

            self::$delegationCache[$uid] = $row ?: null;
        } catch (\Throwable $e) {
            self::$delegationCache[$uid] = null;
        }

        return self::$delegationCache[$uid];
    }

    public static function clearDelegationCache(): void
    {
        self::$delegationCache = [];
    }

    /**
     * Cek apakah pengguna aktif berhak melihat halaman Hasil Survei Pemetaan SDM.
     */
    public static function canViewResults(): bool
    {
        if (!session()->get('isLoggedIn')) {
            return false;
        }

        if (self::canManageSurvey()) {
            return true;
        }

        return self::getActiveDelegation() !== null;
    }

    /**
     * Cek apakah pengguna aktif berhak membuka kembali survei terkirim agar responden dapat mengedit isiannya
     * (berlaku bagi Kabag KUK, Direktur, Admin, serta staf yang diberikan tugas / didelegasikan).
     */
    public static function canUnlockSurvey(): bool
    {
        return self::canViewResults();
    }

    /**
     * Cek apakah pengguna aktif berhak mengunduh rekapitulasi Excel.
     */
    public static function canExportResults(): bool
    {
        if (self::canManageSurvey()) {
            return true;
        }

        $delegation = self::getActiveDelegation();
        return $delegation !== null && (int) ($delegation['can_export'] ?? 0) === 1;
    }

    /**
     * Cek apakah tautan pengisian survei sedang dibuka.
     */
    public static function isSurveyOpen(): bool
    {
        try {
            return (string) SettingModel::getSetting('enable_survei_pemetaan', '1') === '1';
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Ambil tahun survei aktif dari pengaturan sistem.
     */
    public static function getActiveSurveyYear(): int
    {
        try {
            $val = (int) SettingModel::getSetting('tahun_aktif_survei_pemetaan', date('Y'));
            return ($val >= 2024 && $val <= 2035) ? $val : (int) date('Y');
        } catch (\Throwable $e) {
            return (int) date('Y');
        }
    }

    /**
     * Cek apakah nilai atribut profil di tabel users masih dianggap kosong.
     */
    public static function isEmptyProfileValue($val): bool
    {
        $str = trim((string) ($val ?? ''));
        return $str === '' || $str === '-' || strtolower($str) === 'null';
    }

    /**
     * Mengecek apakah akun pegawai ASN masih berhak mengisi survei aktif
     * (aktif, atau mutasi_keluar/pensiun yang tmt_selesai >= hari ini).
     */
    public static function isUserEligibleForSurvey(array $userRow): bool
    {
        $status = strtolower((string) ($userRow['status_pegawai'] ?? 'aktif'));
        if ($status === 'nonaktif') {
            return false;
        }

        if (in_array($status, ['mutasi_keluar', 'pensiun'], true)) {
            $tmt = trim((string) ($userRow['tmt_selesai'] ?? ''));
            if ($tmt !== '' && $tmt < date('Y-m-d')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Menyinkronkan atribut profil kosong (serta pembaruan penulisan gelar pada nama lengkap jika terverifikasi)
     * dari snapshot survei ke tabel users secara aman.
     * - Atribut pangkat, jabatan, dan unit hanya diperbarui jika kolom di tabel users masih kosong
     *   (atau sedang direvisi dalam sesi draf yang sama setelah sebelumnya kosong).
     * - Pembaruan nama_lengkap pada akun yang sudah berisi nama hanya dieksekusi jika sesi terverifikasi ($allowNameUpdate = true).
     * - TIDAK PERNAH menyentuh kolom users.status_pegawai.
     * - Menjalankan Dual-Sync users.unit & users.unit_id berdasarkan master unit_kerja.
     */
    public static function syncEmptyProfileFields(
        int $userId,
        array $responRow,
        bool $updateActiveSession = false,
        bool $allowNameUpdate = false,
        array $sessionEditableFields = []
    ): array {
        helper('audit');

        $userModel = new User();
        $user      = $userModel->select('id, nama_lengkap, nip, jabatan, pangkat, unit, unit_id, role')
                               ->find($userId);

        if (!$user) {
            return ['updated' => false, 'fields' => []];
        }

        $db            = \Config\Database::connect();
        $activeRiwayat = null;
        if ($db->tableExists('riwayat_atasan')) {
            $activeRiwayat = $db->table('riwayat_atasan')
                                ->where('user_id', $userId)
                                ->where('is_aktif', 1)
                                ->orderBy('id', 'DESC')
                                ->get()
                                ->getRowArray();
        }

        $updates        = [];
        $oldValues      = [];
        $riwayatUpdates = [];

        // 1. Nama Lengkap (jika masih kosong, atau jika pegawai/pengelola terverifikasi memperbarui penulisan gelar)
        $snapNama    = trim((string) ($responRow['snapshot_nama'] ?? ''));
        $currentNama = trim((string) ($user['nama_lengkap'] ?? ''));
        $isNamaEmpty = self::isEmptyProfileValue($currentNama);
        $hasNamaDiff = ($snapNama !== '' && $snapNama !== $currentNama);

        if ($snapNama !== '' && ($isNamaEmpty || ($allowNameUpdate && $hasNamaDiff))) {
            $oldValues['nama_lengkap'] = $user['nama_lengkap'] ?? null;
            $updates['nama_lengkap']   = mb_substr($snapNama, 0, 120);
        }

        // 2. Pangkat / Golongan (jika kosong di users, atau sedang dilengkapi pada sesi draf aktif)
        $snapPangkat    = trim((string) ($responRow['snapshot_pangkat'] ?? ''));
        $canSyncPangkat = self::isEmptyProfileValue($user['pangkat'] ?? '')
            || ($allowNameUpdate && in_array('pangkat', $sessionEditableFields, true));

        if ($canSyncPangkat && $snapPangkat !== '' && $snapPangkat !== '-' && $snapPangkat !== trim((string) ($user['pangkat'] ?? ''))) {
            $oldValues['pangkat'] = $user['pangkat'] ?? null;
            $updates['pangkat']   = mb_substr($snapPangkat, 0, 100);
        }

        // 3. Jabatan (jika kosong di users, atau sedang dilengkapi pada sesi draf aktif)
        $snapJabatan    = trim((string) ($responRow['snapshot_jabatan'] ?? ''));
        $canSyncJabatan = self::isEmptyProfileValue($user['jabatan'] ?? '')
            || ($allowNameUpdate && in_array('jabatan', $sessionEditableFields, true));

        if ($canSyncJabatan && $snapJabatan !== '' && $snapJabatan !== '-' && $snapJabatan !== trim((string) ($user['jabatan'] ?? ''))) {
            $oldValues['jabatan'] = $user['jabatan'] ?? null;
            $updates['jabatan']   = mb_substr($snapJabatan, 0, 100);

            if ($activeRiwayat && self::isEmptyProfileValue($activeRiwayat['jabatan'] ?? '')) {
                $riwayatUpdates['jabatan'] = $updates['jabatan'];
            }
        }

        // 4. Dual-Sync Unit Kerja & unit_id (jika kosong di users dan bukan pimpinan institusi tanpa unit)
        $roleLower      = strtolower((string) ($user['role'] ?? 'user'));
        $isLeadership   = in_array($roleLower, ['direktur', 'wadir', 'admin'], true);
        $snapUnitId     = (int) ($responRow['snapshot_unit_id'] ?? 0);
        $snapUnitString = trim((string) ($responRow['snapshot_unit'] ?? ''));
        $canSyncUnit    = !$isLeadership && (
            self::isEmptyProfileValue($user['unit'] ?? '')
            || ($allowNameUpdate && in_array('unit', $sessionEditableFields, true))
        );

        if ($canSyncUnit) {
            $unitModel = new UnitKerja();
            $unitMatch = null;
            if ($snapUnitId > 0) {
                $unitMatch = $unitModel->find($snapUnitId);
            } elseif ($snapUnitString !== '' && $snapUnitString !== 'Pimpinan Institusi') {
                $unitMatch = $unitModel->where('nama_unit', $snapUnitString)->first();
            }

            if ($unitMatch && ($unitMatch['nama_unit'] !== trim((string) ($user['unit'] ?? '')) || (int) ($user['unit_id'] ?? 0) !== (int) $unitMatch['id'])) {
                $oldValues['unit']    = $user['unit'] ?? null;
                $oldValues['unit_id'] = $user['unit_id'] ?? null;
                $updates['unit']      = $unitMatch['nama_unit'];
                $updates['unit_id']   = (int) $unitMatch['id'];

                if ($activeRiwayat && self::isEmptyProfileValue($activeRiwayat['unit'] ?? '')) {
                    $riwayatUpdates['unit']    = $unitMatch['nama_unit'];
                    $riwayatUpdates['unit_id'] = (int) $unitMatch['id'];
                }
            }
        }

        $responModel = new SurveiResponModel();

        try {
            $db->transStart();

            if (!empty($updates)) {
                $userModel->update($userId, $updates);
            }

            if (!empty($riwayatUpdates) && !empty($activeRiwayat['id'])) {
                $db->table('riwayat_atasan')
                   ->where('id', (int) $activeRiwayat['id'])
                   ->update($riwayatUpdates);
            }

            if (!empty($responRow['id'])) {
                $keepPendingForName = (!$allowNameUpdate && !$isNamaEmpty && $hasNamaDiff);
                if (!$keepPendingForName) {
                    $responModel->update((int) $responRow['id'], [
                        'pending_profile_sync' => 0,
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['updated' => false, 'fields' => []];
            }

            if (!empty($updates)) {
                log_audit('UPDATE', 'users', (string) $userId, $oldValues, array_merge($updates, [
                    'source' => 'survei_pemetaan_sdm',
                ]));

                if ($updateActiveSession && (int) self::getCurrentUserId() === $userId) {
                    $sessionPatch = [];
                    if (isset($updates['nama_lengkap'])) {
                        $sessionPatch['nama']         = $updates['nama_lengkap'];
                        $sessionPatch['nama_lengkap'] = $updates['nama_lengkap'];
                    }
                    if (isset($updates['pangkat'])) {
                        $sessionPatch['pangkat'] = $updates['pangkat'];
                    }
                    if (isset($updates['jabatan'])) {
                        $sessionPatch['jabatan'] = $updates['jabatan'];
                    }
                    if (isset($updates['unit'])) {
                        $sessionPatch['unit']    = $updates['unit'];
                        $sessionPatch['unit_id'] = $updates['unit_id'];
                    }
                    if (!empty($sessionPatch)) {
                        session()->set($sessionPatch);
                    }
                }
            }

            return [
                'updated' => !empty($updates),
                'fields'  => array_keys($updates),
            ];
        } catch (\Throwable $e) {
            log_message('error', 'Gagal sinkronisasi profil dari Survei Pemetaan SDM: ' . $e->getMessage());
            return ['updated' => false, 'fields' => []];
        }
    }

    /**
     * Sinkronisasi otomatis saat pengguna ASN berhasil login ke ECC apabila ada pending_profile_sync = 1.
     */
    public static function syncPendingProfileOnLogin(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('survei_pemetaan_respon')) {
                return;
            }

            $responModel = new SurveiResponModel();
            $pendingRow  = $responModel->where('user_id', $userId)
                                       ->where('pending_profile_sync', 1)
                                       ->where('is_valid', 1)
                                       ->orderBy('id', 'DESC')
                                       ->first();

            if ($pendingRow) {
                self::syncEmptyProfileFields($userId, $pendingRow, true);
            }
        } catch (\Throwable $e) {
            // Failsafe: jangan pernah menghambat proses login utama
        }
    }
}

