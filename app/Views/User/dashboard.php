<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title ?? 'User Dashboard') ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .nav-tabs .nav-link { border-bottom-width: 0; color: #6c757d; }
    .nav-tabs .nav-link.active { background-color: #f8f9fa; border-color: #dee2e6 #dee2e6 #f8f9fa; color: #0d6efd; font-weight: bold; }
    .tab-content { background-color: #f8f9fa; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0.375rem 0.375rem 0.375rem; }
    
    .radar-chart-container { position: relative; height: 450px; width: 100%; }
    /* Tinggi container diperbesar agar grafik lebih mudah dibaca */
    .performance-chart-container { position: relative; height: 500px; width: 100%; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h4 class="mb-3">Dashboard ECC (Evidence Command Center)</h4>
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?= site_url('user/dashboard') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= esc($tahun_item) ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : '' ?>><?= esc($tahun_item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8"><p class="text-muted mb-0 pt-2">Menampilkan data tahun <strong><?= esc($tahun_terpilih) ?></strong>.</p></div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php $first = true; foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $first ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
        </li>
    <?php $first = false; endforeach; ?>
</ul>
<div class="tab-content mb-5 shadow-sm" id="prodiTabContent">
    <?php $first = true; foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?> p-4" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h5 class="text-center mb-4">Skor LED: <span class="text-primary"><?= esc($prodi['nama_prodi']) ?></span></h5>
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info text-center">Belum ada data Kategori LED.</div>
                    <?php else: ?>
                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php $first = false; endforeach; ?>
</div>

<hr class="my-5 border-2">

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

<div class="card shadow border-0 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-diagram-3-fill me-2"></i>Capaian per Sasaran Program</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartSasaranLabels)): ?>
            <div class="performance-chart-container">
                <canvas id="chartSasaran"></canvas>
            </div>
            <p class="text-muted small text-center mt-2">* Menampilkan rata-rata persentase capaian dari indikator-indikator pada sasaran tersebut.</p>
        <?php else: ?>
            <div class="alert alert-light text-center p-5">Belum ada data Sasaran Program.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow border-0 mb-5">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Target vs Realisasi per Indikator Kinerja</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartIndikatorLabels)): ?>
            <div class="performance-chart-container">
                <canvas id="chartIndikator"></canvas>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center p-5">Belum ada data Indikator Kinerja.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-success"><i class="bi bi-activity me-2"></i>Tren Progres Kumulatif (<?= esc($tahun_terpilih) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartIndikatorLabels)): ?>
            <div class="performance-chart-container">
                <canvas id="chartTren"></canvas>
            </div>
        <?php else: ?>
             <div class="alert alert-light text-center p-5">Belum ada data progres.</div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- RADAR CHART CONFIG ---
    const prodiData = <?= json_encode($prodiData) ?>;
    function getClickedLabel(clickEvent, chart) {
        const r = chart.scales.r;
        const pointLabelItems = r._pointLabelItems; 
        if (!pointLabelItems || pointLabelItems.length === 0) return null;
        const { x, y } = clickEvent;
        let closestLabelIndex = -1; let minDistance = Infinity;
        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            const distance = Math.sqrt(Math.pow(x - item.x, 2) + Math.pow(y - item.y, 2));
            if (distance < minDistance) { minDistance = distance; closestLabelIndex = i; }
        }
        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                if (minDistance < (item.options.bounds.width / 2) + 10) return closestLabelIndex;
            } catch (e) { if (minDistance < 30) return closestLabelIndex; }
        }
        return null;
    }

    for (const [id, data] of Object.entries(prodiData)) {
        const ctx = document.getElementById('radarChart-' + id);
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: data.chart_labels, labelIds: data.chart_label_ids, prodi: data.id_prodi, tahun: '<?= $tahun_terpilih ?>',
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi, data: data.chart_data, fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)', borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)', pointBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { r: { angleLines: { display: true }, suggestedMin: 0, suggestedMax: 100, grid: { color: 'rgba(0, 0, 0, 0.1)' }, pointLabels: { display: true, color: '#0d6efd', font: { size: 12, weight: 'bold' }, padding: 10 }, ticks: { display: false, stepSize: 25, maxTicksLimit: 5 } } },
                    onClick: (e, elements, chart) => {
                        const idx = getClickedLabel(e, chart);
                        if (idx !== null) window.location.href = `<?= site_url('ecc/detail') ?>/${chart.config.data.labelIds[idx]}/${chart.config.data.prodi}/${chart.config.data.tahun}`;
                    }
                }
            });
        }
    }

    // --- CONFIG GRAFIK KINERJA (VERTIKAL) ---
    
    // Helper: Potong teks jika terlalu panjang
    const formatLongLabel = (val, ctx) => {
        const label = ctx.chart.data.labels[val];
        // Batas karakter diperpanjang ke 50 karena grafik lebar
        return (label.length > 50) ? label.substring(0, 50) + '...' : label;
    };

    <?php if (!empty($chartSasaranLabels)): ?>
    // 1. CHART SASARAN (Label dari Master Sasaran)
    new Chart(document.getElementById('chartSasaran'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartSasaranLabels); ?>,
            datasets: [{
                label: 'Rata-rata Capaian (%)',
                data: <?= json_encode($chartSasaranData); ?>,
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
                borderColor: 'rgba(25, 135, 84, 1)', borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'x', // Vertikal
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { bottom: 10 } },
            scales: {
                y: { beginAtZero: true, max: 100, title: { display: true, text: 'Persentase (%)', font: {weight: 'bold'} } },
                x: { 
                    ticks: { 
                        autoSkip: false, maxRotation: 45, minRotation: 25, font: {size: 11},
                        callback: function(val, index) { return formatLongLabel(val, {chart: this.chart}); } 
                    } 
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { title: (ctx) => ctx[0].label } }
            }
        }
    });
    <?php endif; ?>

    <?php if (!empty($chartIndikatorLabels)): ?>
    // 2. CHART INDIKATOR (Label dari Master Indikator)
    new Chart(document.getElementById('chartIndikator'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartIndikatorLabels); ?>,
            datasets: [
                {
                    label: 'Total Target',
                    data: <?= json_encode($chartIndikatorTargets); ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)', borderWidth: 1
                },
                {
                    label: 'Total Realisasi',
                    data: <?= json_encode($chartIndikatorRealisasi); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)', borderWidth: 1
                }
            ]
        },
        options: {
            indexAxis: 'x', // Vertikal
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { bottom: 10 } },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Nilai / Jumlah', font: {weight: 'bold'} } },
                x: { 
                    ticks: { 
                        autoSkip: false, maxRotation: 45, minRotation: 25, font: {size: 11},
                        callback: function(val, index) { return formatLongLabel(val, {chart: this.chart}); } 
                    } 
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { title: (ctx) => ctx[0].label } }
            }
        }
    });

    // 3. CHART TREN
    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: <?= json_encode($lineChartLabels); ?>,
            datasets: [
                {
                    label: 'Target Kumulatif',
                    data: <?= json_encode($lineChartTargetData); ?>,
                    borderColor: 'rgba(255, 193, 7, 1)', backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: true, tension: 0.3
                },
                {
                    label: 'Realisasi Kumulatif',
                    data: <?= json_encode($lineChartRealisasiData); ?>,
                    borderColor: 'rgba(13, 110, 253, 1)', backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true, tension: 0.3
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } },
            plugins: { legend: { position: 'top' } }
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>