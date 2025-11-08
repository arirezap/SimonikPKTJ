<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Dashboard ECC') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Dashboard ECC (Evidence Command Center)
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Style untuk memperjelas tab yang aktif */
    .nav-tabs .nav-link {
        border-bottom-width: 0;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        color: #0d6efd;
        font-weight: bold;
    }
    /* Memberi background dan border pada konten tab */
    .tab-content {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem;
    }
    .chart-container {
        position: relative;
        height: 450px;
        width: 100%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= site_url('ecc') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($daftar_tahun as $tahun_item): ?>
                        <option value="<?= esc($tahun_item) ?>" <?= ($selectedTahun == $tahun_item) ? 'selected' : '' ?>>
                            <?= esc($tahun_item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs" id="prodiTab" role="tablist">
    <?php $first = true; ?>
    <?php foreach($prodiData as $prodi): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $first ? 'active' : '' ?>" id="tab-<?= esc($prodi['id_prodi']) ?>" data-bs-toggle="tab" data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>" type="button" role="tab" aria-controls="content-<?= esc($prodi['id_prodi']) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
                <?= esc($prodi['nama_prodi']) ?>
            </button>
        </li>
        <?php $first = false; ?>
    <?php endforeach; ?>
</ul>

<div class="tab-content" id="prodiTabContent">
    <?php $first = true; ?>
    <?php foreach($prodiData as $prodi): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?> p-3" id="content-<?= esc($prodi['id_prodi']) ?>" role="tabpanel" aria-labelledby="tab-<?= esc($prodi['id_prodi']) ?>">
            
            <div class="card">
                <div class="card-header">
                    <h5>Rangkuman Data: <?= esc($prodi['nama_prodi']) ?> (Tahun <?= esc($selectedTahun) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($prodi['chart_labels'])): ?>
                        <div class="alert alert-info">Belum ada data Kategori LED yang diisi di Master Data.</div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>"></canvas>
                        </div>
                        <p class="text-center text-muted small mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> Klik pada nama standar (label) di grafik untuk melihat rincian detailnya.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <?php $first = false; ?>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Siapkan data dari PHP
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($selectedTahun) ?>';

    /**
     * Fungsi untuk mendeteksi label mana yang diklik pada grafik radar
     */
    function getClickedLabel(clickEvent, chart) {
        const r = chart.scales.r;
        const pointLabelItems = r._pointLabelItems; 
        if (!pointLabelItems || pointLabelItems.length === 0) return null;

        const { x, y } = clickEvent;
        
        let closestLabelIndex = -1;
        let minDistance = Infinity;

        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            const distance = Math.sqrt(Math.pow(x - item.x, 2) + Math.pow(y - item.y, 2));
            if (distance < minDistance) {
                minDistance = distance;
                closestLabelIndex = i;
            }
        }

        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                const itemWidth = item.options.bounds.width;
                if (minDistance < (itemWidth / 2) + 10) { 
                    return closestLabelIndex;
                }
            } catch (e) {
                if (minDistance < 30) {
                     return closestLabelIndex;
                }
            }
        }
        return null;
    }

    // Loop melalui setiap data prodi dan buat grafiknya
    for (const [id, data] of Object.entries(prodiData)) {
        const ctx = document.getElementById('radarChart-' + id);
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: data.chart_labels,
                    labelIds: data.chart_label_ids, 
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi,
                        data: data.chart_data,
                        fill: true,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(13, 110, 253, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            
                            pointLabels: {
                                display: true,
                                color: '#0d6efd', 
                                hoverColor: '#0a58ca',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                hoverFont: {
                                    weight: 'bolder'
                                },
                                backdropPadding: 4,
                                padding: 10, 
                                onHover: (event, label) => {
                                    event.native.target.style.cursor = 'pointer';
                                },
                                onLeave: (event, label) => {
                                    event.native.target.style.cursor = 'default';
                                }
                            },
                            ticks: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        // --- PERUBAHAN LOGIKA TOOLTIP DI SINI ---
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    let label = tooltipItem.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (tooltipItem.formattedValue !== null) {
                                        label += tooltipItem.formattedValue;
                                    }
                                    return label;
                                },
                                afterLabel: function(tooltipItem) {
                                    const score = tooltipItem.parsed.r;
                                    if (score === 0) {
                                        let lines = [
                                            'Skor 0 karena item standar ini:',
                                            '- Belum disetujui Kabag/Wadir',
                                            '- Belum dinilai/disimulasi',
                                            'Klik label untuk detail.'
                                        ];
                                        return lines;
                                    }
                                    return ''; // Kosong jika skor > 0
                                }
                            }
                        }
                        // --- SELESAI PERUBAHAN TOOLTIP ---
                    },
                    onClick: (e, elements, chart) => {
                        const clickedLabelIndex = getClickedLabel(e, chart);
                        
                        if (clickedLabelIndex !== null) {
                            const labelId = chart.config.data.labelIds[clickedLabelIndex];
                            const prodi = chart.config.data.prodi;
                            const tahun = chart.config.data.tahun;
                            
                            window.location.href = `<?= site_url('ecc/detail') ?>/${labelId}/${prodi}/${tahun}`;
                        }
                    }
                }
            });
        }
    }
});
</script>
<?= $this->endSection() ?>