<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Program Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters      = $filters ?? [];
$programs     = $programs ?? [];
$refs         = $refs ?? [];
$perms        = $perms ?? [];
$isSelfTaruna = !empty($isSelfTaruna);
$aturanLabels = ketarunaan_aturan_seri_catalog();

$curJenis  = (string) ($filters['jenis_beasiswa'] ?? 'all');
$curStatus = (string) ($filters['status_program'] ?? 'all');
$curTahun  = (string) ($filters['tahun_akademik_id'] ?? 'all');
$curQ      = (string) ($filters['q'] ?? '');
$sortBy    = (string) ($filters['sort_by'] ?? '');
$sortOrder = strtolower((string) ($filters['sort_order'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';

$hasActiveFilters = ($curJenis !== 'all' || $curStatus !== 'all' || $curTahun !== 'all' || $curQ !== '');
$baseParams = [
    'jenis_beasiswa'    => $curJenis,
    'status_program'    => $curStatus,
    'tahun_akademik_id' => $curTahun,
    'q'                 => $curQ,
];
$buildSortUrl = static function (string $col) use ($baseParams, $sortBy, $sortOrder): string {
    $next = ($sortBy === $col && $sortOrder === 'asc') ? 'desc' : 'asc';
    return base_url('ketarunaan/beasiswa/program?' . http_build_query(array_merge($baseParams, ['sort_by' => $col, 'sort_order' => $next])));
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

    <div class="ecc-bea-card mb-4">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa/program') ?>" class="row g-2 align-items-end bea-filter-form">
            <input type="hidden" name="sort_by" value="<?= esc($sortBy) ?>">
            <input type="hidden" name="sort_order" value="<?= esc($sortOrder) ?>">

            <div class="col-12 col-md-3">
                <label for="progJenis" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jenis Beasiswa</label>
                <select id="progJenis" name="jenis_beasiswa" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis Resmi</option>
                    <?php foreach (ketarunaan_jenis_beasiswa_catalog() as $k => $m): ?>
                        <option value="<?= esc($k) ?>" <?= $curJenis === $k ? 'selected' : '' ?>>
                            <?= esc($m['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="progStatus" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Program</label>
                <select id="progStatus" name="status_program" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <?php foreach (['dibuka' => 'Dibuka', 'seleksi' => 'Seleksi', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai'] as $sk => $sl): ?>
                        <option value="<?= esc($sk) ?>" <?= $curStatus === $sk ? 'selected' : '' ?>>
                            <?= esc($sl) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="progTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select id="progTahun" name="tahun_akademik_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= $curTahun === (string) $ta['id'] ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="progSearch" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Program / Pemberi</label>
                <input type="text" id="progSearch" name="q" class="form-control ecc-control-sm" value="<?= esc($curQ) ?>" placeholder="Nama program atau pemberi...">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <?php if ($hasActiveFilters): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/program') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold w-100 bea-touch-ctrl btn-tactile">Terapkan</button>
            </div>
        </form>
    </div>

    <div class="ecc-bea-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 fw-bold text-dark mb-0">Daftar Program &amp; Ketentuan Seleksi</h2>
            <span class="ecc-text-muted-aa ecc-num small"><?= count($programs) ?> program</span>
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0">
                <thead>
                    <tr>
                        <th aria-sort="<?= esc($getAriaSort('nama_program')) ?>"><a href="<?= esc($buildSortUrl('nama_program')) ?>" class="bea-sort-link <?= $sortBy === 'nama_program' ? 'is-sorted' : '' ?>"><span>Nama Program &amp; Periode</span> <?= $getSortIcon('nama_program') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('jenis_beasiswa')) ?>"><a href="<?= esc($buildSortUrl('jenis_beasiswa')) ?>" class="bea-sort-link <?= $sortBy === 'jenis_beasiswa' ? 'is-sorted' : '' ?>"><span>Jenis</span> <?= $getSortIcon('jenis_beasiswa') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('pemberi')) ?>"><a href="<?= esc($buildSortUrl('pemberi')) ?>" class="bea-sort-link <?= $sortBy === 'pemberi' ? 'is-sorted' : '' ?>"><span>Pemberi / Sumber</span> <?= $getSortIcon('pemberi') ?></a></th>
                        <th>Aturan Seleksi</th>
                        <th aria-sort="<?= esc($getAriaSort('kuota')) ?>"><a href="<?= esc($buildSortUrl('kuota')) ?>" class="bea-sort-link <?= $sortBy === 'kuota' ? 'is-sorted' : '' ?>"><span>Kuota Terisi</span> <?= $getSortIcon('kuota') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('nominal')) ?>"><a href="<?= esc($buildSortUrl('nominal')) ?>" class="bea-sort-link <?= $sortBy === 'nominal' ? 'is-sorted' : '' ?>"><span>Nominal</span> <?= $getSortIcon('nominal') ?></a></th>
                        <th aria-sort="<?= esc($getAriaSort('status_program')) ?>"><a href="<?= esc($buildSortUrl('status_program')) ?>" class="bea-sort-link <?= $sortBy === 'status_program' ? 'is-sorted' : '' ?>"><span>Status</span> <?= $getSortIcon('status_program') ?></a></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programs)): ?>
                        <tr>
                            <td colspan="8" class="text-center ecc-text-muted-aa py-4">Tidak ada program yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($programs as $prog): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($prog['nama_program']) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                        <?= esc($prog['tahun_akademik_label'] ?: $prog['tahun']) ?> &bull; PJ: <?= esc($prog['penanggung_jawab'] ?: 'Unit Ketarunaan') ?>
                                    </div>
                                </td>
                                <td><?= ketarunaan_jenis_beasiswa_badge((string) $prog['jenis_beasiswa']) ?></td>
                                <td><?= esc($prog['pemberi']) ?></td>
                                <td class="small">
                                    <?php if (($prog['jenis_beasiswa'] ?? '') === 'apresiasi'): ?>
                                        <div class="fw-semibold">IP Semester (Top <?= (int) ($prog['jumlah_peringkat'] ?? 5) ?> / kelas)</div>
                                        <div class="ecc-text-muted-aa">Pemecah seri: <?= esc($aturanLabels[$prog['aturan_seri'] ?? 'belum_ditetapkan'] ?? 'Belum Ditetapkan') ?></div>
                                    <?php else: ?>
                                        <div class="ecc-text-muted-aa">Pengajuan klaim terverifikasi</div>
                                    <?php endif; ?>
                                </td>
                                <td class="ecc-num">
                                    <span class="fw-bold"><?= (int) $prog['ditetapkan_count'] ?></span> / <?= (int) $prog['kuota'] ?>
                                </td>
                                <td class="ecc-num"><?= esc(ketarunaan_rupiah($prog['nominal'] ?? 0)) ?></td>
                                <td><?= ketarunaan_program_beasiswa_status_badge((string) $prog['status_program']) ?></td>
                                <td class="text-end text-nowrap">
                                    <?php if (($prog['jenis_beasiswa'] ?? '') === 'apresiasi' && !$isSelfTaruna): ?>
                                        <a href="<?= base_url('ketarunaan/beasiswa/apresiasi?program_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Buka seleksi kelas <?= esc($prog['nama_program']) ?>">
                                            Seleksi Kelas
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($isSelfTaruna && ketarunaan_is_program_beasiswa_open($prog)): ?>
                                        <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($prog['jenis_beasiswa']) . '?program_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Ajukan klaim <?= esc($prog['nama_program']) ?>">
                                            Ajukan
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($perms['can_manage'])): ?>
                                        <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . (int) $prog['id']) ?>" class="btn btn-light border btn-sm rounded-pill px-3" aria-label="Ubah program <?= esc($prog['nama_program']) ?>">
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

        <!-- Kartu Mobile Program Beasiswa -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($programs)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Tidak ada program yang sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($programs as $prog): ?>
                    <div class="border rounded-3 p-3 bg-white bea-mobile-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($prog['nama_program']) ?></div>
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                    <?= esc($prog['pemberi']) ?> &bull; <?= esc($prog['tahun_akademik_label'] ?: $prog['tahun']) ?>
                                </div>
                            </div>
                            <?= ketarunaan_program_beasiswa_status_badge((string) $prog['status_program']) ?>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <?= ketarunaan_jenis_beasiswa_badge((string) $prog['jenis_beasiswa']) ?>
                            <span class="ecc-num small">Kuota: <strong><?= (int) $prog['ditetapkan_count'] ?> / <?= (int) $prog['kuota'] ?></strong></span>
                        </div>
                        <div class="ecc-text-muted-aa small mb-2">
                            Nominal: <strong class="text-dark ecc-num"><?= esc(ketarunaan_rupiah($prog['nominal'] ?? 0)) ?></strong>
                            <?php if (($prog['jenis_beasiswa'] ?? '') === 'apresiasi'): ?>
                                &bull; Top <?= (int) ($prog['jumlah_peringkat'] ?? 5) ?> / kelas
                            <?php endif; ?>
                        </div>
                        <?php if (!$isSelfTaruna || ($prog['jenis_beasiswa'] ?? '') !== 'apresiasi'): ?>
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                <?php if (($prog['jenis_beasiswa'] ?? '') === 'apresiasi' && !$isSelfTaruna): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/apresiasi?program_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Seleksi Kelas</a>
                                <?php endif; ?>
                                <?php if ($isSelfTaruna && ketarunaan_is_program_beasiswa_open($prog)): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($prog['jenis_beasiswa']) . '?program_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Ajukan</a>
                                <?php endif; ?>
                                <?php if (!empty($perms['can_manage'])): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . (int) $prog['id']) ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Ubah</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<?= $this->endSection() ?>
