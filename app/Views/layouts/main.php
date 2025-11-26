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
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <div class="main-wrapper">
        <?php include(APPPATH . 'Views/layouts/sidebar.php'); ?>

        <div class="content-wrapper">
            
            <header class="navbar navbar-expand bg-white shadow-sm mb-4 rounded-3 px-4 py-3 border-bottom">
                <div class="container-fluid px-0">
                    <div class="d-flex flex-column">
                        <span class="text-muted small text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="bi bi-emoji-smile me-1"></i> Selamat Datang,
                        </span>
                        <h5 class="mb-0 fw-bold text-primary">
                            <?= esc(session()->get('nama_lengkap') ?? 'Pengguna') ?>
                        </h5>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="text-end d-none d-md-block">
                            <span class="badge bg-light text-dark border">
                                <?= strtoupper(esc(session()->get('role') ?? 'GUEST')) ?>
                            </span>
                            <div class="small text-muted mt-1" style="font-size: 0.8rem;">
                                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                            </div>
                        </div>
                        
                        <div class="vr d-none d-md-block mx-2"></div>
                        
                        <a href="<?= site_url('profile') ?>" class="text-decoration-none" title="Lihat Profil">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill fs-5 text-primary"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </header>
            <div class="content-area">
                
                <div class="d-flex align-items-center mb-4">
                    <h1 class="mb-0"><?= $this->renderSection('page_title') ?></h1>
                </div>

                <?= $this->renderSection('content') ?>
            </div>
            
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
                // Agar saat diklik/hover dia tidak error, tapi pakai CSS hover
                dropdownLinks.forEach(link => {
                    link.setAttribute('data-bs-toggle-backup', 'collapse'); // Simpan atribut asli
                    link.removeAttribute('data-bs-toggle'); // Hapus atribut pemicu
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
        setSidebarState(isToggled);

        // Event Listener Tombol Toggle
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