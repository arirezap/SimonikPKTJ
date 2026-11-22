<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Profil Taruna - <?= esc($taruna['nama_lengkap']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-profil-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .nav-tabs-ecc {
        border-bottom: 1px solid #e2e8f0;
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .nav-tabs-ecc .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #475569;
        font-weight: 600;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .nav-tabs-ecc .nav-link:hover {
        color: #1e40af;
        background-color: #f8fafc;
    }
    .nav-tabs-ecc .nav-link.active {
        color: #1e40af;
        border-bottom-color: #1e40af;
        background-color: #eff6ff;
    }
    .foto-upload-box {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 24px;
        background-color: #f8fafc;
        text-align: center;
    }
    .ref-status-card {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid transparent;
        height: 100%;
    }
    .taruna-sort-btn {
        background: none;
        border: 0;
        padding: 0;
        color: inherit;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .taruna-sort-btn:hover,
    .taruna-sort-btn:focus-visible {
        color: #1e40af;
    }
    .profil-table-wrap {
        max-height: 480px;
        overflow-y: auto;
    }
    .profil-table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: #f8fafc;
        padding: 12px 16px;
    }
    .profil-table-wrap tbody td {
        padding: 12px 16px;
    }
    @media (max-width: 767.98px) {
        .taruna-touch-ctrl {
            min-height: 44px !important;
        }
        #pane-identitas .form-control,
        #pane-identitas .form-select,
        #pane-pendidikan .form-control,
        #pane-pendidikan .form-select,
        #pane-kontak .form-control,
        #pane-kontak .form-select,
        #modalCatatPendidikan .form-control,
        #modalCatatPendidikan .form-select {
            font-size: 16px !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .nav-tabs-ecc .nav-link,
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
    @media print {
        .nav-tabs-ecc,
        .d-print-none,
        .btn,
        .alert {
            display: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $nomorTaruna       = (string) $taruna['nomor_taruna'];
    $tingkatRomawi     = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    $canEditOwnProfile = !empty($can_edit_own_profile);
    $canEditProfile    = !empty($can_manage) || $canEditOwnProfile;
    $isSelfTaruna      = !empty($is_self_taruna);
    $fotoPath          = !empty($taruna['foto']) && file_exists(FCPATH . 'assets/uploads/taruna/' . $taruna['foto'])
        ? base_url('assets/uploads/taruna/' . $taruna['foto'])
        : null;
?>
<div class="container-fluid px-0">

    <!-- Flash Feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 1. BREADCRUMB & HEADER PAGE -->
    <div class="mb-3">
        <div class="text-muted small mb-2">
            <?php if (!$isSelfTaruna): ?>
                <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">Ketarunaan</a>
                <span class="mx-1">›</span>
                <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
                <span class="mx-1">›</span>
            <?php else: ?>
                <span>Ketarunaan</span>
                <span class="mx-1">›</span>
            <?php endif; ?>
            <span class="text-dark fw-medium"><?= $isSelfTaruna ? 'Profil Taruna Saya' : 'Profil Taruna' ?></span>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1"><?= $isSelfTaruna ? 'Profil Taruna Saya' : 'Profil Taruna' ?></h1>
                <?php if (!$isSelfTaruna): ?>
                    <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none small text-primary fw-semibold d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Database Taruna</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2. KARTU IDENTITAS RINGKAS ATAS -->
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-profil-lg">
                    <?php if ($fotoPath): ?>
                        <img src="<?= esc($fotoPath) ?>" alt="Foto <?= esc($taruna['nama_lengkap']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="bi bi-person-fill"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1"><?= esc($taruna['nama_lengkap']) ?></h2>
                    <div class="d-flex flex-wrap align-items-center column-gap-3 row-gap-1 small text-muted">
                        <span>Nomor Taruna <strong class="tabular-nums" style="color: #1e40af;"><?= esc($nomorTaruna) ?></strong></span>
                        <strong style="color: #1e40af;"><?= esc($taruna['kode_prodi'] ?? 'RSTJ') ?></strong>
                        <span>Angkatan <strong class="tabular-nums" style="color: #1e40af;"><?= esc(ketarunaan_format_angkatan($taruna)) ?></strong></span>
                        <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                    </div>
                </div>
            </div>

            <?php if ($can_manage): ?>
                <div class="d-flex flex-wrap gap-2 d-print-none">
                    <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 36px;">
                        <span>Ubah Status</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. NAVIGASI 5 TAB PROFIL -->
    <div class="bento-card p-0 mb-4 overflow-hidden">
        <ul class="nav nav-tabs nav-tabs-ecc px-3 pt-2 bg-white d-print-none" id="profilTarunaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link small taruna-touch-ctrl <?= $active_tab === 'identitas' ? 'active' : '' ?>" id="tab-identitas-btn" data-bs-toggle="tab" data-bs-target="#pane-identitas" type="button" role="tab" aria-controls="pane-identitas" aria-selected="<?= $active_tab === 'identitas' ? 'true' : 'false' ?>">
                    Identitas Utama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small taruna-touch-ctrl <?= $active_tab === 'pendidikan' ? 'active' : '' ?>" id="tab-pendidikan-btn" data-bs-toggle="tab" data-bs-target="#pane-pendidikan" type="button" role="tab" aria-controls="pane-pendidikan" aria-selected="<?= $active_tab === 'pendidikan' ? 'true' : 'false' ?>">
                    Data Pendidikan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small taruna-touch-ctrl <?= $active_tab === 'kontak' ? 'active' : '' ?>" id="tab-kontak-btn" data-bs-toggle="tab" data-bs-target="#pane-kontak" type="button" role="tab" aria-controls="pane-kontak" aria-selected="<?= $active_tab === 'kontak' ? 'true' : 'false' ?>">
                    Kontak dan Keluarga
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small taruna-touch-ctrl <?= $active_tab === 'riwayat-status' ? 'active' : '' ?>" id="tab-riwayat-status-btn" data-bs-toggle="tab" data-bs-target="#pane-riwayat-status" type="button" role="tab" aria-controls="pane-riwayat-status" aria-selected="<?= $active_tab === 'riwayat-status' ? 'true' : 'false' ?>">
                    Riwayat Status
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small taruna-touch-ctrl <?= $active_tab === 'riwayat-pendidikan' ? 'active' : '' ?>" id="tab-riwayat-pendidikan-btn" data-bs-toggle="tab" data-bs-target="#pane-riwayat-pendidikan" type="button" role="tab" aria-controls="pane-riwayat-pendidikan" aria-selected="<?= $active_tab === 'riwayat-pendidikan' ? 'true' : 'false' ?>">
                    Riwayat Pendidikan
                </button>
            </li>
        </ul>

        <div class="tab-content p-3 p-md-4 bg-white">

            <!-- ==========================================================
                 TAB 1: IDENTITAS UTAMA
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'identitas' ? 'show active' : '' ?>" id="pane-identitas" role="tabpanel" aria-labelledby="tab-identitas-btn">
                <form id="formProfilIdentitas" method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/identitas') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <h2 class="h5 fw-bold text-dark mb-1">Identitas Utama</h2>
                            <p class="text-muted small mb-3"><?= $isSelfTaruna ? 'Data pribadi dapat diperbarui langsung; foto resmi dan data kampus memerlukan validasi petugas.' : 'Informasi identitas dasar taruna.' ?></p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="profilNoTaruna" class="form-label small fw-semibold">Nomor Taruna <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="profilNoTaruna" class="form-control bg-light tabular-nums" value="<?= esc($nomorTaruna) ?>" readonly style="height: 40px;">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilNik" class="form-label small fw-semibold">NIK</label>
                                    <?php $canEditNik = $canEditProfile && $can_view_sensitive; ?>
                                    <div class="input-group">
                                        <input type="text" id="profilNik" name="nik" class="form-control tabular-nums <?= $canEditNik ? '' : 'bg-light' ?>" value="<?= esc($canEditNik ? old('nik', $taruna['nik'] ?? '') : ketarunaan_mask_nik($taruna['nik'] ?? '', false)) ?>" maxlength="16" inputmode="numeric" placeholder="16 digit NIK" <?= $canEditNik ? '' : 'readonly' ?> style="height: 40px;">
                                        <?php if (!$canEditNik): ?>
                                            <span class="input-group-text bg-light text-muted" title="<?= $can_view_sensitive ? 'NIK Terdaftar: ' . esc($taruna['nik'] ?? '-') : 'Dilindungi kebijakan privasi' ?>">
                                                <i class="bi bi-lock-fill"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilNamaLengkap" class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="profilNamaLengkap" name="nama_lengkap" class="form-control <?= !$can_manage ? 'bg-light' : '' ?>" value="<?= esc(old('nama_lengkap', $taruna['nama_lengkap'])) ?>" required style="height: 40px;" <?= !$can_manage ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilNamaPanggilan" class="form-label small fw-semibold">Nama Panggilan</label>
                                    <input type="text" id="profilNamaPanggilan" name="nama_panggilan" class="form-control" value="<?= esc(old('nama_panggilan', $taruna['nama_panggilan'] ?? '')) ?>" style="height: 40px;" <?= !$canEditProfile ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilJenisKelamin" class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select id="profilJenisKelamin" name="jenis_kelamin" class="form-select" style="height: 40px;" <?= !$canEditProfile ? 'disabled' : '' ?>>
                                        <option value="L" <?= old('jenis_kelamin', $taruna['jenis_kelamin']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= old('jenis_kelamin', $taruna['jenis_kelamin']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilAgama" class="form-label small fw-semibold">Agama</label>
                                    <select id="profilAgama" name="agama" class="form-select" style="height: 40px;" <?= !$canEditProfile ? 'disabled' : '' ?>>
                                        <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm): ?>
                                            <option value="<?= esc($agm) ?>" <?= old('agama', $taruna['agama'] ?? 'Islam') === $agm ? 'selected' : '' ?>><?= esc($agm) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilTempatLahir" class="form-label small fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" id="profilTempatLahir" name="tempat_lahir" class="form-control" value="<?= esc(old('tempat_lahir', $taruna['tempat_lahir'] ?? '')) ?>" placeholder="Kota tempat lahir" required style="height: 40px;" <?= !$canEditProfile ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label for="inputTanggalLahir" class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="text" id="inputTanggalLahir" name="tanggal_lahir" class="form-control bg-white" value="<?= esc(old('tanggal_lahir', $taruna['tanggal_lahir'] ?? '')) ?>" placeholder="Pilih tanggal lahir" style="height: 40px;" <?= !$canEditProfile ? 'readonly' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1">Foto Taruna</h3>
                                    <p class="text-muted small mb-3"><?= $isSelfTaruna ? 'Foto baru memerlukan validasi petugas sebelum tampil di profil.' : 'Unggah foto resmi taruna.' ?></p>

                                    <div class="foto-upload-box mb-3">
                                        <div id="fotoPreviewBox" data-initial-src="<?= esc($fotoPath ?? '') ?>" class="mx-auto mb-3 rounded-4 d-flex align-items-center justify-content-center overflow-hidden bg-white border" style="width: 120px; height: 144px;">
                                            <?php if ($fotoPath): ?>
                                                <img id="previewFotoTaruna" src="<?= esc($fotoPath) ?>" alt="Foto Taruna" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i id="iconPlaceholderFoto" class="bi bi-person-bounding-box text-secondary fs-1"></i>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($canEditProfile): ?>
                                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                                                <label for="inputFotoTaruna" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 cursor-pointer taruna-touch-ctrl" style="height: 36px;">
                                                    <i class="bi bi-upload"></i>
                                                    <span>Unggah Foto</span>
                                                </label>
                                                <button type="button" id="btnBatalFotoTaruna" class="btn btn-outline-secondary btn-sm rounded-3 px-3 fw-semibold d-none align-items-center justify-content-center taruna-touch-ctrl" style="height: 36px;">
                                                    Batal Pilih
                                                </button>
                                            </div>
                                            <input type="file" id="inputFotoTaruna" name="foto" accept="image/png,image/jpeg,image/jpg" class="d-none">
                                        <?php endif; ?>
                                        <div id="fotoInfoLabel" class="text-muted small mt-2">JPG/PNG Maks 250 KB (kompresi otomatis)</div>
                                    </div>

                                    <?php
                                        $pendingFotoFile   = basename((string) ($taruna['foto_pending'] ?? ''));
                                        $pendingFotoStatus = (string) ($taruna['foto_pending_status'] ?? '');
                                        $pendingFotoUrl    = ($pendingFotoFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $pendingFotoFile))
                                            ? base_url('assets/uploads/taruna/' . $pendingFotoFile)
                                            : null;
                                    ?>
                                    <?php if ($pendingFotoStatus === 'diajukan' && $pendingFotoUrl !== null): ?>
                                        <div class="p-3 rounded-3 border mb-2" style="background-color: #fffbeb; border-color: #fde68a !important;">
                                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                                <span class="badge rounded-pill px-2 py-1 fw-semibold" style="background-color: #fef3c7; color: #92400e;">Menunggu Validasi</span>
                                                <a href="<?= esc($pendingFotoUrl) ?>" target="_blank" rel="noopener noreferrer" class="small fw-semibold text-decoration-none" style="color: #1e40af;">Lihat Foto</a>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= esc($pendingFotoUrl) ?>" alt="Foto pengajuan baru" class="rounded-3 border flex-shrink-0" style="width: 48px; height: 56px; object-fit: cover;">
                                                <div class="small" style="color: #475569;">
                                                    <?= !empty($can_validate_foto) ? 'Taruna mengajukan pembaruan foto profil.' : 'Menunggu persetujuan petugas sebelum menggantikan foto resmi.' ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($can_validate_foto)): ?>
                                                <div class="d-flex gap-2 mt-2 pt-2 border-top d-print-none" style="border-color: #fde68a !important;">
                                                    <button type="button" id="btnSetujuiFotoPending" class="btn btn-success btn-sm rounded-3 px-2 fw-semibold flex-grow-1 btn-tactile taruna-touch-ctrl" style="min-height: 32px;">Setujui Foto</button>
                                                    <button type="button" id="btnTolakFotoPending" class="btn btn-outline-danger btn-sm rounded-3 px-2 fw-semibold flex-grow-1 btn-tactile taruna-touch-ctrl" style="min-height: 32px;">Kembalikan</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($pendingFotoStatus === 'perlu_perbaikan'): ?>
                                        <div class="p-3 rounded-3 border bg-danger-subtle border-danger-subtle text-danger-emphasis mb-2 small">
                                            <strong class="d-block">Pengajuan Foto Dikembalikan</strong>
                                            <span><?= esc($taruna['foto_pending_catatan'] ?: 'Unggah ulang foto sesuai ketentuan.') ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top d-print-none">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 40px;">
                                <span>Simpan Draf</span>
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                                <span>Ajukan Validasi</span>
                            </button>
                        </div>
                    <?php elseif ($canEditOwnProfile): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top d-print-none">
                            <button type="submit" name="aksi_submit" value="mandiri" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                                <span>Simpan Identitas</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 2: DATA PENDIDIKAN
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'pendidikan' ? 'show active' : '' ?>" id="pane-pendidikan" role="tabpanel" aria-labelledby="tab-pendidikan-btn">
                <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/pendidikan') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <h2 class="h5 fw-bold text-dark mb-1">Data Pendidikan</h2>
                            <p class="text-muted small mb-3">Informasi program studi, angkatan, kelas, dan data pendidikan taruna</p>
                            <?php if (!$can_manage): ?>
                                <div class="p-3 rounded-3 small mb-3 d-flex align-items-center gap-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0; color: #475569;">
                                    <i class="bi bi-lock-fill"></i>
                                    <span>Mode baca saja. Data pendidikan hanya dapat diubah oleh pengelola ketarunaan.</span>
                                </div>
                            <?php endif; ?>

                            <?php
                                $oldProdiPendidikan    = (int) old('program_studi_id', (string) ($taruna['program_studi_id'] ?? 1));
                                $oldAngkatanPendidikan = (int) old('angkatan_id', (string) ($taruna['angkatan_id'] ?? 1));
                                $oldTingkatPendidikan  = (int) old('tingkat', (string) ($taruna['tingkat'] ?? 3));
                                $oldKelasPendidikan    = (int) old('kelas_id', (string) ($taruna['kelas_id'] ?? 0));
                                $oldSemesterPendidikan = (int) old('semester_aktif', (string) ($taruna['semester_aktif'] ?? 5));
                                $oldJalurPendidikan    = (string) old('jalur_penerimaan', (string) ($taruna['jalur_penerimaan'] ?? 'Pola Pembibitan'));
                                $oldAsalFormasi        = (string) old('asal_formasi', (string) ($taruna['asal_formasi'] ?? ''));
                            ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="selectProdiPendidikan" class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                                    <select id="selectProdiPendidikan" name="program_studi_id" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($prodi_list as $p): ?>
                                            <option value="<?= esc($p['id']) ?>" <?= $oldProdiPendidikan === (int) $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_prodi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="selectAngkatanPendidikan" class="form-label small fw-semibold">Angkatan <span class="text-danger">*</span></label>
                                    <select name="angkatan_id" id="selectAngkatanPendidikan" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($angkatan_list as $a): ?>
                                            <option value="<?= esc($a['id']) ?>" data-tahun="<?= esc($a['tahun_masuk']) ?>" <?= $oldAngkatanPendidikan === (int) $a['id'] ? 'selected' : '' ?>>
                                                <?= esc(ketarunaan_format_angkatan($a)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="inputTahunMasukReadonly" class="form-label small fw-semibold">Tahun Masuk</label>
                                    <input type="text" id="inputTahunMasukReadonly" class="form-control bg-light tabular-nums" value="<?= esc(!empty($taruna['tahun_masuk']) ? (string) $taruna['tahun_masuk'] : '-') ?>" readonly style="height: 40px;">
                                </div>

                                <div class="col-md-6">
                                    <label for="selectTingkatPendidikan" class="form-label small fw-semibold">Tingkat <span class="text-danger">*</span></label>
                                    <select id="selectTingkatPendidikan" name="tingkat" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($tingkatRomawi as $num => $rom): ?>
                                            <option value="<?= $num ?>" <?= $oldTingkatPendidikan === $num ? 'selected' : '' ?>><?= esc($rom) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="selectKelasPendidikan" class="form-label small fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <select id="selectKelasPendidikan" name="kelas_id" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php foreach ($kelas_list as $kls): ?>
                                            <?php
                                                $labelKelas = trim(
                                                    (!empty($kls['kode_prodi']) ? $kls['kode_prodi'] . ' ' : '')
                                                    . (!empty($kls['tingkat']) ? (string) $kls['tingkat'] : '')
                                                    . (string) $kls['nama_kelas']
                                                    . (!empty($kls['tahun_masuk']) ? ' • Angk. ' . ketarunaan_format_angkatan($kls) : '')
                                                );
                                            ?>
                                            <option value="<?= esc($kls['id']) ?>" data-prodi-id="<?= (int) ($kls['program_studi_id'] ?? 0) ?>" data-tingkat="<?= (int) ($kls['tingkat'] ?? 0) ?>" data-angkatan-id="<?= (int) ($kls['angkatan_id'] ?? 0) ?>" <?= $oldKelasPendidikan === (int) $kls['id'] ? 'selected' : '' ?>>
                                                <?= esc($labelKelas) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilSemesterPendidikan" class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                                    <select id="profilSemesterPendidikan" name="semester_aktif" class="form-select tabular-nums" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php for ($s = 1; $s <= 8; $s++): ?>
                                            <option value="<?= $s ?>" <?= $oldSemesterPendidikan === $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilJalurPendidikan" class="form-label small fw-semibold">Jalur Penerimaan <span class="text-danger">*</span></label>
                                    <select id="profilJalurPendidikan" name="jalur_penerimaan" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach (['Pola Pembibitan', 'Mandiri', 'Reguler'] as $jlr): ?>
                                            <option value="<?= esc($jlr) ?>" <?= $oldJalurPendidikan === $jlr ? 'selected' : '' ?>><?= esc($jlr) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="profilAsalFormasiPendidikan" class="form-label small fw-semibold">Asal Formasi</label>
                                    <select id="profilAsalFormasiPendidikan" name="asal_formasi" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <option value="" <?= $oldAsalFormasi === '' ? 'selected' : '' ?>>-- Belum Diisi --</option>
                                        <?php foreach (['Polbit', 'Mandiri', 'Pemerintah Daerah', 'Kemenhub RI', 'Mandiri / Umum'] as $frm): ?>
                                            <option value="<?= esc($frm) ?>" <?= $oldAsalFormasi === $frm ? 'selected' : '' ?>><?= esc($frm) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="p-3 p-md-4 rounded-4 border bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h3 class="h6 fw-bold text-dark mb-1">Status Pendidikan Saat Ini</h3>
                                    <p class="text-muted small mb-3">Status pendidikan taruna berdasarkan data terbaru</p>

                                    <div class="p-3 rounded-3 bg-white border mb-3">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 border-bottom">
                                            <span class="text-muted small">Status Pendidikan</span>
                                            <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 border-bottom">
                                            <span class="text-muted small">Berlaku sejak</span>
                                            <strong class="text-dark small tabular-nums">
                                                <?= !empty($status_aktif_row['mulai_berlaku']) ? esc(ketarunaan_format_tanggal($status_aktif_row['mulai_berlaku'])) : '-' ?>
                                            </strong>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2">
                                            <span class="text-muted small">Status Validasi</span>
                                            <?= ketarunaan_validasi_badge($status_aktif_row['status_validasi'] ?? 'tervalidasi') ?>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-3 small mb-3" style="background-color: #eff6ff; color: #1e40af;">
                                        Status pendidikan hanya dapat diubah melalui proses resmi sesuai ketentuan yang berlaku.
                                    </div>
                                </div>

                                <div class="d-flex gap-2 d-print-none">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-3 flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl text-nowrap" style="height: 36px;" onclick="document.getElementById('tab-riwayat-status-btn').click(); window.scrollTo({top: 0, behavior: 'smooth'});">
                                        Lihat Riwayat
                                    </button>
                                    <?php if ($can_manage): ?>
                                        <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-outline-primary btn-sm rounded-3 flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl text-nowrap" style="height: 36px;">
                                            Ubah Status
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top d-print-none">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">
                                Simpan Draf
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                                Ajukan Validasi
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 3: KONTAK DAN KELUARGA
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'kontak' ? 'show active' : '' ?>" id="pane-kontak" role="tabpanel" aria-labelledby="tab-kontak-btn">
                <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/kontak') ?>">
                    <?= csrf_field() ?>
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1">Kontak dan Keluarga</h2>
                            <p class="text-muted small mb-0">Informasi kontak taruna serta data orang tua/wali.</p>
                        </div>

                        <div class="px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" style="background-color: #fef3c7; border: 1px solid #fde68a;">
                            <i class="bi bi-lock-fill" style="color: #92400e;"></i>
                            <div class="small">
                                <strong class="d-block" style="color: #92400e;">Akses terbatas</strong>
                                <span style="color: #475569;">Hanya dapat diakses petugas berwenang.</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="p-3 p-md-4 rounded-4 border h-100">
                                <h3 class="h6 fw-bold text-dark mb-3">Kontak Taruna</h3>

                                <div class="mb-3">
                                    <label for="profilNomorHp" class="form-label small fw-semibold">Nomor HP</label>
                                    <input type="text" id="profilNomorHp" name="nomor_hp" class="form-control tabular-nums <?= !$can_view_sensitive ? 'bg-light' : '' ?>" value="<?= esc($can_view_sensitive ? old('nomor_hp', $taruna['nomor_hp'] ?? '') : '') ?>" placeholder="<?= $can_view_sensitive ? '08xx-xxxx-xxxx' : 'Terlindungi' ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label for="profilEmail" class="form-label small fw-semibold">Alamat Email</label>
                                    <input type="email" id="profilEmail" name="email" class="form-control <?= !$can_view_sensitive ? 'bg-light' : '' ?>" value="<?= esc($can_view_sensitive ? old('email', $taruna['email'] ?? '') : '') ?>" placeholder="<?= $can_view_sensitive ? 'notar@student.pktj.ac.id' : 'Terlindungi' ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label for="profilAlamatDomisili" class="form-label small fw-semibold">Alamat Domisili</label>
                                    <textarea id="profilAlamatDomisili" name="alamat_domisili" class="form-control <?= !$can_view_sensitive ? 'bg-light' : '' ?>" rows="2" placeholder="<?= $can_view_sensitive ? 'Asrama / alamat domisili di Tegal' : 'Terlindungi' ?>" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>><?= esc($can_view_sensitive ? old('alamat_domisili', $taruna['alamat_domisili'] ?? '') : '') ?></textarea>
                                </div>

                                <div>
                                    <label for="profilAlamatAsal" class="form-label small fw-semibold">Alamat Asal</label>
                                    <textarea id="profilAlamatAsal" name="alamat_asal" class="form-control <?= !$can_view_sensitive ? 'bg-light' : '' ?>" rows="2" placeholder="<?= $can_view_sensitive ? 'Masukkan alamat keluarga' : 'Terlindungi' ?>" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>><?= esc($can_view_sensitive ? old('alamat_asal', $taruna['alamat_asal'] ?? '') : '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="p-3 p-md-4 rounded-4 border h-100">
                                <h3 class="h6 fw-bold text-dark mb-3">Orang Tua / Wali</h3>

                                <div class="mb-3">
                                    <label for="profilNamaWali" class="form-label small fw-semibold">Nama Lengkap</label>
                                    <input type="text" id="profilNamaWali" name="nama_wali" class="form-control <?= !$can_view_sensitive ? 'bg-light' : '' ?>" value="<?= esc($can_view_sensitive ? old('nama_wali', $taruna['nama_wali'] ?? '') : '') ?>" placeholder="<?= $can_view_sensitive ? 'Nama orang tua / wali' : 'Terlindungi' ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label for="profilHubunganWali" class="form-label small fw-semibold">Hubungan</label>
                                    <select id="profilHubunganWali" name="hubungan_wali" class="form-select <?= !$can_view_sensitive ? 'bg-light' : '' ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$canEditProfile) ? 'disabled' : '' ?>>
                                        <?php if (!$can_view_sensitive): ?>
                                            <option value="" selected>Terlindungi</option>
                                        <?php else: ?>
                                            <?php foreach (['Ayah', 'Ibu', 'Wali'] as $hub): ?>
                                                <option value="<?= esc($hub) ?>" <?= old('hubungan_wali', $taruna['hubungan_wali'] ?? 'Ayah') === $hub ? 'selected' : '' ?>><?= esc($hub) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="profilNomorHpWali" class="form-label small fw-semibold">Nomor HP Wali</label>
                                    <input type="text" id="profilNomorHpWali" name="nomor_hp_wali" class="form-control tabular-nums <?= !$can_view_sensitive ? 'bg-light' : '' ?>" value="<?= esc($can_view_sensitive ? old('nomor_hp_wali', $taruna['nomor_hp_wali'] ?? '') : '') ?>" placeholder="<?= $can_view_sensitive ? '08xx-xxxx-xxxx' : 'Terlindungi' ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$canEditProfile) ? 'readonly' : '' ?>>
                                    <div class="form-text small">Kontak darurat yang dapat dihubungi.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_view_sensitive && $can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top d-print-none">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">
                                Simpan Draf
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                                Ajukan Validasi
                            </button>
                        </div>
                    <?php elseif ($can_view_sensitive && $canEditOwnProfile): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top d-print-none">
                            <button type="submit" name="aksi_submit" value="mandiri" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                                <span>Simpan Kontak &amp; Domisili</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 4: RIWAYAT STATUS
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'riwayat-status' ? 'show active' : '' ?>" id="pane-riwayat-status" role="tabpanel" aria-labelledby="tab-riwayat-status-btn">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-5">
                        <div class="p-3 rounded-4 border h-100 d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-muted small mb-1">Status Saat Ini</div>
                                <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                            </div>
                            <div class="text-md-end">
                                <div class="text-muted small">Berlaku sejak</div>
                                <div class="fw-bold tabular-nums" style="color: #1e40af;">
                                    <?= !empty($status_aktif_row['mulai_berlaku']) ? esc(ketarunaan_format_tanggal($status_aktif_row['mulai_berlaku'])) : '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <div class="p-3 rounded-4 border h-100" style="background-color: #f8fafc;">
                            <h3 class="h6 fw-bold text-dark mb-1">Rencana Kembali</h3>
                            <p class="text-muted small mb-0">
                                Rencana kembali dari cuti atau skorsing bukan pengaktifan otomatis. Pengaktifan kembali tetap memerlukan keputusan resmi yang tervalidasi.
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-1">Riwayat Status</h2>
                                <p class="text-muted small mb-0">Riwayat perubahan status pendidikan taruna.</p>
                            </div>
                            <?php if ($can_manage): ?>
                                <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl text-nowrap d-print-none" style="min-height: 36px;">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Ajukan Perubahan Status</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive profil-table-wrap border rounded-3 mb-3 d-none d-md-block">
                            <table id="tabelRiwayatStatus" class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted small">
                                        <th class="text-nowrap" aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="0" data-type="text"><span>Status Pendidikan</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                        <th class="text-nowrap" aria-sort="descending"><button type="button" class="taruna-sort-btn" data-col="1" data-type="text"><span>Mulai Berlaku</span><i class="bi bi-arrow-down text-primary" aria-hidden="true"></i></button></th>
                                        <th class="text-nowrap" aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="2" data-type="text"><span>Akhir Berlaku</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="3" data-type="text"><span>Dasar Keputusan</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                        <th class="text-nowrap" aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="4" data-type="text"><span>Validasi</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                        <th class="text-end text-nowrap d-print-none">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($riwayat_status)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted small">Belum ada riwayat perubahan status.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($riwayat_status as $rs): ?>
                                            <?php
                                                $skUrl         = ketarunaan_resolve_sk_url($rs['bukti_dokumen'] ?? null, (int) $rs['id']);
                                                $isDraftStatus = (($rs['status_validasi'] ?? '') === 'draf');
                                            ?>
                                            <tr>
                                                <td class="text-nowrap" data-sort-value="<?= esc((string) $rs['kode_status']) ?>"><?= ketarunaan_status_badge((string) $rs['kode_status']) ?></td>
                                                <td class="tabular-nums small text-nowrap" data-sort-value="<?= esc((string) ($rs['mulai_berlaku'] ?? '')) ?>"><?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'], true)) ?></td>
                                                <td class="tabular-nums small text-nowrap" data-sort-value="<?= esc((string) ($rs['akhir_berlaku'] ?? '')) ?>"><?= !empty($rs['akhir_berlaku']) ? esc(ketarunaan_format_tanggal($rs['akhir_berlaku'], true)) : '-' ?></td>
                                                <td class="small fw-medium text-dark" data-sort-value="<?= esc((string) ($rs['nomor_keputusan'] ?? '')) ?>">
                                                    <div><?= esc($rs['nomor_keputusan'] ?: '-') ?></div>
                                                    <?php if (!empty($rs['catatan_validasi'])): ?>
                                                        <div class="small" style="color: #b91c1c;">Catatan: <?= esc($rs['catatan_validasi']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-nowrap" data-sort-value="<?= esc((string) ($rs['status_validasi'] ?? '')) ?>"><?= ketarunaan_validasi_badge((string) $rs['status_validasi']) ?></td>
                                                <td class="text-end text-nowrap d-print-none">
                                                    <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                                        <?php if ($skUrl): ?>
                                                            <a href="<?= esc($skUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 btn-tactile text-nowrap" style="min-height: 32px;" title="Buka Dokumen SK">
                                                                <i class="bi bi-box-arrow-up-right"></i>
                                                                <span>SK</span>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($can_manage && $isDraftStatus): ?>
                                                            <?php $draftLengkap = !empty($rs['bukti_dokumen']) && !empty($rs['nomor_keputusan']) && !empty($rs['tanggal_keputusan']); ?>
                                                            <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna) . '?draft_id=' . (int) $rs['id']) ?>" class="btn <?= $draftLengkap ? 'btn-outline-secondary' : 'btn-primary' ?> btn-sm rounded-3 px-2 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile text-nowrap" style="min-height: 32px;">
                                                                <?= $draftLengkap ? 'Ubah' : 'Lengkapi' ?>
                                                            </a>
                                                            <?php if ($draftLengkap): ?>
                                                                <form method="post" action="<?= site_url('ketarunaan/taruna/status/' . (int) $rs['id'] . '/ajukan') ?>" class="d-inline">
                                                                    <?= csrf_field() ?>
                                                                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-2 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile text-nowrap" style="min-height: 32px;">Ajukan</button>
                                                                </form>
                                                            <?php endif; ?>
                                                            <form method="post" action="<?= site_url('ketarunaan/taruna/status/' . (int) $rs['id'] . '/hapus-draf') ?>" class="d-inline form-hapus-draf-status">
                                                                <?= csrf_field() ?>
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-2 d-inline-flex align-items-center justify-content-center btn-tactile text-nowrap" style="min-height: 32px;" title="Hapus Draf" aria-label="Hapus Draf">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-light btn-sm rounded-3 border p-0 d-inline-flex align-items-center justify-content-center btn-tactile btn-detail-status flex-shrink-0" style="width: 32px; height: 32px; color: #1e40af;" data-nomor-sk="<?= esc($rs['nomor_keputusan'] ?: '-') ?>" data-alasan="<?= esc($rs['alasan_perubahan'] ?: '-') ?>" data-mulai="<?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'])) ?>" data-validator="<?= esc($rs['divalidasi_oleh'] ?: '-') ?>" data-catatan="<?= esc($rs['catatan_validasi'] ?: '-') ?>" title="Lihat Rincian Keputusan" aria-label="Lihat Rincian Keputusan">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Kartu Sentuh Riwayat Status Mobile (< 768px) -->
                        <div class="d-md-none d-flex flex-column gap-2 mb-3">
                            <?php if (empty($riwayat_status)): ?>
                                <div class="p-3 rounded-3 border bg-white text-center text-muted small">Belum ada riwayat perubahan status.</div>
                            <?php else: ?>
                                <?php foreach ($riwayat_status as $rs): ?>
                                    <?php
                                        $skUrl         = ketarunaan_resolve_sk_url($rs['bukti_dokumen'] ?? null, (int) $rs['id']);
                                        $isDraftStatus = (($rs['status_validasi'] ?? '') === 'draf');
                                    ?>
                                    <div class="p-3 rounded-3 border bg-white">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                            <?= ketarunaan_status_badge((string) $rs['kode_status']) ?>
                                            <?= ketarunaan_validasi_badge((string) $rs['status_validasi']) ?>
                                        </div>
                                        <div class="small text-dark fw-medium mb-1">Dasar: <?= esc($rs['nomor_keputusan'] ?: '-') ?></div>
                                        <div class="small text-muted tabular-nums mb-2">
                                            Mulai <?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'], true)) ?>
                                            <?= !empty($rs['akhir_berlaku']) ? ' — Akhir ' . esc(ketarunaan_format_tanggal($rs['akhir_berlaku'], true)) : '' ?>
                                        </div>
                                        <?php if (!empty($rs['catatan_validasi'])): ?>
                                            <div class="small mb-2" style="color: #b91c1c;">Catatan: <?= esc($rs['catatan_validasi']) ?></div>
                                        <?php endif; ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-light btn-sm rounded-3 border flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl btn-detail-status" style="color: #1e40af;" data-nomor-sk="<?= esc($rs['nomor_keputusan'] ?: '-') ?>" data-alasan="<?= esc($rs['alasan_perubahan'] ?: '-') ?>" data-mulai="<?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'])) ?>" data-validator="<?= esc($rs['divalidasi_oleh'] ?: '-') ?>" data-catatan="<?= esc($rs['catatan_validasi'] ?: '-') ?>">
                                                <i class="bi bi-eye"></i>
                                                <span>Rincian</span>
                                            </button>
                                            <?php if ($skUrl): ?>
                                                <a href="<?= esc($skUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-1 btn-tactile taruna-touch-ctrl">
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    <span>Lihat SK</span>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($can_manage && $isDraftStatus): ?>
                                                <?php $draftLengkap = !empty($rs['bukti_dokumen']) && !empty($rs['nomor_keputusan']) && !empty($rs['tanggal_keputusan']); ?>
                                                <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna) . '?draft_id=' . (int) $rs['id']) ?>" class="btn <?= $draftLengkap ? 'btn-outline-secondary' : 'btn-primary' ?> btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl">
                                                    <?= $draftLengkap ? 'Ubah' : 'Lengkapi' ?>
                                                </a>
                                                <?php if ($draftLengkap): ?>
                                                    <form method="post" action="<?= site_url('ketarunaan/taruna/status/' . (int) $rs['id'] . '/ajukan') ?>" class="d-inline flex-grow-1">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl">Ajukan</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="post" action="<?= site_url('ketarunaan/taruna/status/' . (int) $rs['id'] . '/hapus-draf') ?>" class="d-inline form-hapus-draf-status">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3 d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl">Hapus</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="p-3 rounded-3 d-flex align-items-start gap-2 small" style="background-color: #eff6ff; color: #1e40af;">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>
                                <strong class="d-block">Akhir berlaku adalah batas eksklusif.</strong>
                                <span>Status berlaku hingga tepat sebelum tanggal tersebut.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referensi 6 Status Pendidikan (Warna Seragam ECC) -->
                <div class="pt-3 border-top">
                    <h3 class="h6 fw-bold text-dark mb-1">Referensi Status Pendidikan</h3>
                    <p class="text-muted small mb-3">Keterangan singkat untuk setiap status pendidikan taruna.</p>

                    <div class="row g-2">
                        <?php foreach ($status_catalog as $kode => $meta): ?>
                            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                                <div class="ref-status-card" style="background-color: <?= esc($meta['bg_soft']) ?>; border-color: <?= esc($meta['border_color']) ?>;">
                                    <div class="d-flex align-items-start gap-2 mb-1">
                                        <span class="rounded-circle d-inline-block flex-shrink-0 mt-1" style="width: 8px; height: 8px; background-color: <?= esc($meta['color']) ?>;"></span>
                                        <strong class="small lh-sm" style="color: <?= esc($meta['text_color'] ?? $meta['color']) ?>;"><?= esc($meta['label']) ?></strong>
                                    </div>
                                    <div class="small" style="color: #475569;"><?= esc($meta['deskripsi']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ==========================================================
                 TAB 5: RIWAYAT PENDIDIKAN
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'riwayat-pendidikan' ? 'show active' : '' ?>" id="pane-riwayat-pendidikan" role="tabpanel" aria-labelledby="tab-riwayat-pendidikan-btn">
                <div class="mb-3">
                    <h2 class="h5 fw-bold text-dark mb-1">Riwayat Pendidikan</h2>
                    <p class="text-muted small mb-0">Posisi pendidikan berdasarkan periode berlaku.</p>
                </div>

                <?php if (!empty($cuti_riwayat_row)): ?>
                    <div class="p-3 rounded-3 d-flex align-items-center gap-2 small mb-3" style="background-color: #eff6ff; color: #1e40af;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Masa cuti <?= esc(ketarunaan_format_tanggal($cuti_riwayat_row['mulai_berlaku'])) ?> – <?= esc(ketarunaan_format_tanggal($cuti_riwayat_row['akhir_berlaku'] ?? '')) ?> tercatat pada Riwayat Status.</span>
                    </div>
                <?php endif; ?>

                <div class="table-responsive profil-table-wrap border rounded-3 mb-4 d-none d-md-block">
                    <table id="tabelRiwayatPendidikan" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="0" data-type="text"><span>Tahun Akademik</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="1" data-type="text"><span>Prodi</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                <th class="text-center" aria-sort="none"><button type="button" class="taruna-sort-btn justify-content-center" data-col="2" data-type="number"><span>Tingkat</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                <th class="text-center" aria-sort="none"><button type="button" class="taruna-sort-btn justify-content-center" data-col="3" data-type="text"><span>Kelas</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                <th class="text-center" aria-sort="none"><button type="button" class="taruna-sort-btn justify-content-center" data-col="4" data-type="number"><span>Semester</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                                <th aria-sort="descending"><button type="button" class="taruna-sort-btn" data-col="5" data-type="text"><span>Mulai Berlaku</span><i class="bi bi-arrow-down text-primary" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="6" data-type="text"><span>Akhir Berlaku</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat_pendidikan)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted small">Belum ada riwayat pendidikan tercatat.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riwayat_pendidikan as $rp): ?>
                                    <tr>
                                        <td class="fw-semibold text-dark small" data-sort-value="<?= esc((string) ($rp['kode_tahun'] ?? '')) ?>"><?= esc(!empty($rp['kode_tahun']) ? $rp['kode_tahun'] : '-') ?></td>
                                        <td class="small" data-sort-value="<?= esc((string) ($rp['kode_prodi'] ?? '')) ?>"><?= esc(!empty($rp['kode_prodi']) ? $rp['kode_prodi'] : '-') ?></td>
                                        <td class="text-center small" data-sort-value="<?= (int) $rp['tingkat'] ?>"><?= esc($tingkatRomawi[(int) $rp['tingkat']] ?? $rp['tingkat']) ?></td>
                                        <td class="text-center small" data-sort-value="<?= esc((string) ($rp['nama_kelas'] ?? '')) ?>"><?= esc(!empty($rp['nama_kelas']) ? $rp['nama_kelas'] : '-') ?></td>
                                        <td class="text-center tabular-nums small" data-sort-value="<?= (int) $rp['semester'] ?>"><?= (int) $rp['semester'] ?></td>
                                        <td class="tabular-nums small" data-sort-value="<?= esc((string) ($rp['mulai_berlaku'] ?? '')) ?>"><?= esc(ketarunaan_format_tanggal($rp['mulai_berlaku'], true)) ?></td>
                                        <td class="tabular-nums small" data-sort-value="<?= esc((string) ($rp['akhir_berlaku'] ?? '')) ?>"><?= !empty($rp['akhir_berlaku']) ? esc(ketarunaan_format_tanggal($rp['akhir_berlaku'], true)) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Kartu Sentuh Riwayat Pendidikan Mobile (< 768px) -->
                <div class="d-md-none d-flex flex-column gap-2 mb-4">
                    <?php if (empty($riwayat_pendidikan)): ?>
                        <div class="p-3 rounded-3 border bg-white text-center text-muted small">Belum ada riwayat pendidikan tercatat.</div>
                    <?php else: ?>
                        <?php foreach ($riwayat_pendidikan as $rp): ?>
                            <div class="p-3 rounded-3 border bg-white">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <strong class="text-dark small"><?= esc(!empty($rp['kode_tahun']) ? $rp['kode_tahun'] : '-') ?></strong>
                                    <span class="small fw-semibold tabular-nums" style="color: #1e40af;"><?= esc(!empty($rp['kode_prodi']) ? $rp['kode_prodi'] : '-') ?> • Tk. <?= esc($tingkatRomawi[(int) $rp['tingkat']] ?? $rp['tingkat']) ?></span>
                                </div>
                                <div class="small text-muted tabular-nums">
                                    <?= !empty($rp['nama_kelas']) ? 'Kelas ' . esc($rp['nama_kelas']) : 'Kelas belum diatur' ?> • Semester <?= (int) $rp['semester'] ?> • Mulai <?= esc(ketarunaan_format_tanggal($rp['mulai_berlaku'], true)) ?><?= !empty($rp['akhir_berlaku']) ? ' - ' . esc(ketarunaan_format_tanggal($rp['akhir_berlaku'], true)) : '' ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2 d-print-none">
                    <button type="button" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 40px;" onclick="window.print()">
                        <i class="bi bi-download"></i>
                        <span>Unduh Riwayat</span>
                    </button>
                    <?php if ($can_manage): ?>
                        <button type="button" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalCatatPendidikan">
                            <i class="bi bi-plus-lg"></i>
                            <span>Catat Perubahan Pendidikan</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?php if (!empty($can_validate_foto)): ?>
<form id="formValidasiFotoPending" method="post" action="<?= site_url('ketarunaan/validasi/foto/' . urlencode($nomorTaruna)) ?>" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="return_to" value="profil">
    <input type="hidden" name="keputusan" id="inputKeputusanFotoPending" value="">
    <input type="hidden" name="catatan_validasi" id="inputCatatanFotoPending" value="">
</form>
<?php endif; ?>

<!-- MODAL CATAT PERUBAHAN PENDIDIKAN -->
<?php if ($can_manage): ?>
<div class="modal fade" id="modalCatatPendidikan" tabindex="-1" aria-labelledby="modalCatatPendidikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/riwayat-pendidikan') ?>">
                <?= csrf_field() ?>
                <?php
                    $oldCatatTa      = (string) old('tahun_akademik_id', '');
                    $oldCatatProdi   = (int) old('program_studi_id', (string) ($taruna['program_studi_id'] ?? 1));
                    $oldCatatTingkat = (int) old('tingkat', (string) ($taruna['tingkat'] ?? 1));
                    $oldCatatKelas   = (int) old('kelas_id', (string) ($taruna['kelas_id'] ?? 0));
                    $oldCatatSem     = (int) old('semester', (string) ($taruna['semester_aktif'] ?? 1));
                    $oldCatatMulai   = (string) old('mulai_berlaku', date('Y-m-d'));
                ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <h2 class="h6 modal-title fw-bold text-dark mb-0" id="modalCatatPendidikanLabel">Catat Perubahan Pendidikan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="catatTahunAkademik" class="form-label small fw-semibold">Tahun Akademik <span class="text-danger">*</span></label>
                            <select id="catatTahunAkademik" name="tahun_akademik_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($tahun_akademik_list as $ta): ?>
                                    <option value="<?= esc($ta['id']) ?>" <?= ($oldCatatTa !== '' ? $oldCatatTa === (string) $ta['id'] : !empty($ta['is_aktif'])) ? 'selected' : '' ?>><?= esc($ta['kode_tahun']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="catatProdi" class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                            <select id="catatProdi" name="program_studi_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($prodi_list as $p): ?>
                                    <option value="<?= esc($p['id']) ?>" <?= $oldCatatProdi === (int) $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="catatTingkat" class="form-label small fw-semibold">Tingkat <span class="text-danger">*</span></label>
                            <select id="catatTingkat" name="tingkat" class="form-select" style="height: 40px;">
                                <?php foreach ($tingkatRomawi as $num => $rom): ?>
                                    <option value="<?= $num ?>" <?= $oldCatatTingkat === $num ? 'selected' : '' ?>><?= esc($rom) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="catatKelas" class="form-label small fw-semibold">Kelas</label>
                            <select id="catatKelas" name="kelas_id" class="form-select" style="height: 40px;">
                                <option value="">-- Pilih --</option>
                                <?php foreach ($kelas_list as $kls): ?>
                                    <?php
                                        $labelKelas = trim(
                                            (!empty($kls['kode_prodi']) ? $kls['kode_prodi'] . ' ' : '')
                                            . (!empty($kls['tingkat']) ? (string) $kls['tingkat'] : '')
                                            . (string) $kls['nama_kelas']
                                            . (!empty($kls['tahun_masuk']) ? ' • Angk. ' . ketarunaan_format_angkatan($kls) : '')
                                        );
                                    ?>
                                    <option value="<?= esc($kls['id']) ?>" data-prodi-id="<?= (int) ($kls['program_studi_id'] ?? 0) ?>" data-tingkat="<?= (int) ($kls['tingkat'] ?? 0) ?>" <?= $oldCatatKelas === (int) $kls['id'] ? 'selected' : '' ?>><?= esc($labelKelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label for="catatSemester" class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                            <select id="catatSemester" name="semester" class="form-select tabular-nums" style="height: 40px;">
                                <?php for ($s = 1; $s <= 8; $s++): ?>
                                    <option value="<?= $s ?>" <?= $oldCatatSem === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="catatMulaiBerlaku" class="form-label small fw-semibold">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="text" id="catatMulaiBerlaku" name="mulai_berlaku" class="form-control bg-white" value="<?= esc($oldCatatMulai) ?>" placeholder="Pilih tanggal mulai berlaku" style="height: 40px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-3 px-4 border d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" data-bs-dismiss="modal" style="height: 40px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">Simpan Riwayat</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function bindKelasCascade(prodiId, tingkatId, kelasId, angkatanId) {
        const selProdi    = document.getElementById(prodiId);
        const selTingkat  = document.getElementById(tingkatId);
        const selKelas    = document.getElementById(kelasId);
        const selAngkatan = angkatanId ? document.getElementById(angkatanId) : null;
        if (!selProdi || !selTingkat || !selKelas) return;

        const allOptions = Array.from(selKelas.options).map(function (opt) {
            return {
                value: opt.value,
                text: opt.text,
                prodiId: opt.getAttribute('data-prodi-id') || '',
                tingkat: opt.getAttribute('data-tingkat') || '',
                angkatanId: opt.getAttribute('data-angkatan-id') || ''
            };
        });

        function refresh() {
            const pid = selProdi.value;
            const tkt = selTingkat.value;
            const aid = selAngkatan ? selAngkatan.value : '';
            const currentVal = selKelas.value;
            selKelas.innerHTML = '';

            const exactMatches = allOptions.filter(function (item) {
                return item.value !== '' && item.prodiId === pid && item.tingkat === tkt && (!aid || item.angkatanId === aid);
            });
            const useStrictAngkatan = aid !== '' && exactMatches.length > 0;

            let restored = false;
            allOptions.forEach(function (item) {
                const matchProdiTingkat = item.prodiId === pid && item.tingkat === tkt;
                const matchAngkatan = !useStrictAngkatan || item.angkatanId === aid;
                if (item.value === '' || (matchProdiTingkat && matchAngkatan)) {
                    const opt = document.createElement('option');
                    opt.value = item.value;
                    opt.textContent = item.text;
                    if (item.value !== '' && item.value === currentVal) {
                        opt.selected = true;
                        restored = true;
                    }
                    selKelas.appendChild(opt);
                }
            });
            if (!restored) {
                selKelas.value = '';
            }
        }

        selProdi.addEventListener('change', refresh);
        selTingkat.addEventListener('change', refresh);
        if (selAngkatan) {
            selAngkatan.addEventListener('change', refresh);
        }
        refresh();
    }

    function initClientTableSort(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const buttons = table.querySelectorAll('thead .taruna-sort-btn');
        if (!tbody || !buttons.length) return;

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const th = btn.closest('th');
                const colIdx = parseInt(btn.getAttribute('data-col') || '0', 10);
                const type = btn.getAttribute('data-type') || 'text';
                const currDir = th ? (th.getAttribute('aria-sort') || 'none') : 'none';
                const nextDir = currDir === 'ascending' ? 'descending' : 'ascending';

                const rows = Array.from(tbody.querySelectorAll('tr')).filter(function (tr) {
                    return !tr.querySelector('td[colspan]');
                });
                if (!rows.length) return;

                rows.sort(function (a, b) {
                    const cellA = a.children[colIdx];
                    const cellB = b.children[colIdx];
                    const rawA = cellA ? (cellA.getAttribute('data-sort-value') ?? cellA.textContent.trim()) : '';
                    const rawB = cellB ? (cellB.getAttribute('data-sort-value') ?? cellB.textContent.trim()) : '';
                    let cmp = 0;
                    if (type === 'number') {
                        cmp = (parseFloat(rawA) || 0) - (parseFloat(rawB) || 0);
                    } else {
                        cmp = String(rawA).localeCompare(String(rawB), 'id', { numeric: true, sensitivity: 'base' });
                    }
                    return nextDir === 'ascending' ? cmp : -cmp;
                });

                rows.forEach(function (tr) { tbody.appendChild(tr); });

                table.querySelectorAll('thead th[aria-sort]').forEach(function (otherTh) {
                    otherTh.setAttribute('aria-sort', 'none');
                    const icon = otherTh.querySelector('.taruna-sort-btn i');
                    if (icon) {
                        icon.className = 'bi bi-arrow-down-up text-muted opacity-50';
                    }
                });
                if (th) {
                    th.setAttribute('aria-sort', nextDir);
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = nextDir === 'ascending' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary';
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const elTglLahir = document.getElementById('inputTanggalLahir');
        let fpTglLahir = null;
        if (elTglLahir && !elTglLahir.hasAttribute('readonly') && typeof flatpickr !== 'undefined') {
            fpTglLahir = flatpickr(elTglLahir, {
                locale: 'id',
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                maxDate: "today"
            });
            if (fpTglLahir && fpTglLahir.altInput) {
                fpTglLahir.altInput.setAttribute('aria-label', 'Tanggal Lahir');
            }
        }

        const elCatatMulai = document.getElementById('catatMulaiBerlaku');
        let fpCatatMulai = null;
        if (elCatatMulai && typeof flatpickr !== 'undefined') {
            fpCatatMulai = flatpickr(elCatatMulai, {
                locale: 'id',
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
            if (fpCatatMulai && fpCatatMulai.altInput) {
                fpCatatMulai.altInput.setAttribute('aria-label', 'Tanggal Mulai Berlaku');
            }
        }

        const formIdentitas = document.getElementById('formProfilIdentitas');
        if (formIdentitas && elTglLahir && !elTglLahir.hasAttribute('readonly')) {
            formIdentitas.addEventListener('submit', function (e) {
                if (!elTglLahir.value) {
                    e.preventDefault();
                    if (fpTglLahir && fpTglLahir.altInput) {
                        fpTglLahir.altInput.focus();
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanggal Lahir Wajib Diisi',
                            text: 'Pilih tanggal lahir sebelum menyimpan identitas.'
                        });
                    } else {
                        alert('Pilih tanggal lahir sebelum menyimpan identitas.');
                    }
                }
            });
        }

        const modalCatatEl = document.getElementById('modalCatatPendidikan');
        if (modalCatatEl && elCatatMulai) {
            const formCatatPendidikan = modalCatatEl.querySelector('form');
            if (formCatatPendidikan) {
                formCatatPendidikan.addEventListener('submit', function (e) {
                    if (!elCatatMulai.value) {
                        e.preventDefault();
                        if (fpCatatMulai && fpCatatMulai.altInput) {
                            fpCatatMulai.altInput.focus();
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tanggal Mulai Wajib Diisi',
                                text: 'Pilih tanggal mulai berlaku sebelum menyimpan riwayat.'
                            });
                        } else {
                            alert('Pilih tanggal mulai berlaku sebelum menyimpan riwayat.');
                        }
                    }
                });
            }
        }
        if (modalCatatEl && typeof bootstrap !== 'undefined') {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('modal') === 'catat') {
                new bootstrap.Modal(modalCatatEl).show();
            }
            modalCatatEl.addEventListener('hidden.bs.modal', function () {
                if (new URLSearchParams(window.location.search).get('modal') === 'catat') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('modal');
                    window.history.replaceState({}, '', url.toString());
                }
            });
        }

        const selectAngkatan = document.getElementById('selectAngkatanPendidikan');
        const inputThnMasuk = document.getElementById('inputTahunMasukReadonly');
        if (selectAngkatan && inputThnMasuk) {
            selectAngkatan.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.dataset.tahun) {
                    inputThnMasuk.value = opt.dataset.tahun;
                }
            });
        }

        bindKelasCascade('selectProdiPendidikan', 'selectTingkatPendidikan', 'selectKelasPendidikan', 'selectAngkatanPendidikan');
        bindKelasCascade('catatProdi', 'catatTingkat', 'catatKelas', null);
        initClientTableSort('tabelRiwayatStatus');
        initClientTableSort('tabelRiwayatPendidikan');

        const inputFoto = document.getElementById('inputFotoTaruna');
        const btnBatalFoto = document.getElementById('btnBatalFotoTaruna');
        const fotoPreviewBox = document.getElementById('fotoPreviewBox');
        const fotoInfoLbl = document.getElementById('fotoInfoLabel');
        const defaultFotoHint = 'JPG/PNG Maks 250 KB (kompresi otomatis)';
        const MAX_FOTO_BYTES = 250 * 1024;
        const initialFotoSrc = fotoPreviewBox ? (fotoPreviewBox.getAttribute('data-initial-src') || '') : '';

        function toggleBatalFoto(show) {
            if (!btnBatalFoto) return;
            btnBatalFoto.classList.toggle('d-none', !show);
            btnBatalFoto.classList.toggle('d-inline-flex', show);
        }

        function resetFotoSelection() {
            if (inputFoto) inputFoto.value = '';
            if (fotoInfoLbl) fotoInfoLbl.textContent = defaultFotoHint;
            toggleBatalFoto(false);

            const img = document.getElementById('previewFotoTaruna');
            let icon = document.getElementById('iconPlaceholderFoto');
            if (initialFotoSrc !== '') {
                setFotoPreview(initialFotoSrc);
            } else {
                if (img) img.classList.add('d-none');
                if (!icon && fotoPreviewBox) {
                    icon = document.createElement('i');
                    icon.id = 'iconPlaceholderFoto';
                    icon.className = 'bi bi-person-bounding-box text-secondary fs-1';
                    fotoPreviewBox.appendChild(icon);
                }
                if (icon) icon.classList.remove('d-none');
            }
        }

        if (btnBatalFoto) {
            btnBatalFoto.addEventListener('click', resetFotoSelection);
        }

        function setFotoPreview(dataUrl) {
            const box = document.getElementById('fotoPreviewBox');
            let img = document.getElementById('previewFotoTaruna');
            const icon = document.getElementById('iconPlaceholderFoto');
            if (icon) {
                icon.classList.add('d-none');
            }
            if (!img && box) {
                img = document.createElement('img');
                img.id = 'previewFotoTaruna';
                img.alt = 'Pratinjau Foto';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                box.appendChild(img);
            }
            if (img) {
                img.src = dataUrl;
                img.classList.remove('d-none');
            }
        }

        function showFotoWarn(title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: title, text: text });
            } else {
                alert(text);
            }
        }

        function canvasToBlobPromise(canvas, quality) {
            return new Promise(function (resolve) {
                canvas.toBlob(function (blob) {
                    resolve(blob);
                }, 'image/jpeg', quality);
            });
        }

        async function compressImageMax250Kb(file) {
            const img = await new Promise(function (resolve, reject) {
                const url = URL.createObjectURL(file);
                const el = new Image();
                el.onload = function () {
                    URL.revokeObjectURL(url);
                    resolve(el);
                };
                el.onerror = function () {
                    URL.revokeObjectURL(url);
                    reject(new Error('invalid-image'));
                };
                el.src = url;
            });

            const maxDims = [800, 640, 480];
            const qualities = [0.82, 0.72, 0.62, 0.52, 0.42, 0.35];
            for (let d = 0; d < maxDims.length; d++) {
                const maxEdge = maxDims[d];
                const scale = Math.min(1, maxEdge / Math.max(img.width || 1, img.height || 1));
                const canvas = document.createElement('canvas');
                canvas.width = Math.max(1, Math.round(img.width * scale));
                canvas.height = Math.max(1, Math.round(img.height * scale));
                const ctx = canvas.getContext('2d');
                if (!ctx) return null;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                for (let q = 0; q < qualities.length; q++) {
                    const blob = await canvasToBlobPromise(canvas, qualities[q]);
                    if (blob && blob.size <= MAX_FOTO_BYTES) {
                        return blob;
                    }
                }
            }
            return null;
        }

        if (inputFoto) {
            inputFoto.addEventListener('change', async function () {
                const file = this.files[0];
                if (!file) return;

                if (!/^image\/(jpeg|jpg|png)$/i.test(file.type)) {
                    showFotoWarn('Format Foto Tidak Didukung', 'Pilih foto berformat JPG atau PNG.');
                    resetFotoSelection();
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    showFotoWarn('Ukuran Foto Terlalu Besar', 'Pilih foto sumber maksimal 10 MB.');
                    resetFotoSelection();
                    return;
                }

                try {
                    if (fotoInfoLbl) fotoInfoLbl.textContent = 'Mengompres foto...';
                    const compressedBlob = await compressImageMax250Kb(file);
                    if (!compressedBlob || compressedBlob.size > MAX_FOTO_BYTES) {
                        if (file.size <= MAX_FOTO_BYTES) {
                            const r = new FileReader();
                            r.onload = function (ev) { setFotoPreview(ev.target.result); };
                            r.readAsDataURL(file);
                            if (fotoInfoLbl) fotoInfoLbl.textContent = 'Siap diunggah (' + Math.ceil(file.size / 1024) + ' KB)';
                            toggleBatalFoto(true);
                            return;
                        }
                        showFotoWarn('Kompresi Gagal', 'Ukuran foto melebihi batas 250 KB. Pilih foto lain.');
                        resetFotoSelection();
                        return;
                    }

                    const baseName = (file.name || 'foto_taruna').replace(/\.[^.]+$/, '');
                    const compressedFile = new File([compressedBlob], baseName + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    if (typeof DataTransfer !== 'undefined') {
                        const dt = new DataTransfer();
                        dt.items.add(compressedFile);
                        this.files = dt.files;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        setFotoPreview(e.target.result);
                    };
                    reader.readAsDataURL(compressedFile);

                    if (fotoInfoLbl) {
                        fotoInfoLbl.textContent = 'Siap diunggah (' + Math.max(1, Math.ceil(compressedBlob.size / 1024)) + ' KB)';
                    }
                    toggleBatalFoto(true);
                } catch (err) {
                    showFotoWarn('Foto Tidak Valid', 'Pastikan berkas berupa gambar JPG/PNG yang sah.');
                    resetFotoSelection();
                }
            });
        }

        const btnSetujuiFoto = document.getElementById('btnSetujuiFotoPending');
        const btnTolakFoto = document.getElementById('btnTolakFotoPending');
        const formValidasiFoto = document.getElementById('formValidasiFotoPending');
        const inpKeputusanFoto = document.getElementById('inputKeputusanFotoPending');
        const inpCatatanFoto = document.getElementById('inputCatatanFotoPending');

        if (btnSetujuiFoto && formValidasiFoto && inpKeputusanFoto) {
            btnSetujuiFoto.addEventListener('click', function () {
                const submitApprove = function () {
                    inpKeputusanFoto.value = 'tervalidasi';
                    if (inpCatatanFoto) inpCatatanFoto.value = '';
                    formValidasiFoto.submit();
                };
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'question',
                        title: 'Setujui Foto Taruna?',
                        text: 'Foto baru akan dipasang pada profil resmi taruna.',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#15803d'
                    }).then(function (res) {
                        if (res.isConfirmed) submitApprove();
                    });
                } else if (confirm('Foto baru akan dipasang pada profil resmi taruna.')) {
                    submitApprove();
                }
            });
        }

        if (btnTolakFoto && formValidasiFoto && inpKeputusanFoto && inpCatatanFoto) {
            btnTolakFoto.addEventListener('click', function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kembalikan Pengajuan Foto?',
                        input: 'textarea',
                        inputLabel: 'Catatan perbaikan untuk taruna',
                        inputPlaceholder: 'Misalnya: latar foto kurang jelas atau tidak sesuai ketentuan.',
                        showCancelButton: true,
                        confirmButtonText: 'Kembalikan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#b91c1c',
                        inputValidator: function (val) {
                            if (!val || !val.trim()) {
                                return 'Isi catatan perbaikan terlebih dahulu.';
                            }
                            return null;
                        }
                    }).then(function (res) {
                        if (res.isConfirmed && res.value) {
                            inpKeputusanFoto.value = 'perlu_perbaikan';
                            inpCatatanFoto.value = res.value.trim();
                            formValidasiFoto.submit();
                        }
                    });
                } else {
                    const note = prompt('Tuliskan catatan perbaikan untuk taruna:');
                    if (note && note.trim()) {
                        inpKeputusanFoto.value = 'perlu_perbaikan';
                        inpCatatanFoto.value = note.trim();
                        formValidasiFoto.submit();
                    }
                }
            });
        }

        document.querySelectorAll('.btn-detail-status').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const nomorSk = btn.getAttribute('data-nomor-sk') || '-';
                const mulai = btn.getAttribute('data-mulai') || '-';
                const alasan = btn.getAttribute('data-alasan') || '-';
                const validator = btn.getAttribute('data-validator') || '-';
                const catatan = btn.getAttribute('data-catatan') || '-';

                if (typeof Swal !== 'undefined') {
                    const wrap = document.createElement('div');
                    wrap.className = 'text-start small';

                    const items = [
                        ['Nomor Keputusan:', nomorSk, 'mb-2'],
                        ['Mulai Berlaku:', mulai, 'mb-2'],
                        ['Keterangan:', alasan, (validator !== '-' || catatan !== '-') ? 'mb-2' : '']
                    ];
                    if (validator !== '-') {
                        items.push(['Divalidasi Oleh:', validator, catatan !== '-' ? 'mb-2' : '']);
                    }
                    if (catatan !== '-') {
                        items.push(['Catatan Validasi:', catatan, '']);
                    }

                    items.forEach(function (row) {
                        const div = document.createElement('div');
                        if (row[2]) div.className = row[2];
                        const strong = document.createElement('strong');
                        strong.textContent = row[0] + ' ';
                        div.appendChild(strong);
                        div.appendChild(document.createTextNode(row[1]));
                        wrap.appendChild(div);
                    });

                    Swal.fire({
                        title: 'Rincian Keputusan Status',
                        html: wrap,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#1e40af',
                        customClass: { popup: 'rounded-4 shadow border-0' }
                    });
                } else {
                    alert('Keputusan: ' + nomorSk + '\nMulai Berlaku: ' + mulai + '\nKeterangan: ' + alasan);
                }
            });
        });

        document.querySelectorAll('.form-hapus-draf-status').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed === '1') return;
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus Draf Status?',
                        text: 'Draf perubahan status ini akan dihapus.',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#b91c1c'
                    }).then(function (res) {
                        if (res.isConfirmed) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                } else if (confirm('Draf perubahan status ini akan dihapus.')) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>

