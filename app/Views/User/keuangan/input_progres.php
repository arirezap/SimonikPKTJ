<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Input Progres Keuangan') ?><?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Input Progres Keuangan
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Formulir Laporan Keuangan Bulanan</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('user/keuangan/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bulan" class="form-label">Periode Bulan</label>
                    <select name="bulan" class="form-select <?= ($validation->hasError('bulan')) ? 'is-invalid' : '' ?>" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= (old('bulan') == $i) ? 'selected' : '' ?>><?= bulan_indo($i) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tahun" class="form-label">Periode Tahun</label>
                    <select name="tahun" class="form-select <?= ($validation->hasError('tahun')) ? 'is-invalid' : '' ?>" required>
                        <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                            <option value="<?= $i ?>" <?= (old('tahun') == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-12">
                    <hr>
                    <h6><i class="bi bi-arrow-down-circle-fill text-success me-2"></i>Pendapatan</h6>
                </div>
                <div class="col-md-6">
                    <label for="total_pendapatan" class="form-label">Total Pendapatan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="total_pendapatan" class="form-control <?= ($validation->hasError('total_pendapatan')) ? 'is-invalid' : '' ?>" value="<?= old('total_pendapatan') ?>" required>
                    </div>
                </div>
                 <div class="col-md-6">
                    <label for="posisi_kas" class="form-label">Posisi Kas Akhir Bulan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="posisi_kas" class="form-control" value="<?= old('posisi_kas') ?>">
                    </div>
                </div>

                <div class="col-12">
                    <hr>
                    <h6><i class="bi bi-arrow-up-circle-fill text-danger me-2"></i>Belanja</h6>
                </div>
                <div class="col-md-6">
                    <label for="total_belanja" class="form-label">Total Belanja</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="total_belanja" class="form-control <?= ($validation->hasError('total_belanja')) ? 'is-invalid' : '' ?>" value="<?= old('total_belanja') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="belanja_pegawai" class="form-label">Belanja Pegawai</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="belanja_pegawai" class="form-control" value="<?= old('belanja_pegawai') ?>">
                    </div>
                </div>
                
                <div class="col-12">
                    <hr>
                </div>
                <div class="col-12">
                    <label for="catatan" class="form-label">Catatan / Keterangan</label>
                    <textarea name="catatan" class="form-control" rows="3"><?= old('catatan') ?></textarea>
                </div>
                <div class="col-12">
                    <label for="bukti" class="form-label">Upload Bukti (Opsional)</label>
                    <input type="file" name="bukti" class="form-control">
                    <div class="form-text">Tipe file yang diizinkan: PDF, XLSX, DOCX.</div>
                </div>
            </div>
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Simpan Laporan Keuangan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
