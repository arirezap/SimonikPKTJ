<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Custom CSS for Audit Trail Bento Grid -->
<style>
    /* BENTO GRID LAYOUT */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }
    
    .bento-card {
        background: #ffffff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .bento-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    /* SPECIFIC GRID PLACEMENTS */
    .bento-header { grid-column: span 12; }
    .bento-filters { grid-column: span 12; }
    .bento-table { grid-column: span 12; }

    /* TYPOGRAPHY & COLORS */
    .text-gradient {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .subtitle {
        color: #64748b;
        font-size: 0.95rem;
        margin-top: 0.25rem;
    }

    /* BADGES */
    .badge-soft-success { background-color: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-weight: 500;}
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-weight: 500;}
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-weight: 500;}
    .badge-soft-info { background-color: #e0f2fe; color: #075985; padding: 6px 12px; border-radius: 20px; font-weight: 500;}
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-weight: 500;}
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-weight: 500;}

    /* TABLE STYLING */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .modern-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.95rem;
    }
    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .data-json {
        font-family: monospace;
        font-size: 0.85rem;
        background: #f1f5f9;
        padding: 8px;
        border-radius: 8px;
        max-height: 100px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }

    /* FILTERS */
    .modern-input, .modern-select {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
        background: #f8fafc;
        font-size: 0.95rem;
    }
    .modern-input:focus, .modern-select:focus {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
</style>

<div class="container-fluid py-4">
    <div class="bento-grid">
        
        <!-- Header Section -->
        <div class="bento-header">
            <div class="bento-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-0 text-gradient">Log Keamanan Aktivitas</h2>
                        <p class="subtitle mb-0">Pantau seluruh rekam jejak aktivitas kritis di dalam sistem untuk menjaga integritas data.</p>
                    </div>
                    <div>
                        <button class="btn btn-light rounded-pill px-4" onclick="window.location.reload();">
                            <i class="fas fa-sync-alt me-2"></i> Segarkan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bento-filters">
            <div class="bento-card">
                <form method="GET" action="<?= base_url('admin/audit-logs') ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">Jenis Aktivitas (Action)</label>
                        <select name="action" class="form-select modern-select" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <?php foreach ($unique_actions as $act): ?>
                                <option value="<?= htmlspecialchars($act) ?>" <?= ($filter_action === $act) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($act) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">Modul/Tabel (Entity)</label>
                        <select name="entity" class="form-select modern-select" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <?php foreach ($unique_entities as $ent): ?>
                                <option value="<?= htmlspecialchars($ent) ?>" <?= ($filter_entity === $ent) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ent) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <a href="<?= base_url('admin/audit-logs') ?>" class="btn btn-outline-secondary rounded-pill px-4">Reset Filter</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bento-table">
            <div class="bento-card">
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="15%">Waktu</th>
                                <th width="15%">Pengguna</th>
                                <th width="10%">Aksi</th>
                                <th width="15%">Entitas</th>
                                <th width="20%">Data Lama</th>
                                <th width="20%">Data Baru</th>
                                <th width="5%">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                        <p class="mb-0">Tidak ada log aktivitas yang ditemukan.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($logs as $log): 
                                    $actionClass = 'badge-soft-secondary';
                                    if ($log['action'] === 'CREATE') $actionClass = 'badge-soft-success';
                                    else if ($log['action'] === 'UPDATE') $actionClass = 'badge-soft-warning';
                                    else if ($log['action'] === 'DELETE') $actionClass = 'badge-soft-danger';
                                    else if ($log['action'] === 'LOGIN' || $log['action'] === 'LOGOUT') $actionClass = 'badge-soft-info';
                                    else if ($log['action'] === 'APPROVE' || $log['action'] === 'SIMULASI') $actionClass = 'badge-soft-primary';
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= date('d M Y', strtotime($log['created_at'])) ?></div>
                                        <div class="small text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($log['nama_lengkap'] ?? 'System / Anonymous') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($log['nip'] ?? '') ?></div>
                                    </td>
                                    <td>
                                        <span class="<?= $actionClass ?>"><?= htmlspecialchars($log['action']) ?></span>
                                    </td>
                                    <td>
                                        <div><strong><?= htmlspecialchars($log['entity']) ?></strong></div>
                                        <div class="small text-muted">ID: <?= htmlspecialchars($log['entity_id'] ?? '-') ?></div>
                                    </td>
                                    <td>
                                        <?php if(!empty($log['old_values'])): ?>
                                            <div class="data-json"><?= htmlspecialchars($log['old_values']) ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($log['new_values'])): ?>
                                            <div class="data-json"><?= htmlspecialchars($log['new_values']) ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small text-muted" title="<?= htmlspecialchars($log['user_agent']) ?>"><?= htmlspecialchars($log['ip_address']) ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    <?= $pager ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
