<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Detail Standar - ECC') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. HEADER & NAVIGATION BANNER (BENTO STYLE) -->
<div class="bento-card border-top border-4 border-primary shadow-sm mb-4">
    <div class="bento-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                    <i class="bi bi-mortarboard-fill me-1"></i> PRODI <?= strtoupper(esc($prodi)) ?>
                </span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                    <i class="bi bi-calendar3 me-1"></i> TAHUN <?= esc($tahun) ?>
                </span>
            </div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">
                Detail Standar: <span class="text-primary-bento"><?= esc($standar['nama_standar'] ?? 'Standar LED') ?></span>
            </h4>
            <p class="text-muted small mb-0">Rincian kriteria, tautan bukti fisik <em>(evidence)</em>, verifikasi bertingkat, dan capaian skor akreditasi.</p>
        </div>
        
        <?php
            $return_url = site_url('ecc?tahun=' . $tahun); // Default
            $return_text = "Kembali ke Dashboard ECC";

            if (isset($from_page) && $from_page === 'admin') {
                $return_url = site_url('dashboard?tahun_ecc=' . $tahun);
                $return_text = "Kembali ke Dashboard Admin";
            } elseif (isset($from_page) && $from_page === 'user') {
                $return_url = site_url('dashboard?tahun_ecc=' . $tahun);
                $return_text = "Kembali ke Dashboard";
            }
        ?>
        <a href="<?= $return_url ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 shadow-sm text-nowrap fw-medium">
            <i class="bi bi-arrow-left me-1"></i> <?= $return_text ?>
        </a>
    </div>
</div>

<!-- 2. VISUALISASI SKOR PER KRITERIA (BAR CHART BENTO) -->
<div class="bento-card shadow-sm mb-4">
    <div class="bento-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="bg-primary-bento text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-bar-chart-fill fs-6"></i>
            </div>
            <span class="fw-bold text-dark">Visualisasi Skor per Kriteria</span>
        </div>
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Rentang Skor 0 - 100</span>
    </div>
    <div class="bento-body p-4">
        <?php if (!empty($criteria_data)): ?>
            <div class="performance-chart-container" style="height: 300px; position: relative;">
                <canvas id="skorBarChart"></canvas>
            </div>
            <p class="text-center text-muted small mt-3 mb-0">
                <i class="bi bi-cursor-fill me-1 text-primary"></i> Arahkan kursor pada batang grafik untuk melihat rincian nama kriteria dan status verifikasi.
            </p>
        <?php else: ?>
            <div class="alert alert-light border text-center text-muted mb-0 shadow-sm">
                <i class="bi bi-info-circle me-2"></i> Tidak ada data kriteria untuk ditampilkan pada grafik tahun ini.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 3. TABEL RINCIAN KRITERIA & BUKTI (HIGH DENSITY BENTO TABLE) -->
<div class="bento-card shadow-sm mb-4">
    <div class="bento-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-table fs-6"></i>
            </div>
            <span class="fw-bold text-dark">Rincian Elemen Kriteria & Bukti Fisik</span>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">
            Total <?= count($criteria_data ?? []) ?> Kriteria
        </span>
    </div>
    <div class="bento-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-0" style="min-width: 1000px;">
                <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th style="width: 50px;" class="text-center ps-3 py-3 border-0 rounded-top-start">No.</th>
                        <th style="min-width: 320px;" class="py-3 border-0">Nama Kriteria / Elemen / Indikator</th>
                        <th style="width: 140px;" class="text-center py-3 border-0">Link Bukti</th>
                        <th style="min-width: 240px;" class="py-3 border-0">Catatan Review</th>
                        <th style="width: 130px;" class="text-center py-3 border-0">Status Kabag</th>
                        <th style="width: 140px;" class="text-center py-3 border-0">Status Wadir</th>
                        <th style="width: 90px;" class="text-center pe-3 py-3 border-0 rounded-top-end">Skor</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (!empty($criteria_data)): ?>
                        <?php foreach ($criteria_data as $item): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted ps-3 py-3 border-bottom border-light">
                                    <?= $item['no'] ?>
                                </td>
                                <td class="py-3 border-bottom border-light">
                                    <div class="fw-semibold text-dark mb-1" style="line-height: 1.4;">
                                        <?= nl2br(esc($item['nama_kriteria'])) ?>
                                    </div>
                                    <?php if (!empty($item['role_assignment']) && $item['role_assignment'] !== 'all'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                                            Bagian: <?= strtoupper(esc($item['role_assignment'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if (!empty($item['catatan'])): ?>
                                        <a href="<?= esc($item['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm text-nowrap fw-medium">
                                            <i class="bi bi-link-45deg me-1"></i> Buka Link
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                            <i class="bi bi-dash me-1"></i> Belum Ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 border-bottom border-light" style="font-size: 0.85rem;">
                                    <?php if (!empty($item['catatan_kabag'])): ?>
                                        <div class="mb-1 p-2 bg-light rounded-3 border">
                                            <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i> Kabag:</strong> 
                                            <span class="text-secondary"><?= nl2br(esc($item['catatan_kabag'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['catatan_wadir'])): ?>
                                        <div class="p-2 bg-light rounded-3 border">
                                            <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-info"></i> Wadir:</strong> 
                                            <span class="text-secondary"><?= nl2br(esc($item['catatan_wadir'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($item['catatan_kabag']) && empty($item['catatan_wadir'])): ?>
                                        <span class="text-muted small"><em>(Belum ada catatan)</em></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if ($item['kabag_approved'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Sesuai
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                            <i class="bi bi-clock-history me-1"></i> Belum Sesuai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3 border-bottom border-light">
                                    <?php if (!empty($item['status'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                            <?= esc($item['status']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                                            Belum Dinilai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3 py-3 border-bottom border-light">
                                    <span class="fw-bold fs-5 <?= $item['is_approved'] ? 'text-success' : 'text-muted' ?>">
                                        <?= esc($item['skor_display']) ?>
                                    </span>
                                    <?php if (!$item['is_approved']): ?>
                                        <i class="bi bi-info-circle-fill text-warning ms-1" 
                                           style="cursor: pointer;"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="left"
                                           title="<?= esc($item['skor_alasan_text']) ?>">
                                        </i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
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
    // Inisialisasi Chart.js
    const barChartLabels = <?= $barChartLabels ?? '[]'; ?>;
    const barChartScores = <?= $barChartScores ?? '[]'; ?>;
    const barChartTooltips = <?= $barChartTooltips ?? '[]'; ?>;

    const ctxBar = document.getElementById('skorBarChart');
    if (ctxBar && Array.isArray(barChartLabels) && barChartLabels.length > 0) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: barChartLabels,
                datasets: [{
                    label: 'Skor Kriteria',
                    data: barChartScores,
                    backgroundColor: 'rgba(30, 64, 175, 0.75)',
                    borderColor: 'rgba(30, 64, 175, 1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(30, 64, 175, 0.95)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11, weight: '500' } }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { stepSize: 25, color: '#64748b', font: { family: 'Inter', size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(tooltipItems) {
                                const index = tooltipItems[0].dataIndex;
                                const fullText = (barChartTooltips[index] && barChartTooltips[index].nama_kriteria) ? barChartTooltips[index].nama_kriteria : '';
                                const maxLength = 45;
                                let lines = [];
                                for (let i = 0; i < fullText.length; i += maxLength) {
                                    lines.push(fullText.substring(i, i + maxLength));
                                }
                                return lines;
                            },
                            label: function(tooltipItem) {
                                return ' Skor: ' + (tooltipItem.parsed.y !== null ? tooltipItem.parsed.y : 0);
                            },
                            afterLabel: function(tooltipItem) {
                                const index = tooltipItem.dataIndex;
                                const alasan = (barChartTooltips[index] && barChartTooltips[index].skor_alasan) ? barChartTooltips[index].skor_alasan : '';
                                return alasan ? ' Catatan: ' + alasan : '';
                            }
                        }
                    }
                }
            }
        });
    }

    // Inisialisasi Bootstrap Tooltip
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
<?= $this->endSection() ?>