<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Rekam Jejak Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/rekam_jejak/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$metrics    = $metrics ?? [];
$rows       = $rows ?? [];
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$filters    = $filters ?? ['q' => '', 'prodi' => 'all', 'angkatan' => 'all', 'status' => 'all', 'periode' => 'all', 'sort_by' => 'jumlah_capaian', 'sort_order' => 'desc'];
$refs       = $refs ?? [];

$sortBy    = (string) ($filters['sort_by'] ?? 'jumlah_capaian');
$sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$exportQuery = http_build_query($filters);
$hasActiveFilters = ($filters['q'] !== '' || $filters['prodi'] !== 'all' || $filters['angkatan'] !== 'all' || $filters['status'] !== 'all' || $filters['periode'] !== 'all');

$buildSortUrl = static function (string $colKey) use ($filters, $sortBy, $sortOrder): string {
    $nextOrder = ($sortBy === $colKey && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, [
        'sort_by'    => $colKey,
        'sort_order' => $nextOrder,
        'page'       => 1,
    ])));
};
$getSortIcon = static function (string $colKey) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $colKey) {
        return '<i class="bi bi-arrow-down-up text-muted opacity-50 ms-1" aria-hidden="true"></i>';
    }
    return $sortOrder === 'asc'
        ? '<i class="bi bi-arrow-up text-primary ms-1" aria-hidden="true"></i>'
        : '<i class="bi bi-arrow-down text-primary ms-1" aria-hidden="true"></i>';
};
$getAriaSort = static function (string $colKey) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $colKey) {
        return 'none';
    }
    return $sortOrder === 'asc' ? 'ascending' : 'descending';
};
?>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Rekam Jejak Taruna</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Rekam Jejak Taruna</h1>
        <p class="text-secondary mb-0" style="font-size: 0.875rem;">Ringkasan perjalanan pendidikan, capaian, dan riwayat status taruna.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('ketarunaan/rekam-jejak/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
            <i class="bi bi-download" aria-hidden="true"></i> Ekspor
        </a>
        <?php if (!empty($canManageTaruna)): ?>
            <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Capaian
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- 4 Kartu Metrik Ringkasan (Keseluruhan) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #eff6ff; color: #0284c7;">
                <i class="bi bi-people-fill" aria-hidden="true"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Taruna Tercatat</div>
                <div class="h4 fw-bold text-dark mb-0 ecc-num">
                    <?= number_format((int) ($metrics['taruna_tercatat'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">taruna</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #ecfdf5; color: #059669;">
                <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Capaian Tervalidasi</div>
                <div class="h4 fw-bold text-dark mb-0 ecc-num">
                    <?= number_format((int) ($metrics['capaian_tervalidasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">capaian</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #f5f3ff; color: #7c3aed;">
                <i class="bi bi-trophy-fill" aria-hidden="true"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tervalidasi</div>
                <div class="h4 fw-bold text-dark mb-0 ecc-num">
                    <?= number_format((int) ($metrics['prestasi_tervalidasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">prestasi</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #fef3c7; color: #d97706;">
                <i class="bi bi-hourglass-split" aria-hidden="true"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Menunggu Validasi</div>
                <div class="h4 fw-bold text-dark mb-0 ecc-num">
                    <?= number_format((int) ($metrics['menunggu_validasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary small">capaian</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kartu Utama Filter & Tabel Direktori -->
<div class="bento-card ecc-rj-card">
    <form method="get" action="<?= base_url('ketarunaan/rekam-jejak') ?>" class="row g-2 align-items-end mb-3 pb-3 border-bottom rj-filter-form">
        <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
        <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">
        <div class="col-12 col-md-6 col-xl-3">
            <label for="filterQ" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Cari Taruna</label>
            <input type="text" id="filterQ" name="q" value="<?= esc($filters['q']) ?>" class="form-control ecc-control-sm" placeholder="Nomor Taruna atau nama...">
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <label for="filterProdi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Program Studi</label>
            <select id="filterProdi" name="prodi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                <option value="all">Semua Prodi</option>
                <?php foreach (($refs['prodi_list'] ?? []) as $pr): ?>
                    <option value="<?= esc($pr['kode_prodi']) ?>" <?= ($filters['prodi'] === $pr['kode_prodi']) ? 'selected' : '' ?>>
                        <?= esc($pr['kode_prodi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <label for="filterAngkatan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Angkatan</label>
            <select id="filterAngkatan" name="angkatan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                <option value="all">Semua Angkatan</option>
                <?php foreach (($refs['angkatan_list'] ?? []) as $ang): ?>
                    <option value="<?= esc((string) $ang['tahun_masuk']) ?>" <?= ((string) $filters['angkatan'] === (string) $ang['tahun_masuk']) ? 'selected' : '' ?>>
                        <?= esc(ketarunaan_format_angkatan($ang)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label for="filterStatus" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Pendidikan</label>
            <select id="filterStatus" name="status" class="form-select ecc-control-sm" onchange="this.form.submit()">
                <option value="all">Semua Status</option>
                <?php foreach (($refs['status_catalog'] ?? []) as $stKey => $stMeta): ?>
                    <option value="<?= esc($stKey) ?>" <?= ($filters['status'] === $stKey) ? 'selected' : '' ?>>
                        <?= esc($stMeta['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <label for="filterPeriode" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik (Capaian)</label>
            <select id="filterPeriode" name="periode" class="form-select ecc-control-sm" onchange="this.form.submit()">
                <option value="all">Semua Periode</option>
                <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                    <option value="<?= (int) $ta['id'] ?>" <?= ((string) $filters['periode'] === (string) $ta['id']) ? 'selected' : '' ?>>
                        <?= esc($ta['kode_tahun']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2">
            <div class="text-secondary small">
                Klik baris taruna untuk membuka rekam jejak lengkap.
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary d-inline-flex align-items-center justify-content-center btn-tactile rj-touch-ctrl text-nowrap">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile rj-touch-ctrl text-nowrap">Terapkan</button>
            </div>
        </div>
    </form>

    <!-- Tabel Desktop (md ke atas) -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover ecc-table-rj mb-0">
            <thead>
                <tr>
                    <th aria-sort="<?= esc($getAriaSort('nomor_taruna')) ?>">
                        <a href="<?= esc($buildSortUrl('nomor_taruna')) ?>" class="rj-sort-link <?= ($sortBy === 'nomor_taruna') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Nomor Taruna">
                            Nomor Taruna <?= $getSortIcon('nomor_taruna') ?>
                        </a>
                    </th>
                    <th aria-sort="<?= esc($getAriaSort('nama_lengkap')) ?>">
                        <a href="<?= esc($buildSortUrl('nama_lengkap')) ?>" class="rj-sort-link <?= ($sortBy === 'nama_lengkap') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Nama Taruna">
                            Nama Taruna <?= $getSortIcon('nama_lengkap') ?>
                        </a>
                    </th>
                    <th aria-sort="<?= esc($getAriaSort('prodi_angkatan')) ?>">
                        <a href="<?= esc($buildSortUrl('prodi_angkatan')) ?>" class="rj-sort-link <?= ($sortBy === 'prodi_angkatan') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Prodi dan Angkatan">
                            Prodi / Angkatan <?= $getSortIcon('prodi_angkatan') ?>
                        </a>
                    </th>
                    <th aria-sort="<?= esc($getAriaSort('status_terkini')) ?>">
                        <a href="<?= esc($buildSortUrl('status_terkini')) ?>" class="rj-sort-link <?= ($sortBy === 'status_terkini') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Status">
                            Status <?= $getSortIcon('status_terkini') ?>
                        </a>
                    </th>
                    <th class="text-center" aria-sort="<?= esc($getAriaSort('jumlah_capaian')) ?>">
                        <a href="<?= esc($buildSortUrl('jumlah_capaian')) ?>" class="rj-sort-link justify-content-center <?= ($sortBy === 'jumlah_capaian') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Jumlah Capaian">
                            Capaian <?= $getSortIcon('jumlah_capaian') ?>
                        </a>
                    </th>
                    <th class="text-center" aria-sort="<?= esc($getAriaSort('jumlah_prestasi')) ?>">
                        <a href="<?= esc($buildSortUrl('jumlah_prestasi')) ?>" class="rj-sort-link justify-content-center <?= ($sortBy === 'jumlah_prestasi') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Jumlah Prestasi">
                            Prestasi <?= $getSortIcon('jumlah_prestasi') ?>
                        </a>
                    </th>
                    <th aria-sort="<?= esc($getAriaSort('pembaruan')) ?>">
                        <a href="<?= esc($buildSortUrl('pembaruan')) ?>" class="rj-sort-link <?= ($sortBy === 'pembaruan') ? 'is-sorted' : '' ?>" title="Urutkan berdasarkan Tanggal Pembaruan">
                            Pembaruan <?= $getSortIcon('pembaruan') ?>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-secondary">Tidak ada data rekam jejak taruna yang sesuai filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $detailUrl = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $row['nomor_taruna']));
                        $angkatanText = !empty($row['angkatan'])
                            ? ketarunaan_format_angkatan([
                                'tahun_masuk' => $row['angkatan'],
                                'kode_romawi' => $row['angkatan_romawi'] ?? '',
                            ])
                            : '-';
                        $initials = strtoupper(mb_substr(trim((string) ($row['nama_lengkap'] ?? '-')), 0, 1));
                        $fotoFile = basename((string) ($row['foto'] ?? ''));
                        $fotoThumbUrl = ($fotoFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $fotoFile))
                            ? base_url('assets/uploads/taruna/' . $fotoFile)
                            : null;
                        ?>
                        <tr class="rj-row-clickable" data-href="<?= esc($detailUrl) ?>" tabindex="0" role="link" aria-label="Buka rekam jejak <?= esc($row['nama_lengkap']) ?>">
                            <td class="fw-bold ecc-num text-nowrap" style="color: #1e40af;"><?= esc($row['nomor_taruna']) ?></td>
                            <td class="fw-semibold text-dark">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="ecc-avatar-sm">
                                        <?php if ($fotoThumbUrl !== null): ?>
                                            <img src="<?= esc($fotoThumbUrl) ?>" alt="Foto <?= esc($row['nama_lengkap']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= esc($initials) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span><?= esc($row['nama_lengkap']) ?></span>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-semibold text-dark"><?= esc($row['kode_prodi'] ?? '-') ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;"><?= esc($angkatanText) ?></div>
                            </td>
                            <td class="text-nowrap"><?= ketarunaan_status_badge((string) $row['status_terkini']) ?></td>
                            <td class="text-center fw-bold ecc-num"><?= (int) $row['jumlah_capaian'] ?></td>
                            <td class="text-center fw-bold ecc-num"><?= (int) $row['jumlah_prestasi'] ?></td>
                            <td class="text-secondary ecc-num text-nowrap"><?= esc(ketarunaan_format_tanggal($row['tanggal_pembaruan'], true)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Kartu Mobile -->
    <div class="d-md-none d-flex flex-column gap-3">
        <?php if (empty($rows)): ?>
            <div class="text-center py-4 text-secondary small">Tidak ada data rekam jejak taruna yang sesuai filter.</div>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <?php
                $detailUrl = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $row['nomor_taruna']));
                $angkatanText = !empty($row['angkatan'])
                    ? ketarunaan_format_angkatan([
                        'tahun_masuk' => $row['angkatan'],
                        'kode_romawi' => $row['angkatan_romawi'] ?? '',
                    ])
                    : '-';
                ?>
                <a href="<?= esc($detailUrl) ?>" class="d-block text-decoration-none border rounded-3 p-3 bg-white rj-mobile-card">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark"><?= esc($row['nama_lengkap']) ?></div>
                            <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                <?= esc($row['nomor_taruna']) ?> &bull; <?= esc($row['kode_prodi'] ?? '-') ?> &bull; <?= esc($angkatanText) ?>
                            </div>
                        </div>
                        <?= ketarunaan_status_badge((string) $row['status_terkini']) ?>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top mt-2 small">
                        <div class="text-secondary ecc-num">
                            Capaian: <strong class="text-dark"><?= (int) $row['jumlah_capaian'] ?></strong> &bull;
                            Prestasi: <strong class="text-dark"><?= (int) $row['jumlah_prestasi'] ?></strong>
                        </div>
                        <span class="text-secondary ecc-num" style="font-size: 0.75rem;">
                            <?= esc(ketarunaan_format_tanggal($row['tanggal_pembaruan'], true)) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginasi & Catatan Bawah -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
        <div class="text-secondary ecc-num small">
            Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> taruna
        </div>
        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <?php
            $curPage  = (int) $pagination['page'];
            $totPages = (int) $pagination['total_pages'];
            $startPg  = max(1, $curPage - 2);
            $endPg    = min($totPages, $curPage + 2);
            ?>
            <nav aria-label="Paginasi Rekam Jejak">
                <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap">
                    <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                        <?php if ($curPage <= 1): ?>
                            <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center rj-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                        <?php else: ?>
                            <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center rj-touch-ctrl" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                        <?php endif; ?>
                    </li>
                    <?php for ($pg = $startPg; $pg <= $endPg; $pg++): ?>
                        <li class="page-item <?= ($pg === $curPage) ? 'active' : '' ?>">
                            <a class="page-link rounded-3 ecc-num d-inline-flex align-items-center justify-content-center rj-touch-ctrl" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $pg]))) ?>"<?= ($pg === $curPage) ? ' aria-current="page"' : '' ?>><?= $pg ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                        <?php if ($curPage >= $totPages): ?>
                            <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center rj-touch-ctrl" aria-disabled="true">Berikutnya</span>
                        <?php else: ?>
                            <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center rj-touch-ctrl" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
        <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
        <span>Ringkasan 4 kartu di atas menampilkan angka keseluruhan. Rekam jejak tetap tersimpan setelah perubahan status pendidikan.</span>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.rj-row-clickable').forEach(function (row) {
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
