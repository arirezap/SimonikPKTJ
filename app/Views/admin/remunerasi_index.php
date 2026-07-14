<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Input Remunerasi Pegawai<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? 'Input Remunerasi') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="bulan" class="form-label fw-bold">Pilih Bulan</label>
                <select class="form-select" id="bulan" name="bulan">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i; ?>" <?= ($selectedBulan == $i) ? 'selected' : ''; ?>>
                            <?= bulan_indo($i) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="tahun" class="form-label fw-bold">Pilih Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    <?php for ($i = date('Y'); $i >= date('Y') - 2; $i--): ?>
                        <option value="<?= $i; ?>" <?= ($selectedTahun == $i) ? 'selected' : ''; ?>>
                            <?= $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Formulir Remunerasi: <?= bulan_indo($selectedBulan) ?> <?= esc($selectedTahun) ?></h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('remunerasi/store') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
            <input type="hidden" name="bulan" value="<?= esc($selectedBulan) ?>">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Nama Pegawai</th>
                            <th>Role</th>
                            <th style="width: 30%;">Jumlah Remunerasi (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pegawai_list)): ?>
                            <?php $no = 1; foreach ($pegawai_list as $pegawai): ?>
                                <?php
                                    // Ambil jumlah yang ada jika ada, format sebagai angka
                                    $jumlah = $remun_map[$pegawai['id']] ?? 0;
                                    $formatted_jumlah = number_format($jumlah, 0, ',', '.');
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= esc($pegawai['nama_lengkap']) ?></div>
                                        <small class="text-muted"><?= esc($pegawai['username']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= strtoupper(esc($pegawai['role'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   name="jumlah[<?= $pegawai['id'] ?>]" 
                                                   value="<?= ($jumlah > 0) ? $formatted_jumlah : '' ?>"
                                                   placeholder="0"
                                                   onkeyup="formatRupiah(this)">
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center p-4">
                                    Tidak ada data pegawai (user) dengan role 'aak', 'kuk', atau 'spm'.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($pegawai_list)): ?>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Data Remunerasi</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Fungsi simple untuk format rupiah saat diketik
    function formatRupiah(input) {
        // Ambil nilai, hapus semua selain angka
        let value = input.value.replace(/[^0-9]/g, '');
        
        // Format dengan titik
        if (value) {
            value = parseInt(value, 10).toLocaleString('id-ID');
        }
        
        // Set nilai kembali ke input
        input.value = value;
    }

    // Terapkan format saat halaman dimuat ke field yang sudah ada isinya
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name^="jumlah["]').forEach(function(input) {
            if (input.value) {
                formatRupiah(input);
            }
        });
    });
</script>
<?= $this->endSection() ?>