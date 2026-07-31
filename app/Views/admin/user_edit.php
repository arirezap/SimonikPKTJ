<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Edit Pengguna & Hierarki') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Pengguna & Hierarki</h1>
        <a href="<?= site_url('users') . (!empty($query_string) ? '?' . $query_string : '') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Data Pegawai</h6>
        </div>
        <div class="card-body">
            <form action="<?= site_url('users/update') ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" name="return_qs" value="<?= esc($query_string ?? '') ?>">
                <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Informasi Akun</h5>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role Aplikasi (Primer)</label>
                            <select name="role" class="form-select">
                                <option value="direktur" <?= $user['role'] == 'direktur' ? 'selected' : '' ?>>Direktur (Level 1)</option>
                                <option value="wadir" <?= $user['role'] == 'wadir' ? 'selected' : '' ?>>Wakil Direktur (Level 2)</option>
                                <option value="kabag_aak" <?= $user['role'] == 'kabag_aak' ? 'selected' : '' ?>>Kabag AAK (Level 3)</option>
                                <option value="kabag_kuk" <?= $user['role'] == 'kabag_kuk' ? 'selected' : '' ?>>Kabag KUK (Level 3)</option>
                                <option value="manajemen" <?= $user['role'] == 'manajemen' ? 'selected' : '' ?>>Kanit / Katim / Kaprodi (Level 4)</option>
                                <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Staff / Pegawai / Dosen (Level 5)</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator (Sistem)</option>
                            </select>
                        </div>
                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-primary">Role Tambahan (Sekunder)</label>
                            <div class="form-text mb-2">Pilih peran tambahan jika pegawai memiliki tugas rangkap.</div>
                            
                            <?php 
                                // Opsi role sekunder yang tersedia
                                $availableSecondary = [
                                    'kepegawaian' => 'Kepegawaian (Melihat rekap seluruh nilai)',
                                    'spm' => 'SPM (Satuan Penjaminan Mutu)'
                                ];
                            ?>
                            
                            <?php foreach ($availableSecondary as $val => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secondary_roles[]" value="<?= esc($val) ?>" id="role_<?= esc($val) ?>" <?= (in_array($val, $secondary_roles ?? [])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="role_<?= esc($val) ?>">
                                        <?= esc($label) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3 text-secondary">Data Kepegawaian & Hierarki</h5>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap (dengan Gelar)</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= esc($user['nama_lengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="<?= esc($user['nip']) ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="<?= esc($user['jabatan']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pangkat/Golongan</label>
                                <input type="text" name="pangkat" class="form-control" value="<?= esc($user['pangkat']) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit Kerja</label>
                            <select name="unit" class="form-select">
                                <option value="">-- Tanpa Unit / Pimpinan Tinggi --</option>
                                <?php if (!empty($unit_kerja_list)): ?>
                                    <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                        <option value="<?= esc($unit_kerja['nama_unit']) ?>" <?= ($user['unit'] == $unit_kerja['nama_unit']) ? 'selected' : '' ?>>
                                            <?= esc($unit_kerja['nama_unit']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3 p-3 bg-light border rounded">
                            <label class="form-label fw-bold text-primary">Atasan Langsung (Pejabat Penilai)</label>
                            <select name="atasan_id" class="form-select select2">
                                <option value="">-- Tidak Memiliki Atasan (Misal: Direktur) --</option>
                                <?php foreach ($potential_bosses as $boss): ?>
                                    <option value="<?= $boss['id'] ?>" <?= ($user['atasan_id'] == $boss['id']) ? 'selected' : '' ?>>
                                        <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Pilih atasan langsung untuk keperluan Kontrak Kinerja dan Penilaian.
                                <br>Direktur -> Wadir -> Kabag -> Kanit/Katim -> Pelaksana.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- DANGER ZONE: Reset Kinerja -->
    <div class="card shadow mb-4 border-danger">
        <div class="card-header py-3 bg-danger text-white">
            <h6 class="m-0 font-weight-bold"><i class="bi bi-exclamation-triangle-fill"></i> Zona Bahaya: Reset Kinerja Bulanan</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Peringatan!</strong> Tindakan ini akan menghapus <b>Target Kinerja Bulanan</b>, <b>Laporan Kegiatan Harian</b>, dan <b>Nilai Rekap Remunerasi</b> pegawai ini pada bulan yang dipilih. Data yang dihapus tidak dapat dikembalikan. (Rencana Kinerja Tahunan tidak akan terhapus).
            </div>
            <form action="<?= site_url('users/reset-kinerja') ?>" method="post" id="formResetKinerja">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= esc($user['id']) ?>">
                
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Pilih Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            <?php 
                                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                foreach($bulanIndo as $idx => $bln): 
                            ?>
                                <option value="<?= $idx + 1 ?>"><?= $bln ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <button type="button" class="btn btn-danger w-100 fw-bold" onclick="confirmResetKinerja()">
                            <i class="bi bi-trash-fill"></i> Hapus Permanen Data Kinerja
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmResetKinerja() {
    const form = document.getElementById('formResetKinerja');
    const bulanSelect = form.querySelector('[name="bulan"]');
    const tahunInput = form.querySelector('[name="tahun"]');
    
    if(!bulanSelect.value || !tahunInput.value) {
        Swal.fire('Peringatan', 'Silakan pilih bulan dan tahun terlebih dahulu!', 'warning');
        return;
    }
    
    const namaBulan = bulanSelect.options[bulanSelect.selectedIndex].text;
    const tahun = tahunInput.value;
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: `Data Target Bulanan, Kegiatan Harian, dan Nilai Rekap untuk <b>${namaBulan} ${tahun}</b> akan <b>Dihapus Permanen</b>!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
<?= $this->endSection() ?>