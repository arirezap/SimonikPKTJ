<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Laporan Evaluasi Diri (LED)') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold text-dark mb-0" style="letter-spacing: -0.02em;">Laporan Evaluasi Diri (LED)</h4>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- 2. FILTER PERIODE & PRODI (BENTO CARD) -->
<div class="bento-card shadow-sm mb-4">
    <div class="bento-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="bg-primary-bento text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-funnel-fill fs-6"></i>
            </div>
            <span class="fw-bold text-dark">Filter Instrumen LED</span>
        </div>
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Pilih prodi dan tahun evaluasi</span>
    </div>
    <div class="bento-body p-4">
        <form action="<?= site_url('ecc/led') ?>" method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="tahun_filter" class="form-label fw-bold text-dark small">Pilih Tahun Anggaran</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar3 text-muted"></i></span>
                        <select name="tahun" id="tahun_filter" class="form-select border-start-0 ps-0 fw-medium">
                            <?php for ($i = date("Y"); $i >= date("Y") - 5; $i--): ?>
                                <option value="<?= $i; ?>" <?= ($selectedTahun == $i) ? 'selected' : ''; ?>>Tahun <?= $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="prodi_filter" class="form-label fw-bold text-dark small">Pilih Program Studi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-mortarboard text-muted"></i></span>
                        <select name="prodi" id="prodi_filter" class="form-select border-start-0 ps-0 fw-bold text-primary-bento">
                            <?php foreach($prodiList as $prodi): ?>
                                <option value="<?= $prodi; ?>" <?= ($selectedProdi == $prodi) ? 'selected' : ''; ?>>PRODI <?= strtoupper(esc($prodi)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm fw-medium">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 3. LIVE SEARCH & QUICK STATUS FILTER (BENTO STYLE) -->
<?php if (!empty($selectedProdi) && !empty($selectedTahun) && !empty($filtered_criteria)): ?>
<div class="bento-card shadow-sm mb-4">
    <div class="bento-body p-3">
        <div class="row g-3 align-items-center">
            <!-- Search Box -->
            <div class="col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                        <i class="bi bi-search text-primary"></i>
                    </span>
                    <input type="text" 
                           id="ledSearchInput" 
                           class="form-control border-start-0 border-end-0 bg-light" 
                           placeholder="Cari kriteria, nama standar, kata kunci...">
                    <button class="btn btn-light border-start-0 rounded-end-pill pe-3 text-muted" type="button" id="btnClearSearch" title="Hapus pencarian">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            </div>

            <!-- Quick Status Filters -->
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-1 flex-wrap justify-content-lg-end" id="quickFilterContainer">
                    <span class="text-muted small me-1 d-none d-xl-inline"><i class="bi bi-funnel me-1"></i> Filter Cepat:</span>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-1 quick-filter active" data-filter="all">
                        Semua (<span id="countAll"><?= count($filtered_criteria) ?></span>)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 quick-filter" data-filter="unuploaded">
                        <i class="bi bi-dash-circle me-1 text-danger"></i> Belum Diunggah
                    </button>
                    <?php if ($is_kabag || $is_wadir): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 quick-filter" data-filter="pending-kabag">
                            <i class="bi bi-clock-history me-1 text-warning"></i> Butuh Review Kabag
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 quick-filter" data-filter="approved-kabag">
                            <i class="bi bi-check-circle me-1 text-success"></i> Sesuai Kabag
                        </button>
                    <?php endif; ?>
                    <?php if ($is_wadir): ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 quick-filter" data-filter="revisi">
                            <i class="bi bi-exclamation-triangle me-1 text-warning"></i> Perlu Revisi
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 4. TABEL CHECKLIST LED (HIGH DENSITY BENTO TABLE) -->
<?php if (!empty($selectedProdi) && !empty($selectedTahun)): ?>
    <form action="<?= site_url('ecc/led/store') ?>" method="POST" id="ledForm">
        <?= csrf_field() ?>
        <input type="hidden" name="tahun" value="<?= esc($selectedTahun) ?>">
        <input type="hidden" name="prodi" value="<?= esc($selectedProdi) ?>">

        <div class="bento-card shadow-sm mb-4">
            <div class="bento-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded p-1 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="bi bi-check2-square fs-6"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark">Checklist Instrumen LED:</span>
                        <span class="text-primary-bento fw-bold">PRODI <?= strtoupper(esc($selectedProdi)) ?> &bull; <?= esc($selectedTahun) ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 shadow-sm fw-medium" id="btnToggleAllKriteria">
                        <i class="bi bi-arrows-expand me-1"></i> <span id="toggleAllText">Buka Semua Rincian</span>
                    </button>
                    <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill" id="counterBadge">
                        Menampilkan <strong class="text-primary-bento" id="visibleCount"><?= count($filtered_criteria ?? []) ?></strong> dari <?= count($filtered_criteria ?? []) ?> Kriteria
                    </span>
                </div>
            </div>
            <div class="bento-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 border-0" style="min-width: 1200px;">
                        <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                            <tr>
                                <th style="width: 50px;" class="text-center ps-3 py-3 border-0 rounded-top-start">No</th>
                                <th style="min-width: 380px;" class="py-3 border-0">Kriteria / Elemen / Indikator</th>
                                <th style="width: 200px;" class="text-center py-3 border-0">Link Bukti Fisik (Staf)</th>
                                <th style="min-width: 260px;" class="py-3 border-0">Catatan Review (Kabag / Wadir)</th>
                                <th style="width: 150px;" class="text-center py-3 border-0">Approval Kabag</th>
                                <th style="width: 180px;" class="text-center pe-3 py-3 border-0 rounded-top-end">Status (Wadir/SPM)</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php if (!empty($filtered_criteria)): $no = 1; ?>
                                <?php foreach ($filtered_criteria as $criteria): 
                                    $data = $submitted_data[$criteria['id']] ?? null;
                                    $link = $data['catatan'] ?? '';
                                    $kabag_approved = $data['kabag_approved'] ?? 0;
                                    $status = $data['status'] ?? '';
                                    $note_kabag = $data['catatan_kabag'] ?? '';
                                    $note_wadir = $data['catatan_wadir'] ?? '';

                                    // Pisahkan judul kriteria dan rincian deskripsi
                                    $rawKriteria = trim($criteria['nama_kriteria'] ?? '');
                                    $parts = explode("\n", $rawKriteria, 2);
                                    $kriteriaTitle = trim($parts[0]);
                                    $kriteriaBody = isset($parts[1]) ? trim($parts[1]) : '';
                                    $hasBody = !empty($kriteriaBody);
                                    $critId = $criteria['id'];
                                ?>
                                <tr class="kriteria-row"
                                    data-kriteria="<?= strtolower(esc($rawKriteria)) ?>"
                                    data-standar="<?= strtolower(esc($criteria['nama_standar'] ?? '')) ?>"
                                    data-role="<?= strtolower(esc($criteria['role_assignment'] ?? '')) ?>"
                                    data-has-link="<?= !empty(trim($link)) ? '1' : '0' ?>"
                                    data-kabag-approved="<?= (int)$kabag_approved ?>"
                                    data-status="<?= strtolower(esc($status)) ?>">
                                    <td class="text-center fw-bold text-muted ps-3 py-3 border-bottom border-light row-number align-top pt-4">
                                        <?= $no++ ?>
                                    </td>
                                    <td class="py-3 border-bottom border-light align-top">
                                        <!-- Judul Kriteria (Bold & Clean) -->
                                        <div class="fw-bold text-dark mb-1 kriteria-title" style="font-size: 0.95rem; line-height: 1.4;">
                                            <?= esc($kriteriaTitle) ?>
                                        </div>
                                        
                                        <!-- Badges Kategori / Standar & Role Assignment -->
                                        <div class="d-flex align-items-center gap-1 flex-wrap mb-2">
                                            <?php if(!empty($criteria['nama_standar'])): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                                    <i class="bi bi-tag-fill me-1"></i> <?= esc($criteria['nama_standar']) ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($criteria['role_assignment']) && ($is_kabag || $is_wadir)): ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                                    Tugas: <?= strtoupper(esc($criteria['role_assignment'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($hasBody): ?>
                                            <!-- Collapsed Preview (2 lines clamp) -->
                                            <div class="kriteria-preview text-muted small mb-1" id="preview-<?= $critId ?>" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;">
                                                <?= nl2br(esc($kriteriaBody)) ?>
                                            </div>

                                            <!-- Expanded Full Description Container -->
                                            <div class="collapse mb-2 kriteria-collapse" id="collapseKriteria-<?= $critId ?>">
                                                <div class="p-3 bg-light-subtle rounded-3 border text-secondary small" style="line-height: 1.6; border-left: 3px solid #1e40af !important; background-color: #f8fafc;">
                                                    <?= nl2br(esc($kriteriaBody)) ?>
                                                </div>
                                            </div>

                                            <!-- Toggle Accordion Button -->
                                            <button type="button" 
                                                    class="btn btn-link btn-sm p-0 text-primary-bento text-decoration-none fw-semibold d-inline-flex align-items-center btn-toggle-kriteria" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapseKriteria-<?= $critId ?>" 
                                                    data-preview-id="preview-<?= $critId ?>"
                                                    aria-expanded="false">
                                                <i class="bi bi-chevron-down me-1 toggle-icon"></i> 
                                                <span class="toggle-text">Lihat Rincian Aspek</span>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center py-3 border-bottom border-light align-top">
                                        <?php if($is_staf): ?>
                                            <textarea name="catatan[<?= $criteria['id'] ?>]" class="form-control form-control-sm rounded-3 shadow-none border-primary-subtle" rows="3" placeholder="Tempel link Google Drive / tautan bukti..."><?= esc($link) ?></textarea>
                                        <?php else: ?>
                                            <?php if (!empty($link)): ?>
                                                <div class="d-flex flex-column align-items-center gap-1">
                                                    <a href="<?= esc($link, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill w-100 shadow-sm text-nowrap fw-medium">
                                                        <i class="bi bi-link-45deg me-1"></i> Buka Bukti
                                                    </a>
                                                    
                                                    <?php if (($is_kabag || $is_wadir) && isset($data['id'])): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill w-100 shadow-sm text-nowrap mt-1 btn-delete-link" 
                                                                data-id="<?= $data['id'] ?>"
                                                                data-url="<?= site_url('ecc/deleteLedLink/' . $data['id']) ?>">
                                                            <i class="bi bi-trash me-1"></i> Hapus Link
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                    <i class="bi bi-dash me-1"></i> Belum diunggah
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="py-3 border-bottom border-light align-top" style="font-size: 0.85rem;">
                                        <?php if($is_staf): ?>
                                            <?php if(!empty($note_kabag)): ?>
                                                <div class="mb-2 p-2 bg-light rounded-3 border">
                                                    <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-primary"></i> Catatan Kabag:</strong>
                                                    <div class="text-secondary mt-1"><?= nl2br(esc($note_kabag)) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($note_wadir)): ?>
                                                <div class="p-2 bg-light rounded-3 border">
                                                    <strong class="text-dark"><i class="bi bi-chat-left-text me-1 text-info"></i> Catatan Wadir:</strong>
                                                    <div class="text-secondary mt-1"><?= nl2br(esc($note_wadir)) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(empty($note_kabag) && empty($note_wadir)): ?>
                                                <span class="text-muted small"><em>(Belum ada catatan review)</em></span>
                                            <?php endif; ?>

                                        <?php elseif($is_kabag): ?>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold text-dark mb-1">Catatan Anda (Kabag):</label>
                                                <textarea name="catatan_kabag[<?= $criteria['id'] ?>]" class="form-control form-control-sm rounded-3" rows="2" placeholder="Beri masukan review untuk staf..."><?= esc($note_kabag) ?></textarea>
                                            </div>
                                            <?php if(!empty($note_wadir)): ?>
                                                <div class="p-2 bg-light rounded-3 border">
                                                    <strong class="text-dark small"><i class="bi bi-chat-left-text me-1 text-info"></i> Catatan Wadir:</strong>
                                                    <div class="text-secondary small mt-1"><?= nl2br(esc($note_wadir)) ?></div>
                                                </div>
                                            <?php endif; ?>

                                        <?php elseif($is_wadir): ?>
                                            <?php if(!empty($note_kabag)): ?>
                                                <div class="mb-2 p-2 bg-light rounded-3 border">
                                                    <strong class="text-dark small"><i class="bi bi-chat-left-text me-1 text-primary"></i> Catatan Kabag:</strong>
                                                    <div class="text-secondary small mt-1"><?= nl2br(esc($note_kabag)) ?></div>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <label class="form-label small fw-bold text-dark mb-1">Catatan Anda (Wadir/SPM):</label>
                                                <textarea name="catatan_wadir[<?= $criteria['id'] ?>]" class="form-control form-control-sm rounded-3" rows="2" placeholder="Beri instruksi penilaian akhir..."><?= esc($note_wadir) ?></textarea>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center py-3 border-bottom border-light align-top">
                                        <?php if($is_kabag): ?>
                                            <select name="kabag_approved[<?= $criteria['id'] ?>]" class="form-select form-select-sm rounded-pill fw-medium" <?= (empty($link)) ? 'disabled' : '' ?>>
                                                <option value="0" <?= ($kabag_approved == 0) ? 'selected' : '' ?>>Belum Sesuai / Pending</option>
                                                <option value="1" <?= ($kabag_approved == 1) ? 'selected' : '' ?>>Sesuai</option>
                                            </select>
                                            <?php if(empty($link)): ?><small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(Link kosong)</small><?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($kabag_approved == 1): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Sesuai
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                                    <i class="bi bi-clock-history me-1"></i> Belum Sesuai
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-3 py-3 border-bottom border-light align-top">
                                        <?php if($is_wadir): ?>
                                            <select name="status[<?= $criteria['id'] ?>]" class="form-select form-select-sm rounded-pill fw-medium" <?= ($kabag_approved == 0) ? 'disabled' : '' ?>>
                                                <option value="" <?= ($status == '') ? 'selected' : '' ?>>-- Pilih Status --</option>
                                                <option value="Memenuhi Standar" <?= ($status == 'Memenuhi Standar') ? 'selected' : '' ?>>Memenuhi Standar</option>
                                                <option value="Tidak Memenuhi Standar" <?= ($status == 'Tidak Memenuhi Standar') ? 'selected' : '' ?>>Tidak Memenuhi Standar</option>
                                                <option value="Perlu Revisi" <?= ($status == 'Perlu Revisi') ? 'selected' : '' ?>>Perlu Revisi</option>
                                            </select>
                                            <?php if($kabag_approved == 0): ?><small class="text-muted d-block mt-1" style="font-size: 0.7rem;">(Tunggu Kabag)</small><?php endif; ?>
                                        <?php else: ?>
                                            <?php if (!empty($status)): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                                                    <?= esc($status) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                                                    Belum Dinilai
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center p-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
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

                <!-- No Search Results Row -->
                <div id="noSearchRow" class="p-5 text-center text-muted" style="display: none;">
                    <i class="bi bi-search fs-1 d-block mb-2 text-muted opacity-50"></i>
                    <h6 class="fw-bold text-dark">Tidak ada kriteria yang sesuai</h6>
                    <p class="small text-muted mb-0">Coba gunakan kata kunci pencarian lain atau pilih filter status yang berbeda.</p>
                </div>
                
                <!-- Custom Pagination -->
                <div class="custom-pagination p-4 d-flex justify-content-center border-top" id="ledPaginationContainer" style="display: none !important;">
                    <div class="d-flex align-items-center bg-white shadow-sm rounded-pill px-3 py-2 border flex-wrap gap-2 justify-content-center">
                        <!-- Prev -->
                        <button type="button" class="page-btn rounded-circle me-1" id="btnPrev"><i class="bi bi-chevron-left"></i></button>
                        
                        <!-- Page Numbers -->
                        <div id="pageNumbers" class="d-flex align-items-center mx-2 gap-1"></div>

                        <!-- Next -->
                        <button type="button" class="page-btn rounded-circle ms-1" id="btnNext"><i class="bi bi-chevron-right"></i></button>

                        <div class="vr mx-2 d-none d-sm-block"></div>

                        <!-- Items Per Page -->
                        <select id="itemsPerPage" class="form-select form-select-sm rounded-pill me-2 border-0 bg-light" style="width: 110px; cursor: pointer;">
                            <option value="10">10 / hal</option>
                            <option value="25">25 / hal</option>
                            <option value="50">50 / hal</option>
                            <option value="100">100 / hal</option>
                        </select>

                        <!-- Go To Page -->
                        <div class="d-flex align-items-center">
                            <span class="me-2 text-muted small">Ke Hal</span>
                            <input type="number" id="goToPage" class="form-control form-control-sm rounded-pill text-center me-1 border-primary" style="width: 60px;" min="1">
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
    <div class="sticky-footer-bar bg-white border-top shadow-lg py-3 px-4 d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1 text-primary"></i> Pastikan seluruh perubahan link & catatan review telah terisi sebelum menyimpan.
        </span>
        <button type="button" id="submitLedForm" class="btn btn-primary rounded-pill px-4 py-2 shadow ms-auto fw-bold">
            <i class="bi bi-save me-2"></i> Simpan Perubahan LED
        </button>
    </div>
    <?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ledForm = document.getElementById('ledForm');
    const submitButton = document.getElementById('submitLedForm');

    // Handler Hapus Link Bukti (POST dengan CSRF)
    document.querySelectorAll('.btn-delete-link').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const deleteUrl = this.getAttribute('data-url');
            
            function doDelete() {
                let form = document.getElementById('formHapusLedLink');
                if (!form) {
                    form = document.createElement('form');
                    form.id = 'formHapusLedLink';
                    form.method = 'POST';
                    form.style.display = 'none';

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '<?= csrf_token() ?>';
                    csrfInput.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                }
                form.action = deleteUrl;
                form.submit();
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Link Bukti?',
                    text: 'Status persetujuan Kabag dan Wadir untuk item ini akan di-reset.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm('Yakin ingin menghapus link ini? Status persetujuan Kabag dan Wadir akan di-reset.')) {
                    doDelete();
                }
            }
        });
    });

    // Handler Simpan Form
    if (submitButton && ledForm) {
        submitButton.addEventListener('click', function() {
            // Nonaktifkan proteksi disabled sementara agar nilai tetap ter-submit
            document.querySelectorAll('#ledForm select[disabled]').forEach(input => {
                input.disabled = false;
            });
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...';
            
            ledForm.submit();
        });
    }

    // --- Search & Filtering & Custom Pagination Logic ---
    const tableBody = document.querySelector(".table tbody");
    if (tableBody) {
        const allRows = Array.from(tableBody.querySelectorAll("tr.kriteria-row"));
        if (allRows.length > 0) {
            const paginationContainer = document.getElementById('ledPaginationContainer');
            const noSearchRow = document.getElementById('noSearchRow');
            const searchInput = document.getElementById('ledSearchInput');
            const btnClearSearch = document.getElementById('btnClearSearch');
            const quickFilterButtons = document.querySelectorAll('.quick-filter');
            const visibleCountEl = document.getElementById('visibleCount');

            let currentActiveRows = [...allRows];
            let currentPage = 1;
            let rowsPerPage = 10;
            let currentFilterStatus = 'all';
            let currentSearchKeyword = '';

            const btnPrev = document.getElementById("btnPrev");
            const btnNext = document.getElementById("btnNext");
            const pageNumbersContainer = document.getElementById("pageNumbers");
            const itemsPerPageSelect = document.getElementById("itemsPerPage");
            const goToPageInput = document.getElementById("goToPage");

            // Filter logic: combines search keyword + quick filter status
            function filterRows() {
                const query = currentSearchKeyword.trim().toLowerCase();

                currentActiveRows = allRows.filter(row => {
                    // 1. Text match (Kriteria, Standar, Role Tugas)
                    const kriteriaText = row.getAttribute('data-kriteria') || '';
                    const standarText = row.getAttribute('data-standar') || '';
                    const roleText = row.getAttribute('data-role') || '';
                    const matchQuery = !query || kriteriaText.includes(query) || standarText.includes(query) || roleText.includes(query);

                    if (!matchQuery) return false;

                    // 2. Status match
                    const hasLink = row.getAttribute('data-has-link') === '1';
                    const kabagApproved = parseInt(row.getAttribute('data-kabag-approved') || '0', 10);
                    const statusVal = row.getAttribute('data-status') || '';

                    if (currentFilterStatus === 'unuploaded') {
                        return !hasLink;
                    } else if (currentFilterStatus === 'pending-kabag') {
                        return hasLink && kabagApproved === 0;
                    } else if (currentFilterStatus === 'approved-kabag') {
                        return kabagApproved === 1;
                    } else if (currentFilterStatus === 'revisi') {
                        return statusVal.includes('revisi');
                    }

                    return true;
                });

                currentPage = 1;
                if (visibleCountEl) visibleCountEl.textContent = currentActiveRows.length;
                renderTable();
            }

            function renderTable() {
                const totalPages = Math.ceil(currentActiveRows.length / rowsPerPage);
                if (currentPage < 1) currentPage = 1;
                if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

                // Hide all rows first
                allRows.forEach(row => row.style.display = "none");

                if (currentActiveRows.length === 0) {
                    if (noSearchRow) noSearchRow.style.display = "block";
                    if (paginationContainer) paginationContainer.style.setProperty('display', 'none', 'important');
                } else {
                    if (noSearchRow) noSearchRow.style.display = "none";
                    if (paginationContainer) paginationContainer.style.setProperty('display', 'flex', 'important');

                    // Show current page slice
                    const startIdx = (currentPage - 1) * rowsPerPage;
                    const endIdx = currentPage * rowsPerPage;

                    currentActiveRows.slice(startIdx, endIdx).forEach(row => {
                        row.style.display = "";
                    });
                }

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                if(!pageNumbersContainer) return;
                pageNumbersContainer.innerHTML = "";
                
                if (totalPages <= 1) {
                    if(btnPrev) btnPrev.disabled = true;
                    if(btnNext) btnNext.disabled = true;
                    return;
                }

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

            // Search input listener
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    currentSearchKeyword = e.target.value;
                    filterRows();
                });
            }

            // Clear search button
            if (btnClearSearch && searchInput) {
                btnClearSearch.addEventListener('click', function() {
                    searchInput.value = '';
                    currentSearchKeyword = '';
                    filterRows();
                    searchInput.focus();
                });
            }

            // Quick filter buttons listener
            quickFilterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    quickFilterButtons.forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-outline-secondary');
                    });
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-primary', 'active');

                    currentFilterStatus = this.getAttribute('data-filter') || 'all';
                    filterRows();
                });
            });

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
                    rowsPerPage = parseInt(e.target.value, 10);
                    currentPage = 1;
                    if(goToPageInput) goToPageInput.value = 1;
                    renderTable();
                });
            }

            if(goToPageInput) {
                goToPageInput.addEventListener("change", (e) => {
                    const page = parseInt(e.target.value, 10);
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

    // Toggle individual preview text on expand/collapse
    document.querySelectorAll('.btn-toggle-kriteria').forEach(btn => {
        const targetId = btn.getAttribute('data-bs-target');
        const previewId = btn.getAttribute('data-preview-id');
        const collapseEl = document.querySelector(targetId);
        const previewEl = document.getElementById(previewId);
        const toggleText = btn.querySelector('.toggle-text');
        const toggleIcon = btn.querySelector('.toggle-icon');

        if (collapseEl) {
            collapseEl.addEventListener('show.bs.collapse', function () {
                if (previewEl) previewEl.style.display = 'none';
                if (toggleText) toggleText.textContent = 'Sembunyikan Rincian';
                if (toggleIcon) { toggleIcon.classList.remove('bi-chevron-down'); toggleIcon.classList.add('bi-chevron-up'); }
            });
            collapseEl.addEventListener('hide.bs.collapse', function () {
                if (previewEl) previewEl.style.display = '-webkit-box';
                if (toggleText) toggleText.textContent = 'Lihat Rincian Aspek';
                if (toggleIcon) { toggleIcon.classList.remove('bi-chevron-up'); toggleIcon.classList.add('bi-chevron-down'); }
            });
        }
    });

    // Master toggle all button
    let isAllExpanded = false;
    const btnToggleAll = document.getElementById('btnToggleAllKriteria');
    if (btnToggleAll) {
        btnToggleAll.addEventListener('click', function() {
            isAllExpanded = !isAllExpanded;
            const collapses = document.querySelectorAll('.kriteria-collapse');
            collapses.forEach(c => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(c, { toggle: false });
                    if (isAllExpanded) {
                        bsCollapse.show();
                    } else {
                        bsCollapse.hide();
                    }
                }
            });
            const toggleAllText = document.getElementById('toggleAllText');
            const toggleAllIcon = btnToggleAll.querySelector('i');
            if (isAllExpanded) {
                if (toggleAllText) toggleAllText.textContent = 'Tutup Semua Rincian';
                if (toggleAllIcon) { toggleAllIcon.className = 'bi bi-arrows-collapse me-1'; }
            } else {
                if (toggleAllText) toggleAllText.textContent = 'Buka Semua Rincian';
                if (toggleAllIcon) { toggleAllIcon.className = 'bi bi-arrows-expand me-1'; }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>