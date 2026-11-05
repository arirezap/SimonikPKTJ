<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Cache Control: Paksa browser selalu ambil halaman terbaru dari server -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php 
    $pageTitle = $this->renderSection('title');
    $pageTitle = trim((string)$pageTitle);
    if (empty($pageTitle)) {
        $pageTitle = 'ECC';
    } else {
        $pageTitle .= ' - ECC';
    }
    ?>
    <title><?= esc($pageTitle) ?></title>

    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('android-chrome-512x512.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=2.0.' . (file_exists(FCPATH . 'assets/css/style.css') ? filemtime(FCPATH . 'assets/css/style.css') : time())) ?>">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <div class="main-wrapper">
        <!-- SIDEBAR ECC 2.0 (MODUL KETARUNAAN & ALUMNI) -->
        <?= $this->include('layouts/ecc2_sidebar') ?>

        <div class="content-wrapper">
            <?php 
                $isMaintenanceModeOn = false;
                try {
                    $settingMdl = new \App\Models\SettingModel();
                    $isMaintenanceModeOn = ($settingMdl->getValue('enable_maintenance_mode', '0') === '1');
                } catch (\Throwable $e) {}
            ?>
            <?php if ($isMaintenanceModeOn && hasRole('admin')): ?>
                <div class="alert alert-warning border-0 rounded-0 py-2 px-3 mb-0 d-flex flex-wrap align-items-center justify-content-between text-dark shadow-sm" style="background: linear-gradient(90deg, #fef3c7, #fde68a); border-bottom: 2px solid #f59e0b !important; font-size: 0.82rem; z-index: 1040;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="spinner-grow spinner-grow-sm text-warning" role="status"></span>
                        <span><strong>Mode Pemeliharaan AKTIF:</strong> Pengguna biasa dialihkan ke halaman pemeliharaan. Anda memiliki akses penuh Administrator.</span>
                    </div>
                    <a href="<?= site_url('settings') ?>" class="btn btn-warning btn-sm rounded-pill px-3 py-0.5 fw-bold btn-tactile text-dark shadow-sm border border-warning-subtle" style="font-size: 0.75rem;">
                        <i class="bi bi-sliders me-1"></i> Kelola Pengaturan
                    </a>
                </div>
            <?php endif; ?>
            
            <!-- TOPBAR HEADER (PERSIS ECC 1.0) -->
            <header class="navbar navbar-expand header-promax mb-2 px-3 px-md-4 py-2">
                <div class="container-fluid px-0">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-primary d-lg-none me-2 p-0 text-decoration-none btn-tactile" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Buka navigasi menu">
                            <i class="bi bi-list fs-1 text-primary"></i>
                        </button>
                        
                        <?php
                            $rawName = session()->get('nama') ?? session()->get('nama_lengkap') ?? 'Pengguna';
                            $nameParts = explode(',', $rawName);
                            $mainName = trim($nameParts[0]);
                            $formattedName = ucwords(strtolower($mainName));
                            
                            if (count($nameParts) > 1) {
                                unset($nameParts[0]);
                                $formattedName .= ', ' . implode(', ', array_map('trim', $nameParts));
                            }
                            
                            $nameWords = explode(' ', $mainName);
                            $initials = strtoupper(substr($nameWords[0] ?? 'U', 0, 1));
                            if (count($nameWords) > 1) {
                                $initials .= strtoupper(substr($nameWords[1], 0, 1));
                            }
                        ?>

                        <div class="d-flex flex-column">
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">
                                Selamat datang,
                            </span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="user-name fw-bold text-dark" style="font-size: 1.15rem; line-height: 1;">
                                    <?= esc($formattedName) ?>
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill fw-bold border border-primary-subtle d-none d-sm-inline-block" style="font-size: 0.6875rem; letter-spacing: 0.04em;">
                                    <?= str_replace('_', ' ', strtoupper(esc(session()->get('role') ?? 'GUEST'))) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="date-text text-muted fw-medium d-none d-md-block" style="font-size: 0.85rem;">
                            <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                        </div>
                        
                        <!-- NOTIFICATION BELL -->
                        <div class="dropdown" id="notifDropdownContainer">
                            <a href="#" class="text-decoration-none position-relative d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownToggle" aria-label="Lihat notifikasi sistem">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-subtle position-relative" style="width: 40px; height: 40px;">
                                    <i class="bi bi-bell-fill text-muted fs-5"></i>
                                    <span id="notifBadge" class="position-absolute badge rounded-pill bg-danger d-none shadow-sm" style="font-size: 0.55rem; padding: 0.2em 0.42em; top: -2px; right: -2px; border: 2px solid #ffffff; line-height: 1; z-index: 2;">
                                        0
                                    </span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 rounded-4 overflow-hidden notif-dropdown-menu">
                                <div class="notif-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 0.9375rem;">
                                            <i class="bi bi-bell-fill me-2 text-primary"></i> Notifikasi
                                        </h6>
                                        <span id="notifHeaderBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-semibold d-none" style="font-size: 0.6875rem; padding: 2px 8px;">0 Baru</span>
                                    </div>
                                    <button type="button" class="btn btn-mark-all-read btn-tactile" id="markAllReadBtn" onclick="markAllNotificationsRead(event)" aria-label="Tandai semua notifikasi sudah dibaca">
                                        <i class="bi bi-check2-all fs-6"></i> <span>Tandai Semua Dibaca</span>
                                    </button>
                                </div>
                                <div id="notifList" class="list-group list-group-flush">
                                    <div class="p-4 text-center text-muted small">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="mt-2">Memuat notifikasi...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- USER PROFILE AVATAR & DROPDOWN -->
                        <div class="dropdown">
                            <a href="#" class="text-decoration-none d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil pengguna">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center shadow-sm profile-avatar" style="width: 42px; height: 42px; border: 2px solid #ffffff;">
                                    <?php
                                        $foto_session = session()->get('foto');
                                        $foto_header_path = 'assets/uploads/profile/' . $foto_session;
                                        if (!empty($foto_session) && file_exists(FCPATH . $foto_header_path)) :
                                    ?>
                                        <img src="<?= base_url($foto_header_path) ?>" alt="Foto Profil" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" width="42" height="42">
                                    <?php else : ?>
                                        <span class="text-primary fw-bold" style="font-size: 1.05rem; letter-spacing: 0.5px;"><?= $initials ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width: 200px;">
                                <li><h6 class="dropdown-header text-primary"><?= esc($formattedName) ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item btn-tactile" href="<?= site_url('profile') ?>">
                                        <i class="bi bi-person-circle me-2"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger btn-tactile" href="<?= site_url('logout') ?>" onclick="confirmLogout(event)">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </a>
                                </li>
                            </ul>
                            <!-- Form Tersembunyi untuk Logout via POST (Anti-CSRF Protection) -->
                            <form id="logoutPostForm" action="<?= site_url('logout') ?>" method="post" class="d-none">
                                <?= csrf_field() ?>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA (PERSIS ECC 1.0) -->
            <div class="content-area">
                <?= $this->renderSection('content') ?>
            </div>
            
            <!-- FOOTER APLIKASI (PERSIS ECC 1.0) -->
            <footer class="footer-promax py-3 mt-auto d-flex justify-content-between px-3 px-md-4 align-items-center">
                <span class="footer-text">&copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal</span>
                <span class="badge bg-light text-secondary border rounded-pill version-badge px-2.5 py-1" style="font-size: 0.75rem; font-variant-numeric: tabular-nums;">v 2.0</span>
            </footer>

            <?= $this->renderSection('footer_bar') ?>
        </div>
    </div>

    <!-- Scripts (Persis ECC 1.0) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainWrapper = document.querySelector('.main-wrapper');

        function setSidebarState(isMini) {
            if (isMini) {
                mainWrapper.classList.add('sidebar-toggled');
            } else {
                mainWrapper.classList.remove('sidebar-toggled');
            }
        }

        const isToggled = localStorage.getItem('sidebarToggled') === 'true';
        if (window.innerWidth >= 992) {
            setSidebarState(isToggled);
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                const willBeToggled = !mainWrapper.classList.contains('sidebar-toggled');
                setSidebarState(willBeToggled);
                localStorage.setItem('sidebarToggled', willBeToggled);
            });
        }

        // Smart Scroll Topbar Logic
        const topbar = document.querySelector('.header-promax');
        const contentWrapper = document.querySelector('.content-wrapper');
        let lastScrollY = contentWrapper ? contentWrapper.scrollTop : 0;

        if (topbar && contentWrapper) {
            contentWrapper.addEventListener('scroll', () => {
                const currentScrollY = contentWrapper.scrollTop;
                if (currentScrollY > lastScrollY && currentScrollY > 60) {
                    topbar.classList.add('header-hidden');
                } else {
                    topbar.classList.remove('header-hidden');
                }
                lastScrollY = currentScrollY;
            });
        }
    });

    function confirmLogout(event) {
        event.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: 'Apakah Anda yakin ingin keluar dari sistem?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutPostForm').submit();
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                document.getElementById('logoutPostForm').submit();
            }
        }
    }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
