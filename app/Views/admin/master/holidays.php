<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Master Hari Libur') ?><?= $this->endSection() ?>
<?= $this->section('page_title') ?>Master Hari Libur Nasional<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Top Toolbar Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchHoliday" class="form-control border-start-0 ps-0" placeholder="Cari tanggal / keterangan libur...">
                </div>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="bi bi-calendar-check-fill me-1"></i> <span id="countHoliday"><?= count($items) ?></span> Hari Libur
                </span>
                <form action="<?= site_url('master-data/holidays/sync') ?>" method="POST" id="formSync" class="m-0">
                    <?= csrf_field() ?>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" onclick="confirmSync()">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync API Libur
                    </button>
                </form>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Libur
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelHoliday">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th style="width: 240px;">Tanggal Libur</th>
                        <th>Keterangan Hari Libur / Cuti Bersama</th>
                        <th style="width: 180px;" class="text-center">Kategori</th>
                        <th style="width: 100px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): foreach ($items as $index => $item) : ?>
                    <tr class="holiday-row">
                        <td class="text-center fw-bold text-muted nomor-holiday"><?= $index + 1 ?></td>
                        <td class="holiday-date">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger-subtle text-danger border border-danger-subtle rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px; min-width: 36px;">
                                    <i class="bi bi-calendar-event-fill"></i>
                                </div>
                                <div>
                                    <span class="d-block fw-semibold text-dark"><?= date('d F Y', strtotime($item['holiday_date'])) ?></span>
                                    <span class="d-block text-muted small"><?= date('l', strtotime($item['holiday_date'])) ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="fw-medium text-dark holiday-name">
                            <?= esc($item['holiday_name']) ?>
                        </td>
                        <td class="text-center holiday-type">
                            <?php if ($item['is_national']): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                    <i class="bi bi-flag-fill me-1"></i> Libur Nasional
                                </span>
                            <?php else: ?>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                    <i class="bi bi-sun-fill me-1"></i> Cuti Bersama
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btn-hapus" 
                                style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                onclick="confirmDeleteHoliday(<?= $item['id'] ?>, '<?= esc($item['holiday_name'], 'js') ?> (<?= date('d M Y', strtotime($item['holiday_date'])) ?>)')"
                                title="Hapus Libur">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr id="emptyRow">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada data hari libur. Klik <strong>Sync API Libur</strong> untuk mengimpor kalender libur otomatis.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr id="noSearchResult" style="display: none;">
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-search fs-3 d-block mb-1 text-secondary opacity-50"></i>
                            Tidak ada hari libur yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Libur Internal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" aria-labelledby="addHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('master-data/holidays/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="addHolidayModalLabel"><i class="bi bi-plus-circle me-2"></i> Tambah Hari Libur Manual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="holiday_date" class="form-label fw-bold text-dark small">Tanggal Libur <span class="text-danger">*</span></label>
                        <input type="date" name="holiday_date" id="holiday_date" class="form-control" value="<?= old('holiday_date', date('Y-m-d')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="holiday_name" class="form-label fw-bold text-dark small">Nama / Keterangan Libur <span class="text-danger">*</span></label>
                        <input type="text" name="holiday_name" id="holiday_name" class="form-control" required placeholder="Contoh: Cuti Bersama Tambahan / Dispensasi Khusus" value="<?= old('holiday_name') ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">Kategori Libur</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_national" id="kategori1" value="1" checked>
                                <label class="form-check-label small" for="kategori1">Libur Nasional</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_national" id="kategori0" value="0">
                                <label class="form-check-label small" for="kategori0">Cuti Bersama / Khusus</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus Tersembunyi -->
<form action="" method="POST" id="formHapus"><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Live Search Holiday
    document.getElementById('searchHoliday').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.holiday-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const date = row.querySelector('.holiday-date').textContent.toLowerCase();
            const name = row.querySelector('.holiday-name').textContent.toLowerCase();
            const type = row.querySelector('.holiday-type').textContent.toLowerCase();
            if (date.includes(query) || name.includes(query) || type.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noResult = document.getElementById('noSearchResult');
        if (noResult) {
            noResult.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        const countEl = document.getElementById('countHoliday');
        if (countEl) {
            countEl.textContent = query ? visibleCount : rows.length;
        }
    });

    // Confirm Sync API with SweetAlert2 & Fallback
    function confirmSync() {
        function executeSync() {
            document.getElementById('formSync').submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Sinkronisasi Hari Libur?',
                text: 'Sistem akan mengambil data Hari Libur Nasional & Cuti Bersama resmi tahun berjalan dari API.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-arrow-repeat me-1"></i> Mulai Sinkronisasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeSync();
                }
            });
        } else {
            if (confirm('Sinkronkan kalender hari libur nasional & cuti bersama resmi dari API?')) {
                executeSync();
            }
        }
    }

    // Delete Confirmation with SweetAlert2 & Native Fallback
    function confirmDeleteHoliday(id, name) {
        function executeDelete() {
            const form = document.getElementById('formHapus');
            form.action = `<?= site_url('master-data/holidays/delete/') ?>${id}`;
            form.submit();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Hari Libur?',
                html: `Hari libur <strong>"${name}"</strong> akan dihapus dari sistem.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin menghapus hari libur "${name}"?`)) {
                executeDelete();
            }
        }
    }
</script>
<?= $this->endSection() ?>

