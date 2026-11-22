<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Formulir Tracer Study - ECC') ?></title>
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
    <?= $this->include('ketarunaan/tracer_study/_styles') ?>
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            padding: 24px 16px 48px;
        }
        .pub-form-wrap {
            max-width: 820px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<?php
$token       = (string) ($token ?? '');
$periode     = $periode ?? [];
$verified    = $verified ?? [];
$ctx         = $ctx ?? [];
$groupedQ    = $ctx['grouped_questions'] ?? [];
$alumni      = $ctx['alumni'] ?? [];
$existing    = $ctx['existing'] ?? null;
$answers     = $ctx['answers'] ?? [];
$refs        = $refs ?? [];

$valTransisi = (string) old('status_transisi', (string) ($existing['status_transisi'] ?? 'bekerja'));
$valTunggu   = (string) old('masa_tunggu_bulan', (string) ($existing['masa_tunggu_bulan'] ?? ''));
$valInstansi = (string) old('nama_instansi', (string) ($existing['nama_instansi'] ?? ($alumni['instansi_saat_ini'] ?? '')));
$valSektor   = (string) old('sektor_industri', (string) ($existing['sektor_industri'] ?? ''));
$valJabatan  = (string) old('jabatan_posisi', (string) ($existing['jabatan_posisi'] ?? ($alumni['pekerjaan_saat_ini'] ?? '')));
$valKota     = (string) old('kota_kerja', (string) ($existing['kota_kerja'] ?? ($alumni['kota_domisili'] ?? '')));
$valBidang   = (string) old('kesesuaian_bidang', (string) ($existing['kesesuaian_bidang'] ?? ''));
$valJenjang  = (string) old('kesesuaian_jenjang', (string) ($existing['kesesuaian_jenjang'] ?? ''));
?>
<div class="pub-form-wrap">
    <!-- Header Kampus & Identitas Alumni Terverifikasi -->
    <div class="ecc-ts-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ Tegal" width="48" height="48" style="width: 48px; height: 48px; object-fit: contain;">
                <div>
                    <div class="small fw-semibold text-primary">Tracer Study PKTJ Tegal</div>
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
                (<span class="ecc-num"><?= esc((string) ($verified['nomor_taruna'] ?? '')) ?></span> &bull; <?= esc((string) ($verified['kode_prodi'] ?? '-')) ?>)
            </div>
            <?php if ($existing): ?>
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1">Mode Pembaruan Jawaban</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3" role="alert">
            <?= esc((string) session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Formulir Survei -->
    <div class="ecc-ts-card">
        <form method="post" action="<?= esc(ketarunaan_tracer_public_url($token, 'simpan')) ?>">
            <?= csrf_field() ?>

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
                        <label for="pubKota" class="form-label fw-semibold small">Kota Lokasi Kerja</label>
                        <input type="text" name="kota_kerja" id="pubKota" class="form-control ecc-control-sm" value="<?= esc($valKota) ?>" placeholder="Contoh: Jakarta / Tegal">
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

            <?php $secNo = 2; ?>
            <?php foreach ($groupedQ as $secTitle => $qList): ?>
                <div class="mb-4 pt-3 border-top">
                    <h2 class="h6 fw-bold text-primary mb-3"><?= $secNo++ . '. ' . esc((string) $secTitle) ?></h2>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($qList as $q): ?>
                            <?php
                            $qid     = (int) $q['id'];
                            $tipe    = (string) $q['tipe_jawaban'];
                            $wajib   = ((int) ($q['is_wajib'] ?? 1) === 1);
                            $opsi    = $q['opsi_parsed'] ?? [];
                            $prev    = $answers[$qid] ?? null;
                            $oldAns  = old('jawaban.' . $qid);
                            $curText = $oldAns !== null ? (is_array($oldAns) ? '' : (string) $oldAns) : (string) ($prev['nilai_teks'] ?? '');
                            $curOpsi = is_array($oldAns) ? $oldAns : (is_array($prev['nilai_opsi'] ?? null) ? $prev['nilai_opsi'] : ($curText !== '' ? [$curText] : []));
                            ?>
                            <div class="ts-q-item">
                                <label class="form-label fw-semibold text-dark small mb-1 d-block">
                                    <?= esc((string) $q['teks_pertanyaan']) ?>
                                    <?php if ($wajib): ?><span class="text-danger">*</span><?php endif; ?>
                                </label>
                                <?php if (!empty($q['keterangan_bantuan'])): ?>
                                    <div class="ecc-text-muted-aa mb-2" style="font-size: 0.75rem;"><?= esc((string) $q['keterangan_bantuan']) ?></div>
                                <?php endif; ?>

                                <?php if ($tipe === 'pilihan_tunggal'): ?>
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <?php foreach ($opsi as $oi => $optLabel): ?>
                                            <?php $optId = 'pq_' . $qid . '_r_' . $oi; ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="jawaban[<?= $qid ?>]" id="<?= $optId ?>" value="<?= esc((string) $optLabel) ?>" <?= in_array((string) $optLabel, $curOpsi, true) ? 'checked' : '' ?> <?= $wajib ? 'required' : '' ?>>
                                                <label class="form-check-label small" for="<?= $optId ?>"><?= esc((string) $optLabel) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($tipe === 'pilihan_ganda'): ?>
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <?php foreach ($opsi as $oi => $optLabel): ?>
                                            <?php $optId = 'pq_' . $qid . '_c_' . $oi; ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="jawaban[<?= $qid ?>][]" id="<?= $optId ?>" value="<?= esc((string) $optLabel) ?>" <?= in_array((string) $optLabel, $curOpsi, true) ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="<?= $optId ?>"><?= esc((string) $optLabel) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($tipe === 'skala_likert'): ?>
                                    <?php
                                    $minLbl = (string) ($opsi['min_label'] ?? 'Sangat Kurang');
                                    $maxLbl = (string) ($opsi['max_label'] ?? 'Sangat Baik');
                                    $curNum = $oldAns !== null ? (int) $oldAns : (int) round((float) ($prev['nilai_angka'] ?? 0));
                                    ?>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                        <span class="ecc-text-muted-aa small"><?= esc($minLbl) ?></span>
                                        <div class="d-flex gap-2">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <?php $lid = 'pq_' . $qid . '_lk_' . $s; ?>
                                                <input type="radio" class="btn-check" name="jawaban[<?= $qid ?>]" id="<?= $lid ?>" value="<?= $s ?>" <?= $curNum === $s ? 'checked' : '' ?> <?= $wajib ? 'required' : '' ?>>
                                                <label class="btn btn-outline-primary ts-likert-option ecc-num" for="<?= $lid ?>"><?= $s ?></label>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="ecc-text-muted-aa small"><?= esc($maxLbl) ?></span>
                                    </div>
                                <?php elseif ($tipe === 'teks_panjang'): ?>
                                    <textarea name="jawaban[<?= $qid ?>]" class="form-control mt-2" rows="3" <?= $wajib ? 'required' : '' ?> placeholder="Tuliskan jawaban Anda..."><?= esc($curText) ?></textarea>
                                <?php elseif ($tipe === 'angka'): ?>
                                    <input type="number" step="any" name="jawaban[<?= $qid ?>]" class="form-control ecc-control-sm ecc-num mt-2" style="max-width: 240px;" value="<?= esc($curText) ?>" <?= $wajib ? 'required' : '' ?>>
                                <?php elseif ($tipe === 'tanggal'): ?>
                                    <input type="text" name="jawaban[<?= $qid ?>]" class="form-control ecc-control-sm bg-white js-pub-q-date mt-2" style="max-width: 240px;" value="<?= esc($curText) ?>" placeholder="YYYY-MM-DD" <?= $wajib ? 'required' : '' ?>>
                                <?php else: ?>
                                    <input type="text" name="jawaban[<?= $qid ?>]" class="form-control ecc-control-sm mt-2" value="<?= esc($curText) ?>" <?= $wajib ? 'required' : '' ?> placeholder="Tuliskan jawaban singkat...">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-end pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    <?= $existing ? 'Simpan Perubahan Jawaban' : 'Kirim Jawaban Survei' ?>
                </button>
            </div>
        </form>
    </div>

    <div class="text-center text-muted small mt-3">
        &copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal
    </div>
</div>

<script src="<?= base_url('assets/vendor/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/l10n/id.js') ?>"></script>
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
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-pub-q-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
    }
});
</script>
</body>
</html>
