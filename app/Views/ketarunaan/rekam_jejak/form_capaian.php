<?= $this->extend('layouts/ecc2_layout') ?>

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

<style>
    .ecc-rj-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-rj-card {
            padding: 16px;
        }
    }
    .ecc-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0284c7 0%, #1d4ed8 100%);
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-section-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #0f172a;
        padding-bottom: 8px;
        margin-bottom: 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    .ecc-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        background-color: #f8fafc;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="text-decoration-none text-secondary">Rekam Jejak Taruna</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= $isEdit ? 'Koreksi Capaian' : 'Tambah Capaian' ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1"><?= $isEdit ? 'Koreksi Capaian Taruna' : 'Tambah Capaian Taruna' ?></h4>
        <p class="text-secondary mb-0" style="font-size: 0.875rem;">Catat capaian kompetensi, organisasi, atau kegiatan pengembangan diri pada rekam jejak taruna.</p>
    </div>
    <div>
        <a href="<?= esc($backUrl) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-chevron-left"></i> Kembali ke Rekam Jejak
        </a>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
        <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (!empty($selectedTaruna)): ?>
    <?php
    $namaTarunaHeader = (string) ($selectedTaruna['nama_lengkap'] ?? '-');
    $inisialHeader    = strtoupper(substr(trim($namaTarunaHeader), 0, 1));
    ?>
    <div class="ecc-rj-card mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-4 d-flex align-items-center gap-3">
                <div class="ecc-avatar-lg"><?= esc($inisialHeader) ?></div>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc($namaTarunaHeader) ?></h5>
                    <div class="text-secondary" style="font-size: 0.8125rem;">Rekam Jejak Terintegrasi</div>
                </div>
            </div>
            <div class="col-6 col-md-2 border-start ps-md-3">
                <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
                <div class="fw-bold text-dark ecc-num"><?= esc((string) $selectedTaruna['nomor_taruna']) ?></div>
            </div>
            <div class="col-6 col-md-2 border-start ps-md-3">
                <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
                <div class="fw-bold text-dark"><?= esc((string) ($selectedTaruna['kode_prodi'] ?? '-')) ?></div>
            </div>
            <div class="col-6 col-md-2 border-start ps-md-3">
                <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
                <div class="fw-bold text-dark ecc-num"><?= esc((string) ($selectedTaruna['angkatan'] ?? '-')) ?></div>
            </div>
            <div class="col-6 col-md-2 border-start ps-md-3">
                <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
                <div><?= ketarunaan_status_badge((string) ($selectedTaruna['status_terkini'] ?? 'aktif')) ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>

<form method="post" action="<?= esc($formAction) ?>" enctype="multipart/form-data" class="ecc-rj-card">
    <?= csrf_field() ?>

    <!-- Bagian 1: Informasi Capaian -->
    <div class="ecc-section-title">Informasi Capaian</div>
    <div class="row g-3 mb-4">
        <?php if (!empty($selectedTaruna)): ?>
            <div class="col-12 col-md-6">
                <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Nomor Taruna</label>
                <input type="text" class="form-control ecc-control-sm bg-light ecc-num" value="<?= esc((string) $selectedTaruna['nomor_taruna']) ?>" readonly>
                <input type="hidden" name="nomor_taruna" value="<?= esc((string) $selectedTaruna['nomor_taruna']) ?>">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Nama Taruna</label>
                <input type="text" class="form-control ecc-control-sm bg-light" value="<?= esc((string) $selectedTaruna['nama_lengkap']) ?>" readonly>
            </div>
        <?php else: ?>
            <div class="col-12">
                <label for="nomor_taruna" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Pilih Taruna <span class="text-danger">*</span></label>
                <select id="nomor_taruna" name="nomor_taruna" class="form-select ecc-control-sm" required>
                    <option value="">-- Pilih Nomor Taruna & Nama --</option>
                    <?php foreach ($tarunaList as $tr): ?>
                        <option value="<?= esc($tr['nomor_taruna']) ?>" <?= ($nomorTarunaTerpilih === $tr['nomor_taruna']) ? 'selected' : '' ?>>
                            <?= esc($tr['nomor_taruna']) ?> - <?= esc($tr['nama_lengkap']) ?> (<?= esc($tr['kode_prodi'] ?? '-') ?> / <?= esc((string) ($tr['angkatan'] ?? '-')) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php
        $valKategori       = (string) old('kategori', $capaian['kategori'] ?? 'kompetensi_sertifikasi');
        $valNamaCapaian    = (string) old('nama_capaian', $capaian['nama_capaian'] ?? '');
        $valTglKegiatan    = (string) old('tanggal_kegiatan', $capaian['tanggal_kegiatan'] ?? '2026-10-20');
        $valTahunAkademik  = (string) old('tahun_akademik_id', (string) ($capaian['tahun_akademik_id'] ?? ''));
        $valSemester       = (int) old('semester', (string) ($capaian['semester'] ?? 5));
        $valPenyelenggara  = (string) old('penyelenggara', $capaian['penyelenggara'] ?? '');
        $valTingkat        = (string) old('tingkat_kegiatan', $capaian['tingkat_kegiatan'] ?? 'Nasional');
        $valUraian         = (string) old('uraian_capaian', $capaian['uraian_capaian'] ?? '');
        ?>

        <div class="col-12 col-md-4">
            <label for="kategori" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Kategori <span class="text-danger">*</span></label>
            <select id="kategori" name="kategori" class="form-select ecc-control-sm" required>
                <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                    <option value="<?= esc($kKode) ?>" <?= ($valKategori === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-4">
            <label for="nama_capaian" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Nama Capaian <span class="text-danger">*</span></label>
            <input type="text" id="nama_capaian" name="nama_capaian" value="<?= esc($valNamaCapaian) ?>" class="form-control ecc-control-sm" placeholder="Contoh: Sertifikasi Keselamatan Jalan" required>
        </div>

        <div class="col-12 col-md-4">
            <label for="tanggal_kegiatan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Tanggal Kegiatan <span class="text-danger">*</span></label>
            <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" value="<?= esc($valTglKegiatan) ?>" class="form-control ecc-control-sm ecc-num" required>
        </div>

        <div class="col-6 col-md-3">
            <label for="tahun_akademik_id" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Tahun Akademik</label>
            <select id="tahun_akademik_id" name="tahun_akademik_id" class="form-select ecc-control-sm">
                <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                    <option value="<?= (int) $ta['id'] ?>" <?= ($valTahunAkademik === (string) $ta['id'] || ($valTahunAkademik === '' && !empty($ta['is_aktif']))) ? 'selected' : '' ?>>
                        <?= esc($ta['kode_tahun']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label for="semester" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Semester</label>
            <select id="semester" name="semester" class="form-select ecc-control-sm ecc-num">
                <?php for ($s = 1; $s <= 8; $s++): ?>
                    <option value="<?= $s ?>" <?= ($valSemester === $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col-12 col-md-3">
            <label for="penyelenggara" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Penyelenggara <span class="text-danger">*</span></label>
            <input type="text" id="penyelenggara" name="penyelenggara" value="<?= esc($valPenyelenggara) ?>" class="form-control ecc-control-sm" placeholder="Nama instansi / penyelenggara" required>
        </div>

        <div class="col-12 col-md-3">
            <label for="tingkat_kegiatan" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Tingkat Kegiatan</label>
            <select id="tingkat_kegiatan" name="tingkat_kegiatan" class="form-select ecc-control-sm">
                <?php foreach (['Kampus / Internal', 'Daerah / Provinsi', 'Nasional', 'Internasional'] as $tk): ?>
                    <option value="<?= esc($tk) ?>" <?= ($valTingkat === $tk) ? 'selected' : '' ?>><?= esc($tk) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="uraian_capaian" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Uraian Capaian</label>
            <textarea id="uraian_capaian" name="uraian_capaian" rows="2" class="form-control rounded-3" style="font-size: 0.875rem;" placeholder="Jelaskan secara singkat kegiatan atau peran taruna..."><?= esc($valUraian) ?></textarea>
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
            <label for="hasil_capaian" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Hasil Capaian <span class="text-danger">*</span></label>
            <select id="hasil_capaian" name="hasil_capaian" class="form-select ecc-control-sm" required>
                <?php foreach (['Kompeten', 'Selesai', 'Peserta', 'Pengurus', 'Sekretaris', 'Ketua', 'Lulus'] as $hs): ?>
                    <option value="<?= esc($hs) ?>" <?= ($valHasil === $hs) ? 'selected' : '' ?>><?= esc($hs) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-6 col-md-3">
            <label for="nilai_angka" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Nilai Angka (Opsional)</label>
            <input type="text" id="nilai_angka" name="nilai_angka" value="<?= esc($valNilai) ?>" class="form-control ecc-control-sm ecc-num" placeholder="Misal: 92,5">
        </div>

        <div class="col-6 col-md-3">
            <label for="satuan_nilai" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Satuan (Opsional)</label>
            <input type="text" id="satuan_nilai" name="satuan_nilai" value="<?= esc($valSatuan) ?>" class="form-control ecc-control-sm" placeholder="Skor / Poin / Jam">
        </div>

        <div class="col-12 col-md-3">
            <label for="nomor_dokumen" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Nomor Dokumen</label>
            <input type="text" id="nomor_dokumen" name="nomor_dokumen" value="<?= esc($valNoDok) ?>" class="form-control ecc-control-sm ecc-num" placeholder="Contoh: SERT/026/2026">
        </div>

        <div class="col-12 col-md-6">
            <label for="unit_pemilik" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Unit Pemilik Data</label>
            <select id="unit_pemilik" name="unit_pemilik" class="form-select ecc-control-sm">
                <?php foreach (['Ketarunaan', 'Pusbangkar', 'Program Studi'] as $up): ?>
                    <option value="<?= esc($up) ?>" <?= ($valUnit === $up) ? 'selected' : '' ?>><?= esc($up) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 col-md-6">
            <label for="sumber_dokumen" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.8125rem;">Sumber Data</label>
            <select id="sumber_dokumen" name="sumber_dokumen" class="form-select ecc-control-sm">
                <?php foreach (['Sertifikat', 'Surat Tugas', 'Surat Keputusan (SK)', 'Piagam'] as $sd): ?>
                    <option value="<?= esc($sd) ?>" <?= ($valSumber === $sd) ? 'selected' : '' ?>><?= esc($sd) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Bagian 3: Bukti Pendukung -->
    <div class="ecc-section-title">Bukti Pendukung</div>
    <div class="ecc-upload-box mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-8">
                <label for="bukti_berkas" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.875rem;">Unggah Berkas Bukti (PDF, JPG, PNG maks. 5 MB)</label>
                <input type="file" id="bukti_berkas" name="bukti_berkas" accept=".pdf,.jpg,.jpeg,.png" class="form-control ecc-control-sm">
                <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                    Sertifikat, surat tugas, atau surat keputusan resmi kegiatan.
                </div>
            </div>
            <?php if (!empty($capaian['bukti_berkas'])): ?>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-2 bg-white d-flex align-items-center justify-content-between">
                        <div class="text-truncate pe-2" style="font-size: 0.8125rem;">
                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                            <span class="fw-semibold text-dark"><?= esc((string) $capaian['bukti_berkas']) ?></span>
                        </div>
                        <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . (int) $capaian['id'] . '?inline=1') ?>" target="_blank" class="btn btn-light border btn-sm py-1 px-2" style="font-size: 0.75rem;">
                            Lihat
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pt-3 border-top">
        <div class="text-secondary" style="font-size: 0.8125rem;">
            <i class="bi bi-info-circle text-primary me-1"></i>
            Perubahan data akan tercatat dalam riwayat dan memerlukan validasi sesuai ketentuan.
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= esc($backUrl) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold">Batal</a>
            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                Simpan Draf
            </button>
            <button type="submit" name="aksi_submit" value="diajukan" class="btn btn-primary ecc-control-sm px-3 fw-semibold">
                Ajukan Validasi
            </button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

