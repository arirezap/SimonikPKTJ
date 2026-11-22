<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Dokumen dan Sumber Lowongan<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$vacancies     = $vacancies ?? [];
$selected      = $selected ?? null;
$documents     = $documents ?? [];
$refs          = $refs ?? [];
$perms         = $perms ?? [];
$canManage     = !empty($perms['can_manage']);
$dokKatCatalog = ketarunaan_lowongan_dokumen_kategori_catalog();
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <div class="alert alert-info border-0 shadow-sm rounded-3 px-3 py-2 mb-3 small">
        Bukti konfirmasi, surat perusahaan, dan tautan sumber internal hanya dapat diakses oleh staf pengelola dan pejabat terkait. Alumni serta taruna hanya melihat poster publik jika ditayangkan.
    </div>

    <!-- Pemilih Lowongan -->
    <div class="ecc-low-card mb-3 py-3">
        <form method="get" action="<?= base_url('ketarunaan/lowongan/dokumen') ?>" class="row g-2 align-items-end low-filter-form">
            <div class="col-12 col-md-8">
                <label for="lowongan_id_pilih" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pilih Lowongan Pekerjaan</label>
                <select name="lowongan_id" id="lowongan_id_pilih" class="form-select ecc-control-sm js-low-select2" onchange="this.form.submit()">
                    <?php if (empty($vacancies)): ?>
                        <option value="">Belum ada lowongan tercatat</option>
                    <?php else: ?>
                        <?php foreach ($vacancies as $vac): ?>
                            <option value="<?= (int) $vac['id'] ?>" <?= ($selected && (int) $selected['id'] === (int) $vac['id']) ? 'selected' : '' ?>>
                                <?= esc($vac['judul_lowongan']) ?> — <?= esc($vac['nama_perusahaan'] ?? '-') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-md-end">
                <?php if ($selected): ?>
                    <a href="<?= base_url('ketarunaan/lowongan/verifikasi/' . (int) $selected['id']) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Lanjut ke Verifikasi
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-12 <?= ($canManage && $selected) ? 'col-xl-7' : '' ?>">
            <!-- Tabel Desktop Dokumen & Sumber -->
            <div class="ecc-low-card p-0 overflow-hidden d-none d-md-block">
                <div class="p-3 border-bottom">
                    <h2 class="h6 fw-bold text-dark mb-0">Daftar Dokumen &amp; Sumber Tercatat</h2>
                </div>
                <div class="ecc-table-low-wrap table-responsive">
                    <table class="table ecc-table-low mb-0" data-sortable-table="true">
                        <thead>
                            <tr>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="0"><span>Dokumen / Sumber</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="1"><span>Kategori</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="2"><span>Akses</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="3"><span>Diterima</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="5" class="text-center ecc-text-muted-aa py-5">Belum ada dokumen atau tautan sumber untuk lowongan ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <?php
                                    $katMeta = $dokKatCatalog[$doc['kategori_dokumen']] ?? ['label' => $doc['kategori_dokumen'], 'badge_class' => 'bg-light text-dark border'];
                                    $urlSrc  = ketarunaan_sanitize_external_url($doc['url_sumber'] ?? null, false);
                                    $berkasVal = trim((string) ($doc['nama_berkas'] ?? ''));
                                    $labelBukti = $urlSrc !== null
                                        ? ketarunaan_format_bukti_label($urlSrc)
                                        : ($berkasVal !== '' ? ketarunaan_format_bukti_label($berkasVal) : '');
                                    ?>
                                    <tr>
                                        <td data-sort-val="<?= esc(strtolower((string) $doc['judul_dokumen'])) ?>">
                                            <div class="fw-semibold text-dark"><?= esc($doc['judul_dokumen']) ?></div>
                                            <?php if (!empty($doc['sumber_dokumen'])): ?>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($doc['sumber_dokumen']) ?></div>
                                            <?php endif; ?>
                                            <?php if ($labelBukti !== ''): ?>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($labelBukti) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($doc['catatan'])): ?>
                                                <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;"><?= esc($doc['catatan']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc((string) $katMeta['label']) ?>">
                                            <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($katMeta['badge_class']) ?>" style="font-size: 0.75rem;">
                                                <?= esc($katMeta['label']) ?>
                                            </span>
                                        </td>
                                        <td data-sort-val="<?= (int) $doc['is_internal'] ?>">
                                            <?= (int) $doc['is_internal'] === 1
                                                ? '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.75rem;">Internal Staf</span>'
                                                : '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size:0.75rem;">Publik</span>' ?>
                                        </td>
                                        <td class="ecc-num" data-sort-val="<?= esc((string) ($doc['tanggal_diterima'] ?? '')) ?>"><?= esc(ketarunaan_format_tanggal($doc['tanggal_diterima'] ?? null)) ?></td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($berkasVal !== '' || $urlSrc !== null): ?>
                                                <a href="<?= base_url('ketarunaan/lowongan/unduh-dokumen/' . (int) $doc['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action" target="_blank" rel="noopener noreferrer" aria-label="Buka dokumen <?= esc($doc['judul_dokumen']) ?>">
                                                    Buka
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($canManage): ?>
                                                <form method="post" action="<?= base_url('ketarunaan/lowongan/dokumen/hapus/' . (int) $doc['id']) ?>" class="d-inline"
                                                      onsubmit="return window.eccLowConfirmSubmit(event, 'Hapus Dokumen?', 'Dokumen sumber ini akan dihapus.', 'Ya, Hapus', true);">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm ecc-btn-action ms-1" aria-label="Hapus dokumen <?= esc($doc['judul_dokumen']) ?>">Hapus</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kartu Mobile Dokumen & Sumber -->
            <div class="d-md-none d-flex flex-column gap-3">
                <?php if (empty($documents)): ?>
                    <div class="ecc-low-card text-center ecc-text-muted-aa py-4 small">Belum ada dokumen atau tautan sumber untuk lowongan ini.</div>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <?php
                        $katMeta   = $dokKatCatalog[$doc['kategori_dokumen']] ?? ['label' => $doc['kategori_dokumen'], 'badge_class' => 'bg-light text-dark border'];
                        $urlSrc    = ketarunaan_sanitize_external_url($doc['url_sumber'] ?? null, false);
                        $berkasVal = trim((string) ($doc['nama_berkas'] ?? ''));
                        ?>
                        <div class="ecc-low-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($doc['judul_dokumen']) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                        <?= esc($doc['sumber_dokumen'] ?: '-') ?> &bull; <?= esc(ketarunaan_format_tanggal($doc['tanggal_diterima'] ?? null)) ?>
                                    </div>
                                </div>
                                <?= (int) $doc['is_internal'] === 1
                                    ? '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size:0.75rem;">Internal Staf</span>'
                                    : '<span class="badge rounded-pill px-3 py-1 fw-semibold bg-success-subtle text-success-emphasis border border-success-subtle" style="font-size:0.75rem;">Publik</span>' ?>
                            </div>
                            <div class="mb-2">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($katMeta['badge_class']) ?>" style="font-size: 0.75rem;">
                                    <?= esc($katMeta['label']) ?>
                                </span>
                            </div>
                            <?php if (!empty($doc['catatan'])): ?>
                                <div class="ecc-text-muted-aa small mb-2"><?= esc($doc['catatan']) ?></div>
                            <?php endif; ?>
                            <div class="d-flex gap-2 pt-2 border-top">
                                <?php if ($berkasVal !== '' || $urlSrc !== null): ?>
                                    <a href="<?= base_url('ketarunaan/lowongan/unduh-dokumen/' . (int) $doc['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action low-touch-ctrl flex-fill" target="_blank" rel="noopener noreferrer">
                                        Buka Tautan
                                    </a>
                                <?php endif; ?>
                                <?php if ($canManage): ?>
                                    <form method="post" action="<?= base_url('ketarunaan/lowongan/dokumen/hapus/' . (int) $doc['id']) ?>"
                                          onsubmit="return window.eccLowConfirmSubmit(event, 'Hapus Dokumen?', 'Dokumen sumber ini akan dihapus.', 'Ya, Hapus', true);">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm ecc-btn-action low-touch-ctrl">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Opsi A: Tautan Google Drive / Cloud / Portal Resmi -->
        <?php if ($canManage && $selected): ?>
            <div class="col-12 col-xl-5">
                <div class="ecc-low-card">
                    <h2 class="h6 fw-bold text-dark mb-3">Tambah Tautan Dokumen atau Sumber</h2>
                    <form method="post" action="<?= base_url('ketarunaan/lowongan/dokumen/' . (int) $selected['id'] . '/simpan') ?>" class="low-form-mobile">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="kategori_dokumen" class="form-label fw-semibold ecc-text-muted-aa small">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_dokumen" id="kategori_dokumen" class="form-select ecc-control-sm" required>
                                <?php foreach ($dokKatCatalog as $kk => $km): ?>
                                    <option value="<?= esc($kk) ?>" <?= old('kategori_dokumen') === $kk ? 'selected' : '' ?>><?= esc($km['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="judul_dokumen" class="form-label fw-semibold ecc-text-muted-aa small">Judul Dokumen / Sumber <span class="text-danger">*</span></label>
                            <input type="text" name="judul_dokumen" id="judul_dokumen" class="form-control ecc-control-sm" required
                                   value="<?= esc(old('judul_dokumen', '')) ?>" placeholder="Contoh: Surat Permohonan Rekrutmen PT KAI">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-7">
                                <label for="sumber_dokumen" class="form-label fw-semibold ecc-text-muted-aa small">Asal Sumber</label>
                                <input type="text" name="sumber_dokumen" id="sumber_dokumen" class="form-control ecc-control-sm"
                                       value="<?= esc(old('sumber_dokumen', '')) ?>" placeholder="HRD / Portal Resmi">
                            </div>
                            <div class="col-12 col-md-5">
                                <label for="tanggal_diterima_dok" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Diterima</label>
                                <input type="text" name="tanggal_diterima" id="tanggal_diterima_dok" class="form-control ecc-control-sm ecc-num js-low-date"
                                       value="<?= esc(old('tanggal_diterima', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="url_sumber" class="form-label fw-semibold ecc-text-muted-aa small">Tautan Google Drive / Portal Resmi <span class="text-danger">*</span></label>
                            <input type="url" name="url_sumber" id="url_sumber" class="form-control ecc-control-sm" required
                                   value="<?= esc(old('url_sumber', '')) ?>" placeholder="https://drive.google.com/file/d/... atau https://perusahaan.co.id/...">
                            <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">
                                Pastikan akses dokumen Google Drive diatur ke <em>Siapa saja yang memiliki link dapat melihat</em>.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="is_internal" class="form-label fw-semibold ecc-text-muted-aa small">Pembatasan Akses</label>
                            <select name="is_internal" id="is_internal" class="form-select ecc-control-sm">
                                <option value="1" <?= (string) old('is_internal', '1') === '1' ? 'selected' : '' ?>>Internal Staf (Tidak tampil ke alumni/taruna)</option>
                                <option value="0" <?= (string) old('is_internal', '1') === '0' ? 'selected' : '' ?>>Publik (Khusus Poster jika lowongan tayang)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan_dok" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Pemeriksaan</label>
                            <textarea name="catatan" id="catatan_dok" rows="2" class="form-control rounded-3"><?= esc(old('catatan', '')) ?></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary ecc-control-sm fw-semibold btn-tactile low-touch-ctrl">Simpan Dokumen &amp; Sumber</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<?= $this->endSection() ?>
