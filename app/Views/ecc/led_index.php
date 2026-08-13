<?= $this->extend('layouts/main') ?>

<?= $this->section('page_title') ?>Laporan Evaluasi Diri (LED)<?= $this->endSection() ?>
<?= $this->section('title') ?><?= esc($page_title ?? '') ?><?= $this->endSection() ?>



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

<?php if (!empty($selectedProdi) && !empty($selectedTahun)): ?>
    <form action="<?= site_url('ecc/led/store') ?>" method="POST" id="ledForm">
        <div class="card">
            <div class="card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
                <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">

                <h5 class="mb-3">Checklist untuk: <span class="text-primary"><?= esc($selectedProdi) ?> - <?= esc($selectedTahun) ?></span></h5>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" style="min-width: 1300px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">No</th>
                                <th style="min-width: 350px;">Kriteria/Elemen/Indikator</th>
                                <th style="width: 15%;" class="text-center">Link Lampiran (Staf)</th>
                                <th style="min-width: 250px;">Catatan Review (Kabag/Wadir)</th>
                                <th style="width: 12%;" class="text-center">Approve Kabag</th>
                                <th style="width: 15%;" class="text-center">Status (Wadir)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($filtered_criteria)): $no = 1; ?>
                                <?php foreach ($filtered_criteria as $criteria): 
                                    $data = $submitted_data[$criteria['id']] ?? null;
                                    $link = $data['catatan'] ?? '';
                                    $kabag_approved = $data['kabag_approved'] ?? 0;
                                    $status = $data['status'] ?? '';
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
                                        <?php if($is_staf): ?>
                                            <textarea name="catatan[<?= $criteria['id'] ?>]" class="form-control form-control-sm" rows="2" placeholder="Masukkan link Google Drive..."><?= esc($link) ?></textarea>
                                        <?php else: ?>
                                            <?php if (!empty($link)): ?>
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <a href="<?= esc($link, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 mb-1 text-nowrap">
                                                        <i class="bi bi-link-45deg"></i> Lihat Bukti
                                                    </a>
                                                    
                                                    <?php if (($is_kabag || $is_wadir) && isset($data['id'])): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 text-nowrap" 
                                                                onclick="if(confirm('Yakin ingin menghapus link ini? \nStatus persetujuan Kabag dan Wadir akan di-reset.')) { window.location.href='<?= site_url('ecc/deleteLedLink/' . $data['id']) ?>'; }">
                                                            <i class="bi bi-trash"></i> Hapus Link
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small"><em>(Belum diisi)</em></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="review-notes">
                                        <?php if($is_staf): ?>
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

                                        <?php elseif($is_kabag): ?>
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

                                        <?php elseif($is_wadir): ?>
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
                                                <option value="0" <?= ($kabag_approved == 0) ? 'selected' : '' ?>>Belum Sesuai / Pending</option>
                                                <option value="1" <?= ($kabag_approved == 1) ? 'selected' : '' ?>>Sesuai</option>
                                            </select>
                                            <?php if(empty($link)): ?><small class="text-muted">(Link kosong)</small><?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($kabag_approved == 1): ?>
                                                <span class="badge bg-success">Sesuai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Belum Sesuai</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($is_wadir): ?>
                                            <select name="status[<?= $criteria['id'] ?>]" class="form-select form-select-sm" <?= ($kabag_approved == 0) ? 'disabled' : '' ?>>
                                                <option value="" <?= ($status == '') ? 'selected' : '' ?>>-- Pilih Status --</option>
                                                <option value="Memenuhi Standar" <?= ($status == 'Memenuhi Standar') ? 'selected' : '' ?>>Memenuhi Standar</option>
                                                <option value="Tidak Memenuhi Standar" <?= ($status == 'Tidak Memenuhi Standar') ? 'selected' : '' ?>>Tidak Memenuhi Standar</option>
                                                <option value="Perlu Revisi" <?= ($status == 'Perlu Revisi') ? 'selected' : '' ?>>Perlu Revisi</option>
                                            </select>
                                            <?php if($kabag_approved == 0): ?><small class="text-muted">(Tunggu Kabag)</small><?php endif; ?>
                                        <?php else: ?>
                                            <?php if (!empty($status)): ?>
                                                <span class="badge bg-primary"><?= esc($status) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small"><em>(Belum Dinilai)</em></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center p-4">
                                        <?php if (empty($all_criteria)): ?>
                                            Belum ada data Master Kriteria LED untuk prodi ini.
                                        <?php else: ?>
                                            Belum ada Kriteria LED yang ditugaskan untuk Anda (sebagai <?= esc(strtoupper($currentRole)) ?>) pada prodi ini.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Custom Pagination -->
                <div class="custom-pagination mt-4 d-flex justify-content-center" id="ledPaginationContainer" style="display: none !important;">
                    <div class="d-flex align-items-center bg-white shadow-sm rounded-pill px-3 py-2 border">
                        <!-- Prev -->
                        <button type="button" class="page-btn rounded-circle me-1" id="btnPrev"><i class="bi bi-chevron-left"></i></button>
                        
                        <!-- Page Numbers -->
                        <div id="pageNumbers" class="d-flex align-items-center mx-2 gap-1"></div>

                        <!-- Next -->
                        <button type="button" class="page-btn rounded-circle ms-1" id="btnNext"><i class="bi bi-chevron-right"></i></button>

                        <div class="vr mx-3"></div>

                        <!-- Items Per Page -->
                        <select id="itemsPerPage" class="form-select form-select-sm rounded-pill me-3 border-0 bg-light" style="width: 105px; cursor: pointer;">
                            <option value="10">10 / page</option>
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                        </select>

                        <!-- Go To Page -->
                        <div class="d-flex align-items-center">
                            <span class="me-2 text-muted small">Go to</span>
                            <input type="number" id="goToPage" class="form-control form-control-sm rounded-pill text-center me-2 border-primary" style="width: 60px;" min="1">
                            <span class="text-muted small">Page</span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('footer_bar') ?>
    <?php if (!empty($filtered_criteria) && ($is_staf || $is_kabag || $is_wadir)): ?>
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
            document.querySelectorAll('#ledForm select[disabled]').forEach(input => {
                input.disabled = false;
            });
            
            ledForm.submit();
        });
    }

    // --- Custom Pagination Logic ---
    const tableBody = document.querySelector(".table tbody");
    if (tableBody) {
        const rows = Array.from(tableBody.querySelectorAll("tr.kriteria-row"));
        if (rows.length > 0) {
            // Tampilkan paginator jika ada baris data
            const container = document.getElementById('ledPaginationContainer');
            if(container) container.style.setProperty('display', 'flex', 'important');
            
            let currentPage = 1;
            let rowsPerPage = 10;
            
            const btnPrev = document.getElementById("btnPrev");
            const btnNext = document.getElementById("btnNext");
            const pageNumbersContainer = document.getElementById("pageNumbers");
            const itemsPerPageSelect = document.getElementById("itemsPerPage");
            const goToPageInput = document.getElementById("goToPage");

            function renderTable() {
                const totalPages = Math.ceil(rows.length / rowsPerPage);
                if (currentPage < 1) currentPage = 1;
                if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
                
                rows.forEach((row, index) => {
                    row.style.display = "none";
                    if (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) {
                        row.style.display = "";
                    }
                });
                
                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                if(!pageNumbersContainer) return;
                pageNumbersContainer.innerHTML = "";
                
                // Menentukan range tombol (maksimal 5 halaman tampil)
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                }

                for (let i = startPage; i <= endPage; i++) {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "page-btn rounded-circle " + (i === currentPage ? "active" : "");
                    btn.textContent = i;
                    btn.addEventListener("click", () => {
                        currentPage = i;
                        if(goToPageInput) goToPageInput.value = i;
                        renderTable();
                    });
                    pageNumbersContainer.appendChild(btn);
                }

                if(btnPrev) btnPrev.disabled = currentPage === 1;
                if(btnNext) btnNext.disabled = currentPage === totalPages || totalPages === 0;
            }

            if(btnPrev) {
                btnPrev.addEventListener("click", () => {
                    if (currentPage > 1) { 
                        currentPage--; 
                        if(goToPageInput) goToPageInput.value = currentPage; 
                        renderTable(); 
                    }
                });
            }

            if(btnNext) {
                btnNext.addEventListener("click", () => {
                    const totalPages = Math.ceil(rows.length / rowsPerPage);
                    if (currentPage < totalPages) { 
                        currentPage++; 
                        if(goToPageInput) goToPageInput.value = currentPage; 
                        renderTable(); 
                    }
                });
            }

            if(itemsPerPageSelect) {
                itemsPerPageSelect.addEventListener("change", (e) => {
                    rowsPerPage = parseInt(e.target.value);
                    currentPage = 1;
                    if(goToPageInput) goToPageInput.value = 1;
                    renderTable();
                });
            }

            if(goToPageInput) {
                goToPageInput.addEventListener("change", (e) => {
                    const page = parseInt(e.target.value);
                    const totalPages = Math.ceil(rows.length / rowsPerPage);
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderTable();
                    } else {
                        e.target.value = currentPage; 
                    }
                });
            }

            if(goToPageInput) goToPageInput.value = currentPage;
            renderTable();
        }
    }
});
</script>
<?= $this->endSection() ?>