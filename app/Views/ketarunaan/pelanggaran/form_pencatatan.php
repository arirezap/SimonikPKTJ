<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= ($mode ?? 'create') === 'edit' ? 'Edit Pencatatan Kasus' : 'Catat Dugaan Pelanggaran' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pl-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pl-card {
            padding: 16px;
        }
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-wizard-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .ecc-wizard-step {
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ecc-wizard-step:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-wizard-step.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-panel-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background-color: #f8fafc;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>
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

    <div class="ecc-pl-card">
        <div class="row g-4">
            <!-- Form Informasi Dugaan -->
            <div class="col-12 col-lg-8">
                <h6 class="fw-bold text-dark mb-1">Informasi Dugaan Pelanggaran</h6>
                <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Lengkapi informasi awal dugaan pelanggaran untuk diperiksa lebih lanjut.</p>

                <form id="formPencatatanKasus" method="post" action="<?= $actionUrl ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="inputNomorPerkara" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputJenisPelanggaran" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Kategori Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <select id="inputJenisPelanggaran" name="jenis_pelanggaran" class="form-select ecc-control-sm" required>
                                <?php $katVal = old('jenis_pelanggaran', (string) ($kasus['jenis_pelanggaran'] ?? 'Kedisiplinan')); ?>
                                <?php foreach ($refs['kategori_catalog'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $katVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="filterTarunaKasus" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Cari Taruna
                            </label>
                            <input
                                type="text"
                                id="filterTarunaKasus"
                                class="form-control ecc-control-sm"
                                placeholder="<?= $canViewFull ? 'Ketik Nomor Taruna atau nama...' : 'Ketik Nomor Taruna...' ?>"
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectTarunaKasus" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Taruna Terlapor <span class="text-danger">*</span>
                            </label>
                            <select id="selectTarunaKasus" name="nomor_taruna" class="form-select ecc-control-sm ecc-num" required>
                                <option value="">Pilih Taruna</option>
                                <?php foreach ($tarunaList as $idx => $tr): ?>
                                    <?php
                                    $noTr      = (string) $tr['nomor_taruna'];
                                    $dispNo    = ketarunaan_mask_nomor_taruna($noTr, $canViewFull);
                                    $dispNama  = $canViewFull ? (string) $tr['nama_lengkap'] : ('Taruna #' . ($idx + 1));
                                    $searchKey = strtolower($noTr . ' ' . ($canViewFull ? $tr['nama_lengkap'] : '') . ' ' . ($tr['kode_prodi'] ?? ''));
                                    ?>
                                    <option value="<?= esc($noTr) ?>" data-search="<?= esc($searchKey) ?>" <?= $selectedNo === $noTr ? 'selected' : '' ?>>
                                        <?= esc($dispNo . ' - ' . $dispNama . ' (' . ($tr['kode_prodi'] ?? '-') . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputTanggalKejadian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Tanggal Kejadian <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputTanggalKejadian"
                                name="tanggal_kejadian"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num"
                                value="<?= esc(old('tanggal_kejadian', $tglDefault)) ?>"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputWaktuKejadian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputLokasi" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputPelapor" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputUnitPemilik" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputSumberData" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
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
                            <label for="inputKerahasiaan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Tingkat Kerahasiaan <span class="text-danger">*</span>
                            </label>
                            <select id="inputKerahasiaan" name="tingkat_kerahasiaan" class="form-select ecc-control-sm" required>
                                <?php $rahasiaVal = old('tingkat_kerahasiaan', (string) ($kasus['tingkat_kerahasiaan'] ?? 'terbatas')); ?>
                                <option value="terbatas" <?= $rahasiaVal === 'terbatas' ? 'selected' : '' ?>>Terbatas</option>
                                <option value="sangat_terbatas" <?= $rahasiaVal === 'sangat_terbatas' ? 'selected' : '' ?>>Sangat Terbatas</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="inputUraian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Uraian Laporan Awal <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="inputUraian"
                                name="uraian"
                                class="form-control"
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
                        <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold">Batal</a>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold">Simpan Draf</button>
                            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                                Berikutnya: Pemeriksaan <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Panel Kanan: Status Kasus (Awal / Saat Ini) -->
            <div class="col-12 col-lg-4">
                <div class="ecc-panel-box h-100">
                    <h6 class="fw-bold text-dark mb-1"><?= $mode === 'edit' ? 'Status Kasus Saat Ini' : 'Status Kasus (Awal)' ?></h6>
                    <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Status berikut diperbarui bertahap sesuai hasil pemeriksaan dan penetapan.</p>

                    <div class="d-flex flex-column gap-3 bg-white border rounded-3 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Penetapan</span>
                            <?= ketarunaan_penetapan_badge($stPenetapan) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Penyelesaian</span>
                            <?= ketarunaan_penyelesaian_badge($stPenyelesaian) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Validasi</span>
                            <?= ketarunaan_validasi_badge($stValidasi) ?>
                        </div>
                    </div>

                    <div class="mt-3 px-3 py-2 rounded-3" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 0.8125rem;">
                        Laporan awal berstatus Dugaan tidak langsung dihitung sebagai pelanggaran terbukti.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.ecc-flatpickr', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    const searchInput = document.getElementById('filterTarunaKasus');
    const selectEl    = document.getElementById('selectTarunaKasus');
    if (searchInput && selectEl) {
        const originalOptions = Array.from(selectEl.options).slice(1);
        searchInput.addEventListener('input', function() {
            const kw = this.value.trim().toLowerCase();
            const currentVal = selectEl.value;
            while (selectEl.options.length > 1) {
                selectEl.remove(1);
            }
            originalOptions.forEach(function(opt) {
                const hay = opt.getAttribute('data-search') || opt.text.toLowerCase();
                if (kw === '' || hay.indexOf(kw) !== -1 || opt.value === currentVal) {
                    const clone = opt.cloneNode(true);
                    if (clone.value === currentVal) {
                        clone.selected = true;
                    }
                    selectEl.add(clone);
                }
            });
        });
    }

    const uraianEl  = document.getElementById('inputUraian');
    const counterEl = document.getElementById('counterUraian');
    if (uraianEl && counterEl) {
        const updateLen = function() {
            counterEl.textContent = String(uraianEl.value.length);
        };
        updateLen();
        uraianEl.addEventListener('input', updateLen);
    }
});
</script>
<?= $this->endSection() ?>
