<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Kasus Pelanggaran<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$kasus            = $detail['kasus'] ?? [];
$pihakList        = $detail['pihak'] ?? [];
$tindakLanjutList = $detail['tindak_lanjut'] ?? [];
$dokumenList      = $detail['dokumen'] ?? [];
$riwayatList      = $detail['riwayat'] ?? [];
$checklist        = $detail['checklist'] ?? [];

$idKasus          = (int) ($kasus['id'] ?? 0);
$activeTab        = (string) ($active_tab ?? 'ringkasan');
$rawPenetapan     = (string) ($kasus['status_penetapan'] ?? 'dugaan');
$rawPenyelesaian  = (string) ($kasus['status_penyelesaian'] ?? 'proses');
$rawValidasi      = (string) ($kasus['status_validasi'] ?? 'draf');
$badgeValidasiKey = ($rawValidasi === 'perlu_perbaikan') ? 'dikembalikan' : $rawValidasi;

$tingkatCatalog   = $refs['tingkat_catalog'] ?? ketarunaan_tingkat_pelanggaran_catalog();
$tingkatRaw       = (string) ($kasus['tingkat_pelanggaran'] ?? 'belum_ditetapkan');
$tingkatLabel     = $tingkatCatalog[$tingkatRaw] ?? ucwords(str_replace('_', ' ', $tingkatRaw));

$kerahasiaanRaw   = (string) ($kasus['tingkat_kerahasiaan'] ?? 'terbatas');
$kerahasiaanLabel = ($kerahasiaanRaw === 'sangat_terbatas') ? 'Akses Sangat Terbatas' : 'Akses Terbatas';

$pemantauanRaw    = (string) ($kasus['status_pemantauan'] ?? '');
$jenisTindakanMap = [
    'penetapan'  => 'Penetapan',
    'pemantauan' => 'Pemantauan',
    'banding'    => 'Banding',
    'selesai'    => 'Penyelesaian',
];

$tabs = [
    'ringkasan'   => 'Ringkasan',
    'pemeriksaan' => 'Pemeriksaan',
    'penetapan'   => 'Penetapan & Tindak Lanjut',
    'dokumen'     => 'Dokumen Bukti',
    'riwayat'     => 'Riwayat Validasi',
];

$canManage      = !empty($canManage);
$canViewFull    = !empty($canViewFull);
$canDecide      = !empty($canDecide);
$canValidateNow = $canDecide && in_array($rawValidasi, ['diajukan', 'perlu_perbaikan'], true);
$canDeleteDraft = $canManage && in_array($rawValidasi, ['draf', 'perlu_perbaikan'], true);

$angkatanLabel = !empty($kasus['angkatan'])
    ? ketarunaan_format_angkatan([
        'tahun_masuk' => $kasus['angkatan'],
        'kode_romawi' => $kasus['angkatan_romawi'] ?? '',
    ])
    : '';

$bapRaw   = trim((string) ($kasus['berkas_bap'] ?? ''));
$isBapUrl = ($bapRaw !== '' && ketarunaan_is_http_url($bapRaw, 2000));
$bapLabel = ($bapRaw !== '') ? ketarunaan_format_bukti_label($bapRaw) : '';
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="text-decoration-none text-secondary">Pelanggaran</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Detail Kasus</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Detail Kasus Pelanggaran</h1>
            <p class="text-secondary mb-0 small">Rincian pencatatan, pemeriksaan, penetapan, dokumen bukti, dan riwayat validasi.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Daftar
            </a>
            <?php if ($canViewFull): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/unduh-ringkasan/' . $idKasus) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                    <i class="bi bi-download" aria-hidden="true"></i> Unduh Ringkasan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Umpan Balik Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Ringkas Identitas Kasus -->
    <div class="bento-card ecc-pl-card mb-4">
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
            <div class="d-flex align-items-start align-items-sm-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1 ecc-num">
                        <?= esc((string) ($kasus['nomor_perkara'] ?: ('Kasus #' . $idKasus))) ?>
                    </h2>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small">
                        <strong class="text-dark"><?= esc((string) ($kasus['display_nama'] ?? '-')) ?></strong>
                        <span>&bull;</span>
                        <span class="ecc-num"><?= esc((string) ($kasus['display_nomor_taruna'] ?? '-')) ?></span>
                        <?php if ($canViewFull && !empty($kasus['nama_prodi'])): ?>
                            <span>&bull;</span>
                            <span><?= esc((string) $kasus['nama_prodi']) ?></span>
                            <?php if ($angkatanLabel !== ''): ?>
                                <span class="ecc-num">(Angkatan <?= esc($angkatanLabel) ?>)</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <span>&bull;</span>
                        <span><?= esc($kerahasiaanLabel) ?></span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <?= ketarunaan_penetapan_badge($rawPenetapan) ?>
                <?= ketarunaan_penyelesaian_badge($rawPenyelesaian) ?>
                <?= ketarunaan_validasi_badge($badgeValidasiKey) ?>
            </div>
        </div>

        <?php if ($canManage || $canValidateNow): ?>
            <div class="d-flex flex-wrap align-items-center gap-2 pt-3 mt-3 border-top">
                <?php if ($canManage): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $idKasus) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                        <?= $rawValidasi === 'tervalidasi' ? 'Ajukan Koreksi' : 'Edit Kasus' ?>
                    </a>
                    <?php if ($canDeleteDraft): ?>
                        <form id="formDeleteKasus" action="<?= base_url('ketarunaan/pelanggaran/hapus/' . $idKasus) ?>" method="post" class="d-inline m-0">
                            <?= csrf_field() ?>
                            <button type="submit" id="btnHapusKasus" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl">
                                Hapus Draf
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($canValidateNow): ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 ms-md-auto">
                        <form id="formApproveKasus" action="<?= base_url('ketarunaan/pelanggaran/validasi/' . $idKasus) ?>" method="post" class="d-inline m-0">
                            <?= csrf_field() ?>
                            <input type="hidden" name="keputusan" value="tervalidasi">
                            <button type="submit" id="btnSetujuiKasus" class="btn btn-success ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Validasi Kasus
                            </button>
                        </form>
                        <?php if ($rawValidasi === 'diajukan'): ?>
                            <button type="button" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl" data-bs-toggle="modal" data-bs-target="#modalKembalikanKasus">
                                Kembalikan
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($rawValidasi === 'perlu_perbaikan' && !empty($kasus['catatan_validasi'])): ?>
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 px-3 py-2 small" role="alert">
                <strong class="d-block text-dark">Catatan Perbaikan Validator</strong>
                <div class="text-dark mt-1"><?= esc((string) $kasus['catatan_validasi']) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kontainer 5 Tab -->
    <div class="bento-card ecc-pl-card">
        <nav class="ecc-tab-nav" aria-label="Bagian Detail Kasus Pelanggaran">
            <?php foreach ($tabs as $tabKey => $tabLabel): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '?tab=' . $tabKey) ?>"
                   class="ecc-tab-link <?= $activeTab === $tabKey ? 'active' : '' ?>"<?= $activeTab === $tabKey ? ' aria-current="page"' : '' ?>>
                    <?= esc($tabLabel) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- TAB 1: RINGKASAN -->
        <?php if ($activeTab === 'ringkasan'): ?>
            <div class="row g-4">
                <div class="col-12 col-lg-7 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Informasi Kasus</h3>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Nomor Perkara</span>
                            <span class="fw-semibold text-dark text-end ecc-num"><?= esc((string) ($kasus['nomor_perkara'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Identitas Taruna</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= esc((string) ($kasus['display_nama'] ?? '-')) ?>
                                (<span class="ecc-num"><?= esc((string) ($kasus['display_nomor_taruna'] ?? '-')) ?></span>)
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Kategori Pelanggaran</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['jenis_pelanggaran'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tingkat Pelanggaran</span>
                            <span class="fw-semibold text-dark text-end"><?= esc($tingkatLabel) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Kejadian</span>
                            <span class="fw-semibold text-dark text-end ecc-num">
                                <?= !empty($kasus['tanggal_kejadian']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_kejadian'], true)) : '-' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Lokasi Kejadian</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= $canViewFull ? esc((string) ($kasus['lokasi'] ?: '-')) : 'Tersamarkan (Akses Terbatas)' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Pelapor / Sumber Data</span>
                            <span class="fw-semibold text-dark text-end">
                                <?php if ($canViewFull): ?>
                                    <?= esc((string) ($kasus['pelapor'] ?: '-')) ?> &bull; <?= esc((string) ($kasus['sumber_data'] ?: '-')) ?>
                                <?php else: ?>
                                    <?= esc((string) ($kasus['sumber_data'] ?: 'Unit Pembina')) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Unit Pemilik</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['unit_pemilik'] ?: '-')) ?></span>
                        </div>
                    </div>

                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Ringkasan Kronologi Kasus</h3>
                        <?php if ($canViewFull): ?>
                            <div class="text-dark small" style="line-height: 1.6;">
                                <?= !empty($kasus['uraian']) ? nl2br(esc((string) $kasus['uraian'])) : '<span class="text-secondary">Belum ada uraian kronologi.</span>' ?>
                            </div>
                        <?php else: ?>
                            <div class="ecc-lock-notice d-flex align-items-center gap-2">
                                <i class="bi bi-lock text-secondary" aria-hidden="true"></i>
                                <span>Ringkasan kasus hanya tersedia bagi petugas berwenang.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Pembinaan dan Pemantauan</h3>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">PIC Penanggung Jawab</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['pic_tindak_lanjut'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Pemantauan</span>
                            <span class="text-end">
                                <?= !empty($pemantauanRaw) ? ketarunaan_penyelesaian_badge($pemantauanRaw) : '<span class="text-secondary">-</span>' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Selesai</span>
                            <span class="fw-semibold text-dark text-end ecc-num">
                                <?= !empty($kasus['tanggal_selesai']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_selesai'], true)) : '-' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Rencana Tindak Lanjut</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= $canViewFull ? esc((string) ($kasus['tindak_lanjut'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Catatan Pemantauan</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= $canViewFull ? esc((string) ($kasus['catatan_pemantauan'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Status &amp; Kelengkapan Kasus</h3>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Penetapan</span>
                            <span><?= ketarunaan_penetapan_badge($rawPenetapan) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Penyelesaian</span>
                            <span><?= ketarunaan_penyelesaian_badge($rawPenyelesaian) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Validasi</span>
                            <span><?= ketarunaan_validasi_badge($badgeValidasiKey) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Laporan / Bukti Awal</span>
                            <span class="fw-semibold <?= !empty($checklist['laporan_ada']) ? 'text-success' : 'text-secondary' ?>">
                                <?= !empty($checklist['laporan_ada']) ? 'Tersedia' : 'Belum Ada' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">BAP Pemeriksaan</span>
                            <span class="fw-semibold <?= !empty($checklist['bap_ada']) ? 'text-success' : 'text-secondary' ?>">
                                <?= !empty($checklist['bap_ada']) ? 'Tersedia' : 'Belum Tersedia' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Surat Penetapan</span>
                            <span class="fw-semibold <?= !empty($checklist['penetapan_ada']) ? 'text-success' : (!empty($checklist['penetapan_wajib']) ? 'text-danger' : 'text-secondary') ?>">
                                <?= !empty($checklist['penetapan_ada']) ? 'Tersedia' : (!empty($checklist['penetapan_wajib']) ? 'Wajib Dilengkapi' : 'Opsional') ?>
                            </span>
                        </div>

                        <?php if ($canManage): ?>
                            <div class="d-flex flex-wrap gap-2 pt-3 mt-3 border-top">
                                <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center">Pemeriksaan</a>
                                <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center">Penetapan</a>
                                <a href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus) ?>" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center">Dokumen</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="h6 fw-bold text-dark mb-0">Dokumen Terkait</h3>
                            <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '?tab=dokumen') ?>" class="text-decoration-none fw-semibold small">
                                Lihat Semua
                            </a>
                        </div>

                        <?php if (empty($dokumenList)): ?>
                            <p class="text-secondary mb-0 small">Belum ada tautan atau berkas bukti yang disimpan.</p>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($dokumenList as $dok): ?>
                                    <?php
                                    $rawDoc     = trim((string) ($dok['nama_berkas'] ?? ''));
                                    $isDocUrl   = ketarunaan_is_http_url($rawDoc, 2000);
                                    $docLabel   = ketarunaan_format_bukti_label($rawDoc);
                                    $klasifDoc  = (($dok['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas') ? 'Sangat Terbatas' : 'Terbatas';
                                    $bolehAkses = $canViewFull && !empty($dok['can_access']);
                                    $docHref    = $isDocUrl
                                        ? $rawDoc
                                        : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawDoc)) . '&inline=1');
                                    ?>
                                    <div class="ecc-file-item">
                                        <div class="d-flex align-items-center gap-2 text-truncate min-w-0">
                                            <i class="bi <?= $isDocUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-pdf-fill text-danger' ?> flex-shrink-0" aria-hidden="true"></i>
                                            <div class="text-truncate min-w-0">
                                                <div class="fw-semibold text-dark text-truncate small"><?= esc($docLabel) ?></div>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    <?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?>
                                                    &bull; <?= esc($klasifDoc) ?>
                                                    <?php if (!empty($dok['tanggal_unggah'])): ?>
                                                        &bull; <?= esc(ketarunaan_format_tanggal((string) $dok['tanggal_unggah'], true)) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($bolehAkses): ?>
                                            <a href="<?= esc($docHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 flex-shrink-0 btn-tactile pl-touch-ctrl d-inline-flex align-items-center gap-1">
                                                <?php if ($isDocUrl): ?>
                                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka
                                                <?php else: ?>
                                                    Pratinjau
                                                <?php endif; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">
                                                <i class="bi bi-lock me-1" aria-hidden="true"></i>Terkunci
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <!-- TAB 2: PEMERIKSAAN -->
        <?php elseif ($activeTab === 'pemeriksaan'): ?>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                <h3 class="h6 fw-bold text-dark mb-0">Data Pemeriksaan &amp; Pihak Kasus</h3>
                <?php if ($canManage): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                        Kelola Pemeriksaan &amp; Pihak
                    </a>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box mb-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Pemeriksaan</span>
                            <span class="fw-semibold text-dark ecc-num">
                                <?= !empty($kasus['tanggal_pemeriksaan']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_pemeriksaan'], true)) : '-' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Pemeriksa</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['pemeriksa'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tingkat Pelanggaran</span>
                            <span class="fw-semibold text-dark text-end"><?= esc($tingkatLabel) ?></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Dasar Peraturan</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['dasar_peraturan'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Dokumen BAP</span>
                            <span class="text-end">
                                <?php if ($bapRaw === ''): ?>
                                    <span class="text-secondary">Belum tersedia</span>
                                <?php elseif ($canViewFull): ?>
                                    <a href="<?= esc($isBapUrl ? $bapRaw : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($bapRaw)) . '&inline=1')) ?>"
                                       target="_blank" rel="noopener noreferrer" class="fw-semibold text-decoration-none d-inline-flex align-items-center gap-1">
                                        <?= esc($bapLabel) ?>
                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Terkunci (Terbatas)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <?php if ($canViewFull): ?>
                    <div class="row g-3 small">
                        <div class="col-12 col-md-6">
                            <div class="fw-semibold text-dark mb-1">Ringkasan Klarifikasi</div>
                            <p class="text-secondary mb-0" style="line-height: 1.6;">
                                <?= !empty($kasus['ringkasan_klarifikasi']) ? nl2br(esc((string) $kasus['ringkasan_klarifikasi'])) : 'Belum dicatat.' ?>
                            </p>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="fw-semibold text-dark mb-1">Hasil Pemeriksaan</div>
                            <p class="text-secondary mb-0" style="line-height: 1.6;">
                                <?= !empty($kasus['hasil_pemeriksaan']) ? nl2br(esc((string) $kasus['hasil_pemeriksaan'])) : 'Belum dicatat.' ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="ecc-lock-notice d-flex align-items-center gap-2">
                        <i class="bi bi-lock text-secondary" aria-hidden="true"></i>
                        <span>Rincian klarifikasi dan hasil pemeriksaan hanya tersedia bagi petugas berwenang.</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="h6 fw-bold text-dark mb-0">Daftar Pihak Terkait Kasus</h4>
                    <span class="text-secondary ecc-num small"><?= count($pihakList) ?> Pihak</span>
                </div>
                <?php if (empty($pihakList)): ?>
                    <div class="py-4 text-center text-secondary small">Belum ada pihak tercatat pada kasus ini.</div>
                <?php else: ?>
                    <div class="table-responsive ecc-pl-scroll d-none d-md-block">
                        <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="0">
                                            <span>Nama Pihak</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="1">
                                            <span>Jenis</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="2">
                                            <span>Identitas / Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="3">
                                            <span>Peran</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pihakList as $ph): ?>
                                    <?php $isEksternal = (($ph['jenis_pihak'] ?? 'taruna') === 'eksternal'); ?>
                                    <tr>
                                        <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) ($ph['display_nama'] ?? ''))) ?>"><?= esc((string) ($ph['display_nama'] ?? '-')) ?></td>
                                        <td data-sort-val="<?= $isEksternal ? 'eksternal' : 'taruna' ?>">
                                            <span class="badge <?= $isEksternal ? 'bg-light text-dark border' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' ?> rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                <?= $isEksternal ? 'Eksternal' : 'Taruna' ?>
                                            </span>
                                        </td>
                                        <td class="ecc-num text-secondary" data-sort-val="<?= esc((string) ($ph['display_nomor'] ?? '')) ?>"><?= esc((string) ($ph['display_nomor'] ?? '-')) ?></td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($ph['peran'] ?: ''))) ?>"><?= esc((string) ($ph['peran'] ?: '-')) ?></td>
                                        <td class="text-secondary"><?= $canViewFull ? esc((string) ($ph['keterangan'] ?: '-')) : 'Terbatas' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Pihak -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($pihakList as $ph): ?>
                            <?php $isEksternal = (($ph['jenis_pihak'] ?? 'taruna') === 'eksternal'); ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <div class="fw-bold text-dark small"><?= esc((string) ($ph['display_nama'] ?? '-')) ?></div>
                                    <span class="badge <?= $isEksternal ? 'bg-light text-dark border' : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle' ?> rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                        <?= $isEksternal ? 'Eksternal' : 'Taruna' ?>
                                    </span>
                                </div>
                                <div class="text-secondary ecc-num mb-1" style="font-size: 0.75rem;">
                                    <?= esc((string) ($ph['display_nomor'] ?? '-')) ?> &bull; <?= esc((string) ($ph['peran'] ?: '-')) ?>
                                </div>
                                <div class="text-secondary small"><?= $canViewFull ? esc((string) ($ph['keterangan'] ?: '-')) : 'Terbatas' ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <!-- TAB 3: PENETAPAN & TINDAK LANJUT -->
        <?php elseif ($activeTab === 'penetapan'): ?>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                <h3 class="h6 fw-bold text-dark mb-0">Keputusan Aktif &amp; Riwayat Tindak Lanjut</h3>
                <?php if ($canManage): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i> Catat Tindak Lanjut
                        </button>
                        <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            Kelola Penetapan
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box mb-4">
                <h4 class="h6 fw-bold text-dark mb-3">Penetapan &amp; Sanksi Aktif</h4>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Penetapan</span>
                            <span><?= ketarunaan_penetapan_badge($rawPenetapan) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Penyelesaian</span>
                            <span><?= ketarunaan_penyelesaian_badge($rawPenyelesaian) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Jenis Sanksi</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['jenis_sanksi'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Sanksi / Keputusan</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= $canViewFull ? esc((string) ($kasus['sanksi'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Nomor Keputusan</span>
                            <span class="fw-semibold text-dark text-end ecc-num"><?= esc((string) ($kasus['nomor_keputusan'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Keputusan</span>
                            <span class="fw-semibold text-dark text-end ecc-num">
                                <?= !empty($kasus['tanggal_keputusan']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_keputusan'], true)) : '-' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">PIC Tindak Lanjut</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['pic_tindak_lanjut'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Target / Tanggal Selesai</span>
                            <span class="fw-semibold text-dark text-end ecc-num">
                                <?= !empty($kasus['tanggal_selesai']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_selesai'], true)) : '-' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ecc-panel-box">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h4 class="h6 fw-bold text-dark mb-1">Riwayat Tindak Lanjut &amp; Banding</h4>
                        <p class="text-secondary mb-0 small">Setiap penetapan awal, pemantauan pembinaan, dan banding tersimpan terpisah.</p>
                    </div>
                    <span class="text-secondary ecc-num small"><?= count($tindakLanjutList) ?> Catatan</span>
                </div>

                <?php if (empty($tindakLanjutList)): ?>
                    <div class="py-4 text-center text-secondary small">Belum ada catatan tindak lanjut atau banding.</div>
                <?php else: ?>
                    <div class="table-responsive ecc-pl-scroll d-none d-md-block">
                        <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="0">
                                            <span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="1">
                                            <span>Jenis</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th>Status Saat Itu</th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="3">
                                            <span>Nomor Dasar</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th>Sanksi / Catatan</th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="5">
                                            <span>PIC &amp; Target</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th class="text-end">Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tindakLanjutList as $tl): ?>
                                    <?php
                                    $jenisKey  = (string) ($tl['jenis_tindakan'] ?? 'pemantauan');
                                    $jenisLbl  = $jenisTindakanMap[$jenisKey] ?? ucfirst($jenisKey);
                                    $stPenTl   = (string) ($tl['status_penetapan_saat_itu'] ?? 'dugaan');
                                    $stSlsTl   = (string) ($tl['status_penyelesaian_saat_itu'] ?? 'proses');
                                    $rawLamp   = trim((string) ($tl['berkas_pendukung'] ?? ''));
                                    $isLampUrl = ketarunaan_is_http_url($rawLamp, 2000);
                                    $lampHref  = $isLampUrl
                                        ? $rawLamp
                                        : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawLamp)) . '&inline=1');
                                    ?>
                                    <tr>
                                        <td class="ecc-num text-nowrap" data-sort-val="<?= esc((string) ($tl['tanggal_tindakan'] ?? '')) ?>">
                                            <?= !empty($tl['tanggal_tindakan']) ? esc(ketarunaan_format_tanggal((string) $tl['tanggal_tindakan'], true)) : '-' ?>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower($jenisLbl)) ?>">
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                <?= esc($jenisLbl) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?= ketarunaan_penetapan_badge($stPenTl) ?>
                                                <?= ketarunaan_penyelesaian_badge($stSlsTl) ?>
                                            </div>
                                        </td>
                                        <td class="ecc-num" data-sort-val="<?= esc(strtolower((string) ($tl['nomor_dasar'] ?: ''))) ?>"><?= esc((string) ($tl['nomor_dasar'] ?: '-')) ?></td>
                                        <td>
                                            <?= $canViewFull ? esc((string) ($tl['sanksi_tindakan'] ?: '-')) : 'Terbatas' ?>
                                            <?php if (!empty($tl['dicatat_oleh'])): ?>
                                                <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                                                    Oleh: <?= esc((string) $tl['dicatat_oleh']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($tl['pic'] ?: ''))) ?>">
                                            <div><?= esc((string) ($tl['pic'] ?: '-')) ?></div>
                                            <?php if (!empty($tl['target_selesai'])): ?>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    Target: <?= esc(ketarunaan_format_tanggal((string) $tl['target_selesai'], true)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($rawLamp === ''): ?>
                                                <span class="text-secondary">-</span>
                                            <?php elseif ($canViewFull): ?>
                                                <a href="<?= esc($lampHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile d-inline-flex align-items-center gap-1">
                                                    <?php if ($isLampUrl): ?>
                                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka
                                                    <?php else: ?>
                                                        Lihat
                                                    <?php endif; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Terkunci</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Tindak Lanjut -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($tindakLanjutList as $tl): ?>
                            <?php
                            $jenisKey  = (string) ($tl['jenis_tindakan'] ?? 'pemantauan');
                            $jenisLbl  = $jenisTindakanMap[$jenisKey] ?? ucfirst($jenisKey);
                            $stPenTl   = (string) ($tl['status_penetapan_saat_itu'] ?? 'dugaan');
                            $stSlsTl   = (string) ($tl['status_penyelesaian_saat_itu'] ?? 'proses');
                            $rawLamp   = trim((string) ($tl['berkas_pendukung'] ?? ''));
                            $isLampUrl = ketarunaan_is_http_url($rawLamp, 2000);
                            $lampHref  = $isLampUrl
                                ? $rawLamp
                                : base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawLamp)) . '&inline=1');
                            ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <div class="fw-bold text-dark small"><?= esc($jenisLbl) ?></div>
                                        <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                            <?= !empty($tl['tanggal_tindakan']) ? esc(ketarunaan_format_tanggal((string) $tl['tanggal_tindakan'], true)) : '-' ?>
                                            <?php if (!empty($tl['nomor_dasar'])): ?>
                                                &bull; <?= esc((string) $tl['nomor_dasar']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                                        <?= ketarunaan_penetapan_badge($stPenTl) ?>
                                        <?= ketarunaan_penyelesaian_badge($stSlsTl) ?>
                                    </div>
                                </div>
                                <div class="small text-dark mb-1"><?= $canViewFull ? esc((string) ($tl['sanksi_tindakan'] ?: '-')) : 'Terbatas' ?></div>
                                <div class="text-secondary mb-2" style="font-size: 0.75rem;">
                                    PIC: <?= esc((string) ($tl['pic'] ?: '-')) ?>
                                    <?php if (!empty($tl['dicatat_oleh'])): ?>
                                        &bull; Oleh: <?= esc((string) $tl['dicatat_oleh']) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($rawLamp !== '' && $canViewFull): ?>
                                    <a href="<?= esc($lampHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 w-100 fw-semibold pl-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> <?= $isLampUrl ? 'Buka Lampiran' : 'Lihat Lampiran' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <!-- TAB 4: DOKUMEN BUKTI -->
        <?php elseif ($activeTab === 'dokumen'): ?>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                <h3 class="h6 fw-bold text-dark mb-0">Dokumen Bukti Kasus</h3>
                <?php if ($canManage): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">
                        Kelola Dokumen &amp; Pengajuan
                    </a>
                <?php endif; ?>
            </div>

            <div class="px-3 py-2 rounded-3 bg-light border text-secondary small mb-3">
                Dokumen kasus bersifat terbatas dan hanya dapat dibuka oleh petugas berwenang sesuai klasifikasi akses.
            </div>

            <div class="ecc-panel-box">
                <?php if (empty($dokumenList)): ?>
                    <div class="py-4 text-center text-secondary small">Belum ada tautan atau berkas bukti untuk kasus ini.</div>
                <?php else: ?>
                    <div class="table-responsive ecc-pl-scroll d-none d-md-block">
                        <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="0">
                                            <span>Tautan / Nama Berkas</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="1">
                                            <span>Jenis Dokumen</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="2">
                                            <span>Klasifikasi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="3">
                                            <span>Tanggal Dicatat</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dokumenList as $dok): ?>
                                    <?php
                                    $rawDoc     = trim((string) ($dok['nama_berkas'] ?? ''));
                                    $isDocUrl   = ketarunaan_is_http_url($rawDoc, 2000);
                                    $docLabel   = ketarunaan_format_bukti_label($rawDoc);
                                    $klasifKey  = (string) ($dok['klasifikasi'] ?? 'terbatas');
                                    $klasifLbl  = ($klasifKey === 'sangat_terbatas') ? 'Sangat Terbatas' : 'Terbatas';
                                    $bolehAkses = $canViewFull && !empty($dok['can_access']);
                                    $urlBase    = base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawDoc)));
                                    ?>
                                    <tr>
                                        <td data-sort-val="<?= esc(strtolower($docLabel)) ?>">
                                            <div class="fw-semibold text-dark"><?= esc($docLabel) ?></div>
                                            <?php if ($isDocUrl && $bolehAkses): ?>
                                                <div class="text-secondary text-truncate" style="max-width: 280px; font-size: 0.75rem;" title="<?= esc($rawDoc) ?>"><?= esc($rawDoc) ?></div>
                                            <?php elseif (!$isDocUrl && !empty($dok['ukuran_kb'])): ?>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;"><?= number_format((int) $dok['ukuran_kb'], 0, ',', '.') ?> KB</div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($dok['jenis_dokumen'] ?? 'Pendukung'))) ?>"><?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?></td>
                                        <td data-sort-val="<?= esc($klasifKey) ?>">
                                            <span class="badge <?= $klasifKey === 'sangat_terbatas' ? 'bg-danger-subtle text-danger-emphasis border border-danger-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' ?> rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                <?= esc($klasifLbl) ?>
                                            </span>
                                        </td>
                                        <td class="ecc-num text-nowrap" data-sort-val="<?= esc((string) ($dok['tanggal_unggah'] ?? '')) ?>">
                                            <?= !empty($dok['tanggal_unggah']) ? esc(ketarunaan_format_tanggal((string) $dok['tanggal_unggah'], true)) : '-' ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($bolehAkses): ?>
                                                <?php if ($isDocUrl): ?>
                                                    <a href="<?= esc($rawDoc) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= esc($urlBase . '&inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 me-1 btn-tactile">Pratinjau</a>
                                                    <a href="<?= esc($urlBase) ?>" class="btn btn-light border btn-sm rounded-3 px-3 btn-tactile">Unduh</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-lock me-1" aria-hidden="true"></i>Terkunci (Terbatas)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Dokumen -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($dokumenList as $dok): ?>
                            <?php
                            $rawDoc     = trim((string) ($dok['nama_berkas'] ?? ''));
                            $isDocUrl   = ketarunaan_is_http_url($rawDoc, 2000);
                            $docLabel   = ketarunaan_format_bukti_label($rawDoc);
                            $klasifKey  = (string) ($dok['klasifikasi'] ?? 'terbatas');
                            $klasifLbl  = ($klasifKey === 'sangat_terbatas') ? 'Sangat Terbatas' : 'Terbatas';
                            $bolehAkses = $canViewFull && !empty($dok['can_access']);
                            $urlBase    = base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename($rawDoc)));
                            ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <div class="fw-bold text-dark small text-truncate"><?= esc($docLabel) ?></div>
                                    <span class="badge <?= $klasifKey === 'sangat_terbatas' ? 'bg-danger-subtle text-danger-emphasis border border-danger-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' ?> rounded-pill px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">
                                        <?= esc($klasifLbl) ?>
                                    </span>
                                </div>
                                <div class="text-secondary ecc-num mb-2" style="font-size: 0.75rem;">
                                    <?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?>
                                    <?php if (!empty($dok['tanggal_unggah'])): ?>
                                        &bull; <?= esc(ketarunaan_format_tanggal((string) $dok['tanggal_unggah'], true)) ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($bolehAkses): ?>
                                    <div class="d-flex gap-2">
                                        <?php if ($isDocUrl): ?>
                                            <a href="<?= esc($rawDoc) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 flex-fill fw-semibold pl-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                                                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= esc($urlBase . '&inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 flex-fill fw-semibold pl-touch-ctrl d-inline-flex align-items-center justify-content-center">Pratinjau</a>
                                            <a href="<?= esc($urlBase) ?>" class="btn btn-light border btn-sm rounded-3 px-3 flex-fill fw-semibold pl-touch-ctrl d-inline-flex align-items-center justify-content-center">Unduh</a>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-lock me-1" aria-hidden="true"></i>Terkunci (Terbatas)
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        <!-- TAB 5: RIWAYAT VALIDASI -->
        <?php else: ?>
            <div class="px-3 py-2 rounded-3 bg-light border text-secondary small mb-3">
                Validasi pencatatan tidak menggantikan kewenangan penetapan kasus. Penetapan status kasus dilakukan sesuai ketentuan oleh pihak berwenang.
            </div>

            <div class="ecc-panel-box mb-4">
                <h3 class="h6 fw-bold text-dark mb-3">Status Validasi Terkini</h3>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Validasi</span>
                            <span><?= ketarunaan_validasi_badge($badgeValidasiKey) ?></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Diajukan Oleh</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['diajukan_oleh'] ?: '-')) ?></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Divalidasi Oleh</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['divalidasi_oleh'] ?: '-')) ?></span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($kasus['catatan_validasi'])): ?>
                    <div class="mt-3 pt-3 border-top small">
                        <span class="text-secondary">Catatan Validasi:</span>
                        <span class="text-dark fw-medium ms-1"><?= esc((string) $kasus['catatan_validasi']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="h6 fw-bold text-dark mb-0">Riwayat Aktivitas Validasi</h4>
                    <span class="text-secondary ecc-num small"><?= count($riwayatList) ?> Aktivitas</span>
                </div>
                <?php if (empty($riwayatList)): ?>
                    <div class="py-4 text-center text-secondary small">Belum ada riwayat aktivitas validasi.</div>
                <?php else: ?>
                    <div class="table-responsive ecc-pl-scroll d-none d-md-block">
                        <table class="table table-hover ecc-table-pl mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th style="width: 56px;">No</th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="1">
                                            <span>Pelaku</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="2">
                                            <span>Aktivitas</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="pl-sort-btn" data-sort-col="3">
                                            <span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayatList as $rIdx => $rw): ?>
                                    <?php
                                    $rwStatusRaw = (string) ($rw['status'] ?? 'draf');
                                    $rwBadgeKey  = ($rwStatusRaw === 'perlu_perbaikan') ? 'dikembalikan' : $rwStatusRaw;
                                    ?>
                                    <tr>
                                        <td class="ecc-num text-secondary"><?= $rIdx + 1 ?></td>
                                        <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) ($rw['pelaku'] ?? ''))) ?>"><?= esc((string) ($rw['pelaku'] ?? '-')) ?></td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($rw['aksi'] ?? ''))) ?>"><?= esc((string) ($rw['aksi'] ?? '-')) ?></td>
                                        <td data-sort-val="<?= esc(strtolower($rwStatusRaw)) ?>"><?= ketarunaan_validasi_badge($rwBadgeKey) ?></td>
                                        <td class="text-secondary"><?= esc((string) ($rw['catatan'] ?: '-')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Riwayat Validasi -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($riwayatList as $rw): ?>
                            <?php
                            $rwStatusRaw = (string) ($rw['status'] ?? 'draf');
                            $rwBadgeKey  = ($rwStatusRaw === 'perlu_perbaikan') ? 'dikembalikan' : $rwStatusRaw;
                            ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <div class="fw-bold text-dark small"><?= esc((string) ($rw['aksi'] ?? '-')) ?></div>
                                    <?= ketarunaan_validasi_badge($rwBadgeKey) ?>
                                </div>
                                <div class="text-secondary small mb-1">Pelaku: <strong class="text-dark"><?= esc((string) ($rw['pelaku'] ?? '-')) ?></strong></div>
                                <div class="text-secondary small"><?= esc((string) ($rw['catatan'] ?: '-')) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Catat Tindak Lanjut (Opsi A - Tautan Google Drive) -->
<?php if ($canManage): ?>
    <div class="modal fade" id="modalTindakLanjut" tabindex="-1" aria-labelledby="modalTindakLanjutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="formTindakLanjut" action="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '/tindak-lanjut') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header border-bottom px-4 py-3">
                        <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalTindakLanjutLabel">Catat Tindak Lanjut / Banding</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="tlTanggal" class="form-label fw-semibold text-dark small">
                                    Tanggal Tindakan <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="tlTanggal" name="tanggal_tindakan" class="form-control ecc-control-sm ecc-num bg-white js-flatpickr-date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tlPenyelesaian" class="form-label fw-semibold text-dark small">
                                    Status Penyelesaian <span class="text-danger">*</span>
                                </label>
                                <select id="tlPenyelesaian" name="status_penyelesaian" class="form-select ecc-control-sm" required>
                                    <option value="proses" <?= $rawPenyelesaian === 'proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="selesai" <?= $rawPenyelesaian === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="banding" <?= $rawPenyelesaian === 'banding' ? 'selected' : '' ?>>Banding</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tlNomorDasar" class="form-label fw-semibold text-dark small">Nomor Dasar / Nota Dinas</label>
                                <input type="text" id="tlNomorDasar" name="nomor_dasar" class="form-control ecc-control-sm ecc-num" maxlength="100" placeholder="Opsional">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tlPic" class="form-label fw-semibold text-dark small">PIC Penanggung Jawab</label>
                                <select id="tlPic" name="pic" class="form-select ecc-control-sm">
                                    <?php $currentPic = (string) ($kasus['pic_tindak_lanjut'] ?? 'Unit Ketarunaan'); ?>
                                    <?php foreach (($refs['pic_options'] ?? []) as $picOption): ?>
                                        <option value="<?= esc((string) $picOption) ?>" <?= $currentPic === $picOption ? 'selected' : '' ?>>
                                            <?= esc((string) $picOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tlTargetSelesai" class="form-label fw-semibold text-dark small">Target Selesai</label>
                                <input type="text" id="tlTargetSelesai" name="target_selesai" class="form-control ecc-control-sm ecc-num bg-white js-flatpickr-date" placeholder="Opsional">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tlBerkas" class="form-label fw-semibold text-dark small">Tautan Lampiran (Google Drive)</label>
                                <input type="url" id="tlBerkas" name="berkas_pendukung" class="form-control ecc-control-sm" maxlength="255" placeholder="https://drive.google.com/file/d/.../view" autocomplete="off">
                                <div class="form-text" style="font-size: 0.75rem;">Opsional. Awali dengan http:// atau https:// (maks. 255 karakter).</div>
                            </div>
                            <div class="col-12">
                                <label for="tlUraian" class="form-label fw-semibold text-dark small">
                                    Uraian Tindak Lanjut / Pemantauan <span class="text-danger">*</span>
                                </label>
                                <textarea id="tlUraian" name="sanksi_tindakan" rows="3" maxlength="1000" class="form-control rounded-3 small" placeholder="Tuliskan perkembangan pembinaan, pemantauan, atau catatan banding." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light border ecc-control-sm px-3 pl-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile pl-touch-ctrl">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Kembalikan Validasi (hanya untuk validator berwenang saat status diajukan) -->
<?php if ($canValidateNow && $rawValidasi === 'diajukan'): ?>
    <div class="modal fade" id="modalKembalikanKasus" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="formKembalikanKasus" action="<?= base_url('ketarunaan/pelanggaran/validasi/' . $idKasus) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="perlu_perbaikan">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalKembalikanLabel">Kembalikan Pencatatan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label for="inputCatatanPerbaikan" class="form-label fw-semibold text-dark small">
                            Catatan Perbaikan <span class="text-danger">*</span>
                        </label>
                        <textarea id="inputCatatanPerbaikan" name="catatan_validasi" rows="3" maxlength="500" class="form-control rounded-3" placeholder="Jelaskan data atau dokumen yang perlu dilengkapi." required></textarea>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light border ecc-control-sm px-3 pl-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger ecc-control-sm px-4 fw-semibold btn-tactile pl-touch-ctrl">Kembalikan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function showWarn(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(text);
        }
    }

    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        document.querySelectorAll('.js-flatpickr-date').forEach(function (el) {
            var lbl = el.id === 'tlTanggal' ? 'Tanggal Tindakan' : 'Target Selesai';
            flatpickr(el, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                onReady: function (d, s, inst) {
                    if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', lbl);
                }
            });
        });
    }

    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
        var buttons = table.querySelectorAll('thead .pl-sort-btn');
        var tbody = table.querySelector('tbody');
        if (!tbody || buttons.length === 0) return;

        var activeCol = null;
        var activeDir = 'asc';

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var th = btn.closest('th');
                var colIdx = parseInt(btn.getAttribute('data-sort-col'), 10);
                if (isNaN(colIdx)) return;

                if (activeCol === colIdx) {
                    activeDir = (activeDir === 'asc') ? 'desc' : 'asc';
                } else {
                    activeCol = colIdx;
                    activeDir = 'asc';
                }

                table.querySelectorAll('thead th[aria-sort]').forEach(function (h) {
                    h.classList.remove('th-sorted');
                    h.setAttribute('aria-sort', 'none');
                    var icon = h.querySelector('.sort-indicator');
                    if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                });

                if (th) {
                    th.classList.add('th-sorted');
                    th.setAttribute('aria-sort', activeDir === 'asc' ? 'ascending' : 'descending');
                }
                var activeIcon = btn.querySelector('.sort-indicator');
                if (activeIcon) {
                    activeIcon.className = activeDir === 'asc'
                        ? 'bi bi-arrow-up text-primary sort-indicator'
                        : 'bi bi-arrow-down text-primary sort-indicator';
                }

                var rows = Array.from(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    var cellA = a.children[colIdx];
                    var cellB = b.children[colIdx];
                    var rawA = cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent.trim().toLowerCase()) : '';
                    var rawB = cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent.trim().toLowerCase()) : '';
                    var cmp = rawA.localeCompare(rawB, 'id', { numeric: true });
                    return activeDir === 'asc' ? cmp : -cmp;
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
            });
        });
    });

    var formHapus = document.getElementById('formDeleteKasus');
    if (formHapus) {
        formHapus.addEventListener('submit', function (e) {
            if (formHapus.dataset.confirmed === '1') return;
            e.preventDefault();
            var submitNow = function () {
                formHapus.dataset.confirmed = '1';
                var btn = document.getElementById('btnHapusKasus');
                if (btn) btn.disabled = true;
                formHapus.submit();
            };
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Draf?',
                    text: 'Draf kasus pelanggaran ini akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (result) {
                    if (result.isConfirmed) submitNow();
                });
            } else if (confirm('Draf kasus pelanggaran ini akan dihapus.')) {
                submitNow();
            }
        });
    }

    var formSetujui = document.getElementById('formApproveKasus');
    if (formSetujui) {
        formSetujui.addEventListener('submit', function (e) {
            if (formSetujui.dataset.confirmed === '1') return;
            e.preventDefault();
            var submitNow = function () {
                formSetujui.dataset.confirmed = '1';
                var btn = document.getElementById('btnSetujuiKasus');
                if (btn) btn.disabled = true;
                formSetujui.submit();
            };
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validasi Kasus?',
                    text: 'Pencatatan kasus ini akan disahkan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#15803d',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Validasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (result) {
                    if (result.isConfirmed) submitNow();
                });
            } else if (confirm('Pencatatan kasus ini akan disahkan.')) {
                submitNow();
            }
        });
    }

    var formKembalikan = document.getElementById('formKembalikanKasus');
    if (formKembalikan) {
        formKembalikan.addEventListener('submit', function (e) {
            var ta = document.getElementById('inputCatatanPerbaikan');
            if (ta && ta.value.trim() === '') {
                e.preventDefault();
                ta.focus();
                showWarn('Catatan Wajib Diisi', 'Tuliskan bagian yang perlu dilengkapi sebelum mengembalikan data.');
                return;
            }
            var btn = formKembalikan.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    }

    var formTl = document.getElementById('formTindakLanjut');
    if (formTl) {
        formTl.addEventListener('submit', function (e) {
            var tgl    = document.getElementById('tlTanggal');
            var target = document.getElementById('tlTargetSelesai');
            var link   = document.getElementById('tlBerkas');
            var uraian = document.getElementById('tlUraian');

            if (tgl && tgl.value.trim() === '') {
                e.preventDefault();
                showWarn('Tanggal Wajib Diisi', 'Pilih tanggal tindakan terlebih dahulu.');
                return;
            }
            if (target && target.value.trim() !== '' && tgl && target.value.trim() < tgl.value.trim()) {
                e.preventDefault();
                showWarn('Tanggal Tidak Valid', 'Target selesai tidak boleh lebih awal dari tanggal tindakan.');
                return;
            }
            if (link && link.value.trim() !== '') {
                var cleanUrl = link.value.trim();
                if (!/^https?:\/\/\S+/i.test(cleanUrl) || cleanUrl.length > 255) {
                    e.preventDefault();
                    link.focus();
                    showWarn('Tautan Tidak Valid', 'Tautan lampiran harus diawali http:// atau https:// (maksimal 255 karakter).');
                    return;
                }
            }
            if (uraian && uraian.value.trim() === '') {
                e.preventDefault();
                uraian.focus();
                showWarn('Uraian Wajib Diisi', 'Tuliskan uraian tindak lanjut atau catatan pemantauan.');
                return;
            }

            var btn = formTl.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    }

    window.addEventListener('pageshow', function () {
        document.querySelectorAll('#formDeleteKasus button, #formApproveKasus button, #formKembalikanKasus button[type="submit"], #formTindakLanjut button[type="submit"]').forEach(function (btn) {
            btn.disabled = false;
        });
    });
});
</script>
<?= $this->endSection() ?>
