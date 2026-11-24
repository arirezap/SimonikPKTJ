<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Unit Kerja') ?><?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Unit Kerja<?= $this->endSection() ?>

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

    .uk-kpi-card {
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid var(--ecc-border-slate, #e2e8f0);
        border-radius: 16px;
        padding: 16px 24px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.18s ease, box-shadow 0.2s ease;
        height: 100%;
    }

    .uk-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .uk-kpi-card:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .uk-kpi-card.is-active {
        border: 2px solid var(--ecc-primary, #0d6efd);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .uk-kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-dark, #0f172a);
    }

    .uk-title-sm {
        font-size: 0.95rem;
    }

    .uk-body {
        font-size: 0.875rem;
    }

    .uk-meta {
        font-size: 0.75rem;
        color: var(--text-muted, #64748b);
    }

    .uk-control {
        height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
        border-color: #cbd5e1;
        color: var(--text-dark, #0f172a);
    }

    .uk-control:focus {
        border-color: var(--ecc-primary, #0d6efd);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .uk-input-icon {
        height: 36px;
        border-radius: 8px 0 0 8px;
        border-color: #cbd5e1;
        background: #f8fafc;
        color: var(--text-muted, #64748b);
    }

    .uk-search-input {
        border-radius: 0 8px 8px 0;
    }

    .uk-btn {
        min-height: 36px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .uk-btn:focus-visible,
    .uk-filter-pill:focus-visible,
    .uk-icon-btn:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .uk-filter-pill {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 9999px;
        border: 1px solid #cbd5e1;
        background: var(--ecc-surface-white, #ffffff);
        color: var(--text-secondary, #475569);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    }

    .uk-filter-pill.active {
        background: var(--ecc-primary-dark, #1e40af);
        border-color: var(--ecc-primary-dark, #1e40af);
        color: #ffffff;
    }

    .uk-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .uk-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .table-bento-scroll {
        max-height: 600px;
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

    .table-bento-scroll::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }

    .table-bento-uk {
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    .table-bento-uk thead th {
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

    .table-bento-uk tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-dark, #0f172a);
    }

    .table-bento-uk tbody tr.row-induk {
        background: #f8fafc;
    }

    .uk-sub-unit-wrap {
        padding-left: 24px;
    }

    .uk-mobile-card {
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid var(--ecc-border-slate, #e2e8f0);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .uk-mobile-card.row-induk {
        background: #f8fafc;
    }

    .uk-mobile-card.row-subunit {
        margin-left: 16px;
    }

    @media (max-width: 767.98px) {
        .card-bento-ecc,
        .uk-kpi-card {
            padding: 16px;
        }
        .uk-control,
        .uk-input-icon,
        .uk-btn,
        .uk-filter-pill {
            min-height: 44px;
            height: 44px;
        }
        .uk-icon-btn {
            width: 44px;
            height: 44px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .uk-kpi-card,
        .uk-filter-pill,
        .btn-tactile {
            transition: none;
        }
        .uk-kpi-card:hover {
            transform: none;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$summary = $hierarchySummary ?? [
    'pimpinan' => ['label' => 'Top Manajemen', 'subtitle' => 'Direktur & Wakil Direktur', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
    'aak'      => ['label' => 'Bagian AAK', 'subtitle' => 'Administrasi Akademik & Ketarunaan', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
    'kuk'      => ['label' => 'Bagian KUK', 'subtitle' => 'Keuangan, Umum & Kerjasama', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0],
];
$parentsList = $potentialParents ?? [];
?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 uk-body" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 uk-body" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 uk-body" role="alert">
        <strong>Periksa kembali isian berikut:</strong>
        <ul class="mb-0 mt-1 ps-3">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- 3 Kartu Ringkasan Hierarki Unit Kerja -->
<div class="row g-3 mb-3">
    <?php
    $kpiMeta = [
        'pimpinan' => ['badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle'],
        'aak'      => ['badge_class' => 'bg-success-subtle text-success border border-success-subtle'],
        'kuk'      => ['badge_class' => 'bg-info-subtle text-info-emphasis border border-info-subtle'],
    ];
    foreach (['pimpinan', 'aak', 'kuk'] as $groupKey):
        $g = $summary[$groupKey] ?? ['label' => '', 'subtitle' => '', 'unit_count' => 0, 'sub_unit_count' => 0, 'employee_count' => 0];
        $m = $kpiMeta[$groupKey];
        $uCnt = (int) ($g['unit_count'] ?? 0);
        $sCnt = (int) ($g['sub_unit_count'] ?? 0);
        $badgeText = $groupKey === 'pimpinan'
            ? ($uCnt . ' unit pimpinan')
            : ($sCnt > 0 ? ($uCnt . ' unit + ' . $sCnt . ' Pokja') : ($uCnt . ' unit pelaksana'));
    ?>
        <div class="col-12 col-md-4">
            <div
                class="uk-kpi-card"
                role="button"
                tabindex="0"
                data-group-filter="<?= esc($groupKey) ?>"
                aria-label="Saring kelompok <?= esc($g['label']) ?>"
            >
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <span class="fw-bold text-dark uk-title-sm"><?= esc($g['label']) ?></span>
                    <span class="uk-pill <?= $m['badge_class'] ?> tabular-nums"><?= esc($badgeText) ?></span>
                </div>
                <div class="uk-kpi-value tabular-nums mb-1"><?= (int) $g['employee_count'] ?> <span class="uk-body fw-medium text-secondary">Pegawai</span></div>
                <div class="uk-meta"><?= esc($g['subtitle']) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Toolbar Pencarian, Filter Kelompok, & Tombol Tambah -->
<div class="card-bento-ecc mb-3">
    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-12 col-lg-4">
            <div class="input-group">
                <span class="input-group-text uk-input-icon border-end-0">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </span>
                <input
                    type="search"
                    id="searchUnit"
                    class="form-control uk-control uk-search-input border-start-0 ps-1"
                    aria-label="Cari unit kerja atau penanggung jawab"
                    placeholder="Cari unit kerja, Pokja, atau bagian..."
                >
            </div>
        </div>

        <div class="col-12 col-lg-8 d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
            <div class="d-flex flex-wrap align-items-center gap-1" id="unitGroupPills">
                <button type="button" class="uk-filter-pill active" data-parent-filter="all">Semua</button>
                <button type="button" class="uk-filter-pill" data-parent-filter="pimpinan">Top Manajemen</button>
                <button type="button" class="uk-filter-pill" data-parent-filter="aak">AAK</button>
                <button type="button" class="uk-filter-pill" data-parent-filter="kuk">KUK</button>
            </div>

            <span class="uk-meta ms-lg-2">
                Menampilkan <strong id="countUnit" class="text-dark tabular-nums"><?= count($items) ?></strong> unit
            </span>

            <button type="button" class="btn btn-primary rounded-pill uk-btn btn-tactile" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Tambah Unit Kerja</span>
            </button>
        </div>
    </div>
</div>

<!-- Tabel Desktop (>= 768px) -->
<div class="card-bento-ecc p-0 overflow-hidden mb-3 d-none d-md-block">
    <div class="table-bento-scroll">
        <table class="table table-hover table-bento-uk align-middle" id="tabelUnit">
            <thead>
                <tr>
                    <th scope="col" style="width: 48px;" class="text-center">No</th>
                    <th scope="col">Nama Unit Kerja</th>
                    <th scope="col" style="width: 176px;" class="text-center">Penanggung Jawab</th>
                    <th scope="col" style="width: 240px;">Pegawai Terdaftar</th>
                    <th scope="col" style="width: 112px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $index => $item): ?>
                        <?php
                        $parent        = strtolower(trim((string) ($item['parent_unit'] ?? '')));
                        $isInduk       = !empty($item['is_unit_induk']);
                        $isSubUnit     = !empty($item['is_sub_unit']);
                        $parentName    = (string) ($item['parent_unit_name'] ?? '');
                        $jmlDirect     = (int) ($item['jumlah_pegawai_langsung'] ?? $item['jumlah_pegawai'] ?? 0);
                        $jmlBinaan     = (int) ($item['total_pegawai_binaan'] ?? $jmlDirect);
                        $subUnitCount  = (int) ($item['sub_unit_count'] ?? 0);
                        $subEmpCount   = (int) ($item['sub_unit_employee_count'] ?? 0);
                        $totalWithSub  = (int) ($item['total_pegawai_dengan_sub'] ?? $jmlDirect);
                        $bagianUnits   = (int) ($item['jumlah_unit_binaan'] ?? 0);
                        $bagianSubs    = (int) ($item['jumlah_subunit_binaan'] ?? 0);
                        $stafBagian    = max(0, $jmlBinaan - $jmlDirect);
                        $protectedSubs = $isInduk ? ($bagianUnits + $bagianSubs) : $subUnitCount;
                        $parentText    = $parent === 'pimpinan' ? 'top manajemen pimpinan' : ($parent !== '' ? $parent : 'belum diatur');
                        $searchVal     = strtolower(($item['nama_unit'] ?? '') . ' ' . $parentText . ' ' . $parentName . ($isInduk ? ' unit induk' : '') . ($isSubUnit ? ' kelompok kerja pokja' : ''));
                        ?>
                        <tr
                            class="unit-row <?= ($isInduk || $parent === 'pimpinan') ? 'row-induk' : '' ?>"
                            data-parent="<?= esc($parent ?: 'none') ?>"
                            data-search="<?= esc($searchVal) ?>"
                        >
                            <td class="text-center fw-semibold text-secondary tabular-nums uk-meta nomor-unit"><?= $index + 1 ?></td>
                            <td class="unit-nama">
                                <?php if ($isSubUnit): ?>
                                    <div class="uk-sub-unit-wrap">
                                        <div class="fw-semibold text-dark uk-body">
                                            <i class="bi bi-arrow-return-right text-secondary me-1" aria-hidden="true"></i><?= esc($item['nama_unit']) ?>
                                        </div>
                                        <div class="uk-meta">
                                            Kelompok Kerja di bawah <?= esc($parentName) ?> &middot; Pelaporan kinerja ke Katim
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="<?= ($isInduk || $parent === 'pimpinan' || $subUnitCount > 0) ? 'fw-bold' : 'fw-semibold' ?> text-dark uk-body">
                                        <?= esc($item['nama_unit']) ?>
                                    </div>
                                    <?php if ($parent === 'pimpinan'): ?>
                                        <div class="uk-meta">Direktur &amp; Wakil Direktur</div>
                                    <?php elseif ($isInduk): ?>
                                        <div class="uk-meta tabular-nums">
                                            Unit Induk Bagian &middot; <?= $bagianUnits ?> unit<?= $bagianSubs > 0 ? (' + ' . $bagianSubs . ' Pokja') : '' ?>
                                        </div>
                                    <?php elseif ($subUnitCount > 0): ?>
                                        <div class="uk-meta tabular-nums">
                                            Membawahi <?= $subUnitCount ?> Kelompok Kerja (Pokja)
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center unit-parent">
                                <?php if ($parent === 'pimpinan'): ?>
                                    <span class="uk-pill bg-primary-subtle text-primary border border-primary-subtle">Top Manajemen</span>
                                <?php elseif ($parent === 'aak'): ?>
                                    <span class="uk-pill bg-success-subtle text-success border border-success-subtle">AAK</span>
                                <?php elseif ($parent === 'kuk'): ?>
                                    <span class="uk-pill bg-info-subtle text-info-emphasis border border-info-subtle">KUK</span>
                                <?php else: ?>
                                    <span class="uk-pill bg-light text-secondary border">Belum Diatur</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isInduk): ?>
                                    <div class="fw-bold text-dark tabular-nums uk-body"><?= $jmlBinaan ?> Pegawai</div>
                                    <div class="uk-meta tabular-nums"><?= $jmlDirect ?> Kabag + <?= $stafBagian ?> staf binaan</div>
                                <?php elseif ($subUnitCount > 0): ?>
                                    <div class="fw-bold text-dark tabular-nums uk-body"><?= $totalWithSub ?> Pegawai</div>
                                    <div class="uk-meta tabular-nums"><?= $jmlDirect ?> inti + <?= $subEmpCount ?> di <?= $subUnitCount ?> Pokja</div>
                                <?php else: ?>
                                    <span class="fw-semibold tabular-nums uk-body <?= $jmlDirect > 0 ? 'text-dark' : 'text-secondary' ?>">
                                        <?= $jmlDirect ?> Pegawai
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex justify-content-center gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-warning uk-icon-btn btn-tactile"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal-<?= (int) $item['id'] ?>"
                                        title="Edit Unit Kerja"
                                        aria-label="Edit <?= esc($item['nama_unit']) ?>"
                                    >
                                        <i class="bi bi-pencil-fill" aria-hidden="true"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger uk-icon-btn btn-tactile btn-hapus"
                                        onclick="confirmDeleteUnit(<?= (int) $item['id'] ?>, '<?= esc($item['nama_unit'], 'js') ?>', <?= $jmlDirect ?>, <?= $protectedSubs ?>)"
                                        title="Hapus Unit Kerja"
                                        aria-label="Hapus <?= esc($item['nama_unit']) ?>"
                                    >
                                        <i class="bi bi-trash-fill" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="emptyRow">
                        <td colspan="5" class="text-center py-5 text-secondary uk-body">Belum ada data unit kerja.</td>
                    </tr>
                <?php endif; ?>
                <tr id="noSearchResult" class="d-none">
                    <td colspan="5" class="text-center py-4 text-secondary uk-body">
                        Tidak ada unit kerja yang cocok dengan pencarian.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Daftar Kartu Mobile (< 768px) -->
<div class="d-md-none mb-3" id="mobileUnitList">
    <?php if (empty($items)): ?>
        <div class="card-bento-ecc text-center py-4 text-secondary uk-body">Belum ada data unit kerja.</div>
    <?php else: ?>
        <div class="d-flex flex-column gap-2">
            <?php foreach ($items as $item): ?>
                <?php
                $parent        = strtolower(trim((string) ($item['parent_unit'] ?? '')));
                $isInduk       = !empty($item['is_unit_induk']);
                $isSubUnit     = !empty($item['is_sub_unit']);
                $parentName    = (string) ($item['parent_unit_name'] ?? '');
                $jmlDirect     = (int) ($item['jumlah_pegawai_langsung'] ?? $item['jumlah_pegawai'] ?? 0);
                $jmlBinaan     = (int) ($item['total_pegawai_binaan'] ?? $jmlDirect);
                $subUnitCount  = (int) ($item['sub_unit_count'] ?? 0);
                $subEmpCount   = (int) ($item['sub_unit_employee_count'] ?? 0);
                $totalWithSub  = (int) ($item['total_pegawai_dengan_sub'] ?? $jmlDirect);
                $bagianUnits   = (int) ($item['jumlah_unit_binaan'] ?? 0);
                $bagianSubs    = (int) ($item['jumlah_subunit_binaan'] ?? 0);
                $stafBagian    = max(0, $jmlBinaan - $jmlDirect);
                $protectedSubs = $isInduk ? ($bagianUnits + $bagianSubs) : $subUnitCount;
                $parentText    = $parent === 'pimpinan' ? 'top manajemen pimpinan' : ($parent !== '' ? $parent : 'belum diatur');
                $searchVal     = strtolower(($item['nama_unit'] ?? '') . ' ' . $parentText . ' ' . $parentName . ($isInduk ? ' unit induk' : '') . ($isSubUnit ? ' kelompok kerja pokja' : ''));
                ?>
                <article
                    class="uk-mobile-card unit-mobile-item <?= ($isInduk || $parent === 'pimpinan') ? 'row-induk' : '' ?> <?= $isSubUnit ? 'row-subunit' : '' ?>"
                    data-parent="<?= esc($parent ?: 'none') ?>"
                    data-search="<?= esc($searchVal) ?>"
                >
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark uk-body">
                                <?php if ($isSubUnit): ?>
                                    <i class="bi bi-arrow-return-right text-secondary me-1" aria-hidden="true"></i>
                                <?php endif; ?>
                                <?= esc($item['nama_unit']) ?>
                            </div>
                            <?php if ($parent === 'pimpinan'): ?>
                                <div class="uk-meta">Direktur &amp; Wakil Direktur</div>
                            <?php elseif ($isInduk): ?>
                                <div class="uk-meta tabular-nums">Unit Induk Bagian &middot; <?= $bagianUnits ?> unit<?= $bagianSubs > 0 ? (' + ' . $bagianSubs . ' Pokja') : '' ?></div>
                            <?php elseif ($isSubUnit): ?>
                                <div class="uk-meta">Kelompok Kerja di bawah <?= esc($parentName) ?></div>
                            <?php elseif ($subUnitCount > 0): ?>
                                <div class="uk-meta tabular-nums">Membawahi <?= $subUnitCount ?> Kelompok Kerja (Pokja)</div>
                            <?php endif; ?>
                        </div>
                        <?php if ($parent === 'pimpinan'): ?>
                            <span class="uk-pill bg-primary-subtle text-primary border border-primary-subtle">Top Manajemen</span>
                        <?php elseif ($parent === 'aak'): ?>
                            <span class="uk-pill bg-success-subtle text-success border border-success-subtle">AAK</span>
                        <?php elseif ($parent === 'kuk'): ?>
                            <span class="uk-pill bg-info-subtle text-info-emphasis border border-info-subtle">KUK</span>
                        <?php else: ?>
                            <span class="uk-pill bg-light text-secondary border">Belum Diatur</span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top">
                        <div>
                            <?php if ($isInduk): ?>
                                <div class="fw-bold text-dark tabular-nums uk-body"><?= $jmlBinaan ?> Pegawai</div>
                                <div class="uk-meta tabular-nums"><?= $jmlDirect ?> Kabag + <?= $stafBagian ?> staf binaan</div>
                            <?php elseif ($subUnitCount > 0): ?>
                                <div class="fw-bold text-dark tabular-nums uk-body"><?= $totalWithSub ?> Pegawai</div>
                                <div class="uk-meta tabular-nums"><?= $jmlDirect ?> inti + <?= $subEmpCount ?> di Pokja</div>
                            <?php else: ?>
                                <div class="fw-semibold text-dark tabular-nums uk-body"><?= $jmlDirect ?> Pegawai</div>
                            <?php endif; ?>
                        </div>
                        <div class="d-inline-flex gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-warning uk-icon-btn btn-tactile"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal-<?= (int) $item['id'] ?>"
                                aria-label="Edit <?= esc($item['nama_unit']) ?>"
                            >
                                <i class="bi bi-pencil-fill" aria-hidden="true"></i>
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-danger uk-icon-btn btn-tactile"
                                onclick="confirmDeleteUnit(<?= (int) $item['id'] ?>, '<?= esc($item['nama_unit'], 'js') ?>', <?= $jmlDirect ?>, <?= $protectedSubs ?>)"
                                aria-label="Hapus <?= esc($item['nama_unit']) ?>"
                            >
                                <i class="bi bi-trash-fill" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div id="noSearchResultMobile" class="card-bento-ecc text-center py-4 text-secondary uk-body d-none">
            Tidak ada unit kerja yang cocok dengan pencarian.
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Unit Kerja -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark mb-0 uk-title-sm" id="addModalLabel">Tambah Unit Kerja Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= site_url('master-data/unit-kerja/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_unit" class="form-label fw-semibold text-dark uk-body mb-1">Nama Unit Kerja <span class="text-danger">*</span></label>
                        <input type="text" name="nama_unit" id="nama_unit" class="form-control uk-control" placeholder="Contoh: Prodi TRO, Pokja Diklat" value="<?= esc(old('nama_unit')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="parent_unit_id" class="form-label fw-semibold text-dark uk-body mb-1">Tim Substansi Induk (Khusus Kelompok Kerja / Pokja)</label>
                        <select name="parent_unit_id" id="parent_unit_id" class="form-select uk-control js-parent-unit-id-select" data-bagian-target="#parent_unit">
                            <option value="">-- Berdiri Sendiri (Bukan Pokja) --</option>
                            <?php foreach ($parentsList as $pp): ?>
                                <option value="<?= (int) $pp['id'] ?>" data-bagian="<?= esc($pp['parent_unit']) ?>" <?= ((string) old('parent_unit_id') === (string) $pp['id']) ? 'selected' : '' ?>>
                                    <?= esc($pp['nama_unit']) ?> (<?= strtoupper(esc($pp['parent_unit'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="uk-meta mt-1">Isi jika unit ini adalah Kelompok Kerja (Pokja) yang melapor ke Katim induknya.</div>
                    </div>
                    <div class="mb-1">
                        <label for="parent_unit" class="form-label fw-semibold text-dark uk-body mb-1">Penanggung Jawab</label>
                        <select name="parent_unit" id="parent_unit" class="form-select uk-control">
                            <option value="">-- Pilih Penanggung Jawab (Opsional) --</option>
                            <option value="pimpinan" <?= (old('parent_unit') === 'pimpinan') ? 'selected' : '' ?>>Top Manajemen (Direktur &amp; Wakil Direktur)</option>
                            <option value="aak" <?= (old('parent_unit') === 'aak') ? 'selected' : '' ?>>AAK (Bagian Administrasi Akademik &amp; Ketarunaan)</option>
                            <option value="kuk" <?= (old('parent_unit') === 'kuk') ? 'selected' : '' ?>>KUK (Bagian Keuangan, Umum &amp; Kerjasama)</option>
                        </select>
                        <div class="uk-meta mt-1">Menentukan pengelompokan unit, hirarki atasan, dan bukti LED.</div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border rounded-pill uk-btn btn-tactile" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill uk-btn btn-tactile">Simpan Unit Kerja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Unit Kerja -->
<?php if (!empty($items)): ?>
    <?php foreach ($items as $item): ?>
        <?php
        $itemId = (int) $item['id'];
        $itemIsInduk = !empty($item['is_unit_induk']);
        $itemHasChildren = (int) ($item['sub_unit_count'] ?? 0) > 0;
        $itemIsPimpinan = (($item['parent_unit'] ?? '') === 'pimpinan');
        $canSetParentUnitId = !$itemIsInduk && !$itemHasChildren && !$itemIsPimpinan;
        ?>
        <div class="modal fade" id="editModal-<?= $itemId ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $itemId ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark mb-0 uk-title-sm" id="editModalLabel-<?= $itemId ?>">Edit Unit Kerja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form action="<?= site_url('master-data/unit-kerja/update/' . $itemId) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="nama_unit_<?= $itemId ?>" class="form-label fw-semibold text-dark uk-body mb-1">Nama Unit Kerja <span class="text-danger">*</span></label>
                                <input type="text" name="nama_unit" id="nama_unit_<?= $itemId ?>" class="form-control uk-control" value="<?= esc($item['nama_unit']) ?>" required>
                            </div>
                            <?php if ($canSetParentUnitId): ?>
                                <div class="mb-3">
                                    <label for="parent_unit_id_<?= $itemId ?>" class="form-label fw-semibold text-dark uk-body mb-1">Tim Substansi Induk (Khusus Kelompok Kerja / Pokja)</label>
                                    <select name="parent_unit_id" id="parent_unit_id_<?= $itemId ?>" class="form-select uk-control js-parent-unit-id-select" data-bagian-target="#parent_unit_<?= $itemId ?>">
                                        <option value="">-- Berdiri Sendiri (Bukan Pokja) --</option>
                                        <?php foreach ($parentsList as $pp): ?>
                                            <?php if ((int) $pp['id'] === $itemId) continue; ?>
                                            <option value="<?= (int) $pp['id'] ?>" data-bagian="<?= esc($pp['parent_unit']) ?>" <?= ((int) ($item['parent_unit_id'] ?? 0) === (int) $pp['id']) ? 'selected' : '' ?>>
                                                <?= esc($pp['nama_unit']) ?> (<?= strtoupper(esc($pp['parent_unit'])) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="uk-meta mt-1">Pilih Tim Substansi induk jika unit ini adalah Kelompok Kerja (Pokja).</div>
                                </div>
                            <?php endif; ?>
                            <div class="mb-1">
                                <label for="parent_unit_<?= $itemId ?>" class="form-label fw-semibold text-dark uk-body mb-1">Penanggung Jawab</label>
                                <select name="parent_unit" id="parent_unit_<?= $itemId ?>" class="form-select uk-control">
                                    <option value="">-- Pilih Penanggung Jawab (Opsional) --</option>
                                    <option value="pimpinan" <?= (($item['parent_unit'] ?? '') === 'pimpinan') ? 'selected' : '' ?>>Top Manajemen (Direktur &amp; Wakil Direktur)</option>
                                    <option value="aak" <?= (($item['parent_unit'] ?? '') === 'aak') ? 'selected' : '' ?>>AAK (Bagian Administrasi Akademik &amp; Ketarunaan)</option>
                                    <option value="kuk" <?= (($item['parent_unit'] ?? '') === 'kuk') ? 'selected' : '' ?>>KUK (Bagian Keuangan, Umum &amp; Kerjasama)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-3">
                            <button type="button" class="btn btn-light border rounded-pill uk-btn btn-tactile" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill uk-btn btn-tactile">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Form Hapus Tersembunyi -->
<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    let activeParentFilter = 'all';

    function applyUnitFilters() {
        const query = (document.getElementById('searchUnit')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.unit-row');
        const mobileCards = document.querySelectorAll('.unit-mobile-item');
        let visibleCount = 0;

        rows.forEach(row => {
            const parent = row.dataset.parent || '';
            const text = row.dataset.search || '';
            const matchParent = (activeParentFilter === 'all' || parent === activeParentFilter);
            const matchQuery = (query === '' || text.includes(query));
            const isVisible = matchParent && matchQuery;

            row.classList.toggle('d-none', !isVisible);
            if (isVisible) {
                visibleCount++;
                const noCell = row.querySelector('.nomor-unit');
                if (noCell) noCell.textContent = String(visibleCount);
            }
        });

        mobileCards.forEach(card => {
            const parent = card.dataset.parent || '';
            const text = card.dataset.search || '';
            const matchParent = (activeParentFilter === 'all' || parent === activeParentFilter);
            const matchQuery = (query === '' || text.includes(query));
            card.classList.toggle('d-none', !(matchParent && matchQuery));
        });

        const noResult = document.getElementById('noSearchResult');
        if (noResult) {
            noResult.classList.toggle('d-none', !(visibleCount === 0 && rows.length > 0));
        }

        const noResultMobile = document.getElementById('noSearchResultMobile');
        if (noResultMobile) {
            noResultMobile.classList.toggle('d-none', !(visibleCount === 0 && mobileCards.length > 0));
        }

        const countEl = document.getElementById('countUnit');
        if (countEl) {
            countEl.textContent = String(visibleCount);
        }
    }

    function setParentFilter(filterVal) {
        activeParentFilter = filterVal || 'all';

        document.querySelectorAll('#unitGroupPills .uk-filter-pill').forEach(btn => {
            btn.classList.toggle('active', (btn.dataset.parentFilter || 'all') === activeParentFilter);
        });

        document.querySelectorAll('.uk-kpi-card[data-group-filter]').forEach(card => {
            card.classList.toggle('is-active', card.dataset.groupFilter === activeParentFilter);
        });

        applyUnitFilters();
    }

    document.getElementById('searchUnit')?.addEventListener('input', applyUnitFilters);

    document.querySelectorAll('#unitGroupPills .uk-filter-pill').forEach(btn => {
        btn.addEventListener('click', function () {
            setParentFilter(this.dataset.parentFilter || 'all');
        });
    });

    document.querySelectorAll('.uk-kpi-card[data-group-filter]').forEach(card => {
        const toggleCardFilter = () => {
            const group = card.dataset.groupFilter || 'all';
            setParentFilter(activeParentFilter === group ? 'all' : group);
        };
        card.addEventListener('click', toggleCardFilter);
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleCardFilter();
            }
        });
    });

    // Otomatis selaraskan Penanggung Jawab (AAK/KUK) saat memilih Induk Unit (Tim Substansi)
    document.querySelectorAll('.js-parent-unit-id-select').forEach(sel => {
        sel.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            const bagian = selectedOpt?.dataset?.bagian || '';
            const targetSel = document.querySelector(this.dataset.bagianTarget || '');
            if (targetSel && bagian) {
                targetSel.value = bagian;
            }
        });
    });

    window.confirmDeleteUnit = function (id, name, employeeCount, subUnitCount) {
        if (Number(employeeCount) > 0) {
            const msg = 'Unit kerja "' + name + '" masih memiliki ' + employeeCount + ' pegawai terdaftar. Pindahkan pegawai di menu Kelola Pengguna terlebih dahulu.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Dapat Dihapus',
                    text: msg,
                    confirmButtonText: 'Mengerti',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow border-0 p-4',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        htmlContainer: 'text-secondary small mb-3',
                        confirmButton: 'btn btn-primary rounded-pill px-4 py-2 fw-semibold btn-tactile'
                    }
                });
            } else {
                window.alert(msg);
            }
            return;
        }

        if (Number(subUnitCount) > 0) {
            const msg = 'Unit "' + name + '" masih membawahi ' + subUnitCount + ' unit atau Kelompok Kerja (Pokja) di bawahnya.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Dapat Dihapus',
                    text: msg,
                    confirmButtonText: 'Mengerti',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow border-0 p-4',
                        title: 'fw-bold text-dark fs-5 mb-2',
                        htmlContainer: 'text-secondary small mb-3',
                        confirmButton: 'btn btn-primary rounded-pill px-4 py-2 fw-semibold btn-tactile'
                    }
                });
            } else {
                window.alert(msg);
            }
            return;
        }

        function executeDelete() {
            const form = document.getElementById('formHapus');
            if (!form) return;
            form.action = <?= json_encode(site_url('master-data/unit-kerja/delete/')) ?> + String(id);
            form.submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Unit Kerja?',
                text: 'Unit kerja "' + name + '" akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-secondary small mb-3',
                    confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-semibold ms-2 btn-tactile',
                    cancelButton: 'btn btn-light border rounded-pill px-4 py-2 fw-semibold btn-tactile'
                }
            }).then(result => {
                if (result.isConfirmed) executeDelete();
            });
        } else if (window.confirm('Hapus unit kerja "' + name + '"?')) {
            executeDelete();
        }
    };

    <?php if (session()->getFlashdata('show_modal')): ?>
    const modalEl = document.getElementById(<?= json_encode((string) session()->getFlashdata('show_modal')) ?>);
    if (modalEl && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
    <?php endif; ?>
})();
</script>
<?= $this->endSection() ?>
