<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap Kinerja Kepegawaian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* iOS Mobile Zoom Fix */
@media (max-width: 767.98px) {
    .form-control, .form-select {
        font-size: 16px !important;
    }
}

/* Staggered Grid Motion */
.bento-stagger {
    animation: bentoEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
    will-change: transform, opacity;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.10s; }
.bento-stagger-3 { animation-delay: 0.18s; }

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

/* Table Header Sorting */
th.sortable {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease;
}
th.sortable:hover {
    background-color: #e2e8f0 !important;
}
.sort-icon {
    display: inline-block;
    font-size: 0.75rem;
    margin-left: 4px;
    opacity: 0.4;
    transition: transform 0.2s ease, opacity 0.2s ease;
}
th.sortable.asc .sort-icon,
th.sortable.desc .sort-icon {
    opacity: 1;
    color: #0d6efd;
}

/* Quick Filter Pills */
.filter-pill {
    cursor: pointer;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.35rem 0.85rem;
    border-radius: 50rem;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #495057;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.filter-pill:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}
.filter-pill:active {
    transform: scale(0.95);
}
.filter-pill.active {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
    transform: scale(1.02);
}

.num-tabular {
    font-variant-numeric: tabular-nums;
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
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 2px solid #e2e8f0;
    padding: 0.7rem 0.75rem;
    vertical-align: middle;
}
.table-bento tbody td {
    padding: 0.65rem 0.75rem;
    vertical-align: middle;
    border-color: #f1f5f9;
}
.table-bento tbody tr:hover td {
    background-color: #f8fafc !important;
}

.cursor-pointer {
    cursor: pointer;
}
.pegawai-trigger {
    transition: transform 0.15s ease, opacity 0.15s ease;
    outline: none;
    border-radius: 8px;
    padding: 2px 4px;
}
.pegawai-trigger:hover {
    opacity: 0.9;
}
.pegawai-trigger:hover .pegawai-nama-link {
    color: #0d6efd !important;
    text-decoration: underline !important;
}
.pegawai-trigger:focus-visible {
    box-shadow: 0 0 0 2px #0d6efd;
}

/* Row Filter Smooth Animation */
@keyframes rowFadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.pegawai-row {
    transition: background-color 0.18s ease;
}
.pegawai-row.row-animated,
.mobile-pegawai-card.row-animated {
    animation: rowFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Mobile Card Styling */
.mobile-pegawai-card.cursor-pointer {
    transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s ease, border-color 0.18s ease;
    outline: none;
}
.mobile-pegawai-card.cursor-pointer:hover,
.mobile-pegawai-card.cursor-pointer:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08) !important;
    border-color: #0d6efd !important;
}

/* KPI Summary Stat Boxes */
.kpi-stat-box {
    border-radius: 12px;
    padding: 0.6rem 0.4rem;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.kpi-stat-box:hover {
    background-color: #f8fafc;
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
    .pegawai-row.row-animated,
    .mobile-pegawai-card.row-animated,
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
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-clipboard2-data-fill text-primary me-2"></i>Rekap Kinerja Kepegawaian</h1>
            <p class="text-muted small mb-0">Monitoring capaian kinerja seluruh unit untuk keperluan remunerasi & evaluasi berkala.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('kepegawaian/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1.5 btn-tactile" id="btnExportExcel">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- FILTER & KPI SUMMARY ROW -->
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">
        <!-- FILTER CARD -->
        <div class="col-lg-6 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-light py-2.5 px-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 small"><i class="bi bi-funnel-fill text-primary me-1.5"></i>Filter Periode & Unit Kerja</h6>
                </div>
                <div class="card-body p-3">
                    <form method="GET" action="<?= site_url('kepegawaian') ?>" class="row g-2 align-items-end" id="filterForm">
                        <div class="col-sm-4">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Periode Bulan</label>
                            <select name="bulan" class="form-select form-select-sm" onchange="showSkeletonAndSubmit()">
                                <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun (1 Tahun)</option>
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Tahun</label>
                            <input type="number" name="tahun" id="filterTahunInput" class="form-control form-control-sm" value="<?= esc($tahun_terpilih) ?>" onchange="showSkeletonAndSubmit()">
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.3px;">Unit Kerja</label>
                            <select name="unit" class="form-select form-select-sm" onchange="showSkeletonAndSubmit()">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($daftar_unit as $u): ?>
                                    <option value="<?= esc($u) ?>" <?= ($u == $unit_filter) ? 'selected' : '' ?>><?= esc($u) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- KPI SUMMARY CARD -->
        <div class="col-lg-6 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-2.5 p-xl-3 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0 align-items-stretch">
                        <div class="col-3 px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.4px;">Total</div>
                            <div class="my-1">
                                <div class="fw-bold text-dark num-tabular skeleton-hide" style="font-size: 1.35rem; white-space: nowrap; line-height: 1.2;" id="statTotalPegawai" data-val="<?= count($rekap_kinerja) ?>"><?= count($rekap_kinerja) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.6rem; display: none;"></div>
                            </div>
                            <div class="text-muted small" style="font-size: 0.68rem; white-space: nowrap;">Pegawai</div>
                        </div>
                        <div class="col-3 px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.4px;">Dinilai</div>
                            <div class="my-1">
                                <div class="fw-bold text-success num-tabular skeleton-hide" style="font-size: 1.35rem; white-space: nowrap; line-height: 1.2;" id="statSudahDinilai" data-val="<?= esc($sudah_dinilai) ?>"><?= esc($sudah_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.6rem; display: none;"></div>
                            </div>
                            <div class="text-success small fw-semibold" style="font-size: 0.68rem; white-space: nowrap;">
                                <?= count($rekap_kinerja) > 0 ? round(($sudah_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>
                        <div class="col-3 px-1 border-end d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.4px;">Belum</div>
                            <div class="my-1">
                                <div class="fw-bold text-danger num-tabular skeleton-hide" style="font-size: 1.35rem; white-space: nowrap; line-height: 1.2;" id="statBelumDinilai" data-val="<?= esc($belum_dinilai) ?>"><?= esc($belum_dinilai) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.6rem; display: none;"></div>
                            </div>
                            <div class="text-danger small fw-semibold" style="font-size: 0.68rem; white-space: nowrap;">
                                <?= count($rekap_kinerja) > 0 ? round(($belum_dinilai / count($rekap_kinerja)) * 100) : 0 ?>%
                            </div>
                        </div>
                        <div class="col-3 px-1 d-flex flex-column justify-content-between kpi-stat-box">
                            <div class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.4px;">Rata-Rata</div>
                            <div class="my-1">
                                <div class="fw-bold text-primary num-tabular skeleton-hide" style="font-size: 1.35rem; white-space: nowrap; line-height: 1.2; letter-spacing: -0.5px;" id="statRataRataInstansi" data-val="<?= round($rata_rata_instansi, 2) ?>"><?= str_replace('.', ',', round($rata_rata_instansi, 2)) ?></div>
                                <div class="skeleton-box skeleton-show mx-auto" style="width: 60%; height: 1.6rem; display: none;"></div>
                            </div>
                            <div class="text-primary small fw-semibold" style="font-size: 0.68rem; white-space: nowrap;">Instansi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL REKAP KINERJA (BENTO CARD) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0 small"><i class="bi bi-table text-primary me-1.5"></i>Daftar Capaian Pegawai</h6>
                <small class="text-muted" id="visibleCounter" style="font-size: 0.72rem;">Menampilkan <?= count($rekap_kinerja) ?> pegawai</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Live Search Box -->
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama, NIP, unit..." autocomplete="off">
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                    <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                </span>
            </div>
        </div>

        <!-- Quick Status Filter Pills -->
        <div class="px-3 py-2 bg-white border-bottom d-flex align-items-center gap-1.5 flex-wrap" id="pillFilterGroup">
            <span class="text-muted small fw-bold me-1" style="font-size: 0.72rem;">Filter Cepat:</span>
            <button type="button" class="filter-pill active" data-filter="all"><i class="bi bi-grid-fill me-0.5"></i> Semua</button>
            <button type="button" class="filter-pill" data-filter="sudah"><i class="bi bi-check-circle-fill text-success me-0.5"></i> Sudah Dinilai</button>
            <button type="button" class="filter-pill" data-filter="belum"><i class="bi bi-hourglass-split text-danger me-0.5"></i> Belum Dinilai</button>
            <button type="button" class="filter-pill" data-filter="perhatian"><i class="bi bi-exclamation-triangle-fill text-warning me-0.5"></i> &lt; 75 (Perhatian)</button>
            <button type="button" class="filter-pill" data-filter="baik"><i class="bi bi-stars text-primary me-0.5"></i> &ge; 90 (Baik)</button>
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
                <table class="table table-bordered table-hover align-middle mb-0 table-bento" id="mainDataTable">
                    <thead>
                        <tr>
                            <th class="ps-3 py-2.5 sortable" data-sort="nama" style="min-width: 230px;">
                                Pegawai <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <th class="py-2.5 sortable" data-sort="unit" style="min-width: 140px;">
                                Unit Kerja <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <?php if ($bulan_terpilih === 'all'): ?>
                                <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m): ?>
                                    <th class="text-center py-2.5" style="min-width: 48px; font-size: 0.7rem;"><?= $m ?></th>
                                <?php endforeach; ?>
                                <th class="text-center py-2.5 border-start bg-light shadow-sm sortable" data-sort="target" style="min-width: 85px;">
                                    Target Thn <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                </th>
                            <?php else: ?>
                                <th class="text-center py-2.5 sortable" data-sort="target" style="width: 110px;">
                                    Komponen <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                </th>
                            <?php endif; ?>
                            <th class="text-center py-2.5 sortable" data-sort="dinilai" style="width: 110px;">
                                Dinilai <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <th class="text-center pe-3 py-2.5 sortable desc" data-sort="nilai" style="width: 130px;">
                                Rata-Rata <span class="sort-icon"><i class="bi bi-arrow-down"></i></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($rekap_kinerja)): ?>
                            <tr class="no-data-row">
                                <td colspan="<?= ($bulan_terpilih === 'all') ? '17' : '5' ?>" class="text-center py-5">
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
                                    $jumlahTarget = $item['jumlah_komponen'] ?? $item['jumlah_rhk'];
                                    
                                    $statusCat = ($dinilai > 0) ? 'sudah' : 'belum';
                                    if ($dinilai > 0 && $rata < 75) $statusCat .= ' perhatian';
                                    if ($dinilai > 0 && $rata >= 90) $statusCat .= ' baik';

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
                                        $badgeClass = 'bg-secondary text-white';
                                        $statusText = 'Butuh Perbaikan';
                                    } elseif ($rata <= 100) {
                                        $badgeClass = 'bg-primary text-white';
                                        $statusText = 'Baik';
                                    } else {
                                        $badgeClass = 'bg-success text-white';
                                        $statusText = 'Sangat Baik';
                                    }
                                ?>
                                <tr class="pegawai-row" 
                                    data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                    data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                    data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                    data-status="<?= $statusCat ?>"
                                    data-val-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-val-unit="<?= esc($item['pegawai']['unit'] ?? '') ?>"
                                    data-val-target="<?= $jumlahTarget ?>"
                                    data-val-dinilai="<?= $dinilai ?>"
                                    data-val-nilai="<?= $rata ?>">
                                    <td class="ps-3 py-2.5" style="max-width: 240px;">
                                        <div class="d-flex align-items-center cursor-pointer pegawai-trigger btn-detail-pegawai" 
                                             role="button" 
                                             tabindex="0"
                                             data-user-id="<?= $item['pegawai']['id'] ?>" 
                                             data-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                             title="Klik untuk melihat rincian capaian kinerja <?= esc($item['pegawai']['nama_lengkap']) ?>">
                                            <?= render_user_avatar($item['pegawai'], $item['pegawai']['nama_lengkap'], 34, 'me-2 flex-shrink-0') ?>
                                            <div class="overflow-hidden">
                                                <span class="fw-bold text-dark d-block text-truncate lh-sm pegawai-nama-link" style="font-size: 0.82rem;">
                                                    <?= esc($item['pegawai']['nama_lengkap']) ?>
                                                </span>
                                                <div class="d-flex align-items-center gap-1 mt-0.5" style="font-size: 0.7rem;">
                                                    <?php if (!empty($item['pegawai']['jabatan'])): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0 text-truncate" style="max-width: 130px;"><?= esc($item['pegawai']['jabatan']) ?></span>
                                                    <?php endif; ?>
                                                    <span class="text-muted num-tabular"><?= esc($item['pegawai']['nip'] ?: '-') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="max-width: 150px;">
                                        <span class="small text-secondary text-truncate d-block" style="font-size: 0.78rem;">
                                            <i class="bi bi-building me-1 text-primary opacity-75"></i><?= esc($item['pegawai']['unit'] ?: '-') ?>
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
                                            <span class="fw-bold text-dark small"><?= $jumlahTarget ?></span>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-center num-tabular">
                                            <span class="fw-bold text-dark small"><?= $jumlahTarget ?></span>
                                            <?php if (!empty($item['has_tugas_tambahan'])): ?>
                                                <i class="bi bi-journal-plus text-success ms-0.5" title="Memiliki Tugas Tambahan"></i>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center num-tabular">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill px-2.5 py-0.5 border">
                                            <span class="fw-bold <?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary' ?> small"><?= $dinilai ?></span>
                                            <span class="text-muted mx-0.5 small">/</span>
                                            <span class="text-muted small"><?= $jumlahTarget ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-3 py-2.5 num-tabular">
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
                            <td colspan="<?= ($bulan_terpilih === 'all') ? '17' : '5' ?>" class="text-center py-5">
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
            <div class="mobile-cards-view p-2.5" id="mobileCardsContainer">
                <?php if (!empty($rekap_kinerja)): ?>
                    <div class="d-flex flex-column gap-2" id="mobileCardsList">
                        <?php foreach ($rekap_kinerja as $item): ?>
                            <?php
                                $rata = $item['rata_rata'];
                                $dinilai = $item['rhk_dinilai'];
                                $jumlahTarget = $item['jumlah_komponen'] ?? $item['jumlah_rhk'];
                                
                                $statusCat = ($dinilai > 0) ? 'sudah' : 'belum';
                                if ($dinilai > 0 && $rata < 75) $statusCat .= ' perhatian';
                                if ($dinilai > 0 && $rata >= 90) $statusCat .= ' baik';

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
                                    $badgeClass = 'bg-secondary text-white';
                                    $statusText = 'Butuh Perbaikan';
                                } elseif ($rata <= 100) {
                                    $badgeClass = 'bg-primary text-white';
                                    $statusText = 'Baik';
                                } else {
                                    $badgeClass = 'bg-success text-white';
                                    $statusText = 'Sangat Baik';
                                }
                            ?>
                            <div class="card border border-light-subtle rounded-3 shadow-sm mobile-pegawai-card cursor-pointer btn-detail-pegawai"
                                 role="button"
                                 tabindex="0"
                                 data-user-id="<?= $item['pegawai']['id'] ?>"
                                 data-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                 data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                 data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                 data-status="<?= $statusCat ?>"
                                 title="Klik untuk melihat rincian capaian">
                                <div class="card-body p-2.5">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <?= render_user_avatar($item['pegawai'], $item['pegawai']['nama_lengkap'], 34, 'me-2 flex-shrink-0') ?>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-dark mb-0 text-truncate pegawai-nama-link" style="max-width: 170px; font-size: 0.82rem;"><?= esc($item['pegawai']['nama_lengkap']) ?></h6>
                                                <small class="text-muted" style="font-size: 0.7rem;"><?= esc($item['pegawai']['unit'] ?: '-') ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end num-tabular">
                                            <div class="fw-bold <?= ($rata > 75) ? 'text-success' : (($dinilai == 0) ? 'text-secondary' : 'text-danger') ?>" style="font-size: 0.95rem;"><?= str_replace('.', ',', round($rata, 2)) ?></div>
                                            <span class="badge <?= $badgeClass ?> rounded-pill px-1.5 py-0.5" style="font-size: 0.62rem;"><?= $statusText ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small" style="font-size: 0.72rem;">
                                        <span><i class="bi bi-list-check me-1"></i> Komponen: <strong><?= $jumlahTarget ?></strong></span>
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi bi-check2-circle me-0.5"></i> Dinilai: <strong class="<?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary' ?>"><?= $dinilai ?>/<?= $jumlahTarget ?></strong>
                                            <i class="bi bi-chevron-right text-muted opacity-50 ms-1" style="font-size: 0.7rem;"></i>
                                        </span>
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

<!-- MODAL DETAIL KINERJA PEGAWAI (BENTO POPUP) -->
<div class="modal fade" id="modalDetailPegawai" tabindex="-1" aria-labelledby="modalDetailPegawaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            <div class="modal-header bg-light border-bottom py-2.5 px-3 px-md-4">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-badge fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="detailNamaPegawai">Rincian Kinerja Pegawai</h6>
                        <small class="text-muted" id="detailSubPegawai" style="font-size: 0.75rem;">Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <!-- Loader Inside Modal -->
                <div id="modalDetailLoader" class="text-center py-5">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted small">Mengambil rincian target dan evaluasi...</div>
                </div>

                <!-- Modal Detail Content -->
                <div id="modalDetailContent" style="display: none;">
                    <!-- EXECUTIVE PROFILE & SCORECARD -->
                    <div class="card bg-light border-0 rounded-4 p-3 mb-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Profil Pegawai</div>
                                <div class="fw-bold text-dark fs-6" id="modalInfoNama">-</div>
                                <div class="text-secondary small mt-0.5" id="modalInfoNipUnit">-</div>
                                <div class="text-muted small mt-1" id="modalInfoAtasan"><i class="bi bi-person-check text-success me-1"></i> Atasan Langsung: <strong id="modalTextAtasan" class="text-dark">-</strong></div>
                            </div>
                            <div class="d-flex align-items-center gap-3 bg-white p-2.5 px-3 rounded-4 border shadow-sm flex-shrink-0">
                                <div class="text-end">
                                    <div class="text-muted small fw-semibold" style="font-size: 0.68rem;">NILAI AKHIR</div>
                                    <span class="badge" id="modalDetailBadge">-</span>
                                </div>
                                <div class="fs-2 fw-bold text-primary num-tabular lh-1" id="modalDetailScore">0</div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL A: TARGET KINERJA RHK -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
                        <h6 class="fw-bold text-dark mb-0 small">
                            <i class="bi bi-list-task text-primary me-1.5"></i>A. Rincian Target Kinerja (RHK)
                        </h6>
                        <span class="badge bg-light text-dark border small" id="modalBadgeCountRhk">0 RHK</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-4 shadow-sm mb-3 overflow-hidden">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th>Indikator Kinerja / RHK</th>
                                    <th style="width: 120px;" class="text-center">Target</th>
                                    <th style="width: 120px;" class="text-center">Realisasi</th>
                                    <th style="width: 125px;" class="text-center">Selisih</th>
                                    <th style="width: 110px;" class="text-center">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableRhkBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- TABEL B: TUGAS TAMBAHAN -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
                        <h6 class="fw-bold text-dark mb-0 small">
                            <i class="bi bi-journal-plus text-success me-1.5"></i>B. Tugas Tambahan
                        </h6>
                        <span class="badge bg-light text-dark border small" id="modalBadgeScoreTambahan">Nilai: -</span>
                    </div>
                    <div class="table-responsive bg-white border rounded-4 shadow-sm mb-2 overflow-hidden">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento" style="font-size: 0.8rem;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th>Deskripsi Kegiatan</th>
                                    <th style="width: 110px;" class="text-center">Tanggal</th>
                                    <th style="width: 120px;" class="text-center">Capaian</th>
                                    <th style="width: 90px;" class="text-center">Bukti</th>
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
            <div class="modal-footer bg-light py-2 px-3 border-top">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
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
            document.getElementById('modalInfoNipUnit').textContent = `NIP: ${data.pegawai.nip} | Unit: ${data.pegawai.unit} | Jabatan: ${data.pegawai.jabatan}`;
            document.getElementById('modalTextAtasan').textContent = data.pegawai.atasan_nama;

            // Scorecard with live ticker
            const scoreVal = parseFloat(data.rata_rata) || 0;
            const scoreEl = document.getElementById('modalDetailScore');
            animateValue(scoreEl, 0, scoreVal, 600, 2);

            const badgeEl = document.getElementById('modalDetailBadge');
            badgeEl.className = `badge ${data.badge_class} rounded-pill px-2.5 py-1`;
            badgeEl.textContent = data.predikat;

            // Tabel A: RHK
            document.getElementById('modalBadgeCountRhk').textContent = `${data.total_rhk} RHK`;
            const rhkBody = document.getElementById('modalTableRhkBody');
            rhkBody.innerHTML = '';
            if (data.rekap_rhk && data.rekap_rhk.length > 0) {
                data.rekap_rhk.forEach((rhk, i) => {
                    const selisih = Math.round((Number(rhk.selisih) || 0) * 10000) / 10000;
                    let selisihBadge = '';
                    if (selisih > 0) {
                        selisihBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 num-tabular">+${formatAngkaIndo(selisih)} ${rhk.satuan}</span>`;
                    } else if (selisih < 0) {
                        selisihBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 num-tabular">${formatAngkaIndo(selisih)} ${rhk.satuan}</span>`;
                    } else {
                        selisihBadge = `<span class="badge bg-light text-dark border px-1.5 py-0.5 num-tabular">0 ${rhk.satuan}</span>`;
                    }

                    const nilaiText = rhk.nilai_capaian !== null ? `<span class="fw-bold text-primary num-tabular">${formatAngkaIndo(rhk.nilai_capaian)}%</span>` : `<span class="text-muted">-</span>`;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-muted">${i + 1}</td>
                        <td class="fw-semibold text-dark">${rhk.indikator}</td>
                        <td class="text-center fw-bold text-dark num-tabular">${formatAngkaIndo(rhk.target)} ${rhk.satuan}</td>
                        <td class="text-center fw-bold text-primary num-tabular">${formatAngkaIndo(rhk.realisasi)} ${rhk.satuan}</td>
                        <td class="text-center num-tabular">${selisihBadge}</td>
                        <td class="text-center align-middle">${nilaiText}</td>
                    `;
                    rhkBody.appendChild(tr);
                });
            } else {
                rhkBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data target RHK pada periode ini.</td></tr>`;
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

            if (data.tugas_tambahan && data.tugas_tambahan.length > 0) {
                data.tugas_tambahan.forEach((tmb, idx) => {
                    const buktiBtn = tmb.link_bukti ? `<a href="${tmb.link_bukti}" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5 btn-tactile" style="font-size: 0.72rem;"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>` : `<span class="text-muted">-</span>`;
                    const capaianText = tmb.capaian ? `${formatAngkaIndo(tmb.capaian)} ${tmb.satuan}` : '-';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-muted">${idx + 1}</td>
                        <td>${tmb.deskripsi.replace(/\n/g, '<br>')}</td>
                        <td class="text-center text-muted num-tabular">${tmb.tanggal}</td>
                        <td class="text-center fw-semibold text-dark num-tabular">${capaianText}</td>
                        <td class="text-center">${buktiBtn}</td>
                    `;
                    tambBody.appendChild(tr);
                });
            } else {
                tambBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada tugas tambahan pada periode ini.</td></tr>`;
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
    const statTotalEl = document.getElementById('statTotalPegawai');
    const statSudahEl = document.getElementById('statSudahDinilai');
    const statBelumEl = document.getElementById('statBelumDinilai');
    const statRataEl  = document.getElementById('statRataRataInstansi');

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
    if (statRataEl) {
        const val = parseFloat(statRataEl.getAttribute('data-val')) || 0;
        animateValue(statRataEl, 0, val, 850, 2);
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

    // Live Search
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            applyFilterAndSearch();
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

    // Column Sorting
    const sortableHeaders = document.querySelectorAll('th.sortable');
    const tableBody = document.getElementById('tableBody');

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort');
            let isAscending = !this.classList.contains('asc');

            // Reset other headers
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
                const icon = h.querySelector('.sort-icon i');
                if (icon) icon.className = 'bi bi-arrow-down-up';
            });

            this.classList.add(isAscending ? 'asc' : 'desc');
            const icon = this.querySelector('.sort-icon i');
            if (icon) icon.className = isAscending ? 'bi bi-arrow-up' : 'bi bi-arrow-down';

            const rowsArray = Array.from(document.querySelectorAll('.pegawai-row'));

            rowsArray.sort((a, b) => {
                let valA, valB;
                if (sortKey === 'nama') {
                    valA = a.getAttribute('data-val-nama').toLowerCase();
                    valB = b.getAttribute('data-val-nama').toLowerCase();
                    return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
                } else if (sortKey === 'unit') {
                    valA = a.getAttribute('data-val-unit').toLowerCase();
                    valB = b.getAttribute('data-val-unit').toLowerCase();
                    return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
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

                return isAscending ? valA - valB : valB - valA;
            });

            rowsArray.forEach(row => {
                row.classList.add('row-animated');
                tableBody.appendChild(row);
            });
            if (desktopEmpty) tableBody.appendChild(desktopEmpty);
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
});
</script>
<?= $this->endSection() ?>
