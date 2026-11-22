<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Capaian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/rekam_jejak/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$capaian     = $capaian ?? [];
$idCapaian   = (int) ($capaian['id'] ?? 0);
$nomorTaruna = (string) ($capaian['nomor_taruna'] ?? '');
$namaLengkap = (string) ($capaian['nama_lengkap'] ?? '-');
$inisial     = strtoupper(mb_substr(trim($namaLengkap), 0, 1));
$detailUrl   = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna));

$fotoFile    = basename((string) ($capaian['foto'] ?? ''));
$fotoUrl     = ($fotoFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $fotoFile))
    ? base_url('assets/uploads/taruna/' . $fotoFile)
    : null;
$angkatanTxt = !empty($capaian['angkatan'])
    ? ketarunaan_format_angkatan([
        'tahun_masuk' => $capaian['angkatan'],
        'kode_romawi' => $capaian['angkatan_romawi'] ?? '',
    ])
    : '-';

$namaBerkasRaw = trim((string) ($capaian['bukti_berkas'] ?? ''));
$hasBerkas     = ($namaBerkasRaw !== '');
$isExternalUrl = $hasBerkas && ketarunaan_is_http_url($namaBerkasRaw, 2000);
$namaBerkas    = $hasBerkas ? ketarunaan_format_bukti_label($namaBerkasRaw) : '';
$extBerkas     = ($hasBerkas && !$isExternalUrl) ? strtolower(pathinfo(basename($namaBerkasRaw), PATHINFO_EXTENSION)) : '';
$isImageBerkas = ($hasBerkas && !$isExternalUrl) && in_array($extBerkas, ['jpg', 'jpeg', 'png'], true);
$ukuranKb      = (int) ($capaian['ukuran_berkas_kb'] ?? 0);
$canEditBtn    = isset($canEditCapaian) ? !empty($canEditCapaian) : !empty($canManageTaruna);
$statusVal     = (string) ($capaian['status_validasi'] ?? 'draf');
$canDeleteBtn  = $canEditBtn && in_array($statusVal, ['draf', 'perlu_perbaikan', 'dikembalikan'], true);
$canDecideNow  = !empty($canValidate) && in_array($statusVal, ['diajukan', 'perlu_perbaikan', 'dikembalikan'], true);
?>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                <?php if (empty($isSelfOnly)): ?>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="text-decoration-none text-secondary">Rekam Jejak Taruna</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item"><a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="text-decoration-none text-secondary"><?= esc($namaLengkap) ?></a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Detail Capaian</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Detail Capaian</h1>
        <p class="text-secondary mb-0 small">Informasi lengkap capaian taruna beserta bukti pendukungnya.</p>
    </div>
    <div>
        <a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
            <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Rekam Jejak
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc(session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Kartu Identitas Header Taruna -->
<div class="bento-card ecc-rj-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-4 d-flex align-items-center gap-3">
            <div class="ecc-avatar-lg">
                <?php if ($fotoUrl !== null): ?>
                    <img src="<?= esc($fotoUrl) ?>" alt="Foto <?= esc($namaLengkap) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <?= esc($inisial) ?>
                <?php endif; ?>
            </div>
            <div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc($namaLengkap) ?></h2>
                <div class="text-secondary small">Detail Capaian Rekam Jejak</div>
            </div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
            <div class="fw-bold text-dark ecc-num small"><?= esc($nomorTaruna) ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
            <div class="fw-bold text-dark small"><?= esc((string) ($capaian['kode_prodi'] ?? '-')) ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
            <div class="fw-bold text-dark ecc-num small"><?= esc($angkatanTxt) ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
            <div><?= ketarunaan_status_badge((string) ($capaian['status_terkini'] ?? 'aktif')) ?></div>
        </div>
    </div>
</div>

<!-- Kontainer Detail Capaian -->
<div class="bento-card ecc-rj-card">
    <nav class="ecc-tab-nav" aria-label="Navigasi Rekam Jejak">
        <a href="<?= esc($detailUrl . '?tab=ringkasan') ?>" class="ecc-tab-link">Ringkasan</a>
        <a href="<?= esc($detailUrl . '?tab=kronologi') ?>" class="ecc-tab-link">Kronologi</a>
        <a href="<?= esc($detailUrl . '?tab=capaian') ?>" class="ecc-tab-link active" aria-current="page">Daftar Capaian</a>
        <a href="<?= esc($detailUrl . '?tab=dokumen') ?>" class="ecc-tab-link">Dokumen Bukti</a>
    </nav>

    <?php if (!empty($capaian['catatan_validasi']) && in_array($statusVal, ['perlu_perbaikan', 'dikembalikan'], true)): ?>
        <div class="alert alert-warning border rounded-3 mb-4 py-2 px-3 small">
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
                            <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="h5 fw-bold text-dark mb-1"><?= esc((string) $capaian['nama_capaian']) ?></h3>
                            <?= ketarunaan_capaian_kategori_badge((string) $capaian['kategori'], true) ?>
                        </div>
                    </div>
                    <div>
                        <?= ketarunaan_validasi_badge($statusVal) ?>
                    </div>
                </div>

                <div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tanggal</div>
                        <div class="ecc-attr-value ecc-num"><?= esc(ketarunaan_format_tanggal($capaian['tanggal_kegiatan'], false)) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tahun Akademik</div>
                        <div class="ecc-attr-value"><?= esc((string) (!empty($capaian['tahun_akademik_label']) ? $capaian['tahun_akademik_label'] : '-')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Semester</div>
                        <div class="ecc-attr-value ecc-num"><?= !empty($capaian['semester']) ? (int) $capaian['semester'] : '-' ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Penyelenggara</div>
                        <div class="ecc-attr-value"><?= esc((string) $capaian['penyelenggara']) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Tingkat</div>
                        <div class="ecc-attr-value"><?= esc((string) $capaian['tingkat_kegiatan']) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Hasil</div>
                        <div class="ecc-attr-value"><?= esc((string) $capaian['hasil_capaian']) ?>
                            <?php if (!empty($capaian['nilai_angka'])): ?>
                                <span class="text-secondary ecc-num">(<?= esc((string) $capaian['nilai_angka']) ?> <?= esc((string) ($capaian['satuan_nilai'] ?? '')) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Nomor Dokumen</div>
                        <div class="ecc-attr-value ecc-num"><?= esc((string) ($capaian['nomor_dokumen'] ?: '-')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Unit</div>
                        <div class="ecc-attr-value"><?= esc((string) ($capaian['unit_pemilik'] ?: '-')) ?></div>
                    </div>
                    <div class="ecc-attr-row">
                        <div class="ecc-attr-label">Sumber Dokumen</div>
                        <div class="ecc-attr-value"><?= esc((string) ($capaian['sumber_dokumen'] ?: '-')) ?></div>
                    </div>
                    <?php if (!empty($capaian['uraian_capaian'])): ?>
                        <div class="ecc-attr-row">
                            <div class="ecc-attr-label">Uraian</div>
                            <div class="ecc-attr-value"><?= esc((string) $capaian['uraian_capaian']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Bukti Pendukung -->
        <div class="col-12 col-lg-5">
            <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">Bukti Pendukung</h3>
                    <div class="ecc-preview-surface mb-3 text-center">
                        <?php if (!$hasBerkas): ?>
                            <div class="text-secondary small">
                                <i class="bi bi-link-45deg d-block mb-2 fs-3 text-muted" aria-hidden="true"></i>
                                <span>Belum ada tautan bukti pendukung.</span>
                            </div>
                        <?php elseif ($isExternalUrl): ?>
                            <div class="text-secondary small w-100">
                                <i class="bi bi-cloud-check-fill text-primary d-block mb-2 fs-1" aria-hidden="true"></i>
                                <div class="fw-semibold text-dark mb-1"><?= esc($namaBerkas) ?></div>
                                <div class="text-truncate mx-auto mb-3" style="max-width: 280px;" title="<?= esc($namaBerkasRaw) ?>"><?= esc($namaBerkasRaw) ?></div>
                                <a href="<?= esc($namaBerkasRaw) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile rj-touch-ctrl d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan Bukti
                                </a>
                            </div>
                        <?php elseif ($isImageBerkas): ?>
                            <img src="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $idCapaian . '?inline=1') ?>"
                                 alt="Pratinjau <?= esc($namaBerkas) ?>"
                                 class="img-fluid rounded-2"
                                 style="max-height: 224px; object-fit: contain;"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="text-secondary small">
                                <i class="bi bi-file-earmark-pdf-fill text-danger d-block mb-2 fs-1" aria-hidden="true"></i>
                                <div class="fw-semibold text-dark text-break"><?= esc($namaBerkas) ?></div>
                                <div>Dokumen <?= esc(strtoupper($extBerkas ?: 'PDF')) ?><?= $ukuranKb > 0 ? ' &bull; ' . $ukuranKb . ' KB' : '' ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($hasBerkas): ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            <i class="bi <?= $isExternalUrl ? 'bi-link-45deg text-primary' : ($isImageBerkas ? 'bi-file-earmark-image text-primary' : 'bi-file-earmark-pdf-fill text-danger') ?> fs-5 flex-shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark small text-truncate"><?= esc($namaBerkas) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?php if ($isExternalUrl): ?>
                                        Tautan Dokumen Eksternal
                                    <?php else: ?>
                                        <?= esc(strtoupper($extBerkas ?: 'PDF')) ?><?= $ukuranKb > 0 ? ' &bull; ' . $ukuranKb . ' KB' : '' ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <?php if ($isExternalUrl): ?>
                                <a href="<?= esc($namaBerkasRaw) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm rounded-3 px-3 border fw-semibold rj-touch-ctrl d-inline-flex align-items-center gap-1" style="min-height: 32px; color: #1e40af;">
                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $idCapaian . '?inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm rounded-3 px-3 border fw-semibold rj-touch-ctrl d-inline-flex align-items-center" style="min-height: 32px; color: #1e40af;">
                                    Pratinjau
                                </a>
                                <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . $idCapaian) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3 fw-semibold rj-touch-ctrl d-inline-flex align-items-center" style="min-height: 32px;">
                                    Unduh
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Baris Bawah: Informasi Pengaju & Validator (Tanpa Jam Teknis sesuai AGENTS.md §4.L) -->
    <div class="border rounded-3 p-3 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 bg-light-subtle">
        <div class="d-flex flex-wrap gap-4">
            <div>
                <div class="text-secondary" style="font-size: 0.75rem;">Diajukan oleh</div>
                <div class="fw-bold text-dark small"><?= esc((string) ($capaian['diajukan_oleh'] ?: '-')) ?></div>
            </div>
            <div class="border-md-start">
                <div class="text-secondary" style="font-size: 0.75rem;">Divalidasi oleh</div>
                <div class="fw-bold text-dark small">
                    <?= esc((string) ($capaian['divalidasi_oleh'] ?: 'Belum divalidasi')) ?>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if ($canDecideNow): ?>
                <form method="post" action="<?= base_url('ketarunaan/rekam-jejak/validasi-capaian/' . $idCapaian) ?>" id="formValidasiCapaian" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="tervalidasi">
                    <button type="button" class="btn btn-success ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl" onclick="confirmValidateCapaian()">
                        Validasi Data
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl" data-bs-toggle="modal" data-bs-target="#modalKembalikanCapaian">
                    Kembalikan
                </button>
            <?php endif; ?>

            <?php if ($canEditBtn): ?>
                <a href="<?= base_url('ketarunaan/rekam-jejak/edit-capaian/' . $idCapaian) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                    <?= ($statusVal === 'tervalidasi') ? 'Ajukan Koreksi' : 'Edit / Perbaiki' ?>
                </a>
            <?php endif; ?>

            <?php if ($canDeleteBtn): ?>
                <form method="post" action="<?= base_url('ketarunaan/rekam-jejak/hapus-capaian/' . $idCapaian) ?>" id="formHapusCapaian" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl" onclick="confirmDeleteCapaian()">
                        Hapus Draf
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Kembalikan Capaian untuk Perbaikan -->
<?php if ($canDecideNow): ?>
    <div class="modal fade" id="modalKembalikanCapaian" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="<?= base_url('ketarunaan/rekam-jejak/validasi-capaian/' . $idCapaian) ?>" class="modal-content rounded-4 border-0 shadow">
                <?= csrf_field() ?>
                <input type="hidden" name="keputusan" value="perlu_perbaikan">
                <div class="modal-header border-bottom px-4 py-3">
                    <h4 class="h6 modal-title fw-bold text-dark" id="modalKembalikanLabel">Kembalikan Data Capaian</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="catatan_validasi" class="form-label fw-semibold text-dark small">Catatan Perbaikan <span class="text-danger">*</span></label>
                    <textarea id="catatan_validasi" name="catatan_validasi" rows="3" class="form-control rounded-3 small" placeholder="Tuliskan bagian yang perlu diperbaiki..." required></textarea>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border ecc-control-sm px-3 fw-semibold rj-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger ecc-control-sm px-3 fw-semibold rj-touch-ctrl">Simpan &amp; Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmValidateCapaian() {
    var form = document.getElementById('formValidasiCapaian');
    if (!form) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Validasi Data Capaian?',
            text: 'Capaian ini akan disahkan dan masuk ke rekam jejak resmi taruna.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#15803d',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Validasi',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow border-0'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else if (confirm('Capaian ini akan disahkan ke rekam jejak resmi taruna. Lanjutkan?')) {
        form.submit();
    }
}

function confirmDeleteCapaian() {
    var form = document.getElementById('formHapusCapaian');
    if (!form) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Draf Capaian?',
            text: 'Data capaian ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow border-0'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else if (confirm('Data capaian ini akan dihapus. Lanjutkan?')) {
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
