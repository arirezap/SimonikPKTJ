<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Tambah Pengguna Baru') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Pengguna Baru</h1>
        <a href="<?= site_url('users') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir User Baru</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('users/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Informasi Akun</h5>
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role Aplikasi</label>
                            <select name="role" class="form-select">
                                <option value="user">User (Pegawai/Dosen)</option>
                                <option value="admin">Administrator</option>
                                <option value="kabag_aak">Kabag AAK</option>
                                <option value="kabag_kuk">Kabag KUK</option>
                                <option value="direktur">Direktur</option>
                                <option value="wadir">Wakil Direktur</option>
                                <option value="manajemen">Manajemen (Lainnya)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Data Kepegawaian & Hierarki</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap (dengan Gelar) <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= old('nama_lengkap') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="<?= old('nip') ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="<?= old('jabatan') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat/Golongan</label>
                                <input type="text" name="pangkat" class="form-control" value="<?= old('pangkat') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit Kerja</label>
                            <select name="unit" class="form-select">
                                <option value="">-- Pilih Unit Kerja --</option>
                                <?php if (!empty($unit_kerja_list)): ?>
                                    <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                        <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= (old('unit') == $unit_kerja['nama_unit']) ? 'selected' : '' ?>><?= esc($unit_kerja['nama_unit']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-primary">Atasan Langsung</label>
                            <select name="atasan_id" class="form-select select2">
                                <option value="">-- Tidak Memiliki Atasan (Misal: Direktur) --</option>
                                <?php foreach ($potential_bosses as $boss): ?>
                                    <option value="<?= $boss['id'] ?>">
                                        <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Atasan ini akan muncul sebagai Pihak Kedua di Kontrak Kinerja.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan User Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>