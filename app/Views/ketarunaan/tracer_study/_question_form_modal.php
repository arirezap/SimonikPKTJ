<?php
/**
 * Modal Tambah / Ubah Pertanyaan Tracer Study (digunakan bersama di Bundel & Periode Draf).
 * Mendukung Radio/Dropdown, opsi "Yang lain:", Skala 1-4 / 1-5, dan aturan tampil kondisional (show_if).
 */
$qFormAction   = $qFormAction ?? '#';
$tipeCatalog   = $refs['tipe_jawaban'] ?? ketarunaan_tracer_tipe_jawaban_catalog();
$bagianPresets = $refs['bagian_presets'] ?? ['Informasi Data Pribadi Alumni', 'Informasi Data Pekerjaan Alumni', 'Saran Untuk Pengembangan Program Studi'];
?>
<div class="modal fade" id="modalQuestionTracer" tabindex="-1" aria-labelledby="modalQuestionTracerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="<?= esc($qFormAction) ?>" id="formQuestionTracer" class="modal-content border-0 rounded-4 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="question_id" id="qModalId" value="0">

            <div class="modal-header border-bottom px-4 py-3">
                <h2 class="modal-title h5 fw-bold text-dark mb-0" id="modalQuestionTracerLabel">Tambah Pertanyaan</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label for="qModalBagian" class="form-label fw-semibold small">Bagian / Kelompok</label>
                        <input type="text" class="form-control ecc-control-sm" id="qModalBagian" name="bagian" list="bagianPresetList" placeholder="Contoh: Informasi Data Pekerjaan Alumni" required>
                        <datalist id="bagianPresetList">
                            <?php foreach ($bagianPresets as $bp): ?>
                                <option value="<?= esc($bp) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <div class="col-7 col-md-4">
                        <label for="qModalKode" class="form-label fw-semibold small">Kode Pertanyaan</label>
                        <input type="text" class="form-control ecc-control-sm text-uppercase" id="qModalKode" name="kode_pertanyaan" maxlength="50" placeholder="Otomatis jika kosong">
                        <div class="form-text" style="font-size: 0.72rem;">Kode sama memudahkan perbandingan antar-periode.</div>
                    </div>

                    <div class="col-5 col-md-3">
                        <label for="qModalUrutan" class="form-label fw-semibold small">Nomor Urut</label>
                        <input type="number" class="form-control ecc-control-sm ecc-num" id="qModalUrutan" name="urutan" min="1" placeholder="Otomatis">
                    </div>

                    <div class="col-12">
                        <label for="qModalTeks" class="form-label fw-semibold small">Teks Pertanyaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="qModalTeks" name="teks_pertanyaan" rows="3" required placeholder="Tuliskan pertanyaan yang jelas dan singkat..."></textarea>
                    </div>

                    <div class="col-12 col-md-7">
                        <label for="qModalTipe" class="form-label fw-semibold small">Tipe Jawaban <span class="text-danger">*</span></label>
                        <select class="form-select ecc-control-sm" id="qModalTipe" name="tipe_jawaban" required onchange="toggleQuestionTypeFields()">
                            <?php foreach ($tipeCatalog as $tk => $tl): ?>
                                <option value="<?= esc($tk) ?>"><?= esc($tl) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="qModalWajib" class="form-label fw-semibold small">Sifat Pengisian <span class="text-danger">*</span></label>
                        <select class="form-select ecc-control-sm" id="qModalWajib" name="is_wajib" required>
                            <option value="1" selected>Wajib Diisi</option>
                            <option value="0">Opsional (Tidak Wajib)</option>
                        </select>
                    </div>

                    <div class="col-12" id="qOpsiWrapper">
                        <label for="qModalOpsi" class="form-label fw-semibold small">Daftar Pilihan Jawaban <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="qModalOpsi" name="opsi_jawaban_text" rows="4" placeholder="Tulis 1 pilihan per baris..."></textarea>
                        <div class="form-text" style="font-size: 0.75rem;">Minimal 2 pilihan jawaban, pisahkan dengan baris baru (Enter).</div>

                        <div class="row g-2 mt-2" id="qSingleExtraWrap">
                            <div class="col-12 col-sm-6">
                                <label for="qModalRenderAs" class="form-label fw-semibold small">Tampilan Pilihan</label>
                                <select class="form-select ecc-control-sm" id="qModalRenderAs" name="render_as">
                                    <option value="radio">Daftar Radio (Semua terlihat)</option>
                                    <option value="dropdown">Dropdown (Cocok untuk banyak pilihan)</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="qModalAllowOther" name="allow_other" value="1">
                                    <label class="form-check-label small fw-semibold" for="qModalAllowOther">Tambahkan opsi "Yang lain:" (isian bebas)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="qLikertWrapper">
                        <div class="row g-2">
                            <div class="col-12 col-sm-4">
                                <label for="qLikertMaxScale" class="form-label fw-semibold small">Rentang Skala</label>
                                <select class="form-select ecc-control-sm" id="qLikertMaxScale" name="likert_max_scale" onchange="syncLikertMaxLabelHint()">
                                    <option value="4">Skala 1 s.d. 4 (Resmi Pengguna Lulusan)</option>
                                    <option value="5" selected>Skala 1 s.d. 5</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label for="qLikertMin" class="form-label fw-semibold small">Label Skor 1 (Terendah)</label>
                                <input type="text" class="form-control ecc-control-sm" id="qLikertMin" name="likert_min_label" value="Sangat Kurang">
                            </div>
                            <div class="col-6 col-sm-4">
                                <label for="qLikertMax" class="form-label fw-semibold small" id="qLikertMaxLblText">Label Skor Tertinggi</label>
                                <input type="text" class="form-control ecc-control-sm" id="qLikertMax" name="likert_max_label" value="Sangat Baik">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="qModalBantuan" class="form-label fw-semibold small">Petunjuk Pengisian (Opsional)</label>
                        <input type="text" class="form-control ecc-control-sm" id="qModalBantuan" name="keterangan_bantuan" maxlength="255" placeholder="Contoh: Pilih satu jawaban yang paling sesuai.">
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="fw-semibold small text-dark mb-1">Aturan Tampil Kondisional (Opsional)</div>
                            <div class="ecc-text-muted-aa mb-2" style="font-size: 0.72rem;">Isi jika pertanyaan ini hanya tampil saat jawaban pertanyaan sebelumnya bernilai tertentu.</div>
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <label for="qModalShowIfKode" class="form-label small fw-semibold mb-1">Kode Pertanyaan Pemicu</label>
                                    <input type="text" class="form-control ecc-control-sm text-uppercase" id="qModalShowIfKode" name="show_if_kode" maxlength="50" placeholder="Contoh: TS25_STATUS_PEKERJAAN">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label for="qModalShowIfVals" class="form-label small fw-semibold mb-1">Nilai Pemicu (pisahkan koma)</label>
                                    <input type="text" class="form-control ecc-control-sm" id="qModalShowIfVals" name="show_if_values" placeholder="Contoh: Sudah Bekerja">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile">Simpan Pertanyaan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleQuestionTypeFields() {
    var tipe = document.getElementById('qModalTipe').value;
    var opsiWrap = document.getElementById('qOpsiWrapper');
    var singleExtra = document.getElementById('qSingleExtraWrap');
    var likertWrap = document.getElementById('qLikertWrapper');
    var opsiArea = document.getElementById('qModalOpsi');

    if (tipe === 'pilihan_tunggal' || tipe === 'pilihan_ganda') {
        opsiWrap.classList.remove('d-none');
        likertWrap.classList.add('d-none');
        if (singleExtra) singleExtra.classList.toggle('d-none', tipe !== 'pilihan_tunggal');
        if (opsiArea) opsiArea.required = true;
    } else if (tipe === 'skala_likert') {
        opsiWrap.classList.add('d-none');
        likertWrap.classList.remove('d-none');
        if (opsiArea) opsiArea.required = false;
    } else {
        opsiWrap.classList.add('d-none');
        likertWrap.classList.add('d-none');
        if (opsiArea) opsiArea.required = false;
    }
}

function syncLikertMaxLabelHint() {
    var sc = document.getElementById('qLikertMaxScale');
    var minInput = document.getElementById('qLikertMin');
    if (!sc || !minInput) return;
    if (sc.value === '4' && minInput.value === 'Sangat Kurang') {
        minInput.value = 'Kurang Baik';
    } else if (sc.value === '5' && minInput.value === 'Kurang Baik') {
        minInput.value = 'Sangat Kurang';
    }
}

function resetQuestionModal(nextUrutan) {
    document.getElementById('modalQuestionTracerLabel').textContent = 'Tambah Pertanyaan';
    document.getElementById('qModalId').value = '0';
    document.getElementById('qModalBagian').value = 'Informasi Data Pekerjaan Alumni';
    document.getElementById('qModalKode').value = '';
    document.getElementById('qModalUrutan').value = nextUrutan || '';
    document.getElementById('qModalTeks').value = '';
    document.getElementById('qModalTipe').value = 'pilihan_tunggal';
    document.getElementById('qModalWajib').value = '1';
    document.getElementById('qModalOpsi').value = '';
    document.getElementById('qModalRenderAs').value = 'radio';
    document.getElementById('qModalAllowOther').checked = false;
    document.getElementById('qLikertMaxScale').value = '5';
    document.getElementById('qLikertMin').value = 'Sangat Kurang';
    document.getElementById('qLikertMax').value = 'Sangat Baik';
    document.getElementById('qModalBantuan').value = '';
    document.getElementById('qModalShowIfKode').value = '';
    document.getElementById('qModalShowIfVals').value = '';
    toggleQuestionTypeFields();
}

function openEditQuestionModal(btn) {
    var d = btn.dataset;
    document.getElementById('modalQuestionTracerLabel').textContent = 'Ubah Pertanyaan';
    document.getElementById('qModalId').value = d.id || '0';
    document.getElementById('qModalBagian').value = d.bagian || 'Umum';
    document.getElementById('qModalKode').value = d.kode || '';
    document.getElementById('qModalUrutan').value = d.urutan || '';
    document.getElementById('qModalTeks').value = d.teks || '';
    document.getElementById('qModalTipe').value = d.tipe || 'pilihan_tunggal';
    document.getElementById('qModalWajib').value = (d.wajib === '0') ? '0' : '1';
    document.getElementById('qModalBantuan').value = d.bantuan || '';

    var opsiRaw = [];
    try { opsiRaw = JSON.parse(d.opsi || '[]'); } catch (e) { opsiRaw = []; }

    document.getElementById('qModalRenderAs').value = 'radio';
    document.getElementById('qModalAllowOther').checked = false;
    document.getElementById('qLikertMaxScale').value = '5';
    document.getElementById('qLikertMin').value = 'Sangat Kurang';
    document.getElementById('qLikertMax').value = 'Sangat Baik';
    document.getElementById('qModalShowIfKode').value = '';
    document.getElementById('qModalShowIfVals').value = '';
    document.getElementById('qModalOpsi').value = '';

    if (Array.isArray(opsiRaw)) {
        document.getElementById('qModalOpsi').value = opsiRaw.join('\n');
    } else if (opsiRaw && typeof opsiRaw === 'object') {
        if (Array.isArray(opsiRaw.options)) {
            document.getElementById('qModalOpsi').value = opsiRaw.options.join('\n');
        }
        if (opsiRaw.render_as === 'dropdown') {
            document.getElementById('qModalRenderAs').value = 'dropdown';
        }
        if (opsiRaw.allow_other) {
            document.getElementById('qModalAllowOther').checked = true;
        }
        if (d.tipe === 'skala_likert') {
            var ms = String(opsiRaw.max_scale || 5);
            document.getElementById('qLikertMaxScale').value = (ms === '4') ? '4' : '5';
            document.getElementById('qLikertMin').value = opsiRaw.min_label || (ms === '4' ? 'Kurang Baik' : 'Sangat Kurang');
            document.getElementById('qLikertMax').value = opsiRaw.max_label || 'Sangat Baik';
        }
        if (opsiRaw.show_if && opsiRaw.show_if.kode) {
            document.getElementById('qModalShowIfKode').value = opsiRaw.show_if.kode;
            var vals = Array.isArray(opsiRaw.show_if.values) ? opsiRaw.show_if.values : [opsiRaw.show_if.values];
            document.getElementById('qModalShowIfVals').value = vals.join(', ');
        }
    }

    toggleQuestionTypeFields();
    var modalEl = document.getElementById('modalQuestionTracer');
    if (window.bootstrap && modalEl) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}
</script>
