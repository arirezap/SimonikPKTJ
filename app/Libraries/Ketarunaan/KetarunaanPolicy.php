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

        if ($nomorTaruna !== null && session()->get('username') === $nomorTaruna) {
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

        if ($nomorTaruna !== null && session()->get('username') === $nomorTaruna) {
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
            if (function_exists('hasAnyRole') && hasAnyRole([
                'admin',
                'direktur',
                'kabag_aak',
                'ketarunaan',
                'ketarunaan_validator',
            ])) {
                return true;
            }

            if ($nomorTaruna !== null && session()->get('username') === $nomorTaruna) {
                return true;
            }

            return false;
        }

        return self::canAccessModule();
    }
}

