<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Daftar Pegawai') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .tabular-nums {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    .card-bento-ecc {
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid var(--ecc-border-slate, #e2e8f0);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .dp-page-title {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -0.02em;
        color: var(--text-dark, #0f172a);
    }

    .dp-body {
        font-size: 0.875rem;
    }

    .dp-meta {
        font-size: 0.75rem;
        color: var(--text-muted, #64748b);
    }

    .dp-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary, #475569);
        margin-bottom: 4px;
    }

    .dp-control {
        height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
        border-color: #cbd5e1;
        color: var(--text-dark, #0f172a);
    }

    .dp-control:focus {
        border-color: var(--ecc-primary, #0d6efd);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .dp-input-icon {
        height: 36px;
        border-radius: 8px 0 0 8px;
        border-color: #cbd5e1;
        background: #f8fafc;
        color: var(--text-muted, #64748b);
    }

    .dp-search-input {
        border-radius: 0 8px 8px 0;
    }

    .dp-btn {
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .dp-btn:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .table-bento-scroll {
        max-height: 640px;
        overflow-y: auto;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .table-bento-scroll::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-bento-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .table-bento-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }

    .table-bento-dp {
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    .table-bento-dp thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        color: var(--text-secondary, #475569);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--ecc-border-slate, #e2e8f0);
        white-space: nowrap;
    }

    .table-bento-dp tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-dark, #0f172a);
    }

    .table-bento-dp tbody tr:last-child td {
        border-bottom: 0;
    }

    .sort-link {
        color: var(--text-secondary, #475569);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        margin: -4px;
        border-radius: 4px;
        transition: color 0.18s ease;
        white-space: nowrap;
    }

    .sort-link:hover {
        color: var(--ecc-primary, #0d6efd);
    }

    .sort-link:focus-visible {
        outline: none;
        color: var(--ecc-primary-dark, #1e40af);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
    }

    .dp-unit-text {
        color: var(--text-dark, #0f172a);
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.4;
    }

    .dp-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .dp-mobile-card {
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid var(--ecc-border-slate, #e2e8f0);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .dp-mobile-divider {
        border-top: 1px solid #f1f5f9;
        margin-top: 12px;
        padding-top: 12px;
    }

    @media (max-width: 767.98px) {
        .card-bento-ecc {
            padding: 16px;
        }
        .dp-page-title {
            font-size: 1.15rem;
        }
        .dp-control,
        .dp-input-icon,
        .dp-btn {
            min-height: 44px;
            height: 44px;
        }
        .dp-control {
            font-size: var(--bs-body-font-size, 1rem);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sort-link,
        .btn-tactile {
            transition: none;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$hasActiveFilter = !empty($search) || !empty($filter_unit) || !empty($filter_role);
$totalUsers = count($users);

$buildSortUrl = function (string $colKey) use ($search, $filter_unit, $filter_role, $sort_by, $sort_order): string {
    $nextOrder = ($sort_by === $colKey && $sort_order === 'asc') ? 'desc' : 'asc';
    $params = [
        'sort_by'    => $colKey,
        'sort_order' => $nextOrder,
    ];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if (!empty($filter_unit)) {
        $params['unit'] = $filter_unit;
    }
    if (!empty($filter_role)) {
        $params['role'] = $filter_role;
    }
    return site_url('daftar-pegawai') . '?' . http_build_query($params);
};

$getSortIcon = function (string $colKey) use ($sort_by, $sort_order): string {
    if ($sort_by !== $colKey) {
        return '<i class="bi bi-arrow-down-up opacity-50" aria-hidden="true"></i>';
    }
    return $sort_order === 'asc'
        ? '<i class="bi bi-sort-alpha-down text-primary" aria-hidden="true"></i>'
        : '<i class="bi bi-sort-alpha-down-alt text-primary" aria-hidden="true"></i>';
};

$getAriaSort = function (string $colKey) use ($sort_by, $sort_order): string {
    if ($sort_by !== $colKey) {
        return 'none';
    }
    return $sort_order === 'asc' ? 'ascending' : 'descending';
};

$statusNonAktifLabels = [
    'mutasi_keluar' => ['label' => 'Mutasi Keluar', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'],
    'pensiun'       => ['label' => 'Pensiun', 'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle'],
    'nonaktif'      => ['label' => 'Nonaktif', 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'],
];
?>
<div class="container-fluid px-2 px-md-3 py-2">
    <!-- PAGE HEADER -->
    <div class="mb-3">
        <h1 class="dp-page-title mb-1">Daftar Pegawai</h1>
        <p class="dp-body text-secondary mb-0">
            <?php if ($hasActiveFilter): ?>
                Menampilkan <strong class="text-dark tabular-nums"><?= $totalUsers ?></strong> pegawai sesuai filter aktif.
            <?php else: ?>
                Direktori pegawai PKTJ &middot; <strong class="text-dark tabular-nums"><?= $totalUsers ?></strong> pegawai terdaftar.
            <?php endif; ?>
        </p>
    </div>

    <!-- FILTER TOOLBAR -->
    <div class="card-bento-ecc mb-3">
        <form action="<?= site_url('daftar-pegawai') ?>" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="sort_by" value="<?= esc($sort_by ?? 'nama_lengkap') ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sort_order ?? 'asc') ?>">

            <div class="col-12 col-md-4">
                <label for="search" class="form-label dp-label">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text dp-input-icon border-end-0">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </span>
                    <input
                        type="search"
                        name="search"
                        id="search"
                        class="form-control dp-control dp-search-input border-start-0 ps-1"
                        placeholder="Nama, NIP, atau Nama Pengguna..."
                        value="<?= esc($search ?? '') ?>"
                    >
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <label for="unit" class="form-label dp-label">Unit Kerja</label>
                <select name="unit" id="unit" class="form-select dp-control">
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
                <label for="role" class="form-label dp-label">Peran</label>
                <select name="role" id="role" class="form-select dp-control">
                    <option value="">Semua Peran</option>
                    <option value="user" <?= ($filter_role === 'user') ? 'selected' : '' ?>>Staf</option>
                    <option value="manajemen" <?= ($filter_role === 'manajemen') ? 'selected' : '' ?>>Kanit / Katim / Kapokja</option>
                    <option value="kabag" <?= ($filter_role === 'kabag') ? 'selected' : '' ?>>Kepala Bagian (Kabag)</option>
                    <option value="wadir" <?= ($filter_role === 'wadir') ? 'selected' : '' ?>>Wakil Direktur (Wadir)</option>
                    <option value="direktur" <?= ($filter_role === 'direktur') ? 'selected' : '' ?>>Direktur</option>
                    <option value="dosen" <?= ($filter_role === 'dosen') ? 'selected' : '' ?>>Dosen</option>
                    <option value="kepegawaian" <?= ($filter_role === 'kepegawaian') ? 'selected' : '' ?>>Kepegawaian</option>
                    <option value="spm" <?= ($filter_role === 'spm') ? 'selected' : '' ?>>SPM</option>
                    <option value="tugas_belajar" <?= ($filter_role === 'tugas_belajar') ? 'selected' : '' ?>>Tugas Belajar</option>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary dp-btn btn-tactile <?= $hasActiveFilter ? 'flex-grow-1' : 'w-100' ?>">
                        Filter
                    </button>
                    <?php if ($hasActiveFilter): ?>
                        <a href="<?= site_url('daftar-pegawai') ?>" class="btn btn-outline-secondary dp-btn btn-tactile" title="Atur ulang filter">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- DESKTOP BENTO TABLE (>= 768px) -->
    <div class="card-bento-ecc p-0 overflow-hidden mb-3 d-none d-md-block">
        <div class="table-bento-scroll">
            <table class="table table-hover table-bento-dp align-middle">
                <thead>
                    <tr>
                        <th scope="col" class="text-center" style="width: 48px;">No</th>
                        <th scope="col" aria-sort="<?= $getAriaSort('nama_lengkap') ?>" style="min-width: 240px;">
                            <a href="<?= $buildSortUrl('nama_lengkap') ?>" class="sort-link" title="Urutkan berdasarkan nama lengkap">
                                <span>Nama Lengkap &amp; NIP</span><?= $getSortIcon('nama_lengkap') ?>
                            </a>
                        </th>
                        <th scope="col" aria-sort="<?= $getAriaSort('jabatan') ?>" style="min-width: 240px;">
                            <a href="<?= $buildSortUrl('jabatan') ?>" class="sort-link" title="Urutkan berdasarkan jabatan">
                                <span>Jabatan / Pangkat</span><?= $getSortIcon('jabatan') ?>
                            </a>
                        </th>
                        <th scope="col" aria-sort="<?= $getAriaSort('unit') ?>" style="min-width: 200px;">
                            <a href="<?= $buildSortUrl('unit') ?>" class="sort-link" title="Urutkan berdasarkan unit kerja">
                                <span>Unit Kerja</span><?= $getSortIcon('unit') ?>
                            </a>
                        </th>
                        <th scope="col" aria-sort="<?= $getAriaSort('atasan') ?>" style="min-width: 200px;">
                            <a href="<?= $buildSortUrl('atasan') ?>" class="sort-link" title="Urutkan berdasarkan atasan langsung">
                                <span>Atasan Langsung</span><?= $getSortIcon('atasan') ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary dp-body">
                                Belum ada data pegawai untuk filter ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($users as $u): ?>
                            <?php
                            $statusKey = strtolower(trim((string) ($u['status_pegawai'] ?? 'aktif')));
                            $statusInfo = $statusNonAktifLabels[$statusKey] ?? null;
                            $subParentName = $sub_unit_parent_map[$u['unit'] ?? ''] ?? '';
                            ?>
                            <tr>
                                <td class="text-center fw-semibold text-secondary tabular-nums dp-meta"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?= render_user_avatar($u, $u['nama_lengkap'] ?? '', 36) ?>
                                        <div>
                                            <div class="fw-semibold text-dark dp-body" style="line-height: 1.35;"><?= esc($u['nama_lengkap']) ?></div>
                                            <div class="dp-meta tabular-nums"><?= esc($u['nip'] ?: ($u['username'] ?: '-')) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark dp-body" style="line-height: 1.35;"><?= esc($u['jabatan'] ?: '-') ?></div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                        <span class="dp-meta"><?= esc($u['pangkat'] ?: '-') ?></span>
                                        <?= render_role_badge($u['role']) ?>
                                        <?php if ($statusInfo !== null): ?>
                                            <span class="dp-status-badge <?= $statusInfo['class'] ?>"><?= esc($statusInfo['label']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dp-unit-text"><?= esc($u['unit'] ?: 'Belum Diatur') ?></div>
                                    <?php if ($subParentName !== ''): ?>
                                        <div class="dp-meta">Kelompok Kerja di bawah <?= esc($subParentName) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($u['nama_atasan']) && $u['nama_atasan'] !== '-'): ?>
                                        <div class="fw-medium text-dark dp-body" style="line-height: 1.35;"><?= esc($u['nama_atasan']) ?></div>
                                    <?php else: ?>
                                        <span class="dp-meta">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MOBILE CARD LIST (< 768px) -->
    <div class="d-md-none mb-3">
        <?php if (empty($users)): ?>
            <div class="card-bento-ecc text-center py-4 text-secondary dp-body">
                Belum ada data pegawai untuk filter ini.
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-2">
                <?php $mNo = 1; foreach ($users as $u): ?>
                    <?php
                    $statusKey = strtolower(trim((string) ($u['status_pegawai'] ?? 'aktif')));
                    $statusInfo = $statusNonAktifLabels[$statusKey] ?? null;
                    $subParentName = $sub_unit_parent_map[$u['unit'] ?? ''] ?? '';
                    ?>
                    <article class="dp-mobile-card">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div class="d-flex align-items-center min-w-0">
                                <?= render_user_avatar($u, $u['nama_lengkap'] ?? '', 40) ?>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark dp-body" style="line-height: 1.35;"><?= esc($u['nama_lengkap']) ?></div>
                                    <div class="dp-meta tabular-nums"><?= esc($u['nip'] ?: ($u['username'] ?: '-')) ?></div>
                                </div>
                            </div>
                            <span class="dp-meta tabular-nums fw-semibold flex-shrink-0">#<?= $mNo++ ?></span>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <?= render_role_badge($u['role']) ?>
                            <?php if ($statusInfo !== null): ?>
                                <span class="dp-status-badge <?= $statusInfo['class'] ?>"><?= esc($statusInfo['label']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="dp-mobile-divider d-flex flex-column gap-2">
                            <div>
                                <div class="dp-meta">Jabatan &amp; Pangkat</div>
                                <div class="fw-semibold text-dark dp-body"><?= esc($u['jabatan'] ?: '-') ?></div>
                                <div class="dp-meta"><?= esc($u['pangkat'] ?: '-') ?></div>
                            </div>
                            <div>
                                <div class="dp-meta">Unit Kerja</div>
                                <div class="dp-unit-text"><?= esc($u['unit'] ?: 'Belum Diatur') ?></div>
                                <?php if ($subParentName !== ''): ?>
                                    <div class="dp-meta">Kelompok Kerja di bawah <?= esc($subParentName) ?></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="dp-meta">Atasan Langsung</div>
                                <div class="fw-medium text-dark dp-body">
                                    <?= esc((!empty($u['nama_atasan']) && $u['nama_atasan'] !== '-') ? $u['nama_atasan'] : '-') ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
