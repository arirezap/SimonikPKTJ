<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Data Dosen<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/data_dosen/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters   = $filters ?? [];
$kpi       = $kpi ?? [];
$jafung    = $jafung ?? [];
$prodiD    = $prodiD ?? [];
$rows      = $rows ?? [];
$refs      = $refs ?? [];
$perms     = $perms ?? [];
$canManage = !empty($perms['can_manage']);

$jafCat    = $refs['jafung'] ?? ketarunaan_dosen_jafung_catalog();
$pendCat   = $refs['pendidikan'] ?? ketarunaan_dosen_pendidikan_catalog();
$serdCat   = $refs['serdos'] ?? ketarunaan_dosen_serdos_catalog();
$ikatCat   = $refs['ikatan'] ?? ketarunaan_dosen_ikatan_catalog();
$totalDsn  = max(1, (int) ($kpi['total_dosen'] ?? 0));

$hasFilter = (
    ($filters['q'] ?? '') !== ''
    || (int) ($filters['prodi_id'] ?? 0) > 0
    || ($filters['jabatan_fungsional'] ?? 'all') !== 'all'
    || ($filters['pendidikan_terakhir'] ?? 'all') !== 'all'
    || ($filters['status_serdos'] ?? 'all') !== 'all'
    || ($filters['status_ikatan'] ?? 'all') !== 'all'
    || ($filters['kesiapan'] ?? 'all') !== 'all'
);
$exportQuery = http_build_query($filters);
?>
<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none ecc-text-muted-aa">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Data Dosen</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Data Dosen</h1>
            <p class="ecc-text-muted-aa mb-0 small">
                Profil akademik, kepakaran, sertifikasi, dan kesiapan penugasan dosen untuk pertimbangan pengambilan keputusan pimpinan.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
            <a href="<?= base_url('ketarunaan/data-dosen/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile dsn-touch-ctrl">
                <i class="bi bi-download" aria-hidden="true"></i> Ekspor CSV
            </a>
            <?php if ($canManage): ?>
                <form method="post" action="<?= base_url('ketarunaan/data-dosen/sinkron') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile dsn-touch-ctrl">
                        Sinkron Pegawai
                    </button>
                </form>
                <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile dsn-touch-ctrl shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddDosen">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Dosen
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 4 Kartu KPI Eksekutif -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="ecc-dsn-kpi">
                <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Total Dosen Terdaftar</div>
                <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($kpi['total_dosen'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">Sinkron dengan data kepegawaian</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ecc-dsn-kpi">
                <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Siap Ditugaskan</div>
                <div class="fw-bold fs-3 text-success ecc-num"><?= (int) ($kpi['siap_ditugaskan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= (int) ($kpi['tugas_belajar'] ?? 0) ?> sedang tugas belajar</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ecc-dsn-kpi">
                <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Kualifikasi S3 / S2</div>
                <div class="fw-bold fs-3 text-primary ecc-num"><?= (int) ($kpi['s3_count'] ?? 0) ?> <span class="fs-6 fw-normal ecc-text-muted-aa">S3</span> / <?= (int) ($kpi['s2_count'] ?? 0) ?> <span class="fs-6 fw-normal ecc-text-muted-aa">S2</span></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pemetaan jenjang pendidikan</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="ecc-dsn-kpi">
                <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Tersertifikasi (Serdos)</div>
                <div class="fw-bold fs-3 text-dark ecc-num"><?= number_format((float) ($kpi['serdos_persen'] ?? 0), 1, ',', '.') ?>%</div>
                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= (int) ($kpi['serdos_sudah'] ?? 0) ?> dari <?= (int) ($kpi['total_dosen'] ?? 0) ?> dosen</div>
            </div>
        </div>
    </div>

    <!-- Sebaran Jabatan Fungsional & Homebase Prodi -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="ecc-dsn-card h-100">
                <h2 class="h6 fw-bold text-dark mb-3">Sebaran Jabatan Fungsional</h2>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($jafung as $jf): ?>
                        <?php $pct = round(((int) $jf['count'] / $totalDsn) * 100, 1); ?>
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium text-dark"><?= esc((string) $jf['label']) ?></span>
                                <span class="ecc-num fw-semibold"><?= (int) $jf['count'] ?> (<?= number_format($pct, 0) ?>%)</span>
                            </div>
                            <div class="dsn-bar-track">
                                <div class="dsn-bar-fill" style="width: <?= min(100, $pct) ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="ecc-dsn-card h-100">
                <h2 class="h6 fw-bold text-dark mb-3">Sebaran Homebase Program Studi</h2>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($prodiD as $pd): ?>
                        <?php $pct = round(((int) $pd['count'] / $totalDsn) * 100, 1); ?>
                        <div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-medium text-dark"><?= esc((string) $pd['kode']) ?></span>
                                <span class="ecc-num fw-semibold"><?= (int) $pd['count'] ?> dosen (<?= number_format($pct, 0) ?>%)</span>
                            </div>
                            <div class="dsn-bar-track">
                                <div class="dsn-bar-fill" style="width: <?= min(100, $pct) ?>%; background-color: #059669;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pencarian Kandidat Keputusan Pimpinan -->
    <div class="ecc-dsn-card mb-4 py-3">
        <form method="get" action="<?= base_url('ketarunaan/data-dosen') ?>" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label for="dsnQ" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Nama, NIP, NIDN, atau Kepakaran</label>
                <input type="text" id="dsnQ" name="q" class="form-control ecc-control-sm" value="<?= esc((string) ($filters['q'] ?? '')) ?>" placeholder="Contoh: keselamatan jalan, otomotif, nama...">
            </div>
            <div class="col-6 col-md-2">
                <label for="dsnProdi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Homebase Prodi</label>
                <select id="dsnProdi" name="prodi_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="0">Semua Prodi</option>
                    <?php foreach (($refs['prodi'] ?? []) as $pr): ?>
                        <option value="<?= (int) $pr['id'] ?>" <?= (int) ($filters['prodi_id'] ?? 0) === (int) $pr['id'] ? 'selected' : '' ?>>
                            <?= esc($pr['kode_prodi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="dsnJafung" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Jabatan Fungsional</label>
                <select id="dsnJafung" name="jabatan_fungsional" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jafung</option>
                    <?php foreach ($jafCat as $jk => $jm): ?>
                        <option value="<?= esc($jk) ?>" <?= ($filters['jabatan_fungsional'] ?? 'all') === $jk ? 'selected' : '' ?>><?= esc($jm['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="dsnPend" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pendidikan</label>
                <select id="dsnPend" name="pendidikan_terakhir" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenjang</option>
                    <?php foreach ($pendCat as $pk => $pl): ?>
                        <option value="<?= esc($pk) ?>" <?= ($filters['pendidikan_terakhir'] ?? 'all') === $pk ? 'selected' : '' ?>><?= esc($pl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="dsnSerdos" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Serdos</label>
                <select id="dsnSerdos" name="status_serdos" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua</option>
                    <?php foreach ($serdCat as $sk => $sm): ?>
                        <option value="<?= esc($sk) ?>" <?= ($filters['status_serdos'] ?? 'all') === $sk ? 'selected' : '' ?>><?= esc($sm['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="dsnKesiapan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Kesiapan Penugasan</label>
                <select id="dsnKesiapan" name="kesiapan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status</option>
                    <option value="siap" <?= ($filters['kesiapan'] ?? 'all') === 'siap' ? 'selected' : '' ?>>Siap Ditugaskan</option>
                    <option value="tidak_siap" <?= ($filters['kesiapan'] ?? 'all') === 'tidak_siap' ? 'selected' : '' ?>>Belum Siap / Tugas Belajar</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="dsnIkatan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Ikatan</label>
                <select id="dsnIkatan" name="status_ikatan" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Ikatan</option>
                    <?php foreach ($ikatCat as $ik => $im): ?>
                        <option value="<?= esc($ik) ?>" <?= ($filters['status_ikatan'] ?? 'all') === $ik ? 'selected' : '' ?>><?= esc($im['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 d-flex flex-wrap justify-content-md-end gap-2">
                <?php if ($hasFilter): ?>
                    <a href="<?= base_url('ketarunaan/data-dosen') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center dsn-touch-ctrl">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold dsn-touch-ctrl">Terapkan Filter</button>
            </div>
        </form>
    </div>

    <!-- Tabel Direktori Dosen -->
    <div class="ecc-dsn-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h6 fw-bold text-dark mb-0">Daftar Profil Dosen</h2>
            <span class="ecc-text-muted-aa small ecc-num">Menampilkan <?= count($rows) ?> dosen</span>
        </div>

        <div class="table-responsive ecc-table-dsn-wrap d-none d-md-block">
            <table class="table ecc-table-dsn mb-0">
                <thead>
                    <tr>
                        <th>Dosen &amp; Identitas</th>
                        <th>Homebase &amp; Unit</th>
                        <th>Jafung &amp; Pendidikan</th>
                        <th>Bidang &amp; Kepakaran</th>
                        <th>Kesiapan &amp; Serdos</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="6" class="text-center ecc-text-muted-aa py-4">Tidak ada dosen yang sesuai kriteria filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $did      = (int) $r['id'];
                            $jfMeta   = $jafCat[$r['jabatan_fungsional'] ?? ''] ?? ['label' => $r['jabatan_fungsional'], 'badge_class' => 'bg-light text-dark border'];
                            $sdMeta   = $serdCat[$r['status_serdos'] ?? ''] ?? ['label' => $r['status_serdos'], 'badge_class' => 'bg-light text-dark border'];
                            $isSiap   = ((int) ($r['is_siap_ditugaskan'] ?? 0) === 1);
                            $fotoFile = !empty($r['foto']) && file_exists(FCPATH . 'assets/uploads/profile/' . $r['foto']) ? base_url('assets/uploads/profile/' . $r['foto']) : null;
                            $initials = strtoupper(substr(trim((string) $r['nama_lengkap']), 0, 1));
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="dsn-avatar-sm">
                                            <?php if ($fotoFile): ?>
                                                <img src="<?= esc($fotoFile) ?>" alt="<?= esc((string) $r['nama_lengkap']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <?= esc($initials) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('ketarunaan/data-dosen/detail/' . $did) ?>" class="fw-semibold text-dark text-decoration-none">
                                                <?= esc((string) $r['nama_lengkap']) ?>
                                            </a>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                                NIP: <?= esc((string) ($r['nip'] ?: '-')) ?>
                                                <?php if (!empty($r['nidn'])): ?>
                                                    &bull; NIDN: <?= esc((string) $r['nidn']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) ($r['kode_prodi'] ?: '-')) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) ($r['unit'] ?: '-')) ?></div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-2 py-1 fw-semibold <?= esc($jfMeta['badge_class']) ?>" style="font-size: 0.72rem;"><?= esc($jfMeta['label']) ?></span>
                                    <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">
                                        <?= esc($pendCat[$r['pendidikan_terakhir'] ?? ''] ?? $r['pendidikan_terakhir'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="max-width: 240px;">
                                    <div class="small text-dark fw-medium"><?= esc((string) ($r['bidang_keilmuan'] ?: '-')) ?></div>
                                    <?php if (!empty($r['kepakaran_list'])): ?>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <?php foreach (array_slice($r['kepakaran_list'], 0, 3) as $tg): ?>
                                                <span class="dsn-tag"><?= esc($tg) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge rounded-pill px-2 py-1 fw-semibold <?= $isSiap ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?>" style="font-size: 0.72rem;">
                                            <?= $isSiap ? 'Siap Ditugaskan' : (($r['status_ikatan'] ?? '') === 'tugas_belajar' ? 'Tugas Belajar' : 'Belum Siap') ?>
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge rounded-pill px-2 py-1 fw-semibold <?= esc($sdMeta['badge_class']) ?>" style="font-size: 0.7rem;"><?= esc($sdMeta['label']) ?></span>
                                    </div>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= base_url('ketarunaan/data-dosen/detail/' . $did) ?>" class="btn btn-outline-primary ecc-btn-action">
                                        Profil Eksekutif
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rows)): ?>
                <div class="text-center ecc-text-muted-aa py-4 small">Tidak ada dosen yang sesuai kriteria filter.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $did    = (int) $r['id'];
                    $jfMeta = $jafCat[$r['jabatan_fungsional'] ?? ''] ?? ['label' => $r['jabatan_fungsional'], 'badge_class' => 'bg-light text-dark border'];
                    $isSiap = ((int) ($r['is_siap_ditugaskan'] ?? 0) === 1);
                    ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc((string) $r['nama_lengkap']) ?></div>
                                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                    <?= esc((string) ($r['kode_prodi'] ?: $r['unit'] ?: '-')) ?> &bull; NIP <?= esc((string) ($r['nip'] ?: '-')) ?>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-2 py-1 fw-semibold <?= esc($jfMeta['badge_class']) ?>" style="font-size: 0.72rem;"><?= esc($jfMeta['label']) ?></span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.72rem;"><?= esc($pendCat[$r['pendidikan_terakhir'] ?? ''] ?? '-') ?></span>
                            <span class="badge rounded-pill px-2 py-1 fw-semibold <?= $isSiap ? 'bg-success-subtle text-success-emphasis' : 'bg-warning-subtle text-warning-emphasis' ?>" style="font-size: 0.72rem;">
                                <?= $isSiap ? 'Siap Ditugaskan' : 'Belum Siap' ?>
                            </span>
                        </div>
                        <a href="<?= base_url('ketarunaan/data-dosen/detail/' . $did) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 w-100 dsn-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            Buka Profil Eksekutif
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($canManage): ?>
<!-- Modal Tambah Dosen dari Pegawai -->
<div class="modal fade" id="modalAddDosen" tabindex="-1" aria-labelledby="modalAddDosenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="<?= base_url('ketarunaan/data-dosen/simpan') ?>" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="dosen_profil_id" value="0">

            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h5 fw-bold text-dark mb-0" id="modalAddDosenLabel">Tambah Data Dosen</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="addDosenUser" class="form-label fw-semibold small">Pilih Pegawai (Sinkron Data Kepegawaian) <span class="text-danger">*</span></label>
                        <select id="addDosenUser" name="user_id" class="form-select ecc-control-sm js-dsn-select2" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach (($refs['candidates'] ?? []) as $cd): ?>
                                <option value="<?= (int) $cd['id'] ?>">
                                    <?= esc($cd['nama_lengkap'] . ' (' . ($cd['nip'] ?: '-') . ' - ' . ($cd['jabatan'] ?: '-') . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="addNidn" class="form-label fw-semibold small">NIDN</label>
                        <input type="text" id="addNidn" name="nidn" class="form-control ecc-control-sm ecc-num" maxlength="20">
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="addNuptk" class="form-label fw-semibold small">NUPTK</label>
                        <input type="text" id="addNuptk" name="nuptk" class="form-control ecc-control-sm ecc-num" maxlength="24">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="addHomebase" class="form-label fw-semibold small">Homebase Prodi</label>
                        <select id="addHomebase" name="homebase_prodi_id" class="form-select ecc-control-sm">
                            <option value="0">-- Non-Homebase / Unit --</option>
                            <?php foreach (($refs['prodi'] ?? []) as $pr): ?>
                                <option value="<?= (int) $pr['id'] ?>"><?= esc($pr['kode_prodi'] . ' - ' . $pr['nama_prodi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="addJafung" class="form-label fw-semibold small">Jabatan Fungsional</label>
                        <select id="addJafung" name="jabatan_fungsional" class="form-select ecc-control-sm">
                            <?php foreach ($jafCat as $jk => $jm): ?>
                                <option value="<?= esc($jk) ?>"><?= esc($jm['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="addPend" class="form-label fw-semibold small">Pendidikan Terakhir</label>
                        <select id="addPend" name="pendidikan_terakhir" class="form-select ecc-control-sm">
                            <?php foreach ($pendCat as $pk => $pl): ?>
                                <option value="<?= esc($pk) ?>" <?= $pk === 'S2' ? 'selected' : '' ?>><?= esc($pl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="addSerdos" class="form-label fw-semibold small">Status Serdos</label>
                        <select id="addSerdos" name="status_serdos" class="form-select ecc-control-sm">
                            <?php foreach ($serdCat as $sk => $sm): ?>
                                <option value="<?= esc($sk) ?>"><?= esc($sm['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="addBidang" class="form-label fw-semibold small">Bidang Keilmuan</label>
                        <input type="text" id="addBidang" name="bidang_keilmuan" class="form-control ecc-control-sm" placeholder="Contoh: Rekayasa Transportasi Jalan">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="addIkatan" class="form-label fw-semibold small">Status Ikatan</label>
                        <select id="addIkatan" name="status_ikatan" class="form-select ecc-control-sm">
                            <?php foreach ($ikatCat as $ik => $im): ?>
                                <option value="<?= esc($ik) ?>"><?= esc($im['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="addTags" class="form-label fw-semibold small">Kata Kunci Kepakaran (Pisahkan dengan koma)</label>
                        <input type="text" id="addTags" name="kepakaran_tags" class="form-control ecc-control-sm" placeholder="Contoh: keselamatan jalan, audit lalu lintas, kendaraan listrik">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="addSiap" name="is_siap_ditugaskan" value="1" checked>
                            <label class="form-check-label small fw-semibold" for="addSiap">Siap ditugaskan (pembimbing, penguji, atau tim kerja)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">Simpan Data Dosen</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.js-dsn-select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: jQuery('#modalAddDosen')
        });
    }
});
</script>
<?= $this->endSection() ?>
