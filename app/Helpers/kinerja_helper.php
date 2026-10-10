<?php

/**
 * Helper Kinerja ECC (Evidence Command Center)
 * Standarisasi perhitungan predikat kinerja, ambang batas resmi, dan rekap komponen.
 */

if (!function_exists('get_predikat_thresholds')) {
    /**
     * Konfigurasi resmi ambang batas predikat kinerja ECC.
     * Standar resmi:
     * - Sangat Baik: > 100% s.d. 150%
     * - Baik: > 90% s.d. 100%
     * - Butuh Perbaikan: > 75% s.d. 90%
     * - Kurang: > 25% s.d. 75%
     * - Sangat Kurang: <= 25%
     * - Belum Dinilai: 0% / belum dinilai / NULL
     *
     * @return array<string, array{min: float, max: float, label: string, badge: string, badge_class: string, text_class: string, color: string}>
     */
    function get_predikat_thresholds(): array
    {
        return [
            'sangat_kurang' => [
                'min'         => 0.0,
                'max'         => 25.0,
                'label'       => 'Sangat Kurang',
                'badge'       => 'danger',
                'badge_class' => 'bg-danger',
                'text_class'  => 'danger',
                'color'       => 'danger',
            ],
            'kurang' => [
                'min'         => 25.0,
                'max'         => 75.0,
                'label'       => 'Kurang',
                'badge'       => 'warning text-dark',
                'badge_class' => 'bg-warning text-dark',
                'text_class'  => 'warning text-dark',
                'color'       => 'warning text-dark',
            ],
            'butuh_perbaikan' => [
                'min'         => 75.0,
                'max'         => 90.0,
                'label'       => 'Butuh Perbaikan',
                'badge'       => 'info text-dark',
                'badge_class' => 'bg-info text-dark',
                'text_class'  => 'info text-dark',
                'color'       => 'info text-dark',
            ],
            'baik' => [
                'min'         => 90.0,
                'max'         => 100.0,
                'label'       => 'Baik',
                'badge'       => 'primary',
                'badge_class' => 'bg-primary',
                'text_class'  => 'primary',
                'color'       => 'primary',
            ],
            'sangat_baik' => [
                'min'         => 100.0,
                'max'         => 150.0,
                'label'       => 'Sangat Baik',
                'badge'       => 'success',
                'badge_class' => 'bg-success',
                'text_class'  => 'success',
                'color'       => 'success',
            ],
        ];
    }
}

if (!function_exists('hitung_predikat_kinerja')) {
    /**
     * Menghitung predikat, label, dan kelas badge kinerja berdasarkan nilai numerik capaian.
     *
     * @param float|int|string|null $nilai Nilai capaian (0.00 s.d. 150.00)
     * @param bool $isTerbit Apakah status penilaian sudah sah / diterbitkan
     * @return array{label: string, predikat: string, badge: string, badge_class: string, text_class: string, color: string}
     */
    function hitung_predikat_kinerja($nilai, bool $isTerbit = true): array
    {
        if ($nilai === null || !$isTerbit || $nilai === '' || !is_numeric($nilai)) {
            return [
                'label'       => 'Belum Dinilai',
                'predikat'    => '-',
                'badge'       => 'secondary',
                'badge_class' => 'bg-secondary',
                'text_class'  => 'secondary',
                'color'       => 'secondary',
            ];
        }

        $n = (float)$nilai;

        if ($n <= 25.0) {
            return [
                'label'       => 'Sangat Kurang',
                'predikat'    => 'Sangat Kurang',
                'badge'       => 'danger',
                'badge_class' => 'bg-danger',
                'text_class'  => 'danger',
                'color'       => 'danger',
            ];
        }

        if ($n <= 75.0) {
            return [
                'label'       => 'Kurang',
                'predikat'    => 'Kurang',
                'badge'       => 'warning text-dark',
                'badge_class' => 'bg-warning text-dark',
                'text_class'  => 'warning text-dark',
                'color'       => 'warning text-dark',
            ];
        }

        if ($n <= 90.0) {
            return [
                'label'       => 'Butuh Perbaikan',
                'predikat'    => 'Butuh Perbaikan',
                'badge'       => 'info text-dark',
                'badge_class' => 'bg-info text-dark',
                'text_class'  => 'info text-dark',
                'color'       => 'info text-dark',
            ];
        }

        if ($n <= 100.0) {
            return [
                'label'       => 'Baik',
                'predikat'    => 'Baik',
                'badge'       => 'primary',
                'badge_class' => 'bg-primary',
                'text_class'  => 'primary',
                'color'       => 'primary',
            ];
        }

        return [
            'label'       => 'Sangat Baik',
            'predikat'    => 'Sangat Baik',
            'badge'       => 'success',
            'badge_class' => 'bg-success',
            'text_class'  => 'success',
            'color'       => 'success',
        ];
    }
}

if (!function_exists('hitung_rekap_komponen_kinerja')) {
    /**
     * Kontrak terpadu untuk menghitung ringkasan rekap komponen kinerja bulanan (RHK + Tugas Tambahan).
     *
     * @param array<int, array<string, mixed>> $targetRows Daftar target RHK bulanan
     * @param array<int, array<string, mixed>> $tambahanRows Daftar kegiatan tugas tambahan bulanan
     * @param bool $onlyTerbit Jika true, hanya memperhitungkan baris yang berstatus 'terbit'
     * @return array{
     *     total_rhk: int,
     *     total_tambahan: int,
     *     has_tambahan: bool,
     *     score_tambahan: ?float,
     *     total_komponen: int,
     *     jml_dinilai: int,
     *     total_nilai: float,
     *     rata_rata: float,
     *     rata_rata_nullable: ?float,
     *     is_lengkap: bool,
     *     predikat: string,
     *     predikat_label: string,
     *     badge_color: string,
     *     badge_class: string,
     *     warna_score: string,
     *     text_class: string
     * }
     */
    function hitung_rekap_komponen_kinerja(array $targetRows, array $tambahanRows, bool $onlyTerbit = true): array
    {
        $totalRhk = count($targetRows);
        $totalNilai = 0.0;
        $jmlDinilai = 0;

        foreach ($targetRows as $rd) {
            $isEval = $onlyTerbit
                ? (($rd['status_penilaian'] ?? '') === 'terbit' && $rd['nilai_capaian'] !== null && trim((string)$rd['nilai_capaian']) !== '')
                : ($rd['nilai_capaian'] !== null && trim((string)$rd['nilai_capaian']) !== '');

            if ($isEval) {
                $jmlDinilai++;
                $totalNilai += (float)$rd['nilai_capaian'];
            }
        }

        $totalTambahanKegiatan = count($tambahanRows);
        $hasTambahan = $totalTambahanKegiatan > 0;
        $scoreTambahan = null;

        if ($hasTambahan) {
            foreach ($tambahanRows as $tmb) {
                $isTmbEval = $onlyTerbit
                    ? (($tmb['status_penilaian'] ?? '') === 'terbit' && $tmb['nilai_capaian'] !== null && trim((string)$tmb['nilai_capaian']) !== '')
                    : ($tmb['nilai_capaian'] !== null && trim((string)$tmb['nilai_capaian']) !== '');

                if ($isTmbEval) {
                    $scoreTambahan = (float)$tmb['nilai_capaian'];
                    break;
                }
            }

            if ($scoreTambahan !== null) {
                $jmlDinilai++;
                $totalNilai += $scoreTambahan;
            }
        }

        $totalKomponen = $totalRhk + ($hasTambahan ? 1 : 0);
        $rataRata = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0.0;
        $rataRataNullable = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : null;
        $isLengkap = ($totalKomponen > 0 && $jmlDinilai >= $totalKomponen);

        $predikatInfo = hitung_predikat_kinerja($rataRataNullable, $jmlDinilai > 0);

        return [
            'total_rhk'          => $totalRhk,
            'total_tambahan'     => $totalTambahanKegiatan,
            'has_tambahan'       => $hasTambahan,
            'score_tambahan'     => $scoreTambahan,
            'total_komponen'     => $totalKomponen,
            'jml_dinilai'        => $jmlDinilai,
            'total_nilai'        => $totalNilai,
            'rata_rata'          => $rataRata,
            'rata_rata_nullable' => $rataRataNullable,
            'is_lengkap'         => $isLengkap,
            'predikat'           => $predikatInfo['predikat'],
            'predikat_label'     => $predikatInfo['label'],
            'badge_color'        => $predikatInfo['badge'],
            'badge_class'        => $predikatInfo['badge_class'],
            'warna_score'        => $predikatInfo['color'],
            'text_class'         => $predikatInfo['text_class'],
        ];
    }
}
