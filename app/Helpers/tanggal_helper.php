<?php

/**
 * Mengambil nama bulan dalam Bahasa Indonesia.
 *
 * @param int $nomorBulan Nomor bulan dari 1 (Januari) hingga 12 (Desember).
 * @param bool $singkat Apakah akan mengembalikan nama singkat (e.g., Jan).
 * @return string Nama bulan.
 */
function bulan_indo(int $nomorBulan, bool $singkat = false): string
{
    $daftarBulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    if ($singkat) {
        return substr($daftarBulan[$nomorBulan] ?? '', 0, 3);
    }

    return $daftarBulan[$nomorBulan] ?? '';
}

if (!function_exists('format_nama_gelar')) {
    /**
     * Format nama menjadi huruf kapital dan mempertahankan gelar di belakang koma.
     *
     * @param string|null $text
     * @return string
     */
    function format_nama_gelar(?string $text): string
    {
        if (empty($text)) return '-';
        $parts = explode(',', $text, 2);
        $nama = strtoupper(trim($parts[0]));
        $gelar = isset($parts[1]) ? ',' . $parts[1] : '';
        return $nama . $gelar;
    }
}
