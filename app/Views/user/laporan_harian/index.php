<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Target Kinerja Bulanan<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Target Kinerja Bulanan
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .table th {
        background-color: #f8f9fa !important;
        color: #333;
        vertical-align: middle;
    }   
    .table {
        font-size: 0.85rem;
    }
    .form-control, .form-select {
        font-size: 0.85rem;
    }
    .col-rhk, .col-indikator {
        min-width: 250px;
    }
    .col-target, .col-satuan {
        min-width: 120px;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        letter-spacing: 0.5px;
        border-radius: 50rem;
    }
    .btn {
        transition: all 0.3s ease;
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }
    .tabel-target th {
        border-bottom-width: 2px;
    }
    .tabel-target td, .tabel-target th {
        border-color: #eaeaea;
    }
    .segmented-control {
        background-color: #f1f3f5;
        border-radius: 50rem;
        padding: 0.35rem;
        display: inline-flex;
        border: 1px solid #e9ecef;
    }
    .segmented-control .nav-link {
        border-radius: 50rem;
        color: #6c757d;
        padding: 0.5rem 1.5rem;
        transition: all 0.2s ease;
        border: none;
        margin: 0;
        font-weight: 500;
    }
    .segmented-control .nav-link:hover {
        background-color: rgba(255,255,255,0.6);
        color: #495057;
    }
    .segmented-control .nav-link.active {
        background-color: #ffffff;
        color: #0d6efd;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        font-weight: 700;
    }

    /* Mobile UI/UX Pro Max Enhancements */
    @media (max-width: 767.98px) {
        .segmented-control {
            display: flex !important;
            width: 100% !important;
            border-radius: 16px !important;
            padding: 4px !important;
            gap: 4px;
        }
        .segmented-control .nav-item {
            flex: 1 !important;
        }
        .segmented-control .nav-link {
            width: 100% !important;
            padding: 10px 8px !important;
            font-size: 0.8rem !important;
            border-radius: 12px !important;
            white-space: nowrap;
            justify-content: center;
        }
        .btn-action-container {
            flex-direction: column !important;
            gap: 12px !important;
        }
        .btn-action-container .btn,
        .btn-group-mobile {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
        }
        .btn-group-mobile {
            flex-direction: column !important;
            gap: 10px !important;
        }
        .table-responsive {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }
        .form-control, .form-select {
            font-size: 16px !important; /* Mencegah auto-zoom browser iOS */
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<ul class="nav segmented-control mb-4" id="targetKinerjaTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= empty($staf_id_terpilih) ? 'active' : '' ?>" id="sendiri-tab" data-bs-toggle="tab" data-bs-target="#sendiri" type="button" role="tab" aria-controls="sendiri" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
            <i class="bi bi-person-fill me-1"></i> Target Kinerja Saya
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

<div class="tab-content" id="targetKinerjaTabContent">

    <!-- TAB 1: TARGET KINERJA SAYA -->
    <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="sendiri" role="tabpanel" aria-labelledby="sendiri-tab">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <!-- Filter Bulan/Tahun -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-2 px-3 bg-light rounded">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="sendiri">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.85rem;">Bulan</label>
                            <select name="bulan" class="form-select" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.85rem;">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-7 text-muted mt-2 mt-md-0 d-flex align-items-end" style="padding-bottom: 0.35rem;">
                            <small><i class="bi bi-info-circle me-1"></i> Buat rancangan target pekerjaan Anda untuk bulan ini pada tabel di bawah.</small>
                        </div>
                    </div>
                </form>

                <?php if ($is_locked): ?>
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-lock-fill me-2"></i> <strong>Akses Terkunci!</strong> Batas waktu pengisian target bulan ini telah ditutup (Batas: tanggal <?= esc($batas_target) ?>). Anda hanya dapat melihat data.
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('laporan-harian/store') ?>" method="POST" class="form-target-sendiri">
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
                        <div class="alert alert-warning mb-3 shadow-sm border border-warning-subtle">
                            <i class="bi bi-pencil-square me-2 text-dark"></i> <strong>Target Perlu Ditingkatkan / Direvisi!</strong> Target Kinerja Bulanan Anda saat ini berstatus <strong>Draf (Perlu Revisi)</strong>. Silakan perbaiki rincian target pada tabel di bawah, lalu klik <strong>"Simpan & Kirim"</strong> untuk mengajukan kembali ke atasan langsung.
                        </div>
                    <?php elseif ($hasTerkirim && !$allApproved): ?>
                        <div class="alert alert-info mb-3 shadow-sm border border-info-subtle">
                            <i class="bi bi-hourglass-split me-2 text-primary"></i> <strong>Menunggu Persetujuan Atasan!</strong> Target Kinerja Bulanan Anda telah dikirim dan saat ini sedang menunggu persetujuan dari atasan langsung.
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle table-hover tabel-target">
                            <thead class="text-center">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                    <th class="col-indikator">Indikator Kinerja Individu</th>
                                    <th class="col-target">Target <br><span class="text-primary"><?= esc($nama_bulan) ?></span></th>
                                    <th class="col-satuan">Satuan</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <?php if (!empty($rekap_data_sendiri)): ?>
                                    <?php $isDirektur = (session()->get('role') === 'direktur'); ?>
                                    <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                        <?php $isRowLocked = !$isDirektur && ($is_locked || $row['status_approval'] === 'disetujui'); ?>
                                        <tr>
                                            <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                            <td class="nomor-baris text-center fw-bold"><?= $index + 1 ?></td>
                                            <td>
                                                <textarea name="sasaran_program[]" class="form-control" rows="2" placeholder="Sasaran Program Unit..." <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['sasaran_program']) ?></textarea>
                                            </td>
                                            <td>
                                                <textarea name="indikator_kinerja[]" class="form-control" rows="2" placeholder="Indikator / RHK..." <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['indikator_kinerja']) ?></textarea>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="target_bulanan[]" class="form-control text-center" placeholder="Target" value="<?= isset($row['target_bulanan']) && $row['target_bulanan'] !== null ? (float)$row['target_bulanan'] : '' ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="text" name="satuan[]" class="form-control text-center" placeholder="Satuan" value="<?= esc($row['satuan']) ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td class="text-center">
                                                <?php if($row['status_approval'] === 'disetujui'): ?>
                                                    <span class="badge bg-success status-badge"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                                <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                    <span class="badge bg-warning text-dark status-badge"><i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark status-badge border border-warning" style="background-color: #fff3cd !important;"><i class="bi bi-pencil-square me-1"></i> Draf (Perlu Revisi)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!$isRowLocked): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" data-id="<?= esc($row['id']) ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif (!$is_locked || (session()->get('role') === 'direktur')): ?>
                                    <!-- Baris Kosong Default -->
                                    <tr>
                                        <input type="hidden" name="laporan_id[]" value="">
                                        <td class="nomor-baris text-center fw-bold">1</td>
                                        <td><textarea name="sasaran_program[]" class="form-control" rows="2" placeholder="Sasaran Program Unit..."></textarea></td>
                                        <td><textarea name="indikator_kinerja[]" class="form-control" rows="2" placeholder="Indikator / RHK..."></textarea></td>
                                        <td><input type="number" step="0.01" name="target_bulanan[]" class="form-control text-center" placeholder="Target"></td>
                                        <td><input type="text" name="satuan[]" class="form-control text-center" placeholder="Satuan"></td>
                                        <td class="text-center"><span class="badge bg-secondary status-badge"><i class="bi bi-pencil"></i> Baru (Draf)</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm hapus-baris" data-id="" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center text-muted">Belum ada data target untuk bulan ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ((session()->get('role') === 'direktur') || (!$is_locked && !$allApproved)): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4 btn-action-container">
                        <button type="button" class="btn btn-success btn-tambah-baris rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-plus-circle me-2"></i> Tambah Baris Kosong</button>
                        <div class="d-flex gap-2 btn-group-mobile">
                            <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-cloud-arrow-up me-2"></i> Simpan Sementara</button>
                            <button type="submit" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold"><i class="bi bi-send me-2"></i> Simpan & Kirim</button>
                        </div>
                    </div>
                    <?php elseif ($allApproved): ?>
                    <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <i class="bi bi-check-circle-fill me-2"></i> Semua target kinerja Anda untuk bulan ini telah <strong>disetujui</strong>. Data telah dikunci.
                        </div>
                        <?php if (hasRole('admin')): ?>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm btn-batal-approve-target"
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
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <!-- Filter Form untuk Staf -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-2 px-3 bg-light rounded">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="staf">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-success mb-1" style="font-size: 0.85rem;">Pilih Staf</label>
                            <select name="staf_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Pilih Staf --</option>
                                <?php foreach ($daftar_staf as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($s['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                        <?= esc($s['nama_lengkap']) ?> - <?= esc($s['jabatan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-success mb-1" style="font-size: 0.85rem;">Bulan</label>
                            <select name="bulan" class="form-select" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mt-2 mt-md-0">
                            <label class="form-label fw-bold text-success mb-1" style="font-size: 0.85rem;">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>

                <?php if (!empty($staf_id_terpilih) && $is_penyetuju): ?>
                    <?php if (empty($rekap_data_staf)): ?>
                        <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> Staf ini belum membuat target kinerja untuk bulan <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>.</div>
                    <?php else: ?>
                        
                        <div class="mb-3">
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-list-check text-success me-2"></i>Target Kinerja Staf</h5>
                            <small class="text-muted">Periksa dan setujui target staf Anda di bawah ini.</small>
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

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered align-middle table-hover tabel-target">
                                    <thead class="text-center">
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                            <th class="col-indikator">Indikator Kinerja Individu</th>
                                            <th class="col-target">Target</th>
                                            <th class="col-satuan">Satuan</th>
                                            <th style="width: 100px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php $isStafApproved = ($row['status_approval'] === 'disetujui'); ?>
                                            <tr>
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                <td class="nomor-baris text-center fw-bold"><?= $index + 1 ?></td>
                                                <td>
                                                    <textarea name="sasaran_program[]" class="form-control staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" required readonly><?= esc($row['sasaran_program']) ?></textarea>
                                                </td>
                                                <td>
                                                    <textarea name="indikator_kinerja[]" class="form-control staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" required readonly><?= esc($row['indikator_kinerja']) ?></textarea>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="target_bulanan[]" class="form-control text-center staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" value="<?= (float)$row['target_bulanan'] ?>" required readonly>
                                                </td>
                                                <td>
                                                    <input type="text" name="satuan[]" class="form-control text-center staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" value="<?= esc($row['satuan']) ?>" required readonly>
                                                </td>
                                                <td class="text-center">
                                                    <?php if($row['status_approval'] === 'disetujui'): ?>
                                                        <span class="badge bg-success status-badge"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                                    <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                        <span class="badge bg-warning text-dark status-badge"><i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary status-badge"><i class="bi bi-pencil me-1"></i> Draf (Belum Diajukan)</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (!$allApprovedStaf): ?>
                            <div class="d-flex justify-content-end gap-3 mt-4 btn-action-container">
                                <button type="submit" id="btnApproveAll" formaction="<?= site_url('laporan-harian/approve-all') ?>" class="btn btn-success rounded-pill shadow-sm px-4 py-2 fw-bold">
                                    <i class="bi bi-check-all me-2"></i> Setujui Semua Target
                                </button>
                                <button type="button" id="btnEditStaf" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Target
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <i class="bi bi-check-circle-fill me-2"></i> Target kinerja bulan ini untuk staf bersangkutan telah <strong>disetujui</strong>.
                                </div>
                                <?php if (hasRole('admin')): ?>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm btn-batal-approve-target"
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
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-arrow-up-circle fs-1"></i>
                        <p class="mt-2">Silakan pilih staf pada dropdown di atas untuk memulai persetujuan/edit target.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        
        // Fungsi Tambah Baris untuk Form Target Saya
        $('.btn-tambah-baris').on('click', function() {
            const tabel = $(this).closest('form').find('.tabel-target tbody');
            const rowPertama = tabel.find('tr:first').clone();
            
            rowPertama.find('input[name="laporan_id[]"]').val('');
            rowPertama.find('input[type="number"]').val('');
            rowPertama.find('input[type="text"]').val('');
            rowPertama.find('textarea').val('');
            rowPertama.find('textarea').removeAttr('readonly');
            rowPertama.find('input').removeAttr('readonly');
            
            rowPertama.find('.status-badge').removeClass('bg-success bg-warning text-dark').addClass('bg-secondary').text('Baru');
            rowPertama.find('.hapus-baris').attr('data-id', '').show();
            
            tabel.append(rowPertama);
            updateRowNumbers(tabel);
        });

        function updateRowNumbers(tabel) {
            tabel.find('tr').each(function(index) {
                $(this).find('.nomor-baris').text(index + 1);
            });
        }

        // Fungsi Reset Baris ke Kosong
        function resetRowToEmpty(row) {
            row.find('input[name="laporan_id[]"]').val('');
            row.find('input[type="number"]').val('');
            row.find('input[type="text"]').val('');
            row.find('textarea').val('');
            row.find('.status-badge').removeClass('bg-success bg-warning text-dark').addClass('bg-secondary').text('Draf');
            row.find('.hapus-baris').attr('data-id', '');
        }

        // Fungsi Hapus Baris
        $(document).on('click', '.hapus-baris', function() {
            const tabel = $(this).closest('tbody');
            const row = $(this).closest('tr');
            let idLaporan = row.find('input[name="laporan_id[]"]').val() || $(this).attr('data-id');

            if (idLaporan && idLaporan.trim() !== '') {
                const doDelete = function() {
                    let csrfTokenName = '<?= csrf_token() ?>';
                    let csrfHash = $('input[name="' + csrfTokenName + '"]').val() || '<?= csrf_hash() ?>';
                    
                    let postData = { id: idLaporan };
                    postData[csrfTokenName] = csrfHash;

                    $.ajax({
                        url: '<?= site_url('laporan-harian/hapus') ?>',
                        type: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.csrf_hash) {
                                $('input[name="' + csrfTokenName + '"]').val(response.csrf_hash);
                                $('input[name="csrf_test_name"]').val(response.csrf_hash);
                            }
                            if (response.success) {
                                if (tabel.find('tr').length > 1) {
                                    row.remove();
                                    updateRowNumbers(tabel);
                                } else {
                                    resetRowToEmpty(row);
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: 'Target RHK berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Gagal', response.message || 'Gagal menghapus data.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Hapus Error:', xhr.responseText);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.', 'error');
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Target Bulanan?',
                        text: 'Data target RHK ini akan dihapus. Kegiatan harian yang telah dilaporkan tetap aman.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDelete();
                        }
                    });
                } else {
                    if (confirm('Apakah Anda yakin ingin menghapus target ini?')) {
                        doDelete();
                    }
                }
            } else {
                if (tabel.find('tr').length > 1) {
                    row.remove();
                    updateRowNumbers(tabel);
                } else {
                    resetRowToEmpty(row);
                }
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
                    $(this).removeClass('btn-primary').addClass('btn-warning');
                    $(this).html('<i class="bi bi-save me-2"></i> Simpan & Setujui Target');
                    $('#btnApproveAll').hide(); // Sembunyikan tombol approve all saat mode edit
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

        // Validasi saat submit "Simpan & Kirim" (Form submit biasa)
        $('.form-target-sendiri').on('submit', function(e) {
            let isValid = true;
            let hasAtLeastOne = false;

            $(this).find('.tabel-target tbody tr').each(function() {
                let sasaran = $(this).find('textarea[name="sasaran_program[]"]').val().trim();
                let indikator = $(this).find('textarea[name="indikator_kinerja[]"]').val().trim();
                let target = $(this).find('input[name="target_bulanan[]"]').val().trim();
                let satuan = $(this).find('input[name="satuan[]"]').val().trim();

                // Jika seluruh kolom di baris ini terisi
                if (sasaran !== '' || indikator !== '' || target !== '' || satuan !== '') {
                    hasAtLeastOne = true;
                    if (sasaran === '' || indikator === '' || target === '' || satuan === '') {
                        isValid = false;
                    }
                }
            });

            if (!hasAtLeastOne) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Target Masih Kosong', 'Silakan isi minimal satu rincian target kinerja sebelum mengirim ke atasan langsung.', 'warning');
                } else {
                    alert('Silakan isi minimal satu rincian target kinerja sebelum mengirim ke atasan langsung.');
                }
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Data Belum Lengkap', 'Untuk mengirim target ke atasan langsung, pastikan semua kolom (Sasaran, Indikator, Target, Satuan) pada setiap baris terisi dengan lengkap.', 'warning');
                } else {
                    alert('Untuk mengirim target ke atasan langsung, pastikan semua kolom (Sasaran, Indikator, Target, Satuan) pada setiap baris terisi dengan lengkap.');
                }
                return false;
            }
        });

        // Fungsi Simpan Sementara (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            let btn = $(this);
            let originalText = btn.html();
            let form = $('.form-target-sendiri');

            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: '<?= site_url('laporan-harian/store') ?>',
                type: 'POST',
                data: form.serialize() + '&action=draft',
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                        $('input[name="csrf_test_name"]').val(response.csrf_hash);
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
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

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
                            $('input[name="' + csrfName + '"]').val(response.csrf_hash);
                            $('input[name="csrf_test_name"]').val(response.csrf_hash);
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
                    html: `Persetujuan target bulanan akan dibatalkan dan dikembalikan ke status draf.<br>Pegawai bersangkutan dapat merevisi dan mengajukan kembali ke atasan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-x-circle-fill me-1"></i> Ya, Batalkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeCancel();
                    }
                });
            } else {
                if (confirm('Batalkan Persetujuan Target?\n\nPersetujuan target bulanan akan dibatalkan dan dikembalikan ke status draf agar pegawai dapat merevisi.')) {
                    executeCancel();
                }
            }
        });

    });
</script>
<?= $this->endSection() ?>

