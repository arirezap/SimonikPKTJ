<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title ?? 'User Dashboard') ?>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Style untuk memperjelas tab yang aktif */
    .nav-tabs .nav-link {
        border-bottom-width: 0;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        color: #0d6efd;
        font-weight: bold;
    }
    /* Memberi background dan border pada konten tab */
    .tab-content {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem;
    }
    .chart-container {
        position: relative;
        height: 450px;
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<h4 class="mb-3">Dashboard ECC (Evidence Command Center)</h4>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= site_url('user/dashboard') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= esc($tahun_item) ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : '' ?>>
                            <?= esc($tahun_item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <p class="text-muted mb-0">Menampilkan data simulasi skor ECC dan Kinerja Personal untuk tahun <?= esc($tahun_terpilih) ?>.</p>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php $first = true; ?>
    <?php foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $first ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab" aria-controls="content-<?= esc($prodi['id_prodi']) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
                <?= esc($prodi['nama_prodi']) ?>
            </button>
        </li>
        <?php $first = false; ?>
    <?php endforeach; ?>
</ul>

<div class="tab-content mb-4" id="prodiTabContent">
    <?php $first = true; ?>
    <?php foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?> p-3" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel" aria-labelledby="tab-<?= esc($prodi['id_prodi']) ?>">
            
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5>Rangkuman Skor LED: <?= esc($prodi['nama_prodi']) ?> (Tahun <?= esc($tahun_terpilih) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info">Belum ada data Kategori LED yang diisi di Master Data.</div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php $first = false; ?>
    <?php endforeach; ?>
</div>
<hr class="my-4">
<h4 class="mb-4">Dashboard Kinerja Personal (<?= esc(session()->get('nama_lengkap')) ?>)</h4>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= esc($totalIndikator) ?></h5>
                    <p class="card-text">Total Indikator Kinerja <?= esc($tahun_terpilih) ?></p>
                </div>
                <i class="bi bi-list-check fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= round($rataRataCapaian, 2) ?>%</h5>
                    <p class="card-text">Rata-rata Capaian Kinerja</p>
                </div>
                <i class="bi bi-bullseye fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5>Capaian vs Target Tahunan</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($chartLabels)): ?>
                    <canvas id="capaianTahunanChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">
                        Belum ada data Rencana Kinerja untuk tahun ini. Silakan input terlebih dahulu.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5>Tren Progres Kinerja Kumulatif (<?= esc($tahun_terpilih) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($chartLabels)): ?>
                    <canvas id="trenBulananChart"></canvas>
                <?php else: ?>
                     <div class="alert alert-info">
                        Data progres bulanan akan muncul di sini setelah realisasi diisi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Siapkan data dari PHP
    const prodiData = <?= json_encode($prodiData) ?>;

    // Loop melalui setiap data prodi dan buat grafiknya
    for (const [id, data] of Object.entries(prodiData)) {
        const ctx = document.getElementById('radarChart-' + id);
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: data.chart_labels,
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
                    scales: {
                        r: {
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            pointLabels: { display: true, font: { size: 12, weight: 'bold' } },
                            ticks: { display: false }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' }
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
    // --- GRAFIK 1: CAPAIAN TAHUNAN (BAR HORIZONTAL) ---
    const ctxBar = document.getElementById('capaianTahunanChart');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels); ?>,
            datasets: [
                {
                    label: 'Target Tahunan',
                    data: <?= json_encode($chartTargets); ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.6)',
                },
                {
                    label: 'Total Realisasi',
                    data: <?= json_encode($chartRealisasi); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true } },
            plugins: { legend: { position: 'top' } }
        }
    });

    // --- GRAFIK 2: TREN BULANAN (GARIS) ---
    const ctxLine = document.getElementById('trenBulananChart');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?= json_encode($lineChartLabels); ?>,
            datasets: [
                {
                    label: 'Target Kumulatif',
                    data: <?= json_encode($lineChartTargetData); ?>,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.1
                },
                {
                    label: 'Realisasi Kumulatif',
                    data: <?= json_encode($lineChartRealisasiData); ?>,
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top' } }
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>