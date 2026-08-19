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

    <div class="row g-4">
        <!-- Bento 1: Avatar / Profile Overview -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <?php
                        // Logika Inisial Cerdas
                        $rawName = $user['nama_lengkap'] ?? 'Pengguna';
                        $nameParts = explode(',', $rawName);
                        $mainName = trim($nameParts[0]);
                        $nameWords = explode(' ', $mainName);
                        $initials = strtoupper(substr($nameWords[0] ?? 'U', 0, 1));
                        if (count($nameWords) > 1) {
                            $initials .= strtoupper(substr($nameWords[1], 0, 1));
                        }
                        
                        $foto_path = 'assets/uploads/profile/' . ($user['foto'] ?? '');
                        $hasPhoto = (!empty($user['foto']) && file_exists(FCPATH . $foto_path));
                        $foto_url = $hasPhoto ? base_url($foto_path) : '';
                    ?>
                    
                    <div class="position-relative d-inline-block mb-4">
                        <?php if ($hasPhoto): ?>
                            <img id="profilePreview" class="img-profile rounded-circle shadow-sm" src="<?= $foto_url ?>" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                            <div id="profilePreviewInitials" class="bg-primary bg-opacity-10 text-primary rounded-circle align-items-center justify-content-center shadow-sm d-none" style="width: 150px; height: 150px; border: 4px solid #ffffff; font-size: 3.5rem; font-weight: bold; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                                <?= $initials ?>
                            </div>
                        <?php else: ?>
                            <div id="profilePreviewInitials" class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 150px; height: 150px; border: 4px solid #ffffff; font-size: 3.5rem; font-weight: bold; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                                <?= $initials ?>
                            </div>
                            <img id="profilePreview" class="img-profile rounded-circle shadow-sm d-none" src="" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;"><?= esc(ucwords(strtolower($mainName))) ?></h4>
                    <p class="text-muted small fw-medium mb-3"><?= esc($user['jabatan'] ?? '-') ?> • <?= esc($user['nip']) ?></p>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                        <?= esc($user['role']) ?>
                    </span>
                    
                    <!-- Upload section for the avatar -->
                    <div class="w-100 mt-4 pt-4 border-top border-light">
                        <label class="form-label text-muted small fw-bold text-uppercase w-100 text-start">Ganti Foto Profil <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                        <div class="input-group">
                            <input form="profileForm" type="file" name="foto" id="fotoInput" class="form-control" accept="image/png, image/jpeg, image/jpg">
                            <button form="profileForm" class="btn btn-outline-danger px-3" type="button" id="hapusFotoBtn" title="Hapus foto">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                        <div class="form-text small mt-1 text-start">Format: JPG/PNG. Maks: 2MB.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bento 2 & 3: Forms -->
        <div class="col-xl-8 col-lg-7">
            <form id="profileForm" action="<?= site_url('profile/update') ?>" method="post" enctype="multipart/form-data" class="h-100 d-flex flex-column gap-4">
                <?= csrf_field() ?>
                <input type="hidden" name="hapus_foto" id="hapusFotoFlag" value="0">
                
                <!-- Bento 2: Kredensial & Keamanan -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-shield-lock-fill fs-5 text-warning"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark fs-5">Kredensial & Keamanan</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Username</label>
                                <input type="text" name="username" class="form-control bg-light" value="<?= esc($user['username']) ?>" readonly>
                                <div class="form-text small mt-1">Username akun terdaftar dan tidak dapat diubah.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Ganti Password <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti...">
                                <div class="form-text small mt-1">Minimal 6 karakter jika ingin mengganti.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bento 3: Data Kepegawaian & Kontak -->
                <div class="card shadow-sm border-0 rounded-4 flex-grow-1">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-vcard-fill fs-5 text-info"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark fs-5">Informasi Pegawai & Kontak</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap (Beserta Gelar) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">No. HP <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                                <input type="text" name="no_hp" class="form-control" value="<?= esc($user['no_hp'] ?? '') ?>" placeholder="Misal: 081234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">NIP / NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control" value="<?= esc($user['nip']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" value="<?= esc($user['jabatan']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Unit Kerja <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <?php foreach ($unit_kerja_list as $uk): ?>
                                        <option value="<?= esc($uk['nama_unit']) ?>" <?= ($user['unit'] == $uk['nama_unit']) ? 'selected' : '' ?>>
                                            <?= esc($uk['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Atasan Langsung <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                                <select name="atasan_id" class="form-select select2">
                                    <option value="">-- Pilih Atasan Langsung --</option>
                                    <?php foreach ($potential_bosses as $boss): ?>
                                        <option value="<?= $boss['id'] ?>" <?= ($user['atasan_id'] == $boss['id']) ? 'selected' : '' ?>>
                                            <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Pangkat / Golongan <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                                <input type="text" name="pangkat" class="form-control" value="<?= esc($user['pangkat']) ?>" placeholder="Misal: Penata Muda / III/a">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top border-light">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2">
                                <i class="bi bi-save2-fill"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('fotoInput');
    const profilePreview = document.getElementById('profilePreview');
    const profilePreviewInitials = document.getElementById('profilePreviewInitials');
    const hapusFotoBtn = document.getElementById('hapusFotoBtn');
    const hapusFotoFlag = document.getElementById('hapusFotoFlag');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
                profilePreview.classList.remove('d-none');
                profilePreviewInitials.classList.add('d-none');
                profilePreviewInitials.classList.remove('d-flex');
                hapusFotoFlag.value = "0";
            }
            reader.readAsDataURL(file);
        }
    });
    hapusFotoBtn.addEventListener('click', function() {
        const doHapusFoto = function() {
            fotoInput.value = "";
            profilePreview.src = "";
            profilePreview.classList.add('d-none');
            profilePreviewInitials.classList.remove('d-none');
            profilePreviewInitials.classList.add('d-flex');
            hapusFotoFlag.value = "1";
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Foto Profil?',
                text: 'Foto profil akan dihapus dan digantikan dengan inisial nama.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus Foto',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    doHapusFoto();
                }
            });
        } else if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            doHapusFoto();
        }
    });

    // Inisialisasi Select2 jika tersedia
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Atasan Langsung --'
        });
    }
});
</script>
<?= $this->endSection() ?>