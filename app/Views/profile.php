<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Profil Saya') ?><?= $this->endSection() ?>

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

        <div class="col-xl-4 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 card-profile text-center d-flex flex-column justify-content-center align-items-center">
                    <?php
                        $foto_path = 'assets/uploads/profile/' . ($user['foto'] ?? '');
                        if (!empty($user['foto']) && file_exists(FCPATH . $foto_path)) {
                            $foto_url = base_url($foto_path);
                        } else {
                            $foto_url = base_url('assets/uploads/profile/default.png');
                        }
                    ?>
                    <div class="position-relative d-inline-block mb-4">
                        <img id="profilePreview" class="img-profile rounded-circle shadow-sm" src="<?= $foto_url ?>" style="width: 160px; height: 160px; object-fit: cover; border: 4px solid #ffffff;">
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-1"><?= esc($user['nama_lengkap']) ?></h5>
                    <p class="text-muted small fw-medium mb-3"><?= esc($user['jabatan'] ?? '-') ?> • <?= esc($user['nip']) ?></p>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                        <?= esc($user['role']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-lines-fill fs-5 text-primary"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark fs-5">Pengaturan Profil</h6>
                </div>
                <div class="card-body p-4">
                    
                    <form action="<?= site_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="hapus_foto" id="hapusFotoFlag" value="0">

                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-primary small mb-3" style="letter-spacing: 0.5px;">Data Kepegawaian</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap (Beserta Gelar)</label>
                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">NIP</label>
                                    <input type="text" name="nip" class="form-control" value="<?= esc($user['nip']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control" value="<?= esc($user['jabatan']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Unit Kerja</label>
                                    <input type="text" name="unit" class="form-control" value="<?= esc($user['unit']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Pangkat / Golongan</label>
                                    <input type="text" name="pangkat" class="form-control" value="<?= esc($user['pangkat']) ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="border-light my-4">

                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-primary small mb-3" style="letter-spacing: 0.5px;">Kredensial & Keamanan</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Ganti Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ganti...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Foto Profil</label>
                                    <div class="input-group">
                                        <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/png, image/jpeg, image/jpg">
                                        <button class="btn btn-outline-danger px-3" type="button" id="hapusFotoBtn" title="Hapus foto dan kembali ke default">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small mt-1">Format: JPG/PNG. Maks: 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top border-light">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                                <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('fotoInput');
    const profilePreview = document.getElementById('profilePreview');
    const hapusFotoBtn = document.getElementById('hapusFotoBtn');
    const hapusFotoFlag = document.getElementById('hapusFotoFlag');
    const defaultFotoUrl = "<?= base_url('assets/uploads/profile/default.png') ?>";

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result; // Tampilkan preview gambar seketika
            }
            reader.readAsDataURL(file);
            // Jika user memilih file, pastikan flag hapus direset
            hapusFotoFlag.value = '0';
        }
    });

    // Event listener untuk tombol hapus foto
    if (hapusFotoBtn) {
        hapusFotoBtn.addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                // Set preview ke gambar default
                profilePreview.src = defaultFotoUrl;
                // Kosongkan input file agar tidak ada file yang terkirim
                fotoInput.value = '';
                // Set flag untuk dikirim ke controller
                hapusFotoFlag.value = '1';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>