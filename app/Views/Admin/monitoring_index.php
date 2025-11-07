<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Monitoring Kinerja') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Monitoring Kinerja Tim/Unit/Pokja</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Pilih Tim/Unit/Pokja dan Tahun</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="unit" class="form-label">Pilih Tim/Unit/Pokja</label>
                <select class="form-select" id="unit" name="unit" onchange="this.form.submit()" required>
                    <option value="">-- Pilih salah satu --</option>
                    <?php foreach ($unit_pokja as $unit): ?>
                        <option value="<?= $unit['id']; ?>" <?= ($selectedUserId == $unit['id']) ? 'selected' : '' ?>><?= esc($unit['nama_lengkap']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tahun" class="form-label">Pilih Tahun Anggaran</label>
                <select class="form-select" id="tahun" name="tahun" onchange="this.form.submit()" required>
                     <option value="">-- Pilih Tahun --</option>
                     <?php foreach ($daftar_tahun as $item): ?>
                        <option value="<?= $item['tahun_anggaran']; ?>" <?= ($selectedYear == $item['tahun_anggaran']) ? 'selected' : '' ?>><?= esc($item['tahun_anggaran']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================== -->
<!-- BAGIAN DETAIL (HANYA MUNCUL JIKA USER DAN TAHUN SUDAH DIPILIH) -->
<!-- ========================================================== -->
<?php if ($user && $selectedYear): ?>
<hr class="my-5">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Detail Kinerja</h2>
        <h5 class="text-muted">Tim/Unit/Pokja: <?= esc($user['nama_lengkap']) ?> | Tahun: <?= esc($selectedYear) ?></h5>
    </div>
    <div>
        <a href="<?= site_url('admin/monitoring/excel/' . $user['id'] . '/' . $selectedYear) ?>" class="btn btn-success"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</a>
        <a href="<?= site_url('admin/monitoring/pdf/' . $user['id'] . '/' . $selectedYear) ?>" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-2"></i> Export PDF</a>
    </div>
</div>

<?php if (!empty($rencana_kinerja)): ?>
    <?php foreach ($rencana_kinerja as $rencana): ?>
        <?php
            // PERBAIKAN: Hapus json_decode() karena Model sudah mengubahnya jadi array
            $target_bulanan = $rencana['target_bulanan'] ?? array_fill(0, 12, 0);
            $realisasi_bulanan = $rencana['realisasi_bulanan'] ?? array_fill(0, 12, null);
            
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
                                        <?php foreach($target_bulanan as $target): ?>
                                            <td><?= $target ?? 0 ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <tr>
                                        <?php foreach($realisasi_bulanan as $realisasi): ?>
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
        Tidak ditemukan data Rencana Kinerja untuk Tim/Unit/Pokja ini pada tahun <?= esc($selectedYear) ?>.
    </div>
<?php endif; ?>
<?php endif; ?>

<?= $this->endSection() ?>