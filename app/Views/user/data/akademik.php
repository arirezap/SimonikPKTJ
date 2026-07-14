<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Data Akademik<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Data Akademik') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h3 class="card-title"><?= number_format($jumlah_taruna_aktif) ?></h3>
                <p class="card-text">Taruna Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h3 class="card-title"><?= esc($ipk_rata_rata) ?></h3>
                <p class="card-text">IPK Rata-rata</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h3 class="card-title"><?= number_format($jumlah_lulusan_tahun_ini) ?></h3>
                <p class="card-text">Lulusan Tahun Ini</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-bg-info">
            <div class="card-body text-center">
                <h3 class="card-title"><?= number_format($jumlah_dosen) ?></h3>
                <p class="card-text">Jumlah Dosen</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Grafik Tren Penerimaan Taruna</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Area untuk menampilkan grafik data akademik.</p>
        <!-- Canvas untuk Chart.js bisa ditambahkan di sini -->
    </div>
</div>
<?= $this->endSection() ?>
