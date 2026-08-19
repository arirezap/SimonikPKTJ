<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Daftar Pegawai') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.compact-table td, .compact-table th {
    padding: 0.5rem 0.65rem !important;
    vertical-align: middle;
}
.compact-table {
    font-size: 0.8125rem;
}
.avatar-circle-sm {
    width: 30px;
    height: 30px;
    font-size: 0.75rem;
    flex-shrink: 0;
}
.sort-link {
    color: #495057;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: color 0.2s ease;
    white-space: nowrap;
}
.sort-link:hover {
    color: #0d6efd;
}
.table-responsive-smooth {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- COMPACT PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Daftar Pegawai</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                Total <?= count($users) ?> Pegawai
            </span>
        </div>
    </div>

    <!-- COMPACT FILTER TOOLBAR (RESPONSIVE GRID) -->
    <div class="card mb-3 border-0 shadow-sm rounded-3">
        <div class="card-body p-2 p-md-3">
            <form action="<?= site_url('daftar-pegawai') ?>" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="sort_by" value="<?= esc($sort_by ?? 'nama_lengkap') ?>">
                <input type="hidden" name="sort_order" value="<?= esc($sort_order ?? 'asc') ?>">
                
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Pencarian</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0" placeholder="Nama, NIP, atau Username..." value="<?= esc($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="unit" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Unit Kerja</label>
                    <select name="unit" id="unit" class="form-select form-select-sm">
                        <option value="">Semua Unit Kerja</option>
                        <?php if (!empty($unit_kerja_list)): ?>
                            <?php foreach ($unit_kerja_list as $uk): ?>
                                <option value="<?= esc($uk['nama_unit']) ?>" <?= ($filter_unit === $uk['nama_unit']) ? 'selected' : '' ?>>
                                    <?= esc($uk['nama_unit']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Role / Peran</label>
                    <select name="role" id="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        <option value="user" <?= ($filter_role === 'user') ? 'selected' : '' ?>>Pegawai (Staf)</option>
                        <option value="kanit" <?= ($filter_role === 'kanit') ? 'selected' : '' ?>>Kepala Unit (Kanit)</option>
                        <option value="katim" <?= ($filter_role === 'katim') ? 'selected' : '' ?>>Ketua Tim (Katim)</option>
                        <option value="kabag" <?= ($filter_role === 'kabag') ? 'selected' : '' ?>>Kepala Bagian (Kabag)</option>
                        <option value="wadir" <?= ($filter_role === 'wadir') ? 'selected' : '' ?>>Wakil Direktur (Wadir)</option>
                        <option value="direktur" <?= ($filter_role === 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="kepegawaian" <?= ($filter_role === 'kepegawaian') ? 'selected' : '' ?>>Kepegawaian</option>
                        <option value="spm" <?= ($filter_role === 'spm') ? 'selected' : '' ?>>SPM</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm px-3" style="min-height: 31px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- COMPACT HIGH-DENSITY TABLE (MOBILE FRIENDLY RESPONSIVE) -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="table-responsive-smooth">
            <table class="table table-sm table-hover compact-table align-middle mb-0" style="min-width: 680px;">
                <thead class="table-light border-bottom">
                    <?php 
                    // Function helper for sort URL generation
                    $buildSortUrl = function($colKey) use ($search, $filter_unit, $filter_role, $sort_by, $sort_order) {
                        $nextOrder = ($sort_by === $colKey && $sort_order === 'asc') ? 'desc' : 'asc';
                        $params = [
                            'sort_by'    => $colKey,
                            'sort_order' => $nextOrder
                        ];
                        if (!empty($search)) $params['search'] = $search;
                        if (!empty($filter_unit)) $params['unit'] = $filter_unit;
                        if (!empty($filter_role)) $params['role'] = $filter_role;
                        return site_url('daftar-pegawai') . '?' . http_build_query($params);
                    };

                    $getSortIcon = function($colKey) use ($sort_by, $sort_order) {
                        if ($sort_by !== $colKey) return '<i class="bi bi-arrow-down-up text-muted opacity-50"></i>';
                        return $sort_order === 'asc' ? '<i class="bi bi-sort-alpha-down text-primary fw-bold"></i>' : '<i class="bi bi-sort-alpha-down-alt text-primary fw-bold"></i>';
                    };
                    ?>
                    <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 45px;" class="text-center py-2">No</th>
                        <th class="py-2" style="min-width: 220px;">
                            <a href="<?= $buildSortUrl('nama_lengkap') ?>" class="sort-link" title="Urutkan berdasar Nama">
                                Nama Lengkap & NIP <?= $getSortIcon('nama_lengkap') ?>
                            </a>
                        </th>
                        <th class="py-2" style="min-width: 180px;">
                            <a href="<?= $buildSortUrl('jabatan') ?>" class="sort-link" title="Urutkan berdasar Jabatan">
                                Jabatan / Pangkat <?= $getSortIcon('jabatan') ?>
                            </a>
                        </th>
                        <th class="py-2" style="min-width: 160px;">
                            <a href="<?= $buildSortUrl('unit') ?>" class="sort-link" title="Urutkan berdasar Unit Kerja">
                                Unit Kerja <?= $getSortIcon('unit') ?>
                            </a>
                        </th>
                        <th class="py-2" style="min-width: 180px;">
                            <a href="<?= $buildSortUrl('atasan') ?>" class="sort-link" title="Urutkan berdasar Atasan Langsung">
                                Atasan Langsung <?= $getSortIcon('atasan') ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                                Tidak ada data pegawai yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($users as $u): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted" style="font-size: 0.78rem;"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?= render_user_avatar($u, $u['nama_lengkap'] ?? '', 32) ?>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.83rem; line-height: 1.25;"><?= esc($u['nama_lengkap']) ?></span>
                                            <small class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-card-text me-1"></i><?= esc($u['nip'] ?: ($u['username'] ?: '-')) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-block text-dark fw-semibold" style="font-size: 0.78rem; line-height: 1.2;"><?= esc($u['jabatan'] ?: '-') ?></span>
                                    <small class="text-muted" style="font-size: 0.71rem;"><?= esc($u['pangkat'] ?: '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-0.5" style="font-size: 0.72rem; font-weight: 500;">
                                        <i class="bi bi-building me-1"></i><?= esc($u['unit'] ?: 'Belum Diatur') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($u['nama_atasan']) && $u['nama_atasan'] !== '-'): ?>
                                        <span class="text-dark fw-semibold" style="font-size: 0.78rem;"><i class="bi bi-person-badge text-success me-1"></i><?= esc($u['nama_atasan']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
