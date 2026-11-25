<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Profil Dosen<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/data_dosen/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail       = $detail ?? [];
$dosen        = $detail['dosen'] ?? [];
$kualifikasi  = $detail['kualifikasi'] ?? [];
$kinerja      = $detail['kinerja_summary'] ?? null;
$checks       = $detail['readiness_checks'] ?? [];
$refs         = $refs ?? [];
$perms        = $perms ?? [];
$isOwn        = !empty($isOwn);
$canViewDir   = !empty($perms['can_view_directory']);
$canManage    = !empty($perms['can_manage']);
$canViewExec  = !empty($perms['can_view_exec']);
$canEditExec  = !empty($perms['can_edit_exec']);
$canEditSelf  = ($canManage || $isOwn);

$did          = (int) ($dosen['id'] ?? 0);
$jafCat       = $refs['jafung'] ?? ketarunaan_dosen_jafung_catalog();
$pendCat      = $refs['pendidikan'] ?? ketarunaan_dosen_pendidikan_catalog();
$serdCat      = $refs['serdos'] ?? ketarunaan_dosen_serdos_catalog();
$ikatCat      = $refs['ikatan'] ?? ketarunaan_dosen_ikatan_catalog();
$kualCat      = $refs['kualifikasi'] ?? ketarunaan_dosen_kualifikasi_catalog();

$jfMeta       = $jafCat[$dosen['jabatan_fungsional'] ?? ''] ?? ['label' => $dosen['jabatan_fungsional'] ?? '-', 'badge_class' => 'bg-light text-dark border'];
$sdMeta       = $serdCat[$dosen['status_serdos'] ?? ''] ?? ['label' => $dosen['status_serdos'] ?? '-', 'badge_class' => 'bg-light text-dark border'];
$ikMeta       = $ikatCat[$dosen['status_ikatan'] ?? ''] ?? ['label' => $dosen['status_ikatan'] ?? '-', 'badge_class' => 'bg-light text-dark border'];
$isSiap       = ((int) ($dosen['is_siap_ditugaskan'] ?? 0) === 1);
$fotoFile     = !empty($dosen['foto']) && file_exists(FCPATH . 'assets/uploads/profile/' . $dosen['foto']) ? base_url('assets/uploads/profile/' . $dosen['foto']) : null;
$initials     = strtoupper(substr(trim((string) ($dosen['nama_lengkap'] ?? 'D')), 0, 1));
?>
<div class="container-fluid px-0">
    <?php if ($canViewDir): ?>
        <div class="mb-3">
            <a href="<?= base_url('ketarunaan/data-dosen') ?>" class="btn btn-light border btn-sm rounded-pill px-3 dsn-touch-ctrl d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Direktori Dosen
            </a>
        </div>
    <?php endif; ?>

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

    <!-- Kartu Header Profil Eksekutif -->
    <div class="ecc-dsn-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="dsn-avatar-lg">
                    <?php if ($fotoFile): ?>
                        <img src="<?= esc($fotoFile) ?>" alt="<?= esc((string) $dosen['nama_lengkap']) ?>" width="64" height="64" decoding="async" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= esc($initials) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 class="h4 fw-bold text-dark mb-1"><?= esc((string) ($dosen['nama_lengkap'] ?? '')) ?></h1>
                    <div class="ecc-text-muted-aa small ecc-num mb-2">
                        NIP: <strong><?= esc((string) ($dosen['nip'] ?: '-')) ?></strong>
                        &bull; NIDN: <strong><?= esc((string) ($dosen['nidn'] ?: '-')) ?></strong>
                        &bull; Homebase: <strong><?= esc((string) ($dosen['kode_prodi'] ?: $dosen['unit'] ?: '-')) ?></strong>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($jfMeta['badge_class']) ?>" style="font-size: 0.75rem;"><?= esc($jfMeta['label']) ?></span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold bg-light text-dark border" style="font-size: 0.75rem;"><?= esc($pendCat[$dosen['pendidikan_terakhir'] ?? ''] ?? '-') ?></span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($sdMeta['badge_class']) ?>" style="font-size: 0.75rem;"><?= esc($sdMeta['label']) ?></span>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= $isSiap ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' ?>" style="font-size: 0.75rem;">
                            <?= $isSiap ? 'Siap Ditugaskan' : esc($ikMeta['label']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Indikator Objektif Pertimbangan Penugasan -->
        <div class="row g-2 mt-3 pt-3 border-top">
            <?php foreach ($checks as $ck): ?>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="dsn-check-tile">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small fw-semibold text-dark"><?= esc((string) $ck['label']) ?></span>
                            <span class="badge rounded-pill px-2 py-1 <?= !empty($ck['ok']) ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' ?>" style="font-size: 0.7rem;">
                                <?= !empty($ck['ok']) ? 'Memenuhi' : 'Perlu Cek' ?>
                            </span>
                        </div>
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) $ck['desc']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3 Tab Profil Eksekutif -->
    <div class="ecc-dsn-card p-0 overflow-hidden">
        <ul class="nav nav-tabs dsn-tabs px-3 pt-2 bg-white border-bottom" id="dosenDetailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active small" id="tab-profil-btn" data-bs-toggle="tab" data-bs-target="#tab-profil" type="button" role="tab">
                    Profil Akademik &amp; Kepakaran
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small" id="tab-kualifikasi-btn" data-bs-toggle="tab" data-bs-target="#tab-kualifikasi" type="button" role="tab">
                    Riwayat Pendidikan, Sertifikasi &amp; Penugasan (<?= count($kualifikasi) ?>)
                </button>
            </li>
            <?php if ($canViewExec): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link small" id="tab-eksekutif-btn" data-bs-toggle="tab" data-bs-target="#tab-eksekutif" type="button" role="tab">
                        Kinerja ECC &amp; Catatan Pimpinan
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-3 p-md-4 bg-white">
            <!-- TAB 1: PROFIL AKADEMIK & KEPAKARAN -->
            <div class="tab-pane fade show active" id="tab-profil" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-lg-5">
                        <div class="p-3 rounded-4 bg-light border h-100">
                            <h2 class="h6 fw-bold text-dark mb-3">Data Kepegawaian (Sinkron ECC)</h2>
                            <dl class="row small mb-0">
                                <dt class="col-5 ecc-text-muted-aa fw-medium">Pangkat / Gol.</dt>
                                <dd class="col-7 fw-semibold text-dark"><?= esc((string) ($dosen['pangkat'] ?: '-')) ?></dd>
                                <dt class="col-5 ecc-text-muted-aa fw-medium">Jabatan Saat Ini</dt>
                                <dd class="col-7 fw-semibold text-dark"><?= esc((string) ($dosen['jabatan'] ?: '-')) ?></dd>
                                <dt class="col-5 ecc-text-muted-aa fw-medium">Unit Kerja</dt>
                                <dd class="col-7 fw-semibold text-dark"><?= esc((string) ($dosen['unit'] ?: '-')) ?></dd>
                                <dt class="col-5 ecc-text-muted-aa fw-medium">Status Pegawai</dt>
                                <dd class="col-7 fw-semibold text-dark text-capitalize"><?= esc(str_replace('_', ' ', (string) ($dosen['status_pegawai'] ?: 'aktif'))) ?></dd>
                                <dt class="col-5 ecc-text-muted-aa fw-medium">Alamat Email</dt>
                                <dd class="col-7 text-dark"><?= esc((string) ($dosen['email'] ?: '-')) ?></dd>
                                <dt class="col-5 ecc-text-muted-aa fw-medium">No. HP</dt>
                                <dd class="col-7 text-dark ecc-num"><?= esc((string) ($dosen['no_hp'] ?: '-')) ?></dd>
                            </dl>

                            <?php if (!empty($dosen['kepakaran_list'])): ?>
                                <hr class="my-3">
                                <div class="small fw-semibold text-dark mb-2">Kata Kunci Kepakaran</div>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($dosen['kepakaran_list'] as $tg): ?>
                                        <span class="dsn-tag"><?= esc($tg) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($dosen['google_scholar_url'])): ?>
                                <?php $safeScholar = ketarunaan_sanitize_external_url((string) $dosen['google_scholar_url'], false); ?>
                                <?php if ($safeScholar): ?>
                                    <div class="mt-3 pt-2 border-top">
                                        <a href="<?= esc($safeScholar) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            Buka Google Scholar
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-lg-7">
                        <h2 class="h6 fw-bold text-dark mb-3">
                            <?= $canManage ? 'Kelola Profil Akademik & Kepakaran' : ($isOwn ? 'Perbarui Profil Akademik & Kepakaran Saya' : 'Rincian Profil Akademik') ?>
                        </h2>
                        <form method="post" action="<?= base_url('ketarunaan/data-dosen/simpan') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="dosen_profil_id" value="<?= $did ?>">

                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <label for="edNidn" class="form-label small fw-semibold">NIDN</label>
                                    <input type="text" id="edNidn" name="nidn" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) ($dosen['nidn'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edNuptk" class="form-label small fw-semibold">NUPTK</label>
                                    <input type="text" id="edNuptk" name="nuptk" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) ($dosen['nuptk'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="edHomebase" class="form-label small fw-semibold">Homebase Prodi</label>
                                    <select id="edHomebase" name="homebase_prodi_id" class="form-select ecc-control-sm" <?= !$canManage ? 'disabled' : '' ?>>
                                        <option value="0">-- Non-Homebase / Unit --</option>
                                        <?php foreach (($refs['prodi'] ?? []) as $pr): ?>
                                            <option value="<?= (int) $pr['id'] ?>" <?= (int) ($dosen['homebase_prodi_id'] ?? 0) === (int) $pr['id'] ? 'selected' : '' ?>>
                                                <?= esc($pr['kode_prodi'] . ' - ' . $pr['nama_prodi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edJafung" class="form-label small fw-semibold">Jabatan Fungsional</label>
                                    <select id="edJafung" name="jabatan_fungsional" class="form-select ecc-control-sm" <?= !$canManage ? 'disabled' : '' ?>>
                                        <?php foreach ($jafCat as $jk => $jm): ?>
                                            <option value="<?= esc($jk) ?>" <?= ($dosen['jabatan_fungsional'] ?? '') === $jk ? 'selected' : '' ?>><?= esc($jm['label']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edTmt" class="form-label small fw-semibold">TMT Jafung</label>
                                    <input type="text" id="edTmt" name="tmt_jabatan_fungsional" class="form-control ecc-control-sm bg-white js-dsn-date ecc-num" value="<?= esc((string) ($dosen['tmt_jabatan_fungsional'] ?? '')) ?>" <?= !$canManage ? 'disabled' : '' ?> placeholder="YYYY-MM-DD">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edPend" class="form-label small fw-semibold">Pendidikan Terakhir</label>
                                    <select id="edPend" name="pendidikan_terakhir" class="form-select ecc-control-sm" <?= !$canManage ? 'disabled' : '' ?>>
                                        <?php foreach ($pendCat as $pk => $pl): ?>
                                            <option value="<?= esc($pk) ?>" <?= ($dosen['pendidikan_terakhir'] ?? '') === $pk ? 'selected' : '' ?>><?= esc($pl) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edSerdos" class="form-label small fw-semibold">Status Serdos</label>
                                    <select id="edSerdos" name="status_serdos" class="form-select ecc-control-sm" <?= !$canManage ? 'disabled' : '' ?>>
                                        <?php foreach ($serdCat as $sk => $sm): ?>
                                            <option value="<?= esc($sk) ?>" <?= ($dosen['status_serdos'] ?? '') === $sk ? 'selected' : '' ?>><?= esc($sm['label']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edThnSerdos" class="form-label small fw-semibold">Tahun Serdos</label>
                                    <input type="number" id="edThnSerdos" name="tahun_serdos" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) ($dosen['tahun_serdos'] ?? '')) ?>" <?= !$canManage ? 'disabled' : '' ?>>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edIkatan" class="form-label small fw-semibold">Status Ikatan</label>
                                    <select id="edIkatan" name="status_ikatan" class="form-select ecc-control-sm" <?= !$canManage ? 'disabled' : '' ?>>
                                        <?php foreach ($ikatCat as $ik => $im): ?>
                                            <option value="<?= esc($ik) ?>" <?= ($dosen['status_ikatan'] ?? '') === $ik ? 'selected' : '' ?>><?= esc($im['label']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="edBidang" class="form-label small fw-semibold">Bidang Keilmuan Utama</label>
                                    <input type="text" id="edBidang" name="bidang_keilmuan" class="form-control ecc-control-sm" value="<?= esc((string) ($dosen['bidang_keilmuan'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-12">
                                    <label for="edTags" class="form-label small fw-semibold">Kata Kunci Kepakaran (Pisahkan dengan koma)</label>
                                    <input type="text" id="edTags" name="kepakaran_tags" class="form-control ecc-control-sm" value="<?= esc((string) ($dosen['kepakaran_tags'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?> placeholder="Contoh: keselamatan jalan, audit lalu lintas, pengujian kendaraan">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edSinta" class="form-label small fw-semibold">SINTA ID</label>
                                    <input type="text" id="edSinta" name="sinta_id" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) ($dosen['sinta_id'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label for="edScopus" class="form-label small fw-semibold">Scopus ID</label>
                                    <input type="text" id="edScopus" name="scopus_id" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) ($dosen['scopus_id'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?>>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="edScholar" class="form-label small fw-semibold">Tautan Google Scholar</label>
                                    <input type="url" id="edScholar" name="google_scholar_url" class="form-control ecc-control-sm" value="<?= esc((string) ($dosen['google_scholar_url'] ?? '')) ?>" <?= !$canEditSelf ? 'readonly' : '' ?> placeholder="https://scholar.google.com/...">
                                </div>
                                <?php if ($canManage): ?>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="edSiap" name="is_siap_ditugaskan" value="1" <?= $isSiap ? 'checked' : '' ?>>
                                            <label class="form-check-label small fw-semibold" for="edSiap">Siap ditugaskan untuk kegiatan akademik, pembimbingan, atau tim strategis</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($canEditSelf): ?>
                                <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">
                                        Simpan Profil Akademik
                                    </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TAB 2: RIWAYAT PENDIDIKAN, SERTIFIKASI & PENUGASAN -->
            <div class="tab-pane fade" id="tab-kualifikasi" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h6 fw-bold text-dark mb-0">Riwayat Pendidikan, Sertifikasi &amp; Penugasan Strategis</h2>
                        <div class="ecc-text-muted-aa small">Rekam jejak kualifikasi teknis dan penugasan penting sebagai bahan pertimbangan pimpinan.</div>
                    </div>
                    <?php if ($canEditSelf): ?>
                        <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile" data-bs-toggle="modal" data-bs-target="#modalKualifikasiDosen" onclick="resetKualifikasiModal()">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> <?= $canManage ? 'Tambah Riwayat' : 'Usulkan Riwayat' ?>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (empty($kualifikasi)): ?>
                    <div class="text-center py-4 ecc-text-muted-aa small">Belum ada riwayat pendidikan, sertifikasi, atau penugasan yang tercatat.</div>
                <?php else: ?>
                    <div class="table-responsive ecc-table-dsn-wrap">
                        <table class="table ecc-table-dsn mb-0">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Judul &amp; Institusi</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kualifikasi as $k): ?>
                                    <?php
                                    $kid     = (int) $k['id'];
                                    $safeUrl = !empty($k['tautan_bukti']) ? ketarunaan_sanitize_external_url((string) $k['tautan_bukti'], false) : null;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                <?= esc($kualCat[$k['kategori'] ?? ''] ?? $k['kategori']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc((string) $k['judul']) ?></div>
                                            <div class="ecc-text-muted-aa small">
                                                <?= esc((string) ($k['institusi_penyelenggara'] ?: '-')) ?>
                                                <?php if (!empty($k['nomor_dokumen'])): ?>
                                                    &bull; No: <?= esc((string) $k['nomor_dokumen']) ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($k['keterangan'])): ?>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) $k['keterangan']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="ecc-num small text-nowrap">
                                            <?= esc((string) ($k['tahun_mulai'] ?: '-')) ?>
                                            <?= !empty($k['tahun_selesai']) && $k['tahun_selesai'] != $k['tahun_mulai'] ? (' - ' . esc((string) $k['tahun_selesai'])) : '' ?>
                                        </td>
                                        <td>
                                            <?php if (($k['status_verifikasi'] ?? '') === 'menunggu'): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">Menunggu Verifikasi</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">Terverifikasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($safeUrl): ?>
                                                <a href="<?= esc($safeUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light border ecc-btn-action">Bukti</a>
                                            <?php endif; ?>
                                            <?php if ($canManage && ($k['status_verifikasi'] ?? '') === 'menunggu'): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/data-dosen/detail/' . $did . '/kualifikasi/' . $kid . '/verifikasi') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-success ecc-btn-action">Verifikasi</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($canManage): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/data-dosen/detail/' . $did . '/kualifikasi/' . $kid . '/hapus') ?>" class="d-inline js-dsn-del-kual">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-danger ecc-btn-action">Hapus</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 3: KINERJA ECC & CATATAN PIMPINAN (KHUSUS PIMPINAN / KEPEGAWAIAN / ADMIN) -->
            <?php if ($canViewExec): ?>
                <div class="tab-pane fade" id="tab-eksekutif" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12 col-lg-5">
                            <div class="p-3 rounded-4 bg-light border h-100">
                                <h2 class="h6 fw-bold text-dark mb-2">Ringkasan Kinerja ECC <?= (int) ($kinerja['tahun'] ?? date('Y')) ?></h2>
                                <p class="ecc-text-muted-aa small mb-3">
                                    Data terintegrasi otomatis dari modul Penilaian Kinerja ECC (hanya nilai berstatus terbit).
                                </p>
                                <?php if (!empty($kinerja) && $kinerja['rata_capaian'] !== null): ?>
                                    <div class="d-flex align-items-baseline gap-2 mb-2">
                                        <span class="fs-2 fw-bold text-primary ecc-num"><?= number_format((float) $kinerja['rata_capaian'], 2, ',', '.') ?>%</span>
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc((string) ($kinerja['predikat']['badge_class'] ?? 'bg-primary-subtle text-primary')) ?>">
                                            <?= esc((string) ($kinerja['predikat']['label'] ?? '-')) ?>
                                        </span>
                                    </div>
                                    <div class="ecc-text-muted-aa small ecc-num">
                                        Berdasarkan <?= (int) ($kinerja['bulan_dinilai'] ?? 0) ?> periode penilaian terbit tahun <?= (int) ($kinerja['tahun'] ?? date('Y')) ?>.
                                    </div>
                                <?php else: ?>
                                    <div class="ecc-text-muted-aa small">Belum ada nilai kinerja terbit pada tahun berjalan.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 col-lg-7">
                            <h2 class="h6 fw-bold text-dark mb-2">Catatan Pertimbangan Pimpinan</h2>
                            <p class="ecc-text-muted-aa small mb-3">
                                Catatan internal khusus pimpinan untuk pertimbangan penugasan strategis (tidak ditampilkan kepada taruna, alumni, maupun staf umum).
                            </p>
                            <?php if ($canEditExec): ?>
                                <form method="post" action="<?= base_url('ketarunaan/data-dosen/detail/' . $did . '/catatan-pimpinan') ?>">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <textarea name="catatan_pimpinan" class="form-control" rows="4" placeholder="Contoh: Direkomendasikan sebagai pembimbing lomba keselamatan jalan / ketua tim akreditasi prodi..."><?= esc((string) ($dosen['catatan_pimpinan'] ?? '')) ?></textarea>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="execSiap" name="is_siap_ditugaskan" value="1" <?= $isSiap ? 'checked' : '' ?>>
                                        <label class="form-check-label small fw-semibold" for="execSiap">Tandai dosen siap ditugaskan</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">
                                        Simpan Catatan Pimpinan
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="p-3 rounded-3 bg-light border small text-dark">
                                    <?= !empty($dosen['catatan_pimpinan']) ? nl2br(esc((string) $dosen['catatan_pimpinan'])) : '<span class="ecc-text-muted-aa">Belum ada catatan pimpinan.</span>' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($canEditSelf): ?>
<!-- Modal Tambah Riwayat Kualifikasi -->
<div class="modal fade" id="modalKualifikasiDosen" tabindex="-1" aria-labelledby="modalKualifikasiDosenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="<?= base_url('ketarunaan/data-dosen/detail/' . $did . '/kualifikasi/simpan') ?>" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="kualifikasi_id" value="0">

            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h5 fw-bold text-dark mb-0" id="modalKualifikasiDosenLabel">Tambah Riwayat Kualifikasi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label for="kualKat" class="form-label small fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select id="kualKat" name="kategori" class="form-select ecc-control-sm" required>
                            <?php foreach ($kualCat as $kk => $kl): ?>
                                <option value="<?= esc($kk) ?>"><?= esc($kl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-7">
                        <label for="kualJudul" class="form-label small fw-semibold">Judul Pendidikan / Sertifikasi / Penugasan <span class="text-danger">*</span></label>
                        <input type="text" id="kualJudul" name="judul" class="form-control ecc-control-sm" required placeholder="Contoh: S2 Teknik Transportasi / Sertifikat Auditor Keselamatan Jalan">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="kualInst" class="form-label small fw-semibold">Institusi / Penyelenggara</label>
                        <input type="text" id="kualInst" name="institusi_penyelenggara" class="form-control ecc-control-sm" placeholder="Contoh: UGM / BNSP / Kemenhub">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="kualMulai" class="form-label small fw-semibold">Tahun Mulai</label>
                        <input type="number" id="kualMulai" name="tahun_mulai" class="form-control ecc-control-sm ecc-num" min="1970" max="2100">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="kualSelesai" class="form-label small fw-semibold">Tahun Selesai</label>
                        <input type="number" id="kualSelesai" name="tahun_selesai" class="form-control ecc-control-sm ecc-num" min="1970" max="2100">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="kualNoDok" class="form-label small fw-semibold">Nomor SK / Sertifikat / Ijazah</label>
                        <input type="text" id="kualNoDok" name="nomor_dokumen" class="form-control ecc-control-sm">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="kualTautan" class="form-label small fw-semibold">Tautan Bukti (Google Drive / URL)</label>
                        <input type="url" id="kualTautan" name="tautan_bukti" class="form-control ecc-control-sm" placeholder="https://...">
                    </div>
                    <div class="col-12">
                        <label for="kualKet" class="form-label small fw-semibold">Keterangan Tambahan</label>
                        <textarea id="kualKet" name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">Simpan Riwayat</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function resetKualifikasiModal() {
    var form = document.querySelector('#modalKualifikasiDosen form');
    if (form) form.reset();
}
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-dsn-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
    }
    if (window.location.hash === '#tab-eksekutif') {
        var btn = document.getElementById('tab-eksekutif-btn');
        if (btn && window.bootstrap) bootstrap.Tab.getOrCreateInstance(btn).show();
    }
    document.querySelectorAll('.js-dsn-del-kual').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Riwayat?',
                    text: 'Riwayat kualifikasi ini akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function(res) {
                    if (res.isConfirmed) form.submit();
                });
            } else if (confirm('Hapus riwayat kualifikasi ini?')) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
