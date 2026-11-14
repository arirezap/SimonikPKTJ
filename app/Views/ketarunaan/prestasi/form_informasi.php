<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= ($mode === 'edit') ? 'Ubah Informasi Prestasi' : 'Tambah Prestasi' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pr-card {
            padding: 16px;
        }
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
    .ecc-wizard-step.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-wizard-step.disabled-step {
        color: #94a3b8;
        cursor: not-allowed;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$mode     = $mode ?? 'create';
$isEdit   = ($mode === 'edit');
$prestasi = $prestasi ?? [];
$refs     = $refs ?? [];
$idPrestasi = (int) ($prestasi['id'] ?? 0);

$formAction = $isEdit && $idPrestasi > 0
    ? base_url('ketarunaan/prestasi/update/' . $idPrestasi)
    : base_url('ketarunaan/prestasi/simpan');

$valNama          = (string) old('nama_prestasi', $prestasi['nama_prestasi'] ?? '');
$valJenis         = (string) old('jenis_prestasi', $prestasi['jenis_prestasi'] ?? 'akademik');
$valBidang        = (string) old('bidang_prestasi', $prestasi['bidang_prestasi'] ?? 'Inovasi Keselamatan Jalan');
$valTingkat       = (string) old('tingkat_prestasi', $prestasi['tingkat_prestasi'] ?? 'nasional');
$valPeringkat     = (string) old('peringkat', $prestasi['peringkat'] ?? 'Juara I');
$valPenyelenggara = (string) old('penyelenggara', $prestasi['penyelenggara'] ?? '');
$valLokasi        = (string) old('lokasi_kegiatan', $prestasi['lokasi_kegiatan'] ?? '');
$valTglMulai      = (string) old('tanggal_mulai', $prestasi['tanggal_mulai'] ?? date('Y-m-d'));
$valTglSelesai    = (string) old('tanggal_selesai', $prestasi['tanggal_selesai'] ?? date('Y-m-d'));
$valKeikutsertaan = (string) old('jenis_keikutsertaan', $prestasi['jenis_keikutsertaan'] ?? 'tim');
$valNamaTim       = (string) old('nama_tim', $prestasi['nama_tim'] ?? '');
$valPembimbing    = (string) old('pembimbing', $prestasi['pembimbing'] ?? 'Pembina Ketarunaan');
$valUnit          = (string) old('unit_pemilik', $prestasi['unit_pemilik'] ?? 'Ketarunaan');
$valSumber        = (string) old('sumber_data', $prestasi['sumber_data'] ?? 'Piagam Penghargaan');
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isEdit ? 'Ubah Informasi' : 'Tambah Prestasi' ?></li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1"><?= $isEdit ? 'Ubah Informasi Prestasi' : 'Tambah Prestasi' ?></h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Lengkapi informasi prestasi yang diperoleh taruna.</p>
        </div>
        <div>
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i> Kembali ke Daftar Prestasi
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= esc($formAction) ?>" class="ecc-pr-card">
        <?= csrf_field() ?>

        <!-- Navigasi 3 Tahap -->
        <div class="ecc-wizard-nav">
            <span class="ecc-wizard-step active">1. Informasi Prestasi</span>
            <?php if ($isEdit && $idPrestasi > 0): ?>
                <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="ecc-wizard-step">2. Peserta</a>
                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="ecc-wizard-step">3. Dokumen Bukti</a>
            <?php else: ?>
                <span class="ecc-wizard-step disabled-step">2. Peserta</span>
                <span class="ecc-wizard-step disabled-step">3. Dokumen Bukti</span>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <!-- Kolom Kiri -->
            <div class="col-12 col-lg-6">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="nama_prestasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Nama Prestasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_prestasi" name="nama_prestasi" value="<?= esc($valNama) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Lomba Inovasi Transportasi" required>
                    </div>

                    <div>
                        <label for="jenis_prestasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Jenis Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="jenis_prestasi" name="jenis_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['jenis_catalog'] ?? []) as $jKey => $jLabel): ?>
                                <option value="<?= esc($jKey) ?>" <?= ($valJenis === $jKey) ? 'selected' : '' ?>><?= esc($jLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="bidang_prestasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Bidang Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="bidang_prestasi" name="bidang_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['bidang_options'] ?? []) as $bid): ?>
                                <option value="<?= esc($bid) ?>" <?= ($valBidang === $bid) ? 'selected' : '' ?>><?= esc($bid) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="tingkat_prestasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Tingkat Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="tingkat_prestasi" name="tingkat_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['tingkat_catalog'] ?? []) as $tKey => $tLabel): ?>
                                <option value="<?= esc($tKey) ?>" <?= ($valTingkat === $tKey) ? 'selected' : '' ?>><?= esc($tLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="peringkat" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Peringkat <span class="text-danger">*</span>
                        </label>
                        <select id="peringkat" name="peringkat" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['peringkat_options'] ?? []) as $prk): ?>
                                <option value="<?= esc($prk) ?>" <?= ($valPeringkat === $prk) ? 'selected' : '' ?>><?= esc($prk) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="penyelenggara" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Penyelenggara <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="penyelenggara" name="penyelenggara" value="<?= esc($valPenyelenggara) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Forum Keselamatan Transportasi" required>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-12 col-lg-6">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="lokasi_kegiatan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Lokasi Kegiatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="lokasi_kegiatan" name="lokasi_kegiatan" value="<?= esc($valLokasi) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Jakarta" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label for="tanggal_mulai" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($valTglMulai) ?>" class="form-control ecc-control-sm ecc-num" required>
                        </div>
                        <div class="col-6">
                            <label for="tanggal_selesai" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($valTglSelesai) ?>" class="form-control ecc-control-sm ecc-num" required>
                        </div>
                    </div>

                    <div>
                        <span class="form-label text-secondary fw-semibold d-block mb-1" style="font-size: 0.8125rem;">
                            Jenis Keikutsertaan <span class="text-danger">*</span>
                        </span>
                        <div class="d-flex align-items-center gap-4 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_keikutsertaan" id="ikutTim" value="tim" <?= ($valKeikutsertaan === 'tim') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium" for="ikutTim" style="font-size: 0.875rem;">Tim</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_keikutsertaan" id="ikutIndividu" value="individu" <?= ($valKeikutsertaan === 'individu') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium" for="ikutIndividu" style="font-size: 0.875rem;">Individu</label>
                            </div>
                        </div>
                    </div>

                    <div id="namaTimGroup">
                        <label for="nama_tim" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Nama Tim <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_tim" name="nama_tim" value="<?= esc($valNamaTim) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Tim Inovasi RSTJ">
                    </div>

                    <div>
                        <label for="pembimbing" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                            Pembimbing <span class="text-danger">*</span>
                        </label>
                        <select id="pembimbing" name="pembimbing" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['pembimbing_options'] ?? []) as $pmb): ?>
                                <option value="<?= esc($pmb) ?>" <?= ($valPembimbing === $pmb) ? 'selected' : '' ?>><?= esc($pmb) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label for="unit_pemilik" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                                Unit Pemilik <span class="text-danger">*</span>
                            </label>
                            <select id="unit_pemilik" name="unit_pemilik" class="form-select ecc-control-sm" required>
                                <?php foreach (($refs['unit_options'] ?? []) as $unt): ?>
                                    <option value="<?= esc($unt) ?>" <?= ($valUnit === $unt) ? 'selected' : '' ?>><?= esc($unt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="sumber_data" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">
                                Sumber Data <span class="text-danger">*</span>
                            </label>
                            <select id="sumber_data" name="sumber_data" class="form-select ecc-control-sm" required>
                                <?php foreach (($refs['sumber_options'] ?? []) as $smb): ?>
                                    <option value="<?= esc($smb) ?>" <?= ($valSumber === $smb) ? 'selected' : '' ?>><?= esc($smb) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span>Peserta tim ditambahkan pada tahap Peserta.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold">Batal</a>
            <?php if ($isEdit): ?>
                <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                    Simpan Draf
                </button>
            <?php endif; ?>
            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                Berikutnya: Peserta <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioTim      = document.getElementById('ikutTim');
    const radioIndividu = document.getElementById('ikutIndividu');
    const namaTimGroup  = document.getElementById('namaTimGroup');
    const namaTimInput  = document.getElementById('nama_tim');

    function syncTimField() {
        const isTim = radioTim && radioTim.checked;
        if (namaTimGroup) {
            namaTimGroup.style.display = isTim ? 'block' : 'none';
        }
        if (namaTimInput) {
            namaTimInput.required = !!isTim;
        }
    }

    if (radioTim) radioTim.addEventListener('change', syncTimField);
    if (radioIndividu) radioIndividu.addEventListener('change', syncTimField);
    syncTimField();
});
</script>
<?= $this->endSection() ?>
