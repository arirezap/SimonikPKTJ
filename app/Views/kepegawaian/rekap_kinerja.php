<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap Kinerja Kepegawaian<?= $this->endSection() ?>

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
    padding: 6px 28px 6px 12px !important;
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
    padding: 6px 8px !important;
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.filter-input-year:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Skeleton Loader Animation */
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
.skeleton-box {
    display: inline-block;
    height: 1em;
    position: relative;
    overflow: hidden;
    background-color: #e2e8f0;
    border-radius: 4px;
}
.skeleton-box::after {
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.2) 20%, rgba(255,255,255,0.5) 60%, rgba(255,255,255,0));
    animation: shimmer 2s infinite;
    content: '';
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
    height: 28px;
    padding: 0 12px;
    border-radius: 16px;
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

/* Tabular Numerics for Precision Alignment */
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* KPI Stat Boxes in Bento Strip */
.kpi-stat-box {
    border-radius: 8px;
    padding: 8px 4px;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.kpi-stat-box:hover {
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
.table-bento tbody tr:hover td {
    background-color: #f8fafc !important;
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

.cursor-pointer {
    cursor: pointer;
}
.pegawai-row {
    transition: background-color 0.18s ease;
}
.pegawai-row:hover .row-hover-hint {
    opacity: 1;
    transform: translateX(3px);
}
.row-hover-hint {
    opacity: 0;
    transition: opacity 0.18s ease, transform 0.18s ease;
    color: #0d6efd;
    font-size: 0.8rem;
}

/* Mobile Card Styling (8-Point Grid Scale) */
.mobile-pegawai-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s ease, border-color 0.18s ease;
    margin-bottom: 8px;
    outline: none;
}
.mobile-pegawai-card:hover,
.mobile-pegawai-card:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06) !important;
    border-color: #0d6efd !important;
}

/* Modal Backdrop Blur & Spring Reveal */
.modal-backdrop.show {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
#modalDetailPegawai .modal-content {
    animation: modalContentSpring 0.28s cubic-bezier(0.16, 1, 0.3, 1) both;
}
@keyframes modalContentSpring {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
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

/* Accessibility Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .bento-stagger,
    .filter-pill,
    .mobile-pegawai-card,
    #modalDetailPegawai .modal-content {
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
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-star-fill text-primary me-2"></i>Monitoring Penilaian Kinerja</h1>
            <p class="text-muted small mb-0">Pemantauan dan rekapitulasi penilaian capaian kinerja bulanan seluruh pegawai.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('kepegawaian/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>&role=<?= esc($role_filter ?? '') ?>" class="btn btn-sm btn-success shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 btn-tactile" id="btnExportExcel" title="Unduh Workbook Excel (.xlsx) Multi-Sheet Data Lengkap" aria-label="Unduh Rekapitulasi Kinerja Excel Multi-Sheet Lengkap">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <a href="<?= site_url('kepegawaian/export-pdf') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>&role=<?= esc($role_filter ?? '') ?>" class="btn btn-sm btn-danger shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 btn-tactile" id="btnExportPdf" title="Unduh Laporan PDF Resmi A4 Landscape" aria-label="Unduh Rekapitulasi Kinerja PDF Resmi A4 Landscape">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- FILTER & KPI SUMMARY ROW -->
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">
        <!-- FILTER CARD (LEFT) -->
        <div class="col-lg-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                        <i class="bi bi-funnel-fill text-primary me-2 fs-6"></i>
                        <span>Filter Periode, Unit Kerja & Kategori</span>
                    </h6>
                    <?php if (!empty($unit_filter) || !empty($role_filter)): ?>
                        <a href="<?= site_url('kepegawaian') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>" class="badge bg-secondary-subtle text-secondary text-decoration-none rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="<?= site_url('kepegawaian') ?>" id="filterForm">
                        <div class="row g-2 align-items-end mb-2">
                            <!-- Filter Bulan -->
                            <div class="col-6 col-sm-4">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Periode Bulan</label>
                                <select name="bulan" class="form-select form-select-sm filter-select-custom shadow-sm" aria-label="Pilih Periode Bulan Rekap" onchange="showSkeletonAndSubmit()">
                                    <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun</option>
                                    <?php foreach($bulan_indo as $index => $nama): ?>
                                        <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= $nama ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Filter Tahun -->
                            <div class="col-6 col-sm-3">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Tahun</label>
                                <input type="number" name="tahun" id="filterTahunInput" class="form-control form-control-sm text-center fw-bold filter-input-year shadow-sm num-tabular" aria-label="Input Tahun Rekap" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="showSkeletonAndSubmit()">
                            </div>

                            <!-- Filter Kategori Jabatan -->
                            <div class="col-12 col-sm-5">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Kategori Jabatan</label>
                                <select name="role" class="form-select form-select-sm filter-select-custom shadow-sm" aria-label="Pilih Filter Kategori Role" onchange="showSkeletonAndSubmit()">
                                    <option value="">Semua Jabatan</option>
                                    <option value="pimpinan" <?= (($role_filter ?? '') === 'pimpinan') ? 'selected' : '' ?>>Pimpinan (Direktur & Wadir)</option>
                                    <option value="manajemen" <?= (($role_filter ?? '') === 'manajemen' || ($role_filter ?? '') === 'struktural') ? 'selected' : '' ?>>Struktural & Manajemen</option>
                                    <option value="kepegawaian" <?= (($role_filter ?? '') === 'kepegawaian') ? 'selected' : '' ?>>Kepegawaian</option>
                                    <option value="user" <?= (($role_filter ?? '') === 'user' || ($role_filter ?? '') === 'staf') ? 'selected' : '' ?>>Staf Pelaksana & Fungsional</option>
                                    <option value="tugas_belajar" <?= (($role_filter ?? '') === 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Unit Kerja -->
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Unit Kerja</label>
                                <select name="unit" class="form-select form-select-sm filter-select-custom shadow-sm" aria-label="Pilih Filter Unit Kerja" onchange="showSkeletonAndSubmit()">
                                    <option value="">Semua Unit Kerja</option>
                                    <?php foreach ($daftar_unit as $u): ?>
                                        <option value="<?= esc($u) ?>" <?= ($u == $unit_filter) ? 'selected' : '' ?>><?= esc($u) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI SUMMARY CARD (RIGHT - 5 METRIC BOXES) -->
        <div class="col-lg-6 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                        <i class="bi bi-pie-chart-fill text-primary me-2 fs-6"></i>
                        <span>Ringkasan Status Penilaian Pegawai</span>
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 small num-tabular fw-bold" title="Total pegawai terdaftar">
                        <?= count($rekap_kinerja) ?> Total Pegawai
                    </span>
                </div>
                <div class="card-body p-2.5 p-xl-3 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0 align-items-stretch">
                        <!-- 1. Total Pegawai -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Jumlah seluruh pegawai aktif non-admin">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Total</div>
                            <div class="my-1">
                                <div class="fw-bold text-dark num-tabular skeleton-hide" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;" id="statTotalPegawai" data-val="<?= count($rekap_kinerja) ?>"><?= count($rekap_kinerja) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.5rem; display: none;"></div>
                            </div>
                            <div class="text-muted small" style="font-size: 0.65rem; white-space: nowrap;">Pegawai</div>
                        </div>

                        <!-- 2. Sudah Dinilai -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang sudah memiliki nilai capaian kinerja">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Dinilai</div>
                            <div class="my-1">
                                <div class="fw-bold text-success num-tabular skeleton-hide" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;" id="statSudahDinilai" data-val="<?= esc($sudah_dinilai) ?>"><?= esc($sudah_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.5rem; display: none;"></div>
                            </div>
                            <div class="text-success small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">
                                <?= count($rekap_kinerja) > 0 ? round(($sudah_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>

                        <!-- 3. Belum Dinilai -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang belum mendapatkan penilaian kinerja">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Belum</div>
                            <div class="my-1">
                                <div class="fw-bold text-danger num-tabular skeleton-hide" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2;" id="statBelumDinilai" data-val="<?= esc($belum_dinilai) ?>"><?= esc($belum_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.5rem; display: none;"></div>
                            </div>
                            <div class="text-danger small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">
                                <?= count($rekap_kinerja) > 0 ? round(($belum_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>

                        <!-- 4. Rata-Rata Dinilai (Sampel Pegawai yang Sudah Terbit Nilainya) -->
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Rata-rata mutu dari seluruh pegawai yang sudah dinilai">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Rata Dinilai</div>
                            <div class="my-1">
                                <div class="fw-bold text-primary num-tabular skeleton-hide" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2; letter-spacing: -0.5px;" id="statRataRataDinilai" data-val="<?= round($rata_rata_dinilai ?? $rata_rata_instansi, 2) ?>"><?= str_replace('.', ',', round($rata_rata_dinilai ?? $rata_rata_instansi, 2)) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.5rem; display: none;"></div>
                            </div>
                            <div class="text-primary small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">Sudah Dinilai</div>
                        </div>

                        <!-- 5. Rata-Rata Keseluruhan (Total Semua Pegawai, Belum Dinilai = 0) -->
                        <div class="col px-1 d-flex flex-column justify-content-between kpi-stat-box" title="Rata-rata capaian dari seluruh total pegawai (yang belum dinilai dihitung 0)">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.3px;">Rata Total</div>
                            <div class="my-1">
                                <div class="fw-bold text-info-emphasis num-tabular skeleton-hide" style="font-size: 1.25rem; white-space: nowrap; line-height: 1.2; letter-spacing: -0.5px;" id="statRataRataTotal" data-val="<?= round($rata_rata_keseluruhan ?? 0, 2) ?>"><?= str_replace('.', ',', round($rata_rata_keseluruhan ?? 0, 2)) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.5rem; display: none;"></div>
                            </div>
                            <div class="text-muted small fw-semibold" style="font-size: 0.65rem; white-space: nowrap;">Semua Pegawai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAP KINERJA (MAIN BENTO CARD) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        <!-- Card Header with Live Search, Counter & Hint -->
        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 small d-flex align-items-center">
                    <i class="bi bi-table text-primary me-2 fs-6"></i>
                    <span>Daftar Rekapitulasi Penilaian Kinerja</span>
                </h6>
                <div class="d-flex align-items-center gap-2 mt-0.5">
                    <small class="text-muted" id="visibleCounter" style="font-size: 0.72rem;">Menampilkan <?= count($rekap_kinerja) ?> pegawai</small>
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
                    <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, NIP, unit..." autocomplete="off" aria-label="Pencarian cepat nama pegawai, NIP, jabatan, atau unit kerja">
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-bold">
                    <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                </span>
            </div>
        </div>

        <!-- Quick Status Filter Pills (8px Compact Grid Spacing - 1 Single Row) -->
        <div class="bg-white border-bottom d-flex align-items-center flex-nowrap" id="pillFilterGroup">
            <span class="text-muted small fw-bold me-1 flex-shrink-0" style="font-size: 0.7rem; letter-spacing: 0.3px;">Filter Cepat:</span>
            <button type="button" class="filter-pill active" data-filter="all" role="button" aria-label="Tampilkan Semua Pegawai"><i class="bi bi-grid-fill"></i> Semua</button>
            <button type="button" class="filter-pill" data-filter="sudah" role="button" aria-label="Filter Pegawai yang Sudah Dinilai"><i class="bi bi-check-circle-fill text-success"></i> Sudah Dinilai</button>
            <button type="button" class="filter-pill" data-filter="belum" role="button" aria-label="Filter Pegawai yang Belum Dinilai"><i class="bi bi-hourglass-split text-warning"></i> Belum Dinilai</button>
            <button type="button" class="filter-pill" data-filter="tidak_mengerjakan" role="button" aria-label="Filter Pegawai yang Tidak Mengerjakan Target/Laporan"><i class="bi bi-x-octagon-fill text-danger"></i> Tidak Mengerjakan</button>
            <button type="button" class="filter-pill" data-filter="perhatian" role="button" aria-label="Filter Pegawai Nilai Kurang dari 75"><i class="bi bi-exclamation-triangle-fill text-warning"></i> &lt; 75 (Perhatian)</button>
            <button type="button" class="filter-pill" data-filter="baik" role="button" aria-label="Filter Pegawai Nilai Baik Lebih dari 90"><i class="bi bi-stars text-primary"></i> &ge; 90 (Baik)</button>
        </div>

        <div class="card-body p-0">
            <!-- Loading State Skeleton -->
            <div id="tableLoader" style="display: none;">
                <div class="p-4">
                    <?php for($i=0; $i<5; $i++): ?>
                    <div class="d-flex mb-3 align-items-center">
                        <div class="skeleton-box me-3 flex-shrink-0" style="width: 36px; height: 36px; border-radius: 50%;"></div>
                        <div class="me-auto" style="width: 200px;">
                            <div class="skeleton-box mb-1" style="width: 100%; height: 14px;"></div>
                            <div class="skeleton-box" style="width: 60%; height: 10px;"></div>
                        </div>
                        <div class="skeleton-box me-3" style="width: 15%; height: 20px; border-radius: 20px;"></div>
                        <div class="skeleton-box me-3" style="width: 10%; height: 24px; border-radius: 20px;"></div>
                        <div class="skeleton-box" style="width: 12%; height: 28px; border-radius: 20px;"></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- DESKTOP TABLE VIEW -->
            <div class="table-responsive desktop-table-view" id="tableContent">
                <table class="table table-hover table-bento mb-0 align-middle" id="mainDataTable">
                    <thead>
                        <tr>
                            <th class="text-center sortable-th th-sorted" style="width: 55px;" data-sort="no" tabindex="0" role="button" aria-sort="ascending" title="Nomor urut resmi">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>No</span>
                                    <i class="bi bi-arrow-up sort-indicator text-primary fw-bold"></i>
                                </div>
                            </th>
                            <th style="min-width: 270px;" class="sortable-th" data-sort="nama" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan nama pegawai">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Pegawai & Jabatan</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th style="min-width: 170px;" class="sortable-th" data-sort="unit" tabindex="0" role="button" aria-sort="none" title="Klik untuk mengurutkan berdasarkan unit kerja">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Unit Kerja</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <?php if ($bulan_terpilih === 'all'): ?>
                                <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m): ?>
                                    <th class="text-center" style="min-width: 48px; font-size: 0.75rem;"><?= $m ?></th>
                                <?php endforeach; ?>
                                <th class="text-center border-start bg-light shadow-sm sortable-th" data-sort="target" tabindex="0" role="button" aria-sort="none" style="min-width: 130px;" title="Klik untuk mengurutkan target tahunan">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                        <span>Target & Tambahan</span>
                                        <i class="bi bi-arrow-down-up sort-indicator"></i>
                                    </div>
                                </th>
                            <?php else: ?>
                                <th class="text-center sortable-th" data-sort="target" tabindex="0" role="button" aria-sort="none" style="min-width: 140px;" title="Klik untuk mengurutkan jumlah target komponen">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                        <span>Komponen Kinerja</span>
                                        <i class="bi bi-arrow-down-up sort-indicator"></i>
                                    </div>
                                </th>
                            <?php endif; ?>
                            <th class="text-center sortable-th" data-sort="dinilai" tabindex="0" role="button" aria-sort="none" style="width: 120px;" title="Klik untuk mengurutkan komponen yang sudah dinilai">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Dinilai</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="text-center pe-3 sortable-th" data-sort="nilai" tabindex="0" role="button" aria-sort="none" style="width: 140px;" title="Klik untuk mengurutkan nilai rata-rata">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Nilai Akhir</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($rekap_kinerja)): ?>
                            <tr class="no-data-row">
                                <td colspan="<?= ($bulan_terpilih === 'all') ? '18' : '6' ?>" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-folder-x fs-1 mb-2 opacity-50"></i>
                                        <span class="small fw-semibold">Belum ada data pegawai untuk filter ini.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rekap_kinerja as $index => $item): ?>
                                <?php
                                    $rata = $item['rata_rata'];
                                    $dinilai = $item['rhk_dinilai'];
                                    $jumlahPokok = $item['jumlah_pokok'] ?? $item['jumlah_rhk'];
                                    $jumlahTambahan = $item['jumlah_tambahan'] ?? 0;
                                    $jumlahTarget = $item['jumlah_komponen'] ?? ($jumlahPokok + ($jumlahTambahan > 0 ? 1 : 0));
                                    
                                    $statusCat = '';
                                    if ($jumlahTarget == 0) {
                                        $statusCat = 'tidak_mengerjakan belum';
                                    } elseif ($dinilai > 0) {
                                        $statusCat = 'sudah';
                                        if ($rata < 75) $statusCat .= ' perhatian';
                                        if ($rata >= 90) $statusCat .= ' baik';
                                    } else {
                                        $statusCat = 'belum';
                                    }

                                    if ($dinilai == 0) {
                                        $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                        $statusText = 'Belum Dinilai';
                                    } elseif ($rata <= 25) {
                                        $badgeClass = 'bg-danger text-white';
                                        $statusText = 'Sangat Kurang';
                                    } elseif ($rata <= 75) {
                                        $badgeClass = 'bg-warning text-dark';
                                        $statusText = 'Kurang';
                                    } elseif ($rata <= 90) {
                                        $badgeClass = 'bg-info text-dark';
                                        $statusText = 'Butuh Perbaikan';
                                    } elseif ($rata <= 100) {
                                        $badgeClass = 'bg-primary text-white';
                                        $statusText = 'Baik';
                                    } else {
                                        $badgeClass = 'bg-success text-white';
                                        $statusText = 'Sangat Baik';
                                    }
                                ?>
                                <tr class="pegawai-row cursor-pointer btn-detail-pegawai" 
                                    role="button"
                                    tabindex="0"
                                    title="Klik untuk melihat rincian capaian kinerja <?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-user-id="<?= $item['pegawai']['id'] ?>" 
                                    data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                    data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                    data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                    data-jabatan="<?= esc(strtolower($item['pegawai']['jabatan'] ?? '')) ?>"
                                    data-status="<?= $statusCat ?>"
                                    data-val-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-val-unit="<?= esc($item['pegawai']['unit'] ?? '') ?>"
                                    data-val-target="<?= $jumlahTarget ?>"
                                    data-val-pokok="<?= $jumlahPokok ?>"
                                    data-val-tambahan="<?= $jumlahTambahan ?>"
                                    data-val-dinilai="<?= $dinilai ?>"
                                    data-val-nilai="<?= $rata ?>">
                                    
                                    <td class="text-center fw-bold text-muted num-tabular"><?= $index + 1 ?></td>

                                    <!-- Pegawai & Jabatan -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <?php if (!empty($item['pegawai']['foto'])): ?>
                                                <img src="<?= base_url('uploads/foto_profil/' . $item['pegawai']['foto']) ?>" alt="Foto" class="rounded-circle object-fit-cover shadow-sm flex-shrink-0" width="38" height="38">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                                    <?= strtoupper(substr($item['pegawai']['nama_lengkap'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="lh-sm" style="min-width: 0;">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;" title="<?= esc($item['pegawai']['nama_lengkap']) ?>">
                                                    <?= esc($item['pegawai']['nama_lengkap']) ?>
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.72rem;">
                                                    NIP: <span class="num-tabular"><?= !empty($item['pegawai']['nip']) ? esc($item['pegawai']['nip']) : '-' ?></span>
                                                </div>
                                                <div class="text-secondary small fw-medium mt-0.5" style="font-size: 0.7rem;">
                                                    <?= !empty($item['pegawai']['jabatan']) ? esc($item['pegawai']['jabatan']) : '-' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Unit Kerja (PERSIS SEPERTI DI MONITORING TARGET KINERJA) -->
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-wrap text-start" style="font-size: 0.74rem;">
                                            <?= !empty($item['pegawai']['unit']) ? esc($item['pegawai']['unit']) : '-' ?>
                                        </span>
                                    </td>

                                    <?php if ($bulan_terpilih === 'all'): ?>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <?php $val = $item['rata_rata_per_bulan'][$i]; ?>
                                            <td class="text-center num-tabular" style="font-size: 0.78rem;">
                                                <?php if ($val !== null): ?>
                                                    <span class="fw-bold <?= $val <= 75 ? 'text-danger' : 'text-success' ?>"><?= str_replace('.', ',', (float)$val) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted opacity-25">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                        <td class="text-center border-start bg-light bg-opacity-50 num-tabular">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;" title="Total Target Tugas Pokok Setahun">
                                                    <?= $jumlahPokok ?> Pokok
                                                </span>
                                                <span class="badge <?= $jumlahTambahan > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?> rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="Total Kegiatan Tugas Tambahan Setahun">
                                                    <?= $jumlahTambahan ?> Tambahan
                                                </span>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-center num-tabular">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;" title="Target RHK Tugas Pokok">
                                                    <i class="bi bi-briefcase-fill me-1"></i><?= $jumlahPokok ?> Pokok
                                                </span>
                                                <?php if ($jumlahTambahan > 0): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;" title="Jumlah Kegiatan Tugas Tambahan">
                                                        <i class="bi bi-journal-plus me-1"></i><?= $jumlahTambahan ?> Tambahan
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="Tidak ada tugas tambahan">
                                                        <i class="bi bi-dash me-0.5"></i>0 Tambahan
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                    
                                    <td class="text-center num-tabular">
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1">
                                            <span class="fw-bold <?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary' ?>"><?= $dinilai ?></span>
                                            <span class="text-muted mx-0.5">/</span>
                                            <span class="text-muted"><?= $jumlahTarget ?></span>
                                        </span>
                                    </td>

                                    <td class="text-center pe-3 num-tabular">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <span class="fw-bold <?= ($rata > 75) ? 'text-success' : (($dinilai == 0) ? 'text-secondary' : 'text-danger') ?>" style="font-size: 0.95rem;">
                                                <?= str_replace('.', ',', round($rata, 2)) ?>
                                            </span>
                                            <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.65rem; font-weight: 500;">
                                                <?= $statusText ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- DYNAMIC EMPTY STATE FOR LIVE SEARCH (DESKTOP) -->
                        <tr id="desktopLiveSearchEmpty" style="display: none;">
                            <td colspan="<?= ($bulan_terpilih === 'all') ? '18' : '6' ?>" class="text-center py-5">
                                <div class="text-muted d-flex flex-column align-items-center">
                                    <i class="bi bi-search fs-1 mb-2 opacity-50"></i>
                                    <span class="small fw-semibold">Tidak ada pegawai yang sesuai dengan pencarian "<strong id="desktopSearchQueryTerm" class="text-dark"></strong>"</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- MOBILE CARDS VIEW (<768px) -->
            <div class="mobile-cards-view p-3" id="mobileCardsContainer">
                <?php if (!empty($rekap_kinerja)): ?>
                    <div class="d-flex flex-column gap-2" id="mobileCardsList">
                        <?php foreach ($rekap_kinerja as $item): ?>
                            <?php
                                $rata = $item['rata_rata'];
                                $dinilai = $item['rhk_dinilai'];
                                $jumlahPokok = $item['jumlah_pokok'] ?? $item['jumlah_rhk'];
                                $jumlahTambahan = $item['jumlah_tambahan'] ?? 0;
                                $jumlahTarget = $item['jumlah_komponen'] ?? ($jumlahPokok + ($jumlahTambahan > 0 ? 1 : 0));
                                
                                $statusCat = '';
                                if ($jumlahTarget == 0) {
                                    $statusCat = 'tidak_mengerjakan belum';
                                } elseif ($dinilai > 0) {
                                    $statusCat = 'sudah';
                                    if ($rata < 75) $statusCat .= ' perhatian';
                                    if ($rata >= 90) $statusCat .= ' baik';
                                } else {
                                    $statusCat = 'belum';
                                }

                                if ($dinilai == 0) {
                                    $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                    $statusText = 'Belum Dinilai';
                                } elseif ($rata <= 25) {
                                    $badgeClass = 'bg-danger text-white';
                                    $statusText = 'Sangat Kurang';
                                } elseif ($rata <= 75) {
                                    $badgeClass = 'bg-warning text-dark';
                                    $statusText = 'Kurang';
                                } elseif ($rata <= 90) {
                                    $badgeClass = 'bg-info text-dark';
                                    $statusText = 'Butuh Perbaikan';
                                } elseif ($rata <= 100) {
                                    $badgeClass = 'bg-primary text-white';
                                    $statusText = 'Baik';
                                } else {
                                    $badgeClass = 'bg-success text-white';
                                    $statusText = 'Sangat Baik';
                                }
                            ?>
                            <div class="mobile-pegawai-card pegawai-row cursor-pointer btn-detail-pegawai"
                                 role="button"
                                 tabindex="0"
                                 data-user-id="<?= $item['pegawai']['id'] ?>"
                                 data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                 data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                 data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                 data-jabatan="<?= esc(strtolower($item['pegawai']['jabatan'] ?? '')) ?>"
                                 data-status="<?= $statusCat ?>"
                                 data-val-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                 data-val-unit="<?= esc($item['pegawai']['unit'] ?? '') ?>"
                                 data-val-target="<?= $jumlahTarget ?>"
                                 data-val-pokok="<?= $jumlahPokok ?>"
                                 data-val-tambahan="<?= $jumlahTambahan ?>"
                                 data-val-dinilai="<?= $dinilai ?>"
                                 data-val-nilai="<?= $rata ?>"
                                 title="Ketuk untuk melihat rincian capaian kinerja <?= esc($item['pegawai']['nama_lengkap']) ?>">
                                
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small">
                                        <?= !empty($item['pegawai']['unit']) ? esc($item['pegawai']['unit']) : '-' ?>
                                    </span>
                                    <span class="badge <?= $badgeClass ?> rounded-pill px-2.5 py-1 fw-bold num-tabular">
                                        <?= str_replace('.', ',', round($rata, 2)) ?>% • <?= $statusText ?>
                                    </span>
                                </div>

                                <div class="d-flex align-items-center gap-2.5 mb-2.5">
                                    <?php if (!empty($item['pegawai']['foto'])): ?>
                                        <img src="<?= base_url('uploads/foto_profil/' . $item['pegawai']['foto']) ?>" alt="Foto" class="rounded-circle object-fit-cover shadow-sm flex-shrink-0" width="40" height="40">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                            <?= strtoupper(substr($item['pegawai']['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="lh-sm flex-grow-1" style="min-width: 0;">
                                        <div class="fw-bold text-dark mobile-nama-text"><?= esc($item['pegawai']['nama_lengkap']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">NIP: <span class="num-tabular"><?= !empty($item['pegawai']['nip']) ? esc($item['pegawai']['nip']) : '-' ?></span></div>
                                        <div class="text-secondary small fw-medium mt-0.5" style="font-size: 0.7rem;"><?= !empty($item['pegawai']['jabatan']) ? esc($item['pegawai']['jabatan']) : '-' ?></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2 flex-wrap">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="bi bi-briefcase-fill me-1"></i><?= $jumlahPokok ?> Pokok
                                        </span>
                                        <span class="badge <?= $jumlahTambahan > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?> rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            <i class="bi <?= $jumlahTambahan > 0 ? 'bi-journal-plus' : 'bi-dash' ?> me-1"></i><?= $jumlahTambahan ?> Tambahan
                                        </span>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                            Dinilai: <strong class="<?= ($dinilai == $jumlahTarget && $jumlahTarget > 0) ? 'text-success' : 'text-primary' ?>"><?= $dinilai ?>/<?= $jumlahTarget ?></strong>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 text-primary small fw-semibold">
                                        <span>Rincian</span>
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- DYNAMIC EMPTY STATE FOR LIVE SEARCH (MOBILE) -->
                    <div id="mobileLiveSearchEmpty" style="display: none;" class="text-center py-5">
                        <div class="text-muted d-flex flex-column align-items-center">
                            <i class="bi bi-search fs-1 mb-2 opacity-50"></i>
                            <span class="small fw-semibold">Tidak ada pegawai yang cocok dengan pencarian</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL KINERJA PEGAWAI (BENTO POPUP - 8pt Grid System) -->
<div class="modal fade" id="modalDetailPegawai" tabindex="-1" aria-labelledby="modalDetailPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            <div class="modal-header bg-light border-bottom" style="padding: 16px 24px;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-person-badge fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="detailNamaPegawai" style="font-size: 1rem; line-height: 1.3;">Rincian Kinerja Pegawai</h6>
                        <small class="text-muted d-block" id="detailSubPegawai" style="font-size: 0.75rem; margin-top: 2px;">Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <!-- Loader Inside Modal -->
                <div id="modalDetailLoader" class="text-center py-5">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted small">Mengambil rincian target dan evaluasi...</div>
                </div>

                <!-- Modal Detail Content -->
                <div id="modalDetailContent" style="display: none;">
                    <!-- EXECUTIVE PROFILE & SCORECARD (8pt Grid) -->
                    <div class="card border rounded-4 shadow-sm" style="background-color: #f8fafc; border-color: #e2e8f0 !important; padding: 16px; margin-bottom: 24px;">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center" style="gap: 16px;">
                            <div class="flex-grow-1">
                                <div class="text-muted fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; margin-bottom: 4px;">Profil Pegawai</div>
                                <div class="fw-bold text-dark" id="modalInfoNama" style="font-size: 1.1rem; line-height: 1.3; margin-bottom: 4px;">-</div>
                                <div class="text-secondary" id="modalInfoNipUnit" style="font-size: 0.75rem; line-height: 1.4; margin-bottom: 8px;">-</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.75rem;">
                                    <div class="text-muted d-flex align-items-center" id="modalInfoAtasan" style="gap: 6px;">
                                        <i class="bi bi-person-check text-success"></i> 
                                        <span>Atasan Langsung: <strong id="modalTextAtasan" class="text-dark">-</strong></span>
                                    </div>
                                    <span class="text-muted opacity-50">|</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" id="modalStatPokok">0 Tugas Pokok</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" id="modalStatTambahan">0 Tugas Tambahan</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center bg-white rounded-3 border shadow-sm flex-shrink-0" style="padding: 12px 16px; gap: 16px;">
                                <div class="text-end">
                                    <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.4px; margin-bottom: 4px;">NILAI AKHIR</div>
                                    <span class="badge" id="modalDetailBadge" style="font-size: 0.68rem; padding: 4px 8px; border-radius: 8px;">-</span>
                                </div>
                                <div class="fw-bold text-primary num-tabular lh-1" id="modalDetailScore" style="font-size: 1.75rem; min-width: 60px; text-align: right;">0,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL A: TARGET KINERJA RHK -->
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 8px;">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size: 0.82rem; gap: 8px;">
                            <i class="bi bi-list-task text-primary fs-6"></i>
                            <span>A. Rincian Target Kinerja (Tugas Pokok)</span>
                        </h6>
                        <span class="badge bg-light text-dark border" id="modalBadgeCountRhk" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 8px;">0 Tugas Pokok</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-3 shadow-sm overflow-hidden" style="margin-bottom: 24px;">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento" style="font-size: 0.78rem;">
                            <thead>
                                <tr class="text-center align-middle" style="background-color: #f8fafc;">
                                    <th style="width: 44px; padding: 10px 8px;" class="text-center">No</th>
                                    <th style="padding: 10px 12px;" class="text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 120px; padding: 10px 8px;" class="text-center">Target</th>
                                    <th style="width: 120px; padding: 10px 8px;" class="text-center">Realisasi</th>
                                    <th style="width: 130px; padding: 10px 8px;" class="text-center">Selisih</th>
                                    <th style="width: 110px; padding: 10px 8px;" class="text-center">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableRhkBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- TABEL B: TUGAS TAMBAHAN -->
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 8px;">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size: 0.82rem; gap: 8px;">
                            <i class="bi bi-journal-plus text-success fs-6"></i>
                            <span>B. Tugas Tambahan</span>
                        </h6>
                        <span class="badge bg-light text-dark border" id="modalBadgeScoreTambahan" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 8px;">Nilai: -</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-3 shadow-sm overflow-hidden" style="margin-bottom: 8px;">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento" style="font-size: 0.78rem;">
                            <thead>
                                <tr class="text-center align-middle" style="background-color: #f8fafc;">
                                    <th style="width: 44px; padding: 10px 8px;" class="text-center">No</th>
                                    <th style="padding: 10px 12px;" class="text-start">Deskripsi Kegiatan</th>
                                    <th style="width: 110px; padding: 10px 8px;" class="text-center">Tanggal</th>
                                    <th style="width: 120px; padding: 10px 8px;" class="text-center">Capaian</th>
                                    <th style="width: 90px; padding: 10px 8px;" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableTambahanBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Error State -->
                <div id="modalDetailError" style="display: none;" class="text-center py-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-2"></i>
                    <h6 class="fw-bold text-dark">Gagal Memuat Data</h6>
                    <p class="text-muted small mb-3" id="modalDetailErrorMessage">Terjadi kesalahan saat mengambil rincian data pegawai.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="retryFetchDetail()">Coba Lagi</button>
                </div>
            </div>
            <div class="modal-footer bg-light border-top" style="padding: 12px 24px;">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" style="height: 32px; font-size: 0.78rem;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Precision Metric Count-Up Number Ticker
function animateValue(el, start, end, duration = 750, decimals = 0, suffix = '') {
    if (!el) return;
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || duration <= 0) {
        el.textContent = (decimals > 0 ? end.toFixed(decimals).replace('.', ',') : Math.round(end)) + suffix;
        return;
    }
    const startTime = performance.now();
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // easeOutExpo
        const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = start + (end - start) * ease;
        el.textContent = (decimals > 0 ? current.toFixed(decimals).replace('.', ',') : Math.round(current)) + suffix;
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    requestAnimationFrame(update);
}

function showSkeletonAndSubmit() {
    document.querySelectorAll('.skeleton-hide').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.skeleton-show').forEach(el => el.style.display = 'block');
    const tableContent = document.getElementById('tableContent');
    const tableLoader = document.getElementById('tableLoader');
    if (tableContent) tableContent.style.display = 'none';
    if (tableLoader) tableLoader.style.display = 'block';

    setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 50);
}

let lastActiveUserId = null;
let lastActiveUserNama = '';

function retryFetchDetail() {
    if (lastActiveUserId) {
        loadDetailPegawai(lastActiveUserId, lastActiveUserNama);
    }
}

function loadDetailPegawai(userId, nama) {
    lastActiveUserId = userId;
    lastActiveUserNama = nama;

    const modalEl = document.getElementById('modalDetailPegawai');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

    document.getElementById('detailNamaPegawai').textContent = nama;
    document.getElementById('modalDetailLoader').style.display = 'block';
    document.getElementById('modalDetailContent').style.display = 'none';
    document.getElementById('modalDetailError').style.display = 'none';

    modalInstance.show();

    const bulanTerpilih = '<?= esc($bulan_terpilih) ?>';
    const tahunTerpilih = '<?= esc($tahun_terpilih) ?>';

    fetch(`<?= site_url('kepegawaian/detail-pegawai') ?>?user_id=${userId}&bulan=${bulanTerpilih}&tahun=${tahunTerpilih}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Gagal memuat rincian');
            }

            // Helper format angka tanpa trailing precision artifacts
            function formatAngkaIndo(val) {
                if (val === null || val === undefined || isNaN(val)) return '0';
                const num = Math.round(Number(val) * 10000) / 10000;
                return String(num).replace('.', ',');
            }

            // Populate Pegawai Info
            document.getElementById('modalInfoNama').textContent = data.pegawai.nama;
            const nipText = data.pegawai.nip ? `NIP: ${data.pegawai.nip}` : 'NIP: -';
            const unitText = data.pegawai.unit ? `Unit: ${data.pegawai.unit}` : 'Unit: -';
            const jabText = data.pegawai.jabatan ? `Jabatan: ${data.pegawai.jabatan}` : 'Jabatan: -';
            document.getElementById('modalInfoNipUnit').textContent = `${nipText} | ${unitText} | ${jabText}`;
            document.getElementById('modalTextAtasan').textContent = data.pegawai.atasan_nama || '-';
            
            // Executive Stat Pills
            const statPokokEl = document.getElementById('modalStatPokok');
            if (statPokokEl) statPokokEl.textContent = `${data.total_pokok || data.total_rhk || 0} Tugas Pokok`;
            const statTambahanEl = document.getElementById('modalStatTambahan');
            if (statTambahanEl) statTambahanEl.textContent = `${data.total_tambahan || (data.tugas_tambahan ? data.tugas_tambahan.length : 0)} Tugas Tambahan`;

            // Scorecard with live ticker
            const scoreVal = parseFloat(data.rata_rata) || 0;
            const scoreEl = document.getElementById('modalDetailScore');
            animateValue(scoreEl, 0, scoreVal, 600, 2);

            const badgeEl = document.getElementById('modalDetailBadge');
            badgeEl.className = `badge ${data.badge_class} rounded-pill px-2 py-0.5`;
            badgeEl.textContent = data.predikat;

            // Tabel A: RHK (Tugas Pokok)
            document.getElementById('modalBadgeCountRhk').textContent = `${data.total_pokok || data.total_rhk || 0} Target Pokok`;
            const rhkBody = document.getElementById('modalTableRhkBody');
            rhkBody.innerHTML = '';
            if (data.rekap_rhk && data.rekap_rhk.length > 0) {
                data.rekap_rhk.forEach((rhk, i) => {
                    const selisih = Math.round((Number(rhk.selisih) || 0) * 10000) / 10000;
                    let selisihBadge = '';
                    if (selisih > 0) {
                        selisihBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 num-tabular" style="font-size: 0.72rem; border-radius: 6px;">+${formatAngkaIndo(selisih)} ${rhk.satuan}</span>`;
                    } else if (selisih < 0) {
                        selisihBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 num-tabular" style="font-size: 0.72rem; border-radius: 6px;">${formatAngkaIndo(selisih)} ${rhk.satuan}</span>`;
                    } else {
                        selisihBadge = `<span class="badge bg-light text-dark border px-2 py-0.5 num-tabular" style="font-size: 0.72rem; border-radius: 6px;">0 ${rhk.satuan}</span>`;
                    }

                    const nilaiText = rhk.nilai_capaian !== null ? `<span class="fw-bold text-primary num-tabular">${formatAngkaIndo(rhk.nilai_capaian)}%</span>` : `<span class="text-muted">-</span>`;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-muted" style="padding: 10px 8px;">${i + 1}</td>
                        <td class="fw-semibold text-dark lh-sm" style="padding: 10px 12px; font-size: 0.78rem;">${rhk.indikator}</td>
                        <td class="text-center fw-bold text-dark num-tabular" style="padding: 10px 8px; font-size: 0.78rem;">${formatAngkaIndo(rhk.target)} ${rhk.satuan}</td>
                        <td class="text-center fw-bold text-primary num-tabular" style="padding: 10px 8px; font-size: 0.78rem;">${formatAngkaIndo(rhk.realisasi)} ${rhk.satuan}</td>
                        <td class="text-center num-tabular" style="padding: 10px 8px;">${selisihBadge}</td>
                        <td class="text-center align-middle" style="padding: 10px 8px;">${nilaiText}</td>
                    `;
                    rhkBody.appendChild(tr);
                });
            } else {
                rhkBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4 small">Tidak ada data target RHK pada periode ini.</td></tr>`;
            }

            // Tabel B: Tugas Tambahan
            const tambBody = document.getElementById('modalTableTambahanBody');
            tambBody.innerHTML = '';
            const badgeTambahan = document.getElementById('modalBadgeScoreTambahan');
            if (data.score_tambahan !== null) {
                badgeTambahan.className = 'badge bg-success-subtle text-success border border-success-subtle small';
                badgeTambahan.innerHTML = `<i class="bi bi-check-circle me-1"></i> Nilai: <strong>${formatAngkaIndo(data.score_tambahan)}%</strong>`;
            } else if (data.tugas_tambahan && data.tugas_tambahan.length > 0) {
                badgeTambahan.className = 'badge bg-warning-subtle text-dark border small';
                badgeTambahan.textContent = 'Belum Dinilai Atasan';
            } else {
                badgeTambahan.className = 'badge bg-light text-muted border small';
                badgeTambahan.textContent = 'Tidak Ada';
            }

            // Sanitasi skema URL bukti cegah Stored XSS
            function sanitizeUrl(url) {
                if (!url || typeof url !== 'string') return '';
                const trimmed = url.trim();
                return /^https?:\/\//i.test(trimmed) ? trimmed : '';
            }

            if (data.tugas_tambahan && data.tugas_tambahan.length > 0) {
                data.tugas_tambahan.forEach((tmb, idx) => {
                    const safeBuktiUrl = sanitizeUrl(tmb.link_bukti);
                    const buktiBtn = safeBuktiUrl ? `<a href="${safeBuktiUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary rounded-pill border px-2.5 py-0.5 btn-tactile" style="font-size: 0.72rem; height: 26px; display: inline-flex; align-items: center;"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>` : `<span class="text-muted">-</span>`;
                    const capaianText = tmb.capaian ? `${formatAngkaIndo(tmb.capaian)} ${tmb.satuan}` : '-';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-muted" style="padding: 10px 8px;">${idx + 1}</td>
                        <td class="text-dark lh-sm" style="padding: 10px 12px; font-size: 0.78rem;">${tmb.deskripsi.replace(/\n/g, '<br>')}</td>
                        <td class="text-center text-muted num-tabular" style="padding: 10px 8px; font-size: 0.78rem;">${tmb.tanggal}</td>
                        <td class="text-center fw-semibold text-dark num-tabular" style="padding: 10px 8px; font-size: 0.78rem;">${capaianText}</td>
                        <td class="text-center" style="padding: 10px 8px;">${buktiBtn}</td>
                    `;
                    tambBody.appendChild(tr);
                });
            } else {
                tambBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4 small">Tidak ada tugas tambahan pada periode ini.</td></tr>`;
            }

            document.getElementById('modalDetailLoader').style.display = 'none';
            document.getElementById('modalDetailContent').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('modalDetailLoader').style.display = 'none';
            document.getElementById('modalDetailErrorMessage').textContent = err.message || 'Terjadi kesalahan sistem.';
            document.getElementById('modalDetailError').style.display = 'block';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initial Metric Count-Up Tickers
    const statTotalEl       = document.getElementById('statTotalPegawai');
    const statSudahEl       = document.getElementById('statSudahDinilai');
    const statBelumEl       = document.getElementById('statBelumDinilai');
    const statRataDinilaiEl = document.getElementById('statRataRataDinilai');
    const statRataTotalEl   = document.getElementById('statRataRataTotal');

    if (statTotalEl) {
        const val = parseInt(statTotalEl.getAttribute('data-val')) || 0;
        animateValue(statTotalEl, 0, val, 700, 0);
    }
    if (statSudahEl) {
        const val = parseInt(statSudahEl.getAttribute('data-val')) || 0;
        animateValue(statSudahEl, 0, val, 750, 0);
    }
    if (statBelumEl) {
        const val = parseInt(statBelumEl.getAttribute('data-val')) || 0;
        animateValue(statBelumEl, 0, val, 750, 0);
    }
    if (statRataDinilaiEl) {
        const val = parseFloat(statRataDinilaiEl.getAttribute('data-val')) || 0;
        animateValue(statRataDinilaiEl, 0, val, 850, 2);
    }
    if (statRataTotalEl) {
        const val = parseFloat(statRataTotalEl.getAttribute('data-val')) || 0;
        animateValue(statRataTotalEl, 0, val, 850, 2);
    }

    const searchInput = document.getElementById('liveSearchInput');
    const tableRows = document.querySelectorAll('.pegawai-row');
    const mobileCards = document.querySelectorAll('.mobile-pegawai-card');
    const pillButtons = document.querySelectorAll('#pillFilterGroup .filter-pill');
    const visibleCounter = document.getElementById('visibleCounter');
    const desktopEmpty = document.getElementById('desktopLiveSearchEmpty');
    const mobileEmpty = document.getElementById('mobileLiveSearchEmpty');
    const desktopQueryTerm = document.getElementById('desktopSearchQueryTerm');

    // Mouse wheel auto-blur on year input
    const filterTahun = document.getElementById('filterTahunInput');
    if (filterTahun) {
        filterTahun.addEventListener('wheel', function(e) {
            this.blur();
        });
    }

    let currentSearchTerm = '';
    let currentFilterCategory = 'all';

    function applyFilterAndSearch() {
        let visibleCount = 0;

        // Filter Desktop Table
        tableRows.forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const nip = row.getAttribute('data-nip') || '';
            const unit = row.getAttribute('data-unit') || '';
            const statusCat = row.getAttribute('data-status') || '';

            const matchesSearch = !currentSearchTerm || (nama.includes(currentSearchTerm) || nip.includes(currentSearchTerm) || unit.includes(currentSearchTerm));
            let matchesCategory = true;

            if (currentFilterCategory === 'sudah') {
                matchesCategory = statusCat.includes('sudah');
            } else if (currentFilterCategory === 'belum') {
                matchesCategory = statusCat.includes('belum');
            } else if (currentFilterCategory === 'tidak_mengerjakan') {
                matchesCategory = statusCat.includes('tidak_mengerjakan');
            } else if (currentFilterCategory === 'perhatian') {
                matchesCategory = statusCat.includes('perhatian');
            } else if (currentFilterCategory === 'baik') {
                matchesCategory = statusCat.includes('baik');
            }

            if (matchesSearch && matchesCategory) {
                if (row.style.display === 'none') {
                    row.classList.add('row-animated');
                }
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Filter Mobile Cards
        mobileCards.forEach(card => {
            const nama = card.getAttribute('data-nama') || '';
            const nip = card.getAttribute('data-nip') || '';
            const unit = card.getAttribute('data-unit') || '';
            const statusCat = card.getAttribute('data-status') || '';

            const matchesSearch = !currentSearchTerm || (nama.includes(currentSearchTerm) || nip.includes(currentSearchTerm) || unit.includes(currentSearchTerm));
            let matchesCategory = true;

            if (currentFilterCategory === 'sudah') {
                matchesCategory = statusCat.includes('sudah');
            } else if (currentFilterCategory === 'belum') {
                matchesCategory = statusCat.includes('belum');
            } else if (currentFilterCategory === 'tidak_mengerjakan') {
                matchesCategory = statusCat.includes('tidak_mengerjakan');
            } else if (currentFilterCategory === 'perhatian') {
                matchesCategory = statusCat.includes('perhatian');
            } else if (currentFilterCategory === 'baik') {
                matchesCategory = statusCat.includes('baik');
            }

            if (matchesSearch && matchesCategory) {
                if (card.style.display === 'none') {
                    card.classList.add('row-animated');
                }
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Handle Empty States
        if (desktopEmpty) {
            if (visibleCount === 0 && tableRows.length > 0) {
                desktopEmpty.style.display = '';
                if (desktopQueryTerm) desktopQueryTerm.textContent = currentSearchTerm || currentFilterCategory;
            } else {
                desktopEmpty.style.display = 'none';
            }
        }

        if (mobileEmpty) {
            if (visibleCount === 0 && mobileCards.length > 0) {
                mobileEmpty.style.display = 'block';
            } else {
                mobileEmpty.style.display = 'none';
            }
        }

        if (visibleCounter) {
            visibleCounter.textContent = 'Menampilkan ' + visibleCount + ' dari ' + tableRows.length + ' pegawai';
        }
    }

    // Live Search with 150ms Debounce
    let searchDebounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchDebounceTimer);
            const val = e.target.value.toLowerCase().trim();
            searchDebounceTimer = setTimeout(() => {
                currentSearchTerm = val;
                applyFilterAndSearch();
            }, 150);
        });
    }

    // Pill Filters
    pillButtons.forEach(pill => {
        pill.addEventListener('click', function() {
            pillButtons.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilterCategory = this.getAttribute('data-filter');
            applyFilterAndSearch();
        });
    });

    // Interactive Column Sorting (Desktop Table & Mobile Cards)
    const sortableHeaders = document.querySelectorAll('.sortable-th');
    const tableBody = document.getElementById('tableBody');
    const mobileCardsContainer = document.getElementById('mobileCardsList');
    let currentSortKey = 'no';
    let currentSortDir = 'asc';

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort');
            
            if (currentSortKey === sortKey) {
                currentSortDir = (currentSortDir === 'asc') ? 'desc' : 'asc';
            } else {
                currentSortKey = sortKey;
                currentSortDir = 'asc';
            }

            // Reset other headers
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc', 'th-sorted');
                h.setAttribute('aria-sort', 'none');
                const icon = h.querySelector('.sort-indicator');
                if (icon) icon.className = 'bi bi-arrow-down-up sort-indicator';
            });

            this.classList.add(currentSortDir === 'asc' ? 'asc' : 'desc', 'th-sorted');
            this.setAttribute('aria-sort', currentSortDir === 'asc' ? 'ascending' : 'descending');
            const icon = this.querySelector('.sort-indicator');
            if (icon) {
                icon.className = currentSortDir === 'asc' ? 'bi bi-arrow-up sort-indicator text-primary fw-bold' : 'bi bi-arrow-down sort-indicator text-primary fw-bold';
            }

            const rowsArray = Array.from(document.querySelectorAll('#tableBody .pegawai-row'));
            const cardsArray = mobileCardsContainer ? Array.from(mobileCardsContainer.querySelectorAll('.mobile-pegawai-card')) : [];

            const comparator = (a, b) => {
                let valA, valB;
                if (sortKey === 'no') {
                    valA = parseFloat(a.querySelector('td:first-child')?.textContent.trim()) || 0;
                    valB = parseFloat(b.querySelector('td:first-child')?.textContent.trim()) || 0;
                } else if (sortKey === 'nama') {
                    valA = (a.getAttribute('data-val-nama') || '').toLowerCase();
                    valB = (b.getAttribute('data-val-nama') || '').toLowerCase();
                    return currentSortDir === 'asc' ? valA.localeCompare(valB, 'id', { numeric: true }) : valB.localeCompare(valA, 'id', { numeric: true });
                } else if (sortKey === 'unit') {
                    valA = (a.getAttribute('data-val-unit') || '').toLowerCase();
                    valB = (b.getAttribute('data-val-unit') || '').toLowerCase();
                    return currentSortDir === 'asc' ? valA.localeCompare(valB, 'id', { numeric: true }) : valB.localeCompare(valA, 'id', { numeric: true });
                } else if (sortKey === 'target') {
                    valA = parseFloat(a.getAttribute('data-val-target')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-target')) || 0;
                } else if (sortKey === 'dinilai') {
                    valA = parseFloat(a.getAttribute('data-val-dinilai')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-dinilai')) || 0;
                } else if (sortKey === 'nilai') {
                    valA = parseFloat(a.getAttribute('data-val-nilai')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-nilai')) || 0;
                }

                return currentSortDir === 'asc' ? valA - valB : valB - valA;
            };

            rowsArray.sort(comparator);
            rowsArray.forEach(row => {
                tableBody.appendChild(row);
            });
            if (desktopEmpty) tableBody.appendChild(desktopEmpty);

            if (mobileCardsContainer && cardsArray.length > 0) {
                cardsArray.sort(comparator);
                cardsArray.forEach(card => {
                    mobileCardsContainer.appendChild(card);
                });
            }
        });

        header.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Detail Pegawai Modal triggers with click & keyboard support
    const detailTriggers = document.querySelectorAll('.btn-detail-pegawai');
    detailTriggers.forEach(el => {
        el.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const nama = this.getAttribute('data-nama');
            loadDetailPegawai(userId, nama);
        });

        el.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const nama = this.getAttribute('data-nama');
                loadDetailPegawai(userId, nama);
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
                        <div class="ecc-loading-title">Sedang mengompilasi data instansi...</div>
                        <span class="ecc-loading-desc">Sistem sedang merekap data target, capaian realisasi, dan tugas tambahan. File akan langsung terunduh begitu proses selesai.</span>
                        <span class="ecc-loading-badge-step"><i class="bi bi-shield-check text-primary"></i> Streaming terenkripsi & aman</span>
                    </div>
                `,
                customClass: {
                    popup: 'ecc-loading-popup'
                },
                showConfirmButton: false,
                allowOutsideClick: false,
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
            triggerExportWithLoading(this, 'Berkas Excel (.xlsx)', 'Rekap_Kinerja_ECC.xlsx');
        });
    }

    const btnExportPdf = document.getElementById('btnExportPdf');
    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', function(e) {
            e.preventDefault();
            triggerExportWithLoading(this, 'Laporan PDF Resmi', 'Laporan_Rekapitulasi_Kinerja_ECC.pdf');
        });
    }
});
</script>
<?= $this->endSection() ?>
