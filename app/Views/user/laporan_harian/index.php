<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Target Kinerja Bulanan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-text-muted-aa {
        color: #475569 !important;
    }
    .num-tabular {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-btn-pill {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    /* Tabel 7 Kolom Fit-to-Viewport (Desktop Tanpa Scrollbar Horizontal) */
    .table-bento {
        width: 100%;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px;
        vertical-align: middle;
        white-space: normal;
        line-height: 1.3;
    }
    .table-bento tbody td {
        padding: 12px;
        vertical-align: top;
        border-color: #f1f5f9;
        font-size: 0.875rem;
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .table-bento tbody td.td-no {
        padding-top: 20px;
    }
    .table-bento tbody td .status-badge {
        margin-top: 4px;
    }
    .table-bento tbody tr:hover td {
        background-color: #f8fafc;
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }

    .col-no { width: 48px; }
    .col-rhk { width: auto; }
    .col-indikator { width: auto; }
    .col-target { width: 112px; }
    .col-satuan { width: 128px; }
    .col-status { width: 136px; }
    .col-aksi { width: 56px; }

    .tabel-target textarea.auto-grow-textarea.form-control {
        overflow-y: hidden !important;
        resize: none !important;
        width: 100%;
        height: auto;
        min-height: 56px;
        field-sizing: content;
        line-height: 1.45;
        font-size: 0.875rem;
    }
    .tabel-target input.input-target-val,
    .tabel-target input.input-satuan-val {
        min-height: 36px;
    }

    .input-target-val::-webkit-outer-spin-button,
    .input-target-val::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-target-val {
        -moz-appearance: textfield;
    }

    .table-bento input.form-control,
    .table-bento textarea.form-control {
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .table-bento input.form-control:focus,
    .table-bento textarea.form-control:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
        background-color: #ffffff !important;
    }

    .hapus-baris {
        width: 36px;
        height: 36px;
        min-width: 36px;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Motion & Transitions */
    @keyframes rowSlideIn {
        0% {
            opacity: 0;
            transform: translateY(-8px);
            background-color: #eff6ff;
        }
        100% {
            opacity: 1;
            transform: translateY(0);
            background-color: transparent;
        }
    }
    .row-slide-in {
        animation: rowSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes rowSlideOut {
        0% {
            opacity: 1;
            transform: translateX(0);
            background-color: #fef2f2;
        }
        100% {
            opacity: 0;
            transform: translateX(16px);
            background-color: #fef2f2;
        }
    }
    .row-slide-out {
        animation: rowSlideOut 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        pointer-events: none;
    }

    @keyframes pulseDuplicate {
        0%, 100% { background-color: #fef2f2; transform: translateX(0); }
        25% { background-color: #fef2f2; transform: translateX(-4px); }
        50% { background-color: #fef2f2; transform: translateX(4px); }
        75% { background-color: #fef2f2; transform: translateX(-2px); }
    }
    .table-danger {
        animation: pulseDuplicate 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .tab-content > .tab-pane {
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tab-content > .tab-pane.active {
        opacity: 1;
        transform: translateY(0);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        white-space: normal;
        line-height: 1.25;
        text-align: center;
    }

    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease;
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    .modal-radio-card {
        border: 1px solid #e2e8f0;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .modal-radio-card:hover {
        border-color: #0d6efd;
        background-color: #f8fafc !important;
    }
    .modal-radio-card.active {
        border-color: #0d6efd !important;
        background-color: #eff6ff !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
    }

    /* Kartu Responsif Mobile (< 768px) Tanpa Duplikasi Input */
    @media (max-width: 767.98px) {
        .ecc-control-sm,
        .gov-touch-ctrl,
        .filter-select,
        .form-control,
        .form-select {
            font-size: var(--bs-body-font-size, 1rem) !important;
            min-height: 44px;
        }
        .hapus-baris {
            width: 44px;
            height: 44px;
            min-width: 44px;
            min-height: 44px;
        }
        .table-bento,
        .table-bento tbody,
        .table-bento tr,
        .table-bento td {
            display: block;
            width: 100% !important;
        }
        .table-bento thead {
            display: none;
        }
        .table-bento-wrap {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }
        .table-bento tbody tr {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }
        .table-bento tbody td {
            border: 0 !important;
            padding: 0 0 12px 0;
            text-align: left !important;
        }
        .table-bento tbody td:last-child {
            padding-bottom: 0;
            display: flex;
            justify-content: flex-end;
        }
        .table-bento tbody td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .table-bento tbody td.td-no {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-top: 0;
            padding-bottom: 8px;
        }
        .table-bento tbody td.td-no::before {
            content: "Target #";
            margin-bottom: 0;
        }
        .table-bento tbody td .status-badge {
            margin-top: 0;
        }
        .table-bento input.input-target-val,
        .table-bento input.input-satuan-val {
            text-align: left !important;
        }
        .btn-action-container,
        .btn-group-mobile {
            width: 100%;
            flex-direction: column;
        }
        .btn-action-container .btn,
        .btn-group-mobile .btn {
            width: 100%;
            min-height: 44px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .row-slide-in, .row-slide-out, .table-danger,
        .tab-content > .tab-pane, .btn-tactile, .modal-radio-card,
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
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Target Kinerja Bulanan</h1>
            <p class="ecc-text-muted-aa small mb-0">Rencana Hasil Kerja (RHK) dan target kuantitatif bulanan periode <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-4 border-0" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-4 border-0" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php $isTabStafActive = !empty($is_atasan) && (($active_tab ?? '') === 'staf' || !empty($staf_id_terpilih)); ?>
    <?php if ($is_atasan): ?>
    <!-- Navigasi Tab -->
    <ul class="nav segmented-control mb-3" id="targetKinerjaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link gov-touch-ctrl <?= !$isTabStafActive ? 'active' : '' ?>" id="sendiri-tab" data-bs-toggle="tab" data-bs-target="#sendiri" type="button" role="tab" aria-controls="sendiri" aria-selected="<?= !$isTabStafActive ? 'true' : 'false' ?>">
                Target Saya
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link gov-touch-ctrl <?= $isTabStafActive ? 'active' : '' ?>" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= $isTabStafActive ? 'true' : 'false' ?>">
                Persetujuan Target Staf
            </button>
        </li>
    </ul>
    <?php endif; ?>

<datalist id="daftarSatuanStandar">
    <?php if (!empty($daftar_satuan)): ?>
        <?php foreach ($daftar_satuan as $sat): ?>
            <?php $valSatuan = $sat['nama_satuan'] ?? $sat['satuan'] ?? ''; ?>
            <?php if ($valSatuan !== ''): ?>
                <option value="<?= esc($valSatuan) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
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
    <?php endif; ?>
</datalist>

<div class="tab-content" id="targetKinerjaTabContent">

    <!-- TAB 1: TARGET KINERJA SAYA -->
    <div class="tab-pane fade <?= !$isTabStafActive ? 'show active' : '' ?>" id="sendiri" role="tabpanel" aria-labelledby="sendiri-tab">
        <div class="card shadow-sm border-0 mb-4 rounded-4">
            <div class="card-body p-3 p-md-4">
                <!-- Filter Bulan/Tahun -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="sendiri">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="filterSendiriBulan" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Bulan</label>
                            <select id="filterSendiriBulan" name="bulan" class="form-select ecc-control-sm shadow-sm filter-select" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="filterSendiriTahun" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Tahun</label>
                            <input type="number" id="filterSendiriTahun" name="tahun" class="form-control ecc-control-sm shadow-sm filter-select num-tabular input-filter-tahun" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099">
                        </div>
                        <div class="col-12 col-md-7 ecc-text-muted-aa small pb-1">
                            <?php if (session()->get('role') !== 'direktur' && !empty($atasan_periode['nama_lengkap'])): ?>
                                Target periode ini diajukan ke <strong class="text-dark"><?= esc(rtrim((string) $atasan_periode['nama_lengkap'], '.')) ?></strong>.
                            <?php else: ?>
                                Rancang dan kelola target kinerja bulanan Anda pada tabel di bawah.
                            <?php endif; ?>
                        </div>
                    </div>
                </form>

                <?php if (!empty($is_post_tmt)): ?>
                    <div class="alert alert-warning mb-3 py-2 px-3 small rounded-4" role="alert">
                        <?= esc($post_tmt_reason) ?>
                    </div>
                <?php elseif ($is_locked): ?>
                    <div class="alert alert-warning mb-3 py-2 px-3 small rounded-4" role="alert">
                        <strong>Akses Terkunci.</strong> Batas pengisian tanggal <?= esc($batas_target) ?> telah terlewati.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('laporan-harian/store') ?>" class="form-target-sendiri">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="is_editing_staf" value="0">
                    <input type="hidden" name="action" value="submit">
                    <div id="containerDeletedTargetIdsSendiri"></div>

                    <?php
                        $formatInputDecimal = static function ($val): string {
                            if ($val === null || $val === '') {
                                return '';
                            }
                            $normalized = str_replace(',', '.', trim((string) $val));
                            if (!is_numeric($normalized)) {
                                return '';
                            }
                            $formatted = number_format((float) $normalized, 4, '.', '');
                            return rtrim(rtrim($formatted, '0'), '.');
                        };
                        $isDirektur = (session()->get('role') === 'direktur');
                        $oldSasaran = old('sasaran_program');
                        $displayRowsSendiri = [];

                        // Rekonstitusi baris dari input old() saat ada kegagalan submit
                        if (is_array($oldSasaran) && !empty($oldSasaran) && old('is_editing_staf') != '1') {
                            $oldIndikator = (array) old('indikator_kinerja', []);
                            $oldTarget = (array) old('target_bulanan', []);
                            $oldSatuan = (array) old('satuan', []);
                            $oldLaporanId = (array) old('laporan_id', []);
                            $oldClientId = (array) old('client_row_id', []);

                            foreach ($oldSasaran as $i => $sasaran) {
                                $rowId = isset($oldLaporanId[$i]) && is_numeric($oldLaporanId[$i]) && (int)$oldLaporanId[$i] > 0 ? (int)$oldLaporanId[$i] : null;
                                $cId = !empty($oldClientId[$i]) ? $oldClientId[$i] : ('c_row_old_' . $i);
                                $dbStatusApproval = null;
                                $dbStatus = 'draft';
                                $dbApprovedBy = null;
                                $dbApprovedAt = null;
                                $dbNamaPenyetuju = null;

                                if ($rowId && !empty($rekap_data_sendiri)) {
                                    foreach ($rekap_data_sendiri as $r) {
                                        if ((int)$r['id'] === $rowId) {
                                            $dbStatusApproval = $r['status_approval'];
                                            $dbStatus = $r['status'] ?? 'draft';
                                            $dbApprovedBy = $r['approved_by'] ?? null;
                                            $dbApprovedAt = $r['approved_at'] ?? null;
                                            $dbNamaPenyetuju = $r['nama_penyetuju'] ?? null;
                                            break;
                                        }
                                    }
                                }

                                $displayRowsSendiri[] = [
                                    'id' => $rowId,
                                    'client_row_id' => $cId,
                                    'sasaran_program' => (string)$sasaran,
                                    'indikator_kinerja' => (string)($oldIndikator[$i] ?? ''),
                                    'target_bulanan' => $oldTarget[$i] ?? '',
                                    'satuan' => (string)($oldSatuan[$i] ?? ''),
                                    'status_approval' => $dbStatusApproval,
                                    'approved_by' => $dbApprovedBy,
                                    'approved_at' => $dbApprovedAt,
                                    'nama_penyetuju' => $dbNamaPenyetuju,
                                    'status' => $dbStatus
                                ];
                            }
                        } else {
                            foreach ($rekap_data_sendiri as $r) {
                                $displayRowsSendiri[] = [
                                    'id' => $r['id'],
                                    'client_row_id' => 'c_row_' . $r['id'],
                                    'sasaran_program' => $r['sasaran_program'] ?? '',
                                    'indikator_kinerja' => $r['indikator_kinerja'] ?? '',
                                    'target_bulanan' => $r['target_bulanan'] ?? '',
                                    'satuan' => $r['satuan'] ?? '',
                                    'status_approval' => $r['status_approval'] ?? null,
                                    'approved_by' => $r['approved_by'] ?? null,
                                    'approved_at' => $r['approved_at'] ?? null,
                                    'nama_penyetuju' => $r['nama_penyetuju'] ?? null,
                                    'status' => $r['status'] ?? 'draft'
                                ];
                            }
                        }

                        $allApproved = !empty($displayRowsSendiri);
                        $hasApproved = false;
                        $hasDraft = false;
                        $hasTerkirim = false;
                        if (!empty($displayRowsSendiri)) {
                            foreach ($displayRowsSendiri as $row) {
                                if ($row['status_approval'] === 'disetujui') {
                                    $hasApproved = true;
                                } else {
                                    $allApproved = false;
                                }
                                if (($row['status'] ?? '') === 'draft' || empty($row['status'])) {
                                    $hasDraft = true;
                                }
                                if (($row['status'] ?? '') === 'terkirim') {
                                    $hasTerkirim = true;
                                }
                            }
                        }
                    ?>

                    <?php if ($isDirektur): ?>
                        <div class="alert alert-success mb-3 py-2 px-3 border border-success-subtle small rounded-4" role="status">
                            <strong>Akun Direktur:</strong> Target Kinerja Bulanan Anda otomatis disetujui dan dapat direvisi sendiri sewaktu-waktu.
                        </div>
                    <?php elseif (!empty($is_revision) && empty($is_post_tmt) && !$is_locked): ?>
                        <div class="alert alert-info border-info-subtle bg-info-subtle text-info-emphasis mb-3 py-2 px-3 small rounded-4" role="status">
                            <strong>Mode Revisi.</strong> Persetujuan target telah dibatalkan agar dapat disesuaikan. Perbaiki rincian target lalu ajukan kembali.
                        </div>
                    <?php elseif ($allApproved): ?>
                        <!-- Banner konfirmasi ada di bawah tabel -->
                    <?php elseif ($hasApproved && !$allApproved): ?>
                        <div class="alert alert-warning mb-3 py-2 px-3 border border-warning-subtle small rounded-4" role="status">
                            <strong>Disetujui Sebagian.</strong> Lengkapi target yang belum disetujui, lalu klik <strong>Ajukan Target</strong>.
                        </div>
                    <?php elseif ($hasTerkirim && !$hasDraft): ?>
                        <div class="alert alert-info mb-3 py-2 px-3 border border-info-subtle small rounded-4" role="status">
                            <strong>Menunggu Persetujuan.</strong> Seluruh target telah diajukan ke atasan langsung. Anda masih dapat mengedit atau menambah target sebelum disetujui.
                        </div>
                    <?php elseif ($hasTerkirim && $hasDraft): ?>
                        <div class="alert alert-info mb-3 py-2 px-3 border border-info-subtle small rounded-4" role="status">
                            <strong>Menunggu Persetujuan.</strong> Terdapat pembaruan draf. Klik <strong>Perbarui &amp; Ajukan Ulang</strong> untuk mengirim seluruh target ke atasan.
                        </div>
                    <?php elseif ($hasDraft): ?>
                        <div class="alert alert-secondary mb-3 py-2 px-3 border border-secondary-subtle small rounded-4" role="status">
                            <strong>Draf Target.</strong> Rancang target kinerja Anda di bawah, lalu klik <strong>Ajukan Target</strong> agar diperiksa atasan.
                        </div>
                    <?php endif; ?>

                    <?php $showAksiSendiri = (!$is_locked && (!$allApproved || $isDirektur)); ?>
                    <!-- Toolbar Tabel -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h2 class="h6 fw-bold text-dark mb-0">Rincian Target <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></h2>
                        <?php if ($showAksiSendiri): ?>
                        <button type="button" class="btn btn-outline-primary ecc-btn-pill gov-touch-ctrl btn-tactile" id="btnSalinTargetModal" data-bs-toggle="modal" data-bs-target="#modalSalinTarget" title="Salin target dari bulan lain">
                            <i class="bi bi-copy" aria-hidden="true"></i>
                            <span>Salin dari Bulan Lain</span>
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="table-bento-wrap mb-3 border rounded-4 shadow-sm bg-white overflow-hidden">
                        <table class="table table-bordered align-top table-hover mb-0 table-bento tabel-target">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-no text-center">No</th>
                                    <th scope="col" class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                    <th scope="col" class="col-indikator">Indikator Kinerja Individu</th>
                                    <th scope="col" class="col-target text-center">Target <?= esc($nama_bulan) ?></th>
                                    <th scope="col" class="col-satuan text-center">Satuan</th>
                                    <th scope="col" class="col-status text-center">Status</th>
                                    <?php if ($showAksiSendiri): ?>
                                    <th scope="col" class="col-aksi text-center">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($displayRowsSendiri)): ?>
                                    <?php foreach ($displayRowsSendiri as $index => $row): ?>
                                        <?php
                                            $isRowApproved = ($row['status_approval'] === 'disetujui');
                                            $isRowLocked = ($is_locked || ($isRowApproved && !$isDirektur));
                                        ?>
                                        <tr data-client-id="<?= esc($row['client_row_id'] ?? ('c_row_' . ($row['id'] ?? $index))) ?>" data-approved="<?= $isRowApproved ? '1' : '0' ?>">
                                            <td class="td-no text-center fw-bold ecc-text-muted-aa num-tabular">
                                                <span class="nomor-baris"><?= $index + 1 ?></span>
                                                <input type="hidden" name="client_row_id[]" value="<?= esc($row['client_row_id'] ?? ('c_row_' . ($row['id'] ?? $index))) ?>">
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id'] ?? '') ?>">
                                            </td>
                                            <td data-label="Rencana Hasil Kerja (RHK)">
                                                <textarea name="sasaran_program[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Tulis rencana hasil kerja..." aria-label="Rencana Hasil Kerja baris <?= $index + 1 ?>" <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['sasaran_program']) ?></textarea>
                                            </td>
                                            <td data-label="Indikator Kinerja Individu">
                                                <textarea name="indikator_kinerja[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Tulis indikator kinerja individu..." aria-label="Indikator Kinerja Individu baris <?= $index + 1 ?>" <?= $isRowLocked ? 'readonly' : '' ?>><?= esc($row['indikator_kinerja']) ?></textarea>
                                            </td>
                                            <td data-label="Target <?= esc($nama_bulan) ?>">
                                                <input type="number" step="any" min="0.0001" max="999999999999999" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val" placeholder="Target" aria-label="Target Kuantitatif baris <?= $index + 1 ?>" value="<?= esc($formatInputDecimal($row['target_bulanan'] ?? null)) ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td data-label="Satuan">
                                                <input type="text" name="satuan[]" maxlength="255" class="form-control form-control-sm text-center input-satuan-val" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Target baris <?= $index + 1 ?>" value="<?= esc($row['satuan']) ?>" title="<?= esc($row['satuan']) ?>" <?= $isRowLocked ? 'readonly' : '' ?>>
                                            </td>
                                            <td data-label="Status" class="text-center">
                                                <?php if($isRowApproved): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 status-badge">Disetujui</span>
                                                    <?php if (!empty($row['nama_penyetuju'])): ?>
                                                        <div class="mt-1 ecc-text-muted-aa" style="font-size: 0.75rem; line-height: 1.25; overflow-wrap: anywhere;" title="Disetujui oleh <?= esc($row['nama_penyetuju']) ?>">
                                                            <?= esc($row['nama_penyetuju']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 status-badge">Menunggu Persetujuan</span>
                                                <?php elseif(empty($row['id'])): ?>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 status-badge">Draf Baru</span>
                                                <?php elseif(!empty($is_revision)): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1 status-badge">Draf Revisi</span>
                                                <?php elseif($is_locked): ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1 status-badge">Lewat Tenggat</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1 status-badge">Draf</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($showAksiSendiri): ?>
                                            <td class="text-center">
                                                <?php if (!$isRowLocked): ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="<?= esc($row['id'] ?? '') ?>" title="Hapus Target" aria-label="Hapus target baris <?= $index + 1 ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                                <?php endif; ?>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php elseif (!$is_locked): ?>
                                    <!-- Baris Kosong Default -->
                                    <tr data-client-id="c_row_init_0" data-approved="0">
                                        <td class="td-no text-center fw-bold ecc-text-muted-aa num-tabular">
                                            <span class="nomor-baris">1</span>
                                            <input type="hidden" name="client_row_id[]" value="c_row_init_0">
                                            <input type="hidden" name="laporan_id[]" value="">
                                        </td>
                                        <td data-label="Rencana Hasil Kerja (RHK)">
                                            <textarea name="sasaran_program[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Tulis rencana hasil kerja..." aria-label="Rencana Hasil Kerja baris 1"></textarea>
                                        </td>
                                        <td data-label="Indikator Kinerja Individu">
                                            <textarea name="indikator_kinerja[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Tulis indikator kinerja individu..." aria-label="Indikator Kinerja Individu baris 1"></textarea>
                                        </td>
                                        <td data-label="Target <?= esc($nama_bulan) ?>">
                                            <input type="number" step="any" min="0.0001" max="999999999999999" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val" placeholder="Target" aria-label="Target Kuantitatif baris 1">
                                        </td>
                                        <td data-label="Satuan">
                                            <input type="text" name="satuan[]" maxlength="255" class="form-control form-control-sm text-center input-satuan-val" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Target baris 1">
                                        </td>
                                        <td data-label="Status" class="text-center">
                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 status-badge">Draf Baru</span>
                                        </td>
                                        <?php if ($showAksiSendiri): ?>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris" data-id="" title="Hapus Target" aria-label="Hapus target baris 1"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php else: ?>
                                    <tr><td colspan="<?= $showAksiSendiri ? 7 : 6 ?>" class="text-center ecc-text-muted-aa py-4">Belum ada data target kinerja untuk bulan ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!$is_locked && (!$allApproved || $isDirektur)): ?>
                    <div class="d-flex justify-content-between align-items-center mt-4 btn-action-container flex-wrap gap-2">
                        <button type="button" class="btn btn-light border btn-tambah-baris btn-tactile rounded-pill px-4 py-2 fw-semibold gov-touch-ctrl">
                            <i class="bi bi-plus-circle me-1" aria-hidden="true"></i> Tambah Target
                        </button>
                        <div class="d-flex gap-2 btn-group-mobile">
                            <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold gov-touch-ctrl">
                                Simpan Draf
                            </button>
                            <button type="submit" id="btnSubmitTargetSendiri" class="btn btn-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold gov-touch-ctrl">
                                <?php if ($isDirektur): ?>
                                    Simpan Target
                                <?php elseif ($hasTerkirim && !$hasDraft): ?>
                                    Perbarui &amp; Ajukan Ulang
                                <?php else: ?>
                                    Ajukan Target
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                    <?php elseif ($allApproved && !$isDirektur): ?>
                    <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3 small rounded-4" role="status">
                        <div>Semua target kinerja bulan ini telah <strong>disetujui</strong> dan dikunci oleh atasan.</div>
                        <?php if (hasRole('admin')): ?>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-tactile rounded-pill px-3 fw-semibold gov-touch-ctrl btn-batal-approve-target"
                            data-staf-id="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>"
                            data-bulan="<?= esc($bulan_terpilih) ?>"
                            data-tahun="<?= esc($tahun_terpilih) ?>">
                            Batalkan Persetujuan
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
    <div class="tab-pane fade <?= $isTabStafActive ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">
        <div class="card shadow-sm border-0 mb-4 rounded-4">
            <div class="card-body p-3 p-md-4">
                <!-- Filter Staf -->
                <form method="POST" action="<?= site_url('laporan-harian') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source_tab" value="staf">
                    <div class="row g-2 align-items-end">
                        <?php if (!empty($is_super) && !empty($daftar_unit_grouped)): ?>
                        <div class="col-12 col-md-3">
                            <label for="filterStafUnit" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Unit Kerja</label>
                            <select id="filterStafUnit" name="unit_kerja" class="form-select ecc-control-sm shadow-sm filter-select" onchange="this.form.submit()">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($daftar_unit_grouped as $groupLabel => $groupUnits): ?>
                                    <optgroup label="<?= esc($groupLabel) ?>">
                                        <?php foreach ($groupUnits as $u): ?>
                                            <option value="<?= esc($u['nama_unit']) ?>" <?= ($unit_kerja_terpilih == $u['nama_unit']) ? 'selected' : '' ?>>
                                                <?= esc($u['label']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-12 <?= (!empty($is_super) && !empty($daftar_unit_grouped)) ? 'col-md-4' : 'col-md-5' ?>">
                            <label for="filterStafId" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Pilih Staf</label>
                            <select id="filterStafId" name="staf_id" class="form-select ecc-control-sm shadow-sm filter-select" onchange="this.form.submit()">
                                <option value="">Pilih Staf</option>
                                <?php if (!empty($daftar_staf_grouped) && count($daftar_staf_grouped) > 1): ?>
                                    <?php foreach ($daftar_staf_grouped as $stafGroup): ?>
                                        <optgroup label="<?= esc($stafGroup['label']) ?>">
                                            <?php foreach ($stafGroup['members'] as $s): ?>
                                                <option value="<?= esc($s['id']) ?>" <?= ($s['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                                    <?= esc($s['nama_lengkap']) ?> - <?= esc($s['jabatan']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($daftar_staf as $s): ?>
                                        <option value="<?= esc($s['id']) ?>" <?= ($s['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                            <?= esc($s['nama_lengkap']) ?> - <?= esc($s['jabatan']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="filterStafBulan" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Bulan</label>
                            <select id="filterStafBulan" name="bulan" class="form-select ecc-control-sm shadow-sm filter-select" onchange="this.form.submit()">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label for="filterStafTahun" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.75rem;">Tahun</label>
                            <input type="number" id="filterStafTahun" name="tahun" class="form-control ecc-control-sm shadow-sm filter-select num-tabular input-filter-tahun" value="<?= esc($tahun_terpilih) ?>" min="2020" max="2099">
                        </div>
                    </div>
                </form>

                <?php if (!empty($staf_id_terpilih) && $is_penyetuju): ?>
                    <?php if (empty($rekap_data_staf)): ?>
                        <div class="card bg-light border-0 rounded-4 p-4 text-center my-3">
                            <h2 class="h6 fw-bold text-dark mb-1">Target Belum Dibuat</h2>
                            <p class="ecc-text-muted-aa small mb-0">
                                <?= !empty($staf_terpilih_detail['nama_lengkap']) ? '<strong>' . esc($staf_terpilih_detail['nama_lengkap']) . '</strong>' : 'Staf yang dipilih' ?>
                                <?php if (!empty($staf_terpilih_detail['unit'])): ?>
                                    (<?= esc($staf_terpilih_detail['unit']) ?>)
                                <?php endif; ?>
                                belum menyusun target kinerja bulanan untuk periode <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong>.
                            </p>
                        </div>
                    <?php else: ?>
                        <?php
                            $displayRowsStaf = [];
                            $oldSasaran = old('sasaran_program');
                            if (is_array($oldSasaran) && !empty($oldSasaran) && old('is_editing_staf') == '1') {
                                $oldIndikator = (array) old('indikator_kinerja', []);
                                $oldTarget = (array) old('target_bulanan', []);
                                $oldSatuan = (array) old('satuan', []);
                                $oldLaporanId = (array) old('laporan_id', []);
                                $oldClientId = (array) old('client_row_id', []);

                                foreach ($oldSasaran as $i => $sasaran) {
                                    $rowId = isset($oldLaporanId[$i]) && is_numeric($oldLaporanId[$i]) && (int)$oldLaporanId[$i] > 0 ? (int)$oldLaporanId[$i] : null;
                                    $cId = !empty($oldClientId[$i]) ? $oldClientId[$i] : ('c_row_staf_old_' . $i);
                                    $dbStatusApproval = null;
                                    $dbStatus = 'draft';
                                    $dbApprovedBy = null;
                                    $dbApprovedAt = null;
                                    $dbNamaPenyetuju = null;

                                    if ($rowId && !empty($rekap_data_staf)) {
                                        foreach ($rekap_data_staf as $r) {
                                            if ((int)$r['id'] === $rowId) {
                                                $dbStatusApproval = $r['status_approval'];
                                                $dbStatus = $r['status'] ?? 'draft';
                                                $dbApprovedBy = $r['approved_by'] ?? null;
                                                $dbApprovedAt = $r['approved_at'] ?? null;
                                                $dbNamaPenyetuju = $r['nama_penyetuju'] ?? null;
                                                break;
                                            }
                                        }
                                    }

                                    $displayRowsStaf[] = [
                                        'id' => $rowId,
                                        'client_row_id' => $cId,
                                        'sasaran_program' => (string)$sasaran,
                                        'indikator_kinerja' => (string)($oldIndikator[$i] ?? ''),
                                        'target_bulanan' => $oldTarget[$i] ?? '',
                                        'satuan' => (string)($oldSatuan[$i] ?? ''),
                                        'status_approval' => $dbStatusApproval,
                                        'approved_by' => $dbApprovedBy,
                                        'approved_at' => $dbApprovedAt,
                                        'nama_penyetuju' => $dbNamaPenyetuju,
                                        'status' => $dbStatus
                                    ];
                                }
                            } else {
                                foreach ($rekap_data_staf as $r) {
                                    $displayRowsStaf[] = [
                                        'id' => $r['id'],
                                        'client_row_id' => 'c_row_staf_' . $r['id'],
                                        'sasaran_program' => $r['sasaran_program'] ?? '',
                                        'indikator_kinerja' => $r['indikator_kinerja'] ?? '',
                                        'target_bulanan' => $r['target_bulanan'] ?? '',
                                        'satuan' => $r['satuan'] ?? '',
                                        'status_approval' => $r['status_approval'] ?? null,
                                        'approved_by' => $r['approved_by'] ?? null,
                                        'approved_at' => $r['approved_at'] ?? null,
                                        'nama_penyetuju' => $r['nama_penyetuju'] ?? null,
                                        'status' => $r['status'] ?? 'draft'
                                    ];
                                }
                            }

                            $allApprovedStaf = !empty($displayRowsStaf);
                            if (!empty($displayRowsStaf)) {
                                foreach ($displayRowsStaf as $row) {
                                    if ($row['status_approval'] !== 'disetujui') {
                                        $allApprovedStaf = false;
                                        break;
                                    }
                                }
                            }
                        ?>

                        <div class="d-flex justify-content-between align-items-start align-items-md-center mb-3 flex-wrap gap-2">
                            <div>
                                <h2 class="h6 fw-bold mb-0 text-dark">
                                    <?= !empty($staf_terpilih_detail['nama_lengkap']) ? 'Target Kinerja — ' . esc($staf_terpilih_detail['nama_lengkap']) : 'Target Kinerja Staf' ?>
                                </h2>
                                <?php if (!empty($staf_terpilih_detail)): ?>
                                    <p class="ecc-text-muted-aa mb-0 mt-1" style="font-size: 0.75rem;">
                                        <?= esc($staf_terpilih_detail['jabatan'] ?: '-') ?>
                                        <?php if (!empty($staf_terpilih_detail['unit'])): ?>
                                            &middot; <strong class="text-dark"><?= esc($staf_terpilih_detail['unit']) ?></strong>
                                            <?php if (!empty($staf_terpilih_detail['is_pokja']) && !empty($staf_terpilih_detail['parent_unit_name'])): ?>
                                                (di bawah <?= esc($staf_terpilih_detail['parent_unit_name']) ?>)
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <span class="ecc-text-muted-aa" style="font-size: 0.75rem;">Periksa dan setujui target kinerja staf di bawah ini.</span>
                        </div>

                        <form action="<?= site_url('laporan-harian/store') ?>" method="POST" class="form-target-staf">
                            <?= csrf_field() ?>
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="is_editing_staf" value="1">
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                            <input type="hidden" name="action" value="submit">
                            <div id="containerDeletedTargetIdsStaf"></div>

                            <div class="table-bento-wrap mb-3 border rounded-4 shadow-sm bg-white overflow-hidden">
                                <table class="table table-bordered align-top table-hover mb-0 table-bento tabel-target">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="col-no text-center">No</th>
                                            <th scope="col" class="col-rhk">Rencana Hasil Kerja (RHK)</th>
                                            <th scope="col" class="col-indikator">Indikator Kinerja Individu</th>
                                            <th scope="col" class="col-target text-center">Target</th>
                                            <th scope="col" class="col-satuan text-center">Satuan</th>
                                            <th scope="col" class="col-status text-center">Status</th>
                                            <th scope="col" class="col-aksi text-center col-aksi-staf" style="display: none;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($displayRowsStaf as $index => $row): ?>
                                            <?php $isStafApproved = ($row['status_approval'] === 'disetujui'); ?>
                                            <tr data-client-id="<?= esc($row['client_row_id'] ?? ('c_row_staf_' . ($row['id'] ?? $index))) ?>" data-approved="<?= $isStafApproved ? '1' : '0' ?>">
                                                <td class="td-no text-center fw-bold ecc-text-muted-aa num-tabular">
                                                    <span class="nomor-baris"><?= $index + 1 ?></span>
                                                    <input type="hidden" name="client_row_id[]" value="<?= esc($row['client_row_id'] ?? ('c_row_staf_' . ($row['id'] ?? $index))) ?>">
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id'] ?? '') ?>">
                                                </td>
                                                <td data-label="Rencana Hasil Kerja (RHK)">
                                                    <textarea name="sasaran_program[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" placeholder="Tulis rencana hasil kerja..." aria-label="Rencana Hasil Kerja Staf baris <?= $index + 1 ?>" required readonly><?= esc($row['sasaran_program']) ?></textarea>
                                                </td>
                                                <td data-label="Indikator Kinerja Individu">
                                                    <textarea name="indikator_kinerja[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea staf-input <?= $isStafApproved ? 'locked-approved' : '' ?>" rows="2" placeholder="Tulis indikator kinerja individu..." aria-label="Indikator Kinerja Individu Staf baris <?= $index + 1 ?>" required readonly><?= esc($row['indikator_kinerja']) ?></textarea>
                                                </td>
                                                <td data-label="Target">
                                                    <input type="number" step="any" min="0.0001" max="999999999999999" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary staf-input input-target-val <?= $isStafApproved ? 'locked-approved' : '' ?>" aria-label="Target Kuantitatif Staf baris <?= $index + 1 ?>" value="<?= esc($formatInputDecimal($row['target_bulanan'] ?? null)) ?>" required readonly>
                                                </td>
                                                <td data-label="Satuan">
                                                    <input type="text" name="satuan[]" maxlength="255" class="form-control form-control-sm text-center staf-input input-satuan-val <?= $isStafApproved ? 'locked-approved' : '' ?>" list="daftarSatuanStandar" aria-label="Satuan Target Staf baris <?= $index + 1 ?>" value="<?= esc($row['satuan']) ?>" title="<?= esc($row['satuan']) ?>" required readonly>
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    <?php if($isStafApproved): ?>
                                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 status-badge">Disetujui</span>
                                                        <?php if (!empty($row['nama_penyetuju'])): ?>
                                                            <div class="mt-1 ecc-text-muted-aa" style="font-size: 0.75rem; line-height: 1.25; overflow-wrap: anywhere;" title="Disetujui oleh <?= esc($row['nama_penyetuju']) ?>">
                                                                <?= esc($row['nama_penyetuju']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php elseif(($row['status'] ?? '') === 'terkirim'): ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 status-badge">Menunggu Persetujuan</span>
                                                    <?php elseif(empty($row['id'])): ?>
                                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 status-badge">Draf Baru</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1 status-badge">Draf</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center col-aksi-staf" style="display: none;">
                                                    <?php if (!$isStafApproved): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-hapus-staf" data-id="<?= esc($row['id'] ?? '') ?>" title="Hapus Target" aria-label="Hapus target staf baris <?= $index + 1 ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (!$allApprovedStaf): ?>
                            <div class="d-flex justify-content-between align-items-center mt-4 btn-action-container flex-wrap gap-2">
                                <div>
                                    <button type="button" id="btnTambahTargetStaf" class="btn btn-light border btn-tambah-baris-staf btn-tactile rounded-pill px-4 py-2 fw-semibold gov-touch-ctrl" style="display: none;">
                                        <i class="bi bi-plus-circle me-1" aria-hidden="true"></i> Tambah Target
                                    </button>
                                </div>
                                <div class="d-flex gap-2 btn-group-mobile">
                                    <button type="button" id="btnEditStaf" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold gov-touch-ctrl">
                                        Edit Target
                                    </button>
                                    <button type="submit" id="btnApproveAll" formaction="<?= site_url('laporan-harian/approve-all') ?>" class="btn btn-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold gov-touch-ctrl">
                                        Setujui Semua
                                    </button>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-success mt-4 mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3 small rounded-4" role="status">
                                <div>Target kinerja staf bulan ini telah <strong>disetujui</strong>.</div>
                                <?php if (hasRole('admin')): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tactile rounded-pill px-3 fw-semibold gov-touch-ctrl btn-batal-approve-target"
                                    data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                    data-bulan="<?= esc($bulan_terpilih) ?>"
                                    data-tahun="<?= esc($tahun_terpilih) ?>">
                                    Batalkan Persetujuan
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card bg-light border-0 rounded-4 p-4 text-center my-3">
                        <h2 class="h6 fw-bold text-dark mb-1">Pilih Staf</h2>
                        <p class="ecc-text-muted-aa small mb-0">Pilih nama staf pada daftar di atas untuk memeriksa dan menyetujui target kinerjanya.</p>
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
            <div class="modal-header bg-light border-bottom p-3 p-md-4">
                <div>
                    <h2 class="modal-title fw-bold text-dark mb-0" id="modalSalinTargetLabel" style="font-size: 1.125rem; line-height: 1.3;">Salin Target Kinerja</h2>
                    <p class="ecc-text-muted-aa mb-0 mt-1" style="font-size: 0.75rem;">Duplikasi target dari periode pilihan Anda</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-3 p-3 rounded-3" style="font-size: 0.875rem;">
                    Target yang disalin masuk sebagai <strong>Draf Baru</strong> ke bulan <strong><?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></strong>.
                </div>

                <!-- Section 1: Periode Sumber -->
                <div class="mb-3">
                    <div class="fw-bold text-dark text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.04em;">Pilih Periode Sumber</div>
                    <div class="row g-2">
                        <div class="col-7">
                            <label for="salinBulanSumber" class="form-label ecc-text-muted-aa fw-semibold mb-1" style="font-size: 0.75rem;">Bulan Sumber</label>
                            <select id="salinBulanSumber" class="form-select ecc-control-sm shadow-sm gov-touch-ctrl">
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($defaultBulanSumber == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label for="salinTahunSumber" class="form-label ecc-text-muted-aa fw-semibold mb-1" style="font-size: 0.75rem;">Tahun Sumber</label>
                            <input type="number" id="salinTahunSumber" class="form-control ecc-control-sm shadow-sm num-tabular gov-touch-ctrl" value="<?= esc($defaultTahunSumber) ?>" min="2020" max="2099">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Metode Penyalinan -->
                <div>
                    <div class="fw-bold text-dark text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.04em;">Metode Penyalinan</div>
                    <div class="d-flex flex-column gap-2">
                        <label class="modal-radio-card d-flex align-items-start bg-white position-relative active p-3 rounded-3 gap-3" for="modeSalinReplace">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="modeSalinTarget" id="modeSalinReplace" value="replace" checked>
                            <div>
                                <strong class="text-dark d-block mb-1" style="font-size: 0.875rem;">Gantikan Baris Tabel Saat Ini</strong>
                                <span class="ecc-text-muted-aa d-block" style="font-size: 0.75rem;">Mengganti baris draf dengan target dari periode sumber.</span>
                            </div>
                        </label>
                        <label class="modal-radio-card d-flex align-items-start bg-white position-relative p-3 rounded-3 gap-3" for="modeSalinAppend">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="modeSalinTarget" id="modeSalinAppend" value="append">
                            <div>
                                <strong class="text-dark d-block mb-1" style="font-size: 0.875rem;">Tambahkan ke Bawah Baris Saat Ini</strong>
                                <span class="ecc-text-muted-aa d-block" style="font-size: 0.75rem;">Menyisipkan target dari periode sumber di bawah target yang ada.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center px-3 px-md-4 py-3">
                <button type="button" class="btn btn-outline-secondary ecc-btn-pill gov-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnEksekusiSalinTarget" class="btn btn-primary ecc-btn-pill gov-touch-ctrl btn-tactile shadow-sm px-4">
                    Salin Target
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script>
    $(document).ready(function() {

        // Auto-grow tinggi seluruh textarea RHK & Indikator (CSS field-sizing: content native dengan fallback JS)
        const supportsFieldSizing = typeof CSS !== 'undefined' && typeof CSS.supports === 'function' && CSS.supports('field-sizing', 'content');

        function autoGrowTextarea(el) {
            if (supportsFieldSizing || !el) return;
            el.style.height = 'auto';
            const targetHeight = Math.max(56, el.scrollHeight + 4);
            el.style.height = targetHeight + 'px';
        }

        function autoGrowAll(scope) {
            if (supportsFieldSizing) return;
            const root = scope || document;
            const textareas = Array.from(root.querySelectorAll('.auto-grow-textarea'));
            if (!textareas.length) return;
            // Fase write 1 -> read -> write 2 secara batch untuk mencegah layout thrashing
            textareas.forEach(function(el) {
                el.style.height = 'auto';
            });
            const heights = textareas.map(function(el) {
                return Math.max(56, el.scrollHeight + 4);
            });
            textareas.forEach(function(el, idx) {
                el.style.height = heights[idx] + 'px';
            });
        }

        if (!supportsFieldSizing) {
            $(document).on('input', '.auto-grow-textarea', function() {
                autoGrowTextarea(this);
            });

            $('#targetKinerjaTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                autoGrowAll(document);
            });

            let resizeGrowTimer = null;
            $(window).on('resize load', function() {
                clearTimeout(resizeGrowTimer);
                resizeGrowTimer = setTimeout(function() {
                    autoGrowAll(document);
                }, 80);
            });

            autoGrowAll(document);
        }

        // Pemulihan status tombol saat pengguna kembali via riwayat browser (bfcache)
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                setTargetFormSavingState($('.form-target-sendiri'), false);
                $('#btnApproveAll, #btnEditStaf, #btnSubmitTargetSendiri').prop('disabled', false);
            }
        });

        // Sinkronkan token CSRF ke seluruh form & meta tag
        function updateAllCsrfTokens(newHash) {
            if (!newHash) return;
            const csrfTokenName = '<?= csrf_token() ?>';
            $('input[name="' + csrfTokenName + '"]').val(newHash);
            $('input[name="csrf_test_name"]').val(newHash);
            $('meta[name="X-CSRF-TOKEN"]').attr('content', newHash);
        }

        // Debounce submit untuk input filter tahun
        let filterTahunDebounceTimer = null;
        $(document).on('input', '.input-filter-tahun', function() {
            const form = $(this).closest('form');
            const val = $(this).val();
            clearTimeout(filterTahunDebounceTimer);
            if (val && val.length === 4 && parseInt(val, 10) >= 2020 && parseInt(val, 10) <= 2099) {
                filterTahunDebounceTimer = setTimeout(function() {
                    form.submit();
                }, 600);
            }
        });
        $(document).on('change blur', '.input-filter-tahun', function() {
            const form = $(this).closest('form');
            const val = $(this).val();
            if (val && parseInt(val, 10) >= 2020 && parseInt(val, 10) <= 2099) {
                clearTimeout(filterTahunDebounceTimer);
                form.submit();
            }
        });

        // Cegah perubahan angka saat pengguna menggulir halaman (passive listener)
        document.addEventListener('wheel', function() {
            const active = document.activeElement;
            if (active && active.tagName === 'INPUT' && active.type === 'number') {
                active.blur();
            }
        }, { passive: true });

        let clientRowCounter = Date.now();
        function generateClientRowId(prefix) {
            clientRowCounter++;
            return (prefix || 'c_row') + '_' + clientRowCounter + '_' + Math.floor(Math.random() * 10000);
        }

        function createTargetRow(data, clientId, isStaf) {
            clientId = clientId || generateClientRowId(isStaf ? 'c_row_staf' : 'c_row');
            data = data || {};
            const valSasaran = data.sasaran_program || '';
            const valIndikator = data.indikator_kinerja || '';
            const valTarget = (data.target_bulanan !== null && data.target_bulanan !== undefined && data.target_bulanan !== '') ? data.target_bulanan : '';
            const valSatuan = data.satuan || '';

            const stafInputClass = isStaf ? 'staf-input' : '';
            const colAksiClass = isStaf ? 'col-aksi-staf' : '';
            const colAksiStyle = isStaf ? 'style="display: table-cell;"' : '';
            const btnStafClass = isStaf ? 'btn-hapus-staf' : '';
            const targetLabel = isStaf ? 'Target' : 'Target <?= esc($nama_bulan) ?>';

            const row = $(`
                <tr class="row-slide-in" data-client-id="${clientId}" data-approved="0">
                    <td class="td-no text-center fw-bold ecc-text-muted-aa num-tabular">
                        <span class="nomor-baris">1</span>
                        <input type="hidden" name="client_row_id[]" value="${clientId}">
                        <input type="hidden" name="laporan_id[]" value="">
                    </td>
                    <td data-label="Rencana Hasil Kerja (RHK)">
                        <textarea name="sasaran_program[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea ${stafInputClass}" rows="2" placeholder="Tulis rencana hasil kerja..."></textarea>
                    </td>
                    <td data-label="Indikator Kinerja Individu">
                        <textarea name="indikator_kinerja[]" maxlength="255" class="form-control form-control-sm auto-grow-textarea ${stafInputClass}" rows="2" placeholder="Tulis indikator kinerja individu..."></textarea>
                    </td>
                    <td data-label="${targetLabel}">
                        <input type="number" step="any" min="0.0001" max="999999999999999" name="target_bulanan[]" class="form-control form-control-sm text-center num-tabular fw-bold text-primary input-target-val ${stafInputClass}" placeholder="Target">
                    </td>
                    <td data-label="Satuan">
                        <input type="text" name="satuan[]" maxlength="255" class="form-control form-control-sm text-center input-satuan-val ${stafInputClass}" placeholder="Satuan" list="daftarSatuanStandar">
                    </td>
                    <td data-label="Status" class="text-center">
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 status-badge">Draf Baru</span>
                    </td>
                    <td class="text-center ${colAksiClass}" ${colAksiStyle}>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris ${btnStafClass}" data-id="" title="Hapus Target"><i class="bi bi-trash3" aria-hidden="true"></i></button>
                    </td>
                </tr>
            `);

            row.find('textarea[name="sasaran_program[]"]').val(valSasaran);
            row.find('textarea[name="indikator_kinerja[]"]').val(valIndikator);
            const targetInput = row.find('input[name="target_bulanan[]"]').val(valTarget);
            formatTargetTooltip(targetInput[0]);
            row.find('input[name="satuan[]"]').val(valSatuan).attr('title', valSatuan);

            return row;
        }

        let isFormDirty = false;
        $(document).on('input change', '.tabel-target textarea, .tabel-target input', function() {
            isFormDirty = true;
        });

        function updateTargetCounter(form) {
            if (!form || !form.length) return;
            const tabel = form.find('.tabel-target tbody');
            const rows = tabel.find('tr').filter(function() {
                return $(this).find('textarea[name="sasaran_program[]"]').length > 0;
            });
            const count = rows.length;
            const btnTambah = form.find('.btn-tambah-baris, .btn-tambah-baris-staf');

            if (count >= 30) {
                btnTambah.prop('disabled', true).addClass('opacity-50');
            } else if (!isSavingTarget) {
                btnTambah.prop('disabled', false).removeClass('opacity-50');
            }
        }

        let isSavingTarget = false;
        function setTargetFormSavingState(form, isSaving) {
            isSavingTarget = isSaving;
            form.find('#btnSimpanSementara').prop('disabled', isSaving);
            form.find('button[type="submit"]').prop('disabled', isSaving);
            form.find('.btn-tambah-baris, .btn-tambah-baris-staf').prop('disabled', isSaving);
            form.find('.hapus-baris').prop('disabled', isSaving);
            $('[data-bs-target="#modalSalinTarget"]').prop('disabled', isSaving);
            $('.filter-select, .input-filter-tahun').prop('disabled', isSaving);
        }

        $('.btn-tambah-baris').on('click', function() {
            if (isSavingTarget) return;
            const form = $(this).closest('form');
            const tabel = form.find('.tabel-target tbody');
            if (tabel.find('tr').length >= 30) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Batas Maksimal', 'Jumlah target kinerja sudah mencapai batas maksimal 30 baris.', 'warning');
                } else {
                    alert('Jumlah target sudah mencapai batas maksimal 30 baris.');
                }
                return;
            }

            isFormDirty = true;
            const newRow = createTargetRow({}, generateClientRowId('c_row'), false);
            tabel.append(newRow);
            updateRowNumbers(tabel);
            autoGrowAll(newRow[0]);

            setTimeout(() => {
                newRow.find('textarea[name="sasaran_program[]"]').focus();
            }, 80);
        });

        function formatTargetTooltip(el) {
            if (!el) return;
            const raw = String(el.value || '').trim().replace(',', '.');
            const num = parseFloat(raw);
            if (!raw || isNaN(num)) {
                el.removeAttribute('title');
                return;
            }
            el.setAttribute('title', new Intl.NumberFormat('id-ID', { maximumFractionDigits: 4 }).format(num));
        }

        $(document).on('input change', '.input-target-val', function() {
            formatTargetTooltip(this);
        });
        document.querySelectorAll('.input-target-val').forEach(formatTargetTooltip);

        $(document).on('input change', '.input-satuan-val', function() {
            $(this).attr('title', $(this).val());
        });

        function updateRowNumbers(tabel) {
            tabel.find('tr').each(function(index) {
                const rowNum = index + 1;
                $(this).find('.nomor-baris').text(rowNum);
                $(this).find('textarea[name="sasaran_program[]"]').attr('aria-label', 'Sasaran Program Unit baris ' + rowNum);
                $(this).find('textarea[name="indikator_kinerja[]"]').attr('aria-label', 'Indikator Kinerja Individu baris ' + rowNum);
                $(this).find('input[name="target_bulanan[]"]').attr('aria-label', 'Target Kuantitatif baris ' + rowNum);
                $(this).find('input[name="satuan[]"]').attr('aria-label', 'Satuan Target baris ' + rowNum);
                $(this).find('.hapus-baris').attr('aria-label', 'Hapus target baris ' + rowNum);
            });
            updateTargetCounter(tabel.closest('form'));
        }

        function resetRowToEmpty(row) {
            const newClientId = generateClientRowId('c_row_reset');
            row.attr('data-client-id', newClientId);
            row.attr('data-approved', '0');
            row.find('input[name="client_row_id[]"]').val(newClientId);
            row.find('input[name="laporan_id[]"]').val('');
            row.find('input[type="number"]').val('');
            row.find('input[type="text"]').val('').attr('title', '');
            row.find('textarea').val('');
            autoGrowAll(row[0]);
            row.find('.status-badge')
                .removeClass('bg-success-subtle text-success-emphasis border-success-subtle bg-warning-subtle text-warning-emphasis border-warning-subtle bg-secondary-subtle text-secondary-emphasis border-secondary-subtle bg-danger-subtle text-danger-emphasis border-danger-subtle')
                .addClass('bg-primary-subtle text-primary-emphasis border border-primary-subtle')
                .text('Draf Baru');
            row.find('.hapus-baris').attr('data-id', '');
        }

        $(document).on('click', '.hapus-baris', function() {
            if (isSavingTarget) return;
            const tabel = $(this).closest('tbody');
            const row = $(this).closest('tr');
            const idLaporan = row.find('input[name="laporan_id[]"]').val() || $(this).attr('data-id');

            const animateRemoveRow = function() {
                isFormDirty = true;
                row.addClass('row-slide-out');
                setTimeout(() => {
                    if (tabel.find('tr').length > 1) {
                        row.remove();
                        updateRowNumbers(tabel);
                    } else {
                        row.removeClass('row-slide-out');
                        resetRowToEmpty(row);
                        updateRowNumbers(tabel);
                    }
                }, 220);
            };

            if (idLaporan && idLaporan.trim() !== '') {
                const doDelete = function() {
                    const csrfTokenName = '<?= csrf_token() ?>';
                    const csrfHash = $('input[name="' + csrfTokenName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';

                    const postData = { id: idLaporan };
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
                                        title: 'Terhapus',
                                        text: response.message || 'Target berhasil dihapus.',
                                        timer: (response.affected_logs && response.affected_logs > 0) ? 3500 : 1500,
                                        showConfirmButton: (response.affected_logs && response.affected_logs > 0)
                                    });
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal', response.message || 'Gagal menghapus target.', 'error');
                                } else {
                                    alert(response.message || 'Gagal menghapus target.');
                                }
                            }
                        },
                        error: function() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal', 'Gagal menghapus data. Silakan coba lagi.', 'error');
                            } else {
                                alert('Gagal menghapus data. Silakan coba lagi.');
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Target?',
                        text: 'Target ini akan dihapus. Kegiatan harian terkait akan kembali ke status draf.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDelete();
                        }
                    });
                } else {
                    if (confirm('Target ini akan dihapus. Kegiatan harian terkait akan kembali ke status draf.')) {
                        doDelete();
                    }
                }
            } else {
                animateRemoveRow();
            }
        });

        // Mode Edit Target Staf (Atasan)
        $('#btnEditStaf').on('click', function(e) {
            const isEditing = $(this).attr('type') === 'submit';
            if (!isEditing) {
                e.preventDefault();
                let unlockCount = 0;
                $('.form-target-staf .staf-input').each(function() {
                    if (!$(this).hasClass('locked-approved')) {
                        $(this).removeAttr('readonly');
                        unlockCount++;
                    }
                });

                if (unlockCount > 0) {
                    $(this).attr('type', 'submit');
                    $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                    $(this).text('Simpan & Setujui');
                    $('#btnApproveAll').hide();

                    $('#btnTambahTargetStaf').show();
                    $('.form-target-staf .col-aksi-staf').show();
                    $('.form-target-staf .btn-hapus-staf').css('display', 'inline-flex');
                    autoGrowAll($('.form-target-staf')[0]);
                    $('.form-target-staf .staf-input:not(.locked-approved)').first().focus();
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Informasi', 'Semua target sudah disetujui dan tidak dapat diubah.', 'info');
                    } else {
                        alert('Semua target sudah disetujui dan tidak dapat diubah.');
                    }
                }
            }
        });

        $(document).on('click', '.btn-tambah-baris-staf', function() {
            if (isSavingTarget) return;
            const form = $(this).closest('form');
            const tabel = form.find('.tabel-target tbody');
            if (tabel.find('tr').length >= 30) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Batas Maksimal', 'Jumlah target kinerja sudah mencapai batas maksimal 30 baris.', 'warning');
                } else {
                    alert('Jumlah target sudah mencapai batas maksimal 30 baris.');
                }
                return;
            }

            isFormDirty = true;
            const newRow = createTargetRow({}, generateClientRowId('c_row_staf'), true);
            tabel.append(newRow);
            updateRowNumbers(tabel);
            autoGrowAll(newRow[0]);

            setTimeout(() => {
                newRow.find('textarea[name="sasaran_program[]"]').focus();
            }, 80);
        });

        function checkTableDuplicates(formEl) {
            const seen = {};
            let duplicateFound = false;
            let dupInfo = null;

            formEl.find('.tabel-target tbody tr').removeClass('table-danger');

            formEl.find('.tabel-target tbody tr').each(function(idx) {
                const sasaran = ($(this).find('textarea[name="sasaran_program[]"]').val() || '').trim().replace(/\s+/g, ' ');
                const indikator = ($(this).find('textarea[name="indikator_kinerja[]"]').val() || '').trim().replace(/\s+/g, ' ');

                if (sasaran !== '' || indikator !== '') {
                    const key = (sasaran + '|||' + indikator).toLowerCase();
                    if (seen[key] !== undefined) {
                        duplicateFound = true;
                        dupInfo = {
                            firstRow: seen[key] + 1,
                            secondRow: idx + 1
                        };
                        $(this).addClass('table-danger');
                        formEl.find('.tabel-target tbody tr').eq(seen[key]).addClass('table-danger');
                        return false;
                    }
                    seen[key] = idx;
                }
            });

            return { hasDuplicate: duplicateFound, info: dupInfo };
        }

        let isTargetSubmitConfirmed = false;

        $('.form-target-sendiri').on('submit', function(e) {
            if (isTargetSubmitConfirmed) {
                isFormDirty = false;
                return true;
            }

            const formEl = $(this);
            let isValid = true;
            let hasAtLeastOne = false;
            let isTargetPositive = true;
            let invalidRow = null;

            formEl.find('.tabel-target tbody tr').each(function(idx) {
                const sasaran = $(this).find('textarea[name="sasaran_program[]"]').val().trim();
                const indikator = $(this).find('textarea[name="indikator_kinerja[]"]').val().trim();
                const target = $(this).find('input[name="target_bulanan[]"]').val().trim();
                const satuan = $(this).find('input[name="satuan[]"]').val().trim();

                if (sasaran !== '' || indikator !== '' || target !== '' || satuan !== '') {
                    hasAtLeastOne = true;
                    if (sasaran === '' || indikator === '' || target === '' || satuan === '') {
                        isValid = false;
                    }
                    if (target !== '') {
                        const targetNum = parseFloat(target.replace(',', '.'));
                        if (isNaN(targetNum) || targetNum <= 0 || targetNum >= 1000000000000000) {
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
                    Swal.fire('Target Kosong', 'Isi minimal 1 target kinerja.', 'warning');
                } else {
                    alert('Isi minimal 1 target kinerja.');
                }
                return false;
            }

            if (!isTargetPositive) {
                e.preventDefault();
                const errMsg = invalidRow ? `Baris ${invalidRow}: Target harus lebih dari 0 dan di bawah 1.000 triliun.` : 'Target harus lebih dari 0 dan di bawah 1.000 triliun.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Target Tidak Sesuai', errMsg, 'warning');
                } else {
                    alert(errMsg);
                }
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                const errMsg = 'Lengkapi seluruh kolom sasaran, indikator, target, dan satuan.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Data Belum Lengkap', errMsg, 'warning');
                } else {
                    alert(errMsg);
                }
                return false;
            }

            const dupCheck = checkTableDuplicates(formEl);
            if (dupCheck.hasDuplicate) {
                e.preventDefault();
                const d = dupCheck.info;
                const msg = `Terdapat duplikasi RHK dan Indikator pada baris ke-${d.firstRow} dan ke-${d.secondRow}.`;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Target Duplikat',
                        text: msg,
                        confirmButtonText: 'Periksa'
                    });
                } else {
                    alert(msg);
                }
                return false;
            }

            e.preventDefault();

            const isDirektur = <?= (session()->get('role') === 'direktur') ? 'true' : 'false' ?>;
            const isAlreadySubmitted = <?= ($hasTerkirim && !$hasDraft && !$isDirektur) ? 'true' : 'false' ?>;
            const confirmTitle = isDirektur ? 'Simpan Target?' : (isAlreadySubmitted ? 'Ajukan Ulang Target?' : 'Ajukan Target?');
            const confirmText = isDirektur
                ? 'Target periode <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?> akan disimpan dan disetujui.'
                : (isAlreadySubmitted
                    ? 'Target periode <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?> akan diajukan ulang ke atasan.'
                    : 'Target periode <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?> akan diajukan ke atasan.');
            const confirmBtnText = isDirektur ? 'Simpan' : (isAlreadySubmitted ? 'Ajukan Ulang' : 'Ajukan');
            const loadingText = isDirektur
                ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...'
                : (isAlreadySubmitted
                    ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memperbarui...'
                    : '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengajukan...');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: confirmTitle,
                    text: confirmText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: confirmBtnText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isTargetSubmitConfirmed = true;
                        isFormDirty = false;
                        const submitBtn = formEl.find('button[type="submit"]');
                        if (submitBtn.length) {
                            submitBtn.html(loadingText).prop('disabled', true);
                        }
                        $('#btnSimpanSementara').prop('disabled', true);
                        formEl[0].submit();
                    }
                });
            } else {
                if (confirm(confirmText)) {
                    isTargetSubmitConfirmed = true;
                    isFormDirty = false;
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

        $('.form-target-staf').on('submit', function() {
            isFormDirty = false;
            $('#btnApproveAll, #btnEditStaf').prop('disabled', true);
            const activeBtn = $(document.activeElement);
            if (activeBtn.is('#btnApproveAll')) {
                activeBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyetujui...');
            } else if (activeBtn.is('#btnEditStaf')) {
                activeBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...');
            }
        });

        // Simpan Sementara (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            if (isSavingTarget) return;
            const btn = $(this);
            const originalText = btn.html();
            const form = $('.form-target-sendiri');

            const dupCheck = checkTableDuplicates(form);
            if (dupCheck.hasDuplicate) {
                const d = dupCheck.info;
                const msg = `Terdapat duplikasi RHK dan Indikator pada baris ke-${d.firstRow} dan ke-${d.secondRow}.`;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Target Duplikat',
                        text: msg,
                        confirmButtonText: 'Periksa'
                    });
                } else {
                    alert(msg);
                }
                return;
            }

            setTargetFormSavingState(form, true);
            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...');

            form.find('input[name="action"]').val('draft');
            $.ajax({
                url: '<?= site_url('laporan-harian/store') ?>',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                complete: function() {
                    form.find('input[name="action"]').val('submit');
                    setTargetFormSavingState(form, false);
                },
                success: function(response) {
                    if (response.csrf_hash) {
                        updateAllCsrfTokens(response.csrf_hash);
                    }

                    if (response.success) {
                        isFormDirty = false;
                        btn.html('Tersimpan Draf').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        $('#containerDeletedTargetIdsSendiri').empty();

                        if (response.new_ids) {
                            for (const clientId in response.new_ids) {
                                const newId = response.new_ids[clientId];
                                const row = form.find('.tabel-target tbody tr[data-client-id="' + clientId + '"]');
                                if (row.length) {
                                    row.find('input[name="laporan_id[]"]').val(newId);
                                    row.find('.hapus-baris').attr('data-id', newId);
                                    const badge = row.find('.status-badge');
                                    if (badge.text().includes('Draf Baru')) {
                                        badge.removeClass('bg-primary-subtle text-primary-emphasis border-primary-subtle')
                                             .addClass('bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle')
                                             .text('Draf');
                                    }
                                }
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan',
                                text: response.message || 'Draf berhasil disimpan.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }

                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary');
                        }, 2000);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal menyimpan target.', 'error');
                        } else {
                            alert(response.message || 'Gagal menyimpan target. Silakan coba lagi.');
                        }
                        btn.html(originalText);
                    }
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', 'Gagal menyimpan target. Silakan coba lagi.', 'error');
                    } else {
                        alert('Gagal menyimpan target. Silakan coba lagi.');
                    }
                    btn.html(originalText);
                }
            });
        });

        // Batalkan Persetujuan Target Bulanan (Admin)
        $(document).on('click', '.btn-batal-approve-target', function(e) {
            e.preventDefault();
            const stafId    = $(this).data('staf-id');
            const bulan     = $(this).data('bulan');
            const tahun     = $(this).data('tahun');
            const csrfName  = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';

            if (!stafId || !bulan || !tahun) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', 'Data pegawai atau periode tidak valid.', 'error');
                } else {
                    alert('Data pegawai atau periode tidak valid.');
                }
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
                                    title: 'Berhasil',
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
                                Swal.fire('Gagal', response.message || 'Gagal membatalkan persetujuan.', 'error');
                            } else {
                                alert(response.message || 'Gagal membatalkan persetujuan.');
                            }
                        }
                    },
                    error: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', 'Gagal memproses data. Silakan coba lagi.', 'error');
                        } else {
                            alert('Gagal memproses data. Silakan coba lagi.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batalkan Persetujuan?',
                    text: 'Target bulanan dan kegiatan terkait akan kembali ke draf, serta nilai penilaian periode ini akan direset.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeCancel();
                    }
                });
            } else {
                if (confirm('Target bulanan dan kegiatan terkait akan kembali ke draf, serta nilai penilaian periode ini akan direset.')) {
                    executeCancel();
                }
            }
        });

        // Salin Target dari Periode Lain
        $('#btnEksekusiSalinTarget').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            const bulanSumber = $('#salinBulanSumber').val();
            const tahunSumber = $('#salinTahunSumber').val();
            const modeSalin = $('input[name="modeSalinTarget"]:checked').val() || 'replace';
            const form = $('.form-target-sendiri');
            const tabel = form.find('.tabel-target tbody');
            const deletedContainer = $('#containerDeletedTargetIdsSendiri');
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfHash = $('input[name="' + csrfTokenName + '"]').first().val() || $('input[name="csrf_test_name"]').first().val() || '<?= csrf_hash() ?>';

            if (!bulanSumber || !tahunSumber) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Peringatan', 'Pilih bulan dan tahun sumber yang valid.', 'warning');
                } else {
                    alert('Pilih bulan dan tahun sumber yang valid.');
                }
                return;
            }

            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengambil...').prop('disabled', true);

            const postData = {
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
                                title: 'Tidak Ada Data',
                                text: response.message || 'Tidak ada data target pada periode tersebut.'
                            });
                        } else {
                            alert(response.message);
                        }
                        return;
                    }

                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        const modalEl = document.getElementById('modalSalinTarget');
                        if (modalEl) {
                            const modalObj = bootstrap.Modal.getInstance(modalEl);
                            if (modalObj) modalObj.hide();
                        }

                        const barisSaatIni = tabel.find('tr');
                        let isCurrentTableEmpty = false;
                        if (barisSaatIni.length === 1) {
                            const firstRow = barisSaatIni.first();
                            const sasaran = (firstRow.find('textarea[name="sasaran_program[]"]').val() || '').trim();
                            const indikator = (firstRow.find('textarea[name="indikator_kinerja[]"]').val() || '').trim();
                            const targetVal = (firstRow.find('input[name="target_bulanan[]"]').val() || '').trim();
                            const idVal = (firstRow.find('input[name="laporan_id[]"]').val() || '').trim();
                            if (sasaran === '' && indikator === '' && targetVal === '' && idVal === '') {
                                isCurrentTableEmpty = true;
                            }
                        }

                        const existingTargetKeys = new Set();
                        let keptApprovedCount = 0;

                        if (modeSalin === 'replace' && !isCurrentTableEmpty) {
                            barisSaatIni.each(function() {
                                const row = $(this);
                                const isApproved = row.attr('data-approved') === '1' || row.find('.locked-approved').length > 0;
                                const existingId = (row.find('input[name="laporan_id[]"]').val() || '').trim();

                                if (isApproved) {
                                    keptApprovedCount++;
                                    const s = (row.find('textarea[name="sasaran_program[]"]').val() || '').trim().toLowerCase();
                                    const i = (row.find('textarea[name="indikator_kinerja[]"]').val() || '').trim().toLowerCase();
                                    if (s !== '' || i !== '') {
                                        existingTargetKeys.add(s + '|||' + i);
                                    }
                                } else {
                                    if (existingId !== '') {
                                        deletedContainer.append(`<input type="hidden" name="deleted_ids[]" value="${existingId}">`);
                                    }
                                    row.remove();
                                }
                            });
                        } else if (isCurrentTableEmpty) {
                            tabel.empty();
                        } else {
                            barisSaatIni.each(function() {
                                const s = ($(this).find('textarea[name="sasaran_program[]"]').val() || '').trim().toLowerCase();
                                const i = ($(this).find('textarea[name="indikator_kinerja[]"]').val() || '').trim().toLowerCase();
                                if (s !== '' || i !== '') {
                                    existingTargetKeys.add(s + '|||' + i);
                                }
                            });
                        }

                        let insertedCount = 0;
                        let skippedDuplicates = 0;
                        let skippedQuota = 0;
                        const maxRows = 30;

                        response.data.forEach(function(item) {
                            if (tabel.find('tr').length >= maxRows) {
                                skippedQuota++;
                                return;
                            }

                            const rawSasaran = (item.sasaran_program || '').trim();
                            const rawIndikator = (item.indikator_kinerja || '').trim();
                            const itemKey = rawSasaran.toLowerCase() + '|||' + rawIndikator.toLowerCase();

                            if (existingTargetKeys.has(itemKey)) {
                                skippedDuplicates++;
                                return;
                            }

                            existingTargetKeys.add(itemKey);
                            insertedCount++;

                            const clientId = generateClientRowId('c_row_copy');
                            const newRow = createTargetRow({
                                sasaran_program: item.sasaran_program,
                                indikator_kinerja: item.indikator_kinerja,
                                target_bulanan: item.target_bulanan,
                                satuan: item.satuan
                            }, clientId, false);

                            newRow.css('animation-delay', (insertedCount * 40) + 'ms');
                            tabel.append(newRow);
                        });

                        updateRowNumbers(tabel);
                        autoGrowAll(tabel[0]);
                        isFormDirty = true;

                        let notifMsg = `Berhasil menyalin ${insertedCount} target kinerja.`;
                        if (keptApprovedCount > 0) {
                            notifMsg += ` ${keptApprovedCount} target yang disetujui tetap dipertahankan.`;
                        }
                        if (skippedDuplicates > 0) {
                            notifMsg += ` ${skippedDuplicates} target dilewati karena sudah ada.`;
                        }
                        if (skippedQuota > 0) {
                            notifMsg += ` ${skippedQuota} target dilewati karena mencapai batas 30 baris.`;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Target Disalin',
                                text: notifMsg,
                                confirmButtonText: 'Selesai'
                            });
                        } else {
                            alert(notifMsg);
                        }
                    }
                },
                error: function(xhr) {
                    btn.html(originalText).prop('disabled', false);
                    let errText = 'Gagal mengambil data target dari periode sumber.';
                    try {
                        const jsonErr = JSON.parse(xhr.responseText);
                        if (jsonErr.message) errText = jsonErr.message;
                        if (jsonErr.csrf_hash) updateAllCsrfTokens(jsonErr.csrf_hash);
                    } catch (e) {}

                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', errText, 'error');
                    } else {
                        alert(errText);
                    }
                }
            });
        });

        $(document).on('change', 'input[name="modeSalinTarget"]', function() {
            $('.modal-radio-card').removeClass('active');
            $(this).closest('.modal-radio-card').addClass('active');
        });

        // Proteksi perubahan belum tersimpan
        $(document).on('submit', 'form', function() {
            isFormDirty = false;
        });

        window.addEventListener('beforeunload', function(e) {
            if (isFormDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        $('#targetKinerjaTab button[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
            if (isFormDirty) {
                e.preventDefault();
                const targetTab = $(this);
                const proceedTabSwitch = function() {
                    isFormDirty = false;
                    targetTab.tab('show');
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Perubahan Belum Disimpan',
                        text: 'Ada perubahan target yang belum disimpan. Yakin ingin berpindah tab?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Pindah',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedTabSwitch();
                        }
                    });
                } else if (confirm('Ada perubahan target yang belum disimpan. Yakin ingin berpindah tab?')) {
                    proceedTabSwitch();
                }
            }
        });

        $(document).on('focus', '.filter-select', function() {
            $(this).data('prev-val', $(this).val());
        });

        $(document).on('change', '.filter-select', function(e) {
            if (isFormDirty) {
                e.preventDefault();
                const selectEl = $(this);
                const previousVal = selectEl.data('prev-val') || selectEl.find('option[selected]').val();

                const proceedFilter = function() {
                    isFormDirty = false;
                    selectEl.closest('form').submit();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Perubahan Belum Disimpan',
                        text: 'Ada perubahan target yang belum disimpan. Yakin ingin mengganti periode atau staf?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Ganti',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedFilter();
                        } else {
                            selectEl.val(previousVal);
                        }
                    });
                } else if (confirm('Ada perubahan target yang belum disimpan. Yakin ingin mengganti filter?')) {
                    proceedFilter();
                } else {
                    selectEl.val(previousVal);
                }
            }
        });

        $('#modalSalinTarget').on('shown.bs.modal', function() {
            $('#salinBulanSumber').focus();
        });

        updateTargetCounter($('.form-target-sendiri'));
        updateTargetCounter($('.form-target-staf'));
    });
</script>
<?= $this->endSection() ?>
