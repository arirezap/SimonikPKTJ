<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Prestasi Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/prestasi/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$metrics    = $metrics ?? [];
$rows       = $rows ?? [];
$pagination = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$filters    = $filters ?? [
    'q'             => '',
    'jenis'         => 'all',
    'tingkat'       => 'all',
    'prodi'         => 'all',
    'periode'       => 'all',
    'status'        => 'all',
    'keikutsertaan' => 'all',
    'milik_saya'    => '',
    'sort_by'       => '',
    'sort_order'    => 'desc',
];
$refs = $refs ?? [];

$sortBy        = (string) ($filters['sort_by'] ?? '');
$sortOrder     = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$curStatus     = (string) ($filters['status'] ?? 'all');
$curKeikut     = (string) ($filters['keikutsertaan'] ?? 'all');
$curMilikSaya  = ((string) ($filters['milik_saya'] ?? '') === '1');
$exportQuery   = http_build_query($filters);
$totalPrestasi = (int) ($metrics['prestasi_tervalidasi'] ?? 0);

$hasActiveFilters = (
    ($filters['q'] ?? '') !== ''
    || ($filters['jenis'] ?? 'all') !== 'all'
    || ($filters['tingkat'] ?? 'all') !== 'all'
    || ($filters['prodi'] ?? 'all') !== 'all'
    || ($filters['periode'] ?? 'all') !== 'all'
    || $curStatus !== 'all'
    || $curKeikut !== 'all'
    || $curMilikSaya
);

$showAksiCol = !empty($canManageTaruna) || !empty($isSelfTaruna);

$buildSortUrl = static function (string $colKey) use ($filters, $sortBy, $sortOrder): string {
    $nextOrder = ($sortBy === $colKey && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, [
        'sort_by'    => $colKey,
        'sort_order' => $nextOrder,
        'page'       => 1,
    ])));
};
$getSortIcon = static function (string $colKey) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $colKey) {
        return '<i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i>';
    }
    return $sortOrder === 'asc'
        ? '<i class="bi bi-arrow-up text-primary" aria-hidden="true"></i>'
        : '<i class="bi bi-arrow-down text-primary" aria-hidden="true"></i>';
};
$getAriaSort = static function (string $colKey) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $colKey) {
        return 'none';
    }
    return $sortOrder === 'asc' ? 'ascending' : 'descending';
};
$buildQuickFilterUrl = static function (array $override) use ($filters): string {
    return base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, $override, ['page' => 1])));
};
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Prestasi Taruna</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Prestasi Taruna</h1>
            <p class="text-secondary mb-0 small">Kelola data prestasi individu dan tim yang terverifikasi.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if (empty($isSelfOnly)): ?>
                <a href="<?= base_url('ketarunaan/prestasi/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                    <i class="bi bi-download" aria-hidden="true"></i> Ekspor
                </a>
            <?php endif; ?>
            <?php if (!empty($canSubmitPrestasi) || !empty($canManageTaruna)): ?>
                <a href="<?= base_url('ketarunaan/prestasi/tambah') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl shadow-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> <?= !empty($isSelfTaruna) ? 'Ajukan Prestasi' : 'Tambah Prestasi' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 4 Kartu Metrik Dual-Count (Filter Cepat) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl(['status' => $curStatus === 'tervalidasi' ? 'all' : 'tervalidasi'])) ?>" class="ecc-metric-tile <?= $curStatus === 'tervalidasi' ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #ecfdf5; color: #059669;">
                    <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tervalidasi</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format($totalPrestasi, 0, ',', '.') ?> <span class="fw-normal text-secondary small">prestasi</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-metric-tile">
                <div class="ecc-metric-icon" style="background-color: #eff6ff; color: #1e40af;">
                    <i class="bi bi-people-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Taruna Berprestasi</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) ($metrics['taruna_berprestasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">taruna unik</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl(['keikutsertaan' => $curKeikut === 'tim' ? 'all' : 'tim'])) ?>" class="ecc-metric-tile <?= $curKeikut === 'tim' ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #f5f3ff; color: #6d28d9;">
                    <i class="bi bi-diagram-3-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tim</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) ($metrics['prestasi_tim'] ?? 0), 0, ',', '.') ?>
                        <span class="fw-normal text-secondary small">dari <?= number_format($totalPrestasi, 0, ',', '.') ?> prestasi</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl(['status' => $curStatus === 'diajukan' ? 'all' : 'diajukan'])) ?>" class="ecc-metric-tile <?= $curStatus === 'diajukan' ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #fef3c7; color: #b45309;">
                    <i class="bi bi-clock-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;"><?= !empty($isSelfTaruna) ? 'Menunggu Validasi Saya' : 'Menunggu Validasi' ?></div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) ($metrics['menunggu_validasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">prestasi</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Kartu Filter & Tabel Direktori Prestasi -->
    <div class="bento-card ecc-pr-card">
        <form method="get" action="<?= base_url('ketarunaan/prestasi') ?>" class="row g-2 align-items-end mb-3 pb-3 border-bottom pr-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">
            <?php if ($curMilikSaya): ?>
                <input type="hidden" name="milik_saya" value="1">
            <?php endif; ?>
            <div class="col-12 col-md-6 col-xl-4">
                <label for="filterQ" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input type="text" id="filterQ" name="q" value="<?= esc($filters['q']) ?>" class="form-control ecc-control-sm" placeholder="Cari prestasi, tim, atau nama peserta...">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="filterJenis" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis</label>
                <select id="filterJenis" name="jenis" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis</option>
                    <?php foreach (($refs['jenis_catalog'] ?? []) as $jKey => $jLabel): ?>
                        <option value="<?= esc($jKey) ?>" <?= ($filters['jenis'] === $jKey) ? 'selected' : '' ?>><?= esc($jLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-3">
                <label for="filterTingkat" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Tingkat</label>
                <select id="filterTingkat" name="tingkat" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Tingkat</option>
                    <?php foreach (($refs['tingkat_catalog'] ?? []) as $tKey => $tLabel): ?>
                        <option value="<?= esc($tKey) ?>" <?= ($filters['tingkat'] === $tKey) ? 'selected' : '' ?>><?= esc($tLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <label for="filterProdi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Prodi</label>
                <select id="filterProdi" name="prodi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Prodi</option>
                    <?php foreach (($refs['prodi_list'] ?? []) as $pr): ?>
                        <option value="<?= esc($pr['kode_prodi']) ?>" <?= ($filters['prodi'] === $pr['kode_prodi']) ? 'selected' : '' ?>><?= esc($pr['kode_prodi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <label for="filterKeikut" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Keikutsertaan</label>
                <select id="filterKeikut" name="keikutsertaan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <option value="tim" <?= ($curKeikut === 'tim') ? 'selected' : '' ?>>Tim</option>
                    <option value="individu" <?= ($curKeikut === 'individu') ? 'selected' : '' ?>>Individu</option>
                </select>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <label for="filterPeriode" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select id="filterPeriode" name="periode" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= ((string) $filters['periode'] === (string) $ta['id']) ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-6 col-xl-3">
                <label for="filterStatus" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                <select id="filterStatus" name="status" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="tervalidasi" <?= ($curStatus === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                    <option value="diajukan" <?= ($curStatus === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                    <option value="perlu_perbaikan" <?= in_array($curStatus, ['dikembalikan', 'perlu_perbaikan'], true) ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    <option value="draf" <?= ($curStatus === 'draf') ? 'selected' : '' ?>>Draf</option>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-3 d-flex align-items-center justify-content-end gap-2">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary d-inline-flex align-items-center justify-content-center btn-tactile pr-touch-ctrl text-nowrap">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile pr-touch-ctrl text-nowrap <?= $hasActiveFilters ? '' : 'w-100 w-xl-auto' ?>">Terapkan</button>
            </div>
        </form>

        <!-- Baris Info & Filter Cepat Prestasi Saya -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <span class="text-secondary small">Klik baris prestasi untuk membuka rincian.</span>
            <?php if (!empty($isSelfOnly)): ?>
                <a href="<?= esc($buildQuickFilterUrl(['milik_saya' => $curMilikSaya ? '' : '1'])) ?>"
                   class="btn btn-sm rounded-pill px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center gap-1 <?= $curMilikSaya ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="bi bi-person-check" aria-hidden="true"></i> Prestasi Saya
                </a>
            <?php endif; ?>
        </div>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover ecc-table-pr mb-0">
                <thead>
                    <tr>
                        <th aria-sort="<?= esc($getAriaSort('nama_prestasi')) ?>">
                            <a href="<?= esc($buildSortUrl('nama_prestasi')) ?>" class="pr-sort-link <?= ($sortBy === 'nama_prestasi') ? 'is-sorted' : '' ?>">
                                <span>Nama Prestasi</span> <?= $getSortIcon('nama_prestasi') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('tingkat_prestasi')) ?>">
                            <a href="<?= esc($buildSortUrl('tingkat_prestasi')) ?>" class="pr-sort-link <?= ($sortBy === 'tingkat_prestasi') ? 'is-sorted' : '' ?>">
                                <span>Tingkat</span> <?= $getSortIcon('tingkat_prestasi') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('peringkat')) ?>">
                            <a href="<?= esc($buildSortUrl('peringkat')) ?>" class="pr-sort-link <?= ($sortBy === 'peringkat') ? 'is-sorted' : '' ?>">
                                <span>Peringkat</span> <?= $getSortIcon('peringkat') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('jenis_keikutsertaan')) ?>">
                            <a href="<?= esc($buildSortUrl('jenis_keikutsertaan')) ?>" class="pr-sort-link <?= ($sortBy === 'jenis_keikutsertaan') ? 'is-sorted' : '' ?>">
                                <span>Keikutsertaan</span> <?= $getSortIcon('jenis_keikutsertaan') ?>
                            </a>
                        </th>
                        <th class="text-center" aria-sort="<?= esc($getAriaSort('jumlah_peserta')) ?>">
                            <a href="<?= esc($buildSortUrl('jumlah_peserta')) ?>" class="pr-sort-link justify-content-center <?= ($sortBy === 'jumlah_peserta') ? 'is-sorted' : '' ?>">
                                <span>Peserta</span> <?= $getSortIcon('jumlah_peserta') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('tanggal_mulai')) ?>">
                            <a href="<?= esc($buildSortUrl('tanggal_mulai')) ?>" class="pr-sort-link <?= ($sortBy === 'tanggal_mulai') ? 'is-sorted' : '' ?>">
                                <span>Tanggal</span> <?= $getSortIcon('tanggal_mulai') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('status_validasi')) ?>">
                            <a href="<?= esc($buildSortUrl('status_validasi')) ?>" class="pr-sort-link <?= ($sortBy === 'status_validasi') ? 'is-sorted' : '' ?>">
                                <span>Validasi</span> <?= $getSortIcon('status_validasi') ?>
                            </a>
                        </th>
                        <?php if ($showAksiCol): ?>
                            <th class="text-end">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $showAksiCol ? 8 : 7 ?>" class="text-center py-4 text-secondary">Tidak ada data prestasi yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $rawStatus    = (string) ($row['status_validasi'] ?? 'draf');
                            $tingkatLabel = ($refs['tingkat_catalog'][$row['tingkat_prestasi']] ?? ucfirst((string) $row['tingkat_prestasi']));
                            $detailUrl    = base_url('ketarunaan/prestasi/detail/' . (int) $row['id']);
                            $canEditRow   = !empty($canManageTaruna)
                                || (!empty($isSelfTaruna) && !empty($row['is_peserta_saya']) && in_array($rawStatus, ['draf', 'perlu_perbaikan'], true));
                            $editLabel    = ($rawStatus === 'tervalidasi') ? 'Ajukan koreksi prestasi' : 'Edit prestasi';
                            ?>
                            <tr class="pr-row-clickable" data-href="<?= esc($detailUrl) ?>" tabindex="0" role="link" aria-label="Buka detail prestasi <?= esc($row['nama_prestasi']) ?>">
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="fw-semibold text-dark"><?= esc($row['nama_prestasi']) ?></span>
                                        <?php if (!empty($isSelfOnly) && !empty($row['is_peserta_saya'])): ?>
                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Prestasi Saya</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($row['nama_tim']) && $row['jenis_keikutsertaan'] === 'tim'): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($row['nama_tim']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap"><?= esc($tingkatLabel) ?></td>
                                <td class="fw-medium text-nowrap"><?= esc($row['peringkat']) ?></td>
                                <td class="text-nowrap"><?= ketarunaan_keikutsertaan_badge((string) $row['jenis_keikutsertaan']) ?></td>
                                <td class="text-center ecc-num text-nowrap"><?= (int) $row['jumlah_peserta'] ?> taruna</td>
                                <td class="ecc-num text-secondary text-nowrap"><?= esc(ketarunaan_format_tanggal($row['tanggal_mulai'], true)) ?></td>
                                <td class="text-nowrap"><?= ketarunaan_validasi_badge($rawStatus) ?></td>
                                <?php if ($showAksiCol): ?>
                                    <td class="text-end text-nowrap">
                                        <?php if ($canEditRow): ?>
                                            <a href="<?= base_url('ketarunaan/prestasi/edit/' . (int) $row['id']) ?>" class="btn btn-light border text-secondary pr-icon-btn btn-tactile" title="<?= esc($editLabel) ?>" aria-label="<?= esc($editLabel . ' ' . $row['nama_prestasi']) ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-secondary small">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center py-4 text-secondary small">Tidak ada data prestasi yang sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $rawStatus        = (string) ($row['status_validasi'] ?? 'draf');
                    $tingkatLabel     = ($refs['tingkat_catalog'][$row['tingkat_prestasi']] ?? ucfirst((string) $row['tingkat_prestasi']));
                    $detailUrl        = base_url('ketarunaan/prestasi/detail/' . (int) $row['id']);
                    $canEditMobileRow = !empty($canManageTaruna)
                        || (!empty($isSelfTaruna) && !empty($row['is_peserta_saya']) && in_array($rawStatus, ['draf', 'perlu_perbaikan'], true));
                    $editMobileLabel  = ($rawStatus === 'tervalidasi') ? 'Koreksi' : 'Edit';
                    ?>
                    <div class="border rounded-3 p-3 bg-white pr-row-clickable pr-mobile-card" data-href="<?= esc($detailUrl) ?>" tabindex="0" role="link" aria-label="Buka detail prestasi <?= esc($row['nama_prestasi']) ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-bold text-dark"><?= esc($row['nama_prestasi']) ?></span>
                                    <?php if (!empty($isSelfOnly) && !empty($row['is_peserta_saya'])): ?>
                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Prestasi Saya</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc($tingkatLabel) ?> &bull; <?= esc($row['peringkat']) ?> &bull; <?= esc(ketarunaan_format_tanggal($row['tanggal_mulai'], true)) ?>
                                </div>
                            </div>
                            <?= ketarunaan_validasi_badge($rawStatus) ?>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top mt-2">
                            <div class="d-flex align-items-center gap-2">
                                <?= ketarunaan_keikutsertaan_badge((string) $row['jenis_keikutsertaan']) ?>
                                <span class="text-secondary ecc-num small"><?= (int) $row['jumlah_peserta'] ?> taruna</span>
                            </div>
                            <?php if ($canEditMobileRow): ?>
                                <a href="<?= base_url('ketarunaan/prestasi/edit/' . (int) $row['id']) ?>" class="btn btn-light border btn-sm rounded-3 px-3 text-secondary fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-pencil" aria-hidden="true"></i> <?= esc($editMobileLabel) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Paginasi & Catatan Bawah -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
            <div class="text-secondary ecc-num small">
                Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> prestasi
            </div>
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                <?php
                $curPage  = (int) $pagination['page'];
                $totPages = (int) $pagination['total_pages'];
                $startPg  = max(1, $curPage - 2);
                $endPg    = min($totPages, $curPage + 2);
                ?>
                <nav aria-label="Paginasi Daftar Prestasi">
                    <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap">
                        <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                            <?php if ($curPage <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pr-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pr-touch-ctrl" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $curPage - 1]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($pg = $startPg; $pg <= $endPg; $pg++): ?>
                            <li class="page-item <?= ($pg === $curPage) ? 'active' : '' ?>">
                                <a class="page-link rounded-3 ecc-num d-inline-flex align-items-center justify-content-center pr-touch-ctrl" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $pg]))) ?>"<?= ($pg === $curPage) ? ' aria-current="page"' : '' ?>><?= $pg ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                            <?php if ($curPage >= $totPages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pr-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pr-touch-ctrl" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $curPage + 1]))) ?>">Berikutnya</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
            <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
            <span>Jumlah prestasi dihitung per penghargaan, sedangkan jumlah taruna dihitung unik.</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pr-row-clickable').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button, input, select, label')) {
                return;
            }
            var href = row.getAttribute('data-href');
            if (!href) return;
            if (e.ctrlKey || e.metaKey) {
                window.open(href, '_blank', 'noopener');
            } else {
                window.location.href = href;
            }
        });
        row.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.closest('a, button, input, select, label')) {
                    return;
                }
                e.preventDefault();
                var href = row.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
