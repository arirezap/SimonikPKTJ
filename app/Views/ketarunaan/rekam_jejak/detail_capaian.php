<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('content') ?>
<?php
$capaian     = $capaian ?? [];
$idCapaian   = (int) ($capaian['id'] ?? 0);
$nomorTaruna = (string) ($capaian['nomor_taruna'] ?? '');
$namaLengkap = (string) ($capaian['nama_lengkap'] ?? '-');
$inisial     = strtoupper(substr(trim($namaLengkap), 0, 1));
$detailUrl   = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna));
?>

<style>
    .ecc-rj-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-rj-card {
            padding: 16px;
        }
    }
    .ecc-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%);
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ecc-tab-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .ecc-tab-link {
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
    .ecc-tab-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-tab-link.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-attr-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .ecc-attr-row:last-child {
        border-bottom: 0;
    }
    .ecc-attr-label {
        width: 160px;
        color: #64748b;
        flex-shrink: 0;
    }
    .ecc-attr-value {
        color: #0f172a;
        font-weight: 500;
    }
    .ecc-preview-surface {
        background-color: #f1f5f9;
        border-radius: 12px;
        padding: 24px;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ecc-preview-sheet {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        width: 160px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-btn-pill-sm {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="text-decoration-none text-secondary">Rekam Jejak Taruna</a></li>
                <li class="breadcrumb-item"><a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="text-decoration-none text-secondary"><?= esc($namaLengkap) ?></a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Detail Capaian</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">Detail Capaian</h4>
        <p class="text-secondary mb-0" style="font-size: 0.875rem;">Informasi lengkap terkait capaian taruna yang terintegrasi dalam satu data di ECC 2.0.</p>
    </div>
    <div>
        <a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-chevron-left"></i> Kembali ke Rekam Jejak
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

<!-- Kartu Identitas Header Taruna -->
<div class="ecc-rj-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-4 d-flex align-items-center gap-3">
            <div class="ecc-avatar-lg"><?= esc($inisial) ?></div>
            <div>
                <h5 class="fw-bold text-dark mb-1"><?= esc($namaLengkap) ?></h5>
                <div class="text-secondary" style="font-size: 0.8125rem;">Detail Capaian Rekam Jejak</div>
            </div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
            <div class="fw-bold text-dark ecc-num"><?= esc($nomorTaruna) ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
            <div class="fw-bold text-dark"><?= esc((string) ($capaian['kode_prodi'] ?? '-')) ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
            <div class="fw-bold text-dark ecc-num"><?= esc((string) ($capaian['angkatan'] ?? '-')) ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
            <div><?= ketarunaan_status_badge((string) ($capaian['status_terkini'] ?? 'aktif')) ?></div>
        </div>
    </div>
</div>

<!-- Kontainer Detail Capaian -->
<div class="ecc-rj-card">
    <div class="ecc-tab-nav">
        <a href="<?= esc($detailUrl . '?tab=ringkasan') ?>" class="ecc-tab-link">
            <i class="bi bi-clipboard-data"></i> Ringkasan
        </a>
        <a href="<?= esc($detailUrl . '?tab=kronologi') ?>" class="ecc-tab-link">
            <i class="bi bi-clock-history"></i> Kronologi
        </a>
        <a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="ecc-tab-link active">
            <i class="bi bi-award"></i> Detail Capaian
        </a>
        <a href="<?= esc($detailUrl . '?tab=dokumen') ?>" class="ecc-tab-link">
            <i class="bi bi-file-earmark-text"></i> Dokumen Bukti
        </a>
    </div>

    <?php if (!empty($capaian['catatan_validasi']) && in_array($capaian['status_validasi'], ['perlu_perbaikan', 'dikembalikan'], true)): ?>
        <div class="alert alert-warning border rounded-3 mb-4 py-2 px-3" style="font-size: 0.875rem;">
            <strong>Catatan Perbaikan:</strong> <?= esc((string) $capaian['catatan_validasi']) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Atribut Capaian -->
        <div class="col-12 col-lg-7">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-48" style="background-color: #fef3c7; color: #d97706;">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1"><?= esc((string) $capaian['nama_capaian']) ?></h5>
                            <?= ketarunaan_capaian_kategori_badge((string) $capaian['kategori'], true) ?>
                        </div>
                    </div>
                    <div>
                        <?= ketarunaan_validasi_badge($capaian['status_validasi'] === 'perlu_perbaikan' ? 'dikembalikan' : (string) $capaian['status_validasi']) ?>
                    </div>
                </div>

                <div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tanggal</div>
                        <div class="ecc-attr-value ecc-num">: <?= esc(ketarunaan_format_tanggal($capaian['tanggal_kegiatan'], false)) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tahun Akademik</div>
                        <div class="ecc-attr-value">: <?= esc((string) ($capaian['tahun_akademik_label'] ?: '2026/2027 Ganjil')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Semester</div>
                        <div class="ecc-attr-value ecc-num">: <?= (int) ($capaian['semester'] ?? 5) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Penyelenggara</div>
                        <div class="ecc-attr-value">: <?= esc((string) $capaian['penyelenggara']) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tingkat</div>
                        <div class="ecc-attr-value">: <?= esc((string) $capaian['tingkat_kegiatan']) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Hasil</div>
                        <div class="ecc-attr-value">: <?= esc((string) $capaian['hasil_capaian']) ?>
                            <?php if (!empty($capaian['nilai_angka'])): ?>
                                <span class="text-secondary ecc-num">(<?= esc((string) $capaian['nilai_angka']) ?> <?= esc((string) ($capaian['satuan_nilai'] ?? '')) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Nomor Dokumen</div>
                        <div class="ecc-attr-value ecc-num">: <?= esc((string) ($capaian['nomor_dokumen'] ?: '-')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Unit</div>
                        <div class="ecc-attr-value">: <?= esc((string) ($capaian['unit_pemilik'] ?: 'Ketarunaan')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Sumber Dokumen</div>
                        <div class="ecc-attr-value">: <?= esc(strtolower((string) ($capaian['sumber_dokumen'] ?: 'sertifikat'))) ?></div>
                    </div>
                    <?php if (!empty($capaian['uraian_capaian'])): ?>
                        <div class="ecc-attr-row">
                            <div class="ecc-attr-label">Uraian</div>
                            <div class="ecc-attr-value">: <?= esc((string) $capaian['uraian_capaian']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Bukti Pendukung -->
        <div class="col-12 col-lg-5">
            <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-3">Bukti Pendukung</h6>
                    <div class="ecc-preview-surface mb-3">
                        <div class="ecc-preview-sheet">
                            <div class="rounded-1 mb-2" style="height: 16px; width: 64px; background-color: #cbd5e1; margin: 0 auto;"></div>
                            <div class="rounded-1 mb-2" style="height: 8px; background-color: #e2e8f0;"></div>
                            <div class="rounded-1 mb-2" style="height: 8px; background-color: #e2e8f0;"></div>
                            <div class="rounded-1 mb-2" style="height: 8px; background-color: #e2e8f0;"></div>
                            <div class="rounded-1" style="height: 8px; width: 96px; background-color: #cbd5e1;"></div>
                        </div>
                    </div>
                </div>

                <?php
                $namaBerkas = (string) ($capaian['bukti_berkas'] ?: 'Sertifikat.pdf');
                $ukuranKb   = (int) ($capaian['ukuran_berkas_kb'] ?: 456);
                ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.875rem;"><?= esc($namaBerkas) ?></div>
                            <div class="text-secondary ecc-num" style="font-size: 0.75rem;">PDF &bull; <?= $ukuranKb ?> KB</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $idCapaian . '?inline=1') ?>" target="_blank" class="btn btn-outline-primary ecc-btn-pill-sm">
                            Pratinjau
                        </a>
                        <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $idCapaian) ?>" class="btn btn-outline-secondary ecc-btn-pill-sm">
                            Unduh
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Bawah: Informasi Pengaju & Validator (Tanpa Jam Teknis sesuai AGENTS.md §4.L) -->
    <div class="border rounded-3 p-3 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light-subtle">
        <div class="d-flex flex-wrap gap-4">
            <div>
                <div class="text-secondary" style="font-size: 0.75rem;">Diajukan oleh</div>
                <div class="fw-bold text-dark" style="font-size: 0.875rem;"><?= esc((string) ($capaian['diajukan_oleh'] ?: 'PIC Ketarunaan')) ?></div>
            </div>
            <div class="border-start ps-4">
                <div class="text-secondary" style="font-size: 0.75rem;">Divalidasi oleh</div>
                <div class="fw-bold text-dark" style="font-size: 0.875rem;">
                    <?= esc((string) ($capaian['divalidasi_oleh'] ?: 'Belum divalidasi')) ?>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if (!empty($canValidate) && $capaian['status_validasi'] !== 'tervalidasi'): ?>
                <form method="post" action="<?= base_url('ketarunaan/rekam-jejak/validasi-capaian/' . $idCapaian) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="tervalidasi">
                    <button type="submit" class="btn btn-success ecc-control-sm px-3 fw-semibold">
                        Validasi Data
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKembalikanCapaian">
                    Kembalikan
                </button>
            <?php endif; ?>

            <?php if (!empty($canManageTaruna)): ?>
                <a href="<?= base_url('ketarunaan/rekam-jejak/edit-capaian/' . $idCapaian) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                    Ajukan Koreksi
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Kembalikan Capaian untuk Perbaikan -->
<?php if (!empty($canValidate)): ?>
    <div class="modal fade" id="modalKembalikanCapaian" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="<?= base_url('ketarunaan/rekam-jejak/validasi-capaian/' . $idCapaian) ?>" class="modal-content rounded-4 border-0 shadow">
                <?= csrf_field() ?>
                <input type="hidden" name="keputusan" value="perlu_perbaikan">
                <div class="modal-header border-bottom px-4 py-3">
                    <h6 class="modal-title fw-bold text-dark" id="modalKembalikanLabel">Kembalikan Data Capaian</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="catatan_validasi" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">Catatan Perbaikan <span class="text-danger">*</span></label>
                    <textarea id="catatan_validasi" name="catatan_validasi" rows="3" class="form-control rounded-3" style="font-size: 0.875rem;" placeholder="Tuliskan bagian yang perlu diperbaiki..." required></textarea>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border ecc-control-sm px-3 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger ecc-control-sm px-3 fw-semibold">Simpan & Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

