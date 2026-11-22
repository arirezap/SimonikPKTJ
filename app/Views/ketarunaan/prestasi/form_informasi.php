<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= (($mode ?? 'create') === 'edit') ? 'Ubah Informasi Prestasi' : 'Tambah Prestasi' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/prestasi/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$mode       = $mode ?? 'create';
$isEdit     = ($mode === 'edit');
$prestasi   = $prestasi ?? [];
$refs       = $refs ?? [];
$idPrestasi = (int) ($prestasi['id'] ?? 0);

$formAction = $isEdit && $idPrestasi > 0
    ? base_url('ketarunaan/prestasi/update/' . $idPrestasi)
    : base_url('ketarunaan/prestasi/simpan');

$valNama              = (string) old('nama_prestasi', $prestasi['nama_prestasi'] ?? '');
$valJenis             = (string) old('jenis_prestasi', $prestasi['jenis_prestasi'] ?? 'akademik');
$valBidang            = (string) old('bidang_prestasi', $prestasi['bidang_prestasi'] ?? 'Inovasi Keselamatan Jalan');
$valTingkat           = (string) old('tingkat_prestasi', $prestasi['tingkat_prestasi'] ?? 'nasional');
$valPeringkat         = (string) old('peringkat', $prestasi['peringkat'] ?? 'Juara I');
$valPenyelenggara     = (string) old('penyelenggara', $prestasi['penyelenggara'] ?? '');
$valLokasi            = (string) old('lokasi_kegiatan', $prestasi['lokasi_kegiatan'] ?? '');
$valTglMulai          = (string) old('tanggal_mulai', $prestasi['tanggal_mulai'] ?? date('Y-m-d'));
$valTglSelesai        = (string) old('tanggal_selesai', $prestasi['tanggal_selesai'] ?? date('Y-m-d'));
$defaultKeikutsertaan = !empty($isSelfTaruna) ? 'individu' : 'tim';
$valKeikutsertaan     = (string) old('jenis_keikutsertaan', $prestasi['jenis_keikutsertaan'] ?? $defaultKeikutsertaan);
$valNamaTim           = (string) old('nama_tim', $prestasi['nama_tim'] ?? '');
$valPembimbing        = (string) old('pembimbing', $prestasi['pembimbing'] ?? 'Pembina Ketarunaan');
$valUnit              = (string) old('unit_pemilik', $prestasi['unit_pemilik'] ?? 'Ketarunaan');
$valSumber            = (string) old('sumber_data', $prestasi['sumber_data'] ?? 'Piagam Penghargaan');
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isEdit ? 'Ubah Informasi' : 'Tambah Prestasi' ?></li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1"><?= $isEdit ? 'Ubah Informasi Prestasi' : 'Tambah Prestasi' ?></h1>
            <p class="text-secondary mb-0 small">Lengkapi informasi prestasi yang diperoleh taruna.</p>
        </div>
        <div>
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Daftar Prestasi
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if ($isEdit && ($prestasi['status_validasi'] ?? '') === 'perlu_perbaikan' && !empty($prestasi['catatan_validasi'])): ?>
        <div class="alert alert-warning border-0 rounded-3 mb-4 px-3 py-2 small" role="alert">
            <strong class="d-block text-dark">Catatan Perbaikan Validator</strong>
            <div class="text-dark mt-1"><?= esc((string) $prestasi['catatan_validasi']) ?></div>
        </div>
    <?php elseif ($isEdit && ($prestasi['status_validasi'] ?? '') === 'tervalidasi'): ?>
        <div class="alert alert-info border-0 rounded-3 mb-4 px-3 py-2 small" role="alert">
            Prestasi ini sudah tervalidasi. Menyimpan perubahan akan mengembalikan status ke <strong>Draf</strong> untuk diajukan validasi ulang.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= esc($formAction) ?>" id="formInformasiPrestasi" class="bento-card ecc-pr-card pr-form-scope">
        <?= csrf_field() ?>

        <!-- Navigasi 3 Tahap -->
        <nav class="ecc-wizard-nav" aria-label="Tahapan Pengisian Prestasi">
            <span class="ecc-wizard-step active" aria-current="step">1. Informasi Prestasi</span>
            <?php if ($isEdit && $idPrestasi > 0): ?>
                <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="ecc-wizard-step">2. Peserta</a>
                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="ecc-wizard-step">3. Dokumen Bukti</a>
            <?php else: ?>
                <span class="ecc-wizard-step disabled-step" aria-disabled="true">2. Peserta</span>
                <span class="ecc-wizard-step disabled-step" aria-disabled="true">3. Dokumen Bukti</span>
            <?php endif; ?>
        </nav>

        <div class="row g-4">
            <!-- Kolom Kiri -->
            <div class="col-12 col-lg-6">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="nama_prestasi" class="form-label text-secondary fw-semibold mb-1 small">
                            Nama Prestasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_prestasi" name="nama_prestasi" value="<?= esc($valNama) ?>" maxlength="200" class="form-control ecc-control-sm" placeholder="Contoh: Lomba Inovasi Transportasi" required>
                    </div>

                    <div>
                        <label for="jenis_prestasi" class="form-label text-secondary fw-semibold mb-1 small">
                            Jenis Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="jenis_prestasi" name="jenis_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['jenis_catalog'] ?? []) as $jKey => $jLabel): ?>
                                <option value="<?= esc($jKey) ?>" <?= ($valJenis === $jKey) ? 'selected' : '' ?>><?= esc($jLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="bidang_prestasi" class="form-label text-secondary fw-semibold mb-1 small">
                            Bidang Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="bidang_prestasi" name="bidang_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['bidang_options'] ?? []) as $bid): ?>
                                <option value="<?= esc($bid) ?>" <?= ($valBidang === $bid) ? 'selected' : '' ?>><?= esc($bid) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="tingkat_prestasi" class="form-label text-secondary fw-semibold mb-1 small">
                            Tingkat Prestasi <span class="text-danger">*</span>
                        </label>
                        <select id="tingkat_prestasi" name="tingkat_prestasi" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['tingkat_catalog'] ?? []) as $tKey => $tLabel): ?>
                                <option value="<?= esc($tKey) ?>" <?= ($valTingkat === $tKey) ? 'selected' : '' ?>><?= esc($tLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="peringkat" class="form-label text-secondary fw-semibold mb-1 small">
                            Peringkat <span class="text-danger">*</span>
                        </label>
                        <select id="peringkat" name="peringkat" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['peringkat_options'] ?? []) as $prk): ?>
                                <option value="<?= esc($prk) ?>" <?= ($valPeringkat === $prk) ? 'selected' : '' ?>><?= esc($prk) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="penyelenggara" class="form-label text-secondary fw-semibold mb-1 small">
                            Penyelenggara <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="penyelenggara" name="penyelenggara" value="<?= esc($valPenyelenggara) ?>" maxlength="150" class="form-control ecc-control-sm" placeholder="Contoh: Forum Keselamatan Transportasi" required>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-12 col-lg-6">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label for="lokasi_kegiatan" class="form-label text-secondary fw-semibold mb-1 small">
                            Lokasi Kegiatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="lokasi_kegiatan" name="lokasi_kegiatan" value="<?= esc($valLokasi) ?>" maxlength="150" class="form-control ecc-control-sm" placeholder="Contoh: Jakarta" required>
                    </div>

                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <label for="tanggal_mulai" class="form-label text-secondary fw-semibold mb-1 small">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($valTglMulai) ?>" class="form-control ecc-control-sm ecc-num bg-white" placeholder="Pilih tanggal mulai">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="tanggal_selesai" class="form-label text-secondary fw-semibold mb-1 small">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($valTglSelesai) ?>" class="form-control ecc-control-sm ecc-num bg-white" placeholder="Pilih tanggal selesai">
                        </div>
                    </div>

                    <div>
                        <span class="form-label text-secondary fw-semibold d-block mb-1 small">
                            Jenis Keikutsertaan <span class="text-danger">*</span>
                        </span>
                        <div class="d-flex align-items-center gap-4 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_keikutsertaan" id="ikutTim" value="tim" <?= ($valKeikutsertaan === 'tim') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium small" for="ikutTim">Tim</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_keikutsertaan" id="ikutIndividu" value="individu" <?= ($valKeikutsertaan === 'individu') ? 'checked' : '' ?>>
                                <label class="form-check-label fw-medium small" for="ikutIndividu">Individu</label>
                            </div>
                        </div>
                    </div>

                    <div id="namaTimGroup">
                        <label for="nama_tim" class="form-label text-secondary fw-semibold mb-1 small">
                            Nama Tim <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="nama_tim" name="nama_tim" value="<?= esc($valNamaTim) ?>" maxlength="120" class="form-control ecc-control-sm" placeholder="Contoh: Tim Inovasi RSTJ">
                    </div>

                    <div>
                        <label for="pembimbing" class="form-label text-secondary fw-semibold mb-1 small">
                            Pembimbing <span class="text-danger">*</span>
                        </label>
                        <select id="pembimbing" name="pembimbing" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['pembimbing_options'] ?? []) as $pmb): ?>
                                <option value="<?= esc($pmb) ?>" <?= ($valPembimbing === $pmb) ? 'selected' : '' ?>><?= esc($pmb) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-12 col-sm-6">
                            <label for="unit_pemilik" class="form-label text-secondary fw-semibold mb-1 small">
                                Unit Pemilik <span class="text-danger">*</span>
                            </label>
                            <?php if (!empty($isSelfTaruna)): ?>
                                <input type="hidden" name="unit_pemilik" value="Ketarunaan">
                                <input type="text" id="unit_pemilik" class="form-control ecc-control-sm bg-light" value="Ketarunaan" readonly tabindex="-1">
                            <?php else: ?>
                                <select id="unit_pemilik" name="unit_pemilik" class="form-select ecc-control-sm" required>
                                    <?php foreach (($refs['unit_options'] ?? []) as $unt): ?>
                                        <option value="<?= esc($unt) ?>" <?= ($valUnit === $unt) ? 'selected' : '' ?>><?= esc($unt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label for="sumber_data" class="form-label text-secondary fw-semibold mb-1 small">
                                Sumber Data <span class="text-danger">*</span>
                            </label>
                            <select id="sumber_data" name="sumber_data" class="form-select ecc-control-sm" required>
                                <?php foreach (($refs['sumber_options'] ?? []) as $smb): ?>
                                    <option value="<?= esc($smb) ?>" <?= ($valSumber === $smb) ? 'selected' : '' ?>><?= esc($smb) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
                        <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
                        <span><?= !empty($isSelfTaruna) ? 'Akun Anda otomatis tercatat sebagai peserta prestasi.' : 'Peserta tim ditambahkan pada tahap Peserta.' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3 border-top">
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Batal</a>
            <?php if ($isEdit): ?>
                <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl js-submit-info">
                    Simpan Draf
                </button>
            <?php endif; ?>
            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl js-submit-info">
                Berikutnya: Peserta <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var radioTim      = document.getElementById('ikutTim');
    var radioIndividu = document.getElementById('ikutIndividu');
    var namaTimGroup  = document.getElementById('namaTimGroup');
    var namaTimInput  = document.getElementById('nama_tim');
    var tglMulaiEl    = document.getElementById('tanggal_mulai');
    var tglSelesaiEl  = document.getElementById('tanggal_selesai');
    var fpMulai       = null;
    var fpSelesai     = null;

    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        if (tglMulaiEl) {
            fpMulai = flatpickr(tglMulaiEl, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                onReady: function (d, s, inst) {
                    if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Mulai');
                }
            });
        }
        if (tglSelesaiEl) {
            fpSelesai = flatpickr(tglSelesaiEl, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                onReady: function (d, s, inst) {
                    if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Selesai');
                }
            });
        }
    }

    function syncTimField() {
        var isTim = radioTim && radioTim.checked;
        if (namaTimGroup) {
            namaTimGroup.classList.toggle('d-none', !isTim);
        }
        if (namaTimInput) {
            namaTimInput.required = !!isTim;
        }
    }

    if (radioTim) radioTim.addEventListener('change', syncTimField);
    if (radioIndividu) radioIndividu.addEventListener('change', syncTimField);
    syncTimField();

    function showWarn(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(text);
        }
    }

    var form = document.getElementById('formInformasiPrestasi');
    var lastAction = 'next';
    document.querySelectorAll('.js-submit-info').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'next';
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            var namaEl = document.getElementById('nama_prestasi');
            var penyEl = document.getElementById('penyelenggara');
            var lokEl  = document.getElementById('lokasi_kegiatan');
            var mulai   = tglMulaiEl ? tglMulaiEl.value.trim() : '';
            var selesai = tglSelesaiEl ? tglSelesaiEl.value.trim() : '';

            if (namaEl && namaEl.value.trim().length < 3) {
                e.preventDefault();
                namaEl.focus();
                showWarn('Nama Prestasi Belum Lengkap', 'Isi nama prestasi minimal 3 karakter.');
                return;
            }
            if (penyEl && penyEl.value.trim() === '') {
                e.preventDefault();
                penyEl.focus();
                showWarn('Penyelenggara Wajib Diisi', 'Isi nama penyelenggara kegiatan.');
                return;
            }
            if (lokEl && lokEl.value.trim() === '') {
                e.preventDefault();
                lokEl.focus();
                showWarn('Lokasi Wajib Diisi', 'Isi lokasi kegiatan terlebih dahulu.');
                return;
            }
            if (!mulai) {
                e.preventDefault();
                if (fpMulai && fpMulai.altInput) fpMulai.altInput.focus();
                showWarn('Tanggal Mulai Wajib Diisi', 'Pilih tanggal mulai kegiatan terlebih dahulu.');
                return;
            }
            if (!selesai) {
                e.preventDefault();
                if (fpSelesai && fpSelesai.altInput) fpSelesai.altInput.focus();
                showWarn('Tanggal Selesai Wajib Diisi', 'Pilih tanggal selesai kegiatan terlebih dahulu.');
                return;
            }
            if (selesai < mulai) {
                e.preventDefault();
                if (fpSelesai && fpSelesai.altInput) fpSelesai.altInput.focus();
                showWarn('Rentang Tanggal Tidak Valid', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
                return;
            }
            if (radioTim && radioTim.checked && namaTimInput && namaTimInput.value.trim() === '') {
                e.preventDefault();
                namaTimInput.focus();
                showWarn('Nama Tim Wajib Diisi', 'Isi nama tim untuk keikutsertaan tim.');
                return;
            }

            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            document.querySelectorAll('.js-submit-info').forEach(function (btn) {
                btn.disabled = true;
            });
            var hiddenAksi = form.querySelector('input[name="aksi_submit"][data-generated="1"]');
            if (!hiddenAksi) {
                hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = 'aksi_submit';
                hiddenAksi.setAttribute('data-generated', '1');
                form.appendChild(hiddenAksi);
            }
            hiddenAksi.value = aksi;
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.js-submit-info').forEach(function (btn) {
                btn.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
