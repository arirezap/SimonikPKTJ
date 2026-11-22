<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= (($mode ?? 'create') === 'edit') ? 'Koreksi Capaian Taruna' : 'Tambah Capaian Taruna' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/rekam_jejak/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$mode           = $mode ?? 'create';
$isEdit         = ($mode === 'edit');
$capaian        = $capaian ?? [];
$selectedTaruna = $selectedTaruna ?? null;
$tarunaList     = $tarunaList ?? [];
$refs           = $refs ?? [];

$nomorTarunaTerpilih = (string) old('nomor_taruna', $capaian['nomor_taruna'] ?? ($selectedTaruna['nomor_taruna'] ?? ''));
$backUrl = $isEdit && !empty($capaian['id'])
    ? base_url('ketarunaan/rekam-jejak/capaian/' . (int) $capaian['id'])
    : ($nomorTarunaTerpilih !== ''
        ? base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTarunaTerpilih) . '?tab=capaian')
        : base_url('ketarunaan/rekam-jejak'));

$formAction = $isEdit && !empty($capaian['id'])
    ? base_url('ketarunaan/rekam-jejak/update-capaian/' . (int) $capaian['id'])
    : base_url('ketarunaan/rekam-jejak/simpan-capaian');
?>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                <?php if (empty($isSelfTaruna)): ?>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="text-decoration-none text-secondary">Rekam Jejak Taruna</a></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?= esc($backUrl) ?>" class="text-decoration-none text-secondary">Rekam Jejak Saya</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isEdit ? 'Koreksi Capaian' : 'Tambah Capaian' ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1"><?= $isEdit ? 'Koreksi Capaian Taruna' : 'Tambah Capaian Taruna' ?></h1>
        <p class="text-secondary mb-0 small">Catat capaian kompetensi, organisasi, atau kegiatan pengembangan diri pada rekam jejak taruna.</p>
    </div>
    <div>
        <a href="<?= esc($backUrl) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
            <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Rekam Jejak
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span class="small"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (!empty($selectedTaruna)): ?>
    <?php
    $namaTarunaHeader = (string) ($selectedTaruna['nama_lengkap'] ?? '-');
    $inisialHeader    = strtoupper(mb_substr(trim($namaTarunaHeader), 0, 1));
    $fotoHeaderFile   = basename((string) ($selectedTaruna['foto'] ?? ''));
    $fotoHeaderUrl    = ($fotoHeaderFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $fotoHeaderFile))
        ? base_url('assets/uploads/taruna/' . $fotoHeaderFile)
        : null;
    $angkatanHeader   = !empty($selectedTaruna['angkatan'])
        ? ketarunaan_format_angkatan([
            'tahun_masuk' => $selectedTaruna['angkatan'],
            'kode_romawi' => $selectedTaruna['angkatan_romawi'] ?? '',
        ])
        : '-';
    ?>
    <div class="bento-card ecc-rj-card mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-4 d-flex align-items-center gap-3">
                <div class="ecc-avatar-lg">
                    <?php if ($fotoHeaderUrl !== null): ?>
                        <img src="<?= esc($fotoHeaderUrl) ?>" alt="Foto <?= esc($namaTarunaHeader) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= esc($inisialHeader) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1"><?= esc($namaTarunaHeader) ?></h2>
                    <div class="text-secondary small">Rekam Jejak Terintegrasi</div>
                </div>
            </div>
            <div class="col-6 col-md-2 border-md-start">
                <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
                <div class="fw-bold text-dark ecc-num small"><?= esc((string) $selectedTaruna['nomor_taruna']) ?></div>
            </div>
            <div class="col-6 col-md-2 border-md-start">
                <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
                <div class="fw-bold text-dark small"><?= esc((string) ($selectedTaruna['kode_prodi'] ?? '-')) ?></div>
            </div>
            <div class="col-6 col-md-2 border-md-start">
                <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
                <div class="fw-bold text-dark ecc-num small"><?= esc($angkatanHeader) ?></div>
            </div>
            <div class="col-6 col-md-2 border-md-start">
                <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
                <div><?= ketarunaan_status_badge((string) ($selectedTaruna['status_terkini'] ?? 'aktif')) ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($formAction) ?>" id="formCapaianTaruna" class="bento-card ecc-rj-card rj-form-scope">
    <?= csrf_field() ?>

    <!-- Bagian 1: Informasi Capaian -->
    <div class="ecc-section-title">Informasi Capaian</div>
    <div class="row g-3 mb-4">
        <?php if (!empty($selectedTaruna)): ?>
            <div class="col-12 col-md-6">
                <label for="nomor_taruna_ro" class="form-label text-secondary fw-semibold mb-1 small">Nomor Taruna</label>
                <input type="text" id="nomor_taruna_ro" class="form-control ecc-control-sm bg-light ecc-num" value="<?= esc((string) $selectedTaruna['nomor_taruna']) ?>" readonly>
                <input type="hidden" name="nomor_taruna" value="<?= esc((string) $selectedTaruna['nomor_taruna']) ?>">
            </div>
            <div class="col-12 col-md-6">
                <label for="nama_taruna_ro" class="form-label text-secondary fw-semibold mb-1 small">Nama Taruna</label>
                <input type="text" id="nama_taruna_ro" class="form-control ecc-control-sm bg-light" value="<?= esc((string) $selectedTaruna['nama_lengkap']) ?>" readonly>
            </div>
        <?php else: ?>
            <div class="col-12">
                <label for="nomor_taruna" class="form-label text-secondary fw-semibold mb-1 small">Pilih Taruna <span class="text-danger">*</span></label>
                <select id="nomor_taruna" name="nomor_taruna" class="form-select ecc-control-sm" required>
                    <option value="">-- Pilih Nomor Taruna &amp; Nama --</option>
                    <?php foreach ($tarunaList as $tr): ?>
                        <?php
                        $angDropdown = !empty($tr['angkatan'])
                            ? ketarunaan_format_angkatan([
                                'tahun_masuk' => $tr['angkatan'],
                                'kode_romawi' => $tr['angkatan_romawi'] ?? '',
                            ])
                            : '-';
                        ?>
                        <option value="<?= esc($tr['nomor_taruna']) ?>" <?= ($nomorTarunaTerpilih === $tr['nomor_taruna']) ? 'selected' : '' ?>>
                            <?= esc($tr['nomor_taruna']) ?> - <?= esc($tr['nama_lengkap']) ?> (<?= esc($tr['kode_prodi'] ?? '-') ?> / <?= esc($angDropdown) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php
        $defaultSemester   = max(1, min(8, (int) ($selectedTaruna['semester_aktif'] ?? 1)));
        $valKategori       = (string) old('kategori', $capaian['kategori'] ?? 'kompetensi_sertifikasi');
        $valNamaCapaian    = (string) old('nama_capaian', $capaian['nama_capaian'] ?? '');
        $valTglKegiatan    = (string) old('tanggal_kegiatan', $capaian['tanggal_kegiatan'] ?? date('Y-m-d'));
        $valTahunAkademik  = (string) old('tahun_akademik_id', (string) ($capaian['tahun_akademik_id'] ?? ''));
        $valSemester       = (int) old('semester', (string) ($capaian['semester'] ?? $defaultSemester));
        $valPenyelenggara  = (string) old('penyelenggara', $capaian['penyelenggara'] ?? '');
        $valTingkat        = (string) old('tingkat_kegiatan', $capaian['tingkat_kegiatan'] ?? 'Nasional');
        $valUraian         = (string) old('uraian_capaian', $capaian['uraian_capaian'] ?? '');
        ?>

        <div class="col-12 col-md-4">
            <label for="kategori" class="form-label text-secondary fw-semibold mb-1 small">Kategori <span class="text-danger">*</span></label>
            <select id="kategori" name="kategori" class="form-select ecc-control-sm" required>
                <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                    <option value="<?= esc($kKode) ?>" <?= ($valKategori === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-4">
            <label for="nama_capaian" class="form-label text-secondary fw-semibold mb-1 small">Nama Capaian <span class="text-danger">*</span></label>
            <input type="text" id="nama_capaian" name="nama_capaian" value="<?= esc($valNamaCapaian) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Sertifikasi Keselamatan Jalan" required>
        </div>

        <div class="col-12 col-md-4">
            <label for="tanggal_kegiatan" class="form-label text-secondary fw-semibold mb-1 small">Tanggal Kegiatan <span class="text-danger">*</span></label>
            <input type="text" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?= esc($valTglKegiatan) ?>" class="form-control ecc-control-sm ecc-num bg-white" placeholder="Pilih tanggal kegiatan">
        </div>

        <div class="col-6 col-md-3">
            <label for="tahun_akademik_id" class="form-label text-secondary fw-semibold mb-1 small">Tahun Akademik</label>
            <select id="tahun_akademik_id" name="tahun_akademik_id" class="form-select ecc-control-sm">
                <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                    <option value="<?= (int) $ta['id'] ?>" <?= ($valTahunAkademik === (string) $ta['id'] || ($valTahunAkademik === '' && !empty($ta['is_aktif']))) ? 'selected' : '' ?>>
                        <?= esc($ta['kode_tahun']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label for="semester" class="form-label text-secondary fw-semibold mb-1 small">Semester</label>
            <select id="semester" name="semester" class="form-select ecc-control-sm ecc-num">
                <?php for ($s = 1; $s <= 8; $s++): ?>
                    <option value="<?= $s ?>" <?= ($valSemester === $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-12 col-md-3">
            <label for="penyelenggara" class="form-label text-secondary fw-semibold mb-1 small">Penyelenggara <span class="text-danger">*</span></label>
            <input type="text" id="penyelenggara" name="penyelenggara" value="<?= esc($valPenyelenggara) ?>" class="form-control ecc-control-sm" placeholder="Nama instansi / penyelenggara" required>
        </div>

        <div class="col-12 col-md-3">
            <label for="tingkat_kegiatan" class="form-label text-secondary fw-semibold mb-1 small">Tingkat Kegiatan</label>
            <select id="tingkat_kegiatan" name="tingkat_kegiatan" class="form-select ecc-control-sm">
                <?php foreach (['Kampus / Internal', 'Daerah / Provinsi', 'Nasional', 'Internasional'] as $tk): ?>
                    <option value="<?= esc($tk) ?>" <?= ($valTingkat === $tk) ? 'selected' : '' ?>><?= esc($tk) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="uraian_capaian" class="form-label text-secondary fw-semibold mb-1 small">Uraian Capaian</label>
            <textarea id="uraian_capaian" name="uraian_capaian" rows="2" class="form-control rounded-3 small" placeholder="Jelaskan secara singkat kegiatan atau peran taruna..."><?= esc($valUraian) ?></textarea>
        </div>
    </div>

    <!-- Bagian 2: Hasil dan Sumber Data -->
    <?php
    $valHasil    = (string) old('hasil_capaian', $capaian['hasil_capaian'] ?? 'Kompeten');
    $valNilai    = (string) old('nilai_angka', (string) ($capaian['nilai_angka'] ?? ''));
    $valSatuan   = (string) old('satuan_nilai', $capaian['satuan_nilai'] ?? '');
    $valNoDok    = (string) old('nomor_dokumen', $capaian['nomor_dokumen'] ?? '');
    $valUnit     = (string) old('unit_pemilik', $capaian['unit_pemilik'] ?? 'Ketarunaan');
    $valSumber   = (string) old('sumber_dokumen', $capaian['sumber_dokumen'] ?? 'Sertifikat');
    ?>
    <div class="ecc-section-title">Hasil dan Sumber Data</div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <label for="hasil_capaian" class="form-label text-secondary fw-semibold mb-1 small">Hasil Capaian <span class="text-danger">*</span></label>
            <select id="hasil_capaian" name="hasil_capaian" class="form-select ecc-control-sm" required>
                <?php foreach (['Kompeten', 'Selesai', 'Peserta', 'Pengurus', 'Sekretaris', 'Ketua', 'Lulus'] as $hs): ?>
                    <option value="<?= esc($hs) ?>" <?= ($valHasil === $hs) ? 'selected' : '' ?>><?= esc($hs) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label for="nilai_angka" class="form-label text-secondary fw-semibold mb-1 small">Nilai Angka (Opsional)</label>
            <input type="text" id="nilai_angka" name="nilai_angka" value="<?= esc($valNilai) ?>" class="form-control ecc-control-sm ecc-num" inputmode="decimal" placeholder="Misal: 92,5">
        </div>

        <div class="col-6 col-md-3">
            <label for="satuan_nilai" class="form-label text-secondary fw-semibold mb-1 small">Satuan (Opsional)</label>
            <input type="text" id="satuan_nilai" name="satuan_nilai" value="<?= esc($valSatuan) ?>" class="form-control ecc-control-sm" placeholder="Skor / Poin / Jam">
        </div>

        <div class="col-12 col-md-3">
            <label for="nomor_dokumen" class="form-label text-secondary fw-semibold mb-1 small">Nomor Dokumen</label>
            <input type="text" id="nomor_dokumen" name="nomor_dokumen" value="<?= esc($valNoDok) ?>" class="form-control ecc-control-sm ecc-num" placeholder="Contoh: SERT/026/2026">
        </div>

        <?php if (!empty($isSelfTaruna)): ?>
            <input type="hidden" name="unit_pemilik" value="Ketarunaan">
        <?php else: ?>
            <div class="col-12 col-md-6">
                <label for="unit_pemilik" class="form-label text-secondary fw-semibold mb-1 small">Unit Pemilik Data</label>
                <select id="unit_pemilik" name="unit_pemilik" class="form-select ecc-control-sm">
                    <?php foreach (['Ketarunaan', 'Pusbangkar', 'Program Studi'] as $up): ?>
                        <option value="<?= esc($up) ?>" <?= ($valUnit === $up) ? 'selected' : '' ?>><?= esc($up) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="<?= !empty($isSelfTaruna) ? 'col-12' : 'col-12 col-md-6' ?>">
            <label for="sumber_dokumen" class="form-label text-secondary fw-semibold mb-1 small">Sumber Data</label>
            <select id="sumber_dokumen" name="sumber_dokumen" class="form-select ecc-control-sm">
                <?php foreach (['Sertifikat', 'Surat Tugas', 'Surat Keputusan (SK)', 'Piagam'] as $sd): ?>
                    <option value="<?= esc($sd) ?>" <?= ($valSumber === $sd) ? 'selected' : '' ?>><?= esc($sd) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Bagian 3: Bukti Pendukung (Tautan Google Drive / Cloud) -->
    <?php
    $existingBuktiRaw = trim((string) ($capaian['bukti_berkas'] ?? ''));
    $hasExistingUrl   = ($existingBuktiRaw !== '' && ketarunaan_is_http_url($existingBuktiRaw, 2000));
    $hasLegacyFile    = ($existingBuktiRaw !== '' && !$hasExistingUrl);
    $valBuktiLink     = (string) old('bukti_berkas', $hasExistingUrl ? $existingBuktiRaw : '');
    $existingBuktiUrl = ($existingBuktiRaw !== '' && !empty($capaian['id']))
        ? ketarunaan_resolve_bukti_url($existingBuktiRaw, 'capaian', (int) $capaian['id'], true)
        : null;
    ?>
    <div class="ecc-section-title">Bukti Pendukung</div>
    <div class="ecc-upload-box mb-4">
        <div class="row align-items-start g-3">
            <div class="col-12 <?= $existingBuktiUrl ? 'col-md-8' : '' ?>">
                <label for="bukti_berkas" class="form-label fw-semibold text-dark mb-1 small">
                    Tautan Dokumen Bukti (Google Drive)
                    <?php if (!empty($isSelfTaruna)): ?>
                        <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-secondary-subtle text-secondary"><i class="bi bi-link-45deg" aria-hidden="true"></i></span>
                    <input type="url" id="bukti_berkas" name="bukti_berkas" value="<?= esc($valBuktiLink) ?>" maxlength="255" class="form-control ecc-control-sm" placeholder="https://drive.google.com/file/d/.../view" autocomplete="off">
                    <button type="button" id="btnBersihkanTautan" class="btn btn-outline-secondary ecc-control-sm px-3 rj-touch-ctrl <?= $valBuktiLink === '' ? 'd-none' : '' ?>" aria-label="Bersihkan tautan">
                        Bersihkan
                    </button>
                </div>
                <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                    <?= !empty($isSelfTaruna)
                        ? 'Tempel tautan Google Drive sertifikat atau bukti kegiatan dengan akses lihat aktif (opsional untuk draf).'
                        : 'Tempel tautan Google Drive sertifikat, surat tugas, atau SK kegiatan dengan akses lihat aktif.' ?>
                </div>
            </div>
            <?php if ($existingBuktiUrl): ?>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-2 bg-white d-flex align-items-center justify-content-between gap-2">
                        <div class="text-truncate small">
                            <i class="bi <?= $hasExistingUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-text text-secondary' ?> me-1" aria-hidden="true"></i>
                            <span class="fw-semibold text-dark"><?= esc(ketarunaan_format_bukti_label($existingBuktiRaw)) ?></span>
                        </div>
                        <a href="<?= esc($existingBuktiUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold rj-touch-ctrl d-inline-flex align-items-center flex-shrink-0" style="min-height: 32px; color: #1e40af;">
                            Buka
                        </a>
                    </div>
                    <?php if ($hasLegacyFile): ?>
                        <div class="text-secondary mt-1" style="font-size: 0.75rem;">Berkas lama tersimpan. Isi tautan di atas bila ingin menggantinya.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pt-3 border-top">
        <div class="text-secondary small">
            <i class="bi bi-info-circle text-primary me-1" aria-hidden="true"></i>
            Perubahan data akan tercatat dalam riwayat dan memerlukan validasi sesuai ketentuan.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= esc($backUrl) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold rj-touch-ctrl d-inline-flex align-items-center justify-content-center">Batal</a>
            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl js-submit-capaian">
                Simpan Draf
            </button>
            <button type="submit" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl js-submit-capaian">
                Ajukan Validasi
            </button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
        var selectEl = window.jQuery('#nomor_taruna');
        if (selectEl.length) {
            selectEl.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Nomor Taruna & Nama --'
            });
        }
    }

    var tglInput = document.getElementById('tanggal_kegiatan');
    var fpTanggal = null;
    if (tglInput && typeof flatpickr !== 'undefined') {
        fpTanggal = flatpickr(tglInput, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default',
            onReady: function (selectedDates, dateStr, instance) {
                if (instance && instance.altInput) {
                    instance.altInput.setAttribute('aria-label', 'Tanggal Kegiatan');
                }
            }
        });
    }

    var linkInput = document.getElementById('bukti_berkas');
    var btnClearLink = document.getElementById('btnBersihkanTautan');
    var syncClearBtn = function () {
        if (!btnClearLink || !linkInput) return;
        btnClearLink.classList.toggle('d-none', linkInput.value.trim() === '');
    };
    if (linkInput) {
        linkInput.addEventListener('input', syncClearBtn);
    }
    if (btnClearLink && linkInput) {
        btnClearLink.addEventListener('click', function () {
            linkInput.value = '';
            syncClearBtn();
            linkInput.focus();
        });
    }

    var showWarn = function (title, msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: msg,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(msg);
        }
    };

    var form = document.getElementById('formCapaianTaruna');
    var isSelfTaruna = <?= !empty($isSelfTaruna) ? 'true' : 'false' ?>;
    var hasLegacyFile = <?= !empty($hasLegacyFile) ? 'true' : 'false' ?>;
    var lastClickedAction = 'diajukan';

    document.querySelectorAll('.js-submit-capaian').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastClickedAction = btn.value || 'diajukan';
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastClickedAction;

            if (tglInput && !tglInput.value.trim()) {
                e.preventDefault();
                if (fpTanggal && fpTanggal.altInput) {
                    fpTanggal.altInput.focus();
                } else {
                    tglInput.focus();
                }
                showWarn('Tanggal Kegiatan Wajib Diisi', 'Pilih tanggal kegiatan terlebih dahulu.');
                return;
            }

            var nilaiInput = document.getElementById('nilai_angka');
            if (nilaiInput && nilaiInput.value.trim() !== '') {
                var normalized = nilaiInput.value.trim().replace(',', '.');
                var numVal = Number(normalized);
                if (isNaN(numVal) || numVal < 0 || numVal > 999999.99) {
                    e.preventDefault();
                    nilaiInput.focus();
                    showWarn('Nilai Tidak Valid', 'Nilai angka harus berada pada rentang 0 sampai 999.999,99.');
                    return;
                }
            }

            var rawLink = linkInput ? linkInput.value.trim() : '';
            if (rawLink !== '') {
                if (!/^https?:\/\/\S+/i.test(rawLink) || rawLink.length > 255) {
                    e.preventDefault();
                    linkInput.focus();
                    showWarn('Tautan Tidak Valid', 'Gunakan tautan URL http:// atau https:// yang valid (maksimal 255 karakter).');
                    return;
                }
            } else if (isSelfTaruna && aksi === 'diajukan' && !hasLegacyFile) {
                e.preventDefault();
                if (linkInput) linkInput.focus();
                showWarn('Tautan Bukti Wajib Diisi', 'Tempel tautan Google Drive bukti pendukung sebelum mengajukan validasi.');
                return;
            }

            document.querySelectorAll('.js-submit-capaian').forEach(function (btn) {
                btn.disabled = true;
            });
            if (e.submitter) {
                var hiddenAksi = form.querySelector('input[name="aksi_submit"][data-generated="1"]');
                if (!hiddenAksi) {
                    hiddenAksi = document.createElement('input');
                    hiddenAksi.type = 'hidden';
                    hiddenAksi.name = 'aksi_submit';
                    hiddenAksi.setAttribute('data-generated', '1');
                    form.appendChild(hiddenAksi);
                }
                hiddenAksi.value = aksi;
            }
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.js-submit-capaian').forEach(function (btn) {
                btn.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
