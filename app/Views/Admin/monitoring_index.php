<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Monitoring Kinerja') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Custom Card Style */
    .monitoring-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .monitoring-card:hover {
        transform: translateY(-2px);
    }
    
    /* Tabel Bulanan */
    .table-monthly th {
        background-color: #f8f9fa;
        font-size: 0.85rem;
        vertical-align: middle;
    }
    .table-monthly td {
        font-size: 0.9rem;
        vertical-align: middle;
    }
    
    /* Progress Bar Custom */
    .progress {
        background-color: #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .progress-bar {
        font-weight: bold;
        font-size: 0.8rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Monitoring Kinerja</h3>
        <p class="text-muted mb-0">Pantau target dan realisasi kinerja Tim/Unit/Pokja.</p>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-5">
    <div class="card-body p-4">
        <h6 class="card-title fw-bold text-primary mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Data</h6>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="unit" class="form-label fw-semibold small text-muted">Pilih Tim/Unit/Pokja</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-people"></i></span>
                    <select class="form-select border-start-0 ps-0" id="unit" name="unit" onchange="this.form.submit()" required>
                        <option value="">-- Pilih User --</option>
                        <?php foreach ($unit_pokja as $unit): ?>
                            <option value="<?= $unit['id']; ?>" <?= ($selectedUserId == $unit['id']) ? 'selected' : '' ?>><?= esc($unit['nama_lengkap']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <label for="tahun" class="form-label fw-semibold small text-muted">Pilih Tahun Anggaran</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                    <select class="form-select border-start-0 ps-0" id="tahun" name="tahun" onchange="this.form.submit()" required>
                         <option value="">-- Pilih Tahun --</option>
                         <?php foreach ($daftar_tahun as $item): ?>
                            <option value="<?= $item['tahun_anggaran']; ?>" <?= ($selectedYear == $item['tahun_anggaran']) ? 'selected' : '' ?>><?= esc($item['tahun_anggaran']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <a href="<?= site_url('admin/monitoring') ?>" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<?php if ($user && $selectedYear): ?>
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-person-badge text-success me-2"></i><?= esc($user['nama_lengkap']) ?></h5>
            <span class="badge bg-light text-dark border me-2">Tahun: <?= esc($selectedYear) ?></span>
            <span class="badge bg-light text-dark border">Role: <?= strtoupper(esc($user['role'])) ?></span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/monitoring/excel/' . $user['id'] . '/' . $selectedYear) ?>" class="btn btn-success btn-sm shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="<?= site_url('admin/monitoring/pdf/' . $user['id'] . '/' . $selectedYear) ?>" class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <?php if (!empty($rencana_kinerja)): ?>
        <?php foreach ($rencana_kinerja as $rencana): ?>
            <?php
                $target_bulanan = $rencana['target_bulanan'] ?? array_fill(0, 12, 0);
                $realisasi_bulanan = $rencana['realisasi_bulanan'] ?? array_fill(0, 12, null);
                
                $total_realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
                $target_utama = (float)$rencana['target_utama'];
                $persentase_capaian = ($target_utama > 0) ? ($total_realisasi / $target_utama) * 100 : 0;
                
                // Warna progress bar
                $progress_color = 'bg-danger';
                if($persentase_capaian >= 50) $progress_color = 'bg-warning';
                if($persentase_capaian >= 80) $progress_color = 'bg-info';
                if($persentase_capaian >= 100) $progress_color = 'bg-success';
            ?>
            
            <div class="card monitoring-card mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-bold text-primary mb-1"><i class="bi bi-check-circle-fill me-2"></i><?= esc($rencana['indikator_kinerja']) ?></h6>
                            <p class="text-muted small mb-0"><i class="bi bi-bullseye me-1"></i> Sasaran: <?= esc($rencana['sasaran_program']) ?></p>
                        </div>
                        <span class="badge bg-light text-dark border"><?= esc($rencana['satuan']) ?></span>
                    </div>
                </div>
                
                <div class="card-body px-4 pb-4">
                    <div class="row g-4">
                        <div class="col-lg-4 border-end-lg">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark mb-3">Ringkasan Tahunan</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Target Utama:</span>
                                    <span class="fw-bold"><?= number_format($target_utama, 0, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Total Realisasi:</span>
                                    <span class="fw-bold text-primary"><?= number_format($total_realisasi, 0, ',', '.') ?></span>
                                </div>
                                
                                <label class="small fw-bold mb-1">Capaian Akhir</label>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?= $progress_color ?> progress-bar-striped progress-bar-animated" role="progressbar" 
                                         style="width: <?= min(100, $persentase_capaian) ?>%;" 
                                         aria-valuenow="<?= $persentase_capaian ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($persentase_capaian, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <h6 class="fw-bold text-dark mb-3">Detail Bulanan</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center table-monthly mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start" style="width: 100px;">Bulan</th>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <th><?= $i ?></th>
                                            <?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start fw-bold text-muted bg-light">Target</td>
                                            <?php foreach($target_bulanan as $target): ?>
                                                <td class="<?= ($target > 0) ? 'bg-warning bg-opacity-10' : '' ?>">
                                                    <?= $target == 0 ? '<span class="text-muted opacity-25">-</span>' : $target ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-bold text-muted bg-light">Realisasi</td>
                                            <?php foreach($realisasi_bulanan as $realisasi): ?>
                                                <td class="<?= ($realisasi > 0) ? 'fw-bold text-primary' : '' ?>">
                                                    <?= ($realisasi === null || $realisasi == 0) ? '<span class="text-muted opacity-25">-</span>' : $realisasi ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2 text-end">
                                <small class="text-muted fst-italic" style="font-size: 0.75rem;">* Angka 1-12 merepresentasikan bulan Januari s.d Desember</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    
    <?php else: ?>
        <div class="alert alert-info text-center py-5 border-0 shadow-sm">
            <i class="bi bi-inbox-fill display-4 text-info mb-3 d-block"></i>
            <h5 class="fw-bold">Data Tidak Ditemukan</h5>
            <p class="text-muted">Belum ada Rencana Kinerja yang diinput oleh <strong><?= esc($user['nama_lengkap']) ?></strong> pada tahun <strong><?= esc($selectedYear) ?></strong>.</p>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="text-center py-5 text-muted opacity-50">
        <i class="bi bi-search display-1 mb-3"></i>
        <p class="fs-5">Silakan pilih <strong>Tim/Unit/Pokja</strong> dan <strong>Tahun</strong> di atas untuk melihat data.</p>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>