<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Impor Data Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stepper-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .impor-stat-tile {
        border-radius: 12px;
        padding: 16px;
        border: 1px solid transparent;
        height: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 1. BREADCRUMB & HEADER -->
    <div class="mb-4">
        <div class="text-muted small mb-2">
            <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">ECC 2.0</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
            <span class="mx-1">›</span>
            <span class="text-dark fw-medium">Impor Data</span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1">Impor Data Taruna</h1>
        <p class="text-muted small mb-2">Unggah, periksa, dan impor data taruna dari file Excel/CSV.</p>
        <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light btn-sm rounded-3 border px-3 fw-semibold text-primary d-inline-flex align-items-center gap-2 btn-tactile" style="height: 36px;">
            <i class="bi bi-chevron-left"></i>
            <span>Kembali ke Database Taruna</span>
        </a>
    </div>

    <!-- 2. STEPPER 3 TAHAP -->
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="row g-3 text-center align-items-center">
            <div class="col-12 col-md-4">
                <div class="stepper-circle bg-primary text-white mb-2">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="fw-bold text-dark small">1. Unggah Berkas</div>
                <div class="text-muted small">Berkas berhasil diunggah</div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stepper-circle bg-primary text-white mb-2 tabular-nums">2</div>
                <div class="fw-bold text-primary small">2. Periksa Data</div>
                <div class="text-muted small">Periksa hasil validasi data</div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stepper-circle bg-light text-muted border mb-2 tabular-nums">3</div>
                <div class="fw-semibold text-muted small">3. Simpan Draf</div>
                <div class="text-muted small">Selesai dan simpan sebagai draf</div>
            </div>
        </div>
    </div>

    <!-- 3. PANEL PEMERIKSAAN & PRATINJAU DATA -->
    <div class="bento-card p-3 p-md-4">
        <!-- Info Berkas & Tombol Template -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 rounded-3 border bg-light bg-opacity-50 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 40px; height: 40px;">
                    <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark"><?= esc($preview['nama_berkas'] ?? 'data_taruna.xlsx') ?></div>
                    <div class="text-muted small tabular-nums"><?= number_format((int) ($preview['total_baris'] ?? 0)) ?> baris data ditemukan</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="<?= site_url('ketarunaan/taruna/impor/template') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 36px;">
                    <i class="bi bi-download"></i>
                    <span>Unduh Template Excel</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Pemeriksaan -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="text-primary fw-semibold small mb-1">Total Baris</div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['total_baris'] ?? 0)) ?></div>
                    <div class="text-muted small">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile" style="background-color: #ecfdf5; border-color: #a7f3d0;">
                    <div class="text-success fw-semibold small mb-1">Siap Diimpor</div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['siap_diimpor'] ?? 0)) ?></div>
                    <div class="text-muted small">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile" style="background-color: #fef3c7; border-color: #fde68a;">
                    <div class="text-warning-emphasis fw-semibold small mb-1">Duplikat</div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['duplikat'] ?? 0)) ?></div>
                    <div class="text-muted small">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile" style="background-color: #fef2f2; border-color: #fecaca;">
                    <div class="text-danger fw-semibold small mb-1">Perlu Perbaikan</div>
                    <div class="h2 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['perlu_perbaikan'] ?? 0)) ?></div>
                    <div class="text-muted small">baris</div>
                </div>
            </div>
        </div>

        <!-- Tabel Pratinjau Hasil Pemeriksaan Data -->
        <h6 class="fw-bold text-dark mb-3">Pratinjau Hasil Pemeriksaan Data</h6>
        <div class="table-responsive border rounded-3 mb-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 80px;">Baris</th>
                        <th>Nomor Taruna</th>
                        <th>Nama</th>
                        <th>Hasil</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($preview['preview_rows'] ?? []) as $row): ?>
                        <?php
                            $rowClass = '';
                            $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                            if (($row['status_cek'] ?? '') === 'duplikat') {
                                $rowClass = 'table-warning';
                                $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                            } elseif (($row['status_cek'] ?? '') === 'tidak_valid') {
                                $rowClass = 'table-danger';
                                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            }
                        ?>
                        <tr class="<?= esc($rowClass) ?>">
                            <td class="tabular-nums small fw-semibold"><?= (int) $row['baris'] ?></td>
                            <td class="tabular-nums fw-bold text-dark small"><?= esc($row['nomor_taruna']) ?></td>
                            <td class="small fw-medium text-dark"><?= esc($row['nama_lengkap']) ?></td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($badgeClass) ?>">
                                    <?= esc($row['label_cek']) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= esc($row['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Info Banner -->
        <div class="p-3 rounded-3 d-flex align-items-center gap-2 small mb-4" style="background-color: #eff6ff; color: #1e40af;">
            <i class="bi bi-info-circle-fill"></i>
            <span>Data hasil impor disimpan <strong>sebagai draf</strong> dan memerlukan validasi. Tidak ada data yang langsung ditimpa secara otomatis.</span>
        </div>

        <!-- Baris Aksi Bawah -->
        <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
            <button type="button" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 40px;" onclick="window.print()">
                <i class="bi bi-download"></i>
                <span>Unduh Hasil Pemeriksaan</span>
            </button>

            <button type="button" class="btn btn-outline-primary rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalGantiBerkas">
                <i class="bi bi-arrow-repeat"></i>
                <span>Ganti Berkas</span>
            </button>

            <?php if ($can_manage && (int) ($preview['siap_diimpor'] ?? 0) > 0): ?>
                <form method="post" action="<?= site_url('ketarunaan/taruna/impor/proses') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 40px;">
                        <i class="bi bi-upload"></i>
                        <span>Impor <?= number_format((int) $preview['siap_diimpor']) ?> Baris Valid</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- MODAL UNGGAH / GANTI BERKAS -->
<div class="modal fade" id="modalGantiBerkas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/impor/preview') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <h6 class="modal-title fw-bold text-dark mb-0">Unggah Berkas Data Taruna</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Pilih berkas CSV sesuai format template resmi untuk diperiksa otomatis.</p>
                    <input type="file" name="berkas_impor" accept=".csv,.txt" class="form-control" required>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal" style="height: 36px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="height: 36px;">Periksa Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

