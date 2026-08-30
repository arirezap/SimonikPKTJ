<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Detail Standar - ECC') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. BREADCRUMB & HEADER BANNER (BENTO STYLE) -->
<div class="bento-card border-top border-4 border-primary shadow-sm mb-4 bento-stagger bento-stagger-1">
    <div class="bento-body p-4">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="font-size: 0.825rem;">
                <?php
                    $dashboardUrl = ($from_page === 'admin') ? site_url('dashboard?tahun_ecc=' . $tahun) : site_url('dashboard?tahun_ecc=' . $tahun);
                    $dashboardName = ($from_page === 'admin') ? 'Dashboard Admin' : 'Dashboard';
                ?>
                <li class="breadcrumb-item">
                    <a href="<?= $dashboardUrl ?>" class="text-decoration-none text-primary fw-medium">
                        <i class="bi bi-speedometer2 me-1"></i> <?= $dashboardName ?>
                    </a>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Evidence Command Center (ECC)
                </li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">
                    Standar <?= esc($standar['id'] ?? '') ?>
                </li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                        <i class="bi bi-mortarboard-fill me-1"></i> PRODI <?= strtoupper(esc($prodi)) ?>
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-calendar3 me-1"></i> TAHUN <?= esc($tahun) ?>
                    </span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-shield-check me-1"></i> Standar Akreditasi LED
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                    Detail: <span class="text-primary-bento"><?= esc($standar['nama_standar'] ?? 'Standar LED') ?></span>
                </h3>
                <p class="text-muted small mb-0">Rincian kriteria pemenuhan mutu, dokumen bukti fisik <em>(evidence)</em>, validasi bertingkat, dan capaian skor.</p>
            </div>
            
            <div>
                <a href="<?= $dashboardUrl ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 shadow-sm text-nowrap fw-medium d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> <?= ($from_page === 'admin') ? 'Kembali ke Dashboard Admin' : 'Kembali ke Dashboard' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 2. QUICK STATS KPI SUMMARY ROW -->
<div class="row g-3 mb-4 bento-stagger bento-stagger-2">
    <!-- Stat 1: Nilai Rata-Rata Standar -->
    <div class="col-md-4">
        <div class="bento-card bg-primary-bento text-white h-100 shadow-sm">
            <div class="bento-body p-4 d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label text-white-50">Rata-Rata Skor Standar</div>
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px;">
                        <i class="bi bi-award-fill fs-5"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="stat-value text-white" id="valAvgSkor"><?= esc($avgSkor) ?><span class="fs-4">/100</span></div>
                    <div class="progress w-100 bg-white bg-opacity-25 mt-2 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-warning rounded-pill" style="width: <?= min(100, $avgSkor) ?>%; transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                    </div>
                </div>
                <div class="text-white-50 small mt-1">
                    <i class="bi bi-info-circle me-1"></i> Berdasarkan kriteria yang telah diverifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Dokumen Bukti Terunggah -->
    <div class="col-md-4">
        <div class="bento-card h-100 shadow-sm border-top border-4 border-info">
            <div class="bento-body p-4 d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label text-muted">Kelengkapan Bukti (Evidence)</div>
                    <div class="rounded-circle bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-link-45deg fs-5"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="stat-value text-dark" id="valEvidence"><?= esc($totalEvidence) ?><span class="fs-4 text-muted fw-normal"> / <?= esc($totalKriteria) ?></span></div>
                    <?php $pctEvidence = ($totalKriteria > 0) ? round(($totalEvidence / $totalKriteria) * 100, 1) : 0; ?>
                    <div class="progress w-100 bg-light mt-2 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-info rounded-pill" style="width: <?= $pctEvidence ?>%; transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    <span class="text-info fw-bold"><?= $pctEvidence ?>%</span> kriteria telah melampirkan tautan berkas
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Verifikasi Bertingkat -->
    <div class="col-md-4">
        <div class="bento-card h-100 shadow-sm border-top border-4 border-success">
            <div class="bento-body p-4 d-flex flex-column justify-content-between h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-label text-muted">Status Verifikasi Penuh</div>
                    <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-check2-all fs-5"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="stat-value text-dark" id="valApproved"><?= esc($totalApproved) ?><span class="fs-4 text-muted fw-normal"> / <?= esc($totalKriteria) ?></span></div>
                    <?php $pctApproved = ($totalKriteria > 0) ? round(($totalApproved / $totalKriteria) * 100, 1) : 0; ?>
                    <div class="progress w-100 bg-light mt-2 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-success rounded-pill" style="width: <?= $pctApproved ?>%; transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    <span class="text-success fw-bold"><?= $pctApproved ?>%</span> kriteria disetujui Kabag & Wadir
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. VISUALISASI SKOR PER KRITERIA (BAR CHART BENTO) -->
<div class="bento-card shadow-sm mb-4 bento-stagger bento-stagger-2">
    <div class="bento-header d-flex align-items-center justify-content-between border-bottom pb-3 mb-2 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="bg-primary-bento text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                <i class="bi bi-bar-chart-fill fs-6"></i>
            </div>
            <div>
                <span class="fw-bold text-dark fs-6">Visualisasi Skor per Elemen Kriteria</span>
                <div class="text-muted" style="font-size: 0.75rem;">Grafik interaktif pemenuhan skor akreditasi (0 - 100)</div>
            </div>
        </div>
        <!-- Legend Indicator -->
        <div class="d-flex align-items-center gap-3" style="font-size: 0.75rem;">
            <span class="d-flex align-items-center"><span class="badge bg-success me-1 p-1 rounded-circle"> </span> ≥ 80 (Sangat Baik)</span>
            <span class="d-flex align-items-center"><span class="badge bg-primary me-1 p-1 rounded-circle"> </span> < 80 (Terverifikasi)</span>
            <span class="d-flex align-items-center"><span class="badge bg-warning me-1 p-1 rounded-circle"> </span> Menunggu Validasi</span>
            <span class="d-flex align-items-center"><span class="badge bg-secondary me-1 p-1 rounded-circle"> </span> Belum Ada Bukti</span>
        </div>
    </div>
    <div class="bento-body p-4">
        <?php if (!empty($criteria_data)): ?>
            <div class="performance-chart-container" style="height: 280px; position: relative;">
                <canvas id="skorBarChart" role="img" aria-label="Grafik Skor per Kriteria Standar"></canvas>
            </div>
            <p class="text-center text-muted small mt-3 mb-0">
                <i class="bi bi-cursor-fill me-1 text-primary"></i> <strong>Tip:</strong> Klik salah satu batang pada grafik di atas untuk langsung menuju baris kriteria terkait pada tabel di bawah.
            </p>
        <?php else: ?>
            <div class="alert alert-light border text-center text-muted mb-0 shadow-sm py-4">
                <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-75"></i>
                Tidak ada data kriteria untuk ditampilkan pada grafik tahun ini.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 4. TABEL RINCIAN KRITERIA & BUKTI (HIGH DENSITY BENTO TABLE) -->
<div class="bento-card shadow-sm mb-4 bento-stagger bento-stagger-3">
    <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-bottom pb-3 mb-0">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                <i class="bi bi-table fs-6"></i>
            </div>
            <div>
                <span class="fw-bold text-dark fs-6">Rincian Elemen Kriteria & Bukti Fisik</span>
                <div class="text-muted" style="font-size: 0.75rem;">Daftar lengkap bukti pendukung akreditasi dan catatan evaluasi</div>
            </div>
        </div>
        
        <!-- Live Search & Filter Bar -->
        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
            <div class="input-group input-group-sm" style="max-width: 240px;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filterKeyword" class="form-control border-start-0 ps-0" placeholder="Cari nama kriteria..." aria-label="Cari Kriteria" onkeyup="filterCriteriaTable()">
            </div>
            
            <select id="filterStatus" class="form-select form-select-sm filter-select fw-semibold" style="width: auto; cursor: pointer;" onchange="filterCriteriaTable()">
                <option value="all">Semua Status</option>
                <option value="approved">Disetujui & Dinilai</option>
                <option value="pending">Menunggu Verifikasi</option>
                <option value="empty">Belum Ada Bukti</option>
            </select>
        </div>
    </div>
    
    <div class="bento-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0 border-0" id="criteriaTable" style="min-width: 1000px;">
                <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th style="width: 60px;" class="text-center ps-4 py-3 border-0 rounded-top-start">No.</th>
                        <th style="min-width: 340px;" class="py-3 border-0">Nama Kriteria / Elemen Mutu</th>
                        <th style="width: 140px;" class="text-center py-3 border-0">Bukti Fisik</th>
                        <th style="min-width: 240px;" class="py-3 border-0">Catatan Review</th>
                        <th style="width: 130px;" class="text-center py-3 border-0">Status Kabag</th>
                        <th style="width: 140px;" class="text-center py-3 border-0">Status Wadir</th>
                        <th style="width: 100px;" class="text-center pe-4 py-3 border-0 rounded-top-end">Skor</th>
                    </tr>
                </thead>
                <tbody class="border-top-0" id="criteriaTbody">
                    <?php if (!empty($criteria_data)): ?>
                        <?php foreach ($criteria_data as $item): ?>
                            <?php 
                                $statusCategory = 'empty';
                                if ($item['is_approved']) {
                                    $statusCategory = 'approved';
                                } elseif (!empty($item['catatan'])) {
                                    $statusCategory = 'pending';
                                }
                            ?>
                            <tr id="criteria-row-<?= $item['no'] ?>" class="criteria-item-row" data-status="<?= $statusCategory ?>" style="transition: background-color 0.3s ease;">
                                <td class="text-center fw-bold text-muted ps-4 py-3 border-bottom border-light">
                                    <span class="badge bg-light text-dark border rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                        <?= $item['no'] ?>
                                    </span>
                                </td>
                                <td class="py-3 border-bottom border-light">
                                    <div class="fw-semibold text-dark mb-1 criteria-name" style="line-height: 1.45; font-size: 0.9rem;">
                                        <?= nl2br(esc($item['nama_kriteria'])) ?>
                                    </div>
                                    <?php if (!empty($item['role_assignment']) && $item['role_assignment'] !== 'all'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                                            <i class="bi bi-person-badge me-1"></i> Penanggung Jawab: <?= strtoupper(esc($item['role_assignment'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if (!empty($item['catatan'])): ?>
                                        <a href="<?= esc($item['catatan_link'] ?? $item['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm text-nowrap fw-medium d-inline-flex align-items-center gap-1" style="font-size: 0.775rem;">
                                            <i class="bi bi-box-arrow-up-right"></i> Buka Link
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle px-2 py-1 rounded-pill" style="font-size: 0.725rem;">
                                            <i class="bi bi-dash me-1"></i> Belum Ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 border-bottom border-light" style="font-size: 0.825rem;">
                                    <?php if (!empty($item['catatan_kabag'])): ?>
                                        <div class="mb-1 p-2 bg-light rounded-3 border">
                                            <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i> Kabag:</strong> 
                                            <span class="text-secondary"><?= nl2br(esc($item['catatan_kabag'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['catatan_wadir'])): ?>
                                        <div class="p-2 bg-light rounded-3 border">
                                            <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-info"></i> Wadir:</strong> 
                                            <span class="text-secondary"><?= nl2br(esc($item['catatan_wadir'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($item['catatan_kabag']) && empty($item['catatan_wadir'])): ?>
                                        <span class="text-muted small"><em>(Belum ada catatan review)</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if ($item['kabag_approved'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Sesuai
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock-history me-1"></i> Belum Sesuai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if (!empty($item['status'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-patch-check-fill me-1"></i> <?= esc($item['status']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                            Belum Dinilai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4 py-3 border-bottom border-light">
                                    <span class="fw-bold fs-5 <?= $item['is_approved'] ? 'text-success' : 'text-muted opacity-75' ?>">
                                        <?= esc($item['skor_display']) ?>
                                    </span>
                                    <?php if (!$item['is_approved']): ?>
                                        <i class="bi bi-info-circle-fill text-warning ms-1" 
                                           style="cursor: pointer; font-size: 0.85rem;"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="left"
                                           title="<?= esc($item['skor_alasan_text']) ?>">
                                        </i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center p-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-75"></i>
                                Tidak ada data kriteria yang ditemukan untuk standar, prodi, dan tahun ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Touch Cards View (<768px) -->
        <div class="d-md-none p-3 d-flex flex-column gap-3" id="criteriaMobileCards">
            <?php if (!empty($criteria_data)): ?>
                <?php foreach ($criteria_data as $item): ?>
                    <?php 
                        $statusCategory = 'empty';
                        if ($item['is_approved']) {
                            $statusCategory = 'approved';
                        } elseif (!empty($item['catatan'])) {
                            $statusCategory = 'pending';
                        }
                    ?>
                    <div id="criteria-mobile-<?= $item['no'] ?>" class="criteria-item-row p-3 rounded-3 border bg-white shadow-sm" data-status="<?= $statusCategory ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                Kriteria #<?= $item['no'] ?>
                            </span>
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-bold fs-5 <?= $item['is_approved'] ? 'text-success' : 'text-muted' ?>">
                                    Skor: <?= esc($item['skor_display']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="fw-semibold text-dark mb-2 criteria-name" style="font-size: 0.875rem; line-height: 1.4;">
                            <?= nl2br(esc($item['nama_kriteria'])) ?>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?php if ($item['kabag_approved'] == 1): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                    Kabag: Sesuai
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                    Kabag: Belum Sesuai
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($item['status'])): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                    Wadir: <?= esc($item['status']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                                    Wadir: Belum Dinilai
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Link Bukti Button -->
                        <?php if (!empty($item['catatan'])): ?>
                            <a href="<?= esc($item['catatan_link'] ?? $item['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill w-100 py-1.5 fw-medium d-flex align-items-center justify-content-center gap-2 mb-2" style="font-size: 0.8rem;">
                                <i class="bi bi-box-arrow-up-right"></i> Buka Link Bukti Fisik
                            </a>
                        <?php else: ?>
                            <div class="p-1.5 bg-light rounded text-center text-muted small mb-2" style="font-size: 0.75rem;">
                                <i class="bi bi-dash-circle me-1"></i> Bukti fisik belum diunggah
                            </div>
                        <?php endif; ?>

                        <!-- Catatan Review -->
                        <?php if (!empty($item['catatan_kabag']) || !empty($item['catatan_wadir'])): ?>
                            <div class="p-2 bg-light rounded-3 border small mt-2" style="font-size: 0.775rem;">
                                <?php if (!empty($item['catatan_kabag'])): ?>
                                    <div class="mb-1"><strong>Kabag:</strong> <?= esc($item['catatan_kabag']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($item['catatan_wadir'])): ?>
                                    <div><strong>Wadir:</strong> <?= esc($item['catatan_wadir']) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-light border text-center text-muted mb-0 shadow-sm py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-75"></i>
                    Tidak ada data kriteria yang ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.table-active-pulse {
    background-color: rgba(13, 110, 253, 0.12) !important;
    outline: 2px solid rgba(13, 110, 253, 0.4);
    border-radius: 6px;
    animation: pulseHighlight 2s ease-out;
}
@keyframes pulseHighlight {
    0% { background-color: rgba(13, 110, 253, 0.25); }
    100% { background-color: transparent; }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Helper: Smooth KPI Metric Count-Up Number Ticker
function animateValue(elementId, start, end, duration = 800, suffix = '', decimals = 0) {
    const obj = document.getElementById(elementId);
    if (!obj) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        obj.innerHTML = (decimals > 0 ? end.toFixed(decimals) : Math.round(end)) + suffix;
        return;
    }
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeProgress = 1 - Math.pow(2, -10 * progress);
        const current = start + (end - start) * easeProgress;
        obj.innerHTML = (decimals > 0 ? current.toFixed(decimals) : Math.round(current)) + suffix;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.innerHTML = (decimals > 0 ? end.toFixed(decimals) : Math.round(end)) + suffix;
        }
    };
    window.requestAnimationFrame(step);
}

// Client-side Live Search & Filter
function filterCriteriaTable() {
    const keyword = (document.getElementById('filterKeyword')?.value || '').toLowerCase().trim();
    const status = document.getElementById('filterStatus')?.value || 'all';
    const rows = document.querySelectorAll('.criteria-item-row');
    
    let visibleCount = 0;
    rows.forEach(row => {
        const text = (row.querySelector('.criteria-name')?.innerText || '').toLowerCase();
        const rowStatus = row.getAttribute('data-status') || '';
        
        const matchKeyword = !keyword || text.includes(keyword);
        const matchStatus = (status === 'all') || (rowStatus === status);
        
        if (matchKeyword && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // Initial KPI Number Ticker
    animateValue('valAvgSkor', 0, <?= (float)$avgSkor ?>, 850, '<span class="fs-4">/100</span>', 1);

    // Inisialisasi Chart.js
    const barChartLabels = <?= $barChartLabels ?? '[]'; ?>;
    const barChartScores = <?= $barChartScores ?? '[]'; ?>;
    const barChartColors = <?= $barChartColors ?? '[]'; ?>;
    const barChartTooltips = <?= $barChartTooltips ?? '[]'; ?>;

    const ctxBar = document.getElementById('skorBarChart');
    if (ctxBar && Array.isArray(barChartLabels) && barChartLabels.length > 0) {
        const chart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barChartLabels,
                datasets: [{
                    label: 'Skor Kriteria',
                    data: barChartScores,
                    backgroundColor: barChartColors.length ? barChartColors : 'rgba(30, 64, 175, 0.75)',
                    borderColor: 'rgba(30, 64, 175, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', borderDash: [3, 3] },
                        ticks: { stepSize: 25, color: '#64748b', font: { family: 'Inter', size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(tooltipItems) {
                                const index = tooltipItems[0].dataIndex;
                                const fullText = (barChartTooltips[index] && barChartTooltips[index].nama_kriteria) ? barChartTooltips[index].nama_kriteria : '';
                                const maxLength = 45;
                                let lines = [];
                                for (let i = 0; i < fullText.length; i += maxLength) {
                                    lines.push(fullText.substring(i, i + maxLength));
                                }
                                return lines;
                            },
                            label: function(tooltipItem) {
                                return ' Skor Capaian: ' + (tooltipItem.parsed.y !== null ? tooltipItem.parsed.y : 0);
                            },
                            afterLabel: function(tooltipItem) {
                                const index = tooltipItem.dataIndex;
                                const alasan = (barChartTooltips[index] && barChartTooltips[index].skor_alasan) ? barChartTooltips[index].skor_alasan : '';
                                return alasan ? ' Status: ' + alasan : '';
                            }
                        }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const criteriaNo = idx + 1;
                        const desktopRow = document.getElementById('criteria-row-' + criteriaNo);
                        const mobileRow = document.getElementById('criteria-mobile-' + criteriaNo);
                        const targetRow = (window.innerWidth >= 768) ? desktopRow : mobileRow;
                        
                        if (targetRow) {
                            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            targetRow.classList.remove('table-active-pulse');
                            void targetRow.offsetWidth; // Trigger reflow
                            targetRow.classList.add('table-active-pulse');
                            setTimeout(() => targetRow.classList.remove('table-active-pulse'), 2500);
                        }
                    }
                },
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }

    // Inisialisasi Bootstrap Tooltip
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
<?= $this->endSection() ?>