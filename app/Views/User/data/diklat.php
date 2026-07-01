<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Data Diklat<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Data Diklat') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted">Kelola daftar program diklat yang sedang berjalan, akan datang, atau telah selesai.</p>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDiklatModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Program Diklat
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Program</th>
                        <th>Periode</th>
                        <th class="text-center">Jumlah Peserta</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($daftar_diklat)): foreach($daftar_diklat as $diklat): ?>
                    <tr>
                        <td><?= esc($diklat['nama_diklat']) ?></td>
                        <td><?= esc($diklat['periode']) ?></td>
                        <td class="text-center"><?= esc($diklat['jumlah_peserta']) ?></td>
                        <td class="text-center">
                            <?php
                                $status_class = 'bg-secondary';
                                if ($diklat['status'] == 'Berjalan') $status_class = 'bg-primary';
                                if ($diklat['status'] == 'Selesai') $status_class = 'bg-success';
                            ?>
                            <span class="badge <?= $status_class ?>"><?= esc($diklat['status']) ?></span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDiklatModal-<?= $diklat['id'] ?>"><i class="bi bi-pencil-fill"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $diklat['id'] ?>, '<?= esc($diklat['nama_diklat']) ?>')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada data program diklat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Diklat -->
<div class="modal fade" id="addDiklatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Form Program Diklat Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('diklat/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Program</label><input type="text" name="nama_diklat" class="form-control" value="<?= old('nama_diklat') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Periode</label><input type="text" name="periode" class="form-control" value="<?= old('periode') ?>" placeholder="Contoh: 1 - 15 Sep 2025" required></div>
                    <div class="mb-3"><label class="form-label">Jumlah Peserta</label><input type="number" name="jumlah_peserta" class="form-control" value="<?= old('jumlah_peserta') ?>" required></div>
                    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="Akan Datang">Akan Datang</option><option value="Berjalan">Berjalan</option><option value="Selesai">Selesai</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Diklat -->
<?php if (!empty($daftar_diklat)): foreach($daftar_diklat as $diklat): ?>
<div class="modal fade" id="editDiklatModal-<?= $diklat['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Program Diklat</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="<?= site_url('diklat/update/' . $diklat['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Program</label><input type="text" name="nama_diklat" class="form-control" value="<?= old('nama_diklat', $diklat['nama_diklat']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Periode</label><input type="text" name="periode" class="form-control" value="<?= old('periode', $diklat['periode']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Jumlah Peserta</label><input type="number" name="jumlah_peserta" class="form-control" value="<?= old('jumlah_peserta', $diklat['jumlah_peserta']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="Akan Datang" <?= $diklat['status'] == 'Akan Datang' ? 'selected' : '' ?>>Akan Datang</option><option value="Berjalan" <?= $diklat['status'] == 'Berjalan' ? 'selected' : '' ?>>Berjalan</option><option value="Selesai" <?= $diklat['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>

<form id="formHapus" method="POST" action=""><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus program diklat "${name}"?`)) {
        const form = document.getElementById('formHapus');
        form.action = `<?= site_url('diklat/delete/') ?>${id}`;
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
