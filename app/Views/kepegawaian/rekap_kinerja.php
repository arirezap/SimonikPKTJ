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
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    transform: translateX(-100%);
    background-image: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0,
        rgba(255, 255, 255, 0.2) 20%,
        rgba(255, 255, 255, 0.5) 60%,
        rgba(255, 255, 255, 0)
    );
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
    padding: 1.25rem 1.5rem;
}
</style>

<div class="container-fluid px-3 pt-3">
    <!-- Header Area -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clipboard2-data-fill text-primary me-2"></i> Rekap Kinerja Kepegawaian</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Monitoring & evaluasi capaian kinerja seluruh unit untuk keperluan remunerasi.</p>
        </div>
        <a href="<?= site_url('kepegawaian/export-excel') ?>?bulan=<?= esc($bulan_terpilih) ?>&tahun=<?= esc($tahun_terpilih) ?>&unit=<?= esc($unit_filter) ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold d-flex align-items-center gap-2 transition-all">
            <i class="bi bi-file-earmark-excel-fill fs-5"></i> Export Data
        </a>
    </div>

    <div class="row g-4 mb-4">
        <!-- Filter Bento Box -->
        <div class="col-lg-7">
            <div class="bento-card h-100">
                <div class="bento-header pb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-funnel-fill text-muted me-2"></i>Filter Data</h6>
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
                                <option value="">Semua Unit</option>
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
        <?php
            $sudahDinilai = 0;
            $belumDinilai = 0;
            foreach ($rekap_kinerja as $r) {
                if ($r['rhk_dinilai'] > 0) $sudahDinilai++;
                else $belumDinilai++;
            }
        ?>
        <div class="col-lg-5">
            <div class="bento-card h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center h-100">
                    <div class="row text-center g-0">
                        <div class="col-4 border-end">
                            <div class="display-5 fw-bold text-dark mb-1 skeleton-hide"><?= count($rekap_kinerja) ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2.5rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.85rem;">Total Pegawai</span>
                        </div>
                        <div class="col-4 border-end">
                            <div class="display-5 fw-bold text-success mb-1 skeleton-hide"><?= $sudahDinilai ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2.5rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.85rem;">Sudah Dinilai</span>
                        </div>
                        <div class="col-4">
                            <div class="display-5 fw-bold text-danger mb-1 skeleton-hide"><?= $belumDinilai ?></div>
                            <div class="skeleton-box skeleton-show" style="width: 60%; height: 2.5rem; margin-bottom: 0.5rem; display: none;"></div>
                            <span class="text-muted fw-medium" style="font-size: 0.85rem;">Belum Dinilai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap Kinerja Bento Box -->
    <div class="bento-card">
        <div class="bento-header d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-table text-muted me-2"></i>Daftar Capaian Individu</h6>
            <span class="badge bg-primary rounded-pill px-3 py-2 fw-medium shadow-sm">Periode: <?= esc($nama_bulan) ?> <?= esc($tahun_terpilih) ?></span>
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

            <div class="table-responsive" id="tableContent">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead class="table-light text-muted" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 border-0">Pegawai</th>
                            <th class="py-3 border-0">Unit Kerja</th>
                            <?php if ($bulan_terpilih === 'all'): ?>
                                <?php foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m): ?>
                                    <th class="text-center py-3 border-0" style="min-width: 60px; font-size: 0.75rem;"><?= $m ?></th>
                                <?php endforeach; ?>
                                <th class="text-center py-3 border-0 border-start bg-light shadow-sm" style="min-width: 90px;">Target Thn</th>
                            <?php else: ?>
                                <th class="text-center py-3 border-0">Total Target</th>
                            <?php endif; ?>
                            <th class="text-center py-3 border-0">Telah Dinilai</th>
                            <th class="text-center pe-4 py-3 border-0">Rata-Rata Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php if (empty($rekap_kinerja)): ?>
                            <tr>
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
                                    
                                    // Penentuan warna badge nilai
                                    if ($item['rhk_dinilai'] == 0) {
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
                                <tr>
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
                                                <div class="fw-bold text-dark text-truncate mb-1" style="max-width: 200px;" title="<?= esc($item['pegawai']['nama_lengkap']) ?>"><?= esc($item['pegawai']['nama_lengkap']) ?></div>
                                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2"><?= esc($item['pegawai']['jabatan'] ?? 'Staf') ?></span>
                                                    <span><?= esc($item['pegawai']['nip']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal px-2 py-1"><i class="bi bi-building me-1 opacity-50"></i> <?= esc($item['pegawai']['unit'] ?? '-') ?></span>
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
                                            <span class="fs-5 fw-medium text-dark"><?= $item['jumlah_rhk'] ?></span>
                                        </td>
                                    <?php else: ?>
                                        <td class="text-center">
                                            <span class="fs-5 fw-medium text-dark"><?= $item['jumlah_rhk'] ?></span>
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill px-3 py-1 border shadow-sm">
                                            <span class="fw-bold <?= $item['rhk_dinilai'] == $item['jumlah_rhk'] && $item['jumlah_rhk'] > 0 ? 'text-success' : 'text-primary' ?> fs-6"><?= $item['rhk_dinilai'] ?></span>
                                            <span class="text-muted mx-1">/</span>
                                            <span class="text-muted"><?= $item['jumlah_rhk'] ?></span>
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
        </div>
    </div>
</div>

<script>
function showSkeletonAndSubmit() {
    // Show skeleton, hide actual content
    document.querySelectorAll('.skeleton-hide').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.skeleton-show').forEach(el => el.style.display = 'block');
    
    document.getElementById('tableContent').style.display = 'none';
    document.getElementById('tableLoader').style.display = 'block';

    // Submit form after a tiny delay so UI has time to update
    setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 50);
}
</script>
<?= $this->endSection() ?>
