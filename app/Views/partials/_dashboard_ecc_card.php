<?php
/**
 * Partial Kartu Radar Evidence Command Center (ECC) untuk Dashboard Admin & Staf.
 * Memenuhi prinsip DRY, DESIGN.md v3.1.0 (8-Point Grid), dan aksesibilitas keyboard WCAG AA.
 *
 * Variabel yang diharapkan:
 * - array  $prodiData
 * - array  $daftar_tahun
 * - string $tahun_ecc
 * - string $eccFrom ('admin' | 'user')
 */
$eccFrom = $eccFrom ?? 'admin';
?>
<div class="bento-card flex-fill">
    <div class="dash-card-header dash-card-header-auto flex-column flex-md-row align-items-start align-items-md-center">
        <div>
            <h2 class="dash-card-title">Evidence Command Center (ECC)</h2>
            <p class="text-muted mb-0" style="font-size: 0.75rem;">Pemantauan Pemenuhan Standar Mutu</p>
        </div>

        <form id="formEcc" class="m-0 d-flex align-items-center gap-2">
            <label for="tahun_ecc" class="visually-hidden">Pilih Tahun Standar Mutu ECC</label>
            <select name="tahun_ecc" id="tahun_ecc" class="form-select form-select-sm dash-filter-control" style="width: auto; min-width: 120px;" onchange="updateEccData()">
                <?php foreach ($daftar_tahun as $tahun_item): ?>
                    <option value="<?= esc($tahun_item) ?>" <?= ((string) $tahun_ecc === (string) $tahun_item) ? 'selected' : '' ?>>ECC <?= esc($tahun_item) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="bento-body p-4">
        <ul class="nav nav-pills ecc-tabs mb-4 gap-2" id="prodiTab" role="tablist" aria-label="Pilih Program Studi">
            <?php $tabIdx = 0; foreach ($prodiData as $prodi): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 <?= $tabIdx === 0 ? 'active' : '' ?>"
                            style="font-size: 0.875rem; font-weight: 600;"
                            id="tab-<?= esc($prodi['id_prodi']) ?>"
                            data-bs-toggle="tab"
                            data-bs-target="#content-<?= esc($prodi['id_prodi']) ?>"
                            type="button"
                            role="tab"
                            aria-controls="content-<?= esc($prodi['id_prodi']) ?>"
                            aria-selected="<?= $tabIdx === 0 ? 'true' : 'false' ?>">
                        <?= esc($prodi['nama_prodi']) ?>
                    </button>
                </li>
            <?php $tabIdx++; endforeach; ?>
        </ul>

        <div class="tab-content border-0 p-0 shadow-none bg-transparent" id="prodiTabContent">
            <?php $paneIdx = 0; foreach ($prodiData as $prodi): ?>
                <?php
                    $prodiSlug = strtolower((string) $prodi['id_prodi']);
                    $labels    = $prodi['chart_labels'] ?? [];
                    $labelIds  = $prodi['chart_label_ids'] ?? [];
                ?>
                <div class="tab-pane fade <?= $paneIdx === 0 ? 'show active' : '' ?>"
                     id="content-<?= esc($prodi['id_prodi']) ?>"
                     role="tabpanel"
                     aria-labelledby="tab-<?= esc($prodi['id_prodi']) ?>">
                    <?php if (empty($labels)): ?>
                        <div class="alert alert-light text-center border text-muted rounded-4 shadow-none p-4 mb-0">
                            Belum ada data Kategori LED untuk tahun ini.
                        </div>
                    <?php else: ?>
                        <div class="radar-chart-container">
                            <canvas id="radarChart-<?= esc($prodi['id_prodi']) ?>" role="img" aria-label="Grafik Radar Pemenuhan Standar Mutu <?= esc($prodi['nama_prodi']) ?>"></canvas>
                        </div>
                        <p class="text-center text-muted mb-0 mt-2" style="font-size: 0.75rem;">
                            Pilih nama standar pada grafik atau tautan di bawah untuk melihat detail.
                        </p>
                        <div class="dash-ecc-standar-list" id="eccLinks-<?= esc($prodi['id_prodi']) ?>" aria-label="Daftar Standar Mutu <?= esc($prodi['nama_prodi']) ?>">
                            <?php foreach ($labels as $idx => $lbl): ?>
                                <?php $lid = $labelIds[$idx] ?? null; ?>
                                <?php if (!empty($lid)): ?>
                                    <a class="dash-ecc-standar-link"
                                       href="<?= site_url('ecc/detailStandar/' . rawurlencode((string) $lid) . '/' . rawurlencode($prodiSlug) . '/' . rawurlencode((string) $tahun_ecc) . '?from=' . rawurlencode($eccFrom)) ?>">
                                        <?= esc($lbl) ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php $paneIdx++; endforeach; ?>
        </div>
    </div>
</div>
