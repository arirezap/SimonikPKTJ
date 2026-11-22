<?php
/**
 * Header, Flash Alerts, Banner Kasus, dan Navigasi Wizard 4 Tahap Modul Pelanggaran.
 * Parameter yang diharapkan: $activeStep (1|2|3|4), $pageTitle, $pageSubtitle, $kasus (array).
 */
$activeStep  = (int) ($activeStep ?? 1);
$kasus       = $kasus ?? [];
$idKasus     = (int) ($kasus['id'] ?? 0);
$rawValidasi = (string) ($kasus['status_validasi'] ?? 'draf');
$stValidasi  = ($rawValidasi === 'perlu_perbaikan') ? 'dikembalikan' : $rawValidasi;
?>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="text-decoration-none text-secondary">Pelanggaran</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($pageTitle ?? 'Kelola Kasus') ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1"><?= esc($pageTitle ?? 'Kelola Kasus') ?></h1>
        <p class="text-secondary mb-0 small"><?= esc($pageSubtitle ?? '') ?></p>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= esc($idKasus > 0 ? base_url('ketarunaan/pelanggaran/detail/' . $idKasus) : base_url('ketarunaan/pelanggaran')) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
            <i class="bi bi-chevron-left" aria-hidden="true"></i> <?= $idKasus > 0 ? 'Kembali ke Detail Kasus' : 'Kembali ke Daftar Kasus' ?>
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc(session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if ($idKasus > 0): ?>
    <!-- Kartu Ringkas Kasus -->
    <div class="bento-card ecc-pl-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1 ecc-num"><?= esc((string) ($kasus['nomor_perkara'] ?? '-')) ?></h2>
                    <div class="text-secondary small">
                        <?= esc((string) ($kasus['display_nama'] ?? ($kasus['nama_lengkap'] ?? '-'))) ?>
                        &bull;
                        <span class="ecc-num"><?= esc((string) ($kasus['display_nomor_taruna'] ?? ($kasus['nomor_taruna'] ?? '-'))) ?></span>
                        &bull;
                        <?= esc((string) ($kasus['jenis_pelanggaran'] ?? '-')) ?>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?= ketarunaan_penetapan_badge((string) ($kasus['status_penetapan'] ?? 'dugaan')) ?>
                <?= ketarunaan_penyelesaian_badge((string) ($kasus['status_penyelesaian'] ?? 'proses')) ?>
                <?= ketarunaan_validasi_badge($stValidasi) ?>
            </div>
        </div>

        <?php if ($rawValidasi === 'perlu_perbaikan' && !empty($kasus['catatan_validasi'])): ?>
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 px-3 py-2 small" role="alert">
                <strong class="d-block text-dark">Catatan Perbaikan Validator</strong>
                <div class="text-dark mt-1"><?= esc((string) $kasus['catatan_validasi']) ?></div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Navigasi Wizard 4 Tahap -->
<nav class="ecc-wizard-nav" aria-label="Tahapan Penanganan Kasus Pelanggaran">
    <?php if ($idKasus > 0): ?>
        <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $idKasus) ?>" class="ecc-wizard-step <?= $activeStep === 1 ? 'active' : '' ?>"<?= $activeStep === 1 ? ' aria-current="step"' : '' ?>>1. Pencatatan</a>
        <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="ecc-wizard-step <?= $activeStep === 2 ? 'active' : '' ?>"<?= $activeStep === 2 ? ' aria-current="step"' : '' ?>>2. Pemeriksaan</a>
        <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="ecc-wizard-step <?= $activeStep === 3 ? 'active' : '' ?>"<?= $activeStep === 3 ? ' aria-current="step"' : '' ?>>3. Penetapan &amp; Tindak Lanjut</a>
        <a href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus) ?>" class="ecc-wizard-step <?= $activeStep === 4 ? 'active' : '' ?>"<?= $activeStep === 4 ? ' aria-current="step"' : '' ?>>4. Dokumen Bukti</a>
    <?php else: ?>
        <span class="ecc-wizard-step active" aria-current="step">1. Pencatatan</span>
        <span class="ecc-wizard-step disabled-step" aria-disabled="true">2. Pemeriksaan</span>
        <span class="ecc-wizard-step disabled-step" aria-disabled="true">3. Penetapan &amp; Tindak Lanjut</span>
        <span class="ecc-wizard-step disabled-step" aria-disabled="true">4. Dokumen Bukti</span>
    <?php endif; ?>
</nav>
