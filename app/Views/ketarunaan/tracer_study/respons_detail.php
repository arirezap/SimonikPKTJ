<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Rincian Jawaban Tracer Study<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/tracer_study/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail       = $detail ?? [];
$respons      = $detail['respons'] ?? [];
$jawaban      = $detail['jawaban'] ?? [];
$isSelfAlumni = !empty($isSelfAlumni);
$perms        = $perms ?? [];
$canManage    = !empty($perms['can_manage']);
$transisiCat  = ketarunaan_tracer_status_transisi_catalog();
$bidangCat    = ketarunaan_tracer_kesesuaian_bidang_catalog();
$jenjangCat   = ketarunaan_tracer_kesesuaian_jenjang_catalog();
$tMeta        = $transisiCat[$respons['status_transisi'] ?? ''] ?? ['label' => $respons['status_transisi'] ?? '-', 'badge_class' => 'bg-light text-dark border'];
$pid          = (int) ($respons['periode_id'] ?? 0);
$isMitra      = (($respons['target_responden'] ?? 'alumni') === 'mitra');
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/tracer_study/_nav_tabs') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="<?= base_url($isSelfAlumni ? 'ketarunaan/tracer-study' : ('ketarunaan/tracer-study/responden?periode_id=' . $pid . (($respons['status_validasi'] ?? '') === 'diajukan' ? '&sub=verifikasi' : ''))) ?>" class="btn btn-light border btn-sm rounded-pill px-3 ts-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
        </a>
        <div class="d-flex flex-wrap gap-2">
            <?php if (!$isSelfAlumni && $canManage && ($respons['status_validasi'] ?? '') === 'diajukan'): ?>
                <form method="post" action="<?= base_url('ketarunaan/tracer-study/respons/' . (int) $respons['id'] . '/setujui') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="create_account" value="1">
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl">
                        Setujui, Daftarkan &amp; Buat Akun Alumni
                    </button>
                </form>
            <?php endif; ?>
            <?php if (!$isSelfAlumni && $canManage && ($respons['status_periode'] ?? '') !== 'draf' && (int) ($respons['is_alumni_terdaftar'] ?? 1) === 1): ?>
                <a href="<?= base_url('ketarunaan/tracer-study/isi/' . $pid . '?nomor_taruna=' . urlencode((string) $respons['nomor_taruna'])) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold ts-touch-ctrl d-inline-flex align-items-center">
                    Koreksi (PIC)
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (($respons['status_validasi'] ?? '') === 'diajukan'): ?>
        <div class="alert alert-warning border-0 rounded-3 py-2 px-3 small mb-3">
            <strong>Menunggu Verifikasi Alumni Baru:</strong> Notar ini belum terdaftar di database alumni. Periksa identitas lalu klik <strong>Setujui</strong> agar masuk ke Data Alumni dan statistik resmi.
        </div>
    <?php endif; ?>

    <!-- Identitas & Indikator Utama -->
    <div class="ecc-ts-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3 pb-3 border-bottom">
            <div>
                <h2 class="h5 fw-bold text-dark mb-1"><?= esc((string) ($respons['nama_lengkap'] ?: $respons['nomor_taruna'])) ?></h2>
                <div class="ecc-text-muted-aa small ecc-num">
                    <?php if ($isMitra): ?>
                        Kode Respons: <strong><?= esc((string) $respons['nomor_taruna']) ?></strong>
                        &bull; Instansi: <strong><?= esc((string) ($respons['nama_instansi'] ?: '-')) ?></strong>
                    <?php else: ?>
                        Nomor Taruna: <strong><?= esc((string) $respons['nomor_taruna']) ?></strong>
                        &bull; Prodi: <strong><?= esc((string) ($respons['kode_prodi'] ?: '-')) ?></strong>
                        &bull; Lulus: <strong><?= esc((string) ($respons['tahun_lulus'] ?: '-')) ?></strong>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if ($isMitra): ?>
                    <?= ketarunaan_tracer_target_badge('mitra') ?>
                <?php else: ?>
                    <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($tMeta['badge_class']) ?>" style="font-size: 0.75rem;">
                        <?= esc($tMeta['label']) ?>
                    </span>
                <?php endif; ?>
                <?= ketarunaan_tracer_status_periode_badge((string) ($respons['status_periode'] ?? 'dibuka')) ?>
            </div>
        </div>

        <?php if ($isMitra): ?>
            <div class="row g-3 small">
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Periode Survei</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['nama_periode'] ?? '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Alumni PKTJ yang Dinilai</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['nama_alumni_dinilai'] ?: '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Program Studi Alumni</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['kode_prodi'] ? ($respons['kode_prodi'] . ' — ' . $respons['nama_prodi']) : '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Email Atasan Langsung</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['email_alumni'] ?: '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">No. Handphone Atasan</div>
                    <div class="fw-semibold text-dark ecc-num"><?= esc((string) ($respons['telepon_alumni'] ?: '-')) ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3 small">
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Periode Survei</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['nama_periode'] ?? '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Instansi / Usaha</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['nama_instansi'] ?: '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Jabatan / Posisi</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['jabatan_posisi'] ?: '-')) ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Sektor &amp; Kota</div>
                    <div class="fw-semibold text-dark"><?= esc((string) ($respons['sektor_industri'] ?: '-')) ?><?= !empty($respons['kota_kerja']) ? (' — ' . esc((string) $respons['kota_kerja'])) : '' ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Masa Tunggu Kerja</div>
                    <div class="fw-semibold text-dark ecc-num"><?= $respons['masa_tunggu_bulan'] !== null ? ((int) $respons['masa_tunggu_bulan'] . ' bulan') : '-' ?></div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="ecc-text-muted-aa">Kesesuaian Bidang / Jenjang</div>
                    <div class="fw-semibold text-dark">
                        <?= esc($bidangCat[$respons['kesesuaian_bidang'] ?? ''] ?? '-') ?> /
                        <?= esc($jenjangCat[$respons['kesesuaian_jenjang'] ?? ''] ?? '-') ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Jawaban Pertanyaan Bundel -->
    <div class="ecc-ts-card">
        <h3 class="h6 fw-bold text-dark mb-3">Jawaban Kuesioner Survei</h3>
        <?php if (empty($jawaban)): ?>
            <p class="ecc-text-muted-aa small mb-0">Tidak ada jawaban pertanyaan tambahan tercatat.</p>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($jawaban as $idx => $qa): ?>
                    <div class="ts-q-item">
                        <div class="ecc-text-muted-aa mb-1" style="font-size: 0.72rem;"><?= esc((string) $qa['bagian']) ?></div>
                        <div class="fw-semibold text-dark small mb-2"><?= ($idx + 1) . '. ' . esc((string) $qa['teks_pertanyaan']) ?></div>
                        <div class="p-2 px-3 rounded-3 bg-light border small text-dark">
                            <?php if ($qa['tipe_jawaban'] === 'skala_likert' && $qa['nilai_angka'] !== null): ?>
                                <strong class="text-primary ecc-num">Skor <?= (int) round((float) $qa['nilai_angka']) ?> / <?= (int) ($qa['max_scale'] ?? 5) ?></strong>
                            <?php else: ?>
                                <?= nl2br(esc((string) ($qa['nilai_teks'] ?: '-'))) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
