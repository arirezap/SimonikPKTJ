<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Laporan Evaluasi Diri (LED)<?= $this->endSection() ?>
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
    .kriteria-row {
        border-bottom: 1px solid #eee;
    }
    .kriteria-row:last-child {
        border-bottom: none;
    }
    /* Style untuk text area catatan review */
    .review-notes {
        font-size: 0.85rem;
    }
    .review-notes label {
        color: #555;
    }
    .review-notes textarea {
        background-color: #f8f9fa;
    }
    .review-notes textarea[readonly] {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
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


<?php 
    $is_staf = in_array($currentRole, ['aak', 'kuk', 'spm']); // SPM sekarang dianggap staf di sini
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
                    <table class="table align-middle" style="min-width: 1200px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">No</th>
                                <th>Kriteria/Elemen/Indikator</th>
                                <th style="width: 20%;" class="text-center">Link Lampiran (Staf)</th>
                                <th style="width: 25%;" class="text-center">Catatan Review (Kabag/Wadir)</th>
                                <th style="width: 12%;" class="text-center">Approve Kabag</th>
                                <th style="width: 12%;" class="text-center">Status (Wadir)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($all_criteria)): $no = 1; ?>
                                <?php foreach ($all_criteria as $criteria): 
                                    $data = $submitted_data[$criteria['id']] ?? null;
                                    $link = $data['catatan'] ?? '';
                                    $kabag_approved = $data['kabag_approved'] ?? 0;
                                    $status = $data['status'] ?? '';
                                    // Data Komentar Baru
                                    $note_kabag = $data['catatan_kabag'] ?? '';
                                    $note_wadir = $data['catatan_wadir'] ?? '';
                                ?>
                                <tr class="kriteria-row">
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td>
                                        <div><?= nl2br(esc($criteria['nama_kriteria'])) ?></div>
                                        
                                        <?php if($criteria['nama_standar']): ?>
                                            <span class="badge bg-info text-dark"><?= esc($criteria['nama_standar']) ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if($criteria['role_assignment'] && ($is_kabag || $is_wadir)): ?>
                                            <span class="badge bg-secondary">Tugas: <?= strtoupper(esc($criteria['role_assignment'])) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($is_staf): // Staf hanya bisa isi link ?>
                                            <textarea name="catatan[<?= $criteria['id'] ?>]" class="form-control form-select-sm" rows="2" placeholder="Masukkan link Google Drive..."><?= esc($link) ?></textarea>
                                        <?php else: // Peran lain hanya bisa lihat link ?>
                                            <?php if (!empty($link)): ?>
                                                <a href="<?= esc($link, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-link-45deg"></i> Lihat Bukti
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small"><em>(Belum diisi)</em></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="review-notes">
                                        <?php if($is_staf): // Staf View (Read-only) ?>
                                            <?php if(!empty($note_kabag)): ?>
                                                <div class='mb-2'>
                                                    <label class='form-label small fw-bold'>Catatan Kabag:</label>
                                                    <textarea class='form-control form-control-sm' rows='2' readonly><?= esc($note_kabag) ?></textarea>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($note_wadir)): ?>
                                                <div>
                                                    <label class='form-label small fw-bold'>Catatan Wadir:</label>
                                                    <textarea class='form-control form-control-sm' rows='2' readonly><?= esc($note_wadir) ?></textarea>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(empty($note_kabag) && empty($note_wadir)): ?>
                                                <span class="text-muted small"><em>(Belum ada catatan)</em></span>
                                            <?php endif; ?>

                                        <?php elseif($is_kabag): // Kabag View (Edit Kabag, Read Wadir) ?>
                                            <div class='mb-2'>
                                                <label class='form-label small fw-bold'>Catatan Anda (Kabag):</label>
                                                <textarea name="catatan_kabag[<?= $criteria['id'] ?>]" class="form-control form-control-sm" rows="2" placeholder="Beri masukan untuk staf..."><?= esc($note_kabag) ?></textarea>
                                            </div>
                                            <?php if(!empty($note_wadir)): ?>
                                                <div>
                                                    <label class='form-label small fw-bold'>Catatan Wadir:</label>
                                                    <textarea class='form-control form-control-sm' rows='2' readonly><?= esc($note_wadir) ?></textarea>
                                                </div>
                                            <?php endif; ?>

                                        <?php elseif($is_wadir): // Wadir/Admin View (Read Kabag, Edit Wadir) ?>
                                            <?php if(!empty($note_kabag)): ?>
                                                <div class='mb-2'>
                                                    <label class='form-label small fw-bold'>Catatan Kabag:</label>
                                                    <textarea class='form-control form-control-sm' rows='2' readonly><?= esc($note_kabag) ?></textarea>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <label class='form-label small fw-bold'>Catatan Anda (Wadir):</label>
                                                <textarea name="catatan_wadir[<?= $criteria['id'] ?>]" class="form-control form-control-sm" rows="2" placeholder="Beri masukan..."><?= esc($note_wadir) ?></textarea>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if($is_kabag): ?>
                                            <select name="kabag_approved[<?= $criteria['id'] ?>]" class="form-select form-select-sm" <?= (empty($link)) ? 'disabled' : '' ?>>
                                                <option value="0" <?= ($kabag_approved == 0) ? 'selected' : '' ?>>Pending</option>
                                                <option value="1" <?= ($kabag_approved == 1) ? 'selected' : '' ?>>Approved</option>
                                            </select>
                                            <?php if(empty($link)): ?><small class="text-muted">(Link kosong)</small><?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($kabag_approved == 1): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($is_wadir): ?>
                                            <select name="status[<?= $criteria['id'] ?>]" class="form-select form-select-sm" <?= ($kabag_approved == 0) ? 'disabled' : '' ?>>
                                                <option value="" <?= ($status == '') ? 'selected' : '' ?>>-- Pilih --</option>
                                                <option value="Ada" <?= ($status == 'Ada') ? 'selected' : '' ?>>Ada</option>
                                                <option value="Tidak Ada" <?= ($status == 'Tidak Ada') ? 'selected' : '' ?>>Tidak Ada</option>
                                                <option value="Terlampir" <?= ($status == 'Terlampir') ? 'selected' : '' ?>>Terlampir</option>
                                            </select>
                                            <?php if($kabag_approved == 0): ?><small class="text-muted">(Tunggu Kabag)</small><?php endif; ?>
                                        <?php else: ?>
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
                                    <td colspan="6" class="text-center p-4">
                                        <?php if ($is_staf): ?>
                                            Belum ada Kriteria LED yang ditugaskan untuk Anda pada prodi ini.
                                        <?php else: ?>
                                            Belum ada data Master Kriteria LED untuk prodi ini.
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
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('footer_bar') ?>
    <?php if (!empty($all_criteria) && ($is_staf || $is_kabag || $is_wadir)): // Tampilkan tombol simpan jika ada rolenya ?>
    <div class="sticky-footer-bar">
        <button type="button" id="submitLedForm" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
    </div>
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