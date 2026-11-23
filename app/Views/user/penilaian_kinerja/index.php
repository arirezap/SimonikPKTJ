<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/vendor/select2/select2.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>" rel="stylesheet">
<style>
    .num-tabular {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-text-muted-aa {
        color: #475569;
    }

    /* Proportional Bento Table (Desktop Fit & Text Wrapping) */
    .table-bento-wrapper {
        overflow-x: visible;
    }
    .table-bento {
        width: 100%;
        table-layout: fixed;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.75rem;
        line-height: 1.3;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
        vertical-align: middle;
        white-space: normal;
        overflow-wrap: break-word;
    }
    .table-bento tbody td {
        padding: 12px 16px;
        vertical-align: top;
        border-color: #f1f5f9;
        font-size: 0.875rem;
        line-height: 1.45;
        overflow-wrap: anywhere;
        word-break: break-word;
        transition: background-color 0.15s ease;
    }
    .table-bento tbody td.td-no,
    .table-bento tbody td.td-aksi {
        vertical-align: top;
        padding: 12px 8px;
        white-space: nowrap;
    }
    .table-bento tbody tr:hover td {
        background-color: #f8fafc;
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    .table-bento tfoot td {
        padding: 12px 16px;
        border-top: 2px solid #e2e8f0;
        vertical-align: middle;
    }

    .col-no-bento {
        width: 56px;
        padding-left: 8px !important;
        padding-right: 8px !important;
        white-space: nowrap !important;
    }
    .col-target {
        width: auto;
    }
    .col-metric {
        width: 15%;
    }
    .col-nilai {
        width: 19%;
    }
    .col-tanggal-bento {
        width: 10%;
    }
    .col-jenis-bento {
        width: 11%;
    }
    .col-bukti-bento {
        width: 10%;
    }
    .col-aksi-bento {
        width: 12%;
    }

    /* Number Spin Button Sanitization */
    .input-nilai-capaian::-webkit-outer-spin-button,
    .input-nilai-capaian::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-nilai-capaian {
        -moz-appearance: textfield;
        min-width: 0;
    }

    .ecc-focus-ring:focus,
    .ecc-focus-ring:focus-visible,
    .table-bento input.form-control:focus,
    .table-bento select.form-select:focus,
    .btn-bukti-link:focus-visible,
    .btn-nav-date:focus-visible {
        outline: none;
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
    }

    .btn-bukti-link {
        height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 0.75rem;
    }

    /* Tactile Interaction & Micro-Motion */
    .badge-predikat-pop {
        transform: scale(1.04);
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .score-card-transition {
        transition: border-color 0.25s ease, color 0.25s ease, background-color 0.25s ease;
    }
    .score-value-hero {
        font-size: 1.75rem !important;
        line-height: 1.1;
        white-space: nowrap;
    }
    .tab-content > .tab-pane {
        transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .pk-filter-control {
        height: 36px;
    }
    .pk-summary-pill {
        white-space: normal;
        text-align: start;
        line-height: 1.35;
    }
    .pk-predikat-details {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
    }
    .pk-predikat-details summary {
        cursor: pointer;
        font-weight: 700;
        font-size: 0.875rem;
        color: #0f172a;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 24px;
    }
    .pk-predikat-details summary::-webkit-details-marker {
        display: none;
    }
    .pk-predikat-details summary::after {
        content: "+";
        font-size: 0.95rem;
        font-weight: 700;
        color: #475569;
    }
    .pk-predikat-details[open] summary::after {
        content: "−";
    }
    .heatmap-kpi-grid {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .heatmap-kpi-item {
        min-width: 104px;
    }
    .heatmap-kpi-label {
        font-size: 0.75rem;
        line-height: 1.25;
    }
    .heatmap-kpi-val {
        font-size: 0.875rem;
    }
    .heatmap-swatch-grid {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .heatmap-hint-box {
        font-size: 0.75rem;
        line-height: 1.4;
        text-align: center;
        white-space: normal;
    }
    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease;
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    /* Select2 8-Point Grid Alignment */
    .select2-container--bootstrap-5 .select2-selection--single,
    .select2-container--default .select2-selection--single {
        min-height: 36px !important;
        padding: 4px 12px !important;
        border-radius: 8px !important;
        border: 1px solid var(--bs-success) !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: var(--bs-success-text-emphasis) !important;
        padding-left: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border-radius: 8px !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
        font-size: 0.875rem !important;
    }

    /* Calendar Heatmap Matrix (Strict DESIGN.md Tokens) */
    .calendar-heatmap-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    }
    .calendar-heatmap-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
    }
    .calendar-header-cell {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
        text-align: center;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
    }
    .calendar-header-cell.weekend-header {
        color: #dc2626;
    }
    .heatmap-day-cell {
        min-height: 64px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
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
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        border-color: #0d6efd !important;
        z-index: 2;
    }
    .heatmap-day-cell:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        border-color: #0d6efd !important;
        z-index: 3;
    }
    .heatmap-day-cell:active {
        transform: scale(0.97);
    }
    .heatmap-empty-cell {
        min-height: 64px;
        background-color: transparent;
        border: 1px dashed #f1f5f9;
        border-radius: 8px;
        opacity: 0.5;
    }

    /* Tanggal Merah */
    .heatmap-day-cell.is-tanggal-merah .day-num,
    .heatmap-day-cell.is-tanggal-merah .holiday-indicator {
        color: #dc2626 !important;
        font-weight: 800;
    }
    .heatmap-day-cell.heatmap-level-3.is-tanggal-merah .day-num,
    .heatmap-day-cell.heatmap-level-4.is-tanggal-merah .day-num {
        color: #fef2f2 !important;
    }

    /* Heatmap Intensity Levels (DESIGN.md Tokens + WCAG AA) */
    .heatmap-level-0 {
        background-color: #ffffff;
        border-color: #e2e8f0;
        color: #475569;
    }
    .heatmap-level-0 .day-num {
        color: #0f172a;
    }
    .heatmap-level-0 .badge-count {
        color: #475569;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .heatmap-level-1 {
        background-color: #ecfdf5;
        border-color: var(--bs-success-border-subtle);
        color: var(--bs-success-text-emphasis);
    }
    .heatmap-level-1 .day-num {
        color: var(--bs-success-text-emphasis);
    }
    .heatmap-level-1 .badge-count {
        background-color: #ffffff;
        color: var(--bs-success-text-emphasis);
        border: 1px solid var(--bs-success-border-subtle);
    }
    .heatmap-level-2 {
        background-color: var(--bs-success-bg-subtle);
        border-color: #10b981;
        color: var(--bs-success-text-emphasis);
    }
    .heatmap-level-2 .day-num {
        color: var(--bs-success-text-emphasis);
    }
    .heatmap-level-2 .badge-count {
        background-color: #ffffff;
        color: var(--bs-success-text-emphasis);
        border: 1px solid #10b981;
    }
    .heatmap-level-3 {
        background-color: #10b981;
        border-color: #059669;
        color: #ffffff;
    }
    .heatmap-level-3 .day-num {
        color: #ffffff;
    }
    .heatmap-level-3 .badge-subtle-custom {
        background-color: #ffffff;
        color: #059669;
    }
    .heatmap-level-4 {
        background-color: #059669;
        border-color: var(--bs-success-text-emphasis);
        color: #ffffff;
    }
    .heatmap-level-4 .day-num {
        color: #ffffff;
    }
    .heatmap-level-4 .badge-subtle-custom {
        background-color: #ffffff;
        color: var(--bs-success-text-emphasis);
    }

    /* Status Draf */
    .heatmap-day-cell.is-draft {
        background-color: #fef3c7 !important;
        border-color: #f59e0b !important;
        color: #92400e !important;
    }
    .heatmap-day-cell.is-draft:hover {
        border-color: #d97706 !important;
    }
    .heatmap-day-cell.is-draft .day-num {
        color: #92400e !important;
        font-weight: 700;
    }
    .heatmap-day-cell.is-draft .badge-count,
    .heatmap-day-cell.is-draft .badge-subtle-custom {
        background-color: #ffffff !important;
        color: #92400e !important;
        border: 1px solid #f59e0b !important;
        font-weight: 700;
    }
    .heatmap-day-cell.is-draft.is-tanggal-merah .day-num {
        color: #dc2626 !important;
    }

    .heatmap-day-cell .day-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        line-height: 1;
    }
    .heatmap-day-cell .day-num {
        font-size: 0.875rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .heatmap-day-cell .day-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
    }
    .heatmap-day-cell .badge-count {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        line-height: 1.2;
    }

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
    .heatmap-swatch-empty {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .heatmap-swatch-draft {
        background-color: #fef3c7;
        border: 1px solid #f59e0b;
    }
    .heatmap-swatch-l1 {
        background-color: #ecfdf5;
        border: 1px solid var(--bs-success-border-subtle);
    }
    .heatmap-swatch-l2 {
        background-color: var(--bs-success-bg-subtle);
        border: 1px solid #10b981;
    }
    .heatmap-swatch-l3 {
        background-color: #10b981;
        border: 1px solid #059669;
    }
    .heatmap-swatch-l4 {
        background-color: #059669;
        border: 1px solid var(--bs-success-text-emphasis);
    }

    /* Modal Rincian Pekerjaan */
    #modalDetailLogTanggal .modal-content {
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.16);
        border-radius: 16px;
        border-top: 4px solid #0d6efd !important;
    }
    #modalDetailLogTanggal #modalLogTableContainer {
        max-height: 440px;
        overflow-y: auto;
    }
    .btn-nav-date {
        min-width: 36px;
        min-height: 36px;
        width: 36px;
        height: 36px;
        padding: 0;
        background-color: #ffffff;
        transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .btn-nav-date:hover:not(:disabled) {
        background-color: #eff6ff;
        color: #0d6efd;
        border-color: #e2e8f0;
    }
    .btn-nav-date:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Responsive Mobile Card Stack (< 768px) */
    @media (max-width: 767.98px) {
        .pk-page-wrap {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .pk-main-card {
            border-radius: 12px !important;
        }
        .pk-main-card-body {
            padding: 12px !important;
        }
        .form-control,
        .form-select {
            font-size: var(--bs-body-font-size, 1rem) !important;
        }
        .pk-filter-control {
            height: auto;
            min-height: 44px;
        }
        .input-nilai-capaian,
        .input-group .input-group-text {
            min-height: 44px;
        }
        .btn-action-container {
            flex-direction: column-reverse;
            align-items: stretch !important;
        }
        .btn-action-container .btn,
        .btn-bukti-link,
        .btn-buka-kunci-penilaian {
            min-height: 44px;
            width: 100%;
        }
        .table-bento-wrapper {
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .table-bento thead {
            display: none;
        }
        .table-bento,
        .table-bento tbody,
        .table-bento tfoot,
        .table-bento tr,
        .table-bento td {
            display: block;
            width: 100% !important;
        }
        .table-bento tbody tr,
        .table-bento tfoot tr {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .table-bento tbody td,
        .table-bento tfoot td {
            border: 0;
            padding: 8px 4px !important;
            text-align: left !important;
        }
        .table-bento tbody td.td-no {
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }
        .table-bento tbody td.td-no::before {
            content: attr(data-label) " ";
            font-weight: 700;
            color: #475569;
            margin-right: 4px;
        }
        .table-bento td[data-label]:not(.td-no)::before {
            content: attr(data-label);
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }
        /* Grid 3 kolom kompak untuk metrik Target, Realisasi, Selisih di kartu mobile RHK */
        .table-bento-rhk tbody tr {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .table-bento-rhk tbody td.td-no,
        .table-bento-rhk tbody td.td-rhk-indikator,
        .table-bento-rhk tbody td.td-rhk-nilai {
            grid-column: 1 / -1;
        }
        .table-bento-rhk tbody td.td-rhk-metric {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 8px !important;
            text-align: center !important;
        }
        .table-bento-rhk tbody td.td-rhk-metric::before {
            text-align: center;
        }
        /* Kalender Heatmap Mobile (Anti-Overlap di 360px-375px) */
        .calendar-heatmap-card {
            padding: 12px 8px;
            border-radius: 12px;
        }
        .heatmap-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }
        .heatmap-kpi-item {
            min-width: 0;
        }
        .calendar-heatmap-grid {
            gap: 4px;
        }
        .calendar-header-cell {
            font-size: 0.75rem;
            padding-bottom: 4px;
            letter-spacing: 0;
        }
        .heatmap-day-cell {
            min-height: 48px;
            padding: 4px;
            border-radius: 8px;
            align-items: center;
            text-align: center;
        }
        .heatmap-day-cell .day-header {
            justify-content: center;
            width: 100%;
        }
        .heatmap-day-cell .day-footer {
            justify-content: center;
            width: 100%;
            margin-top: 4px;
        }
        .heatmap-day-cell .badge-count {
            font-size: 0.75rem;
            padding: 4px;
            line-height: 1;
            white-space: nowrap;
            min-width: 24px;
            text-align: center;
        }
        .heatmap-empty-cell {
            min-height: 48px;
            border-radius: 8px;
        }
        .heatmap-swatch-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }
        .heatmap-hint-box {
            width: 100%;
            border-radius: 12px !important;
        }
        /* Modal Detail Log Mobile: tanpa nested scroll trap & target sentuh >= 44px */
        #modalDetailLogTanggal #modalLogTableContainer {
            max-height: none;
            overflow-y: visible;
        }
        #modalDetailLogTanggal .btn-nav-date,
        #modalDetailLogTanggal .btn-close {
            min-width: 44px;
            min-height: 44px;
            width: 44px;
            height: 44px;
        }
        #modalDetailLogTanggal .modal-footer {
            flex-wrap: wrap;
            gap: 8px;
        }
        #modalDetailLogTanggal .modal-footer .btn {
            min-height: 44px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .badge-predikat-pop,
        .tab-content > .tab-pane,
        .btn-tactile,
        .heatmap-day-cell {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$canEvaluateSelf = !empty($can_evaluate_self);

$formatDisplayDecimal = static function ($val): string {
    if ($val === null || $val === '') {
        return '0';
    }
    $num = round((float) str_replace(',', '.', trim((string) $val)), 4);
    if ($num == 0.0) {
        return '0';
    }
    $formatted = sprintf('%.4F', $num);
    $formatted = rtrim(rtrim($formatted, '0'), '.');
    return str_replace('.', ',', $formatted === '-0' ? '0' : $formatted);
};

$formatInputNumber = static function ($val): string {
    if ($val === null || trim((string) $val) === '') {
        return '';
    }
    $num = (float) str_replace(',', '.', trim((string) $val));
    $formatted = sprintf('%.2F', $num);
    return rtrim(rtrim($formatted, '0'), '.');
};

$formatTanggalPendek = static function (?string $dateStr) use ($bulan_indo): string {
    if (empty($dateStr) || !strtotime($dateStr)) {
        return '-';
    }
    $tgl = date('j', strtotime($dateStr));
    $bln = $bulan_indo[(int) date('n', strtotime($dateStr)) - 1] ?? '';
    return $tgl . ' ' . substr($bln, 0, 3);
};

$mapColorTokenToClass = static function (string $colorToken): string {
    if ($colorToken === 'warning text-dark') {
        return 'warning-emphasis';
    }
    if ($colorToken === 'info text-dark') {
        return 'info-emphasis';
    }
    if ($colorToken === 'danger') {
        return 'danger-emphasis';
    }
    if ($colorToken === 'success') {
        return 'success-emphasis';
    }
    if ($colorToken === 'primary') {
        return 'primary-emphasis';
    }
    return 'secondary';
};

$mapBorderColor = static function (string $colorToken): string {
    if ($colorToken === 'warning text-dark') {
        return 'warning';
    }
    if ($colorToken === 'info text-dark') {
        return 'info';
    }
    return $colorToken;
};

$mapPredikatBadgeClass = static function (string $badgeClass): string {
    $map = [
        'bg-danger'  => 'bg-danger text-white',
        'bg-warning text-dark' => 'bg-warning text-dark',
        'bg-info text-dark' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'bg-primary' => 'bg-primary text-white',
        'bg-success' => 'bg-success text-white',
        'bg-secondary' => 'bg-secondary text-white',
    ];
    return $map[$badgeClass] ?? $badgeClass;
};
?>

<div class="container-fluid px-2 px-md-3 pk-page-wrap">
    <!-- PAGE HEADER (Clean Typography, No Trailing Period Pill) -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Rekap &amp; Penilaian Kinerja</h1>
            <p class="ecc-text-muted-aa small mb-0">Evaluasi capaian target RHK, aktivitas harian, dan penerbitan nilai capaian kinerja.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2 pk-main-card">
        <div class="card-body p-3 p-md-4 pk-main-card-body">

            <!-- Filter Bar Toolbar -->
            <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle" id="filterForm">
                <?= csrf_field() ?>
                <input type="hidden" name="active_tab" id="active_tab_input" value="<?= empty($staf_id_terpilih) ? 'individu' : 'staf' ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-7 col-sm-4 col-md-3">
                        <label for="filterBulanSelect" class="form-label fw-bold text-dark small mb-1">Bulan</label>
                        <select name="bulan" id="filterBulanSelect" class="form-select form-select-sm border-primary fw-semibold ecc-focus-ring pk-filter-control" aria-label="Pilih Bulan Penilaian">
                            <?php foreach ($bulan_indo as $index => $nama): ?>
                                <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= esc($nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-5 col-sm-3 col-md-2">
                        <label for="tahunFilterSelect" class="form-label fw-bold text-dark small mb-1">Tahun</label>
                        <select name="tahun" id="tahunFilterSelect" class="form-select form-select-sm border-primary fw-semibold ecc-focus-ring pk-filter-control" aria-label="Pilih Tahun Penilaian">
                            <?php foreach (($daftar_tahun ?? [date('Y')]) as $thnItem): ?>
                                <option value="<?= esc($thnItem) ?>" <?= ((int) $tahun_terpilih === (int) $thnItem) ? 'selected' : '' ?>><?= esc($thnItem) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($is_super): ?>
                        <div class="col-12 col-sm-5 col-md-4 <?= empty($staf_id_terpilih) ? 'd-none' : '' ?>" id="filterUnitCol">
                            <label for="filterUnitSelect" class="form-label fw-bold text-dark small mb-1">Unit Kerja</label>
                            <select name="unit_kerja" id="filterUnitSelect" class="form-select form-select-sm border-primary fw-semibold ecc-focus-ring pk-filter-control" aria-label="Pilih Unit Kerja">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($daftar_unit as $u): ?>
                                    <option value="<?= esc($u) ?>" <?= ($u == $unit_kerja_terpilih) ? 'selected' : '' ?>><?= esc($u) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Unified Standar Predikat Bar (Desktop Penuh, Mobile Ringkas Dapat Dibuka) -->
            <div class="alert alert-light border shadow-sm mb-4 py-2 px-3 text-dark rounded-3 small d-none d-md-block">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="fw-bold ecc-text-muted-aa text-nowrap">Standar Predikat:</span>
                    <span class="text-nowrap"><span class="badge bg-danger text-white px-2 py-1">Sangat Kurang</span> &le; 25%</span>
                    <span class="text-nowrap"><span class="badge bg-warning text-dark px-2 py-1">Kurang</span> &gt; 25% - 75%</span>
                    <span class="text-nowrap"><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">Butuh Perbaikan</span> &gt; 75% - 90%</span>
                    <span class="text-nowrap"><span class="badge bg-primary text-white px-2 py-1">Baik</span> &gt; 90% - 100%</span>
                    <span class="text-nowrap"><span class="badge bg-success text-white px-2 py-1">Sangat Baik</span> &gt; 100% - 150%</span>
                </div>
            </div>
            <details class="pk-predikat-details mb-3 d-md-none">
                <summary>Standar Predikat (5 Kategori)</summary>
                <div class="d-flex flex-column gap-2 mt-3 small">
                    <div class="d-flex justify-content-between align-items-center"><span class="badge bg-success text-white px-2 py-1">Sangat Baik</span><span class="num-tabular fw-semibold">&gt; 100% – 150%</span></div>
                    <div class="d-flex justify-content-between align-items-center"><span class="badge bg-primary text-white px-2 py-1">Baik</span><span class="num-tabular fw-semibold">&gt; 90% – 100%</span></div>
                    <div class="d-flex justify-content-between align-items-center"><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">Butuh Perbaikan</span><span class="num-tabular fw-semibold">&gt; 75% – 90%</span></div>
                    <div class="d-flex justify-content-between align-items-center"><span class="badge bg-warning text-dark px-2 py-1">Kurang</span><span class="num-tabular fw-semibold">&gt; 25% – 75%</span></div>
                    <div class="d-flex justify-content-between align-items-center"><span class="badge bg-danger text-white px-2 py-1">Sangat Kurang</span><span class="num-tabular fw-semibold">&le; 25%</span></div>
                </div>
            </details>

            <!-- Navigation Tabs (Hanya tampil jika pengguna memiliki akses Penilaian Staf) -->
            <?php if ($is_atasan): ?>
            <ul class="nav segmented-control mb-4 bento-stagger bento-stagger-2" id="penilaianTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-tactile <?= empty($staf_id_terpilih) ? 'active' : '' ?>" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                        Target Saya
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-tactile <?= !empty($staf_id_terpilih) ? 'active' : '' ?>" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                        Penilaian Staf
                    </button>
                </li>
            </ul>
            <?php endif; ?>

            <div class="tab-content" id="penilaianTabsContent">

                <!-- ==================== TAB 1: TARGET INDIVIDU SAYA ==================== -->
                <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="individu" role="tabpanel" aria-labelledby="individu-tab">
                    <?php if (empty($rekap_data_sendiri)): ?>
                        <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                            <h2 class="h5 fw-bold text-dark mb-1">Belum Ada Target Kinerja</h2>
                            <p class="ecc-text-muted-aa small mb-0">Belum ada target RHK yang ditetapkan pada periode <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>.</p>
                        </div>
                    <?php else: ?>
                        <?php
                            $rekapIndividu = hitung_rekap_komponen_kinerja($rekap_data_sendiri, $tugas_tambahan_sendiri, !$canEvaluateSelf);
                            $jmlDinilai = $rekapIndividu['jml_dinilai'];
                            $totalNilai = $rekapIndividu['total_nilai'];
                            $scoreTambahanIndividu = $rekapIndividu['score_tambahan'];
                            $rataRataIndividu = $rekapIndividu['rata_rata'];
                            $predikatLabelIndividu = $rekapIndividu['predikat_label'];
                            $badgeColorRataIndividu = $mapPredikatBadgeClass($rekapIndividu['badge_class']);
                            $warnaScore = $rekapIndividu['warna_score'];
                            $borderMandiri = $mapBorderColor($warnaScore);
                            $textMandiriClass = $mapColorTokenToClass($warnaScore);

                            $scoreTambahanDirekturForm = $scoreTambahanIndividu;
                            $isMandiriTmbTerbit = false;
                            $isMandiriTmbDraft = false;
                            $hasMandiriDraft = false;
                            $hasMandiriTerbit = false;

                            if ($canEvaluateSelf) {
                                foreach ($rekap_data_sendiri as $rdSelf) {
                                    if ($rdSelf['nilai_capaian'] !== null && trim((string) $rdSelf['nilai_capaian']) !== '') {
                                        if (($rdSelf['status_penilaian'] ?? '') === 'terbit') {
                                            $hasMandiriTerbit = true;
                                        } else {
                                            $hasMandiriDraft = true;
                                        }
                                    }
                                }
                                if (!empty($tugas_tambahan_sendiri)) {
                                    foreach ($tugas_tambahan_sendiri as $tmbSelf) {
                                        if ($tmbSelf['nilai_capaian'] !== null && trim((string) $tmbSelf['nilai_capaian']) !== '') {
                                            if ($scoreTambahanDirekturForm === null) {
                                                $scoreTambahanDirekturForm = (float) $tmbSelf['nilai_capaian'];
                                            }
                                            if (($tmbSelf['status_penilaian'] ?? '') === 'terbit') {
                                                $hasMandiriTerbit = true;
                                                $isMandiriTmbTerbit = true;
                                            } else {
                                                $hasMandiriDraft = true;
                                                $isMandiriTmbDraft = true;
                                            }
                                        }
                                    }
                                }
                            }
                            $statusMandiriGlobal = $hasMandiriDraft ? 'draft' : ($hasMandiriTerbit ? 'terbit' : 'belum_dinilai');
                            $totalKomponenIndividu = count($rekap_data_sendiri) + (!empty($tugas_tambahan_sendiri) ? 1 : 0);
                        ?>

                        <?php if ($canEvaluateSelf): ?>
                            <form method="POST" action="<?= site_url('penilaian-kinerja/store') ?>" id="formPenilaianMandiri">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" id="mandiriActionInput" value="submit">
                                <input type="hidden" name="is_self_eval" value="1">
                                <input type="hidden" name="staf_id" value="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>">
                                <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                                <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                        <?php endif; ?>

                        <!-- BAGIAN A: TARGET KINERJA RHK -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-2">
                            <h2 class="h6 fw-bold text-dark mb-0">A. Target Kinerja RHK</h2>
                        </div>

                        <div class="table-bento-wrapper mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
                            <table class="table table-bordered table-hover mb-0 table-bento table-bento-rhk" id="tablePenilaianMandiri">
                                <thead>
                                    <tr>
                                        <th class="col-no-bento text-center">No</th>
                                        <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                        <th class="col-metric text-center">Target Bulanan</th>
                                        <th class="col-metric text-center">Total Realisasi</th>
                                        <th class="col-metric text-center">Selisih (Gap)</th>
                                        <th class="col-nilai text-center">Nilai Capaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                        <?php
                                            $target = round((float) $row['target_bulanan'], 4);
                                            $realisasi = round((float) $row['total_realisasi'], 4);
                                            $selisih = round($realisasi - $target, 4);
                                            $isTerbit = (isset($row['status_penilaian']) && $row['status_penilaian'] === 'terbit' && $row['nilai_capaian'] !== null);
                                            $hasSelfRowVal = ($row['nilai_capaian'] !== null && trim((string) $row['nilai_capaian']) !== '');
                                            $selfRowPred = $hasSelfRowVal ? hitung_predikat_kinerja($row['nilai_capaian'], true) : null;
                                        ?>
                                        <tr>
                                            <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris"><?= $index + 1 ?></td>
                                            <td class="td-rhk-indikator fw-semibold text-dark" data-label="Indikator Kinerja / RHK"><?= esc($row['indikator_kinerja']) ?></td>
                                            <td class="td-rhk-metric text-center fw-bold text-dark num-tabular" data-label="Target"><?= $formatDisplayDecimal($target) ?> <?= esc($row['satuan']) ?></td>
                                            <td class="td-rhk-metric text-center fw-bold text-primary-emphasis num-tabular" data-label="Realisasi"><?= $formatDisplayDecimal($realisasi) ?> <?= esc($row['satuan']) ?></td>
                                            <td class="td-rhk-metric text-center fw-bold num-tabular" data-label="Selisih">
                                                <?php if ($selisih > 0): ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">+<?= $formatDisplayDecimal($selisih) ?> <?= esc($row['satuan']) ?></span>
                                                <?php elseif ($selisih < 0): ?>
                                                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-1"><?= $formatDisplayDecimal($selisih) ?> <?= esc($row['satuan']) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="td-rhk-nilai text-center fw-bold num-tabular" data-label="Nilai Capaian">
                                                <?php if ($canEvaluateSelf): ?>
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                    <div class="input-group input-group-sm mb-2">
                                                        <input type="number" step="0.01" min="0" max="150" name="nilai_capaian[<?= esc($row['id']) ?>]" class="form-control text-center text-primary-emphasis fw-bold num-tabular input-nilai-capaian" value="<?= esc(old('nilai_capaian.' . $row['id'], old('nilai_capaian.' . $index, $formatInputNumber($row['nilai_capaian'])))) ?>" data-status-penilaian="<?= $isTerbit ? 'terbit' : ($hasSelfRowVal ? 'draft' : 'none') ?>" data-initial-value="<?= esc($formatInputNumber($row['nilai_capaian'])) ?>" placeholder="0 - 150" aria-label="Nilai Capaian Mandiri RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                        <span class="input-group-text bg-light fw-bold">%</span>
                                                    </div>
                                                    <div class="predikat-badge-container d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                                        <?php if ($hasSelfRowVal): ?>
                                                            <?php if ($isTerbit): ?>
                                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 badge-status-row">Terbit</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 badge-status-row">Draf</span>
                                                            <?php endif; ?>
                                                            <span class="badge <?= esc($mapPredikatBadgeClass($selfRowPred['badge_class'])) ?> rounded-pill px-2 py-1 badge-predikat-val"><?= esc($selfRowPred['label']) ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1">Belum dinilai</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="invalid-feedback small">Nilai harus 0 - 150%.</div>
                                                <?php else: ?>
                                                    <?php if ($isTerbit): ?>
                                                        <span class="fw-bold text-primary-emphasis num-tabular"><?= $formatDisplayDecimal($row['nilai_capaian']) ?>%</span>
                                                        <?php if (!empty($row['nama_penilai'])): ?>
                                                            <div class="small ecc-text-muted-aa mt-1 fw-normal" title="Dinilai oleh <?= esc($row['nama_penilai']) ?>">
                                                                <?= esc($row['nama_penilai']) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="ecc-text-muted-aa fw-normal" title="Belum diterbitkan atasan">-</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- BAGIAN B: TUGAS TAMBAHAN -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-4">
                            <h2 class="h6 fw-bold text-dark mb-0">B. Tugas Tambahan</h2>
                        </div>

                        <div class="table-bento-wrapper mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
                            <table class="table table-bordered table-hover mb-0 table-bento">
                                <thead>
                                    <tr>
                                        <th class="col-no-bento text-center">No</th>
                                        <th class="col-target text-start">Deskripsi Tugas Tambahan</th>
                                        <th class="col-tanggal-bento text-center">Tanggal</th>
                                        <th class="col-metric text-center">Capaian / Output</th>
                                        <th class="col-nilai text-center">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tugas_tambahan_sendiri)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center ecc-text-muted-aa py-4">Tidak ada tugas tambahan pada bulan ini.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tugas_tambahan_sendiri as $idx => $tmb): ?>
                                            <tr>
                                                <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                                                    <?php if ($canEvaluateSelf): ?>
                                                        <input type="hidden" name="log_tambahan_id[]" value="<?= esc($tmb['id']) ?>">
                                                    <?php endif; ?>
                                                    <?= $idx + 1 ?>
                                                </td>
                                                <td data-label="Deskripsi Tugas Tambahan"><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                                <td class="text-center ecc-text-muted-aa num-tabular" data-label="Tanggal">
                                                    <?= esc($formatTanggalPendek($tmb['tanggal_kegiatan'] ?? '')) ?>
                                                </td>
                                                <td class="text-center fw-semibold text-dark num-tabular" data-label="Capaian / Output">
                                                    <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? $formatDisplayDecimal($tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                                </td>
                                                <td class="text-center" data-label="Bukti">
                                                    <?php
                                                        $linkBuktiSendiri = trim((string) ($tmb['link_bukti'] ?? ''));
                                                        $isSafeBuktiSendiri = ($linkBuktiSendiri !== '' && preg_match('/^https?:\/\//i', $linkBuktiSendiri));
                                                    ?>
                                                    <?php if ($isSafeBuktiSendiri): ?>
                                                        <a href="<?= esc($linkBuktiSendiri) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary-emphasis border btn-bukti-link btn-tactile" title="Buka tautan bukti pekerjaan">
                                                            <i class="bi bi-box-arrow-up-right"></i> Bukti
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="ecc-text-muted-aa">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($tugas_tambahan_sendiri)): ?>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="4" class="text-end text-dark fw-bold d-none d-md-table-cell">Nilai Tugas Tambahan (0 - 150%):</td>
                                            <td class="text-center fw-bold num-tabular" data-label="Nilai Tugas Tambahan (0 - 150%)">
                                                <?php if ($canEvaluateSelf): ?>
                                                    <div class="input-group input-group-sm mb-2">
                                                        <input type="number" step="0.01" min="0" max="150" name="nilai_tugas_tambahan_gabungan" id="inputNilaiTambahanMandiri" class="form-control text-center text-success-emphasis fw-bold num-tabular input-nilai-capaian" value="<?= esc(old('nilai_tugas_tambahan_gabungan', $formatInputNumber($scoreTambahanDirekturForm))) ?>" data-status-penilaian="<?= $isMandiriTmbTerbit ? 'terbit' : ($isMandiriTmbDraft ? 'draft' : 'none') ?>" data-initial-value="<?= esc($formatInputNumber($scoreTambahanDirekturForm)) ?>" placeholder="0 - 150" aria-label="Nilai Tugas Tambahan Mandiri">
                                                        <span class="input-group-text bg-light fw-bold">%</span>
                                                    </div>
                                                    <div id="hintTambahanMandiriContainer" class="d-flex align-items-center justify-content-center gap-1">
                                                        <?php if ($scoreTambahanDirekturForm === null): ?>
                                                            <span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1">Belum dinilai</span>
                                                        <?php elseif ($isMandiriTmbTerbit): ?>
                                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1">Terbit</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Draf</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="invalid-feedback small">Nilai harus 0 - 150%.</div>
                                                <?php else: ?>
                                                    <span class="text-success-emphasis"><?= $scoreTambahanIndividu !== null ? $formatDisplayDecimal($scoreTambahanIndividu) . '%' : '<span class="ecc-text-muted-aa fw-normal">-</span>' ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>

                        <!-- RINGKASAN SKOR AKHIR INDIVIDU -->
                        <div class="card bg-white border border-2 border-<?= esc($borderMandiri) ?> rounded-4 p-3 p-md-4 shadow-sm mb-4 score-card-transition bento-stagger bento-stagger-3" id="cardKinerjaMandiriSummary" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Bulanan">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                        <h3 class="h6 fw-bold text-dark mb-0">Nilai Akhir Kinerja Bulanan</h3>
                                        <?php if ($canEvaluateSelf): ?>
                                            <?php if ($statusMandiriGlobal === 'draft'): ?>
                                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusMandiri">Draf</span>
                                            <?php elseif ($statusMandiriGlobal === 'terbit'): ?>
                                                <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusMandiri">Terbit</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary text-white px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusMandiri">Belum Dinilai</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge bg-light text-dark border px-3 py-1 pk-summary-pill">
                                            <span id="countRhkMandiriTerisi"><?= $jmlDinilai - ($scoreTambahanIndividu !== null ? 1 : 0) ?></span> / <?= count($rekap_data_sendiri) ?> Target RHK
                                        </span>
                                        <?php if (!empty($tugas_tambahan_sendiri)): ?>
                                            <span class="badge bg-light text-dark border px-3 py-1 pk-summary-pill">
                                                Tugas Tambahan: <span id="textTambahanMandiriStatus"><?= $scoreTambahanIndividu !== null ? $formatDisplayDecimal($scoreTambahanIndividu) . '%' : 'Belum Dinilai' ?></span>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($jmlDinilai < $totalKomponenIndividu): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 pk-summary-pill" id="badgeHintUnfilledMandiri">
                                                <?= $canEvaluateSelf ? 'Sebagian komponen belum diisi atau diterbitkan' : 'Sebagian nilai belum diterbitkan atasan' ?>
                                            </span>
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 pk-summary-pill d-none" id="badgeAllFilledMandiri">
                                                Seluruh komponen telah dinilai
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 pk-summary-pill d-none" id="badgeHintUnfilledMandiri">
                                                <?= $canEvaluateSelf ? 'Sebagian komponen belum diisi atau diterbitkan' : 'Sebagian nilai belum diterbitkan atasan' ?>
                                            </span>
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 pk-summary-pill" id="badgeAllFilledMandiri">
                                                Seluruh komponen telah dinilai
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                    <span id="totalPredikatMandiriBadge" class="badge <?= esc($badgeColorRataIndividu) ?> fs-6 px-3 py-2 rounded-pill"><?= esc($predikatLabelIndividu) ?></span>
                                    <span id="totalKinerjaMandiriText" class="fw-bold text-<?= esc($textMandiriClass) ?> mb-0 num-tabular score-value-hero"><?= $formatDisplayDecimal(round($rataRataIndividu, 2)) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if ($canEvaluateSelf): ?>
                            <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                                <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btnResetNilaiMandiri">Reset Nilai</button>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm">Simpan Draf</button>
                                <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 py-2 fw-bold shadow-sm">Simpan &amp; Terbitkan</button>
                            </div>
                            </form>
                        <?php endif; ?>

                        <?php
                            $hasAnyDraftSendiri = false;
                            if (!empty($log_harian_sendiri)) {
                                foreach ($log_harian_sendiri as $item) {
                                    if (isset($item['status']) && in_array($item['status'], ['draft', 'draf'], true)) {
                                        $hasAnyDraftSendiri = true;
                                        break;
                                    }
                                }
                            }
                        ?>

                        <?php if ($hasAnyDraftSendiri): ?>
                            <div class="alert alert-warning py-3 px-3 border-warning shadow-sm mb-4 d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-2 rounded-4 bento-stagger bento-stagger-3">
                                <div>
                                    <strong>Perhatian:</strong> Terdapat laporan harian berstatus draf yang belum dikirim dan belum masuk perhitungan realisasi.
                                </div>
                                <a href="<?= site_url('log-kegiatan') ?>" class="btn btn-sm btn-warning btn-tactile text-dark fw-bold flex-shrink-0 rounded-pill px-3">
                                    Buka &amp; Kirim Laporan
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($is_heatmap_hidden)): ?>
                            <!-- TABEL LAPORAN KEGIATAN HARIAN SAYA -->
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
                                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                                            Laporan Kegiatan Harian Saya
                                        </span>
                                        <span class="ecc-text-muted-aa small fw-semibold">
                                            Periode: <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
                                        </span>
                                    </div>
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1 num-tabular">
                                        Total: <strong><?= count($log_harian_sendiri) ?></strong> Kegiatan
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($log_harian_sendiri)): ?>
                                        <div class="text-center py-5 px-3">
                                            <h3 class="h6 fw-bold text-dark mb-1">Belum Ada Catatan Kegiatan</h3>
                                            <p class="ecc-text-muted-aa small mb-0">Tidak ada laporan kegiatan harian pada periode <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-bento-wrapper">
                                            <table class="table table-bordered table-hover mb-0 table-bento">
                                                <thead>
                                                    <tr>
                                                        <th class="col-no-bento text-center">No</th>
                                                        <th class="col-tanggal-bento text-center">Tanggal</th>
                                                        <th class="col-jenis-bento text-center">Jenis</th>
                                                        <th class="col-target">Indikator RHK / Sasaran</th>
                                                        <th class="col-target">Deskripsi Aktivitas</th>
                                                        <th class="col-tanggal-bento text-center">Realisasi</th>
                                                        <th class="col-bukti-bento text-center">Bukti</th>
                                                        <th class="col-jenis-bento text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($log_harian_sendiri as $idx => $logItem): ?>
                                                        <tr>
                                                            <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris"><?= $idx + 1 ?></td>
                                                            <td class="text-center num-tabular" data-label="Tanggal">
                                                                <?php
                                                                    $tglWaktu = strtotime($logItem['tanggal_kegiatan'] ?? '');
                                                                    echo $tglWaktu ? date('d/m/Y', $tglWaktu) : '-';
                                                                ?>
                                                            </td>
                                                            <td class="text-center" data-label="Jenis">
                                                                <?php if (!empty($logItem['is_tambahan'])): ?>
                                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1">Tugas Tambahan</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1">Tugas Pokok</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td data-label="Indikator RHK"><?= esc($logItem['indikator_kinerja'] ?? '-') ?></td>
                                                            <td data-label="Deskripsi Aktivitas"><?= nl2br(esc($logItem['deskripsi_kegiatan'] ?? '-')) ?></td>
                                                            <td class="text-center num-tabular fw-bold text-primary-emphasis" data-label="Realisasi">
                                                                <?= (isset($logItem['jumlah_capaian']) && $logItem['jumlah_capaian'] !== null) ? $formatDisplayDecimal($logItem['jumlah_capaian']) : '-' ?>
                                                                <span class="ecc-text-muted-aa fw-normal"><?= esc($logItem['satuan'] ?? '') ?></span>
                                                            </td>
                                                            <td class="text-center" data-label="Bukti">
                                                                <?php
                                                                    $rawLink = trim((string) ($logItem['link_bukti'] ?? ''));
                                                                    $isSafeUrl = (bool) preg_match('/^https?:\/\//i', $rawLink);
                                                                ?>
                                                                <?php if (!empty($rawLink) && $isSafeUrl): ?>
                                                                    <a href="<?= esc($rawLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary-emphasis border btn-bukti-link btn-tactile" title="Lihat bukti pekerjaan">
                                                                        <i class="bi bi-box-arrow-up-right"></i> Bukti
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="ecc-text-muted-aa">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center" data-label="Status">
                                                                <?php $st = $logItem['status'] ?? 'terkirim'; ?>
                                                                <?php if ($st === 'disetujui'): ?>
                                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1">Disetujui</span>
                                                                <?php elseif ($st === 'terkirim'): ?>
                                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Terkirim</span>
                                                                <?php elseif ($st === 'draf' || $st === 'draft'): ?>
                                                                    <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">Draf</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?= esc(ucfirst($st)) ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- HEATMAP KALENDER AKTIVITAS BULANAN PEGAWAI (INDIVIDU) -->
                            <?= view('user/penilaian_kinerja/_calendar_heatmap', [
                                'heatmap'              => $heatmap_sendiri,
                                'isStaf'               => false,
                                'stafId'               => '',
                                'bulanNama'            => $bulan_indo[$bulan_terpilih - 1] ?? '',
                                'tahunTerpilih'        => $tahun_terpilih,
                                'formatDisplayDecimal' => $formatDisplayDecimal,
                            ]) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div> <!-- End Tab Individu -->

                <?php if ($is_atasan): ?>
                    <!-- ==================== TAB 2: PENILAIAN STAF (ATASAN / DIREKTUR / ADMIN) ==================== -->
                    <div class="tab-pane fade <?= !empty($staf_id_terpilih) ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">

                        <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-4 border border-light-subtle" id="formPilihStaf">
                            <?= csrf_field() ?>
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="selectStaf" class="form-label fw-bold text-success-emphasis mb-1 small">Pilih Staf yang Akan Dinilai</label>
                                    <select name="staf_id" id="selectStaf" class="form-select border-success form-select-sm ecc-focus-ring" aria-label="Pilih Staf yang Akan Dinilai">
                                        <option value="">-- Pilih Staf --</option>
                                        <?php foreach ($daftar_staf as $b): ?>
                                            <option value="<?= esc($b['id']) ?>" <?= ($b['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
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
                                    <h2 class="h5 fw-bold text-dark mb-1">Target Kinerja Staf Belum Ditetapkan</h2>
                                    <p class="ecc-text-muted-aa small mb-0">Staf belum memiliki Target Kinerja (RHK) pada periode <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>.</p>
                                </div>
                            <?php else: ?>
                                <?php
                                    $rekapStaf = hitung_rekap_komponen_kinerja($rekap_data_staf, $tugas_tambahan_staf, true);
                                    $jmlDinilaiBwh = $rekapStaf['jml_dinilai'];
                                    $scoreTambahanStaf = $rekapStaf['score_tambahan'];
                                    $rataRataBwh = $rekapStaf['rata_rata'];
                                    $predikatLabelBwh = $rekapStaf['predikat_label'];
                                    $badgeColorRataBwh = $mapPredikatBadgeClass($rekapStaf['badge_class']);
                                    $warnaScoreBwh = $rekapStaf['warna_score'];
                                    $borderStaf = $mapBorderColor($warnaScoreBwh);
                                    $textStafClass = $mapColorTokenToClass($warnaScoreBwh);

                                    $scoreTambahanStafInput = $scoreTambahanStaf;
                                    if ($scoreTambahanStafInput === null && !empty($tugas_tambahan_staf)) {
                                        foreach ($tugas_tambahan_staf as $tmb) {
                                            if ($tmb['nilai_capaian'] !== null && trim((string) $tmb['nilai_capaian']) !== '') {
                                                $scoreTambahanStafInput = (float) $tmb['nilai_capaian'];
                                                break;
                                            }
                                        }
                                    }

                                    $hasAnyDraft = false;
                                    $hasAnyTerbit = false;
                                    $hasAnyNilai = false;

                                    foreach ($rekap_data_staf as $rdRow) {
                                        if ($rdRow['nilai_capaian'] !== null && trim((string) $rdRow['nilai_capaian']) !== '') {
                                            $hasAnyNilai = true;
                                            if (($rdRow['status_penilaian'] ?? '') === 'terbit') {
                                                $hasAnyTerbit = true;
                                            } else {
                                                $hasAnyDraft = true;
                                            }
                                        }
                                    }

                                    $isTmbTerbit = false;
                                    $isTmbDraft = false;
                                    if (!empty($tugas_tambahan_staf)) {
                                        foreach ($tugas_tambahan_staf as $tmbRow) {
                                            if ($tmbRow['nilai_capaian'] !== null && trim((string) $tmbRow['nilai_capaian']) !== '') {
                                                $hasAnyNilai = true;
                                                if (($tmbRow['status_penilaian'] ?? '') === 'terbit') {
                                                    $hasAnyTerbit = true;
                                                    $isTmbTerbit = true;
                                                } else {
                                                    $hasAnyDraft = true;
                                                    $isTmbDraft = true;
                                                }
                                            }
                                        }
                                    }

                                    $statusPenilaianGlobal = 'belum_dinilai';
                                    if ($hasAnyDraft) {
                                        $statusPenilaianGlobal = 'draft';
                                    } elseif ($hasAnyTerbit) {
                                        $statusPenilaianGlobal = 'terbit';
                                    }
                                ?>

                                <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                                    <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3 bento-stagger bento-stagger-1">
                                        <div>
                                            <h3 class="h6 fw-bold text-dark mb-1">Target Kinerja Bulanan Staf Belum Disetujui</h3>
                                            <p class="ecc-text-muted-aa small mb-0">
                                                <?= !empty($target_staf_unapproved_count) ? (int) $target_staf_unapproved_count . ' target ' : 'Target ' ?>periode <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?> belum disetujui. Setujui target terlebih dahulu sebelum menilai.
                                            </p>
                                        </div>
                                        <a href="<?= site_url('laporan-harian?staf_id=' . esc($staf_id_terpilih) . '&bulan=' . esc($bulan_terpilih) . '&tahun=' . esc($tahun_terpilih) . '&source_tab=staf') ?>" class="btn btn-warning btn-sm btn-tactile text-dark fw-bold rounded-pill px-3 py-2">
                                            Setujui Target Kinerja
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($statusPenilaianGlobal === 'draft'): ?>
                                    <div class="alert alert-warning border border-warning-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-2 bento-stagger bento-stagger-1">
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">Draf</span>
                                        <span class="small text-dark fw-medium">Nilai pada periode ini masih berupa draf dan belum dikirim ke staf.</span>
                                    </div>
                                <?php elseif ($statusPenilaianGlobal === 'terbit'): ?>
                                    <div class="alert alert-success border border-success-subtle shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-2 bento-stagger bento-stagger-1">
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold">Terbit</span>
                                        <span class="small text-dark fw-medium">Nilai pada periode ini telah dikirim ke staf.</span>
                                    </div>
                                <?php endif; ?>

                                <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" id="penilaianActionInput" value="submit">
                                    <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                                    <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                                    <!-- BAGIAN A STAF -->
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-2">
                                        <h2 class="h6 fw-bold text-dark mb-0">A. Penilaian Target RHK</h2>
                                        <?php if (hasRole('admin') && !empty($staf_id_terpilih)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold shadow-sm btn-batal-approve-target-penilaian btn-tactile"
                                                data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                                data-bulan="<?= esc($bulan_terpilih) ?>"
                                                data-tahun="<?= esc($tahun_terpilih) ?>"
                                                title="Batalkan persetujuan target bulanan staf agar staf dapat merevisi target">
                                                <i class="bi bi-x-circle-fill"></i> Batalkan Target (Admin)
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <div class="table-bento-wrapper mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
                                        <table class="table table-bordered table-hover mb-0 table-bento table-bento-rhk" id="tablePenilaianStaf">
                                            <thead>
                                                <tr>
                                                    <th class="col-no-bento text-center">No</th>
                                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                                    <th class="col-metric text-center">Target Bulanan</th>
                                                    <th class="col-metric text-center">Total Realisasi</th>
                                                    <th class="col-metric text-center">Selisih (Gap)</th>
                                                    <th class="col-nilai text-center">Input Nilai Capaian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rekap_data_staf as $index => $row): ?>
                                                    <?php
                                                        $target = round((float) $row['target_bulanan'], 4);
                                                        $realisasi = round((float) $row['total_realisasi'], 4);
                                                        $selisih = round($realisasi - $target, 4);

                                                        $nilai_capaian = $row['nilai_capaian'];
                                                        $predikatLabel = 'Belum dinilai';
                                                        $predikatBadge = 'bg-light ecc-text-muted-aa border';
                                                        $rowStatusPenilaian = $row['status_penilaian'] ?? null;
                                                        $hasRowNilai = ($nilai_capaian !== null && trim((string) $nilai_capaian) !== '');
                                                        $isRowTerbit = ($hasRowNilai && $rowStatusPenilaian === 'terbit');
                                                        $isRowDraft = ($hasRowNilai && !$isRowTerbit);

                                                        if ($hasRowNilai) {
                                                            $predInfoRow = hitung_predikat_kinerja($nilai_capaian, true);
                                                            $predikatLabel = $predInfoRow['label'];
                                                            $predikatBadge = $mapPredikatBadgeClass($predInfoRow['badge_class']);
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris"><?= $index + 1 ?></td>
                                                        <td class="td-rhk-indikator fw-semibold text-dark" data-label="Indikator Kinerja / RHK"><?= esc($row['indikator_kinerja']) ?></td>
                                                        <td class="td-rhk-metric text-center fw-bold text-dark num-tabular" data-label="Target"><?= $formatDisplayDecimal($target) ?> <?= esc($row['satuan']) ?></td>
                                                        <td class="td-rhk-metric text-center fw-bold text-primary-emphasis num-tabular" data-label="Realisasi"><?= $formatDisplayDecimal($realisasi) ?> <?= esc($row['satuan']) ?></td>
                                                        <td class="td-rhk-metric text-center fw-bold num-tabular" data-label="Selisih">
                                                            <?php if ($selisih > 0): ?>
                                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">+<?= $formatDisplayDecimal($selisih) ?> <?= esc($row['satuan']) ?></span>
                                                            <?php elseif ($selisih < 0): ?>
                                                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-1"><?= $formatDisplayDecimal($selisih) ?> <?= esc($row['satuan']) ?></span>
                                                            <?php else: ?>
                                                                <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="td-rhk-nilai text-center col-nilai" data-label="Input Nilai Capaian">
                                                            <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                            <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                                                                <div class="input-group input-group-sm mb-2">
                                                                    <input type="number" class="form-control text-center fw-bold ecc-text-muted-aa num-tabular bg-light" value="" disabled placeholder="Terkunci" aria-label="Target belum disetujui">
                                                                    <span class="input-group-text bg-light ecc-text-muted-aa fw-bold">%</span>
                                                                </div>
                                                                <div class="predikat-badge-container">
                                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Target Belum Disetujui</span>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="input-group input-group-sm mb-2">
                                                                    <input type="number" name="nilai_capaian[<?= esc($row['id']) ?>]" class="form-control text-center input-nilai-capaian fw-bold text-primary-emphasis num-tabular" value="<?= esc(old('nilai_capaian.' . $row['id'], old('nilai_capaian.' . $index, $formatInputNumber($nilai_capaian)))) ?>" step="0.01" min="0" max="150" placeholder="0 - 150" data-status-penilaian="<?= $isRowTerbit ? 'terbit' : ($isRowDraft ? 'draft' : 'none') ?>" data-initial-value="<?= esc($formatInputNumber($nilai_capaian)) ?>" aria-label="Input Nilai Capaian RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                                    <span class="input-group-text bg-primary-subtle text-primary-emphasis fw-bold">%</span>
                                                                </div>
                                                                <div class="predikat-badge-container d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                                                    <?php if ($isRowDraft): ?>
                                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 badge-status-row">Draf</span>
                                                                    <?php elseif ($isRowTerbit): ?>
                                                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 badge-status-row">Terbit</span>
                                                                    <?php endif; ?>
                                                                    <span class="badge <?= esc($predikatBadge) ?> rounded-pill px-2 py-1 badge-predikat-val"><?= esc($predikatLabel) ?></span>
                                                                </div>
                                                                <div class="invalid-feedback small">Nilai harus 0 - 150%.</div>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- BAGIAN B STAF -->
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3 mt-4">
                                        <h2 class="h6 fw-bold text-dark mb-0">B. Penilaian Tugas Tambahan</h2>
                                    </div>

                                    <div class="table-bento-wrapper mb-4 bg-white border rounded-4 shadow-sm overflow-hidden">
                                        <table class="table table-bordered table-hover mb-0 table-bento">
                                            <thead>
                                                <tr>
                                                    <th class="col-no-bento text-center">No</th>
                                                    <th class="col-target text-start">Deskripsi Tugas Tambahan</th>
                                                    <th class="col-tanggal-bento text-center">Tanggal</th>
                                                    <th class="col-metric text-center">Capaian / Output</th>
                                                    <th class="col-nilai text-center">Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($tugas_tambahan_staf)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center ecc-text-muted-aa py-4">Staf tidak memiliki tugas tambahan pada bulan ini.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($tugas_tambahan_staf as $idx => $tmb): ?>
                                                        <tr>
                                                            <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                                                                <input type="hidden" name="log_tambahan_id[]" value="<?= esc($tmb['id']) ?>">
                                                                <?= $idx + 1 ?>
                                                            </td>
                                                            <td data-label="Deskripsi Tugas Tambahan"><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                                            <td class="text-center ecc-text-muted-aa num-tabular" data-label="Tanggal">
                                                                <?= esc($formatTanggalPendek($tmb['tanggal_kegiatan'] ?? '')) ?>
                                                            </td>
                                                            <td class="text-center fw-semibold text-dark num-tabular" data-label="Capaian / Output">
                                                                <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? $formatDisplayDecimal($tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                                            </td>
                                                            <td class="text-center" data-label="Bukti">
                                                                <?php
                                                                    $linkBuktiStaf = trim((string) ($tmb['link_bukti'] ?? ''));
                                                                    $isSafeBuktiStaf = ($linkBuktiStaf !== '' && preg_match('/^https?:\/\//i', $linkBuktiStaf));
                                                                ?>
                                                                <?php if ($isSafeBuktiStaf): ?>
                                                                    <a href="<?= esc($linkBuktiStaf) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary-emphasis border btn-bukti-link btn-tactile" title="Lihat bukti pekerjaan">
                                                                        <i class="bi bi-box-arrow-up-right"></i> Bukti
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="ecc-text-muted-aa">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                            <?php if (!empty($tugas_tambahan_staf)): ?>
                                                <tfoot class="table-light fw-bold">
                                                    <tr>
                                                        <td colspan="4" class="text-end text-dark fw-bold d-none d-md-table-cell">Nilai Tugas Tambahan (0 - 150%):</td>
                                                        <td class="text-center col-nilai" data-label="Nilai Tugas Tambahan (0 - 150%)">
                                                            <div class="input-group input-group-sm mb-2">
                                                                <input type="number" step="0.01" max="150" min="0" name="nilai_tugas_tambahan_gabungan" id="inputNilaiTambahanGabungan" class="form-control text-center fw-bold <?= (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui) ? 'ecc-text-muted-aa bg-light' : 'text-success-emphasis' ?> num-tabular input-nilai-capaian" value="<?= esc(old('nilai_tugas_tambahan_gabungan', $formatInputNumber($scoreTambahanStafInput))) ?>" placeholder="<?= (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui) ? 'Terkunci' : '0 - 150' ?>" data-status-penilaian="<?= $isTmbTerbit ? 'terbit' : ($isTmbDraft ? 'draft' : 'none') ?>" data-initial-value="<?= esc($formatInputNumber($scoreTambahanStafInput)) ?>" aria-label="Input Nilai Akumulasi Tugas Tambahan Staf" <?= (!$is_penilai || (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui)) ? 'disabled' : '' ?>>
                                                                <span class="input-group-text bg-success-subtle text-success-emphasis fw-bold">%</span>
                                                            </div>
                                                            <div id="hintTambahanContainer" class="d-flex align-items-center justify-content-center gap-1">
                                                                <?php if ($scoreTambahanStafInput === null): ?>
                                                                    <span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1">Belum dinilai</span>
                                                                <?php elseif ($isTmbTerbit): ?>
                                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1">Terbit</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Draf</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="invalid-feedback small">Nilai harus 0 - 150%.</div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            <?php endif; ?>
                                        </table>
                                    </div>

                                    <!-- RINGKASAN EXECUTIVE SKOR AKHIR STAF -->
                                    <div class="card bg-white border border-2 border-<?= esc($borderStaf) ?> rounded-4 p-3 p-md-4 shadow-sm mb-4 score-card-transition bento-stagger bento-stagger-3" id="cardKinerjaStafSummary" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Staf">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                                    <h3 class="h6 fw-bold text-dark mb-0">Nilai Akhir Kinerja Staf</h3>
                                                    <?php if ($statusPenilaianGlobal === 'draft'): ?>
                                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusPenilaian">Draf</span>
                                                    <?php elseif ($statusPenilaianGlobal === 'terbit'): ?>
                                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusPenilaian">Terbit</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary text-white px-3 py-1 rounded-pill fw-bold" id="badgeSummaryStatusPenilaian">Belum Dinilai</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    <span class="badge bg-light text-dark border px-3 py-1 pk-summary-pill">
                                                        <span id="countRhkTerisi"><?= $jmlDinilaiBwh - ($scoreTambahanStaf !== null ? 1 : 0) ?></span> / <?= count($rekap_data_staf) ?> RHK Terisi
                                                    </span>
                                                    <?php if (!empty($tugas_tambahan_staf)): ?>
                                                        <span class="badge bg-light text-dark border px-3 py-1 pk-summary-pill" id="badgeTambahanStatus">
                                                            Tugas Tambahan: <span id="textTambahanStatus"><?= $scoreTambahanStafInput !== null ? $formatDisplayDecimal($scoreTambahanStafInput) . '%' . ($isTmbTerbit ? ' (Terbit)' : ' (Draf)') : 'Belum Dinilai' ?></span>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 pk-summary-pill d-none" id="badgeHintUnfilled">
                                                        <span id="textCountUnfilled">0</span> kolom belum diisi
                                                    </span>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 pk-summary-pill d-none" id="badgeAllFilled">
                                                        Semua nilai terisi
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                                <span id="totalPredikatStafBadge" class="badge <?= esc($badgeColorRataBwh) ?> fs-6 px-3 py-2 rounded-pill"><?= esc($predikatLabelBwh) ?></span>
                                                <span class="fw-bold text-<?= esc($textStafClass) ?> mb-0 num-tabular score-value-hero" id="totalKinerjaStafText"><?= $formatDisplayDecimal(round($rataRataBwh, 2)) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ACTION TOOLBAR -->
                                    <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                                        <?php if (isset($is_target_staf_disetujui) && !$is_target_staf_disetujui): ?>
                                            <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-semibold opacity-75 shadow-sm" disabled>
                                                Penilaian Dikunci
                                            </button>
                                            <a href="<?= site_url('laporan-harian?staf_id=' . esc($staf_id_terpilih) . '&bulan=' . esc($bulan_terpilih) . '&tahun=' . esc($tahun_terpilih) . '&source_tab=staf') ?>" class="btn btn-warning btn-tactile text-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                                                Setujui Target Kinerja Staf
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" id="btnResetNilaiStaf" aria-label="Reset seluruh nilai kinerja staf">Reset Nilai</button>
                                            <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" aria-label="Simpan draf penilaian kinerja staf">Simpan Draf</button>
                                            <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 py-2 fw-bold shadow-sm" aria-label="Simpan dan terbitkan nilai kinerja staf">Simpan &amp; Terbitkan Penilaian</button>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <?php if (!empty($is_heatmap_hidden)): ?>
                                    <!-- TABEL LAPORAN KEGIATAN HARIAN STAF -->
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bento-stagger bento-stagger-3">
                                        <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                                    Laporan Kegiatan Harian Staf
                                                </span>
                                                <span class="ecc-text-muted-aa small fw-semibold">
                                                    Periode: <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
                                                </span>
                                            </div>
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 num-tabular">
                                                Total: <strong><?= count($log_harian_staf) ?></strong> Kegiatan
                                            </span>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if (empty($log_harian_staf)): ?>
                                                <div class="text-center py-5 px-3">
                                                    <h3 class="h6 fw-bold text-dark mb-1">Belum Ada Catatan Kegiatan Staf</h3>
                                                    <p class="ecc-text-muted-aa small mb-0">Tidak ada laporan kegiatan harian pada periode <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>.</p>
                                                </div>
                                            <?php else: ?>
                                                <div class="table-bento-wrapper">
                                                    <table class="table table-bordered table-hover mb-0 table-bento">
                                                        <thead>
                                                            <tr>
                                                                <th class="col-no-bento text-center">No</th>
                                                                <th class="col-tanggal-bento text-center">Tanggal</th>
                                                                <th class="col-jenis-bento text-center">Jenis</th>
                                                                <th class="col-target">Indikator RHK / Sasaran</th>
                                                                <th class="col-target">Deskripsi Aktivitas</th>
                                                                <th class="col-tanggal-bento text-center">Realisasi</th>
                                                                <th class="col-bukti-bento text-center">Bukti</th>
                                                                <th class="col-jenis-bento text-center">Status</th>
                                                                <th class="col-aksi-bento text-center">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($log_harian_staf as $idx => $logItem): ?>
                                                                <tr>
                                                                    <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris"><?= $idx + 1 ?></td>
                                                                    <td class="text-center num-tabular" data-label="Tanggal">
                                                                        <?php
                                                                            $tglWaktu = strtotime($logItem['tanggal_kegiatan'] ?? '');
                                                                            echo $tglWaktu ? date('d/m/Y', $tglWaktu) : '-';
                                                                        ?>
                                                                    </td>
                                                                    <td class="text-center" data-label="Jenis">
                                                                        <?php if (!empty($logItem['is_tambahan'])): ?>
                                                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1">Tugas Tambahan</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1">Tugas Pokok</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td data-label="Indikator RHK"><?= esc($logItem['indikator_kinerja'] ?? '-') ?></td>
                                                                    <td data-label="Deskripsi Aktivitas"><?= nl2br(esc($logItem['deskripsi_kegiatan'] ?? '-')) ?></td>
                                                                    <td class="text-center num-tabular fw-bold text-primary-emphasis" data-label="Realisasi">
                                                                        <?= (isset($logItem['jumlah_capaian']) && $logItem['jumlah_capaian'] !== null) ? $formatDisplayDecimal($logItem['jumlah_capaian']) : '-' ?>
                                                                        <span class="ecc-text-muted-aa fw-normal"><?= esc($logItem['satuan'] ?? '') ?></span>
                                                                    </td>
                                                                    <td class="text-center" data-label="Bukti">
                                                                        <?php
                                                                            $rawLink = trim((string) ($logItem['link_bukti'] ?? ''));
                                                                            $isSafeUrl = (bool) preg_match('/^https?:\/\//i', $rawLink);
                                                                        ?>
                                                                        <?php if (!empty($rawLink) && $isSafeUrl): ?>
                                                                            <a href="<?= esc($rawLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary-emphasis border btn-bukti-link btn-tactile" title="Lihat bukti pekerjaan">
                                                                                <i class="bi bi-box-arrow-up-right"></i> Bukti
                                                                            </a>
                                                                        <?php else: ?>
                                                                            <span class="ecc-text-muted-aa">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center" data-label="Status">
                                                                        <?php $st = $logItem['status'] ?? 'terkirim'; ?>
                                                                        <?php if ($st === 'disetujui'): ?>
                                                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1">Disetujui</span>
                                                                        <?php elseif ($st === 'terkirim'): ?>
                                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">Terkirim</span>
                                                                        <?php elseif ($st === 'draf' || $st === 'draft'): ?>
                                                                            <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">Draf</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-light text-dark border rounded-pill px-2 py-1"><?= esc(ucfirst($st)) ?></span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="td-aksi text-center" data-label="Aksi">
                                                                        <?php
                                                                            $canManage = (hasAnyRole(['admin', 'direktur']) || !empty($is_atasan) || !empty($is_penilai));
                                                                            $tglRaw = $logItem['tanggal_kegiatan'] ?? '';
                                                                            $tglFormatted = $tglWaktu ? date('d/m/Y', $tglWaktu) : $tglRaw;
                                                                        ?>
                                                                        <?php if ($canManage && !empty($staf_id_terpilih)): ?>
                                                                            <?php if ($st === 'draf' || $st === 'draft'): ?>
                                                                                <span class="badge bg-secondary-subtle text-dark border rounded-pill px-2 py-1">Mode Revisi</span>
                                                                            <?php elseif ($st === 'terkirim' || $st === 'disetujui'): ?>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-warning text-dark fw-semibold rounded-pill px-3 py-1 btn-tactile btn-buka-kunci-penilaian"
                                                                                    data-tanggal="<?= esc($tglRaw) ?>"
                                                                                    data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                                                                    data-tanggal-formatted="<?= esc($tglFormatted) ?>"
                                                                                    title="Kembalikan laporan tanggal ini agar dapat direvisi staf">
                                                                                    Kembalikan
                                                                                </button>
                                                                            <?php else: ?>
                                                                                <span class="ecc-text-muted-aa">-</span>
                                                                            <?php endif; ?>
                                                                        <?php else: ?>
                                                                            <span class="ecc-text-muted-aa">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- HEATMAP KALENDER AKTIVITAS BULANAN STAF -->
                                    <?= view('user/penilaian_kinerja/_calendar_heatmap', [
                                        'heatmap'              => $heatmap_staf,
                                        'isStaf'               => true,
                                        'stafId'               => $staf_id_terpilih,
                                        'bulanNama'            => $bulan_indo[$bulan_terpilih - 1] ?? '',
                                        'tahunTerpilih'        => $tahun_terpilih,
                                        'formatDisplayDecimal' => $formatDisplayDecimal,
                                    ]) ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                                <h2 class="h5 fw-bold text-dark mb-1">Pilih Staf untuk Memulai Penilaian</h2>
                                <p class="ecc-text-muted-aa small mb-0">Pilih nama staf pada daftar di atas untuk melihat target dan menilai kinerja.</p>
                            </div>
                        <?php endif; ?>
                    </div> <!-- End Tab Staf -->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL LOG PEKERJAAN PER TANGGAL -->
<div class="modal fade" id="modalDetailLogTanggal" tabindex="-1" aria-labelledby="modalDetailLogTanggalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-3 px-md-4 py-3">
                <div>
                    <h2 class="h5 modal-title fw-bold text-dark mb-0" id="modalDetailLogTanggalLabel">Rincian Pekerjaan Harian</h2>
                    <small class="ecc-text-muted-aa d-block mt-1" id="modalLogTanggalSub">-</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group" aria-label="Navigasi Tanggal">
                        <button type="button" class="btn btn-outline-secondary btn-nav-date btn-tactile rounded-start-3" id="btnModalPrevDay" title="Hari Sebelumnya" aria-label="Hari Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-nav-date btn-tactile rounded-end-3" id="btnModalNextDay" title="Hari Berikutnya" aria-label="Hari Berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <button type="button" class="btn-close ms-2 btn-tactile" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="card border rounded-4 mb-3 bg-light p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <h3 class="h6 fw-bold text-dark mb-0" id="modalLogInfoTanggal">-</h3>
                            <div class="mt-2" id="modalLogInfoStatusHari" style="display: none;"></div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 flex-shrink-0">
                            <div id="modalLogBadgeRevisiAktif" style="display: none;">
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2 fw-bold rounded-pill">
                                    Mode Revisi Aktif
                                </span>
                            </div>
                            <div id="modalLogBtnRevisiContainer" style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3 py-2 btn-tactile" id="btnModalBukaKunci">
                                    Izinkan Revisi Laporan
                                </button>
                            </div>
                            <div class="d-inline-flex align-items-center bg-white border border-primary-subtle rounded-pill px-3 py-1 gap-2">
                                <span class="ecc-text-muted-aa fw-bold small text-uppercase">Total:</span>
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1 fw-bold num-tabular" id="modalLogBadgeTotal">0 Kegiatan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="h6 fw-bold text-dark mb-2">Daftar Aktivitas &amp; Bukti Pekerjaan</h3>
                <div class="table-bento-wrapper bg-white border rounded-4 overflow-hidden mb-3" id="modalLogTableContainer">
                    <table class="table table-bordered table-hover mb-0 table-bento">
                        <thead>
                            <tr>
                                <th class="col-no-bento text-center">No</th>
                                <th class="col-jenis-bento text-center">Jenis</th>
                                <th class="col-target text-start">Deskripsi Aktivitas</th>
                                <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                <th class="col-tanggal-bento text-center">Realisasi</th>
                                <th class="col-bukti-bento text-center">Bukti</th>
                                <th class="col-jenis-bento text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="modalLogTabelBody"></tbody>
                    </table>
                </div>

                <div id="modalLogEmptyState" style="display: none;" class="text-center py-5 px-3 bg-light rounded-4 border my-2">
                    <h4 class="h6 fw-bold text-dark mb-1">Tidak Ada Laporan Kegiatan</h4>
                    <p class="ecc-text-muted-aa small mb-0" id="modalLogEmptyDesc">Tidak ada catatan aktivitas harian pada tanggal ini.</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center px-3 px-md-4 py-2">
                <div>
                    <a href="#" id="btnModalGoToLapor" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-bold btn-tactile" style="display: none;">
                        Buka di Lapor Harian
                    </a>
                </div>
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold btn-tactile" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
    const ECC_PREDIKAT_THRESHOLDS = <?= json_encode(get_predikat_thresholds()) ?>;
    const bulanTerpilih = <?= esc($bulan_terpilih) ?>;
    const tahunTerpilih = <?= esc($tahun_terpilih) ?>;
    const canEvaluateSelf = <?= $canEvaluateSelf ? 'true' : 'false' ?>;

    $(document).ready(function() {
        let isPenilaianFormDirty = false;
        let isPublishConfirmed = false;
        let isMandiriPublishConfirmed = false;
        let lastClickedAction = 'submit';
        let lastClickedMandiriAction = 'submit';

        const filterPreviousValues = {
            bulan: $('#filterBulanSelect').val(),
            tahun: $('#tahunFilterSelect').val(),
            unit: $('#filterUnitSelect').val(),
            staf: $('#selectStaf').val()
        };

        $(document).on('input change', '#formPenilaian .input-nilai-capaian, #formPenilaian #inputNilaiTambahanGabungan, #formPenilaianMandiri .input-nilai-capaian', function() {
            isPenilaianFormDirty = true;
        });

        window.addEventListener('pageshow', function() {
            isPublishConfirmed = false;
            isMandiriPublishConfirmed = false;
            $('#formPenilaian button[type="submit"], #btnResetNilaiStaf').prop('disabled', false);
            $('#formPenilaian button[value="draft"]').text('Simpan Draf');
            $('#formPenilaian button[value="submit"]').text('Simpan & Terbitkan Penilaian');

            $('#formPenilaianMandiri button[type="submit"], #btnResetNilaiMandiri').prop('disabled', false);
            $('#formPenilaianMandiri button[value="draft"]').text('Simpan Draf');
            $('#formPenilaianMandiri button[value="submit"]').text('Simpan & Terbitkan');
        });

        function handleFilterChange(formToSubmit, changedSelectEl, key) {
            if (isPenilaianFormDirty) {
                const revertVal = filterPreviousValues[key];
                const proceedSubmit = function() {
                    isPenilaianFormDirty = false;
                    formToSubmit[0].submit();
                };
                const cancelSubmit = function() {
                    if (changedSelectEl && changedSelectEl.length) {
                        if (key === 'staf' && typeof $.fn.select2 !== 'undefined') {
                            changedSelectEl.val(revertVal).trigger('change.select2');
                        } else {
                            changedSelectEl.val(revertVal);
                        }
                    }
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Ganti Filter?',
                        text: 'Perubahan nilai belum disimpan akan hilang.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedSubmit();
                        } else {
                            cancelSubmit();
                        }
                    });
                } else if (confirm('Perubahan nilai belum disimpan akan hilang. Tetap ganti filter?')) {
                    proceedSubmit();
                } else {
                    cancelSubmit();
                }
            } else {
                formToSubmit[0].submit();
            }
        }

        $('#filterBulanSelect').on('change', function() {
            handleFilterChange($(this).closest('form'), $(this), 'bulan');
        });
        $('#tahunFilterSelect').on('change', function() {
            handleFilterChange($(this).closest('form'), $(this), 'tahun');
        });
        $('#filterUnitSelect').on('change', function() {
            handleFilterChange($(this).closest('form'), $(this), 'unit');
        });

        if ($('#selectStaf').length) {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#selectStaf').select2({
                    width: '100%',
                    placeholder: 'Cari Nama Staf...',
                    allowClear: false
                });
                $('#selectStaf').on('select2:select', function() {
                    handleFilterChange($(this).closest('form'), $(this), 'staf');
                });
                $('#selectStaf').on('select2:open', function() {
                    setTimeout(function() {
                        const searchField = document.querySelector('.select2-search__field');
                        if (searchField) searchField.focus();
                    }, 50);
                });
            } else {
                $('#selectStaf').on('change', function() {
                    handleFilterChange($(this).closest('form'), $(this), 'staf');
                });
            }
        }

        $(document).on('wheel', 'input[type="number"]', function() {
            $(this).blur();
        });

        function mapBadgeClassForContrast(badgeClass) {
            if (badgeClass === 'bg-danger') return 'bg-danger text-white';
            if (badgeClass === 'bg-warning text-dark') return 'bg-warning text-dark';
            if (badgeClass === 'bg-info text-dark') return 'bg-info-subtle text-info-emphasis border border-info-subtle';
            if (badgeClass === 'bg-primary') return 'bg-primary text-white';
            if (badgeClass === 'bg-success') return 'bg-success text-white';
            return 'bg-secondary text-white';
        }

        function mapColorToBorder(colorToken) {
            if (colorToken === 'warning text-dark') return 'warning';
            if (colorToken === 'info text-dark') return 'info';
            return colorToken;
        }

        function mapColorToText(colorToken) {
            if (colorToken === 'warning text-dark') return 'warning-emphasis';
            if (colorToken === 'info text-dark') return 'info-emphasis';
            if (colorToken === 'danger') return 'danger-emphasis';
            if (colorToken === 'success') return 'success-emphasis';
            if (colorToken === 'primary') return 'primary-emphasis';
            return 'secondary';
        }

        function getPredikatInfo(val) {
            if (isNaN(val) || val === '' || val === null) {
                return { label: 'Belum Dinilai', class: 'bg-secondary text-white', colorToken: 'secondary' };
            }
            const num = parseFloat(val);
            if (num <= ECC_PREDIKAT_THRESHOLDS.sangat_kurang.max) {
                return { label: ECC_PREDIKAT_THRESHOLDS.sangat_kurang.label, class: mapBadgeClassForContrast(ECC_PREDIKAT_THRESHOLDS.sangat_kurang.badge_class), colorToken: ECC_PREDIKAT_THRESHOLDS.sangat_kurang.color };
            }
            if (num <= ECC_PREDIKAT_THRESHOLDS.kurang.max) {
                return { label: ECC_PREDIKAT_THRESHOLDS.kurang.label, class: mapBadgeClassForContrast(ECC_PREDIKAT_THRESHOLDS.kurang.badge_class), colorToken: ECC_PREDIKAT_THRESHOLDS.kurang.color };
            }
            if (num <= ECC_PREDIKAT_THRESHOLDS.butuh_perbaikan.max) {
                return { label: ECC_PREDIKAT_THRESHOLDS.butuh_perbaikan.label, class: mapBadgeClassForContrast(ECC_PREDIKAT_THRESHOLDS.butuh_perbaikan.badge_class), colorToken: ECC_PREDIKAT_THRESHOLDS.butuh_perbaikan.color };
            }
            if (num <= ECC_PREDIKAT_THRESHOLDS.baik.max) {
                return { label: ECC_PREDIKAT_THRESHOLDS.baik.label, class: mapBadgeClassForContrast(ECC_PREDIKAT_THRESHOLDS.baik.badge_class), colorToken: ECC_PREDIKAT_THRESHOLDS.baik.color };
            }
            return { label: ECC_PREDIKAT_THRESHOLDS.sangat_baik.label, class: mapBadgeClassForContrast(ECC_PREDIKAT_THRESHOLDS.sangat_baik.badge_class), colorToken: ECC_PREDIKAT_THRESHOLDS.sangat_baik.color };
        }

        function recalculateFormSummary(config) {
            let total = 0;
            let count = 0;
            const $rhkInputs = $(config.rhkSelector);
            const rhkTotal = $rhkInputs.length;
            let rhkFilled = 0;
            let hasAnyDraftRow = false;
            let hasAnyTerbitRow = false;
            let hasAnyNilaiRow = false;

            $rhkInputs.each(function() {
                const rawVal = ($(this).val() || '').trim();
                const initialVal = $(this).attr('data-initial-value') || '';
                const statusPenilaian = $(this).attr('data-status-penilaian') || 'none';
                const v = parseFloat(rawVal);
                const $badgeContainer = $(this).closest('td').find('.predikat-badge-container');

                if (rawVal !== '' && !isNaN(v)) {
                    if (!$(this).hasClass('is-invalid')) {
                        total += v;
                        count++;
                        rhkFilled++;
                        hasAnyNilaiRow = true;
                    }
                    const p = getPredikatInfo(v);
                    const isDirty = (initialVal !== '' && parseFloat(initialVal) !== v) || (initialVal === '' && rawVal !== '');
                    let rowStatusBadge = '';
                    if (isDirty || statusPenilaian === 'draft') {
                        rowStatusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 badge-status-row">Draf</span>';
                        hasAnyDraftRow = true;
                    } else if (statusPenilaian === 'terbit') {
                        rowStatusBadge = '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 badge-status-row">Terbit</span>';
                        hasAnyTerbitRow = true;
                    } else {
                        rowStatusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 badge-status-row">Draf</span>';
                        hasAnyDraftRow = true;
                    }
                    $badgeContainer.html(rowStatusBadge + '<span class="badge ' + p.class + ' rounded-pill px-2 py-1 badge-predikat-pop">' + escHtml(p.label) + '</span>');
                    $(this).removeClass('border-warning shadow-sm');
                } else {
                    $badgeContainer.html('<span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1">Belum dinilai</span>');
                }
            });

            const $tmbInput = $(config.tmbSelector);
            const hasTambahan = $tmbInput.length > 0;
            let tambahanFilled = false;

            if (hasTambahan) {
                const rawTambahan = ($tmbInput.val() || '').trim();
                const vTambahan = parseFloat(rawTambahan);
                const $hintContainer = $(config.tmbHintSelector);
                const initialTmbVal = $tmbInput.attr('data-initial-value') || '';
                const tmbStatus = $tmbInput.attr('data-status-penilaian') || 'none';

                if (rawTambahan !== '' && !isNaN(vTambahan) && vTambahan >= 0 && vTambahan <= 150) {
                    total += vTambahan;
                    count++;
                    tambahanFilled = true;
                    hasAnyNilaiRow = true;

                    const isTmbDirty = (initialTmbVal !== '' && parseFloat(initialTmbVal) !== vTambahan) || (initialTmbVal === '' && rawTambahan !== '');
                    let tmbStatusLabel = 'Draf';
                    if (!isTmbDirty && tmbStatus === 'terbit') {
                        tmbStatusLabel = 'Terbit';
                        hasAnyTerbitRow = true;
                        $hintContainer.html('<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2 py-1 badge-predikat-pop">Terbit</span>');
                    } else {
                        hasAnyDraftRow = true;
                        $hintContainer.html('<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1 badge-predikat-pop">Draf</span>');
                    }
                    $(config.tmbStatusTextSelector).text(vTambahan.toString().replace('.', ',') + '% (' + tmbStatusLabel + ')');
                    $tmbInput.removeClass('border-warning shadow-sm');
                } else {
                    $(config.tmbStatusTextSelector).text('Belum Dinilai');
                    if ($hintContainer.length) {
                        $hintContainer.html('<span class="badge bg-light ecc-text-muted-aa border rounded-pill px-2 py-1">Belum dinilai</span>');
                    }
                }
            }

            const $summaryBadgeEl = $(config.summaryStatusBadgeSelector);
            if ($summaryBadgeEl.length) {
                if (hasAnyDraftRow || (hasAnyNilaiRow && !hasAnyTerbitRow)) {
                    $summaryBadgeEl.attr('class', 'badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold').text('Draf');
                } else if (hasAnyTerbitRow && !hasAnyDraftRow) {
                    $summaryBadgeEl.attr('class', 'badge bg-success text-white px-3 py-1 rounded-pill fw-bold').text('Terbit');
                } else {
                    $summaryBadgeEl.attr('class', 'badge bg-secondary text-white px-3 py-1 rounded-pill fw-bold').text('Belum Dinilai');
                }
            }

            $(config.countRhkSelector).text(rhkFilled);

            const totalComponents = rhkTotal + (hasTambahan ? 1 : 0);
            const totalFilled = rhkFilled + (hasTambahan && tambahanFilled ? 1 : 0);
            const totalUnfilled = totalComponents - totalFilled;

            if (totalUnfilled > 0) {
                if (config.countUnfilledSelector) {
                    $(config.countUnfilledSelector).text(totalUnfilled);
                }
                $(config.hintUnfilledSelector).removeClass('d-none');
                $(config.allFilledSelector).addClass('d-none');
            } else {
                $(config.hintUnfilledSelector).addClass('d-none');
                $(config.allFilledSelector).removeClass('d-none');
            }

            const avg = count > 0 ? (Math.round((total / count) * 100) / 100) : 0;
            $(config.totalTextSelector).text(avg.toString().replace('.', ','));

            const pRata = getPredikatInfo(count > 0 ? avg : null);
            const $textEl = $(config.totalTextSelector);
            const $wrapper = $(config.summaryCardSelector);
            const $badgeEl = $(config.totalBadgeSelector);

            $textEl.removeClass('text-success-emphasis text-secondary text-danger-emphasis text-warning-emphasis text-primary-emphasis text-info-emphasis text-success text-danger text-warning text-primary text-info');
            $wrapper.removeClass('border-success border-secondary border-danger border-warning border-primary border-info');

            $textEl.addClass('text-' + mapColorToText(pRata.colorToken));
            $wrapper.addClass('border-' + mapColorToBorder(pRata.colorToken));
            $badgeEl.attr('class', 'badge ' + pRata.class + ' fs-6 px-3 py-2 rounded-pill badge-predikat-pop').text(pRata.label);

            return { totalUnfilled, count, totalFilled, totalComponents };
        }

        const stafScoreConfig = {
            rhkSelector: '#tablePenilaianStaf tbody .input-nilai-capaian',
            tmbSelector: '#inputNilaiTambahanGabungan',
            tmbHintSelector: '#hintTambahanContainer',
            tmbStatusTextSelector: '#textTambahanStatus',
            summaryStatusBadgeSelector: '#badgeSummaryStatusPenilaian',
            countRhkSelector: '#countRhkTerisi',
            countUnfilledSelector: '#textCountUnfilled',
            hintUnfilledSelector: '#badgeHintUnfilled',
            allFilledSelector: '#badgeAllFilled',
            totalTextSelector: '#totalKinerjaStafText',
            summaryCardSelector: '#cardKinerjaStafSummary',
            totalBadgeSelector: '#totalPredikatStafBadge'
        };

        const mandiriScoreConfig = {
            rhkSelector: '#tablePenilaianMandiri tbody .input-nilai-capaian',
            tmbSelector: '#inputNilaiTambahanMandiri',
            tmbHintSelector: '#hintTambahanMandiriContainer',
            tmbStatusTextSelector: '#textTambahanMandiriStatus',
            summaryStatusBadgeSelector: '#badgeSummaryStatusMandiri',
            countRhkSelector: '#countRhkMandiriTerisi',
            countUnfilledSelector: null,
            hintUnfilledSelector: '#badgeHintUnfilledMandiri',
            allFilledSelector: '#badgeAllFilledMandiri',
            totalTextSelector: '#totalKinerjaMandiriText',
            summaryCardSelector: '#cardKinerjaMandiriSummary',
            totalBadgeSelector: '#totalPredikatMandiriBadge'
        };

        if ($('#tablePenilaianStaf').length) {
            recalculateFormSummary(stafScoreConfig);
        }
        if (canEvaluateSelf && $('#tablePenilaianMandiri').length) {
            recalculateFormSummary(mandiriScoreConfig);
        }

        $(document).on('input change keyup', '.input-nilai-capaian, #inputNilaiTambahanGabungan, #inputNilaiTambahanMandiri', function() {
            const val = parseFloat($(this).val());
            const $error = $(this).parent().siblings('.invalid-feedback');
            const $form = $(this).closest('form');
            const $btnSubmit = $form.find('button[type="submit"]');

            if (!isNaN(val) && (val < 0 || val > 150)) {
                $(this).addClass('is-invalid');
                $error.show();
                $btnSubmit.prop('disabled', true);
            } else {
                $(this).removeClass('is-invalid');
                $error.hide();
                if ($form.find('.input-nilai-capaian.is-invalid').length === 0) {
                    $btnSubmit.prop('disabled', false);
                }
            }

            if ($form.attr('id') === 'formPenilaianMandiri') {
                recalculateFormSummary(mandiriScoreConfig);
            } else if ($('#tablePenilaianStaf').length) {
                recalculateFormSummary(stafScoreConfig);
            }
        });

        // Reset Nilai Staf
        $('#formPenilaian button[type="reset"], #btnResetNilaiStaf').on('click', function(e) {
            e.preventDefault();
            function doResetStaf() {
                $('#formPenilaian .input-nilai-capaian, #formPenilaian #inputNilaiTambahanGabungan').val('').removeClass('is-invalid border-warning shadow-sm').removeAttr('title');
                $('#formPenilaian .invalid-feedback').hide();
                recalculateFormSummary(stafScoreConfig);
                isPenilaianFormDirty = false;
                $('#penilaianActionInput').val('reset');
                $('#formPenilaian button[type="submit"], #btnResetNilaiStaf').prop('disabled', true);
                $('#formPenilaian')[0].submit();
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reset Penilaian?',
                    text: 'Nilai kinerja staf pada periode ini akan dikosongkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) doResetStaf();
                });
            } else if (confirm('Nilai kinerja staf pada periode ini akan dikosongkan. Lanjutkan?')) {
                doResetStaf();
            }
        });

        // Reset Nilai Mandiri Direktur
        $('#btnResetNilaiMandiri').on('click', function(e) {
            e.preventDefault();
            function doResetMandiri() {
                $('#formPenilaianMandiri .input-nilai-capaian, #formPenilaianMandiri #inputNilaiTambahanMandiri').val('').removeClass('is-invalid border-warning shadow-sm').removeAttr('title');
                $('#formPenilaianMandiri .invalid-feedback').hide();
                recalculateFormSummary(mandiriScoreConfig);
                isPenilaianFormDirty = false;
                $('#mandiriActionInput').val('reset');
                $('#formPenilaianMandiri button[type="submit"], #btnResetNilaiMandiri').prop('disabled', true);
                $('#formPenilaianMandiri')[0].submit();
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Reset Nilai?',
                    text: 'Nilai kinerja mandiri pada periode ini akan dikosongkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) doResetMandiri();
                });
            } else if (confirm('Nilai kinerja mandiri pada periode ini akan dikosongkan. Lanjutkan?')) {
                doResetMandiri();
            }
        });

        $('#formPenilaian button[type="submit"]').on('click', function() {
            lastClickedAction = $(this).val() || 'submit';
            $('#penilaianActionInput').val(lastClickedAction);
        });

        $('#formPenilaian').on('submit', function(e) {
            const form = $(this);
            let hasInvalid = false;
            $('#penilaianActionInput').val(lastClickedAction);

            form.find('.input-nilai-capaian').each(function() {
                const v = parseFloat($(this).val());
                if (!isNaN(v) && (v < 0 || v > 150)) {
                    hasInvalid = true;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (hasInvalid) {
                e.preventDefault();
                const invalidMsg = 'Nilai capaian harus di antara 0 dan 150%.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Nilai di Luar Batas', text: invalidMsg });
                } else {
                    alert(invalidMsg);
                }
                return false;
            }

            const stats = recalculateFormSummary(stafScoreConfig);
            if (lastClickedAction === 'submit' && stats.totalUnfilled > 0 && !isPublishConfirmed) {
                e.preventDefault();
                const confirmPublish = function() {
                    isPublishConfirmed = true;
                    isPenilaianFormDirty = false;
                    form.find('#tablePenilaianStaf tbody .input-nilai-capaian').each(function() {
                        if ($(this).val() === '') $(this).val('0');
                    });
                    const tmb = form.find('#inputNilaiTambahanGabungan');
                    if (tmb.length && tmb.val() === '') tmb.val('0');
                    recalculateFormSummary(stafScoreConfig);

                    $('#penilaianActionInput').val('submit');
                    form.find('button[type="submit"]').prop('disabled', true);
                    form.find('button[value="submit"]').text('Menerbitkan...');
                    form[0].submit();
                };

                const cancelPublish = function() {
                    form.find('.input-nilai-capaian').each(function() {
                        if ($(this).val() === '') $(this).addClass('border-warning shadow-sm');
                    });
                    const firstEmpty = form.find('.input-nilai-capaian').filter(function() { return $(this).val() === ''; }).first();
                    if (firstEmpty.length) firstEmpty.focus();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Terbitkan Nilai?',
                        text: `${stats.totalUnfilled} komponen belum diisi dan akan dinilai 0. Tetap terbitkan?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Ya, Terbitkan',
                        cancelButtonText: 'Periksa Kembali',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) confirmPublish();
                        else cancelPublish();
                    });
                } else if (confirm(`${stats.totalUnfilled} komponen belum diisi dan akan dinilai 0. Tetap terbitkan?`)) {
                    confirmPublish();
                } else {
                    cancelPublish();
                }
                return false;
            }

            $('#penilaianActionInput').val(lastClickedAction);
            isPenilaianFormDirty = false;
            const submitBtns = form.find('button[type="submit"]');
            const activeBtn = form.find('button[value="' + lastClickedAction + '"]');
            if (activeBtn.length) {
                activeBtn.text(lastClickedAction === 'submit' ? 'Menerbitkan...' : 'Menyimpan...');
            }
            setTimeout(() => { submitBtns.prop('disabled', true); }, 10);
        });

        $('#formPenilaianMandiri button[type="submit"]').on('click', function() {
            lastClickedMandiriAction = $(this).val() || 'submit';
            $('#mandiriActionInput').val(lastClickedMandiriAction);
        });

        $('#formPenilaianMandiri').on('submit', function(e) {
            const form = $(this);
            let hasInvalid = false;
            $('#mandiriActionInput').val(lastClickedMandiriAction);

            form.find('.input-nilai-capaian').each(function() {
                const v = parseFloat($(this).val());
                if (!isNaN(v) && (v < 0 || v > 150)) {
                    hasInvalid = true;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (hasInvalid) {
                e.preventDefault();
                const invalidMsg = 'Nilai capaian harus di antara 0 dan 150%.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Nilai di Luar Batas', text: invalidMsg });
                } else {
                    alert(invalidMsg);
                }
                return false;
            }

            const stats = recalculateFormSummary(mandiriScoreConfig);
            if (lastClickedMandiriAction === 'submit' && stats.totalUnfilled > 0 && !isMandiriPublishConfirmed) {
                e.preventDefault();
                const confirmPublishMandiri = function() {
                    isMandiriPublishConfirmed = true;
                    isPenilaianFormDirty = false;
                    form.find('#tablePenilaianMandiri tbody .input-nilai-capaian').each(function() {
                        if ($(this).val() === '') $(this).val('0');
                    });
                    const tmb = form.find('#inputNilaiTambahanMandiri');
                    if (tmb.length && tmb.val() === '') tmb.val('0');
                    recalculateFormSummary(mandiriScoreConfig);

                    $('#mandiriActionInput').val('submit');
                    form.find('button[type="submit"]').prop('disabled', true);
                    form.find('button[value="submit"]').text('Menerbitkan...');
                    form[0].submit();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Terbitkan Nilai?',
                        text: `${stats.totalUnfilled} komponen belum diisi dan akan dinilai 0. Tetap terbitkan?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#475569',
                        confirmButtonText: 'Ya, Terbitkan',
                        cancelButtonText: 'Periksa Kembali',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) confirmPublishMandiri();
                    });
                } else if (confirm(`${stats.totalUnfilled} komponen belum diisi dan akan dinilai 0. Tetap terbitkan?`)) {
                    confirmPublishMandiri();
                }
                return false;
            }

            isPenilaianFormDirty = false;
            const submitBtns = form.find('button[type="submit"]');
            const activeBtn = form.find('button[value="' + lastClickedMandiriAction + '"]');
            if (activeBtn.length) {
                activeBtn.text(lastClickedMandiriAction === 'submit' ? 'Menerbitkan...' : 'Menyimpan...');
            }
            setTimeout(() => { submitBtns.prop('disabled', true); }, 10);
        });

        function syncFilterUnitVisibility(tabName) {
            const unitCol = document.getElementById('filterUnitCol');
            if (!unitCol) return;
            if (tabName === 'staf') {
                unitCol.classList.remove('d-none');
            } else {
                unitCol.classList.add('d-none');
            }
        }

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('aria-controls');
            $('#active_tab_input').val(target);
            syncFilterUnitVisibility(target);
            if (history.pushState) {
                history.pushState(null, null, '#' + target);
            } else {
                location.hash = '#' + target;
            }
        });

        if (window.location.hash) {
            const hashTab = window.location.hash.substring(1);
            const targetTab = document.querySelector('button[aria-controls="' + hashTab + '"]');
            if (targetTab) {
                const tab = new bootstrap.Tab(targetTab);
                tab.show();
                $('#active_tab_input').val(hashTab);
                syncFilterUnitVisibility(hashTab);
            }
        } else {
            syncFilterUnitVisibility($('#active_tab_input').val());
        }

        // Modal Detail Log Heatmap
        let currentActiveHeatmapCell = null;

        function populateModalFromCell(cell) {
            if (!cell || !cell.length) return;
            currentActiveHeatmapCell = cell;

            const isStaf = cell.attr('data-is-staf') === '1';
            const stafId = cell.attr('data-staf-id') || '';
            const dateStr = cell.attr('data-date-str') || cell.attr('data-tanggal') || '';
            const tglFormatted = cell.attr('data-tanggal-formatted') || cell.data('tanggal-formatted') || '-';
            const isHoliday = cell.attr('data-is-holiday') === '1';
            const holidayName = cell.attr('data-holiday-name') || '';
            const isWeekend = cell.attr('data-is-weekend') === '1';
            const totalKegiatan = parseInt(cell.attr('data-total-kegiatan'), 10) || 0;
            const hasDraft = cell.attr('data-has-draft') === '1';
            const hasTerkirim = cell.attr('data-has-terkirim') === '1';

            let logs = [];
            const rawLogs = cell.data('logs');
            if (Array.isArray(rawLogs)) {
                logs = rawLogs;
            } else if (typeof rawLogs === 'string') {
                try { logs = JSON.parse(rawLogs); } catch (err) { logs = []; }
            } else {
                const attrLogs = cell.attr('data-logs');
                if (attrLogs) {
                    try { logs = JSON.parse(attrLogs); } catch (err) { logs = []; }
                }
            }

            $('#modalLogTanggalSub').text(tglFormatted);
            $('#modalLogInfoTanggal').text(tglFormatted);
            $('#modalLogBadgeTotal').text(`${totalKegiatan} Kegiatan`);

            if (isHoliday) {
                $('#modalLogInfoStatusHari').html(`<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">Libur Nasional: ${escHtml(holidayName)}</span>`).show();
            } else if (isWeekend) {
                $('#modalLogInfoStatusHari').html('<span class="badge bg-light ecc-text-muted-aa border px-3 py-1 rounded-pill fw-semibold">Akhir Pekan (Sabtu / Minggu)</span>').show();
            } else {
                $('#modalLogInfoStatusHari').empty().hide();
            }

            const canManageRevisi = <?= (hasAnyRole(['admin', 'direktur']) || $is_atasan || $is_penilai) ? 'true' : 'false' ?>;
            const isRevisi = cell.attr('data-is-revisi') === '1';

            if (isStaf) {
                $('#btnModalGoToLapor').hide();
                if (canManageRevisi && stafId) {
                    if (isRevisi || (hasDraft && !hasTerkirim && totalKegiatan > 0)) {
                        $('#modalLogBtnRevisiContainer').hide();
                        $('#modalLogBadgeRevisiAktif').show();
                    } else if (hasTerkirim) {
                        $('#btnModalBukaKunci').data('tanggal', dateStr).data('staf-id', stafId).data('tanggal-formatted', tglFormatted);
                        $('#modalLogBtnRevisiContainer').show();
                        $('#modalLogBadgeRevisiAktif').hide();
                    } else {
                        $('#modalLogBtnRevisiContainer').hide();
                        $('#modalLogBadgeRevisiAktif').hide();
                    }
                } else {
                    $('#modalLogBtnRevisiContainer').hide();
                    $('#modalLogBadgeRevisiAktif').hide();
                }
            } else {
                $('#modalLogBtnRevisiContainer').hide();
                $('#modalLogBadgeRevisiAktif').hide();
                const laporUrl = '<?= site_url('log-kegiatan') ?>' + (dateStr ? ('?tanggal=' + encodeURIComponent(dateStr)) : '');
                $('#btnModalGoToLapor').attr('href', laporUrl).show();
            }

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
                        ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill fw-semibold">Tambahan</span>'
                        : '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-2 py-1 rounded-pill fw-semibold">Utama</span>';

                    let capaianVal = '-';
                    if (item.jumlah_capaian !== null && item.jumlah_capaian !== undefined && item.jumlah_capaian !== '') {
                        const num = parseFloat(item.jumlah_capaian);
                        if (!isNaN(num)) {
                            capaianVal = num.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
                        }
                    }
                    const satuanText = item.satuan ? escHtml(item.satuan) : '';
                    const capaianFull = (capaianVal !== '-') ? `${capaianVal} <span class="small fw-normal ecc-text-muted-aa">${satuanText}</span>` : '-';

                    let buktiBtn = '<span class="ecc-text-muted-aa small">-</span>';
                    if (item.link_bukti && typeof item.link_bukti === 'string' && /^https?:\/\//i.test(item.link_bukti.trim())) {
                        buktiBtn = `<a href="${escHtml(item.link_bukti.trim())}" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm text-primary-emphasis border btn-bukti-link btn-tactile"><i class="bi bi-box-arrow-up-right"></i> Bukti</a>`;
                    }

                    const statusBadge = (item.status === 'terkirim' || item.status === 'disetujui')
                        ? '<span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1 rounded-pill fw-semibold">Terkirim</span>'
                        : '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill fw-semibold">Draf</span>';

                    const descHtml = escHtml(item.deskripsi_kegiatan || '-').replace(/\n/g, '<br>');
                    const indikatorText = escHtml(item.indikator_kinerja || '-');

                    const rowHtml = `
                        <tr>
                            <td class="td-no text-center fw-bold ecc-text-muted-aa num-tabular" data-label="Baris">${idx + 1}</td>
                            <td class="text-center" data-label="Jenis">${jenisBadge}</td>
                            <td class="text-dark" data-label="Deskripsi Aktivitas">${descHtml}</td>
                            <td class="ecc-text-muted-aa" data-label="Indikator RHK">${indikatorText}</td>
                            <td class="text-center fw-bold text-primary-emphasis num-tabular" data-label="Realisasi">${capaianFull}</td>
                            <td class="text-center" data-label="Bukti">${buktiBtn}</td>
                            <td class="text-center" data-label="Status">${statusBadge}</td>
                        </tr>
                    `;
                    tbody.append(rowHtml);
                });
            } else {
                $('#modalLogTableContainer').hide();
                if (isRevisi) {
                    $('#modalLogEmptyDesc').text('Laporan tanggal ini sedang dibuka untuk revisi staf.');
                } else if (isHoliday) {
                    $('#modalLogEmptyDesc').text(`Hari Libur Nasional: ${holidayName}. Tidak ada laporan kegiatan.`);
                } else if (isWeekend) {
                    $('#modalLogEmptyDesc').text('Akhir pekan. Tidak ada laporan kegiatan.');
                } else {
                    $('#modalLogEmptyDesc').text('Tidak ada laporan kegiatan pada tanggal ini.');
                }
                $('#modalLogEmptyState').show();
            }

            const modalEl = document.getElementById('modalDetailLogTanggal');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
                    $('#modalDetailLogTanggal').modal('show');
                }
            }
        }

        $(document).on('click', '.heatmap-day-cell', function(e) {
            e.preventDefault();
            populateModalFromCell($(this));
        });

        $(document).on('keydown', '.heatmap-day-cell', function(e) {
            if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
                e.preventDefault();
                populateModalFromCell($(this));
            }
        });

        $('#modalDetailLogTanggal').on('hidden.bs.modal', function() {
            if (currentActiveHeatmapCell && currentActiveHeatmapCell.length) {
                currentActiveHeatmapCell.focus();
            }
        });

        $(document).on('click', '#btnModalPrevDay', function(e) {
            e.preventDefault();
            if (currentActiveHeatmapCell) {
                const prevCell = currentActiveHeatmapCell.prevAll('.heatmap-day-cell').first();
                if (prevCell && prevCell.length) populateModalFromCell(prevCell);
            }
        });

        $(document).on('click', '#btnModalNextDay', function(e) {
            e.preventDefault();
            if (currentActiveHeatmapCell) {
                const nextCell = currentActiveHeatmapCell.nextAll('.heatmap-day-cell').first();
                if (nextCell && nextCell.length) populateModalFromCell(nextCell);
            }
        });

        $(document).on('click', '#btnModalBukaKunci, .btn-buka-kunci-penilaian', function(e) {
            e.preventDefault();
            const tanggal = $(this).data('tanggal') || $(this).attr('data-tanggal');
            const stafId = $(this).data('staf-id') || $(this).attr('data-staf-id');
            const tglFormatted = $(this).data('tanggal-formatted') || tanggal;
            const csrfName = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!tanggal || !stafId) {
                alert('Data tanggal atau staf tidak lengkap.');
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
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal memproses permintaan.', 'error');
                        } else {
                            alert(response.message || 'Gagal memproses permintaan.');
                        }
                    },
                    error: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', 'Terjadi kendala. Silakan coba lagi.', 'error');
                        } else {
                            alert('Terjadi kendala. Silakan coba lagi.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Kembalikan Laporan?',
                    html: `Laporan tanggal <strong>${escHtml(tglFormatted)}</strong> akan dikembalikan ke draf untuk direvisi staf.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Ya, Kembalikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) executeBukaKunciStaf();
                });
            } else if (confirm(`Laporan tanggal ${tglFormatted} akan dikembalikan ke draf. Lanjutkan?`)) {
                executeBukaKunciStaf();
            }
        });

        function escHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        if (window.matchMedia && window.matchMedia('(hover: hover)').matches && typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });
        }

        $(document).on('click', '.btn-batal-approve-target-penilaian', function(e) {
            e.preventDefault();
            const stafId = $(this).data('staf-id');
            const bulan = $(this).data('bulan');
            const tahun = $(this).data('tahun');
            const csrfName = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!stafId || !bulan || !tahun) {
                alert('Data staf atau periode tidak lengkap.');
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
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal memproses data.', 'error');
                        } else {
                            alert('Gagal: ' + (response.message || 'Gagal memproses data.'));
                        }
                    },
                    error: function() {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', 'Terjadi kendala. Silakan coba lagi.', 'error');
                        } else {
                            alert('Terjadi kendala. Silakan coba lagi.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batalkan Persetujuan?',
                    text: 'Target dan kegiatan terkait akan kembali ke draf, serta nilai penilaian direset.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) executeCancel();
                });
            } else if (confirm('Target dan kegiatan terkait akan kembali ke draf, serta nilai penilaian direset. Lanjutkan?')) {
                executeCancel();
            }
        });
    });
</script>
<?= $this->endSection() ?>
