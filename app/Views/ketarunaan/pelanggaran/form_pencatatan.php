<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= ($mode ?? 'create') === 'edit' ? 'Edit Pencatatan Kasus' : 'Catat Dugaan Pelanggaran' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$mode        = $mode ?? 'create';
$kasus       = $kasus ?? [];
$refs        = $refs ?? [];
$tarunaList  = $tarunaList ?? [];
$canViewFull = !empty($canViewFull);
$idKasus     = (int) ($kasus['id'] ?? 0);

$actionUrl   = ($mode === 'edit' && $idKasus > 0)
    ? base_url('ketarunaan/pelanggaran/update/' . $idKasus)
    : base_url('ketarunaan/pelanggaran/simpan');

$rawDateTime = (string) ($kasus['tanggal_kejadian'] ?? date('Y-m-d 08:00:00'));
$tglDefault  = substr($rawDateTime, 0, 10);
$jamDefault  = strlen($rawDateTime) >= 16 ? substr($rawDateTime, 11, 5) : '08:00';
$selectedNo  = old('nomor_taruna', (string) ($kasus['nomor_taruna'] ?? ''));

$stPenetapan    = (string) ($kasus['status_penetapan'] ?? 'dugaan');
$stPenyelesaian = (string) ($kasus['status_penyelesaian'] ?? 'proses');
$rawValidasi    = (string) ($kasus['status_validasi'] ?? 'draf');
$stValidasi     = ($rawValidasi === 'perlu_perbaikan') ? 'dikembalikan' : $rawValidasi;
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 1,
        'pageTitle'    => ($mode === 'edit') ? 'Edit Pencatatan Kasus' : 'Catat Dugaan Pelanggaran',
        'pageSubtitle' => 'Mencatat dugaan awal pelanggaran sebelum proses pemeriksaan dan penetapan.',
        'kasus'        => $kasus,
    ]) ?>

    <div class="bento-card ecc-pl-card pl-form-scope">
        <div class="row g-4">
            <!-- Form Informasi Dugaan -->
            <div class="col-12 col-lg-8">
                <h2 class="h6 fw-bold text-dark mb-1">Informasi Dugaan Pelanggaran</h2>
                <p class="text-secondary mb-3 small">Lengkapi informasi awal dugaan pelanggaran untuk diperiksa lebih lanjut.</p>

                <form id="formPencatatanKasus" method="post" action="<?= $actionUrl ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="inputNomorPerkara" class="form-label fw-semibold text-dark small">
                                Nomor Kasus <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputNomorPerkara"
                                name="nomor_perkara"
                                class="form-control ecc-control-sm ecc-num"
                                value="<?= esc(old('nomor_perkara', (string) ($kasus['nomor_perkara'] ?? ''))) ?>"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputJenisPelanggaran" class="form-label fw-semibold text-dark small">
                                Kategori Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <select id="inputJenisPelanggaran" name="jenis_pelanggaran" class="form-select ecc-control-sm" required>
                                <?php $katVal = old('jenis_pelanggaran', (string) ($kasus['jenis_pelanggaran'] ?? 'Kedisiplinan')); ?>
                                <?php foreach ($refs['kategori_catalog'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $katVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="selectTarunaKasus" class="form-label fw-semibold text-dark small">
                                Taruna Terlapor <span class="text-danger">*</span>
                            </label>
                            <select id="selectTarunaKasus" name="nomor_taruna" class="form-select ecc-control-sm ecc-num" required>
                                <option value="">-- Pilih Nomor Taruna &amp; Nama --</option>
                                <?php foreach ($tarunaList as $idx => $tr): ?>
                                    <?php
                                    $noTr     = (string) $tr['nomor_taruna'];
                                    $dispNo   = ketarunaan_mask_nomor_taruna($noTr, $canViewFull);
                                    $dispNama = $canViewFull ? (string) $tr['nama_lengkap'] : ('Taruna #' . ($idx + 1));
                                    $angTr    = !empty($tr['angkatan'])
                                        ? ketarunaan_format_angkatan([
                                            'tahun_masuk' => $tr['angkatan'],
                                            'kode_romawi' => $tr['angkatan_romawi'] ?? '',
                                        ])
                                        : '-';
                                    ?>
                                    <option value="<?= esc($noTr) ?>" <?= $selectedNo === $noTr ? 'selected' : '' ?>>
                                        <?= esc($dispNo . ' - ' . $dispNama . ' (' . ($tr['kode_prodi'] ?? '-') . ' / ' . $angTr . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputTanggalKejadian" class="form-label fw-semibold text-dark small">
                                Tanggal Kejadian <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputTanggalKejadian"
                                name="tanggal_kejadian"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num bg-white"
                                value="<?= esc(old('tanggal_kejadian', $tglDefault)) ?>"
                                placeholder="Pilih tanggal kejadian"
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputWaktuKejadian" class="form-label fw-semibold text-dark small">
                                Waktu Kejadian <span class="text-danger">*</span>
                            </label>
                            <input
                                type="time"
                                id="inputWaktuKejadian"
                                name="waktu_kejadian"
                                class="form-control ecc-control-sm ecc-num"
                                value="<?= esc(old('waktu_kejadian', $jamDefault)) ?>"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputLokasi" class="form-label fw-semibold text-dark small">
                                Lokasi Kejadian <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputLokasi"
                                name="lokasi"
                                class="form-control ecc-control-sm"
                                value="<?= esc(old('lokasi', (string) ($kasus['lokasi'] ?? ''))) ?>"
                                placeholder="Misal: Area Kampus / Asrama"
                                maxlength="150"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputPelapor" class="form-label fw-semibold text-dark small">
                                Pelapor <span class="text-danger">*</span>
                            </label>
                            <select id="inputPelapor" name="pelapor" class="form-select ecc-control-sm" required>
                                <?php $pelVal = old('pelapor', (string) ($kasus['pelapor'] ?? 'Unit Ketarunaan')); ?>
                                <?php foreach ($refs['pelapor_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $pelVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputUnitPemilik" class="form-label fw-semibold text-dark small">
                                Unit Pemilik <span class="text-danger">*</span>
                            </label>
                            <select id="inputUnitPemilik" name="unit_pemilik" class="form-select ecc-control-sm" required>
                                <?php $unitVal = old('unit_pemilik', (string) ($kasus['unit_pemilik'] ?? 'Ketarunaan')); ?>
                                <?php foreach ($refs['unit_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $unitVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputSumberData" class="form-label fw-semibold text-dark small">
                                Sumber Data <span class="text-danger">*</span>
                            </label>
                            <select id="inputSumberData" name="sumber_data" class="form-select ecc-control-sm" required>
                                <?php $sumberVal = old('sumber_data', (string) ($kasus['sumber_data'] ?? 'Laporan petugas')); ?>
                                <?php foreach ($refs['sumber_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $sumberVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputKerahasiaan" class="form-label fw-semibold text-dark small">
                                Tingkat Kerahasiaan <span class="text-danger">*</span>
                            </label>
                            <select id="inputKerahasiaan" name="tingkat_kerahasiaan" class="form-select ecc-control-sm" required>
                                <?php $rahasiaVal = old('tingkat_kerahasiaan', (string) ($kasus['tingkat_kerahasiaan'] ?? 'terbatas')); ?>
                                <option value="terbatas" <?= $rahasiaVal === 'terbatas' ? 'selected' : '' ?>>Terbatas</option>
                                <option value="sangat_terbatas" <?= $rahasiaVal === 'sangat_terbatas' ? 'selected' : '' ?>>Sangat Terbatas</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="inputUraian" class="form-label fw-semibold text-dark small">
                                Uraian Laporan Awal <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="inputUraian"
                                name="uraian"
                                class="form-control rounded-3"
                                rows="4"
                                maxlength="1000"
                                placeholder="Tuliskan ringkasan peristiwa atau laporan dugaan yang perlu diperiksa..."
                                required
                            ><?= esc(old('uraian', (string) ($kasus['uraian'] ?? ''))) ?></textarea>
                            <div class="form-text text-end ecc-num" style="font-size: 0.75rem;">
                                <span id="counterUraian">0</span> / 1000
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
                        <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl d-inline-flex align-items-center justify-content-center">Batal</a>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl js-submit-pl1">Simpan Draf</button>
                            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl js-submit-pl1">
                                Berikutnya: Pemeriksaan <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Panel Kanan: Status Kasus -->
            <div class="col-12 col-lg-4">
                <div class="ecc-panel-box h-100 bg-light">
                    <h3 class="h6 fw-bold text-dark mb-1"><?= $mode === 'edit' ? 'Status Kasus Saat Ini' : 'Status Kasus (Awal)' ?></h3>
                    <p class="text-secondary mb-3 small">Status berikut diperbarui bertahap sesuai hasil pemeriksaan dan penetapan.</p>

                    <div class="d-flex flex-column gap-3 bg-white border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Penetapan</span>
                            <?= ketarunaan_penetapan_badge($stPenetapan) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Penyelesaian</span>
                            <?= ketarunaan_penyelesaian_badge($stPenyelesaian) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Validasi</span>
                            <?= ketarunaan_validasi_badge($stValidasi) ?>
                        </div>
                    </div>

                    <div class="mt-3 px-3 py-2 rounded-3 small" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a;">
                        Laporan awal berstatus Dugaan tidak langsung dihitung sebagai pelanggaran terbukti.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
        var selectEl = window.jQuery('#selectTarunaKasus');
        if (selectEl.length) {
            selectEl.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Nomor Taruna & Nama --'
            });
        }
    }

    var tglEl = document.getElementById('inputTanggalKejadian');
    var fpTgl = null;
    if (typeof flatpickr !== 'undefined' && tglEl) {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        fpTgl = flatpickr(tglEl, {
            locale: fpLocale,
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            onReady: function (d, s, inst) {
                if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Kejadian');
            }
        });
    }

    var uraianEl  = document.getElementById('inputUraian');
    var counterEl = document.getElementById('counterUraian');
    if (uraianEl && counterEl) {
        var updateLen = function () {
            counterEl.textContent = String(uraianEl.value.length);
        };
        updateLen();
        uraianEl.addEventListener('input', updateLen);
    }

    function showWarn(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(text);
        }
    }

    var form = document.getElementById('formPencatatanKasus');
    var lastAction = 'next';
    document.querySelectorAll('.js-submit-pl1').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'next';
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            if (tglEl && tglEl.value.trim() === '') {
                e.preventDefault();
                if (fpTgl && fpTgl.altInput) fpTgl.altInput.focus();
                showWarn('Tanggal Wajib Diisi', 'Pilih tanggal kejadian terlebih dahulu.');
                return;
            }
            if (uraianEl && uraianEl.value.trim() === '') {
                e.preventDefault();
                uraianEl.focus();
                showWarn('Uraian Wajib Diisi', 'Tuliskan ringkasan kronologi dugaan pelanggaran.');
                return;
            }

            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            document.querySelectorAll('.js-submit-pl1').forEach(function (btn) {
                btn.disabled = true;
            });
            var hiddenAksi = form.querySelector('input[name="aksi_submit"][data-generated="1"]');
            if (!hiddenAksi) {
                hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = 'aksi_submit';
                hiddenAksi.setAttribute('data-generated', '1');
                form.appendChild(hiddenAksi);
            }
            hiddenAksi.value = aksi;
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.js-submit-pl1').forEach(function (btn) {
                btn.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
