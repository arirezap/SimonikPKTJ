<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Edit Pengguna & Hierarki') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Pengguna & Hierarki</h1>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Data Pegawai</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('admin/users/update') ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Informasi Akun</h5>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role Aplikasi</label>
                            <select name="role" class="form-select">
                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User (Pegawai/Dosen)</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="kabag_aak" <?= $user['role'] == 'kabag_aak' ? 'selected' : '' ?>>Kabag AAK</option>
                                <option value="kabag_kuk" <?= $user['role'] == 'kabag_kuk' ? 'selected' : '' ?>>Kabag KUK</option>
                                <option value="manajemen" <?= $user['role'] == 'manajemen' ? 'selected' : '' ?>>Manajemen (Direktur/Wadir)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Data Kepegawaian & Hierarki</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap (dengan Gelar)</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="<?= esc($user['nip']) ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="<?= esc($user['jabatan']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat/Golongan</label>
                                <input type="text" name="pangkat" class="form-control" value="<?= esc($user['pangkat']) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" name="unit" class="form-control" value="<?= esc($user['unit']) ?>">
                        </div>

                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-primary">Atasan Langsung (Pejabat Penilai)</label>
                            <select name="atasan_id" class="form-select select2">
                                <option value="">-- Tidak Memiliki Atasan (Misal: Direktur) --</option>
                                <?php foreach ($potential_bosses as $boss): ?>
                                    <option value="<?= $boss['id'] ?>" <?= ($user['atasan_id'] == $boss['id']) ? 'selected' : '' ?>>
                                        <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Pilih atasan langsung untuk keperluan Kontrak Kinerja dan Penilaian.
                                <br>Direktur -> Wadir -> Kabag -> Kanit/Katim -> Pelaksana.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>