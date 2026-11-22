<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Lowongan Pekerjaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$kpi               = $kpi ?? ['terbuka' => 0, 'menunggu' => 0, 'ditutup' => 0, 'segera_berakhir' => 0, 'perusahaan' => 0];
$rows              = $rows ?? [];
$refs              = $refs ?? [];
$filters           = $filters ?? [];
$pagination        = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_rows' => count($rows), 'total_pages' => 1];
$waktuAcuan        = $waktuAcuan ?? date('Y-m-d H:i:s');
$canViewStaffBoard = !empty($canViewStaffBoard);
$perms             = $perms ?? [];

$qVal              = trim((string) ($filters['q'] ?? ''));
$prodiVal          = (string) ($filters['prodi_id'] ?? 'all');
$perusahaanVal     = (string) ($filters['perusahaan_id'] ?? 'all');
$jenisVal          = (string) ($filters['jenis_pekerjaan'] ?? 'all');
$efektifVal        = (string) ($filters['status_efektif'] ?? 'all');
$validasiVal       = (string) ($filters['status_validasi'] ?? 'all');
$currentSort       = (string) ($filters['sort'] ?? '');
$currentDir        = strtolower((string) ($filters['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

$hasActiveFilter = ($qVal !== '')
    || ($prodiVal !== '' && $prodiVal !== 'all')
    || ($perusahaanVal !== '' && $perusahaanVal !== 'all')
    || ($jenisVal !== '' && $jenisVal !== 'all')
    || ($efektifVal !== '' && $efektifVal !== 'all')
    || ($validasiVal !== '' && $validasiVal !== 'all');

$buildUrl = static function (array $overrides = []) use ($filters): string {
    $params = array_merge($filters, $overrides);
    $clean  = [];
    foreach ($params as $k => $v) {
        $sv = trim((string) $v);
        if ($sv === '' || $sv === 'all' || ($k === 'page' && (int) $sv <= 1)) {
            continue;
        }
        $clean[$k] = $sv;
    }
    return base_url('ketarunaan/lowongan') . (!empty($clean) ? ('?' . http_build_query($clean)) : '');
};

$renderSortTh = static function (string $colKey, string $label, string $extraClass = '') use ($currentSort, $currentDir, $buildUrl): string {
    $isSorted = ($currentSort === $colKey);
    $ariaSort = $isSorted ? ($currentDir === 'asc' ? 'ascending' : 'descending') : 'none';
    $nextDir  = ($isSorted && $currentDir === 'asc') ? 'desc' : 'asc';
    $iconCls  = !$isSorted
        ? 'bi-arrow-down-up text-muted opacity-50'
        : ($currentDir === 'asc' ? 'bi-sort-up text-primary' : 'bi-sort-down text-primary');
    $url = $buildUrl(['sort' => $colKey, 'dir' => $nextDir, 'page' => 1]);

    return sprintf(
        '<th%s aria-sort="%s"><a href="%s" class="low-sort-btn"><span>%s</span><i class="bi %s" aria-hidden="true"></i></a></th>',
        $extraClass !== '' ? (' class="' . esc($extraClass) . '"') : '',
        esc($ariaSort),
        esc($url),
        esc($label),
        esc($iconCls)
    );
};
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <!-- 4 Kartu Metrik Interaktif (Tanpa border-top 3px sesuai craft-floor.md) -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <a href="<?= esc($buildUrl(['status_efektif' => 'terbuka', 'status_validasi' => 'all', 'page' => 1])) ?>"
               class="ecc-low-kpi <?= $efektifVal === 'terbuka' ? 'active-kpi' : '' ?>">
                <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Lowongan Terbuka</div>
                <div class="fw-bold fs-4 text-success-emphasis ecc-num"><?= number_format((int) $kpi['terbuka']) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tervalidasi, terbit, dan aktif</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <?php if ($canViewStaffBoard): ?>
                <a href="<?= esc($buildUrl(['status_validasi' => 'antrean', 'status_efektif' => 'all', 'page' => 1])) ?>"
                   class="ecc-low-kpi <?= in_array($validasiVal, ['antrean', 'diajukan', 'perlu_perbaikan'], true) ? 'active-kpi' : '' ?>">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Menunggu Verifikasi</div>
                    <div class="fw-bold fs-4 text-warning-emphasis ecc-num"><?= number_format((int) $kpi['menunggu']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Diajukan dan perlu perbaikan</div>
                </a>
            <?php else: ?>
                <a href="<?= esc($buildUrl(['status_efektif' => 'segera_berakhir', 'page' => 1])) ?>"
                   class="ecc-low-kpi <?= $efektifVal === 'segera_berakhir' ? 'active-kpi' : '' ?>">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Segera Berakhir</div>
                    <div class="fw-bold fs-4 text-warning-emphasis ecc-num"><?= number_format((int) $kpi['segera_berakhir']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tenggat dalam 7 hari ke depan</div>
                </a>
            <?php endif; ?>
        </div>
        <div class="col-6 col-xl-3">
            <?php if ($canViewStaffBoard): ?>
                <a href="<?= esc($buildUrl(['status_efektif' => 'arsip', 'status_validasi' => 'all', 'page' => 1])) ?>"
                   class="ecc-low-kpi <?= in_array($efektifVal, ['arsip', 'ditutup', 'berakhir'], true) ? 'active-kpi' : '' ?>">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Ditutup &amp; Berakhir</div>
                    <div class="fw-bold fs-4 text-dark ecc-num"><?= number_format((int) $kpi['ditutup']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tersimpan di arsip riwayat</div>
                </a>
            <?php else: ?>
                <div class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Ditutup &amp; Berakhir</div>
                    <div class="fw-bold fs-4 text-dark ecc-num"><?= number_format((int) $kpi['ditutup']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tersimpan di arsip riwayat</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-6 col-xl-3">
            <?php if ($canViewStaffBoard): ?>
                <a href="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Perusahaan Mitra</div>
                    <div class="fw-bold fs-4 text-primary ecc-num"><?= number_format((int) $kpi['perusahaan']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Terdaftar di jejaring industri</div>
                </a>
            <?php else: ?>
                <div class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Perusahaan Mitra</div>
                    <div class="fw-bold fs-4 text-primary ecc-num"><?= number_format((int) $kpi['perusahaan']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Terdaftar di jejaring industri</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bar Filter Direktori (2 Baris Proporsional — Bebas Tabrakan) -->
    <div class="ecc-low-card mb-3 py-3">
        <form method="get" action="<?= base_url('ketarunaan/lowongan') ?>" class="row g-2 align-items-end low-filter-form">
            <div class="col-12 col-md-6 col-lg-4">
                <label for="filter_q" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input type="text" name="q" id="filter_q" class="form-control ecc-control-sm"
                       value="<?= esc($qVal) ?>" placeholder="Cari posisi, perusahaan, atau lokasi">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="filter_prodi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Studi</label>
                <select name="prodi_id" id="filter_prodi" class="form-select ecc-control-sm">
                    <option value="all">Semua Prodi</option>
                    <?php foreach (($refs['prodi'] ?? []) as $pr): ?>
                        <option value="<?= (int) $pr['id'] ?>" <?= $prodiVal === (string) $pr['id'] ? 'selected' : '' ?>>
                            <?= esc($pr['kode_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-3">
                <label for="filter_perusahaan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Perusahaan</label>
                <select name="perusahaan_id" id="filter_perusahaan" class="form-select ecc-control-sm js-low-select2">
                    <option value="all">Semua Mitra</option>
                    <?php foreach (($refs['perusahaan'] ?? []) as $pm): ?>
                        <option value="<?= (int) $pm['id'] ?>" <?= $perusahaanVal === (string) $pm['id'] ? 'selected' : '' ?>>
                            <?= esc($pm['nama_perusahaan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label for="filter_jenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis Pekerjaan</label>
                <select name="jenis_pekerjaan" id="filter_jenis" class="form-select ecc-control-sm">
                    <option value="all">Semua Jenis</option>
                    <?php foreach (($refs['jenis_catalog'] ?? []) as $kJ => $lblJ): ?>
                        <option value="<?= esc($kJ) ?>" <?= $jenisVal === $kJ ? 'selected' : '' ?>><?= esc($lblJ) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($canViewStaffBoard): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <label for="filter_efektif" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Tayang</label>
                    <select name="status_efektif" id="filter_efektif" class="form-select ecc-control-sm">
                        <option value="all">Semua Status</option>
                        <option value="terbuka" <?= $efektifVal === 'terbuka' ? 'selected' : '' ?>>Terbuka</option>
                        <option value="segera_berakhir" <?= $efektifVal === 'segera_berakhir' ? 'selected' : '' ?>>Segera Berakhir (7 Hari)</option>
                        <option value="draf_tervalidasi" <?= $efektifVal === 'draf_tervalidasi' ? 'selected' : '' ?>>Draf Siap Terbit</option>
                        <option value="draf" <?= $efektifVal === 'draf' ? 'selected' : '' ?>>Draf</option>
                        <option value="berakhir" <?= $efektifVal === 'berakhir' ? 'selected' : '' ?>>Berakhir</option>
                        <option value="ditutup" <?= $efektifVal === 'ditutup' ? 'selected' : '' ?>>Ditutup</option>
                        <option value="arsip" <?= $efektifVal === 'arsip' ? 'selected' : '' ?>>Arsip (Ditutup &amp; Berakhir)</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-3">
                    <label for="filter_validasi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                    <select name="status_validasi" id="filter_validasi" class="form-select ecc-control-sm">
                        <option value="all">Semua Validasi</option>
                        <option value="tervalidasi" <?= $validasiVal === 'tervalidasi' ? 'selected' : '' ?>>Tervalidasi</option>
                        <option value="antrean" <?= $validasiVal === 'antrean' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                        <option value="diajukan" <?= $validasiVal === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="perlu_perbaikan" <?= $validasiVal === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                        <option value="draf" <?= $validasiVal === 'draf' ? 'selected' : '' ?>>Draf</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-12 <?= $canViewStaffBoard ? 'col-md-4 col-lg-6' : 'col-md-8 col-lg-12' ?> d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile low-touch-ctrl text-nowrap">
                    Terapkan
                </button>
                <?php if ($hasActiveFilter): ?>
                    <a href="<?= base_url('ketarunaan/lowongan') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Reset
                    </a>
                <?php endif; ?>
                <?php if ($canViewStaffBoard): ?>
                    <a href="<?= base_url('ketarunaan/lowongan/ekspor?' . http_build_query($filters)) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile low-touch-ctrl text-nowrap">
                        <i class="bi bi-download" aria-hidden="true"></i> Ekspor CSV
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Desktop -->
    <div class="ecc-low-card d-none d-md-block p-0 overflow-hidden">
        <div class="ecc-table-low-wrap table-responsive">
            <table class="table ecc-table-low mb-0">
                <thead>
                    <tr>
                        <?= $renderSortTh('judul_lowongan', 'Posisi') ?>
                        <?= $renderSortTh('nama_perusahaan', 'Perusahaan') ?>
                        <th>Prodi Sasaran</th>
                        <?= $renderSortTh('lokasi', 'Lokasi') ?>
                        <?= $renderSortTh('batas_pendaftaran', 'Batas Lamaran') ?>
                        <?php if ($canViewStaffBoard): ?>
                            <?= $renderSortTh('status_validasi', 'Validasi') ?>
                            <?= $renderSortTh('status_publikasi', 'Publikasi') ?>
                        <?php else: ?>
                            <?= $renderSortTh('status_publikasi', 'Status') ?>
                        <?php endif; ?>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $canViewStaffBoard ? 8 : 7 ?>" class="text-center py-5">
                                <div class="fw-semibold text-dark mb-1">Belum ada lowongan yang sesuai dengan filter</div>
                                <div class="ecc-text-muted-aa small mb-3">
                                    <?= $hasActiveFilter ? 'Ubah atau reset filter pencarian untuk menampilkan seluruh lowongan.' : 'Belum ada lowongan pekerjaan yang tercatat pada direktori ini.' ?>
                                </div>
                                <div class="d-inline-flex flex-wrap justify-content-center gap-2">
                                    <?php if ($hasActiveFilter): ?>
                                        <a href="<?= base_url('ketarunaan/lowongan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Reset Filter</a>
                                    <?php endif; ?>
                                    <?php if (!empty($perms['can_manage'])): ?>
                                        <a href="<?= base_url('ketarunaan/lowongan/tambah') ?>" class="btn btn-primary btn-sm rounded-pill px-3">Tambah Lowongan</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php $detUrl = base_url('ketarunaan/lowongan/detail/' . (int) $row['id']); ?>
                            <tr class="low-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail lowongan <?= esc($row['judul_lowongan']) ?>">
                                <td>
                                    <a href="<?= esc($detUrl) ?>" class="fw-semibold text-dark text-decoration-none">
                                        <?= esc($row['judul_lowongan']) ?>
                                    </a>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                        <?= esc($row['jenis_pekerjaan']) ?><?= !empty($row['jenjang_pendidikan']) ? ' &bull; ' . esc($row['jenjang_pendidikan']) : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($row['nama_perusahaan'] ?? '-') ?></div>
                                    <?php if ($canViewStaffBoard): ?>
                                        <div class="mt-1"><?= ketarunaan_perusahaan_verifikasi_badge((int) ($row['perusahaan_verified'] ?? 0)) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach (($row['sasaran_prodi'] ?? []) as $pr): ?>
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?= esc($pr['kode_prodi']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                <td class="ecc-num"><?= esc(ketarunaan_format_tanggal($row['batas_pendaftaran'] ?? null)) ?></td>
                                <?php if ($canViewStaffBoard): ?>
                                    <td><?= ketarunaan_validasi_badge((string) ($row['status_validasi'] ?? 'draf')) ?></td>
                                    <td><?= ketarunaan_lowongan_publikasi_badge($row, $waktuAcuan) ?></td>
                                <?php else: ?>
                                    <td><?= ketarunaan_lowongan_publikasi_badge($row, $waktuAcuan) ?></td>
                                <?php endif; ?>
                                <td class="text-end text-nowrap">
                                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm ecc-btn-action btn-tactile" aria-label="Buka detail <?= esc($row['judul_lowongan']) ?>">
                                        Detail
                                    </a>
                                    <?php if (!empty($perms['can_manage'])): ?>
                                        <a href="<?= base_url('ketarunaan/lowongan/edit/' . (int) $row['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action btn-tactile ms-1" aria-label="Ubah lowongan <?= esc($row['judul_lowongan']) ?>">
                                            Ubah
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daftar Kartu Mobile -->
    <div class="d-md-none d-flex flex-column gap-3">
        <?php if (empty($rows)): ?>
            <div class="ecc-low-card text-center py-4">
                <div class="fw-semibold text-dark mb-1">Belum ada lowongan yang sesuai dengan filter</div>
                <div class="ecc-text-muted-aa small mb-3">
                    <?= $hasActiveFilter ? 'Reset filter untuk melihat seluruh lowongan.' : 'Belum ada data lowongan tercatat.' ?>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <?php if ($hasActiveFilter): ?>
                        <a href="<?= base_url('ketarunaan/lowongan') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 low-touch-ctrl d-inline-flex align-items-center">Reset Filter</a>
                    <?php endif; ?>
                    <?php if (!empty($perms['can_manage'])): ?>
                        <a href="<?= base_url('ketarunaan/lowongan/tambah') ?>" class="btn btn-primary btn-sm rounded-pill px-3 low-touch-ctrl d-inline-flex align-items-center">Tambah Lowongan</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <?php $detUrl = base_url('ketarunaan/lowongan/detail/' . (int) $row['id']); ?>
                <div class="ecc-low-card p-3 low-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail lowongan <?= esc($row['judul_lowongan']) ?>">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark"><?= esc($row['judul_lowongan']) ?></div>
                            <div class="ecc-text-muted-aa small"><?= esc($row['nama_perusahaan'] ?? '-') ?></div>
                        </div>
                        <?= ketarunaan_lowongan_publikasi_badge($row, $waktuAcuan) ?>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <?php if ($canViewStaffBoard): ?>
                            <?= ketarunaan_validasi_badge((string) ($row['status_validasi'] ?? 'draf')) ?>
                        <?php endif; ?>
                        <?php foreach (($row['sasaran_prodi'] ?? []) as $pr): ?>
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?= esc($pr['kode_prodi']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="ecc-text-muted-aa mb-3 ecc-num small">
                        <?= esc($row['lokasi'] ?? '-') ?> &bull; Batas: <?= esc(ketarunaan_format_tanggal($row['batas_pendaftaran'] ?? null)) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm ecc-btn-action flex-fill low-touch-ctrl">Lihat Detail</a>
                        <?php if (!empty($perms['can_manage'])): ?>
                            <a href="<?= base_url('ketarunaan/lowongan/edit/' . (int) $row['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action low-touch-ctrl">Ubah</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ((int) ($pagination['total_pages'] ?? 1) > 1): ?>
        <?php
        $curPage   = (int) $pagination['page'];
        $totPages  = (int) $pagination['total_pages'];
        $totRows   = (int) $pagination['total_rows'];
        ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
            <div class="ecc-text-muted-aa small ecc-num">
                Halaman <?= $curPage ?> dari <?= $totPages ?> (total <?= $totRows ?> lowongan)
            </div>
            <nav aria-label="Paginasi Daftar Lowongan">
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link rounded-2" href="<?= esc($buildUrl(['page' => max(1, $curPage - 1)])) ?>">Sebelumnya</a>
                    </li>
                    <?php for ($p = max(1, $curPage - 2); $p <= min($totPages, $curPage + 2); $p++): ?>
                        <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                            <a class="page-link rounded-2 ecc-num" href="<?= esc($buildUrl(['page' => $p])) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $curPage >= $totPages ? 'disabled' : '' ?>">
                        <a class="page-link rounded-2" href="<?= esc($buildUrl(['page' => min($totPages, $curPage + 1)])) ?>">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<?= $this->endSection() ?>
