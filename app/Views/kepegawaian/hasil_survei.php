<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Hasil Survei Pemetaan SDM<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .tabular-nums {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    .card-bento-ecc {
        background: #ffffff;
        border: 0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    @media (max-width: 767.98px) {
        .card-bento-ecc {
            padding: 16px;
        }
        .form-control,
        .form-select {
            font-size: 16px !important;
        }
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.15;
        color: #0f172a;
    }

    /* =======================================================================
       NAVIGASI 3 TAB ANALITIK ECC (STANDOUT & BENTO DESIGN)
       ======================================================================= */
    .nav-tabs-ecc-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 6px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .nav-tabs-ecc {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
        padding: 0;
        list-style: none;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .nav-tabs-ecc::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs-ecc .nav-item {
        flex: 1 1 0;
        min-width: 220px;
    }

    .nav-tabs-ecc .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 48px;
        padding: 10px 18px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .nav-tabs-ecc .nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .nav-tabs-ecc .nav-link .tab-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #e2e8f0;
        color: #475569;
        font-variant-numeric: tabular-nums;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .nav-tabs-ecc .nav-link:hover .tab-number {
        background: #cbd5e1;
        color: #0f172a;
    }

    .nav-tabs-ecc .nav-link.active {
        background: #1e40af;
        color: #ffffff;
        border-color: #1d4ed8;
        box-shadow: 0 4px 14px rgba(30, 64, 175, 0.25);
    }

    .nav-tabs-ecc .nav-link.active .tab-number {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    .nav-tabs-ecc .nav-link.active .tab-label {
        color: #ffffff;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .nav-tabs-ecc-wrapper {
            border-radius: 12px;
            padding: 4px;
        }
        .nav-tabs-ecc .nav-item {
            flex: 0 0 auto;
            min-width: 200px;
        }
        .nav-tabs-ecc .nav-link {
            min-height: 40px;
            padding: 8px 12px;
            font-size: 0.8125rem;
            gap: 8px;
        }
        .nav-tabs-ecc .nav-link .tab-number {
            width: 22px;
            height: 22px;
            font-size: 0.7rem;
        }
    }

    .table-bento-scroll {
        max-height: 480px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .table-bento {
        margin-bottom: 0;
        font-size: 0.8125rem;
    }

    .table-bento thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-bento tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .status-pill-compact {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .btn-action-compact {
        height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .chart-box {
        position: relative;
        height: 296px;
        width: 100%;
    }

    .filter-pill-sub {
        height: 32px;
        padding: 4px 12px;
        border-radius: 50rem;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }

    .filter-pill-sub.active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
    }

    /* Custom Modern Slim Scrollbar (ECC Standard) */
    .custom-scroll,
    .table-bento-scroll,
    .compare-table-wrapper {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .custom-scroll::-webkit-scrollbar,
    .table-bento-scroll::-webkit-scrollbar,
    .compare-table-wrapper::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track,
    .table-bento-scroll::-webkit-scrollbar-track,
    .compare-table-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb,
    .table-bento-scroll::-webkit-scrollbar-thumb,
    .compare-table-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 9999px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover,
    .table-bento-scroll::-webkit-scrollbar-thumb:hover,
    .compare-table-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* Interaktivitas Kartu & Grafik ECC */
    .card-interactive {
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
        position: relative;
    }
    .card-interactive:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }
    .card-interactive:active {
        transform: scale(0.99);
    }
    .card-interactive.is-active-filter {
        border: 2px solid #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
    .card-clickable-badge {
        cursor: pointer;
        transition: opacity 0.15s ease, transform 0.15s ease;
        display: inline-block;
        border-radius: 4px;
        padding: 1px 4px;
    }
    .card-clickable-badge:hover {
        background: #e2e8f0;
        color: #1d4ed8 !important;
    }
    .active-filter-banner {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        border-radius: 50rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .active-filter-banner .btn-clear-filter {
        border: 0;
        background: transparent;
        color: #1d4ed8;
        padding: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        line-height: 1;
        margin-left: 2px;
    }
    .pulse-highlight {
        animation: pulseHighlight 1.6s ease-out;
    }
    @keyframes pulseHighlight {
        0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.45); }
        50% { box-shadow: 0 0 0 8px rgba(37, 99, 235, 0.2); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }

    .detail-q-row {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-q-row:last-child {
        border-bottom: 0;
    }

    /* Tabel Perbandingan Berdampingan (Pegawai A, B, C...) */
    .compare-table-wrapper {
        position: relative;
        overflow-x: auto;
        overflow-y: auto;
        max-height: 68vh;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .table-compare {
        border-collapse: separate;
        border-spacing: 0;
        min-width: 100%;
        margin-bottom: 0;
    }

    .table-compare th,
    .table-compare td {
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        padding: 10px 14px;
        font-size: 0.8125rem;
    }

    .table-compare thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        z-index: 10;
        box-shadow: inset 0 -2px 0 #e2e8f0;
    }

    .table-compare thead th.sticky-col-header {
        left: 0;
        z-index: 20;
        background: #f1f5f9;
    }

    .table-compare tbody td.sticky-col {
        position: sticky;
        left: 0;
        background: #ffffff;
        z-index: 5;
        box-shadow: 2px 0 4px rgba(0, 0, 0, 0.04);
        font-weight: 500;
    }

    .table-compare tbody tr:hover td.sticky-col {
        background: #f8fafc;
    }

    .table-compare .sec-heading-row th {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        position: sticky;
        left: 0;
        border-top: 1px solid #dbeafe;
        border-bottom: 1px solid #dbeafe;
    }

    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$exportQuery = http_build_query([
    'tahun'    => $tahunFilter,
    'unit'     => $unitFilter,
    'kelompok' => $kelompokFilter,
]);
?>

<!-- ======================================================================= -->
<!-- HEADER HALAMAN & TOMBOL AKSI UTAMA                                      -->
<!-- ======================================================================= -->
<div class="card-bento-ecc mb-3">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h1 class="h4 fw-bold text-dark mb-0">Hasil Survei Pemetaan SDM</h1>
                <span id="badgeStatusPeriode" class="status-pill-compact <?= $isSurveyOpen ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' ?>">
                    <?= $isSurveyOpen ? 'Periode Dibuka' : 'Periode Ditutup' ?>
                </span>
                <?php if (!$canManage && !empty($delegationInfo)): ?>
                    <span class="status-pill-compact bg-primary-subtle text-primary border border-primary-subtle">
                        Akses Delegasi Kabag KUK
                    </span>
                <?php endif; ?>
            </div>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                Pemetaan potensi, kompetensi, kesiapan lintas unit, dan kontribusi BLU pegawai PKTJ.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <button
                type="button"
                id="btnCopyShareLink"
                data-link="<?= esc($shareLinkUrl) ?>"
                class="btn btn-outline-primary rounded-pill fw-semibold btn-tactile"
                style="height: 36px; padding: 4px 16px; font-size: 0.8125rem;"
            >
                <i class="bi bi-link-45deg me-1"></i> Salin Tautan Survei
            </button>

            <?php if ($canManage): ?>
                <button
                    type="button"
                    id="btnTogglePeriode"
                    data-open="<?= $isSurveyOpen ? '1' : '0' ?>"
                    class="btn <?= $isSurveyOpen ? 'btn-outline-secondary' : 'btn-outline-success' ?> rounded-pill fw-semibold btn-tactile"
                    style="height: 36px; padding: 4px 16px; font-size: 0.8125rem;"
                >
                    <?= $isSurveyOpen ? 'Tutup Periode' : 'Buka Periode' ?>
                </button>

                <button
                    type="button"
                    class="btn btn-light border rounded-pill fw-semibold text-dark btn-tactile"
                    style="height: 36px; padding: 4px 16px; font-size: 0.8125rem;"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDelegasiStaf"
                >
                    Delegasi Staf
                    <span id="badgeCountDelegasi" class="badge bg-primary text-white rounded-pill ms-1 tabular-nums"><?= count($delegatesList) ?></span>
                </button>
            <?php endif; ?>

            <?php if ($canExport): ?>
                <a
                    href="<?= site_url('kepegawaian/survei-pemetaan/export-excel?' . $exportQuery) ?>"
                    class="btn btn-success rounded-pill fw-semibold btn-tactile"
                    style="height: 36px; padding: 6px 16px; font-size: 0.8125rem;"
                >
                    <i class="bi bi-download me-1"></i> Export Excel
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bilah Filter Global -->
    <form action="<?= site_url('kepegawaian/survei-pemetaan') ?>" method="get" class="row g-2 align-items-end mt-3 pt-3 border-top">
        <div class="col-6 col-md-2">
            <label for="filterTahun" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Tahun Survei</label>
            <select name="tahun" id="filterTahun" class="form-select tabular-nums" style="height: 36px; font-size: 0.8125rem;">
                <?php for ($yr = max(2025, $activeSurveyYear - 1); $yr <= $activeSurveyYear + 1; $yr++): ?>
                    <option value="<?= $yr ?>" <?= (int) $tahunFilter === $yr ? 'selected' : '' ?>><?= $yr ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-12 col-md-5">
            <label for="filterUnit" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Unit Kerja</label>
            <select name="unit" id="filterUnit" class="form-select" style="height: 36px; font-size: 0.8125rem;">
                <option value="">Seluruh Unit Kerja</option>
                <?php foreach ($unitOptions as $uOpt): ?>
                    <option value="<?= esc($uOpt) ?>" <?= $unitFilter === $uOpt ? 'selected' : '' ?>><?= esc($uOpt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="filterKelompok" class="form-label text-secondary fw-semibold mb-1" style="font-size: 0.75rem;">Status ASN</label>
            <select name="kelompok" id="filterKelompok" class="form-select" style="height: 36px; font-size: 0.8125rem;">
                <option value="all" <?= $kelompokFilter === 'all' ? 'selected' : '' ?>>Semua ASN</option>
                <option value="PNS" <?= $kelompokFilter === 'PNS' ? 'selected' : '' ?>>PNS</option>
                <option value="PPPK" <?= $kelompokFilter === 'PPPK' ? 'selected' : '' ?>>PPPK</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary fw-semibold rounded-3 flex-grow-1 btn-tactile" style="height: 36px; font-size: 0.8125rem;">
                Terapkan
            </button>
            <?php if ($unitFilter !== '' || $kelompokFilter !== 'all' || $tahunFilter !== $activeSurveyYear): ?>
                <a href="<?= site_url('kepegawaian/survei-pemetaan') ?>" class="btn btn-light border fw-semibold rounded-3 btn-tactile" style="height: 36px; font-size: 0.8125rem;">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ======================================================================= -->
<!-- ANTREAN SINKRONISASI PROFIL KOSONG PEGAWAI (KHUSUS PENGELOLA UTAMA)     -->
<!-- ======================================================================= -->
<?php if ($canManage && !empty($pendingSyncList)): ?>
    <div class="card-bento-ecc mb-3 border border-primary-subtle" id="pendingSyncCard" style="background: #eff6ff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div>
                <div class="fw-bold text-dark" style="font-size: 0.9375rem;">
                    Usulan Pelengkapan Profil Pegawai (<span class="tabular-nums"><?= count($pendingSyncList) ?></span> Akun)
                </div>
                <div class="text-secondary" style="font-size: 0.8125rem;">
                    Pegawai berikut memperbarui penulisan gelar nama atau melengkapi atribut profil yang kosong saat mengisi survei.
                </div>
            </div>
            <button type="button" id="btnSyncAllProfiles" class="btn btn-primary btn-sm rounded-pill fw-semibold btn-tactile" style="height: 36px; padding: 4px 16px;">
                Terapkan Semua ke Profil Pegawai
            </button>
        </div>
        <div class="d-flex flex-column gap-2 mt-2">
            <?php foreach (array_slice($pendingSyncList, 0, 5) as $ps): ?>
                <div class="bg-white rounded-3 border p-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div style="font-size: 0.8125rem;">
                        <span class="fw-semibold text-dark"><?= esc($ps['nama']) ?></span>
                        <span class="text-secondary tabular-nums ms-1">(<?= esc($ps['nip']) ?>)</span>
                        <span class="text-primary fw-medium ms-2"><?= esc(implode(' · ', $ps['changes'])) ?></span>
                    </div>
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-action-compact btn-tactile btn-sync-single"
                        data-respon-id="<?= (int) $ps['respon_id'] ?>"
                    >
                        Terapkan
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- ======================================================================= -->
<!-- 4 KARTU KPI EKSEKUTIF PEMETAAN PEGAWAI                                   -->
<!-- ======================================================================= -->
<div class="row g-3 mb-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-bento-ecc card-interactive h-100" id="cardKpiKeterisian" title="Klik untuk melihat tabel responden">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Keterisian Pegawai</div>
                <span class="text-primary" style="font-size: 0.75rem;">Lihat Data &rarr;</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between gap-2 mb-2">
                <div class="kpi-value tabular-nums"><?= (float) $kpi['asn_persen'] ?>%</div>
                <div class="text-secondary fw-semibold tabular-nums" style="font-size: 0.875rem;">
                    <?= (int) $kpi['asn_submitted'] ?> / <?= (int) $kpi['asn_wajib'] ?> pegawai
                </div>
            </div>
            <div class="progress rounded-pill mb-2" style="height: 8px; background: #e2e8f0;">
                <div class="progress-bar rounded-pill" style="width: <?= min(100, (float) $kpi['asn_persen']) ?>%; background: #1d4ed8;"></div>
            </div>
            <div class="text-secondary tabular-nums" style="font-size: 0.75rem;">
                <span class="card-clickable-badge text-dark" id="btnFilterKpiDraft" title="Klik untuk menyaring status Draf">Draf: <strong><?= (int) $kpi['asn_draft'] ?></strong></span>
                &middot;
                <span class="card-clickable-badge text-danger" id="btnFilterKpiBelum" title="Klik untuk melihat pegawai yang belum mulai">Belum mulai: <strong><?= (int) $kpi['asn_belum'] ?></strong></span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-bento-ecc card-interactive h-100" id="cardKpiKerjaLebih" title="Klik untuk menyaring pegawai siap kerja lebih">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Mau &amp; Siap Bekerja Lebih</div>
                <span class="text-primary" style="font-size: 0.75rem;">Saring &rarr;</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between gap-2 mb-2">
                <div class="kpi-value tabular-nums"><?= (int) $kpi['kerja_lebih_ready'] ?></div>
                <span class="status-pill-compact bg-primary-subtle text-primary tabular-nums"><?= (float) $kpi['kerja_lebih_persen'] ?>%</span>
            </div>
            <div class="text-secondary tabular-nums mt-3" style="font-size: 0.75rem;">
                Siap jam lebih: <strong><?= (int) $kpi['overtime_willing'] ?></strong> &middot; Ada kapasitas: <strong><?= (int) $kpi['extra_capacity_count'] ?></strong>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-bento-ecc card-interactive h-100" id="cardKpiLintasUnit" title="Klik untuk menyaring pegawai siap lintas unit">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Siap Diperbantukan Lintas Unit</div>
                <span class="text-success" style="font-size: 0.75rem;">Saring &rarr;</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between gap-2 mb-2">
                <div class="kpi-value tabular-nums"><?= (int) $kpi['lintas_unit_ready'] ?></div>
                <span class="status-pill-compact bg-success-subtle text-success tabular-nums"><?= (float) $kpi['lintas_unit_persen'] ?>%</span>
            </div>
            <div class="text-secondary tabular-nums mt-3" style="font-size: 0.75rem;">
                Alokasi &ge; 2 jam/minggu: <strong><?= (int) $kpi['lintas_jam_cukup'] ?></strong> pegawai
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card-bento-ecc card-interactive h-100" id="cardKpiBlu" title="Klik untuk membuka pemetaan kontributor BLU">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-secondary fw-semibold" style="font-size: 0.75rem;">Mau &amp; Siap Membantu BLU</div>
                <span class="text-info-emphasis" style="font-size: 0.75rem;">Buka Peta &rarr;</span>
            </div>
            <div class="d-flex align-items-baseline justify-content-between gap-2 mb-2">
                <div class="kpi-value tabular-nums"><?= (int) $kpi['blu_ready_count'] ?></div>
                <span class="status-pill-compact bg-info-subtle text-info-emphasis tabular-nums"><?= (float) $kpi['blu_ready_persen'] ?>%</span>
            </div>
            <div class="text-secondary tabular-nums mt-3" style="font-size: 0.75rem;">
                Minat BLU: <strong><?= (int) $kpi['blu_interested'] ?></strong> &middot; Siap target BLU: <strong><?= (int) $kpi['blu_target_willing'] ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- NAVIGASI 3 TAB ANALITIK (STANDOUT BENTO SEGMENTED BAR)                  -->
<!-- ======================================================================= -->
<div class="nav-tabs-ecc-wrapper mb-3">
    <ul class="nav nav-tabs-ecc nav-pills-ecc" id="hasilSurveiTabs" role="tablist">
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link active w-100" id="tab-monitoring-btn" data-bs-toggle="pill" data-bs-target="#paneMonitoring" type="button" role="tab" aria-selected="true">
                <span class="tab-number">1</span>
                <span class="tab-label">Monitoring &amp; Responden</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100" id="tab-talenta-btn" data-bs-toggle="pill" data-bs-target="#paneTalenta" type="button" role="tab" aria-selected="false">
                <span class="tab-number">2</span>
                <span class="tab-label">Peta Talenta Lintas Unit &amp; BLU</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100" id="tab-aspirasi-btn" data-bs-toggle="pill" data-bs-target="#paneAspirasi" type="button" role="tab" aria-selected="false">
                <span class="tab-number">3</span>
                <span class="tab-label">Perilaku Kerja, Penghargaan &amp; Masukan</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content">
    <!-- =================================================================== -->
    <!-- TAB 1: MONITORING KETERISIAN UNIT & DAFTAR RESPONDEN ASN            -->
    <!-- =================================================================== -->
    <div class="tab-pane fade show active" id="paneMonitoring" role="tabpanel">
        <!-- Tabel Rekap Keterisian per Unit Kerja -->
        <div class="card-bento-ecc mb-3" id="cardRekapUnit">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Rekapitulasi Keterisian per Unit Kerja</h2>
                    <div class="text-secondary" style="font-size: 0.8125rem;">Progres pengisian survei pegawai aktif per unit. <span class="text-primary fw-medium">&bull; Klik baris unit untuk menyaring responden</span></div>
                </div>
            </div>

            <div class="table-bento-scroll">
                <table class="table table-bento table-hover">
                    <thead>
                        <tr>
                            <th>Unit Kerja</th>
                            <th class="text-center">Pegawai Wajib Isi</th>
                            <th class="text-center">Terkirim</th>
                            <th class="text-center">Draf</th>
                            <th class="text-center">Belum Mengisi</th>
                            <th style="min-width: 160px;">% Keterisian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rekapUnit)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Belum ada data unit kerja pada filter ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rekapUnit as $ru): ?>
                                <tr class="unit-summary-row" role="button" data-unit="<?= esc($ru['nama_unit']) ?>" style="cursor: pointer;" title="Klik untuk menyaring daftar responden unit <?= esc($ru['nama_unit']) ?>">
                                    <td class="fw-semibold text-dark">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span><?= esc($ru['nama_unit']) ?></span>
                                            <span class="text-primary" style="font-size: 0.75rem;">Saring &rarr;</span>
                                        </div>
                                    </td>
                                    <td class="text-center tabular-nums"><?= (int) $ru['asn_wajib'] ?></td>
                                    <td class="text-center tabular-nums fw-semibold text-success"><?= (int) $ru['asn_submitted'] ?></td>
                                    <td class="text-center tabular-nums text-warning-emphasis"><?= (int) $ru['asn_draft'] ?></td>
                                    <td class="text-center tabular-nums text-danger"><?= (int) $ru['asn_belum'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress rounded-pill flex-grow-1" style="height: 8px; background: #e2e8f0;">
                                                <div class="progress-bar rounded-pill" style="width: <?= min(100, (float) $ru['asn_persen']) ?>%; background: #1d4ed8;"></div>
                                            </div>
                                            <span class="fw-semibold text-dark tabular-nums" style="font-size: 0.75rem; min-width: 44px; text-align: right;">
                                                <?= (float) $ru['asn_persen'] ?>%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Responden Survei ASN (Terkirim & Draf) -->
        <div class="card-bento-ecc mb-3" id="cardDaftarResponden">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Daftar Responden (<span class="tabular-nums"><?= count($daftarResponden) ?></span>)</h2>
                    <div class="text-secondary" style="font-size: 0.8125rem;">Klik Rincian untuk membaca seluruh jawaban 10 bagian. Gunakan Buka Edit agar pegawai yang sudah selesai dapat mengedit kembali isiannya.</div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="d-flex gap-1" id="respondenStatusPills">
                        <button type="button" class="filter-pill-sub active" data-status-filter="all">Semua</button>
                        <button type="button" class="filter-pill-sub" data-status-filter="submitted">Terkirim</button>
                        <button type="button" class="filter-pill-sub" data-status-filter="draft">Draf</button>
                    </div>
                    <input
                        type="search"
                        id="searchRespondenInput"
                        class="form-control"
                        style="height: 36px; width: 240px; font-size: 0.8125rem;"
                        placeholder="Cari nama, NIP, unit..."
                    >
                </div>
            </div>

            <!-- Banner Filter Aktif Responden -->
            <div id="activeRespondenFilterBanner" class="d-none mb-3"></div>

            <div class="table-bento-scroll">
                <table class="table table-bento table-hover" id="tableDaftarResponden">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 44px;">
                                <input type="checkbox" id="checkAllResponden" class="form-check-input" title="Pilih Semua Pegawai Terlihat">
                            </th>
                            <th>Nama &amp; NIP</th>
                            <th>Status ASN</th>
                            <th>Unit Kerja &amp; Jabatan</th>
                            <th>Progres</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarResponden)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">Belum ada pegawai yang mengisi survei pada filter ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftarResponden as $resp): ?>
                                <?php
                                $isSub = ($resp['status'] === 'submitted');
                                $searchKey = strtolower($resp['nama'] . ' ' . $resp['nomor_identitas'] . ' ' . $resp['unit'] . ' ' . $resp['jabatan'] . ' ' . $resp['status_kepegawaian']);
                                ?>
                                <tr class="responden-row" data-id="<?= (int) $resp['id'] ?>" data-status="<?= esc($resp['status']) ?>" data-unit="<?= esc($resp['unit']) ?>" data-search="<?= esc($searchKey) ?>">
                                    <td class="text-center align-middle">
                                        <input
                                            type="checkbox"
                                            class="form-check-input check-responden-item"
                                            value="<?= (int) $resp['id'] ?>"
                                            data-nama="<?= esc($resp['nama']) ?>"
                                            data-nip="<?= esc($resp['nomor_identitas']) ?>"
                                            data-unit="<?= esc($resp['unit']) ?>"
                                        >
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($resp['nama']) ?></div>
                                        <div class="text-secondary tabular-nums" style="font-size: 0.75rem;"><?= esc($resp['nomor_identitas']) ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill-compact bg-primary-subtle text-primary">
                                            <?= esc($resp['status_kepegawaian']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark"><?= esc($resp['unit']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($resp['jabatan']) ?></div>
                                    </td>
                                    <td class="tabular-nums">
                                        <div class="fw-semibold text-dark"><?= (float) $resp['progress_percent'] ?>%</div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= (int) $resp['total_answered'] ?>/<?= (int) $resp['total_applicable'] ?> butir</div>
                                    </td>
                                    <td>
                                        <?php if ($isSub): ?>
                                            <span class="status-pill-compact bg-success-subtle text-success">Terkirim</span>
                                        <?php else: ?>
                                            <span class="status-pill-compact bg-warning-subtle text-warning-emphasis">Draf (Bagian <?= (int) $resp['last_step'] ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-action-compact btn-tactile btn-view-detail"
                                                data-respon-id="<?= (int) $resp['id'] ?>"
                                            >
                                                Rincian
                                            </button>

                                            <?php if (!$isSub): ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-warning text-dark btn-action-compact btn-tactile btn-reset-draf"
                                                    data-respon-id="<?= (int) $resp['id'] ?>"
                                                    data-nama="<?= esc($resp['nama']) ?>"
                                                >
                                                    Reset Draf
                                                </button>
                                            <?php endif; ?>

                                            <?php if (!empty($canUnlock) && $isSub): ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary btn-action-compact btn-tactile btn-buka-kunci"
                                                    data-respon-id="<?= (int) $resp['id'] ?>"
                                                    data-nama="<?= esc($resp['nama']) ?>"
                                                >
                                                    Buka Edit
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Pegawai yang Belum Menyelesaikan Survei -->
        <div class="card-bento-ecc" id="cardAsnBelum">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Pegawai Belum Menyelesaikan Survei (<span class="tabular-nums"><?= count($daftarAsnBelum) ?></span>)</h2>
                    <div class="text-secondary" style="font-size: 0.8125rem;">Daftar pemilik akun ECC aktif yang belum memulai atau masih berstatus draf.</div>
                </div>
                <input
                    type="search"
                    id="searchAsnBelumInput"
                    class="form-control"
                    style="height: 36px; width: 240px; font-size: 0.8125rem;"
                    placeholder="Cari pegawai..."
                >
            </div>

            <!-- Banner Filter Aktif Pegawai Belum -->
            <div id="activeAsnBelumFilterBanner" class="d-none mb-3"></div>

            <div class="table-bento-scroll">
                <table class="table table-bento table-hover" id="tableAsnBelum">
                    <thead>
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>NIP</th>
                            <th>Unit Kerja</th>
                            <th>Jabatan</th>
                            <th>Status Pengisian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarAsnBelum)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-success fw-semibold py-4">Seluruh pegawai pada cakupan ini telah mengirim survei.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftarAsnBelum as $ab): ?>
                                <?php $abSearch = strtolower($ab['nama'] . ' ' . $ab['nip'] . ' ' . $ab['unit'] . ' ' . $ab['jabatan']); ?>
                                <tr class="asn-belum-row" data-unit="<?= esc($ab['unit']) ?>" data-search="<?= esc($abSearch) ?>">
                                    <td class="fw-semibold text-dark"><?= esc($ab['nama']) ?></td>
                                    <td class="tabular-nums text-secondary"><?= esc($ab['nip']) ?></td>
                                    <td><?= esc($ab['unit']) ?></td>
                                    <td class="text-secondary"><?= esc($ab['jabatan']) ?></td>
                                    <td>
                                        <?php if ($ab['state'] === 'draft'): ?>
                                            <span class="status-pill-compact bg-warning-subtle text-warning-emphasis tabular-nums">
                                                Draf <?= (float) $ab['progress_percent'] ?>% (Bagian <?= (int) $ab['last_step'] ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="status-pill-compact bg-danger-subtle text-danger">Belum Mulai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 2: PETA TALENTA LINTAS UNIT, BLU & VISUALISASI CHART.JS         -->
    <!-- =================================================================== -->
    <div class="tab-pane fade" id="paneTalenta" role="tabpanel">
        <!-- Sorotan Khusus No. 21: Keahlian Tambahan di Luar Tupoksi (Fokus 2 Analitik) -->
        <div class="card-bento-ecc mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill text-primary border border-primary-subtle fw-semibold" style="background: #eff6ff; padding: 4px 10px; font-size: 0.75rem;">
                            Fokus Analitik No. 21
                        </span>
                        <h2 class="h6 fw-bold text-dark mb-0">Keahlian Tambahan Pegawai di Luar Tupoksi</h2>
                    </div>
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        Pemetaan pegawai yang memiliki keahlian pendukung di luar tugas utama (No. 21–25) untuk mengoptimalkan potensi internal PKTJ.
                    </div>
                </div>
            </div>

            <!-- 3 Kartu Mini Ringkasan No. 21 -->
            <div class="row g-2 mb-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 border card-interactive card-keahlian-filter h-100 is-active-filter" id="cardKeahlianSemua" title="Klik untuk menampilkan seluruh pegawai berkeahlian" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Memiliki Keahlian Tambahan (No. 21)</div>
                            <span class="text-primary" style="font-size: 0.75rem;">Semua &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-dark mb-0 tabular-nums"><?= (int) ($q21Stats['total_has_skill'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">dari <?= (int) ($kpi['total_submitted'] ?? $kpi['submitted_filtered'] ?? $kpi['asn_submitted'] ?? 0) ?> responden (<?= (float) ($q21Stats['persen_memiliki_keahlian'] ?? 0) ?>%)</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 border card-interactive card-keahlian-filter h-100" id="cardKeahlianProduk" title="Klik untuk menyaring yang menghasilkan produk/jasa" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Pernah Hasilkan Produk/Jasa (No. 24)</div>
                            <span class="text-primary" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-primary mb-0 tabular-nums"><?= (int) ($q21Stats['has_product_count'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">pegawai</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 rounded-3 border card-interactive card-keahlian-filter h-100" id="cardKeahlianPortofolio" title="Klik untuk menyaring yang memiliki portofolio/bukti" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Memiliki Portofolio / Bukti (No. 25)</div>
                            <span class="text-success" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-success mb-0 tabular-nums"><?= (int) ($q21Stats['has_portfolio_count'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">pegawai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Filter Aktif Keahlian Tambahan -->
            <div id="activeKeahlianFilterBanner" class="d-none mb-3"></div>

            <!-- Tabel Daftar Pegawai Pemilik Keahlian Tambahan -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="fw-semibold text-dark" style="font-size: 0.8125rem;">
                    Daftar Pegawai &amp; Rincian Keahlian (No. 21–25)
                </div>
                <input
                    type="search"
                    id="searchKeahlianTambahanInput"
                    class="form-control"
                    style="height: 36px; width: 240px; font-size: 0.8125rem;"
                    placeholder="Cari keahlian, nama, unit..."
                >
            </div>

            <div class="table-bento-scroll" style="max-height: 360px;">
                <table class="table table-bento table-hover" id="tableKeahlianTambahan">
                    <thead>
                        <tr>
                            <th>Pegawai &amp; Unit</th>
                            <th>Keahlian Tambahan (No. 22)</th>
                            <th class="text-center">Tingkat Penguasaan (No. 23)</th>
                            <th>Pengalaman Produk/Jasa (No. 24)</th>
                            <th>Bukti / Portofolio (No. 25)</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarKeahlianTambahan)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-3">Belum ada pegawai dengan keahlian tambahan pada filter ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftarKeahlianTambahan as $kt): ?>
                                <?php
                                $namaTxt       = $kt['nama'] ?? '';
                                $nipTxt        = $kt['nip'] ?? '';
                                $unitTxt       = $kt['unit'] ?? '';
                                $keahlianTxt   = $kt['keahlian'] ?? $kt['q22_keahlian'] ?? 'Belum merinci';
                                $skorVal       = (int) ($kt['skor'] ?? $kt['q23_skor'] ?? 0);
                                $skorLabel     = $kt['skor_label'] ?? ('Skor ' . $skorVal);
                                $produkTxt     = $kt['produk'] ?? $kt['q24_produk'] ?? '-';
                                $portofolioTxt = $kt['portofolio'] ?? $kt['q25_bukti'] ?? '-';
                                $ktSearch      = strtolower($namaTxt . ' ' . $nipTxt . ' ' . $unitTxt . ' ' . $keahlianTxt . ' ' . $produkTxt . ' ' . $portofolioTxt);
                                $hasProd       = ($produkTxt !== '' && $produkTxt !== '-') ? '1' : '0';
                                $hasPort       = ($portofolioTxt === 'Ada' || $portofolioTxt === 'Sedang dalam proses pengumpulan') ? '1' : '0';
                                ?>
                                <tr class="keahlian-row" data-has-product="<?= $hasProd ?>" data-has-portfolio="<?= $hasPort ?>" data-search="<?= esc($ktSearch) ?>">
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($namaTxt) ?></div>
                                        <div class="text-secondary tabular-nums" style="font-size: 0.75rem;"><?= esc($nipTxt) ?> &middot; <?= esc($unitTxt) ?></div>
                                    </td>
                                    <td style="max-width: 260px;">
                                        <div class="fw-medium text-primary" style="font-size: 0.8125rem;"><?= esc($keahlianTxt) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($skorVal > 0): ?>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill tabular-nums" title="<?= esc($skorLabel) ?>">
                                                Skor <?= $skorVal ?>/5
                                            </span>
                                        <?php else: ?>
                                            <span class="text-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 220px;">
                                        <div class="text-dark" style="font-size: 0.75rem;"><?= esc($produkTxt !== '' ? $produkTxt : '-') ?></div>
                                    </td>
                                    <td>
                                        <?php if ($portofolioTxt === 'Ada'): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill">Ada</span>
                                        <?php elseif ($portofolioTxt === 'Sedang dalam proses pengumpulan'): ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Proses Kumpul</span>
                                        <?php else: ?>
                                            <span class="text-secondary" style="font-size: 0.75rem;"><?= esc($portofolioTxt !== '' ? $portofolioTxt : '-') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-action-compact btn-tactile btn-view-detail"
                                            data-respon-id="<?= (int) ($kt['respon_id'] ?? 0) ?>"
                                        >
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4 Kartu Chart.js -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-6">
                <div class="card-bento-ecc h-100">
                    <h2 class="h6 fw-bold text-dark mb-1">10 Kompetensi Utama Terbanyak (No. 20)</h2>
                    <div class="text-secondary mb-3" style="font-size: 0.75rem;">Sebaran bidang keahlian utama responden. <span class="text-primary fw-medium">&bull; Klik batang untuk menyaring talenta</span></div>
                    <div class="chart-box">
                        <canvas id="chartKompetensiCanvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card-bento-ecc h-100">
                    <h2 class="h6 fw-bold text-dark mb-1">Bidang Kontribusi Pendapatan BLU (No. 61)</h2>
                    <div class="text-secondary mb-3" style="font-size: 0.75rem;">Layanan BLU yang paling siap didukung pegawai. <span class="text-primary fw-medium">&bull; Klik batang untuk menyaring kontributor</span></div>
                    <div class="chart-box">
                        <canvas id="chartBluCanvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card-bento-ecc h-100">
                    <h2 class="h6 fw-bold text-dark mb-1">Kondisi Beban Kerja &amp; Kapasitas Tambahan (No. 68 &amp; 70)</h2>
                    <div class="text-secondary mb-3" style="font-size: 0.75rem;">Pemetaan ruang kapasitas pegawai. <span class="text-primary fw-medium">&bull; Klik donat untuk menyaring kapasitas</span></div>
                    <div class="chart-box">
                        <canvas id="chartKapasitasCanvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card-bento-ecc h-100">
                    <h2 class="h6 fw-bold text-dark mb-1">10 Kebutuhan Pelatihan Prioritas (No. 37)</h2>
                    <div class="text-secondary mb-3" style="font-size: 0.75rem;">Bidang pelatihan yang paling diminati. <span class="text-primary fw-medium">&bull; Klik batang untuk rincian pegawai</span></div>
                    <div class="chart-box">
                        <canvas id="chartPelatihanCanvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- =============================================================== -->
        <!-- MUARA PEMETAAN: REKOMENDASI KONTRIBUTOR LAYANAN BLU (PAK KBU)   -->
        <!-- =============================================================== -->
        <div class="card-bento-ecc mb-3" id="secMuaraKontributorBlu">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill text-success border border-success-subtle fw-semibold" style="background: #f0fdf4; padding: 4px 10px; font-size: 0.75rem;">
                            Muara Analitik Pak KBU
                        </span>
                        <h2 class="h6 fw-bold text-dark mb-0">Pemetaan Kontributor &amp; Tim Penggerak Layanan BLU</h2>
                    </div>
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        Peringkat prioritas pegawai yang siap dan mau mendukung pendapatan BLU PKTJ berdasarkan minat (No. 60), kesiapan target (No. 66), kapasitas kerja (No. 70), keahlian tambahan (No. 21–25), serta kesiapan komersial dan jejaring (No. 62–64).
                    </div>
                </div>
            </div>

            <!-- 4 Kartu KPI Kontributor BLU -->
            <div class="row g-2 mb-3">
                <div class="col-6 col-lg-3">
                    <div class="p-3 rounded-3 border card-interactive card-blu-filter h-100" id="cardBluTier1" title="Klik untuk menyaring Prioritas I (Tim Inti)" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Tim Inti Penggerak (Tier 1)</div>
                            <span class="text-success" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-success mb-0 tabular-nums"><?= (int) ($bluStats['tier1_count'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">Prioritas Utama</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 rounded-3 border card-interactive card-blu-filter h-100" id="cardBluTarget" title="Klik untuk menyaring pegawai siap target BLU" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Siap Target BLU (No. 66)</div>
                            <span class="text-primary" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-primary mb-0 tabular-nums"><?= (int) ($kpi['blu_target_willing'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">pegawai</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 rounded-3 border card-interactive card-blu-filter h-100" id="cardBluMitra" title="Klik untuk menyaring pegawai bersedia cari mitra" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Bersedia Cari Mitra (No. 63)</div>
                            <span class="text-info-emphasis" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-info-emphasis mb-0 tabular-nums"><?= (int) ($bluStats['willing_partner_cnt'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">pegawai</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 rounded-3 border card-interactive card-blu-filter h-100" id="cardBluExperience" title="Klik untuk menyaring pegawai dengan pengalaman bisnis/relasi" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="text-secondary" style="font-size: 0.75rem;">Pengalaman Bisnis (No. 62 &amp; 64)</div>
                            <span class="text-dark" style="font-size: 0.75rem;">Saring &rarr;</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="h5 fw-bold text-dark mb-0 tabular-nums"><?= (int) ($bluStats['has_experience_cnt'] ?? 0) ?></span>
                            <span class="text-secondary" style="font-size: 0.75rem;">berpengalaman</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Cepat & Pencarian Kontributor BLU -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <div class="d-flex flex-wrap align-items-center gap-1" id="bluTierFilterPills">
                    <span class="text-secondary me-1 fw-medium" style="font-size: 0.75rem;">Tingkat Prioritas:</span>
                    <button type="button" class="filter-pill-sub active" data-blu-tier="all">Semua Kandidat</button>
                    <button type="button" class="filter-pill-sub" data-blu-tier="1">Prioritas I (Tim Inti)</button>
                    <button type="button" class="filter-pill-sub" data-blu-tier="2">Prioritas II (Potensial)</button>
                    <button type="button" class="filter-pill-sub" data-blu-tier="3">Prioritas III (Pendukung)</button>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="bluFilterServiceSelect" class="form-select" style="height: 36px; width: 230px; font-size: 0.8125rem;">
                        <option value="">Semua Layanan BLU (No. 61)</option>
                        <?php foreach (($filterOptions['q61_options'] ?? []) as $opt61): ?>
                            <option value="<?= esc($opt61) ?>"><?= esc($opt61) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input
                        type="search"
                        id="searchKontributorBluInput"
                        class="form-control"
                        style="height: 36px; width: 240px; font-size: 0.8125rem;"
                        placeholder="Cari nama, NIP, keahlian..."
                    >
                </div>
            </div>

            <!-- Banner Filter Aktif Kontributor BLU -->
            <div id="activeBluFilterBanner" class="d-none mb-3"></div>
                        <?php foreach (($filterOptions['q61_options'] ?? []) as $opt61): ?>
                            <option value="<?= esc($opt61) ?>"><?= esc($opt61) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input
                        type="search"
                        id="searchKontributorBluInput"
                        class="form-control"
                        style="height: 36px; width: 240px; font-size: 0.8125rem;"
                        placeholder="Cari nama, NIP, keahlian..."
                    >
                </div>
            </div>

            <!-- Tabel Peringkat Kontributor BLU -->
            <div class="table-bento-scroll" style="max-height: 480px;">
                <table class="table table-bento table-hover" id="tableKontributorBlu">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 44px;">
                                <input type="checkbox" id="checkAllBlu" class="form-check-input" title="Pilih Semua Pegawai Terlihat">
                            </th>
                            <th>Pegawai &amp; Unit</th>
                            <th>Prioritas &amp; Skor Kesiapan</th>
                            <th>Kesiapan Target &amp; Minat (No. 66 &amp; 60)</th>
                            <th>Bidang Layanan BLU (No. 61)</th>
                            <th>Aset Penggerak (Bisnis, Mitra, Jejaring, Skill)</th>
                            <th>Bentuk Kontribusi (No. 67) &amp; Ide (No. 65)</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarKontributorBlu)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">Belum ada responden terkirim untuk pemetaan kontributor BLU.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftarKontributorBlu as $kb): ?>
                                <?php
                                $kbSearch = strtolower($kb['nama'] . ' ' . $kb['nip'] . ' ' . $kb['unit'] . ' ' . $kb['jabatan'] . ' ' . $kb['q22_keahlian'] . ' ' . implode(' ', $kb['q61_list']) . ' ' . $kb['q67_kontribusi'] . ' ' . $kb['q65_ide']);
                                ?>
                                <tr
                                    class="blu-contributor-row"
                                    data-id="<?= (int) $kb['respon_id'] ?>"
                                    data-tier="<?= (int) $kb['tier_blu'] ?>"
                                    data-services='<?= esc(json_encode($kb['q61_list'], JSON_UNESCAPED_UNICODE)) ?>'
                                    data-search="<?= esc($kbSearch) ?>"
                                >
                                    <td class="text-center align-middle">
                                        <input
                                            type="checkbox"
                                            class="form-check-input check-responden-item"
                                            value="<?= (int) $kb['respon_id'] ?>"
                                            data-nama="<?= esc($kb['nama']) ?>"
                                            data-nip="<?= esc($kb['nip']) ?>"
                                            data-unit="<?= esc($kb['unit']) ?>"
                                        >
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($kb['nama']) ?></div>
                                        <div class="text-secondary tabular-nums" style="font-size: 0.75rem;"><?= esc($kb['nip']) ?> &middot; <?= esc($kb['unit']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($kb['jabatan']) ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill-compact <?= esc($kb['tier_badge_class']) ?> mb-1">
                                            <?= esc($kb['tier_label']) ?>
                                        </span>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <div class="progress rounded-pill flex-grow-1" style="height: 6px; width: 70px; background: #e2e8f0;">
                                                <div class="progress-bar rounded-pill" style="width: <?= min(100, (int) $kb['skor_blu']) ?>%; background: <?= $kb['tier_blu'] === 1 ? '#16a34a' : ($kb['tier_blu'] === 2 ? '#2563eb' : '#f59e0b') ?>;"></div>
                                            </div>
                                            <span class="fw-bold text-dark tabular-nums" style="font-size: 0.75rem;"><?= (int) $kb['skor_blu'] ?>%</span>
                                        </div>
                                    </td>
                                    <td style="max-width: 220px;">
                                        <div class="text-dark fw-medium" style="font-size: 0.8125rem;">
                                            Target: <span class="<?= $kb['is_blu_committed'] ? 'text-success fw-semibold' : 'text-secondary' ?>"><?= esc($kb['q66_target']) ?></span>
                                        </div>
                                        <div class="text-secondary mt-1" style="font-size: 0.75rem;">
                                            Minat: <?= esc($kb['q60_minat']) ?>
                                        </div>
                                    </td>
                                    <td style="max-width: 240px;">
                                        <?php if (!empty($kb['q61_list'])): ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach (array_slice($kb['q61_list'], 0, 3) as $svc): ?>
                                                    <span class="badge bg-light text-dark border rounded-pill" style="font-size: 0.6875rem;">
                                                        <?= esc(mb_strimwidth($svc, 0, 28, '...')) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                                <?php if (count($kb['q61_list']) > 3): ?>
                                                    <span class="badge bg-light text-primary border rounded-pill" style="font-size: 0.6875rem;">
                                                        +<?= count($kb['q61_list']) - 3 ?> lainnya
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-secondary" style="font-size: 0.75rem;">Belum memilih bidang</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width: 240px;">
                                        <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                            <?php if ($kb['q62_bisnis'] !== 'Belum memiliki pengalaman' && $kb['q62_bisnis'] !== '-'): ?>
                                                <div class="text-dark"><span class="fw-semibold text-primary">Bisnis:</span> <?= esc($kb['q62_bisnis']) ?></div>
                                            <?php endif; ?>
                                            <?php if (in_array($kb['q63_mitra'], ['Sangat bersedia', 'Bersedia'], true)): ?>
                                                <div class="text-success"><span class="fw-semibold">Cari Mitra:</span> <?= esc($kb['q63_mitra']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($kb['q64_jejaring'] === 'Ya, memiliki jaringan yang relevan'): ?>
                                                <div class="text-info-emphasis"><span class="fw-semibold">Jejaring:</span> Memiliki relasi profesional</div>
                                            <?php endif; ?>
                                            <?php if (!empty($kb['has_skill_21']) && $kb['q22_keahlian'] !== ''): ?>
                                                <div class="text-secondary"><span class="fw-semibold text-dark">Keahlian:</span> <?= esc(mb_strimwidth($kb['q22_keahlian'], 0, 45, '...')) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <?php if ($kb['q67_kontribusi'] !== ''): ?>
                                            <div class="text-dark fst-italic" style="font-size: 0.75rem; line-height: 1.4;">
                                                &ldquo;<?= esc(mb_strimwidth($kb['q67_kontribusi'], 0, 90, '...')) ?>&rdquo;
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($kb['q65_ide'] !== ''): ?>
                                            <div class="text-secondary mt-1" style="font-size: 0.6875rem;">
                                                Ide: <?= esc(mb_strimwidth($kb['q65_ide'], 0, 70, '...')) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($kb['q67_kontribusi'] === '' && $kb['q65_ide'] === ''): ?>
                                            <span class="text-secondary" style="font-size: 0.75rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-action-compact btn-tactile btn-view-detail"
                                            data-respon-id="<?= (int) $kb['respon_id'] ?>"
                                        >
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pencari Talenta Lintas Unit & Kontributor BLU -->
        <div class="card-bento-ecc">
            <div class="mb-3">
                <h2 class="h6 fw-bold text-dark mb-1">Pencari Talenta Lintas Unit &amp; Kontributor BLU</h2>
                <div class="text-secondary" style="font-size: 0.8125rem;">
                    Saring pegawai berdasarkan keahlian, kesiapan membantu unit kerja lain, minat kegiatan BLU, serta ketersediaan kapasitas kerja.
                </div>
            </div>

            <!-- Filter Cepat 4 Fokus Analitik Pak KBU -->
            <div class="d-flex flex-wrap align-items-center gap-1 mb-3" id="talentQuickFilterPills">
                <span class="text-secondary me-1 fw-medium" style="font-size: 0.75rem;">Filter Fokus:</span>
                <button type="button" class="filter-pill-sub active" data-quick-filter="all">Semua Pegawai</button>
                <button type="button" class="filter-pill-sub" data-quick-filter="kerja_lebih">
                    <i class="bi bi-clock-history me-1"></i>Mau &amp; Siap Kerja Lebih (No. 33 + 70)
                </button>
                <button type="button" class="filter-pill-sub" data-quick-filter="skill21">
                    <i class="bi bi-stars me-1"></i>Memiliki Keahlian Tambahan (No. 21)
                </button>
                <button type="button" class="filter-pill-sub" data-quick-filter="lintas">
                    <i class="bi bi-arrows-angle-expand me-1"></i>Siap Bantu Lintas Unit (No. 27)
                </button>
                <button type="button" class="filter-pill-sub" data-quick-filter="blu_commit">
                    <i class="bi bi-briefcase me-1"></i>Mau &amp; Siap Bantu BLU (No. 60 + 66)
                </button>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-12 col-md-3">
                    <input type="search" id="tfSearchKeyword" class="form-control" style="height: 36px; font-size: 0.8125rem;" placeholder="Cari nama / keahlian tambahan...">
                </div>
                <div class="col-12 col-md-3">
                    <select id="tfFilterKompetensi" class="form-select" style="height: 36px; font-size: 0.8125rem;">
                        <option value="">Semua Kompetensi Utama (No. 20)</option>
                        <?php foreach (($filterOptions['q20_options'] ?? []) as $opt20): ?>
                            <option value="<?= esc($opt20) ?>"><?= esc($opt20) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select id="tfFilterBlu" class="form-select" style="height: 36px; font-size: 0.8125rem;">
                        <option value="">Semua Kegiatan BLU (No. 61)</option>
                        <?php foreach (($filterOptions['q61_options'] ?? []) as $opt61): ?>
                            <option value="<?= esc($opt61) ?>"><?= esc($opt61) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <select id="tfFilterReadiness" class="form-select" style="height: 36px; font-size: 0.8125rem;">
                        <option value="all">Semua Tingkat Kesiapan</option>
                        <option value="lintas">Siap Bantu Lintas Unit</option>
                        <option value="blu">Tertarik Kontribusi BLU</option>
                        <option value="kapasitas">Punya Kapasitas Tambahan</option>
                        <option value="lintas_kapasitas">Siap Lintas Unit + Ada Kapasitas</option>
                    </select>
                </div>
            </div>

            <!-- Banner Filter Aktif Pencari Talenta -->
            <div id="activeTalentFilterBanner" class="d-none mb-3"></div>

            <div class="table-bento-scroll">
                <table class="table table-bento table-hover" id="tableTalentFinder">
                    <thead>
                        <tr>
                            <th>Pegawai &amp; Unit</th>
                            <th>Kompetensi &amp; Keahlian Tambahan</th>
                            <th>Kesiapan Lintas Unit (No. 27 &amp; 32)</th>
                            <th>Minat &amp; Bidang BLU (No. 60 &amp; 61)</th>
                            <th>Beban &amp; Kapasitas (No. 68 &amp; 70)</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($talentPool)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">Belum ada data talenta dari survei terkirim.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($talentPool as $tp): ?>
                                <?php
                                $tfKeyword = strtolower($tp['nama'] . ' ' . $tp['nip'] . ' ' . $tp['unit'] . ' ' . $tp['q22_keahlian'] . ' ' . implode(' ', $tp['q20_list']));
                                ?>
                                <tr
                                    class="talent-row"
                                    data-keyword="<?= esc($tfKeyword) ?>"
                                    data-q20='<?= esc(json_encode($tp['q20_list'], JSON_UNESCAPED_UNICODE)) ?>'
                                    data-q61='<?= esc(json_encode($tp['q61_list'], JSON_UNESCAPED_UNICODE)) ?>'
                                    data-q37='<?= esc(json_encode($tp['q37_list'] ?? [], JSON_UNESCAPED_UNICODE)) ?>'
                                    data-lintas="<?= $tp['is_lintas_ready'] ? '1' : '0' ?>"
                                    data-blu="<?= $tp['is_blu_ready'] ? '1' : '0' ?>"
                                    data-kapasitas="<?= $tp['has_capacity'] ? '1' : '0' ?>"
                                    data-kerja-lebih="<?= !empty($tp['is_kerja_lebih_ready']) ? '1' : '0' ?>"
                                    data-skill21="<?= !empty($tp['has_skill_21']) ? '1' : '0' ?>"
                                    data-blu-commit="<?= !empty($tp['is_blu_committed']) ? '1' : '0' ?>"
                                >
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($tp['nama']) ?></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($tp['kelompok']) ?> &middot; <?= esc($tp['unit']) ?></div>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <?php if (!empty($tp['is_kerja_lebih_ready'])): ?>
                                                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.625rem;">Siap Kerja Lebih</span>
                                            <?php endif; ?>
                                            <?php if (!empty($tp['has_skill_21'])): ?>
                                                <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.625rem;">Skill No. 21</span>
                                            <?php endif; ?>
                                            <?php if (!empty($tp['is_blu_committed'])): ?>
                                                <span class="badge bg-info-subtle text-info-emphasis rounded-pill" style="font-size: 0.625rem;">BLU Ready</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="max-width: 260px;">
                                        <div class="text-dark" style="font-size: 0.75rem;">
                                            <?= esc(!empty($tp['q20_list']) ? implode(', ', array_slice($tp['q20_list'], 0, 3)) : '-') ?>
                                            <?php if (count($tp['q20_list']) > 3): ?>
                                                <span class="text-primary fw-semibold">(+<?= count($tp['q20_list']) - 3 ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($tp['q22_keahlian'] !== ''): ?>
                                            <div class="text-primary fw-medium mt-1" style="font-size: 0.75rem;">
                                                + <?= esc(mb_strimwidth($tp['q22_keahlian'], 0, 70, '...')) ?>
                                                <?php if ($tp['q23_skor'] > 0): ?>
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-1 tabular-nums">Skor <?= (int) $tp['q23_skor'] ?>/5</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-medium <?= $tp['is_lintas_ready'] ? 'text-success' : 'text-secondary' ?>" style="font-size: 0.75rem;">
                                            <?= esc($tp['q27_lintas_unit']) ?>
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($tp['q32_jam']) ?></div>
                                    </td>
                                    <td style="max-width: 240px;">
                                        <div class="fw-medium <?= $tp['is_blu_ready'] ? 'text-primary' : 'text-secondary' ?>" style="font-size: 0.75rem;">
                                            <?= esc($tp['q60_minat_blu']) ?>
                                        </div>
                                        <?php if (!empty($tp['q61_list'])): ?>
                                            <div class="text-secondary" style="font-size: 0.75rem;">
                                                <?= esc(implode(', ', array_slice($tp['q61_list'], 0, 2))) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-dark" style="font-size: 0.75rem;">Beban: <strong><?= esc($tp['q68_beban']) ?></strong></div>
                                        <div class="text-secondary" style="font-size: 0.75rem;"><?= esc($tp['q70_kapasitas']) ?></div>
                                    </td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-action-compact btn-tactile btn-view-detail"
                                            data-respon-id="<?= (int) $tp['respon_id'] ?>"
                                        >
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 3: PERILAKU KERJA, SISTEM PENGHARGAAN & MASUKAN STRATEGIS       -->
    <!-- =================================================================== -->
    <div class="tab-pane fade" id="paneAspirasi" role="tabpanel">
        <div class="row g-3 mb-3 align-items-stretch">
            <!-- Rata-Rata Skor Perilaku Kerja (No. 43 - 55) -->
            <div class="col-12 col-lg-6 d-flex flex-column">
                <div class="card-bento-ecc h-100 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3 flex-shrink-0">
                        <div>
                            <h2 class="h6 fw-bold text-dark mb-0">Indeks Perilaku &amp; Inisiatif Kerja (No. 43&ndash;55)</h2>
                            <div class="text-secondary" style="font-size: 0.75rem;">Rata-rata skala 1,00 s.d. 5,00 dari responden terkirim.</div>
                        </div>
                        <span class="status-pill-compact bg-primary-subtle text-primary tabular-nums flex-shrink-0">
                            Rata-rata: <?= number_format((float) $kpi['overall_behavior_avg'], 2, ',', '.') ?> / 5,00
                        </span>
                    </div>

                    <div class="d-flex flex-column gap-2 flex-grow-1 custom-scroll" style="max-height: 540px; overflow-y: auto; padding-right: 6px;">
                        <?php foreach (($insights['likert_perilaku'] ?? []) as $lp): ?>
                            <div class="p-2 px-3 rounded-3 border" style="background: #f8fafc;">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <div class="text-dark" style="font-size: 0.8125rem;">
                                        <strong class="tabular-nums"><?= (int) $lp['no'] ?>.</strong> <?= esc($lp['label']) ?>
                                    </div>
                                    <span class="fw-bold text-primary tabular-nums flex-shrink-0" style="font-size: 0.8125rem;">
                                        <?= number_format((float) $lp['avg'], 2, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="progress rounded-pill" style="height: 6px; background: #e2e8f0;">
                                    <div class="progress-bar rounded-pill" style="width: <?= min(100, round(((float) $lp['avg'] / 5) * 100, 1)) ?>%; background: #1d4ed8;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Persepsi Sistem Penghargaan & Hambatan Utama -->
            <div class="col-12 col-lg-6 d-flex flex-column gap-3">
                <div class="card-bento-ecc flex-shrink-0">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                        <h2 class="h6 fw-bold text-dark mb-0">Persepsi Sistem Kinerja &amp; Komitmen BLU</h2>
                        <span class="badge rounded-pill bg-light text-secondary border fw-normal" style="font-size: 0.6875rem;">Skala 1&ndash;5</span>
                    </div>
                    <div class="text-secondary mb-3" style="font-size: 0.75rem;">
                        Penilaian keterkaitan capaian kerja, skema penghargaan, dan komitmen institusi.
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach (($insights['skor_penghargaan'] ?? []) as $sp): ?>
                            <div class="p-2 px-3 rounded-3 border" style="background: #f8fafc;">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <div class="text-dark" style="font-size: 0.8125rem;">
                                        <strong class="tabular-nums">No. <?= (int) $sp['no'] ?>:</strong> <?= esc($sp['label']) ?>
                                    </div>
                                    <span class="fw-bold tabular-nums flex-shrink-0" style="font-size: 0.8125rem; color: #0284c7;">
                                        <?= number_format((float) $sp['avg'], 2, ',', '.') ?>
                                    </span>
                                </div>
                                <div class="progress rounded-pill" style="height: 5px; background: #e2e8f0;">
                                    <div class="progress-bar rounded-pill" style="width: <?= min(100, round(((float) $sp['avg'] / 5) * 100, 1)) ?>%; background: #0284c7;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card-bento-ecc flex-grow-1 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1 flex-shrink-0">
                        <h2 class="h6 fw-bold text-dark mb-0">Hambatan Produktivitas (No. 57)</h2>
                        <span class="badge rounded-pill text-danger border border-danger-subtle fw-semibold" style="background: #fef2f2; padding: 4px 10px; font-size: 0.6875rem;">
                            Fokus Analitik 4
                        </span>
                    </div>
                    <div class="text-secondary mb-3 flex-shrink-0" style="font-size: 0.75rem;">
                        Peringkat faktor utama penghambat produktivitas kerja pegawai di lingkungan PKTJ.
                    </div>
                    <?php if (empty($insights['hambatan_ranked'])): ?>
                        <div class="text-secondary" style="font-size: 0.8125rem;">Belum ada data hambatan terkirim.</div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-2 flex-grow-1 custom-scroll" style="max-height: 250px; overflow-y: auto; padding-right: 6px;">
                            <?php foreach ($insights['hambatan_ranked'] as $hRank => $hItem): ?>
                                <div class="p-2 px-3 rounded-3 border" style="background: #f8fafc;">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1" style="font-size: 0.8125rem;">
                                        <div class="text-dark">
                                            <span class="badge bg-white text-dark border rounded-pill me-1 tabular-nums">#<?= $hRank + 1 ?></span>
                                            <?= esc($hItem['label']) ?>
                                        </div>
                                        <span class="fw-bold text-dark tabular-nums flex-shrink-0" style="font-size: 0.75rem;">
                                            <?= (int) $hItem['count'] ?> <span class="text-secondary fw-normal">(<?= (float) $hItem['percent'] ?>%)</span>
                                        </span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 6px; background: #e2e8f0;">
                                        <div class="progress-bar rounded-pill" style="width: <?= min(100, (float) $hItem['percent']) ?>%; background: #dc2626;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Masukan & Saran Kualitatif Pegawai (No. 83, 65, 58, 42, 26) -->
        <div class="card-bento-ecc">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-bold text-dark mb-0">Masukan Strategis &amp; Aspirasi Pengembangan SDM</h2>
                    <div class="text-secondary" style="font-size: 0.8125rem;">Rangkuman aspirasi, kebutuhan keahlian, dan saran terbuka dari seluruh responden terkirim.</div>
                </div>
                <div class="d-flex flex-wrap gap-1" id="essayTopicPills">
                    <button type="button" class="filter-pill-sub active" data-essay-q="83">Saran Pimpinan (No. 83)</button>
                    <button type="button" class="filter-pill-sub" data-essay-q="65">Peluang Layanan BLU (No. 65)</button>
                    <button type="button" class="filter-pill-sub" data-essay-q="58">Perbaikan Produktivitas (No. 58)</button>
                    <button type="button" class="filter-pill-sub" data-essay-q="42">Kompetensi Langka PKTJ (No. 42)</button>
                    <button type="button" class="filter-pill-sub" data-essay-q="26">Keahlian Ingin Ditingkatkan (No. 26)</button>
                </div>
            </div>

            <?php foreach ([83, 65, 58, 42, 26] as $eNo): ?>
                <?php $eList = $insights['aspirasi'][$eNo] ?? []; ?>
                <div class="essay-pane <?= $eNo === 83 ? '' : 'd-none' ?>" id="essayPane_<?= $eNo ?>">
                    <?php if (empty($eList)): ?>
                        <div class="text-center text-secondary py-4" style="font-size: 0.875rem;">Belum ada jawaban terbuka pada butir pertanyaan ini.</div>
                    <?php else: ?>
                        <div class="row g-2 custom-scroll" style="max-height: 440px; overflow-y: auto; padding-right: 4px;">
                            <?php foreach ($eList as $eItem): ?>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3 border h-100" style="background: #f8fafc;">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <span class="fw-semibold text-dark" style="font-size: 0.8125rem;"><?= esc($eItem['nama']) ?></span>
                                            <span class="badge bg-white text-secondary border rounded-pill" style="padding: 4px 10px; font-size: 0.6875rem;">
                                                <?= esc($eItem['kelompok']) ?> &middot; <?= esc($eItem['unit']) ?>
                                            </span>
                                        </div>
                                        <div class="text-dark" style="font-size: 0.8125rem; white-space: pre-line; line-height: 1.5;"><?= esc($eItem['teks']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL RINCIAN 83 JAWABAN RESPONDEN (ZERO-RELOAD)                        -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalDetailResponden" tabindex="-1" aria-labelledby="modalDetailRespondenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalDetailRespondenLabel">Rincian Jawaban Responden</h5>
                    <div id="detailRespondenSubtitle" class="text-secondary tabular-nums" style="font-size: 0.8125rem;">Memuat data...</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <input type="search" id="searchInsideDetailInput" class="form-control" style="height: 36px; font-size: 0.8125rem;" placeholder="Cari nomor atau kata kunci pertanyaan/jawaban...">
                </div>
                <div id="detailRespondenBody">
                    <div class="text-center text-secondary py-5">Memuat rincian jawaban...</div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2 d-flex justify-content-between">
                <div>
                    <?php if (!empty($canUnlock)): ?>
                        <button
                            type="button"
                            id="btnDetailModalUnlock"
                            class="btn btn-outline-secondary rounded-pill fw-semibold px-3 d-none btn-tactile btn-buka-kunci"
                            style="height: 36px; font-size: 0.8125rem;"
                        >
                            Buka Edit
                        </button>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-light border rounded-pill fw-semibold px-4" style="height: 36px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- FLOATING ACTION BAR PILIHAN PEGAWAI (BANDINGKAN / UNDUH EXCEL)          -->
<!-- ======================================================================= -->
<div id="floatingSelectionBar" class="d-none position-fixed bottom-0 start-50 translate-middle-x mb-4 shadow-lg rounded-pill px-4 py-2 border bg-white" style="z-index: 1045; min-width: 320px; max-width: 92vw; border-color: #cbd5e1 !important;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill tabular-nums px-2 py-1" id="selectedCountBadge" style="font-size: 0.8125rem;">0</span>
            <span class="fw-semibold text-dark" style="font-size: 0.8125rem;" id="selectedSummaryText">Pegawai Terpilih</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary rounded-pill fw-semibold btn-tactile d-inline-flex align-items-center gap-1" id="btnCompareSelected" style="height: 36px; font-size: 0.8125rem; padding: 4px 16px;">
                Bandingkan Isian
            </button>
            <?php if ($canExport): ?>
                <button type="button" class="btn btn-outline-success rounded-pill fw-semibold btn-tactile d-inline-flex align-items-center gap-1" id="btnExportSelectedExcel" style="height: 36px; font-size: 0.8125rem; padding: 4px 16px;">
                    Unduh Excel
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-secondary rounded-pill btn-tactile" id="btnClearSelected" style="height: 36px; font-size: 0.8125rem; padding: 4px 14px;">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL PERBANDINGAN ISIAN 83 BUTIR PEGAWAI (BERDAMPINGAN)                 -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalBandingkanResponden" tabindex="-1" aria-labelledby="modalBandingkanRespondenLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-xl-down modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <div class="me-auto">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalBandingkanRespondenLabel">Perbandingan Isian Pegawai</h5>
                    <div id="compareRespondenSubtitle" class="text-secondary tabular-nums" style="font-size: 0.8125rem;">Memuat data...</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($canExport): ?>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-semibold btn-tactile" id="btnModalCompareExportExcel" style="height: 32px; font-size: 0.75rem;">
                            Unduh Excel Pegawai Ini
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <input type="search" id="searchInsideCompareInput" class="form-control" style="height: 36px; font-size: 0.8125rem;" placeholder="Cari nomor butir atau kata kunci pertanyaan...">
                </div>
                <div id="compareRespondenContainer">
                    <div class="text-center text-secondary py-5">Memuat perbandingan isian...</div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2 d-flex justify-content-between">
                <div id="compareFooterMeta" class="text-secondary tabular-nums" style="font-size: 0.8125rem;"></div>
                <button type="button" class="btn btn-light border rounded-pill fw-semibold px-4" style="height: 36px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- MODAL DRILL-DOWN ANALITIK GRAFIK (PELATIHAN, KOMPETENSI, DLL)            -->
<!-- ======================================================================= -->
<div class="modal fade" id="modalDrilldownChart" tabindex="-1" aria-labelledby="modalDrilldownTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-sm" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalDrilldownTitle">Daftar Pegawai</h5>
                    <div class="text-secondary" id="modalDrilldownSubtitle" style="font-size: 0.8125rem;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        Ditemukan <strong id="modalDrilldownCount" class="text-dark tabular-nums">0</strong> pegawai pada kategori ini.
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="search" id="modalDrilldownSearch" class="form-control form-control-sm" placeholder="Cari nama atau unit..." style="height: 32px; width: 220px; font-size: 0.8125rem;">
                        <button type="button" id="btnCopyDrilldownList" class="btn btn-outline-secondary btn-action-compact btn-tactile" title="Salin daftar ke clipboard">
                            Salin
                        </button>
                    </div>
                </div>
                <div class="table-bento-scroll" style="max-height: 380px;">
                    <table class="table table-bento table-hover" id="tableDrilldownChart">
                        <thead>
                            <tr>
                                <th style="width: 44px;" class="text-center">No</th>
                                <th>Nama &amp; NIP</th>
                                <th>Unit Kerja</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                        <tbody id="modalDrilldownTbody">
                            <!-- Baris Pegawai Dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 btn-tactile fw-medium" data-bs-dismiss="modal" style="height: 36px; font-size: 0.8125rem;">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php if ($canManage): ?>
<div class="modal fade" id="modalDelegasiStaf" tabindex="-1" aria-labelledby="modalDelegasiStafLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalDelegasiStafLabel">Delegasi Akses Hasil Survei SDM</h5>
                    <div class="text-secondary" style="font-size: 0.8125rem;">
                        Berikan akses terbatas kepada staf untuk memantau hasil survei tanpa membuka menu Kepegawaian lainnya.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <form id="formTambahDelegasi" class="p-3 rounded-3 border mb-3" style="background: #f8fafc;">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <label for="selectDelegasiStaf" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8125rem;">Pilih Staf</label>
                            <select id="selectDelegasiStaf" class="form-select" required>
                                <option value="">-- Cari nama atau NIP staf --</option>
                                <?php foreach ($delegateCandidates as $cand): ?>
                                    <option value="<?= (int) $cand['id'] ?>">
                                        <?= esc($cand['nama_lengkap']) ?> (<?= esc($cand['nip'] ?: '-') ?> — <?= esc($cand['unit'] ?: '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="inputCatatanDelegasi" class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8125rem;">Catatan Tugas (Opsional)</label>
                            <input type="text" id="inputCatatanDelegasi" class="form-control" style="height: 38px; font-size: 0.8125rem;" maxlength="150" placeholder="Contoh: Rekapitulasi SDM KUK">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" id="btnSubmitDelegasi" class="btn btn-primary fw-semibold rounded-3 w-100 btn-tactile" style="height: 38px; font-size: 0.8125rem;">
                                Beri Akses
                            </button>
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="chkDelegasiCanExport" value="1" checked>
                        <label class="form-check-label text-secondary" for="chkDelegasiCanExport" style="font-size: 0.8125rem;">
                            Izinkan staf mengunduh rekapitulasi Excel
                        </label>
                    </div>
                </form>

                <div class="fw-semibold text-dark mb-2" style="font-size: 0.875rem;">Daftar Staf Penerima Delegasi Aktif</div>
                <div class="table-bento-scroll" style="max-height: 280px;">
                    <table class="table table-bento" id="tableActiveDelegates">
                        <thead>
                            <tr>
                                <th>Nama Staf</th>
                                <th>Unit Kerja</th>
                                <th>Hak Ekspor</th>
                                <th>Catatan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyActiveDelegates">
                            <?php if (empty($delegatesList)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-3">Belum ada staf yang didelegasikan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($delegatesList as $dg): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($dg['nama_lengkap']) ?></div>
                                            <div class="text-secondary tabular-nums" style="font-size: 0.75rem;"><?= esc($dg['nip'] ?: '-') ?></div>
                                        </td>
                                        <td><?= esc($dg['unit'] ?: '-') ?></td>
                                        <td>
                                            <span class="status-pill-compact <?= (int) $dg['can_export'] === 1 ? 'bg-success-subtle text-success' : 'bg-light text-secondary border' ?>">
                                                <?= (int) $dg['can_export'] === 1 ? 'Diizinkan' : 'Hanya Lihat' ?>
                                            </span>
                                        </td>
                                        <td class="text-secondary"><?= esc($dg['catatan'] ?: '-') ?></td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger btn-action-compact btn-tactile btn-revoke-delegasi"
                                                data-delegasi-id="<?= (int) $dg['id'] ?>"
                                                data-nama="<?= esc($dg['nama_lengkap']) ?>"
                                            >
                                                Cabut Akses
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    'use strict';

    const URLS = {
        detailResponden:  <?= json_encode(site_url('kepegawaian/survei-pemetaan/detail-responden')) ?>,
        compareResponden: <?= json_encode(site_url('kepegawaian/survei-pemetaan/compare-responden')) ?>,
        exportExcel:      <?= json_encode(site_url('kepegawaian/survei-pemetaan/export-excel')) ?>,
        storeDelegasi:    <?= json_encode(site_url('kepegawaian/survei-pemetaan/delegasi/store')) ?>,
        revokeDelegasi:   <?= json_encode(site_url('kepegawaian/survei-pemetaan/delegasi/revoke')) ?>,
        togglePeriode:    <?= json_encode(site_url('kepegawaian/survei-pemetaan/toggle-periode')) ?>,
        syncProfil:       <?= json_encode(site_url('kepegawaian/survei-pemetaan/sync-profil')) ?>,
        resetDraf:        <?= json_encode(site_url('kepegawaian/survei-pemetaan/reset-draf')) ?>,
        bukaKunci:        <?= json_encode(site_url('kepegawaian/survei-pemetaan/buka-kunci')) ?>
    };

    const CHARTS_DATA = <?= json_encode($charts, JSON_UNESCAPED_UNICODE) ?>;

    let csrfTokenName = <?= json_encode(csrf_token()) ?>;
    let csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || <?= json_encode(csrf_hash()) ?>;
    let chartsInitialized = false;

    function updateCsrf(data) {
        if (data && data.csrf_token) csrfTokenName = data.csrf_token;
        if (data && data.csrf_hash) {
            csrfHash = data.csrf_hash;
            const meta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
            if (meta) meta.setAttribute('content', csrfHash);
        }
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showDialog(opts) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: opts.title || 'Informasi',
                text: opts.text || '',
                icon: opts.icon || 'info',
                showCancelButton: !!opts.showCancel,
                confirmButtonText: opts.confirmText || 'OK',
                cancelButtonText: opts.cancelText || 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-secondary small mb-3',
                    confirmButton: 'btn ' + (opts.confirmClass || 'btn-primary') + ' rounded-pill px-4 py-2 fw-semibold ms-2 btn-tactile',
                    cancelButton: 'btn btn-light border rounded-pill px-4 py-2 fw-semibold btn-tactile'
                }
            });
        }
        if (opts.showCancel) {
            return Promise.resolve({ isConfirmed: window.confirm((opts.title ? opts.title + '\n' : '') + (opts.text || '')) });
        }
        window.alert((opts.title ? opts.title + '\n' : '') + (opts.text || ''));
        return Promise.resolve({ isConfirmed: true });
    }

    function postAction(url, params) {
        const body = new URLSearchParams();
        body.append(csrfTokenName, csrfHash);
        Object.keys(params).forEach(k => {
            if (params[k] !== undefined && params[k] !== null) {
                body.append(k, String(params[k]));
            }
        });

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash,
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString()
        }).then(async res => {
            let json = {};
            try {
                json = await res.json();
            } catch (e) {
                json = { status: 'error', message: 'Permintaan gagal diproses. Silakan coba lagi.' };
            }
            updateCsrf(json);
            return json;
        });
    }

    function renderDelegatesTable(delegates) {
        const tbody = document.getElementById('tbodyActiveDelegates');
        const badge = document.getElementById('badgeCountDelegasi');
        if (badge) badge.textContent = Array.isArray(delegates) ? delegates.length : 0;
        if (!tbody) return;

        if (!Array.isArray(delegates) || delegates.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary py-3">Belum ada staf yang didelegasikan.</td></tr>';
            return;
        }

        let html = '';
        delegates.forEach(dg => {
            const canExp = Number(dg.can_export) === 1;
            html += `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">${escapeHtml(dg.nama_lengkap)}</div>
                        <div class="text-secondary tabular-nums" style="font-size: 0.75rem;">${escapeHtml(dg.nip || '-')}</div>
                    </td>
                    <td>${escapeHtml(dg.unit || '-')}</td>
                    <td>
                        <span class="status-pill-compact ${canExp ? 'bg-success-subtle text-success' : 'bg-light text-secondary border'}">
                            ${canExp ? 'Diizinkan' : 'Hanya Lihat'}
                        </span>
                    </td>
                    <td class="text-secondary">${escapeHtml(dg.catatan || '-')}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-action-compact btn-tactile btn-revoke-delegasi" data-delegasi-id="${Number(dg.id)}" data-nama="${escapeHtml(dg.nama_lengkap)}">
                            Cabut Akses
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Select2 di dalam Modal Delegasi Staf
        if (window.jQuery && jQuery.fn.select2 && document.getElementById('selectDelegasiStaf')) {
            jQuery('#selectDelegasiStaf').select2({
                theme: 'bootstrap-5',
                dropdownParent: jQuery('#modalDelegasiStaf'),
                width: '100%',
                placeholder: '-- Cari nama atau NIP staf --'
            });
        }

        // Inisialisasi Chart.js ketika Tab 2 dibuka
        document.getElementById('tab-talenta-btn')?.addEventListener('shown.bs.tab', function () {
            initAnalyticsCharts();
        });
        document.getElementById('tab-talenta-btn')?.addEventListener('click', function () {
            setTimeout(initAnalyticsCharts, 80);
        });
        if (document.getElementById('paneTalenta')?.classList.contains('active') || document.getElementById('tab-talenta-btn')?.classList.contains('active')) {
            setTimeout(initAnalyticsCharts, 100);
        }

        // Salin Tautan Survei
        document.getElementById('btnCopyShareLink')?.addEventListener('click', async function () {
            const link = this.dataset.link || '';
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(link);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = link;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    ta.remove();
                }
                showDialog({
                    title: 'Tautan Disalin',
                    text: 'Tautan pengisian survei siap dibagikan kepada pegawai.',
                    icon: 'success'
                });
            } catch (e) {
                showDialog({
                    title: 'Tautan Survei',
                    text: link,
                    icon: 'info'
                });
            }
        });

        // Buka / Tutup Periode Survei
        document.getElementById('btnTogglePeriode')?.addEventListener('click', async function () {
            const currentlyOpen = this.dataset.open === '1';
            const nextOpen = currentlyOpen ? '0' : '1';
            const conf = await showDialog({
                title: currentlyOpen ? 'Tutup Periode Survei?' : 'Buka Periode Survei?',
                text: currentlyOpen
                    ? 'Pegawai tidak dapat mengisi survei selama periode ditutup.'
                    : 'Tautan pengisian survei akan kembali dapat diakses pegawai.',
                icon: 'question',
                showCancel: true,
                confirmText: currentlyOpen ? 'Ya, Tutup' : 'Ya, Buka'
            });
            if (!conf.isConfirmed) return;

            const res = await postAction(URLS.togglePeriode, { is_open: nextOpen });
            if (res.status === 'success') {
                window.location.reload();
            } else {
                showDialog({ title: 'Gagal', text: res.message || 'Gagal memperbarui status periode.', icon: 'error' });
            }
        });

        // Sinkronisasi Profil Pegawai ASN (Semua & Satuan)
        document.getElementById('btnSyncAllProfiles')?.addEventListener('click', async function () {
            const conf = await showDialog({
                title: 'Terapkan ke Profil?',
                text: 'Hanya kolom profil pegawai yang masih kosong yang akan dilengkapi.',
                icon: 'question',
                showCancel: true,
                confirmText: 'Ya, Terapkan'
            });
            if (!conf.isConfirmed) return;

            const res = await postAction(URLS.syncProfil, { mode: 'all' });
            if (res.status === 'success') {
                await showDialog({ title: 'Tersimpan', text: res.message, icon: 'success' });
                window.location.reload();
            } else {
                showDialog({ title: 'Gagal', text: res.message || 'Gagal menerapkan profil.', icon: 'error' });
            }
        });

        document.querySelectorAll('.btn-sync-single').forEach(btn => {
            btn.addEventListener('click', async function () {
                const rid = this.dataset.responId;
                const res = await postAction(URLS.syncProfil, { mode: 'single', respon_id: rid });
                if (res.status === 'success') {
                    await showDialog({ title: 'Tersimpan', text: res.message, icon: 'success' });
                    window.location.reload();
                } else {
                    showDialog({ title: 'Gagal', text: res.message || 'Gagal menerapkan profil.', icon: 'error' });
                }
            });
        });

        // Pencarian & Filter Status Daftar Responden (Tab 1)
        let activeStatusFilter = 'all';
        let activeRespondenUnitFilter = '';

        function renderRespondenUnitBanner() {
            const banner = document.getElementById('activeRespondenFilterBanner');
            if (!banner) return;
            if (!activeRespondenUnitFilter) {
                banner.classList.add('d-none');
                banner.innerHTML = '';
                return;
            }
            banner.classList.remove('d-none');
            banner.innerHTML = `
                <span class="active-filter-banner">
                    Unit Kerja: <strong>${escapeHtml(activeRespondenUnitFilter)}</strong>
                    <button type="button" class="btn-clear-filter" id="btnClearRespondenUnitFilter" title="Hapus filter unit">&times;</button>
                </span>
            `;
            document.getElementById('btnClearRespondenUnitFilter')?.addEventListener('click', function () {
                activeRespondenUnitFilter = '';
                renderRespondenUnitBanner();
                filterDaftarResponden();
            });
        }

        function filterDaftarResponden() {
            const kw = (document.getElementById('searchRespondenInput')?.value || '').trim().toLowerCase();
            document.querySelectorAll('#tableDaftarResponden .responden-row').forEach(tr => {
                const st = tr.dataset.status || '';
                const u  = tr.dataset.unit || '';
                const sr = tr.dataset.search || '';
                const matchSt = (activeStatusFilter === 'all' || st === activeStatusFilter);
                const matchU  = (activeRespondenUnitFilter === '' || u === activeRespondenUnitFilter);
                const matchKw = (kw === '' || sr.includes(kw));
                tr.classList.toggle('d-none', !(matchSt && matchU && matchKw));
            });
        }

        document.getElementById('searchRespondenInput')?.addEventListener('input', filterDaftarResponden);
        document.querySelectorAll('#respondenStatusPills .filter-pill-sub').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#respondenStatusPills .filter-pill-sub').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeStatusFilter = this.dataset.statusFilter || 'all';
                filterDaftarResponden();
            });
        });

        // Pencarian Pegawai Belum Mengisi
        let activeAsnBelumUnitFilter = '';
        function renderAsnBelumUnitBanner() {
            const banner = document.getElementById('activeAsnBelumFilterBanner');
            if (!banner) return;
            if (!activeAsnBelumUnitFilter) {
                banner.classList.add('d-none');
                banner.innerHTML = '';
                return;
            }
            banner.classList.remove('d-none');
            banner.innerHTML = `
                <span class="active-filter-banner">
                    Unit Kerja: <strong>${escapeHtml(activeAsnBelumUnitFilter)}</strong>
                    <button type="button" class="btn-clear-filter" id="btnClearAsnBelumUnitFilter" title="Hapus filter unit">&times;</button>
                </span>
            `;
            document.getElementById('btnClearAsnBelumUnitFilter')?.addEventListener('click', function () {
                activeAsnBelumUnitFilter = '';
                renderAsnBelumUnitBanner();
                filterAsnBelum();
            });
        }

        function filterAsnBelum() {
            const kw = (document.getElementById('searchAsnBelumInput')?.value || '').trim().toLowerCase();
            document.querySelectorAll('#tableAsnBelum .asn-belum-row').forEach(tr => {
                const u  = tr.dataset.unit || '';
                const sr = tr.dataset.search || '';
                const matchU  = (activeAsnBelumUnitFilter === '' || u === activeAsnBelumUnitFilter);
                const matchKw = (kw === '' || sr.includes(kw));
                tr.classList.toggle('d-none', !(matchU && matchKw));
            });
        }

        document.getElementById('searchAsnBelumInput')?.addEventListener('input', filterAsnBelum);

        // Klik Baris Rekapitulasi Unit Kerja (Interaktif)
        document.querySelectorAll('.unit-summary-row').forEach(tr => {
            tr.addEventListener('click', function () {
                const unit = this.dataset.unit || '';
                activeRespondenUnitFilter = unit;
                activeAsnBelumUnitFilter = unit;
                renderRespondenUnitBanner();
                renderAsnBelumUnitBanner();
                filterDaftarResponden();
                filterAsnBelum();
                document.getElementById('cardDaftarResponden')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Pencarian & Filter Kartu Keahlian Tambahan (No. 21)
        let activeKeahlianFilter = 'all';
        function applyKeahlianTambahanFilter() {
            const kw = (document.getElementById('searchKeahlianTambahanInput')?.value || '').trim().toLowerCase();
            document.querySelectorAll('#tableKeahlianTambahan .keahlian-row').forEach(tr => {
                const hasProd = tr.dataset.hasProduct === '1';
                const hasPort = tr.dataset.hasPortfolio === '1';
                const sr = tr.dataset.search || '';

                let matchFilter = true;
                if (activeKeahlianFilter === 'product') matchFilter = hasProd;
                else if (activeKeahlianFilter === 'portfolio') matchFilter = hasPort;

                const matchKw = (kw === '' || sr.includes(kw));
                tr.classList.toggle('d-none', !(matchFilter && matchKw));
            });
        }

        function setKeahlianFilter(type, label) {
            activeKeahlianFilter = type;
            document.querySelectorAll('.card-keahlian-filter').forEach(c => c.classList.remove('is-active-filter'));

            const banner = document.getElementById('activeKeahlianFilterBanner');
            if (type === 'all') {
                document.getElementById('cardKeahlianSemua')?.classList.add('is-active-filter');
                if (banner) { banner.classList.add('d-none'); banner.innerHTML = ''; }
            } else if (type === 'product') {
                document.getElementById('cardKeahlianProduk')?.classList.add('is-active-filter');
                if (banner) {
                    banner.classList.remove('d-none');
                    banner.innerHTML = `<span class="active-filter-banner">${escapeHtml(label)} <button type="button" class="btn-clear-filter" id="btnClearKeahlianActiveFilter" title="Hapus filter">&times;</button></span>`;
                    document.getElementById('btnClearKeahlianActiveFilter')?.addEventListener('click', () => setKeahlianFilter('all', 'Semua'));
                }
            } else if (type === 'portfolio') {
                document.getElementById('cardKeahlianPortofolio')?.classList.add('is-active-filter');
                if (banner) {
                    banner.classList.remove('d-none');
                    banner.innerHTML = `<span class="active-filter-banner">${escapeHtml(label)} <button type="button" class="btn-clear-filter" id="btnClearKeahlianActiveFilter" title="Hapus filter">&times;</button></span>`;
                    document.getElementById('btnClearKeahlianActiveFilter')?.addEventListener('click', () => setKeahlianFilter('all', 'Semua'));
                }
            }
            applyKeahlianTambahanFilter();
            document.getElementById('tableKeahlianTambahan')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        document.getElementById('cardKeahlianSemua')?.addEventListener('click', () => setKeahlianFilter('all', 'Semua'));
        document.getElementById('cardKeahlianProduk')?.addEventListener('click', () => setKeahlianFilter('product', 'Pernah Hasilkan Produk/Jasa (No. 24)'));
        document.getElementById('cardKeahlianPortofolio')?.addEventListener('click', () => setKeahlianFilter('portfolio', 'Memiliki Portofolio / Bukti (No. 25)'));
        document.getElementById('searchKeahlianTambahanInput')?.addEventListener('input', applyKeahlianTambahanFilter);

        // Filter & Pencarian Kontributor BLU (Pak KBU)
        let activeBluTierFilter = 'all';
        function applyBluContributorFilters() {
            const kw = (document.getElementById('searchKontributorBluInput')?.value || '').trim().toLowerCase();
            const fSvc = document.getElementById('bluFilterServiceSelect')?.value || '';

            document.querySelectorAll('#tableKontributorBlu .blu-contributor-row').forEach(tr => {
                const tier = tr.dataset.tier || '';
                const sr = tr.dataset.search || '';
                let svcArr = [];
                try { svcArr = JSON.parse(tr.dataset.services || '[]'); } catch (e) {}

                const matchTier = (activeBluTierFilter === 'all' || tier === activeBluTierFilter);
                const matchSvc = (fSvc === '' || svcArr.includes(fSvc));
                const matchKw = (kw === '' || sr.includes(kw));

                tr.classList.toggle('d-none', !(matchTier && matchSvc && matchKw));
            });
        }

        document.getElementById('searchKontributorBluInput')?.addEventListener('input', applyBluContributorFilters);
        document.getElementById('bluFilterServiceSelect')?.addEventListener('change', applyBluContributorFilters);
        document.querySelectorAll('#bluTierFilterPills .filter-pill-sub').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#bluTierFilterPills .filter-pill-sub').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeBluTierFilter = this.dataset.bluTier || 'all';
                applyBluContributorFilters();
            });
        });

        // Filter Pencari Talenta (Tab 2)
        let activeTalentQuickFilter = 'all';
        function applyTalentFinderFilters() {
            const kw = (document.getElementById('tfSearchKeyword')?.value || '').trim().toLowerCase();
            const f20 = document.getElementById('tfFilterKompetensi')?.value || '';
            const f61 = document.getElementById('tfFilterBlu')?.value || '';
            const fReady = document.getElementById('tfFilterReadiness')?.value || 'all';

            document.querySelectorAll('#tableTalentFinder .talent-row').forEach(tr => {
                const matchKw = (kw === '' || (tr.dataset.keyword || '').includes(kw));
                let q20Arr = [];
                let q61Arr = [];
                try { q20Arr = JSON.parse(tr.dataset.q20 || '[]'); } catch (e) {}
                try { q61Arr = JSON.parse(tr.dataset.q61 || '[]'); } catch (e) {}

                const match20 = (f20 === '' || q20Arr.includes(f20));
                const match61 = (f61 === '' || q61Arr.includes(f61));

                let matchReady = true;
                if (fReady === 'lintas') matchReady = tr.dataset.lintas === '1';
                else if (fReady === 'blu') matchReady = tr.dataset.blu === '1';
                else if (fReady === 'kapasitas') matchReady = tr.dataset.kapasitas === '1';
                else if (fReady === 'lintas_kapasitas') matchReady = (tr.dataset.lintas === '1' && tr.dataset.kapasitas === '1');

                let matchQuick = true;
                if (activeTalentQuickFilter === 'kerja_lebih') matchQuick = tr.dataset.kerjaLebih === '1';
                else if (activeTalentQuickFilter === 'skill21') matchQuick = tr.dataset.skill21 === '1';
                else if (activeTalentQuickFilter === 'lintas') matchQuick = tr.dataset.lintas === '1';
                else if (activeTalentQuickFilter === 'blu_commit') matchQuick = tr.dataset.bluCommit === '1';

                tr.classList.toggle('d-none', !(matchKw && match20 && match61 && matchReady && matchQuick));
            });
        }

        document.querySelectorAll('#talentQuickFilterPills .filter-pill-sub').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#talentQuickFilterPills .filter-pill-sub').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeTalentQuickFilter = this.dataset.quickFilter || 'all';
                applyTalentFinderFilters();
            });
        });

        ['tfSearchKeyword', 'tfFilterKompetensi', 'tfFilterBlu', 'tfFilterReadiness'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', applyTalentFinderFilters);
                el.addEventListener('change', applyTalentFinderFilters);
            }
        });

        // =====================================================================
        // INTERAKTIVITAS KARTU, GRAFIK & DRILL-DOWN (CROSS-FILTERING)
        // =====================================================================
        function renderTalentActiveFilterBanner(labelType, labelValue, onClear) {
            const banner = document.getElementById('activeTalentFilterBanner');
            if (!banner) return;
            if (!labelType || !labelValue) {
                banner.classList.add('d-none');
                banner.innerHTML = '';
                return;
            }
            banner.classList.remove('d-none');
            banner.innerHTML = `
                <span class="active-filter-banner">
                    ${escapeHtml(labelType)}: <strong>${escapeHtml(labelValue)}</strong>
                    <button type="button" class="btn-clear-filter" id="btnClearTalentActiveFilter" title="Hapus filter">&times;</button>
                </span>
            `;
            document.getElementById('btnClearTalentActiveFilter')?.addEventListener('click', onClear);
        }

        function renderBluActiveFilterBanner(labelType, labelValue, onClear) {
            const banner = document.getElementById('activeBluFilterBanner');
            if (!banner) return;
            if (!labelType || !labelValue) {
                banner.classList.add('d-none');
                banner.innerHTML = '';
                return;
            }
            banner.classList.remove('d-none');
            banner.innerHTML = `
                <span class="active-filter-banner">
                    ${escapeHtml(labelType)}: <strong>${escapeHtml(labelValue)}</strong>
                    <button type="button" class="btn-clear-filter" id="btnClearBluActiveFilter" title="Hapus filter">&times;</button>
                </span>
            `;
            document.getElementById('btnClearBluActiveFilter')?.addEventListener('click', onClear);
        }

        // Klik Grafik 1: Kompetensi Utama (Cross-filter ke Tabel Talenta)
        function handleKompetensiChartClick(fullKey) {
            const select = document.getElementById('tfFilterKompetensi');
            if (select) {
                let matched = false;
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === fullKey) {
                        select.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    const searchInp = document.getElementById('tfSearchKeyword');
                    if (searchInp) searchInp.value = fullKey;
                }
            }
            applyTalentFinderFilters();
            renderTalentActiveFilterBanner('Kompetensi', fullKey, function () {
                if (select) select.value = '';
                const searchInp = document.getElementById('tfSearchKeyword');
                if (searchInp) searchInp.value = '';
                applyTalentFinderFilters();
                renderTalentActiveFilterBanner(null);
            });
            document.getElementById('tableTalentFinder')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Klik Grafik 2: Layanan BLU (Cross-filter ke Tabel Kontributor BLU)
        function handleBluChartClick(fullKey) {
            const select = document.getElementById('bluFilterServiceSelect');
            if (select) {
                let matched = false;
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === fullKey) {
                        select.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    const searchInp = document.getElementById('searchKontributorBluInput');
                    if (searchInp) searchInp.value = fullKey;
                }
            }
            applyBluContributorFilters();
            renderBluActiveFilterBanner('Layanan BLU', fullKey, function () {
                if (select) select.value = '';
                const searchInp = document.getElementById('searchKontributorBluInput');
                if (searchInp) searchInp.value = '';
                applyBluContributorFilters();
                renderBluActiveFilterBanner(null);
            });
            document.getElementById('tableKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Klik Grafik 3: Kapasitas Tambahan (Cross-filter ke Tabel Talenta)
        function handleKapasitasChartClick(selectedLabel) {
            const select = document.getElementById('tfFilterReadiness');
            if (select) {
                select.value = 'kapasitas';
            }
            applyTalentFinderFilters();
            renderTalentActiveFilterBanner('Kapasitas', selectedLabel, function () {
                if (select) select.value = 'all';
                applyTalentFinderFilters();
                renderTalentActiveFilterBanner(null);
            });
            document.getElementById('tableTalentFinder')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Klik Grafik 4: Kebutuhan Pelatihan (Modal Drill-down Pegawai)
        let currentDrilldownList = [];
        function handlePelatihanChartClick(fullKey) {
            const list = (CHARTS_DATA.pelatihan_pegawai && CHARTS_DATA.pelatihan_pegawai[fullKey]) ? CHARTS_DATA.pelatihan_pegawai[fullKey] : [];
            currentDrilldownList = list;

            const modalEl = document.getElementById('modalDrilldownChart');
            if (!modalEl) return;

            document.getElementById('modalDrilldownTitle').textContent = 'Daftar Pegawai yang Membutuhkan Pelatihan';
            document.getElementById('modalDrilldownSubtitle').textContent = fullKey;
            document.getElementById('modalDrilldownCount').textContent = list.length;
            const searchInp = document.getElementById('modalDrilldownSearch');
            if (searchInp) searchInp.value = '';

            renderDrilldownTable(list);

            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        }

        function renderDrilldownTable(list) {
            const tbody = document.getElementById('modalDrilldownTbody');
            if (!tbody) return;

            if (!list || list.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary py-4">Belum ada pegawai pada kategori ini.</td></tr>';
                return;
            }

            let html = '';
            list.forEach((p, idx) => {
                const searchVal = ((p.nama || '') + ' ' + (p.nip || '') + ' ' + (p.unit || '') + ' ' + (p.jabatan || '')).toLowerCase();
                html += `
                    <tr class="drilldown-row" data-search="${escapeHtml(searchVal)}">
                        <td class="text-center tabular-nums text-secondary">${idx + 1}</td>
                        <td>
                            <div class="fw-semibold text-dark">${escapeHtml(p.nama)}</div>
                            <div class="text-secondary tabular-nums" style="font-size: 0.75rem;">${escapeHtml(p.nip || '-')}</div>
                        </td>
                        <td>${escapeHtml(p.unit || '-')}</td>
                        <td class="text-secondary">${escapeHtml(p.jabatan || '-')}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        document.getElementById('modalDrilldownSearch')?.addEventListener('input', function () {
            const kw = this.value.trim().toLowerCase();
            document.querySelectorAll('#modalDrilldownTbody .drilldown-row').forEach(tr => {
                const sr = tr.dataset.search || '';
                tr.classList.toggle('d-none', kw !== '' && !sr.includes(kw));
            });
        });

        document.getElementById('btnCopyDrilldownList')?.addEventListener('click', async function () {
            if (!currentDrilldownList || currentDrilldownList.length === 0) return;
            const topic = document.getElementById('modalDrilldownSubtitle')?.textContent || '';
            let txt = `Daftar Kebutuhan Pelatihan: ${topic}\nTotal: ${currentDrilldownList.length} Pegawai\n\n`;
            currentDrilldownList.forEach((p, idx) => {
                txt += `${idx + 1}. ${p.nama} (${p.nip || '-'}) - ${p.unit || '-'} - ${p.jabatan || '-'}\n`;
            });
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(txt);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = txt;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                showDialog({ title: 'Tersalin', text: 'Daftar nama berhasil disalin ke clipboard.', icon: 'success' });
            } catch (e) {
                showDialog({ title: 'Gagal', text: 'Gagal menyalin data.', icon: 'error' });
            }
        });

        // =================================================================
        // INISIALISASI GRAFIK CHART.JS (PETA TALENTA LINTAS UNIT & BLU)
        // =================================================================
        let activeAnalyticsCharts = {};

        function initAnalyticsCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initAnalyticsCharts, 120);
                return;
            }
            if (chartsInitialized) return;

            function buildHorizontalBar(canvasId, objMap, barColor, onBarClick) {
                const el = document.getElementById(canvasId);
                if (!el) return;

                if (activeAnalyticsCharts[canvasId]) {
                    try { activeAnalyticsCharts[canvasId].destroy(); } catch (e) {}
                    delete activeAnalyticsCharts[canvasId];
                }

                const fullKeys = Object.keys(objMap || {});
                const labels = fullKeys.map(l => l.length > 38 ? l.slice(0, 36) + '...' : l);
                const values = Object.values(objMap || {});
                activeAnalyticsCharts[canvasId] = new Chart(el, {
                    type: 'bar',
                    data: {
                        labels: labels.length ? labels : ['Belum ada data'],
                        datasets: [{
                            label: 'Responden',
                            data: values.length ? values : [0],
                            backgroundColor: barColor,
                            borderRadius: 6,
                            maxBarThickness: 22
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterBody: () => onBarClick ? ['Klik untuk melihat / menyaring data'] : []
                                }
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 } },
                            y: { ticks: { font: { size: 11 } } }
                        },
                        onClick: (evt, elements, chart) => {
                            if (!elements || elements.length === 0 || !onBarClick) return;
                            const idx = elements[0].index;
                            const fullKey = fullKeys[idx] || chart.data.labels[idx];
                            if (fullKey && fullKey !== 'Belum ada data') {
                                onBarClick(fullKey);
                            }
                        },
                        onHover: (evt, elements) => {
                            if (evt.native && evt.native.target) {
                                evt.native.target.style.cursor = (elements && elements.length > 0 && onBarClick) ? 'pointer' : 'default';
                            }
                        }
                    }
                });
            }

            buildHorizontalBar('chartKompetensiCanvas', CHARTS_DATA.top_kompetensi, '#1d4ed8', handleKompetensiChartClick);
            buildHorizontalBar('chartBluCanvas', CHARTS_DATA.top_blu, '#0284c7', handleBluChartClick);
            buildHorizontalBar('chartPelatihanCanvas', CHARTS_DATA.top_pelatihan, '#4f46e5', handlePelatihanChartClick);

            const kapEl = document.getElementById('chartKapasitasCanvas');
            if (kapEl) {
                if (activeAnalyticsCharts['chartKapasitasCanvas']) {
                    try { activeAnalyticsCharts['chartKapasitasCanvas'].destroy(); } catch (e) {}
                    delete activeAnalyticsCharts['chartKapasitasCanvas'];
                }
                const kapMap = CHARTS_DATA.kapasitas || {};
                const kapKeys = Object.keys(kapMap);
                const hasData = Object.values(kapMap).some(v => Number(v) > 0);
                activeAnalyticsCharts['chartKapasitasCanvas'] = new Chart(kapEl, {
                    type: 'doughnut',
                    data: {
                        labels: hasData ? kapKeys : ['Belum ada data'],
                        datasets: [{
                            data: hasData ? Object.values(kapMap) : [1],
                            backgroundColor: hasData ? ['#16a34a', '#2563eb', '#0ea5e9', '#f59e0b', '#94a3b8'] : ['#e2e8f0'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, font: { size: 11 } }
                            },
                            tooltip: {
                                callbacks: {
                                    afterBody: () => hasData ? ['Klik untuk menyaring kapasitas di tabel talenta'] : []
                                }
                            }
                        },
                        onClick: (evt, elements) => {
                            if (!elements || elements.length === 0 || !hasData) return;
                            const idx = elements[0].index;
                            const selectedLabel = kapKeys[idx];
                            if (selectedLabel) {
                                handleKapasitasChartClick(selectedLabel);
                            }
                        },
                        onHover: (evt, elements) => {
                            if (evt.native && evt.native.target) {
                                evt.native.target.style.cursor = (elements && elements.length > 0 && hasData) ? 'pointer' : 'default';
                            }
                        }
                    }
                });
            }

            chartsInitialized = true;
        }

        // Interaksi Klik 4 Kartu KPI Kontributor BLU
        document.getElementById('cardBluTier1')?.addEventListener('click', function () {
            document.querySelector('#bluTierFilterPills [data-blu-tier="1"]')?.click();
            document.getElementById('tableKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('cardBluTarget')?.addEventListener('click', function () {
            const inp = document.getElementById('searchKontributorBluInput');
            if (inp) inp.value = 'target';
            applyBluContributorFilters();
            renderBluActiveFilterBanner('Status', 'Siap Diberi Target BLU (No. 66)', function () {
                if (inp) inp.value = '';
                applyBluContributorFilters();
                renderBluActiveFilterBanner(null);
            });
            document.getElementById('tableKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('cardBluMitra')?.addEventListener('click', function () {
            const inp = document.getElementById('searchKontributorBluInput');
            if (inp) inp.value = 'mitra';
            applyBluContributorFilters();
            renderBluActiveFilterBanner('Status', 'Bersedia Cari Mitra (No. 63)', function () {
                if (inp) inp.value = '';
                applyBluContributorFilters();
                renderBluActiveFilterBanner(null);
            });
            document.getElementById('tableKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('cardBluExperience')?.addEventListener('click', function () {
            const inp = document.getElementById('searchKontributorBluInput');
            if (inp) inp.value = 'bisnis';
            applyBluContributorFilters();
            renderBluActiveFilterBanner('Status', 'Pengalaman Bisnis / Relasi (No. 62 & 64)', function () {
                if (inp) inp.value = '';
                applyBluContributorFilters();
                renderBluActiveFilterBanner(null);
            });
            document.getElementById('tableKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // Interaksi Klik 4 Kartu KPI Eksekutif Baris Teratas
        document.getElementById('cardKpiKeterisian')?.addEventListener('click', function (e) {
            if (e.target.closest('#btnFilterKpiDraft') || e.target.closest('#btnFilterKpiBelum')) return;
            const tabBtn = document.getElementById('tab-monitoring-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            document.getElementById('cardDaftarResponden')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('btnFilterKpiDraft')?.addEventListener('click', function (e) {
            e.stopPropagation();
            const tabBtn = document.getElementById('tab-monitoring-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            document.querySelector('#respondenStatusPills [data-status-filter="draft"]')?.click();
            document.getElementById('cardDaftarResponden')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.getElementById('btnFilterKpiBelum')?.addEventListener('click', function (e) {
            e.stopPropagation();
            const tabBtn = document.getElementById('tab-monitoring-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            const cardBelum = document.getElementById('cardAsnBelum');
            if (cardBelum) {
                cardBelum.scrollIntoView({ behavior: 'smooth', block: 'start' });
                cardBelum.classList.remove('pulse-highlight');
                void cardBelum.offsetWidth;
                cardBelum.classList.add('pulse-highlight');
            }
        });

        document.getElementById('cardKpiKerjaLebih')?.addEventListener('click', function () {
            const tabBtn = document.getElementById('tab-talenta-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(initAnalyticsCharts, 80);
            setTimeout(() => {
                document.querySelector('#talentQuickFilterPills [data-quick-filter="kerja_lebih"]')?.click();
                document.getElementById('tableTalentFinder')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 120);
        });

        document.getElementById('cardKpiLintasUnit')?.addEventListener('click', function () {
            const tabBtn = document.getElementById('tab-talenta-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(initAnalyticsCharts, 80);
            setTimeout(() => {
                document.querySelector('#talentQuickFilterPills [data-quick-filter="lintas"]')?.click();
                document.getElementById('tableTalentFinder')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 120);
        });

        document.getElementById('cardKpiBlu')?.addEventListener('click', function () {
            const tabBtn = document.getElementById('tab-talenta-btn');
            if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            setTimeout(initAnalyticsCharts, 80);
            setTimeout(() => {
                document.getElementById('secMuaraKontributorBlu')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 120);
        });

        // Sub-Pill Masukan Esai (Tab 3)
        document.querySelectorAll('#essayTopicPills .filter-pill-sub').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#essayTopicPills .filter-pill-sub').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const qNo = this.dataset.essayQ;
                document.querySelectorAll('.essay-pane').forEach(p => {
                    p.classList.toggle('d-none', p.id !== ('essayPane_' + qNo));
                });
            });
        });

        // Klik Rincian Jawaban Responden (Modal Zero-Reload)
        document.addEventListener('click', async function (e) {
            const viewBtn = e.target.closest('.btn-view-detail');
            if (viewBtn) {
                const rid = viewBtn.dataset.responId;
                const modalEl = document.getElementById('modalDetailResponden');
                const bodyEl = document.getElementById('detailRespondenBody');
                const subEl = document.getElementById('detailRespondenSubtitle');
                const searchInp = document.getElementById('searchInsideDetailInput');
                const modalUnlockBtn = document.getElementById('btnDetailModalUnlock');

                if (searchInp) searchInp.value = '';
                if (modalUnlockBtn) modalUnlockBtn.classList.add('d-none');
                if (bodyEl) bodyEl.innerHTML = '<div class="text-center text-secondary py-5">Memuat rincian jawaban...</div>';
                if (modalEl && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }

                try {
                    const res = await fetch(URLS.detailResponden + '?respon_id=' + encodeURIComponent(rid), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(r => r.json());
                    updateCsrf(res);

                    if (res.status !== 'success') {
                        if (bodyEl) bodyEl.innerHTML = `<div class="alert alert-danger">${escapeHtml(res.message || 'Gagal memuat rincian.')}</div>`;
                        return;
                    }

                    const rp = res.respon;
                    if (subEl) {
                        subEl.textContent = `${rp.nama} · ${rp.nomor_identitas} · ${rp.status_kepegawaian} · ${rp.unit} (${rp.progress_percent}%)`;
                    }
                    if (modalUnlockBtn && rp.status === 'submitted') {
                        modalUnlockBtn.dataset.responId = String(rp.id);
                        modalUnlockBtn.dataset.nama = rp.nama || 'pegawai ini';
                        modalUnlockBtn.classList.remove('d-none');
                    }

                    let html = '';
                    (res.sections || []).forEach(sec => {
                        html += `
                            <div class="mb-3 p-3 rounded-3 border detail-section-card">
                                <div class="fw-bold text-primary mb-2 pb-1 border-bottom" style="font-size: 0.8125rem;">
                                    BAGIAN ${escapeHtml(sec.roman)} — ${escapeHtml(sec.short_title)}
                                </div>
                        `;
                        (sec.items || []).forEach(it => {
                            if (!it.applicable && it.display === 'Tidak berlaku') return;
                            const rowText = (it.no + ' ' + it.label + ' ' + it.display).toLowerCase();
                            html += `
                                <div class="detail-q-row" data-qsearch="${escapeHtml(rowText)}">
                                    <div class="text-secondary mb-1" style="font-size: 0.8125rem;">
                                        <strong class="text-dark tabular-nums">${Number(it.no)}.</strong> ${escapeHtml(it.label)}
                                    </div>
                                    <div class="fw-medium text-dark ps-3" style="font-size: 0.8125rem; white-space: pre-line;">${escapeHtml(it.display)}</div>
                                </div>
                            `;
                        });
                        html += `</div>`;
                    });
                    if (bodyEl) bodyEl.innerHTML = html;
                } catch (err) {
                    if (bodyEl) bodyEl.innerHTML = '<div class="alert alert-danger">Gagal memuat rincian jawaban.</div>';
                }
                return;
            }

            // Reset Draf
            const resetBtn = e.target.closest('.btn-reset-draf');
            if (resetBtn) {
                const rid = resetBtn.dataset.responId;
                const nm = resetBtn.dataset.nama || 'pegawai ini';
                const conf = await showDialog({
                    title: 'Reset Draf?',
                    text: 'Draf milik ' + nm + ' akan dihapus agar dapat mulai mengisi ulang.',
                    icon: 'warning',
                    showCancel: true,
                    confirmText: 'Ya, Reset',
                    confirmClass: 'btn-warning text-dark'
                });
                if (!conf.isConfirmed) return;

                const res = await postAction(URLS.resetDraf, { respon_id: rid });
                if (res.status === 'success') {
                    await showDialog({ title: 'Draf Direset', text: res.message, icon: 'success' });
                    window.location.reload();
                } else {
                    showDialog({ title: 'Gagal', text: res.message || 'Gagal mereset draf.', icon: 'error' });
                }
                return;
            }

            // Buka Edit Survei Terkirim
            const unlockBtn = e.target.closest('.btn-buka-kunci');
            if (unlockBtn) {
                const rid = unlockBtn.dataset.responId;
                const nm = unlockBtn.dataset.nama || 'pegawai ini';
                const conf = await showDialog({
                    title: 'Buka Edit Survei?',
                    text: 'Survei milik ' + nm + ' akan dibuka kembali agar dapat diedit.',
                    icon: 'question',
                    showCancel: true,
                    confirmText: 'Ya, Buka Edit'
                });
                if (!conf.isConfirmed) return;

                const res = await postAction(URLS.bukaKunci, { respon_id: rid });
                if (res.status === 'success') {
                    await showDialog({ title: 'Edit Dibuka', text: res.message, icon: 'success' });
                    window.location.reload();
                } else {
                    showDialog({ title: 'Gagal', text: res.message || 'Gagal membuka edit survei.', icon: 'error' });
                }
                return;
            }

            // Cabut Delegasi Staf
            const revBtn = e.target.closest('.btn-revoke-delegasi');
            if (revBtn) {
                const did = revBtn.dataset.delegasiId;
                const nm = revBtn.dataset.nama || 'staf ini';
                const conf = await showDialog({
                    title: 'Cabut Akses Staf?',
                    text: 'Akses hasil survei untuk ' + nm + ' akan dinonaktifkan.',
                    icon: 'warning',
                    showCancel: true,
                    confirmText: 'Ya, Cabut',
                    confirmClass: 'btn-danger'
                });
                if (!conf.isConfirmed) return;

                const res = await postAction(URLS.revokeDelegasi, { delegasi_id: did });
                if (res.status === 'success') {
                    renderDelegatesTable(res.delegates || []);
                    showDialog({ title: 'Akses Dicabut', text: res.message, icon: 'success' });
                } else {
                    showDialog({ title: 'Gagal', text: res.message || 'Gagal mencabut akses.', icon: 'error' });
                }
            }
        });

        // Pencarian Cepat di Dalam Modal Rincian 83 Jawaban
        document.getElementById('searchInsideDetailInput')?.addEventListener('input', function () {
            const kw = this.value.trim().toLowerCase();
            document.querySelectorAll('#detailRespondenBody .detail-q-row').forEach(row => {
                const txt = row.dataset.qsearch || '';
                row.classList.toggle('d-none', kw !== '' && !txt.includes(kw));
            });
        });

        // Submit Form Tambah Delegasi Staf
        document.getElementById('formTambahDelegasi')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const uid = document.getElementById('selectDelegasiStaf')?.value || '';
            const catatan = document.getElementById('inputCatatanDelegasi')?.value || '';
            const canExp = document.getElementById('chkDelegasiCanExport')?.checked ? '1' : '0';

            if (!uid) {
                showDialog({ title: 'Pilih Staf', text: 'Pilih staf yang akan diberikan akses terlebih dahulu.', icon: 'warning' });
                return;
            }

            const btn = document.getElementById('btnSubmitDelegasi');
            if (btn) btn.disabled = true;
            try {
                const res = await postAction(URLS.storeDelegasi, {
                    user_id: uid,
                    can_export: canExp,
                    catatan: catatan
                });

                if (res.status === 'success') {
                    renderDelegatesTable(res.delegates || []);
                    document.getElementById('inputCatatanDelegasi').value = '';
                    if (window.jQuery && jQuery.fn.select2) {
                        jQuery('#selectDelegasiStaf').val('').trigger('change');
                    }
                    showDialog({ title: 'Akses Diberikan', text: res.message, icon: 'success' });
                } else {
                    showDialog({ title: 'Gagal', text: res.message || 'Gagal menyimpan delegasi.', icon: 'error' });
                }
            } finally {
                if (btn) btn.disabled = false;
            }
        });

        // ===================================================================
        // PEMILIHAN PEGAWAI, FLOATING BAR & PERBANDINGAN BERDAMPINGAN (1-83)
        // ===================================================================
        const selectedRespondenMap = new Map();
        let currentComparedIds = [];

        function updateSelectionUI() {
            const count = selectedRespondenMap.size;
            const bar = document.getElementById('floatingSelectionBar');
            const badge = document.getElementById('selectedCountBadge');
            const summary = document.getElementById('selectedSummaryText');

            if (badge) badge.textContent = count;

            if (count > 0) {
                if (bar) bar.classList.remove('d-none');
                const names = Array.from(selectedRespondenMap.values()).map(v => v.nama);
                if (names.length === 1) {
                    summary.textContent = names[0];
                } else if (names.length === 2) {
                    summary.textContent = names.join(' & ');
                } else {
                    summary.textContent = count + ' Pegawai Terpilih (' + names.slice(0, 2).join(', ') + ', +' + (count - 2) + ')';
                }
            } else {
                if (bar) bar.classList.add('d-none');
            }

            // Sync all checkboxes on page
            document.querySelectorAll('.check-responden-item').forEach(chk => {
                chk.checked = selectedRespondenMap.has(Number(chk.value));
            });

            // Sync select-all checkboxes
            const visibleResponden = Array.from(document.querySelectorAll('#tableDaftarResponden .responden-row:not(.d-none) .check-responden-item'));
            const checkAllResp = document.getElementById('checkAllResponden');
            if (checkAllResp && visibleResponden.length > 0) {
                const allChecked = visibleResponden.every(chk => chk.checked);
                const someChecked = visibleResponden.some(chk => chk.checked);
                checkAllResp.checked = allChecked;
                checkAllResp.indeterminate = someChecked && !allChecked;
            } else if (checkAllResp) {
                checkAllResp.checked = false;
                checkAllResp.indeterminate = false;
            }

            const visibleBlu = Array.from(document.querySelectorAll('#tableKontributorBlu .blu-contributor-row:not(.d-none) .check-responden-item'));
            const checkAllBlu = document.getElementById('checkAllBlu');
            if (checkAllBlu && visibleBlu.length > 0) {
                const allChecked = visibleBlu.every(chk => chk.checked);
                const someChecked = visibleBlu.some(chk => chk.checked);
                checkAllBlu.checked = allChecked;
                checkAllBlu.indeterminate = someChecked && !allChecked;
            } else if (checkAllBlu) {
                checkAllBlu.checked = false;
                checkAllBlu.indeterminate = false;
            }
        }

        // Checkbox individual change
        document.addEventListener('change', function (e) {
            if (e.target.matches('.check-responden-item')) {
                const id = Number(e.target.value);
                if (e.target.checked) {
                    if (selectedRespondenMap.size >= 10) {
                        e.target.checked = false;
                        showDialog({
                            title: 'Batas Maksimal',
                            text: 'Maksimal 10 pegawai untuk perbandingan berdampingan sekaligus.',
                            icon: 'info'
                        });
                        return;
                    }
                    selectedRespondenMap.set(id, {
                        id: id,
                        nama: e.target.dataset.nama || 'Pegawai #' + id,
                        nip: e.target.dataset.nip || '',
                        unit: e.target.dataset.unit || ''
                    });
                } else {
                    selectedRespondenMap.delete(id);
                }
                updateSelectionUI();
            }

            if (e.target.id === 'checkAllResponden') {
                const checked = e.target.checked;
                const rows = document.querySelectorAll('#tableDaftarResponden .responden-row:not(.d-none) .check-responden-item');
                rows.forEach(chk => {
                    const id = Number(chk.value);
                    if (checked) {
                        if (selectedRespondenMap.size < 10) {
                            selectedRespondenMap.set(id, {
                                id: id,
                                nama: chk.dataset.nama || 'Pegawai #' + id,
                                nip: chk.dataset.nip || '',
                                unit: chk.dataset.unit || ''
                            });
                        }
                    } else {
                        selectedRespondenMap.delete(id);
                    }
                });
                updateSelectionUI();
            }

            if (e.target.id === 'checkAllBlu') {
                const checked = e.target.checked;
                const rows = document.querySelectorAll('#tableKontributorBlu .blu-contributor-row:not(.d-none) .check-responden-item');
                rows.forEach(chk => {
                    const id = Number(chk.value);
                    if (checked) {
                        if (selectedRespondenMap.size < 10) {
                            selectedRespondenMap.set(id, {
                                id: id,
                                nama: chk.dataset.nama || 'Pegawai #' + id,
                                nip: chk.dataset.nip || '',
                                unit: chk.dataset.unit || ''
                            });
                        }
                    } else {
                        selectedRespondenMap.delete(id);
                    }
                });
                updateSelectionUI();
            }
        });

        // Batal pemilihan
        document.getElementById('btnClearSelected')?.addEventListener('click', function () {
            selectedRespondenMap.clear();
            updateSelectionUI();
        });

        // Unduh Excel responden terpilih
        document.getElementById('btnExportSelectedExcel')?.addEventListener('click', function () {
            if (selectedRespondenMap.size === 0) return;
            const ids = Array.from(selectedRespondenMap.keys()).join(',');
            window.location.href = URLS.exportExcel + '?respon_ids=' + encodeURIComponent(ids);
        });

        document.getElementById('btnModalCompareExportExcel')?.addEventListener('click', function () {
            const ids = currentComparedIds.length > 0 ? currentComparedIds.join(',') : Array.from(selectedRespondenMap.keys()).join(',');
            if (!ids) return;
            window.location.href = URLS.exportExcel + '?respon_ids=' + encodeURIComponent(ids);
        });

        // Buka Modal Bandingkan
        document.getElementById('btnCompareSelected')?.addEventListener('click', async function () {
            if (selectedRespondenMap.size === 0) {
                showDialog({ title: 'Pilih Pegawai', text: 'Centang minimal satu pegawai untuk melihat perbandingan.', icon: 'info' });
                return;
            }

            const ids = Array.from(selectedRespondenMap.keys());
            currentComparedIds = ids;
            const modalEl = document.getElementById('modalBandingkanResponden');
            const modalBs = bootstrap.Modal.getOrCreateInstance(modalEl);
            const container = document.getElementById('compareRespondenContainer');
            const subtitle = document.getElementById('compareRespondenSubtitle');
            const footerMeta = document.getElementById('compareFooterMeta');

            subtitle.textContent = 'Memuat perbandingan ' + ids.length + ' pegawai...';
            container.innerHTML = '<div class="text-center text-secondary py-5">Memuat perbandingan 83 butir isian...</div>';
            modalBs.show();

            try {
                const res = await fetch(URLS.compareResponden + '?respon_ids=' + encodeURIComponent(ids.join(',')), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.status !== 'success') {
                    container.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml(data.message || 'Gagal memuat data.') + '</div>';
                    subtitle.textContent = 'Gagal memuat';
                    return;
                }

                renderComparisonTable(data.responden || [], data.sections || []);
                subtitle.textContent = (data.responden || []).length + ' Pegawai \u00b7 10 Bagian \u00b7 83 Butir Pertanyaan';
                if (footerMeta) {
                    footerMeta.textContent = 'Menampilkan seluruh isian lengkap pegawai terpilih secara berdampingan.';
                }
            } catch (err) {
                container.innerHTML = '<div class="alert alert-danger mb-0">Terjadi kendala saat memuat data perbandingan. Silakan coba lagi.</div>';
                subtitle.textContent = 'Gagal memuat';
            }
        });

        // Render tabel perbandingan
        function renderComparisonTable(respList, sectionsList) {
            const container = document.getElementById('compareRespondenContainer');
            if (!container) return;

            if (!respList.length) {
                container.innerHTML = '<div class="text-center text-secondary py-5">Tidak ada data pegawai yang dapat dibandingkan.</div>';
                return;
            }

            let html = '<div class="compare-table-wrapper">';
            html += '<table class="table-compare" id="tableComparisonResponden">';
            html += '<thead><tr>';
            html += '<th class="sticky-col-header" style="min-width: 280px; max-width: 340px;">No &amp; Butir Pertanyaan</th>';

            respList.forEach(r => {
                const isSub = (r.status === 'submitted');
                html += '<th style="min-width: 240px; vertical-align: top;">';
                html += '<div class="fw-bold text-dark">' + escapeHtml(r.nama) + '</div>';
                html += '<div class="text-secondary tabular-nums" style="font-size: 0.75rem;">' + escapeHtml(r.nomor_identitas) + '</div>';
                html += '<div class="text-secondary" style="font-size: 0.75rem;">' + escapeHtml(r.unit) + ' &middot; ' + escapeHtml(r.jabatan) + '</div>';
                html += '<div class="mt-1">';
                html += '<span class="status-pill-compact ' + (isSub ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis') + '">';
                html += isSub ? 'Terkirim' : 'Draf ' + r.progress_percent + '%';
                html += '</span>';
                html += '</div>';
                html += '</th>';
            });
            html += '</tr></thead>';

            html += '<tbody>';
            sectionsList.forEach(sec => {
                html += '<tr class="sec-heading-row">';
                html += '<th colspan="' + (1 + respList.length) + '">';
                html += '<span class="badge bg-primary me-2">Bagian ' + escapeHtml(sec.roman) + '</span> ' + escapeHtml(sec.title);
                html += '</th></tr>';

                (sec.items || []).forEach(it => {
                    const searchKey = ((it.no || '') + ' ' + (it.label || '')).toLowerCase();
                    html += '<tr class="compare-q-row" data-qno="' + it.no + '" data-qsearch="' + escapeHtml(searchKey) + '">';
                    html += '<td class="sticky-col">';
                    html += '<span class="badge bg-light text-primary border me-1">No. ' + it.no + '</span>';
                    html += '<span class="text-dark">' + escapeHtml(it.label) + '</span>';
                    html += '</td>';

                    respList.forEach(r => {
                        const ans = (it.pegawai && it.pegawai[r.id]) ? it.pegawai[r.id] : null;
                        let valText = ans ? ans.display : '-';
                        let badgeClass = '';

                        if (!ans || !ans.applicable) {
                            valText = 'Tidak berlaku';
                            badgeClass = 'text-secondary fst-italic';
                        } else if (!ans.answered) {
                            valText = 'Belum diisi';
                            badgeClass = 'text-warning-emphasis bg-warning-subtle py-1 px-2 rounded';
                        }

                        html += '<td><div class="' + badgeClass + '" style="line-height: 1.45; white-space: pre-wrap; word-break: break-word;">' + escapeHtml(valText) + '</div></td>';
                    });
                    html += '</tr>';
                });
            });
            html += '</tbody></table></div>';

            container.innerHTML = html;
        }

        // Pencarian di dalam modal perbandingan
        document.getElementById('searchInsideCompareInput')?.addEventListener('input', function () {
            const kw = this.value.trim().toLowerCase();
            document.querySelectorAll('#tableComparisonResponden .compare-q-row').forEach(row => {
                const txt = row.dataset.qsearch || '';
                row.classList.toggle('d-none', kw !== '' && !txt.includes(kw));
            });
        });
    });
})();
</script>
<?= $this->endSection() ?>
