<?= $this->extend('layouts/ecc2_layout') ?>

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
$inisial           = strtoupper(substr(trim($namaLengkap), 0, 1));
$detailBaseUrl     = base_url('ketarunaan/rekam-jejak/detail/' . rawurlencode($nomorTaruna));
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
        border: 1px solid transparent;
    }
    .ecc-tab-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .ecc-tab-link.active {
        background-color: #0284c7;
        color: #ffffff;
    }
    .ecc-summary-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: border-color 0.2s ease;
    }
    .ecc-summary-box:hover {
        border-color: #93c5fd;
    }
    .ecc-icon-40 {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    .ecc-timeline-item {
        position: relative;
        padding-left: 24px;
        padding-bottom: 16px;
        border-left: 2px solid #e2e8f0;
    }
    .ecc-timeline-item:last-child {
        padding-bottom: 0;
    }
    .ecc-timeline-dot {
        position: absolute;
        left: -4px;
        top: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #0284c7;
    }
    .ecc-control-sm {
        min-height: 36px;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .ecc-table-rj thead th {
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
    .ecc-table-rj tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
    }
    .ecc-btn-pill-sm {
        min-height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= esc($namaLengkap) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">Rekam Jejak <?= esc($namaLengkap) ?></h4>
        <p class="text-secondary mb-0" style="font-size: 0.875rem;">Riwayat lengkap perjalanan taruna yang terintegrasi dari seluruh data di ECC 2.0.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?= base_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
            Profil Taruna
        </a>
        <a href="<?= base_url('ketarunaan/rekam-jejak') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2">
            <i class="bi bi-chevron-left"></i> Kembali ke Daftar
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

<!-- Kartu Identitas Header Taruna -->
<div class="ecc-rj-card mb-4">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-4 d-flex align-items-center gap-3">
            <div class="ecc-avatar-lg"><?= esc($inisial) ?></div>
            <div>
                <h5 class="fw-bold text-dark mb-1"><?= esc($namaLengkap) ?></h5>
                <div class="text-secondary" style="font-size: 0.8125rem;">
                    Tingkat <?= (int) ($taruna['tingkat'] ?? 1) ?> &bull; Semester <?= (int) ($taruna['semester_aktif'] ?? 1) ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Nomor Taruna</div>
            <div class="fw-bold text-dark ecc-num" style="font-size: 1rem;"><?= esc($nomorTaruna) ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Program Studi</div>
            <div class="fw-bold text-dark" style="font-size: 1rem;"><?= esc($taruna['kode_prodi'] ?? '-') ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary" style="font-size: 0.75rem;">Angkatan</div>
            <div class="fw-bold text-dark ecc-num" style="font-size: 1rem;"><?= esc((string) ($taruna['angkatan'] ?? '-')) ?></div>
        </div>
        <div class="col-6 col-md-2 border-start ps-md-3">
            <div class="text-secondary mb-1" style="font-size: 0.75rem;">Status Pendidikan</div>
            <div><?= ketarunaan_status_badge((string) ($taruna['status_terkini'] ?? 'aktif')) ?></div>
        </div>
    </div>
</div>

<!-- Kontainer 4 Tab Rekam Jejak -->
<div class="ecc-rj-card">
    <div class="ecc-tab-nav" role="tablist">
        <a href="<?= $detailBaseUrl . '?tab=ringkasan' ?>" class="ecc-tab-link <?= ($activeTab === 'ringkasan') ? 'active' : '' ?>">
            <i class="bi bi-clipboard-data"></i> Ringkasan
        </a>
        <a href="<?= $detailBaseUrl . '?tab=kronologi' ?>" class="ecc-tab-link <?= ($activeTab === 'kronologi') ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> Kronologi
        </a>
        <a href="<?= $detailBaseUrl . '?tab=capaian' ?>" class="ecc-tab-link <?= ($activeTab === 'capaian') ? 'active' : '' ?>">
            <i class="bi bi-award"></i> Daftar Capaian
        </a>
        <a href="<?= $detailBaseUrl . '?tab=dokumen' ?>" class="ecc-tab-link <?= ($activeTab === 'dokumen') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i> Dokumen Bukti
        </a>
    </div>

    <?php if ($activeTab === 'ringkasan'): ?>
        <!-- ==================== TAB 1: RINGKASAN ==================== -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= $detailBaseUrl . '?tab=capaian' ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #fef3c7; color: #d97706;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Capaian Tervalidasi</div>
                            <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= (int) $capaianCounts['tervalidasi'] ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= $detailBaseUrl . '?tab=kronologi&kategori_kronologi=prestasi' ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #f5f3ff; color: #7c3aed;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Prestasi</div>
                            <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= $prestasiCount ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= $detailBaseUrl . '?tab=kronologi&kategori_kronologi=beasiswa' ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #fff7ed; color: #ea580c;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Beasiswa</div>
                            <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= $beasiswaCount ?></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="<?= base_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna) . '?tab=status') ?>" class="ecc-summary-box">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ecc-icon-40" style="background-color: #eff6ff; color: #0284c7;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <div class="text-secondary" style="font-size: 0.75rem;">Status Pendidikan</div>
                            <div class="fw-bold text-dark" style="font-size: 1.125rem;">
                                <?= esc(ketarunaan_status_meta((string) ($taruna['status_terkini'] ?? 'aktif'))['label']) ?>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-secondary"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Kolom Kiri: Ringkasan Perjalanan -->
            <div class="col-12 col-lg-7">
                <div class="border rounded-3 p-3 h-100">
                    <h6 class="fw-bold text-dark mb-3">Ringkasan Perjalanan</h6>
                    <div class="table-responsive">
                        <table class="table ecc-table-rj mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ringkasanJalan as $rj): ?>
                                    <tr>
                                        <td class="ecc-num text-nowrap" style="font-size: 0.8125rem;">
                                            <div class="fw-semibold text-dark"><?= esc(ketarunaan_format_tanggal($rj['mulai_berlaku'], true)) ?></div>
                                            <?php if (!empty($rj['akhir_berlaku'])): ?>
                                                <div class="text-secondary">s.d. <?= esc(ketarunaan_format_tanggal($rj['akhir_berlaku'], true)) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($rj['kategori']) ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($rj['judul']) ?></div>
                                            <div class="text-secondary" style="font-size: 0.8125rem;"><?= esc($rj['uraian']) ?></div>
                                        </td>
                                        <td class="text-end"><?= ketarunaan_status_badge((string) $rj['kode_status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Capaian Terbaru -->
            <div class="col-12 col-lg-5">
                <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-3">Capaian Terbaru</h6>
                        <?php if (empty($capaianTerbaru)): ?>
                            <div class="text-secondary py-3" style="font-size: 0.875rem;">Belum ada capaian tervalidasi yang tercatat.</div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($capaianTerbaru as $ct): ?>
                                    <a href="<?= base_url('ketarunaan/rekam-jejak/capaian/' . (int) $ct['id']) ?>" class="border rounded-3 p-3 text-decoration-none d-block bg-light-subtle">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                            <span class="text-secondary ecc-num" style="font-size: 0.75rem;">
                                                <?= esc(ketarunaan_format_tanggal($ct['tanggal_kegiatan'], true)) ?> &bull; <?= esc(ketarunaan_capaian_kategori_meta($ct['kategori'])['label']) ?>
                                            </span>
                                            <?= ketarunaan_validasi_badge((string) $ct['status_validasi']) ?>
                                        </div>
                                        <div class="fw-bold text-dark mb-1"><?= esc($ct['nama_capaian']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.8125rem;"><?= esc($ct['uraian_capaian']) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <a href="<?= $detailBaseUrl . '?tab=kronologi' ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold">
                            Buka Kronologi
                        </a>
                        <a href="<?= $detailBaseUrl . '?tab=capaian' ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold">
                            Lihat Semua Capaian
                        </a>
                        <?php if (!empty($canManageTaruna)): ?>
                            <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold ms-md-auto">
                                <i class="bi bi-plus-lg"></i> Tambah Capaian
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
            <i class="bi bi-info-circle text-primary"></i>
            <span>Riwayat bersumber dari data tervalidasi di seluruh sistem ECC 2.0. Akses terhadap data mengikuti kewenangan pengguna.</span>
        </div>

    <?php elseif ($activeTab === 'kronologi'): ?>
        <!-- ==================== TAB 2: KRONOLOGI ==================== -->
        <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 align-items-end mb-4">
            <input type="hidden" name="tab" value="kronologi">
            <div class="col-12 col-md-4">
                <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Kategori</label>
                <select name="kategori_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Kategori</option>
                    <option value="pendidikan" <?= (($filters['kategori_kronologi'] ?? '') === 'pendidikan') ? 'selected' : '' ?>>Pendidikan</option>
                    <option value="status_pendidikan" <?= (($filters['kategori_kronologi'] ?? '') === 'status_pendidikan') ? 'selected' : '' ?>>Status Pendidikan</option>
                    <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                        <option value="<?= esc($kKode) ?>" <?= (($filters['kategori_kronologi'] ?? '') === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                    <?php endforeach; ?>
                    <option value="prestasi" <?= (($filters['kategori_kronologi'] ?? '') === 'prestasi') ? 'selected' : '' ?>>Prestasi</option>
                    <option value="beasiswa" <?= (($filters['kategori_kronologi'] ?? '') === 'beasiswa') ? 'selected' : '' ?>>Beasiswa</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select name="periode_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= ((string) ($filters['periode_kronologi'] ?? '') === (string) $ta['id']) ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status Validasi</label>
                <select name="validasi_kronologi" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Status Validasi</option>
                    <option value="tervalidasi" <?= (($filters['validasi_kronologi'] ?? '') === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                    <option value="diajukan" <?= (($filters['validasi_kronologi'] ?? '') === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                    <option value="perlu_perbaikan" <?= (($filters['validasi_kronologi'] ?? '') === 'perlu_perbaikan') ? 'selected' : '' ?>>Perlu Perbaikan</option>
                </select>
            </div>
        </form>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <?php if (empty($kronologiItems)): ?>
                    <div class="border rounded-3 p-4 text-center text-secondary">Tidak ada catatan kronologi yang sesuai filter.</div>
                <?php else: ?>
                    <div class="ps-2">
                        <?php foreach ($kronologiItems as $kr): ?>
                            <div class="ecc-timeline-item">
                                <span class="ecc-timeline-dot"></span>
                                <div class="border rounded-3 p-3 bg-white">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark ecc-num" style="font-size: 0.8125rem;">
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
                                            <div class="text-secondary" style="font-size: 0.8125rem;"><?= esc($kr['uraian']) ?></div>
                                        </div>
                                        <a href="<?= esc($kr['detail_url']) ?>" class="btn btn-light border ecc-btn-pill-sm text-nowrap">
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
                    <h6 class="fw-bold text-dark mb-3">Ringkasan Rekam Jejak</h6>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary" style="font-size: 0.875rem;">Capaian</span>
                        <span class="fw-bold text-dark ecc-num"><?= (int) $capaianCounts['total'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-secondary" style="font-size: 0.875rem;">Prestasi</span>
                        <span class="fw-bold text-dark ecc-num"><?= $prestasiCount ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary" style="font-size: 0.875rem;">Program Beasiswa</span>
                        <span class="fw-bold text-dark ecc-num"><?= $beasiswaCount ?></span>
                    </div>
                </div>

                <div class="border rounded-3 p-3" style="background-color: #fffbeb; border-color: #fde68a !important;">
                    <div class="d-flex align-items-center gap-2 fw-bold mb-1" style="color: #b45309; font-size: 0.875rem;">
                        <i class="bi bi-lock-fill"></i> Akses Terbatas
                    </div>
                    <p class="mb-0 text-secondary" style="font-size: 0.8125rem;">
                        Catatan pembinaan dan pelanggaran hanya ditampilkan bagi peran yang berwenang.
                    </p>
                </div>
            </div>
        </div>

    <?php elseif ($activeTab === 'capaian'): ?>
        <!-- ==================== TAB 3: DAFTAR CAPAIAN ==================== -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
            <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 flex-grow-1">
                <input type="hidden" name="tab" value="capaian">
                <div class="col-12 col-md-4">
                    <select name="kategori_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Kategori</option>
                        <?php foreach (($refs['kategori_catalog'] ?? []) as $kKode => $kMeta): ?>
                            <option value="<?= esc($kKode) ?>" <?= (($filters['kategori_capaian'] ?? '') === $kKode) ? 'selected' : '' ?>><?= esc($kMeta['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <select name="periode_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Periode</option>
                        <?php foreach (($refs['tahun_akademik_list'] ?? []) as $ta): ?>
                            <option value="<?= (int) $ta['id'] ?>" <?= ((string) ($filters['periode_capaian'] ?? '') === (string) $ta['id']) ? 'selected' : '' ?>>
                                <?= esc($ta['kode_tahun']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <select name="validasi_capaian" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="all">Semua Status Validasi</option>
                        <option value="tervalidasi" <?= (($filters['validasi_capaian'] ?? '') === 'tervalidasi') ? 'selected' : '' ?>>Tervalidasi</option>
                        <option value="diajukan" <?= (($filters['validasi_capaian'] ?? '') === 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                        <option value="perlu_perbaikan" <?= (($filters['validasi_capaian'] ?? '') === 'perlu_perbaikan') ? 'selected' : '' ?>>Dikembalikan / Perlu Perbaikan</option>
                    </select>
                </div>
            </form>
            <?php if (!empty($canManageTaruna)): ?>
                <a href="<?= base_url('ketarunaan/rekam-jejak/tambah-capaian/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-primary ecc-control-sm px-3 fw-semibold text-nowrap">
                    <i class="bi bi-plus-lg"></i> Tambah Capaian
                </a>
            <?php endif; ?>
        </div>

        <!-- 4 Kotak Ringkasan Status Capaian -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3">
                    <div class="ecc-icon-40" style="background-color: #eff6ff; color: #0284c7;">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Total Capaian</div>
                        <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= (int) $capaianCounts['total'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3">
                    <div class="ecc-icon-40" style="background-color: #ecfdf5; color: #059669;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Tervalidasi</div>
                        <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= (int) $capaianCounts['tervalidasi'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3">
                    <div class="ecc-icon-40" style="background-color: #fef3c7; color: #d97706;">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Diajukan</div>
                        <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= (int) $capaianCounts['diajukan'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="border rounded-3 p-3 d-flex align-items-center gap-3">
                    <div class="ecc-icon-40" style="background-color: #fef2f2; color: #dc2626;">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div>
                        <div class="text-secondary" style="font-size: 0.75rem;">Dikembalikan</div>
                        <div class="fw-bold text-dark ecc-num" style="font-size: 1.25rem;"><?= (int) $capaianCounts['perlu_perbaikan'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table ecc-table-rj mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Nama Capaian</th>
                        <th>Hasil</th>
                        <th>Bukti</th>
                        <th>Validasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($capaianItems)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">Belum ada data capaian yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($capaianItems as $ci): ?>
                            <tr>
                                <td class="ecc-num text-nowrap"><?= esc(ketarunaan_format_tanggal($ci['tanggal_kegiatan'], true)) ?></td>
                                <td><?= ketarunaan_capaian_kategori_badge((string) $ci['kategori']) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($ci['nama_capaian']) ?></td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 bg-light text-dark border fw-semibold" style="font-size: 0.75rem;">
                                        <?= esc($ci['hasil_capaian']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('ketarunaan/rekam-jejak/unduh-bukti/capaian/' . (int) $ci['id'] . '?inline=1') ?>" target="_blank" class="text-decoration-none ecc-num">
                                        <?= (int) ($ci['jumlah_berkas'] ?? 1) ?> berkas
                                    </a>
                                </td>
                                <td>
                                    <?= ketarunaan_validasi_badge($ci['status_validasi'] === 'perlu_perbaikan' ? 'dikembalikan' : (string) $ci['status_validasi']) ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('ketarunaan/rekam-jejak/capaian/' . (int) $ci['id']) ?>" class="btn btn-outline-primary ecc-btn-pill-sm" title="Lihat Detail Capaian">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
            <i class="bi bi-info-circle text-primary"></i>
            <span>Data capaian merupakan agregasi di ECC 2.0. Prestasi dan Beasiswa ditampilkan dari modul sumber pada tab Kronologi.</span>
        </div>

    <?php elseif ($activeTab === 'dokumen'): ?>
        <!-- ==================== TAB 4: DOKUMEN BUKTI ==================== -->
        <div class="mb-3">
            <h6 class="fw-bold text-dark mb-1">Dokumen Bukti</h6>
            <p class="text-secondary mb-0" style="font-size: 0.8125rem;">Kumpulan dokumen bukti yang berasal dari berbagai modul sumber dalam rekam jejak taruna.</p>
        </div>

        <form method="get" action="<?= $detailBaseUrl ?>" class="row g-2 align-items-end mb-4">
            <input type="hidden" name="tab" value="dokumen">
            <div class="col-12 col-md-4">
                <select name="modul_dokumen" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Modul</option>
                    <option value="capaian" <?= (($filters['modul_dokumen'] ?? '') === 'capaian') ? 'selected' : '' ?>>Capaian</option>
                    <option value="prestasi" <?= (($filters['modul_dokumen'] ?? '') === 'prestasi') ? 'selected' : '' ?>>Prestasi</option>
                    <option value="beasiswa" <?= (($filters['modul_dokumen'] ?? '') === 'beasiswa') ? 'selected' : '' ?>>Beasiswa</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <select name="jenis_dokumen" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all">Semua Jenis Dokumen</option>
                    <option value="sertifikat" <?= (($filters['jenis_dokumen'] ?? '') === 'sertifikat') ? 'selected' : '' ?>>Sertifikat</option>
                    <option value="surat" <?= (($filters['jenis_dokumen'] ?? '') === 'surat') ? 'selected' : '' ?>>Surat Tugas / SK</option>
                    <option value="piagam" <?= (($filters['jenis_dokumen'] ?? '') === 'piagam') ? 'selected' : '' ?>>Piagam</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <input type="text" name="q_dokumen" value="<?= esc($filters['q_dokumen'] ?? '') ?>" class="form-control ecc-control-sm" placeholder="Cari dokumen...">
                <button type="submit" class="btn btn-primary ecc-control-sm px-3 fw-semibold">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table ecc-table-rj mb-0">
                <thead>
                    <tr>
                        <th>Nama Berkas</th>
                        <th>Modul Sumber</th>
                        <th>Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Akses</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dokumenItems)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">Tidak ada dokumen bukti yang sesuai filter.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dokumenItems as $doc): ?>
                            <tr>
                                <td class="fw-semibold text-dark">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                    <?= esc($doc['nama_berkas']) ?>
                                </td>
                                <td><?= ketarunaan_modul_sumber_badge((string) $doc['modul_sumber']) ?></td>
                                <td><?= esc($doc['kegiatan']) ?></td>
                                <td class="ecc-num text-nowrap"><?= esc(ketarunaan_format_tanggal($doc['tanggal'], true)) ?></td>
                                <td>
                                    <?php if ($doc['akses'] === 'terbatas'): ?>
                                        <span class="text-danger fw-semibold" style="font-size: 0.8125rem;">
                                            <i class="bi bi-lock-fill"></i> Terbatas
                                        </span>
                                    <?php else: ?>
                                        <span class="text-success fw-semibold" style="font-size: 0.8125rem;">
                                            <i class="bi bi-unlock-fill"></i> Internal
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                                        <?php if (!empty($doc['can_access'])): ?>
                                            <a href="<?= esc($doc['preview_url']) ?>" target="_blank" class="btn btn-outline-primary ecc-btn-pill-sm">
                                                Pratinjau
                                            </a>
                                            <a href="<?= esc($doc['download_url']) ?>" class="btn btn-outline-secondary ecc-btn-pill-sm">
                                                Unduh
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-light border ecc-btn-pill-sm text-secondary" disabled>Pratinjau</button>
                                            <button type="button" class="btn btn-light border ecc-btn-pill-sm text-secondary" disabled>Unduh</button>
                                        <?php endif; ?>
                                        <a href="<?= esc($doc['modul_url']) ?>" class="btn btn-light border ecc-btn-pill-sm text-dark">
                                            Buka Modul Sumber
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-secondary ecc-num" style="font-size: 0.8125rem;">
            <?= count($dokumenItems) ?> dokumen ditampilkan.
        </div>

        <div class="mt-3 px-3 py-2 rounded-3 bg-light border d-flex align-items-center gap-2 text-secondary" style="font-size: 0.8125rem;">
            <i class="bi bi-info-circle text-primary"></i>
            <span>Berkas mengikuti izin modul sumber. Dokumen tidak dapat diunggah secara mandiri di sini untuk mencegah bukti yang terlepas dari sumber data.</span>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
