<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title ?? 'Edit Pengguna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/vendor/select2/select2.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>" />
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
        <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Edit Pengguna & Hierarki</h1>
        <a href="<?= site_url('users') . (!empty($query_string) ? '?' . $query_string : '') ?>" class="btn btn-sm btn-outline-secondary fw-semibold">
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

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show small mb-3 shadow-sm py-2 px-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <form action="<?= site_url('users/update') ?>" method="post" autocomplete="off" id="formUserEdit">
                <?= csrf_field() ?>
                
                <input type="hidden" name="return_qs" value="<?= esc($query_string ?? '') ?>">
                <input type="hidden" name="id" value="<?= esc($user['id']) ?>">

                <div class="row g-4">
                    <!-- SECTION A: AKUN -->
                    <div class="col-lg-6">
                        <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
                            <h6 class="fw-bold text-primary mb-3">A. Informasi Akun</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control form-control-sm" value="<?= esc($user['username']) ?>" placeholder="Username login..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm" value="<?= esc($user['email']) ?>" placeholder="nama@instansi.go.id" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Kata Sandi Baru <small class="text-muted fw-normal">(Kosongkan jika tidak diubah)</small></label>
                                <input type="password" name="password" class="form-control form-control-sm" placeholder="Isi hanya jika ingin mengubah kata sandi..." autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Role Primer</label>
                                <select name="role" class="form-select form-select-sm">
                                    <optgroup label="Peran Organisasi &amp; Kinerja">
                                        <option value="direktur" <?= $user['role'] == 'direktur' ? 'selected' : '' ?>>Direktur (Level 1)</option>
                                        <option value="wadir" <?= $user['role'] == 'wadir' ? 'selected' : '' ?>>Wakil Direktur (Level 2)</option>
                                        <option value="kabag_aak" <?= $user['role'] == 'kabag_aak' ? 'selected' : '' ?>>Kabag AAK (Level 3)</option>
                                        <option value="kabag_kuk" <?= $user['role'] == 'kabag_kuk' ? 'selected' : '' ?>>Kabag KUK (Level 3)</option>
                                        <option value="manajemen" <?= $user['role'] == 'manajemen' ? 'selected' : '' ?>>Kanit / Katim / Kaprodi (Level 4)</option>
                                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Staf / Pelaksana (Level 5)</option>
                                        <option value="tugas_belajar" <?= $user['role'] == 'tugas_belajar' ? 'selected' : '' ?>>Tugas Belajar (Level 5)</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator (Sistem)</option>
                                    </optgroup>
                                    <optgroup label="Peran Ketarunaan &amp; Alumni">
                                        <option value="ketarunaan" <?= $user['role'] == 'ketarunaan' ? 'selected' : '' ?>>Ketarunaan (PIC Data Taruna)</option>
                                        <option value="ketarunaan_validator" <?= $user['role'] == 'ketarunaan_validator' ? 'selected' : '' ?>>Validator Ketarunaan</option>
                                        <option value="pusbangkar" <?= $user['role'] == 'pusbangkar' ? 'selected' : '' ?>>Pusbangkar (Karakter &amp; Disiplin)</option>
                                        <option value="karir_alumni" <?= $user['role'] == 'karir_alumni' ? 'selected' : '' ?>>Karier &amp; Alumni</option>
                                        <option value="kerjasama" <?= $user['role'] == 'kerjasama' ? 'selected' : '' ?>>Kerjasama (Mitra &amp; Pengguna Lulusan)</option>
                                        <option value="taruna" <?= $user['role'] == 'taruna' ? 'selected' : '' ?>>Taruna (Portal Mandiri)</option>
                                        <option value="alumni" <?= $user['role'] == 'alumni' ? 'selected' : '' ?>>Alumni (Portal Mandiri)</option>
                                        <option value="pengguna_lulusan" <?= $user['role'] == 'pengguna_lulusan' ? 'selected' : '' ?>>Pengguna Lulusan</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="p-3 bg-white border rounded-3">
                                <label class="form-label small fw-bold text-dark mb-1">Role Tambahan (Sekunder)</label>
                                <div class="form-text small mb-2" style="font-size: 0.72rem;">Centang jika pegawai memiliki peran rangkap.</div>
                                
                                <?php 
                                    $availableSecondary = [
                                        'kepegawaian'          => 'Kepegawaian (Rekap & Approval)',
                                        'dosen'                => 'Dosen (Profil Akademik & Kepakaran)',
                                        'spm'                  => 'SPM (Penjaminan Mutu)',
                                        'tugas_belajar'        => 'Tugas Belajar (Laporan Studi)',
                                        'pusbangkar'           => 'Pusbangkar (Pengembangan Karakter & Disiplin)',
                                        'ketarunaan'           => 'Ketarunaan (Operator / PIC Data Taruna)',
                                        'ketarunaan_validator' => 'Validator Ketarunaan (Pengesahan Data)',
                                        'karir_alumni'         => 'Karier & Alumni (Tracer Study, Lowongan & Mitra)',
                                        'kerjasama'            => 'Kerjasama (Mitra & Pengguna Lulusan)',
                                        'taruna'               => 'Taruna (Layanan Mandiri)',
                                        'alumni'               => 'Alumni (Karier & Tracer Study)',
                                        'pengguna_lulusan'     => 'Pengguna Lulusan (Survei Mitra Kerja)'
                                    ];
                                ?>
                                
                                <?php foreach ($availableSecondary as $val => $label): ?>
                                    <div class="form-check form-check-inline me-3">
                                        <input class="form-check-input" type="checkbox" name="secondary_roles[]" value="<?= esc($val) ?>" id="role_<?= esc($val) ?>" <?= (in_array($val, $secondary_roles ?? [])) ? 'checked' : '' ?>>
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
                            <h6 class="fw-bold text-success mb-3">B. Data Kepegawaian & Hierarki</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-sm" value="<?= esc($user['nama_lengkap']) ?>" placeholder="Nama lengkap dengan gelar..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark mb-1">NIP</label>
                                <input type="text" name="nip" class="form-control form-control-sm" value="<?= esc($user['nip']) ?>" placeholder="Nomor Induk Pegawai...">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control form-control-sm" value="<?= esc($user['jabatan']) ?>" placeholder="Nama jabatan...">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Pangkat / Golongan</label>
                                    <input type="text" name="pangkat" class="form-control form-control-sm" value="<?= esc($user['pangkat']) ?>" placeholder="Pangkat/Golongan...">
                                </div>
                            </div>
                            <?php
                                $selectedUnitIdEdit = '';
                                if (!empty($unit_kerja_list)) {
                                    foreach ($unit_kerja_list as $ukOpt) {
                                        if ((!empty($user['unit_id']) && $user['unit_id'] == $ukOpt['id']) || ($user['unit'] == $ukOpt['nama_unit'])) {
                                            $selectedUnitIdEdit = (string) $ukOpt['id'];
                                            break;
                                        }
                                    }
                                }
                                $selectedAtasanIdEdit = !empty($user['atasan_id']) ? (string) $user['atasan_id'] : '';
                                $bulanIndoEdit = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $curMonthEdit  = (int) old('bulan_efektif', date('n'));
                                $curYearEdit   = (int) old('tahun_efektif', date('Y'));
                                $curModeEdit   = (string) old('mode_perubahan', 'mutasi');
                            ?>
                            <div class="mb-3">
                                <label for="selectUnitEdit" class="form-label small fw-bold text-dark mb-1">Unit Kerja</label>
                                <select name="unit_id" id="selectUnitEdit" class="form-select form-select-sm" data-initial-unit="<?= esc($selectedUnitIdEdit) ?>">
                                    <option value="">Pilih Unit Kerja</option>
                                    <?php if (!empty($unit_kerja_list)): ?>
                                        <?php foreach ($unit_kerja_list as $unit_kerja): ?>
                                            <option value="<?= esc($unit_kerja['id']) ?>" <?= ($selectedUnitIdEdit === (string) $unit_kerja['id']) ? 'selected' : '' ?>>
                                                <?= esc($unit_kerja['nama_unit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="p-3 bg-white border rounded-3 mb-3">
                                <label for="selectAtasanEdit" class="form-label small fw-bold text-dark mb-1">Atasan Langsung (Pejabat Penilai)</label>
                                <select name="atasan_id" class="form-select form-select-sm select2" id="selectAtasanEdit" data-initial-atasan="<?= esc($selectedAtasanIdEdit) ?>">
                                    <option value="">Tanpa Atasan (Misal: Direktur)</option>
                                    <?php foreach ($potential_bosses as $boss): ?>
                                        <option value="<?= $boss['id'] ?>" <?= ($user['atasan_id'] == $boss['id']) ? 'selected' : '' ?>>
                                            <?= esc($boss['nama_lengkap']) ?> - <?= esc($boss['jabatan'] ?? 'Tanpa Jabatan') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small" style="font-size: 0.72rem;">
                                    Atasan akan menjadi pejabat penilai kinerja dan pihak kedua di kontrak kinerja.
                                </div>
                            </div>

                            <!-- PANEL KONTEKSTUAL: PENGATURAN RIWAYAT MUTASI / ROTASI (MUNCUL SAAT UNIT ATAU ATASAN BERUBAH) -->
                            <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3 mb-3 d-none" id="panelRiwayatMutasiEdit">
                                <div class="fw-bold text-primary-emphasis small mb-2">Pengaturan Riwayat Penugasan</div>
                                <div class="d-flex flex-column gap-2 mb-2">
                                    <label class="d-flex align-items-start gap-2 p-2 bg-white border rounded-3 mb-0" style="cursor: pointer;">
                                        <input type="radio" name="mode_perubahan" value="mutasi" class="form-check-input mt-1 edit-mode-mutasi" <?= $curModeEdit !== 'koreksi' ? 'checked' : '' ?>>
                                        <span class="small">
                                            <strong class="d-block text-dark">Mutasi / Rotasi Baru (Simpan Riwayat Lama)</strong>
                                            <span class="text-muted">Penilaian bulan sebelumnya tetap tinggal di unit &amp; atasan lama.</span>
                                        </span>
                                    </label>
                                    <label class="d-flex align-items-start gap-2 p-2 bg-white border rounded-3 mb-0" style="cursor: pointer;">
                                        <input type="radio" name="mode_perubahan" value="koreksi" class="form-check-input mt-1 edit-mode-mutasi" <?= $curModeEdit === 'koreksi' ? 'checked' : '' ?>>
                                        <span class="small">
                                            <strong class="d-block text-dark">Koreksi Salah Input</strong>
                                            <span class="text-muted">Hanya memperbarui profil aktif tanpa memotong riwayat penugasan.</span>
                                        </span>
                                    </label>
                                </div>
                                <div id="wrapperPeriodeMutasiEdit" class="mt-2 pt-2 border-top border-primary-subtle">
                                    <div class="row g-2 mb-2">
                                        <div class="col-7">
                                            <label for="editBulanEfektif" class="form-label small fw-bold text-dark mb-1">Mulai Efektif Bulan</label>
                                            <select name="bulan_efektif" id="editBulanEfektif" class="form-select form-select-sm">
                                                <?php foreach ($bulanIndoEdit as $idxBln => $nmBln): ?>
                                                    <option value="<?= $idxBln + 1 ?>" <?= ($curMonthEdit === $idxBln + 1) ? 'selected' : '' ?>><?= esc($nmBln) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label for="editTahunEfektif" class="form-label small fw-bold text-dark mb-1">Tahun</label>
                                            <input type="number" name="tahun_efektif" id="editTahunEfektif" class="form-control form-control-sm num-tabular" value="<?= $curYearEdit ?>" min="2020" max="2099">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="editCatatanMutasi" class="form-label small fw-bold text-dark mb-1">Catatan / Nomor SK (Opsional)</label>
                                        <input type="text" name="catatan_mutasi" id="editCatatanMutasi" class="form-control form-control-sm" maxlength="200" value="<?= esc(old('catatan_mutasi', '')) ?>" placeholder="Contoh: Mutasi internal sesuai SK...">
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-white border rounded-3">
                                <label class="form-label small fw-bold text-dark mb-1">Status Kepegawaian & Akses</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">Status Pegawai</label>
                                        <select name="status_pegawai" id="statusPegawaiEdit" class="form-select form-select-sm">
                                            <?php $curStatus = old('status_pegawai', $user['status_pegawai'] ?? 'aktif'); ?>
                                            <option value="aktif" <?= $curStatus === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="mutasi_keluar" <?= $curStatus === 'mutasi_keluar' ? 'selected' : '' ?>>Mutasi Keluar</option>
                                            <option value="pensiun" <?= $curStatus === 'pensiun' ? 'selected' : '' ?>>Pensiun</option>
                                            <option value="nonaktif" <?= $curStatus === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6" id="wrapperTmtSelesaiEdit" style="<?= $curStatus === 'aktif' ? 'display: none;' : '' ?>">
                                        <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">Tanggal Akhir Tugas (TMT)</label>
                                        <input type="date" name="tmt_selesai" class="form-control form-control-sm" value="<?= old('tmt_selesai', $user['tmt_selesai'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <label class="form-label small text-muted mb-1" style="font-size: 0.75rem;">Dispensasi Pengisian Lampau (Khusus)</label>
                                    <input type="date" name="dispensasi_lampau_sampai" class="form-control form-control-sm" value="<?= old('dispensasi_lampau_sampai', $user['dispensasi_lampau_sampai'] ?? '') ?>">
                                    <div class="form-text small" style="font-size: 0.72rem;">
                                        Jika diisi, akun ini diizinkan menginput laporan tanggal lampau sampai batas tanggal yang ditentukan.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="<?= site_url('users') . (!empty($query_string) ? '?' . $query_string : '') ?>" class="btn btn-secondary btn-sm rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm px-4" id="btnSubmitUserEdit">
                        <i class="bi bi-save me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- DANGER ZONE: Reset Kinerja Bulanan -->
    <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden border-start border-danger border-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-danger-subtle text-danger rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-danger mb-0">Zona Bahaya: Reset Kinerja Bulanan</h6>
                    <small class="text-muted">Tindakan ini menghapus Target Bulanan, Kegiatan Harian, dan Nilai Rekap pada bulan terpilih.</small>
                </div>
            </div>

            <form action="<?= site_url('users/reset-kinerja') ?>" method="post" id="formResetKinerja">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= esc($user['id']) ?>">
                
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Bulan</label>
                        <select name="bulan" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Bulan --</option>
                            <?php 
                                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                foreach($bulanIndo as $idx => $bln): 
                            ?>
                                <option value="<?= $idx + 1 ?>"><?= $bln ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">Tahun</label>
                        <input type="number" name="tahun" class="form-control form-control-sm" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold rounded-3" onclick="confirmResetKinerja()">
                            <i class="bi bi-trash3-fill me-1"></i> Reset Kinerja Bulanan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('#selectAtasanEdit').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- Pilih Atasan --'
        });
    }

    function toggleTmtField() {
        var status = $('#statusPegawaiEdit').val();
        if (status === 'aktif') {
            $('#wrapperTmtSelesaiEdit').hide();
        } else {
            $('#wrapperTmtSelesaiEdit').show();
        }
    }
    $('#statusPegawaiEdit').on('change', toggleTmtField);
    toggleTmtField();

    function syncMutasiPanel() {
        var unitEl = $('#selectUnitEdit');
        var atasanEl = $('#selectAtasanEdit');
        var initUnit = String(unitEl.data('initial-unit') ?? '');
        var initAtasan = String(atasanEl.data('initial-atasan') ?? '');
        var curUnit = String(unitEl.val() ?? '');
        var curAtasan = String(atasanEl.val() ?? '');
        var changed = (curUnit !== initUnit) || (curAtasan !== initAtasan);

        $('#panelRiwayatMutasiEdit').toggleClass('d-none', !changed);
        var isMutasi = $('input[name="mode_perubahan"]:checked').val() !== 'koreksi';
        $('#wrapperPeriodeMutasiEdit').toggle(isMutasi);
    }

    $('#selectUnitEdit, #selectAtasanEdit').on('change', syncMutasiPanel);
    $(document).on('change', '.edit-mode-mutasi', syncMutasiPanel);
    syncMutasiPanel();

    $('#formUserEdit').on('submit', function() {
        $('#btnSubmitUserEdit').html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...').prop('disabled', true);
    });
});

function confirmResetKinerja() {
    const form = document.getElementById('formResetKinerja');
    const bulanSelect = form.querySelector('[name="bulan"]');
    const tahunInput = form.querySelector('[name="tahun"]');
    
    if(!bulanSelect.value || !tahunInput.value) {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Peringatan', 'Silakan pilih bulan dan tahun terlebih dahulu.', 'warning');
        } else {
            alert('Silakan pilih bulan dan tahun terlebih dahulu.');
        }
        return;
    }
    
    const namaBulan = bulanSelect.options[bulanSelect.selectedIndex].text;
    const tahun = tahunInput.value;
    const userName = <?= json_encode($user['nama_lengkap']) ?>;
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Kinerja Bulanan?',
            html: `Data kinerja <b>${userName}</b> pada <b>${namaBulan} ${tahun}</b> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Ya, Reset Kinerja',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(`Reset kinerja ${userName} pada ${namaBulan} ${tahun}?`)) {
            form.submit();
        }
    }
}
</script>
<?= $this->endSection() ?>