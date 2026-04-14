<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>400 - Permintaan Tidak Valid</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .error-card {
            text-align: center;
            background: #ffffff;
            padding: 3rem 2rem;
            border-radius: 12px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 90%;
            border-top: 5px solid #ffc107;
        }
        .error-icon {
            font-size: 5rem;
            color: #ffc107;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-title {
            font-weight: 700;
            color: #212529;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .error-subtitle {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .error-text {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="bi bi-exclamation-triangle error-icon"></i>
        <h1 class="error-title">400</h1>
        <h4 class="error-subtitle">Permintaan Tidak Valid (Bad Request)</h4>

        <p class="error-text">
        <?php if (ENVIRONMENT !== 'production') : ?>
            <?= nl2br(esc($message)) ?>
        <?php else : ?>
            Maaf, permintaan yang dikirimkan oleh peramban Anda tidak dapat diproses oleh server kami.
        <?php endif; ?>
        </p>
        
        <a href="<?= site_url() ?>" class="btn btn-warning px-4 py-2 fw-medium text-dark">
            <i class="bi bi-house-door me-2"></i>Kembali ke Beranda
        </a>
    </div>
</body>
</html>
