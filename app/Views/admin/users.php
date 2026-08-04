<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
/* Custom style to make select2 fit nicely in a table cell */
.select2-container--bootstrap-5 .select2-selection--single {
    font-size: 0.875rem;
    min-height: 31px;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kelola Pengguna</h1>
        
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-info" id="batchEditBtn" title="Ubah atasan banyak pengguna sekaligus">
                    <i class="bi bi-people-fill"></i> Batch Edit Atasan
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal" title="Impor banyak pengguna dari file CSV">
                    <i class="bi bi-upload"></i> Import
                </button>
                <a href="<?= site_url('users/export') ?>" class="btn btn-sm btn-outline-secondary" title="Unduh template CSV untuk impor">
                    <i class="bi bi-download"></i> Export Template
                </a>
            </div>
            <a href="<?= site_url('users/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus-fill"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="<?= site_url('users') ?>" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label text-muted small fw-bold text-uppercase">Pencarian</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Nama atau Username..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label text-muted small fw-bold text-uppercase">Role</label>
                    <select name="role" id="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        <option value="admin" <?= (isset($filter_role) && $filter_role == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="direktur" <?= (isset($filter_role) && $filter_role == 'direktur') ? 'selected' : '' ?>>Direktur</option>
                        <option value="wadir" <?= (isset($filter_role) && $filter_role == 'wadir') ? 'selected' : '' ?>>Wakil Direktur</option>
                        <option value="user" <?= (isset($filter_role) && $filter_role == 'user') ? 'selected' : '' ?>>User</option>
                        <option value="kabag" <?= (isset($filter_role) && $filter_role == 'kabag') ? 'selected' : '' ?>>Kabag (AAK/KUK)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="unit" class="form-label text-muted small fw-bold text-uppercase">Unit Kerja</label>
                    <select name="unit" id="unit" class="form-select form-select-sm">
                        <option value="">Semua Unit</option>
                        <option value="kosong" <?= (isset($filter_unit) && $filter_unit === 'kosong') ? 'selected' : '' ?>>-- Belum Diatur / Kosong --</option>
                        <?php foreach($unit_kerja_list as $uk): ?>
                            <option value="<?= esc($uk['nama_unit']) ?>" <?= (isset($filter_unit) && $filter_unit == $uk['nama_unit']) ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="bi bi-funnel-fill"></i> Filter
                    </button>
                    <?php if (!empty($search) || !empty($filter_role) || !empty($filter_unit) || (!empty($sortBy) && $sortBy !== 'users.nama_lengkap') || (!empty($sortOrder) && $sortOrder !== 'asc')): ?>
                        <a href="<?= site_url('users') ?>" class="btn btn-sm btn-light border" title="Reset Filter">
                            <i class="bi bi-x-circle text-danger"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

<!-- Letakkan kode Modal ini di bagian bawah file view -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Pengguna dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/import') ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>
                        Silakan unduh template terlebih dahulu untuk memastikan format data sesuai.
                        <a href="<?= site_url('users/export') ?>">Unduh Template Disini</a>.
                    </p>
                    <hr>
                    <div class="mb-3">
                        <label for="file_excel" class="form-label">Pilih File CSV (.csv)</label>
                        <input class="form-control" type="file" name="file_excel" id="file_excel" required accept=".csv, text/csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Batch Edit Atasan -->
<div class="modal fade" id="batchEditModal" tabindex="-1" aria-labelledby="batchEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchEditModalLabel">Batch Edit Atasan Langsung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('users/batch_update') ?>" method="post" autocomplete="off">
                <?= csrf_field() ?>
                <input type="hidden" name="user_ids" id="batchUserIds">
                <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                <input type="hidden" name="return_qs" value="<?= esc($qs) ?>">
                <div class="modal-body">
                    <p>Anda akan mengubah atasan untuk <strong id="batchEditCount">0</strong> pengguna terpilih.</p>
                    <div class="mb-3">
                        <label for="atasan_id_batch" class="form-label fw-bold">Pilih Atasan Langsung Baru</label>
                        <select name="atasan_id" id="atasan_id_batch" class="form-select">
                            <option value="">-- Hapus Atasan (Tidak Memiliki Atasan) --</option>
                            <?php foreach ($potential_bosses as $boss): ?>
                                <option value="<?= $boss['id'] ?>"><?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Semua pengguna yang dipilih akan memiliki atasan yang sama setelah disimpan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tambahkan ini untuk menampilkan error validasi dari proses import -->
<?php if (session()->has('import_errors')): ?>
    <div class="alert alert-danger">
        <strong>Terjadi beberapa kesalahan saat import:</strong>
        <ul>
            <?php foreach (session('import_errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- ... sisa kode tabel pengguna Anda ... -->

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php
    // Helper function for sorting links
    $queryParams = $_GET;
    function getSortUrl($column, $queryParams, $currentSortBy, $currentSortOrder) {
        $params = $queryParams;
        $params['sort_by'] = $column;
        $params['sort_order'] = ($currentSortBy === $column && $currentSortOrder === 'asc') ? 'desc' : 'asc';
        unset($params['page_users']); // Reset pagination when sorting
        return site_url('users?' . http_build_query($params));
    }
    ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="3%" class="px-4 py-3"><input class="form-check-input" type="checkbox" id="selectAll" title="Pilih semua"></th>
                            <th width="5%" class="py-3">No</th> 
                            <th style="min-width: 200px;" class="py-3">
                                <a href="<?= getSortUrl('users.nama_lengkap', $queryParams, $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark fw-bold">
                                    Nama Lengkap
                                    <?php if ($sortBy === 'users.nama_lengkap'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="min-width: 180px;" class="py-3">
                                <a href="<?= getSortUrl('users.jabatan', $queryParams, $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark fw-bold">
                                    Jabatan
                                    <?php if ($sortBy === 'users.jabatan'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="min-width: 220px;" class="py-3 fw-bold text-dark">Unit Kerja</th>
                            <th class="py-3">
                                <a href="<?= getSortUrl('users.role', $queryParams, $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark fw-bold">
                                    Role
                                    <?php if ($sortBy === 'users.role'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th class="py-3 fw-bold text-dark">Unit Kabag</th>
                            <th class="bg-light text-dark fw-bold py-3" style="min-width: 200px;">Atasan Langsung</th>
                            <th width="10%" class="text-nowrap text-center py-3 fw-bold text-dark px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        
                        if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                        <?php endif;
                        
                        foreach ($users as $user): 
                            $rowClass = ($i > 10) ? 'd-none user-row-hidden' : 'user-row-visible';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td class="text-center px-4">
                                <input class="form-check-input user-checkbox" type="checkbox" value="<?= $user['id'] ?>" aria-label="Pilih pengguna <?= esc($user['nama_lengkap']) ?>">
                            </td>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= esc($user['nama_lengkap']) ?></strong><br>
                                <small class="text-muted"><?= esc($user['nip'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($user['jabatan'] ?? '-') ?></td>
                            <td>
                                <?php if ($user['role'] === 'direktur'): ?>
                                    <span class="badge bg-danger">Direktur</span>
                                <?php elseif ($user['role'] === 'wadir'): ?>
                                    <span class="badge bg-warning text-dark">Wakil Direktur</span>
                                <?php else: ?>
                                    <select class="form-select form-select-sm unit-kerja-select" data-user-id="<?= $user['id'] ?>" aria-label="Pilih unit kerja untuk <?= esc($user['nama_lengkap']) ?>">
                                        <option value="">-- Pilih --</option>
                                        <?php if (!empty($unit_kerja_list)): ?>
                                            <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                                <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= ($user['unit'] == $unit_kerja['nama_unit']) ? 'selected' : '' ?>>
                                                    <?= esc($unit_kerja['nama_unit']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="update-status small mt-1"></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $roleLabels = [
                                        'direktur' => 'Direktur (Level 1)',
                                        'wadir' => 'Wakil Direktur (Level 2)',
                                        'kabag_aak' => 'Kabag AAK (Level 3)',
                                        'kabag_kuk' => 'Kabag KUK (Level 3)',
                                        'manajemen' => 'Kanit/Katim (Level 4)',
                                        'user' => 'Staff (Level 5)',
                                        'tugas_belajar' => 'Tugas Belajar (Level 5)',
                                        'admin' => 'Administrator'
                                    ];
                                    $roleText = $roleLabels[$user['role']] ?? $user['role'];
                                    $badgeClass = ($user['role'] === 'tugas_belajar') ? 'bg-primary bg-opacity-75' : 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= esc($roleText) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($user['unit_kabag'])):
                                    $unit_kabag = esc($user['unit_kabag']);
                                    $badge_class = 'bg-secondary';
                                    if ($unit_kabag === 'aak') $badge_class = 'bg-success';
                                    if ($unit_kabag === 'kuk') $badge_class = 'bg-info text-dark';
                                ?>
                                    <span class="badge <?= $badge_class ?>"><?= strtoupper($unit_kabag) ?></span>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.85em;">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php 
                                    $atasan_display = $user['nama_atasan'] ?? null;
                                    $is_auto_synced = false;
                                    // Jika atasan belum diset, cek pimpinan unitnya
                                    if ((empty($atasan_display) || $atasan_display === '-') && !empty($user['unit']) && isset($unitManagers[$user['unit']])) {
                                        $atasan_display = $unitManagers[$user['unit']];
                                        $is_auto_synced = true;
                                    }
                                ?>
                                <?php if(!empty($atasan_display) && $atasan_display !== '-'): ?>
                                    <span class="badge <?= $is_auto_synced ? 'bg-info text-dark' : 'bg-success' ?>" <?= $is_auto_synced ? 'title="Disinkronisasi otomatis dari Unit Kerja"' : '' ?>>
                                        <i class="bi bi-person-check-fill"></i> <?= esc($atasan_display) ?>
                                        <?= $is_auto_synced ? ' <i class="bi bi-arrow-repeat"></i>' : '' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted font-italic" style="font-size: 0.85em;">Tidak diset / Puncak Pimpinan</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-nowrap">
                                <?php $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>
                                <a href="<?= site_url('users/edit/'.$user['id']) . $qs ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= site_url('users/delete/'.$user['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Skeleton Loader (Selalu dirender, JS yang mengatur hide/show) -->
            <div id="skeleton-loader" class="d-none p-4 text-center">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
                <div class="text-muted small">Memuat data pengguna berikutnya...</div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const batchEditBtn = document.getElementById('batchEditBtn');
    const batchEditModalEl = document.getElementById('batchEditModal');
    let batchEditModal = null;
    
    if (batchEditModalEl) {
        batchEditModal = new bootstrap.Modal(batchEditModalEl);
    }
    
    const batchUserIdsInput = document.getElementById('batchUserIds');
    const batchEditCountSpan = document.getElementById('batchEditCount');

    // Event Delegation untuk Checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Fungsi untuk tombol batch edit
    if (batchEditBtn) {
        batchEditBtn.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox'))
                                     .filter(checkbox => checkbox.checked)
                                     .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                alert('Peringatan: Silakan pilih minimal satu pengguna untuk diedit.');
                return;
            }

            batchUserIdsInput.value = selectedIds.join(',');
            batchEditCountSpan.textContent = selectedIds.length;
            batchEditModal.show();
        });
    }

    // --- EVENT DELEGATION UNTUK AJAX UNIT KERJA UPDATE ---
    $(document).on('change', '.unit-kerja-select', function(e) {
        const select = this;
        const userId = select.dataset.userId;
        const newUnit = select.value;
        // Gunakan jQuery atau querySelector dari parent karena DOM Select2 menyisipkan elemen di tengah
        const statusDiv = select.parentElement.querySelector('.update-status');

            // Tampilkan indikator loading
            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';

            const csrfTokenName = '<?= csrf_token() ?>';
            // Ambil hash terbaru dari salah satu form di halaman (batch edit form)
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('unit', newUnit);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('users/ajax_update_unit') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data[csrfTokenName]) {
                    document.querySelectorAll('input[name="' + csrfTokenName + '"]').forEach(input => {
                        input.value = data[csrfTokenName];
                    });
                }

                if (data.success) {
                    statusDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
                    statusDiv.className = 'update-status small mt-1 text-success';
                } else {
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Gagal';
                    statusDiv.className = 'update-status small mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            });
    });

    // Inisialisasi Select2 untuk dropdown unit kerja
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.unit-kerja-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih --'
        });

        // Autofocus kotak pencarian teks ketika dropdown Select2 diklik
        $(document).on('select2:open', () => {
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if(searchField) searchField.focus();
            }, 50);
        });
    }

    // --- CLIENT-SIDE INFINITE SCROLLING LOGIC ---
    let isLoading = false;
    const skeletonLoader = document.getElementById('skeleton-loader');

    // Buat observer
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading) {
            loadMoreData();
        }
    }, { rootMargin: "100px" });

    // Observasi loader jika ada
    if (skeletonLoader) {
        observer.observe(skeletonLoader);
        checkRemainingRows(); // Cek inisial saat halaman dimuat
    }

    function checkRemainingRows() {
        const hiddenRows = document.querySelectorAll('tr.user-row-hidden');
        if (hiddenRows.length === 0) {
            skeletonLoader.classList.add('d-none');
        } else {
            skeletonLoader.classList.remove('d-none');
        }
    }

    function loadMoreData() {
        const hiddenRows = document.querySelectorAll('tr.user-row-hidden');
        if (hiddenRows.length === 0) return;

        isLoading = true;
        
        // Beri sedikit delay simulasi network (500ms) agar skeleton loading terlihat dan terasa mulus
        setTimeout(() => {
            const limit = Math.min(10, hiddenRows.length);
            for (let i = 0; i < limit; i++) {
                hiddenRows[i].classList.remove('d-none', 'user-row-hidden');
                hiddenRows[i].classList.add('user-row-visible');
            }
            isLoading = false;
            checkRemainingRows(); // Cek lagi apakah masih ada sisa untuk scroll berikutnya
        }, 500);
    }
});
</script>
<?= $this->endSection() ?>