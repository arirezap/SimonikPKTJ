<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Master Indikator Kinerja<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted">Kelola daftar Indikator Kinerja yang akan digunakan sebagai pilihan pada form Input Rencana Kerja.</p>
    </div>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Indikator Baru
    </button>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Indikator Kinerja</th>
                        <th style="width: 15%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): $no = 1; foreach ($items as $item): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($item['nama_indikator']) ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?= $item['id'] ?>">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="<?= $item['id'] ?>" data-nama="<?= esc($item['nama_indikator']) ?>">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Belum ada data Indikator Kinerja.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- MODAL UNTUK TAMBAH DATA -->
<!-- ========================================================== -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Form Tambah Indikator Kinerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= site_url('admin/master-data/indikator/store') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body">
            <?php if ($validation->getErrors() && session()->getFlashdata('show_modal') === 'addModal'): ?>
                <div class="alert alert-danger">Terdapat kesalahan input, silakan periksa kembali.</div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="add_nama_indikator" class="form-label">Nama Indikator Kinerja</label>
                <textarea name="nama_indikator" id="add_nama_indikator" class="form-control <?= $validation->hasError('nama_indikator') ? 'is-invalid' : '' ?>" rows="3" required><?= old('nama_indikator') ?></textarea>
                <?php if($validation->hasError('nama_indikator')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('nama_indikator') ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================================== -->
<!-- MODAL UNTUK EDIT DATA -->
<!-- ========================================================== -->
<?php if (!empty($items)): foreach ($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel-<?= $item['id'] ?>">Edit Indikator Kinerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= site_url('admin/master-data/indikator/update/' . $item['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="modal-body">
            <?php if ($validation->getErrors() && session()->getFlashdata('show_modal') === 'editModal-' . $item['id']): ?>
                <div class="alert alert-danger">Terdapat kesalahan input, silakan periksa kembali.</div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="edit_nama_indikator_<?= $item['id'] ?>" class="form-label">Nama Indikator Kinerja</label>
                <textarea name="nama_indikator" id="edit_nama_indikator_<?= $item['id'] ?>" class="form-control" rows="3" required><?= old('nama_indikator', $item['nama_indikator']) ?></textarea>
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

    // Logika event listener untuk tombol hapus
    const table = document.querySelector('.table');
    if (table) {
        table.addEventListener('click', function(e) {
            const btnHapus = e.target.closest('.btn-hapus');
            if (btnHapus) {
                const id = btnHapus.dataset.id;
                const nama = btnHapus.dataset.nama;
                
                if (confirm(`Apakah Anda yakin ingin menghapus Indikator Kinerja:\n"${nama}"?`)) {
                    const form = document.getElementById('formHapus');
                    form.action = `<?= site_url('admin/master-data/indikator/delete/') ?>${id}`;
                    form.submit();
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
