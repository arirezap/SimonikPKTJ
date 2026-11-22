<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dashboard Ketarunaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-tile {
        border-radius: 12px;
        padding: 12px 16px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid transparent;
        position: relative;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    .tile-ink-aktif  { color: var(--ecc-ink-aktif, #0a58ca); }
    .tile-ink-do     { color: var(--ecc-ink-crimson, #b91c1c); }
    .tile-ink-lulus  { color: var(--ecc-ink-violet, #7c3aed); }

    .metric-icon-amber { color: var(--ecc-ink-amber, #b45309); }
    .metric-icon-red   { color: var(--ecc-ink-crimson, #b91c1c); }
    .metric-icon-green { color: var(--ecc-ink-emerald, #047857); }
    .metric-icon-blue  { color: var(--ecc-primary-dark, #1e40af); }

    .quick-icon-sky    { color: var(--ecc-ink-sky-dark, #0369a1); }
    .quick-icon-amber  { color: var(--ecc-ink-amber, #b45309); }

    .metric-card-ops {
        border-radius: 16px;
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        padding: 16px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .icon-box-ecc {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .bento-dark-action {
        background: var(--ecc-navy-header, #0f172a);
        color: var(--ecc-surface-white, #ffffff);
        border-radius: 16px;
        padding: 24px;
    }

    .btn-quick-ecc {
        background: var(--ecc-surface-white, #ffffff);
        border: 1px solid var(--ecc-border-slate, #e2e8f0);
        border-radius: 12px;
        padding: 12px 16px;
        text-align: left;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        text-decoration: none;
        color: var(--ecc-navy-header, #0f172a);
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .status-tile:focus-visible,
    .btn-quick-ecc:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .status-tile:active,
    .btn-quick-ecc:active {
        transform: scale(0.985);
        transition-duration: 0.12s;
    }

    .dashboard-filter-scope {
        transition: opacity 0.15s ease;
    }
    .dashboard-filter-scope.is-filtering {
        opacity: 0.65;
        pointer-events: none;
    }

    @media (hover: hover) {
        .status-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
        }
        .metric-card-ops:hover {
            transform: translateY(-2px);
        }
        .btn-quick-ecc:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--ecc-ink-sky, #0284c7);
            transform: translateY(-1px);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .status-tile,
        .status-tile:hover,
        .status-tile:active,
        .metric-card-ops,
        .metric-card-ops:hover,
        .btn-quick-ecc,
        .btn-quick-ecc:hover,
        .btn-quick-ecc:active,
        .dashboard-filter-scope {
            transition: none !important;
            transform: none !important;
        }
    }

    .table-sebaran-sticky th:first-child,
    .table-sebaran-sticky td:first-child {
        position: sticky;
        left: 0;
        z-index: 1;
    }
    .table-sebaran-sticky thead th:first-child {
        background-color: var(--bs-table-bg, #f8fafc);
        z-index: 2;
    }
    .table-sebaran-sticky tbody td:first-child {
        background-color: #ffffff;
        box-shadow: 4px 0 8px -4px rgba(15, 23, 42, 0.08);
    }

    @media (max-width: 767.98px) {
        .dashboard-filter-bar {
            width: 100%;
        }
        .dashboard-filter-bar > .form-select,
        .dashboard-filter-bar > .input-group,
        .dashboard-filter-bar > .btn,
        .dashboard-filter-bar .form-control,
        .dashboard-filter-bar .input-group-text {
            min-height: 44px;
            height: auto !important;
            font-size: 1rem;
        }
        #linkBukaTaruna,
        #linkLihatTaruna,
        .bento-dark-action a,
        .btn-quick-ecc,
        .btn-row-detail {
            min-height: 44px !important;
        }
        .btn-row-detail {
            min-width: 44px !important;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-filter-bar > * {
            width: 100% !important;
            min-width: 0 !important;
        }
        .table-mobile-cards thead {
            display: none;
        }
        .table-mobile-cards tbody,
        .table-mobile-cards tr,
        .table-mobile-cards td {
            display: block;
            width: 100%;
        }
        .table-mobile-cards tbody tr {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            background-color: #ffffff;
        }
        .table-mobile-cards tbody tr:last-child {
            margin-bottom: 0;
        }
        .table-mobile-cards tbody td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 0;
            padding: 4px 0;
            text-align: right !important;
        }
        .table-mobile-cards tbody td::before {
            content: attr(data-label);
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-align: left;
            flex-shrink: 0;
        }
        .table-mobile-cards tbody td.table-empty-row {
            display: block;
            text-align: center !important;
            padding: 16px 8px;
        }
        .table-mobile-cards tbody td.table-empty-row::before {
            content: none;
        }
    }

    @media print {
        .dashboard-filter-bar,
        #linkBukaTaruna,
        #linkLihatTaruna,
        .btn-row-detail {
            display: none !important;
        }
        .bento-card,
        .metric-card-ops,
        .status-tile,
        .bento-dark-action {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            break-inside: avoid;
        }
        .bento-dark-action {
            background: #ffffff !important;
            color: #0f172a !important;
        }
        .bento-dark-action .text-white,
        .bento-dark-action .text-white-50 {
            color: #0f172a !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">

    <!-- 1. TOP HEADER & FILTER TOOLBAR -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Dashboard Ketarunaan</h1>
            <p class="text-muted mb-0 small">Ringkasan status pendidikan, capaian, dan kualitas data</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 dashboard-filter-bar">
            <select id="filterProdi" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="width: auto; min-width: 176px; height: 36px;" aria-label="Filter Program Studi">
                <option value="">Semua Program Studi</option>
                <?php foreach ($prodi_list as $p): ?>
                    <option value="<?= esc($p['id']) ?>" <?= ($prodi_id == $p['id']) ? 'selected' : '' ?>>
                        <?= esc($p['kode_prodi']) ?> - <?= esc($p['nama_prodi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="filterAngkatan" class="form-select form-select-sm rounded-3 border-secondary-subtle" style="width: auto; min-width: 144px; height: 36px;" aria-label="Filter Angkatan">
                <option value="">Semua Angkatan</option>
                <?php foreach ($angkatan_list as $a): ?>
                    <option value="<?= esc($a['id']) ?>" <?= ($angkatan_id == $a['id']) ? 'selected' : '' ?>>
                        Angkatan <?= esc(ketarunaan_format_angkatan($a)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="input-group input-group-sm" style="width: auto; min-width: 224px;">
                <span class="input-group-text bg-white border-secondary-subtle text-primary"><i class="bi bi-calendar3"></i></span>
                <input type="text" id="filterTanggalAcuan" class="form-control form-control-sm border-secondary-subtle bg-white" value="<?= esc($tanggal_acuan) ?>" placeholder="Tanggal acuan" style="height: 36px;" aria-label="Tanggal acuan">
            </div>

            <?php if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessLaporanModule()): ?>
            <button type="button" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile shadow-sm" style="height: 36px;" onclick="unduhLaporanRingkasan()">
                <i class="bi bi-download"></i> <span>Unduh Laporan</span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div id="dashboardFilterScope" class="dashboard-filter-scope">
    <!-- 2. STATUS PENDIDIKAN TARUNA (6 STATUS PILAR) -->
    <?php
    $tarunaBaseQuery = [];
    if (!empty($prodi_id)) {
        $tarunaBaseQuery['prodi_id'] = (int) $prodi_id;
    }
    if (!empty($angkatan_id)) {
        $tarunaBaseQuery['angkatan_id'] = (int) $angkatan_id;
    }
    $buildTarunaUrl = static function (?string $status = null) use ($tarunaBaseQuery): string {
        $params = $tarunaBaseQuery;
        if ($status !== null && $status !== '') {
            $params['status'] = $status;
        }
        $qs = http_build_query($params);
        return site_url('ketarunaan/taruna') . ($qs !== '' ? '?' . $qs : '');
    };
    $statusCatalog = ketarunaan_status_catalog();
    $statIdMap = [
        'aktif'             => 'statAktif',
        'skorsing'          => 'statSkorsing',
        'cuti'              => 'statCuti',
        'mengundurkan_diri' => 'statMundur',
        'drop_out'          => 'statDo',
        'lulus'             => 'statLulus',
    ];
    ?>
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-dark mb-0">Status Pendidikan Taruna</h2>
                <span class="text-muted small">Posisi status pada tanggal acuan: <strong class="text-dark" id="lblTanggalAcuan"><?= esc(ketarunaan_format_tanggal($tanggal_acuan)) ?></strong></span>
            </div>
            <a id="linkBukaTaruna" href="<?= esc($buildTarunaUrl()) ?>" class="btn btn-light btn-sm rounded-3 px-3 fw-semibold border d-inline-flex align-items-center text-nowrap btn-tactile" style="min-height: 32px; color: #1e40af;">
                Buka Database Taruna
            </a>
        </div>

        <div class="row g-2 mb-3">
            <?php foreach ($statusCatalog as $kode => $meta): ?>
                <?php $statDomId = $statIdMap[$kode] ?? 'stat_' . $kode; ?>
                <div class="col-6 col-sm-4 col-xxl-2">
                    <a href="<?= esc($buildTarunaUrl($kode)) ?>" class="status-tile h-100" data-status-target="<?= esc($kode) ?>" style="background-color: <?= esc($meta['bg_soft']) ?>; border-color: <?= esc($meta['border_color']) ?>;">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <span class="fw-bold small lh-sm" style="color: <?= esc($meta['text_color'] ?? $meta['color']) ?>;"><?= esc($meta['label']) ?></span>
                            <span class="rounded-circle d-inline-block flex-shrink-0 mt-1" style="width: 8px; height: 8px; background-color: <?= esc($meta['color']) ?>;"></span>
                        </div>
                        <div class="mt-auto">
                            <div class="h4 fw-bold text-dark mb-0 tabular-nums" id="<?= esc($statDomId) ?>"><?= number_format($status_counts[$kode] ?? 0) ?></div>
                            <span class="small" style="color: #475569;">taruna</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Baris Ringkasan Agregat -->
        <div class="pt-3 border-top d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 gap-md-4 text-muted small">
            <div>Dalam pendidikan: <strong class="tile-ink-aktif tabular-nums" id="statDalamPendidikan"><?= number_format($dalam_pendidikan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Berhenti pendidikan: <strong class="tile-ink-do tabular-nums" id="statBerhentiPendidikan"><?= number_format($berhenti_pendidikan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Lulus: <strong class="tabular-nums tile-ink-lulus" id="statLulusan"><?= number_format($lulusan) ?></strong></div>
            <span class="d-none d-md-inline">•</span>
            <div>Seluruh rekaman: <strong class="text-dark tabular-nums" id="statTotalRekaman"><?= number_format($total_rekaman) ?></strong></div>
        </div>
    </div>

    <!-- 3. BARIS 4 KARTU METRIK OPERASIONAL -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xxl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-warning-subtle metric-icon-amber">
                    <i class="bi bi-trophy-fill" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <span class="text-muted small fw-medium d-block">Prestasi Tervalidasi</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statTotalPrestasi"><?= number_format($total_prestasi) ?></span> <span class="fs-6 fw-normal text-muted">prestasi</span>
                    </div>
                    <span class="text-muted small d-block">
                        <strong class="text-dark tabular-nums" id="statTarunaBerprestasi"><?= number_format($taruna_berprestasi) ?></strong> taruna berprestasi
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xxl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-danger-subtle metric-icon-red">
                    <i class="bi bi-shield-fill-exclamation" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <span class="text-muted small fw-medium d-block">Pelanggaran Terbukti</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statPelanggaranTerbukti"><?= number_format($pelanggaran_terbukti) ?></span> <span class="fs-6 fw-normal text-muted">kasus</span>
                    </div>
                    <span class="text-muted small d-block">
                        <span id="statPelanggaranSelesai" class="tabular-nums"><?= $pelanggaran_selesai ?></span> selesai • <span id="statPelanggaranProses" class="tabular-nums"><?= $pelanggaran_proses ?></span> proses
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xxl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-success-subtle metric-icon-green">
                    <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <span class="text-muted small fw-medium d-block">Penerima Beasiswa</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statPenerimaBeasiswa"><?= number_format($penerima_beasiswa) ?></span> <span class="fs-6 fw-normal text-muted">taruna</span>
                    </div>
                    <span class="text-muted small d-block">
                        <strong class="text-dark tabular-nums" id="statProgramBeasiswa"><?= $program_beasiswa_aktif ?></strong> program aktif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xxl-3">
            <div class="metric-card-ops h-100 d-flex align-items-center gap-3">
                <div class="icon-box-ecc bg-primary-subtle metric-icon-blue">
                    <i class="bi bi-briefcase-fill" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <span class="text-muted small fw-medium d-block">Lowongan Aktif</span>
                    <div class="h3 fw-bold text-dark mb-0 tabular-nums">
                        <span id="statLowonganAktif"><?= number_format($lowongan_aktif) ?></span> <span class="fs-6 fw-normal text-muted">lowongan</span>
                    </div>
                    <span class="text-danger small d-block">
                        <strong class="tabular-nums" id="statLowonganSegera"><?= $lowongan_segera_berakhir ?></strong> segera berakhir
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. BENTO GRID TENGAH (3 KOLOM SEIMBANG) -->
    <div class="row g-4 mb-4">
        <!-- 4.1 Tabel Sebaran Status per Prodi -->
        <div class="col-12 col-xl-6 col-xxl-5">
            <div class="bento-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-1">Sebaran Status per Program Studi</h2>
                    <p class="text-muted mb-3 small">Jumlah taruna berdasarkan status pendidikan</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 table-sebaran-sticky">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>Prodi</th>
                                    <th class="text-center">Aktif</th>
                                    <th class="text-center">Skorsing</th>
                                    <th class="text-center">Cuti</th>
                                    <th class="text-center">Mundur</th>
                                    <th class="text-center">DO</th>
                                    <th class="text-center">Lulus</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySebaranProdi">
                                <?php foreach ($sebaran_prodi as $sp): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= esc($sp['kode_prodi']) ?></td>
                                        <td class="text-center tabular-nums fw-medium tile-ink-aktif"><?= number_format($sp['aktif']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['skorsing']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['cuti']) ?></td>
                                        <td class="text-center tabular-nums"><?= number_format($sp['mundur']) ?></td>
                                        <td class="text-center tabular-nums tile-ink-do"><?= number_format($sp['do_count']) ?></td>
                                        <td class="text-center tabular-nums fw-medium tile-ink-lulus"><?= number_format($sp['lulus']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-3 border-top mt-3 text-end">
                    <a id="linkLihatTaruna" href="<?= esc($buildTarunaUrl()) ?>" class="text-decoration-none fw-semibold small text-primary d-inline-flex align-items-center gap-1">
                        <span>Lihat Database Taruna</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4.2 Widget Kualitas Data (Donut Chart) -->
        <div class="col-12 col-md-6 col-xl-6 col-xxl-4">
            <div class="bento-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-1">Kualitas Data</h2>
                    <p class="text-muted mb-3 small">Tingkat kelengkapan dan validasi data ketarunaan</p>

                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-4 my-2">
                        <div class="flex-shrink-0" style="width: 120px; height: 120px; position: relative;">
                            <canvas id="kualitasDataChart"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle text-center" style="pointer-events: none;">
                                <div class="fw-bold text-dark fs-5 tabular-nums" id="lblPersenLengkapCenter"><?= (int) $kualitas_data['persen_lengkap'] ?>%</div>
                                <div class="text-muted small">Lengkap</div>
                            </div>
                        </div>

                        <div class="w-100 flex-grow-1">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-dark fw-medium">Kelengkapan</span>
                                    <span class="fw-bold text-primary tabular-nums" id="lblPersenLengkap"><?= (int) $kualitas_data['persen_lengkap'] ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div id="barPersenLengkap" class="progress-bar bg-primary" role="progressbar" aria-label="Persentase kelengkapan data" aria-valuenow="<?= (int) $kualitas_data['persen_lengkap'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= (int) $kualitas_data['persen_lengkap'] ?>%;"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="text-dark fw-medium">Tervalidasi</span>
                                    <span class="fw-bold text-success tabular-nums" id="lblPersenTervalidasi"><?= (int) $kualitas_data['persen_tervalidasi'] ?>%</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div id="barPersenTervalidasi" class="progress-bar bg-success" role="progressbar" aria-label="Persentase data tervalidasi" aria-valuenow="<?= (int) $kualitas_data['persen_tervalidasi'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= (int) $kualitas_data['persen_tervalidasi'] ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top mt-3 text-muted small">
                    Mencakup kelengkapan profil dan validasi data
                </div>
            </div>
        </div>

        <!-- 4.3 Widget Perlu Tindak Lanjut (Dark Navy Bento) -->
        <div class="col-12 col-md-6 col-xl-12 col-xxl-3">
            <div class="bento-dark-action h-100 d-flex flex-column justify-content-between">
                <div>
                    <h2 class="h6 fw-bold text-white mb-1">Perlu Tindak Lanjut</h2>
                    <p class="text-white-50 mb-3 small">Data yang memerlukan perhatian</p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <a href="<?= site_url('ketarunaan/validasi?status=diajukan') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Menunggu validasi</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-warning text-dark fw-bold tabular-nums px-3 py-1" id="statMenungguValidasi"><?= (int) $antrean_validasi['menunggu'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>

                        <a href="<?= site_url('ketarunaan/validasi?status=perlu_perbaikan') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Perlu perbaikan</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-danger fw-bold tabular-nums px-3 py-1" id="statPerluPerbaikan"><?= (int) $antrean_validasi['perlu_perbaikan'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>

                        <a href="<?= site_url('ketarunaan/validasi?tab=rencana_kembali') ?>" class="text-decoration-none text-white d-flex align-items-center justify-content-between p-2 rounded-3 bg-white bg-opacity-10 btn-tactile">
                            <span class="small">Rencana kembali perlu ditinjau</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-info text-dark fw-bold tabular-nums px-3 py-1" id="statRencanaKembali"><?= (int) $antrean_validasi['rencana_kembali'] ?></span>
                                <i class="bi bi-chevron-right text-white-50 small"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="<?= site_url('ketarunaan/validasi') ?>" class="btn btn-warning w-100 rounded-3 fw-bold btn-sm text-dark d-flex align-items-center justify-content-center gap-2 text-nowrap btn-tactile shadow-sm" style="height: 36px;">
                    <span>Buka Antrean Validasi</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    </div>

    <!-- 5. BENTO GRID BAWAH (2 KOLOM SEIMBANG) -->
    <div class="row g-4">
        <!-- 5.1 Aktivitas Terbaru -->
        <div class="col-12 col-xl-7">
            <div class="bento-card p-3 p-md-4 h-100">
                <h2 class="h6 fw-bold text-dark mb-1">Aktivitas Terbaru</h2>
                <p class="text-muted mb-3 small">Pembaruan status, prestasi, dan pengajuan terkini</p>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 table-mobile-cards">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th>Modul</th>
                                <th>Aktivitas</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($aktivitas_terbaru)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4 small table-empty-row">
                                        Belum ada aktivitas perubahan status atau validasi.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($aktivitas_terbaru as $act): ?>
                                    <tr>
                                        <td class="fw-bold text-dark small" data-label="Modul"><?= esc($act['modul']) ?></td>
                                        <td class="small" data-label="Aktivitas"><?= esc($act['aktivitas']) ?></td>
                                        <td data-label="Status">
                                            <span class="badge rounded-pill px-3 py-1 text-nowrap <?= esc($act['badge']) ?>">
                                                <?= esc($act['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end" data-label="Aksi">
                                            <a href="<?= esc($act['url'] ?? site_url('ketarunaan/taruna')) ?>" class="btn btn-light btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center btn-tactile btn-row-detail text-muted" style="width: 32px; height: 32px;" aria-label="Buka detail aktivitas">
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5.2 Akses Cepat -->
        <?php
        $quickLinks = [];
        if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canManageTaruna()) {
            $quickLinks = [
                [
                    'url'   => site_url('ketarunaan/taruna?modal=tambah'),
                    'icon'  => 'bi-plus-circle-fill text-primary',
                    'title' => 'Tambah Data',
                    'desc'  => 'Input data ketarunaan',
                ],
                [
                    'url'   => site_url('ketarunaan/taruna/impor'),
                    'icon'  => 'bi-file-earmark-excel-fill text-success',
                    'title' => 'Impor Excel',
                    'desc'  => 'Unggah data massal',
                ],
                [
                    'url'   => site_url('ketarunaan/taruna?status=aktif'),
                    'icon'  => 'bi-person-gear quick-icon-sky',
                    'title' => 'Ubah Status',
                    'desc'  => 'Pilih taruna & status',
                ],
                [
                    'url'   => site_url('ketarunaan/taruna'),
                    'icon'  => 'bi-person-badge-fill quick-icon-amber',
                    'title' => 'Profil Taruna',
                    'desc'  => 'Identitas & riwayat',
                ],
            ];
        } else {
            $quickLinks[] = [
                'url'   => site_url('ketarunaan/taruna'),
                'icon'  => 'bi-people-fill text-primary',
                'title' => 'Database Taruna',
                'desc'  => 'Identitas & riwayat',
            ];
            if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessValidasiModule()) {
                $quickLinks[] = [
                    'url'   => site_url('ketarunaan/validasi'),
                    'icon'  => 'bi-check-circle-fill text-success',
                    'title' => 'Validasi Data',
                    'desc'  => 'Antrean persetujuan',
                ];
            }
            if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessLaporanModule()) {
                $quickLinks[] = [
                    'url'   => site_url('ketarunaan/laporan'),
                    'icon'  => 'bi-file-earmark-bar-graph-fill quick-icon-sky',
                    'title' => 'Laporan Resmi',
                    'desc'  => 'Pratinjau & ekspor',
                ];
            }
            if (\App\Libraries\Ketarunaan\KetarunaanPolicy::canAccessAlumniModule()) {
                $quickLinks[] = [
                    'url'   => site_url('ketarunaan/alumni'),
                    'icon'  => 'bi-mortarboard text-primary',
                    'title' => 'Direktori Alumni',
                    'desc'  => 'Profil dasar lulusan',
                ];
            }
            $quickLinks[] = [
                'url'   => site_url('ketarunaan/lowongan'),
                'icon'  => 'bi-briefcase-fill quick-icon-amber',
                'title' => 'Lowongan Kerja',
                'desc'  => 'Bursa karier aktif',
            ];
            $quickLinks = array_slice($quickLinks, 0, 4);
        }
        ?>
        <div class="col-12 col-xl-5">
            <div class="bento-card p-3 p-md-4 h-100">
                <h2 class="h6 fw-bold text-dark mb-1">Akses Cepat</h2>
                <p class="text-muted mb-3 small">Menu yang sering digunakan</p>

                <div class="row g-2">
                    <?php foreach ($quickLinks as $ql): ?>
                        <div class="col-12 col-sm-6">
                            <a href="<?= esc($ql['url']) ?>" class="btn-quick-ecc h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi <?= esc($ql['icon']) ?> fs-5" aria-hidden="true"></i>
                                    <div>
                                        <div class="fw-bold small"><?= esc($ql['title']) ?></div>
                                        <div class="text-muted small"><?= esc($ql['desc']) ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. FOOTNOTE STRIP -->
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-muted mt-4 pt-3 border-top small">
        <div>
            Status pendidikan terpisah dari status validasi • <?= esc(strtolower($data_mode['label'] ?? ketarunaan_data_mode_info()['label'])) ?>
        </div>
        <div class="mt-1 mt-md-0">
            Evidence Command Center (ECC) | Politeknik Keselamatan Transportasi Jalan
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>" defer></script>
<script>
    let chartKualitasData = null;
    let activeFilterController = null;

    function prefersReducedMotion() {
        return typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#filterTanggalAcuan", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            defaultDate: "<?= esc($tanggal_acuan) ?>",
            onChange: function() {
                applyFilter();
            }
        });

        initKualitasDataChart(<?= (int) $kualitas_data['persen_lengkap'] ?>);

        document.getElementById('filterProdi').addEventListener('change', applyFilter);
        document.getElementById('filterAngkatan').addEventListener('change', applyFilter);
    });

    function initKualitasDataChart(persenLengkap) {
        const ctx = document.getElementById('kualitasDataChart');
        if (!ctx || typeof Chart === 'undefined') return;

        if (chartKualitasData) {
            chartKualitasData.destroy();
        }

        const reduceMotion = prefersReducedMotion();

        chartKualitasData = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [persenLengkap, 100 - persenLengkap],
                    backgroundColor: ['#0284c7', '#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: reduceMotion ? 0 : 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                animation: reduceMotion ? false : { duration: 280 },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }

    function escapeDashboardHtml(val) {
        if (val === null || val === undefined) return '';
        return String(val)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatIdNumber(val) {
        const num = Number(val || 0);
        return (Number.isFinite(num) ? num : 0).toLocaleString('id-ID');
    }

    function showDashboardToast(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
            return;
        }
        window.alert(message);
    }

    function syncTarunaFilterLinks(prodiId, angkatanId) {
        const baseUrl = '<?= site_url('ketarunaan/taruna') ?>';
        const buildUrl = (status) => {
            const params = new URLSearchParams();
            if (prodiId) params.set('prodi_id', prodiId);
            if (angkatanId) params.set('angkatan_id', angkatanId);
            if (status) params.set('status', status);
            const qs = params.toString();
            return qs ? `${baseUrl}?${qs}` : baseUrl;
        };

        document.querySelectorAll('.status-tile[data-status-target]').forEach((el) => {
            el.setAttribute('href', buildUrl(el.getAttribute('data-status-target')));
        });

        const bukaLink = document.getElementById('linkBukaTaruna');
        if (bukaLink) bukaLink.setAttribute('href', buildUrl(''));

        const lihatLink = document.getElementById('linkLihatTaruna');
        if (lihatLink) lihatLink.setAttribute('href', buildUrl(''));
    }

    function applyFilter() {
        const prodiId = document.getElementById('filterProdi').value;
        const angkatanId = document.getElementById('filterAngkatan').value;
        const tanggalAcuan = document.getElementById('filterTanggalAcuan').value;
        const csrfMeta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const filterScope = document.getElementById('dashboardFilterScope');

        if (activeFilterController) {
            activeFilterController.abort();
        }
        const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
        activeFilterController = controller;

        if (filterScope) {
            filterScope.classList.add('is-filtering');
            filterScope.setAttribute('aria-busy', 'true');
        }

        const formData = new FormData();
        formData.append('prodi_id', prodiId);
        formData.append('angkatan_id', angkatanId);
        formData.append('tanggal_acuan', tanggalAcuan);
        formData.append('<?= csrf_token() ?>', csrfToken);

        fetch('<?= site_url('ketarunaan/dashboard/filter-data') ?>', {
            method: 'POST',
            body: formData,
            signal: controller ? controller.signal : undefined,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat ringkasan.');
            }
            return response.json();
        })
        .then(res => {
            if (res.csrf_hash && csrfMeta) {
                csrfMeta.setAttribute('content', res.csrf_hash);
            }
            if (res.status !== 'success' || !res.data) {
                showDashboardToast('Gagal memuat ringkasan. Silakan coba lagi.');
                return;
            }

            const stats = res.data;
            document.getElementById('statAktif').textContent = formatIdNumber(stats.status_counts.aktif);
            document.getElementById('statSkorsing').textContent = formatIdNumber(stats.status_counts.skorsing);
            document.getElementById('statCuti').textContent = formatIdNumber(stats.status_counts.cuti);
            document.getElementById('statMundur').textContent = formatIdNumber(stats.status_counts.mengundurkan_diri);
            document.getElementById('statDo').textContent = formatIdNumber(stats.status_counts.drop_out);
            document.getElementById('statLulus').textContent = formatIdNumber(stats.status_counts.lulus);

            document.getElementById('statDalamPendidikan').textContent = formatIdNumber(stats.dalam_pendidikan);
            document.getElementById('statBerhentiPendidikan').textContent = formatIdNumber(stats.berhenti_pendidikan);
            document.getElementById('statLulusan').textContent = formatIdNumber(stats.lulusan);
            document.getElementById('statTotalRekaman').textContent = formatIdNumber(stats.total_rekaman);

            document.getElementById('statTotalPrestasi').textContent = formatIdNumber(stats.total_prestasi);
            document.getElementById('statTarunaBerprestasi').textContent = formatIdNumber(stats.taruna_berprestasi);
            document.getElementById('statPelanggaranTerbukti').textContent = formatIdNumber(stats.pelanggaran_terbukti);
            document.getElementById('statPelanggaranSelesai').textContent = formatIdNumber(stats.pelanggaran_selesai);
            document.getElementById('statPelanggaranProses').textContent = formatIdNumber(stats.pelanggaran_proses);
            document.getElementById('statPenerimaBeasiswa').textContent = formatIdNumber(stats.penerima_beasiswa);
            const pb = document.getElementById('statProgramBeasiswa');
            if (pb && stats.program_beasiswa_aktif !== undefined) {
                pb.textContent = formatIdNumber(stats.program_beasiswa_aktif);
            }
            document.getElementById('statLowonganAktif').textContent = formatIdNumber(stats.lowongan_aktif);
            document.getElementById('statLowonganSegera').textContent = formatIdNumber(stats.lowongan_segera_berakhir);

            if (stats.antrean_validasi) {
                const mv = document.getElementById('statMenungguValidasi');
                const pp = document.getElementById('statPerluPerbaikan');
                const rk = document.getElementById('statRencanaKembali');
                if (mv) mv.textContent = formatIdNumber(stats.antrean_validasi.menunggu);
                if (pp) pp.textContent = formatIdNumber(stats.antrean_validasi.perlu_perbaikan);
                if (rk) rk.textContent = formatIdNumber(stats.antrean_validasi.rencana_kembali);
            }

            if (stats.kualitas_data) {
                const rawLengkap = Number(stats.kualitas_data.persen_lengkap ?? 0);
                const rawValid = Number(stats.kualitas_data.persen_tervalidasi ?? 0);
                const persenLengkap = Math.min(100, Math.max(0, Math.round(isNaN(rawLengkap) ? 0 : rawLengkap)));
                const persenTervalidasi = Math.min(100, Math.max(0, Math.round(isNaN(rawValid) ? 0 : rawValid)));

                const lblCenter = document.getElementById('lblPersenLengkapCenter');
                const lblLengkap = document.getElementById('lblPersenLengkap');
                const lblValid = document.getElementById('lblPersenTervalidasi');
                const barLengkap = document.getElementById('barPersenLengkap');
                const barValid = document.getElementById('barPersenTervalidasi');

                if (lblCenter) lblCenter.textContent = `${persenLengkap}%`;
                if (lblLengkap) lblLengkap.textContent = `${persenLengkap}%`;
                if (lblValid) lblValid.textContent = `${persenTervalidasi}%`;
                if (barLengkap) {
                    barLengkap.style.width = `${persenLengkap}%`;
                    barLengkap.setAttribute('aria-valuenow', String(persenLengkap));
                }
                if (barValid) {
                    barValid.style.width = `${persenTervalidasi}%`;
                    barValid.setAttribute('aria-valuenow', String(persenTervalidasi));
                }
                if (chartKualitasData && chartKualitasData.data && Array.isArray(chartKualitasData.data.datasets) && chartKualitasData.data.datasets[0]) {
                    chartKualitasData.data.datasets[0].data = [persenLengkap, 100 - persenLengkap];
                    chartKualitasData.update(prefersReducedMotion() ? 'none' : undefined);
                }
            }

            if (Array.isArray(stats.sebaran_prodi)) {
                const tbody = document.getElementById('tbodySebaranProdi');
                tbody.innerHTML = stats.sebaran_prodi.map(sp => `
                    <tr>
                        <td class="fw-bold text-dark">${escapeDashboardHtml(sp.kode_prodi)}</td>
                        <td class="text-center tabular-nums fw-medium tile-ink-aktif">${formatIdNumber(sp.aktif)}</td>
                        <td class="text-center tabular-nums">${formatIdNumber(sp.skorsing)}</td>
                        <td class="text-center tabular-nums">${formatIdNumber(sp.cuti)}</td>
                        <td class="text-center tabular-nums">${formatIdNumber(sp.mundur)}</td>
                        <td class="text-center tabular-nums tile-ink-do">${formatIdNumber(sp.do_count)}</td>
                        <td class="text-center tabular-nums fw-medium tile-ink-lulus">${formatIdNumber(sp.lulus)}</td>
                    </tr>
                `).join('');
            }

            const tglMatch = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(tanggalAcuan || '').trim());
            if (tglMatch) {
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const monthIdx = parseInt(tglMatch[2], 10) - 1;
                if (monthIdx >= 0 && monthIdx < 12) {
                    const lblTgl = document.getElementById('lblTanggalAcuan');
                    if (lblTgl) {
                        lblTgl.textContent = `${tglMatch[3]} ${months[monthIdx]} ${tglMatch[1]}`;
                    }
                }
            }

            syncTarunaFilterLinks(prodiId, angkatanId);

            if (window.history && typeof window.history.replaceState === 'function') {
                const url = new URL(window.location.href);
                if (prodiId) url.searchParams.set('prodi_id', prodiId);
                else url.searchParams.delete('prodi_id');
                if (angkatanId) url.searchParams.set('angkatan_id', angkatanId);
                else url.searchParams.delete('angkatan_id');
                if (tanggalAcuan) url.searchParams.set('tanggal_acuan', tanggalAcuan);
                else url.searchParams.delete('tanggal_acuan');
                window.history.replaceState({}, '', url.toString());
            }
        })
        .catch((err) => {
            if (err && err.name === 'AbortError') {
                return;
            }
            showDashboardToast('Gagal memuat ringkasan. Silakan coba lagi.');
        })
        .finally(() => {
            if (activeFilterController === controller) {
                activeFilterController = null;
                if (filterScope) {
                    filterScope.classList.remove('is-filtering');
                    filterScope.removeAttribute('aria-busy');
                }
            }
        });
    }

    function unduhLaporanRingkasan() {
        const execDownload = () => {
            const prodiId = document.getElementById('filterProdi')?.value || '';
            const angkatanId = document.getElementById('filterAngkatan')?.value || '';
            const tanggalAcuan = document.getElementById('filterTanggalAcuan')?.value || '<?= esc($tanggal_acuan) ?>';
            const params = new URLSearchParams({
                jenis: 'status_pendidikan',
                tanggal_acuan: tanggalAcuan,
                prodi_id: prodiId,
                angkatan_id: angkatanId
            });
            window.location.href = '<?= site_url('ketarunaan/laporan/ekspor') ?>?' + params.toString();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Unduh Laporan?',
                text: 'Laporan status pendidikan akan diunduh sesuai filter aktif.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Unduh',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    execDownload();
                }
            });
        } else if (confirm('Unduh laporan status pendidikan sesuai filter aktif?')) {
            execDownload();
        }
    }
</script>
<?= $this->endSection() ?>
