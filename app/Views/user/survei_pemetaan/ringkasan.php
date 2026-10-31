<?= $this->extend('layouts/public_survei') ?>
<?= $this->section('title') ?>Ringkasan Survei Pemetaan SDM<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .summary-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 24px;
    }

    .summary-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .summary-q-item {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-q-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use App\Libraries\SurveiPemetaanConfig;

$namaResponden = trim((string) ($respon['snapshot_nama'] ?? ($answersMap[1]['jawaban_teks'] ?? '-')));
$nipResponden  = trim((string) ($respon['nomor_identitas'] ?? '-'));
$statusPeg     = trim((string) ($respon['snapshot_status_kepegawaian'] ?? ($answersMap[5]['jawaban_teks'] ?? '-')));
$jabatan       = trim((string) ($respon['snapshot_jabatan'] ?? ($answersMap[7]['jawaban_teks'] ?? '-')));
$unitKerja     = trim((string) ($respon['snapshot_unit'] ?? ($answersMap[8]['jawaban_teks'] ?? '-')));
?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-3 py-3 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.8125rem;" role="alert">
        <i class="bi bi-exclamation-circle-fill text-danger flex-shrink-0 mt-1"></i>
        <div><?= esc(session()->getFlashdata('error')) ?></div>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info border-0 shadow-sm rounded-3 py-3 px-3 mb-3 d-flex align-items-start gap-2" style="font-size: 0.8125rem;" role="alert">
        <i class="bi bi-info-circle-fill text-primary flex-shrink-0 mt-1"></i>
        <div><?= esc(session()->getFlashdata('info')) ?></div>
    </div>
<?php endif; ?>

<div class="summary-hero mb-4">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
        <div>
            <span class="badge rounded-pill text-success border border-success-subtle fw-semibold mb-2" style="background-color: #dcfce7; padding: 4px 12px; font-size: 0.75rem;">
                <i class="bi bi-check-circle-fill me-1"></i> Survei Terkirim
            </span>
            <h1 class="h5 fw-bold text-dark mb-1">Terima kasih atas partisipasi Anda</h1>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">
                Jawaban Survei Pemetaan Potensi, Kompetensi, dan Kapasitas Pegawai PKTJ Anda telah tercatat.
            </p>
        </div>
        <div>
            <form action="<?= site_url('survei-pemetaan/keluar-sesi') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-semibold btn-tactile" style="height: 36px; padding: 4px 16px;">
                    Isi untuk Pegawai Lain
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-3 border p-3 summary-meta-grid">
        <div>
            <div class="text-secondary" style="font-size: 0.75rem;">Nama Lengkap</div>
            <div class="fw-semibold text-dark" style="font-size: 0.875rem;"><?= esc($namaResponden ?: '-') ?></div>
        </div>
        <div>
            <div class="text-secondary" style="font-size: 0.75rem;">NIP</div>
            <div class="fw-semibold text-dark tabular-nums" style="font-size: 0.875rem;"><?= esc($nipResponden) ?></div>
        </div>
        <div>
            <div class="text-secondary" style="font-size: 0.75rem;">Status Kepegawaian</div>
            <div class="fw-semibold text-dark" style="font-size: 0.875rem;"><?= esc($statusPeg ?: '-') ?></div>
        </div>
        <div>
            <div class="text-secondary" style="font-size: 0.75rem;">Unit Kerja & Jabatan</div>
            <div class="fw-semibold text-dark" style="font-size: 0.875rem;"><?= esc($unitKerja ?: '-') ?> &middot; <?= esc($jabatan ?: '-') ?></div>
        </div>
    </div>

    <div class="bg-white rounded-3 border mt-3" style="padding: 12px 16px; border-color: #bbf7d0 !important;">
        <div class="text-dark" style="font-size: 0.8125rem; line-height: 1.5;">
            <span class="fw-semibold text-success">Pernyataan Disetujui:</span>
            Seluruh data dan jawaban yang disampaikan adalah benar serta dipertanggungjawabkan sepenuhnya oleh responden.
        </div>
    </div>
</div>

<div class="d-flex flex-column gap-3">
    <?php foreach ($sections as $stepNo => $sec): ?>
        <section class="card-bento-survei">
            <div class="d-flex align-items-center justify-content-between gap-2 pb-2 mb-2 border-bottom">
                <div>
                    <div class="fw-bold text-primary" style="font-size: 0.75rem; letter-spacing: 0.04em;">BAGIAN <?= esc($sec['roman']) ?></div>
                    <h2 class="h6 fw-bold text-dark mb-0"><?= esc($sec['short_title']) ?></h2>
                </div>
                <span class="badge bg-light text-secondary border rounded-pill tabular-nums" style="padding: 4px 12px; font-size: 0.75rem;">
                    No. <?= (int) min($sec['questions']) ?>&ndash;<?= (int) max($sec['questions']) ?>
                </span>
            </div>

            <div>
                <?php foreach ($sec['questions'] as $qNo): ?>
                    <?php
                    $meta = $questions[$qNo] ?? null;
                    if (!$meta) {
                        continue;
                    }
                    $isApplicable = SurveiPemetaanConfig::isQuestionRequired($qNo, $answersMap);
                    if (!$isApplicable && empty($answersMap[$qNo])) {
                        continue;
                    }
                    $row = $answersMap[$qNo] ?? null;
                    $display = SurveiPemetaanConfig::formatAnswerDisplay($qNo, $row);
                    if ($qNo === 10 && !empty($row['jawaban_lainnya'])) {
                        $display = (string) $row['jawaban_lainnya'];
                    }
                    ?>
                    <div class="summary-q-item">
                        <div class="text-secondary mb-1" style="font-size: 0.8125rem;">
                            <span class="fw-semibold text-dark tabular-nums"><?= (int) $qNo ?>.</span>
                            <?= esc($meta['label']) ?>
                        </div>
                        <div class="fw-medium text-dark ps-3" style="font-size: 0.875rem; white-space: pre-line;"><?= esc($display) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>

