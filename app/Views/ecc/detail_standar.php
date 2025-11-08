<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Detail Standar') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
<?= esc($page_title ?? 'Detail Standar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Prodi: <span class="text-primary"><?= esc($prodi) ?></span> | Tahun: <span class="text-primary"><?= esc($tahun) ?></span></h4>
        <p class="text-muted">Menampilkan rincian kriteria dan skor untuk standar yang dipilih.</p>
    </div>
    
    <?php
        $return_url = site_url('ecc?tahun=' . $tahun); // Default
        $return_text = "Kembali ke Dashboard ECC";

        if (isset($from_page) && $from_page === 'admin') {
            // Kita asumsikan dashboard admin/user juga difilter berdasarkan tahun yang sama
            $return_url = site_url('admin/dashboard?tahun=' . $tahun);
            $return_text = "Kembali ke Dashboard Admin";
        } elseif (isset($from_page) && $from_page === 'user') {
            $return_url = site_url('user/dashboard?tahun=' . $tahun);
            $return_text = "Kembali ke Dashboard User";
        }
    ?>
    <a href="<?= $return_url ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> <?= $return_text ?>
    </a>
    </div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Visualisasi Skor per Kriteria</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($criteria_data)): ?>
            <div style="height: 350px;">
                <canvas id="skorBarChart"></canvas>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">Tidak ada kriteria untuk ditampilkan pada grafik.</div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Rincian Kriteria untuk Standar "<?= esc($standar['nama_standar']) ?>"</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Nama Kriteria/Elemen/Indikator</th>
                        <th class="text-center">Link Bukti</th>
                        <th class="text-center">Status Kabag</th>
                        <th class="text-center">Status Wadir</th>
                        <th class="text-center">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($criteria_data)): ?>
                        <?php foreach ($criteria_data as $item): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $item['no'] ?></td>
                                <td><?= nl2br(esc($item['nama_kriteria'])) ?></td>
                                <td class="text-center">
                                    <?php if (!empty($item['catatan'])): ?>
                                        <a href="<?= esc($item['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-link-45deg"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>(Kosong)</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($item['kabag_approved'] == 1): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($item['status'])): ?>
                                        <span class="badge bg-primary"><?= esc($item['status']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>(N/A)</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold fs-5">
                                    <?= esc($item['skor_display']) ?>
                                    <?php if (!$item['is_approved']): ?>
                                        <i class="bi bi-info-circle-fill text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="<?= esc($item['skor_alasan_text']) ?>">
                                        </i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                Tidak ada data kriteria yang ditemukan untuk standar, prodi, dan tahun ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Ambil data dari PHP
    const barChartLabels = <?= $barChartLabels; ?>;
    const barChartScores = <?= $barChartScores; ?>;
    const barChartTooltips = <?= $barChartTooltips; ?>; // Data alasan

    const ctxBar = document.getElementById('skorBarChart');
    if (ctxBar && barChartLabels.length > 0) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barChartLabels,
                datasets: [{
                    label: 'Skor Kriteria',
                    data: barChartScores,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    },
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                const index = tooltipItems[0].dataIndex;
                                const fullText = barChartTooltips[index].nama_kriteria || '';
                                const maxLength = 50;
                                let lines = [];
                                for (let i = 0; i < fullText.length; i += maxLength) {
                                    lines.push(fullText.substring(i, i + maxLength));
                                }
                                return lines;
                            },
                            label: function(tooltipItem) {
                                let label = tooltipItem.dataset.label || 'Skor';
                                if (label) {
                                    label += ': ';
                                }
                                if (tooltipItem.parsed.y !== null) {
                                    label += tooltipItem.parsed.y;
                                }
                                return label;
                            },
                            afterLabel: function(tooltipItem) {
                                const index = tooltipItem.dataIndex;
                                const alasan = barChartTooltips[index].skor_alasan;
                                if (alasan) {
                                    return 'Alasan: ' + alasan;
                                }
                                return ''; 
                            }
                        }
                    }
                }
            }
        });
    }

    // Inisialisasi Bootstrap Tooltip
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>