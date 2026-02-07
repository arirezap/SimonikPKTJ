<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kelola Pengguna</h1>
        
        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </a>
    </div>

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