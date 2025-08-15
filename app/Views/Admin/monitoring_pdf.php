<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h3 { text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Detail Kinerja</h1>
    <h3>Tim/Unit/Pokja: <?= esc($user['nama_lengkap']) ?> | Tahun: <?= esc($tahun_terpilih) ?></h3>
    <hr>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Indikator Kinerja</th>
                <th>Target Tahunan</th>
                <th>Total Realisasi</th>
                <th>Capaian (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rencana_kinerja)): ?>
                <?php $no = 1; foreach ($rencana_kinerja as $rencana): ?>
                    <?php
                        $realisasi_bulanan = json_decode($rencana['realisasi_bulanan'], true) ?? [];
                        $total_realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
                        $target_utama = (float)$rencana['target_utama'];
                        $persentase_capaian = ($target_utama > 0) ? ($total_realisasi / $target_utama) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= esc($rencana['indikator_kinerja']); ?></td>
                        <td><?= esc($target_utama) . ' ' . esc($rencana['satuan']); ?></td>
                        <td><?= $total_realisasi; ?></td>
                        <td><?= round($persentase_capaian, 2); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
