<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Profil Saya') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1 class="mb-4">Profil Saya</h1>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Kolom Kiri: Kartu Profil Statis -->
    <div class="col-lg-4">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <?php
                    $foto_profil = 'default.png';
                    if (!empty($user['foto']) && file_exists(FCPATH . 'assets/uploads/profile/' . $user['foto'])) {
                        $foto_profil = 'profile/' . $user['foto'];
                    }
                ?>
                <img src="<?= base_url('assets/uploads/' . $foto_profil) ?>" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h4 class="card-title mb-1"><?= esc($user['nama_lengkap']); ?></h4>
                <p class="text-muted mb-2">@<?= esc($user['username']); ?></p>
                <span class="badge bg-primary text-capitalize"><?= esc($user['role']); ?></span>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Edit -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Edit Informasi Profil</h5>
            </div>
            <div class="card-body">
                <form action="<?= site_url('profile') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc($user['email']); ?>" required>
                    </div>
                    
                    <hr class="my-4">
                    <h6>Ubah Kata Sandi</h6>
                    <p class="text-muted small">Kosongkan kedua kolom di bawah jika Anda tidak ingin mengubah kata sandi.</p>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi Baru</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save-fill me-2"></i>Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
