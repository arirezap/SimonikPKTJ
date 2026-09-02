<?php
// app/Views/layouts/sidebar.php

$uri = service('uri');
$segment1 = $uri->getSegment(1); // Cth: 'admin', 'user', 'ecc'
$segment2 = $uri->getSegment(2); // Cth: 'dashboard', 'led', 'users'

$current_uri = uri_string();
$role = session()->get('role');

// Helper untuk status aktif submenu
$isMasterDataActive = str_starts_with($current_uri, 'master-data');
$isEccActive = str_starts_with($current_uri, 'ecc');

// Definisi Peran Helper (menggunakan multi-role helper)
$isAdmin = hasRole('admin');
$isTimLeader = hasAnyRole(['manajemen', 'kabag', 'kabag_aak', 'kabag_kuk', 'kanit', 'katim', 'kapokja', 'admin']);
$isDirektur = hasRole('direktur');
$isKepegawaian = hasRole('kepegawaian');
?>


<div class="sidebar bg-white offcanvas-lg offcanvas-start border-end" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <i class="bi bi-list sidebar-toggle d-none d-lg-flex btn-tactile" id="sidebarToggle" title="Ciutkan / Lebarkan Navigasi" aria-label="Toggle Sidebar Navigation"></i>

    <div class="sidebar-header d-none d-lg-flex">
        <a href="<?= site_url('dashboard') ?>" title="Evidence Command Center (ECC) PKTJ" class="text-decoration-none">
            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" class="sidebar-logo" width="48" height="48" style="width: 48px; height: 48px; object-fit: contain;" loading="eager">
        </a>
    </div>
    
    <!-- Header Sidebar Mobile (Offcanvas) -->
    <div class="offcanvas-header d-lg-none border-bottom px-3 py-2.5">
        <div class="d-flex align-items-center gap-2">
            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" class="sidebar-logo" width="40" height="40" style="width: 40px; height: 40px; object-fit: contain;" loading="eager">
            <span class="fw-bold text-dark fs-6" id="sidebarOffcanvasLabel">ECC PKTJ</span>
        </div>
        <button type="button" class="btn-close text-reset btn-tactile" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Tutup navigasi"></button>
    </div>

    <div class="sidebar-menu offcanvas-body p-0 d-block">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($segment1 == 'dashboard') ? 'active' : '' ?>"
                    href="<?= site_url('dashboard') ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isEccActive ? 'active' : 'collapsed' ?>" href="#eccSubmenu" role="button" data-bs-toggle="collapse" aria-expanded="<?= $isEccActive ? 'true' : 'false' ?>" aria-controls="eccSubmenu">
                    <i class="bi bi-display-fill"></i><span>ECC</span>
                    <i class="bi bi-chevron-down sidebar-toggle-icon ms-auto"></i>
                </a>
                <div class="collapse <?= $isEccActive ? 'show' : '' ?>" id="eccSubmenu">
                    <ul class="nav flex-column ps-3 mb-1">
                        <li class="nav-item"><a href="<?= site_url('ecc/led') ?>" class="nav-link sub-link <?= ($segment2 == 'led') ? 'active' : '' ?>"><span>LED</span></a></li>

                        <?php if (hasAnyRole(['spm', 'admin'])): ?>
                            <li class="nav-item"><a href="<?= site_url('ecc/simulasi') ?>" class="nav-link sub-link <?= ($segment2 == 'simulasi') ? 'active' : '' ?>"><span>Simulasi Penilaian</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'laporan-harian') ? 'active' : '' ?>" href="<?= site_url('laporan-harian') ?>">
                    <i class="bi bi-bullseye"></i><span>Target Kinerja Bulanan</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'log-kegiatan') ? 'active' : '' ?>" href="<?= site_url('log-kegiatan') ?>">
                    <i class="bi bi-calendar-check-fill"></i><span>Lapor Kegiatan Harian</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'penilaian-kinerja') ? 'active' : '' ?>" href="<?= site_url('penilaian-kinerja') ?>">
                    <i class="bi bi-star-fill"></i><span>Rekap & Penilaian Kinerja</span>
                </a>
            </li>

            <?php if (hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])): ?>
                <?php $isKepegawaianActive = str_starts_with($current_uri, 'kepegawaian'); ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isKepegawaianActive ? 'active' : 'collapsed' ?>" href="#kepegawaianSubmenu" role="button" data-bs-toggle="collapse" aria-expanded="<?= $isKepegawaianActive ? 'true' : 'false' ?>" aria-controls="kepegawaianSubmenu">
                        <i class="bi bi-briefcase-fill"></i><span>Kepegawaian</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon ms-auto"></i>
                    </a>
                    <div class="collapse <?= $isKepegawaianActive ? 'show' : '' ?>" id="kepegawaianSubmenu">
                        <ul class="nav flex-column ps-3 mb-1">
                            <li class="nav-item">
                                <a href="<?= site_url('kepegawaian/target-kinerja') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'kepegawaian/target-kinerja')) ? 'active' : '' ?>">
                                    <span>Monitoring Target Kinerja</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= site_url('kepegawaian') ?>" class="nav-link sub-link <?= ($current_uri === 'kepegawaian' || str_starts_with($current_uri, 'kepegawaian/monitoring-penilaian') || (str_starts_with($current_uri, 'kepegawaian') && !str_starts_with($current_uri, 'kepegawaian/target-kinerja'))) ? 'active' : '' ?>">
                                    <span>Monitoring Penilaian Kinerja</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'kontrak') ? 'active' : '' ?>" href="<?= site_url('kontrak') ?>">
                    <i class="bi bi-file-earmark-text-fill"></i><span>Kontrak Kinerja</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'pakta') ? 'active' : '' ?>" href="<?= site_url('pakta') ?>">
                    <i class="bi bi-file-earmark-check-fill"></i><span>Pakta Integritas</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'panduan') ? 'active' : '' ?>" href="<?= site_url('panduan') ?>">
                    <i class="bi bi-book-half"></i><span>Panduan Penggunaan</span>
                </a>
            </li>

            <?php if ($isTimLeader): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (str_starts_with($current_uri, 'user/tim') || str_starts_with($current_uri, 'tim')) ? 'active' : '' ?>" href="<?= site_url('tim') ?>">
                        <i class="bi bi-people-fill"></i><span>Kelola Tim</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (hasAnyRole(['admin', 'kepegawaian', 'spm'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isMasterDataActive ? 'active' : 'collapsed' ?>" href="#masterDataSubmenu" role="button" data-bs-toggle="collapse" aria-expanded="<?= $isMasterDataActive ? 'true' : 'false' ?>" aria-controls="masterDataSubmenu">
                        <i class="bi bi-database-fill-gear"></i><span>Master Data</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon ms-auto"></i>
                    </a>
                    <div class="collapse <?= $isMasterDataActive ? 'show' : '' ?>" id="masterDataSubmenu">
                        <ul class="nav flex-column ps-3 mb-1">
                            <li class="nav-item"><a href="<?= site_url('master-data/sasaran') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/sasaran') ? 'active' : '' ?>"><span>Sasaran Program</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/indikator') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/indikator') ? 'active' : '' ?>"><span>Indikator Kinerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/satuan') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/satuan') ? 'active' : '' ?>"><span>Satuan</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/unit-kerja') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/unit-kerja') ? 'active' : '' ?>"><span>Unit Kerja</span></a></li>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/led') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/led') ? 'active' : '' ?>"><span>Kriteria LED</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/led-standar') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/led-standar') ? 'active' : '' ?>"><span>Standar LED</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/holidays') ?>" class="nav-link sub-link <?= str_starts_with($current_uri, 'master-data/holidays') ? 'active' : '' ?>"><span>Hari Libur Nasional</span></a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if (hasAnyRole(['admin', 'kepegawaian'])): ?>
                <li class="nav-item"><a href="<?= site_url('users') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/users') || str_starts_with($current_uri, 'users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Kelola Pengguna</span></a></li>
            <?php else: ?>
                <li class="nav-item"><a href="<?= site_url('daftar-pegawai') ?>" class="nav-link <?= str_starts_with($current_uri, 'daftar-pegawai') ? 'active' : '' ?>"><i class="bi bi-person-lines-fill"></i><span>Daftar Pegawai</span></a></li>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <li class="nav-item"><a href="<?= site_url('admin/audit-logs') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/audit-logs')) ? 'active' : '' ?>"><i class="bi bi-shield-check"></i><span>Log Aktivitas Sistem</span></a></li>
                <li class="nav-item"><a href="<?= site_url('settings') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/settings') || str_starts_with($current_uri, 'settings')) ? 'active' : '' ?>"><i class="bi bi-gear-fill"></i><span>Pengaturan Sistem</span></a></li>
            <?php endif; ?>

        </ul>
    </div>
</div>