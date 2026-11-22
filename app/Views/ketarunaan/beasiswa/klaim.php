<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Klaim Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters      = $filters ?? [];
$rows         = $rows ?? [];
$pagination   = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$refs         = $refs ?? [];
$perms        = $perms ?? [];
$isSelfTaruna = !empty($isSelfTaruna);
$canSubmit    = !empty($perms['can_submit_claim']);

$curJenis  = (string) ($filters['jenis_beasiswa'] ?? 'all');
$curSumber = (string) ($filters['sumber_klaim'] ?? 'all');
$curVerif  = (string) ($filters['hasil_verifikasi'] ?? 'all');
$curProgId = (int) ($filters['program_beasiswa_id'] ?? 0);
$curTahun  = (string) ($filters['tahun_akademik_id'] ?? 'all');
$curQ      = (string) ($filters['q'] ?? '');
$sortBy    = (string) ($filters['sort_by'] ?? '');
$sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$baseParams = [
    'jenis_beasiswa'      => $curJenis,
    'sumber_klaim'        => $curSumber,
    'hasil_verifikasi'    => $curVerif,
    'program_beasiswa_id' => $curProgId,
    'tahun_akademik_id'   => $curTahun,
    'q'                   => $curQ,
    'sort_by'             => $sortBy,
    'sort_order'          => $sortOrder,
];
$hasActiveFilters = ($curJenis !== 'all' || $curSumber !== 'all' || $curVerif !== 'all' || $curProgId > 0 || $curTahun !== 'all' || $curQ !== '');

$buildSortUrl = static function (string $col) use ($baseParams, $sortBy, $sortOrder): string {
    $next = ($sortBy === $col && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/beasiswa/klaim?' . http_build_query(array_merge($baseParams, ['sort_by' => $col, 'sort_order' => $next, 'page' => 1])));
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

    <?php if ($canSubmit): ?>
        <?php
        $klaimShortcuts = [
            'prestasi'    => ['label' => 'Klaim Prestasi',    'btn_class' => 'btn-outline-primary'],
            'daerah_3tp'  => ['label' => 'Klaim Daerah 3TP',  'btn_class' => 'btn-outline-success'],
            'tidak_mampu' => ['label' => 'Klaim Tidak Mampu', 'btn_class' => 'btn-outline-danger'],
        ];
        $openKlaimMap = [];
        foreach (array_keys($klaimShortcuts) as $jk) {
            if (!$isSelfTaruna) {
                $openKlaimMap[$jk] = true;
                continue;
            }
            foreach (($refs['programs'] ?? []) as $rp) {
                if (($rp['jenis_beasiswa'] ?? '') === $jk && ketarunaan_is_program_beasiswa_open($rp)) {
                    $openKlaimMap[$jk] = true;
                    break;
                }
            }
        }
        ?>
        <!-- Pintasan Ajukan 3 Jenis Klaim (WCAG AA Contrast) -->
        <div class="ecc-bea-card mb-4 py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <div class="fw-bold text-dark">Ajukan Klaim Beasiswa Baru</div>
                    <div class="ecc-text-muted-aa small">
                        <?= $isSelfTaruna
                            ? 'Pengajuan baru hanya tersedia untuk jalur program beasiswa yang sedang dibuka.'
                            : 'Pilih jalur pengajuan klaim yang sesuai disertai tautan Google Drive berkas bukti.' ?>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($klaimShortcuts as $jk => $sc): ?>
                        <?php if (!empty($openKlaimMap[$jk])): ?>
                            <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . $jk) ?>" class="btn <?= esc($sc['btn_class']) ?> btn-sm rounded-pill px-3 btn-tactile bea-touch-ctrl d-inline-flex align-items-center">
                                + <?= esc($sc['label']) ?>
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-light border text-muted btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center" disabled aria-disabled="true" title="Belum ada program yang sedang dibuka">
                                <?= esc($sc['label']) ?> (Belum Dibuka)
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filter Daftar Klaim -->
    <div class="ecc-bea-card mb-4">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa/klaim') ?>" class="row g-2 align-items-end bea-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">

            <div class="col-12 col-md-3">
                <label for="klaimJenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis Klaim</label>
                <select id="klaimJenis" name="jenis_beasiswa" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis Klaim</option>
                    <?php foreach (['prestasi' => 'Beasiswa Prestasi', 'daerah_3tp' => 'Beasiswa Daerah 3TP', 'tidak_mampu' => 'Beasiswa Tidak Mampu'] as $jk => $jl): ?>
                        <option value="<?= esc($jk) ?>" <?= $curJenis === $jk ? 'selected' : '' ?>>
                            <?= esc($jl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="klaimProgram" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program</label>
                <select id="klaimProgram" name="program_beasiswa_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="0">Semua Program</option>
                    <?php foreach (($refs['programs'] ?? []) as $pr): ?>
                        <?php if (($pr['jenis_beasiswa'] ?? '') !== 'apresiasi'): ?>
                            <option value="<?= (int) $pr['id'] ?>" <?= $curProgId === (int) $pr['id'] ? 'selected' : '' ?>>
                                <?= esc($pr['nama_program']) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!$isSelfTaruna): ?>
                <div class="col-6 col-md-2">
                    <label for="klaimSumber" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Sumber Klaim</label>
                    <select id="klaimSumber" name="sumber_klaim" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua</option>
                        <option value="taruna" <?= $curSumber === 'taruna' ? 'selected' : '' ?>>Taruna</option>
                        <option value="pic" <?= $curSumber === 'pic' ? 'selected' : '' ?>>Petugas / PIC</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-6 col-md-2">
                <label for="klaimVerif" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Hasil Verifikasi</label>
                <select id="klaimVerif" name="hasil_verifikasi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <?php foreach (['belum_diperiksa' => 'Belum Diperiksa', 'memenuhi' => 'Memenuhi', 'perlu_perbaikan' => 'Perlu Perbaikan', 'tidak_memenuhi' => 'Tidak Memenuhi'] as $vk => $vl): ?>
                        <option value="<?= esc($vk) ?>" <?= $curVerif === $vk ? 'selected' : '' ?>>
                            <?= esc($vl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="klaimTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode</label>
                <select id="klaimTahun" name="tahun_akademik_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= $curTahun === (string) $ta['id'] ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-8">
                <label for="klaimSearch" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input type="text" id="klaimSearch" name="q" class="form-control ecc-control-sm" value="<?= esc($curQ) ?>"
                       placeholder="<?= $isSelfTaruna ? 'Cari program, nama prestasi, atau wilayah...' : 'Cari nama, nomor taruna, prestasi, atau wilayah...' ?>">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2 justify-content-md-end">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/klaim') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-4 fw-semibold bea-touch-ctrl btn-tactile">Terapkan</button>
            </div>
        </form>
    </div>

    <!-- Tabel & Kartu Daftar Klaim -->
    <div class="ecc-bea-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 fw-bold text-dark mb-0"><?= $isSelfTaruna ? 'Daftar Pengajuan Klaim Saya' : 'Daftar Pengajuan Klaim Beasiswa' ?></h2>
            <span class="ecc-text-muted-aa ecc-num small"><?= (int) $pagination['total_filtered'] ?> pengajuan</span>
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0">
                <thead>
                    <tr>
                        <?php if (!$isSelfTaruna): ?>
                            <th aria-sort="<?= esc($getAriaSort('taruna')) ?>"><a href="<?= esc($buildSortUrl('taruna')) ?>" class="bea-sort-link <?= $sortBy === 'taruna' ? 'is-sorted' : '' ?>"><span>Taruna</span> <?= $getSortIcon('taruna') ?></a></th>
                        <?php endif; ?>
                        <th aria-sort="<?= esc($getAriaSort('program')) ?>"><a href="<?= esc($buildSortUrl('program')) ?>" class="bea-sort-link <?= $sortBy === 'program' ? 'is-sorted' : '' ?>"><span>Program &amp; Jenis</span> <?= $getSortIcon('program') ?></a></th>
                        <th>Ringkasan Klaim</th>
                        <th aria-sort="<?= esc($getAriaSort('sumber_klaim')) ?>"><a href="<?= esc($buildSortUrl('sumber_klaim')) ?>" class="bea-sort-link <?= $sortBy === 'sumber_klaim' ? 'is-sorted' : '' ?>"><span>Sumber &amp; Pencatat</span> <?= $getSortIcon('sumber_klaim') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('hasil_verifikasi')) ?>"><a href="<?= esc($buildSortUrl('hasil_verifikasi')) ?>" class="bea-sort-link <?= $sortBy === 'hasil_verifikasi' ? 'is-sorted' : '' ?>"><span>Verifikasi</span> <?= $getSortIcon('hasil_verifikasi') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('status_penetapan')) ?>"><a href="<?= esc($buildSortUrl('status_penetapan')) ?>" class="bea-sort-link <?= $sortBy === 'status_penetapan' ? 'is-sorted' : '' ?>"><span>Penetapan</span> <?= $getSortIcon('status_penetapan') ?></a></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= $isSelfTaruna ? 6 : 7 ?>" class="text-center ecc-text-muted-aa py-4">Belum ada pengajuan klaim sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $rowId = (int) $r['id'];
                            $jenis = (string) ($r['jenis_beasiswa'] ?? '');
                            $stVal = (string) ($r['status_validasi'] ?? 'draf');
                            $stPen = (string) ($r['status_penetapan'] ?? 'kandidat');
                            $ringkasanKlaim = '-';
                            if ($jenis === 'prestasi') {
                                $ringkasanKlaim = ($r['nama_prestasi'] ?: '-') . (!empty($r['peringkat_prestasi']) ? ' (' . $r['peringkat_prestasi'] . ')' : '');
                            } elseif ($jenis === 'daerah_3tp') {
                                $ringkasanKlaim = trim(($r['kabupaten_kota'] ?? '') . ', ' . ($r['provinsi'] ?? ''), ', ') ?: '-';
                            } elseif ($jenis === 'tidak_mampu') {
                                $ringkasanKlaim = !empty($r['economy_masked'])
                                    ? 'Rincian Ekonomi (Akses Terbatas)'
                                    : ('Tanggungan: ' . (int) ($r['jumlah_tanggungan'] ?? 0) . ' • ' . ($r['pekerjaan_orang_tua'] ?: '-'));
                            }
                            $canEditRow = $canSubmit && $stPen === 'kandidat' && in_array($stVal, ['draf', 'perlu_perbaikan'], true);
                            $detUrl     = base_url('ketarunaan/beasiswa/detail/' . $rowId);
                            $subLabel   = (string) ($r['nama_lengkap'] ?: $r['nomor_taruna']);
                            ?>
                            <tr class="bea-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Buka detail klaim <?= esc($subLabel) ?>">
                                <?php if (!$isSelfTaruna): ?>
                                    <td>
                                        <div class="fw-semibold"><?= esc($subLabel) ?></div>
                                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?></div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) $r['nama_program']) ?></div>
                                    <div class="mt-1"><?= ketarunaan_jenis_beasiswa_badge($jenis) ?></div>
                                </td>
                                <td style="max-width: 240px;">
                                    <div class="text-truncate" title="<?= esc($ringkasanKlaim) ?>"><?= esc($ringkasanKlaim) ?></div>
                                    <?php if ($jenis === 'daerah_3tp' && !empty($r['status_pemeriksaan_3tp'])): ?>
                                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                            Acuan 3TP: <?= esc(ucwords(str_replace('_', ' ', (string) $r['status_pemeriksaan_3tp']))) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1 mb-1">
                                        <?= esc($r['sumber_klaim'] === 'taruna' ? 'Mandiri Taruna' : 'Dicatat Petugas') ?>
                                    </span>
                                    <div class="ecc-text-muted-aa">Oleh: <?= esc((string) ($r['dicatat_oleh'] ?: '-')) ?></div>
                                </td>
                                <td>
                                    <?= ketarunaan_verifikasi_beasiswa_badge((string) $r['hasil_verifikasi']) ?>
                                    <?php if ($stVal === 'draf'): ?>
                                        <div class="mt-1"><span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">Draf</span></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= ketarunaan_penetapan_beasiswa_badge($stPen) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Lihat detail klaim <?= esc($subLabel) ?>">Detail</a>
                                    <?php if ($canEditRow): ?>
                                        <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($jenis) . '?edit=' . $rowId) ?>" class="btn btn-light border btn-sm rounded-pill px-3" aria-label="Ubah klaim <?= esc($subLabel) ?>">Ubah</a>
                                    <?php endif; ?>
                                    <?php if ($canSubmit && $stVal === 'draf' && $stPen !== 'ditetapkan'): ?>
                                        <form method="post" action="<?= base_url('ketarunaan/beasiswa/hapus/' . $rowId) ?>" class="d-inline"
                                              onsubmit="return window.eccBeaConfirmSubmit(event, 'Hapus Draf Klaim?', 'Draf pengajuan ini akan dihapus.', 'Ya, Hapus', '#dc3545');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" aria-label="Hapus draf klaim <?= esc($subLabel) ?>">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Klaim Beasiswa -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Belum ada pengajuan klaim sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $rowId = (int) $r['id'];
                    $jenis = (string) ($r['jenis_beasiswa'] ?? '');
                    $stVal = (string) ($r['status_validasi'] ?? 'draf');
                    $stPen = (string) ($r['status_penetapan'] ?? 'kandidat');
                    $ringkasanKlaim = '-';
                    if ($jenis === 'prestasi') {
                        $ringkasanKlaim = ($r['nama_prestasi'] ?: '-') . (!empty($r['peringkat_prestasi']) ? ' (' . $r['peringkat_prestasi'] . ')' : '');
                    } elseif ($jenis === 'daerah_3tp') {
                        $ringkasanKlaim = trim(($r['kabupaten_kota'] ?? '') . ', ' . ($r['provinsi'] ?? ''), ', ') ?: '-';
                    } elseif ($jenis === 'tidak_mampu') {
                        $ringkasanKlaim = !empty($r['economy_masked'])
                            ? 'Rincian Ekonomi (Akses Terbatas)'
                            : ('Tanggungan: ' . (int) ($r['jumlah_tanggungan'] ?? 0) . ' • ' . ($r['pekerjaan_orang_tua'] ?: '-'));
                    }
                    $canEditRow = $canSubmit && $stPen === 'kandidat' && in_array($stVal, ['draf', 'perlu_perbaikan'], true);
                    $detUrl     = base_url('ketarunaan/beasiswa/detail/' . $rowId);
                    ?>
                    <div class="border rounded-3 p-3 bg-white bea-row-clickable bea-mobile-card" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail klaim <?= esc((string) $r['nama_program']) ?>">
                        <?php if (!$isSelfTaruna): ?>
                            <div class="fw-bold text-dark"><?= esc((string) ($r['nama_lengkap'] ?: $r['nomor_taruna'])) ?></div>
                            <div class="ecc-text-muted-aa ecc-num mb-1" style="font-size: 0.75rem;"><?= esc((string) $r['nomor_taruna']) ?> &bull; <?= esc((string) ($r['kode_prodi'] ?? '-')) ?></div>
                        <?php endif; ?>
                        <div class="fw-semibold text-dark small mb-1"><?= esc((string) $r['nama_program']) ?></div>
                        <div class="ecc-text-muted-aa small mb-2"><?= esc($ringkasanKlaim) ?></div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <?= ketarunaan_jenis_beasiswa_badge($jenis) ?>
                            <?= ketarunaan_verifikasi_beasiswa_badge((string) $r['hasil_verifikasi']) ?>
                            <?= ketarunaan_penetapan_beasiswa_badge($stPen) ?>
                            <?php if ($stVal === 'draf'): ?>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">Draf</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Detail</a>
                            <?php if ($canEditRow): ?>
                                <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($jenis) . '?edit=' . $rowId) ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Ubah</a>
                            <?php endif; ?>
                            <?php if ($canSubmit && $stVal === 'draf' && $stPen !== 'ditetapkan'): ?>
                                <form method="post" action="<?= base_url('ketarunaan/beasiswa/hapus/' . $rowId) ?>" class="d-inline"
                                      onsubmit="return window.eccBeaConfirmSubmit(event, 'Hapus Draf Klaim?', 'Draf pengajuan ini akan dihapus.', 'Ya, Hapus', '#dc3545');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 bea-touch-ctrl">Hapus</button>
                                </form>
                            <?php endif; ?>
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
                <nav aria-label="Paginasi Daftar Klaim Beasiswa">
                    <ul class="pagination pagination-sm mb-0 gap-1 flex-wrap ecc-num">
                        <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                            <?php if ($curPage <= 1): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Sebelumnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/klaim?' . http_build_query(array_merge($baseParams, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                            <?php endif; ?>
                        </li>
                        <?php for ($p = $startPg; $p <= $endPg; $p++): ?>
                            <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/klaim?' . http_build_query(array_merge($baseParams, ['page' => $p]))) ?>"<?= $p === $curPage ? ' aria-current="page"' : '' ?>><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                            <?php if ($curPage >= $totalPages): ?>
                                <span class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" aria-disabled="true">Berikutnya</span>
                            <?php else: ?>
                                <a class="page-link rounded-3 d-inline-flex align-items-center justify-content-center bea-touch-ctrl" href="<?= base_url('ketarunaan/beasiswa/klaim?' . http_build_query(array_merge($baseParams, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
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
