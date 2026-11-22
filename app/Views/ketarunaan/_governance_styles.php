<style>
    .ecc-gov-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }
    @media (max-width: 767.98px) {
        .ecc-gov-card {
            padding: 16px;
        }
    }
    .ecc-gov-kpi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        height: 100%;
        display: block;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-gov-kpi:hover {
        border-color: #94a3b8;
        color: inherit;
    }
    .ecc-gov-kpi.active-kpi {
        border-color: #1e40af;
        background-color: #eff6ff;
    }
    .ecc-tab-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 16px;
    }
    .ecc-tab-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        border-radius: 8px 8px 0 0;
        min-height: 40px;
        transition: color 0.18s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-tab-link:hover {
        color: #0f172a;
        background-color: #f8fafc;
    }
    .ecc-tab-link.active {
        color: #1e40af;
        border-bottom-color: #1e40af;
        background-color: #eff6ff;
    }
    .ecc-gov-tabbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .ecc-gov-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        transition: background-color 0.18s cubic-bezier(0.16, 1, 0.3, 1), color 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-gov-tab:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-gov-tab.active {
        background-color: #0d6efd;
        color: #ffffff;
    }
    .ecc-text-muted-aa {
        color: #475569 !important;
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
    .ecc-table-scroll {
        max-height: 480px;
        overflow: auto;
    }
    .ecc-table-gov thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .ecc-table-gov tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .gov-sort-btn {
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0;
        color: inherit;
        font: inherit;
        text-transform: inherit;
        letter-spacing: inherit;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .gov-sort-btn:hover {
        color: #0f172a;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    @media (max-width: 767.98px) {
        .ecc-tab-link,
        .ecc-control-sm,
        .ecc-btn-pill,
        .gov-touch-ctrl {
            min-height: 44px;
        }
        .gov-filter-form .form-control,
        .gov-filter-form .form-select {
            font-size: var(--bs-body-font-size, 1rem) !important;
            min-height: 44px;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-gov-kpi, .ecc-tab-link, .ecc-gov-tab, .btn-tactile {
            transition: none !important;
        }
    }
</style>
