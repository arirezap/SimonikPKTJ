<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?><?= $program ? 'Ubah Program Beasiswa' : 'Tambah Program Beasiswa' ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/beasiswa/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$program  = $program ?? null;
$refs     = $refs ?? [];
$jenisVal = old('jenis_beasiswa', $program['jenis_beasiswa'] ?? 'apresiasi');
$aktifTa  = (string) ($refs['aktif_tahun_akademik_id'] ?? '');
?>
<div class="container-fluid px-0">
    <?= $this->include('ketarunaan/beasiswa/_nav_tabs') ?>

    <div class="mb-3">
        <a href="<?= base_url('ketarunaan/beasiswa/program') ?>" class="btn btn-light border btn-sm rounded-pill px-3 bea-touch-ctrl d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali ke Program
        </a>
    </div>

    <div class="ecc-bea-card bea-form-scope">
        <h2 class="h5 fw-bold text-dark mb-1"><?= $program ? 'Ubah Program Beasiswa' : 'Tambah Program Beasiswa' ?></h2>
        <p class="ecc-text-muted-aa mb-4 small">
            Jenis beasiswa dipisahkan dari sumber dana atau pemberi. Atur mekanisme seleksi Apresiasi bila memilih jenis Apresiasi.
        </p>

        <form id="formProgramBeasiswa" method="post" action="<?= base_url('ketarunaan/beasiswa/program/simpan') ?>" data-lock-submit="true">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) ($program['id'] ?? 0) ?>">

            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label for="nama_program" class="form-label fw-semibold">Nama Program Beasiswa <span class="text-danger">*</span></label>
                    <input type="text" id="nama_program" name="nama_program" class="form-control ecc-control-sm" required maxlength="150"
                           value="<?= esc((string) old('nama_program', $program['nama_program'] ?? '')) ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label for="jenis_beasiswa" class="form-label fw-semibold">Jenis Beasiswa Resmi <span class="text-danger">*</span></label>
                    <select id="jenis_beasiswa" name="jenis_beasiswa" class="form-select ecc-control-sm" required>
                        <?php foreach (ketarunaan_jenis_beasiswa_catalog() as $k => $m): ?>
                            <option value="<?= esc($k) ?>" <?= $jenisVal === $k ? 'selected' : '' ?>>
                                <?= esc($m['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="pemberi" class="form-label fw-semibold">Pemberi / Sumber Dana <span class="text-danger">*</span></label>
                    <input type="text" id="pemberi" name="pemberi" class="form-control ecc-control-sm" required maxlength="150"
                           value="<?= esc((string) old('pemberi', $program['pemberi'] ?? '')) ?>" placeholder="Contoh: Kementerian Perhubungan / PKTJ Tegal / Mitra Industri">
                </div>
                <div class="col-12 col-md-3">
                    <label for="tahun_akademik_id" class="form-label fw-semibold">Periode Akademik <span class="text-danger">*</span></label>
                    <select id="tahun_akademik_id" name="tahun_akademik_id" class="form-select ecc-control-sm" required>
                        <?php $selTahun = (string) old('tahun_akademik_id', (string) ($program['tahun_akademik_id'] ?? $aktifTa)); ?>
                        <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                            <option value="<?= (int) $ta['id'] ?>" <?= (string) $ta['id'] === $selTahun ? 'selected' : '' ?>>
                                <?= esc($ta['kode_tahun']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="status_program" class="form-label fw-semibold">Status Pelaksanaan <span class="text-danger">*</span></label>
                    <?php $selStat = (string) old('status_program', $program['status_program'] ?? 'dibuka'); ?>
                    <select id="status_program" name="status_program" class="form-select ecc-control-sm" required>
                        <?php foreach (['dibuka' => 'Dibuka', 'seleksi' => 'Seleksi', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai'] as $sk => $sl): ?>
                            <option value="<?= esc($sk) ?>" <?= $selStat === $sk ? 'selected' : '' ?>><?= esc($sl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label for="kuota" class="form-label fw-semibold">Kuota Penerima <span class="text-danger">*</span></label>
                    <input type="number" id="kuota" name="kuota" class="form-control ecc-control-sm ecc-num" min="0" required
                           value="<?= esc((string) old('kuota', $program['kuota'] ?? 15)) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label for="nominal" class="form-label fw-semibold">Nominal per Penerima (Rp)</label>
                    <input type="number" id="nominal" name="nominal" class="form-control ecc-control-sm ecc-num" min="0"
                           value="<?= esc((string) old('nominal', $program['nominal'] ?? 2500000)) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label for="periode_mulai" class="form-label fw-semibold">Periode Mulai</label>
                    <input type="text" id="periode_mulai" name="periode_mulai" class="form-control ecc-control-sm ecc-num bg-white js-bea-date"
                           placeholder="Pilih tanggal..."
                           value="<?= esc((string) old('periode_mulai', $program['periode_mulai'] ?? '')) ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label for="periode_selesai" class="form-label fw-semibold">Periode Selesai</label>
                    <input type="text" id="periode_selesai" name="periode_selesai" class="form-control ecc-control-sm ecc-num bg-white js-bea-date"
                           placeholder="Pilih tanggal..."
                           value="<?= esc((string) old('periode_selesai', $program['periode_selesai'] ?? '')) ?>">
                </div>

                <div class="col-12 col-md-4">
                    <label for="penanggung_jawab" class="form-label fw-semibold">Unit Penanggung Jawab</label>
                    <input type="text" id="penanggung_jawab" name="penanggung_jawab" class="form-control ecc-control-sm"
                           value="<?= esc((string) old('penanggung_jawab', $program['penanggung_jawab'] ?? 'Unit Ketarunaan')) ?>">
                </div>
                <div class="col-12 col-md-8">
                    <label for="persyaratan" class="form-label fw-semibold">Ringkasan Persyaratan Program</label>
                    <textarea id="persyaratan" name="persyaratan" rows="2" class="form-control rounded-3"><?= esc((string) old('persyaratan', $program['persyaratan'] ?? '')) ?></textarea>
                </div>

                <!-- Pengaturan Khusus Beasiswa Apresiasi -->
                <div class="col-12 <?= $jenisVal === 'apresiasi' ? '' : 'd-none' ?>" id="panelApresiasiConfig">
                    <div class="p-3 rounded-4 border" style="background-color: #f8fafc;">
                        <h3 class="h6 fw-bold text-primary mb-2">Pengaturan Khusus Seleksi Beasiswa Apresiasi</h3>
                        <div class="row g-3">
                            <div class="col-12 col-md-3">
                                <label for="dasar_penilaian_info" class="form-label fw-semibold">Dasar Penilaian</label>
                                <input type="text" id="dasar_penilaian_info" class="form-control ecc-control-sm bg-white" value="IP Semester (Bukan IPK)" readonly>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="periode_nilai_id" class="form-label fw-semibold">Periode Sumber Nilai IP</label>
                                <?php $selPerNilai = (string) old('periode_nilai_id', (string) ($program['periode_nilai_id'] ?? $aktifTa)); ?>
                                <select id="periode_nilai_id" name="periode_nilai_id" class="form-select ecc-control-sm">
                                    <?php foreach (($refs['tahun_akademik'] ?? []) as $ta): ?>
                                        <option value="<?= (int) $ta['id'] ?>" <?= (string) $ta['id'] === $selPerNilai ? 'selected' : '' ?>>
                                            <?= esc($ta['kode_tahun']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="jumlah_peringkat" class="form-label fw-semibold">Peringkat / Kelas</label>
                                <input type="number" id="jumlah_peringkat" name="jumlah_peringkat" class="form-control ecc-control-sm ecc-num" min="1" max="15"
                                       value="<?= esc((string) old('jumlah_peringkat', $program['jumlah_peringkat'] ?? 5)) ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="aturan_seri" class="form-label fw-semibold">Aturan Pemecah Seri Peringkat</label>
                                <?php $selSeri = (string) old('aturan_seri', $program['aturan_seri'] ?? 'belum_ditetapkan'); ?>
                                <select id="aturan_seri" name="aturan_seri" class="form-select ecc-control-sm">
                                    <?php foreach (ketarunaan_aturan_seri_catalog() as $sk => $sl): ?>
                                        <option value="<?= esc($sk) ?>" <?= $selSeri === $sk ? 'selected' : '' ?>><?= esc($sl) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?= base_url('ketarunaan/beasiswa/program') ?>" class="btn btn-light border rounded-pill px-4 bea-touch-ctrl d-inline-flex align-items-center">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 btn-tactile bea-touch-ctrl">
                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i> Simpan Program
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('ketarunaan/beasiswa/_scripts_common') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var jenisSelect = document.getElementById('jenis_beasiswa');
    var panelApr    = document.getElementById('panelApresiasiConfig');
    if (jenisSelect && panelApr) {
        jenisSelect.addEventListener('change', function() {
            panelApr.classList.toggle('d-none', this.value !== 'apresiasi');
        });
    }

    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        document.querySelectorAll('.js-bea-date').forEach(function(el) {
            flatpickr(el, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                allowInput: true
            });
        });
    }

    var form = document.getElementById('formProgramBeasiswa');
    if (form) {
        form.addEventListener('submit', function(e) {
            var nama    = (document.getElementById('nama_program')?.value || '').trim();
            var pemberi = (document.getElementById('pemberi')?.value || '').trim();
            var mulai   = (document.getElementById('periode_mulai')?.value || '').trim();
            var selesai = (document.getElementById('periode_selesai')?.value || '').trim();

            if (nama === '' || pemberi === '') {
                e.preventDefault();
                window.eccBeaShowMessage('Data Belum Lengkap', 'Nama program dan sumber dana wajib diisi.');
                return;
            }
            if (mulai !== '' && selesai !== '' && selesai < mulai) {
                e.preventDefault();
                window.eccBeaShowMessage('Tanggal Tidak Valid', 'Tanggal selesai periode tidak boleh lebih awal dari tanggal mulai.');
                return;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
