<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Penerima &amp; Penetapan SK Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters      = $filters ?? [];
$summary      = $summary ?? [];
$rows         = $rows ?? [];
$pagination   = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$refs         = $refs ?? [];
$perms        = $perms ?? [];
$isSelfTaruna = !empty($isSelfTaruna);
$canDecide    = !empty($perms['can_decide']);

$curProgId    = (int) ($filters['program_beasiswa_id'] ?? 0);
$curJenis     = (string) ($filters['jenis_beasiswa'] ?? 'all');
$curPenetapan = (string) ($filters['status_penetapan'] ?? 'all');
$curValidasi  = (string) ($filters['status_validasi'] ?? 'all');
$curProdi     = (int) ($filters['prodi_id'] ?? 0);
$curTahun     = (string) ($filters['tahun_akademik_id'] ?? 'all');
$curQ         = (string) ($filters['q'] ?? '');
$sortBy       = (string) ($filters['sort_by'] ?? '');
$sortOrder    = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$baseParams = [
    'program_beasiswa_id' => $curProgId,
    'jenis_beasiswa'      => $curJenis,
    'status_penetapan'    => $curPenetapan,
    'status_validasi'     => $curValidasi,
    'prodi_id'            => $curProdi,
    'tahun_akademik_id'   => $curTahun,
    'q'                   => $curQ,
    'sort_by'             => $sortBy,
    'sort_order'          => $sortOrder,
];
$exportQuery      = http_build_query($baseParams);
$hasActiveFilters = ($curProgId > 0 || $curJenis !== 'all' || $curPenetapan !== 'all' || $curValidasi !== 'all' || $curProdi > 0 || $curTahun !== 'all' || $curQ !== '');

$isTileSah   = ($curPenetapan === 'ditetapkan' && $curValidasi === 'tervalidasi');
$isTileSiap  = ($curPenetapan === 'kandidat' && $curValidasi === 'all');
$isTileBatal = ($curPenetapan === 'dibatalkan' && $curValidasi === 'all');

$buildQuickUrl = static function (array $override) use ($baseParams): string {
    return base_url('ketarunaan/beasiswa/penerima?' . http_build_query(array_merge($baseParams, $override, ['page' => 1])));
};
$buildSortUrl = static function (string $col) use ($baseParams, $sortBy, $sortOrder): string {
    $next = ($sortBy === $col && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/beasiswa/penerima?' . http_build_query(array_merge($baseParams, ['sort_by' => $col, 'sort_order' => $next, 'page' => 1])));
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

    <!-- Strip 4 Angka Kunci Terpisah (Filter Cepat Interaktif) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl($isTileSah ? ['status_penetapan' => 'all', 'status_validasi' => 'all'] : ['status_penetapan' => 'ditetapkan', 'status_validasi' => 'tervalidasi'])) ?>"
               class="ecc-bea-kpi <?= $isTileSah ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;"><?= $isSelfTaruna ? 'Ditetapkan &amp; Sah' : 'Penetapan Disahkan' ?></div>
                <div class="fw-bold fs-3 text-success ecc-num"><?= (int) ($summary['penetapan_disahkan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Ditetapkan &amp; tervalidasi sah</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;"><?= $isSelfTaruna ? 'Status Penerima Aktif' : 'Taruna Penerima Unik' ?></div>
                <div class="fw-bold fs-3 text-primary ecc-num"><?= (int) ($summary['penerima_unik'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= $isSelfTaruna ? 'Keaktifan penerima resmi' : 'Individu penerima resmi' ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl($isTileSiap ? ['status_penetapan' => 'all', 'status_validasi' => 'all'] : ['status_penetapan' => 'kandidat', 'status_validasi' => 'all'])) ?>"
               class="ecc-bea-kpi <?= $isTileSiap ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Siap Penetapan SK</div>
                <div class="fw-bold fs-3 text-warning-emphasis ecc-num"><?= (int) ($summary['siap_penetapan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Memenuhi verifikasi, belum SK</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="<?= esc($buildQuickUrl($isTileBatal ? ['status_penetapan' => 'all', 'status_validasi' => 'all'] : ['status_penetapan' => 'dibatalkan', 'status_validasi' => 'all'])) ?>"
               class="ecc-bea-kpi <?= $isTileBatal ? 'is-active' : '' ?>">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Dibatalkan</div>
                <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($summary['dibatalkan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tidak dihitung sebagai penerima</div>
            </a>
        </div>
    </div>

    <!-- Filter & Ekspor -->
    <div class="ecc-bea-card mb-4">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa/penerima') ?>" class="row g-2 align-items-end bea-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">

            <div class="col-12 col-md-3">
                <label for="penProgram" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Beasiswa</label>
                <select id="penProgram" name="program_beasiswa_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="0">Semua Program</option>
                    <?php foreach (($refs['programs'] ?? []) as $pr): ?>
                        <option value="<?= (int) $pr['id'] ?>" <?= $curProgId === (int) $pr['id'] ? 'selected' : '' ?>>
                            <?= esc($pr['nama_program']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="penJenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis</label>
                <select id="penJenis" name="jenis_beasiswa" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <?php foreach (ketarunaan_jenis_beasiswa_catalog() as $jk => $jm): ?>
                        <option value="<?= esc($jk) ?>" <?= $curJenis === $jk ? 'selected' : '' ?>><?= esc($jm['short']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="penStatus" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Penetapan</label>
                <select id="penStatus" name="status_penetapan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <option value="ditetapkan" <?= $curPenetapan === 'ditetapkan' ? 'selected' : '' ?>>Ditetapkan</option>
                    <option value="kandidat" <?= $curPenetapan === 'kandidat' ? 'selected' : '' ?>>Menunggu SK</option>
                    <option value="dibatalkan" <?= $curPenetapan === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="penValidasi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Validasi</label>
                <select id="penValidasi" name="status_validasi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <option value="draf" <?= $curValidasi === 'draf' ? 'selected' : '' ?>>Draf</option>
                    <option value="diajukan" <?= $curValidasi === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
                    <option value="perlu_perbaikan" <?= $curValidasi === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    <option value="tervalidasi" <?= $curValidasi === 'tervalidasi' ? 'selected' : '' ?>>Tervalidasi</option>
                </select>
            </div>
            <?php if (!$isSelfTaruna): ?>
                <div class="col-6 col-md-3">
                    <label for="penProdi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Studi</label>
                    <select id="penProdi" name="prodi_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="0">Semua Prodi</option>
                        <?php foreach (($refs['prodi'] ?? []) as $prd): ?>
                            <option value="<?= (int) $prd['id'] ?>" <?= $curProdi === (int) $prd['id'] ? 'selected' : '' ?>><?= esc($prd['kode_prodi'] . ' - ' . $prd['nama_prodi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-6 col-md-3">
                <label for="penTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select id="penTahun" name="tahun_akademik_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= $curTahun === (string) $ta['id'] ? 'selected' : '' ?>><?= esc($ta['kode_tahun']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-5">
                <label for="penSearch" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input type="text" id="penSearch" name="q" class="form-control ecc-control-sm" value="<?= esc($curQ) ?>"
                       placeholder="<?= $isSelfTaruna ? 'Cari nama program atau nomor SK...' : 'Nama taruna, nomor taruna, nomor SK...' ?>">
            </div>
            <div class="col-12 col-md-4 d-flex flex-wrap gap-2 justify-content-md-end">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/penerima') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-4 fw-semibold bea-touch-ctrl btn-tactile">Terapkan</button>
                <?php if (!$isSelfTaruna): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile bea-touch-ctrl">
                        <i class="bi bi-download" aria-hidden="true"></i> Ekspor CSV
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel & Kartu Penerima & Kandidat SK -->
    <div class="ecc-bea-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 fw-bold text-dark mb-0"><?= $isSelfTaruna ? 'Riwayat Pengajuan &amp; SK Beasiswa Saya' : 'Daftar Pengajuan, Kandidat SK &amp; Penerima Beasiswa' ?></h2>
            <span class="ecc-text-muted-aa ecc-num small">Total <?= (int) $pagination['total_filtered'] ?> data</span>
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0">
                <thead>
                    <tr>
                        <?php if (!$isSelfTaruna): ?>
                            <th aria-sort="<?= esc($getAriaSort('taruna')) ?>"><a href="<?= esc($buildSortUrl('taruna')) ?>" class="bea-sort-link <?= $sortBy === 'taruna' ? 'is-sorted' : '' ?>"><span>Taruna</span> <?= $getSortIcon('taruna') ?></a></th>
                        <?php endif; ?>
                        <th aria-sort="<?= esc($getAriaSort('program')) ?>"><a href="<?= esc($buildSortUrl('program')) ?>" class="bea-sort-link <?= $sortBy === 'program' ? 'is-sorted' : '' ?>"><span>Program &amp; Jenis</span> <?= $getSortIcon('program') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('hasil_verifikasi')) ?>"><a href="<?= esc($buildSortUrl('hasil_verifikasi')) ?>" class="bea-sort-link <?= $sortBy === 'hasil_verifikasi' ? 'is-sorted' : '' ?>"><span>Verifikasi</span> <?= $getSortIcon('hasil_verifikasi') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('status_penetapan')) ?>"><a href="<?= esc($buildSortUrl('status_penetapan')) ?>" class="bea-sort-link <?= $sortBy === 'status_penetapan' ? 'is-sorted' : '' ?>"><span>Penetapan</span> <?= $getSortIcon('status_penetapan') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('status_validasi')) ?>"><a href="<?= esc($buildSortUrl('status_validasi')) ?>" class="bea-sort-link <?= $sortBy === 'status_validasi' ? 'is-sorted' : '' ?>"><span>Validasi</span> <?= $getSortIcon('status_validasi') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('tanggal_penetapan')) ?>"><a href="<?= esc($buildSortUrl('tanggal_penetapan')) ?>" class="bea-sort-link <?= $sortBy === 'tanggal_penetapan' ? 'is-sorted' : '' ?>"><span>Nomor SK &amp; Tanggal</span> <?= $getSortIcon('tanggal_penetapan') ?></a></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $isSelfTaruna ? 6 : 7 ?>" class="text-center ecc-text-muted-aa py-4">Tidak ada data penerima beasiswa pada filter ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $rowId   = (int) $r['id'];
                            $verif   = (string) ($r['hasil_verifikasi'] ?? 'belum_diperiksa');
                            $tetap   = (string) ($r['status_penetapan'] ?? 'kandidat');
                            $valid   = (string) ($r['status_validasi'] ?? 'draf');
                            $siapSk  = ($verif === 'memenuhi' && $tetap === 'kandidat');
                            $subName = (string) ($r['nama_lengkap'] ?: $r['nomor_taruna']);
                            $detUrl  = base_url('ketarunaan/beasiswa/detail/' . $rowId);
                            ?>
                            <tr class="bea-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail penerima <?= esc($subName) ?>">
                                <?php if (!$isSelfTaruna): ?>
                                    <td>
                                        <div class="fw-semibold"><?= esc($subName) ?></div>
                                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                            <?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?> &bull; <?= esc((string) ($r['nama_kelas'] ?: ($r['kelas_snapshot'] ?? '-'))) ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) $r['nama_program']) ?></div>
                                    <div class="mt-1"><?= ketarunaan_jenis_beasiswa_badge((string) $r['jenis_beasiswa']) ?></div>
                                </td>
                                <td><?= ketarunaan_verifikasi_beasiswa_badge($verif) ?></td>
                                <td><?= ketarunaan_penetapan_beasiswa_badge($tetap) ?></td>
                                <td><?= ketarunaan_validasi_badge($valid) ?></td>
                                <td class="ecc-num small">
                                    <?php if (!empty($r['nomor_keputusan'])): ?>
                                        <div class="fw-semibold"><?= esc((string) $r['nomor_keputusan']) ?></div>
                                        <div class="ecc-text-muted-aa"><?= esc(ketarunaan_format_tanggal((string) ($r['tanggal_penetapan'] ?: ''), true)) ?></div>
                                    <?php else: ?>
                                        <span class="ecc-text-muted-aa">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Lihat detail <?= esc($subName) ?>">Detail</a>
                                    <?php if ($siapSk && $canDecide): ?>
                                        <a href="<?= esc($detUrl) ?>#panelPenetapanSk" class="btn btn-success btn-sm rounded-pill px-3" aria-label="Tetapkan SK untuk <?= esc($subName) ?>">Tetapkan SK</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Penerima -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Tidak ada data penerima beasiswa pada filter ini.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $rowId   = (int) $r['id'];
                    $verif   = (string) ($r['hasil_verifikasi'] ?? 'belum_diperiksa');
                    $tetap   = (string) ($r['status_penetapan'] ?? 'kandidat');
                    $valid   = (string) ($r['status_validasi'] ?? 'draf');
                    $siapSk  = ($verif === 'memenuhi' && $tetap === 'kandidat');
                    $subName = (string) ($r['nama_lengkap'] ?: $r['nomor_taruna']);
                    $detUrl  = base_url('ketarunaan/beasiswa/detail/' . $rowId);
                    ?>
                    <div class="border rounded-3 p-3 bg-white bea-row-clickable bea-mobile-card" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail penerima <?= esc($subName) ?>">
                        <?php if (!$isSelfTaruna): ?>
                            <div class="fw-bold text-dark"><?= esc($subName) ?></div>
                            <div class="ecc-text-muted-aa ecc-num mb-1" style="font-size: 0.75rem;">
                                <?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?> &bull; <?= esc((string) ($r['nama_kelas'] ?: ($r['kelas_snapshot'] ?? '-'))) ?>
                            </div>
                        <?php endif; ?>
                        <div class="fw-semibold text-dark small mb-2"><?= esc((string) $r['nama_program']) ?></div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?= ketarunaan_jenis_beasiswa_badge((string) $r['jenis_beasiswa']) ?>
                            <?= ketarunaan_verifikasi_beasiswa_badge($verif) ?>
                            <?= ketarunaan_penetapan_beasiswa_badge($tetap) ?>
                            <?= ketarunaan_validasi_badge($valid) ?>
                        </div>
                        <?php if (!empty($r['nomor_keputusan'])): ?>
                            <div class="ecc-text-muted-aa ecc-num small mb-2">
                                SK: <strong class="text-dark"><?= esc((string) $r['nomor_keputusan']) ?></strong> (<?= esc(ketarunaan_format_tanggal((string) ($r['tanggal_penetapan'] ?: ''), true)) ?>)
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Detail</a>
                            <?php if ($siapSk && $canDecide): ?>
                                <a href="<?= esc($detUrl) ?>#panelPenetapanSk" class="btn btn-success btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Tetapkan SK</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Ringkasan & Paginasi -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
            <div class="ecc-text-muted-aa ecc-num small">
                Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> data
            </div>
            <?php if ((int) $pagination['total_pages'] > 1): ?>
                <?php
                $curPage    = (int) $pagination['page'];
                $totalPages = (int) $pagination['total_pages'];
                $startPg    = max(1, $curPage - 2);
                $endPg      = min($totalPages, $curPage + 2);
                ?>
                <nav aria-label="Paginasi Daftar Penerima Beasiswa">
                    <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap ecc-num">
                        <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                            <?php if ($curPage <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/penerima?' . http_build_query(array_merge($baseParams, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($p = $startPg; $p <= $endPg; $p++): ?>
                            <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/penerima?' . http_build_query(array_merge($baseParams, ['page' => $p]))) ?>"<?= $p === $curPage ? ' aria-current="page"' : '' ?>><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                            <?php if ($curPage >= $totalPages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/penerima?' . http_build_query(array_merge($baseParams, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 small ecc-text-muted-aa" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
            Hanya penerima dengan status Penetapan <strong>Ditetapkan</strong> dan Validasi <strong>Tervalidasi</strong> yang masuk ke perhitungan resmi.
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<?= $this->endSection() ?>
