<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pengaturan Sistem<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Pengaturan Sistem
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-sliders me-2"></i> Konfigurasi Kunci & Batas Waktu</h6>
    </div>
    <div class="card-body">
        
        <form action="<?= site_url('settings/store') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 30%;">Pengaturan</th>
                            <th style="width: 45%;">Deskripsi</th>
                            <th style="width: 20%;">Nilai (Angka/Tanggal)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($settings as $index => $setting): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td class="fw-bold"><?= esc($setting['setting_name']) ?></td>
                                <td class="text-muted small"><?= esc($setting['description']) ?></td>
                                <td>
                                    <input type="number" name="settings[<?= esc($setting['setting_key']) ?>]" class="form-control" value="<?= esc($setting['setting_value']) ?>" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Simpan Pengaturan</button>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
