<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
    .num-tabular {
        font-variant-numeric: tabular-nums;
    }
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    .table-bento {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 820px;
    }
    .table-bento thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
    }
    .table-bento tbody td {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        border-color: #f1f5f9;
        transition: background-color 0.2s ease;
    }
    .table-bento tbody tr:last-child td {
        border-bottom: 0;
    }
    .date-group-odd {
        background-color: #ffffff;
    }
    .date-group-even {
        background-color: #f8fafc;
    }
    .date-group-odd:hover, .date-group-even:hover {
        background-color: #f1f5f9;
    }
    .col-target { min-width: 220px; }
    .col-nilai { min-width: 150px; }
    .scrollable-table {
        max-height: 480px;
        overflow-y: auto !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }
    .scrollable-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background-color: #f8fafc !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .input-nilai-capaian::-webkit-outer-spin-button,
    .input-nilai-capaian::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-nilai-capaian {
        -moz-appearance: textfield;
    }
    .predikat-rule-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Motion Design & Ergonomic Transitions */
    .badge-predikat-pop {
        transform: scale(1.08);
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .score-card-transition {
        transition: border-color 0.3s ease, color 0.3s ease, background-color 0.3s ease;
    }
    .tab-content > .tab-pane {
        transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .tab-content > .tab-pane.fade:not(.show) {
        transform: translateY(4px);
    }
    .tab-content > .tab-pane.fade.show {
        transform: translateY(0);
    }
    .btn-tactile {
        transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.15s ease;
    }
    .btn-tactile:active {
        transform: scale(0.97);
    }

    @media (prefers-reduced-motion: reduce) {
        .badge-predikat-pop, .tab-content > .tab-pane, .btn-tactile {
            animation: none !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2 bento-stagger bento-stagger-1">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-award-fill text-primary me-2"></i>Rekap & Penilaian Kinerja</h1>
            <p class="text-muted small mb-0">Evaluasi capaian target RHK, realisasi harian, dan penerbitan nilai capaian kinerja.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                <i class="bi bi-calendar-month me-1"></i> <?= esc($bulan_indo[$bulan_terpilih - 1] ?? '') ?> <?= esc($tahun_terpilih) ?>
            </span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm py-2 px-3 small rounded-3 bento-stagger bento-stagger-1" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden bento-stagger bento-stagger-2">
        <div class="card-body p-3 p-md-4">
            
            <!-- Filter Bar Toolbar -->
            <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-3 p-3 bg-light rounded-4 border border-light-subtle" id="filterForm">
                <?= csrf_field() ?>
                <input type="hidden" name="active_tab" id="active_tab_input" value="<?= empty($staf_id_terpilih) ? 'individu' : 'staf' ?>">
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-sm-3 col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-month text-primary me-1"></i> Bulan</label>
                        <select name="bulan" class="form-select form-select-sm border-primary fw-semibold" aria-label="Pilih Bulan Penilaian" onchange="this.form.submit()">
                            <?php foreach($bulan_indo as $index => $nama): ?>
                                <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-sm-2 col-md-2">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-calendar-event text-primary me-1"></i> Tahun</label>
                        <input type="number" name="tahun" class="form-control form-control-sm border-primary fw-semibold" aria-label="Input Tahun Penilaian" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                    </div>
                    
                    <?php if ($is_super): ?>
                    <div class="col-12 col-sm-4 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1" style="font-size: 0.72rem; letter-spacing: 0.3px;"><i class="bi bi-building text-primary me-1"></i> Unit Kerja</label>
                        <select name="unit_kerja" class="form-select form-select-sm border-primary fw-semibold" aria-label="Pilih Unit Kerja" onchange="this.form.submit()">
                            <option value="">Semua Unit Kerja</option>
                            <?php foreach ($daftar_unit as $u): ?>
                                <option value="<?= esc($u) ?>" <?= ($u == $unit_kerja_terpilih) ? 'selected' : '' ?>><?= esc($u) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </form>

        <!-- Navigation Tabs -->
        <ul class="nav segmented-control mb-4 bento-stagger bento-stagger-2" id="penilaianTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-tactile <?= empty($staf_id_terpilih) ? 'active' : '' ?>" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-person-fill me-1.5"></i> Target Saya
                </button>
            </li>
            <?php if ($is_atasan): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link btn-tactile <?= !empty($staf_id_terpilih) ? 'active' : '' ?>" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-people-fill me-1.5"></i> Penilaian Staf
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content" id="penilaianTabsContent">
            
            <!-- ==================== TAB 1: TARGET INDIVIDU SAYA ==================== -->
            <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="individu" role="tabpanel" aria-labelledby="individu-tab">
                <?php if (empty($rekap_data_sendiri)): ?>
                    <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                        <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Target Kinerja</h6>
                        <p class="text-muted small mb-0">Belum ada target RHK yang ditetapkan pada periode <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>.</p>
                    </div>
                <?php else: ?>
                    <?php
                        $jmlDinilai = 0;
                        $totalNilai = 0;
                        foreach ($rekap_data_sendiri as $rd) {
                            if (isset($rd['status_penilaian']) && $rd['status_penilaian'] === 'terbit' && $rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') {
                                $jmlDinilai++;
                                $totalNilai += (float)$rd['nilai_capaian'];
                            }
                        }
                        
                        // Perhitungan tugas tambahan jika dinilai dan terbit
                        $scoreTambahanIndividu = null;
                        if (!empty($tugas_tambahan_sendiri)) {
                            foreach ($tugas_tambahan_sendiri as $tmb) {
                                if (isset($tmb['status_penilaian']) && $tmb['status_penilaian'] === 'terbit' && $tmb['nilai_capaian'] !== null) {
                                    $scoreTambahanIndividu = (float)$tmb['nilai_capaian'];
                                    break;
                                }
                            }
                        }

                        if ($scoreTambahanIndividu !== null) {
                            $totalNilai += $scoreTambahanIndividu;
                            $jmlDinilai++;
                        }

                        $rataRataIndividu = $jmlDinilai > 0 ? (float)($totalNilai / $jmlDinilai) : 0;
                        
                        $predikatRataIndividu = '-';
                        $badgeColorRataIndividu = 'secondary';
                        $warnaScore = 'success';
                        if ($jmlDinilai == 0) {
                            $warnaScore = 'secondary';
                        } else {
                            if ($rataRataIndividu <= 25) { $warnaScore = 'danger'; $predikatRataIndividu = 'Sangat Kurang'; $badgeColorRataIndividu = 'danger'; }
                            elseif ($rataRataIndividu <= 75) { $warnaScore = 'warning text-dark'; $predikatRataIndividu = 'Kurang'; $badgeColorRataIndividu = 'warning text-dark'; }
                            elseif ($rataRataIndividu <= 90) { $warnaScore = 'info text-dark'; $predikatRataIndividu = 'Butuh Perbaikan'; $badgeColorRataIndividu = 'info text-dark'; }
                            elseif ($rataRataIndividu <= 100) { $warnaScore = 'primary'; $predikatRataIndividu = 'Baik'; $badgeColorRataIndividu = 'primary'; }
                            else { $warnaScore = 'success'; $predikatRataIndividu = 'Sangat Baik'; $badgeColorRataIndividu = 'success'; }
                        }
                    ?>

                    <!-- GUIDANCE & RULES BANNER -->
                    <div class="alert alert-light border border-info-subtle shadow-sm mb-3 py-2 px-3 text-dark rounded-3" style="font-size: 0.76rem;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <span class="fw-bold text-secondary text-nowrap"><i class="bi bi-journal-check text-primary me-1"></i> Standar Predikat:</span>
                            <span class="text-nowrap"><span class="badge bg-danger px-1.5 py-0.5" style="font-size:0.72rem;">Sangat Kurang</span> &le; 25%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size:0.72rem;">Kurang</span> &gt; 25% - 75%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-info text-dark px-1.5 py-0.5" style="font-size:0.72rem;">Butuh Perbaikan</span> &gt; 75% - 90%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-primary px-1.5 py-0.5" style="font-size:0.72rem;">Baik</span> &gt; 90% - 100%</span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-nowrap"><span class="badge bg-success px-1.5 py-0.5" style="font-size:0.72rem;">Sangat Baik</span> &gt; 100% - 150%</span>
                        </div>
                    </div>

                    <!-- BAGIAN A: TARGET KINERJA RHK -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-3">
                        <h6 class="fw-bold text-dark section-header-title mb-0 small">
                            <i class="bi bi-list-task text-primary me-1.5"></i> A. Target Kinerja RHK
                        </h6>
                    </div>
                    
                    <?php if (session()->get('role') === 'direktur'): ?>
                    <form method="POST" action="<?= site_url('penilaian-kinerja/store') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="is_self_eval" value="1">
                        <input type="hidden" name="staf_id" value="<?= esc(session()->get('id') ?? session()->get('user_id')) ?>">
                        <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                        <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <?php endif; ?>

                    <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 140px;" class="text-center">Target Bulanan</th>
                                    <th style="width: 140px;" class="text-center">Total Realisasi</th>
                                    <th style="width: 140px;" class="text-center">Selisih (Gap)</th>
                                    <th class="col-nilai text-center" style="width: 160px;">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                    <?php 
                                        $target = round((float)$row['target_bulanan'], 4);
                                        $realisasi = round((float)$row['total_realisasi'], 4);
                                        $selisih = round($realisasi - $target, 4);
                                        $isTerbit = (isset($row['status_penilaian']) && $row['status_penilaian'] === 'terbit' && $row['nilai_capaian'] !== null);
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                        <td class="text-center fw-bold text-dark num-tabular"><?= str_replace('.', ',', (float)$target) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold text-primary num-tabular"><?= str_replace('.', ',', (float)$realisasi) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold num-tabular">
                                            <?php if ($selisih > 0): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php elseif ($selisih < 0): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle fw-bold fs-6 text-primary num-tabular">
                                            <?php if (session()->get('role') === 'direktur'): ?>
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="0" max="150" name="nilai_capaian[]" class="form-control text-center text-primary fw-bold num-tabular input-nilai-capaian" value="<?= isset($row['nilai_capaian']) && $row['nilai_capaian'] !== null ? (float)$row['nilai_capaian'] : '' ?>" placeholder="0 - 150" aria-label="Nilai Capaian Mandiri RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            <?php else: ?>
                                                <?= $isTerbit ? str_replace('.', ',', (float)$row['nilai_capaian']) . '%' : '<span class="text-muted fw-normal" title="Belum diterbitkan atasan">-</span>' ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- BAGIAN B: TUGAS TAMBAHAN -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-4">
                        <h6 class="fw-bold text-dark section-header-title mb-0 small">
                            <i class="bi bi-journal-plus text-primary me-1.5"></i> B. Tugas Tambahan
                        </h6>
                    </div>
                    
                    <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th class="text-start">Deskripsi Tugas Tambahan</th>
                                    <th style="width: 120px;" class="text-center">Tanggal</th>
                                    <th style="width: 140px;" class="text-center">Capaian / Output</th>
                                    <th style="width: 90px;" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tugas_tambahan_sendiri)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada tugas tambahan pada bulan ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($tugas_tambahan_sendiri as $idx => $tmb): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                            <td><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                            <td class="text-center text-muted num-tabular">
                                                <?php 
                                                    $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                    $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                    echo $tgl . ' ' . substr($bln, 0, 3);
                                                ?>
                                            </td>
                                             <td class="text-center fw-semibold text-dark num-tabular">
                                                 <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                             </td>
                                            <td class="text-center">
                                                <?php if (!empty($tmb['link_bukti'])): ?>
                                                    <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5 btn-tactile" style="font-size: 0.72rem;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if (!empty($tugas_tambahan_sendiri)): ?>
                            <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                <tr>
                                    <td colspan="4" class="text-end pe-3 align-middle text-muted fw-normal">Nilai Tugas Tambahan:</td>
                                    <td class="text-center align-middle fw-bold fs-6 text-success p-2 num-tabular">
                                        <?php if (session()->get('role') === 'direktur'): ?>
                                            <input type="hidden" name="log_tambahan_id[]" value="<?= esc($tugas_tambahan_sendiri[0]['id']) ?>">
                                            <div class="input-group input-group-sm justify-content-center" style="max-width: 130px; margin: 0 auto;">
                                                <input type="number" step="0.01" min="0" max="150" name="nilai_tugas_tambahan_gabungan" class="form-control text-center text-success fw-bold num-tabular input-nilai-capaian" value="<?= $scoreTambahanIndividu !== null ? (float)$scoreTambahanIndividu : '' ?>" placeholder="0 - 150" aria-label="Nilai Tugas Tambahan Mandiri">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        <?php else: ?>
                                            <?= $scoreTambahanIndividu !== null ? str_replace('.', ',', $scoreTambahanIndividu) . '%' : '<span class="text-muted fw-normal">-</span>' ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                    <?php if (session()->get('role') === 'direktur'): ?>
                    <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 fw-bold">
                            <i class="bi bi-pencil me-1"></i> Simpan Draf
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-send me-1"></i> Simpan & Terbitkan
                        </button>
                    </div>
                    </form>
                    <?php endif; ?>

                    <!-- RINGKASAN SKOR EXECUTIVE DI PALING BAWAH EVALUASI -->
                    <div class="card bg-white border border-2 border-<?= ($warnaScore === 'warning text-dark' || $warnaScore === 'info text-dark') ? ($warnaScore === 'warning text-dark' ? 'warning' : 'info') : $warnaScore ?> rounded-4 p-3 shadow-sm mb-4 score-card-transition bento-stagger bento-stagger-3" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Mandiri">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 score-banner-wrapper">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-award-fill text-primary me-2"></i> NILAI AKHIR KINERJA BULANAN</h6>
                                <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-list-task me-1 text-primary"></i> <?= count($rekap_data_sendiri) ?> Target RHK
                                    </span>
                                    <?php if (!empty($tugas_tambahan_sendiri)): ?>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-journal-plus me-1 text-success"></i> Tugas Tambahan: <?= $scoreTambahanIndividu !== null ? str_replace('.', ',', $scoreTambahanIndividu) . '%' : '<span class="text-muted">Belum Dinilai</span>' ?>
                                    </span>
                                    <?php endif; ?>
                                    <?php if ($jmlDinilai < (count($rekap_data_sendiri) + (!empty($tugas_tambahan_sendiri) ? 1 : 0))): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                        <i class="bi bi-info-circle me-1"></i> Sebagian nilai belum diterbitkan atasan
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i> Seluruh komponen telah dinilai
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                <span class="badge bg-<?= $badgeColorRataIndividu ?> fs-6 px-3 py-2 rounded-pill"><?= $predikatRataIndividu ?></span>
                                <span class="fs-2 fw-bold text-<?= $warnaScore ?> mb-0 lh-1 num-tabular" style="white-space: nowrap;"><?= str_replace('.', ',', round($rataRataIndividu, 2)) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN C: LOG KEGIATAN HARIAN -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-4">
                        <h6 class="fw-bold text-dark section-header-title mb-0 small">
                            <i class="bi bi-calendar-check text-primary me-1.5"></i> C. Log Kegiatan Harian
                        </h6>
                    </div>
                    
                    <?php 
                        $groupedSendiriByDate = [];
                        $hasAnyDraftSendiri = false;
                        if (!empty($log_harian_sendiri)) {
                            foreach ($log_harian_sendiri as $item) {
                                $tglKey = $item['tanggal_kegiatan'];
                                if (!isset($groupedSendiriByDate[$tglKey])) {
                                    $groupedSendiriByDate[$tglKey] = [
                                        'tanggal' => $tglKey,
                                        'items' => [],
                                        'has_draft' => false
                                    ];
                                }
                                $groupedSendiriByDate[$tglKey]['items'][] = $item;
                                if (isset($item['status']) && $item['status'] === 'draft') {
                                    $groupedSendiriByDate[$tglKey]['has_draft'] = true;
                                    $hasAnyDraftSendiri = true;
                                }
                            }
                        }
                    ?>

                    <?php if ($hasAnyDraftSendiri): ?>
                        <div class="alert alert-warning py-2.5 px-3 border-warning shadow-sm mb-3 d-flex flex-column flex-md-row align-items-stretch align-items-md-center justify-content-between gap-2 rounded-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill me-2 text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <strong>Perhatian:</strong> Anda memiliki laporan harian yang <strong>masih berupa Draf (Belum Dikirim)</strong> pada bulan ini. Laporan draf tidak dihitung dalam realisasi kinerja sampai Anda mengirimkannya.
                                </div>
                            </div>
                            <a href="<?= site_url('log-kegiatan') ?>" class="btn btn-sm btn-warning btn-tactile text-dark fw-bold ms-md-3 flex-shrink-0 rounded-pill px-3">
                                <i class="bi bi-send-fill me-1"></i> Buka & Kirim Laporan
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="scrollable-table mb-4 bg-white border rounded-4 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th style="width: 110px;" class="text-center">Tanggal</th>
                                    <th style="width: 105px;" class="text-center">Jenis</th>
                                    <th>Aktivitas Harian</th>
                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 110px;" class="text-center">Realisasi</th>
                                    <th style="width: 70px;" class="text-center">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($groupedSendiriByDate)): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada laporan harian pada bulan ini.</td></tr>
                                <?php else: ?>
                                    <?php 
                                        $noRow = 1;
                                        $groupIndex = 0;
                                        $hariIndoArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                        foreach ($groupedSendiriByDate as $tglKey => $group): 
                                            $groupIndex++;
                                            $groupClass = ($groupIndex % 2 === 0) ? 'date-group-even' : 'date-group-odd';
                                            $hariName = $hariIndoArr[date('w', strtotime($tglKey))];
                                            $tglFormatted = $hariName . ', ' . date('j', strtotime($tglKey)) . ' ' . substr($bulan_indo[date('n', strtotime($tglKey)) - 1], 0, 3);
                                            $rowSpan = count($group['items']);
                                            $hasDraft = $group['has_draft'];
                                            
                                            foreach ($group['items'] as $itemIdx => $it):
                                                $isTambahan = !empty($it['is_tambahan']);
                                    ?>
                                        <tr class="align-middle <?= $groupClass ?>">
                                            <?php if ($itemIdx === 0): ?>
                                                <td class="text-center fw-bold text-muted align-middle" rowspan="<?= $rowSpan ?>"><?= $noRow++ ?></td>
                                                <td class="text-center align-middle" rowspan="<?= $rowSpan ?>">
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold text-wrap mb-1 shadow-sm" style="font-size:0.8rem; line-height: 1.3;">
                                                        <i class="bi bi-calendar-event me-1"></i> <?= $tglFormatted ?>
                                                    </span>
                                                    <?php if ($hasDraft): ?>
                                                        <br><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.65rem;" title="Laporan pada tanggal ini masih draf (belum dikirim)"><i class="bi bi-pencil-fill me-1"></i> Draf</span>
                                                    <?php else: ?>
                                                        <br><span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;"><i class="bi bi-check-circle-fill me-1"></i> Terkirim</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            
                                            <td class="text-center">
                                                <?php if ($isTambahan): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Tambahan</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Utama</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td><?= nl2br(esc($it['deskripsi_kegiatan'])) ?></td>
                                            
                                            <td>
                                                <?php if ($isTambahan): ?>
                                                    <small class="text-muted">Tugas Tambahan</small>
                                                <?php else: ?>
                                                    <small class="text-muted"><?= esc($it['indikator_kinerja']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="text-center fw-bold text-primary num-tabular">
                                                <?= str_replace('.', ',', (float)$it['jumlah_capaian']) ?> <small class="fw-normal text-muted"><?= esc($it['satuan']) ?></small>
                                            </td>
                                            
                                            <td class="text-center">
                                                <?php if (!empty($it['link_bukti'])): ?>
                                                    <a href="<?= esc($it['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5 btn-tactile" style="font-size: 0.72rem;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div> <!-- End Tab Individu -->

            <?php if ($is_atasan): ?>
            <!-- ==================== TAB 2: PENILAIAN STAF (KHUSUS ATASAN) ==================== -->
            <div class="tab-pane fade <?= !empty($staf_id_terpilih) ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">
                
                <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-4 border border-light-subtle" id="formPilihStaf">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">
                    
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success mb-1 small" style="font-size: 0.75rem; letter-spacing: 0.3px;"><i class="bi bi-person-check me-1"></i> Pilih Staf yang Akan Dinilai</label>
                            <select name="staf_id" id="selectStaf" class="form-select border-success form-select-sm" aria-label="Pilih Staf yang Akan Dinilai" onchange="this.form.submit()">
                                <option value="">-- Pilih Staf --</option>
                                <?php foreach ($daftar_staf as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= ($b['id'] == $staf_id_terpilih) ? 'selected' : '' ?>>
                                        <?= esc($b['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <?php if (!empty($staf_id_terpilih)): ?>
                    <?php if (empty($rekap_data_staf)): ?>
                        <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                            <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">Target Kinerja Staf Belum Ditetapkan</h6>
                            <p class="text-muted small mb-0">Staf belum memiliki Target Kinerja (RHK) pada periode <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>.</p>
                        </div>
                    <?php else: ?>
                        <?php
                            $jmlDinilaiBwh = 0;
                            $totalNilaiBwh = 0;
                            foreach ($rekap_data_staf as $rd) {
                                if ($rd['nilai_capaian'] !== null && $rd['nilai_capaian'] !== '') {
                                    $jmlDinilaiBwh++;
                                    $totalNilaiBwh += (float)$rd['nilai_capaian'];
                                }
                            }

                            // Perhitungan tugas tambahan gabungan jika dinilai
                            $scoreTambahanStaf = null;
                            if (!empty($tugas_tambahan_staf)) {
                                foreach ($tugas_tambahan_staf as $tmb) {
                                    if ($tmb['nilai_capaian'] !== null) {
                                        $scoreTambahanStaf = (float)$tmb['nilai_capaian'];
                                        break;
                                    }
                                }
                            }

                            if ($scoreTambahanStaf !== null) {
                                $totalNilaiBwh += $scoreTambahanStaf;
                                $jmlDinilaiBwh++;
                            }

                            $rataRataBwh = $jmlDinilaiBwh > 0 ? (float)($totalNilaiBwh / $jmlDinilaiBwh) : 0;
                            
                            $predikatRataRataBwh = '-';
                            $badgeColorRataBwh = 'secondary';
                            $warnaScoreBwh = 'success';
                            if ($jmlDinilaiBwh == 0) {
                                $warnaScoreBwh = 'secondary';
                            } else {
                                if ($rataRataBwh <= 25) { $warnaScoreBwh = 'danger'; $predikatRataRataBwh = 'Sangat Kurang'; $badgeColorRataBwh = 'danger'; }
                                elseif ($rataRataBwh <= 75) { $warnaScoreBwh = 'warning text-dark'; $predikatRataRataBwh = 'Kurang'; $badgeColorRataBwh = 'warning text-dark'; }
                                elseif ($rataRataBwh <= 90) { $warnaScoreBwh = 'info text-dark'; $predikatRataRataBwh = 'Butuh Perbaikan'; $badgeColorRataBwh = 'info text-dark'; }
                                elseif ($rataRataBwh <= 100) { $warnaScoreBwh = 'primary'; $predikatRataRataBwh = 'Baik'; $badgeColorRataBwh = 'primary'; }
                                else { $warnaScoreBwh = 'success'; $predikatRataRataBwh = 'Sangat Baik'; $badgeColorRataBwh = 'success'; }
                            }
                        ?>

                        <!-- GUIDANCE & RULES BANNER -->
                        <div class="alert alert-light border border-info-subtle shadow-sm mb-3 py-2 px-3 text-dark rounded-3" style="font-size: 0.76rem;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                <span class="fw-bold text-secondary text-nowrap"><i class="bi bi-journal-check text-success me-1"></i> Standar Predikat:</span>
                                <span class="text-nowrap"><span class="badge bg-danger px-1.5 py-0.5" style="font-size:0.72rem;">Sangat Kurang</span> &le; 25%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size:0.72rem;">Kurang</span> &gt; 25% - 75%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-info text-dark px-1.5 py-0.5" style="font-size:0.72rem;">Butuh Perbaikan</span> &gt; 75% - 90%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-primary px-1.5 py-0.5" style="font-size:0.72rem;">Baik</span> &gt; 90% - 100%</span>
                                <span class="text-muted opacity-50">|</span>
                                <span class="text-nowrap"><span class="badge bg-success px-1.5 py-0.5" style="font-size:0.72rem;">Sangat Baik</span> &gt; 100% - 150%</span>
                            </div>
                        </div>

                        <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" id="penilaianActionInput" value="submit">
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                            <!-- BAGIAN A STAF: PENILAIAN TARGET RHK -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-3">
                                <h6 class="fw-bold text-dark section-header-title mb-0 small">
                                    <i class="bi bi-list-task text-success me-1.5"></i> A. Penilaian Target RHK
                                </h6>
                                <?php if (hasRole('admin') && !empty($staf_id_terpilih)): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm btn-batal-approve-target-penilaian"
                                    data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                    data-bulan="<?= esc($bulan_terpilih) ?>"
                                    data-tahun="<?= esc($tahun_terpilih) ?>"
                                    title="Batalkan persetujuan target bulanan staf agar staf dapat merevisi target">
                                    <i class="bi bi-x-circle-fill me-1"></i> Batalkan Target (Admin)
                                </button>
                                <?php endif; ?>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0 table-bento" id="tablePenilaianStaf">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                            <th style="width: 130px;" class="text-center">Target Bulanan</th>
                                            <th style="width: 130px;" class="text-center">Total Realisasi</th>
                                            <th style="width: 130px;" class="text-center">Selisih (Gap)</th>
                                            <th class="col-nilai text-center">Input Nilai Capaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php 
                                                $target = round((float)$row['target_bulanan'], 4);
                                                $realisasi = round((float)$row['total_realisasi'], 4);
                                                $selisih = round($realisasi - $target, 4);
                                                
                                                $nilai_capaian = $row['nilai_capaian'];
                                                $predikatLabel = '-';
                                                $predikatBadge = 'bg-light text-muted border border-secondary-subtle';
                                                if ($nilai_capaian !== null && $nilai_capaian !== '') {
                                                    $n = (float)$nilai_capaian;
                                                    if ($n <= 25) { $predikatLabel = 'Sangat Kurang'; $predikatBadge = 'bg-danger'; }
                                                    elseif ($n <= 75) { $predikatLabel = 'Kurang'; $predikatBadge = 'bg-warning text-dark'; }
                                                    elseif ($n <= 90) { $predikatLabel = 'Butuh Perbaikan'; $predikatBadge = 'bg-info text-dark'; }
                                                    elseif ($n <= 100) { $predikatLabel = 'Baik'; $predikatBadge = 'bg-primary'; }
                                                    else { $predikatLabel = 'Sangat Baik'; $predikatBadge = 'bg-success'; }
                                                } else {
                                                    $predikatLabel = '<i class="bi bi-dash-circle me-1"></i> Belum dinilai';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                                <td class="text-center fw-bold text-dark num-tabular"><?= str_replace('.', ',', (float)$target) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold text-primary num-tabular"><?= str_replace('.', ',', (float)$realisasi) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold num-tabular">
                                                    <?php if ($selisih > 0): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php elseif ($selisih < 0): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', (float)$selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle p-2 text-center col-nilai">
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                    <div class="input-group input-group-sm mb-1 shadow-sm rounded-3 border border-primary-subtle" style="width: 100%; min-width: 145px;">
                                                        <input type="number" name="nilai_capaian[]" class="form-control text-center input-nilai-capaian fw-bold text-primary px-2 num-tabular" style="font-size:0.95rem; min-width: 90px;" value="<?= $nilai_capaian !== null ? (float)$nilai_capaian : '' ?>" step="0.01" min="0" max="150" placeholder="0 - 150" aria-label="Input Nilai Capaian RHK: <?= esc($row['indikator_kinerja']) ?>">
                                                        <span class="input-group-text bg-primary-subtle text-primary fw-bold px-2">%</span>
                                                    </div>
                                                    <div class="predikat-badge-container">
                                                        <span class="badge <?= $predikatBadge ?> rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><?= $predikatLabel ?></span>
                                                    </div>
                                                    <div class="invalid-feedback" style="font-size: 0.7rem;">Nilai tidak sesuai!</div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- BAGIAN B STAF: PENILAIAN TUGAS TAMBAHAN -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-4">
                                <h6 class="fw-bold text-dark section-header-title mb-0 small">
                                    <i class="bi bi-journal-plus text-success me-1.5"></i> B. Penilaian Tugas Tambahan
                                </h6>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-4 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="text-start">Deskripsi Tugas Tambahan</th>
                                            <th style="width: 120px;" class="text-center">Tanggal</th>
                                            <th style="width: 140px;" class="text-center">Capaian / Output</th>
                                            <th style="width: 90px;" class="text-center">Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($tugas_tambahan_staf)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-3">Staf ini tidak memiliki tugas tambahan bulan ini.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($tugas_tambahan_staf as $idx => $tmb): ?>
                                                <input type="hidden" name="log_tambahan_id[]" value="<?= $tmb['id'] ?>">
                                                <tr>
                                                    <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                                    <td><?= nl2br(esc($tmb['deskripsi_kegiatan'])) ?></td>
                                                    <td class="text-center text-muted num-tabular">
                                                        <?php 
                                                            $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                            $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                            echo $tgl . ' ' . substr($bln, 0, 3);
                                                        ?>
                                                    </td>
                                                     <td class="text-center fw-semibold text-dark num-tabular">
                                                         <?= (isset($tmb['jumlah_capaian']) && $tmb['jumlah_capaian'] !== null && $tmb['jumlah_capaian'] !== '') ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                                     </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($tmb['link_bukti'])): ?>
                                                            <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5" style="font-size: 0.72rem;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php if (!empty($tugas_tambahan_staf)): ?>
                                    <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="4" class="text-end pe-3 align-middle text-dark fw-bold" style="font-size: 0.82rem;">
                                                <i class="bi bi-journal-check text-success me-1"></i> Nilai Tugas Tambahan (0 - 150%):
                                            </td>
                                            <td class="p-2 align-middle text-center col-nilai">
                                                <div class="input-group input-group-sm shadow-sm rounded-3 border border-success-subtle" style="width: 100%; min-width: 145px;">
                                                    <input type="number" step="0.01" max="150" min="0" name="nilai_tugas_tambahan_gabungan" id="inputNilaiTambahanGabungan" class="form-control text-center fw-bold text-success px-2 num-tabular input-nilai-capaian" style="font-size:0.95rem; min-width: 90px;" value="<?= $scoreTambahanStaf !== null ? $scoreTambahanStaf : '' ?>" placeholder="0 - 150" aria-label="Input Nilai Akumulasi Tugas Tambahan Staf" <?= !$is_penilai ? 'readonly' : '' ?>>
                                                    <span class="input-group-text bg-success-subtle text-success fw-bold px-2">%</span>
                                                </div>
                                                <div id="hintTambahanContainer">
                                                    <?php if ($scoreTambahanStaf === null): ?>
                                                        <span class="badge bg-light text-muted border border-secondary-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-check-circle me-1"></i> Terisi</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>

                            <!-- RINGKASAN EXECUTIVE SKOR AKHIR DI PALING BAWAH PENILAIAN -->
                            <div class="card bg-white border border-2 border-<?= ($warnaScoreBwh === 'warning text-dark' || $warnaScoreBwh === 'info text-dark') ? ($warnaScoreBwh === 'warning text-dark' ? 'warning' : 'info') : $warnaScoreBwh ?> rounded-4 p-3 shadow-sm mb-4" id="cardKinerjaStafSummary" role="region" aria-label="Ringkasan Nilai Akhir Kinerja Staf">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 score-banner-wrapper">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-award-fill text-success me-2"></i> NILAI AKHIR KINERJA STAF</h6>
                                        <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-list-task me-1 text-primary"></i> <span id="countRhkTerisi"><?= $jmlDinilaiBwh - ($scoreTambahanStaf !== null ? 1 : 0) ?></span> / <?= count($rekap_data_staf) ?> RHK Terisi
                                            </span>
                                            <?php if (!empty($tugas_tambahan_staf)): ?>
                                            <span class="badge bg-light text-dark border" id="badgeTambahanStatus">
                                                <i class="bi bi-journal-plus me-1 text-success"></i> Tugas Tambahan: <span id="textTambahanStatus"><?= $scoreTambahanStaf !== null ? (float)$scoreTambahanStaf . '%' : 'Belum Dinilai' ?></span>
                                            </span>
                                            <?php endif; ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-none" id="badgeHintUnfilled">
                                                <i class="bi bi-exclamation-circle me-1"></i> <span id="textCountUnfilled">0</span> kolom belum diisi
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle d-none" id="badgeAllFilled">
                                                <i class="bi bi-check-circle-fill me-1"></i> Semua nilai terisi
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3" aria-live="polite" aria-atomic="true">
                                        <span id="totalPredikatStafBadge" class="badge bg-<?= $badgeColorRataBwh ?> fs-6 px-3 py-2 rounded-pill"><?= $predikatRataRataBwh ?></span>
                                        <span class="fs-2 fw-bold text-<?= $warnaScoreBwh ?> mb-0 lh-1 num-tabular" id="totalKinerjaStafText" style="white-space: nowrap;"><?= str_replace('.', ',', round($rataRataBwh, 2)) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION TOOLBAR AT BOTTOM OF STAF FORM -->
                            <div class="d-flex justify-content-end mb-4 gap-2 btn-action-container bento-stagger bento-stagger-3">
                                <button type="reset" class="btn btn-outline-secondary btn-tactile rounded-pill px-3 py-2 fw-semibold shadow-sm" title="Kosongkan seluruh isian nilai pada halaman ini" aria-label="Kosongkan seluruh isian nilai pada halaman ini"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset Nilai</button>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm" title="Simpan sebagai draf" aria-label="Simpan draf penilaian kinerja staf"><i class="bi bi-journal-bookmark me-1"></i> Simpan Draf</button>
                                <button type="submit" name="action" value="submit" class="btn btn-success btn-tactile rounded-pill px-4 py-2 fw-bold shadow-sm" title="Simpan dan terbitkan nilai kinerja" aria-label="Simpan dan terbitkan nilai kinerja staf"><i class="bi bi-check-circle-fill me-1.5"></i> Simpan & Terbitkan Penilaian</button>
                            </div>
                        </form>

                        <!-- BAGIAN C STAF: LOG KEGIATAN STAF -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2 mt-4">
                            <h6 class="fw-bold text-dark section-header-title mb-0 small">
                                <i class="bi bi-calendar-check text-success me-1.5"></i> C. Log Kegiatan Staf
                            </h6>
                            <?php if ((hasRole('admin') || $is_atasan || $is_penilai) && !empty($staf_id_terpilih)): ?>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Klik <span class="badge bg-warning-subtle text-dark border me-1"><i class="bi bi-pencil-square"></i> Revisi</span> untuk mengizinkan staf memperbarui laporan pada tanggal tersebut.</small>
                            <?php endif; ?>
                        </div>

                        <div class="scrollable-table mb-4 bg-white border rounded-4 shadow-sm">
                            <table class="table table-bordered table-hover align-middle mb-0 table-bento">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th style="width: 105px;" class="text-center">Tanggal</th>
                                        <th style="width: 105px;" class="text-center">Jenis</th>
                                        <th>Aktivitas Harian</th>
                                        <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                        <th style="width: 110px;" class="text-center">Realisasi</th>
                                        <th style="width: 70px;" class="text-center">Bukti</th>
                                        <?php if (hasRole('admin') || $is_atasan || $is_penilai): ?>
                                        <th style="width: 75px;" class="text-center">Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $groupedLogsByDate = [];
                                        if (!empty($log_harian_staf)) {
                                             foreach ($log_harian_staf as $item) {
                                                $tglKey = $item['tanggal_kegiatan'];
                                                if (!isset($groupedLogsByDate[$tglKey])) {
                                                    $groupedLogsByDate[$tglKey] = [
                                                        'tanggal' => $tglKey,
                                                        'items' => [],
                                                        'has_terkirim' => false
                                                    ];
                                                }
                                                $groupedLogsByDate[$tglKey]['items'][] = $item;
                                                if (isset($item['status']) && $item['status'] === 'terkirim') {
                                                    $groupedLogsByDate[$tglKey]['has_terkirim'] = true;
                                                }
                                            }
                                        }
                                    ?>
                                    <?php if(empty($groupedLogsByDate)): ?>
                                        <tr><td colspan="<?= (hasRole('admin') || $is_atasan || $is_penilai) ? 8 : 7 ?>" class="text-center text-muted py-3">Belum ada laporan harian dari staf ini.</td></tr>
                                    <?php else: ?>
                                        <?php 
                                            $noRow = 1;
                                            $groupIndexStaf = 0;
                                            $hariIndoArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                            foreach ($groupedLogsByDate as $tglKey => $group): 
                                                $groupIndexStaf++;
                                                $groupClass = ($groupIndexStaf % 2 === 0) ? 'date-group-even' : 'date-group-odd';
                                                $hariName = $hariIndoArr[date('w', strtotime($tglKey))];
                                                $tglFormatted = $hariName . ', ' . date('j', strtotime($tglKey)) . ' ' . substr($bulan_indo[date('n', strtotime($tglKey)) - 1], 0, 3);
                                                $rowSpan = count($group['items']);
                                                $hasTerkirim = $group['has_terkirim'];
                                                
                                                foreach ($group['items'] as $itemIdx => $it):
                                                    $isTambahan = !empty($it['is_tambahan']);
                                        ?>
                                            <tr class="align-middle <?= $groupClass ?>">
                                                <?php if ($itemIdx === 0): ?>
                                                    <td class="text-center fw-bold text-muted align-middle" rowspan="<?= $rowSpan ?>"><?= $noRow++ ?></td>
                                                    <td class="text-center align-middle" rowspan="<?= $rowSpan ?>">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-bold text-wrap mb-1 shadow-sm" style="font-size:0.8rem; line-height: 1.3;">
                                                            <i class="bi bi-calendar-event me-1"></i> <?= $tglFormatted ?>
                                                        </span>
                                                    </td>
                                                <?php endif; ?>
                                                
                                                <td class="text-center">
                                                    <?php if ($isTambahan): ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Tambahan</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Utama</span>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <td><?= nl2br(esc($it['deskripsi_kegiatan'])) ?></td>
                                                
                                                <td>
                                                    <?php if ($isTambahan): ?>
                                                        <small class="text-muted">Tugas Tambahan</small>
                                                    <?php else: ?>
                                                        <small class="text-muted"><?= esc($it['indikator_kinerja']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <td class="text-center fw-bold text-primary num-tabular">
                                                    <?= str_replace('.', ',', (float)$it['jumlah_capaian']) ?> <small class="fw-normal text-muted"><?= esc($it['satuan']) ?></small>
                                                </td>
                                                
                                                <td class="text-center">
                                                    <?php if (!empty($it['link_bukti'])): ?>
                                                        <a href="<?= esc($it['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-pill border px-2 py-0.5" style="font-size: 0.72rem;" title="Lihat Bukti Pekerjaan"><i class="bi bi-box-arrow-up-right me-1"></i>Bukti</a>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <?php if ((hasRole('admin') || $is_atasan || $is_penilai) && $itemIdx === 0): ?>
                                                    <td class="text-center align-middle" rowspan="<?= $rowSpan ?>">
                                                        <?php if ($hasTerkirim): ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning text-dark border-warning-subtle fw-semibold px-2 py-1 btn-buka-kunci-penilaian shadow-sm"
                                                            style="font-size:0.75rem;"
                                                            data-tanggal="<?= esc($tglKey) ?>"
                                                            data-staf-id="<?= esc($staf_id_terpilih) ?>"
                                                            title="Izinkan revisi laporan harian tanggal <?= esc(date('d M Y', strtotime($tglKey))) ?>">
                                                            <i class="bi bi-pencil-square me-1"></i> Revisi
                                                        </button>
                                                        <?php else: ?>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.65rem;">
                                                                <i class="bi bi-pencil me-1"></i> Mode Revisi
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card bg-light border-0 rounded-4 p-5 text-center my-3">
                        <i class="bi bi-person-lines-fill fs-1 text-primary opacity-50 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Pilih Staf untuk Memulai Penilaian</h6>
                        <p class="text-muted small mb-0">Silakan pilih nama staf dari menu dropdown di atas untuk melihat target dan memberikan penilaian kinerja.</p>
                    </div>
                <?php endif; ?>

            </div> <!-- End Tab Staf -->
            <?php endif; ?>

        </div>
    </div>
</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const bulanTerpilih = <?= esc($bulan_terpilih) ?>;
    const tahunTerpilih = <?= esc($tahun_terpilih) ?>;

    $(document).ready(function() {
        if ($('#selectStaf').length) {
            $('#selectStaf').select2({ 
                width: '100%', 
                placeholder: "Cari Nama Staf...",
                allowClear: false
            });
            $('#selectStaf').on('select2:select', function (e) { $(this).closest('form').submit(); });
            
            $('#selectStaf').on('select2:open', function () {
                setTimeout(function() {
                    document.querySelector('.select2-search__field').focus();
                }, 50);
            });
        }

        // Cegah perubahan nilai angka secara tidak sengaja saat pengguna scrolling halaman dengan mouse wheel
        $(document).on('wheel', 'input[type="number"]', function (e) {
            $(this).blur();
        });

        function getPredikatInfo(val) {
            if (isNaN(val) || val === '' || val === null) {
                return { label: '-', class: 'bg-light text-muted border', textClass: 'secondary' };
            }
            if (val <= 25) return { label: 'Sangat Kurang', class: 'bg-danger', textClass: 'danger' };
            if (val <= 75) return { label: 'Kurang', class: 'bg-warning text-dark', textClass: 'warning' };
            if (val <= 90) return { label: 'Butuh Perbaikan', class: 'bg-info text-dark', textClass: 'info' };
            if (val <= 100) return { label: 'Baik', class: 'bg-primary', textClass: 'primary' };
            return { label: 'Sangat Baik', class: 'bg-success', textClass: 'success' };
        }

        function calculateOverallStafScore() {
            let total = 0;
            let count = 0;
            let rhkInputs = $('#tablePenilaianStaf tbody .input-nilai-capaian');
            let rhkTotal = rhkInputs.length;
            let rhkFilled = 0;

            // 1. Hitung Nilai RHK (hanya dari baris tbody)
            rhkInputs.each(function() {
                let rawVal = $(this).val();
                let v = parseFloat(rawVal);
                let badgeContainer = $(this).closest('td').find('.predikat-badge-container');

                if (rawVal !== '' && !isNaN(v)) {
                    if (!$(this).hasClass('is-invalid')) {
                        total += v;
                        count++;
                        rhkFilled++;
                    }
                    let p = getPredikatInfo(v);
                    badgeContainer.html('<span class="badge ' + p.class + ' rounded-pill px-2 py-0.5 badge-predikat-pop" style="font-size:0.72rem;">' + p.label + '</span>');
                    $(this).removeClass('border-warning shadow-sm');
                } else {
                    badgeContainer.html('<span class="badge bg-light text-muted border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                }
            });

            // 2. Hitung Nilai Akumulasi Tugas Tambahan (jika diisi)
            let hasTambahan = $('#inputNilaiTambahanGabungan').length > 0;
            let tambahanFilled = false;
            let rawTambahan = $('#inputNilaiTambahanGabungan').val();
            let vTambahan = parseFloat(rawTambahan);

            if (hasTambahan) {
                let hintContainer = $('#hintTambahanContainer');
                if (rawTambahan !== '' && !isNaN(vTambahan) && vTambahan >= 0 && vTambahan <= 150) {
                    total += vTambahan;
                    count++;
                    tambahanFilled = true;
                    $('#textTambahanStatus').text(vTambahan + '%');
                    if (hintContainer.length) {
                        hintContainer.html('<span class="badge bg-success-subtle text-success border border-success-subtle mt-1 rounded-pill px-2 py-0.5 badge-predikat-pop" style="font-size:0.72rem;"><i class="bi bi-check-circle me-1"></i> Terisi</span>');
                    }
                    $('#inputNilaiTambahanGabungan').removeClass('border-warning shadow-sm');
                } else {
                    $('#textTambahanStatus').text('Belum Dinilai');
                    if (hintContainer.length) {
                        hintContainer.html('<span class="badge bg-light text-muted border border-secondary-subtle mt-1 rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                    }
                }
            }

            // 3. Update counter & unfilled hints
            $('#countRhkTerisi').text(rhkFilled);
            
            let totalComponents = rhkTotal + (hasTambahan ? 1 : 0);
            let totalFilled = rhkFilled + (hasTambahan && tambahanFilled ? 1 : 0);
            let totalUnfilled = totalComponents - totalFilled;

            if (totalUnfilled > 0) {
                $('#textCountUnfilled').text(totalUnfilled);
                $('#badgeHintUnfilled').removeClass('d-none');
                $('#badgeAllFilled').addClass('d-none');
            } else {
                $('#badgeHintUnfilled').addClass('d-none');
                $('#badgeAllFilled').removeClass('d-none');
            }

            // 4. Kalkulasi Nilai Akhir
            let avg = count > 0 ? (Math.round((total / count) * 100) / 100) : 0;
            $('#totalKinerjaStafText').text(avg.toString().replace('.', ','));

            let pRata = getPredikatInfo(count > 0 ? avg : null);
            
            let textEl = $('#totalKinerjaStafText');
            let wrapper = $('#cardKinerjaStafSummary');
            let badgeEl = $('#totalPredikatStafBadge');

            textEl.removeClass('text-success text-secondary text-danger text-warning text-primary text-info');
            wrapper.removeClass('border-success border-secondary border-danger border-warning border-primary border-info');

            textEl.addClass('text-' + pRata.textClass);
            wrapper.addClass('border-' + pRata.textClass);

            badgeEl.attr('class', 'badge ' + pRata.class + ' fs-6 px-3 py-2 rounded-pill badge-predikat-pop').text(pRata.label);

            return { totalUnfilled, count, totalFilled, totalComponents };
        }

        // Jalankan inisialisasi perhitungan saat halaman selesai dimuat
        calculateOverallStafScore();

        // Auto-calculate Rata-rata Kinerja Staf & Update Predikat Per-Baris secara Real-time
        $(document).on('input change keyup', '.input-nilai-capaian, #inputNilaiTambahanGabungan', function() {
            if ($(this).hasClass('input-nilai-capaian')) {
                var val = parseFloat($(this).val());
                var error = $(this).parent().siblings('.invalid-feedback');
                var btnSubmit = $('#formPenilaian button[type="submit"]');

                if (!isNaN(val) && (val < 0 || val > 150)) {
                    $(this).addClass('is-invalid');
                    error.show();
                    btnSubmit.prop('disabled', true);
                } else {
                    $(this).removeClass('is-invalid');
                    error.hide();
                    if ($('.input-nilai-capaian.is-invalid').length === 0) {
                        btnSubmit.prop('disabled', false);
                    }
                }
            }

            calculateOverallStafScore();
        });

        // Handle Kosongkan form (Reset)
        $('#formPenilaian button[type="reset"]').on('click', function(e) {
            e.preventDefault(); 
            
            Swal.fire({
                title: 'Kosongkan Seluruh Isian?',
                text: "Seluruh isian angka penilaian di layar ini akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Ya, Kosongkan Nilai',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.input-nilai-capaian, #inputNilaiTambahanGabungan').val('').removeClass('is-invalid border-warning shadow-sm').removeAttr('title');
                    $('.invalid-feedback').hide();
                    $('.predikat-badge-container').html('<span class="badge bg-light text-muted border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size:0.72rem;"><i class="bi bi-dash-circle me-1"></i> Belum dinilai</span>');
                    $('#formPenilaian button[type="submit"]').prop('disabled', false);
                    
                    calculateOverallStafScore();
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Form berhasil dikosongkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Hardening: Form Submission Validation, Unfilled Component Hints & Action Sync
        let isPublishConfirmed = false;
        let lastClickedAction = 'submit';

        $('#formPenilaian button[type="submit"]').on('click', function() {
            lastClickedAction = $(this).val() || 'submit';
            $('#penilaianActionInput').val(lastClickedAction);
        });

        $('#formPenilaian').on('submit', function(e) {
            let form = $(this);
            let hasInvalid = false;

            // Make sure the hidden input is always set before any button state changes
            $('#penilaianActionInput').val(lastClickedAction);

            form.find('.input-nilai-capaian').each(function() {
                let v = parseFloat($(this).val());
                if (!isNaN(v) && (v < 0 || v > 150)) {
                    hasInvalid = true;
                    $(this).addClass('is-invalid');
                }
            });

            let vTambahan = parseFloat($('#inputNilaiTambahanGabungan').val());
            if (!isNaN(vTambahan) && (vTambahan < 0 || vTambahan > 150)) {
                hasInvalid = true;
                $('#inputNilaiTambahanGabungan').addClass('is-invalid');
            }

            if (hasInvalid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Nilai Tidak Sesuai',
                    text: 'Terdapat nilai capaian yang di luar batas yang diperbolehkan (0 - 150%).'
                });
                return false;
            }

            let stats = calculateOverallStafScore();

            // Jika tombol yang ditekan adalah Simpan & Terbitkan Penilaian dan masih ada kolom yang kosong
            if (lastClickedAction === 'submit' && stats.totalUnfilled > 0 && !isPublishConfirmed) {
                e.preventDefault();
                Swal.fire({
                    title: 'Ada Nilai Belum Diisi',
                    html: `Terdapat <strong>${stats.totalUnfilled} komponen penilaian</strong> yang belum diisi nilainya.<br><br>` +
                          `Jika Anda tetap melanjutkan penerbitan, seluruh komponen yang kosong akan <strong>diberi nilai 0 (Nol)</strong> dan dihitung ke dalam nilai akhir kinerja staf.<br><br>` +
                          `Apakah Anda ingin tetap menerbitkan penilaian?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-check-circle-fill me-1"></i> Ya, Tetap Terbitkan (Nilai 0)',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        isPublishConfirmed = true;

                        // Otomatis isi seluruh input RHK yang kosong dengan '0'
                        $('#tablePenilaianStaf tbody .input-nilai-capaian').each(function() {
                            if ($(this).val() === '') {
                                $(this).val('0');
                            }
                        });

                        // Otomatis isi input tugas tambahan jika ada dan kosong dengan '0'
                        if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').val('0');
                        }

                        // Hitung ulang kalkulasi di layar dengan nilai 0
                        calculateOverallStafScore();

                        $('#penilaianActionInput').val('submit');
                        form.find('button[type="submit"]').prop('disabled', true);
                        form.find('button[value="submit"]').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menerbitkan...');
                        form[0].submit();
                    } else {
                        // Highlight kolom yang masih kosong dengan border warning
                        $('.input-nilai-capaian').each(function() {
                            if ($(this).val() === '') {
                                $(this).addClass('border-warning shadow-sm');
                            }
                        });
                        if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').addClass('border-warning shadow-sm');
                        }
                        let firstEmpty = $('.input-nilai-capaian').filter(function() { return $(this).val() === ''; }).first();
                        if (firstEmpty.length) {
                            firstEmpty.focus();
                        } else if ($('#inputNilaiTambahanGabungan').length && $('#inputNilaiTambahanGabungan').val() === '') {
                            $('#inputNilaiTambahanGabungan').focus();
                        }
                    }
                });
                return false;
            }

            // Sync hidden action input before submission
            $('#penilaianActionInput').val(lastClickedAction);

            let submitBtns = form.find('button[type="submit"]');
            let activeBtn = form.find('button[value="' + lastClickedAction + '"]');
            if (activeBtn.length) {
                activeBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ' + (lastClickedAction === 'submit' ? 'Menerbitkan...' : 'Menyimpan...'));
            }
            // Use setTimeout to disable buttons so current submit event carries the form data normally
            setTimeout(() => {
                submitBtns.prop('disabled', true);
            }, 10);
        });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("aria-controls");
            $('#active_tab_input').val(target);
            if (history.pushState) {
                history.pushState(null, null, '#' + target);
            } else {
                location.hash = '#' + target;
            }
        });

        if (window.location.hash) {
            var targetTab = document.querySelector('button[aria-controls="' + window.location.hash.substring(1) + '"]');
            if (targetTab) {
                var tab = new bootstrap.Tab(targetTab);
                tab.show();
                $('#active_tab_input').val(window.location.hash.substring(1));
            }
        }
        // =============================================
        // [SUPERADMIN] Buka Kunci Laporan dari Penilaian Kinerja
        // =============================================
        $(document).on('click', '.btn-buka-kunci-penilaian', function(e) {
            e.preventDefault();
            const tanggal   = $(this).data('tanggal');
            const stafId    = $(this).data('staf-id');
            const csrfName  = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!tanggal || !stafId) {
                alert('Parameter tanggal atau staf ID tidak valid.');
                return;
            }

            function executeBukaKunciStaf() {
                $.ajax({
                    url: '<?= site_url('log-kegiatan/buka-kunci') ?>',
                    type: 'POST',
                    data: {
                        target_user_id: stafId,
                        tanggal: tanggal,
                        [csrfName]: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.csrf_hash) {
                            $('input[name="' + csrfName + '"]').val(response.csrf_hash);
                            $('input[name="csrf_test_name"]').val(response.csrf_hash);
                        }
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                            } else {
                                alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText || error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
                        } else {
                            alert('Terjadi kesalahan jaringan atau server.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Izinkan Revisi Laporan?',
                    html: `Laporan harian staf tanggal <strong>${tanggal}</strong> akan dibuka untuk direvisi.<br>Staf dapat memperbarui dan mengirim kembali laporan tersebut.<br><br><span class='text-dark fw-semibold'>Setelah dikirim ulang, laporan akan terkunci kembali otomatis.</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-pencil-square me-1"></i> Ya, Izinkan Revisi',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeBukaKunciStaf();
                    }
                });
            } else {
                if (confirm(`Izinkan Revisi Laporan harian staf tanggal ${tanggal}?\n\nStaf dapat memperbarui dan mengirim kembali laporan tersebut.`)) {
                    executeBukaKunciStaf();
                }
            }
        });

        // =============================================
        // [SUPERADMIN] Batalkan Persetujuan Target Bulanan Staf
        // =============================================
        $(document).on('click', '.btn-batal-approve-target-penilaian', function(e) {
            e.preventDefault();
            const stafId    = $(this).data('staf-id');
            const bulan     = $(this).data('bulan');
            const tahun     = $(this).data('tahun');
            const csrfName  = '<?= csrf_token() ?>';
            const csrfToken = $('input[name="' + csrfName + '"]').first().val() || $('input[name="csrf_test_name"]').val();

            if (!stafId || !bulan || !tahun) {
                alert('Parameter staf_id, bulan, atau tahun tidak valid.');
                return;
            }

            function executeCancel() {
                $.ajax({
                    url: '<?= site_url('laporan-harian/batal-approve') ?>',
                    type: 'POST',
                    data: {
                        staf_id: stafId,
                        bulan: bulan,
                        tahun: tahun,
                        [csrfName]: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.csrf_hash) {
                            $('input[name="' + csrfName + '"]').val(response.csrf_hash);
                            $('input[name="csrf_test_name"]').val(response.csrf_hash);
                        }
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                            } else {
                                alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText || error);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
                        } else {
                            alert('Terjadi kesalahan jaringan atau server.');
                        }
                    }
                });
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Batalkan Persetujuan Target?',
                    html: `Persetujuan target bulanan staf akan dibatalkan.<br>Staf akan dapat merevisi dan mengajukan kembali ke atasan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-x-circle-fill me-1"></i> Ya, Batalkan Persetujuan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeCancel();
                    }
                });
            } else {
                if (confirm('Batalkan Persetujuan Target?\n\nPersetujuan target bulanan staf akan dibatalkan agar staf dapat merevisi.')) {
                    executeCancel();
                }
            }
        });

    });
</script>
<?= $this->endSection() ?>

