<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>429 - Terlalu Banyak Percobaan | Evidence Command Center (ECC)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="referrer" content="strict-origin-when-cross-origin">
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
            color: #0f172a;
            margin: 0;
            padding: 16px;
        }
        .error-card {
            text-align: center;
            background: #ffffff;
            padding: 32px 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            max-width: 480px;
            width: 100%;
        }
        .error-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background-color: #fef3c7;
            color: #b45309;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 16px;
        }
        .error-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.75rem;
            line-height: 1.25;
            margin: 0 0 8px;
        }
        .error-desc {
            color: #475569;
            font-size: 0.875rem;
            line-height: 1.6;
            margin: 0 0 24px;
        }
        .btn-action {
            min-height: 44px;
            padding: 8px 24px;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .error-meta {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            font-size: 0.75rem;
            color: #475569;
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-icon-wrap" aria-hidden="true">
            <i class="bi bi-hourglass-split" aria-hidden="true"></i>
        </div>
        <h1 class="error-title">Terlalu Banyak Percobaan</h1>
        <p class="error-desc">
            Aktivitas terlalu cepat. Tunggu sebentar lalu coba lagi.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= function_exists('site_url') ? site_url() : '/' ?>" class="btn btn-primary rounded-pill fw-semibold btn-action">
                <i class="bi bi-house-door" aria-hidden="true"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
        <p class="error-meta mb-0">Evidence Command Center (ECC) &bull; Kode 429</p>
    </main>
</body>
</html>
