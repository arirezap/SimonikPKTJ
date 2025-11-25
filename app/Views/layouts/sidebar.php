<?php
// app/Views/layouts/sidebar.php

$uri = service('uri');
$segment1 = $uri->getSegment(1); // Cth: 'admin', 'user', 'ecc'
$segment2 = $uri->getSegment(2); // Cth: 'dashboard', 'led', 'users'

$current_uri = uri_string(); 
$role = session()->get('role');

$isKinerjaActive = (
    str_starts_with($current_uri, 'user/rencana') ||
    str_starts_with($current_uri, 'user/realisasi') ||
    str_starts_with($current_uri, 'user/kinerja') ||
    str_starts_with($current_uri, 'user/alokasi') ||
    str_starts_with($current_uri, 'user/keuangan')
);
$isAkademikActive = str_starts_with($current_uri, 'user/akademik');
$isMasterDataActive = str_starts_with($current_uri, 'admin/master-data');
$isEccActive = str_starts_with($current_uri, 'ecc');
?>

<div class="sidebar">
    <i class="bi bi-list sidebar-toggle" id="sidebarToggle"></i>
    
    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">

            <?php
            // Definisikan peran
            $isAdmin = in_array($role, ['admin', 'manajemen']);
            $isKabag = in_array($role, ['kabag_aak', 'kabag_kuk']);
            $isUser = in_array($role, ['aak', 'kuk', 'spm']);
            ?>

            <li class="nav-item">
                <a class="nav-link <?= ($segment1 == 'admin' && $segment2 == 'dashboard') || ($segment1 == 'user' && $segment2 == 'dashboard') ? 'active' : '' ?>" 
                   href="<?= $isAdmin || $isKabag ? site_url('admin/dashboard') : site_url('user/dashboard') ?>">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $isEccActive ? '' : 'collapsed' ?>" href="#eccSubmenu" data-bs-toggle="collapse" role="button">
                    <i class="bi bi-display-fill"></i><span>ECC</span>
                    <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                </a>
                <div class="collapse <?= $isEccActive ? 'show' : '' ?>" id="eccSubmenu">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item"><a href="<?= site_url('ecc/led') ?>" class="nav-link sub-link <?= ($segment2 == 'led') ? 'active' : '' ?>"><span>LED</span></a></li>
                        
                        <?php if ($role === 'spm' || $role === 'admin'): ?>
                            <li class="nav-item"><a href="<?= site_url('ecc/simulasi') ?>" class="nav-link sub-link <?= ($segment2 == 'simulasi') ? 'active' : '' ?>"><span>Simulasi Penilaian</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>


            <?php if (in_array($role, ['admin', 'manajemen', 'kabag_aak', 'kabag_kuk', 'aak', 'kuk', 'spm'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-graph-up-arrow"></i><span>Kinerja</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>"><span>Input Rencana Kerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>"><span>Input Realisasi</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>"><span>Kelola Target & Realisasi</span></a></li>
                            
                            <?php if(in_array($role, ['admin', 'kabag_kuk', 'kuk'])): // Hanya role keuangan ?>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('user/keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>"><span>Input Progres Keuangan</span></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isAkademikActive ? '' : 'collapsed' ?>" href="#akademikSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-book-half"></i><span>Data Akademik</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isAkademikActive ? 'show' : '' ?>" id="akademikSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/akademik') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik') ? 'active' : '' ?>"><span>Rangkuman</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/akademik/jadwal') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik/jadwal') ? 'active' : '' ?>"><span>Kelola Jadwal Kuliah</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('user/ketarunaan') ?>" class="nav-link <?= ($current_uri == 'user/ketarunaan') ? 'active' : '' ?>"><i class="bi bi-shield-check"></i><span>Data Ketarunaan</span></a></li>
                <li class="nav-item"><a href="<?= site_url('user/diklat') ?>" class="nav-link <?= ($current_uri == 'user/diklat') ? 'active' : '' ?>"><i class="bi bi-easel-fill"></i><span>Data Diklat</span></a></li>
            <?php endif; ?>


            <?php if ($isAdmin || $isKabag): ?>
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Kinerja</span></a></li>
                
                <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($segment1 == 'admin' && $segment2 == 'remunerasi') ? 'active' : '' ?>" 
                       href="<?= site_url('admin/remunerasi') ?>">
                        <i class="bi bi-wallet-fill"></i>
                        <span>Input Remunerasi</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= $isMasterDataActive ? '' : 'collapsed' ?>" href="#masterSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-stack"></i><span>Master Data</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isMasterDataActive ? 'show' : '' ?>" id="masterSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/sasaran') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/sasaran') ? 'active' : '' ?>"><span>Sasaran Program</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/indikator') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/indikator') ? 'active' : '' ?>"><span>Indikator Kinerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/satuan') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/satuan') ? 'active' : '' ?>"><span>Satuan</span></a></li>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/led') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led') ? 'active' : '' ?>"><span>Kriteria LED</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/led-standar') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led-standar') ? 'active' : '' ?>"><span>Standar LED</span></a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
            
            <?php if ($role === 'admin'): // Hanya Admin ?>
                <li class="nav-item"><a href="<?= site_url('admin/users') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Kelola Pengguna</span></a></li>
            <?php endif; ?>

            <li class="menu-divider-major"></li>
            <li class="nav-item">
                <a href="<?= site_url('profile') ?>" class="nav-link <?= ($current_uri == 'profile') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i><span>Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('logout') ?>" class="nav-link logout">
                    <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>