<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Seleksi Beasiswa Apresiasi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$data          = $data ?? [];
$programs      = $data['programs'] ?? [];
$selProg       = $data['selected_program'] ?? null;
$refs          = $refs ?? [];
$periodeId     = (int) ($data['periode_nilai_id'] ?? ($refs['aktif_tahun_akademik_id'] ?? 0));
$periodeLabel  = (string) ($data['periode_label'] ?? '-');
$kelasOptions  = $data['kelas_options'] ?? [];
$selKelasId    = (int) ($data['selected_kelas_id'] ?? 0);
$pemeringkatan = $data['pemeringkatan'] ?? null;
$rankingRows   = $data['ranking_rows'] ?? [];
$riwayatVersi  = $data['riwayat_versi'] ?? [];
$populasiIp    = (int) ($data['populasi_ip'] ?? 0);
$ipPreview     = $ipPreview ?? null;
$perms         = $perms ?? [];

$canManage    = !empty($perms['can_manage']);
$aturanLabels = ketarunaan_aturan_seri_catalog();
$statusFinal  = (string) ($pemeringkatan['status_finalisasi'] ?? 'belum_dihitung');
$isDitahan    = ($statusFinal === 'ditahan_seri' || (int) ($pemeringkatan['ada_seri_kelima'] ?? 0) === 1);
$selProgId    = $selProg ? (int) $selProg['id'] : 0;
$aturanAktif  = $selProg ? ($aturanLabels[$selProg['aturan_seri'] ?? 'belum_ditetapkan'] ?? 'Belum Ditetapkan') : '-';
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <!-- Filter & Pengendali Seleksi -->
    <div class="ecc-bea-card mb-4">
        <form method="get" action="<?= base_url('ketarunaan/beasiswa/apresiasi') ?>" class="row g-2 align-items-end bea-filter-form">
            <div class="col-12 col-md-4">
                <label for="aprProgram" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Apresiasi</label>
                <select id="aprProgram" name="program_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <?php if (empty($programs)): ?>
                        <option value="0">Belum ada program Apresiasi</option>
                    <?php else: ?>
                        <?php foreach ($programs as $ap): ?>
                            <option value="<?= (int) $ap['id'] ?>" <?= (int) $ap['id'] === $selProgId ? 'selected' : '' ?>>
                                <?= esc($ap['nama_program']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="aprPeriode" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Periode Nilai Semester</label>
                <select id="aprPeriode" name="periode_nilai_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                        <option value="<?= (int) $ta['id'] ?>" <?= (int) $ta['id'] === $periodeId ? 'selected' : '' ?>>
                            <?= esc($ta['kode_tahun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="aprKelas" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Kelas Penilaian</label>
                <select id="aprKelas" name="kelas_id" class="form-select ecc-control-sm" onchange="this.form.submit()">
                    <?php if (empty($kelasOptions)): ?>
                        <option value="0">Belum ada nilai IP periode ini</option>
                    <?php else: ?>
                        <?php foreach ($kelasOptions as $ko): ?>
                            <option value="<?= (int) $ko['id'] ?>" <?= (int) $ko['id'] === $selKelasId ? 'selected' : '' ?>>
                                <?= esc($ko['nama_kelas']) ?> (<?= (int) $ko['total_nilai'] ?> taruna)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-outline-primary ecc-control-sm fw-semibold w-100 bea-touch-ctrl btn-tactile">Tampilkan</button>
            </div>
        </form>

        <?php if ($canManage): ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top">
                <div class="ecc-text-muted-aa small">
                    Dasar penilaian: <strong class="text-dark">IP Semester (bukan IPK)</strong> &bull; Pemecah seri program: <strong class="text-dark"><?= esc($aturanAktif) ?></strong>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-light border btn-sm rounded-pill px-3 btn-tactile bea-touch-ctrl" data-bs-toggle="modal" data-bs-target="#modalImporIp">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i> Impor Nilai IP
                    </button>
                    <?php if ($selProgId > 0): ?>
                        <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . $selProgId) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                            Atur Pemecah Seri
                        </a>
                        <?php if ($selKelasId > 0): ?>
                            <form method="post" action="<?= base_url('ketarunaan/beasiswa/apresiasi/hitung') ?>" class="d-inline" data-lock-submit="true">
                                <?= csrf_field() ?>
                                <input type="hidden" name="program_id" value="<?= $selProgId ?>">
                                <input type="hidden" name="periode_nilai_id" value="<?= $periodeId ?>">
                                <input type="hidden" name="kelas_id" value="<?= $selKelasId ?>">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 btn-tactile bea-touch-ctrl shadow-sm">
                                    Hitung Ulang Peringkat Kelas
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (is_array($ipPreview) && !empty($ipPreview['rows'])): ?>
        <!-- Pratinjau Impor CSV IP Semester -->
        <div class="ecc-bea-card mb-4 border-primary">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h2 class="h6 fw-bold text-primary mb-1">Pratinjau Nilai IP Semester Siap Impor</h2>
                    <div class="ecc-text-muted-aa ecc-num small">
                        <?= (int) $ipPreview['valid_count'] ?> baris valid &bull; <?= (int) $ipPreview['invalid_count'] ?> baris perlu diperbaiki
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="<?= base_url('ketarunaan/beasiswa/apresiasi/impor-ip/batal') ?>" class="d-inline" data-lock-submit="true">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl">Tutup Pratinjau</button>
                    </form>
                    <?php if ((int) ($ipPreview['valid_count'] ?? 0) > 0 && $canManage): ?>
                        <form method="post" action="<?= base_url('ketarunaan/beasiswa/apresiasi/impor-ip/simpan') ?>" class="d-inline" data-lock-submit="true">
                            <?= csrf_field() ?>
                            <input type="hidden" name="preview_token" value="<?= esc((string) ($ipPreview['token'] ?? '')) ?>">
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 btn-tactile bea-touch-ctrl">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Simpan <?= (int) $ipPreview['valid_count'] ?> Baris Valid
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
                <table class="table ecc-table-bea mb-0">
                    <thead>
                        <tr>
                            <th>Baris</th>
                            <th>Nomor Taruna</th>
                            <th>Nama Taruna</th>
                            <th>Kelas</th>
                            <th>IP Semester</th>
                            <th>Status Pemeriksaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ipPreview['rows'] as $pr): ?>
                            <tr class="<?= ($pr['status'] ?? '') === 'invalid' ? 'table-danger' : '' ?>">
                                <td class="ecc-num"><?= (int) $pr['baris'] ?></td>
                                <td class="ecc-num"><?= esc((string) $pr['nomor_taruna']) ?></td>
                                <td><?= esc((string) $pr['nama_lengkap']) ?></td>
                                <td><?= esc((string) $pr['nama_kelas']) ?></td>
                                <td class="ecc-num fw-semibold"><?= esc((string) $pr['ip_semester']) ?></td>
                                <td>
                                    <?php if (($pr['status'] ?? '') === 'valid'): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Valid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1">Perlu Diperbaiki</span>
                                        <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc((string) $pr['alasan_error']) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Kartu Mobile Pratinjau CSV -->
            <div class="d-md-none d-flex flex-column gap-2">
                <?php foreach ($ipPreview['rows'] as $pr): ?>
                    <div class="border rounded-3 p-3 <?= ($pr['status'] ?? '') === 'invalid' ? 'border-danger bg-danger-subtle' : 'bg-white' ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div>
                                <div class="fw-bold text-dark ecc-num">Baris #<?= (int) $pr['baris'] ?> &bull; <?= esc((string) $pr['nomor_taruna']) ?></div>
                                <div class="small fw-semibold"><?= esc((string) $pr['nama_lengkap']) ?> (<?= esc((string) $pr['nama_kelas']) ?>)</div>
                            </div>
                            <span class="fw-bold ecc-num text-primary"><?= esc((string) $pr['ip_semester']) ?></span>
                        </div>
                        <?php if (($pr['status'] ?? '') === 'valid'): ?>
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Valid</span>
                        <?php else: ?>
                            <div class="text-danger small fw-semibold"><?= esc((string) $pr['alasan_error']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3 Kartu Ringkasan Pemeringkatan Kelas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Sumber Nilai &amp; Populasi Kelas</div>
                <div class="fw-bold fs-4 text-dark ecc-num"><?= $populasiIp ?> Taruna</div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Periode <?= esc($periodeLabel) ?> &bull; IP semester tervalidasi</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Kandidat Peringkat Kelas</div>
                <div class="fw-bold fs-4 text-primary ecc-num">
                    <?= (int) ($pemeringkatan['jumlah_kandidat'] ?? 0) ?> / <?= (int) ($selProg['jumlah_peringkat'] ?? 5) ?>
                </div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                    Kode versi: <?= esc((string) ($pemeringkatan['kode_versi'] ?? 'Belum Dihitung')) ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="ecc-bea-kpi">
                <div class="ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Pemeringkatan</div>
                <div class="mt-1 mb-1">
                    <?php if ($statusFinal === 'tervalidasi'): ?>
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Tervalidasi</span>
                    <?php elseif ($statusFinal === 'ditahan_seri'): ?>
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-3 py-1">Ditahan (Seri Peringkat Ke-5)</span>
                    <?php elseif ($statusFinal === 'diajukan'): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Diajukan ke Verifikasi</span>
                    <?php elseif ($statusFinal === 'draf'): ?>
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1">Siap Diajukan</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-3 py-1">Belum Dihitung</span>
                    <?php endif; ?>
                </div>
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                    Aturan seri: <?= esc($aturanAktif) ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($isDitahan): ?>
        <!-- Peringatan Resmi Penahanan Kelas Seri -->
        <div class="alert alert-warning border-warning rounded-4 p-3 mb-4" role="alert">
            <div class="fw-bold text-dark mb-1">Finalisasi Pemeringkatan Kelas Ini Ditahan</div>
            <div class="text-dark mb-2 small">
                <?= esc((string) ($pemeringkatan['alasan_ditahan'] ?? 'Terdapat nilai IP semester yang sama pada batas peringkat ke-5 sehingga memerlukan penetapan aturan pemecah seri resmi.')) ?>
            </div>
            <?php if ($canManage && $selProgId > 0): ?>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('ketarunaan/beasiswa/kelola-program/' . $selProgId) ?>" class="btn btn-dark btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                        Tetapkan Aturan Pemecah Seri
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Hasil Pemeringkatan IP Semester -->
    <div class="ecc-bea-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-dark mb-1">Daftar Peringkat IP Semester Kelas</h2>
                <div class="ecc-text-muted-aa small">
                    Pemeringkatan menggunakan IP semester pada periode terpilih, bukan IP kumulatif.
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($statusFinal === 'diajukan'): ?>
                    <a href="<?= base_url('ketarunaan/beasiswa/verifikasi?jenis_beasiswa=apresiasi') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center">
                        Buka Antrean Verifikasi
                    </a>
                <?php endif; ?>
                <?php if ($canManage && $pemeringkatan && !$isDitahan && in_array($statusFinal, ['draf', 'dikembalikan'], true)): ?>
                    <form method="post" action="<?= base_url('ketarunaan/beasiswa/apresiasi/ajukan/' . (int) $pemeringkatan['id']) ?>"
                          onsubmit="return window.eccBeaConfirmSubmit(event, 'Ajukan Kandidat Apresiasi?', 'Kandidat terbaik kelas akan dikirim ke antrean verifikasi dan penetapan.', 'Ya, Ajukan');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 btn-tactile bea-touch-ctrl">
                            <i class="bi bi-send me-1" aria-hidden="true"></i> Ajukan Kandidat ke Verifikasi
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive ecc-table-bea-wrap d-none d-md-block">
            <table class="table ecc-table-bea mb-0" data-sortable-table="true">
                <thead>
                    <tr>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="0"><span>Peringkat</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="1"><span>Nomor Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="2"><span>Nama Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th>Kelas</th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="4"><span>IP Semester</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th aria-sort="none"><button type="button" class="bea-sort-btn" data-sort-col="5"><span>Status Kandidat</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rankingRows)): ?>
                        <tr>
                            <td colspan="7" class="text-center ecc-text-muted-aa py-4">Belum ada hasil pemeringkatan untuk kelas ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rankingRows as $rr): ?>
                            <?php $stK = (string) ($rr['status_kandidat'] ?? 'di_luar_5_besar'); ?>
                            <tr class="<?= !empty($rr['is_seri']) ? 'ecc-tie-row' : '' ?>">
                                <td class="ecc-num fw-bold" data-sort-val="<?= (int) $rr['peringkat'] ?>">#<?= (int) $rr['peringkat'] ?></td>
                                <td class="ecc-num" data-sort-val="<?= esc((string) $rr['nomor_taruna']) ?>"><?= esc((string) $rr['nomor_taruna']) ?></td>
                                <td class="fw-semibold" data-sort-val="<?= esc(strtolower((string) $rr['nama_snapshot'])) ?>"><?= esc((string) $rr['nama_snapshot']) ?></td>
                                <td><?= esc((string) $rr['kelas_snapshot']) ?></td>
                                <td class="ecc-num fw-bold text-primary" data-sort-val="<?= number_format((float) $rr['ip_semester'], 2, '.', '') ?>"><?= number_format((float) $rr['ip_semester'], 2, ',', '.') ?></td>
                                <td data-sort-val="<?= esc($stK) ?>">
                                    <?php if ($stK === 'kandidat'): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Kandidat Terpilih</span>
                                    <?php elseif ($stK === 'seri_kelima'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Seri Batas Kuota</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1">Di Luar Kuota</span>
                                    <?php endif; ?>
                                </td>
                                <td class="ecc-text-muted-aa small"><?= esc((string) ($rr['catatan'] ?: '-')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Kartu Mobile Peringkat Kelas -->
        <div class="d-md-none d-flex flex-column gap-3">
            <?php if (empty($rankingRows)): ?>
                <div class="text-center ecc-text-muted-aa py-3 small">Belum ada hasil pemeringkatan untuk kelas ini.</div>
            <?php else: ?>
                <?php foreach ($rankingRows as $rr): ?>
                    <?php $stK = (string) ($rr['status_kandidat'] ?? 'di_luar_5_besar'); ?>
                    <div class="border rounded-3 p-3 <?= !empty($rr['is_seri']) ? 'ecc-tie-row' : 'bg-white' ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                            <div>
                                <div class="fw-bold text-dark ecc-num">#<?= (int) $rr['peringkat'] ?> &bull; <?= esc((string) $rr['nama_snapshot']) ?></div>
                                <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $rr['nomor_taruna']) ?> &bull; <?= esc((string) $rr['kelas_snapshot']) ?></div>
                            </div>
                            <span class="fw-bold text-primary ecc-num fs-5"><?= number_format((float) $rr['ip_semester'], 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                            <?php if ($stK === 'kandidat'): ?>
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1">Kandidat Terpilih</span>
                            <?php elseif ($stK === 'seri_kelima'): ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Seri Batas Kuota</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1">Di Luar Kuota</span>
                            <?php endif; ?>
                            <?php if (!empty($rr['catatan'])): ?>
                                <span class="ecc-text-muted-aa small"><?= esc((string) $rr['catatan']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($riwayatVersi)): ?>
        <div class="ecc-bea-card">
            <h2 class="h6 fw-bold text-dark mb-3">Riwayat Versi Pemeringkatan Kelas</h2>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($riwayatVersi as $rv): ?>
                    <div class="p-3 rounded-3 border bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                        <div>
                            <span class="fw-bold text-dark ecc-num"><?= esc((string) $rv['kode_versi']) ?></span>
                            <span class="ecc-text-muted-aa ms-2">Oleh: <strong><?= esc((string) ($rv['diproses_oleh'] ?: '-')) ?></strong></span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2 ecc-num">
                            <span class="ecc-text-muted-aa"><?= (int) $rv['jumlah_kandidat'] ?> kandidat / <?= (int) $rv['populasi_kelas'] ?> taruna</span>
                            <span class="badge bg-white text-dark border rounded-pill px-3 py-1"><?= esc(ucwords(str_replace('_', ' ', (string) $rv['status_finalisasi']))) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Impor Nilai IP CSV -->
<?php if ($canManage): ?>
<div class="modal fade" id="modalImporIp" tabindex="-1" aria-labelledby="modalImporIpLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h2 class="h5 modal-title fw-bold" id="modalImporIpLabel">Impor Nilai IP Semester (CSV)</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formImporIpCsv" method="post" action="<?= base_url('ketarunaan/beasiswa/apresiasi/impor-ip/preview') ?>" enctype="multipart/form-data" data-lock-submit="true">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="ecc-text-muted-aa mb-3 small">
                        Sistem memeriksa setiap baris terlebih dahulu. Nilai di luar rentang 0,00 sampai 4,00 atau taruna tidak terdaftar akan ditandai sebelum disimpan.
                    </p>
                    <div class="mb-3">
                        <label for="modal_periode_nilai_id" class="form-label fw-semibold">Periode Semester Nilai <span class="text-danger">*</span></label>
                        <select id="modal_periode_nilai_id" name="periode_nilai_id" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                                <option value="<?= (int) $ta['id'] ?>" <?= (int) $ta['id'] === $periodeId ? 'selected' : '' ?>>
                                    <?= esc($ta['kode_tahun']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="csv_nilai_ip" class="form-label fw-semibold mb-0">Pilih Berkas CSV <span class="text-danger">*</span></label>
                            <a href="<?= base_url('ketarunaan/beasiswa/apresiasi/impor-ip/templat') ?>" class="small fw-semibold text-decoration-none">
                                <i class="bi bi-download me-1" aria-hidden="true"></i>Unduh Contoh CSV
                            </a>
                        </div>
                        <input type="file" id="csv_nilai_ip" name="csv_nilai_ip" class="form-control ecc-control-sm" accept=".csv,.txt" required>
                        <div class="form-text">Format kolom: <code>nomor_taruna,ip_semester,sks_lulus</code> (maksimal 2 MB).</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border rounded-pill px-3 bea-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 btn-tactile bea-touch-ctrl">Periksa &amp; Pratinjau</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var formCsv = document.getElementById('formImporIpCsv');
    var fileCsv = document.getElementById('csv_nilai_ip');
    if (formCsv && fileCsv) {
        formCsv.addEventListener('submit', function(e) {
            var file = fileCsv.files && fileCsv.files[0];
            if (!file) {
                e.preventDefault();
                window.eccBeaShowMessage('Berkas Wajib Dipilih', 'Pilih berkas CSV terlebih dahulu.');
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                e.preventDefault();
                window.eccBeaShowMessage('Berkas Terlalu Besar', 'Ukuran berkas CSV maksimal 2 MB.');
                return;
            }
            var ext = (file.name.split('.').pop() || '').toLowerCase();
            if (ext !== 'csv' && ext !== 'txt') {
                e.preventDefault();
                window.eccBeaShowMessage('Format Tidak Sesuai', 'Berkas harus berformat .csv atau .txt.');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
