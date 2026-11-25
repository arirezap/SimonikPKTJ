<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Formulir Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= view('partials/_wilayah_styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<style>
    .ts-letter-box {
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
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$ctx           = $ctx ?? [];
$periode       = $ctx['periode'] ?? [];
$isMitra       = !empty($ctx['is_mitra']);
$hideLegacy    = !empty($ctx['hide_legacy_work_block']);
$groupedQ      = $ctx['grouped_questions'] ?? [];
$sectionIntros = $ctx['section_intros'] ?? [];
$prefills      = $ctx['prefills'] ?? [];
$alumni        = $ctx['alumni'] ?? null;
$alumniOpts    = $ctx['alumni_options'] ?? [];
$existing      = $ctx['existing'] ?? null;
$answers       = $ctx['answers'] ?? [];
$refs          = $refs ?? [];
$isSelfAlumni  = !empty($isSelfAlumni);
$selectedNim   = (string) ($selectedNim ?? '');
$pid           = (int) ($periode['id'] ?? 0);

$valTransisi  = (string) old('status_transisi', (string) ($existing['status_transisi'] ?? 'bekerja'));
$valTunggu    = (string) old('masa_tunggu_bulan', (string) ($existing['masa_tunggu_bulan'] ?? ''));
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
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h2 class="h5 fw-bold text-dark mb-0"><?= esc((string) $periode['nama_periode']) ?></h2>
                    <?= ketarunaan_tracer_target_badge((string) ($periode['target_responden'] ?? 'alumni')) ?>
                </div>
                <?php if (!empty($periode['deskripsi'])): ?>
                    <details class="mt-2">
                        <summary class="small fw-semibold text-primary" style="cursor: pointer;">Surat Pengantar Resmi Tim Tracer Study PKTJ</summary>
                        <div class="ts-letter-box mt-2"><?= esc((string) $periode['deskripsi']) ?></div>
                    </details>
                <?php else: ?>
                    <p class="ecc-text-muted-aa small mb-0">Lengkapi pertanyaan kuesioner berikut dengan benar.</p>
                <?php endif; ?>
            </div>
            <?= ketarunaan_tracer_status_periode_badge((string) ($periode['status_periode'] ?? 'dibuka')) ?>
        </div>

        <form method="post" action="<?= base_url('ketarunaan/tracer-study/isi/' . $pid . '/simpan') ?>" class="js-ts-dynamic-form" data-sec-start="<?= $hideLegacy ? '1' : '2' ?>">
            <?= csrf_field() ?>

            <?php if (!$isMitra): ?>
                <!-- Identitas Alumni Responden (wajib untuk target alumni) -->
                <div class="mb-4">
                    <?php if (!$hideLegacy): ?>
                        <h3 class="h6 fw-bold text-primary mb-3">1. Profil Transisi Lulusan &amp; Pekerjaan</h3>
                    <?php endif; ?>
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

                        <?php if (!$isSelfAlumni): ?>
                            <div class="col-12 col-md-6">
                                <label for="tsSumber" class="form-label fw-semibold small">Sumber Konfirmasi PIC</label>
                                <select name="sumber_pengisian" id="tsSumber" class="form-select ecc-control-sm">
                                    <option value="telepon_pic" <?= $valSumber === 'telepon_pic' ? 'selected' : '' ?>>Telepon Resmi</option>
                                    <option value="whatsapp" <?= $valSumber === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                                    <option value="email" <?= $valSumber === 'email' ? 'selected' : '' ?>>Alamat Email</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (!$hideLegacy): ?>
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
                                <label for="tsKota" class="form-label fw-semibold small">Kota / Kabupaten Lokasi Kerja</label>
                                <select name="kota_kerja" id="tsKota" class="form-select ecc-control-sm js-ecc-wilayah-kab-only" data-allow-tags="1" data-current-value="<?= esc($valKota) ?>" data-placeholder="-- Pilih Kabupaten / Kota --">
                                    <option value="">-- Pilih Kabupaten / Kota --</option>
                                    <?php if ($valKota !== ''): ?>
                                        <option value="<?= esc($valKota) ?>" selected><?= esc($valKota) ?></option>
                                    <?php endif; ?>
                                </select>
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
                        <?php endif; ?>
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
                'idPrefix'      => 'q_',
            ]) ?>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url($isSelfAlumni ? 'ketarunaan/tracer-study' : ('ketarunaan/tracer-study/responden?periode_id=' . $pid)) ?>" class="btn btn-light border rounded-pill px-4 ts-touch-ctrl d-inline-flex align-items-center">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile ts-touch-ctrl shadow-sm">
                    <?= $existing ? 'Simpan Perubahan Jawaban' : ($isMitra ? 'Kirim Penilaian Lulusan' : 'Kirim Jawaban Survei') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= view('partials/_wilayah_scripts') ?>
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
    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.js-ts-select2').select2({ theme: 'bootstrap-5', width: '100%' });
    }
});
</script>
<?= $this->endSection() ?>
