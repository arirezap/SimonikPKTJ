<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Admin Dashboard') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Tab Navigasi */
    .nav-tabs .nav-link { border-bottom-width: 0; color: #6c757d; }
    .nav-tabs .nav-link.active { background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; color: #0d6efd; font-weight: bold; }
    
    /* Card Container */
    .tab-content, .chart-card-wrapper {
        background-color: #ffffff; border: 1px solid #dee2e6; border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); padding: 2rem;
    }
    
    /* Ukuran Chart */
    .radar-chart-container { position: relative; height: 500px; width: 100%; }
    .chart-container { position: relative; height: 500px; width: 100%; }
    .performance-chart-container { position: relative; min-height: 600px; width: 100%; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Dashboard</h4>
</div>

<div class="card mb-5 shadow-sm">
    <div class="card-body">
        <form method="GET" action="<?= site_url('dashboard') ?>" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= $tahun_item; ?>" <?= ($tahun_terpilih == $tahun_item) ? 'selected' : ''; ?>><?= esc($tahun_item); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="bulan" class="form-label fw-bold">Pilih Bulan (untuk Kinerja Global)</label>
                <select class="form-select" id="bulan" name="bulan">
                    <option value="all" <?= ($bulan_terpilih === 'all' || !$bulan_terpilih) ? 'selected' : '' ?>>Semua Bulan (Tahunan)</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i; ?>" <?= ($bulan_terpilih == $i) ? 'selected' : ''; ?>><?= bulan_indo($i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- ECC Dashboard (Paling Atas) -->
<h5 class="mb-3 fw-bold text-dark mt-4"><i class="bi bi-shield-check text-success me-2"></i> Dashboard ECC (Evidence Command Center)</h5>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab"><?= esc($prodi['nama_prodi']) ?></button>
        </li>
    <?php endforeach; ?>
</ul>

<div class="tab-content mb-5" id="prodiTabContent">
    <?php foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h6 class="text-center mb-4">Rangkuman Skor LED: <span class="text-primary"><?= esc($prodi['nama_prodi']) ?></span> (Tahun <?= esc($tahun_terpilih) ?>)</h6>
                    
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info text-center">Belum ada data Kategori LED untuk tahun ini.</div>
                    <?php else: ?>
                        <div class="radar-chart-container"><canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas></div>
                        <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i> Klik pada nama standar (label) di grafik untuk melihat detail.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<hr class="my-5 text-muted">

<h5 class="mb-3 mt-4 fw-bold text-dark"><i class="bi bi-activity text-primary me-2"></i> Command Center: Analitik Instansi</h5>

<!-- Baris 1: Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); color: white;">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <div class="text-white-50 fw-bold mb-1 text-uppercase small">Rata-Rata Kinerja Bulanan</div>
                <h2 class="display-5 fw-bold mb-0">
                    <?php 
                        $rataRataValue = 0;
                        if (!empty($unitStats)) {
                            $totalAll = array_sum(array_column($unitStats, 'total_rata'));
                            $countAll = array_sum(array_column($unitStats, 'count'));
                            $rataRataValue = $countAll > 0 ? round($totalAll / $countAll, 2) : 0;
                        }
                        echo esc($rataRataValue);
                    ?>
                </h2>
                <div class="mt-2 text-white-50 small"><i class="bi bi-graph-up-arrow"></i> Skor Agregat Seluruh Pegawai</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Total Laporan Dinilai</div>
                <h2 class="display-5 fw-bold text-dark mb-0"><?= esc($globalTotalDinilai) ?></h2>
                <div class="mt-2 text-success small"><i class="bi bi-check-circle-fill"></i> Laporan telah diperiksa atasan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <div class="text-muted fw-bold mb-1 text-uppercase small">Ketepatan Waktu Penyelesaian</div>
                <h2 class="display-5 fw-bold text-dark mb-0">
                    <?php 
                        $totWaktu = $globalTepatWaktu + $globalTerlambat;
                        echo $totWaktu > 0 ? round(($globalTepatWaktu / $totWaktu) * 100, 1) : 0;
                    ?>%
                </h2>
                <div class="mt-2 text-muted small"><i class="bi bi-clock-history"></i> Tepat Waktu: <?= $globalTepatWaktu ?> | Terlambat: <?= $globalTerlambat ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Baris 2: Doughnut & Line Chart -->
<div class="row g-4 mb-4">
    <!-- Ketepatan Waktu (Doughnut) -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 text-dark">Rasio Ketepatan Waktu</h6>
                <div style="height: 280px; position: relative;">
                    <canvas id="punctualityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Line Chart Tren Tahunan -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 text-dark">Tren Kinerja Bulanan Organisasi (Tahun <?= esc($tahun_terpilih) ?>)</h6>
                <div style="height: 280px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Baris 4: Leaderboard & Needs Attention -->
<div class="row g-4 mb-5">
    <!-- Top 5 Performers -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Pegawai Berkinerja Terbaik</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody>
                            <?php if(empty($top5)): ?>
                                <tr><td class="text-center text-muted">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($top5 as $i => $t): ?>
                                <tr>
                                    <td style="width: 40px;" class="text-center">
                                        <?php if($i == 0): ?><span class="badge rounded-circle bg-warning text-dark p-2 fs-6">1</span>
                                        <?php elseif($i == 1): ?><span class="badge rounded-circle bg-secondary text-white p-2 fs-6">2</span>
                                        <?php elseif($i == 2): ?><span class="badge rounded-circle p-2 fs-6 text-white" style="background-color: #cd7f32;">3</span>
                                        <?php else: ?><span class="fw-bold text-muted"><?= $i+1 ?></span><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($t['bawahan']['nama_lengkap']) ?></div>
                                        <div class="small text-muted"><?= esc($t['bawahan']['jabatan'] ?? '-') ?></div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success bg-opacity-10 text-success fs-6 border border-success"><?= esc($t['rata_rata']) ?></span>
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
    <!-- Needs Attention -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Perlu Perhatian Khusus (Terbawah)</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <tbody>
                            <?php if(empty($bottom5)): ?>
                                <tr><td class="text-center text-muted">Belum ada data</td></tr>
                            <?php else: ?>
                                <?php foreach($bottom5 as $i => $b): ?>
                                <tr>
                                    <td style="width: 40px;" class="text-center">
                                        <span class="fw-bold text-danger"><?= $i+1 ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($b['bawahan']['nama_lengkap']) ?></div>
                                        <div class="small text-muted"><?= esc($b['bawahan']['jabatan'] ?? '-') ?></div>
                                    </td>
                                    <td class="text-end">
                                        <?php $color = $b['rata_rata'] < 60 ? 'danger' : 'warning'; ?>
                                        <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> fs-6 border border-<?= $color ?>"><?= esc($b['rata_rata']) ?></span>
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



<div class="d-flex align-items-center mb-4 mt-5">
    <h4 class="mb-0 me-3">Kinerja Seluruh Pegawai (Per Unit Kerja)</h4>
    <span class="badge bg-primary bg-opacity-10 text-primary fs-6">Direktur / Wadir</span>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-5">
    <div class="card-body p-4">
        <?php if (empty($chartPegawaiUnitLabels)): ?>
            <div class="alert alert-info border-0 shadow-sm"><i class="bi bi-info-circle me-2"></i> Belum ada data rekap kinerja bawahan untuk bulan ini.</div>
        <?php else: ?>
            <div class="performance-chart-container" style="height: 400px; position: relative; width: 100%;">
                <canvas id="unitPerformanceChart"></canvas>
            </div>
            <p class="text-center text-muted small mt-3"><i class="bi bi-info-circle me-1"></i> Klik pada grafik batang untuk melihat detail anggota unit kerja.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail Unit -->
<div class="modal fade" id="unitDetailModal" tabindex="-1" aria-labelledby="unitDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="unitDetailModalLabel">Detail Pegawai: <span id="modalUnitName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-secondary small fw-bold text-uppercase rounded-start">Nama Pegawai</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center">Laporan Dinilai</th>
                                <th class="text-secondary small fw-bold text-uppercase text-center rounded-end">Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody id="unitDetailTbody">
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

document.addEventListener('DOMContentLoaded', function () {
    // Cleanup chart lama
    for (let key in window.pageCharts) {
        if (window.pageCharts[key]) { window.pageCharts[key].destroy(); delete window.pageCharts[key]; }
    }

    // --- SCRIPT BARU UNTUK MENGINGAT TAB AKTIF ---
    const prodiTabs = document.querySelectorAll('#prodiTab button[data-bs-toggle="tab"]');
    const activeTabKey = 'activeProdiTab';

    // 1. Saat tab diklik, simpan ID targetnya
    prodiTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target');
            if (targetId) {
                sessionStorage.setItem(activeTabKey, targetId);
            }
        });
    });

    // 2. Saat halaman dimuat, aktifkan tab yang tersimpan atau tab pertama
    const savedTabId = sessionStorage.getItem(activeTabKey);
    let tabToActivate = null;

    if (savedTabId) {
        // Cari tombol yang menargetkan konten yang tersimpan
        tabToActivate = document.querySelector(`button[data-bs-target="${savedTabId}"]`);
    }

    // Jika tab yang tersimpan tidak ditemukan, atau tidak ada yang tersimpan, aktifkan yang pertama
    if (!tabToActivate) {
        tabToActivate = document.querySelector('#prodiTab button[data-bs-toggle="tab"]');
    }

    // Tampilkan tab yang sudah ditentukan
    if (tabToActivate) {
        const tab = new bootstrap.Tab(tabToActivate);
        tab.show();
    }

    // --- 1. HELPER FUNCTIONS (SAMA DENGAN USER) ---
    
    // Fungsi Split Label (Word Wrap)
    function splitLabel(label, maxLength = 25) {
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

    // Fungsi Format Angka
    const formatLongLabel = (val, ctx) => {
        const label = ctx.chart.data.labels[val];
        return (label.length > 40) ? label.substring(0, 40) + '...' : label;
    };

    // Fungsi Deteksi Klik Radar (SAMA PERSIS DENGAN USER)
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

    // --- 2. INIT RADAR CHART (LOGIKA SAMA DENGAN USER) ---
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_terpilih) ?>';

    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        if (ctx) {
            const wrappedLabels = data.chart_labels.map(label => splitLabel(label, 25));

            window.pageCharts[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels,
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi, data: data.chart_data, fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)', borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)', pointBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, 
                    maintainAspectRatio: false,
                    layout: { padding: 20 },
                    scales: {
                        r: {
                            angleLines: { display: true },
                            min: 0, max: 100,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            pointLabels: { 
                                display: true, color: '#0d6efd', font: { size: 11, weight: 'bold' }, 
                                backdropPadding: 4, padding: 15
                            },
                            ticks: { 
                                display: false, 
                                stepSize: 33.3333 // SKALA 3 GARIS
                            }
                        }
                    },
                    plugins: { legend: { position: 'top' } },
                    
                    // EVENT HANDLERS (KLIK LABEL)
                    onHover: (event, activeElements, chart) => {
                         const index = getClickedLabel(event, chart);
                         event.native.target.style.cursor = (index !== null) ? 'pointer' : 'default';
                    },
                    onClick: (e, elements, chart) => {
                        // Deteksi klik pada Label (bukan dot)
                        const idx = getClickedLabel(e, chart);
                        if (idx !== null) {
                            if (chart.config.data.labelIds && chart.config.data.labelIds[idx]) {
                                const labelId = chart.config.data.labelIds[idx];
                                // SOLUSI: Ubah nama prodi menjadi lowercase agar sesuai dengan aturan router
                                const prodi = chart.config.data.prodi.toLowerCase();
                                const tahun = chart.config.data.tahun;
                                
                                document.body.style.cursor = 'wait'; 
                                // PERBAIKAN: Sesuaikan URL ke 'ecc/detailStandar' dan gunakan variabel prodi yang sudah di-lowercase
                                window.location.href = `<?= site_url('ecc/detailStandar') ?>/${labelId}/${prodi}/${tahun}?from=admin`;
                            }
                        }
                    }
                }
            });
        }
    }

    <?php if (!empty($chartPegawaiUnitLabels)): ?>
    const ctxUnit = document.getElementById('unitPerformanceChart');
    if (ctxUnit) {
        const unitStats = <?= json_encode($unitStats ?? []) ?>;
        const unitLabels = <?= json_encode($chartPegawaiUnitLabels) ?>;
        const unitData = <?= json_encode($chartPegawaiUnitData) ?>;
        
        // Adjust height based on number of labels so it's not squished (dikecilkan ~50% sesuai request)
        const dynamicHeight = Math.max(200, unitLabels.length * 25);
        ctxUnit.parentElement.style.height = dynamicHeight + 'px';
        
        const unitChart = new Chart(ctxUnit, {
            type: 'bar',
            data: {
                labels: unitLabels,
                datasets: [{
                    label: 'Rata-Rata Kinerja',
                    data: unitData,
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4,
                    maxBarThickness: 30
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rata-Rata: ' + context.parsed.x;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { borderDash: [2, 4], color: '#e9ecef' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const selectedUnit = unitLabels[idx];
                        const details = unitStats[selectedUnit].anggota;
                        
                        document.getElementById('modalUnitName').innerText = selectedUnit;
                        
                        let tbody = '';
                        if(details && details.length > 0) {
                            details.forEach(item => {
                                let badgeClass = 'bg-success';
                                if(item.rata_rata < 60) badgeClass = 'bg-danger';
                                else if(item.rata_rata < 75) badgeClass = 'bg-warning text-dark';
                                
                                tbody += `
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">${item.nama}</div>
                                            <div class="small text-muted">${item.jabatan}</div>
                                        </td>
                                        <td class="text-center">${item.dinilai} / ${item.total_laporan}</td>
                                        <td class="text-center">
                                            <span class="badge ${badgeClass} fs-6">${item.rata_rata}</span>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tbody = `<tr><td colspan="3" class="text-center text-muted">Data kosong</td></tr>`;
                        }
                        document.getElementById('unitDetailTbody').innerHTML = tbody;
                        
                        new bootstrap.Modal(document.getElementById('unitDetailModal')).show();
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
    
    // 1. Punctuality Doughnut Chart
    const ctxPunctuality = document.getElementById('punctualityChart');
    if (ctxPunctuality) {
        new Chart(ctxPunctuality, {
            type: 'doughnut',
            data: {
                labels: ['Tepat Waktu', 'Terlambat'],
                datasets: [{
                    data: [<?= $globalTepatWaktu ?>, <?= $globalTerlambat ?>],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                }
            }
        });
    }

    // 4. Monthly Trend Line Chart
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        const trendData = <?= json_encode($trendBulananData) ?>;
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Rata-rata Nilai Instansi',
                    data: trendData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { min: 0, max: 100, grid: { borderDash: [4, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

});
</script>
<?= $this->endSection() ?>