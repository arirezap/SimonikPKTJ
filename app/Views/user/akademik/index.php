<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Rangkuman Akademik<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Rangkuman Akademik') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h3><?= $total_matkul ?></h3>
                <p>Total Mata Kuliah</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h3><?= $total_dosen ?></h3>
                <p>Total Dosen Pengampu</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h3><?= $total_ruangan ?></h3>
                <p>Total Ruangan Tersedia</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Distribusi Mahasiswa per Program Studi</h5>
    </div>
    <div class="card-body">
        <canvas id="prodiChart"></canvas>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('prodiChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: <?= json_encode($chart_data); ?>,
                backgroundColor: ['rgba(13, 110, 253, 0.7)', 'rgba(25, 135, 84, 0.7)', 'rgba(255, 193, 7, 0.7)'],
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});
</script>
<?= $this->endSection() ?>
