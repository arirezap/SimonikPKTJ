<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Bundel Pertanyaan Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$bundles   = $bundles ?? [];
$perms     = $perms ?? [];
$canManage = !empty($perms['can_manage']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="ecc-ts-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold text-dark mb-0">Pustaka Bundel Pertanyaan</h2>
                <div class="ecc-text-muted-aa small">Satu bundel pertanyaan dapat digunakan kembali atau diduplikasi dan dimodifikasi untuk periode survei lain.</div>
            </div>
            <span class="ecc-text-muted-aa small ecc-num"><?= count($bundles) ?> bundel</span>
        </div>

        <?php if (empty($bundles)): ?>
            <div class="text-center py-5 ecc-text-muted-aa small">Belum ada bundel pertanyaan.</div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($bundles as $bn): ?>
                    <?php $bid = (int) $bn['id']; ?>
                    <div class="col-12 col-lg-6">
                        <div class="ts-q-item h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <div class="fw-bold text-dark"><?= esc((string) $bn['nama_bundle']) ?></div>
                                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $bn['kode_bundle']) ?></div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-end gap-1">
                                        <?= ketarunaan_tracer_target_badge((string) ($bn['target_responden'] ?? 'alumni')) ?>
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= (int) ($bn['is_aktif'] ?? 1) === 1 ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' ?>" style="font-size: 0.75rem;">
                                            <?= (int) ($bn['is_aktif'] ?? 1) === 1 ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if (!empty($bn['deskripsi'])): ?>
                                    <p class="ecc-text-muted-aa small mb-3"><?= esc(mb_strimwidth(trim(preg_replace('/\s+/', ' ', (string) $bn['deskripsi'])), 0, 210, '...')) ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top">
                                    <div class="small ecc-text-muted-aa ecc-num">
                                        <strong><?= (int) $bn['jumlah_pertanyaan'] ?></strong> pertanyaan &bull; Dipakai di <strong><?= (int) $bn['jumlah_periode_pakai'] ?></strong> periode
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if ($canManage): ?>
                                            <a href="<?= base_url('ketarunaan/tracer-study/periode?create_bundle_id=' . $bid) ?>" class="btn btn-primary ecc-btn-action">
                                                Buat Periode
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('ketarunaan/tracer-study/bundel/' . $bid) ?>" class="btn btn-outline-primary ecc-btn-action">
                                            <?= $canManage ? 'Kelola Soal' : 'Lihat Soal' ?>
                                        </a>
                                        <?php if ($canManage): ?>
                                            <form method="post" action="<?= base_url('ketarunaan/tracer-study/bundel/' . $bid . '/duplikat') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-light border ecc-btn-action">Duplikasi</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="modalBundleTracer" tabindex="-1" aria-labelledby="modalBundleTracerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= base_url('ketarunaan/tracer-study/bundel/simpan') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="bundle_id" id="bModalId" value="0">

                <div class="modal-header border-bottom px-4 py-3">
                    <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalBundleTracerLabel">Buat Bundel Pertanyaan Baru</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="bModalNama" class="form-label fw-semibold small">Nama Bundel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control ecc-control-sm" id="bModalNama" name="nama_bundle" required placeholder="Contoh: Bundel Tracer Study Lulusan 2026">
                    </div>
                    <div class="mb-3">
                        <label for="bModalTarget" class="form-label fw-semibold small">Sasaran Responden <span class="text-danger">*</span></label>
                        <select class="form-select ecc-control-sm" id="bModalTarget" name="target_responden" required>
                            <option value="alumni">Lulusan / Alumni PKTJ</option>
                            <option value="mitra">Pengguna Lulusan (Atasan Langsung / Instansi)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bModalKode" class="form-label fw-semibold small">Kode Bundel</label>
                        <input type="text" class="form-control ecc-control-sm text-uppercase" id="bModalKode" name="kode_bundle" maxlength="40" placeholder="Otomatis jika kosong">
                    </div>
                    <div>
                        <label for="bModalDesk" class="form-label fw-semibold small">Surat Pengantar / Deskripsi Singkat</label>
                        <textarea class="form-control" id="bModalDesk" name="deskripsi" rows="3" placeholder="Tujuan atau surat pengantar kuesioner..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">Simpan Bundel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function resetBundleModal() {
    var elId = document.getElementById('bModalId');
    if (elId) elId.value = '0';
    var elN = document.getElementById('bModalNama');
    if (elN) elN.value = '';
    var elK = document.getElementById('bModalKode');
    if (elK) elK.value = '';
    var elD = document.getElementById('bModalDesk');
    if (elD) elD.value = '';
}
</script>
<?= $this->endSection() ?>
