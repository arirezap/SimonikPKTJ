<?php

/**
 * Avatar & UI Badge Helper - Clean & Reusable UI Helpers
 * 
 * Helper ini menyediakan fungsi-fungsi reusable untuk merender avatar profil,
 * badge role, dan badge status pegawai secara konsisten di seluruh view SIMONIK.
 */

if (!function_exists('render_user_avatar')) {
    /**
     * Render avatar foto profil atau inisial bulat pegawai.
     *
     * @param array|string|null $userOrPhoto Array user (dengan key 'foto' & 'nama_lengkap') atau string nama file foto.
     * @param string $namaNama Lengkap jika $userOrPhoto berupa string foto.
     * @param int $size Ukuran avatar dalam piksel (default 30px).
     * @param string $extraClass Kelas CSS tambahan.
     * @return string HTML rendering avatar
     */
    function render_user_avatar($userOrPhoto = null, string $nama = '', int $size = 30, string $extraClass = ''): string
    {
        $fotoName = '';
        $namaLengkap = $nama;

        if (is_array($userOrPhoto)) {
            $fotoName = $userOrPhoto['foto'] ?? '';
            $namaLengkap = $userOrPhoto['nama_lengkap'] ?? $nama;
        } elseif (is_string($userOrPhoto)) {
            $fotoName = $userOrPhoto;
        }

        $fotoPath = !empty($fotoName) ? 'assets/uploads/profile/' . $fotoName : '';
        $hasPhoto = (!empty($fotoPath) && file_exists(FCPATH . $fotoPath));

        if ($hasPhoto) {
            $imgUrl = base_url($fotoPath);
            $escNama = esc($namaLengkap);
            return '<img src="' . $imgUrl . '" class="avatar-circle-sm rounded-circle border shadow-sm me-2 ' . esc($extraClass) . '" style="width: ' . $size . 'px; height: ' . $size . 'px; object-fit: cover; flex-shrink: 0;" alt="' . $escNama . '">';
        }

        $initials = 'P';
        if (!empty(trim($namaLengkap))) {
            $words = explode(' ', trim($namaLengkap));
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($words[0], 0, 2));
            }
        }

        $fontSizePx = max(10, round($size * 0.4));
        return '<div class="avatar-circle-sm me-2 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle ' . esc($extraClass) . '" style="width: ' . $size . 'px; height: ' . $size . 'px; font-size: ' . $fontSizePx . 'px; flex-shrink: 0;">' . $initials . '</div>';
    }
}

if (!function_exists('render_role_badge')) {
    /**
     * Render badge role utama pegawai dengan warna konsisten.
     *
     * @param string $role Key role pengguna
     * @return string HTML badge
     */
    function render_role_badge(string $role): string
    {
        $roleLabels = [
            'direktur'      => 'Direktur (Level 1)',
            'wadir'         => 'Wadir (Level 2)',
            'kabag_aak'     => 'Kabag AAK (Level 3)',
            'kabag_kuk'     => 'Kabag KUK (Level 3)',
            'manajemen'     => 'Kanit/Katim (Level 4)',
            'user'          => 'Staf (Level 5)',
            'tugas_belajar' => 'Tugas Belajar (Level 5)',
            'admin'         => 'Superadmin',
            'kepegawaian'   => 'Kepegawaian',
            'spm'           => 'SPM'
        ];

        $roleText = $roleLabels[$role] ?? ucfirst($role);
        $badgeStyle = 'bg-secondary-subtle text-secondary border border-secondary-subtle';

        if ($role === 'admin') {
            $badgeStyle = 'bg-danger-subtle text-danger border border-danger-subtle';
        } elseif ($role === 'kepegawaian') {
            $badgeStyle = 'bg-info-subtle text-info-emphasis border border-info-subtle';
        } elseif ($role === 'manajemen') {
            $badgeStyle = 'bg-success-subtle text-success border border-success-subtle';
        } elseif ($role === 'direktur' || $role === 'wadir') {
            $badgeStyle = 'bg-primary-subtle text-primary border border-primary-subtle';
        }

        return '<span class="badge ' . $badgeStyle . ' px-2 py-0.5" style="font-size: 0.72rem; font-weight: 500;">' . esc($roleText) . '</span>';
    }
}

if (!function_exists('render_unit_kabag_badge')) {
    /**
     * Render badge unit kabag (AAK / KUK).
     *
     * @param string|null $unitKabag
     * @return string HTML badge
     */
    function render_unit_kabag_badge(?string $unitKabag): string
    {
        if (empty($unitKabag)) {
            return '<span class="text-muted small">-</span>';
        }

        $val = strtolower(trim($unitKabag));
        $badgeClass = 'bg-secondary-subtle text-secondary border';
        if ($val === 'aak') $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
        if ($val === 'kuk') $badgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';

        return '<span class="badge ' . $badgeClass . ' px-2 py-0.5" style="font-size: 0.7rem;">' . strtoupper(esc($val)) . '</span>';
    }
}
