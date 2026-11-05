<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('content') ?>
<?php
$metrics    = $metrics ?? [];
$rows       = $rows ?? [];
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$filters    = $filters ?? ['q' => '', 'prodi' => 'all', 'angkatan' => 'all', 'status' => 'all', 'periode' => 'all'];
$refs       = $refs ?? [];

$exportQuery = http_build_query($filters);
?>

<style>
    .ecc-rj-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-rj-card {
            padding: 16px;
        }
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
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
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
    .ecc-table-rj tbody td {
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
    .ecc-btn-pill-sm {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
</style>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Rekam Jejak Taruna</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">Rekam Jejak Taruna</h4>
        <p class="text-secondary mb-0" style="font-size: 0.875rem;">Ringkasan perjalanan pendidikan, capaian, dan riwayat status seluruh taruna di ECC 2.0.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('ketarunaan/rekam-jejak/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Ekspor
        </a>
        <?php if (!empty($canManageTaruna)): ?>
            <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Capaian
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('success')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- 4 Kartu Metrik Ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #eff6ff; color: #0284c7;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Taruna Tercatat</div>
                <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                    <?= number_format((int) ($metrics['taruna_tercatat'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">taruna</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #ecfdf5; color: #059669;">
                <i class="bi bi-patch-check-fill"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Capaian Tervalidasi</div>
                <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                    <?= number_format((int) ($metrics['capaian_tervalidasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">capaian</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #f5f3ff; color: #7c3aed;">
                <i class="bi bi-trophy-fill"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tervalidasi</div>
                <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                    <?= number_format((int) ($metrics['prestasi_tervalidasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">prestasi</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="ecc-metric-tile">
            <div class="ecc-metric-icon" style="background-color: #fef3c7; color: #d97706;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Menunggu Validasi</div>
                <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                    <?= number_format((int) ($metrics['menunggu_validasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">capaian</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kartu Utama Filter & Tabel Direktori -->
<div class="ecc-rj-card">
    <form method="get" action="<?= base_url('ketarunaan/rekam-jejak') ?>" class="row g-2 align-items-end mb-4">
        <div class="col-12 col-md-3">
            <label for="filterQ" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Cari Taruna</label>
            <input type="text" id="filterQ" name="q" value="<?= esc($filters['q']) ?>" class="form-control ecc-control-sm" placeholder="Nomor Taruna atau nama...">
        </div>
        <div class="col-6 col-md-2">
            <label for="filterProdi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Program Studi</label>
            <select id="filterProdi" name="prodi" class="form-select ecc-control-sm">
                <option value="all">Semua Prodi</option>
                <?php foreach (($refs['prodi_list'] ?? []) as $pr): ?>
                    <option value="<?= esc($pr['kode_prodi']) ?>" <?= ($filters['prodi'] === $pr['kode_prodi']) ? 'selected' : '' ?>>
                        <?= esc($pr['kode_prodi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="filterAngkatan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Angkatan</label>
            <select id="filterAngkatan" name="angkatan" class="form-select ecc-control-sm">
                <option value="all">Semua Angkatan</option>
                <?php foreach (($refs['angkatan_list'] ?? []) as $ang): ?>
                    <option value="<?= esc((string) $ang['tahun_masuk']) ?>" <?= ((string) $filters['angkatan'] === (string) $ang['tahun_masuk']) ? 'selected' : '' ?>>
                        <?= esc((string) $ang['tahun_masuk']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="filterStatus" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Pendidikan</label>
            <select id="filterStatus" name="status" class="form-select ecc-control-sm">
                <option value="all">Semua Status</option>
                <?php foreach (($refs['status_catalog'] ?? []) as $stKey => $stMeta): ?>
                    <option value="<?= esc($stKey) ?>" <?= ($filters['status'] === $stKey) ? 'selected' : '' ?>>
                        <?= esc($stMeta['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
            <div class="flex-grow-1">
                <label for="filterPeriode" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select id="filterPeriode" name="periode" class="form-select ecc-control-sm">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= ((string) $filters['periode'] === (string) $ta['id']) ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold">Terapkan</button>
                <a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="btn btn-light border ecc-control-sm px-2 text-secondary" title="Reset Filter">Reset</a>
            </div>
        </div>
    </form>

    <!-- Tabel Desktop (md ke atas) -->
    <div class="table-responsive d-none d-md-block">
        <table class="table ecc-table-rj mb-0">
            <thead>
                <tr>
                    <th>Nomor Taruna</th>
                    <th>Nama Taruna</th>
                    <th>Prodi</th>
                    <th>Angkatan</th>
                    <th>Status Pendidikan</th>
                    <th class="text-center">Capaian</th>
                    <th class="text-center">Prestasi</th>
                    <th>Pembaruan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-secondary">Tidak ada data rekam jejak taruna yang sesuai filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="fw-semibold ecc-num"><?= esc($row['nomor_taruna']) ?></td>
                            <td class="fw-semibold text-dark"><?= esc($row['nama_lengkap']) ?></td>
                            <td><?= esc($row['kode_prodi'] ?? '-') ?></td>
                            <td class="ecc-num"><?= esc((string) ($row['angkatan'] ?? '-')) ?></td>
                            <td><?= ketarunaan_status_badge((string) $row['status_terkini']) ?></td>
                            <td class="text-center fw-bold ecc-num"><?= (int) $row['jumlah_capaian'] ?></td>
                            <td class="text-center fw-bold ecc-num"><?= (int) $row['jumlah_prestasi'] ?></td>
                            <td class="text-secondary ecc-num"><?= esc(ketarunaan_format_tanggal($row['tanggal_pembaruan'], true)) ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $row['nomor_taruna'])) ?>" class="btn btn-outline-primary ecc-btn-pill-sm">
                                    Lihat Rekam Jejak
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Kartu Mobile -->
    <div class="d-md-none d-flex flex-column gap-3">
        <?php if (empty($rows)): ?>
            <div class="text-center py-4 text-secondary" style="font-size: 0.875rem;">Tidak ada data rekam jejak taruna yang sesuai filter.</div>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <div class="border rounded-3 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark"><?= esc($row['nama_lengkap']) ?></div>
                            <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                <?= esc($row['nomor_taruna']) ?> &bull; <?= esc($row['kode_prodi'] ?? '-') ?> &bull; Angkatan <?= esc((string) ($row['angkatan'] ?? '-')) ?>
                            </div>
                        </div>
                        <?= ketarunaan_status_badge((string) $row['status_terkini']) ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2" style="font-size: 0.8125rem;">
                        <div class="text-secondary ecc-num">
                            Capaian: <strong class="text-dark"><?= (int) $row['jumlah_capaian'] ?></strong> &bull;
                            Prestasi: <strong class="text-dark"><?= (int) $row['jumlah_prestasi'] ?></strong>
                        </div>
                        <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $row['nomor_taruna'])) ?>" class="btn btn-outline-primary ecc-btn-pill-sm">
                            Lihat Rekam Jejak
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginasi & Catatan Bawah -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
        <div class="text-secondary ecc-num" style="font-size: 0.8125rem;">
            Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> taruna
        </div>
        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <?php
            $curPage  = (int) $pagination['page'];
            $totPages = (int) $pagination['total_pages'];
            $startPg  = max(1, $curPage - 2);
            $endPg    = min($totPages, $curPage + 2);
            ?>
            <nav aria-label="Paginasi Rekam Jejak">
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link rounded-2" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $curPage - 1]))) ?>">Sebelumnya</a>
                    </li>
                    <?php for ($pg = $startPg; $pg <= $endPg; $pg++): ?>
                        <li class="page-item <?= ($pg === $curPage) ? 'active' : '' ?>">
                            <a class="page-link rounded-2 ecc-num" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $pg]))) ?>"><?= $pg ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                        <a class="page-link rounded-2" href="<?= base_url('ketarunaan/rekam-jejak?' . http_build_query(array_merge($filters, ['page' => $curPage + 1]))) ?>">Berikutnya</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Rekam jejak tetap tersimpan setelah perubahan status pendidikan.</span>
    </div>
</div>
<?= $this->endSection() ?>
