<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* ==========================================================================
       ECC DESIGN SYSTEM — 8-POINT GRID SYSTEM & POLISHED INTERFACES
       Spacing Token Scale: 4px (0.5x), 8px (1x), 12px (1.5x), 16px (2x), 
                            24px (3x), 32px (4x), 40px (5x), 48px (6x), 64px (8x)
       ========================================================================== */

    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important; /* Mencegah auto-zoom pada iOS/Android */
        }
    }

    .num-tabular {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    /* Bento Card Table Standard (8-Point Grid Rhythm) */
    .table-bento {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 840px;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.75rem; /* 12px */
        line-height: 1.25;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
        vertical-align: middle;
    }
    .table-bento tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 0.8125rem; /* 13px */
        line-height: 1.4;
        transition: background-color 0.15s ease;
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    .date-group-odd {
        background-color: #ffffff;
    }
    .date-group-even {
        background-color: #f8fafc;
    }
    .date-group-odd:hover, .date-group-even:hover {
        background-color: #f1f5f9;
    }
    .col-target { min-width: 220px; }
    .col-nilai { min-width: 152px; }

    .scrollable-table {
        max-height: 480px; /* 60 x 8px */
        overflow-y: auto !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }
    .scrollable-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background-color: #f8fafc !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    /* Number Spin Button Sanitization */
    .input-nilai-capaian::-webkit-outer-spin-button,
    .input-nilai-capaian::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-nilai-capaian {
        -moz-appearance: textfield;
    }

    .predikat-rule-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Tactile Interaction & Micro-Motion */
    .badge-predikat-pop {
        transform: scale(1.06);
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .score-card-transition {
        transition: border-color 0.25s ease, color 0.25s ease, background-color 0.25s ease;
    }
    .tab-content > .tab-pane {
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tab-content > .tab-pane.fade:not(.show) {
        transform: translateY(4px);
    }
    .tab-content > .tab-pane.fade.show {
        transform: translateY(0);
    }
    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease;
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    /* Select2 8-Point Grid Alignment */
    .select2-container--default .select2-selection--single {
        height: 36px !important;
        padding: 4px 8px !important;
        border-radius: 8px !important;
        border: 1px solid #198754 !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        color: #198754 !important;
        padding-left: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08) !important;
        font-size: 0.8125rem !important;
    }

    /* ==========================================================================
       CALENDAR HEATMAP MATRIX (8-Point Grid System — 100% Bebas Emoji)
       ========================================================================== */
    .calendar-heatmap-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .calendar-heatmap-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
    }
    .calendar-header-cell {
        font-size: 0.75rem; /* 12px */
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        text-align: center;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
    }
    .calendar-header-cell.weekend-header {
        color: #dc3545;
    }
    .heatmap-day-cell {
        min-height: 64px; /* 8.0 x 8px (Strict 8-Point Scale) */
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease, border-color 0.15s ease;
        position: relative;
        user-select: none;
        background-color: #ffffff;
    }
    .heatmap-day-cell:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #0d6efd !important;
        z-index: 2;
    }
    .heatmap-day-cell:active {
        transform: scale(0.97);
    }
    .heatmap-empty-cell {
        min-height: 64px; /* 8.0 x 8px */
        background-color: transparent;
        border: 1px dashed #f1f5f9;
        border-radius: 8px;
        opacity: 0.4;
    }

    /* Tanggal Merah (Sabtu, Minggu & Libur Nasional API) */
    .heatmap-day-cell.is-tanggal-merah .day-num {
        color: #dc3545 !important;
        font-weight: 800;
    }
    .heatmap-day-cell.is-tanggal-merah .holiday-indicator {
        color: #dc3545 !important;
    }
    .heatmap-day-cell.heatmap-level-3.is-tanggal-merah .day-num,
    .heatmap-day-cell.heatmap-level-4.is-tanggal-merah .day-num {
        color: #ffe4e6 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
    }

    /* Heatmap Intensity Levels Berdasarkan Aktivitas Riil */
    .heatmap-level-0 {
        background-color: #ffffff;
        border-color: #e2e8f0;
        color: #64748b;
    }
    .heatmap-level-0 .day-num {
        color: #334155;
    }
    .heatmap-level-0 .badge-count {
        color: #94a3b8;
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
    }
    .heatmap-level-1 {
        background-color: #f0fdf4;
        border-color: #bbf7d0;
        color: #15803d;
    }
    .heatmap-level-1 .day-num {
        color: #15803d;
    }
    .heatmap-level-1 .badge-count {
        background-color: rgba(220, 252, 231, 0.85);
        color: #166534;
        border: 1px solid #86efac;
    }
    .heatmap-level-2 {
        background-color: #dcfce7;
        border-color: #86efac;
        color: #166534;
    }
    .heatmap-level-2 .day-num {
        color: #166534;
    }
    .heatmap-level-2 .badge-count {
        background-color: rgba(134, 239, 172, 0.7);
        color: #14532d;
        border: 1px solid #4ade80;
    }
    .heatmap-level-3 {
        background-color: #22c55e;
        border-color: #16a34a;
        color: #ffffff;
    }
    .heatmap-level-3 .day-num {
        color: #ffffff;
    }
    .heatmap-level-3 .badge-subtle-custom {
        background-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
    .heatmap-level-4 {
        background-color: #15803d;
        border-color: #14532d;
        color: #ffffff;
    }
    .heatmap-level-4 .day-num {
        color: #ffffff;
    }
    .heatmap-level-4 .badge-subtle-custom {
        background-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Cell Internal Elements */
    .heatmap-day-cell .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        line-height: 1;
    }
    .heatmap-day-cell .day-num {
        font-size: 0.8125rem; /* 13px */
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .heatmap-day-cell .day-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 6px;
    }
    .heatmap-day-cell .badge-count {
        font-size: 0.6875rem; /* 11px */
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 6px;
        line-height: 1.2;
    }

    /* Heatmap Legend */
    .heatmap-legend-container {
        border-top: 1px solid #f1f5f9;
    }
    .heatmap-legend-swatch {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Modal Rincian Pekerjaan Styling (8-Point Grid Standard) */
    #modalDetailLogTanggal .modal-content {
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.16);
        border-radius: 16px;
    }
    #modalDetailLogTanggal #modalLogTableContainer {
        max-height: 440px; /* 55 x 8px */
    }
    #modalDetailLogTanggal .table-bento thead th {
        font-size: 0.75rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-weight: 700;
        color: #475569;
        padding: 12px 16px;
        background-color: #f8fafc;
    }
    #modalDetailLogTanggal .table-bento tbody td {
        font-size: 0.8125rem;
        padding: 12px 16px;
        vertical-align: middle;
    }
    .btn-nav-date {
        transition: all 0.15s ease;
        background-color: #ffffff;
    }
    .btn-nav-date:hover:not(:disabled) {
        background-color: #f1f5f9;
        color: #0d6efd;
        border-color: #cbd5e1;
    }
    .btn-nav-date:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Responsive Mobile Grid Adjustments (8-Point Grid Sub-scale) */
    @media (max-width: 575.98px) {
        .calendar-heatmap-card {
            padding: 16px;
        }
        .calendar-heatmap-grid {
            gap: 4px;
        }
        .heatmap-day-cell {
            min-height: 48px; /* 6.0 x 8px */
            padding: 4px 6px;
            border-radius: 6px;
        }
        .heatmap-day-cell .badge-count {
            font-size: 0.6rem;
            padding: 1px 4px;
        }
        .heatmap-empty-cell {
            min-height: 48px; /* 6.0 x 8px */
            border-radius: 6px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .badge-predikat-pop, .tab-content > .tab-pane, .btn-tactile, .heatmap-day-cell {
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
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-award-fill text-primary me-2"></i>Rekap & Penilaian Kinerja</h1>
            <p class="text-muted small mb-0">Evaluasi capaian target RHK, realisasi harian, dan penerbitan nilai capaian kinerja.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-calendar-month me-1"></i> <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
        <div class="card-body p-3 p-md-4">
            
            <!-- Filter Bar Toolbar (8-Point Grid Standard) -->
            <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-4 border border-light-subtle shadow-xs" id="filterForm">
                <?= csrf_field() ?>
                <input type="hidden" name="active_tab" id="active_tab_input" value="<?= empty($staf_id_terpilih) ? 'individu' : 'staf' ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-6 col-sm-3 col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.75rem; letter-spacing: 0.02em;"><i class="bi bi-calendar-month text-primary me-1"></i> Bulan</label>
                        <select name="bulan" class="form-select form-select-sm border-primary fw-semibold" style="height: 36px; font-size: 0.8125rem;" aria-label="Pilih Bulan Penilaian" onchange="this.form.submit()">
                            <?php foreach($bulan_indo as $index => $nama): ?>
                                <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-sm-2 col-md-2">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.75rem; letter-spacing: 0.02em;"><i class="bi bi-calendar-event text-primary me-1"></i> Tahun</label>
                        <input type="number" name="tahun" id="tahunFilterInput" class="form-control form-control-sm border-primary fw-semibold" style="height: 36px; font-size: 0.8125rem;" aria-label="Input Tahun Penilaian" value="<?= esc($tahun_terpilih) ?>">
                    </div>
                    
                    <?php if ($is_super): ?>
                    <div class="col-12 col-sm-4 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.75rem; letter-spacing: 0.02em;"><i class="bi bi-building text-primary me-1"></i> Unit Kerja</label>
                        <select name="unit_kerja" class="form-select form-select-sm border-primary fw-semibold" style="height: 36px; font-size: 0.8125rem;" aria-label="Pilih Unit Kerja" onchange="this.form.submit()">
                            <option value="">Semua Unit Kerja</option>
                            <?php foreach ($daftar_unit as $u): ?>
                                <option value="<?= esc($u) ?>" <?= ($u == $unit_kerja_terpilih) ? 'selected' : '' ?>><?= esc($u) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>

        <!-- Navigation Tabs -->
        <ul class="nav segmented-control mb-4 bento-stagger bento-stagger-2" id="penilaianTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-tactile <?= empty($staf_id_terpilih) ? 'active' : '' ?>" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-person-fill me-1.5"></i> Target Saya
                </button>
            </li>
            <?php if ($is_atasan): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-tactile <?= !empty($staf_id_terpilih) ? 'active' : '' ?>" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-people-fill me-1.5"></i> Penilaian Staf
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content" id="penilaianTabsContent">
            
            <!-- ==================== TAB 1: TARGET INDIVIDU SAYA ==================== -->
            <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="individu" role="tabpanel" aria-labelledby="individu-tab">
                <?php if (empty($rekap_data_sendiri)): ?>
                    <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                        <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Target Kinerja</h6>
                        <p class="text-muted small mb-0">Belum ada target RHK yang ditetapkan pada periode <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>.</p>
                    </div>
                <?php else: ?>
                    <?php
                        $jmlDinilai = 0;
                        $totalNilai = 0;
                        foreach ($rekap_data_sendiri as $rd) {
                            if (isset($rd['status_penilaian']) && $rd['status_penilaian'] === 'terbit' && $rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') {
                                $jmlDinilai++;
                                $totalNilai += (float)$rd['nilai_capaian'];
                            }
                        }
                        
                        // Perhitungan tugas tambahan jika dinilai dan terbit
                        $scoreTambahanIndividu = null;
                        if (!empty($tugas_tambahan_sendiri)) {
                            foreach ($tugas_tambahan_sendiri as $tmb) {
                                if (isset($tmb['status_penilaian']) && $tmb['status_penilaian'] === 'terbit' && $tmb['nilai_capaian'] !== null) {
                                    $scoreTambahanIndividu = (float)$tmb['nilai_capaian'];
                                    break;
                                }
                            }
                        }

                        if ($scoreTambahanIndividu !== null) {
                            $totalNilai += $scoreTambahanIndividu;
                            $jmlDinilai++;
                        }

                        $rataRataIndividu = $jmlDinilai > 0 ? (float)($totalNilai / $jmlDinilai) : 0;
                        
                        $predikatRataIndividu = '-';
                        $badgeColorRataIndividu = 'secondary';
                        $warnaScore = 'success';
                        if ($jmlDinilai == 0) {
                            $warnaScore = 'secondary';
                        } else {
                            if ($rataRataIndividu <= 25) { $warnaScore = 'danger'; $predikatRataIndividu = 'Sangat Kurang'; $badgeColorRataIndividu = 'danger'; }
                            elseif ($rataRataIndividu <= 75) { $warnaScore = 'warning text-dark'; $predikatRataIndividu = 'Kurang'; $badgeColorRataIndividu = 'warning text-dark'; }
                            elseif ($rataRataIndividu <= 90) { $warnaScore = 'info text-dark'; $predikatRataIndividu = 'Butuh Perbaikan'; $badgeColorRataIndividu = 'info text-dark'; }
                            elseif ($rataRataIndividu <= 100) { $warnaScore = 'primary'; $predikatRataIndividu = 'Baik'; $badgeColorRataIndividu = 'primary'; }
                            else { $warnaScore = 'success'; $predikatRataIndividu = 'Sangat Baik'; $badgeColorRataIndividu = 'success'; }
                        }
                    ?>

                    <!-- GUIDANCE & RULES BANNER (8-Point Grid) -->
                    <div class="alert alert-light border border-info-subtle shadow-sm mb-4 py-2.5 px-3 text-dark rounded-3" style="font-size: 0.75rem;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span class="fw-bold text-secondary text-nowrap"><i class="bi bi-journal-check text-primary me-1"></i> Standar Predikat:</span>
                            <span class="text-nowrap"><span class="badge bg-danger px-2 py-1" style="font-size:0.75rem;">Sangat Kurang</span> &le; 25%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-warning text-dark px-2 py-1" style="font-size:0.75rem;">Kurang</span> &gt; 25% - 75%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-info text-dark px-2 py-1" style="font-size:0.75rem;">Butuh Perbaikan</span> &gt; 75% - 90%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-primary px-2 py-1" style="font-size:0.75rem;">Baik</span> &gt; 90% - 100%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-success px-2 py-1" style="font-size:0.75rem;">Sangat Baik</span> &gt; 100% - 150%</span>
                        </div>
                    </div>

                    <!-- BAGIAN A: TARGET KINERJA RHK -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-4">
                        <h6 class="fw-bold text-dark section-header-title mb-0" style="font-size: 0.875rem;">
                            <i class="bi bi-list-task text-primary me-2"></i> A. Target Kinerja RHK
                        </h6>
                    </div>
                    
                    <?php if (session()->get('role') === 'direktur'): ?>
                    <form method="POST" action="<?= site_url('penilaian-kinerja/store') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="is_self_eval" value="1">
                        <input type="hidden" name="staf_id" value="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>">
                        <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                        <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <?php endif; ?>

                    <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 140px;" class="text-center">Target Bulanan</th>
                                    <th style="width: 140px;" class="text-center">Total Realisasi</th>
                                    <th style="width: 140px;" class="text-center">Selisih (Gap)</th>
                                    <th class="col-nilai text-center" style="width: 160px;">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                    <?php 
                                        $target = round((float)$row['target_bulanan'], 4);
                                        $realisasi = round((float)$row['total_realisasi'], 4);
                                        $selisih = round($realisasi - $target, 4);
                                        $isTerbit = (isset($row['status_penilaian']) && $row['status_penilaian'] === 'terbit' && $row['nilai_capaian'] !== null);
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                        <td class="text-center fw-bold text-dark num-tabular"><?= str_replace('.', ',', (float)$target) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold text-primary num-tabular"><?= str_replace('.', ',', (float)$realisasi) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold num-tabular">
                                            <?php if ($selisih > 0): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php elseif ($selisih < 0): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle fw-bold fs-6 text-primary num-tabular">
                                            <?php if (session()->get('role') === 'direktur'): ?>
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="0" max="150" name="nilai_capaian[]" class="form-control text-center text-primary fw-bold num-tabular input-nilai-capaian" value="<?= isset($row['nilai_capaian']) && $row['nilai_capaian'] !== null ? (float)$row['nilai_capaian'] : '' ?>" placeholder="0 - 150" aria-label="Nilai Capaian Mandiri RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            <?php else: ?>
                                                <?= $isTerbit ? str_replace('.', ',', (float)$row['nilai_capaian']) . '%' : '<span class="text-muted fw-normal" title="Belum diterbitkan atasan">-</span>' ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- BAGIAN B: TUGAS TAMBAHAN -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-4">
                        <h6 class="fw-bold text-dark section-header-title mb-0 small">
                            <i class="bi bi-journal-plus text-primary me-1.5"></i> B. Tugas Tambahan
                        </h6>
                    </div>
                    
                    <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th class="text-start">Deskripsi Tugas Tambahan</th>
                                    <th style="width: 120px;" class="text-center">Tanggal</th>
                                    <th style="width: 140px;" class="text-center">Capaian / Output</th>
                                    <th style="width: 90px;" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tugas_tambahan_sendiri)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada tugas tambahan pada bulan ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($tugas_tambahan_sendiri as $idx => $tmb): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                            <td><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                            <td class="text-center text-muted num-tabular">
                                                <?php 
                                                    $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                    $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                    echo $tgl . ' ' . substr($bln, 0, 3);
                                                ?>
                                            </td>
                                             <td class="text-center fw-semibold text-dark num-tabular">
                                                 <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                             </td>
                                            <td class="text-center">
                                                <?php if (!empty($tmb['link_bukti'])): ?>
                                                    <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5 btn-tactile" style="font-size: 0.72rem;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($tugas_tambahan_sendiri)): ?>
                            <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                <tr>
                                    <td colspan="4" class="text-end pe-3 align-middle text-muted fw-normal">Nilai Tugas Tambahan:</td>
                                    <td class="text-center align-middle fw-bold fs-6 text-success p-2 num-tabular">
                                        <?php if (session()->get('role') === 'direktur'): ?>
                                            <input type="hidden" name="log_tambahan_id[]" value="<?= esc($tugas_tambahan_sendiri[0]['id']) ?>">
                                            <div class="input-group input-group-sm justify-content-center" style="max-width: 130px; margin: 0 auto;">
                                                <input type="number" step="0.01" min="0" max="150" name="nilai_tugas_tambahan_gabungan" class="form-control text-center text-success fw-bold num-tabular input-nilai-capaian" value="<?= $scoreTambahanIndividu !== null ? (float)$scoreTambahanIndividu : '' ?>" placeholder="0 - 150" aria-label="Nilai Tugas Tambahan Mandiri">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        <?php else: ?>
                                            <?= $scoreTambahanIndividu !== null ? str_replace('.', ',', $scoreTambahanIndividu) . '%' : '<span class="text-muted fw-normal">-</span>' ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                    <?php if (session()->get('role') === 'direktur'): ?>
                    <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 fw-bold">
                            <i class="bi bi-pencil me-1"></i> Simpan Draf
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-send me-1"></i> Simpan & Terbitkan
                        </button>
                    </div>
                    </form>
                    <?php endif; ?>

                    <!-- RINGKASAN SKOR EXECUTIVE DI PALING BAWAH EVALUASI (8-Point Grid) -->
                    <div class="card bg-white border border-2 border-<?= ($warnaScore === 'warning text-dark' || $warnaScore === 'info text-dark') ? ($warnaScore === 'warning text-dark' ? 'warning' : 'info') : $warnaScore ?> rounded-4 p-3 p-md-4 shadow-sm mb-4 score-card-transition bento-stagger bento-stagger-3" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Mandiri">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 score-banner-wrapper">
                            <div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9375rem;"><i class="bi bi-award-fill text-primary me-2"></i> NILAI AKHIR KINERJA BULANAN</h6>
                                <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5">
                                        <i class="bi bi-list-task me-1 text-primary"></i> <?= count($rekap_data_sendiri) ?> Target RHK
                                    </span>
                                    <?php if (!empty($tugas_tambahan_sendiri)): ?>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5">
                                        <i class="bi bi-journal-plus me-1 text-success"></i> Tugas Tambahan: <?= $scoreTambahanIndividu !== null ? str_replace('.', ',', $scoreTambahanIndividu) . '%' : '<span class="text-muted">Belum Dinilai</span>' ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($jmlDinilai < (count($rekap_data_sendiri) + (!empty($tugas_tambahan_sendiri) ? 1 : 0))): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5">
                                        <i class="bi bi-info-circle me-1"></i> Sebagian nilai belum diterbitkan atasan
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5">
                                        <i class="bi bi-check-circle-fill me-1"></i> Seluruh komponen telah dinilai
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                <span class="badge bg-<?= $badgeColorRataIndividu ?> fs-6 px-3 py-2 rounded-pill"><?= $predikatRataIndividu ?></span>
                                <span class="fs-1 fw-bold text-<?= $warnaScore ?> mb-0 lh-1 num-tabular" style="font-size: 2.25rem !important; white-space: nowrap;"><?= str_replace('.', ',', round($rataRataIndividu, 2)) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php 
                        $hasAnyDraftSendiri = false;
                        if (!empty($log_harian_sendiri)) {
                            foreach ($log_harian_sendiri as $item) {
                                if (isset($item['status']) && $item['status'] === 'draft') {
                                    $hasAnyDraftSendiri = true;
                                    break;
                                }
                            }
                        }
                    ?>

                    <?php if ($hasAnyDraftSendiri): ?>
                        <!-- BANNER PERINGATAN LAPORAN MASIH DRAF (8-Point Grid) -->
                        <div class="alert alert-warning py-2.5 px-3 border-warning shadow-sm mb-3 d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-2 rounded-4 bento-stagger bento-stagger-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <strong>Perhatian:</strong> Anda memiliki laporan harian yang <strong>masih berupa Draf (Belum Dikirim)</strong> pada bulan ini. Laporan draf tidak dihitung dalam realisasi kinerja sampai Anda mengirimkannya.
                                </div>
                            </div>
                            <a href="<?= site_url('log-kegiatan') ?>" class="btn btn-sm btn-warning btn-tactile text-dark fw-bold ms-md-3 flex-shrink-0 rounded-pill px-3 shadow-xs">
                                <i class="bi bi-send-fill me-1"></i> Buka & Kirim Laporan
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- HEATMAP KALENDER AKTIVITAS BULANAN PEGAWAI (INDIVIDU) -->
                    <?php if (!empty($heatmap_sendiri)): ?>
                    <div class="calendar-heatmap-card mb-4 bento-stagger bento-stagger-3">
                        <!-- Header Heatmap -->
                        <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-2 mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar3 me-1"></i> Kalender Aktivitas Saya
                                    </span>
                                    <span class="text-muted small fw-semibold" style="font-size: 0.75rem;">
                                        Periode: <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
                                    </span>
                                </div>
                                <h6 class="fw-bold text-dark mb-0 mt-1" style="font-size: 0.92rem;">
                                    Distribusi & Konsistensi Kerja Harian
                                </h6>
                            </div>
                            <!-- Executive Summary Badges (8-Point Grid) -->
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                    <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">HARI TERISI</div>
                                    <div class="fw-bold text-success num-tabular" style="font-size: 0.875rem;"><?= $heatmap_sendiri['summary']['total_hari_terisi'] ?> Hari</div>
                                </div>
                                <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                    <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">TOTAL KEGIATAN</div>
                                    <div class="fw-bold text-primary num-tabular" style="font-size: 0.875rem;"><?= $heatmap_sendiri['summary']['total_log_items'] ?> Kegiatan</div>
                                </div>
                                <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                    <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">RATA-RATA / HARI</div>
                                    <div class="fw-bold text-dark num-tabular" style="font-size: 0.875rem;"><?= str_replace('.', ',', $heatmap_sendiri['summary']['rata_rata_per_hari_aktif']) ?> Log</div>
                                </div>
                            </div>
                        </div>

                        <!-- Grid Kalender 7 Kolom (Senin s/d Minggu) -->
                        <div class="calendar-heatmap-grid mb-3">
                            <!-- Header Hari -->
                            <div class="calendar-header-cell">Sen</div>
                            <div class="calendar-header-cell">Sel</div>
                            <div class="calendar-header-cell">Rab</div>
                            <div class="calendar-header-cell">Kam</div>
                            <div class="calendar-header-cell">Jum</div>
                            <div class="calendar-header-cell weekend-header">Sab</div>
                            <div class="calendar-header-cell weekend-header">Min</div>

                            <!-- Leading empty cells for alignment -->
                            <?php for ($emptyIdx = 1; $emptyIdx < $heatmap_sendiri['first_day_of_week']; $emptyIdx++): ?>
                                <div class="heatmap-empty-cell"></div>
                            <?php endfor; ?>

                            <!-- Day Cells -->
                            <?php foreach ($heatmap_sendiri['days'] as $day): ?>
                                <?php
                                    $levelClass = 'heatmap-level-' . $day['level'];
                                    $tanggalMerahClass = $day['is_tanggal_merah'] ? 'is-tanggal-merah' : '';
                                    $tooltipTitle = esc($day['date_formatted']);
                                    if ($day['is_holiday']) {
                                        $tooltipTitle .= ' - ' . esc($day['holiday_name']);
                                    } elseif ($day['is_weekend']) {
                                        $tooltipTitle .= ' - Akhir Pekan';
                                    }
                                    $tooltipTitle .= ' (' . $day['count_logs'] . ' Kegiatan)';
                                ?>
                                <div class="heatmap-day-cell <?= $levelClass ?> <?= $tanggalMerahClass ?>" 
                                     data-is-staf="0"
                                     data-staf-id=""
                                     data-day-num="<?= $day['day_num'] ?>"
                                     data-date-str="<?= esc($day['date_str']) ?>"
                                     data-tanggal="<?= esc($day['date_str']) ?>"
                                     data-tanggal-formatted="<?= esc($day['date_formatted']) ?>"
                                     data-is-holiday="<?= $day['is_holiday'] ? '1' : '0' ?>"
                                     data-holiday-name="<?= esc($day['holiday_name'] ?? '') ?>"
                                     data-is-weekend="<?= $day['is_weekend'] ? '1' : '0' ?>"
                                     data-is-tanggal-merah="<?= $day['is_tanggal_merah'] ? '1' : '0' ?>"
                                     data-has-draft="<?= $day['has_draft'] ? '1' : '0' ?>"
                                     data-has-terkirim="<?= $day['has_terkirim'] ? '1' : '0' ?>"
                                     data-total-kegiatan="<?= $day['count_logs'] ?>"
                                     data-logs='<?= htmlspecialchars(json_encode($day['logs']), ENT_QUOTES, 'UTF-8') ?>'
                                     data-bs-toggle="tooltip"
                                     data-bs-placement="top"
                                     title="<?= $tooltipTitle ?>">
                                    <div class="day-header">
                                        <span class="day-num"><?= $day['day_num'] ?></span>
                                        <?php if ($day['is_holiday']): ?>
                                            <i class="bi bi-sun-fill holiday-indicator" style="font-size: 0.65rem;" title="<?= esc($day['holiday_name']) ?>"></i>
                                        <?php elseif ($day['is_weekend']): ?>
                                            <i class="bi bi-moon-stars text-secondary" style="font-size: 0.65rem;"></i>
                                        <?php elseif ($day['count_logs'] === 0): ?>
                                            <i class="bi bi-dash text-muted opacity-50" style="font-size: 0.75rem;"></i>
                                        <?php elseif ($day['has_draft']): ?>
                                            <i class="bi bi-pencil-fill text-warning-emphasis" style="font-size: 0.65rem;"></i>
                                        <?php else: ?>
                                            <i class="bi bi-check2-circle text-success" style="font-size: 0.65rem;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="day-footer">
                                        <?php if ($day['count_logs'] === 0): ?>
                                            <span class="badge-count text-muted">-</span>
                                        <?php else: ?>
                                            <span class="badge-count <?= in_array($day['level'], [3, 4]) ? 'badge-subtle-custom' : 'fw-bold' ?>">
                                                <?= $day['count_logs'] ?> Log
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Legend Footer (8-Point Grid Standard) -->
                        <div class="heatmap-legend-container mt-3 pt-3">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                                <!-- Sisi Kiri: Keterangan & Swatch Skala Intensitas -->
                                <div class="d-flex align-items-center flex-wrap gap-2.5">
                                    <span class="badge bg-light text-secondary border px-3 py-2 fw-bold text-uppercase d-inline-flex align-items-center" style="font-size: 0.6875rem; letter-spacing: 0.05em; border-radius: 8px; min-height: 32px;">Keterangan</span>
                                    
                                    <div class="d-inline-flex align-items-center bg-danger-subtle text-danger border border-danger-subtle rounded-pill shadow-xs" style="font-size: 0.75rem; font-weight: 600; min-height: 32px; padding: 6px 14px; gap: 8px;">
                                        <i class="bi bi-calendar-x-fill" style="font-size: 0.75rem;"></i>
                                        <span>Tgl Merah / Libur</span>
                                    </div>

                                    <!-- Strip Swatch Skala Intensitas (Bento Capsule) -->
                                    <div class="d-inline-flex align-items-center bg-light rounded-pill border border-light-subtle flex-wrap shadow-xs" style="min-height: 32px; padding: 6px 16px; gap: 16px;">
                                        <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                            <span class="heatmap-legend-swatch" style="background-color: #ffffff; border: 1px solid #cbd5e1;"></span>
                                            <span class="text-secondary fw-medium">0 Log</span>
                                        </div>
                                        <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                            <span class="heatmap-legend-swatch" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;"></span>
                                            <span class="text-dark fw-medium">1 - 2</span>
                                        </div>
                                        <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                            <span class="heatmap-legend-swatch" style="background-color: #dcfce7; border: 1px solid #86efac;"></span>
                                            <span class="text-dark fw-medium">3 - 4</span>
                                        </div>
                                        <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                            <span class="heatmap-legend-swatch" style="background-color: #22c55e; border: 1px solid #16a34a;"></span>
                                            <span class="text-dark fw-medium">5 - 6</span>
                                        </div>
                                        <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                            <span class="heatmap-legend-swatch" style="background-color: #15803d; border: 1px solid #14532d;"></span>
                                            <span class="text-dark fw-medium">&gt; 6 Log</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sisi Kanan: Hint Interaksi Kalender -->
                                <div class="d-inline-flex align-items-center text-primary bg-primary-subtle rounded-pill border border-primary-subtle shadow-xs" style="font-size: 0.75rem; font-weight: 600; min-height: 32px; padding: 6px 16px; gap: 8px;">
                                    <i class="bi bi-cursor-fill" style="font-size: 0.75rem;"></i>
                                    <span>Klik tanggal pada kalender untuk melihat rincian pekerjaan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div> <!-- End Tab Individu -->

            <?php if ($is_atasan): ?>
            <!-- ==================== TAB 2: PENILAIAN STAF (KHUSUS ATASAN) ==================== -->
            <div class="tab-pane fade <?= !empty($staf_id_terpilih) ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">
                
                <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-4 border border-light-subtle" id="formPilihStaf">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">
                    
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.3px;"><i class="bi bi-person-check me-1"></i> Pilih Staf yang Akan Dinilai</label>
                            <select name="staf_id" id="selectStaf" class="form-select border-success form-select-sm" aria-label="Pilih Staf yang Akan Dinilai" onchange="this.form.submit()">
                                <option value="">-- Pilih Staf --</option>
                                <?php foreach ($daftar_staf as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($b['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                        <?= esc($b['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <?php if (!empty($staf_id_terpilih)): ?>
                    <?php if (empty($rekap_data_staf)): ?>
                        <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                            <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Target Kinerja Staf Belum Ditetapkan</h6>
                            <p class="text-muted small mb-0">Staf belum memiliki Target Kinerja (RHK) pada periode <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>.</p>
                        </div>
                    <?php else: ?>
                        <?php
                            $jmlDinilaiBwh = 0;
                            $totalNilaiBwh = 0;
                            foreach ($rekap_data_staf as $rd) {
                                // Hanya hitung nilai yang sudah resmi diterbitkan (status_penilaian = 'terbit')
                                if (isset($rd['status_penilaian']) && $rd['status_penilaian'] === 'terbit' && $rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') {
                                    $jmlDinilaiBwh++;
                                    $totalNilaiBwh += (float)$rd['nilai_capaian'];
                                }
                            }

                            // Perhitungan tugas tambahan gabungan jika dinilai
                            $scoreTambahanStaf = null;
                            if (!empty($tugas_tambahan_staf)) {
                                foreach ($tugas_tambahan_staf as $tmb) {
                                    if ($tmb['nilai_capaian'] !== null) {
                                        $scoreTambahanStaf = (float)$tmb['nilai_capaian'];
                                        break;
                                    }
                                }
                            }

                            if ($scoreTambahanStaf !== null) {
                                $totalNilaiBwh += $scoreTambahanStaf;
                                $jmlDinilaiBwh++;
                            }

                            $rataRataBwh = $jmlDinilaiBwh > 0 ? (float)($totalNilaiBwh / $jmlDinilaiBwh) : 0;
                            
                            $predikatRataRataBwh = '-';
                            $badgeColorRataBwh = 'secondary';
                            $warnaScoreBwh = 'success';
                            if ($jmlDinilaiBwh == 0) {
                                $warnaScoreBwh = 'secondary';
                            } else {
                                if ($rataRataBwh <= 25) { $warnaScoreBwh = 'danger'; $predikatRataRataBwh = 'Sangat Kurang'; $badgeColorRataBwh = 'danger'; }
                                elseif ($rataRataBwh <= 75) { $warnaScoreBwh = 'warning text-dark'; $predikatRataRataBwh = 'Kurang'; $badgeColorRataBwh = 'warning text-dark'; }
                                elseif ($rataRataBwh <= 90) { $warnaScoreBwh = 'info text-dark'; $predikatRataRataBwh = 'Butuh Perbaikan'; $badgeColorRataBwh = 'info text-dark'; }
                                elseif ($rataRataBwh <= 100) { $warnaScoreBwh = 'primary'; $predikatRataRataBwh = 'Baik'; $badgeColorRataBwh = 'primary'; }
                                else { $warnaScoreBwh = 'success'; $predikatRataRataBwh = 'Sangat Baik'; $badgeColorRataBwh = 'success'; }
                            }
                        ?>

                        <!-- GUIDANCE & RULES BANNER (8-Point Grid) -->
                        <div class="alert alert-light border border-info-subtle shadow-sm mb-4 py-2.5 px-3 text-dark rounded-3" style="font-size: 0.75rem;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="fw-bold text-secondary text-nowrap"><i class="bi bi-journal-check text-success me-1"></i> Standar Predikat:</span>
                                <span class="text-nowrap"><span class="badge bg-danger px-2 py-1" style="font-size:0.75rem;">Sangat Kurang</span> &le; 25%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-warning text-dark px-2 py-1" style="font-size:0.75rem;">Kurang</span> &gt; 25% - 75%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-info text-dark px-2 py-1" style="font-size:0.75rem;">Butuh Perbaikan</span> &gt; 75% - 90%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-primary px-2 py-1" style="font-size:0.75rem;">Baik</span> &gt; 90% - 100%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-success px-2 py-1" style="font-size:0.75rem;">Sangat Baik</span> &gt; 100% - 150%</span>
                            </div>
                        </div>

                        <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                        <!-- ALERT: TARGET BELUM DISETUJUI -->
                        <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3 bento-stagger bento-stagger-1">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px;">
                                    <i class="bi bi-clock-history fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.875rem;">Target Kinerja Bulanan Staf Belum Disetujui</h6>
                                    <p class="text-muted small mb-0" style="font-size: 0.775rem;">
                                        Target Kinerja (RHK) staf untuk periode <strong><?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?></strong> masih berstatus <em>Menunggu Persetujuan</em><?= !empty($target_staf_unapproved_count) ? ' (' . (int)$target_staf_unapproved_count . ' target belum disetujui)' : '' ?>. Anda harus menyetujui target kinerja staf terlebih dahulu sebelum dapat memberikan penilaian.
                                    </p>
                                </div>
                            </div>
                            <a href="<?= site_url('laporan-harian?staf_id=' . esc($staf_id_terpilih) . '&bulan=' . esc($bulan_terpilih) . '&tahun=' . esc($tahun_terpilih) . '&source_tab=staf') ?>" class="btn btn-warning btn-sm btn-tactile text-dark fw-bold rounded-pill px-3 py-1.5 shadow-xs text-nowrap" style="height: 36px; display: inline-flex; align-items: center;">
                                <i class="bi bi-check2-circle me-1.5"></i> Buka Menu Target Kinerja untuk Menyetujui
                            </a>
                        </div>
                        <?php endif; ?>

                        <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" id="penilaianActionInput" value="submit">
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                            <!-- BAGIAN A STAF: PENILAIAN TARGET RHK -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-4">
                                <h6 class="fw-bold text-dark section-header-title mb-0" style="font-size: 0.875rem;">
                                    <i class="bi bi-list-task text-success me-2"></i> A. Penilaian Target RHK
                                </h6>
                                <?php if (hasRole('admin') && !empty($staf_id_terpilih)): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold shadow-sm btn-batal-approve-target-penilaian btn-tactile"
                                    data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                    data-bulan="<?= esc($bulan_terpilih) ?>"
                                    data-tahun="<?= esc($tahun_terpilih) ?>"
                                    style="height: 32px; font-size: 0.75rem;"
                                    title="Batalkan persetujuan target bulanan staf agar staf dapat merevisi target">
                                    <i class="bi bi-x-circle-fill me-1"></i> Batalkan Target (Admin)
                                </button>
                                <?php endif; ?>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0 table-bento" id="tablePenilaianStaf">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                            <th style="width: 130px;" class="text-center">Target Bulanan</th>
                                            <th style="width: 130px;" class="text-center">Total Realisasi</th>
                                            <th style="width: 130px;" class="text-center">Selisih (Gap)</th>
                                            <th class="col-nilai text-center">Input Nilai Capaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php 
                                                $target = round((float)$row['target_bulanan'], 4);
                                                $realisasi = round((float)$row['total_realisasi'], 4);
                                                $selisih = round($realisasi - $target, 4);
                                                
                                                $nilai_capaian = $row['nilai_capaian'];
                                                $predikatLabel = '-';
                                                $predikatBadge = 'bg-light text-muted border border-secondary-subtle';
                                                if ($nilai_capaian !== null && $nilai_capaian !== '') {
                                                    $n = (float)$nilai_capaian;
                                                    if ($n <= 25) { $predikatLabel = 'Sangat Kurang'; $predikatBadge = 'bg-danger'; }
                                                    elseif ($n <= 75) { $predikatLabel = 'Kurang'; $predikatBadge = 'bg-warning text-dark'; }
                                                    elseif ($n <= 90) { $predikatLabel = 'Butuh Perbaikan'; $predikatBadge = 'bg-info text-dark'; }
                                                    elseif ($n <= 100) { $predikatLabel = 'Baik'; $predikatBadge = 'bg-primary'; }
                                                    else { $predikatLabel = 'Sangat Baik'; $predikatBadge = 'bg-success'; }
                                                } else {
                                                    $predikatLabel = '<i class="bi bi-dash-circle me-1"></i> Belum dinilai';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                                <td class="text-center fw-bold text-dark num-tabular"><?= str_replace('.', ',', (float)$target) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold text-primary num-tabular"><?= str_replace('.', ',', (float)$realisasi) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold num-tabular">
                                                    <?php if ($selisih > 0): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php elseif ($selisih < 0): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle p-2 text-center col-nilai">
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                    <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                                                        <div class="input-group input-group-sm mb-1 shadow-sm rounded-3 border border-secondary-subtle opacity-75" style="width: 100%; min-width: 145px; height: 36px;">
                                                            <input type="number" class="form-control text-center fw-bold text-muted px-2 num-tabular bg-light" style="font-size:0.875rem; min-width: 90px; height: 34px;" value="" disabled placeholder="Terkunci" aria-label="Target belum disetujui">
                                                            <span class="input-group-text bg-light text-muted fw-bold px-2">%</span>
                                                        </div>
                                                        <div class="predikat-badge-container">
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-clock-history me-1"></i> Target Belum Disetujui</span>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="input-group input-group-sm mb-1 shadow-sm rounded-3 border border-primary-subtle" style="width: 100%; min-width: 145px; height: 36px;">
                                                            <input type="number" name="nilai_capaian[]" class="form-control text-center input-nilai-capaian fw-bold text-primary px-2 num-tabular" style="font-size:0.875rem; min-width: 90px; height: 34px;" value="<?= $nilai_capaian !== null ? (float)$nilai_capaian : '' ?>" step="0.01" min="0" max="150" placeholder="0 - 150" aria-label="Input Nilai Capaian RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                            <span class="input-group-text bg-primary-subtle text-primary fw-bold px-2">%</span>
                                                        </div>
                                                        <div class="predikat-badge-container">
                                                            <span class="badge <?= $predikatBadge ?> rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><?= $predikatLabel ?></span>
                                                        </div>
                                                        <div class="invalid-feedback" style="font-size: 0.7rem;">Nilai tidak sesuai!</div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- BAGIAN B STAF: PENILAIAN TUGAS TAMBAHAN -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-4">
                                <h6 class="fw-bold text-dark section-header-title mb-0" style="font-size: 0.875rem;">
                                    <i class="bi bi-journal-plus text-success me-2"></i> B. Penilaian Tugas Tambahan
                                </h6>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="text-start">Deskripsi Tugas Tambahan</th>
                                            <th style="width: 120px;" class="text-center">Tanggal</th>
                                            <th style="width: 140px;" class="text-center">Capaian / Output</th>
                                            <th style="width: 90px;" class="text-center">Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($tugas_tambahan_staf)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-3">Staf ini tidak memiliki tugas tambahan bulan ini.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($tugas_tambahan_staf as $idx => $tmb): ?>
                                                <input type="hidden" name="log_tambahan_id[]" value="<?= $tmb['id'] ?>">
                                                <tr>
                                                    <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                                    <td><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                                    <td class="text-center text-muted num-tabular">
                                                        <?php 
                                                            $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                            $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                            echo $tgl . ' ' . substr($bln, 0, 3);
                                                        ?>
                                                    </td>
                                                     <td class="text-center fw-semibold text-dark num-tabular">
                                                         <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                                     </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($tmb['link_bukti'])): ?>
                                                            <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-3 py-1 btn-tactile" style="font-size: 0.75rem; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php if (!empty($tugas_tambahan_staf)): ?>
                                    <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="4" class="text-end pe-3 align-middle text-dark fw-bold" style="font-size: 0.8125rem;">
                                                <i class="bi bi-journal-check text-success me-1"></i> Nilai Tugas Tambahan (0 - 150%):
                                            </td>
                                            <td class="p-2 align-middle text-center col-nilai">
                                                <div class="input-group input-group-sm shadow-sm rounded-3 border border-success-subtle" style="width: 100%; min-width: 145px; height: 36px;">
                                                    <input type="number" step="0.01" max="150" min="0" name="nilai_tugas_tambahan_gabungan" id="inputNilaiTambahanGabungan" class="form-control text-center fw-bold <?= (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui) ? 'text-muted bg-light' : 'text-success' ?> px-2 num-tabular input-nilai-capaian" style="font-size:0.875rem; min-width: 90px; height: 34px;" value="<?= $scoreTambahanStaf !== null ? $scoreTambahanStaf : '' ?>" placeholder="<?= (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui) ? 'Terkunci' : '0 - 150' ?>" aria-label="Input Nilai Akumulasi Tugas Tambahan Staf" <?= (!$is_penilai || (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui)) ? 'disabled' : '' ?>>
                                                    <span class="input-group-text bg-success-subtle text-success fw-bold px-2">%</span>
                                                </div>
                                                <div id="hintTambahanContainer">
                                                    <?php if ($scoreTambahanStaf === null): ?>
                                                        <span class="badge bg-light text-muted border border-secondary-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-check-circle me-1"></i> Terisi</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>

                            <!-- RINGKASAN EXECUTIVE SKOR AKHIR DI PALING BAWAH PENILAIAN (8-Point Grid) -->
                            <div class="card bg-white border border-2 border-<?= ($warnaScoreBwh === 'warning text-dark' || $warnaScoreBwh === 'info text-dark') ? ($warnaScoreBwh === 'warning text-dark' ? 'warning' : 'info') : $warnaScoreBwh ?> rounded-4 p-3 p-md-4 shadow-sm mb-4 score-card-transition bento-stagger bento-stagger-3" id="cardKinerjaStafSummary" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Staf">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 score-banner-wrapper">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9375rem;"><i class="bi bi-award-fill text-success me-2"></i> NILAI AKHIR KINERJA STAF</h6>
                                        <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5">
                                                <i class="bi bi-list-task me-1 text-primary"></i> <span id="countRhkTerisi"><?= $jmlDinilaiBwh - ($scoreTambahanStaf !== null ? 1 : 0) ?></span> / <?= count($rekap_data_staf) ?> RHK Terisi
                                            </span>
                                            <?php if (!empty($tugas_tambahan_staf)): ?>
                                            <span class="badge bg-light text-dark border px-2.5 py-1.5" id="badgeTambahanStatus">
                                                <i class="bi bi-journal-plus me-1 text-success"></i> Tugas Tambahan: <span id="textTambahanStatus"><?= $scoreTambahanStaf !== null ? (float)$scoreTambahanStaf . '%' : 'Belum Dinilai' ?></span>
                                            </span>
                                            <?php endif; ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 d-none" id="badgeHintUnfilled">
                                                <i class="bi bi-exclamation-circle me-1"></i> <span id="textCountUnfilled">0</span> kolom belum diisi
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 d-none" id="badgeAllFilled">
                                                <i class="bi bi-check-circle-fill me-1"></i> Semua nilai terisi
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                        <span id="totalPredikatStafBadge" class="badge bg-<?= $badgeColorRataBwh ?> fs-6 px-3 py-2 rounded-pill"><?= $predikatRataRataBwh ?></span>
                                        <span class="fs-1 fw-bold text-<?= $warnaScoreBwh ?> mb-0 lh-1 num-tabular" id="totalKinerjaStafText" style="font-size: 2.25rem !important; white-space: nowrap;"><?= str_replace('.', ',', round($rataRataBwh, 2)) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION TOOLBAR AT BOTTOM OF STAF FORM (8-Point Grid) -->
                            <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                                <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                                    <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold opacity-75 pe-none shadow-sm" style="min-height: 40px; font-size: 0.8125rem;" disabled>
                                        <i class="bi bi-lock-fill me-1"></i> Penilaian Dikunci (Target Belum Disetujui)
                                    </button>
                                    <a href="<?= site_url('laporan-harian?staf_id=' . esc($staf_id_terpilih) . '&bulan=' . esc($bulan_terpilih) . '&tahun=' . esc($tahun_terpilih) . '&source_tab=staf') ?>" class="btn btn-warning btn-tactile text-dark rounded-pill px-4 py-2 fw-bold shadow-sm" style="min-height: 40px; font-size: 0.8125rem;" title="Buka menu Target Kinerja untuk menyetujui target staf">
                                        <i class="bi bi-check2-circle me-1.5"></i> Setujui Target Kinerja Staf
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btnResetNilaiStaf" style="min-height: 40px; font-size: 0.8125rem;" title="Kosongkan dan reset seluruh nilai pada periode ini" aria-label="Reset seluruh nilai kinerja staf"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset Nilai</button>
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" style="min-height: 40px; font-size: 0.8125rem;" title="Simpan sebagai draf" aria-label="Simpan draf penilaian kinerja staf"><i class="bi bi-journal-bookmark me-1"></i> Simpan Draf</button>
                                    <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 py-2 fw-bold shadow-sm" style="min-height: 40px; font-size: 0.8125rem;" title="Simpan dan terbitkan nilai kinerja" aria-label="Simpan dan terbitkan nilai kinerja staf"><i class="bi bi-check-circle-fill me-1.5"></i> Simpan & Terbitkan Penilaian</button>
                                <?php endif; ?>
                            </div>
                        </form>

                        <!-- HEATMAP KALENDER AKTIVITAS BULANAN STAF (PENILAIAN) -->
                        <?php if (!empty($heatmap_staf)): ?>
                        <div class="calendar-heatmap-card mb-4 bento-stagger bento-stagger-3">
                            <!-- Header Heatmap -->
                            <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-2 mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i class="bi bi-calendar3 me-1"></i> Kalender Aktivitas Staf
                                        </span>
                                        <span class="text-muted small fw-semibold" style="font-size: 0.75rem;">
                                            Periode: <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 mt-1" style="font-size: 0.92rem;">
                                        Distribusi & Konsistensi Kerja Harian Staf
                                    </h6>
                                </div>
                                <!-- Executive Summary Badges (8-Point Grid) -->
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                        <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">HARI TERISI</div>
                                        <div class="fw-bold text-success num-tabular" style="font-size: 0.875rem;"><?= $heatmap_staf['summary']['total_hari_terisi'] ?> Hari</div>
                                    </div>
                                    <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                        <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">TOTAL KEGIATAN</div>
                                        <div class="fw-bold text-primary num-tabular" style="font-size: 0.875rem;"><?= $heatmap_staf['summary']['total_log_items'] ?> Kegiatan</div>
                                    </div>
                                    <div class="bg-light border rounded-3 px-3 py-1.5 text-center" style="min-width: 104px;">
                                        <div class="text-muted fw-semibold" style="font-size: 0.6875rem; letter-spacing: 0.04em;">RATA-RATA / HARI</div>
                                        <div class="fw-bold text-dark num-tabular" style="font-size: 0.875rem;"><?= str_replace('.', ',', $heatmap_staf['summary']['rata_rata_per_hari_aktif']) ?> Log</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Grid Kalender 7 Kolom (Senin s/d Minggu) -->
                            <div class="calendar-heatmap-grid mb-3">
                                <!-- Header Hari -->
                                <div class="calendar-header-cell">Sen</div>
                                <div class="calendar-header-cell">Sel</div>
                                <div class="calendar-header-cell">Rab</div>
                                <div class="calendar-header-cell">Kam</div>
                                <div class="calendar-header-cell">Jum</div>
                                <div class="calendar-header-cell weekend-header">Sab</div>
                                <div class="calendar-header-cell weekend-header">Min</div>

                                <!-- Leading empty cells for alignment -->
                                <?php for ($emptyIdx = 1; $emptyIdx < $heatmap_staf['first_day_of_week']; $emptyIdx++): ?>
                                    <div class="heatmap-empty-cell"></div>
                                <?php endfor; ?>

                                <!-- Day Cells -->
                                <?php foreach ($heatmap_staf['days'] as $day): ?>
                                    <?php
                                        $levelClass = 'heatmap-level-' . $day['level'];
                                        $tanggalMerahClass = $day['is_tanggal_merah'] ? 'is-tanggal-merah' : '';
                                        $tooltipTitle = esc($day['date_formatted']);
                                        if ($day['is_holiday']) {
                                            $tooltipTitle .= ' - ' . esc($day['holiday_name']);
                                        } elseif ($day['is_weekend']) {
                                            $tooltipTitle .= ' - Akhir Pekan';
                                        }
                                        $tooltipTitle .= ' (' . $day['count_logs'] . ' Kegiatan)';
                                    ?>
                                    <div class="heatmap-day-cell <?= $levelClass ?> <?= $tanggalMerahClass ?>" 
                                         data-is-staf="1"
                                         data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                         data-day-num="<?= $day['day_num'] ?>"
                                         data-date-str="<?= esc($day['date_str']) ?>"
                                         data-tanggal="<?= esc($day['date_str']) ?>"
                                         data-tanggal-formatted="<?= esc($day['date_formatted']) ?>"
                                         data-is-holiday="<?= $day['is_holiday'] ? '1' : '0' ?>"
                                         data-holiday-name="<?= esc($day['holiday_name'] ?? '') ?>"
                                         data-is-weekend="<?= $day['is_weekend'] ? '1' : '0' ?>"
                                         data-is-tanggal-merah="<?= $day['is_tanggal_merah'] ? '1' : '0' ?>"
                                         data-has-draft="<?= $day['has_draft'] ? '1' : '0' ?>"
                                         data-has-terkirim="<?= $day['has_terkirim'] ? '1' : '0' ?>"
                                         data-total-kegiatan="<?= $day['count_logs'] ?>"
                                         data-logs='<?= htmlspecialchars(json_encode($day['logs']), ENT_QUOTES, 'UTF-8') ?>'
                                         data-bs-toggle="tooltip"
                                         data-bs-placement="top"
                                         title="<?= $tooltipTitle ?>">
                                        <div class="day-header">
                                            <span class="day-num"><?= $day['day_num'] ?></span>
                                            <?php if ($day['is_holiday']): ?>
                                                <i class="bi bi-sun-fill holiday-indicator" style="font-size: 0.65rem;" title="<?= esc($day['holiday_name']) ?>"></i>
                                            <?php elseif ($day['is_weekend']): ?>
                                                <i class="bi bi-moon-stars text-secondary" style="font-size: 0.65rem;"></i>
                                            <?php elseif ($day['count_logs'] === 0): ?>
                                                <i class="bi bi-dash text-muted opacity-50" style="font-size: 0.75rem;"></i>
                                            <?php elseif ($day['has_draft']): ?>
                                                <i class="bi bi-pencil-fill text-warning-emphasis" style="font-size: 0.65rem;"></i>
                                            <?php else: ?>
                                                <i class="bi bi-check2-circle text-success" style="font-size: 0.65rem;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="day-footer">
                                            <?php if ($day['count_logs'] === 0): ?>
                                                <span class="badge-count text-muted">-</span>
                                            <?php else: ?>
                                                <span class="badge-count <?= in_array($day['level'], [3, 4]) ? 'badge-subtle-custom' : 'fw-bold' ?>">
                                                    <?= $day['count_logs'] ?> Log
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Legend Footer (8-Point Grid Standard) -->
                            <div class="heatmap-legend-container mt-3 pt-3">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                                    <!-- Sisi Kiri: Keterangan & Swatch Skala Intensitas -->
                                    <div class="d-flex align-items-center flex-wrap gap-2.5">
                                        <span class="badge bg-light text-secondary border px-3 py-2 fw-bold text-uppercase d-inline-flex align-items-center" style="font-size: 0.6875rem; letter-spacing: 0.05em; border-radius: 8px; min-height: 32px;">Keterangan</span>
                                        
                                        <div class="d-inline-flex align-items-center bg-danger-subtle text-danger border border-danger-subtle rounded-pill shadow-xs" style="font-size: 0.75rem; font-weight: 600; min-height: 32px; padding: 6px 14px; gap: 8px;">
                                            <i class="bi bi-calendar-x-fill" style="font-size: 0.75rem;"></i>
                                            <span>Tgl Merah / Libur</span>
                                        </div>

                                        <!-- Strip Swatch Skala Intensitas (Bento Capsule) -->
                                        <div class="d-inline-flex align-items-center bg-light rounded-pill border border-light-subtle flex-wrap shadow-xs" style="min-height: 32px; padding: 6px 16px; gap: 16px;">
                                            <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                                <span class="heatmap-legend-swatch" style="background-color: #ffffff; border: 1px solid #cbd5e1;"></span>
                                                <span class="text-secondary fw-medium">0 Log</span>
                                            </div>
                                            <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                                <span class="heatmap-legend-swatch" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;"></span>
                                                <span class="text-dark fw-medium">1 - 2</span>
                                            </div>
                                            <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                                <span class="heatmap-legend-swatch" style="background-color: #dcfce7; border: 1px solid #86efac;"></span>
                                                <span class="text-dark fw-medium">3 - 4</span>
                                            </div>
                                            <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                                <span class="heatmap-legend-swatch" style="background-color: #22c55e; border: 1px solid #16a34a;"></span>
                                                <span class="text-dark fw-medium">5 - 6</span>
                                            </div>
                                            <div class="d-flex align-items-center" style="font-size: 0.75rem; gap: 8px;">
                                                <span class="heatmap-legend-swatch" style="background-color: #15803d; border: 1px solid #14532d;"></span>
                                                <span class="text-dark fw-medium">&gt; 6 Log</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sisi Kanan: Hint Interaksi Kalender -->
                                    <div class="d-inline-flex align-items-center text-primary bg-primary-subtle rounded-pill border border-primary-subtle shadow-xs" style="font-size: 0.75rem; font-weight: 600; min-height: 32px; padding: 6px 16px; gap: 8px;">
                                        <i class="bi bi-cursor-fill" style="font-size: 0.75rem;"></i>
                                        <span>Klik tanggal pada kalender untuk melihat rincian pekerjaan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                        <i class="bi bi-person-lines-fill fs-1 text-primary opacity-50 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Pilih Staf untuk Memulai Penilaian</h6>
                        <p class="text-muted small mb-0">Silakan pilih nama staf dari menu dropdown di atas untuk melihat target dan memberikan penilaian kinerja.</p>
                    </div>
                <?php endif; ?>

            </div> <!-- End Tab Staf -->
            <?php endif; ?>

        </div>
    </div>
</div>
</div>

<!-- MODAL DETAIL LOG PEKERJAAN PER TANGGAL (100% BEBAS EMOJI - 8pt Grid System) -->
<div class="modal fade" id="modalDetailLogTanggal" tabindex="-1" aria-labelledby="modalDetailLogTanggalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="border-top: 4px solid #0d6efd !important;">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 40px; height: 40px; border-radius: 12px;">
                        <i class="bi bi-calendar2-week fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalDetailLogTanggalLabel" style="font-size: 1rem; line-height: 1.25;">Rincian Pekerjaan Harian</h6>
                        <small class="text-secondary d-flex align-items-center" id="modalLogTanggalSub" style="font-size: 0.75rem; margin-top: 2px;">-</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Tombol Navigasi Tanggal Cepat (Prev / Next) -->
                    <div class="btn-group btn-group-sm shadow-xs" role="group" aria-label="Navigasi Tanggal">
                        <button type="button" class="btn btn-outline-secondary btn-nav-date btn-tactile d-inline-flex align-items-center justify-content-center" id="btnModalPrevDay" title="Hari Sebelumnya" style="width: 32px; height: 32px; padding: 0; border-radius: 8px 0 0 8px;">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-nav-date btn-tactile d-inline-flex align-items-center justify-content-center" id="btnModalNextDay" title="Hari Berikutnya" style="width: 32px; height: 32px; padding: 0; border-radius: 0 8px 8px 0;">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close ms-2 btn-tactile" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4">
                <!-- Header Info Banner Tanggal -->
                <div class="card border rounded-4 shadow-xs mb-3" style="background-color: #f8fafc; border-color: #e2e8f0 !important; padding: 16px 20px;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-0 lh-sm" id="modalLogInfoTanggal" style="font-size: 1.125rem;">-</h5>
                            <div class="mt-1" id="modalLogInfoStatusHari" style="display: none;"></div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 flex-shrink-0">
                            <!-- Status Mode Revisi Badge (Jika sudah dibuka kuncinya) -->
                            <div id="modalLogBadgeRevisiAktif" style="display: none;">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 fw-bold shadow-xs rounded-pill" style="font-size: 0.75rem; height: 32px; display: inline-flex; align-items: center;">
                                    <i class="bi bi-pencil-square me-1.5"></i> Mode Revisi Aktif
                                </span>
                            </div>
                            <!-- Tombol Izinkan Revisi (Khusus Atasan/Admin pada Kalender Staf jika Terkirim) -->
                            <div id="modalLogBtnRevisiContainer" style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark border-warning-subtle fw-bold rounded-pill px-3.5 shadow-xs btn-tactile" id="btnModalBukaKunci" style="height: 36px; font-size: 0.75rem; display: inline-flex; align-items: center;">
                                    <i class="bi bi-pencil-square text-warning me-1.5"></i> Izinkan Revisi Laporan
                                </button>
                            </div>
                            <!-- Badge Total Kegiatan -->
                            <div class="d-inline-flex align-items-center bg-white border border-primary-subtle rounded-pill shadow-xs px-3 py-1" style="height: 36px; gap: 8px;">
                                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.6875rem; letter-spacing: 0.05em;">Total:</span>
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold num-tabular" id="modalLogBadgeTotal" style="font-size: 0.8125rem;">0 Kegiatan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Rincian Pekerjaan Pada Tanggal Ini -->
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size: 0.875rem; gap: 8px;">
                        <i class="bi bi-list-check text-primary fs-6"></i>
                        <span>Daftar Aktivitas & Bukti Pekerjaan</span>
                    </h6>
                </div>
                <div class="table-responsive bg-white border rounded-4 shadow-xs overflow-hidden mb-3" id="modalLogTableContainer">
                    <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                        <thead>
                            <tr class="text-center align-middle" style="background-color: #f8fafc;">
                                <th style="width: 48px;" class="text-center">No</th>
                                <th style="width: 96px;" class="text-center">Jenis</th>
                                <th class="text-start" style="min-width: 220px;">Deskripsi Aktivitas</th>
                                <th class="text-start" style="min-width: 200px;">Indikator Kinerja / RHK</th>
                                <th style="width: 112px;" class="text-center">Realisasi</th>
                                <th style="width: 104px;" class="text-center">Bukti</th>
                                <th style="width: 112px;" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="modalLogTabelBody">
                            <!-- Dynamic rows via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State jika 0 log -->
                <div id="modalLogEmptyState" style="display: none;" class="text-center py-5 px-3 bg-light rounded-4 border border-dashed my-2">
                    <i class="bi bi-calendar-x text-muted fs-1 mb-2 d-block opacity-40"></i>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9375rem;">Tidak Ada Laporan Kegiatan</h6>
                    <p class="text-muted small mb-0" id="modalLogEmptyDesc">Tidak ada catatan aktivitas harian yang diisi pada tanggal ini.</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center px-3 px-md-4 py-2.5">
                <div>
                    <!-- Tombol Shortcut ke Lapor Harian (Khusus Kalender Saya) -->
                    <a href="#" id="btnModalGoToLapor" class="btn btn-outline-primary rounded-pill px-3.5 py-1.5 fw-bold btn-tactile shadow-xs" style="font-size: 0.8125rem; display: none; height: 36px; align-items: center; gap: 6px;">
                        <i class="bi bi-box-arrow-up-right"></i> Buka di Menu Lapor Harian
                    </a>
                </div>
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold btn-tactile shadow-xs" style="min-height: 36px; font-size: 0.8125rem;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const bulanTerpilih = <?= esc($bulan_terpilih) ?>;
    const tahunTerpilih = <?= esc($tahun_terpilih) ?>;

    $(document).ready(function() {
        if ($('#selectStaf').length) {
            $('#selectStaf').select2({ 
                width: '100%', 
                placeholder: "Cari Nama Staf...",
                allowClear: false
            });
            $('#selectStaf').on('select2:select', function (e) { $(this).closest('form').submit(); });
            
            $('#selectStaf').on('select2:open', function () {
                setTimeout(function() {
                    document.querySelector('.select2-search__field').focus();
                }, 50);
            });
        }

        // Debounce 600ms untuk input tahun filter (cegah multiple submit saat mengetik)
        let tahunDebounceTimer = null;
        const tahunInput = document.getElementById('tahunFilterInput');
        if (tahunInput) {
            tahunInput.addEventListener('input', function() {
                clearTimeout(tahunDebounceTimer);
                tahunDebounceTimer = setTimeout(function() {
                    const val = parseInt(tahunInput.value);
                    if (!isNaN(val) && val >= 2020 && val <= 2099) {
                        tahunInput.closest('form').submit();
                    }
                }, 600);
            });
        }

        // Cegah perubahan nilai angka secara tidak sengaja saat pengguna scrolling halaman dengan mouse wheel
        $(document).on('wheel', 'input[type="number"]', function (e) {
            $(this).blur();
        });

        function getPredikatInfo(val) {
            if (isNaN(val) || val === '' || val === null) {
                return { label: '-', class: 'bg-light text-muted border', textClass: 'secondary' };
            }
            // Threshold standar ECC: SK ≤25 | K >25-75 | BP >75-90 | Baik >90-100 | SB >100-150
            if (val <= 25) return { label: 'Sangat Kurang', class: 'bg-danger', textClass: 'danger' };
            if (val <= 75) return { label: 'Kurang', class: 'bg-warning text-dark', textClass: 'warning' };
            if (val <= 90) return { label: 'Butuh Perbaikan', class: 'bg-info text-dark', textClass: 'info' };
            if (val <= 100) return { label: 'Baik', class: 'bg-primary', textClass: 'primary' };
            return { label: 'Sangat Baik', class: 'bg-success', textClass: 'success' };
        }

        function calculateOverallStafScore() {
            let total = 0;
            let count = 0;
            let rhkInputs = $('#tablePenilaianStaf tbody .input-nilai-capaian');
            let rhkTotal = rhkInputs.length;
            let rhkFilled = 0;

            // 1. Hitung Nilai RHK (hanya dari baris tbody)
            rhkInputs.each(function() {
                let rawVal = $(this).val();
                let v = parseFloat(rawVal);
                let badgeContainer = $(this).closest('td').find('.predikat-badge-container');

                if (rawVal !== '' && !isNaN(v)) {
                    if (!$(this).hasClass('is-invalid')) {
                        total += v;
                        count++;
                        rhkFilled++;
                    }
                    let p = getPredikatInfo(v);
                    badgeContainer.html('<span class="badge ' + p.class + ' rounded-pill px-2 py-0.5 badge-predikat-pop" style="font-size:0.72rem;">' + p.label + '</span>');
                    $(this).removeClass('border-warning shadow-sm');
                } else {
                    badgeContainer.html('<span class="badge bg-light text-muted border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                }
            });

            // 2. Hitung Nilai Akumulasi Tugas Tambahan (jika diisi)
            let hasTambahan = $('#inputNilaiTambahanGabungan').length > 0;
            let tambahanFilled = false;
            let rawTambahan = $('#inputNilaiTambahanGabungan').val();
            let vTambahan = parseFloat(rawTambahan);

            if (hasTambahan) {
                let hintContainer = $('#hintTambahanContainer');
                if (rawTambahan !== '' && !isNaN(vTambahan) && vTambahan >= 0 && vTambahan <= 150) {
                    total += vTambahan;
                    count++;
                    tambahanFilled = true;
                    $('#textTambahanStatus').text(vTambahan + '%');
                    if (hintContainer.length) {
                        hintContainer.html('<span class="badge bg-success-subtle text-success border border-success-subtle mt-1 rounded-pill px-2 py-0.5 badge-predikat-pop" style="font-size:0.72rem;"><i class="bi bi-check-circle me-1"></i> Terisi</span>');
                    }
                    $('#inputNilaiTambahanGabungan').removeClass('border-warning shadow-sm');
                } else {
                    $('#textTambahanStatus').text('Belum Dinilai');
                    if (hintContainer.length) {
                        hintContainer.html('<span class="badge bg-light text-muted border border-secondary-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                    }
                }
            }

            // 3. Update counter & unfilled hints
            $('#countRhkTerisi').text(rhkFilled);
            
            let totalComponents = rhkTotal + (hasTambahan ? 1 : 0);
            let totalFilled = rhkFilled + (hasTambahan && tambahanFilled ? 1 : 0);
            let totalUnfilled = totalComponents - totalFilled;

            if (totalUnfilled > 0) {
                $('#textCountUnfilled').text(totalUnfilled);
                $('#badgeHintUnfilled').removeClass('d-none');
                $('#badgeAllFilled').addClass('d-none');
            } else {
                $('#badgeHintUnfilled').addClass('d-none');
                $('#badgeAllFilled').removeClass('d-none');
            }

            // 4. Kalkulasi Nilai Akhir
            let avg = count > 0 ? (Math.round((total / count) * 100) / 100) : 0;
            $('#totalKinerjaStafText').text(avg.toString().replace('.', ','));

            let pRata = getPredikatInfo(count > 0 ? avg : null);
            
            let textEl = $('#totalKinerjaStafText');
            let wrapper = $('#cardKinerjaStafSummary');
            let badgeEl = $('#totalPredikatStafBadge');

            textEl.removeClass('text-success text-secondary text-danger text-warning text-primary text-info');
            wrapper.removeClass('border-success border-secondary border-danger border-warning border-primary border-info');

            textEl.addClass('text-' + pRata.textClass);
            wrapper.addClass('border-' + pRata.textClass);

            badgeEl.attr('class', 'badge ' + pRata.class + ' fs-6 px-3 py-2 rounded-pill badge-predikat-pop').text(pRata.label);

            return { totalUnfilled, count, totalFilled, totalComponents };
        }

        // Jalankan inisialisasi perhitungan saat halaman selesai dimuat
        calculateOverallStafScore();

        // Auto-calculate Rata-rata Kinerja Staf & Update Predikat Per-Baris secara Real-time
        $(document).on('input change keyup', '.input-nilai-capaian, #inputNilaiTambahanGabungan', function() {
            if ($(this).hasClass('input-nilai-capaian')) {
                var val = parseFloat($(this).val());
                var error = $(this).parent().siblings('.invalid-feedback');
                var btnSubmit = $('#formPenilaian button[type="submit"]');

                if (!isNaN(val) && (val < 0 || val > 150)) {
                    $(this).addClass('is-invalid');
                    error.show();
                    btnSubmit.prop('disabled', true);
                } else {
                    $(this).removeClass('is-invalid');
                    error.hide();
                    if ($('.input-nilai-capaian.is-invalid').length === 0) {
                        btnSubmit.prop('disabled', false);
                    }
                }
            }

            calculateOverallStafScore();
        });

        // Handle Kosongkan form (Reset)
        $('#formPenilaian button[type="reset"], #btnResetNilaiStaf').on('click', function(e) {
            e.preventDefault();

            function doResetAndSubmit() {
                $('.input-nilai-capaian, #inputNilaiTambahanGabungan').val('').removeClass('is-invalid border-warning shadow-sm').removeAttr('title');
                $('.invalid-feedback').hide();
                $('.predikat-badge-container').html('<span class="badge bg-light text-muted border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                if ($('#hintTambahanContainer').length) {
                    $('#hintTambahanContainer').html('<span class="badge bg-light text-muted border border-secondary-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                }
                calculateOverallStafScore();
                $('#penilaianActionInput').val('reset');
                $('#formPenilaian')[0].submit();
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reset & Kosongkan Penilaian?',
                    text: "Seluruh nilai capaian staf pada periode ini akan dikosongkan dan di-reset seperti belum pernah dinilai.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Reset Penilaian',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        doResetAndSubmit();
                    }
                });
            } else {
                if (confirm('Reset dan kosongkan seluruh nilai capaian staf pada periode ini seperti belum pernah dinilai?')) {
                    doResetAndSubmit();
                }
            }
        });

        // Hardening: Form Submission Validation, Unfilled Component Hints & Action Sync
        let isPublishConfirmed = false;
        let lastClickedAction = 'submit';

        $('#formPenilaian button[type="submit"]').on('click', function() {
            lastClickedAction = $(this).val() || 'submit';
            $('#penilaianActionInput').val(lastClickedAction);
        });

        $('#formPenilaian').on('submit', function(e) {
            let form = $(this);
            let hasInvalid = false;

            // Make sure the hidden input is always set before any button state changes
            $('#penilaianActionInput').val(lastClickedAction);

            form.find('.input-nilai-capaian').each(function() {
                let v = parseFloat($(this).val());
                if (!isNaN(v) && (v < 0 || v > 150)) {
                    hasInvalid = true;
                    $(this).addClass('is-invalid');
                }
            });

            let vTambahan = parseFloat($('#inputNilaiTambahanGabungan').val());
            if (!isNaN(vTambahan) && (vTambahan < 0 || vTambahan > 150)) {
                hasInvalid = true;
                $('#inputNilaiTambahanGabungan').addClass('is-invalid');
            }

            if (hasInvalid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Nilai Tidak Sesuai',
                    text: 'Terdapat nilai capaian yang di luar batas yang diperbolehkan (0 - 150%).'
                });
                return false;
            }

            let stats = calculateOverallStafScore();

            // Jika tombol yang ditekan adalah Simpan & Terbitkan Penilaian dan masih ada kolom yang kosong
            if (lastClickedAction === 'submit' && stats.totalUnfilled > 0 && !isPublishConfirmed) {
                e.preventDefault();
                Swal.fire({
                    title: 'Ada Nilai Belum Diisi',
                    html: `Terdapat <strong>${stats.totalUnfilled} komponen penilaian</strong> yang belum diisi nilainya.<br><br>` +
                          `Jika Anda tetap melanjutkan penerbitan, seluruh komponen yang kosong akan <strong>diberi nilai 0 (Nol)</strong> dan dihitung ke dalam nilai akhir kinerja staf.<br><br>` +
                          `Apakah Anda ingin tetap menerbitkan penilaian?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-circle-fill me-1"></i> Ya, Tetap Terbitkan (Nilai 0)',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isPublishConfirmed = true;

                        // Otomatis isi seluruh input RHK yang kosong dengan '0'
                        $('#tablePenilaianStaf tbody .input-nilai-capaian').each(function() {
                            if ($(this).val() === '') {
                                $(this).val('0');
                            }
                        });

                        // Otomatis isi input tugas tambahan jika ada dan kosong dengan '0'
                        if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').val('0');
                        }

                        // Hitung ulang kalkulasi di layar dengan nilai 0
                        calculateOverallStafScore();

                        $('#penilaianActionInput').val('submit');
                        form.find('button[type="submit"]').prop('disabled', true);
                        form.find('button[value="submit"]').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menerbitkan...');
                        form[0].submit();
                    } else {
                        // Highlight kolom yang masih kosong dengan border warning
                        $('.input-nilai-capaian').each(function() {
                            if ($(this).val() === '') {
                                $(this).addClass('border-warning shadow-sm');
                            }
                        });
                        if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').addClass('border-warning shadow-sm');
                        }
                        let firstEmpty = $('.input-nilai-capaian').filter(function() { return $(this).val() === ''; }).first();
                        if (firstEmpty.length) {
                            firstEmpty.focus();
                        } else if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').focus();
                        }
                    }
                });
                return false;
            }

            // Sync hidden action input before submission
            $('#penilaianActionInput').val(lastClickedAction);

            let submitBtns = form.find('button[type="submit"]');
            let activeBtn = form.find('button[value="' + lastClickedAction + '"]');
            if (activeBtn.length) {
                activeBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' + (lastClickedAction === 'submit' ? 'Menerbitkan...' : 'Menyimpan...'));
            }
            // Use setTimeout to disable buttons so current submit event carries the form data normally
            setTimeout(() => {
                submitBtns.prop('disabled', true);
            }, 10);
        });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("aria-controls");
            $('#active_tab_input').val(target);
            if (history.pushState) {
                history.pushState(null, null, '#' + target);
            } else {
                location.hash = '#' + target;
            }
        });

        if (window.location.hash) {
            var targetTab = document.querySelector('button[aria-controls="' + window.location.hash.substring(1) + '"]');
            if (targetTab) {
                var tab = new bootstrap.Tab(targetTab);
                tab.show();
                $('#active_tab_input').val(window.location.hash.substring(1));
            }
        }
        // =============================================
        // [HEATMAP KALENDER] State & Event Klik Sel Tanggal
        // =============================================
        let currentActiveHeatmapCell = null;

        function populateModalFromCell(cell) {
            if (!cell || !cell.length) return;
            currentActiveHeatmapCell = cell;

            const isStaf = cell.attr('data-is-staf') === '1';
            const stafId = cell.attr('data-staf-id') || '';
            const dateStr = cell.attr('data-date-str') || cell.attr('data-tanggal') || '';
            const tglFormatted = cell.attr('data-tanggal-formatted') || cell.data('tanggal-formatted') || cell.data('tanggalFormatted') || '-';
            const isHoliday = cell.attr('data-is-holiday') === '1' || cell.data('is-holiday') === 1 || cell.data('isHoliday') === 1;
            const holidayName = cell.attr('data-holiday-name') || cell.data('holiday-name') || cell.data('holidayName') || '';
            const isWeekend = cell.attr('data-is-weekend') === '1' || cell.data('is-weekend') === 1 || cell.data('isWeekend') === 1;
            const totalKegiatan = parseInt(cell.attr('data-total-kegiatan') || cell.data('total-kegiatan') || cell.data('totalKegiatan')) || 0;
            const hasDraft = cell.attr('data-has-draft') === '1' || cell.attr('data-has-draft') === 1;
            const hasTerkirim = cell.attr('data-has-terkirim') === '1' || cell.attr('data-has-terkirim') === 1;

            let logs = [];
            const rawLogs = cell.data('logs');
            if (Array.isArray(rawLogs)) {
                logs = rawLogs;
            } else if (typeof rawLogs === 'string') {
                try {
                    logs = JSON.parse(rawLogs);
                } catch (err) {
                    logs = [];
                }
            } else {
                const attrLogs = cell.attr('data-logs');
                if (attrLogs) {
                    try {
                        logs = JSON.parse(attrLogs);
                    } catch (err) {
                        logs = [];
                    }
                }
            }

            // Set Header Modal
            $('#modalLogTanggalSub').html('<i class="bi bi-calendar-check text-primary me-1.5"></i> ' + escHtml(tglFormatted));
            $('#modalLogInfoTanggal').text(tglFormatted);
            $('#modalLogBadgeTotal').text(`${totalKegiatan} Kegiatan`);

            if (isHoliday) {
                $('#modalLogInfoStatusHari').html(`<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-sun-fill me-1"></i> Libur Nasional: ${escHtml(holidayName)}</span>`).show();
            } else if (isWeekend) {
                $('#modalLogInfoStatusHari').html(`<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-moon-stars me-1"></i> Akhir Pekan (Sabtu / Minggu)</span>`).show();
            } else {
                $('#modalLogInfoStatusHari').empty().hide();
            }

            // Contextual Action Buttons & Badges
            const canManageRevisi = <?= (hasRole('admin') || $is_atasan || $is_penilai) ? 'true' : 'false' ?>;

            if (isStaf) {
                // Sembunyikan shortcut form lapor
                $('#btnModalGoToLapor').hide();

                if (canManageRevisi && stafId) {
                    if (hasTerkirim) {
                        // Tampilkan tombol Buka Kunci
                        $('#btnModalBukaKunci').data('tanggal', dateStr).data('staf-id', stafId).data('tanggal-formatted', tglFormatted);
                        $('#modalLogBtnRevisiContainer').show();
                        $('#modalLogBadgeRevisiAktif').hide();
                    } else if (hasDraft && totalKegiatan > 0) {
                        // Laporan sudah dalam mode revisi
                        $('#modalLogBtnRevisiContainer').hide();
                        $('#modalLogBadgeRevisiAktif').show();
                    } else {
                        $('#modalLogBtnRevisiContainer').hide();
                        $('#modalLogBadgeRevisiAktif').hide();
                    }
                } else {
                    $('#modalLogBtnRevisiContainer').hide();
                    $('#modalLogBadgeRevisiAktif').hide();
                }
            } else {
                // Kalender Mandiri (Saya)
                $('#modalLogBtnRevisiContainer').hide();
                $('#modalLogBadgeRevisiAktif').hide();

                // Tampilkan shortcut ke menu Lapor Kegiatan
                const laporUrl = '<?= site_url('log-kegiatan') ?>' + (dateStr ? ('?tanggal=' + encodeURIComponent(dateStr)) : '');
                $('#btnModalGoToLapor').attr('href', laporUrl).show();
            }

            // Update state tombol Prev / Next
            const prevCell = cell.prevAll('.heatmap-day-cell').first();
            const nextCell = cell.nextAll('.heatmap-day-cell').first();
            
            $('#btnModalPrevDay').prop('disabled', !prevCell.length);
            $('#btnModalNextDay').prop('disabled', !nextCell.length);

            const tbody = $('#modalLogTabelBody');
            tbody.empty();

            if (logs && logs.length > 0) {
                $('#modalLogEmptyState').hide();
                $('#modalLogTableContainer').show();

                logs.forEach((item, idx) => {
                    const isTambahan = item.is_tambahan == 1 || item.is_tambahan == '1';
                    const jenisBadge = isTambahan 
                        ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;">Tambahan</span>' 
                        : '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem;">Utama</span>';

                    let capaianVal = '-';
                    if (item.jumlah_capaian !== null && item.jumlah_capaian !== undefined && item.jumlah_capaian !== '') {
                        const num = parseFloat(item.jumlah_capaian);
                        if (!isNaN(num)) {
                            capaianVal = String(parseFloat(num.toFixed(4))).replace('.', ',');
                        }
                    }
                    const satuanText = item.satuan ? escHtml(item.satuan) : '';
                    const capaianFull = (capaianVal !== '-') ? `${capaianVal} <span class="small fw-normal text-muted">${satuanText}</span>` : '-';

                    // Sanitasi URL Bukti
                    let buktiBtn = '<span class="text-muted small">-</span>';
                    if (item.link_bukti && typeof item.link_bukti === 'string' && /^https?:\/\//i.test(item.link_bukti.trim())) {
                        buktiBtn = `<a href="${escHtml(item.link_bukti.trim())}" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary rounded-pill border px-3 py-1 btn-tactile shadow-xs" style="font-size: 0.75rem; height: 32px; display: inline-flex; align-items: center; justify-content: center; gap: 4px;"><i class="bi bi-box-arrow-up-right"></i> Bukti</a>`;
                    }

                    const statusBadge = (item.status === 'terkirim' || item.status === 'disetujui')
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-check-circle-fill"></i> Terkirim</span>'
                        : '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.72rem; display: inline-flex; align-items: center; gap: 4px;"><i class="bi bi-pencil-fill"></i> Draf</span>';

                    const descHtml = escHtml(item.deskripsi_kegiatan || '-').replace(/\n/g, '<br>');
                    const indikatorText = escHtml(item.indikator_kinerja || '-');

                    const rowHtml = `
                        <tr>
                            <td class="text-center fw-bold text-muted num-tabular" style="padding: 12px 14px;">${idx + 1}</td>
                            <td class="text-center" style="padding: 12px 14px;">${jenisBadge}</td>
                            <td class="text-dark lh-sm" style="padding: 12px 14px; font-size: 0.8125rem;">${descHtml}</td>
                            <td class="text-secondary lh-sm" style="padding: 12px 14px; font-size: 0.8125rem;">${indikatorText}</td>
                            <td class="text-center fw-bold text-primary num-tabular" style="padding: 12px 14px; font-size: 0.875rem;">${capaianFull}</td>
                            <td class="text-center" style="padding: 12px 14px;">${buktiBtn}</td>
                            <td class="text-center" style="padding: 12px 14px;">${statusBadge}</td>
                        </tr>
                    `;
                    tbody.append(rowHtml);
                });
            } else {
                $('#modalLogTableContainer').hide();
                if (isHoliday) {
                    $('#modalLogEmptyDesc').text(`Hari Libur Nasional: ${holidayName}. Tidak ada catatan laporan kegiatan.`);
                } else if (isWeekend) {
                    $('#modalLogEmptyDesc').text('Akhir pekan (Sabtu / Minggu). Tidak ada catatan laporan kegiatan.');
                } else {
                    $('#modalLogEmptyDesc').text('Tidak ada catatan laporan kegiatan pada tanggal ini.');
                }
                $('#modalLogEmptyState').show();
            }

            // Tampilkan Modal secara aman menggunakan Bootstrap 5 / jQuery
            const modalEl = document.getElementById('modalDetailLogTanggal');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined') {
                    if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else {
                        (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)).show();
                    }
                } else if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
                    $('#modalDetailLogTanggal').modal('show');
                }
            }
        }

        $(document).on('click', '.heatmap-day-cell', function(e) {
            e.preventDefault();
            populateModalFromCell($(this));
        });

        // Event Navigasi Hari Prev / Next di Modal
        $(document).on('click', '#btnModalPrevDay', function(e) {
            e.preventDefault();
            if (currentActiveHeatmapCell) {
                const prevCell = currentActiveHeatmapCell.prevAll('.heatmap-day-cell').first();
                if (prevCell && prevCell.length) {
                    populateModalFromCell(prevCell);
                }
            }
        });

        $(document).on('click', '#btnModalNextDay', function(e) {
            e.preventDefault();
            if (currentActiveHeatmapCell) {
                const nextCell = currentActiveHeatmapCell.nextAll('.heatmap-day-cell').first();
                if (nextCell && nextCell.length) {
                    populateModalFromCell(nextCell);
                }
            }
        });

        // Event Klik Buka Kunci / Revisi dari Modal
        $(document).on('click', '#btnModalBukaKunci, .btn-buka-kunci-penilaian', function(e) {
            e.preventDefault();
            const tanggal   = $(this).data('tanggal') || $(this).attr('data-tanggal');
            const stafId    = $(this).data('staf-id') || $(this).attr('data-staf-id');
            const tglFormatted = $(this).data('tanggal-formatted') || tanggal;
            const csrfName  = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!tanggal || !stafId) {
                alert('Parameter tanggal atau staf ID tidak valid.');
                return;
            }

            function executeBukaKunciStaf() {
                $.ajax({
                    url: '<?= site_url('log-kegiatan/buka-kunci') ?>',
                    type: 'POST',
                    data: {
                        target_user_id: stafId,
                        tanggal: tanggal,
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
                    title: 'Izinkan Revisi Laporan?',
                    html: `Laporan harian staf tanggal <strong>${tglFormatted}</strong> akan dibuka untuk direvisi.<br>Staf dapat memperbarui dan mengirim kembali laporan tersebut.<br><br><span class='text-dark fw-semibold'>Setelah dikirim ulang, laporan akan terkunci kembali otomatis.</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-pencil-square me-1"></i> Ya, Izinkan Revisi',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeBukaKunciStaf();
                    }
                });
            } else {
                if (confirm(`Izinkan Revisi Laporan harian staf tanggal ${tglFormatted}?\n\nStaf dapat memperbarui dan mengirim kembali laporan tersebut.`)) {
                    executeBukaKunciStaf();
                }
            }
        });

        // Helper fungsi escape HTML untuk keamanan XSS
        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Inisialisasi Tooltip Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // =============================================
        // [SUPERADMIN] Batalkan Persetujuan Target Bulanan Staf
        // =============================================
        $(document).on('click', '.btn-batal-approve-target-penilaian', function(e) {
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
                    html: `Persetujuan target bulanan staf akan dibatalkan.<br>Staf akan dapat merevisi dan mengajukan kembali ke atasan.`,
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
                if (confirm('Batalkan Persetujuan Target?\n\nPersetujuan target bulanan staf akan dibatalkan agar staf dapat merevisi.')) {
                    executeCancel();
                }
            }
        });

    });
</script>
<?= $this->endSection() ?>
