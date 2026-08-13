<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Log Keamanan Aktivitas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Audit Trail Bento Cards */
.bento-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bento-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
}
.bento-header {
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1.1rem 1.5rem;
}

/* JSON Code Box */
.json-preview-box {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.78rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 10px;
    max-height: 80px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
    color: #334155;
}
</style>

<div class="container-fluid px-3 pt-3">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-check text-primary me-2"></i> Log Keamanan Aktivitas
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Pantau seluruh rekam jejak aktivitas & audit trail transaksi seluruh pengguna untuk menjaga integritas data sistem.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" onclick="window.location.reload();">
                <i class="bi bi-arrow-repeat fs-6"></i> Segarkan Data
            </button>
        </div>
    </div>

    <!-- Filter Bento Box -->
    <div class="bento-card mb-4">
        <div class="bento-header">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi bi-funnel-fill text-primary me-2"></i>Filter Audit Trail
            </h6>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="<?= base_url('admin/audit-logs') ?>" id="auditFilterForm">
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Cari Pengguna / IP / Entity ID</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 bg-light rounded-end-3" placeholder="Nama, NIP, IP, ID..." value="<?= esc($filter_search ?? '') ?>">
                        </div>
                    </div>

                    <!-- Jenis Aktivitas (Action) -->
                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Jenis Aksi</label>
                        <select name="action" class="form-select form-select-sm border-0 bg-light rounded-3" onchange="this.form.submit()">
                            <option value="">-- Semua Aksi --</option>
                            <?php foreach ($unique_actions as $act): ?>
                                <option value="<?= esc($act) ?>" <?= ($filter_action === $act) ? 'selected' : '' ?>><?= esc($act) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Modul/Tabel (Entity) -->
                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Entitas / Modul</label>
                        <select name="entity" class="form-select form-select-sm border-0 bg-light rounded-3" onchange="this.form.submit()">
                            <option value="">-- Semua Modul --</option>
                            <?php foreach ($unique_entities as $ent): ?>
                                <option value="<?= esc($ent) ?>" <?= ($filter_entity === $ent) ? 'selected' : '' ?>><?= esc($ent) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control form-control-sm border-0 bg-light rounded-3" value="<?= esc($filter_date_start ?? '') ?>" onchange="this.form.submit()">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control form-control-sm border-0 bg-light rounded-3" value="<?= esc($filter_date_end ?? '') ?>" onchange="this.form.submit()">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                    <a href="<?= base_url('admin/audit-logs') ?>" class="btn btn-light btn-sm rounded-pill px-3 border">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Audit Log Bento Box -->
    <div class="bento-card mb-4">
        <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-list-ul text-primary me-2"></i>Rekam Aktivitas Pengguna
                </h6>
                <small class="text-muted">Menampilkan <?= count($logs) ?> dari <?= esc($total_rows ?? count($logs)) ?> total catatan log</small>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-clock-history me-1"></i> Realtime Audit Trail
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0" id="auditLogTable">
                    <thead class="table-light text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 border-0" width="15%">Waktu</th>
                            <th class="py-3 border-0" width="20%">Pengguna</th>
                            <th class="py-3 border-0 text-center" width="12%">Aksi</th>
                            <th class="py-3 border-0" width="15%">Entitas</th>
                            <th class="py-3 border-0" width="16%">Data Lama</th>
                            <th class="py-3 border-0" width="16%">Data Baru</th>
                            <th class="pe-4 py-3 border-0 text-end" width="6%">IP / Device</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox display-4 mb-2 opacity-50"></i>
                                        <span class="fw-medium">Tidak ada data audit log yang sesuai dengan filter.</span>
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
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                            <?= date('d M Y', strtotime($log['created_at'])) ?>
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i><?= date('H:i:s', strtotime($log['created_at'])) ?> WIB
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-2 flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                                <?= strtoupper(substr(trim($log['nama_lengkap'] ?? 'System'), 0, 1)) ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="<?= esc($log['nama_lengkap'] ?? 'System / Anonymous') ?>">
                                                    <?= esc($log['nama_lengkap'] ?? 'System / Anonymous') ?>
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">
                                                    <?= esc($log['nip'] ?? '-') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi <?= $icon ?> me-1"></i><?= esc($action) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark mb-0.5" style="font-size: 0.88rem;">
                                            <?= esc($log['entity']) ?>
                                        </div>
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-0.5" style="font-size: 0.72rem;">
                                            ID: <?= esc($log['entity_id'] ?? '-') ?>
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
                                            <span class="text-muted opacity-50">-</span>
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
                                            <span class="text-muted opacity-50">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="fw-medium text-dark small" style="font-size: 0.8rem;">
                                            <?= esc($log['ip_address'] ?? '127.0.0.1') ?>
                                        </div>
                                        <?php if (!empty($log['user_agent'])): ?>
                                            <span class="text-muted" style="font-size: 0.7rem;" data-bs-toggle="tooltip" title="<?= esc($log['user_agent']) ?>">
                                                <i class="bi bi-info-circle text-primary me-1"></i>UA
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
            <div class="p-3 border-top d-flex justify-content-center">
                <?= $pager ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Viewer Detail Data JSON -->
<div class="modal fade" id="jsonViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold text-dark" id="jsonViewerTitle">
                    <i class="bi bi-code-square text-primary me-2"></i> Detail Data JSON Audit Log
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <pre id="jsonViewerContent" class="p-3 bg-dark text-light rounded-3 font-monospace mb-0" style="max-height: 400px; overflow-y: auto; font-size: 0.85rem;"></pre>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewJsonModal(title, elementId) {
    const rawText = document.getElementById(elementId)?.innerText || '';
    document.getElementById('jsonViewerTitle').innerText = title;
    
    try {
        const parsed = JSON.parse(rawText);
        document.getElementById('jsonViewerContent').innerText = JSON.stringify(parsed, null, 2);
    } catch (e) {
        document.getElementById('jsonViewerContent').innerText = rawText;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('jsonViewerModal'));
    modal.show();
}

document.addEventListener("DOMContentLoaded", function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?= $this->endSection() ?>
