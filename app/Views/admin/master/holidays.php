<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Master Hari Libur<?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Hari Libur Nasional<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-3 p-3 bg-light rounded border d-flex justify-content-between align-items-center">
    <div>
        <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-calendar3 me-2"></i> Pengaturan Hari Libur</h6>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">Kalender Libur Nasional & Cuti Bersama tersinkronisasi otomatis via API. Digunakan untuk batas pengisian log harian.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?= site_url('master-data/holidays/sync') ?>" method="post" class="m-0">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Paksa sinkronisasi ulang dengan API?')">
                <i class="bi bi-arrow-repeat me-1"></i> Sync Ulang
            </button>
        </form>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Libur
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="dataTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Tanggal</th>
                        <th>Nama Hari Libur</th>
                        <th width="15%" class="text-center">Jenis</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($items as $item) : ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-semibold text-dark"><?= date('d F Y', strtotime($item['holiday_date'])) ?></span>
                                        <span class="d-block text-muted small"><?= date('l', strtotime($item['holiday_date'])) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium text-dark">
                                <?= esc($item['holiday_name']) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($item['is_national']): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-3 py-2 rounded-pill">Libur Nasional</span>
                                <?php else: ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-pill">Cuti Bersama</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('master-data/holidays/delete/' . $item['id']) ?>" 
                                   class="btn btn-light btn-sm text-danger rounded-circle shadow-sm border border-light"
                                   style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background-color: #f8f9fa;"
                                   onclick="return confirm('Yakin ingin menghapus hari libur ini?')"
                                   data-bs-toggle="tooltip" title="Hapus">
                                    <i class="bi bi-trash3 fs-5"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($items)) : ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="my-3">
                                    <i class="bi bi-calendar-x text-muted opacity-50 mb-3 d-block" style="font-size: 3rem;"></i>
                                    <h6 class="fw-semibold text-dark mb-1">Belum Ada Data</h6>
                                    <p class="text-muted small mb-0">Klik tombol <strong>Sinkron Ulang</strong> untuk memuat hari libur otomatis.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Internal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('master-data/holidays/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Tambah Libur / Cuti Bersama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Masukkan tanggal yang diliburkan di luar kalender otomatis (misal: dispensasi khusus).</p>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Libur <span class="text-danger">*</span></label>
                        <input type="date" name="holiday_date" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nama / Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="holiday_name" class="form-control" required placeholder="Cth: Cuti Bersama Tambahan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?= $this->endSection() ?>
