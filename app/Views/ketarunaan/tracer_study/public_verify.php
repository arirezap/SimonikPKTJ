<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Verifikasi Tracer Study - ECC') ?></title>
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
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
            padding: 24px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }
        .ecc-control {
            min-height: 40px;
            border-radius: 8px;
        }
        .ecc-num {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
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
$periode    = $periode ?? [];
$token      = (string) ($token ?? '');
$pid        = (int) ($periode['id'] ?? 0);
$allowUnreg = !empty($allowUnreg);
$showDaftar = !empty($showDaftar) && $allowUnreg;
$refs       = $refs ?? [];

$preNotar   = (string) old('nomor_taruna', (string) (session()->getFlashdata('pub_prefill_notar') ?? ''));
$preDob     = (string) old('tanggal_lahir', (string) (session()->getFlashdata('pub_prefill_dob') ?? ''));
$preTahun   = (string) old('tahun_lulus', (string) (session()->getFlashdata('pub_prefill_tahun') ?: ''));
?>
<div class="pub-shell">
    <div class="pub-card">
        <div class="text-center mb-4">
            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" width="52" height="52" style="width: 52px; height: 52px; object-fit: contain;" class="mb-2">
            <div class="small fw-semibold text-primary mb-1">Tracer Study Alumni &bull; PKTJ Tegal</div>
            <h1 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($periode['nama_periode'] ?? 'Survei Tracer Study')) ?></h1>
            <p class="text-secondary small mb-0">
                <?= $showDaftar
                    ? 'Lengkapi identitas alumni di bawah untuk melanjutkan ke formulir survei.'
                    : 'Verifikasi identitas alumni terlebih dahulu untuk membuka formulir survei.' ?>
            </p>
        </div>

        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info border-0 rounded-3 py-2 px-3 small mb-3" role="alert">
                <?= esc((string) session()->getFlashdata('info')) ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3" role="alert">
                <?= esc((string) session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (!$showDaftar): ?>
            <!-- Langkah 1A: Verifikasi Alumni -->
            <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'verifikasi')) ?>">
                <?= csrf_field() ?>
                <input type="text" name="website_url" class="hp-field" tabindex="-1" autocomplete="off">

                <div class="mb-3">
                    <label for="pubNim" class="form-label fw-semibold small">Nomor Taruna (Notar) <span class="text-danger">*</span></label>
                    <input type="text" id="pubNim" name="nomor_taruna" class="form-control ecc-control ecc-num" inputmode="numeric" value="<?= esc($preNotar) ?>" required autofocus placeholder="Contoh: 26012100">
                </div>

                <div class="mb-3">
                    <label for="pubDob" class="form-label fw-semibold small">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="text" id="pubDob" name="tanggal_lahir" class="form-control ecc-control bg-white ecc-num js-pub-date" value="<?= esc($preDob) ?>" required placeholder="YYYY-MM-DD (Contoh: 2001-05-14)">
                </div>

                <div class="mb-4">
                    <label for="pubTahunLulus" class="form-label fw-semibold small">Tahun Lulus (Opsional)</label>
                    <input type="number" id="pubTahunLulus" name="tahun_lulus" class="form-control ecc-control ecc-num" min="1970" max="<?= (int) date('Y') ?>" value="<?= esc($preTahun) ?>" placeholder="Contoh: <?= (int) date('Y') - 1 ?>">
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold py-2 mb-3">
                    Lanjutkan ke Formulir Survei
                </button>
            </form>

            <?php if ($allowUnreg): ?>
                <div class="text-center mb-3">
                    <a href="<?= esc(ketarunaan_tracer_public_url($token) . '?daftar=1') ?>" class="small text-primary text-decoration-none fw-semibold">
                        Alumni angkatan lama / belum terdaftar di sistem? Isi identitas di sini
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Langkah 1B: Lengkapi Identitas Alumni Baru (Belum Terdaftar di Database) -->
            <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'daftar')) ?>">
                <?= csrf_field() ?>
                <input type="text" name="website_url" class="hp-field" tabindex="-1" autocomplete="off">

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="regNotar" class="form-label fw-semibold small">Nomor Taruna (Notar) <span class="text-danger">*</span></label>
                        <input type="text" id="regNotar" name="nomor_taruna" class="form-control ecc-control ecc-num" inputmode="numeric" value="<?= esc($preNotar) ?>" required placeholder="Contoh: 26012100">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="regTahun" class="form-label fw-semibold small">Tahun Lulus <span class="text-danger">*</span></label>
                        <input type="number" id="regTahun" name="tahun_lulus" class="form-control ecc-control ecc-num" min="1970" max="<?= (int) date('Y') ?>" value="<?= esc($preTahun) ?>" required placeholder="Contoh: 2023">
                    </div>
                    <div class="col-12">
                        <label for="regNama" class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="regNama" name="nama_lengkap" class="form-control ecc-control" value="<?= esc((string) old('nama_lengkap', '')) ?>" required placeholder="Nama lengkap sesuai ijazah">
                    </div>
                    <div class="col-12 col-sm-7">
                        <label for="regProdi" class="form-label fw-semibold small">Program Studi <span class="text-danger">*</span></label>
                        <select id="regProdi" name="program_studi_id" class="form-select ecc-control" required>
                            <option value="">-- Pilih Program Studi --</option>
                            <?php foreach (($refs['prodi'] ?? []) as $pd): ?>
                                <option value="<?= (int) $pd['id'] ?>" <?= (string) old('program_studi_id', '') === (string) $pd['id'] ? 'selected' : '' ?>>
                                    <?= esc($pd['kode_prodi'] . ' - ' . $pd['nama_prodi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-sm-5">
                        <label for="regDob" class="form-label fw-semibold small">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="text" id="regDob" name="tanggal_lahir" class="form-control ecc-control bg-white ecc-num js-pub-date" value="<?= esc($preDob) ?>" required placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="regHp" class="form-label fw-semibold small">No. HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" id="regHp" name="no_hp" class="form-control ecc-control ecc-num" value="<?= esc((string) old('no_hp', '')) ?>" required placeholder="Contoh: 0812xxxxxxxx">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="regEmail" class="form-label fw-semibold small">Alamat Email</label>
                        <input type="email" id="regEmail" name="email" class="form-control ecc-control" value="<?= esc((string) old('email', '')) ?>" placeholder="email@contoh.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold py-2 mb-3">
                    Lanjut Isi Survei Tracer Study
                </button>
            </form>

            <div class="text-center mb-3">
                <a href="<?= esc(ketarunaan_tracer_public_url($token)) ?>" class="small text-secondary text-decoration-none">
                    &larr; Kembali ke Verifikasi Alumni Terdaftar
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center pt-3 border-top">
            <div class="text-secondary small mb-2">Sudah memiliki akun login ECC Alumni?</div>
            <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $pid) ?>" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">
                Masuk dengan Akun ECC
            </a>
        </div>
    </div>

    <div class="text-center text-muted small mt-3">
        &copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal
    </div>
</div>

<script src="<?= base_url('assets/vendor/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/l10n/id.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-pub-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
    }
});
</script>
</body>
</html>
