<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Lowongan Pekerjaan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$low                  = $lowongan ?? [];
$refs                 = $refs ?? [];
$perms                = $perms ?? [];
$waktuAcuan           = $waktuAcuan ?? date('Y-m-d H:i:s');
$canViewStaffBoard    = !empty($canViewStaffBoard);
$canViewInternalProof = !empty($canViewInternalProof);
$statusEfektif        = $low['status_efektif'] ?? ketarunaan_lowongan_status_efektif($low, $waktuAcuan);
$tautanAman           = ketarunaan_sanitize_external_url($low['tautan_pendaftaran'] ?? null, false);
$websiteMitra         = ketarunaan_sanitize_external_url($low['website_perusahaan'] ?? null, false);
$emailLamaran         = trim((string) ($low['email_lamaran'] ?? ''));
$mailtoAman           = $emailLamaran !== '' ? ketarunaan_sanitize_external_url('mailto:' . $emailLamaran, true) : null;
$dokKatCatalog        = ketarunaan_lowongan_dokumen_kategori_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <!-- Header Detail Lowongan -->
    <div class="ecc-low-card mb-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <?php if ($canViewStaffBoard): ?>
                        <?= ketarunaan_validasi_badge((string) ($low['status_validasi'] ?? 'draf')) ?>
                    <?php endif; ?>
                    <?= ketarunaan_lowongan_publikasi_badge($low, $waktuAcuan) ?>
                    <?php if ($canViewStaffBoard): ?>
                        <?= ketarunaan_perusahaan_verifikasi_badge((int) ($low['perusahaan_verified'] ?? 0)) ?>
                    <?php endif; ?>
                </div>
                <h2 class="h4 fw-bold text-dark mb-1"><?= esc($low['judul_lowongan'] ?? '') ?></h2>
                <div class="ecc-text-muted-aa small">
                    <?= esc($low['nama_perusahaan'] ?? '-') ?> &bull; <?= esc($low['lokasi'] ?? '-') ?> &bull; <?= esc($low['jenis_pekerjaan'] ?? '-') ?>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?= base_url('ketarunaan/lowongan') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                    Kembali
                </a>
                <?php if (!empty($perms['can_manage'])): ?>
                    <a href="<?= base_url('ketarunaan/lowongan/edit/' . (int) $low['id']) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Ubah Lowongan
                    </a>
                    <a href="<?= base_url('ketarunaan/lowongan/dokumen/' . (int) $low['id']) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Dokumen
                    </a>
                <?php endif; ?>
                <?php if (!empty($perms['can_validate']) || !empty($perms['can_manage'])): ?>
                    <a href="<?= base_url('ketarunaan/lowongan/verifikasi/' . (int) $low['id']) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Verifikasi &amp; Terbit
                    </a>
                <?php endif; ?>
                <?php if (!empty($perms['can_publish']) && $statusEfektif === 'terbuka'): ?>
                    <button type="button" class="btn btn-outline-danger ecc-control-sm px-3 fw-semibold btn-tactile low-touch-ctrl text-nowrap" data-bs-toggle="modal" data-bs-target="#modalTutupLowongan">
                        Tutup Lowongan
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($statusEfektif === 'ditutup' && !empty($low['alasan_penutupan'])): ?>
            <div class="alert alert-danger border-0 rounded-3 mt-3 mb-0 py-2 px-3 small">
                Lowongan ditutup: <?= esc($low['alasan_penutupan']) ?>
            </div>
        <?php elseif ($statusEfektif === 'berakhir'): ?>
            <div class="alert alert-warning border-0 rounded-3 mt-3 mb-0 py-2 px-3 small">
                Masa pendaftaran berakhir pada <?= esc(ketarunaan_format_tanggal($low['batas_pendaftaran'] ?? null)) ?>.
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="ecc-low-card mb-3">
                <h3 class="h6 fw-bold text-dark mb-2">Ringkasan Lowongan</h3>
                <p class="ecc-text-muted-aa mb-4 small" style="white-space: pre-line;"><?= esc($low['deskripsi'] ?? '-') ?></p>

                <h3 class="h6 fw-bold text-dark mb-2">Persyaratan Kompetensi</h3>
                <p class="ecc-text-muted-aa mb-3 small" style="white-space: pre-line;"><?= esc($low['persyaratan'] ?? '-') ?></p>

                <div class="row g-3 pt-2 border-top">
                    <div class="col-12 col-md-4">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Jenjang Minimum</div>
                        <div class="fw-semibold text-dark small"><?= esc($low['jenjang_pendidikan'] ?? '-') ?></div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pengalaman Minimum</div>
                        <div class="fw-semibold text-dark small"><?= esc($low['pengalaman_minimum'] ?? '-') ?></div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Sertifikasi Khusus</div>
                        <div class="fw-semibold text-dark small"><?= esc($low['sertifikasi_opsional'] ?? 'Tidak wajib') ?></div>
                    </div>
                </div>
            </div>

            <div class="ecc-low-card mb-3">
                <h3 class="h6 fw-bold text-dark mb-3">Sasaran Program Studi</h3>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (($low['sasaran_prodi'] ?? []) as $pr): ?>
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;">
                            <?= esc($pr['kode_prodi']) ?> — <?= esc($pr['nama_prodi']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($low['tahun_lulus_sasaran']) || !empty($low['jumlah_kebutuhan'])): ?>
                    <div class="ecc-text-muted-aa mt-3 ecc-num small">
                        <?php if (!empty($low['tahun_lulus_sasaran'])): ?>
                            Sasaran tahun lulus: <strong class="text-dark"><?= esc($low['tahun_lulus_sasaran']) ?></strong>
                        <?php endif; ?>
                        <?php if (!empty($low['jumlah_kebutuhan'])): ?>
                            <?= !empty($low['tahun_lulus_sasaran']) ? ' &bull; ' : '' ?>Kebutuhan: <strong class="text-dark"><?= (int) $low['jumlah_kebutuhan'] ?> posisi</strong>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dokumen Terlampir (Tersaring otomatis di server untuk Non-Staf) -->
            <div class="ecc-low-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-bold text-dark mb-0">Dokumen Terkait</h3>
                    <?php if ($canViewInternalProof): ?>
                        <a href="<?= base_url('ketarunaan/lowongan/dokumen/' . (int) $low['id']) ?>" class="text-decoration-none fw-semibold small">Kelola Dokumen</a>
                    <?php endif; ?>
                </div>
                <?php if (empty($low['dokumen_list'])): ?>
                    <div class="ecc-text-muted-aa small">Belum ada poster atau dokumen publik terlampir.</div>
                <?php else: ?>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table ecc-table-low mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="0"><span>Dokumen</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                    <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="1"><span>Kategori</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                    <?php if ($canViewInternalProof): ?>
                                        <th>Akses</th>
                                    <?php endif; ?>
                                    <th class="text-end">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low['dokumen_list'] as $doc): ?>
                                    <?php $katMeta = $dokKatCatalog[$doc['kategori_dokumen']] ?? ['label' => $doc['kategori_dokumen'], 'badge_class' => 'bg-light text-dark border']; ?>
                                    <tr>
                                        <td data-sort-val="<?= esc(strtolower((string) $doc['judul_dokumen'])) ?>">
                                            <div class="fw-medium text-dark"><?= esc($doc['judul_dokumen']) ?></div>
                                            <?php if (!empty($doc['sumber_dokumen'])): ?>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($doc['sumber_dokumen']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc((string) $katMeta['label']) ?>">
                                            <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($katMeta['badge_class']) ?>" style="font-size: 0.75rem;">
                                                <?= esc($katMeta['label']) ?>
                                            </span>
                                        </td>
                                        <?php if ($canViewInternalProof): ?>
                                            <td>
                                                <?= (int) $doc['is_internal'] === 1
                                                    ? '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.75rem;">Internal Staf</span>'
                                                    : '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size:0.75rem;">Publik</span>' ?>
                                            </td>
                                        <?php endif; ?>
                                        <td class="text-end">
                                            <a href="<?= base_url('ketarunaan/lowongan/unduh-dokumen/' . (int) $doc['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action" target="_blank" rel="noopener noreferrer" aria-label="Buka dokumen <?= esc($doc['judul_dokumen']) ?>">
                                                Buka
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Mobile Dokumen Terkait -->
                    <div class="d-md-none d-flex flex-column gap-2">
                        <?php foreach ($low['dokumen_list'] as $doc): ?>
                            <?php $katMeta = $dokKatCatalog[$doc['kategori_dokumen']] ?? ['label' => $doc['kategori_dokumen'], 'badge_class' => 'bg-light text-dark border']; ?>
                            <div class="border rounded-3 p-3 bg-white">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <div class="fw-semibold text-dark"><?= esc($doc['judul_dokumen']) ?></div>
                                        <?php if (!empty($doc['sumber_dokumen'])): ?>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($doc['sumber_dokumen']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('ketarunaan/lowongan/unduh-dokumen/' . (int) $doc['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action low-touch-ctrl" target="_blank" rel="noopener noreferrer">
                                        Buka
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($katMeta['badge_class']) ?>" style="font-size: 0.75rem;">
                                        <?= esc($katMeta['label']) ?>
                                    </span>
                                    <?php if ($canViewInternalProof): ?>
                                        <?= (int) $doc['is_internal'] === 1
                                            ? '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.75rem;">Internal Staf</span>'
                                            : '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size:0.75rem;">Publik</span>' ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel Kanan: Informasi Rekrutmen & Kanal Lamaran -->
        <div class="col-12 col-lg-4">
            <div class="ecc-low-card mb-3">
                <h3 class="h6 fw-bold text-dark mb-3">Informasi Rekrutmen</h3>
                <div class="mb-3">
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Batas Lamaran</div>
                    <div class="fw-bold text-dark ecc-num fs-6">
                        <?= esc(ketarunaan_format_tanggal($low['batas_pendaftaran'] ?? null)) ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Kanal Lamaran</div>
                    <div class="fw-semibold text-dark small">
                        <?= esc(($refs['metode_catalog'][$low['metode_lamaran'] ?? ''] ?? 'Kanal Resmi Perusahaan')) ?>
                    </div>
                </div>
                <?php if ($websiteMitra !== null): ?>
                    <div class="mb-3">
                        <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Situs Perusahaan</div>
                        <a href="<?= esc($websiteMitra) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none small text-break">
                            <?= esc($websiteMitra) ?>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="p-3 rounded-3 bg-light border mb-3 small ecc-text-muted-aa">
                    Lamaran dikirim langsung melalui kanal perusahaan. Klik tautan tidak dicatat sebagai jumlah pelamar atau lulusan diterima kerja.
                </div>

                <?php if ($statusEfektif === 'terbuka'): ?>
                    <div class="d-grid gap-2">
                        <?php if ($tautanAman !== null): ?>
                            <a href="<?= esc($tautanAman) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary ecc-control-sm fw-semibold d-flex align-items-center justify-content-center gap-2 btn-tactile low-touch-ctrl">
                                Buka Tautan Lamaran <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($mailtoAman !== null && in_array($low['metode_lamaran'] ?? '', ['email', 'keduanya'], true)): ?>
                            <a href="<?= esc($mailtoAman) ?>" class="btn btn-outline-primary ecc-control-sm fw-semibold d-flex align-items-center justify-content-center btn-tactile low-touch-ctrl">
                                Kirim ke Alamat Email Rekrutmen
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="ecc-text-muted-aa fw-semibold small">
                        Tautan lamaran dinonaktifkan karena lowongan belum terbit atau sudah berakhir.
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($canViewInternalProof): ?>
                <div class="ecc-low-card">
                    <h3 class="h6 fw-bold text-dark mb-2">Asal Informasi (Khusus Staf)</h3>
                    <div class="mb-2 small">
                        <span class="ecc-text-muted-aa">Sumber:</span> <strong><?= esc($low['sumber_informasi'] ?: '-') ?></strong>
                    </div>
                    <div class="mb-2 ecc-num small">
                        <span class="ecc-text-muted-aa">Diterima:</span> <strong><?= esc(ketarunaan_format_tanggal($low['tanggal_diterima'] ?? null)) ?></strong>
                    </div>
                    <div class="mb-2 small">
                        <span class="ecc-text-muted-aa">Pengelola:</span> <strong><?= esc($low['pic_pengelola'] ?: '-') ?></strong>
                    </div>
                    <?php if (!empty($low['catatan_sumber'])): ?>
                        <div class="ecc-text-muted-aa border-top pt-2 mt-2 small">
                            <?= esc($low['catatan_sumber']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-3 pt-2 border-top">
                        <a href="<?= base_url('ketarunaan/lowongan/riwayat/' . (int) $low['id']) ?>" class="text-decoration-none fw-semibold small">
                            Lihat Riwayat Proses
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($perms['can_publish']) && $statusEfektif === 'terbuka'): ?>
<div class="modal fade" id="modalTutupLowongan" tabindex="-1" aria-labelledby="modalTutupLowonganLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="<?= base_url('ketarunaan/lowongan/tutup/' . (int) $low['id']) ?>" class="low-form-mobile">
                <?= csrf_field() ?>
                <div class="modal-header border-bottom">
                    <h2 class="h6 modal-title fw-bold mb-0" id="modalTutupLowonganLabel">Tutup Lowongan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="ecc-text-muted-aa mb-3 small">
                        Lowongan ditutup tidak lagi tampil sebagai lowongan terbuka, namun tetap tersimpan di riwayat.
                    </p>
                    <div class="mb-3">
                        <label for="alasan_pilihan" class="form-label fw-semibold ecc-text-muted-aa small">Alasan Penutupan <span class="text-danger">*</span></label>
                        <select name="alasan_pilihan" id="alasan_pilihan" class="form-select ecc-control-sm" required>
                            <?php foreach (($refs['alasan_penutupan'] ?? []) as $al): ?>
                                <option value="<?= esc($al) ?>"><?= esc($al) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="alasan_custom" class="form-label fw-semibold ecc-text-muted-aa small">
                            Keterangan Tambahan <span class="text-danger d-none" id="req_alasan_custom">*</span>
                        </label>
                        <textarea name="alasan_custom" id="alasan_custom" rows="2" class="form-control rounded-3" placeholder="Diperlukan bila memilih Lainnya"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-3 low-touch-ctrl" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-3 low-touch-ctrl">Ya, Tutup Lowongan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<script>
(function () {
    var sel = document.getElementById('alasan_pilihan');
    var txt = document.getElementById('alasan_custom');
    var req = document.getElementById('req_alasan_custom');
    if (sel && txt) {
        var syncAlasan = function () {
            var isLainnya = sel.value === 'Lainnya';
            txt.required = isLainnya;
            if (req) {
                req.classList.toggle('d-none', !isLainnya);
            }
        };
        sel.addEventListener('change', syncAlasan);
        syncAlasan();
    }
})();
</script>
<?= $this->endSection() ?>
