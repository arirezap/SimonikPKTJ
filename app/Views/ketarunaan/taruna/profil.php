<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Profil Taruna - <?= esc($taruna['nama_lengkap']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-profil-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }
    .nav-tabs-ecc {
        border-bottom: 1px solid #e2e8f0;
        gap: 8px;
    }
    .nav-tabs-ecc .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .nav-tabs-ecc .nav-link:hover {
        color: #0284c7;
        background-color: #f8fafc;
    }
    .nav-tabs-ecc .nav-link.active {
        color: #0284c7;
        border-bottom-color: #0284c7;
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $nomorTaruna = (string) $taruna['nomor_taruna'];
    $tingkatRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    $fotoPath = !empty($taruna['foto']) && file_exists(FCPATH . 'assets/uploads/taruna/' . $taruna['foto'])
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
            <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">ECC 2.0</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
            <span class="mx-1">›</span>
            <span class="text-dark fw-medium">Profil Taruna</span>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Profil Taruna</h1>
                <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none small text-primary fw-semibold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Database Taruna</span>
                </a>
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
                    <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                        <span>Nomor Taruna <strong class="text-primary tabular-nums"><?= esc($nomorTaruna) ?></strong></span>
                        <span>•</span>
                        <strong class="text-primary"><?= esc($taruna['kode_prodi'] ?? 'RSTJ') ?></strong>
                        <span>•</span>
                        <span>Angkatan <strong class="text-primary tabular-nums"><?= esc($taruna['tahun_masuk'] ?? '2024') ?></strong></span>
                        <span>•</span>
                        <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 36px;">
                    <span>Ubah Status</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. NAVIGASI 5 TAB PROFIL -->
    <div class="bento-card p-0 mb-4 overflow-hidden">
        <ul class="nav nav-tabs nav-tabs-ecc px-3 pt-2 bg-white" id="profilTarunaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link small <?= $active_tab === 'identitas' ? 'active' : '' ?>" id="tab-identitas-btn" data-bs-toggle="tab" data-bs-target="#pane-identitas" type="button" role="tab">
                    Identitas Utama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small <?= $active_tab === 'pendidikan' ? 'active' : '' ?>" id="tab-pendidikan-btn" data-bs-toggle="tab" data-bs-target="#pane-pendidikan" type="button" role="tab">
                    Data Pendidikan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small <?= $active_tab === 'kontak' ? 'active' : '' ?>" id="tab-kontak-btn" data-bs-toggle="tab" data-bs-target="#pane-kontak" type="button" role="tab">
                    Kontak dan Keluarga
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small <?= $active_tab === 'riwayat-status' ? 'active' : '' ?>" id="tab-riwayat-status-btn" data-bs-toggle="tab" data-bs-target="#pane-riwayat-status" type="button" role="tab">
                    Riwayat Status
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link small <?= $active_tab === 'riwayat-pendidikan' ? 'active' : '' ?>" id="tab-riwayat-pendidikan-btn" data-bs-toggle="tab" data-bs-target="#pane-riwayat-pendidikan" type="button" role="tab">
                    Riwayat Pendidikan
                </button>
            </li>
        </ul>

        <div class="tab-content p-3 p-md-4 bg-white">

            <!-- ==========================================================
                 TAB 1: IDENTITAS UTAMA
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'identitas' ? 'show active' : '' ?>" id="pane-identitas" role="tabpanel">
                <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/identitas') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <h5 class="fw-bold text-dark mb-1">Identitas Utama</h5>
                            <p class="text-muted small mb-3">Informasi identitas dasar taruna.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nomor Taruna <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light tabular-nums" value="<?= esc($nomorTaruna) ?>" readonly style="height: 40px;">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">NIK</label>
                                    <div class="input-group">
                                        <input type="text" name="nik" class="form-control bg-light tabular-nums" value="<?= esc(ketarunaan_mask_nik($taruna['nik'] ?? '', false)) ?>" readonly style="height: 40px;">
                                        <span class="input-group-text bg-light text-muted" title="<?= $can_view_sensitive ? 'NIK Terdaftar: ' . esc($taruna['nik'] ?? '-') : 'Dilindungi kebijakan privasi' ?>">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control" value="<?= esc(old('nama_lengkap', $taruna['nama_lengkap'])) ?>" required style="height: 40px;" <?= !$can_manage ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Panggilan</label>
                                    <input type="text" name="nama_panggilan" class="form-control" value="<?= esc(old('nama_panggilan', $taruna['nama_panggilan'] ?? '')) ?>" style="height: 40px;" <?= !$can_manage ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <option value="L" <?= old('jenis_kelamin', $taruna['jenis_kelamin']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= old('jenis_kelamin', $taruna['jenis_kelamin']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Agama</label>
                                    <select name="agama" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agm): ?>
                                            <option value="<?= esc($agm) ?>" <?= old('agama', $taruna['agama'] ?? 'Islam') === $agm ? 'selected' : '' ?>><?= esc($agm) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="<?= esc(old('tempat_lahir', $taruna['tempat_lahir'] ?? 'Tegal')) ?>" required style="height: 40px;" <?= !$can_manage ? 'readonly' : '' ?>>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="text" id="inputTanggalLahir" name="tanggal_lahir" class="form-control bg-white" value="<?= esc(old('tanggal_lahir', $taruna['tanggal_lahir'] ?? '2005-05-15')) ?>" required style="height: 40px;" <?= !$can_manage ? 'readonly' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="p-3 rounded-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Foto Taruna</h6>
                                    <p class="text-muted small mb-3">Unggah foto resmi taruna.</p>

                                    <div class="foto-upload-box mb-3">
                                        <div class="mx-auto mb-3 rounded-4 d-flex align-items-center justify-content-center overflow-hidden bg-white border" style="width: 120px; height: 144px;">
                                            <?php if ($fotoPath): ?>
                                                <img id="previewFotoTaruna" src="<?= esc($fotoPath) ?>" alt="Foto Taruna" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i id="iconPlaceholderFoto" class="bi bi-person-bounding-box text-secondary" style="font-size: 3rem;"></i>
                                                <img id="previewFotoTaruna" src="" alt="Pratinjau Foto" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($can_manage): ?>
                                            <label for="inputFotoTaruna" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 cursor-pointer" style="height: 36px;">
                                                <i class="bi bi-upload"></i>
                                                <span>Unggah Foto</span>
                                            </label>
                                            <input type="file" id="inputFotoTaruna" name="foto" accept="image/png,image/jpeg,image/jpg" class="d-none">
                                        <?php endif; ?>
                                        <div class="text-muted small mt-2">JPG/PNG Maks 2 MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 40px;">
                                <span>Simpan Draf</span>
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 40px;">
                                <span>Ajukan Validasi</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 2: DATA PENDIDIKAN
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'pendidikan' ? 'show active' : '' ?>" id="pane-pendidikan" role="tabpanel">
                <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/pendidikan') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <h5 class="fw-bold text-dark mb-1">Data Pendidikan</h5>
                            <p class="text-muted small mb-3">Informasi program studi, angkatan, kelas, dan data pendidikan taruna</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                                    <select name="program_studi_id" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($prodi_list as $p): ?>
                                            <option value="<?= esc($p['id']) ?>" <?= (int) ($taruna['program_studi_id'] ?? 1) === (int) $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['nama_prodi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Angkatan <span class="text-danger">*</span></label>
                                    <select name="angkatan_id" id="selectAngkatanPendidikan" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($angkatan_list as $a): ?>
                                            <option value="<?= esc($a['id']) ?>" data-tahun="<?= esc($a['tahun_masuk']) ?>" <?= (int) ($taruna['angkatan_id'] ?? 1) === (int) $a['id'] ? 'selected' : '' ?>>
                                                <?= esc($a['tahun_masuk']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Tahun Masuk</label>
                                    <input type="text" id="inputTahunMasukReadonly" class="form-control bg-light tabular-nums" value="<?= esc($taruna['tahun_masuk'] ?? '2024') ?>" readonly style="height: 40px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Tingkat <span class="text-danger">*</span></label>
                                    <select name="tingkat" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($tingkatRomawi as $num => $rom): ?>
                                            <option value="<?= $num ?>" <?= (int) ($taruna['tingkat'] ?? 3) === $num ? 'selected' : '' ?>><?= esc($rom) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Kelas <span class="text-danger">*</span></label>
                                    <select name="kelas_id" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach ($kelas_list as $kls): ?>
                                            <option value="<?= esc($kls['id']) ?>" <?= (int) ($taruna['kelas_id'] ?? 0) === (int) $kls['id'] ? 'selected' : '' ?>>
                                                <?= esc($kls['nama_kelas']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                                    <select name="semester_aktif" class="form-select tabular-nums" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php for ($s = 1; $s <= 8; $s++): ?>
                                            <option value="<?= $s ?>" <?= (int) ($taruna['semester_aktif'] ?? 5) === $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Jalur Penerimaan <span class="text-danger">*</span></label>
                                    <select name="jalur_penerimaan" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach (['Pola Pembibitan', 'Reguler', 'Mandiri'] as $jlr): ?>
                                            <option value="<?= esc($jlr) ?>" <?= ($taruna['jalur_penerimaan'] ?? 'Pola Pembibitan') === $jlr ? 'selected' : '' ?>><?= esc($jlr) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Asal Formasi <span class="text-danger">*</span></label>
                                    <select name="asal_formasi" class="form-select" style="height: 40px;" <?= !$can_manage ? 'disabled' : '' ?>>
                                        <?php foreach (['Pemerintah Daerah', 'Kemenhub RI', 'Mandiri / Umum'] as $frm): ?>
                                            <option value="<?= esc($frm) ?>" <?= ($taruna['asal_formasi'] ?? 'Pemerintah Daerah') === $frm ? 'selected' : '' ?>><?= esc($frm) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="p-3 p-md-4 rounded-4 border bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Status Pendidikan Saat Ini</h6>
                                    <p class="text-muted small mb-3">Status pendidikan taruna berdasarkan data terbaru</p>

                                    <div class="p-3 rounded-3 bg-white border mb-3">
                                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span class="text-muted small">Status Pendidikan</span>
                                            <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span class="text-muted small">Berlaku sejak</span>
                                            <strong class="text-dark small tabular-nums">
                                                <?= esc(ketarunaan_format_tanggal($status_aktif_row['mulai_berlaku'] ?? '2026-09-01')) ?>
                                            </strong>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-2">
                                            <span class="text-muted small">Status Validasi</span>
                                            <?= ketarunaan_validasi_badge($status_aktif_row['status_validasi'] ?? 'tervalidasi') ?>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-3 small mb-3" style="background-color: #eff6ff; color: #1e40af;">
                                        Status pendidikan hanya dapat diubah melalui proses resmi sesuai ketentuan yang berlaku.
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-3 flex-grow-1 fw-semibold" style="height: 36px;" onclick="document.getElementById('tab-riwayat-status-btn').click()">
                                        Lihat Riwayat
                                    </button>
                                    <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-outline-primary btn-sm rounded-3 flex-grow-1 fw-semibold d-inline-flex align-items-center justify-content-center" style="height: 36px;">
                                        Ubah Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold btn-tactile" style="height: 40px;">
                                Simpan Draf
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold btn-tactile shadow-sm" style="height: 40px;">
                                Ajukan Validasi
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 3: KONTAK DAN KELUARGA
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'kontak' ? 'show active' : '' ?>" id="pane-kontak" role="tabpanel">
                <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/kontak') ?>">
                    <?= csrf_field() ?>
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Kontak dan Keluarga</h5>
                            <p class="text-muted small mb-0">Informasi kontak taruna serta data orang tua/wali.</p>
                        </div>

                        <div class="px-3 py-2 rounded-3 d-inline-flex align-items-center gap-2" style="background-color: #fef3c7; border: 1px solid #fde68a;">
                            <i class="bi bi-lock-fill text-warning-emphasis"></i>
                            <div class="small">
                                <strong class="text-dark d-block">Akses terbatas</strong>
                                <span class="text-muted">Hanya dapat diakses petugas berwenang.</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="p-3 p-md-4 rounded-4 border h-100">
                                <h6 class="fw-bold text-dark mb-3">Kontak Taruna</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Nomor HP</label>
                                    <input type="text" name="nomor_hp" class="form-control tabular-nums" value="<?= esc($can_view_sensitive ? ($taruna['nomor_hp'] ?? '') : '08xx-xxxx-xxxx') ?>" placeholder="08xx-xxxx-xxxx" style="height: 40px;" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($taruna['email'] ?? '') ?>" placeholder="nama@taruna.pktj.ac.id" style="height: 40px;" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Alamat Domisili</label>
                                    <textarea name="alamat_domisili" class="form-control" rows="2" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>><?= esc($taruna['alamat_domisili'] ?? 'Asrama Taruna PKTJ Tegal') ?></textarea>
                                </div>

                                <div>
                                    <label class="form-label small fw-semibold">Alamat Asal</label>
                                    <textarea name="alamat_asal" class="form-control" rows="2" placeholder="Masukkan alamat keluarga" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>><?= esc($can_view_sensitive ? ($taruna['alamat_asal'] ?? '') : 'Alamat keluarga terlindungi') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="p-3 p-md-4 rounded-4 border h-100">
                                <h6 class="fw-bold text-dark mb-3">Orang Tua / Wali</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="nama_wali" class="form-control" value="<?= esc($can_view_sensitive ? ($taruna['nama_wali'] ?? '') : 'Data Wali Terlindungi') ?>" style="height: 40px;" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Hubungan</label>
                                    <select name="hubungan_wali" class="form-select" style="height: 40px;" <?= (!$can_view_sensitive || !$can_manage) ? 'disabled' : '' ?>>
                                        <?php foreach (['Ayah', 'Ibu', 'Wali'] as $hub): ?>
                                            <option value="<?= esc($hub) ?>" <?= ($taruna['hubungan_wali'] ?? 'Ayah') === $hub ? 'selected' : '' ?>><?= esc($hub) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label small fw-semibold">Nomor HP Wali</label>
                                    <input type="text" name="nomor_hp_wali" class="form-control tabular-nums" value="<?= esc($can_view_sensitive ? ($taruna['nomor_hp_wali'] ?? '') : '08xx-xxxx-xxxx') ?>" placeholder="08xx-xxxx-xxxx" style="height: 40px;" <?= (!$can_view_sensitive || !$can_manage) ? 'readonly' : '' ?>>
                                    <div class="form-text small">Kontak darurat yang dapat dihubungi.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($can_view_sensitive && $can_manage): ?>
                        <div class="d-flex justify-content-end gap-2 pt-4 mt-4 border-top">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold btn-tactile" style="height: 40px;">
                                Simpan Draf
                            </button>
                            <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold btn-tactile shadow-sm" style="height: 40px;">
                                Ajukan Validasi
                            </button>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- ==========================================================
                 TAB 4: RIWAYAT STATUS
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'riwayat-status' ? 'show active' : '' ?>" id="pane-riwayat-status" role="tabpanel">
                <div class="row g-4 mb-4">
                    <div class="col-12 col-lg-8">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Riwayat Status</h5>
                                <p class="text-muted small mb-0">Riwayat perubahan status pendidikan taruna.</p>
                            </div>
                            <?php if ($can_manage): ?>
                                <a href="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 36px;">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Ajukan Perubahan Status</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive border rounded-3 mb-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-muted small">
                                        <th>Status Pendidikan</th>
                                        <th>Mulai Berlaku</th>
                                        <th>Akhir Berlaku</th>
                                        <th>Dasar Keputusan</th>
                                        <th>Validasi</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riwayat_status as $rs): ?>
                                        <tr>
                                            <td><?= ketarunaan_status_badge((string) $rs['kode_status']) ?></td>
                                            <td class="tabular-nums small"><?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'], true)) ?></td>
                                            <td class="tabular-nums small"><?= !empty($rs['akhir_berlaku']) ? esc(ketarunaan_format_tanggal($rs['akhir_berlaku'], true)) : '-' ?></td>
                                            <td class="small fw-medium text-dark"><?= esc($rs['nomor_keputusan'] ?: '-') ?></td>
                                            <td><?= ketarunaan_validasi_badge((string) $rs['status_validasi']) ?></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-light btn-sm rounded-3 border p-0 d-inline-flex align-items-center justify-content-center text-primary" style="width: 32px; height: 32px;" onclick="lihatDetailStatus('<?= esc($rs['nomor_keputusan'] ?: '-') ?>', '<?= esc($rs['alasan_perubahan'] ?: '-') ?>', '<?= esc(ketarunaan_format_tanggal($rs['mulai_berlaku'])) ?>')" title="Lihat Rincian Keputusan">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 rounded-3 d-flex align-items-start gap-2 small" style="background-color: #eff6ff; color: #1e40af;">
                            <i class="bi bi-info-circle-fill mt-1"></i>
                            <div>
                                <strong class="d-block">Akhir berlaku adalah batas eksklusif.</strong>
                                <span>Status berlaku hingga tepat sebelum tanggal tersebut.</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 d-flex flex-column gap-3">
                        <div class="p-3 rounded-4 border">
                            <h6 class="fw-bold text-dark mb-2">Status Saat Ini</h6>
                            <div class="mb-2">
                                <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                            </div>
                            <div class="text-muted small">Berlaku sejak</div>
                            <div class="fw-bold text-primary tabular-nums">
                                <?= esc(ketarunaan_format_tanggal($status_aktif_row['mulai_berlaku'] ?? '2026-09-01')) ?>
                            </div>
                        </div>

                        <div class="p-3 rounded-4 border" style="background-color: #f8fafc;">
                            <h6 class="fw-bold text-dark mb-2">Rencana Kembali</h6>
                            <p class="text-muted small mb-2">
                                Rencana kembali dari status cuti bukan merupakan pengaktifan otomatis.
                            </p>
                            <p class="text-muted small mb-0">
                                Pengaktifan kembali tetap memerlukan proses dan keputusan resmi sesuai ketentuan yang berlaku.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Referensi 6 Status Pendidikan (Warna Seragam ECC) -->
                <div class="pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-1">Referensi Status Pendidikan</h6>
                    <p class="text-muted small mb-3">Keterangan singkat untuk setiap status pendidikan taruna.</p>

                    <div class="row g-2">
                        <?php foreach ($status_catalog as $kode => $meta): ?>
                            <div class="col-12 col-sm-6 col-xl-2">
                                <div class="ref-status-card" style="background-color: <?= esc($meta['bg_soft']) ?>; border-color: <?= esc($meta['border_color']) ?>;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: <?= esc($meta['color']) ?>;"></span>
                                        <strong class="small" style="color: <?= esc($meta['color']) ?>;"><?= esc($meta['label']) ?></strong>
                                    </div>
                                    <div class="text-muted small"><?= esc($meta['deskripsi']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ==========================================================
                 TAB 5: RIWAYAT PENDIDIKAN
                 ========================================================== -->
            <div class="tab-pane fade <?= $active_tab === 'riwayat-pendidikan' ? 'show active' : '' ?>" id="pane-riwayat-pendidikan" role="tabpanel">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1">Riwayat Pendidikan</h5>
                    <p class="text-muted small mb-0">Posisi pendidikan berdasarkan periode berlaku.</p>
                </div>

                <?php if (!empty($cuti_riwayat_row)): ?>
                    <div class="p-3 rounded-3 d-flex align-items-center gap-2 small mb-3" style="background-color: #eff6ff; color: #1e40af;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Masa cuti <?= esc(ketarunaan_format_tanggal($cuti_riwayat_row['mulai_berlaku'])) ?> – <?= esc(ketarunaan_format_tanggal($cuti_riwayat_row['akhir_berlaku'] ?? '')) ?> tercatat pada Riwayat Status.</span>
                    </div>
                <?php endif; ?>

                <div class="table-responsive border rounded-3 mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th>Tahun Akademik</th>
                                <th>Prodi</th>
                                <th class="text-center">Tingkat</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Semester</th>
                                <th>Mulai Berlaku</th>
                                <th>Akhir Berlaku</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat_pendidikan as $rp): ?>
                                <tr>
                                    <td class="fw-semibold text-dark small"><?= esc($rp['kode_tahun']) ?></td>
                                    <td class="small"><?= esc($rp['kode_prodi'] ?? 'RSTJ') ?></td>
                                    <td class="text-center small"><?= esc($tingkatRomawi[(int) $rp['tingkat']] ?? $rp['tingkat']) ?></td>
                                    <td class="text-center small"><?= esc($rp['nama_kelas'] ?? 'A') ?></td>
                                    <td class="text-center tabular-nums small"><?= (int) $rp['semester'] ?></td>
                                    <td class="tabular-nums small"><?= esc(ketarunaan_format_tanggal($rp['mulai_berlaku'], true)) ?></td>
                                    <td class="tabular-nums small"><?= !empty($rp['akhir_berlaku']) ? esc(ketarunaan_format_tanggal($rp['akhir_berlaku'], true)) : '–' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 40px;" onclick="window.print()">
                        <i class="bi bi-download"></i>
                        <span>Unduh Riwayat</span>
                    </button>
                    <?php if ($can_manage): ?>
                        <button type="button" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalCatatPendidikan">
                            <i class="bi bi-plus-lg"></i>
                            <span>Catat Perubahan Pendidikan</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- MODAL CATAT PERUBAHAN PENDIDIKAN -->
<?php if ($can_manage): ?>
<div class="modal fade" id="modalCatatPendidikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '/riwayat-pendidikan') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <h6 class="modal-title fw-bold text-dark mb-0">Catat Perubahan Pendidikan</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Tahun Akademik <span class="text-danger">*</span></label>
                            <select name="tahun_akademik_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($tahun_akademik_list as $ta): ?>
                                    <option value="<?= esc($ta['id']) ?>"><?= esc($ta['kode_tahun']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Program Studi <span class="text-danger">*</span></label>
                            <select name="program_studi_id" class="form-select" required style="height: 40px;">
                                <?php foreach ($prodi_list as $p): ?>
                                    <option value="<?= esc($p['id']) ?>" <?= (int) $taruna['program_studi_id'] === (int) $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Tingkat <span class="text-danger">*</span></label>
                            <select name="tingkat" class="form-select" style="height: 40px;">
                                <?php foreach ($tingkatRomawi as $num => $rom): ?>
                                    <option value="<?= $num ?>" <?= (int) $taruna['tingkat'] === $num ? 'selected' : '' ?>><?= esc($rom) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Kelas</label>
                            <select name="kelas_id" class="form-select" style="height: 40px;">
                                <?php foreach ($kelas_list as $kls): ?>
                                    <option value="<?= esc($kls['id']) ?>"><?= esc($kls['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select tabular-nums" style="height: 40px;">
                                <?php for ($s = 1; $s <= 8; $s++): ?>
                                    <option value="<?= $s ?>" <?= (int) $taruna['semester_aktif'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="date" name="mulai_berlaku" class="form-control" value="<?= date('Y-m-d') ?>" required style="height: 40px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal" style="height: 36px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="height: 36px;">Simpan Riwayat</button>
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
        flatpickr("#inputTanggalLahir", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y"
        });

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

        const inputFoto = document.getElementById('inputFotoTaruna');
        if (inputFoto) {
            inputFoto.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ukuran Foto Terlalu Besar',
                            text: 'Pilih foto maksimal 2 MB.'
                        });
                    } else {
                        alert('Pilih foto maksimal 2 MB.');
                    }
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('previewFotoTaruna');
                    const icon = document.getElementById('iconPlaceholderFoto');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('d-none');
                    }
                    if (icon) {
                        icon.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            });
        }
    });

    function lihatDetailStatus(nomorSk, alasan, tanggalMulai) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Rincian Keputusan Status',
                html: `<div class="text-start small">
                    <div class="mb-2"><strong>Nomor Keputusan:</strong> ${nomorSk}</div>
                    <div class="mb-2"><strong>Mulai Berlaku:</strong> ${tanggalMulai}</div>
                    <div><strong>Keterangan:</strong> ${alasan}</div>
                </div>`,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0284c7',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(`Keputusan: ${nomorSk}\nMulai Berlaku: ${tanggalMulai}\nKeterangan: ${alasan}`);
        }
    }
</script>
<?= $this->endSection() ?>

