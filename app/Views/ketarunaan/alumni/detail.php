<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Detail Profil Alumni<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/alumni/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail           = $detail ?? [];
$alumni           = $detail['alumni'] ?? [];
$riwayatPerubahan = $detail['riwayat_perubahan'] ?? [];
$pendingKoreksi   = $detail['pending_koreksi'] ?? null;
$ringkasRekam     = $detail['ringkas_rekam'] ?? ['prestasi' => 0, 'beasiswa' => 0];
$dampakProyeksi   = $detail['dampak_proyeksi'] ?? ['alumni_aktif_saat_ini' => 0, 'alumni_aktif_jika_batal' => 0];
$refs             = $refs ?? [];
$sumberMap        = $refs['sumber_konfirmasi'] ?? ketarunaan_alumni_sumber_konfirmasi_catalog();
$canViewSensitive = !empty($canViewSensitive);
$canUpdateContact = !empty($canUpdateContact);
$isSelfOnly       = !empty($isSelfOnly);
$perms            = $perms ?? [];

$nomorTaruna = (string) ($alumni['nomor_taruna'] ?? '');
$nama        = (string) ($alumni['nama_lengkap'] ?? '-');
$words       = preg_split('/\s+/', trim($nama)) ?: [];
$initials    = '';
foreach (array_slice($words, 0, 2) as $w) {
    $initials .= strtoupper(mb_substr($w, 0, 1));
}
$isAktifProfil = ($alumni['status_profil'] ?? 'aktif') === 'aktif';
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/alumni/_nav_tabs') ?>

    <div class="mb-3">
        <a href="<?= base_url('ketarunaan/alumni') ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center gap-2 btn-tactile alm-touch-ctrl">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Direktori Alumni
        </a>
    </div>

    <!-- Header Profil Dasar 1-to-1 Nomor Taruna -->
    <div class="ecc-alm-card mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="ecc-avatar-48 bg-primary-subtle text-primary-emphasis"><?= esc($initials ?: 'AL') ?></div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h2 class="h5 fw-bold text-dark mb-0"><?= esc($nama) ?></h2>
                        <?= ketarunaan_alumni_status_badge((string) ($alumni['status_profil'] ?? 'aktif')) ?>
                    </div>
                    <div class="ecc-text-muted-aa ecc-num small">
                        No. Taruna: <strong class="text-dark"><?= esc($nomorTaruna) ?></strong>
                        &bull; <?= esc((string) ($alumni['kode_prodi'] ?? '-')) ?> (<?= esc((string) ($alumni['nama_prodi'] ?? '-')) ?>)
                        &bull; Angkatan <?= esc(ketarunaan_format_angkatan($alumni['tahun_masuk'] ?? null)) ?>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php if (!$isSelfOnly): ?>
                    <a href="<?= site_url('ketarunaan/taruna/profil/' . rawurlencode($nomorTaruna)) ?>" class="btn btn-outline-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile alm-touch-ctrl text-nowrap">
                        Profil Taruna Asal
                    </a>
                    <a href="<?= site_url('ketarunaan/rekam-jejak?nomor_taruna=' . rawurlencode($nomorTaruna)) ?>" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile alm-touch-ctrl text-nowrap">
                        Rekam Jejak
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <hr class="my-3 text-secondary opacity-25">

        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Tanggal Lulus</div>
                <div class="fw-semibold text-dark ecc-num"><?= esc(ketarunaan_format_tanggal($alumni['tanggal_lulus'] ?? null)) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Nomor SK Kelulusan</div>
                <div class="fw-semibold text-dark ecc-num"><?= esc((string) ($alumni['nomor_keputusan_lulus'] ?? '-')) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Pejabat Pengesah</div>
                <div class="fw-semibold text-dark"><?= esc((string) ($alumni['divalidasi_oleh'] ?: 'Validator Ketarunaan')) ?></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">Konfirmasi Kontak Terakhir</div>
                <div class="fw-semibold text-dark ecc-num">
                    <?= esc(ketarunaan_format_tanggal($alumni['tanggal_pembaruan_kontak'] ?? null)) ?>
                    <?php if (!empty($alumni['sumber_konfirmasi'])): ?>
                        <div class="ecc-text-muted-aa fw-normal" style="font-size: 0.75rem;">
                            <?= esc($sumberMap[$alumni['sumber_konfirmasi']] ?? $alumni['sumber_konfirmasi']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pendingKoreksi !== null): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-3 px-3 py-2 mb-3">
            <div class="fw-bold text-dark mb-1">Ada Usulan Koreksi Kelulusan Menunggu Persetujuan</div>
            <div class="small">
                <?= esc((string) ($pendingKoreksi['judul_ringkas'] ?? '')) ?> &mdash;
                Diajukan oleh <strong><?= esc((string) ($pendingKoreksi['diajukan_oleh'] ?? '-')) ?></strong>.
                Data aktif alumni belum berubah sebelum diputuskan validator.
                <?php if (!empty($perms['can_view_staff'])): ?>
                    <a href="<?= base_url('ketarunaan/alumni/koreksi') ?>" class="fw-semibold ms-2">Buka Antrean Koreksi</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <!-- Kolom Kiri: Form Pembaruan Kontak & Riwayat Perubahan -->
        <div class="col-12 col-lg-7 d-flex flex-column gap-3">
            <?php if ($canUpdateContact): ?>
                <div class="ecc-alm-card">
                    <h2 class="h6 fw-bold text-dark mb-1">Perbarui Kontak &amp; Pekerjaan Saat Ini</h2>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Setiap pembaruan dicatat beserta sumber konfirmasi dan nama pencatat agar jejak perubahan tetap jelas.
                    </p>

                    <?php if (!$isAktifProfil): ?>
                        <div class="alert alert-secondary border-0 rounded-3 mb-0 small">
                            Profil alumni ini berstatus nonaktif karena koreksi kelulusan sehingga pembaruan kontak ditutup.
                        </div>
                    <?php else: ?>
                        <form method="post" action="<?= base_url('ketarunaan/alumni/kontak/' . rawurlencode($nomorTaruna)) ?>" class="row g-3 alm-form-mobile">
                            <?= csrf_field() ?>
                            <div class="col-12 col-md-6">
                                <label for="pekerjaan_saat_ini" class="form-label fw-semibold ecc-text-muted-aa small">Pekerjaan / Posisi Saat Ini</label>
                                <input type="text" name="pekerjaan_saat_ini" id="pekerjaan_saat_ini" class="form-control ecc-control-sm"
                                       value="<?= esc((string) old('pekerjaan_saat_ini', $alumni['pekerjaan_saat_ini'] ?? '')) ?>"
                                       placeholder="Contoh: Penguji Kendaraan Bermotor">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="instansi_saat_ini" class="form-label fw-semibold ecc-text-muted-aa small">Instansi / Perusahaan</label>
                                <input type="text" name="instansi_saat_ini" id="instansi_saat_ini" class="form-control ecc-control-sm"
                                       value="<?= esc((string) old('instansi_saat_ini', $alumni['instansi_saat_ini'] ?? '')) ?>"
                                       placeholder="Nama instansi atau perusahaan">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email_alumni" class="form-label fw-semibold ecc-text-muted-aa small">Alamat Email Aktif</label>
                                <input type="email" name="email_alumni" id="email_alumni" class="form-control ecc-control-sm"
                                       value="<?= esc((string) old('email_alumni', $alumni['email_alumni'] ?? '')) ?>"
                                       placeholder="nama@contoh.id">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="telepon_alumni" class="form-label fw-semibold ecc-text-muted-aa small">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="telepon_alumni" id="telepon_alumni" class="form-control ecc-control-sm ecc-num"
                                       value="<?= esc((string) old('telepon_alumni', $alumni['telepon_alumni'] ?: ($alumni['kontak_terakhir'] ?? ''))) ?>"
                                       placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kota_domisili" class="form-label fw-semibold ecc-text-muted-aa small">Kota Domisili</label>
                                <input type="text" name="kota_domisili" id="kota_domisili" class="form-control ecc-control-sm"
                                       value="<?= esc((string) old('kota_domisili', $alumni['kota_domisili'] ?? '')) ?>"
                                       placeholder="Contoh: Semarang">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="sumber_konfirmasi" class="form-label fw-semibold ecc-text-muted-aa small">Sumber Konfirmasi</label>
                                <?php if ($isSelfOnly): ?>
                                    <input type="text" id="sumber_konfirmasi" class="form-control ecc-control-sm bg-light" value="Mandiri oleh Alumni" readonly>
                                    <input type="hidden" name="sumber_konfirmasi" value="mandiri_alumni">
                                <?php else: ?>
                                    <select name="sumber_konfirmasi" id="sumber_konfirmasi" class="form-select ecc-control-sm" required>
                                        <?php $selSumber = (string) old('sumber_konfirmasi', $alumni['sumber_konfirmasi'] ?? 'telepon_pic'); ?>
                                        <?php foreach ($sumberMap as $kodeSumber => $labelSumber): ?>
                                            <option value="<?= esc($kodeSumber) ?>" <?= $selSumber === $kodeSumber ? 'selected' : '' ?>>
                                                <?= esc($labelSumber) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_kejadian" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Konfirmasi <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_kejadian" id="tanggal_kejadian" class="form-control ecc-control-sm ecc-num js-alm-date"
                                       value="<?= esc((string) old('tanggal_kejadian', date('Y-m-d'))) ?>" placeholder="YYYY-MM-DD" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="catatan_pembaruan" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Singkat</label>
                                <input type="text" name="catatan_pembaruan" id="catatan_pembaruan" class="form-control ecc-control-sm"
                                       value="<?= esc((string) old('catatan_pembaruan', '')) ?>"
                                       placeholder="Contoh: Alumni pindah unit kerja baru">
                            </div>
                            <div class="col-12">
                                <label for="alamat_domisili" class="form-label fw-semibold ecc-text-muted-aa small">Alamat Domisili</label>
                                <textarea name="alamat_domisili" id="alamat_domisili" rows="2" class="form-control rounded-3"><?= esc((string) old('alamat_domisili', $alumni['alamat_domisili'] ?? '')) ?></textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary ecc-control-sm px-4 fw-semibold btn-tactile alm-touch-ctrl">
                                    Simpan Pembaruan Kontak
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Riwayat Pembaruan & Koreksi (Sesuai AGENTS.md §4.L Tanpa Tanggal Validasi/Keputusan) -->
            <div class="ecc-alm-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold text-dark mb-0">Riwayat Pembaruan Kontak &amp; Koreksi</h2>
                    <span class="ecc-text-muted-aa ecc-num small"><?= count($riwayatPerubahan) ?> catatan</span>
                </div>
                <?php if (empty($riwayatPerubahan)): ?>
                    <div class="ecc-text-muted-aa small">Belum ada catatan perubahan profil alumni.</div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($riwayatPerubahan as $item): ?>
                            <?php
                            $jenisMeta = ketarunaan_alumni_jenis_kejadian_meta((string) ($item['jenis_kejadian'] ?? ''));
                            $sumberLbl = $sumberMap[$item['sumber_konfirmasi'] ?? ''] ?? ($item['sumber_konfirmasi'] ?? '-');
                            ?>
                            <div class="ecc-alm-timeline-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold <?= esc($jenisMeta['class']) ?>" style="font-size: 0.75rem;">
                                            <?= esc($jenisMeta['label']) ?>
                                        </span>
                                        <?= ketarunaan_alumni_koreksi_status_badge((string) ($item['status_validasi'] ?? 'tervalidasi')) ?>
                                    </div>
                                    <?php if (($item['jenis_kejadian'] ?? '') === 'pembaruan_kontak'): ?>
                                        <span class="ecc-text-muted-aa ecc-num" style="font-size: 0.75rem;">
                                            <?= esc(ketarunaan_format_tanggal($item['tanggal_kejadian'] ?? null)) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="fw-semibold text-dark mb-1 small">
                                    <?= esc((string) ($item['judul_ringkas'] ?? '-')) ?>
                                </div>
                                <?php if (!empty($item['alasan_perubahan'])): ?>
                                    <div class="ecc-text-muted-aa mb-1 small">
                                        <?= esc((string) $item['alasan_perubahan']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['dampak_ringkasan'])): ?>
                                    <div class="text-dark mb-1 small">
                                        Dampak: <?= esc((string) $item['dampak_ringkasan']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="ecc-text-muted-aa" style="font-size: 0.75rem;">
                                    Dicatat oleh: <strong class="text-dark"><?= esc((string) ($item['diajukan_oleh'] ?? '-')) ?></strong>
                                    <?php if (!empty($item['divalidasi_oleh'])): ?>
                                        &bull; Penyetuju: <strong class="text-dark"><?= esc((string) $item['divalidasi_oleh']) ?></strong>
                                    <?php endif; ?>
                                    &bull; Sumber: <?= esc((string) $sumberLbl) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Pendidikan, Privasi Data, & Aksi Koreksi -->
        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
            <div class="ecc-alm-card">
                <h2 class="h6 fw-bold text-dark mb-3">Tautan Data Taruna Asal</h2>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="ecc-text-muted-aa">Program Studi</span>
                        <span class="fw-semibold text-dark text-end"><?= esc((string) ($alumni['nama_prodi'] ?? '-')) ?> (<?= esc((string) ($alumni['jenjang'] ?? 'D-IV')) ?>)</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="ecc-text-muted-aa">Jalur Penerimaan</span>
                        <span class="fw-semibold text-dark"><?= esc((string) ($alumni['jalur_penerimaan'] ?? '-')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="ecc-text-muted-aa">Asal Formasi</span>
                        <span class="fw-semibold text-dark"><?= esc((string) ($alumni['asal_formasi'] ?: '-')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="ecc-text-muted-aa">Prestasi Tervalidasi</span>
                        <span class="fw-semibold text-dark ecc-num"><?= number_format((int) $ringkasRekam['prestasi']) ?> prestasi</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="ecc-text-muted-aa">Penerimaan Beasiswa</span>
                        <span class="fw-semibold text-dark ecc-num"><?= number_format((int) $ringkasRekam['beasiswa']) ?> riwayat</span>
                    </div>
                </div>
            </div>

            <div class="ecc-alm-card">
                <h2 class="h6 fw-bold text-dark mb-3">Perlindungan Data Pribadi</h2>
                <?php if ($canViewSensitive): ?>
                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="ecc-text-muted-aa">NIK</span>
                            <span class="fw-semibold text-dark ecc-num"><?= esc((string) ($alumni['nik'] ?: '-')) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="ecc-text-muted-aa">Nama Wali</span>
                            <span class="fw-semibold text-dark"><?= esc((string) ($alumni['nama_wali'] ?: '-')) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="ecc-text-muted-aa">Kontak Wali</span>
                            <span class="fw-semibold text-dark ecc-num"><?= esc((string) ($alumni['nomor_hp_wali'] ?: '-')) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-3 bg-light ecc-text-muted-aa small">
                        Data NIK dan kontak wali dibatasi khusus untuk pengelola ketarunaan berwenang.
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($perms['can_manage']) && $isAktifProfil): ?>
                <div class="ecc-alm-card">
                    <h2 class="h6 fw-bold text-dark mb-2">Koreksi Kelulusan Terkendali</h2>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Jika terdapat kesalahan SK, tanggal lulus, atau pembatalan kelulusan, ajukan usulan koreksi agar diperiksa pejabat validator.
                    </p>
                    <div class="p-3 rounded-3 bg-light mb-3 small">
                        <div>Alumni aktif saat ini: <strong class="ecc-num text-dark"><?= number_format((int) $dampakProyeksi['alumni_aktif_saat_ini']) ?></strong></div>
                        <div>Jika kelulusan dibatalkan: <strong class="ecc-num text-danger"><?= number_format((int) $dampakProyeksi['alumni_aktif_jika_batal']) ?></strong> (riwayat tetap tersimpan)</div>
                    </div>
                    <a href="<?= base_url('ketarunaan/alumni/koreksi?nomor_taruna=' . rawurlencode($nomorTaruna)) ?>"
                       class="btn btn-outline-danger ecc-control-sm px-4 fw-semibold w-100 d-inline-flex align-items-center justify-content-center btn-tactile alm-touch-ctrl">
                        Ajukan Koreksi Kelulusan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/alumni/_scripts_common') ?>
<?= $this->endSection() ?>
