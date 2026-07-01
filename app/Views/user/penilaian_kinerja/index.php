<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Rekap & Penilaian Kinerja<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Rekap & Penilaian Kinerja
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
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
        <form method="POST" action="<?= site_url('penilaian-kinerja') ?>" class="mb-4 p-3 bg-light rounded border">
            <?= csrf_field() ?>
            <div class="row align-items-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label fw-bold text-primary">Pilih Bulan</label>
                    <select name="bulan" class="form-select border-primary" onchange="this.form.submit()">
                        <?php foreach($bulan_indo as $index => $nama): ?>
                            <option value="<?= $index + 1 ?>" <?= ($bulan_terpilih == $index + 1) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="form-label fw-bold text-primary">Tahun</label>
                    <input type="number" name="tahun" class="form-control border-primary" value="<?= esc($tahun_terpilih) ?>" onchange="this.form.submit()">
                </div>
                
                <?php if ($is_atasan): ?>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-success"><i class="bi bi-people-fill"></i> Pilih Bawahan (Khusus Penilai)</label>
                    <select name="bawahan_id" class="form-select border-success" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Data Saya Sendiri --</option>
                        <?php foreach($daftar_bawahan as $bawahan): ?>
                            <option value="<?= esc($bawahan['id']) ?>" <?= ($bawahan_id_terpilih == $bawahan['id']) ? 'selected' : '' ?>>
                                <?= esc($bawahan['nama_lengkap']) ?> (<?= esc($bawahan['jabatan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <?php if ($is_penilai && empty($rekap_data)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Pegawai ini belum memiliki rekap kegiatan harian pada bulan tersebut.
            </div>
        <?php elseif (empty($rekap_data)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i> Anda belum memiliki rekap kegiatan harian pada bulan ini.
            </div>
        <?php else: ?>
        
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
                                        <?php if ($is_penilai): ?>
                                            <textarea name="edit_deskripsi[]" class="form-control form-control-sm" rows="2" required><?= esc($row['deskripsi_kegiatan']) ?></textarea>
                                        <?php else: ?>
                                            <?= nl2br(esc($row['deskripsi_kegiatan'])) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= intval($row['target_bulanan']) ?> <?= esc($row['satuan']) ?></td>
                                    <td>
                                        <?php if ($is_penilai): ?>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="1" name="edit_capaian[]" class="form-control text-center" value="<?= intval($row['jumlah_capaian']) ?>" required>
                                                <span class="input-group-text"><?= esc($row['satuan']) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center"><?= intval($row['jumlah_capaian']) ?> <?= esc($row['satuan']) ?></div>
                                        <?php endif; ?>
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
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
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
    });
</script>
<?= $this->endSection() ?>
