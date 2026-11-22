<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Koreksi Kelulusan Alumni<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/alumni/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$queue             = $queue ?? [];
$pending           = $queue['pending'] ?? [];
$riwayat           = $queue['riwayat'] ?? [];
$activeAlumni      = $queue['active_alumni'] ?? [];
$jumlahAlumniAktif = (int) ($queue['jumlah_alumni_aktif'] ?? 0);
$refs              = $refs ?? [];
$statusPengganti   = $refs['status_pengganti'] ?? [];
$selectedNomor     = (string) old('nomor_taruna', $selectedNomor ?? '');
$perms             = $perms ?? [];
$canManage         = !empty($perms['can_manage']);
$canValidate       = !empty($perms['can_validate']);
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/alumni/_nav_tabs') ?>

    <!-- Ringkasan Kendali Koreksi -->
    <div class="ecc-alm-card mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-8">
                <h2 class="h6 fw-bold text-dark mb-1">Pengendalian Koreksi Status Kelulusan</h2>
                <p class="ecc-text-muted-aa mb-0 small">
                    Setiap perubahan data lulusan diajukan dengan alasan jelas untuk persetujuan validator.
                    Profil alumni yang dibatalkan kelulusannya tidak dihapus, melainkan dinonaktifkan agar riwayat tetap terjaga.
                </p>
            </div>
            <div class="col-12 col-lg-4">
                <div class="p-3 rounded-3 bg-light d-flex justify-content-between align-items-center small">
                    <span>Alumni aktif saat ini:</span>
                    <strong class="ecc-num fs-6 text-dark"><?= number_format($jumlahAlumniAktif) ?> profil</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php if ($canManage): ?>
            <!-- Form Usulan Koreksi Kelulusan -->
            <div class="col-12 col-lg-5">
                <div class="ecc-alm-card">
                    <h2 class="h6 fw-bold text-dark mb-1">Ajukan Koreksi Kelulusan</h2>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Data aktif belum berubah sebelum validator memutuskan usulan koreksi ini.
                    </p>
                    <form method="post" action="<?= base_url('ketarunaan/alumni/koreksi/ajukan') ?>" class="row g-3 alm-form-mobile" id="formKoreksiAlumni"
                          onsubmit="return window.eccAlmConfirmSubmit(event, 'Ajukan Koreksi Kelulusan?', 'Usulan koreksi akan dikirim ke antrean validator.', 'Ya, Ajukan', true);">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <label for="koreksi_nomor_taruna" class="form-label fw-semibold ecc-text-muted-aa small">Pilih Alumni Aktif <span class="text-danger">*</span></label>
                            <select name="nomor_taruna" id="koreksi_nomor_taruna" class="form-select ecc-control-sm js-alm-select2" required>
                                <option value="">-- Pilih alumni --</option>
                                <?php foreach ($activeAlumni as $al): ?>
                                    <option value="<?= esc((string) $al['nomor_taruna']) ?>" <?= $selectedNomor === (string) $al['nomor_taruna'] ? 'selected' : '' ?>>
                                        <?= esc((string) $al['nomor_taruna']) ?> &mdash; <?= esc((string) $al['nama_lengkap']) ?> (<?= esc((string) ($al['kode_prodi'] ?? '-')) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="jenis_kejadian" class="form-label fw-semibold ecc-text-muted-aa small">Jenis Koreksi <span class="text-danger">*</span></label>
                            <?php $oldJenis = (string) old('jenis_kejadian', 'ralat_sk'); ?>
                            <select name="jenis_kejadian" id="jenis_kejadian" class="form-select ecc-control-sm" required>
                                <option value="ralat_sk" <?= $oldJenis === 'ralat_sk' ? 'selected' : '' ?>>Ralat Nomor SK / Tanggal Lulus</option>
                                <option value="batal_lulus" <?= $oldJenis === 'batal_lulus' ? 'selected' : '' ?>>Pembatalan Kelulusan (Kembalikan ke Status Taruna)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6 group-ralat-sk">
                            <label for="tanggal_lulus_baru" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Lulus Baru <span class="text-danger">*</span></label>
                            <input type="text" name="tanggal_lulus_baru" id="tanggal_lulus_baru" class="form-control ecc-control-sm ecc-num js-alm-date"
                                   value="<?= esc((string) old('tanggal_lulus_baru', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="nomor_keputusan_baru" class="form-label fw-semibold ecc-text-muted-aa small">
                                Nomor SK Koreksi / Ralat <span class="text-danger" id="req_sk_baru">*</span>
                            </label>
                            <input type="text" name="nomor_keputusan_baru" id="nomor_keputusan_baru" class="form-control ecc-control-sm"
                                   value="<?= esc((string) old('nomor_keputusan_baru', '')) ?>" placeholder="SK.RALAT/PKTJ/...">
                        </div>

                        <div class="col-12 col-md-6 group-batal-lulus d-none">
                            <label for="status_taruna_pengganti" class="form-label fw-semibold ecc-text-muted-aa small">Status Taruna Pengganti</label>
                            <select name="status_taruna_pengganti" id="status_taruna_pengganti" class="form-select ecc-control-sm">
                                <?php $oldPengganti = (string) old('status_taruna_pengganti', 'aktif'); ?>
                                <?php foreach ($statusPengganti as $kodeStatus => $labelStatus): ?>
                                    <option value="<?= esc($kodeStatus) ?>" <?= $oldPengganti === $kodeStatus ? 'selected' : '' ?>>
                                        <?= esc($labelStatus) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="koreksi_tanggal_kejadian" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Berlaku Koreksi <span class="text-danger">*</span></label>
                            <input type="text" name="tanggal_kejadian" id="koreksi_tanggal_kejadian" class="form-control ecc-control-sm ecc-num js-alm-date"
                                   value="<?= esc((string) old('tanggal_kejadian', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD" required>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-light small" id="previewDampakKoreksi">
                                Dampak jika disetujui: jumlah alumni aktif tetap <strong class="ecc-num"><?= number_format($jumlahAlumniAktif) ?></strong>; hanya SK dan tanggal lulus diperbarui.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="alasan_perubahan_koreksi" class="form-label fw-semibold ecc-text-muted-aa small">Alasan Koreksi <span class="text-danger">*</span></label>
                            <textarea name="alasan_perubahan" id="alasan_perubahan_koreksi" rows="3" class="form-control rounded-3" required placeholder="Jelaskan dasar administratif pengajuan koreksi ini"><?= esc((string) old('alasan_perubahan', '')) ?></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile alm-touch-ctrl">
                                Ajukan Koreksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Antrean & Riwayat Koreksi -->
        <div class="col-12 <?= $canManage ? 'col-lg-7' : '' ?>">
            <div class="ecc-alm-card mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold text-dark mb-0">Menunggu Keputusan Validator</h2>
                    <span class="badge rounded-pill px-3 py-1 fw-semibold bg-warning-subtle text-warning-emphasis border border-warning-subtle ecc-num" style="font-size: 0.75rem;">
                        <?= count($pending) ?> usulan
                    </span>
                </div>

                <?php if (empty($pending)): ?>
                    <div class="ecc-text-muted-aa py-3 small">
                        Belum ada usulan koreksi kelulusan yang menunggu persetujuan.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($pending as $row): ?>
                            <?php $jMeta = ketarunaan_alumni_jenis_kejadian_meta((string) ($row['jenis_kejadian'] ?? 'ralat_sk')); ?>
                            <div class="ecc-alm-timeline-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($jMeta['class']) ?> mb-1" style="font-size: 0.75rem;">
                                            <?= esc($jMeta['label']) ?>
                                        </span>
                                        <div class="fw-bold text-dark"><?= esc((string) $row['nama_lengkap']) ?></div>
                                        <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                            No. Taruna: <?= esc((string) $row['nomor_taruna']) ?> &bull; <?= esc((string) ($row['kode_prodi'] ?? '-')) ?>
                                        </div>
                                    </div>
                                    <?= ketarunaan_alumni_koreksi_status_badge((string) $row['status_validasi']) ?>
                                </div>
                                <div class="text-dark mb-1 small">
                                    Alasan: <?= esc((string) ($row['alasan_perubahan'] ?? '-')) ?>
                                </div>
                                <div class="ecc-text-muted-aa mb-3 small">
                                    Dampak: <?= esc((string) ($row['dampak_ringkasan'] ?? '-')) ?>
                                </div>

                                <?php if ($canValidate): ?>
                                    <form method="post" action="<?= base_url('ketarunaan/alumni/koreksi/putuskan/' . (int) $row['id']) ?>" class="row g-2 align-items-center alm-form-mobile ecc-koreksi-decide-form">
                                        <?= csrf_field() ?>
                                        <div class="col-12 col-md-6">
                                            <input type="text" name="catatan_validator" class="form-control ecc-control-sm" placeholder="Catatan validator (wajib jika ditolak)" aria-label="Catatan validator">
                                        </div>
                                        <div class="col-12 col-md-6 d-flex flex-wrap justify-content-md-end gap-2">
                                            <button type="submit" name="keputusan" value="ditolak" class="btn btn-outline-danger ecc-btn-action alm-touch-ctrl">
                                                Tolak Koreksi
                                            </button>
                                            <button type="submit" name="keputusan" value="tervalidasi" class="btn btn-success ecc-btn-action alm-touch-ctrl">
                                                Setujui Koreksi
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat Keputusan Koreksi (Sesuai AGENTS.md §4.L: Tanpa Tanggal/Jam Validasi di UI) -->
            <div class="ecc-alm-card p-0 overflow-hidden d-none d-md-block">
                <div class="px-4 py-3 border-bottom">
                    <h2 class="h6 fw-bold text-dark mb-0">Riwayat Keputusan Koreksi</h2>
                </div>
                <div class="table-responsive ecc-table-alm-wrap">
                    <table class="table ecc-table-alm mb-0" data-sortable-table="true">
                        <thead>
                            <tr>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="0"><span>Alumni</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="1"><span>Jenis &amp; Alasan</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="2"><span>Pejabat Penyetuju</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                                <th aria-sort="none"><button type="button" class="alm-sort-btn" data-sort-col="3"><span>Status</span><i class="bi bi-arrow-down-up text-muted opacity-50 sort-indicator" aria-hidden="true"></i></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat)): ?>
                                <tr>
                                    <td colspan="4" class="text-center ecc-text-muted-aa py-4">Belum ada riwayat keputusan koreksi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riwayat as $rk): ?>
                                    <?php $rMeta = ketarunaan_alumni_jenis_kejadian_meta((string) ($rk['jenis_kejadian'] ?? '')); ?>
                                    <tr>
                                        <td data-sort-val="<?= esc(strtolower((string) $rk['nama_lengkap'])) ?>">
                                            <div class="fw-semibold text-dark"><?= esc((string) $rk['nama_lengkap']) ?></div>
                                            <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $rk['nomor_taruna']) ?></div>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower((string) $rMeta['label'])) ?>">
                                            <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($rMeta['class']) ?>" style="font-size: 0.75rem;">
                                                <?= esc($rMeta['label']) ?>
                                            </span>
                                            <div class="ecc-text-muted-aa mt-1" style="font-size: 0.75rem;"><?= esc((string) ($rk['alasan_perubahan'] ?? '-')) ?></div>
                                        </td>
                                        <td data-sort-val="<?= esc(strtolower((string) ($rk['divalidasi_oleh'] ?? ''))) ?>">
                                            <div class="fw-medium text-dark"><?= esc((string) ($rk['divalidasi_oleh'] ?? '-')) ?></div>
                                            <?php if (!empty($rk['catatan_validator'])): ?>
                                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;"><?= esc((string) $rk['catatan_validator']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-sort-val="<?= esc((string) ($rk['status_validasi'] ?? 'tervalidasi')) ?>"><?= ketarunaan_alumni_koreksi_status_badge((string) ($rk['status_validasi'] ?? 'tervalidasi')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kartu Mobile Riwayat Keputusan Koreksi (Sesuai AGENTS.md §4.L: Tanpa Tanggal/Jam Validasi) -->
            <div class="d-md-none d-flex flex-column gap-3">
                <h2 class="h6 fw-bold text-dark mb-0">Riwayat Keputusan Koreksi</h2>
                <?php if (empty($riwayat)): ?>
                    <div class="ecc-alm-card text-center ecc-text-muted-aa py-4 small">Belum ada riwayat keputusan koreksi.</div>
                <?php else: ?>
                    <?php foreach ($riwayat as $rk): ?>
                        <?php $rMeta = ketarunaan_alumni_jenis_kejadian_meta((string) ($rk['jenis_kejadian'] ?? '')); ?>
                        <div class="ecc-alm-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc((string) $rk['nama_lengkap']) ?></div>
                                    <div class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;"><?= esc((string) $rk['nomor_taruna']) ?></div>
                                </div>
                                <?= ketarunaan_alumni_koreksi_status_badge((string) ($rk['status_validasi'] ?? 'tervalidasi')) ?>
                            </div>
                            <div class="mb-2">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($rMeta['class']) ?>" style="font-size: 0.75rem;">
                                    <?= esc($rMeta['label']) ?>
                                </span>
                            </div>
                            <div class="ecc-text-muted-aa small mb-1"><?= esc((string) ($rk['alasan_perubahan'] ?? '-')) ?></div>
                            <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                Penyetuju: <strong class="text-dark"><?= esc((string) ($rk['divalidasi_oleh'] ?? '-')) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/alumni/_scripts_common') ?>
<script>
(function () {
    var totalAktif = <?= (int) $jumlahAlumniAktif ?>;
    var jenisSelect = document.getElementById('jenis_kejadian');
    var previewEl = document.getElementById('previewDampakKoreksi');
    var tglBaruEl = document.getElementById('tanggal_lulus_baru');
    var skBaruEl  = document.getElementById('nomor_keputusan_baru');
    var reqSkEl   = document.getElementById('req_sk_baru');

    function syncFormFields() {
        if (!jenisSelect) {
            return;
        }
        var isBatal = jenisSelect.value === 'batal_lulus';
        document.querySelectorAll('.group-ralat-sk').forEach(function (el) {
            el.classList.toggle('d-none', isBatal);
        });
        document.querySelectorAll('.group-batal-lulus').forEach(function (el) {
            el.classList.toggle('d-none', !isBatal);
        });
        if (tglBaruEl) {
            tglBaruEl.required = !isBatal;
        }
        if (skBaruEl) {
            skBaruEl.required = !isBatal;
        }
        if (reqSkEl) {
            reqSkEl.classList.toggle('d-none', isBatal);
        }
        if (previewEl) {
            if (isBatal) {
                var proyeksi = Math.max(0, totalAktif - 1);
                previewEl.textContent = 'Dampak jika disetujui: jumlah alumni aktif berkurang dari ' + totalAktif + ' menjadi ' + proyeksi + ' (profil dinonaktifkan, riwayat tetap ada).';
            } else {
                previewEl.textContent = 'Dampak jika disetujui: jumlah alumni aktif tetap ' + totalAktif + '; hanya SK dan tanggal lulus diperbarui.';
            }
        }
    }

    if (jenisSelect) {
        jenisSelect.addEventListener('change', syncFormFields);
        syncFormFields();
    }

    document.querySelectorAll('.ecc-koreksi-decide-form button[type="submit"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var form = btn.closest('form');
            if (!form || form.dataset.confirmed === '1') {
                return;
            }
            e.preventDefault();
            var label = btn.textContent.trim();
            var isDanger = btn.value === 'ditolak';
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = btn.name || 'keputusan';
            hiddenInput.value = btn.value || '';

            var commit = function () {
                form.querySelectorAll('input[data-temp-keputusan="1"]').forEach(function (el) { el.remove(); });
                hiddenInput.dataset.tempKeputusan = '1';
                form.appendChild(hiddenInput);
                form.dataset.confirmed = '1';
                form.submit();
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: label + '?',
                    text: 'Keputusan koreksi kelulusan akan diperhitungkan pada profil alumni dan dasbor.',
                    icon: isDanger ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: isDanger ? '#dc2626' : '#0d6efd'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        commit();
                    }
                });
            } else if (window.confirm(label + '?')) {
                commit();
            }
        });
    });
})();
</script>
<?= $this->endSection() ?>
