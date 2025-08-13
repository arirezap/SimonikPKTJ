<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= esc($page_title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Dashboard Administrator</h1>

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card text-bg-primary shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= number_format($totalPengguna); ?></h5>
                    <p class="card-text">Total Pengguna</p>
                </div>
                <i class="bi bi-people-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-bg-success shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title"><?= number_format($jumlahTaruna); ?></h5>
                    <p class="card-text">Jumlah Taruna</p>
                </div>
                <i class="bi bi-person-badge fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-bg-warning shadow-sm">
             <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Rp <?= number_format($totalAnggaran, 0, ',', '.'); ?></h5>
                    <p class="card-text">Total Anggaran</p>
                </div>
                <i class="bi bi-wallet2 fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card text-bg-info shadow-sm">
             <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">1,240</h5>
                    <p class="card-text">Total Lulusan</p>
                </div>
                <i class="bi bi-patch-check-fill fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Aktivitas Sistem</h5>
    </div>
    <div class="card-body">
        <p>Panel manajemen dan log aktivitas akan ditampilkan di sini.</p>
    </div>
</div>

<?= $this->endSection() ?>