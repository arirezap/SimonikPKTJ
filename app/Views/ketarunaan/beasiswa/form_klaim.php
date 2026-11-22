<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Formulir Klaim Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/vendor/select2/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$jenis          = (string) ($jenis ?? 'prestasi');
$existing       = $existing ?? null;
$refs           = $refs ?? [];
$selectedNim    = (string) ($selectedNim ?? '');
$prestasiTaruna = $prestasiTaruna ?? [];
$isSelfTaruna   = !empty($isSelfTaruna);

$jenisMeta      = ketarunaan_jenis_beasiswa_catalog()[$jenis] ?? ['label' => 'Beasiswa'];
$matchingProgs  = array_values(array_filter(
    $refs['programs'] ?? [],
    static fn ($p) => ($p['jenis_beasiswa'] ?? '') === $jenis
));
$selProgId      = (string) old('program_beasiswa_id', (string) ($existing['program_beasiswa_id'] ?? ($matchingProgs[0]['id'] ?? '')));
$selSumber      = $isSelfTaruna ? 'taruna' : (string) old('sumber_klaim', (string) ($existing['sumber_klaim'] ?? 'pic'));
$editId         = $existing ? (int) $existing['id'] : 0;

$existingSertRaw = trim((string) ($existing['sertifikat_berkas'] ?? ''));
$existingDokRaw  = trim((string) ($existing['dokumentasi_berkas'] ?? ''));
$existingDomRaw  = trim((string) ($existing['dokumen_domisili'] ?? ''));
$existingPendRaw = trim((string) ($existing['dokumen_pendukung_3tp'] ?? ''));
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="<?= base_url('ketarunaan/beasiswa/klaim') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Klaim
        </a>
        <?php if ($editId === 0): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach (['prestasi' => 'Prestasi', 'daerah_3tp' => 'Daerah 3TP', 'tidak_mampu' => 'Tidak Mampu'] as $jk => $jl): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . $jk) ?>"
                       class="btn btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center <?= $jenis === $jk ? 'btn-primary' : 'btn-light border' ?>">
                        <?= esc($jl) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="ecc-bea-card bea-form-scope">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <?= ketarunaan_jenis_beasiswa_badge($jenis, true) ?>
            <?php if ($editId > 0): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Mode Perubahan Pengajuan</span>
            <?php endif; ?>
        </div>
        <h2 class="h5 fw-bold text-dark mb-1">Formulir Pengajuan <?= esc($jenisMeta['label']) ?></h2>
        <p class="ecc-text-muted-aa mb-4 small">
            Lengkapi identitas pengajuan dan tempel tautan Google Drive berkas bukti (diawali <code>http://</code> atau <code>https://</code>, pastikan akses lihat aktif).
        </p>

        <form id="formKlaimBeasiswa" method="post" action="<?= base_url('ketarunaan/beasiswa/klaim/simpan/' . esc($jenis)) ?>" data-lock-submit="true">
            <?= csrf_field() ?>
            <input type="hidden" name="edit_id" value="<?= $editId ?>">

            <!-- Bagian 1: Identitas Program, Taruna & Sumber Klaim -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-5">
                    <label for="program_beasiswa_id" class="form-label fw-semibold">Program Beasiswa <span class="text-danger">*</span></label>
                    <select id="program_beasiswa_id" name="program_beasiswa_id" class="form-select ecc-control-sm" required>
                        <?php foreach ($matchingProgs as $mp): ?>
                            <option value="<?= (int) $mp['id'] ?>" <?= (string) $mp['id'] === $selProgId ? 'selected' : '' ?>>
                                <?= esc($mp['nama_program']) ?> (<?= esc($mp['pemberi']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="nomor_taruna" class="form-label fw-semibold">Taruna Pemohon <span class="text-danger">*</span></label>
                    <?php if ($isSelfTaruna || $editId > 0): ?>
                        <?php
                        $namaTerpilih = $selectedNim;
                        foreach (($refs['taruna'] ?? []) as $tr) {
                            if (strcasecmp((string) $tr['nomor_taruna'], $selectedNim) === 0) {
                                $namaTerpilih = $tr['nama_lengkap'] . ' (' . $tr['nomor_taruna'] . ' - ' . ($tr['kode_prodi'] ?? '-') . ')';
                                break;
                            }
                        }
                        ?>
                        <input type="hidden" name="nomor_taruna" id="nomor_taruna" value="<?= esc($selectedNim) ?>">
                        <input type="text" id="nomor_taruna_display" class="form-control ecc-control-sm bg-light" value="<?= esc($namaTerpilih) ?>" readonly>
                    <?php else: ?>
                        <select id="nomor_taruna" name="nomor_taruna" class="form-select ecc-control-sm js-select2-taruna" required>
                            <option value="">-- Pilih Taruna --</option>
                            <?php foreach (($refs['taruna'] ?? []) as $tr): ?>
                                <option value="<?= esc((string) $tr['nomor_taruna']) ?>"
                                        <?= old('nomor_taruna', $selectedNim) === (string) $tr['nomor_taruna'] ? 'selected' : '' ?>>
                                    <?= esc($tr['nomor_taruna'] . ' - ' . $tr['nama_lengkap'] . ' (' . ($tr['kode_prodi'] ?? '-') . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-3">
                    <label for="sumber_klaim" class="form-label fw-semibold">Sumber Klaim</label>
                    <?php if ($isSelfTaruna): ?>
                        <input type="hidden" name="sumber_klaim" value="taruna">
                        <input type="text" id="sumber_klaim" class="form-control ecc-control-sm bg-light" value="Mandiri Taruna" readonly>
                    <?php else: ?>
                        <select id="sumber_klaim" name="sumber_klaim" class="form-select ecc-control-sm">
                            <option value="pic" <?= $selSumber === 'pic' ? 'selected' : '' ?>>Dicatat Petugas / PIC</option>
                            <option value="taruna" <?= $selSumber === 'taruna' ? 'selected' : '' ?>>Klaim Mandiri Taruna</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bagian 2 Dinamis Berdasarkan Jenis Klaim -->
            <?php if ($jenis === 'prestasi'): ?>
                <div class="p-3 rounded-4 border mb-4" style="background-color: #fffbeb;">
                    <label for="prestasi_id" class="form-label fw-bold text-dark mb-1">Tautkan Prestasi Tervalidasi (Opsional)</label>
                    <div class="ecc-text-muted-aa mb-2 small">
                        Jika taruna sudah memiliki prestasi tervalidasi di menu Prestasi, pilih dari daftar di bawah agar data dan tautan bukti terisi otomatis.
                    </div>
                    <select id="prestasi_id" name="prestasi_id" class="form-select ecc-control-sm">
                        <option value="">-- Tidak menautkan / Isi rincian prestasi di bawah --</option>
                        <?php foreach ($prestasiTaruna as $pt): ?>
                            <?php
                            $ptDokFirst = '';
                            if (!empty($pt['dokumentasi_json'])) {
                                $ptDokArr = json_decode((string) $pt['dokumentasi_json'], true);
                                if (is_array($ptDokArr) && !empty($ptDokArr[0])) {
                                    $ptDokFirst = is_array($ptDokArr[0]) ? (string) ($ptDokArr[0]['url'] ?? $ptDokArr[0]['nama'] ?? '') : (string) $ptDokArr[0];
                                }
                            }
                            ?>
                            <option value="<?= (int) $pt['id'] ?>"
                                    data-nama="<?= esc((string) $pt['nama_prestasi']) ?>"
                                    data-kategori="<?= esc((string) ($pt['jenis_prestasi'] ?: 'Akademik')) ?>"
                                    data-tingkat="<?= esc((string) ($pt['tingkat_prestasi'] ?: 'Nasional')) ?>"
                                    data-peringkat="<?= esc((string) ($pt['peringkat'] ?: '')) ?>"
                                    data-tanggal="<?= esc((string) ($pt['tanggal_mulai'] ?: '')) ?>"
                                    data-penyelenggara="<?= esc((string) ($pt['penyelenggara'] ?: '')) ?>"
                                    data-bukti="<?= esc((string) ($pt['bukti_berkas'] ?: '')) ?>"
                                    data-dok="<?= esc($ptDokFirst) ?>"
                                    <?= (string) old('prestasi_id', (string) ($existing['prestasi_id'] ?? '')) === (string) $pt['id'] ? 'selected' : '' ?>>
                                <?= esc($pt['nama_prestasi'] . ' (' . $pt['peringkat'] . ' - ' . ucfirst((string) $pt['tingkat_prestasi']) . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-8">
                        <label for="nama_prestasi" class="form-label fw-semibold">Nama Kejuaraan / Prestasi <span class="text-danger">*</span></label>
                        <input type="text" id="nama_prestasi" name="nama_prestasi" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('nama_prestasi', $existing['nama_prestasi'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="kategori_prestasi" class="form-label fw-semibold">Kategori Prestasi</label>
                        <?php $katPr = (string) old('kategori_prestasi', $existing['kategori_prestasi'] ?? 'Akademik'); ?>
                        <select id="kategori_prestasi" name="kategori_prestasi" class="form-select ecc-control-sm">
                            <?php foreach (['Akademik', 'Nonakademik', 'Inovasi', 'Olahraga & Seni'] as $kp): ?>
                                <option value="<?= esc($kp) ?>" <?= strtolower($katPr) === strtolower($kp) ? 'selected' : '' ?>><?= esc($kp) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="tingkat_prestasi" class="form-label fw-semibold">Tingkat</label>
                        <?php $tktPr = (string) old('tingkat_prestasi', $existing['tingkat_prestasi'] ?? 'Nasional'); ?>
                        <select id="tingkat_prestasi" name="tingkat_prestasi" class="form-select ecc-control-sm">
                            <?php foreach (['Kampus', 'Regional', 'Provinsi', 'Nasional', 'Internasional'] as $tp): ?>
                                <option value="<?= esc($tp) ?>" <?= strtolower($tktPr) === strtolower($tp) ? 'selected' : '' ?>><?= esc($tp) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="peringkat_prestasi" class="form-label fw-semibold">Peringkat / Capaian <span class="text-danger">*</span></label>
                        <input type="text" id="peringkat_prestasi" name="peringkat_prestasi" class="form-control ecc-control-sm" placeholder="Contoh: Juara I"
                               value="<?= esc((string) old('peringkat_prestasi', $existing['peringkat_prestasi'] ?? '')) ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="tanggal_prestasi" class="form-label fw-semibold">Tanggal Kegiatan</label>
                        <input type="text" id="tanggal_prestasi" name="tanggal_prestasi" class="form-control ecc-control-sm ecc-num bg-white js-bea-date"
                               placeholder="Pilih tanggal..."
                               value="<?= esc((string) old('tanggal_prestasi', $existing['tanggal_prestasi'] ?? '')) ?>">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="penyelenggara_prestasi" class="form-label fw-semibold">Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" id="penyelenggara_prestasi" name="penyelenggara_prestasi" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('penyelenggara_prestasi', $existing['penyelenggara_prestasi'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="sertifikat_link" class="form-label fw-semibold">Tautan Sertifikat / Piagam (Google Drive) <span class="text-danger">*</span></label>
                        <input type="url" id="sertifikat_link" name="sertifikat_link" class="form-control ecc-control-sm" maxlength="255"
                               placeholder="https://drive.google.com/file/d/.../view"
                               value="<?= esc((string) old('sertifikat_link', ketarunaan_is_http_url($existingSertRaw, 2000) ? $existingSertRaw : '')) ?>">
                        <?php if ($existingSertRaw !== ''): ?>
                            <div class="form-text text-success">Saat ini: <?= esc(ketarunaan_format_bukti_label($existingSertRaw)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="dokumentasi_link" class="form-label fw-semibold">Tautan Dokumentasi Kegiatan (Google Drive) <span class="text-danger">*</span></label>
                        <input type="url" id="dokumentasi_link" name="dokumentasi_link" class="form-control ecc-control-sm" maxlength="255"
                               placeholder="https://drive.google.com/file/d/.../view"
                               value="<?= esc((string) old('dokumentasi_link', ketarunaan_is_http_url($existingDokRaw, 2000) ? $existingDokRaw : '')) ?>">
                        <?php if ($existingDokRaw !== ''): ?>
                            <div class="form-text text-success">Saat ini: <?= esc(ketarunaan_format_bukti_label($existingDokRaw)) ?></div>
                        <?php else: ?>
                            <div class="form-text">Tautan sertifikat dan dokumentasi kegiatan wajib diisi saat mengirim pengajuan.</div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($jenis === 'daerah_3tp'): ?>
                <div class="p-3 rounded-4 border mb-4" style="background-color: #ecfdf5;">
                    <label for="pilihWilayah3tp" class="form-label fw-bold text-dark mb-1">Pilih Cepat dari Daftar Wilayah 3TP Acuan Resmi</label>
                    <select id="pilihWilayah3tp" class="form-select ecc-control-sm js-select2-wilayah">
                        <option value="">-- Cari atau pilih Kabupaten/Kota 3TP --</option>
                        <?php foreach (($refs['wilayah_3tp'] ?? []) as $w3): ?>
                            <option value="<?= esc((string) $w3['kode_wilayah']) ?>"
                                    data-kode="<?= esc((string) $w3['kode_wilayah']) ?>"
                                    data-provinsi="<?= esc((string) $w3['provinsi']) ?>"
                                    data-kabupaten="<?= esc((string) $w3['kabupaten_kota']) ?>">
                                <?= esc($w3['kabupaten_kota'] . ' - ' . $w3['provinsi'] . ' (' . $w3['kode_wilayah'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">
                        Wilayah di luar acuan resmi atau yang belum memiliki dasar wilayah aktif tidak dapat disetujui otomatis oleh sistem.
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label for="alamat_domisili" class="form-label fw-semibold">Alamat Lengkap Domisili <span class="text-danger">*</span></label>
                        <textarea id="alamat_domisili" name="alamat_domisili" rows="2" class="form-control rounded-3"><?= esc((string) old('alamat_domisili', $existing['alamat_domisili'] ?? '')) ?></textarea>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="provinsi" class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
                        <input type="text" id="provinsi" name="provinsi" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('provinsi', $existing['provinsi'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="kabupaten_kota" class="form-label fw-semibold">Kabupaten / Kota <span class="text-danger">*</span></label>
                        <input type="text" id="kabupaten_kota" name="kabupaten_kota" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('kabupaten_kota', $existing['kabupaten_kota'] ?? '')) ?>">
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="kode_wilayah" class="form-label fw-semibold">Kode Wilayah</label>
                        <input type="text" id="kode_wilayah" name="kode_wilayah" class="form-control ecc-control-sm ecc-num" placeholder="53.17"
                               value="<?= esc((string) old('kode_wilayah', $existing['kode_wilayah'] ?? '')) ?>">
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="tanggal_acuan_domisili" class="form-label fw-semibold">Tgl Acuan <span class="text-danger">*</span></label>
                        <input type="text" id="tanggal_acuan_domisili" name="tanggal_acuan_domisili" class="form-control ecc-control-sm ecc-num bg-white js-bea-date"
                               value="<?= esc((string) old('tanggal_acuan_domisili', $existing['tanggal_acuan_domisili'] ?? date('Y-m-d'))) ?>">
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="kecamatan" class="form-label fw-semibold">Kecamatan</label>
                        <input type="text" id="kecamatan" name="kecamatan" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('kecamatan', $existing['kecamatan'] ?? '')) ?>">
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="desa_kelurahan" class="form-label fw-semibold">Desa / Kelurahan</label>
                        <input type="text" id="desa_kelurahan" name="desa_kelurahan" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('desa_kelurahan', $existing['desa_kelurahan'] ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="jenis_dokumen_domisili" class="form-label fw-semibold">Jenis Dokumen Domisili</label>
                        <?php $jDokDom = (string) old('jenis_dokumen_domisili', $existing['jenis_dokumen_domisili'] ?? 'Kartu Keluarga'); ?>
                        <select id="jenis_dokumen_domisili" name="jenis_dokumen_domisili" class="form-select ecc-control-sm">
                            <?php foreach (($refs['dokumen_3tp_options'] ?? []) as $opt): ?>
                                <option value="<?= esc($opt) ?>" <?= $jDokDom === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="dokumen_domisili_link" class="form-label fw-semibold">Tautan Dokumen Domisili (Google Drive) <span class="text-danger">*</span></label>
                        <input type="url" id="dokumen_domisili_link" name="dokumen_domisili_link" class="form-control ecc-control-sm" maxlength="255"
                               placeholder="https://drive.google.com/file/d/.../view"
                               value="<?= esc((string) old('dokumen_domisili_link', ketarunaan_is_http_url($existingDomRaw, 2000) ? $existingDomRaw : '')) ?>">
                        <?php if ($existingDomRaw !== ''): ?>
                            <div class="form-text text-success">Saat ini: <?= esc(ketarunaan_format_bukti_label($existingDomRaw)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="dokumen_pendukung_3tp_link" class="form-label fw-semibold">Tautan Pendukung Daerah 3TP (Opsional)</label>
                        <input type="url" id="dokumen_pendukung_3tp_link" name="dokumen_pendukung_3tp_link" class="form-control ecc-control-sm" maxlength="255"
                               placeholder="https://drive.google.com/file/d/.../view"
                               value="<?= esc((string) old('dokumen_pendukung_3tp_link', ketarunaan_is_http_url($existingPendRaw, 2000) ? $existingPendRaw : '')) ?>">
                        <?php if ($existingPendRaw !== ''): ?>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span class="form-text text-success mb-0">Saat ini: <?= esc(ketarunaan_format_bukti_label($existingPendRaw)) ?></span>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="hapusPendukung3tp" name="hapus_dokumen_pendukung_3tp" value="1">
                                    <label class="form-check-label small text-danger" for="hapusPendukung3tp">Hapus tautan ini</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 small" role="note">
                    <strong>Akses Berkas Terbatas:</strong> Rincian penghasilan keluarga dan tautan bukti ekonomi hanya dapat dilihat oleh pengelola berwenang serta taruna pemilik pengajuan.
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-5">
                        <label for="pekerjaan_orang_tua" class="form-label fw-semibold">Pekerjaan Orang Tua / Wali <span class="text-danger">*</span></label>
                        <input type="text" id="pekerjaan_orang_tua" name="pekerjaan_orang_tua" class="form-control ecc-control-sm"
                               value="<?= esc((string) old('pekerjaan_orang_tua', $existing['pekerjaan_orang_tua'] ?? '')) ?>">
                    </div>
                    <div class="col-8 col-md-4">
                        <label for="penghasilan_bulanan" class="form-label fw-semibold">Perkiraan Penghasilan / Bulan (Rp)</label>
                        <input type="number" id="penghasilan_bulanan" name="penghasilan_bulanan" class="form-control ecc-control-sm ecc-num" min="0"
                               value="<?= esc((string) old('penghasilan_bulanan', $existing['penghasilan_bulanan'] ?? '')) ?>">
                    </div>
                    <div class="col-4 col-md-3">
                        <label for="jumlah_tanggungan" class="form-label fw-semibold">Jumlah Tanggungan <span class="text-danger">*</span></label>
                        <input type="number" id="jumlah_tanggungan" name="jumlah_tanggungan" class="form-control ecc-control-sm ecc-num" min="1"
                               value="<?= esc((string) old('jumlah_tanggungan', $existing['jumlah_tanggungan'] ?? 1)) ?>">
                    </div>
                </div>

                <div class="p-3 rounded-4 border mb-4" style="background-color: #f8fafc;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h3 class="h6 fw-bold text-dark mb-0">Lampiran Bukti Ekonomi (Tautan Google Drive)</h3>
                        <button type="button" id="btnTambahDokEkonomi" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl">
                            + Tambah Baris Dokumen
                        </button>
                    </div>
                    <?php if (!empty($existing['dokumen_ekonomi_json'])): ?>
                        <?php $existingEkoDocs = json_decode((string) $existing['dokumen_ekonomi_json'], true); ?>
                        <?php if (is_array($existingEkoDocs) && !empty($existingEkoDocs)): ?>
                            <div class="mb-3">
                                <div class="small fw-semibold text-dark mb-2">Dokumen tersimpan saat ini (centang untuk menghapus saat perbaikan):</div>
                                <div class="d-flex flex-column gap-2">
                                    <?php foreach ($existingEkoDocs as $idxEko => $ed): ?>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 bg-white border rounded-3 px-3 py-2 small">
                                            <div>
                                                <span class="fw-semibold text-dark"><?= esc((string) ($ed['jenis_dokumen'] ?? 'Dokumen')) ?></span>
                                                <span class="ecc-text-muted-aa ms-1">&bull; <?= esc(ketarunaan_format_bukti_label((string) ($ed['nama_berkas'] ?? ''))) ?></span>
                                            </div>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="remove_ekonomi_indices[]" value="<?= (int) $idxEko ?>" id="rmEko_<?= (int) $idxEko ?>">
                                                <label class="form-check-label text-danger" for="rmEko_<?= (int) $idxEko ?>">Hapus</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div id="containerDokEkonomi" class="d-flex flex-column gap-2">
                        <div class="row g-2 align-items-end doc-eko-row bg-white p-2 rounded-3 border">
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1" style="font-size: 0.75rem;">Jenis Dokumen</label>
                                <select name="dokumen_ekonomi_jenis[]" class="form-select ecc-control-sm" aria-label="Jenis dokumen ekonomi">
                                    <?php foreach (($refs['dokumen_ekonomi_options'] ?? []) as $opt): ?>
                                        <option value="<?= esc($opt) ?>"><?= esc($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1" style="font-size: 0.75rem;">Nomor Dokumen</label>
                                <input type="text" name="dokumen_ekonomi_nomor[]" class="form-control ecc-control-sm" aria-label="Nomor dokumen ekonomi">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1" style="font-size: 0.75rem;">Instansi Penerbit</label>
                                <input type="text" name="dokumen_ekonomi_penerbit[]" class="form-control ecc-control-sm" aria-label="Instansi penerbit">
                            </div>
                            <div class="col-10 col-md-4">
                                <label class="form-label mb-1" style="font-size: 0.75rem;">Tautan Google Drive / Cloud</label>
                                <input type="url" name="dokumen_ekonomi_links[]" class="form-control ecc-control-sm js-eko-link" maxlength="255"
                                       placeholder="https://drive.google.com/file/d/.../view" aria-label="Tautan Google Drive dokumen ekonomi">
                            </div>
                            <div class="col-2 col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger ecc-control-sm px-2 btn-remove-eko-row d-none" aria-label="Hapus baris dokumen">
                                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="uraian_pengajuan" class="form-label fw-semibold">Keterangan Tambahan Pengajuan <?= $jenis === 'tidak_mampu' ? '<span class="text-danger">*</span>' : '' ?></label>
                <textarea id="uraian_pengajuan" name="uraian_pengajuan" rows="3" class="form-control rounded-3"><?= esc((string) old('uraian_pengajuan', $existing['uraian_pengajuan'] ?? '')) ?></textarea>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                <a href="<?= base_url('ketarunaan/beasiswa/klaim') ?>" class="btn btn-light border rounded-pill px-4 bea-touch-ctrl d-inline-flex align-items-center">Batal</a>
                <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary rounded-pill px-4 btn-tactile bea-touch-ctrl">
                    Simpan Draf
                </button>
                <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-pill px-4 btn-tactile bea-touch-ctrl shadow-sm">
                    <i class="bi bi-send me-1" aria-hidden="true"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
        var tarunaSel = window.jQuery('.js-select2-taruna');
        if (tarunaSel.length) {
            tarunaSel.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Pilih Taruna --',
                allowClear: true
            });
        }
        var wilSel = window.jQuery('.js-select2-wilayah');
        if (wilSel.length) {
            wilSel.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: '-- Cari atau pilih Kabupaten/Kota 3TP --',
                allowClear: true
            }).on('change', function() {
                var opt = this.options[this.selectedIndex];
                if (!opt || !opt.value) return;
                var kodeEl = document.getElementById('kode_wilayah');
                var provEl = document.getElementById('provinsi');
                var kabEl  = document.getElementById('kabupaten_kota');
                if (kodeEl) kodeEl.value = opt.dataset.kode || '';
                if (provEl) provEl.value = opt.dataset.provinsi || '';
                if (kabEl)  kabEl.value  = opt.dataset.kabupaten || '';
            });
        }
    }

    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        document.querySelectorAll('.js-bea-date').forEach(function(el) {
            flatpickr(el, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                allowInput: true
            });
        });
    }

    // Auto-isi data Prestasi saat memilih dari daftar prestasi tervalidasi
    var nimEl      = document.getElementById('nomor_taruna');
    var prestasiEl = document.getElementById('prestasi_id');

    function setSelectByTextOrValue(sel, targetVal) {
        if (!sel || !targetVal) return;
        var low = targetVal.toLowerCase();
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value.toLowerCase() === low || sel.options[i].text.toLowerCase() === low) {
                sel.selectedIndex = i;
                break;
            }
        }
    }

    function applyPrestasiOption(opt) {
        if (!opt || !opt.value) return;
        var fields = {
            nama_prestasi: opt.dataset.nama || '',
            peringkat_prestasi: opt.dataset.peringkat || '',
            tanggal_prestasi: opt.dataset.tanggal || '',
            penyelenggara_prestasi: opt.dataset.penyelenggara || ''
        };
        Object.keys(fields).forEach(function(id) {
            var input = document.getElementById(id);
            if (input && fields[id]) {
                if (input._flatpickr) {
                    input._flatpickr.setDate(fields[id], true);
                } else {
                    input.value = fields[id];
                }
            }
        });
        setSelectByTextOrValue(document.getElementById('kategori_prestasi'), opt.dataset.kategori || '');
        setSelectByTextOrValue(document.getElementById('tingkat_prestasi'), opt.dataset.tingkat || '');

        var sertInp = document.getElementById('sertifikat_link');
        var dokInp  = document.getElementById('dokumentasi_link');
        if (sertInp && opt.dataset.bukti && /^https?:\/\//i.test(opt.dataset.bukti)) {
            sertInp.value = opt.dataset.bukti;
        }
        if (dokInp && opt.dataset.dok && /^https?:\/\//i.test(opt.dataset.dok)) {
            dokInp.value = opt.dataset.dok;
        }
    }

    if (prestasiEl) {
        prestasiEl.addEventListener('change', function() {
            applyPrestasiOption(this.options[this.selectedIndex]);
        });
    }

    function refreshPrestasiList(nim) {
        if (!prestasiEl) return;
        prestasiEl.innerHTML = '<option value="">-- Tidak menautkan / Isi rincian prestasi di bawah --</option>';
        if (!nim) return;
        fetch('<?= base_url('ketarunaan/beasiswa/api/prestasi-taruna/') ?>' + encodeURIComponent(nim))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                (data.items || []).forEach(function(it) {
                    var opt = document.createElement('option');
                    opt.value = it.id;
                    opt.textContent = it.nama_prestasi + ' (' + (it.peringkat || '-') + ' - ' + (it.tingkat_prestasi || '') + ')';
                    opt.dataset.nama = it.nama_prestasi || '';
                    opt.dataset.kategori = it.jenis_prestasi || '';
                    opt.dataset.tingkat = it.tingkat_prestasi || '';
                    opt.dataset.peringkat = it.peringkat || '';
                    opt.dataset.tanggal = it.tanggal_mulai || '';
                    opt.dataset.penyelenggara = it.penyelenggara || '';
                    opt.dataset.bukti = it.bukti_berkas || '';
                    var dokFirst = '';
                    if (it.dokumentasi_json) {
                        try {
                            var parsed = JSON.parse(it.dokumentasi_json);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                dokFirst = typeof parsed[0] === 'string' ? parsed[0] : (parsed[0].url || parsed[0].nama || '');
                            }
                        } catch (err) {}
                    }
                    opt.dataset.dok = dokFirst;
                    prestasiEl.appendChild(opt);
                });
            })
            .catch(function() {});
    }

    if (nimEl && nimEl.tagName === 'SELECT' && prestasiEl) {
        if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery(nimEl).on('change', function() { refreshPrestasiList(this.value); });
        } else {
            nimEl.addEventListener('change', function() { refreshPrestasiList(this.value); });
        }
    }

    // Tambah / hapus baris dokumen ekonomi Tidak Mampu
    var btnAddEko = document.getElementById('btnTambahDokEkonomi');
    var boxEko    = document.getElementById('containerDokEkonomi');
    if (btnAddEko && boxEko) {
        btnAddEko.addEventListener('click', function() {
            var first = boxEko.querySelector('.doc-eko-row');
            if (!first) return;
            var clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(function(inp) { inp.value = ''; });
            var rmBtn = clone.querySelector('.btn-remove-eko-row');
            if (rmBtn) {
                rmBtn.classList.remove('d-none');
                rmBtn.addEventListener('click', function() {
                    clone.remove();
                });
            }
            boxEko.appendChild(clone);
        });
    }

    // Validasi tautan URL (Opsi A) & whitespace sebelum submit
    var formKlaim = document.getElementById('formKlaimBeasiswa');
    if (formKlaim) {
        formKlaim.addEventListener('submit', function(e) {
            var clicked = document.activeElement;
            var isDraft = clicked && clicked.value === 'draf';
            var nimVal  = (document.getElementById('nomor_taruna')?.value || '').trim();
            if (nimVal === '') {
                e.preventDefault();
                window.eccBeaShowMessage('Taruna Wajib Dipilih', 'Pilih taruna pemohon terlebih dahulu.');
                return;
            }

            var urlInputs = formKlaim.querySelectorAll('input[type="url"]');
            for (var i = 0; i < urlInputs.length; i++) {
                var val = (urlInputs[i].value || '').trim();
                if (val !== '' && (!/^https?:\/\/\S+/i.test(val) || val.length > 255)) {
                    e.preventDefault();
                    window.eccBeaShowMessage('Tautan Tidak Valid', 'Pastikan setiap tautan Google Drive diawali http:// atau https:// (maksimal 255 karakter).');
                    return;
                }
            }

            if (!isDraft) {
                var jenis = '<?= esc($jenis) ?>';
                if (jenis === 'prestasi') {
                    var np = (document.getElementById('nama_prestasi')?.value || '').trim();
                    var pr = (document.getElementById('peringkat_prestasi')?.value || '').trim();
                    var py = (document.getElementById('penyelenggara_prestasi')?.value || '').trim();
                    if (np === '' || pr === '' || py === '') {
                        e.preventDefault();
                        window.eccBeaShowMessage('Data Belum Lengkap', 'Nama prestasi, peringkat, dan penyelenggara wajib diisi.');
                        return;
                    }
                } else if (jenis === 'daerah_3tp') {
                    var al = (document.getElementById('alamat_domisili')?.value || '').trim();
                    var pv = (document.getElementById('provinsi')?.value || '').trim();
                    var kb = (document.getElementById('kabupaten_kota')?.value || '').trim();
                    if (al === '' || pv === '' || kb === '') {
                        e.preventDefault();
                        window.eccBeaShowMessage('Data Belum Lengkap', 'Alamat domisili, provinsi, dan kabupaten/kota wajib diisi.');
                        return;
                    }
                } else if (jenis === 'tidak_mampu') {
                    var pk = (document.getElementById('pekerjaan_orang_tua')?.value || '').trim();
                    var ur = (document.getElementById('uraian_pengajuan')?.value || '').trim();
                    if (pk === '' || ur === '') {
                        e.preventDefault();
                        window.eccBeaShowMessage('Data Belum Lengkap', 'Pekerjaan orang tua dan keterangan kondisi ekonomi wajib diisi.');
                        return;
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
