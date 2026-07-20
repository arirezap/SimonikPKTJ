<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($page_title ?? 'Kelola Tim Saya') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Kelola Tim Saya</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStafModal">
                <i class="bi bi-person-plus-fill"></i> Tambah Anggota Tim
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="py-3 px-4">No</th> 
                            <th class="py-3 fw-bold text-dark">Nama Lengkap</th>
                            <th class="py-3 fw-bold text-dark">Jabatan</th>
                            <th class="py-3 fw-bold text-dark" style="min-width: 200px;">Unit Kerja</th>
                            <th width="10%" class="text-center py-3 fw-bold text-dark px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bawahan)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada anggota tim. Klik 'Tambah Anggota Tim' untuk mulai.</td>
                        </tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($bawahan as $b): ?>
                            <tr>
                                <td class="px-4"><?= $i++ ?></td>
                                <td>
                                    <strong><?= esc($b['nama_lengkap']) ?></strong><br>
                                    <small class="text-muted"><?= esc($b['nip'] ?? '-') ?></small>
                                </td>
                                <td><?= esc($b['jabatan'] ?? '-') ?></td>
                                <td>
                                    <select class="form-select form-select-sm unit-kerja-select" data-user-id="<?= $b['id'] ?>">
                                        <option value="">-- Pilih Unit --</option>
                                        <?php foreach ($unit_kerja_list as $unit): ?>
                                            <option value="<?= esc($unit['nama_unit']) ?>" <?= ($b['unit'] == $unit['nama_unit']) ? 'selected' : '' ?>>
                                                <?= esc($unit['nama_unit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="update-status small mt-1"></div>
                                </td>
                                <td class="text-center">
                                    <form action="<?= site_url('tim/remove') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="bawahan_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Keluarkan pegawai ini dari tim Anda?')" title="Hapus dari Tim">
                                            <i class="bi bi-person-x-fill"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Staf -->
<div class="modal fade" id="addStafModal" tabindex="-1" aria-labelledby="addStafModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStafModalLabel">Tambah Anggota Tim</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('tim/add') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bawahan_id" class="form-label fw-bold">Pilih Pegawai</label>
                        <select name="bawahan_id" id="bawahan_id" class="form-select select2" required style="width: 100%;">
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($semua_pegawai as $p): ?>
                                <?php if($p['atasan_id'] == session()->get('id')) continue; ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= esc($p['nama_lengkap']) ?> - <?= esc($p['unit'] ?? 'Tanpa Unit') ?>
                                    <?= !empty($p['atasan_id']) ? '(Sudah memiliki atasan)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Pilih pegawai yang akan dikelola kinerja dan laporannya oleh Anda.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Select2 jika tersedia
    if (typeof jQuery !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            dropdownParent: $('#addStafModal')
        });
    }

    // AJAX untuk ubah Unit Kerja
    const unitKerjaSelects = document.querySelectorAll('.unit-kerja-select');
    
    unitKerjaSelects.forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newUnit = this.value;
            const statusDiv = this.nextElementSibling;

            statusDiv.innerHTML = '<i class="bi bi-arrow-repeat"></i> Menyimpan...';
            statusDiv.className = 'update-status small mt-1 text-muted';

            const csrfTokenName = '<?= csrf_token() ?>';
            // Ambil hash dari hidden input form pertama yang ada csrf
            const csrfInput = document.querySelector('input[name="' + csrfTokenName + '"]');
            const csrfHash = csrfInput ? csrfInput.value : '';

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('unit', newUnit);
            formData.append(csrfTokenName, csrfHash);

            fetch('<?= site_url('tim/update_unit') ?>', {
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
                    statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + (data.message || 'Gagal');
                    statusDiv.className = 'update-status small mt-1 text-danger';
                }
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> Error jaringan!';
                statusDiv.className = 'update-status small mt-1 text-danger';
                setTimeout(() => { statusDiv.innerHTML = ''; }, 3000);
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
