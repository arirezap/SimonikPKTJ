<?php
// app/Views/layouts/sidebar.php

$current_uri = uri_string();
$role = session()->get('role');

// Logika untuk menu aktif
$isKinerjaActive = (str_starts_with($current_uri, 'user/'));
$isAdminManagementActive = (str_starts_with($current_uri, 'admin/users') || str_starts_with($current_uri, 'admin/monitoring'));
$isAkademikActive = str_starts_with($current_uri, 'user/akademik');
$isMasterDataActive = str_starts_with($current_uri, 'admin/master-data');
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <!-- Wrapper untuk menu agar bisa scroll -->
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <?php if ($role === 'admin'): ?>
                <!-- ================= MENU KHUSUS ADMIN (SEMUA AKSES) ================= -->
                <li class="nav-item"><a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button"><i class="bi bi-graph-up-arrow"></i><span>Kinerja</span></a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>">Input Rencana Kerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>">Input Realisasi</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>">Kelola Target & Realisasi</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Kinerja</span></a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('admin/users') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Kelola Pengguna</span></a>
                </li>
                <!-- MENU MASTER DATA DITAMBAHKAN KEMBALI -->
                <li class="nav-item">
                    <a class="nav-link <?= $isMasterDataActive ? '' : 'collapsed' ?>" href="#masterSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-stack"></i><span>Master Data</span>
                    </a>
                    <div class="collapse <?= $isMasterDataActive ? 'show' : '' ?>" id="masterSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/sasaran') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/sasaran') ? 'active' : '' ?>">Sasaran Program</a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/indikator') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/indikator') ? 'active' : '' ?>">Indikator Kinerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/satuan') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/satuan') ? 'active' : '' ?>">Satuan</a></li>
                        </ul>
                    </div>
                </li>

            <?php elseif ($role === 'manajemen'): ?>
                <!-- ================= MENU GABUNGAN UNTUK MANAJEMEN ================= -->
                <li class="nav-item"><a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard Global</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button"><i class="bi bi-graph-up-arrow"></i><span>Kinerja Saya</span></a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>">Input Rencana Kerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>">Input Realisasi</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>">Kelola Target & Realisasi</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Tim</span></a></li>
                <!-- MENU MASTER DATA DITAMBAHKAN KEMBALI -->
                <li class="nav-item">
                    <a class="nav-link <?= $isMasterDataActive ? '' : 'collapsed' ?>" href="#masterSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-stack"></i><span>Master Data</span>
                    </a>
                    <div class="collapse <?= $isMasterDataActive ? 'show' : '' ?>" id="masterSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/sasaran') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/sasaran') ? 'active' : '' ?>">Sasaran Program</a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/indikator') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/indikator') ? 'active' : '' ?>">Indikator Kinerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/satuan') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/satuan') ? 'active' : '' ?>">Satuan</a></li>
                        </ul>
                    </div>
                </li>
            <?php elseif ($role === 'aak'): ?>
                <!-- ================= MENU KHUSUS AAK ================= -->
                <li class="nav-item"><a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isAkademikActive ? '' : 'collapsed' ?>" href="#akademikSubmenu" data-bs-toggle="collapse" role="button"><i class="bi bi-book-half"></i><span>Data Akademik</span></a>
                    <div class="collapse <?= $isAkademikActive ? 'show' : '' ?>" id="akademikSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/akademik') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik') ? 'active' : '' ?>">Rangkuman</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/akademik/jadwal') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik/jadwal') ? 'active' : '' ?>">Kelola Jadwal Kuliah</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('user/ketarunaan') ?>" class="nav-link <?= ($current_uri == 'user/ketarunaan') ? 'active' : '' ?>"><i class="bi bi-shield-check"></i><span>Data Ketarunaan</span></a></li>
                <li class="nav-item"><a href="<?= site_url('user/diklat') ?>" class="nav-link <?= ($current_uri == 'user/diklat') ? 'active' : '' ?>"><i class="bi bi-easel-fill"></i><span>Data Diklat</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button"><i class="bi bi-graph-up-arrow"></i><span>Kinerja</span></a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>">Input Rencana Kerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>">Input Realisasi</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>">Kelola Target & Realisasi</a></li>
                        </ul>
                    </div>
                </li>

            <?php elseif ($role === 'kuk'): ?>
                <!-- ================= MENU KHUSUS KUK ================= -->
                <li class="nav-item"><a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button"><i class="bi bi-graph-up-arrow"></i><span>Kinerja</span></a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>">Input Rencana Kerja</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>">Input Realisasi</a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>">Kelola Target & Realisasi</a></li>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('user/keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>">Input Progres Keuangan</a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Menu yang sama untuk semua role -->
            <li class="nav-item"><a href="<?= site_url('profile') ?>" class="nav-link <?= ($current_uri == 'profile') ? 'active' : '' ?>"><i class="bi bi-person-circle"></i><span>Profil</span></a></li>
            <li class="nav-item" style="margin-top: 2rem;"><a href="<?= site_url('logout') ?>" class="nav-link logout"><i class="bi bi-box-arrow-left"></i><span>Logout</span></a></li>
        </ul>
    </div> <!-- Penutup div .sidebar-menu -->
</div>