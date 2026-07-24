<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title ?? 'Dashboard') ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* BENTO GRID STYLES (Based on ui-ux-pro-max guidelines) */
    .bento-card {
        background-color: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: hidden;
    }
    .bento-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    .bento-header {
        padding: 1.25rem 1.5rem 0.5rem;
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
    }
    .bento-body {
        padding: 1.5rem;
    }
    
    /* Typography & Colors for Dashboards */
    .text-primary-bento { color: #1e40af; }
    .bg-primary-bento { background-color: #1e40af; }
    .bg-light-bento { background-color: #f8fafc; }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
    }
    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* ECC Tab Style Adjustment for Bento */
    .ecc-tabs {
        background-color: #f1f5f9;
        padding: 0.4rem;
        border-radius: 0.75rem;
        display: inline-flex;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }
    .ecc-tabs .nav-link { 
        border: none; 
        color: #64748b; 
        font-weight: 600;
        padding: 0.6rem 1.75rem;
        border-radius: 0.5rem;
        margin-right: 0.25rem;
        transition: all 0.2s ease;
    }
    .ecc-tabs .nav-link:hover { 
        background-color: #e2e8f0; 
        color: #0f172a;
    }
    .ecc-tabs .nav-link.active { 
        background-color: #1e40af; 
        color: #ffffff; 
        box-shadow: 0 4px 10px rgba(30, 64, 175, 0.3);
        transform: translateY(-1px);
    }
    
    .filter-select {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        transition: all 0.2s ease;
    }
    .filter-select:hover {
        border-color: #94a3b8 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    
    .radar-chart-container { position: relative; height: 420px; width: 100%; }
    .line-chart-container { position: relative; height: 300px; width: 100%; }
    .performance-chart-container { position: relative; height: 350px; width: 100%; }
    
    /* Custom Scrollbar for tables */
    .table-responsive::-webkit-scrollbar { width: 6px; height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. ECC DASHBOARD (TOP SECTION AS REQUESTED) -->
<div class="row g-4 mb-5">
    <div class="col-lg-8 d-flex flex-column">
        <div class="bento-card border-primary border-top border-4 flex-fill">
            <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-2 mb-2 gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-bento text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                    <div>
                        <div class="mb-0 fw-bold text-dark fs-6">Evidence Command Center (ECC)</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Monitoring Pemenuhan Standar Mutu</div>
                    </div>
                </div>
                
                <!-- Filter Tahun ECC -->
                <form id="formEcc" class="m-0 d-flex gap-2">
                    <select name="tahun_ecc" id="tahun_ecc" class="form-select form-select-sm filter-select fw-bold text-primary-bento" style="width: auto; cursor:pointer;" onchange="updateEccData()">
                        <?php foreach ($daftar_tahun as $tahun_item): ?>
                            <option value="<?= esc($tahun_item) ?>" <?= ($tahun_ecc == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <div class="bento-body pt-2">
                <ul class="nav nav-pills ecc-tabs mb-4" id="prodiTab" role="tablist">
                    <?php foreach($prodiData as $prodi): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3" style="font-size: 0.85rem;" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content border-0 p-0 shadow-none bg-transparent" id="prodiTabContent">
                    <?php foreach($prodiData as $prodi): ?>
                        <div class="tab-pane fade" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 px-0 px-md-3">
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                            Rangkuman Skor LED: <strong class="text-primary-bento"><?= esc($prodi['nama_prodi']) ?></strong>
                                        </span>
                                    </div>
                                    <?php if (empty($prodi['chart_labels'])): ?>
                                        <div class="alert alert-light text-center border">Belum ada data Kategori LED untuk tahun ini.</div>
                                    <?php else: ?>
                                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                                        <p class="text-center text-muted small mt-2"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Rata-rata Capaian -->
        <div class="bento-card bg-primary-bento text-white flex-fill shadow-sm" style="min-height: 150px;">
            <div class="bento-body d-flex flex-column justify-content-center align-items-center text-center py-4 h-100">
                <div class="stat-label text-white-50 mb-2">Nilai Rata-Rata Kinerja</div>
                <div class="stat-value text-white mb-2" id="valRataRataCapaian"><?= round($rataRataCapaian, 1) ?><span class="fs-4">%</span></div>
                <div class="progress w-75 bg-white bg-opacity-25 mt-2 rounded-pill" style="height: 6px;">
                    <div class="progress-bar bg-warning" id="barRataRataCapaian" style="width: <?= min(100, $rataRataCapaian) ?>%"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Indikator -->
        <div class="bento-card flex-fill shadow-sm border-top border-4 border-primary" style="min-height: 150px;">
            <div class="bento-body d-flex align-items-center justify-content-center h-100">
                <div class="bg-light rounded-circle p-3 me-3 text-primary-bento">
                    <i class="bi bi-list-check fs-3"></i>
                </div>
                <div>
                    <div class="stat-value fs-2" id="valTotalIndikator"><?= $totalIndikator ?></div>
                    <div class="stat-label text-uppercase" style="letter-spacing: 0.5px; font-size: 0.85rem;">Target Kinerja</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. ANALISIS KINERJA PRIBADI -->
<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <h5 class="fw-bold text-secondary mb-0"><i class="bi bi-person-workspace me-2"></i> Kinerja Pribadi</h5>
    <form id="formKinerja" class="m-0 d-flex gap-2">
        <select name="tahun_kinerja" id="tahun_kinerja" class="form-select filter-select fw-bold text-primary-bento" style="width: auto; cursor:pointer;" onchange="updateKinerjaData()">
            <?php foreach ($daftar_tahun as $tahun_item): ?>
                <option value="<?= esc($tahun_item) ?>" <?= ($tahun_kinerja == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<div class="row g-4 mb-5">

    <!-- Tren Kumulatif Chart -->
    <div class="col-12">
        <div class="bento-card">
            <div class="bento-header">
                Tren Nilai Rata-Rata Kinerja Bulanan
            </div>
            <div class="bento-body pt-0">
                <div class="line-chart-container">
                    <canvas id="kumulatifChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. ANALISIS KINERJA STAF / REKAN SATU UNIT -->
<?php if (((isset($isAtasan) && $isAtasan) || (isset($isUnitPeers) && $isUnitPeers)) && !empty($rekapDashboard)): ?>
<h5 class="fw-bold text-secondary mb-3" id="tabelRekapTitle">
    <?php if (isset($isAtasan) && $isAtasan): ?>
        <i class="bi bi-people-fill me-2"></i> Monitoring Kinerja Staf Staf
    <?php else: ?>
        <i class="bi bi-diagram-3-fill me-2"></i> Kinerja Rekan 1 Unit Kerja
    <?php endif; ?>
</h5>
<div class="row g-4 mb-5" id="tabelRekapContainer">
    <div class="col-12">
        <div class="bento-card">
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0">
                        <thead class="bg-light-bento text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3 border-0 rounded-top-start">Nama Pegawai</th>
                                <th class="text-center py-3 border-0">Jabatan</th>
                                <th class="text-center py-3 border-0">Target Dinilai</th>
                                <th class="text-center py-3 border-0 rounded-top-end">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0" id="tbodyKinerjaStaf">
                            <?php foreach ($rekapDashboard as $rekap): ?>
                                <?php 
                                    $rata = $rekap['rata_rata'];
                                    $warnaBadge = 'bg-success';
                                    if ($rata < 60) $warnaBadge = 'bg-danger';
                                    elseif ($rata < 75) $warnaBadge = 'bg-warning text-dark';
                                    elseif ($rata == 0 && $rekap['dinilai'] == 0) $warnaBadge = 'bg-secondary';
                                ?>
                                <tr>
                                    <td class="ps-4 py-3 border-bottom-0 border-bottom border-light">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold shadow-sm" style="width: 42px; height: 42px;">
                                                <?= strtoupper(substr(trim($rekap['staf']['nama_lengkap']), 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= esc($rekap['staf']['nama_lengkap']) ?></div>
                                                <small class="text-muted"><?= esc($rekap['staf']['nip']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3 text-muted border-bottom-0 border-bottom border-light">
                                        <?= esc($rekap['staf']['jabatan'] ?: '-') ?>
                                    </td>
                                    <td class="text-center py-3 border-bottom-0 border-bottom border-light">
                                        <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">
                                            <?= $rekap['dinilai'] ?> / <?= $rekap['total_laporan'] ?> laporan
                                        </span>
                                    </td>
                                    <td class="text-center py-3 border-bottom-0 border-bottom border-light">
                                        <span class="badge <?= $warnaBadge ?> fs-6 rounded-pill px-3 py-2 shadow-sm">
                                            <?= $rata ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 4. ANALISIS KINERJA UNIT (Khusus Super/Admin/Direktur/Wadir) -->
<?php if (isset($isSuper) && $isSuper): ?>
<div class="d-flex align-items-center mb-3">
    <h5 class="fw-bold text-secondary mb-0"><i class="bi bi-building me-2"></i> Kinerja Seluruh Pegawai (Per Unit)</h5>
    <span class="badge bg-primary bg-opacity-10 text-primary fs-7 ms-3 rounded-pill shadow-sm">Direktur / Wadir</span>
</div>

<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="bento-card border-top border-4 border-info">
            <div class="bento-body p-4">
                <?php if (empty($chartPegawaiUnitLabels)): ?>
                    <div class="alert alert-light border text-center text-muted mb-0 shadow-sm"><i class="bi bi-info-circle me-2"></i> Belum ada data rekap kinerja staf.</div>
                <?php else: ?>
                    <div class="performance-chart-container">
                        <canvas id="unitPerformanceChart"></canvas>
                    </div>
                    <p class="text-center text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i> Klik pada grafik batang untuk melihat detail anggota unit kerja.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="unitDetailModalLabel">Detail Pegawai: <span id="modalUnitName" class="text-primary-bento"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 rounded-start">Nama Pegawai</th>
                                <th class="text-center">Laporan Dinilai</th>
                                <th class="text-center rounded-end">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody" class="border-top-0">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.userDashboardCharts = window.userDashboardCharts || {};
window.unitStatsCache = {};
window.unitLabelsCache = [];

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
                if(window.userDashboardCharts[canvasId]) {
                    const chart = window.userDashboardCharts[canvasId];
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

async function updateKinerjaData() {
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&tahun_kinerja=${tahunKinerja}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data) {
            // Update Text
            const rr = data.rataRataCapaian || 0;
            document.getElementById('valRataRataCapaian').innerHTML = rr.toFixed(1) + '<span class="fs-4">%</span>';
            document.getElementById('barRataRataCapaian').style.width = Math.min(100, rr) + '%';
            document.getElementById('valTotalIndikator').innerText = data.totalIndikator || 0;
            
            // Update Line Chart
            if(window.userDashboardCharts['kumulatifChart']) {
                const chart = window.userDashboardCharts['kumulatifChart'];
                chart.data.datasets[0].data = data.cumulative_realisasi || [];
                chart.update();
            }

            // 3. Update Tabel Staf / Rekan 1 Unit
            const isAtasan = data.isAtasan;
            const isUnitPeers = data.isUnitPeers;
            const rekapDashboard = data.rekapDashboard || [];
            
            const tabelContainer = document.getElementById('tabelRekapContainer');
            const tabelTitle = document.getElementById('tabelRekapTitle');
            if (tabelContainer && tabelTitle) {
                if ((isAtasan || isUnitPeers) && rekapDashboard.length > 0) {
                    tabelContainer.style.display = 'flex';
                    tabelTitle.style.display = 'block';
                    if (isAtasan) {
                        tabelTitle.innerHTML = '<i class="bi bi-people-fill me-2"></i> Monitoring Kinerja Staf Staf';
                    } else {
                        tabelTitle.innerHTML = '<i class="bi bi-diagram-3-fill me-2"></i> Kinerja Rekan 1 Unit Kerja';
                    }
                    
                    let htmlStaf = '';
                    rekapDashboard.forEach(rekap => {
                        let rata = rekap.rata_rata;
                        let warnaBadge = 'bg-success';
                        if (rata < 60) warnaBadge = 'bg-danger';
                        else if (rata < 75) warnaBadge = 'bg-warning text-dark';
                        else if (rata == 0 && rekap.dinilai == 0) warnaBadge = 'bg-secondary';
                        
                        let avatar = rekap.staf.avatar ? `/uploads/avatars/${rekap.staf.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(rekap.staf.nama_lengkap)}&background=random`;
                        let jabatan = rekap.staf.jabatan || '-';
                        
                        htmlStaf += `
                        <tr>
                            <td class="ps-4 py-3 border-bottom border-light">
                                <div class="d-flex align-items-center">
                                    <img src="${avatar}" alt="Avatar" class="rounded-circle shadow-sm me-3" width="40" height="40" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark text-truncate" style="max-width:200px;">${rekap.staf.nama_lengkap}</div>
                                        <div class="small text-muted">${rekap.staf.nip || ''}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-3 border-bottom border-light">
                                <span class="badge bg-light text-secondary border px-2 py-1 text-wrap" style="max-width:150px;">${jabatan}</span>
                            </td>
                            <td class="text-center py-3 border-bottom border-light">
                                <span class="badge bg-light text-secondary border px-2 py-1">${rekap.dinilai} / ${rekap.total_laporan}</span>
                            </td>
                            <td class="text-center py-3 border-bottom border-light pe-4">
                                <span class="badge ${warnaBadge} fs-6 rounded-pill px-3 py-2 shadow-sm">${rata}</span>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('tbodyKinerjaStaf').innerHTML = htmlStaf;
                } else {
                    tabelContainer.style.display = 'none';
                    tabelTitle.style.display = 'none';
                }
            }

            // Update Unit Chart
            if (data.isSuper && window.userDashboardCharts['unitChart']) {
                const chart = window.userDashboardCharts['unitChart'];
                window.unitLabelsCache = data.chartPegawaiUnitLabels || [];
                window.unitStatsCache = data.unitStats || {};
                chart.data.labels = window.unitLabelsCache;
                chart.data.datasets[0].data = data.chartPegawaiUnitData || [];
                chart.update();
            }
        }
    } catch(err) { console.error("Gagal mengambil data Kinerja:", err); }
}

document.addEventListener('DOMContentLoaded', function () {
    
    // Cleanup Old Charts
    for (let key in window.userDashboardCharts) {
        if (window.userDashboardCharts[key]) {
            window.userDashboardCharts[key].destroy();
            delete window.userDashboardCharts[key];
        }
    }

    // --- SCRIPT UNTUK MENGINGAT TAB AKTIF ECC ---
    const activeTab = localStorage.getItem('userDashboardTab');
    if (activeTab) {
        const tabTrigger = document.querySelector('button[data-bs-target="' + activeTab + '"]');
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        } else {
            const firstTab = document.querySelector('#prodiTab button');
            if (firstTab) new bootstrap.Tab(firstTab).show();
        }
    } else {
        const firstTab = document.querySelector('#prodiTab button');
        if (firstTab) new bootstrap.Tab(firstTab).show();
    }

    const tabElements = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabElements.forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            localStorage.setItem('userDashboardTab', event.target.getAttribute('data-bs-target'));
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

    // --- DATA ECC ---
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_ecc) ?>';

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
    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            const existing = Chart.getChart(ctx);
            if (existing) existing.destroy();

            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 16));

            window.userDashboardCharts[canvasId] = new Chart(ctx, {
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
                    layout: { padding: 10 },
                    scales: { 
                        r: { 
                            angleLines: { display: true, color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 },
                            min: 0, max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 }, 
                            pointLabels: { 
                                display: true, 
                                color: '#334155', 
                                font: { size: 12, weight: '700', family: 'system-ui', lineHeight: 1.2 }, 
                                backdropPadding: 2,
                                padding: 8, 
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
                                window.location.href = `<?= site_url('ecc/detailStandar') ?>/${labelId}/${prodi}/${tahun}`;
                            }
                        }
                    }
                }
            });
        }
    }

    // --- INIT CHART PRIBADI (KUMULATIF) ---
    const ctxKumulatif = document.getElementById('kumulatifChart');
    if (ctxKumulatif) {
        window.userDashboardCharts['kumulatifChart'] = new Chart(ctxKumulatif.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($lineChartLabels ?? []) ?>,
                datasets: [
                    {
                        label: 'Nilai Rata-rata',
                        data: <?= json_encode($lineChartRealisasiData ?? []) ?>,
                        borderColor: '#2962ff',
                        backgroundColor: 'rgba(41, 98, 255, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#2962ff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'system-ui' } } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // --- INIT CHART UNIT (SUPER ADMIN) ---
    <?php if (isset($isSuper) && $isSuper && !empty($chartPegawaiUnitLabels)): ?>
    const ctxUnit = document.getElementById('unitPerformanceChart');
    if (ctxUnit) {
        window.unitStatsCache = <?= json_encode($unitStats ?? []) ?>;
        window.unitLabelsCache = <?= json_encode($chartPegawaiUnitLabels ?? []) ?>;
        const unitData = <?= json_encode($chartPegawaiUnitData ?? []) ?>;
        
        window.userDashboardCharts['unitChart'] = new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: window.unitLabelsCache,
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
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            label: function(context) { return 'Rata-Rata: ' + context.parsed.y + '%'; }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { borderDash: [2, 4], color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const selectedUnit = window.unitLabelsCache[idx];
                        const details = window.unitStatsCache[selectedUnit]?.anggota;
                        
                        document.getElementById('modalUnitName').innerText = selectedUnit;
                        
                        let tbody = '';
                        if(details && details.length > 0) {
                            details.forEach(item => {
                                let badgeClass = 'bg-success';
                                if(item.rata_rata < 60) badgeClass = 'bg-danger';
                                else if(item.rata_rata < 75) badgeClass = 'bg-warning text-dark';
                                else if(item.rata_rata == 0 && item.dinilai == 0) badgeClass = 'bg-secondary';
                                
                                tbody += `
                                    <tr>
                                        <td class="ps-3 py-3 border-bottom border-light">
                                            <div class="fw-bold text-dark">${item.nama}</div>
                                            <div class="small text-muted">${item.jabatan || '-'}</div>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light">
                                            <span class="badge bg-light text-dark border shadow-sm">${item.dinilai} / ${item.total_laporan}</span>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light pe-3">
                                            <span class="badge ${badgeClass} fs-6 rounded-pill px-3 shadow-sm">${item.rata_rata}</span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tbody = `<tr><td colspan="3" class="text-center text-muted py-4">Data staf kosong</td></tr>`;
                        }
                        document.getElementById('unitDetailTbody').innerHTML = tbody;
                        
                        new bootstrap.Modal(document.getElementById('unitDetailModal')).show();
                    }
                },
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>