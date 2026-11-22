<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap Kinerja Kepegawaian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Evidence Command Center (ECC) — 8-Point Grid & DESIGN.md Tokens */
.ecc-text-muted-aa {
    color: #475569 !important;
}

.ecc-text-subtle {
    color: #64748b !important;
}

.bento-card {
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04) !important;
    background-color: #ffffff;
}

.bento-card-header {
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 16px 24px !important;
}

/* Staggered Entrance (single-shot natural deceleration, zero resting will-change) */
@keyframes bentoEntrance {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bento-stagger {
    animation: bentoEntrance 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.08s; }
.bento-stagger-3 { animation-delay: 0.12s; }

/* Tactile Buttons */
.btn-tactile {
    min-height: 36px;
    padding: 8px 16px;
    border-radius: 50rem;
    font-size: 0.875rem;
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.15s ease, border-color 0.15s ease;
}
.btn-tactile:active {
    transform: scale(0.97) !important;
}
.btn-tactile:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
}

/* Custom Filter Controls (36px height, 8px radius, DESIGN.md typography) */
.filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 4px;
}

.filter-select-custom,
.filter-input-year {
    font-size: 0.875rem !important;
    height: 36px !important;
    border-radius: 8px !important;
    border: 1px solid #cbd5e1 !important;
    color: #0f172a;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.filter-select-custom {
    padding: 4px 32px 4px 12px !important;
    background-position: right 12px center !important;
    background-size: 12px 10px !important;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.filter-input-year {
    padding: 4px 8px !important;
}

.filter-select-custom:focus,
.filter-input-year:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Skeleton Loader */
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
.skeleton-box {
    display: inline-block;
    height: 16px;
    position: relative;
    overflow: hidden;
    background-color: #e2e8f0;
    border-radius: 4px;
}
.skeleton-box::after {
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.45) 50%, rgba(255, 255, 255, 0) 100%);
    animation: shimmer 1.8s infinite linear;
    content: '';
}

/* Quick Filter Pills (32px desktop, 36px mobile touch-friendly, 8pt scale) */
#pillFilterGroup {
    padding: 12px 24px !important;
    gap: 8px !important;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    background-color: #ffffff;
    border-bottom: 1px solid #e2e8f0;
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
    padding: 4px 12px;
    border-radius: 50rem;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    white-space: nowrap;
    flex-shrink: 0;
    user-select: none;
}
.filter-pill:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}
.filter-pill:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
}
.filter-pill:active {
    transform: scale(0.97);
}
.filter-pill.active {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    border-color: #0d6efd !important;
}
.filter-pill.active i {
    color: #ffffff !important;
}

/* Tabular Numerics */
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* KPI Stat Boxes */
.kpi-stat-box {
    border-radius: 8px;
    padding: 8px 4px;
    transition: background-color 0.15s ease;
}
.kpi-stat-box:hover {
    background-color: #f8fafc;
}
.kpi-stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #475569;
}
.kpi-stat-number {
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.3;
    white-space: nowrap;
}
.kpi-stat-sub {
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

/* Bento Table Styling */
.table-bento {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
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
}
.table-bento tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-color: #f1f5f9;
    font-size: 0.875rem;
}
.table-bento tbody tr:hover td {
    background-color: #f8fafc !important;
}
.col-rk-no {
    width: 64px;
    padding-left: 8px !important;
    padding-right: 8px !important;
    white-space: nowrap;
}

/* Sortable Header Button (Preserves <th> ARIA columnheader role) */
.sortable-th {
    user-select: none;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    white-space: nowrap;
}
.btn-sort-th {
    background: transparent;
    border: 0;
    padding: 0;
    margin: 0;
    width: 100%;
    font: inherit;
    color: inherit;
    text-transform: inherit;
    letter-spacing: inherit;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-sort-th:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    border-radius: 4px;
}
.sortable-th:hover {
    background-color: #f1f5f9 !important;
    color: #0d6efd !important;
}
.sortable-th.th-sorted {
    background-color: #eff6ff !important;
    color: #1e40af !important;
    border-bottom: 2px solid #0d6efd !important;
}
.sort-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    transition: opacity 0.15s ease, color 0.15s ease;
    opacity: 0.45;
    flex-shrink: 0;
}
.sortable-th:hover .sort-indicator {
    opacity: 0.85;
    color: #0d6efd;
}
.sortable-th.th-sorted .sort-indicator {
    opacity: 1;
    color: #1e40af !important;
}

.cursor-pointer {
    cursor: pointer;
}
.desktop-pegawai-row {
    transition: background-color 0.15s ease;
}
.btn-row-name {
    background: transparent;
    border: 0;
    padding: 0;
    color: #0f172a;
    font-size: 0.875rem;
    font-weight: 700;
    text-align: left;
    line-height: 1.3;
    transition: color 0.15s ease;
}
.desktop-pegawai-row:hover .btn-row-name,
.btn-row-name:hover {
    color: #0d6efd;
}
.btn-row-name:focus-visible {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    border-radius: 4px;
}

.ecc-pill-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 50rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1.25;
}

/* Mobile Card Styling (8-Point Grid) */
.mobile-pegawai-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, border-color 0.15s ease;
    margin-bottom: 8px;
}
.mobile-pegawai-card:hover,
.mobile-pegawai-card:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    border-color: #0d6efd !important;
    outline: none;
}

/* Modal Reveal */
.modal-backdrop.show {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
#modalDetailPegawai .modal-content {
    animation: modalFadeLift 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
}
@keyframes modalFadeLift {
    from {
        opacity: 0;
        transform: translateY(8px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.btn-bukti-modal {
    height: 32px;
    padding: 4px 12px;
    font-size: 0.75rem;
    border-radius: 50rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Responsive Breakpoints */
@media (max-width: 767.98px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
    .bento-card-header,
    #pillFilterGroup {
        padding: 12px 16px !important;
    }
    .filter-pill {
        height: 36px;
        padding: 4px 12px;
    }
    .btn-bukti-modal {
        height: 36px;
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
    .btn-tactile,
    .filter-pill,
    .kpi-stat-box,
    .desktop-pegawai-row,
    .btn-row-name,
    .sort-indicator,
    .mobile-pegawai-card,
    .skeleton-box::after,
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
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-1 fw-bold text-dark">Monitoring Penilaian Kinerja</h1>
            <p class="ecc-text-muted-aa small mb-0">Pemantauan dan rekapitulasi penilaian capaian kinerja bulanan seluruh pegawai.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?= site_url('kepegawaian/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>&role=<?= esc($role_filter ?? '') ?>"
               class="btn btn-sm btn-success shadow-sm px-3 fw-bold d-inline-flex align-items-center gap-2 btn-tactile"
               id="btnExportExcel"
               title="Unduh Rekap Excel (.xlsx)"
               aria-label="Unduh Rekapitulasi Kinerja Excel">
                <i class="bi bi-file-earmark-excel-fill" aria-hidden="true"></i>
                <span>Export Excel</span>
            </a>
            <a href="<?= site_url('kepegawaian/export-pdf') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>&role=<?= esc($role_filter ?? '') ?>"
               class="btn btn-sm btn-danger shadow-sm px-3 fw-bold d-inline-flex align-items-center gap-2 btn-tactile"
               id="btnExportPdf"
               title="Unduh Laporan PDF (A4 Landscape)"
               aria-label="Unduh Rekapitulasi Kinerja PDF">
                <i class="bi bi-file-earmark-pdf-fill" aria-hidden="true"></i>
                <span>Export PDF</span>
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

    <!-- FILTER & KPI SUMMARY ROW -->
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">
        <!-- FILTER CARD -->
        <div class="col-lg-6 col-xl-6">
            <div class="card bento-card overflow-hidden h-100">
                <div class="card-header bento-card-header d-flex justify-content-between align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Filter Periode, Unit Kerja &amp; Kategori</h2>
                    <?php if (!empty($unit_filter) || !empty($role_filter)): ?>
                        <a href="<?= site_url('kepegawaian') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>"
                           class="badge bg-secondary-subtle text-dark text-decoration-none ecc-pill-badge">
                            Reset Filter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form method="GET" action="<?= site_url('kepegawaian') ?>" id="filterForm">
                        <div class="row g-2 align-items-end mb-2">
                            <div class="col-6 col-sm-4">
                                <label for="filterBulanSelect" class="form-label filter-label">Periode Bulan</label>
                                <select id="filterBulanSelect" name="bulan" class="form-select form-select-sm filter-select-custom" onchange="showSkeletonAndSubmit()">
                                    <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun</option>
                                    <?php foreach ($bulan_indo as $index => $nama): ?>
                                        <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= esc($nama) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-6 col-sm-3">
                                <label for="filterTahunInput" class="form-label filter-label">Tahun</label>
                                <input type="number" name="tahun" id="filterTahunInput" class="form-control form-control-sm text-center fw-bold filter-input-year num-tabular" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="showSkeletonAndSubmit()">
                            </div>

                            <div class="col-12 col-sm-5">
                                <label for="filterRoleSelect" class="form-label filter-label">Kategori Jabatan</label>
                                <select id="filterRoleSelect" name="role" class="form-select form-select-sm filter-select-custom" onchange="showSkeletonAndSubmit()">
                                    <option value="">Semua Jabatan</option>
                                    <option value="pimpinan" <?= (($role_filter ?? '') === 'pimpinan') ? 'selected' : '' ?>>Pimpinan (Direktur &amp; Wadir)</option>
                                    <option value="manajemen" <?= (($role_filter ?? '') === 'manajemen' || ($role_filter ?? '') === 'struktural') ? 'selected' : '' ?>>Struktural &amp; Manajemen</option>
                                    <option value="kepegawaian" <?= (($role_filter ?? '') === 'kepegawaian') ? 'selected' : '' ?>>Kepegawaian</option>
                                    <option value="user" <?= (($role_filter ?? '') === 'user' || ($role_filter ?? '') === 'staf') ? 'selected' : '' ?>>Staf Pelaksana &amp; Fungsional</option>
                                    <option value="tugas_belajar" <?= (($role_filter ?? '') === 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <label for="filterUnitSelect" class="form-label filter-label">Unit Kerja</label>
                                <select id="filterUnitSelect" name="unit" class="form-select form-select-sm filter-select-custom" onchange="showSkeletonAndSubmit()">
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

        <!-- KPI SUMMARY CARD -->
        <div class="col-lg-6 col-xl-6">
            <div class="card bento-card overflow-hidden h-100">
                <div class="card-header bento-card-header d-flex justify-content-between align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Ringkasan Status Penilaian Pegawai</h2>
                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge num-tabular">
                        <?= count($rekap_kinerja) ?> Total Pegawai
                    </span>
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-center">
                    <div class="row text-center g-0 align-items-stretch">
                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Jumlah seluruh pegawai aktif non-admin">
                            <div class="kpi-stat-label">Total</div>
                            <div class="my-1">
                                <div class="text-dark num-tabular skeleton-hide kpi-stat-number" id="statTotalPegawai" data-val="<?= count($rekap_kinerja) ?>"><?= count($rekap_kinerja) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; display: none;"></div>
                            </div>
                            <div class="ecc-text-muted-aa kpi-stat-sub">Pegawai</div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang sudah memiliki nilai capaian kinerja">
                            <div class="kpi-stat-label">Dinilai</div>
                            <div class="my-1">
                                <div class="text-success num-tabular skeleton-hide kpi-stat-number" id="statSudahDinilai" data-val="<?= esc($sudah_dinilai) ?>"><?= esc($sudah_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; display: none;"></div>
                            </div>
                            <div class="text-success num-tabular kpi-stat-sub">
                                <?= count($rekap_kinerja) > 0 ? round(($sudah_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Pegawai yang belum mendapatkan penilaian kinerja">
                            <div class="kpi-stat-label">Belum</div>
                            <div class="my-1">
                                <div class="text-danger num-tabular skeleton-hide kpi-stat-number" id="statBelumDinilai" data-val="<?= esc($belum_dinilai) ?>"><?= esc($belum_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; display: none;"></div>
                            </div>
                            <div class="text-danger num-tabular kpi-stat-sub">
                                <?= count($rekap_kinerja) > 0 ? round(($belum_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>

                        <div class="col px-1 border-end d-flex flex-column justify-content-between kpi-stat-box" title="Rata-rata mutu dari seluruh pegawai yang sudah dinilai">
                            <div class="kpi-stat-label">Rata Dinilai</div>
                            <div class="my-1">
                                <div class="text-primary num-tabular skeleton-hide kpi-stat-number" id="statRataRataDinilai" data-val="<?= round($rata_rata_dinilai ?? $rata_rata_instansi, 2) ?>"><?= str_replace('.', ',', round($rata_rata_dinilai ?? $rata_rata_instansi, 2)) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; display: none;"></div>
                            </div>
                            <div class="text-primary-emphasis kpi-stat-sub">Sudah Dinilai</div>
                        </div>

                        <div class="col px-1 d-flex flex-column justify-content-between kpi-stat-box" title="Rata-rata capaian dari seluruh total pegawai">
                            <div class="kpi-stat-label">Rata Total</div>
                            <div class="my-1">
                                <div class="text-dark num-tabular skeleton-hide kpi-stat-number" id="statRataRataTotal" data-val="<?= round($rata_rata_keseluruhan ?? 0, 2) ?>"><?= str_replace('.', ',', round($rata_rata_keseluruhan ?? 0, 2)) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; display: none;"></div>
                            </div>
                            <div class="ecc-text-muted-aa kpi-stat-sub">Semua Pegawai</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAP KINERJA (MAIN BENTO CARD) -->
    <div class="card bento-card overflow-hidden mb-4 bento-stagger bento-stagger-3">
        <!-- Card Header -->
        <div class="card-header bento-card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Daftar Rekapitulasi Penilaian Kinerja</h2>
                <div class="ecc-text-muted-aa" id="visibleCounter" style="font-size: 0.75rem;">
                    Menampilkan <?= count($rekap_kinerja) ?> pegawai · Pilih baris untuk rincian
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="max-width: 240px;">
                    <span class="input-group-text bg-white border-end-0 ecc-text-subtle"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0" style="height: 36px; font-size: 0.875rem;" placeholder="Cari nama, NIP, unit..." autocomplete="off" aria-label="Cari pegawai, NIP, jabatan, atau unit kerja">
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge">
                    <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                </span>
            </div>
        </div>

        <!-- Quick Status Filter Pills -->
        <div class="d-flex align-items-center flex-nowrap" id="pillFilterGroup">
            <span class="ecc-text-muted-aa fw-bold me-1 flex-shrink-0" style="font-size: 0.75rem;">Filter Cepat:</span>
            <button type="button" class="filter-pill active" data-filter="all">Semua</button>
            <button type="button" class="filter-pill" data-filter="sudah"><i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i> Sudah Dinilai</button>
            <button type="button" class="filter-pill" data-filter="belum"><i class="bi bi-hourglass-split text-warning" aria-hidden="true"></i> Belum Dinilai</button>
            <button type="button" class="filter-pill" data-filter="tidak_mengerjakan"><i class="bi bi-x-circle-fill text-danger" aria-hidden="true"></i> Tidak Mengerjakan</button>
            <button type="button" class="filter-pill" data-filter="perhatian"><i class="bi bi-exclamation-triangle-fill text-warning" aria-hidden="true"></i> &lt; 75 (Perhatian)</button>
            <button type="button" class="filter-pill" data-filter="baik"><i class="bi bi-stars text-primary" aria-hidden="true"></i> &ge; 90 (Baik)</button>
        </div>

        <div class="card-body p-0">
            <!-- Loading State Skeleton -->
            <div id="tableLoader" style="display: none;">
                <div class="p-4">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <div class="d-flex mb-3 align-items-center">
                        <div class="skeleton-box me-3 flex-shrink-0" style="width: 40px; height: 40px; border-radius: 50%;"></div>
                        <div class="me-auto" style="width: 200px;">
                            <div class="skeleton-box mb-1" style="width: 100%; height: 16px;"></div>
                            <div class="skeleton-box" style="width: 60%; height: 12px;"></div>
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
                            <th class="col-rk-no text-center sortable-th th-sorted" data-sort="no" aria-sort="ascending">
                                <button type="button" class="btn-sort-th justify-content-center" aria-label="Urutkan berdasarkan nomor">
                                    <span>No</span>
                                    <i class="bi bi-arrow-up sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th style="min-width: 270px;" class="sortable-th" data-sort="nama" aria-sort="none">
                                <button type="button" class="btn-sort-th justify-content-between" aria-label="Urutkan berdasarkan nama pegawai">
                                    <span>Pegawai &amp; Jabatan</span>
                                    <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th style="min-width: 160px;" class="sortable-th" data-sort="unit" aria-sort="none">
                                <button type="button" class="btn-sort-th justify-content-between" aria-label="Urutkan berdasarkan unit kerja">
                                    <span>Unit Kerja</span>
                                    <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <?php if ($bulan_terpilih === 'all'): ?>
                                <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m): ?>
                                    <th class="text-center" style="min-width: 48px;"><?= $m ?></th>
                                <?php endforeach; ?>
                                <th class="text-center border-start bg-light sortable-th" data-sort="target" aria-sort="none" style="min-width: 136px;">
                                    <button type="button" class="btn-sort-th justify-content-center" aria-label="Urutkan berdasarkan target tahunan">
                                        <span>Target &amp; Tambahan</span>
                                        <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                    </button>
                                </th>
                            <?php else: ?>
                                <th class="text-center sortable-th" data-sort="target" aria-sort="none" style="min-width: 144px;">
                                    <button type="button" class="btn-sort-th justify-content-center" aria-label="Urutkan berdasarkan komponen kinerja">
                                        <span>Komponen Kinerja</span>
                                        <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                    </button>
                                </th>
                            <?php endif; ?>
                            <th class="text-center sortable-th" data-sort="dinilai" aria-sort="none" style="width: 120px;">
                                <button type="button" class="btn-sort-th justify-content-center" aria-label="Urutkan berdasarkan jumlah komponen dinilai">
                                    <span>Dinilai</span>
                                    <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="text-center pe-3 sortable-th" data-sort="nilai" aria-sort="none" style="width: 144px;">
                                <button type="button" class="btn-sort-th justify-content-center" aria-label="Urutkan berdasarkan nilai akhir">
                                    <span>Nilai Akhir</span>
                                    <i class="bi bi-arrow-down-up sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($rekap_kinerja)): ?>
                            <tr class="no-data-row">
                                <td colspan="<?= ($bulan_terpilih === 'all') ? '18' : '6' ?>" class="text-center py-5">
                                    <div class="ecc-text-muted-aa small fw-semibold">Belum ada data pegawai untuk filter ini.</div>
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

                                    $predikatInfo = hitung_predikat_kinerja($dinilai > 0 ? $rata : null, $dinilai > 0);
                                    $statusText = $predikatInfo['label'];
                                    if ($dinilai == 0) {
                                        $badgeClass = 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle';
                                    } else {
                                        $badgeClass = (strpos($predikatInfo['badge_class'], 'text-dark') !== false)
                                            ? $predikatInfo['badge_class']
                                            : $predikatInfo['badge_class'] . ' text-white';
                                    }

                                    $fotoPegawai = $item['pegawai']['foto'] ?? '';
                                    $fotoPath = !empty($fotoPegawai) ? 'assets/uploads/profile/' . $fotoPegawai : '';
                                    $hasPhoto = (!empty($fotoPath) && file_exists(FCPATH . $fotoPath));
                                    $initial = strtoupper(substr(trim($item['pegawai']['nama_lengkap'] ?? 'P'), 0, 1));
                                ?>
                                <tr class="desktop-pegawai-row cursor-pointer btn-detail-pegawai"
                                    title="Klik untuk melihat rincian capaian kinerja <?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-user-id="<?= $item['pegawai']['id'] ?>"
                                    data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                    data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                    data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                    data-jabatan="<?= esc(strtolower($item['pegawai']['jabatan'] ?? '')) ?>"
                                    data-status="<?= $statusCat ?>"
                                    data-val-no="<?= $index + 1 ?>"
                                    data-val-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-val-unit="<?= esc($item['pegawai']['unit'] ?? '') ?>"
                                    data-val-target="<?= $jumlahTarget ?>"
                                    data-val-pokok="<?= $jumlahPokok ?>"
                                    data-val-tambahan="<?= $jumlahTambahan ?>"
                                    data-val-dinilai="<?= $dinilai ?>"
                                    data-val-nilai="<?= $rata ?>">

                                    <td class="col-rk-no text-center fw-bold ecc-text-muted-aa num-tabular"><?= $index + 1 ?></td>

                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($hasPhoto): ?>
                                                <img src="<?= base_url($fotoPath) ?>" alt="<?= esc($item['pegawai']['nama_lengkap']) ?>" class="rounded-circle object-fit-cover shadow-sm flex-shrink-0" width="40" height="40" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="rounded-circle bg-primary-subtle text-primary-emphasis fw-bold align-items-center justify-content-center flex-shrink-0" style="display: none; width: 40px; height: 40px; font-size: 0.95rem;" aria-hidden="true">
                                                    <?= $initial ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary-subtle text-primary-emphasis fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;" aria-hidden="true">
                                                    <?= $initial ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="lh-sm" style="min-width: 0;">
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <button type="button" class="btn-row-name text-truncate" style="max-width: 100%;" title="<?= esc($item['pegawai']['nama_lengkap']) ?>">
                                                        <?= esc($item['pegawai']['nama_lengkap']) ?>
                                                    </button>
                                                    <?php if (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-pill-badge" title="Mutasi Keluar <?= !empty($item['pegawai']['tmt_selesai']) ? 'TMT ' . date('d/m/Y', strtotime($item['pegawai']['tmt_selesai'])) : '' ?>">Mutasi</span>
                                                    <?php elseif (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ecc-pill-badge">Pensiun</span>
                                                    <?php elseif (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle ecc-pill-badge">Nonaktif</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                                    NIP: <span class="num-tabular"><?= !empty($item['pegawai']['nip']) ? esc($item['pegawai']['nip']) : '-' ?></span>
                                                </div>
                                                <div class="ecc-text-subtle fw-medium mt-1" style="font-size: 0.75rem;">
                                                    <?= !empty($item['pegawai']['jabatan']) ? esc($item['pegawai']['jabatan']) : '-' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 text-wrap text-start" style="font-size: 0.75rem;">
                                            <?= !empty($item['pegawai']['unit']) ? esc($item['pegawai']['unit']) : '-' ?>
                                        </span>
                                    </td>

                                    <?php if ($bulan_terpilih === 'all'): ?>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <?php $val = $item['rata_rata_per_bulan'][$i]; ?>
                                            <td class="text-center num-tabular" style="font-size: 0.75rem;">
                                                <?php if ($val !== null): ?>
                                                    <span class="fw-bold <?= $val <= 75 ? 'text-danger' : 'text-success' ?>"><?= str_replace('.', ',', (float)$val) ?></span>
                                                <?php else: ?>
                                                    <span class="ecc-text-subtle">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                        <td class="text-center border-start bg-light num-tabular">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge" title="Total Target Tugas Pokok Setahun">
                                                    <?= $jumlahPokok ?> Pokok
                                                </span>
                                                <span class="badge <?= $jumlahTambahan > 0 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-light ecc-text-muted-aa border' ?> ecc-pill-badge" title="Total Kegiatan Tugas Tambahan Setahun">
                                                    <?= $jumlahTambahan ?> Tambahan
                                                </span>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-center num-tabular">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge" title="Target RHK Tugas Pokok">
                                                    <?= $jumlahPokok ?> Pokok
                                                </span>
                                                <span class="badge <?= $jumlahTambahan > 0 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-light ecc-text-muted-aa border' ?> ecc-pill-badge" title="Jumlah Kegiatan Tugas Tambahan">
                                                    <?= $jumlahTambahan ?> Tambahan
                                                </span>
                                            </div>
                                        </td>
                                    <?php endif; ?>

                                    <td class="text-center num-tabular">
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                            <span class="fw-bold <?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary-emphasis' ?>"><?= $dinilai ?></span>
                                            <span class="ecc-text-subtle mx-1">/</span>
                                            <span class="ecc-text-muted-aa"><?= $jumlahTarget ?></span>
                                        </span>
                                    </td>

                                    <td class="text-center pe-3 num-tabular">
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                            <span class="fw-bold <?= ($rata > 75) ? 'text-success' : (($dinilai == 0) ? 'ecc-text-muted-aa' : 'text-danger') ?>" style="font-size: 0.95rem;">
                                                <?= str_replace('.', ',', round($rata, 2)) ?>
                                            </span>
                                            <span class="badge <?= $badgeClass ?> ecc-pill-badge">
                                                <?= esc($statusText) ?>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- DYNAMIC EMPTY STATE FOR LIVE SEARCH (DESKTOP) -->
                        <tr id="desktopLiveSearchEmpty" style="display: none;">
                            <td colspan="<?= ($bulan_terpilih === 'all') ? '18' : '6' ?>" class="text-center py-5">
                                <div class="ecc-text-muted-aa small fw-semibold">
                                    Tidak ada pegawai yang sesuai dengan "<strong id="desktopSearchQueryTerm" class="text-dark"></strong>"
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- MOBILE CARDS VIEW (<768px) -->
            <div class="mobile-cards-view p-3" id="mobileCardsContainer">
                <?php if (empty($rekap_kinerja)): ?>
                    <div class="text-center py-5 ecc-text-muted-aa small fw-semibold">
                        Belum ada data pegawai untuk filter ini.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2" id="mobileCardsList">
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

                                $predikatInfo = hitung_predikat_kinerja($dinilai > 0 ? $rata : null, $dinilai > 0);
                                $statusText = $predikatInfo['label'];
                                if ($dinilai == 0) {
                                    $badgeClass = 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle';
                                } else {
                                    $badgeClass = (strpos($predikatInfo['badge_class'], 'text-dark') !== false)
                                        ? $predikatInfo['badge_class']
                                        : $predikatInfo['badge_class'] . ' text-white';
                                }

                                $fotoPegawaiMob = $item['pegawai']['foto'] ?? '';
                                $fotoPathMob = !empty($fotoPegawaiMob) ? 'assets/uploads/profile/' . $fotoPegawaiMob : '';
                                $hasPhotoMob = (!empty($fotoPathMob) && file_exists(FCPATH . $fotoPathMob));
                                $initialMob = strtoupper(substr(trim($item['pegawai']['nama_lengkap'] ?? 'P'), 0, 1));
                            ?>
                            <div class="mobile-pegawai-card cursor-pointer btn-detail-pegawai"
                                 role="button"
                                 tabindex="0"
                                 aria-label="Lihat rincian capaian kinerja <?= esc($item['pegawai']['nama_lengkap']) ?>"
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

                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-light text-dark border ecc-pill-badge">
                                        <?= !empty($item['pegawai']['unit']) ? esc($item['pegawai']['unit']) : '-' ?>
                                    </span>
                                    <span class="badge <?= $badgeClass ?> ecc-pill-badge num-tabular">
                                        <?= str_replace('.', ',', round($rata, 2)) ?>% · <?= esc($statusText) ?>
                                    </span>
                                </div>

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <?php if ($hasPhotoMob): ?>
                                        <img src="<?= base_url($fotoPathMob) ?>" alt="<?= esc($item['pegawai']['nama_lengkap']) ?>" class="rounded-circle object-fit-cover shadow-sm flex-shrink-0" width="40" height="40" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="rounded-circle bg-primary-subtle text-primary-emphasis fw-bold align-items-center justify-content-center flex-shrink-0" style="display: none; width: 40px; height: 40px; font-size: 0.95rem;" aria-hidden="true">
                                            <?= $initialMob ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="rounded-circle bg-primary-subtle text-primary-emphasis fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;" aria-hidden="true">
                                            <?= $initialMob ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="lh-sm flex-grow-1" style="min-width: 0;">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;"><?= esc($item['pegawai']['nama_lengkap']) ?></div>
                                            <?php if (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-pill-badge">Mutasi</span>
                                            <?php elseif (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ecc-pill-badge">Pensiun</span>
                                            <?php elseif (($item['pegawai']['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle ecc-pill-badge">Nonaktif</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">NIP: <span class="num-tabular"><?= !empty($item['pegawai']['nip']) ? esc($item['pegawai']['nip']) : '-' ?></span></div>
                                        <div class="ecc-text-subtle fw-medium mt-1" style="font-size: 0.75rem;"><?= !empty($item['pegawai']['jabatan']) ? esc($item['pegawai']['jabatan']) : '-' ?></div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2 flex-wrap">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge">
                                            <?= $jumlahPokok ?> Pokok
                                        </span>
                                        <span class="badge <?= $jumlahTambahan > 0 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-light ecc-text-muted-aa border' ?> ecc-pill-badge">
                                            <?= $jumlahTambahan ?> Tambahan
                                        </span>
                                        <span class="badge bg-light text-dark border ecc-pill-badge num-tabular">
                                            Dinilai: <strong class="ms-1 <?= ($dinilai == $jumlahTarget && $jumlahTarget > 0) ? 'text-success' : 'text-primary-emphasis' ?>"><?= $dinilai ?>/<?= $jumlahTarget ?></strong>
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 text-primary-emphasis fw-semibold" style="font-size: 0.75rem;">
                                        <span>Rincian</span>
                                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- DYNAMIC EMPTY STATE FOR LIVE SEARCH (MOBILE) -->
                    <div id="mobileLiveSearchEmpty" style="display: none;" class="text-center py-5">
                        <div class="ecc-text-muted-aa small fw-semibold">Tidak ada pegawai yang sesuai dengan pencarian.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL KINERJA PEGAWAI -->
<div class="modal fade" id="modalDetailPegawai" tabindex="-1" aria-labelledby="detailNamaPegawai" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom" style="padding: 16px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-person-badge fs-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="detailNamaPegawai" style="font-size: 0.95rem;">Rincian Kinerja Pegawai</h5>
                        <div class="ecc-text-muted-aa" id="detailSubPegawai" style="font-size: 0.75rem;">Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div id="modalDetailLoader" class="text-center py-5">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <div class="ecc-text-muted-aa small">Mengambil rincian target dan evaluasi...</div>
                </div>

                <div id="modalDetailContent" style="display: none;">
                    <div class="card border rounded-4 shadow-none" style="background-color: #f8fafc; border-color: #e2e8f0 !important; padding: 16px; margin-bottom: 24px;">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="flex-grow-1">
                                <div class="ecc-text-muted-aa fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em; margin-bottom: 4px;">Profil Pegawai</div>
                                <div class="fw-bold text-dark" id="modalInfoNama" style="font-size: 1.15rem; line-height: 1.3; margin-bottom: 4px;">-</div>
                                <div class="ecc-text-muted-aa" id="modalInfoNipUnit" style="font-size: 0.75rem; line-height: 1.4; margin-bottom: 8px;">-</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.75rem;">
                                    <div class="ecc-text-muted-aa" id="modalInfoAtasan">
                                        Atasan Langsung: <strong id="modalTextAtasan" class="text-dark">-</strong>
                                    </div>
                                    <span class="ecc-text-subtle">|</span>
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-pill-badge" id="modalStatPokok">0 Tugas Pokok</span>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle ecc-pill-badge" id="modalStatTambahan">0 Tugas Tambahan</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center bg-white rounded-3 border flex-shrink-0" style="padding: 12px 16px; gap: 16px;">
                                <div class="text-end">
                                    <div class="ecc-text-muted-aa fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em; margin-bottom: 4px;">Nilai Akhir</div>
                                    <span class="badge ecc-pill-badge" id="modalDetailBadge">-</span>
                                </div>
                                <div class="fw-bold text-primary num-tabular lh-1" id="modalDetailScore" style="font-size: 1.75rem; min-width: 64px; text-align: right;">0,00</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.875rem;">A. Rincian Target Kinerja (Tugas Pokok)</h6>
                        <span class="badge bg-light text-dark border ecc-pill-badge" id="modalBadgeCountRhk">0 Target Pokok</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-3 overflow-hidden mb-4">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th class="col-rk-no text-center">No</th>
                                    <th class="text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 120px;" class="text-center">Target</th>
                                    <th style="width: 120px;" class="text-center">Realisasi</th>
                                    <th style="width: 128px;" class="text-center">Selisih</th>
                                    <th style="width: 112px;" class="text-center">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableRhkBody"></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.875rem;">B. Tugas Tambahan</h6>
                        <span class="badge bg-light text-dark border ecc-pill-badge" id="modalBadgeScoreTambahan">Nilai: -</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-3 overflow-hidden mb-2">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th class="col-rk-no text-center">No</th>
                                    <th class="text-start">Deskripsi Kegiatan</th>
                                    <th style="width: 112px;" class="text-center">Tanggal</th>
                                    <th style="width: 120px;" class="text-center">Capaian</th>
                                    <th style="width: 96px;" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableTambahanBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="modalDetailError" style="display: none;" class="text-center py-4">
                    <h6 class="fw-bold text-dark mb-1">Gagal Memuat Data</h6>
                    <p class="ecc-text-muted-aa small mb-3" id="modalDetailErrorMessage">Gagal mengambil rincian data pegawai. Silakan coba lagi.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-tactile px-3" onclick="retryFetchDetail()">Coba Lagi</button>
                </div>
            </div>
            <div class="modal-footer bg-light border-top" style="padding: 12px 24px;">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" style="height: 36px; font-size: 0.875rem;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function sanitizeUrl(url) {
    if (!url || typeof url !== 'string') return '';
    const trimmed = url.trim();
    return /^https?:\/\//i.test(trimmed) ? trimmed : '';
}

function formatAngkaIndo(val) {
    if (val === null || val === undefined || isNaN(val)) return '0';
    const num = Math.round(Number(val) * 10000) / 10000;
    return String(num).replace('.', ',');
}

function animateValue(el, start, end, duration = 600, decimals = 0, suffix = '') {
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

    document.getElementById('detailNamaPegawai').textContent = nama || 'Rincian Kinerja Pegawai';
    document.getElementById('modalDetailLoader').style.display = 'block';
    document.getElementById('modalDetailContent').style.display = 'none';
    document.getElementById('modalDetailError').style.display = 'none';

    modalInstance.show();

    const bulanTerpilih = '<?= esc($bulan_terpilih) ?>';
    const tahunTerpilih = '<?= esc($tahun_terpilih) ?>';

    fetch(`<?= site_url('kepegawaian/detail-pegawai') ?>?user_id=${encodeURIComponent(userId)}&bulan=${encodeURIComponent(bulanTerpilih)}&tahun=${encodeURIComponent(tahunTerpilih)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Gagal memuat rincian.');
            }

            const namaResmi = data.pegawai.nama || nama || '-';
            document.getElementById('detailNamaPegawai').textContent = namaResmi;
            document.getElementById('modalInfoNama').textContent = namaResmi;

            const nipText = data.pegawai.nip ? `NIP: ${data.pegawai.nip}` : 'NIP: -';
            const unitText = data.pegawai.unit ? `Unit: ${data.pegawai.unit}` : 'Unit: -';
            const jabText = data.pegawai.jabatan ? `Jabatan: ${data.pegawai.jabatan}` : 'Jabatan: -';
            document.getElementById('modalInfoNipUnit').textContent = `${nipText} | ${unitText} | ${jabText}`;
            document.getElementById('modalTextAtasan').textContent = data.pegawai.atasan_nama || '-';

            const statPokokEl = document.getElementById('modalStatPokok');
            if (statPokokEl) statPokokEl.textContent = `${data.total_pokok || data.total_rhk || 0} Tugas Pokok`;
            const statTambahanEl = document.getElementById('modalStatTambahan');
            if (statTambahanEl) statTambahanEl.textContent = `${data.total_tambahan || (data.tugas_tambahan ? data.tugas_tambahan.length : 0)} Tugas Tambahan`;

            const scoreVal = parseFloat(data.rata_rata) || 0;
            const scoreEl = document.getElementById('modalDetailScore');
            animateValue(scoreEl, 0, scoreVal, 550, 2);

            const badgeEl = document.getElementById('modalDetailBadge');
            badgeEl.className = `badge ${escapeHtml(data.badge_class)} ecc-pill-badge`;
            badgeEl.textContent = data.predikat || '-';

            // Tabel A: RHK (Tugas Pokok)
            document.getElementById('modalBadgeCountRhk').textContent = `${data.total_pokok || data.total_rhk || 0} Target Pokok`;
            const rhkBody = document.getElementById('modalTableRhkBody');
            rhkBody.innerHTML = '';
            if (data.rekap_rhk && data.rekap_rhk.length > 0) {
                data.rekap_rhk.forEach((rhk, i) => {
                    const selisih = Math.round((Number(rhk.selisih) || 0) * 10000) / 10000;
                    const safeSatuan = escapeHtml(rhk.satuan || '');
                    const safeIndikator = escapeHtml(rhk.indikator || '-');
                    let selisihBadge = '';

                    if (selisih > 0) {
                        selisihBadge = `<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle ecc-pill-badge num-tabular">+${formatAngkaIndo(selisih)} ${safeSatuan}</span>`;
                    } else if (selisih < 0) {
                        selisihBadge = `<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle ecc-pill-badge num-tabular">${formatAngkaIndo(selisih)} ${safeSatuan}</span>`;
                    } else {
                        selisihBadge = `<span class="badge bg-light text-dark border ecc-pill-badge num-tabular">0 ${safeSatuan}</span>`;
                    }

                    const nilaiText = rhk.nilai_capaian !== null
                        ? `<span class="fw-bold text-primary num-tabular">${formatAngkaIndo(rhk.nilai_capaian)}%</span>`
                        : `<span class="ecc-text-subtle">-</span>`;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="col-rk-no text-center fw-bold ecc-text-muted-aa num-tabular">${i + 1}</td>
                        <td class="fw-semibold text-dark lh-sm">${safeIndikator}</td>
                        <td class="text-center fw-bold text-dark num-tabular">${formatAngkaIndo(rhk.target)} ${safeSatuan}</td>
                        <td class="text-center fw-bold text-primary num-tabular">${formatAngkaIndo(rhk.realisasi)} ${safeSatuan}</td>
                        <td class="text-center num-tabular">${selisihBadge}</td>
                        <td class="text-center align-middle">${nilaiText}</td>
                    `;
                    rhkBody.appendChild(tr);
                });
            } else {
                rhkBody.innerHTML = `<tr><td colspan="6" class="text-center ecc-text-muted-aa py-4 small">Tidak ada data target RHK pada periode ini.</td></tr>`;
            }

            // Tabel B: Tugas Tambahan
            const tambBody = document.getElementById('modalTableTambahanBody');
            tambBody.innerHTML = '';
            const badgeTambahan = document.getElementById('modalBadgeScoreTambahan');
            if (data.score_tambahan !== null) {
                badgeTambahan.className = 'badge bg-success-subtle text-success-emphasis border border-success-subtle ecc-pill-badge';
                badgeTambahan.innerHTML = `Nilai: <strong class="ms-1">${escapeHtml(formatAngkaIndo(data.score_tambahan))}%</strong>`;
            } else if (data.tugas_tambahan && data.tugas_tambahan.length > 0) {
                badgeTambahan.className = 'badge bg-warning-subtle text-dark border ecc-pill-badge';
                badgeTambahan.textContent = 'Belum Dinilai Atasan';
            } else {
                badgeTambahan.className = 'badge bg-light ecc-text-muted-aa border ecc-pill-badge';
                badgeTambahan.textContent = 'Tidak Ada';
            }

            if (data.tugas_tambahan && data.tugas_tambahan.length > 0) {
                data.tugas_tambahan.forEach((tmb, idx) => {
                    const safeBuktiUrl = sanitizeUrl(tmb.link_bukti);
                    const buktiBtn = safeBuktiUrl
                        ? `<a href="${escapeHtml(safeBuktiUrl)}" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary border btn-bukti-modal">Bukti <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>`
                        : `<span class="ecc-text-subtle">-</span>`;
                    const capaianText = tmb.capaian ? `${escapeHtml(formatAngkaIndo(tmb.capaian))} ${escapeHtml(tmb.satuan || '')}` : '-';
                    const safeDeskripsi = escapeHtml(tmb.deskripsi || '-').replace(/\r?\n/g, '<br>');
                    const safeTanggal = escapeHtml(tmb.tanggal || '-');

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="col-rk-no text-center fw-bold ecc-text-muted-aa num-tabular">${idx + 1}</td>
                        <td class="text-dark lh-sm">${safeDeskripsi}</td>
                        <td class="text-center ecc-text-muted-aa num-tabular">${safeTanggal}</td>
                        <td class="text-center fw-semibold text-dark num-tabular">${capaianText}</td>
                        <td class="text-center">${buktiBtn}</td>
                    `;
                    tambBody.appendChild(tr);
                });
            } else {
                tambBody.innerHTML = `<tr><td colspan="5" class="text-center ecc-text-muted-aa py-4 small">Tidak ada tugas tambahan pada periode ini.</td></tr>`;
            }

            document.getElementById('modalDetailLoader').style.display = 'none';
            document.getElementById('modalDetailContent').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('modalDetailLoader').style.display = 'none';
            document.getElementById('modalDetailErrorMessage').textContent = err.message || 'Gagal mengambil rincian data pegawai. Silakan coba lagi.';
            document.getElementById('modalDetailError').style.display = 'block';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const statTotalEl       = document.getElementById('statTotalPegawai');
    const statSudahEl       = document.getElementById('statSudahDinilai');
    const statBelumEl       = document.getElementById('statBelumDinilai');
    const statRataDinilaiEl = document.getElementById('statRataRataDinilai');
    const statRataTotalEl   = document.getElementById('statRataRataTotal');

    if (statTotalEl) animateValue(statTotalEl, 0, parseInt(statTotalEl.getAttribute('data-val')) || 0, 550, 0);
    if (statSudahEl) animateValue(statSudahEl, 0, parseInt(statSudahEl.getAttribute('data-val')) || 0, 600, 0);
    if (statBelumEl) animateValue(statBelumEl, 0, parseInt(statBelumEl.getAttribute('data-val')) || 0, 600, 0);
    if (statRataDinilaiEl) animateValue(statRataDinilaiEl, 0, parseFloat(statRataDinilaiEl.getAttribute('data-val')) || 0, 650, 2);
    if (statRataTotalEl) animateValue(statRataTotalEl, 0, parseFloat(statRataTotalEl.getAttribute('data-val')) || 0, 650, 2);

    const searchInput = document.getElementById('liveSearchInput');
    const desktopRows = document.querySelectorAll('#tableBody .desktop-pegawai-row');
    const mobileCards = document.querySelectorAll('#mobileCardsList .mobile-pegawai-card');
    const pillButtons = document.querySelectorAll('#pillFilterGroup .filter-pill');
    const visibleCounter = document.getElementById('visibleCounter');
    const desktopEmpty = document.getElementById('desktopLiveSearchEmpty');
    const mobileEmpty = document.getElementById('mobileLiveSearchEmpty');
    const desktopQueryTerm = document.getElementById('desktopSearchQueryTerm');

    const filterTahun = document.getElementById('filterTahunInput');
    if (filterTahun) {
        filterTahun.addEventListener('wheel', function() {
            this.blur();
        });
    }

    let currentSearchTerm = '';
    let currentFilterCategory = 'all';

    function matchesRowCriteria(el) {
        const nama = el.getAttribute('data-nama') || '';
        const nip = el.getAttribute('data-nip') || '';
        const unit = el.getAttribute('data-unit') || '';
        const jabatan = el.getAttribute('data-jabatan') || '';
        const statusCat = el.getAttribute('data-status') || '';

        const matchesSearch = !currentSearchTerm
            || nama.includes(currentSearchTerm)
            || nip.includes(currentSearchTerm)
            || unit.includes(currentSearchTerm)
            || jabatan.includes(currentSearchTerm);

        let matchesCategory = true;
        if (currentFilterCategory !== 'all') {
            matchesCategory = statusCat.split(' ').includes(currentFilterCategory);
        }

        return matchesSearch && matchesCategory;
    }

    function applyFilterAndSearch() {
        let visibleCount = 0;

        desktopRows.forEach(row => {
            if (matchesRowCriteria(row)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        mobileCards.forEach(card => {
            card.style.display = matchesRowCriteria(card) ? '' : 'none';
        });

        if (desktopEmpty) {
            if (visibleCount === 0 && desktopRows.length > 0) {
                desktopEmpty.style.display = '';
                if (desktopQueryTerm) desktopQueryTerm.textContent = currentSearchTerm || currentFilterCategory;
            } else {
                desktopEmpty.style.display = 'none';
            }
        }

        if (mobileEmpty) {
            mobileEmpty.style.display = (visibleCount === 0 && mobileCards.length > 0) ? 'block' : 'none';
        }

        if (visibleCounter) {
            visibleCounter.textContent = (visibleCount === desktopRows.length)
                ? `Menampilkan ${desktopRows.length} pegawai · Pilih baris untuk rincian`
                : `Menampilkan ${visibleCount} dari ${desktopRows.length} pegawai`;
        }
    }

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
        const trigger = header.querySelector('.btn-sort-th') || header;
        trigger.addEventListener('click', function() {
            const sortKey = header.getAttribute('data-sort');

            if (currentSortKey === sortKey) {
                currentSortDir = (currentSortDir === 'asc') ? 'desc' : 'asc';
            } else {
                currentSortKey = sortKey;
                currentSortDir = 'asc';
            }

            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc', 'th-sorted');
                h.setAttribute('aria-sort', 'none');
                const icon = h.querySelector('.sort-indicator');
                if (icon) icon.className = 'bi bi-arrow-down-up sort-indicator';
            });

            header.classList.add(currentSortDir === 'asc' ? 'asc' : 'desc', 'th-sorted');
            header.setAttribute('aria-sort', currentSortDir === 'asc' ? 'ascending' : 'descending');
            const icon = header.querySelector('.sort-indicator');
            if (icon) {
                icon.className = currentSortDir === 'asc'
                    ? 'bi bi-arrow-up sort-indicator fw-bold'
                    : 'bi bi-arrow-down sort-indicator fw-bold';
            }

            const rowsArray = Array.from(document.querySelectorAll('#tableBody .desktop-pegawai-row'));
            const cardsArray = mobileCardsContainer ? Array.from(mobileCardsContainer.querySelectorAll('.mobile-pegawai-card')) : [];

            const comparator = (a, b) => {
                let valA, valB;
                if (sortKey === 'no') {
                    valA = parseFloat(a.getAttribute('data-val-no')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-no')) || 0;
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
            rowsArray.forEach(row => tableBody.appendChild(row));
            if (desktopEmpty) tableBody.appendChild(desktopEmpty);

            if (mobileCardsContainer && cardsArray.length > 0) {
                cardsArray.sort(comparator);
                cardsArray.forEach(card => mobileCardsContainer.appendChild(card));
            }
        });
    });

    // Detail Pegawai Modal triggers (desktop row click + focusable name button; mobile card click/keyboard)
    document.querySelectorAll('.btn-detail-pegawai').forEach(el => {
        el.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const nama = this.getAttribute('data-val-nama') || this.getAttribute('data-nama');
            loadDetailPegawai(userId, nama);
        });

        if (el.tagName !== 'TR') {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const userId = this.getAttribute('data-user-id');
                    const nama = this.getAttribute('data-val-nama') || this.getAttribute('data-nama');
                    loadDetailPegawai(userId, nama);
                }
            });
        }
    });

    // Synchronized Export Handlers
    function buildSyncedExportUrl(baseUrl) {
        const form = document.getElementById('filterForm');
        if (!form) return baseUrl;
        const urlObj = new URL(baseUrl, window.location.origin);
        const bulan = form.querySelector('[name="bulan"]')?.value;
        const tahun = form.querySelector('[name="tahun"]')?.value;
        const role = form.querySelector('[name="role"]')?.value;
        const unit = form.querySelector('[name="unit"]')?.value;
        if (bulan !== undefined) urlObj.searchParams.set('bulan', bulan);
        if (tahun !== undefined) urlObj.searchParams.set('tahun', tahun);
        if (role !== undefined) urlObj.searchParams.set('role', role);
        if (unit !== undefined) urlObj.searchParams.set('unit', unit);
        return urlObj.toString();
    }

    async function triggerExportWithLoading(btnEl, typeText, defaultFilename) {
        const rawHref = btnEl.getAttribute('href');
        if (!rawHref) return;
        const url = buildSyncedExportUrl(rawHref);

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Menyiapkan ${typeText}...`,
                html: `
                    <div class="d-flex flex-column align-items-center gap-2 my-2">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="fw-semibold text-dark small">Menyiapkan dokumen...</div>
                        <span class="ecc-text-muted-aa small">Berkas akan terunduh otomatis.</span>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: async () => {
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error('Gagal mengunduh berkas');
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
            triggerExportWithLoading(this, 'Laporan PDF', 'Laporan_Rekapitulasi_Kinerja_ECC.pdf');
        });
    }
});
</script>
<?= $this->endSection() ?>
