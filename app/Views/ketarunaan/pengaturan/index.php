<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pengaturan Ketarunaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/_governance_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$activeTab   = $active_tab ?? 'referensi';
$activeRef   = $active_ref ?? 'prodi';
$referensi   = $referensi ?? ['prodi' => [], 'angkatan' => [], 'tahun_akademik' => [], 'kelas' => [], 'wilayah_3tp' => []];
$roleMatrix  = $role_matrix ?? [];
$userDir     = $user_directory ?? [];
$operasional = $operasional ?? [];
$perms       = $perms ?? ['can_manage_referensi' => false, 'can_manage_operasional' => false, 'can_manage_users' => false];

$refTabs = [
    'prodi'          => 'Program Studi',
    'angkatan'       => 'Angkatan',
    'tahun_akademik' => 'Tahun Akademik',
    'kelas'          => 'Kelas',
    'wilayah_3tp'    => 'Wilayah 3TP',
];

$isEditingRef = trim((string) old('ref_id', '')) !== '';
$refLabelNow  = $refTabs[$activeRef] ?? 'Data Rujukan';
$colCountBase = match ($activeRef) {
    'prodi', 'kelas' => 5,
    default          => 4,
};
$colCount = $colCountBase + (!empty($perms['can_manage_referensi']) ? 1 : 0);
?>
<div class="container-fluid px-0">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none ecc-text-muted-aa">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Pengaturan</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Pengaturan Ketarunaan</h1>
            <p class="ecc-text-muted-aa mb-0 small">Kelola data rujukan tanpa menghapus riwayat lama, matriks kewenangan peran, dan penanda data.</p>
        </div>
        <a href="<?= site_url('ketarunaan/dashboard') ?>" class="btn btn-light border ecc-control-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center justify-content-center text-nowrap btn-tactile gov-touch-ctrl">
            Kembali ke Ringkasan
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between gap-2" role="alert">
            <span class="small fw-medium"><?= esc((string) session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between gap-2" role="alert">
            <span class="small fw-medium"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Tab Utama Pengaturan -->
    <nav class="ecc-gov-tabbar mb-4" aria-label="Navigasi pengaturan">
        <a href="<?= site_url('ketarunaan/pengaturan?tab=referensi&ref=' . urlencode($activeRef)) ?>" class="ecc-gov-tab gov-touch-ctrl <?= $activeTab === 'referensi' ? 'active' : '' ?>" <?= $activeTab === 'referensi' ? 'aria-current="page"' : '' ?>>
            Data Rujukan
        </a>
        <a href="<?= site_url('ketarunaan/pengaturan?tab=peran') ?>" class="ecc-gov-tab gov-touch-ctrl <?= $activeTab === 'peran' ? 'active' : '' ?>" <?= $activeTab === 'peran' ? 'aria-current="page"' : '' ?>>
            Kewenangan Peran
        </a>
        <a href="<?= site_url('ketarunaan/pengaturan?tab=operasional') ?>" class="ecc-gov-tab gov-touch-ctrl <?= $activeTab === 'operasional' ? 'active' : '' ?>" <?= $activeTab === 'operasional' ? 'aria-current="page"' : '' ?>>
            Parameter Operasional
        </a>
    </nav>

    <?php if ($activeTab === 'referensi'): ?>
        <!-- Pil Jenis Data Rujukan -->
        <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Kategori data rujukan">
            <?php foreach ($refTabs as $kode => $label): ?>
                <?php $isActiveRef = ($activeRef === $kode); ?>
                <a href="<?= site_url('ketarunaan/pengaturan?tab=referensi&ref=' . urlencode($kode)) ?>" class="btn btn-sm rounded-pill <?= $isActiveRef ? 'btn-primary' : 'btn-light border' ?> ecc-btn-pill gov-touch-ctrl" <?= $isActiveRef ? 'aria-current="page"' : '' ?>>
                    <span><?= esc($label) ?></span>
                    <span class="badge rounded-pill ecc-num <?= $isActiveRef ? 'bg-white text-primary' : 'bg-secondary-subtle text-dark' ?>"><?= count($referensi[$kode] ?? []) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="row g-4">
            <?php if (!empty($perms['can_manage_referensi'])): ?>
                <!-- Formulir Tambah / Ubah Data Rujukan -->
                <div class="col-12 col-xl-4">
                    <div class="ecc-gov-card">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <h2 class="h6 fw-bold text-dark mb-0" id="judulFormRef"><?= $isEditingRef ? 'Ubah ' : 'Tambah ' ?><?= esc($refLabelNow) ?></h2>
                            <button type="button" class="btn btn-light btn-sm border ecc-btn-pill gov-touch-ctrl <?= $isEditingRef ? '' : 'd-none' ?>" id="btnResetFormRef" onclick="resetFormReferensi()">Batal Ubah</button>
                        </div>

                        <form method="post" action="<?= site_url('ketarunaan/pengaturan/referensi/simpan') ?>" id="formReferensi" data-ref-label="<?= esc($refLabelNow, 'attr') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="jenis_referensi" value="<?= esc($activeRef) ?>">
                            <input type="hidden" name="ref_id" id="refIdInput" value="<?= esc((string) old('ref_id', '')) ?>">

                            <?php if ($activeRef === 'prodi'): ?>
                                <div class="mb-3">
                                    <label for="fKodeProdi" class="form-label small fw-semibold">Kode Program Studi</label>
                                    <input type="text" id="fKodeProdi" name="kode_prodi" class="form-control ecc-control-sm" value="<?= esc((string) old('kode_prodi', '')) ?>" required placeholder="Contoh: RSTJ">
                                </div>
                                <div class="mb-3">
                                    <label for="fNamaProdi" class="form-label small fw-semibold">Nama Program Studi</label>
                                    <input type="text" id="fNamaProdi" name="nama_prodi" class="form-control ecc-control-sm" value="<?= esc((string) old('nama_prodi', '')) ?>" required placeholder="Nama lengkap program studi">
                                </div>
                                <div class="mb-3">
                                    <label for="fJenjang" class="form-label small fw-semibold">Jenjang</label>
                                    <input type="text" id="fJenjang" name="jenjang" class="form-control ecc-control-sm" value="<?= esc((string) old('jenjang', 'D-IV')) ?>" required placeholder="Contoh: D-IV">
                                </div>
                            <?php elseif ($activeRef === 'angkatan'): ?>
                                <div class="mb-3">
                                    <label for="fTahunMasuk" class="form-label small fw-semibold">Tahun Masuk</label>
                                    <input type="number" id="fTahunMasuk" name="tahun_masuk" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('tahun_masuk', '')) ?>" min="1990" max="2100" required>
                                    <div class="ecc-text-muted-aa small mt-1" id="previewAngkatanLabel">Format otomatis: Tahun Masuk/Angka Romawi</div>
                                </div>
                                <div class="mb-3">
                                    <label for="fPerkiraanLulus" class="form-label small fw-semibold">Perkiraan Tahun Lulus</label>
                                    <input type="number" id="fPerkiraanLulus" name="perkiraan_lulus" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('perkiraan_lulus', '')) ?>" min="1993" max="2110" required>
                                </div>
                            <?php elseif ($activeRef === 'tahun_akademik'): ?>
                                <div class="mb-3">
                                    <label for="fKodeTahun" class="form-label small fw-semibold">Kode Tahun Akademik</label>
                                    <input type="text" id="fKodeTahun" name="kode_tahun" class="form-control ecc-control-sm" value="<?= esc((string) old('kode_tahun', '')) ?>" required placeholder="Contoh: 2026/2027-GANJIL">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="fTahunAkad" class="form-label small fw-semibold">Tahun</label>
                                        <input type="number" id="fTahunAkad" name="tahun" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('tahun', '2026')) ?>" min="2000" max="2100" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="fSemester" class="form-label small fw-semibold">Semester</label>
                                        <?php $oldSem = (string) old('semester', 'ganjil'); ?>
                                        <select id="fSemester" name="semester" class="form-select ecc-control-sm">
                                            <option value="ganjil" <?= $oldSem === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                            <option value="genap" <?= $oldSem === 'genap' ? 'selected' : '' ?>>Genap</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="fTglMulai" class="form-label small fw-semibold">Tanggal Mulai</label>
                                        <input type="text" id="fTglMulai" name="tanggal_mulai" class="form-control ecc-control-sm ecc-flatpickr-date" value="<?= esc((string) old('tanggal_mulai', '')) ?>" required placeholder="Pilih tanggal">
                                    </div>
                                    <div class="col-6">
                                        <label for="fTglSelesai" class="form-label small fw-semibold">Tanggal Selesai</label>
                                        <input type="text" id="fTglSelesai" name="tanggal_selesai" class="form-control ecc-control-sm ecc-flatpickr-date" value="<?= esc((string) old('tanggal_selesai', '')) ?>" required placeholder="Pilih tanggal">
                                    </div>
                                </div>
                            <?php elseif ($activeRef === 'kelas'): ?>
                                <div class="mb-3">
                                    <label for="fKelasProdi" class="form-label small fw-semibold">Program Studi</label>
                                    <?php $oldKp = (string) old('program_studi_id', ''); ?>
                                    <select id="fKelasProdi" name="program_studi_id" class="form-select ecc-control-sm" required>
                                        <option value="">Pilih Program Studi</option>
                                        <?php foreach ($prodi_list as $p): ?>
                                            <option value="<?= esc($p['id']) ?>" <?= $oldKp === (string) $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="fKelasAngkatan" class="form-label small fw-semibold">Angkatan</label>
                                        <?php $oldKa = (string) old('angkatan_id', ''); ?>
                                        <select id="fKelasAngkatan" name="angkatan_id" class="form-select ecc-control-sm" required>
                                            <option value="">Pilih Angkatan</option>
                                            <?php foreach ($angkatan_list as $a): ?>
                                                <option value="<?= esc($a['id']) ?>" <?= $oldKa === (string) $a['id'] ? 'selected' : '' ?>>
                                                    <?= esc(ketarunaan_format_angkatan($a)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="fKelasTingkat" class="form-label small fw-semibold">Tingkat</label>
                                        <input type="number" id="fKelasTingkat" name="tingkat" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('tingkat', '1')) ?>" min="1" max="4" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="fNamaKelas" class="form-label small fw-semibold">Nama Kelas</label>
                                    <input type="text" id="fNamaKelas" name="nama_kelas" class="form-control ecc-control-sm" value="<?= esc((string) old('nama_kelas', '')) ?>" required placeholder="Contoh: RSTJ 1A">
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="fKodeWilayah" class="form-label small fw-semibold">Kode Wilayah</label>
                                    <input type="text" id="fKodeWilayah" name="kode_wilayah" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('kode_wilayah', '')) ?>" required placeholder="Contoh: 53.01">
                                </div>
                                <div class="mb-3">
                                    <label for="fProvinsi" class="form-label small fw-semibold">Provinsi</label>
                                    <input type="text" id="fProvinsi" name="provinsi" class="form-control ecc-control-sm" value="<?= esc((string) old('provinsi', '')) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="fKabKota" class="form-label small fw-semibold">Kabupaten / Kota</label>
                                    <input type="text" id="fKabKota" name="kabupaten_kota" class="form-control ecc-control-sm" value="<?= esc((string) old('kabupaten_kota', '')) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="fKategori3tp" class="form-label small fw-semibold">Kategori 3TP</label>
                                    <?php $oldKat3 = (string) old('kategori_3tp', 'Daerah Tertinggal'); ?>
                                    <select id="fKategori3tp" name="kategori_3tp" class="form-select ecc-control-sm">
                                        <option value="Daerah Tertinggal" <?= $oldKat3 === 'Daerah Tertinggal' ? 'selected' : '' ?>>Daerah Tertinggal</option>
                                        <option value="Daerah Terdepan" <?= $oldKat3 === 'Daerah Terdepan' ? 'selected' : '' ?>>Daerah Terdepan</option>
                                        <option value="Daerah Terluar" <?= $oldKat3 === 'Daerah Terluar' ? 'selected' : '' ?>>Daerah Terluar</option>
                                        <option value="Perbatasan" <?= $oldKat3 === 'Perbatasan' ? 'selected' : '' ?>>Perbatasan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="fDasarAcuan" class="form-label small fw-semibold">Dasar Acuan Resmi</label>
                                    <input type="text" id="fDasarAcuan" name="dasar_acuan" class="form-control ecc-control-sm" value="<?= esc((string) old('dasar_acuan', '')) ?>" required placeholder="Contoh: Perpres No. 63 Tahun 2020">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-7">
                                        <label for="fVersiAcuan" class="form-label small fw-semibold">Versi Acuan</label>
                                        <input type="text" id="fVersiAcuan" name="versi_acuan" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('versi_acuan', '2024.1')) ?>" required>
                                    </div>
                                    <div class="col-5">
                                        <label for="fTahunBerlaku" class="form-label small fw-semibold">Tahun</label>
                                        <input type="number" id="fTahunBerlaku" name="tahun_berlaku" class="form-control ecc-control-sm ecc-num" value="<?= esc((string) old('tahun_berlaku', '2024')) ?>" min="2000" max="2100" required>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="fIsAktif" class="form-label small fw-semibold">Status Ketersediaan</label>
                                <?php $oldAktif = (string) old('is_aktif', '1'); ?>
                                <select id="fIsAktif" name="is_aktif" class="form-select ecc-control-sm">
                                    <option value="1" <?= $oldAktif === '1' ? 'selected' : '' ?>>Aktif (Dipakai dalam pengisian baru)</option>
                                    <option value="0" <?= $oldAktif === '0' ? 'selected' : '' ?>>Nonaktif (Disembunyikan dari pilihan baru)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold ecc-control-sm gov-touch-ctrl btn-tactile">Simpan Data Rujukan</button>
                            <p class="ecc-text-muted-aa mt-3 mb-0" style="font-size: 0.75rem;">
                                Perubahan rujukan tidak pernah menghapus riwayat taruna dan alumni yang sudah tercatat.
                            </p>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tabel Daftar Data Rujukan -->
            <div class="col-12 <?= !empty($perms['can_manage_referensi']) ? 'col-xl-8' : '' ?>">
                <div class="ecc-gov-card p-0 overflow-hidden">
                    <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h2 class="h6 fw-bold text-dark mb-0">Daftar <?= esc($refLabelNow) ?></h2>
                            <span class="ecc-text-muted-aa small">Gunakan tombol Nonaktifkan bila rujukan tidak lagi dipakai pada periode mendatang.</span>
                        </div>
                    </div>

                    <?php $refRows = $referensi[$activeRef] ?? []; ?>

                    <!-- Tampilan Desktop -->
                    <div class="ecc-table-scroll d-none d-md-block">
                        <table class="table ecc-table-gov table-hover mb-0">
                            <thead>
                                <tr>
                                    <?php if ($activeRef === 'prodi'): ?>
                                        <th>Kode</th>
                                        <th>Nama Program Studi</th>
                                        <th>Jenjang</th>
                                        <th>Taruna</th>
                                    <?php elseif ($activeRef === 'angkatan'): ?>
                                        <th>Angkatan</th>
                                        <th>Perkiraan Lulus</th>
                                        <th>Taruna</th>
                                    <?php elseif ($activeRef === 'tahun_akademik'): ?>
                                        <th>Kode Tahun</th>
                                        <th>Tahun / Semester</th>
                                        <th>Periode</th>
                                    <?php elseif ($activeRef === 'kelas'): ?>
                                        <th>Nama Kelas</th>
                                        <th>Program Studi</th>
                                        <th>Angkatan / Tingkat</th>
                                        <th>Taruna</th>
                                    <?php else: ?>
                                        <th>Kode</th>
                                        <th>Wilayah</th>
                                        <th>Kategori &amp; Acuan</th>
                                    <?php endif; ?>
                                    <th>Status</th>
                                    <?php if (!empty($perms['can_manage_referensi'])): ?>
                                        <th class="text-end">Kelola</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($refRows)): ?>
                                    <tr><td colspan="<?= (int) $colCount ?>" class="text-center ecc-text-muted-aa py-4">Belum ada data rujukan pada kategori ini.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($refRows as $row): ?>
                                        <tr>
                                            <?php if ($activeRef === 'prodi'): ?>
                                                <td class="fw-bold text-dark"><?= esc($row['kode_prodi']) ?></td>
                                                <td><?= esc($row['nama_prodi']) ?></td>
                                                <td class="small ecc-text-muted-aa"><?= esc($row['jenjang']) ?></td>
                                                <td class="ecc-num small"><?= number_format((int) ($row['total_taruna'] ?? 0)) ?></td>
                                            <?php elseif ($activeRef === 'angkatan'): ?>
                                                <td class="fw-bold text-dark ecc-num"><?= esc(ketarunaan_format_angkatan($row)) ?></td>
                                                <td class="ecc-num ecc-text-muted-aa"><?= esc((string) $row['perkiraan_lulus']) ?></td>
                                                <td class="ecc-num small"><?= number_format((int) ($row['total_taruna'] ?? 0)) ?></td>
                                            <?php elseif ($activeRef === 'tahun_akademik'): ?>
                                                <td class="fw-bold text-dark ecc-num"><?= esc($row['kode_tahun']) ?></td>
                                                <td class="small"><?= esc((string) $row['tahun']) ?> / <?= esc(ucfirst((string) $row['semester'])) ?></td>
                                                <td class="small ecc-num ecc-text-muted-aa"><?= esc(ketarunaan_format_tanggal((string) $row['tanggal_mulai'], true)) ?> - <?= esc(ketarunaan_format_tanggal((string) $row['tanggal_selesai'], true)) ?></td>
                                            <?php elseif ($activeRef === 'kelas'): ?>
                                                <td class="fw-bold text-dark"><?= esc($row['nama_kelas']) ?></td>
                                                <td class="small"><?= esc(($row['kode_prodi'] ?: '-') . ' - ' . ($row['nama_prodi'] ?: '-')) ?></td>
                                                <td class="small ecc-num"><?= esc(ketarunaan_format_angkatan($row)) ?> / Tingkat <?= (int) $row['tingkat'] ?></td>
                                                <td class="ecc-num small"><?= number_format((int) ($row['total_taruna'] ?? 0)) ?></td>
                                            <?php else: ?>
                                                <td class="fw-bold text-dark ecc-num"><?= esc($row['kode_wilayah']) ?></td>
                                                <td>
                                                    <div class="fw-semibold text-dark"><?= esc($row['kabupaten_kota']) ?></div>
                                                    <div class="small ecc-text-muted-aa"><?= esc($row['provinsi']) ?></div>
                                                </td>
                                                <td class="small">
                                                    <div><?= esc($row['kategori_3tp']) ?></div>
                                                    <div class="ecc-text-muted-aa"><?= esc($row['dasar_acuan']) ?> (v<?= esc($row['versi_acuan']) ?>)</div>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <?php if ((int) ($row['is_aktif'] ?? 0) === 1): ?>
                                                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1" style="font-size: 0.75rem;">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-1" style="font-size: 0.75rem;">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (!empty($perms['can_manage_referensi'])): ?>
                                                <td class="text-end text-nowrap">
                                                    <div class="d-inline-flex gap-2">
                                                        <button type="button" class="btn btn-light btn-sm border ecc-btn-pill btn-tactile" data-row="<?= esc(json_encode($row), 'attr') ?>" onclick="muatEditReferensi('<?= esc($activeRef) ?>', this)">
                                                            Ubah
                                                        </button>
                                                        <form method="post" action="<?= site_url('ketarunaan/pengaturan/referensi/toggle/' . urlencode($activeRef) . '/' . (int) $row['id']) ?>" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <button type="submit" class="btn btn-sm <?= (int) ($row['is_aktif'] ?? 0) === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> ecc-btn-pill btn-tactile" onclick="return konfirmasiToggleReferensi(this, event);">
                                                                <?= (int) ($row['is_aktif'] ?? 0) === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan Kartu Mobile -->
                    <div class="d-md-none p-3 d-flex flex-column gap-3">
                        <?php if (empty($refRows)): ?>
                            <div class="text-center ecc-text-muted-aa py-4 small">Belum ada data rujukan pada kategori ini.</div>
                        <?php else: ?>
                            <?php foreach ($refRows as $row): ?>
                                <div class="border rounded-4 p-3 bg-white">
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <div>
                                            <?php if ($activeRef === 'prodi'): ?>
                                                <div class="fw-bold text-dark"><?= esc($row['kode_prodi']) ?> — <?= esc($row['jenjang']) ?></div>
                                                <div class="small text-dark"><?= esc($row['nama_prodi']) ?></div>
                                            <?php elseif ($activeRef === 'angkatan'): ?>
                                                <div class="fw-bold text-dark ecc-num"><?= esc(ketarunaan_format_angkatan($row)) ?></div>
                                                <div class="small ecc-text-muted-aa ecc-num">Perkiraan lulus: <?= esc((string) $row['perkiraan_lulus']) ?></div>
                                            <?php elseif ($activeRef === 'tahun_akademik'): ?>
                                                <div class="fw-bold text-dark ecc-num"><?= esc($row['kode_tahun']) ?></div>
                                                <div class="small text-dark"><?= esc((string) $row['tahun']) ?> / <?= esc(ucfirst((string) $row['semester'])) ?></div>
                                            <?php elseif ($activeRef === 'kelas'): ?>
                                                <div class="fw-bold text-dark"><?= esc($row['nama_kelas']) ?></div>
                                                <div class="small ecc-text-muted-aa"><?= esc(($row['kode_prodi'] ?: '-') . ' - ' . ($row['nama_prodi'] ?: '-')) ?></div>
                                            <?php else: ?>
                                                <div class="fw-bold text-dark ecc-num"><?= esc($row['kode_wilayah']) ?> — <?= esc($row['kabupaten_kota']) ?></div>
                                                <div class="small ecc-text-muted-aa"><?= esc($row['provinsi']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ((int) ($row['is_aktif'] ?? 0) === 1): ?>
                                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-1 flex-shrink-0" style="font-size: 0.75rem;">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="small ecc-text-muted-aa mb-2">
                                        <?php if ($activeRef === 'prodi' || $activeRef === 'angkatan'): ?>
                                            <span class="ecc-num">Taruna terdaftar: <?= number_format((int) ($row['total_taruna'] ?? 0)) ?></span>
                                        <?php elseif ($activeRef === 'tahun_akademik'): ?>
                                            <span class="ecc-num">Periode: <?= esc(ketarunaan_format_tanggal((string) $row['tanggal_mulai'], true)) ?> - <?= esc(ketarunaan_format_tanggal((string) $row['tanggal_selesai'], true)) ?></span>
                                        <?php elseif ($activeRef === 'kelas'): ?>
                                            <span class="ecc-num"><?= esc(ketarunaan_format_angkatan($row)) ?> / Tingkat <?= (int) $row['tingkat'] ?> • Taruna: <?= number_format((int) ($row['total_taruna'] ?? 0)) ?></span>
                                        <?php else: ?>
                                            <span><?= esc($row['kategori_3tp']) ?> • <?= esc($row['dasar_acuan']) ?> (v<?= esc($row['versi_acuan']) ?>)</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($perms['can_manage_referensi'])): ?>
                                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                            <button type="button" class="btn btn-light btn-sm border ecc-btn-pill gov-touch-ctrl flex-fill btn-tactile" data-row="<?= esc(json_encode($row), 'attr') ?>" onclick="muatEditReferensi('<?= esc($activeRef) ?>', this)">
                                                Ubah
                                            </button>
                                            <form method="post" action="<?= site_url('ketarunaan/pengaturan/referensi/toggle/' . urlencode($activeRef) . '/' . (int) $row['id']) ?>" class="flex-fill">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm w-100 <?= (int) ($row['is_aktif'] ?? 0) === 1 ? 'btn-outline-danger' : 'btn-outline-success' ?> ecc-btn-pill gov-touch-ctrl btn-tactile" onclick="return konfirmasiToggleReferensi(this, event);">
                                                    <?= (int) ($row['is_aktif'] ?? 0) === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($activeTab === 'peran'): ?>
        <!-- Tab Matriks Kewenangan Peran & Akun Ringkas -->
        <div class="ecc-gov-card p-0 overflow-hidden mb-4">
            <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Matriks Kewenangan Peran Ketarunaan</h2>
                    <span class="ecc-text-muted-aa small">Batasan akses setiap peran terhadap validasi data, data pembinaan sensitif, dan data rujukan.</span>
                </div>
                <?php if (!empty($perms['can_manage_users'])): ?>
                    <a href="<?= site_url('users') ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold ecc-control-sm gov-touch-ctrl d-inline-flex align-items-center justify-content-center btn-tactile">
                        Kelola Akun Pengguna
                    </a>
                <?php endif; ?>
            </div>

            <div class="ecc-table-scroll d-none d-md-block">
                <table class="table ecc-table-gov table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Peran</th>
                            <th>Kode Peran</th>
                            <th>Cakupan Tugas</th>
                            <th>Hak Validasi</th>
                            <th>Pembinaan &amp; Disiplin</th>
                            <th>Data Rujukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roleMatrix as $rm): ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= esc($rm['peran']) ?></td>
                                <td class="small ecc-text-muted-aa"><?= esc($rm['kode']) ?></td>
                                <td class="small"><?= esc($rm['cakupan']) ?></td>
                                <td class="small"><?= esc($rm['validasi']) ?></td>
                                <td class="small"><?= esc($rm['pelanggaran']) ?></td>
                                <td class="small"><?= esc($rm['referensi']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-md-none p-3 d-flex flex-column gap-3">
                <?php foreach ($roleMatrix as $rm): ?>
                    <div class="border rounded-4 p-3 bg-white">
                        <div class="fw-bold text-dark"><?= esc($rm['peran']) ?></div>
                        <div class="small ecc-text-muted-aa mb-2"><?= esc($rm['kode']) ?></div>
                        <p class="small text-dark mb-2"><?= esc($rm['cakupan']) ?></p>
                        <div class="small d-flex flex-column gap-1 border-top pt-2">
                            <div><span class="fw-semibold text-dark">Hak Validasi:</span> <span class="ecc-text-muted-aa"><?= esc($rm['validasi']) ?></span></div>
                            <div><span class="fw-semibold text-dark">Pembinaan &amp; Disiplin:</span> <span class="ecc-text-muted-aa"><?= esc($rm['pelanggaran']) ?></span></div>
                            <div><span class="fw-semibold text-dark">Data Rujukan:</span> <span class="ecc-text-muted-aa"><?= esc($rm['referensi']) ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="ecc-gov-card p-0 overflow-hidden">
            <div class="p-3 p-md-4 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Daftar Ringkas Pengguna &amp; Peran Aktif</h2>
                    <span class="ecc-text-muted-aa small">Penugasan peran dikelola melalui menu pengguna pusat agar tetap selaras lintas modul.</span>
                </div>
                <div style="min-width: 240px;">
                    <label for="cariPenggunaPeran" class="visually-hidden">Cari pengguna atau peran</label>
                    <input type="search" id="cariPenggunaPeran" class="form-control ecc-control-sm gov-touch-ctrl" placeholder="Cari nama, NIP, unit, atau peran...">
                </div>
            </div>

            <div class="ecc-table-scroll d-none d-md-block">
                <table class="table ecc-table-gov table-hover mb-0" id="tabelPenggunaPeran">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Nama Pengguna / NIP</th>
                            <th>Unit Kerja</th>
                            <th>Peran Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($userDir)): ?>
                            <tr><td colspan="4" class="text-center ecc-text-muted-aa py-4">Belum ada data pengguna yang ditampilkan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($userDir as $usr): ?>
                                <?php
                                $searchBlob = strtolower(implode(' ', [
                                    (string) ($usr['nama_lengkap'] ?? ''),
                                    (string) ($usr['username'] ?? ''),
                                    (string) ($usr['nip'] ?? ''),
                                    (string) ($usr['unit'] ?? ''),
                                    implode(' ', $usr['peran_list'] ?? []),
                                ]));
                                ?>
                                <tr data-user-search="<?= esc($searchBlob, 'attr') ?>">
                                    <td class="fw-semibold text-dark"><?= esc($usr['nama_lengkap'] ?: $usr['username']) ?></td>
                                    <td class="small ecc-num ecc-text-muted-aa"><?= esc($usr['username']) ?><?= !empty($usr['nip']) && $usr['nip'] !== '-' ? ' • ' . esc($usr['nip']) : '' ?></td>
                                    <td class="small"><?= esc($usr['unit'] ?: '-') ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach (($usr['peran_list'] ?? []) as $pr): ?>
                                                <span class="badge rounded-pill bg-light text-dark border px-3 py-1" style="font-size: 0.75rem;"><?= esc($pr) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-md-none p-3 d-flex flex-column gap-3" id="kartuPenggunaPeran">
                <?php if (empty($userDir)): ?>
                    <div class="text-center ecc-text-muted-aa py-4 small">Belum ada data pengguna yang ditampilkan.</div>
                <?php else: ?>
                    <?php foreach ($userDir as $usr): ?>
                        <?php
                        $searchBlob = strtolower(implode(' ', [
                            (string) ($usr['nama_lengkap'] ?? ''),
                            (string) ($usr['username'] ?? ''),
                            (string) ($usr['nip'] ?? ''),
                            (string) ($usr['unit'] ?? ''),
                            implode(' ', $usr['peran_list'] ?? []),
                        ]));
                        ?>
                        <div class="border rounded-4 p-3 bg-white" data-user-search="<?= esc($searchBlob, 'attr') ?>">
                            <div class="fw-bold text-dark"><?= esc($usr['nama_lengkap'] ?: $usr['username']) ?></div>
                            <div class="small ecc-num ecc-text-muted-aa mb-1"><?= esc($usr['username']) ?><?= !empty($usr['nip']) && $usr['nip'] !== '-' ? ' • ' . esc($usr['nip']) : '' ?></div>
                            <div class="small text-dark mb-2"><?= esc($usr['unit'] ?: '-') ?></div>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach (($usr['peran_list'] ?? []) as $pr): ?>
                                    <span class="badge rounded-pill bg-light text-dark border px-3 py-1" style="font-size: 0.75rem;"><?= esc($pr) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div id="emptyCariPengguna" class="text-center ecc-text-muted-aa py-4 small d-none">Tidak ada pengguna yang cocok dengan kata kunci.</div>
        </div>

    <?php else: ?>
        <!-- Tab Parameter Operasional ECC 2.0 (NFR-09) -->
        <div class="ecc-gov-card">
            <h2 class="h6 fw-bold text-dark mb-1">Parameter Operasional &amp; Penanda Data</h2>
            <p class="ecc-text-muted-aa small mb-4">Mengatur tanda lingkungan data pada Dashboard dan Laporan Resmi serta batas ukur operasional.</p>

            <?php $canEditOps = !empty($perms['can_manage_operasional']); ?>
            <?php if (!$canEditOps): ?>
                <div class="alert alert-light border rounded-3 mb-4 py-2 px-3 small ecc-text-muted-aa" role="status">
                    Akun Anda hanya memiliki akses lihat untuk parameter operasional.
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('ketarunaan/pengaturan/operasional/simpan') ?>" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-12 col-md-4">
                    <label for="opsDataMode" class="form-label small fw-semibold">Penanda Jenis Data (Dashboard &amp; Laporan)</label>
                    <?php $oldMode = (string) old('ecc2_data_mode', $operasional['ecc2_data_mode'] ?? 'simulasi'); ?>
                    <select id="opsDataMode" name="ecc2_data_mode" class="form-select ecc-control-sm gov-touch-ctrl" <?= $canEditOps ? '' : 'disabled' ?>>
                        <option value="simulasi" <?= $oldMode === 'simulasi' ? 'selected' : '' ?>>Data Simulasi</option>
                        <option value="produksi" <?= $oldMode === 'produksi' ? 'selected' : '' ?>>Data Operasional</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="opsSkalaIp" class="form-label small fw-semibold">Batas Maksimal Skala IP</label>
                    <input type="text" id="opsSkalaIp" name="ecc2_skala_ip_maks" class="form-control ecc-control-sm gov-touch-ctrl ecc-num" value="<?= esc((string) old('ecc2_skala_ip_maks', $operasional['ecc2_skala_ip_maks'] ?? '4.00')) ?>" <?= $canEditOps ? 'required' : 'disabled' ?>>
                    <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">Rentang sah: 2,00–4,50.</div>
                </div>
                <div class="col-12 col-md-4">
                    <label for="opsBatasJam" class="form-label small fw-semibold">Batas Jam Tayang Lowongan</label>
                    <input type="number" id="opsBatasJam" name="ecc2_batas_jam_lowongan" class="form-control ecc-control-sm gov-touch-ctrl ecc-num" min="1" max="720" value="<?= esc((string) old('ecc2_batas_jam_lowongan', $operasional['ecc2_batas_jam_lowongan'] ?? '24')) ?>" <?= $canEditOps ? 'required' : 'disabled' ?>>
                    <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">Rentang sah: 1–720 jam.</div>
                </div>
                <div class="col-12">
                    <label for="opsCatatan" class="form-label small fw-semibold">Catatan Operasional pada Kepala Laporan</label>
                    <textarea id="opsCatatan" name="ecc2_catatan_operasional" class="form-control rounded-3" rows="2" maxlength="255" <?= $canEditOps ? 'required' : 'disabled' ?>><?= esc((string) old('ecc2_catatan_operasional', $operasional['ecc2_catatan_operasional'] ?? '')) ?></textarea>
                </div>
                <?php if ($canEditOps): ?>
                    <div class="col-12 d-flex justify-content-end pt-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold ecc-control-sm gov-touch-ctrl btn-tactile">Simpan Parameter</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function muatEditReferensi(jenis, btn) {
        const raw = btn.getAttribute('data-row');
        if (!raw) return;
        const row = JSON.parse(raw);

        const form = document.getElementById('formReferensi');
        if (!form) return;

        const refLabel = form.getAttribute('data-ref-label') || 'Data Rujukan';
        document.getElementById('refIdInput').value = row.id || '';
        const titleEl = document.getElementById('judulFormRef');
        const resetBtn = document.getElementById('btnResetFormRef');
        if (titleEl) titleEl.textContent = 'Ubah ' + refLabel;
        if (resetBtn) resetBtn.classList.remove('d-none');

        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (!el) return;
            if (el._flatpickr) {
                el._flatpickr.setDate(val || null, false);
            } else {
                el.value = val ?? '';
            }
        };

        if (jenis === 'prodi') {
            setVal('fKodeProdi', row.kode_prodi);
            setVal('fNamaProdi', row.nama_prodi);
            setVal('fJenjang', row.jenjang);
        } else if (jenis === 'angkatan') {
            setVal('fTahunMasuk', row.tahun_masuk);
            setVal('fPerkiraanLulus', row.perkiraan_lulus);
            if (typeof updateAngkatanPreview === 'function') updateAngkatanPreview();
        } else if (jenis === 'tahun_akademik') {
            setVal('fKodeTahun', row.kode_tahun);
            setVal('fTahunAkad', row.tahun);
            setVal('fSemester', row.semester);
            setVal('fTglMulai', row.tanggal_mulai);
            setVal('fTglSelesai', row.tanggal_selesai);
        } else if (jenis === 'kelas') {
            setVal('fKelasProdi', row.program_studi_id);
            setVal('fKelasAngkatan', row.angkatan_id);
            setVal('fKelasTingkat', row.tingkat);
            setVal('fNamaKelas', row.nama_kelas);
        } else if (jenis === 'wilayah_3tp') {
            setVal('fKodeWilayah', row.kode_wilayah);
            setVal('fProvinsi', row.provinsi);
            setVal('fKabKota', row.kabupaten_kota);
            setVal('fKategori3tp', row.kategori_3tp);
            setVal('fDasarAcuan', row.dasar_acuan);
            setVal('fVersiAcuan', row.versi_acuan);
            setVal('fTahunBerlaku', row.tahun_berlaku);
        }
        setVal('fIsAktif', String(row.is_aktif ?? 1));

        const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        form.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });

        const firstControl = form.querySelector('input:not([type="hidden"]):not([disabled]), select:not([disabled])');
        if (firstControl && typeof firstControl.focus === 'function') {
            firstControl.focus({ preventScroll: true });
        }
    }

    function resetFormReferensi() {
        const form = document.getElementById('formReferensi');
        if (!form) return;
        form.reset();
        form.querySelectorAll('.ecc-flatpickr-date').forEach(function (el) {
            if (el._flatpickr) el._flatpickr.clear();
        });
        document.getElementById('refIdInput').value = '';
        const refLabel = form.getAttribute('data-ref-label') || 'Data Rujukan';
        const titleEl = document.getElementById('judulFormRef');
        const resetBtn = document.getElementById('btnResetFormRef');
        if (titleEl) titleEl.textContent = 'Tambah ' + refLabel;
        if (resetBtn) resetBtn.classList.add('d-none');
        if (typeof updateAngkatanPreview === 'function') updateAngkatanPreview();
    }

    function konfirmasiToggleReferensi(btn, ev) {
        const form = btn.closest('form');
        if (!form) return true;
        const aksi = btn.textContent.trim();

        if (typeof Swal !== 'undefined') {
            ev.preventDefault();
            Swal.fire({
                title: aksi + ' Data?',
                text: 'Riwayat taruna yang sudah ada tetap dipertahankan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-4 shadow border-0' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        return confirm(aksi + ' data rujukan ini?');
    }

    function toRomanNumeral(num) {
        if (!Number.isInteger(num) || num <= 0) return '';
        const map = [
            [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
            [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
            [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
        ];
        let res = '';
        let n = num;
        for (const [val, sym] of map) {
            while (n >= val) {
                res += sym;
                n -= val;
            }
        }
        return res;
    }

    function updateAngkatanPreview() {
        const input = document.getElementById('fTahunMasuk');
        const label = document.getElementById('previewAngkatanLabel');
        if (!input || !label) return;
        const yr = parseInt(input.value, 10);
        if (!Number.isInteger(yr) || yr < 1990 || yr > 2100) {
            label.textContent = 'Format otomatis: Tahun Masuk/Angka Romawi';
            return;
        }
        const rom = toRomanNumeral(yr - 1989);
        label.textContent = 'Format tersimpan: ' + yr + '/' + rom;
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof flatpickr !== 'undefined') {
            document.querySelectorAll('.ecc-flatpickr-date').forEach(function (el) {
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default'
                });
            });
        }

        const input = document.getElementById('fTahunMasuk');
        if (input) {
            input.addEventListener('input', updateAngkatanPreview);
            updateAngkatanPreview();
        }

        const searchUser = document.getElementById('cariPenggunaPeran');
        if (searchUser) {
            const emptyBox = document.getElementById('emptyCariPengguna');
            const filterUsers = function () {
                const q = searchUser.value.trim().toLowerCase();
                let visibleRows = 0;
                document.querySelectorAll('#tabelPenggunaPeran tbody tr[data-user-search]').forEach(function (tr) {
                    const match = q === '' || (tr.getAttribute('data-user-search') || '').indexOf(q) !== -1;
                    tr.classList.toggle('d-none', !match);
                    if (match) visibleRows++;
                });
                document.querySelectorAll('#kartuPenggunaPeran [data-user-search]').forEach(function (card) {
                    const match = q === '' || (card.getAttribute('data-user-search') || '').indexOf(q) !== -1;
                    card.classList.toggle('d-none', !match);
                });
                if (emptyBox) {
                    emptyBox.classList.toggle('d-none', visibleRows > 0);
                }
            };
            searchUser.addEventListener('input', filterUsers);
        }
    });
</script>
<?= $this->endSection() ?>
