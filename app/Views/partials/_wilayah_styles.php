<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<style>
    .select2-container--bootstrap-5 .select2-selection--single {
        min-height: 36px;
        border-radius: 8px;
        border-color: #cbd5e1;
        padding-top: 4px;
        padding-bottom: 4px;
        font-size: 0.875rem;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 10px;
        border-color: #cbd5e1;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }
    .select2-container--bootstrap-5 .select2-results__group {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e40af;
        background-color: #f8fafc;
        padding: 6px 12px;
    }
    @media (max-width: 767.98px) {
        .select2-container--bootstrap-5 .select2-selection--single {
            min-height: 44px;
            font-size: 16px;
            padding-top: 8px;
            padding-bottom: 8px;
        }
    }
</style>
