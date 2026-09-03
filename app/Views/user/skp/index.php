<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>
Daftar Sasaran Kinerja Pegawai
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Kelola Sasaran Kinerja Pegawai Anda di sini.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSKP">
            <i class="bi bi-plus-circle me-1"></i> Tambah SKP
        </button>
    </div>

    <?php if (empty($list_skp)) : ?>
        <div class="alert alert-secondary text-center py-4">Belum ada data SKP.</div>
    <?php else : ?>
        <?php foreach ($list_skp as $skp) : ?>
            <div class="card border-0 shadow-sm mb-3 rounded-3">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="row mb-2">
                                <div class="col-3 text-muted">Periode</div>
                                <div class="col-9 fw-bold">
                                    : <?= date('d-M-Y', strtotime($skp['periode_awal'])) ?> s.d <?= date('d-M-Y', strtotime($skp['periode_akhir'])) ?>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-3 text-muted">Pendekatan</div>
                                <div class="col-9 fw-bold text-uppercase text-primary">
                                    : <?= esc($skp['model_skp'] ?? 'Kuantitatif') ?>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-3 text-muted">Status</div>
                                <div class="col-9">
                                    : <span class="badge bg-secondary rounded-pill"><?= $skp['status'] ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <a href="<?= site_url('skp/detail/' . $skp['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-list-task me-1"></i> Detail
                                </a>
                                
                                <?php if($skp['status'] == 'Draft'): ?>
                                    <form action="<?= site_url('skp/delete/' . $skp['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus SKP Periode <?= $skp['tahun'] ?> ini? Data yang dihapus tidak dapat dikembalikan.');">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<div class="modal fade" id="modalTambahSKP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= site_url('skp/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah SKP Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <?php 
                            $thn_skr = date('Y');
                            for ($i = $thn_skr + 1; $i >= 2024; $i--) : ?>
                                <option value="<?= $i ?>" <?= $i == $thn_skr ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pendekatan / Model SKP</label>
                        <select name="model_skp" class="form-select" required>
                            <option value="Kuantitatif" selected>Kuantitatif</option>
                            <option value="Kualitatif">Kualitatif</option>
                        </select>
                        <div class="form-text">Pilih pendekatan indikator kinerja yang akan digunakan.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('error_modal')) : ?>
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Gagal Membuat SKP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="fs-5"><?= session()->getFlashdata('error_modal') ?></p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>