<style>
    .ecc-pl-card {
        padding: 24px;
    }
    .ecc-pl-scroll {
        max-height: 480px;
        overflow: auto;
    }
    .ecc-metric-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
        transition: border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        a.ecc-metric-tile:hover {
            border-color: #93c5fd;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
        }
    }
    .ecc-metric-tile.is-active {
        border-color: #1e40af;
        background-color: #eff6ff;
    }
    .ecc-metric-icon,
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .ecc-icon-48 {
        background-color: #fef2f2;
        color: #dc2626;
    }
    .ecc-tab-nav,
    .ecc-wizard-nav {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .ecc-tab-link,
    .ecc-wizard-step {
        min-height: 40px;
        padding: 10px 16px;
        border-radius: 8px 8px 0 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        flex-shrink: 0;
        border: none;
        border-bottom: 2px solid transparent;
        transition: background-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), color 0.16s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-tab-link:hover,
    a.ecc-wizard-step:hover {
        background-color: #f8fafc;
        color: #1e40af;
    }
    .ecc-tab-link.active,
    .ecc-wizard-step.active {
        background-color: #eff6ff;
        color: #1e40af;
        border-bottom-color: #1e40af;
    }
    .ecc-wizard-step.disabled-step {
        color: #94a3b8;
        cursor: not-allowed;
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
    .ecc-lock-notice {
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        color: #475569;
        font-size: 0.875rem;
    }
    .ecc-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .ecc-meta-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
    .ecc-check-item,
    .ecc-file-item {
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
    .ecc-pl-scroll .ecc-table-pl thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .ecc-table-pl tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-table-pl tbody tr:last-child td {
        border-bottom: 0;
    }
    .pl-sort-link {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .pl-sort-btn {
        background: none;
        border: 0;
        padding: 0;
        color: inherit;
        font: inherit;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        white-space: nowrap;
    }
    .pl-sort-link:hover,
    .pl-sort-link.is-sorted,
    .pl-sort-btn:hover,
    .pl-sort-btn:focus-visible,
    th.th-sorted .pl-sort-btn {
        color: #1e40af;
    }
    .pl-row-clickable {
        cursor: pointer;
    }
    .pl-row-clickable:focus-visible {
        outline: 2px solid #1e40af;
        outline-offset: -2px;
        background-color: #eff6ff;
    }
    .pl-icon-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pl-mobile-card {
        color: inherit;
        transition: border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        .pl-mobile-card:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-pl-card a:focus-visible,
    .ecc-pl-card button:focus-visible,
    .ecc-pl-card .form-control:focus-visible,
    .ecc-pl-card .form-select:focus-visible,
    .ecc-tab-link:focus-visible,
    .ecc-wizard-step:focus-visible,
    .ecc-metric-tile:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    @media (max-width: 767.98px) {
        .ecc-pl-card {
            padding: 16px;
        }
        .ecc-file-item {
            flex-wrap: wrap;
        }
        .pl-touch-ctrl,
        .ecc-tab-link,
        .ecc-wizard-step {
            min-height: 44px !important;
        }
        .pl-icon-btn {
            width: 44px;
            height: 44px;
        }
        .pl-filter-form .form-control,
        .pl-filter-form .form-select,
        .pl-form-scope .form-control,
        .pl-form-scope .form-select,
        #modalTindakLanjut .form-control,
        #modalTindakLanjut .form-select,
        #modalKembalikanKasus .form-control {
            font-size: 1rem !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-metric-tile,
        .ecc-tab-link,
        .ecc-wizard-step,
        .pl-mobile-card,
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
