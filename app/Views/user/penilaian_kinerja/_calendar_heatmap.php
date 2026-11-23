<?php
/**
 * Partial Kalender Heatmap Aktivitas Bulanan (Individu & Staf).
 * Responsif hingga viewport 360px-375px tanpa tumpang-tindih sel, mematuhi DESIGN.md 8-Point Grid.
 *
 * Variabel yang diharapkan:
 * - array    $heatmap
 * - bool     $isStaf
 * - string   $stafId
 * - string   $bulanNama
 * - string   $tahunTerpilih
 * - callable $formatDisplayDecimal
 */
if (empty($heatmap)) {
    return;
}
$isStaf     = !empty($isStaf);
$stafIdAttr = $isStaf ? (string) ($stafId ?? '') : '';
$badgeCls   = $isStaf
    ? 'bg-success-subtle text-success-emphasis border border-success-subtle'
    : 'bg-primary-subtle text-primary-emphasis border border-primary-subtle';
$badgeText  = $isStaf ? 'Kalender Aktivitas Staf' : 'Kalender Aktivitas Saya';
$headingTxt = $isStaf ? 'Distribusi & Konsistensi Kerja Harian Staf' : 'Distribusi & Konsistensi Kerja Harian';
?>
<div class="calendar-heatmap-card mb-4 bento-stagger bento-stagger-3">
    <div class="d-flex justify-content-between align-items-stretch align-items-lg-center flex-column flex-lg-row gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge <?= $badgeCls ?> rounded-pill px-3 py-1 fw-bold">
                    <?= esc($badgeText) ?>
                </span>
                <span class="ecc-text-muted-aa small fw-semibold">
                    Periode: <?= esc($bulanNama) ?> <?= esc($tahunTerpilih) ?>
                </span>
            </div>
            <h3 class="h6 fw-bold text-dark mb-0 mt-2">
                <?= esc($headingTxt) ?>
            </h3>
        </div>
        <div class="heatmap-kpi-grid">
            <div class="bg-light border rounded-3 px-2 px-md-3 py-2 text-center heatmap-kpi-item">
                <div class="ecc-text-muted-aa fw-bold text-uppercase heatmap-kpi-label">Hari Terisi</div>
                <div class="fw-bold text-success-emphasis num-tabular heatmap-kpi-val"><?= (int) $heatmap['summary']['total_hari_terisi'] ?> Hari</div>
            </div>
            <div class="bg-light border rounded-3 px-2 px-md-3 py-2 text-center heatmap-kpi-item">
                <div class="ecc-text-muted-aa fw-bold text-uppercase heatmap-kpi-label">Total Kegiatan</div>
                <div class="fw-bold text-primary-emphasis num-tabular heatmap-kpi-val"><?= (int) $heatmap['summary']['total_log_items'] ?></div>
            </div>
            <div class="bg-light border rounded-3 px-2 px-md-3 py-2 text-center heatmap-kpi-item">
                <div class="ecc-text-muted-aa fw-bold text-uppercase heatmap-kpi-label">Rata / Hari</div>
                <div class="fw-bold text-dark num-tabular heatmap-kpi-val"><?= esc($formatDisplayDecimal($heatmap['summary']['rata_rata_per_hari_aktif'])) ?> Log</div>
            </div>
        </div>
    </div>

    <div class="calendar-heatmap-grid mb-3">
        <div class="calendar-header-cell">Sen</div>
        <div class="calendar-header-cell">Sel</div>
        <div class="calendar-header-cell">Rab</div>
        <div class="calendar-header-cell">Kam</div>
        <div class="calendar-header-cell">Jum</div>
        <div class="calendar-header-cell weekend-header">Sab</div>
        <div class="calendar-header-cell weekend-header">Min</div>

        <?php for ($emptyIdx = 1; $emptyIdx < (int) $heatmap['first_day_of_week']; $emptyIdx++): ?>
            <div class="heatmap-empty-cell" aria-hidden="true"></div>
        <?php endfor; ?>

        <?php foreach ($heatmap['days'] as $day): ?>
            <?php
                $levelClass        = 'heatmap-level-' . (int) $day['level'];
                $tanggalMerahClass = !empty($day['is_tanggal_merah']) ? 'is-tanggal-merah' : '';
                $isRevisi          = !empty($day['is_revisi_aktif']);
                $hasDraftState     = !empty($day['has_draft']) || $isRevisi;
                $draftClass        = $hasDraftState ? 'is-draft' : '';

                $tooltipTitle = esc($day['date_formatted']);
                if (!empty($day['is_holiday'])) {
                    $tooltipTitle .= ' - ' . esc($day['holiday_name']);
                } elseif (!empty($day['is_weekend'])) {
                    $tooltipTitle .= ' - Akhir Pekan';
                }
                if ($isRevisi) {
                    $tooltipTitle .= ' [Revisi Aktif]';
                } elseif (!empty($day['has_draft'])) {
                    $tooltipTitle .= ' [Draf]';
                }
                $tooltipTitle .= ' (' . (int) $day['count_logs'] . ' Kegiatan)';
            ?>
            <div class="heatmap-day-cell <?= $levelClass ?> <?= $tanggalMerahClass ?> <?= $draftClass ?>"
                 role="button"
                 tabindex="0"
                 aria-label="<?= esc($day['date_formatted']) ?>, <?= (int) $day['count_logs'] ?> kegiatan<?= !empty($day['is_holiday']) ? ', libur ' . esc($day['holiday_name']) : '' ?>"
                 data-is-staf="<?= $isStaf ? '1' : '0' ?>"
                 data-staf-id="<?= esc($stafIdAttr) ?>"
                 data-day-num="<?= (int) $day['day_num'] ?>"
                 data-date-str="<?= esc($day['date_str']) ?>"
                 data-tanggal="<?= esc($day['date_str']) ?>"
                 data-tanggal-formatted="<?= esc($day['date_formatted']) ?>"
                 data-is-holiday="<?= !empty($day['is_holiday']) ? '1' : '0' ?>"
                 data-holiday-name="<?= esc($day['holiday_name'] ?? '') ?>"
                 data-is-weekend="<?= !empty($day['is_weekend']) ? '1' : '0' ?>"
                 data-is-tanggal-merah="<?= !empty($day['is_tanggal_merah']) ? '1' : '0' ?>"
                 data-has-draft="<?= $hasDraftState ? '1' : '0' ?>"
                 data-has-terkirim="<?= !empty($day['has_terkirim']) ? '1' : '0' ?>"
                 data-is-revisi="<?= $isRevisi ? '1' : '0' ?>"
                 data-total-kegiatan="<?= (int) $day['count_logs'] ?>"
                 data-logs='<?= htmlspecialchars(json_encode($day['logs']), ENT_QUOTES, 'UTF-8') ?>'
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 title="<?= $tooltipTitle ?>">
                <div class="day-header">
                    <span class="day-num"><?= (int) $day['day_num'] ?></span>
                    <?php if (!empty($day['is_holiday'])): ?>
                        <i class="bi bi-sun-fill holiday-indicator small d-none d-sm-inline" aria-hidden="true" title="<?= esc($day['holiday_name']) ?>"></i>
                    <?php elseif (!empty($day['is_weekend'])): ?>
                        <i class="bi bi-moon-stars ecc-text-muted-aa small d-none d-sm-inline" aria-hidden="true"></i>
                    <?php elseif ($isRevisi): ?>
                        <i class="bi bi-pencil-square text-warning-emphasis small d-none d-sm-inline" aria-hidden="true" title="Mode Revisi Aktif"></i>
                    <?php elseif (!empty($day['has_draft'])): ?>
                        <i class="bi bi-pencil-fill text-warning-emphasis small d-none d-sm-inline" aria-hidden="true"></i>
                    <?php elseif ((int) $day['count_logs'] > 0): ?>
                        <i class="bi bi-check2-circle text-success-emphasis small d-none d-sm-inline" aria-hidden="true"></i>
                    <?php endif; ?>
                </div>
                <div class="day-footer">
                    <?php if ($isRevisi && (int) $day['count_logs'] === 0): ?>
                        <span class="badge-count text-warning-emphasis fw-bold">
                            <span class="d-none d-sm-inline">Revisi</span><span class="d-sm-none">Rev</span>
                        </span>
                    <?php elseif ((int) $day['count_logs'] === 0): ?>
                        <span class="badge-count">-</span>
                    <?php else: ?>
                        <span class="badge-count <?= in_array((int) $day['level'], [3, 4], true) ? 'badge-subtle-custom' : 'fw-bold' ?>">
                            <?= (int) $day['count_logs'] ?><span class="d-none d-sm-inline"> Log</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Legend Footer -->
    <div class="heatmap-legend-container mt-3 pt-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-3">
            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-light ecc-text-muted-aa border px-3 py-2 fw-bold text-uppercase">Keterangan</span>
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-2 fw-semibold">Tgl Merah / Libur</span>
                </div>
                <div class="heatmap-swatch-grid bg-light rounded-3 border px-3 py-2 small">
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-empty"></span>
                        <span class="ecc-text-muted-aa fw-medium">0 Log</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-draft"></span>
                        <span class="text-warning-emphasis fw-semibold">Draf</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-l1"></span>
                        <span class="text-dark fw-medium">1 - 2</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-l2"></span>
                        <span class="text-dark fw-medium">3 - 4</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-l3"></span>
                        <span class="text-dark fw-medium">5 - 6</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="heatmap-legend-swatch heatmap-swatch-l4"></span>
                        <span class="text-dark fw-medium">&gt; 6 Log</span>
                    </div>
                </div>
            </div>
            <div class="heatmap-hint-box text-primary-emphasis bg-primary-subtle rounded-pill border border-primary-subtle px-3 py-2 fw-semibold">
                Pilih tanggal untuk melihat rincian pekerjaan.
            </div>
        </div>
    </div>
</div>
