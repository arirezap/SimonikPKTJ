<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .nav-tabs .nav-link { border-bottom-width: 0; color: #6c757d; }
    .nav-tabs .nav-link.active { background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; color: #0d6efd; font-weight: bold; }
    .tab-content, .chart-card-wrapper {
        background-color: #ffffff; border: 1px solid #dee2e6; border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); padding: 2rem;
    }
    .chart-container { position: relative; height: 500px; width: 100%; }
    .performance-chart-container { position: relative; min-height: 600px; width: 100%; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h4 class="mb-3">Dashboard ECC (Evidence Command Center)</h4>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php $isFirstTab = true; foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $isFirstTab ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
        </li>
    <?php $isFirstTab = false; endforeach; ?>
</ul>

<div class="tab-content mb-5" id="prodiTabContent">
    <?php $isFirstContent = true; foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade <?= $isFirstContent ? 'show active' : '' ?>" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h5 class="text-center mb-4">Rangkuman Skor LED: <span class="text-primary"><?= esc($prodi['nama_prodi']) ?></span> (Tahun <?= esc($tahun_terpilih) ?>)</h5>
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info text-center">Belum ada data Kategori LED.</div>
                    <?php else: ?>
                        <div class="chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                        <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php $isFirstContent = false; endforeach; ?>
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
                        <option value="<?= $tahun_item; ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : ''; ?>><?= esc($tahun_item); ?></option>
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
                <p class="card-text fs-5">Total Indikator Kinerja</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= round($rataRataCapaianGlobal, 2) ?>%</h2>
                <p class="card-text fs-5">Rata-rata Capaian Global</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Perbandingan Capaian per Tim/Unit/Pokja (%)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartLabels)): ?>
            <div style="position: relative; height: 400px; width: 100%;"><canvas id="userPerformanceChart"></canvas></div>
        <?php else: ?>
            <div class="alert alert-info">Belum ada data kinerja user.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Persentase Capaian per Indikator Kinerja (Target vs Realisasi)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartIndikatorLabels)): ?>
            <div class="performance-chart-container">
                <canvas id="indikatorChart"></canvas>
            </div>
            <p class="text-muted small text-center mt-2">
                * Grafik menampilkan <strong>Persentase Capaian</strong>. Arahkan kursor (hover) pada batang untuk melihat nilai <strong>Target & Realisasi</strong> asli.
            </p>
        <?php else: ?>
            <div class="alert alert-light text-center p-5">
                <i class="bi bi-clipboard-data display-4 text-muted mb-3 d-block"></i>
                Belum ada data Indikator Kinerja untuk ditampilkan.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.pageCharts = window.pageCharts || {};

document.addEventListener('DOMContentLoaded', function () {
    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) { window.pageCharts[key].destroy(); delete window.pageCharts[key]; }
    }

    function splitLabel(label, maxLength = 35) {
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

    const formatLongLabel = (val, ctx) => {
        const label = ctx.chart.data.labels[val];
        return (label.length > 40) ? label.substring(0, 40) + '...' : label;
    };

    // 1. RADAR CHART
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_terpilih) ?>';

    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            window.pageCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: data.chart_labels,
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
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { display: true }, suggestedMin: 0, suggestedMax: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            pointLabels: { display: true, color: '#0d6efd', font: { size: 11, weight: 'bold' }, padding: 10 },
                            ticks: { display: false, stepSize: 25, maxTicksLimit: 5 }
                        }
                    },
                    plugins: { legend: { position: 'top' } },
                    onClick: (e, elements, chart) => {
                        const points = chart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
                        if (points.length) {
                            const idx = points[0].index;
                            if (chart.config.data.labelIds && chart.config.data.labelIds[idx]) {
                                document.body.style.cursor = 'wait'; 
                                window.location.href = `<?= site_url('ecc/detail') ?>/${chart.config.data.labelIds[idx]}/${chart.config.data.prodi}/${chart.config.data.tahun}`;
                            }
                        }
                    }
                }
            });
        }
    }

    // 2. USER PERFORMANCE
    <?php if (!empty($chartLabels)): ?>
    const ctxBarUser = document.getElementById('userPerformanceChart');
    if (ctxBarUser) {
        window.pageCharts['userPerformanceChart'] = new Chart(ctxBarUser, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Capaian (%)', data: <?= json_encode($chartData); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)', borderColor: 'rgba(13, 110, 253, 1)', borderWidth: 1, borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                scales: { x: { beginAtZero: true, max: 100, ticks: { callback: value => value + "%" } } },
                plugins: { legend: { display: false } }
            }
        });
    }
    <?php endif; ?>

    // 3. INDIKATOR CHART (PERSENTASE)
    <?php if (!empty($chartIndikatorLabels)): ?>
    const ctxIndikator = document.getElementById('indikatorChart');
    if (ctxIndikator) {
        const rawLabels = <?= json_encode($chartIndikatorLabels); ?>;
        const metaData = <?= json_encode($chartIndikatorMeta ?? []); ?>;
        
        const wrappedLabels = rawLabels.map(label => splitLabel(label, 45));

        window.pageCharts['indikatorChart'] = new Chart(ctxIndikator, {
            type: 'bar',
            data: {
                labels: wrappedLabels,
                datasets: [
                    {
                        label: 'Capaian (%)',
                        data: <?= json_encode($chartIndikatorPersen ?? []); ?>,
                        backgroundColor: function(context) {
                            const value = context.raw;
                            if (value >= 100) return '#198754'; // Hijau
                            if (value >= 80) return '#0d6efd';  // Biru
                            if (value >= 50) return '#ffc107';  // Kuning
                            return '#dc3545'; // Merah
                        },
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: 'y', 
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { right: 20 } },
                scales: {
                    x: { 
                        beginAtZero: true, 
                        max: 100, // Mengunci skala maks 100% (bisa lebih jika ada overachievement)
                        ticks: { callback: value => value + "%" },
                        grid: { borderDash: [2, 2] }
                    },
                    y: { ticks: { autoSkip: false, font: { size: 11 } } }
                },
                plugins: {
                    legend: { display: false }, // Sembunyikan legend karena hanya 1 dataset
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const label = context[0].label;
                                return Array.isArray(label) ? label.join(' ') : label;
                            },
                            label: function(context) {
                                const idx = context.dataIndex;
                                const item = metaData[idx];
                                const percent = context.formattedValue;
                                
                                // Format angka ribuan
                                const formatNum = (num) => new Intl.NumberFormat('id-ID').format(num);
                                
                                return [
                                    `Capaian: ${percent}%`,
                                    `Target: ${formatNum(item.target)} ${item.satuan}`,
                                    `Realisasi: ${formatNum(item.realisasi)} ${item.satuan}`
                                ];
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>