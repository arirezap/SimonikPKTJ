<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <!-- Cache Control: Paksa browser selalu ambil halaman terbaru dari server -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php 
    $pageTitle = $this->renderSection('title');
    $pageTitle = trim($pageTitle);
    if (empty($pageTitle)) {
        $pageTitle = 'ECC';
    } else {
        $pageTitle .= ' - ECC';
    }
    ?>
    <title><?= $pageTitle ?></title>

    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('android-chrome-512x512.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=2.0.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Bootstrap Offcanvas menangani backdrop secara otomatis -->

    <div class="main-wrapper">
        <?= $this->include('layouts/sidebar') ?>

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
                        
                        <?php $isSelfAccount = \App\Libraries\Ketarunaan\KetarunaanPolicy::isSelfAccountOnly(); ?>
                        <?php if (!$isSelfAccount): ?>
                        <!-- NOTIFICATION BELL -->
                        <div class="dropdown" id="notifDropdownContainer">
                            <a href="#" class="text-decoration-none position-relative d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownToggle" aria-label="Lihat notifikasi">
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
                                        <h6 class="m-0 fw-bold text-dark" style="font-size: 0.9375rem;">
                                            Notifikasi
                                        </h6>
                                        <span id="notifHeaderBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-semibold d-none" style="font-size: 0.6875rem; padding: 4px 8px;">0 Baru</span>
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
                        <?php endif; ?>

                        <div class="dropdown">
                            <a href="#" class="text-decoration-none d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil pengguna">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center shadow-sm profile-avatar" style="width: 40px; height: 40px; border: 2px solid #ffffff;">
                                    <?php
                                        $foto_session = session()->get('foto');
                                        $foto_header_path = 'assets/uploads/profile/' . $foto_session;
                                        if (!empty($foto_session) && file_exists(FCPATH . $foto_header_path)) :
                                    ?>
                                        <img src="<?= base_url($foto_header_path) ?>" alt="Foto Profil" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" width="40" height="40" decoding="async">
                                    <?php else : ?>
                                        <span class="text-primary fw-bold" style="font-size: 1.05rem; letter-spacing: 0.5px;"><?= $initials ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width: 200px;">
                                <li><h6 class="dropdown-header text-primary"><?= esc($formattedName) ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php if ($isSelfAccount): ?>
                                    <?php $selfPortalHref = site_url(ltrim(\App\Libraries\Ketarunaan\KetarunaanPolicy::getSelfPortalUrl(), '/')); ?>
                                    <li>
                                        <a class="dropdown-item btn-tactile" href="<?= esc($selfPortalHref) ?>">
                                            <i class="bi bi-person-circle me-2" aria-hidden="true"></i> Profil Saya
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item btn-tactile" href="<?= site_url('ketarunaan/keamanan-akun') ?>">
                                            <i class="bi bi-key me-2" aria-hidden="true"></i> Ubah Kata Sandi
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a class="dropdown-item btn-tactile" href="<?= site_url('profile') ?>">
                                            <i class="bi bi-person-circle me-2" aria-hidden="true"></i> Profil Saya
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <button type="button" class="dropdown-item text-danger btn-tactile" onclick="confirmLogout(event)">
                                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Keluar
                                    </button>
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

            <main class="content-area">
                <?php $renderedPageTitle = trim((string) $this->renderSection('page_title')); ?>
                <?php if ($renderedPageTitle !== ''): ?>
                <div class="d-flex align-items-center mb-3">
                    <h1 class="h3 mb-0 fw-bold text-dark"><?= $renderedPageTitle ?></h1>
                </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </main>
            
            <!-- Footer Aplikasi -->
            <footer class="footer-promax py-3 mt-auto d-flex justify-content-between px-3 px-md-4 align-items-center">
                <span class="footer-text">&copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal</span>
                <span class="badge bg-light text-secondary border rounded-pill version-badge px-3 py-1" style="font-size: 0.75rem; font-variant-numeric: tabular-nums;">v 2.0</span>
            </footer>

            <?= $this->renderSection('footer_bar') ?>
        </div>
    </div>

    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>

   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainWrapper = document.querySelector('.main-wrapper');

        // Fungsi untuk mengatur perilaku Dropdown
        function setSidebarState(isMini) {
            if (isMini) {
                mainWrapper.classList.add('sidebar-toggled');
            } else {
                mainWrapper.classList.remove('sidebar-toggled');
            }
            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', isMini ? 'false' : 'true');
            }
        }

        // Cek LocalStorage saat load
        const isToggled = localStorage.getItem('sidebarToggled') === 'true';
        if (window.innerWidth >= 992) {
            setSidebarState(isToggled); // Terapkan preferensi mini/lebar HANYA di layar Desktop
        }

        // Event Listener Tombol Toggle (Desktop)
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
        let lastScrollY = contentWrapper.scrollTop;

        if (topbar && contentWrapper) {
            contentWrapper.addEventListener('scroll', () => {
                const currentScrollY = contentWrapper.scrollTop;
                
                // Jika scroll ke bawah dan melewati batas tertentu, sembunyikan topbar
                if (currentScrollY > lastScrollY && currentScrollY > 60) {
                    topbar.classList.add('header-hidden');
                } else {
                    // Jika scroll ke atas, tampilkan kembali topbar
                    topbar.classList.remove('header-hidden');
                }
                
                lastScrollY = currentScrollY;
            });
        }
    });
</script>
    <?php if (!$isSelfAccount): ?>
    <?= $this->include('layouts/_notification_script') ?>
    <?php endif; ?>
<script>

    function confirmLogout(event) {
        if (event) event.preventDefault();
        const executeLogout = function() {
            const logoutForm = document.getElementById('logoutPostForm');
            if (logoutForm) {
                logoutForm.submit();
            }
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar dari ECC?',
                text: 'Sesi Anda saat ini akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-muted small mb-4',
                    confirmButton: 'btn btn-danger btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm',
                    cancelButton: 'btn btn-secondary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    executeLogout();
                }
            });
        } else if (confirm('Keluar dari ECC?')) {
            executeLogout();
        }
    }

    // Cegah perubahan angka pada input type=number secara tidak sengaja saat scrolling halaman
    document.addEventListener('wheel', function(e) {
        if (document.activeElement && document.activeElement.type === 'number') {
            document.activeElement.blur();
        }
    });
</script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>