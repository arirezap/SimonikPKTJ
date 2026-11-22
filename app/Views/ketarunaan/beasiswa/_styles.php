<style>
    .ecc-bea-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .ecc-bea-kpi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        height: 100%;
        display: block;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        a.ecc-bea-kpi:hover {
            border-color: #93c5fd;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
        }
    }
    .ecc-bea-kpi.is-active {
        border-color: #1e40af;
        background-color: #eff6ff;
    }
    .ecc-tab-nav {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .ecc-tab-link {
        min-height: 40px;
        padding: 8px 16px;
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
    .ecc-tab-link:hover {
        background-color: #f8fafc;
        color: #1e40af;
    }
    .ecc-tab-link.active {
        background-color: #eff6ff;
        color: #1e40af;
        border-bottom-color: #1e40af;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-table-bea-wrap {
        max-height: 480px;
        overflow: auto;
    }
    .ecc-table-bea thead th {
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
    .ecc-table-bea-wrap .ecc-table-bea thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .ecc-table-bea tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-table-bea tbody tr:last-child td {
        border-bottom: 0;
    }
    .bea-sort-link {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .bea-sort-btn {
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
    .bea-sort-link:hover,
    .bea-sort-link.is-sorted,
    .bea-sort-btn:hover,
    .bea-sort-btn:focus-visible,
    th.th-sorted .bea-sort-btn {
        color: #1e40af;
    }
    .bea-row-clickable {
        cursor: pointer;
    }
    .bea-row-clickable:focus-visible {
        outline: 2px solid #1e40af;
        outline-offset: -2px;
        background-color: #eff6ff;
    }
    .bea-mobile-card {
        color: inherit;
        transition: border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        .bea-mobile-card:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-text-muted-aa {
        color: #475569;
    }
    .ecc-step-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        height: 100%;
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
    .ecc-tie-row {
        background-color: #fffbeb !important;
    }
    .ecc-bea-card a:focus-visible,
    .ecc-bea-card button:focus-visible,
    .ecc-bea-card .form-control:focus-visible,
    .ecc-bea-card .form-select:focus-visible,
    .ecc-tab-link:focus-visible,
    .ecc-bea-kpi:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    @media (max-width: 767.98px) {
        .ecc-bea-card {
            padding: 16px;
        }
        .bea-touch-ctrl,
        .ecc-tab-link {
            min-height: 44px !important;
        }
        .bea-filter-form .form-control,
        .bea-filter-form .form-select,
        .bea-form-scope .form-control,
        .bea-form-scope .form-select,
        #modalImporIp .form-control,
        #modalImporIp .form-select {
            font-size: 1rem !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-bea-kpi,
        .ecc-tab-link,
        .bea-mobile-card,
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
