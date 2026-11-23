<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Tracer Study Alumni<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$periodes = $periodes ?? [];
$openList = array_values(array_filter($periodes, static fn (array $p): bool => !empty($p['is_open_now']) && !empty($p['is_eligible'])));
$doneList = array_values(array_filter($periodes, static fn (array $p): bool => !empty($p['my_respons'])));
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <!-- Survei Terbuka untuk Alumni -->
    <div class="ecc-ts-card mb-4">
        <h2 class="h6 fw-bold text-dark mb-3">Survei Tracer Study Sedang Dibuka</h2>
        <?php if (empty($openList)): ?>
            <p class="ecc-text-muted-aa small mb-0">Saat ini belum ada periode Tracer Study yang sedang dibuka untuk angkatan kelulusan Anda.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($openList as $pr): ?>
                    <?php $myR = $pr['my_respons'] ?? null; ?>
                    <div class="col-12 col-lg-6">
                        <div class="ts-q-item h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div class="fw-bold text-dark"><?= esc((string) $pr['nama_periode']) ?></div>
                                    <?= ketarunaan_tracer_status_periode_badge('dibuka') ?>
                                </div>
                                <?php if (!empty($pr['deskripsi'])): ?>
                                    <p class="ecc-text-muted-aa small mb-2"><?= esc((string) $pr['deskripsi']) ?></p>
                                <?php endif; ?>
                                <div class="ecc-text-muted-aa small ecc-num mb-3">
                                    <?= (int) $pr['jumlah_pertanyaan'] ?> pertanyaan
                                    <?php if (!empty($pr['tanggal_selesai'])): ?>
                                        &bull; Batas pengisian: <?= esc((string) $pr['tanggal_selesai']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                                <?php if ($myR): ?>
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                                        Sudah Mengisi
                                    </span>
                                    <a href="<?= base_url('ketarunaan/tracer-study/respons/' . (int) $myR['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center">
                                        Lihat Jawaban Saya
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('ketarunaan/tracer-study/isi/' . (int) $pr['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold btn-tactile ts-touch-ctrl d-inline-flex align-items-center">
                                        Isi Survei Sekarang
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Riwayat Pengisian Saya -->
    <?php if (!empty($doneList)): ?>
        <div class="ecc-ts-card">
            <h2 class="h6 fw-bold text-dark mb-3">Riwayat Pengisian Tracer Study Saya</h2>
            <div class="table-responsive">
                <table class="table ecc-table-ts mb-0">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Status Periode</th>
                            <th>Instansi Tercatat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doneList as $pr): ?>
                            <?php $myR = $pr['my_respons']; ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= esc((string) $pr['nama_periode']) ?></td>
                                <td><?= ketarunaan_tracer_status_periode_badge((string) $pr['status_periode']) ?></td>
                                <td class="small"><?= esc((string) ($myR['nama_instansi'] ?: '-')) ?></td>
                                <td class="text-end">
                                    <a href="<?= base_url('ketarunaan/tracer-study/respons/' . (int) $myR['id']) ?>" class="btn btn-outline-primary ecc-btn-action">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
