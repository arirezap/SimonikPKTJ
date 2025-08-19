<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kelola Pengguna</h1>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Pengguna Baru
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error') || (isset($validation) && $validation->getErrors())): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?? 'Terdapat kesalahan input.' ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php $no = 1; foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($user['nama_lengkap']) ?></div>
                                </td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                        $role = esc($user['role']);
                                        $badge_class = 'bg-secondary'; // Default
                                        if ($role === 'admin') $badge_class = 'bg-danger';
                                        if ($role === 'manajemen') $badge_class = 'bg-primary';
                                        if ($role === 'aak') $badge_class = 'bg-success';
                                        if ($role === 'kuk') $badge_class = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= strtoupper($role) ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal-<?= $user['id'] ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $user['id'] ?>, '<?= esc($user['nama_lengkap']) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pengguna terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Form Tambah Pengguna Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= site_url('admin/users/store') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body">
          <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'addUserModal'): ?>
              <div class="alert alert-danger">
                  Terdapat kesalahan input. Silakan periksa kembali.
              </div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control <?= (isset($validation) && $validation->hasError('nama_lengkap')) ? 'is-invalid' : '' ?>" value="<?= old('nama_lengkap') ?>" required>
            <?php if(isset($validation) && $validation->hasError('nama_lengkap')): ?>
                <div class="invalid-feedback"><?= $validation->getError('nama_lengkap') ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" value="<?= old('username') ?>" required>
            <?php if(isset($validation) && $validation->hasError('username')): ?>
                <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" value="<?= old('email') ?>" required>
            <?php if(isset($validation) && $validation->hasError('email')): ?>
                <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select <?= (isset($validation) && $validation->hasError('role')) ? 'is-invalid' : '' ?>" required>
                <option value="" disabled selected>-- Pilih Role --</option>
                <option value="manajemen" <?= (old('role') == 'manajemen') ? 'selected' : '' ?>>Manajemen</option>
                <option value="aak" <?= (old('role') == 'aak') ? 'selected' : '' ?>>AAK</option>
                <option value="kuk" <?= (old('role') == 'kuk') ? 'selected' : '' ?>>KUK</option>
                <option value="admin" <?= (old('role') == 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
            <?php if(isset($validation) && $validation->hasError('role')): ?>
                <div class="invalid-feedback"><?= $validation->getError('role') ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <!-- PERBAIKAN: Menggunakan div.input-group-icon -->
            <div class="input-group-icon">
                <i class="bi bi-lock-fill form-icon"></i>
                <input type="password" name="password" id="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" required>
                <button type="button" class="btn password-toggle-btn" data-target="password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <?php if(isset($validation) && $validation->hasError('password')): ?>
                <div class="text-danger small mt-1"><?= $validation->getError('password') ?></div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
            <!-- PERBAIKAN: Menggunakan div.input-group-icon -->
            <div class="input-group-icon">
                <i class="bi bi-lock-fill form-icon"></i>
                <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control <?= (isset($validation) && $validation->hasError('konfirmasi_password')) ? 'is-invalid' : '' ?>" required>
                <button type="button" class="btn password-toggle-btn" data-target="konfirmasi_password"><i class="bi bi-eye-slash"></i></button>
            </div>
             <?php if(isset($validation) && $validation->hasError('konfirmasi_password')): ?>
                <div class="text-danger small mt-1"><?= $validation->getError('konfirmasi_password') ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Pengguna -->
<?php if (!empty($users)): foreach ($users as $user): ?>
<div class="modal fade" id="editUserModal-<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel-<?= $user['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel-<?= $user['id'] ?>">Edit Pengguna: <?= esc($user['nama_lengkap']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/users/update/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= old('nama_lengkap', $user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= old('username', $user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="manajemen" <?= (old('role', $user['role']) == 'manajemen') ? 'selected' : '' ?>>Manajemen</option>
                            <option value="aak" <?= (old('role', $user['role']) == 'aak') ? 'selected' : '' ?>>AAK</option>
                            <option value="kuk" <?= (old('role', $user['role']) == 'kuk') ? 'selected' : '' ?>>KUK</option>
                            <option value="admin" <?= (old('role', $user['role']) == 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <hr>
                    <p class="text-muted small">Kosongkan password jika tidak ingin mengubahnya.</p>
                    <div class="mb-3">
                        <label for="edit_password_<?= $user['id'] ?>" class="form-label">Password Baru</label>
                        <!-- PERBAIKAN: Menggunakan div.input-group-icon -->
                        <div class="input-group-icon">
                            <i class="bi bi-lock-fill form-icon"></i>
                            <input type="password" name="password" id="edit_password_<?= $user['id'] ?>" class="form-control">
                            <button type="button" class="btn password-toggle-btn" data-target="edit_password_<?= $user['id'] ?>"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_konfirmasi_password_<?= $user['id'] ?>" class="form-label">Konfirmasi Password Baru</label>
                        <!-- PERBAIKAN: Menggunakan div.input-group-icon -->
                        <div class="input-group-icon">
                            <i class="bi bi-lock-fill form-icon"></i>
                            <input type="password" name="konfirmasi_password" id="edit_konfirmasi_password_<?= $user['id'] ?>" class="form-control">
                            <button type="button" class="btn password-toggle-btn" data-target="edit_konfirmasi_password_<?= $user['id'] ?>"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>

<!-- Form tersembunyi untuk proses hapus -->
<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logika untuk membuka kembali modal jika ada error validasi
    <?php if (session()->getFlashdata('show_modal')): ?>
        const modalId = '<?= session()->getFlashdata('show_modal') ?>';
        if (document.getElementById(modalId)) {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        }
    <?php endif; ?>

    // Logika untuk tombol lihat password
    document.querySelectorAll('.password-toggle-btn').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const passwordInput = document.getElementById(targetId);
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        });
    });
});

// Fungsi konfirmasi hapus
function confirmDelete(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${name}"?`)) {
        const form = document.getElementById('formHapus');
        form.action = `<?= site_url('admin/users/delete/') ?>${id}`;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
