<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pelanggaran Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters     = $filters ?? [];
$metrics     = $metrics ?? ['terbukti' => 0, 'dugaan' => 0, 'terbukti_selesai' => 0, 'terbukti_proses' => 0];
$rows        = $rows ?? [];
$pagination  = $pagination ?? ['page' => 1, 'per_page' => 10, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$refs        = $refs ?? [];
$canViewFull = !empty($canViewFull);
$canManage   = !empty($canManage);

$curQ            = (string) ($filters['q'] ?? '');
$curProdi        = (string) ($filters['prodi_id'] ?? '');
$curTahun        = (string) ($filters['tahun'] ?? 'all');
$curPenetapan    = (string) ($filters['status_penetapan'] ?? 'all');
$curPenyelesaian = (string) ($filters['status_penyelesaian'] ?? 'all');
$curValidasi     = (string) ($filters['status_validasi'] ?? 'all');
$sortBy          = (string) ($filters['sort_by'] ?? '');
$sortOrder       = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$baseFilterParams = [
    'q'                   => $curQ,
    'prodi_id'            => $curProdi,
    'tahun'               => $curTahun,
    'status_penetapan'    => $curPenetapan,
    'status_penyelesaian' => $curPenyelesaian,
    'status_validasi'     => $curValidasi,
    'sort_by'             => $sortBy,
    'sort_order'          => $sortOrder,
];
$exportQuery = http_build_query($baseFilterParams);

$hasActiveFilters = (
    $curQ !== ''
    || $curProdi !== ''
    || $curTahun !== 'all'
    || $curPenetapan !== 'all'
    || $curPenyelesaian !== 'all'
    || $curValidasi !== 'all'
);

$isTileTerbukti        = ($curPenetapan === 'terbukti' && $curValidasi === 'tervalidasi' && $curPenyelesaian === 'all');
$isTileDugaan          = ($curPenetapan === 'dugaan' && $curValidasi === 'all' && $curPenyelesaian === 'all');
$isTileTerbuktiSelesai = ($curPenetapan === 'terbukti' && $curValidasi === 'tervalidasi' && $curPenyelesaian === 'selesai');
$isTileTerbuktiProses  = ($curPenetapan === 'terbukti' && $curValidasi === 'tervalidasi' && $curPenyelesaian === 'proses');

$buildQuickFilterUrl = static function (array $override) use ($baseFilterParams): string {
    return base_url('ketarunaan/pelanggaran?' . http_build_query(array_merge($baseFilterParams, $override, ['page' => 1])));
};
$buildSortUrl = static function (string $colKey) use ($baseFilterParams, $sortBy, $sortOrder): string {
    $nextOrder = ($sortBy === $colKey && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/pelanggaran?' . http_build_query(array_merge($baseFilterParams, [
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
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Pelanggaran Taruna</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Pelanggaran Taruna</h1>
            <p class="text-secondary mb-0 small">Pencatatan dugaan, pemeriksaan, penetapan, dan pemantauan tindak lanjut kasus.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if ($canViewFull): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                    <i class="bi bi-download" aria-hidden="true"></i> Ekspor
                </a>
            <?php endif; ?>
            <?php if ($canManage): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/tambah') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl shadow-sm">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Catat Kasus
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

    <!-- 4 Kartu Metrik Utama (Filter Cepat) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl($isTileTerbukti
                ? ['status_penetapan' => 'all', 'status_validasi' => 'all', 'status_penyelesaian' => 'all']
                : ['status_penetapan' => 'terbukti', 'status_validasi' => 'tervalidasi', 'status_penyelesaian' => 'all'])) ?>" class="ecc-metric-tile <?= $isTileTerbukti ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #fef2f2; color: #dc2626;">
                    <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Pelanggaran Terbukti</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) $metrics['terbukti'], 0, ',', '.') ?>
                        <span class="fw-normal text-secondary small">kasus</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl($isTileDugaan
                ? ['status_penetapan' => 'all', 'status_validasi' => 'all', 'status_penyelesaian' => 'all']
                : ['status_penetapan' => 'dugaan', 'status_validasi' => 'all', 'status_penyelesaian' => 'all'])) ?>" class="ecc-metric-tile <?= $isTileDugaan ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #fef3c7; color: #b45309;">
                    <i class="bi bi-search" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Dugaan Dalam Pemeriksaan</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) $metrics['dugaan'], 0, ',', '.') ?>
                        <span class="fw-normal text-secondary small">kasus</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl($isTileTerbuktiSelesai
                ? ['status_penetapan' => 'all', 'status_validasi' => 'all', 'status_penyelesaian' => 'all']
                : ['status_penetapan' => 'terbukti', 'status_validasi' => 'tervalidasi', 'status_penyelesaian' => 'selesai'])) ?>" class="ecc-metric-tile <?= $isTileTerbuktiSelesai ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #ecfdf5; color: #059669;">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Kasus Terbukti Selesai</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) $metrics['terbukti_selesai'], 0, ',', '.') ?>
                        <span class="fw-normal text-secondary small">kasus</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= esc($buildQuickFilterUrl($isTileTerbuktiProses
                ? ['status_penetapan' => 'all', 'status_validasi' => 'all', 'status_penyelesaian' => 'all']
                : ['status_penetapan' => 'terbukti', 'status_validasi' => 'tervalidasi', 'status_penyelesaian' => 'proses'])) ?>" class="ecc-metric-tile <?= $isTileTerbuktiProses ? 'is-active' : '' ?>">
                <div class="ecc-metric-icon" style="background-color: #eff6ff; color: #1e40af;">
                    <i class="bi bi-clock-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Terbukti Dalam Proses</div>
                    <div class="h4 fw-bold text-dark mb-0 ecc-num">
                        <?= number_format((int) $metrics['terbukti_proses'], 0, ',', '.') ?>
                        <span class="fw-normal text-secondary small">kasus</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Panel Filter & Tabel Kasus -->
    <div class="bento-card ecc-pl-card">
        <form method="get" action="<?= base_url('ketarunaan/pelanggaran') ?>" class="row g-2 align-items-end mb-3 pb-3 border-bottom pl-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">

            <div class="col-12 col-md-6 col-xl-4">
                <label for="filterQ" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input
                    type="text"
                    id="filterQ"
                    name="q"
                    class="form-control ecc-control-sm"
                    value="<?= esc($curQ) ?>"
                    placeholder="<?= $canViewFull ? 'Cari nomor kasus, Nomor Taruna, atau nama...' : 'Cari nomor kasus atau Nomor Taruna...' ?>"
                >
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="filterProdi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Prodi</label>
                <select id="filterProdi" name="prodi_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    <?php foreach ($refs['prodi_list'] ?? [] as $pr): ?>
                        <option value="<?= (int) $pr['id'] ?>" <?= ($curProdi === (string) $pr['id']) ? 'selected' : '' ?>>
                            <?= esc((string) $pr['kode_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="filterTahun" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode</label>
                <select id="filterTahun" name="tahun" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Tahun</option>
                    <?php foreach ($refs['tahun_list'] ?? [] as $thn): ?>
                        <option value="<?= (int) $thn ?>" <?= ($curTahun === (string) $thn) ? 'selected' : '' ?>>
                            Tahun <?= (int) $thn ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <label for="filterPenetapan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Penetapan</label>
                <select id="filterPenetapan" name="status_penetapan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <?php foreach ($refs['penetapan_options'] ?? [] as $k => $lbl): ?>
                        <option value="<?= esc($k) ?>" <?= ($curPenetapan === $k) ? 'selected' : '' ?>>
                            <?= esc($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <label for="filterPenyelesaian" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Penyelesaian</label>
                <select id="filterPenyelesaian" name="status_penyelesaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <?php foreach ($refs['penyelesaian_options'] ?? [] as $k => $lbl): ?>
                        <option value="<?= esc($k) ?>" <?= ($curPenyelesaian === $k) ? 'selected' : '' ?>>
                            <?= esc($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <label for="filterValidasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                <select id="filterValidasi" name="status_validasi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <?php foreach ($refs['validasi_options'] ?? [] as $k => $lbl): ?>
                        <option value="<?= esc($k) ?>" <?= ($curValidasi === $k) ? 'selected' : '' ?>>
                            <?= esc($lbl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-xl-9 d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 pt-xl-0">
                <span class="text-secondary small">Klik baris kasus untuk membuka rincian penanganan.</span>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <?php if ($hasActiveFilters): ?>
                        <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary d-inline-flex align-items-center justify-content-center btn-tactile pl-touch-ctrl text-nowrap">Reset</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile pl-touch-ctrl text-nowrap">Terapkan</button>
                </div>
            </div>
        </form>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover ecc-table-pl mb-0">
                <thead>
                    <tr>
                        <th aria-sort="<?= esc($getAriaSort('nomor_perkara')) ?>">
                            <a href="<?= esc($buildSortUrl('nomor_perkara')) ?>" class="pl-sort-link <?= ($sortBy === 'nomor_perkara') ? 'is-sorted' : '' ?>">
                                <span>Nomor Kasus</span> <?= $getSortIcon('nomor_perkara') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('taruna')) ?>">
                            <a href="<?= esc($buildSortUrl('taruna')) ?>" class="pl-sort-link <?= ($sortBy === 'taruna') ? 'is-sorted' : '' ?>">
                                <span>Taruna</span> <?= $getSortIcon('taruna') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('jenis_pelanggaran')) ?>">
                            <a href="<?= esc($buildSortUrl('jenis_pelanggaran')) ?>" class="pl-sort-link <?= ($sortBy === 'jenis_pelanggaran') ? 'is-sorted' : '' ?>">
                                <span>Kategori</span> <?= $getSortIcon('jenis_pelanggaran') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('tanggal_kejadian')) ?>">
                            <a href="<?= esc($buildSortUrl('tanggal_kejadian')) ?>" class="pl-sort-link <?= ($sortBy === 'tanggal_kejadian') ? 'is-sorted' : '' ?>">
                                <span>Tanggal</span> <?= $getSortIcon('tanggal_kejadian') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('status_penetapan')) ?>">
                            <a href="<?= esc($buildSortUrl('status_penetapan')) ?>" class="pl-sort-link <?= ($sortBy === 'status_penetapan') ? 'is-sorted' : '' ?>">
                                <span>Penetapan</span> <?= $getSortIcon('status_penetapan') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('status_penyelesaian')) ?>">
                            <a href="<?= esc($buildSortUrl('status_penyelesaian')) ?>" class="pl-sort-link <?= ($sortBy === 'status_penyelesaian') ? 'is-sorted' : '' ?>">
                                <span>Penyelesaian</span> <?= $getSortIcon('status_penyelesaian') ?>
                            </a>
                        </th>
                        <th aria-sort="<?= esc($getAriaSort('status_validasi')) ?>">
                            <a href="<?= esc($buildSortUrl('status_validasi')) ?>" class="pl-sort-link <?= ($sortBy === 'status_validasi') ? 'is-sorted' : '' ?>">
                                <span>Validasi</span> <?= $getSortIcon('status_validasi') ?>
                            </a>
                        </th>
                        <?php if ($canManage): ?>
                            <th class="text-end">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $canManage ? 8 : 7 ?>" class="text-center text-secondary py-4">Tidak ada kasus yang sesuai dengan filter saat ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $rowId     = (int) $r['id'];
                            $rawValid  = (string) ($r['status_validasi'] ?? 'draf');
                            $stVal     = ($rawValid === 'perlu_perbaikan') ? 'dikembalikan' : $rawValid;
                            $tgl       = ketarunaan_format_tanggal(substr((string) ($r['tanggal_kejadian'] ?? ''), 0, 10), true);
                            $detailUrl = base_url('ketarunaan/pelanggaran/detail/' . $rowId);
                            $editLabel = ($rawValid === 'tervalidasi') ? 'Ajukan koreksi kasus' : 'Kelola kasus';
                            ?>
                            <tr class="pl-row-clickable" data-href="<?= esc($detailUrl) ?>" tabindex="0" role="link" aria-label="Buka detail kasus <?= esc((string) $r['nomor_perkara']) ?>">
                                <td class="fw-semibold text-dark ecc-num text-nowrap"><?= esc((string) $r['nomor_perkara']) ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) $r['display_nama']) ?></div>
                                    <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                        <?= esc((string) $r['display_nomor_taruna']) ?>
                                        <?php if (!empty($r['kode_prodi'])): ?>
                                            &bull; <?= esc((string) $r['kode_prodi']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= esc((string) ($r['jenis_pelanggaran'] ?: '-')) ?></td>
                                <td class="ecc-num text-secondary text-nowrap"><?= esc($tgl) ?></td>
                                <td class="text-nowrap"><?= ketarunaan_penetapan_badge((string) $r['status_penetapan']) ?></td>
                                <td class="text-nowrap"><?= ketarunaan_penyelesaian_badge((string) $r['status_penyelesaian']) ?></td>
                                <td class="text-nowrap"><?= ketarunaan_validasi_badge($stVal) ?></td>
                                <?php if ($canManage): ?>
                                    <td class="text-end text-nowrap">
                                        <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $rowId) ?>" class="btn btn-light border text-secondary pl-icon-btn btn-tactile" title="<?= esc($editLabel) ?>" aria-label="<?= esc($editLabel . ' ' . $r['nomor_perkara']) ?>">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile -->
        <div class="d-lg-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center text-secondary py-4 small">Tidak ada kasus yang sesuai dengan filter saat ini.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $rowId     = (int) $r['id'];
                    $rawValid  = (string) ($r['status_validasi'] ?? 'draf');
                    $stVal     = ($rawValid === 'perlu_perbaikan') ? 'dikembalikan' : $rawValid;
                    $tgl       = ketarunaan_format_tanggal(substr((string) ($r['tanggal_kejadian'] ?? ''), 0, 10), true);
                    $detailUrl = base_url('ketarunaan/pelanggaran/detail/' . $rowId);
                    $editText  = ($rawValid === 'tervalidasi') ? 'Koreksi' : 'Edit';
                    ?>
                    <div class="border rounded-3 p-3 bg-white pl-row-clickable pl-mobile-card" data-href="<?= esc($detailUrl) ?>" tabindex="0" role="link" aria-label="Buka detail kasus <?= esc((string) $r['nomor_perkara']) ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark ecc-num"><?= esc((string) $r['nomor_perkara']) ?></div>
                                <div class="fw-semibold text-dark small mt-1"><?= esc((string) $r['display_nama']) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc((string) $r['display_nomor_taruna']) ?> &bull; <?= esc((string) ($r['jenis_pelanggaran'] ?: '-')) ?> &bull; <?= esc($tgl) ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?= ketarunaan_penetapan_badge((string) $r['status_penetapan']) ?>
                            <?= ketarunaan_penyelesaian_badge((string) $r['status_penyelesaian']) ?>
                            <?= ketarunaan_validasi_badge($stVal) ?>
                        </div>
                        <?php if ($canManage): ?>
                            <div class="d-flex justify-content-end pt-2 border-top mt-2">
                                <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $rowId) ?>" class="btn btn-light border btn-sm rounded-3 px-3 text-secondary fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-pencil" aria-hidden="true"></i> <?= esc($editText) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Ringkasan & Paginasi -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
            <div class="text-secondary ecc-num small">
                Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> kasus
            </div>

            <?php if ((int) $pagination['total_pages'] > 1): ?>
                <?php
                $curPage    = (int) $pagination['page'];
                $totalPages = (int) $pagination['total_pages'];
                $startPg    = max(1, $curPage - 2);
                $endPg      = min($totalPages, $curPage + 2);
                ?>
                <nav aria-label="Paginasi Daftar Kasus Pelanggaran">
                    <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap ecc-num">
                        <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                            <?php if ($curPage <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pl-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pl-touch-ctrl" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query(array_merge($baseFilterParams, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($p = $startPg; $p <= $endPg; $p++): ?>
                            <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pl-touch-ctrl" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query(array_merge($baseFilterParams, ['page' => $p]))) ?>"<?= $p === $curPage ? ' aria-current="page"' : '' ?>><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                            <?php if ($curPage >= $totalPages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pl-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center pl-touch-ctrl" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query(array_merge($baseFilterParams, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
            <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
            <span>Hanya kasus terbukti dan tervalidasi dihitung sebagai pelanggaran sah.</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.pl-row-clickable').forEach(function (row) {
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
