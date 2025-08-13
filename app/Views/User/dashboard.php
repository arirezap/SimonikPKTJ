<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Selamat datang, <?= esc(session()->get('nama_lengkap')) ?>!</h1>

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Jumlah Taruna Aktif</h6>
                <h4 class="card-title"><?= number_format($jumlahTaruna); ?> Orang</h4>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Serapan Lulusan</h6>
                <h4 class="card-title"><?= $persentaseSerapanLulusan; ?>%</h4>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Realisasi Pendapatan</h6>
                <h4 class="card-title">Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Realisasi Anggaran</h6>
                <h4 class="card-title">Rp <?= number_format($realisasiAnggaran, 0, ',', '.'); ?></h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Realisasi Anggaran Tahunan (Juta Rupiah)</h5>
            </div>
            <div class="card-body">
                <canvas id="anggaranChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5>Distribusi Serapan Lulusan</h5>
            </div>
            <div class="card-body">
                 <canvas id="serapanChart"></canvas>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // 1. Grafik Realisasi Anggaran
    const ctxAnggaran = document.getElementById('anggaranChart');
    new Chart(ctxAnggaran, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsAnggaran); ?>,
            datasets: [
                {
                    label: 'Target',
                    data: <?= json_encode($dataTargetAnggaran); ?>,
                    backgroundColor: 'rgba(200, 200, 200, 0.6)',
                },
                {
                    label: 'Realisasi',
                    data: <?= json_encode($dataRealisasiAnggaran); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 2. Grafik Serapan Lulusan
    const ctxSerapan = document.getElementById('serapanChart');
    new Chart(ctxSerapan, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labelsSerapan); ?>,
            datasets: [{
                label: 'Persentase',
                data: <?= json_encode($dataSerapan); ?>,
            }]
        },
        options: {
            responsive: true,
        }
    });
});
</script>
<?= $this->endSection() ?>