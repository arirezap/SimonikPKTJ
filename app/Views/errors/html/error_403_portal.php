<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>403 - Akses Terbatas | Evidence Command Center (ECC)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= function_exists('base_url') ? base_url('assets/vendor/bootstrap/bootstrap.min.css') : '/assets/vendor/bootstrap/bootstrap.min.css' ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= function_exists('base_url') ? base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') : '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css' ?>">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 16px;
        }
        .error-card {
            text-align: center;
            background: #ffffff;
            padding: 32px 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            max-width: 480px;
            width: 100%;
        }
        .error-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background-color: #fef2f2;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 16px;
        }
        .error-code {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.75rem;
            line-height: 1.25;
            margin-bottom: 8px;
            font-variant-numeric: tabular-nums;
        }
        .error-desc {
            color: #475569;
            font-size: 0.875rem;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .btn-portal {
            min-height: 44px;
            padding: 8px 24px;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-icon-wrap" aria-hidden="true">
            <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
        </div>
        <h1 class="error-code">Akses Terbatas</h1>
        <p class="error-desc">
            <?= !empty($isTaruna)
                ? 'Halaman ini tidak tersedia untuk akun taruna.'
                : 'Halaman ini hanya tersedia untuk akun pegawai.' ?>
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php if (!empty($backUrl)): ?>
                <a href="<?= esc($backUrl) ?>" class="btn btn-primary rounded-pill fw-semibold btn-portal">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span><?= !empty($isTaruna) ? 'Kembali ke Portal Taruna' : 'Kembali ke Portal' ?></span>
                </a>
            <?php endif; ?>
            <form action="<?= function_exists('site_url') ? site_url('logout') : '/logout' ?>" method="post" class="d-inline">
                <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                <button type="submit" class="btn btn-outline-danger rounded-pill fw-semibold btn-portal">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </main>
</body>
</html>
