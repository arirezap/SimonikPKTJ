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
    }
    .table td {
        vertical-align: middle;
    }
    .table {
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .col-target {
        min-width: 250px;
    }
    .col-nilai {
        min-width: 120px;
        max-width: 150px;
    }
    .form-control-sm, .form-select-sm, .input-group-sm > .input-group-text {
        font-size: 0.85rem;
    }
    .readonly-text {
        font-weight: 500;
    }
    /* Scrollable table container */
    .scrollable-table {
        max-height: 350px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .scrollable-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        box-shadow: 0 1px 0 #dee2e6; /* Fake bottom border for sticky header */
    }
    .card-body {
        padding: 1rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        
        <!-- Filter Bar -->
        <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-3 p-2 bg-light rounded border" id="filterForm">
            <?= csrf_field() ?>
            <input type="hidden" name="active_tab" id="active_tab_input" value="<?= empty($staf_id_terpilih) ? 'individu' : 'staf' ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.85rem;">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm border-primary" onchange="this.form.submit()">
                        <?php foreach($bulan_indo as $index => $nama): ?>
                            <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.85rem;">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm border-primary" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                </div>
                
                <?php if ($is_super): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.85rem;">Filter Unit</label>
                    <select name="unit_kerja" class="form-select form-select-sm border-primary" onchange="this.form.submit()">
                        <option value="">Semua Unit</option>
                        <?php foreach ($daftar_unit as $u): ?>
                            <option value="<?= esc($u) ?>" <?= ($u == $unit_kerja_terpilih) ? 'selected' : '' ?>><?= esc($u) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

            </div>
        </form>

        <ul class="nav nav-tabs mb-3" id="penilaianTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= empty($staf_id_terpilih) ? 'active' : '' ?> fw-bold text-primary py-2" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="<?= empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-person-lines-fill me-1"></i> Target Bulanan Saya
                </button>
            </li>
            <?php if ($is_atasan): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= !empty($staf_id_terpilih) ? 'active' : '' ?> fw-bold text-success py-2" id="staf-tab" data-bs-toggle="tab" data-bs-target="#staf" type="button" role="tab" aria-controls="staf" aria-selected="<?= !empty($staf_id_terpilih) ? 'true' : 'false' ?>">
                    <i class="bi bi-people-fill me-1"></i> Penilaian Staf
                </button>
            </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content" id="penilaianTabsContent">
            <!-- TAB: TARGET INDIVIDU -->
            <div class="tab-pane fade <?= empty($staf_id_terpilih) ? 'show active' : '' ?>" id="individu" role="tabpanel" aria-labelledby="individu-tab">
                <?php if (empty($rekap_data_sendiri)): ?>
                    <div class="alert alert-info py-2">
                        <i class="bi bi-info-circle me-2"></i> Anda belum memiliki Target Kinerja Bulanan (RHK) pada bulan ini.
                    </div>
                <?php else: ?>
                    <?php
                        $jmlDinilai = 0;
                        $totalNilai = 0;
                        foreach ($rekap_data_sendiri as $rd) {
                            if (!empty($rd['nilai_capaian'])) {
                                $jmlDinilai++;
                                $totalNilai += (float)$rd['nilai_capaian'];
                            }
                        }
                        $rataRataIndividu = $jmlDinilai > 0 ? (float)($totalNilai / $jmlDinilai) : 0;
                        
                        $warnaScore = 'success';
                        if ($jmlDinilai == 0) {
                            $warnaScore = 'secondary';
                        } elseif ($rataRataIndividu < 60) {
                            $warnaScore = 'danger';
                        } elseif ($rataRataIndividu < 75) {
                            $warnaScore = 'warning text-dark';
                        }
                    ?>


                    <!-- PENILAIAN RHK DI ATAS -->
                    <h6 class="fw-bold mb-2 text-primary"><i class="bi bi-bullseye me-2"></i> Target Kinerja Bulanan (RHK)</h6>
                    <div class="table-responsive mb-4 bg-white border rounded shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th class="col-target">Indikator Kinerja / RHK</th>
                                    <th>Target Bulanan</th>
                                    <th>Total Realisasi</th>
                                    <th>Selisih (Gap)</th>
                                    <th class="col-nilai">Nilai Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap_data_sendiri as $index => $row): ?>
                                    <?php 
                                        $target = (float)$row['target_bulanan'];
                                        $realisasi = (float)$row['total_realisasi'];
                                        $selisih = $realisasi - $target;
                                        $warnaSelisih = $selisih >= 0 ? 'text-success' : 'text-danger';
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td><?= esc($row['indikator_kinerja']) ?></td>
                                        <td class="text-center fw-bold"><?= $target ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold text-primary"><?= $realisasi ?> <?= esc($row['satuan']) ?></td>
                                        <td class="text-center fw-bold <?= $warnaSelisih ?>">
                                            <?= $selisih > 0 ? '+' : '' ?><?= $selisih ?> <?= esc($row['satuan']) ?>
                                        </td>
                                        <td class="text-center align-middle readonly-text fw-bold fs-6 text-primary">
                                            <?= $row['nilai_capaian'] !== null ? str_replace('.', ',', (float)$row['nilai_capaian']) : '-' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                <tr>
                                    <td colspan="5" class="text-end pe-3 align-middle text-muted fw-normal" style="font-size: 0.85rem;">Rata-rata Penilaian Kinerja:</td>
                                    <td class="align-middle p-2">
                                        <div class="d-flex justify-content-between align-items-center bg-white border border-<?= $warnaScore === 'warning text-dark' ? 'warning' : $warnaScore ?> rounded px-3 py-2 shadow-sm">
                                            <div class="d-flex flex-column">
                                                <span class="small fw-bold text-muted mb-0" style="font-size: 0.7rem; line-height: 1;">NILAI KINERJA</span>
                                            </div>
                                            <span class="fs-4 fw-bold text-<?= $warnaScore ?> mb-0 lh-1"><?= str_replace('.', ',', round($rataRataIndividu, 2)) ?></span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- LAPORAN HARIAN SCROLLABLE DI BAWAH -->
                    <h6 class="fw-bold mb-2 text-secondary"><i class="bi bi-list-task me-2"></i> Bukti Laporan Harian</h6>
                    <div class="scrollable-table mb-2 bg-white border rounded shadow-sm">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 90px;">Tanggal</th>
                                    <th>Aktivitas Harian</th>
                                    <th class="col-target">Indikator Kinerja / RHK</th>
                                    <th style="width: 80px;">Realisasi</th>
                                    <th style="width: 80px;">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($log_harian_sendiri)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">Belum ada laporan harian.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($log_harian_sendiri as $index => $log): ?>
                                        <tr>
                                            <td class="text-center"><?= $index + 1 ?></td>
                                            <td class="text-center">
                                                <?php 
                                                    $tgl = date('j', strtotime($log['tanggal_kegiatan']));
                                                    $bln = $bulan_indo[date('n', strtotime($log['tanggal_kegiatan'])) - 1];
                                                    echo $tgl . ' ' . substr($bln, 0, 3);
                                                ?>
                                            </td>
                                            <td><?= nl2br(esc($log['deskripsi_kegiatan'])) ?></td>
                                            <td><small class="text-muted"><?= esc($log['indikator_kinerja']) ?></small></td>
                                            <td class="text-center fw-bold"><?= (float)$log['jumlah_capaian'] ?> <small><?= esc($log['satuan']) ?></small></td>
                                            <td class="text-center">
                                                <?php if (!empty($log['link_bukti'])): ?>
                                                    <a href="<?= esc($log['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border border-light" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background-color: #f8f9fa;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
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
            <!-- TAB: PENILAIAN STAF (KHUSUS ATASAN) -->
            <div class="tab-pane fade <?= !empty($staf_id_terpilih) ? 'show active' : '' ?>" id="staf" role="tabpanel" aria-labelledby="staf-tab">
                
                <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-3 p-2 bg-light rounded border" id="formPilihStaf">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                    <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                    <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">
                    
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success mb-1" style="font-size: 0.85rem;">Pilih Staf yang Akan Dinilai</label>
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
                        <div class="alert alert-info py-2 mt-2">
                            <i class="bi bi-info-circle me-2"></i> Pegawai ini belum memiliki Target Kinerja (RHK) pada bulan tersebut.
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
                            $rataRataBwh = $jmlDinilaiBwh > 0 ? (float)($totalNilaiBwh / $jmlDinilaiBwh) : 0;
                            
                            $warnaScoreBwh = 'success';
                            if ($jmlDinilaiBwh == 0) {
                                $warnaScoreBwh = 'secondary';
                            } elseif ($rataRataBwh < 60) {
                                $warnaScoreBwh = 'danger';
                            } elseif ($rataRataBwh < 75) {
                                $warnaScoreBwh = 'warning text-dark';
                            }
                        ?>

                        <!-- PENILAIAN RHK DI ATAS -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-success mb-0"><i class="bi bi-bullseye me-2"></i> Penilaian Target Kinerja Bulanan (RHK)</h6>
                        </div>
                        
                        <div class="alert alert-info py-2 small mb-3 border-info">
                            <strong>Panduan Predikat Penilaian:</strong><br>
                            <span class="badge bg-danger">Sangat Kurang</span>: &le; 25% &nbsp;|&nbsp;
                            <span class="badge bg-warning text-dark">Kurang</span>: > 25% - 75% &nbsp;|&nbsp;
                            <span class="badge bg-secondary">Butuh Perbaikan</span>: > 75% - 90% &nbsp;|&nbsp;
                            <span class="badge bg-primary">Baik</span>: > 90% - 100% &nbsp;|&nbsp;
                            <span class="badge bg-success">Sangat Baik</span>: > 100% - 150%
                            <br><i class="text-muted">* Pilih predikat terlebih dahulu, lalu masukkan angka capaian (tanpa simbol %) sesuai batas rentang predikat.</i>
                        </div>

                        <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
                            <?= csrf_field() ?>
                            <input type="hidden" name="staf_id" value="<?= esc($staf_id_terpilih) ?>">
                            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
                            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
                            <input type="hidden" name="unit_kerja" value="<?= esc($unit_kerja_terpilih) ?>">

                            <div class="table-responsive mb-4 bg-white border rounded shadow-sm">
                                <table class="table table-bordered table-hover align-middle mb-0" id="tablePenilaianStaf">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">No</th>
                                            <th class="col-target">Indikator Kinerja / RHK</th>
                                            <th>Target Bulanan</th>
                                            <th>Total Realisasi</th>
                                            <th>Selisih (Gap)</th>
                                            <th class="col-nilai">Input Nilai Capaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rekap_data_staf as $index => $row): ?>
                                            <?php 
                                                $target = (float)$row['target_bulanan'];
                                                $realisasi = (float)$row['total_realisasi'];
                                                $selisih = $realisasi - $target;
                                                $warnaSelisih = $selisih >= 0 ? 'text-success' : 'text-danger';
                                                
                                                // Tentukan predikat jika sudah ada nilai
                                                $nilai_capaian = $row['nilai_capaian'];
                                                $predikat = '';
                                                if ($nilai_capaian !== null && $nilai_capaian !== '') {
                                                    $n = (float)$nilai_capaian;
                                                    if ($n <= 25) $predikat = 'sangat_kurang';
                                                    elseif ($n > 25 && $n <= 75) $predikat = 'kurang';
                                                    elseif ($n > 75 && $n <= 90) $predikat = 'butuh_perbaikan';
                                                    elseif ($n > 90 && $n <= 100) $predikat = 'baik';
                                                    elseif ($n > 100) $predikat = 'sangat_baik';
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td><?= esc($row['indikator_kinerja']) ?></td>
                                                <td class="text-center fw-bold"><?= $target ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold text-primary"><?= $realisasi ?> <?= esc($row['satuan']) ?></td>
                                                <td class="text-center fw-bold <?= $warnaSelisih ?>">
                                                    <?= $selisih > 0 ? '+' : '' ?><?= $selisih ?> <?= esc($row['satuan']) ?>
                                                </td>
                                                <td class="align-middle p-2" style="min-width: 160px;">
                                                    <input type="hidden" name="laporan_id[]" value="<?= esc($row['id']) ?>">
                                                    <select class="form-select form-select-sm mb-1 predikat-select" required style="font-size: 0.8rem;">
                                                        <option value="">- Predikat -</option>
                                                        <option value="sangat_kurang" <?= $predikat == 'sangat_kurang' ? 'selected' : '' ?>>Sangat Kurang</option>
                                                        <option value="kurang" <?= $predikat == 'kurang' ? 'selected' : '' ?>>Kurang</option>
                                                        <option value="butuh_perbaikan" <?= $predikat == 'butuh_perbaikan' ? 'selected' : '' ?>>Butuh Perbaikan</option>
                                                        <option value="baik" <?= $predikat == 'baik' ? 'selected' : '' ?>>Baik</option>
                                                        <option value="sangat_baik" <?= $predikat == 'sangat_baik' ? 'selected' : '' ?>>Sangat Baik</option>
                                                    </select>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="nilai_capaian[]" class="form-control text-center input-nilai-capaian fw-bold text-primary" style="font-size:0.95rem;" value="<?= $nilai_capaian !== null ? (float)$nilai_capaian : '' ?>" step="0.01" required <?= empty($predikat) ? 'disabled' : '' ?>>
                                                        <span class="input-group-text bg-light">%</span>
                                                    </div>
                                                    <div class="invalid-feedback" style="font-size: 0.7rem;">Nilai tidak sesuai!</div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                                        <tr>
                                            <td colspan="5" class="text-end pe-3 align-middle text-muted fw-normal" style="font-size: 0.85rem;">Rata-rata Penilaian Kinerja:</td>
                                            <td class="align-middle p-2">
                                                <div id="totalKinerjaStafWrapper" class="d-flex justify-content-between align-items-center bg-white border border-<?= $warnaScoreBwh === 'warning text-dark' ? 'warning' : $warnaScoreBwh ?> rounded px-3 py-2 shadow-sm">
                                                    <div class="d-flex flex-column">
                                                        <span class="small fw-bold text-muted mb-0" style="font-size: 0.7rem; line-height: 1;">NILAI KINERJA</span>
                                                    </div>
                                                    <span class="fs-4 fw-bold text-<?= $warnaScoreBwh ?> mb-0 lh-1" id="totalKinerjaStafText"><?= str_replace('.', ',', round($rataRataBwh, 2)) ?></span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mb-4 gap-2">
                                <button type="reset" class="btn btn-outline-secondary btn-sm px-3 fw-bold shadow-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Kosongkan</button>
                                <button type="submit" name="action" value="draft" class="btn btn-outline-primary btn-sm px-3 fw-bold shadow-sm"><i class="bi bi-journal-bookmark me-1"></i> Simpan Sementara</button>
                                <button type="submit" name="action" value="submit" class="btn btn-success btn-sm px-4 fw-bold shadow-sm"><i class="bi bi-save me-2"></i> Simpan Penilaian Staf</button>
                            </div>
                        </form>

                        <!-- LAPORAN HARIAN SCROLLABLE DI BAWAH -->
                        <h6 class="fw-bold mb-2 text-secondary"><i class="bi bi-list-task me-2"></i> Bukti Laporan Harian Staf</h6>
                        <div class="scrollable-table mb-4 bg-white border rounded shadow-sm">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th style="width: 90px;">Tanggal</th>
                                        <th>Aktivitas Harian</th>
                                        <th class="col-target">Indikator Kinerja / RHK</th>
                                        <th style="width: 80px;">Realisasi</th>
                                        <th style="width: 80px;">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($log_harian_staf)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">Belum ada laporan harian.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($log_harian_staf as $index => $log): ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td class="text-center">
                                                    <?php 
                                                        $tgl = date('j', strtotime($log['tanggal_kegiatan']));
                                                        $bln = $bulan_indo[date('n', strtotime($log['tanggal_kegiatan'])) - 1];
                                                        echo $tgl . ' ' . substr($bln, 0, 3);
                                                    ?>
                                                </td>
                                                <td><?= nl2br(esc($log['deskripsi_kegiatan'])) ?></td>
                                                <td><small class="text-muted"><?= esc($log['indikator_kinerja']) ?></small></td>
                                                <td class="text-center fw-bold"><?= (float)$log['jumlah_capaian'] ?> <small><?= esc($log['satuan']) ?></small></td>
                                                <td class="text-center">
                                                    <?php if (!empty($log['link_bukti'])): ?>
                                                        <a href="<?= esc($log['link_bukti']) ?>" target="_blank" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm border border-light" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background-color: #f8f9fa;" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg fs-5"></i></a>
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
                    <div class="alert alert-secondary py-2 mt-2">
                        <i class="bi bi-arrow-up-circle me-2"></i> Silakan pilih staf pada dropdown di atas untuk memulai penilaian.
                    </div>
                <?php endif; ?>

            </div> <!-- End Tab Staf -->
            <?php endif; ?>



        
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const bulanTerpilih = <?= esc($bulan_terpilih) ?>;
    const tahunTerpilih = <?= esc($tahun_terpilih) ?>;

    $(document).ready(function() {
        // Inisialisasi Select2
        if ($('#selectStaf').length) {
            $('#selectStaf').select2({ 
                width: '100%', 
                placeholder: "Cari Nama...",
                allowClear: false
            });
            $('#selectStaf').on('select2:select', function (e) { $(this).closest('form').submit(); });
            
            // Auto focus search box on open
            $('#selectStaf').on('select2:open', function () {
                setTimeout(function() {
                    document.querySelector('.select2-search__field').focus();
                }, 50);
            });
        }
        
        // Handle Predikat change
        $('.predikat-select').on('change', function() {
            var val = $(this).val();
            var input = $(this).siblings('.input-group').find('.input-nilai-capaian');
            
            if (val === '') {
                input.prop('disabled', true).val('');
                input.removeAttr('min max');
            } else {
                input.prop('disabled', false);
                if (val === 'sangat_kurang') { input.attr('min', 0).attr('max', 25); }
                else if (val === 'kurang') { input.attr('min', 25.01).attr('max', 75); }
                else if (val === 'butuh_perbaikan') { input.attr('min', 75.01).attr('max', 90); }
                else if (val === 'baik') { input.attr('min', 90.01).attr('max', 100); }
                else if (val === 'sangat_baik') { input.attr('min', 100.01).attr('max', 150); }
                
                // Trigger validation if there's a value
                if (input.val() !== '') {
                    input.trigger('input');
                }
            }
        });

        // Auto-calculate Rata-rata Kinerja Staf & Validate
        $('.input-nilai-capaian').on('input', function() {
            var min = parseFloat($(this).attr('min'));
            var max = parseFloat($(this).attr('max'));
            var val = parseFloat($(this).val());
            var error = $(this).parent().siblings('.invalid-feedback');
            var btnSubmit = $('#formPenilaian button[type="submit"]');

            // Validation
            if (!isNaN(val) && (val < min || val > max)) {
                $(this).addClass('is-invalid');
                let hintMsg = 'Batas nilai: ' + min + ' - ' + max;
                error.text(hintMsg).show();
                $(this).attr('title', hintMsg);
                btnSubmit.prop('disabled', true);
            } else {
                $(this).removeClass('is-invalid');
                $(this).removeAttr('title');
                error.hide();
                // Check if any other is invalid
                if ($('.input-nilai-capaian.is-invalid').length === 0) {
                    btnSubmit.prop('disabled', false);
                }
            }

            // Calculation
            let total = 0;
            let count = 0;
            $('.input-nilai-capaian').each(function() {
                let v = parseFloat($(this).val());
                if (!isNaN(v) && !$(this).hasClass('is-invalid')) {
                    total += v;
                    count++;
                }
            });
            let avg = count > 0 ? (Math.round((total / count) * 100) / 100) : 0;
            $('#totalKinerjaStafText').text(avg.toString().replace('.', ','));
            
            // Dynamic UI Color for footer
            let newColor = 'success';
            if (count === 0) newColor = 'secondary';
            else if (avg < 60) newColor = 'danger';
            else if (avg < 75) newColor = 'warning';

            let textEl = $('#totalKinerjaStafText');
            let wrapper = $('#totalKinerjaStafWrapper');
            
            // Hapus kelas warna lama
            textEl.removeClass('text-success text-secondary text-danger text-warning');
            wrapper.removeClass('border-success border-secondary border-danger border-warning');
            
            // Tambahkan kelas warna baru
            textEl.addClass('text-' + newColor);
            wrapper.addClass('border-' + newColor);
        });
        
        // Trigger initialization for existing data
        $('.predikat-select').each(function() {
            if($(this).val() !== '') {
                // Set min/max quietly without erasing value
                var val = $(this).val();
                var input = $(this).siblings('.input-group').find('.input-nilai-capaian');
                if (val === 'sangat_kurang') { input.attr('min', 0).attr('max', 25); }
                else if (val === 'kurang') { input.attr('min', 25.01).attr('max', 75); }
                else if (val === 'butuh_perbaikan') { input.attr('min', 75.01).attr('max', 90); }
                else if (val === 'baik') { input.attr('min', 90.01).attr('max', 100); }
                else if (val === 'sangat_baik') { input.attr('min', 100.01).attr('max', 150); }
            }
        });

        // Handle Kosongkan form
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
                    $('.input-nilai-capaian').removeClass('is-invalid').removeAttr('title');
                    $('.invalid-feedback').hide();
                    $('#formPenilaian button[type="submit"]').prop('disabled', false);
                    
                    $('.predikat-select').val('').trigger('change');
                    
                    if ($('#totalKinerjaStafText').length) {
                        $('#totalKinerjaStafText').text('0');
                    }
                    
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

        // Track tab aktif di hidden input & sinkronisasi URL hash
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("aria-controls"); // e.g. "individu" atau "staf"
            $('#active_tab_input').val(target);
            
            // Update URL tanpa reload
            if (history.pushState) {
                history.pushState(null, null, '#' + target);
            } else {
                location.hash = '#' + target;
            }
        });

        // Buka tab sesuai hash di URL saat halaman direfresh/dimuat
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
