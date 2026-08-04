<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap Kinerja Kepegawaian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
/* Skeleton Loader Animation */
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
.skeleton-box {
    display: inline-block;
    height: 1em;
    position: relative;
    overflow: hidden;
    background-color: #e2e8f0;
    border-radius: 4px;
}
.skeleton-box::after {
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.2) 20%, rgba(255,255,255,0.5) 60%, rgba(255,255,255,0));
    animation: shimmer 2s infinite;
    content: '';
}

/* Bento Grid Styles */
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

/* Table Header Sorting */
th.sortable {
    cursor: pointer;
    user-select: none;
    transition: background-color 0.15s ease;
}
th.sortable:hover {
    background-color: #e9ecef !important;
}
.sort-icon {
    display: inline-block;
    font-size: 0.75rem;
    margin-left: 4px;
    opacity: 0.4;
    transition: transform 0.2s ease, opacity 0.2s ease;
}
th.sortable.asc .sort-icon,
th.sortable.desc .sort-icon {
    opacity: 1;
    color: #0d6efd;
}

/* Quick Filter Pills */
.filter-pill {
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.4rem 0.9rem;
    border-radius: 50rem;
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
    background-color: #fff;
    color: #495057;
}
.filter-pill:hover {
    background-color: #e9ecef;
}
.filter-pill.active {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.25);
}

/* Mobile Card View for < 768px */
@media (max-width: 767.98px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
}
@media (min-width: 768px) {
    .desktop-table-view {
        display: block !important;
    }
    .mobile-cards-view {
        display: none !important;
    }
}
</style>

<div class="container-fluid px-3 pt-3">
    <!-- Header Area -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard2-data-fill text-primary me-2"></i> Rekap Kinerja Kepegawaian</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Monitoring & evaluasi capaian kinerja seluruh unit untuk keperluan remunerasi.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('kepegawaian/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>" class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 transition-all">
                <i class="bi bi-file-earmark-excel-fill fs-5"></i> Export Data
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Filter Bento Box -->
        <div class="col-lg-7">
            <div class="bento-card h-100">
                <div class="bento-header pb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-funnel-fill text-primary me-2"></i>Filter</h6>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="<?= site_url('kepegawaian') ?>" class="row g-3" id="filterForm">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Periode</label>
                            <select name="bulan" class="form-select border-0 bg-light rounded-3" onchange="showSkeletonAndSubmit()">
                                <option value="all" <?= ($bulan_terpilih === 'all') ? 'selected' : '' ?>>Sepanjang Tahun (1 Tahun)</option>
                                <?php foreach($bulan_indo as $index => $nama): ?>
                                    <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1 && $bulan_terpilih !== 'all') ? 'selected' : '' ?>><?= $nama ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Tahun</label>
                            <input type="number" name="tahun" class="form-control border-0 bg-light rounded-3" value="<?= esc($tahun_terpilih) ?>" onchange="showSkeletonAndSubmit()">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Unit Kerja</label>
                            <select name="unit" class="form-select border-0 bg-light rounded-3" onchange="showSkeletonAndSubmit()">
                                <option value="">Semua Unit Kerja</option>
                                <?php foreach ($daftar_unit as $u): ?>
                                    <option value="<?= esc($u) ?>" <?= ($u == $unit_filter) ? 'selected' : '' ?>><?= esc($u) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Bento Box -->
        <div class="col-lg-5">
            <div class="bento-card h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0 align-items-center">
                        <div class="col-3 border-end">
                            <div class="display-6 fw-bold text-dark mb-1 skeleton-hide" id="statTotalPegawai"><?= count($rekap_kinerja) ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">Total Pegawai</span>
                        </div>
                        <div class="col-3 border-end">
                            <div class="display-6 fw-bold text-success mb-1 skeleton-hide" id="statSudahDinilai"><?= esc($sudah_dinilai) ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">Sudah Dinilai</span>
                        </div>
                        <div class="col-3 border-end">
                            <div class="display-6 fw-bold text-danger mb-1 skeleton-hide" id="statBelumDinilai"><?= esc($belum_dinilai) ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">Belum Dinilai</span>
                        </div>
                        <div class="col-3">
                            <div class="display-6 fw-bold text-primary mb-1 skeleton-hide"><?= number_format($rata_rata_instansi, 1, ',', '.') ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">Rata-Rata</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap Kinerja Bento Box -->
    <div class="bento-card mb-4">
        <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-primary me-2"></i>Daftar Capaian Individu</h6>
                <small class="text-muted" id="visibleCounter">Menampilkan <?= count($rekap_kinerja) ?> pegawai</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Live Search Box -->
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchInput" class="form-control bg-light border-0" placeholder="Cari Nama, NIP, Unit..." autocomplete="off">
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold">
                    <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?>
                </span>
            </div>
        </div>

        <!-- Quick Status Filter Pills -->
        <div class="px-4 py-2 bg-light border-bottom d-flex align-items-center gap-2 flex-wrap" id="pillFilterGroup">
            <span class="text-muted small fw-semibold me-1"><i class="bi bi-filter me-1"></i>Filter Cepat:</span>
            <button type="button" class="filter-pill active" data-filter="all">Semua Data</button>
            <button type="button" class="filter-pill" data-filter="sudah">Sudah Dinilai</button>
            <button type="button" class="filter-pill" data-filter="belum">Belum Dinilai</button>
            <button type="button" class="filter-pill" data-filter="perhatian">Butuh Perhatian (&lt; 75)</button>
            <button type="button" class="filter-pill" data-filter="baik">Sangat Baik (&ge; 90)</button>
        </div>

        <div class="card-body p-0">
            <!-- Loading State -->
            <div id="tableLoader" style="display: none;">
                <div class="p-4">
                    <?php for($i=0; $i<5; $i++): ?>
                    <div class="d-flex mb-4 align-items-center">
                        <div class="skeleton-box me-3 flex-shrink-0" style="width: 45px; height: 45px; border-radius: 50%;"></div>
                        <div class="me-auto" style="width: 250px;">
                            <div class="skeleton-box mb-2" style="width: 100%; height: 16px;"></div>
                            <div class="skeleton-box" style="width: 60%; height: 12px;"></div>
                        </div>
                        <div class="skeleton-box me-4" style="width: 15%; height: 24px; border-radius: 20px;"></div>
                        <div class="skeleton-box me-4" style="width: 10%; height: 30px; border-radius: 20px;"></div>
                        <div class="skeleton-box" style="width: 12%; height: 35px; border-radius: 20px;"></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- DESKTOP TABLE VIEW -->
            <div class="table-responsive desktop-table-view" id="tableContent">
                <table class="table table-hover align-middle mb-0 border-0" id="mainDataTable">
                    <thead class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 border-0 sortable" data-sort="nama">
                                Pegawai <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <th class="py-3 border-0 sortable" data-sort="unit">
                                Unit Kerja <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <?php if ($bulan_terpilih === 'all'): ?>
                                <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m): ?>
                                    <th class="text-center py-3 border-0" style="min-width: 55px; font-size: 0.75rem;"><?= $m ?></th>
                                <?php endforeach; ?>
                                <th class="text-center py-3 border-0 border-start bg-light shadow-sm sortable" data-sort="target" style="min-width: 90px;">
                                    Target Thn <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                </th>
                            <?php else: ?>
                                <th class="text-center py-3 border-0 sortable" data-sort="target">
                                    Total Target <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                                </th>
                            <?php endif; ?>
                            <th class="text-center py-3 border-0 sortable" data-sort="dinilai">
                                Telah Dinilai <span class="sort-icon"><i class="bi bi-arrow-down-up"></i></span>
                            </th>
                            <th class="text-center pe-4 py-3 border-0 sortable desc" data-sort="nilai">
                                Rata-Rata Nilai <span class="sort-icon"><i class="bi bi-arrow-down"></i></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0" id="tableBody">
                        <?php if (empty($rekap_kinerja)): ?>
                            <tr class="no-data-row">
                                <td colspan="<?= ($bulan_terpilih === 'all') ? '17' : '5' ?>" class="text-center py-5">
                                    <div class="text-muted d-flex flex-column align-items-center">
                                        <i class="bi bi-folder-x display-4 mb-2 opacity-50"></i>
                                        <span class="fw-medium">Belum ada data pegawai untuk filter ini.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rekap_kinerja as $index => $item): ?>
                                <?php
                                    $rata = $item['rata_rata'];
                                    $dinilai = $item['rhk_dinilai'];
                                    $jumlahTarget = $item['jumlah_rhk'];
                                    
                                    // Status category for filter
                                    $statusCat = ($dinilai > 0) ? 'sudah' : 'belum';
                                    if ($dinilai > 0 && $rata < 75) $statusCat .= ' perhatian';
                                    if ($dinilai > 0 && $rata >= 90) $statusCat .= ' baik';

                                    // Badge styling
                                    if ($dinilai == 0) {
                                        $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                        $statusText = 'Belum Dinilai';
                                    } elseif ($rata < 60) {
                                        $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                        $statusText = 'Sangat Kurang';
                                    } elseif ($rata < 75) {
                                        $badgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25';
                                        $statusText = 'Butuh Perhatian';
                                    } elseif ($rata < 90) {
                                        $badgeClass = 'bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25';
                                        $statusText = 'Baik';
                                    } else {
                                        $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                        $statusText = 'Sangat Baik';
                                    }
                                ?>
                                <tr class="pegawai-row" 
                                    data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                    data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                    data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                    data-status="<?= $statusCat ?>"
                                    data-val-nama="<?= esc($item['pegawai']['nama_lengkap']) ?>"
                                    data-val-unit="<?= esc($item['pegawai']['unit'] ?? '') ?>"
                                    data-val-target="<?= $jumlahTarget ?>"
                                    data-val-dinilai="<?= $dinilai ?>"
                                    data-val-nilai="<?= $rata ?>">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <?php if (!empty($item['pegawai']['foto'])): ?>
                                                    <img src="<?= base_url('uploads/avatars/' . $item['pegawai']['foto']) ?>" class="rounded-circle border border-2 border-white shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 45px; height: 45px; font-size: 1.1rem; border: 2px solid #fff;">
                                                        <?= strtoupper(substr(trim($item['pegawai']['nama_lengkap']), 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-bold text-dark text-truncate mb-1" style="max-width: 220px;" title="<?= esc($item['pegawai']['nama_lengkap']) ?>"><?= esc($item['pegawai']['nama_lengkap']) ?></div>
                                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2"><?= esc($item['pegawai']['jabatan'] ?? 'Staf') ?></span>
                                                    <span><?= esc($item['pegawai']['nip']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal px-2.5 py-1.5"><i class="bi bi-building me-1 opacity-50"></i> <?= esc($item['pegawai']['unit'] ?? '-') ?></span>
                                    </td>

                                    <?php if ($bulan_terpilih === 'all'): ?>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <?php $val = $item['rata_rata_per_bulan'][$i]; ?>
                                            <td class="text-center" style="font-size: 0.85rem;">
                                                <?php if ($val !== null): ?>
                                                    <span class="fw-bold <?= $val < 75 ? 'text-danger' : 'text-success' ?>"><?= number_format($val, 1, ',', '.') ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted opacity-25">-</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endfor; ?>
                                        <td class="text-center border-start bg-light bg-opacity-50">
                                            <span class="fs-5 fw-medium text-dark"><?= $jumlahTarget ?></span>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-center">
                                            <span class="fs-5 fw-medium text-dark"><?= $jumlahTarget ?></span>
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill px-3 py-1 border shadow-sm">
                                            <span class="fw-bold <?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary' ?> fs-6"><?= $dinilai ?></span>
                                            <span class="text-muted mx-1">/</span>
                                            <span class="text-muted"><?= $jumlahTarget ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                            <span class="fs-4 fw-bold <?= explode(' ', $badgeClass)[2] ?>"><?= number_format($rata, 1, ',', '.') ?></span>
                                            <span class="badge <?= $badgeClass ?> rounded-pill" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2px;"><?= $statusText ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- MOBILE CARDS VIEW (<768px) -->
            <div class="mobile-cards-view p-3" id="mobileCardsContainer">
                <?php if (!empty($rekap_kinerja)): ?>
                    <div class="d-flex flex-column gap-3" id="mobileCardsList">
                        <?php foreach ($rekap_kinerja as $item): ?>
                            <?php
                                $rata = $item['rata_rata'];
                                $dinilai = $item['rhk_dinilai'];
                                $jumlahTarget = $item['jumlah_rhk'];
                                
                                $statusCat = ($dinilai > 0) ? 'sudah' : 'belum';
                                if ($dinilai > 0 && $rata < 75) $statusCat .= ' perhatian';
                                if ($dinilai > 0 && $rata >= 90) $statusCat .= ' baik';

                                if ($dinilai == 0) {
                                    $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                    $statusText = 'Belum Dinilai';
                                } elseif ($rata < 60) {
                                    $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    $statusText = 'Sangat Kurang';
                                } elseif ($rata < 75) {
                                    $badgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25';
                                    $statusText = 'Butuh Perhatian';
                                } elseif ($rata < 90) {
                                    $badgeClass = 'bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25';
                                    $statusText = 'Baik';
                                } else {
                                    $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                    $statusText = 'Sangat Baik';
                                }
                            ?>
                            <div class="card border border-light-subtle rounded-3 shadow-sm mobile-pegawai-card"
                                 data-nama="<?= esc(strtolower($item['pegawai']['nama_lengkap'])) ?>"
                                 data-nip="<?= esc(strtolower($item['pegawai']['nip'] ?? '')) ?>"
                                 data-unit="<?= esc(strtolower($item['pegawai']['unit'] ?? '')) ?>"
                                 data-status="<?= $statusCat ?>">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['pegawai']['foto'])): ?>
                                                <img src="<?= base_url('uploads/avatars/' . $item['pegawai']['foto']) ?>" class="rounded-circle border me-2.5" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-2.5" style="width: 40px; height: 40px; font-size: 1rem;">
                                                    <?= strtoupper(substr(trim($item['pegawai']['nama_lengkap']), 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 170px;"><?= esc($item['pegawai']['nama_lengkap']) ?></h6>
                                                <small class="text-muted"><?= esc($item['pegawai']['unit'] ?? '-') ?></small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fs-5 fw-bold <?= explode(' ', $badgeClass)[2] ?>"><?= number_format($rata, 1, ',', '.') ?></div>
                                            <span class="badge <?= $badgeClass ?> rounded-pill" style="font-size: 0.65rem;"><?= $statusText ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small">
                                        <span><i class="bi bi-list-check me-1"></i> Target RHK: <strong><?= $jumlahTarget ?></strong></span>
                                        <span><i class="bi bi-check2-circle me-1"></i> Dinilai: <strong class="<?= $dinilai == $jumlahTarget && $jumlahTarget > 0 ? 'text-success' : 'text-primary' ?>"><?= $dinilai ?>/<?= $jumlahTarget ?></strong></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function showSkeletonAndSubmit() {
    document.querySelectorAll('.skeleton-hide').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.skeleton-show').forEach(el => el.style.display = 'block');
    document.getElementById('tableContent').style.display = 'none';
    document.getElementById('tableLoader').style.display = 'block';

    setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 50);
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('liveSearchInput');
    const tableRows = document.querySelectorAll('.pegawai-row');
    const mobileCards = document.querySelectorAll('.mobile-pegawai-card');
    const pillButtons = document.querySelectorAll('#pillFilterGroup .filter-pill');
    const visibleCounter = document.getElementById('visibleCounter');

    let currentSearchTerm = '';
    let currentFilterCategory = 'all';

    function applyFilterAndSearch() {
        let visibleCount = 0;

        // Filter Desktop Table
        tableRows.forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const nip = row.getAttribute('data-nip') || '';
            const unit = row.getAttribute('data-unit') || '';
            const statusCat = row.getAttribute('data-status') || '';

            const matchesSearch = !currentSearchTerm || (nama.includes(currentSearchTerm) || nip.includes(currentSearchTerm) || unit.includes(currentSearchTerm));
            let matchesCategory = true;

            if (currentFilterCategory === 'sudah') {
                matchesCategory = statusCat.includes('sudah');
            } else if (currentFilterCategory === 'belum') {
                matchesCategory = statusCat.includes('belum');
            } else if (currentFilterCategory === 'perhatian') {
                matchesCategory = statusCat.includes('perhatian');
            } else if (currentFilterCategory === 'baik') {
                matchesCategory = statusCat.includes('baik');
            }

            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Filter Mobile Cards
        mobileCards.forEach(card => {
            const nama = card.getAttribute('data-nama') || '';
            const nip = card.getAttribute('data-nip') || '';
            const unit = card.getAttribute('data-unit') || '';
            const statusCat = card.getAttribute('data-status') || '';

            const matchesSearch = !currentSearchTerm || (nama.includes(currentSearchTerm) || nip.includes(currentSearchTerm) || unit.includes(currentSearchTerm));
            let matchesCategory = true;

            if (currentFilterCategory === 'sudah') {
                matchesCategory = statusCat.includes('sudah');
            } else if (currentFilterCategory === 'belum') {
                matchesCategory = statusCat.includes('belum');
            } else if (currentFilterCategory === 'perhatian') {
                matchesCategory = statusCat.includes('perhatian');
            } else if (currentFilterCategory === 'baik') {
                matchesCategory = statusCat.includes('baik');
            }

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCounter) {
            visibleCounter.textContent = 'Menampilkan ' + visibleCount + ' dari ' + tableRows.length + ' pegawai';
        }
    }

    // Live Search
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            applyFilterAndSearch();
        });
    }

    // Pill Filters
    pillButtons.forEach(pill => {
        pill.addEventListener('click', function() {
            pillButtons.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilterCategory = this.getAttribute('data-filter');
            applyFilterAndSearch();
        });
    });

    // Column Sorting
    const sortableHeaders = document.querySelectorAll('th.sortable');
    const tableBody = document.getElementById('tableBody');

    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort');
            let isAscending = !this.classList.contains('asc');

            // Reset other headers
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
                const icon = h.querySelector('.sort-icon i');
                if (icon) icon.className = 'bi bi-arrow-down-up';
            });

            this.classList.add(isAscending ? 'asc' : 'desc');
            const icon = this.querySelector('.sort-icon i');
            if (icon) icon.className = isAscending ? 'bi bi-arrow-up' : 'bi bi-arrow-down';

            const rowsArray = Array.from(document.querySelectorAll('.pegawai-row'));

            rowsArray.sort((a, b) => {
                let valA, valB;
                if (sortKey === 'nama') {
                    valA = a.getAttribute('data-val-nama').toLowerCase();
                    valB = b.getAttribute('data-val-nama').toLowerCase();
                    return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
                } else if (sortKey === 'unit') {
                    valA = a.getAttribute('data-val-unit').toLowerCase();
                    valB = b.getAttribute('data-val-unit').toLowerCase();
                    return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
                } else if (sortKey === 'target') {
                    valA = parseFloat(a.getAttribute('data-val-target')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-target')) || 0;
                } else if (sortKey === 'dinilai') {
                    valA = parseFloat(a.getAttribute('data-val-dinilai')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-dinilai')) || 0;
                } else if (sortKey === 'nilai') {
                    valA = parseFloat(a.getAttribute('data-val-nilai')) || 0;
                    valB = parseFloat(b.getAttribute('data-val-nilai')) || 0;
                }

                return isAscending ? valA - valB : valB - valA;
            });

            rowsArray.forEach(row => tableBody.appendChild(row));
        });
    });
});
</script>
<?= $this->endSection() ?>
