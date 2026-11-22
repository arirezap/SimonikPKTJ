<?php
// app/Views/layouts/ecc2_sidebar.php

$current_uri = uri_string();

// Status navigasi aktif
$isRingkasanActive   = ($current_uri === 'ketarunaan' || str_starts_with($current_uri, 'ketarunaan/dashboard'));
$isTarunaActive      = str_starts_with($current_uri, 'ketarunaan/taruna');
$isRekamJejakActive  = str_starts_with($current_uri, 'ketarunaan/rekam-jejak');
$isPrestasiActive    = str_starts_with($current_uri, 'ketarunaan/prestasi');
$isPelanggaranActive = str_starts_with($current_uri, 'ketarunaan/pelanggaran');
$isBeasiswaActive    = str_starts_with($current_uri, 'ketarunaan/beasiswa');
$isLowonganActive    = str_starts_with($current_uri, 'ketarunaan/lowongan');
$isAlumniActive      = str_starts_with($current_uri, 'ketarunaan/alumni');
$isTracerStudyActive = str_starts_with($current_uri, 'ketarunaan/tracer-study');
$isValidasiActive    = str_starts_with($current_uri, 'ketarunaan/validasi');
$isLaporanActive     = str_starts_with($current_uri, 'ketarunaan/laporan');
$isPengaturanActive  = str_starts_with($current_uri, 'ketarunaan/pengaturan');

helper('ketarunaan');
$isSelfTaruna  = \App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfTarunaOnly();
$isSelfAlumni  = \App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfAlumniOnly();
$isSelfAccount = $isSelfTaruna || $isSelfAlumni;
$sidebarHome   = ketarunaan_home_url();
?>

<div class="sidebar bg-white offcanvas-lg offcanvas-start border-end" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <button type="button" class="sidebar-toggle d-none d-lg-flex btn-tactile" id="sidebarToggle" title="Ciutkan / Lebarkan Navigasi" aria-label="Ciutkan atau lebarkan navigasi" aria-expanded="true">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <div class="sidebar-header d-none d-lg-flex">
        <a href="<?= esc($sidebarHome) ?>" title="Evidence Command Center (ECC) PKTJ" class="text-decoration-none">
            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" class="sidebar-logo" width="48" height="48" style="width: 48px; height: 48px; object-fit: contain;" loading="eager" decoding="async">
        </a>
    </div>
    
    <!-- Header Sidebar Mobile (Offcanvas) -->
    <div class="offcanvas-header d-lg-none border-bottom px-3 py-2">
        <div class="d-flex align-items-center gap-2">
            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" class="sidebar-logo" width="40" height="40" style="width: 40px; height: 40px; object-fit: contain;" loading="eager" decoding="async">
            <span class="fw-bold text-dark fs-6" id="sidebarOffcanvasLabel">ECC PKTJ</span>
        </div>
        <button type="button" class="btn-close text-reset btn-tactile" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Tutup navigasi"></button>
    </div>

    <div class="sidebar-menu offcanvas-body p-0 d-block">
        <ul class="nav flex-column">
            
            <?php if (!$isSelfAccount): ?>
            <li class="sidebar-category">DASHBOARD</li>
            <li class="nav-item">
                <a class="nav-link <?= $isRingkasanActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/dashboard') ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Ringkasan</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="sidebar-category">DATA KETARUNAAN</li>
            <?php if (!$isSelfAlumni): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isTarunaActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/taruna') ?>">
                    <i class="bi bi-people-fill"></i>
                    <span><?= $isSelfTaruna ? 'Profil Taruna Saya' : 'Database Taruna' ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isRekamJejakActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/rekam-jejak') ?>">
                    <i class="bi bi-person-lines-fill"></i>
                    <span><?= $isSelfTaruna ? 'Rekam Jejak Saya' : 'Rekam Jejak Taruna' ?></span>
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= $isPrestasiActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/prestasi') ?>">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Prestasi</span>
                </a>
            </li>

            <?php if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessPelanggaranModule()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isPelanggaranActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/pelanggaran') ?>">
                    <i class="bi bi-shield-fill-exclamation"></i>
                    <span>Pelanggaran</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessBeasiswaModule()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isBeasiswaActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/beasiswa') ?>">
                    <i class="bi bi-mortarboard-fill"></i>
                    <span>Beasiswa</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="sidebar-category">KARIER & ALUMNI</li>
            <li class="nav-item">
                <a class="nav-link <?= $isLowonganActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/lowongan') ?>">
                    <i class="bi bi-briefcase-fill"></i>
                    <span>Lowongan Pekerjaan</span>
                </a>
            </li>

            <?php if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessAlumniModule()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isAlumniActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/alumni') ?>">
                    <i class="bi bi-mortarboard" aria-hidden="true"></i>
                    <span>Alumni</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessTracerStudyModule()): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isTracerStudyActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/tracer-study') ?>">
                    <i class="bi bi-clipboard2-data-fill" aria-hidden="true"></i>
                    <span>Tracer Study</span>
                </a>
            </li>
            <?php endif; ?>

            <?php
            $canValidasi   = \App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessValidasiModule();
            $canLaporan    = \App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessLaporanModule();
            $canPengaturan = \App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessPengaturanModule();
            ?>
            <?php if ($canValidasi || $canLaporan || $canPengaturan): ?>
            <li class="sidebar-category">TATA KELOLA</li>
            <?php if ($canValidasi): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isValidasiActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/validasi') ?>">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Validasi Data</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($canLaporan): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isLaporanActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/laporan') ?>">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($canPengaturan): ?>
            <li class="nav-item">
                <a class="nav-link <?= $isPengaturanActive ? 'active' : '' ?>" href="<?= site_url('ketarunaan/pengaturan') ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (!$isSelfTaruna && !$isSelfAlumni): ?>
            <!-- TOMBOL PINDAH KE MODUL KINERJA DI PALING BAWAH -->
            <li class="menu-divider my-2"></li>
            <li class="nav-item">
                <a class="nav-link text-primary fw-semibold" href="<?= site_url('dashboard') ?>">
                    <i class="bi bi-grid-fill text-primary"></i>
                    <span>Modul Kinerja</span>
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </div>
</div>
