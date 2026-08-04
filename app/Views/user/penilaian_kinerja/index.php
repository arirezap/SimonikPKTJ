<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Rekap & Penilaian Kinerja
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .table th {
        background-color: #f8f9fa !important;
        color: #333;
        vertical-align: middle;
        text-align: center;
        font-weight: 600;
    }
    .table td {
        vertical-align: middle;
    }
    .table {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .col-target {
        min-width: 260px;
    }
    .col-nilai {
        min-width: 125px;
        max-width: 150px;
    }
    .form-control-sm, .form-select-sm, .input-group-sm > .input-group-text {
        font-size: 0.85rem;
    }
    .readonly-text {
        font-weight: 600;
    }
    .scrollable-table {
        max-height: 380px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }
    .scrollable-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        box-shadow: 0 1px 0 #dee2e6;
    }
    .section-header-title {
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }
    .card-body {
        padding: 1.25rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        
        <!-- Filter Bar -->
        <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-3 border" id="filterForm">
            <?= csrf_field() ?>
            <input type="hidden" name="active_tab" id="active_tab_input" value="<?= empty($staf_id_terpilih) ? 'individu' : 'staf' ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small mb-1"><i class="bi bi-calendar-month text-primary me-1"></i> Bulan</label>
                    <select name="bulan" class="form-select form-select-sm border-primary fw-bold text-primary" onchange="this.form.submit()">
                        <?php foreach($bulan_indo as $index => $nama): ?>
                            <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-dark small mb-1"><i class="bi bi-calendar-event text-primary me-1"></i> Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm border-primary fw-bold text-primary" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                </div>
                
                <?php if ($is_super): ?>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark small mb-1"><i class="bi bi-building text-primary me-1"></i> Filter Unit Kerja</label>
                    <select name="unit_kerja" class="form-select form-select-sm border-primary" onchange="this.form.submit()">
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
        <ul class="nav nav-tabs mb-4 border-bottom" id="penilaianTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= empty($staf_id_terpilih) ? 'active' : '' ?> fw-bold text-primary py-2.5 px-3" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-person-lines-fill me-1.5"></i> Target Bulanan Saya
                </button>
            </li>
            <?php if ($is_atasan): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= !empty($staf_id_terpilih) ? 'active' : '' ?> fw-bold text-success py-2.5 px-3" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-people-fill me-1.5"></i> Penilaian Staf
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content" id="penilaianTabsContent">
            
            <!-- ==================== TAB 1: TARGET INDIVIDU SAYA ==================== -->
            <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="individu" role="tabpanel" aria-labelledby="individu-tab">
                <?php if (empty($rekap_data_sendiri)): ?>
                    <div class="alert alert-info py-3 border-info shadow-sm">
                        <i class="bi bi-info-circle-fill me-2"></i> Anda belum memiliki Target Kinerja Bulanan (RHK) pada bulan <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>. Silakan buat target kinerja terlebih dahulu.
                    </div>
                <?php else: ?>
                    <?php
                        $jmlDinilai = 0;
                        $totalNilai = 0;
                        foreach ($rekap_data_sendiri as $rd) {
                            if (isset($rd['status_penilaian']) && $rd['status_penilaian'] === 'terbit' && !empty($rd['nilai_capaian'])) {
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
                            elseif ($rataRataIndividu <= 90) { $warnaScore = 'secondary'; $predikatRataIndividu = 'Butuh Perbaikan'; $badgeColorRataIndividu = 'secondary'; }
                            elseif ($rataRataIndividu <= 100) { $warnaScore = 'primary'; $predikatRataIndividu = 'Baik'; $badgeColorRataIndividu = 'primary'; }
                            else { $warnaScore = 'success'; $predikatRataIndividu = 'Sangat Baik'; $badgeColorRataIndividu = 'success'; }
                        }
                    ?>

                    <!-- Informational Banner -->
                    <div class="alert alert-light border shadow-sm mb-4 py-2.5 px-3 text-dark small">
                        <i class="bi bi-lightbulb text-warning me-2 fs-6"></i>
                        <strong>Informasi Evaluasi Kinerja:</strong> Nilai Kinerja Bulanan diperoleh dari akumulasi penilaian atasan langsung atas Target RHK, Selisih (Gap) Realisasi, serta pertimbangan seluruh Tugas Tambahan yang Anda kerjakan.
                    </div>

                    <!-- BAGIAN A: TARGET KINERJA BULANAN (RHK) -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-primary section-header-title mb-0">
                            <i class="bi bi-list-task me-2"></i> A. Target Kinerja Bulanan (RHK)
                        </h6>
                    </div>
                    
                    <?php if (session()->get('role') === 'direktur'): ?>
                    <form method="POST" action="<?= site_url('penilaian-kinerja/store') ?>">
                        <?= csrf_field() ?>
                    <?php endif; ?>

                    <div class="table-responsive mb-4 bg-white border rounded-3 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">No</th>
                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 140px;">Target Bulanan</th>
                                    <th style="width: 140px;">Total Realisasi</th>
                                    <th style="width: 140px;">Selisih (Gap)</th>
                                    <th class="col-nilai" style="width: 160px;">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                    <?php 
                                        $target = (float)$row['target_bulanan'];
                                        $realisasi = (float)$row['total_realisasi'];
                                        $selisih = $realisasi - $target;
                                        $isTerbit = (isset($row['status_penilaian']) && $row['status_penilaian'] === 'terbit' && $row['nilai_capaian'] !== null);
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                        <td class="text-center fw-bold text-dark"><?= str_replace('.', ',', $target) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold text-primary"><?= str_replace('.', ',', $realisasi) ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold">
                                            <?php if ($selisih > 0): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', $selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php elseif ($selisih < 0): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', $selisih) ?> <?= esc($row['satuan']) ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle fw-bold fs-6 text-primary">
                                            <?php if (session()->get('role') === 'direktur'): ?>
                                                <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="0" max="100" name="nilai_capaian[]" class="form-control text-center text-primary fw-bold" value="<?= isset($row['nilai_capaian']) && $row['nilai_capaian'] !== null ? (float)$row['nilai_capaian'] : '' ?>" placeholder="0 - 100">
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

                    <!-- BAGIAN B: TUGAS TAMBAHAN (OPSIONAL) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                        <h6 class="fw-bold text-secondary section-header-title mb-0">
                            <i class="bi bi-journal-plus me-2 text-primary"></i> B. Tugas Tambahan Bulan <?= $bulan_indo[$bulan_terpilih - 1] ?>
                        </h6>
                    </div>
                    
                    <div class="table-responsive mb-4 bg-white border rounded-3 shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">No</th>
                                    <th class="text-start">Deskripsi Tugas Tambahan</th>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th style="width: 140px;">Capaian / Output</th>
                                    <th style="width: 90px;">Bukti</th>
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
                                            <td class="text-center text-muted">
                                                <?php 
                                                    $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                    $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                    echo $tgl . ' ' . substr($bln, 0, 3);
                                                ?>
                                            </td>
                                            <td class="text-center fw-semibold text-dark">
                                                <?= !empty($tmb['jumlah_capaian']) ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($tmb['link_bukti'])): ?>
                                                    <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
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
                                    <td colspan="4" class="text-end pe-3 align-middle text-muted fw-normal">Nilai Tugas Tambahan Bulan <?= $bulan_indo[$bulan_terpilih - 1] ?> (Akumulasi):</td>
                                    <td class="text-center align-middle fw-bold fs-6 text-success p-2">
                                        <?php if (session()->get('role') === 'direktur'): ?>
                                            <input type="hidden" name="log_tambahan_id[]" value="<?= esc($tugas_tambahan_sendiri[0]['id']) ?>">
                                            <div class="input-group input-group-sm justify-content-center" style="max-width: 130px; margin: 0 auto;">
                                                <input type="number" step="0.01" min="0" max="100" name="nilai_tugas_tambahan_gabungan" class="form-control text-center text-success fw-bold" value="<?= $scoreTambahanIndividu !== null ? (float)$scoreTambahanIndividu : '' ?>" placeholder="0 - 100">
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
                    <div class="d-flex justify-content-end mb-4 gap-2">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-pencil me-1"></i> Simpan Draf Penilaian
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-send me-1"></i> Simpan & Terbitkan Penilaian Direktur
                        </button>
                    </div>
                    </form>
                    <?php endif; ?>

                    <!-- RINGKASAN SKOR EXECUTIVE DI PALING BAWAH EVALUASI -->
                    <div class="card bg-white border border-2 border-<?= $warnaScore === 'warning text-dark' ? 'warning' : $warnaScore ?> rounded-3 p-3 shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-award-fill text-primary me-2"></i> RATA-RATA PENILAIAN KINERJA BULANAN</h6>
                                <small class="text-muted">Dihitung dari Rata-rata Nilai Target RHK (<?= count($rekap_data_sendiri) ?> Indikator) <?= $scoreTambahanIndividu !== null ? '+ 1 Nilai Tugas Tambahan' : '' ?></small>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-<?= $badgeColorRataIndividu ?> fs-6 px-3 py-2"><?= $predikatRataIndividu ?></span>
                                <span class="fs-2 fw-bold text-<?= $warnaScore ?> mb-0 lh-1"><?= str_replace('.', ',', round($rataRataIndividu, 2)) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN C: BUKTI & LOG LAPORAN HARIAN -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                        <h6 class="fw-bold text-secondary section-header-title mb-0">
                            <i class="bi bi-calendar-check me-2 text-primary"></i> C. Bukti & Activity Log Laporan Harian
                        </h6>
                    </div>
                    
                    <div class="scrollable-table mb-2 bg-white shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 45px;">No</th>
                                    <th style="width: 100px;">Tanggal</th>
                                    <th>Aktivitas Harian</th>
                                    <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                    <th style="width: 110px;">Realisasi</th>
                                    <th style="width: 80px;">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($log_harian_sendiri)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada laporan harian pada bulan ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($log_harian_sendiri as $index => $log): ?>
                                        <tr>
                                            <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                            <td class="text-center text-muted">
                                                <?php 
                                                    $tgl = date('j', strtotime($log['tanggal_kegiatan']));
                                                    $bln = $bulan_indo[date('n', strtotime($log['tanggal_kegiatan'])) - 1];
                                                    echo $tgl . ' ' . substr($bln, 0, 3);
                                                ?>
                                            </td>
                                            <td><?= nl2br(esc($log['deskripsi_kegiatan'])) ?></td>
                                            <td><small class="text-muted"><?= esc($log['indikator_kinerja']) ?></small></td>
                                            <td class="text-center fw-bold text-primary"><?= str_replace('.', ',', (float)$log['jumlah_capaian']) ?> <small><?= esc($log['satuan']) ?></small></td>
                                            <td class="text-center">
                                                <?php if (!empty($log['link_bukti'])): ?>
                                                    <a href="<?= esc($log['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
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
                
                <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded-3 border" id="formPilihStaf">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">
                    
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success mb-1 small"><i class="bi bi-person-check me-1"></i> Pilih Staf yang Akan Dinilai</label>
                            <select name="staf_id" id="selectStaf" class="form-select border-success form-select-sm" onchange="this.form.submit()">
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
                        <div class="alert alert-info py-3 border-info shadow-sm mt-2">
                            <i class="bi bi-info-circle-fill me-2"></i> Pegawai ini belum memiliki Target Kinerja (RHK) pada bulan <?= $bulan_indo[$bulan_terpilih - 1] ?> <?= $tahun_terpilih ?>.
                        </div>
                    <?php else: ?>
                        <?php
                            $jmlDinilaiBwh = 0;
                            $totalNilaiBwh = 0;
                            foreach ($rekap_data_staf as $rd) {
                                if (!empty($rd['nilai_capaian'])) {
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
                                elseif ($rataRataBwh <= 90) { $warnaScoreBwh = 'secondary'; $predikatRataRataBwh = 'Butuh Perbaikan'; $badgeColorRataBwh = 'secondary'; }
                                elseif ($rataRataBwh <= 100) { $warnaScoreBwh = 'primary'; $predikatRataRataBwh = 'Baik'; $badgeColorRataBwh = 'primary'; }
                                else { $warnaScoreBwh = 'success'; $predikatRataRataBwh = 'Sangat Baik'; $badgeColorRataBwh = 'success'; }
                            }
                        ?>

                        <!-- GUIDANCE & RULES BANNER -->
                        <div class="alert alert-light border border-info-subtle shadow-sm py-2.5 px-3 small mb-4">
                            <strong><i class="bi bi-journal-check text-success me-1"></i> Panduan Predikat Penilaian Capaian:</strong><br>
                            <span class="badge bg-danger">Sangat Kurang</span>: &le; 25% &nbsp;|&nbsp;
                            <span class="badge bg-warning text-dark">Kurang</span>: > 25% - 75% &nbsp;|&nbsp;
                            <span class="badge bg-secondary">Butuh Perbaikan</span>: > 75% - 90% &nbsp;|&nbsp;
                            <span class="badge bg-primary">Baik</span>: > 90% - 100% &nbsp;|&nbsp;
                            <span class="badge bg-success">Sangat Baik</span>: > 100% - 150%
                            <br><i class="text-muted">* Cermati selisih realisasi vs target serta seluruh tugas tambahan di bawah sebelum memberikan nilai capaian.</i>
                        </div>

                        <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
                            <?= csrf_field() ?>
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                            <!-- BAGIAN A STAF: TARGET KINERJA BULANAN (RHK) -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-success section-header-title mb-0">
                                    <i class="bi bi-list-task me-2"></i> A. Penilaian Target Kinerja Bulanan (RHK)
                                </h6>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-3 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0" id="tablePenilaianStaf">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">No</th>
                                            <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                            <th style="width: 130px;" class="text-center">Target Bulanan</th>
                                            <th style="width: 130px;" class="text-center">Total Realisasi</th>
                                            <th style="width: 130px;" class="text-center">Selisih (Gap)</th>
                                            <th style="width: 135px;" class="text-center">Input Nilai Capaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php 
                                                $target = (float)$row['target_bulanan'];
                                                $realisasi = (float)$row['total_realisasi'];
                                                $selisih = $realisasi - $target;
                                                
                                                $nilai_capaian = $row['nilai_capaian'];
                                                $predikatLabel = '-';
                                                $predikatBadge = 'bg-light text-muted border';
                                                if ($nilai_capaian !== null && $nilai_capaian !== '') {
                                                    $n = (float)$nilai_capaian;
                                                    if ($n <= 25) { $predikatLabel = 'Sangat Kurang'; $predikatBadge = 'bg-danger'; }
                                                    elseif ($n <= 75) { $predikatLabel = 'Kurang'; $predikatBadge = 'bg-warning text-dark'; }
                                                    elseif ($n <= 90) { $predikatLabel = 'Butuh Perbaikan'; $predikatBadge = 'bg-secondary'; }
                                                    elseif ($n <= 100) { $predikatLabel = 'Baik'; $predikatBadge = 'bg-primary'; }
                                                    else { $predikatLabel = 'Sangat Baik'; $predikatBadge = 'bg-success'; }
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td class="fw-semibold text-dark"><?= esc($row['indikator_kinerja']) ?></td>
                                                <td class="text-center fw-bold text-dark"><?= str_replace('.', ',', $target) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold text-primary"><?= str_replace('.', ',', $realisasi) ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold">
                                                    <?php if ($selisih > 0): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">+<?= str_replace('.', ',', $selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php elseif ($selisih < 0): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= str_replace('.', ',', $selisih) ?> <?= esc($row['satuan']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border px-2 py-1">0 <?= esc($row['satuan']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle p-2 text-center" style="width: 135px;">
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <input type="number" name="nilai_capaian[]" class="form-control text-center input-nilai-capaian fw-bold text-primary p-1" style="font-size:0.9rem;" value="<?= $nilai_capaian !== null ? (float)$nilai_capaian : '' ?>" step="0.01" min="0" max="150" placeholder="0 - 150" required>
                                                        <span class="input-group-text bg-light px-1">%</span>
                                                    </div>
                                                    <div class="predikat-badge-container">
                                                        <span class="badge <?= $predikatBadge ?>" style="font-size:0.7rem;"><?= $predikatLabel ?></span>
                                                    </div>
                                                    <div class="invalid-feedback" style="font-size: 0.7rem;">Nilai tidak sesuai!</div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- BAGIAN B STAF: TUGAS TAMBAHAN -->
                            <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                                <h6 class="fw-bold text-secondary section-header-title mb-0">
                                    <i class="bi bi-journal-plus me-2 text-success"></i> B. Penilaian Tugas Tambahan Bulan <?= $bulan_indo[$bulan_terpilih - 1] ?>
                                </h6>
                            </div>

                            <div class="table-responsive mb-4 bg-white border rounded-3 shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;">No</th>
                                            <th class="text-start">Deskripsi Tugas Tambahan</th>
                                            <th style="width: 120px;">Tanggal</th>
                                            <th style="width: 140px;">Capaian / Output</th>
                                            <th style="width: 90px;">Bukti</th>
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
                                                    <td class="text-center text-muted">
                                                        <?php 
                                                            $tgl = date('j', strtotime($tmb['tanggal_kegiatan']));
                                                            $bln = $bulan_indo[date('n', strtotime($tmb['tanggal_kegiatan'])) - 1];
                                                            echo $tgl . ' ' . substr($bln, 0, 3);
                                                        ?>
                                                    </td>
                                                    <td class="text-center fw-semibold text-dark">
                                                        <?= !empty($tmb['jumlah_capaian']) ? str_replace('.', ',', (float)$tmb['jumlah_capaian']) : '-' ?> <?= esc($tmb['satuan'] ?? '') ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (!empty($tmb['link_bukti'])): ?>
                                                            <a href="<?= esc($tmb['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
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
                                            <td colspan="4" class="text-end pe-3 align-middle text-dark fw-bold" style="font-size: 0.85rem;">
                                                <i class="bi bi-journal-check text-success me-1"></i> Nilai Tugas Tambahan Bulan <?= $bulan_indo[$bulan_terpilih - 1] ?> (0 - 100%):
                                            </td>
                                            <td class="p-2 align-middle text-center" style="width: 145px;">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" max="100" min="0" name="nilai_tugas_tambahan_gabungan" id="inputNilaiTambahanGabungan" class="form-control text-center fw-bold text-success p-1" style="font-size:0.9rem;" value="<?= $scoreTambahanStaf !== null ? $scoreTambahanStaf : '' ?>" placeholder="0 - 100" <?= !$is_penilai ? 'readonly' : '' ?>>
                                                    <span class="input-group-text bg-light px-1">%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>

                            <!-- RINGKASAN EXECUTIVE SKOR AKHIR DI PALING BAWAH PENILAIAN -->
                            <div class="card bg-white border border-2 border-<?= $warnaScoreBwh === 'warning text-dark' ? 'warning' : $warnaScoreBwh ?> rounded-3 p-3 shadow-sm mb-4" id="cardKinerjaStafSummary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-award-fill text-success me-2"></i> RATA-RATA PENILAIAN KINERJA BULANAN STAF</h6>
                                        <small class="text-muted" id="subtextHitungKinerja">Dihitung dari Rata-rata Nilai Target RHK (<?= count($rekap_data_staf) ?> Indikator) <?= !empty($tugas_tambahan_staf) ? '+ 1 Nilai Tugas Tambahan' : '' ?></small>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span id="totalPredikatStafBadge" class="badge bg-<?= $badgeColorRataBwh ?> fs-6 px-3 py-2"><?= $predikatRataRataBwh ?></span>
                                        <span class="fs-2 fw-bold text-<?= $warnaScoreBwh ?> mb-0 lh-1" id="totalKinerjaStafText"><?= str_replace('.', ',', round($rataRataBwh, 2)) ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- ACTION TOOLBAR AT BOTTOM OF STAF FORM -->
                            <div class="d-flex justify-content-end mb-4 gap-2">
                                <button type="reset" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold shadow-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Kosongkan</button>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm"><i class="bi bi-journal-bookmark me-1"></i> Simpan Sementara</button>
                                <button type="submit" name="action" value="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm"><i class="bi bi-check-circle-fill me-2"></i> Simpan Penilaian Staf</button>
                            </div>
                        </form>

                        <!-- BAGIAN C STAF: BUKTI LAPORAN HARIAN STAF -->
                        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                            <h6 class="fw-bold text-secondary section-header-title mb-0">
                                <i class="bi bi-calendar-check me-2 text-success"></i> C. Bukti & Activity Log Laporan Harian Staf
                            </h6>
                        </div>

                        <div class="scrollable-table mb-4 bg-white shadow-sm">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th style="width: 100px;">Tanggal</th>
                                        <th>Aktivitas Harian</th>
                                        <th class="col-target text-start">Indikator Kinerja / RHK</th>
                                        <th style="width: 110px;">Realisasi</th>
                                        <th style="width: 80px;">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($log_harian_staf)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada laporan harian dari staf ini.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($log_harian_staf as $index => $log): ?>
                                            <tr>
                                                <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td class="text-center text-muted">
                                                    <?php 
                                                        $tgl = date('j', strtotime($log['tanggal_kegiatan']));
                                                        $bln = $bulan_indo[date('n', strtotime($log['tanggal_kegiatan'])) - 1];
                                                        echo $tgl . ' ' . substr($bln, 0, 3);
                                                    ?>
                                                </td>
                                                <td><?= nl2br(esc($log['deskripsi_kegiatan'])) ?></td>
                                                <td><small class="text-muted"><?= esc($log['indikator_kinerja']) ?></small></td>
                                                <td class="text-center fw-bold text-primary"><?= str_replace('.', ',', (float)$log['jumlah_capaian']) ?> <small><?= esc($log['satuan']) ?></small></td>
                                                <td class="text-center">
                                                    <?php if (!empty($log['link_bukti'])): ?>
                                                        <a href="<?= esc($log['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-secondary py-3 border-secondary shadow-sm mt-2">
                        <i class="bi bi-arrow-up-circle-fill me-2 text-primary"></i> Silakan pilih nama staf pada menu pencarian di atas untuk mulai melakukan penilaian kinerja bulanan.
                    </div>
                <?php endif; ?>

            </div> <!-- End Tab Staf -->
            <?php endif; ?>

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

        function getPredikatInfo(val) {
            if (isNaN(val) || val === '' || val === null) {
                return { label: '-', class: 'bg-light text-muted border', textClass: 'secondary' };
            }
            if (val <= 25) return { label: 'Sangat Kurang', class: 'bg-danger', textClass: 'danger' };
            if (val <= 75) return { label: 'Kurang', class: 'bg-warning text-dark', textClass: 'warning' };
            if (val <= 90) return { label: 'Butuh Perbaikan', class: 'bg-secondary', textClass: 'secondary' };
            if (val <= 100) return { label: 'Baik', class: 'bg-primary', textClass: 'primary' };
            return { label: 'Sangat Baik', class: 'bg-success', textClass: 'success' };
        }

        function calculateOverallStafScore() {
            let total = 0;
            let count = 0;

            // 1. Hitung Nilai RHK
            $('.input-nilai-capaian').each(function() {
                let v = parseFloat($(this).val());
                if (!isNaN(v) && !$(this).hasClass('is-invalid')) {
                    total += v;
                    count++;
                }
            });

            // 2. Hitung Nilai Akumulasi Tugas Tambahan (jika diisi)
            let vTambahan = parseFloat($('#inputNilaiTambahanGabungan').val());
            if (!isNaN(vTambahan) && vTambahan >= 0 && vTambahan <= 100) {
                total += vTambahan;
                count++;
            }

            let avg = count > 0 ? (Math.round((total / count) * 100) / 100) : 0;
            $('#totalKinerjaStafText').text(avg.toString().replace('.', ','));

            let pRata = getPredikatInfo(count > 0 ? avg : null);
            
            let textEl = $('#totalKinerjaStafText');
            let wrapper = $('#cardKinerjaStafSummary');
            let badgeEl = $('#totalPredikatStafBadge');

            textEl.removeClass('text-success text-secondary text-danger text-warning text-primary');
            wrapper.removeClass('border-success border-secondary border-danger border-warning border-primary');

            textEl.addClass('text-' + pRata.textClass);
            wrapper.addClass('border-' + pRata.textClass);

            badgeEl.attr('class', 'badge ' + pRata.class + ' fs-6 px-3 py-2').text(pRata.label);
        }

        // Auto-calculate Rata-rata Kinerja Staf & Update Predikat Per-Baris secara Real-time
        $(document).on('input change keyup', '.input-nilai-capaian, #inputNilaiTambahanGabungan', function() {
            if ($(this).hasClass('input-nilai-capaian')) {
                var val = parseFloat($(this).val());
                var badgeContainer = $(this).closest('td').find('.predikat-badge-container');
                var error = $(this).parent().siblings('.invalid-feedback');
                var btnSubmit = $('#formPenilaian button[type="submit"]');

                if (!isNaN(val)) {
                    let p = getPredikatInfo(val);
                    badgeContainer.html('<span class="badge ' + p.class + '" style="font-size:0.75rem;">' + p.label + '</span>');
                } else {
                    badgeContainer.html('<span class="badge bg-light text-muted border" style="font-size:0.75rem;">-</span>');
                }

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
                title: 'Kosongkan Isian?',
                text: "Seluruh isian penilaian di layar ini akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash3 me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.input-nilai-capaian, #inputNilaiTambahanGabungan').val('').removeClass('is-invalid').removeAttr('title');
                    $('.invalid-feedback').hide();
                    $('.predikat-badge-container').html('<span class="badge bg-light text-muted border" style="font-size:0.75rem;">-</span>');
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
    });
</script>
<?= $this->endSection() ?>
