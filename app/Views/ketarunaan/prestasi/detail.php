<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Prestasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/prestasi/_styles') ?>
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
$canEditPrestasi = isset($canEditPrestasi) ? !empty($canEditPrestasi) : $canManageTaruna;
$canValidate     = !empty($canValidate);
$isSelfOnly       = !empty($isSelfOnly);
$isPesertaSendiri = !empty($isPesertaSendiri);
$canViewBukti     = !$isSelfOnly || $isPesertaSendiri;
$ownNomorTaruna   = (string) ($ownNomorTaruna ?? '');

$idPrestasi      = (int) ($prestasi['id'] ?? 0);
$rawStatus       = (string) ($prestasi['status_validasi'] ?? 'draf');
$canDeleteDraft  = $canEditPrestasi && in_array($rawStatus, ['draf', 'perlu_perbaikan'], true);
$canDecideNow    = $canValidate && in_array($rawStatus, ['diajukan', 'perlu_perbaikan'], true);

$jenisMap        = ketarunaan_jenis_prestasi_catalog();
$tingkatMap      = ketarunaan_tingkat_prestasi_catalog();
$jenisLbl        = $jenisMap[$prestasi['jenis_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['jenis_prestasi'] ?? '-'));
$tingkatLbl      = $tingkatMap[$prestasi['tingkat_prestasi'] ?? ''] ?? ucfirst((string) ($prestasi['tingkat_prestasi'] ?? '-'));

$tglMulai        = ketarunaan_format_tanggal((string) ($prestasi['tanggal_mulai'] ?? ''), true);
$tglSelesai      = ketarunaan_format_tanggal((string) ($prestasi['tanggal_selesai'] ?? ''), true);
$rentangTgl      = ($tglSelesai !== '-' && $tglSelesai !== $tglMulai) ? ($tglMulai . ' – ' . $tglSelesai) : $tglMulai;

$buktiBerkas     = trim((string) ($prestasi['bukti_berkas'] ?? ''));
$isBuktiUrl      = ($buktiBerkas !== '' && ketarunaan_is_http_url($buktiBerkas, 2000));
$buktiLabel      = ($buktiBerkas !== '') ? ketarunaan_format_bukti_label($buktiBerkas) : '';
$ukuranKb        = (int) ($prestasi['ukuran_berkas_kb'] ?? 0);

$canSeeRekamJejak = static function (string $nomorTaruna) use ($isSelfOnly, $ownNomorTaruna): bool {
    if (!$isSelfOnly) {
        return true;
    }
    return $ownNomorTaruna !== '' && strcasecmp($nomorTaruna, $ownNomorTaruna) === 0;
};
?>

<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/prestasi') ?>" class="text-decoration-none text-secondary">Prestasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Detail Prestasi</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Detail Prestasi</h1>
            <p class="text-secondary mb-0 small">Rincian prestasi taruna, peserta, dokumen bukti, dan riwayat validasi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('ketarunaan/prestasi') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Daftar
            </a>
            <a href="<?= base_url('ketarunaan/prestasi/unduh-ringkasan/' . $idPrestasi) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pr-touch-ctrl">
                <i class="bi bi-download" aria-hidden="true"></i> Unduh Ringkasan
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

    <!-- Kartu Ringkas Prestasi -->
    <div class="bento-card ecc-pr-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-icon-48">
                    <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                </div>
                <div>
                    <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($prestasi['nama_prestasi'] ?? '-')) ?></h2>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-secondary small">
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
                <?= ketarunaan_validasi_badge($rawStatus) ?>
                <?php if ($canEditPrestasi): ?>
                    <?php
                    $labelEditBtn = ($rawStatus === 'tervalidasi')
                        ? 'Ajukan Koreksi'
                        : ($isSelfOnly ? 'Edit Pengajuan' : 'Edit Prestasi');
                    ?>
                    <a href="<?= base_url('ketarunaan/prestasi/edit/' . $idPrestasi) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">
                        <?= esc($labelEditBtn) ?>
                    </a>
                    <?php if ($canDeleteDraft): ?>
                        <form method="post" action="<?= base_url('ketarunaan/prestasi/hapus/' . $idPrestasi) ?>" class="d-inline" id="formHapusPrestasi">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl">Hapus Draf</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($rawStatus === 'perlu_perbaikan' && !empty($prestasi['catatan_validasi'])): ?>
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 px-3 py-2 small" role="alert">
                <strong class="d-block text-dark">Catatan Perbaikan Validator</strong>
                <div class="text-dark mt-1"><?= esc((string) $prestasi['catatan_validasi']) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kontainer 4 Tab -->
    <div class="bento-card ecc-pr-card">
        <nav class="ecc-tab-nav" aria-label="Bagian Detail Prestasi">
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=ringkasan') ?>" class="ecc-tab-link <?= $activeTab === 'ringkasan' ? 'active' : '' ?>"<?= $activeTab === 'ringkasan' ? ' aria-current="page"' : '' ?>>Ringkasan</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=peserta') ?>" class="ecc-tab-link ecc-num <?= $activeTab === 'peserta' ? 'active' : '' ?>"<?= $activeTab === 'peserta' ? ' aria-current="page"' : '' ?>>Peserta (<?= count($peserta) ?>)</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=dokumen') ?>" class="ecc-tab-link <?= $activeTab === 'dokumen' ? 'active' : '' ?>"<?= $activeTab === 'dokumen' ? ' aria-current="page"' : '' ?>>Dokumen Bukti</a>
            <a href="<?= base_url('ketarunaan/prestasi/detail/' . $idPrestasi . '?tab=riwayat') ?>" class="ecc-tab-link <?= $activeTab === 'riwayat' ? 'active' : '' ?>"<?= $activeTab === 'riwayat' ? ' aria-current="page"' : '' ?>>Riwayat Validasi</a>
        </nav>

        <?php if ($activeTab === 'ringkasan'): ?>
            <div class="row g-4">
                <!-- Kolom Kiri Ringkasan -->
                <div class="col-12 col-lg-8 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Informasi Prestasi</h3>
                        <div class="row g-3 small">
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
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h3 class="h6 fw-bold text-dark mb-0 ecc-num">Peserta Prestasi (<?= count($peserta) ?> Taruna)</h3>
                            <?php if ($canEditPrestasi): ?>
                                <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Kelola Peserta</a>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive ecc-pr-scroll d-none d-md-block">
                            <table class="table table-hover ecc-table-pr mb-0" data-sortable-table="true">
                                <thead>
                                    <tr>
                                        <th style="width: 48px;">No</th>
                                        <th aria-sort="none">
                                            <button type="button" class="pr-sort-btn" data-sort-col="1" data-sort-type="text">
                                                <span>Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                        <th aria-sort="none">
                                            <button type="button" class="pr-sort-btn" data-sort-col="2" data-sort-type="text">
                                                <span>Prodi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                        <th aria-sort="none">
                                            <button type="button" class="pr-sort-btn" data-sort-col="3" data-sort-type="text">
                                                <span>Peran</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($peserta)): ?>
                                        <tr class="pr-empty-row">
                                            <td colspan="5" class="text-center text-secondary py-4">Belum ada peserta terdaftar pada prestasi ini.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($peserta as $idx => $ps): ?>
                                            <?php
                                            $angPeserta = !empty($ps['angkatan'])
                                                ? ketarunaan_format_angkatan([
                                                    'tahun_masuk' => $ps['angkatan'],
                                                    'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                                                ])
                                                : '-';
                                            ?>
                                            <tr>
                                                <td class="ecc-num text-secondary"><?= $idx + 1 ?></td>
                                                <td data-sort-val="<?= esc(strtolower((string) $ps['nama_lengkap'])) ?>">
                                                    <div class="fw-semibold text-dark"><?= esc((string) $ps['nama_lengkap']) ?></div>
                                                    <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                        <?= esc((string) $ps['nomor_taruna']) ?> &bull; Angkatan <?= esc($angPeserta) ?>
                                                    </div>
                                                </td>
                                                <td data-sort-val="<?= esc(strtolower((string) ($ps['kode_prodi'] ?? ''))) ?>"><?= esc((string) ($ps['kode_prodi'] ?? '-')) ?></td>
                                                <td data-sort-val="<?= esc(strtolower((string) ($ps['peran'] ?: 'Peserta'))) ?>"><?= esc((string) ($ps['peran'] ?: 'Peserta')) ?></td>
                                                <td class="text-end text-nowrap">
                                                    <?php if ($canSeeRekamJejak((string) $ps['nomor_taruna'])): ?>
                                                        <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile">Rekam Jejak</a>
                                                    <?php else: ?>
                                                        <span class="text-secondary small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Kartu Mobile Peserta Ringkasan -->
                        <div class="d-md-none d-flex flex-column gap-2">
                            <?php if (empty($peserta)): ?>
                                <div class="text-center text-secondary py-3 small">Belum ada peserta terdaftar pada prestasi ini.</div>
                            <?php else: ?>
                                <?php foreach ($peserta as $ps): ?>
                                    <?php
                                    $angPeserta = !empty($ps['angkatan'])
                                        ? ketarunaan_format_angkatan([
                                            'tahun_masuk' => $ps['angkatan'],
                                            'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                                        ])
                                        : '-';
                                    ?>
                                    <div class="border rounded-3 p-3 bg-white">
                                        <div class="fw-bold text-dark small"><?= esc((string) $ps['nama_lengkap']) ?></div>
                                        <div class="text-secondary ecc-num mb-2" style="font-size: 0.75rem;">
                                            <?= esc((string) $ps['nomor_taruna']) ?> &bull; <?= esc((string) ($ps['kode_prodi'] ?? '-')) ?> &bull; <?= esc($angPeserta) ?> &bull; <?= esc((string) ($ps['peran'] ?: 'Peserta')) ?>
                                        </div>
                                        <?php if ($canSeeRekamJejak((string) $ps['nomor_taruna'])): ?>
                                            <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 w-100 fw-semibold pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Rekam Jejak</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan Ringkasan -->
                <div class="col-12 col-lg-4 d-flex flex-column gap-4">
                    <div class="ecc-panel-box">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <h3 class="h6 fw-bold text-dark mb-0">Bukti Utama</h3>
                            <?php if ($canEditPrestasi): ?>
                                <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-light border btn-sm rounded-3 px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Kelola</a>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Sertifikat / SK</div>
                            <div class="fw-semibold text-dark ecc-num small"><?= esc((string) ($prestasi['nomor_sertifikat'] ?: '-')) ?></div>
                        </div>

                        <?php if ($buktiBerkas !== ''): ?>
                            <?php if ($canViewBukti): ?>
                                <div class="p-3 rounded-3 bg-light border mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi <?= $isBuktiUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-pdf-fill text-danger' ?>" aria-hidden="true"></i>
                                        <div class="fw-semibold text-dark text-truncate small"><?= esc($buktiLabel) ?></div>
                                    </div>
                                    <?php if (!$isBuktiUrl && $ukuranKb > 0): ?>
                                        <div class="text-secondary ecc-num mt-1" style="font-size: 0.75rem;"><?= number_format($ukuranKb, 0, ',', '.') ?> KB</div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <?php if ($isBuktiUrl): ?>
                                        <a href="<?= esc($buktiBerkas) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Pratinjau</a>
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold flex-fill btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Unduh</a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="p-3 rounded-3 bg-light border text-secondary small d-flex align-items-center gap-2">
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                    <span>Bukti terverifikasi (khusus peserta &amp; petugas).</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-secondary small">Belum ada tautan bukti utama.</div>
                        <?php endif; ?>
                    </div>

                    <div class="ecc-panel-box">
                        <h3 class="h6 fw-bold text-dark mb-3">Informasi Validasi</h3>
                        <div class="ecc-meta-row">
                            <span class="text-secondary">Status</span>
                            <span><?= ketarunaan_validasi_badge($rawStatus) ?></span>
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

                        <?php if ($canDecideNow): ?>
                            <div class="d-flex flex-column gap-2 pt-3 mt-3 border-top">
                                <form method="post" action="<?= base_url('ketarunaan/prestasi/validasi/' . $idPrestasi) ?>" id="formValidasiPrestasi">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="keputusan" value="tervalidasi">
                                    <button type="submit" class="btn btn-success ecc-control-sm w-100 fw-semibold btn-tactile pr-touch-ctrl">Validasi Prestasi</button>
                                </form>
                                <?php if ($rawStatus === 'diajukan'): ?>
                                    <button type="button" class="btn btn-outline-danger ecc-control-sm w-100 fw-semibold btn-tactile pr-touch-ctrl" data-bs-toggle="modal" data-bs-target="#modalKembalikanPrestasi">
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
                    <h3 class="h6 fw-bold text-dark mb-1 ecc-num">Daftar Peserta Prestasi (<?= count($peserta) ?> Taruna)</h3>
                    <p class="text-secondary mb-0 small">Setiap taruna terhubung langsung dengan Rekam Jejak masing-masing.</p>
                </div>
                <?php if ($canEditPrestasi): ?>
                    <a href="<?= base_url('ketarunaan/prestasi/peserta/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Kelola Peserta</a>
                <?php endif; ?>
            </div>

            <div class="table-responsive ecc-pr-scroll d-none d-md-block">
                <table class="table table-hover ecc-table-pr mb-0" data-sortable-table="true">
                    <thead>
                        <tr>
                            <th style="width: 48px;">No</th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="1" data-sort-type="text">
                                    <span>Nama Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="2" data-sort-type="text">
                                    <span>Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="3" data-sort-type="text">
                                    <span>Prodi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="4" data-sort-type="text">
                                    <span>Angkatan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="5" data-sort-type="text">
                                    <span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="6" data-sort-type="text">
                                    <span>Peran</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peserta)): ?>
                            <tr class="pr-empty-row">
                                <td colspan="8" class="text-center text-secondary py-4">Belum ada peserta terdaftar pada prestasi ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($peserta as $idx => $ps): ?>
                                <?php
                                $angPeserta = !empty($ps['angkatan'])
                                    ? ketarunaan_format_angkatan([
                                        'tahun_masuk' => $ps['angkatan'],
                                        'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                                    ])
                                    : '-';
                                ?>
                                <tr>
                                    <td class="ecc-num text-secondary"><?= $idx + 1 ?></td>
                                    <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $ps['nama_lengkap'])) ?>"><?= esc((string) $ps['nama_lengkap']) ?></td>
                                    <td class="ecc-num" data-sort-val="<?= esc((string) $ps['nomor_taruna']) ?>"><?= esc((string) $ps['nomor_taruna']) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) ($ps['kode_prodi'] ?? ''))) ?>"><?= esc((string) ($ps['kode_prodi'] ?? '-')) ?></td>
                                    <td class="ecc-num" data-sort-val="<?= esc((string) ($ps['angkatan'] ?? '')) ?>"><?= esc($angPeserta) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) ($ps['status_terkini'] ?? 'aktif'))) ?>"><?= ketarunaan_status_badge((string) ($ps['status_terkini'] ?? 'aktif')) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) ($ps['peran'] ?: 'Peserta'))) ?>"><?= esc((string) ($ps['peran'] ?: 'Peserta')) ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php if ($canSeeRekamJejak((string) $ps['nomor_taruna'])): ?>
                                            <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile">Rekam Jejak</a>
                                        <?php else: ?>
                                            <span class="text-secondary small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Kartu Mobile Tab Peserta -->
            <div class="d-md-none d-flex flex-column gap-2">
                <?php if (empty($peserta)): ?>
                    <div class="text-center text-secondary py-4 small">Belum ada peserta terdaftar pada prestasi ini.</div>
                <?php else: ?>
                    <?php foreach ($peserta as $ps): ?>
                        <?php
                        $angPeserta = !empty($ps['angkatan'])
                            ? ketarunaan_format_angkatan([
                                'tahun_masuk' => $ps['angkatan'],
                                'kode_romawi' => $ps['angkatan_romawi'] ?? '',
                            ])
                            : '-';
                        ?>
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div>
                                    <div class="fw-bold text-dark small"><?= esc((string) $ps['nama_lengkap']) ?></div>
                                    <div class="text-secondary ecc-num" style="font-size: 0.75rem;"><?= esc((string) $ps['nomor_taruna']) ?> &bull; <?= esc((string) ($ps['kode_prodi'] ?? '-')) ?> &bull; <?= esc($angPeserta) ?></div>
                                </div>
                                <?= ketarunaan_status_badge((string) ($ps['status_terkini'] ?? 'aktif')) ?>
                            </div>
                            <div class="small text-secondary mb-2">Peran: <strong class="text-dark"><?= esc((string) ($ps['peran'] ?: 'Peserta')) ?></strong></div>
                            <?php if ($canSeeRekamJejak((string) $ps['nomor_taruna'])): ?>
                                <a href="<?= base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode((string) $ps['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 w-100 fw-semibold pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Rekam Jejak</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        <?php elseif ($activeTab === 'dokumen'): ?>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <div>
                    <h3 class="h6 fw-bold text-dark mb-1">Dokumen Bukti &amp; Dokumentasi</h3>
                    <p class="text-secondary mb-0 small">Tautan bukti utama piagam dan dokumentasi pendukung kegiatan.</p>
                </div>
                <?php if ($canEditPrestasi): ?>
                    <a href="<?= base_url('ketarunaan/prestasi/dokumen/' . $idPrestasi) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center justify-content-center">Kelola Dokumen</a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="ecc-panel-box h-100">
                        <h4 class="h6 fw-bold text-dark mb-3">Bukti Utama Prestasi</h4>
                        <div class="mb-3">
                            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Sertifikat / Piagam / SK</div>
                            <div class="fw-semibold text-dark ecc-num small"><?= esc((string) ($prestasi['nomor_sertifikat'] ?: '-')) ?></div>
                        </div>

                        <?php if ($buktiBerkas !== ''): ?>
                            <?php if ($canViewBukti): ?>
                                <div class="p-3 rounded-3 bg-light border mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi <?= $isBuktiUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-pdf-fill text-danger' ?>" aria-hidden="true"></i>
                                        <div class="fw-semibold text-dark text-truncate small"><?= esc($buktiLabel) ?></div>
                                    </div>
                                    <?php if ($isBuktiUrl): ?>
                                        <div class="text-secondary text-truncate mt-1" style="font-size: 0.75rem;" title="<?= esc($buktiBerkas) ?>"><?= esc($buktiBerkas) ?></div>
                                    <?php elseif ($ukuranKb > 0): ?>
                                        <div class="text-secondary ecc-num mt-1" style="font-size: 0.75rem;">Ukuran: <?= number_format($ukuranKb, 0, ',', '.') ?> KB</div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if ($isBuktiUrl): ?>
                                        <a href="<?= esc($buktiBerkas) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan Bukti
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?inline=1') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Pratinjau PDF</a>
                                        <a href="<?= base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Unduh</a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="p-3 rounded-3 bg-light border text-secondary small d-flex align-items-center gap-2">
                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                    <span>Bukti terverifikasi (khusus peserta &amp; petugas).</span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-secondary mb-0 small">Belum ada tautan bukti utama.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="ecc-panel-box h-100">
                        <h4 class="h6 fw-bold text-dark mb-3 ecc-num">Dokumentasi Tambahan (<?= count($dokumentasi) ?> Tautan/Berkas)</h4>
                        <?php if (empty($dokumentasi)): ?>
                            <p class="text-secondary mb-0 small">Tidak ada dokumentasi tambahan.</p>
                        <?php elseif (!$canViewBukti): ?>
                            <div class="p-3 rounded-3 bg-light border text-secondary small d-flex align-items-center gap-2">
                                <i class="bi bi-lock" aria-hidden="true"></i>
                                <span>Dokumentasi kegiatan hanya dapat dibuka oleh peserta terkait dan petugas.</span>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($dokumentasi as $item): ?>
                                    <?php
                                    $rawItem    = trim((string) $item);
                                    $isItemUrl  = ketarunaan_is_http_url($rawItem, 2000);
                                    $itemLabel  = ketarunaan_format_bukti_label($rawItem);
                                    if ($isItemUrl) {
                                        $previewUrl  = $rawItem;
                                        $downloadUrl = $rawItem;
                                        $hasDiskFoto = false;
                                    } else {
                                        $fotoSafe     = basename($rawItem);
                                        $physicalFoto = FCPATH . 'assets/uploads/ketarunaan/prestasi/dokumentasi/' . $fotoSafe;
                                        $hasDiskFoto  = is_file($physicalFoto);
                                        $previewUrl   = base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode($fotoSafe) . '&inline=1');
                                        $downloadUrl  = base_url('ketarunaan/prestasi/unduh-dokumen/' . $idPrestasi . '?jenis=foto&file=' . rawurlencode($fotoSafe));
                                    }
                                    ?>
                                    <div class="ecc-file-item">
                                        <div class="d-flex align-items-center gap-2 text-truncate min-w-0">
                                            <?php if ($hasDiskFoto): ?>
                                                <img src="<?= esc($previewUrl) ?>" alt="Dokumentasi <?= esc($itemLabel) ?>" class="ecc-doc-thumb" loading="lazy">
                                            <?php else: ?>
                                                <i class="bi <?= $isItemUrl ? 'bi-link-45deg text-primary' : 'bi-file-earmark-image text-secondary' ?> fs-5 flex-shrink-0" aria-hidden="true"></i>
                                            <?php endif; ?>
                                            <div class="text-truncate min-w-0">
                                                <div class="fw-medium text-dark text-truncate small"><?= esc($itemLabel) ?></div>
                                                <?php if ($isItemUrl): ?>
                                                    <div class="text-secondary text-truncate" style="font-size: 0.75rem;" title="<?= esc($rawItem) ?>"><?= esc($rawItem) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <?php if ($isItemUrl): ?>
                                                <a href="<?= esc($previewUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= esc($previewUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Pratinjau</a>
                                                <a href="<?= esc($downloadUrl) ?>" class="btn btn-light border btn-sm rounded-3 px-3 btn-tactile pr-touch-ctrl d-inline-flex align-items-center">Unduh</a>
                                            <?php endif; ?>
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
                <h3 class="h6 fw-bold text-dark mb-1">Riwayat Validasi &amp; Perubahan Status</h3>
                <p class="text-secondary mb-0 small">Catatan tahapan pengajuan dan hasil pemeriksaan validator.</p>
            </div>

            <div class="table-responsive ecc-pr-scroll d-none d-md-block mb-4">
                <table class="table table-hover ecc-table-pr mb-0" data-sortable-table="true">
                    <thead>
                        <tr>
                            <th style="width: 56px;">No</th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="1" data-sort-type="text">
                                    <span>Aksi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="2" data-sort-type="text">
                                    <span>Pelaku</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th aria-sort="none">
                                <button type="button" class="pr-sort-btn" data-sort-col="3" data-sort-type="text">
                                    <span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                </button>
                            </th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr class="pr-empty-row">
                                <td colspan="5" class="text-center text-secondary py-4">Belum ada riwayat validasi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $rIdx => $row): ?>
                                <?php $rowSt = (string) ($row['status'] ?? 'draf'); ?>
                                <tr>
                                    <td class="ecc-num text-secondary"><?= $rIdx + 1 ?></td>
                                    <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) ($row['aksi'] ?? ''))) ?>"><?= esc((string) ($row['aksi'] ?? '-')) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) ($row['pelaku'] ?? ''))) ?>"><?= esc((string) ($row['pelaku'] ?? '-')) ?></td>
                                    <td data-sort-val="<?= esc(strtolower($rowSt)) ?>"><?= ketarunaan_validasi_badge($rowSt) ?></td>
                                    <td><?= esc((string) ($row['catatan'] ?? '-')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Kartu Mobile Riwayat Validasi -->
            <div class="d-md-none d-flex flex-column gap-2 mb-4">
                <?php if (empty($riwayat)): ?>
                    <div class="text-center text-secondary py-4 small">Belum ada riwayat validasi.</div>
                <?php else: ?>
                    <?php foreach ($riwayat as $row): ?>
                        <?php $rowSt = (string) ($row['status'] ?? 'draf'); ?>
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div class="fw-bold text-dark small"><?= esc((string) ($row['aksi'] ?? '-')) ?></div>
                                <?= ketarunaan_validasi_badge($rowSt) ?>
                            </div>
                            <div class="text-secondary small mb-1">Pelaku: <strong class="text-dark"><?= esc((string) ($row['pelaku'] ?? '-')) ?></strong></div>
                            <div class="text-secondary small"><?= esc((string) ($row['catatan'] ?? '-')) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="px-3 py-2 rounded-3 bg-light border text-secondary small">
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
                <form method="post" action="<?= base_url('ketarunaan/prestasi/validasi/' . $idPrestasi) ?>" id="formKembalikanPrestasi">
                    <?= csrf_field() ?>
                    <input type="hidden" name="keputusan" value="perlu_perbaikan">
                    <div class="modal-header border-bottom px-4 py-3">
                        <h2 class="h6 modal-title fw-bold mb-0" id="modalKembalikanLabel">Kembalikan Prestasi</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label for="catatanPerbaikanInput" class="form-label fw-semibold text-dark small">
                            Catatan Perbaikan <span class="text-danger">*</span>
                        </label>
                        <textarea
                            id="catatanPerbaikanInput"
                            name="catatan_validasi"
                            class="form-control rounded-3"
                            rows="3"
                            placeholder="Tuliskan bagian yang perlu dilengkapi atau diperbaiki..."
                            required
                        ></textarea>
                    </div>
                    <div class="modal-footer border-top px-4 py-3">
                        <button type="button" class="btn btn-light border ecc-control-sm px-3 pr-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger ecc-control-sm px-3 fw-semibold btn-tactile pr-touch-ctrl">Kembalikan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
        var buttons = table.querySelectorAll('thead .pr-sort-btn');
        var tbody = table.querySelector('tbody');
        if (!tbody || buttons.length === 0) return;

        var activeCol = null;
        var activeDir = 'asc';

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var th = btn.closest('th');
                var colIdx = parseInt(btn.getAttribute('data-sort-col'), 10);
                var sortType = btn.getAttribute('data-sort-type') || 'text';
                if (isNaN(colIdx)) return;

                if (activeCol === colIdx) {
                    activeDir = (activeDir === 'asc') ? 'desc' : 'asc';
                } else {
                    activeCol = colIdx;
                    activeDir = 'asc';
                }

                table.querySelectorAll('thead th[aria-sort]').forEach(function (h) {
                    h.classList.remove('th-sorted');
                    h.setAttribute('aria-sort', 'none');
                    var icon = h.querySelector('.sort-indicator');
                    if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                });

                if (th) {
                    th.classList.add('th-sorted');
                    th.setAttribute('aria-sort', activeDir === 'asc' ? 'ascending' : 'descending');
                }
                var activeIcon = btn.querySelector('.sort-indicator');
                if (activeIcon) {
                    activeIcon.className = activeDir === 'asc'
                        ? 'bi bi-arrow-up text-primary sort-indicator'
                        : 'bi bi-arrow-down text-primary sort-indicator';
                }

                var rows = Array.from(tbody.querySelectorAll('tr:not(.pr-empty-row)'));
                rows.sort(function (a, b) {
                    var cellA = a.children[colIdx];
                    var cellB = b.children[colIdx];
                    var rawA = cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent.trim().toLowerCase()) : '';
                    var rawB = cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent.trim().toLowerCase()) : '';
                    if (sortType === 'number') {
                        return activeDir === 'asc' ? ((parseFloat(rawA) || 0) - (parseFloat(rawB) || 0)) : ((parseFloat(rawB) || 0) - (parseFloat(rawA) || 0));
                    }
                    var cmp = rawA.localeCompare(rawB, 'id', { numeric: true });
                    return activeDir === 'asc' ? cmp : -cmp;
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
            });
        });
    });

    var formHapus = document.getElementById('formHapusPrestasi');
    if (formHapus) {
        formHapus.addEventListener('submit', function (e) {
            if (formHapus.dataset.confirmed === '1') return;
            e.preventDefault();
            var submitNow = function () {
                formHapus.dataset.confirmed = '1';
                var btn = formHapus.querySelector('button[type="submit"]');
                if (btn) btn.disabled = true;
                formHapus.submit();
            };
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
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (res) {
                    if (res.isConfirmed) submitNow();
                });
            } else if (confirm('Data prestasi ini akan dihapus.')) {
                submitNow();
            }
        });
    }

    var formValidasi = document.getElementById('formValidasiPrestasi');
    if (formValidasi) {
        formValidasi.addEventListener('submit', function (e) {
            if (formValidasi.dataset.confirmed === '1') return;
            e.preventDefault();
            var submitNow = function () {
                formValidasi.dataset.confirmed = '1';
                var btn = formValidasi.querySelector('button[type="submit"]');
                if (btn) btn.disabled = true;
                formValidasi.submit();
            };
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validasi Prestasi?',
                    text: 'Data ini akan disahkan sebagai prestasi tervalidasi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#15803d',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Validasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function (res) {
                    if (res.isConfirmed) submitNow();
                });
            } else if (confirm('Data ini akan disahkan sebagai prestasi tervalidasi.')) {
                submitNow();
            }
        });
    }

    var formKembalikan = document.getElementById('formKembalikanPrestasi');
    if (formKembalikan) {
        formKembalikan.addEventListener('submit', function (e) {
            var ta = document.getElementById('catatanPerbaikanInput');
            if (ta && ta.value.trim() === '') {
                e.preventDefault();
                ta.focus();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Catatan Wajib Diisi',
                        text: 'Tuliskan bagian yang perlu diperbaiki sebelum mengembalikan data.',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#1e40af',
                        customClass: { popup: 'rounded-4 shadow border-0' }
                    });
                } else {
                    alert('Tuliskan catatan perbaikan terlebih dahulu.');
                }
                return;
            }
            var btn = formKembalikan.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    }

    window.addEventListener('pageshow', function () {
        document.querySelectorAll('#formHapusPrestasi button, #formValidasiPrestasi button, #formKembalikanPrestasi button[type="submit"]').forEach(function (btn) {
            btn.disabled = false;
        });
    });
});
</script>
<?= $this->endSection() ?>
