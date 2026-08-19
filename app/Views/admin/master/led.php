<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Kriteria LED') ?><?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Kriteria LED<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .sticky-footer-bar {
        position: sticky;
        bottom: 0;
        background-color: #ffffff;
        padding: 1rem 1.5rem;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        z-index: 1020;
    }
    .kriteria-clamped {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kriteria-text {
        white-space: pre-line;
        line-height: 1.55;
    }
    .btn-toggle-kriteria {
        cursor: pointer;
        user-select: none;
        transition: color 0.15s ease-in-out;
    }
    .btn-toggle-kriteria:hover {
        text-decoration: underline !important;
    }
</style>
<?= $this->endSection() ?>

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
            <div class="col-md-5">
                <form action="<?= site_url('master-data/led') ?>" method="GET" id="filterForm" class="m-0">
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-primary"><i class="bi bi-mortarboard me-1"></i> Prodi</span>
                        <select name="prodi" id="prodi_filter" class="form-select border-primary fw-bold text-primary" onchange="this.form.submit()">
                            <?php $prodiList = config('Ecc')->prodiList; ?>
                            <?php foreach($prodiList as $prodi): ?>
                                <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>><?= $prodi; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-2">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchLed" class="form-control border-start-0 ps-0" placeholder="Cari kriteria / standar...">
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold text-nowrap">
                    <i class="bi bi-list-check me-1"></i> <span id="countLed"><?= count($items) ?></span> Butir
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <form action="" method="POST" id="bulkActionForm">
            <?= csrf_field() ?>
            <input type="hidden" name="prodi_filter" value="<?= esc($selectedProdi) ?>">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelLed">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Kriteria / Elemen / Indikator</th>
                            <th style="width: 220px;">Standar LED</th>
                            <th style="width: 140px;" class="text-center">Penanggung Jawab</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): $no = 1; foreach ($items as $item): ?>
                        <tr id="kriteria-<?= $item['id'] ?>" class="led-row">
                            <td class="text-center"><input class="form-check-input row-checkbox" type="checkbox" name="ids[]" value="<?= $item['id'] ?>"></td>
                            <td class="text-center fw-bold text-muted nomor-led"><?= $no++ ?></td>
                            <td class="led-nama">
                                <?php 
                                    $rawText = $item['nama_kriteria'] ?? '';
                                    $isLong = mb_strlen($rawText) > 100;
                                ?>
                                <div class="kriteria-wrapper">
                                    <div class="kriteria-text text-dark fw-semibold <?= $isLong ? 'kriteria-clamped' : '' ?>"><?= esc($rawText) ?></div>
                                    <?php if ($isLong): ?>
                                        <a href="javascript:void(0)" class="btn-toggle-kriteria text-primary text-decoration-none small fw-semibold d-inline-flex align-items-center mt-1" onclick="toggleKriteria(this)">
                                            <span>Lihat Selengkapnya</span> <i class="bi bi-chevron-down ms-1"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="led-standar">
                                <?php if (!empty($item['nama_standar'])): ?>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill"><i class="bi bi-bookmark-fill text-primary me-1"></i> <?= esc($item['nama_standar']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic">- Tanpa Standar -</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center led-role">
                                <?php
                                    $role = esc($item['role_assignment']);
                                    $badge_class = 'bg-secondary';
                                    if ($role === 'aak') $badge_class = 'bg-success-subtle text-success border border-success-subtle';
                                    elseif ($role === 'kuk') $badge_class = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                    elseif ($role === 'all') $badge_class = 'bg-primary-subtle text-primary border border-primary-subtle';
                                    else $badge_class = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                ?>
                                <span class="badge <?= $badge_class ?> px-2.5 py-1.5 rounded-pill fw-semibold"><?= strtoupper($role) ?: 'N/A' ?></span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-outline-warning btn-sm rounded-circle shadow-sm" 
                                        style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal-<?= $item['id'] ?>"
                                        title="Edit Kriteria">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btn-hapus" 
                                        style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                        onclick="confirmDelete(<?= $item['id'] ?>, '<?= esc($item['nama_kriteria'], 'js') ?>')"
                                        title="Hapus Kriteria">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr id="emptyRow">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada data Kriteria LED untuk prodi <?= esc($selectedProdi) ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr id="noSearchResult" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-search fs-3 d-block mb-1 text-secondary opacity-50"></i>
                                Tidak ada kriteria yang cocok dengan pencarian.
                            </td>
                        </tr>
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
<!-- ========================================================== -->
<!-- FOOTER BAR -->
<!-- ========================================================== -->
<?= $this->section('footer_bar') ?>
<div class="sticky-footer-bar">
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('master-data/led/export?prodi=' . esc($selectedProdi)) ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
        </a>
        <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 text-info-emphasis" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
        </button>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm d-none" id="bulkEditButton" data-bs-toggle="modal" data-bs-target="#batchEditModal">
            <i class="bi bi-pencil-square me-1"></i> Ubah (<span id="bulkEditCount">0</span>) Terpilih
        </button>
        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm d-none" id="bulkDeleteButton">
            <i class="bi bi-trash-fill me-1"></i> Hapus (<span id="bulkDeleteCount">0</span>) Terpilih
        </button>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const scrollContainer = document.querySelector('.content-wrapper');

    function saveScrollPosition() {
        if (scrollContainer) {
            sessionStorage.setItem('scrollPos', scrollContainer.scrollTop);
        } else {
            sessionStorage.setItem('scrollPos', window.scrollY);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Restore scroll position
        if (sessionStorage.getItem('scrollPos')) {
            setTimeout(function() {
                let scrollPos = parseInt(sessionStorage.getItem('scrollPos'));
                if (scrollContainer) {
                    scrollContainer.scrollTo(0, scrollPos);
                } else {
                    window.scrollTo(0, scrollPos);
                }
                sessionStorage.removeItem('scrollPos');
            }, 100);
        }

        // Live Search LED
        const searchInput = document.getElementById('searchLed');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.led-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nama = row.querySelector('.led-nama').textContent.toLowerCase();
                    const standar = row.querySelector('.led-standar').textContent.toLowerCase();
                    const role = row.querySelector('.led-role').textContent.toLowerCase();
                    if (nama.includes(query) || standar.includes(query) || role.includes(query)) {
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

                const countEl = document.getElementById('countLed');
                if (countEl) {
                    countEl.textContent = query ? visibleCount : rows.length;
                }
            });
        }

        <?php if (session()->getFlashdata('show_modal')): ?>
            const modalId = '<?= session()->getFlashdata('show_modal') ?>';
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        <?php endif; ?>

        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteButton = document.getElementById('bulkDeleteButton');
        const bulkEditButton = document.getElementById('bulkEditButton'); 
        const bulkActionForm = document.getElementById('bulkActionForm'); 
        const bulkEditCount = document.getElementById('bulkEditCount');
        const bulkDeleteCount = document.getElementById('bulkDeleteCount');

        function toggleBulkButtons() {
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            const count = checkedBoxes.length;
            const anyChecked = count > 0;
            
            bulkDeleteButton.classList.toggle('d-none', !anyChecked);
            bulkEditButton.classList.toggle('d-none', !anyChecked);
            if (bulkEditCount) bulkEditCount.textContent = count;
            if (bulkDeleteCount) bulkDeleteCount.textContent = count;
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

        // Bulk Delete with SweetAlert2 & Fallback
        if (bulkDeleteButton) {
            bulkDeleteButton.addEventListener('click', function() {
                const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
                if (checkedBoxes.length === 0) return;

                function executeBulkDelete() {
                    saveScrollPosition();
                    bulkActionForm.action = "<?= site_url('master-data/led/deleteBatch') ?>";
                    bulkActionForm.submit();
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Kriteria Terpilih?',
                        html: `Sebanyak <strong>${checkedBoxes.length} butir kriteria</strong> akan dihapus permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus Semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            executeBulkDelete();
                        }
                    });
                } else {
                    if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} butir kriteria yang dipilih?`)) {
                        executeBulkDelete();
                    }
                }
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
                bulkActionForm.action = "<?= site_url('master-data/led/batchUpdate') ?>";
                bulkActionForm.submit();
            });
        }
        
        const formsToTrack = document.querySelectorAll('.form-simpan-scroll, .editForm');
        formsToTrack.forEach(form => {
            form.addEventListener('submit', saveScrollPosition);
        });
    });

    // Single Delete with SweetAlert2 & Fallback
    function confirmDelete(id, name) {
        function executeDelete() {
            saveScrollPosition();
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('master-data/led/delete/') ?>${id}?prodi=<?= esc($selectedProdi) ?>`;
            form.submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Kriteria?',
                html: `Kriteria <strong>"${name}"</strong> akan dihapus permanen.`,
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
            if (confirm(`Apakah Anda yakin ingin menghapus Kriteria:\n"${name}"?`)) {
                executeDelete();
            }
        }
    }

    // Toggle Kriteria Inline Expansion Seamlessly
    function toggleKriteria(btn) {
        const wrapper = btn.closest('.kriteria-wrapper');
        const textEl = wrapper.querySelector('.kriteria-text');
        const span = btn.querySelector('span');
        const icon = btn.querySelector('i');
        
        if (textEl.classList.contains('kriteria-clamped')) {
            textEl.classList.remove('kriteria-clamped');
            span.textContent = 'Sembunyikan';
            icon.className = 'bi bi-chevron-up ms-1';
        } else {
            textEl.classList.add('kriteria-clamped');
            span.textContent = 'Lihat Selengkapnya';
            icon.className = 'bi bi-chevron-down ms-1';
        }
    }
</script>
<?= $this->endSection() ?>