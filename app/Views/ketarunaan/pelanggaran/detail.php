<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Kasus Pelanggaran<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pl-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pl-card {
            padding: 16px;
        }
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-tab-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .ecc-tab-link {
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 150ms cubic-bezier(0.16, 1, 0.3, 1), color 150ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-tab-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-tab-link.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-panel-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background-color: #ffffff;
    }
    .ecc-lock-notice {
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        color: #475569;
        font-size: 0.875rem;
    }
    .ecc-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .ecc-meta-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
    .ecc-table-pl thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .ecc-table-pl tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #0f172a;
    }
    .ecc-table-pl tbody tr:last-child td {
        border-bottom: 0;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-tab-link {
            transition: none;
        }
    }
</style>
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

$canValidateNow = !empty($canDecide) && in_array($rawValidasi, ['diajukan', 'perlu_perbaikan'], true);
?>

<div class="container-fluid px-0">
    <!-- Bar Navigasi Atas -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3 btn-tactile">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
        <?php if (!empty($canManage) || !empty($canViewFull) || $canValidateNow): ?>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if (!empty($canViewFull)): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/unduh-ringkasan/' . $idKasus) ?>" class="btn btn-outline-primary btn-sm ecc-control-sm px-3 btn-tactile">
                        <i class="bi bi-download me-1"></i>Unduh Ringkasan
                    </a>
                <?php endif; ?>

                <?php if (!empty($canManage)): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $idKasus) ?>" class="btn btn-primary btn-sm ecc-control-sm px-3 btn-tactile">
                        <?= $rawValidasi === 'tervalidasi' ? 'Ajukan Koreksi' : 'Edit Kasus' ?>
                    </a>
                    <?php if ($rawValidasi !== 'tervalidasi'): ?>
                        <form id="formDeleteKasus" action="<?= base_url('ketarunaan/pelanggaran/hapus/' . $idKasus) ?>" method="post" class="d-inline m-0">
                            <?= csrf_field() ?>
                            <button type="button" id="btnHapusKasus" class="btn btn-outline-danger btn-sm ecc-control-sm px-3 btn-tactile">
                                Hapus Draf
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($canValidateNow): ?>
                    <form id="formApproveKasus" action="<?= base_url('ketarunaan/pelanggaran/validasi/' . $idKasus) ?>" method="post" class="d-inline m-0">
                        <?= csrf_field() ?>
                        <input type="hidden" name="keputusan" value="tervalidasi">
                        <button type="button" id="btnSetujuiKasus" class="btn btn-success btn-sm ecc-control-sm px-3 btn-tactile">
                            <i class="bi bi-check-lg me-1"></i>Validasi Kasus
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-warning btn-sm ecc-control-sm px-3 btn-tactile" data-bs-toggle="modal" data-bs-target="#modalKembalikanKasus">
                        Kembalikan
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Umpan Balik Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc((string) session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if ($rawValidasi === 'perlu_perbaikan' && !empty($kasus['catatan_validasi'])): ?>
        <div class="alert alert-warning border-warning-subtle rounded-3 mb-3 py-2 px-3" role="alert" style="font-size: 0.875rem;">
            <strong>Perlu Perbaikan:</strong> <?= esc((string) $kasus['catatan_validasi']) ?>
        </div>
    <?php endif; ?>

    <!-- Kartu Utama Detail Kasus -->
    <div class="ecc-pl-card">
        <!-- Header Identitas Kasus & Tiga Dimensi Status -->
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 pb-3 mb-3 border-bottom">
            <div class="d-flex align-items-start align-items-sm-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h1 class="h5 fw-bold text-dark mb-0 ecc-num">
                            <?= esc((string) ($kasus['nomor_perkara'] ?: ('Kasus #' . $idKasus))) ?>
                        </h1>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                            <?= esc($kerahasiaanLabel) ?>
                        </span>
                    </div>
                    <div class="text-secondary" style="font-size: 0.875rem;">
                        <strong><?= esc((string) ($kasus['display_nama'] ?? '-')) ?></strong>
                        <span class="mx-1">&middot;</span>
                        <span class="ecc-num"><?= esc((string) ($kasus['display_nomor_taruna'] ?? '-')) ?></span>
                        <?php if (!empty($canViewFull) && !empty($kasus['nama_prodi'])): ?>
                            <span class="mx-1">&middot;</span>
                            <span><?= esc((string) $kasus['nama_prodi']) ?></span>
                            <?php if (!empty($kasus['angkatan'])): ?>
                                <span class="ecc-num">(Angkatan <?= esc((string) $kasus['angkatan']) ?>)</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <?= ketarunaan_penetapan_badge($rawPenetapan) ?>
                <?= ketarunaan_penyelesaian_badge($rawPenyelesaian) ?>
                <?= ketarunaan_validasi_badge($badgeValidasiKey) ?>
            </div>
        </div>

        <!-- Navigasi 5 Tab -->
        <nav class="ecc-tab-nav" aria-label="Tab detail kasus pelanggaran">
            <?php foreach ($tabs as $tabKey => $tabLabel): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '?tab=' . $tabKey) ?>"
                   class="ecc-tab-link <?= $activeTab === $tabKey ? 'active' : '' ?>">
                    <?= esc($tabLabel) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- TAB 1: RINGKASAN -->
        <?php if ($activeTab === 'ringkasan'): ?>
            <div class="row g-3">
                <div class="col-lg-7 d-flex flex-column gap-3">
                    <div class="ecc-panel-box">
                        <h2 class="h6 fw-bold text-dark mb-3">Informasi Kasus</h2>
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
                                <?= !empty($canViewFull) ? esc((string) ($kasus['lokasi'] ?: '-')) : 'Tersamarkan (Akses Terbatas)' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Pelapor / Sumber Data</span>
                            <span class="fw-semibold text-dark text-end">
                                <?php if (!empty($canViewFull)): ?>
                                    <?= esc((string) ($kasus['pelapor'] ?: '-')) ?> &middot; <?= esc((string) ($kasus['sumber_data'] ?: '-')) ?>
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
                        <h2 class="h6 fw-bold text-dark mb-3">Ringkasan Kronologi Kasus</h2>
                        <?php if (!empty($canViewFull)): ?>
                            <div class="text-dark" style="font-size: 0.875rem; line-height: 1.6;">
                                <?= !empty($kasus['uraian']) ? nl2br(esc((string) $kasus['uraian'])) : '<span class="text-secondary">Belum ada uraian kronologi.</span>' ?>
                            </div>
                        <?php else: ?>
                            <div class="ecc-lock-notice d-flex align-items-center gap-2">
                                <i class="bi bi-lock text-secondary"></i>
                                <span>Ringkasan kasus hanya tersedia bagi petugas berwenang.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <h2 class="h6 fw-bold text-dark mb-3">Pembinaan dan Pemantauan</h2>
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
                                <?= !empty($canViewFull) ? esc((string) ($kasus['tindak_lanjut'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Catatan Pemantauan</span>
                            <span class="fw-semibold text-dark text-end">
                                <?= !empty($canViewFull) ? esc((string) ($kasus['catatan_pemantauan'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 d-flex flex-column gap-3">
                    <div class="ecc-panel-box">
                        <h2 class="h6 fw-bold text-dark mb-3">Status &amp; Kelengkapan Kasus</h2>
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
                                <?= !empty($checklist['bap_ada']) ? 'Tersedia' : 'Belum Diunggah' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Surat Penetapan</span>
                            <span class="fw-semibold <?= !empty($checklist['penetapan_ada']) ? 'text-success' : (!empty($checklist['penetapan_wajib']) ? 'text-danger' : 'text-secondary') ?>">
                                <?= !empty($checklist['penetapan_ada']) ? 'Tersedia' : (!empty($checklist['penetapan_wajib']) ? 'Wajib Dilengkapi' : 'Opsional') ?>
                            </span>
                        </div>

                        <?php if (!empty($canManage)): ?>
                            <div class="d-flex flex-wrap gap-2 pt-3 mt-2 border-top">
                                <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3">
                                    Pemeriksaan
                                </a>
                                <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3">
                                    Penetapan
                                </a>
                                <a href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus) ?>" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3">
                                    Dokumen
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-bold text-dark mb-0">Dokumen Terkait</h2>
                            <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '?tab=dokumen') ?>" class="text-decoration-none fw-semibold" style="font-size: 0.8125rem;">
                                Lihat Semua
                            </a>
                        </div>

                        <?php if (empty($dokumenList)): ?>
                            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Belum ada dokumen bukti yang diunggah.</p>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($dokumenList as $dok): ?>
                                    <?php
                                    $namaBerkas   = basename((string) ($dok['nama_berkas'] ?? 'Dokumen.pdf'));
                                    $klasifDoc    = (($dok['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas') ? 'Sangat Terbatas' : 'Terbatas';
                                    $bolehAkses   = !empty($canViewFull) && !empty($dok['can_access']);
                                    $urlPreview   = base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode($namaBerkas) . '&inline=1');
                                    ?>
                                    <div class="border rounded-3 p-2 d-flex align-items-center justify-content-between gap-2" style="border-color: #e2e8f0 !important;">
                                        <div class="text-truncate">
                                            <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8125rem;">
                                                <?= esc($namaBerkas) ?>
                                            </div>
                                            <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                <?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?>
                                                &middot; <?= esc($klasifDoc) ?>
                                                <?php if (!empty($dok['tanggal_unggah'])): ?>
                                                    &middot; <?= esc(ketarunaan_format_tanggal((string) $dok['tanggal_unggah'], true)) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($bolehAkses): ?>
                                            <a href="<?= esc($urlPreview) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm ecc-control-sm px-3 flex-shrink-0">
                                                Pratinjau
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">
                                                <i class="bi bi-lock me-1"></i>Terkunci (Terbatas)
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
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h6 fw-bold text-dark mb-0">Data Pemeriksaan &amp; Pihak Kasus</h2>
                <?php if (!empty($canManage)): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-outline-primary btn-sm ecc-control-sm px-3">
                        Kelola Pemeriksaan &amp; Pihak
                    </a>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box mb-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Pemeriksaan</span>
                            <span class="fw-semibold text-dark ecc-num">
                                <?= !empty($kasus['tanggal_pemeriksaan']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_pemeriksaan'], true)) : '-' ?>
                            </span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Pemeriksa</span>
                            <span class="fw-semibold text-dark"><?= esc((string) ($kasus['pemeriksa'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tingkat Pelanggaran</span>
                            <span class="fw-semibold text-dark"><?= esc($tingkatLabel) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Dasar Peraturan</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($kasus['dasar_peraturan'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Berkas BAP</span>
                            <span class="text-end">
                                <?php if (empty($kasus['berkas_bap'])): ?>
                                    <span class="text-secondary">Belum diunggah</span>
                                <?php elseif (!empty($canViewFull)): ?>
                                    <a href="<?= esc(base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode(basename((string) $kasus['berkas_bap'])) . '&inline=1')) ?>"
                                       target="_blank" rel="noopener noreferrer" class="fw-semibold text-decoration-none">
                                        <?= esc(basename((string) $kasus['berkas_bap'])) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Terkunci (Terbatas)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <?php if (!empty($canViewFull)): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold text-dark mb-1" style="font-size: 0.875rem;">Ringkasan Klarifikasi</div>
                            <p class="text-secondary mb-0" style="font-size: 0.875rem; line-height: 1.6;">
                                <?= !empty($kasus['ringkasan_klarifikasi']) ? nl2br(esc((string) $kasus['ringkasan_klarifikasi'])) : 'Belum dicatat.' ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold text-dark mb-1" style="font-size: 0.875rem;">Hasil Pemeriksaan</div>
                            <p class="text-secondary mb-0" style="font-size: 0.875rem; line-height: 1.6;">
                                <?= !empty($kasus['hasil_pemeriksaan']) ? nl2br(esc((string) $kasus['hasil_pemeriksaan'])) : 'Belum dicatat.' ?>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="ecc-lock-notice d-flex align-items-center gap-2">
                        <i class="bi bi-lock text-secondary"></i>
                        <span>Rincian klarifikasi dan hasil pemeriksaan hanya tersedia bagi petugas berwenang.</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box p-0 overflow-hidden">
                <div class="px-3 py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h3 class="h6 fw-bold text-dark mb-0">Daftar Pihak Terkait Kasus</h3>
                    <span class="text-secondary ecc-num" style="font-size: 0.8125rem;"><?= count($pihakList) ?> Pihak</span>
                </div>
                <?php if (empty($pihakList)): ?>
                    <div class="p-4 text-center text-secondary" style="font-size: 0.875rem;">
                        Belum ada pihak tambahan yang tercatat pada kasus ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table ecc-table-pl mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Pihak</th>
                                    <th>Jenis</th>
                                    <th>Identitas / Nomor Taruna</th>
                                    <th>Peran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pihakList as $ph): ?>
                                    <?php $isEksternal = (($ph['jenis_pihak'] ?? 'taruna') === 'eksternal'); ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= esc((string) ($ph['display_nama'] ?? '-')) ?></td>
                                        <td>
                                            <span class="badge <?= $isEksternal ? 'bg-light text-dark border' : 'bg-primary bg-opacity-10 text-primary border border-primary-subtle' ?> rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                <?= $isEksternal ? 'Eksternal' : 'Taruna' ?>
                                            </span>
                                        </td>
                                        <td class="ecc-num text-secondary"><?= esc((string) ($ph['display_nomor'] ?? '-')) ?></td>
                                        <td><?= esc((string) ($ph['peran'] ?: '-')) ?></td>
                                        <td class="text-secondary">
                                            <?= !empty($canViewFull) ? esc((string) ($ph['keterangan'] ?: '-')) : 'Terbatas' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        <!-- TAB 3: PENETAPAN & TINDAK LANJUT -->
        <?php elseif ($activeTab === 'penetapan'): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h6 fw-bold text-dark mb-0">Keputusan Aktif &amp; Riwayat Tindak Lanjut</h2>
                <?php if (!empty($canManage)): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-primary btn-sm ecc-control-sm px-3 btn-tactile" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut">
                            <i class="bi bi-plus-lg me-1"></i>Catat Tindak Lanjut
                        </button>
                        <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3">
                            Kelola Penetapan
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box mb-3">
                <h3 class="h6 fw-bold text-dark mb-3">Penetapan &amp; Sanksi Aktif</h3>
                <div class="row g-3">
                    <div class="col-md-6">
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
                                <?= !empty($canViewFull) ? esc((string) ($kasus['sanksi'] ?: '-')) : 'Tersedia bagi petugas berwenang' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
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

            <div class="ecc-panel-box p-0 overflow-hidden">
                <div class="px-3 py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h3 class="h6 fw-bold text-dark mb-1">Riwayat Tindak Lanjut &amp; Banding</h3>
                        <p class="text-secondary mb-0" style="font-size: 0.8125rem;">
                            Setiap penetapan awal, pemantauan pembinaan, dan proses banding tersimpan terpisah tanpa menghapus keputusan sebelumnya.
                        </p>
                    </div>
                    <span class="text-secondary ecc-num" style="font-size: 0.8125rem;"><?= count($tindakLanjutList) ?> Catatan</span>
                </div>

                <?php if (empty($tindakLanjutList)): ?>
                    <div class="p-4 text-center text-secondary" style="font-size: 0.875rem;">
                        Belum ada catatan tindak lanjut atau banding.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table ecc-table-pl mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Status Saat Itu</th>
                                    <th>Nomor Dasar</th>
                                    <th>Sanksi / Catatan</th>
                                    <th>PIC &amp; Target</th>
                                    <th>Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tindakLanjutList as $tl): ?>
                                    <?php
                                    $jenisKey = (string) ($tl['jenis_tindakan'] ?? 'pemantauan');
                                    $jenisLbl = $jenisTindakanMap[$jenisKey] ?? ucfirst($jenisKey);
                                    $stPenTl  = (string) ($tl['status_penetapan_saat_itu'] ?? 'dugaan');
                                    $stSlsTl  = (string) ($tl['status_penyelesaian_saat_itu'] ?? 'proses');
                                    $fileTl   = !empty($tl['berkas_pendukung']) ? basename((string) $tl['berkas_pendukung']) : '';
                                    ?>
                                    <tr>
                                        <td class="ecc-num text-nowrap">
                                            <?= !empty($tl['tanggal_tindakan']) ? esc(ketarunaan_format_tanggal((string) $tl['tanggal_tindakan'], true)) : '-' ?>
                                        </td>
                                        <td>
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
                                        <td class="ecc-num"><?= esc((string) ($tl['nomor_dasar'] ?: '-')) ?></td>
                                        <td>
                                            <?= !empty($canViewFull) ? esc((string) ($tl['sanksi_tindakan'] ?: '-')) : 'Terbatas' ?>
                                            <?php if (!empty($tl['dicatat_oleh'])): ?>
                                                <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                                                    Oleh: <?= esc((string) $tl['dicatat_oleh']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><?= esc((string) ($tl['pic'] ?: '-')) ?></div>
                                            <?php if (!empty($tl['target_selesai'])): ?>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    Target: <?= esc(ketarunaan_format_tanggal((string) $tl['target_selesai'], true)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <?php if ($fileTl === ''): ?>
                                                <span class="text-secondary">-</span>
                                            <?php elseif (!empty($canViewFull)): ?>
                                                <a href="<?= esc(base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode($fileTl) . '&inline=1')) ?>"
                                                   target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm ecc-control-sm px-3">
                                                    Lihat
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
                <?php endif; ?>
            </div>

        <!-- TAB 4: DOKUMEN BUKTI -->
        <?php elseif ($activeTab === 'dokumen'): ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 class="h6 fw-bold text-dark mb-0">Dokumen Bukti Kasus</h2>
                <?php if (!empty($canManage)): ?>
                    <a href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus) ?>" class="btn btn-outline-primary btn-sm ecc-control-sm px-3">
                        Kelola Dokumen &amp; Pengajuan
                    </a>
                <?php endif; ?>
            </div>

            <div class="alert alert-light border rounded-3 mb-3 py-2 px-3 text-secondary" style="font-size: 0.8125rem; border-color: #e2e8f0 !important;">
                Dokumen kasus bersifat terbatas. Pratinjau dan unduhan hanya terbuka bagi petugas berwenang sesuai klasifikasi akses.
            </div>

            <div class="ecc-panel-box p-0 overflow-hidden">
                <?php if (empty($dokumenList)): ?>
                    <div class="p-4 text-center text-secondary" style="font-size: 0.875rem;">
                        Belum ada dokumen bukti yang diunggah untuk kasus ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table ecc-table-pl mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Berkas</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Klasifikasi</th>
                                    <th>Tanggal Unggah</th>
                                    <th>Ukuran</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dokumenList as $dok): ?>
                                    <?php
                                    $namaBerkas = basename((string) ($dok['nama_berkas'] ?? 'Dokumen.pdf'));
                                    $klasifKey  = (string) ($dok['klasifikasi'] ?? 'terbatas');
                                    $klasifLbl  = ($klasifKey === 'sangat_terbatas') ? 'Sangat Terbatas' : 'Terbatas';
                                    $bolehAkses = !empty($canViewFull) && !empty($dok['can_access']);
                                    $urlBase    = base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode($namaBerkas));
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= esc($namaBerkas) ?></td>
                                        <td><?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?></td>
                                        <td>
                                            <span class="badge <?= $klasifKey === 'sangat_terbatas' ? 'bg-danger bg-opacity-10 text-danger border border-danger-subtle' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle' ?> rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                <?= esc($klasifLbl) ?>
                                            </span>
                                        </td>
                                        <td class="ecc-num">
                                            <?= !empty($dok['tanggal_unggah']) ? esc(ketarunaan_format_tanggal((string) $dok['tanggal_unggah'], true)) : '-' ?>
                                        </td>
                                        <td class="ecc-num text-secondary">
                                            <?= number_format((int) ($dok['ukuran_kb'] ?? 0), 0, ',', '.') ?> KB
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($bolehAkses): ?>
                                                <a href="<?= esc($urlBase . '&inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm ecc-control-sm px-3 me-1">
                                                    Pratinjau
                                                </a>
                                                <a href="<?= esc($urlBase) ?>" class="btn btn-primary btn-sm ecc-control-sm px-3">
                                                    Unduh
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-lock me-1"></i>Terkunci (Terbatas)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        <!-- TAB 5: RIWAYAT VALIDASI -->
        <?php else: ?>
            <div class="alert alert-light border rounded-3 mb-3 py-2 px-3 text-secondary" style="font-size: 0.8125rem; border-color: #e2e8f0 !important;">
                Validasi pencatatan tidak menggantikan kewenangan penetapan kasus. Penetapan dugaan, terbukti, tidak terbukti, atau dibatalkan dilakukan sesuai ketentuan yang berlaku oleh pihak berwenang.
            </div>

            <div class="ecc-panel-box mb-3">
                <h2 class="h6 fw-bold text-dark mb-3">Status Validasi Terkini</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status Validasi</span>
                            <span><?= ketarunaan_validasi_badge($badgeValidasiKey) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Diajukan Oleh</span>
                            <span class="fw-semibold text-dark"><?= esc((string) ($kasus['diajukan_oleh'] ?: '-')) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Divalidasi Oleh</span>
                            <span class="fw-semibold text-dark"><?= esc((string) ($kasus['divalidasi_oleh'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Tanggal Validasi</span>
                            <span class="fw-semibold text-dark ecc-num">
                                <?= !empty($kasus['tanggal_validasi']) ? esc(ketarunaan_format_tanggal((string) $kasus['tanggal_validasi'], true)) : '-' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php if (!empty($kasus['catatan_validasi'])): ?>
                    <div class="mt-3 pt-3 border-top" style="font-size: 0.875rem;">
                        <span class="text-secondary">Catatan Validasi:</span>
                        <span class="text-dark fw-medium ms-1"><?= esc((string) $kasus['catatan_validasi']) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="ecc-panel-box p-0 overflow-hidden">
                <div class="px-3 py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h3 class="h6 fw-bold text-dark mb-0">Riwayat Aktivitas Validasi</h3>
                    <span class="text-secondary ecc-num" style="font-size: 0.8125rem;"><?= count($riwayatList) ?> Aktivitas</span>
                </div>
                <?php if (empty($riwayatList)): ?>
                    <div class="p-4 text-center text-secondary" style="font-size: 0.875rem;">
                        Belum ada riwayat aktivitas validasi.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table ecc-table-pl mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelaku</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayatList as $rw): ?>
                                    <?php
                                    $rwStatusRaw = (string) ($rw['status'] ?? 'draf');
                                    $rwBadgeKey  = ($rwStatusRaw === 'perlu_perbaikan') ? 'dikembalikan' : $rwStatusRaw;
                                    ?>
                                    <tr>
                                        <td class="ecc-num text-nowrap">
                                            <?= !empty($rw['tanggal']) ? esc(ketarunaan_format_tanggal((string) $rw['tanggal'], true)) : '-' ?>
                                        </td>
                                        <td class="fw-semibold text-dark"><?= esc((string) ($rw['pelaku'] ?? '-')) ?></td>
                                        <td><?= esc((string) ($rw['aksi'] ?? '-')) ?></td>
                                        <td><?= ketarunaan_validasi_badge($rwBadgeKey) ?></td>
                                        <td class="text-secondary"><?= esc((string) ($rw['catatan'] ?: '-')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Catat Tindak Lanjut (hanya untuk pengelola) -->
<?php if (!empty($canManage)): ?>
    <div class="modal fade" id="modalTindakLanjut" tabindex="-1" aria-labelledby="modalTindakLanjutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus . '/tindak-lanjut') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-header border-bottom px-4 py-3">
                        <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalTindakLanjutLabel">Catat Tindak Lanjut / Banding</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tlTanggal" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Tanggal Tindakan</label>
                                <input type="text" id="tlTanggal" name="tanggal_tindakan" class="form-control ecc-control-sm ecc-num js-flatpickr-date" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="tlPenyelesaian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Status Penyelesaian</label>
                                <select id="tlPenyelesaian" name="status_penyelesaian" class="form-select ecc-control-sm" required>
                                    <option value="proses" <?= $rawPenyelesaian === 'proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="selesai" <?= $rawPenyelesaian === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="banding" <?= $rawPenyelesaian === 'banding' ? 'selected' : '' ?>>Banding</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tlNomorDasar" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Nomor Dasar / Nota Dinas</label>
                                <input type="text" id="tlNomorDasar" name="nomor_dasar" class="form-control ecc-control-sm ecc-num" placeholder="Opsional">
                            </div>
                            <div class="col-md-6">
                                <label for="tlPic" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">PIC Penanggung Jawab</label>
                                <select id="tlPic" name="pic" class="form-select ecc-control-sm">
                                    <?php $currentPic = (string) ($kasus['pic_tindak_lanjut'] ?? 'Unit Ketarunaan'); ?>
                                    <?php foreach (($refs['pic_options'] ?? []) as $picOption): ?>
                                        <option value="<?= esc((string) $picOption) ?>" <?= $currentPic === $picOption ? 'selected' : '' ?>>
                                            <?= esc((string) $picOption) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tlTargetSelesai" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Target Selesai</label>
                                <input type="text" id="tlTargetSelesai" name="target_selesai" class="form-control ecc-control-sm ecc-num js-flatpickr-date" placeholder="Opsional (YYYY-MM-DD)">
                            </div>
                            <div class="col-md-6">
                                <label for="tlBerkas" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Lampiran Pendukung</label>
                                <input type="file" id="tlBerkas" name="berkas_pendukung" class="form-control ecc-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text" style="font-size: 0.75rem;">PDF, JPG, atau PNG maksimal 5 MB.</div>
                            </div>
                            <div class="col-12">
                                <label for="tlUraian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Uraian Tindak Lanjut / Pemantauan</label>
                                <textarea id="tlUraian" name="sanksi_tindakan" rows="3" class="form-control" style="border-radius: 8px; font-size: 0.875rem;" placeholder="Tuliskan perkembangan pembinaan, pemantauan, atau catatan banding." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm ecc-control-sm px-4 btn-tactile">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Kembalikan Validasi (hanya untuk validator berwenang) -->
<?php if ($canValidateNow): ?>
    <div class="modal fade" id="modalKembalikanKasus" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="<?= base_url('ketarunaan/pelanggaran/validasi/' . $idKasus) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="perlu_perbaikan">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalKembalikanLabel">Kembalikan Pencatatan</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label for="inputCatatanPerbaikan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Catatan Perbaikan</label>
                        <textarea id="inputCatatanPerbaikan" name="catatan_validasi" rows="3" class="form-control" style="border-radius: 8px; font-size: 0.875rem;" placeholder="Jelaskan data atau dokumen yang perlu dilengkapi." required></textarea>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm ecc-control-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm ecc-control-sm px-4 text-dark fw-semibold btn-tactile">Kembalikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-flatpickr-date', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    const btnHapus = document.getElementById('btnHapusKasus');
    const formHapus = document.getElementById('formDeleteKasus');
    if (btnHapus && formHapus) {
        btnHapus.addEventListener('click', function() {
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
                }).then(function(result) {
                    if (result.isConfirmed) {
                        formHapus.submit();
                    }
                });
            } else if (confirm('Draf kasus pelanggaran ini akan dihapus.')) {
                formHapus.submit();
            }
        });
    }

    const btnSetujui = document.getElementById('btnSetujuiKasus');
    const formSetujui = document.getElementById('formApproveKasus');
    if (btnSetujui && formSetujui) {
        btnSetujui.addEventListener('click', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validasi Kasus?',
                    text: 'Pencatatan kasus ini akan disahkan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Validasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        formSetujui.submit();
                    }
                });
            } else if (confirm('Pencatatan kasus ini akan disahkan.')) {
                formSetujui.submit();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
