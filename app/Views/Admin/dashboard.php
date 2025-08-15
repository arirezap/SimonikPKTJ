<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Dashboard Administrator</h1>

<!-- Baris Kartu Statistik Agregat -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-info shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title"><?= esc($totalIndikator) ?></h5>
                <p class="card-text">Total Indikator Kinerja (<?= esc($tahun_sekarang) ?>)</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title"><?= round($rataRataCapaianGlobal, 2) ?>%</h5>
                <p class="card-text">Rata-rata Capaian Kinerja Global</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Grafik Perbandingan Kinerja Tim/Unit/Pokja -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h5>Perbandingan Capaian Kinerja per Tim/Unit/Pokja (%)</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($chartLabels)): ?>
                    <canvas id="userPerformanceChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada data kinerja dari Tim/Unit/Pokja untuk ditampilkan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Grafik Distribusi Kinerja -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5>Distribusi Kinerja Tim/Unit/Pokja</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <?php if (!empty($chartLabels)): ?>
                    <canvas id="performanceDistributionChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">Data distribusi akan muncul di sini.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rincian Kinerja Tim/Unit/Pokja -->
<div class="card">
    <div class="card-header">
        <h5>Rincian Kinerja Tim/Unit/Pokja</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Tim/Unit/Pokja</th>
                        <th class="text-center">Indikator</th>
                        <th class="text-center">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($kinerja_per_user as $kinerja): ?>
                    <tr>
                        <td><?= esc($kinerja['nama']) ?></td>
                        <td class="text-center"><?= esc($kinerja['jumlah_indikator']) ?></td>
                        <td class="text-center fw-bold"><?= round($kinerja['persentase_capaian'], 2) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($chartLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Grafik Batang Horizontal
    const ctxBar = document.getElementById('userPerformanceChart');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Persentase Capaian',
                data: <?= json_encode($chartData); ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { callback: value => value + "%" } } },
            plugins: { legend: { display: false } }
        }
    });

    // Grafik Donat Baru
    const ctxDoughnut = document.getElementById('performanceDistributionChart');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($distribusiLabels); ?>,
            datasets: [{
                label: 'Jumlah Tim/Unit/Pokja',
                data: <?= json_encode($distribusiData); ?>,
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',  // Success
                    'rgba(13, 202, 240, 0.7)',   // Info
                    'rgba(255, 193, 7, 0.7)',   // Warning
                    'rgba(220, 53, 69, 0.7)'    // Danger
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
