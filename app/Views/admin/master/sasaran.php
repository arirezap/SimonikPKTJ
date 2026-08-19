<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Sasaran Program') ?><?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Sasaran Program<?= $this->endSection() ?>

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
                    <input type="text" id="searchSasaran" class="form-control border-start-0 ps-0" placeholder="Cari sasaran program...">
                </div>
            </div>
            <div class="col-md-6 col-lg-7 d-flex justify-content-md-end align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-folder-check me-1"></i> <span id="countSasaran"><?= count($items) ?></span> Sasaran
                </span>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Sasaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelSasaran">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th>Nama Sasaran Program / Kegiatan</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): foreach($items as $index => $item): ?>
                    <tr class="sasaran-row">
                        <td class="text-center fw-bold text-muted nomor-sasaran"><?= $index + 1 ?></td>
                        <td class="fw-semibold text-dark sasaran-nama"><?= esc($item['nama_sasaran']) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-circle shadow-sm" 
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal-<?= $item['id'] ?>"
                                    title="Edit Sasaran">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btn-hapus" 
                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                    onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nama_sasaran'], 'js') ?>')"
                                    title="Hapus Sasaran">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr id="emptyRow">
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada data sasaran program.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr id="noSearchResult" style="display: none;">
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-3 d-block mb-1 text-secondary opacity-50"></i>
                            Tidak ada sasaran yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Sasaran -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="addModalLabel"><i class="bi bi-plus-circle me-2"></i> Tambah Sasaran Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/sasaran/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nama Sasaran Program / Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="nama_sasaran" class="form-control" rows="3" placeholder="Masukkan sasaran program..." required><?= old('nama_sasaran') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Sasaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Sasaran -->
<?php if (!empty($items)): foreach($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fs-6 fw-bold" id="editModalLabel-<?= $item['id'] ?>"><i class="bi bi-pencil-square me-2"></i> Edit Sasaran Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/sasaran/update/' . $item['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nama Sasaran Program / Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="nama_sasaran" class="form-control" rows="3" required><?= esc($item['nama_sasaran']) ?></textarea>
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
<form id="formHapus" method="POST" action=""><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Live Search Sasaran
    document.getElementById('searchSasaran').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.sasaran-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nama = row.querySelector('.sasaran-nama').textContent.toLowerCase();
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

        const countEl = document.getElementById('countSasaran');
        if (countEl) {
            countEl.textContent = query ? visibleCount : rows.length;
        }
    });

    // Delete Confirmation with SweetAlert2 & Native Fallback
    function confirmDelete(id, name) {
        function executeDelete() {
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('master-data/sasaran/delete/') ?>${id}`;
            form.submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Sasaran?',
                html: `Sasaran <strong>"${name}"</strong> akan dihapus permanen dari sistem.`,
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
            if (confirm(`Apakah Anda yakin ingin menghapus sasaran "${name}"?`)) {
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

