<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- COMPACT PAGE HEADER & ACTION TOOLBAR -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                Total <?= count($users) ?> Pengguna
            </span>
        </div>
        
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-info fw-semibold" id="batchEditBtn" title="Ubah atasan banyak pengguna sekaligus">
                <i class="bi bi-people-fill me-1"></i> Batch Edit Atasan
            </button>
            <button type="button" class="btn btn-sm btn-outline-success fw-semibold" data-bs-toggle="modal" data-bs-target="#importModal" title="Impor pengguna dari file Excel (.xlsx / .xls)">
                <i class="bi bi-file-earmark-excel-fill me-1 text-success"></i> Import Excel
            </button>
            <a href="<?= site_url('users/export') ?>" class="btn btn-sm btn-outline-secondary fw-semibold" title="Unduh template Excel (.xlsx) untuk impor">
                <i class="bi bi-download me-1"></i> Template Excel
            </a>
            <a href="<?= site_url('users/create') ?>" class="btn btn-sm btn-primary fw-bold shadow-sm px-3 ms-md-1">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- COMPACT FILTER TOOLBAR (RESPONSIVE GRID) -->
    <div class="card mb-3 border-0 shadow-sm rounded-4">
        <div class="card-body p-2 p-md-3">
            <form action="<?= site_url('users') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0" placeholder="Nama, NIP, atau Username..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Role / Peran</label>
                    <select name="role" id="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= (isset($filter_role) && $filter_role == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="direktur" <?= (isset($filter_role) && $filter_role == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="wadir" <?= (isset($filter_role) && $filter_role == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                        <option value="user" <?= (isset($filter_role) && $filter_role == 'user') ? 'selected' : '' ?>>Staf</option>
                        <option value="kabag" <?= (isset($filter_role) && $filter_role == 'kabag') ? 'selected' : '' ?>>Kabag (AAK/KUK)</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="unit" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Unit Kerja</label>
                    <select name="unit" id="unit" class="form-select form-select-sm">
                        <option value="">Semua Unit Kerja</option>
                        <option value="kosong" <?= (isset($filter_unit) && $filter_unit === 'kosong') ? 'selected' : '' ?>>-- Belum Diatur / Kosong --</option>
                        <?php foreach($unit_kerja_list as $uk): ?>
                            <option value="<?= esc($uk['nama_unit']) ?>" <?= (isset($filter_unit) && $filter_unit == $uk['nama_unit']) ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1.5">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1 fw-bold shadow-sm px-2" style="min-height: 31px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <?php if (!empty($search) || !empty($filter_role) || !empty($filter_unit) || (!empty($sortBy) && $sortBy !== 'users.nama_lengkap') || (!empty($sortOrder) && $sortOrder !== 'asc')): ?>
                        <a href="<?= site_url('users') ?>" class="btn btn-sm btn-outline-secondary px-2" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (session()->has('import_errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show small mb-3 shadow-sm py-2 px-3" role="alert">
            <strong>Terjadi kesalahan saat import:</strong>
            <ul class="mb-0 ps-3">
                <?php foreach (session('import_errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show small mb-3 shadow-sm py-2 px-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show small mb-3 shadow-sm py-2 px-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php
    // Helper function for sorting links
    $queryParams = $_GET;
    function getSortUrl($column, $queryParams, $currentSortBy, $currentSortOrder) {
        $params = $queryParams;
        $params['sort_by'] = $column;
        $params['sort_order'] = ($currentSortBy === $column && $currentSortOrder === 'asc') ? 'desc' : 'asc';
        unset($params['page_users']); // Reset pagination when sorting
        return site_url('users?' . http_build_query($params));
    }
    ?>

    <!-- COMPACT HIGH-DENSITY TABLE (RESPONSIVE GRID) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="table-responsive-smooth">
            <table class="table table-sm table-hover compact-table align-middle mb-0" id="dataTable" style="min-width: 980px;">
                <thead class="table-light border-bottom">
                    <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 38px;" class="text-center py-2">
                            <input class="form-check-input" type="checkbox" id="selectAll" title="Pilih semua">
                        </th>
                        <th style="width: 42px;" class="text-center py-2">No</th>
                        <th class="py-2" style="min-width: 220px;">
                            <a href="<?= getSortUrl('users.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="sort-link" title="Urutkan berdasar Nama">
                                Nama Lengkap & NIP
                                <?php if ($sortBy === 'users.nama_lengkap'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="py-2" style="min-width: 170px;">
                            <a href="<?= getSortUrl('users.jabatan', $queryParams, $sortBy, $sortOrder) ?>" class="sort-link" title="Urutkan berdasar Jabatan">
                                Jabatan / Pangkat
                                <?php if ($sortBy === 'users.jabatan'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="py-2" style="min-width: 175px;">Unit Kerja</th>
                        <th class="py-2" style="min-width: 140px;">
                            <a href="<?= getSortUrl('users.role', $queryParams, $sortBy, $sortOrder) ?>" class="sort-link" title="Urutkan berdasar Role">
                                Role Utama
                                <?php if ($sortBy === 'users.role'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="py-2 text-center" style="width: 90px;">Unit Kabag</th>
                        <th class="py-2" style="min-width: 190px;">Atasan Langsung</th>
                        <th class="py-2 text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                    <?php endif;
                    
                    foreach ($users as $user): 
                        $rowClass = ($i > 10) ? 'd-none user-row-hidden' : 'user-row-visible';
                        $isSuperAdminAccount = ($user['role'] === 'admin' || $user['username'] === 'admin');
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td class="text-center">
                            <?php if (!$isSuperAdminAccount): ?>
                                <input class="form-check-input user-checkbox" type="checkbox" value="<?= $user['id'] ?>" aria-label="Pilih pengguna <?= esc($user['nama_lengkap']) ?>">
                            <?php else: ?>
                                <i class="bi bi-shield-lock-fill text-muted opacity-50" title="Akun Superadmin dikunci"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-bold text-muted" style="font-size: 0.78rem;"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?= render_user_avatar($user, '', 30) ?>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 0.83rem; line-height: 1.25;"><?= esc($user['nama_lengkap']) ?></span>
                                    <small class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-card-text me-1"></i><?= esc($user['nip'] ?: ($user['username'] ?: '-')) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="d-block text-dark fw-semibold" style="font-size: 0.78rem; line-height: 1.2;"><?= esc($user['jabatan'] ?: '-') ?></span>
                            <small class="text-muted" style="font-size: 0.71rem;"><?= esc($user['pangkat'] ?: '-') ?></small>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'direktur'): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Direktur</span>
                            <?php elseif ($user['role'] === 'wadir'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Wakil Direktur</span>
                            <?php else: ?>
                                <div class="unit-kerja-select-wrapper">
                                    <select class="form-select form-select-sm unit-kerja-select" data-user-id="<?= $user['id'] ?>" aria-label="Pilih unit kerja untuk <?= esc($user['nama_lengkap']) ?>">
                                        <option value="">-- Pilih --</option>
                                        <?php if (!empty($unit_kerja_list)): ?>
                                            <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                                <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= ($user['unit'] == $unit_kerja['nama_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($unit_kerja['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="update-status small" style="font-size: 0.68rem;"></div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= render_role_badge($user['role']) ?>
                        </td>
                        <td class="text-center">
                            <?= render_unit_kabag_badge($user['unit_kabag'] ?? null) ?>
                        </td>
                        
                        <td>
                            <?php 
                                $atasan_display = $user['nama_atasan'] ?? null;
                                $is_auto_synced = false;
                                if ((empty($atasan_display) || $atasan_display === '-') && !empty($user['unit']) && isset($unitManagers[$user['unit']])) {
                                    $atasan_display = $unitManagers[$user['unit']];
                                    $is_auto_synced = true;
                                }
                            ?>
                            <?php if(!empty($atasan_display) && $atasan_display !== '-'): ?>
                                <span class="badge <?= $is_auto_synced ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2 py-0.5 text-truncate d-inline-block align-middle" style="max-width: 180px; font-size: 0.72rem; font-weight: 500;" <?= $is_auto_synced ? 'title="Disinkronisasi otomatis dari Unit Kerja"' : '' ?>>
                                    <i class="bi bi-person-check-fill me-1"></i><?= esc($atasan_display) ?>
                                    <?= $is_auto_synced ? ' <i class="bi bi-arrow-repeat"></i>' : '' ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted fst-italic" style="font-size: 0.72rem;">Tidak diset / Pimpinan</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-sm btn-outline-warning py-0 px-1.5" title="Edit Data Pengguna">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if (!$isSuperAdminAccount): ?>
                                    <a href="<?= site_url('users/delete/'.$user['id']) ?>" class="btn btn-sm btn-outline-danger py-0 px-1.5 btn-delete-user" data-user-name="<?= esc($user['nama_lengkap']) ?>" title="Hapus Pengguna">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary py-0 px-1.5" disabled title="Superadmin Kunci">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Skeleton Loader (Selalu dirender, JS yang mengatur hide/show) -->
        <div id="skeleton-loader" class="d-none p-3 text-center bg-light border-top">
            <div class="spinner-border spinner-border-sm text-primary mb-1" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">Memuat pengguna berikutnya...</div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT CSV -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header py-3 px-4 border-bottom">
                <h5 class="modal-title h6 fw-bold mb-0" id="importModalLabel"><i class="bi bi-file-earmark-excel-fill me-2 text-success"></i>Import Pengguna Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/import') ?>" method="post" enctype="multipart/form-data" autocomplete="off" id="formImportUsers">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-2">
                        Unduh template Excel untuk memastikan format kolom dan struktur data sesuai.
                    </p>
                    <a href="<?= site_url('users/export') ?>" class="btn btn-sm btn-outline-success mb-3 w-100 fw-semibold">
                        <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Unduh Template Excel (.xlsx)
                    </a>
                    <div class="mb-2">
                        <label for="file_excel" class="form-label small fw-bold">Pilih Berkas Excel / CSV</label>
                        <input class="form-control form-control-sm" type="file" name="file_excel" id="file_excel" required accept=".xlsx, .xls, .csv">
                    </div>
                </div>
                <div class="modal-footer py-2 px-4 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSubmitImport"><i class="bi bi-upload me-1"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BATCH EDIT ATASAN -->
<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header py-3 px-4 border-bottom">
                <h5 class="modal-title h6 fw-bold mb-0" id="batchEditModalLabel"><i class="bi bi-people-fill me-2 text-info"></i>Batch Edit Atasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/batch_update') ?>" method="post" autocomplete="off" id="formBatchUpdate">
                <?= csrf_field() ?>
                <input type="hidden" name="user_ids" id="batchUserIds">
                <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                <input type="hidden" name="return_qs" value="<?= esc($qs) ?>">
                <div class="modal-body p-4">
                    <p class="small mb-3">Ubah atasan langsung untuk <strong id="batchEditCount" class="text-primary fs-6">0</strong> pengguna terpilih:</p>
                    <div class="mb-2">
                        <label for="atasan_id_batch" class="form-label small fw-bold">Atasan Langsung Baru</label>
                        <select name="atasan_id" id="atasan_id_batch" class="form-select form-select-sm">
                            <option value="">-- Hapus Atasan (Tanpa Atasan) --</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <option value="<?= $boss['id'] ?>"><?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small" style="font-size: 0.72rem;">Semua pengguna terpilih akan diperbarui atasannya secara serentak.</div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-4 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSubmitBatch"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Double submit handling untuk modal
    $('#formImportUsers').on('submit', function() {
        $('#btnSubmitImport').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengimpor...').prop('disabled', true);
    });
    $('#formBatchUpdate').on('submit', function() {
        $('#btnSubmitBatch').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...').prop('disabled', true);
    });

    // Handling Konfirmasi Hapus Pengguna (SweetAlert2)
    $(document).on('click', '.btn-delete-user', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const userName = $(this).data('userName') || 'pengguna ini';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Pengguna?',
                html: `Akun <b>${userName}</b> beserta data kinerjanya akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus Pengguna',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        } else {
            if (confirm(`Hapus akun ${userName}?`)) {
                window.location.href = href;
            }
        }
    });
    const selectAllCheckbox = document.getElementById('selectAll');
    const batchEditBtn = document.getElementById('batchEditBtn');
    const batchEditModalEl = document.getElementById('batchEditModal');
    let batchEditModal = null;
    
    if (batchEditModalEl) {
        batchEditModal = new bootstrap.Modal(batchEditModalEl);
    }
    
    const batchUserIdsInput = document.getElementById('batchUserIds');
    const batchEditCountSpan = document.getElementById('batchEditCount');

    // Event Delegation untuk Checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Fungsi untuk tombol batch edit
    if (batchEditBtn) {
        batchEditBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox'))
                                     .filter(checkbox => checkbox.checked)
                                     .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Silakan pilih minimal satu pengguna untuk diedit.',
                        icon: 'warning',
                        confirmButtonColor: '#0d6efd'
                    });
                } else {
                    alert('Peringatan: Silakan pilih minimal satu pengguna untuk diedit.');
                }
                return;
            }

            batchUserIdsInput.value = selectedIds.join(',');
            batchEditCountSpan.textContent = selectedIds.length;
            batchEditModal.show();
        });
    }

    // --- EVENT DELEGATION UNTUK AJAX UNIT KERJA UPDATE ---
    $(document).on('change', '.unit-kerja-select', function(e) {
        const select = this;
        const userId = select.dataset.userId;
        const newUnit = select.value;
        const statusDiv = select.parentElement.querySelector('.update-status');

        if (statusDiv) {
            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';
        }

        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
        const csrfHash = csrfInput ? csrfInput.value : '';

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('unit', newUnit);
        formData.append(csrfTokenName, csrfHash);

        fetch('<?= site_url('users/ajax_update_unit') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data[csrfTokenName]) {
                document.querySelectorAll('input[name="' + csrfTokenName + '"]').forEach(input => {
                    input.value = data[csrfTokenName];
                });
            }

            if (statusDiv) {
                if (data.success) {
                    statusDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
                    statusDiv.className = 'update-status small mt-1 text-success';
                } else {
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Gagal';
                    statusDiv.className = 'update-status small mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (statusDiv) {
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            }
        });
    });

    // Inisialisasi Select2 untuk dropdown unit kerja
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.unit-kerja-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih --'
        });

        $(document).on('select2:open', () => {
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if(searchField) searchField.focus();
            }, 50);
        });
    }

    // --- CLIENT-SIDE INFINITE SCROLLING LOGIC ---
    let isLoading = false;
    const skeletonLoader = document.getElementById('skeleton-loader');

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading) {
            loadMoreData();
        }
    }, { rootMargin: "100px" });

    if (skeletonLoader) {
        observer.observe(skeletonLoader);
        checkRemainingRows();
    }

    function checkRemainingRows() {
        const hiddenRows = document.querySelectorAll('tr.user-row-hidden');
        if (hiddenRows.length === 0) {
            skeletonLoader.classList.add('d-none');
        } else {
            skeletonLoader.classList.remove('d-none');
        }
    }

    function loadMoreData() {
        const hiddenRows = document.querySelectorAll('tr.user-row-hidden');
        if (hiddenRows.length === 0) return;

        isLoading = true;
        setTimeout(() => {
            const limit = Math.min(10, hiddenRows.length);
            for (let i = 0; i < limit; i++) {
                hiddenRows[i].classList.remove('d-none', 'user-row-hidden');
                hiddenRows[i].classList.add('user-row-visible');
            }
            isLoading = false;
            checkRemainingRows();
        }, 500);
    }
});
</script>
<?= $this->endSection() ?>