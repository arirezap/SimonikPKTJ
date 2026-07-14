<?php
// app/Views/layouts/sidebar.php

$uri = service('uri');
$segment1 = $uri->getSegment(1); // Cth: 'admin', 'user', 'ecc'
$segment2 = $uri->getSegment(2); // Cth: 'dashboard', 'led', 'users'

$current_uri = uri_string();
$role = session()->get('role');

// Helper untuk status aktif menu
$isKinerjaActive = (
    str_starts_with($current_uri, 'user/rencana') ||
    str_starts_with($current_uri, 'user/realisasi') ||
    str_starts_with($current_uri, 'user/kinerja') ||
    str_starts_with($current_uri, 'user/alokasi') ||
    str_starts_with($current_uri, 'user/keuangan') ||
    str_starts_with($current_uri, 'user/skp') 
);
$isAkademikActive = str_starts_with($current_uri, 'user/akademik');
$isMasterDataActive = str_starts_with($current_uri, 'admin/master-data');
$isEccActive = str_starts_with($current_uri, 'ecc');

// Definisi Peran Helper
$isAdmin = ($role === 'admin');
$isManajemenLevel = in_array($role, ['manajemen', 'kabag_aak']); // Gabungkan manajemen dan kabag_aak
$isKabagKuk = ($role === 'kabag_kuk');
$isDirektur = ($role === 'direktur'); 
?>


<div class="sidebar">
    <i class="bi bi-list sidebar-toggle" id="sidebarToggle"></i>

    <div class="sidebar-header">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link <?= ($segment1 == 'dashboard') ? 'active' : '' ?>"
                    href="<?= site_url('dashboard') ?>">
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

            <?php
            // Menu Kinerja (Termasuk Direktur)
            // Disembunyikan untuk manajemen & kabag karena data dummy
            if (in_array($role, ['admin', 'direktur', 'aak', 'kuk', 'spm'])):
            ?>
                <?php if (!$isAdmin): // Sembunyikan menu Kinerja untuk Admin ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $isKinerjaActive ? '' : 'collapsed' ?>" href="#kinerjaSubmenu" data-bs-toggle="collapse" role="button">
                            <i class="bi bi-graph-up-arrow"></i><span>Kinerja</span>
                            <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                        </a>
                        <div class="collapse <?= $isKinerjaActive ? 'show' : '' ?>" id="kinerjaSubmenu">
                            <ul class="nav flex-column ps-4">
                                <li class="nav-item"><a href="<?= site_url('rencana/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/rencana/input') ? 'active' : '' ?>"><span>Input Rencana Kerja</span></a></li>

                                <li class="nav-item">
                                    <a class="nav-link sub-link <?= str_starts_with($current_uri, 'user/skp') ? 'active' : '' ?>" href="<?= site_url('skp') ?>">
                                        <span>Sasaran Kinerja (SKP)</span>
                                    </a>
                                </li>
                                
                                <li class="nav-item"><a href="<?= site_url('realisasi/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/realisasi/input') ? 'active' : '' ?>"><span>Input Realisasi</span></a></li>
                                <li class="nav-item"><a href="<?= site_url('kinerja/update') ?>" class="nav-link sub-link <?= (str_starts_with($current_uri, 'user/kinerja/update') || str_starts_with($current_uri, 'user/alokasi/bulanan')) ? 'active' : '' ?>"><span>Kelola Target & Realisasi</span></a></li>

                                <?php if (in_array($role, ['admin', 'kabag_kuk', 'kuk'])): ?>
                                    <li class="menu-divider"></li>
                                    <li class="nav-item"><a href="<?= site_url('keuangan/input') ?>" class="nav-link sub-link <?= ($current_uri == 'user/keuangan/input') ? 'active' : '' ?>"><span>Input Progres Keuangan</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'user/laporan-harian') ? 'active' : '' ?>" href="<?= site_url('laporan-harian') ?>">
                    <i class="bi bi-bullseye"></i><span>Target Kinerja Bulanan</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'user/log-kegiatan') ? 'active' : '' ?>" href="<?= site_url('log-kegiatan') ?>">
                    <i class="bi bi-calendar-check-fill"></i><span>Lapor Kegiatan Harian</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'user/penilaian-kinerja') ? 'active' : '' ?>" href="<?= site_url('penilaian-kinerja') ?>">
                    <i class="bi bi-star-fill"></i><span>Rekap & Penilaian Kinerja</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'user/kontrak') ? 'active' : '' ?>" href="<?= site_url('kontrak') ?>">
                    <i class="bi bi-file-earmark-text-fill"></i><span>Kontrak Kinerja</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_starts_with($current_uri, 'user/pakta') ? 'active' : '' ?>" href="<?= site_url('pakta') ?>">
                    <i class="bi bi-file-earmark-check-fill"></i><span>Pakta Integritas</span>
                </a>
            </li>

            <?php if (false && $isAdmin): // Disembunyikan sementara ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isAkademikActive ? '' : 'collapsed' ?>" href="#akademikSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-book-half"></i><span>Data Akademik</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isAkademikActive ? 'show' : '' ?>" id="akademikSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('akademik') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik') ? 'active' : '' ?>"><span>Rangkuman</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('akademik/jadwal') ?>" class="nav-link sub-link <?= ($current_uri == 'user/akademik/jadwal') ? 'active' : '' ?>"><span>Kelola Jadwal Kuliah</span></a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item"><a href="<?= site_url('ketarunaan') ?>" class="nav-link <?= ($current_uri == 'user/ketarunaan') ? 'active' : '' ?>"><i class="bi bi-shield-check"></i><span>Data Ketarunaan</span></a></li>
                <li class="nav-item"><a href="<?= site_url('diklat') ?>" class="nav-link <?= ($current_uri == 'user/diklat') ? 'active' : '' ?>"><i class="bi bi-easel-fill"></i><span>Data Diklat</span></a></li>
            <?php endif; ?>


            <?php if (false && ($isManajemenLevel || $isKabagKuk)): // Untuk Kabag & Manajemen, SEMENTARA DISEMBUNYIKAN ?>
                <li class="nav-item"><a href="<?= site_url('monitoring') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/monitoring')) ? 'active' : '' ?>"><i class="bi bi-kanban-fill"></i><span>Monitoring Kinerja</span></a></li>
            <?php endif; ?>

            <?php if (false && $isAdmin): // Disembunyikan sementara ?>
                <li class="nav-item">

            <?php endif; ?>

            <?php if ($isAdmin || $isManajemenLevel || $isKabagKuk || $isDirektur): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isMasterDataActive ? '' : 'collapsed' ?>" href="#masterSubmenu" data-bs-toggle="collapse" role="button">
                        <i class="bi bi-stack"></i><span>Master Data</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </a>
                    <div class="collapse <?= $isMasterDataActive ? 'show' : '' ?>" id="masterSubmenu">
                        <ul class="nav flex-column ps-4">
                            <li class="nav-item"><a href="<?= site_url('master-data/sasaran') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/sasaran') ? 'active' : '' ?>"><span>Sasaran Program</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/indikator') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/indikator') ? 'active' : '' ?>"><span>Indikator Kinerja</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/satuan') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/satuan') ? 'active' : '' ?>"><span>Satuan</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/unit-kerja') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/unit-kerja') ? 'active' : '' ?>"><span>Unit Kerja</span></a></li>
                            <li class="menu-divider"></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/led') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led') ? 'active' : '' ?>"><span>Kriteria LED</span></a></li>
                            <li class="nav-item"><a href="<?= site_url('master-data/led-standar') ?>" class="nav-link sub-link <?= ($current_uri == 'admin/master-data/led-standar') ? 'active' : '' ?>"><span>Standar LED</span></a></li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <li class="nav-item"><a href="<?= site_url('users') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Kelola Pengguna</span></a></li>
                <li class="nav-item"><a href="<?= site_url('settings') ?>" class="nav-link <?= (str_starts_with($current_uri, 'admin/settings')) ? 'active' : '' ?>"><i class="bi bi-gear-fill"></i><span>Pengaturan Sistem</span></a></li>
            <?php endif; ?> 

        </ul>
    </div>
</div>