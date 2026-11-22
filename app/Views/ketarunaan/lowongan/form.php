<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= !empty($isEdit) ? 'Ubah Lowongan' : 'Tambah Lowongan' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/lowongan/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$isEdit      = !empty($isEdit);
$low         = $lowongan ?? [];
$refs        = $refs ?? [];
$actionUrl   = $isEdit ? base_url('ketarunaan/lowongan/update/' . (int) $low['id']) : base_url('ketarunaan/lowongan/simpan');
$selectedProdis = old('prodi_ids') !== null
    ? array_map('intval', (array) old('prodi_ids'))
    : ($low['sasaran_prodi_ids'] ?? []);
$batasRaw    = (string) old('batas_pendaftaran', !empty($low['batas_pendaftaran']) ? substr((string) $low['batas_pendaftaran'], 0, 10) : '');
$tglTerima   = (string) old('tanggal_diterima', $low['tanggal_diterima'] ?? date('Y-m-d'));
$metodeVal   = (string) old('metode_lamaran', $low['metode_lamaran'] ?? 'tautan_eksternal');
$isLockedOrPublished = $isEdit && (($low['status_validasi'] ?? '') === 'tervalidasi' || ($low['status_publikasi'] ?? '') === 'terbit');
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/lowongan/_nav_tabs') ?>

    <?php if ($isLockedOrPublished): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-3 px-3 py-2 mb-3 small">
            Perubahan pada posisi, perusahaan, kualifikasi, sasaran program studi, batas waktu, atau kanal lamaran akan mengembalikan lowongan ini ke antrean verifikasi ulang sebelum ditayangkan kembali.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $actionUrl ?>" id="formLowongan" class="low-form-mobile">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <!-- Seksi 1: Informasi Lowongan -->
                <div class="ecc-low-card mb-3">
                    <h2 class="h6 fw-bold text-dark mb-3">Informasi Lowongan</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-7">
                            <label for="judul_lowongan" class="form-label fw-semibold ecc-text-muted-aa small">Posisi / Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="judul_lowongan" id="judul_lowongan" class="form-control ecc-control-sm" required
                                   value="<?= esc(old('judul_lowongan', $low['judul_lowongan'] ?? '')) ?>" placeholder="Contoh: Transport Safety Officer">
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="perusahaan_id" class="form-label fw-semibold ecc-text-muted-aa mb-0 small">Perusahaan Mitra <span class="text-danger">*</span></label>
                                <a href="<?= base_url('ketarunaan/lowongan/perusahaan') ?>" class="text-decoration-none fw-semibold" style="font-size: 0.75rem;">Kelola Mitra</a>
                            </div>
                            <select name="perusahaan_id" id="perusahaan_id" class="form-select ecc-control-sm js-low-select2" required>
                                <option value="">Pilih Perusahaan</option>
                                <?php foreach (($refs['perusahaan'] ?? []) as $pm): ?>
                                    <option value="<?= (int) $pm['id'] ?>" <?= (string) old('perusahaan_id', $low['perusahaan_id'] ?? '') === (string) $pm['id'] ? 'selected' : '' ?>>
                                        <?= esc($pm['nama_perusahaan']) ?><?= (int) $pm['is_verified'] === 1 ? ' (Kontak diperiksa)' : ' (Belum diperiksa)' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="jenis_pekerjaan" class="form-label fw-semibold ecc-text-muted-aa small">Jenis Pekerjaan <span class="text-danger">*</span></label>
                            <select name="jenis_pekerjaan" id="jenis_pekerjaan" class="form-select ecc-control-sm" required>
                                <?php foreach (($refs['jenis_catalog'] ?? []) as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= old('jenis_pekerjaan', $low['jenis_pekerjaan'] ?? 'Tetap') === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="jenjang_pendidikan" class="form-label fw-semibold ecc-text-muted-aa small">Jenjang Minimum</label>
                            <select name="jenjang_pendidikan" id="jenjang_pendidikan" class="form-select ecc-control-sm">
                                <?php foreach (['D-III', 'D-IV', 'D-III / D-IV', 'S-1', 'Terbuka Semua Jenjang'] as $j): ?>
                                    <option value="<?= esc($j) ?>" <?= old('jenjang_pendidikan', $low['jenjang_pendidikan'] ?? 'D-III / D-IV') === $j ? 'selected' : '' ?>><?= esc($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="jumlah_kebutuhan" class="form-label fw-semibold ecc-text-muted-aa small">Jumlah Kebutuhan</label>
                            <input type="number" min="1" name="jumlah_kebutuhan" id="jumlah_kebutuhan" class="form-control ecc-control-sm ecc-num"
                                   value="<?= esc(old('jumlah_kebutuhan', $low['jumlah_kebutuhan'] ?? '')) ?>" placeholder="Opsional">
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="lokasi" class="form-label fw-semibold ecc-text-muted-aa small">Lokasi Penempatan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control ecc-control-sm" required
                                   value="<?= esc(old('lokasi', $low['lokasi'] ?? '')) ?>" placeholder="Contoh: Bandung / DKI Jakarta">
                        </div>
                        <div class="col-6 col-md-4">
                            <label for="tahun_lulus_sasaran" class="form-label fw-semibold ecc-text-muted-aa small">Tahun Lulus Sasaran</label>
                            <input type="text" name="tahun_lulus_sasaran" id="tahun_lulus_sasaran" class="form-control ecc-control-sm ecc-num"
                                   value="<?= esc(old('tahun_lulus_sasaran', $low['tahun_lulus_sasaran'] ?? '2024 - 2026')) ?>" placeholder="Contoh: 2024 - 2026">
                        </div>
                        <div class="col-12">
                            <span class="form-label fw-semibold ecc-text-muted-aa small d-block">Program Studi Sasaran <span class="text-danger">*</span></span>
                            <div class="d-flex flex-wrap gap-3 p-3 rounded-3 bg-light border">
                                <?php foreach (($refs['prodi'] ?? []) as $pr): ?>
                                    <?php $pid = (int) $pr['id']; ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="prodi_ids[]" value="<?= $pid ?>"
                                               id="prodi_chk_<?= $pid ?>" <?= in_array($pid, $selectedProdis, true) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="prodi_chk_<?= $pid ?>">
                                            <strong><?= esc($pr['kode_prodi']) ?></strong> — <?= esc($pr['nama_prodi']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seksi 2: Deskripsi dan Persyaratan -->
                <div class="ecc-low-card mb-3">
                    <h2 class="h6 fw-bold text-dark mb-3">Deskripsi dan Persyaratan</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="deskripsi" class="form-label fw-semibold ecc-text-muted-aa small">Ringkasan Pekerjaan <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control rounded-3" required
                                      placeholder="Uraian tugas utama posisi kerja"><?= esc(old('deskripsi', $low['deskripsi'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="persyaratan" class="form-label fw-semibold ecc-text-muted-aa small">Persyaratan Kompetensi <span class="text-danger">*</span></label>
                            <textarea name="persyaratan" id="persyaratan" rows="3" class="form-control rounded-3" required
                                      placeholder="Kualifikasi akademik, kompetensi teknis, dan syarat umum"><?= esc(old('persyaratan', $low['persyaratan'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="pengalaman_minimum" class="form-label fw-semibold ecc-text-muted-aa small">Pengalaman Minimum</label>
                            <input type="text" name="pengalaman_minimum" id="pengalaman_minimum" class="form-control ecc-control-sm"
                                   value="<?= esc(old('pengalaman_minimum', $low['pengalaman_minimum'] ?? 'Lulusan baru / maksimal 1 tahun')) ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="sertifikasi_opsional" class="form-label fw-semibold ecc-text-muted-aa small">Sertifikasi Khusus (Opsional)</label>
                            <input type="text" name="sertifikasi_opsional" id="sertifikasi_opsional" class="form-control ecc-control-sm"
                                   value="<?= esc(old('sertifikasi_opsional', $low['sertifikasi_opsional'] ?? '')) ?>" placeholder="Contoh: Sertifikat K3 / Penguji">
                        </div>
                    </div>
                </div>

                <!-- Seksi 3: Kanal Lamaran dan Sumber Informasi -->
                <div class="ecc-low-card">
                    <h2 class="h6 fw-bold text-dark mb-3">Kanal Lamaran &amp; Asal Informasi</h2>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="batas_pendaftaran" class="form-label fw-semibold ecc-text-muted-aa small">Batas Lamaran <span class="text-danger">*</span></label>
                            <input type="text" name="batas_pendaftaran" id="batas_pendaftaran" class="form-control ecc-control-sm ecc-num js-low-date" required
                                   value="<?= esc($batasRaw) ?>" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="metode_lamaran" class="form-label fw-semibold ecc-text-muted-aa small">Metode Lamaran <span class="text-danger">*</span></label>
                            <select name="metode_lamaran" id="metode_lamaran" class="form-select ecc-control-sm" required>
                                <?php foreach (($refs['metode_catalog'] ?? []) as $mk => $mlbl): ?>
                                    <option value="<?= esc($mk) ?>" <?= $metodeVal === $mk ? 'selected' : '' ?>><?= esc($mlbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="tautan_pendaftaran" class="form-label fw-semibold ecc-text-muted-aa small">
                                Tautan Pendaftaran Perusahaan <span class="text-danger" id="req_tautan">*</span>
                            </label>
                            <input type="url" name="tautan_pendaftaran" id="tautan_pendaftaran" class="form-control ecc-control-sm"
                                   value="<?= esc(old('tautan_pendaftaran', $low['tautan_pendaftaran'] ?? '')) ?>" placeholder="https://karir.perusahaan.co.id/...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email_lamaran" class="form-label fw-semibold ecc-text-muted-aa small">
                                Alamat Email Rekrutmen <span class="text-danger d-none" id="req_email">*</span>
                            </label>
                            <input type="email" name="email_lamaran" id="email_lamaran" class="form-control ecc-control-sm"
                                   value="<?= esc(old('email_lamaran', $low['email_lamaran'] ?? '')) ?>" placeholder="rekrutmen@perusahaan.co.id">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="sumber_informasi" class="form-label fw-semibold ecc-text-muted-aa small">Sumber Informasi (Internal)</label>
                            <input type="text" name="sumber_informasi" id="sumber_informasi" class="form-control ecc-control-sm"
                                   value="<?= esc(old('sumber_informasi', $low['sumber_informasi'] ?? '')) ?>" placeholder="Contoh: Surat Resmi HRD / Portal Mitra">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="tanggal_diterima" class="form-label fw-semibold ecc-text-muted-aa small">Tanggal Diterima</label>
                            <input type="text" name="tanggal_diterima" id="tanggal_diterima" class="form-control ecc-control-sm ecc-num js-low-date"
                                   value="<?= esc($tglTerima) ?>" placeholder="YYYY-MM-DD">
                        </div>
                        <div class="col-6 col-md-3">
                            <label for="pic_pengelola" class="form-label fw-semibold ecc-text-muted-aa small">PIC Pengelola</label>
                            <input type="text" name="pic_pengelola" id="pic_pengelola" class="form-control ecc-control-sm"
                                   value="<?= esc(old('pic_pengelola', $low['pic_pengelola'] ?? (session()->get('nama_lengkap') ?: 'PIC Karir & Alumni'))) ?>">
                        </div>
                        <div class="col-12">
                            <label for="catatan_sumber" class="form-label fw-semibold ecc-text-muted-aa small">Catatan Asal Informasi (Khusus Staf)</label>
                            <textarea name="catatan_sumber" id="catatan_sumber" rows="2" class="form-control rounded-3"
                                      placeholder="Catatan konfirmasi atau keterangan internal (tidak tampil ke alumni)"><?= esc(old('catatan_sumber', $low['catatan_sumber'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Status Lowongan -->
            <div class="col-12 col-lg-4">
                <div class="ecc-low-card position-sticky" style="top: 24px;">
                    <h2 class="h6 fw-bold text-dark mb-2">Status Lowongan</h2>
                    <?php if ($isEdit): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <?= ketarunaan_validasi_badge((string) ($low['status_validasi'] ?? 'draf')) ?>
                            <?= ketarunaan_lowongan_publikasi_badge($low) ?>
                        </div>
                    <?php endif; ?>
                    <p class="ecc-text-muted-aa mb-3 small">
                        Lowongan tidak ditayangkan otomatis sebelum diperiksa validator dan diterbitkan terpisah.
                    </p>
                    <input type="hidden" name="action_mode" id="action_mode" value="draf">
                    <div class="d-grid gap-2">
                        <button type="submit" onclick="document.getElementById('action_mode').value='ajukan';" class="btn btn-primary ecc-control-sm fw-semibold btn-tactile low-touch-ctrl">
                            Ajukan Verifikasi
                        </button>
                        <button type="submit" onclick="document.getElementById('action_mode').value='draf';" class="btn btn-outline-secondary ecc-control-sm fw-semibold btn-tactile low-touch-ctrl">
                            Simpan Draf
                        </button>
                        <a href="<?= esc($isEdit ? base_url('ketarunaan/lowongan/detail/' . (int) $low['id']) : base_url('ketarunaan/lowongan')) ?>" class="btn btn-light border ecc-control-sm fw-semibold d-flex align-items-center justify-content-center btn-tactile low-touch-ctrl">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/lowongan/_scripts_common') ?>
<script>
(function () {
    var selMetode = document.getElementById('metode_lamaran');
    var inpTautan = document.getElementById('tautan_pendaftaran');
    var inpEmail  = document.getElementById('email_lamaran');
    var reqTautan = document.getElementById('req_tautan');
    var reqEmail  = document.getElementById('req_email');

    if (selMetode && inpTautan && inpEmail) {
        var syncMetode = function () {
            var val = selMetode.value;
            var needTautan = (val === 'tautan_eksternal' || val === 'keduanya');
            var needEmail  = (val === 'email' || val === 'keduanya');
            inpTautan.required = needTautan;
            inpEmail.required  = needEmail;
            if (reqTautan) {
                reqTautan.classList.toggle('d-none', !needTautan);
            }
            if (reqEmail) {
                reqEmail.classList.toggle('d-none', !needEmail);
            }
        };
        selMetode.addEventListener('change', syncMetode);
        syncMetode();
    }
})();
</script>
<?= $this->endSection() ?>
