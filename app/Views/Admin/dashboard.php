// ... existing code ...
<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Siapkan data dari PHP
    const prodiData = <?= json_encode($prodiData) ?>;
    const selectedTahun = '<?= esc($tahun_terpilih) ?>';

    /**
     * Fungsi untuk mendeteksi label mana yang diklik pada grafik radar
     */
    function getClickedLabel(clickEvent, chart) {
        // GUARD: Cek apakah scale 'r' tersedia
        if (!chart || !chart.scales || !chart.scales.r) return null;

        const r = chart.scales.r;
        const pointLabelItems = r._pointLabelItems; 
        
        if (!pointLabelItems || pointLabelItems.length === 0) return null;

        // Gunakan posisi relatif terhadap canvas, bukan event absolute
        const canvasPosition = Chart.helpers.getRelativePosition(clickEvent, chart);
        const x = canvasPosition.x;
        const y = canvasPosition.y;
        
        let closestLabelIndex = -1;
        let minDistance = Infinity;

        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            // Hitung jarak Euclidean
            const distance = Math.sqrt(Math.pow(x - item.x, 2) + Math.pow(y - item.y, 2));
            if (distance < minDistance) {
                minDistance = distance;
                closestLabelIndex = i;
            }
        }

        if (closestLabelIndex > -1) {
            try {
                const item = pointLabelItems[closestLabelIndex];
                // Area klik yang valid: setengah lebar label + toleransi 10px
                // Tambahkan fallback width jika undefined (misal 50px)
                const itemWidth = item.options?.bounds?.width || 50; 
                
                if (minDistance < (itemWidth / 2) + 15) { // Toleransi diperbesar sedikit ke 15
                    return closestLabelIndex;
                }
            } catch (e) {
                // Fallback jika perhitungan bounds gagal
                if (minDistance < 40) {
                     return closestLabelIndex;
                }
            }
        }
        return null;
    }

    // Simpan instance chart agar bisa dimanage
    const chartInstances = {};

    // Loop melalui setiap data prodi dan buat grafiknya
    for (const [id, data] of Object.entries(prodiData)) {
        const canvasId = 'radarChart-' + id;
        const ctx = document.getElementById(canvasId);
        
        if (ctx) {
            // Hancurkan chart lama jika ada (mencegah memory leak/double render)
            if (chartInstances[id]) {
                chartInstances[id].destroy();
            }

            chartInstances[id] = new Chart(ctx, {
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
                            angleLines: { display: true },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)', 
                            },
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
                                // Event hover untuk cursor pointer
                                onHover: (event) => {
                                    const chart = event.chart;
                                    const index = getClickedLabel(event, chart);
                                    // Ubah cursor hanya jika di atas label valid
                                    event.native.target.style.cursor = (index !== null) ? 'pointer' : 'default';
                                },
                                onLeave: (event) => {
                                    event.native.target.style.cursor = 'default';
                                }
                            },
                            ticks: { 
                                display: false, 
                                stepSize: 25,   
                                maxTicksLimit: 5 
                            }
                        }
                    },
                    plugins: { 
                        legend: { position: 'top' },
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
                                    return ''; 
                                }
                            }
                        }
                    },
                    onClick: (e, elements, chart) => {
                        const clickedLabelIndex = getClickedLabel(e, chart);
                        
                        if (clickedLabelIndex !== null) {
                            // Pastikan data tersedia sebelum redirect
                            if (chart.config.data.labelIds && chart.config.data.labelIds[clickedLabelIndex]) {
                                const labelId = chart.config.data.labelIds[clickedLabelIndex];
                                const prodi = chart.config.data.prodi;
                                const tahun = chart.config.data.tahun;
                                
                                // Tambahkan loading state visual (opsional tapi UX bagus)
                                document.body.style.cursor = 'wait'; 
                                
                                window.location.href = `<?= site_url('ecc/detail') ?>/${labelId}/${prodi}/${tahun}`;
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>

<?php if (!empty($chartLabels)): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctxBar = document.getElementById('userPerformanceChart');
    // ... kode chart batang tetap sama ...
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Persentase Capaian',
                    data: <?= json_encode($chartData); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    x: { 
                        beginAtZero: true, 
                        ticks: { 
                            callback: value => value + "%" 
                        } 
                    } 
                },
                plugins: { 
                    legend: { 
                        display: false 
                    } 
                }
            }
        });
    }
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>