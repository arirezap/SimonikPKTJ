<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Formulir Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$ctx          = $ctx ?? [];
$periode      = $ctx['periode'] ?? [];
$groupedQ     = $ctx['grouped_questions'] ?? [];
$alumni       = $ctx['alumni'] ?? null;
$alumniOpts   = $ctx['alumni_options'] ?? [];
$existing     = $ctx['existing'] ?? null;
$answers      = $ctx['answers'] ?? [];
$refs         = $refs ?? [];
$isSelfAlumni = !empty($isSelfAlumni);
$selectedNim  = (string) ($selectedNim ?? '');
$pid          = (int) ($periode['id'] ?? 0);

$valTransisi  = (string) old('status_transisi', (string) ($existing['status_transisi'] ?? 'bekerja'));
$valTunggu    = (string) old('masa_tunggu_bulan', (string) ($existing['masa_tunggu_bulan'] ?? ''));
$valTglKerja  = (string) old('tanggal_mulai_kerja', (string) ($existing['tanggal_mulai_kerja'] ?? ''));
$valInstansi  = (string) old('nama_instansi', (string) ($existing['nama_instansi'] ?? ($alumni['instansi_saat_ini'] ?? '')));
$valSektor    = (string) old('sektor_industri', (string) ($existing['sektor_industri'] ?? ''));
$valJabatan   = (string) old('jabatan_posisi', (string) ($existing['jabatan_posisi'] ?? ($alumni['pekerjaan_saat_ini'] ?? '')));
$valKota      = (string) old('kota_kerja', (string) ($existing['kota_kerja'] ?? ($alumni['kota_domisili'] ?? '')));
$valBidang    = (string) old('kesesuaian_bidang', (string) ($existing['kesesuaian_bidang'] ?? ''));
$valJenjang   = (string) old('kesesuaian_jenjang', (string) ($existing['kesesuaian_jenjang'] ?? ''));
$valSumber    = (string) old('sumber_pengisian', (string) ($existing['sumber_pengisian'] ?? ($isSelfAlumni ? 'mandiri_alumni' : 'telepon_pic')));
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="mb-3">
        <a href="<?= base_url($isSelfAlumni ? 'ketarunaan/tracer-study' : ('ketarunaan/tracer-study/responden?periode_id=' . $pid)) ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
        </a>
    </div>

    <div class="ecc-ts-card">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3 pb-3 border-bottom">
            <div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) $periode['nama_periode']) ?></h2>
                <p class="ecc-text-muted-aa small mb-0">
                    <?= esc((string) ($periode['deskripsi'] ?: 'Lengkapi data transisi lulusan serta pertanyaan kuesioner berikut dengan benar.')) ?>
                </p>
            </div>
            <?= ketarunaan_tracer_status_periode_badge((string) ($periode['status_periode'] ?? 'dibuka')) ?>
        </div>

        <form method="post" action="<?= base_url('ketarunaan/tracer-study/isi/' . $pid . '/simpan') ?>">
            <?= csrf_field() ?>

            <!-- Bagian 1: Identitas Alumni & Data Transisi Kerja -->
            <div class="mb-4">
                <h3 class="h6 fw-bold text-primary mb-3">1. Profil Transisi Lulusan &amp; Pekerjaan</h3>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="tsNomorTaruna" class="form-label fw-semibold small">Alumni Responden <span class="text-danger">*</span></label>
                        <?php if ($isSelfAlumni): ?>
                            <input type="hidden" name="nomor_taruna" value="<?= esc($selectedNim) ?>">
                            <input type="text" id="tsNomorTaruna" class="form-control ecc-control-sm bg-light" readonly
                                   value="<?= esc(($alumni['nama_lengkap'] ?? $selectedNim) . ' (' . $selectedNim . ')') ?>">
                        <?php else: ?>
                            <select name="nomor_taruna" id="tsNomorTaruna" class="form-select ecc-control-sm js-ts-select2" required>
                                <option value="">-- Pilih Alumni Sasaran --</option>
                                <?php foreach ($alumniOpts as $ao): ?>
                                    <option value="<?= esc((string) $ao['nomor_taruna']) ?>" <?= $selectedNim === (string) $ao['nomor_taruna'] ? 'selected' : '' ?>>
                                        <?= esc($ao['nomor_taruna'] . ' - ' . ($ao['nama_lengkap'] ?: $ao['nomor_taruna']) . ' (' . ($ao['kode_prodi'] ?: '-') . ' / ' . ($ao['tahun_lulus'] ?: '-') . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="tsStatusTransisi" class="form-label fw-semibold small">Status Kegiatan Utama Saat Ini <span class="text-danger">*</span></label>
                        <select name="status_transisi" id="tsStatusTransisi" class="form-select ecc-control-sm" required onchange="toggleWorkFields()">
                            <?php foreach (($refs['status_transisi'] ?? []) as $sk => $sm): ?>
                                <option value="<?= esc($sk) ?>" <?= $valTransisi === $sk ? 'selected' : '' ?>><?= esc($sm['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 js-work-field">
                        <label for="tsInstansi" class="form-label fw-semibold small">Nama Instansi / Perusahaan / Usaha <span class="text-danger">*</span></label>
                        <input type="text" name="nama_instansi" id="tsInstansi" class="form-control ecc-control-sm" value="<?= esc($valInstansi) ?>" placeholder="Contoh: Kementerian Perhubungan / PT ...">
                    </div>

                    <div class="col-12 col-md-6 js-work-field">
                        <label for="tsJabatan" class="form-label fw-semibold small">Jabatan / Posisi</label>
                        <input type="text" name="jabatan_posisi" id="tsJabatan" class="form-control ecc-control-sm" value="<?= esc($valJabatan) ?>" placeholder="Contoh: Analis Transportasi / Teknisi">
                    </div>

                    <div class="col-12 col-md-4 js-work-field">
                        <label for="tsSektor" class="form-label fw-semibold small">Sektor Industri</label>
                        <select name="sektor_industri" id="tsSektor" class="form-select ecc-control-sm">
                            <option value="">-- Pilih Sektor --</option>
                            <?php foreach (($refs['sektor_industri'] ?? []) as $sek): ?>
                                <option value="<?= esc($sek) ?>" <?= $valSektor === $sek ? 'selected' : '' ?>><?= esc($sek) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-6 col-md-4 js-work-field">
                        <label for="tsKota" class="form-label fw-semibold small">Kota Lokasi Kerja</label>
                        <input type="text" name="kota_kerja" id="tsKota" class="form-control ecc-control-sm" value="<?= esc($valKota) ?>" placeholder="Contoh: Jakarta / Semarang">
                    </div>

                    <div class="col-6 col-md-4 js-work-field">
                        <label for="tsMasaTunggu" class="form-label fw-semibold small">Masa Tunggu Kerja (Bulan)</label>
                        <input type="number" name="masa_tunggu_bulan" id="tsMasaTunggu" class="form-control ecc-control-sm ecc-num" min="0" max="60" value="<?= esc($valTunggu) ?>" placeholder="0 jika sebelum lulus">
                    </div>

                    <div class="col-6 col-md-4 js-work-field">
                        <label for="tsBidang" class="form-label fw-semibold small">Kesesuaian Bidang dengan Prodi</label>
                        <select name="kesesuaian_bidang" id="tsBidang" class="form-select ecc-control-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach (($refs['kesesuaian_bidang'] ?? []) as $bk => $bl): ?>
                                <option value="<?= esc($bk) ?>" <?= $valBidang === $bk ? 'selected' : '' ?>><?= esc($bl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-6 col-md-4 js-work-field">
                        <label for="tsJenjang" class="form-label fw-semibold small">Kesesuaian Jenjang Pendidikan</label>
                        <select name="kesesuaian_jenjang" id="tsJenjang" class="form-select ecc-control-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach (($refs['kesesuaian_jenjang'] ?? []) as $jk => $jl): ?>
                                <option value="<?= esc($jk) ?>" <?= $valJenjang === $jk ? 'selected' : '' ?>><?= esc($jl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!$isSelfAlumni): ?>
                        <div class="col-12 col-md-4">
                            <label for="tsSumber" class="form-label fw-semibold small">Sumber Konfirmasi PIC</label>
                            <select name="sumber_pengisian" id="tsSumber" class="form-select ecc-control-sm">
                                <option value="telepon_pic" <?= $valSumber === 'telepon_pic' ? 'selected' : '' ?>>Telepon Resmi</option>
                                <option value="whatsapp" <?= $valSumber === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                                <option value="email" <?= $valSumber === 'email' ? 'selected' : '' ?>>Alamat Email</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bagian 2..N: Pertanyaan Dinamis Periode -->
            <?php $secNo = 2; ?>
            <?php foreach ($groupedQ as $secTitle => $qList): ?>
                <div class="mb-4 pt-3 border-top">
                    <h3 class="h6 fw-bold text-primary mb-3"><?= $secNo++ . '. ' . esc((string) $secTitle) ?></h3>
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
                                    <?php if ($wajib): ?>
                                        <span class="text-danger">*</span>
                                    <?php else: ?>
                                        <span class="fw-normal ecc-text-muted-aa">(Opsional)</span>
                                    <?php endif; ?>
                                </label>
                                <?php if (!empty($q['keterangan_bantuan'])): ?>
                                    <div class="ecc-text-muted-aa mb-2" style="font-size: 0.75rem;"><?= esc((string) $q['keterangan_bantuan']) ?></div>
                                <?php endif; ?>

                                <?php if ($tipe === 'pilihan_tunggal'): ?>
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <?php foreach ($opsi as $oi => $optLabel): ?>
                                            <?php $optId = 'q_' . $qid . '_r_' . $oi; ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                       name="jawaban[<?= $qid ?>]"
                                                       id="<?= $optId ?>"
                                                       value="<?= esc((string) $optLabel) ?>"
                                                       <?= in_array((string) $optLabel, $curOpsi, true) ? 'checked' : '' ?>
                                                       <?= $wajib ? 'required' : '' ?>>
                                                <label class="form-check-label small" for="<?= $optId ?>"><?= esc((string) $optLabel) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($tipe === 'pilihan_ganda'): ?>
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <?php foreach ($opsi as $oi => $optLabel): ?>
                                            <?php $optId = 'q_' . $qid . '_c_' . $oi; ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       name="jawaban[<?= $qid ?>][]"
                                                       id="<?= $optId ?>"
                                                       value="<?= esc((string) $optLabel) ?>"
                                                       <?= in_array((string) $optLabel, $curOpsi, true) ? 'checked' : '' ?>>
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
                                                <?php $lid = 'q_' . $qid . '_lk_' . $s; ?>
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
                                    <input type="text" name="jawaban[<?= $qid ?>]" class="form-control ecc-control-sm bg-white js-ts-date mt-2" style="max-width: 240px;" value="<?= esc($curText) ?>" placeholder="YYYY-MM-DD" <?= $wajib ? 'required' : '' ?>>
                                <?php else: ?>
                                    <input type="text" name="jawaban[<?= $qid ?>]" class="form-control ecc-control-sm mt-2" value="<?= esc($curText) ?>" <?= $wajib ? 'required' : '' ?> placeholder="Tuliskan jawaban singkat...">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url($isSelfAlumni ? 'ketarunaan/tracer-study' : ('ketarunaan/tracer-study/responden?periode_id=' . $pid)) ?>" class="btn btn-light border rounded-pill px-4 ts-touch-ctrl d-inline-flex align-items-center">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile ts-touch-ctrl shadow-sm">
                    <?= $existing ? 'Simpan Perubahan Jawaban' : 'Kirim Jawaban Survei' ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
function toggleWorkFields() {
    var st = document.getElementById('tsStatusTransisi');
    if (!st) return;
    var isWork = (st.value === 'bekerja' || st.value === 'wirausaha');
    var instInput = document.getElementById('tsInstansi');
    if (instInput) instInput.required = isWork;
    document.querySelectorAll('.js-work-field').forEach(function(el) {
        el.style.opacity = isWork ? '1' : '0.65';
    });
}
document.addEventListener('DOMContentLoaded', function() {
    toggleWorkFields();
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-ts-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
    }
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.js-ts-select2').select2({ theme: 'bootstrap-5', width: '100%' });
    }
});
</script>
<?= $this->endSection() ?>
