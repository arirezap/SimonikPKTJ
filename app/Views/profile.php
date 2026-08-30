<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Profil Saya') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Fluid Container Max-Width for Ultra-Wide / 4K Screens */
    .profile-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Card Elevation & Border System */
    .card-bento-ecc {
        background-color: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.85) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04) !important;
        border-radius: 1rem !important;
    }

    /* Accessible Focus Visible Ring */
    .form-control:focus, .form-select:focus {
        border-color: var(--ecc-primary, #0d6efd) !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
        outline: 0;
    }

    /* Mobile Typography & Input Sizing (iOS Safari Zoom Prevention) */
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
        .profile-container {
            padding-bottom: 75px; /* Space for Mobile Sticky Action Bar */
        }
    }

    /* Desktop Sticky Profile Overview Card */
    @media (min-width: 992px) {
        .sticky-profile-sidebar {
            position: sticky;
            top: 1.5rem;
            z-index: 10;
        }
    }

    /* Staggered Grid Motion */
    .bento-stagger {
        animation: bentoEntrance 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        will-change: transform, opacity;
    }
    .bento-stagger-1 { animation-delay: 0.04s; }
    .bento-stagger-2 { animation-delay: 0.10s; }
    .bento-stagger-3 { animation-delay: 0.18s; }

    @keyframes bentoEntrance {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.99);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Live Password Match Pop Animation */
    @keyframes matchPopIn {
        from {
            opacity: 0;
            transform: translateY(-3px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .match-pop-anim {
        animation: matchPopIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    /* Avatar Cross-Fade Transition */
    @keyframes avatarPhotoIn {
        from {
            opacity: 0;
            transform: scale(0.94);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .avatar-photo-anim {
        animation: avatarPhotoIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    /* Avatar Container & Hover Overlay */
    .avatar-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin: 0 auto;
        cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }
    .avatar-wrapper:hover {
        transform: scale(1.04);
    }
    .avatar-wrapper:active {
        transform: scale(0.98);
    }
    .avatar-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.65);
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
    }
    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
    .avatar-badge-btn {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: var(--ecc-primary, #0d6efd);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
    }
    .avatar-badge-btn:hover {
        background-color: #0b5ed7;
        transform: scale(1.1);
    }
    .avatar-badge-btn:active {
        transform: scale(0.92);
    }

    /* Password Input Group Polish & 44px Touch Target */
    .btn-toggle-pw {
        border-color: #dee2e6;
        color: #64748b;
        min-width: 44px;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease, background-color 0.15s ease;
    }
    .btn-toggle-pw:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }

    /* Tactile Button Feedback */
    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease;
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    /* Overflow & Long Text Resilience */
    .user-profile-heading {
        word-break: break-word;
        overflow-wrap: break-word;
    }

    /* Mobile Floating Action Bar */
    .mobile-floating-bar {
        display: none;
    }
    @media (max-width: 767.98px) {
        .mobile-floating-bar {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            padding: 10px 16px;
            z-index: 1040;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.06);
        }
        .desktop-submit-container {
            display: none !important;
        }
    }

    /* Accessibility: Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .bento-stagger, .avatar-wrapper, .avatar-badge-btn, .btn-tactile {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }

    /* Print Optimization Stylesheet */
    @media print {
        .mobile-floating-bar,
        .btn,
        .btn-toggle-pw,
        .avatar-overlay,
        .avatar-badge-btn,
        .alert,
        #fotoInput,
        .card-header .bg-opacity-10 {
            display: none !important;
        }
        .card-bento-ecc {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        body {
            background: #ffffff !important;
            font-size: 12pt;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3 profile-container">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-person-badge text-primary me-2"></i>Profil Saya</h1>
            <p class="text-muted small mb-0">Kelola kredensial akun, data kepegawaian, dan informasi kontak Anda.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-shield-check me-1"></i> Akun Terverifikasi
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-0.5 fs-6 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <strong class="d-block mb-1">Periksa kembali data yang Anda masukkan:</strong>
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- ==================== BENTO 1: AVATAR / PROFILE OVERVIEW (STICKY ON DESKTOP) ==================== -->
        <div class="col-xl-4 col-lg-5 bento-stagger bento-stagger-2">
            <div class="card card-bento-ecc h-100 sticky-profile-sidebar">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <?php
                        // Logika Inisial Cerdas
                        $rawName = $user['nama_lengkap'] ?? 'Pengguna';
                        $nameParts = explode(',', $rawName);
                        $mainName = trim($nameParts[0]);
                        $nameWords = explode(' ', $mainName);
                        $initials = strtoupper(substr($nameWords[0] ?? 'U', 0, 1));
                        if (count($nameWords) > 1) {
                            $initials .= strtoupper(substr($nameWords[1], 0, 1));
                        }
                        
                        $foto_path = 'assets/uploads/profile/' . ($user['foto'] ?? '');
                        $hasPhoto = (!empty($user['foto']) && file_exists(FCPATH . $foto_path));
                        $foto_url = $hasPhoto ? base_url($foto_path) : base_url('assets/img/undraw_profile.svg');
                    ?>
                    
                    <!-- Single Streamlined Avatar Interaction (Klik untuk Unggah) -->
                    <div class="avatar-wrapper mb-3" id="avatarWrapper" title="Klik untuk mengganti foto profil" role="button" tabindex="0" aria-label="Ganti foto profil">
                        <img src="<?= $foto_url ?>" id="profilePreview" class="img-profile rounded-circle shadow-sm <?= $hasPhoto ? '' : 'd-none' ?>" alt="Foto Profil" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ffffff; box-shadow: 0 10px 24px rgba(0,0,0,0.06);">
                        
                        <div id="profilePreviewInitials" class="bg-primary bg-opacity-10 text-primary rounded-circle align-items-center justify-content-center shadow-sm <?= $hasPhoto ? 'd-none' : 'd-flex' ?>" style="width: 150px; height: 150px; border: 4px solid #ffffff; font-size: 3.5rem; font-weight: bold; box-shadow: 0 10px 24px rgba(0,0,0,0.06);">
                            <?= $initials ?>
                        </div>

                        <!-- Hover Camera Overlay -->
                        <div class="avatar-overlay">
                            <i class="bi bi-camera-fill fs-3 mb-1"></i>
                            <span class="small fw-semibold" style="font-size: 0.75rem;">Ganti Foto</span>
                        </div>

                        <!-- Camera Badge Button (44px touch target) -->
                        <div class="avatar-badge-btn btn-tactile" id="avatarBadgeBtn" title="Klik untuk mengganti foto">
                            <i class="bi bi-camera-fill" style="font-size: 1.05rem;"></i>
                        </div>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1 user-profile-heading" style="letter-spacing: -0.5px;"><?= esc(ucwords(strtolower($mainName))) ?></h4>
                    <p class="text-muted small fw-medium mb-3 user-profile-heading"><?= esc($user['jabatan'] ?? '-') ?> • <?= esc($user['nip']) ?></p>
                    
                    <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <i class="bi bi-person-gear me-1"></i> <?= esc(str_replace('_', ' ', $user['role'])) ?>
                        </span>
                        <?php if (!empty($user['unit'])): ?>
                        <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                            <i class="bi bi-building me-1"></i> <?= esc($user['unit']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Hidden File Input terhubung langsung ke Form Utama -->
                    <input form="profileForm" type="file" name="foto" id="fotoInput" class="d-none" accept="image/png, image/jpeg, image/jpg">

                    <!-- Actions & Helper Toolbar -->
                    <div class="w-100 pt-3 border-top border-light-subtle d-flex flex-column align-items-center">
                        <button form="profileForm" type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 btn-tactile <?= $hasPhoto ? '' : 'd-none' ?>" id="hapusFotoBtn" title="Hapus foto profil saat ini">
                            <i class="bi bi-trash3-fill me-1"></i> Hapus Foto
                        </button>
                        
                        <div class="form-text text-center text-muted mt-2" style="font-size: 0.73rem; line-height: 1.4;">
                            <i class="bi bi-camera me-1 text-primary"></i> Klik foto di atas untuk memilih gambar baru.<br>
                            <span class="text-secondary" style="font-size: 0.7rem;">Format: JPG, PNG. Maksimal: 2MB.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== BENTO 2 & 3: FORMS ==================== -->
        <div class="col-xl-8 col-lg-7 bento-stagger bento-stagger-3">
            <form id="profileForm" action="<?= site_url('profile/update') ?>" method="post" enctype="multipart/form-data" class="h-100 d-flex flex-column gap-4">
                <?= csrf_field() ?>
                <input type="hidden" name="hapus_foto" id="hapusFotoFlag" value="0">
                
                <!-- BENTO 2: KREDENSIAL & KEAMANAN -->
                <div class="card card-bento-ecc">
                    <div class="card-header bg-white border-bottom border-light-subtle p-3 p-md-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                                <i class="bi bi-shield-lock-fill fs-5 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="m-0 fw-bold text-dark fs-5">Kredensial & Keamanan</h6>
                                <p class="text-muted small mb-0" style="font-size: 0.75rem;">Kelola username akses dan pembaruan kata sandi akun.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">Username Akun</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="username" class="form-control bg-light fw-bold text-dark border-start-0" value="<?= esc($user['username']) ?>" readonly>
                                    <span class="input-group-text bg-light text-muted small"><i class="bi bi-lock-fill me-1"></i> Terkunci</span>
                                </div>
                                <div class="form-text small mt-1" style="font-size: 0.72rem;">Username terikat permanen pada identitas akun dan tidak dapat diubah.</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Ganti Password Baru <span class="text-secondary fw-normal text-lowercase">(opsional)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="password" id="passwordInput" class="form-control border-end-0" placeholder="Kosongkan jika tidak diganti..." autocomplete="new-password" minlength="6" maxlength="100">
                                    <button type="button" class="btn btn-outline-secondary btn-toggle-pw border-start-0" data-target="passwordInput" title="Tampilkan / Sembunyikan Password" aria-label="Tampilkan atau sembunyikan kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text small mt-1" style="font-size: 0.72rem;">Minimal 6 karakter jika ingin mengganti kata sandi.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Konfirmasi Password Baru <span class="text-secondary fw-normal text-lowercase">(opsional)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" name="password_confirm" id="passwordConfirmInput" class="form-control border-end-0" placeholder="Ulangi password baru..." autocomplete="new-password" minlength="6" maxlength="100">
                                    <button type="button" class="btn btn-outline-secondary btn-toggle-pw border-start-0" data-target="passwordConfirmInput" title="Tampilkan / Sembunyikan Password" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatchFeedback" class="small mt-1" style="font-size: 0.72rem; display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BENTO 3: DATA KEPEGAWAIAN & KONTAK -->
                <div class="card card-bento-ecc flex-grow-1">
                    <div class="card-header bg-white border-bottom border-light-subtle p-3 p-md-4 d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                            <i class="bi bi-person-vcard-fill fs-5 text-info"></i>
                        </div>
                        <div>
                            <h6 class="m-0 fw-bold text-dark fs-5">Informasi Pegawai & Kontak</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Data profil dinas yang tertera pada laporan dan rekapitulasi kinerja.</p>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Nama Lengkap (Beserta Gelar) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" id="inputNamaLengkap" class="form-control form-control-sm fw-semibold" value="<?= esc($user['nama_lengkap']) ?>" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="inputEmail" class="form-control form-control-sm" value="<?= esc($user['email']) ?>" required maxlength="100" inputmode="email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    No. Handphone / WhatsApp <span class="text-secondary fw-normal text-lowercase">(opsional)</span>
                                </label>
                                <input type="tel" name="no_hp" id="inputNoHp" class="form-control form-control-sm" value="<?= esc($user['no_hp'] ?? '') ?>" placeholder="Misal: 081234567890" maxlength="20" inputmode="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    NIP / NIK <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nip" id="inputNip" class="form-control form-control-sm" value="<?= esc($user['nip']) ?>" required maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Jabatan Kedinasan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="jabatan" id="inputJabatan" class="form-control form-control-sm" value="<?= esc($user['jabatan']) ?>" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Unit Kerja <span class="text-danger">*</span>
                                </label>
                                <select name="unit" id="selectUnitKerja" class="form-select form-select-sm select2" required>
                                    <option value="">-- Pilih Unit Kerja --</option>
                                    <?php foreach ($unit_kerja_list as $uk): ?>
                                        <option value="<?= esc($uk['nama_unit']) ?>" <?= ($user['unit'] == $uk['nama_unit']) ? 'selected' : '' ?>>
                                            <?= esc($uk['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Atasan Langsung <span class="text-secondary fw-normal text-lowercase">(opsional)</span>
                                </label>
                                <select name="atasan_id" id="selectAtasan" class="form-select form-select-sm select2">
                                    <option value="">-- Pilih Atasan Langsung --</option>
                                    <?php foreach ($potential_bosses as $boss): ?>
                                        <option value="<?= $boss['id'] ?>" <?= ($user['atasan_id'] == $boss['id']) ? 'selected' : '' ?>>
                                            <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                    Pangkat / Golongan <span class="text-secondary fw-normal text-lowercase">(opsional)</span>
                                </label>
                                <input type="text" name="pangkat" id="inputPangkat" class="form-control form-control-sm" value="<?= esc($user['pangkat']) ?>" placeholder="Misal: Penata Muda / III/a" maxlength="50">
                            </div>
                        </div>

                        <!-- Desktop Submit Action Container -->
                        <div class="desktop-submit-container d-flex justify-content-end mt-4 pt-3 border-top border-light-subtle">
                            <button type="submit" id="btnSubmitProfile" class="btn btn-primary btn-tactile px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-bold">
                                <i class="bi bi-save2-fill"></i> <span id="btnSubmitText">Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>

    <!-- Mobile Floating Sticky Action Bar (Thumbs-First Design for Phone Viewports) -->
    <div class="mobile-floating-bar d-md-none justify-content-between align-items-center">
        <div class="text-muted small">
            <i class="bi bi-shield-check text-primary me-1"></i> <span class="fw-semibold text-dark">Simpan Profil</span>
        </div>
        <button type="button" id="btnMobileSubmitProfile" class="btn btn-primary btn-tactile px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 fw-bold">
            <i class="bi bi-save2-fill"></i> <span id="btnMobileSubmitText">Simpan</span>
        </button>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const fotoInput = document.getElementById('fotoInput');
    const profilePreview = document.getElementById('profilePreview');
    const profilePreviewInitials = document.getElementById('profilePreviewInitials');
    const hapusFotoBtn = document.getElementById('hapusFotoBtn');
    const hapusFotoFlag = document.getElementById('hapusFotoFlag');
    const avatarWrapper = document.getElementById('avatarWrapper');
    const btnSubmitProfile = document.getElementById('btnSubmitProfile');
    const btnSubmitText = document.getElementById('btnSubmitText');
    const btnMobileSubmitProfile = document.getElementById('btnMobileSubmitProfile');
    const btnMobileSubmitText = document.getElementById('btnMobileSubmitText');
    const pwInput = document.getElementById('passwordInput');
    const pwConfirmInput = document.getElementById('passwordConfirmInput');
    const pwFeedback = document.getElementById('passwordMatchFeedback');

    let formIsDirty = false;
    let formIsSubmitting = false;

    // Track Form Dirty State
    profileForm.querySelectorAll('input, select').forEach(function(el) {
        el.addEventListener('input', function() { formIsDirty = true; });
        el.addEventListener('change', function() { formIsDirty = true; });
    });

    // Unsaved Changes Warning (Dirty Form Guard)
    window.addEventListener('beforeunload', function(e) {
        if (formIsDirty && !formIsSubmitting) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Single unified click: Klik foto atau badge kamera langsung buka file explorer
    if (avatarWrapper && fotoInput) {
        avatarWrapper.addEventListener('click', function(e) {
            fotoInput.click();
        });
        avatarWrapper.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fotoInput.click();
            }
        });
    }

    // Live Preview & Client-Side File Hardening
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Hardening 1: Validasi Ukuran File (Maks 2MB = 2,097,152 bytes)
        const maxSizeBytes = 2 * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            fotoInput.value = "";
            const msg = 'Ukuran berkas foto terlalu besar (' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). Maksimal ukuran yang diperbolehkan adalah 2MB.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Foto Terlalu Besar',
                    text: msg,
                    confirmButtonColor: '#0d6efd',
                    customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                    buttonsStyling: false
                });
            } else {
                alert(msg);
            }
            return;
        }

        // Hardening 2: Validasi Format MIME
        const allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedMimes.includes(file.type.toLowerCase())) {
            fotoInput.value = "";
            const msg = 'Format berkas tidak didukung. Harap unggah foto dengan format JPG, JPEG, atau PNG.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Tidak Valid',
                    text: msg,
                    confirmButtonColor: '#0d6efd',
                    customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                    buttonsStyling: false
                });
            } else {
                alert(msg);
            }
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            profilePreview.src = e.target.result;
            profilePreview.classList.remove('d-none');
            profilePreview.classList.remove('avatar-photo-anim');
            void profilePreview.offsetWidth; // Trigger reflow for re-animation
            profilePreview.classList.add('avatar-photo-anim');
            profilePreviewInitials.classList.add('d-none');
            profilePreviewInitials.classList.remove('d-flex');
            hapusFotoFlag.value = "0";
            if (hapusFotoBtn) {
                hapusFotoBtn.classList.remove('d-none');
            }
            formIsDirty = true;
        };
        reader.onerror = function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal Membaca File', 
                    text: 'Terjadi kesalahan saat memproses gambar.',
                    customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                    buttonsStyling: false
                });
            }
        };
        reader.readAsDataURL(file);
    });

    // Hapus Foto Profil dengan Konfirmasi Aman
    if (hapusFotoBtn) {
        hapusFotoBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah trigger avatar click
            const doHapusFoto = function() {
                fotoInput.value = "";
                profilePreview.src = "<?= base_url('assets/img/undraw_profile.svg') ?>";
                profilePreview.classList.add('d-none');
                profilePreviewInitials.classList.remove('d-none');
                profilePreviewInitials.classList.add('d-flex');
                hapusFotoFlag.value = "1";
                hapusFotoBtn.classList.add('d-none');
                formIsDirty = true;
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Foto Profil?',
                    text: 'Foto profil akan dihapus dan digantikan dengan inisial nama Anda.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Foto',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger rounded-pill px-4',
                        cancelButton: 'btn btn-secondary rounded-pill px-4'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        doHapusFoto();
                    }
                });
            } else if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
                doHapusFoto();
            }
        });
    }

    // Password Visibility Toggle (Mata)
    document.querySelectorAll('.btn-toggle-pw').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // Real-Time Password Matching Feedback
    function checkPasswordMatch() {
        const val1 = pwInput.value;
        const val2 = pwConfirmInput.value;

        if (!val1 && !val2) {
            pwFeedback.style.display = 'none';
            return true;
        }

        pwFeedback.style.display = 'block';
        if (val1 && val2 && val1 === val2) {
            pwFeedback.className = 'small mt-1 text-success fw-semibold match-pop-anim';
            pwFeedback.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Password cocok.';
            return true;
        } else if (val2) {
            pwFeedback.className = 'small mt-1 text-danger fw-semibold match-pop-anim';
            pwFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Konfirmasi password tidak cocok.';
            return false;
        } else {
            pwFeedback.className = 'small mt-1 text-muted match-pop-anim';
            pwFeedback.innerHTML = 'Masukkan ulang password baru untuk konfirmasi.';
            return false;
        }
    }

    pwInput.addEventListener('input', checkPasswordMatch);
    pwConfirmInput.addEventListener('input', checkPasswordMatch);

    // Form Submit Handler
    function handleFormSubmit(e) {
        const newPw = pwInput.value;
        const confirmPw = pwConfirmInput.value;

        if (newPw) {
            if (newPw.length < 6) {
                if (e) e.preventDefault();
                pwInput.focus();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Password Terlalu Pendek', 
                        text: 'Password baru minimal harus 6 karakter.',
                        customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                        buttonsStyling: false
                    });
                } else {
                    alert('Password baru minimal harus 6 karakter.');
                }
                return false;
            }

            if (newPw !== confirmPw) {
                if (e) e.preventDefault();
                pwConfirmInput.focus();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Password Tidak Cocok', 
                        text: 'Konfirmasi password tidak cocok dengan password baru.',
                        customClass: { confirmButton: 'btn btn-primary rounded-pill px-4' },
                        buttonsStyling: false
                    });
                } else {
                    alert('Konfirmasi password tidak cocok dengan password baru.');
                }
                return false;
            }
        }

        // Lock submit to prevent double-post
        formIsSubmitting = true;
        if (btnSubmitProfile) {
            btnSubmitProfile.disabled = true;
            btnSubmitText.textContent = 'Menyimpan...';
            btnSubmitProfile.querySelector('i').className = 'spinner-border spinner-border-sm me-1';
        }
        if (btnMobileSubmitProfile) {
            btnMobileSubmitProfile.disabled = true;
            btnMobileSubmitText.textContent = 'Menyimpan...';
            btnMobileSubmitProfile.querySelector('i').className = 'spinner-border spinner-border-sm me-1';
        }
        return true;
    }

    profileForm.addEventListener('submit', handleFormSubmit);

    // Trigger Form Submit from Mobile Floating Bar
    if (btnMobileSubmitProfile) {
        btnMobileSubmitProfile.addEventListener('click', function() {
            if (profileForm.reportValidity()) {
                if (handleFormSubmit()) {
                    profileForm.submit();
                }
            }
        });
    }

    // Inisialisasi Select2 jika tersedia
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
});
</script>
<?= $this->endSection() ?>