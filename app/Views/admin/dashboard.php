<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>



<?= $this->section('content') ?>

<!-- 1. ECC DASHBOARD (TOP SECTION AS REQUESTED) -->
<div class="row g-4 mb-5">
    <div class="col-lg-8 d-flex flex-column">
        <div class="bento-card border-primary border-top border-4 flex-fill">
            <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-2 mb-2 gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-bento text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                    <div>
                        <div class="mb-0 fw-bold text-dark fs-6">Evidence Command Center (ECC)</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Monitoring Pemenuhan Standar Mutu</div>
                    </div>
                </div>
                
                <!-- Filter Tahun ECC -->
                <form id="formEcc" class="m-0 d-flex gap-2">
                    <select name="tahun_ecc" id="tahun_ecc" class="form-select form-select-sm filter-select fw-bold text-primary-bento" style="width: auto; cursor:pointer;" onchange="updateEccData()">
                        <?php foreach ($daftar_tahun as $tahun_item): ?>
                            <option value="<?= esc($tahun_item) ?>" <?= ($tahun_ecc == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <div class="bento-body pt-2">
                <ul class="nav nav-pills ecc-tabs mb-4" id="prodiTab" role="tablist">
                    <?php foreach($prodiData as $prodi): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3" style="font-size: 0.85rem;" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="tab-content border-0 p-0 shadow-none bg-transparent" id="prodiTabContent">
                    <?php foreach($prodiData as $prodi): ?>
                        <div class="tab-pane fade" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
                            <div class="row justify-content-center">
                                <div class="col-12 px-0 px-md-3">
                                    <div class="text-center mb-3">
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                            Rangkuman Skor LED: <strong class="text-primary-bento"><?= esc($prodi['nama_prodi']) ?></strong>
                                        </span>
                                    </div>
                                    <?php if (empty($prodi['chart_labels'])): ?>
                                        <div class="alert alert-light text-center border">Belum ada data Kategori LED untuk tahun ini.</div>
                                    <?php else: ?>
                                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                                        <p class="text-center text-muted small mt-2"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Rata-rata Kinerja -->
        <div class="bento-card bg-primary-bento text-white flex-fill shadow-sm" style="min-height:150px;">
            <div class="bento-body p-4 d-flex flex-column justify-content-center h-100">
                <div class="stat-label text-white-50 mb-1">Rata-Rata Kinerja Bulanan</div>
                <div class="stat-value text-white mb-2" id="valRataRataKinerja">
                    <?php 
                        $rataRataValue = 0;
                        if (!empty($unitStats)) {
                            $totalAktif = 0;
                            $countAktif = 0;
                            foreach ($unitStats as $unit) {
                                if (isset($unit['anggota'])) {
                                    foreach ($unit['anggota'] as $anggota) {
                                        if ($anggota['rata_rata'] > 0) {
                                            $totalAktif += $anggota['rata_rata'];
                                            $countAktif++;
                                        }
                                    }
                                }
                            }
                            $rataRataValue = $countAktif > 0 ? round($totalAktif / $countAktif, 2) : 0;
                        }
                        echo esc($rataRataValue);
                    ?>
                </div>
                <div class="text-white-50 small"><i class="bi bi-graph-up-arrow me-1"></i> Skor Agregat Seluruh Pegawai</div>
            </div>
        </div>
        
        <!-- Partisipasi Pegawai Aktif -->
        <div class="bento-card flex-fill shadow-sm border-top border-4 border-primary" style="min-height:150px;">
            <div class="bento-body p-4 d-flex flex-column justify-content-center h-100">
                <div class="stat-label mb-1">Tingkat Partisipasi Aktif</div>
                <div class="stat-value text-dark mb-2" id="valPartisipasi">
                    <?php 
                        $pctPart = ($totalPegawai > 0) ? round(($partisipasiAktif / $totalPegawai) * 100, 1) : 0;
                        echo esc($pctPart);
                    ?><span class="fs-4">%</span>
                </div>
                <div class="text-primary small fw-bold"><i class="bi bi-people-fill me-1"></i> <span id="valPartisipasiCount"><?= esc($partisipasiAktif) ?></span> dari <span id="valTotalPegawai"><?= esc($totalPegawai) ?></span> Pegawai Aktif</div>
            </div>
        </div>
    </div>
</div>

<!-- 2. ANALISIS KINERJA UNIT -->
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">Analitik Kinerja Unit</h4>
        <p class="text-muted mb-0 small">Ringkasan performa seluruh unit berdasarkan laporan harian yang dinilai</p>
    </div>
    <form id="formKinerja" class="m-0 d-flex gap-2">
        <select class="form-select filter-select fw-bold text-primary-bento" id="bulan_kinerja" name="bulan_kinerja" style="width: auto; cursor:pointer;" onchange="updateKinerjaData()">
            <option value="all" <?= ($bulan_kinerja === 'all' || !$bulan_kinerja) ? 'selected' : '' ?>>Semua Bulan</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i; ?>" <?= ($bulan_kinerja == $i) ? 'selected' : ''; ?>><?= bulan_indo($i) ?></option>
            <?php endfor; ?>
        </select>
        <select name="tahun_kinerja" id="tahun_kinerja" class="form-select filter-select fw-bold text-primary-bento" style="width: auto; cursor:pointer;" onchange="updateKinerjaData()">
            <?php foreach ($daftar_tahun as $tahun_item): ?>
                <option value="<?= esc($tahun_item) ?>" <?= ($tahun_kinerja == $tahun_item) ? 'selected' : '' ?>>Tahun <?= esc($tahun_item) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Row Sebaran & Tren Kinerja -->
<div class="row g-4 mb-5">
    <!-- Sebaran Kinerja Doughnut Chart -->
    <div class="col-lg-4">
        <div class="bento-card h-100">
            <div class="bento-header">Sebaran Predikat Kinerja</div>
            <div class="bento-body pt-0">
                <div class="chart-container">
                    <canvas id="sebaranChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Trend Line Chart -->
    <div class="col-lg-8">
        <div class="bento-card h-100">
            <div class="bento-header">Tren Kinerja Bulanan Organisasi</div>
            <div class="bento-body pt-0">
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. LEADERBOARD UNIT & PEGAWAI -->
<div class="row g-4 mb-5">
    <!-- Kolom Kiri: Top 5 Unit -->
    <div class="col-lg-4">
        <div class="bento-card h-100 d-flex flex-column">
            <div class="bento-header d-flex justify-content-between align-items-center border-bottom pb-2 mb-2" style="height: 42px;">
                <div style="font-size: 0.85rem;" class="fw-bold"><i class="bi bi-building-up text-primary me-2"></i>Top 5 Unit Kerja</div>
                <a href="#unitPerformanceChart" class="btn btn-light rounded-pill px-2 py-0 text-primary fw-bold d-flex align-items-center" style="text-decoration: none; font-size: 0.7rem; height: 24px;">Selengkapnya <i class="bi bi-arrow-down-short fs-6"></i></a>
            </div>
            <div class="bento-body p-0 flex-fill d-flex flex-column justify-content-center">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyTop5Unit">
                            <?php 
                                $unitRanking = [];
                                if (!empty($unitStats)) {
                                    foreach ($unitStats as $unitName => $unitData) {
                                        $totalAktif = 0;
                                        $countAktif = 0;
                                        if (isset($unitData['anggota'])) {
                                            foreach ($unitData['anggota'] as $anggota) {
                                                if ($anggota['rata_rata'] > 0) {
                                                    $totalAktif += $anggota['rata_rata'];
                                                    $countAktif++;
                                                }
                                            }
                                        }
                                        $avg = $countAktif > 0 ? round($totalAktif / $countAktif, 2) : 0;
                                        $unitRanking[] = ['nama' => $unitName, 'rata' => $avg];
                                    }
                                    usort($unitRanking, function($a, $b) {
                                        return $b['rata'] <=> $a['rata'];
                                    });
                                }
                                $top5Unit = array_slice($unitRanking, 0, 5);
                            ?>
                            <?php if(empty($top5Unit)): ?>
                                <tr><td class="text-center text-muted py-4">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($top5Unit as $i => $u): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">
                                        <?php if($i == 0): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>
                                        <?php elseif($i == 1): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>
                                        <?php elseif($i == 2): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>
                                        <?php else: ?><div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;"><?= $i+1 ?></div><?php endif; ?>
                                    </td>
                                    <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="<?= esc($u['nama']) ?>"><?= esc($u['nama']) ?></div>
                                    </td>
                                    <td class="text-end pe-4 py-2 border-bottom border-light">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 shadow-sm border border-success"><?= $u['rata'] > 0 ? $u['rata'] . '%' : '-' ?></span>
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
    <!-- Top 5 -->
    <div class="col-lg-4">
        <div class="bento-card h-100">
            <div class="bento-header d-flex align-items-center text-dark border-bottom pb-2 mb-2 fw-bold" style="font-size: 0.85rem; height: 42px;"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Pegawai Berkinerja Terbaik</div>
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyTop5">
                            <?php if(empty($top5)): ?>
                                <tr><td class="text-center text-muted py-4">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($top5 as $i => $t): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">
                                        <?php if($i == 0): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>
                                        <?php elseif($i == 1): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>
                                        <?php elseif($i == 2): ?><div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>
                                        <?php else: ?><div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;"><?= $i+1 ?></div><?php endif; ?>
                                    </td>
                                    <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="<?= esc($t['staf']['nama_lengkap']) ?>"><?= esc($t['staf']['nama_lengkap']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($t['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                    </td>
                                    <td class="text-end pe-3 py-2 border-bottom border-light">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 shadow-sm border border-success" style="font-size: 0.8rem;"><?= esc($t['rata_rata']) ?></span>
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
    
    <!-- Bottom 5 -->
    <div class="col-lg-4">
        <div class="bento-card h-100">
            <div class="bento-header d-flex align-items-center text-dark border-bottom pb-2 mb-2 fw-bold" style="font-size: 0.85rem; height: 42px;"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Perlu Perhatian Khusus</div>
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody id="tbodyBottom5">
                            <?php if(empty($bottom5)): ?>
                                <tr><td class="text-center text-muted py-4">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($bottom5 as $i => $b): ?>
                                <tr style="height: 56px;">
                                    <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">
                                        <div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-danger" style="width:24px;height:24px;font-size:13px;"><?= $i+1 ?></div>
                                    </td>
                                    <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="<?= esc($b['staf']['nama_lengkap']) ?>"><?= esc($b['staf']['nama_lengkap']) ?></div>
                                        <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?>"><?= esc(trim($b['staf']['jabatan'] ?? '') ?: '-') ?></div>
                                    </td>
                                    <td class="text-end pe-3 py-2 border-bottom border-light">
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 shadow-sm border border-danger" style="font-size: 0.8rem;"><?= esc($b['rata_rata']) ?></span>
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

<!-- 4. GRAFIK KINERJA UNIT -->
<div class="d-flex align-items-center mb-3">
    <h5 class="fw-bold text-secondary mb-0"><i class="bi bi-building me-2"></i> Grafik Kinerja Unit</h5>
</div>

<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="bento-card border-top border-4 border-info">
            <div class="bento-body p-4">
                <?php if (empty($chartPegawaiUnitLabels)): ?>
                    <div class="alert alert-light border text-center text-muted mb-0 shadow-sm"><i class="bi bi-info-circle me-2"></i> Belum ada data rekap kinerja staf.</div>
                <?php else: ?>
                    <div class="performance-chart-container">
                        <canvas id="unitPerformanceChart"></canvas>
                    </div>
                    <p class="text-center text-muted small mt-3 mb-0"><i class="bi bi-info-circle me-1"></i> Klik pada grafik batang untuk melihat detail anggota unit kerja.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="unitDetailModalLabel">Detail Pegawai: <span id="modalUnitName" class="text-primary-bento"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 rounded-start">Nama Pegawai</th>
                                <th class="text-center">Target Dinilai</th>
                                <th class="text-center rounded-end">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody" class="border-top-0">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.pageCharts = window.pageCharts || {};
window.adminUnitStatsCache = {};
window.adminUnitLabelsCache = [];

async function updateEccData() {
    const tahunEcc = document.getElementById('tahun_ecc').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=ecc&tahun_ecc=${tahunEcc}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data && data.prodiData) {
            for (const [id, prodi] of Object.entries(data.prodiData)) {
                const canvasId = 'radarChart-' + id;
                if(window.pageCharts[canvasId]) {
                    const chart = window.pageCharts[canvasId];
                    chart.data.labels = prodi.chart_labels.map(label => {
                        if (label.length <= 16) return label;
                        const words = label.split(' ');
                        const lines = [];
                        let currentLine = words[0];
                        for (let i = 1; i < words.length; i++) {
                            if (currentLine.length + 1 + words[i].length <= 16) { currentLine += ' ' + words[i]; } 
                            else { lines.push(currentLine); currentLine = words[i]; }
                        }
                        lines.push(currentLine);
                        return lines;
                    });
                    chart.data.labelIds = prodi.chart_label_ids;
                    chart.data.tahun = tahunEcc;
                    chart.data.datasets[0].data = prodi.chart_data;
                    chart.update();
                }
            }
        }
    } catch(err) { console.error("Gagal mengambil data ECC:", err); }
}

async function updateKinerjaData() {
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    const bulanKinerja = document.getElementById('bulan_kinerja').value;
    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&tahun_kinerja=${tahunKinerja}&bulan_kinerja=${bulanKinerja}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if(data) {
            // Analitik Instansi (Card Top)
            const unitStats = data.unitStats || {};            // Update Summary Cards
            let rataRataValue = 0;
            let unitRanking = [];
            
            if (data.unitStats && Object.keys(data.unitStats).length > 0) {
                let globalTotalAktif = 0;
                let globalCountAktif = 0;
                
                for (let unit in data.unitStats) {
                    let totalAktif = 0;
                    let countAktif = 0;
                    let anggota = data.unitStats[unit].anggota || [];
                    anggota.forEach(a => {
                        if (a.rata_rata > 0) {
                            totalAktif += a.rata_rata;
                            countAktif++;
                            globalTotalAktif += a.rata_rata;
                            globalCountAktif++;
                        }
                    });
                    
                    let unitAvg = countAktif > 0 ? (totalAktif / countAktif).toFixed(2) : 0;
                    unitRanking.push({nama: unit, rata: parseFloat(unitAvg)});
                }
                rataRataValue = globalCountAktif > 0 ? (globalTotalAktif / globalCountAktif).toFixed(2) : 0;
                unitRanking.sort((a, b) => b.rata - a.rata);
            }
            
            document.getElementById('valRataRataKinerja').innerText = rataRataValue;
            
            let htmlUnit = '';
            let top5Unit = unitRanking.slice(0, 5);
            if(top5Unit.length === 0) {
                htmlUnit = '<tr><td class="text-center text-muted py-4">Belum ada data</td></tr>';
            } else {
                top5Unit.forEach((u, i) => {
                    let badgeNum = '';
                    if(i === 0) badgeNum = '<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>';
                    else if(i === 1) badgeNum = '<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>';
                    else if(i === 2) badgeNum = '<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>';
                    else badgeNum = '<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;">'+(i+1)+'</div>';
                    
                    let rataStr = u.rata > 0 ? u.rata + '%' : '-';
                    
                    htmlUnit += `
                    <tr style="height: 56px;">
                        <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">${badgeNum}</td>
                        <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="${u.nama}">${u.nama}</div>
                        </td>
                        <td class="text-end pe-4 py-2 border-bottom border-light">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 shadow-sm border border-success">${rataStr}</span>
                        </td>
                    </tr>`;
                });
            }
            document.getElementById('tbodyTop5Unit').innerHTML = htmlUnit;
            
            const partisipasi = data.partisipasiAktif || 0;
            const total = data.totalPegawai || 0;
            const pctPart = total > 0 ? ((partisipasi / total) * 100).toFixed(1) : 0;
            document.getElementById('valPartisipasi').innerHTML = pctPart + '<span class="fs-4">%</span>';
            document.getElementById('valPartisipasiCount').innerText = partisipasi;
            document.getElementById('valTotalPegawai').innerText = total;

            // Sebaran Doughnut Chart
            if(window.pageCharts['sebaranChart'] && data.sebaranKinerja) {
                window.pageCharts['sebaranChart'].data.datasets[0].data = [
                    data.sebaranKinerja.sangat_baik || 0,
                    data.sebaranKinerja.baik || 0,
                    data.sebaranKinerja.cukup || 0,
                    data.sebaranKinerja.kurang || 0
                ];
                window.pageCharts['sebaranChart'].update();
            }

            // Trend Chart
            if(window.pageCharts['trendChart']) {
                window.pageCharts['trendChart'].data.datasets[0].data = data.trendBulananData || [];
                window.pageCharts['trendChart'].update();
            }

            // Top 5 / Bottom 5 Tables
            const renderTable = (arr, isTop) => {
                if(!arr || arr.length === 0) return '<tr><td class="text-center text-muted py-4">Belum ada data</td></tr>';
                let html = '';
                arr.forEach((item, i) => {
                    let badge = '';
                    if(isTop) {
                        if(i === 0) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-warning text-dark p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">1</div>`;
                        else if(i === 1) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle bg-secondary text-white p-0 shadow-sm" style="width:24px;height:24px;font-size:11px;">2</div>`;
                        else if(i === 2) badge = `<div class="d-flex justify-content-center align-items-center mx-auto badge rounded-circle text-white p-0 shadow-sm" style="background-color: #cd7f32; width:24px;height:24px;font-size:11px;">3</div>`;
                        else badge = `<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-muted" style="width:24px;height:24px;font-size:13px;">${i+1}</div>`;
                    } else {
                        badge = `<div class="d-flex justify-content-center align-items-center mx-auto fw-bold text-danger" style="width:24px;height:24px;font-size:13px;">${i+1}</div>`;
                    }
                    const scoreClass = isTop ? 'bg-success text-success border-success' : 'bg-danger text-danger border-danger';
                    html += `
                    <tr style="height: 56px;">
                        <td style="width: 50px;" class="text-center align-middle py-2 border-bottom border-light">${badge}</td>
                        <td class="py-2 border-bottom border-light" style="max-width: 0; width: 100%;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="${item.staf.nama_lengkap}">${item.staf.nama_lengkap}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="${item.staf.jabatan || '-'}">${item.staf.jabatan || '-'}</div>
                        </td>
                        <td class="text-end pe-3 py-2 border-bottom border-light">
                            <span class="badge ${scoreClass} bg-opacity-10 rounded-pill px-2 py-1 shadow-sm border" style="font-size: 0.8rem;">${item.rata_rata}</span>
                        </td>
                    </tr>`;
                });
                return html;
            };
            document.getElementById('tbodyTop5').innerHTML = renderTable(data.top5, true);
            document.getElementById('tbodyBottom5').innerHTML = renderTable(data.bottom5, false);

            // Unit Chart
            if(window.pageCharts['unitChart']) {
                window.adminUnitLabelsCache = data.chartPegawaiUnitLabels || [];
                window.adminUnitStatsCache = data.unitStats || {};
                window.pageCharts['unitChart'].data.labels = window.adminUnitLabelsCache;
                window.pageCharts['unitChart'].data.datasets[0].data = data.chartPegawaiUnitData || [];
                window.pageCharts['unitChart'].update();
            }
        }
    } catch(err) { console.error("Gagal mengambil data Kinerja:", err); }
}

document.addEventListener('DOMContentLoaded', function () {
    // Cleanup chart lama
    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) { window.pageCharts[key].destroy(); delete window.pageCharts[key]; }
    }

    // --- SCRIPT UNTUK MENGINGAT TAB AKTIF ECC ---
    const prodiTabs = document.querySelectorAll('#prodiTab button[data-bs-toggle="tab"]');
    const activeTabKey = 'activeProdiTab';

    prodiTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                sessionStorage.setItem(activeTabKey, targetId);
            }
        });
    });

    const savedTabId = sessionStorage.getItem(activeTabKey);
    let tabToActivate = null;
    if (savedTabId) {
        tabToActivate = document.querySelector(`button[data-bs-target="${savedTabId}"]`);
    }
    if (!tabToActivate) {
        tabToActivate = document.querySelector('#prodiTab button[data-bs-toggle="tab"]');
    }
    if (tabToActivate) {
        const tab = new bootstrap.Tab(tabToActivate);
        tab.show();
    }

    // --- Helper: Word Wrap ---
    function splitLabel(label, maxLength = 16) {
        if (label.length <= maxLength) return label;
        const words = label.split(' ');
        const lines = [];
        let currentLine = words[0];
        for (let i = 1; i < words.length; i++) {
            if (currentLine.length + 1 + words[i].length <= maxLength) {
                currentLine += ' ' + words[i];
            } else {
                lines.push(currentLine);
                currentLine = words[i];
            }
        }
        lines.push(currentLine);
        return lines;
    }

    // --- EVENT KLIK LABEL ECC ---
    function getClickedLabel(clickEvent, chart) {
        if (!chart || !chart.scales || !chart.scales.r) return null;
        const r = chart.scales.r;
        const pointLabelItems = r._pointLabelItems; 
        if (!pointLabelItems || pointLabelItems.length === 0) return null;
        const canvasPosition = Chart.helpers.getRelativePosition(clickEvent, chart);
        const x = canvasPosition.x;
        const y = canvasPosition.y;
        let closestLabelIndex = -1;
        let minDistance = Infinity;
        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            const distance = Math.sqrt(Math.pow(x - item.x, 2) + Math.pow(y - item.y, 2));
            if (distance < minDistance) { minDistance = distance; closestLabelIndex = i; }
        }
        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                const itemWidth = item.options?.bounds?.width || 80; 
                if (minDistance < (itemWidth / 2) + 20) return closestLabelIndex;
            } catch (e) { if (minDistance < 50) return closestLabelIndex; }
        }
        return null;
    }

    // --- INIT RADAR CHART ECC ---
    const prodiData = <?= json_encode($prodiData ?? []) ?>;
    const selectedTahun = '<?= esc($tahun_ecc) ?>';

    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 16));

            window.pageCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels,
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi, data: data.chart_data, fill: true,
                        backgroundColor: 'rgba(30, 64, 175, 0.15)', borderColor: 'rgba(30, 64, 175, 1)',
                        pointBackgroundColor: 'rgba(30, 64, 175, 1)', pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff', pointHoverBorderColor: 'rgba(30, 64, 175, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    layout: { padding: 10 },
                    scales: {
                        r: {
                            angleLines: { display: true, color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 },
                            min: 0, max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.2)', lineWidth: 1.5 },
                            pointLabels: { 
                                display: true, color: '#334155', font: { size: 12, weight: '700', family: 'system-ui', lineHeight: 1.2 }, 
                                backdropPadding: 2, padding: 8
                            },
                            ticks: { display: false, stepSize: 33.3333 }
                        }
                    },
                    plugins: { legend: { display: false } },
                    onHover: (event, activeElements, chart) => {
                         const index = getClickedLabel(event, chart);
                         event.native.target.style.cursor = (index !== null) ? 'pointer' : 'default';
                    },
                    onClick: (e, elements, chart) => {
                        const idx = getClickedLabel(e, chart);
                        if (idx !== null) {
                            if (chart.config.data.labelIds && chart.config.data.labelIds[idx]) {
                                const labelId = chart.config.data.labelIds[idx];
                                const prodi = chart.config.data.prodi.toLowerCase();
                                const tahun = chart.config.data.tahun;
                                document.body.style.cursor = 'wait'; 
                                window.location.href = `<?= site_url('ecc/detailStandar') ?>/${labelId}/${prodi}/${tahun}?from=admin`;
                            }
                        }
                    }
                }
            });
        }
    }

    // --- INIT CHART UNIT ---
    <?php if (!empty($chartPegawaiUnitLabels)): ?>
    const ctxUnit = document.getElementById('unitPerformanceChart');
    if (ctxUnit) {
        window.adminUnitStatsCache = <?= json_encode($unitStats ?? []) ?>;
        window.adminUnitLabelsCache = <?= json_encode($chartPegawaiUnitLabels ?? []) ?>;
        const unitData = <?= json_encode($chartPegawaiUnitData ?? []) ?>;
        
        const dynamicHeight = Math.max(200, window.adminUnitLabelsCache.length * 40);
        ctxUnit.parentElement.style.height = dynamicHeight + 'px';
        
        window.pageCharts['unitChart'] = new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: window.adminUnitLabelsCache,
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: unitData,
                    backgroundColor: 'rgba(30, 64, 175, 0.85)',
                    borderColor: '#1e40af',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: '#1e40af'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            label: function(context) { return 'Rata-Rata: ' + context.parsed.x + '%'; }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true, max: 100, grid: { borderDash: [2, 4], color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }, ticks: { font: { size: 11, family: 'system-ui' } }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const selectedUnit = window.adminUnitLabelsCache[idx];
                        const details = window.adminUnitStatsCache[selectedUnit]?.anggota;
                        
                        document.getElementById('modalUnitName').innerText = selectedUnit;
                        
                        let tbody = '';
                        if(details && details.length > 0) {
                            details.forEach(item => {
                                let badgeClass = 'bg-success';
                                if(item.rata_rata < 60) badgeClass = 'bg-danger';
                                else if(item.rata_rata < 75) badgeClass = 'bg-warning text-dark';
                                else if(item.rata_rata == 0 && item.dinilai == 0) badgeClass = 'bg-secondary';
                                
                                tbody += `
                                    <tr>
                                        <td class="ps-3 py-3 border-bottom border-light">
                                            <div class="fw-bold text-dark">${escapeHtml(item.nama)}</div>
                                            <div class="small text-muted">${escapeHtml(item.jabatan || '-')}</div>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light">
                                            <span class="badge bg-light text-dark border shadow-sm">${escapeHtml(item.dinilai)} / ${escapeHtml(item.total_laporan)}</span>
                                        </td>
                                        <td class="text-center py-3 border-bottom border-light pe-3">
                                            <span class="badge ${badgeClass} fs-6 rounded-pill px-3 shadow-sm">${escapeHtml(item.rata_rata)}</span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tbody = `<tr><td colspan="3" class="text-center text-muted py-4">Data kosong</td></tr>`;
                        }
                        document.getElementById('unitDetailTbody').innerHTML = tbody;
                        
                        const unitModalEl = document.getElementById('unitDetailModal');
                        const unitModal = bootstrap.Modal.getInstance(unitModalEl) || new bootstrap.Modal(unitModalEl);
                        unitModal.show();
                    }
                },
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }
    <?php endif; ?>

    // --- PRO MAX ANALYTICS CHARTS ---
    
    // 1. Sebaran Predikat Kinerja (Doughnut Chart)
    const ctxSebaran = document.getElementById('sebaranChart');
    if (ctxSebaran) {
        window.pageCharts['sebaranChart'] = new Chart(ctxSebaran, {
            type: 'doughnut',
            data: {
                labels: ['Sangat Baik (>100%)', 'Baik (>90-100%)', 'Butuh Perbaikan (>75-90%)', 'Kurang (>25-75%)', 'Sangat Kurang (≤25%)'],
                datasets: [{
                    data: [
                        <?= esc($sebaranKinerja['sangat_baik']) ?>, 
                        <?= esc($sebaranKinerja['baik']) ?>, 
                        <?= esc($sebaranKinerja['butuh_perbaikan']) ?>, 
                        <?= esc($sebaranKinerja['kurang']) ?>,
                        <?= esc($sebaranKinerja['sangat_kurang']) ?>
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'], // green, blue, purple, yellow, red
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'system-ui' } } }
                },
                onClick: function(evt, activeElements, chart) {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const fullLabel = chart.data.labels[index];
                        let realLabel = '';
                        if (fullLabel.includes('Sangat Baik')) realLabel = 'Sangat Baik';
                        else if (fullLabel.includes('Sangat Kurang')) realLabel = 'Sangat Kurang';
                        else if (fullLabel.includes('Butuh Perbaikan')) realLabel = 'Butuh Perbaikan';
                        else if (fullLabel.includes('Baik')) realLabel = 'Baik';
                        else if (fullLabel.includes('Kurang')) realLabel = 'Kurang';
                        
                        fetchChartDetail('sebaran', realLabel);
                    }
                }
            }
        });
    }

    // 4. Monthly Trend Line Chart
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        window.pageCharts['trendChart'] = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: <?= json_encode($trendBulananData) ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [5, 5], color: '#f3f4f6', drawBorder: false }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                },
                onClick: function(evt, activeElements, chart) {
                    if (activeElements.length > 0) {
                        const index = activeElements[0].index;
                        const bulanIdx = index + 1; // 1-12
                        const bulanNames = chart.data.labels;
                        fetchChartDetail('tren', bulanNames[index], bulanIdx);
                    }
                }
            }
        });
    }

});
    // --- HELPER ESCAPE HTML FOR DOM INJECTION ---
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // --- CHART DETAIL MODAL LOGIC ---
    function fetchChartDetail(mode, label, bulanVal = null) {
        const tahun = document.getElementById('tahun_kinerja').value;
        let bulan = document.getElementById('bulan_kinerja').value;
        if (mode === 'tren' && bulanVal) {
            bulan = bulanVal;
        }

        let title = '';
        let url = '<?= site_url('dashboard/api-detail-chart') ?>?mode=' + mode + '&tahun=' + tahun + '&bulan=' + (bulan === 'all' ? new Date().getMonth()+1 : bulan);
        
        if (mode === 'sebaran') {
            title = 'Pegawai Predikat ' + label;
            url += '&kategori=' + encodeURIComponent(label);
        } else if (mode === 'tren') {
            title = 'Detail Kinerja - ' + label + ' ' + tahun;
        }

        document.getElementById('detailChartTitle').innerText = title;
        document.getElementById('detailChartBody').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted small">Memuat data...</div>
            </div>`;
            
        const chartModalEl = document.getElementById('detailChartModal');
        const modal = bootstrap.Modal.getInstance(chartModalEl) || new bootstrap.Modal(chartModalEl);
        modal.show();

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    if (res.data.length === 0) {
                        document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-info py-2">Tidak ada data pegawai.</div>`;
                        return;
                    }
                    
                    let html = `<div class="table-responsive"><table class="table table-hover align-middle border">
                                <thead class="table-light"><tr><th class="ps-3">Nama Pegawai</th><th class="text-center">Nilai Rata-rata</th></tr></thead><tbody>`;
                    
                    res.data.forEach(p => {
                        let badgeColor = 'secondary';
                        if (p.rata_rata >= 90) badgeColor = 'success';
                        else if (p.rata_rata >= 75) badgeColor = 'primary';
                        else if (p.rata_rata >= 60) badgeColor = 'warning text-dark';
                        else if (p.rata_rata > 0) badgeColor = 'danger';

                        html += `<tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark">${escapeHtml(p.nama)}</div>
                                        <div class="text-muted small">${escapeHtml(p.jabatan || '-')} • ${escapeHtml(p.unit || '-')}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-${badgeColor} rounded-pill px-3 py-2" style="font-size:0.85rem">${escapeHtml(p.rata_rata)}</span>
                                    </td>
                                 </tr>`;
                    });
                    
                    html += `</tbody></table></div>`;
                    document.getElementById('detailChartBody').innerHTML = html;
                } else {
                    document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-danger py-2">Terjadi kesalahan pemuatan data.</div>`;
                }
            })
            .catch(err => {
                document.getElementById('detailChartBody').innerHTML = `<div class="alert alert-danger py-2">Gagal menghubungi server.</div>`;
            });
    }
</script>

<!-- Modal Detail Chart -->
<div class="modal fade" id="detailChartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 bg-light rounded-top-4 pb-2">
        <h5 class="modal-title fw-bold text-dark" id="detailChartTitle">Detail Kinerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="min-width: 44px; min-height: 44px;"></button>
      </div>
      <div class="modal-body pt-2" id="detailChartBody">
        <!-- Content loaded via AJAX -->
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>