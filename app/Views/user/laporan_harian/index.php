<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Target Kinerja Bulanan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .num-tabular {
        font-variant-numeric: tabular-nums;
    }
    .col-rhk { min-width: 250px; }
    .col-indikator { min-width: 250px; }
    .col-target { width: 130px; min-width: 120px; }
    .col-satuan { width: 135px; min-width: 120px; }
    .col-status { width: 160px; min-width: 140px; }
    
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-bento {
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        min-width: 900px;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
    }
    .table-bento tbody td {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .table-bento tbody tr {
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .table-bento tbody tr:hover td {
        background-color: rgba(241, 245, 249, 0.75);
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    
    .input-target-val::-webkit-outer-spin-button,
    .input-target-val::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-target-val {
        -moz-appearance: textfield;
    }

    /* Table Input Focus & Micro-elevation */
    .table-bento input.form-control,
    .table-bento textarea.form-control {
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, transform 0.15s ease;
        border-radius: 8px;
    }
    .table-bento input.form-control:focus,
    .table-bento textarea.form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        background-color: #ffffff !important;
    }

    .readonly-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }

    /* Motion Design & Natural Deceleration Transitions */
    @keyframes rowSlideIn {
        0% {
            opacity: 0;
            transform: translateY(-12px) scale(0.98);
            background-color: rgba(13, 110, 253, 0.12);
        }
        60% {
            background-color: rgba(13, 110, 253, 0.06);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            background-color: transparent;
        }
    }
    .row-slide-in {
        animation: rowSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes rowSlideOut {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
            background-color: rgba(220, 53, 69, 0.08);
        }
        100% {
            opacity: 0;
            transform: translateX(18px) scale(0.96);
            background-color: rgba(220, 53, 69, 0.15);
        }
    }
    .row-slide-out {
        animation: rowSlideOut 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        pointer-events: none;
    }

    @keyframes pulseDuplicate {
        0% {
            background-color: rgba(220, 53, 69, 0.06);
            transform: translateX(0);
        }
        20% {
            background-color: rgba(220, 53, 69, 0.25);
            transform: translateX(-4px);
        }
        40% {
            transform: translateX(4px);
        }
        60% {
            background-color: rgba(220, 53, 69, 0.18);
            transform: translateX(-2px);
        }
        80% {
            transform: translateX(2px);
        }
        100% {
            background-color: rgba(220, 53, 69, 0.10);
            transform: translateX(0);
        }
    }
    .table-danger {
        animation: pulseDuplicate 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Segmented Tab Pane Transition */
    .tab-content > .tab-pane {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tab-content > .tab-pane.active {
        opacity: 1;
        transform: translateY(0);
    }

    /* Status Badge Pop */
    .status-badge {
        display: inline-flex;
        align-items: center;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }
    .status-badge:hover {
        transform: scale(1.05);
    }

    /* Tactile Buttons */
    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-tactile:hover {
        transform: translateY(-1px);
    }
    .btn-tactile:active {
        transform: scale(0.965);
    }

    /* Modal Backdrop Blur & Pop-In */
    .modal.fade .modal-dialog {
        transform: scale(0.96) translateY(-8px);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }

    /* Modal Radio Card Selection */
    .modal-radio-card {
        border: 1.5px solid #e2e8f0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .modal-radio-card:hover {
        border-color: #93c5fd;
        background-color: #f8faff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.06);
    }
    .modal-radio-card.active {
        border-color: #3b82f6 !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .row-slide-in, .row-slide-out, .table-danger, .modal.fade .modal-dialog,
        .tab-content > .tab-pane, .status-badge, .btn-tactile, .modal-radio-card,
        .table-bento tbody tr {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-bullseye text-primary me-2"></i>Target Kinerja Bulanan</h1>
            <p class="text-muted small mb-0">Rencana Hasil Kerja (RHK) dan target kuantitatif bulanan.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-calendar-month me-1"></i> <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <ul class="nav segmented-control mb-3 bento-stagger bento-stagger-1" id="targetKinerjaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= empty($staf_id_terpilih) ? 'active' : '' ?>" id="sendiri-tab" data-bs-toggle="tab" data-bs-target="#sendiri" type="button" role="tab" aria-controls="sendiri" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                <i class="bi bi-person-fill me-1"></i> Target Saya
            </button>
        </li>
        <?php if ($is_atasan): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= !empty($staf_id_terpilih) ? 'active' : '' ?>" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                <i class="bi bi-people-fill me-1"></i> Persetujuan Target Staf
            </button>
        </li>
        <?php endif; ?>
    </ul>

<datalist id="daftarSatuanStandar">
    <option value="Laporan">
    <option value="Dokumen">
    <option value="Pertemuan">
    <option value="Kegiatan">
    <option value="Mahasiswa">
    <option value="Peserta">
    <option value="Sertifikat">
    <option value="Paket Kegiatan">
    <option value="Jam Pelajaran (JP)">
    <option value="Surat Keputusan (SK)">
</datalist>

<div class="tab-content" id="targetKinerjaTabContent">

    <!-- TAB 1: TARGET KINERJA SAYA -->
    <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="sendiri" role="tabpanel" aria-labelledby="sendiri-tab">
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
            <div class="card-body p-3 p-md-4">
                <!-- Filter Bulan/Tahun -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="sendiri">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-event text-primary me-1"></i> Bulan</label>
                            <select name="bulan" class="form-select form-select-sm shadow-sm filter-select" aria-label="Pilih Bulan Target Kinerja" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-date text-primary me-1"></i> Tahun</label>
                            <input type="number" name="tahun" class="form-control form-select-sm shadow-sm filter-select num-tabular" aria-label="Input Tahun Target Kinerja" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-7 text-muted pt-sm-3 small">
                            <i class="bi bi-info-circle text-primary me-1"></i> Rancang dan kelola target kinerja bulanan Anda pada tabel di bawah.
                        </div>
                    </div>
                </form>

                <?php if ($is_locked): ?>
                    <div class="alert alert-warning mb-3 py-2.5 px-3 small rounded-4 d-flex align-items-center gap-2">
                        <i class="bi bi-lock-fill text-warning-emphasis fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Akses Terkunci.</strong> Batas pengisian tanggal <?= esc($batas_target) ?> telah terlewati.
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('laporan-harian/store') ?>" class="form-target-sendiri">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="is_editing_staf" value="0">

                    <?php
                        $allApproved = !empty($rekap_data_sendiri);
                        $hasDraft = false;
                        $hasTerkirim = false;
                        if (!empty($rekap_data_sendiri)) {
                            foreach ($rekap_data_sendiri as $row) {
                                if ($row['status_approval'] !== 'disetujui') {
                                    $allApproved = false;
                                }
                                if (($row['status'] ?? '') === 'draft') {
                                    $hasDraft = true;
                                }
                                if (($row['status'] ?? '') === 'terkirim') {
                                    $hasTerkirim = true;
                                }
                            }
                        }
                    ?>

                    <?php if ($hasDraft && !$allApproved): ?>
                        <div class="alert alert-warning mb-3 shadow-sm py-2.5 px-3 border border-warning-subtle small rounded-4 d-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square fs-5 text-warning-emphasis flex-shrink-0"></i>
                            <div>
                                <strong>Perlu Revisi.</strong> Perbaiki rincian target di bawah, lalu klik <strong>Ajukan Target</strong>.
                            </div>
                        </div>
                    <?php elseif ($hasTerkirim && !$allApproved): ?>
                        <div class="alert alert-info mb-3 shadow-sm py-2.5 px-3 border border-info-subtle small rounded-4 d-flex align-items-center gap-2">
                            <i class="bi bi-hourglass-split fs-5 text-primary flex-shrink-0"></i>
                            <div>
                                <strong>Menunggu Persetujuan.</strong> Target kinerja telah dikirim ke atasan langsung dan menunggu persetujuan.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- TABLE TOOLBAR HEADER -->
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 small fw-semibold">
                                <i class="bi bi-list-check text-primary me-1"></i> Rincian Target <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                            </span>
                        </div>
                        <?php if (!$is_locked && !$allApproved): ?>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold shadow-none d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalSalinTarget" title="Salin target dan sasaran kinerja dari periode bulan sebelumnya atau periode lainnya">
                                <i class="bi bi-copy text-primary"></i> Salin dari Bulan Lain
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="table-responsive mb-3 border rounded-4 shadow-sm bg-white">
                        <table class="table table-bordered align-middle table-hover mb-0 table-bento tabel-target">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                    <th class="col-indikator">Indikator Kinerja Individu</th>
                                    <th class="col-target text-center">Target <?= esc($nama_bulan) ?></th>
                                    <th class="col-satuan text-center">Satuan</th>
                                    <th class="col-status text-center">Status</th>
                                    <th style="width: 55px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php if (!empty($rekap_data_sendiri)): ?>
                                    <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                        <?php $isRowLocked = ($is_locked || $row['status_approval'] === 'disetujui'); ?>
                                        <tr>
                                            <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                            <td class="nomor-baris text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                            <td>
                                                <textarea name="sasaran_program[]" class="form-control form-control-sm" rows="2" placeholder="Sasaran Program Unit..." aria-label="Sasaran Program Unit baris <?= $index + 1 ?>" <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['sasaran_program']) ?></textarea>
                                            </td>
                                            <td>
                                                <textarea name="indikator_kinerja[]" class="form-control form-control-sm" rows="2" placeholder="Indikator / RHK..." aria-label="Indikator Kinerja Individu baris <?= $index + 1 ?>" <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['indikator_kinerja']) ?></textarea>
                                            </td>
                                            <td>
                                                <input type="number" step="any" min="0.0001" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val" placeholder="Target" aria-label="Target Kuantitatif baris <?= $index + 1 ?>" value="<?= isset($row['target_bulanan']) && $row['target_bulanan'] !== null ? (float)$row['target_bulanan'] : '' ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="text" name="satuan[]" class="form-control form-control-sm text-center input-satuan-val" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Target baris <?= $index + 1 ?>" value="<?= esc($row['satuan']) ?>" title="<?= esc($row['satuan']) ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td class="text-center">
                                                <?php if($row['status_approval'] === 'disetujui'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                                <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-pencil-square me-1"></i> Draf (Revisi)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!$isRowLocked): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="<?= esc($row['id']) ?>" title="Hapus Target" aria-label="Hapus target baris <?= $index + 1 ?>" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif (!$is_locked): ?>
                                    <!-- Baris Kosong Default -->
                                    <tr>
                                        <input type="hidden" name="laporan_id[]" value="">
                                        <td class="nomor-baris text-center fw-bold text-muted">1</td>
                                        <td><textarea name="sasaran_program[]" class="form-control form-control-sm" rows="2" placeholder="Sasaran Program Unit..." aria-label="Sasaran Program Unit baris 1"></textarea></td>
                                        <td><textarea name="indikator_kinerja[]" class="form-control form-control-sm" rows="2" placeholder="Indikator / RHK..." aria-label="Indikator Kinerja Individu baris 1"></textarea></td>
                                        <td><input type="number" step="any" min="0.0001" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val" placeholder="Target" aria-label="Target Kuantitatif baris 1"></td>
                                        <td><input type="text" name="satuan[]" class="form-control form-control-sm text-center input-satuan-val" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Target baris 1"></td>
                                        <td class="text-center"><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-pencil me-1"></i> Draf Baru</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="" title="Hapus Target" aria-label="Hapus target baris 1" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data target kinerja untuk bulan ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!$is_locked && !$allApproved): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4 btn-action-container flex-wrap gap-2 bento-stagger bento-stagger-3">
                        <button type="button" class="btn btn-primary btn-tambah-baris btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold"><i class="bi bi-plus-circle me-1.5"></i> Tambah Target</button>
                        <div class="d-flex gap-2 btn-group-mobile">
                            <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold"><i class="bi bi-cloud-arrow-up me-1.5"></i> Simpan Draf</button>
                            <button type="submit" class="btn btn-success btn-tactile rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-send me-1.5"></i> Ajukan Target</button>
                        </div>
                    </div>
                    <?php elseif ($allApproved): ?>
                    <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2 py-2.5 px-3 small rounded-4 shadow-sm bento-stagger bento-stagger-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                            <div>Semua target kinerja bulan ini telah <strong>disetujui</strong> dan dikunci oleh atasan.</div>
                        </div>
                        <?php if (hasRole('admin')): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-tactile rounded-pill px-3 fw-bold shadow-sm btn-batal-approve-target"
                            data-staf-id="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>"
                            data-bulan="<?= esc($bulan_terpilih) ?>"
                            data-tahun="<?= esc($tahun_terpilih) ?>"
                            title="Batalkan persetujuan target bulan ini">
                            <i class="bi bi-x-circle-fill me-1"></i> Batalkan Persetujuan (Admin)
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div> <!-- End Tab Sendiri -->

    <!-- TAB 2: PERSETUJUAN TARGET STAF -->
    <?php if ($is_atasan): ?>
    <div class="tab-pane fade <?= !empty($staf_id_terpilih) ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">
        <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
            <div class="card-body p-3 p-md-4">
                <!-- Filter Form untuk Staf -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="staf">
                    <div class="row g-2 align-items-end">
                        <?php if (!empty($is_super) && !empty($daftar_unit)): ?>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-building text-primary me-1"></i> Unit Kerja</label>
                            <select name="unit_kerja" class="form-select form-select-sm shadow-sm filter-select" aria-label="Pilih Unit Kerja" onchange="this.form.submit()">
                                <option value="">-- Semua Unit Kerja --</option>
                                <?php foreach ($daftar_unit as $u): ?>
                                    <option value="<?= esc($u) ?>" <?= ($unit_kerja_terpilih == $u) ? 'selected' : '' ?>>
                                        <?= esc($u) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="<?= (!empty($is_super) && !empty($daftar_unit)) ? 'col-md-4' : 'col-md-5' ?>">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-person-badge text-primary me-1"></i> Pilih Staf</label>
                            <select name="staf_id" class="form-select form-select-sm shadow-sm filter-select" aria-label="Pilih Staf" onchange="this.form.submit()">
                                <option value="">-- Pilih Staf --</option>
                                <?php foreach ($daftar_staf as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($s['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                        <?= esc($s['nama_lengkap']) ?> - <?= esc($s['jabatan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-event text-primary me-1"></i> Bulan</label>
                            <select name="bulan" class="form-select form-select-sm shadow-sm filter-select" aria-label="Pilih Bulan Target Staf" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-date text-primary me-1"></i> Tahun</label>
                            <input type="number" name="tahun" class="form-control form-select-sm shadow-sm filter-select num-tabular" aria-label="Input Tahun Target Staf" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>

                <?php if (!empty($staf_id_terpilih) && $is_penyetuju): ?>
                    <?php if (empty($rekap_data_staf)): ?>
                        <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                            <div class="opacity-50 mb-3"><i class="bi bi-folder-x fs-1 text-muted"></i></div>
                            <h6 class="fw-bold text-dark">Target Belum Dibuat</h6>
                            <p class="text-muted small mb-0">Staf yang dipilih belum menyusun target kinerja bulanan untuk periode <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong>.</p>
                        </div>
                    <?php else: ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
                            <h6 class="fw-bold mb-0 text-dark small"><i class="bi bi-list-check text-primary me-1.5"></i>Target Kinerja Staf</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Periksa dan setujui target kinerja staf di bawah ini.</small>
                        </div>

                        <?php
                            $allApprovedStaf = !empty($rekap_data_staf);
                            if (!empty($rekap_data_staf)) {
                                foreach ($rekap_data_staf as $row) {
                                    if ($row['status_approval'] !== 'disetujui') {
                                        $allApprovedStaf = false;
                                        break;
                                    }
                                }
                            }
                        ?>

                        <form action="<?= site_url('laporan-harian/store') ?>" method="POST" class="form-target-staf">
                            <?= csrf_field() ?>
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="is_editing_staf" value="1">
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">

                            <div class="table-responsive mb-3 border rounded-4 shadow-sm bg-white">
                                <table class="table table-bordered align-middle table-hover mb-0 table-bento tabel-target">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                            <th class="col-indikator">Indikator Kinerja Individu</th>
                                            <th class="col-target text-center">Target</th>
                                            <th class="col-satuan text-center">Satuan</th>
                                            <th class="col-status text-center">Status</th>
                                            <th style="width: 55px; display: none;" class="text-center col-aksi-staf">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php $isStafApproved = ($row['status_approval'] === 'disetujui'); ?>
                                            <tr>
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                <td class="nomor-baris text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td>
                                                    <textarea name="sasaran_program[]" class="form-control form-control-sm staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" placeholder="Sasaran Program Unit..." aria-label="Sasaran Program Unit Staf baris <?= $index + 1 ?>" required readonly><?= esc($row['sasaran_program']) ?></textarea>
                                                </td>
                                                <td>
                                                    <textarea name="indikator_kinerja[]" class="form-control form-control-sm staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" placeholder="Indikator / RHK..." aria-label="Indikator Kinerja Individu Staf baris <?= $index + 1 ?>" required readonly><?= esc($row['indikator_kinerja']) ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0.0001" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary staf-input input-target-val <?= $isStafApproved ? 'locked-approved' : '' ?>" aria-label="Target Kuantitatif Staf baris <?= $index + 1 ?>" value="<?= (float)$row['target_bulanan'] ?>" required readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="satuan[]" class="form-control form-control-sm text-center staf-input input-satuan-val <?= $isStafApproved ? 'locked-approved' : '' ?>" list="daftarSatuanStandar" aria-label="Satuan Target Staf baris <?= $index + 1 ?>" value="<?= esc($row['satuan']) ?>" title="<?= esc($row['satuan']) ?>" required readonly>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($row['status_approval'] === 'disetujui'): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                                    <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-pencil me-1"></i> Draf</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center col-aksi-staf" style="display: none;">
                                                    <?php if (!$isStafApproved): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-hapus-staf" data-id="<?= esc($row['id']) ?>" title="Hapus Target" aria-label="Hapus target staf baris <?= $index + 1 ?>" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (!$allApprovedStaf): ?>
                            <div class="d-flex justify-content-between align-items-center mt-4 btn-action-container flex-wrap gap-2 bento-stagger bento-stagger-3">
                                <div>
                                    <button type="button" id="btnTambahTargetStaf" class="btn btn-primary btn-tambah-baris-staf btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold" style="display: none;">
                                        <i class="bi bi-plus-circle me-1.5"></i> Tambah Target
                                    </button>
                                </div>
                                <div class="d-flex gap-2 btn-group-mobile">
                                    <button type="submit" id="btnApproveAll" formaction="<?= site_url('laporan-harian/approve-all') ?>" class="btn btn-success btn-tactile rounded-pill shadow-sm px-4 py-2 fw-bold">
                                        <i class="bi bi-check-all me-1.5"></i> Setujui Semua
                                    </button>
                                    <button type="button" id="btnEditStaf" class="btn btn-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-bold">
                                        <i class="bi bi-pencil-square me-1.5"></i> Edit Target
                                    </button>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2 py-2.5 px-3 small rounded-4 shadow-sm bento-stagger bento-stagger-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                                    <div>Target kinerja staf bulan ini telah <strong>disetujui</strong>.</div>
                                </div>
                                <?php if (hasRole('admin')): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tactile rounded-pill px-3 fw-bold shadow-sm btn-batal-approve-target"
                                    data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                    data-bulan="<?= esc($bulan_terpilih) ?>"
                                    data-tahun="<?= esc($tahun_terpilih) ?>"
                                    title="Batalkan persetujuan target bulanan staf">
                                    <i class="bi bi-x-circle-fill me-1"></i> Batalkan Persetujuan (Admin)
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card bg-light border-0 rounded-4 p-5 text-center my-3 bento-stagger bento-stagger-2">
                        <div class="opacity-50 mb-3"><i class="bi bi-people fs-1 text-primary"></i></div>
                        <h6 class="fw-bold text-dark">Pilih Staf</h6>
                        <p class="text-muted small mb-0">Silakan pilih nama staf pada pilihan dropdown di atas untuk memeriksa dan menyetujui target kinerjanya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- MODAL SALIN TARGET DARI PERIODE LAIN -->
<?php
    $defaultBulanSumber = ($bulan_terpilih > 1) ? ($bulan_terpilih - 1) : 12;
    $defaultTahunSumber = ($bulan_terpilih > 1) ? $tahun_terpilih : ($tahun_terpilih - 1);
?>
<div class="modal fade" id="modalSalinTarget" tabindex="-1" aria-labelledby="modalSalinTargetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-copy fs-6"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalSalinTargetLabel">Salin Target Kinerja</h6>
                        <p class="text-muted small mb-0" style="font-size: 0.75rem;">Duplikasi target & sasaran dari periode pilihan Anda</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2.5 px-3 small rounded-3 mb-3 d-flex align-items-start gap-2 border-0 bg-info-subtle text-info-emphasis">
                    <i class="bi bi-info-circle-fill text-info mt-0.5 flex-shrink-0"></i>
                    <div>
                        Target yang disalin akan dimasukkan sebagai <strong>Draf Baru</strong> ke bulan <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong> sehingga dapat Anda periksa dan sesuaikan sebelum disimpan atau diajukan.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small mb-1.5" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                        <i class="bi bi-calendar-range text-primary me-1"></i> PILIH PERIODE SUMBER
                    </label>
                    <div class="row g-2">
                        <div class="col-7">
                            <label for="salinBulanSumber" class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Bulan Sumber</label>
                            <select id="salinBulanSumber" class="form-select form-select-sm shadow-sm rounded-3">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($defaultBulanSumber == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="salinTahunSumber" class="form-label text-muted small mb-1" style="font-size: 0.7rem;">Tahun Sumber</label>
                            <input type="number" id="salinTahunSumber" class="form-control form-control-sm shadow-sm rounded-3 num-tabular" value="<?= esc($defaultTahunSumber) ?>" min="2020" max="2099">
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold text-dark small mb-1.5" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                        <i class="bi bi-layers text-primary me-1"></i> METODE PENYALINAN
                    </label>
                    <div class="d-flex flex-column gap-2">
                        <label class="modal-radio-card d-flex align-items-start gap-3 p-3 rounded-3 bg-white position-relative active" for="modeSalinReplace">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="modeSalinTarget" id="modeSalinReplace" value="replace" checked>
                            <div>
                                <strong class="text-dark d-block">Gantikan Baris Tabel Saat Ini</strong>
                                <span class="text-muted small" style="font-size: 0.75rem;">Mengganti seluruh baris target yang ada di tabel dengan target dari periode sumber.</span>
                            </div>
                        </label>
                        <label class="modal-radio-card d-flex align-items-start gap-3 p-3 rounded-3 bg-white position-relative" for="modeSalinAppend">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="modeSalinTarget" id="modeSalinAppend" value="append">
                            <div>
                                <strong class="text-dark d-block">Tambahkan ke Bawah Baris Saat Ini</strong>
                                <span class="text-muted small" style="font-size: 0.75rem;">Menyisipkan target dari periode sumber di bawah target yang sudah ada tanpa menghapusnya.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2.5 px-4 justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnEksekusiSalinTarget" class="btn btn-primary btn-sm btn-tactile rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-arrow-down-left-square me-1.5"></i> Ambil & Salin Target
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        
        // Helper untuk menyinkronkan token CSRF ke seluruh form & meta tag di halaman
        function updateAllCsrfTokens(newHash) {
            if (!newHash) return;
            const csrfTokenName = '<?= csrf_token() ?>';
            $('input[name="' + csrfTokenName + '"]').val(newHash);
            $('input[name="csrf_test_name"]').val(newHash);
            $('meta[name="csrf-token"]').attr('content', newHash);
        }

        // Cegah perubahan nilai angka secara tidak sengaja saat pengguna scrolling halaman dengan mouse wheel
        $(document).on('wheel', 'input[type="number"]', function (e) {
            $(this).blur();
        });

        // Fungsi Tambah Baris untuk Form Target Saya dengan Fluid Animation
        $('.btn-tambah-baris').on('click', function() {
            const tabel = $(this).closest('form').find('.tabel-target tbody');
            const rowPertama = tabel.find('tr:first').clone();
            
            rowPertama.removeClass('row-slide-out table-danger').addClass('row-slide-in');
            rowPertama.find('input[name="laporan_id[]"]').val('');
            rowPertama.find('input[type="number"]').val('');
            rowPertama.find('input[type="text"]').val('').attr('title', '');
            rowPertama.find('textarea').val('');
            rowPertama.find('textarea').removeAttr('readonly');
            rowPertama.find('input').removeAttr('readonly');
            
            rowPertama.find('.status-badge').removeClass('bg-success-subtle text-success bg-warning-subtle text-warning-emphasis bg-secondary-subtle text-secondary').addClass('bg-primary-subtle text-primary border border-primary-subtle').html('<i class="bi bi-pencil me-1"></i> Draf Baru');
            rowPertama.find('.hapus-baris').attr('data-id', '').show();
            
            tabel.append(rowPertama);
            updateRowNumbers(tabel);

            // Auto focus ke sasaran program dengan smooth micro-delay
            setTimeout(() => {
                rowPertama.find('textarea[name="sasaran_program[]"]').focus();
            }, 80);
        });

        // Dynamic title update saat mengetik satuan agar teks panjang selalu terbaca saat di-hover
        $(document).on('input change', '.input-satuan-val', function() {
            $(this).attr('title', $(this).val());
        });

        function updateRowNumbers(tabel) {
            tabel.find('tr').each(function(index) {
                let rowNum = index + 1;
                $(this).find('.nomor-baris').text(rowNum);
                $(this).find('textarea[name="sasaran_program[]"]').attr('aria-label', 'Sasaran Program Unit baris ' + rowNum);
                $(this).find('textarea[name="indikator_kinerja[]"]').attr('aria-label', 'Indikator Kinerja Individu baris ' + rowNum);
                $(this).find('input[name="target_bulanan[]"]').attr('aria-label', 'Target Kuantitatif baris ' + rowNum);
                $(this).find('input[name="satuan[]"]').attr('aria-label', 'Satuan Target baris ' + rowNum);
                $(this).find('.hapus-baris').attr('aria-label', 'Hapus target baris ' + rowNum);
            });
        }

        // Fungsi Reset Baris ke Kosong
        function resetRowToEmpty(row) {
            row.find('input[name="laporan_id[]"]').val('');
            row.find('input[type="number"]').val('');
            row.find('input[type="text"]').val('');
            row.find('textarea').val('');
            row.find('.status-badge').removeClass('bg-success-subtle text-success bg-warning-subtle text-warning-emphasis bg-secondary-subtle text-secondary').addClass('bg-primary-subtle text-primary border border-primary-subtle').html('<i class="bi bi-pencil me-1"></i> Draf Baru');
            row.find('.hapus-baris').attr('data-id', '');
        }

        // Fungsi Hapus Baris dengan Smooth Collapse & Slide-out
        $(document).on('click', '.hapus-baris', function() {
            const tabel = $(this).closest('tbody');
            const row = $(this).closest('tr');
            let idLaporan = row.find('input[name="laporan_id[]"]').val() || $(this).attr('data-id');

            const animateRemoveRow = function() {
                row.addClass('row-slide-out');
                setTimeout(() => {
                    if (tabel.find('tr').length > 1) {
                        row.remove();
                        updateRowNumbers(tabel);
                    } else {
                        row.removeClass('row-slide-out');
                        resetRowToEmpty(row);
                    }
                }, 220);
            };

            if (idLaporan && idLaporan.trim() !== '') {
                const doDelete = function() {
                    let csrfTokenName = '<?= csrf_token() ?>';
                    let csrfHash = $('input[name="' + csrfTokenName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';
                    
                    let postData = { id: idLaporan };
                    postData[csrfTokenName] = csrfHash;

                    $.ajax({
                        url: '<?= site_url('laporan-harian/hapus') ?>',
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.csrf_hash) {
                                updateAllCsrfTokens(response.csrf_hash);
                            }
                            if (response.success) {
                                animateRemoveRow();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Target Terhapus',
                                        text: response.message || 'Target RHK berhasil dihapus.',
                                        timer: (response.affected_logs && response.affected_logs > 0) ? 3500 : 1500,
                                        showConfirmButton: (response.affected_logs && response.affected_logs > 0)
                                    });
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal', response.message || 'Gagal menghapus data.', 'error');
                                } else {
                                    alert(response.message || 'Gagal menghapus data.');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Hapus Error:', xhr.responseText);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.', 'error');
                            } else {
                                alert('Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Target Bulanan?',
                        html: '<p class="mb-2">Target RHK ini akan dihapus dari sistem.</p><p class="small text-muted mb-0"><i class="bi bi-info-circle text-primary me-1"></i> Jika target ini pernah digunakan pada laporan harian, pilihan target kegiatan tersebut akan dikosongkan dan statusnya kembali menjadi <strong>Draf</strong> agar targetnya dapat disesuaikan kembali.</p>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Target',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDelete();
                        }
                    });
                } else {
                    if (confirm('Hapus target bulanan ini?\n\nJika target ini pernah digunakan pada laporan harian, status laporan tersebut akan kembali menjadi draf.')) {
                        doDelete();
                    }
                }
            } else {
                animateRemoveRow();
            }
        });

        // Fungsi Edit Staf Target
        $('#btnEditStaf').on('click', function(e) {
            const isEditing = $(this).attr('type') === 'submit';
            if (!isEditing) {
                e.preventDefault(); // Mencegah submit form saat pertama kali klik (mode ubah ke submit)
                
                // Berubah ke mode edit
                let unlockCount = 0;
                $('.form-target-staf .staf-input').each(function() {
                    if (!$(this).hasClass('locked-approved')) {
                        $(this).removeAttr('readonly');
                        unlockCount++;
                    }
                });
                
                if (unlockCount > 0) {
                    // Ada yang bisa diedit
                    $(this).attr('type', 'submit');
                    $(this).removeClass('btn-primary').addClass('btn-warning text-dark');
                    $(this).html('<i class="bi bi-check-circle me-1.5"></i> Simpan & Setujui');
                    $('#btnApproveAll').hide(); // Sembunyikan tombol approve all saat mode edit
                    
                    // Tampilkan tombol Tambah Target dan kolom Aksi Hapus di tabel staf
                    $('#btnTambahTargetStaf').fadeIn(200);
                    $('.form-target-staf .col-aksi-staf').fadeIn(200);
                    $('.form-target-staf .btn-hapus-staf').css('display', 'inline-flex');

                    // Focus ke input pertama yang terbuka
                    $('.form-target-staf .staf-input:not(.locked-approved)').first().focus();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Informasi', 'Semua target sudah disetujui dan tidak dapat diedit lagi.', 'info');
                    } else {
                        alert('Semua target sudah disetujui dan tidak dapat diedit lagi.');
                    }
                }
            }
        });

        // Fungsi Tambah Baris untuk Form Target Staf (Mode Edit Atasan)
        $(document).on('click', '.btn-tambah-baris-staf', function() {
            const tabel = $(this).closest('form').find('.tabel-target tbody');
            const rowPertama = tabel.find('tr:first').clone();
            
            rowPertama.removeClass('row-slide-out table-danger').addClass('row-slide-in');
            rowPertama.find('input[name="laporan_id[]"]').val('');
            rowPertama.find('input[type="number"]').val('').removeAttr('readonly').removeClass('locked-approved');
            rowPertama.find('input[type="text"]').val('').attr('title', '').removeAttr('readonly').removeClass('locked-approved');
            rowPertama.find('textarea').val('').removeAttr('readonly').removeClass('locked-approved');
            
            rowPertama.find('.status-badge').removeClass('bg-success-subtle text-success bg-warning-subtle text-warning-emphasis bg-secondary-subtle text-secondary').addClass('bg-primary-subtle text-primary border border-primary-subtle').html('<i class="bi bi-pencil me-1"></i> Draf Baru');
            rowPertama.find('.col-aksi-staf').show();
            rowPertama.find('.hapus-baris').attr('data-id', '').css('display', 'inline-flex');
            
            tabel.append(rowPertama);
            updateRowNumbers(tabel);

            // Auto focus ke sasaran program dengan smooth micro-delay
            setTimeout(() => {
                rowPertama.find('textarea[name="sasaran_program[]"]').focus();
            }, 80);
        });

        // Helper untuk memeriksa duplikasi pasangan RHK & Indikator Kinerja di dalam tabel
        function checkTableDuplicates(formEl) {
            let seen = {};
            let duplicateFound = false;
            let dupInfo = null;

            // Reset highlight merah sebelumnya
            formEl.find('.tabel-target tbody tr').removeClass('table-danger');

            formEl.find('.tabel-target tbody tr').each(function(idx) {
                let sasaran = ($(this).find('textarea[name="sasaran_program[]"]').val() || '').trim();
                let indikator = ($(this).find('textarea[name="indikator_kinerja[]"]').val() || '').trim();

                if (sasaran !== '' || indikator !== '') {
                    let key = (sasaran + '|||' + indikator).toLowerCase();
                    if (seen[key] !== undefined) {
                        duplicateFound = true;
                        dupInfo = {
                            firstRow: seen[key] + 1,
                            secondRow: idx + 1,
                            sasaran: sasaran,
                            indikator: indikator
                        };
                        $(this).addClass('table-danger');
                        formEl.find('.tabel-target tbody tr').eq(seen[key]).addClass('table-danger');
                        return false; // Hentikan loop
                    }
                    seen[key] = idx;
                }
            });

            return { hasDuplicate: duplicateFound, info: dupInfo };
        }

        // Flag konfirmasi dialog Ajukan Target
        let isTargetSubmitConfirmed = false;

        // Validasi saat submit "Ajukan Target" (Form submit biasa dengan konfirmasi SweetAlert)
        $('.form-target-sendiri').on('submit', function(e) {
            if (isTargetSubmitConfirmed) {
                return true;
            }

            const formEl = $(this);
            let isValid = true;
            let hasAtLeastOne = false;

            let isTargetPositive = true;
            let invalidRow = null;

            formEl.find('.tabel-target tbody tr').each(function(idx) {
                let sasaran = $(this).find('textarea[name="sasaran_program[]"]').val().trim();
                let indikator = $(this).find('textarea[name="indikator_kinerja[]"]').val().trim();
                let target = $(this).find('input[name="target_bulanan[]"]').val().trim();
                let satuan = $(this).find('input[name="satuan[]"]').val().trim();

                // Jika salah satu kolom di baris ini terisi
                if (sasaran !== '' || indikator !== '' || target !== '' || satuan !== '') {
                    hasAtLeastOne = true;
                    if (sasaran === '' || indikator === '' || target === '' || satuan === '') {
                        isValid = false;
                    }
                    if (target !== '') {
                        let targetNum = parseFloat(target.replace(',', '.'));
                        if (isNaN(targetNum) || targetNum <= 0) {
                            isValid = false;
                            isTargetPositive = false;
                            invalidRow = idx + 1;
                        }
                    }
                }
            });

            if (!hasAtLeastOne) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Target Kosong', 'Silakan isi minimal satu rincian target kinerja sebelum mengajukan ke atasan.', 'warning');
                } else {
                    alert('Silakan isi minimal satu rincian target kinerja sebelum mengajukan ke atasan.');
                }
                return false;
            }

            if (!isTargetPositive) {
                e.preventDefault();
                let errMsg = invalidRow ? `Nilai Target Bulanan pada Baris ke-${invalidRow} harus lebih besar dari 0 (tidak boleh 0 atau negatif).` : 'Nilai Target Bulanan harus lebih besar dari 0 (tidak boleh 0 atau negatif).';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Nilai Target Tidak Valid', errMsg, 'warning');
                } else {
                    alert(errMsg);
                }
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Data Belum Lengkap', 'Pastikan semua kolom (Sasaran, Indikator, Target, Satuan) terisi dengan benar sebelum diajukan.', 'warning');
                } else {
                    alert('Pastikan semua kolom (Sasaran, Indikator, Target, Satuan) terisi dengan benar sebelum diajukan.');
                }
                return false;
            }

            // Validasi duplikasi target
            const dupCheck = checkTableDuplicates(formEl);
            if (dupCheck.hasDuplicate) {
                e.preventDefault();
                const d = dupCheck.info;
                const msg = `Terdapat duplikasi RHK & Indikator Kinerja pada <strong>Baris ke-${d.firstRow}</strong> dan <strong>Baris ke-${d.secondRow}</strong>.<br><br>Harap sesuaikan atau gabungkan indikator agar tidak tercatat ganda.`;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Target Duplikat Terdeteksi',
                        html: msg,
                        confirmButtonText: 'Periksa Baris Tersebut'
                    });
                } else {
                    alert(`Terdapat duplikasi target pada Baris ke-${d.firstRow} dan Baris ke-${d.secondRow}.`);
                }
                return false;
            }

            e.preventDefault();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Ajukan Target Kinerja?',
                    html: `Rincian target kinerja untuk periode <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong> akan diajukan ke atasan langsung untuk diperiksa dan disetujui.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-send-fill me-1"></i> Ya, Ajukan Sekarang',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isTargetSubmitConfirmed = true;
                        const submitBtn = formEl.find('button[type="submit"]');
                        if (submitBtn.length) {
                            submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengajukan...').prop('disabled', true);
                        }
                        $('#btnSimpanSementara').prop('disabled', true);
                        formEl[0].submit();
                    }
                });
            } else {
                if (confirm('Ajukan target kinerja bulanan ini ke atasan langsung?')) {
                    isTargetSubmitConfirmed = true;
                    const submitBtn = formEl.find('button[type="submit"]');
                    if (submitBtn.length) {
                        submitBtn.prop('disabled', true);
                    }
                    $('#btnSimpanSementara').prop('disabled', true);
                    formEl[0].submit();
                }
            }
            return false;
        });

        // Validasi saat submit "Persetujuan Target Staf"
        $('.form-target-staf').on('submit', function() {
            $('#btnApproveAll, #btnEditStaf').prop('disabled', true);
            let activeBtn = $(document.activeElement);
            if (activeBtn.is('#btnApproveAll')) {
                activeBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyetujui...');
            }
        });

        // Fungsi Simpan Sementara (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            let btn = $(this);
            let originalText = btn.html();
            let form = $('.form-target-sendiri');

            // Validasi duplikasi target sebelum simpan draf
            const dupCheck = checkTableDuplicates(form);
            if (dupCheck.hasDuplicate) {
                const d = dupCheck.info;
                const msg = `Terdapat duplikasi RHK & Indikator Kinerja pada <strong>Baris ke-${d.firstRow}</strong> dan <strong>Baris ke-${d.secondRow}</strong>.<br><br>Harap sesuaikan atau gabungkan indikator agar tidak tercatat ganda.`;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Target Duplikat Terdeteksi',
                        html: msg,
                        confirmButtonText: 'Periksa Baris Tersebut'
                    });
                } else {
                    alert(`Terdapat duplikasi target pada Baris ke-${d.firstRow} dan Baris ke-${d.secondRow}.`);
                }
                return;
            }

            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: '<?= site_url('laporan-harian/store') ?>',
                type: 'POST',
                data: form.serialize() + '&action=draft',
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        updateAllCsrfTokens(response.csrf_hash);
                    }

                    if (response.success) {
                        btn.html('<i class="bi bi-check-lg me-2"></i> Tersimpan Draf').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        
                        if (response.new_ids) {
                            for (let index in response.new_ids) {
                                let row = form.find('.tabel-target tbody tr').eq(index);
                                if (row.length) {
                                    row.find('input[name="laporan_id[]"]').val(response.new_ids[index]);
                                    row.find('.hapus-baris').attr('data-id', response.new_ids[index]);
                                }
                            }
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan Draf',
                                text: response.message || 'Target Bulanan berhasil disimpan sementara.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }

                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary').prop('disabled', false);
                        }, 2000);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal menyimpan target.', 'error');
                        } else {
                            alert('Gagal menyimpan: ' + (response.message || 'Error tidak diketahui'));
                        }
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.', 'error');
                    } else {
                        alert('Terjadi kesalahan jaringan atau server. Silakan coba lagi.');
                    }
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // =============================================
        // [SUPERADMIN] Batalkan Persetujuan Target Bulanan
        // =============================================
        $(document).on('click', '.btn-batal-approve-target', function(e) {
            e.preventDefault();
            const stafId    = $(this).data('staf-id');
            const bulan     = $(this).data('bulan');
            const tahun     = $(this).data('tahun');
            const csrfName  = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';

            if (!stafId || !bulan || !tahun) {
                alert('Parameter staf_id, bulan, atau tahun tidak valid.');
                return;
            }

            function executeCancel() {
                $.ajax({
                    url: '<?= site_url('laporan-harian/batal-approve') ?>',
                    type: 'POST',
                    data: {
                        staf_id: stafId,
                        bulan: bulan,
                        tahun: tahun,
                        [csrfName]: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.csrf_hash) {
                            updateAllCsrfTokens(response.csrf_hash);
                        }
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                            } else {
                                alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText || error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
                        } else {
                            alert('Terjadi kesalahan jaringan atau server.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batalkan Persetujuan Target?',
                    html: `Persetujuan target bulanan staf akan dibatalkan agar staf dapat merevisi target.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-x-circle-fill me-1"></i> Ya, Batalkan Persetujuan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeCancel();
                    }
                });
            } else {
                if (confirm('Batalkan persetujuan target bulanan ini?')) {
                    executeCancel();
                }
            }
        });

        // =============================================
        // SALIN TARGET KINERJA DARI PERIODE PILIHAN USER (POST)
        // =============================================
        $('#btnEksekusiSalinTarget').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            const bulanSumber = $('#salinBulanSumber').val();
            const tahunSumber = $('#salinTahunSumber').val();
            const modeSalin = $('input[name="modeSalinTarget"]:checked').val() || 'replace';
            const tabel = $('.form-target-sendiri .tabel-target tbody');
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfHash = $('input[name="' + csrfTokenName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';

            if (!bulanSumber || !tahunSumber) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Peringatan', 'Silakan pilih bulan dan tahun sumber yang valid.', 'warning');
                } else {
                    alert('Silakan pilih bulan dan tahun sumber yang valid.');
                }
                return;
            }

            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengambil Data...').prop('disabled', true);

            let postData = {
                bulan: bulanSumber,
                tahun: tahunSumber
            };
            postData[csrfTokenName] = csrfHash;

            $.ajax({
                url: '<?= site_url('laporan-harian/get-previous-targets') ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    btn.html(originalText).prop('disabled', false);
                    if (response.csrf_hash) {
                        updateAllCsrfTokens(response.csrf_hash);
                    }

                    if (response.status === 'empty') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Data Tidak Ditemukan',
                                text: response.message || 'Tidak ada data target pada periode tersebut.'
                            });
                        } else {
                            alert(response.message);
                        }
                        return;
                    }

                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        // Tutup modal
                        const modalEl = document.getElementById('modalSalinTarget');
                        if (modalEl) {
                            const modalObj = bootstrap.Modal.getInstance(modalEl);
                            if (modalObj) modalObj.hide();
                        }

                        // Cek apakah tabel saat ini hanya memiliki 1 baris dan semua isiannya kosong
                        const barisSaatIni = tabel.find('tr');
                        let isCurrentTableEmpty = false;
                        if (barisSaatIni.length === 1) {
                            const firstRow = barisSaatIni.first();
                            const sasaran = (firstRow.find('textarea[name="sasaran_program[]"]').val() || '').trim();
                            const indikator = (firstRow.find('textarea[name="indikator_kinerja[]"]').val() || '').trim();
                            const targetVal = (firstRow.find('input[name="target_bulanan[]"]').val() || '').trim();
                            if (sasaran === '' && indikator === '' && targetVal === '') {
                                isCurrentTableEmpty = true;
                            }
                        }

                        // Koleksi target yang sudah ada di tabel saat ini untuk mencegah duplikasi jika mode append
                        const existingTargetKeys = new Set();
                        if (modeSalin === 'append' && !isCurrentTableEmpty) {
                            tabel.find('tr').each(function() {
                                const s = ($(this).find('textarea[name="sasaran_program[]"]').val() || '').trim().toLowerCase();
                                const i = ($(this).find('textarea[name="indikator_kinerja[]"]').val() || '').trim().toLowerCase();
                                if (s !== '' || i !== '') {
                                    existingTargetKeys.add(s + '|||' + i);
                                }
                            });
                        }

                        if (modeSalin === 'replace' || isCurrentTableEmpty) {
                            tabel.empty();
                        }

                        let insertedCount = 0;
                        let skippedDuplicates = 0;

                        response.data.forEach(function(item) {
                            const rawSasaran = (item.sasaran_program || '').trim();
                            const rawIndikator = (item.indikator_kinerja || '').trim();
                            const itemKey = rawSasaran.toLowerCase() + '|||' + rawIndikator.toLowerCase();

                            // Jika mode append dan item ini sudah ada di tabel, lewati untuk mencegah duplikasi
                            if (modeSalin === 'append' && existingTargetKeys.has(itemKey)) {
                                skippedDuplicates++;
                                return;
                            }

                            existingTargetKeys.add(itemKey);
                            insertedCount++;

                            const valTarget = (item.target_bulanan !== null && item.target_bulanan !== '') ? item.target_bulanan : '';
                            const safeSasaran = escapeHtml(item.sasaran_program);
                            const safeIndikator = escapeHtml(item.indikator_kinerja);
                            const safeSatuan = escapeHtml(item.satuan);

                            const newRow = $(`
                                <tr class="row-slide-in" style="animation-delay: ${insertedCount * 45}ms;">
                                    <input type="hidden" name="laporan_id[]" value="">
                                    <td class="nomor-baris text-center fw-bold text-muted">1</td>
                                    <td>
                                        <textarea name="sasaran_program[]" class="form-control form-control-sm" rows="2" placeholder="Sasaran Program Unit..." aria-label="Sasaran Program Unit baris ${insertedCount}">${safeSasaran}</textarea>
                                    </td>
                                    <td>
                                        <textarea name="indikator_kinerja[]" class="form-control form-control-sm" rows="2" placeholder="Indikator / RHK..." aria-label="Indikator Kinerja Individu baris ${insertedCount}">${safeIndikator}</textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val" placeholder="Target" aria-label="Target Kuantitatif baris ${insertedCount}" value="${valTarget}">
                                    </td>
                                    <td>
                                        <input type="text" name="satuan[]" class="form-control form-control-sm text-center input-satuan-val" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Target baris ${insertedCount}" value="${safeSatuan}" title="${safeSatuan}">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 status-badge"><i class="bi bi-pencil me-1"></i> Draf Baru</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="" title="Hapus Target" aria-label="Hapus target baris ${insertedCount}" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                    </td>
                                </tr>
                            `);
                            tabel.append(newRow);
                        });

                        updateRowNumbers(tabel);

                        // Notifikasi hasil salin cerdas
                        if (typeof Swal !== 'undefined') {
                            if (insertedCount > 0 && skippedDuplicates === 0) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Target Berhasil Disalin!',
                                    text: `${insertedCount} target dari ${response.nama_bulan_sumber} ${response.tahun_sumber} berhasil disalin ke tabel. Silakan sesuaikan angka target jika diperlukan, lalu simpan draf atau ajukan.`,
                                    confirmButtonText: '<i class="bi bi-check2 me-1"></i> Siap, Periksa Target'
                                });
                            } else if (insertedCount > 0 && skippedDuplicates > 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Target Berhasil Ditambahkan',
                                    text: `${insertedCount} target baru berhasil ditambahkan (${skippedDuplicates} target dilewati karena sudah ada di tabel).`,
                                    confirmButtonText: '<i class="bi bi-check2 me-1"></i> Mengerti'
                                });
                            } else if (insertedCount === 0 && skippedDuplicates > 0) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Semua Target Sudah Ada',
                                    text: `Seluruh ${skippedDuplicates} target dari ${response.nama_bulan_sumber} ${response.tahun_sumber} sudah ada di tabel saat ini (tidak ada baris duplikat yang ditambahkan).`,
                                    confirmButtonText: '<i class="bi bi-check2 me-1"></i> Baik'
                                });
                            }
                        } else {
                            if (insertedCount > 0) {
                                alert(`${insertedCount} target berhasil ditambahkan (${skippedDuplicates} dilewati).`);
                            } else {
                                alert('Semua target dari periode tersebut sudah ada di tabel.');
                            }
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal memuat target dari periode tersebut.', 'error');
                        } else {
                            alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    btn.html(originalText).prop('disabled', false);
                    console.error('Salin Target Error:', xhr.responseText || error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Terjadi kesalahan jaringan atau server saat mengambil data.', 'error');
                    } else {
                        alert('Terjadi kesalahan jaringan atau server.');
                    }
                }
            });
        });

        // Toggle active class pada modal radio card
        $(document).on('change', 'input[name="modeSalinTarget"]', function() {
            $('.modal-radio-card').removeClass('active');
            $(this).closest('.modal-radio-card').addClass('active');
        });

        function escapeHtml(text) {
            if (!text) return '';
            return $('<div>').text(text).html();
        }

    });
</script>
<?= $this->endSection() ?>

