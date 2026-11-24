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

$scorePillClass = static function (float $rata, int $dinilai): string {
    if ($dinilai <= 0 || $rata <= 0) return 'dash-score-neutral';
    if ($rata > 100) return 'dash-score-success';
    if ($rata > 90) return 'dash-score-primary';
    if ($rata > 75) return 'dash-score-info';
    if ($rata > 25) return 'dash-score-warning';
    return 'dash-score-danger';
};

$hasRekapSection = (!empty($isAtasan) || !empty($isUnitPeers));
$hasRekapData    = ($hasRekapSection && !empty($rekapDashboard));
?>

<!-- Toolbar Filter Periode Kinerja (8-Point Grid) -->
<div class="d-flex justify-content-start align-items-center flex-wrap gap-2 mb-3 bento-stagger bento-stagger-1">
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
<div id="dashKinerjaError" class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-none" role="alert" style="font-size: 0.875rem;"></div>

<!-- Bagian 1: Radar ECC & Metrik Kinerja Pribadi -->
<div class="row g-4 mb-4 bento-stagger bento-stagger-1">
    <div class="col-lg-8 d-flex flex-column">
        <?= view('partials/_dashboard_ecc_card', [
            'prodiData'    => $prodiData,
            'daftar_tahun' => $daftar_tahun,
            'tahun_ecc'    => $tahun_ecc,
            'eccFrom'      => 'user',
        ]) ?>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-4">
        <div class="bento-card dash-kpi-primary flex-fill">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="dash-kpi-label">Nilai Rata-Rata Kinerja</div>
                <div class="dash-kpi-value" id="valRataRataCapaian" aria-live="polite" aria-atomic="true">
                    <?= number_format((float) $rataRataCapaian, 2, ',', '.') ?>
                </div>
                <div class="progress w-100 bg-white bg-opacity-25 rounded-pill" style="height: 8px;" aria-hidden="true">
                    <div class="progress-bar bg-warning rounded-pill dash-kpi-bar" id="barRataRataCapaian" style="width: <?= min(100, (float) $rataRataCapaian) ?>%;"></div>
                </div>
            </div>
        </div>

        <div class="bento-card dash-kpi-secondary flex-fill">
            <div class="d-flex flex-column justify-content-center h-100">
                <div class="dash-kpi-label text-muted">Komponen Dinilai</div>
                <div class="dash-kpi-value text-dark" id="valTotalIndikator" aria-live="polite" aria-atomic="true">
                    <?= (int) $totalIndikator ?>
                </div>
                <div class="dash-kpi-meta">RHK Utama &amp; Tugas Tambahan</div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian 2: Tren Kinerja Pribadi -->
<div class="bento-stagger bento-stagger-2">
    <div class="mb-3 mt-4">
        <h2 class="dash-section-title">Kinerja Pribadi</h2>
        <p class="dash-section-subtitle">Perkembangan nilai rata-rata kinerja bulanan Anda pada tahun terpilih.</p>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bento-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">Tren Nilai Rata-Rata Kinerja Bulanan</h3>
                </div>
                <div class="bento-body p-4">
                    <div class="line-chart-container">
                        <canvas id="kumulatifChart" role="img" aria-label="Grafik Tren Nilai Rata-Rata Kinerja Bulanan Pribadi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bagian 3: Kinerja Staf / Rekan 1 Unit (Selalu ada di DOM agar AJAX dapat menampilkannya) -->
<div class="bento-stagger bento-stagger-3 <?= $hasRekapData ? '' : 'd-none' ?>" id="tabelRekapSection">
    <div class="mb-3 mt-4">
        <h2 class="dash-section-title" id="tabelRekapTitle">
            <?= !empty($isAtasan) ? 'Pemantauan Kinerja Staf Saya' : 'Kinerja Rekan 1 Unit Kerja' ?>
        </h2>
        <p class="dash-section-subtitle <?= empty($peerUnitSubLabel) || !empty($isAtasan) ? 'd-none' : '' ?>" id="tabelRekapSubtitle">
            <?= esc($peerUnitSubLabel ?? '') ?>
        </p>
    </div>
    <div class="row g-4 mb-4" id="tabelRekapContainer">
        <div class="col-12">
            <div class="bento-card">
                <!-- Tabel Desktop (>= 768px) -->
                <div class="bento-body p-0 d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border-0 table-bento" style="font-size: 0.875rem;">
                            <thead class="bg-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                                <tr>
                                    <th scope="col" class="ps-4 border-0" style="padding: 12px 16px;">Nama Pegawai</th>
                                    <th scope="col" class="text-center border-0" style="padding: 12px 16px;">Jabatan</th>
                                    <th scope="col" class="text-center border-0" style="padding: 12px 16px;">Target &amp; Tambahan</th>
                                    <th scope="col" class="text-center border-0" style="padding: 12px 16px;">Rata-Rata Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0" id="tbodyKinerjaStaf">
                                <?php foreach ($rekapDashboard as $rekap): ?>
                                    <?php
                                        $rata          = (float) $rekap['rata_rata'];
                                        $dinilai       = (int) ($rekap['dinilai'] ?? 0);
                                        $scoreCls      = $scorePillClass($rata, $dinilai);
                                        $namaRapih     = $formatNama($rekap['staf']['nama_lengkap'] ?? '');
                                        $inisial       = strtoupper(substr(trim($namaRapih), 0, 1));
                                        $stafUnit      = trim((string) ($rekap['staf']['unit'] ?? ''));
                                        $stafPokjaMeta = !empty($isAtasan) && !empty($rekap['staf']['parent_unit_name']) ? $stafUnit : '';
                                    ?>
                                    <tr>
                                        <td class="ps-4 border-bottom border-light" style="padding: 12px 16px;">
                                            <div class="d-flex align-items-center gap-3">
                                                <div aria-hidden="true" class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.875rem;">
                                                    <?= esc($inisial) ?>
                                                </div>
                                                <div style="min-width: 0;">
                                                    <div class="fw-semibold text-dark text-truncate d-flex align-items-center gap-2">
                                                        <span class="text-truncate"><?= esc($namaRapih) ?></span>
                                                        <?php if (($rekap['staf']['status_pegawai'] ?? '') === 'mutasi_keluar'): ?>
                                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Mutasi</span>
                                                        <?php elseif (($rekap['staf']['status_pegawai'] ?? '') === 'pensiun'): ?>
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Pensiun</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-muted num-tabular" style="font-size: 0.75rem;"><?= esc($rekap['staf']['nip'] ?: '-') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted border-bottom border-light" style="padding: 12px 16px;">
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 text-wrap" style="font-size: 0.75rem;"><?= esc($rekap['staf']['jabatan'] ?: '-') ?></span>
                                            <?php if ($stafPokjaMeta !== ''): ?>
                                                <div class="text-muted mt-1" style="font-size: 0.75rem;"><?= esc($stafPokjaMeta) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                            <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">
                                                    <?= (int) ($rekap['total_pokok'] ?? $rekap['total_laporan']) ?> Pokok
                                                </span>
                                                <span class="badge <?= ($rekap['total_tambahan'] ?? 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?> rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">
                                                    <?= (int) ($rekap['total_tambahan'] ?? 0) ?> Tambahan
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                            <?php if ($rata == 0 || $dinilai == 0): ?>
                                                <span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>
                                            <?php else: ?>
                                                <span class="dash-score-pill <?= $scoreCls ?>"><?= number_format($rata, 2, ',', '.') ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Daftar Kartu Mobile (< 768px) -->
                <div class="d-md-none p-3 d-flex flex-column gap-3" id="mobileCardsKinerjaStaf">
                    <?php foreach ($rekapDashboard as $rekap): ?>
                        <?php
                            $rata      = (float) $rekap['rata_rata'];
                            $dinilai   = (int) ($rekap['dinilai'] ?? 0);
                            $scoreCls  = $scorePillClass($rata, $dinilai);
                            $namaRapih = $formatNama($rekap['staf']['nama_lengkap'] ?? '');
                        ?>
                        <div class="p-3 rounded-3 border bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-dark d-flex align-items-center gap-1 flex-wrap" style="font-size: 0.875rem;">
                                        <span><?= esc($namaRapih) ?></span>
                                        <?php if (($rekap['staf']['status_pegawai'] ?? '') === 'mutasi_keluar'): ?>
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Mutasi</span>
                                        <?php elseif (($rekap['staf']['status_pegawai'] ?? '') === 'pensiun'): ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Pensiun</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted num-tabular" style="font-size: 0.75rem;"><?= esc($rekap['staf']['jabatan'] ?: '-') ?> • <?= esc($rekap['staf']['nip'] ?: '-') ?></div>
                                </div>
                                <?php if ($rata == 0 || $dinilai == 0): ?>
                                    <span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>
                                <?php else: ?>
                                    <span class="dash-score-pill <?= $scoreCls ?>"><?= number_format($rata, 2, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">
                                    <?= (int) ($rekap['total_pokok'] ?? $rekap['total_laporan']) ?> Pokok
                                </span>
                                <span class="badge <?= ($rekap['total_tambahan'] ?? 0) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border' ?> rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">
                                    <?= (int) ($rekap['total_tambahan'] ?? 0) ?> Tambahan
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= view('partials/_dashboard_ecc_script', ['eccFrom' => 'user']) ?>

<script>
window.userDashboardCharts = window.userDashboardCharts || {};

async function updateKinerjaData() {
    const tahunKinerja = document.getElementById('tahun_kinerja').value;
    const bulanKinerja = document.getElementById('bulan_kinerja') ? document.getElementById('bulan_kinerja').value : 'all';
    const formEl = document.getElementById('formKinerja');
    const errorEl = document.getElementById('dashKinerjaError');
    if (errorEl) errorEl.classList.add('d-none');
    if (formEl) formEl.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=kinerja&tahun_kinerja=${encodeURIComponent(tahunKinerja)}&bulan_kinerja=${encodeURIComponent(bulanKinerja)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) {
            throw new Error('Gagal memuat data');
        }
        const data = await response.json();
        if (!data) return;

        const currentRr = window.eccDash.parseAngkaId(document.getElementById('valRataRataCapaian')?.innerText);
        const rr = Number(data.rataRataCapaian || 0);
        window.eccDash.animateValue('valRataRataCapaian', currentRr, rr, 650, '', 2);

        const bar = document.getElementById('barRataRataCapaian');
        if (bar) bar.style.width = Math.min(100, rr) + '%';

        const currentTot = window.eccDash.parseAngkaId(document.getElementById('valTotalIndikator')?.innerText);
        window.eccDash.animateValue('valTotalIndikator', currentTot, data.totalIndikator || 0, 650, '', 0);

        if (window.userDashboardCharts['kumulatifChart']) {
            window.userDashboardCharts['kumulatifChart'].data.datasets[0].data = data.cumulative_realisasi || [];
            window.userDashboardCharts['kumulatifChart'].update();
        }

        const isAtasan = !!data.isAtasan;
        const isUnitPeers = !!data.isUnitPeers;
        const peerUnitSubLabel = data.peerUnitSubLabel || '';
        const rekapDashboard = data.rekapDashboard || [];
        const sectionEl = document.getElementById('tabelRekapSection');
        const titleEl = document.getElementById('tabelRekapTitle');
        const subtitleEl = document.getElementById('tabelRekapSubtitle');
        const tbodyEl = document.getElementById('tbodyKinerjaStaf');
        const mobileEl = document.getElementById('mobileCardsKinerjaStaf');

        if (sectionEl && titleEl && tbodyEl) {
            if ((isAtasan || isUnitPeers) && rekapDashboard.length > 0) {
                sectionEl.classList.remove('d-none');
                titleEl.innerText = isAtasan ? 'Pemantauan Kinerja Staf Saya' : 'Kinerja Rekan 1 Unit Kerja';
                if (subtitleEl) {
                    if (!isAtasan && peerUnitSubLabel) {
                        subtitleEl.innerText = peerUnitSubLabel;
                        subtitleEl.classList.remove('d-none');
                    } else {
                        subtitleEl.classList.add('d-none');
                    }
                }

                let htmlDesktop = '';
                let htmlMobile = '';
                rekapDashboard.forEach(rekap => {
                    const rata = Number(rekap.rata_rata || 0);
                    const dinilai = Number(rekap.dinilai || 0);
                    const scoreCls = window.eccDash.getPredikatScoreClass(rata, dinilai);
                    const namaRapih = window.eccDash.formatNamaTitleCase(rekap.staf?.nama_lengkap);
                    const inisial = namaRapih.charAt(0).toUpperCase();
                    const jabatan = rekap.staf?.jabatan || '-';
                    const nip = rekap.staf?.nip || '-';
                    const pokjaTag = (isAtasan && rekap.staf?.parent_unit_name && rekap.staf?.unit) ? String(rekap.staf.unit) : '';
                    const statusBadge = window.eccDash.getStatusPegawaiBadge(rekap.staf?.status_pegawai);
                    const pokokVal = rekap.total_pokok !== undefined ? rekap.total_pokok : rekap.total_laporan;
                    const tambVal = rekap.total_tambahan || 0;
                    const scoreHtml = (rata === 0 || dinilai === 0)
                        ? '<span class="dash-score-pill dash-score-neutral">Belum Dinilai</span>'
                        : `<span class="dash-score-pill ${scoreCls}">${window.eccDash.formatAngkaId(rata, 2)}</span>`;

                    htmlDesktop += `
                        <tr>
                            <td class="ps-4 border-bottom border-light" style="padding: 12px 16px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div aria-hidden="true" class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 40px; height: 40px; font-size: 0.875rem;">
                                        ${window.eccDash.escapeHtml(inisial)}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-semibold text-dark text-truncate d-flex align-items-center gap-2">
                                            <span class="text-truncate">${window.eccDash.escapeHtml(namaRapih)}</span>${statusBadge}
                                        </div>
                                        <div class="text-muted num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(nip)}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-muted border-bottom border-light" style="padding: 12px 16px;">
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 text-wrap" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(jabatan)}</span>
                                ${pokjaTag ? `<div class="text-muted mt-1" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(pokjaTag)}</div>` : ''}
                            </td>
                            <td class="text-center border-bottom border-light" style="padding: 12px 16px;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(pokokVal)} Pokok</span>
                                    <span class="badge ${tambVal > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border'} rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(tambVal)} Tambahan</span>
                                </div>
                            </td>
                            <td class="text-center border-bottom border-light" style="padding: 12px 16px;">${scoreHtml}</td>
                        </tr>`;

                    htmlMobile += `
                        <div class="p-3 rounded-3 border bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div style="min-width: 0;">
                                    <div class="fw-semibold text-dark" style="font-size: 0.875rem;">${window.eccDash.escapeHtml(namaRapih)}${statusBadge}</div>
                                    <div class="text-muted num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(jabatan)}${pokjaTag ? ' · ' + window.eccDash.escapeHtml(pokjaTag) : ''} · ${window.eccDash.escapeHtml(nip)}</div>
                                </div>
                                ${scoreHtml}
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(pokokVal)} Pokok</span>
                                <span class="badge ${tambVal > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-muted border'} rounded-pill px-2 py-1 num-tabular" style="font-size: 0.75rem;">${window.eccDash.escapeHtml(tambVal)} Tambahan</span>
                            </div>
                        </div>`;
                });

                tbodyEl.innerHTML = htmlDesktop;
                if (mobileEl) mobileEl.innerHTML = htmlMobile;
            } else {
                sectionEl.classList.add('d-none');
            }
        }
    } catch (err) {
        if (errorEl) {
            errorEl.textContent = 'Gagal memperbarui data kinerja. Silakan pilih ulang periode atau muat ulang halaman.';
            errorEl.classList.remove('d-none');
        }
    } finally {
        if (formEl) formEl.removeAttribute('aria-busy');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    window.eccDash.animateValue('valRataRataCapaian', 0, <?= round((float) $rataRataCapaian, 2) ?>, 650, '', 2);
    window.eccDash.animateValue('valTotalIndikator', 0, <?= (int) $totalIndikator ?>, 650, '', 0);

    for (let key in window.userDashboardCharts) {
        if (window.userDashboardCharts[key]) {
            window.userDashboardCharts[key].destroy();
            delete window.userDashboardCharts[key];
        }
    }

    const prodiData = <?= json_encode($prodiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    window.eccDash.initEccRadar(prodiData, '<?= esc($tahun_ecc, 'js') ?>', window.userDashboardCharts);

    const ctxKumulatif = document.getElementById('kumulatifChart');
    if (ctxKumulatif && typeof Chart !== 'undefined') {
        window.userDashboardCharts['kumulatifChart'] = new Chart(ctxKumulatif.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($lineChartLabels ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [{
                    label: 'Nilai Rata-Rata',
                    data: <?= json_encode($lineChartRealisasiData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.10)',
                    borderWidth: 3,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    spanGaps: false,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: window.eccDash.chartAnimationOption(),
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ctx.parsed.y === null ? 'Belum dinilai' : 'Nilai Rata-Rata: ' + window.eccDash.formatAngkaId(ctx.parsed.y, 2)
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, suggestedMax: 100, grace: '8%', grid: { borderDash: [4, 4], color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
