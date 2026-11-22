<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Ringkasan Beasiswa<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$ringkasan    = $ringkasan ?? [];
$summary      = $ringkasan['summary'] ?? [];
$breakdown    = $ringkasan['breakdown_jenis'] ?? [];
$programRows  = $ringkasan['program_rows'] ?? [];
$kandidatRows = $ringkasan['recent_kandidat'] ?? [];
$refs         = $refs ?? [];
$tahunId      = (string) ($tahunId ?? 'all');
$isSelfTaruna = !empty($isSelfTaruna);
$perms        = $perms ?? [];

$exportUrl = base_url('ketarunaan/beasiswa/ekspor')
    . ($tahunId !== 'all' ? ('?tahun_akademik_id=' . urlencode($tahunId)) : '');
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <!-- Filter Periode & Aksi Ringkas -->
    <div class="ecc-bea-card mb-3 py-3">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa') ?>" class="row g-2 align-items-end bea-filter-form">
            <div class="col-12 col-md-4">
                <label for="tahun_akademik_id" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode Akademik</label>
                <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <option value="all" <?= $tahunId === 'all' ? 'selected' : '' ?>>Semua Periode</option>
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= (string) $ta['id'] === $tahunId ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?><?= (int) $ta['is_aktif'] === 1 ? ' (Aktif)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-8 d-flex flex-wrap justify-content-md-end gap-2">
                <?php if ($tahunId !== 'all'): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa?tahun_akademik_id=all') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">
                        Semua Periode
                    </a>
                <?php endif; ?>
                <?php if (!$isSelfTaruna): ?>
                    <a href="<?= esc($exportUrl) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile bea-touch-ctrl">
                        <i class="bi bi-download" aria-hidden="true"></i> Ekspor Rekap
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('ketarunaan/beasiswa/penerima') ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile bea-touch-ctrl">
                    <?= $isSelfTaruna ? 'Riwayat Beasiswa Saya' : 'Lihat Daftar Penerima' ?>
                </a>
            </div>
        </form>
    </div>

    <!-- 4 Kartu Jenis Resmi Beasiswa -->
    <div class="row g-3 mb-3">
        <?php foreach ($breakdown as $kode => $item): ?>
            <?php
            $meta = $item['meta'];
            $tautanUrl = $kode === 'apresiasi'
                ? ($isSelfTaruna ? base_url('ketarunaan/beasiswa/penerima?jenis_beasiswa=apresiasi') : base_url('ketarunaan/beasiswa/apresiasi'))
                : base_url('ketarunaan/beasiswa/klaim?jenis_beasiswa=' . $kode);
            ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="ecc-bea-kpi d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($meta['badge_class']) ?>" style="font-size: 0.75rem;">
                                <?= esc($meta['mekanisme']) ?>
                            </span>
                            <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                <?= (int) $item['program_count'] ?> Program
                            </span>
                        </div>
                        <h2 class="h6 fw-bold text-dark mb-1"><?= esc($meta['label']) ?></h2>
                        <p class="ecc-text-muted-aa mb-3 small"><?= esc($meta['ringkasan']) ?></p>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-baseline border-top pt-2 mb-2">
                            <div>
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                    <?= $isSelfTaruna ? 'Ditetapkan (Saya)' : 'Penerima Ditetapkan' ?>
                                </div>
                                <div class="fw-bold fs-4 text-dark ecc-num"><?= (int) $item['ditetapkan_count'] ?></div>
                            </div>
                            <div class="text-end">
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Menunggu</div>
                                <div class="fw-bold fs-4 text-dark ecc-num"><?= (int) $item['antrean_count'] + (int) $item['lolos_menunggu_sk'] ?></div>
                            </div>
                        </div>
                        <a href="<?= esc($tautanUrl) ?>" class="btn btn-light border btn-sm w-100 rounded-pill fw-semibold bea-touch-ctrl d-inline-flex align-items-center justify-content-center">
                            <?= $kode === 'apresiasi' && !$isSelfTaruna ? 'Buka Seleksi Apresiasi' : ($isSelfTaruna ? 'Lihat Jalur Ini' : 'Buka Daftar') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Strip KPI Terpisah -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Berjalan</div>
                <div class="fw-bold fs-3 text-dark ecc-num"><?= (int) ($summary['program_aktif'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Dari <?= (int) ($summary['total_program'] ?? 0) ?> program terdaftar</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">
                    <?= $isSelfTaruna ? 'Beasiswa Saya Disahkan' : 'Penetapan Disahkan' ?>
                </div>
                <div class="fw-bold fs-3 text-success ecc-num"><?= (int) ($summary['penetapan_disahkan'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Status ditetapkan &amp; tervalidasi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">
                    <?= $isSelfTaruna ? 'Program Diikuti' : 'Penerima Taruna Unik' ?>
                </div>
                <div class="fw-bold fs-3 text-primary ecc-num"><?= (int) ($summary['penerima_unik'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                    <?= $isSelfTaruna ? 'Status penetapan aktif saya' : 'Individu taruna penerima aktif' ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Menunggu Pemeriksaan &amp; SK</div>
                <div class="fw-bold fs-3 text-warning-emphasis ecc-num"><?= (int) ($summary['menunggu_verifikasi'] ?? 0) + (int) ($summary['lolos_menunggu_sk'] ?? 0) ?></div>
                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                    <?= (int) ($summary['belum_diperiksa'] ?? 0) ?> periksa &bull; <?= (int) ($summary['lolos_menunggu_sk'] ?? 0) ?> siap SK
                </div>
            </div>
        </div>
    </div>

    <!-- Alur Tata Kelola 4 Tahap -->
    <div class="ecc-bea-card mb-4">
        <h2 class="h6 fw-bold text-dark mb-3">Alur Pemrosesan Beasiswa</h2>
        <div class="row g-2">
            <div class="col-12 col-md-3">
                <div class="ecc-step-pill">
                    <div class="fw-semibold text-primary mb-1 small">1. Seleksi atau Klaim</div>
                    <div class="ecc-text-muted-aa small">Pemeringkatan IP semester kelas atau pengajuan klaim beasiswa.</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="ecc-step-pill">
                    <div class="fw-semibold text-primary mb-1 small">2. Verifikasi Kelayakan</div>
                    <div class="ecc-text-muted-aa small">Pemeriksaan syarat, acuan wilayah 3TP, dan tautan bukti dukung.</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="ecc-step-pill">
                    <div class="fw-semibold text-primary mb-1 small">3. Penetapan SK</div>
                    <div class="ecc-text-muted-aa small">Penerbitan keputusan resmi bagi pengajuan yang dinyatakan memenuhi.</div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="ecc-step-pill">
                    <div class="fw-semibold text-success mb-1 small">4. Penerima Sah</div>
                    <div class="ecc-text-muted-aa small">Tercatat pada rekap resmi dan terhubung ke Rekam Jejak Taruna.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Program Beasiswa Aktif -->
    <div class="ecc-bea-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 fw-bold text-dark mb-0">Daftar Program Beasiswa</h2>
            <a href="<?= base_url('ketarunaan/beasiswa/program') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                <?= $isSelfTaruna ? 'Lihat Semua Program' : 'Kelola Semua Program' ?>
            </a>
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="0"><span>Program Beasiswa</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="1"><span>Jenis</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="2"><span>Pemberi / Sumber</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="3"><span>Kuota Terisi</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="4"><span>Nominal</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="5"><span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programRows)): ?>
                        <tr>
                            <td colspan="7" class="text-center ecc-text-muted-aa py-4">Belum ada program beasiswa pada periode terpilih.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($programRows as $prog): ?>
                            <?php $mySub = $prog['my_submission'] ?? null; ?>
                            <tr>
                                <td data-sort-val="<?= esc(strtolower((string) $prog['nama_program'])) ?>">
                                    <div class="fw-semibold text-dark"><?= esc($prog['nama_program']) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($prog['tahun_akademik_label'] ?: $prog['tahun']) ?></div>
                                </td>
                                <td data-sort-val="<?= esc((string) $prog['jenis_beasiswa']) ?>"><?= ketarunaan_jenis_beasiswa_badge((string) $prog['jenis_beasiswa']) ?></td>
                                <td data-sort-val="<?= esc(strtolower((string) $prog['pemberi'])) ?>"><?= esc($prog['pemberi']) ?></td>
                                <td class="ecc-num" data-sort-val="<?= (int) $prog['ditetapkan_count'] ?>">
                                    <span class="fw-semibold"><?= (int) $prog['ditetapkan_count'] ?></span> / <?= (int) $prog['kuota'] ?>
                                    <?php if (!$isSelfTaruna && (int) $prog['kandidat_siap_count'] > 0): ?>
                                        <span class="ecc-text-muted-aa" style="font-size: 0.75rem;">(<?= (int) $prog['kandidat_siap_count'] ?> siap SK)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="ecc-num" data-sort-val="<?= (int) ($prog['nominal'] ?? 0) ?>"><?= esc(ketarunaan_rupiah($prog['nominal'] ?? 0)) ?></td>
                                <td data-sort-val="<?= esc((string) $prog['status_program']) ?>"><?= ketarunaan_program_beasiswa_status_badge((string) $prog['status_program']) ?></td>
                                <td class="text-end text-nowrap">
                                    <?php if ($isSelfTaruna): ?>
                                        <?php if ($mySub): ?>
                                            <a href="<?= base_url('ketarunaan/beasiswa/detail/' . (int) $mySub['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Lihat status pengajuan saya untuk <?= esc($prog['nama_program']) ?>">
                                                Status Saya
                                            </a>
                                        <?php elseif (($prog['jenis_beasiswa'] ?? '') !== 'apresiasi' && ($prog['status_program'] ?? '') !== 'selesai'): ?>
                                            <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($prog['jenis_beasiswa'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Ajukan klaim untuk <?= esc($prog['nama_program']) ?>">
                                                Ajukan
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('ketarunaan/beasiswa/penerima?program_beasiswa_id=' . (int) $prog['id']) ?>" class="btn btn-light border btn-sm rounded-pill px-3" aria-label="Lihat riwayat program <?= esc($prog['nama_program']) ?>">
                                                Riwayat
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="<?= base_url('ketarunaan/beasiswa/penerima?program_beasiswa_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Lihat penerima <?= esc($prog['nama_program']) ?>">
                                            Penerima
                                        </a>
                                        <?php if (!empty($perms['can_manage'])): ?>
                                            <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . (int) $prog['id']) ?>" class="btn btn-light border btn-sm rounded-pill px-3" aria-label="Atur program <?= esc($prog['nama_program']) ?>">
                                                Atur
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Program Beasiswa -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($programRows)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Belum ada program beasiswa pada periode terpilih.</div>
            <?php else: ?>
                <?php foreach ($programRows as $prog): ?>
                    <?php $mySub = $prog['my_submission'] ?? null; ?>
                    <div class="border rounded-3 p-3 bg-white bea-mobile-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><?= esc($prog['nama_program']) ?></div>
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($prog['pemberi']) ?> &bull; <?= esc($prog['tahun_akademik_label'] ?: $prog['tahun']) ?></div>
                            </div>
                            <?= ketarunaan_program_beasiswa_status_badge((string) $prog['status_program']) ?>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <?= ketarunaan_jenis_beasiswa_badge((string) $prog['jenis_beasiswa']) ?>
                            <div class="ecc-num small">
                                Kuota: <strong><?= (int) $prog['ditetapkan_count'] ?> / <?= (int) $prog['kuota'] ?></strong> &bull; <?= esc(ketarunaan_rupiah($prog['nominal'] ?? 0)) ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <?php if ($isSelfTaruna): ?>
                                <?php if ($mySub): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/detail/' . (int) $mySub['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Status Saya</a>
                                <?php elseif (($prog['jenis_beasiswa'] ?? '') !== 'apresiasi' && ($prog['status_program'] ?? '') !== 'selesai'): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/klaim/ajukan/' . esc($prog['jenis_beasiswa'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Ajukan</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= base_url('ketarunaan/beasiswa/penerima?program_beasiswa_id=' . (int) $prog['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Penerima</a>
                                <?php if (!empty($perms['can_manage'])): ?>
                                    <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . (int) $prog['id']) ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">Atur</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($kandidatRows)): ?>
        <div class="ecc-bea-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="h6 fw-bold text-dark mb-0"><?= $isSelfTaruna ? 'Pengajuan Terbaru Saya' : 'Pengajuan &amp; Kandidat Terbaru' ?></h2>
                <a href="<?= base_url($isSelfTaruna ? 'ketarunaan/beasiswa/klaim' : 'ketarunaan/beasiswa/verifikasi') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                    <?= $isSelfTaruna ? 'Lihat Semua Klaim' : 'Lihat Antrean' ?>
                </a>
            </div>
            <div class="table-responsive d-none d-md-block">
                <table class="table ecc-table-bea mb-0">
                    <thead>
                        <tr>
                            <?php if (!$isSelfTaruna): ?><th>Taruna</th><?php endif; ?>
                            <th>Program</th>
                            <th>Sumber</th>
                            <th>Hasil Verifikasi</th>
                            <th>Status Penetapan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kandidatRows as $kr): ?>
                            <?php $detUrl = base_url('ketarunaan/beasiswa/detail/' . (int) $kr['id']); ?>
                            <tr class="bea-row-clickable" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Detail pengajuan <?= esc($kr['nama_program']) ?>">
                                <?php if (!$isSelfTaruna): ?>
                                    <td>
                                        <div class="fw-semibold"><?= esc($kr['nama_lengkap'] ?: $kr['nomor_taruna']) ?></div>
                                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc($kr['nomor_taruna']) ?> &bull; <?= esc($kr['kode_prodi'] ?? '-') ?></div>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($kr['nama_program']) ?></div>
                                    <div class="mt-1"><?= ketarunaan_jenis_beasiswa_badge((string) $kr['jenis_beasiswa']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                        <?= esc(ucfirst((string) $kr['sumber_klaim'])) ?>
                                    </span>
                                </td>
                                <td><?= ketarunaan_verifikasi_beasiswa_badge((string) $kr['hasil_verifikasi']) ?></td>
                                <td><?= ketarunaan_penetapan_beasiswa_badge((string) $kr['status_penetapan']) ?></td>
                                <td class="text-end">
                                    <a href="<?= esc($detUrl) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" aria-label="Buka detail pengajuan <?= esc($kr['nama_lengkap'] ?: $kr['nomor_taruna']) ?>">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Kartu Mobile Kandidat Terbaru -->
            <div class="d-md-none d-flex flex-column gap-3">
                <?php foreach ($kandidatRows as $kr): ?>
                    <?php $detUrl = base_url('ketarunaan/beasiswa/detail/' . (int) $kr['id']); ?>
                    <div class="border rounded-3 p-3 bg-white bea-row-clickable bea-mobile-card" data-href="<?= esc($detUrl) ?>" tabindex="0" role="link" aria-label="Buka detail pengajuan <?= esc($kr['nama_program']) ?>">
                        <?php if (!$isSelfTaruna): ?>
                            <div class="fw-bold text-dark"><?= esc($kr['nama_lengkap'] ?: $kr['nomor_taruna']) ?></div>
                            <div class="ecc-text-muted-aa ecc-num mb-1" style="font-size: 0.75rem;"><?= esc($kr['nomor_taruna']) ?> &bull; <?= esc($kr['kode_prodi'] ?? '-') ?></div>
                        <?php endif; ?>
                        <div class="fw-semibold small text-dark mb-2"><?= esc($kr['nama_program']) ?></div>
                        <div class="d-flex flex-wrap gap-2">
                            <?= ketarunaan_jenis_beasiswa_badge((string) $kr['jenis_beasiswa']) ?>
                            <?= ketarunaan_verifikasi_beasiswa_badge((string) $kr['hasil_verifikasi']) ?>
                            <?= ketarunaan_penetapan_beasiswa_badge((string) $kr['status_penetapan']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<?= $this->endSection() ?>
