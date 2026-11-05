<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Ajukan Perubahan Status - <?= esc($taruna['nama_lengkap']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-profil-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .step-circle-ecc {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .upload-dropzone-ecc {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 24px;
        background-color: #f8fafc;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $nomorTaruna = (string) $taruna['nomor_taruna']; ?>
<div class="container-fluid px-0">

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-exclamation-circle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- 1. BREADCRUMB & HEADER -->
    <div class="mb-3">
        <div class="text-muted small mb-2">
            <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">ECC 2.0</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna)) ?>" class="text-decoration-none text-muted">Profil Taruna</a>
            <span class="mx-1">›</span>
            <span class="text-dark fw-medium">Ajukan Perubahan Status</span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1">Ajukan Perubahan Status</h1>
        <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status') ?>" class="text-decoration-none small text-primary fw-semibold d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Riwayat Status</span>
        </a>
    </div>

    <!-- 2. RINGKASAN IDENTITAS TARUNA -->
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-profil-lg">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold text-dark mb-1"><?= esc($taruna['nama_lengkap']) ?></h2>
                <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                    <span>Nomor Taruna <strong class="text-primary tabular-nums"><?= esc($nomorTaruna) ?></strong></span>
                    <span>•</span>
                    <strong class="text-primary"><?= esc($taruna['kode_prodi'] ?? 'RSTJ') ?></strong>
                    <span>•</span>
                    <span>Angkatan <strong class="text-primary tabular-nums"><?= esc($taruna['tahun_masuk'] ?? '2024') ?></strong></span>
                    <span>•</span>
                    <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FORMULIR PENGAJUAN PERUBAHAN STATUS -->
    <div class="bento-card p-3 p-md-4">
        <form method="post" action="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <h5 class="fw-bold text-dark mb-1">Form Pengajuan Perubahan Status</h5>
                    <p class="text-muted small mb-3">Ajukan perubahan status taruna sesuai dengan kondisi dan dokumen pendukung.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status Saat Ini</label>
                            <div class="form-control bg-light d-flex align-items-center" style="height: 40px;">
                                <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Status Baru <span class="text-danger">*</span></label>
                            <select name="kode_status" id="selectStatusBaru" class="form-select" required style="height: 40px;">
                                <?php foreach ($status_catalog as $kode => $meta): ?>
                                    <option value="<?= esc($kode) ?>" <?= old('kode_status', 'cuti') === $kode ? 'selected' : '' ?>>
                                        <?= esc($meta['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="text" id="inputMulaiBerlaku" name="mulai_berlaku" class="form-control bg-white" value="<?= esc(old('mulai_berlaku', '2026-11-01')) ?>" required style="height: 40px;">
                        </div>

                        <div class="col-md-6" id="wrapRencanaKembali">
                            <label class="form-label small fw-semibold">Rencana Kembali <span class="text-danger" id="reqRencanaKembali">*</span></label>
                            <input type="text" id="inputRencanaKembali" name="rencana_kembali" class="form-control bg-white" value="<?= esc(old('rencana_kembali', '2027-02-01')) ?>" style="height: 40px;">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alasan Perubahan <span class="text-danger">*</span></label>
                            <textarea name="alasan_perubahan" class="form-control" rows="2" placeholder="Tuliskan alasan perubahan status secara singkat dan jelas" required><?= esc(old('alasan_perubahan', 'Keperluan keluarga')) ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nomor Keputusan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_keputusan" class="form-control" value="<?= esc(old('nomor_keputusan', 'SK/030/2026')) ?>" placeholder="Contoh: SK/030/2026" required style="height: 40px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Keputusan <span class="text-danger">*</span></label>
                            <input type="text" id="inputTanggalKeputusan" name="tanggal_keputusan" class="form-control bg-white" value="<?= esc(old('tanggal_keputusan', '2026-10-30')) ?>" required style="height: 40px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tahun Akademik <span class="text-danger">*</span></label>
                            <select name="tahun_akademik_id" class="form-select" style="height: 40px;">
                                <?php foreach ($tahun_akademik_list as $ta): ?>
                                    <option value="<?= esc($ta['id']) ?>" <?= (int) ($ta['is_aktif'] ?? 0) === 1 ? 'selected' : '' ?>>
                                        <?= esc($ta['kode_tahun']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select tabular-nums" style="height: 40px;">
                                <?php for ($s = 1; $s <= 8; $s++): ?>
                                    <option value="<?= $s ?>" <?= (int) ($taruna['semester_aktif'] ?? 5) === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 d-flex flex-column gap-3">
                    <!-- Proses Pengajuan (3 Tahap) -->
                    <div class="p-3 p-md-4 rounded-4 border">
                        <h6 class="fw-bold text-dark mb-3">Proses Pengajuan</h6>

                        <div class="d-flex align-items-center justify-content-between text-center mb-3 px-2">
                            <div>
                                <div class="step-circle-ecc bg-primary text-white mb-1">1</div>
                                <div class="small fw-semibold text-primary">Draf</div>
                            </div>
                            <div class="flex-grow-1 border-top mx-2"></div>
                            <div>
                                <div class="step-circle-ecc bg-light text-muted border mb-1">2</div>
                                <div class="small text-muted">Diajukan</div>
                            </div>
                            <div class="flex-grow-1 border-top mx-2"></div>
                            <div>
                                <div class="step-circle-ecc bg-light text-muted border mb-1">3</div>
                                <div class="small text-muted">Tervalidasi</div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 small" style="background-color: #eff6ff; color: #1e40af;">
                            Status berubah setelah validasi dan tanggal efektif tercapai.
                        </div>
                    </div>

                    <!-- Dokumen Pendukung -->
                    <div class="p-3 p-md-4 rounded-4 border flex-grow-1">
                        <label class="form-label small fw-semibold mb-2">Dokumen Pendukung <span class="text-danger">*</span></label>
                        <div class="upload-dropzone-ecc">
                            <i class="bi bi-cloud-arrow-up text-primary fs-2 d-block mb-2"></i>
                            <label for="inputBuktiDokumen" class="btn btn-outline-primary btn-sm rounded-3 px-3 fw-semibold d-inline-flex align-items-center gap-2 cursor-pointer" style="height: 36px;">
                                <i class="bi bi-upload"></i>
                                <span>Unggah Keputusan</span>
                            </label>
                            <input type="file" id="inputBuktiDokumen" name="bukti_dokumen" accept="application/pdf" class="d-none">
                            <div class="text-muted small mt-2" id="lblNamaBerkasKeputusan">PDF Maks 5 MB</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-4 mt-4 border-top">
                <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status') ?>" class="btn btn-light rounded-3 px-4 border fw-semibold d-inline-flex align-items-center" style="height: 40px;">
                    Batal
                </a>
                <?php if ($can_manage): ?>
                    <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold btn-tactile" style="height: 40px;">
                        Simpan Draf
                    </button>
                    <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold btn-tactile shadow-sm" style="height: 40px;">
                        Ajukan Validasi
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#inputMulaiBerlaku", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y"
        });
        flatpickr("#inputRencanaKembali", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y"
        });
        flatpickr("#inputTanggalKeputusan", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y"
        });

        const selectStatus = document.getElementById('selectStatusBaru');
        const reqBadge = document.getElementById('reqRencanaKembali');
        function toggleRencanaKembali() {
            const val = selectStatus.value;
            const wajib = (val === 'cuti' || val === 'skorsing');
            if (reqBadge) {
                reqBadge.style.display = wajib ? 'inline' : 'none';
            }
        }
        if (selectStatus) {
            selectStatus.addEventListener('change', toggleRencanaKembali);
            toggleRencanaKembali();
        }

        const inputDoc = document.getElementById('inputBuktiDokumen');
        const lblDoc = document.getElementById('lblNamaBerkasBerkas') || document.getElementById('lblNamaBerkasKeputusan');
        if (inputDoc && lblDoc) {
            inputDoc.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Berkas Terlalu Besar',
                            text: 'Pilih dokumen PDF maksimal 5 MB.'
                        });
                    } else {
                        alert('Pilih dokumen PDF maksimal 5 MB.');
                    }
                    this.value = '';
                    return;
                }
                lblDoc.textContent = file.name;
                lblDoc.classList.replace('text-muted', 'text-primary');
            });
        }
    });
</script>
<?= $this->endSection() ?>

