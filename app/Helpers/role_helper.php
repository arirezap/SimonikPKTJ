<?php

/**
 * Role Helper - Multi-Role System
 * 
 * Helper ini menyediakan fungsi-fungsi untuk mengecek role pengguna
 * yang mendukung sistem multi-role (tabel pivot user_roles).
 */

if (!function_exists('hasRole')) {
    /**
     * Cek apakah user yang sedang login memiliki role tertentu.
     * Mengecek baik role primer (kolom `role` di users) maupun
     * role dari tabel pivot `user_roles`.
     *
     * @param string $roleName Nama role yang dicek
     * @return bool
     */
    function hasRole(string $roleName): bool
    {
        // Cek role primer (backward compatible)
        if (strtolower((string) session()->get('role')) === strtolower($roleName)) {
            return true;
        }

        // Lazy-loading fallback: jika all_roles belum tersimpan di session pengguna aktif
        if (!session()->has('all_roles')) {
            $userId = session()->get('id') ?? session()->get('user_id');
            if ($userId) {
                try {
                    $db = \Config\Database::connect();
                    $secondaryRoles = $db->table('user_roles')
                        ->select('role')
                        ->where('user_id', $userId)
                        ->get()
                        ->getResultArray();
                    $roles = array_column($secondaryRoles, 'role');
                    $primaryRole = (string)session()->get('role');
                    if ($primaryRole !== '') {
                        array_unshift($roles, $primaryRole);
                    }
                    $allRoles = array_values(array_unique(array_filter($roles)));
                    session()->set('all_roles', $allRoles);
                } catch (\Throwable $e) {
                    // Fallback tenang jika basis data belum siap
                }
            }
        }

        // Cek dari array roles yang di-load saat login atau lazy-loaded
        $allRoles = session()->get('all_roles') ?? [];
        return in_array(strtolower($roleName), array_map('strtolower', $allRoles));
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Cek apakah user memiliki SALAH SATU dari daftar role yang diberikan.
     *
     * @param array $roleNames Array nama role
     * @return bool
     */
    function hasAnyRole(array $roleNames): bool
    {
        foreach ($roleNames as $role) {
            if (hasRole($role)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getUserRoles')) {
    /**
     * Ambil semua role yang dimiliki user yang sedang login.
     *
     * @return array
     */
    function getUserRoles(): array
    {
        if (!session()->has('all_roles')) {
            // Trigger lazy-loading via hasRole check
            hasRole('dummy_trigger');
        }
        return session()->get('all_roles') ?? [session()->get('role')];
    }
}
