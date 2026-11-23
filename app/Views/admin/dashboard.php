<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$formatNama = static function (?string $raw): string {
    $raw = trim((string) $raw);
    if ($raw === '') {
        return '-';
    }
    $parts = explode(',', $raw);
    $main  = ucwords(strtolower(trim($parts[0])));
    if (count($parts) > 1) {
        unset($parts[0]);
        return $main . ', ' . implode(', ', array_map('trim', $parts));
    }
    return $main;
};

$pctPartInitial = ($totalPegawai > 0) ? round(($partisipasiAktif / $totalPegawai) * 100, 1) : 0;
$sebaranItems = [
    ['key' => 'Sangat Baik',     'label' => 'Sangat Baik (> 100%–150%)', 'count' => (int) ($sebaranKinerja['sangat_baik'] ?? 0),     'color' => '#10b981'],
    ['key' => 'Baik',            'label' => 'Baik (> 90%–100%)',         'count' => (int) ($sebaranKinerja['baik'] ?? 0),            'color' => '#0d6efd'],
    ['key' => 'Butuh Perbaikan', 'label' => 'Butuh Perbaikan (> 75%–90%)','count' => (int) ($sebaranKinerja['butuh_perbaikan'] ?? 0),'color' => '#7c3aed'],
    ['key' => 'Kurang',          'label' => 'Kurang (> 25%–75%)',        'count' => (int) ($sebaranKinerja['kurang'] ?? 0),          'color' => '#f59e0b'],
    ['key' => 'Sangat Kurang',   'label' => 'Sangat Kurang (≤ 25%)',     'count' => (int) ($sebaranKinerja['sangat_kurang'] ?? 0),   'color' => '#dc2626'],
    ['key' => 'Belum Dinilai',   'label' => 'Belum Dinilai (0%)',        'count' => (int) ($sebaranKinerja['belum_dinilai'] ?? $sebaranKinerja['belum_mengisi'] ?? 0), 'color' => '#64748b'],
];
?>

<!-- Toolbar Filter Periode Kinerja (8-Point Grid) -->
<div class="d-flex justify-content-start align-items-center flex-wrap gap-2 mb-4 bento-stagger bento-stagger-1">
    <form id="formKinerja" class="m-0 d-flex flex-wrap gap-2 align-items-center" aria-label="Filter Periode Kinerja">
        <span class="text-muted fw-semibold me-1" style="font-size: 0.75rem;">Periode Kinerja:</span>
        <label for="bulan_kinerja" class="visually-hidden">Pilih Bulan Kinerja</label>
        <select class="form-select form-select-sm dash-filter-control" id="bulan_kinerja" name="bulan_kinerja" style="width: auto; min-width: 160px;" onchange="updateKinerjaData()">
            <option value="all" <?= ($bulan_kinerja === 'all') ? 'selected' : '' ?>>Semua Bulan (Setahun)</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i; ?>" <?= ((string) $bulan_kinerja === (string) $i) ? 'selected' : ''; ?>><?= bulan_indo($i) ?></option>
            <?php endfor; ?>
        </select>
        <label for="tahun_kinerja" class="visually-hidden">Pilih Tahun Kinerja</label>
        <select name="tahun_kinerja" id="tahun_kinerja" class="form-select form-select-sm dash-filter-control" style="width: auto; min-width: 128px;" onchange="updateKinerjaData()">
            <?php foreach ($daftar_tahun as $tahun_item): ?>
                <option value="<?= esc($tahun_item) ?>" <?= ((string) $tahun_kinerja === (string) $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Bagian 1: Radar ECC & Metrik Utama -->
<div class="row g-4 mb-4 bento-stagger bento-stagger-1">
    <div class="col-lg-8 d-flex flex-column">
        <?= view('partials/_dashboard_ecc_card', [
            'prodiData'    => $prodiData,
            'daftar_tahun' => $daftar_tahun,
            'tahun_ecc'    => $tahun_ecc,
            'eccFrom'      => 'admin',
        ]) ?>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">
        <div class="bento-card dash-kpi-primary flex-fill">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="dash-kpi-label">Rata-Rata Kinerja Bulanan</div>
                <div class="dash-kpi-value" id="valRataRataKinerja" aria-live="polite" aria-atomic="true">
                    <?= number_format((float) ($rataRataValue ?? 0), 2, ',', '.') ?>
                </div>
                <div class="dash-kpi-meta">Rata-rata seluruh pegawai</div>
            </div>
        </div>

        <div class="bento-card dash-kpi-secondary flex-fill">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="dash-kpi-label text-muted">Tingkat Partisipasi Aktif</div>
                <div class="dash-kpi-value text-dark" id="valPartisipasi" aria-live="polite" aria-atomic="true">
                    <?= number_format((float) $pctPartInitial, 1, ',', '.') ?>%
                </div>
                <div class="dash-kpi-meta fw-semibold text-primary">
                    <span id="valPartisipasiCount" class="num-tabular"><?= esc($partisipasiAktif) ?></span>
                    dari
                    <span id="valTotalPegawai" class="num-tabular"><?= esc($totalPegawai) ?></span>
                    Pegawai Aktif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian 2: Ringkasan Kinerja Unit -->
<div class="bento-stagger bento-stagger-2">
    <div class="mb-3 mt-4">
        <h2 class="dash-section-title">Ringkasan Kinerja Unit</h2>
        <p class="dash-section-subtitle">Performa unit kerja berdasarkan penilaian laporan pada periode terpilih.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="bento-card h-100">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">Sebaran Predikat Kinerja</h3>
                </div>
                <div class="bento-body p-4">
                    <div class="chart-container" style="height: 224px;">
                        <canvas id="sebaranChart" role="img" aria-label="Grafik Sebaran Predikat Kinerja Organisasi"></canvas>
                    </div>
                    <div class="dash-legend-list" id="sebaranLegendList" aria-label="Filter detail pegawai berdasarkan predikat">
                        <?php foreach ($sebaranItems as $item): ?>
                            <button type="button"
                                    class="dash-legend-btn"
                                    data-predikat-key="<?= esc($item['key']) ?>"
                                    onclick="fetchChartDetail('sebaran', '<?= esc($item['key'], 'js') ?>')">
                                <span class="d-inline-flex align-items-center gap-2 text-truncate">
                                    <span class="dash-legend-dot" style="background-color: <?= esc($item['color']) ?>;"></span>
                                    <span class="text-truncate"><?= esc($item['key']) ?></span>
                                </span>
                                <span class="text-muted num-tabular flex-shrink-0" data-legend-count="<?= esc($item['key']) ?>">(<?= $item['count'] ?>)</span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="bento-card h-100">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">Tren Kinerja Bulanan Organisasi</h3>
                </div>
                <div class="bento-body p-4">
                    <div class="chart-container">
                        <canvas id="trendChart" role="img" aria-label="Grafik Tren Kinerja Bulanan Organisasi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian 3: Peringkat Unit & Pegawai (Header 64px & Baris 64px Seragam) -->
<div class="row g-4 mb-4 bento-stagger bento-stagger-3">
    <!-- Top 5 Unit Kerja -->
    <div class="col-lg-4 d-flex">
        <div class="bento-card h-100 w-100 d-flex flex-column">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Top 5 Unit Kerja</h3>
                <a href="#unitPerformanceSection" class="dash-action-pill flex-shrink-0">
                    <span>Selengkapnya</span>
                    <i class="bi bi-arrow-down-short" aria-hidden="true"></i>
                </a>
            </div>
            <div class="bento-body p-0 flex-fill d-flex flex-column">
                <div class="table-responsive flex-fill">
                    <table class="table table-borderless table-hover align-middle mb-0 dash-leaderboard-table">
                        <tbody id="tbodyTop5Unit">
                            <?php if (empty($top5Unit)): ?>
                                <tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Belum ada data.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($top5Unit as $i => $u): ?>
                                    <?php
                                        $rankClass = 'dash-rank-default';
                                        if ($i === 0) $rankClass = 'dash-rank-1';
                                        elseif ($i === 1) $rankClass = 'dash-rank-2';
                                        elseif ($i === 2) $rankClass = 'dash-rank-3';
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle border-bottom border-light" style="width: 56px; padding: 8px 16px;">
                                            <span class="dash-rank-badge <?= $rankClass ?>"><?= $i + 1 ?></span>
                                        </td>
                                        <td class="border-bottom border-light" style="padding: 8px 4px;">
                                            <button type="button"
                                                    class="dash-row-btn"
                                                    onclick="openUnitDetailModal('<?= esc($u['nama'], 'js') ?>')"
                                                    title="<?= esc($u['nama']) ?>">
                                                <span class="dash-unit-name"><?= esc($u['nama']) ?></span>
                                            </button>
                                        </td>
                                        <td class="text-end border-bottom border-light" style="width: 112px; padding: 8px 16px;">
                                            <span class="dash-score-pill dash-score-success">
                                                <?= $u['rata'] > 0 ? number_format((float) $u['rata'], 2, ',', '.') : '-' ?>
                                            </span>
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

    <!-- Top 5 Pegawai Berkinerja Terbaik -->
    <div class="col-lg-4 d-flex">
        <div class="bento-card h-100 w-100 d-flex flex-column">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Top 5 Pegawai Berkinerja Terbaik</h3>
            </div>
            <div class="bento-body p-0 flex-fill d-flex flex-column">
                <div class="table-responsive flex-fill">
                    <table class="table table-borderless table-hover align-middle mb-0 dash-leaderboard-table">
                        <tbody id="tbodyTop5">
                            <?php if (empty($top5)): ?>
                                <tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Belum ada data.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($top5 as $i => $t): ?>
                                    <?php
                                        $rankClass = 'dash-rank-default';
                                        if ($i === 0) $rankClass = 'dash-rank-1';
                                        elseif ($i === 1) $rankClass = 'dash-rank-2';
                                        elseif ($i === 2) $rankClass = 'dash-rank-3';
                                        $namaRapih = $formatNama($t['staf']['nama_lengkap'] ?? '');
                                    ?>
                                    <tr>
                                        <td class="text-center align-middle border-bottom border-light" style="width: 56px; padding: 8px 16px;">
                                            <span class="dash-rank-badge <?= $rankClass ?>"><?= $i + 1 ?></span>
                                        </td>
                                        <td class="border-bottom border-light" style="padding: 8px 4px;">
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.875rem;" title="<?= esc($namaRapih) ?>"><?= esc($namaRapih) ?></span>
                                                <?php if (($t['staf']['status_pegawai'] ?? '') === 'mutasi_keluar'): ?>
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1 flex-shrink-0" style="font-size: 0.75rem;">Mutasi</span>
                                                <?php elseif (($t['staf']['status_pegawai'] ?? '') === 'pensiun'): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1 flex-shrink-0" style="font-size: 0.75rem;">Pensiun</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                        </td>
                                        <td class="text-end border-bottom border-light" style="width: 112px; padding: 8px 16px;">
                                            <?php if ((float) $t['rata_rata'] == 0 || empty($t['dinilai'])): ?>
                                                <span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>
                                            <?php else: ?>
                                                <span class="dash-score-pill dash-score-success"><?= number_format((float) $t['rata_rata'], 2, ',', '.') ?></span>
                                            <?php endif; ?>
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

    <!-- Perlu Perhatian Khusus -->
    <div class="col-lg-4 d-flex">
        <div class="bento-card h-100 w-100 d-flex flex-column">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Perlu Perhatian Khusus</h3>
            </div>
            <div class="bento-body p-0 flex-fill d-flex flex-column">
                <div class="table-responsive flex-fill">
                    <table class="table table-borderless table-hover align-middle mb-0 dash-leaderboard-table">
                        <tbody id="tbodyBottom5">
                            <?php if (empty($bottom5)): ?>
                                <tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Tidak ada pegawai yang perlu perhatian khusus.</div></td></tr>
                            <?php else: ?>
                                <?php foreach ($bottom5 as $i => $b): ?>
                                    <?php $namaRapih = $formatNama($b['staf']['nama_lengkap'] ?? ''); ?>
                                    <tr>
                                        <td class="text-center align-middle border-bottom border-light" style="width: 56px; padding: 8px 16px;">
                                            <span class="dash-rank-badge dash-rank-alert"><?= $i + 1 ?></span>
                                        </td>
                                        <td class="border-bottom border-light" style="padding: 8px 4px;">
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.875rem;" title="<?= esc($namaRapih) ?>"><?= esc($namaRapih) ?></span>
                                                <?php if (($b['staf']['status_pegawai'] ?? '') === 'mutasi_keluar'): ?>
                                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1 flex-shrink-0" style="font-size: 0.75rem;">Mutasi</span>
                                                <?php elseif (($b['staf']['status_pegawai'] ?? '') === 'pensiun'): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1 flex-shrink-0" style="font-size: 0.75rem;">Pensiun</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                        </td>
                                        <td class="text-end border-bottom border-light" style="width: 112px; padding: 8px 16px;">
                                            <?php if ((float) $b['rata_rata'] == 0 || empty($b['dinilai'])): ?>
                                                <span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>
                                            <?php else: ?>
                                                <span class="dash-score-pill dash-score-danger"><?= number_format((float) $b['rata_rata'], 2, ',', '.') ?></span>
                                            <?php endif; ?>
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
</div>

<!-- Bagian 4: Grafik Kinerja Unit -->
<div class="bento-stagger bento-stagger-4" id="unitPerformanceSection">
    <div class="mb-3 mt-4">
        <h2 class="dash-section-title">Grafik Kinerja Unit</h2>
        <p class="dash-section-subtitle">Klik batang unit kerja untuk melihat rincian penilaian setiap anggota.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bento-card">
                <div class="bento-body p-4">
                    <?php if (empty($chartPegawaiUnitLabels)): ?>
                        <div class="alert alert-light border text-center text-muted mb-0 rounded-3 p-4">Belum ada data rekap kinerja staf.</div>
                    <?php else: ?>
                        <div class="dash-bar-scroll">
                            <div class="performance-chart-container" id="unitChartWrap">
                                <canvas id="unitPerformanceChart" role="img" aria-label="Grafik Batang Kinerja Seluruh Unit Kerja Organisasi"></canvas>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Anggota Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-labelledby="unitDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h2 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center flex-wrap gap-2" id="unitDetailModalLabel" style="font-size: 0.95rem;">
                    <span>Detail Pegawai:</span>
                    <span id="modalUnitName" class="text-primary"></span>
                    <span id="modalPeriodBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;"></span>
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="min-width: 44px; min-height: 44px;"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive bg-white border rounded-3 overflow-hidden">
                    <table class="table table-hover align-middle mb-0 table-bento" style="font-size: 0.875rem;">
                        <thead class="bg-light text-muted text-uppercase" style="font-size: 0.75rem;">
                            <tr>
                                <th class="px-3 py-3">Nama Pegawai</th>
                                <th class="text-center px-3 py-3">Target &amp; Tambahan</th>
                                <th class="text-center px-3 py-3">Rata-Rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody" class="border-top-0"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" style="min-height: 36px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Chart (Sebaran & Tren) -->
<div class="modal fade" id="detailChartModal" tabindex="-1" aria-labelledby="detailChartTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light px-4 py-3">
                <h2 class="modal-title fw-bold text-dark mb-0" id="detailChartTitle" style="font-size: 0.95rem;">Detail Kinerja</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="min-width: 44px; min-height: 44px;"></button>
            </div>
            <div class="modal-body p-4" id="detailChartBody"></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= view('partials/_dashboard_ecc_script', ['eccFrom' => 'admin']) ?>

<script>
window.pageCharts = window.pageCharts || {};
window.adminUnitStatsCache = {};
window.adminUnitLabelsCache = [];

function getUnitChartHeight(count) {
    return Math.max(224, (count || 0) * 32);
}

function openUnitDetailModal(selectedUnit) {
    const details = window.adminUnitStatsCache[selectedUnit]?.anggota || [];
    const nameEl = document.getElementById('modalUnitName');
    if (nameEl) nameEl.innerText = selectedUnit;

    const bulanSelect = document.getElementById('bulan_kinerja');
    const tahunSelect = document.getElementById('tahun_kinerja');
    const periodText = (bulanSelect && tahunSelect && bulanSelect.selectedIndex >= 0)
        ? `${bulanSelect.options[bulanSelect.selectedIndex].text} ${tahunSelect.value}`
        : '';
    const periodBadgeEl = document.getElementById('modalPeriodBadge');
    if (periodBadgeEl) periodBadgeEl.innerText = periodText;

    let tbody = '';
    if (details.length > 0) {
        details.forEach(item => {
            const isBelum = !item.dinilai || Number(item.rata_rata || 0) === 0;
            const scoreClass = window.eccDash.getPredikatScoreClass(item.rata_rata, item.dinilai);
            const scoreText = isBelum ? 'Belum Dinilai' : window.eccDash.formatAngkaId(item.rata_rata, 2);
            const namaRapih = window.eccDash.formatNamaTitleCase(item.nama);
            const pokokVal = item.total_pokok !== undefined ? item.total_pokok : item.total_laporan;
            const tambVal = item.total_tambahan || 0;

            tbody += `
                <tr>
                    <td class="px-3 py-3 border-bottom border-light">
                        <div class="fw-semibold text-dark">${window.eccDash.escapeHtml(namaRapih)}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(item.jabatan || '-')}</div>
                    </td>
                    <td class="text-center px-3 py-3 border-bottom border-light">
                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(pokokVal)} Pokok</span>
                            <span class="badge ${tambVal > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border'} rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(tambVal)} Tambahan</span>
                        </div>
                    </td>
                    <td class="text-center px-3 py-3 border-bottom border-light">
                        <span class="dash-score-pill ${scoreClass}">${scoreText}</span>
                    </td>
                </tr>`;
        });
    } else {
        tbody = '<tr><td colspan="3" class="text-center text-muted p-4">Belum ada data pegawai.</td></tr>';
    }

    document.getElementById('unitDetailTbody').innerHTML = tbody;
    const unitModalEl = document.getElementById('unitDetailModal');
    const unitModal = bootstrap.Modal.getInstance(unitModalEl) || new bootstrap.Modal(unitModalEl);
    unitModal.show();
}

function updateSebaranLegendCounts(sebaran) {
    if (!sebaran) return;
    const map = {
        'Sangat Baik': sebaran.sangat_baik || 0,
        'Baik': sebaran.baik || 0,
        'Butuh Perbaikan': sebaran.butuh_perbaikan || 0,
        'Kurang': sebaran.kurang || 0,
        'Sangat Kurang': sebaran.sangat_kurang || 0,
        'Belum Dinilai': sebaran.belum_dinilai !== undefined ? sebaran.belum_dinilai : (sebaran.belum_mengisi || 0)
    };
    Object.entries(map).forEach(([key, count]) => {
        const el = document.querySelector(`[data-legend-count="${key}"]`);
        if (el) el.innerText = `(${count})`;
    });
}

async function updateKinerjaData() {
    const bulanKinerja = document.getElementById('bulan_kinerja').value;
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&bulan_kinerja=${encodeURIComponent(bulanKinerja)}&tahun_kinerja=${encodeURIComponent(tahunKinerja)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (!data) return;

        const currentRata = window.eccDash.parseAngkaId(document.getElementById('valRataRataKinerja')?.innerText);
        window.eccDash.animateValue('valRataRataKinerja', currentRata, data.rataRataValue || 0, 650, '', 2);

        const partisipasi = data.partisipasiAktif || 0;
        const total = data.totalPegawai || 0;
        const pctPart = total > 0 ? Number(((partisipasi / total) * 100).toFixed(1)) : 0;
        const currentPct = window.eccDash.parseAngkaId(document.getElementById('valPartisipasi')?.innerText);
        window.eccDash.animateValue('valPartisipasi', currentPct, pctPart, 650, '%', 1);

        const currentPartCount = window.eccDash.parseAngkaId(document.getElementById('valPartisipasiCount')?.innerText);
        window.eccDash.animateValue('valPartisipasiCount', currentPartCount, partisipasi, 650, '', 0);

        const currentTotPeg = window.eccDash.parseAngkaId(document.getElementById('valTotalPegawai')?.innerText);
        window.eccDash.animateValue('valTotalPegawai', currentTotPeg, total, 650, '', 0);

        // Perbarui Top 5 Unit (konsisten SSR & empty state)
        let htmlUnit = '';
        if (data.top5Unit && data.top5Unit.length > 0) {
            data.top5Unit.forEach((u, i) => {
                const rankClass = i === 0 ? 'dash-rank-1' : (i === 1 ? 'dash-rank-2' : (i === 2 ? 'dash-rank-3' : 'dash-rank-default'));
                const rataStr = (u.rata !== undefined && u.rata !== null && Number(u.rata) > 0)
                    ? window.eccDash.formatAngkaId(u.rata, 2)
                    : '-';
                const escapedNama = window.eccDash.escapeHtml(u.nama);
                htmlUnit += `
                    <tr>
                        <td class="text-center align-middle border-bottom border-light" style="width: 56px; padding: 8px 16px;">
                            <span class="dash-rank-badge ${rankClass}">${i + 1}</span>
                        </td>
                        <td class="border-bottom border-light" style="padding: 8px 4px;">
                            <button type="button" class="dash-row-btn" data-unit-open="${escapedNama}" title="${escapedNama}">
                                <span class="dash-unit-name">${escapedNama}</span>
                            </button>
                        </td>
                        <td class="text-end border-bottom border-light" style="width: 112px; padding: 8px 16px;">
                            <span class="dash-score-pill dash-score-success">${rataStr}</span>
                        </td>
                    </tr>`;
            });
        } else {
            htmlUnit = '<tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Belum ada data.</div></td></tr>';
        }
        const tbodyTop5Unit = document.getElementById('tbodyTop5Unit');
        tbodyTop5Unit.innerHTML = htmlUnit;
        tbodyTop5Unit.querySelectorAll('[data-unit-open]').forEach(btn => {
            btn.addEventListener('click', () => openUnitDetailModal(btn.getAttribute('data-unit-open')));
        });

        // Perbarui Doughnut & Legenda Sebaran
        if (window.pageCharts['sebaranChart'] && data.sebaranKinerja) {
            window.pageCharts['sebaranChart'].data.datasets[0].data = [
                data.sebaranKinerja.sangat_baik || 0,
                data.sebaranKinerja.baik || 0,
                data.sebaranKinerja.butuh_perbaikan || 0,
                data.sebaranKinerja.kurang || 0,
                data.sebaranKinerja.sangat_kurang || 0,
                data.sebaranKinerja.belum_dinilai !== undefined ? data.sebaranKinerja.belum_dinilai : (data.sebaranKinerja.belum_mengisi || 0)
            ];
            window.pageCharts['sebaranChart'].update();
            updateSebaranLegendCounts(data.sebaranKinerja);
        }

        // Perbarui Tren Bulanan
        if (window.pageCharts['trendChart']) {
            window.pageCharts['trendChart'].data.datasets[0].data = data.trendBulananData || [];
            window.pageCharts['trendChart'].update();
        }

        // Perbarui Top 5 & Perlu Perhatian Khusus (konsisten dengan SSR)
        const renderEmployeeTable = (arr, isTop) => {
            if (!arr || arr.length === 0) {
                return isTop
                    ? '<tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Belum ada data.</div></td></tr>'
                    : '<tr class="dash-empty-row"><td colspan="3" class="p-0 border-0"><div class="dash-empty-center">Tidak ada pegawai yang perlu perhatian khusus.</div></td></tr>';
            }
            let html = '';
            arr.forEach((item, i) => {
                const rankClass = isTop
                    ? (i === 0 ? 'dash-rank-1' : (i === 1 ? 'dash-rank-2' : (i === 2 ? 'dash-rank-3' : 'dash-rank-default')))
                    : 'dash-rank-alert';
                const statusBadge = window.eccDash.getStatusPegawaiBadge(item.staf?.status_pegawai);
                const namaRapih = window.eccDash.formatNamaTitleCase(item.staf?.nama_lengkap);
                const rataVal = Number(item.rata_rata || 0);
                const dinilaiVal = item.dinilai !== undefined ? Number(item.dinilai) : 1;

                let scoreHtml = '';
                if (rataVal === 0 || dinilaiVal === 0) {
                    scoreHtml = '<span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>';
                } else {
                    const cls = isTop ? 'dash-score-success' : 'dash-score-danger';
                    scoreHtml = `<span class="dash-score-pill ${cls}">${window.eccDash.formatAngkaId(rataVal, 2)}</span>`;
                }

                html += `
                    <tr>
                        <td class="text-center align-middle border-bottom border-light" style="width: 56px; padding: 8px 16px;">
                            <span class="dash-rank-badge ${rankClass}">${i + 1}</span>
                        </td>
                        <td class="border-bottom border-light" style="padding: 8px 4px;">
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.875rem;" title="${window.eccDash.escapeHtml(namaRapih)}">${window.eccDash.escapeHtml(namaRapih)}</span>${statusBadge}
                            </div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="${window.eccDash.escapeHtml(item.staf?.jabatan || '-')}">${window.eccDash.escapeHtml(item.staf?.jabatan || '-')}</div>
                        </td>
                        <td class="text-end border-bottom border-light" style="width: 112px; padding: 8px 16px;">
                            ${scoreHtml}
                        </td>
                    </tr>`;
            });
            return html;
        };

        document.getElementById('tbodyTop5').innerHTML = renderEmployeeTable(data.top5, true);
        document.getElementById('tbodyBottom5').innerHTML = renderEmployeeTable(data.bottom5, false);

        // Perbarui Grafik Unit & Hitung Ulang Tinggi Kanvas
        window.adminUnitLabelsCache = data.chartPegawaiUnitLabels || [];
        window.adminUnitStatsCache = data.unitStats || {};
        if (window.pageCharts['unitChart']) {
            const wrap = document.getElementById('unitChartWrap');
            if (wrap) wrap.style.height = getUnitChartHeight(window.adminUnitLabelsCache.length) + 'px';
            window.pageCharts['unitChart'].data.labels = window.adminUnitLabelsCache;
            window.pageCharts['unitChart'].data.datasets[0].data = data.chartPegawaiUnitData || [];
            window.pageCharts['unitChart'].resize();
            window.pageCharts['unitChart'].update();
        }
    } catch (err) {
        console.error('Gagal mengambil data kinerja:', err);
    }
}

function fetchChartDetail(mode, label, bulanVal = null) {
    const tahun = document.getElementById('tahun_kinerja').value;
    let bulan = document.getElementById('bulan_kinerja').value;
    if (mode === 'tren' && bulanVal) {
        bulan = bulanVal;
    }

    let title = '';
    let url = `<?= site_url('dashboard/api-detail-chart') ?>?mode=${encodeURIComponent(mode)}&tahun=${encodeURIComponent(tahun)}&bulan=${encodeURIComponent(bulan)}`;

    if (mode === 'sebaran') {
        title = 'Pegawai Predikat ' + label;
        url += '&kategori=' + encodeURIComponent(label);
    } else if (mode === 'tren') {
        title = 'Detail Kinerja - ' + label + ' ' + tahun;
    }

    document.getElementById('detailChartTitle').innerText = title;
    document.getElementById('detailChartBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat data</span></div>
            <div class="mt-2 text-muted" style="font-size: 0.875rem;">Memuat data...</div>
        </div>`;

    const chartModalEl = document.getElementById('detailChartModal');
    const modal = bootstrap.Modal.getInstance(chartModalEl) || new bootstrap.Modal(chartModalEl);
    modal.show();

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                if (!res.data || res.data.length === 0) {
                    document.getElementById('detailChartBody').innerHTML = '<div class="alert alert-light border text-muted mb-0">Tidak ada data pegawai pada kategori ini.</div>';
                    return;
                }

                let html = '<div class="table-responsive"><table class="table table-hover align-middle border mb-0" style="font-size: 0.875rem;"><thead class="table-light" style="font-size: 0.75rem;"><tr><th class="px-3 py-2">Nama Pegawai</th><th class="text-center px-3 py-2">Nilai Rata-Rata</th></tr></thead><tbody>';
                res.data.forEach(p => {
                    const isBelum = (p.dinilai === 0 || Number(p.rata_rata || 0) === 0);
                    const scoreClass = window.eccDash.getPredikatScoreClass(p.rata_rata, p.dinilai !== undefined ? p.dinilai : 1);
                    const scoreText = isBelum ? 'Belum Dinilai' : window.eccDash.formatAngkaId(p.rata_rata, 2);
                    const namaRapih = window.eccDash.formatNamaTitleCase(p.nama);
                    const statusTag = window.eccDash.getStatusPegawaiBadge(p.status_pegawai);

                    html += `<tr>
                        <td class="px-3 py-2">
                            <div class="fw-semibold text-dark d-flex align-items-center gap-1 flex-wrap"><span>${window.eccDash.escapeHtml(namaRapih)}</span>${statusTag}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(p.jabatan || '-')} • ${window.eccDash.escapeHtml(p.unit || '-')}</div>
                        </td>
                        <td class="text-center px-3 py-2">
                            <span class="dash-score-pill ${scoreClass}">${scoreText}</span>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                document.getElementById('detailChartBody').innerHTML = html;
            } else {
                document.getElementById('detailChartBody').innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat data. Silakan coba lagi.</div>';
            }
        })
        .catch(() => {
            document.getElementById('detailChartBody').innerHTML = '<div class="alert alert-danger mb-0">Gagal memuat data. Silakan coba lagi.</div>';
        });
}

document.addEventListener('DOMContentLoaded', function () {
    window.eccDash.animateValue('valRataRataKinerja', 0, <?= (float) ($rataRataValue ?? 0) ?>, 650, '', 2);
    window.eccDash.animateValue('valPartisipasi', 0, <?= (float) $pctPartInitial ?>, 650, '%', 1);
    window.eccDash.animateValue('valPartisipasiCount', 0, <?= (int) $partisipasiAktif ?>, 650, '', 0);
    window.eccDash.animateValue('valTotalPegawai', 0, <?= (int) $totalPegawai ?>, 650, '', 0);

    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) {
            window.pageCharts[key].destroy();
            delete window.pageCharts[key];
        }
    }

    const prodiData = <?= json_encode($prodiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    window.eccDash.initEccRadar(prodiData, '<?= esc($tahun_ecc, 'js') ?>', window.pageCharts);

    if (typeof Chart === 'undefined') return;

    // 1. Grafik Batang Kinerja Unit
    <?php if (!empty($chartPegawaiUnitLabels)): ?>
    const ctxUnit = document.getElementById('unitPerformanceChart');
    if (ctxUnit) {
        window.adminUnitStatsCache = <?= json_encode($unitStats ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        window.adminUnitLabelsCache = <?= json_encode($chartPegawaiUnitLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const unitData = <?= json_encode($chartPegawaiUnitData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        const wrap = document.getElementById('unitChartWrap');
        if (wrap) wrap.style.height = getUnitChartHeight(window.adminUnitLabelsCache.length) + 'px';

        window.pageCharts['unitChart'] = new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: window.adminUnitLabelsCache,
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: unitData,
                    backgroundColor: '#1e40af',
                    borderColor: '#1e3a8a',
                    borderWidth: 0,
                    borderRadius: 4,
                    hoverBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: window.eccDash.chartAnimationOption(),
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        callbacks: {
                            title: (items) => items[0] ? window.adminUnitLabelsCache[items[0].dataIndex] : '',
                            label: (ctx) => 'Rata-Rata: ' + window.eccDash.formatAngkaId(ctx.parsed.x || 0, 2)
                        }
                    }
                },
                scales: {
                    x: {
                        position: 'top',
                        beginAtZero: true,
                        suggestedMax: 100,
                        grace: '8%',
                        grid: { borderDash: [4, 4], color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: '#475569',
                            font: { size: 12 },
                            callback: function (value) {
                                const full = this.getLabelForValue(value) || '';
                                const maxChars = window.innerWidth < 768 ? 18 : 42;
                                return full.length > maxChars ? full.slice(0, maxChars - 1) + '…' : full;
                            }
                        }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const selectedUnit = window.adminUnitLabelsCache[elements[0].index];
                        if (selectedUnit) openUnitDetailModal(selectedUnit);
                    }
                },
                onHover: (e, elements) => {
                    if (e.native && e.native.target) {
                        e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // 2. Doughnut Sebaran Predikat Kinerja (Legenda interaktif keyboard berada di DOM)
    const ctxSebaran = document.getElementById('sebaranChart');
    if (ctxSebaran) {
        const sebaranKeys = ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang', 'Belum Dinilai'];
        window.pageCharts['sebaranChart'] = new Chart(ctxSebaran, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Baik (> 100%–150%)', 'Baik (> 90%–100%)', 'Butuh Perbaikan (> 75%–90%)', 'Kurang (> 25%–75%)', 'Sangat Kurang (≤ 25%)', 'Belum Dinilai (0%)'],
                datasets: [{
                    data: [
                        <?= (int) ($sebaranKinerja['sangat_baik'] ?? 0) ?>,
                        <?= (int) ($sebaranKinerja['baik'] ?? 0) ?>,
                        <?= (int) ($sebaranKinerja['butuh_perbaikan'] ?? 0) ?>,
                        <?= (int) ($sebaranKinerja['kurang'] ?? 0) ?>,
                        <?= (int) ($sebaranKinerja['sangat_kurang'] ?? 0) ?>,
                        <?= (int) ($sebaranKinerja['belum_dinilai'] ?? $sebaranKinerja['belum_mengisi'] ?? 0) ?>
                    ],
                    backgroundColor: ['#10b981', '#0d6efd', '#7c3aed', '#f59e0b', '#dc2626', '#64748b'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                animation: window.eccDash.chartAnimationOption(),
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#0f172a', padding: 12 }
                },
                onHover: (e, elements) => {
                    if (e.native && e.native.target) {
                        e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                    }
                },
                onClick: (evt, activeElements) => {
                    if (activeElements.length > 0) {
                        const key = sebaranKeys[activeElements[0].index];
                        if (key) fetchChartDetail('sebaran', key);
                    }
                }
            }
        });
    }

    // 3. Tren Kinerja Bulanan Organisasi (null untuk bulan belum dinilai agar tidak terjun ke 0)
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        window.pageCharts['trendChart'] = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: <?= json_encode($trendBulananData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.10)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    spanGaps: false,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: window.eccDash.chartAnimationOption(),
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ctx.parsed.y === null ? 'Belum dinilai' : 'Rata-Rata: ' + window.eccDash.formatAngkaId(ctx.parsed.y, 2)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100,
                        grace: '8%',
                        grid: { borderDash: [4, 4], color: '#f1f5f9', drawBorder: false }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                },
                onHover: (e, elements) => {
                    if (e.native && e.native.target) {
                        e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                    }
                },
                onClick: (evt, activeElements, chart) => {
                    if (activeElements.length > 0) {
                        const idx = activeElements[0].index;
                        fetchChartDetail('tren', chart.data.labels[idx], idx + 1);
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
