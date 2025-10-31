<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Master KRITERIA/ELEMEN/INDIKATOR<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- PERUBAHAN: Menggunakan Grid System untuk header yang responsif -->
<div class="row g-3 align-items-center mb-4">
    <!-- Kolom Teks Deskripsi -->
    <div class="col-lg-6">
        <p class="text-muted mb-0">Kelola daftar Kriteria/Elemen/Indikator yang akan digunakan sebagai checklist pada halaman Input LED.</p>
    </div>
    <!-- Kolom Tombol Aksi -->
    <div class="col-lg-6 text-lg-end">
        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-2"></i> Import Excel
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-2"></i> Tambah Kriteria Baru
        </button>
        <button type="button" class="btn btn-danger btn-sm d-none" id="bulkDeleteButton" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
            <i class="bi bi-trash me-2"></i> Hapus Data Terpilih
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <!-- Form untuk Hapus Massal (Bulk Delete) -->
        <form action="<?= site_url('admin/master-data/led/delete-batch') ?>" method="POST" id="bulkDeleteForm">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 3%;" class="text-center"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                            <th style="width: 5%;" class="text-center">Nomor</th>
                            <th>Nama Kriteria/Elemen/Indikator</th>
                            <th style="width: 20%;">Kategori</th>
                            <th style="width: 15%;">Penanggung Jawab</th> <!-- KOLOM BARU -->
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center"><input class="form-check-input row-checkbox" type="checkbox" name="ids[]" value="<?= $item['id'] ?>"></td>
                            <td class="text-center fw-bold"><?= esc($item['nomor_kriteria']) ?></td>
                            <td><?= nl2br(esc($item['nama_kriteria'])) ?></td>
                            <td><?= esc($item['kategori']) ?></td>
                            <td>
                                <?php
                                    $role = esc($item['role_assignment']);
                                    $badge_class = 'bg-secondary';
                                    if ($role === 'aak') $badge_class = 'bg-success';
                                    if ($role === 'kuk') $badge_class = 'bg-info text-dark';
                                    if ($role === 'all') $badge_class = 'bg-primary';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= strtoupper($role) ?: 'N/A' ?></span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?= $item['id'] ?>"
                                    data-id="<?= $item['id'] ?>"
                                    data-nomor="<?= esc($item['nomor_kriteria']) ?>"
                                    data-nama="<?= esc($item['nama_kriteria']) ?>"
                                    data-kategori="<?= esc($item['kategori']) ?>"
                                    data-role_assignment="<?= esc($item['role_assignment']) ?>"
                                >
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nomor_kriteria']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data Kriteria LED.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================== -->
<!-- MODAL UNTUK TAMBAH DATA -->
<!-- ========================================================== -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Modal diperbesar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Form Tambah Kriteria LED</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/master-data/led/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'addModal'): ?>
                        <div class="alert alert-danger">Terdapat kesalahan input, silakan periksa kembali.</div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="add_nomor_kriteria" class="form-label">Nomor Kriteria</label>
                        <input type="text" name="nomor_kriteria" id="add_nomor_kriteria" class="form-control <?= (isset($validation) && $validation->hasError('nomor_kriteria')) ? 'is-invalid' : '' ?>" value="<?= old('nomor_kriteria') ?>" placeholder="Contoh: 1.1 atau 2.a" required>
                        <?php if(isset($validation) && $validation->hasError('nomor_kriteria')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nomor_kriteria') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="add_nama_kriteria" class="form-label">Nama Kriteria/Elemen/Indikator</label>
                        <textarea name="nama_kriteria" id="add_nama_kriteria" class="form-control <?= (isset($validation) && $validation->hasError('nama_kriteria')) ? 'is-invalid' : '' ?>" rows="4" required><?= old('nama_kriteria') ?></textarea>
                        <?php if(isset($validation) && $validation->hasError('nama_kriteria')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_kriteria') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="add_kategori" class="form-select">
                                <option value="">-- Tidak Ada Kategori --</option>
                                <?php if (!empty($kategori_list)): ?>
                                    <?php foreach($kategori_list as $kategori): ?>
                                        <option value="<?= esc($kategori['nama_kategori']) ?>" <?= (old('kategori') == $kategori['nama_kategori']) ? 'selected' : '' ?>>
                                            <?= esc($kategori['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="add_role_assignment" class="form-label">Penanggung Jawab</label>
                            <select name="role_assignment" id="add_role_assignment" class="form-select">
                                <option value="">-- Pilih Role --</option>
                                <option value="all" <?= (old('role_assignment') == 'all') ? 'selected' : '' ?>>Semua (AAK & KUK)</option>
                                <option value="aak" <?= (old('role_assignment') == 'aak') ? 'selected' : '' ?>>AAK</option>
                                <option value="kuk" <?= (old('role_assignment') == 'kuk') ? 'selected' : '' ?>>KUK</option>
                            </select>
                        </div>
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
<!-- MODAL UNTUK IMPORT EXCEL -->
<!-- ========================================================== -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Kriteria LED dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/master-data/led/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Upload file (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" id="file_excel" accept=".xlsx" required>
                    </div>
                    <div class="alert alert-info small">
                        <strong>Panduan Format:</strong>
                        <ul>
                            <li>Baris pertama (header) akan dilewati.</li>
                            <li>Data dimulai dari baris ke-2.</li>
                            <li>Kolom A: Nomor Kriteria</li>
                            <li>Kolom B: Nama Kriteria</li>
                            <li>Kolom C: Kategori (cth: Kriteria 1)</li>
                            <li>Kolom D: Penanggung Jawab (isi: aak, kuk, atau all)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- MODAL UNTUK EDIT DATA (PER ITEM) -->
<!-- ========================================================== -->
<?php if (!empty($items)): foreach ($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Modal diperbesar -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel-<?= $item['id'] ?>">Edit Kriteria LED</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/master-data/led/update/' . $item['id']) ?>" method="POST" class="editForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'editModal-' . $item['id']): ?>
                        <div class="alert alert-danger">Terdapat kesalahan input.</div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="edit_nomor_kriteria_<?= $item['id'] ?>" class="form-label">Nomor Kriteria</label>
                        <input type="text" name="nomor_kriteria" id="edit_nomor_kriteria_<?= $item['id'] ?>" class="form-control" value="<?= old('nomor_kriteria', $item['nomor_kriteria']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_kriteria_<?= $item['id'] ?>" class="form-label">Nama Kriteria/Elemen/Indikator</label>
                        <textarea name="nama_kriteria" id="edit_nama_kriteria_<?= $item['id'] ?>" class="form-control" rows="4" required><?= old('nama_kriteria', $item['nama_kriteria']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kategori_<?= $item['id'] ?>" class="form-label">Kategori</label>
                            <select name="kategori" id="edit_kategori_<?= $item['id'] ?>" class="form-select">
                                <option value="">-- Tidak Ada Kategori --</option>
                                <?php if (!empty($kategori_list)): ?>
                                    <?php foreach($kategori_list as $kategori): ?>
                                        <option value="<?= esc($kategori['nama_kategori']) ?>" <?= (old('kategori', $item['kategori']) == $kategori['nama_kategori']) ? 'selected' : '' ?>>
                                            <?= esc($kategori['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role_assignment_<?= $item['id'] ?>" class="form-label">Penanggung Jawab</label>
                            <select name="role_assignment" id="edit_role_assignment_<?= $item['id'] ?>" class="form-select">
                                <option value="">-- Pilih Role --</option>
                                <option value="all" <?= (old('role_assignment', $item['role_assignment']) == 'all') ? 'selected' : '' ?>>Semua (AAK & KUK)</option>
                                <option value="aak" <?= (old('role_assignment', $item['role_assignment']) == 'aak') ? 'selected' : '' ?>>AAK</option>
                                <option value="kuk" <?= (old('role_assignment', $item['role_assignment']) == 'kuk') ? 'selected' : '' ?>>KUK</option>
                            </select>
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

<!-- Modal Konfirmasi Hapus Massal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus semua data yang terpilih?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
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

    // --- LOGIKA HAPUS MASSAL ---
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteButton = document.getElementById('bulkDeleteButton');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');

    function toggleBulkDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteButton.classList.toggle('d-none', !anyChecked);
    }

    if (selectAll) {
        selectAll.addEventListener('click', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            toggleBulkDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('click', toggleBulkDeleteButton);
    });

    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function() {
            bulkDeleteForm.submit();
        });
    }
});

// Fungsi konfirmasi hapus tunggal
function confirmDelete(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus Kriteria:\n"${name}"?`)) {
        const form = document.getElementById('formHapus');
        form.action = `<?= site_url('admin/master-data/led/delete/') ?>${id}`;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
