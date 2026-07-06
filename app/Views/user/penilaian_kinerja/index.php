<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Rekap & Penilaian Kinerja
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    }
    .col-target, .col-deskripsi {
        min-width: 200px;
    }
    .col-nilai {
        min-width: 80px;
    }
    .col-bukti {
        min-width: 120px;
    }
    .form-control-sm, .form-select-sm, .input-group-sm > .input-group-text {
        font-size: 0.85rem;
    }
    .readonly-text {
        font-weight: 500;
    }
    .eval-label {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 2px;
        display: block;
    }
    .card-pegawai {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .card-pegawai:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        
        <!-- Filter Bar -->
        <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded border" id="filterForm">
            <?= csrf_field() ?>
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.9rem;">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm border-primary" onchange="this.form.submit()">
                        <?php foreach($bulan_indo as $index => $nama): ?>
                            <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary mb-1" style="font-size: 0.9rem;">Tahun</label>
                    <input type="number" name="tahun" class="form-control form-control-sm border-primary" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                </div>
                
                <?php if ($is_atasan): ?>
                
                <?php if (isset($is_super) && $is_super): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-success mb-1" style="font-size: 0.9rem;"><i class="bi bi-building"></i> Unit Kerja</label>
                    <select name="unit_kerja" id="selectUnit" class="form-select form-select-sm border-success" onchange="this.form.submit()">
                        <option value="">-- Semua Unit --</option>
                        <?php foreach($daftar_unit as $unit): ?>
                            <option value="<?= esc($unit) ?>" <?= ($unit_kerja_terpilih == $unit) ? 'selected' : '' ?>><?= esc($unit) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-success mb-1" style="font-size: 0.9rem;"><i class="bi bi-people-fill"></i> Pegawai</label>
                    <select name="bawahan_id" id="selectBawahan" class="form-select form-select-sm border-success">
                        <option value="">-- Semua Pegawai --</option>
                        <?php foreach($daftar_bawahan as $bawahan): ?>
                            <option value="<?= esc($bawahan['id']) ?>" <?= ($bawahan_id_terpilih == $bawahan['id']) ? 'selected' : '' ?>>
                                <?= esc($bawahan['nama_lengkap']) ?> <?= !empty($bawahan['jabatan']) ? '('.esc($bawahan['jabatan']).')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold" onclick="resetPencarian()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
                <?php else: ?>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success mb-1" style="font-size: 0.9rem;"><i class="bi bi-people-fill"></i> Pilih Pegawai</label>
                    <select name="bawahan_id" id="selectBawahan" class="form-select form-select-sm border-success">
                        <option value="">-- Tampilkan Data Saya Sendiri --</option>
                        <?php foreach($daftar_bawahan as $bawahan): ?>
                            <option value="<?= esc($bawahan['id']) ?>" <?= ($bawahan_id_terpilih == $bawahan['id']) ? 'selected' : '' ?>>
                                <?= esc($bawahan['nama_lengkap']) ?> <?= !empty($bawahan['jabatan']) ? '('.esc($bawahan['jabatan']).')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold" onclick="resetPencarian()">
                        <i class="bi bi-arrow-counterclockwise"></i> Kembali
                    </button>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($is_atasan): ?>
            <ul class="nav nav-tabs mb-4" id="penilaianTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-primary" id="individu-tab" data-bs-toggle="tab" data-bs-target="#individu" type="button" role="tab" aria-controls="individu" aria-selected="true">
                        <i class="bi bi-person-lines-fill me-1"></i> Penilaian Individu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-success" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">
                        <i class="bi bi-bar-chart-line-fill me-1"></i> Dashboard Rekap Pegawai
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="penilaianTabsContent">
                <!-- Tab Penilaian Individu -->
                <div class="tab-pane fade show active" id="individu" role="tabpanel" aria-labelledby="individu-tab">
        <?php endif; ?>

        <?php if ($is_penilai && empty($rekap_data)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Pegawai ini belum memiliki rekap kegiatan harian pada bulan tersebut.
            </div>
        <?php elseif (empty($rekap_data)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Anda belum memiliki rekap kegiatan harian pada bulan ini.
            </div>
        <?php else: ?>
        
        <?php
            // Hitung rata-rata nilai untuk ditampilkan di atas tabel
            $jmlDinilai = 0;
            $totalNilai = 0;
            foreach ($rekap_data as $rd) {
                if (!empty($rd['nilai_harian'])) {
                    $jmlDinilai++;
                    $totalNilai += (float)$rd['nilai_harian'];
                }
            }
            $rataRataIndividu = $jmlDinilai > 0 ? round($totalNilai / $jmlDinilai, 2) : 0;
            
            $warnaScore = 'success';
            if ($jmlDinilai == 0) {
                $warnaScore = 'secondary';
            } elseif ($rataRataIndividu < 60) {
                $warnaScore = 'danger';
            } elseif ($rataRataIndividu < 75) {
                $warnaScore = 'warning text-dark';
            }
        ?>
        
        <div class="alert alert-light border-<?= $warnaScore === 'warning text-dark' ? 'warning' : $warnaScore ?> border-start border-4 shadow-sm mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-1 text-dark">Ringkasan Kinerja Bulan Ini</h6>
                <small class="text-muted">Dari <?= count($rekap_data) ?> total aktivitas harian yang dilaporkan, <?= $jmlDinilai ?> aktivitas telah dievaluasi.</small>
            </div>
            <div class="text-end">
                <span class="d-block small fw-bold text-<?= $warnaScore ?> mb-1">RATA-RATA NILAI</span>
                <span class="fs-4 fw-bold text-<?= $warnaScore ?>"><?= number_format($rataRataIndividu, 2, ',', '.') ?></span>
            </div>
        </div>

        <!-- Chart Analytics Section dipindahkan ke Modal -->

        <!-- Form Penilaian (Jika Penilai) -->
        <?php if ($is_penilai): ?>
        <form action="<?= site_url('penilaian-kinerja/store') ?>" method="POST" id="formPenilaian">
            <?= csrf_field() ?>
            <input type="hidden" name="bulan" value="<?= esc($bulan_terpilih) ?>">
            <input type="hidden" name="tahun" value="<?= esc($tahun_terpilih) ?>">
            <input type="hidden" name="bawahan_id" value="<?= esc($bawahan_id_terpilih) ?>">
        <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th class="col-deskripsi">Aktivitas Harian</th>
                            <th class="col-target">Target Pekerjaan</th>
                            <th>Realisasi</th>
                            <th class="col-bukti">Bukti Pekerjaan</th>
                            <th style="min-width: 160px;">Evaluasi Kinerja</th>
                            <th style="min-width: 170px;">Sikap & Perilaku</th>
                            <th class="col-nilai">Nilai Harian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rekap_data as $index => $row): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td>
                                    <?php 
                                        $tgl = date('j', strtotime($row['tanggal_kegiatan']));
                                        $bln = $bulan_indo[date('n', strtotime($row['tanggal_kegiatan'])) - 1];
                                        $thn = date('Y', strtotime($row['tanggal_kegiatan']));
                                        echo $tgl . ' ' . $bln . ' ' . $thn;
                                    ?>
                                </td>
                                    <td>
                                        <?= nl2br(esc($row['deskripsi_kegiatan'])) ?>
                                    </td>
                                    <td><?= intval($row['target_bulanan']) ?> <?= esc($row['satuan']) ?></td>
                                    <td>
                                        <div class="text-center"><?= intval($row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($row['link_bukti'])): ?>
                                            <a href="<?= esc($row['link_bukti']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Bukti Pekerjaan"><i class="bi bi-link-45deg"></i> Buka</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                
                                <?php if ($is_penilai): ?>
                                    <!-- Kolom Input untuk Atasan -->
                                    <input type="hidden" name="log_id[]" value="<?= esc($row['id']) ?>">
                                    <td>
                                        <select name="waktu_penyelesaian[]" class="form-select form-select-sm mb-2" title="Waktu Penyelesaian">
                                            <option value="">- Waktu -</option>
                                            <option value="Tepat waktu" <?= ($row['waktu_penyelesaian'] == 'Tepat waktu') ? 'selected' : '' ?>>Tepat waktu</option>
                                            <option value="Terlambat" <?= ($row['waktu_penyelesaian'] == 'Terlambat') ? 'selected' : '' ?>>Terlambat</option>
                                        </select>
                                        <select name="kualitas_hasil[]" class="form-select form-select-sm" title="Kualitas Hasil">
                                            <option value="">- Kualitas -</option>
                                            <option value="Sangat Baik" <?= ($row['kualitas_hasil'] == 'Sangat Baik') ? 'selected' : '' ?>>Sangat Baik</option>
                                            <option value="Baik" <?= ($row['kualitas_hasil'] == 'Baik') ? 'selected' : '' ?>>Baik</option>
                                            <option value="Cukup" <?= ($row['kualitas_hasil'] == 'Cukup') ? 'selected' : '' ?>>Cukup</option>
                                            <option value="Kurang" <?= ($row['kualitas_hasil'] == 'Kurang') ? 'selected' : '' ?>>Kurang</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text" style="width: 85px;">Disiplin</span>
                                            <input type="number" name="disiplin[]" class="form-control text-center input-disiplin" value="<?= esc($row['disiplin']) ?>" min="0" max="100">
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text" style="width: 85px;">Kerjasama</span>
                                            <input type="number" name="kerjasama[]" class="form-control text-center input-kerjasama" value="<?= esc($row['kerjasama']) ?>" min="0" max="100">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input type="number" name="nilai_harian[]" class="form-control form-control-sm text-center input-nilai-harian fw-bold" value="<?= esc($row['nilai_harian']) ?>" readonly>
                                    </td>
                                <?php else: ?>
                                    <!-- Kolom Read-Only untuk Pegawai Biasa -->
                                    <td>
                                        <span class="eval-label">Waktu Penyelesaian:</span>
                                        <div class="mb-1 fw-bold"><?= esc($row['waktu_penyelesaian']) ?: '-' ?></div>
                                        <span class="eval-label">Kualitas Hasil:</span>
                                        <div class="fw-bold"><?= esc($row['kualitas_hasil']) ?: '-' ?></div>
                                    </td>
                                    <td>
                                        <span class="eval-label">Disiplin:</span>
                                        <div class="mb-1 fw-bold"><?= esc($row['disiplin']) ?: '-' ?></div>
                                        <span class="eval-label">Kerjasama:</span>
                                        <div class="fw-bold"><?= esc($row['kerjasama']) ?: '-' ?></div>
                                    </td>
                                    <td class="text-center align-middle readonly-text fw-bold fs-5 text-primary">
                                        <?= esc($row['nilai_harian']) ?: '-' ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php if ($is_penilai): ?>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i> Simpan Penilaian</button>
            </div>
        </form>
        <?php endif; ?>

        <?php endif; ?>
        
        <?php if ($is_atasan): ?>
                </div> <!-- End Tab Individu -->
                
                <!-- Tab Dashboard -->
                <div class="tab-pane fade" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                    <div class="row g-3">
                        <?php if (empty($rekap_dashboard)): ?>
                            <div class="col-12">
                                <div class="alert alert-info">Belum ada bawahan yang terdaftar.</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($rekap_dashboard as $rek): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm card-pegawai" data-id="<?= esc($rek['bawahan']['id']) ?>" data-nama="<?= esc($rek['bawahan']['nama_lengkap']) ?>" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold text-dark mb-1"><?= esc($rek['bawahan']['nama_lengkap']) ?></h6>
                                            <p class="card-text text-muted small mb-3"><?= esc($rek['bawahan']['jabatan']) ?></p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small fw-semibold text-secondary">Total Laporan</span>
                                                <span class="badge bg-primary rounded-pill"><?= $rek['total_laporan'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small fw-semibold text-secondary">Rata-rata Nilai</span>
                                                <?php 
                                                    $warnaDash = 'success';
                                                    if ($rek['dinilai'] == 0) $warnaDash = 'secondary';
                                                    elseif ($rek['rata_rata'] < 60) $warnaDash = 'danger';
                                                    elseif ($rek['rata_rata'] < 75) $warnaDash = 'warning text-dark';
                                                ?>
                                                <span class="badge bg-<?= $warnaDash ?> rounded-pill"><?= number_format($rek['rata_rata'], 2, ',', '.') ?></span>
                                            </div>
                                            <div class="progress mt-3" style="height: 6px;">
                                                <?php 
                                                    $pct = $rek['total_laporan'] > 0 ? ($rek['dinilai'] / $rek['total_laporan'] * 100) : 0;
                                                ?>
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-end mt-1">
                                                <small class="text-muted" style="font-size: 0.7rem;">Dinilai: <?= $rek['dinilai'] ?> / <?= $rek['total_laporan'] ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div> <!-- End Tab Dashboard -->
            </div> <!-- End Tab Content -->
        <?php endif; ?>
    </div>
</div>

<!-- Modal Chart -->
<div class="modal fade" id="chartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0 bg-light">
        <h5 class="modal-title fw-bold text-primary"><i class="bi bi-bar-chart-fill me-2"></i> Analisis Kinerja: <span id="modalPegawaiName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
          <!-- Loading Spinner -->
          <div id="chartLoading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2 text-muted">Memuat data analitik...</p>
          </div>
          <!-- Chart Analytics Section -->
          <div class="row g-3" id="chartContainer" style="display: none;">
              <div class="col-md-6">
                  <div class="card shadow-sm h-100 border-0 bg-white">
                      <div class="card-body">
                          <h6 class="card-title fw-bold text-secondary mb-3"><i class="bi bi-graph-up text-primary"></i> Tren Nilai 6 Bulan Terakhir</h6>
                          <div style="height: 250px;">
                              <canvas id="trendChart"></canvas>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="card shadow-sm h-100 border-0 bg-white">
                      <div class="card-body">
                          <h6 class="card-title fw-bold text-secondary mb-3"><i class="bi bi-pie-chart text-success"></i> Kualitas & Ketepatan Waktu (Bulan Ini)</h6>
                          <div style="height: 250px;">
                              <canvas id="qualityChart"></canvas>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="card shadow-sm h-100 border-0 bg-white">
                      <div class="card-body">
                          <h6 class="card-title fw-bold text-secondary mb-3"><i class="bi bi-heptagon text-warning"></i> Disiplin vs Kerjasama (Bulan Ini)</h6>
                          <div style="height: 250px;">
                              <canvas id="sikapChart"></canvas>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="card shadow-sm h-100 border-0 bg-white">
                      <div class="card-body">
                          <h6 class="card-title fw-bold text-secondary mb-3"><i class="bi bi-bar-chart-steps text-info"></i> Capaian Realisasi Pekerjaan (Bulan Ini)</h6>
                          <div style="height: 250px;">
                              <canvas id="targetChart"></canvas>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trendChartInstance = null;
    let qualityChartInstance = null;
    let sikapChartInstance = null;
    let targetChartInstance = null;

    const bulanTerpilih = <?= esc($bulan_terpilih) ?>;
    const tahunTerpilih = <?= esc($tahun_terpilih) ?>;

    $(document).ready(function() {
        // Inisialisasi Select2
        if ($('#selectBawahan').length) {
            $('#selectBawahan').select2({ 
                width: '100%', 
                placeholder: "Cari Nama...",
                allowClear: false // Matikan clear kecil karena sudah ada tombol besar
            });
            $('#selectBawahan').on('select2:select', function (e) { $(this).closest('form').submit(); });
        }
        
        // Klik pada Kartu Pegawai
        $('.card-pegawai').on('click', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('nama');
            
            $('#modalPegawaiName').text(userName);
            $('#chartLoading').show();
            $('#chartContainer').hide();
            
            const chartModal = new bootstrap.Modal(document.getElementById('chartModal'));
            chartModal.show();
            
            $.ajax({
                url: '<?= site_url('penilaian-kinerja/api-chart') ?>',
                method: 'GET',
                data: { user_id: userId, bulan: bulanTerpilih, tahun: tahunTerpilih },
                success: function(res) {
                    $('#chartLoading').hide();
                    $('#chartContainer').show();
                    renderCharts(res);
                },
                error: function() {
                    $('#chartLoading').hide();
                    alert('Gagal memuat data grafik.');
                }
            });
        });
        
        function renderCharts(data) {
            // Hancurkan instance chart lama jika ada
            if(trendChartInstance) trendChartInstance.destroy();
            if(qualityChartInstance) qualityChartInstance.destroy();
            if(sikapChartInstance) sikapChartInstance.destroy();
            if(targetChartInstance) targetChartInstance.destroy();

            // 1. Trend Chart
            trendChartInstance = new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: { labels: data.trend_labels, datasets: [{ label: 'Rata-rata Nilai', data: data.trend_data, borderColor: '#0d6efd', tension: 0.3, fill: true, backgroundColor: 'rgba(13, 110, 253, 0.1)' }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { min: 0, max: 100 } }, plugins: { legend: { display: false } } }
            });

            // 2. Quality Chart
            qualityChartInstance = new Chart(document.getElementById('qualityChart'), {
                type: 'doughnut',
                data: { labels: ['Tepat Waktu', 'Terlambat'], datasets: [{ data: [data.kualitas.tepat, data.kualitas.lambat], backgroundColor: ['#198754', '#dc3545'] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });

            // 3. Sikap Chart
            sikapChartInstance = new Chart(document.getElementById('sikapChart'), {
                type: 'bar',
                data: { labels: ['Disiplin', 'Kerjasama'], datasets: [{ label: 'Rata-rata', data: [data.sikap.disiplin, data.sikap.kerjasama], backgroundColor: ['#ffc107', '#0dcaf0'] }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { min: 0, max: 100 } }, plugins: { legend: { display: false } } }
            });

            // 4. Target Chart
            targetChartInstance = new Chart(document.getElementById('targetChart'), {
                type: 'bar',
                data: {
                    labels: ['Produktivitas'],
                    datasets: [
                        { label: 'Realisasi', data: [data.produktivitas.realisasi], backgroundColor: '#198754' },
                        { label: 'Target Tersisa', data: [data.produktivitas.sisa], backgroundColor: '#e9ecef' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', scales: { x: { stacked: true }, y: { stacked: true } } }
            });
        }

        // Otomatis hitung Nilai Harian saat Disiplin atau Kerjasama diubah
        $('.input-disiplin, .input-kerjasama').on('input', function() {
            var row = $(this).closest('tr');
            var disiplin = parseFloat(row.find('.input-disiplin').val()) || 0;
            var kerjasama = parseFloat(row.find('.input-kerjasama').val()) || 0;
            
            if (disiplin > 0 || kerjasama > 0) {
                var avg = Math.round((disiplin + kerjasama) / 2);
                row.find('.input-nilai-harian').val(avg);
            } else {
                row.find('.input-nilai-harian').val('');
            }
        });
        
        // Fungsi Reset Pencarian
        window.resetPencarian = function() {
            if ($('#selectUnit').length) {
                $('#selectUnit').val('');
            }
            if ($('#selectBawahan').length) {
                $('#selectBawahan').val(''); // Kosongkan nilai tanpa mengubah DOM optionnya
            }
            $('#filterForm').submit();
        };
    });
</script>
<?= $this->endSection() ?>
