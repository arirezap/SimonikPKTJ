<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Master Standar LED<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div>
                    <h5>Daftar Standar LED</h5>
                    <p class="text-muted mb-0">Kelola daftar Standar yang akan digunakan untuk mengelompokkan Kriteria LED.</p>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <tbody>
                        <?php if (!empty($items)): foreach($items as $item): ?>
                        <tr>
                            <td><?= esc($item['nama_standar']) ?></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?= $item['id'] ?>">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nama_standar']) ?>')">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td class="text-center">Belum ada data standar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Tambah Standar Baru</h5></div>
            <div class="card-body">
                <form action="<?= site_url('admin/master-data/led-standar/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="add_nama_standar" class="form-label">Nama Standar</label>
                        <input type="text" name="nama_standar" id="add_nama_standar" class="form-control <?= $validation->hasError('nama_standar') ? 'is-invalid' : '' ?>" value="<?= old('nama_standar') ?>" required>
                        <?php if($validation->hasError('nama_standar')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_standar') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan Standar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($items)): foreach ($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Standar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/master-data/led-standar/update/' . $item['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_standar_<?= $item['id'] ?>" class="form-label">Nama Standar</label>
                        <input type="text" name="nama_standar" id="edit_nama_standar_<?= $item['id'] ?>" class="form-control" value="<?= old('nama_standar', $item['nama_standar']) ?>" required>
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

<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus standar "${name}"?`)) {
        const form = document.getElementById('formHapus');
        form.action = `<?= site_url('admin/master-data/led-standar/delete/') ?>${id}`;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>