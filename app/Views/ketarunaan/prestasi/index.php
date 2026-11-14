<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Prestasi Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pr-card {
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
    .ecc-table-pr thead th {
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
    .ecc-table-pr tbody td {
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
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$metrics    = $metrics ?? [];
$rows       = $rows ?? [];
$pagination = $pagination ?? ['page' => 1, 'per_page' => 15, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$filters    = $filters ?? ['q' => '', 'jenis' => 'all', 'tingkat' => 'all', 'prodi' => 'all', 'periode' => 'all', 'status' => 'all'];
$refs       = $refs ?? [];

$exportQuery = http_build_query($filters);
$totalPrestasiValid = (int) ($metrics['prestasi_tervalidasi'] ?? 0);
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Prestasi Taruna</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">Prestasi Taruna</h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Kelola prestasi individu dan tim yang terverifikasi.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= base_url('ketarunaan/prestasi/ekspor?' . $exportQuery) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-download"></i> Ekspor
            </a>
            <?php if (!empty($canManageTaruna)): ?>
                <a href="<?= base_url('ketarunaan/prestasi/tambah') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Tambah Prestasi
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

    <!-- 4 Kartu Metrik Dual-Count -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-metric-tile">
                <div class="ecc-metric-icon" style="background-color: #ecfdf5; color: #059669;">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tervalidasi</div>
                    <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                        <?= number_format($totalPrestasiValid, 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">prestasi</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-metric-tile">
                <div class="ecc-metric-icon" style="background-color: #eff6ff; color: #0284c7;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Taruna Berprestasi</div>
                    <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                        <?= number_format((int) ($metrics['taruna_berprestasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">taruna (unik)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-metric-tile">
                <div class="ecc-metric-icon" style="background-color: #f5f3ff; color: #7c3aed;">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Prestasi Tim</div>
                    <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                        <?= number_format((int) ($metrics['prestasi_tim'] ?? 0), 0, ',', '.') ?>
                        <span class="fw-normal text-secondary" style="font-size: 0.75rem;">dari <?= number_format($totalPrestasiValid, 0, ',', '.') ?> prestasi</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-metric-tile">
                <div class="ecc-metric-icon" style="background-color: #fef3c7; color: #d97706;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-medium" style="font-size: 0.75rem;">Menunggu Validasi</div>
                    <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;">
                        <?= number_format((int) ($metrics['menunggu_validasi'] ?? 0), 0, ',', '.') ?> <span class="fw-normal text-secondary" style="font-size: 0.8125rem;">prestasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Daftar Prestasi -->
    <div class="ecc-pr-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">Daftar Prestasi</h5>
        </div>

        <form method="get" action="<?= base_url('ketarunaan/prestasi') ?>" class="row g-2 align-items-end mb-4">
            <div class="col-12 col-md-4">
                <label for="filterQ" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Pencarian</label>
                <input type="text" id="filterQ" name="q" value="<?= esc($filters['q']) ?>" class="form-control ecc-control-sm" placeholder="Cari prestasi atau nama peserta...">
            </div>
            <div class="col-6 col-md-4">
                <label for="filterJenis" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Prestasi</label>
                <select id="filterJenis" name="jenis" class="form-select ecc-control-sm">
                    <option value="all">Semua Jenis</option>
                    <?php foreach (($refs['jenis_catalog'] ?? []) as $jKey => $jLabel): ?>
                        <option value="<?= esc($jKey) ?>" <?= ($filters['jenis'] === $jKey) ? 'selected' : '' ?>><?= esc($jLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <label for="filterTingkat" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Tingkat Prestasi</label>
                <select id="filterTingkat" name="tingkat" class="form-select ecc-control-sm">
                    <option value="all">Semua Tingkat</option>
                    <?php foreach (($refs['tingkat_catalog'] ?? []) as $tKey => $tLabel): ?>
                        <option value="<?= esc($tKey) ?>" <?= ($filters['tingkat'] === $tKey) ? 'selected' : '' ?>><?= esc($tLabel) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label for="filterProdi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Program Studi</label>
                <select id="filterProdi" name="prodi" class="form-select ecc-control-sm">
                    <option value="all">Semua Prodi</option>
                    <?php foreach (($refs['prodi_list'] ?? []) as $pr): ?>
                        <option value="<?= esc($pr['kode_prodi']) ?>" <?= ($filters['prodi'] === $pr['kode_prodi']) ? 'selected' : '' ?>><?= esc($pr['kode_prodi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
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
            <div class="col-6 col-md-3">
                <label for="filterStatus" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                <select id="filterStatus" name="status" class="form-select ecc-control-sm">
                    <option value="all">Semua Status Validasi</option>
                    <option value="tervalidasi" <?= ($filters['status'] === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                    <option value="diajukan" <?= ($filters['status'] === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                    <option value="dikembalikan" <?= (in_array($filters['status'], ['dikembalikan', 'perlu_perbaikan'], true)) ? 'selected' : '' ?>>Dikembalikan</option>
                    <option value="draf" <?= ($filters['status'] === 'draf') ? 'selected' : '' ?>>Draf</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-grow-1">Terapkan</button>
                <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary">Reset</a>
            </div>
        </form>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-md-block">
            <table class="table ecc-table-pr mb-0">
                <thead>
                    <tr>
                        <th>Nama Prestasi</th>
                        <th>Tingkat</th>
                        <th>Peringkat</th>
                        <th>Keikutsertaan</th>
                        <th>Peserta</th>
                        <th>Tanggal</th>
                        <th>Validasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">Tidak ada data prestasi yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $stValid = ($row['status_validasi'] === 'perlu_perbaikan') ? 'dikembalikan' : (string) $row['status_validasi'];
                            $tingkatLabel = ($refs['tingkat_catalog'][$row['tingkat_prestasi']] ?? ucfirst((string) $row['tingkat_prestasi']));
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= base_url('ketarunaan/prestasi/detail/' . (int) $row['id']) ?>" class="fw-semibold text-dark text-decoration-none">
                                        <?= esc($row['nama_prestasi']) ?>
                                    </a>
                                    <?php if (!empty($row['nama_tim']) && $row['jenis_keikutsertaan'] === 'tim'): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($row['nama_tim']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($tingkatLabel) ?></td>
                                <td class="fw-medium"><?= esc($row['peringkat']) ?></td>
                                <td><?= ketarunaan_keikutsertaan_badge((string) $row['jenis_keikutsertaan']) ?></td>
                                <td class="ecc-num text-nowrap"><?= (int) $row['jumlah_peserta'] ?> taruna</td>
                                <td class="ecc-num text-secondary text-nowrap"><?= esc(ketarunaan_format_tanggal($row['tanggal_mulai'], true)) ?></td>
                                <td><?= ketarunaan_validasi_badge($stValid) ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="<?= base_url('ketarunaan/prestasi/detail/' . (int) $row['id']) ?>" class="btn btn-outline-primary ecc-btn-pill-sm" title="Detail Prestasi">
                                            Detail
                                        </a>
                                        <?php if (!empty($canManageTaruna)): ?>
                                            <a href="<?= base_url('ketarunaan/prestasi/edit/' . (int) $row['id']) ?>" class="btn btn-light border ecc-btn-pill-sm text-secondary" title="Ubah / Kelola">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
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
                <div class="text-center py-4 text-secondary" style="font-size: 0.875rem;">Tidak ada data prestasi yang sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $stValid = ($row['status_validasi'] === 'perlu_perbaikan') ? 'dikembalikan' : (string) $row['status_validasi'];
                    $tingkatLabel = ($refs['tingkat_catalog'][$row['tingkat_prestasi']] ?? ucfirst((string) $row['tingkat_prestasi']));
                    ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($row['nama_prestasi']) ?></div>
                                <div class="text-secondary" style="font-size: 0.75rem;">
                                    <?= esc($tingkatLabel) ?> &bull; <?= esc($row['peringkat']) ?> &bull; <?= esc(ketarunaan_format_tanggal($row['tanggal_mulai'], true)) ?>
                                </div>
                            </div>
                            <?= ketarunaan_validasi_badge($stValid) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                            <div class="d-flex align-items-center gap-2">
                                <?= ketarunaan_keikutsertaan_badge((string) $row['jenis_keikutsertaan']) ?>
                                <span class="text-secondary ecc-num" style="font-size: 0.8125rem;"><?= (int) $row['jumlah_peserta'] ?> taruna</span>
                            </div>
                            <a href="<?= base_url('ketarunaan/prestasi/detail/' . (int) $row['id']) ?>" class="btn btn-outline-primary ecc-btn-pill-sm">
                                Detail
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Paginasi & Catatan Bawah -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4 pt-3 border-top">
            <div class="text-secondary ecc-num" style="font-size: 0.8125rem;">
                Menampilkan <strong><?= (int) $pagination['from'] ?>&ndash;<?= (int) $pagination['to'] ?></strong> dari <strong><?= number_format((int) $pagination['total_filtered'], 0, ',', '.') ?></strong> prestasi
            </div>
            <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
                <?php
                $curPage  = (int) $pagination['page'];
                $totPages = (int) $pagination['total_pages'];
                $startPg  = max(1, $curPage - 2);
                $endPg    = min($totPages, $curPage + 2);
                ?>
                <nav aria-label="Paginasi Daftar Prestasi">
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <li class="page-item <?= ($curPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $curPage - 1]))) ?>">Sebelumnya</a>
                        </li>
                        <?php for ($pg = $startPg; $pg <= $endPg; $pg++): ?>
                            <li class="page-item <?= ($pg === $curPage) ? 'active' : '' ?>">
                                <a class="page-link rounded-2 ecc-num" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $pg]))) ?>"><?= $pg ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($curPage >= $totPages) ? 'disabled' : '' ?>">
                            <a class="page-link rounded-2" href="<?= base_url('ketarunaan/prestasi?' . http_build_query(array_merge($filters, ['per_page' => $pagination['per_page'], 'page' => $curPage + 1]))) ?>">Berikutnya</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
            <i class="bi bi-info-circle text-primary"></i>
            <span>Jumlah prestasi dihitung per penghargaan, jumlah taruna dihitung unik.</span>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
