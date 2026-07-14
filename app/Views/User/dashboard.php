<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title ?? 'User Dashboard') ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Tab Style Standard */
    .nav-tabs .nav-link { border-bottom-width: 0; color: #6c757d; }
    .nav-tabs .nav-link.active { background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; color: #0d6efd; font-weight: bold; }
    
    /* Card Effect untuk Konten Tab */
    .tab-content { 
        background-color: #ffffff; 
        border: 1px solid #dee2e6; 
        border-top: 0; 
        border-radius: 0 0.375rem 0.375rem 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); /* Shadow halus */
        padding: 2rem;
    }
    
    .radar-chart-container { position: relative; height: 500px; width: 100%; }
    .performance-chart-container { position: relative; height: 500px; width: 100%; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h4 class="mb-3">Dashboard ECC (Evidence Command Center)</h4>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?= site_url('dashboard') ?>" class="row g-3 align-items-end">
            <div class="col-12">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= esc($tahun_item) ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : '' ?>><?= esc($tahun_item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content mb-5" id="prodiTabContent">
    <?php foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h6 class="text-center mb-4">Rangkuman Skor LED: <span class="text-primary"><?= esc($prodi['nama_prodi']) ?></span> (Tahun <?= esc($tahun_terpilih) ?>)</h6>
                    
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info text-center">Belum ada data Kategori LED untuk tahun ini.</div>
                    <?php else: ?>
                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                        <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isset($isSuper) && $isSuper): ?>
<div class="d-flex align-items-center mb-4 mt-5">
    <h4 class="mb-0 me-3">Kinerja Seluruh Pegawai (Per Unit Kerja)</h4>
    <span class="badge bg-primary bg-opacity-10 text-primary fs-6">Direktur / Wadir</span>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-5">
    <div class="card-body p-4">
        <?php if (empty($chartPegawaiUnitLabels)): ?>
            <div class="alert alert-info border-0 shadow-sm"><i class="bi bi-info-circle me-2"></i> Belum ada data rekap kinerja bawahan untuk bulan ini.</div>
        <?php else: ?>
            <div class="performance-chart-container" style="height: 400px;">
                <canvas id="unitPerformanceChart"></canvas>
            </div>
            <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i> Klik pada grafik batang untuk melihat detail anggota unit kerja.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-labelledby="unitDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="unitDetailModalLabel">Detail Pegawai: <span id="modalUnitName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary small fw-bold text-uppercase rounded-start">Nama Pegawai</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center">Laporan Dinilai</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center rounded-end">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="d-flex align-items-center mb-4">
    <h4 class="mb-0 me-3">Kinerja Personal</h4>
    <span class="badge bg-secondary fs-6"><?= esc(session()->get('nama_lengkap')) ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-primary shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= esc($totalIndikator) ?></h2>
                <p class="card-text fs-5">Total Indikator Kinerja</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= round($rataRataCapaian, 2) ?>%</h2>
                <p class="card-text fs-5">Rata-rata Capaian</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.userDashboardCharts = window.userDashboardCharts || {};

document.addEventListener('DOMContentLoaded', function () {
    
    // Cleanup Old Charts
    for (let key in window.userDashboardCharts) {
        if (window.userDashboardCharts[key]) {
            window.userDashboardCharts[key].destroy();
            delete window.userDashboardCharts[key];
        }
    }

    // --- SCRIPT BARU UNTUK MENGINGAT TAB AKTIF ---
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
    function splitLabel(label, maxLength = 25) {
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

    // --- DATA ---
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_terpilih) ?>';

    // --- EVENT KLIK LABEL ---
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

    // --- INIT RADAR CHART ---
    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            const existing = Chart.getChart(ctx);
            if (existing) existing.destroy();

            // Apply Word Wrap
            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 25));

            window.userDashboardCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels,
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi, 
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi, data: data.chart_data, fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)', borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)', pointBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    layout: { padding: 20 },
                    scales: { 
                        r: { 
                            angleLines: { display: true },
                            min: 0, max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' }, 
                            pointLabels: { 
                                display: true, 
                                color: '#0d6efd', 
                                font: { size: 11, weight: 'bold' }, 
                                backdropPadding: 4,
                                padding: 15, 
                            }, 
                            ticks: { 
                                display: false, 
                                stepSize: 33.3333,
                            } 
                        } 
                    },
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
});

<?php if (isset($isSuper) && $isSuper && !empty($chartPegawaiUnitLabels)): ?>
const ctxUnit = document.getElementById('unitPerformanceChart');
if (ctxUnit) {
    const unitStats = <?= json_encode($unitStats ?? []) ?>;
    const unitLabels = <?= json_encode($chartPegawaiUnitLabels) ?>;
    const unitData = <?= json_encode($chartPegawaiUnitData) ?>;
    
    const unitChart = new Chart(ctxUnit, {
        type: 'bar',
        data: {
            labels: unitLabels,
            datasets: [{
                label: 'Rata-Rata Kinerja',
                data: unitData,
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: '#0d6efd',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rata-Rata: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { borderDash: [2, 4], color: '#e9ecef' }
                },
                x: {
                    grid: { display: false }
                }
            },
            onClick: (e, elements) => {
                if (elements.length > 0) {
                    const idx = elements[0].index;
                    const selectedUnit = unitLabels[idx];
                    const details = unitStats[selectedUnit].anggota;
                    
                    document.getElementById('modalUnitName').innerText = selectedUnit;
                    
                    let tbody = '';
                    if(details && details.length > 0) {
                        details.forEach(item => {
                            let badgeClass = 'bg-success';
                            if(item.rata_rata < 60) badgeClass = 'bg-danger';
                            else if(item.rata_rata < 75) badgeClass = 'bg-warning text-dark';
                            
                            tbody += `
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">${item.nama}</div>
                                        <div class="small text-muted">${item.jabatan}</div>
                                    </td>
                                    <td class="text-center">${item.dinilai} / ${item.total_laporan}</td>
                                    <td class="text-center">
                                        <span class="badge ${badgeClass} fs-6">${item.rata_rata}</span>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody = `<tr><td colspan="3" class="text-center text-muted">Data kosong</td></tr>`;
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
</script>
<?= $this->endSection() ?>