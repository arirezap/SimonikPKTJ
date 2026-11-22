<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Alumni<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/alumni/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$kpi        = $kpi ?? ['total_aktif' => 0, 'mengisi_pekerjaan' => 0, 'menunggu_kelulusan' => 0, 'antrean_koreksi' => 0];
$rows       = $rows ?? [];
$refs       = $refs ?? [];
$filters    = $filters ?? [];
$pagination = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_rows' => count($rows), 'total_pages' => 1];
$isSelfOnly = !empty($isSelfOnly);
$perms      = $perms ?? [];
$sumberMap  = $refs['sumber_konfirmasi'] ?? ketarunaan_alumni_sumber_konfirmasi_catalog();

$qVal          = trim((string) ($filters['q'] ?? ''));
$prodiVal      = (string) ($filters['prodi_id'] ?? 'all');
$angkatanVal   = (string) ($filters['angkatan_id'] ?? 'all');
$tahunLulusVal = (string) ($filters['tahun_lulus'] ?? 'all');
$statusVal     = (string) ($filters['status_profil'] ?? 'aktif');
$pekerjaanVal  = (string) ($filters['pekerjaan_status'] ?? 'all');
$currentSort   = (string) ($filters['sort'] ?? '');
$currentDir    = strtolower((string) ($filters['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

$hasActiveFilter = ($qVal !== '')
    || ($prodiVal !== '' && $prodiVal !== 'all')
    || ($angkatanVal !== '' && $angkatanVal !== 'all')
    || ($tahunLulusVal !== '' && $tahunLulusVal !== 'all')
    || ($statusVal !== '' && $statusVal !== 'aktif')
    || ($pekerjaanVal !== '' && $pekerjaanVal !== 'all');

$buildUrl = static function (array $overrides = []) use ($filters): string {
    $params = array_merge($filters, $overrides);
    $clean  = [];
    foreach ($params as $k => $v) {
        $sv = trim((string) $v);
        if ($sv === '' || $sv === 'all' || ($k === 'status_profil' && $sv === 'aktif') || ($k === 'page' && (int) $sv <= 1)) {
            continue;
        }
        $clean[$k] = $sv;
    }
    return base_url('ketarunaan/alumni') . (!empty($clean) ? ('?' . http_build_query($clean)) : '');
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
        '<th%s aria-sort="%s"><a href="%s" class="alm-sort-btn"><span>%s</span><i class="bi %s" aria-hidden="true"></i></a></th>',
        $extraClass !== '' ? (' class="' . esc($extraClass) . '"') : '',
        esc($ariaSort),
        esc($url),
        esc($label),
        esc($iconCls)
    );
};

$exportQuery = http_build_query([
    'q'                => $qVal,
    'prodi_id'         => $prodiVal,
    'angkatan_id'      => $angkatanVal,
    'tahun_lulus'      => $tahunLulusVal,
    'status_profil'    => $statusVal,
    'pekerjaan_status' => $pekerjaanVal,
]);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/alumni/_nav_tabs') ?>

    <!-- 4 Kartu Metrik Interaktif (Tanpa border-top 4px sesuai craft-floor.md) -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <?php if (!$isSelfOnly): ?>
                <a href="<?= esc(base_url('ketarunaan/alumni')) ?>"
                   class="ecc-alm-kpi <?= ($statusVal === 'aktif' && $pekerjaanVal === 'all') ? 'active-kpi' : '' ?>">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Alumni Aktif Tervalidasi</div>
                    <div class="fw-bold fs-4 text-primary ecc-num"><?= number_format((int) $kpi['total_aktif']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Hanya dari kelulusan sah</div>
                </a>
            <?php else: ?>
                <div class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Profil Dasar Alumni</div>
                    <div class="fw-bold fs-4 text-primary ecc-num"><?= number_format((int) $kpi['total_aktif']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Hanya dari kelulusan sah</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-6 col-xl-3">
            <?php if (!$isSelfOnly): ?>
                <a href="<?= esc($buildUrl(['status_profil' => 'aktif', 'pekerjaan_status' => 'terisi', 'page' => 1])) ?>"
                   class="ecc-alm-kpi <?= $pekerjaanVal === 'terisi' ? 'active-kpi' : '' ?>">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Data Kerja Terisi</div>
                    <div class="fw-bold fs-4 text-success-emphasis ecc-num"><?= number_format((int) $kpi['mengisi_pekerjaan']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pekerjaan dan instansi tercatat</div>
                </a>
            <?php else: ?>
                <div class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Data Kerja Terisi</div>
                    <div class="fw-bold fs-4 text-success-emphasis ecc-num"><?= number_format((int) $kpi['mengisi_pekerjaan']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pekerjaan dan instansi tercatat</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-6 col-xl-3">
            <?php if (!$isSelfOnly): ?>
                <a href="<?= base_url('ketarunaan/alumni/kelulusan') ?>" class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Menunggu Validasi Lulus</div>
                    <div class="fw-bold fs-4 text-warning-emphasis ecc-num"><?= number_format((int) $kpi['menunggu_kelulusan']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Belum menjadi profil alumni</div>
                </a>
            <?php else: ?>
                <div class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Akses Akun</div>
                    <div class="fw-bold fs-5 text-dark">Mandiri</div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pembaruan kontak milik sendiri</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-6 col-xl-3">
            <?php if (!$isSelfOnly): ?>
                <a href="<?= base_url('ketarunaan/alumni/koreksi') ?>" class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Usulan Koreksi Terbuka</div>
                    <div class="fw-bold fs-4 text-dark ecc-num"><?= number_format((int) $kpi['antrean_koreksi']) ?></div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Menunggu keputusan validator</div>
                </a>
            <?php else: ?>
                <div class="ecc-alm-kpi">
                    <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Layanan Mandiri</div>
                    <div class="fw-bold fs-5 text-dark">Profil Alumni</div>
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Data kelulusan dan kontak</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isSelfOnly): ?>
        <!-- Bar Filter Direktori Alumni -->
        <div class="ecc-alm-card mb-3 py-3">
            <form method="get" action="<?= base_url('ketarunaan/alumni') ?>" class="row g-2 align-items-end alm-filter-form">
                <?php if ($pekerjaanVal === 'terisi'): ?>
                    <input type="hidden" name="pekerjaan_status" value="terisi">
                <?php endif; ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="filter_q" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pencarian</label>
                    <input type="text" name="q" id="filter_q" class="form-control ecc-control-sm"
                           value="<?= esc($qVal) ?>" placeholder="Cari nama, Nomor Taruna, instansi, atau SK">
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
                <div class="col-6 col-md-3 col-lg-2">
                    <label for="filter_angkatan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Angkatan</label>
                    <select name="angkatan_id" id="filter_angkatan" class="form-select ecc-control-sm">
                        <option value="all">Semua Angkatan</option>
                        <?php foreach (($refs['angkatan'] ?? []) as $an): ?>
                            <option value="<?= (int) $an['id'] ?>" <?= $angkatanVal === (string) $an['id'] ? 'selected' : '' ?>>
                                <?= esc(ketarunaan_format_angkatan($an)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="filter_tahun_lulus" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tahun Lulus</label>
                    <select name="tahun_lulus" id="filter_tahun_lulus" class="form-select ecc-control-sm">
                        <option value="all">Semua Tahun</option>
                        <?php foreach (($refs['tahun_lulus'] ?? []) as $th): ?>
                            <option value="<?= (int) $th ?>" <?= $tahunLulusVal === (string) $th ? 'selected' : '' ?>>
                                <?= esc((string) $th) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="filter_status_profil" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Profil</label>
                    <select name="status_profil" id="filter_status_profil" class="form-select ecc-control-sm">
                        <option value="aktif" <?= $statusVal === 'aktif' ? 'selected' : '' ?>>Alumni Aktif</option>
                        <option value="nonaktif_koreksi" <?= $statusVal === 'nonaktif_koreksi' ? 'selected' : '' ?>>Nonaktif (Koreksi)</option>
                        <option value="all" <?= $statusVal === 'all' ? 'selected' : '' ?>>Semua Riwayat</option>
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center pt-2 gap-2">
                    <span class="ecc-text-muted-aa small">
                        Menampilkan <strong class="ecc-num text-dark"><?= (int) ($pagination['total_rows'] ?? count($rows)) ?></strong> profil alumni.
                    </span>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile alm-touch-ctrl text-nowrap">
                            Terapkan
                        </button>
                        <?php if ($hasActiveFilter): ?>
                            <a href="<?= base_url('ketarunaan/alumni') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile alm-touch-ctrl text-nowrap">
                                Reset
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('ketarunaan/alumni/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile alm-touch-ctrl text-nowrap">
                            <i class="bi bi-download" aria-hidden="true"></i> Ekspor CSV
                        </a>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tabel Desktop Direktori Alumni -->
    <div class="ecc-alm-card p-0 overflow-hidden d-none d-md-block">
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white">
            <h2 class="h6 fw-bold text-dark mb-0">
                <?= $isSelfOnly ? 'Profil Dasar Alumni Anda' : 'Daftar Profil Alumni' ?>
            </h2>
            <span class="ecc-text-muted-aa small">Terhubung langsung dengan Nomor Taruna asal</span>
        </div>
        <div class="table-responsive ecc-table-alm-wrap">
            <table class="table ecc-table-alm mb-0">
                <thead>
                    <tr>
                        <?= $isSelfOnly ? '<th>Alumni &amp; Nomor Taruna</th>' : $renderSortTh('nama_lengkap', 'Alumni & Nomor Taruna') ?>
                        <?= $isSelfOnly ? '<th>Prodi &amp; Angkatan</th>' : $renderSortTh('prodi', 'Prodi & Angkatan') ?>
                        <?= $isSelfOnly ? '<th>Kelulusan &amp; No. SK</th>' : $renderSortTh('tanggal_lulus', 'Kelulusan & No. SK') ?>
                        <?= $isSelfOnly ? '<th>Pekerjaan &amp; Instansi</th>' : $renderSortTh('pekerjaan', 'Pekerjaan & Instansi') ?>
                        <th>Kontak &amp; Konfirmasi</th>
                        <?= $isSelfOnly ? '<th>Status</th>' : $renderSortTh('status_profil', 'Status') ?>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="fw-semibold text-dark mb-1">Belum ada profil alumni yang sesuai dengan filter</div>
                                <div class="ecc-text-muted-aa small mb-3">
                                    <?= $hasActiveFilter
                                        ? 'Ubah atau reset filter pencarian untuk melihat seluruh data alumni.'
                                        : 'Profil alumni terbentuk setelah pengajuan kelulusan disetujui validator.' ?>
                                </div>
                                <div class="d-inline-flex flex-wrap justify-content-center gap-2">
                                    <?php if ($hasActiveFilter): ?>
                                        <a href="<?= base_url('ketarunaan/alumni') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Reset Filter</a>
                                    <?php endif; ?>
                                    <?php if (!empty($perms['can_view_staff'])): ?>
                                        <a href="<?= base_url('ketarunaan/alumni/kelulusan') ?>" class="btn btn-primary btn-sm rounded-pill px-3">Buka Antrean Kelulusan</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $nama     = (string) ($row['nama_lengkap'] ?? '-');
                            $words    = preg_split('/\s+/', trim($nama)) ?: [];
                            $initials = '';
                            foreach (array_slice($words, 0, 2) as $w) {
                                $initials .= strtoupper(mb_substr($w, 0, 1));
                            }
                            $sumberKode  = (string) ($row['sumber_konfirmasi'] ?? '');
                            $sumberLabel = $sumberMap[$sumberKode] ?? ($sumberKode !== '' ? $sumberKode : 'Belum dicatat');
                            $detUrl      = base_url('ketarunaan/alumni/detail/' . rawurlencode((string) $row['nomor_taruna']));
                            ?>
                            <tr class="alm-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Buka profil alumni <?= esc($nama) ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="ecc-avatar-40 bg-primary-subtle text-primary-emphasis"><?= esc($initials ?: 'AL') ?></div>
                                        <div>
                                            <div class="fw-semibold text-dark"><?= esc($nama) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">No. Taruna: <?= esc((string) $row['nomor_taruna']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) ($row['kode_prodi'] ?? '-')) ?></div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">Angkatan <?= esc(ketarunaan_format_angkatan($row['tahun_masuk'] ?? null)) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark ecc-num"><?= esc(ketarunaan_format_tanggal($row['tanggal_lulus'] ?? null)) ?></div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) ($row['nomor_keputusan_lulus'] ?? '-')) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) ($row['pekerjaan_saat_ini'] ?: '-')) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                        <?= esc((string) ($row['instansi_saat_ini'] ?: 'Instansi belum diisi')) ?>
                                        <?php if (!empty($row['kota_domisili'])): ?>
                                            &bull; <?= esc((string) $row['kota_domisili']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark ecc-num"><?= esc((string) ($row['telepon_alumni'] ?: ($row['kontak_terakhir'] ?: '-'))) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($sumberLabel) ?></div>
                                </td>
                                <td><?= ketarunaan_alumni_status_badge((string) ($row['status_profil'] ?? 'aktif')) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary ecc-btn-action btn-tactile" aria-label="Buka profil <?= esc($nama) ?>">
                                        Buka Profil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daftar Kartu Mobile Alumni -->
    <div class="d-md-none d-flex flex-column gap-3">
        <?php if (empty($rows)): ?>
            <div class="ecc-alm-card text-center py-4">
                <div class="fw-semibold text-dark mb-1">Belum ada profil alumni yang sesuai dengan filter</div>
                <div class="ecc-text-muted-aa small mb-3">
                    <?= $hasActiveFilter ? 'Reset filter untuk menampilkan seluruh alumni.' : 'Profil alumni terbentuk setelah kelulusan disetujui validator.' ?>
                </div>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <?php if ($hasActiveFilter): ?>
                        <a href="<?= base_url('ketarunaan/alumni') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 alm-touch-ctrl d-inline-flex align-items-center">Reset Filter</a>
                    <?php endif; ?>
                    <?php if (!empty($perms['can_view_staff'])): ?>
                        <a href="<?= base_url('ketarunaan/alumni/kelulusan') ?>" class="btn btn-primary btn-sm rounded-pill px-3 alm-touch-ctrl d-inline-flex align-items-center">Antrean Kelulusan</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <?php
                $nama   = (string) ($row['nama_lengkap'] ?? '-');
                $detUrl = base_url('ketarunaan/alumni/detail/' . rawurlencode((string) $row['nomor_taruna']));
                ?>
                <div class="ecc-alm-card p-3 alm-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Buka profil <?= esc($nama) ?>">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark"><?= esc($nama) ?></div>
                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                <?= esc((string) $row['nomor_taruna']) ?> &bull; <?= esc((string) ($row['kode_prodi'] ?? '-')) ?> &bull; Angkatan <?= esc(ketarunaan_format_angkatan($row['tahun_masuk'] ?? null)) ?>
                            </div>
                        </div>
                        <?= ketarunaan_alumni_status_badge((string) ($row['status_profil'] ?? 'aktif')) ?>
                    </div>
                    <div class="small text-dark mb-1">
                        <strong><?= esc((string) ($row['pekerjaan_saat_ini'] ?: 'Pekerjaan belum diisi')) ?></strong>
                        <?php if (!empty($row['instansi_saat_ini'])): ?>
                            &mdash; <?= esc((string) $row['instansi_saat_ini']) ?>
                        <?php endif; ?>
                    </div>
                    <div class="ecc-text-muted-aa ecc-num small mb-3">
                        Lulus: <?= esc(ketarunaan_format_tanggal($row['tanggal_lulus'] ?? null)) ?> &bull; <?= esc((string) ($row['telepon_alumni'] ?: ($row['kontak_terakhir'] ?: '-'))) ?>
                    </div>
                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm ecc-btn-action w-100 alm-touch-ctrl">
                        Buka Profil
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ((int) ($pagination['total_pages'] ?? 1) > 1): ?>
        <?php
        $curPage  = (int) $pagination['page'];
        $totPages = (int) $pagination['total_pages'];
        $totRows  = (int) $pagination['total_rows'];
        ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
            <div class="ecc-text-muted-aa small ecc-num">
                Halaman <?= $curPage ?> dari <?= $totPages ?> (total <?= $totRows ?> alumni)
            </div>
            <nav aria-label="Paginasi Direktori Alumni">
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
<?= $this->include('ketarunaan/alumni/_scripts_common') ?>
<?= $this->endSection() ?>
