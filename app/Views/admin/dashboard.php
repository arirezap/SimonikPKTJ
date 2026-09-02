<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 0. TOP FILTER TOOLBAR (8pt Grid System) -->
<div class="d-flex justify-content-start align-items-center flex-wrap gap-2 mb-4 bento-stagger bento-stagger-1">
    <form id="formKinerja" class="m-0 d-flex flex-wrap gap-2 align-items-center">
        <select class="form-select form-select-sm filter-select fw-semibold text-dark bg-white shadow-xs rounded-pill border" id="bulan_kinerja" name="bulan_kinerja" aria-label="Pilih Bulan Kinerja" style="height: 36px; padding: 0 32px 0 16px; width: auto; min-width: 160px; cursor: pointer; font-size: 0.82rem;" onchange="updateKinerjaData()">
            <option value="all" <?= ($bulan_kinerja === 'all') ? 'selected' : '' ?>>Semua Bulan (Setahun)</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i; ?>" <?= ((string)$bulan_kinerja === (string)$i) ? 'selected' : ''; ?>><?= bulan_indo($i) ?></option>
            <?php endfor; ?>
        </select>
        <select name="tahun_kinerja" id="tahun_kinerja" class="form-select form-select-sm filter-select fw-semibold text-dark bg-white shadow-xs rounded-pill border" aria-label="Pilih Tahun Kinerja" style="height: 36px; padding: 0 32px 0 16px; width: auto; min-width: 140px; cursor: pointer; font-size: 0.82rem;" onchange="updateKinerjaData()">
            <?php foreach ($daftar_tahun as $tahun_item): ?>
                <option value="<?= esc($tahun_item) ?>" <?= ($tahun_kinerja == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- 1. ECC DASHBOARD (TOP HERO SECTION - 8pt Grid) -->
<div class="row g-4 mb-5 bento-stagger bento-stagger-1">
    <div class="col-lg-8 d-flex flex-column">
        <div class="bento-card border-top border-4 border-primary flex-fill rounded-4 shadow-sm">
            <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-2 gap-2" style="padding: 16px 24px;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 32px; height: 32px; border-radius: 8px;">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                    <div>
                        <div class="mb-0 fw-bold text-dark" style="font-size: 0.95rem; line-height: 1.3;">Evidence Command Center (ECC)</div>
                        <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">Monitoring Pemenuhan Standar Mutu</div>
                    </div>
                </div>
                
                <!-- Filter Tahun ECC -->
                <form id="formEcc" class="m-0 d-flex gap-2">
                    <select name="tahun_ecc" id="tahun_ecc" aria-label="Pilih Tahun ECC" class="form-select form-select-sm filter-select fw-semibold text-primary rounded-pill border" style="height: 32px; padding: 0 28px 0 12px; width: auto; cursor: pointer; font-size: 0.78rem;" onchange="updateEccData()">
                        <?php foreach ($daftar_tahun as $tahun_item): ?>
                            <option value="<?= esc($tahun_item) ?>" <?= ($tahun_ecc == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <div class="bento-body pt-2" style="padding: 24px;">
                <ul class="nav nav-pills ecc-tabs mb-4 gap-1" id="prodiTab" role="tablist">
                    <?php $tabIdx = 0; foreach($prodiData as $prodi): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-1.5 px-3 <?= $tabIdx === 0 ? 'active' : '' ?>" style="font-size: 0.82rem; font-weight: 600;" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
                        </li>
                    <?php $tabIdx++; endforeach; ?>
                </ul>

                <div class="tab-content border-0 p-0 shadow-none bg-transparent" id="prodiTabContent">
                    <?php $paneIdx = 0; foreach($prodiData as $prodi): ?>
                        <div class="tab-pane fade <?= $paneIdx === 0 ? 'show active' : '' ?>" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 px-0 px-md-3">
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                             Rangkuman Skor LED: <strong class="text-primary"><?= esc($prodi['nama_prodi']) ?></strong>
                                        </span>
                                    </div>
                                    <?php if (empty($prodi['chart_labels'])): ?>
                                        <div class="alert alert-light text-center border py-4 text-muted rounded-4 shadow-none" style="padding: 32px 16px;">
                                            <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary opacity-75"></i>
                                            Belum ada data Kategori LED untuk tahun ini.
                                        </div>
                                    <?php else: ?>
                                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>" role="img" aria-label="Grafik Radar Pemenuhan Standar Mutu <?= esc($prodi['nama_prodi']) ?>"></canvas></div>
                                        <p class="text-center text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php $paneIdx++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Rata-rata Kinerja (8pt Grid) -->
        <div class="bento-card bg-primary text-white flex-fill shadow-sm rounded-4" style="min-height: 160px; padding: 24px;">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; margin-bottom: 6px;">Rata-Rata Kinerja Bulanan</div>
                <div class="stat-value text-white num-tabular" id="valRataRataKinerja" aria-live="polite" aria-atomic="true" style="font-size: 2.25rem; font-weight: 700; line-height: 1.1; margin-bottom: 8px;">
                    <?= number_format((float)($rataRataValue ?? 0), 2, ',', '.') ?>
                </div>
                <div class="text-white-50 small d-flex align-items-center" style="font-size: 0.75rem; gap: 6px;"><i class="bi bi-graph-up-arrow"></i> Skor Agregat Seluruh Pegawai</div>
            </div>
        </div>
        
        <!-- Partisipasi Pegawai Aktif (8pt Grid) -->
        <div class="bento-card flex-fill shadow-sm border-top border-4 border-primary rounded-4" style="min-height: 160px; padding: 24px;">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; margin-bottom: 6px;">Tingkat Partisipasi Aktif</div>
                <div class="stat-value text-dark num-tabular" id="valPartisipasi" aria-live="polite" aria-atomic="true" style="font-size: 2.25rem; font-weight: 700; line-height: 1.1; margin-bottom: 8px;">
                    <?php 
                        $pctPart = ($totalPegawai > 0) ? round(($partisipasiAktif / $totalPegawai) * 100, 1) : 0;
                        echo esc($pctPart);
                    ?><span class="fs-4">%</span>
                </div>
                <div class="text-primary small fw-bold d-flex align-items-center" style="font-size: 0.75rem; gap: 6px;">
                    <i class="bi bi-people-fill"></i> 
                    <span><strong id="valPartisipasiCount" class="num-tabular"><?= esc($partisipasiAktif) ?></strong> dari <strong id="valTotalPegawai" class="num-tabular"><?= esc($totalPegawai) ?></strong> Pegawai Aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. ANALISIS KINERJA UNIT (8pt Grid) -->
<div class="bento-stagger bento-stagger-2">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.1rem;">Analitik Kinerja Unit</h5>
            <p class="text-muted mb-0 small" style="font-size: 0.78rem; margin-top: 2px;">Ringkasan performa seluruh unit berdasarkan laporan harian yang dinilai pada periode terpilih</p>
        </div>
    </div>

    <!-- Row Sebaran & Tren Kinerja -->
    <div class="row g-4 mb-5">
        <!-- Sebaran Kinerja Doughnut Chart -->
        <div class="col-lg-4">
            <div class="bento-card h-100 rounded-4 shadow-sm">
                <div class="bento-header fw-bold text-dark border-bottom" style="padding: 16px 24px; font-size: 0.88rem; min-height: 56px; display: flex; align-items: center;">Sebaran Predikat Kinerja</div>
                <div class="bento-body pt-0" style="padding: 24px;">
                    <div class="chart-container">
                        <canvas id="sebaranChart" role="img" aria-label="Grafik Sebaran Predikat Kinerja Organisasi"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trend Line Chart -->
        <div class="col-lg-8">
            <div class="bento-card h-100 rounded-4 shadow-sm">
                <div class="bento-header fw-bold text-dark border-bottom" style="padding: 16px 24px; font-size: 0.88rem; min-height: 56px; display: flex; align-items: center;">Tren Kinerja Bulanan Organisasi</div>
                <div class="bento-body pt-0" style="padding: 24px;">
                    <div class="chart-container">
                        <canvas id="trendChart" role="img" aria-label="Grafik Tren Kinerja Bulanan Organisasi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. LEADERBOARD UNIT & PEGAWAI (8pt Grid) -->
<div class="row g-4 mb-5 bento-stagger bento-stagger-3">
    <!-- Kolom Kiri: Top 5 Unit -->
    <div class="col-lg-4">
        <div class="bento-card h-100 d-flex flex-column rounded-4 shadow-sm">
            <div class="bento-header d-flex justify-content-between align-items-center border-bottom" style="padding: 16px 24px; min-height: 56px;">
                <div style="font-size: 0.88rem;" class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-building-up text-primary fs-6"></i>
                    <span>Top 5 Unit Kerja</span>
                </div>
                <a href="#unitPerformanceChart" class="btn btn-sm btn-light rounded-pill px-2.5 text-primary fw-semibold d-flex align-items-center" style="text-decoration: none; font-size: 0.72rem; height: 28px; gap: 4px;">
                    <span>Selengkapnya</span>
                    <i class="bi bi-arrow-down-short fs-6"></i>
                </a>
            </div>
            <div class="bento-body p-0 flex-fill d-flex flex-column justify-content-center">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyTop5Unit">
                            <?php if(empty($top5Unit)): ?>
                                <tr><td class="text-center text-muted py-4 small">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($top5Unit as $i => $u): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px; padding: 12px 16px;" class="text-center align-middle border-bottom border-light">
                                        <?php if($i == 0): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">1</div>
                                        <?php elseif($i == 1): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">2</div>
                                        <?php elseif($i == 2): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">3</div>
                                        <?php else: ?><div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width: 24px; height: 24px; font-size: 0.8rem;"><?= $i+1 ?></div><?php endif; ?>
                                    </td>
                                    <td class="border-bottom border-light" style="padding: 12px 16px; max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;" title="<?= esc($u['nama']) ?>"><?= esc($u['nama']) ?></div>
                                    </td>
                                    <td class="text-end border-bottom border-light" style="padding: 12px 16px;">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 shadow-sm border border-success num-tabular" style="font-size: 0.78rem; font-weight: 700;"><?= $u['rata'] > 0 ? number_format((float)$u['rata'], 2, ',', '.') : '-' ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top 5 Pegawai -->
    <div class="col-lg-4">
        <div class="bento-card h-100 rounded-4 shadow-sm">
            <div class="bento-header d-flex align-items-center text-dark border-bottom fw-bold" style="padding: 16px 24px; font-size: 0.88rem; min-height: 56px; gap: 8px;">
                <i class="bi bi-trophy-fill text-warning fs-6"></i>
                <span>Top 5 Pegawai Berkinerja Terbaik</span>
            </div>
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyTop5">
                            <?php if(empty($top5)): ?>
                                <tr><td class="text-center text-muted py-4 small">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($top5 as $i => $t): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px; padding: 12px 16px;" class="text-center align-middle border-bottom border-light">
                                        <?php if($i == 0): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">1</div>
                                        <?php elseif($i == 1): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">2</div>
                                        <?php elseif($i == 2): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width: 24px; height: 24px; font-size: 0.75rem; font-weight: 700;">3</div>
                                        <?php else: ?><div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width: 24px; height: 24px; font-size: 0.8rem;"><?= $i+1 ?></div><?php endif; ?>
                                    </td>
                                    <td class="border-bottom border-light" style="padding: 12px 16px; max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;" title="<?= esc($t['staf']['nama_lengkap']) ?>"><?= esc($t['staf']['nama_lengkap']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem; margin-top: 1px;" title="<?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                    </td>
                                    <td class="text-end border-bottom border-light" style="padding: 12px 16px;">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 shadow-sm border border-success num-tabular" style="font-size: 0.78rem; font-weight: 700;"><?= number_format((float)$t['rata_rata'], 2, ',', '.') ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom 5 / Perlu Perhatian Khusus -->
    <div class="col-lg-4">
        <div class="bento-card h-100 rounded-4 shadow-sm">
            <div class="bento-header d-flex align-items-center text-dark border-bottom fw-bold" style="padding: 16px 24px; font-size: 0.88rem; min-height: 56px; gap: 8px;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-6"></i>
                <span>Perlu Perhatian Khusus</span>
            </div>
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyBottom5">
                            <?php if(empty($bottom5)): ?>
                                <tr><td class="text-center text-muted py-4 small"><i class="bi bi-check-circle-fill text-success me-1"></i> Tidak ada pegawai yang perlu perhatian khusus</td></tr>
                            <?php else: ?>
                                <?php foreach($bottom5 as $i => $b): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px; padding: 12px 16px;" class="text-center align-middle border-bottom border-light">
                                        <div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-danger bg-danger bg-opacity-10 rounded-circle" style="width: 24px; height: 24px; font-size: 0.75rem;"><?= $i+1 ?></div>
                                    </td>
                                    <td class="border-bottom border-light" style="padding: 12px 16px; max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;" title="<?= esc($b['staf']['nama_lengkap']) ?>"><?= esc($b['staf']['nama_lengkap']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem; margin-top: 1px;" title="<?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                    </td>
                                    <td class="text-end border-bottom border-light" style="padding: 12px 16px;">
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1 shadow-sm border border-danger num-tabular" style="font-size: 0.78rem; font-weight: 700;"><?= number_format((float)$b['rata_rata'], 2, ',', '.') ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. GRAFIK KINERJA UNIT (8pt Grid) -->
<div class="bento-stagger bento-stagger-4">
    <div class="d-flex align-items-center mb-3">
        <h5 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2" style="font-size: 1rem;"><i class="bi bi-building"></i> Grafik Kinerja Unit</h5>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="bento-card border-top border-4 border-info rounded-4 shadow-sm">
                <div class="bento-body" style="padding: 24px;">
                    <?php if (empty($chartPegawaiUnitLabels)): ?>
                        <div class="alert alert-light border text-center text-muted mb-0 shadow-sm rounded-3"><i class="bi bi-info-circle me-2"></i> Belum ada data rekap kinerja staf.</div>
                    <?php else: ?>
                        <div class="performance-chart-container">
                            <canvas id="unitPerformanceChart" role="img" aria-label="Grafik Batang Kinerja Seluruh Unit Kerja Organisasi"></canvas>
                        </div>
                        <p class="text-center text-muted small mt-3 mb-0" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i> Klik pada grafik batang untuk melihat detail anggota unit kerja.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Unit (Bento Popup - 8pt Grid System) -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-labelledby="unitDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            <div class="modal-header bg-light border-bottom" style="padding: 16px 24px;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-building-check fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center flex-wrap gap-2" id="unitDetailModalLabel" style="font-size: 1rem; line-height: 1.3;">
                            <span>Detail Pegawai:</span>
                            <span id="modalUnitName" class="text-primary"></span>
                            <span id="modalPeriodBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;"></span>
                        </h6>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="table-responsive bg-white border rounded-3 shadow-sm overflow-hidden">
                    <table class="table table-hover align-middle mb-0 table-bento" style="font-size: 0.8rem;">
                        <thead class="bg-light text-muted small text-uppercase" style="background-color: #f8fafc;">
                            <tr>
                                <th class="ps-3" style="padding: 12px 16px;">Nama Pegawai</th>
                                <th class="text-center" style="padding: 12px 16px;">Target & Tambahan</th>
                                <th class="text-center pe-3" style="padding: 12px 16px;">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody" class="border-top-0">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top" style="padding: 12px 24px;">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" style="height: 32px; font-size: 0.78rem;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.pageCharts = window.pageCharts || {};
window.adminUnitStatsCache = {};
window.adminUnitLabelsCache = [];

// Helper: Smooth KPI Metric Count-Up Number Ticker
function animateValue(elementId, start, end, duration = 800, suffix = '', decimals = 0) {
    const obj = document.getElementById(elementId);
    if (!obj) return;
    
    // Respect user's reduced-motion settings
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

async function updateEccData() {
    const tahunEcc = document.getElementById('tahun_ecc').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=ecc&tahun_ecc=${tahunEcc}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data && data.prodiData) {
            for (const [id, prodi] of Object.entries(data.prodiData)) {
                const canvasId = 'radarChart-' + id;
                if(window.pageCharts[canvasId]) {
                    const chart = window.pageCharts[canvasId];
                    chart.data.labels = prodi.chart_labels.map(label => {
                        if (label.length <= 25) return label;
                        const words = label.split(' ');
                        const lines = [];
                        let currentLine = words[0];
                        for (let i = 1; i < words.length; i++) {
                            if (currentLine.length + 1 + words[i].length <= 25) { currentLine += ' ' + words[i]; } 
                            else { lines.push(currentLine); currentLine = words[i]; }
                        }
                        lines.push(currentLine);
                        return lines;
                    });
                    chart.data.labelIds = prodi.chart_label_ids;
                    chart.data.tahun = tahunEcc;
                    chart.data.datasets[0].data = prodi.chart_data;
                    chart.update();
                }
            }
        }
    } catch(err) { console.error("Gagal mengambil data ECC:", err); }
}

function updateDashboardYear(val) {
    const tahunEcc = document.getElementById('tahun_ecc');
    if (tahunEcc) {
        tahunEcc.value = val;
    }
    updateEccData();
    updateKinerjaData();
}

async function updateKinerjaData() {
    const bulanKinerja = document.getElementById('bulan_kinerja').value;
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&bulan_kinerja=${bulanKinerja}&tahun_kinerja=${tahunKinerja}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data) {
            // Update Text With Smooth Count-Up Tickers
            const currentRata = parseFloat(document.getElementById('valRataRataKinerja')?.innerText || 0);
            animateValue('valRataRataKinerja', currentRata, data.rataRataValue || 0, 750, '', 2);

            const partisipasi = data.partisipasiAktif || 0;
            const total = data.totalPegawai || 0;
            const pctPart = total > 0 ? parseFloat(((partisipasi / total) * 100).toFixed(1)) : 0;
            const currentPct = parseFloat(document.getElementById('valPartisipasi')?.innerText || 0);
            animateValue('valPartisipasi', currentPct, pctPart, 750, '<span class="fs-4">%</span>', 1);

            const currentPartCount = parseInt(document.getElementById('valPartisipasiCount')?.innerText || 0);
            animateValue('valPartisipasiCount', currentPartCount, partisipasi, 750);
            
            const currentTotPeg = parseInt(document.getElementById('valTotalPegawai')?.innerText || 0);
            animateValue('valTotalPegawai', currentTotPeg, total, 750);

            // Top 5 Unit Table
            let htmlUnit = '';
            if(data.top5Unit && data.top5Unit.length > 0) {
                data.top5Unit.forEach((u, i) => {
                    let badge = '';
                    if(i === 0) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>`;
                    else if(i === 1) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>`;
                    else if(i === 2) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>`;
                    else badge = `<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;">${i+1}</div>`;

                    const rataStr = (u.rata !== undefined && u.rata !== null) ? Number(u.rata).toFixed(2) : '0.00';
                    htmlUnit += `
                    <tr style="height: 56px;">
                        <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">${badge}</td>
                        <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="${escapeHtml(u.nama)}">${escapeHtml(u.nama)}</div>
                        </td>
                        <td class="text-end pe-4 py-2 border-bottom border-light">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 shadow-sm border border-success">${rataStr}</span>
                        </td>
                    </tr>`;
                });
            }
            document.getElementById('tbodyTop5Unit').innerHTML = htmlUnit;

            // Sebaran Doughnut Chart
            if(window.pageCharts['sebaranChart'] && data.sebaranKinerja) {
                window.pageCharts['sebaranChart'].data.datasets[0].data = [
                    data.sebaranKinerja.sangat_baik || 0,
                    data.sebaranKinerja.baik || 0,
                    data.sebaranKinerja.butuh_perbaikan || 0,
                    data.sebaranKinerja.kurang || 0,
                    data.sebaranKinerja.sangat_kurang || 0
                ];
                window.pageCharts['sebaranChart'].update();
            }

            // Trend Chart
            if(window.pageCharts['trendChart']) {
                window.pageCharts['trendChart'].data.datasets[0].data = data.trendBulananData || [];
                window.pageCharts['trendChart'].update();
            }

            // Top 5 / Bottom 5 Tables
            const renderTable = (arr, isTop) => {
                if(!arr || arr.length === 0) {
                    return isTop 
                        ? '<tr><td class="text-center text-muted py-4 small">Belum ada data</td></tr>' 
                        : '<tr><td class="text-center text-muted py-4 small"><i class="bi bi-check-circle-fill text-success me-1"></i> Tidak ada pegawai yang perlu perhatian khusus</td></tr>';
                }
                let html = '';
                arr.forEach((item, i) => {
                    let badge = '';
                    if(isTop) {
                        if(i === 0) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>`;
                        else if(i === 1) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>`;
                        else if(i === 2) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>`;
                        else badge = `<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;">${i+1}</div>`;
                    } else {
                        badge = `<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-danger" style="width:24px;height:24px;font-size:13px;">${i+1}</div>`;
                    }
                    const scoreClass = isTop ? 'bg-success text-success border-success' : 'bg-danger text-danger border-danger';
                    const rataStr = (item.rata_rata !== undefined && item.rata_rata !== null) ? Number(item.rata_rata).toFixed(2) : '0.00';
                    html += `
                    <tr style="height: 56px;">
                        <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">${badge}</td>
                        <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="${escapeHtml(item.staf.nama_lengkap)}">${escapeHtml(item.staf.nama_lengkap)}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="${escapeHtml(item.staf.jabatan || '-')}">${escapeHtml(item.staf.jabatan || '-')}</div>
                        </td>
                        <td class="text-end pe-3 py-2 border-bottom border-light">
                            <span class="badge ${scoreClass} bg-opacity-10 rounded-pill px-2 py-1 shadow-sm border" style="font-size: 0.8rem;">${rataStr}</span>
                        </td>
                    </tr>`;
                });
                return html;
            };
            document.getElementById('tbodyTop5').innerHTML = renderTable(data.top5, true);
            document.getElementById('tbodyBottom5').innerHTML = renderTable(data.bottom5, false);

            // Unit Chart
            if(window.pageCharts['unitChart']) {
                window.adminUnitLabelsCache = data.chartPegawaiUnitLabels || [];
                window.adminUnitStatsCache = data.unitStats || {};
                window.pageCharts['unitChart'].data.labels = window.adminUnitLabelsCache;
                window.pageCharts['unitChart'].data.datasets[0].data = data.chartPegawaiUnitData || [];
                window.pageCharts['unitChart'].update();
            }
        }
    } catch(err) { console.error("Gagal mengambil data Kinerja:", err); }
}

document.addEventListener('DOMContentLoaded', function () {
    // Initial KPI Number Ticker Animations
    animateValue('valRataRataKinerja', 0, <?= (float)($rataRataValue ?? 0) ?>, 850, '', 2);
    <?php $pctPartInitial = ($totalPegawai > 0) ? round(($partisipasiAktif / $totalPegawai) * 100, 1) : 0; ?>
    animateValue('valPartisipasi', 0, <?= (float)$pctPartInitial ?>, 850, '<span class="fs-4">%</span>', 1);
    animateValue('valPartisipasiCount', 0, <?= (int)$partisipasiAktif ?>, 850);
    animateValue('valTotalPegawai', 0, <?= (int)$totalPegawai ?>, 850);

    // Cleanup chart lama
    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) { window.pageCharts[key].destroy(); delete window.pageCharts[key]; }
    }

    // --- SCRIPT UNTUK MENGINGAT TAB AKTIF ECC ---
    const activeTabKey = 'ecc_active_prodi_tab';
    const savedTabTarget = localStorage.getItem(activeTabKey);
    if (savedTabTarget) {
        const targetBtn = document.querySelector(`button[data-bs-target="${savedTabTarget}"]`);
        if (targetBtn) {
            const tabObj = bootstrap.Tab.getInstance(targetBtn) || new bootstrap.Tab(targetBtn);
            tabObj.show();
        }
    }

    const prodiTabs = document.querySelectorAll('#prodiTab button[data-bs-toggle="tab"]');
    prodiTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                localStorage.setItem(activeTabKey, targetId);
            }
        });
    });

    // --- Helper: Word Wrap ---
    function splitLabel(label, maxLength = 16) {
        if (label.length <= maxLength) return label;
        const words = label.split(' ');
        const lines = [];
        let currentLine = words[0];
        for (let i = 1; i < words.length; i++) {
            if (currentLine.length + 1 + words[i].length <= maxLength) {
                currentLine += ' ' + words[i];
            } else {
                lines.push(currentLine);
                currentLine = words[i];
            }
        }
        lines.push(currentLine);
        return lines;
    }

    // --- EVENT KLIK LABEL ECC ---
    function getClickedLabel(clickEvent, chart) {
        if (!chart || !chart.scales || !chart.scales.r) return null;
        const r = chart.scales.r;
        const pointLabelItems = r._pointLabelItems; 
        if (!pointLabelItems || pointLabelItems.length === 0) return null;
        const canvasPosition = Chart.helpers.getRelativePosition(clickEvent, chart);
        const x = canvasPosition.x;
        const y = canvasPosition.y;
        let closestLabelIndex = -1;
        let minDistance = Infinity;
        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            const distance = Math.sqrt(Math.pow(x - item.x, 2) + Math.pow(y - item.y, 2));
            if (distance < minDistance) { minDistance = distance; closestLabelIndex = i; }
        }
        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                const itemWidth = item.options?.bounds?.width || 80; 
                if (minDistance < (itemWidth / 2) + 20) return closestLabelIndex;
            } catch (e) { if (minDistance < 50) return closestLabelIndex; }
        }
        return null;
    }

    // --- INIT RADAR CHART ECC ---
    const prodiData = <?= json_encode($prodiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const selectedTahun = '<?= esc($tahun_ecc) ?>';

    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 16));

            window.pageCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels,
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi, data: data.chart_data, fill: true,
                        backgroundColor: 'rgba(30, 64, 175, 0.15)', borderColor: 'rgba(30, 64, 175, 1)',
                        pointBackgroundColor: 'rgba(30, 64, 175, 1)', pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff', pointHoverBorderColor: 'rgba(30, 64, 175, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    animation: {
                        duration: 900,
                        easing: 'easeOutQuart'
                    },
                    layout: { padding: 10 },
                    scales: {
                        r: {
                            angleLines: { display: true, color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 },
                            min: 0, max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 },
                            pointLabels: { 
                                display: true, color: '#334155', font: { size: 12, weight: '700', family: 'system-ui', lineHeight: 1.2 }, 
                                backdropPadding: 2, padding: 8
                            },
                            ticks: { display: false, stepSize: 33.3333 }
                        }
                    },
                    plugins: { legend: { display: false } },
                    onHover: (event, activeElements, chart) => {
                         const index = getClickedLabel(event, chart);
                         event.native.target.style.cursor = (index !== null) ? 'pointer' : 'default';
                    },
                    onClick: (e, elements, chart) => {
                        const idx = getClickedLabel(e, chart);
                        if (idx !== null) {
                            if (chart.config.data.labelIds && chart.config.data.labelIds[idx]) {
                                const labelId = chart.config.data.labelIds[idx];
                                const prodi = chart.config.data.prodi.toLowerCase();
                                const tahun = chart.config.data.tahun;
                                document.body.style.cursor = 'wait'; 
                                window.location.href = `<?= site_url('ecc/detailStandar') ?>/${labelId}/${prodi}/${tahun}?from=admin`;
                            }
                        }
                    }
                }
            });
        }
    }

    // --- INIT CHART UNIT ---
    <?php if (!empty($chartPegawaiUnitLabels)): ?>
    const ctxUnit = document.getElementById('unitPerformanceChart');
    if (ctxUnit) {
        window.adminUnitStatsCache = <?= json_encode($unitStats ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        window.adminUnitLabelsCache = <?= json_encode($chartPegawaiUnitLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const unitData = <?= json_encode($chartPegawaiUnitData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        
        const dynamicHeight = Math.max(200, window.adminUnitLabelsCache.length * 40);
        ctxUnit.parentElement.style.height = dynamicHeight + 'px';
        
        window.pageCharts['unitChart'] = new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: window.adminUnitLabelsCache,
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: unitData,
                    backgroundColor: 'rgba(30, 64, 175, 0.85)',
                    borderColor: '#1e40af',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: '#1e40af'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            label: function(context) { return 'Rata-Rata: ' + Number(context.parsed.x || 0).toFixed(2); }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: 100,
                        grace: '8%',
                        grid: { borderDash: [2, 4], color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }, ticks: { font: { size: 11, family: 'system-ui' } }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const selectedUnit = window.adminUnitLabelsCache[idx];
                        const details = window.adminUnitStatsCache[selectedUnit]?.anggota;
                        
                        document.getElementById('modalUnitName').innerText = selectedUnit;
                        const bulanSelect = document.getElementById('bulan_kinerja');
                        const tahunSelect = document.getElementById('tahun_kinerja');
                        const periodText = (bulanSelect && tahunSelect && bulanSelect.selectedIndex >= 0) 
                            ? `${bulanSelect.options[bulanSelect.selectedIndex].text} ${tahunSelect.value}`
                            : '';
                        const periodBadgeEl = document.getElementById('modalPeriodBadge');
                        if (periodBadgeEl) periodBadgeEl.innerText = periodText;
                        
                        let tbody = '';
                        if(details && details.length > 0) {
                            details.forEach(item => {
                                let badgeClass = 'bg-secondary';
                                if (item.dinilai > 0 || item.rata_rata > 0) {
                                    if (item.rata_rata > 100) badgeClass = 'bg-success';
                                    else if (item.rata_rata > 90) badgeClass = 'bg-primary';
                                    else if (item.rata_rata > 75) badgeClass = 'bg-info text-dark';
                                    else if (item.rata_rata > 25) badgeClass = 'bg-warning text-dark';
                                    else badgeClass = 'bg-danger';
                                }
                                
                                const scoreItemStr = Number(item.rata_rata || 0).toFixed(2);
                                tbody += `
                                    <tr>
                                        <td class="ps-3 py-3 border-bottom border-light">
                                            <div class="fw-bold text-dark">${escapeHtml(item.nama)}</div>
                                            <div class="small text-muted">${escapeHtml(item.jabatan || '-')}</div>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                                    ${escapeHtml(item.total_pokok !== undefined ? item.total_pokok : item.total_laporan)} Pokok
                                                </span>
                                                <span class="badge ${(item.total_tambahan || 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border'} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                                    ${escapeHtml(item.total_tambahan || 0)} Tambahan
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light pe-3">
                                            <span class="badge ${badgeClass} fs-6 rounded-pill px-3 shadow-sm">${scoreItemStr}</span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tbody = `<tr><td colspan="3" class="text-center text-muted py-4">Data kosong</td></tr>`;
                        }
                        document.getElementById('unitDetailTbody').innerHTML = tbody;
                        
                        const unitModalEl = document.getElementById('unitDetailModal');
                        const unitModal = bootstrap.Modal.getInstance(unitModalEl) || new bootstrap.Modal(unitModalEl);
                        unitModal.show();
                    }
                },
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }
    <?php endif; ?>

    // --- PRO MAX ANALYTICS CHARTS ---
    
    // 1. Sebaran Predikat Kinerja (Doughnut Chart)
    const ctxSebaran = document.getElementById('sebaranChart');
    if (ctxSebaran) {
        window.pageCharts['sebaranChart'] = new Chart(ctxSebaran, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Baik (> 100% - 150%)', 'Baik (> 90% - 100%)', 'Butuh Perbaikan (> 75% - 90%)', 'Kurang (> 25% - 75%)', 'Sangat Kurang (≤ 25%)'],
                datasets: [{
                    data: [
                        <?= esc($sebaranKinerja['sangat_baik']) ?>, 
                        <?= esc($sebaranKinerja['baik']) ?>, 
                        <?= esc($sebaranKinerja['butuh_perbaikan']) ?>, 
                        <?= esc($sebaranKinerja['kurang']) ?>,
                        <?= esc($sebaranKinerja['sangat_kurang']) ?>
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'system-ui' } } }
                },
                onClick: function(evt, activeElements, chart) {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const fullLabel = chart.data.labels[index];
                        let realLabel = '';
                        if (fullLabel.includes('Sangat Baik')) realLabel = 'Sangat Baik';
                        else if (fullLabel.includes('Sangat Kurang')) realLabel = 'Sangat Kurang';
                        else if (fullLabel.includes('Butuh Perbaikan')) realLabel = 'Butuh Perbaikan';
                        else if (fullLabel.includes('Baik')) realLabel = 'Baik';
                        else if (fullLabel.includes('Kurang')) realLabel = 'Kurang';
                        
                        fetchChartDetail('sebaran', realLabel);
                    }
                }
            }
        });
    }

    // 4. Monthly Trend Line Chart
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        window.pageCharts['trendChart'] = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: <?= json_encode($trendBulananData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100,
                        grace: '8%',
                        grid: { borderDash: [5, 5], color: '#f3f4f6', drawBorder: false }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                },
                onClick: function(evt, activeElements, chart) {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const bulanIdx = index + 1;
                        const bulanNames = chart.data.labels;
                        fetchChartDetail('tren', bulanNames[index], bulanIdx);
                    }
                }
            }
        });
    }

});
    // --- HELPER ESCAPE HTML FOR DOM INJECTION ---
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // --- CHART DETAIL MODAL LOGIC ---
    function fetchChartDetail(mode, label, bulanVal = null) {
        const tahun = document.getElementById('tahun_kinerja').value;
        let bulan = document.getElementById('bulan_kinerja').value;
        if (mode === 'tren' && bulanVal) {
            bulan = bulanVal;
        }

        let title = '';
        let url = '<?= site_url('dashboard/api-detail-chart') ?>?mode=' + mode + '&tahun=' + tahun + '&bulan=' + (bulan === 'all' ? new Date().getMonth()+1 : bulan);
        
        if (mode === 'sebaran') {
            title = 'Pegawai Predikat ' + label;
            url += '&kategori=' + encodeURIComponent(label);
        } else if (mode === 'tren') {
            title = 'Detail Kinerja - ' + label + ' ' + tahun;
        }

        document.getElementById('detailChartTitle').innerText = title;
        document.getElementById('detailChartBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted small">Memuat data...</div>
            </div>`;
            
        const chartModalEl = document.getElementById('detailChartModal');
        const modal = bootstrap.Modal.getInstance(chartModalEl) || new bootstrap.Modal(chartModalEl);
        modal.show();

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (res.data.length === 0) {
                        document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-info py-2">Tidak ada data pegawai.</div>`;
                        return;
                    }
                    
                    let html = `<div class="table-responsive"><table class="table table-hover align-middle border">
                                <thead class="table-light"><tr><th class="ps-3">Nama Pegawai</th><th class="text-center">Nilai Rata-rata</th></tr></thead><tbody>`;
                    
                    res.data.forEach(p => {
                        let badgeColor = 'secondary';
                        if (p.rata_rata > 100) badgeColor = 'success';
                        else if (p.rata_rata > 90) badgeColor = 'primary';
                        else if (p.rata_rata > 75) badgeColor = 'info text-dark';
                        else if (p.rata_rata > 25) badgeColor = 'warning text-dark';
                        else if (p.rata_rata > 0 || (p.dinilai && p.dinilai > 0)) badgeColor = 'danger';

                        const scoreDetailStr = Number(p.rata_rata || 0).toFixed(2);
                        html += `<tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">${escapeHtml(p.nama)}</div>
                                        <div class="text-muted small">${escapeHtml(p.jabatan || '-')} • ${escapeHtml(p.unit || '-')}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-${badgeColor} rounded-pill px-3 py-2" style="font-size:0.85rem">${scoreDetailStr}</span>
                                    </td>
                                 </tr>`;
                    });
                    
                    html += `</tbody></table></div>`;
                    document.getElementById('detailChartBody').innerHTML = html;
                } else {
                    document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan pemuatan data.</div>`;
                }
            })
            .catch(err => {
                document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-danger py-2">Gagal menghubungi server.</div>`;
            });
    }
</script>

<!-- Modal Detail Chart -->
<div class="modal fade" id="detailChartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 bg-light rounded-top-4 pb-2">
        <h5 class="modal-title fw-bold text-dark" id="detailChartTitle">Detail Kinerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="min-width: 44px; min-height: 44px;"></button>
      </div>
      <div class="modal-body pt-2" id="detailChartBody">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>