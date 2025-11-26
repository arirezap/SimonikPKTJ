<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Style Tab Navigasi */
    .nav-tabs .nav-link {
        border-bottom-width: 0;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        color: #0d6efd;
        font-weight: bold;
    }
    
    /* Style Card untuk Konten Tab (Background Putih & Shadow) */
    .tab-content {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); /* Shadow halus */
        padding: 2rem;
    }

    /* Tinggi container diperbesar agar label panjang muat */
    .chart-container {
        position: relative;
        height: 500px; 
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h4 class="mb-3">Dashboard ECC (Evidence Command Center)</h4>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php 
    $isFirstTab = true; 
    foreach($prodiData as $prodi): 
    ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $isFirstTab ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab" aria-controls="content-<?= esc($prodi['id_prodi']) ?>" aria-selected="<?= $isFirstTab ? 'true' : 'false' ?>">
                <?= esc($prodi['nama_prodi']) ?>
            </button>
        </li>
    <?php 
        $isFirstTab = false; 
    endforeach; 
    ?>
</ul>

<div class="tab-content mb-5" id="prodiTabContent">
    <?php 
    $isFirstContent = true; 
    foreach($prodiData as $prodi): 
    ?>
        <div class="tab-pane fade <?= $isFirstContent ? 'show active' : '' ?>" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel" aria-labelledby="tab-<?= esc($prodi['id_prodi']) ?>">
            
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h5 class="text-center mb-4">Rangkuman Skor LED: <span class="text-primary"><?= esc($prodi['nama_prodi']) ?></span> (Tahun <?= esc($tahun_terpilih) ?>)</h5>
                    
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info text-center">Belum ada data Kategori LED yang diisi di Master Data.</div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas>
                        </div>
                        <p class="text-center text-muted small mt-3">
                            <i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat rincian detailnya.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    <?php 
        $isFirstContent = false; 
    endforeach; 
    ?>
</div>

<hr class="my-5 border-2">
<h3 class="mb-4">Dashboard Monitoring Kinerja Global</h3>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?= site_url('admin/dashboard') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= $tahun_item; ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : ''; ?>>
                            <?= esc($tahun_item); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="bulan" class="form-label fw-bold">Pilih Bulan</label>
                <select class="form-select" id="bulan" name="bulan">
                    <option value="all" <?= ($bulan_terpilih === 'all' || !$bulan_terpilih) ? 'selected' : '' ?>>Semua Bulan (Tahunan)</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i; ?>" <?= ($bulan_terpilih == $i) ? 'selected' : ''; ?>><?= bulan_indo($i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Tampilkan Data</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-info shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= esc($totalIndikator) ?></h2>
                <p class="card-text fs-5">Total Indikator Kinerja (<?= esc($tahun_terpilih) ?>)</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= round($rataRataCapaianGlobal, 2) ?>%</h2>
                <p class="card-text fs-5">Rata-rata Capaian Kinerja</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary">Perbandingan Capaian per Tim/Unit/Pokja (%)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartLabels)): ?>
            <div style="position: relative; height: 400px; width: 100%;">
                <canvas id="userPerformanceChart"></canvas>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Belum ada data kinerja dari Tim/Unit/Pokja untuk ditampilkan pada periode ini.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Rincian Kinerja Tim/Unit/Pokja</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Tim/Unit/Pokja</th>
                        <th class="text-center">Indikator</th>
                        <th class="text-center">Target Periode Ini</th>
                        <th class="text-center">Realisasi Periode Ini</th>
                        <th class="text-center">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kinerja_per_user)): ?>
                        <?php foreach($kinerja_per_user as $kinerja): ?>
                        <tr>
                            <td><?= esc($kinerja['nama']) ?></td>
                            <td class="text-center"><?= esc($kinerja['jumlah_indikator']) ?></td>
                            <td class="text-center"><?= number_format($kinerja['total_target'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= number_format($kinerja['total_realisasi'], 0, ',', '.') ?></td>
                            <td class="text-center fw-bold"><?= round($kinerja['persentase_capaian'], 2) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center p-4">Tidak ada data untuk ditampilkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global State Management for Charts
window.pageCharts = window.pageCharts || {};

document.addEventListener('DOMContentLoaded', function () {
    // Cleanup old charts
    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) {
            window.pageCharts[key].destroy();
            delete window.pageCharts[key];
        }
    }

    // --- Helper: Word Wrap Function ---
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

    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_terpilih) ?>';

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
            if (distance < minDistance) {
                minDistance = distance;
                closestLabelIndex = i;
            }
        }
        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                const itemWidth = item.options?.bounds?.width || 80; 
                if (minDistance < (itemWidth / 2) + 20) return closestLabelIndex;
            } catch (e) {
                if (minDistance < 50) return closestLabelIndex;
            }
        }
        return null;
    }

    // --- INIT RADAR CHART ---
    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        
        if (ctx) {
            const existingChart = Chart.getChart(ctx);
            if (existingChart) existingChart.destroy();

            // Terapkan Word Wrap pada Label
            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 25));

            window.pageCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels, // Gunakan label yang sudah di-wrap
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi,
                        data: data.chart_data,
                        fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(13, 110, 253, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 20 }, // Tambah padding agar label tidak terpotong
                    scales: {
                        r: {
                            angleLines: { display: true },
                            min: 0,      // Kunci Min 0
                            max: 100,    // Kunci Max 100
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)', 
                            },
                            pointLabels: { 
                                display: true, 
                                color: '#0d6efd', 
                                hoverColor: '#0a58ca',
                                font: { size: 11, weight: 'bold' },
                                hoverFont: { weight: 'bolder' },
                                backdropPadding: 4,
                                padding: 15, 
                                onHover: (event) => {
                                    const chart = event.chart;
                                    const index = getClickedLabel(event, chart);
                                    event.native.target.style.cursor = (index !== null) ? 'pointer' : 'default';
                                },
                                onLeave: (event) => { event.native.target.style.cursor = 'default'; }
                            },
                            ticks: { 
                                display: false, 
                                stepSize: 33.3333, // Bagi 100 jadi 3 bagian
                                maxTicksLimit: 5 
                            }
                        }
                    },
                    plugins: { 
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    let label = tooltipItem.dataset.label || '';
                                    if (label) label += ': ';
                                    if (tooltipItem.formattedValue !== null) label += tooltipItem.formattedValue;
                                    return label;
                                },
                                afterLabel: function(tooltipItem) {
                                    const score = tooltipItem.parsed.r;
                                    if (score === 0) {
                                        return ['Skor 0 karena item standar ini:', '- Belum disetujui Kabag/Wadir', '- Belum dinilai/disimulasi', 'Klik label untuk detail.'];
                                    }
                                    return ''; 
                                }
                            }
                        }
                    },
                    onClick: (e, elements, chart) => {
                        const clickedLabelIndex = getClickedLabel(e, chart);
                        if (clickedLabelIndex !== null) {
                            if (chart.config.data.labelIds && chart.config.data.labelIds[clickedLabelIndex]) {
                                const labelId = chart.config.data.labelIds[clickedLabelIndex];
                                const prodi = chart.config.data.prodi;
                                const tahun = chart.config.data.tahun;
                                document.body.style.cursor = 'wait'; 
                                window.location.href = `<?= site_url('ecc/detail') ?>/${labelId}/${prodi}/${tahun}`;
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>

<?php if (!empty($chartLabels)): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctxBar = document.getElementById('userPerformanceChart');
    if (ctxBar) { 
        const existingBarChart = Chart.getChart(ctxBar);
        if (existingBarChart) existingBarChart.destroy();

        // Helper format label panjang untuk bar chart
        const formatLongLabel = (val, ctx) => {
            const label = ctx.chart.data.labels[val];
            return (label.length > 40) ? label.substring(0, 40) + '...' : label;
        };

        window.pageCharts['userPerformanceChart'] = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Persentase Capaian',
                    data: <?= json_encode($chartData); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    x: { 
                        beginAtZero: true,
                        max: 100, 
                        ticks: { callback: value => value + "%" } 
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                            callback: function(val) { return formatLongLabel(val, {chart: this.chart}); }
                        }
                    }
                },
                plugins: { 
                    legend: { display: false } 
                }
            }
        });
    }
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>