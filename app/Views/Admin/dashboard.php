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
        <form method="GET" action="<?= site_url('admin/dashboard') ?>" class="row g-3 align-items-end">
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

<h5 class="mb-3">Dashboard ECC (Evidence Command Center)</h5>

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

<h5 class="mb-4">Dashboard Monitoring Kinerja Global</h5>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card text-bg-info shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= esc($totalIndikator) ?></h2>
                <p class="card-text fs-5">Total Indikator Kinerja</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-bg-success shadow h-100 border-0">
            <div class="card-body p-4 text-center">
                <h2 class="display-6 fw-bold mb-0"><?= round($rataRataCapaianGlobal, 2) ?>%</h2>
                <p class="card-text fs-5">Rata-rata Capaian Global</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">Perbandingan Capaian per Tim/Unit/Pokja (%)</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartLabels)): ?>
            <div style="position: relative; height: 400px; width: 100%;"><canvas id="userPerformanceChart"></canvas></div>
        <?php else: ?>
            <div class="alert alert-info">Belum ada data kinerja dari Tim/Unit/Pokja.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i>Persentase Capaian per Indikator Kinerja</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($chartIndikatorLabels)): ?>
            <div class="performance-chart-container">
                <canvas id="indikatorChart"></canvas>
            </div>
            <p class="text-muted small text-center mt-2">
                * Grafik menampilkan <strong>Persentase Capaian</strong>. Arahkan kursor (hover) pada batang untuk melihat nilai <strong>Target & Realisasi</strong> asli.
            </p>
        <?php else: ?>
            <div class="alert alert-light text-center p-5">
                <i class="bi bi-clipboard-data display-4 text-muted mb-3 d-block"></i>
                Belum ada data Indikator Kinerja untuk ditampilkan.
            </div>
        <?php endif; ?>
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

    // --- 3. CHART USER PERFORMANCE (ADMIN DATA) ---
    <?php if (!empty($chartLabels)): ?>
    const ctxBarUser = document.getElementById('userPerformanceChart');
    if (ctxBarUser) {
        window.pageCharts['userPerformanceChart'] = new Chart(ctxBarUser, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Capaian (%)', data: <?= json_encode($chartData); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)', borderColor: 'rgba(13, 110, 253, 1)', borderWidth: 1, borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                scales: { x: { beginAtZero: true, max: 100, ticks: { callback: value => value + "%" } } },
                plugins: { legend: { display: false } }
            }
        });
    }
    <?php endif; ?>

    // --- 4. CHART INDIKATOR (VERTICAL BAR) ---
    <?php if (!empty($chartIndikatorLabels)): ?>
    const ctxIndikator = document.getElementById('indikatorChart');
    if (ctxIndikator) {
        const rawLabels = <?= json_encode($chartIndikatorLabels); ?>;
        const metaData = <?= json_encode($chartIndikatorMeta ?? []); ?>;
        
        // Split label agar tidak melebar
        const wrappedLabels = rawLabels.map(label => splitLabel(label, 45)); 

        window.pageCharts['indikatorChart'] = new Chart(ctxIndikator, {
            type: 'bar',
            data: {
                labels: wrappedLabels,
                datasets: [
                    {
                        label: 'Capaian (%)',
                        data: <?= json_encode($chartIndikatorPersen ?? []); ?>,
                        backgroundColor: function(context) {
                            const value = context.raw;
                            if (value >= 100) return '#198754';
                            if (value >= 80) return '#0d6efd';
                            if (value >= 50) return '#ffc107';
                            return '#dc3545';
                        },
                        borderRadius: 4
                    }
                ]
            },
            options: {
                indexAxis: 'x', // Vertical
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { bottom: 20 } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        max: 100, 
                        ticks: { callback: value => value + "%" },
                        grid: { borderDash: [2, 2] }
                    },
                    x: { 
                        ticks: { 
                            autoSkip: false, 
                            maxRotation: 45, 
                            minRotation: 25,
                            font: { size: 10 } 
                        } 
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const label = context[0].label;
                                return Array.isArray(label) ? label.join(' ') : label;
                            },
                            label: function(context) {
                                const idx = context.dataIndex;
                                const item = metaData[idx];
                                const percent = context.formattedValue;
                                const formatNum = (num) => new Intl.NumberFormat('id-ID').format(num);
                                return [
                                    `Capaian: ${percent}%`,
                                    `Target: ${formatNum(item.target)} ${item.satuan}`,
                                    `Realisasi: ${formatNum(item.realisasi)} ${item.satuan}`
                                ];
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>