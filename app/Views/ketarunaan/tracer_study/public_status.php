<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Tracer Study - ECC') ?></title>
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .pub-shell {
            max-width: 560px;
            width: 100%;
            margin: auto;
            padding: 24px 16px;
        }
        .pub-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px 24px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body>
<?php
$status   = (string) ($status ?? 'invalid');
$periode  = $periode ?? null;
$verified = $verified ?? null;
$token    = (string) ($token ?? '');
?>
<div class="pub-shell">
    <div class="pub-card text-center">
        <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" width="56" height="56" style="width: 56px; height: 56px; object-fit: contain;" class="mb-3">
        <div class="small fw-semibold text-primary mb-2">Evidence Command Center (ECC) &bull; PKTJ Tegal</div>

        <h1 class="h4 fw-bold text-dark mb-2"><?= esc((string) ($heading ?? 'Informasi Survei')) ?></h1>
        <?php if (!empty($periode['nama_periode'])): ?>
            <div class="badge bg-light text-dark border rounded-pill px-3 py-1 mb-3" style="font-size: 0.75rem;">
                <?= esc((string) $periode['nama_periode']) ?>
            </div>
        <?php endif; ?>

        <p class="text-secondary small mb-4">
            <?= esc((string) ($message ?? '')) ?>
        </p>

        <?php if ($status === 'selesai' && !empty($verified)): ?>
            <?php if (isset($verified['is_alumni_terdaftar']) && (int) $verified['is_alumni_terdaftar'] === 0): ?>
                <div class="alert alert-info border-0 rounded-3 py-2 px-3 small text-start mb-3">
                    Data identitas alumni Anda turut tercatat dan sedang menunggu verifikasi oleh pengelola PKTJ untuk dimasukkan ke direktori alumni resmi.
                </div>
            <?php endif; ?>
            <div class="p-3 rounded-3 bg-light border text-start small mb-4">
                <div class="fw-semibold text-dark"><?= esc((string) ($verified['nama_lengkap'] ?? '')) ?></div>
                <div class="text-secondary">
                    Nomor Taruna: <strong><?= esc((string) ($verified['nomor_taruna'] ?? '')) ?></strong>
                    &bull; <?= esc((string) ($verified['kode_prodi'] ?? '-')) ?>
                </div>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <?php if ($token !== ''): ?>
                    <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'ganti-alumni')) ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Selesai</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <a href="<?= site_url('login') ?>" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                Masuk ke Portal ECC
            </a>
        <?php endif; ?>
    </div>
    <div class="text-center text-muted small mt-3">
        &copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal
    </div>
</div>
</body>
</html>
