<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Laporan Evaluasi Diri (LED)<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Style untuk sticky footer button */
    .sticky-footer-bar {
        position: sticky;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #ffffff;
        padding: 1rem;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: flex-end;
        z-index: 10;
        /* Menyesuaikan margin negatif dari .content-area */
        margin-left: -1.5rem;
        margin-right: -1.5rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    .kriteria-row {
        border-bottom: 1px solid #eee;
    }
    .kriteria-row:last-child {
        border-bottom: none;
    }
    /* Warna untuk status persetujuan Kabag */
    .status-pending { color: #ffc107; }
    .status-approved { color: #198754; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted">Kelola data checklist Laporan Evaluasi Diri (LED) untuk setiap Program Studi.</p>
    </div>
</div>

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

<!-- ========================================================== -->
<!-- FORM 1: FILTER (METHOD GET) -->
<!-- ========================================================== -->
<div class="card mb-4">
    <div class="card-body">
        <form action="<?= site_url('ecc/led') ?>" method="GET" id="filterForm">
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


<!-- ========================================================== -->
<!-- FORM 2: SIMPAN DATA (METHOD POST) -->
<!-- ========================================================== -->
<?php 
    // Tentukan peran saat ini
    $is_staf = in_array($currentRole, ['aak', 'kuk']);
    $is_kabag = in_array($currentRole, ['kabag_aak', 'kabag_kuk']);
    $is_wadir = in_array($currentRole, ['admin', 'manajemen']);
?>

<?php if (!empty($selectedProdi) && !empty($selectedTahun)): ?>
    <form action="<?= site_url('ecc/led/store') ?>" method="POST" id="ledForm">
        <div class="card">
            <div class="card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
                <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">

                <h5 class="mb-3">Checklist untuk: <span class="text-primary"><?= esc($selectedProdi) ?> - <?= esc($selectedTahun) ?></span></h5>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;" class="text-center">Nomor</th>
                                <th>Kriteria/Elemen/Indikator</th>
                                <th style="width: 25%;">Link Lampiran (Staf)</th>
                                <th style="width: 15%;" class="text-center">Approve Kabag</th>
                                <th style="width: 15%;" class="text-center">Status (Wadir)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($all_criteria)): ?>
                                <?php foreach ($all_criteria as $criteria): 
                                    $data = $submitted_data[$criteria['id']] ?? null;
                                    $link = $data['catatan'] ?? '';
                                    $kabag_approved = $data['kabag_approved'] ?? 0;
                                    $status = $data['status'] ?? '';
                                ?>
                                <tr class="kriteria-row">
                                    <td class="text-center fw-bold"><?= esc($criteria['nomor_kriteria']) ?></td>
                                    <td>
                                        <div><?= nl2br(esc($criteria['nama_kriteria'])) ?></div>
                                        <?php if($criteria['role_assignment'] && ($is_kabag || $is_wadir)): ?>
                                            <span class="badge bg-secondary">Tugas: <?= strtoupper(esc($criteria['role_assignment'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- KOLOM LINK (Staf) -->
                                        <?php if($is_staf): // Hanya staf yang bisa edit link ?>
                                            <textarea name="catatan[<?= $criteria['id'] ?>]" class="form-control form-select-sm" rows="2" placeholder="Masukkan link Google Drive..."><?= esc($link) ?></textarea>
                                        <?php else: // Kabag & Wadir hanya bisa melihat link (jika ada) ?>
                                            <?php if (!empty($link)): ?>
                                                <a href="<?= esc($link, 'attr') ?>" target="_blank" rel="noopener noreferrer">Lihat Bukti</a>
                                            <?php else: ?>
                                                <span class="text-muted small"><em>(Belum diisi)</em></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- KOLOM APPROVAL (Kabag) -->
                                        <?php if($is_kabag): // Hanya Kabag yang bisa edit approval ?>
                                            <select name="kabag_approved[<?= $criteria['id'] ?>]" class="form-select form-select-sm" <?= (empty($link)) ? 'disabled' : '' ?>>
                                                <option value="0" <?= ($kabag_approved == 0) ? 'selected' : '' ?>>Pending</option>
                                                <option value="1" <?= ($kabag_approved == 1) ? 'selected' : '' ?>>Approved</option>
                                            </select>
                                            <?php if(empty($link)): ?><small class="text-muted">(Link kosong)</small><?php endif; ?>
                                        <?php else: // Staf & Wadir hanya bisa melihat status approval Kabag ?>
                                            <?php if ($kabag_approved == 1): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- KOLOM STATUS (Wadir) -->
                                        <?php if($is_wadir): // Hanya Wadir yang bisa edit status ?>
                                            <select name="status[<?= $criteria['id'] ?>]" class="form-select form-select-sm" <?= ($kabag_approved == 0) ? 'disabled' : '' ?>>
                                                <option value="" <?= ($status == '') ? 'selected' : '' ?>>-- Pilih --</option>
                                                <option value="Ada" <?= ($status == 'Ada') ? 'selected' : '' ?>>Ada</option>
                                                <option value="Tidak Ada" <?= ($status == 'Tidak Ada') ? 'selected' : '' ?>>Tidak Ada</option>
                                                <option value="Terlampir" <?= ($status == 'Terlampir') ? 'selected' : '' ?>>Terlampir</option>
                                            </select>
                                            <?php if($kabag_approved == 0): ?><small class="text-muted">(Tunggu Kabag)</small><?php endif; ?>
                                        <?php else: // Staf & Kabag hanya bisa melihat status Wadir ?>
                                            <?php if (!empty($status)): ?>
                                                <span class="badge bg-primary"><?= esc($status) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small"><em>(Belum diisi)</em></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        <?php if ($is_staf): ?>
                                            Belum ada Kriteria LED yang ditugaskan untuk Anda.
                                        <?php else: ?>
                                            Belum ada data Master Kriteria LED. Silakan isi di menu Master Data.
                                        <?php endif; ?>
                                    </td>
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
        <button type="button" id="submitLedForm" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ledForm = document.getElementById('ledForm');
    const submitButton = document.getElementById('submitLedForm');

    if (submitButton) {
        submitButton.addEventListener('click', function() {
            // Saat menyimpan, aktifkan semua field yang dinonaktifkan
            // agar nilainya ikut terkirim ke controller
            document.querySelectorAll('#ledForm select[disabled]').forEach(input => {
                input.disabled = false;
            });
            
            ledForm.submit();
        });
    }
});
</script>
<?= $this->endSection() ?>
