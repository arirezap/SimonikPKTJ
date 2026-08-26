<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Log Aktivitas Sistem<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .json-preview-box {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 8px;
        max-height: 68px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
        color: #334155;
    }
    .table-audit th, .table-audit td {
        vertical-align: middle;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-shield-check text-primary me-2"></i>Log Aktivitas Sistem</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                Total <?= esc($total_rows ?? count($logs)) ?> Catatan
            </span>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm" onclick="window.location.reload();" title="Segarkan data audit log">
                <i class="bi bi-arrow-repeat me-1"></i> Segarkan Data
            </button>
        </div>
    </div>

    <!-- FILTER TOOLBAR (BENTO CARD) -->
    <div class="card mb-3 border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-3">
            <form method="GET" action="<?= base_url('admin/audit-logs') ?>" id="auditFilterForm">
                <div class="row g-2 align-items-end">
                    <!-- Search Input -->
                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Pencarian</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama, NIP, IP, atau ID..." value="<?= esc($filter_search ?? '') ?>">
                        </div>
                    </div>

                    <!-- Jenis Aksi (Action) -->
                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Jenis Aksi</label>
                        <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Aksi</option>
                            <?php foreach ($unique_actions as $act): ?>
                                <option value="<?= esc($act) ?>" <?= ($filter_action === $act) ? 'selected' : '' ?>><?= esc($act) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Modul/Tabel (Entity) -->
                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Modul</label>
                        <select name="entity" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Modul</option>
                            <?php foreach ($unique_entities as $ent): ?>
                                <option value="<?= esc($ent) ?>" <?= ($filter_entity === $ent) ? 'selected' : '' ?>><?= esc($ent) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control form-control-sm" value="<?= esc($filter_date_start ?? '') ?>" onchange="this.form.submit()">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-6 col-sm-3 col-md-2">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control form-control-sm" value="<?= esc($filter_date_end ?? '') ?>" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                    <?php if (!empty($filter_search) || !empty($filter_action) || !empty($filter_entity) || !empty($filter_date_start) || !empty($filter_date_end)): ?>
                        <a href="<?= base_url('admin/audit-logs') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DATA TABLE (BENTO CARD) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-audit align-middle mb-0" id="auditLogTable">
                    <thead class="table-light border-bottom">
                        <tr class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <th class="ps-3 py-2.5" style="width: 140px;">Waktu</th>
                            <th class="py-2.5" style="min-width: 180px;">Pengguna</th>
                            <th class="py-2.5 text-center" style="width: 110px;">Aksi</th>
                            <th class="py-2.5" style="min-width: 140px;">Entitas</th>
                            <th class="py-2.5" style="min-width: 160px;">Data Lama</th>
                            <th class="py-2.5" style="min-width: 160px;">Data Baru</th>
                            <th class="pe-3 py-2.5 text-end" style="width: 110px;">IP / Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
                                        <span class="small fw-semibold">Tidak ada data log yang sesuai dengan filter.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $index => $log): ?>
                                <?php
                                    $action = strtoupper($log['action']);
                                    if ($action === 'CREATE') {
                                        $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                        $icon = 'bi-plus-circle';
                                    } elseif ($action === 'UPDATE') {
                                        $badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                        $icon = 'bi-pencil-square';
                                    } elseif ($action === 'DELETE') {
                                        $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                        $icon = 'bi-trash3';
                                    } elseif ($action === 'LOGIN' || $action === 'LOGOUT') {
                                        $badgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                        $icon = ($action === 'LOGIN') ? 'bi-box-arrow-in-right' : 'bi-box-arrow-left';
                                    } elseif ($action === 'APPROVE' || $action === 'SIMULASI') {
                                        $badgeClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                        $icon = 'bi-check-circle';
                                    } else {
                                        $badgeClass = 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                                        $icon = 'bi-activity';
                                    }
                                ?>
                                <tr>
                                    <td class="ps-3 py-2.5" style="font-variant-numeric: tabular-nums;">
                                        <div class="fw-bold text-dark" style="font-size: 0.82rem;">
                                            <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.71rem;">
                                            <i class="bi bi-clock me-1"></i><?= date('H:i:s', strtotime($log['created_at'])) ?> WIB
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                <?= strtoupper(substr(trim($log['nama_lengkap'] ?? 'System'), 0, 1)) ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <span class="fw-bold text-dark d-block text-truncate" style="max-width: 170px; font-size: 0.82rem;" title="<?= esc($log['nama_lengkap'] ?? 'System / Anonymous') ?>">
                                                    <?= esc($log['nama_lengkap'] ?? 'System') ?>
                                                </span>
                                                <small class="text-muted" style="font-size: 0.71rem;">
                                                    <?= esc($log['nip'] ?: '-') ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-2.5 py-1" style="font-size: 0.72rem; font-weight: 500;">
                                            <i class="bi <?= $icon ?> me-1"></i><?= esc($action) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark" style="font-size: 0.82rem;">
                                            <?= esc($log['entity']) ?>
                                        </div>
                                        <span class="badge bg-light text-secondary border fw-normal px-1.5 py-0.5" style="font-size: 0.68rem;">
                                            ID: <?= esc($log['entity_id'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['old_values']) && $log['old_values'] !== 'null'): ?>
                                            <div class="json-preview-box" id="oldJson_<?= $log['id'] ?>">
                                                <?= esc($log['old_values']) ?>
                                            </div>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary mt-1" onclick="viewJsonModal('Data Lama ID #<?= $log['id'] ?>', 'oldJson_<?= $log['id'] ?>')">
                                                <i class="bi bi-fullscreen me-1"></i> Detail
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small opacity-50">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['new_values']) && $log['new_values'] !== 'null'): ?>
                                            <div class="json-preview-box" id="newJson_<?= $log['id'] ?>">
                                                <?= esc($log['new_values']) ?>
                                            </div>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary mt-1" onclick="viewJsonModal('Data Baru ID #<?= $log['id'] ?>', 'newJson_<?= $log['id'] ?>')">
                                                <i class="bi bi-fullscreen me-1"></i> Detail
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small opacity-50">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-3 text-end" style="font-variant-numeric: tabular-nums;">
                                        <div class="fw-medium text-dark small" style="font-size: 0.78rem;">
                                            <?= esc($log['ip_address'] ?? '127.0.0.1') ?>
                                        </div>
                                        <?php if (!empty($log['user_agent'])): ?>
                                            <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem;" data-bs-toggle="tooltip" title="<?= esc($log['user_agent']) ?>">
                                                <i class="bi bi-browser-chrome me-0.5 text-primary"></i> UA
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pager)): ?>
            <div class="p-3 border-top d-flex justify-content-center">
                <?= $pager ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Viewer Detail Data JSON -->
<div class="modal fade" id="jsonViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header py-3 px-4 border-bottom bg-light rounded-top-4">
                <h6 class="modal-title fw-bold text-dark mb-0" id="jsonViewerTitle">
                    <i class="bi bi-code-square text-primary me-2"></i> Detail Data JSON Audit Log
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="copyJsonContent()">
                        <i class="bi bi-clipboard me-1"></i> Salin JSON
                    </button>
                </div>
                <pre id="jsonViewerContent" class="p-3 bg-dark text-light rounded-3 font-monospace mb-0" style="max-height: 420px; overflow-y: auto; font-size: 0.83rem; line-height: 1.4;"></pre>
            </div>
            <div class="modal-footer py-2 px-4 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentJsonText = '';

function viewJsonModal(title, elementId) {
    const rawText = document.getElementById(elementId)?.innerText || '';
    document.getElementById('jsonViewerTitle').innerText = title;
    
    try {
        const parsed = JSON.parse(rawText);
        currentJsonText = JSON.stringify(parsed, null, 2);
    } catch (e) {
        currentJsonText = rawText;
    }
    
    document.getElementById('jsonViewerContent').innerText = currentJsonText;
    const modal = new bootstrap.Modal(document.getElementById('jsonViewerModal'));
    modal.show();
}

function copyJsonContent() {
    if (!currentJsonText) return;
    navigator.clipboard.writeText(currentJsonText).then(function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Tersalin',
                text: 'Data JSON berhasil disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert('Data JSON berhasil disalin ke clipboard.');
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>
