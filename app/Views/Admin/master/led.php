<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Master KRITERIA/ELEMEN/INDIKATOR<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .sticky-footer-bar {
        position: sticky;
        bottom: 0;
        background-color: #ffffff;
        padding: 1rem 1.5rem;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: flex-end; 
        align-items: center;
        gap: 0.5rem;
        z-index: 1020; 
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<p class="text-muted mb-4">Kelola daftar Kriteria/Elemen/Indikator yang akan digunakan sebagai checklist pada halaman Input LED.</p>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?= site_url('master-data/led') ?>" method="GET" id="filterForm">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label for="prodi_filter" class="form-label fw-bold">Tampilkan Kriteria untuk Program Studi</label>
                    <select name="prodi" id="prodi_filter" class="form-select" onchange="this.form.submit()">
                        <?php $prodiList = config('Simonik')->prodiList; ?>
                        <?php foreach($prodiList as $prodi): ?>
                            <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>><?= $prodi; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
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
        <form action="" method="POST" id="bulkActionForm">
            <?= csrf_field() ?>
            <input type="hidden" name="prodi_filter" value="<?= esc($selectedProdi) ?>">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 3%;" class="text-center"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th>Nama Kriteria/Elemen/Indikator</th>
                            <th style="width: 20%;">Standar</th> <th style="width: 15%;">Penanggung Jawab</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): $no = 1; foreach ($items as $item): ?>
                        <tr id="kriteria-<?= $item['id'] ?>">
                            <td class="text-center"><input class="form-check-input row-checkbox" type="checkbox" name="ids[]" value="<?= $item['id'] ?>"></td>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= nl2br(esc($item['nama_kriteria'])) ?></td>
                            <td><?= esc($item['nama_standar']) ?></td> <td>
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
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-<?= $item['id'] ?>">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nama_kriteria']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data Kriteria LED untuk prodi <?= esc($selectedProdi) ?>.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Form Tambah Kriteria LED</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/led/store') ?>" method="POST" class="form-simpan-scroll">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'addModal'): ?>
                        <div class="alert alert-danger">Terdapat kesalahan input, silakan periksa kembali.</div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="add_prodi" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="prodi" id="add_prodi" class="form-select" required>
                            <?php foreach($prodiList as $prodi): ?>
                                <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>><?= $prodi; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_nama_kriteria" class="form-label">Nama Kriteria/Elemen/Indikator <span class="text-danger">*</span></label>
                        <textarea name="nama_kriteria" id="add_nama_kriteria" class="form-control <?= (isset($validation) && $validation->hasError('nama_kriteria')) ? 'is-invalid' : '' ?>" rows="4" required><?= old('nama_kriteria') ?></textarea>
                        <?php if(isset($validation) && $validation->hasError('nama_kriteria')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('nama_kriteria') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="add_id_standar" class="form-label">Standar</label>
                            <select name="id_standar" id="add_id_standar" class="form-select">
                                <option value="">-- Tidak Ada Standar --</option>
                                <?php if (!empty($standar_list)): ?> <?php foreach($standar_list as $standar): ?> <option value="<?= esc($standar['id']) ?>" <?= (old('id_standar') == $standar['id']) ? 'selected' : '' ?>>
                                            <?= esc($standar['nama_standar']) ?> </option>
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

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Kriteria LED dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/led/import') ?>" method="POST" enctype="multipart/form-data" class="form-simpan-scroll">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_prodi" class="form-label">Import untuk Program Studi <span class="text-danger">*</span></label>
                        <select name="prodi" id="import_prodi" class="form-select" required>
                            <?php foreach($prodiList as $prodi): ?>
                                <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>><?= $prodi; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Upload file (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" id="file_excel" accept=".xlsx" required>
                    </div>
                    <div class="alert alert-info small">
                        <strong>Panduan Format:</strong>
                        <ul>
                            <li>Baris pertama (header) akan dilewati.</li>
                            <li>Kolom A: ID (Kosongkan untuk data baru)</li>
                            <li>Kolom B: Nama Kriteria</li>
                            <li>Kolom C: Standar (Nama persis seperti di master)</li>
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

<!-- Modal Edit -->
<?php if (!empty($items)): foreach ($items as $item): ?>
<div class="modal fade" id="editModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $item['id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel-<?= $item['id'] ?>">Edit Kriteria LED</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('master-data/led/update/' . $item['id']) ?>" method="POST" class="editForm form-simpan-scroll">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (isset($validation) && $validation->getErrors() && session()->getFlashdata('show_modal') === 'editModal-' . $item['id']): ?>
                        <div class="alert alert-danger">Terdapat kesalahan input.</div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="edit_prodi_<?= $item['id'] ?>" class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="prodi" id="edit_prodi_<?= $item['id'] ?>" class="form-select" required>
                            <?php foreach($prodiList as $prodi): ?>
                                <option value="<?= $prodi; ?>" <?= (old('prodi', $item['prodi']) == $prodi) ? 'selected' : '' ?>>
                                    <?= $prodi; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_nama_kriteria_<?= $item['id'] ?>" class="form-label">Nama Kriteria/Elemen/Indikator <span class="text-danger">*</span></label>
                        <textarea name="nama_kriteria" id="edit_nama_kriteria_<?= $item['id'] ?>" class="form-control" rows="4" required><?= old('nama_kriteria', $item['nama_kriteria']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_id_standar_<?= $item['id'] ?>" class="form-label">Standar</label>
                            <select name="id_standar" id="edit_id_standar_<?= $item['id'] ?>" class="form-select">
                                <option value="">-- Tidak Ada Standar --</option>
                                <?php if (!empty($standar_list)): ?> <?php foreach($standar_list as $standar): ?> 
                                    
                                    <!-- INI ADALAH BARIS YANG DIPERBAIKI (Baris 256) -->
                                    <option value="<?= esc($standar['id']) ?>" <?= (old('id_standar', $item['id_standar']) == $standar['id']) ? 'selected' : '' ?>>
                                        <?= esc($standar['nama_standar']) ?> 
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

<!-- Form Hapus Tunggal -->
<form action="" method="POST" id="formHapus">
    <?= csrf_field() ?>
    <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">
</form>

<!-- Modal Hapus Massal -->
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

<!-- Modal Batch Edit -->
<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchEditModalLabel">Ubah Data Terpilih Secara Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Silakan pilih nilai baru yang ingin Anda terapkan ke semua kriteria yang dipilih. Biarkan kosong jika tidak ingin mengubah.</p>
                
                <div class="mb-3">
                    <label for="batch_id_standar" class="form-label">Standar</label>
                    <select id="batch_id_standar" class="form-select">
                        <option value="">-- Biarkan (Tidak Berubah) --</option>
                        <option value="null">-- Hapus Standar (Tidak Ada) --</option>
                        <?php if (!empty($standar_list)): ?>
                            <?php foreach($standar_list as $standar): ?>
                                <option value="<?= esc($standar['id']) ?>">
                                    <?= esc($standar['nama_standar']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="batch_role_assignment" class="form-label">Penanggung Jawab</label>
                    <select id="batch_role_assignment" class="form-select">
                        <option value="">-- Biarkan (Tidak Berubah) --</option>
                        <option value="null">-- Hapus Role (N/A) --</option>
                        <option value="all">Semua (AAK & KUK)</option>
                        <option value="aak">AAK</option>
                        <option value="kuk">KUK</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBulkEditButton">Terapkan Perubahan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- ========================================================== -->
<!-- FOOTER BAR -->
<!-- ========================================================== -->
<?= $this->section('footer_bar') ?>
<div class="sticky-footer-bar">
    <a href="<?= site_url('master-data/led/export?prodi=' . esc($selectedProdi)) ?>" class="btn btn-dark btn-sm">
        <i class="bi bi-download me-2"></i> Export Excel
    </a>
    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload me-2"></i> Import Excel
    </button>
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle me-2"></i> Tambah Kriteria Baru
    </button>
    
    <button type="button" class="btn btn-primary btn-sm d-none" id="bulkEditButton" data-bs-toggle="modal" data-bs-target="#batchEditModal">
        <i class="bi bi-pencil-square me-2"></i> Ubah Data Terpilih
    </button>
    <button type="button" class="btn btn-danger btn-sm d-none" id="bulkDeleteButton" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        <i class="bi bi-trash me-2"></i> Hapus Data Terpilih
    </button>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    // --- PERBAIKAN: Dapatkan elemen yang bisa di-scroll ---
    const scrollContainer = document.querySelector('.content-wrapper');

    /**
     * --- FUNGSI SIMPAN SCROLL ---
     * Menyimpan posisi scroll dari .content-wrapper
     */
    function saveScrollPosition() {
        if (scrollContainer) {
            sessionStorage.setItem('scrollPos', scrollContainer.scrollTop);
        } else {
            // Fallback jika .content-wrapper tidak ditemukan
            sessionStorage.setItem('scrollPos', window.scrollY);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- FUNGSI RESTORE SCROLL ---
        if (sessionStorage.getItem('scrollPos')) {
            setTimeout(function() {
                let scrollPos = parseInt(sessionStorage.getItem('scrollPos'));
                if (scrollContainer) {
                    scrollContainer.scrollTo(0, scrollPos);
                } else {
                    window.scrollTo(0, scrollPos);
                }
                sessionStorage.removeItem('scrollPos'); // Hapus setelah digunakan
            }, 100); // Delay 100ms agar DOM sempat render
        }

        <?php if (session()->getFlashdata('show_modal')): ?>
            const modalId = '<?= session()->getFlashdata('show_modal') ?>';
            if (document.getElementById(modalId)) {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            }
        <?php endif; ?>

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteButton = document.getElementById('bulkDeleteButton');
        const bulkEditButton = document.getElementById('bulkEditButton'); 
        const bulkActionForm = document.getElementById('bulkActionForm'); 
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');

        function toggleBulkButtons() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            bulkDeleteButton.classList.toggle('d-none', !anyChecked);
            bulkEditButton.classList.toggle('d-none', !anyChecked);
        }

        if (selectAll) {
            selectAll.addEventListener('click', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                toggleBulkButtons();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('click', toggleBulkButtons);
        });

        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', function() {
                saveScrollPosition();
                bulkActionForm.action = "<?= site_url('master-data/led/delete-batch') ?>";
                bulkActionForm.submit();
            });
        }

        const confirmBulkEditButton = document.getElementById('confirmBulkEditButton');
        
        if (confirmBulkEditButton) {
            confirmBulkEditButton.addEventListener('click', function() {
                const standarId = document.getElementById('batch_id_standar').value;
                const role = document.getElementById('batch_role_assignment').value;

                bulkActionForm.querySelector('input[name="id_standar"]')?.remove();
                bulkActionForm.querySelector('input[name="role_assignment"]')?.remove();

                if (standarId) {
                    bulkActionForm.insertAdjacentHTML('beforeend', 
                        `<input type="hidden" name="id_standar" value="${standarId}">`
                    );
                }
                if (role) {
                    bulkActionForm.insertAdjacentHTML('beforeend', 
                        `<input type="hidden" name="role_assignment" value="${role}">`
                    );
                }

                saveScrollPosition();
                bulkActionForm.action = "<?= site_url('master-data/led/batch-update') ?>";
                bulkActionForm.submit();
            });
        }
        
        const formsToTrack = document.querySelectorAll('.form-simpan-scroll, .editForm');
        formsToTrack.forEach(form => {
            form.addEventListener('submit', saveScrollPosition);
        });
    });

    function confirmDelete(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus Kriteria:\n"${name}"?`)) {
            saveScrollPosition();
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('master-data/led/delete/') ?>${id}?prodi=<?= esc($selectedProdi) ?>`;
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>