<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Validasi Data<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/_governance_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$kpi           = $kpi ?? ['menunggu' => 0, 'perlu_perbaikan' => 0, 'total_antrean' => 0, 'rencana_kembali' => 0];
$countsSource  = $counts_by_source ?? [];
$items         = $items ?? [];
$rkList        = $rencana_kembali_list ?? [];
$auditTrail    = $audit_trail ?? [];
$entityOptions = $entity_options ?? [];
$activeTab     = $active_tab ?? 'antrean';
$filters       = $filters ?? [];
$tanggalAcuan  = $tanggal_acuan ?? date('Y-m-d');

$selSumber = (string) ($filters['sumber'] ?? 'semua');
$stNow     = (string) ($filters['status'] ?? 'antrean');
$qNow      = trim((string) ($filters['q'] ?? ''));
$entNow    = (string) ($filters['entitas'] ?? '');

$hasAntreanFilter = ($selSumber !== 'semua') || ($stNow !== 'antrean') || ($qNow !== '');
?>
<div class="container-fluid px-0">

    <!-- Header Halaman -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?= esc(ketarunaan_home_url()) ?>" class="text-decoration-none ecc-text-muted-aa">Ketarunaan</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Validasi Data</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-1">Validasi Data</h1>
            <p class="ecc-text-muted-aa mb-0 small">Pusat antrean persetujuan data, pengingat cuti dan skorsing, serta jejak aktivitas</p>
        </div>
        <a href="<?= site_url('ketarunaan/dashboard?tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center text-nowrap btn-tactile gov-touch-ctrl flex-shrink-0">
            Kembali ke Ringkasan
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('success')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-3 py-2 px-3 d-flex align-items-center justify-content-between" role="alert">
            <span class="small"><?= esc((string) session()->getFlashdata('error')) ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 4 Kartu KPI Interaktif (Tanpa border-top 4px sesuai craft-floor.md) -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <a href="<?= site_url('ketarunaan/validasi?tab=antrean&status=diajukan&sumber=' . urlencode($selSumber) . '&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
               class="ecc-gov-kpi <?= ($activeTab === 'antrean' && $stNow === 'diajukan') ? 'active-kpi' : '' ?>">
                <div class="ecc-text-muted-aa small fw-semibold mb-1">Menunggu Validasi</div>
                <div class="h4 fw-bold text-warning-emphasis mb-0 ecc-num"><?= number_format((int) $kpi['menunggu']) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Siap diperiksa pejabat</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="<?= site_url('ketarunaan/validasi?tab=antrean&status=perlu_perbaikan&sumber=' . urlencode($selSumber) . '&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
               class="ecc-gov-kpi <?= ($activeTab === 'antrean' && $stNow === 'perlu_perbaikan') ? 'active-kpi' : '' ?>">
                <div class="ecc-text-muted-aa small fw-semibold mb-1">Perlu Perbaikan</div>
                <div class="h4 fw-bold text-danger-emphasis mb-0 ecc-num"><?= number_format((int) $kpi['perlu_perbaikan']) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Dikembalikan ke pengelola</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="<?= site_url('ketarunaan/validasi?tab=antrean&status=antrean&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
               class="ecc-gov-kpi <?= ($activeTab === 'antrean' && $stNow === 'antrean' && $selSumber === 'semua') ? 'active-kpi' : '' ?>">
                <div class="ecc-text-muted-aa small fw-semibold mb-1">Total Antrean Terbuka</div>
                <div class="h4 fw-bold text-primary mb-0 ecc-num"><?= number_format((int) $kpi['total_antrean']) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Lintas modul sesuai peran</div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <a href="<?= site_url('ketarunaan/validasi?tab=rencana_kembali&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
               class="ecc-gov-kpi <?= $activeTab === 'rencana_kembali' ? 'active-kpi' : '' ?>">
                <div class="ecc-text-muted-aa small fw-semibold mb-1">Rencana Kembali Jatuh Tempo</div>
                <div class="h4 fw-bold text-dark mb-0 ecc-num"><?= number_format((int) $kpi['rencana_kembali']) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Acuan <?= esc(ketarunaan_format_tanggal($tanggalAcuan, true)) ?></div>
            </a>
        </div>
    </div>

    <!-- Bilah Tab Utama (Standar Garis Bawah ECC 1.5) -->
    <nav class="ecc-tab-nav" aria-label="Navigasi Modul Validasi Data">
        <a href="<?= site_url('ketarunaan/validasi?tab=antrean&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
           class="ecc-tab-link <?= $activeTab === 'antrean' ? 'active' : '' ?>"<?= $activeTab === 'antrean' ? ' aria-current="page"' : '' ?>>
            <span>Antrean Validasi</span>
            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-num" style="font-size: 0.75rem;"><?= (int) $kpi['total_antrean'] ?></span>
        </a>
        <a href="<?= site_url('ketarunaan/validasi?tab=rencana_kembali&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
           class="ecc-tab-link <?= $activeTab === 'rencana_kembali' ? 'active' : '' ?>"<?= $activeTab === 'rencana_kembali' ? ' aria-current="page"' : '' ?>>
            <span>Rencana Kembali Cuti &amp; Skorsing</span>
            <span class="badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle ecc-num" style="font-size: 0.75rem;"><?= (int) $kpi['rencana_kembali'] ?></span>
        </a>
        <a href="<?= site_url('ketarunaan/validasi?tab=jejak_aktivitas&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
           class="ecc-tab-link <?= $activeTab === 'jejak_aktivitas' ? 'active' : '' ?>"<?= $activeTab === 'jejak_aktivitas' ? ' aria-current="page"' : '' ?>>
            <span>Jejak Aktivitas</span>
        </a>
    </nav>

    <?php if ($activeTab === 'antrean'): ?>
        <!-- Filter Sumber & Pencarian -->
        <div class="ecc-gov-card mb-3 py-3">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="<?= site_url('ketarunaan/validasi?tab=antrean&sumber=semua&status=' . urlencode($stNow) . '&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
                   class="btn btn-sm rounded-pill <?= $selSumber === 'semua' ? 'btn-primary' : 'btn-light border' ?> ecc-btn-pill">
                    <span>Semua Modul</span>
                    <span class="badge rounded-pill bg-white text-dark border ecc-num"><?= (int) $kpi['total_antrean'] ?></span>
                </a>
                <?php foreach ($countsSource as $kode => $grp): ?>
                    <a href="<?= site_url('ketarunaan/validasi?tab=antrean&sumber=' . urlencode($kode) . '&status=' . urlencode($stNow) . '&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>"
                       class="btn btn-sm rounded-pill <?= $selSumber === $kode ? 'btn-primary' : 'btn-light border' ?> ecc-btn-pill">
                        <span><?= esc($grp['label']) ?></span>
                        <span class="badge rounded-pill bg-white text-dark border ecc-num"><?= (int) $grp['total_antrean'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="get" action="<?= site_url('ketarunaan/validasi') ?>" class="row g-2 align-items-end gov-filter-form">
                <input type="hidden" name="tab" value="antrean">
                <input type="hidden" name="sumber" value="<?= esc($selSumber) ?>">
                <input type="hidden" name="tanggal_acuan" value="<?= esc($tanggalAcuan) ?>">
                <div class="col-12 col-md-4">
                    <label for="filterStatusValidasi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Pengajuan</label>
                    <select id="filterStatusValidasi" name="status" class="form-select ecc-control-sm">
                        <option value="antrean" <?= $stNow === 'antrean' ? 'selected' : '' ?>>Perlu Tindakan (Diajukan &amp; Perbaikan)</option>
                        <option value="diajukan" <?= $stNow === 'diajukan' ? 'selected' : '' ?>>Menunggu Validasi (Diajukan)</option>
                        <option value="perlu_perbaikan" <?= $stNow === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                        <option value="draf" <?= $stNow === 'draf' ? 'selected' : '' ?>>Masih Draf</option>
                        <option value="tervalidasi" <?= $stNow === 'tervalidasi' ? 'selected' : '' ?>>Sudah Tervalidasi</option>
                        <option value="semua" <?= $stNow === 'semua' ? 'selected' : '' ?>>Semua Status</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label for="filterKeywordValidasi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Nama, Nomor Taruna, atau Judul</label>
                    <input type="text" id="filterKeywordValidasi" name="q" class="form-control ecc-control-sm" value="<?= esc($qNow) ?>" placeholder="Ketik kata kunci pencarian...">
                </div>
                <div class="col-12 col-md-3 d-flex flex-wrap justify-content-md-end gap-2">
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile gov-touch-ctrl text-nowrap">Terapkan</button>
                    <?php if ($hasAntreanFilter): ?>
                        <a href="<?= site_url('ketarunaan/validasi?tab=antrean&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile gov-touch-ctrl text-nowrap">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Daftar Antrean Desktop -->
        <div class="ecc-gov-card p-0 overflow-hidden d-none d-md-block">
            <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Daftar Antrean Validasi</h2>
                    <span class="ecc-text-muted-aa small">Status Pendidikan, Foto Taruna, dan Capaian dapat disahkan langsung; modul lain diarahkan ke lembar pemeriksaannya.</span>
                </div>
                <span class="badge rounded-pill bg-light text-dark border px-3 py-1 ecc-num" style="font-size: 0.75rem;"><?= count($items) ?> data ditampilkan</span>
            </div>
            <div class="ecc-table-scroll">
                <table class="table ecc-table-gov table-hover mb-0" data-sortable-table="true">
                    <thead>
                        <tr>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="0"><span>Sumber</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="1"><span>Taruna / Mitra</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="2"><span>Uraian Pengajuan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="3"><span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="4"><span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="fw-semibold text-dark mb-1">Tidak ada antrean yang cocok dengan filter saat ini</div>
                                    <div class="ecc-text-muted-aa small mb-3">
                                        <?= $hasAntreanFilter ? 'Reset filter untuk menampilkan seluruh antrean terbuka.' : 'Seluruh pengajuan data lintas modul telah selesai diperiksa.' ?>
                                    </div>
                                    <?php if ($hasAntreanFilter): ?>
                                        <a href="<?= site_url('ketarunaan/validasi?tab=antrean&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Reset Filter</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td data-sort-val="<?= esc((string) $item['sumber']) ?>"><?= ketarunaan_modul_sumber_badge((string) $item['sumber']) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) $item['subjek_utama'])) ?>">
                                        <div class="fw-semibold text-dark"><?= esc($item['subjek_utama']) ?></div>
                                        <div class="ecc-text-muted-aa small ecc-num"><?= esc($item['subjek_kode']) ?></div>
                                    </td>
                                    <td data-sort-val="<?= esc(strtolower((string) $item['judul'])) ?>">
                                        <div class="fw-semibold text-dark"><?= esc($item['judul']) ?></div>
                                        <div class="ecc-text-muted-aa small"><?= esc($item['ringkasan']) ?></div>
                                        <?php if (!empty($item['catatan'])): ?>
                                            <div class="small text-danger-emphasis mt-1">Catatan: <?= esc($item['catatan']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap ecc-num small ecc-text-muted-aa" data-sort-val="<?= esc((string) ($item['sort_date'] ?? '')) ?>"><?= esc($item['tanggal_tampil']) ?></td>
                                    <td class="text-nowrap" data-sort-val="<?= esc((string) $item['status_validasi']) ?>"><?= ketarunaan_validasi_badge((string) $item['status_validasi']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                            <?php if (!empty($item['can_decide_inline']) && !empty($item['inline_action_url'])): ?>
                                                <form method="post" action="<?= esc($item['inline_action_url']) ?>" class="d-inline form-setuju-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="keputusan" value="tervalidasi">
                                                    <input type="hidden" name="catatan_validasi" value="">
                                                    <button type="submit" class="btn btn-success btn-sm ecc-btn-pill btn-tactile" data-judul="<?= esc($item['judul']) ?>" onclick="return konfirmasiSetujuValidasi(this, event);">
                                                        <i class="bi bi-check2" aria-hidden="true"></i> <span>Setujui</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-outline-danger btn-sm ecc-btn-pill btn-tactile" onclick="bukaModalKembalikan('<?= esc($item['inline_action_url']) ?>', '<?= esc($item['judul']) ?>')">
                                                    <i class="bi bi-arrow-return-left" aria-hidden="true"></i> <span>Kembalikan</span>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (!empty($item['dokumen_url'])): ?>
                                                <a href="<?= esc($item['dokumen_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm ecc-btn-pill btn-tactile">
                                                    <?= esc($item['dokumen_label'] ?? 'Lihat SK') ?>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= esc($item['detail_url']) ?>" class="btn btn-light btn-sm border ecc-btn-pill btn-tactile">
                                                Periksa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Kartu Mobile Antrean Validasi -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($items)): ?>
                <div class="ecc-gov-card text-center py-4">
                    <div class="fw-semibold text-dark mb-1">Tidak ada antrean yang cocok dengan filter</div>
                    <?php if ($hasAntreanFilter): ?>
                        <a href="<?= site_url('ketarunaan/validasi?tab=antrean&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 mt-2 gov-touch-ctrl d-inline-flex align-items-center">Reset Filter</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="ecc-gov-card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <?= ketarunaan_modul_sumber_badge((string) $item['sumber']) ?>
                            <?= ketarunaan_validasi_badge((string) $item['status_validasi']) ?>
                        </div>
                        <div class="fw-bold text-dark"><?= esc($item['subjek_utama']) ?></div>
                        <div class="ecc-text-muted-aa ecc-num mb-2" style="font-size: 0.75rem;"><?= esc($item['subjek_kode']) ?> &bull; <?= esc($item['tanggal_tampil']) ?></div>
                        <div class="fw-semibold text-dark small mb-1"><?= esc($item['judul']) ?></div>
                        <div class="ecc-text-muted-aa small mb-2"><?= esc($item['ringkasan']) ?></div>
                        <?php if (!empty($item['catatan'])): ?>
                            <div class="small text-danger-emphasis mb-2">Catatan: <?= esc($item['catatan']) ?></div>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <?php if (!empty($item['can_decide_inline']) && !empty($item['inline_action_url'])): ?>
                                <form method="post" action="<?= esc($item['inline_action_url']) ?>" class="flex-fill form-setuju-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="keputusan" value="tervalidasi">
                                    <input type="hidden" name="catatan_validasi" value="">
                                    <button type="submit" class="btn btn-success btn-sm ecc-btn-pill w-100 gov-touch-ctrl" data-judul="<?= esc($item['judul']) ?>" onclick="return konfirmasiSetujuValidasi(this, event);">
                                        Setujui
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm ecc-btn-pill flex-fill gov-touch-ctrl" onclick="bukaModalKembalikan('<?= esc($item['inline_action_url']) ?>', '<?= esc($item['judul']) ?>')">
                                    Kembalikan
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($item['dokumen_url'])): ?>
                                <a href="<?= esc($item['dokumen_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm ecc-btn-pill gov-touch-ctrl">
                                    <?= esc($item['dokumen_label'] ?? 'Lihat SK') ?>
                                </a>
                            <?php endif; ?>
                            <a href="<?= esc($item['detail_url']) ?>" class="btn btn-light btn-sm border ecc-btn-pill gov-touch-ctrl">
                                Periksa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php elseif ($activeTab === 'rencana_kembali'): ?>
        <!-- Tab Rencana Kembali Cuti/Skorsing (UAT-03) -->
        <div class="ecc-gov-card mb-3 py-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-1">Tinjauan Rencana Kembali Cuti &amp; Skorsing</h2>
                    <p class="ecc-text-muted-aa small mb-0">Taruna yang melewati tanggal rencana kembali tetap berstatus Cuti atau Skorsing hingga pengaktifan kembali diajukan dan disetujui.</p>
                </div>
                <form method="get" action="<?= site_url('ketarunaan/validasi') ?>" class="d-flex align-items-center gap-2 gov-filter-form">
                    <input type="hidden" name="tab" value="rencana_kembali">
                    <input type="text" id="rkTanggalAcuan" name="tanggal_acuan" class="form-control ecc-control-sm ecc-num" value="<?= esc($tanggalAcuan) ?>" style="min-width: 176px;" aria-label="Tanggal acuan tinjauan">
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile gov-touch-ctrl text-nowrap">Tampilkan</button>
                </form>
            </div>
        </div>

        <!-- Tabel Desktop Rencana Kembali -->
        <div class="ecc-gov-card p-0 overflow-hidden d-none d-md-block">
            <div class="ecc-table-scroll">
                <table class="table ecc-table-gov table-hover mb-0" data-sortable-table="true">
                    <thead>
                        <tr>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="0"><span>Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="1"><span>Prodi / Angkatan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="2"><span>Status Saat Ini</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="3"><span>Mulai Berlaku</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="4"><span>Rencana Kembali</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="5"><span>Lewat</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th>Nomor Keputusan</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rkList)): ?>
                            <tr>
                                <td colspan="8" class="text-center ecc-text-muted-aa py-4">Tidak ada taruna cuti atau skorsing yang melewati tanggal rencana kembali hingga <?= esc(ketarunaan_format_tanggal($tanggalAcuan)) ?>.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rkList as $rk): ?>
                                <tr>
                                    <td data-sort-val="<?= esc(strtolower((string) $rk['nama_lengkap'])) ?>">
                                        <div class="fw-semibold text-dark"><?= esc($rk['nama_lengkap']) ?></div>
                                        <div class="ecc-text-muted-aa small ecc-num"><?= esc($rk['nomor_taruna']) ?></div>
                                    </td>
                                    <td class="small ecc-num" data-sort-val="<?= esc((string) ($rk['kode_prodi'] ?? '')) ?>"><?= esc(($rk['kode_prodi'] ?: '-') . ' / ' . ketarunaan_format_angkatan($rk['tahun_masuk'] ?? null)) ?></td>
                                    <td data-sort-val="<?= esc((string) $rk['kode_status']) ?>"><?= ketarunaan_status_badge((string) $rk['kode_status']) ?></td>
                                    <td class="small ecc-num ecc-text-muted-aa" data-sort-val="<?= esc((string) $rk['mulai_berlaku']) ?>"><?= esc(ketarunaan_format_tanggal((string) $rk['mulai_berlaku'], true)) ?></td>
                                    <td class="small ecc-num fw-semibold text-danger-emphasis" data-sort-val="<?= esc((string) $rk['rencana_kembali']) ?>"><?= esc(ketarunaan_format_tanggal((string) $rk['rencana_kembali'], true)) ?></td>
                                    <td data-sort-val="<?= (int) $rk['hari_lewat'] ?>">
                                        <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle px-3 py-1 fw-semibold ecc-num" style="font-size: 0.75rem;">
                                            <?= (int) $rk['hari_lewat'] ?> hari
                                        </span>
                                    </td>
                                    <td class="small ecc-text-muted-aa"><?= esc($rk['nomor_keputusan'] ?: '-') ?></td>
                                    <td class="text-end">
                                        <a href="<?= esc($rk['detail_url']) ?>" class="btn btn-primary btn-sm ecc-btn-pill btn-tactile">
                                            Tindak Lanjuti
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kartu Mobile Rencana Kembali -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rkList)): ?>
                <div class="ecc-gov-card text-center ecc-text-muted-aa py-4 small">Tidak ada taruna cuti atau skorsing yang melewati tanggal rencana kembali.</div>
            <?php else: ?>
                <?php foreach ($rkList as $rk): ?>
                    <div class="ecc-gov-card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($rk['nama_lengkap']) ?></div>
                                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc($rk['nomor_taruna']) ?> &bull; <?= esc($rk['kode_prodi'] ?: '-') ?></div>
                            </div>
                            <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis border border-danger-subtle px-3 py-1 fw-semibold ecc-num" style="font-size: 0.75rem;">
                                <?= (int) $rk['hari_lewat'] ?> hari
                            </span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <?= ketarunaan_status_badge((string) $rk['kode_status']) ?>
                            <span class="ecc-text-muted-aa small ecc-num">Rencana kembali: <strong class="text-danger-emphasis"><?= esc(ketarunaan_format_tanggal((string) $rk['rencana_kembali'], true)) ?></strong></span>
                        </div>
                        <a href="<?= esc($rk['detail_url']) ?>" class="btn btn-primary btn-sm ecc-btn-pill w-100 gov-touch-ctrl">Tindak Lanjuti</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Tab Jejak Aktivitas ECC 2.0 (GOV-04) -->
        <div class="ecc-gov-card mb-3 py-3">
            <form method="get" action="<?= site_url('ketarunaan/validasi') ?>" class="row g-2 align-items-end gov-filter-form">
                <input type="hidden" name="tab" value="jejak_aktivitas">
                <input type="hidden" name="tanggal_acuan" value="<?= esc($tanggalAcuan) ?>">
                <div class="col-12 col-md-6 col-lg-4">
                    <label for="filterEntitasAudit" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Saring Modul Data</label>
                    <select id="filterEntitasAudit" name="entitas" class="form-select ecc-control-sm" onchange="this.form.submit()">
                        <option value="">Semua Modul Data</option>
                        <?php foreach ($entityOptions as $entCode => $entLabel): ?>
                            <option value="<?= esc($entCode) ?>" <?= $entNow === $entCode ? 'selected' : '' ?>><?= esc($entLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-8 d-flex flex-wrap justify-content-md-end gap-2">
                    <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile gov-touch-ctrl text-nowrap">Terapkan</button>
                    <?php if ($entNow !== ''): ?>
                        <a href="<?= site_url('ketarunaan/validasi?tab=jejak_aktivitas&tanggal_acuan=' . urlencode($tanggalAcuan)) ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile gov-touch-ctrl text-nowrap">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Desktop Jejak Aktivitas -->
        <div class="ecc-gov-card p-0 overflow-hidden d-none d-md-block">
            <div class="p-3 p-md-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Jejak Aktivitas Data</h2>
                    <span class="ecc-text-muted-aa small">Menampilkan 40 perubahan terbaru pada data ketarunaan dan alumni sesuai hak akses peran.</span>
                </div>
                <span class="badge rounded-pill bg-light text-dark border px-3 py-1 ecc-num" style="font-size: 0.75rem;"><?= count($auditTrail) ?> catatan</span>
            </div>
            <div class="ecc-table-scroll">
                <table class="table ecc-table-gov table-hover mb-0" data-sortable-table="true">
                    <thead>
                        <tr>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="0"><span>Tanggal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="1"><span>Pengguna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="2"><span>Aktivitas</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th aria-sort="none"><button type="button" class="gov-sort-btn" data-sort-col="3"><span>Modul Data</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            <th>Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($auditTrail)): ?>
                            <tr>
                                <td colspan="5" class="text-center ecc-text-muted-aa py-4">Belum ada catatan aktivitas untuk ditampilkan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($auditTrail as $log): ?>
                                <tr>
                                    <td class="small ecc-num ecc-text-muted-aa text-nowrap" data-sort-val="<?= esc((string) $log['id']) ?>"><?= esc($log['tanggal']) ?></td>
                                    <td class="fw-semibold text-dark" data-sort-val="<?= esc(strtolower((string) $log['aktor'])) ?>"><?= esc($log['aktor']) ?></td>
                                    <td data-sort-val="<?= esc(strtolower((string) $log['aksi'])) ?>">
                                        <span class="badge rounded-pill bg-light text-dark border px-3 py-1" style="font-size: 0.75rem;">
                                            <?= esc($log['aksi']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-dark" data-sort-val="<?= esc(strtolower((string) $log['modul'])) ?>"><?= esc($log['modul']) ?></td>
                                    <td class="small ecc-num ecc-text-muted-aa"><?= esc($log['referensi']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kartu Mobile Jejak Aktivitas -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($auditTrail)): ?>
                <div class="ecc-gov-card text-center ecc-text-muted-aa py-4 small">Belum ada catatan aktivitas untuk ditampilkan.</div>
            <?php else: ?>
                <?php foreach ($auditTrail as $log): ?>
                    <div class="ecc-gov-card p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <span class="fw-bold text-dark"><?= esc($log['aktor']) ?></span>
                            <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc($log['tanggal']) ?></span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-1" style="font-size: 0.75rem;"><?= esc($log['aksi']) ?></span>
                            <span class="small text-dark"><?= esc($log['modul']) ?></span>
                        </div>
                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">Ref: <?= esc($log['referensi']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<!-- Modal Tunggal Pengembalian Validasi (Wajib Catatan Perbaikan) -->
<div class="modal fade" id="modalKembalikanValidasi" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="" id="formKembalikanValidasi">
                <?= csrf_field() ?>
                <input type="hidden" name="keputusan" value="perlu_perbaikan">
                <div class="modal-header border-bottom">
                    <h2 class="h5 modal-title fw-bold text-dark mb-0" id="modalKembalikanLabel">Kembalikan untuk Perbaikan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="small ecc-text-muted-aa mb-3" id="modalKembalikanSubjek"></p>
                    <label for="catatanPerbaikanInput" class="form-label small fw-semibold text-dark">Catatan Perbaikan <span class="text-danger">*</span></label>
                    <textarea id="catatanPerbaikanInput" name="catatan_validasi" class="form-control rounded-3" rows="3" required placeholder="Tuliskan bagian yang perlu dilengkapi atau diperbaiki..."></textarea>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-3 gov-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold btn-tactile gov-touch-ctrl">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var rkInput = document.getElementById('rkTanggalAcuan');
        if (rkInput && typeof flatpickr !== 'undefined') {
            flatpickr(rkInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                defaultDate: '<?= esc($tanggalAcuan) ?>',
                locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default'
            });
        }

        // Sorting tabel client-side dengan atribut aria-sort
        document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
            var headers = table.querySelectorAll('thead th');
            headers.forEach(function (th) {
                var btn = th.querySelector('.gov-sort-btn');
                if (!btn) return;
                btn.addEventListener('click', function () {
                    var colIdx = parseInt(btn.getAttribute('data-sort-col') || '0', 10);
                    var currentSort = th.getAttribute('aria-sort') || 'none';
                    var nextSort = currentSort === 'ascending' ? 'descending' : 'ascending';

                    headers.forEach(function (h) {
                        if (h.hasAttribute('aria-sort')) {
                            h.setAttribute('aria-sort', 'none');
                            var icon = h.querySelector('.sort-indicator');
                            if (icon) icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                        }
                    });
                    th.setAttribute('aria-sort', nextSort);
                    var activeIcon = th.querySelector('.sort-indicator');
                    if (activeIcon) {
                        activeIcon.className = nextSort === 'ascending'
                            ? 'bi bi-sort-up text-primary sort-indicator'
                            : 'bi bi-sort-down text-primary sort-indicator';
                    }

                    var tbody = table.querySelector('tbody');
                    if (!tbody) return;
                    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                    if (rows.length <= 1 && rows[0] && rows[0].querySelector('td[colspan]')) return;

                    rows.sort(function (a, b) {
                        var cellA = a.children[colIdx];
                        var cellB = b.children[colIdx];
                        var valA = (cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent || '') : '').trim();
                        var valB = (cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent || '') : '').trim();
                        var numA = parseFloat(valA);
                        var numB = parseFloat(valB);
                        var cmp = (!isNaN(numA) && !isNaN(numB) && String(numA) === valA && String(numB) === valB)
                            ? (numA - numB)
                            : valA.localeCompare(valB, 'id', { numeric: true, sensitivity: 'base' });
                        return nextSort === 'ascending' ? cmp : -cmp;
                    });
                    rows.forEach(function (r) { tbody.appendChild(r); });
                });
            });
        });

        // Anti double-submit pada form POST & reset saat pageshow
        document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (e.defaultPrevented) return;
                if (form.dataset.submitting === '1') {
                    e.preventDefault();
                    return;
                }
                form.dataset.submitting = '1';
                form.querySelectorAll('button[type="submit"]').forEach(function (b) { b.classList.add('disabled'); });
            });
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form[data-submitting="1"]').forEach(function (form) {
                delete form.dataset.submitting;
                form.querySelectorAll('button[type="submit"]').forEach(function (b) {
                    b.classList.remove('disabled');
                    b.disabled = false;
                });
            });
        });
    });

    function konfirmasiSetujuValidasi(btn, ev) {
        var form = btn.closest('form');
        if (!form) return true;
        if (form.dataset.submitting === '1') {
            ev.preventDefault();
            return false;
        }
        var judul = btn.getAttribute('data-judul') || 'pengajuan ini';

        if (typeof Swal !== 'undefined') {
            ev.preventDefault();
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: 'Pengajuan "' + judul + '" akan disahkan dan langsung memperbarui data aktif.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-4 shadow border-0' }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.submitting = '1';
                    form.submit();
                }
            });
            return false;
        }

        if (confirm('Setujui pengajuan "' + judul + '"?')) {
            form.dataset.submitting = '1';
            return true;
        }
        return false;
    }

    function bukaModalKembalikan(actionUrl, judul) {
        var form = document.getElementById('formKembalikanValidasi');
        var subjek = document.getElementById('modalKembalikanSubjek');
        var input = document.getElementById('catatanPerbaikanInput');
        if (!form) return;

        form.action = actionUrl;
        delete form.dataset.submitting;
        if (subjek) {
            subjek.textContent = 'Pengajuan: ' + judul;
        }
        if (input) {
            input.value = '';
        }

        var modalEl = document.getElementById('modalKembalikanValidasi');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }
</script>
<?= $this->endSection() ?>
