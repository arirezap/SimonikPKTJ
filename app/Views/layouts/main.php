<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'ECC' ?></title>

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
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . filemtime(FCPATH . 'assets/css/style.css')) ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Bootstrap Offcanvas menangani backdrop secara otomatis -->

    <div class="main-wrapper">
        <?= $this->include('layouts/sidebar') ?>

        <div class="content-wrapper">
            
            <header class="navbar navbar-expand header-promax mb-4 px-3 px-md-4 py-2">
                <div class="container-fluid px-0">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-primary d-lg-none me-2 p-0 text-decoration-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                            <i class="bi bi-list fs-1 text-primary"></i>
                        </button>
                        <div class="d-flex flex-column">
                            <span class="greeting-text text-uppercase">
                                Selamat Datang,
                            </span>
                            <span class="user-name mt-1">
                                <?= esc(session()->get('nama') ?? session()->get('nama_lengkap') ?? 'Pengguna') ?>
                            </span>
                        </div>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <div class="role-badge mb-1">
                                <?= strtoupper(esc(session()->get('role') ?? 'GUEST')) ?>
                            </div>
                            <div class="date-text">
                                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                            </div>
                        </div>
                        
                        <div class="vr d-none d-md-block mx-2"></div>
                        
                        <div class="dropdown">
                            <a href="#" class="text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center profile-icon-wrapper" style="width: 40px; height: 40px;">
                                    <?php
                                        $foto_session = session()->get('foto');
                                        $foto_header_path = 'assets/uploads/profile/' . $foto_session;
                                        if (!empty($foto_session) && file_exists(FCPATH . $foto_header_path)) :
                                    ?>
                                        <img src="<?= base_url($foto_header_path) ?>" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else : // Fallback ke ikon jika tidak ada foto atau file tidak ditemukan ?>
                                        <i class="bi bi-person-fill fs-5 text-primary"></i>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width: 200px;">
                                <li><h6 class="dropdown-header text-primary"><?= esc(session()->get('nama')) ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= site_url('profile') ?>">
                                        <i class="bi bi-person-circle me-2"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                
                <div class="d-flex align-items-center mb-4">
                    <h1 class="mb-0"><?= $this->renderSection('page_title') ?></h1>
                </div>

                <?= $this->renderSection('content') ?>
            </div>
            
            <!-- Footer Aplikasi -->
            <footer class="footer-promax py-3 mt-auto d-flex justify-content-between px-4 align-items-center">
                <span class="footer-text">&copy; <?= date('Y') ?> ECC (Evidence Command Center)</span>
                <span class="version-badge text-muted">v1.0.0</span>
            </footer>

            <?= $this->renderSection('footer_bar') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainWrapper = document.querySelector('.main-wrapper');
        
        // Ambil semua link yang punya fungsi dropdown/collapse
        const dropdownLinks = document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]');

        // Fungsi untuk mengatur perilaku Dropdown (Accordion vs Hover)
        function setSidebarState(isMini) {
            if (isMini) {
                mainWrapper.classList.add('sidebar-toggled');
                
                // MATIKAN fungsi klik Bootstrap collapse saat mode mini
                dropdownLinks.forEach(link => {
                    link.setAttribute('data-bs-toggle-backup', 'collapse'); 
                    link.removeAttribute('data-bs-toggle'); 
                });

                // Tutup semua menu yang sedang terbuka agar rapi
                document.querySelectorAll('.sidebar .collapse.show').forEach(el => {
                    el.classList.remove('show');
                });

            } else {
                mainWrapper.classList.remove('sidebar-toggled');
                
                // KEMBALIKAN fungsi klik Bootstrap collapse saat mode normal
                dropdownLinks.forEach(link => {
                    link.setAttribute('data-bs-toggle', 'collapse');
                });
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
                
                // Simpan preferensi user
                localStorage.setItem('sidebarToggled', willBeToggled);
            });
        }


    });
</script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>