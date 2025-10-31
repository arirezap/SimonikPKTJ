<?php
// app/Views/layouts/sidebar.php

$current_uri = uri_string();
$role = session()->get('role');

// Logika untuk menu aktif
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
    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <!-- Wrapper untuk menu agar bisa scroll -->
    <div class="sidebar-menu">
        <ul class="nav flex-column">

            <?php if ($role === 'admin'): ?>
                <!-- ================= MENU KHUSUS ADMIN (SEMUA AKSES) ================= -->
                <li class="nav-item">
                    <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-grid-fill"></i><span>Dashboard</span>
                    </a>
                </li>

                <!-- Menu Kinerja -->
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
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('user/keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>"><span>Input Progres Keuangan</span></a></li>
                        </ul>
                    </div>
                </li>

                <!-- Menu Akademik -->
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

                <!-- Menu Manajemen -->
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Kinerja</span></a></li>
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
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/led-kategori') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led-kategori') ? 'active' : '' ?>"><span>Kategori LED</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('admin/users') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Kelola Pengguna</span></a></li>

            <?php elseif ($role === 'manajemen'): ?>
                <!-- ================= MENU GABUNGAN UNTUK MANAJEMEN ================= -->
                <li class="nav-item"><a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard Global</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-graph-up-arrow"></i><span>Kinerja Saya</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>"><span>Input Rencana Kerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>"><span>Input Realisasi</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>"><span>Kelola Target & Realisasi</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Tim</span></a></li>
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
                            <li class="nav-item"><a href="<?= site_url('admin/master-data/led-kategori') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led-kategori') ? 'active' : '' ?>"><span>Kategori LED</span></a></li>
                        </ul>
                    </div>
                </li>

            <?php elseif ($role === 'kabag_aak'): ?>
                <!-- ================= MENU KHUSUS KABAG AAK ================= -->
                <li class="nav-item"><a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard Global</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-graph-up-arrow"></i><span>Kinerja Saya</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>"><span>Input Rencana Kerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>"><span>Input Realisasi</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>"><span>Kelola Target & Realisasi</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Tim AAK</span></a></li>
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

            <?php elseif ($role === 'kabag_kuk'): ?>
                <!-- ================= MENU KHUSUS KABAG KUK ================= -->
                <li class="nav-item"><a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= ($current_uri == 'admin/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard Global</span></a></li>
                <li class="nav-item">
                    <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-graph-up-arrow"></i><span>Kinerja Saya</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('user/rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>"><span>Input Rencana Kerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>"><span>Input Realisasi</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('user/kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>"><span>Kelola Target & Realisasi</span></a></li>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('user/keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>"><span>Input Progres Keuangan</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('admin/monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Tim KUK</span></a></li>

            <?php elseif (in_array($role, ['aak', 'spm'])): // PERUBAHAN: Gabungkan AAK dan SPM ?>
                <!-- ================= MENU KHUSUS AAK ================= -->
                <li class="nav-item"><a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
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
                        </ul>
                    </div>
                </li>
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

            <?php elseif ($role === 'kuk'): ?>
                <!-- ================= MENU KHUSUS KUK ================= -->
                <li class="nav-item"><a href="<?= site_url('user/dashboard') ?>" class="nav-link <?= ($current_uri == 'user/dashboard') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
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
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('user/keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>"><span>Input Progres Keuangan</span></a></li>
                        </ul>
                    </div>
                </li>

            <?php endif; ?>

            <!-- ================= MENU BERSAMA UNTUK SEMUA ROLE ================= -->
            <li class="menu-divider-major"></li>

            <li class="nav-item">
                <a class="nav-link <?= $isEccActive ? '' : 'collapsed' ?>" href="#eccSubmenu" data-bs-toggle="collapse" role="button">
                    <i class="bi bi-display-fill"></i><span>ECC</span>
                    <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                </a>
                <div class="collapse <?= $isEccActive ? 'show' : '' ?>" id="eccSubmenu">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item"><a href="<?= site_url('ecc') ?>" class="nav-link sub-link <?= ($current_uri == 'ecc') ? 'active' : '' ?>"><span>Dashboard ECC</span></a></li>
                        <li class="nav-item"><a href="<?= site_url('ecc/led') ?>" class="nav-link sub-link <?= ($current_uri == 'ecc/led') ? 'active' : '' ?>"><span>LED</span></a></li>

                        <?php if ($role === 'spm'): // Tampilkan Simulasi HANYA untuk SPM
                        ?>
                            <li class="nav-item"><a href="<?= site_url('ecc/simulasi') ?>" class="nav-link sub-link <?= ($current_uri == 'ecc/simulasi') ? 'active' : '' ?>"><span>Simulasi Penilaian</span></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>

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
    </div> <!-- Penutup div .sidebar-menu -->
</div>