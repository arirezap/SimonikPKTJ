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
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }
    .setting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06) !important;
    }
    .setting-card.active-deadline {
        border-color: #fca5a5 !important;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.08) !important;
    }
    .setting-card .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
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

        <div class="row g-3 g-md-4 mb-4">
            <!-- 0. MODE PEMELIHARAAN (MAINTENANCE MODE) -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden setting-card <?= $isMaintenanceActive ? 'border-warning shadow' : '' ?>" id="cardMaintenance" style="<?= $isMaintenanceActive ? 'border: 2px solid #f59e0b !important;' : '' ?>">
                    <div class="card-header <?= $isMaintenanceActive ? 'bg-warning-subtle text-dark' : 'bg-light-subtle' ?> py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge <?= $isMaintenanceActive ? 'bg-warning text-dark' : 'bg-secondary-subtle text-secondary' ?> border rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 1rem;">
                                <i class="bi bi-tools"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Mode Pemeliharaan Sistem (Maintenance Mode)</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Kunci akses seluruh pengguna biasa selama pembaruan kode / basis data</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-4 m-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" role="switch" name="enable_maintenance_mode" value="1" id="switchMaintenance" aria-label="Saklar mode pemeliharaan sistem" <?= $isMaintenanceActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge <?= $isMaintenanceActive ? 'bg-warning text-dark border border-warning-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeMaintenance">
                                        <i class="bi <?= $isMaintenanceActive ? 'bi-cone-striped' : 'bi-check-circle-fill' ?> me-1"></i>
                                        <span><?= $isMaintenanceActive ? 'Mode Pemeliharaan AKTIF' : 'Sistem Aktif Normal' ?></span>
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-info-circle me-1"></i> Administrator tetap dapat masuk & mengelola sistem.
                                    </span>
                                </div>
                                <p class="text-muted small mb-0 leading-relaxed">
                                    Ketika saklar ini diaktifkan, seluruh pengguna non-admin (Staf, Atasan, Direktur, Kepegawaian, Tamu) akan dialihkan ke halaman pemberitahuan pemeliharaan dengan hitung mundur otomatis 30 detik tanpa perlu menyunting file cPanel.
                                </p>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="p-3 bg-light rounded-3 border border-light-subtle">
                                    <label class="form-label fw-bold text-dark small mb-1.5 d-flex align-items-center justify-content-between">
                                        <span><i class="bi bi-chat-left-quote text-primary me-1"></i> Pesan untuk Pengguna</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">Opsional</span>
                                    </label>
                                    <textarea name="settings[maintenance_message]" rows="2" class="form-control form-control-sm" placeholder="Sistem sedang dalam pembaruan..." style="font-size: 0.82rem; resize: none;"><?= esc($maintenanceMessage) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. TARGET BULANAN -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardTarget">
                    <div class="card-header bg-light-subtle py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">1</span>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Target Kinerja Bulanan</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Batas waktu penyusunan RHK</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_target_deadline" value="1" id="switchTarget" data-target="cardTarget" data-status="badgeTarget" data-input="input_batas_target" aria-label="Saklar batas waktu target bulanan" <?= $isTargetDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <span class="badge <?= $isTargetDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeTarget">
                                    <i class="bi <?= $isTargetDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isTargetDeadlineActive ? 'Batas Waktu Aktif' : 'Mode Bebas' ?></span>
                                </span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Menu: Target Kinerja Bulanan</span>
                            </div>
                            <p class="text-muted small mb-3 leading-relaxed">
                                Batas tanggal tiap bulan berjalan bagi pegawai untuk menyusun dan mengajukan target RHK bulanan kepada atasan langsung.
                            </p>
                        </div>
                        
                        <!-- PARAMETER CONTROL BOX -->
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-calendar-event text-primary"></i> Tanggal Maksimal
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Setiap bulan berjalan</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm shadow-sm" style="width: 130px;">
                                        <span class="input-group-text bg-white text-muted fw-semibold">Tgl</span>
                                        <input type="number" min="1" max="31" name="settings[batas_input_target]" id="input_batas_target" class="form-control text-center fw-bold text-primary fs-6" value="<?= esc($settingsMap['batas_input_target']['setting_value'] ?? '5') ?>" required>
                                    </div>
                                    <span class="text-muted small fw-medium">tiap bulan</span>
                                </div>
                            </div>
                            <div class="form-text text-muted small mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i> Default sistem: Tanggal 5 setiap bulan berjalan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KUNCI PENGISIAN BULAN LALU (BATAS AKHIR BULAN) -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardMonthlyLog">
                    <div class="card-header bg-light-subtle py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">2</span>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Kunci Laporan Bulan Lalu</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Batas pengisian akhir bulan (Cutoff)</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_monthly_log_deadline" value="1" id="switchMonthlyLog" data-target="cardMonthlyLog" data-status="badgeMonthlyLog" data-input="input_toleransi_bulan_lalu" aria-label="Saklar kunci laporan bulan lalu" <?= $isMonthlyLogDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <span class="badge <?= $isMonthlyLogDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeMonthlyLog">
                                    <i class="bi <?= $isMonthlyLogDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isMonthlyLogDeadlineActive ? 'Kunci Aktif' : 'Mode Bebas' ?></span>
                                </span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Menu: Lapor Kegiatan Harian</span>
                            </div>
                            <p class="text-muted small mb-3 leading-relaxed">
                                Seluruh tanggal di bulan berjalan bebas diisi kapan saja. Begitu memasuki bulan berikutnya, pengisian bulan lalu otomatis terkunci.
                            </p>
                        </div>

                        <!-- PARAMETER CONTROL BOX -->
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-clock-history text-primary"></i> Toleransi Tambahan
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Setelah tanggal akhir bulan</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm shadow-sm" style="width: 130px;">
                                        <span class="input-group-text bg-white text-muted fw-semibold">+</span>
                                        <input type="number" min="0" max="30" name="settings[toleransi_hari_bulan_lalu]" id="input_toleransi_bulan_lalu" class="form-control text-center fw-bold text-primary fs-6" value="<?= esc($settingsMap['toleransi_hari_bulan_lalu']['setting_value'] ?? '0') ?>" required>
                                    </div>
                                    <span class="text-muted small fw-medium">hari ekstra</span>
                                </div>
                            </div>
                            <div class="form-text text-muted small mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i> <strong>0 Hari:</strong> Tepat tgl 1 jam 00:00 terkunci (Misal: 1 Sept untuk bulan Agustus)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TOLERANSI HARIAN LOG KEGIATAN -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardLog">
                    <div class="card-header bg-light-subtle py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">3</span>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Toleransi Harian Pelaporan</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Batas bergulir per tanggal kegiatan</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_log_deadline" value="1" id="switchLog" data-target="cardLog" data-status="badgeLog" data-input="input_batas_log" aria-label="Saklar batas waktu log harian" <?= $isLogDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <span class="badge <?= $isLogDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgeLog">
                                    <i class="bi <?= $isLogDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isLogDeadlineActive ? 'Batas Harian Aktif' : 'Mode Bebas' ?></span>
                                </span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Rolling Daily Limit</span>
                            </div>
                            <p class="text-muted small mb-3 leading-relaxed">
                                Membatasi pengisian laporan harian maksimal N hari setelah masing-masing tanggal kegiatan berlangsung (Masa depan tetap dilarang).
                            </p>
                        </div>

                        <!-- PARAMETER CONTROL BOX -->
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-hourglass-split text-primary"></i> Toleransi Hari Pelaporan
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Setelah tanggal kegiatan</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm shadow-sm" style="width: 130px;">
                                        <span class="input-group-text bg-white text-muted fw-semibold">Maks</span>
                                        <input type="number" min="1" max="60" name="settings[batas_input_log]" id="input_batas_log" class="form-control text-center fw-bold text-primary fs-6" value="<?= esc($settingsMap['batas_input_log']['setting_value'] ?? '3') ?>" required>
                                    </div>
                                    <span class="text-muted small fw-medium">hari setelahnya</span>
                                </div>
                            </div>
                            <div class="form-text text-muted small mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i> Default sistem: H+3 hari per tanggal kegiatan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PENILAIAN OLEH ATASAN -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden setting-card" id="cardPenilaian">
                    <div class="card-header bg-light-subtle py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">4</span>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Penilaian Kinerja Staf</h6>
                                <span class="text-muted" style="font-size: 0.72rem;">Batas waktu verifikasi atasan</span>
                            </div>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input cursor-pointer toggle-deadline" type="checkbox" role="switch" name="enable_penilaian_deadline" value="1" id="switchPenilaian" data-target="cardPenilaian" data-status="badgePenilaian" data-input="input_batas_penilaian" aria-label="Saklar batas waktu penilaian atasan" <?= $isPenilaianDeadlineActive ? 'checked' : '' ?>>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                <span class="badge <?= $isPenilaianDeadlineActive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 rounded-pill small fw-semibold" id="badgePenilaian">
                                    <i class="bi <?= $isPenilaianDeadlineActive ? 'bi-lock-fill' : 'bi-unlock-fill' ?> me-1"></i>
                                    <span><?= $isPenilaianDeadlineActive ? 'Batas Waktu Aktif' : 'Mode Bebas' ?></span>
                                </span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Menu: Penilaian Staf</span>
                            </div>
                            <p class="text-muted small mb-3 leading-relaxed">
                                Batas tanggal di bulan berikutnya bagi atasan langsung untuk memberikan nilai capaian target kinerja staf di bawahnya.
                            </p>
                        </div>

                        <!-- PARAMETER CONTROL BOX -->
                        <div class="p-3 bg-light rounded-3 border border-light-subtle">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                        <i class="bi bi-award-fill text-primary"></i> Tanggal Maksimal
                                    </label>
                                    <span class="text-muted d-block" style="font-size: 0.72rem;">Di bulan berikutnya</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm shadow-sm" style="width: 130px;">
                                        <span class="input-group-text bg-white text-muted fw-semibold">Tgl</span>
                                        <input type="number" min="1" max="31" name="settings[batas_penilaian_kinerja]" id="input_batas_penilaian" class="form-control text-center fw-bold text-primary fs-6" value="<?= esc($settingsMap['batas_penilaian_kinerja']['setting_value'] ?? '10') ?>" required>
                                    </div>
                                    <span class="text-muted small fw-medium">bulan depan</span>
                                </div>
                            </div>
                            <div class="form-text text-muted small mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                <i class="bi bi-info-circle me-1"></i> Default sistem: Tanggal 10 di bulan berikutnya
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM ACTION BAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-shield-check fs-5 text-primary"></i>
                    <span>Perubahan saklar dan angka parameter akan langsung diberlakukan ke sistem dan dicatat ke dalam audit trail.</span>
                </div>
                <button type="submit" class="btn btn-primary btn-tactile rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2 flex-shrink-0" id="btnSubmitSettings">
                    <i class="bi bi-check-circle-fill"></i> Simpan Pengaturan
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

    const switchMaint = document.getElementById('switchMaintenance');
    const badgeMaint  = document.getElementById('badgeMaintenance');
    const cardMaint   = document.getElementById('cardMaintenance');

    if (switchMaint && badgeMaint) {
        switchMaint.addEventListener('change', function() {
            if (this.checked) {
                badgeMaint.className = 'badge bg-warning text-dark border border-warning-subtle px-2.5 py-1 rounded-pill small fw-semibold';
                badgeMaint.innerHTML = '<i class="bi bi-cone-striped me-1"></i> <span>Mode Pemeliharaan AKTIF</span>';
                if (cardMaint) {
                    cardMaint.style.border = '2px solid #f59e0b !important';
                    cardMaint.classList.add('border-warning', 'shadow');
                }
            } else {
                badgeMaint.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small fw-semibold';
                badgeMaint.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> <span>Sistem Aktif Normal</span>';
                if (cardMaint) {
                    cardMaint.style.border = '';
                    cardMaint.classList.remove('border-warning', 'shadow');
                }
            }
        });
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


