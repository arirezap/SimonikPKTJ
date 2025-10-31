<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Simulasi Penilaian LED<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
        color: #0d6efd;
        font-weight: bold;
    }
    .tab-content {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-top: 0;
        border-radius: 0 0.375rem 0.375rem 0.375rem;
    }
    .kriteria-row { border-bottom: 1px solid #eee; }
    .kriteria-row:last-child { border-bottom: none; }
    
    .sticky-footer-bar {
        position: sticky; bottom: 0; left: 0; width: 100%;
        background-color: #ffffff; padding: 1rem;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        display: flex; justify-content: flex-end; z-index: 10;
        margin-left: -1.5rem; margin-right: -1.5rem;
        padding-left: 1.5rem; padding-right: 1.5rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<p class="text-muted">Input nilai/skor (0-100) untuk setiap kriteria. Skor ini akan digunakan untuk menghasilkan diagram laba-laba di Dashboard ECC.</p>

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

<!-- FORM FILTER (GET) -->
<div class="card mb-4">
    <div class="card-body">
        <form action="<?= site_url('ecc/simulasi') ?>" method="GET" id="filterForm">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="tahun_filter" class="form-label fw-bold">Pilih Tahun</label>
                    <select name="tahun" id="tahun_filter" class="form-select">
                        <?php for ($i = date("Y"); $i >= date("Y") - 5; $i--): ?>
                            <option value="<?= $i; ?>" <?= ($selectedTahun == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="prodi_filter" class="form-label fw-bold">Pilih Program Studi</label>
                    <select name="prodi" id="prodi_filter" class="form-select">
                        <?php $prodiList = ['RSTJ', 'TRO', 'TO']; ?>
                        <?php foreach($prodiList as $prodi): ?>
                            <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>><?= $prodi; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- FORM SIMPAN (POST) -->
<form action="<?= site_url('ecc/simulasi/store') ?>" method="POST" id="simulasiForm">
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
            <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">

            <h5 class="mb-3">Input Skor untuk: <span class="text-primary"><?= esc($selectedProdi) ?> - <?= esc($selectedTahun) ?></span></h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;" class="text-center">Nomor</th>
                            <th>Kriteria/Elemen/Indikator</th>
                            <th style="width: 15%;">Kategori</th>
                            <th style="width: 20%;">Bukti & Status</th> <!-- KOLOM BARU -->
                            <th style="width: 15%;" class="text-center">Skor (0-100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_criteria)): ?>
                            <?php foreach ($all_criteria as $criteria): 
                                $score_data = $submitted_scores[$criteria['id']] ?? null;
                                $submission_data = $submitted_submissions[$criteria['id']] ?? null; // AMBIL DATA SUBMISSION
                            ?>
                            <tr class="kriteria-row">
                                <td class="text-center fw-bold"><?= esc($criteria['nomor_kriteria']) ?></td>
                                <td><?= nl2br(esc($criteria['nama_kriteria'])) ?></td>
                                <td><?= esc($criteria['kategori']) ?></td>
                                <td>
                                    <!-- TAMPILKAN LINK BUKTI -->
                                    <?php if (!empty($submission_data['catatan'])): ?>
                                        <a href="<?= esc($submission_data['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm mb-2 w-100">
                                            <i class="bi bi-link-45deg"></i> Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary mb-2 w-100">Bukti Belum Ada</span>
                                    <?php endif; ?>

                                    <!-- TAMPILKAN STATUS -->
                                    <div class="d-flex justify-content-around small">
                                        <span>Kabag: 
                                            <?php if ($submission_data['kabag_approved'] ?? 0 == 1): ?>
                                                <span class="badge bg-success">OK</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </span>
                                        <span>Wadir: <span class="badge bg-info text-dark"><?= esc($submission_data['status'] ?? 'N/A') ?></span></span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" min="0" max="100" name="skor[<?= $criteria['id'] ?>]" class="form-control text-center" value="<?= esc($score_data['skor'] ?? '0') ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data Master Kriteria LED.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<!-- Sticky Footer untuk Tombol Simpan -->
<?php if (!empty($all_criteria)): ?>
<div class="sticky-footer-bar">
    <button type="button" id="submitSimulasiForm" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Skor</button>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const simulasiForm = document.getElementById('simulasiForm');
    const submitButton = document.getElementById('submitSimulasiForm');

    if (submitButton) {
        submitButton.addEventListener('click', function() {
            simulasiForm.submit();
        });
    }
});
</script>
<?= $this->endSection() ?>
