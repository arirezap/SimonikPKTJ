<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php
    $pageTitle = trim((string) $this->renderSection('title'));
    $fullTitle = $pageTitle !== '' ? ($pageTitle . ' - Evidence Command Center (ECC)') : 'Evidence Command Center (ECC)';
    ?>
    <title><?= esc($fullTitle) ?></title>

    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">

    <style>
        :root {
            --ecc-bg: #f8fafc;
            --ecc-surface: #ffffff;
            --ecc-border: #e2e8f0;
            --ecc-text: #0f172a;
            --ecc-muted: #64748b;
            --ecc-primary: #1d4ed8;
            --ecc-primary-soft: #eff6ff;
            --ecc-success: #15803d;
            --ecc-success-soft: #f0fdf4;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--ecc-bg);
            color: var(--ecc-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }

        .survei-topbar {
            background-color: var(--ecc-surface);
            border-bottom: 1px solid var(--ecc-border);
            min-height: 64px;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .survei-container {
            width: 100%;
            max-width: 880px;
            margin: 0 auto;
            padding: 24px 16px 48px;
            flex: 1 0 auto;
        }

        .card-bento-survei {
            background-color: var(--ecc-surface);
            border: 1px solid var(--ecc-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        @media (max-width: 767.98px) {
            .card-bento-survei {
                padding: 16px;
            }
            .survei-container {
                padding: 16px 12px 96px;
            }
            input.form-control,
            select.form-select,
            textarea.form-control {
                font-size: 16px !important;
            }
        }

        .form-control,
        .form-select {
            min-height: 40px;
            border-radius: 8px;
            border-color: #cbd5e1;
            color: var(--ecc-text);
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .btn-tactile {
            transition: transform 0.16s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.16s ease, box-shadow 0.16s ease;
        }

        .btn-tactile:active:not(:disabled) {
            transform: scale(0.97);
        }

        .survei-footer {
            border-top: 1px solid var(--ecc-border);
            background-color: var(--ecc-surface);
            padding: 16px;
            font-size: 0.8125rem;
            color: var(--ecc-muted);
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <header class="survei-topbar">
        <div class="mx-auto d-flex align-items-center justify-content-between gap-3" style="max-width: 880px;">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" width="40" height="40" style="width: 40px; height: 40px; object-fit: contain;">
                <div>
                    <div class="fw-bold text-dark lh-sm" style="font-size: 0.9375rem;">Evidence Command Center (ECC)</div>
                    <div class="text-secondary" style="font-size: 0.75rem;">Politeknik Keselamatan Transportasi Jalan</div>
                </div>
            </div>
            <span class="badge rounded-pill text-primary border border-primary-subtle fw-semibold tabular-nums" style="background-color: var(--ecc-primary-soft); padding: 4px 12px; font-size: 0.75rem;">
                Survei SDM <?= esc((string) ($tahunSurvei ?? date('Y'))) ?>
            </span>
        </div>
    </header>

    <main class="survei-container">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="survei-footer text-center">
        <div class="mx-auto" style="max-width: 880px;">
            &copy; <?= date('Y') ?> Evidence Command Center (ECC) &mdash; PKTJ Tegal
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('wheel', function (e) {
            if (document.activeElement && document.activeElement.type === 'number') {
                document.activeElement.blur();
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>

