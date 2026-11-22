<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Verifikasi dan Publikasi Lowongan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$queue        = $queue ?? [];
$selected     = $selected ?? null;
$selectedDocs = $selectedDocs ?? [];
$refs         = $refs ?? [];
$perms        = $perms ?? [];
$waktuAcuan   = $waktuAcuan ?? date('Y-m-d H:i:s');
$canValidate  = !empty($perms['can_validate']);
$canPublish   = !empty($perms['can_publish']);
$checklistCat = ketarunaan_lowongan_checklist_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <div class="row g-3">
        <!-- Daftar Antrean Pemeriksaan & Penerbitan -->
        <div class="col-12 col-lg-4">
            <div class="ecc-low-card">
                <h2 class="h6 fw-bold text-dark mb-3">Antrean Lowongan</h2>
                <?php if (empty($queue)): ?>
                    <div class="ecc-text-muted-aa small">Belum ada pengajuan lowongan.</div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2" style="max-height: 480px; overflow-y: auto;">
                        <?php foreach ($queue as $item): ?>
                            <?php $isActive = $selected && (int) $selected['id'] === (int) $item['id']; ?>
                            <a href="<?= base_url('ketarunaan/lowongan/verifikasi/' . (int) $item['id']) ?>"
                               class="ecc-low-queue-item <?= $isActive ? 'active' : '' ?>"<?= $isActive ? ' aria-current="true"' : '' ?>>
                                <div class="fw-semibold text-dark mb-1"><?= esc($item['judul_lowongan']) ?></div>
                                <div class="ecc-text-muted-aa mb-2" style="font-size: 0.75rem;"><?= esc($item['nama_perusahaan'] ?? '-') ?></div>
                                <div class="d-flex flex-wrap gap-1">
                                    <?= ketarunaan_validasi_badge((string) ($item['status_validasi'] ?? 'draf')) ?>
                                    <?= ketarunaan_lowongan_publikasi_badge($item, $waktuAcuan) ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel Detail Pemeriksaan & Publikasi Terpisah -->
        <div class="col-12 col-lg-8">
            <?php if (!$selected): ?>
                <div class="ecc-low-card ecc-text-muted-aa small">Pilih salah satu lowongan untuk memeriksa rinciannya.</div>
            <?php else: ?>
                <?php
                $isValidated  = ($selected['status_validasi'] ?? '') === 'tervalidasi';
                $efektif      = $selected['status_efektif'] ?? ketarunaan_lowongan_status_efektif($selected, $waktuAcuan);
                $deadlineOk   = (string) ($selected['batas_pendaftaran'] ?? '') >= $waktuAcuan;
                $canTerbitNow = $canPublish && $isValidated && $deadlineOk;
                $checklist    = $selected['checklist'] ?? [];
                ?>
                <!-- Data Pengajuan -->
                <div class="ecc-low-card mb-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
                        <div>
                            <h2 class="h5 fw-bold text-dark mb-1"><?= esc($selected['judul_lowongan']) ?></h2>
                            <div class="ecc-text-muted-aa small">
                                <?= esc($selected['nama_perusahaan'] ?? '-') ?> &bull; <?= esc($selected['lokasi'] ?? '-') ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?= ketarunaan_validasi_badge((string) ($selected['status_validasi'] ?? 'draf')) ?>
                            <?= ketarunaan_lowongan_publikasi_badge($selected, $waktuAcuan) ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3 p-3 rounded-3 bg-light border">
                        <div class="col-12 col-md-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Kontak Mitra</div>
                            <div class="mt-1"><?= ketarunaan_perusahaan_verifikasi_badge((int) ($selected['perusahaan_verified'] ?? 0)) ?></div>
                            <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;"><?= esc($selected['kontak_person'] ?? '-') ?></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Batas Lamaran</div>
                            <div class="fw-semibold text-dark ecc-num small"><?= esc(ketarunaan_format_tanggal($selected['batas_pendaftaran'] ?? null)) ?></div>
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($refs['metode_catalog'][$selected['metode_lamaran'] ?? ''] ?? '-') ?></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Dokumen Sumber</div>
                            <div class="fw-semibold text-dark ecc-num small"><?= count($selectedDocs) ?> tautan/berkas</div>
                            <a href="<?= base_url('ketarunaan/lowongan/dokumen/' . (int) $selected['id']) ?>" class="fw-semibold text-decoration-none" style="font-size: 0.75rem;">Periksa dokumen sumber</a>
                        </div>
                    </div>

                    <?php if (!empty($selected['catatan_pemeriksa'])): ?>
                        <div class="alert alert-secondary border-0 rounded-3 py-2 px-3 mb-0 small">
                            Catatan pemeriksa terakhir: <?= esc($selected['catatan_pemeriksa']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Checklist Pemeriksaan Lowongan & Keputusan Validasi -->
                <div class="ecc-low-card mb-3">
                    <h3 class="h6 fw-bold text-dark mb-1">Checklist Pemeriksaan Lowongan</h3>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Validasi memeriksa keabsahan lowongan dan dokumennya secara terpisah dari penayangan.
                    </p>

                    <form method="post" action="<?= base_url('ketarunaan/lowongan/verifikasi/' . (int) $selected['id'] . '/simpan') ?>" class="low-form-mobile" id="formVerifikasiLowongan">
                        <?= csrf_field() ?>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <?php foreach ($checklistCat as $cKey => $cLabel): ?>
                                <div class="form-check p-3 rounded-3 bg-light border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="checklist[<?= esc($cKey) ?>]" value="1"
                                           id="chk_<?= esc($cKey) ?>" <?= !empty($checklist[$cKey]) ? 'checked' : '' ?> <?= !$canValidate ? 'disabled' : '' ?>>
                                    <label class="form-check-label text-dark small" for="chk_<?= esc($cKey) ?>">
                                        <?= esc($cLabel) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_pemeriksa" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Pemeriksa (Wajib jika dikembalikan)</label>
                            <textarea name="catatan_pemeriksa" id="catatan_pemeriksa" rows="2" class="form-control rounded-3" <?= !$canValidate ? 'readonly' : '' ?>
                                      placeholder="Tulis alasan atau hal yang perlu diperbaiki"><?= esc(old('catatan_pemeriksa', $selected['catatan_pemeriksa'] ?? '')) ?></textarea>
                        </div>

                        <?php if ($canValidate): ?>
                            <input type="hidden" name="keputusan" id="keputusan_verifikasi" value="tervalidasi">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" onclick="document.getElementById('keputusan_verifikasi').value='tervalidasi';" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile low-touch-ctrl">
                                    Validasi Lowongan
                                </button>
                                <button type="submit" onclick="document.getElementById('keputusan_verifikasi').value='perlu_perbaikan';" class="btn btn-outline-danger ecc-control-sm px-4 fw-semibold btn-tactile low-touch-ctrl">
                                    Kembalikan untuk Perbaikan
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Status Publikasi Terpisah (LOW-05 & UAT-15) -->
                <div class="ecc-low-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="h6 fw-bold text-dark mb-0">Status Publikasi Terpisah</h3>
                        <?= ketarunaan_lowongan_publikasi_badge($selected, $waktuAcuan) ?>
                    </div>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Lowongan tervalidasi yang masih berstatus draf tidak akan muncul ke publik atau alumni sebelum tombol terbitkan dijalankan.
                    </p>

                    <?php if ($canPublish): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <form method="post" action="<?= base_url('ketarunaan/lowongan/publikasi/' . (int) $selected['id']) ?>" class="d-inline"
                                  onsubmit="return window.eccLowConfirmSubmit(event, 'Terbitkan Lowongan?', 'Lowongan akan ditayangkan ke direktori resmi alumni dan taruna.', 'Ya, Terbitkan', false);">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi_publikasi" value="terbit">
                                <button type="submit" class="btn btn-success ecc-control-sm px-4 fw-semibold btn-tactile low-touch-ctrl" <?= $canTerbitNow ? '' : 'disabled' ?>>
                                    Terbitkan Lowongan
                                </button>
                            </form>
                            <?php if (($selected['status_publikasi'] ?? '') === 'terbit'): ?>
                                <form method="post" action="<?= base_url('ketarunaan/lowongan/publikasi/' . (int) $selected['id']) ?>" class="d-inline"
                                      onsubmit="return window.eccLowConfirmSubmit(event, 'Tahan sebagai Draf?', 'Lowongan akan diturunkan sementara dari direktori tayang.', 'Ya, Tahan', true);">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="aksi_publikasi" value="draf">
                                    <button type="submit" class="btn btn-outline-secondary ecc-control-sm px-4 fw-semibold btn-tactile low-touch-ctrl">
                                        Tahan sebagai Draf
                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="<?= base_url('ketarunaan/lowongan/riwayat/' . (int) $selected['id']) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl">
                                Riwayat &amp; Tenggat
                            </a>
                        </div>
                        <?php if (!$isValidated): ?>
                            <div class="text-warning-emphasis mt-2" style="font-size: 0.75rem;">
                                Tombol terbitkan aktif setelah lowongan berstatus tervalidasi.
                            </div>
                        <?php elseif (!$deadlineOk): ?>
                            <div class="text-danger mt-2" style="font-size: 0.75rem;">
                                Batas pendaftaran telah melewati tanggal hari ini. Perpanjang tanggal tenggat terlebih dahulu.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<?= $this->endSection() ?>
