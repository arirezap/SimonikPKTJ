<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'SIMONIK' ?></title>

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
            <div class="content-area">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-list sidebar-toggle" id="sidebarToggle"></i>
                    <h1 class="mb-0 ms-3"><?= $this->renderSection('page_title') ?></h1>
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

            if (localStorage.getItem('sidebarToggled') === 'true') {
                mainWrapper.classList.add('sidebar-toggled');
            }

            sidebarToggle.addEventListener('click', function() {
                mainWrapper.classList.toggle('sidebar-toggled');
                localStorage.setItem('sidebarToggled', mainWrapper.classList.contains('sidebar-toggled'));
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>