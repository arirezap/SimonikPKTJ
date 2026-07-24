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

        // Cek dari array roles yang di-load saat login
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
        return session()->get('all_roles') ?? [session()->get('role')];
    }
}
