<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Detail Kinerja') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Detail Kinerja</h1>
        <h5 class="text-muted">Tim/Unit/Pokja: <?= esc($user['nama_lengkap']) ?> | Tahun: <?= esc($tahun_terpilih) ?></h5>
    </div>
    <div>
        <!-- TOMBOL-TOMBOL BARU -->
        <a href="<?= site_url('admin/monitoring/excel/' . $user['id'] . '/' . $tahun_terpilih) ?>" class="btn btn-success"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</a>
        <a href="<?= site_url('admin/monitoring/pdf/' . $user['id'] . '/' . $tahun_terpilih) ?>" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-2"></i> Export PDF</a>
        <a href="<?= site_url('admin/monitoring') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
    </div>
</div>

<?php if (!empty($rencana_kinerja)): ?>
    <?php foreach ($rencana_kinerja as $rencana): ?>
        <?php
        $target_bulanan = json_decode($rencana['target_bulanan'], true) ?? array_fill(0, 12, 0);
        $realisasi_bulanan = json_decode($rencana['realisasi_bulanan'], true) ?? array_fill(0, 12, null);
        $total_realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
        $target_utama = (float)$rencana['target_utama'];
        $persentase_capaian = ($target_utama > 0) ? ($total_realisasi / $target_utama) * 100 : 0;
        ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?= esc($rencana['indikator_kinerja']) ?></h5>
                <small class="text-muted"><?= esc($rencana['sasaran_program']) ?></small>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6>Ringkasan Tahunan</h6>
                        <p class="mb-1"><strong>Target:</strong> <?= esc($target_utama) ?> <?= esc($rencana['satuan']) ?></p>
                        <p class="mb-1"><strong>Realisasi:</strong> <?= $total_realisasi ?> <?= esc($rencana['satuan']) ?></p>
                        <hr>
                        <p class="mb-1"><strong>Capaian:</strong></p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= min(100, $persentase_capaian) ?>%;" aria-valuenow="<?= $persentase_capaian ?>" aria-valuemin="0" aria-valuemax="100"><?= round($persentase_capaian, 2) ?>%</div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6>Detail Bulanan (Target / Realisasi)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <th><?= bulan_indo($i, true) ?></th>
                                        <?php endfor; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ($target_bulanan as $target): ?>
                                            <td><?= $target ?? 0 ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach ($realisasi_bulanan as $realisasi): ?>
                                            <td class="fw-bold"><?= $realisasi ?? '-' ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        Tidak ditemukan data Rencana Kinerja untuk Tim/Unit/Pokja ini pada tahun <?= esc($tahun_terpilih) ?>.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>