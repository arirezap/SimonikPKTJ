<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pelanggaran Taruna<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pl-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pl-card {
            padding: 16px;
        }
    }
    .ecc-kpi-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .ecc-kpi-icon {
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
    .ecc-table-pl thead th {
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
    .ecc-table-pl tbody td {
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$filters     = $filters ?? [];
$metrics     = $metrics ?? ['terbukti' => 0, 'dugaan' => 0, 'terbukti_selesai' => 0, 'terbukti_proses' => 0];
$rows        = $rows ?? [];
$pagination  = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_filtered' => 0, 'from' => 0, 'to' => 0];
$refs        = $refs ?? [];
$canViewFull = !empty($canViewFull);
$canManage   = !empty($canManage);

$exportQuery = http_build_query([
    'q'                   => $filters['q'] ?? '',
    'prodi_id'            => $filters['prodi_id'] ?? '',
    'tahun'               => $filters['tahun'] ?? 'all',
    'status_penetapan'    => $filters['status_penetapan'] ?? 'all',
    'status_penyelesaian' => $filters['status_penyelesaian'] ?? 'all',
    'status_validasi'     => $filters['status_validasi'] ?? 'all',
]);
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Pelanggaran Taruna</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">Pelanggaran Taruna</h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Pencatatan dugaan, pemeriksaan, penetapan, dan pemantauan tindak lanjut kasus.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.75rem;">
                Akses Terbatas
            </span>
            <?php if ($canViewFull): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/ekspor?' . $exportQuery) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-download"></i> Ekspor
                </a>
            <?php endif; ?>
            <?php if ($canManage): ?>
                <a href="<?= base_url('ketarunaan/pelanggaran/tambah') ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Catat Kasus
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

    <!-- 4 Kartu Metrik Utama -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-kpi-box">
                <div class="ecc-kpi-icon" style="background-color: #fef2f2; color: #dc2626;">
                    <i class="bi bi-shield-fill-exclamation"></i>
                </div>
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Pelanggaran Terbukti</div>
                    <div class="fs-4 fw-bold text-dark ecc-num lh-sm mt-1">
                        <?= number_format((int) $metrics['terbukti'], 0, ',', '.') ?>
                        <span class="fs-6 fw-normal text-secondary">kasus</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-kpi-box">
                <div class="ecc-kpi-icon" style="background-color: #fef3c7; color: #d97706;">
                    <i class="bi bi-search"></i>
                </div>
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Dugaan Dalam Pemeriksaan</div>
                    <div class="fs-4 fw-bold text-dark ecc-num lh-sm mt-1">
                        <?= number_format((int) $metrics['dugaan'], 0, ',', '.') ?>
                        <span class="fs-6 fw-normal text-secondary">kasus</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-kpi-box">
                <div class="ecc-kpi-icon" style="background-color: #ecfdf5; color: #10b981;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Kasus Terbukti Selesai</div>
                    <div class="fs-4 fw-bold text-dark ecc-num lh-sm mt-1">
                        <?= number_format((int) $metrics['terbukti_selesai'], 0, ',', '.') ?>
                        <span class="fs-6 fw-normal text-secondary">kasus</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="ecc-kpi-box">
                <div class="ecc-kpi-icon" style="background-color: #eff6ff; color: #0284c7;">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div>
                    <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Kasus Terbukti Dalam Proses</div>
                    <div class="fs-4 fw-bold text-dark ecc-num lh-sm mt-1">
                        <?= number_format((int) $metrics['terbukti_proses'], 0, ',', '.') ?>
                        <span class="fs-6 fw-normal text-secondary">kasus</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Daftar & Filter Kasus -->
    <div class="ecc-pl-card">
        <h5 class="fw-bold text-dark mb-3">Daftar Kasus Pelanggaran</h5>

        <form method="get" action="<?= base_url('ketarunaan/pelanggaran') ?>" class="mb-4">
            <div class="row g-2 mb-2">
                <div class="col-12 col-lg-6">
                    <input
                        type="text"
                        name="q"
                        class="form-control ecc-control-sm"
                        value="<?= esc((string) ($filters['q'] ?? '')) ?>"
                        placeholder="<?= $canViewFull ? 'Cari nomor kasus, Nomor Taruna, atau nama...' : 'Cari nomor kasus atau Nomor Taruna...' ?>"
                    >
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <select name="prodi_id" class="form-select ecc-control-sm" aria-label="Filter Program Studi">
                        <option value="">Semua Prodi</option>
                        <?php foreach ($refs['prodi_list'] ?? [] as $pr): ?>
                            <option value="<?= (int) $pr['id'] ?>" <?= ((string) ($filters['prodi_id'] ?? '') === (string) $pr['id']) ? 'selected' : '' ?>>
                                <?= esc((string) $pr['kode_prodi']) ?> - <?= esc((string) $pr['nama_prodi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <select name="tahun" class="form-select ecc-control-sm" aria-label="Filter Periode Tahun">
                        <option value="all">Semua Periode</option>
                        <?php foreach ($refs['tahun_list'] ?? [] as $thn): ?>
                            <option value="<?= (int) $thn ?>" <?= ((string) ($filters['tahun'] ?? 'all') === (string) $thn) ? 'selected' : '' ?>>
                                Tahun <?= (int) $thn ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="status_penetapan" class="form-select ecc-control-sm" aria-label="Filter Status Penetapan">
                        <option value="all">Semua Status Penetapan</option>
                        <?php foreach ($refs['penetapan_options'] ?? [] as $k => $lbl): ?>
                            <option value="<?= esc($k) ?>" <?= ((string) ($filters['status_penetapan'] ?? 'all') === $k) ? 'selected' : '' ?>>
                                <?= esc($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="status_penyelesaian" class="form-select ecc-control-sm" aria-label="Filter Status Penyelesaian">
                        <option value="all">Semua Status Penyelesaian</option>
                        <?php foreach ($refs['penyelesaian_options'] ?? [] as $k => $lbl): ?>
                            <option value="<?= esc($k) ?>" <?= ((string) ($filters['status_penyelesaian'] ?? 'all') === $k) ? 'selected' : '' ?>>
                                <?= esc($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="status_validasi" class="form-select ecc-control-sm" aria-label="Filter Status Validasi">
                        <option value="all">Semua Status Validasi</option>
                        <?php foreach ($refs['validasi_options'] ?? [] as $k => $lbl): ?>
                            <option value="<?= esc($k) ?>" <?= ((string) ($filters['status_validasi'] ?? 'all') === $k) ? 'selected' : '' ?>>
                                <?= esc($lbl) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold flex-fill">Terapkan</button>
                    <a href="<?= base_url('ketarunaan/pelanggaran') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold">Reset</a>
                </div>
            </div>
        </form>

        <!-- Tabel Desktop -->
        <div class="table-responsive d-none d-lg-block mb-3">
            <table class="table ecc-table-pl mb-0">
                <thead>
                    <tr>
                        <th>Nomor Kasus</th>
                        <th>Taruna</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Penetapan</th>
                        <th>Penyelesaian</th>
                        <th>Validasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">Tidak ada kasus yang sesuai dengan filter saat ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $r): ?>
                            <?php
                            $rowId = (int) $r['id'];
                            $stVal = ($r['status_validasi'] === 'perlu_perbaikan') ? 'dikembalikan' : (string) $r['status_validasi'];
                            $tgl   = ketarunaan_format_tanggal(substr((string) ($r['tanggal_kejadian'] ?? ''), 0, 10), true);
                            ?>
                            <tr>
                                <td class="fw-semibold text-dark ecc-num text-nowrap"><?= esc((string) $r['nomor_perkara']) ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc((string) $r['display_nama']) ?></div>
                                    <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                        <?= esc((string) $r['display_nomor_taruna']) ?>
                                        <?php if (!empty($r['kode_prodi'])): ?>
                                            &bull; <?= esc((string) $r['kode_prodi']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= esc((string) ($r['jenis_pelanggaran'] ?: '-')) ?></td>
                                <td class="ecc-num text-nowrap"><?= esc($tgl) ?></td>
                                <td><?= ketarunaan_penetapan_badge((string) $r['status_penetapan']) ?></td>
                                <td><?= ketarunaan_penyelesaian_badge((string) $r['status_penyelesaian']) ?></td>
                                <td><?= ketarunaan_validasi_badge($stVal) ?></td>
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $rowId) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Detail</a>
                                        <?php if ($canManage): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-light border btn-sm rounded-pill px-2" type="button" data-bs-toggle="dropdown" aria-label="Menu Aksi">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="font-size: 0.875rem;">
                                                    <li><a class="dropdown-item" href="<?= base_url('ketarunaan/pelanggaran/edit/' . $rowId) ?>">1. Edit Pencatatan</a></li>
                                                    <li><a class="dropdown-item" href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $rowId) ?>">2. Pemeriksaan &amp; Pihak</a></li>
                                                    <li><a class="dropdown-item" href="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $rowId) ?>">3. Penetapan &amp; Sanksi</a></li>
                                                    <li><a class="dropdown-item" href="<?= base_url('ketarunaan/pelanggaran/dokumen/' . $rowId) ?>">4. Dokumen Bukti</a></li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tampilan Kartu Mobile -->
        <div class="d-lg-none d-flex flex-column gap-3 mb-3">
            <?php if (empty($rows)): ?>
                <div class="border rounded-3 p-4 text-center text-secondary">Tidak ada kasus yang sesuai dengan filter saat ini.</div>
            <?php else: ?>
                <?php foreach ($rows as $r): ?>
                    <?php
                    $rowId = (int) $r['id'];
                    $stVal = ($r['status_validasi'] === 'perlu_perbaikan') ? 'dikembalikan' : (string) $r['status_validasi'];
                    $tgl   = ketarunaan_format_tanggal(substr((string) ($r['tanggal_kejadian'] ?? ''), 0, 10), true);
                    ?>
                    <div class="border rounded-3 p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark ecc-num"><?= esc((string) $r['nomor_perkara']) ?></div>
                                <div class="fw-semibold text-dark mt-1"><?= esc((string) $r['display_nama']) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc((string) $r['display_nomor_taruna']) ?> &bull; <?= esc((string) ($r['jenis_pelanggaran'] ?: '-')) ?> &bull; <?= esc($tgl) ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?= ketarunaan_penetapan_badge((string) $r['status_penetapan']) ?>
                            <?= ketarunaan_penyelesaian_badge((string) $r['status_penyelesaian']) ?>
                            <?= ketarunaan_validasi_badge($stVal) ?>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?= base_url('ketarunaan/pelanggaran/detail/' . $rowId) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 flex-fill">Lihat Detail</a>
                            <?php if ($canManage): ?>
                                <a href="<?= base_url('ketarunaan/pelanggaran/edit/' . $rowId) ?>" class="btn btn-light border btn-sm rounded-pill px-3">Edit</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Ringkasan & Paginasi -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 pt-3 border-top">
            <div class="text-secondary ecc-num" style="font-size: 0.8125rem;">
                Menampilkan <?= (int) $pagination['from'] ?>–<?= (int) $pagination['to'] ?> dari <?= (int) $pagination['total_filtered'] ?> kasus
            </div>

            <?php if ((int) $pagination['total_pages'] > 1): ?>
                <nav aria-label="Navigasi Halaman Kasus">
                    <ul class="pagination pagination-sm mb-0 ecc-num">
                        <?php
                        $curPage    = (int) $pagination['page'];
                        $totalPages = (int) $pagination['total_pages'];
                        $baseParams = $filters;
                        ?>
                        <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                            <?php $baseParams['page'] = max(1, $curPage - 1); ?>
                            <a class="page-link" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query($baseParams)) ?>">&laquo;</a>
                        </li>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php $baseParams['page'] = $p; ?>
                            <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query($baseParams)) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                            <?php $baseParams['page'] = min($totalPages, $curPage + 1); ?>
                            <a class="page-link" href="<?= base_url('ketarunaan/pelanggaran?' . http_build_query($baseParams)) ?>">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 0.8125rem;">
            Hanya kasus terbukti dan tervalidasi dihitung sebagai pelanggaran sah.
        </div>
    </div>
</div>
<?= $this->endSection() ?>
