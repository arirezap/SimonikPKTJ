<style>
    .ecc-ts-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-ts-card {
            padding: 16px;
        }
    }
    .ecc-ts-kpi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        height: 100%;
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
        gap: 4px;
        white-space: nowrap;
    }
    .ecc-table-ts-wrap {
        max-height: 480px;
        overflow-y: auto;
    }
    .ecc-table-ts thead th {
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
        z-index: 2;
    }
    .ecc-table-ts tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ts-progress {
        height: 8px;
        border-radius: 999px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
    .ts-progress-bar {
        height: 100%;
        border-radius: 999px;
        background-color: #1e40af;
        transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ts-q-item {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background-color: #ffffff;
    }
    .ts-likert-option {
        min-width: 48px;
        min-height: 44px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        cursor: pointer;
    }
    @media (max-width: 767.98px) {
        .ts-touch-ctrl {
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-tab-link,
        .ts-progress-bar,
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
