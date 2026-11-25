<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title><?= esc($title ?? 'Formulir Tracer Study - ECC') ?></title>
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
    <?= view('partials/_wilayah_styles') ?>
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
    </style>
</head>
<body>
<?php
$token         = (string) ($token ?? '');
$periode       = $periode ?? [];
$verified      = $verified ?? [];
$ctx           = $ctx ?? [];
$groupedQ      = $ctx['grouped_questions'] ?? [];
$sectionIntros = $ctx['section_intros'] ?? [];
$prefills      = $ctx['prefills'] ?? [];
$alumni        = $ctx['alumni'] ?? [];
$existing      = $ctx['existing'] ?? null;
$answers       = $ctx['answers'] ?? [];
$refs          = $refs ?? [];
$hideLegacy    = !empty($ctx['hide_legacy_work_block']);

$valTransisi = (string) old('status_transisi', (string) ($existing['status_transisi'] ?? 'bekerja'));
$valTunggu   = (string) old('masa_tunggu_bulan', (string) ($existing['masa_tunggu_bulan'] ?? ''));
$valInstansi = (string) old('nama_instansi', (string) ($existing['nama_instansi'] ?? ($alumni['instansi_saat_ini'] ?? '')));
$valSektor   = (string) old('sektor_industri', (string) ($existing['sektor_industri'] ?? ''));
$valJabatan  = (string) old('jabatan_posisi', (string) ($existing['jabatan_posisi'] ?? ($alumni['pekerjaan_saat_ini'] ?? '')));
$valKota     = (string) old('kota_kerja', (string) ($existing['kota_kerja'] ?? ($alumni['kota_domisili'] ?? '')));
$valBidang   = (string) old('kesesuaian_bidang', (string) ($existing['kesesuaian_bidang'] ?? ''));
$valJenjang  = (string) old('kesesuaian_jenjang', (string) ($existing['kesesuaian_jenjang'] ?? ''));
?>
<main class="pub-form-wrap">
    <div class="ecc-ts-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" width="48" height="48" style="width: 48px; height: 48px; object-fit: contain;">
                <div>
                    <div class="small fw-semibold text-primary">Tracer Study Lulusan &bull; PKTJ Tegal</div>
                    <h1 class="h5 fw-bold text-dark mb-0"><?= esc((string) ($periode['nama_periode'] ?? 'Survei Tracer Study')) ?></h1>
                </div>
            </div>
            <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'ganti-alumni')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-light border btn-sm rounded-pill px-3">Ganti Alumni</button>
            </form>
        </div>

        <div class="mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2 small">
            <div>
                Responden: <strong class="text-dark"><?= esc((string) ($verified['nama_lengkap'] ?? '')) ?></strong>
                (<span class="ecc-num"><?= esc((string) ($verified['nomor_taruna'] ?? '')) ?></span> &bull; <?= esc((string) ($verified['kode_prodi'] ?? '-')) ?><?php if (!empty($verified['tahun_lulus'])): ?> &bull; Lulus <?= esc((string) $verified['tahun_lulus']) ?><?php endif; ?>)
            </div>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Pengisian 1 Kali (Final)</span>
        </div>

        <?php if (!empty($periode['deskripsi'])): ?>
            <div class="mt-3 pt-3 border-top">
                <details>
                    <summary class="small fw-semibold text-primary" style="cursor: pointer;">Baca Surat Pengantar Tim Tracer Study PKTJ</summary>
                    <div class="pub-letter-box mt-2"><?= esc((string) $periode['deskripsi']) ?></div>
                </details>
            </div>
        <?php endif; ?>

        <div class="mt-2 small text-danger">* Menunjukkan pertanyaan yang wajib diisi</div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3" role="alert">
            <?= esc((string) session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="ecc-ts-card">
        <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'simpan')) ?>" class="js-ts-dynamic-form" data-sec-start="<?= $hideLegacy ? '1' : '2' ?>">
            <?= csrf_field() ?>

            <?php if (!$hideLegacy): ?>
                <div class="mb-4">
                    <h2 class="h6 fw-bold text-primary mb-3">1. Profil Transisi Lulusan &amp; Pekerjaan</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="pubStatusTransisi" class="form-label fw-semibold small">Status Kegiatan Utama Saat Ini <span class="text-danger">*</span></label>
                            <select name="status_transisi" id="pubStatusTransisi" class="form-select ecc-control-sm" required onchange="togglePubWorkFields()">
                                <?php foreach (($refs['status_transisi'] ?? []) as $sk => $sm): ?>
                                    <option value="<?= esc($sk) ?>" <?= $valTransisi === $sk ? 'selected' : '' ?>><?= esc($sm['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 js-pub-work">
                            <label for="pubInstansi" class="form-label fw-semibold small">Nama Instansi / Perusahaan / Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="nama_instansi" id="pubInstansi" class="form-control ecc-control-sm" value="<?= esc($valInstansi) ?>" placeholder="Contoh: Kementerian Perhubungan / PT ...">
                        </div>
                        <div class="col-12 col-md-6 js-pub-work">
                            <label for="pubJabatan" class="form-label fw-semibold small">Jabatan / Posisi</label>
                            <input type="text" name="jabatan_posisi" id="pubJabatan" class="form-control ecc-control-sm" value="<?= esc($valJabatan) ?>" placeholder="Contoh: Analis Transportasi / Teknisi">
                        </div>
                        <div class="col-12 col-md-6 js-pub-work">
                            <label for="pubSektor" class="form-label fw-semibold small">Sektor Industri</label>
                            <select name="sektor_industri" id="pubSektor" class="form-select ecc-control-sm">
                                <option value="">-- Pilih Sektor --</option>
                                <?php foreach (($refs['sektor_industri'] ?? []) as $sek): ?>
                                    <option value="<?= esc($sek) ?>" <?= $valSektor === $sek ? 'selected' : '' ?>><?= esc($sek) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 js-pub-work">
                            <label for="pubKota" class="form-label fw-semibold small">Kota / Kabupaten Lokasi Kerja</label>
                            <select name="kota_kerja" id="pubKota" class="form-select ecc-control-sm js-ecc-wilayah-kab-only" data-allow-tags="1" data-current-value="<?= esc($valKota) ?>" data-placeholder="-- Pilih Kabupaten / Kota --">
                                <option value="">-- Pilih Kabupaten / Kota --</option>
                                <?php if ($valKota !== ''): ?>
                                    <option value="<?= esc($valKota) ?>" selected><?= esc($valKota) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 js-pub-work">
                            <label for="pubMasaTunggu" class="form-label fw-semibold small">Masa Tunggu Kerja (Bulan)</label>
                            <input type="number" name="masa_tunggu_bulan" id="pubMasaTunggu" class="form-control ecc-control-sm ecc-num" min="0" max="60" value="<?= esc($valTunggu) ?>" placeholder="0 jika sebelum lulus">
                        </div>
                        <div class="col-6 col-md-4 js-pub-work">
                            <label for="pubBidang" class="form-label fw-semibold small">Kesesuaian Bidang Prodi</label>
                            <select name="kesesuaian_bidang" id="pubBidang" class="form-select ecc-control-sm">
                                <option value="">-- Pilih --</option>
                                <?php foreach (($refs['kesesuaian_bidang'] ?? []) as $bk => $bl): ?>
                                    <option value="<?= esc($bk) ?>" <?= $valBidang === $bk ? 'selected' : '' ?>><?= esc($bl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 js-pub-work">
                            <label for="pubJenjang" class="form-label fw-semibold small">Kesesuaian Jenjang</label>
                            <select name="kesesuaian_jenjang" id="pubJenjang" class="form-select ecc-control-sm">
                                <option value="">-- Pilih --</option>
                                <?php foreach (($refs['kesesuaian_jenjang'] ?? []) as $jk => $jl): ?>
                                    <option value="<?= esc($jk) ?>" <?= $valJenjang === $jk ? 'selected' : '' ?>><?= esc($jl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?= view('ketarunaan/tracer_study/_survey_questions_fields', [
                'groupedQ'      => $groupedQ,
                'sectionIntros' => $sectionIntros,
                'answers'       => $answers,
                'prefills'      => $prefills,
                'codePrefills'  => [],
                'secStartNo'    => $hideLegacy ? 1 : 2,
                'idPrefix'      => 'pq_',
            ]) ?>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 border-top">
                <span class="ecc-text-muted-aa small">Pastikan jawaban sudah benar sebelum dikirim. Jawaban yang telah dikirim bersifat final.</span>
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    Kirim Jawaban Survei
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
<?= view('partials/_wilayah_scripts') ?>
<script>
function togglePubWorkFields() {
    var st = document.getElementById('pubStatusTransisi');
    if (!st) return;
    var isWork = (st.value === 'bekerja' || st.value === 'wirausaha');
    var inst = document.getElementById('pubInstansi');
    if (inst) inst.required = isWork;
    document.querySelectorAll('.js-pub-work').forEach(function(el) {
        el.style.opacity = isWork ? '1' : '0.65';
    });
}
document.addEventListener('DOMContentLoaded', function() {
    togglePubWorkFields();
});
</script>
</body>
</html>
