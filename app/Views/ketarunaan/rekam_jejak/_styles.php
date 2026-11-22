<style>
    .ecc-rj-card {
        padding: 24px;
    }
    .ecc-rj-scroll {
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
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
    }
    .ecc-metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .ecc-avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #1e40af;
        font-size: 0.875rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
    }
    .ecc-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #1e40af;
        font-size: 1.15rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
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
    .ecc-tab-link:hover {
        background-color: #f8fafc;
        color: #1e40af;
    }
    .ecc-tab-link.active {
        background-color: #eff6ff;
        color: #1e40af;
        border-bottom-color: #1e40af;
    }
    .ecc-status-tile {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        transition: border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        .ecc-status-tile:hover {
            border-color: #93c5fd;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }
    }
    .ecc-status-tile.is-active {
        border-color: #1e40af;
        background-color: #eff6ff;
    }
    .ecc-summary-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: border-color 0.18s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ecc-summary-box:hover {
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }
    .ecc-icon-40 {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
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
    .ecc-timeline-item {
        position: relative;
        padding-left: 24px;
        padding-bottom: 16px;
        border-left: 2px solid #e2e8f0;
    }
    .ecc-timeline-item:last-child {
        padding-bottom: 0;
    }
    .ecc-timeline-dot {
        position: absolute;
        left: -5px;
        top: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #1e40af;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        padding-bottom: 8px;
        margin-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    .ecc-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        background-color: #f8fafc;
    }
    .ecc-attr-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .ecc-attr-row:last-child {
        border-bottom: 0;
    }
    .ecc-attr-label {
        width: 160px;
        color: #475569;
        flex-shrink: 0;
    }
    .ecc-attr-value {
        color: #0f172a;
        font-weight: 500;
        min-width: 0;
        word-break: break-word;
    }
    .ecc-preview-surface {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        min-height: 176px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ecc-table-rj thead th {
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
    .ecc-rj-scroll .ecc-table-rj thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .ecc-table-rj tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .rj-sort-btn {
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
    .rj-sort-btn:hover,
    .rj-sort-btn:focus-visible,
    th.th-sorted .rj-sort-btn,
    .rj-sort-link:hover,
    .rj-sort-link.is-sorted {
        color: #1e40af;
    }
    .rj-sort-link {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    .rj-row-clickable {
        cursor: pointer;
    }
    .rj-row-clickable:focus-visible {
        outline: 2px solid #1e40af;
        outline-offset: -2px;
        background-color: #eff6ff;
    }
    @media (min-width: 768px) {
        .border-md-start {
            border-left: 1px solid #e2e8f0;
            padding-left: 16px;
        }
    }
    .rj-mobile-card {
        color: inherit;
        transition: border-color 0.16s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (hover: hover) {
        .rj-mobile-card:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        }
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .ecc-rj-card a:focus-visible,
    .ecc-rj-card button:focus-visible,
    .ecc-rj-card .form-control:focus-visible,
    .ecc-rj-card .form-select:focus-visible,
    .ecc-tab-link:focus-visible,
    .ecc-summary-box:focus-visible,
    .ecc-status-tile:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    @media (max-width: 767.98px) {
        .ecc-rj-card {
            padding: 16px;
        }
        .ecc-attr-row {
            flex-direction: column;
            gap: 4px;
        }
        .ecc-attr-label {
            width: 100%;
            font-size: 0.75rem;
        }
        .rj-touch-ctrl,
        .ecc-tab-link {
            min-height: 44px !important;
        }
        .rj-filter-form .form-control,
        .rj-filter-form .form-select,
        .rj-form-scope .form-control,
        .rj-form-scope .form-select,
        #modalKembalikanCapaian .form-control {
            font-size: 16px !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .ecc-tab-link,
        .ecc-summary-box,
        .ecc-status-tile,
        .rj-mobile-card {
            transition: none !important;
        }
    }
</style>
