<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Simulasi Penilaian LED<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .sticky-footer-bar {
        position: sticky;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ffffff;
        padding: 1rem 1.5rem; /* Sesuaikan padding */
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: flex-end;
        z-index: 10;
    }
    .kriteria-row { border-bottom: 1px solid #eee; }
    .kriteria-row:last-child { border-bottom: none; }
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

<form action="<?= site_url('ecc/simulasi/store') ?>" method="POST" id="simulasiForm">
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
            <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">

            <h5 class="mb-3">Input Skor untuk: <span class="text-primary"><?= esc($selectedProdi) ?> - <?= esc($selectedTahun) ?></span></h5>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" style="min-width: 1100px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="min-width: 350px;">Kriteria/Elemen/Indikator</th>
                            <th style="width: 15%;">Standar</th>
                            <th style="width: 20%;" class="text-center">Bukti & Status</th>
                            <th style="width: 12%;" class="text-center">Skor (0-100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_criteria)): $no = 1; ?>
                            <?php foreach ($all_criteria as $criteria): 
                                $score_data = $submitted_scores[$criteria['id']] ?? null;
                                $submission_data = $submitted_submissions[$criteria['id']] ?? null;
                            ?>
                            <tr class="kriteria-row">
                                <td class="text-center fw-bold"><?= $no++ ?></td>
                                <td><?= nl2br(esc($criteria['nama_kriteria'])) ?></td>
                                <td><?= esc($criteria['nama_standar']) ?></td>
                                <td class="text-center"> <?php if (!empty($submission_data['catatan'])): ?>
                                        <a href="<?= esc($submission_data['catatan'], 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm mb-2 w-100">
                                            <i class="bi bi-link-45deg"></i> Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary mb-2 w-100">Bukti Belum Ada</span>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-around small">
                                        <span>Kabag: 
                                            <?php if ($submission_data['kabag_approved'] ?? 0 == 1): ?>
                                                <span class="badge bg-success">Sesuai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Belum Sesuai</span>
                                            <?php endif; ?>
                                        </span>
                                        <span>Wadir: <span class="badge bg-info text-dark"><?= esc($submission_data['status'] ?? 'Belum Dinilai') ?></span></span>
                                    </div>
                                </td>
                                <?php
                                    // Cek apakah bukti sudah ada DAN disetujui Kabag DAN sudah direview Wadir
                                    $is_approved = (!empty($submission_data['catatan']) && ($submission_data['kabag_approved'] ?? 0) == 1 && !empty($submission_data['status']));
                                ?>
                                <td>
                                    <input type="number" min="0" max="100" name="skor[<?= $criteria['id'] ?>]" class="form-control text-center <?= !$is_approved ? 'bg-light' : '' ?>" value="<?= esc($score_data['skor'] ?? '0') ?>" <?= !$is_approved ? 'readonly' : '' ?>>
                                    <?php if (!$is_approved): ?>
                                        <div class="text-danger mt-1 text-center" style="font-size: 0.75rem;"><i class="bi bi-lock-fill"></i> Belum Disetujui</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data Master Kriteria LED untuk prodi <?= esc($selectedProdi) ?>.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('footer_bar') ?>
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