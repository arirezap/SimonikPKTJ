<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Impor Data Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stepper-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .impor-stat-tile {
        border-radius: 12px;
        padding: 16px;
        height: 100%;
    }
    .taruna-sort-btn {
        background: none;
        border: 0;
        padding: 0;
        color: inherit;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .taruna-sort-btn:hover,
    .taruna-sort-btn:focus-visible {
        color: #1e40af;
    }
    .impor-table-wrap {
        max-height: 480px;
        overflow-y: auto;
    }
    .impor-table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: 12px 16px;
    }
    .impor-table-wrap tbody td {
        padding: 12px 16px;
    }
    @media (max-width: 767.98px) {
        .taruna-touch-ctrl {
            min-height: 44px !important;
        }
        #inputBerkasImpor,
        #modalBerkasImporInput {
            font-size: 16px !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
    @media print {
        .d-print-none,
        .btn,
        .alert {
            display: none !important;
        }
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
            <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">Ketarunaan</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
            <span class="mx-1">›</span>
            <span class="text-dark fw-medium">Impor Data</span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1">Impor Data Taruna</h1>
        <p class="text-muted small mb-2">Unggah, periksa, dan impor data taruna dari berkas Excel.</p>
        <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light btn-sm rounded-3 border px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl d-print-none" style="height: 36px; color: #1e40af;">
            <i class="bi bi-chevron-left"></i>
            <span>Kembali ke Database Taruna</span>
        </a>
    </div>

    <?php $hasPreview = is_array($preview) && !empty($preview); ?>

    <!-- 2. STEPPER 3 TAHAP -->
    <div class="bento-card p-3 p-md-4 mb-4 d-print-none">
        <div class="row g-3 text-center align-items-center">
            <div class="col-12 col-md-4">
                <div class="stepper-circle bg-primary text-white mb-2">
                    <?php if ($hasPreview): ?>
                        <i class="bi bi-check-lg"></i>
                    <?php else: ?>
                        <span class="tabular-nums">1</span>
                    <?php endif; ?>
                </div>
                <div class="fw-bold small" style="color: <?= $hasPreview ? '#0f172a' : '#1e40af' ?>;">1. Unggah Berkas</div>
                <div class="text-muted small"><?= $hasPreview ? 'Berkas berhasil diperiksa' : 'Pilih berkas Excel (.xlsx / .xls)' ?></div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stepper-circle <?= $hasPreview ? 'bg-primary text-white' : 'bg-light text-muted border' ?> mb-2 tabular-nums">2</div>
                <div class="fw-bold small" style="color: <?= $hasPreview ? '#1e40af' : '#64748b' ?>;">2. Periksa Data</div>
                <div class="text-muted small">Periksa hasil validasi data</div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stepper-circle bg-light text-muted border mb-2 tabular-nums">3</div>
                <div class="fw-semibold text-muted small">3. Simpan Draf</div>
                <div class="text-muted small">Selesai dan simpan sebagai draf</div>
            </div>
        </div>
    </div>

    <?php if (!$hasPreview): ?>
    <!-- 3A. TAHAP 1: PANEL UNGGAH BERKAS -->
    <div class="bento-card p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
            <div>
                <h2 class="h5 fw-bold text-dark mb-1">Unggah Berkas Data Taruna</h2>
                <p class="text-muted small mb-0">Gunakan template Excel (.xlsx) atau berkas ekspor Excel (.xls).</p>
            </div>
            <a href="<?= site_url('ketarunaan/taruna/impor/template') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 36px;">
                <i class="bi bi-download"></i>
                <span>Unduh Template Excel</span>
            </a>
        </div>

        <form method="post" action="<?= site_url('ketarunaan/taruna/impor/preview') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="p-4 rounded-3 border bg-light bg-opacity-50 mb-4">
                <label for="inputBerkasImpor" class="form-label fw-semibold text-dark small mb-2">Pilih Berkas Excel (.xlsx atau .xls) <span class="text-danger">*</span></label>
                <input type="file" id="inputBerkasImpor" name="berkas_impor" accept=".xlsx,.xls" class="form-control mb-2" required>
                <div class="text-muted small">Tahun masuk &amp; angka Romawi angkatan terdeteksi otomatis (termasuk taruna tahan tingkat), dan angkatan baru dibuat otomatis bila belum ada. Maksimal 5 MB.</div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= site_url('ketarunaan/taruna') ?>" class="btn btn-light rounded-3 px-3 border fw-semibold d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" style="height: 40px;">Batal</a>
                <?php if ($can_manage): ?>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                        <i class="bi bi-search"></i>
                        <span>Periksa Berkas</span>
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php else: ?>
    <!-- 3B. TAHAP 2: PANEL PEMERIKSAAN & PRATINJAU DATA -->
    <div class="bento-card p-3 p-md-4">
        <!-- Info Berkas & Tombol Template -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-3 rounded-3 border bg-light bg-opacity-50 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success-emphasis" style="width: 40px; height: 40px;">
                    <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark"><?= esc($preview['nama_berkas'] ?? '-') ?></div>
                    <div class="text-muted small tabular-nums"><?= number_format((int) ($preview['total_baris'] ?? 0)) ?> baris data ditemukan</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 d-print-none">
                <a href="<?= site_url('ketarunaan/taruna/impor/template') ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 36px;">
                    <i class="bi bi-download"></i>
                    <span>Unduh Template Excel</span>
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Pemeriksaan -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile bg-primary-subtle border border-primary-subtle">
                    <div class="text-primary-emphasis fw-semibold small mb-1">Total Baris</div>
                    <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['total_baris'] ?? 0)) ?></div>
                    <div class="small" style="color: #475569;">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile bg-success-subtle border border-success-subtle">
                    <div class="text-success-emphasis fw-semibold small mb-1">Siap Diimpor</div>
                    <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['siap_diimpor'] ?? 0)) ?></div>
                    <div class="small" style="color: #475569;">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile bg-warning-subtle border border-warning-subtle">
                    <div class="text-warning-emphasis fw-semibold small mb-1">Duplikat</div>
                    <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['duplikat'] ?? 0)) ?></div>
                    <div class="small" style="color: #475569;">baris</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="impor-stat-tile bg-danger-subtle border border-danger-subtle">
                    <div class="text-danger-emphasis fw-semibold small mb-1">Perlu Perbaikan</div>
                    <div class="h4 fw-bold text-dark mb-0 tabular-nums"><?= number_format((int) ($preview['perlu_perbaikan'] ?? 0)) ?></div>
                    <div class="small" style="color: #475569;">baris</div>
                </div>
            </div>
        </div>

        <!-- Tabel Pratinjau Hasil Pemeriksaan Data (Desktop) -->
        <h2 class="h6 fw-bold text-dark mb-3">Pratinjau Hasil Pemeriksaan Data</h2>
        <div class="table-responsive impor-table-wrap border rounded-3 mb-3 d-none d-md-block">
            <table id="tabelPratinjauImpor" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th style="width: 88px;" aria-sort="ascending"><button type="button" class="taruna-sort-btn" data-col="0" data-type="number"><span>Baris</span><i class="bi bi-arrow-up text-primary" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="1" data-type="text"><span>Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="2" data-type="text"><span>Nama</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="3" data-type="number"><span>Angkatan</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="4" data-type="number"><span>Hasil</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="taruna-sort-btn" data-col="5" data-type="text"><span>Keterangan</span><i class="bi bi-arrow-down-up text-muted opacity-50" aria-hidden="true"></i></button></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($preview['preview_rows'] ?? []) as $row): ?>
                        <?php
                            $rowClass = '';
                            $badgeClass = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                            $hasilOrder = 3;
                            if (($row['status_cek'] ?? '') === 'duplikat') {
                                $rowClass = 'table-warning';
                                $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                $hasilOrder = 2;
                            } elseif (($row['status_cek'] ?? '') === 'tidak_valid') {
                                $rowClass = 'table-danger';
                                $badgeClass = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                                $hasilOrder = 1;
                            }
                            $angkatanTampil = (string) ($row['angkatan_label'] ?? ketarunaan_format_angkatan($row['tahun_masuk'] ?? null));
                        ?>
                        <tr class="<?= esc($rowClass) ?>">
                            <td class="tabular-nums small fw-semibold" data-sort-value="<?= (int) $row['baris'] ?>"><?= (int) $row['baris'] ?></td>
                            <td class="tabular-nums fw-bold text-dark small" data-sort-value="<?= esc((string) $row['nomor_taruna']) ?>"><?= esc($row['nomor_taruna']) ?></td>
                            <td class="small fw-medium text-dark" data-sort-value="<?= esc((string) $row['nama_lengkap']) ?>"><?= esc($row['nama_lengkap']) ?></td>
                            <td class="tabular-nums small fw-semibold" style="color: #1e40af;" data-sort-value="<?= (int) ($row['tahun_masuk'] ?? 0) ?>"><?= esc($angkatanTampil) ?></td>
                            <td data-sort-value="<?= $hasilOrder ?>">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($badgeClass) ?>">
                                    <?= esc($row['label_cek']) ?>
                                </span>
                            </td>
                            <td class="small <?= $rowClass !== '' ? 'text-dark' : 'text-muted' ?>" data-sort-value="<?= esc((string) $row['keterangan']) ?>"><?= esc($row['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Pratinjau Mobile (< 768px) -->
        <div class="d-md-none d-flex flex-column gap-2 mb-3">
            <?php foreach (($preview['preview_rows'] ?? []) as $row): ?>
                <?php
                    $badgeClass = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                    if (($row['status_cek'] ?? '') === 'duplikat') {
                        $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                    } elseif (($row['status_cek'] ?? '') === 'tidak_valid') {
                        $badgeClass = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                    }
                    $angkatanTampil = (string) ($row['angkatan_label'] ?? ketarunaan_format_angkatan($row['tahun_masuk'] ?? null));
                ?>
                <div class="p-3 rounded-3 border bg-white">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                        <div>
                            <div class="fw-bold text-dark small"><?= esc($row['nama_lengkap'] ?: '-') ?></div>
                            <div class="small tabular-nums fw-semibold" style="color: #1e40af;">Baris <?= (int) $row['baris'] ?> • <?= esc($row['nomor_taruna'] ?: '-') ?> • <?= esc($angkatanTampil) ?></div>
                        </div>
                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($badgeClass) ?>">
                            <?= esc($row['label_cek']) ?>
                        </span>
                    </div>
                    <div class="small text-muted"><?= esc($row['keterangan']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Info Banner -->
        <div class="p-3 rounded-3 d-flex align-items-center gap-2 small mb-4" style="background-color: #eff6ff; color: #1e40af;">
            <i class="bi bi-info-circle-fill"></i>
            <span>Data hasil impor disimpan <strong>sebagai draf</strong> dan memerlukan validasi. Tidak ada data yang langsung ditimpa secara otomatis.</span>
        </div>

        <!-- Baris Aksi Bawah -->
        <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top d-print-none">
            <button type="button" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 40px;" onclick="window.print()">
                <i class="bi bi-printer"></i>
                <span>Cetak / Simpan PDF</span>
            </button>

            <button type="button" class="btn btn-outline-primary rounded-3 px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile taruna-touch-ctrl" style="height: 40px;" data-bs-toggle="modal" data-bs-target="#modalGantiBerkas">
                <i class="bi bi-arrow-repeat"></i>
                <span>Ganti Berkas</span>
            </button>

            <?php if ($can_manage && (int) ($preview['siap_diimpor'] ?? 0) > 0): ?>
                <form method="post" action="<?= site_url('ketarunaan/taruna/impor/proses') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
                        <i class="bi bi-upload"></i>
                        <span>Impor <?= number_format((int) $preview['siap_diimpor']) ?> Baris Valid</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php if ($hasPreview): ?>
<!-- MODAL UNGGAH / GANTI BERKAS -->
<div class="modal fade" id="modalGantiBerkas" tabindex="-1" aria-labelledby="modalGantiBerkasLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= site_url('ketarunaan/taruna/impor/preview') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <h2 class="h6 modal-title fw-bold text-dark mb-0" id="modalGantiBerkasLabel">Unggah Berkas Data Taruna</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="modalBerkasImporInput" class="form-label text-muted small mb-2">Pilih berkas Excel (.xlsx atau .xls) untuk diperiksa otomatis.</label>
                    <input type="file" id="modalBerkasImporInput" name="berkas_impor" accept=".xlsx,.xls" class="form-control" required>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-3 px-4 border d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" data-bs-dismiss="modal" style="height: 40px;">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">Periksa Berkas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const MAX_EXCEL_BYTES = 5 * 1024 * 1024;

        function warnImpor(title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: title, text: text });
            } else {
                alert(text);
            }
        }

        function validateExcelInput(input) {
            const file = input && input.files ? input.files[0] : null;
            if (!file) return true;
            const ext = (file.name || '').split('.').pop().toLowerCase();
            if (ext !== 'xlsx' && ext !== 'xls') {
                warnImpor('Format Berkas Tidak Didukung', 'Pilih berkas Excel berformat .xlsx atau .xls.');
                input.value = '';
                return false;
            }
            if (file.size > MAX_EXCEL_BYTES) {
                warnImpor('Ukuran Berkas Terlalu Besar', 'Pilih berkas Excel maksimal 5 MB.');
                input.value = '';
                return false;
            }
            return true;
        }

        ['inputBerkasImpor', 'modalBerkasImporInput'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function () {
                    validateExcelInput(el);
                });
            }
        });

        const imporForms = document.querySelectorAll('form[action*="/ketarunaan/taruna/impor/"]');
        imporForms.forEach(function (form) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                const labelEl = btn.querySelector('span');
                if (labelEl && !btn.dataset.defaultLabel) {
                    btn.dataset.defaultLabel = labelEl.textContent;
                }
            }
            form.addEventListener('submit', function (e) {
                const fileInput = form.querySelector('input[type="file"]');
                if (fileInput && !validateExcelInput(fileInput)) {
                    e.preventDefault();
                    return;
                }
                const submitBtn = form.querySelector('button[type="submit"]');
                if (!submitBtn || submitBtn.disabled) return;
                submitBtn.disabled = true;
                const label = submitBtn.querySelector('span');
                if (label) {
                    label.textContent = form.action.indexOf('/proses') !== -1 ? 'Menyimpan...' : 'Memeriksa...';
                }
            });
        });

        window.addEventListener('pageshow', function () {
            imporForms.forEach(function (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (!submitBtn) return;
                submitBtn.disabled = false;
                const label = submitBtn.querySelector('span');
                if (label && submitBtn.dataset.defaultLabel) {
                    label.textContent = submitBtn.dataset.defaultLabel;
                }
            });
        });

        const table = document.getElementById('tabelPratinjauImpor');
        if (table) {
            const tbody = table.querySelector('tbody');
            const buttons = table.querySelectorAll('thead .taruna-sort-btn');
            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const th = btn.closest('th');
                    const colIdx = parseInt(btn.getAttribute('data-col') || '0', 10);
                    const type = btn.getAttribute('data-type') || 'text';
                    const currDir = th ? (th.getAttribute('aria-sort') || 'none') : 'none';
                    const nextDir = currDir === 'ascending' ? 'descending' : 'ascending';
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    rows.sort(function (a, b) {
                        const cellA = a.children[colIdx];
                        const cellB = b.children[colIdx];
                        const rawA = cellA ? (cellA.getAttribute('data-sort-value') ?? cellA.textContent.trim()) : '';
                        const rawB = cellB ? (cellB.getAttribute('data-sort-value') ?? cellB.textContent.trim()) : '';
                        let cmp = 0;
                        if (type === 'number') {
                            cmp = (parseFloat(rawA) || 0) - (parseFloat(rawB) || 0);
                        } else {
                            cmp = String(rawA).localeCompare(String(rawB), 'id', { numeric: true, sensitivity: 'base' });
                        }
                        return nextDir === 'ascending' ? cmp : -cmp;
                    });
                    rows.forEach(function (tr) { tbody.appendChild(tr); });
                    table.querySelectorAll('thead th[aria-sort]').forEach(function (otherTh) {
                        otherTh.setAttribute('aria-sort', 'none');
                        const icon = otherTh.querySelector('.taruna-sort-btn i');
                        if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50';
                    });
                    if (th) {
                        th.setAttribute('aria-sort', nextDir);
                        const icon = btn.querySelector('i');
                        if (icon) icon.className = nextDir === 'ascending' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary';
                    }
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>

