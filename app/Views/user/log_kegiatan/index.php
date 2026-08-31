<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Lapor Kegiatan Harian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .num-tabular {
        font-variant-numeric: tabular-nums;
    }
    .col-target-log { min-width: 240px; }
    .col-deskripsi-log { min-width: 260px; }
    .col-capaian-log { width: 155px; min-width: 150px; }
    .col-bukti-log { min-width: 190px; }
    
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-bento {
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        min-width: 920px;
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
    .table-bento tbody tr:not(#rowHeaderPokok):not(#rowHeaderTambahan) {
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .table-bento tbody tr:not(#rowHeaderPokok):not(#rowHeaderTambahan):hover td {
        background-color: rgba(241, 245, 249, 0.75);
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    
    .input-capaian-num::-webkit-outer-spin-button,
    .input-capaian-num::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-capaian-num {
        -moz-appearance: textfield;
    }

    /* Table Input Focus & Hover Micro-Interactions */
    .table-bento input.form-control,
    .table-bento textarea.form-control,
    .table-bento select.form-select {
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        border-radius: 8px;
    }
    .table-bento input.form-control:focus,
    .table-bento textarea.form-control:focus,
    .table-bento select.form-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        background-color: #ffffff !important;
    }

    /* Validation Error Attention Pulse */
    @keyframes invalidPulse {
        0% { transform: translateX(0); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        20% { transform: translateX(-3px); box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25); }
        40% { transform: translateX(3px); }
        60% { transform: translateX(-2px); }
        80% { transform: translateX(2px); }
        100% { transform: translateX(0); box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2); }
    }
    .form-control.is-invalid, .form-select.is-invalid {
        animation: invalidPulse 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        border-color: #dc3545 !important;
    }

    .readonly-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .readonly-box:hover {
        background-color: #ffffff;
        border-color: #cbd5e1;
    }

    /* Motion Design & Natural Deceleration Transitions */
    @keyframes rowSlideInPokok {
        0% {
            opacity: 0;
            transform: translateY(-12px) scale(0.98);
            background-color: rgba(13, 110, 253, 0.12);
        }
        60% {
            background-color: rgba(13, 110, 253, 0.05);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            background-color: transparent;
        }
    }
    .row-tugas-pokok.row-slide-in {
        animation: rowSlideInPokok 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes rowSlideInTambahan {
        0% {
            opacity: 0;
            transform: translateY(-12px) scale(0.98);
            background-color: rgba(25, 135, 84, 0.14);
        }
        60% {
            background-color: rgba(25, 135, 84, 0.06);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            background-color: transparent;
        }
    }
    .row-tugas-tambahan.row-slide-in {
        animation: rowSlideInTambahan 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

    .badge-capaian-satuan {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, color 0.2s ease;
    }
    .badge-satuan-pop {
        transform: scale(1.12);
        background-color: rgba(13, 110, 253, 0.15) !important;
        color: #0d6efd !important;
        font-weight: 700;
    }

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

    /* Flatpickr Custom Styling with Smooth Pop-in & Indicator Dots */
    .flatpickr-calendar {
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12) !important;
        font-family: 'Inter', sans-serif !important;
        overflow: hidden;
        animation: calendarPopIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes calendarPopIn {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(-8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .flatpickr-months {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding-top: 4px;
        padding-bottom: 4px;
    }
    .flatpickr-current-month {
        font-weight: 700;
        font-size: 0.95rem;
    }
    .flatpickr-weekday {
        font-weight: 700 !important;
        font-size: 0.72rem !important;
        color: #64748b !important;
    }
    .flatpickr-day {
        border-radius: 8px !important;
        position: relative !important;
        font-weight: 500;
        height: 38px;
        line-height: 32px;
        margin: 1px;
        transition: background-color 0.15s ease, transform 0.15s ease;
    }
    .flatpickr-day:hover {
        background-color: #eff6ff !important;
        transform: scale(1.04);
    }
    .flatpickr-day.selected {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 700;
        transform: scale(1.06);
    }
    .flatpickr-day.today {
        border-color: #93c5fd !important;
    }
    .flatpickr-day .fp-dot {
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        border-radius: 50%;
        pointer-events: none;
    }
    .flatpickr-day.selected .fp-dot {
        background-color: #ffffff !important;
    }
    .fp-dot-sent {
        background-color: #10b981; /* Hijau Terkirim */
    }
    .fp-dot-draft {
        background-color: #f59e0b; /* Kuning Draf */
    }
    .fp-dot-missing {
        background-color: #ef4444; /* Merah Belum Diisi */
    }
    .flatpickr-day.is-holiday,
    .flatpickr-day.is-weekend {
        color: #ef4444 !important;
        font-weight: 700;
    }
    .flatpickr-day.is-holiday.prevMonthDay,
    .flatpickr-day.is-holiday.nextMonthDay,
    .flatpickr-day.is-weekend.prevMonthDay,
    .flatpickr-day.is-weekend.nextMonthDay {
        color: #fca5a5 !important;
        opacity: 0.6;
    }
    .flatpickr-day.selected.is-holiday,
    .flatpickr-day.selected.is-weekend {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
    }
    .flatpickr-weekday:nth-child(6),
    .flatpickr-weekday:nth-child(7) {
        color: #ef4444 !important;
        font-weight: 800 !important;
    }

    /* Date Selector Button Styling - Symmetrical Bento Pro-Max */
    .date-nav-btn {
        width: 35px;
        height: 35px;
        min-width: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px !important;
        border: 1.5px solid #dbeafe !important;
        background-color: #ffffff !important;
        color: #2563eb !important;
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }
    .date-nav-btn:hover:not(.disabled) {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        color: #1d4ed8 !important;
        transform: translateY(-1.5px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.15);
    }
    .date-selector-box {
        height: 35px;
        border-radius: 10px !important;
        border: 1.5px solid #dbeafe !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .date-selector-box:hover {
        background-color: #eff6ff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.18) !important;
        transform: translateY(-1.5px);
    }
    .date-selector-box:active {
        transform: scale(0.985);
    }
    .date-btn-input {
        background-color: transparent !important;
        color: #1e3a8a !important;
        font-weight: 700 !important;
        font-size: 0.76rem !important;
        letter-spacing: 0.1px;
        cursor: pointer !important;
        padding-left: 0.4rem !important;
        padding-right: 0.2rem !important;
        height: 100% !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .date-today-btn {
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px !important;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        padding: 0 12px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .date-today-btn:hover {
        transform: translateY(-1.5px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }
    .legend-status-pill {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 50rem;
        padding: 0.35rem 0.85rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        white-space: nowrap;
        transition: box-shadow 0.2s ease;
    }
    .legend-status-pill:hover {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 600;
        font-size: 0.72rem;
        white-space: nowrap;
    }
    .legend-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .legend-item:hover .legend-dot {
        transform: scale(1.3);
    }
    .legend-dot-success {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    .legend-dot-warning {
        background-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }
    .legend-dot-danger {
        background-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    /* Proof Link Button Micro-Interaction */
    .btn-bukti-link {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-bukti-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(13, 110, 253, 0.15);
    }
    .btn-bukti-link:hover i {
        transform: translate(1px, -1px);
    }
    .btn-bukti-link i {
        display: inline-block;
        transition: transform 0.15s ease;
    }

    @media (prefers-reduced-motion: reduce) {
        .row-tugas-pokok.row-slide-in,
        .row-tugas-tambahan.row-slide-in,
        .row-slide-out,
        .badge-satuan-pop,
        .btn-tactile,
        .flatpickr-calendar,
        .date-nav-btn,
        .date-selector-box,
        .date-today-btn,
        .form-control.is-invalid,
        .form-select.is-invalid,
        .table-bento tbody tr {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-calendar-check text-primary me-2"></i>Lapor Kegiatan Harian</h1>
            <p class="text-muted small mb-0">Catat realisasi aktivitas kerja harian beserta tautan bukti pekerjaan.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-calendar-date me-1"></i> <?= date('d M Y', strtotime($tanggal_terpilih)) ?>
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

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
        <div class="card-body p-3 p-md-4">
            
            <!-- Filter Tanggal Toolbar (Interactive Symmetrical Datepicker with Status Dots) -->
            <div class="mb-3 p-3 p-md-3.5 bg-light rounded-4 border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                    <!-- Left: Date Picker & Navigation Controls -->
                    <div>
                        <label class="form-label fw-bold text-dark small mb-1.5" style="font-size: 0.73rem; letter-spacing: 0.3px;">
                            <i class="bi bi-calendar-event text-primary me-1"></i> Tanggal Kegiatan
                        </label>
                        
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php
                                $prevDay = date('Y-m-d', strtotime($tanggal_terpilih . ' -1 day'));
                                $nextDay = date('Y-m-d', strtotime($tanggal_terpilih . ' +1 day'));
                                $today   = date('Y-m-d');
                            ?>
                            <!-- Prev Day Button (Left) -->
                            <a href="<?= site_url('log-kegiatan') ?>?tanggal=<?= $prevDay ?>" class="date-nav-btn btn-tactile shadow-xs" title="Hari Sebelumnya (<?= date('d/m/Y', strtotime($prevDay)) ?>)" aria-label="Hari Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </a>

                            <!-- Main Datepicker Center Button -->
                            <div class="date-selector-wrapper position-relative" style="width: 275px; min-width: 250px;">
                                <div class="input-group input-group-sm shadow-xs rounded-3 overflow-hidden date-selector-box d-flex align-items-center px-1" id="dateSelectorGroup" title="Klik untuk memilih tanggal di kalender">
                                    <span class="input-group-text bg-primary text-white border-0 rounded-2 px-1.5 py-0.5 my-0.5">
                                        <i class="bi bi-calendar-date-fill" style="font-size: 0.76rem;"></i>
                                    </span>
                                    <input type="text" id="tanggalPicker" class="form-control border-0 fw-bold date-btn-input shadow-none h-100" value="<?= esc($tanggal_terpilih) ?>" placeholder="Pilih tanggal..." aria-label="Pilih Tanggal Kalender Kegiatan" readonly style="cursor: pointer;">
                                    <span class="input-group-text bg-transparent border-0 text-primary px-1 h-100">
                                        <i class="bi bi-chevron-down fw-bold" style="font-size: 0.68rem;"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Next Day Button (Right) -->
                            <a href="<?= ($nextDay <= $today) ? site_url('log-kegiatan') . '?tanggal=' . $nextDay : 'javascript:void(0)' ?>" class="date-nav-btn btn-tactile shadow-xs <?= ($nextDay > $today) ? 'disabled opacity-50 pe-none' : '' ?>" title="Hari Berikutnya (<?= date('d/m/Y', strtotime($nextDay)) ?>)" aria-label="Hari Berikutnya">
                                <i class="bi bi-chevron-right"></i>
                            </a>

                            <!-- Today Quick Jump Button -->
                            <?php if ($tanggal_terpilih !== $today): ?>
                                <a href="<?= site_url('log-kegiatan') ?>?tanggal=<?= $today ?>" class="btn btn-sm btn-primary date-today-btn btn-tactile shadow-xs text-nowrap ms-1" title="Kembali ke Hari Ini" aria-label="Kembali ke Hari Ini">
                                    Hari Ini
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Right: Legend Indicators (Guaranteed Single Crisp Line) -->
                    <div class="text-end pt-md-3">
                        <div class="legend-status-pill">
                            <span class="text-muted fw-bold" style="font-size: 0.72rem;">Indikator:</span>
                            <span class="legend-item text-success" title="Laporan sudah dikirim">
                                <span class="legend-dot legend-dot-success"></span>
                                <span>Terkirim</span>
                            </span>
                            <span class="legend-item text-warning-emphasis" title="Laporan masih draf">
                                <span class="legend-dot legend-dot-warning"></span>
                                <span>Draf</span>
                            </span>
                            <span class="legend-item text-danger" title="Hari kerja yang belum diisi">
                                <span class="legend-dot legend-dot-danger"></span>
                                <span>Belum Diisi</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SINGLE UNIFIED TOP ALERT NOTIFICATION -->
            <?php if (isset($target_status) && $target_status === 'belum_ada'): ?>
                <div class="alert alert-danger mb-3 shadow-sm py-2.5 px-3 small rounded-4 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger flex-shrink-0"></i>
                    <div>
                        <strong>Target Belum Ada.</strong> Silakan susun target pada menu <strong>Target Kinerja Bulanan</strong> terlebih dahulu.
                    </div>
                </div>
            <?php elseif (isset($target_status) && $target_status === 'belum_disetujui'): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-2.5 px-3 small rounded-4 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-5 text-warning-emphasis flex-shrink-0"></i>
                    <div>
                        <strong>Target Belum Disetujui.</strong> Menunggu persetujuan atasan langsung sebelum dapat mengisi laporan kegiatan harian.
                    </div>
                </div>
            <?php elseif ($is_locked): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-2.5 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 small rounded-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-lock-fill text-warning-emphasis fs-5 flex-shrink-0"></i>
                        <div>
                            <strong>Laporan Terkunci.</strong> <?= esc(!empty($lock_reason) ? $lock_reason : 'Laporan pada tanggal ini telah dikirim ke atasan dan berada dalam status terkunci.') ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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

        <!-- SINGLE UNIFIED FORM FOR BOTH TUGAS POKOK & TAMBAHAN -->
        <form action="<?= site_url('log-kegiatan/store') ?>" method="POST" id="formLog">
            <?= csrf_field() ?>
            <input type="hidden" name="tanggal" value="<?= esc($tanggal_terpilih) ?>">

            <div class="table-responsive mb-3 border rounded-4 shadow-sm bg-white">
                <table class="table table-bordered align-middle table-hover mb-0 table-bento" id="tabelLog">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">No</th>
                            <th class="col-target-log">Indikator RHK / Tugas</th>
                            <th class="col-deskripsi-log">Deskripsi Kegiatan</th>
                            <th class="col-capaian-log text-center">Capaian</th>
                            <th class="col-bukti-log">Bukti Pekerjaan</th>
                            <th style="width: 55px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ==================== BAGIAN A: TUGAS POKOK (RHK) ==================== -->
                        <tr class="table-primary text-primary fw-bold" id="rowHeaderPokok">
                            <td colspan="6" class="py-2.5 px-3 bg-primary-subtle text-primary border-primary">
                                <div class="d-flex justify-content-between align-items-center header-section-wrapper">
                                    <span class="fs-6 fw-bold"><i class="bi bi-list-task me-2"></i> A. TUGAS POKOK (RHK)</span>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBaris" class="btn btn-sm btn-primary btn-tactile rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                             <i class="bi bi-plus-circle me-1"></i> Tambah Kegiatan
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- BARIS TUGAS POKOK -->
                        <?php $rowIndex = 1; ?>
                        <?php if (!empty($rekap_data)): ?>
                            <?php foreach ($rekap_data as $row): ?>
                                <tr class="row-tugas-pokok">
                                    <td class="nomor-baris text-center fw-bold text-muted"><?= $rowIndex++ ?></td>
                                    <?php if ($is_locked || (isset($row['status']) && $row['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td>
                                            <?php 
                                            $targetName = '-';
                                            if (!empty($row['indikator_kinerja'])) {
                                                $targetName = esc($row['indikator_kinerja']) . (!empty($row['satuan']) ? ' (' . (!empty($row['target_bulanan']) ? str_replace('.', ',', (float)$row['target_bulanan']) . ' ' : '') . esc($row['satuan']) . ')' : '');
                                            } else {
                                                foreach($daftar_target as $target) {
                                                    if ($target['id'] == $row['target_id']) {
                                                        $targetName = esc($target['indikator_kinerja']) . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . esc($target['satuan']) . ')';
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <input type="hidden" class="target-select-hidden" value="<?= esc($row['target_id']) ?>">
                                            <div class="readonly-box text-dark fw-semibold"><?= $targetName ?></div>
                                        </td>
                                        <td>
                                            <div class="readonly-box text-secondary"><?= nl2br(esc($row['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="readonly-box text-center fw-bold text-primary num-tabular readonly-capaian-box" title="<?= str_replace('.', ',', (float)$row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?>">
                                                <?= str_replace('.', ',', (float)$row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['link_bukti'])): ?>
                                                <div class="text-center">
                                                    <a href="<?= esc($row['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-light text-primary border rounded-pill px-3 py-1 btn-bukti-link btn-tactile shadow-xs" title="Buka tautan bukti pekerjaan di tab baru">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Bukti
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="readonly-box text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning fs-6" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_id[]" value="<?= $row['id'] ?>">
                                        <td>
                                            <select name="target_id[]" class="form-select form-select-sm" aria-label="Pilih Target RHK baris <?= $rowIndex ?>">
                                                <option value="">-- Pilih Target / RHK --</option>
                                                 <?php 
                                                 $foundSelected = false;
                                                 foreach($daftar_target as $target): 
                                                     $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                                     $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                                     $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                                     $selected = ($target['id'] == $row['target_id']) ? 'selected' : '';
                                                     if ($selected) $foundSelected = true;
                                                 ?>
                                                     <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>" <?= $selected ?>><?= $labelTarget ?></option>
                                                 <?php endforeach; ?>
                                                 <?php if (!$foundSelected && !empty($row['target_id']) && !empty($row['indikator_kinerja'])): ?>
                                                     <option value="<?= esc($row['target_id']) ?>" data-satuan="<?= esc($row['satuan'] ?? '') ?>" selected><?= esc($row['indikator_kinerja']) ?> (<?= esc($row['satuan'] ?? '') ?>)</option>
                                                 <?php endif; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok baris <?= $rowIndex ?>"><?= esc($row['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" step="any" min="0.0001" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" value="<?= (float)$row['jumlah_capaian'] ?>" aria-label="Jumlah Capaian Pokok baris <?= $rowIndex ?>">
                                                <span class="input-group-text badge-capaian-satuan bg-light" title="<?= esc($row['satuan'] ?? '-') ?>"><?= esc($row['satuan'] ?? '-') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($row['link_bukti']) ?>" aria-label="Tautan Bukti Pekerjaan Pokok baris <?= $rowIndex ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile" data-id="<?= $row['id'] ?>" title="Hapus baris" aria-label="Hapus kegiatan pokok baris <?= $rowIndex ?>" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!$is_locked && empty($rekap_data)): ?>
                            <!-- Baris Input Baru Default Tugas Pokok -->
                            <tr class="row-tugas-pokok input-row">
                                <input type="hidden" name="log_id[]" value="">
                                <td class="nomor-baris text-center fw-bold text-muted">1</td>
                                <td>
                                    <select name="target_id[]" class="form-select form-select-sm" aria-label="Pilih Target RHK baris 1">
                                        <option value="">-- Pilih Target / RHK --</option>
                                        <?php foreach($daftar_target as $target): ?>
                                             <?php 
                                             $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                             $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                             $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                             ?>
                                             <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                                         <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok baris 1"></textarea>
                                </td>
                                <td class="col-capaian-log">
                                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                        <input type="number" step="any" min="0.0001" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" aria-label="Jumlah Capaian Pokok baris 1">
                                        <span class="input-group-text badge-capaian-satuan bg-light">-</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Pokok baris 1">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile" title="Hapus baris baru" aria-label="Hapus kegiatan pokok baris 1" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        <?php endif; ?>


                        <!-- ==================== BAGIAN B: SEPARATOR TUGAS TAMBAHAN ==================== -->
                        <tr class="table-light text-dark fw-bold" id="rowHeaderTambahan">
                            <td colspan="6" class="py-2.5 px-3 bg-light border-top border-bottom border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center header-section-wrapper">
                                    <div>
                                        <i class="bi bi-journal-plus me-2 text-success"></i> 
                                        <span class="text-dark fs-6 fw-bold">B. TUGAS TAMBAHAN</span>
                                    </div>
                                    <?php if (!$is_locked): ?>
                                        <button type="button" id="tambahBarisTambahan" class="btn btn-sm btn-outline-success btn-tactile rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                            <i class="bi bi-plus-circle me-1"></i> Tambah Tugas
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- BARIS TUGAS TAMBAHAN -->
                        <?php $rowTmbIndex = 1; ?>
                        <?php if (!empty($rekap_data_tambahan)): ?>
                            <?php foreach ($rekap_data_tambahan as $rowTmb): ?>
                                <tr class="row-tugas-tambahan">
                                    <td class="nomor-baris-tmb text-center fw-bold text-muted"><?= $rowTmbIndex++ ?></td>
                                    <?php if ($is_locked || (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim')): ?>
                                        <!-- Read-only View -->
                                        <td class="align-middle">
                                            <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <div class="readonly-box text-secondary"><?= nl2br(esc($rowTmb['deskripsi_kegiatan'])) ?></div>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="readonly-box text-center fw-bold text-success num-tabular readonly-capaian-box" title="<?= (isset($rowTmb['jumlah_capaian']) && $rowTmb['jumlah_capaian'] !== null && $rowTmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>">
                                                <?= (isset($rowTmb['jumlah_capaian']) && $rowTmb['jumlah_capaian'] !== null && $rowTmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($rowTmb['link_bukti'])): ?>
                                                <div class="text-center">
                                                    <a href="<?= esc($rowTmb['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-light text-primary border rounded-pill px-3 py-1 btn-bukti-link btn-tactile shadow-xs" title="Buka tautan bukti pekerjaan di tab baru">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> Bukti
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="readonly-box text-center text-muted">-</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-success">
                                            <i class="bi bi-lock-fill text-warning fs-6" title="Terkunci"></i>
                                        </td>
                                    <?php else: ?>
                                        <!-- Editable Draft View -->
                                        <input type="hidden" name="log_tambahan_id[]" value="<?= $rowTmb['id'] ?>">
                                        <td class="align-middle">
                                            <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                                <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan tugas tambahan Anda..." aria-label="Deskripsi Kegiatan Tambahan baris <?= $rowTmbIndex ?>"><?= esc($rowTmb['deskripsi_kegiatan']) ?></textarea>
                                        </td>
                                        <td class="col-capaian-log">
                                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                                <input type="number" step="any" min="0.0001" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success" placeholder="0" value="<?= isset($rowTmb['jumlah_capaian']) ? (float)$rowTmb['jumlah_capaian'] : '' ?>" aria-label="Jumlah Capaian Tambahan baris <?= $rowTmbIndex ?>">
                                                <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val text-center" placeholder="Satuan" list="daftarSatuanStandar" value="<?= esc($rowTmb['satuan'] ?? '') ?>" title="<?= esc($rowTmb['satuan'] ?? '') ?>" aria-label="Satuan Capaian Tambahan baris <?= $rowTmbIndex ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($rowTmb['link_bukti']) ?>" aria-label="Tautan Bukti Pekerjaan Tambahan baris <?= $rowTmbIndex ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb btn-tactile" data-id="<?= $rowTmb['id'] ?>" title="Hapus baris" aria-label="Hapus tugas tambahan baris <?= $rowTmbIndex ?>" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <!-- SINGLE UNIFIED BUTTON TOOLBAR AT BOTTOM -->
            <?php if (!$is_locked): ?>
            <div class="d-flex justify-content-end align-items-center mt-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                <button type="button" id="btnSimpanSementara" class="btn btn-outline-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-semibold">
                    <i class="bi bi-cloud-arrow-up me-1.5"></i> Simpan Draf
                </button>
                <button type="submit" class="btn btn-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-bold">
                    <i class="bi bi-send me-1.5"></i> Kirim Laporan
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const tabelLog = $('#tabelLog tbody');

        // Cegah perubahan nilai angka secara tidak sengaja saat pengguna scrolling halaman dengan mouse wheel
        $(document).on('wheel', 'input[type="number"]', function (e) {
            $(this).blur();
        });

        function updateRowNumbers() {
            tabelLog.find('tr.row-tugas-pokok').each(function(index) {
                const num = index + 1;
                $(this).find('.nomor-baris').text(num);
                $(this).find('select[name="target_id[]"]').attr('aria-label', `Pilih Target RHK baris ${num}`);
                $(this).find('textarea[name="deskripsi_kegiatan[]"]').attr('aria-label', `Deskripsi Kegiatan Pokok baris ${num}`);
                $(this).find('input[name="jumlah_capaian[]"]').attr('aria-label', `Jumlah Capaian Pokok baris ${num}`);
                $(this).find('input[name="link_bukti[]"]').attr('aria-label', `Tautan Bukti Pekerjaan Pokok baris ${num}`);
                $(this).find('.hapus-baris').attr('aria-label', `Hapus kegiatan pokok baris ${num}`);
            });
            tabelLog.find('tr.row-tugas-tambahan').each(function(index) {
                const num = index + 1;
                $(this).find('.nomor-baris-tmb').text(num);
                $(this).find('textarea[name="deskripsi_kegiatan_tambahan[]"]').attr('aria-label', `Deskripsi Kegiatan Tambahan baris ${num}`);
                $(this).find('input[name="jumlah_capaian_tambahan[]"]').attr('aria-label', `Jumlah Capaian Tambahan baris ${num}`);
                $(this).find('input[name="satuan_tambahan[]"]').attr('aria-label', `Satuan Capaian Tambahan baris ${num}`);
                $(this).find('input[name="link_bukti_tambahan[]"]').attr('aria-label', `Tautan Bukti Pekerjaan Tambahan baris ${num}`);
                $(this).find('.hapus-baris-tmb').attr('aria-label', `Hapus tugas tambahan baris ${num}`);
            });
        }

        function updateDropdownOptions() {
            // Mengizinkan 1 RHK dipilih untuk lebih dari 1 baris kegiatan harian dalam 1 hari
            tabelLog.find('select[name="target_id[]"]').each(function() {
                $(this).find('option').prop('disabled', false).show();
            });
        }

        // Tambah Baris Tugas Pokok dengan Fluid Animation & Auto-Focus
        $('#tambahBaris').on('click', function() {
            const templateRow = tabelLog.find('tr.row-tugas-pokok:first');
            let newRow;

            if (templateRow.length > 0 && templateRow.find('select[name="target_id[]"]').length > 0) {
                newRow = templateRow.clone();
                newRow.removeClass('row-slide-out').addClass('row-slide-in');
                newRow.find('input[name="log_id[]"]').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('input[type="url"]').val('');
                newRow.find('textarea').val('');
                newRow.find('select').val('');
                newRow.find('.input-group-text').text('-');
                newRow.find('.hapus-baris').removeAttr('data-id');
                newRow.find('.is-invalid').removeClass('is-invalid');
            } else {
                newRow = $(`
                <tr class="row-tugas-pokok input-row row-slide-in">
                    <input type="hidden" name="log_id[]" value="">
                    <td class="nomor-baris text-center fw-bold text-muted">1</td>
                    <td>
                        <select name="target_id[]" class="form-select form-select-sm" aria-label="Pilih Target RHK baris">
                            <option value="">-- Pilih Target / RHK --</option>
                            <?php foreach($daftar_target as $target): ?>
                                <?php 
                                $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                $labelTarget = $cleanIndikator . ' (' . str_replace('.', ',', (float)$target['target_bulanan']) . ' ' . $cleanSatuan . ')'; 
                                ?>
                                <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok baris"></textarea>
                    </td>
                    <td class="col-capaian-log">
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <input type="number" step="0.01" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" aria-label="Jumlah Capaian Pokok baris">
                            <span class="input-group-text badge-capaian-satuan bg-light">-</span>
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Pokok baris">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile" title="Hapus baris" aria-label="Hapus kegiatan pokok baris" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`);
            }
            
            $('#rowHeaderTambahan').before(newRow);
            updateRowNumbers();
            updateDropdownOptions();

            setTimeout(() => {
                $(newRow).find('select[name="target_id[]"]').focus();
            }, 80);
        });

        // Hapus Baris Tugas Pokok dengan Smooth Animation
        tabelLog.on('click', '.hapus-baris', function() {
            const pokokRows = tabelLog.find('tr.row-tugas-pokok');
            const idLog = $(this).attr('data-id');
            const row = $(this).closest('tr');

            // Cek apakah baris ini kosong (tidak ada target/deskripsi terisi)
            const targetVal = row.find('select[name="target_id[]"]').val();
            const deskripsiVal = row.find('textarea[name="deskripsi_kegiatan[]"]').val();
            const isRowEmpty = !targetVal && (!deskripsiVal || !deskripsiVal.trim());

            // Cek apakah ada Tugas Tambahan sebagai alternatif
            const tambahanRows = tabelLog.find('tr.row-tugas-tambahan');
            const hasTambahan = tambahanRows.length > 0;

            // Izinkan hapus jika: ada >1 baris Pokok, ATAU baris terakhir kosong, ATAU sudah ada Tugas Tambahan
            const canDelete = pokokRows.length > 1 || isRowEmpty || hasTambahan;

            const animateRemoveRow = function() {
                row.addClass('row-slide-out');
                setTimeout(() => {
                    row.remove();
                    updateRowNumbers();
                    updateDropdownOptions();
                }, 220);
            };

            if (canDelete) {
                if (idLog) {
                    const doDeletePokok = function() {
                        let csrfTokenName = '<?= csrf_token() ?>';
                        let csrfHash = $('input[name="' + csrfTokenName + '"]').val() || '<?= csrf_hash() ?>';
                        let postData = { id: idLog };
                        postData[csrfTokenName] = csrfHash;

                        $.ajax({
                            url: '<?= site_url('log-kegiatan/hapus') ?>',
                            type: 'POST',
                            data: postData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.csrf_hash) {
                                    $('input[name="' + csrfTokenName + '"]').val(response.csrf_hash);
                                    $('input[name="csrf_test_name"]').val(response.csrf_hash);
                                }
                                if (response.success) {
                                    animateRemoveRow();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus',
                                        text: 'Catatan kegiatan harian berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message || 'Gagal menghapus data.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Hapus Log Error:', xhr.responseText);
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.', 'error');
                            }
                        });
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Kegiatan Harian?',
                            text: 'Apakah Anda yakin ingin menghapus catatan kegiatan ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Kegiatan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                doDeletePokok();
                            }
                        });
                    } else {
                        if (confirm('Apakah Anda yakin ingin menghapus catatan kegiatan ini?')) {
                            doDeletePokok();
                        }
                    }
                } else {
                    animateRemoveRow();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Pemberitahuan', 'Minimal harus ada 1 kegiatan (Tugas Pokok atau Tugas Tambahan) yang dilaporkan.', 'info');
                } else {
                    alert('Minimal harus ada 1 kegiatan (Tugas Pokok atau Tugas Tambahan) yang dilaporkan.');
                }
            }
        });

        // Tambah Baris Tugas Tambahan dengan Fluid Animation & Auto-Focus
        $('#tambahBarisTambahan').on('click', function() {
            const templateRowTmb = tabelLog.find('tr.row-tugas-tambahan:first');
            let newRow;

            if (templateRowTmb.length > 0 && templateRowTmb.find('textarea[name="deskripsi_kegiatan_tambahan[]"]').length > 0) {
                newRow = templateRowTmb.clone();
                newRow.removeClass('row-slide-out').addClass('row-slide-in');
                newRow.find('input[name="log_tambahan_id[]"]').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('input[name="satuan_tambahan[]"]').val('').attr('title', '');
                newRow.find('input[type="url"]').val('');
                newRow.find('textarea').val('');
                newRow.find('.hapus-baris-tmb').removeAttr('data-id');
                newRow.find('.is-invalid').removeClass('is-invalid');
            } else {
                newRow = $(`
                <tr class="row-tugas-tambahan row-slide-in">
                    <input type="hidden" name="log_tambahan_id[]" value="">
                    <td class="nomor-baris-tmb text-center fw-bold text-muted">1</td>
                    <td class="align-middle">
                        <div class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                            <i class="bi bi-journal-plus me-1"></i> Tugas Tambahan
                        </div>
                    </td>
                    <td>
                        <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm" rows="2" placeholder="Jelaskan tugas tambahan yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Tambahan baris"></textarea>
                    </td>
                    <td class="col-capaian-log">
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <input type="number" step="0.01" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success" placeholder="0" aria-label="Jumlah Capaian Tambahan baris">
                            <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val text-center" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Capaian Tambahan baris">
                        </div>
                    </td>
                    <td>
                        <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Tambahan baris">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb btn-tactile" title="Hapus" aria-label="Hapus tugas tambahan baris" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`);
            }
            tabelLog.append(newRow);
            updateRowNumbers();

            setTimeout(() => {
                $(newRow).find('textarea[name="deskripsi_kegiatan_tambahan[]"]').focus();
            }, 80);
        });

        // Dynamic title update saat mengetik satuan tambahan
        $(document).on('input change', '.input-satuan-val', function() {
            $(this).attr('title', $(this).val());
        });

        // Hapus Baris Tugas Tambahan dengan Smooth Animation
        tabelLog.on('click', '.hapus-baris-tmb', function() {
            const tr = $(this).closest('tr');
            const logId = $(this).data('id');

            const animateRemoveTmb = function() {
                tr.addClass('row-slide-out');
                setTimeout(() => {
                    tr.remove();
                    updateRowNumbers();
                }, 220);
            };

            if (logId) {
                const doDeleteTambahan = function() {
                    const csrfToken = $('input[name="<?= csrf_token() ?>"]').val() || $('input[name="csrf_test_name"]').val();
                    $.ajax({
                        url: '<?= site_url("log-kegiatan/hapusTugasTambahan") ?>',
                        type: 'POST',
                        data: {
                            id: logId,
                            '<?= csrf_token() ?>': csrfToken
                        },
                        success: function(response) {
                            if (response.csrf_hash) {
                                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                                $('input[name="csrf_test_name"]').val(response.csrf_hash);
                            }
                            if(response.success) {
                                animateRemoveTmb();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: 'Data tugas tambahan berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert('Data tugas tambahan berhasil dihapus.');
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal!', response.message || 'Gagal menghapus.', 'error');
                                } else {
                                    alert('Gagal: ' + (response.message || 'Gagal menghapus.'));
                                }
                            }
                        },
                        error: function() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            } else {
                                alert('Terjadi kesalahan sistem.');
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Tugas Tambahan?',
                        text: "Data yang sudah disimpan akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Tugas',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDeleteTambahan();
                        }
                    });
                } else {
                    if (confirm('Hapus Tugas Tambahan? Data yang sudah disimpan akan dihapus permanen.')) {
                        doDeleteTambahan();
                    }
                }
            } else {
                animateRemoveTmb();
            }
        });

        // Auto update satuan text based on selected target with pop animation
        tabelLog.on('change', 'select[name="target_id[]"]', function() {
            let selectedOption = $(this).find('option:selected');
            let satuan = selectedOption.data('satuan') || selectedOption.attr('data-satuan');
            if (!satuan) {
                let selectedText = selectedOption.text();
                let matches = selectedText.match(/\((\d+(?:[\.,]\d+)?)\s+(.+)\)$/);
                satuan = matches ? matches[2] : '-';
            }
            const badge = $(this).closest('tr').find('.badge-capaian-satuan');
            badge.text(satuan || '-').addClass('badge-satuan-pop');
            setTimeout(() => {
                badge.removeClass('badge-satuan-pop');
            }, 200);
            updateDropdownOptions();
        });

        updateDropdownOptions();

        // Validasi Form saat klik "Simpan & Kirim Laporan Harian" (Form Submit Normal)
        $('#formLog').on('submit', function(e) {
            // Bersihkan semua hint highlight merah sebelumnya
            $('#formLog .is-invalid').removeClass('is-invalid');

            let isValid = true;
            let hasPokok = false;
            let hasTambahan = false;
            let errorHints = [];

            // Validasi Tugas Pokok
            $('#formLog tr.row-tugas-pokok').each(function(idx) {
                let rowNum = idx + 1;
                let targetElem = $(this).find('select[name="target_id[]"]');
                let deskripsiElem = $(this).find('textarea[name="deskripsi_kegiatan[]"]');
                let capaianElem = $(this).find('input[name="jumlah_capaian[]"]');
                let linkElem = $(this).find('input[name="link_bukti[]"]');

                let targetId = targetElem.val() ? targetElem.val().trim() : '';
                let deskripsi = deskripsiElem.val() ? deskripsiElem.val().trim() : '';
                let capaian = capaianElem.val() ? capaianElem.val().trim() : '';
                let link = linkElem.val() ? linkElem.val().trim() : '';

                // Jika salah satu kolom di baris ini terisi
                if (targetId || deskripsi || capaian !== '' || link) {
                    hasPokok = true;
                    let missingCols = [];

                    if (!targetId) {
                        targetElem.addClass('is-invalid');
                        missingCols.push('Target RHK');
                    }
                    if (!deskripsi) {
                        deskripsiElem.addClass('is-invalid');
                        missingCols.push('Deskripsi Kegiatan');
                    }
                    let capaianVal = parseFloat(capaian.replace(',', '.'));
                    if (capaian === '' || isNaN(capaianVal) || capaianVal <= 0) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push(capaianVal <= 0 ? 'Jumlah Capaian (> 0)' : 'Jumlah Capaian');
                    }
                    if (!link || link === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Link Bukti Pekerjaan');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Pokok Baris ke-${rowNum}</b>: Kolom <i>${missingCols.join(', ')}</i> belum terisi.`);
                    }
                }
            });

            // Validasi Tugas Tambahan
            $('#formLog tr.row-tugas-tambahan').each(function(idx) {
                let rowNum = idx + 1;
                let deskripsiElem = $(this).find('textarea[name="deskripsi_kegiatan_tambahan[]"]');
                let capaianElem = $(this).find('input[name="jumlah_capaian_tambahan[]"]');
                let linkElem = $(this).find('input[name="link_bukti_tambahan[]"]');

                let deskripsiTmb = deskripsiElem.val() ? deskripsiElem.val().trim() : '';
                let capaianTmb = capaianElem.val() ? capaianElem.val().trim() : '';
                let linkTmb = linkElem.val() ? linkElem.val().trim() : '';

                if (deskripsiTmb || capaianTmb !== '' || linkTmb) {
                    hasTambahan = true;
                    let missingCols = [];

                    if (!deskripsiTmb) {
                        deskripsiElem.addClass('is-invalid');
                        missingCols.push('Deskripsi Kegiatan');
                    }
                    let capaianValTmb = parseFloat(capaianTmb.replace(',', '.'));
                    if (capaianTmb === '' || isNaN(capaianValTmb) || capaianValTmb <= 0) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push(capaianValTmb <= 0 ? 'Jumlah Capaian (> 0)' : 'Jumlah Capaian');
                    }
                    if (!linkTmb || linkTmb === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Link Bukti Pekerjaan');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Tambahan Baris ke-${rowNum}</b>: Kolom <i>${missingCols.join(', ')}</i> belum terisi.`);
                    }
                }
            });

            if (!hasPokok && !hasTambahan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Laporan Masih Kosong',
                    text: 'Silakan isi minimal satu kegiatan pada Tugas Pokok atau Tugas Tambahan sebelum mengirim ke atasan langsung.'
                });
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                let hintHtml = `<div class="text-start small mt-2"><p class="mb-2 text-danger fw-bold">Beberapa kolom belum lengkap sebelum pengiriman:</p><ul class="ps-3 mb-0">` +
                               errorHints.map(h => `<li class="mb-1">${h}</li>`).join('') +
                               `</ul><p class="mt-2 text-muted mb-0" style="font-size:0.8rem;">💡 <i>Tips: Jika belum selesai diisi, Anda dapat menekan tombol <b>Simpan Sementara</b> terlebih dahulu.</i></p></div>`;
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    html: hintHtml,
                    confirmButtonText: 'Saya Mengerti'
                });
                return false;
            }

            // Hardening: Double submission prevention
            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim Laporan...').prop('disabled', true);
            $('#btnSimpanSementara').prop('disabled', true);
        });

        // Single Unified Submit "Simpan Sementara" (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            let btn = $(this);
            let originalText = btn.html();
            
            btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: '<?= site_url('log-kegiatan/store') ?>',
                type: 'POST',
                data: $('#formLog').serialize() + '&action=draft',
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                        $('input[name="csrf_test_name"]').val(response.csrf_hash);
                    }

                    if (response.success) {
                        btn.html('<i class="bi bi-check-lg me-2"></i> Tersimpan Draf').removeClass('btn-outline-primary').addClass('btn-outline-success');
                        
                        if (response.new_ids) {
                            tabelLog.find('tr.row-tugas-pokok').each(function(idx) {
                                let inputHidden = $(this).find('input[name="log_id[]"]');
                                let btnHapus = $(this).find('.hapus-baris');
                                if (response.new_ids[idx]) {
                                    inputHidden.val(response.new_ids[idx]);
                                    btnHapus.attr('data-id', response.new_ids[idx]);
                                }
                            });
                        }

                        if (response.new_tambahan_ids) {
                            tabelLog.find('tr.row-tugas-tambahan').each(function(jdx) {
                                let inputHidden = $(this).find('input[name="log_tambahan_id[]"]');
                                let btnHapus = $(this).find('.hapus-baris-tmb');
                                if (response.new_tambahan_ids[jdx]) {
                                    inputHidden.val(response.new_tambahan_ids[jdx]);
                                    btnHapus.attr('data-id', response.new_tambahan_ids[jdx]);
                                }
                            });
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan Draf',
                            text: response.message || 'Laporan harian & tugas tambahan berhasil disimpan sementara.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary').prop('disabled', false);
                        }, 2500);
                    } else {
                        Swal.fire('Gagal', response.message || 'Gagal menyimpan.', 'error');
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire('Error', 'Terjadi kesalahan jaringan atau server. Silakan coba lagi.', 'error');
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // =============================================
        // [FLATPICKR DATEPICKER WITH COLORED STATUS DOTS]
        // =============================================
        const dateStatusMap = <?= json_encode($date_status_map ?? []) ?>;
        const holidayMap    = <?= json_encode($holiday_map ?? []) ?>;
        const todayStr      = '<?= date('Y-m-d') ?>';

        if (typeof flatpickr !== 'undefined' && document.getElementById('tanggalPicker')) {
            const fpInstance = flatpickr("#tanggalPicker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "l, j F Y",
                altInputClass: "form-control border-0 fw-bold date-btn-input shadow-none",
                defaultDate: "<?= esc($tanggal_terpilih) ?>",
                maxDate: "today",
                locale: "id",
                disableMobile: true,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                    const dayOfWeek = dayElem.dateObj.getDay(); // 0 = Minggu, 6 = Sabtu
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    const isHoliday = !!holidayMap[dateStr];
                    const isWorkingDay = (!isWeekend && !isHoliday);
                    const isPastOrToday = (dateStr <= todayStr);
                    
                    if (isHoliday) {
                        dayElem.classList.add('is-holiday');
                        dayElem.setAttribute('title', holidayMap[dateStr]);
                    } else if (isWeekend) {
                        dayElem.classList.add('is-weekend', 'is-holiday');
                        dayElem.setAttribute('title', dayOfWeek === 0 ? 'Minggu' : 'Sabtu');
                    }

                    const status = dateStatusMap[dateStr];
                    if (status === 'terkirim') {
                        dayElem.classList.add('has-log-sent');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-sent" title="Laporan Terkirim"></span>';
                    } else if (status === 'draft') {
                        dayElem.classList.add('has-log-draft');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-draft" title="Draf Tersimpan"></span>';
                    } else if (isWorkingDay && isPastOrToday) {
                        dayElem.classList.add('has-log-missing');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-missing" title="Hari Kerja Belum Diisi"></span>';
                    }
                },
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr && dateStr !== "<?= esc($tanggal_terpilih) ?>") {
                        window.location.href = '<?= site_url('log-kegiatan') ?>?tanggal=' + dateStr;
                    }
                }
            });

            $('#dateSelectorGroup').on('click', function() {
                fpInstance.open();
            });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<?= $this->endSection() ?>
