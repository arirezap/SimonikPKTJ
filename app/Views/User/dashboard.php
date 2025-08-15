<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title ?? 'User Dashboard') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Selamat datang, <?= esc(session()->get('nama_lengkap')) ?>!</h1>

<!-- Baris untuk Kartu Statistik (KPI Cards) -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= esc($totalIndikator) ?></h5>
                    <p class="card-text">Total Indikator Kinerja <?= esc($tahun_sekarang) ?></p>
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
    <!-- Grafik 1: Capaian Target Tahunan per Indikator -->
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
    
    <!-- Grafik 2: Tren Progres Kinerja Kumulatif Bulanan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5>Tren Progres Kinerja Kumulatif (<?= esc($tahun_sekarang) ?>)</h5>
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
<?php if (!empty($chartLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
