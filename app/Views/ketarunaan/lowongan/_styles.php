<style>
    .ecc-low-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-low-card {
            padding: 16px;
        }
    }
    .ecc-low-kpi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        height: 100%;
        display: block;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-low-kpi:hover {
        border-color: #94a3b8;
        color: inherit;
    }
    .ecc-low-kpi.active-kpi {
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
    .ecc-text-muted-aa {
        color: #475569 !important;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-btn-action {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .ecc-table-low thead th {
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
    .ecc-table-low tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-table-low-wrap {
        max-height: 480px;
        overflow-y: auto;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .low-sort-btn {
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
        text-decoration: none;
    }
    .low-sort-btn:hover {
        color: #0f172a;
    }
    .low-row-clickable {
        cursor: pointer;
        transition: background-color 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .low-row-clickable:hover {
        background-color: #f8fafc;
    }
    .low-row-clickable:focus-visible {
        outline: none;
        box-shadow: inset 0 0 0 2px #0d6efd;
    }
    .ecc-low-queue-item {
        display: block;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #1e293b;
        background: #ffffff;
        transition: border-color 0.18s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-low-queue-item:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }
    .ecc-low-queue-item.active {
        border-color: #1e40af;
        background: #eff6ff;
    }
    @media (max-width: 767.98px) {
        .ecc-tab-link,
        .ecc-control-sm,
        .ecc-btn-action,
        .low-touch-ctrl {
            min-height: 44px;
        }
        .low-filter-form .form-control,
        .low-filter-form .form-select,
        .low-form-mobile .form-control,
        .low-form-mobile .form-select {
            font-size: var(--bs-body-font-size, 1rem) !important;
            min-height: 44px;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-low-kpi,
        .ecc-tab-link,
        .low-row-clickable,
        .ecc-low-queue-item {
            transition: none;
        }
    }
</style>
