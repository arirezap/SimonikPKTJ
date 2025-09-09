<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Dashboard Administrator</h1>

<!-- Form Filter Periode -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    <?php foreach ($daftar_tahun as $item): ?>
                        <option value="<?= $item['tahun_anggaran']; ?>" <?= ($tahun_terpilih == $item['tahun_anggaran']) ? 'selected' : ''; ?>>
                            <?= esc($item['tahun_anggaran']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="bulan" class="form-label">Pilih Bulan</label>
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

<!-- Baris Kartu Statistik Agregat -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-info shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title"><?= esc($totalIndikator) ?></h5>
                <p class="card-text">Total Indikator Kinerja (<?= esc($tahun_terpilih) ?>)</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title"><?= round($rataRataCapaianGlobal, 2) ?>%</h5>
                <p class="card-text">Rata-rata Capaian Kinerja</p>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Perbandingan Kinerja Tim/Unit/Pokja -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Perbandingan Capaian per Tim/Unit/Pokja (%)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartLabels)): ?>
            <canvas id="userPerformanceChart" style="min-height: 300px;"></canvas>
        <?php else: ?>
            <div class="alert alert-info">Belum ada data kinerja dari Tim/Unit/Pokja untuk ditampilkan pada periode ini.</div>
        <?php endif; ?>
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
<?php if (!empty($chartLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
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
            scales: { 
                x: { 
                    beginAtZero: true, 
                    ticks: { 
                        callback: value => value + "%" 
                    } 
                } 
            },
            plugins: { 
                legend: { 
                    display: false 
                } 
            }
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
