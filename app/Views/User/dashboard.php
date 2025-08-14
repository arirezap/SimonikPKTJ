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
                <i class="bi bi-graph-up fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-info shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= esc($totalPengguna) ?></h5>
                    <p class="card-text">Total Pengguna Terdaftar</p>
                </div>
                <i class="bi bi-people-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Capaian Target Tahunan -->
<div class="card">
    <div class="card-header">
        <h5>Grafik Capaian Target Tahunan (<?= esc($tahun_sekarang) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartLabels)): ?>
            <canvas id="capaianTahunanChart"></canvas>
        <?php else: ?>
            <div class="alert alert-info">
                Belum ada data Rencana Kinerja untuk tahun ini. Silakan input terlebih dahulu melalui menu <strong>Kinerja &rarr; Input Rencana Kinerja</strong>.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Hanya muat Chart.js jika ada data untuk ditampilkan -->
<?php if (!empty($chartLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('capaianTahunanChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels); ?>,
            datasets: [
                {
                    label: 'Target Tahunan',
                    data: <?= json_encode($chartTargets); ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.6)', // Kuning
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Total Realisasi',
                    data: <?= json_encode($chartRealisasi); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)', // Biru
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            // PERUBAHAN: Mengubah grafik menjadi horizontal
            indexAxis: 'y',
            responsive: true,
            scales: {
                // Sumbu X sekarang menjadi sumbu nilai
                x: {
                    beginAtZero: true,
                    ticks: {
                        // Menambahkan format angka jika diperlukan
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + ' Jt';
                            }
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.x !== null) {
                                label += new Intl.NumberFormat('id-ID').format(context.parsed.x);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
