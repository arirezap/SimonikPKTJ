<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Pertanyaan Periode Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail           = $detail ?? [];
$periode          = $detail['periode'] ?? [];
$pertanyaan       = $detail['pertanyaan'] ?? [];
$jumlahRespons    = (int) ($detail['jumlah_respons'] ?? 0);
$canEditQuestions = !empty($detail['can_edit_questions']);
$canRevertDraft   = !empty($detail['can_revert_draft']);
$refs             = $refs ?? [];
$perms            = $perms ?? [];
$canManage        = !empty($perms['can_manage']);
$canToggle        = !empty($perms['can_toggle']);
$pid              = (int) ($periode['id'] ?? 0);
$status           = (string) ($periode['status_periode'] ?? 'draf');
$tipeCatalog      = $refs['tipe_jawaban'] ?? ketarunaan_tracer_tipe_jawaban_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="mb-3">
        <a href="<?= base_url('ketarunaan/tracer-study/periode') ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Daftar Periode
        </a>
    </div>

    <!-- Ringkasan Status & Kontrol Pembukaan -->
    <div class="ecc-ts-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h2 class="h5 fw-bold text-dark mb-0"><?= esc((string) $periode['nama_periode']) ?></h2>
                    <?= ketarunaan_tracer_status_periode_badge($status) ?>
                </div>
                <div class="ecc-text-muted-aa small">
                    <?php if ($canEditQuestions): ?>
                        Status <strong>Draf</strong>: Anda dapat menambah, mengubah, atau menghapus pertanyaan sebelum survei dibuka.
                    <?php else: ?>
                        Daftar pertanyaan <strong>terkunci</strong> karena survei sudah dibuka/ditutup (<?= $jumlahRespons ?> respons tercatat). Modifikasi dapat disimpan sebagai bundel baru untuk periode berikutnya.
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <?php if ($canManage && $canEditQuestions): ?>
                    <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl shadow-sm" data-bs-toggle="modal" data-bs-target="#modalQuestionTracer" onclick="resetQuestionModal(<?= count($pertanyaan) + 1 ?>)">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Tambah Pertanyaan
                    </button>
                <?php endif; ?>

                <?php if ($canManage && !empty($pertanyaan)): ?>
                    <button type="button" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl" data-bs-toggle="modal" data-bs-target="#modalSaveAsBundle">
                        Simpan sebagai Bundel
                    </button>
                <?php endif; ?>

                <?php if ($canToggle && $status === 'draf'): ?>
                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-q-confirm" data-title="Buka Survei?" data-text="Pertanyaan akan dikunci dan alumni sasaran mulai dapat mengisi survei." data-confirm="Buka Survei">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status_periode" value="dibuka">
                        <button type="submit" class="btn btn-success ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl">
                            Buka Survei
                        </button>
                    </form>
                <?php elseif ($canToggle && $status === 'dibuka'): ?>
                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-q-confirm" data-title="Tutup Survei?" data-text="Pengisian survei oleh alumni pada periode ini akan dihentikan." data-confirm="Tutup Survei">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status_periode" value="ditutup">
                        <button type="submit" class="btn btn-danger ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl">
                            Tutup Survei
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($canToggle && $canRevertDraft): ?>
                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/status') ?>" class="d-inline js-ts-q-confirm" data-title="Kembalikan ke Draf?" data-text="Pertanyaan akan dibuka kembali untuk diubah karena belum ada respons masuk." data-confirm="Tarik ke Draf">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status_periode" value="draf">
                        <button type="submit" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl">
                            Tarik ke Draf
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daftar Pertanyaan -->
    <div class="ecc-ts-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-bold text-dark mb-0">Daftar Pertanyaan Kuesioner</h3>
            <span class="ecc-text-muted-aa small ecc-num"><?= count($pertanyaan) ?> pertanyaan</span>
        </div>

        <div class="alert alert-light border small text-dark mb-3 py-2 px-3">
            Bagian dasar (status pekerjaan, masa tunggu, nama instansi, sektor, jabatan, dan kesesuaian bidang/jenjang) otomatis tersedia di bagian atas formulir. Pertanyaan di bawah ini merupakan pertanyaan tambahan dari bundel periode ini.
        </div>

        <?php if (empty($pertanyaan)): ?>
            <div class="text-center py-4 ecc-text-muted-aa small">
                Belum ada pertanyaan pada periode ini. Klik <strong>Tambah Pertanyaan</strong> untuk menyusun kuesioner.
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($pertanyaan as $q): ?>
                    <?php
                    $qid  = (int) $q['id'];
                    $opsi = $q['opsi_parsed'] ?? [];
                    ?>
                    <div class="ts-q-item">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-2">
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1 ecc-num" style="font-size: 0.72rem;">
                                        #<?= (int) $q['urutan'] ?>
                                    </span>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                        <?= esc((string) $q['bagian']) ?>
                                    </span>
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                        <?= esc($tipeCatalog[$q['tipe_jawaban']] ?? $q['tipe_jawaban']) ?>
                                    </span>
                                    <?php if ((int) ($q['is_wajib'] ?? 1) === 1): ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-2 py-1" style="font-size: 0.7rem;">Wajib</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-2 py-1" style="font-size: 0.7rem;">Opsional</span>
                                    <?php endif; ?>
                                    <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.72rem;">Kode: <?= esc((string) $q['kode_pertanyaan']) ?></span>
                                </div>
                                <div class="fw-semibold text-dark mb-1"><?= esc((string) $q['teks_pertanyaan']) ?></div>
                                <?php if (!empty($q['keterangan_bantuan'])): ?>
                                    <div class="ecc-text-muted-aa small mb-2"><?= esc((string) $q['keterangan_bantuan']) ?></div>
                                <?php endif; ?>

                                <?php if (in_array($q['tipe_jawaban'], ['pilihan_tunggal', 'pilihan_ganda'], true) && !empty($opsi)): ?>
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <?php foreach ($opsi as $op): ?>
                                            <span class="badge bg-light text-dark border fw-normal px-2 py-1" style="font-size: 0.75rem;"><?= esc((string) $op) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif ($q['tipe_jawaban'] === 'skala_likert'): ?>
                                    <div class="ecc-text-muted-aa small mt-1">
                                        Skala 1 (<?= esc((string) ($opsi['min_label'] ?? 'Sangat Kurang')) ?>) s.d. 5 (<?= esc((string) ($opsi['max_label'] ?? 'Sangat Baik')) ?>)
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($canManage && $canEditQuestions): ?>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <button type="button" class="btn btn-light border ecc-btn-action"
                                            onclick="openEditQuestionModal(this)"
                                            data-id="<?= $qid ?>"
                                            data-bagian="<?= esc((string) $q['bagian']) ?>"
                                            data-kode="<?= esc((string) $q['kode_pertanyaan']) ?>"
                                            data-urutan="<?= (int) $q['urutan'] ?>"
                                            data-teks="<?= esc((string) $q['teks_pertanyaan']) ?>"
                                            data-tipe="<?= esc((string) $q['tipe_jawaban']) ?>"
                                            data-wajib="<?= (int) ($q['is_wajib'] ?? 1) ?>"
                                            data-bantuan="<?= esc((string) ($q['keterangan_bantuan'] ?? '')) ?>"
                                            data-opsi='<?= esc((string) ($q['opsi_jawaban_json'] ?? '[]')) ?>'>
                                        Ubah
                                    </button>
                                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/pertanyaan/' . $qid . '/hapus') ?>" class="d-inline js-ts-q-confirm" data-title="Hapus Pertanyaan?" data-text="Pertanyaan ini akan dihapus dari periode." data-confirm="Ya, Hapus">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger ecc-btn-action">Hapus</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($canManage && $canEditQuestions): ?>
    <?php
    $qFormAction = base_url('ketarunaan/tracer-study/periode/' . $pid . '/pertanyaan/simpan');
    echo $this->include('ketarunaan/tracer_study/_question_form_modal');
    ?>
<?php endif; ?>

<?php if ($canManage && !empty($pertanyaan)): ?>
<div class="modal fade" id="modalSaveAsBundle" tabindex="-1" aria-labelledby="modalSaveAsBundleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= base_url('ketarunaan/tracer-study/periode/' . $pid . '/simpan-bundel') ?>">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom px-4 py-3">
                    <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalSaveAsBundleLabel">Simpan sebagai Bundel Baru</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <label for="saveBundleName" class="form-label fw-semibold small">Nama Bundel Pertanyaan Baru</label>
                    <input type="text" class="form-control ecc-control-sm" id="saveBundleName" name="nama_bundle" required value="Bundel <?= esc((string) $periode['nama_periode']) ?>">
                    <div class="form-text small">Seluruh <?= count($pertanyaan) ?> pertanyaan pada periode ini akan disimpan sebagai paket master agar dapat dipakai di periode lain.</div>
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
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-ts-q-confirm').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var title = form.dataset.title || 'Konfirmasi';
            var text  = form.dataset.text || 'Lanjutkan tindakan ini?';
            var btnTx = form.dataset.confirm || 'Ya';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: btnTx,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function(res) {
                    if (res.isConfirmed) form.submit();
                });
            } else if (confirm(title + '\n' + text)) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
