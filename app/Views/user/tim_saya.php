<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Tim Saya') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
/* ==========================================================================
   ECC DESIGN SYSTEM — 8-POINT GRID SCALE TOKENS & RESPONSIVE LAYOUT
   ========================================================================== */
:root {
    --ecc-space-0-5: 4px;   /* 0.5x Micro */
    --ecc-space-1: 8px;     /* 1.0x Base */
    --ecc-space-1-5: 12px;  /* 1.5x */
    --ecc-space-2: 16px;    /* 2.0x */
    --ecc-space-3: 24px;    /* 3.0x */
    --ecc-space-4: 32px;    /* 4.0x */
    --ecc-space-5: 40px;    /* 5.0x */
    --ecc-space-6: 48px;    /* 6.0x */
    --ecc-space-8: 64px;    /* 8.0x */
}

/* iOS Mobile Zoom Fix (16px minimum on touch devices) */
@media (max-width: 767.98px) {
    .form-control, .form-select {
        font-size: 16px !important;
    }
}

/* Staggered Entrance Animations */
@keyframes bentoEntrance {
    0% {
        opacity: 0;
        transform: translateY(12px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
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

/* KPI Bento Summary Boxes (8-Point Grid) */
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
    font-size: 0.8125rem;
    line-height: 1.4;
    transition: background-color 0.15s ease;
}
.table-bento tbody tr:hover td {
    background-color: #f8fafc;
}

/* Search Box Toolbar (8-Point Scale) */
.search-staf-container {
    position: relative;
    max-width: 360px;
    width: 100%;
}
.search-staf-input {
    height: 38px;
    padding: 6px 14px 6px 36px;
    font-size: 0.8125rem;
    border-radius: 50rem;
    border: 1px solid #cbd5e1;
    background-color: #ffffff;
    transition: all 0.15s ease;
}
.search-staf-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}
.search-staf-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    font-size: 0.875rem;
}

/* Unit Dropdown inside Table (36px 8-Point Scale) */
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

/* Dual-View Mobile vs Desktop Toggle */
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
.mobile-staf-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.mobile-staf-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

/* Select2 8-Point Grid Alignment */
.select2-container--bootstrap-5 .select2-selection {
    border-radius: 8px !important;
    padding: 6px 12px !important;
    min-height: 40px !important;
    border-color: #cbd5e1 !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--bootstrap-5.select2-container--focus .select2-selection,
.select2-container--bootstrap-5.select2-container--open .select2-selection {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
}
.select2-container--bootstrap-5 .select2-dropdown {
    border-radius: 12px !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    overflow: hidden !important;
}
.select2-container--bootstrap-5 .select2-search--dropdown {
    padding: 8px 12px !important;
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.select2-container--bootstrap-5 .select2-search__field {
    border-radius: 6px !important;
    padding: 6px 12px !important;
    border-color: #cbd5e1 !important;
    font-size: 0.8125rem !important;
}
.select2-pegawai-card {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}

/* Accessibility: Prefers Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .bento-stagger,
    .btn-tactile,
    .kpi-bento-card,
    .mobile-staf-card,
    #addStafModal .modal-content {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    
    <!-- PAGE HEADER (8-Point Grid) -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Tim Saya</h1>
            <p class="text-muted small mb-0">Manajemen struktur anggota tim, penugasan staf, dan sinkronisasi unit kerja.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addStafModal" style="height: 36px; font-size: 0.8125rem;">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Anggota Tim</span>
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
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

    <!-- KPI SUMMARY STRIP (8-Point Grid System) -->
    <div class="row g-3 mb-4 bento-stagger bento-stagger-2">
        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Total Anggota Tim</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1 num-tabular" id="kpiTotalStaf"><?= count($staf) ?></h3>
                    <small class="text-secondary d-block mt-0.5" style="font-size: 0.72rem;">Staf aktif di bawah supervisi</small>
                </div>
                <div class="bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Unit Kerja</span>
                    <div class="fw-bold text-dark mt-1 lh-sm" style="font-size: 0.9375rem; word-break: break-word;" title="<?= esc($my_unit ?? 'Belum Ditentukan') ?>">
                        <?= !empty($my_unit) ? esc($my_unit) : '<span class="text-muted fst-italic">Tanpa Unit</span>' ?>
                    </div>
                    <small class="text-secondary d-block mt-1" style="font-size: 0.72rem;">Unit kerja tim Anda</small>
                </div>
                <div class="bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-building-check fs-5"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-bento-card d-flex align-items-center justify-content-between h-100 gap-3">
                <div style="min-width: 0;" class="flex-grow-1">
                    <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Otoritas Tim Leader</span>
                    <div class="fw-bold text-dark mt-1 lh-sm" style="font-size: 0.9375rem; word-break: break-word;" title="<?= esc($me['jabatan'] ?? 'Ketua Tim') ?>">
                        <?= !empty(trim((string)($me['jabatan'] ?? ''))) ? esc($me['jabatan']) : 'Ketua Tim / Manajer' ?>
                    </div>
                    <small class="text-secondary d-block mt-1" style="font-size: 0.72rem;">Supervisi & Penilaian Kinerja</small>
                </div>
                <div class="bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; border-radius: 12px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN BENTO CONTAINER -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
        
        <!-- CARD TOOLBAR: SEARCH & STAT -->
        <div class="card-header bg-white border-bottom px-3 px-md-4 py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2.5">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark" style="font-size: 0.9375rem;">Daftar Anggota Tim</span>
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small num-tabular">
                        <span id="stafCountText"><?= count($staf) ?></span> Staf
                    </span>
                </div>

                <!-- Instant Live Search Box -->
                <div class="search-staf-container ms-auto">
                    <i class="bi bi-search search-staf-icon"></i>
                    <input type="text" id="searchStafInput" class="form-control search-staf-input" placeholder="Cari nama staf, NIP, atau jabatan..." aria-label="Cari staf di tim">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            
            <!-- DESKTOP TABLE VIEW (>=768px) -->
            <div class="table-responsive desktop-table-view">
                <table class="table table-hover align-middle table-bento" id="tableTimDesktop" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th> 
                            <th style="min-width: 260px;" class="text-start">Pegawai / Staf</th>
                            <th style="min-width: 180px;" class="text-start">Jabatan</th>
                            <th style="min-width: 240px; max-width: 300px;" class="text-start">Unit Kerja</th>
                            <th style="width: 140px; min-width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="stafTableBody">
                        <?php if (empty($staf)): ?>
                        <tr id="rowEmptyState">
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-xs" style="width: 64px; height: 64px;">
                                        <i class="bi bi-people text-muted fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Anggota Tim</h6>
                                    <p class="text-muted small mb-3" style="max-width: 380px;">
                                        Anda belum memiliki staf yang terdaftar di tim ini. Tambahkan anggota tim untuk mulai memantau dan mengevaluasi kinerja staf.
                                    </p>
                                    <button type="button" class="btn btn-primary btn-sm btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#addStafModal">
                                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($staf as $b): ?>
                            <tr class="staf-item-row"
                                data-nama="<?= esc(strtolower($b['nama_lengkap'])) ?>"
                                data-nip="<?= esc(strtolower($b['nip'] ?? '')) ?>"
                                data-jabatan="<?= esc(strtolower($b['jabatan'] ?? '')) ?>"
                                data-unit="<?= esc(strtolower($b['unit'] ?? '')) ?>">
                                <td class="text-center fw-bold text-muted num-tabular" style="font-size: 0.8rem;"><?= $i++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <?= render_user_avatar($b, $b['nama_lengkap'], 40) ?>
                                        <div class="d-flex flex-column" style="min-width: 0;">
                                            <span class="fw-bold text-dark text-truncate lh-sm mb-0.5 staf-nama-col" style="font-size: 0.875rem;" title="<?= esc($b['nama_lengkap']) ?>"><?= esc($b['nama_lengkap']) ?></span>
                                            <span class="text-muted small num-tabular d-inline-flex align-items-center font-monospace" style="font-size: 0.72rem;">
                                                <i class="bi bi-person-badge me-1 opacity-75"></i><?= esc($b['nip'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 text-wrap text-start lh-sm" style="font-size: 0.75rem;">
                                        <?= !empty(trim((string)($b['jabatan'] ?? ''))) ? esc($b['jabatan']) : 'Staf' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="max-width: 280px;">
                                        <select class="form-select form-select-sm unit-kerja-select-custom unit-kerja-select" data-user-id="<?= $b['id'] ?>" aria-label="Ubah unit kerja <?= esc($b['nama_lengkap']) ?>">
                                            <option value="">-- Pilih Unit Kerja --</option>
                                            <?php foreach ($unit_kerja_list as $unit): ?>
                                                <option value="<?= esc($unit['nama_unit']) ?>" <?= ($b['unit'] == $unit['nama_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($unit['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="update-status small mt-1" style="min-height: 18px; font-size: 0.72rem;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="<?= site_url('tim/remove') ?>" method="POST" class="d-inline form-remove-staf">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="staf_id" value="<?= $b['id'] ?>">
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-tactile rounded-pill px-3 py-1 text-nowrap d-inline-flex align-items-center justify-content-center gap-1 btn-remove-staf shadow-xs" data-nama="<?= esc($b['nama_lengkap']) ?>" title="Keluarkan dari Tim" style="height: 32px; font-size: 0.75rem;">
                                            <i class="bi bi-person-x-fill"></i>
                                            <span>Keluarkan</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Empty Search Result Template -->
                        <tr id="rowSearchEmpty" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-search fs-3 d-block mb-2 opacity-50"></i>
                                <span class="small fw-semibold">Tidak ditemukan staf yang cocok dengan kata kunci pencarian.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- MOBILE CARDS VIEW (<768px) -->
            <div class="mobile-cards-view p-3" id="mobileCardsContainer">
                <?php if (empty($staf)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <span class="fw-semibold">Belum ada anggota tim terdaftar.</span>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2" id="mobileCardsList">
                        <?php foreach ($staf as $b): ?>
                        <div class="mobile-staf-card staf-item-card"
                             data-nama="<?= esc(strtolower($b['nama_lengkap'])) ?>"
                             data-nip="<?= esc(strtolower($b['nip'] ?? '')) ?>"
                             data-jabatan="<?= esc(strtolower($b['jabatan'] ?? '')) ?>"
                             data-unit="<?= esc(strtolower($b['unit'] ?? '')) ?>">
                            
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small text-truncate" style="max-width: 200px;">
                                    <?= !empty($b['unit']) ? esc($b['unit']) : 'Tanpa Unit' ?>
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5 small">
                                    <?= !empty(trim((string)($b['jabatan'] ?? ''))) ? esc($b['jabatan']) : 'Staf' ?>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2.5 mb-3">
                                <?= render_user_avatar($b, $b['nama_lengkap'], 42) ?>
                                <div class="lh-sm flex-grow-1" style="min-width: 0;">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.9375rem;"><?= esc($b['nama_lengkap']) ?></div>
                                    <div class="text-muted small num-tabular font-monospace mt-0.5" style="font-size: 0.75rem;">
                                        NIP: <?= esc($b['nip'] ?? '-') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-light p-2.5 rounded-3 mb-3 border border-light-subtle">
                                <label class="form-label small fw-bold text-secondary mb-1" style="font-size: 0.72rem;">UBAH UNIT KERJA STAF:</label>
                                <select class="form-select form-select-sm unit-kerja-select-custom unit-kerja-select w-100" data-user-id="<?= $b['id'] ?>" aria-label="Ubah unit kerja mobile <?= esc($b['nama_lengkap']) ?>">
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <?php foreach ($unit_kerja_list as $unit): ?>
                                        <option value="<?= esc($unit['nama_unit']) ?>" <?= ($b['unit'] == $unit['nama_unit']) ? 'selected' : '' ?>>
                                            <?= esc($unit['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="update-status small mt-1" style="min-height: 18px; font-size: 0.72rem;"></div>
                            </div>

                            <div class="d-flex justify-content-end pt-2 border-top">
                                <form action="<?= site_url('tim/remove') ?>" method="POST" class="w-100 form-remove-staf">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="staf_id" value="<?= $b['id'] ?>">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-tactile rounded-pill w-100 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 btn-remove-staf fw-semibold shadow-xs" data-nama="<?= esc($b['nama_lengkap']) ?>" style="min-height: 40px; font-size: 0.8125rem;">
                                        <i class="bi bi-person-x-fill"></i>
                                        <span>Keluarkan dari Tim</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Empty Mobile Search Result -->
                        <div id="mobileSearchEmpty" class="text-center py-4 text-muted" style="display: none;">
                            <i class="bi bi-search fs-3 d-block mb-2 opacity-50"></i>
                            <span class="small fw-semibold">Tidak ditemukan staf yang cocok.</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<!-- MODAL TAMBAH STAF (STANDAR BENTO 8-POINT GRID ECC) -->
<div class="modal fade" id="addStafModal" tabindex="-1" aria-labelledby="addStafModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="addStafModalLabel" style="font-size: 1rem;">Tambah Anggota Tim</h6>
                        <small class="text-secondary" style="font-size: 0.75rem;">Pilih pegawai untuk dimasukkan ke dalam unit tim kerja Anda</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-tactile" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= site_url('tim/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-3 p-md-4">
                    
                    <div class="mb-3">
                        <label for="staf_id" class="form-label fw-bold small text-uppercase text-secondary mb-1.5" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                            Pilih Pegawai <span class="text-danger">*</span>
                        </label>
                        <select name="staf_id" id="staf_id" class="form-select select2-pegawai" required style="width: 100%;">
                            <option value="">-- Ketik Nama, NIP, Jabatan, atau Unit --</option>
                            <?php foreach ($semua_pegawai as $p): ?>
                                <?php if($p['atasan_id'] == session()->get('id')) continue; ?>
                                <option value="<?= $p['id'] ?>"
                                    data-nama="<?= esc($p['nama_lengkap']) ?>"
                                    data-nip="<?= esc($p['nip'] ?? '-') ?>"
                                    data-jabatan="<?= esc($p['jabatan'] ?? 'Staf') ?>"
                                    data-unit="<?= esc($p['unit'] ?? 'Tanpa Unit') ?>"
                                    data-has-atasan="<?= !empty($p['atasan_id']) ? '1' : '0' ?>">
                                    <?= esc($p['nama_lengkap']) ?> - <?= esc($p['jabatan'] ?? 'Staf') ?> [NIP: <?= esc($p['nip'] ?? '-') ?>] (<?= esc($p['unit'] ?? 'Tanpa Unit') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small mt-2 text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle-fill me-1 text-primary"></i> Anda dapat mencari pegawai berdasarkan <strong>Nama</strong>, <strong>Nomor NIP</strong>, <strong>Jabatan</strong>, atau <strong>Unit Kerja</strong>.
                        </div>
                    </div>

                    <div class="alert alert-light border border-info-subtle rounded-3 p-2.5 mb-0" style="font-size: 0.75rem;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-primary fs-6 mt-0.5"></i>
                            <div class="text-secondary">
                                Pegawai yang ditambahkan akan otomatis dihubungkan dengan <strong><?= esc($my_unit ?? 'Unit Kerja Tim Anda') ?></strong> dan Anda akan ditetapkan sebagai Atasan Langsung.
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top bg-light px-3 px-md-4 py-2.5 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-3 py-1.5 fw-semibold shadow-xs" data-bs-dismiss="modal" style="height: 36px; font-size: 0.8125rem;">Batal</button>
                    <button type="submit" class="btn btn-primary btn-tactile rounded-pill px-3.5 py-1.5 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" style="height: 36px; font-size: 0.8125rem;">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Tambahkan ke Tim</span>
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
<script>
$(document).ready(function() {
    
    // 1. Template Hasil Pencarian Pegawai di Select2 (Bento Card Style)
    function formatPegawaiOption(state) {
        if (!state.id || !state.element) {
            return state.text;
        }
        const nama = state.element.dataset.nama || state.text;
        const nip = state.element.dataset.nip || '-';
        const jabatan = state.element.dataset.jabatan || 'Staf';
        const unit = state.element.dataset.unit || 'Tanpa Unit';
        const hasAtasan = state.element.dataset.hasAtasan === '1';

        // Inisial avatar
        const parts = nama.trim().split(' ').filter(Boolean);
        const initials = (parts.length >= 2 ? (parts[0][0] + parts[1][0]) : (parts[0] ? parts[0].substring(0, 2) : 'PG')).toUpperCase();

        return $(`
            <div class="select2-pegawai-card py-1">
                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.8rem;">
                    ${initials}
                </div>
                <div class="d-flex flex-column flex-grow-1" style="min-width: 0;">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="fw-bold nama-pegawai text-truncate text-dark" style="font-size: 0.88rem;">${nama}</span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5 flex-shrink-0" style="font-size: 0.68rem;">${jabatan}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-0.5" style="font-size: 0.75rem; color: #64748b;">
                        <span class="text-nowrap flex-shrink-0 font-monospace"><i class="bi bi-person-badge me-1 opacity-75"></i>${nip}</span>
                        <span class="opacity-50 flex-shrink-0">•</span>
                        <span class="text-truncate flex-grow-1"><i class="bi bi-building me-1 opacity-75"></i>${unit}</span>
                        ${hasAtasan ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 flex-shrink-0 ms-auto" style="font-size: 0.65rem;">Sudah punya atasan</span>' : ''}
                    </div>
                </div>
            </div>
        `);
    }

    // 2. Matcher Cerdas Multi-Field (Nama, NIP, Jabatan, Unit)
    function matchPegawai(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (typeof data.text === 'undefined') {
            return null;
        }
        const term = params.term.toLowerCase();
        const nama = (data.element?.dataset.nama || '').toLowerCase();
        const nip = (data.element?.dataset.nip || '').toLowerCase();
        const jabatan = (data.element?.dataset.jabatan || '').toLowerCase();
        const unit = (data.element?.dataset.unit || '').toLowerCase();
        const rawText = data.text.toLowerCase();

        if (nama.includes(term) || nip.includes(term) || jabatan.includes(term) || unit.includes(term) || rawText.includes(term)) {
            return data;
        }
        return null;
    }

    // Inisialisasi Select2
    $('.select2-pegawai').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addStafModal'),
        placeholder: '-- Ketik Nama, NIP, Jabatan, atau Unit --',
        allowClear: true,
        templateResult: formatPegawaiOption,
        templateSelection: function(state) {
            if (!state.id || !state.element) return state.text;
            const nama = state.element.dataset.nama || state.text;
            const jabatan = state.element.dataset.jabatan || '';
            return `${nama} (${jabatan})`;
        },
        matcher: matchPegawai
    });

    // Auto-focus field pencarian saat modal dibuka
    $('#addStafModal').on('shown.bs.modal', function () {
        $('.select2-pegawai').select2('open');
    });

    // 3. Instant Live Client-Side Search Box
    const searchInput = document.getElementById('searchStafInput');
    const tableRows = document.querySelectorAll('.staf-item-row');
    const mobileCards = document.querySelectorAll('.staf-item-card');
    const rowSearchEmpty = document.getElementById('rowSearchEmpty');
    const mobileSearchEmpty = document.getElementById('mobileSearchEmpty');
    const stafCountText = document.getElementById('stafCountText');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            let visibleDesktop = 0;
            let visibleMobile = 0;

            tableRows.forEach(row => {
                const nama = row.dataset.nama || '';
                const nip = row.dataset.nip || '';
                const jabatan = row.dataset.jabatan || '';
                const unit = row.dataset.unit || '';

                if (nama.includes(query) || nip.includes(query) || jabatan.includes(query) || unit.includes(query)) {
                    row.style.display = '';
                    visibleDesktop++;
                } else {
                    row.style.display = 'none';
                }
            });

            mobileCards.forEach(card => {
                const nama = card.dataset.nama || '';
                const nip = card.dataset.nip || '';
                const jabatan = card.dataset.jabatan || '';
                const unit = card.dataset.unit || '';

                if (nama.includes(query) || nip.includes(query) || jabatan.includes(query) || unit.includes(query)) {
                    card.style.display = '';
                    visibleMobile++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (rowSearchEmpty) {
                rowSearchEmpty.style.display = (visibleDesktop === 0 && tableRows.length > 0) ? '' : 'none';
            }
            if (mobileSearchEmpty) {
                mobileSearchEmpty.style.display = (visibleMobile === 0 && mobileCards.length > 0) ? '' : 'none';
            }
            if (stafCountText) {
                stafCountText.textContent = query !== '' ? visibleDesktop : tableRows.length;
            }
        });
    }

    // 4. AJAX untuk Ubah Unit Kerja Real-Time
    const unitKerjaSelects = document.querySelectorAll('.unit-kerja-select');
    unitKerjaSelects.forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newUnit = this.value;
            const statusDiv = this.nextElementSibling;

            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat spin-icon"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';

            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('unit', newUnit);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('tim/update_unit') ?>', {
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
                    statusDiv.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Tersimpan';
                    statusDiv.className = 'update-status small mt-1 text-success fw-medium';
                } else {
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> ' + (data.message || 'Gagal');
                    statusDiv.className = 'update-status small mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Error jaringan!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            });
        });
    });

    // 5. Konfirmasi Keluarkan Anggota Tim (SweetAlert2 & Native Fallback)
    const removeButtons = document.querySelectorAll('.btn-remove-staf');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const namaPegawai = this.dataset.nama || 'pegawai ini';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Keluarkan dari Tim?',
                    text: `Apakah Anda yakin ingin mengeluarkan ${namaPegawai} dari tim Anda?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-person-x-fill me-1"></i> Ya, Keluarkan',
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
            } else if (confirm(`Keluarkan ${namaPegawai} dari tim Anda?`)) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
