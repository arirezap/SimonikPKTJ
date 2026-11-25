<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Ringkasan & Hasil Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$periodes    = $periodes ?? [];
$selectedPid = (int) ($selectedPid ?? 0);
$filters     = $filters ?? ['prodi_id' => 0, 'tahun_lulus' => 0];
$refs        = $refs ?? [];
$analytics   = $analytics ?? null;
$comparison  = $comparison ?? [];
$perms       = $perms ?? [];
$canManage   = !empty($perms['can_manage']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <?php if (empty($periodes)): ?>
        <div class="ecc-ts-card text-center py-5">
            <h2 class="h5 fw-bold text-dark mb-2">Belum Ada Periode Tracer Study</h2>
            <p class="ecc-text-muted-aa small mb-3">
                Buat periode pembukaan survei pertama dengan memuat bundel pertanyaan standar yang tersedia.
            </p>
            <?php if ($canManage): ?>
                <a href="<?= base_url('ketarunaan/tracer-study/periode') ?>" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">
                    Buat Periode Pertama
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php
        $periode  = $analytics['periode'] ?? [];
        $isMitra  = !empty($analytics['is_mitra']);
        $kpi      = $analytics['kpi'] ?? [];
        $prodiPrg = $analytics['prodi_progress'] ?? [];
        $tahunPrg = $analytics['tahun_progress'] ?? [];
        $transisi = $analytics['transisi_counts'] ?? [];
        $kesesuai = $analytics['kesesuaian_counts'] ?? [];
        $qSummary = $analytics['question_summaries'] ?? [];
        ?>

        <!-- Pemilih Periode & Filter -->
        <div class="ecc-ts-card mb-4 py-3">
            <form method="get" action="<?= base_url('ketarunaan/tracer-study') ?>" class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="selectPeriodeId" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode Pembukaan Survei</label>
                    <select id="selectPeriodeId" name="periode_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <?php foreach ($periodes as $pr): ?>
                            <option value="<?= (int) $pr['id'] ?>" <?= (int) $pr['id'] === $selectedPid ? 'selected' : '' ?>>
                                <?= esc($pr['nama_periode']) ?> [<?= strtoupper(esc((string) $pr['status_periode'])) ?>] — <?= (int) $pr['jumlah_respons'] ?> respons
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label for="filterProdi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Studi</label>
                    <select id="filterProdi" name="prodi_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="0">Semua Prodi</option>
                        <?php foreach (($refs['prodi'] ?? []) as $pd): ?>
                            <option value="<?= (int) $pd['id'] ?>" <?= (int) ($filters['prodi_id'] ?? 0) === (int) $pd['id'] ? 'selected' : '' ?>>
                                <?= esc($pd['kode_prodi'] . ' - ' . $pd['nama_prodi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="filterTahun" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tahun Lulus</label>
                    <select id="filterTahun" name="tahun_lulus" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="0">Semua Tahun</option>
                        <?php foreach (($refs['tahun_lulus'] ?? []) as $tl): ?>
                            <option value="<?= (int) $tl ?>" <?= (int) ($filters['tahun_lulus'] ?? 0) === (int) $tl ? 'selected' : '' ?>>
                                <?= (int) $tl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid) ?>" class="btn btn-outline-primary ecc-control-sm w-100 fw-semibold d-inline-flex align-items-center justify-content-center ts-touch-ctrl">
                        Lihat Responden
                    </a>
                </div>
            </form>
        </div>

        <?php if ($analytics): ?>
            <?php if ((int) ($kpi['pending_verif_count'] ?? 0) > 0): ?>
                <div class="alert alert-warning border-0 rounded-4 mb-3 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2 shadow-sm">
                    <div class="small text-dark">
                        Terdapat <strong class="ecc-num"><?= (int) $kpi['pending_verif_count'] ?> respons alumni baru</strong> (belum terdaftar di database) yang menunggu verifikasi pengelola agar masuk ke statistik resmi.
                    </div>
                    <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid . '&sub=verifikasi') ?>" class="btn btn-warning btn-sm rounded-pill px-3 fw-semibold text-dark">
                        Tinjau Verifikasi
                    </a>
                </div>
            <?php endif; ?>

            <!-- Banner Status Periode Terpilih -->
            <div class="ecc-ts-card mb-4 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="fw-bold text-dark fs-6"><?= esc((string) $periode['nama_periode']) ?></span>
                            <?= ketarunaan_tracer_target_badge((string) ($periode['target_responden'] ?? 'alumni')) ?>
                            <?= ketarunaan_tracer_status_periode_badge((string) ($periode['status_periode'] ?? 'draf')) ?>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1 ecc-num" style="font-size: 0.75rem;">
                                <?= (int) ($kpi['jumlah_pertanyaan'] ?? 0) ?> Pertanyaan
                            </span>
                        </div>
                        <div class="ecc-text-muted-aa small ecc-num">
                            Kode: <strong><?= esc((string) $periode['kode_periode']) ?></strong>
                            <?php if (!empty($periode['tanggal_mulai']) || !empty($periode['tanggal_selesai'])): ?>
                                &bull; Jadwal: <?= esc((string) ($periode['tanggal_mulai'] ?: '-')) ?> s.d. <?= esc((string) ($periode['tanggal_selesai'] ?: '-')) ?>
                            <?php endif; ?>
                            <?php if (!empty($periode['dibuka_oleh'])): ?>
                                &bull; Dibuka oleh: <?= esc((string) $periode['dibuka_oleh']) ?>
                            <?php endif; ?>
                            <?php if (!empty($periode['ditutup_oleh']) && ($periode['status_periode'] ?? '') === 'ditutup'): ?>
                                &bull; Ditutup oleh: <?= esc((string) $periode['ditutup_oleh']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($periode['public_token'])): ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center"
                                    onclick="openShareTracerModal(this)"
                                    data-nama="<?= esc((string) $periode['nama_periode']) ?>"
                                    data-status="<?= esc((string) ($periode['status_periode'] ?? 'draf')) ?>"
                                    data-public-url="<?= esc(ketarunaan_tracer_public_url((string) $periode['public_token'])) ?>"
                                    data-login-url="<?= esc(base_url('ketarunaan/tracer-study/isi/' . $selectedPid)) ?>"
                                    data-reset-url="<?= esc(base_url('ketarunaan/tracer-study/periode/' . $selectedPid . '/reset-token')) ?>">
                                Bagikan Tautan
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('ketarunaan/tracer-study/periode/' . $selectedPid . '/pertanyaan') ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center">
                            <?= ($periode['status_periode'] ?? '') === 'draf' ? 'Atur Pertanyaan' : 'Lihat Pertanyaan' ?>
                        </a>
                        <?php if ($canManage && ($periode['status_periode'] ?? '') !== 'draf'): ?>
                            <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $selectedPid) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center">
                                Input Respons PIC
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 4 Kartu KPI Progres & Hasil -->
            <?php if ($isMitra): ?>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Total Penilaian Masuk</div>
                            <div class="fw-bold fs-3 text-primary ecc-num"><?= (int) ($kpi['responden_total'] ?? 0) ?></div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Alumni PKTJ yang telah dinilai atasan</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Instansi / Perusahaan</div>
                            <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($kpi['instansi_unik_count'] ?? 0) ?></div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Instansi pengguna lulusan berpartisipasi</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Rata-Rata Skor Kinerja</div>
                            <div class="fw-bold fs-3 text-success ecc-num">
                                <?= number_format((float) ($kpi['avg_likert_overall'] ?? 0), 2, ',', '.') ?>
                                <span class="fs-6 fw-normal ecc-text-muted-aa">/ <?= (int) ($kpi['max_likert_scale'] ?? 4) ?>,00</span>
                            </div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Gabungan seluruh aspek penilaian</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Butir Kuesioner</div>
                            <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($kpi['jumlah_pertanyaan'] ?? 0) ?></div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Identitas, kompetensi &amp; saran prodi</div>
                        </div>
                    </div>
                </div>

                <!-- Sebaran Penilaian per Program Studi (Mitra) -->
                <div class="ecc-ts-card mb-4">
                    <h2 class="h6 fw-bold text-dark mb-3">Sebaran Alumni yang Dinilai per Program Studi</h2>
                    <div class="row g-3">
                        <?php foreach ($prodiPrg as $pp): ?>
                            <?php $pctMitra = ((int) ($kpi['responden_total'] ?? 0) > 0) ? round(((int) $pp['terisi'] / (int) $kpi['responden_total']) * 100, 1) : 0.0; ?>
                            <div class="col-12 col-md-4">
                                <div class="p-3 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center small mb-1">
                                        <span class="fw-bold text-dark"><?= esc((string) $pp['kode_prodi']) ?></span>
                                        <span class="ecc-num fw-semibold text-primary"><?= (int) $pp['terisi'] ?> penilaian (<?= number_format($pctMitra, 1, ',', '.') ?>%)</span>
                                    </div>
                                    <div class="ecc-text-muted-aa mb-2" style="font-size: 0.72rem;"><?= esc((string) $pp['nama_prodi']) ?></div>
                                    <div class="ts-progress">
                                        <div class="ts-progress-bar" style="width: <?= min(100, max(0, $pctMitra)) ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-3 mb-4">
                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Progres Partisipasi</div>
                            <div class="fw-bold fs-3 text-primary ecc-num"><?= number_format((float) ($kpi['response_rate'] ?? 0), 1, ',', '.') ?>%</div>
                            <div class="ts-progress my-2">
                                <div class="ts-progress-bar" style="width: <?= min(100, max(0, (float) ($kpi['response_rate'] ?? 0))) ?>%;"></div>
                            </div>
                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                <strong><?= (int) ($kpi['responden_total'] ?? 0) ?></strong> dari <?= (int) ($kpi['sasaran_total'] ?? 0) ?> alumni (Target <?= number_format((float) ($kpi['target_persen'] ?? 80), 0) ?>%)
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Lulusan Terserap</div>
                            <div class="fw-bold fs-3 text-success ecc-num"><?= number_format((float) ($kpi['terserap_persen'] ?? 0), 1, ',', '.') ?>%</div>
                            <div class="ecc-text-muted-aa ecc-num mt-2" style="font-size: 0.75rem;">
                                <?= (int) ($kpi['terserap_count'] ?? 0) ?> responden bekerja, wirausaha, atau studi lanjut
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Rata-Rata Masa Tunggu</div>
                            <div class="fw-bold fs-3 text-dark ecc-num"><?= number_format((float) ($kpi['avg_masa_tunggu'] ?? 0), 1, ',', '.') ?> <span class="fs-6 fw-normal ecc-text-muted-aa">bln</span></div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">
                                Dari lulus hingga pekerjaan pertama
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-xl-3">
                        <div class="ecc-ts-kpi">
                            <div class="ecc-text-muted-aa mb-1 fw-semibold" style="font-size: 0.75rem;">Kesesuaian Bidang</div>
                            <div class="fw-bold fs-3 text-dark ecc-num"><?= number_format((float) ($kpi['sesuai_bidang_pct'] ?? 0), 1, ',', '.') ?>%</div>
                            <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">
                                Pekerjaan sesuai atau sangat sesuai prodi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progres per Prodi & Grafik Distribusi Transisi -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-lg-7">
                        <div class="ecc-ts-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold text-dark mb-0">Progres Partisipasi per Program Studi</h2>
                                <a href="<?= base_url('ketarunaan/tracer-study/responden?periode_id=' . $selectedPid . '&sub=belum') ?>" class="btn btn-light border btn-sm rounded-pill px-3" style="font-size: 0.75rem;">
                                    Belum Mengisi (<?= (int) ($kpi['belum_mengisi'] ?? 0) ?>)
                                </a>
                            </div>
                            <?php if (empty($prodiPrg)): ?>
                                <p class="ecc-text-muted-aa small mb-0">Belum ada data alumni sasaran pada periode ini.</p>
                            <?php else: ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($prodiPrg as $pp): ?>
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                                <span class="fw-semibold text-dark"><?= esc((string) $pp['kode_prodi']) ?> <span class="fw-normal ecc-text-muted-aa">— <?= esc((string) $pp['nama_prodi']) ?></span></span>
                                                <span class="ecc-num fw-semibold">
                                                    <?= (int) $pp['terisi'] ?> / <?= (int) $pp['sasaran'] ?> (<?= number_format((float) $pp['persen'], 1, ',', '.') ?>%)
                                                </span>
                                            </div>
                                            <div class="ts-progress">
                                                <div class="ts-progress-bar" style="width: <?= min(100, max(0, (float) $pp['persen'])) ?>%;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($tahunPrg)): ?>
                                <hr class="my-3">
                                <div class="fw-semibold small text-dark mb-2">Sebaran per Tahun Lulus</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($tahunPrg as $tp): ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 ecc-num" style="font-size: 0.75rem;">
                                            Lulus <?= (int) $tp['tahun_lulus'] ?>: <strong><?= (int) $tp['terisi'] ?>/<?= (int) $tp['sasaran'] ?></strong> (<?= number_format((float) $tp['persen'], 0) ?>%)
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="ecc-ts-card h-100">
                            <h2 class="h6 fw-bold text-dark mb-3">Distribusi Status Lulusan</h2>
                            <?php if ((int) ($kpi['responden_total'] ?? 0) === 0): ?>
                                <div class="text-center py-5 ecc-text-muted-aa small">
                                    Belum ada respons masuk pada periode ini.
                                </div>
                            <?php else: ?>
                                <div style="height: 220px; position: relative;">
                                    <canvas id="chartTransisiTracer"></canvas>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Rekap Hasil per Pertanyaan Bundel pada Periode Ini -->
            <div class="ecc-ts-card mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h2 class="h6 fw-bold text-dark mb-0">Hasil Survei per Pertanyaan</h2>
                        <div class="ecc-text-muted-aa small">Agregasi otomatis berdasarkan bundel pertanyaan pada periode terpilih.</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 ecc-num" style="font-size: 0.75rem;">
                        <?= (int) ($kpi['responden_total'] ?? 0) ?> Responden
                    </span>
                </div>

                <?php if (empty($qSummary)): ?>
                    <p class="ecc-text-muted-aa small mb-0">Belum ada pertanyaan aktif pada periode ini.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($qSummary as $idx => $qs): ?>
                            <div class="col-12 col-lg-6">
                                <div class="ts-q-item h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                            <?= esc((string) $qs['bagian']) ?>
                                        </span>
                                        <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                            <?= (int) $qs['total_jawaban'] ?> jawaban
                                        </span>
                                    </div>
                                    <div class="fw-semibold text-dark small mb-3">
                                        <?= ($idx + 1) . '. ' . esc((string) $qs['teks_pertanyaan']) ?>
                                    </div>

                                    <?php if ((int) $qs['total_jawaban'] === 0): ?>
                                        <div class="ecc-text-muted-aa small">Belum ada jawaban masuk.</div>
                                    <?php elseif (in_array($qs['tipe_jawaban'], ['pilihan_tunggal', 'pilihan_ganda'], true)): ?>
                                        <div class="d-flex flex-column gap-2">
                                            <?php foreach ($qs['options'] as $opt): ?>
                                                <div>
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span class="text-dark"><?= esc((string) $opt['label']) ?></span>
                                                        <span class="ecc-num fw-semibold"><?= (int) $opt['count'] ?> (<?= number_format((float) $opt['persen'], 1, ',', '.') ?>%)</span>
                                                    </div>
                                                    <div class="ts-progress">
                                                        <div class="ts-progress-bar" style="width: <?= min(100, max(0, (float) $opt['persen'])) ?>%;"></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($qs['tipe_jawaban'] === 'skala_likert'): ?>
                                        <?php $maxS = max(3, (int) ($qs['likert_max'] ?? 5)); ?>
                                        <div class="d-flex align-items-baseline gap-2 mb-2">
                                            <span class="fs-4 fw-bold text-primary ecc-num"><?= number_format((float) $qs['likert_avg'], 2, ',', '.') ?></span>
                                            <span class="small ecc-text-muted-aa ecc-num">dari skala <?= $maxS ?>,00</span>
                                        </div>
                                        <div class="d-flex flex-column gap-1">
                                            <?php for ($s = $maxS; $s >= 1; $s--): ?>
                                                <?php
                                                $sc = (int) ($qs['likert_dist'][$s] ?? 0);
                                                $sp = $qs['total_jawaban'] > 0 ? round(($sc / $qs['total_jawaban']) * 100, 1) : 0;
                                                ?>
                                                <div class="d-flex align-items-center gap-2 small">
                                                    <span class="ecc-num fw-semibold" style="width: 44px;">Skor <?= $s ?></span>
                                                    <div class="ts-progress flex-grow-1">
                                                        <div class="ts-progress-bar" style="width: <?= $sp ?>%;"></div>
                                                    </div>
                                                    <span class="ecc-num text-end" style="width: 72px;"><?= $sc ?> (<?= number_format($sp, 0) ?>%)</span>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    <?php elseif ($qs['tipe_jawaban'] === 'angka'): ?>
                                        <div class="d-flex flex-wrap gap-3 small ecc-num">
                                            <div>Rata-rata: <strong><?= number_format((float) $qs['angka_stats']['avg'], 2, ',', '.') ?></strong></div>
                                            <div>Minimum: <strong><?= number_format((float) $qs['angka_stats']['min'], 2, ',', '.') ?></strong></div>
                                            <div>Maksimum: <strong><?= number_format((float) $qs['angka_stats']['max'], 2, ',', '.') ?></strong></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex flex-column gap-2" style="max-height: 180px; overflow-y: auto;">
                                            <?php foreach ($qs['text_samples'] as $sm): ?>
                                                <div class="p-2 rounded-3 bg-light border small">
                                                    <div class="fw-semibold text-dark" style="font-size: 0.75rem;"><?= esc((string) $sm['nama']) ?></div>
                                                    <div class="text-dark"><?= esc((string) $sm['teks']) ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Perbandingan Antar Periode -->
            <?php if (count($comparison) > 1): ?>
                <div class="ecc-ts-card">
                    <h2 class="h6 fw-bold text-dark mb-3">Perbandingan Progres &amp; Hasil Antar-Periode</h2>
                    <div class="table-responsive ecc-table-ts-wrap">
                        <table class="table ecc-table-ts mb-0">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th class="text-end">Responden / Sasaran</th>
                                    <th class="text-end">Partisipasi</th>
                                    <th class="text-end">Terserap</th>
                                    <th class="text-end">Masa Tunggu</th>
                                    <th class="text-end">Sesuai Bidang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comparison as $cp): ?>
                                    <tr class="<?= (int) $cp['id'] === $selectedPid ? 'table-primary bg-opacity-10' : '' ?>">
                                        <td>
                                            <a href="<?= base_url('ketarunaan/tracer-study?periode_id=' . (int) $cp['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                                <?= esc((string) $cp['nama_periode']) ?>
                                            </a>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $cp['kode_periode']) ?></div>
                                        </td>
                                        <td><?= ketarunaan_tracer_status_periode_badge((string) $cp['status_periode']) ?></td>
                                        <td class="text-end ecc-num"><?= (int) $cp['responden_total'] ?> / <?= (int) $cp['sasaran_total'] ?></td>
                                        <td class="text-end ecc-num fw-semibold"><?= number_format((float) $cp['response_rate'], 1, ',', '.') ?>%</td>
                                        <td class="text-end ecc-num"><?= number_format((float) $cp['terserap_persen'], 1, ',', '.') ?>%</td>
                                        <td class="text-end ecc-num"><?= number_format((float) $cp['avg_masa_tunggu'], 1, ',', '.') ?> bln</td>
                                        <td class="text-end ecc-num"><?= number_format((float) $cp['sesuai_bidang'], 1, ',', '.') ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->include('ketarunaan/tracer_study/_share_link_modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($analytics) && (int) ($analytics['kpi']['responden_total'] ?? 0) > 0): ?>
<script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var canvas = document.getElementById('chartTransisiTracer');
    if (!canvas || typeof Chart === 'undefined') return;
    var items = <?= json_encode($analytics['transisi_counts'] ?? [], JSON_UNESCAPED_UNICODE) ?>;
    var labels = items.map(function(i) { return i.label; });
    var values = items.map(function(i) { return i.count; });

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#059669', '#1e40af', '#0284c7', '#d97706', '#64748b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 } } }
            }
        }
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
