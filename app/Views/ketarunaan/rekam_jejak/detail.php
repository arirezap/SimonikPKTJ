<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Rekam Jejak <?= esc($detail['taruna']['nama_lengkap'] ?? 'Taruna') ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/rekam_jejak/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail            = $detail ?? [];
$taruna            = $detail['taruna'] ?? [];
$capaianCounts     = $detail['capaian_counts'] ?? ['total' => 0, 'tervalidasi' => 0, 'diajukan' => 0, 'perlu_perbaikan' => 0];
$prestasiCount     = (int) ($detail['prestasi_count'] ?? 0);
$beasiswaCount     = (int) ($detail['beasiswa_count'] ?? 0);
$ringkasanJalan    = $detail['ringkasan_perjalanan'] ?? [];
$capaianTerbaru    = $detail['capaian_terbaru'] ?? [];
$kronologiItems    = $detail['kronologi_items'] ?? [];
$capaianItems      = $detail['capaian_items'] ?? [];
$dokumenItems      = $detail['dokumen_items'] ?? [];
$activeTab         = $active_tab ?? 'ringkasan';
$filters           = $filters ?? [];
$refs              = $refs ?? [];

$nomorTaruna       = (string) ($taruna['nomor_taruna'] ?? '');
$namaLengkap       = (string) ($taruna['nama_lengkap'] ?? '-');
$inisial           = strtoupper(mb_substr(trim($namaLengkap), 0, 1));
$detailBaseUrl     = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna));
$fotoFile          = basename((string) ($taruna['foto'] ?? ''));
$fotoUrl           = ($fotoFile !== '' && is_file(FCPATH . 'assets/uploads/taruna/' . $fotoFile))
    ? base_url('assets/uploads/taruna/' . $fotoFile)
    : null;
$angkatanTxt       = !empty($taruna['angkatan'])
    ? ketarunaan_format_angkatan([
        'tahun_masuk' => $taruna['angkatan'],
        'kode_romawi' => $taruna['angkatan_romawi'] ?? '',
    ])
    : '-';
?>

<!-- Header Halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none text-secondary">Ketarunaan</a></li>
                <?php if (empty($isSelfOnly)): ?>
                    <li class="breadcrumb-item"><a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="text-decoration-none text-secondary">Rekam Jejak Taruna</a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($namaLengkap) ?></li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-dark mb-1">Rekam Jejak <?= esc($namaLengkap) ?></h1>
        <p class="text-secondary mb-0 small">Riwayat perjalanan pendidikan, capaian, dan bukti pendukung taruna.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
            Profil Taruna
        </a>
        <?php if (empty($isSelfOnly)): ?>
            <a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile rj-touch-ctrl">
                <i class="bi bi-chevron-left" aria-hidden="true"></i> Kembali ke Daftar
            </a>
        <?php endif; ?>
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

<!-- Kartu Identitas Header Taruna -->
<div class="bento-card ecc-rj-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-4 d-flex align-items-center gap-3">
            <div class="ecc-avatar-lg">
                <?php if ($fotoUrl !== null): ?>
                    <img src="<?= esc($fotoUrl) ?>" alt="Foto <?= esc($namaLengkap) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <?= esc($inisial) ?>
                <?php endif; ?>
            </div>
            <div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc($namaLengkap) ?></h2>
                <div class="text-secondary small">
                    Tingkat <?= (int) ($taruna['tingkat'] ?? 1) ?> &bull; Semester <?= (int) ($taruna['semester_aktif'] ?? 1) ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
            <div class="fw-bold text-dark ecc-num small"><?= esc($nomorTaruna) ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
            <div class="fw-bold text-dark small"><?= esc($taruna['kode_prodi'] ?? '-') ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
            <div class="fw-bold text-dark ecc-num small"><?= esc($angkatanTxt) ?></div>
        </div>
        <div class="col-6 col-md-2 border-md-start">
            <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
            <div><?= ketarunaan_status_badge((string) ($taruna['status_terkini'] ?? 'aktif')) ?></div>
        </div>
    </div>
</div>

<!-- Kontainer 4 Tab Rekam Jejak -->
<div class="bento-card ecc-rj-card">
    <nav class="ecc-tab-nav" aria-label="Bagian Rekam Jejak">
        <a href="<?= esc($detailBaseUrl . '?tab=ringkasan') ?>" class="ecc-tab-link <?= ($activeTab === 'ringkasan') ? 'active' : '' ?>"<?= ($activeTab === 'ringkasan') ? ' aria-current="page"' : '' ?>>Ringkasan</a>
        <a href="<?= esc($detailBaseUrl . '?tab=kronologi') ?>" class="ecc-tab-link <?= ($activeTab === 'kronologi') ? 'active' : '' ?>"<?= ($activeTab === 'kronologi') ? ' aria-current="page"' : '' ?>>Kronologi</a>
        <a href="<?= esc($detailBaseUrl . '?tab=capaian') ?>" class="ecc-tab-link <?= ($activeTab === 'capaian') ? 'active' : '' ?>"<?= ($activeTab === 'capaian') ? ' aria-current="page"' : '' ?>>Daftar Capaian</a>
        <a href="<?= esc($detailBaseUrl . '?tab=dokumen') ?>" class="ecc-tab-link <?= ($activeTab === 'dokumen') ? 'active' : '' ?>"<?= ($activeTab === 'dokumen') ? ' aria-current="page"' : '' ?>>Dokumen Bukti</a>
    </nav>

    <?php if ($activeTab === 'ringkasan'): ?>
        <!-- ==================== TAB 1: RINGKASAN ==================== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= esc($detailBaseUrl . '?tab=capaian') ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #fef3c7; color: #d97706;">
                            <i class="bi bi-award-fill" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Capaian Tervalidasi</div>
                            <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= (int) $capaianCounts['tervalidasi'] ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= esc($detailBaseUrl . '?tab=kronologi&kategori_kronologi=prestasi') ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #f5f3ff; color: #7c3aed;">
                            <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Prestasi</div>
                            <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= $prestasiCount ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= esc($detailBaseUrl . '?tab=kronologi&kategori_kronologi=beasiswa') ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #fff7ed; color: #b45309;">
                            <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Beasiswa</div>
                            <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= $beasiswaCount ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= base_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna) . '?tab=riwayat-status') ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #eff6ff; color: #1e40af;">
                            <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Status Pendidikan</div>
                            <div class="fw-bold text-dark" style="font-size: 1.15rem;">
                                <?= esc(ketarunaan_status_meta((string) ($taruna['status_terkini'] ?? 'aktif'))['label']) ?>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Kolom Kiri: Ringkasan Perjalanan -->
            <div class="col-12 col-lg-7">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 fw-bold text-dark mb-3">Ringkasan Perjalanan</h3>
                    <div class="table-responsive ecc-rj-scroll d-none d-md-block">
                        <table class="table table-hover ecc-table-rj mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none">
                                        <button type="button" class="rj-sort-btn" data-sort-col="0" data-sort-type="text">
                                            <span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="rj-sort-btn" data-sort-col="1" data-sort-type="text">
                                            <span>Kategori</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th aria-sort="none">
                                        <button type="button" class="rj-sort-btn" data-sort-col="2" data-sort-type="text">
                                            <span>Keterangan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                    <th class="text-end" aria-sort="none">
                                        <button type="button" class="rj-sort-btn justify-content-end" data-sort-col="3" data-sort-type="text">
                                            <span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ringkasanJalan)): ?>
                                    <tr class="rj-empty-row">
                                        <td colspan="4" class="text-center text-secondary py-4 small">Belum ada riwayat perjalanan yang tervalidasi.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($ringkasanJalan as $rj): ?>
                                        <tr>
                                            <td class="ecc-num text-nowrap small" data-sort-val="<?= esc((string) ($rj['mulai_berlaku'] ?? '')) ?>">
                                                <div class="fw-semibold text-dark"><?= esc(ketarunaan_format_tanggal($rj['mulai_berlaku'], true)) ?></div>
                                                <?php if (!empty($rj['akhir_berlaku'])): ?>
                                                    <div class="text-secondary">s.d. <?= esc(ketarunaan_format_tanggal($rj['akhir_berlaku'], true)) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td data-sort-val="<?= esc(strtolower((string) $rj['kategori'])) ?>"><?= esc($rj['kategori']) ?></td>
                                            <td data-sort-val="<?= esc(strtolower((string) $rj['judul'])) ?>">
                                                <div class="fw-semibold text-dark"><?= esc($rj['judul']) ?></div>
                                                <div class="text-secondary small"><?= esc($rj['uraian']) ?></div>
                                            </td>
                                            <td class="text-end" data-sort-val="<?= esc(strtolower((string) $rj['kode_status'])) ?>">
                                                <?= ketarunaan_status_badge((string) $rj['kode_status']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Seluler Ringkasan Perjalanan -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php if (empty($ringkasanJalan)): ?>
                            <div class="text-center text-secondary py-3 small">Belum ada riwayat perjalanan yang tervalidasi.</div>
                        <?php else: ?>
                            <?php foreach ($ringkasanJalan as $rj): ?>
                                <div class="border rounded-3 p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-bold text-dark small"><?= esc($rj['judul']) ?></div>
                                        <?= ketarunaan_status_badge((string) $rj['kode_status']) ?>
                                    </div>
                                    <div class="text-secondary ecc-num mb-1" style="font-size: 0.75rem;">
                                        <?= esc($rj['kategori']) ?> &bull; <?= esc(ketarunaan_format_tanggal($rj['mulai_berlaku'], true)) ?>
                                        <?php if (!empty($rj['akhir_berlaku'])): ?>
                                            s.d. <?= esc(ketarunaan_format_tanggal($rj['akhir_berlaku'], true)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-secondary small"><?= esc($rj['uraian']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Capaian Terbaru -->
            <div class="col-12 col-lg-5">
                <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h3 class="h6 fw-bold text-dark mb-3">Capaian Terbaru</h3>
                        <?php if (empty($capaianTerbaru)): ?>
                            <div class="text-secondary py-3 small">Belum ada capaian tervalidasi yang tercatat.</div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($capaianTerbaru as $ct): ?>
                                    <a href="<?= base_url('ketarunaan/rekam-jejak/capaian/' . (int) $ct['id']) ?>" class="border rounded-3 p-3 text-decoration-none d-block bg-light-subtle rj-mobile-card">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                            <span class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                <?= esc(ketarunaan_format_tanggal($ct['tanggal_kegiatan'], true)) ?> &bull; <?= esc(ketarunaan_capaian_kategori_meta($ct['kategori'])['label']) ?>
                                            </span>
                                            <?= ketarunaan_validasi_badge((string) $ct['status_validasi']) ?>
                                        </div>
                                        <div class="fw-bold text-dark mb-1"><?= esc($ct['nama_capaian']) ?></div>
                                        <div class="text-secondary small"><?= esc($ct['uraian_capaian']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <a href="<?= esc($detailBaseUrl . '?tab=kronologi') ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            Buka Kronologi
                        </a>
                        <a href="<?= esc($detailBaseUrl . '?tab=capaian') ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            Lihat Semua Capaian
                        </a>
                        <?php if (!empty($canSubmitCapaian) || !empty($canManageTaruna)): ?>
                            <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold ms-md-auto btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> <?= !empty($isSelfOnly) ? 'Ajukan Capaian' : 'Tambah Capaian' ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
            <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
            <span>Riwayat bersumber dari data tervalidasi di seluruh sistem ECC. Akses terhadap data mengikuti kewenangan pengguna.</span>
        </div>

    <?php elseif ($activeTab === 'kronologi'): ?>
        <!-- ==================== TAB 2: KRONOLOGI ==================== -->
        <?php
        $hasKronFilter = (($filters['kategori_kronologi'] ?? 'all') !== 'all'
            || ($filters['periode_kronologi'] ?? 'all') !== 'all'
            || ($filters['validasi_kronologi'] ?? 'all') !== 'all');
        ?>
        <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 align-items-end mb-4 rj-filter-form">
            <input type="hidden" name="tab" value="kronologi">
            <div class="col-12 <?= $hasKronFilter ? 'col-md-4' : 'col-md-4' ?>">
                <label for="kronKategori" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Kategori</label>
                <select id="kronKategori" name="kategori_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    <option value="pendidikan" <?= (($filters['kategori_kronologi'] ?? '') === 'pendidikan') ? 'selected' : '' ?>>Pendidikan</option>
                    <option value="status_pendidikan" <?= (($filters['kategori_kronologi'] ?? '') === 'status_pendidikan') ? 'selected' : '' ?>>Status Pendidikan</option>
                    <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                        <option value="<?= esc($kKode) ?>" <?= (($filters['kategori_kronologi'] ?? '') === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                    <?php endforeach; ?>
                    <option value="prestasi" <?= (($filters['kategori_kronologi'] ?? '') === 'prestasi') ? 'selected' : '' ?>>Prestasi</option>
                    <option value="beasiswa" <?= (($filters['kategori_kronologi'] ?? '') === 'beasiswa') ? 'selected' : '' ?>>Beasiswa</option>
                    <?php if (!empty($canViewDiscipline)): ?>
                        <option value="pelanggaran" <?= (($filters['kategori_kronologi'] ?? '') === 'pelanggaran') ? 'selected' : '' ?>>Pembinaan / Disiplin</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 <?= $hasKronFilter ? 'col-md-3' : 'col-md-4' ?>">
                <label for="kronPeriode" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select id="kronPeriode" name="periode_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= ((string) ($filters['periode_kronologi'] ?? '') === (string) $ta['id']) ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 <?= $hasKronFilter ? 'col-md-3' : 'col-md-4' ?>">
                <label for="kronValidasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                <select id="kronValidasi" name="validasi_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status Validasi</option>
                    <option value="tervalidasi" <?= (($filters['validasi_kronologi'] ?? '') === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                    <option value="diajukan" <?= (($filters['validasi_kronologi'] ?? '') === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                    <option value="perlu_perbaikan" <?= (($filters['validasi_kronologi'] ?? '') === 'perlu_perbaikan') ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    <option value="draf" <?= (($filters['validasi_kronologi'] ?? '') === 'draf') ? 'selected' : '' ?>>Draf</option>
                </select>
            </div>
            <?php if ($hasKronFilter): ?>
                <div class="col-12 col-md-2 d-grid">
                    <a href="<?= esc($detailBaseUrl . '?tab=kronologi') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary d-inline-flex align-items-center justify-content-center btn-tactile rj-touch-ctrl text-nowrap">Reset</a>
                </div>
            <?php endif; ?>
        </form>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <?php if (empty($kronologiItems)): ?>
                    <div class="border rounded-3 p-4 text-center text-secondary small">Tidak ada catatan kronologi yang sesuai filter.</div>
                <?php else: ?>
                    <div class="ps-2">
                        <?php foreach ($kronologiItems as $kr): ?>
                            <div class="ecc-timeline-item">
                                <span class="ecc-timeline-dot"></span>
                                <div class="border rounded-3 p-3 bg-white">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark ecc-num small">
                                                <?= esc(ketarunaan_format_tanggal($kr['tanggal'], true)) ?>
                                                <?php if (!empty($kr['tanggal_selesai'])): ?>
                                                    &ndash; <?= esc(ketarunaan_format_tanggal($kr['tanggal_selesai'], true)) ?>
                                                <?php endif; ?>
                                            </span>
                                            <?= ketarunaan_modul_sumber_badge((string) $kr['modul']) ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($kr['kode_status_didik'])): ?>
                                                <?= ketarunaan_status_badge((string) $kr['kode_status_didik']) ?>
                                            <?php else: ?>
                                                <?= ketarunaan_validasi_badge((string) $kr['status_validasi']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($kr['judul']) ?></div>
                                            <div class="text-secondary small"><?= esc($kr['uraian']) ?></div>
                                        </div>
                                        <a href="<?= esc($kr['detail_url']) ?>" class="btn btn-light btn-sm border rounded-3 px-3 fw-semibold text-nowrap btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center" style="min-height: 32px; color: #1e40af;">
                                            <?= esc($kr['action_label']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-4">
                <div class="border rounded-3 p-3 mb-3">
                    <h3 class="h6 fw-bold text-dark mb-3">Ringkasan Rekam Jejak</h3>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary small">Capaian</span>
                        <span class="fw-bold text-dark ecc-num"><?= (int) $capaianCounts['total'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary small">Prestasi</span>
                        <span class="fw-bold text-dark ecc-num"><?= $prestasiCount ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary small">Program Beasiswa</span>
                        <span class="fw-bold text-dark ecc-num"><?= $beasiswaCount ?></span>
                    </div>
                </div>

                <div class="border rounded-3 p-3" style="background-color: #fffbeb; border-color: #fde68a !important;">
                    <div class="d-flex align-items-center gap-2 fw-bold mb-1 small" style="color: #b45309;">
                        <i class="bi bi-lock-fill" aria-hidden="true"></i> Akses Terbatas
                    </div>
                    <p class="mb-0 text-secondary small">
                        Catatan pembinaan dan pelanggaran hanya ditampilkan bagi peran yang berwenang.
                    </p>
                </div>
            </div>
        </div>

    <?php elseif ($activeTab === 'capaian'): ?>
        <!-- ==================== TAB 3: DAFTAR CAPAIAN ==================== -->
        <?php
        $curCapVal    = (string) ($filters['validasi_capaian'] ?? 'all');
        $hasCapFilter = (($filters['kategori_capaian'] ?? 'all') !== 'all'
            || ($filters['periode_capaian'] ?? 'all') !== 'all'
            || $curCapVal !== 'all');
        $capBaseQuery = [
            'tab'              => 'capaian',
            'kategori_capaian' => $filters['kategori_capaian'] ?? 'all',
            'periode_capaian'  => $filters['periode_capaian'] ?? 'all',
        ];
        ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
            <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 flex-grow-1 rj-filter-form">
                <input type="hidden" name="tab" value="capaian">
                <div class="col-12 col-md-4">
                    <label for="capKategori" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Kategori</label>
                    <select id="capKategori" name="kategori_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Kategori</option>
                        <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                            <option value="<?= esc($kKode) ?>" <?= (($filters['kategori_capaian'] ?? '') === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="capPeriode" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                    <select id="capPeriode" name="periode_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Periode</option>
                        <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                            <option value="<?= (int) $ta['id'] ?>" <?= ((string) ($filters['periode_capaian'] ?? '') === (string) $ta['id']) ? 'selected' : '' ?>>
                                <?= esc($ta['kode_tahun']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label for="capValidasi" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                    <select id="capValidasi" name="validasi_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Status Validasi</option>
                        <option value="draf" <?= ($curCapVal === 'draf') ? 'selected' : '' ?>>Draf</option>
                        <option value="tervalidasi" <?= ($curCapVal === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                        <option value="diajukan" <?= ($curCapVal === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                        <option value="perlu_perbaikan" <?= ($curCapVal === 'perlu_perbaikan') ? 'selected' : '' ?>>Perlu Perbaikan</option>
                    </select>
                </div>
            </form>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <?php if ($hasCapFilter): ?>
                    <a href="<?= esc($detailBaseUrl . '?tab=capaian') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary text-nowrap btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                        Reset
                    </a>
                <?php endif; ?>
                <?php if (!empty($canSubmitCapaian) || !empty($canManageTaruna)): ?>
                    <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold text-nowrap btn-tactile rj-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i> <?= !empty($isSelfOnly) ? 'Ajukan Capaian' : 'Tambah Capaian' ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4 Kotak Ringkasan Status Capaian (Filter Cepat) -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="<?= esc($detailBaseUrl . '?' . http_build_query(array_merge($capBaseQuery, ['validasi_capaian' => 'all']))) ?>" class="ecc-status-tile <?= ($curCapVal === 'all') ? 'is-active' : '' ?>">
                    <div class="ecc-icon-40" style="background-color: #eff6ff; color: #1e40af;">
                        <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Total Capaian</div>
                        <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= (int) $capaianCounts['total'] ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= esc($detailBaseUrl . '?' . http_build_query(array_merge($capBaseQuery, ['validasi_capaian' => ($curCapVal === 'tervalidasi') ? 'all' : 'tervalidasi']))) ?>" class="ecc-status-tile <?= ($curCapVal === 'tervalidasi') ? 'is-active' : '' ?>">
                    <div class="ecc-icon-40" style="background-color: #ecfdf5; color: #059669;">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Tervalidasi</div>
                        <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= (int) $capaianCounts['tervalidasi'] ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= esc($detailBaseUrl . '?' . http_build_query(array_merge($capBaseQuery, ['validasi_capaian' => ($curCapVal === 'diajukan') ? 'all' : 'diajukan']))) ?>" class="ecc-status-tile <?= ($curCapVal === 'diajukan') ? 'is-active' : '' ?>">
                    <div class="ecc-icon-40" style="background-color: #fef3c7; color: #b45309;">
                        <i class="bi bi-clock-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Diajukan</div>
                        <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= (int) $capaianCounts['diajukan'] ?></div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="<?= esc($detailBaseUrl . '?' . http_build_query(array_merge($capBaseQuery, ['validasi_capaian' => ($curCapVal === 'perlu_perbaikan') ? 'all' : 'perlu_perbaikan']))) ?>" class="ecc-status-tile <?= ($curCapVal === 'perlu_perbaikan') ? 'is-active' : '' ?>">
                    <div class="ecc-icon-40" style="background-color: #fef2f2; color: #b91c1c;">
                        <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Perlu Perbaikan</div>
                        <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= (int) $capaianCounts['perlu_perbaikan'] ?></div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Tabel Desktop Daftar Capaian -->
        <div class="table-responsive ecc-rj-scroll d-none d-md-block">
            <table class="table table-hover ecc-table-rj mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="0" data-sort-type="text">
                                <span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="1" data-sort-type="text">
                                <span>Kategori</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="2" data-sort-type="text">
                                <span>Nama Capaian</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="3" data-sort-type="text">
                                <span>Hasil</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="4" data-sort-type="number">
                                <span>Bukti</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="5" data-sort-type="text">
                                <span>Validasi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($capaianItems)): ?>
                        <tr class="rj-empty-row">
                            <td colspan="7" class="text-center py-4 text-secondary">Belum ada data capaian yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($capaianItems as $ci): ?>
                            <?php
                            $rawBuktiCap = trim((string) ($ci['bukti_berkas'] ?? ''));
                            $hasBukti    = ($rawBuktiCap !== '');
                            $isBuktiUrl  = $hasBukti && ketarunaan_is_http_url($rawBuktiCap, 2000);
                            $buktiHref   = $hasBukti ? ketarunaan_resolve_bukti_url($rawBuktiCap, 'capaian', (int) $ci['id'], true) : null;
                            $canEditCapaianRow = !empty($canManageTaruna)
                                || (!empty($canSubmitCapaian) && in_array((string) ($ci['status_validasi'] ?? ''), ['draf', 'perlu_perbaikan'], true));
                            ?>
                            <tr>
                                <td class="ecc-num text-nowrap" data-sort-val="<?= esc((string) ($ci['tanggal_kegiatan'] ?? '')) ?>">
                                    <?= esc(ketarunaan_format_tanggal($ci['tanggal_kegiatan'], true)) ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $ci['kategori'])) ?>">
                                    <?= ketarunaan_capaian_kategori_badge((string) $ci['kategori']) ?>
                                </td>
                                <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $ci['nama_capaian'])) ?>">
                                    <?= esc($ci['nama_capaian']) ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $ci['hasil_capaian'])) ?>">
                                    <span class="badge rounded-pill px-3 py-1 bg-light text-dark border fw-semibold" style="font-size: 0.75rem;">
                                        <?= esc($ci['hasil_capaian']) ?>
                                    </span>
                                </td>
                                <td data-sort-val="<?= $hasBukti ? '1' : '0' ?>">
                                    <?php if ($buktiHref): ?>
                                        <a href="<?= esc($buktiHref) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none ecc-num fw-medium text-nowrap" style="color: #1e40af;">
                                            <?= $isBuktiUrl ? 'Buka Tautan' : 'Lihat Berkas' ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $ci['status_validasi'])) ?>">
                                    <?= ketarunaan_validasi_badge((string) $ci['status_validasi']) ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="<?= base_url('ketarunaan/rekam-jejak/capaian/' . (int) $ci['id']) ?>" class="btn btn-light btn-sm rounded-3 px-3 border fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile text-nowrap" style="min-height: 32px; color: #1e40af;">
                                            Detail
                                        </a>
                                        <?php if ($canEditCapaianRow): ?>
                                            <a href="<?= base_url('ketarunaan/rekam-jejak/edit-capaian/' . (int) $ci['id']) ?>" class="btn btn-light btn-sm rounded-3 px-2 border text-secondary d-inline-flex align-items-center justify-content-center btn-tactile" style="min-height: 32px; min-width: 32px;" title="Edit Capaian" aria-label="Edit capaian <?= esc($ci['nama_capaian']) ?>">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Seluler Daftar Capaian -->
        <div class="d-md-none d-flex flex-column gap-2">
            <?php if (empty($capaianItems)): ?>
                <div class="text-center py-4 text-secondary small">Belum ada data capaian yang sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($capaianItems as $ci): ?>
                    <?php
                    $rawBuktiCap = trim((string) ($ci['bukti_berkas'] ?? ''));
                    $hasBukti    = ($rawBuktiCap !== '');
                    $isBuktiUrl  = $hasBukti && ketarunaan_is_http_url($rawBuktiCap, 2000);
                    $buktiHref   = $hasBukti ? ketarunaan_resolve_bukti_url($rawBuktiCap, 'capaian', (int) $ci['id'], true) : null;
                    $canEditCapaianRow = !empty($canManageTaruna)
                        || (!empty($canSubmitCapaian) && in_array((string) ($ci['status_validasi'] ?? ''), ['draf', 'perlu_perbaikan'], true));
                    ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($ci['nama_capaian']) ?></div>
                                <div class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                    <?= esc(ketarunaan_format_tanggal($ci['tanggal_kegiatan'], true)) ?> &bull; <?= esc($ci['hasil_capaian']) ?>
                                </div>
                            </div>
                            <?= ketarunaan_validasi_badge((string) $ci['status_validasi']) ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <a href="<?= base_url('ketarunaan/rekam-jejak/capaian/' . (int) $ci['id']) ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold flex-grow-1 rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                                Detail
                            </a>
                            <?php if ($buktiHref): ?>
                                <a href="<?= esc($buktiHref) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm border rounded-3 px-3 fw-semibold rj-touch-ctrl d-inline-flex align-items-center justify-content-center" style="color: #1e40af;">
                                    <?= $isBuktiUrl ? 'Buka Tautan' : 'Lihat Berkas' ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($canEditCapaianRow): ?>
                                <a href="<?= base_url('ketarunaan/rekam-jejak/edit-capaian/' . (int) $ci['id']) ?>" class="btn btn-light btn-sm border rounded-3 px-3 text-secondary rj-touch-ctrl d-inline-flex align-items-center justify-content-center" aria-label="Edit capaian">
                                    Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
            <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
            <span>Prestasi dan beasiswa ditampilkan dari masing-masing modul pada tab Kronologi.</span>
        </div>

    <?php elseif ($activeTab === 'dokumen'): ?>
        <!-- ==================== TAB 4: DOKUMEN BUKTI ==================== -->
        <?php
        $hasDokFilter = (($filters['modul_dokumen'] ?? 'all') !== 'all'
            || ($filters['jenis_dokumen'] ?? 'all') !== 'all'
            || trim((string) ($filters['q_dokumen'] ?? '')) !== '');
        ?>
        <div class="mb-3">
            <h2 class="h6 fw-bold text-dark mb-1">Dokumen Bukti</h2>
            <p class="text-secondary mb-0 small">Kumpulan tautan dan dokumen bukti pendukung dari seluruh catatan rekam jejak taruna.</p>
        </div>

        <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 align-items-end mb-4 rj-filter-form">
            <input type="hidden" name="tab" value="dokumen">
            <div class="col-12 col-md-3">
                <label for="dokModul" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Modul Sumber</label>
                <select id="dokModul" name="modul_dokumen" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Modul</option>
                    <option value="capaian" <?= (($filters['modul_dokumen'] ?? '') === 'capaian') ? 'selected' : '' ?>>Capaian</option>
                    <option value="prestasi" <?= (($filters['modul_dokumen'] ?? '') === 'prestasi') ? 'selected' : '' ?>>Prestasi</option>
                    <option value="beasiswa" <?= (($filters['modul_dokumen'] ?? '') === 'beasiswa') ? 'selected' : '' ?>>Beasiswa</option>
                    <?php if (!empty($canViewDiscipline)): ?>
                        <option value="pelanggaran" <?= (($filters['modul_dokumen'] ?? '') === 'pelanggaran') ? 'selected' : '' ?>>Pelanggaran / Disiplin</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="dokJenis" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Jenis Dokumen</label>
                <select id="dokJenis" name="jenis_dokumen" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis Dokumen</option>
                    <option value="sertifikat" <?= (($filters['jenis_dokumen'] ?? '') === 'sertifikat') ? 'selected' : '' ?>>Sertifikat</option>
                    <option value="surat" <?= (($filters['jenis_dokumen'] ?? '') === 'surat') ? 'selected' : '' ?>>Surat Tugas / SK</option>
                    <option value="piagam" <?= (($filters['jenis_dokumen'] ?? '') === 'piagam') ? 'selected' : '' ?>>Piagam</option>
                    <?php if (!empty($canViewDiscipline)): ?>
                        <option value="berita_acara" <?= (($filters['jenis_dokumen'] ?? '') === 'berita_acara') ? 'selected' : '' ?>>Berita Acara</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="dokQuery" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Cari Dokumen</label>
                <div class="d-flex gap-2">
                    <input type="text" id="dokQuery" name="q_dokumen" value="<?= esc($filters['q_dokumen'] ?? '') ?>" class="form-control ecc-control-sm" placeholder="Nama bukti atau kegiatan...">
                    <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile rj-touch-ctrl text-nowrap">Cari</button>
                    <?php if ($hasDokFilter): ?>
                        <a href="<?= esc($detailBaseUrl . '?tab=dokumen') ?>" class="btn btn-light border ecc-control-sm px-3 text-secondary d-inline-flex align-items-center justify-content-center btn-tactile rj-touch-ctrl text-nowrap">Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Tabel Desktop Dokumen Bukti -->
        <div class="table-responsive ecc-rj-scroll d-none d-md-block">
            <table class="table table-hover ecc-table-rj mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="0" data-sort-type="text">
                                <span>Dokumen / Tautan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="1" data-sort-type="text">
                                <span>Modul</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="2" data-sort-type="text">
                                <span>Kegiatan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="3" data-sort-type="text">
                                <span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th aria-sort="none">
                            <button type="button" class="rj-sort-btn" data-sort-col="4" data-sort-type="text">
                                <span>Akses</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i>
                            </button>
                        </th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dokumenItems)): ?>
                        <tr class="rj-empty-row">
                            <td colspan="6" class="text-center py-4 text-secondary">Tidak ada dokumen bukti yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dokumenItems as $doc): ?>
                            <?php
                            $isExtDoc = !empty($doc['is_external_url']);
                            $docExt   = !$isExtDoc ? strtolower(pathinfo((string) ($doc['raw_bukti'] ?? $doc['nama_berkas']), PATHINFO_EXTENSION)) : '';
                            $docIcon  = $isExtDoc
                                ? 'bi-link-45deg text-primary'
                                : (in_array($docExt, ['jpg', 'jpeg', 'png'], true) ? 'bi-file-earmark-image text-primary' : 'bi-file-earmark-pdf-fill text-danger');
                            ?>
                            <tr>
                                <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $doc['nama_berkas'])) ?>">
                                    <i class="bi <?= esc($docIcon) ?> me-1" aria-hidden="true"></i>
                                    <?= esc($doc['nama_berkas']) ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $doc['modul_sumber'])) ?>">
                                    <?= ketarunaan_modul_sumber_badge((string) $doc['modul_sumber']) ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $doc['kegiatan'])) ?>">
                                    <?= esc($doc['kegiatan']) ?>
                                </td>
                                <td class="ecc-num text-nowrap" data-sort-val="<?= esc((string) ($doc['tanggal'] ?? '')) ?>">
                                    <?= esc(ketarunaan_format_tanggal($doc['tanggal'], true)) ?>
                                </td>
                                <td data-sort-val="<?= esc(strtolower((string) $doc['akses'])) ?>">
                                    <?php if ($doc['akses'] === 'terbatas'): ?>
                                        <span class="text-danger fw-semibold small">
                                            <i class="bi bi-lock-fill" aria-hidden="true"></i> Terbatas
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success fw-semibold small">
                                            <i class="bi bi-unlock-fill" aria-hidden="true"></i> Internal
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                        <?php if (!empty($doc['can_access']) && !empty($doc['preview_url'])): ?>
                                            <?php if ($isExtDoc): ?>
                                                <a href="<?= esc($doc['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm rounded-3 px-2 border fw-semibold text-nowrap btn-tactile d-inline-flex align-items-center gap-1" style="min-height: 32px; color: #1e40af;">
                                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= esc($doc['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-light btn-sm rounded-3 px-2 border fw-semibold text-nowrap btn-tactile" style="min-height: 32px; color: #1e40af;">
                                                    Pratinjau
                                                </a>
                                                <a href="<?= esc($doc['download_url']) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-2 fw-semibold text-nowrap btn-tactile" style="min-height: 32px;">
                                                    Unduh
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <a href="<?= esc($doc['modul_url']) ?>" class="btn btn-light btn-sm rounded-3 px-2 border text-dark text-nowrap btn-tactile" style="min-height: 32px;">
                                            Sumber
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Seluler Dokumen Bukti -->
        <div class="d-md-none d-flex flex-column gap-2">
            <?php if (empty($dokumenItems)): ?>
                <div class="text-center py-4 text-secondary small">Tidak ada dokumen bukti yang sesuai filter.</div>
            <?php else: ?>
                <?php foreach ($dokumenItems as $doc): ?>
                    <?php $isExtDoc = !empty($doc['is_external_url']); ?>
                    <div class="border rounded-3 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div class="fw-bold text-dark small text-break"><?= esc($doc['nama_berkas']) ?></div>
                            <?= ketarunaan_modul_sumber_badge((string) $doc['modul_sumber']) ?>
                        </div>
                        <div class="text-secondary small mb-2"><?= esc($doc['kegiatan']) ?></div>
                        <div class="text-secondary ecc-num mb-2" style="font-size: 0.75rem;">
                            <?= esc(ketarunaan_format_tanggal($doc['tanggal'], true)) ?> &bull;
                            <?= $doc['akses'] === 'terbatas' ? '<span class="text-danger fw-semibold">Terbatas</span>' : '<span class="text-success fw-semibold">Internal</span>' ?>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <?php if (!empty($doc['can_access']) && !empty($doc['preview_url'])): ?>
                                <?php if ($isExtDoc): ?>
                                    <a href="<?= esc($doc['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold flex-grow-1 rj-touch-ctrl d-inline-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Buka Tautan
                                    </a>
                                <?php else: ?>
                                    <a href="<?= esc($doc['preview_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold flex-grow-1 rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                                        Pratinjau
                                    </a>
                                    <a href="<?= esc($doc['download_url']) ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3 fw-semibold rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                                        Unduh
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="<?= esc($doc['modul_url']) ?>" class="btn btn-light btn-sm border rounded-3 px-3 text-dark rj-touch-ctrl d-inline-flex align-items-center justify-content-center">
                                Sumber
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-3 text-secondary ecc-num small">
            <?= count($dokumenItems) ?> dokumen ditampilkan.
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary small">
            <i class="bi bi-info-circle text-primary" aria-hidden="true"></i>
            <span>Hak akses dokumen mengikuti masing-masing catatan asal. Penambahan bukti dilakukan melalui formulir capaian atau modul terkait.</span>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
        var buttons = table.querySelectorAll('thead .rj-sort-btn');
        var tbody = table.querySelector('tbody');
        if (!tbody || buttons.length === 0) return;

        var activeCol = null;
        var activeDir = 'asc';

        var applySort = function (btn) {
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

            var rows = Array.from(tbody.querySelectorAll('tr:not(.rj-empty-row)'));
            rows.sort(function (a, b) {
                var cellA = a.children[colIdx];
                var cellB = b.children[colIdx];
                var rawA = cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent.trim().toLowerCase()) : '';
                var rawB = cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent.trim().toLowerCase()) : '';

                if (sortType === 'number') {
                    var numA = parseFloat(rawA) || 0;
                    var numB = parseFloat(rawB) || 0;
                    return activeDir === 'asc' ? (numA - numB) : (numB - numA);
                }

                var cmp = rawA.localeCompare(rawB, 'id', { numeric: true });
                return activeDir === 'asc' ? cmp : -cmp;
            });

            rows.forEach(function (r) { tbody.appendChild(r); });
        };

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () { applySort(btn); });
        });
    });
});
</script>
<?= $this->endSection() ?>
