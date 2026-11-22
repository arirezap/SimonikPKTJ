<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Penetapan dan Tindak Lanjut<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<?= $this->include('ketarunaan/pelanggaran/_styles') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$detail    = $detail ?? [];
$kasus     = $detail['kasus'] ?? [];
$refs      = $refs ?? [];
$canDecide = !empty($canDecide);
$idKasus   = (int) ($kasus['id'] ?? 0);

$existingPenetapan = (string) ($kasus['status_penetapan'] ?? 'dugaan');
$stPenetapan       = old('status_penetapan', $existingPenetapan);
$stPenyelesaian    = old('status_penyelesaian', (string) ($kasus['status_penyelesaian'] ?? 'proses'));
$rawValidasi       = (string) ($kasus['status_validasi'] ?? 'draf');
$stValidasi        = ($rawValidasi === 'perlu_perbaikan') ? 'dikembalikan' : $rawValidasi;
?>

<div class="container-fluid px-0">
    <?= view('ketarunaan/pelanggaran/_wizard_header', [
        'activeStep'   => 3,
        'pageTitle'    => 'Penetapan dan Tindak Lanjut',
        'pageSubtitle' => 'Menetapkan hasil pemeriksaan, sanksi, dan pemantauan penyelesaian kasus.',
        'kasus'        => $kasus,
    ]) ?>

    <div class="bento-card ecc-pl-card pl-form-scope">
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <h2 class="h6 fw-bold text-dark mb-1">Penetapan Hasil dan Tindak Lanjut</h2>
                <p class="text-secondary mb-3 small">Setiap perubahan status penetapan atau banding tersimpan terpisah tanpa menghapus keputusan sebelumnya.</p>

                <?php if (!$canDecide): ?>
                    <div class="alert alert-light border rounded-3 px-3 py-2 mb-3 small text-secondary">
                        Penetapan akhir (Terbukti, Tidak Terbukti, Dibatalkan) hanya dapat ditetapkan oleh pejabat berwenang. Anda tetap dapat memperbarui rencana tindak lanjut dan pemantauan.
                    </div>
                <?php endif; ?>

                <form id="formPenetapanKasus" method="post" action="<?= base_url('ketarunaan/pelanggaran/penetapan/' . $idKasus . '/simpan') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="selectPenetapan" class="form-label fw-semibold text-dark small">
                                Status Penetapan <span class="text-danger">*</span>
                            </label>
                            <select id="selectPenetapan" name="status_penetapan" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['penetapan_options'] ?? [] as $k => $lbl): ?>
                                    <?php
                                    $isFinalOption = in_array($k, ['terbukti', 'tidak_terbukti', 'dibatalkan'], true);
                                    $lockOption    = !$canDecide && $isFinalOption && $k !== $existingPenetapan;
                                    ?>
                                    <option value="<?= esc($k) ?>" <?= $stPenetapan === $k ? 'selected' : '' ?><?= $lockOption ? ' disabled' : '' ?>>
                                        <?= esc($lbl) ?><?= $lockOption ? ' (Khusus Pejabat Penetap)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectTingkat" class="form-label fw-semibold text-dark small">
                                Tingkat Pelanggaran <span class="text-danger">*</span>
                            </label>
                            <?php $tktVal = old('tingkat_pelanggaran', (string) ($kasus['tingkat_pelanggaran'] ?: 'belum_ditetapkan')); ?>
                            <select id="selectTingkat" name="tingkat_pelanggaran" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['tingkat_catalog'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $tktVal === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectDasarAturan" class="form-label fw-semibold text-dark small">
                                Dasar Peraturan
                            </label>
                            <?php $dasarVal = old('dasar_peraturan', (string) ($kasus['dasar_peraturan'] ?: 'Pedoman Tata Tertib Taruna PKTJ')); ?>
                            <select id="selectDasarAturan" name="dasar_peraturan" class="form-select ecc-control-sm">
                                <?php foreach ($refs['dasar_peraturan_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $dasarVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="selectJenisSanksi" class="form-label fw-semibold text-dark small">
                                Jenis Sanksi
                            </label>
                            <?php $sanksiVal = old('jenis_sanksi', (string) ($kasus['jenis_sanksi'] ?: 'Pembinaan')); ?>
                            <select id="selectJenisSanksi" name="jenis_sanksi" class="form-select ecc-control-sm">
                                <?php foreach ($refs['jenis_sanksi_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $sanksiVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputNomorKeputusan" class="form-label fw-semibold text-dark small">
                                Nomor Keputusan
                            </label>
                            <input
                                type="text"
                                id="inputNomorKeputusan"
                                name="nomor_keputusan"
                                class="form-control ecc-control-sm ecc-num"
                                value="<?= esc(old('nomor_keputusan', (string) ($kasus['nomor_keputusan'] ?? ''))) ?>"
                                placeholder="Misal: SK/026/2026"
                                maxlength="100"
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="inputTglKeputusan" class="form-label fw-semibold text-dark small">
                                Tanggal Keputusan
                            </label>
                            <input
                                type="text"
                                id="inputTglKeputusan"
                                name="tanggal_keputusan"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num bg-white"
                                value="<?= esc(old('tanggal_keputusan', (string) ($kasus['tanggal_keputusan'] ?? date('Y-m-d')))) ?>"
                                placeholder="Pilih tanggal keputusan"
                            >
                        </div>

                        <div class="col-12">
                            <label for="inputTindakLanjut" class="form-label fw-semibold text-dark small">
                                Rencana Tindak Lanjut / Sanksi <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="inputTindakLanjut"
                                name="tindak_lanjut"
                                class="form-control rounded-3"
                                rows="3"
                                maxlength="1000"
                                placeholder="Contoh: Pembinaan sesuai ketentuan yang berlaku dan pemantauan perkembangan."
                                required
                            ><?= esc(old('tindak_lanjut', (string) ($kasus['tindak_lanjut'] ?: ($kasus['sanksi'] ?? '')))) ?></textarea>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="selectPenyelesaian" class="form-label fw-semibold text-dark small">
                                Status Penyelesaian <span class="text-danger">*</span>
                            </label>
                            <select id="selectPenyelesaian" name="status_penyelesaian" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['penyelesaian_options'] ?? [] as $k => $lbl): ?>
                                    <option value="<?= esc($k) ?>" <?= $stPenyelesaian === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="inputTglSelesai" class="form-label fw-semibold text-dark small">
                                Tanggal Selesai
                            </label>
                            <input
                                type="text"
                                id="inputTglSelesai"
                                name="tanggal_selesai"
                                class="form-control ecc-control-sm ecc-flatpickr ecc-num bg-white"
                                value="<?= esc(old('tanggal_selesai', (string) ($kasus['tanggal_selesai'] ?? ''))) ?>"
                                placeholder="Diisi jika selesai"
                                <?= $stPenyelesaian !== 'selesai' ? 'disabled' : '' ?>
                            >
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="selectPic" class="form-label fw-semibold text-dark small">
                                PIC Tindak Lanjut <span class="text-danger">*</span>
                            </label>
                            <?php $picVal = old('pic_tindak_lanjut', (string) ($kasus['pic_tindak_lanjut'] ?: 'Unit Ketarunaan')); ?>
                            <select id="selectPic" name="pic_tindak_lanjut" class="form-select ecc-control-sm" required>
                                <?php foreach ($refs['pic_options'] ?? [] as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= $picVal === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="inputCatatanPemantauan" class="form-label fw-semibold text-dark small">
                                Catatan Pemantauan / Alasan Perubahan
                            </label>
                            <input
                                type="text"
                                id="inputCatatanPemantauan"
                                name="catatan_pemantauan"
                                class="form-control ecc-control-sm"
                                value="<?= esc(old('catatan_pemantauan', (string) ($kasus['catatan_pemantauan'] ?? ''))) ?>"
                                maxlength="500"
                                placeholder="Catatan perkembangan pelaksanaan atau alasan pengajuan banding..."
                            >
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-4 border-top">
                        <a href="<?= base_url('ketarunaan/pelanggaran/pemeriksaan/' . $idKasus) ?>" class="btn btn-light border ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i> Sebelumnya: Pemeriksaan
                        </a>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="aksi_submit" value="draf" class="btn btn-outline-secondary ecc-control-sm px-3 fw-semibold btn-tactile pl-touch-ctrl js-submit-pl3">Simpan Draf</button>
                            <button type="submit" name="aksi_submit" value="next" class="btn btn-primary ecc-control-sm px-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 btn-tactile pl-touch-ctrl js-submit-pl3">
                                Berikutnya: Dokumen Bukti <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Panel Kanan: Status Kasus Saat Ini -->
            <div class="col-12 col-lg-4">
                <div class="ecc-panel-box h-100 bg-light">
                    <h3 class="h6 fw-bold text-dark mb-1">Status Kasus (Saat Ini)</h3>
                    <p class="text-secondary mb-3 small">Pembaruan penetapan, penyelesaian, dan validasi dicatat secara terpisah.</p>

                    <div class="d-flex flex-column gap-3 bg-white border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Penetapan</span>
                            <?= ketarunaan_penetapan_badge($existingPenetapan) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Penyelesaian</span>
                            <?= ketarunaan_penyelesaian_badge((string) ($kasus['status_penyelesaian'] ?? 'proses')) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary small">Status Validasi</span>
                            <?= ketarunaan_validasi_badge($stValidasi) ?>
                        </div>
                    </div>

                    <div class="px-3 py-2 rounded-3 small" style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a;">
                        Perubahan status pendidikan diproses terpisah melalui Database Taruna.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tglKepEl     = document.getElementById('inputTglKeputusan');
    var tglSelesaiEl = document.getElementById('inputTglSelesai');
    var fpKep        = null;
    var fpSelesai    = null;

    if (typeof flatpickr !== 'undefined') {
        var fpLocale = (flatpickr.l10ns && flatpickr.l10ns.id) ? 'id' : 'default';
        if (tglKepEl) {
            fpKep = flatpickr(tglKepEl, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                onReady: function (d, s, inst) {
                    if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Keputusan');
                }
            });
        }
        if (tglSelesaiEl) {
            fpSelesai = flatpickr(tglSelesaiEl, {
                locale: fpLocale,
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                onReady: function (d, s, inst) {
                    if (inst && inst.altInput) inst.altInput.setAttribute('aria-label', 'Tanggal Selesai');
                }
            });
        }
    }

    var selectPenyelesaian = document.getElementById('selectPenyelesaian');

    function syncTanggalSelesai() {
        if (!selectPenyelesaian || !tglSelesaiEl) return;
        var isSelesai = selectPenyelesaian.value === 'selesai';
        tglSelesaiEl.disabled = !isSelesai;
        if (fpSelesai && fpSelesai.altInput) {
            fpSelesai.altInput.disabled = !isSelesai;
            fpSelesai.altInput.classList.toggle('bg-light', !isSelesai);
            fpSelesai.altInput.classList.toggle('bg-white', isSelesai);
        }
        if (!isSelesai) {
            if (fpSelesai) {
                fpSelesai.clear();
            } else {
                tglSelesaiEl.value = '';
            }
        }
    }

    if (selectPenyelesaian) {
        selectPenyelesaian.addEventListener('change', syncTanggalSelesai);
        syncTanggalSelesai();
    }

    function showWarn(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#1e40af',
                customClass: { popup: 'rounded-4 shadow border-0' }
            });
        } else {
            alert(text);
        }
    }

    var form = document.getElementById('formPenetapanKasus');
    var lastAction = 'next';
    document.querySelectorAll('.js-submit-pl3').forEach(function (btn) {
        btn.addEventListener('click', function () {
            lastAction = btn.value || 'next';
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            var tindakEl = document.getElementById('inputTindakLanjut');
            var tKep     = tglKepEl ? tglKepEl.value.trim() : '';
            var tSel     = tglSelesaiEl ? tglSelesaiEl.value.trim() : '';
            var isSel    = selectPenyelesaian && selectPenyelesaian.value === 'selesai';

            if (tindakEl && tindakEl.value.trim() === '') {
                e.preventDefault();
                tindakEl.focus();
                showWarn('Rencana Wajib Diisi', 'Isi rencana tindak lanjut atau sanksi terlebih dahulu.');
                return;
            }
            if (isSel && tSel === '') {
                e.preventDefault();
                if (fpSelesai && fpSelesai.altInput) fpSelesai.altInput.focus();
                showWarn('Tanggal Selesai Wajib Diisi', 'Pilih tanggal selesai untuk kasus berstatus Selesai.');
                return;
            }
            if (isSel && tKep !== '' && tSel !== '' && tSel < tKep) {
                e.preventDefault();
                if (fpSelesai && fpSelesai.altInput) fpSelesai.altInput.focus();
                showWarn('Rentang Tanggal Tidak Valid', 'Tanggal selesai tidak boleh lebih awal dari tanggal keputusan.');
                return;
            }

            var aksi = (e.submitter && e.submitter.value) ? e.submitter.value : lastAction;
            document.querySelectorAll('.js-submit-pl3').forEach(function (btn) {
                btn.disabled = true;
            });
            var hiddenAksi = form.querySelector('input[name="aksi_submit"][data-generated="1"]');
            if (!hiddenAksi) {
                hiddenAksi = document.createElement('input');
                hiddenAksi.type = 'hidden';
                hiddenAksi.name = 'aksi_submit';
                hiddenAksi.setAttribute('data-generated', '1');
                form.appendChild(hiddenAksi);
            }
            hiddenAksi.value = aksi;
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.js-submit-pl3').forEach(function (btn) {
                btn.disabled = false;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
