<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
/* ==========================================================================
   ECC DESIGN SYSTEM — 8-POINT GRID SCALE & BENTO TOKENS
   ========================================================================== */
:root {
    --ecc-space-0-5: 4px;
    --ecc-space-1: 8px;
    --ecc-space-1-5: 12px;
    --ecc-space-2: 16px;
    --ecc-space-3: 24px;
    --ecc-space-4: 32px;
    --ecc-space-5: 40px;
    --ecc-space-6: 48px;
    --ecc-space-8: 64px;
}

@media (max-width: 767.98px) {
    .form-control, .form-select {
        font-size: 16px !important;
    }
}

/* Staggered Entrance Animations */
@keyframes bentoEntrance {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}

.bento-stagger {
    animation: bentoEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    will-change: transform, opacity;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.10s; }
.bento-stagger-3 { animation-delay: 0.16s; }

/* Tactile Micro-Interactions */
.btn-tactile {
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, background-color 0.15s ease;
}
.btn-tactile:active {
    transform: scale(0.97) !important;
}

/* Tabular Numerics */
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* KPI Bento Summary Card */
.kpi-bento-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.kpi-bento-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

/* Bento Table Standard */
.table-bento {
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
}
.table-bento thead th {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 14px;
    vertical-align: middle;
}
.table-bento tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border-color: #f1f5f9;
    font-size: 0.8125rem;
    line-height: 1.35;
    transition: background-color 0.15s ease;
}
.table-bento tbody tr:hover td {
    background-color: #f8fafc;
}

/* Custom Styled Select Dropdown in Table */
.unit-kerja-select-custom {
    height: 36px !important;
    font-size: 0.8125rem !important;
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    background-color: #ffffff !important;
    padding: 4px 28px 4px 10px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.unit-kerja-select-custom:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Dual-View Mobile Toggle */
@media (max-width: 767.98px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
}
@media (min-width: 768px) {
    .desktop-table-view {
        display: block !important;
    }
    .mobile-cards-view {
        display: none !important;
    }
}

/* Mobile Touch Cards */
.mobile-user-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.mobile-user-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

/* Select2 8-Point Grid Alignment */
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 8px !important;
    padding: 6px 12px !important;
    min-height: 38px !important;
    border-color: #cbd5e1 !important;
}
.select2-container--bootstrap-5.select2-container--focus .select2-selection,
.select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Accessibility: Prefers Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .bento-stagger, .btn-tactile, .kpi-bento-card, .mobile-user-card {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">

    <!-- PAGE HEADER & ACTION TOOLBAR (8-Point Grid Scale) -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna</h1>
            <p class="text-muted small mb-0">Manajemen akun pengguna, hierarki pimpinan, hak akses peran, dan penempatan unit kerja.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-info btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" id="batchEditBtn" style="height: 36px; font-size: 0.8125rem;" title="Ubah atasan banyak pengguna sekaligus">
                <i class="bi bi-people-fill"></i><span>Batch Edit Atasan</span>
            </button>
            <button type="button" class="btn btn-outline-success btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#importModal" style="height: 36px; font-size: 0.8125rem;" title="Impor pengguna dari file Excel">
                <i class="bi bi-file-earmark-excel-fill text-success"></i><span>Import Excel</span>
            </button>
            <a href="<?= site_url('users/export') ?>" class="btn btn-outline-secondary btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" style="height: 36px; font-size: 0.8125rem;" title="Unduh template Excel">
                <i class="bi bi-download"></i><span>Template Excel</span>
            </a>
            <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-tactile rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1.5 ms-md-1" style="height: 36px; font-size: 0.8125rem;">
                <i class="bi bi-person-plus-fill"></i><span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (session()->has('import_errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-4 mb-3 p-3 bento-stagger bento-stagger-1" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 mt-0.5"></i>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1">Terjadi kesalahan saat import data:</strong>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach (session('import_errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-4 mb-3 d-flex align-items-center gap-2 py-2.5 px-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div class="small fw-medium text-dark flex-grow-1"><?= session()->getFlashdata('success') ?></div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs rounded-4 mb-3 d-flex align-items-center gap-2 py-2.5 px-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div class="small fw-medium text-dark flex-grow-1"><?= session()->getFlashdata('error') ?></div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
        </div>
    <?php endif; ?>

    <!-- KPI SUMMARY STRIP (8-Point Grid Bento) -->
    <?php
    $totalUsers = count($users);
    $totalUnits = count($unit_kerja_list ?? []);
    $pimpinanCount = 0;
    $stafCount = 0;
    foreach ($users as $u) {
        if (in_array($u['role'] ?? '', ['admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk', 'manajemen', 'kanit', 'katim', 'kapokja'])) {
            $pimpinanCount++;
        } else {
            $stafCount++;
        }
    }
    ?>
    <div class="row g-3 mb-3 bento-stagger bento-stagger-2">
        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Total Pengguna</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1 num-tabular"><?= $totalUsers ?></h3>
                    <small class="text-secondary d-block mt-0.5" style="font-size: 0.72rem;">Akun aktif terdaftar di sistem</small>
                </div>
                <div class="bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Master Unit Kerja</span>
                    <h3 class="fw-bold text-success mb-0 mt-1 num-tabular"><?= $totalUnits ?></h3>
                    <small class="text-secondary d-block mt-0.5" style="font-size: 0.72rem;">Unit kerja struktural & fungsional</small>
                </div>
                <div class="bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-building-check fs-5"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Komposisi Peran</span>
                    <div class="fw-bold text-dark mt-1 lh-sm num-tabular" style="font-size: 0.9375rem;">
                        <span class="text-info-emphasis"><?= $pimpinanCount ?> Pimpinan</span> • <span class="text-secondary"><?= $stafCount ?> Staf</span>
                    </div>
                    <small class="text-secondary d-block mt-1" style="font-size: 0.72rem;">Distribusi jabatan struktural & operasional</small>
                </div>
                <div class="bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- COMPACT FILTER TOOLBAR (BENTO CARD) -->
    <div class="card mb-3 border-0 shadow-sm rounded-4 bento-stagger bento-stagger-2">
        <div class="card-body p-3">
            <form action="<?= site_url('users') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.6875rem; letter-spacing: 0.04em;">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted ps-2.5 pe-2" style="border-radius: 8px 0 0 8px; height: 38px;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-1" placeholder="Nama, NIP, atau Username..." value="<?= esc($search ?? '') ?>" style="border-radius: 0 8px 8px 0; height: 38px; font-size: 0.8125rem;">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.6875rem; letter-spacing: 0.04em;">Role / Peran</label>
                    <select name="role" id="role" class="form-select form-select-sm" style="height: 38px; border-radius: 8px; font-size: 0.8125rem;">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= (isset($filter_role) && $filter_role == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="direktur" <?= (isset($filter_role) && $filter_role == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="wadir" <?= (isset($filter_role) && $filter_role == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                        <option value="user" <?= (isset($filter_role) && $filter_role == 'user') ? 'selected' : '' ?>>Staf</option>
                        <option value="kabag" <?= (isset($filter_role) && $filter_role == 'kabag') ? 'selected' : '' ?>>Kabag (AAK/KUK)</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="unit" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.6875rem; letter-spacing: 0.04em;">Unit Kerja</label>
                    <select name="unit" id="unit" class="form-select form-select-sm" style="height: 38px; border-radius: 8px; font-size: 0.8125rem;">
                        <option value="">Semua Unit Kerja</option>
                        <option value="kosong" <?= (isset($filter_unit) && $filter_unit === 'kosong') ? 'selected' : '' ?>>-- Belum Diatur / Kosong --</option>
                        <?php foreach($unit_kerja_list as $uk): ?>
                            <option value="<?= esc($uk['nama_unit']) ?>" <?= (isset($filter_unit) && $filter_unit == $uk['nama_unit']) ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1.5">
                    <button type="submit" class="btn btn-primary btn-tactile flex-grow-1 fw-bold shadow-xs px-3 d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px; border-radius: 8px; font-size: 0.8125rem;">
                        <i class="bi bi-funnel-fill"></i><span>Filter</span>
                    </button>
                    <?php if (!empty($search) || !empty($filter_role) || !empty($filter_unit) || (!empty($sortBy) && $sortBy !== 'users.nama_lengkap') || (!empty($sortOrder) && $sortOrder !== 'asc')): ?>
                        <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary btn-tactile d-inline-flex align-items-center justify-content-center px-2.5" title="Reset Filter" style="height: 38px; border-radius: 8px;">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php
    $queryParams = $_GET;
    function getSortUrl($column, $queryParams, $currentSortBy, $currentSortOrder) {
        $params = $queryParams;
        $params['sort_by'] = $column;
        $params['sort_order'] = ($currentSortBy === $column && $currentSortOrder === 'asc') ? 'desc' : 'asc';
        unset($params['page_users']);
        return site_url('users?' . http_build_query($params));
    }
    ?>

    <!-- MAIN BENTO TABLE & MOBILE CONTAINER -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        
        <!-- DESKTOP TABLE VIEW (>=768px) -->
        <div class="table-responsive-smooth desktop-table-view">
            <table class="table table-hover align-middle table-bento mb-0" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input class="form-check-input" type="checkbox" id="selectAll" title="Pilih semua">
                        </th>
                        <th style="width: 50px;" class="text-center">
                            <a href="<?= getSortUrl('users.id', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center justify-content-center gap-1" title="Urutkan berdasar No / ID">
                                <span>No</span>
                                <?php if ($sortBy === 'users.id'): ?>
                                    <i class="bi bi-sort-numeric-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.65rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th style="min-width: 230px;">
                            <a href="<?= getSortUrl('users.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1" title="Urutkan berdasar Nama">
                                <span>Nama & NIP</span>
                                <?php if ($sortBy === 'users.nama_lengkap'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th style="min-width: 170px;">
                            <a href="<?= getSortUrl('users.jabatan', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1" title="Urutkan berdasar Jabatan">
                                <span>Jabatan / Pangkat</span>
                                <?php if ($sortBy === 'users.jabatan'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th style="min-width: 210px;">
                            <a href="<?= getSortUrl('users.unit', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1" title="Urutkan berdasar Unit Kerja">
                                <span>Unit Kerja</span>
                                <?php if ($sortBy === 'users.unit'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th style="width: 130px;">
                            <a href="<?= getSortUrl('users.role', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1" title="Urutkan berdasar Role">
                                <span>Role Utama</span>
                                <?php if ($sortBy === 'users.role'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="text-center" style="width: 100px;">
                            <a href="<?= getSortUrl('users.unit', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center justify-content-center gap-1" title="Urutkan berdasar Unit Kabag">
                                <span>Unit Kabag</span>
                                <?php if ($sortBy === 'users.unit'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th style="min-width: 220px;">
                            <a href="<?= getSortUrl('atasan.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1" title="Urutkan berdasar Atasan Langsung">
                                <span>Atasan Langsung</span>
                                <?php if ($sortBy === 'atasan.nama_lengkap'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.7rem;"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                <span class="fw-semibold">Tidak ada data pengguna ditemukan.</span>
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
                        <td class="text-center fw-bold text-muted num-tabular" style="font-size: 0.8rem;"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <?= render_user_avatar($user, $user['nama_lengkap'], 36) ?>
                                <div class="d-flex flex-column" style="min-width: 0;">
                                    <span class="fw-bold text-dark text-truncate lh-sm mb-0.5" style="font-size: 0.875rem;" title="<?= esc($user['nama_lengkap']) ?>"><?= esc($user['nama_lengkap']) ?></span>
                                    <span class="text-muted small num-tabular font-monospace" style="font-size: 0.72rem;">
                                        <i class="bi bi-person-badge me-1 opacity-75"></i><?= esc($user['nip'] ?: ($user['username'] ?: '-')) ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $hasJabatan = !empty(trim((string)($user['jabatan'] ?? '')));
                            $hasPangkat = !empty(trim((string)($user['pangkat'] ?? '')));
                            ?>
                            <?php if ($hasJabatan || $hasPangkat): ?>
                                <span class="d-block text-dark fw-semibold lh-sm" style="font-size: 0.8125rem;"><?= esc($user['jabatan'] ?: 'Staf Pelaksana') ?></span>
                                <small class="text-muted num-tabular d-block mt-0.5" style="font-size: 0.72rem;"><?= esc($user['pangkat'] ?: '-') ?></small>
                            <?php else: ?>
                                <span class="text-muted small fst-italic" style="font-size: 0.78rem;">Staf Pelaksana</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'direktur'): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Direktur</span>
                            <?php elseif ($user['role'] === 'wadir'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1">Wakil Direktur</span>
                            <?php else: ?>
                                <div class="unit-kerja-select-wrapper" style="max-width: 220px;">
                                    <select class="form-select form-select-sm unit-kerja-select-custom unit-kerja-select w-100" data-user-id="<?= $user['id'] ?>" aria-label="Pilih unit kerja untuk <?= esc($user['nama_lengkap']) ?>">
                                        <option value="">-- Pilih --</option>
                                        <?php if (!empty($unit_kerja_list)): ?>
                                            <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                                <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= ($user['unit'] == $unit_kerja['nama_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($unit_kerja['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="update-status small mt-1" style="min-height: 18px; font-size: 0.7rem;"></div>
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
                                <span class="badge <?= $is_auto_synced ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-success-subtle text-success border border-success-subtle' ?> px-2.5 py-1 text-wrap text-start lh-sm d-inline-flex align-items-center gap-1" style="font-size: 0.75rem; font-weight: 500;" <?= $is_auto_synced ? 'title="Disinkronisasi otomatis dari Unit Kerja"' : '' ?>>
                                    <i class="bi bi-person-check-fill flex-shrink-0"></i>
                                    <span><?= esc($atasan_display) ?></span>
                                    <?= $is_auto_synced ? ' <i class="bi bi-arrow-repeat flex-shrink-0"></i>' : '' ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted fst-italic small" style="font-size: 0.75rem;">Tidak diset / Pimpinan</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                            <div class="d-inline-flex align-items-center gap-1" role="group">
                                <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-outline-warning btn-tactile rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 30px; height: 30px;" title="Edit Data Pengguna">
                                    <i class="bi bi-pencil-square" style="font-size: 0.75rem;"></i>
                                </a>
                                <?php if (!$isSuperAdminAccount): ?>
                                    <form action="<?= site_url('users/delete/'.$user['id']) ?>" method="POST" class="d-inline form-delete-user">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-outline-danger btn-tactile rounded-circle d-inline-flex align-items-center justify-content-center p-0 btn-delete-user" style="width: 30px; height: 30px;" data-user-name="<?= esc($user['nama_lengkap']) ?>" title="Hapus Pengguna">
                                            <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center p-0" disabled style="width: 30px; height: 30px;" title="Superadmin Terkunci">
                                        <i class="bi bi-lock-fill" style="font-size: 0.75rem;"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS VIEW (<768px) -->
        <div class="mobile-cards-view p-2 p-md-3">
            <?php if (empty($users)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-person-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                    <span class="fw-semibold">Tidak ada data pengguna ditemukan.</span>
                </div>
            <?php else: ?>
                <?php 
                $mIndex = 1;
                foreach ($users as $user): 
                    $cardClass = ($mIndex > 10) ? 'd-none user-card-hidden' : 'user-card-visible';
                    $isSuperAdminAccount = ($user['role'] === 'admin' || $user['username'] === 'admin');
                    $mIndex++;

                    $atasan_display = $user['nama_atasan'] ?? null;
                    $is_auto_synced = false;
                    if ((empty($atasan_display) || $atasan_display === '-') && !empty($user['unit']) && isset($unitManagers[$user['unit']])) {
                        $atasan_display = $unitManagers[$user['unit']];
                        $is_auto_synced = true;
                    }
                ?>
                <div class="mobile-user-card <?= $cardClass ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!$isSuperAdminAccount): ?>
                                <input class="form-check-input user-checkbox" type="checkbox" value="<?= $user['id'] ?>" aria-label="Pilih <?= esc($user['nama_lengkap']) ?>">
                            <?php else: ?>
                                <i class="bi bi-shield-lock-fill text-muted opacity-50 small" title="Superadmin"></i>
                            <?php endif; ?>
                            <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small font-monospace">
                                <?= esc($user['nip'] ?: ($user['username'] ?: '-')) ?>
                            </span>
                        </div>
                        <div>
                            <?= render_role_badge($user['role']) ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2.5 mb-2.5">
                        <?= render_user_avatar($user, $user['nama_lengkap'], 38) ?>
                        <div class="lh-sm flex-grow-1" style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.9375rem;"><?= esc($user['nama_lengkap']) ?></div>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= esc($user['jabatan'] ?: 'Staf Pelaksana') ?> <?= !empty($user['pangkat']) ? '• ' . esc($user['pangkat']) : '' ?></small>
                        </div>
                    </div>

                    <div class="bg-light p-2.5 rounded-3 mb-2.5 border border-light-subtle small" style="font-size: 0.78rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <span class="text-muted fw-semibold">Unit Kerja:</span>
                            <span class="fw-medium text-dark text-end text-truncate" style="max-width: 200px;"><?= !empty($user['unit']) ? esc($user['unit']) : '<span class="text-muted fst-italic">Belum Diatur</span>' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Atasan:</span>
                            <?php if(!empty($atasan_display) && $atasan_display !== '-'): ?>
                                <span class="badge <?= $is_auto_synced ? 'bg-info-subtle text-info-emphasis' : 'bg-success-subtle text-success' ?> px-2 py-0.5 text-truncate" style="max-width: 190px;">
                                    <i class="bi bi-person-check-fill me-0.5"></i><?= esc($atasan_display) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Tidak diset / Pimpinan</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-1.5 pt-2 border-top">
                        <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                        <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-sm btn-outline-warning btn-tactile py-1 px-3 rounded-pill" style="font-size: 0.78rem;">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <?php if (!$isSuperAdminAccount): ?>
                            <form action="<?= site_url('users/delete/'.$user['id']) ?>" method="POST" class="d-inline form-delete-user">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tactile py-1 px-3 rounded-pill btn-delete-user" data-user-name="<?= esc($user['nama_lengkap']) ?>" style="font-size: 0.78rem;">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Skeleton Loader -->
        <div id="skeleton-loader" class="d-none p-3 text-center bg-light border-top">
            <div class="spinner-border spinner-border-sm text-primary mb-1" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">Memuat pengguna berikutnya...</div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="border-top: 4px solid #198754 !important;">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-file-earmark-excel-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="importModalLabel" style="font-size: 1rem;">Import Pengguna Excel</h6>
                        <small class="text-secondary" style="font-size: 0.75rem;">Unggah berkas spreadsheet untuk menambahkan pengguna massal</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/import') ?>" method="post" enctype="multipart/form-data" autocomplete="off" id="formImportUsers">
                <?= csrf_field() ?>
                <div class="modal-body p-3 p-md-4">
                    <p class="small text-muted mb-2.5">
                        Pastikan struktur kolom pada berkas Excel sesuai dengan template resmi sistem.
                    </p>
                    <a href="<?= site_url('users/export') ?>" class="btn btn-outline-success btn-tactile rounded-pill mb-3 w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1.5" style="height: 38px; font-size: 0.8125rem;">
                        <i class="bi bi-file-earmark-arrow-down-fill"></i><span>Unduh Template Excel (.xlsx)</span>
                    </a>
                    <div class="mb-2">
                        <label for="file_excel" class="form-label small fw-bold text-secondary mb-1">PILIH BERKAS EXCEL / CSV</label>
                        <input class="form-control" type="file" name="file_excel" id="file_excel" required accept=".xlsx, .xls, .csv" style="border-radius: 8px; font-size: 0.8125rem;">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-3 px-md-4 py-2.5 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs" data-bs-dismiss="modal" style="height: 36px; font-size: 0.8125rem;">Batal</button>
                    <button type="submit" class="btn btn-success btn-tactile rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" id="btnSubmitImport" style="height: 36px; font-size: 0.8125rem;">
                        <i class="bi bi-upload"></i><span>Mulai Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BATCH EDIT ATASAN -->
<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="border-top: 4px solid #0dcaf0 !important;">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="batchEditModalLabel" style="font-size: 1rem;">Batch Edit Atasan</h6>
                        <small class="text-secondary" style="font-size: 0.75rem;">Perbarui atasan langsung untuk beberapa pengguna sekaligus</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/batch_update') ?>" method="post" autocomplete="off" id="formBatchUpdate">
                <?= csrf_field() ?>
                <input type="hidden" name="user_ids" id="batchUserIds">
                <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                <input type="hidden" name="return_qs" value="<?= esc($qs) ?>">
                <div class="modal-body p-3 p-md-4">
                    <p class="small mb-3">Tugaskan atasan langsung baru untuk <strong id="batchEditCount" class="text-primary fs-6">0</strong> pengguna terpilih:</p>
                    <div class="mb-2">
                        <label for="atasan_id_batch" class="form-label small fw-bold text-secondary mb-1">ATASAN LANGSUNG BARU</label>
                        <select name="atasan_id" id="atasan_id_batch" class="form-select" style="border-radius: 8px; font-size: 0.8125rem;">
                            <option value="">-- Hapus Atasan (Tanpa Atasan) --</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <option value="<?= $boss['id'] ?>"><?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small mt-1 text-muted" style="font-size: 0.72rem;">Seluruh pengguna terpilih akan disinkronkan ke atasan yang dipilih.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-3 px-md-4 py-2.5 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs" data-bs-dismiss="modal" style="height: 36px; font-size: 0.8125rem;">Batal</button>
                    <button type="submit" class="btn btn-primary btn-tactile rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" id="btnSubmitBatch" style="height: 36px; font-size: 0.8125rem;">
                        <i class="bi bi-check-lg"></i><span>Simpan Perubahan</span>
                    </button>
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

    // Handling Konfirmasi Hapus Pengguna (SweetAlert2 & POST Form)
    $(document).on('click', '.btn-delete-user', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const userName = $(this).data('userName') || 'pengguna ini';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Pengguna?',
                html: `Akun <b>${userName}</b> beserta seluruh data kinerjanya akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus Pengguna',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm(`Hapus akun ${userName}?`)) {
                form.submit();
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
            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat spin-icon"></i> Menyimpan...';
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
                    statusDiv.className = 'update-status small mt-1 text-success fw-medium';
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
        const hiddenCards = document.querySelectorAll('.user-card-hidden');
        if (hiddenRows.length === 0 && hiddenCards.length === 0) {
            skeletonLoader.classList.add('d-none');
        } else {
            skeletonLoader.classList.remove('d-none');
        }
    }

    function loadMoreData() {
        const hiddenRows = document.querySelectorAll('tr.user-row-hidden');
        const hiddenCards = document.querySelectorAll('.user-card-hidden');
        if (hiddenRows.length === 0 && hiddenCards.length === 0) return;

        isLoading = true;
        setTimeout(() => {
            const limit = 10;
            for (let i = 0; i < limit; i++) {
                if (hiddenRows[i]) {
                    hiddenRows[i].classList.remove('d-none', 'user-row-hidden');
                    hiddenRows[i].classList.add('user-row-visible');
                }
                if (hiddenCards[i]) {
                    hiddenCards[i].classList.remove('d-none', 'user-card-hidden');
                    hiddenCards[i].classList.add('user-card-visible');
                }
            }
            isLoading = false;
            checkRemainingRows();
        }, 300);
    }
});
</script>
<?= $this->endSection() ?>