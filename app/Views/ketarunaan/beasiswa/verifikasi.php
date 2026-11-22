<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Verifikasi Klaim Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters    = $filters ?? [];
$summary    = $summary ?? [];
$rows       = $rows ?? [];
$pagination = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$refs       = $refs ?? [];
$perms      = $perms ?? [];

$curHasil  = (string) ($filters['hasil_verifikasi'] ?? 'all');
$curJenis  = (string) ($filters['jenis_beasiswa'] ?? 'all');
$curProgId = (int) ($filters['program_beasiswa_id'] ?? 0);
$curTahun  = (string) ($filters['tahun_akademik_id'] ?? 'all');
$curQ      = (string) ($filters['q'] ?? '');
$sortBy    = (string) ($filters['sort_by'] ?? '');
$sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$baseParams = [
    'hasil_verifikasi'    => $curHasil,
    'jenis_beasiswa'      => $curJenis,
    'program_beasiswa_id' => $curProgId,
    'tahun_akademik_id'   => $curTahun,
    'q'                   => $curQ,
    'sort_by'             => $sortBy,
    'sort_order'          => $sortOrder,
];
$hasActiveFilters = ($curHasil !== 'all' || $curJenis !== 'all' || $curProgId > 0 || $curTahun !== 'all' || $curQ !== '');

$buildQuickUrl = static function (string $targetHasil) use ($baseParams, $curHasil): string {
    $next = ($curHasil === $targetHasil) ? 'all' : $targetHasil;
    return base_url('ketarunaan/beasiswa/verifikasi?' . http_build_query(array_merge($baseParams, ['hasil_verifikasi' => $next, 'page' => 1])));
};
$buildSortUrl = static function (string $col) use ($baseParams, $sortBy, $sortOrder): string {
    $next = ($sortBy === $col && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/beasiswa/verifikasi?' . http_build_query(array_merge($baseParams, ['sort_by' => $col, 'sort_order' => $next, 'page' => 1])));
};
$getAriaSort = static function (string $col) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $col) return 'none';
    return $sortOrder === 'asc' ? 'ascending' : 'descending';
};
$getSortIcon = static function (string $col) use ($sortBy, $sortOrder): string {
    if ($sortBy !== $col) return '<i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i>';
    return $sortOrder === 'asc'
        ? '<i class="bi bi-arrow-up text-primary" aria-hidden="true"></i>'
        : '<i class="bi bi-arrow-down text-primary" aria-hidden="true"></i>';
};
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <!-- Strip 4 Status Antrean Verifikasi (Filter Cepat Interaktif) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl('belum_diperiksa')) ?>" class="ecc-bea-kpi <?= $curHasil === 'belum_diperiksa' ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Belum Diperiksa</div>
                <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($summary['belum_diperiksa'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Menunggu pemeriksaan kelayakan</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl('memenuhi')) ?>" class="ecc-bea-kpi <?= $curHasil === 'memenuhi' ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Memenuhi (Siap SK)</div>
                <div class="fw-bold fs-3 text-success ecc-num"><?= (int) ($summary['memenuhi'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Lolos syarat program</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl('perlu_perbaikan')) ?>" class="ecc-bea-kpi <?= $curHasil === 'perlu_perbaikan' ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Perlu Perbaikan</div>
                <div class="fw-bold fs-3 text-warning-emphasis ecc-num"><?= (int) ($summary['perlu_perbaikan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Dikembalikan untuk dilengkapi</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl('tidak_memenuhi')) ?>" class="ecc-bea-kpi <?= $curHasil === 'tidak_memenuhi' ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tidak Memenuhi</div>
                <div class="fw-bold fs-3 text-danger ecc-num"><?= (int) ($summary['tidak_memenuhi'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tidak lolos ketentuan</div>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="ecc-bea-card mb-4">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa/verifikasi') ?>" class="row g-2 align-items-end bea-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">

            <div class="col-12 col-md-3">
                <label for="verifHasil" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Hasil Verifikasi</label>
                <select id="verifHasil" name="hasil_verifikasi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Antrean &amp; Riwayat</option>
                    <?php foreach (['belum_diperiksa' => 'Belum Diperiksa', 'perlu_perbaikan' => 'Perlu Perbaikan', 'memenuhi' => 'Memenuhi', 'tidak_memenuhi' => 'Tidak Memenuhi'] as $vk => $vl): ?>
                        <option value="<?= esc($vk) ?>" <?= $curHasil === $vk ? 'selected' : '' ?>><?= esc($vl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="verifJenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis Beasiswa</label>
                <select id="verifJenis" name="jenis_beasiswa" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis</option>
                    <?php foreach (ketarunaan_jenis_beasiswa_catalog() as $jk => $jm): ?>
                        <option value="<?= esc($jk) ?>" <?= $curJenis === $jk ? 'selected' : '' ?>><?= esc($jm['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="verifProgram" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program</label>
                <select id="verifProgram" name="program_beasiswa_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="0">Semua Program</option>
                    <?php foreach (($refs['programs'] ?? []) as $pr): ?>
                        <option value="<?= (int) $pr['id'] ?>" <?= $curProgId === (int) $pr['id'] ? 'selected' : '' ?>><?= esc($pr['nama_program']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="verifTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode</label>
                <select id="verifTahun" name="tahun_akademik_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= $curTahun === (string) $ta['id'] ? 'selected' : '' ?>><?= esc($ta['kode_tahun']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-8">
                <label for="verifSearch" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Taruna / Program</label>
                <input type="text" id="verifSearch" name="q" class="form-control ecc-control-sm" value="<?= esc($curQ) ?>" placeholder="Ketik nama, nomor taruna, atau program...">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2 justify-content-md-end">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/verifikasi') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-4 fw-semibold bea-touch-ctrl btn-tactile">Terapkan</button>
            </div>
        </form>
    </div>

    <!-- Tabel & Kartu Antrean Verifikasi -->
    <div class="ecc-bea-card">
        <div class="alert alert-info border-0 rounded-4 px-3 py-2 mb-3 small" role="note">
            Pemeriksaan dengan hasil <strong>Memenuhi</strong> tidak otomatis menetapkan penerima. Keputusan penetapan dilakukan melalui tahap Penetapan SK.
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0">
                <thead>
                    <tr>
                        <th aria-sort="<?= esc($getAriaSort('taruna')) ?>"><a href="<?= esc($buildSortUrl('taruna')) ?>" class="bea-sort-link <?= $sortBy === 'taruna' ? 'is-sorted' : '' ?>"><span>Taruna</span> <?= $getSortIcon('taruna') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('program')) ?>"><a href="<?= esc($buildSortUrl('program')) ?>" class="bea-sort-link <?= $sortBy === 'program' ? 'is-sorted' : '' ?>"><span>Program &amp; Jenis</span> <?= $getSortIcon('program') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('tanggal')) ?>"><a href="<?= esc($buildSortUrl('tanggal')) ?>" class="bea-sort-link <?= $sortBy === 'tanggal' ? 'is-sorted' : '' ?>"><span>Tanggal Pengajuan</span> <?= $getSortIcon('tanggal') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('hasil_verifikasi')) ?>"><a href="<?= esc($buildSortUrl('hasil_verifikasi')) ?>" class="bea-sort-link <?= $sortBy === 'hasil_verifikasi' ? 'is-sorted' : '' ?>"><span>Hasil Verifikasi</span> <?= $getSortIcon('hasil_verifikasi') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('status_penetapan')) ?>"><a href="<?= esc($buildSortUrl('status_penetapan')) ?>" class="bea-sort-link <?= $sortBy === 'status_penetapan' ? 'is-sorted' : '' ?>"><span>Status Penetapan</span> <?= $getSortIcon('status_penetapan') ?></a></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" class="text-center ecc-text-muted-aa py-4">Tidak ada pengajuan pada filter verifikasi ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $rowId   = (int) $r['id'];
                            $subName = (string) ($r['nama_lengkap'] ?: $r['nomor_taruna']);
                            $verUrl  = base_url('ketarunaan/beasiswa/verifikasi/' . $rowId);
                            ?>
                            <tr class="bea-row-clickable" data-href="<?= esc($verUrl) ?>" tabindex="0" role="link" aria-label="Periksa kelayakan <?= esc($subName) ?>">
                                <td>
                                    <div class="fw-semibold"><?= esc($subName) ?></div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) $r['nama_program']) ?></div>
                                    <div class="mt-1"><?= ketarunaan_jenis_beasiswa_badge((string) $r['jenis_beasiswa']) ?></div>
                                </td>
                                <td class="ecc-num"><?= esc(ketarunaan_format_tanggal((string) ($r['tanggal_pengajuan'] ?: ''), true)) ?></td>
                                <td><?= ketarunaan_verifikasi_beasiswa_badge((string) $r['hasil_verifikasi']) ?></td>
                                <td><?= ketarunaan_penetapan_beasiswa_badge((string) $r['status_penetapan']) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= esc($verUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Periksa kelayakan <?= esc($subName) ?>">
                                        Periksa Kelayakan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Verifikasi -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Tidak ada pengajuan pada filter verifikasi ini.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $rowId   = (int) $r['id'];
                    $subName = (string) ($r['nama_lengkap'] ?: $r['nomor_taruna']);
                    $verUrl  = base_url('ketarunaan/beasiswa/verifikasi/' . $rowId);
                    ?>
                    <div class="border rounded-3 p-3 bg-white bea-row-clickable bea-mobile-card" data-href="<?= esc($verUrl) ?>" tabindex="0" role="link" aria-label="Periksa kelayakan <?= esc($subName) ?>">
                        <div class="fw-bold text-dark"><?= esc($subName) ?></div>
                        <div class="ecc-text-muted-aa ecc-num mb-1" style="font-size: 0.75rem;">
                            <?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?> &bull; <?= esc(ketarunaan_format_tanggal((string) ($r['tanggal_pengajuan'] ?: ''), true)) ?>
                        </div>
                        <div class="fw-semibold text-dark small mb-2"><?= esc((string) $r['nama_program']) ?></div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?= ketarunaan_jenis_beasiswa_badge((string) $r['jenis_beasiswa']) ?>
                            <?= ketarunaan_verifikasi_beasiswa_badge((string) $r['hasil_verifikasi']) ?>
                            <?= ketarunaan_penetapan_beasiswa_badge((string) $r['status_penetapan']) ?>
                        </div>
                        <div class="pt-2 border-top">
                            <a href="<?= esc($verUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Periksa Kelayakan</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Ringkasan & Paginasi -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
            <div class="ecc-text-muted-aa ecc-num small">
                Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> pengajuan
            </div>
            <?php if ((int) $pagination['total_pages'] > 1): ?>
                <?php
                $curPage    = (int) $pagination['page'];
                $totalPages = (int) $pagination['total_pages'];
                $startPg    = max(1, $curPage - 2);
                $endPg      = min($totalPages, $curPage + 2);
                ?>
                <nav aria-label="Paginasi Antrean Verifikasi">
                    <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap ecc-num">
                        <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                            <?php if ($curPage <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/verifikasi?' . http_build_query(array_merge($baseParams, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($p = $startPg; $p <= $endPg; $p++): ?>
                            <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/verifikasi?' . http_build_query(array_merge($baseParams, ['page' => $p]))) ?>"<?= $p === $curPage ? ' aria-current="page"' : '' ?>><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                            <?php if ($curPage >= $totalPages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/verifikasi?' . http_build_query(array_merge($baseParams, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<?= $this->endSection() ?>
