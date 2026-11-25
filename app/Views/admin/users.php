<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/vendor/select2/select2.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>" />
<style>
/* ==========================================================================
   ECC DESIGN SYSTEM — 8-POINT GRID SCALE & BENTO TOKENS
   ========================================================================== */
.ecc-text-muted-aa {
    color: #475569 !important;
}

/* Staggered Entrance Animations (zero resting will-change) */
@keyframes bentoEntrance {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}

.bento-stagger {
    animation: bentoEntrance 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
}
.bento-stagger-1 { animation-delay: 0.04s; }
.bento-stagger-2 { animation-delay: 0.10s; }
.bento-stagger-3 { animation-delay: 0.16s; }

/* Tactile Micro-Interactions */
.btn-tactile {
    transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, background-color 0.15s ease, border-color 0.15s ease;
}
.btn-tactile:active {
    transform: scale(0.97) !important;
}
.btn-tactile:focus-visible,
.sort-header-link:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2) !important;
}

/* Standardized Control & Button Sizing (36px Desktop, 44px Mobile) */
.btn-ecc-toolbar,
.btn-modal-action {
    min-height: 36px;
    padding: 8px 16px;
    font-size: 0.875rem;
}
.ecc-filter-label {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 4px;
}
.ecc-filter-control {
    height: 36px;
    border-radius: 8px;
    font-size: 0.875rem;
    border-color: #cbd5e1;
}
.ecc-filter-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}
.ecc-filter-icon {
    height: 36px;
    border-radius: 8px 0 0 8px;
    border-color: #cbd5e1;
}
.ecc-search-input {
    border-radius: 0 8px 8px 0;
}
.btn-icon-action {
    width: 32px;
    height: 32px;
    padding: 0;
    font-size: 0.75rem;
}
.btn-mobile-card-action {
    min-height: 36px;
    padding: 4px 12px;
    font-size: 0.75rem;
}

/* Tabular Numerics */
.num-tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* Typography Helpers on DESIGN.md Ramp */
.ecc-text-sm {
    font-size: 0.875rem;
}
.ecc-text-xs {
    font-size: 0.75rem;
}
.ecc-text-title {
    font-size: 0.95rem;
}
.ecc-badge-pill {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 50rem;
    line-height: 1.25;
}

/* KPI Bento Summary Card */
.kpi-bento-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 24px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.kpi-bento-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}
.kpi-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 12px;
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
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 16px;
    vertical-align: middle;
}
.table-bento tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-color: #f1f5f9;
    font-size: 0.875rem;
    line-height: 1.35;
    transition: background-color 0.15s ease;
}
.table-bento tbody tr:hover td {
    background-color: #f8fafc;
}
.sort-header-link {
    color: #475569;
    text-decoration: none;
    border-radius: 4px;
    padding: 4px;
    margin: -4px;
    transition: color 0.15s ease;
}
.sort-header-link:hover {
    color: #0d6efd;
}

/* Custom Styled Select Dropdown in Table */
.unit-kerja-select-custom {
    height: 36px !important;
    font-size: 0.875rem !important;
    border-radius: 8px !important;
    border-color: #cbd5e1 !important;
    background-color: #ffffff !important;
    padding: 4px 32px 4px 12px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.unit-kerja-select-custom:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Dual-View Mobile Toggle */
@media (max-width: 767.98px) {
    .form-control,
    .form-select,
    .ecc-filter-control {
        font-size: var(--bs-body-font-size, 1rem) !important;
    }
    .ecc-filter-control,
    .ecc-filter-icon {
        height: 44px;
    }
    .btn-ecc-toolbar,
    .btn-modal-action,
    .btn-mobile-card-action {
        min-height: 44px;
        height: auto;
    }
    .btn-icon-action {
        width: 44px;
        height: 44px;
    }
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
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.mobile-user-card:hover {
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

/* Modal Top Accents (DESIGN.md Tokens) */
.modal-accent-primary {
    border-top: 4px solid var(--ecc-primary, #0d6efd) !important;
}
.modal-accent-success {
    border-top: 4px solid #10b981 !important;
}
.modal-accent-sky {
    border-top: 4px solid #0284c7 !important;
}
.modal-icon-box {
    width: 40px;
    height: 40px;
    border-radius: 12px;
}

/* Select2 8-Point Grid Alignment */
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 8px !important;
    padding: 4px 12px !important;
    min-height: 36px !important;
    border-color: #cbd5e1 !important;
    font-size: 0.875rem !important;
}
.select2-container--bootstrap-5.select2-container--focus .select2-selection,
.select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}

/* Accessibility: Prefers Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .bento-stagger,
    .btn-tactile,
    .kpi-bento-card,
    .mobile-user-card,
    .sort-header-link,
    .table-bento tbody td {
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
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Kelola Pengguna</h1>
            <p class="ecc-text-muted-aa small mb-0">Manajemen akun pengguna, hierarki pimpinan, hak akses peran, dan penempatan unit kerja.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-primary btn-tactile btn-ecc-toolbar rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalRotasiAtasan" title="Alihkan staf dari atasan lama ke atasan baru mulai bulan tertentu">
                <i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Rotasi Atasan</span>
            </button>
            <button type="button" class="btn btn-outline-info btn-tactile btn-ecc-toolbar rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" id="batchEditBtn" title="Ubah atasan beberapa pengguna terpilih">
                <i class="bi bi-people-fill" aria-hidden="true"></i><span>Batch Edit Atasan</span>
            </button>
            <button type="button" class="btn btn-outline-success btn-tactile btn-ecc-toolbar rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importModal" title="Impor pengguna dari berkas Excel">
                <i class="bi bi-file-earmark-excel-fill" aria-hidden="true"></i><span>Impor Excel</span>
            </button>
            <a href="<?= site_url('users/export') ?>" class="btn btn-outline-secondary btn-tactile btn-ecc-toolbar rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" title="Unduh template Excel">
                <i class="bi bi-download" aria-hidden="true"></i><span>Template Excel</span>
            </a>
            <a href="<?= site_url('users/create') ?>" class="btn btn-primary btn-tactile btn-ecc-toolbar rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2 ms-md-1">
                <i class="bi bi-person-plus-fill" aria-hidden="true"></i><span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (session()->has('import_errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 p-3 bento-stagger bento-stagger-1" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5" aria-hidden="true"></i>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1">Terjadi kesalahan saat impor data:</strong>
                    <ul class="mb-0 ps-3 small">
                        <?php foreach (session('import_errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2 py-2 px-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-5" aria-hidden="true"></i>
            <div class="small fw-medium text-dark flex-grow-1"><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2 py-2 px-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5" aria-hidden="true"></i>
            <div class="small fw-medium text-dark flex-grow-1"><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
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
                    <span class="ecc-text-muted-aa ecc-text-xs fw-bold text-uppercase d-block" style="letter-spacing: 0.04em;">Total Pengguna</span>
                    <div class="h3 fw-bold text-primary mb-0 mt-1 num-tabular"><?= $totalUsers ?></div>
                    <small class="ecc-text-muted-aa ecc-text-xs d-block mt-1">Akun aktif terdaftar di ECC</small>
                </div>
                <div class="bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 kpi-icon-box" aria-hidden="true">
                    <i class="bi bi-people-fill fs-5" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="ecc-text-muted-aa ecc-text-xs fw-bold text-uppercase d-block" style="letter-spacing: 0.04em;">Master Unit Kerja</span>
                    <div class="h3 fw-bold text-success mb-0 mt-1 num-tabular"><?= $totalUnits ?></div>
                    <small class="ecc-text-muted-aa ecc-text-xs d-block mt-1">Unit kerja struktural &amp; fungsional</small>
                </div>
                <div class="bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0 kpi-icon-box" aria-hidden="true">
                    <i class="bi bi-building-check fs-5" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="ecc-text-muted-aa ecc-text-xs fw-bold text-uppercase d-block" style="letter-spacing: 0.04em;">Komposisi Peran</span>
                    <div class="fw-bold text-dark mt-1 lh-sm num-tabular ecc-text-title">
                        <span class="text-info-emphasis"><?= $pimpinanCount ?> Pimpinan</span> &middot; <span class="ecc-text-muted-aa"><?= $stafCount ?> Staf</span>
                    </div>
                    <small class="ecc-text-muted-aa ecc-text-xs d-block mt-1">Distribusi jabatan struktural &amp; operasional</small>
                </div>
                <div class="bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center flex-shrink-0 kpi-icon-box" aria-hidden="true">
                    <i class="bi bi-shield-check fs-5" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- COMPACT FILTER TOOLBAR (BENTO CARD) -->
    <div class="card mb-3 border-0 shadow-sm rounded-4 bento-stagger bento-stagger-2">
        <div class="card-body p-3">
            <form action="<?= site_url('users') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="search" class="form-label ecc-filter-label">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 ecc-text-muted-aa ps-2 pe-2 ecc-filter-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-1 ecc-filter-control ecc-search-input" placeholder="Nama, NIP, atau Nama Pengguna..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="role" class="form-label ecc-filter-label">Peran</label>
                    <select name="role" id="role" class="form-select form-select-sm ecc-filter-control">
                        <option value="">Semua Peran</option>
                        <option value="admin" <?= (isset($filter_role) && $filter_role == 'admin') ? 'selected' : '' ?>>Administrator</option>
                        <option value="direktur" <?= (isset($filter_role) && $filter_role == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="wadir" <?= (isset($filter_role) && $filter_role == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                        <option value="kabag" <?= (isset($filter_role) && $filter_role == 'kabag') ? 'selected' : '' ?>>Kabag (AAK/KUK)</option>
                        <option value="manajemen" <?= (isset($filter_role) && $filter_role == 'manajemen') ? 'selected' : '' ?>>Kanit / Katim</option>
                        <option value="user" <?= (isset($filter_role) && $filter_role == 'user') ? 'selected' : '' ?>>Staf</option>
                        <option value="kepegawaian" <?= (isset($filter_role) && $filter_role == 'kepegawaian') ? 'selected' : '' ?>>Kepegawaian</option>
                        <option value="dosen" <?= (isset($filter_role) && $filter_role == 'dosen') ? 'selected' : '' ?>>Dosen</option>
                        <option value="spm" <?= (isset($filter_role) && $filter_role == 'spm') ? 'selected' : '' ?>>SPM</option>
                        <option value="tugas_belajar" <?= (isset($filter_role) && $filter_role == 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                        <option value="ketarunaan" <?= (isset($filter_role) && $filter_role == 'ketarunaan') ? 'selected' : '' ?>>Ketarunaan (PIC Taruna)</option>
                        <option value="ketarunaan_validator" <?= (isset($filter_role) && $filter_role == 'ketarunaan_validator') ? 'selected' : '' ?>>Validator Ketarunaan</option>
                        <option value="pusbangkar" <?= (isset($filter_role) && $filter_role == 'pusbangkar') ? 'selected' : '' ?>>Pusbangkar</option>
                        <option value="karir_alumni" <?= (isset($filter_role) && $filter_role == 'karir_alumni') ? 'selected' : '' ?>>Karier &amp; Alumni</option>
                        <option value="kerjasama" <?= (isset($filter_role) && $filter_role == 'kerjasama') ? 'selected' : '' ?>>Kerjasama &amp; Mitra</option>
                        <option value="taruna" <?= (isset($filter_role) && $filter_role == 'taruna') ? 'selected' : '' ?>>Taruna</option>
                        <option value="alumni" <?= (isset($filter_role) && $filter_role == 'alumni') ? 'selected' : '' ?>>Alumni</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="unit" class="form-label ecc-filter-label">Unit Kerja</label>
                    <select name="unit" id="unit" class="form-select form-select-sm ecc-filter-control">
                        <option value="">Semua Unit Kerja</option>
                        <option value="kosong" <?= (isset($filter_unit) && $filter_unit === 'kosong') ? 'selected' : '' ?>>Belum Diatur</option>
                        <?php foreach($unit_kerja_list as $uk): ?>
                            <option value="<?= esc($uk['nama_unit']) ?>" <?= (isset($filter_unit) && $filter_unit == $uk['nama_unit']) ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="status_pegawai" class="form-label ecc-filter-label">Status</label>
                    <select name="status_pegawai" id="status_pegawai" class="form-select form-select-sm ecc-filter-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?= (isset($filter_status_pegawai) && $filter_status_pegawai == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                        <option value="mutasi_keluar" <?= (isset($filter_status_pegawai) && $filter_status_pegawai == 'mutasi_keluar') ? 'selected' : '' ?>>Mutasi Keluar</option>
                        <option value="pensiun" <?= (isset($filter_status_pegawai) && $filter_status_pegawai == 'pensiun') ? 'selected' : '' ?>>Pensiun</option>
                        <option value="nonaktif" <?= (isset($filter_status_pegawai) && $filter_status_pegawai == 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-tactile btn-ecc-toolbar flex-grow-1 fw-bold shadow-sm px-3 d-inline-flex align-items-center justify-content-center gap-1" style="border-radius: 8px;">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i><span>Filter</span>
                    </button>
                    <?php if (!empty($search) || !empty($filter_role) || !empty($filter_unit) || !empty($filter_status_pegawai) || (!empty($sortBy) && $sortBy !== 'users.nama_lengkap') || (!empty($sortOrder) && $sortOrder !== 'asc')): ?>
                        <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary btn-tactile btn-ecc-toolbar d-inline-flex align-items-center justify-content-center px-3" title="Reset Filter" aria-label="Atur ulang filter" style="border-radius: 8px;">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
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
    $getAriaSort = function (string $column) use ($sortBy, $sortOrder): string {
        if ($sortBy !== $column) {
            return 'none';
        }
        return ($sortOrder === 'asc') ? 'ascending' : 'descending';
    };
    ?>

    <!-- MAIN BENTO TABLE & MOBILE CONTAINER -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        
        <!-- DESKTOP TABLE VIEW (>=768px) -->
        <div class="table-responsive-smooth desktop-table-view">
            <table class="table table-hover align-middle table-bento mb-0" id="dataTable" width="100%">
                <thead>
                    <tr>
                        <th scope="col" style="width: 40px;" class="text-center">
                            <input class="form-check-input" type="checkbox" id="selectAll" title="Pilih semua" aria-label="Pilih semua pengguna">
                        </th>
                        <th scope="col" style="width: 56px;" class="text-center" aria-sort="<?= $getAriaSort('users.id') ?>">
                            <a href="<?= getSortUrl('users.id', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center justify-content-center gap-1" title="Urutkan berdasarkan nomor">
                                <span>No</span>
                                <?php if ($sortBy === 'users.id'): ?>
                                    <i class="bi bi-sort-numeric-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" style="min-width: 232px;" aria-sort="<?= $getAriaSort('users.nama_lengkap') ?>">
                            <a href="<?= getSortUrl('users.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center gap-1" title="Urutkan berdasarkan nama">
                                <span>Nama &amp; NIP</span>
                                <?php if ($sortBy === 'users.nama_lengkap'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" style="min-width: 176px;" aria-sort="<?= $getAriaSort('users.jabatan') ?>">
                            <a href="<?= getSortUrl('users.jabatan', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center gap-1" title="Urutkan berdasarkan jabatan">
                                <span>Jabatan / Pangkat</span>
                                <?php if ($sortBy === 'users.jabatan'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" style="min-width: 208px;" aria-sort="<?= $getAriaSort('users.unit') ?>">
                            <a href="<?= getSortUrl('users.unit', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center gap-1" title="Urutkan berdasarkan unit kerja">
                                <span>Unit Kerja</span>
                                <?php if ($sortBy === 'users.unit'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" style="min-width: 168px;" aria-sort="<?= $getAriaSort('users.role') ?>">
                            <a href="<?= getSortUrl('users.role', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center gap-1" title="Urutkan berdasarkan peran">
                                <span>Peran</span>
                                <?php if ($sortBy === 'users.role'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" class="text-center" style="width: 104px;" aria-sort="<?= $getAriaSort('users.unit') ?>">
                            <a href="<?= getSortUrl('users.unit', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center justify-content-center gap-1" title="Urutkan berdasarkan unit kabag">
                                <span>Unit Kabag</span>
                                <?php if ($sortBy === 'users.unit'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" style="min-width: 224px;" aria-sort="<?= $getAriaSort('atasan.nama_lengkap') ?>">
                            <a href="<?= getSortUrl('atasan.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="sort-header-link d-inline-flex align-items-center gap-1" title="Urutkan berdasarkan atasan langsung">
                                <span>Atasan Langsung</span>
                                <?php if ($sortBy === 'atasan.nama_lengkap'): ?>
                                    <i class="bi bi-sort-alpha-<?= ($sortOrder === 'asc') ? 'down' : 'down-alt' ?> text-primary fw-bold" aria-hidden="true"></i>
                                <?php else: ?>
                                    <i class="bi bi-arrow-down-up opacity-50 ecc-text-xs" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th scope="col" class="text-center" style="width: 112px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 ecc-text-muted-aa">
                                <span class="fw-semibold">Belum ada data pengguna untuk filter ini.</span>
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
                                <i class="bi bi-shield-lock-fill ecc-text-muted-aa opacity-50" title="Akun Superadmin dikunci" aria-hidden="true"></i>
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-bold ecc-text-muted-aa num-tabular ecc-text-xs"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?= render_user_avatar($user, $user['nama_lengkap'], 36) ?>
                                <div class="d-flex flex-column" style="min-width: 0;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold text-dark text-truncate lh-sm ecc-text-sm" title="<?= esc($user['nama_lengkap']) ?>"><?= esc($user['nama_lengkap']) ?></span>
                                        <?php if (($user['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-badge-pill" title="Mutasi Keluar <?= !empty($user['tmt_selesai']) ? 'TMT ' . date('d/m/Y', strtotime($user['tmt_selesai'])) : '' ?>">Mutasi</span>
                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ecc-badge-pill" title="Pensiun">Pensiun</span>
                                        <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle ecc-badge-pill" title="Nonaktif">Nonaktif</span>
                                        <?php endif; ?>
                                        <?php if (!empty($user['dispensasi_lampau_sampai']) && $user['dispensasi_lampau_sampai'] >= date('Y-m-d')): ?>
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle ecc-badge-pill" title="Dispensasi s.d. <?= date('d/m/Y', strtotime($user['dispensasi_lampau_sampai'])) ?>">Dispensasi</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="ecc-text-muted-aa ecc-text-xs num-tabular font-monospace">
                                        <?= esc($user['nip'] ?: ($user['username'] ?: '-')) ?>
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
                                <span class="d-block text-dark fw-semibold lh-sm ecc-text-sm"><?= esc($user['jabatan'] ?: 'Staf Pelaksana') ?></span>
                                <small class="ecc-text-muted-aa num-tabular d-block mt-1 ecc-text-xs"><?= esc($user['pangkat'] ?: '-') ?></small>
                            <?php else: ?>
                                <span class="ecc-text-muted-aa ecc-text-xs fst-italic">Staf Pelaksana</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['role'] === 'direktur'): ?>
                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 ecc-text-xs">Direktur</span>
                            <?php elseif ($user['role'] === 'wadir'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 ecc-text-xs">Wakil Direktur</span>
                            <?php else: ?>
                                <div class="unit-kerja-select-wrapper" style="max-width: 224px;">
                                    <select class="form-select form-select-sm unit-kerja-select-custom unit-kerja-select w-100" data-user-id="<?= $user['id'] ?>" aria-label="Pilih unit kerja untuk <?= esc($user['nama_lengkap']) ?>">
                                        <option value="">Pilih Unit Kerja</option>
                                        <?php if (!empty($unit_kerja_list)): ?>
                                            <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                                <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= ($user['unit'] == $unit_kerja['nama_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($unit_kerja['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="update-status ecc-text-xs mt-1" style="min-height: 16px;" aria-live="polite"></div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-start gap-1">
                                <?= render_role_badge($user['role']) ?>
                                <?php if (!empty($user['secondary_roles'])): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($user['secondary_roles'] as $secRole): ?>
                                            <?= render_role_badge($secRole) ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <?= render_unit_kabag_badge($user['unit_kabag'] ?? null) ?>
                        </td>
                        <td>
                            <?php 
                                $atasan_display = $user['nama_atasan'] ?? null;
                                $is_auto_synced = false;
                                $isExecutiveRow = in_array(strtolower((string) ($user['role'] ?? '')), ['direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'], true);
                                if (!$isExecutiveRow && (empty($atasan_display) || $atasan_display === '-') && !empty($user['unit']) && isset($unitManagers[$user['unit']])) {
                                    $atasan_display = $unitManagers[$user['unit']];
                                    $is_auto_synced = true;
                                }
                            ?>
                            <?php if(!empty($atasan_display) && $atasan_display !== '-'): ?>
                                <span class="badge <?= $is_auto_synced ? 'bg-info-subtle text-info-emphasis border border-info-subtle' : 'bg-success-subtle text-success-emphasis border border-success-subtle' ?> px-2 py-1 text-wrap text-start lh-sm ecc-text-xs fw-medium" <?= $is_auto_synced ? 'title="Disinkronisasi otomatis dari Unit Kerja"' : '' ?>>
                                    <span><?= esc($atasan_display) ?></span>
                                </span>
                            <?php else: ?>
                                <span class="ecc-text-muted-aa fst-italic ecc-text-xs">Belum diatur / Pimpinan</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                            <div class="d-inline-flex align-items-center gap-1" role="group" aria-label="Aksi untuk <?= esc($user['nama_lengkap']) ?>">
                                <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-outline-warning btn-tactile btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center" title="Edit Data Pengguna" aria-label="Edit <?= esc($user['nama_lengkap']) ?>">
                                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-tactile btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center btn-riwayat-penugasan" data-user-id="<?= $user['id'] ?>" data-user-name="<?= esc($user['nama_lengkap']) ?>" data-user-nip="<?= esc($user['nip'] ?: '-') ?>" data-user-unit="<?= esc($user['unit'] ?: '') ?>" data-user-jabatan="<?= esc($user['jabatan'] ?: '') ?>" data-user-role="<?= esc($user['role'] ?: 'pegawai') ?>" data-user-atasan="<?= esc($user['atasan_id'] ?? '') ?>" title="Riwayat Penugasan &amp; Atasan" aria-label="Riwayat penugasan <?= esc($user['nama_lengkap']) ?>">
                                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                                </button>
                                <?php if (!$isSuperAdminAccount): ?>
                                    <form action="<?= site_url('users/delete/'.$user['id']) ?>" method="POST" class="d-inline form-delete-user">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-outline-danger btn-tactile btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center btn-delete-user" data-user-name="<?= esc($user['nama_lengkap']) ?>" title="Hapus Pengguna" aria-label="Hapus <?= esc($user['nama_lengkap']) ?>">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-secondary btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center" disabled title="Superadmin Terkunci" aria-label="Akun Superadmin terkunci">
                                        <i class="bi bi-lock-fill" aria-hidden="true"></i>
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
                <div class="text-center py-4 ecc-text-muted-aa">
                    <span class="fw-semibold">Belum ada data pengguna untuk filter ini.</span>
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
                    $isExecutiveRow = in_array(strtolower((string) ($user['role'] ?? '')), ['direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'], true);
                    if (!$isExecutiveRow && (empty($atasan_display) || $atasan_display === '-') && !empty($user['unit']) && isset($unitManagers[$user['unit']])) {
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
                                <i class="bi bi-shield-lock-fill ecc-text-muted-aa opacity-50 small" title="Superadmin" aria-hidden="true"></i>
                            <?php endif; ?>
                            <span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1 ecc-text-xs font-monospace">
                                <?= esc($user['nip'] ?: ($user['username'] ?: '-')) ?>
                            </span>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <?= render_role_badge($user['role']) ?>
                            <?php if (!empty($user['secondary_roles'])): ?>
                                <div class="d-flex flex-wrap justify-content-end gap-1">
                                    <?php foreach ($user['secondary_roles'] as $secRole): ?>
                                        <?= render_role_badge($secRole) ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <?= render_user_avatar($user, $user['nama_lengkap'], 40) ?>
                        <div class="lh-sm flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark text-truncate ecc-text-title"><?= esc($user['nama_lengkap']) ?></span>
                                <?php if (($user['status_pegawai'] ?? 'aktif') === 'mutasi_keluar'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-badge-pill" title="Mutasi Keluar <?= !empty($user['tmt_selesai']) ? 'TMT ' . date('d/m/Y', strtotime($user['tmt_selesai'])) : '' ?>">Mutasi</span>
                                <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'pensiun'): ?>
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle ecc-badge-pill" title="Pensiun">Pensiun</span>
                                <?php elseif (($user['status_pegawai'] ?? 'aktif') === 'nonaktif'): ?>
                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle ecc-badge-pill" title="Nonaktif">Nonaktif</span>
                                <?php endif; ?>
                                <?php if (!empty($user['dispensasi_lampau_sampai']) && $user['dispensasi_lampau_sampai'] >= date('Y-m-d')): ?>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle ecc-badge-pill" title="Dispensasi s.d. <?= date('d/m/Y', strtotime($user['dispensasi_lampau_sampai'])) ?>">Dispensasi</span>
                                <?php endif; ?>
                            </div>
                            <small class="ecc-text-muted-aa ecc-text-xs"><?= esc($user['jabatan'] ?: 'Staf Pelaksana') ?> <?= !empty($user['pangkat']) ? '&middot; ' . esc($user['pangkat']) : '' ?></small>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3 border border-light-subtle ecc-text-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="ecc-text-muted-aa fw-semibold">Unit Kerja:</span>
                            <span class="fw-medium text-dark text-end text-truncate" style="max-width: 200px;"><?= !empty($user['unit']) ? esc($user['unit']) : '<span class="ecc-text-muted-aa fst-italic">Belum Diatur</span>' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="ecc-text-muted-aa fw-semibold">Atasan:</span>
                            <?php if(!empty($atasan_display) && $atasan_display !== '-'): ?>
                                <span class="badge <?= $is_auto_synced ? 'bg-info-subtle text-info-emphasis' : 'bg-success-subtle text-success-emphasis' ?> px-2 py-1 text-truncate" style="max-width: 192px;">
                                    <?= esc($atasan_display) ?>
                                </span>
                            <?php else: ?>
                                <span class="ecc-text-muted-aa fst-italic">Belum diatur / Pimpinan</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top flex-wrap">
                        <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                        <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-sm btn-outline-warning btn-tactile btn-mobile-card-action rounded-pill d-inline-flex align-items-center gap-1">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i><span>Edit</span>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-tactile btn-mobile-card-action rounded-pill btn-riwayat-penugasan d-inline-flex align-items-center gap-1" data-user-id="<?= $user['id'] ?>" data-user-name="<?= esc($user['nama_lengkap']) ?>" data-user-nip="<?= esc($user['nip'] ?: '-') ?>" data-user-unit="<?= esc($user['unit'] ?: '') ?>" data-user-jabatan="<?= esc($user['jabatan'] ?: '') ?>" data-user-role="<?= esc($user['role'] ?: 'pegawai') ?>" data-user-atasan="<?= esc($user['atasan_id'] ?? '') ?>">
                            <i class="bi bi-clock-history" aria-hidden="true"></i><span>Penugasan</span>
                        </button>
                        <?php if (!$isSuperAdminAccount): ?>
                            <form action="<?= site_url('users/delete/'.$user['id']) ?>" method="POST" class="d-inline form-delete-user">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tactile btn-mobile-card-action rounded-pill btn-delete-user d-inline-flex align-items-center gap-1" data-user-name="<?= esc($user['nama_lengkap']) ?>">
                                    <i class="bi bi-trash" aria-hidden="true"></i><span>Hapus</span>
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
            <div class="ecc-text-muted-aa ecc-text-xs">Memuat pengguna berikutnya...</div>
        </div>
    </div>
</div>

<!-- MODAL IMPOR EXCEL -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden modal-accent-success">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm modal-icon-box" aria-hidden="true">
                        <i class="bi bi-file-earmark-excel-fill fs-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="h5 modal-title fw-bold text-dark mb-0 ecc-text-title" id="importModalLabel">Impor Pengguna Excel</h2>
                        <small class="ecc-text-muted-aa ecc-text-xs">Unggah berkas Excel untuk menambahkan pengguna massal</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= site_url('users/import') ?>" method="post" enctype="multipart/form-data" autocomplete="off" id="formImportUsers">
                <?= csrf_field() ?>
                <div class="modal-body p-3 p-md-4">
                    <p class="ecc-text-sm ecc-text-muted-aa mb-3">
                        Pastikan struktur kolom pada berkas Excel sesuai dengan template resmi ECC.
                    </p>
                    <a href="<?= site_url('users/export') ?>" class="btn btn-outline-success btn-tactile btn-modal-action rounded-pill mb-3 w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-arrow-down-fill" aria-hidden="true"></i><span>Unduh Template Excel</span>
                    </a>
                    <div class="mb-2">
                        <label for="file_excel" class="form-label ecc-filter-label">Pilih Berkas Excel / CSV</label>
                        <input class="form-control ecc-filter-control" type="file" name="file_excel" id="file_excel" required accept=".xlsx, .xls, .csv">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-3 px-md-4 py-3 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" id="btnSubmitImport">
                        <i class="bi bi-upload" aria-hidden="true"></i><span>Mulai Impor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL BATCH EDIT ATASAN -->
<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden modal-accent-sky">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm modal-icon-box" aria-hidden="true">
                        <i class="bi bi-people-fill fs-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="h5 modal-title fw-bold text-dark mb-0 ecc-text-title" id="batchEditModalLabel">Batch Edit Atasan</h2>
                        <small class="ecc-text-muted-aa ecc-text-xs">Perbarui atasan langsung untuk beberapa pengguna sekaligus</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= site_url('users/batch_update') ?>" method="post" autocomplete="off" id="formBatchUpdate">
                <?= csrf_field() ?>
                <input type="hidden" name="user_ids" id="batchUserIds">
                <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                <input type="hidden" name="return_qs" value="<?= esc($qs) ?>">
                <div class="modal-body p-3 p-md-4">
                    <?php
                        $bulanIndoModal = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $curMonthModal  = (int) date('n');
                        $curYearModal   = (int) date('Y');
                    ?>
                    <p class="ecc-text-sm mb-3">Tugaskan atasan langsung baru untuk <strong id="batchEditCount" class="text-primary num-tabular">0</strong> pengguna terpilih:</p>
                    <div class="mb-3">
                        <label for="atasan_id_batch" class="form-label ecc-filter-label">Atasan Langsung Baru</label>
                        <select name="atasan_id" id="atasan_id_batch" class="form-select ecc-filter-control">
                            <option value="">Tanpa Atasan</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <option value="<?= $boss['id'] ?>"><?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="ecc-filter-label">Jenis Perubahan</div>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-start gap-2 p-2 bg-light border rounded-3 mb-0" style="cursor: pointer;">
                                <input type="radio" name="mode_perubahan" value="mutasi" class="form-check-input mt-1 batch-mode-radio" checked>
                                <span class="ecc-text-xs">
                                    <strong class="d-block text-dark">Rotasi / Mutasi Baru</strong>
                                    <span class="ecc-text-muted-aa">Penilaian bulan sebelumnya tetap tercatat pada atasan lama.</span>
                                </span>
                            </label>
                            <label class="d-flex align-items-start gap-2 p-2 bg-white border rounded-3 mb-0" style="cursor: pointer;">
                                <input type="radio" name="mode_perubahan" value="koreksi" class="form-check-input mt-1 batch-mode-radio">
                                <span class="ecc-text-xs">
                                    <strong class="d-block text-dark">Koreksi Salah Input</strong>
                                    <span class="ecc-text-muted-aa">Hanya memperbarui profil aktif tanpa memotong riwayat penugasan.</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="row g-2 mb-2" id="batchPeriodeWrapper">
                        <div class="col-7">
                            <label for="batchBulanEfektif" class="form-label ecc-filter-label">Mulai Efektif Bulan</label>
                            <select name="bulan_efektif" id="batchBulanEfektif" class="form-select form-select-sm ecc-filter-control">
                                <?php foreach ($bulanIndoModal as $idxBln => $nmBln): ?>
                                    <option value="<?= $idxBln + 1 ?>" <?= ($curMonthModal === $idxBln + 1) ? 'selected' : '' ?>><?= esc($nmBln) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="batchTahunEfektif" class="form-label ecc-filter-label">Tahun</label>
                            <input type="number" name="tahun_efektif" id="batchTahunEfektif" class="form-control form-control-sm num-tabular ecc-filter-control" value="<?= $curYearModal ?>" min="2020" max="2099">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-3 px-md-4 py-3 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-2" id="btnSubmitBatch">
                        <i class="bi bi-check-lg" aria-hidden="true"></i><span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ROTASI ATASAN LANGSUNG MASSAL PER PEJABAT / UNIT -->
<div class="modal fade" id="modalRotasiAtasan" tabindex="-1" aria-labelledby="modalRotasiAtasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden modal-accent-primary">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div>
                    <h2 class="h5 modal-title fw-bold text-dark mb-0 ecc-text-title" id="modalRotasiAtasanLabel">Rotasi Atasan Langsung Massal</h2>
                    <small class="ecc-text-muted-aa ecc-text-xs">Alihkan seluruh staf dari pejabat lama ke pejabat baru mulai bulan efektif</small>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= site_url('users/rotasi-atasan') ?>" method="post" autocomplete="off" id="formRotasiAtasan">
                <?= csrf_field() ?>
                <input type="hidden" name="return_qs" value="<?= esc($qs) ?>">
                <div class="modal-body p-3 p-md-4">
                    <div class="alert alert-primary bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-3 ecc-text-xs py-2 px-3 mb-3">
                        Penilaian bulan sebelum bulan efektif tetap tercatat pada <strong>Atasan Lama</strong>. Mulai bulan efektif, staf beralih ke <strong>Atasan Baru</strong>.
                    </div>
                    <div class="mb-3">
                        <label for="rotasiAtasanLama" class="form-label ecc-filter-label">Atasan Lama (Yang Diganti) <span class="text-danger">*</span></label>
                        <select name="atasan_lama_id" id="rotasiAtasanLama" class="form-select form-select-sm ecc-filter-control" required>
                            <option value="">Pilih Atasan Lama</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <?php $jmlStafBoss = (int) ($staf_count_by_atasan[(int) $boss['id']] ?? 0); ?>
                                <option value="<?= esc($boss['id']) ?>">
                                    <?= esc($boss['nama_lengkap']) ?> — <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?> (<?= $jmlStafBoss ?> staf)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="rotasiAtasanBaru" class="form-label ecc-filter-label">Atasan Baru (Pengganti) <span class="text-danger">*</span></label>
                        <select name="atasan_baru_id" id="rotasiAtasanBaru" class="form-select form-select-sm ecc-filter-control" required>
                            <option value="">Pilih Atasan Baru</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <option value="<?= esc($boss['id']) ?>">
                                    <?= esc($boss['nama_lengkap']) ?> — <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="rotasiUnitFilter" class="form-label ecc-filter-label">Batasi Unit Kerja (Opsional)</label>
                        <select name="unit_filter" id="rotasiUnitFilter" class="form-select form-select-sm ecc-filter-control">
                            <option value="">Semua Staf di Bawah Atasan Lama</option>
                            <?php foreach ($unit_kerja_list as $uk): ?>
                                <option value="<?= esc($uk['nama_unit']) ?>"><?= esc($uk['nama_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label for="rotasiBulanEfektif" class="form-label ecc-filter-label">Mulai Efektif Bulan <span class="text-danger">*</span></label>
                            <select name="bulan_efektif" id="rotasiBulanEfektif" class="form-select form-select-sm ecc-filter-control" required>
                                <?php foreach ($bulanIndoModal as $idxBln => $nmBln): ?>
                                    <option value="<?= $idxBln + 1 ?>" <?= ($curMonthModal === $idxBln + 1) ? 'selected' : '' ?>><?= esc($nmBln) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="rotasiTahunEfektif" class="form-label ecc-filter-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun_efektif" id="rotasiTahunEfektif" class="form-control form-control-sm num-tabular ecc-filter-control" value="<?= $curYearModal ?>" min="2020" max="2099" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="rotasiKeterangan" class="form-label ecc-filter-label">Catatan / Nomor SK (Opsional)</label>
                        <input type="text" name="keterangan" id="rotasiKeterangan" class="form-control form-control-sm ecc-filter-control" maxlength="200" placeholder="Contoh: Serah terima jabatan Katim per SK...">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-3 px-md-4 py-3 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm" id="btnSubmitRotasi">
                        Terapkan Rotasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RIWAYAT PENUGASAN & ATASAN LANGSUNG -->
<div class="modal fade" id="modalRiwayatPenugasan" tabindex="-1" aria-labelledby="modalRiwayatPenugasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden modal-accent-primary">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm modal-icon-box" aria-hidden="true">
                        <i class="bi bi-clock-history fs-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="h5 modal-title fw-bold text-dark mb-0 ecc-text-title" id="modalRiwayatPenugasanLabel">Riwayat Penugasan &amp; Atasan Langsung</h2>
                        <small class="ecc-text-muted-aa ecc-text-xs">
                            <span id="riwayatModalUserName" class="fw-bold text-dark">-</span>
                            (<span id="riwayatModalUserNip" class="font-monospace num-tabular">-</span>)
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1 btn-tactile btn-modal-action" id="btnToggleFormRiwayat">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i> <span id="labelToggleForm">Tambah Periode</span>
                    </button>
                    <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>

            <div class="modal-body p-3 p-md-4">
                <!-- FORM CONTAINER (COLLAPSIBLE) -->
                <div id="containerFormRiwayat" class="d-none mb-4 p-3 rounded-3 bg-light border">
                    <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                        <span class="fw-bold text-dark ecc-text-sm" id="formRiwayatTitle">Form Periode Penugasan</span>
                        <button type="button" class="btn-close" id="btnCloseFormRiwayat" aria-label="Tutup form"></button>
                    </div>

                    <form id="formRiwayatPenugasan" autocomplete="off">
                        <input type="hidden" name="id" id="inputRiwayatId" value="">
                        <input type="hidden" name="user_id" id="inputRiwayatUserId" value="">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="inputRiwayatTahun" class="form-label ecc-filter-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" name="tahun" id="inputRiwayatTahun" class="form-control form-control-sm num-tabular ecc-filter-control" min="2020" max="2099" value="<?= date('Y') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="inputRiwayatBulanMulai" class="form-label ecc-filter-label">Bulan Mulai <span class="text-danger">*</span></label>
                                <select name="bulan_mulai" id="inputRiwayatBulanMulai" class="form-select form-select-sm ecc-filter-control" required>
                                    <?php 
                                    $bulanNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                                    foreach ($bulanNames as $bNum => $bName): ?>
                                        <option value="<?= $bNum ?>"><?= $bName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="inputRiwayatBulanSelesai" class="form-label ecc-filter-label">Bulan Selesai <span class="text-danger">*</span></label>
                                <select name="bulan_selesai" id="inputRiwayatBulanSelesai" class="form-select form-select-sm ecc-filter-control" required>
                                    <?php foreach ($bulanNames as $bNum => $bName): ?>
                                        <option value="<?= $bNum ?>" <?= $bNum === 12 ? 'selected' : '' ?>><?= $bName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="inputRiwayatUnit" class="form-label ecc-filter-label">Unit Kerja <span class="text-danger">*</span></label>
                                <select name="unit" id="inputRiwayatUnit" class="form-select form-select-sm ecc-filter-control" required>
                                    <option value="">Pilih Unit Kerja</option>
                                    <?php if (!empty($unit_kerja_list)): ?>
                                        <?php foreach ($unit_kerja_list as $uk): ?>
                                            <option value="<?= esc($uk['nama_unit']) ?>"><?= esc($uk['nama_unit']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inputRiwayatJabatan" class="form-label ecc-filter-label">Jabatan Dinas <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" id="inputRiwayatJabatan" class="form-control form-control-sm ecc-filter-control" placeholder="Contoh: Kapokja Humas / Dosen" required maxlength="100">
                            </div>

                            <div class="col-md-6">
                                <label for="inputRiwayatAtasanId" class="form-label ecc-filter-label">Atasan Langsung <span class="text-danger">*</span></label>
                                <select name="atasan_id" id="inputRiwayatAtasanId" class="form-select form-select-sm ecc-filter-control" required>
                                    <option value="">Pilih Atasan Langsung</option>
                                    <?php if (!empty($potential_bosses)): ?>
                                        <?php foreach ($potential_bosses as $boss): ?>
                                            <option value="<?= $boss['id'] ?>"><?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Pimpinan') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inputRiwayatRole" class="form-label ecc-filter-label">Peran Akun <span class="ecc-text-muted-aa fw-normal">(opsional)</span></label>
                                <select name="role" id="inputRiwayatRole" class="form-select form-select-sm ecc-filter-control">
                                    <option value="pegawai">Staf</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="katim">Katim</option>
                                    <option value="kapokja">Kapokja</option>
                                    <option value="kanit">Kanit</option>
                                    <option value="kabag">Kabag</option>
                                    <option value="kabag_aak">Kabag AAK</option>
                                    <option value="kabag_kuk">Kabag KUK</option>
                                    <option value="wadir">Wakil Direktur</option>
                                    <option value="manajemen">Manajemen</option>
                                    <option value="tugas_belajar">Tugas Belajar</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="inputRiwayatKeterangan" class="form-label ecc-filter-label">No. SK / Keterangan Penugasan <span class="ecc-text-muted-aa fw-normal">(opsional)</span></label>
                                <input type="text" name="keterangan" id="inputRiwayatKeterangan" class="form-control form-control-sm ecc-filter-control" placeholder="Contoh: SK Direktur No. 04/2025 tentang Rotasi Jabatan" maxlength="255">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill btn-tactile btn-modal-action" id="btnCancelFormRiwayat">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold btn-tactile btn-modal-action d-inline-flex align-items-center gap-1" id="btnSaveRiwayat">
                                <i class="bi bi-check-lg" aria-hidden="true"></i> <span id="labelSaveRiwayat">Simpan Penugasan</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- LOADING SPINNER -->
                <div id="loadingRiwayatList" class="text-center py-4 d-none">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <div class="ecc-text-muted-aa ecc-text-xs">Memuat riwayat penugasan...</div>
                </div>

                <!-- DAFTAR RIWAYAT CONTAINER -->
                <div id="contentRiwayatList">
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th scope="col" style="min-width: 144px;">Periode Dinas</th>
                                    <th scope="col">Jabatan &amp; Unit Kerja</th>
                                    <th scope="col">Atasan Langsung</th>
                                    <th scope="col" class="text-center" style="width: 112px;">Status</th>
                                    <th scope="col" class="text-center" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRiwayatList">
                                <tr>
                                    <td colspan="5" class="text-center py-4 ecc-text-muted-aa">Belum ada riwayat penugasan tercatat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top bg-light px-3 px-md-4 py-3">
                <button type="button" class="btn btn-outline-secondary btn-tactile btn-modal-action rounded-pill fw-semibold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function showAlertMessage(title, text, icon) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon || 'error',
                confirmButtonColor: '#0d6efd',
                customClass: { popup: 'rounded-4' }
            });
        } else {
            alert(text || title);
        }
    }

    // Passive wheel blur guard pada input tahun modal
    ['batchTahunEfektif', 'rotasiTahunEfektif', 'inputRiwayatTahun'].forEach(function(inputId) {
        const el = document.getElementById(inputId);
        if (el) {
            el.addEventListener('wheel', function() {
                this.blur();
            }, { passive: true });
        }
    });

    // Focus restoration saat modal ditutup
    ['importModal', 'batchEditModal', 'modalRotasiAtasan', 'modalRiwayatPenugasan'].forEach(function(modalId) {
        const mEl = document.getElementById(modalId);
        if (!mEl) return;
        let triggerRef = null;
        mEl.addEventListener('show.bs.modal', function(e) {
            triggerRef = e.relatedTarget || mEl._eccTriggerEl || document.activeElement;
        });
        mEl.addEventListener('hidden.bs.modal', function() {
            const target = mEl._eccTriggerEl || triggerRef;
            mEl._eccTriggerEl = null;
            if (target && document.body.contains(target) && typeof target.focus === 'function') {
                target.focus();
            }
        });
    });

    // Double submit handling untuk modal
    $('#formImportUsers').on('submit', function() {
        $('#btnSubmitImport').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengimpor...').prop('disabled', true);
    });
    $('#formBatchUpdate').on('submit', function() {
        $('#btnSubmitBatch').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
    });

    // Handling Konfirmasi Hapus Pengguna (SweetAlert2 & POST Form)
    $(document).on('click', '.btn-delete-user', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const userName = $(this).data('userName') || 'pengguna ini';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Pengguna?',
                html: `Akun <b>${escapeHtml(userName)}</b> beserta data kinerjanya akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ya, Hapus',
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

    function getUniqueSelectedUserIds() {
        const checkedBoxes = document.querySelectorAll('#dataTable .user-checkbox:checked, .mobile-cards-view .user-checkbox:checked');
        return Array.from(new Set(Array.from(checkedBoxes).map(cb => cb.value).filter(Boolean)));
    }

    function updateSelectAllState() {
        if (!selectAllCheckbox) return;
        const desktopBoxes = document.querySelectorAll('#dataTable .user-checkbox');
        if (desktopBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            return;
        }
        const checkedCount = document.querySelectorAll('#dataTable .user-checkbox:checked').length;
        selectAllCheckbox.checked = (checkedCount === desktopBoxes.length);
        selectAllCheckbox.indeterminate = (checkedCount > 0 && checkedCount < desktopBoxes.length);
    }

    // Sinkronisasi checkbox desktop <-> mobile berdasarkan user ID agar tidak pernah duplikat
    $(document).on('change', '.user-checkbox', function() {
        const uid = this.value;
        const isChecked = this.checked;
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            if (cb.value === uid && cb !== this) {
                cb.checked = isChecked;
            }
        });
        updateSelectAllState();
    });

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            selectAllCheckbox.indeterminate = false;
        });
    }

    // Fungsi untuk tombol batch edit
    if (batchEditBtn) {
        batchEditBtn.addEventListener('click', function() {
            const selectedIds = getUniqueSelectedUserIds();

            if (selectedIds.length === 0) {
                showAlertMessage('Peringatan', 'Pilih minimal satu pengguna terlebih dahulu.', 'warning');
                return;
            }

            batchUserIdsInput.value = selectedIds.join(',');
            batchEditCountSpan.textContent = selectedIds.length;
            if (batchEditModalEl) batchEditModalEl._eccTriggerEl = this;
            batchEditModal.show();
        });
    }

    // --- TOGGLE PERIODE EFEKTIF DI MODAL BATCH EDIT & VALIDASI ROTASI ATASAN ---
    $(document).on('change', '.batch-mode-radio', function() {
        const isMutasi = $('input[name="mode_perubahan"]:checked').val() === 'mutasi';
        $('#batchPeriodeWrapper').toggle(isMutasi);
    });

    $('#formRotasiAtasan').on('submit', function(e) {
        const lama = $('#rotasiAtasanLama').val();
        const baru = $('#rotasiAtasanBaru').val();
        if (!lama || !baru || lama === baru) {
            e.preventDefault();
            const msg = (!lama || !baru)
                ? 'Pilih atasan lama dan atasan baru terlebih dahulu.'
                : 'Atasan baru tidak boleh sama dengan atasan lama.';
            showAlertMessage('Periksa Pilihan', msg, 'warning');
            return false;
        }
        $('#btnSubmitRotasi').text('Memproses...').prop('disabled', true);
    });

    // Simpan nilai awal setiap dropdown unit agar dapat dikembalikan jika dibatalkan
    document.querySelectorAll('.unit-kerja-select').forEach(function(sel) {
        sel.dataset.prevUnit = sel.value;
    });

    const bulanIndoNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let suppressUnitSelectEvent = false;

    function revertUnitSelect(selectEl, prevVal) {
        suppressUnitSelectEvent = true;
        selectEl.value = prevVal;
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2 && jQuery(selectEl).hasClass('select2-hidden-accessible')) {
            jQuery(selectEl).trigger('change.select2');
        }
        suppressUnitSelectEvent = false;
    }

    function sendAjaxUnitUpdate(select, userId, newUnit, prevUnit, modePerubahan, bulanEfektif, tahunEfektif) {
        const statusDiv = select.parentElement.querySelector('.update-status');
        if (statusDiv) {
            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat spin-icon" aria-hidden="true"></i> Menyimpan...';
            statusDiv.className = 'update-status ecc-text-xs mt-1 ecc-text-muted-aa';
        }

        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
        const csrfHash = csrfInput ? csrfInput.value : '';

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('unit', newUnit);
        formData.append('mode_perubahan', modePerubahan);
        formData.append('bulan_efektif', String(bulanEfektif));
        formData.append('tahun_efektif', String(tahunEfektif));
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

            if (data.success) {
                if (data.should_sync_profile === false) {
                    revertUnitSelect(select, prevUnit);
                } else {
                    select.dataset.prevUnit = newUnit;
                }
            } else {
                revertUnitSelect(select, prevUnit);
            }

            if (statusDiv) {
                if (data.success) {
                    const labelMsg = data.should_sync_profile === false
                        ? 'Terjadwal (' + escapeHtml(bulanIndoNames[bulanEfektif - 1] || '') + ')'
                        : 'Tersimpan';
                    statusDiv.innerHTML = '<i class="bi bi-check-circle-fill" aria-hidden="true"></i> ' + labelMsg;
                    statusDiv.className = 'update-status ecc-text-xs mt-1 text-success fw-medium';
                } else {
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill" aria-hidden="true"></i> Gagal';
                    statusDiv.className = 'update-status ecc-text-xs mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.replaceChildren(); }, 3500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            revertUnitSelect(select, prevUnit);
            if (statusDiv) {
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill" aria-hidden="true"></i> Gagal';
                statusDiv.className = 'update-status ecc-text-xs mt-1 text-danger';
                setTimeout(() => { statusDiv.replaceChildren(); }, 3000);
            }
        });
    }

    // --- EVENT DELEGATION UNTUK AJAX UNIT KERJA UPDATE (DENGAN PROTEKSI RIWAYAT) ---
    $(document).on('change', '.unit-kerja-select', function() {
        if (suppressUnitSelectEvent) return;
        const select = this;
        const userId = select.dataset.userId;
        const newUnit = select.value;
        const prevUnit = select.dataset.prevUnit ?? '';
        if (newUnit === prevUnit) return;

        const now = new Date();
        const curMonth = now.getMonth() + 1;
        const curYear = now.getFullYear();

        if (typeof Swal !== 'undefined') {
            const monthOptions = bulanIndoNames.map(function(nm, idx) {
                const mVal = idx + 1;
                return '<option value="' + mVal + '"' + (mVal === curMonth ? ' selected' : '') + '>' + nm + '</option>';
            }).join('');

            Swal.fire({
                title: 'Pindahkan Unit Kerja?',
                html: `
                    <p class="small text-muted mb-3">Pilih cara pencatatan agar nilai kinerja di unit lama tetap terjaga.</p>
                    <div class="text-start small">
                        <label class="d-flex align-items-start gap-2 p-2 bg-light border rounded-3 mb-2" style="cursor:pointer;">
                            <input type="radio" name="swalUnitMode" value="mutasi" class="form-check-input mt-1" checked>
                            <span><strong class="d-block text-dark">Mutasi Baru (Simpan Riwayat Unit Lama)</strong><span class="text-muted">Penilaian bulan sebelumnya tetap tinggal di unit lama.</span></span>
                        </label>
                        <div class="row g-2 mb-3" id="swalUnitPeriodeRow">
                            <div class="col-7">
                                <label for="swalUnitBulan" class="form-label fw-bold text-secondary mb-1 ecc-text-xs">Mulai Efektif Bulan</label>
                                <select id="swalUnitBulan" class="form-select form-select-sm">${monthOptions}</select>
                            </div>
                            <div class="col-5">
                                <label for="swalUnitTahun" class="form-label fw-bold text-secondary mb-1 ecc-text-xs">Tahun</label>
                                <input type="number" id="swalUnitTahun" class="form-control form-control-sm num-tabular" value="${curYear}" min="2020" max="2099">
                            </div>
                        </div>
                        <label class="d-flex align-items-start gap-2 p-2 bg-white border rounded-3 mb-0" style="cursor:pointer;">
                            <input type="radio" name="swalUnitMode" value="koreksi" class="form-check-input mt-1">
                            <span><strong class="d-block text-dark">Koreksi Salah Input</strong><span class="text-muted">Timpa unit profil tanpa membuat riwayat mutasi.</span></span>
                        </label>
                    </div>
                `,
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                didOpen: function() {
                    const radios = document.querySelectorAll('input[name="swalUnitMode"]');
                    const row = document.getElementById('swalUnitPeriodeRow');
                    radios.forEach(function(r) {
                        r.addEventListener('change', function() {
                            const checked = document.querySelector('input[name="swalUnitMode"]:checked');
                            if (row) row.style.display = (checked && checked.value === 'mutasi') ? '' : 'none';
                        });
                    });
                },
                preConfirm: function() {
                    const checked = document.querySelector('input[name="swalUnitMode"]:checked');
                    const mode = checked ? checked.value : 'mutasi';
                    const bln = parseInt((document.getElementById('swalUnitBulan') || {}).value, 10) || curMonth;
                    const thn = parseInt((document.getElementById('swalUnitTahun') || {}).value, 10) || curYear;
                    return { mode, bln, thn };
                }
            }).then(function(result) {
                if (result.isConfirmed && result.value) {
                    sendAjaxUnitUpdate(select, userId, newUnit, prevUnit, result.value.mode, result.value.bln, result.value.thn);
                } else {
                    revertUnitSelect(select, prevUnit);
                }
            });
        } else if (confirm('Pindahkan unit kerja pegawai ini mulai bulan ' + bulanIndoNames[curMonth - 1] + ' ' + curYear + '?')) {
            sendAjaxUnitUpdate(select, userId, newUnit, prevUnit, 'mutasi', curMonth, curYear);
        } else {
            revertUnitSelect(select, prevUnit);
        }
    });

    // Inisialisasi Select2 bertahap (hanya baris terlihat) untuk mempercepat muat awal
    function initUnitSelect2($selects) {
        if (typeof jQuery === 'undefined' || !$.fn.select2 || !$selects || !$selects.length) return;
        $selects.each(function() {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) return;
            $el.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Unit Kerja'
            });
        });
    }

    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        initUnitSelect2($('tr.user-row-visible .unit-kerja-select'));

        $(document).on('select2:open', () => {
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if (searchField) searchField.focus();
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
            const revealedRows = [];
            for (let i = 0; i < limit; i++) {
                if (hiddenRows[i]) {
                    hiddenRows[i].classList.remove('d-none', 'user-row-hidden');
                    hiddenRows[i].classList.add('user-row-visible');
                    revealedRows.push(hiddenRows[i]);
                }
                if (hiddenCards[i]) {
                    hiddenCards[i].classList.remove('d-none', 'user-card-hidden');
                    hiddenCards[i].classList.add('user-card-visible');
                }
            }
            if (revealedRows.length > 0 && typeof jQuery !== 'undefined') {
                initUnitSelect2($(revealedRows).find('.unit-kerja-select'));
            }
            isLoading = false;
            checkRemainingRows();
        }, 250);
    }

    // =========================================================================
    // MODAL RIWAYAT PENUGASAN & ATASAN LANGSUNG (AJAX LOGIC)
    // =========================================================================
    const modalRiwayatEl = document.getElementById('modalRiwayatPenugasan');
    const modalRiwayat = modalRiwayatEl ? new bootstrap.Modal(modalRiwayatEl) : null;
    const containerFormRiwayat = document.getElementById('containerFormRiwayat');
    const formRiwayat = document.getElementById('formRiwayatPenugasan');
    const tbodyRiwayat = document.getElementById('tbodyRiwayatList');
    const loadingRiwayat = document.getElementById('loadingRiwayatList');
    const contentRiwayat = document.getElementById('contentRiwayatList');
    const btnToggleFormRiwayat = document.getElementById('btnToggleFormRiwayat');
    const labelToggleForm = document.getElementById('labelToggleForm');

    let currentRiwayatUserId = null;

    // Buka Modal Riwayat Penugasan
    $(document).on('click', '.btn-riwayat-penugasan', function(e) {
        e.preventDefault();
        const userId = $(this).data('userId');
        const userName = $(this).data('userName') || '-';
        const userNip = $(this).data('userNip') || '-';
        const userUnit = $(this).data('userUnit') || '';
        const userJabatan = $(this).data('userJabatan') || '';
        const userRole = $(this).data('userRole') || 'pegawai';
        const userAtasan = $(this).data('userAtasan') || '';

        currentRiwayatUserId = userId;
        $('#riwayatModalUserName').text(userName);
        $('#riwayatModalUserNip').text(userNip);

        // Reset Form
        resetFormRiwayat();
        $('#inputRiwayatUserId').val(userId);
        $('#inputRiwayatUnit').val(userUnit);
        $('#inputRiwayatJabatan').val(userJabatan);
        $('#inputRiwayatRole').val(userRole);
        $('#inputRiwayatAtasanId').val(userAtasan);

        // Sembunyikan form dulu saat modal pertama dibuka
        hideFormRiwayat();

        if (modalRiwayatEl) modalRiwayatEl._eccTriggerEl = this;
        if (modalRiwayat) {
            modalRiwayat.show();
        }

        loadRiwayatData(userId);
    });

    $(document).on('click', '.btn-retry-riwayat', function() {
        if (currentRiwayatUserId) {
            loadRiwayatData(currentRiwayatUserId);
        }
    });

    // Toggle Tampilkan / Sembunyikan Form
    if (btnToggleFormRiwayat) {
        btnToggleFormRiwayat.addEventListener('click', function() {
            if (containerFormRiwayat.classList.contains('d-none')) {
                showFormRiwayat();
            } else {
                hideFormRiwayat();
            }
        });
    }

    $('#btnCloseFormRiwayat, #btnCancelFormRiwayat').on('click', function() {
        hideFormRiwayat();
    });

    function showFormRiwayat(isEdit = false) {
        containerFormRiwayat.classList.remove('d-none');
        if (isEdit) {
            $('#formRiwayatTitle').text('Edit Periode Penugasan');
            $('#labelSaveRiwayat').text('Perbarui Penugasan');
        } else {
            $('#formRiwayatTitle').text('Form Periode Penugasan');
            $('#labelSaveRiwayat').text('Simpan Penugasan');
        }
        if (labelToggleForm) labelToggleForm.textContent = 'Tutup Form';
    }

    function hideFormRiwayat() {
        containerFormRiwayat.classList.add('d-none');
        if (labelToggleForm) labelToggleForm.textContent = 'Tambah Periode';
        resetFormRiwayat();
    }

    function resetFormRiwayat() {
        $('#inputRiwayatId').val('');
        $('#inputRiwayatTahun').val(new Date().getFullYear());
        $('#inputRiwayatBulanMulai').val('1');
        $('#inputRiwayatBulanSelesai').val('12');
        $('#inputRiwayatKeterangan').val('');
    }

    // Ambil data riwayat via AJAX
    function loadRiwayatData(userId) {
        if (!userId) return;
        loadingRiwayat.classList.remove('d-none');
        contentRiwayat.classList.add('d-none');

        fetch('<?= site_url('users/riwayat-penugasan') ?>/' + userId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            loadingRiwayat.classList.add('d-none');
            contentRiwayat.classList.remove('d-none');

            if (res.csrf_hash) {
                updateCsrfTokens(res.csrf_hash);
            }

            renderRiwayatTable(res.data || []);
        })
        .catch(err => {
            console.error('Error load riwayat:', err);
            loadingRiwayat.classList.add('d-none');
            contentRiwayat.classList.remove('d-none');
            tbodyRiwayat.innerHTML = '<tr><td colspan="5" class="text-center py-4"><div class="text-danger mb-2 ecc-text-sm">Gagal memuat riwayat penugasan.</div><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-tactile btn-modal-action btn-retry-riwayat">Coba Lagi</button></td></tr>';
        });
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    function renderRiwayatTable(list) {
        if (!list || list.length === 0) {
            tbodyRiwayat.innerHTML = '<tr><td colspan="5" class="text-center py-4 ecc-text-muted-aa">Belum ada riwayat penugasan tercatat. Pilih "Tambah Periode" untuk menambahkan.</td></tr>';
            return;
        }

        const namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const curDate = new Date();
        const curYear = curDate.getFullYear();
        const curMonth = curDate.getMonth() + 1;
        let html = '';

        list.forEach(item => {
            const tahun = parseInt(item.tahun) || curYear;
            const blnMulai = parseInt(item.bulan_mulai) || 1;
            const blnSelesai = parseInt(item.bulan_selesai) || 12;

            let statusBadge = '';
            if (tahun === curYear && blnMulai <= curMonth && blnSelesai >= curMonth) {
                statusBadge = '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 fw-semibold ecc-text-xs">Aktif</span>';
            } else if (tahun < curYear || (tahun === curYear && blnSelesai < curMonth)) {
                statusBadge = '<span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1 ecc-text-xs">Lampau</span>';
            } else {
                statusBadge = '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1 fw-semibold ecc-text-xs">Mendatang</span>';
            }

            const periodeStr = (blnMulai === blnSelesai)
                ? (namaBulan[blnMulai] || blnMulai) + ' ' + tahun
                : (namaBulan[blnMulai] || blnMulai) + ' &mdash; ' + (namaBulan[blnSelesai] || blnSelesai) + ' ' + tahun;

            const namaAtasan = item.nama_atasan ? escapeHtml(item.nama_atasan) : '-';
            const jabatanAtasan = item.jabatan_atasan ? '<small class="d-block ecc-text-muted-aa ecc-text-xs">' + escapeHtml(item.jabatan_atasan) + '</small>' : '';
            const ketSk = item.keterangan ? '<div class="ecc-text-muted-aa ecc-text-xs mt-1"><i class="bi bi-file-text me-1" aria-hidden="true"></i>' + escapeHtml(item.keterangan) + '</div>' : '';

            const itemAttr = escapeHtml(JSON.stringify(item));

            html += `<tr>
                <td class="num-tabular font-monospace ecc-text-xs">
                    <span class="fw-semibold text-dark">${periodeStr}</span>
                    ${ketSk}
                </td>
                <td>
                    <strong class="text-dark d-block lh-sm">${escapeHtml(item.jabatan) || '-'}</strong>
                    <span class="ecc-text-muted-aa ecc-text-xs">${escapeHtml(item.unit) || '-'}</span>
                </td>
                <td>
                    <span class="fw-semibold text-dark d-block lh-sm">${namaAtasan}</span>
                    ${jabatanAtasan}
                </td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-outline-warning btn-tactile btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center btn-edit-riwayat" data-item='${itemAttr}' title="Edit Penugasan" aria-label="Edit riwayat ${escapeHtml(periodeStr.replace('&mdash;', '-'))}">
                            <i class="bi bi-pencil" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-tactile btn-icon-action rounded-circle d-inline-flex align-items-center justify-content-center btn-delete-riwayat" data-id="${escapeHtml(item.id)}" title="Hapus Riwayat" aria-label="Hapus riwayat ${escapeHtml(periodeStr.replace('&mdash;', '-'))}">
                            <i class="bi bi-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });

        tbodyRiwayat.innerHTML = html;
    }

    // Handler tombol Edit Penugasan
    $(document).on('click', '.btn-edit-riwayat', function(e) {
        e.preventDefault();
        const raw = $(this).attr('data-item');
        if (!raw) return;
        try {
            const item = JSON.parse(raw);
            $('#inputRiwayatId').val(item.id);
            $('#inputRiwayatTahun').val(item.tahun);
            $('#inputRiwayatBulanMulai').val(item.bulan_mulai);
            $('#inputRiwayatBulanSelesai').val(item.bulan_selesai);
            $('#inputRiwayatUnit').val(item.unit);
            $('#inputRiwayatJabatan').val(item.jabatan);
            $('#inputRiwayatRole').val(item.role || 'pegawai');
            $('#inputRiwayatAtasanId').val(item.atasan_id);
            $('#inputRiwayatKeterangan').val(item.keterangan || '');
            showFormRiwayat(true);
        } catch (err) {
            console.error('Error parse data-item:', err);
        }
    });

    // Submit Form Simpan Riwayat Penugasan
    if (formRiwayat) {
        formRiwayat.addEventListener('submit', function(e) {
            e.preventDefault();
            const btnSave = document.getElementById('btnSaveRiwayat');
            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData(formRiwayat);
            formData.set('user_id', currentRiwayatUserId);
            formData.set(csrfTokenName, csrfHash);

            fetch('<?= site_url('users/riwayat-penugasan/store') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i> <span id="labelSaveRiwayat">Simpan Penugasan</span>';

                if (res.csrf_hash) {
                    updateCsrfTokens(res.csrf_hash);
                }

                if (res.success) {
                    hideFormRiwayat();
                    loadRiwayatData(currentRiwayatUserId);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Tersimpan',
                            text: res.message || 'Riwayat penugasan berhasil disimpan.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        });
                    }
                } else {
                    showAlertMessage('Gagal Menyimpan', res.message || 'Periksa kembali data penugasan.', 'error');
                }
            })
            .catch(err => {
                console.error('Error simpan riwayat:', err);
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i> <span id="labelSaveRiwayat">Simpan Penugasan</span>';
                showAlertMessage('Gagal Menyimpan', 'Gagal menyimpan riwayat penugasan. Silakan coba lagi.', 'error');
            });
        });
    }

    // Hapus baris riwayat
    $(document).on('click', '.btn-delete-riwayat', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (!id) return;

        const executeDelete = () => {
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData();
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('users/riwayat-penugasan/delete') ?>/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.csrf_hash) {
                    updateCsrfTokens(res.csrf_hash);
                }

                if (res.success) {
                    loadRiwayatData(currentRiwayatUserId);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Terhapus',
                            text: res.message || 'Riwayat penugasan berhasil dihapus.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-4' }
                        });
                    }
                } else {
                    showAlertMessage('Gagal Menghapus', res.message || 'Gagal menghapus riwayat penugasan.', 'error');
                }
            })
            .catch(err => {
                console.error('Error delete riwayat:', err);
                showAlertMessage('Gagal Menghapus', 'Gagal menghapus riwayat penugasan. Silakan coba lagi.', 'error');
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: 'Riwayat penugasan ini akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-4' }
            }).then(result => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm('Hapus riwayat penugasan ini?')) {
                executeDelete();
            }
        }
    });

    function updateCsrfTokens(newHash) {
        const csrfTokenName = '<?= csrf_token() ?>';
        document.querySelectorAll('input[name="' + csrfTokenName + '"]').forEach(input => {
            input.value = newHash;
        });
    }
});
</script>
<?= $this->endSection() ?>