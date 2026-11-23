<style>
    .ecc-dsn-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-dsn-card {
            padding: 16px;
        }
    }
    .ecc-dsn-kpi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        height: 100%;
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
    .ecc-table-dsn-wrap {
        max-height: 480px;
        overflow-y: auto;
    }
    .ecc-table-dsn thead th {
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
    .ecc-table-dsn tbody td {
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
    .dsn-avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #1e40af;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    .dsn-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #1e40af;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        flex-shrink: 0;
        overflow: hidden;
    }
    .dsn-tag {
        display: inline-block;
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 2px 10px;
        font-size: 0.72rem;
        font-weight: 500;
    }
    .dsn-bar-track {
        height: 8px;
        border-radius: 999px;
        background-color: #e2e8f0;
        overflow: hidden;
    }
    .dsn-bar-fill {
        height: 100%;
        border-radius: 999px;
        background-color: #1e40af;
    }
    .dsn-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #475569;
        font-weight: 600;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
        white-space: nowrap;
    }
    .dsn-tabs .nav-link.active {
        color: #1e40af;
        border-bottom-color: #1e40af;
        background-color: #eff6ff;
    }
    .dsn-check-tile {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        background-color: #f8fafc;
        height: 100%;
    }
    @media (max-width: 767.98px) {
        .dsn-touch-ctrl {
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
