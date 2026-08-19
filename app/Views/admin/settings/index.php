<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pengaturan Sistem & Batas Waktu<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Pengaturan Sistem<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Top Info Banner -->
<div class="card border-0 shadow-sm mb-4 bg-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-sliders me-2"></i> Kebijakan Batas Waktu Kinerja</h5>
                <p class="mb-0 text-white-50 small">
                    Atur saklar batas waktu secara independen per tahapan (Target Bulanan, Laporan Harian, dan Penilaian Atasan).
                </p>
            </div>
            <div class="text-md-end">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Kontrol Fleksibel Mandiri
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Essential System Rules Note -->
<div class="alert alert-info border-0 bg-info-subtle text-info-emphasis d-flex align-items-start gap-2 mb-4 shadow-sm">
    <i class="bi bi-info-circle-fill fs-5 mt-0.5 flex-shrink-0"></i>
    <div class="small">
        <strong>Aturan Sistem yang Berlaku:</strong>
        <ul class="mb-0 ps-3 mt-1">
            <li><strong>Pelaporan Kegiatan Harian</strong>: <span class="text-danger fw-semibold">Dilarang keras</span> memilih tanggal di masa depan (melebihi hari ini).</li>
            <li><strong>Target Kinerja Bulanan</strong>: <span class="text-success fw-semibold">Diperbolehkan</span> menyusun dan mengirimkan target untuk bulan-bulan ke depan.</li>
            <li>Jika saklar suatu tahapan di-<strong>Nonaktifkan (OFF)</strong>, pengisian tahapan tersebut dapat dilakukan kapan saja tanpa batasan tanggal.</li>
        </ul>
    </div>
</div>

<form action="<?= site_url('settings/store') ?>" method="POST" id="formSettings">
    <?= csrf_field() ?>

    <div class="row g-4 mb-4">
        <!-- 1. Target Kinerja Bulanan Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bento-card" id="cardTarget" style="transition: all 0.3s ease;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">1</span>
                        <h6 class="fw-bold text-dark mb-0">Target Bulanan</h6>
                    </div>
                    <div class="form-check form-switch fs-5 m-0">
                        <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_target_deadline" value="1" id="switchTarget" data-target="cardTarget" data-status="badgeTarget" data-input="input_batas_target" <?= $isTargetDeadlineActive ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3">
                            <span class="badge <?= $isTargetDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeTarget">
                                <i class="bi <?= $isTargetDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                <span><?= $isTargetDeadlineActive ? 'Batas Waktu Aktif (Ketat)' : 'Mode Bebas / Fleksibel' ?></span>
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            Batas tanggal maksimal di bulan berjalan untuk menyusun target bulanan.
                        </p>
                    </div>
                    <div class="input-container mt-2">
                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Maksimal Tiap Bulan:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Tanggal</span>
                            <input type="number" min="1" max="31" name="settings[batas_input_target]" id="input_batas_target" class="form-control text-center fw-bold fs-5" value="<?= esc($settingsMap['batas_input_target']['setting_value'] ?? '5') ?>" required>
                            <span class="input-group-text bg-light">tiap bulan</span>
                        </div>
                        <div class="form-text text-muted small mt-1">Default: Tanggal 5 (Bulan ke depan tetap diizinkan)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Lapor Kegiatan Harian Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bento-card" id="cardLog" style="transition: all 0.3s ease;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">2</span>
                        <h6 class="fw-bold text-dark mb-0">Lapor Kegiatan Harian</h6>
                    </div>
                    <div class="form-check form-switch fs-5 m-0">
                        <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_log_deadline" value="1" id="switchLog" data-target="cardLog" data-status="badgeLog" data-input="input_batas_log" <?= $isLogDeadlineActive ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3">
                            <span class="badge <?= $isLogDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeLog">
                                <i class="bi <?= $isLogDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                <span><?= $isLogDeadlineActive ? 'Batas Waktu Aktif (Ketat)' : 'Mode Bebas / Fleksibel' ?></span>
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            Jumlah hari toleransi pengisian setelah tanggal kegiatan berlangsung.
                        </p>
                    </div>
                    <div class="input-container mt-2">
                        <label class="form-label small fw-bold text-dark mb-1">Batas Toleransi Hari:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Maksimal</span>
                            <input type="number" min="1" max="60" name="settings[batas_input_log]" id="input_batas_log" class="form-control text-center fw-bold fs-5" value="<?= esc($settingsMap['batas_input_log']['setting_value'] ?? '3') ?>" required>
                            <span class="input-group-text bg-light">hari setelahnya</span>
                        </div>
                        <div class="form-text text-muted small mt-1">Default: H+3 hari (Tanggal masa depan tetap dilarang)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Penilaian oleh Atasan Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bento-card" id="cardPenilaian" style="transition: all 0.3s ease;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">3</span>
                        <h6 class="fw-bold text-dark mb-0">Penilaian oleh Atasan</h6>
                    </div>
                    <div class="form-check form-switch fs-5 m-0">
                        <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_penilaian_deadline" value="1" id="switchPenilaian" data-target="cardPenilaian" data-status="badgePenilaian" data-input="input_batas_penilaian" <?= $isPenilaianDeadlineActive ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3">
                            <span class="badge <?= $isPenilaianDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgePenilaian">
                                <i class="bi <?= $isPenilaianDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                <span><?= $isPenilaianDeadlineActive ? 'Batas Waktu Aktif (Ketat)' : 'Mode Bebas / Fleksibel' ?></span>
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            Batas tanggal maksimal di bulan berikutnya bagi atasan untuk menerbitkan nilai capaian.
                        </p>
                    </div>
                    <div class="input-container mt-2">
                        <label class="form-label small fw-bold text-dark mb-1">Tanggal Maksimal Penilaian:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Tanggal</span>
                            <input type="number" min="1" max="31" name="settings[batas_penilaian_kinerja]" id="input_batas_penilaian" class="form-control text-center fw-bold fs-5" value="<?= esc($settingsMap['batas_penilaian_kinerja']['setting_value'] ?? '10') ?>" required>
                            <span class="input-group-text bg-light">bulan depan</span>
                        </div>
                        <div class="form-text text-muted small mt-1">Default: Tanggal 10 di bulan berikutnya</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Save Action Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i class="bi bi-info-circle me-1"></i> Seluruh perubahan saklar dan angka akan disimpan ke basis data dan dicatat di Log Keamanan.
            </span>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> Simpan Pengaturan
            </button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateSingleCardUI(switchEl) {
        const isChecked = switchEl.checked;
        const targetCard = document.getElementById(switchEl.dataset.target);
        const statusBadge = document.getElementById(switchEl.dataset.status);
        const inputField = document.getElementById(switchEl.dataset.input);

        if (isChecked) {
            statusBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold';
            statusBadge.innerHTML = '<i class="bi bi-lock-fill me-1"></i> <span>Batas Waktu Aktif (Ketat)</span>';
            if (targetCard) targetCard.style.opacity = '1';
        } else {
            statusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-semibold';
            statusBadge.innerHTML = '<i class="bi bi-unlock-fill me-1"></i> <span>Mode Bebas / Fleksibel</span>';
            if (targetCard) targetCard.style.opacity = '0.75';
        }
    }

    document.querySelectorAll('.toggle-deadline').forEach(switchEl => {
        updateSingleCardUI(switchEl);
        switchEl.addEventListener('change', function() {
            updateSingleCardUI(this);
        });
    });
</script>
<?= $this->endSection() ?>


