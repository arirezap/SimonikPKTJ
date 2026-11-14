<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dokumen Bukti Prestasi<?= $this->endSection() ?>

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
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #0284c7;
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
        background-color: #ffffff;
    }
    .ecc-check-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
    }
    .ecc-file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
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
$detail       = $detail ?? [];
$prestasi     = $detail['prestasi'] ?? [];
$dokumentasi  = $detail['dokumentasi'] ?? [];
$checklist    = $detail['checklist'] ?? [];

$idPrestasi   = (int) ($prestasi['id'] ?? 0);
$rawStatus    = (string) ($prestasi['status_validasi'] ?? 'draf');
$stValidasi   = ($rawStatus === 'perlu_perbaikan') ? 'dikembalikan' : $rawStatus;

$tingkatMap   = ketarunaan_tingkat_prestasi_catalog();
$tingkatLbl   = $tingkatMap[$prestasi['tingkat_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));

$infoLengkap  = !empty($checklist['info_lengkap']);
$pesertaAda   = !empty($checklist['peserta_ada']);
$jumlahPeserta = (int) ($checklist['jumlah_peserta'] ?? 0);
$buktiAda     = !empty($checklist['bukti_ada']);

$buktiBerkas  = (string) ($prestasi['bukti_berkas'] ?? '');
$ukuranKb     = (int) ($prestasi['ukuran_berkas_kb'] ?? 0);
$sisaSlotFoto = max(0, 5 - count($dokumentasi));
$isSubmitReady = ($infoLengkap && $pesertaAda);
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Dokumen Bukti</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">Dokumen Bukti Prestasi</h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Lengkapi dokumen bukti utama dan ajukan prestasi untuk validasi.</p>
        </div>
        <div>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i> Kembali ke Detail Prestasi
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

    <!-- Kartu Ringkas Prestasi -->
    <div class="ecc-pr-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc((string) ($prestasi['nama_prestasi'] ?? '-')) ?></h5>
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        <?= esc($tingkatLbl) ?> &bull; <?= esc((string) ($prestasi['peringkat'] ?? '-')) ?>
                        <?php if (!empty($prestasi['nama_tim'])): ?>
                            &bull; <?= esc((string) $prestasi['nama_tim']) ?>
                        <?php else: ?>
                            &bull; <?= esc(ucfirst((string) ($prestasi['jenis_keikutsertaan'] ?? 'individu'))) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div><?= ketarunaan_validasi_badge($stValidasi) ?></div>
        </div>
    </div>

    <?php if ($rawStatus === 'perlu_perbaikan' && !empty($prestasi['catatan_validasi'])): ?>
        <div class="alert alert-warning border-0 rounded-3 mb-4 px-3 py-2" role="alert">
            <div class="fw-semibold text-dark" style="font-size: 0.8125rem;">Catatan Perbaikan Validator</div>
            <div class="text-dark mt-1" style="font-size: 0.875rem;"><?= esc((string) $prestasi['catatan_validasi']) ?></div>
        </div>
    <?php endif; ?>

    <!-- Kontainer Tahap 3 -->
    <div class="ecc-pr-card">
        <div class="ecc-wizard-nav">
            <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="ecc-wizard-step">1. Informasi Prestasi</a>
            <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="ecc-wizard-step">2. Peserta</a>
            <span class="ecc-wizard-step active">3. Dokumen Bukti</span>
        </div>

        <div class="row g-4">
            <!-- Kolom Kiri: Dokumen Unggahan -->
            <div class="col-12 col-lg-7">
                <div class="ecc-panel-box h-100">
                    <h6 class="fw-bold text-dark mb-1">Dokumen Unggahan</h6>
                    <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Isi nomor sertifikat dan unggah bukti piagam atau SK pemenang.</p>

                    <form id="formDokumenBukti" method="post" action="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi . '/simpan') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="inputNomorSertifikat" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Nomor Sertifikat / Piagam / SK <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                id="inputNomorSertifikat"
                                name="nomor_sertifikat"
                                class="form-control ecc-control-sm"
                                value="<?= esc(old('nomor_sertifikat', (string) ($prestasi['nomor_sertifikat'] ?? ''))) ?>"
                                placeholder="Misal: PIAGAM/115/2025"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="inputBuktiBerkas" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Bukti Utama Prestasi (PDF) <?php if ($buktiBerkas === ''): ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <input
                                type="file"
                                id="inputBuktiBerkas"
                                name="bukti_berkas"
                                class="form-control ecc-control-sm"
                                accept=".pdf,application/pdf"
                                <?= $buktiBerkas === '' ? 'required' : '' ?>
                            >
                            <div class="form-text" style="font-size: 0.75rem;">Format PDF, ukuran maksimal 10 MB.</div>

                            <?php if ($buktiBerkas !== ''): ?>
                                <div class="ecc-file-item mt-2">
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8125rem;"><?= esc(basename($buktiBerkas)) ?></div>
                                        <?php if ($ukuranKb > 0): ?>
                                            <div class="text-secondary ecc-num" style="font-size: 0.75rem;"><?= number_format($ukuranKb, 0, ',', '.') ?> KB</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm rounded-pill px-3">Pratinjau</a>
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-pill px-3">Unduh</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-0">
                            <label for="inputDokumentasiFiles" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                                Dokumentasi Tambahan (Opsional)
                            </label>
                            <input
                                type="file"
                                id="inputDokumentasiFiles"
                                name="dokumentasi_files[]"
                                class="form-control ecc-control-sm"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                multiple
                                data-sisa-slot="<?= $sisaSlotFoto ?>"
                                <?= $sisaSlotFoto === 0 ? 'disabled' : '' ?>
                            >
                            <div class="form-text ecc-num" style="font-size: 0.75rem;">
                                Format JPG atau PNG, maksimal 5 berkas (5 MB per berkas). Tersisa <?= $sisaSlotFoto ?> slot.
                            </div>

                            <?php if (!empty($dokumentasi)): ?>
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <?php foreach ($dokumentasi as $foto): ?>
                                        <div class="ecc-file-item">
                                            <div class="text-truncate">
                                                <span class="fw-medium text-dark text-truncate d-block" style="font-size: 0.8125rem;"><?= esc(basename((string) $foto)) ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode((string) $foto) . '&inline=1') ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Pratinjau</a>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 btn-hapus-foto" data-filename="<?= esc((string) $foto) ?>">Hapus</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Pemeriksaan Pengajuan -->
            <div class="col-12 col-lg-5">
                <div class="ecc-panel-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Pemeriksaan Pengajuan</h6>
                        <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Pastikan seluruh kelengkapan berikut sudah terpenuhi sebelum diajukan.</p>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">1. Informasi prestasi lengkap</span>
                                <?php if ($infoLengkap): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">Lengkap</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1">Belum Lengkap</span>
                                <?php endif; ?>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">2. Minimal 1 peserta terhubung</span>
                                <?php if ($pesertaAda): ?>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 ecc-num"><?= $jumlahPeserta ?> peserta</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">Belum Ada</span>
                                <?php endif; ?>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">3. Dokumen bukti utama tersedia</span>
                                <span id="badgeChecklistBukti" class="badge <?= $buktiAda ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill px-3 py-1">
                                    <?= $buktiAda ? 'Tersedia' : 'Belum Diunggah' ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!$isSubmitReady): ?>
                            <div class="alert alert-warning border-0 rounded-3 px-3 py-2 mb-3" style="font-size: 0.8125rem;">
                                Lengkapi tahap informasi prestasi dan tambahkan minimal 1 peserta sebelum mengajukan validasi.
                            </div>
                        <?php endif; ?>

                        <div class="rounded-3 p-3" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 0.8125rem;">
                            Data tervalidasi dihitung sebagai kinerja utama Unit dan tampil pada Rekam Jejak Taruna.
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-3 mt-4 border-top">
                        <button type="submit" form="formDokumenBukti" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold flex-fill">
                            Simpan Draf
                        </button>
                        <button type="submit" form="formDokumenBukti" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-fill">
                            Ajukan Validasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigasi Bawah Tahap -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
            <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Sebelumnya: Peserta
            </a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                Lihat Detail Prestasi
            </a>
        </div>
    </div>

    <!-- Form Terpisah Hapus Foto Dokumentasi (Menghindari Nested Form) -->
    <form id="formHapusFoto" method="post" action="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi . '/hapus-foto') ?>" class="d-none">
        <?= csrf_field() ?>
        <input type="hidden" name="filename" id="hapusFotoFilename" value="">
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBukti   = document.getElementById('inputBuktiBerkas');
    const inputFoto    = document.getElementById('inputDokumentasiFiles');
    const badgeBukti   = document.getElementById('badgeChecklistBukti');
    const sudahAdaFile = <?= $buktiBerkas !== '' ? 'true' : 'false' ?>;

    function tampilPesan(judul, pesan) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: judul,
                text: pesan,
                icon: 'warning',
                confirmButtonText: 'Tutup'
            });
        } else {
            alert(pesan);
        }
    }

    if (inputBukti) {
        inputBukti.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) {
                if (badgeBukti && !sudahAdaFile) {
                    badgeBukti.className = 'badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1';
                    badgeBukti.textContent = 'Belum Diunggah';
                }
                return;
            }
            const isPdf = file.name.toLowerCase().endsWith('.pdf') || file.type === 'application/pdf';
            if (!isPdf || file.size > 10 * 1024 * 1024) {
                this.value = '';
                tampilPesan('Berkas Tidak Sesuai', 'Bukti utama harus berformat PDF dengan ukuran maksimal 10 MB.');
                return;
            }
            if (badgeBukti) {
                badgeBukti.className = 'badge bg-success-subtle text-success rounded-pill px-3 py-1';
                badgeBukti.textContent = 'Siap Diunggah';
            }
        });
    }

    if (inputFoto) {
        inputFoto.addEventListener('change', function() {
            const sisa = parseInt(this.getAttribute('data-sisa-slot') || '5', 10);
            const files = Array.from(this.files || []);
            if (files.length > sisa) {
                this.value = '';
                tampilPesan('Melebihi Batas', 'Jumlah dokumentasi tambahan maksimal tersisa ' + sisa + ' berkas.');
                return;
            }
            for (const f of files) {
                const lower = f.name.toLowerCase();
                const isImg = lower.endsWith('.jpg') || lower.endsWith('.jpeg') || lower.endsWith('.png');
                if (!isImg || f.size > 5 * 1024 * 1024) {
                    this.value = '';
                    tampilPesan('Berkas Tidak Sesuai', 'Foto dokumentasi harus berformat JPG atau PNG maksimal 5 MB.');
                    return;
                }
            }
        });
    }

    const formHapusFoto = document.getElementById('formHapusFoto');
    const inputHapus    = document.getElementById('hapusFotoFilename');

    document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename') || '';
            if (!filename || !formHapusFoto || !inputHapus) {
                return;
            }
            inputHapus.value = filename;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Dokumentasi?',
                    text: 'Berkas ' + filename + ' akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(res) {
                    if (res.isConfirmed) {
                        formHapusFoto.submit();
                    }
                });
            } else if (confirm('Berkas ' + filename + ' akan dihapus.')) {
                formHapusFoto.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
