<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Riwayat dan Kendali Masa Tayang<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$vacancies  = $vacancies ?? [];
$selected   = $selected ?? null;
$refs       = $refs ?? [];
$perms      = $perms ?? [];
$waktuAcuan = $waktuAcuan ?? date('Y-m-d H:i:s');
$canPublish = !empty($perms['can_publish']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <!-- Pemilih Lowongan -->
    <div class="ecc-low-card mb-3 py-3">
        <form method="get" action="<?= base_url('ketarunaan/lowongan/riwayat') ?>" class="row g-2 align-items-end low-filter-form">
            <div class="col-12 col-md-8">
                <label for="lowongan_riwayat_select" class="form-label fw-semibold ecc-text-muted-aa mb-1" style="font-size: 0.75rem;">Pilih Lowongan untuk Tinjau Riwayat</label>
                <select name="lowongan_id" id="lowongan_riwayat_select" class="form-select ecc-control-sm js-low-select2" onchange="this.form.submit()">
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
                    <a href="<?= base_url('ketarunaan/lowongan/detail/' . (int) $selected['id']) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile low-touch-ctrl text-nowrap">
                        Lihat Detail
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($selected): ?>
        <!-- 3 Kartu Ringkas Status -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-2 fw-semibold" style="font-size: 0.75rem;">Status Validasi</div>
                    <div><?= ketarunaan_validasi_badge((string) ($selected['status_validasi'] ?? 'draf')) ?></div>
                    <?php if (!empty($selected['diperiksa_oleh'])): ?>
                        <div class="ecc-text-muted-aa mt-2" style="font-size: 0.75rem;">Pemeriksa: <?= esc($selected['diperiksa_oleh']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-2 fw-semibold" style="font-size: 0.75rem;">Status Tayang Efektif</div>
                    <div><?= ketarunaan_lowongan_publikasi_badge($selected, $waktuAcuan) ?></div>
                    <?php if (!empty($selected['alasan_penutupan'])): ?>
                        <div class="text-danger mt-2" style="font-size: 0.75rem;">Alasan: <?= esc($selected['alasan_penutupan']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="ecc-low-kpi">
                    <div class="ecc-text-muted-aa mb-2 fw-semibold" style="font-size: 0.75rem;">Tenggat Pendaftaran</div>
                    <div class="fw-bold fs-5 text-dark ecc-num"><?= esc(ketarunaan_format_tanggal($selected['batas_pendaftaran'] ?? null)) ?></div>
                    <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;">Berhenti tampil otomatis setelah tenggat</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Tabel Riwayat Proses (Sesuai AGENTS.md §4.L: Hanya Nama Pejabat, Aktivitas, Status, & Catatan — Tanpa Tanggal/Jam) -->
            <div class="col-12 col-lg-8">
                <?php $riwayatList = array_reverse($selected['riwayat_proses'] ?? []); ?>
                <div class="ecc-low-card p-0 overflow-hidden d-none d-md-block">
                    <div class="p-3 border-bottom">
                        <h2 class="h6 fw-bold text-dark mb-0">Riwayat Proses</h2>
                    </div>
                    <div class="ecc-table-low-wrap table-responsive">
                        <table class="table ecc-table-low mb-0" data-sortable-table="true">
                            <thead>
                                <tr>
                                    <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="0"><span>Pejabat / Pelaku</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                    <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="1"><span>Aktivitas</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                    <th aria-sort="none"><button type="button" class="low-sort-btn" data-sort-col="2"><span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($riwayatList)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center ecc-text-muted-aa py-5">Belum ada catatan riwayat proses.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($riwayatList as $log): ?>
                                        <tr>
                                            <td class="fw-medium text-dark" data-sort-val="<?= esc(strtolower((string) ($log['pelaku'] ?? ''))) ?>"><?= esc($log['pelaku'] ?? '-') ?></td>
                                            <td data-sort-val="<?= esc(strtolower((string) ($log['aksi'] ?? ''))) ?>">
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;"><?= esc($log['aksi'] ?? '-') ?></span>
                                            </td>
                                            <td data-sort-val="<?= esc((string) ($log['status'] ?? 'draf')) ?>">
                                                <?= ketarunaan_validasi_badge((string) ($log['status'] ?? 'draf')) ?>
                                            </td>
                                            <td class="ecc-text-muted-aa"><?= esc($log['catatan'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kartu Mobile Riwayat Proses (Sesuai AGENTS.md §4.L Tanpa Tanggal/Jam) -->
                <div class="d-md-none d-flex flex-column gap-3">
                    <?php if (empty($riwayatList)): ?>
                        <div class="ecc-low-card text-center ecc-text-muted-aa py-4 small">Belum ada catatan riwayat proses.</div>
                    <?php else: ?>
                        <?php foreach ($riwayatList as $log): ?>
                            <div class="ecc-low-card p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div class="fw-bold text-dark"><?= esc($log['pelaku'] ?? '-') ?></div>
                                    <?= ketarunaan_validasi_badge((string) ($log['status'] ?? 'draf')) ?>
                                </div>
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-1" style="font-size: 0.75rem;"><?= esc($log['aksi'] ?? '-') ?></span>
                                </div>
                                <div class="ecc-text-muted-aa small"><?= esc($log['catatan'] ?? '-') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pengendalian Masa Tayang & Penutupan Manual -->
            <div class="col-12 col-lg-4">
                <div class="ecc-low-card">
                    <h2 class="h6 fw-bold text-dark mb-2">Pengendalian Masa Tayang</h2>
                    <ul class="ecc-text-muted-aa ps-3 mb-3 small">
                        <li class="mb-1">Lowongan yang melewati tenggat berhenti tampil sebagai lowongan terbuka.</li>
                        <li class="mb-1">Penutupan manual mewajibkan alasan dan tidak menghapus arsip maupun riwayatnya.</li>
                        <li>Perubahan substantif pasca terbit otomatis memerlukan verifikasi ulang.</li>
                    </ul>

                    <?php if ($canPublish && ($selected['status_publikasi'] ?? '') !== 'ditutup'): ?>
                        <form method="post" action="<?= base_url('ketarunaan/lowongan/tutup/' . (int) $selected['id']) ?>" id="formTutupManual" class="low-form-mobile"
                              onsubmit="return window.eccLowConfirmSubmit(event, 'Tutup Lowongan?', 'Lowongan ditutup tidak lagi tayang sebagai lowongan terbuka.', 'Ya, Tutup', true);">
                            <?= csrf_field() ?>
                            <div class="mb-2">
                                <label for="alasan_pilihan_rwy" class="form-label fw-semibold ecc-text-muted-aa small">Alasan Penutupan Manual <span class="text-danger">*</span></label>
                                <select name="alasan_pilihan" id="alasan_pilihan_rwy" class="form-select ecc-control-sm" required>
                                    <?php foreach (($refs['alasan_penutupan'] ?? []) as $al): ?>
                                        <option value="<?= esc($al) ?>"><?= esc($al) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="alasan_custom_rwy" class="form-label fw-semibold ecc-text-muted-aa small">
                                    Keterangan Tambahan <span class="text-danger d-none" id="req_alasan_custom_rwy">*</span>
                                </label>
                                <textarea name="alasan_custom" id="alasan_custom_rwy" rows="2" class="form-control rounded-3" placeholder="Tambahkan rincian alasan penutupan"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-danger ecc-control-sm fw-semibold btn-tactile low-touch-ctrl">
                                    Tutup Lowongan Sekarang
                                </button>
                            </div>
                        </form>
                    <?php elseif (($selected['status_publikasi'] ?? '') === 'ditutup'): ?>
                        <div class="p-3 rounded-3 bg-light border ecc-text-muted-aa small">
                            Lowongan telah ditutup oleh <strong class="text-dark"><?= esc($selected['ditutup_oleh'] ?? '-') ?></strong>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<script>
(function () {
    var sel = document.getElementById('alasan_pilihan_rwy');
    var txt = document.getElementById('alasan_custom_rwy');
    var req = document.getElementById('req_alasan_custom_rwy');
    if (sel && txt) {
        var syncAlasanRwy = function () {
            var isLainnya = sel.value === 'Lainnya';
            txt.required = isLainnya;
            if (req) {
                req.classList.toggle('d-none', !isLainnya);
            }
        };
        sel.addEventListener('change', syncAlasanRwy);
        syncAlasanRwy();
    }
})();
</script>
<?= $this->endSection() ?>
