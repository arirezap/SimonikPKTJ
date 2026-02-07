<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-body card-profile text-center">
                    <?php 
                        $foto = !empty($user['foto']) ? base_url('assets/uploads/profile/' . $user['foto']) : base_url('assets/uploads/profile/default.png');
                    ?>
                    <img class="img-profile rounded-circle mb-3" src="<?= $foto ?>" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #e3e6f0;">
                    
                    <h5 class="font-weight-bold text-dark mb-1"><?= esc($user['nama_lengkap']) ?></h5>
                    <p class="text-muted mb-1"><?= esc($user['jabatan'] ?? '-') ?></p>
                    <span class="badge bg-primary"><?= esc(ucfirst($user['role'])) ?></span>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Profil</h6>
                </div>
                <div class="card-body">
                    
                    <form action="<?= site_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <h6 class="heading-small text-muted mb-4">Informasi User</h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="heading-small text-muted mb-4">Data Kepegawaian</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap (Beserta Gelar)</label>
                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">NIP</label>
                                    <input type="text" name="nip" class="form-control" value="<?= esc($user['nip']) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Unit Kerja</label>
                                    <input type="text" name="unit" class="form-control" value="<?= esc($user['unit']) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control" value="<?= esc($user['jabatan']) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Pangkat / Golongan</label>
                                    <input type="text" name="pangkat" class="form-control" value="<?= esc($user['pangkat']) ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="heading-small text-muted mb-4">Keamanan & Foto</h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Ganti Password <small class="text-danger">(Kosongkan jika tidak ingin ganti)</small></label>
                                    <input type="password" name="password" class="form-control" placeholder="Password Baru...">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Ganti Foto Profil</label>
                                    <input type="file" name="foto" class="form-control">
                                    <small class="text-muted">Format: jpg, png. Max: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save-fill"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>