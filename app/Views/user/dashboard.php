<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>



<?= $this->section('content') ?>

<!-- 0. TOP FILTER TOOLBAR (8pt Grid System) -->
<div class="d-flex justify-content-start align-items-center flex-wrap gap-2 mb-4 bento-stagger bento-stagger-1">
    <form id="formKinerja" class="m-0 d-flex flex-wrap gap-2 align-items-center">
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
        <!-- Rata-rata Capaian (8pt Grid) -->
        <div class="bento-card bg-primary text-white flex-fill shadow-sm rounded-4" style="min-height: 160px; padding: 24px;">
            <div class="d-flex flex-column justify-content-center align-items-center text-center h-100">
                <div class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; margin-bottom: 6px;">Nilai Rata-Rata Kinerja</div>
                <div class="stat-value text-white num-tabular" id="valRataRataCapaian" aria-live="polite" aria-atomic="true" style="font-size: 2.25rem; font-weight: 700; line-height: 1.1; margin-bottom: 8px;"><?= number_format($rataRataCapaian, 2, ',', '.') ?></div>
                <div class="progress w-75 bg-white bg-opacity-25 rounded-pill" style="height: 8px;">
                    <div class="progress-bar bg-warning rounded-pill" id="barRataRataCapaian" style="width: <?= min(100, $rataRataCapaian) ?>%; transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);"></div>
                </div>
            </div>
        </div>
        
        <!-- Total Komponen Dinilai (8pt Grid) -->
        <div class="bento-card flex-fill shadow-sm border-top border-4 border-primary rounded-4" style="min-height: 160px; padding: 24px;">
            <div class="d-flex align-items-center justify-content-center h-100" style="gap: 16px;">
                <div class="bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 48px; height: 48px; border-radius: 16px;">
                    <i class="bi bi-layers-fill fs-4"></i>
                </div>
                <div>
                    <div class="stat-value num-tabular" id="valTotalIndikator" aria-live="polite" aria-atomic="true" style="font-size: 2.25rem; font-weight: 700; line-height: 1.1; margin-bottom: 2px;"><?= $totalIndikator ?></div>
                    <div class="fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px; font-size: 0.78rem;">Komponen Dinilai</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">RHK Utama & Tugas Tambahan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. ANALISIS KINERJA PRIBADI (8pt Grid) -->
<div class="bento-stagger bento-stagger-2">
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <h5 class="fw-bold text-secondary mb-0 d-flex align-items-center gap-2" style="font-size: 1rem;"><i class="bi bi-person-workspace"></i> Kinerja Pribadi</h5>
    </div>
    <div class="row g-4 mb-5">
        <!-- Tren Kumulatif Chart -->
        <div class="col-12">
            <div class="bento-card rounded-4 shadow-sm">
                <div class="bento-header fw-bold text-dark border-bottom" style="padding: 16px 24px; font-size: 0.88rem; min-height: 56px; display: flex; align-items: center;">
                    Tren Nilai Rata-Rata Kinerja Bulanan
                </div>
                <div class="bento-body pt-0" style="padding: 24px;">
                    <div class="line-chart-container">
                        <canvas id="kumulatifChart" role="img" aria-label="Grafik Tren Nilai Rata-Rata Kinerja Bulanan Pribadi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. ANALISIS KINERJA STAF / REKAN SATU UNIT (8pt Grid) -->
<?php if (((isset($isAtasan) && $isAtasan) || (isset($isUnitPeers) && $isUnitPeers)) && !empty($rekapDashboard)): ?>
<div class="bento-stagger bento-stagger-3">
    <h5 class="fw-bold text-secondary mb-3 d-flex align-items-center gap-2" id="tabelRekapTitle" style="font-size: 1rem;">
        <?php if (isset($isAtasan) && $isAtasan): ?>
            <i class="bi bi-people-fill"></i> Monitoring Kinerja Staf Saya
        <?php else: ?>
            <i class="bi bi-diagram-3-fill"></i> Kinerja Rekan 1 Unit Kerja
        <?php endif; ?>
    </h5>
    <div class="row g-4 mb-5" id="tabelRekapContainer">
        <div class="col-12">
            <div class="bento-card rounded-4 shadow-sm">
                <div class="bento-body p-0">
                    <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0 table-bento" style="font-size: 0.8rem;">
                        <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px; background-color: #f8fafc;">
                            <tr>
                                <th class="ps-4 border-0 rounded-top-start" style="padding: 12px 16px;">Nama Pegawai</th>
                                <th class="text-center border-0" style="padding: 12px 16px;">Jabatan</th>
                                <th class="text-center border-0" style="padding: 12px 16px;">Target & Tambahan</th>
                                <th class="text-center border-0 rounded-top-end" style="padding: 12px 16px;">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0" id="tbodyKinerjaStaf">
                            <?php foreach ($rekapDashboard as $rekap): ?>
                                <?php 
                                    $rata = (float)$rekap['rata_rata'];
                                    $warnaBadge = 'bg-secondary';
                                    if ($rekap['dinilai'] > 0 || $rata > 0) {
                                        if ($rata > 100) $warnaBadge = 'bg-success';
                                        elseif ($rata > 90) $warnaBadge = 'bg-primary';
                                        elseif ($rata > 75) $warnaBadge = 'bg-info text-dark';
                                        elseif ($rata > 25) $warnaBadge = 'bg-warning text-dark';
                                        else $warnaBadge = 'bg-danger';
                                    }
                                ?>
                                <tr>
                                    <td class="ps-4 border-bottom border-light" style="padding: 12px 16px;">
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                                <?= strtoupper(substr(trim($rekap['staf']['nama_lengkap']), 0, 1)) ?>
                                            </div>
                                            <div style="min-width: 0;">
                                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;"><?= esc($rekap['staf']['nama_lengkap']) ?></div>
                                                <div class="text-muted small num-tabular" style="font-size: 0.72rem; margin-top: 1px;"><?= esc($rekap['staf']['nip'] ?: '-') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted border-bottom border-light" style="padding: 12px 16px;">
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-wrap" style="font-size: 0.74rem;"><?= esc($rekap['staf']['jabatan'] ?: '-') ?></span>
                                    </td>
                                    <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 num-tabular" style="font-size: 0.68rem;">
                                                <?= $rekap['total_pokok'] ?? $rekap['total_laporan'] ?> Pokok
                                            </span>
                                            <span class="badge <?= ($rekap['total_tambahan'] ?? 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?> rounded-pill px-2 py-0.5 num-tabular" style="font-size: 0.68rem;">
                                                <?= ($rekap['total_tambahan'] ?? 0) ?> Tambahan
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                        <span class="badge <?= $warnaBadge ?> rounded-pill px-3 py-1.5 shadow-sm num-tabular" style="font-size: 0.82rem; font-weight: 700;">
                                            <?= number_format($rata, 2, ',', '.') ?>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.userDashboardCharts = window.userDashboardCharts || {};

// Helper: Predikat Badge Class (Standar ECC)
function getPredikatBadgeClass(rata, dinilai = 1) {
    if (!dinilai || Number(rata) === 0) return 'bg-secondary';
    const val = Number(rata);
    if (val > 100) return 'bg-success';
    if (val > 90) return 'bg-primary';
    if (val > 75) return 'bg-info text-dark';
    if (val > 25) return 'bg-warning text-dark';
    return 'bg-danger';
}

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
        // Exponential ease-out
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

function updateDashboardYear(val) {
    const tahunEcc = document.getElementById('tahun_ecc');
    if (tahunEcc) {
        tahunEcc.value = val;
    }
    updateEccData();
    updateKinerjaData();
}

async function updateKinerjaData() {
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&tahun_kinerja=${tahunKinerja}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data) {
            // Update Text with smooth Count-up Ticker
            const currentRr = parseFloat(document.getElementById('valRataRataCapaian')?.innerText?.replace(',', '.') || 0);
            const rr = parseFloat(data.rataRataCapaian || 0);
            animateValue('valRataRataCapaian', currentRr, rr, 700, '', 2);
            
            const bar = document.getElementById('barRataRataCapaian');
            if (bar) bar.style.width = Math.min(100, rr) + '%';
            
            const currentTot = parseInt(document.getElementById('valTotalIndikator')?.innerText || 0);
            animateValue('valTotalIndikator', currentTot, data.totalIndikator || 0, 700);
            
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
                        tabelTitle.innerHTML = '<i class="bi bi-people-fill me-2"></i> Monitoring Kinerja Staf Saya';
                    } else {
                        tabelTitle.innerHTML = '<i class="bi bi-diagram-3-fill me-2"></i> Kinerja Rekan 1 Unit Kerja';
                    }
                    
                    function escapeHtml(str) {
                        if (str === null || str === undefined) return '';
                        return String(str)
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#039;');
                    }

                    let htmlStaf = '';
                    rekapDashboard.forEach(rekap => {
                        let rata = Number(rekap.rata_rata || 0);
                        const warnaBadge = getPredikatBadgeClass(rata, rekap.dinilai);
                        
                        let inisial = (rekap.staf.nama_lengkap || '').trim().charAt(0).toUpperCase();
                        let jabatan = rekap.staf.jabatan || '-';
                        let rataStr = rata.toFixed(2);
                        
                        htmlStaf += `
                        <tr>
                            <td class="ps-4 border-bottom border-light" style="padding: 12px 16px;">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.95rem;">
                                        ${escapeHtml(inisial)}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">${escapeHtml(rekap.staf.nama_lengkap)}</div>
                                        <div class="text-muted small num-tabular" style="font-size: 0.72rem; margin-top: 1px;">${escapeHtml(rekap.staf.nip || '-')}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-muted border-bottom border-light" style="padding: 12px 16px;">
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-wrap" style="font-size: 0.74rem;">${escapeHtml(jabatan)}</span>
                            </td>
                            <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 num-tabular" style="font-size: 0.68rem;">
                                        ${escapeHtml(rekap.total_pokok !== undefined ? rekap.total_pokok : rekap.total_laporan)} Pokok
                                    </span>
                                    <span class="badge ${(rekap.total_tambahan || 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border'} rounded-pill px-2 py-0.5 num-tabular" style="font-size: 0.68rem;">
                                        ${escapeHtml(rekap.total_tambahan || 0)} Tambahan
                                    </span>
                                </div>
                            </td>
                            <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                <span class="badge ${warnaBadge} rounded-pill px-3 py-1.5 shadow-sm num-tabular" style="font-size: 0.82rem; font-weight: 700;">${rataStr}</span>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('tbodyKinerjaStaf').innerHTML = htmlStaf;
                } else {
                    tabelContainer.style.display = 'none';
                    tabelTitle.style.display = 'none';
                }
            }
        }
    } catch(err) { console.error("Gagal mengambil data Kinerja:", err); }
}

document.addEventListener('DOMContentLoaded', function () {
    
    // Initial KPI Number Ticker Entrance
    animateValue('valRataRataCapaian', 0, <?= round($rataRataCapaian, 2) ?>, 850, '', 2);
    animateValue('valTotalIndikator', 0, <?= (int)$totalIndikator ?>, 850);

    // Cleanup Old Charts
    for (let key in window.userDashboardCharts) {
        if (window.userDashboardCharts[key]) {
            window.userDashboardCharts[key].destroy();
            delete window.userDashboardCharts[key];
        }
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

    const tabElements = document.querySelectorAll('#prodiTab button[data-bs-toggle="tab"]');
    tabElements.forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => {
            const target = event.target.getAttribute('data-bs-target');
            if (target) {
                localStorage.setItem(activeTabKey, target);
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

    // --- DATA ECC ---
    const prodiData = <?= json_encode($prodiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
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
                                window.location.href = `<?= site_url('ecc/detailStandar') ?>/${labelId}/${prodi}/${tahun}?from=user`;
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
                labels: <?= json_encode($lineChartLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [
                    {
                        label: 'Nilai Rata-rata',
                        data: <?= json_encode($lineChartRealisasiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
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
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart'
                },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'system-ui' } } }
                },
                scales: {
                    y: { beginAtZero: true, suggestedMax: 100, grace: '8%', grid: { borderDash: [2, 4], color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>