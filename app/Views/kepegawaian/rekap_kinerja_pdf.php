<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Laporan Rekapitulasi Kinerja Pegawai') ?></title>
    <style>
        @page {
            margin: 10mm 12mm 12mm 12mm;
            size: A4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT RESMI */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .kop-text {
            text-align: center;
        }

        .kop-instansi-1 {
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-instansi-2 {
            font-size: 8.5pt;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .kop-instansi-3 {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: 1px;
        }

        .kop-sistem {
            font-size: 8pt;
            color: #64748b;
            font-style: italic;
            margin-top: 1px;
        }

        .kop-divider {
            border-top: 2px solid #0f172a;
            border-bottom: 0.75px solid #0f172a;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        /* JUDUL & METADATA */
        .doc-title {
            text-align: center;
            font-size: 11.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .meta-table td {
            padding: 1.5px 0;
        }

        /* KPI SUMMARY BOXES */
        .kpi-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .kpi-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 5px 8px;
            text-align: center;
            width: 20%;
        }

        .kpi-title {
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
        }

        .kpi-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }

        .kpi-sub {
            font-size: 6.5pt;
            color: #64748b;
        }

        /* TABEL UTAMA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 14px;
        }

        .data-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #1e3a8a;
            font-size: 7pt;
            letter-spacing: 0.2px;
        }

        .data-table td {
            padding: 4.5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.5pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .badge-sangat-baik { background-color: #d1fae5; color: #065f46; border: 0.5px solid #a7f3d0; }
        .badge-baik { background-color: #dbeafe; color: #1e40af; border: 0.5px solid #bfdbfe; }
        .badge-butuh-perbaikan { background-color: #f1f5f9; color: #334155; border: 0.5px solid #cbd5e1; }
        .badge-kurang { background-color: #fef3c7; color: #92400e; border: 0.5px solid #fde68a; }
        .badge-sangat-kurang { background-color: #fee2e2; color: #991b1b; border: 0.5px solid #fecaca; }
        .badge-belum { background-color: #f1f5f9; color: #64748b; border: 0.5px solid #e2e8f0; }

        /* LEMBAR PENGESAHAN */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
            font-size: 8pt;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 20px;
        }

        .sign-title {
            font-weight: 600;
            margin-bottom: 45px;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .sign-nip {
            color: #475569;
            font-size: 7.5pt;
            margin-top: 1px;
        }

        /* FOOTER */
        .footer-note {
            margin-top: 8px;
            font-size: 6.5pt;
            color: #94a3b8;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="kop-instansi-1">KEMENTERIAN PERHUBUNGAN</div>
                <div class="kop-instansi-2">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA PERHUBUNGAN</div>
                <div class="kop-instansi-3">POLITEKNIK KESELAMATAN TRANSPORTASI JALAN TEGAL</div>
                <div class="kop-sistem">Sistem Informasi & Manajemen Kinerja Terpadu — Evidence Command Center (ECC)</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    <!-- JUDUL LAPORAN & METADATA -->
    <div class="doc-title">Laporan Rekapitulasi Capaian Kinerja Pegawai</div>
    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Periode Evaluasi</td>
            <td style="width: 35%;">: <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong></td>
            <td style="width: 15%; font-weight: bold;">Unit Kerja</td>
            <td style="width: 35%;">: <?= !empty($unit_filter) ? esc($unit_filter) : 'Semua Unit Kerja' ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Waktu Ekspor</td>
            <td>: <?= date('d/m/Y H:i:s') ?> WIB</td>
            <td style="font-weight: bold;">Status Data</td>
            <td>: <span style="color: #059669; font-weight: bold;">Resmi (Official ECC Report)</span></td>
        </tr>
    </table>

    <!-- KPI STATISTIC BOXES (5 METRICS) -->
    <table class="kpi-container">
        <tr>
            <td class="kpi-box">
                <div class="kpi-title">Total Pegawai</div>
                <div class="kpi-value"><?= count($rekap_kinerja) ?></div>
                <div class="kpi-sub">Pegawai Aktif</div>
            </td>
            <td style="width: 8px;"></td>
            <td class="kpi-box" style="border-left: 3px solid #059669;">
                <div class="kpi-title">Sudah Dinilai</div>
                <div class="kpi-value" style="color: #059669;"><?= esc($sudah_dinilai) ?></div>
                <div class="kpi-sub"><?= count($rekap_kinerja) > 0 ? round(($sudah_dinilai / count($rekap_kinerja)) * 100) : 0 ?>% Kepatuhan</div>
            </td>
            <td style="width: 8px;"></td>
            <td class="kpi-box" style="border-left: 3px solid #e11d48;">
                <div class="kpi-title">Belum Dinilai</div>
                <div class="kpi-value" style="color: #e11d48;"><?= esc($belum_dinilai) ?></div>
                <div class="kpi-sub"><?= count($rekap_kinerja) > 0 ? round(($belum_dinilai / count($rekap_kinerja)) * 100) : 0 ?>% Belum Terbit</div>
            </td>
            <td style="width: 8px;"></td>
            <td class="kpi-box" style="border-left: 3px solid #2563eb;">
                <div class="kpi-title">Rata Dinilai</div>
                <div class="kpi-value" style="color: #2563eb;"><?= str_replace('.', ',', round($rata_rata_dinilai ?? $rata_rata_instansi, 2)) ?>%</div>
                <div class="kpi-sub">Mutu Pegawai Dinilai</div>
            </td>
            <td style="width: 8px;"></td>
            <td class="kpi-box" style="border-left: 3px solid #475569;">
                <div class="kpi-title">Rata Total</div>
                <div class="kpi-value"><?= str_replace('.', ',', round($rata_rata_keseluruhan ?? 0, 2)) ?>%</div>
                <div class="kpi-sub">Semua Pegawai (0%)</div>
            </td>
        </tr>
    </table>

    <!-- TABEL UTAMA REKAP KINERJA -->
    <table class="data-table">
        <thead>
            <?php if ($bulan_terpilih === 'all'): ?>
                <tr>
                    <th rowspan="2" style="width: 3%;">No</th>
                    <th rowspan="2" style="width: 17%;">Nama Pegawai / NIP</th>
                    <th rowspan="2" style="width: 13%;">Jabatan</th>
                    <th rowspan="2" style="width: 11%;">Unit Kerja</th>
                    <th colspan="12" style="width: 36%;">Nilai Capaian Kinerja Bulanan (%)</th>
                    <th rowspan="2" style="width: 5%;">Total Komp.</th>
                    <th rowspan="2" style="width: 7%;">Rata-Rata Tahunan</th>
                    <th rowspan="2" style="width: 8%;">Predikat</th>
                </tr>
                <tr>
                    <th style="width: 3%;">Jan</th>
                    <th style="width: 3%;">Feb</th>
                    <th style="width: 3%;">Mar</th>
                    <th style="width: 3%;">Apr</th>
                    <th style="width: 3%;">Mei</th>
                    <th style="width: 3%;">Jun</th>
                    <th style="width: 3%;">Jul</th>
                    <th style="width: 3%;">Agu</th>
                    <th style="width: 3%;">Sep</th>
                    <th style="width: 3%;">Okt</th>
                    <th style="width: 3%;">Nov</th>
                    <th style="width: 3%;">Des</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 22%;">Nama Lengkap & NIP</th>
                    <th style="width: 16%;">Jabatan</th>
                    <th style="width: 15%;">Unit Kerja</th>
                    <th style="width: 18%;">Atasan Penilai</th>
                    <th style="width: 7%;">Komponen</th>
                    <th style="width: 8%;">Nilai (%)</th>
                    <th style="width: 10%;">Predikat</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (empty($rekap_kinerja)): ?>
                <tr>
                    <td colspan="<?= ($bulan_terpilih === 'all') ? '21' : '8' ?>" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data kinerja pegawai yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                $no = 1; 
                foreach ($rekap_kinerja as $row): 
                    $p = $row['pegawai'];
                    $score = (float)$row['rata_rata'];
                    
                    // Predikat
                    if ($row['rhk_dinilai'] == 0 && $score == 0) {
                        $predikatText = 'Belum Dinilai';
                        $badgeClass = 'badge-belum';
                    } elseif ($score >= 100) {
                        $predikatText = 'Sangat Baik';
                        $badgeClass = 'badge-sangat-baik';
                    } elseif ($score >= 80) {
                        $predikatText = 'Baik';
                        $badgeClass = 'badge-baik';
                    } elseif ($score >= 60) {
                        $predikatText = 'Butuh Perbaikan';
                        $badgeClass = 'badge-butuh-perbaikan';
                    } elseif ($score >= 50) {
                        $predikatText = 'Kurang';
                        $badgeClass = 'badge-kurang';
                    } else {
                        $predikatText = 'Sangat Kurang';
                        $badgeClass = 'badge-sangat-kurang';
                    }
                ?>
                    <?php if ($bulan_terpilih === 'all'): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold" style="color: #0f172a;"><?= esc($p['nama_lengkap']) ?></div>
                                <div style="font-size: 6.8pt; color: #64748b;">NIP. <?= esc($p['nip'] ?? '-') ?></div>
                            </td>
                            <td><?= esc($p['jabatan'] ?? '-') ?></td>
                            <td><?= esc($p['unit'] ?? '-') ?></td>
                            
                            <!-- 12 Bulan -->
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <?php $v = $row['rata_rata_per_bulan'][$m]; ?>
                                <td class="text-center" style="font-size: 6.8pt;">
                                    <?= ($v !== null) ? str_replace('.', ',', round($v, 1)) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            
                            <td class="text-center"><?= $row['jumlah_komponen'] ?></td>
                            <td class="text-center fw-bold" style="color: #1e3a8a;">
                                <?= ($score > 0) ? str_replace('.', ',', round($score, 2)) : '-' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $predikatText ?></span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold" style="color: #0f172a;"><?= esc($p['nama_lengkap']) ?></div>
                                <div style="font-size: 6.8pt; color: #64748b;">NIP. <?= esc($p['nip'] ?? '-') ?></div>
                            </td>
                            <td><?= esc($p['jabatan'] ?? '-') ?></td>
                            <td><?= esc($p['unit'] ?? '-') ?></td>
                            <td><?= esc($p['atasan_nama'] ?? '-') ?></td>
                            <td class="text-center">
                                <?= $row['rhk_dinilai'] ?> / <?= $row['jumlah_komponen'] ?>
                            </td>
                            <td class="text-center fw-bold" style="color: #1e3a8a;">
                                <?= ($row['rhk_dinilai'] > 0) ? str_replace('.', ',', round($score, 2)) : '-' ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?>"><?= $predikatText ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN / TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td style="text-align: left;">
                <div class="sign-title">
                    Pengelola Kepegawaian,<br>
                    Politeknik Keselamatan Transportasi Jalan
                </div>
                <div class="sign-name">( ............................................................ )</div>
                <div class="sign-nip">NIP. .....................................................</div>
            </td>
            <td style="text-align: right;">
                <div class="sign-title">
                    Tegal, <?= date('d') ?> <?= $bulan_indo[(int)date('n') - 1] ?> <?= date('Y') ?><br>
                    Mengetahui,<br>
                    <strong>Direktur / Wakil Direktur</strong>
                </div>
                <div class="sign-name">( ............................................................ )</div>
                <div class="sign-nip">NIP. .....................................................</div>
            </td>
        </tr>
    </table>

    <!-- FOOTER NOTE -->
    <div class="footer-note">
        * Dokumen ini digenerate secara otomatis oleh Evidence Command Center (ECC) Politeknik Keselamatan Transportasi Jalan. Dicetak pada <?= date('d/m/Y H:i:s') ?> WIB.
    </div>

</body>
</html>
