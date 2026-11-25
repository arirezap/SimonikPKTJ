<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Validasi Kelulusan Alumni<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/alumni/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$queue           = $queue ?? [];
$pending         = $queue['pending'] ?? [];
$kandidatTaruna  = $queue['kandidat_taruna'] ?? [];
$prodiRefs       = $queue['prodi_refs'] ?? [];
$angkatanRefs    = $queue['angkatan_refs'] ?? [];
$recentValidated = $queue['recent_validated'] ?? [];
$jumlahBerhenti  = (int) ($queue['jumlah_berhenti'] ?? 0);
$berhentiDiAl    = (int) ($queue['berhenti_di_alumni'] ?? 0);
$perms           = $perms ?? [];
$canManage       = !empty($perms['can_manage']);
$canValidate     = !empty($perms['can_validate']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/alumni/_nav_tabs') ?>

    <!-- Penegasan Aturan Pemisahan Lulus vs Berhenti -->
    <div class="ecc-alm-card mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-8">
                <h2 class="h6 fw-bold text-dark mb-1">Pembentukan Profil Alumni Tervalidasi</h2>
                <p class="ecc-text-muted-aa mb-0 small">
                    Profil dasar alumni hanya terbentuk setelah pengajuan kelulusan disetujui pejabat validator.
                    Taruna dengan status mengundurkan diri atau drop out tercatat berhenti dan tidak pernah masuk ke daftar alumni.
                </p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="p-3 rounded-3 bg-light d-flex justify-content-between align-items-center small">
                    <span>Taruna berhenti tercatat: <strong class="ecc-num"><?= number_format($jumlahBerhenti) ?></strong></span>
                    <span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size: 0.75rem;">
                        Di Alumni: <?= $berhentiDiAl ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php if ($canManage): ?>
            <!-- Form Pengajuan Kelulusan (Mode Massal / Batch 1 SK & Mode Satuan) -->
            <div class="col-12 col-lg-5">
                <div class="ecc-alm-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h2 class="h6 fw-bold text-dark mb-0">Ajukan Kelulusan Taruna</h2>
                        <ul class="nav nav-pills gap-1" id="kelulusanModeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1 px-3 rounded-pill small fw-semibold" id="tab-batch-btn" data-bs-toggle="pill" data-bs-target="#pane-batch" type="button" role="tab" aria-controls="pane-batch" aria-selected="true">
                                    Massal (1 SK)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-3 rounded-pill small fw-semibold" id="tab-single-btn" data-bs-toggle="pill" data-bs-target="#pane-single" type="button" role="tab" aria-controls="pane-single" aria-selected="false">
                                    Satuan
                                </button>
                            </li>
                        </ul>
                    </div>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Pengajuan masuk ke antrean validator terlebih dahulu dan tidak langsung menambah jumlah alumni aktif.
                    </p>

                    <div class="tab-content">
                        <!-- Mode 1: Pengajuan Massal (1 SK untuk Banyak Taruna / 1 Angkatan) -->
                        <div class="tab-pane fade show active" id="pane-batch" role="tabpanel" aria-labelledby="tab-batch-btn">
                            <form method="post" action="<?= base_url('ketarunaan/alumni/kelulusan/ajukan-batch') ?>" class="row g-3 alm-form-mobile" id="formAjukanKelulusanBatch"
                                  onsubmit="return window.eccAlmSubmitBatchKelulusan(event);">
                                <?= csrf_field() ?>

                                <!-- Filter Cepat Kandidat -->
                                <div class="col-12">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label for="batch_filter_prodi" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Program Studi</label>
                                                <select id="batch_filter_prodi" class="form-select ecc-control-sm">
                                                    <option value="all">Semua Prodi</option>
                                                    <?php foreach ($prodiRefs as $pr): ?>
                                                        <option value="<?= (int) $pr['id'] ?>"><?= esc((string) $pr['kode_prodi']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="batch_filter_angkatan" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Angkatan</label>
                                                <select id="batch_filter_angkatan" class="form-select ecc-control-sm">
                                                    <option value="all">Semua Angkatan</option>
                                                    <?php foreach ($angkatanRefs as $an): ?>
                                                        <option value="<?= (int) $an['id'] ?>"><?= esc(ketarunaan_format_angkatan($an)) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <label for="batch_filter_tingkat" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Tingkat</label>
                                                <select id="batch_filter_tingkat" class="form-select ecc-control-sm">
                                                    <option value="all">Semua</option>
                                                    <option value="4">Tingkat 4</option>
                                                    <option value="3">Tingkat 3</option>
                                                    <option value="2">Tingkat 2</option>
                                                    <option value="1">Tingkat 1</option>
                                                </select>
                                            </div>
                                            <div class="col-7">
                                                <label for="batch_filter_q" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Taruna</label>
                                                <input type="text" id="batch_filter_q" class="form-control ecc-control-sm" placeholder="Nama / No. Taruna">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-2 pt-1">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" id="batch_select_all_visible">
                                                <label class="form-check-label small fw-semibold text-dark" for="batch_select_all_visible">
                                                    Pilih Semua yang Tampil
                                                </label>
                                            </div>
                                            <span class="badge rounded-pill px-3 py-1 fw-semibold bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-num" id="batch_selected_badge" style="font-size: 0.75rem;">
                                                0 dipilih
                                            </span>
                                        </div>

                                        <div class="bg-white border rounded-3 p-2" style="max-height: 240px; overflow-y: auto;" id="batch_kandidat_list">
                                            <?php if (empty($kandidatTaruna)): ?>
                                                <div class="ecc-text-muted-aa small text-center py-3">Tidak ada kandidat taruna aktif tersedia.</div>
                                            <?php else: ?>
                                                <?php foreach ($kandidatTaruna as $kt): ?>
                                                    <?php
                                                    $nt         = (string) $kt['nomor_taruna'];
                                                    $prodiIdKt  = (string) ($kt['program_studi_id'] ?? '');
                                                    $angkIdKt   = (string) ($kt['angkatan_id'] ?? '');
                                                    $tingkatKt  = (string) ($kt['tingkat'] ?? '1');
                                                    $searchBlob = strtolower($nt . ' ' . (string) $kt['nama_lengkap'] . ' ' . (string) ($kt['kode_prodi'] ?? ''));
                                                    ?>
                                                    <label class="d-flex align-items-start gap-2 p-2 rounded-2 border-bottom batch-kandidat-item"
                                                           style="cursor: pointer;"
                                                           data-prodi="<?= esc($prodiIdKt) ?>"
                                                           data-angkatan="<?= esc($angkIdKt) ?>"
                                                           data-tingkat="<?= esc($tingkatKt) ?>"
                                                           data-search="<?= esc($searchBlob) ?>">
                                                        <input type="checkbox" class="form-check-input mt-1 flex-shrink-0 batch-kandidat-chk" name="nomor_taruna_list[]" value="<?= esc($nt) ?>">
                                                        <span class="small lh-sm">
                                                            <strong class="text-dark d-block"><?= esc((string) $kt['nama_lengkap']) ?></strong>
                                                            <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                                                <?= esc($nt) ?> &bull; <?= esc((string) ($kt['kode_prodi'] ?? '-')) ?> &bull; Tkt <?= esc($tingkatKt) ?>
                                                                <?php if (!empty($kt['tahun_masuk'])): ?>
                                                                    &bull; <?= esc(ketarunaan_format_angkatan($kt)) ?>
                                                                <?php endif; ?>
                                                            </span>
                                                        </span>
                                                    </label>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="batch_tanggal_lulus" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Lulus (SK) <span class="text-danger">*</span></label>
                                    <input type="text" name="tanggal_lulus" id="batch_tanggal_lulus" class="form-control ecc-control-sm ecc-num js-alm-date"
                                           value="<?= esc((string) old('tanggal_lulus', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="batch_nomor_keputusan" class="form-label fw-semibold ecc-text-muted-aa small">Nomor SK Kelulusan <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_keputusan" id="batch_nomor_keputusan" class="form-control ecc-control-sm"
                                           value="<?= esc((string) old('nomor_keputusan', '')) ?>"
                                           placeholder="SK.LULUS/PKTJ/..." required>
                                </div>
                                <div class="col-12">
                                    <label for="batch_alasan_perubahan" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Pengajuan</label>
                                    <textarea name="alasan_perubahan" id="batch_alasan_perubahan" rows="2" class="form-control rounded-3" placeholder="Contoh: Yudisium Wisuda Angkatan LX Tahun 2026"><?= esc((string) old('alasan_perubahan', '')) ?></textarea>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile alm-touch-ctrl">
                                        Kirim Pengajuan Massal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Mode 2: Pengajuan Satuan -->
                        <div class="tab-pane fade" id="pane-single" role="tabpanel" aria-labelledby="tab-single-btn">
                            <form method="post" action="<?= base_url('ketarunaan/alumni/kelulusan/ajukan') ?>" class="row g-3 alm-form-mobile" id="formAjukanKelulusan"
                                  onsubmit="return window.eccAlmConfirmSubmit(event, 'Ajukan Kelulusan?', 'Pengajuan akan masuk ke antrean validator sebelum profil alumni dibentuk.', 'Ya, Ajukan', false);">
                                <?= csrf_field() ?>
                                <div class="col-12">
                                    <label for="nomor_taruna_lulus" class="form-label fw-semibold ecc-text-muted-aa small">Pilih Taruna <span class="text-danger">*</span></label>
                                    <select name="nomor_taruna" id="nomor_taruna_lulus" class="form-select ecc-control-sm js-alm-select2" required>
                                        <option value="">-- Pilih taruna aktif --</option>
                                        <?php $oldNomor = (string) old('nomor_taruna', ''); ?>
                                        <?php foreach ($kandidatTaruna as $kt): ?>
                                            <option value="<?= esc((string) $kt['nomor_taruna']) ?>" <?= $oldNomor === (string) $kt['nomor_taruna'] ? 'selected' : '' ?>>
                                                <?= esc((string) $kt['nomor_taruna']) ?> &mdash; <?= esc((string) $kt['nama_lengkap']) ?> (<?= esc((string) ($kt['kode_prodi'] ?? '-')) ?>, Tingkat <?= (int) ($kt['tingkat'] ?? 1) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="tanggal_lulus" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Lulus <span class="text-danger">*</span></label>
                                    <input type="text" name="tanggal_lulus" id="tanggal_lulus" class="form-control ecc-control-sm ecc-num js-alm-date"
                                           value="<?= esc((string) old('tanggal_lulus', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nomor_keputusan" class="form-label fw-semibold ecc-text-muted-aa small">Nomor SK Kelulusan <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_keputusan" id="nomor_keputusan" class="form-control ecc-control-sm"
                                           value="<?= esc((string) old('nomor_keputusan', '')) ?>"
                                           placeholder="SK.LULUS/PKTJ/..." required>
                                </div>
                                <div class="col-12">
                                    <label for="alasan_perubahan_lulus" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Pengajuan</label>
                                    <textarea name="alasan_perubahan" id="alasan_perubahan_lulus" rows="2" class="form-control rounded-3" placeholder="Contoh: Yudisium akhir tahun akademik berjalan"><?= esc((string) old('alasan_perubahan', '')) ?></textarea>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile alm-touch-ctrl">
                                        Kirim ke Antrean Validasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Antrean Validasi Kelulusan (Mendukung Validasi Massal & Satuan) -->
        <div class="col-12 <?= $canManage ? 'col-lg-7' : '' ?>">
            <div class="ecc-alm-card mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="h6 fw-bold text-dark mb-0">Antrean Validasi Kelulusan</h2>
                    <span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-num" style="font-size: 0.75rem;">
                        <?= count($pending) ?> menunggu
                    </span>
                </div>

                <?php if (empty($pending)): ?>
                    <div class="ecc-text-muted-aa py-3 small">
                        Tidak ada pengajuan kelulusan yang menunggu persetujuan saat ini.
                    </div>
                <?php else: ?>
                    <?php if ($canValidate): ?>
                        <!-- Bilah Aksi Validasi Massal (Batch) -->
                        <form method="post" action="<?= base_url('ketarunaan/alumni/kelulusan/validasi-batch') ?>" class="p-3 rounded-3 bg-light border mb-3 alm-form-mobile" id="formValidasiKelulusanBatch">
                            <?= csrf_field() ?>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="queue_select_all">
                                    <label class="form-check-label small fw-semibold text-dark" for="queue_select_all">
                                        Pilih Semua Antrean
                                    </label>
                                </div>
                                <span class="badge rounded-pill px-3 py-1 fw-semibold bg-primary-subtle text-primary-emphasis border border-primary-subtle ecc-num" id="queue_selected_badge" style="font-size: 0.75rem;">
                                    0 dipilih
                                </span>
                            </div>
                            <div id="queue_batch_hidden_inputs"></div>
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <input type="text" name="catatan_validasi" class="form-control ecc-control-sm" placeholder="Catatan massal (wajib jika dikembalikan)" aria-label="Catatan validasi massal">
                                </div>
                                <div class="col-12 col-md-7 d-flex flex-wrap justify-content-md-end gap-2">
                                    <button type="submit" name="keputusan" value="perlu_perbaikan" class="btn btn-outline-danger ecc-btn-action alm-touch-ctrl batch-val-btn">
                                        Kembalikan Terpilih
                                    </button>
                                    <button type="submit" name="keputusan" value="tervalidasi" class="btn btn-success ecc-btn-action alm-touch-ctrl batch-val-btn">
                                        Setujui Terpilih &amp; Bentuk Alumni
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="d-flex flex-column gap-3" style="max-height: 480px; overflow-y: auto;">
                        <?php foreach ($pending as $row): ?>
                            <div class="ecc-alm-timeline-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <?php if ($canValidate): ?>
                                            <input type="checkbox" class="form-check-input mt-1 flex-shrink-0 queue-item-chk" value="<?= (int) $row['id'] ?>" aria-label="Pilih <?= esc((string) $row['nama_lengkap']) ?>">
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc((string) $row['nama_lengkap']) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num small">
                                                No. Taruna: <?= esc((string) $row['nomor_taruna']) ?>
                                                &bull; <?= esc((string) ($row['kode_prodi'] ?? '-')) ?>
                                                &bull; Angkatan <?= esc(ketarunaan_format_angkatan($row['tahun_masuk'] ?? null)) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?= ketarunaan_validasi_badge((string) ($row['status_validasi'] ?? 'diajukan')) ?>
                                </div>
                                <div class="text-dark ecc-num mb-1 small">
                                    Tanggal Lulus: <strong><?= esc(ketarunaan_format_tanggal($row['mulai_berlaku'] ?? null)) ?></strong>
                                    &bull; SK: <strong><?= esc((string) ($row['nomor_keputusan'] ?? '-')) ?></strong>
                                </div>
                                <?php if (!empty($row['alasan_perubahan'])): ?>
                                    <div class="ecc-text-muted-aa mb-3 small"><?= esc((string) $row['alasan_perubahan']) ?></div>
                                <?php endif; ?>

                                <?php if ($canValidate): ?>
                                    <form method="post" action="<?= base_url('ketarunaan/alumni/kelulusan/validasi/' . (int) $row['id']) ?>" class="row g-2 align-items-center alm-form-mobile ecc-confirm-form" data-confirm-text="Keputusan persetujuan akan membentuk profil dasar alumni.">
                                        <?= csrf_field() ?>
                                        <div class="col-12 col-md-6">
                                            <input type="text" name="catatan_validasi" class="form-control ecc-control-sm" placeholder="Catatan validator (wajib jika dikembalikan)" aria-label="Catatan validator">
                                        </div>
                                        <div class="col-12 col-md-6 d-flex flex-wrap justify-content-md-end gap-2">
                                            <button type="submit" name="keputusan" value="perlu_perbaikan" class="btn btn-outline-danger ecc-btn-action alm-touch-ctrl">
                                                Kembalikan
                                            </button>
                                            <button type="submit" name="keputusan" value="tervalidasi" class="btn btn-success ecc-btn-action alm-touch-ctrl">
                                                Setujui &amp; Bentuk Alumni
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Kelulusan Tervalidasi Terbaru -->
            <div class="ecc-alm-card p-0 overflow-hidden d-none d-md-block">
                <div class="px-4 py-3 border-bottom">
                    <h2 class="h6 fw-bold text-dark mb-0">Penetapan Kelulusan Terbaru</h2>
                </div>
                <div class="table-responsive ecc-table-alm-wrap">
                    <table class="table ecc-table-alm mb-0" data-sortable-table="true">
                        <thead>
                            <tr>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="0"><span>Taruna</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="1"><span>Tanggal &amp; No. SK</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="2"><span>Profil Alumni</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentValidated)): ?>
                                <tr>
                                    <td colspan="3" class="text-center ecc-text-muted-aa py-4">Belum ada penetapan kelulusan terbaru.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentValidated as $rv): ?>
                                    <tr>
                                        <td data-sort-val="<?= esc(strtolower((string) $rv['nama_lengkap'])) ?>">
                                            <div class="fw-semibold text-dark"><?= esc((string) $rv['nama_lengkap']) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $rv['nomor_taruna']) ?> &bull; <?= esc((string) ($rv['kode_prodi'] ?? '-')) ?></div>
                                        </td>
                                        <td data-sort-val="<?= esc((string) ($rv['mulai_berlaku'] ?? '')) ?>">
                                            <div class="ecc-num"><?= esc(ketarunaan_format_tanggal($rv['mulai_berlaku'] ?? null)) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) ($rv['nomor_keputusan'] ?? '-')) ?></div>
                                        </td>
                                        <td data-sort-val="<?= esc((string) ($rv['status_profil'] ?? '')) ?>">
                                            <?php if (!empty($rv['status_profil'])): ?>
                                                <?= ketarunaan_alumni_status_badge((string) $rv['status_profil']) ?>
                                            <?php else: ?>
                                                <span class="ecc-text-muted-aa">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kartu Mobile Penetapan Kelulusan Terbaru -->
            <div class="d-md-none d-flex flex-column gap-3">
                <h2 class="h6 fw-bold text-dark mb-0">Penetapan Kelulusan Terbaru</h2>
                <?php if (empty($recentValidated)): ?>
                    <div class="ecc-alm-card text-center ecc-text-muted-aa py-4 small">Belum ada penetapan kelulusan terbaru.</div>
                <?php else: ?>
                    <?php foreach ($recentValidated as $rv): ?>
                        <div class="ecc-alm-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc((string) $rv['nama_lengkap']) ?></div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $rv['nomor_taruna']) ?> &bull; <?= esc((string) ($rv['kode_prodi'] ?? '-')) ?></div>
                                </div>
                                <?php if (!empty($rv['status_profil'])): ?>
                                    <?= ketarunaan_alumni_status_badge((string) $rv['status_profil']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="ecc-text-muted-aa ecc-num small">
                                Lulus: <strong class="text-dark"><?= esc(ketarunaan_format_tanggal($rv['mulai_berlaku'] ?? null)) ?></strong> &bull; <?= esc((string) ($rv['nomor_keputusan'] ?? '-')) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/alumni/_scripts_common') ?>
<script>
(function () {
    // 1. Filter & Pilih Semua untuk Pengajuan Kelulusan Massal (Batch)
    var selProdi   = document.getElementById('batch_filter_prodi');
    var selAngk    = document.getElementById('batch_filter_angkatan');
    var selTingkat = document.getElementById('batch_filter_tingkat');
    var inpSearch  = document.getElementById('batch_filter_q');
    var chkAllVis  = document.getElementById('batch_select_all_visible');
    var badgeSel   = document.getElementById('batch_selected_badge');
    var items      = Array.prototype.slice.call(document.querySelectorAll('.batch-kandidat-item'));

    function updateBatchCounter() {
        var checkedCount = document.querySelectorAll('.batch-kandidat-chk:checked').length;
        if (badgeSel) {
            badgeSel.textContent = checkedCount + ' dipilih';
        }
        if (chkAllVis) {
            var visChks = items.filter(function (el) { return !el.classList.contains('d-none'); })
                .map(function (el) { return el.querySelector('.batch-kandidat-chk'); })
                .filter(Boolean);
            chkAllVis.checked = visChks.length > 0 && visChks.every(function (c) { return c.checked; });
        }
    }

    function applyBatchFilter() {
        var fProdi   = selProdi ? selProdi.value : 'all';
        var fAngk    = selAngk ? selAngk.value : 'all';
        var fTingkat = selTingkat ? selTingkat.value : 'all';
        var fQuery   = inpSearch ? inpSearch.value.trim().toLowerCase() : '';

        items.forEach(function (el) {
            var okProdi   = (fProdi === 'all' || el.dataset.prodi === fProdi);
            var okAngk    = (fAngk === 'all' || el.dataset.angkatan === fAngk);
            var okTingkat = (fTingkat === 'all' || el.dataset.tingkat === fTingkat);
            var okQuery   = (fQuery === '' || (el.dataset.search || '').indexOf(fQuery) !== -1);
            el.classList.toggle('d-none', !(okProdi && okAngk && okTingkat && okQuery));
        });
        updateBatchCounter();
    }

    [selProdi, selAngk, selTingkat].forEach(function (el) {
        if (el) {
            el.addEventListener('change', applyBatchFilter);
        }
    });
    if (inpSearch) {
        inpSearch.addEventListener('input', applyBatchFilter);
    }
    if (chkAllVis) {
        chkAllVis.addEventListener('change', function () {
            var checked = chkAllVis.checked;
            items.forEach(function (el) {
                if (!el.classList.contains('d-none')) {
                    var c = el.querySelector('.batch-kandidat-chk');
                    if (c) {
                        c.checked = checked;
                    }
                }
            });
            updateBatchCounter();
        });
    }
    items.forEach(function (el) {
        var c = el.querySelector('.batch-kandidat-chk');
        if (c) {
            c.addEventListener('change', updateBatchCounter);
        }
    });
    updateBatchCounter();

    window.eccAlmSubmitBatchKelulusan = function (event) {
        var form = document.getElementById('formAjukanKelulusanBatch');
        if (!form || form.dataset.confirmed === '1') {
            return true;
        }
        var count = document.querySelectorAll('.batch-kandidat-chk:checked').length;
        if (count === 0) {
            event.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({ title: 'Belum Ada Taruna Dipilih', text: 'Pilih minimal 1 taruna untuk pengajuan kelulusan massal.', icon: 'warning' });
            } else {
                alert('Pilih minimal 1 taruna untuk pengajuan kelulusan massal.');
            }
            return false;
        }
        return window.eccAlmConfirmSubmit(
            event,
            'Ajukan ' + count + ' Taruna Lulus?',
            'Seluruh taruna terpilih akan dikirim ke antrean validasi dengan 1 SK Kelulusan.',
            'Ya, Ajukan Semua',
            false
        );
    };

    // 2. Validasi Kelulusan Massal (Batch) pada Antrean
    var queueAllChk   = document.getElementById('queue_select_all');
    var queueBadge    = document.getElementById('queue_selected_badge');
    var queueChks     = Array.prototype.slice.call(document.querySelectorAll('.queue-item-chk'));
    var batchValForm  = document.getElementById('formValidasiKelulusanBatch');
    var hiddenHolder  = document.getElementById('queue_batch_hidden_inputs');

    function updateQueueCounter() {
        var checked = queueChks.filter(function (c) { return c.checked; });
        if (queueBadge) {
            queueBadge.textContent = checked.length + ' dipilih';
        }
        if (queueAllChk) {
            queueAllChk.checked = queueChks.length > 0 && checked.length === queueChks.length;
        }
    }

    if (queueAllChk) {
        queueAllChk.addEventListener('change', function () {
            queueChks.forEach(function (c) { c.checked = queueAllChk.checked; });
            updateQueueCounter();
        });
    }
    queueChks.forEach(function (c) {
        c.addEventListener('change', updateQueueCounter);
    });
    updateQueueCounter();

    if (batchValForm) {
        batchValForm.querySelectorAll('.batch-val-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (batchValForm.dataset.confirmed === '1') {
                    return;
                }
                e.preventDefault();
                var selectedIds = queueChks.filter(function (c) { return c.checked; }).map(function (c) { return c.value; });
                if (selectedIds.length === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'Belum Ada Antrean Dipilih', text: 'Centang minimal 1 pengajuan kelulusan pada antrean.', icon: 'warning' });
                    } else {
                        alert('Centang minimal 1 pengajuan kelulusan pada antrean.');
                    }
                    return;
                }

                var isDanger = btn.value === 'perlu_perbaikan';
                var label    = btn.textContent.trim();

                var commitBatch = function () {
                    if (hiddenHolder) {
                        hiddenHolder.replaceChildren();
                        selectedIds.forEach(function (idVal) {
                            var inp = document.createElement('input');
                            inp.type  = 'hidden';
                            inp.name  = 'riwayat_ids[]';
                            inp.value = idVal;
                            hiddenHolder.appendChild(inp);
                        });
                    }
                    batchValForm.querySelectorAll('input[data-temp-keputusan="1"]').forEach(function (el) { el.remove(); });
                    var kInp = document.createElement('input');
                    kInp.type  = 'hidden';
                    kInp.name  = 'keputusan';
                    kInp.value = btn.value || '';
                    kInp.dataset.tempKeputusan = '1';
                    batchValForm.appendChild(kInp);
                    batchValForm.dataset.confirmed = '1';
                    batchValForm.submit();
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: label + ' (' + selectedIds.length + ' taruna)?',
                        text: isDanger
                            ? 'Pengajuan terpilih akan dikembalikan untuk perbaikan.'
                            : 'Profil dasar alumni aktif akan dibentuk untuk seluruh taruna terpilih.',
                        icon: isDanger ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: isDanger ? '#dc2626' : '#0d6efd'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            commitBatch();
                        }
                    });
                } else if (window.confirm(label + ' (' + selectedIds.length + ' taruna)?')) {
                    commitBatch();
                }
            });
        });
    }

    // 3. Validasi Satuan pada Antrean
    document.querySelectorAll('.ecc-confirm-form button[type="submit"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var form = btn.closest('form');
            if (!form || form.dataset.confirmed === '1') {
                return;
            }
            e.preventDefault();
            var actionLabel = btn.textContent.trim();
            var isDanger = btn.value === 'perlu_perbaikan';
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = btn.name || 'keputusan';
            hiddenInput.value = btn.value || '';

            var submitNow = function () {
                form.querySelectorAll('input[data-temp-keputusan="1"]').forEach(function (el) { el.remove(); });
                hiddenInput.dataset.tempKeputusan = '1';
                form.appendChild(hiddenInput);
                form.dataset.confirmed = '1';
                form.submit();
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: actionLabel + '?',
                    text: form.dataset.confirmText || 'Keputusan ini akan disimpan.',
                    icon: isDanger ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: isDanger ? '#dc2626' : '#0d6efd'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitNow();
                    }
                });
            } else if (window.confirm(actionLabel + '?')) {
                submitNow();
            }
        });
    });
})();
</script>
<?= $this->endSection() ?>
