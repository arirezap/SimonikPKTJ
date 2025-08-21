<?= $this->extend('layouts/main') ?>
<?= $this->section('page_title') ?>Master Sasaran Program<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted">Kelola daftar Sasaran Program/Kegiatan yang akan digunakan sebagai pilihan pada form Input Rencana Kerja.</p>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Sasaran Baru
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Sasaran Program/Kegiatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): foreach($items as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($item['nama_sasaran']) ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal-<?= $item['id'] ?>">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nama_sasaran']) ?>')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3" class="text-center">Belum ada data sasaran.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Sasaran -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Form Tambah Sasaran Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('admin/master-data/sasaran/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if ($validation->getErrors() && old('nama_sasaran')): ?><div class="alert alert-danger">Terdapat kesalahan input.</div><?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Sasaran</label>
                        <input type="text" name="nama_sasaran" class="form-control <?= $validation->hasError('nama_sasaran') ? 'is-invalid' : '' ?>" value="<?= old('nama_sasaran') ?>" required>
                        <?php if($validation->hasError('nama_sasaran')): ?><div class="invalid-feedback"><?= $validation->getError('nama_sasaran') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Sasaran -->
<?php if (!empty($items)): foreach($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Sasaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('admin/master-data/sasaran/update/' . $item['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sasaran</label>
                        <input type="text" name="nama_sasaran" class="form-control" value="<?= esc($item['nama_sasaran']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>

<!-- Form Hapus Tersembunyi -->
<form id="formHapus" method="POST" action=""><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus sasaran "${name}"?`)) {
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('admin/master-data/sasaran/delete/') ?>${id}`;
            form.submit();
        }
    }
    // Logika untuk membuka kembali modal jika ada error validasi
    <?php if (session()->getFlashdata('show_modal')): ?>
        const modal = new bootstrap.Modal(document.getElementById('<?= session()->getFlashdata('show_modal') ?>'));
        modal.show();
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
