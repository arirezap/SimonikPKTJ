<?php

namespace App\Libraries;

/**
 * PenilaianKinerjaPolicy
 * 
 * Sentralisasi logika otorisasi dan matriks hak akses untuk modul
 * Rekap & Penilaian Kinerja ECC, mencakup penilaian atasan,
 * evaluasi mandiri direktur, pemantauan grafik, dan pembukaan revisi.
 */
class PenilaianKinerjaPolicy
{
    /**
     * Cek apakah user berhak mengakses modul Rekap & Penilaian Kinerja.
     * Semua akun terautentikasi dapat melihat rekap kinerjanya sendiri.
     */
    public static function canAccessModule(array $user): bool
    {
        return !empty($user['id']);
    }

    /**
     * Cek apakah user berhak melakukan penilaian mandiri (self-evaluation).
     * Khusus akun Direktur atau pengguna tanpa atasan langsung di sistem.
     */
    public static function canEvaluateSelf(array $user): bool
    {
        $role = strtolower((string)($user['role'] ?? ''));
        return ($role === 'direktur' || empty($user['atasan_id']));
    }

    /**
     * Cek apakah evaluator berhak memberikan penilaian kinerja kepada staf tertentu.
     * 
     * Aturan Matriks:
     * - Evaluasi mandiri ditolak di sini (gunakan canEvaluateSelf).
     * - Role Wadir DILARANG keras menilai staf (hanya mengelola Target Saya).
     * - Admin dan Direktur berhak menilai seluruh staf.
     * - Kepegawaian HANYA berhak menilai:
     *     a. Staf langsung di bawahnya (atasan_id == evaluatorId)
     *     b. Pegawai berstatus Tugas Belajar (role 'tugas_belajar' atau di user_roles)
     * - Atasan Langsung berhak menilai staf langsung di bawahnya (atasan_id == evaluatorId).
     */
    public static function canEvaluateStaf(array $evaluator, array $staf): bool
    {
        $evaluatorId = (int)($evaluator['id'] ?? 0);
        $stafId = (int)($staf['id'] ?? 0);

        if ($evaluatorId === 0 || $stafId === 0 || $evaluatorId === $stafId) {
            return false;
        }

        // Role Wadir dilarang menilai staf
        $evaluatorRoles = self::getUserRolesList($evaluator);
        if (in_array('wadir', $evaluatorRoles, true)) {
            return false;
        }

        // Admin dan Direktur memiliki otoritas penilaian luas
        if (in_array('admin', $evaluatorRoles, true) || in_array('direktur', $evaluatorRoles, true)) {
            return true;
        }

        // Cek relasi atasan langsung
        $isAtasanLangsung = !empty($staf['atasan_id']) && ((int)$staf['atasan_id'] === $evaluatorId);
        if ($isAtasanLangsung) {
            return true;
        }

        // Role Kepegawaian hanya boleh menilai pegawai Tugas Belajar jika bukan atasan langsung
        if (in_array('kepegawaian', $evaluatorRoles, true)) {
            return self::isTugasBelajar($staf);
        }

        return false;
    }

    /**
     * Cek apakah viewer berhak melihat data rekap/grafik kinerja pegawai tertentu (misal API chart).
     * 
     * Aturan:
     * - Melihat data diri sendiri: selalu boleh.
     * - Admin, Direktur, Kepegawaian: berhak memantau institusi penuh.
     * - Atasan Langsung (termasuk Kabag, Katim, Koordinator): berhak melihat data staf langsungnya.
     * - Wadir dan pegawai biasa: TIDAK berhak melihat data detail staf lain.
     */
    public static function canViewStafRekap(array $viewer, array $staf): bool
    {
        $viewerId = (int)($viewer['id'] ?? 0);
        $stafId = (int)($staf['id'] ?? 0);

        if ($viewerId === 0 || $stafId === 0) {
            return false;
        }

        if ($viewerId === $stafId) {
            return true;
        }

        $viewerRoles = self::getUserRolesList($viewer);

        // Role Wadir tidak boleh melihat data rekap staf lain
        if (in_array('wadir', $viewerRoles, true)) {
            return false;
        }

        // Admin, Direktur, Kepegawaian memiliki visibilitas institusi penuh
        if (
            in_array('admin', $viewerRoles, true) || 
            in_array('direktur', $viewerRoles, true) || 
            in_array('kepegawaian', $viewerRoles, true)
        ) {
            return true;
        }

        // Atasan Langsung berhak melihat staf langsungnya
        if (!empty($staf['atasan_id']) && ((int)$staf['atasan_id'] === $viewerId)) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah operator berhak membuka izin revisi laporan harian staf (bukaKunci).
     * 
     * Aturan:
     * - Admin dan Direktur: berhak membuka seluruh staf institusi.
     * - Kepegawaian: berhak membuka untuk staf langsungnya dan pegawai tugas belajar.
     * - Atasan Langsung: berhak membuka untuk staf langsungnya.
     */
    public static function canOpenRevision(array $operator, array $staf): bool
    {
        $operatorId = (int)($operator['id'] ?? 0);
        $stafId = (int)($staf['id'] ?? 0);

        if ($operatorId === 0 || $stafId === 0) {
            return false;
        }

        $operatorRoles = self::getUserRolesList($operator);

        if (in_array('admin', $operatorRoles, true) || in_array('direktur', $operatorRoles, true)) {
            return true;
        }

        $isAtasanLangsung = !empty($staf['atasan_id']) && ((int)$staf['atasan_id'] === $operatorId);
        if ($isAtasanLangsung) {
            return true;
        }

        if (in_array('kepegawaian', $operatorRoles, true)) {
            return self::isTugasBelajar($staf);
        }

        return false;
    }

    /**
     * Cek apakah seorang pegawai berstatus Tugas Belajar.
     */
    public static function isTugasBelajar(array $user): bool
    {
        $role = strtolower((string)($user['role'] ?? ''));
        if ($role === 'tugas_belajar') {
            return true;
        }

        $userId = (int)($user['id'] ?? 0);
        if ($userId > 0) {
            try {
                $db = \Config\Database::connect();
                $exists = $db->table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_name', 'tugas_belajar')
                    ->countAllResults();
                return $exists > 0;
            } catch (\Throwable $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Helper privat untuk mengumpulkan semua role (primer dan sekunder).
     */
    public static function getUserRolesList(array $user): array
    {
        $roles = [];
        if (!empty($user['role'])) {
            $roles[] = strtolower(trim((string)$user['role']));
        }

        $sessionUserId = session()->get('id') ?? session()->get('user_id');
        if (!empty($user['id']) && (int)$user['id'] === (int)$sessionUserId && session()->has('all_roles')) {
            $sessionRoles = session()->get('all_roles');
            if (is_array($sessionRoles)) {
                foreach ($sessionRoles as $r) {
                    $roles[] = strtolower(trim((string)$r));
                }
            }
        } elseif (!empty($user['id'])) {
            try {
                $db = \Config\Database::connect();
                $secondary = $db->table('user_roles')
                    ->select('role_name')
                    ->where('user_id', (int)$user['id'])
                    ->get()
                    ->getResultArray();
                foreach ($secondary as $row) {
                    if (!empty($row['role_name'])) {
                        $roles[] = strtolower(trim((string)$row['role_name']));
                    }
                }
            } catch (\Throwable $e) {}
        }

        return array_values(array_unique(array_filter($roles)));
    }

    // =========================================================================
    // MODUL TARGET KINERJA BULANAN (/laporan-harian)
    // =========================================================================

    /**
     * Cek apakah user berhak melihat target kinerja pegawai tertentu.
     */
    public static function canViewTarget(array $currentUser, array $targetUser): bool
    {
        $currentUserId = (int)($currentUser['id'] ?? 0);
        $targetUserId  = (int)($targetUser['id'] ?? 0);

        if ($currentUserId === 0 || $targetUserId === 0) {
            return false;
        }

        // Melihat target sendiri selalu diizinkan
        if ($currentUserId === $targetUserId) {
            return true;
        }

        // Role Wadir dilarang mengelola/melihat target staf
        $currentRoles = self::getUserRolesList($currentUser);
        if (in_array('wadir', $currentRoles, true)) {
            return false;
        }

        // Admin dan Direktur berhak melihat target seluruh staf
        if (in_array('admin', $currentRoles, true) || in_array('direktur', $currentRoles, true)) {
            return true;
        }

        // Atasan langsung
        $isAtasan = !empty($targetUser['atasan_id']) && ((int)$targetUser['atasan_id'] === $currentUserId);
        if ($isAtasan) {
            return true;
        }

        // Kepegawaian boleh melihat pegawai Tugas Belajar
        if (in_array('kepegawaian', $currentRoles, true)) {
            return self::isTugasBelajar($targetUser);
        }

        return false;
    }

    /**
     * Cek apakah user berhak menyunting/merevisi target kinerja pegawai tertentu.
     */
    public static function canEditTarget(array $currentUser, array $targetUser): bool
    {
        $currentUserId = (int)($currentUser['id'] ?? 0);
        $targetUserId  = (int)($targetUser['id'] ?? 0);

        if ($currentUserId === 0 || $targetUserId === 0) {
            return false;
        }

        // Mengedit target diri sendiri
        if ($currentUserId === $targetUserId) {
            return true;
        }

        // Role Wadir DILARANG keras menyunting/merevisi target staf
        $currentRoles = self::getUserRolesList($currentUser);
        if (in_array('wadir', $currentRoles, true)) {
            return false;
        }

        // Admin dan Direktur berhak menyunting target staf
        if (in_array('admin', $currentRoles, true) || in_array('direktur', $currentRoles, true)) {
            return true;
        }

        // Atasan langsung berhak
        $isAtasan = !empty($targetUser['atasan_id']) && ((int)$targetUser['atasan_id'] === $currentUserId);
        if ($isAtasan) {
            return true;
        }

        // Kepegawaian hanya untuk pegawai tugas belajar
        if (in_array('kepegawaian', $currentRoles, true)) {
            return self::isTugasBelajar($targetUser);
        }

        return false;
    }

    /**
     * Cek apakah user berhak menyetujui target kinerja pegawai tertentu.
     */
    public static function canApproveTarget(array $currentUser, array $targetUser): bool
    {
        $currentUserId = (int)($currentUser['id'] ?? 0);
        $targetUserId  = (int)($targetUser['id'] ?? 0);

        if ($currentUserId === 0 || $targetUserId === 0) {
            return false;
        }

        // Dilarang menyetujui target sendiri (Conflict of Interest) kecuali akun role primer Direktur
        if ($currentUserId === $targetUserId) {
            $primaryRole = strtolower(trim((string)($currentUser['role'] ?? '')));
            return ($primaryRole === 'direktur');
        }

        // Role Wadir DILARANG keras menyetujui target staf
        $currentRoles = self::getUserRolesList($currentUser);
        if (in_array('wadir', $currentRoles, true)) {
            return false;
        }

        // Admin dan Direktur berhak menyetujui seluruh staf
        if (in_array('admin', $currentRoles, true) || in_array('direktur', $currentRoles, true)) {
            return true;
        }

        // Atasan langsung
        $isAtasan = !empty($targetUser['atasan_id']) && ((int)$targetUser['atasan_id'] === $currentUserId);
        if ($isAtasan) {
            return true;
        }

        // Kepegawaian hanya untuk pegawai tugas belajar
        if (in_array('kepegawaian', $currentRoles, true)) {
            return self::isTugasBelajar($targetUser);
        }

        return false;
    }

    /**
     * Cek apakah user berhak menghapus baris target kinerja.
     */
    public static function canDeleteTarget(array $currentUser, array $targetUser, ?array $targetRow = null): bool
    {
        $currentUserId = (int)($currentUser['id'] ?? 0);
        $targetUserId  = (int)($targetUser['id'] ?? 0);

        if ($currentUserId === 0 || $targetUserId === 0) {
            return false;
        }

        $currentRoles = self::getUserRolesList($currentUser);
        $isDirektur = in_array('direktur', $currentRoles, true);
        $isAdmin    = in_array('admin', $currentRoles, true);

        // Jika target milik sendiri
        if ($currentUserId === $targetUserId) {
            if ($targetRow && ($targetRow['status_approval'] ?? '') === 'disetujui' && !$isDirektur && !$isAdmin) {
                return false;
            }
            return true;
        }

        // Jika target milik staf lain:
        // Role Wadir dilarang
        if (in_array('wadir', $currentRoles, true)) {
            return false;
        }

        // Admin dan Direktur boleh
        if ($isAdmin || $isDirektur) {
            return true;
        }

        // Atasan langsung
        $isAtasan = !empty($targetUser['atasan_id']) && ((int)$targetUser['atasan_id'] === $currentUserId);
        if ($isAtasan) {
            if ($targetRow && ($targetRow['status_approval'] ?? '') === 'disetujui') {
                return false;
            }
            return true;
        }

        // Kepegawaian untuk tugas belajar
        if (in_array('kepegawaian', $currentRoles, true) && self::isTugasBelajar($targetUser)) {
            if ($targetRow && ($targetRow['status_approval'] ?? '') === 'disetujui') {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Cek apakah user berhak menyalin target dari periode sumber.
     */
    public static function canCopyTargetSource(array $currentUser, array $sourceUser): bool
    {
        return self::canViewTarget($currentUser, $sourceUser);
    }

    /**
     * Cek apakah user berhak membatalkan persetujuan target bulanan staf (cancelApprove).
     * Khusus role Admin.
     */
    public static function canCancelTargetApproval(array $currentUser): bool
    {
        $roles = self::getUserRolesList($currentUser);
        return in_array('admin', $roles, true);
    }
}
