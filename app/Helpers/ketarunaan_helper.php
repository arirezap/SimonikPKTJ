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
                'text_color'   => '#1e40af',
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
                'text_color'   => '#92400e',
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
                'text_color'   => '#065f46',
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
                'text_color'   => '#9a3412',
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
                'text_color'   => '#b91c1c',
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
                'text_color'   => '#6d28d9',
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
            'text_color'   => '#334155',
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
        $dotColor  = esc($meta['color']);
        $textColor = esc($meta['text_color'] ?? $meta['color']);
        $bg        = esc($meta['bg_soft']);
        $border    = esc($meta['border_color']);
        $label     = esc($meta['label']);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 d-inline-flex align-items-center gap-2 fw-semibold text-nowrap" style="background-color:%s; color:%s; border:1px solid %s; font-size:0.75rem;">'
            . '<span class="rounded-circle d-inline-block flex-shrink-0" style="width:8px; height:8px; background-color:%s;"></span>'
            . '%s</span>',
            $bg,
            $textColor,
            $border,
            $dotColor,
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
                'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
                'icon'        => 'bi-pencil-fill',
            ],
            'diajukan' => [
                'label'       => 'Diajukan',
                'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'icon'        => 'bi-clock-fill',
            ],
            'perlu_perbaikan' => [
                'label'       => 'Perlu Perbaikan',
                'badge_class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                'icon'        => 'bi-exclamation-circle-fill',
            ],
            'dikembalikan' => [
                'label'       => 'Dikembalikan',
                'badge_class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                'icon'        => 'bi-exclamation-circle-fill',
            ],
            'tervalidasi' => [
                'label'       => 'Tervalidasi',
                'badge_class' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
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
            '<span class="badge rounded-pill px-3 py-1 fw-semibold text-nowrap %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['badge_class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_status_validasi_badge')) {
    /**
     * Alias kompatibilitas untuk merender badge status validasi ketarunaan.
     *
     * @param string $statusValidasi
     * @return string
     */
    function ketarunaan_status_validasi_badge(string $statusValidasi): string
    {
        return ketarunaan_validasi_badge($statusValidasi);
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
                'color'        => '#1e40af',
                'bg_soft'      => '#eff6ff',
                'border_color' => '#bfdbfe',
            ],
            'organisasi' => [
                'kode'         => 'organisasi',
                'label'        => 'Organisasi',
                'short_label'  => 'Organisasi',
                'color'        => '#0f766e',
                'bg_soft'      => '#f0fdfa',
                'border_color' => '#99f6e4',
            ],
            'pengembangan_diri' => [
                'kode'         => 'pengembangan_diri',
                'label'        => 'Pengembangan Diri',
                'short_label'  => 'Pengembangan Diri',
                'color'        => '#6d28d9',
                'bg_soft'      => '#f5f3ff',
                'border_color' => '#ddd6fe',
            ],
            'akademik' => [
                'kode'         => 'akademik',
                'label'        => 'Akademik',
                'short_label'  => 'Akademik',
                'color'        => '#1d4ed8',
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
            'lowongan' => [
                'label'  => 'Lowongan',
                'bg'     => '#ecfeff',
                'color'  => '#0e7490',
                'border' => '#a5f3fc',
            ],
            'alumni' => [
                'label'  => 'Alumni',
                'bg'     => '#f5f3ff',
                'color'  => '#6d28d9',
                'border' => '#ddd6fe',
            ],
            'foto_taruna' => [
                'label'  => 'Foto Taruna',
                'bg'     => '#eff6ff',
                'color'  => '#1e40af',
                'border' => '#bfdbfe',
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

if (!function_exists('ketarunaan_jenis_prestasi_catalog')) {
    /**
     * Katalog jenis dan tingkat prestasi resmi ECC 2.0.
     *
     * @return array<string, string>
     */
    function ketarunaan_jenis_prestasi_catalog(): array
    {
        return [
            'akademik'     => 'Akademik',
            'non_akademik' => 'Non-Akademik',
            'olahraga'     => 'Olahraga',
            'seni'         => 'Seni & Budaya',
        ];
    }
}

if (!function_exists('ketarunaan_tingkat_prestasi_catalog')) {
    /**
     * Katalog tingkat prestasi resmi ECC 2.0.
     *
     * @return array<string, string>
     */
    function ketarunaan_tingkat_prestasi_catalog(): array
    {
        return [
            'internal'      => 'Internal',
            'daerah'        => 'Daerah',
            'nasional'      => 'Nasional',
            'internasional' => 'Internasional',
        ];
    }
}

if (!function_exists('ketarunaan_keikutsertaan_badge')) {
    /**
     * Merender badge keikutsertaan (Tim / Individu) sesuai 8-Point Grid.
     *
     * @param string $jenis
     * @return string
     */
    function ketarunaan_keikutsertaan_badge(string $jenis): string
    {
        $isTim = strtolower(trim($jenis)) === 'tim';
        if ($isTim) {
            return '<span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-size:0.75rem;">Tim</span>';
        }

        return '<span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color:#f8fafc; color:#475569; border:1px solid #cbd5e1; font-size:0.75rem;">Individu</span>';
    }
}

if (!function_exists('ketarunaan_generate_simple_pdf')) {
    /**
     * Menghasilkan dokumen PDF 1 halaman standar untuk pratinjau/unduhan dokumen ringkas ECC 2.0.
     *
     * @param string               $subjudul
     * @param string               $judul
     * @param array<string,string> $barisInfo
     * @return string
     */
    function ketarunaan_generate_simple_pdf(string $subjudul, string $judul, array $barisInfo = []): string
    {
        $cleanText = static function (string $txt): string {
            $cleaned = preg_replace('/[^A-Za-z0-9 \-.,:\/()#]/', '', $txt);
            $cleaned = $cleaned !== null && $cleaned !== '' ? $cleaned : '-';
            return str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $cleaned);
        };

        $linesPdf = [
            "BT /F1 16 Tf 50 750 Td (EVIDENCE COMMAND CENTER \\(ECC\\) - PKTJ TEGAL) Tj",
            "0 -26 Td /F1 13 Tf (" . $cleanText($subjudul) . ") Tj",
            "0 -32 Td /F1 11 Tf (" . $cleanText($judul) . ") Tj",
        ];

        $gap = -24;
        foreach ($barisInfo as $label => $val) {
            $linesPdf[] = "0 {$gap} Td (" . $cleanText($label . ' : ' . $val) . ") Tj";
            $gap = -18;
        }
        $linesPdf[] = "0 -28 Td (Dokumen ini tercatat resmi pada Evidence Command Center \\(ECC\\) PKTJ.) Tj ET";

        $stream = implode(' ', $linesPdf);
        $len    = strlen($stream);

        return "%PDF-1.4\n"
            . "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n"
            . "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n"
            . "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n"
            . "4 0 obj << /Length {$len} >> stream\n{$stream}\nendstream endobj\n"
            . "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n"
            . "xref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000241 00000 n \n"
            . sprintf("%010d 00000 n \n", 293 + $len)
            . "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n" . (363 + $len) . "\n%%EOF";
    }
}

if (!function_exists('ketarunaan_mask_nomor_taruna')) {
    /**
     * Menyamarkan Nomor Taruna secara konsisten untuk peran pemantau non-disiplin.
     *
     * @param string|null $nomorTaruna
     * @param bool        $canViewFull
     * @return string
     */
    function ketarunaan_mask_nomor_taruna(?string $nomorTaruna, bool $canViewFull = false): string
    {
        $clean = trim((string) $nomorTaruna);
        if ($clean === '') {
            return '-';
        }
        if ($canViewFull || strlen($clean) <= 4) {
            return $clean;
        }

        return substr($clean, 0, 2) . '••' . substr($clean, -3);
    }
}

if (!function_exists('ketarunaan_penetapan_meta')) {
    /**
     * Metadata 4 status penetapan kasus pelanggaran sesuai PRD PEL-03.
     *
     * @param string $status
     * @return array<string, string>
     */
    function ketarunaan_penetapan_meta(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'dugaan' => [
                'label' => 'Dugaan',
                'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
            'terbukti' => [
                'label' => 'Terbukti',
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
            ],
            'tidak_terbukti' => [
                'label' => 'Tidak Terbukti',
                'class' => 'bg-success-subtle text-success border border-success-subtle',
            ],
            'dibatalkan' => [
                'label' => 'Dibatalkan',
                'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            ],
        ];

        return $map[$key] ?? [
            'label' => ucwords(str_replace('_', ' ', $key)),
            'class' => 'bg-light text-dark border',
        ];
    }
}

if (!function_exists('ketarunaan_penetapan_badge')) {
    /**
     * Merender badge HTML status penetapan kasus sesuai 8-Point Grid.
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_penetapan_badge(string $status): string
    {
        $meta = ketarunaan_penetapan_meta($status);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_penyelesaian_meta')) {
    /**
     * Metadata 3 status penyelesaian kasus pelanggaran sesuai PRD PEL-04.
     *
     * @param string $status
     * @return array<string, string>
     */
    function ketarunaan_penyelesaian_meta(string $status): array
    {
        $key = strtolower(trim($status));
        $map = [
            'proses' => [
                'label' => 'Proses',
                'class' => 'bg-primary-subtle text-primary border border-primary-subtle',
            ],
            'selesai' => [
                'label' => 'Selesai',
                'class' => 'bg-success-subtle text-success border border-success-subtle',
            ],
            'banding' => [
                'label' => 'Banding',
                'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
        ];

        return $map[$key] ?? [
            'label' => ucfirst($key),
            'class' => 'bg-light text-dark border',
        ];
    }
}

if (!function_exists('ketarunaan_penyelesaian_badge')) {
    /**
     * Merender badge HTML status penyelesaian kasus sesuai 8-Point Grid.
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_penyelesaian_badge(string $status): string
    {
        $meta = ketarunaan_penyelesaian_meta($status);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_kategori_pelanggaran_catalog')) {
    /**
     * Katalog kategori pelanggaran resmi ECC 2.0.
     *
     * @return array<string, string>
     */
    function ketarunaan_kategori_pelanggaran_catalog(): array
    {
        return [
            'Kedisiplinan'    => 'Kedisiplinan',
            'Tata Kehidupan'  => 'Tata Kehidupan',
            'Akademik'        => 'Akademik',
            'Etika & Perilaku'=> 'Etika & Perilaku',
            'Keselamatan'     => 'Keselamatan',
        ];
    }
}

if (!function_exists('ketarunaan_tingkat_pelanggaran_catalog')) {
    /**
     * Katalog tingkat pelanggaran resmi ECC 2.0.
     *
     * @return array<string, string>
     */
    function ketarunaan_tingkat_pelanggaran_catalog(): array
    {
        return [
            'belum_ditetapkan' => 'Belum Ditetapkan',
            'ringan'           => 'Ringan',
            'sedang'           => 'Sedang',
            'berat'            => 'Berat',
        ];
    }
}

if (!function_exists('ketarunaan_jenis_beasiswa_catalog')) {
    /**
     * Katalog 4 jenis resmi beasiswa ECC 2.0 beserta mekanisme dan palet warna ECC 1.5.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_jenis_beasiswa_catalog(): array
    {
        return [
            'apresiasi' => [
                'kode'          => 'apresiasi',
                'label'         => 'Beasiswa Apresiasi',
                'short'         => 'Apresiasi',
                'mekanisme'     => 'Pemeringkatan',
                'ringkasan'     => '5 IP semester tertinggi per kelas',
                'syarat'        => 'IP semester tervalidasi dan kelas',
                'color'         => '#0284c7',
                'bg_soft'       => '#eff6ff',
                'border_color'  => '#bfdbfe',
                'badge_class'   => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            ],
            'prestasi' => [
                'kode'          => 'prestasi',
                'label'         => 'Beasiswa Prestasi',
                'short'         => 'Prestasi',
                'mekanisme'     => 'Klaim',
                'ringkasan'     => 'Akademik dan nonakademik',
                'syarat'        => 'Sertifikat dan dokumentasi kegiatan',
                'color'         => '#d97706',
                'bg_soft'       => '#fffbeb',
                'border_color'  => '#fde68a',
                'badge_class'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
            'daerah_3tp' => [
                'kode'          => 'daerah_3tp',
                'label'         => 'Beasiswa Daerah 3TP',
                'short'         => 'Daerah 3TP',
                'mekanisme'     => 'Klaim',
                'ringkasan'     => 'Berdasarkan domisili wilayah 3TP',
                'syarat'        => 'Domisili dan acuan wilayah resmi',
                'color'         => '#059669',
                'bg_soft'       => '#ecfdf5',
                'border_color'  => '#a7f3d0',
                'badge_class'   => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            ],
            'tidak_mampu' => [
                'kode'          => 'tidak_mampu',
                'label'         => 'Beasiswa Tidak Mampu',
                'short'         => 'Tidak Mampu',
                'mekanisme'     => 'Klaim',
                'ringkasan'     => 'Berdasarkan kondisi ekonomi keluarga',
                'syarat'        => 'Data dan dokumen ekonomi terbatas',
                'color'         => '#dc2626',
                'bg_soft'       => '#fef2f2',
                'border_color'  => '#fecaca',
                'badge_class'   => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_jenis_beasiswa_badge')) {
    /**
     * Merender badge HTML jenis beasiswa sesuai 8-Point Grid (px-3 py-1).
     *
     * @param string $jenis
     * @param bool   $useFullLabel
     * @return string
     */
    function ketarunaan_jenis_beasiswa_badge(string $jenis, bool $useFullLabel = false): string
    {
        $catalog = ketarunaan_jenis_beasiswa_catalog();
        $key     = strtolower(trim($jenis));
        $meta    = $catalog[$key] ?? [
            'label'       => ucwords(str_replace('_', ' ', $key)),
            'short'       => ucwords(str_replace('_', ' ', $key)),
            'badge_class' => 'bg-light text-dark border',
        ];
        $text = $useFullLabel ? $meta['label'] : $meta['short'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['badge_class']),
            esc($text)
        );
    }
}

if (!function_exists('ketarunaan_program_beasiswa_status_badge')) {
    /**
     * Merender badge status pelaksanaan program beasiswa.
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_program_beasiswa_status_badge(string $status): string
    {
        $key = strtolower(trim($status));
        $map = [
            'dibuka'   => ['label' => 'Dibuka',   'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
            'seleksi'  => ['label' => 'Seleksi',  'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'berjalan' => ['label' => 'Berjalan', 'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'selesai'  => ['label' => 'Selesai',  'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
        ];
        $meta = $map[$key] ?? ['label' => ucfirst($key), 'class' => 'bg-light text-dark border'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_is_program_beasiswa_open')) {
    /**
     * Memeriksa apakah suatu program beasiswa jalur klaim sedang dibuka untuk pengajuan baru taruna.
     * Syarat: jenis termasuk jalur klaim (prestasi, daerah_3tp, tidak_mampu), status_program = 'dibuka',
     * tanggal hari ini berada dalam rentang periode_mulai s.d. periode_selesai (jika diisi),
     * serta kuota penerima ditetapkan belum penuh.
     *
     * @param array<string, mixed> $program
     * @param string|null          $today Format Y-m-d
     * @return bool
     */
    function ketarunaan_is_program_beasiswa_open(array $program, ?string $today = null): bool
    {
        $jenis = strtolower(trim((string) ($program['jenis_beasiswa'] ?? '')));
        if (!in_array($jenis, ['prestasi', 'daerah_3tp', 'tidak_mampu'], true)) {
            return false;
        }

        $status = strtolower(trim((string) ($program['status_program'] ?? '')));
        if ($status !== 'dibuka') {
            return false;
        }

        $acuan   = $today !== null && trim($today) !== '' ? substr(trim($today), 0, 10) : date('Y-m-d');
        $mulai   = trim((string) ($program['periode_mulai'] ?? ''));
        $selesai = trim((string) ($program['periode_selesai'] ?? ''));

        if ($mulai !== '' && $mulai !== '0000-00-00' && $acuan < substr($mulai, 0, 10)) {
            return false;
        }
        if ($selesai !== '' && $selesai !== '0000-00-00' && $acuan > substr($selesai, 0, 10)) {
            return false;
        }

        $kuota = (int) ($program['kuota'] ?? 0);
        if ($kuota > 0 && array_key_exists('ditetapkan_count', $program) && (int) $program['ditetapkan_count'] >= $kuota) {
            return false;
        }

        return true;
    }
}

if (!function_exists('ketarunaan_verifikasi_beasiswa_badge')) {
    /**
     * Merender badge hasil verifikasi berkas/kelayakan beasiswa (terpisah dari penetapan).
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_verifikasi_beasiswa_badge(string $status): string
    {
        $key = strtolower(trim($status));
        $map = [
            'belum_diperiksa' => ['label' => 'Belum Diperiksa', 'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
            'memenuhi'        => ['label' => 'Memenuhi',        'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'perlu_perbaikan' => ['label' => 'Perbaikan',       'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'tidak_memenuhi'  => ['label' => 'Tidak Memenuhi',  'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
        ];
        $meta = $map[$key] ?? ['label' => ucwords(str_replace('_', ' ', $key)), 'class' => 'bg-light text-dark border'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_penetapan_beasiswa_badge')) {
    /**
     * Merender badge status penetapan penerima beasiswa sesuai PRD BEA-09.
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_penetapan_beasiswa_badge(string $status): string
    {
        $key = strtolower(trim($status));
        $map = [
            'kandidat'   => ['label' => 'Menunggu Penetapan', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'ditetapkan' => ['label' => 'Ditetapkan',         'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'dibatalkan' => ['label' => 'Dibatalkan',         'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
        ];
        $meta = $map[$key] ?? ['label' => ucfirst($key), 'class' => 'bg-light text-dark border'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_aturan_seri_catalog')) {
    /**
     * Katalog aturan pemecah seri peringkat ke-5 Beasiswa Apresiasi (PRD BEA-04 & UAT-10).
     *
     * @return array<string, string>
     */
    function ketarunaan_aturan_seri_catalog(): array
    {
        return [
            'belum_ditetapkan' => 'Belum Ditetapkan (Tahan Finalisasi)',
            'ipk'              => 'IP Kumulatif Tertinggi',
            'sks_tertinggi'    => 'Jumlah SKS Lulus Terbanyak',
            'prestasi'         => 'Skor Prestasi Tervalidasi',
            'disiplin'         => 'Catatan Disiplin Bersih',
        ];
    }
}

if (!function_exists('ketarunaan_rupiah')) {
    /**
     * Format nominal rupiah yang rapi untuk tampilan tabular.
     *
     * @param int|float|string|null $nominal
     * @return string
     */
    function ketarunaan_rupiah($nominal): string
    {
        $val = (int) round((float) ($nominal ?? 0));
        if ($val <= 0) {
            return '-';
        }

        return 'Rp ' . number_format($val, 0, ',', '.');
    }
}

if (!function_exists('ketarunaan_lowongan_jenis_catalog')) {
    /**
     * Katalog jenis pekerjaan resmi pada Modul Lowongan Pekerjaan.
     *
     * @return array<string, string>
     */
    function ketarunaan_lowongan_jenis_catalog(): array
    {
        return [
            'Tetap'   => 'Karyawan Tetap',
            'Kontrak' => 'Kontrak / PKWT',
            'Magang'  => 'Magang Industri',
            'MT'      => 'Management Trainee',
        ];
    }
}

if (!function_exists('ketarunaan_lowongan_metode_catalog')) {
    /**
     * Katalog kanal penyampaian lamaran oleh alumni/taruna.
     *
     * @return array<string, string>
     */
    function ketarunaan_lowongan_metode_catalog(): array
    {
        return [
            'tautan_eksternal' => 'Tautan Pendaftaran Perusahaan',
            'email'            => 'Alamat Email Rekrutmen',
            'keduanya'         => 'Tautan & Alamat Email',
        ];
    }
}

if (!function_exists('ketarunaan_lowongan_dokumen_kategori_catalog')) {
    /**
     * Katalog kategori dokumen pendukung & sumber lowongan kerja (LOW-03).
     *
     * @return array<string, array<string, mixed>>
     */
    function ketarunaan_lowongan_dokumen_kategori_catalog(): array
    {
        return [
            'surat_perusahaan' => [
                'label'       => 'Surat Perusahaan',
                'is_internal' => 1,
                'badge_class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            ],
            'poster_rekrutmen' => [
                'label'       => 'Poster Rekrutmen',
                'is_internal' => 0,
                'badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            ],
            'bukti_konfirmasi' => [
                'label'       => 'Bukti Konfirmasi',
                'is_internal' => 1,
                'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
            'tautan_sumber' => [
                'label'       => 'Tautan Sumber',
                'is_internal' => 1,
                'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_lowongan_checklist_catalog')) {
    /**
     * Butir pemeriksaan standar untuk verifikasi per lowongan (LOW-04, LOW-05).
     *
     * @return array<string, string>
     */
    function ketarunaan_lowongan_checklist_catalog(): array
    {
        return [
            'kontak_mitra'      => 'Keabsahan perusahaan dan kontak rekrutmen telah diperiksa',
            'kesesuaian_syarat' => 'Posisi, kualifikasi, dan program studi sasaran sesuai',
            'kanal_dan_tenggat' => 'Kanal lamaran resmi dan batas pendaftaran masih berlaku',
            'dokumen_sumber'    => 'Dokumen pendukung atau tautan sumber lowongan memadai',
        ];
    }
}

if (!function_exists('ketarunaan_lowongan_alasan_penutupan_catalog')) {
    /**
     * Pilihan alasan standar penutupan manual lowongan (LOW-06).
     *
     * @return array<int, string>
     */
    function ketarunaan_lowongan_alasan_penutupan_catalog(): array
    {
        return [
            'Kuota pelamar telah terpenuhi',
            'Rekrutmen selesai lebih awal dari mitra',
            'Perusahaan mitra menunda atau membatalkan rekrutmen',
            'Perubahan kebutuhan posisi dari mitra',
            'Lainnya',
        ];
    }
}

if (!function_exists('ketarunaan_lowongan_status_efektif')) {
    /**
     * Menentukan status tayang efektif lowongan secara dinamis berdasarkan validasi, publikasi, dan tenggat (LOW-05, LOW-06).
     *
     * @param array<string, mixed> $row
     * @param string|null          $waktuAcuan Format Y-m-d H:i:s
     * @return string terbuka|draf_tervalidasi|draf|berakhir|ditutup
     */
    function ketarunaan_lowongan_status_efektif(array $row, ?string $waktuAcuan = null): string
    {
        $validasi  = strtolower(trim((string) ($row['status_validasi'] ?? 'draf')));
        $publikasi = strtolower(trim((string) ($row['status_publikasi'] ?? 'draf')));
        $tenggat   = trim((string) ($row['batas_pendaftaran'] ?? ''));
        $acuan     = $waktuAcuan ?? date('Y-m-d H:i:s');

        if ($publikasi === 'ditutup') {
            return 'ditutup';
        }

        if ($publikasi === 'terbit') {
            if ($tenggat !== '' && $tenggat < $acuan) {
                return 'berakhir';
            }
            if ($validasi === 'tervalidasi') {
                return 'terbuka';
            }
        }

        if ($validasi === 'tervalidasi') {
            return 'draf_tervalidasi';
        }

        return 'draf';
    }
}

if (!function_exists('ketarunaan_lowongan_publikasi_badge')) {
    /**
     * Merender badge status publikasi efektif lowongan sesuai 8-Point Grid.
     *
     * @param array<string, mixed> $row
     * @param string|null          $waktuAcuan
     * @return string
     */
    function ketarunaan_lowongan_publikasi_badge(array $row, ?string $waktuAcuan = null): string
    {
        $efektif = ketarunaan_lowongan_status_efektif($row, $waktuAcuan);
        $map     = [
            'terbuka'          => ['label' => 'Terbuka',   'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'draf_tervalidasi' => ['label' => 'Draf Siap', 'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
            'draf'             => ['label' => 'Draf',      'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
            'berakhir'         => ['label' => 'Berakhir',  'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'ditutup'          => ['label' => 'Ditutup',   'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
        ];
        $meta = $map[$efektif] ?? $map['draf'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_perusahaan_verifikasi_badge')) {
    /**
     * Merender badge status verifikasi kontak perusahaan mitra (LOW-01, LOW-04).
     *
     * @param int|bool $isVerified
     * @return string
     */
    function ketarunaan_perusahaan_verifikasi_badge($isVerified): string
    {
        if ((int) $isVerified === 1) {
            return '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size:0.75rem;">Kontak diperiksa</span>';
        }

        return '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.75rem;">Belum diperiksa</span>';
    }
}

if (!function_exists('ketarunaan_sanitize_external_url')) {
    /**
     * Sanitasi tautan eksternal agar hanya menerima skema http:// atau https:// (dan opsional mailto:).
     *
     * @param string|null $url
     * @param bool        $allowMailto
     * @return string|null
     */
    function ketarunaan_sanitize_external_url(?string $url, bool $allowMailto = false): ?string
    {
        $clean = trim((string) $url);
        if ($clean === '') {
            return null;
        }

        if ($allowMailto && stripos($clean, 'mailto:') === 0) {
            $emailPart = substr($clean, 7);
            return filter_var($emailPart, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $emailPart : null;
        }

        $scheme = strtolower((string) parse_url($clean, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return filter_var($clean, FILTER_VALIDATE_URL) ? $clean : null;
    }
}

if (!function_exists('ketarunaan_alumni_sumber_konfirmasi_catalog')) {
    /**
     * Daftar sumber konfirmasi pembaruan kontak dan pekerjaan alumni (ALM-02).
     *
     * @return array<string, string>
     */
    function ketarunaan_alumni_sumber_konfirmasi_catalog(): array
    {
        return [
            'mandiri_alumni'    => 'Mandiri oleh Alumni',
            'telepon_pic'       => 'Telepon Resmi Staf',
            'pesan_whatsapp'    => 'Pesan Resmi / WhatsApp',
            'email_resmi'       => 'Alamat Email Resmi',
            'yudisium_akademik' => 'Keputusan Yudisium / SK',
            'korps_alumni'      => 'Forum / Ikatan Alumni',
        ];
    }
}

if (!function_exists('ketarunaan_alumni_status_meta')) {
    /**
     * Metadata status profil dasar alumni (aktif atau nonaktif karena koreksi kelulusan).
     *
     * @return array<string, string>
     */
    function ketarunaan_alumni_status_meta(string $statusProfil): array
    {
        $key = strtolower(trim($statusProfil));
        $map = [
            'aktif' => [
                'kode'  => 'aktif',
                'label' => 'Alumni Aktif',
                'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            ],
            'nonaktif_koreksi' => [
                'kode'  => 'nonaktif_koreksi',
                'label' => 'Nonaktif (Koreksi)',
                'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            ],
        ];

        return $map[$key] ?? [
            'kode'  => $key,
            'label' => ucwords(str_replace('_', ' ', $key)),
            'class' => 'bg-light text-dark border',
        ];
    }
}

if (!function_exists('ketarunaan_alumni_status_badge')) {
    /**
     * Badge HTML status profil alumni sesuai 8-Point Grid.
     */
    function ketarunaan_alumni_status_badge(string $statusProfil): string
    {
        $meta = ketarunaan_alumni_status_meta($statusProfil);

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_alumni_jenis_kejadian_meta')) {
    /**
     * Metadata jenis kejadian riwayat alumni dan koreksi kelulusan.
     *
     * @return array<string, string>
     */
    function ketarunaan_alumni_jenis_kejadian_meta(string $jenis): array
    {
        $key = strtolower(trim($jenis));
        $map = [
            'kelulusan_awal' => [
                'label' => 'Penetapan Kelulusan',
                'class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            ],
            'pembaruan_kontak' => [
                'label' => 'Pembaruan Kontak',
                'class' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            ],
            'ralat_sk' => [
                'label' => 'Ralat SK / Tanggal Lulus',
                'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
            'batal_lulus' => [
                'label' => 'Pembatalan Kelulusan',
                'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            ],
        ];

        return $map[$key] ?? [
            'label' => ucwords(str_replace('_', ' ', $key)),
            'class' => 'bg-light text-dark border',
        ];
    }
}

if (!function_exists('ketarunaan_alumni_koreksi_status_badge')) {
    /**
     * Badge HTML status pengajuan koreksi kelulusan alumni.
     */
    function ketarunaan_alumni_koreksi_status_badge(string $statusValidasi): string
    {
        $key = strtolower(trim($statusValidasi));
        $map = [
            'diajukan' => [
                'label' => 'Menunggu Persetujuan',
                'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            ],
            'tervalidasi' => [
                'label' => 'Disetujui',
                'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            ],
        ];
        $meta = $map[$key] ?? [
            'label' => ucfirst($key),
            'class' => 'bg-light text-dark border',
        ];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_alumni_profil_dasar_badge')) {
    /**
     * Penanda lingkup modul Alumni (dikosongkan dari header sesuai kebijakan larangan pill header di design.md).
     */
    function ketarunaan_alumni_profil_dasar_badge(): string
    {
        return '';
    }
}

if (!function_exists('ketarunaan_safe_csv_cell')) {
    /**
     * Netralisasi sel CSV terhadap formula injection pada aplikasi pengolah angka.
     */
    function ketarunaan_safe_csv_cell(?string $value): string
    {
        $clean = str_replace(["\r", "\n"], ' ', (string) $value);
        if ($clean !== '' && in_array($clean[0], ['=', '+', '-', '@', "\t"], true)) {
            return "'" . $clean;
        }

        return $clean;
    }
}

if (!function_exists('ketarunaan_data_mode_badge')) {
    /**
     * Mengembalikan informasi penanda Data Simulasi vs Data Operasional dari pengaturan ECC 2.0 (NFR-09).
     *
     * @return array{mode: string, label: string, badge_class: string, html: string}
     */
    function ketarunaan_data_mode_info(?string $mode = null): array
    {
        if ($mode === null) {
            try {
                $mode = (string) \App\Models\SettingModel::getSetting('ecc2_data_mode', 'simulasi');
            } catch (\Throwable $e) {
                $mode = 'simulasi';
            }
        }

        $normalized = strtolower(trim($mode)) === 'produksi' ? 'produksi' : 'simulasi';
        $label      = $normalized === 'simulasi' ? 'Data Simulasi' : 'Data Operasional';
        $badgeClass = $normalized === 'simulasi'
            ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
            : 'bg-success-subtle text-success border border-success-subtle';

        // Pill di area judul halaman ditiadakan sesuai design.md; informasi mode tetap tersedia pada 'label' & 'mode'.
        $html = '';

        return [
            'mode'        => $normalized,
            'label'       => $label,
            'badge_class' => $badgeClass,
            'html'        => $html,
        ];
    }
}

if (!function_exists('ketarunaan_validasi_sumber_catalog')) {
    /**
     * Katalog 7 sumber antrean validasi lintas modul untuk Modul Validasi Data (GOV-01..03).
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_validasi_sumber_catalog(): array
    {
        return [
            'status_pendidikan' => [
                'kode'  => 'status_pendidikan',
                'label' => 'Status Pendidikan',
            ],
            'capaian' => [
                'kode'  => 'capaian',
                'label' => 'Capaian Taruna',
            ],
            'prestasi' => [
                'kode'  => 'prestasi',
                'label' => 'Prestasi Taruna',
            ],
            'pelanggaran' => [
                'kode'  => 'pelanggaran',
                'label' => 'Pembinaan / Pelanggaran',
            ],
            'beasiswa' => [
                'kode'  => 'beasiswa',
                'label' => 'Beasiswa Taruna',
            ],
            'lowongan' => [
                'kode'  => 'lowongan',
                'label' => 'Lowongan Pekerjaan',
            ],
            'alumni' => [
                'kode'  => 'alumni',
                'label' => 'Koreksi Alumni',
            ],
            'foto_taruna' => [
                'kode'  => 'foto_taruna',
                'label' => 'Foto Taruna',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_laporan_catalog')) {
    /**
     * Katalog 6 jenis laporan resmi ECC 2.0 beserta definisi satuan agar konsisten dengan Dashboard (DSH-03, UAT-19).
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_laporan_catalog(): array
    {
        return [
            'status_pendidikan' => [
                'kode'      => 'status_pendidikan',
                'label'     => 'Status Pendidikan Taruna',
                'satuan'    => 'Orang (taruna unik pada tanggal acuan)',
                'ringkasan' => 'Posisi 6 status pendidikan taruna berdasarkan riwayat status tervalidasi hingga tanggal acuan.',
            ],
            'prestasi' => [
                'kode'      => 'prestasi',
                'label'     => 'Prestasi Taruna',
                'satuan'    => 'Kegiatan prestasi (per taruna atau tim)',
                'ringkasan' => 'Daftar prestasi tervalidasi yang berdampak terhadap penghargaan dan rekam jejak taruna.',
            ],
            'pelanggaran' => [
                'kode'      => 'pelanggaran',
                'label'     => 'Pembinaan & Pelanggaran',
                'satuan'    => 'Kasus / perkara tercatat',
                'ringkasan' => 'Catatan perkara pelanggaran tervalidasi dengan status penetapan dan penyelesaian.',
            ],
            'beasiswa' => [
                'kode'      => 'beasiswa',
                'label'     => 'Penerima Beasiswa',
                'satuan'    => 'Taruna penerima tervalidasi',
                'ringkasan' => 'Daftar penerima beasiswa yang telah disahkan per program dan periode akademik.',
            ],
            'lowongan' => [
                'kode'      => 'lowongan',
                'label'     => 'Lowongan Pekerjaan',
                'satuan'    => 'Lowongan kerja tercatat',
                'ringkasan' => 'Rekapitulasi lowongan pekerjaan mitra dengan sasaran program studi dan batas pendaftaran.',
            ],
            'alumni' => [
                'kode'      => 'alumni',
                'label'     => 'Direktori Alumni',
                'satuan'    => 'Alumni aktif tervalidasi',
                'ringkasan' => 'Daftar alumni lulusan sah beserta pekerjaan saat ini dan kontak terkonfirmasi (Profil dasar).',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_home_url')) {
    /**
     * Menentukan tautan beranda ECC 2.0 sesuai peran pengguna aktif (portal mandiri untuk taruna/alumni, Dashboard untuk staf).
     */
    function ketarunaan_home_url(): string
    {
        if (\App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfAccountOnly()) {
            return site_url(ltrim(\App\Libraries\Ketarunaan\KetarunaanPolicy::getSelfPortalUrl(), '/'));
        }

        return site_url('ketarunaan/dashboard');
    }
}

if (!function_exists('ketarunaan_int_to_roman')) {
    /**
     * Mengonversi bilangan bulat positif menjadi angka Romawi standar.
     */
    function ketarunaan_int_to_roman(int $number): string
    {
        if ($number <= 0) {
            return '';
        }

        $map = [
            1000 => 'M',
            900  => 'CM',
            500  => 'D',
            400  => 'CD',
            100  => 'C',
            90   => 'XC',
            50   => 'L',
            40   => 'XL',
            10   => 'X',
            9    => 'IX',
            5    => 'V',
            4    => 'IV',
            1    => 'I',
        ];

        $result = '';
        $value  = $number;
        foreach ($map as $int => $roman) {
            while ($value >= $int) {
                $result .= $roman;
                $value -= $int;
            }
        }

        return $result;
    }
}

if (!function_exists('ketarunaan_roman_to_int')) {
    /**
     * Mengonversi string angka Romawi menjadi bilangan bulat (0 jika tidak sah).
     */
    function ketarunaan_roman_to_int(string $roman): int
    {
        $clean = strtoupper(trim($roman));
        if ($clean === '' || !preg_match('/^[IVXLCDM]+$/', $clean)) {
            return 0;
        }

        $values = [
            'I' => 1,
            'V' => 5,
            'X' => 10,
            'L' => 50,
            'C' => 100,
            'D' => 500,
            'M' => 1000,
        ];

        $total = 0;
        $prev  = 0;
        for ($i = strlen($clean) - 1; $i >= 0; $i--) {
            $curr = $values[$clean[$i]] ?? 0;
            if ($curr < $prev) {
                $total -= $curr;
            } else {
                $total += $curr;
            }
            $prev = $curr;
        }

        // Validasi kanonik agar string tidak sah seperti "IIX" ditolak
        return ketarunaan_int_to_roman($total) === $clean ? $total : 0;
    }
}

if (!function_exists('ketarunaan_angkatan_meta')) {
    /**
     * Menghitung nomor angkatan dan angka Romawi dari tahun masuk (patokan D-IV lulus 2023 = 2019/XXX, selisih 1989).
     *
     * @return array{tahun_masuk: int, angkatan_ke: ?int, kode_romawi: string, label: string}
     */
    function ketarunaan_angkatan_meta(int $tahunMasuk): array
    {
        if ($tahunMasuk < 1990 || $tahunMasuk > 2100) {
            return [
                'tahun_masuk' => $tahunMasuk,
                'angkatan_ke' => null,
                'kode_romawi' => '',
                'label'       => $tahunMasuk > 0 ? (string) $tahunMasuk : '-',
            ];
        }

        $angkatanKe = $tahunMasuk - 1989;
        $romawi     = ketarunaan_int_to_roman($angkatanKe);

        return [
            'tahun_masuk' => $tahunMasuk,
            'angkatan_ke' => $angkatanKe,
            'kode_romawi' => $romawi,
            'label'       => $romawi !== '' ? ($tahunMasuk . '/' . $romawi) : (string) $tahunMasuk,
        ];
    }
}

if (!function_exists('ketarunaan_format_angkatan')) {
    /**
     * Memformat angkatan menjadi "Tahun Masuk/Angka Romawi" (mis. 2022/XXXIII, 2023/XXXIV).
     *
     * @param array<string, mixed>|int|string|null $tahunOrRow
     */
    function ketarunaan_format_angkatan(array|int|string|null $tahunOrRow, ?string $kodeRomawi = null): string
    {
        $tahun  = 0;
        $romawi = trim((string) ($kodeRomawi ?? ''));

        if (is_array($tahunOrRow)) {
            $tahun = (int) ($tahunOrRow['tahun_masuk'] ?? 0);
            if ($romawi === '') {
                $romawi = trim((string) ($tahunOrRow['kode_romawi'] ?? ''));
            }
        } elseif ($tahunOrRow !== null && $tahunOrRow !== '') {
            if (is_string($tahunOrRow) && preg_match('/^\s*(\d{4})\s*\/\s*([IVXLCDM]+)\s*$/i', $tahunOrRow, $m)) {
                $tahun  = (int) $m[1];
                $romawi = $romawi !== '' ? $romawi : strtoupper($m[2]);
            } else {
                $tahun = (int) $tahunOrRow;
            }
        }

        if ($tahun <= 0) {
            return '-';
        }

        if ($romawi === '') {
            return ketarunaan_angkatan_meta($tahun)['label'];
        }

        return $tahun . '/' . strtoupper($romawi);
    }
}

if (!function_exists('ketarunaan_ensure_kalender_terkini')) {
    /**
     * Menjamin rujukan Angkatan (tahun berjalan s.d. 5 tahun ke belakang) dan
     * Tahun Akademik aktif otomatis terbentuk saat pergantian tahun / semester.
     */
    function ketarunaan_ensure_kalender_terkini(?\CodeIgniter\Database\BaseConnection $db = null, ?string $referenceDate = null, bool $forceCheck = false): void
    {
        $dateStr = ($referenceDate !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $referenceDate))
            ? $referenceDate
            : date('Y-m-d');

        static $checked = [];
        try {
            $db ??= \Config\Database::connect();
            $cacheKey = spl_object_id($db) . '|' . $dateStr;
            if (!$forceCheck && isset($checked[$cacheKey])) {
                return;
            }
            $checked[$cacheKey] = true;

            $year  = (int) substr($dateStr, 0, 4);
            $month = (int) substr($dateStr, 5, 2);
            if ($year < 1990 || $year > 2100) {
                return;
            }

            $now = date('Y-m-d H:i:s');

            // 1. Eskalasi otomatis ref_angkatan untuk rentang ($year - 5) s.d. $year
            $minYear      = max(1990, $year - 5);
            $existingRows = $db->table('ref_angkatan')
                ->select('tahun_masuk')
                ->where('tahun_masuk >=', $minYear)
                ->where('tahun_masuk <=', $year)
                ->get()
                ->getResultArray();

            $existingYears = [];
            foreach ($existingRows as $er) {
                $existingYears[(int) ($er['tahun_masuk'] ?? 0)] = true;
            }

            for ($y = $minYear; $y <= $year; $y++) {
                if (isset($existingYears[$y])) {
                    continue;
                }
                $meta = ketarunaan_angkatan_meta($y);
                $row  = [
                    'tahun_masuk'     => $y,
                    'angkatan_ke'     => $meta['angkatan_ke'],
                    'kode_romawi'     => $meta['kode_romawi'] !== '' ? $meta['kode_romawi'] : null,
                    'perkiraan_lulus' => $y + 4,
                    'is_aktif'        => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $db->table('ref_angkatan')->insert($row);
                $newId = (int) $db->insertID();
                if ($newId > 0 && function_exists('log_audit')) {
                    log_audit('CREATE', 'ref_angkatan', (string) $newId, null, $row);
                }
            }

            // 2. Eskalasi otomatis ref_tahun_akademik (Ganjil: Sep–Jan, Genap: Feb–Agu)
            if ($month >= 9) {
                $taTahun  = $year;
                $semester = 'ganjil';
                $mulai    = sprintf('%04d-09-01', $taTahun);
                $selesai  = sprintf('%04d-01-31', $taTahun + 1);
            } elseif ($month === 1) {
                $taTahun  = $year - 1;
                $semester = 'ganjil';
                $mulai    = sprintf('%04d-09-01', $taTahun);
                $selesai  = sprintf('%04d-01-31', $taTahun + 1);
            } else {
                $taTahun  = $year - 1;
                $semester = 'genap';
                $mulai    = sprintf('%04d-02-01', $taTahun + 1);
                $selesai  = sprintf('%04d-08-31', $taTahun + 1);
            }
            $kodeTahun = sprintf('%04d/%04d %s', $taTahun, $taTahun + 1, ucfirst($semester));

            $existingTa = $db->table('ref_tahun_akademik')
                ->where('kode_tahun', $kodeTahun)
                ->get()
                ->getRowArray();

            if (!$existingTa) {
                $db->transStart();
                $db->table('ref_tahun_akademik')->where('is_aktif', 1)->update([
                    'is_aktif'   => 0,
                    'updated_at' => $now,
                ]);
                $taRow = [
                    'kode_tahun'      => $kodeTahun,
                    'tahun'           => $taTahun,
                    'semester'        => $semester,
                    'tanggal_mulai'   => $mulai,
                    'tanggal_selesai' => $selesai,
                    'is_aktif'        => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
                $db->table('ref_tahun_akademik')->insert($taRow);
                $newTaId = (int) $db->insertID();
                $db->transComplete();

                if ($db->transStatus() && $newTaId > 0 && function_exists('log_audit')) {
                    log_audit('CREATE', 'ref_tahun_akademik', (string) $newTaId, null, $taRow);
                }
            } elseif ((int) ($existingTa['is_aktif'] ?? 0) !== 1) {
                $activeTa = $db->table('ref_tahun_akademik')
                    ->select('id, tanggal_selesai')
                    ->where('is_aktif', 1)
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->getRowArray();

                $expired = !$activeTa || (!empty($activeTa['tanggal_selesai']) && (string) $activeTa['tanggal_selesai'] < $dateStr);
                if ($expired) {
                    $db->transStart();
                    $db->table('ref_tahun_akademik')->where('is_aktif', 1)->update([
                        'is_aktif'   => 0,
                        'updated_at' => $now,
                    ]);
                    $db->table('ref_tahun_akademik')->where('id', (int) $existingTa['id'])->update([
                        'is_aktif'   => 1,
                        'updated_at' => $now,
                    ]);
                    $db->transComplete();

                    if ($db->transStatus() && function_exists('log_audit')) {
                        log_audit('UPDATE', 'ref_tahun_akademik', (string) $existingTa['id'], ['is_aktif' => 0], ['is_aktif' => 1]);
                    }
                }
            }
        } catch (\Throwable $e) {
            if (function_exists('log_message')) {
                log_message('warning', 'Gagal menyinkronkan kalender akademik otomatis: ' . $e->getMessage());
            }
        }
    }
}

if (!function_exists('ketarunaan_ensure_upload_protection')) {
    /**
     * Memastikan direktori unggahan tersedia dengan berkas .htaccess anti-eksekusi skrip.
     */
    function ketarunaan_ensure_upload_protection(string $uploadDir): void
    {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $htaccessPath = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . '.htaccess';
        if (!is_file($htaccessPath)) {
            $rules = implode("\n", [
                '# Evidence Command Center (ECC) - Upload Folder Security Hardening',
                '<IfModule mod_php7.c>',
                '    php_flag engine off',
                '</IfModule>',
                '<IfModule mod_php.c>',
                '    php_flag engine off',
                '</IfModule>',
                '<FilesMatch "\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|pl|py|cgi|sh|bash)$">',
                '    Require all denied',
                '</FilesMatch>',
                'Options -Indexes -ExecCGI',
                '',
            ]);
            @file_put_contents($htaccessPath, $rules);
        }
    }
}

if (!function_exists('ketarunaan_is_http_url')) {
    /**
     * Memastikan tautan dokumen eksternal menggunakan skema http:// atau https:// dan muat di kolom VARCHAR(255).
     */
    function ketarunaan_is_http_url(?string $url, int $maxLength = 255): bool
    {
        $clean = trim((string) $url);
        if ($clean === '' || strlen($clean) > $maxLength) {
            return false;
        }
        if (preg_match('#^https?://#i', $clean) !== 1) {
            return false;
        }

        return filter_var($clean, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('ketarunaan_resolve_sk_url')) {
    /**
     * Mengembalikan tautan aman dokumen SK: langsung URL eksternal http(s):// atau endpoint unduh berkas lama.
     */
    function ketarunaan_resolve_sk_url(?string $buktiDokumen, int $riwayatId): ?string
    {
        $clean = trim((string) $buktiDokumen);
        if ($clean === '') {
            return null;
        }

        if (ketarunaan_is_http_url($clean, 2000)) {
            return $clean;
        }

        return site_url('ketarunaan/taruna/unduh-sk/' . $riwayatId . '?inline=1');
    }
}

if (!function_exists('ketarunaan_resolve_bukti_url')) {
    /**
     * Mengembalikan tautan aman bukti rekam jejak: langsung URL eksternal http(s):// atau endpoint unduh berkas lama.
     */
    function ketarunaan_resolve_bukti_url(?string $buktiBerkas, string $modul, int $recordId, bool $inline = true): ?string
    {
        $clean = trim((string) $buktiBerkas);
        if ($clean === '') {
            return null;
        }

        if (ketarunaan_is_http_url($clean, 2000)) {
            return $clean;
        }

        $base = site_url('ketarunaan/rekam-jejak/unduh-bukti/' . rawurlencode(strtolower(trim($modul))) . '/' . $recordId);

        return $inline ? ($base . '?inline=1') : $base;
    }
}

if (!function_exists('ketarunaan_format_bukti_label')) {
    /**
     * Menghasilkan label ringkas yang ramah dibaca untuk bukti berupa tautan URL atau nama berkas lama.
     */
    function ketarunaan_format_bukti_label(?string $buktiBerkas): string
    {
        $clean = trim((string) $buktiBerkas);
        if ($clean === '') {
            return '-';
        }

        if (ketarunaan_is_http_url($clean, 2000)) {
            $host = strtolower((string) parse_url($clean, PHP_URL_HOST));
            if ($host !== '' && (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com'))) {
                return 'Tautan Google Drive';
            }

            return $host !== '' ? ('Tautan Bukti (' . $host . ')') : 'Tautan Bukti Eksternal';
        }

        return basename($clean);
    }
}

if (!function_exists('ketarunaan_csv_safe')) {
    /**
     * Melindungi sel CSV dari serangan CSV/Formula Injection (OWASP).
     */
    function ketarunaan_csv_safe(?string $value): string
    {
        $str = (string) ($value ?? '');
        if ($str !== '' && preg_match('/^[=+\-@\t\r]/', $str) === 1) {
            return "'" . $str;
        }

        return $str;
    }
}

if (!function_exists('ketarunaan_compress_foto_taruna')) {
    /**
     * Mengompres foto taruna ke format JPEG hingga ukurannya <= $maxBytes (bawaan 250 KB).
     */
    function ketarunaan_compress_foto_taruna(string $sourcePath, string $destPath, int $maxBytes = 256000): bool
    {
        if (!is_file($sourcePath)) {
            return false;
        }

        if (!function_exists('imagecreatetruecolor') || !function_exists('imagejpeg')) {
            if (filesize($sourcePath) <= $maxBytes) {
                return @copy($sourcePath, $destPath);
            }
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info[0]) || empty($info[1])) {
            return false;
        }

        $origW = (int) $info[0];
        $origH = (int) $info[1];
        $mime  = strtolower((string) ($info['mime'] ?? ''));

        $srcImg = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($sourcePath),
            'image/png'               => @imagecreatefrompng($sourcePath),
            default                   => false,
        };

        if (!$srcImg) {
            return false;
        }

        // Perbaiki orientasi kamera ponsel bila metadata EXIF tersedia
        if (in_array($mime, ['image/jpeg', 'image/jpg'], true) && function_exists('exif_read_data') && function_exists('imagerotate')) {
            $exif = @exif_read_data($sourcePath);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            $rotated = match ($orientation) {
                3 => @imagerotate($srcImg, 180, 0),
                6 => @imagerotate($srcImg, -90, 0),
                8 => @imagerotate($srcImg, 90, 0),
                default => false,
            };
            if ($rotated) {
                imagedestroy($srcImg);
                $srcImg = $rotated;
                $origW  = imagesx($srcImg);
                $origH  = imagesy($srcImg);
            }
        }

        $maxDims   = [800, 640, 480];
        $qualities = [82, 72, 62, 52, 42, 35];
        $success   = false;

        foreach ($maxDims as $maxEdge) {
            $scale = min(1.0, $maxEdge / max($origW, $origH));
            $dstW  = max(1, (int) round($origW * $scale));
            $dstH  = max(1, (int) round($origH * $scale));

            $canvas = imagecreatetruecolor($dstW, $dstH);
            $white  = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopyresampled($canvas, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $origW, $origH);

            foreach ($qualities as $q) {
                @imagejpeg($canvas, $destPath, $q);
                clearstatcache(true, $destPath);
                if (is_file($destPath) && filesize($destPath) <= $maxBytes) {
                    $success = true;
                    break;
                }
            }
            imagedestroy($canvas);
            if ($success) {
                break;
            }
        }

        imagedestroy($srcImg);

        if (!$success && is_file($destPath)) {
            @unlink($destPath);
        }

        return $success;
    }
}

if (!function_exists('ketarunaan_tracer_status_periode_badge')) {
    /**
     * Merender badge status periode Tracer Study (draf / dibuka / ditutup).
     *
     * @param string $status
     * @return string
     */
    function ketarunaan_tracer_status_periode_badge(string $status): string
    {
        $key = strtolower(trim($status));
        $map = [
            'draf'    => ['label' => 'Draf',    'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
            'dibuka'  => ['label' => 'Dibuka',  'class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'ditutup' => ['label' => 'Ditutup', 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
        ];
        $meta = $map[$key] ?? ['label' => ucfirst($key), 'class' => 'bg-light text-dark border'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem; white-space:nowrap;">%s</span>',
            esc($meta['class']),
            esc($meta['label'])
        );
    }
}

if (!function_exists('ketarunaan_is_tracer_periode_open')) {
    /**
     * Memeriksa apakah periode Tracer Study sedang terbuka untuk pengisian responden.
     *
     * @param array<string, mixed> $periode
     * @param string|null          $today Format Y-m-d
     * @return bool
     */
    function ketarunaan_is_tracer_periode_open(array $periode, ?string $today = null): bool
    {
        if (strtolower(trim((string) ($periode['status_periode'] ?? ''))) !== 'dibuka') {
            return false;
        }

        $acuan   = ($today !== null && trim($today) !== '') ? substr(trim($today), 0, 10) : date('Y-m-d');
        $mulai   = trim((string) ($periode['tanggal_mulai'] ?? ''));
        $selesai = trim((string) ($periode['tanggal_selesai'] ?? ''));

        if ($mulai !== '' && $mulai !== '0000-00-00' && $acuan < substr($mulai, 0, 10)) {
            return false;
        }
        if ($selesai !== '' && $selesai !== '0000-00-00' && $acuan > substr($selesai, 0, 10)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('ketarunaan_tracer_tipe_jawaban_catalog')) {
    /**
     * Katalog tipe pertanyaan dinamis Tracer Study.
     *
     * @return array<string, string>
     */
    function ketarunaan_tracer_tipe_jawaban_catalog(): array
    {
        return [
            'pilihan_tunggal' => 'Pilihan Tunggal (Radio / Dropdown)',
            'pilihan_ganda'   => 'Pilihan Ganda (Centang)',
            'skala_likert'    => 'Skala Penilaian (1 - 4 / 1 - 5)',
            'teks_singkat'    => 'Teks Singkat',
            'teks_panjang'    => 'Uraian / Saran',
            'angka'           => 'Angka',
            'tanggal'         => 'Tanggal',
        ];
    }
}

if (!function_exists('ketarunaan_tracer_target_responden_catalog')) {
    /**
     * Katalog sasaran responden Tracer Study (Alumni vs Pengguna Lulusan / Mitra).
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_tracer_target_responden_catalog(): array
    {
        return [
            'alumni' => [
                'label'       => 'Lulusan / Alumni',
                'short'       => 'Alumni',
                'badge_class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            ],
            'mitra'  => [
                'label'       => 'Pengguna Lulusan (Instansi)',
                'short'       => 'Pengguna Lulusan',
                'badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            ],
        ];
    }
}

if (!function_exists('ketarunaan_tracer_target_badge')) {
    /**
     * Menghasilkan badge HTML target responden Tracer Study.
     */
    function ketarunaan_tracer_target_badge(string $target): string
    {
        $key = strtolower(trim($target));
        $cat = ketarunaan_tracer_target_responden_catalog();
        $meta = $cat[$key] ?? $cat['alumni'];

        return sprintf(
            '<span class="badge rounded-pill px-3 py-1 fw-semibold %s" style="font-size:0.75rem; white-space:nowrap;">%s</span>',
            esc($meta['badge_class']),
            esc($meta['short'])
        );
    }
}

if (!function_exists('ketarunaan_tracer_parse_opsi')) {
    /**
     * Mem-parse metadata opsi_jawaban_json secara backward-compatible
     * (mendukung array list lama, object Likert lama, dan metadata kaya: dropdown, allow_other, max_scale, show_if, section_intro).
     *
     * @param mixed  $raw  JSON string atau array hasil decode
     * @param string $tipe Tipe jawaban pertanyaan
     * @return array<string, mixed>
     */
    function ketarunaan_tracer_parse_opsi($raw, string $tipe = ''): array
    {
        $decoded = [];
        if (is_array($raw)) {
            $decoded = $raw;
        } elseif (is_string($raw) && trim($raw) !== '') {
            $tmp = json_decode($raw, true);
            if (is_array($tmp)) {
                $decoded = $tmp;
            }
        }

        $isList = !empty($decoded) && array_is_list($decoded);
        $options = [];
        if ($isList) {
            foreach ($decoded as $item) {
                if (is_scalar($item) && trim((string) $item) !== '') {
                    $options[] = trim((string) $item);
                }
            }
        } elseif (isset($decoded['options']) && is_array($decoded['options'])) {
            foreach ($decoded['options'] as $item) {
                if (is_scalar($item) && trim((string) $item) !== '') {
                    $options[] = trim((string) $item);
                }
            }
        }

        $renderAs = (!$isList && isset($decoded['render_as']) && strtolower((string) $decoded['render_as']) === 'dropdown')
            ? 'dropdown'
            : 'radio';

        $allowOther = !$isList && !empty($decoded['allow_other']);

        $maxScale = 5;
        if (!$isList && isset($decoded['max_scale']) && is_numeric($decoded['max_scale'])) {
            $ms = (int) $decoded['max_scale'];
            if ($ms >= 3 && $ms <= 10) {
                $maxScale = $ms;
            }
        }

        $minLabel = (!$isList && !empty($decoded['min_label']))
            ? trim((string) $decoded['min_label'])
            : ($maxScale === 4 ? 'Kurang Baik' : 'Sangat Kurang');
        $maxLabel = (!$isList && !empty($decoded['max_label']))
            ? trim((string) $decoded['max_label'])
            : 'Sangat Baik';

        $showIf = null;
        if (!$isList && isset($decoded['show_if']) && is_array($decoded['show_if'])) {
            $siKode = strtoupper(trim((string) ($decoded['show_if']['kode'] ?? '')));
            $siVals = $decoded['show_if']['values'] ?? [];
            if (is_string($siVals) && trim($siVals) !== '') {
                $siVals = [trim($siVals)];
            }
            $cleanVals = [];
            if (is_array($siVals)) {
                foreach ($siVals as $v) {
                    if (is_scalar($v) && trim((string) $v) !== '') {
                        $cleanVals[] = trim((string) $v);
                    }
                }
            }
            if ($siKode !== '' && !empty($cleanVals)) {
                $showIf = ['kode' => $siKode, 'values' => $cleanVals];
            }
        }

        $sectionIntro = (!$isList && !empty($decoded['section_intro']))
            ? trim((string) $decoded['section_intro'])
            : null;

        $inputType = 'text';
        if (!$isList && !empty($decoded['input_type'])) {
            $it = strtolower(trim((string) $decoded['input_type']));
            if (in_array($it, ['text', 'email', 'tel'], true)) {
                $inputType = $it;
            }
        }

        $prefillKey = (!$isList && !empty($decoded['prefill_key']))
            ? trim((string) $decoded['prefill_key'])
            : null;

        $wilayahRole = (!$isList && !empty($decoded['wilayah_role']))
            ? strtolower(trim((string) $decoded['wilayah_role']))
            : null;
        if ($wilayahRole !== null && !in_array($wilayahRole, ['provinsi', 'kabupaten_kota', 'kabupaten_kota_tags'], true)) {
            $wilayahRole = null;
        }

        $wilayahParentKode = (!$isList && !empty($decoded['wilayah_parent_kode']))
            ? strtoupper(trim((string) $decoded['wilayah_parent_kode']))
            : null;

        return [
            'options'             => $options,
            'render_as'           => $renderAs,
            'allow_other'         => $allowOther,
            'min_label'           => $minLabel,
            'max_label'           => $maxLabel,
            'max_scale'           => $maxScale,
            'show_if'             => $showIf,
            'section_intro'       => $sectionIntro,
            'input_type'          => $inputType,
            'prefill_key'         => $prefillKey,
            'wilayah_role'        => $wilayahRole,
            'wilayah_parent_kode' => $wilayahParentKode,
        ];
    }
}

if (!function_exists('ketarunaan_tracer_prodi_tokens')) {
    /**
     * Memetakan pilihan Program Studi pada formulir Pengguna Lulusan menjadi token label dinamis.
     *
     * @return array{short:string, full:string, kode_prodi:?string}
     */
    function ketarunaan_tracer_prodi_tokens(string $selectedProdi): array
    {
        $up = strtoupper(trim($selectedProdi));
        if (str_contains($up, 'RSTJ')) {
            return [
                'short'      => 'RSTJ',
                'full'       => 'Sarjana Terapan Program Studi RSTJ',
                'kode_prodi' => 'RSTJ',
            ];
        }
        if (str_contains($up, 'TRO')) {
            return [
                'short'      => 'TRO',
                'full'       => 'Sarjana Terapan Program Studi TRO',
                'kode_prodi' => 'TRO',
            ];
        }
        if ($up === 'TO' || str_contains($up, 'DIPLOMA III TO') || str_contains($up, 'DIPLOMA 3 TO') || str_contains($up, '(TO)') || str_ends_with($up, ' TO')) {
            return [
                'short'      => 'TO',
                'full'       => 'Diploma III Program Studi TO',
                'kode_prodi' => 'TO',
            ];
        }

        return [
            'short'      => 'PKTJ',
            'full'       => 'Program Studi PKTJ',
            'kode_prodi' => null,
        ];
    }
}

if (!function_exists('ketarunaan_tracer_status_transisi_catalog')) {
    /**
     * Katalog status transisi lulusan pada Tracer Study.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_tracer_status_transisi_catalog(): array
    {
        return [
            'bekerja'            => ['label' => 'Bekerja',            'badge_class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'wirausaha'          => ['label' => 'Wirausaha',          'badge_class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
            'studi_lanjut'       => ['label' => 'Studi Lanjut',       'badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
            'mencari_kerja'      => ['label' => 'Mencari Kerja',      'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'belum_memungkinkan' => ['label' => 'Belum Memungkinkan', 'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
        ];
    }
}

if (!function_exists('ketarunaan_tracer_kesesuaian_bidang_catalog')) {
    /**
     * Katalog kesesuaian bidang kerja dengan program studi PKTJ.
     *
     * @return array<string, string>
     */
    function ketarunaan_tracer_kesesuaian_bidang_catalog(): array
    {
        return [
            'sangat_sesuai' => 'Sangat Sesuai',
            'sesuai'        => 'Sesuai',
            'kurang_sesuai' => 'Kurang Sesuai',
            'tidak_sesuai'  => 'Tidak Sesuai',
        ];
    }
}

if (!function_exists('ketarunaan_tracer_kesesuaian_jenjang_catalog')) {
    /**
     * Katalog kesesuaian jenjang pendidikan terhadap posisi pekerjaan.
     *
     * @return array<string, string>
     */
    function ketarunaan_tracer_kesesuaian_jenjang_catalog(): array
    {
        return [
            'lebih_tinggi' => 'Tingkat Lebih Tinggi',
            'setingkat'    => 'Setingkat',
            'lebih_rendah' => 'Tingkat Lebih Rendah',
        ];
    }
}

if (!function_exists('ketarunaan_tracer_sektor_catalog')) {
    /**
     * Katalog sektor industri pekerjaan lulusan PKTJ.
     *
     * @return array<int, string>
     */
    function ketarunaan_tracer_sektor_catalog(): array
    {
        return [
            'Instansi Pemerintah / Kemenhub / Dishub',
            'BUMN / BUMD Transportasi & Infrastruktur',
            'Perusahaan Swasta Transportasi & Logistik',
            'Industri Otomotif & Manufaktur',
            'Konsultan Rekayasa & Keselamatan Jalan',
            'Pendidikan / Penelitian',
            'Wirausaha / Mandiri',
            'Lainnya',
        ];
    }
}

if (!function_exists('ketarunaan_tracer_public_url')) {
    /**
     * Menghasilkan URL tautan singkat publik Tracer Study (contoh: domain/tracerstudy2026).
     *
     * @param string $slug
     * @param string $suffix Opsional, mis. '/verifikasi', '/simpan', '/selesai'
     * @return string
     */
    function ketarunaan_tracer_public_url(string $slug, string $suffix = ''): string
    {
        $clean = trim($slug);
        if ($clean === '') {
            return base_url('ketarunaan/tracer-study');
        }
        $sfx = $suffix !== '' ? ('/' . ltrim($suffix, '/')) : '';
        if (preg_match('/^tracer[a-z0-9\-]{2,48}$/', $clean)) {
            return base_url($clean . $sfx);
        }

        return base_url('tracer/' . urlencode($clean) . $sfx);
    }
}

if (!function_exists('ketarunaan_dosen_jafung_catalog')) {
    /**
     * Katalog jabatan fungsional dosen resmi.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_dosen_jafung_catalog(): array
    {
        return [
            'guru_besar'      => ['label' => 'Guru Besar',       'badge_class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
            'lektor_kepala'   => ['label' => 'Lektor Kepala',    'badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
            'lektor'          => ['label' => 'Lektor',           'badge_class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'asisten_ahli'    => ['label' => 'Asisten Ahli',     'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'tenaga_pengajar' => ['label' => 'Tenaga Pengajar',  'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
        ];
    }
}

if (!function_exists('ketarunaan_dosen_pendidikan_catalog')) {
    /**
     * Katalog jenjang pendidikan terakhir dosen.
     *
     * @return array<string, string>
     */
    function ketarunaan_dosen_pendidikan_catalog(): array
    {
        return [
            'S3'        => 'Doktor (S3)',
            'S2'        => 'Magister (S2)',
            'spesialis' => 'Spesialis / Profesi',
            'S1'        => 'Sarjana (S1)',
        ];
    }
}

if (!function_exists('ketarunaan_dosen_serdos_catalog')) {
    /**
     * Katalog status Sertifikasi Dosen (Serdos).
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_dosen_serdos_catalog(): array
    {
        return [
            'sudah'  => ['label' => 'Sudah Serdos', 'badge_class' => 'bg-success-subtle text-success-emphasis border border-success-subtle'],
            'proses' => ['label' => 'Proses',       'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'belum'  => ['label' => 'Belum Serdos', 'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
        ];
    }
}

if (!function_exists('ketarunaan_dosen_ikatan_catalog')) {
    /**
     * Katalog status ikatan/penugasan dosen.
     *
     * @return array<string, array<string, string>>
     */
    function ketarunaan_dosen_ikatan_catalog(): array
    {
        return [
            'dosen_tetap'      => ['label' => 'Dosen Tetap',      'badge_class' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle'],
            'dosen_luar_biasa' => ['label' => 'Dosen Luar Biasa', 'badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
            'tugas_belajar'    => ['label' => 'Tugas Belajar',    'badge_class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
            'izin_belajar'     => ['label' => 'Izin Belajar',     'badge_class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
        ];
    }
}

if (!function_exists('ketarunaan_dosen_kualifikasi_catalog')) {
    /**
     * Katalog kategori riwayat kualifikasi & rekam jejak dosen.
     *
     * @return array<string, string>
     */
    function ketarunaan_dosen_kualifikasi_catalog(): array
    {
        return [
            'pendidikan'             => 'Riwayat Pendidikan',
            'sertifikasi_kompetensi' => 'Sertifikasi Kompetensi',
            'pelatihan_strategis'    => 'Pelatihan / Diklat Strategis',
            'penugasan_khusus'       => 'Penugasan Strategis / Tim Kerja',
            'penghargaan'            => 'Penghargaan & Prestasi',
        ];
    }
}







