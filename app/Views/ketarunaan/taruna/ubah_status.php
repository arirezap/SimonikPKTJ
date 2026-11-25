<?= $this->extend('layouts/ecc2_layout') ?>
<?php
    $draftRow            = $draft_row ?? null;
    $isEditDraft         = is_array($draftRow) && !empty($draftRow['id']);
    $hasExistingDraftPdf = $isEditDraft && !empty($draftRow['bukti_dokumen']);
?>

<?= $this->section('title') ?><?= $isEditDraft ? 'Ubah Draf Perubahan Status' : 'Ajukan Perubahan Status' ?> - <?= esc($taruna['nama_lengkap']) ?><?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-profil-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background-color: #eff6ff;
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
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
    @media (max-width: 767.98px) {
        .taruna-touch-ctrl {
            min-height: 44px !important;
        }
        #formUbahStatus .form-control,
        #formUbahStatus .form-select {
            font-size: 16px !important;
            min-height: 44px !important;
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .btn-tactile {
            transition: none !important;
            transform: none !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $nomorTaruna   = (string) $taruna['nomor_taruna'];
    $tingkatRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
    $fotoPath      = !empty($taruna['foto']) && file_exists(FCPATH . 'assets/uploads/taruna/' . $taruna['foto'])
        ? base_url('assets/uploads/taruna/' . $taruna['foto'])
        : null;
?>
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
            <a href="<?= site_url('ketarunaan/dashboard') ?>" class="text-decoration-none text-muted">Ketarunaan</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna') ?>" class="text-decoration-none text-muted">Database Taruna</a>
            <span class="mx-1">›</span>
            <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna)) ?>" class="text-decoration-none text-muted">Profil Taruna</a>
            <span class="mx-1">›</span>
            <span class="text-dark fw-medium"><?= $isEditDraft ? 'Ubah Draf Status' : 'Ajukan Perubahan Status' ?></span>
        </div>
        <h1 class="h3 fw-bold text-dark mb-1"><?= $isEditDraft ? 'Ubah Draf Perubahan Status' : 'Ajukan Perubahan Status' ?></h1>
        <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status') ?>" class="text-decoration-none small fw-semibold d-inline-flex align-items-center gap-1" style="color: #1e40af;">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Riwayat Status</span>
        </a>
    </div>

    <!-- 2. RINGKASAN IDENTITAS TARUNA -->
    <div class="bento-card p-3 p-md-4 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-profil-lg">
                <?php if ($fotoPath): ?>
                    <img src="<?= esc($fotoPath) ?>" alt="Foto <?= esc($taruna['nama_lengkap']) ?>" width="64" height="64" decoding="async" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="bi bi-person-fill"></i>
                <?php endif; ?>
            </div>
            <div>
                <h2 class="h4 fw-bold text-dark mb-1"><?= esc($taruna['nama_lengkap']) ?></h2>
                <div class="d-flex flex-wrap align-items-center column-gap-3 row-gap-1 small text-muted">
                    <span>Nomor Taruna <strong class="tabular-nums" style="color: #1e40af;"><?= esc($nomorTaruna) ?></strong></span>
                    <strong style="color: #1e40af;"><?= esc($taruna['kode_prodi'] ?: '-') ?></strong>
                    <span>Angkatan <strong class="tabular-nums" style="color: #1e40af;"><?= esc(ketarunaan_format_angkatan($taruna)) ?></strong></span>
                    <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. FORMULIR PENGAJUAN PERUBAHAN STATUS -->
    <div class="bento-card p-3 p-md-4">
        <form id="formUbahStatus" method="post" action="<?= site_url('ketarunaan/taruna/ubah-status/' . urlencode($nomorTaruna)) ?>">
            <?= csrf_field() ?>
            <?php if ($isEditDraft): ?>
                <input type="hidden" name="draft_id" value="<?= (int) $draftRow['id'] ?>">
            <?php endif; ?>
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <h2 class="h5 fw-bold text-dark mb-1"><?= $isEditDraft ? 'Lengkapi Draf Perubahan Status' : 'Form Pengajuan Perubahan Status' ?></h2>
                    <p class="text-muted small mb-3">Ajukan perubahan status taruna sesuai dengan kondisi dan dokumen pendukung.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="form-label small fw-semibold d-block">Status Saat Ini</span>
                            <div class="form-control bg-light d-flex align-items-center" style="height: 40px;">
                                <?= ketarunaan_status_badge((string) $taruna['status_terkini']) ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="selectStatusBaru" class="form-label small fw-semibold">Status Baru <span class="text-danger">*</span></label>
                            <?php $valStatus = (string) old('kode_status', $draftRow['kode_status'] ?? ''); ?>
                            <select name="kode_status" id="selectStatusBaru" class="form-select" required style="height: 40px;">
                                <option value="">-- Pilih Status Baru --</option>
                                <?php foreach ($status_catalog as $kode => $meta): ?>
                                    <?php $isCurrentStatus = strtolower((string) ($taruna['status_terkini'] ?? '')) === strtolower((string) $kode); ?>
                                    <option value="<?= esc($kode) ?>" <?= $isCurrentStatus ? 'disabled' : ($valStatus === $kode ? 'selected' : '') ?>>
                                        <?= esc($meta['label']) ?><?= $isCurrentStatus ? ' (Status Saat Ini)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="inputMulaiBerlaku" class="form-label small fw-semibold">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="text" id="inputMulaiBerlaku" name="mulai_berlaku" class="form-control bg-white" value="<?= esc(old('mulai_berlaku', $draftRow['mulai_berlaku'] ?? date('Y-m-d'))) ?>" placeholder="Pilih tanggal mulai berlaku" style="height: 40px;">
                        </div>

                        <div class="col-md-6" id="wrapRencanaKembali">
                            <label for="inputRencanaKembali" class="form-label small fw-semibold">Rencana Kembali <span class="text-danger" id="reqRencanaKembali">*</span></label>
                            <input type="text" id="inputRencanaKembali" name="rencana_kembali" class="form-control bg-white" value="<?= esc(old('rencana_kembali', $draftRow['rencana_kembali'] ?? '')) ?>" placeholder="Pilih tanggal rencana kembali" style="height: 40px;">
                        </div>

                        <div class="col-12">
                            <label for="inputAlasanPerubahan" class="form-label small fw-semibold">Alasan Perubahan <span class="text-danger">*</span></label>
                            <textarea id="inputAlasanPerubahan" name="alasan_perubahan" class="form-control" rows="2" placeholder="Tuliskan alasan perubahan status secara singkat dan jelas" required><?= esc(old('alasan_perubahan', $draftRow['alasan_perubahan'] ?? '')) ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="inputNomorKeputusan" class="form-label small fw-semibold">Nomor Keputusan <span class="text-muted fw-normal">(wajib saat diajukan)</span></label>
                            <input type="text" id="inputNomorKeputusan" name="nomor_keputusan" class="form-control" value="<?= esc(old('nomor_keputusan', $draftRow['nomor_keputusan'] ?? '')) ?>" placeholder="Contoh: KP-PKTJ 373 TAHUN 2026" style="height: 40px;">
                        </div>

                        <div class="col-md-6">
                            <label for="inputTanggalKeputusan" class="form-label small fw-semibold">Tanggal Keputusan <span class="text-muted fw-normal">(wajib saat diajukan)</span></label>
                            <input type="text" id="inputTanggalKeputusan" name="tanggal_keputusan" class="form-control bg-white" value="<?= esc(old('tanggal_keputusan', $draftRow['tanggal_keputusan'] ?? '')) ?>" placeholder="Pilih tanggal keputusan" style="height: 40px;">
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-light bg-opacity-50 d-flex flex-wrap align-items-center justify-content-between gap-2 small">
                                <span class="text-muted">Posisi Pendidikan Terdaftar</span>
                                <span class="fw-semibold text-dark tabular-nums">
                                    <?= esc(!empty($taruna['nama_tahun_akademik']) ? $taruna['nama_tahun_akademik'] : '-') ?> •
                                    Tingkat <?= esc($tingkatRomawi[(int) ($taruna['tingkat'] ?? 1)] ?? (string) ($taruna['tingkat'] ?? '-')) ?> /
                                    Semester <?= (int) ($taruna['semester_aktif'] ?? 1) ?> •
                                    <?= !empty($taruna['nama_kelas']) ? 'Kelas ' . esc($taruna['nama_kelas']) : 'Kelas belum diatur' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 d-flex flex-column gap-3">
                    <!-- Proses Pengajuan (3 Tahap) -->
                    <div class="p-3 p-md-4 rounded-4 border">
                        <h3 class="h6 fw-bold text-dark mb-3">Proses Pengajuan</h3>

                        <div class="d-flex align-items-center justify-content-between text-center mb-3 px-2">
                            <div>
                                <div class="step-circle-ecc bg-primary text-white mb-1 tabular-nums">1</div>
                                <div class="small fw-semibold" style="color: #1e40af;">Draf</div>
                            </div>
                            <div class="flex-grow-1 border-top mx-2"></div>
                            <div>
                                <div class="step-circle-ecc bg-light text-muted border mb-1 tabular-nums">2</div>
                                <div class="small text-muted">Diajukan</div>
                            </div>
                            <div class="flex-grow-1 border-top mx-2"></div>
                            <div>
                                <div class="step-circle-ecc bg-light text-muted border mb-1 tabular-nums">3</div>
                                <div class="small text-muted">Tervalidasi</div>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 small" style="background-color: #eff6ff; color: #1e40af;">
                            Status berubah setelah validasi dan tanggal efektif tercapai.
                        </div>
                    </div>

                    <!-- Tautan Dokumen SK -->
                    <div class="p-3 p-md-4 rounded-4 border flex-grow-1">
                        <?php
                            $draftDocRaw = $isEditDraft ? trim((string) ($draftRow['bukti_dokumen'] ?? '')) : '';
                            $isDraftHttpUrl = $draftDocRaw !== '' && ketarunaan_is_http_url($draftDocRaw, 2000);
                            $isDraftLegacyFile = $draftDocRaw !== '' && !$isDraftHttpUrl;
                            $valLinkDokumen = (string) old('bukti_dokumen', $isDraftHttpUrl ? $draftDocRaw : '');
                        ?>
                        <label for="inputBuktiDokumen" class="form-label small fw-semibold mb-2">Tautan Dokumen SK <span class="text-muted fw-normal">(wajib saat diajukan)</span></label>
                        <input type="url" id="inputBuktiDokumen" name="bukti_dokumen" class="form-control mb-2" maxlength="255" value="<?= esc($valLinkDokumen) ?>" placeholder="https://drive.google.com/..." style="height: 40px;">
                        <div class="small" style="color: #475569;">
                            Tempel tautan Google Drive atau repositori resmi SK (awalan <code>https://</code> atau <code>http://</code>).
                        </div>
                        <?php if ($isDraftLegacyFile): ?>
                            <div class="mt-2 pt-2 border-top small">
                                <a href="<?= site_url('ketarunaan/taruna/unduh-sk/' . (int) $draftRow['id'] . '?inline=1') ?>" target="_blank" rel="noopener noreferrer" class="fw-semibold text-decoration-none d-inline-flex align-items-center gap-1" style="color: #1e40af;">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>Lihat dokumen SK lama</span>
                                </a>
                                <div class="text-muted mt-1">Biarkan tautan kosong jika masih memakai dokumen ini, atau isi tautan baru.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 pt-4 mt-4 border-top">
                <a href="<?= site_url('ketarunaan/taruna/profil/' . urlencode($nomorTaruna) . '?tab=riwayat-status') ?>" class="btn btn-light rounded-3 px-4 border fw-semibold d-inline-flex align-items-center justify-content-center taruna-touch-ctrl" style="height: 40px;">
                    Batal
                </a>
                <?php if ($can_manage): ?>
                    <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile taruna-touch-ctrl" style="height: 40px;">
                        Simpan Draf
                    </button>
                    <button type="submit" name="aksi_submit" value="ajukan" class="btn btn-primary rounded-3 px-4 fw-semibold d-inline-flex align-items-center justify-content-center btn-tactile shadow-sm taruna-touch-ctrl" style="height: 40px;">
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
        const elMulai     = document.getElementById('inputMulaiBerlaku');
        const elRencana   = document.getElementById('inputRencanaKembali');
        const elKeputusan = document.getElementById('inputTanggalKeputusan');
        const fpLocale    = (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';

        function initDateInput(el, labelText) {
            if (!el || typeof flatpickr === 'undefined') return null;
            const fp = flatpickr(el, {
                locale: fpLocale,
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
            if (fp && fp.altInput) {
                fp.altInput.setAttribute('aria-label', labelText);
            }
            return fp;
        }

        const fpMulai     = initDateInput(elMulai, 'Tanggal Mulai Berlaku');
        const fpRencana   = initDateInput(elRencana, 'Tanggal Rencana Kembali');
        const fpKeputusan = initDateInput(elKeputusan, 'Tanggal Keputusan');

        const selectStatus = document.getElementById('selectStatusBaru');
        const wrapRencana  = document.getElementById('wrapRencanaKembali');
        const reqBadge     = document.getElementById('reqRencanaKembali');
        function toggleRencanaKembali() {
            const val = selectStatus ? selectStatus.value : '';
            const wajib = (val === 'cuti' || val === 'skorsing');
            if (wrapRencana) {
                wrapRencana.classList.toggle('d-none', !wajib);
            }
            if (reqBadge) {
                reqBadge.style.display = wajib ? 'inline' : 'none';
            }
            if (!wajib) {
                if (fpRencana) {
                    fpRencana.clear();
                } else if (elRencana) {
                    elRencana.value = '';
                }
            }
        }
        if (selectStatus) {
            selectStatus.addEventListener('change', toggleRencanaKembali);
            toggleRencanaKembali();
        }

        const inputDoc = document.getElementById('inputBuktiDokumen');

        function showFormWarn(title, text, focusEl) {
            if (focusEl) {
                focusEl.focus();
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: title, text: text });
            } else {
                alert(text);
            }
        }

        function isValidHttpUrl(val) {
            if (!val || val.length > 255) return false;
            try {
                const parsed = new URL(val);
                return parsed.protocol === 'http:' || parsed.protocol === 'https:';
            } catch (err) {
                return false;
            }
        }

        const elNomorKeputusan = document.getElementById('inputNomorKeputusan');
        const hasExistingDraftDoc = <?= $hasExistingDraftPdf ? 'true' : 'false' ?>;
        const formStatus = document.getElementById('formUbahStatus');
        if (formStatus) {
            formStatus.addEventListener('submit', function (e) {
                const submitter = e.submitter;
                const aksi = submitter ? submitter.value : 'ajukan';
                const statusVal = selectStatus ? selectStatus.value : '';
                const mulaiVal = elMulai ? elMulai.value.trim() : '';
                const rencanaVal = elRencana ? elRencana.value.trim() : '';
                const nomorVal = elNomorKeputusan ? elNomorKeputusan.value.trim() : '';
                const keputusanVal = elKeputusan ? elKeputusan.value.trim() : '';
                const docUrlVal = inputDoc ? inputDoc.value.trim() : '';

                if (!mulaiVal) {
                    e.preventDefault();
                    showFormWarn('Tanggal Belum Dipilih', 'Pilih tanggal mulai berlaku terlebih dahulu.', fpMulai ? fpMulai.altInput : null);
                    return;
                }

                if (statusVal === 'cuti' || statusVal === 'skorsing') {
                    if (!rencanaVal) {
                        e.preventDefault();
                        showFormWarn('Rencana Kembali Wajib Diisi', 'Pilih tanggal rencana kembali untuk status Cuti atau Skorsing.', fpRencana ? fpRencana.altInput : null);
                        return;
                    }
                    if (rencanaVal < mulaiVal) {
                        e.preventDefault();
                        showFormWarn('Tanggal Tidak Sesuai', 'Tanggal rencana kembali tidak boleh sebelum tanggal mulai berlaku.', fpRencana ? fpRencana.altInput : null);
                        return;
                    }
                }

                if (docUrlVal !== '' && !isValidHttpUrl(docUrlVal)) {
                    e.preventDefault();
                    showFormWarn('Tautan Tidak Valid', 'Tautan dokumen SK harus diawali http:// atau https:// (maks 255 karakter).', inputDoc);
                    return;
                }

                if (aksi !== 'draf') {
                    if (!nomorVal) {
                        e.preventDefault();
                        showFormWarn('Nomor Keputusan Wajib Diisi', 'Isi nomor keputusan SK sebelum mengajukan validasi.', elNomorKeputusan);
                        return;
                    }
                    if (!keputusanVal) {
                        e.preventDefault();
                        showFormWarn('Tanggal Keputusan Wajib Diisi', 'Pilih tanggal keputusan SK terlebih dahulu.', fpKeputusan ? fpKeputusan.altInput : null);
                        return;
                    }
                    if (docUrlVal === '' && !hasExistingDraftDoc) {
                        e.preventDefault();
                        showFormWarn('Tautan SK Wajib Diisi', 'Masukkan tautan dokumen keputusan (https://...) sebelum mengajukan validasi.', inputDoc);
                    }
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>

