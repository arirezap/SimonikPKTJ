<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Kelola Pengguna') ?><?= $this->endSection() ?>

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

    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= site_url('users') ?>" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-bold">Cari Pengguna</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan nama..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
                <div class="col-md-3">
                    <?php if (!empty($search) || (!empty($sortBy) && $sortBy !== 'nama_lengkap') || (!empty($sortOrder) && $sortOrder !== 'asc')): ?>
                        <a href="<?= site_url('users') ?>" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
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

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="3%"><input class="form-check-input" type="checkbox" id="selectAll" title="Pilih semua"></th>
                            <th width="5%">No</th> 
                            <th style="min-width: 200px;">
                                <a href="<?= site_url('users?search=' . esc($search ?? '') . '&sort_by=nama_lengkap&sort_order=' . (($sortBy === 'nama_lengkap' && $sortOrder === 'asc') ? 'desc' : 'asc')) ?>" class="text-decoration-none text-dark">
                                    Nama Lengkap
                                    <?php if ($sortBy === 'nama_lengkap'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="min-width: 180px;">
                                <a href="<?= site_url('users?search=' . esc($search ?? '') . '&sort_by=jabatan&sort_order=' . (($sortBy === 'jabatan' && $sortOrder === 'asc') ? 'desc' : 'asc')) ?>" class="text-decoration-none text-dark">
                                    Jabatan
                                    <?php if ($sortBy === 'jabatan'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th style="min-width: 220px;">Unit Kerja</th>
                            <th>
                                <a href="<?= site_url('users?search=' . esc($search ?? '') . '&sort_by=role&sort_order=' . (($sortBy === 'role' && $sortOrder === 'asc') ? 'desc' : 'asc')) ?>" class="text-decoration-none text-dark">
                                    Role
                                    <?php if ($sortBy === 'role'): ?>
                                        <i class="bi bi-arrow-<?= ($sortOrder === 'asc') ? 'up' : 'down' ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Unit Kabag</th>
                            <th class="bg-info text-white" style="min-width: 200px;">Atasan Langsung</th>
                            <th width="10%" class="text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1; 
                        // Jika ada pagination, sesuaikan nomor urut
                        // Untuk saat ini, kita asumsikan tidak ada pagination atau dimulai dari 1
                        // Jika pagination diimplementasikan, Anda bisa menggunakan:
                        // $currentPage = $pager->getCurrentPage();
                        // $perPage = $pager->getPerPage();
                        // $i = ($currentPage - 1) * $perPage + 1;
                        
                        foreach ($users as $user): ?>
                        <tr>
                            <td class="text-center">
                                <input class="form-check-input user-checkbox" type="checkbox" value="<?= $user['id'] ?>" aria-label="Pilih pengguna <?= esc($user['nama_lengkap']) ?>">
                            </td>
                            <td><?= $i++ ?></td>
                            <td>
                                <strong><?= esc($user['nama_lengkap']) ?></strong><br>
                                <small class="text-muted"><?= esc($user['nip'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($user['jabatan'] ?? '-') ?></td>
                            <td>
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
                            </td>
                            <td><span class="badge bg-secondary"><?= esc($user['role']) ?></span></td>
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
                                <?php if($user['nama_atasan'] != '-'): ?>
                                    <span class="badge bg-success"><i class="bi bi-person-check-fill"></i> <?= esc($user['nama_atasan']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted font-italic" style="font-size: 0.85em;">Tidak diset / Puncak Pimpinan</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-nowrap">
                                <a href="<?= site_url('users/edit/'.$user['id']) ?>" class="btn btn-warning btn-sm" title="Edit">
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
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const batchEditBtn = document.getElementById('batchEditBtn');
    const batchEditModalEl = document.getElementById('batchEditModal');
    
    if (!batchEditModalEl) return; // Guard clause

    const batchEditModal = new bootstrap.Modal(batchEditModalEl);
    const batchUserIdsInput = document.getElementById('batchUserIds');
    const batchEditCountSpan = document.getElementById('batchEditCount');

    // Fungsi untuk select/deselect all
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Fungsi untuk tombol batch edit
    batchEditBtn.addEventListener('click', function() {
        const selectedIds = Array.from(userCheckboxes)
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

    // --- SCRIPT BARU UNTUK AJAX UNIT KERJA UPDATE ---
    const unitKerjaSelects = document.querySelectorAll('.unit-kerja-select');
    
    unitKerjaSelects.forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newUnit = this.value;
            const statusDiv = this.nextElementSibling; // Div .update-status

            // Tampilkan indikator loading
            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';

            const csrfTokenName = '<?= csrf_token() ?>';
            // Ambil hash terbaru dari salah satu form di halaman
            const csrfHash = document.querySelector('input[name="' + csrfTokenName + '"]').value;

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
                // Perbarui nilai CSRF token di semua form untuk request selanjutnya
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
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000); // Hilangkan pesan setelah 3 detik
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            });
        });
    });
});
</script>
<?= $this->endSection() ?>