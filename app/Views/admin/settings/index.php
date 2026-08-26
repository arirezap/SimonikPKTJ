<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Pengaturan Sistem<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .setting-card {
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .setting-card.active-deadline {
        border-color: #fca5a5 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-sliders text-primary me-2"></i>Pengaturan Sistem</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                Batas Waktu Kinerja
            </span>
        </div>
        
        <div>
            <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5 small">
                <i class="bi bi-shield-lock-fill me-1 text-primary"></i> Kontrol Mandiri Administrator
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- PANDUAN RINGKAS SISTEM -->
    <div class="alert alert-info border border-info-subtle bg-info-subtle text-info-emphasis d-flex align-items-start gap-2 mb-3 shadow-sm py-2.5 px-3 rounded-3">
        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-0.5"></i>
        <div class="small">
            <strong>Kebijakan Saklar Batas Waktu:</strong>
            <span class="d-block mt-0.5">
                Jika saklar <strong>Aktif (ON)</strong>, pengisian dibatasi tanggal/hari maksimal. Jika <strong>Nonaktif (OFF)</strong>, pengisian dapat dilakukan kapan saja (bebas).
            </span>
        </div>
    </div>

    <form action="<?= site_url('settings/store') ?>" method="POST" id="formSettings" autocomplete="off">
        <?= csrf_field() ?>

        <div class="row g-3 mb-4">
            <!-- 1. TARGET BULANAN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardTarget">
                    <div class="card-header bg-light py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">1</span>
                            <h6 class="fw-bold text-dark mb-0">Target Bulanan</h6>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_target_deadline" value="1" id="switchTarget" data-target="cardTarget" data-status="badgeTarget" data-input="input_batas_target" aria-label="Saklar batas waktu target bulanan" <?= $isTargetDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-2">
                                <span class="badge <?= $isTargetDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeTarget">
                                    <i class="bi <?= $isTargetDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isTargetDeadlineActive ? 'Batas Waktu Aktif' : 'Mode Bebas' ?></span>
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                Batas tanggal tiap bulan berjalan untuk menyusun dan mengajukan target RHK.
                            </p>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <label class="form-label small fw-bold text-dark mb-1">Tanggal Maksimal:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Tanggal</span>
                                <input type="number" min="1" max="31" name="settings[batas_input_target]" id="input_batas_target" class="form-control text-center fw-bold fs-6" value="<?= esc($settingsMap['batas_input_target']['setting_value'] ?? '5') ?>" required>
                                <span class="input-group-text bg-light">tiap bulan</span>
                            </div>
                            <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">Default: Tanggal 5</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. LAPOR KEGIATAN HARIAN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardLog">
                    <div class="card-header bg-light py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">2</span>
                            <h6 class="fw-bold text-dark mb-0">Lapor Kegiatan Harian</h6>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_log_deadline" value="1" id="switchLog" data-target="cardLog" data-status="badgeLog" data-input="input_batas_log" aria-label="Saklar batas waktu log harian" <?= $isLogDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-2">
                                <span class="badge <?= $isLogDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeLog">
                                    <i class="bi <?= $isLogDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isLogDeadlineActive ? 'Batas Waktu Aktif' : 'Mode Bebas' ?></span>
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                Batas toleransi hari pengisian setelah tanggal kegiatan berlangsung.
                            </p>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <label class="form-label small fw-bold text-dark mb-1">Toleransi Hari:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Maksimal</span>
                                <input type="number" min="1" max="60" name="settings[batas_input_log]" id="input_batas_log" class="form-control text-center fw-bold fs-6" value="<?= esc($settingsMap['batas_input_log']['setting_value'] ?? '3') ?>" required>
                                <span class="input-group-text bg-light">hari setelahnya</span>
                            </div>
                            <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">Default: H+3 hari (Masa depan tetap dilarang)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PENILAIAN OLEH ATASAN -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardPenilaian">
                    <div class="card-header bg-light py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">3</span>
                            <h6 class="fw-bold text-dark mb-0">Penilaian Staf</h6>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_penilaian_deadline" value="1" id="switchPenilaian" data-target="cardPenilaian" data-status="badgePenilaian" data-input="input_batas_penilaian" aria-label="Saklar batas waktu penilaian atasan" <?= $isPenilaianDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-2">
                                <span class="badge <?= $isPenilaianDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgePenilaian">
                                    <i class="bi <?= $isPenilaianDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isPenilaianDeadlineActive ? 'Batas Waktu Aktif' : 'Mode Bebas' ?></span>
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                Batas tanggal di bulan berikutnya bagi atasan untuk menilai kinerja staf.
                            </p>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <label class="form-label small fw-bold text-dark mb-1">Tanggal Maksimal:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Tanggal</span>
                                <input type="number" min="1" max="31" name="settings[batas_penilaian_kinerja]" id="input_batas_penilaian" class="form-control text-center fw-bold fs-6" value="<?= esc($settingsMap['batas_penilaian_kinerja']['setting_value'] ?? '10') ?>" required>
                                <span class="input-group-text bg-light">bulan depan</span>
                            </div>
                            <div class="form-text text-muted small mt-1" style="font-size: 0.72rem;">Default: Tanggal 10 di bulan berikutnya</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM ACTION BAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Perubahan saklar dan angka akan disimpan ke sistem dan dicatat di log aktivitas.
                </span>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" id="btnSubmitSettings">
                    <i class="bi bi-check-circle-fill me-1"></i> Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateSingleCardUI(switchEl) {
        const isChecked = switchEl.checked;
        const targetCard = document.getElementById(switchEl.dataset.target);
        const statusBadge = document.getElementById(switchEl.dataset.status);

        if (isChecked) {
            statusBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill small fw-semibold';
            statusBadge.innerHTML = '<i class="bi bi-lock-fill me-1"></i> <span>Batas Waktu Aktif</span>';
            if (targetCard) {
                targetCard.classList.add('active-deadline');
                targetCard.style.opacity = '1';
            }
        } else {
            statusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-semibold';
            statusBadge.innerHTML = '<i class="bi bi-unlock-fill me-1"></i> <span>Mode Bebas</span>';
            if (targetCard) {
                targetCard.classList.remove('active-deadline');
                targetCard.style.opacity = '0.85';
            }
        }
    }

    document.querySelectorAll('.toggle-deadline').forEach(switchEl => {
        updateSingleCardUI(switchEl);
        switchEl.addEventListener('change', function() {
            updateSingleCardUI(this);
        });
    });

    document.getElementById('formSettings').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmitSettings');
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
            btn.disabled = true;
        }
    });
</script>
<?= $this->endSection() ?>


