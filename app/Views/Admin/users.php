<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kelola Pengguna</h1>
        
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal" title="Impor banyak pengguna dari file CSV">
                    <i class="bi bi-upload"></i> Import
                </button>
                <a href="<?= site_url('admin/users/export') ?>" class="btn btn-sm btn-outline-secondary" title="Unduh template CSV untuk impor">
                    <i class="bi bi-download"></i> Export Template
                </a>
            </div>
            <a href="<?= site_url('admin/users/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus-fill"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

<!-- Letakkan kode Modal ini di bagian bawah file view -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Pengguna dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/users/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>
                        Silakan unduh template terlebih dahulu untuk memastikan format data sesuai.
                        <a href="<?= site_url('admin/users/export') ?>">Unduh Template Disini</a>.
                    </p>
                    <hr>
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Pilih File CSV (.csv)</label>
                        <input class="form-control" type="file" name="file_excel" id="file_excel" required accept=".csv, text/csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan ini untuk menampilkan error validasi dari proses import -->
<?php if (session()->has('import_errors')): ?>
    <div class="alert alert-danger">
        <strong>Terjadi beberapa kesalahan saat import:</strong>
        <ul>
            <?php foreach (session('import_errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- ... sisa kode tabel pengguna Anda ... -->

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>Role</th>
                            <th class="bg-info text-white">Atasan Langsung</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($users as $user): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= esc($user['nama_lengkap']) ?></strong><br>
                                <small class="text-muted"><?= esc($user['nip'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($user['jabatan'] ?? '-') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($user['role']) ?></span></td>
                            
                            <td>
                                <?php if($user['nama_atasan'] != '-'): ?>
                                    <span class="badge bg-success"><i class="bi bi-person-check-fill"></i> <?= esc($user['nama_atasan']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted font-italic" style="font-size: 0.85em;">Tidak diset / Puncak Pimpinan</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="<?= site_url('admin/users/edit/'.$user['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= site_url('admin/users/delete/'.$user['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>