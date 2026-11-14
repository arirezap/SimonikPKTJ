<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Prestasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .ecc-pr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    @media (max-width: 767.98px) {
        .ecc-pr-card {
            padding: 16px;
        }
    }
    .ecc-icon-48 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #eff6ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .ecc-tab-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 24px;
    }
    .ecc-tab-link {
        min-height: 36px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ecc-tab-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-tab-link.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-panel-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        background-color: #ffffff;
    }
    .ecc-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .ecc-meta-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
    .ecc-table-pr thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .ecc-table-pr tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-doc-thumb {
        width: 64px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .ecc-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail          = $detail ?? [];
$prestasi        = $detail['prestasi'] ?? [];
$peserta         = $detail['peserta'] ?? [];
$dokumentasi     = $detail['dokumentasi'] ?? [];
$riwayat         = $detail['riwayat'] ?? [];
$activeTab       = $active_tab ?? 'ringkasan';
$canManageTaruna = !empty($canManageTaruna);
$canValidate     = !empty($canValidate);

$idPrestasi      = (int) ($prestasi['id'] ?? 0);
$rawStatus       = (string) ($prestasi['status_validasi'] ?? 'draf');
$stValidasi      = ($rawStatus === 'perlu_perbaikan') ? 'dikembalikan' : $rawStatus;

$jenisMap        = ketarunaan_jenis_prestasi_catalog();
$tingkatMap      = ketarunaan_tingkat_prestasi_catalog();
$jenisLbl        = $jenisMap[$prestasi['jenis_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['jenis_prestasi'] ?? '-'));
$tingkatLbl      = $tingkatMap[$prestasi['tingkat_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));

$tglMulai        = ketarunaan_format_tanggal((string) ($prestasi['tanggal_mulai'] ?? ''), true);
$tglSelesai      = ketarunaan_format_tanggal((string) ($prestasi['tanggal_selesai'] ?? ''), true);
$rentangTgl      = ($tglSelesai !== '-' && $tglSelesai !== $tglMulai) ? ($tglMulai . ' – ' . $tglSelesai) : $tglMulai;

$buktiBerkas     = (string) ($prestasi['bukti_berkas'] ?? '');
$ukuranKb        = (int) ($prestasi['ukuran_berkas_kb'] ?? 0);
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-secondary">ECC 2.0</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Detail Prestasi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1">Detail Prestasi</h4>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Rincian prestasi taruna, peserta, dokumen bukti, dan riwayat validasi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-chevron-left"></i> Kembali ke Daftar
            </a>
            <a href="<?= base_url('ketarunaan/prestasi/unduh-ringkasan/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-download"></i> Unduh Ringkasan
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-4 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span style="font-size: 0.875rem;"><?= esc(session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Kartu Ringkas Prestasi -->
    <div class="ecc-pr-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?= esc((string) ($prestasi['nama_prestasi'] ?? '-')) ?></h5>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
                        <span><?= esc($jenisLbl) ?></span>
                        <span>&bull;</span>
                        <span><?= esc($tingkatLbl) ?></span>
                        <span>&bull;</span>
                        <span class="fw-semibold text-dark"><?= esc((string) ($prestasi['peringkat'] ?? '-')) ?></span>
                        <?= ketarunaan_keikutsertaan_badge((string) ($prestasi['jenis_keikutsertaan'] ?? 'individu')) ?>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 ecc-num"><?= count($peserta) ?> peserta</span>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <?= ketarunaan_validasi_badge($stValidasi) ?>
                <?php if ($canManageTaruna): ?>
                    <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold">
                        <?= $rawStatus === 'tervalidasi' ? 'Ajukan Koreksi' : 'Edit Prestasi' ?>
                    </a>
                    <?php if ($rawStatus !== 'tervalidasi'): ?>
                        <form method="post" action="<?= base_url('ketarunaan/prestasi/hapus/' . $idPrestasi) ?>" class="d-inline" id="formHapusPrestasi">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold">Hapus Draf</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($rawStatus === 'perlu_perbaikan' && !empty($prestasi['catatan_validasi'])): ?>
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 px-3 py-2" role="alert">
                <div class="fw-semibold text-dark" style="font-size: 0.8125rem;">Catatan Perbaikan Validator</div>
                <div class="text-dark mt-1" style="font-size: 0.875rem;"><?= esc((string) $prestasi['catatan_validasi']) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kontainer 4 Tab -->
    <div class="ecc-pr-card">
        <div class="ecc-tab-nav">
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=ringkasan') ?>" class="ecc-tab-link <?= $activeTab === 'ringkasan' ? 'active' : '' ?>">Ringkasan</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=peserta') ?>" class="ecc-tab-link ecc-num <?= $activeTab === 'peserta' ? 'active' : '' ?>">Peserta (<?= count($peserta) ?>)</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=dokumen') ?>" class="ecc-tab-link <?= $activeTab === 'dokumen' ? 'active' : '' ?>">Dokumen Bukti</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=riwayat') ?>" class="ecc-tab-link <?= $activeTab === 'riwayat' ? 'active' : '' ?>">Riwayat Validasi</a>
        </div>

        <?php if ($activeTab === 'ringkasan'): ?>
            <div class="row g-4">
                <!-- Kolom Kiri Ringkasan -->
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <h6 class="fw-bold text-dark mb-3">Informasi Prestasi</h6>
                        <div class="row g-3" style="font-size: 0.875rem;">
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Jenis Prestasi</div>
                                <div class="fw-semibold text-dark"><?= esc($jenisLbl) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Bidang / Subkategori</div>
                                <div class="fw-semibold text-dark"><?= esc((string) ($prestasi['bidang_prestasi'] ?: '-')) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Tingkat Prestasi</div>
                                <div class="fw-semibold text-dark"><?= esc($tingkatLbl) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Peringkat / Juara</div>
                                <div class="fw-semibold text-dark"><?= esc((string) ($prestasi['peringkat'] ?: '-')) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Penyelenggara</div>
                                <div class="fw-semibold text-dark"><?= esc((string) ($prestasi['penyelenggara'] ?: '-')) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Lokasi Kegiatan</div>
                                <div class="fw-semibold text-dark"><?= esc((string) ($prestasi['lokasi_kegiatan'] ?: '-')) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Tanggal Pelaksanaan</div>
                                <div class="fw-semibold text-dark ecc-num"><?= esc($rentangTgl) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Keikutsertaan</div>
                                <div class="fw-semibold text-dark">
                                    <?= esc(ucfirst((string) ($prestasi['jenis_keikutsertaan'] ?? 'individu'))) ?>
                                    <?php if (!empty($prestasi['nama_tim'])): ?>
                                        (<?= esc((string) $prestasi['nama_tim']) ?>)
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Pembimbing</div>
                                <div class="fw-semibold text-dark"><?= esc((string) ($prestasi['pembimbing'] ?: '-')) ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="text-secondary" style="font-size: 0.75rem;">Unit Pemilik &amp; Sumber Data</div>
                                <div class="fw-semibold text-dark">
                                    <?= esc((string) ($prestasi['unit_pemilik'] ?: '-')) ?>
                                    &bull;
                                    <?= esc((string) ($prestasi['sumber_data'] ?: '-')) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ecc-panel-box">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0 ecc-num">Peserta Prestasi (<?= count($peserta) ?> Taruna)</h6>
                            <?php if ($canManageTaruna): ?>
                                <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">Kelola Peserta</a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table ecc-table-pr mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">No</th>
                                        <th>Taruna</th>
                                        <th>Prodi</th>
                                        <th>Peran</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($peserta)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-secondary py-4">Belum ada peserta terdaftar pada prestasi ini.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($peserta as $idx => $ps): ?>
                                            <tr>
                                                <td class="ecc-num text-secondary"><?= $idx + 1 ?></td>
                                                <td>
                                                    <div class="fw-semibold text-dark"><?= esc((string) $ps['nama_lengkap']) ?></div>
                                                    <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                        <?= esc((string) $ps['nomor_taruna']) ?> &bull; Angkatan <?= esc((string) $ps['angkatan']) ?>
                                                    </div>
                                                </td>
                                                <td><?= esc((string) ($ps['kode_prodi'] ?? '-')) ?></td>
                                                <td><?= esc((string) ($ps['peran'] ?: 'Peserta')) ?></td>
                                                <td class="text-end">
                                                    <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Rekam Jejak</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan Ringkasan -->
                <div class="col-12 col-lg-4 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <h6 class="fw-bold text-dark mb-0">Bukti Utama</h6>
                            <?php if ($canManageTaruna): ?>
                                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">Kelola</a>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Sertifikat / SK</div>
                            <div class="fw-semibold text-dark ecc-num"><?= esc((string) ($prestasi['nomor_sertifikat'] ?: '-')) ?></div>
                        </div>

                        <?php if ($buktiBerkas !== ''): ?>
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8125rem;"><?= esc(basename($buktiBerkas)) ?></div>
                                <?php if ($ukuranKb > 0): ?>
                                    <div class="text-secondary ecc-num mt-1" style="font-size: 0.75rem;"><?= number_format($ukuranKb, 0, ',', '.') ?> KB</div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold flex-fill">Pratinjau</a>
                                <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold flex-fill">Unduh</a>
                            </div>
                        <?php else: ?>
                            <div class="text-secondary" style="font-size: 0.875rem;">Belum ada dokumen bukti utama yang diunggah.</div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <h6 class="fw-bold text-dark mb-3">Informasi Validasi</h6>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status</span>
                            <span><?= ketarunaan_validasi_badge($stValidasi) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Diajukan oleh</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($prestasi['diajukan_oleh'] ?: '-')) ?></span>
                        </div>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Divalidasi oleh</span>
                            <span class="fw-semibold text-dark text-end"><?= esc((string) ($prestasi['divalidasi_oleh'] ?: '-')) ?></span>
                        </div>
                        <?php if (!empty($prestasi['catatan_validasi'])): ?>
                            <div class="ecc-meta-row flex-column">
                                <span class="text-secondary">Catatan</span>
                                <span class="text-dark"><?= esc((string) $prestasi['catatan_validasi']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($canValidate && in_array($rawStatus, ['diajukan', 'perlu_perbaikan'], true)): ?>
                            <div class="d-flex flex-column gap-2 pt-3 mt-3 border-top">
                                <form method="post" action="<?= base_url('ketarunaan/prestasi/validasi/' . $idPrestasi) ?>" id="formValidasiPrestasi">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="keputusan" value="tervalidasi">
                                    <button type="submit" class="btn btn-success ecc-control-sm w-100 fw-semibold">Validasi Prestasi</button>
                                </form>
                                <?php if ($rawStatus === 'diajukan'): ?>
                                    <button type="button" class="btn btn-outline-warning ecc-control-sm w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKembalikanPrestasi">
                                        Kembalikan untuk Perbaikan
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($activeTab === 'peserta'): ?>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-1 ecc-num">Daftar Peserta Prestasi (<?= count($peserta) ?> Taruna)</h6>
                    <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Setiap taruna terhubung langsung dengan Rekam Jejak masing-masing.</p>
                </div>
                <?php if ($canManageTaruna): ?>
                    <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold">Kelola Peserta</a>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table ecc-table-pr mb-0">
                    <thead>
                        <tr>
                            <th style="width: 48px;">No</th>
                            <th>Nama Taruna</th>
                            <th>Nomor Taruna</th>
                            <th>Prodi</th>
                            <th>Angkatan</th>
                            <th>Status</th>
                            <th>Peran</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peserta)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">Belum ada peserta terdaftar pada prestasi ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($peserta as $idx => $ps): ?>
                                <tr>
                                    <td class="ecc-num text-secondary"><?= $idx + 1 ?></td>
                                    <td class="fw-semibold text-dark"><?= esc((string) $ps['nama_lengkap']) ?></td>
                                    <td class="ecc-num"><?= esc((string) $ps['nomor_taruna']) ?></td>
                                    <td><?= esc((string) ($ps['kode_prodi'] ?? '-')) ?></td>
                                    <td class="ecc-num"><?= esc((string) $ps['angkatan']) ?></td>
                                    <td><?= ketarunaan_status_badge((string) ($ps['status_terkini'] ?? 'aktif')) ?></td>
                                    <td><?= esc((string) ($ps['peran'] ?: 'Peserta')) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Rekam Jejak</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($activeTab === 'dokumen'): ?>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-1">Dokumen Bukti &amp; Dokumentasi</h6>
                    <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Berkas bukti utama piagam dan dokumentasi pendukung kegiatan.</p>
                </div>
                <?php if ($canManageTaruna): ?>
                    <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold">Kelola Dokumen</a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="ecc-panel-box h-100">
                        <h6 class="fw-bold text-dark mb-3">Bukti Utama Prestasi</h6>
                        <div class="mb-3">
                            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Sertifikat / Piagam / SK</div>
                            <div class="fw-semibold text-dark ecc-num"><?= esc((string) ($prestasi['nomor_sertifikat'] ?: '-')) ?></div>
                        </div>

                        <?php if ($buktiBerkas !== ''): ?>
                            <div class="p-3 rounded-3 bg-light border mb-3">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.875rem;"><?= esc(basename($buktiBerkas)) ?></div>
                                <?php if ($ukuranKb > 0): ?>
                                    <div class="text-secondary ecc-num mt-1" style="font-size: 0.75rem;">Ukuran: <?= number_format($ukuranKb, 0, ',', '.') ?> KB</div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">Pratinjau PDF</a>
                                <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold">Unduh</a>
                            </div>
                        <?php else: ?>
                            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Belum ada berkas bukti utama yang diunggah.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="ecc-panel-box h-100">
                        <h6 class="fw-bold text-dark mb-3 ecc-num">Dokumentasi Tambahan (<?= count($dokumentasi) ?> Berkas)</h6>
                        <?php if (empty($dokumentasi)): ?>
                            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Tidak ada foto dokumentasi tambahan.</p>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($dokumentasi as $foto): ?>
                                    <?php
                                    $fotoSafe     = basename((string) $foto);
                                    $physicalFoto = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . $fotoSafe;
                                    $hasDiskFoto  = is_file($physicalFoto);
                                    $previewUrl   = base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode($fotoSafe) . '&inline=1');
                                    $downloadUrl  = base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode($fotoSafe));
                                    ?>
                                    <div class="d-flex align-items-center justify-content-between gap-2 p-2 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <?php if ($hasDiskFoto): ?>
                                                <img src="<?= esc($previewUrl) ?>" alt="<?= esc($fotoSafe) ?>" class="ecc-doc-thumb">
                                            <?php endif; ?>
                                            <span class="fw-medium text-dark text-truncate" style="font-size: 0.8125rem;"><?= esc($fotoSafe) ?></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <a href="<?= esc($previewUrl) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Pratinjau</a>
                                            <a href="<?= esc($downloadUrl) ?>" class="btn btn-light border btn-sm rounded-pill px-3">Unduh</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($activeTab === 'riwayat'): ?>
            <div class="mb-3">
                <h6 class="fw-bold text-dark mb-1">Riwayat Validasi &amp; Perubahan Status</h6>
                <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Catatan perubahan status pengajuan dan hasil pemeriksaan validator.</p>
            </div>

            <div class="table-responsive mb-4">
                <table class="table ecc-table-pr mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                            <th>Pelaku</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">Belum ada riwayat validasi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $row): ?>
                                <?php
                                $rowSt = (string) ($row['status'] ?? 'draf');
                                if ($rowSt === 'perlu_perbaikan') {
                                    $rowSt = 'dikembalikan';
                                }
                                ?>
                                <tr>
                                    <td class="ecc-num text-nowrap"><?= esc(ketarunaan_format_tanggal((string) ($row['tanggal'] ?? ''), true)) ?></td>
                                    <td class="fw-semibold text-dark"><?= esc((string) ($row['aksi'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($row['pelaku'] ?? '-')) ?></td>
                                    <td><?= ketarunaan_validasi_badge($rowSt) ?></td>
                                    <td><?= esc((string) ($row['catatan'] ?? '-')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-3 py-2 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; color: #475569; font-size: 0.8125rem;">
                Riwayat perubahan tersimpan dan tidak dapat dihapus oleh penginput.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($canValidate && $rawStatus === 'diajukan'): ?>
    <!-- Modal Kembalikan untuk Perbaikan -->
    <div class="modal fade" id="modalKembalikanPrestasi" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form method="post" action="<?= base_url('ketarunaan/prestasi/validasi/' . $idPrestasi) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="perlu_perbaikan">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold" id="modalKembalikanLabel">Kembalikan Prestasi</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <label for="catatanPerbaikanInput" class="form-label fw-semibold text-dark" style="font-size: 0.875rem;">
                            Catatan Perbaikan <span class="text-danger">*</span>
                        </label>
                        <textarea
                            id="catatanPerbaikanInput"
                            name="catatan_validasi"
                            class="form-control"
                            rows="3"
                            placeholder="Tuliskan bagian yang perlu dilengkapi atau diperbaiki..."
                            required
                        ></textarea>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light border ecc-control-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning ecc-control-sm px-3 fw-semibold">Kembalikan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formHapus = document.getElementById('formHapusPrestasi');
    if (formHapus) {
        formHapus.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Draf?',
                    text: 'Data prestasi ini akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(res) {
                    if (res.isConfirmed) {
                        formHapus.submit();
                    }
                });
            } else if (confirm('Data prestasi ini akan dihapus.')) {
                formHapus.submit();
            }
        });
    }

    const formValidasi = document.getElementById('formValidasiPrestasi');
    if (formValidasi) {
        formValidasi.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validasi Prestasi?',
                    text: 'Data ini akan disahkan sebagai prestasi tervalidasi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Validasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(res) {
                    if (res.isConfirmed) {
                        formValidasi.submit();
                    }
                });
            } else if (confirm('Data ini akan disahkan sebagai prestasi tervalidasi.')) {
                formValidasi.submit();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
