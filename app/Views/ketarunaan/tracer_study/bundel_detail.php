<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Bundel Pertanyaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail      = $detail ?? [];
$bundle      = $detail['bundle'] ?? [];
$pertanyaan  = $detail['pertanyaan'] ?? [];
$refs        = $refs ?? [];
$perms       = $perms ?? [];
$canManage   = !empty($perms['can_manage']);
$bid         = (int) ($bundle['id'] ?? 0);
$tipeCatalog = $refs['tipe_jawaban'] ?? ketarunaan_tracer_tipe_jawaban_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="mb-3">
        <a href="<?= base_url('ketarunaan/tracer-study/bundel') ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Pustaka Bundel
        </a>
    </div>

    <div class="ecc-ts-card mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) $bundle['nama_bundle']) ?></h2>
                <div class="ecc-text-muted-aa small ecc-num">
                    Kode: <strong><?= esc((string) $bundle['kode_bundle']) ?></strong>
                    <?php if (!empty($bundle['deskripsi'])): ?>
                        &bull; <?= esc((string) $bundle['deskripsi']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($canManage): ?>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl shadow-sm" data-bs-toggle="modal" data-bs-target="#modalQuestionTracer" onclick="resetQuestionModal(<?= count($pertanyaan) + 1 ?>)">
                        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Tambah Pertanyaan
                    </button>
                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/bundel/' . $bid . '/duplikat') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile ts-touch-ctrl">Duplikasi Bundel</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ecc-ts-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-bold text-dark mb-0">Daftar Pertanyaan dalam Bundel</h3>
            <span class="ecc-text-muted-aa small ecc-num"><?= count($pertanyaan) ?> pertanyaan</span>
        </div>

        <div class="alert alert-light border small text-dark mb-3 py-2 px-3">
            Perubahan pada bundel master hanya memengaruhi periode baru yang akan menggunakan bundel ini; periode survei yang sudah dibuat sebelumnya tetap aman dan tidak berubah.
        </div>

        <?php if (empty($pertanyaan)): ?>
            <div class="text-center py-4 ecc-text-muted-aa small">Bundel ini belum memiliki pertanyaan.</div>
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
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-2 py-1 ecc-num" style="font-size: 0.72rem;">#<?= (int) $q['urutan'] ?></span>
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.72rem;"><?= esc((string) $q['bagian']) ?></span>
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size: 0.72rem;"><?= esc($tipeCatalog[$q['tipe_jawaban']] ?? $q['tipe_jawaban']) ?></span>
                                    <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.72rem;">Kode: <?= esc((string) $q['kode_pertanyaan']) ?></span>
                                </div>
                                <div class="fw-semibold text-dark mb-1"><?= esc((string) $q['teks_pertanyaan']) ?></div>
                                <?php if (!empty($q['keterangan_bantuan'])): ?>
                                    <div class="ecc-text-muted-aa small mb-1"><?= esc((string) $q['keterangan_bantuan']) ?></div>
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
                            <?php if ($canManage): ?>
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
                                    <form method="post" action="<?= base_url('ketarunaan/tracer-study/bundel/' . $bid . '/pertanyaan/' . $qid . '/hapus') ?>" class="d-inline js-ts-b-del">
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

<?php if ($canManage): ?>
    <?php
    $qFormAction = base_url('ketarunaan/tracer-study/bundel/' . $bid . '/pertanyaan/simpan');
    echo $this->include('ketarunaan/tracer_study/_question_form_modal');
    ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-ts-b-del').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Pertanyaan?',
                    text: 'Pertanyaan ini akan dihapus dari bundel.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-4 shadow border-0' }
                }).then(function(res) {
                    if (res.isConfirmed) form.submit();
                });
            } else if (confirm('Hapus pertanyaan ini dari bundel?')) {
                form.submit();
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
