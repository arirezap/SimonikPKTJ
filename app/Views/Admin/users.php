<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Kelola Pengguna<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .user-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table-users thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        text-transform: uppercase;
        font-size: 0.8rem;
        color: #6c757d;
    }
    .avatar-initial {
        width: 35px;
        height: 35px;
        background-color: #e9ecef;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        margin-right: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Daftar Pengguna</h3>
        <p class="text-muted mb-0">Kelola akun dan hak akses pengguna sistem.</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-lg me-2"></i> Tambah Pengguna
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error') && !session()->getFlashdata('show_modal')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card user-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-users mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="5%">No</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="20%">Username</th>
                        <th width="25%">Email</th>
                        <th width="15%">Role</th>
                        <th class="text-center pe-4" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php $no = 1; foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial">
                                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-bold text-dark"><?= esc($user['nama_lengkap']) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= esc($user['username']) ?></td>
                                <td class="text-muted small"><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                        $role = esc($user['role']);
                                        $badge_class = 'bg-secondary';
                                        $icon_class = 'bi-person';
                                        
                                        switch($role) {
                                            case 'admin': $badge_class = 'bg-danger'; $icon_class = 'bi-shield-lock-fill'; break;
                                            case 'manajemen': $badge_class = 'bg-primary'; $icon_class = 'bi-briefcase-fill'; break;
                                            case 'spm': $badge_class = 'bg-dark'; $icon_class = 'bi-building-fill'; break;
                                            case 'kabag_aak': 
                                            case 'kabag_kuk': $badge_class = 'bg-warning text-dark'; $icon_class = 'bi-person-lines-fill'; break;
                                            case 'aak': $badge_class = 'bg-success'; $icon_class = 'bi-book-half'; break;
                                            case 'kuk': $badge_class = 'bg-info text-dark'; $icon_class = 'bi-currency-dollar'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $badge_class ?> rounded-pill fw-normal px-3 py-2">
                                        <i class="bi <?= $icon_class ?> me-1"></i> <?= strtoupper(str_replace('_', ' ', $role)) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-light btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#editUserModal-<?= $user['id'] ?>" title="Edit">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>
                                        <?php if ($user['id'] != session()->get('user_id')): ?>
                                        <button type="button" class="btn btn-light btn-sm text-danger" onclick="confirmDelete(<?= $user['id'] ?>, '<?= esc($user['nama_lengkap']) ?>')" title="Hapus">
                                            <i class="bi bi-trash fs-6"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 d-block mb-2 opacity-50"></i>
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/users/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'addUserModal'): ?>
                        <div class="alert alert-danger small py-2">Periksa kembali inputan Anda.</div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <option value="admin">Admin</option>
                                <option value="manajemen">Manajemen</option>
                                <option value="spm">SPM</option>
                                <option value="kabag_aak">Kabag AAK</option>
                                <option value="kabag_kuk">Kabag KUK</option>
                                <option value="aak">Staf AAK</option>
                                <option value="kuk">Staf KUK</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password_new" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="password_new"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" name="konfirmasi_password" id="konf_password_new" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="konf_password_new"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($users)): foreach ($users as $user): ?>
<div class="modal fade" id="editUserModal-<?= $user['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/users/update/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Username</label>
                            <input type="text" name="username" class="form-control bg-light" value="<?= esc($user['username']) ?>" readonly title="Username tidak dapat diubah">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="manajemen" <?= $user['role'] == 'manajemen' ? 'selected' : '' ?>>Manajemen</option>
                                <option value="spm" <?= $user['role'] == 'spm' ? 'selected' : '' ?>>SPM</option>
                                <option value="kabag_aak" <?= $user['role'] == 'kabag_aak' ? 'selected' : '' ?>>Kabag AAK</option>
                                <option value="kabag_kuk" <?= $user['role'] == 'kabag_kuk' ? 'selected' : '' ?>>Kabag KUK</option>
                                <option value="aak" <?= $user['role'] == 'aak' ? 'selected' : '' ?>>Staf AAK</option>
                                <option value="kuk" <?= $user['role'] == 'kuk' ? 'selected' : '' ?>>Staf KUK</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                    </div>
                    
                    <div class="bg-light p-3 rounded-3 mt-2">
                        <p class="text-muted small mb-2"><i class="bi bi-key me-1"></i> Ganti Password (Opsional)</p>
                        <div class="mb-2">
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="edit_pass_<?= $user['id'] ?>" class="form-control" placeholder="Password Baru">
                                <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="edit_pass_<?= $user['id'] ?>"><i class="bi bi-eye-slash"></i></button>
                            </div>
                        </div>
                        <div>
                            <div class="input-group input-group-sm">
                                <input type="password" name="konfirmasi_password" id="edit_konf_<?= $user['id'] ?>" class="form-control" placeholder="Konfirmasi Password">
                                <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="edit_konf_<?= $user['id'] ?>"><i class="bi bi-eye-slash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>

<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tampilkan modal jika ada error validasi (flash data)
    <?php if (session()->getFlashdata('show_modal')): ?>
        const modalId = '<?= session()->getFlashdata('show_modal') ?>';
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    <?php endif; ?>

    // Toggle Password Visibility
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
});

function confirmDelete(id, name) {
    if (confirm(`Hapus pengguna "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
        const form = document.getElementById('formHapus');
        form.action = `<?= site_url('admin/users/delete/') ?>${id}`;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>