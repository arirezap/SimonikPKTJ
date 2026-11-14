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
            return '<span class="badge rounded-pill px-3 py-1 fw-semibold" style="background-color:#eff6ff; color:#0284c7; border:1px solid #bfdbfe; font-size:0.75rem;">Tim</span>';
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
        $linesPdf[] = "0 -28 Td (Dokumen ini tercatat dan terhubung pada basis data ECC 2.0.) Tj ET";

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



