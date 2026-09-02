<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Monitoring Target Kinerja Bulanan') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* 8-Point Grid Spacing & Sizing Scale Tokens */
:root {
    --ecc-space-0-5: 4px;   /* 0.5x Micro */
    --ecc-space-1: 8px;     /* 1.0x Base */
    --ecc-space-1-5: 12px;  /* 1.5x */
    --ecc-space-2: 16px;    /* 2.0x */
    --ecc-space-3: 24px;    /* 3.0x */
    --ecc-space-4: 32px;    /* 4.0x */
    --ecc-space-5: 40px;    /* 5.0x */
    --ecc-space-6: 48px;    /* 6.0x */
    --ecc-space-8: 64px;    /* 8.0x */
}

/* iOS Mobile Zoom Fix (16px minimum on touch devices) */
@media (max-width: 767.98px) {
    .form-control, .form-select {
        font-size: 16px !important;
    }
}

/* Staggered Entrance Animations */
@keyframes bentoEntrance {
    0% {
        opacity: 0;
        transform: translateY(12px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.bento-stagger {
    animation: bentoEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    will-change: transform, opacity;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.10s; }
.bento-stagger-3 { animation-delay: 0.16s; }

/* Tactile Micro-Interactions */
.btn-tactile {
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, background-color 0.15s ease;
}
.btn-tactile:active {
    transform: scale(0.97) !important;
}

/* Custom Filter Control Sizing (8-Point Grid Scale) */
.filter-select-custom {
    font-size: 0.8125rem !important;
    height: 36px !important;
    padding: 6px 32px 6px 12px !important;
    background-position: right 0.6rem center !important;
    background-size: 12px 10px !important;
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    white-space: nowrap;
    text-overflow: ellipsis;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.filter-select-custom:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

.filter-input-year {
    font-size: 0.8125rem !important;
    height: 36px !important;
    padding: 6px 12px !important;
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.filter-input-year:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Quick Filter Pills (8pt Compact Grid System - Single Row Scroll) */
#pillFilterGroup {
    padding: 8px 16px !important;
    gap: 8px !important;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
#pillFilterGroup::-webkit-scrollbar {
    display: none;
}
.filter-pill {
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1;
    height: 32px;
    padding: 4px 14px;
    border-radius: 50rem;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #dee2e6;
    background-color: #ffffff;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    flex-shrink: 0;
    user-select: none;
}
.filter-pill:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
    transform: translateY(-1px);
}
.filter-pill:active {
    transform: scale(0.96);
}
.filter-pill.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    border-color: #0d6efd !important;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
}
.filter-pill.active i {
    color: #ffffff !important;
}

/* Tabular Numerics for Precise Alignment */
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* KPI Stat Boxes in Bento Strip (8-Point Grid Scale) */
.kpi-stat-box {
    border-radius: 8px;
    padding: 12px 8px;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.kpi-stat-box:hover {
    background-color: #f8fafc;
}
    background-color: #f8fafc;
}

/* Bento Table Styling */
.table-bento {
    border-collapse: separate;
    border-spacing: 0;
}
.table-bento thead th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 16px;
    vertical-align: middle;
}
.table-bento tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-color: #f1f5f9;
    font-size: 0.8125rem;
}

/* Sortable Table Header */
.sortable-th {
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    white-space: nowrap;
}
.sortable-th:hover {
    background-color: #f1f5f9 !important;
    color: #0d6efd !important;
}
.sortable-th:focus-visible {
    outline: none;
    box-shadow: inset 0 0 0 2px #0d6efd !important;
}
.sortable-th.th-sorted {
    background-color: #eff6ff !important;
    color: #0d6efd !important;
    border-bottom: 2px solid #0d6efd !important;
}
.sort-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease, color 0.15s ease;
    opacity: 0.35;
    flex-shrink: 0;
}
.sortable-th:hover .sort-indicator {
    opacity: 0.85;
    color: #0d6efd;
}
.sortable-th.th-sorted .sort-indicator {
    opacity: 1;
    color: #0d6efd !important;
}

/* Clickable Row & Card Hover Affordance */
.cursor-pointer {
    cursor: pointer;
}
.pegawai-row.cursor-pointer {
    transition: background-color 0.15s ease, transform 0.15s ease;
    outline: none;
}
.pegawai-row.cursor-pointer:hover td {
    background-color: #f8fafc !important;
}
.pegawai-row.cursor-pointer:hover .pegawai-nama-text {
    color: #0d6efd !important;
    text-decoration: underline !important;
}
.pegawai-row.cursor-pointer:hover .row-hover-hint {
    opacity: 1 !important;
    transform: translateX(0) !important;
}
.pegawai-row.cursor-pointer:focus-visible {
    box-shadow: inset 0 0 0 2px #0d6efd !important;
}

.row-hover-hint {
    opacity: 0;
    transform: translateX(-4px);
    transition: opacity 0.18s cubic-bezier(0.16, 1, 0.3, 1), transform 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    color: #0d6efd;
    font-size: 0.8125rem;
    display: inline-flex;
    align-items: center;
}

/* Mobile Pegawai Card Clickable & Hover */
.mobile-pegawai-card.cursor-pointer {
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s ease, border-color 0.18s ease;
    outline: none;
    border-radius: 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mobile-pegawai-card.cursor-pointer:hover,
.mobile-pegawai-card.cursor-pointer:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important;
    border-color: #0d6efd !important;
}
.mobile-pegawai-card.cursor-pointer:hover .mobile-nama-text {
    color: #0d6efd !important;
}
.mobile-pegawai-card.cursor-pointer:active {
    transform: scale(0.98);
}

/* Semantic Status Badges & Pills */
.badge-status-pill {
    font-size: 0.72rem;
    font-weight: 600;
    height: 26px;
    padding: 4px 10px;
    border-radius: 50rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    line-height: 1;
}

/* Skeleton Loading for Zero-Reload Modal */
.skeleton-bar {
    height: 12px;
    background-color: #e2e8f0;
    border-radius: 4px;
    margin-bottom: 8px;
    animation: shimmer 1.6s infinite linear;
    background-image: linear-gradient(90deg, #e2e8f0 0%, #f1f5f9 50%, #e2e8f0 100%);
    background-size: 200% 100%;
}
@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Mobile vs Desktop View Toggle */
@media (max-width: 767.98px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
}
@media (min-width: 768px) {
    .desktop-table-view {
        display: block !important;
    }
    .mobile-cards-view {
        display: none !important;
    }
}

/* Accessibility: Prefers Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .bento-stagger,
    .filter-pill,
    .pegawai-row,
    .mobile-pegawai-card,
    .btn-tactile,
    #modalDetailTarget .modal-content {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-bullseye text-primary me-2 fs-5"></i>
                <span>Monitoring Target Kinerja Bulanan</span>
            </h1>
            <p class="text-muted small mb-0 mt-0.5">Pemantauan status pengisian, pengajuan, dan persetujuan Rencana Hasil Kerja (RHK) seluruh pegawai.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('kepegawaian/target-kinerja/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= urlencode($unit_filter) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm btn-success shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 btn-tactile" id="btnExportExcel" title="Unduh Workbook Excel (.xlsx) Multi-Sheet Data Lengkap" aria-label="Unduh Rekapitulasi Target Excel">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <a href="<?= site_url('kepegawaian/target-kinerja/export-pdf') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= urlencode($unit_filter) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm btn-danger shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 btn-tactile" id="btnExportPdf" title="Unduh Laporan PDF Resmi A4 Landscape" aria-label="Unduh Rekapitulasi Target PDF">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- DEADLINE BANNER (IF ACTIVE) -->
    <?php if (!empty($is_deadline_active)): ?>
    <div class="alert alert-info border-info-subtle bg-info-subtle text-info-emphasis rounded-4 p-2.5 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm bento-stagger bento-stagger-1">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-clock-history fs-6"></i>
            </div>
            <div>
                <strong class="d-block small">Batas Waktu Pengisian Target Bulanan Aktif</strong>
                <span class="small text-secondary" style="font-size: 0.76rem;">Pengisian target dibatasi maksimal tanggal <strong><?= esc($batas_target) ?></strong> pada setiap bulannya.</span>
            </div>
        </div>
        <div class="badge bg-white text-dark border rounded-pill px-3 py-1 small fw-bold">
            Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- FILTER & KPI SUMMARY ROW -->
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">
        
        <!-- FILTER CARD (LEFT) -->
        <div class="col-lg-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                        <i class="bi bi-funnel-fill text-primary me-2 fs-6"></i>
                        <span>Filter Periode, Unit & Kategori</span>
                    </h6>
                    <?php if (!empty($unit_filter) || !empty($role_filter) || !empty($status_filter)): ?>
                        <a href="<?= site_url('kepegawaian/target-kinerja') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>" class="badge bg-secondary-subtle text-secondary text-decoration-none rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="<?= site_url('kepegawaian/target-kinerja') ?>" id="formFilterTarget">
                        <div class="row g-2 align-items-end mb-2">
                            <!-- Filter Bulan -->
                            <div class="col-6 col-sm-4">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Periode Bulan</label>
                                <select name="bulan" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun</option>
                                    <?php foreach($bulan_indo as $idx => $bNama): ?>
                                        <option value="<?= $idx + 1 ?>" <?= ($bulan_terpilih == $idx + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= $bNama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Tahun -->
                            <div class="col-6 col-sm-3">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Tahun</label>
                                <input type="number" name="tahun" class="form-control form-control-sm text-center fw-bold filter-input-year shadow-sm num-tabular" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="this.form.submit()">
                            </div>

                            <!-- Filter Kategori Jabatan -->
                            <div class="col-12 col-sm-5">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Kategori Jabatan</label>
                                <select name="role" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Jabatan</option>
                                    <option value="pimpinan" <?= ($role_filter === 'pimpinan') ? 'selected' : '' ?>>Pimpinan (Direktur & Wadir)</option>
                                    <option value="struktural" <?= ($role_filter === 'struktural') ? 'selected' : '' ?>>Struktural & Manajemen</option>
                                    <option value="user" <?= ($role_filter === 'user' || $role_filter === 'staf') ? 'selected' : '' ?>>Staf Pelaksana & Fungsional</option>
                                    <option value="tugas_belajar" <?= ($role_filter === 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Unit Kerja -->
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Unit Kerja</label>
                                <select name="unit" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Unit Kerja</option>
                                    <?php foreach($daftar_unit as $uNama): ?>
                                        <option value="<?= esc($uNama) ?>" <?= ($unit_filter === $uNama) ? 'selected' : '' ?>><?= esc($uNama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="status" id="inputStatusFilter" value="<?= esc($status_filter) ?>">
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI SUMMARY CARD (RIGHT - 6 METRIC BOXES) -->
        <div class="col-lg-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                        <i class="bi bi-pie-chart-fill text-primary me-2 fs-6"></i>
                        <span>Ringkasan Status Target Pegawai</span>
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 small num-tabular fw-bold" title="Total Rencana Hasil Kerja yang tersusun pada periode ini">
                        <?= esc($stat_total_rhk) ?> Total RHK
                    </span>
                </div>
                <div class="card-body p-2.5 p-xl-3 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0 align-items-stretch">
                        
                        <!-- 1. Total Pegawai -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Jumlah seluruh pegawai aktif non-admin">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Total</div>
                            <div class="my-1">
                                <div class="fw-bold text-dark num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_total_pegawai) ?></div>
                            </div>
                            <div class="text-muted small" style="font-size: 0.65rem; white-space: nowrap;">Pegawai</div>
                        </div>

                        <!-- 2. Terkirim -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang sudah mengirimkan target">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Terkirim</div>
                            <div class="my-1">
                                <div class="fw-bold text-success num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_sudah_mengirim) ?></div>
                            </div>
                            <div class="text-success small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;"><?= esc($persen_kirim) ?>%</div>
                        </div>

                        <!-- 3. Disetujui -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang seluruh targetnya telah disetujui atasan">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Disetujui</div>
                            <div class="my-1">
                                <div class="fw-bold text-info-emphasis num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_sudah_disetujui) ?></div>
                            </div>
                            <div class="text-info-emphasis small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;"><?= esc($persen_setuju) ?>%</div>
                        </div>

                        <!-- 4. Menunggu -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang targetnya terkirim namun masih menunggu verifikasi atasan">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Menunggu</div>
                            <div class="my-1">
                                <div class="fw-bold text-warning-emphasis num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_menunggu_persetujuan) ?></div>
                            </div>
                            <div class="text-warning-emphasis small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">Antrean</div>
                        </div>

                        <!-- 5. Draf -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang telah menyusun target namun belum mengajukan/mengirim">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Draf</div>
                            <div class="my-1">
                                <div class="fw-bold text-secondary num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_draft) ?></div>
                            </div>
                            <div class="text-secondary small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">Draf</div>
                        </div>

                        <!-- 6. Belum Isi -->
                        <div class="col px-1 d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang belum mengisi sama sekali (0 RHK)">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Belum Isi</div>
                            <div class="my-1">
                                <div class="fw-bold text-danger num-tabular" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;"><?= esc($stat_belum_mengisi) ?></div>
                            </div>
                            <div class="text-danger small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">0 RHK</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MAIN BENTO CARD: DAFTAR STATUS TARGET PEGAWAI -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        
        <!-- Card Header with Live Search, Counter & Hint -->
        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                    <i class="bi bi-table text-primary me-2 fs-6"></i>
                    <span>Daftar Status Target Pegawai</span>
                </h6>
                <div class="d-flex align-items-center gap-2 mt-0.5">
                    <small class="text-muted" id="visibleCounter" style="font-size: 0.72rem;">Menampilkan <?= count($rekap_target) ?> pegawai</small>
                    <span class="text-muted small">|</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                        <i class="bi bi-cursor-fill me-1"></i>Klik baris/kartu untuk rincian
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Live Search Box -->
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, NIP, unit..." autocomplete="off" aria-label="Pencarian cepat pegawai">
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold">
                    <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                </span>
            </div>
        </div>

        <!-- Quick Status Filter Pills (8px Compact Grid System) -->
        <div class="bg-white border-bottom d-flex align-items-center flex-nowrap" id="pillFilterGroup">
            <span class="text-muted small fw-bold me-1 flex-shrink-0" style="font-size: 0.7rem; letter-spacing: 0.3px;">Filter Cepat:</span>
            <button type="button" class="filter-pill <?= empty($status_filter) ? 'active' : '' ?>" data-status="" role="button" aria-label="Semua Pegawai">
                <i class="bi bi-grid-fill"></i> Semua (<span class="num-tabular"><?= esc($stat_total_pegawai) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'sudah_mengirim') ? 'active' : '' ?>" data-status="sudah_mengirim" role="button" aria-label="Sudah Mengirim">
                <i class="bi bi-send-check-fill text-success"></i> Sudah Mengirim (<span class="num-tabular"><?= esc($stat_sudah_mengirim) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'disetujui') ? 'active' : '' ?>" data-status="disetujui" role="button" aria-label="Sudah Disetujui">
                <i class="bi bi-check-circle-fill text-info"></i> Disetujui (<span class="num-tabular"><?= esc($stat_sudah_disetujui) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'menunggu_persetujuan') ? 'active' : '' ?>" data-status="menunggu_persetujuan" role="button" aria-label="Menunggu Persetujuan">
                <i class="bi bi-hourglass-split text-warning"></i> Menunggu (<span class="num-tabular"><?= esc($stat_menunggu_persetujuan) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'draft') ? 'active' : '' ?>" data-status="draft" role="button" aria-label="Draf">
                <i class="bi bi-pencil-square text-secondary"></i> Draf (<span class="num-tabular"><?= esc($stat_draft) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'belum_mengisi') ? 'active' : '' ?>" data-status="belum_mengisi" role="button" aria-label="Belum Mengisi">
                <i class="bi bi-x-circle-fill text-danger"></i> Belum Mengisi (<span class="num-tabular"><?= esc($stat_belum_mengisi) ?></span>)
            </button>
        </div>

        <div class="card-body p-0">
            <!-- Desktop Bento Table -->
            <div class="table-responsive desktop-table-view">
                <table class="table table-hover table-bento mb-0 align-middle" id="tableMonitoringTarget">
                    <thead>
                        <tr>
                            <th class="text-center sortable-th th-sorted" style="width: 55px;" data-sort-key="no" data-sort-type="number" tabindex="0" role="button" aria-sort="ascending" title="Klik untuk mengurutkan berdasarkan nomor urut resmi">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>No</span>
                                    <i class="bi bi-arrow-up sort-indicator text-primary fw-bold"></i>
                                </div>
                            </th>
                            <th style="min-width: 270px;" class="sortable-th" data-sort-key="nama" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan nama pegawai">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Pegawai & Jabatan</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th style="min-width: 170px;" class="sortable-th" data-sort-key="unit" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan unit kerja">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Unit Kerja</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th style="min-width: 170px;" class="sortable-th" data-sort-key="atasan" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan atasan penyetuju">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Atasan Penyetuju</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" style="width: 125px;" data-sort-key="rhk" data-sort-type="number" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan jumlah Target RHK">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Target (RHK)</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" style="width: 155px;" data-sort-key="status-kirim" data-sort-type="status-kirim" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan status pengiriman">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Status Pengiriman</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="text-center sortable-th" style="width: 165px;" data-sort-key="status-appr" data-sort-type="status-appr" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan status persetujuan">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Status Persetujuan</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rekap_target)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                    <span>Tidak ditemukan data pegawai yang sesuai dengan kriteria filter.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($rekap_target as $index => $row): ?>
                                <?php 
                                    $user = $row['user'];
                                    $atasan = $row['atasan'];
                                    $stKirim = $row['status_pengiriman'];
                                    $stAppr = $row['status_persetujuan'];
                                    $atasanSortKey = ($user['role'] === 'direktur') ? 'aaa direktur auto approve' : (!empty($atasan['nama_lengkap']) ? strtolower($atasan['nama_lengkap']) : 'zzz');
                                ?>
                                <tr class="pegawai-row cursor-pointer btn-detail-target-trigger" 
                                    role="button"
                                    tabindex="0"
                                    title="Klik untuk melihat rincian target <?= esc($user['nama_lengkap']) ?>"
                                    data-user-id="<?= esc($user['id']) ?>" 
                                    data-bulan="<?= esc($bulan_terpilih) ?>" 
                                    data-tahun="<?= esc($tahun_terpilih) ?>"
                                    data-no="<?= $index + 1 ?>"
                                    data-nama="<?= esc(strtolower($user['nama_lengkap'])) ?>" 
                                    data-nip="<?= esc(strtolower($user['nip'] ?? '')) ?>" 
                                    data-unit="<?= esc(strtolower($user['unit'] ?? '')) ?>" 
                                    data-jabatan="<?= esc(strtolower($user['jabatan'] ?? '')) ?>"
                                    data-atasan="<?= esc($atasanSortKey) ?>"
                                    data-rhk="<?= (float)$row['total_rhk'] ?>"
                                    data-status-kirim="<?= esc($stKirim) ?>"
                                    data-status-appr="<?= esc($stAppr) ?>">
                                    
                                    <td class="text-center fw-bold text-muted num-tabular"><?= $index + 1 ?></td>
                                    
                                    <!-- Pegawai & Jabatan (Hover Hint Link) -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <?php if (!empty($user['foto'])): ?>
                                                <img src="<?= base_url('uploads/foto_profil/' . $user['foto']) ?>" alt="Foto" class="object-fit-cover shadow-xs flex-shrink-0" width="40" height="40" style="border-radius: 12px;">
                                            <?php else: ?>
                                                <div class="bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px; font-size: 0.875rem;">
                                                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="lh-sm flex-grow-1">
                                                <div class="d-flex align-items-center justify-content-between pe-1">
                                                    <div class="fw-bold text-dark mb-0.5 pegawai-nama-text"><?= esc($user['nama_lengkap']) ?></div>
                                                    <span class="row-hover-hint ms-1" title="Lihat rincian"><i class="bi bi-chevron-right"></i></span>
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.72rem;">
                                                    NIP: <span class="num-tabular"><?= !empty($user['nip']) ? esc($user['nip']) : '-' ?></span>
                                                </div>
                                                <div class="text-secondary small fw-medium mt-0.5" style="font-size: 0.7rem;">
                                                    <?= !empty($user['jabatan']) ? esc($user['jabatan']) : '-' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Unit Kerja -->
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-wrap text-start" style="font-size: 0.74rem;">
                                            <?= !empty($user['unit']) ? esc($user['unit']) : '-' ?>
                                        </span>
                                    </td>

                                    <!-- Atasan Penyetuju -->
                                    <td>
                                        <?php if ($user['role'] === 'direktur'): ?>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 small">
                                                <i class="bi bi-shield-fill-check me-1"></i> Auto-Approve (Direktur)
                                            </span>
                                        <?php elseif (!empty($atasan)): ?>
                                            <div class="fw-semibold text-dark small mb-0.5"><?= esc($atasan['nama_lengkap']) ?></div>
                                            <div class="text-muted small" style="font-size: 0.68rem;"><?= esc($atasan['jabatan'] ?? '-') ?></div>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Total Target (RHK) -->
                                    <td class="text-center">
                                        <span class="badge <?= ($row['total_rhk'] > 0) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2.5 py-1 fw-bold num-tabular" style="font-size: 0.72rem;">
                                            <?= esc($row['total_rhk']) ?> RHK
                                        </span>
                                    </td>

                                    <!-- Status Pengiriman -->
                                    <td class="text-center">
                                        <?php if ($stKirim === 'disetujui'): ?>
                                            <span class="badge-status-pill bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill"></i> Disetujui
                                            </span>
                                        <?php elseif ($stKirim === 'terkirim'): ?>
                                            <span class="badge-status-pill bg-primary-subtle text-primary border border-primary-subtle">
                                                <i class="bi bi-send-check-fill"></i> Terkirim
                                            </span>
                                        <?php elseif ($stKirim === 'sebagian_disetujui'): ?>
                                            <span class="badge-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">
                                                <i class="bi bi-pie-chart-fill"></i> Sebagian Disetujui
                                            </span>
                                        <?php elseif ($stKirim === 'draft'): ?>
                                            <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-pencil-square"></i> Draf (Belum Dikirim)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status-pill bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-x-circle-fill"></i> Belum Mengisi
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Persetujuan -->
                                    <td class="text-center">
                                        <?php if ($stAppr === 'disetujui'): ?>
                                            <span class="badge-status-pill bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-all"></i> Disetujui Atasan
                                            </span>
                                        <?php elseif ($stAppr === 'menunggu_persetujuan'): ?>
                                            <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-hourglass-split"></i> Menunggu
                                            </span>
                                        <?php elseif ($stAppr === 'sebagian_disetujui'): ?>
                                            <span class="badge-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">
                                                <i class="bi bi-check-circle"></i> Sebagian (<span class="num-tabular"><?= esc($row['count_disetujui']) ?>/<?= esc($row['total_rhk']) ?></span>)
                                            </span>
                                        <?php elseif ($stAppr === 'draft'): ?>
                                            <span class="badge-status-pill bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                <i class="bi bi-pencil"></i> Draf
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status-pill bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                <i class="bi bi-dash-circle"></i> Belum Ada Target
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Touch Cards (<768px) -->
            <div class="mobile-cards-view p-3">
                <?php if (empty($rekap_target)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                        <span>Tidak ada data pegawai.</span>
                    </div>
                <?php else: ?>
                    <?php foreach($rekap_target as $index => $row): ?>
                        <?php 
                            $user = $row['user'];
                            $atasan = $row['atasan'];
                            $stKirim = $row['status_pengiriman'];
                            $stAppr = $row['status_persetujuan'];
                            $atasanSortKey = ($user['role'] === 'direktur') ? 'aaa direktur auto approve' : (!empty($atasan['nama_lengkap']) ? strtolower($atasan['nama_lengkap']) : 'zzz');
                        ?>
                        <div class="mobile-pegawai-card pegawai-row cursor-pointer btn-detail-target-trigger"
                             role="button"
                             tabindex="0"
                             title="Ketuk untuk melihat rincian target <?= esc($user['nama_lengkap']) ?>"
                             data-user-id="<?= esc($user['id']) ?>" 
                             data-bulan="<?= esc($bulan_terpilih) ?>" 
                             data-tahun="<?= esc($tahun_terpilih) ?>"
                             data-no="<?= $index + 1 ?>"
                             data-nama="<?= esc(strtolower($user['nama_lengkap'])) ?>" 
                             data-nip="<?= esc(strtolower($user['nip'] ?? '')) ?>" 
                             data-unit="<?= esc(strtolower($user['unit'] ?? '')) ?>" 
                             data-jabatan="<?= esc(strtolower($user['jabatan'] ?? '')) ?>"
                             data-atasan="<?= esc($atasanSortKey) ?>"
                             data-rhk="<?= (float)$row['total_rhk'] ?>"
                             data-status-kirim="<?= esc($stKirim) ?>"
                             data-status-appr="<?= esc($stAppr) ?>">
                            
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small">
                                    <?= !empty($user['unit']) ? esc($user['unit']) : '-' ?>
                                </span>
                                <span class="badge <?= ($row['total_rhk'] > 0) ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-2.5 py-1 fw-bold num-tabular">
                                    <?= esc($row['total_rhk']) ?> Target
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2.5 mb-2.5">
                                <?php if (!empty($user['foto'])): ?>
                                    <img src="<?= base_url('uploads/foto_profil/' . $user['foto']) ?>" alt="Foto" class="object-fit-cover shadow-xs flex-shrink-0" width="40" height="40" style="border-radius: 12px;">
                                <?php else: ?>
                                    <div class="bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px; font-size: 0.95rem;">
                                        <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="lh-sm flex-grow-1">
                                    <div class="fw-bold text-dark mobile-nama-text"><?= esc($user['nama_lengkap']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">NIP: <span class="num-tabular"><?= !empty($user['nip']) ? esc($user['nip']) : '-' ?></span></div>
                                    <div class="text-secondary small fw-medium mt-0.5" style="font-size: 0.7rem;"><?= !empty($user['jabatan']) ? esc($user['jabatan']) : '-' ?></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2 flex-wrap">
                                <div>
                                    <?php if ($stKirim === 'disetujui'): ?>
                                        <span class="badge-status-pill bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    <?php elseif ($stKirim === 'terkirim'): ?>
                                        <span class="badge-status-pill bg-primary-subtle text-primary border border-primary-subtle">
                                            <i class="bi bi-send-check-fill"></i> Terkirim
                                        </span>
                                    <?php elseif ($stKirim === 'draft'): ?>
                                        <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                            <i class="bi bi-pencil-square"></i> Draf
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status-pill bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle-fill"></i> Belum Mengisi
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-primary small fw-semibold">
                                    <span>Lihat Rincian</span>
                                    <i class="bi bi-chevron-right"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- MODAL RINCIAN TARGET PEGAWAI (ZERO-RELOAD INTERACTION) -->
<div class="modal fade" id="modalDetailTarget" tabindex="-1" aria-labelledby="modalDetailTargetLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-card-checklist fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetailTargetLabel" style="font-size: 1rem; line-height: 1.25;">Rincian Target Kinerja Bulanan</h5>
                        <span class="text-muted small d-flex align-items-center" id="modalTargetPeriode" style="font-size: 0.75rem; margin-top: 2px;">
                            <i class="bi bi-calendar-check text-primary me-1.5"></i> Periode: -
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4">
                
                <!-- Skeleton Loader -->
                <div id="modalTargetSkeleton">
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4 border">
                        <div class="bg-secondary-subtle" style="width: 48px; height: 48px; border-radius: 14px;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-bar" style="width: 40%;"></div>
                            <div class="skeleton-bar" style="width: 25%;"></div>
                        </div>
                    </div>
                    <div class="skeleton-bar" style="width: 100%;"></div>
                    <div class="skeleton-bar" style="width: 90%;"></div>
                    <div class="skeleton-bar" style="width: 95%;"></div>
                </div>

                <!-- Content Container -->
                <div id="modalTargetContent" style="display: none;">
                    
                    <!-- Employee Profile Summary Card -->
                    <div class="card border rounded-4 shadow-xs mb-3" style="background-color: #f8fafc; border-color: #e2e8f0 !important; padding: 14px 18px;">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div id="modalUserAvatarContainer"></div>
                                    <div class="lh-sm">
                                        <h6 class="fw-bold text-dark mb-1" id="modalUserNama">-</h6>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            NIP: <span class="num-tabular fw-semibold" id="modalUserNip">-</span> | 
                                            Unit: <span class="fw-semibold text-dark" id="modalUserUnit">-</span>
                                        </div>
                                        <div class="text-secondary small fw-medium mt-1" id="modalUserJabatan" style="font-size: 0.72rem;">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 border-start-md">
                                <div class="ps-md-3">
                                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.6875rem; letter-spacing: 0.04em;">Atasan Langsung Penyetuju:</span>
                                    <div class="fw-bold text-dark small" id="modalAtasanNama">-</div>
                                    <div class="text-muted small mt-0.5" id="modalAtasanJabatan" style="font-size: 0.72rem;">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Targets List Table -->
                    <div class="table-responsive border rounded-4 bg-white shadow-xs overflow-hidden mb-2">
                        <table class="table table-bordered table-hover table-bento mb-0 align-middle">
                            <thead id="modalTargetTableHead">
                                <tr class="text-center align-middle" style="background-color: #f8fafc;">
                                    <th style="width: 48px;">No</th>
                                    <th class="text-start" style="min-width: 200px;">Rencana Hasil Kerja (RHK)</th>
                                    <th class="text-start" style="min-width: 200px;">Indikator Kinerja Individu</th>
                                    <th style="width: 104px;">Target</th>
                                    <th style="width: 96px;">Satuan</th>
                                    <th style="width: 136px;">Status Pengiriman</th>
                                    <th style="width: 136px;">Status Persetujuan</th>
                                </tr>
                            </thead>
                            <tbody id="modalTargetTableBody">
                                <!-- Rendered via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light border-top d-flex justify-content-end align-items-center px-3 px-md-4 py-2.5">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold btn-tactile shadow-xs" style="min-height: 36px; font-size: 0.8125rem;" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Live Search Filter & Counter
    const liveSearchInput = document.getElementById('liveSearchInput');
    const tableRows = document.querySelectorAll('.pegawai-row');
    const visibleCounter = document.getElementById('visibleCounter');
    let currentPillStatus = '<?= esc($status_filter) ?>';

    function applyFilters() {
        const query = (liveSearchInput ? liveSearchInput.value : '').toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const nip = row.getAttribute('data-nip') || '';
            const unit = row.getAttribute('data-unit') || '';
            const jabatan = row.getAttribute('data-jabatan') || '';
            const stKirim = row.getAttribute('data-status-kirim') || '';
            const stAppr = row.getAttribute('data-status-appr') || '';

            // Search query match
            const matchSearch = (query === '' || nama.includes(query) || nip.includes(query) || unit.includes(query) || jabatan.includes(query));

            // Status filter pill match
            let matchStatus = true;
            if (currentPillStatus === 'sudah_mengirim') {
                matchStatus = (stKirim === 'terkirim' || stKirim === 'disetujui' || stKirim === 'sebagian_disetujui');
            } else if (currentPillStatus === 'disetujui') {
                matchStatus = (stAppr === 'disetujui');
            } else if (currentPillStatus === 'menunggu_persetujuan') {
                matchStatus = (stAppr === 'menunggu_persetujuan' || stAppr === 'sebagian_disetujui');
            } else if (currentPillStatus === 'draft') {
                matchStatus = (stKirim === 'draft');
            } else if (currentPillStatus === 'belum_mengisi') {
                matchStatus = (stKirim === 'belum_mengisi');
            }

            if (matchSearch && matchStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCounter) {
            visibleCounter.innerText = `Menampilkan ${visibleCount} pegawai`;
        }
    }

    // Live Search Filter with 150ms Debounce
    let searchDebounceTimer;
    if (liveSearchInput) {
        liveSearchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(applyFilters, 150);
        });
    }

    // Quick Status Pills Filter (Client-Side Real-time Filter)
    const pills = document.querySelectorAll('.filter-pill');
    pills.forEach(pill => {
        pill.addEventListener('click', function() {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            currentPillStatus = this.getAttribute('data-status') || '';
            const inputStatusFilter = document.getElementById('inputStatusFilter');
            if (inputStatusFilter) {
                inputStatusFilter.value = currentPillStatus;
            }
            applyFilters();
        });
    });

    // Interactive Column Sorting (Desktop Table & Mobile Cards)
    const sortableHeaders = document.querySelectorAll('.sortable-th');
    const tableTbody = document.querySelector('.table-bento tbody');
    const mobileCardsContainer = document.querySelector('.mobile-cards-view');
    let currentSortKey = 'no';
    let currentSortDir = 'asc';

    const statusKirimWeight = {
        'belum_mengisi': 0,
        'draft': 1,
        'terkirim': 2,
        'sebagian_disetujui': 3,
        'disetujui': 4
    };

    const statusApprWeight = {
        'belum_mengisi': 0,
        'draft': 1,
        'menunggu_persetujuan': 2,
        'sebagian_disetujui': 3,
        'disetujui': 4
    };

    function sortElements(sortKey, sortType, direction) {
        if (!tableTbody) return;

        const rows = Array.from(tableTbody.querySelectorAll('tr.pegawai-row'));
        const cards = mobileCardsContainer ? Array.from(mobileCardsContainer.querySelectorAll('.mobile-pegawai-card')) : [];

        const comparator = (a, b) => {
            let valA = a.getAttribute(`data-${sortKey}`) || '';
            let valB = b.getAttribute(`data-${sortKey}`) || '';

            let result = 0;
            if (sortType === 'number') {
                valA = parseFloat(valA) || 0;
                valB = parseFloat(valB) || 0;
                result = valA - valB;
            } else if (sortType === 'status-kirim') {
                const wA = statusKirimWeight[valA] ?? -1;
                const wB = statusKirimWeight[valB] ?? -1;
                result = wA - wB;
            } else if (sortType === 'status-appr') {
                const wA = statusApprWeight[valA] ?? -1;
                const wB = statusApprWeight[valB] ?? -1;
                result = wA - wB;
            } else {
                result = valA.localeCompare(valB, 'id', { numeric: true, sensitivity: 'base' });
            }

            return direction === 'asc' ? result : -result;
        };

        rows.sort(comparator);
        rows.forEach(r => tableTbody.appendChild(r));

        if (mobileCardsContainer && cards.length > 0) {
            cards.sort(comparator);
            cards.forEach(c => mobileCardsContainer.appendChild(c));
        }
    }

    sortableHeaders.forEach(th => {
        th.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort-key');
            const sortType = this.getAttribute('data-sort-type') || 'string';

            if (currentSortKey === sortKey) {
                currentSortDir = (currentSortDir === 'asc') ? 'desc' : 'asc';
            } else {
                currentSortKey = sortKey;
                currentSortDir = 'asc';
            }

            // Update UI indicators
            sortableHeaders.forEach(h => {
                h.classList.remove('sorted-asc', 'sorted-desc', 'th-sorted');
                h.setAttribute('aria-sort', 'none');
                const icon = h.querySelector('.sort-indicator');
                if (icon) {
                    icon.className = 'bi bi-arrow-down-up sort-indicator';
                }
            });

            this.classList.add(currentSortDir === 'asc' ? 'sorted-asc' : 'sorted-desc', 'th-sorted');
            this.setAttribute('aria-sort', currentSortDir === 'asc' ? 'ascending' : 'descending');
            const currentIcon = this.querySelector('.sort-indicator');
            if (currentIcon) {
                currentIcon.className = (currentSortDir === 'asc') ? 'bi bi-arrow-up sort-indicator text-primary fw-bold' : 'bi bi-arrow-down sort-indicator text-primary fw-bold';
            }

            sortElements(sortKey, sortType, currentSortDir);
        });

        th.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Modal Detail Target Loader & Row/Card Click Triggers
    const modalEl = document.getElementById('modalDetailTarget');
    const bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const skeleton = document.getElementById('modalTargetSkeleton');
    const content = document.getElementById('modalTargetContent');
    const tableBody = document.getElementById('modalTargetTableBody');
    const tableHead = document.getElementById('modalTargetTableHead');

    function openTargetDetail(element) {
        const userId = element.getAttribute('data-user-id');
        const bulan = element.getAttribute('data-bulan');
        const tahun = element.getAttribute('data-tahun');

        if (!bsModal || !userId) return;

        // Reset modal state
        skeleton.style.display = 'block';
        content.style.display = 'none';
        tableBody.innerHTML = '';
        bsModal.show();

        // Fetch detail target via AJAX
        fetch(`<?= site_url('kepegawaian/target-kinerja/detail-pegawai') ?>?user_id=${userId}&bulan=${bulan}&tahun=${tahun}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    skeleton.style.display = 'none';
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">${data.message || 'Gagal memuat rincian target.'}</td></tr>`;
                    content.style.display = 'block';
                    return;
                }

                const isAllYear = (data.periode.bulan === 'all');

                // Populate User Profile
                document.getElementById('modalTargetPeriode').innerHTML = `<i class="bi bi-calendar-check text-primary me-1.5"></i> Periode: ${escapeHtml(data.periode.nama_bulan)} ${escapeHtml(data.periode.tahun)}`;
                document.getElementById('modalUserNama').innerText = data.user.nama_lengkap;
                document.getElementById('modalUserNip').innerText = data.user.nip;
                document.getElementById('modalUserUnit').innerText = data.user.unit;
                document.getElementById('modalUserJabatan').innerText = data.user.jabatan;

                // Avatar
                const avatarContainer = document.getElementById('modalUserAvatarContainer');
                if (data.user.foto) {
                    avatarContainer.innerHTML = `<img src="${data.user.foto}" alt="Foto" class="object-fit-cover shadow-xs" width="48" height="48" style="border-radius: 14px;">`;
                } else {
                    const initial = data.user.nama_lengkap.charAt(0).toUpperCase();
                    avatarContainer.innerHTML = `<div class="bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 48px; height: 48px; border-radius: 14px; font-size: 1.1rem;">${initial}</div>`;
                }

                // Atasan
                if (data.user.role === 'direktur') {
                    document.getElementById('modalAtasanNama').innerHTML = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold"><i class="bi bi-shield-fill-check me-1"></i> Auto-Approve (Direktur)</span>';
                    document.getElementById('modalAtasanJabatan').innerText = 'Pimpinan Tertinggi';
                } else if (data.atasan) {
                    document.getElementById('modalAtasanNama').innerText = data.atasan.nama_lengkap;
                    document.getElementById('modalAtasanJabatan').innerText = data.atasan.jabatan || '-';
                } else {
                    document.getElementById('modalAtasanNama').innerText = '-';
                    document.getElementById('modalAtasanJabatan').innerText = 'Belum Ditentukan';
                }

                // Dynamic Thead if all year
                if (tableHead) {
                    tableHead.innerHTML = `
                        <tr class="text-center align-middle" style="background-color: #f8fafc;">
                            <th style="width: 48px;">No</th>
                            ${isAllYear ? '<th style="width: 112px;">Bulan</th>' : ''}
                            <th class="text-start" style="min-width: 200px;">Rencana Hasil Kerja (RHK)</th>
                            <th class="text-start" style="min-width: 200px;">Indikator Kinerja Individu</th>
                            <th style="width: 104px;">Target</th>
                            <th style="width: 96px;">Satuan</th>
                            <th style="width: 136px;">Status Pengiriman</th>
                            <th style="width: 136px;">Status Persetujuan</th>
                        </tr>
                    `;
                }

                // Populate Target Rows
                if (data.targets && data.targets.length > 0) {
                    let rowsHtml = '';
                    data.targets.forEach((t, idx) => {
                        const isAppr = (t.status_approval === 'disetujui');
                        const isKirim = (t.status === 'terkirim');

                        const badgeKirim = isKirim 
                            ? '<span class="badge-status-pill bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-send-check-fill"></i> Terkirim</span>' 
                            : '<span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-pencil-square"></i> Draf</span>';

                        const badgeAppr = isAppr 
                            ? '<span class="badge-status-pill bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle-fill"></i> Disetujui</span>' 
                            : '<span class="badge-status-pill bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-hourglass-split"></i> Menunggu</span>';

                        const targetVal = parseFloat(t.target_bulanan || 0);
                        const targetValStr = isNaN(targetVal) ? '0' : String(parseFloat(targetVal.toFixed(4))).replace('.', ',');

                        const monthBadge = isAllYear ? `<td class="text-center" style="padding: 10px 8px;"><span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small fw-bold">${escapeHtml(t.nama_bulan || ('Bulan ' + t.bulan))}</span></td>` : '';

                        rowsHtml += `
                            <tr>
                                <td class="text-center fw-bold text-muted num-tabular" style="padding: 10px 8px;">${idx + 1}</td>
                                ${monthBadge}
                                <td style="padding: 10px 14px;"><div class="fw-semibold text-dark lh-sm" style="font-size: 0.8125rem;">${escapeHtml(t.sasaran_program || '-')}</div></td>
                                <td style="padding: 10px 14px;"><div class="text-secondary lh-sm" style="font-size: 0.8125rem;">${escapeHtml(t.indikator_kinerja || '-')}</div></td>
                                <td class="text-center fw-bold text-primary num-tabular" style="padding: 10px 8px; font-size: 0.8125rem;">${targetValStr}</td>
                                <td class="text-center text-muted small" style="padding: 10px 8px; font-size: 0.75rem;">${escapeHtml(t.satuan || '-')}</td>
                                <td class="text-center" style="padding: 10px 8px;">${badgeKirim}</td>
                                <td class="text-center" style="padding: 10px 8px;">${badgeAppr}</td>
                            </tr>
                        `;
                    });
                    tableBody.innerHTML = rowsHtml;
                } else {
                    const colSpanCount = isAllYear ? 8 : 7;
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="${colSpanCount}" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                <span>Pegawai ini belum memiliki data Target Kinerja Bulanan pada periode ini.</span>
                            </td>
                        </tr>
                    `;
                }

                skeleton.style.display = 'none';
                content.style.display = 'block';
            })
            .catch(err => {
                skeleton.style.display = 'none';
                const isAllYear = (document.getElementById('modalTargetTableHead') && document.getElementById('modalTargetTableHead').innerText.includes('Bulan'));
                const colSpanCount = isAllYear ? 8 : 7;
                tableBody.innerHTML = `<tr><td colspan="${colSpanCount}" class="text-center py-4 text-danger">Terjadi kesalahan saat memuat rincian target.</td></tr>`;
                content.style.display = 'block';
            });
    }

    // Attach click and keyboard events to rows/cards
    document.querySelectorAll('.btn-detail-target-trigger').forEach(triggerEl => {
        triggerEl.addEventListener('click', function(e) {
            openTargetDetail(this);
        });
        triggerEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openTargetDetail(this);
            }
        });
    });

    // Synchronized Export Handlers with SweetAlert2 Loading Alert
    async function triggerExportWithLoading(btnEl, typeText, defaultFilename) {
        const url = btnEl.getAttribute('href');
        if (!url) return;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Menyiapkan ${typeText}...`,
                html: `
                    <div class="d-flex flex-column align-items-center gap-2 my-2">
                        <div class="ecc-loading-spinner-wrapper">
                            <div class="ecc-loading-spinner"></div>
                        </div>
                        <div class="ecc-loading-title">Sedang mengompilasi data target kinerja...</div>
                        <span class="ecc-loading-desc">Sistem sedang merekap data target, sasaran program, dan status persetujuan. Mohon tunggu, berkas akan terunduh otomatis saat proses selesai.</span>
                        <span class="ecc-loading-badge-step"><i class="bi bi-shield-check text-primary"></i> Streaming data terenkripsi & aman</span>
                    </div>
                `,
                customClass: {
                    popup: 'ecc-loading-popup'
                },
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: async () => {
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error('Gagal menyiapkan berkas dari server');
                        }

                        let filename = defaultFilename;
                        const disposition = response.headers.get('Content-Disposition');
                        if (disposition && disposition.includes('filename=')) {
                            const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                            if (match && match[1]) {
                                filename = match[1].replace(/['"]/g, '').trim();
                            }
                        }

                        const blob = await response.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                        const downloadAnchor = document.createElement('a');
                        downloadAnchor.style.display = 'none';
                        downloadAnchor.href = blobUrl;
                        downloadAnchor.download = filename;
                        document.body.appendChild(downloadAnchor);
                        downloadAnchor.click();
                        
                        setTimeout(() => {
                            window.URL.revokeObjectURL(blobUrl);
                            if (document.body.contains(downloadAnchor)) {
                                document.body.removeChild(downloadAnchor);
                            }
                        }, 2000);

                        Swal.fire({
                            icon: 'success',
                            title: 'Unduhan Berhasil!',
                            text: `Berkas ${filename} berhasil disiapkan dan diunduh.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } catch (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengunduh',
                            text: 'Terjadi kendala saat mengompilasi berkas. Silakan coba beberapa saat lagi.'
                        });
                    }
                }
            });
        } else {
            window.location.href = url;
        }
    }

    const btnExportExcel = document.getElementById('btnExportExcel');
    if (btnExportExcel) {
        btnExportExcel.addEventListener('click', function(e) {
            e.preventDefault();
            triggerExportWithLoading(this, 'Berkas Excel (.xlsx)', 'Rekap_Target_Kinerja_ECC.xlsx');
        });
    }

    const btnExportPdf = document.getElementById('btnExportPdf');
    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', function(e) {
            e.preventDefault();
            triggerExportWithLoading(this, 'Laporan PDF Resmi', 'Laporan_Rekapitulasi_Target_ECC.pdf');
        });
    }

    // Helper HTML Escaping
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.innerText = str;
        return div.innerHTML;
    }

});
</script>
<?= $this->endSection() ?>
