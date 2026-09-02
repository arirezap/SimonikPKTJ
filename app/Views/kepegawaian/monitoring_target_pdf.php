<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Laporan Monitoring Target Kinerja Bulanan Pegawai') ?></title>
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

        /* STATISTIK RINGKAS */
        .kpi-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .kpi-box {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: center;
            background-color: #f8fafc;
            border-radius: 4px;
        }

        .kpi-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
        }

        .kpi-label {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* TABEL DATA UTAMA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 15px;
        }

        .data-table thead th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 5px 6px;
            border: 1px solid #334155;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .data-table tbody td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        /* TANDA TANGAN */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .ttd-table td {
            font-size: 8pt;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="kop-instansi-1">KEMENTERIAN PERHUBUNGAN</div>
                <div class="kop-instansi-2">BADAN PENGEMBANGAN SUMBER DAYA MANUSIA PERHUBUNGAN</div>
                <div class="kop-instansi-3">POLITEKNIK KESELAMATAN TRANSPORTASI JALAN</div>
                <div class="kop-sistem">Evidence Command Center (ECC) - Modul Monitoring Kepegawaian</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    <!-- JUDUL LAPORAN -->
    <div class="doc-title">LAPORAN MONITORING TARGET KINERJA BULANAN PEGAWAI</div>

    <!-- METADATA -->
    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Periode Pemantauan</td>
            <td style="width: 35%;">: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></td>
            <td style="width: 18%; font-weight: bold;">Filter Unit Kerja</td>
            <td style="width: 32%;">: <?= !empty($unit_filter) ? esc($unit_filter) : 'Semua Unit Kerja' ?></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Tanggal Cetak</td>
            <td>: <?= date('d/m/Y H:i:s') ?> WIB</td>
            <td style="font-weight: bold;">Total Pegawai Terdaftar</td>
            <td>: <?= esc($stat_total) ?> Orang</td>
        </tr>
    </table>

    <!-- KPI BOXES -->
    <table class="kpi-container">
        <tr>
            <td style="width: 20%; padding: 0 3px;">
                <div class="kpi-box">
                    <div class="kpi-value"><?= esc($stat_total) ?></div>
                    <div class="kpi-label">Total Pegawai</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 3px;">
                <div class="kpi-box" style="background-color: #ecfdf5; border-color: #a7f3d0;">
                    <div class="kpi-value" style="color: #065f46;"><?= esc($stat_kirim) ?></div>
                    <div class="kpi-label" style="color: #047857;">Sudah Mengirim</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 3px;">
                <div class="kpi-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-value" style="color: #166534;"><?= esc($stat_setuju) ?></div>
                    <div class="kpi-label" style="color: #15803d;">Sudah Disetujui</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 3px;">
                <div class="kpi-box" style="background-color: #fffbeb; border-color: #fde68a;">
                    <div class="kpi-value" style="color: #92400e;"><?= esc($stat_draft) ?></div>
                    <div class="kpi-label" style="color: #b45309;">Draf (Belum Dikirim)</div>
                </div>
            </td>
            <td style="width: 20%; padding: 0 3px;">
                <div class="kpi-box" style="background-color: #fef2f2; border-color: #fecaca;">
                    <div class="kpi-value" style="color: #991b1b;"><?= esc($stat_kosong) ?></div>
                    <div class="kpi-label" style="color: #b91c1c;">Belum Mengisi</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA PEGAWAI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 180px;">Nama Pegawai & NIP</th>
                <th style="width: 120px;">Jabatan</th>
                <th style="width: 130px;">Unit Kerja</th>
                <th style="width: 140px;">Atasan Penyetuju</th>
                <th style="width: 55px;">Target</th>
                <th style="width: 100px;">Status Pengiriman</th>
                <th style="width: 100px;">Status Persetujuan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rekap_target)): ?>
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data target kinerja pegawai yang sesuai dengan filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($rekap_target as $idx => $r): ?>
                    <?php 
                        $u = $r['user'];
                        $at = $r['atasan'];
                        $stKirim = $r['status_pengiriman'];
                        $stAppr = $r['status_persetujuan'];

                        $stKirimText = 'Belum Mengisi';
                        if ($stKirim === 'disetujui') $stKirimText = 'Disetujui';
                        elseif ($stKirim === 'terkirim') $stKirimText = 'Terkirim';
                        elseif ($stKirim === 'sebagian_disetujui') $stKirimText = 'Sebagian Disetujui';
                        elseif ($stKirim === 'draft') $stKirimText = 'Draf';

                        $stApprText = 'Belum Mengisi';
                        if ($stAppr === 'disetujui') $stApprText = 'Disetujui Atasan';
                        elseif ($stAppr === 'menunggu_persetujuan') $stApprText = 'Menunggu Persetujuan';
                        elseif ($stAppr === 'sebagian_disetujui') $stApprText = 'Sebagian Disetujui';
                        elseif ($stAppr === 'draft') $stApprText = 'Draf';
                    ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td>
                            <div class="fw-bold"><?= esc($u['nama_lengkap']) ?></div>
                            <div style="font-size: 7pt; color: #64748b;">NIP: <?= !empty($u['nip']) ? esc($u['nip']) : '-' ?></div>
                        </td>
                        <td><?= !empty($u['jabatan']) ? esc($u['jabatan']) : '-' ?></td>
                        <td><?= !empty($u['unit']) ? esc($u['unit']) : '-' ?></td>
                        <td>
                            <?php if ($u['role'] === 'direktur'): ?>
                                <em>Auto-Approve (Direktur)</em>
                            <?php elseif (!empty($at)): ?>
                                <?= esc($at['nama_lengkap']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-bold"><?= esc($r['total_rhk']) ?> RHK</td>
                        <td class="text-center"><?= esc($stKirimText) ?></td>
                        <td class="text-center"><?= esc($stApprText) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN -->
    <table class="ttd-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <div>Tegal, <?= date('d') ?> <?= esc($nama_bulan_ttd ?? $nama_bulan) ?> <?= date('Y') ?></div>
                <div style="font-weight: bold; margin-top: 2px;">Tim Kepegawaian PKTJ</div>
                <div style="height: 45px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">Subbagian Kepegawaian & Tata Usaha</div>
                <div style="font-size: 7.5pt; color: #475569;">Politeknik Keselamatan Transportasi Jalan</div>
            </td>
        </tr>
    </table>

</body>
</html>
