<?php
// app/Views/layouts/sidebar.php

// Helper to get the current URL path
$current_uri = uri_string(); 

// Logic to check if any of the "Kinerja" submenu items are active
$isKinerjaActive = (str_starts_with($current_uri, 'user/rencana/input') || str_starts_with($current_uri, 'user/kinerja/update'));
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <?php if (session()->get('role') == 'admin'): ?>
        <!-- ================= TAMPILAN MENU UNTUK ADMIN (Tidak Berubah) ================= -->
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
                <a href="<?= site_url('profile') ?>" class="nav-link <?= ($current_uri == 'profile') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i><span>Profile</span>
                </a>
            </li>
            <li class="nav-item" style="margin-top: 2rem;">
                <a href="<?= site_url('logout') ?>" class="nav-link logout">
                    <i class="bi bi-box-arrow-left"></i><span>Logout</span>
                </a>
            </li>
        </ul>

    <?php else: // Jika bukan admin, berarti user biasa ?>
        <!-- ================= TAMPILAN MENU UNTUK USER (Sudah Diperbarui) ================= -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                </a>
            </li>
            
            <!-- PERUBAHAN DIMULAI DI SINI: Menu Kinerja Dropdown -->
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
                            <a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= ($current_uri == 'user/kinerja/update') ? 'active' : '' ?>">
                                <span>Target Kinerja</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- AKHIR DARI PERUBAHAN -->
            
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