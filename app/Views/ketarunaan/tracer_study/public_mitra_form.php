<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title><?= esc($title ?? 'Tracer Study Pengguna Lulusan - ECC') ?></title>
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
    <?= $this->include('ketarunaan/tracer_study/_styles') ?>
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            padding: 24px 16px 48px;
        }
        .pub-form-wrap {
            max-width: 820px;
            margin: 0 auto;
        }
        .pub-letter-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            font-size: 0.875rem;
            line-height: 1.65;
            color: #334155;
            white-space: pre-line;
        }
        .hp-field {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
<?php
$token         = (string) ($token ?? '');
$periode       = $periode ?? [];
$ctx           = $ctx ?? [];
$groupedQ      = $ctx['grouped_questions'] ?? [];
$sectionIntros = $ctx['section_intros'] ?? [];
$answers       = $ctx['answers'] ?? [];
$mitraPrev     = is_array($mitraPrev ?? null) ? $mitraPrev : [];
$countPrev     = (int) ($mitraPrev['count_submitted'] ?? 0);
$codePrefills  = [
    'TSM25_NAMA_ATASAN' => (string) ($mitraPrev['TSM25_NAMA_ATASAN'] ?? ''),
    'TSM25_INSTANSI'    => (string) ($mitraPrev['TSM25_INSTANSI'] ?? ''),
    'TSM25_EMAIL'       => (string) ($mitraPrev['TSM25_EMAIL'] ?? ''),
    'TSM25_NO_HP'       => (string) ($mitraPrev['TSM25_NO_HP'] ?? ''),
];
?>
<main class="pub-form-wrap">
    <div class="ecc-ts-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" width="48" height="48" style="width: 48px; height: 48px; object-fit: contain;">
                <div>
                    <div class="small fw-semibold text-primary">Tracer Study Pengguna Lulusan &bull; PKTJ Tegal</div>
                    <h1 class="h5 fw-bold text-dark mb-0"><?= esc((string) ($periode['nama_periode'] ?? 'Tracer Study Pengguna Lulusan PKTJ')) ?></h1>
                </div>
            </div>
            <?php if ($countPrev > 0): ?>
                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-2 small ecc-num">
                    Sudah menilai <?= $countPrev ?> alumni pada sesi ini
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($periode['deskripsi'])): ?>
            <div class="mt-3 pt-3 border-top">
                <details <?= $countPrev === 0 ? 'open' : '' ?>>
                    <summary class="small fw-semibold text-primary" style="cursor: pointer;">Surat Pengantar Resmi Tim Tracer Study PKTJ</summary>
                    <div class="pub-letter-box mt-2"><?= esc((string) $periode['deskripsi']) ?></div>
                </details>
            </div>
        <?php endif; ?>

        <div class="mt-3 pt-2 border-top small text-danger">
            * Menunjukkan pertanyaan yang wajib diisi
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3" role="alert">
            <?= esc((string) session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="ecc-ts-card">
        <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'simpan')) ?>" class="js-ts-dynamic-form" data-sec-start="1">
            <?= csrf_field() ?>
            <input type="text" name="website_url" class="hp-field" tabindex="-1" autocomplete="off">

            <?= view('ketarunaan/tracer_study/_survey_questions_fields', [
                'groupedQ'      => $groupedQ,
                'sectionIntros' => $sectionIntros,
                'answers'       => $answers,
                'prefills'      => [],
                'codePrefills'  => $codePrefills,
                'secStartNo'    => 1,
                'idPrefix'      => 'pm_',
            ]) ?>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
                <span class="ecc-text-muted-aa small">Bila terdapat lebih dari 1 alumni PKTJ, formulir dapat diisi kembali setelah dikirim.</span>
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    Kirim Penilaian Lulusan
                </button>
            </div>
        </form>
    </div>

    <div class="text-center text-muted small mt-3">
        &copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal
    </div>
</main>

<script src="<?= base_url('assets/vendor/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/l10n/id.js') ?>"></script>
</body>
</html>
