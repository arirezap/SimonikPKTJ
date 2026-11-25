<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Log Gangguan Aplikasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .log-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }
    .log-kpi-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
    }
    .log-kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        line-height: 1.25;
    }
    .log-msg-cell {
        font-size: 0.875rem;
        color: #0f172a;
        word-break: break-word;
    }
    .log-ref-badge {
        font-size: 0.75rem;
        font-weight: 600;
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 4px 8px;
        display: inline-block;
        font-variant-numeric: tabular-nums;
    }
    .log-detail-pre {
        max-height: 440px;
        overflow-y: auto;
        font-size: 0.75rem;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-word;
    }
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 1rem !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="h4 mb-0 fw-bold text-dark">Log Gangguan Aplikasi</h1>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 small" style="font-variant-numeric: tabular-nums;">
                <?= number_format((int) ($total_matched ?? 0), 0, ',', '.') ?> Entri Cocok
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?= site_url('settings') ?>#cardHousekeeping" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                <i class="bi bi-trash3 me-1" aria-hidden="true"></i> Kelola Retensi Log
            </a>
            <button type="button" id="btnRefreshAppLogs" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i> Segarkan
            </button>
        </div>
    </div>

    <ul class="nav nav-pills gap-2 mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-2 fw-semibold small bg-white border text-dark" href="<?= site_url('admin/audit-logs') ?>">
                Aktivitas Pengguna
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active rounded-pill px-3 py-2 fw-semibold small" href="<?= site_url('admin/audit-logs/app-errors') ?>" aria-current="page">
                Log Gangguan Aplikasi
            </a>
        </li>
    </ul>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="log-kpi-card">
                <div class="log-kpi-label mb-1">Kritis (Critical)</div>
                <div class="log-kpi-value text-danger"><?= number_format((int) ($counts['critical'] ?? 0), 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="log-kpi-card">
                <div class="log-kpi-label mb-1">Galat (Error)</div>
                <div class="log-kpi-value text-warning-emphasis"><?= number_format((int) ($counts['error'] ?? 0), 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="log-kpi-card">
                <div class="log-kpi-label mb-1">Peringatan (Warning)</div>
                <div class="log-kpi-value text-secondary"><?= number_format((int) ($counts['warning'] ?? 0), 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="log-kpi-card">
                <div class="log-kpi-label mb-1">Penyimpanan Log</div>
                <div class="log-kpi-value"><?= number_format((float) ($storage_stats['total_mb'] ?? 0), 2, ',', '.') ?> MB</div>
                <div class="small text-muted mt-1" style="font-variant-numeric: tabular-nums;">
                    <?= number_format((int) ($storage_stats['file_count'] ?? 0), 0, ',', '.') ?> berkas harian
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <form method="get" action="<?= site_url('admin/audit-logs/app-errors') ?>" id="appLogFilterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="filterLogDate" class="form-label text-muted small fw-semibold mb-1">Tanggal Berkas Log</label>
                        <select name="date" id="filterLogDate" class="form-select form-select-sm">
                            <?php if (empty($available_dates)): ?>
                                <option value="">Belum ada berkas log</option>
                            <?php else: ?>
                                <?php foreach ($available_dates as $d): ?>
                                    <option value="<?= esc($d) ?>" <?= ($selected_date === $d) ? 'selected' : '' ?>>
                                        <?= esc(date('d/m/Y', strtotime($d))) ?> (<?= esc($d) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="filterLogLevel" class="form-label text-muted small fw-semibold mb-1">Tingkat Gangguan</label>
                        <select name="level" id="filterLogLevel" class="form-select form-select-sm">
                            <option value="ERROR_CRITICAL" <?= ($filter_level === 'ERROR_CRITICAL') ? 'selected' : '' ?>>Kritis &amp; Error (Utama)</option>
                            <option value="CRITICAL" <?= ($filter_level === 'CRITICAL') ? 'selected' : '' ?>>Kritis Saja (HTTP 500)</option>
                            <option value="ERROR" <?= ($filter_level === 'ERROR') ? 'selected' : '' ?>>Error Saja</option>
                            <option value="WARNING" <?= ($filter_level === 'WARNING') ? 'selected' : '' ?>>Peringatan (Warning)</option>
                            <option value="ALL_ISSUES" <?= ($filter_level === 'ALL_ISSUES') ? 'selected' : '' ?>>Semua Gangguan</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="filterLogSearch" class="form-label text-muted small fw-semibold mb-1">Cari Kode Rujukan atau Kata Kunci</label>
                        <input
                            type="text"
                            id="filterLogSearch"
                            name="search"
                            class="form-control form-control-sm"
                            placeholder="Contoh: ECC-20261125-7F3A atau nama modul..."
                            value="<?= esc($filter_search ?? '') ?>">
                    </div>

                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold flex-grow-1">
                            Terapkan
                        </button>
                        <?php if (!empty($filter_search) || ($filter_level ?? 'ERROR_CRITICAL') !== 'ERROR_CRITICAL'): ?>
                            <a href="<?= site_url('admin/audit-logs/app-errors') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr class="text-uppercase text-muted small">
                            <th scope="col" class="ps-3 py-3" style="width: 152px;">Waktu</th>
                            <th scope="col" class="py-3 text-center" style="width: 112px;">Tingkat</th>
                            <th scope="col" class="py-3" style="width: 184px;">Kode Rujukan</th>
                            <th scope="col" class="py-3">Ringkasan Pesan</th>
                            <th scope="col" class="pe-3 py-3 text-end" style="width: 104px;">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entries)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="fw-semibold mb-1">Tidak ada catatan gangguan pada filter ini.</div>
                                    <div class="small">Pilih tanggal lain atau ubah filter tingkat gangguan.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($entries as $idx => $entry): ?>
                                <?php
                                    $lvl = strtoupper((string) ($entry['level'] ?? 'ERROR'));
                                    if (in_array($lvl, ['CRITICAL', 'ALERT', 'EMERGENCY'], true)) {
                                        $lvlBadge = 'bg-danger-subtle text-danger border border-danger-subtle';
                                    } elseif ($lvl === 'ERROR') {
                                        $lvlBadge = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                    } else {
                                        $lvlBadge = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                    }
                                    $fullDetail = trim($entry['message'] . ($entry['detail'] !== '' ? "\n\n" . $entry['detail'] : ''));
                                ?>
                                <tr>
                                    <td class="ps-3 py-3" style="font-variant-numeric: tabular-nums;">
                                        <div class="fw-semibold text-dark small"><?= esc(date('d/m/Y', strtotime($entry['timestamp']))) ?></div>
                                        <div class="text-muted small"><?= esc(date('H:i:s', strtotime($entry['timestamp']))) ?> WIB</div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge <?= $lvlBadge ?> rounded-pill px-3 py-1 fw-semibold">
                                            <?= esc($lvl) ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <?php if (!empty($entry['ref_id'])): ?>
                                            <span class="log-ref-badge font-monospace"><?= esc($entry['ref_id']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 log-msg-cell">
                                        <?= esc(mb_strimwidth((string) $entry['message'], 0, 180, '...')) ?>
                                    </td>
                                    <td class="pe-3 py-3 text-end">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-view-log-detail"
                                            data-title="<?= esc(($entry['ref_id'] ? $entry['ref_id'] . ' — ' : '') . $lvl . ' (' . $entry['timestamp'] . ')') ?>"
                                            data-target="logDetailPayload_<?= $idx ?>">
                                            Detail
                                        </button>
                                        <textarea id="logDetailPayload_<?= $idx ?>" class="d-none" readonly aria-hidden="true"><?= esc($fullDetail) ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($selected_date)): ?>
                <div class="px-3 py-2 border-top bg-light d-flex justify-content-between flex-wrap gap-2 small text-muted" style="font-variant-numeric: tabular-nums;">
                    <span>Berkas aktif: <strong>log-<?= esc($selected_date) ?>.log</strong> (<?= number_format((float) ($file_size_mb ?? 0), 2, ',', '.') ?> MB)</span>
                    <span>Menampilkan maksimal 120 entri terbaru yang cocok.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="appLogDetailModal" tabindex="-1" aria-labelledby="appLogDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header py-3 px-4 border-bottom bg-light rounded-top-4">
                <h2 class="modal-title h6 fw-bold text-dark mb-0" id="appLogDetailTitle">Rincian Log Gangguan</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" id="btnCopyAppLogDetail" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-clipboard me-1" aria-hidden="true"></i> Salin Rincian
                    </button>
                </div>
                <pre id="appLogDetailContent" class="p-3 bg-dark text-light rounded-3 font-monospace mb-0 log-detail-pre"></pre>
            </div>
            <div class="modal-footer py-2 px-4 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('appLogFilterForm');
    const dateSelect = document.getElementById('filterLogDate');
    const levelSelect = document.getElementById('filterLogLevel');
    const btnRefresh = document.getElementById('btnRefreshAppLogs');

    if (dateSelect && form) {
        dateSelect.addEventListener('change', function () {
            form.submit();
        });
    }
    if (levelSelect && form) {
        levelSelect.addEventListener('change', function () {
            form.submit();
        });
    }
    if (btnRefresh) {
        btnRefresh.addEventListener('click', function () {
            window.location.reload();
        });
    }

    const modalEl = document.getElementById('appLogDetailModal');
    const titleEl = document.getElementById('appLogDetailTitle');
    const contentEl = document.getElementById('appLogDetailContent');
    const btnCopy = document.getElementById('btnCopyAppLogDetail');
    let activeDetailText = '';

    document.querySelectorAll('.btn-view-log-detail').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const payloadEl = targetId ? document.getElementById(targetId) : null;
            activeDetailText = payloadEl ? payloadEl.value : '';
            if (titleEl) {
                titleEl.textContent = this.getAttribute('data-title') || 'Rincian Log Gangguan';
            }
            if (contentEl) {
                contentEl.textContent = activeDetailText;
            }
            if (modalEl && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    });

    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            if (!activeDetailText || !navigator.clipboard) return;
            navigator.clipboard.writeText(activeDetailText).then(function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersalin',
                        text: 'Rincian log berhasil disalin.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
