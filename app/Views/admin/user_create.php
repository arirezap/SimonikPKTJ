<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Tambah Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    @media (max-width: 767.98px) {
        .form-control, .form-select {
            font-size: 16px !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-3">
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
        <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-person-plus-fill text-primary me-2"></i>Tambah Pengguna</h1>
        <a href="<?= site_url('users') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show small mb-3 shadow-sm py-2 px-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Terjadi kesalahan input:</strong>
            <ul class="mb-0 ps-3 mt-1">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <form action="<?= site_url('users/store') ?>" method="post" autocomplete="off" id="formUserCreate">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <!-- SECTION A: AKUN -->
                    <div class="col-lg-6">
                        <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-lock me-1"></i> A. Informasi Akun</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control form-control-sm" value="<?= old('username') ?>" placeholder="Username login..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm" value="<?= old('email') ?>" placeholder="nama@instansi.go.id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-sm" placeholder="Password akun..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Role Primer</label>
                                <select name="role" class="form-select form-select-sm">
                                    <option value="direktur" <?= old('role') == 'direktur' ? 'selected' : '' ?>>Direktur (Level 1)</option>
                                    <option value="wadir" <?= old('role') == 'wadir' ? 'selected' : '' ?>>Wakil Direktur (Level 2)</option>
                                    <option value="kabag_aak" <?= old('role') == 'kabag_aak' ? 'selected' : '' ?>>Kabag AAK (Level 3)</option>
                                    <option value="kabag_kuk" <?= old('role') == 'kabag_kuk' ? 'selected' : '' ?>>Kabag KUK (Level 3)</option>
                                    <option value="manajemen" <?= old('role') == 'manajemen' ? 'selected' : '' ?>>Kanit / Katim / Kaprodi (Level 4)</option>
                                    <option value="user" <?= old('role', 'user') == 'user' ? 'selected' : '' ?>>Staf / Pelaksana (Level 5)</option>
                                    <option value="tugas_belajar" <?= old('role') == 'tugas_belajar' ? 'selected' : '' ?>>Tugas Belajar (Level 5)</option>
                                    <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Administrator (Sistem)</option>
                                </select>
                            </div>
                            <div class="p-3 bg-white border rounded-3">
                                <label class="form-label small fw-bold text-dark mb-1">Role Tambahan (Sekunder)</label>
                                <div class="form-text small mb-2" style="font-size: 0.72rem;">Centang jika pegawai memiliki peran rangkap.</div>
                                
                                <?php 
                                    $availableSecondary = [
                                        'kepegawaian' => 'Kepegawaian (Rekap & Approval)',
                                        'spm' => 'SPM (Penjaminan Mutu)',
                                        'tugas_belajar' => 'Tugas Belajar (Laporan Studi)'
                                    ];
                                    $oldSecondary = old('secondary_roles') ?? [];
                                ?>
                                
                                <?php foreach ($availableSecondary as $val => $label): ?>
                                    <div class="form-check form-check-inline me-3">
                                        <input class="form-check-input" type="checkbox" name="secondary_roles[]" value="<?= esc($val) ?>" id="role_<?= esc($val) ?>" <?= (in_array($val, $oldSecondary)) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="role_<?= esc($val) ?>">
                                            <?= esc($label) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION B: KEPEGAWAIAN & HIERARKI -->
                    <div class="col-lg-6">
                        <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-diagram-3 me-1"></i> B. Data Kepegawaian & Hierarki</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-sm" value="<?= old('nama_lengkap') ?>" placeholder="Nama lengkap dengan gelar..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">NIP</label>
                                <input type="text" name="nip" class="form-control form-control-sm" value="<?= old('nip') ?>" placeholder="Nomor Induk Pegawai...">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control form-control-sm" value="<?= old('jabatan') ?>" placeholder="Nama jabatan...">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Pangkat / Golongan</label>
                                    <input type="text" name="pangkat" class="form-control form-control-sm" value="<?= old('pangkat') ?>" placeholder="Pangkat/Golongan...">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Unit Kerja</label>
                                <select name="unit_id" class="form-select form-select-sm">
                                    <option value="">-- Tanpa Unit / Pimpinan --</option>
                                    <?php if (!empty($unit_kerja_list)): ?>
                                        <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                            <option value="<?= esc($unit_kerja['id']) ?>" <?= (old('unit_id') == $unit_kerja['id'] || old('unit') == $unit_kerja['nama_unit']) ? 'selected' : '' ?>><?= esc($unit_kerja['nama_unit']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="p-3 bg-white border rounded-3">
                                <label class="form-label small fw-bold text-dark mb-1">Atasan Langsung (Pejabat Penilai)</label>
                                <select name="atasan_id" class="form-select form-select-sm select2" id="selectAtasanCreate">
                                    <option value="">-- Tanpa Atasan (Misal: Direktur) --</option>
                                    <?php foreach ($potential_bosses as $boss): ?>
                                        <option value="<?= $boss['id'] ?>" <?= (old('atasan_id') == $boss['id']) ? 'selected' : '' ?>>
                                            <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small" style="font-size: 0.72rem;">
                                    Atasan akan menjadi pejabat penilai kinerja dan pihak kedua di kontrak kinerja.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="<?= site_url('users') ?>" class="btn btn-secondary btn-sm rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm px-4" id="btnSubmitUserCreate">
                        <i class="bi bi-save me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('#selectAtasanCreate').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Atasan --'
        });
    }

    $('#formUserCreate').on('submit', function() {
        $('#btnSubmitUserCreate').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...').prop('disabled', true);
    });
});
</script>
<?= $this->endSection() ?>