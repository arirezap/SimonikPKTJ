<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Monitoring Target Kinerja Bulanan') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}
.ecc-text-muted-aa {
    color: #475569;
}

/* Entrance & Tactile Motion */
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
    animation: bentoEntrance 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.10s; }
.bento-stagger-3 { animation-delay: 0.16s; }

.btn-tactile {
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, background-color 0.15s ease;
}
.btn-tactile:active {
    transform: scale(0.97) !important;
}

/* Filter Controls (8-Point Grid Scale) */
.filter-select-custom {
    font-size: 0.875rem !important;
    height: 36px !important;
    padding: 4px 32px 4px 12px !important;
    border-radius: 8px !important;
    border-color: #e2e8f0 !important;
    white-space: nowrap;
    text-overflow: ellipsis;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.filter-input-year {
    font-size: 0.875rem !important;
    height: 36px !important;
    padding: 4px 12px !important;
    border-radius: 8px !important;
    border-color: #e2e8f0 !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.filter-select-custom:focus,
.filter-input-year:focus,
.filter-pill:focus-visible,
.btn-tactile:focus-visible {
    outline: none;
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Quick Filter Pills (Clean Wrapping Grid) */
#pillFilterGroup {
    padding: 12px 16px;
    gap: 8px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}
.filter-pill {
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1;
    height: 32px;
    padding: 4px 12px;
    border-radius: 50rem;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    white-space: nowrap;
    user-select: none;
}
.filter-pill:hover {
    background-color: #f1f5f9;
    border-color: #e2e8f0;
    color: #0f172a;
}
.filter-pill:active {
    transform: scale(0.97);
}
.filter-pill.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    border-color: #0d6efd !important;
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.15);
}

/* KPI Stat Boxes */
.kpi-stat-box {
    border-radius: 8px;
    padding: 12px 8px;
    transition: background-color 0.2s ease;
}
.kpi-stat-box:hover {
    background-color: #f8fafc;
}
.kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
}
.kpi-val {
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.25;
    white-space: nowrap;
}
.kpi-sub {
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

/* Proportional Bento Table (Zero Horizontal Overflow on Desktop) */
.desktop-table-view {
    overflow-x: visible;
}
.table-bento {
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
}
.table-bento thead th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 16px;
    vertical-align: middle;
    white-space: normal;
    overflow-wrap: break-word;
}
.table-bento tbody td {
    padding: 12px 16px;
    vertical-align: top;
    border-color: #f1f5f9;
    font-size: 0.875rem;
    overflow-wrap: anywhere;
    word-break: break-word;
}
.col-mt-no {
    width: 64px;
    padding-left: 8px !important;
    padding-right: 8px !important;
    white-space: nowrap !important;
    overflow-wrap: normal !important;
    word-break: normal !important;
}
.col-mt-pegawai {
    width: auto;
}
.col-mt-unit {
    width: 13%;
}
.col-mt-atasan {
    width: 16%;
}
.col-mt-rhk {
    width: 12%;
    padding-left: 8px !important;
    padding-right: 8px !important;
    overflow-wrap: normal !important;
    word-break: normal !important;
}
.col-mt-status {
    width: 15%;
    padding-left: 8px !important;
    padding-right: 8px !important;
    overflow-wrap: normal !important;
    word-break: normal !important;
}
.col-mt-appr {
    width: 16%;
    padding-left: 8px !important;
    padding-right: 8px !important;
    overflow-wrap: normal !important;
    word-break: normal !important;
}

/* Sortable Table Header */
.sortable-th {
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
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
    font-size: 0.75rem;
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease, color 0.15s ease;
    opacity: 0.45;
    flex-shrink: 0;
}
.sortable-th:hover .sort-indicator,
.sortable-th.th-sorted .sort-indicator {
    opacity: 1;
    color: #0d6efd !important;
}

/* Clickable Row & Mobile Card */
.cursor-pointer {
    cursor: pointer;
}
.pegawai-row.cursor-pointer {
    transition: background-color 0.15s ease;
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
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
}

.mobile-pegawai-card.cursor-pointer {
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s ease, border-color 0.18s ease;
    outline: none;
    border-radius: 16px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
}
.mobile-pegawai-card.cursor-pointer:hover,
.mobile-pegawai-card.cursor-pointer:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    border-color: #0d6efd !important;
}
.mobile-pegawai-card.cursor-pointer:hover .mobile-nama-text {
    color: #0d6efd !important;
}
.mobile-pegawai-card.cursor-pointer:active {
    transform: scale(0.98);
}

.avatar-bento-ecc {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    font-size: 0.875rem;
}
.avatar-bento-lg {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    font-size: 0.95rem;
}

.badge-status-pill {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 50rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    text-align: center;
    line-height: 1.25;
}

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

#modalDetailTarget .modal-content {
    border-top: 4px solid #0d6efd !important;
}
.modal-profile-card {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0 !important;
    padding: 16px 24px;
}

/* Mobile vs Desktop View Toggle & Typography */
@media (max-width: 767.98px) {
    .form-control,
    .form-select {
        font-size: var(--bs-body-font-size, 1rem) !important;
    }
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
    .btn-export-action {
        min-height: 44px;
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
<?php
$persenKirimFormatted = str_replace('.', ',', (string) ($persen_kirim ?? 0));
$persenSetujuFormatted = str_replace('.', ',', (string) ($persen_setuju ?? 0));
?>
<div class="container-fluid px-2 px-md-3">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Monitoring Target Kinerja Bulanan</h1>
            <p class="ecc-text-muted-aa small mb-0">Pemantauan status pengisian, pengajuan, dan persetujuan RHK pegawai.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?= site_url('kepegawaian/target-kinerja/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= urlencode($unit_filter) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm btn-success shadow-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 btn-tactile btn-export-action" id="btnExportExcel" title="Unduh Excel">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
            <a href="<?= site_url('kepegawaian/target-kinerja/export-pdf') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= urlencode($unit_filter) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>" class="btn btn-sm btn-danger shadow-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-2 btn-tactile btn-export-action" id="btnExportPdf" title="Unduh PDF">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($is_deadline_active)): ?>
        <div class="alert alert-info border-info-subtle bg-info-subtle text-info-emphasis rounded-4 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm bento-stagger bento-stagger-1">
            <div>
                <strong class="d-block small">Batas Waktu Pengisian Target Bulanan Aktif</strong>
                <span class="small">Pengisian target dibatasi maksimal tanggal <strong><?= esc($batas_target) ?></strong> setiap bulan (Periode <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>).</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- FILTER & KPI SUMMARY ROW -->
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">

        <!-- FILTER CARD -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold text-dark mb-0">Filter Periode, Unit &amp; Kategori</h2>
                    <?php if (!empty($unit_filter) || !empty($role_filter) || !empty($status_filter)): ?>
                        <a href="<?= site_url('kepegawaian/target-kinerja') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>" class="badge bg-light ecc-text-muted-aa border text-decoration-none rounded-pill px-3 py-1 fw-semibold">
                            Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="<?= site_url('kepegawaian/target-kinerja') ?>" id="formFilterTarget">
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-6 col-sm-4">
                                <label for="filterBulan" class="form-label ecc-text-muted-aa small fw-bold text-uppercase mb-1">Periode Bulan</label>
                                <select id="filterBulan" name="bulan" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun</option>
                                    <?php foreach ($bulan_indo as $idx => $bNama): ?>
                                        <option value="<?= $idx + 1 ?>" <?= ($bulan_terpilih == $idx + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= esc($bNama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-6 col-sm-3">
                                <label for="filterTahun" class="form-label ecc-text-muted-aa small fw-bold text-uppercase mb-1">Tahun</label>
                                <input id="filterTahun" type="number" name="tahun" class="form-control form-control-sm text-center fw-bold filter-input-year shadow-sm num-tabular" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="this.form.submit()">
                            </div>

                            <div class="col-12 col-sm-5">
                                <label for="filterRole" class="form-label ecc-text-muted-aa small fw-bold text-uppercase mb-1">Kategori Jabatan</label>
                                <select id="filterRole" name="role" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Jabatan</option>
                                    <option value="pimpinan" <?= ($role_filter === 'pimpinan') ? 'selected' : '' ?>>Pimpinan (Direktur &amp; Wadir)</option>
                                    <option value="struktural" <?= ($role_filter === 'struktural') ? 'selected' : '' ?>>Struktural &amp; Manajemen</option>
                                    <option value="user" <?= ($role_filter === 'user' || $role_filter === 'staf') ? 'selected' : '' ?>>Staf Pelaksana &amp; Fungsional</option>
                                    <option value="tugas_belajar" <?= ($role_filter === 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <label for="filterUnit" class="form-label ecc-text-muted-aa small fw-bold text-uppercase mb-1">Unit Kerja</label>
                                <select id="filterUnit" name="unit" class="form-select form-select-sm filter-select-custom shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Unit Kerja</option>
                                    <?php foreach ($daftar_unit as $uNama): ?>
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

        <!-- KPI SUMMARY CARD -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold text-dark mb-0">Ringkasan Status Target Pegawai</h2>
                    <span class="small fw-bold text-primary-emphasis num-tabular"><?= esc($stat_total_rhk) ?> Total RHK</span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0 align-items-stretch">
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Total</div>
                            <div class="my-1 kpi-val text-dark num-tabular"><?= esc($stat_total_pegawai) ?></div>
                            <div class="kpi-sub ecc-text-muted-aa">Pegawai</div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Terkirim</div>
                            <div class="my-1 kpi-val text-success-emphasis num-tabular"><?= esc($stat_sudah_mengirim) ?></div>
                            <div class="kpi-sub text-success-emphasis num-tabular"><?= esc($persenKirimFormatted) ?>%</div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Disetujui</div>
                            <div class="my-1 kpi-val text-info-emphasis num-tabular"><?= esc($stat_sudah_disetujui) ?></div>
                            <div class="kpi-sub text-info-emphasis num-tabular"><?= esc($persenSetujuFormatted) ?>%</div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Menunggu</div>
                            <div class="my-1 kpi-val text-warning-emphasis num-tabular"><?= esc($stat_menunggu_persetujuan) ?></div>
                            <div class="kpi-sub text-warning-emphasis">Antrean</div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Draf</div>
                            <div class="my-1 kpi-val ecc-text-muted-aa num-tabular"><?= esc($stat_draft) ?></div>
                            <div class="kpi-sub ecc-text-muted-aa">Draf</div>
                        </div>

                        <div class="col px-1 d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="kpi-label">Belum Isi</div>
                            <div class="my-1 kpi-val text-danger-emphasis num-tabular"><?= esc($stat_belum_mengisi) ?></div>
                            <div class="kpi-sub text-danger-emphasis">0 RHK</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MAIN BENTO CARD: DAFTAR STATUS TARGET PEGAWAI -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">

        <div class="card-header bg-light py-3 px-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h2 class="h6 fw-bold text-dark mb-1">Daftar Status Target Pegawai</h2>
                <div class="small ecc-text-muted-aa" id="visibleCounter">Menampilkan <?= count($rekap_target) ?> pegawai · <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?> · Pilih baris untuk rincian</div>
            </div>
            <div class="input-group input-group-sm" style="max-width: 260px;">
                <span class="input-group-text bg-white border-end-0 ecc-text-muted-aa"><i class="bi bi-search"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 ecc-focus-ring" placeholder="Cari nama, NIP, unit..." autocomplete="off" aria-label="Pencarian cepat pegawai">
            </div>
        </div>

        <!-- Quick Status Filter Pills (Wrap Cleanly, Zero Clipping) -->
        <div class="bg-white border-bottom" id="pillFilterGroup">
            <span class="ecc-text-muted-aa small fw-bold me-1">Filter Cepat:</span>
            <button type="button" class="filter-pill <?= empty($status_filter) ? 'active' : '' ?>" data-status="" aria-label="Semua Pegawai">
                Semua (<span class="num-tabular"><?= esc($stat_total_pegawai) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'sudah_mengirim') ? 'active' : '' ?>" data-status="sudah_mengirim" aria-label="Sudah Mengirim">
                Sudah Mengirim (<span class="num-tabular"><?= esc($stat_sudah_mengirim) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'disetujui') ? 'active' : '' ?>" data-status="disetujui" aria-label="Sudah Disetujui">
                Disetujui (<span class="num-tabular"><?= esc($stat_sudah_disetujui) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'menunggu_persetujuan') ? 'active' : '' ?>" data-status="menunggu_persetujuan" aria-label="Menunggu Persetujuan">
                Menunggu (<span class="num-tabular"><?= esc($stat_menunggu_persetujuan) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'draft') ? 'active' : '' ?>" data-status="draft" aria-label="Draf">
                Draf (<span class="num-tabular"><?= esc($stat_draft) ?></span>)
            </button>
            <button type="button" class="filter-pill <?= ($status_filter === 'belum_mengisi') ? 'active' : '' ?>" data-status="belum_mengisi" aria-label="Belum Mengisi">
                Belum Mengisi (<span class="num-tabular"><?= esc($stat_belum_mengisi) ?></span>)
            </button>
        </div>

        <div class="card-body p-0">
            <!-- Desktop Bento Table (Fits Viewport Cleanly) -->
            <div class="desktop-table-view">
                <table class="table table-hover table-bento mb-0 align-middle" id="tableMonitoringTarget">
                    <thead>
                        <tr>
                            <th class="col-mt-no text-center sortable-th th-sorted" data-sort-key="no" data-sort-type="number" tabindex="0" role="button" aria-sort="ascending" title="Urutkan berdasarkan nomor">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>No</span>
                                    <i class="bi bi-arrow-up sort-indicator text-primary fw-bold"></i>
                                </div>
                            </th>
                            <th class="col-mt-pegawai sortable-th" data-sort-key="nama" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan nama">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Pegawai &amp; Jabatan</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="col-mt-unit sortable-th" data-sort-key="unit" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan unit kerja">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Unit Kerja</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="col-mt-atasan sortable-th" data-sort-key="atasan" data-sort-type="string" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan atasan">
                                <div class="d-flex align-items-center justify-content-between gap-1">
                                    <span>Atasan Langsung</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="col-mt-rhk text-center sortable-th" data-sort-key="rhk" data-sort-type="number" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan jumlah Target RHK">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Target (RHK)</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="col-mt-status text-center sortable-th" data-sort-key="status-kirim" data-sort-type="status-kirim" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan status pengiriman">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <span>Status Pengiriman</span>
                                    <i class="bi bi-arrow-down-up sort-indicator"></i>
                                </div>
                            </th>
                            <th class="col-mt-appr text-center sortable-th" data-sort-key="status-appr" data-sort-type="status-appr" tabindex="0" role="button" aria-sort="none" title="Urutkan berdasarkan status persetujuan">
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
                                <td colspan="7" class="text-center py-5 ecc-text-muted-aa">
                                    Tidak ditemukan data pegawai yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rekap_target as $index => $row): ?>
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
                                    title="Lihat rincian target <?= esc($user['nama_lengkap']) ?>"
                                    data-user-id="<?= esc($user['id']) ?>"
                                    data-bulan="<?= esc($bulan_terpilih) ?>"
                                    data-tahun="<?= esc($tahun_terpilih) ?>"
                                    data-no="<?= $index + 1 ?>"
                                    data-nama="<?= esc(strtolower($user['nama_lengkap'])) ?>"
                                    data-nip="<?= esc(strtolower($user['nip'] ?? '')) ?>"
                                    data-unit="<?= esc(strtolower($user['unit'] ?? '')) ?>"
                                    data-jabatan="<?= esc(strtolower($user['jabatan'] ?? '')) ?>"
                                    data-atasan="<?= esc($atasanSortKey) ?>"
                                    data-rhk="<?= (float) $row['total_rhk'] ?>"
                                    data-status-kirim="<?= esc($stKirim) ?>"
                                    data-status-appr="<?= esc($stAppr) ?>">

                                    <td class="col-mt-no text-center fw-bold ecc-text-muted-aa num-tabular"><?= $index + 1 ?></td>

                                    <td>
                                        <div class="d-flex align-items-start gap-3">
                                            <?php
                                                $fotoUser = $user['foto'] ?? '';
                                                $fotoPathTgt = !empty($fotoUser) ? 'assets/uploads/profile/' . $fotoUser : '';
                                                $hasPhotoTgt = (!empty($fotoPathTgt) && file_exists(FCPATH . $fotoPathTgt));
                                                $initialTgt = strtoupper(substr(trim($user['nama_lengkap'] ?? 'P'), 0, 1));
                                            ?>
                                            <?php if ($hasPhotoTgt): ?>
                                                <img src="<?= base_url($fotoPathTgt) ?>" alt="<?= esc($user['nama_lengkap']) ?>" class="object-fit-cover shadow-sm flex-shrink-0 avatar-bento-ecc" width="40" height="40" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="bg-primary-subtle text-primary-emphasis fw-bold align-items-center justify-content-center flex-shrink-0 avatar-bento-ecc" style="display: none;">
                                                    <?= esc($initialTgt) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="bg-primary-subtle text-primary-emphasis fw-bold d-flex align-items-center justify-content-center flex-shrink-0 avatar-bento-ecc">
                                                    <?= esc($initialTgt) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="d-flex align-items-center justify-content-between gap-1">
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <span class="fw-bold text-dark pegawai-nama-text"><?= esc($user['nama_lengkap']) ?></span>
                                                        <?php if (($user['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1" title="Mutasi Keluar <?= !empty($user['tmt_selesai']) ? 'TMT ' . date('d/m/Y', strtotime($user['tmt_selesai'])) : '' ?>">Mutasi</span>
                                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                                            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">Pensiun</span>
                                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-1">Nonaktif</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="row-hover-hint" title="Lihat rincian"><i class="bi bi-chevron-right"></i></span>
                                                </div>
                                                <div class="ecc-text-muted-aa small">
                                                    NIP: <span class="num-tabular"><?= !empty($user['nip']) ? esc($user['nip']) : '-' ?></span>
                                                </div>
                                                <div class="ecc-text-muted-aa small">
                                                    <?= !empty($user['jabatan']) ? esc($user['jabatan']) : '-' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 text-wrap text-start small fw-medium">
                                            <?= !empty($user['unit']) ? esc($user['unit']) : '-' ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($user['role'] === 'direktur'): ?>
                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1">
                                                Otomatis Disetujui
                                            </span>
                                        <?php elseif (!empty($atasan)): ?>
                                            <div class="fw-semibold text-dark small"><?= esc($atasan['nama_lengkap']) ?></div>
                                            <div class="ecc-text-muted-aa small"><?= esc($atasan['jabatan'] ?? '-') ?></div>
                                        <?php else: ?>
                                            <span class="ecc-text-muted-aa small">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="col-mt-rhk text-center">
                                        <span class="badge <?= ($row['total_rhk'] > 0) ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-light ecc-text-muted-aa border' ?> rounded-pill px-3 py-1 fw-bold num-tabular">
                                            <?= esc($row['total_rhk']) ?> RHK
                                        </span>
                                    </td>

                                    <td class="col-mt-status text-center">
                                        <?php if ($stKirim === 'disetujui'): ?>
                                            <span class="badge-status-pill bg-success-subtle text-success-emphasis border border-success-subtle">Disetujui</span>
                                        <?php elseif ($stKirim === 'terkirim'): ?>
                                            <span class="badge-status-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle">Terkirim</span>
                                        <?php elseif ($stKirim === 'sebagian_disetujui'): ?>
                                            <span class="badge-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">Sebagian Disetujui</span>
                                        <?php elseif ($stKirim === 'draft'): ?>
                                            <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Draf</span>
                                        <?php else: ?>
                                            <span class="badge-status-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle">Belum Mengisi</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="col-mt-appr text-center">
                                        <?php if ($stAppr === 'disetujui'): ?>
                                            <span class="badge-status-pill bg-success-subtle text-success-emphasis border border-success-subtle">Disetujui Atasan</span>
                                        <?php elseif ($stAppr === 'menunggu_persetujuan'): ?>
                                            <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Menunggu</span>
                                        <?php elseif ($stAppr === 'sebagian_disetujui'): ?>
                                            <span class="badge-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">Sebagian (<span class="num-tabular"><?= esc($row['count_disetujui']) ?>/<?= esc($row['total_rhk']) ?></span>)</span>
                                        <?php elseif ($stAppr === 'draft'): ?>
                                            <span class="badge-status-pill bg-light ecc-text-muted-aa border">Draf</span>
                                        <?php else: ?>
                                            <span class="badge-status-pill bg-light ecc-text-muted-aa border">Belum Ada Target</span>
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
                    <div class="text-center py-4 ecc-text-muted-aa">
                        Tidak ditemukan data pegawai yang sesuai.
                    </div>
                <?php else: ?>
                    <?php foreach ($rekap_target as $index => $row): ?>
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
                             title="Lihat rincian target <?= esc($user['nama_lengkap']) ?>"
                             data-user-id="<?= esc($user['id']) ?>"
                             data-bulan="<?= esc($bulan_terpilih) ?>"
                             data-tahun="<?= esc($tahun_terpilih) ?>"
                             data-no="<?= $index + 1 ?>"
                             data-nama="<?= esc(strtolower($user['nama_lengkap'])) ?>"
                             data-nip="<?= esc(strtolower($user['nip'] ?? '')) ?>"
                             data-unit="<?= esc(strtolower($user['unit'] ?? '')) ?>"
                             data-jabatan="<?= esc(strtolower($user['jabatan'] ?? '')) ?>"
                             data-atasan="<?= esc($atasanSortKey) ?>"
                             data-rhk="<?= (float) $row['total_rhk'] ?>"
                             data-status-kirim="<?= esc($stKirim) ?>"
                             data-status-appr="<?= esc($stAppr) ?>">

                            <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 small">
                                    <?= !empty($user['unit']) ? esc($user['unit']) : '-' ?>
                                </span>
                                <span class="badge <?= ($row['total_rhk'] > 0) ? 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' : 'bg-light ecc-text-muted-aa border' ?> rounded-pill px-3 py-1 fw-bold num-tabular">
                                    <?= esc($row['total_rhk']) ?> RHK
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <?php
                                    $fotoUserMob = $user['foto'] ?? '';
                                    $fotoPathTgtMob = !empty($fotoUserMob) ? 'assets/uploads/profile/' . $fotoUserMob : '';
                                    $hasPhotoTgtMob = (!empty($fotoPathTgtMob) && file_exists(FCPATH . $fotoPathTgtMob));
                                    $initialTgtMob = strtoupper(substr(trim($user['nama_lengkap'] ?? 'P'), 0, 1));
                                ?>
                                <?php if ($hasPhotoTgtMob): ?>
                                    <img src="<?= base_url($fotoPathTgtMob) ?>" alt="<?= esc($user['nama_lengkap']) ?>" class="object-fit-cover shadow-sm flex-shrink-0 avatar-bento-ecc" width="40" height="40" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="bg-primary-subtle text-primary-emphasis fw-bold align-items-center justify-content-center flex-shrink-0 avatar-bento-ecc" style="display: none;">
                                        <?= esc($initialTgtMob) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-primary-subtle text-primary-emphasis fw-bold d-flex align-items-center justify-content-center flex-shrink-0 avatar-bento-ecc">
                                        <?= esc($initialTgtMob) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold text-dark mobile-nama-text"><?= esc($user['nama_lengkap']) ?></span>
                                        <?php if (($user['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Mutasi</span>
                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">Pensiun</span>
                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2 py-1">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ecc-text-muted-aa small">NIP: <span class="num-tabular"><?= !empty($user['nip']) ? esc($user['nip']) : '-' ?></span></div>
                                    <div class="ecc-text-muted-aa small"><?= !empty($user['jabatan']) ? esc($user['jabatan']) : '-' ?></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2 flex-wrap">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <?php if ($stKirim === 'disetujui'): ?>
                                        <span class="badge-status-pill bg-success-subtle text-success-emphasis border border-success-subtle">Disetujui</span>
                                    <?php elseif ($stKirim === 'terkirim'): ?>
                                        <span class="badge-status-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle">Terkirim</span>
                                    <?php elseif ($stKirim === 'sebagian_disetujui'): ?>
                                        <span class="badge-status-pill bg-info-subtle text-info-emphasis border border-info-subtle">Sebagian Disetujui</span>
                                    <?php elseif ($stKirim === 'draft'): ?>
                                        <span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Draf</span>
                                    <?php else: ?>
                                        <span class="badge-status-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle">Belum Mengisi</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center gap-1 text-primary-emphasis small fw-semibold">
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

<!-- MODAL RINCIAN TARGET PEGAWAI -->
<div class="modal fade" id="modalDetailTarget" tabindex="-1" aria-labelledby="modalDetailTargetLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div>
                    <h2 class="h5 modal-title fw-bold text-dark mb-0" id="modalDetailTargetLabel">Rincian Target Kinerja Bulanan</h2>
                    <span class="ecc-text-muted-aa small d-block mt-1" id="modalTargetPeriode">Periode: -</span>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-3 p-md-4">

                <div id="modalTargetSkeleton">
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4 border">
                        <div class="bg-secondary-subtle avatar-bento-lg"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-bar" style="width: 40%;"></div>
                            <div class="skeleton-bar" style="width: 25%;"></div>
                        </div>
                    </div>
                    <div class="skeleton-bar" style="width: 100%;"></div>
                    <div class="skeleton-bar" style="width: 90%;"></div>
                    <div class="skeleton-bar" style="width: 95%;"></div>
                </div>

                <div id="modalTargetContent" style="display: none;">

                    <div class="card border rounded-4 mb-3 modal-profile-card">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div id="modalUserAvatarContainer"></div>
                                    <div style="min-width: 0;">
                                        <h3 class="h6 fw-bold text-dark mb-1" id="modalUserNama">-</h3>
                                        <div class="ecc-text-muted-aa small">
                                            NIP: <span class="num-tabular fw-semibold" id="modalUserNip">-</span> |
                                            Unit: <span class="fw-semibold text-dark" id="modalUserUnit">-</span>
                                        </div>
                                        <div class="ecc-text-muted-aa small mt-1" id="modalUserJabatan">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 border-start-md">
                                <div class="ps-md-3">
                                    <span class="ecc-text-muted-aa small fw-semibold text-uppercase d-block mb-1">Atasan Langsung:</span>
                                    <div class="fw-bold text-dark small" id="modalAtasanNama">-</div>
                                    <div class="ecc-text-muted-aa small mt-1" id="modalAtasanJabatan">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-4 bg-white overflow-hidden mb-2">
                        <table class="table table-bordered table-hover table-bento mb-0 align-middle">
                            <thead id="modalTargetTableHead">
                                <tr>
                                    <th class="col-mt-no text-center">No</th>
                                    <th class="text-start">Rencana Hasil Kerja (RHK)</th>
                                    <th class="text-start">Indikator Kinerja Individu</th>
                                    <th class="text-center" style="width: 12%;">Target</th>
                                    <th class="text-center" style="width: 11%;">Satuan</th>
                                    <th class="text-center" style="width: 14%;">Status Pengiriman</th>
                                    <th class="text-center" style="width: 15%;">Status Persetujuan</th>
                                </tr>
                            </thead>
                            <tbody id="modalTargetTableBody"></tbody>
                        </table>
                    </div>

                </div>

            </div>

            <div class="modal-footer bg-light border-top d-flex justify-content-end align-items-center px-3 px-md-4 py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold btn-tactile" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const liveSearchInput = document.getElementById('liveSearchInput');
    const tableRows = document.querySelectorAll('.pegawai-row');
    const visibleCounter = document.getElementById('visibleCounter');
    const btnExportExcel = document.getElementById('btnExportExcel');
    const btnExportPdf = document.getElementById('btnExportPdf');
    const namaBulanPeriode = <?= json_encode($nama_bulan . ' ' . $tahun_terpilih) ?>;
    let currentPillStatus = '<?= esc($status_filter) ?>';

    function syncExportUrls() {
        [btnExportExcel, btnExportPdf].forEach(btn => {
            if (!btn) return;
            try {
                const urlObj = new URL(btn.href, window.location.origin);
                if (currentPillStatus) {
                    urlObj.searchParams.set('status', currentPillStatus);
                } else {
                    urlObj.searchParams.set('status', '');
                }
                btn.href = urlObj.toString();
            } catch (err) {}
        });
    }

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

            const matchSearch = (query === '' || nama.includes(query) || nip.includes(query) || unit.includes(query) || jabatan.includes(query));

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
                if (row.tagName === 'TR') visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCounter) {
            visibleCounter.innerText = `Menampilkan ${visibleCount} pegawai · ${namaBulanPeriode} · Pilih baris untuk rincian`;
        }
        syncExportUrls();
    }

    let searchDebounceTimer;
    if (liveSearchInput) {
        liveSearchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(applyFilters, 150);
        });
    }

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

    // Interactive Column Sorting (Desktop & Mobile)
    const sortableHeaders = document.querySelectorAll('.sortable-th');
    const tableTbody = document.querySelector('#tableMonitoringTarget tbody');
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
                result = (statusKirimWeight[valA] ?? -1) - (statusKirimWeight[valB] ?? -1);
            } else if (sortType === 'status-appr') {
                result = (statusApprWeight[valA] ?? -1) - (statusApprWeight[valB] ?? -1);
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

            sortableHeaders.forEach(h => {
                h.classList.remove('sorted-asc', 'sorted-desc', 'th-sorted');
                h.setAttribute('aria-sort', 'none');
                const icon = h.querySelector('.sort-indicator');
                if (icon) icon.className = 'bi bi-arrow-down-up sort-indicator';
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

    // Modal Detail Target
    const modalEl = document.getElementById('modalDetailTarget');
    const bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const skeleton = document.getElementById('modalTargetSkeleton');
    const content = document.getElementById('modalTargetContent');
    const tableBody = document.getElementById('modalTargetTableBody');
    const tableHead = document.getElementById('modalTargetTableHead');

    function formatTargetDecimal(val) {
        if (val === null || val === undefined || val === '') return '0';
        const num = Number(val);
        if (isNaN(num)) return escapeHtml(String(val));
        return num.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
    }

    function openTargetDetail(element) {
        const userId = element.getAttribute('data-user-id');
        const bulan = element.getAttribute('data-bulan');
        const tahun = element.getAttribute('data-tahun');

        if (!bsModal || !userId) return;

        skeleton.style.display = 'block';
        content.style.display = 'none';
        tableBody.innerHTML = '';
        bsModal.show();

        fetch(`<?= site_url('kepegawaian/target-kinerja/detail-pegawai') ?>?user_id=${encodeURIComponent(userId)}&bulan=${encodeURIComponent(bulan)}&tahun=${encodeURIComponent(tahun)}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    skeleton.style.display = 'none';
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger-emphasis">${escapeHtml(data.message || 'Gagal memuat rincian target.')}</td></tr>`;
                    content.style.display = 'block';
                    return;
                }

                const isAllYear = (data.periode.bulan === 'all');

                document.getElementById('modalTargetPeriode').innerText = `Periode: ${data.periode.nama_bulan} ${data.periode.tahun}`;
                document.getElementById('modalUserNama').innerText = data.user.nama_lengkap;
                document.getElementById('modalUserNip').innerText = data.user.nip;
                document.getElementById('modalUserUnit').innerText = data.user.unit;
                document.getElementById('modalUserJabatan').innerText = data.user.jabatan;

                const avatarContainer = document.getElementById('modalUserAvatarContainer');
                const initial = (data.user.nama_lengkap || 'P').charAt(0).toUpperCase();
                if (data.user.foto) {
                    avatarContainer.innerHTML = `
                        <img src="${escapeHtml(data.user.foto)}" alt="Foto" class="object-fit-cover shadow-sm avatar-bento-lg" width="48" height="48" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="bg-primary-subtle text-primary-emphasis fw-bold align-items-center justify-content-center avatar-bento-lg" style="display: none;">${escapeHtml(initial)}</div>
                    `;
                } else {
                    avatarContainer.innerHTML = `<div class="bg-primary-subtle text-primary-emphasis fw-bold d-flex align-items-center justify-content-center avatar-bento-lg">${escapeHtml(initial)}</div>`;
                }

                if (data.user.role === 'direktur') {
                    document.getElementById('modalAtasanNama').innerHTML = '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 fw-bold">Otomatis Disetujui (Direktur)</span>';
                    document.getElementById('modalAtasanJabatan').innerText = 'Pimpinan Tertinggi';
                } else if (data.atasan) {
                    document.getElementById('modalAtasanNama').innerText = data.atasan.nama_lengkap;
                    document.getElementById('modalAtasanJabatan').innerText = data.atasan.jabatan || '-';
                } else {
                    document.getElementById('modalAtasanNama').innerText = '-';
                    document.getElementById('modalAtasanJabatan').innerText = 'Belum Ditentukan';
                }

                if (tableHead) {
                    tableHead.innerHTML = `
                        <tr>
                            <th class="col-mt-no text-center">No</th>
                            ${isAllYear ? '<th class="text-center" style="width: 11%;">Bulan</th>' : ''}
                            <th class="text-start">Rencana Hasil Kerja (RHK)</th>
                            <th class="text-start">Indikator Kinerja Individu</th>
                            <th class="text-center" style="width: 11%;">Target</th>
                            <th class="text-center" style="width: 10%;">Satuan</th>
                            <th class="text-center" style="width: 13%;">Status Pengiriman</th>
                            <th class="text-center" style="width: 14%;">Status Persetujuan</th>
                        </tr>
                    `;
                }

                if (data.targets && data.targets.length > 0) {
                    let rowsHtml = '';
                    data.targets.forEach((t, idx) => {
                        const isAppr = (t.status_approval === 'disetujui');
                        const isKirim = (t.status === 'terkirim');

                        const badgeKirim = isKirim
                            ? '<span class="badge-status-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle">Terkirim</span>'
                            : '<span class="badge-status-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">Draf</span>';

                        const badgeAppr = isAppr
                            ? '<span class="badge-status-pill bg-success-subtle text-success-emphasis border border-success-subtle">Disetujui</span>'
                            : '<span class="badge-status-pill bg-light ecc-text-muted-aa border">Menunggu</span>';

                        const targetValStr = formatTargetDecimal(t.target_bulanan || 0);
                        const monthBadge = isAllYear ? `<td class="text-center"><span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1 fw-bold">${escapeHtml(t.nama_bulan || ('Bulan ' + t.bulan))}</span></td>` : '';

                        rowsHtml += `
                            <tr>
                                <td class="col-mt-no text-center fw-bold ecc-text-muted-aa num-tabular">${idx + 1}</td>
                                ${monthBadge}
                                <td><div class="fw-semibold text-dark">${escapeHtml(t.sasaran_program || '-')}</div></td>
                                <td><div class="ecc-text-muted-aa">${escapeHtml(t.indikator_kinerja || '-')}</div></td>
                                <td class="text-center fw-bold text-primary-emphasis num-tabular">${targetValStr}</td>
                                <td class="text-center ecc-text-muted-aa small">${escapeHtml(t.satuan || '-')}</td>
                                <td class="text-center">${badgeKirim}</td>
                                <td class="text-center">${badgeAppr}</td>
                            </tr>
                        `;
                    });
                    tableBody.innerHTML = rowsHtml;
                } else {
                    const colSpanCount = isAllYear ? 8 : 7;
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="${colSpanCount}" class="text-center py-5 ecc-text-muted-aa">
                                Belum ada data Target Kinerja Bulanan pada periode ini.
                            </td>
                        </tr>
                    `;
                }

                skeleton.style.display = 'none';
                content.style.display = 'block';
            })
            .catch(() => {
                skeleton.style.display = 'none';
                const isAllYear = (document.getElementById('modalTargetTableHead') && document.getElementById('modalTargetTableHead').innerText.includes('Bulan'));
                const colSpanCount = isAllYear ? 8 : 7;
                tableBody.innerHTML = `<tr><td colspan="${colSpanCount}" class="text-center py-4 text-danger-emphasis">Gagal memuat rincian target.</td></tr>`;
                content.style.display = 'block';
            });
    }

    document.querySelectorAll('.btn-detail-target-trigger').forEach(triggerEl => {
        triggerEl.addEventListener('click', function() {
            openTargetDetail(this);
        });
        triggerEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openTargetDetail(this);
            }
        });
    });

    async function triggerExportWithLoading(btnEl, typeText, defaultFilename) {
        const url = btnEl.getAttribute('href');
        if (!url) return;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Menyiapkan ${typeText}...`,
                html: `
                    <div class="d-flex flex-column align-items-center gap-2 my-2">
                        <div class="ecc-loading-spinner-wrapper">
                            <div class="ecc-loading-spinner spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 3px;"></div>
                        </div>
                        <div class="ecc-loading-title">Sedang menyiapkan berkas...</div>
                        <span class="ecc-loading-desc">Unduhan akan dimulai secara otomatis.</span>
                    </div>
                `,
                customClass: { popup: 'ecc-loading-popup' },
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: async () => {
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error('Gagal menyiapkan berkas.');
                        }

                        const contentType = response.headers.get('Content-Type') || '';
                        if (contentType.includes('text/html') || response.redirected) {
                            Swal.close();
                            window.location.href = response.url || url;
                            return;
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
                            title: 'Unduhan Selesai',
                            text: `Berkas ${filename} berhasil diunduh.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } catch (err) {
                        Swal.close();
                        window.location.href = url;
                    }
                }
            });
        } else {
            window.location.href = url;
        }
    }

    if (btnExportExcel) {
        btnExportExcel.addEventListener('click', function(e) {
            e.preventDefault();
            triggerExportWithLoading(this, 'Berkas Excel', 'Rekap_Target_Kinerja_ECC.xlsx');
        });
    }

    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', function(e) {
            e.preventDefault();
            triggerExportWithLoading(this, 'Laporan PDF', 'Laporan_Rekapitulasi_Target_ECC.pdf');
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.innerText = str;
        return div.innerHTML;
    }

});
</script>
<?= $this->endSection() ?>
