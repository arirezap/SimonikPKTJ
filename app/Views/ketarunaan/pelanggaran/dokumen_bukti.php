<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dokumen Bukti Kasus<?= $this->endSection() ?>

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
    .ecc-table-pl thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .ecc-table-pl tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
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
$kasus        = $detail['kasus'] ?? [];
$dokumen      = $detail['dokumen'] ?? [];
$checklist    = $detail['checklist'] ?? [];
$refs         = $refs ?? [];
$canViewFull  = !empty($canViewFull);
$idKasus      = (int) ($kasus['id'] ?? 0);

$laporanAda     = !empty($checklist['laporan_ada']);
$bapAda         = !empty($checklist['bap_ada']);
$penetapanAda   = !empty($checklist['penetapan_ada']);
$penetapanWajib = !empty($checklist['penetapan_wajib']);
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 4,
        'pageTitle'    => 'Dokumen Bukti Kasus',
        'pageSubtitle' => 'Mengelola dokumen laporan, pemeriksaan, dan keputusan untuk validasi pencatatan.',
        'kasus'        => $kasus,
    ]) ?>

    <div class="ecc-pl-card">
        <div class="row g-4">
            <!-- Kolom Kiri: Daftar Dokumen & Unggah -->
            <div class="col-12 col-lg-7">
                <div class="ecc-panel-box h-100">
                    <h6 class="fw-bold text-dark mb-1">Daftar Dokumen Bukti</h6>
                    <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Seluruh berkas berstatus terbatas dan hanya dapat dibuka oleh petugas berwenang.</p>

                    <div class="table-responsive mb-4">
                        <table class="table ecc-table-pl mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Berkas</th>
                                    <th>Jenis</th>
                                    <th>Klasifikasi</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dokumen)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">Belum ada dokumen bukti yang diunggah.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dokumen as $dok): ?>
                                        <?php
                                        $fileSafe   = basename((string) ($dok['nama_berkas'] ?? 'Dokumen.pdf'));
                                        $tglUnggah  = ketarunaan_format_tanggal((string) ($dok['tanggal_unggah'] ?? ''), true);
                                        $isSangat   = (($dok['klasifikasi'] ?? 'terbatas') === 'sangat_terbatas');
                                        $bisaAkses  = !empty($dok['can_access']) && $canViewFull;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark text-truncate" style="max-width: 240px;"><?= esc($fileSafe) ?></div>
                                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                    Diunggah <?= esc($tglUnggah) ?>
                                                    <?php if (!empty($dok['ukuran_kb'])): ?>
                                                        &bull; <?= number_format((int) $dok['ukuran_kb'], 0, ',', '.') ?> KB
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= esc((string) ($dok['jenis_dokumen'] ?? 'Pendukung')) ?></td>
                                            <td>
                                                <?php if ($isSangat): ?>
                                                    <span class="badge bg-dark-subtle text-dark rounded-pill px-3 py-1" style="font-size: 0.75rem;">Sangat Terbatas</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1" style="font-size: 0.75rem;">Terbatas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <?php if ($bisaAkses): ?>
                                                    <a href="<?= base_url('ketarunaan/pelanggaran/unduh-dokumen/' . $idKasus . '?file=' . rawurlencode($fileSafe) . '&inline=1') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm rounded-pill px-3">Pratinjau</a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Terkunci</span>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 btn-hapus-dok" data-filename="<?= esc($fileSafe) ?>">Hapus</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <form id="formDokumenKasus" method="post" action="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus . '/simpan') ?>" enctype="multipart/form-data" class="border rounded-3 p-3 bg-light">
                        <?= csrf_field() ?>
                        <h6 class="fw-semibold text-dark mb-2" style="font-size: 0.875rem;">Tambah Dokumen Baru (Opsional)</h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <input type="file" id="inputDokFile" name="dokumen_file" class="form-control ecc-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text" style="font-size: 0.75rem;">Format PDF, JPG, atau PNG. Ukuran maksimal 5 MB per berkas.</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="selectJenisDok" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Dokumen</label>
                                <select id="selectJenisDok" name="jenis_dokumen" class="form-select ecc-control-sm">
                                    <?php foreach ($refs['jenis_dokumen_options'] ?? [] as $opt): ?>
                                        <option value="<?= esc($opt) ?>"><?= esc($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="selectKlasifikasiDok" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Klasifikasi Akses</label>
                                <select id="selectKlasifikasiDok" name="klasifikasi" class="form-select ecc-control-sm">
                                    <option value="terbatas">Terbatas</option>
                                    <option value="sangat_terbatas">Sangat Terbatas</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Status Ketersediaan Dokumen -->
            <div class="col-12 col-lg-5">
                <div class="ecc-panel-box h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Status Ketersediaan Dokumen</h6>
                        <p class="text-secondary mb-3" style="font-size: 0.8125rem;">Periksa kelengkapan dokumen sesuai tahapan penanganan kasus.</p>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">1. Laporan awal tercatat</span>
                                <span class="badge <?= $laporanAda ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' ?> rounded-pill px-3 py-1">
                                    <?= $laporanAda ? 'Tersedia' : 'Belum Lengkap' ?>
                                </span>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">2. Berita acara pemeriksaan (BAP)</span>
                                <span class="badge <?= $bapAda ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> rounded-pill px-3 py-1">
                                    <?= $bapAda ? 'Tersedia' : 'Belum Tersedia' ?>
                                </span>
                            </div>

                            <div class="ecc-check-item">
                                <span class="fw-medium text-dark" style="font-size: 0.8125rem;">
                                    3. Dokumen penetapan <?= $penetapanWajib ? '(Wajib untuk Terbukti)' : '(Opsional untuk Dugaan)' ?>
                                </span>
                                <span class="badge <?= $penetapanAda ? 'bg-success-subtle text-success' : ($penetapanWajib ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary') ?> rounded-pill px-3 py-1">
                                    <?= $penetapanAda ? 'Tersedia' : 'Belum Tersedia' ?>
                                </span>
                            </div>
                        </div>

                        <div class="rounded-3 p-3" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; font-size: 0.8125rem;">
                            Kasus dugaan dapat diajukan untuk validasi pencatatan dan tetap tidak dihitung sebagai pelanggaran terbukti.
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-3 mt-4 border-top">
                        <button type="submit" form="formDokumenKasus" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold flex-fill">
                            Simpan Draf
                        </button>
                        <button type="submit" form="formDokumenKasus" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-fill">
                            Ajukan Validasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
            <a href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Sebelumnya: Penetapan dan Tindak Lanjut
            </a>
            <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $idKasus) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                Lihat Detail Kasus
            </a>
        </div>
    </div>

    <!-- Form Terpisah Hapus Dokumen (Menghindari Nested Form) -->
    <form id="formHapusDok" method="post" action="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $idKasus . '/hapus') ?>" class="d-none">
        <?= csrf_field() ?>
        <input type="hidden" name="filename" id="hapusDokFilename" value="">
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputDok = document.getElementById('inputDokFile');
    if (inputDok) {
        inputDok.addEventListener('change', function() {
            const f = this.files && this.files[0];
            if (!f) {
                return;
            }
            const lower = f.name.toLowerCase();
            const okExt = lower.endsWith('.pdf') || lower.endsWith('.jpg') || lower.endsWith('.jpeg') || lower.endsWith('.png');
            if (!okExt || f.size > 5 * 1024 * 1024) {
                this.value = '';
                const msg = 'Berkas harus berformat PDF, JPG, atau PNG maksimal 5 MB.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Berkas Tidak Sesuai', text: msg, icon: 'warning', confirmButtonText: 'Tutup' });
                } else {
                    alert(msg);
                }
            }
        });
    }

    const formHapusDok = document.getElementById('formHapusDok');
    const inputHapus   = document.getElementById('hapusDokFilename');

    document.querySelectorAll('.btn-hapus-dok').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename') || '';
            if (!filename || !formHapusDok || !inputHapus) {
                return;
            }
            inputHapus.value = filename;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Dokumen?',
                    text: 'Berkas ' + filename + ' akan dihapus dari kasus ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(res) {
                    if (res.isConfirmed) {
                        formHapusDok.submit();
                    }
                });
            } else if (confirm('Berkas ' + filename + ' akan dihapus dari kasus ini.')) {
                formHapusDok.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
