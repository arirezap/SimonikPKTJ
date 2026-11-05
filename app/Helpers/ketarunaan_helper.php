<?php

/**
 * Helper terpusat (Single Source of Truth) untuk modul ECC 2.0 (Ketarunaan & Alumni).
 * Mematuhi prinsip DRY, 8-Point Grid, dan standar visual ECC 1.5 (design.md §21).
 */

if (!function_exists('ketarunaan_status_catalog')) {
    /**
     * Mengembalikan katalog lengkap 6 status pendidikan taruna beserta token warna dan penjelasan resmi.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_status_catalog(): array
    {
        return [
            'aktif' => [
                'kode'         => 'aktif',
                'label'        => 'Aktif',
                'kelompok'     => 'dalam_pendidikan',
                'color'        => '#0284c7',
                'bg_soft'      => '#eff6ff',
                'border_color' => '#bfdbfe',
                'tile_class'   => 'status-tile-aktif',
                'deskripsi'    => 'Mengikuti pendidikan sesuai ketentuan.',
            ],
            'skorsing' => [
                'kode'         => 'skorsing',
                'label'        => 'Skorsing',
                'kelompok'     => 'dalam_pendidikan_sementara',
                'color'        => '#d97706',
                'bg_soft'      => '#fef3c7',
                'border_color' => '#fde68a',
                'tile_class'   => 'status-tile-skorsing',
                'deskripsi'    => 'Dihentikan sementara karena pembinaan.',
            ],
            'cuti' => [
                'kode'         => 'cuti',
                'label'        => 'Cuti Akademik',
                'kelompok'     => 'dalam_pendidikan_sementara',
                'color'        => '#10b981',
                'bg_soft'      => '#ecfdf5',
                'border_color' => '#a7f3d0',
                'tile_class'   => 'status-tile-cuti',
                'deskripsi'    => 'Izin cuti akademik untuk periode tertentu.',
            ],
            'mengundurkan_diri' => [
                'kode'         => 'mengundurkan_diri',
                'label'        => 'Mengundurkan Diri',
                'kelompok'     => 'berhenti',
                'color'        => '#ea580c',
                'bg_soft'      => '#fff7ed',
                'border_color' => '#fed7aa',
                'tile_class'   => 'status-tile-mundur',
                'deskripsi'    => 'Berhenti atas permohonan resmi; bukan lulusan.',
            ],
            'drop_out' => [
                'kode'         => 'drop_out',
                'label'        => 'Drop Out',
                'kelompok'     => 'berhenti',
                'color'        => '#dc2626',
                'bg_soft'      => '#fef2f2',
                'border_color' => '#fecaca',
                'tile_class'   => 'status-tile-do',
                'deskripsi'    => 'Diberhentikan berdasarkan keputusan resmi.',
            ],
            'lulus' => [
                'kode'         => 'lulus',
                'label'        => 'Lulus',
                'kelompok'     => 'lulusan',
                'color'        => '#7c3aed',
                'bg_soft'      => '#f5f3ff',
                'border_color' => '#ddd6fe',
                'tile_class'   => 'status-tile-lulus',
                'deskripsi'    => 'Menyelesaikan seluruh program pendidikan.',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_status_meta')) {
    /**
     * Mengambil metadata satu status pendidikan taruna berdasarkan kode.
     *
     * @param string $kodeStatus
     * @return array<string, string>
     */
    function ketarunaan_status_meta(string $kodeStatus): array
    {
        $catalog = ketarunaan_status_catalog();
        $key = strtolower(trim($kodeStatus));

        return $catalog[$key] ?? [
            'kode'         => $key,
            'label'        => ucwords(str_replace('_', ' ', $key)),
            'kelompok'     => 'lainnya',
            'color'        => '#475569',
            'bg_soft'      => '#f1f5f9',
            'border_color' => '#cbd5e1',
            'tile_class'   => 'status-tile-aktif',
            'deskripsi'    => '-',
        ];
    }
}

if (!function_exists('ketarunaan_status_badge')) {
    /**
     * Merender HTML badge status pendidikan sesuai 8-Point Grid (px-3 py-1).
     *
     * @param string $kodeStatus
     * @return string
     */
    function ketarunaan_status_badge(string $kodeStatus): string
    {
        $meta = ketarunaan_status_meta($kodeStatus);
        $color = esc($meta['color']);
        $bg = esc($meta['bg_soft']);
        $border = esc($meta['border_color']);
        $label = esc($meta['label']);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 fw-semibold" style="background-color:%s; color:%s; border:1px solid %s; font-size:0.75rem;">'
            . '<span class="rounded-circle d-inline-block" style="width:8px; height:8px; background-color:%s;"></span>'
            . '%s</span>',
            $bg,
            $color,
            $border,
            $color,
            $label
        );
    }
}

if (!function_exists('ketarunaan_validasi_meta')) {
    /**
     * Mengambil metadata status validasi data ketarunaan.
     *
     * @param string $statusValidasi
     * @return array<string, string>
     */
    function ketarunaan_validasi_meta(string $statusValidasi): array
    {
        $key = strtolower(trim($statusValidasi));
        $map = [
            'draf' => [
                'label'       => 'Draf',
                'badge_class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                'icon'        => 'bi-pencil-fill',
            ],
            'diajukan' => [
                'label'       => 'Diajukan',
                'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'icon'        => 'bi-clock-fill',
            ],
            'perlu_perbaikan' => [
                'label'       => 'Perlu Perbaikan',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon'        => 'bi-exclamation-circle-fill',
            ],
            'dikembalikan' => [
                'label'       => 'Dikembalikan',
                'badge_class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon'        => 'bi-exclamation-circle-fill',
            ],
            'tervalidasi' => [
                'label'       => 'Tervalidasi',
                'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                'icon'        => 'bi-check-circle-fill',
            ],
        ];

        return $map[$key] ?? [
            'label'       => ucfirst($key),
            'badge_class' => 'bg-light text-dark border',
            'icon'        => 'bi-info-circle-fill',
        ];
    }
}

if (!function_exists('ketarunaan_validasi_badge')) {
    /**
     * Merender HTML badge status validasi sesuai 8-Point Grid (px-3 py-1).
     *
     * @param string $statusValidasi
     * @return string
     */
    function ketarunaan_validasi_badge(string $statusValidasi): string
    {
        $meta = ketarunaan_validasi_meta($statusValidasi);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['badge_class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_format_tanggal')) {
    /**
     * Format tanggal Y-m-d ke format bahasa Indonesia.
     *
     * @param string|null $dateStr
     * @param bool        $shortMonth
     * @return string
     */
    function ketarunaan_format_tanggal(?string $dateStr, bool $shortMonth = false): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return '-';
        }

        $ts = strtotime($dateStr);
        if ($ts === false) {
            return '-';
        }

        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthsShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $day = date('d', $ts);
        $monthIdx = (int) date('n', $ts);
        $year = date('Y', $ts);
        $monthName = $shortMonth ? ($monthsShort[$monthIdx] ?? '') : ($monthsFull[$monthIdx] ?? '');

        return sprintf('%s %s %s', $day, $monthName, $year);
    }
}

if (!function_exists('ketarunaan_mask_nik')) {
    /**
     * Menyamarkan NIK secara konsisten kecuali pengguna memiliki hak akses penuh.
     *
     * @param string|null $nik
     * @param bool        $canViewFull
     * @return string
     */
    function ketarunaan_mask_nik(?string $nik, bool $canViewFull = false): string
    {
        if (empty($nik)) {
            return '-';
        }
        if ($canViewFull) {
            return $nik;
        }

        return '****************';
    }
}

if (!function_exists('ketarunaan_capaian_kategori_catalog')) {
    /**
     * Katalog resmi kategori capaian rekam jejak taruna.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_capaian_kategori_catalog(): array
    {
        return [
            'kompetensi_sertifikasi' => [
                'kode'         => 'kompetensi_sertifikasi',
                'label'        => 'Kompetensi / Sertifikasi',
                'short_label'  => 'Kompetensi',
                'color'        => '#0284c7',
                'bg_soft'      => '#eff6ff',
                'border_color' => '#bfdbfe',
            ],
            'organisasi' => [
                'kode'         => 'organisasi',
                'label'        => 'Organisasi',
                'short_label'  => 'Organisasi',
                'color'        => '#0d9488',
                'bg_soft'      => '#f0fdfa',
                'border_color' => '#99f6e4',
            ],
            'pengembangan_diri' => [
                'kode'         => 'pengembangan_diri',
                'label'        => 'Pengembangan Diri',
                'short_label'  => 'Pengembangan Diri',
                'color'        => '#7c3aed',
                'bg_soft'      => '#f5f3ff',
                'border_color' => '#ddd6fe',
            ],
            'akademik' => [
                'kode'         => 'akademik',
                'label'        => 'Akademik',
                'short_label'  => 'Akademik',
                'color'        => '#2563eb',
                'bg_soft'      => '#eff6ff',
                'border_color' => '#bfdbfe',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_capaian_kategori_meta')) {
    /**
     * Mengambil metadata satu kategori capaian.
     *
     * @param string $kodeKategori
     * @return array<string, string>
     */
    function ketarunaan_capaian_kategori_meta(string $kodeKategori): array
    {
        $catalog = ketarunaan_capaian_kategori_catalog();
        $key = strtolower(trim($kodeKategori));

        return $catalog[$key] ?? [
            'kode'         => $key,
            'label'        => ucwords(str_replace('_', ' ', $key)),
            'short_label'  => ucwords(str_replace('_', ' ', $key)),
            'color'        => '#475569',
            'bg_soft'      => '#f1f5f9',
            'border_color' => '#cbd5e1',
        ];
    }
}

if (!function_exists('ketarunaan_capaian_kategori_badge')) {
    /**
     * Merender badge kategori capaian sesuai 8-Point Grid (px-3 py-1).
     *
     * @param string $kodeKategori
     * @param bool   $useShortLabel
     * @return string
     */
    function ketarunaan_capaian_kategori_badge(string $kodeKategori, bool $useShortLabel = false): string
    {
        $meta = ketarunaan_capaian_kategori_meta($kodeKategori);
        $label = $useShortLabel ? $meta['short_label'] : $meta['label'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color:%s; color:%s; border:1px solid %s; font-size:0.75rem;">%s</span>',
            esc($meta['bg_soft']),
            esc($meta['color']),
            esc($meta['border_color']),
            esc($label)
        );
    }
}

if (!function_exists('ketarunaan_modul_sumber_badge')) {
    /**
     * Merender badge modul sumber pada Kronologi dan Dokumen Bukti.
     *
     * @param string $modulSumber
     * @return string
     */
    function ketarunaan_modul_sumber_badge(string $modulSumber): string
    {
        $key = strtolower(trim($modulSumber));
        $map = [
            'capaian' => [
                'label'  => 'Capaian',
                'bg'     => '#fef3c7',
                'color'  => '#b45309',
                'border' => '#fde68a',
            ],
            'prestasi' => [
                'label'  => 'Prestasi',
                'bg'     => '#f5f3ff',
                'color'  => '#6d28d9',
                'border' => '#ddd6fe',
            ],
            'beasiswa' => [
                'label'  => 'Beasiswa',
                'bg'     => '#fff7ed',
                'color'  => '#c2410c',
                'border' => '#fed7aa',
            ],
            'status_pendidikan' => [
                'label'  => 'Status Pendidikan',
                'bg'     => '#ecfdf5',
                'color'  => '#047857',
                'border' => '#a7f3d0',
            ],
            'pendidikan' => [
                'label'  => 'Pendidikan',
                'bg'     => '#eff6ff',
                'color'  => '#1d4ed8',
                'border' => '#bfdbfe',
            ],
            'pelanggaran' => [
                'label'  => 'Pembinaan / Disiplin',
                'bg'     => '#fef2f2',
                'color'  => '#b91c1c',
                'border' => '#fecaca',
            ],
        ];

        $meta = $map[$key] ?? [
            'label'  => ucwords(str_replace('_', ' ', $key)),
            'bg'     => '#f1f5f9',
            'color'  => '#475569',
            'border' => '#cbd5e1',
        ];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color:%s; color:%s; border:1px solid %s; font-size:0.75rem;">%s</span>',
            esc($meta['bg']),
            esc($meta['color']),
            esc($meta['border']),
            esc($meta['label'])
        );
    }
}


