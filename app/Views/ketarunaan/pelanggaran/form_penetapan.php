<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Penetapan dan Tindak Lanjut<?= $this->endSection() ?>

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
$detail   = $detail ?? [];
$kasus    = $detail['kasus'] ?? [];
$refs     = $refs ?? [];
$idKasus  = (int) ($kasus['id'] ?? 0);

$stPenetapan    = old('status_penetapan', (string) ($kasus['status_penetapan'] ?? 'dugaan'));
$stPenyelesaian = old('status_penyelesaian', (string) ($kasus['status_penyelesaian'] ?? 'proses'));
$rawValidasi    = (string) ($kasus['status_validasi'] ?? 'draf');
$stValidasi     = ($rawValidasi === 'perlu_perbaikan') ? 'dikembalikan' : $rawValidasi;
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 3,
        'pageTitle'    => 'Penetapan dan Tindak Lanjut',
        'pageSubtitle' => 'Menetapkan hasil pemeriksaan, sanksi, dan pemantauan penyelesaian kasus.',
        'kasus'        => $kasus,
    ]) ?>

    <div class="ecc-pl-card">
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <h6 class="fw-bold text-dark mb-1">Penetapan Hasil dan Tindak Lanjut</h6>
                <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Setiap perubahan status penetapan atau banding tersimpan terpisah tanpa menghapus keputusan sebelumnya.</p>

                <form method="post" action="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus . '/simpan') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="selectPenetapan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Status Penetapan <span class="text-danger">*</span>
                            </label>
                            <select id="selectPenetapan" name="status_penetapan" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['penetapan_options'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $stPenetapan === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectTingkat" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Tingkat Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <?php $tktVal = old('tingkat_pelanggaran', (string) ($kasus['tingkat_pelanggaran'] ?: 'belum_ditetapkan')); ?>
                            <select id="selectTingkat" name="tingkat_pelanggaran" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['tingkat_catalog'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $tktVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectDasarAturan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Dasar Peraturan
                            </label>
                            <?php $dasarVal = old('dasar_peraturan', (string) ($kasus['dasar_peraturan'] ?: 'Pedoman Tata Tertib Taruna PKTJ')); ?>
                            <select id="selectDasarAturan" name="dasar_peraturan" class="form-select ecc-control-sm">
                                <?php foreach ($refs['dasar_peraturan_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $dasarVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectJenisSanksi" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Jenis Sanksi
                            </label>
                            <?php $sanksiVal = old('jenis_sanksi', (string) ($kasus['jenis_sanksi'] ?: 'Pembinaan')); ?>
                            <select id="selectJenisSanksi" name="jenis_sanksi" class="form-select ecc-control-sm">
                                <?php foreach ($refs['jenis_sanksi_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $sanksiVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputNomorKeputusan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Nomor Keputusan
                            </label>
                            <input
                                type="text"
                                id="inputNomorKeputusan"
                                name="nomor_keputusan"
                                class="form-control ecc-control-sm ecc-num"
                                value="<?= esc(old('nomor_keputusan', (string) ($kasus['nomor_keputusan'] ?? ''))) ?>"
                                placeholder="Misal: SK/026/2026"
                                maxlength="100"
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputTglKeputusan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Tanggal Keputusan
                            </label>
                            <input
                                type="text"
                                id="inputTglKeputusan"
                                name="tanggal_keputusan"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num"
                                value="<?= esc(old('tanggal_keputusan', (string) ($kasus['tanggal_keputusan'] ?? date('Y-m-d')))) ?>"
                            >
                        </div>

                        <div class="col-12">
                            <label for="inputTindakLanjut" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Rencana Tindak Lanjut / Sanksi <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="inputTindakLanjut"
                                name="tindak_lanjut"
                                class="form-control"
                                rows="3"
                                maxlength="1000"
                                placeholder="Contoh: Pembinaan sesuai ketentuan yang berlaku dan pemantauan perkembangan."
                                required
                            ><?= esc(old('tindak_lanjut', (string) ($kasus['tindak_lanjut'] ?: ($kasus['sanksi'] ?? '')))) ?></textarea>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="selectPenyelesaian" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Status Penyelesaian <span class="text-danger">*</span>
                            </label>
                            <select id="selectPenyelesaian" name="status_penyelesaian" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['penyelesaian_options'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $stPenyelesaian === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputTglSelesai" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Tanggal Selesai
                            </label>
                            <input
                                type="text"
                                id="inputTglSelesai"
                                name="tanggal_selesai"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num"
                                value="<?= esc(old('tanggal_selesai', (string) ($kasus['tanggal_selesai'] ?? ''))) ?>"
                                placeholder="Diisi jika selesai"
                                <?= $stPenyelesaian !== 'selesai' ? 'disabled' : '' ?>
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="selectPic" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                PIC Tindak Lanjut <span class="text-danger">*</span>
                            </label>
                            <?php $picVal = old('pic_tindak_lanjut', (string) ($kasus['pic_tindak_lanjut'] ?: 'Unit Ketarunaan')); ?>
                            <select id="selectPic" name="pic_tindak_lanjut" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['pic_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $picVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="inputCatatanPemantauan" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Catatan Pemantauan / Alasan Perubahan
                            </label>
                            <input
                                type="text"
                                id="inputCatatanPemantauan"
                                name="catatan_pemantauan"
                                class="form-control ecc-control-sm"
                                value="<?= esc(old('catatan_pemantauan', (string) ($kasus['catatan_pemantauan'] ?? ''))) ?>"
                                placeholder="Catatan perkembangan pelaksanaan atau alasan pengajuan banding..."
                            >
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
                        <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Sebelumnya: Pemeriksaan
                        </a>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold">Simpan Draf</button>
                            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                                Berikutnya: Dokumen Bukti <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Panel Kanan: Status Kasus (Saat Ini) -->
            <div class="col-12 col-lg-4">
                <div class="ecc-panel-box h-100">
                    <h6 class="fw-bold text-dark mb-1">Status Kasus (Saat Ini)</h6>
                    <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Pembaruan penetapan, penyelesaian, dan validasi dicatat secara terpisah.</p>

                    <div class="d-flex flex-column gap-3 bg-white border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Penetapan</span>
                            <?= ketarunaan_penetapan_badge((string) ($kasus['status_penetapan'] ?? 'dugaan')) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Penyelesaian</span>
                            <?= ketarunaan_penyelesaian_badge((string) ($kasus['status_penyelesaian'] ?? 'proses')) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.8125rem;">Status Validasi</span>
                            <?= ketarunaan_validasi_badge($stValidasi) ?>
                        </div>
                    </div>

                    <div class="px-3 py-2 rounded-3" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 0.8125rem;">
                        Perubahan status pendidikan diproses terpisah melalui Database Taruna.
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

    const selectPenyelesaian = document.getElementById('selectPenyelesaian');
    const inputTglSelesai    = document.getElementById('inputTglSelesai');

    function syncTanggalSelesai() {
        if (!selectPenyelesaian || !inputTglSelesai) {
            return;
        }
        const isSelesai = selectPenyelesaian.value === 'selesai';
        inputTglSelesai.disabled = !isSelesai;
        inputTglSelesai.required = isSelesai;
        if (!isSelesai) {
            inputTglSelesai.value = '';
        }
    }

    if (selectPenyelesaian) {
        selectPenyelesaian.addEventListener('change', syncTanggalSelesai);
        syncTanggalSelesai();
    }
});
</script>
<?= $this->endSection() ?>
