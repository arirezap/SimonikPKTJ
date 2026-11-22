<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Perusahaan &amp; Kontak Rekrutmen<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$companies     = $companies ?? [];
$filters       = $filters ?? [];
$selected      = $selected ?? null;
$perms         = $perms ?? [];
$canManage     = !empty($perms['can_manage']);
$canValidate   = !empty($perms['can_validate']);
$canShareMitra = !empty($perms['can_share_mitra']);

$qMitra        = trim((string) ($filters['q'] ?? ''));
$verifMitra    = (string) ($filters['is_verified'] ?? 'all');
$hasFilter     = ($qMitra !== '') || ($verifMitra !== '' && $verifMitra !== 'all');
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <div class="alert alert-info border-0 shadow-sm rounded-3 px-3 py-2 mb-3 small">
        Verifikasi kontak perusahaan hanya mengesahkan data mitra, bukan mengesahkan otomatis seluruh lowongan dari perusahaan tersebut. Setiap lowongan tetap diperiksa terpisah.<?php if ($canShareMitra): ?> Bidang Kerjasama serta Karir &amp; Alumni berwenang mengelola data mitra dan membagikan ke mitra pengguna lulusan.<?php endif; ?>
    </div>

    <div class="row g-3">
        <!-- Daftar Perusahaan Mitra -->
        <div class="col-12 <?= $canManage ? 'col-xl-7' : '' ?>">
            <div class="ecc-low-card mb-3 py-3">
                <form method="get" action="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="row g-2 align-items-end low-filter-form">
                    <div class="col-12 col-md-5">
                        <label for="q_mitra" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Cari Perusahaan</label>
                        <input type="text" name="q" id="q_mitra" class="form-control ecc-control-sm"
                               value="<?= esc($qMitra) ?>" placeholder="Nama, sektor, kota, atau kontak">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="verif_mitra" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Status Kontak</label>
                        <select name="is_verified" id="verif_mitra" class="form-select ecc-control-sm">
                            <option value="all">Semua</option>
                            <option value="1" <?= $verifMitra === '1' ? 'selected' : '' ?>>Kontak diperiksa</option>
                            <option value="0" <?= $verifMitra === '0' ? 'selected' : '' ?>>Belum diperiksa</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 d-flex flex-wrap justify-content-md-end gap-2">
                        <button type="submit" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold btn-tactile low-touch-ctrl text-nowrap">Terapkan</button>
                        <?php if ($hasFilter): ?>
                            <a href="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="btn btn-light border ecc-control-sm px-3 ecc-text-muted-aa d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Tabel Desktop Perusahaan Mitra -->
            <div class="ecc-low-card p-0 overflow-hidden d-none d-md-block">
                <div class="ecc-table-low-wrap table-responsive">
                    <table class="table ecc-table-low mb-0" data-sortable-table="true">
                        <thead>
                            <tr>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="0"><span>Perusahaan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="1"><span>Kontak Rekrutmen</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="2"><span>Status Kontak</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th class="text-center" aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="3"><span>Lowongan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <?php if ($canManage): ?>
                                    <th class="text-end">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($companies)): ?>
                                <tr>
                                    <td colspan="<?= $canManage ? 5 : 4 ?>" class="text-center ecc-text-muted-aa py-5">Belum ada data perusahaan mitra.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($companies as $comp): ?>
                                    <?php $webAman = ketarunaan_sanitize_external_url($comp['website'] ?? null, false); ?>
                                    <tr class="<?= ($selected && (int) $selected['id'] === (int) $comp['id']) ? 'table-primary' : '' ?>">
                                        <td data-sort-val="<?= esc(strtolower((string) $comp['nama_perusahaan'])) ?>">
                                            <div class="fw-semibold text-dark"><?= esc($comp['nama_perusahaan']) ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                                <?= esc($comp['sektor'] ?? '-') ?><?= !empty($comp['kota']) ? ' &bull; ' . esc($comp['kota']) : '' ?>
                                            </div>
                                            <?php if ($webAman !== null): ?>
                                                <a href="<?= esc($webAman) ?>" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem;"><?= esc($webAman) ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($comp['kontak_person'] ?? ''))) ?>">
                                            <div class="fw-medium text-dark"><?= esc($comp['kontak_person'] ?? '-') ?></div>
                                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc($comp['email_rekrutmen'] ?? '-') ?></div>
                                        </td>
                                        <td data-sort-val="<?= (int) ($comp['is_verified'] ?? 0) ?>"><?= ketarunaan_perusahaan_verifikasi_badge((int) ($comp['is_verified'] ?? 0)) ?></td>
                                        <td class="text-center ecc-num" data-sort-val="<?= (int) $comp['jumlah_terbuka'] ?>">
                                            <span class="fw-semibold text-dark"><?= (int) $comp['jumlah_terbuka'] ?></span>
                                            <span class="ecc-text-muted-aa" style="font-size: 0.75rem;">/ <?= (int) $comp['jumlah_lowongan'] ?></span>
                                        </td>
                                        <?php if ($canManage): ?>
                                            <td class="text-end">
                                                <a href="<?= base_url('ketarunaan/lowongan/perusahaan?edit=' . (int) $comp['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action" aria-label="Ubah perusahaan <?= esc($comp['nama_perusahaan']) ?>">
                                                    Ubah
                                                </a>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kartu Mobile Perusahaan Mitra -->
            <div class="d-md-none d-flex flex-column gap-3">
                <?php if (empty($companies)): ?>
                    <div class="ecc-low-card text-center ecc-text-muted-aa py-4 small">Belum ada data perusahaan mitra.</div>
                <?php else: ?>
                    <?php foreach ($companies as $comp): ?>
                        <?php $webAman = ketarunaan_sanitize_external_url($comp['website'] ?? null, false); ?>
                        <div class="ecc-low-card p-3 <?= ($selected && (int) $selected['id'] === (int) $comp['id']) ? 'border-primary' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($comp['nama_perusahaan']) ?></div>
                                    <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                        <?= esc($comp['sektor'] ?? '-') ?><?= !empty($comp['kota']) ? ' &bull; ' . esc($comp['kota']) : '' ?>
                                    </div>
                                </div>
                                <?= ketarunaan_perusahaan_verifikasi_badge((int) ($comp['is_verified'] ?? 0)) ?>
                            </div>
                            <div class="ecc-text-muted-aa small mb-2">
                                PIC: <strong class="text-dark"><?= esc($comp['kontak_person'] ?? '-') ?></strong>
                                <?php if (!empty($comp['email_rekrutmen'])): ?>
                                    &bull; <?= esc($comp['email_rekrutmen']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="ecc-text-muted-aa small ecc-num">Lowongan aktif: <strong class="text-dark"><?= (int) $comp['jumlah_terbuka'] ?> / <?= (int) $comp['jumlah_lowongan'] ?></strong></span>
                                <?php if ($canManage): ?>
                                    <a href="<?= base_url('ketarunaan/lowongan/perusahaan?edit=' . (int) $comp['id']) ?>" class="btn btn-light border btn-sm ecc-btn-action low-touch-ctrl">Ubah</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Tambah / Sunting Perusahaan & Kontak (Tanpa input tanggal pemeriksaan sesuai AGENTS.md §4.L) -->
        <?php if ($canManage): ?>
            <div class="col-12 col-xl-5">
                <div class="ecc-low-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-bold text-dark mb-0"><?= $selected ? 'Ubah Perusahaan Mitra' : 'Tambah Perusahaan Mitra' ?></h2>
                        <?php if ($selected): ?>
                            <a href="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="btn btn-light border btn-sm ecc-btn-action">Tambah Baru</a>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="<?= base_url('ketarunaan/lowongan/perusahaan/simpan') ?>" class="low-form-mobile">
                        <?= csrf_field() ?>
                        <input type="hidden" name="perusahaan_id" value="<?= $selected ? (int) $selected['id'] : '' ?>">

                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label fw-semibold ecc-text-muted-aa small">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control ecc-control-sm" required
                                   value="<?= esc(old('nama_perusahaan', $selected['nama_perusahaan'] ?? '')) ?>">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="sektor" class="form-label fw-semibold ecc-text-muted-aa small">Sektor Industri</label>
                                <input type="text" name="sektor" id="sektor" class="form-control ecc-control-sm"
                                       value="<?= esc(old('sektor', $selected['sektor'] ?? '')) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kota" class="form-label fw-semibold ecc-text-muted-aa small">Kota / Wilayah</label>
                                <input type="text" name="kota" id="kota" class="form-control ecc-control-sm"
                                       value="<?= esc(old('kota', $selected['kota'] ?? '')) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label fw-semibold ecc-text-muted-aa small">Situs Resmi Perusahaan</label>
                            <input type="url" name="website" id="website" class="form-control ecc-control-sm" placeholder="https://perusahaan.co.id"
                                   value="<?= esc(old('website', $selected['website'] ?? '')) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold ecc-text-muted-aa small">Alamat Kantor</label>
                            <textarea name="alamat" id="alamat" rows="2" class="form-control rounded-3"><?= esc(old('alamat', $selected['alamat'] ?? '')) ?></textarea>
                        </div>

                        <hr class="my-3">
                        <h3 class="h6 fw-bold text-dark mb-2 small">Kontak Rekrutmen</h3>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="kontak_person" class="form-label fw-semibold ecc-text-muted-aa small">Nama Kontak PIC</label>
                                <input type="text" name="kontak_person" id="kontak_person" class="form-control ecc-control-sm"
                                       value="<?= esc(old('kontak_person', $selected['kontak_person'] ?? '')) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="pic_jabatan" class="form-label fw-semibold ecc-text-muted-aa small">Jabatan PIC</label>
                                <input type="text" name="pic_jabatan" id="pic_jabatan" class="form-control ecc-control-sm"
                                       value="<?= esc(old('pic_jabatan', $selected['pic_jabatan'] ?? '')) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email_rekrutmen" class="form-label fw-semibold ecc-text-muted-aa small">Alamat Email Rekrutmen</label>
                                <input type="email" name="email_rekrutmen" id="email_rekrutmen" class="form-control ecc-control-sm"
                                       value="<?= esc(old('email_rekrutmen', $selected['email_rekrutmen'] ?? '')) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="telepon_rekrutmen" class="form-label fw-semibold ecc-text-muted-aa small">Telepon Kantor / HRD</label>
                                <input type="text" name="telepon_rekrutmen" id="telepon_rekrutmen" class="form-control ecc-control-sm ecc-num"
                                       value="<?= esc(old('telepon_rekrutmen', $selected['telepon_rekrutmen'] ?? '')) ?>">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h3 class="h6 fw-bold text-dark mb-2 small">Pemeriksaan Kontak Mitra</h3>
                        <?php if ($canValidate): ?>
                            <div class="mb-3">
                                <label for="is_verified" class="form-label fw-semibold ecc-text-muted-aa small">Status Pemeriksaan Kontak</label>
                                <select name="is_verified" id="is_verified" class="form-select ecc-control-sm">
                                    <option value="0" <?= (string) old('is_verified', $selected['is_verified'] ?? '0') === '0' ? 'selected' : '' ?>>Belum diperiksa</option>
                                    <option value="1" <?= (string) old('is_verified', $selected['is_verified'] ?? '0') === '1' ? 'selected' : '' ?>>Kontak diperiksa</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="sumber_verifikasi" class="form-label fw-semibold ecc-text-muted-aa small">Sumber Pemeriksaan</label>
                            <input type="text" name="sumber_verifikasi" id="sumber_verifikasi" class="form-control ecc-control-sm"
                                   value="<?= esc(old('sumber_verifikasi', $selected['sumber_verifikasi'] ?? '')) ?>" placeholder="Surat resmi / portal / telepon HRD">
                        </div>
                        <div class="mb-3">
                            <label for="catatan_verifikasi" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Pemeriksaan Kontak</label>
                            <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="2" class="form-control rounded-3"><?= esc(old('catatan_verifikasi', $selected['catatan_verifikasi'] ?? '')) ?></textarea>
                            <?php if (!empty($selected['diperiksa_oleh'])): ?>
                                <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">
                                    Pemeriksa kontak: <strong><?= esc($selected['diperiksa_oleh']) ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary ecc-control-sm fw-semibold btn-tactile low-touch-ctrl">
                                <?= $selected ? 'Simpan Perubahan Mitra' : 'Simpan Perusahaan Mitra' ?>
                            </button>
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
