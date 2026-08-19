<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Standar LED') ?><?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Standar LED<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Top Toolbar Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchStandar" class="form-control border-start-0 ps-0" placeholder="Cari standar LED...">
                </div>
            </div>
            <div class="col-md-6 col-lg-7 d-flex justify-content-md-end align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-bookmarks-fill me-1"></i> <span id="countStandar"><?= count($items) ?></span> Standar
                </span>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Standar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelStandar">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Nama Standar LED</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): foreach($items as $index => $item): ?>
                    <tr class="standar-row">
                        <td class="text-center fw-bold text-muted nomor-standar"><?= $index + 1 ?></td>
                        <td class="fw-semibold text-dark standar-nama">
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill me-2">
                                <i class="bi bi-bookmark-fill text-primary me-1"></i> <?= esc($item['nama_standar']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-circle shadow-sm" 
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal-<?= $item['id'] ?>"
                                    title="Edit Standar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btn-hapus" 
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                    onclick="confirmDeleteStandar(<?= $item['id'] ?>, '<?= esc($item['nama_standar'], 'js') ?>')"
                                    title="Hapus Standar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr id="emptyRow">
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada data standar LED.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr id="noSearchResult" style="display: none;">
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-3 d-block mb-1 text-secondary opacity-50"></i>
                            Tidak ada standar yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Standar -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="addModalLabel"><i class="bi bi-plus-circle me-2"></i> Tambah Standar Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/led-standar/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_nama_standar" class="form-label fw-bold text-dark small">Nama Standar LED <span class="text-danger">*</span></label>
                        <input type="text" name="nama_standar" id="add_nama_standar" class="form-control" placeholder="Contoh: Standar 1 VMTS, Standar 2 Tata Pamong..." value="<?= old('nama_standar') ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Standar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Standar -->
<?php if (!empty($items)): foreach ($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fs-6 fw-bold" id="editModalLabel-<?= $item['id'] ?>"><i class="bi bi-pencil-square me-2"></i> Edit Standar LED</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/led-standar/update/' . $item['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_nama_standar_<?= $item['id'] ?>" class="form-label fw-bold text-dark small">Nama Standar LED <span class="text-danger">*</span></label>
                        <input type="text" name="nama_standar" id="edit_nama_standar_<?= $item['id'] ?>" class="form-control" value="<?= esc($item['nama_standar']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>

<!-- Form Hapus Tersembunyi -->
<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Live Search Standar
    document.getElementById('searchStandar').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.standar-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nama = row.querySelector('.standar-nama').textContent.toLowerCase();
            if (nama.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResult = document.getElementById('noSearchResult');
        if (noResult) {
            noResult.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        const countEl = document.getElementById('countStandar');
        if (countEl) {
            countEl.textContent = query ? visibleCount : rows.length;
        }
    });

    // Delete Confirmation with SweetAlert2 & Native Fallback
    function confirmDeleteStandar(id, name) {
        function executeDelete() {
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('master-data/led-standar/delete/') ?>${id}`;
            form.submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Standar?',
                html: `Standar <strong>"${name}"</strong> akan dihapus. Kriteria terkait akan dialihkan menjadi tanpa standar.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus standar "${name}"? Kriteria terkait akan dialihkan menjadi tanpa standar.`)) {
                executeDelete();
            }
        }
    }

    // Auto open modal on validation error
    <?php if (session()->getFlashdata('show_modal')): ?>
        const modalEl = document.getElementById('<?= session()->getFlashdata('show_modal') ?>');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    <?php endif; ?>
</script>
<?= $this->endSection() ?>