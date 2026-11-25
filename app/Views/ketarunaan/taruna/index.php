<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Database Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-filter-tile {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid transparent;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        .status-filter-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
        }
    }
    .status-filter-tile.active-tile {
        box-shadow: 0 0 0 2px #1e40af;
    }
    .avatar-taruna-sm {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #1e40af;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .taruna-btn-reset {
        width: 36px;
        height: 36px;
    }
    .taruna-row-clickable {
        cursor: pointer;
    }
    .taruna-row-clickable:focus-visible {
        outline: 2px solid #1e40af;
        outline-offset: -2px;
        background-color: #eff6ff;
    }
    .taruna-sort-link {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
    }
    .taruna-sort-link:hover,
    .taruna-sort-link:focus-visible {
        color: #1e40af;
    }
    @media (max-width: 767.98px) {
        .taruna-touch-ctrl {
            min-height: 44px !important;
        }
        .taruna-btn-reset {
            min-width: 44px;
            min-height: 44px;
        }
        .taruna-filter-form .form-control,
        .taruna-filter-form .form-select,
        #modalTambahTaruna .form-control,
        #modalTambahTaruna .form-select {
            font-size: 16px !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .status-filter-tile,
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <!-- Flash Feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 1. HEADER & PRIMARY ACTIONS -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-muted small mb-1">
                <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">Ketarunaan</a>
                <span class="mx-1">›</span>
                <span class="text-dark fw-medium">Database Taruna</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">Database Taruna</h1>
            <p class="text-muted mb-0 small">Master data identitas, pendidikan, dan riwayat status taruna PKTJ</p>
        </div>

        <?php if ($can_manage): ?>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= site_url('ketarunaan/taruna/impor') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 40px;">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>Impor Data</span>
                </a>
                <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalTambahTaruna">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Taruna</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. RINGKASAN 6 STATUS PENDIDIKAN (FILTER CEPAT) -->
    <?php
        $currentSortBy    = (string) ($sort_by ?? 'nomor_taruna');
        $currentSortOrder = (string) ($sort_order ?? 'asc');
        $sortQueryBase    = array_filter([
            'prodi_id'    => $filters['prodi_id'] ?? '',
            'angkatan_id' => $filters['angkatan_id'] ?? '',
            'status'      => $filters['status'] ?? '',
            'q'           => $filters['q'] ?? '',
        ]);
        $buildSortUrl = static function (string $col) use ($sortQueryBase, $currentSortBy, $currentSortOrder): string {
            $nextOrder = ($currentSortBy === $col && $currentSortOrder === 'asc') ? 'desc' : 'asc';
            return site_url('ketarunaan/taruna?' . http_build_query(array_merge($sortQueryBase, [
                'sort_by'    => $col,
                'sort_order' => $nextOrder,
            ])));
        };
        $ariaSort = static function (string $col) use ($currentSortBy, $currentSortOrder): string {
            if ($currentSortBy !== $col) {
                return 'none';
            }
            return $currentSortOrder === 'desc' ? 'descending' : 'ascending';
        };
        $sortIcon = static function (string $col) use ($currentSortBy, $currentSortOrder): string {
            if ($currentSortBy !== $col) {
                return 'bi-arrow-down-up text-muted opacity-50';
            }
            return $currentSortOrder === 'desc' ? 'bi-arrow-down text-primary' : 'bi-arrow-up text-primary';
        };
    ?>
    <div class="row g-2 mb-4">
        <?php foreach ($status_catalog as $kode => $meta): ?>
            <?php
                $isActiveTile = (($filters['status'] ?? '') === $kode);
                $tileUrl = site_url('ketarunaan/taruna?' . http_build_query(array_filter([
                    'prodi_id'    => $filters['prodi_id'] ?? '',
                    'angkatan_id' => $filters['angkatan_id'] ?? '',
                    'status'      => $isActiveTile ? '' : $kode,
                    'q'           => $filters['q'] ?? '',
                    'sort_by'     => $currentSortBy !== 'nomor_taruna' ? $currentSortBy : '',
                    'sort_order'  => $currentSortOrder !== 'asc' ? $currentSortOrder : '',
                ])));
            ?>
            <div class="col-6 col-sm-4 col-xxl-2">
                <a href="<?= esc($tileUrl) ?>" class="status-filter-tile h-100 <?= $isActiveTile ? 'active-tile' : '' ?>" style="background-color: <?= esc($meta['bg_soft']) ?>; border-color: <?= esc($meta['border_color']) ?>;">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <span class="fw-bold small lh-sm" style="color: <?= esc($meta['text_color'] ?? $meta['color']) ?>;"><?= esc($meta['label']) ?></span>
                        <span class="rounded-circle d-inline-block flex-shrink-0 mt-1" style="width: 8px; height: 8px; background-color: <?= esc($meta['color']) ?>;"></span>
                    </div>
                    <div class="mt-auto">
                        <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format($status_counts[$kode] ?? 0) ?></div>
                        <span class="small" style="color: #475569;">taruna</span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 3. FILTER & TABEL DIREKTORI TARUNA -->
    <div class="bento-card p-3 p-md-4">
        <form method="get" action="<?= site_url('ketarunaan/taruna') ?>" class="row g-2 align-items-center mb-3 pb-3 border-bottom taruna-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($currentSortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($currentSortOrder) ?>">
            <div class="col-12 col-md-5 col-xxl-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-secondary-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control border-secondary-subtle" placeholder="Cari Nomor Taruna atau Nama..." aria-label="Cari Nomor Taruna atau Nama" value="<?= esc($filters['q'] ?? '') ?>" style="height: 36px;">
                </div>
            </div>

            <div class="col-6 col-md-3 col-xxl-2">
                <select name="prodi_id" class="form-select form-select-sm border-secondary-subtle" aria-label="Filter Program Studi" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    <?php foreach ($prodi_list as $p): ?>
                        <option value="<?= esc($p['id']) ?>" <?= (($filters['prodi_id'] ?? '') == $p['id']) ? 'selected' : '' ?>>
                            <?= esc($p['kode_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-4 col-xxl-2">
                <select name="angkatan_id" class="form-select form-select-sm border-secondary-subtle" aria-label="Filter Angkatan" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Angkatan</option>
                    <?php foreach ($angkatan_list as $a): ?>
                        <option value="<?= esc($a['id']) ?>" <?= (($filters['angkatan_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                            Angkatan <?= esc(ketarunaan_format_angkatan($a)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-6 col-xxl-2">
                <select name="status" class="form-select form-select-sm border-secondary-subtle" aria-label="Filter Status Pendidikan" style="height: 36px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <?php foreach ($status_catalog as $kode => $meta): ?>
                        <option value="<?= esc($kode) ?>" <?= (($filters['status'] ?? '') === $kode) ? 'selected' : '' ?>>
                            <?= esc($meta['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6 col-md-6 col-xxl-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold flex-grow-1 d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl text-nowrap" style="height: 36px;">Terapkan</button>
                <?php if (!empty(array_filter($filters))): ?>
                    <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light btn-sm rounded-3 border d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl taruna-btn-reset flex-shrink-0" title="Reset Filter" aria-label="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 small text-muted">
            <div>Menampilkan <strong class="text-dark tabular-nums"><?= number_format(count($rows)) ?></strong> dari <strong class="text-dark tabular-nums"><?= number_format($total_filtered) ?></strong> rekaman taruna</div>
            <div>Klik baris taruna untuk membuka 5 tab rincian profil</div>
        </div>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th class="text-nowrap" aria-sort="<?= esc($ariaSort('nomor_taruna')) ?>">
                            <a href="<?= esc($buildSortUrl('nomor_taruna')) ?>" class="taruna-sort-link">
                                <span>Nomor Taruna</span>
                                <i class="bi <?= esc($sortIcon('nomor_taruna')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($ariaSort('nama_lengkap')) ?>">
                            <a href="<?= esc($buildSortUrl('nama_lengkap')) ?>" class="taruna-sort-link">
                                <span>Nama Taruna</span>
                                <i class="bi <?= esc($sortIcon('nama_lengkap')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th class="text-nowrap" aria-sort="<?= esc($ariaSort('prodi')) ?>">
                            <a href="<?= esc($buildSortUrl('prodi')) ?>" class="taruna-sort-link">
                                <span>Prodi</span>
                                <i class="bi <?= esc($sortIcon('prodi')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th class="text-center text-nowrap" aria-sort="<?= esc($ariaSort('angkatan')) ?>">
                            <a href="<?= esc($buildSortUrl('angkatan')) ?>" class="taruna-sort-link justify-content-center">
                                <span>Angkatan</span>
                                <i class="bi <?= esc($sortIcon('angkatan')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th class="text-center text-nowrap" aria-sort="<?= esc($ariaSort('tingkat')) ?>">
                            <a href="<?= esc($buildSortUrl('tingkat')) ?>" class="taruna-sort-link justify-content-center">
                                <span>Tingkat / Sem</span>
                                <i class="bi <?= esc($sortIcon('tingkat')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th class="text-nowrap" aria-sort="<?= esc($ariaSort('jalur_penerimaan')) ?>">
                            <a href="<?= esc($buildSortUrl('jalur_penerimaan')) ?>" class="taruna-sort-link">
                                <span>Jalur &amp; Formasi</span>
                                <i class="bi <?= esc($sortIcon('jalur_penerimaan')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <th class="text-center text-nowrap" aria-sort="<?= esc($ariaSort('status_terkini')) ?>">
                            <a href="<?= esc($buildSortUrl('status_terkini')) ?>" class="taruna-sort-link justify-content-center">
                                <span>Status Pendidikan</span>
                                <i class="bi <?= esc($sortIcon('status_terkini')) ?>" aria-hidden="true"></i>
                            </a>
                        </th>
                        <?php if ($can_manage): ?>
                            <th class="text-end text-nowrap">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $can_manage ? 8 : 7 ?>" class="text-center py-5 text-muted">
                                Data taruna tidak ditemukan untuk filter yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                                $initials = strtoupper(mb_substr(trim((string) $r['nama_lengkap']), 0, 1));
                                $profilUrl = site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna']));
                                $prodiRingkas = trim((!empty($r['jenjang']) ? $r['jenjang'] . ' ' : '') . ($r['kode_prodi'] ?? '-'));
                                $fotoFile = basename((string) ($r['foto'] ?? ''));
                                $fotoThumbUrl = ($fotoFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $fotoFile))
                                    ? base_url('assets/uploads/taruna/' . $fotoFile)
                                    : null;
                            ?>
                            <tr class="taruna-row-clickable" data-href="<?= esc($profilUrl) ?>" tabindex="0" role="link" aria-label="Buka profil <?= esc($r['nama_lengkap']) ?> (<?= esc($r['nomor_taruna']) ?>)">
                                <td class="fw-bold tabular-nums text-nowrap" style="color: #1e40af;">
                                    <?= esc($r['nomor_taruna']) ?>
                                </td>
                                <td style="min-width: 184px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-taruna-sm small">
                                            <?php if ($fotoThumbUrl !== null): ?>
                                                <img src="<?= esc($fotoThumbUrl) ?>" alt="Foto <?= esc($r['nama_lengkap']) ?>" width="40" height="40" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <?= esc($initials) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($r['nama_lengkap']) ?></div>
                                            <span class="text-muted small"><?= $r['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?> • <?= esc($r['tempat_lahir'] ?: '-') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="fw-semibold text-dark d-block" title="<?= esc($r['nama_prodi'] ?? '-') ?>"><?= esc($prodiRingkas) ?></span>
                                </td>
                                <td class="text-center tabular-nums fw-semibold text-nowrap"><?= esc(ketarunaan_format_angkatan($r)) ?></td>
                                <td class="text-center tabular-nums small text-nowrap">
                                    Tk. <?= (int) ($r['tingkat'] ?? 1) ?> / Sem <?= (int) ($r['semester_aktif'] ?? 1) ?>
                                    <span class="text-muted d-block"><?= !empty($r['nama_kelas']) ? 'Kelas ' . esc($r['nama_kelas']) : 'Kelas belum diatur' ?></span>
                                </td>
                                <td class="small text-nowrap">
                                    <span class="text-dark d-block"><?= esc($r['jalur_penerimaan'] ?? '-') ?></span>
                                    <span class="text-muted"><?= esc($r['asal_formasi'] ?? '-') ?></span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <?= ketarunaan_status_badge((string) $r['status_terkini']) ?>
                                </td>
                                <?php if ($can_manage): ?>
                                    <td class="text-end text-nowrap">
                                        <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode((string) $r['nomor_taruna'])) ?>" class="btn btn-light btn-sm rounded-3 px-3 border text-dark d-inline-flex align-items-center justify-content-center btn-tactile text-nowrap flex-shrink-0" style="min-height: 32px;">
                                            Ubah Status
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Sentuh Mobile (< 768px) -->
        <div class="d-md-none d-flex flex-column gap-2">
            <?php if (empty($rows)): ?>
                <div class="p-4 rounded-3 border bg-white text-center text-muted small">
                    Data taruna tidak ditemukan untuk filter yang dipilih.
                </div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php $profilUrl = site_url('ketarunaan/taruna/profil/' . urlencode((string) $r['nomor_taruna'])); ?>
                    <div class="p-3 rounded-3 border bg-white taruna-row-clickable" data-href="<?= esc($profilUrl) ?>" tabindex="0" role="link" aria-label="Buka profil <?= esc($r['nama_lengkap']) ?>">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($r['nama_lengkap']) ?></div>
                                <div class="fw-semibold small tabular-nums" style="color: #1e40af;">Nomor Taruna <?= esc($r['nomor_taruna']) ?></div>
                            </div>
                            <?= ketarunaan_status_badge((string) $r['status_terkini']) ?>
                        </div>
                        <div class="text-muted small <?= $can_manage ? 'mb-3' : 'mb-0' ?>">
                            <?= esc($r['kode_prodi'] ?? '-') ?> • Angkatan <?= esc(ketarunaan_format_angkatan($r)) ?> • Semester <?= (int) ($r['semester_aktif'] ?? 1) ?>
                        </div>
                        <?php if ($can_manage): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode((string) $r['nomor_taruna'])) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3 flex-grow-1 d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl text-nowrap" style="min-height: 36px;">Ubah Status</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Paginasi -->
        <?php if ($total_pages > 1): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 mt-3 border-top">
                <span class="text-muted small tabular-nums">Halaman <?= $page ?> dari <?= $total_pages ?></span>
                <nav aria-label="Navigasi halaman database taruna">
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php
                            $prevPage = max(1, $page - 1);
                            $nextPage = min($total_pages, $page + 1);
                            $baseQuery = array_filter(array_merge($filters, [
                                'sort_by'    => $currentSortBy !== 'nomor_taruna' ? $currentSortBy : '',
                                'sort_order' => $currentSortOrder !== 'asc' ? $currentSortOrder : '',
                            ]));
                        ?>
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <?php if ($page <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $prevPage]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>" <?= ($p === $page) ? 'aria-current="page"' : '' ?>>
                                <a class="page-link rounded-3 tabular-nums d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $p]))) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <?php if ($page >= $total_pages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" href="<?= site_url('ketarunaan/taruna?' . http_build_query(array_merge($baseQuery, ['page' => $nextPage]))) ?>">Berikutnya</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- MODAL TAMBAH TARUNA BARU -->
<?php if ($can_manage): ?>
<div class="modal fade" id="modalTambahTaruna" tabindex="-1" aria-labelledby="modalTambahTarunaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/store') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <div>
                        <h2 class="h6 modal-title fw-bold text-dark mb-0" id="modalTambahTarunaLabel">Tambah Data Taruna Baru</h2>
                        <span class="text-muted small">Pastikan Nomor Taruna unik sesuai SK penetapan</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tambahNomorTaruna" class="form-label small fw-semibold">Nomor Taruna <span class="text-danger">*</span></label>
                            <input type="text" id="tambahNomorTaruna" name="nomor_taruna" class="form-control tabular-nums" value="<?= esc(old('nomor_taruna')) ?>" placeholder="Contoh: 26012001" required style="height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label for="tambahNik" class="form-label small fw-semibold">NIK</label>
                            <input type="text" id="tambahNik" name="nik" class="form-control tabular-nums" value="<?= esc(old('nik')) ?>" maxlength="16" inputmode="numeric" placeholder="16 digit NIK" style="height: 40px;">
                        </div>
                        <div class="col-md-8">
                            <label for="tambahNamaLengkap" class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="tambahNamaLengkap" name="nama_lengkap" class="form-control" value="<?= esc(old('nama_lengkap')) ?>" placeholder="Nama lengkap sesuai ijazah" required style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label for="tambahNamaPanggilan" class="form-label small fw-semibold">Nama Panggilan</label>
                            <input type="text" id="tambahNamaPanggilan" name="nama_panggilan" class="form-control" value="<?= esc(old('nama_panggilan')) ?>" placeholder="Nama panggilan" style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label for="tambahJenisKelamin" class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select id="tambahJenisKelamin" name="jenis_kelamin" class="form-select" style="height: 40px;">
                                <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tambahTempatLahir" class="form-label small fw-semibold">Tempat Lahir</label>
                            <input type="text" id="tambahTempatLahir" name="tempat_lahir" class="form-control" value="<?= esc(old('tempat_lahir', '')) ?>" placeholder="Contoh: Tegal" style="height: 40px;">
                        </div>
                        <div class="col-md-4">
                            <label for="tambahTanggalLahir" class="form-label small fw-semibold">Tanggal Lahir</label>
                            <input type="text" id="tambahTanggalLahir" name="tanggal_lahir" class="form-control bg-white" value="<?= esc(old('tanggal_lahir', '')) ?>" placeholder="Pilih tanggal lahir" style="height: 40px;">
                        </div>
                        <div class="col-md-6">
                            <label for="selectTambahProdi" class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                            <select name="program_studi_id" id="selectTambahProdi" class="form-select" required style="height: 40px;">
                                <?php foreach ($prodi_list as $p): ?>
                                    <option value="<?= esc($p['id']) ?>" <?= ((string) old('program_studi_id') === (string) $p['id']) ? 'selected' : '' ?>><?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tambahAngkatan" class="form-label small fw-semibold">Angkatan <span class="text-danger">*</span></label>
                            <select id="tambahAngkatan" name="angkatan_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($angkatan_list as $a): ?>
                                    <option value="<?= esc($a['id']) ?>" <?= ((string) old('angkatan_id') === (string) $a['id']) ? 'selected' : '' ?>><?= esc(ketarunaan_format_angkatan($a)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tambahStatusAwal" class="form-label small fw-semibold">Status Awal</label>
                            <select id="tambahStatusAwal" name="status_terkini" class="form-select" style="height: 40px;">
                                <option value="aktif" <?= old('status_terkini', 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="cuti" <?= old('status_terkini') === 'cuti' ? 'selected' : '' ?>>Cuti Akademik</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="selectTambahTingkat" class="form-label small fw-semibold">Tingkat</label>
                            <select name="tingkat" id="selectTambahTingkat" class="form-select tabular-nums" style="height: 40px;">
                                <?php $oldTingkat = (int) old('tingkat', '1'); ?>
                                <?php for ($t = 1; $t <= 4; $t++): ?>
                                    <option value="<?= $t ?>" <?= ($oldTingkat === $t) ? 'selected' : '' ?>>Tingkat <?= $t ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label for="selectTambahKelas" class="form-label small fw-semibold">Kelas <span class="text-muted fw-normal">(Opsional)</span></label>
                            <select name="kelas_id" id="selectTambahKelas" class="form-select" style="height: 40px;">
                                <option value="">-- Belum ditentukan --</option>
                                <?php foreach (($kelas_list ?? []) as $k): ?>
                                    <option value="<?= esc($k['id']) ?>" data-prodi-id="<?= (int) $k['program_studi_id'] ?>" data-tingkat="<?= (int) $k['tingkat'] ?>" data-angkatan-id="<?= (int) ($k['angkatan_id'] ?? 0) ?>" <?= ((string) old('kelas_id') === (string) $k['id']) ? 'selected' : '' ?>>
                                        <?= esc($k['kode_prodi'] . ' Tingkat ' . $k['tingkat'] . ' - Kelas ' . $k['nama_kelas'] . ' (' . ketarunaan_format_angkatan($k) . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-3 px-4 border d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" data-bs-dismiss="modal" style="height: 40px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">Simpan Taruna</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const modalEl = document.getElementById('modalTambahTaruna');
        if (modalEl && typeof bootstrap !== 'undefined') {
            if (params.get('modal') === 'tambah') {
                new bootstrap.Modal(modalEl).show();
            }
            modalEl.addEventListener('hidden.bs.modal', function () {
                if (new URLSearchParams(window.location.search).get('modal') === 'tambah') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('modal');
                    window.history.replaceState({}, '', url.toString());
                }
            });
        }

        document.querySelectorAll('.taruna-row-clickable').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button, input, select, label')) {
                    return;
                }
                const href = row.getAttribute('data-href');
                if (!href) return;
                if (e.ctrlKey || e.metaKey) {
                    window.open(href, '_blank', 'noopener');
                } else {
                    window.location.href = href;
                }
            });
            row.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' && e.key !== ' ') return;
                if (e.target.closest('a, button, input, select, label')) {
                    return;
                }
                e.preventDefault();
                const href = row.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });

        const formTambahTaruna = modalEl ? modalEl.querySelector('form') : null;
        const elTambahTglLahir = document.getElementById('tambahTanggalLahir');
        if (elTambahTglLahir && typeof flatpickr !== 'undefined') {
            const fpTambahLahir = flatpickr(elTambahTglLahir, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                maxDate: 'today'
            });
            if (fpTambahLahir && fpTambahLahir.altInput) {
                fpTambahLahir.altInput.setAttribute('aria-label', 'Tanggal Lahir');
            }
        }

        const inputTambahNik = document.getElementById('tambahNik');
        if (formTambahTaruna && inputTambahNik) {
            formTambahTaruna.addEventListener('submit', function (e) {
                const rawNik = inputTambahNik.value.trim();
                if (rawNik !== '') {
                    const digits = rawNik.replace(/\D/g, '');
                    if (digits.length !== 16) {
                        e.preventDefault();
                        inputTambahNik.focus();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Format NIK Belum Sesuai',
                                text: 'NIK harus terdiri dari tepat 16 digit angka.'
                            });
                        } else {
                            alert('NIK harus terdiri dari tepat 16 digit angka.');
                        }
                    }
                }
            });
        }

        const selProdi    = document.getElementById('selectTambahProdi');
        const selAngkatan = document.getElementById('tambahAngkatan');
        const selTingkat  = document.getElementById('selectTambahTingkat');
        const selKelas    = document.getElementById('selectTambahKelas');
        if (selProdi && selTingkat && selKelas) {
            const allKelasOptions = Array.from(selKelas.options).map(function (opt) {
                return {
                    value: opt.value,
                    text: opt.text,
                    prodiId: opt.getAttribute('data-prodi-id') || '',
                    tingkat: opt.getAttribute('data-tingkat') || '',
                    angkatanId: opt.getAttribute('data-angkatan-id') || '',
                    selected: opt.selected
                };
            });

            function syncKelasFilter() {
                const pid = selProdi.value;
                const tkt = selTingkat.value;
                const aid = selAngkatan ? selAngkatan.value : '';
                const currentVal = selKelas.value;
                selKelas.replaceChildren();

                const exactMatches = allKelasOptions.filter(function (item) {
                    return item.value !== '' && item.prodiId === pid && item.tingkat === tkt && (!aid || item.angkatanId === aid);
                });
                const useStrictAngkatan = aid !== '' && exactMatches.length > 0;

                let restored = false;
                allKelasOptions.forEach(function (item) {
                    const matchProdiTingkat = item.prodiId === pid && item.tingkat === tkt;
                    const matchAngkatan = !useStrictAngkatan || item.angkatanId === aid;
                    if (item.value === '' || (matchProdiTingkat && matchAngkatan)) {
                        const opt = document.createElement('option');
                        opt.value = item.value;
                        opt.textContent = item.text;
                        if (item.value !== '' && item.value === currentVal) {
                            opt.selected = true;
                            restored = true;
                        }
                        selKelas.appendChild(opt);
                    }
                });

                if (!restored) {
                    selKelas.value = '';
                }
            }

            selProdi.addEventListener('change', syncKelasFilter);
            selTingkat.addEventListener('change', syncKelasFilter);
            if (selAngkatan) {
                selAngkatan.addEventListener('change', syncKelasFilter);
            }
            syncKelasFilter();
        }
    });
</script>
<?= $this->endSection() ?>

