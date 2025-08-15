<?php
// app/Views/layouts/sidebar.php

$current_uri = uri_string();

// Logika untuk membuat menu induk "Kinerja" tetap aktif
$isKinerjaActive = (
    str_starts_with($current_uri, 'user/rencana/input') || 
    str_starts_with($current_uri, 'user/realisasi/input') || // Ditambahkan
    str_starts_with($current_uri, 'user/kinerja/update') || 
    str_starts_with($current_uri, 'user/alokasi/bulanan')
);
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <?php if (session()->get('role') == 'admin'): ?>
        <!-- ========================================================== -->
        <!-- MENU UNTUK ADMIN -->
        <!-- ========================================================== -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= ($current_uri == 'admin/monitoring') ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line-fill"></i><span>Monitoring</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('admin/users') ?>" class="nav-link <?= ($current_uri == 'admin/users') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i><span>Kelola Pengguna</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('profile') ?>" class="nav-link <?= ($current_uri == 'profile') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i><span>Profil</span>
                </a>
            </li>
            <li class="nav-item" style="margin-top: 2rem;">
                <a href="<?= site_url('logout') ?>" class="nav-link logout">
                    <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                </a>
            </li>
        </ul>

    <?php else: // Menu User ?>
        <!-- ========================================================== -->
        <!-- MENU UNTUK USER -->
        <!-- ========================================================== -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= $isKinerjaActive ? 'true' : 'false' ?>" aria-controls="kinerjaSubmenu">
                    <i class="bi bi-graph-up-arrow"></i><span>Kinerja</span>
                </a>
                <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item">
                            <a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>">
                                <span>Input Rencana Kinerja</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>">
                                <span>Input Realisasi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= ($current_uri == 'user/kinerja/update' || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>">
                                <span>Kelola Target & Realisasi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
             <li class="nav-item">
                <a href="<?= site_url('profile') ?>" class="nav-link <?= ($current_uri == 'profile') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i><span>Profil</span>
                </a>
            </li>
            <li class="nav-item" style="margin-top: 2rem;">
                <a href="<?= site_url('logout') ?>" class="nav-link logout">
                    <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                </a>
            </li>
        </ul>
    <?php endif; ?>
</div>
