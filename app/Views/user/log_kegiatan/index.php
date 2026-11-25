<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Lapor Kegiatan Harian<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/themes/airbnb.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<style>
    .num-tabular {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-text-muted-aa {
        color: #475569;
    }

    /* Proportional Desktop Table (No Forced Horizontal Overflow) */
    .tabel-log-wrapper {
        overflow-x: visible;
    }
    .tabel-log-bento {
        width: 100%;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .tabel-log-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
        vertical-align: middle;
        white-space: normal;
        overflow-wrap: break-word;
    }
    .tabel-log-bento tbody td {
        padding: 12px 16px;
        vertical-align: top !important;
        border-color: #f1f5f9;
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-wrap: break-word;
    }
    .tabel-log-bento tbody td.td-no {
        padding: 16px 8px 12px;
    }
    .tabel-log-bento tbody td.td-aksi {
        padding: 12px 8px;
    }
    .tabel-log-bento tbody tr:not(#rowHeaderPokok):not(#rowHeaderTambahan) {
        transition: background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tabel-log-bento tbody tr:not(#rowHeaderPokok):not(#rowHeaderTambahan):hover td {
        background-color: #f8fafc;
    }
    .tabel-log-bento tbody tr:last-child td {
        border-bottom: 0;
    }

    .col-no-log {
        width: 56px;
        white-space: nowrap !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .col-target-log {
        width: 24%;
    }
    .col-deskripsi-log {
        width: auto;
    }
    .col-capaian-log {
        width: 20%;
    }
    .col-bukti-log {
        width: 18%;
    }
    .col-aksi-log {
        width: 64px;
        white-space: nowrap !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .tabel-log-bento.is-locked-table .col-target-log {
        width: 26%;
    }
    .tabel-log-bento.is-locked-table .col-deskripsi-log {
        width: auto;
    }
    .tabel-log-bento.is-locked-table .col-capaian-log {
        width: 20%;
    }
    .tabel-log-bento.is-locked-table .col-bukti-log {
        width: 18%;
    }

    .input-capaian-num::-webkit-outer-spin-button,
    .input-capaian-num::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-capaian-num {
        -moz-appearance: textfield;
        min-width: 0;
    }

    /* Unified Horizontal Capaian Control (Tugas Pokok & Tugas Tambahan) */
    .capaian-input-group {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        gap: 1px;
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background-color: #e2e8f0;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .capaian-input-group:focus-within {
        border-color: var(--ecc-primary, #0d6efd);
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    .capaian-input-group:has(.is-invalid) {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .capaian-input-group .input-capaian-num {
        flex: 0 0 72px;
        width: 72px;
        min-width: 72px;
        height: 36px;
        border: 0 !important;
        border-radius: 8px 0 0 8px !important;
        padding: 4px 8px !important;
        font-size: 0.875rem;
        background-color: #ffffff;
        box-shadow: none !important;
        text-align: center;
    }
    .capaian-input-group .badge-capaian-satuan,
    .capaian-input-group .input-satuan-tambahan {
        flex: 1 1 0;
        min-width: 0;
        width: auto;
        height: 36px;
        border: 0 !important;
        border-radius: 0 8px 8px 0 !important;
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
        line-height: 1.4;
        background-color: #f8fafc;
        color: #475569;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        box-shadow: none !important;
    }
    .capaian-input-group .badge-capaian-satuan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, color 0.2s ease;
    }
    .capaian-input-group .input-satuan-tambahan {
        color: #0f172a;
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .capaian-input-group .input-satuan-tambahan::placeholder {
        color: #64748b;
        font-weight: 400;
    }
    .capaian-input-group .input-satuan-tambahan:focus {
        background-color: #ffffff;
        text-overflow: clip;
    }
    .capaian-input-group .input-capaian-num:focus,
    .capaian-input-group .input-satuan-tambahan:focus {
        box-shadow: none !important;
        border-color: transparent !important;
    }
    .col-capaian-log .readonly-capaian-box {
        width: 100%;
        min-height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Auto-Grow Textarea & Multi-Line Target RHK Dropdown (Tanpa Search) */
    .tabel-log-bento textarea.auto-grow-textarea.form-control {
        overflow-y: hidden !important;
        resize: none !important;
        width: 100%;
        height: auto;
        min-height: 56px;
        field-sizing: content;
        line-height: 1.45;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }
    .tabel-log-bento .select2-container--bootstrap-5 {
        width: 100% !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single.target-rhk-selection {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        background-position: right 12px center !important;
        min-height: 36px !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        padding: 8px 32px 8px 12px !important;
        font-size: 0.875rem !important;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .select2-container--bootstrap-5 .select2-selection--single.target-rhk-selection .select2-selection__rendered {
        display: block;
        padding: 0 !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.45 !important;
        color: #0f172a !important;
        font-size: 0.875rem !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single.target-rhk-selection .select2-selection__placeholder {
        color: #64748b !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection--single.target-rhk-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection--single.target-rhk-selection {
        border-color: var(--ecc-primary, #0d6efd) !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
    }
    select.is-invalid + .select2-container--bootstrap-5 .select2-selection--single.target-rhk-selection {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        animation: invalidPulse 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 12px !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12) !important;
        overflow: hidden;
        min-width: 360px;
        max-width: min(480px, calc(100vw - 32px));
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__options {
        max-height: 360px !important;
        overflow-y: auto;
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__option {
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        padding: 8px 12px !important;
        font-size: 0.875rem !important;
        line-height: 1.45;
        color: #0f172a;
        border-bottom: 1px solid #f1f5f9;
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__option:last-child {
        border-bottom: 0;
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__option--highlighted,
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__option--selectable:hover {
        background-color: #eff6ff !important;
        color: #1e40af !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown .select2-results__option--selected {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600;
    }

    /* Table Input Focus & Hover Micro-Interactions */
    .tabel-log-bento input.form-control,
    .tabel-log-bento textarea.form-control,
    .tabel-log-bento select.form-select {
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
        border-radius: 8px;
    }
    .tabel-log-bento input.form-control:focus,
    .tabel-log-bento textarea.form-control:focus,
    .tabel-log-bento select.form-select:focus {
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
        background-color: #ffffff !important;
    }

    /* Validation Error Attention Pulse */
    @keyframes invalidPulse {
        0% { transform: translateX(0); }
        20% { transform: translateX(-3px); }
        40% { transform: translateX(3px); }
        60% { transform: translateX(-2px); }
        80% { transform: translateX(2px); }
        100% { transform: translateX(0); }
    }
    .form-control.is-invalid,
    .form-select.is-invalid {
        animation: invalidPulse 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border-color: var(--bs-danger) !important;
    }

    .readonly-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.875rem;
        line-height: 1.45;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .readonly-box:hover {
        background-color: #ffffff;
        border-color: var(--bs-border-color);
    }

    /* Motion Design & Natural Deceleration Transitions */
    @keyframes rowSlideInPokok {
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
    .row-tugas-pokok.row-slide-in {
        animation: rowSlideInPokok 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes rowSlideInTambahan {
        0% {
            opacity: 0;
            transform: translateY(-8px);
            background-color: #ecfdf5;
        }
        100% {
            opacity: 1;
            transform: translateY(0);
            background-color: transparent;
        }
    }
    .row-tugas-tambahan.row-slide-in {
        animation: rowSlideInTambahan 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

    .badge-satuan-pop {
        transform: scale(1.08);
        background-color: #eff6ff !important;
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
        transform: scale(0.97);
    }

    .btn-hapus-baris {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .date-nav-btn:focus-visible,
    .date-today-btn:focus-visible,
    .btn-hapus-baris:focus-visible,
    .btn-bukti-link:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
    }

    /* Proof Link Button */
    .btn-bukti-link {
        height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-bukti-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }

    /* Flatpickr Custom Styling */
    .flatpickr-calendar {
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12) !important;
        overflow: hidden;
        animation: calendarPopIn 0.24s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .flatpickr-rContainer,
    .flatpickr-days,
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
    }
    @keyframes calendarPopIn {
        from {
            opacity: 0;
            transform: scale(0.97) translateY(-8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .flatpickr-months {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 4px 0;
    }
    .flatpickr-current-month {
        font-weight: 700;
        font-size: 0.875rem;
    }
    .flatpickr-weekday {
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        color: #64748b !important;
    }
    .flatpickr-day,
    span.flatpickr-day,
    span.flatpickr-day.prevMonthDay,
    span.flatpickr-day.nextMonthDay {
        border-radius: 8px !important;
        border: 1px solid transparent !important;
        box-sizing: border-box !important;
        position: relative !important;
        font-weight: 500;
        width: 14.2857143% !important;
        flex: 0 0 14.2857143% !important;
        max-width: 14.2857143% !important;
        height: 40px;
        line-height: 36px;
        margin: 0 !important;
        transition: background-color 0.15s ease, transform 0.15s ease;
    }
    .flatpickr-day:hover {
        background-color: #eff6ff !important;
        transform: scale(1.04);
    }
    .flatpickr-day.selected,
    span.flatpickr-day.selected {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 700;
        transform: scale(1.04);
    }
    .flatpickr-day.today,
    span.flatpickr-day.today:not(.selected) {
        border-color: var(--bs-primary-border-subtle) !important;
    }
    .flatpickr-day .fp-dot {
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        pointer-events: none;
    }
    .flatpickr-day.selected .fp-dot {
        background-color: #ffffff !important;
    }
    .fp-dot-sent {
        background-color: #10b981;
    }
    .fp-dot-draft {
        background-color: #f59e0b;
    }
    .fp-dot-missing {
        background-color: #ef4444;
    }
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
        transform: none !important;
        background-color: transparent !important;
        cursor: not-allowed !important;
    }
    .flatpickr-day.is-holiday:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay),
    .flatpickr-day.is-weekend:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay) {
        color: #ef4444 !important;
        font-weight: 700;
        opacity: 1;
    }
    .flatpickr-day.flatpickr-disabled.is-holiday,
    .flatpickr-day.flatpickr-disabled.is-weekend,
    .flatpickr-day.flatpickr-disabled.is-holiday:hover,
    .flatpickr-day.flatpickr-disabled.is-weekend:hover,
    .flatpickr-day.notAllowed.is-holiday,
    .flatpickr-day.notAllowed.is-weekend {
        color: #fca5a5 !important;
        opacity: 0.35 !important;
        font-weight: 400 !important;
        cursor: not-allowed !important;
        background: transparent !important;
        transform: none !important;
    }
    .flatpickr-day.is-holiday.prevMonthDay,
    .flatpickr-day.is-holiday.nextMonthDay,
    .flatpickr-day.is-weekend.prevMonthDay,
    .flatpickr-day.is-weekend.nextMonthDay {
        color: #fca5a5 !important;
        opacity: 0.3 !important;
        font-weight: 400 !important;
    }
    .flatpickr-day.selected.is-holiday,
    .flatpickr-day.selected.is-weekend {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #ffffff !important;
        opacity: 1 !important;
        font-weight: 700;
    }
    .flatpickr-weekday:nth-child(6),
    .flatpickr-weekday:nth-child(7) {
        color: #ef4444 !important;
        font-weight: 800 !important;
    }

    /* Date Selector Toolbar (8-Point Grid) */
    .date-nav-btn {
        width: 36px;
        height: 36px;
        min-width: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        border: 1px solid var(--bs-primary-border-subtle) !important;
        background-color: #ffffff !important;
        color: var(--bs-primary-text-emphasis) !important;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }
    .date-nav-btn:hover:not(.disabled):not(:disabled) {
        background-color: #eff6ff !important;
        border-color: var(--bs-primary) !important;
        color: var(--bs-primary) !important;
        transform: translateY(-1px);
    }
    .date-nav-btn:disabled {
        cursor: not-allowed;
    }
    .date-selector-wrapper {
        width: 280px;
        max-width: 100%;
    }
    .date-selector-box {
        height: 36px;
        border-radius: 8px !important;
        border: 1px solid var(--bs-primary-border-subtle) !important;
        background-color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
    }
    .date-selector-box:hover {
        background-color: #eff6ff !important;
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15) !important;
    }
    .date-selector-box:active {
        transform: scale(0.985);
    }
    .date-btn-input {
        background-color: transparent !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        cursor: pointer !important;
        padding-left: 8px !important;
        padding-right: 4px !important;
        height: 100% !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .date-today-btn {
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px !important;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0 12px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .legend-status-pill {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 50rem;
        min-height: 32px;
        padding: 4px 12px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        white-space: nowrap;
    }
    .legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .legend-dot-success {
        background-color: #10b981;
    }
    .legend-dot-warning {
        background-color: #f59e0b;
    }
    .legend-dot-danger {
        background-color: #ef4444;
    }

    /* Mobile Card Stack (< 768px) */
    @media (max-width: 767.98px) {
        .form-control,
        .form-select {
            font-size: var(--bs-body-font-size, 1rem) !important;
        }
        .date-nav-btn,
        .date-selector-box,
        .date-today-btn,
        .btn-hapus-baris,
        .btn-bukti-link,
        .btn-action-container .btn {
            min-height: 44px;
        }
        .btn-hapus-baris {
            min-width: 44px;
        }
        .capaian-input-group .input-capaian-num {
            flex: 0 0 80px;
            width: 80px;
            min-width: 80px;
            height: 44px;
            min-height: 44px;
        }
        .capaian-input-group .badge-capaian-satuan,
        .capaian-input-group .input-satuan-tambahan {
            height: 44px;
            min-height: 44px;
            font-size: var(--bs-body-font-size, 1rem) !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single.target-rhk-selection {
            min-height: 44px !important;
            padding: 12px 32px 12px 12px !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown.target-rhk-dropdown {
            min-width: 0;
            max-width: calc(100vw - 32px);
        }
        .date-nav-btn {
            width: 44px;
            min-width: 44px;
        }
        .date-selector-wrapper {
            flex: 1 1 200px;
            width: auto;
        }
        .tabel-log-wrapper {
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .tabel-log-bento thead {
            display: none;
        }
        .tabel-log-bento,
        .tabel-log-bento tbody,
        .tabel-log-bento tr,
        .tabel-log-bento td {
            display: block;
            width: 100% !important;
        }
        .tabel-log-bento #rowHeaderPokok td,
        .tabel-log-bento #rowHeaderTambahan td {
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 12px 16px;
        }
        .tabel-log-bento tr.row-tugas-pokok,
        .tabel-log-bento tr.row-tugas-tambahan {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .tabel-log-bento tr.row-tugas-pokok td,
        .tabel-log-bento tr.row-tugas-tambahan td {
            border: 0;
            padding: 8px 4px;
            text-align: left !important;
        }
        .tabel-log-bento tr.row-tugas-pokok td.td-no,
        .tabel-log-bento tr.row-tugas-tambahan td.td-no {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 4px;
        }
        .tabel-log-bento tr.row-tugas-pokok td.td-no::before,
        .tabel-log-bento tr.row-tugas-tambahan td.td-no::before {
            content: attr(data-label) " ";
            font-weight: 700;
            color: #475569;
            margin-right: 4px;
        }
        .tabel-log-bento td[data-label]:not(.td-no)::before {
            content: attr(data-label);
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .tabel-log-bento td.td-aksi {
            border-top: 1px solid #f1f5f9;
            margin-top: 4px;
            padding-top: 12px;
        }
        .btn-action-container {
            flex-direction: column-reverse;
            align-items: stretch !important;
        }
        .btn-action-container .btn {
            width: 100%;
        }
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
        .tabel-log-bento tbody tr {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$showAksiCol = !$is_locked;
$tableColspan = $showAksiCol ? 6 : 5;

$formatInputDecimal = static function ($val): string {
    if ($val === null || $val === '') {
        return '';
    }
    $numStr = trim((string) $val);
    if (strpos($numStr, '.') !== false) {
        $numStr = rtrim(rtrim($numStr, '0'), '.');
    }
    return $numStr === '' ? '0' : $numStr;
};

$formatDisplayDecimal = static function ($val): string {
    if ($val === null || $val === '') {
        return '0';
    }
    $formatted = str_replace('.', ',', trim((string) $val));
    if (strpos($formatted, ',') !== false) {
        $formatted = rtrim(rtrim($formatted, '0'), ',');
    }
    return $formatted === '' ? '0' : $formatted;
};

$prevDay        = date('Y-m-d', strtotime($tanggal_terpilih . ' -1 day'));
$nextDay        = date('Y-m-d', strtotime($tanggal_terpilih . ' +1 day'));
$today          = $today ?? date('Y-m-d');
$maxAllowedDate = $max_future_date ?? date('Y-m-t', strtotime($today));
?>

<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Lapor Kegiatan Harian</h1>
            <p class="ecc-text-muted-aa small mb-0">Catat realisasi aktivitas kerja harian beserta tautan bukti pekerjaan.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm py-2 px-3 small mb-3 rounded-3 bento-stagger bento-stagger-1" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
        <div class="card-body p-3 p-md-4">

            <!-- Filter Tanggal Toolbar -->
            <div class="mb-3 p-3 bg-light rounded-4 border border-light-subtle">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <label id="labelTanggalKegiatan" for="tanggalPicker" class="form-label fw-bold text-dark small mb-2 d-block">
                            Tanggal Kegiatan
                        </label>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="<?= site_url('log-kegiatan') ?>?tanggal=<?= $prevDay ?>" class="date-nav-btn btn-tactile" title="Hari Sebelumnya (<?= date('d/m/Y', strtotime($prevDay)) ?>)" aria-label="Hari Sebelumnya">
                                <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            </a>

                            <div class="date-selector-wrapper position-relative">
                                <div class="input-group input-group-sm rounded-3 overflow-hidden date-selector-box d-flex align-items-center px-1" id="dateSelectorGroup" title="Pilih tanggal kegiatan">
                                    <span class="input-group-text bg-primary text-white border-0 rounded-2 px-2 py-1">
                                        <i class="bi bi-calendar-date-fill" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" id="tanggalPicker" class="form-control border-0 fw-bold date-btn-input shadow-none h-100" value="<?= esc($tanggal_terpilih) ?>" placeholder="Pilih tanggal..." aria-label="Pilih Tanggal Kegiatan" readonly>
                                    <span class="input-group-text bg-transparent border-0 text-primary px-2 h-100">
                                        <i class="bi bi-chevron-down fw-bold" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>

                            <?php if ($nextDay <= $maxAllowedDate): ?>
                                <a href="<?= site_url('log-kegiatan') . '?tanggal=' . $nextDay ?>" class="date-nav-btn btn-tactile" title="Hari Berikutnya (<?= date('d/m/Y', strtotime($nextDay)) ?>)" aria-label="Hari Berikutnya">
                                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                </a>
                            <?php else: ?>
                                <button type="button" class="date-nav-btn opacity-50" disabled aria-disabled="true" tabindex="-1" title="Batas akhir tanggal tersedia" aria-label="Hari Berikutnya">
                                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                </button>
                            <?php endif; ?>

                            <?php if ($tanggal_terpilih !== $today && $today <= $maxAllowedDate): ?>
                                <a href="<?= site_url('log-kegiatan') ?>?tanggal=<?= $today ?>" class="btn btn-sm btn-primary date-today-btn btn-tactile text-nowrap ms-1" title="Kembali ke Hari Ini">
                                    Hari Ini
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-md-end">
                        <div class="legend-status-pill">
                            <span class="ecc-text-muted-aa fw-bold small">Indikator:</span>
                            <span class="legend-item text-success-emphasis" title="Laporan sudah dikirim">
                                <span class="legend-dot legend-dot-success"></span>
                                <span>Terkirim</span>
                            </span>
                            <span class="legend-item text-warning-emphasis" title="Laporan masih draf">
                                <span class="legend-dot legend-dot-warning"></span>
                                <span>Draf</span>
                            </span>
                            <span class="legend-item text-danger-emphasis" title="Hari kerja yang belum diisi">
                                <span class="legend-dot legend-dot-danger"></span>
                                <span>Belum Diisi</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SINGLE UNIFIED TOP ALERT NOTIFICATION -->
            <?php if ($is_locked): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-3 px-3 d-flex align-items-center gap-2 small rounded-4" role="alert">
                    <i class="bi bi-lock-fill text-warning-emphasis fs-5 flex-shrink-0" aria-hidden="true"></i>
                    <div>
                        <strong>Laporan Terkunci.</strong> <?= esc(!empty($lock_reason) ? $lock_reason : 'Laporan tanggal ini sudah dikirim dan tidak dapat diubah.') ?>
                    </div>
                </div>
            <?php elseif (!empty($is_future_date)): ?>
                <div class="alert alert-info border-info-subtle bg-info-subtle text-info-emphasis mb-3 shadow-sm py-3 px-3 small rounded-4" role="status">
                    <strong>Rencana Kegiatan.</strong> Simpan sebagai draf terlebih dahulu. Laporan baru dapat dikirim saat hari kegiatan tiba.
                </div>
            <?php elseif (isset($target_status) && $target_status === 'belum_ada'): ?>
                <div class="alert alert-danger mb-3 shadow-sm py-3 px-3 small rounded-4" role="alert">
                    <strong>Target Belum Ada.</strong> Buat target di menu <strong>Target Kinerja Bulanan</strong> terlebih dahulu.
                </div>
            <?php elseif (isset($target_status) && $target_status === 'belum_disetujui'): ?>
                <div class="alert alert-warning mb-3 shadow-sm py-3 px-3 small rounded-4" role="alert">
                    <strong>Target Belum Disetujui.</strong> Menunggu persetujuan atasan sebelum dapat mengisi laporan.
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

                <div class="tabel-log-wrapper mb-3 border rounded-4 shadow-sm bg-white overflow-hidden">
                    <table class="table table-bordered table-hover mb-0 tabel-log-bento <?= $is_locked ? 'is-locked-table' : '' ?>" id="tabelLog">
                        <thead>
                            <tr>
                                <th scope="col" class="col-no-log text-center">No</th>
                                <th scope="col" class="col-target-log">Indikator RHK / Tugas</th>
                                <th scope="col" class="col-deskripsi-log">Deskripsi Kegiatan</th>
                                <th scope="col" class="col-capaian-log text-center">Capaian</th>
                                <th scope="col" class="col-bukti-log">Bukti Pekerjaan</th>
                                <?php if ($showAksiCol): ?>
                                    <th scope="col" class="col-aksi-log text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ==================== BAGIAN A: TUGAS POKOK (RHK) ==================== -->
                            <tr class="table-primary text-primary-emphasis fw-bold" id="rowHeaderPokok">
                                <td colspan="<?= $tableColspan ?>" class="py-3 px-3 bg-primary-subtle text-primary-emphasis border-primary-subtle">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 header-section-wrapper">
                                        <span class="fs-6 fw-bold">A. Tugas Pokok (RHK)</span>
                                        <?php if (!$is_locked): ?>
                                            <button type="button" id="tambahBaris" class="btn btn-sm btn-primary btn-tactile rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                                + Tambah Kegiatan
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- BARIS TUGAS POKOK -->
                            <?php $rowIndex = 1; ?>
                            <?php if (!empty($rekap_data)): ?>
                                <?php foreach ($rekap_data as $row): ?>
                                    <?php $currentNum = $rowIndex++; ?>
                                    <?php $isPokokRowLocked = ($is_locked || (isset($row['status']) && $row['status'] === 'terkirim')); ?>
                                    <tr class="row-tugas-pokok">
                                        <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                                            <span class="nomor-baris"><?= $currentNum ?></span>
                                            <?php if (!$isPokokRowLocked): ?>
                                                <input type="hidden" name="log_id[]" value="<?= esc($row['id']) ?>">
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isPokokRowLocked): ?>
                                            <!-- Read-only View -->
                                            <td data-label="Indikator RHK / Tugas">
                                                <?php
                                                $targetName = '-';
                                                if (!empty($row['indikator_kinerja'])) {
                                                    $targetName = esc($row['indikator_kinerja']) . (!empty($row['satuan']) ? ' (' . (!empty($row['target_bulanan']) ? $formatDisplayDecimal($row['target_bulanan']) . ' ' : '') . esc($row['satuan']) . ')' : '');
                                                } else {
                                                    foreach ($daftar_target as $target) {
                                                        if ($target['id'] == $row['target_id']) {
                                                            $targetName = esc($target['indikator_kinerja']) . ' (' . $formatDisplayDecimal($target['target_bulanan']) . ' ' . esc($target['satuan']) . ')';
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <input type="hidden" class="target-select-hidden" value="<?= esc($row['target_id']) ?>">
                                                <div class="readonly-box text-dark fw-semibold"><?= $targetName ?></div>
                                            </td>
                                            <td data-label="Deskripsi Kegiatan">
                                                <div class="readonly-box text-body-secondary"><?= nl2br(esc($row['deskripsi_kegiatan'])) ?></div>
                                            </td>
                                            <td class="col-capaian-log" data-label="Capaian">
                                                <div class="readonly-box text-center fw-bold text-primary-emphasis num-tabular readonly-capaian-box" title="<?= $formatDisplayDecimal($row['jumlah_capaian']) ?> <?= esc($row['satuan'] ?? '') ?>">
                                                    <?= $formatDisplayDecimal($row['jumlah_capaian']) ?> <?= esc($row['satuan'] ?? '') ?>
                                                </div>
                                            </td>
                                            <td data-label="Bukti Pekerjaan">
                                                <?php
                                                $safeLink = !empty($row['link_bukti']) && preg_match('#^https?://#i', trim($row['link_bukti'])) ? trim($row['link_bukti']) : null;
                                                ?>
                                                <?php if ($safeLink): ?>
                                                    <div class="text-center">
                                                        <a href="<?= esc($safeLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary border btn-bukti-link btn-tactile" title="Buka tautan bukti pekerjaan">
                                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Bukti
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="readonly-box text-center ecc-text-muted-aa">-</div>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($showAksiCol): ?>
                                                <td class="td-aksi text-center">
                                                    <i class="bi bi-lock-fill text-warning-emphasis fs-6" title="Terkunci" aria-hidden="true"></i>
                                                </td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- Editable Draft View -->
                                            <td data-label="Indikator RHK / Tugas">
                                                <select name="target_id[]" class="form-select form-select-sm target-rhk-select" aria-label="Pilih Target RHK baris <?= $currentNum ?>">
                                                    <option value="">-- Pilih Target / RHK --</option>
                                                    <?php
                                                    $foundSelected = false;
                                                    foreach ($daftar_target as $target):
                                                        $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                                        $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                                        $labelTarget = $cleanIndikator . ' (' . $formatDisplayDecimal($target['target_bulanan']) . ' ' . $cleanSatuan . ')';
                                                        $selected = ($target['id'] == $row['target_id']) ? 'selected' : '';
                                                        if ($selected) {
                                                            $foundSelected = true;
                                                        }
                                                    ?>
                                                        <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>" <?= $selected ?>><?= $labelTarget ?></option>
                                                    <?php endforeach; ?>
                                                    <?php if (!$foundSelected && !empty($row['target_id']) && !empty($row['indikator_kinerja'])): ?>
                                                        <?php $fallbackLabel = esc($row['indikator_kinerja']) . ' (' . esc($row['satuan'] ?? '') . ')'; ?>
                                                        <option value="<?= esc($row['target_id']) ?>" data-satuan="<?= esc($row['satuan'] ?? '') ?>" selected><?= $fallbackLabel ?></option>
                                                    <?php endif; ?>
                                                </select>
                                            </td>
                                            <td data-label="Deskripsi Kegiatan">
                                                <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok baris <?= $currentNum ?>"><?= esc($row['deskripsi_kegiatan']) ?></textarea>
                                            </td>
                                            <td class="col-capaian-log" data-label="Capaian">
                                                <div class="capaian-input-group">
                                                    <input type="number" step="0.0001" min="0.0001" max="999999999999999" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" value="<?= esc($formatInputDecimal($row['jumlah_capaian'])) ?>" title="<?= $formatDisplayDecimal($row['jumlah_capaian']) ?>" aria-label="Jumlah Capaian Pokok baris <?= $currentNum ?>">
                                                    <span class="badge-capaian-satuan" title="<?= esc($row['satuan'] ?? '-') ?>"><?= esc($row['satuan'] ?? '-') ?></span>
                                                </div>
                                            </td>
                                            <td data-label="Bukti Pekerjaan">
                                                <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($row['link_bukti']) ?>" title="<?= esc($row['link_bukti']) ?>" aria-label="Tautan Bukti Pekerjaan Pokok baris <?= $currentNum ?>">
                                            </td>
                                            <?php if ($showAksiCol): ?>
                                                <td class="td-aksi text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile btn-hapus-baris" data-id="<?= esc($row['id']) ?>" title="Hapus baris" aria-label="Hapus kegiatan pokok baris <?= $currentNum ?>">
                                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!$is_locked && empty($rekap_data)): ?>
                                <!-- Baris Input Baru Default Tugas Pokok -->
                                <tr class="row-tugas-pokok input-row">
                                    <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                                        <span class="nomor-baris">1</span>
                                        <input type="hidden" name="log_id[]" value="">
                                    </td>
                                    <td data-label="Indikator RHK / Tugas">
                                        <select name="target_id[]" class="form-select form-select-sm target-rhk-select" aria-label="Pilih Target RHK baris 1">
                                            <option value="">-- Pilih Target / RHK --</option>
                                            <?php foreach ($daftar_target as $target): ?>
                                                <?php
                                                $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                                $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                                $labelTarget = $cleanIndikator . ' (' . $formatDisplayDecimal($target['target_bulanan']) . ' ' . $cleanSatuan . ')';
                                                ?>
                                                <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td data-label="Deskripsi Kegiatan">
                                        <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok baris 1"></textarea>
                                    </td>
                                    <td class="col-capaian-log" data-label="Capaian">
                                        <div class="capaian-input-group">
                                            <input type="number" step="0.0001" min="0.0001" max="999999999999999" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" aria-label="Jumlah Capaian Pokok baris 1">
                                            <span class="badge-capaian-satuan" title="-">-</span>
                                        </div>
                                    </td>
                                    <td data-label="Bukti Pekerjaan">
                                        <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Pokok baris 1">
                                    </td>
                                    <td class="td-aksi text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile btn-hapus-baris" title="Hapus baris" aria-label="Hapus kegiatan pokok baris 1">
                                            <i class="bi bi-trash3" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <!-- ==================== BAGIAN B: TUGAS TAMBAHAN ==================== -->
                            <tr class="table-light text-dark fw-bold" id="rowHeaderTambahan">
                                <td colspan="<?= $tableColspan ?>" class="py-3 px-3 bg-light border-top border-bottom border-secondary-subtle">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 header-section-wrapper">
                                        <span class="text-dark fs-6 fw-bold">B. Tugas Tambahan</span>
                                        <?php if (!$is_locked): ?>
                                            <button type="button" id="tambahBarisTambahan" class="btn btn-sm btn-outline-success btn-tactile rounded-pill px-3 py-1 shadow-sm fw-semibold">
                                                + Tambah Tugas
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- BARIS TUGAS TAMBAHAN -->
                            <?php $rowTmbIndex = 1; ?>
                            <?php if (!empty($rekap_data_tambahan)): ?>
                                <?php foreach ($rekap_data_tambahan as $rowTmb): ?>
                                    <?php $currentTmbNum = $rowTmbIndex++; ?>
                                    <?php $isTmbRowLocked = ($is_locked || (isset($rowTmb['status']) && $rowTmb['status'] === 'terkirim')); ?>
                                    <tr class="row-tugas-tambahan">
                                        <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                                            <span class="nomor-baris-tmb"><?= $currentTmbNum ?></span>
                                            <?php if (!$isTmbRowLocked): ?>
                                                <input type="hidden" name="log_tambahan_id[]" value="<?= esc($rowTmb['id']) ?>">
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isTmbRowLocked): ?>
                                            <!-- Read-only View -->
                                            <td data-label="Kategori">
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                                                    Tugas Tambahan
                                                </span>
                                            </td>
                                            <td data-label="Deskripsi Kegiatan">
                                                <div class="readonly-box text-body-secondary"><?= nl2br(esc($rowTmb['deskripsi_kegiatan'])) ?></div>
                                            </td>
                                            <td class="col-capaian-log" data-label="Capaian">
                                                <?php $hasCapaianTmb = isset($rowTmb['jumlah_capaian']) && $rowTmb['jumlah_capaian'] !== null && $rowTmb['jumlah_capaian'] !== ''; ?>
                                                <div class="readonly-box text-center fw-bold text-success-emphasis num-tabular readonly-capaian-box" title="<?= $hasCapaianTmb ? $formatDisplayDecimal($rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>">
                                                    <?= $hasCapaianTmb ? $formatDisplayDecimal($rowTmb['jumlah_capaian']) : '-' ?> <?= esc($rowTmb['satuan'] ?? '') ?>
                                                </div>
                                            </td>
                                            <td data-label="Bukti Pekerjaan">
                                                <?php
                                                $safeLinkTmb = !empty($rowTmb['link_bukti']) && preg_match('#^https?://#i', trim($rowTmb['link_bukti'])) ? trim($rowTmb['link_bukti']) : null;
                                                ?>
                                                <?php if ($safeLinkTmb): ?>
                                                    <div class="text-center">
                                                        <a href="<?= esc($safeLinkTmb) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light text-primary border btn-bukti-link btn-tactile" title="Buka tautan bukti pekerjaan">
                                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Bukti
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="readonly-box text-center ecc-text-muted-aa">-</div>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($showAksiCol): ?>
                                                <td class="td-aksi text-center">
                                                    <i class="bi bi-lock-fill text-warning-emphasis fs-6" title="Terkunci" aria-hidden="true"></i>
                                                </td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- Editable Draft View -->
                                            <td data-label="Kategori">
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                                                    Tugas Tambahan
                                                </span>
                                            </td>
                                            <td data-label="Deskripsi Kegiatan">
                                                <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Jelaskan tugas tambahan Anda..." aria-label="Deskripsi Kegiatan Tambahan baris <?= $currentTmbNum ?>"><?= esc($rowTmb['deskripsi_kegiatan']) ?></textarea>
                                            </td>
                                            <td class="col-capaian-log" data-label="Capaian">
                                                <div class="capaian-input-group">
                                                    <input type="number" step="0.0001" min="0.0001" max="999999999999999" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success-emphasis" placeholder="0" value="<?= isset($rowTmb['jumlah_capaian']) ? esc($formatInputDecimal($rowTmb['jumlah_capaian'])) : '' ?>" title="<?= isset($rowTmb['jumlah_capaian']) ? $formatDisplayDecimal($rowTmb['jumlah_capaian']) : '' ?>" aria-label="Jumlah Capaian Tambahan baris <?= $currentTmbNum ?>">
                                                    <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val input-satuan-tambahan text-center" placeholder="Satuan" list="daftarSatuanStandar" value="<?= esc($rowTmb['satuan'] ?? '') ?>" title="<?= esc($rowTmb['satuan'] ?? '') ?>" aria-label="Satuan Capaian Tambahan baris <?= $currentTmbNum ?>">
                                                </div>
                                            </td>
                                            <td data-label="Bukti Pekerjaan">
                                                <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://..." value="<?= esc($rowTmb['link_bukti']) ?>" title="<?= esc($rowTmb['link_bukti']) ?>" aria-label="Tautan Bukti Pekerjaan Tambahan baris <?= $currentTmbNum ?>">
                                            </td>
                                            <?php if ($showAksiCol): ?>
                                                <td class="td-aksi text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb btn-tactile btn-hapus-baris" data-id="<?= esc($rowTmb['id']) ?>" title="Hapus baris" aria-label="Hapus tugas tambahan baris <?= $currentTmbNum ?>">
                                                        <i class="bi bi-trash3" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
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
                        <button type="button" id="btnSimpanSementara" class="btn <?= empty($is_future_date) ? 'btn-outline-primary fw-semibold' : 'btn-primary fw-bold' ?> btn-tactile rounded-pill shadow-sm px-4 py-2">
                            Simpan Draf
                        </button>
                        <?php if (empty($is_future_date)): ?>
                            <button type="submit" class="btn btn-primary btn-tactile rounded-pill shadow-sm px-4 py-2 fw-bold">
                                Kirim Laporan
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Template DOM HTML untuk Baris Baru -->
            <template id="templateRowPokok">
                <tr class="row-tugas-pokok input-row row-slide-in">
                    <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                        <span class="nomor-baris">1</span>
                        <input type="hidden" name="log_id[]" value="">
                    </td>
                    <td data-label="Indikator RHK / Tugas">
                        <select name="target_id[]" class="form-select form-select-sm target-rhk-select" aria-label="Pilih Target RHK">
                            <option value="">-- Pilih Target / RHK --</option>
                            <?php if (!empty($daftar_target)): ?>
                                <?php foreach ($daftar_target as $target): ?>
                                    <?php
                                    $cleanIndikator = str_replace('`', '', esc($target['indikator_kinerja']));
                                    $cleanSatuan = str_replace('`', '', esc($target['satuan']));
                                    $labelTarget = $cleanIndikator . ' (' . $formatDisplayDecimal($target['target_bulanan']) . ' ' . $cleanSatuan . ')';
                                    ?>
                                    <option value="<?= esc($target['id']) ?>" data-satuan="<?= $cleanSatuan ?>"><?= $labelTarget ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td data-label="Deskripsi Kegiatan">
                        <textarea name="deskripsi_kegiatan[]" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Jelaskan apa yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Pokok"></textarea>
                    </td>
                    <td class="col-capaian-log" data-label="Capaian">
                        <div class="capaian-input-group">
                            <input type="number" step="0.0001" min="0.0001" max="999999999999999" name="jumlah_capaian[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-primary" placeholder="0" aria-label="Jumlah Capaian Pokok">
                            <span class="badge-capaian-satuan" title="-">-</span>
                        </div>
                    </td>
                    <td data-label="Bukti Pekerjaan">
                        <input type="url" name="link_bukti[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Pokok">
                    </td>
                    <td class="td-aksi text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris btn-tactile btn-hapus-baris" title="Hapus baris" aria-label="Hapus kegiatan pokok">
                            <i class="bi bi-trash3" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            </template>

            <template id="templateRowTambahan">
                <tr class="row-tugas-tambahan row-slide-in">
                    <td class="td-no text-center fw-bold ecc-text-muted-aa" data-label="Baris">
                        <span class="nomor-baris-tmb">1</span>
                        <input type="hidden" name="log_tambahan_id[]" value="">
                    </td>
                    <td data-label="Kategori">
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                            Tugas Tambahan
                        </span>
                    </td>
                    <td data-label="Deskripsi Kegiatan">
                        <textarea name="deskripsi_kegiatan_tambahan[]" class="form-control form-control-sm auto-grow-textarea" rows="2" placeholder="Jelaskan tugas tambahan yang Anda kerjakan hari ini..." aria-label="Deskripsi Kegiatan Tambahan"></textarea>
                    </td>
                    <td class="col-capaian-log" data-label="Capaian">
                        <div class="capaian-input-group">
                            <input type="number" step="0.0001" min="0.0001" max="999999999999999" name="jumlah_capaian_tambahan[]" class="form-control input-capaian-num input-target-val text-center num-tabular fw-bold text-success-emphasis" placeholder="0" aria-label="Jumlah Capaian Tambahan">
                            <input type="text" name="satuan_tambahan[]" class="form-control input-satuan-val input-satuan-tambahan text-center" placeholder="Satuan" list="daftarSatuanStandar" aria-label="Satuan Capaian Tambahan">
                        </div>
                    </td>
                    <td data-label="Bukti Pekerjaan">
                        <input type="url" name="link_bukti_tambahan[]" class="form-control form-control-sm" placeholder="https://..." aria-label="Tautan Bukti Pekerjaan Tambahan">
                    </td>
                    <td class="td-aksi text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle hapus-baris-tmb btn-tactile btn-hapus-baris" title="Hapus" aria-label="Hapus tugas tambahan">
                            <i class="bi bi-trash3" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            </template>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/flatpickr/l10n/id.js') ?>"></script>
<script>
    $(document).ready(function() {
        const tabelLog = $('#tabelLog tbody');
        const idNumberFormatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 4 });

        const supportsFieldSizing = typeof CSS !== 'undefined' && typeof CSS.supports === 'function' && CSS.supports('field-sizing', 'content');

        function autoGrowTextarea(el) {
            if (supportsFieldSizing || !el) return;
            el.style.height = 'auto';
            el.style.height = Math.max(56, el.scrollHeight + 4) + 'px';
        }

        function resizeAllTextareas(container) {
            if (supportsFieldSizing) return;
            const textareas = $(container || document).find('textarea.auto-grow-textarea').get();
            if (!textareas.length) return;
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

        function initTargetRhkSelect(scope) {
            if (!$.fn.select2) return;
            $(scope || tabelLog).find('select.target-rhk-select').each(function() {
                const $sel = $(this);
                if ($sel.hasClass('select2-hidden-accessible')) {
                    return;
                }
                const ariaLabel = $sel.attr('aria-label') || 'Pilih Target RHK';
                $sel.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    minimumResultsForSearch: Infinity,
                    selectionCssClass: 'target-rhk-selection',
                    dropdownCssClass: 'target-rhk-dropdown'
                });
                $sel.next('.select2-container').find('.select2-selection--single').attr('aria-label', ariaLabel);
            });
        }

        function updateCapaianTooltip(inputEl) {
            const raw = ($(inputEl).val() || '').trim().replace(',', '.');
            const num = parseFloat(raw);
            if (!raw || isNaN(num)) {
                $(inputEl).removeAttr('title');
            } else {
                $(inputEl).attr('title', 'Terbaca: ' + idNumberFormatter.format(num));
            }
        }

        if (!supportsFieldSizing) {
            resizeAllTextareas(document);
            $(window).one('load', function() {
                resizeAllTextareas(document);
            });
            let resizeDebounceTimer = null;
            $(window).on('resize', function() {
                clearTimeout(resizeDebounceTimer);
                resizeDebounceTimer = setTimeout(function() {
                    resizeAllTextareas(document);
                }, 100);
            });

            tabelLog.on('input', 'textarea.auto-grow-textarea', function() {
                autoGrowTextarea(this);
            });
        }

        tabelLog.on('input change', 'input[type="url"]', function() {
            $(this).attr('title', $(this).val() || '');
        });

        tabelLog.on('input change', '.input-capaian-num', function() {
            updateCapaianTooltip(this);
        });
        tabelLog.find('.input-capaian-num').each(function() {
            updateCapaianTooltip(this);
        });

        // Cegah perubahan nilai angka saat pengguna scrolling halaman (passive listener)
        document.addEventListener('wheel', function() {
            const active = document.activeElement;
            if (active && active.tagName === 'INPUT' && active.type === 'number') {
                active.blur();
            }
        }, { passive: true });

        function updateRowNumbers() {
            tabelLog.find('tr.row-tugas-pokok').each(function(index) {
                const num = index + 1;
                $(this).find('.nomor-baris').text(num);
                const $targetSelect = $(this).find('select[name="target_id[]"]');
                $targetSelect.attr('aria-label', `Pilih Target RHK baris ${num}`);
                $targetSelect.next('.select2-container').find('.select2-selection--single').attr('aria-label', `Pilih Target RHK baris ${num}`);
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
            tabelLog.find('select[name="target_id[]"]').each(function() {
                $(this).find('option').prop('disabled', false).show();
            });
        }

        function setRowControlsDisabled(disabled) {
            $('#tambahBaris, #tambahBarisTambahan').prop('disabled', disabled);
            tabelLog.find('.hapus-baris, .hapus-baris-tmb').prop('disabled', disabled);
            $('#formLog button[type="submit"]').prop('disabled', disabled);
        }

        // Pulihkan tombol jika pengguna kembali lewat tombol Back browser
        window.addEventListener('pageshow', function() {
            const $submitBtn = $('#formLog button[type="submit"]');
            if ($submitBtn.length) {
                $submitBtn.prop('disabled', false).text('Kirim Laporan');
            }
            const $draftBtn = $('#btnSimpanSementara');
            if ($draftBtn.length) {
                $draftBtn.prop('disabled', false).text('Simpan Draf');
            }
            setRowControlsDisabled(false);
        });

        // Tambah Baris Tugas Pokok (selalu instansiasi bersih dari template agar Select2 tidak terduplikasi)
        $('#tambahBaris').on('click', function() {
            const tpl = document.getElementById('templateRowPokok');
            if (!tpl) return;
            const newRow = $(tpl.content.cloneNode(true)).find('tr');

            $('#rowHeaderTambahan').before(newRow);
            initTargetRhkSelect(newRow);
            updateRowNumbers();
            updateDropdownOptions();
            resizeAllTextareas(newRow);

            setTimeout(() => {
                const $s2Trigger = newRow.find('.select2-selection--single');
                if ($s2Trigger.length) {
                    $s2Trigger.trigger('focus');
                } else {
                    newRow.find('select[name="target_id[]"]').trigger('focus');
                }
            }, 80);
        });

        // Hapus Baris Tugas Pokok
        tabelLog.on('click', '.hapus-baris', function() {
            const pokokRows = tabelLog.find('tr.row-tugas-pokok');
            const idLog = $(this).attr('data-id');
            const row = $(this).closest('tr');

            const targetVal = row.find('select[name="target_id[]"]').val();
            const deskripsiVal = row.find('textarea[name="deskripsi_kegiatan[]"]').val();
            const isRowEmpty = !targetVal && (!deskripsiVal || !deskripsiVal.trim());
            const hasTambahan = tabelLog.find('tr.row-tugas-tambahan').length > 0;
            const canDelete = pokokRows.length > 1 || isRowEmpty || hasTambahan;

            const animateRemoveRow = function() {
                row.addClass('row-slide-out');
                setTimeout(() => {
                    if ($.fn.select2) {
                        row.find('select.select2-hidden-accessible').select2('destroy');
                    }
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
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Terhapus',
                                            text: 'Kegiatan berhasil dihapus.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        alert('Kegiatan berhasil dihapus.');
                                    }
                                } else {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire('Gagal', response.message || 'Gagal menghapus.', 'error');
                                    } else {
                                        alert('Gagal: ' + (response.message || 'Gagal menghapus.'));
                                    }
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
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Hapus Kegiatan?',
                            text: 'Kegiatan ini akan dihapus.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                doDeletePokok();
                            }
                        });
                    } else if (confirm('Kegiatan ini akan dihapus.')) {
                        doDeletePokok();
                    }
                } else {
                    animateRemoveRow();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Laporan Kosong', 'Isi minimal 1 kegiatan pokok atau tugas tambahan.', 'info');
                } else {
                    alert('Isi minimal 1 kegiatan pokok atau tugas tambahan.');
                }
            }
        });

        // Tambah Baris Tugas Tambahan
        $('#tambahBarisTambahan').on('click', function() {
            const templateRowTmb = tabelLog.find('tr.row-tugas-tambahan:first');
            let newRow;

            if (templateRowTmb.length > 0 && templateRowTmb.find('textarea[name="deskripsi_kegiatan_tambahan[]"]').length > 0) {
                newRow = templateRowTmb.clone();
                newRow.removeClass('row-slide-out').addClass('row-slide-in');
                newRow.find('input[name="log_tambahan_id[]"]').val('');
                newRow.find('input[type="number"]').val('').removeAttr('title');
                newRow.find('input[name="satuan_tambahan[]"]').val('').attr('title', '');
                newRow.find('input[type="url"]').val('').removeAttr('title');
                newRow.find('textarea').val('').css('height', '');
                newRow.find('.hapus-baris-tmb').removeAttr('data-id');
                newRow.find('.is-invalid').removeClass('is-invalid');
            } else {
                const tplTmb = document.getElementById('templateRowTambahan');
                newRow = $(tplTmb.content.cloneNode(true)).find('tr');
            }
            tabelLog.append(newRow);
            updateRowNumbers();
            resizeAllTextareas(newRow);

            setTimeout(() => {
                $(newRow).find('textarea[name="deskripsi_kegiatan_tambahan[]"]').focus();
            }, 80);
        });

        $(document).on('input change', '.input-satuan-val', function() {
            $(this).attr('title', $(this).val());
        });

        // Hapus Baris Tugas Tambahan
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
                            if (response.success) {
                                animateRemoveTmb();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus',
                                        text: 'Tugas tambahan berhasil dihapus.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert('Tugas tambahan berhasil dihapus.');
                                }
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire('Gagal', response.message || 'Gagal menghapus.', 'error');
                                } else {
                                    alert('Gagal: ' + (response.message || 'Gagal menghapus.'));
                                }
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
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Tugas Tambahan?',
                        text: 'Tugas tambahan ini akan dihapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            doDeleteTambahan();
                        }
                    });
                } else if (confirm('Tugas tambahan ini akan dihapus.')) {
                    doDeleteTambahan();
                }
            } else {
                animateRemoveTmb();
            }
        });

        // Update satuan saat Target RHK berubah
        tabelLog.on('change', 'select[name="target_id[]"]', function() {
            const $sel = $(this);
            $sel.removeClass('is-invalid');
            const selectedOption = $sel.find('option:selected');
            let satuan = selectedOption.data('satuan') || selectedOption.attr('data-satuan');
            if (!satuan) {
                const selectedText = selectedOption.text();
                const matches = selectedText.match(/\((\d+(?:[\.,]\d+)?)\s+(.+)\)$/);
                satuan = matches ? matches[2] : '-';
            }
            const badge = $sel.closest('tr').find('.badge-capaian-satuan');
            badge.text(satuan || '-').attr('title', satuan || '-').addClass('badge-satuan-pop');
            setTimeout(() => {
                badge.removeClass('badge-satuan-pop');
            }, 200);
            updateDropdownOptions();
        });

        initTargetRhkSelect(tabelLog);
        updateDropdownOptions();

        function checkUpperBounds() {
            let valid = true;
            tabelLog.find('input.input-capaian-num').each(function() {
                const raw = ($(this).val() || '').trim().replace(',', '.');
                if (raw === '') return;
                const val = parseFloat(raw);
                if (!isNaN(val) && val >= 1000000000000000) {
                    $(this).addClass('is-invalid');
                    valid = false;
                }
            });
            return valid;
        }

        // Validasi Form saat klik "Kirim Laporan"
        $('#formLog').on('submit', function(e) {
            const isFutureDatePage = <?= !empty($is_future_date) ? 'true' : 'false' ?>;
            if (isFutureDatePage) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Rencana Kegiatan',
                        text: 'Rencana kegiatan hanya dapat disimpan sebagai draf.',
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'Saya Mengerti'
                    });
                } else {
                    alert('Rencana kegiatan hanya dapat disimpan sebagai draf.');
                }
                return false;
            }

            $('#formLog .is-invalid').removeClass('is-invalid');

            let isValid = true;
            let hasPokok = false;
            let hasTambahan = false;
            let errorHints = [];

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

                if (targetId || deskripsi || capaian !== '' || link) {
                    hasPokok = true;
                    let missingCols = [];

                    if (!targetId) {
                        targetElem.addClass('is-invalid');
                        missingCols.push('Target RHK');
                    }
                    if (!deskripsi) {
                        deskripsiElem.addClass('is-invalid');
                        missingCols.push('Deskripsi');
                    }
                    let capaianVal = parseFloat(capaian.replace(',', '.'));
                    if (capaian === '' || isNaN(capaianVal) || capaianVal <= 0) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push(capaianVal <= 0 ? 'Capaian (> 0)' : 'Capaian');
                    } else if (capaianVal >= 1000000000000000) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push('Capaian maksimal 999.999.999.999.999');
                    }
                    if (!link || link === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Tautan Bukti');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Pokok Baris ${rowNum}</b>: ${missingCols.join(', ')} belum sesuai.`);
                    }
                }
            });

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
                        missingCols.push('Deskripsi');
                    }
                    let capaianValTmb = parseFloat(capaianTmb.replace(',', '.'));
                    if (capaianTmb === '' || isNaN(capaianValTmb) || capaianValTmb <= 0) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push(capaianValTmb <= 0 ? 'Capaian (> 0)' : 'Capaian');
                    } else if (capaianValTmb >= 1000000000000000) {
                        capaianElem.addClass('is-invalid');
                        missingCols.push('Capaian maksimal 999.999.999.999.999');
                    }
                    if (!linkTmb || linkTmb === 'https://...') {
                        linkElem.addClass('is-invalid');
                        missingCols.push('Tautan Bukti');
                    }

                    if (missingCols.length > 0) {
                        isValid = false;
                        errorHints.push(`<b>Tugas Tambahan Baris ${rowNum}</b>: ${missingCols.join(', ')} belum sesuai.`);
                    }
                }
            });

            if (!hasPokok && !hasTambahan) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Laporan Kosong',
                        text: 'Isi minimal 1 kegiatan pokok atau tugas tambahan.'
                    });
                } else {
                    alert('Isi minimal 1 kegiatan pokok atau tugas tambahan.');
                }
                return false;
            }

            if (!isValid) {
                e.preventDefault();
                let hintHtml = `<div class="text-start small mt-2"><p class="mb-2 text-danger fw-bold">Periksa kembali kolom berikut:</p><ul class="ps-3 mb-0">` +
                    errorHints.map(h => `<li class="mb-1">${h}</li>`).join('') +
                    `</ul><p class="mt-2 ecc-text-muted-aa mb-0">Gunakan tombol <b>Simpan Draf</b> jika belum selesai.</p></div>`;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        html: hintHtml,
                        confirmButtonText: 'Saya Mengerti'
                    });
                } else {
                    alert('Data belum lengkap. Silakan periksa kolom yang ditandai.');
                }
                return false;
            }

            let submitBtn = $(this).find('button[type="submit"]');
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...').prop('disabled', true);
            $('#btnSimpanSementara').prop('disabled', true);
            setRowControlsDisabled(true);
        });

        // Single Unified Submit "Simpan Draf" (AJAX)
        $('#btnSimpanSementara').on('click', function() {
            $('#formLog .is-invalid').removeClass('is-invalid');
            if (!checkUpperBounds()) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Capaian Terlalu Besar', 'Nilai capaian maksimal adalah 999.999.999.999.999.', 'warning');
                } else {
                    alert('Nilai capaian maksimal adalah 999.999.999.999.999.');
                }
                return false;
            }

            let btn = $(this);
            let originalText = btn.html();

            setRowControlsDisabled(true);
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
                        btn.html('Tersimpan').removeClass('btn-outline-primary').addClass('btn-outline-success');

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

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan',
                                text: response.message || 'Draf berhasil disimpan.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(response.message || 'Draf berhasil disimpan.');
                        }

                        setTimeout(() => {
                            btn.html(originalText).removeClass('btn-outline-success').addClass('btn-outline-primary').prop('disabled', false);
                        }, 2500);
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Gagal', response.message || 'Gagal menyimpan.', 'error');
                        } else {
                            alert('Gagal: ' + (response.message || 'Gagal menyimpan.'));
                        }
                        btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Gagal', 'Terjadi kendala. Silakan coba lagi.', 'error');
                    } else {
                        alert('Terjadi kendala. Silakan coba lagi.');
                    }
                    btn.html(originalText).prop('disabled', false);
                },
                complete: function() {
                    setRowControlsDisabled(false);
                }
            });
        });

        // Flatpickr Datepicker with Colored Status Dots
        const dateStatusMap = <?= json_encode($date_status_map ?? []) ?>;
        const holidayMap = <?= json_encode($holiday_map ?? []) ?>;
        const todayStr = '<?= date('Y-m-d') ?>';

        if (typeof flatpickr !== 'undefined' && document.getElementById('tanggalPicker')) {
            const fpInstance = flatpickr('#tanggalPicker', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'l, j F Y',
                altInputClass: 'form-control border-0 fw-bold date-btn-input shadow-none',
                defaultDate: '<?= esc($tanggal_terpilih) ?>',
                maxDate: '<?= esc($max_future_date ?? date('Y-m-t', strtotime($today ?? date('Y-m-d')))) ?>',
                locale: 'id',
                disableMobile: true,
                onReady: function(selectedDates, dateStr, fp) {
                    if (fp && fp.altInput) {
                        fp.input.setAttribute('aria-hidden', 'true');
                        fp.altInput.setAttribute('id', 'tanggalPickerDisplay');
                        fp.altInput.setAttribute('aria-labelledby', 'labelTanggalKegiatan');
                        fp.altInput.setAttribute('aria-label', 'Pilih Tanggal Kegiatan');
                        const lbl = document.getElementById('labelTanggalKegiatan');
                        if (lbl) {
                            lbl.setAttribute('for', 'tanggalPickerDisplay');
                        }
                    }
                },
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dateStr = fp.formatDate(dayElem.dateObj, 'Y-m-d');
                    const dayOfWeek = dayElem.dateObj.getDay();
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
                    const baseAriaLabel = dayElem.getAttribute('aria-label') || dateStr;
                    if (status === 'terkirim') {
                        dayElem.classList.add('has-log-sent');
                        dayElem.setAttribute('aria-label', baseAriaLabel + ' — Laporan terkirim');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-sent" aria-hidden="true" title="Laporan Terkirim"></span>';
                    } else if (status === 'draft') {
                        dayElem.classList.add('has-log-draft');
                        dayElem.setAttribute('aria-label', baseAriaLabel + ' — Draf tersimpan');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-draft" aria-hidden="true" title="Draf Tersimpan"></span>';
                    } else if (isWorkingDay && isPastOrToday) {
                        dayElem.classList.add('has-log-missing');
                        dayElem.setAttribute('aria-label', baseAriaLabel + ' — Hari kerja belum diisi');
                        dayElem.innerHTML += '<span class="fp-dot fp-dot-missing" aria-hidden="true" title="Hari Kerja Belum Diisi"></span>';
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    if (dateStr && dateStr !== '<?= esc($tanggal_terpilih) ?>') {
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
<?= $this->endSection() ?>
