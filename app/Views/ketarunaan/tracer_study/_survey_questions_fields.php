<?php
/**
 * Partial DRY untuk merender seluruh pertanyaan dinamis Tracer Study:
 * - Mendukung Radio, Dropdown, Checkbox (Pilihan Ganda), Skala Penilaian 1-4 / 1-5, Teks Singkat (text/email/tel), Uraian, Angka, Tanggal
 * - Mendukung opsi "Yang lain:" dengan input teks tervalidasi
 * - Mendukung logika tampil kondisional (`show_if`) untuk percabangan PDF 2 (Sudah/Belum/Tidak Bekerja & Lanjut Studi Ya/Tidak)
 * - Mendukung interpolasi dinamis `{PRODI}` & `{PRODI_LENGKAP}` untuk PDF 1 (Pengguna Lulusan RSTJ/TRO/TO)
 *
 * Variabel yang diterima:
 * - $groupedQ (array)
 * - $sectionIntros (array)
 * - $answers (array)
 * - $prefills (array)        Prefill berbasis profil alumni (keyed by prefill_key)
 * - $codePrefills (array)    Prefill berbasis kode_pertanyaan (mis. identitas Atasan Langsung saat menilai > 1 alumni)
 * - $secStartNo (int)        Nomor bagian awal (1 jika tanpa blok statis lama, 2 jika ada blok statis)
 * - $idPrefix (string)       Prefix ID input HTML ('pq_' atau 'q_')
 */
$groupedQ      = $groupedQ ?? [];
$sectionIntros = $sectionIntros ?? [];
$answers       = $answers ?? [];
$prefills      = $prefills ?? [];
$codePrefills  = $codePrefills ?? [];
$secStartNo    = (int) ($secStartNo ?? 1);
$idPrefix      = (string) ($idPrefix ?? 'q_');

$secIdx = 0;
foreach ($groupedQ as $secTitle => $qList):
    $secIdx++;
    $secIntro   = $sectionIntros[$secTitle] ?? null;
    $secHasTpl  = str_contains((string) $secTitle, '{PRODI}');
    $secDisplay = str_replace(['{PRODI_LENGKAP}', '{PRODI}'], ['Program Studi PKTJ', 'PKTJ'], (string) $secTitle);
?>
    <section class="ts-dyn-section mb-4 <?= ($secStartNo > 1 || $secIdx > 1) ? 'pt-3 border-top' : '' ?>" data-section-title-tpl="<?= esc((string) $secTitle) ?>">
        <h2 class="h6 fw-bold text-primary mb-2">
            <span class="js-ts-sec-num"><?= $secStartNo + $secIdx - 1 ?></span>.
            <span class="<?= $secHasTpl ? 'js-ts-prodi-tpl' : '' ?>" data-tpl="<?= esc((string) $secTitle) ?>"><?= esc($secDisplay) ?></span>
        </h2>
        <?php if (!empty($secIntro)): ?>
            <p class="ecc-text-muted-aa small mb-3"><?= esc((string) $secIntro) ?></p>
        <?php endif; ?>

        <div class="d-flex flex-column gap-3">
            <?php foreach ($qList as $q): ?>
                <?php
                $qid       = (int) $q['id'];
                $kode      = strtoupper(trim((string) ($q['kode_pertanyaan'] ?? '')));
                $tipe      = (string) $q['tipe_jawaban'];
                $wajib     = ((int) ($q['is_wajib'] ?? 1) === 1);
                $meta      = $q['opsi_meta'] ?? ketarunaan_tracer_parse_opsi($q['opsi_jawaban_json'] ?? null, $tipe);
                $opts      = $meta['options'] ?? [];
                $renderAs  = (string) ($meta['render_as'] ?? 'radio');
                $allowOth  = !empty($meta['allow_other']);
                $maxScale  = (int) ($meta['max_scale'] ?? 5);
                $minLbl    = (string) ($meta['min_label'] ?? ($maxScale === 4 ? 'Kurang Baik' : 'Sangat Kurang'));
                $maxLbl    = (string) ($meta['max_label'] ?? 'Sangat Baik');
                $showIf        = $meta['show_if'] ?? null;
                $inputType     = (string) ($meta['input_type'] ?? 'text');
                $prefKey       = $meta['prefill_key'] ?? null;
                $wilayahRole   = $meta['wilayah_role'] ?? null;
                $wilayahParent = $meta['wilayah_parent_kode'] ?? null;

                $prev      = $answers[$qid] ?? null;
                $oldAns    = old('jawaban.' . $qid);
                $oldOther  = old('jawaban_other.' . $qid);

                $defaultPrefill = '';
                if ($kode !== '' && isset($codePrefills[$kode]) && trim((string) $codePrefills[$kode]) !== '') {
                    $defaultPrefill = (string) $codePrefills[$kode];
                } elseif ($prefKey !== null && isset($prefills[$prefKey]) && trim((string) $prefills[$prefKey]) !== '') {
                    $defaultPrefill = (string) $prefills[$prefKey];
                }

                $curText = $oldAns !== null
                    ? (is_array($oldAns) ? '' : (string) $oldAns)
                    : (string) ($prev['nilai_teks'] ?? $defaultPrefill);

                $curOpsi = is_array($oldAns)
                    ? $oldAns
                    : (is_array($prev['nilai_opsi'] ?? null) ? $prev['nilai_opsi'] : ($curText !== '' ? [$curText] : []));

                $isOtherSelected = false;
                $otherTextValue  = is_string($oldOther) ? $oldOther : '';
                if ($tipe === 'pilihan_tunggal' && $allowOth) {
                    if ($curText === '__OTHER__') {
                        $isOtherSelected = true;
                    } elseif ($curText !== '' && !in_array($curText, $opts, true)) {
                        $isOtherSelected = true;
                        if ($otherTextValue === '') {
                            $otherTextValue = $curText;
                        }
                    }
                }

                $qHasTpl   = str_contains((string) $q['teks_pertanyaan'], '{PRODI}');
                $qDisplay  = str_replace(['{PRODI_LENGKAP}', '{PRODI}'], ['Program Studi PKTJ', 'PKTJ'], (string) $q['teks_pertanyaan']);
                $fieldId   = $idPrefix . 'fld_' . $qid;
                ?>
                <div class="ts-q-item js-ts-q-wrap"
                     data-q-id="<?= $qid ?>"
                     data-q-kode="<?= esc($kode) ?>"
                     data-q-required="<?= $wajib ? '1' : '0' ?>"
                     data-q-type="<?= esc($tipe) ?>"
                     <?php if (!empty($showIf)): ?>
                         data-show-if-kode="<?= esc((string) $showIf['kode']) ?>"
                         data-show-if-values='<?= esc(json_encode($showIf['values'], JSON_UNESCAPED_UNICODE)) ?>'
                     <?php endif; ?>>

                    <?php if (in_array($tipe, ['teks_singkat', 'teks_panjang', 'angka', 'tanggal'], true) || ($tipe === 'pilihan_tunggal' && $renderAs === 'dropdown')): ?>
                        <label for="<?= esc($fieldId) ?>" class="form-label fw-semibold text-dark small mb-1 d-block">
                            <span class="<?= $qHasTpl ? 'js-ts-prodi-tpl' : '' ?>" data-tpl="<?= esc((string) $q['teks_pertanyaan']) ?>"><?= esc($qDisplay) ?></span>
                            <?php if ($wajib): ?>
                                <span class="text-danger">*</span>
                            <?php else: ?>
                                <span class="fw-normal ecc-text-muted-aa">(Opsional)</span>
                            <?php endif; ?>
                        </label>
                    <?php else: ?>
                        <div class="form-label fw-semibold text-dark small mb-1 d-block">
                            <span class="<?= $qHasTpl ? 'js-ts-prodi-tpl' : '' ?>" data-tpl="<?= esc((string) $q['teks_pertanyaan']) ?>"><?= esc($qDisplay) ?></span>
                            <?php if ($wajib): ?>
                                <span class="text-danger">*</span>
                            <?php else: ?>
                                <span class="fw-normal ecc-text-muted-aa">(Opsional)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($q['keterangan_bantuan'])): ?>
                        <div class="ecc-text-muted-aa mb-2" style="font-size: 0.75rem;"><?= esc((string) $q['keterangan_bantuan']) ?></div>
                    <?php endif; ?>

                    <?php if ($wilayahRole === 'provinsi'): ?>
                        <select name="jawaban[<?= $qid ?>]"
                                id="<?= esc($fieldId) ?>"
                                class="form-select ecc-control-sm mt-2 js-ts-input js-ecc-wilayah-prov"
                                data-q-kode="<?= esc($kode) ?>"
                                data-wilayah-pair="ts_<?= esc($kode) ?>"
                                data-current-value="<?= esc($curText) ?>"
                                data-placeholder="-- Pilih Provinsi --"
                                data-orig-required="<?= $wajib ? '1' : '0' ?>"
                                <?= $wajib ? 'required' : '' ?>>
                            <option value="">-- Pilih Provinsi --</option>
                            <?php foreach ($opts as $optLabel): ?>
                                <option value="<?= esc((string) $optLabel) ?>" <?= $curText === (string) $optLabel ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($wilayahRole === 'kabupaten_kota'): ?>
                        <select name="jawaban[<?= $qid ?>]"
                                id="<?= esc($fieldId) ?>"
                                class="form-select ecc-control-sm mt-2 js-ts-input js-ecc-wilayah-kab"
                                data-q-kode="<?= esc($kode) ?>"
                                data-wilayah-pair="ts_<?= esc($wilayahParent ?: 'TS25_PROVINSI_DOMISILI') ?>"
                                data-current-value="<?= esc($curText) ?>"
                                data-placeholder="-- Pilih Kabupaten / Kota --"
                                data-orig-required="<?= $wajib ? '1' : '0' ?>"
                                <?= $wajib ? 'required' : '' ?>>
                            <option value="">-- Pilih Kabupaten / Kota --</option>
                            <?php if ($curText !== ''): ?>
                                <option value="<?= esc($curText) ?>" selected><?= esc($curText) ?></option>
                            <?php endif; ?>
                        </select>

                    <?php elseif ($wilayahRole === 'kabupaten_kota_tags'): ?>
                        <select name="jawaban[<?= $qid ?>]"
                                id="<?= esc($fieldId) ?>"
                                class="form-select ecc-control-sm mt-2 js-ts-input js-ecc-wilayah-kab-only"
                                data-q-kode="<?= esc($kode) ?>"
                                data-allow-tags="1"
                                data-current-value="<?= esc($curText) ?>"
                                data-placeholder="-- Pilih atau ketik Kabupaten / Kota --"
                                data-orig-required="<?= $wajib ? '1' : '0' ?>"
                                <?= $wajib ? 'required' : '' ?>>
                            <option value="">-- Pilih atau ketik Kabupaten / Kota --</option>
                            <?php if ($curText !== ''): ?>
                                <option value="<?= esc($curText) ?>" selected><?= esc($curText) ?></option>
                            <?php endif; ?>
                        </select>

                    <?php elseif ($tipe === 'pilihan_tunggal' && $renderAs === 'dropdown'): ?>
                        <select name="jawaban[<?= $qid ?>]"
                                id="<?= esc($fieldId) ?>"
                                class="form-select ecc-control-sm mt-2 js-ts-input"
                                data-q-kode="<?= esc($kode) ?>"
                                data-orig-required="<?= $wajib ? '1' : '0' ?>"
                                <?= $wajib ? 'required' : '' ?>>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($opts as $optLabel): ?>
                                <option value="<?= esc((string) $optLabel) ?>" <?= $curText === (string) $optLabel ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($tipe === 'pilihan_tunggal'): ?>
                        <div class="d-flex flex-column gap-2 mt-2">
                            <?php foreach ($opts as $oi => $optLabel): ?>
                                <?php $optId = $idPrefix . $qid . '_r_' . $oi; ?>
                                <div class="form-check">
                                    <input class="form-check-input js-ts-input"
                                           type="radio"
                                           name="jawaban[<?= $qid ?>]"
                                           id="<?= esc($optId) ?>"
                                           value="<?= esc((string) $optLabel) ?>"
                                           data-q-kode="<?= esc($kode) ?>"
                                           data-orig-required="<?= ($wajib && $oi === 0) ? '1' : '0' ?>"
                                           <?= (!$isOtherSelected && in_array((string) $optLabel, $curOpsi, true)) ? 'checked' : '' ?>
                                           <?= ($wajib && $oi === 0) ? 'required' : '' ?>>
                                    <label class="form-check-label small" for="<?= esc($optId) ?>"><?= esc((string) $optLabel) ?></label>
                                </div>
                            <?php endforeach; ?>

                            <?php if ($allowOth): ?>
                                <?php $othRadioId = $idPrefix . $qid . '_r_other'; ?>
                                <div class="form-check">
                                    <input class="form-check-input js-ts-input js-ts-other-radio"
                                           type="radio"
                                           name="jawaban[<?= $qid ?>]"
                                           id="<?= esc($othRadioId) ?>"
                                           value="__OTHER__"
                                           data-q-kode="<?= esc($kode) ?>"
                                           data-other-target="<?= esc($idPrefix . $qid . '_other_txt') ?>"
                                           <?= $isOtherSelected ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="<?= esc($othRadioId) ?>">Yang lain:</label>
                                </div>
                                <div class="ms-4 <?= $isOtherSelected ? '' : 'd-none' ?>" id="<?= esc($idPrefix . $qid . '_other_wrap') ?>">
                                    <input type="text"
                                           name="jawaban_other[<?= $qid ?>]"
                                           id="<?= esc($idPrefix . $qid . '_other_txt') ?>"
                                           class="form-control ecc-control-sm js-ts-other-input"
                                           value="<?= esc($otherTextValue) ?>"
                                           placeholder="Tuliskan jawaban lainnya..."
                                           <?= $isOtherSelected ? 'required' : '' ?>>
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($tipe === 'pilihan_ganda'): ?>
                        <div class="d-flex flex-column gap-2 mt-2 js-ts-checkbox-group" data-required-group="<?= $wajib ? '1' : '0' ?>">
                            <?php foreach ($opts as $oi => $optLabel): ?>
                                <?php $optId = $idPrefix . $qid . '_c_' . $oi; ?>
                                <div class="form-check">
                                    <input class="form-check-input js-ts-input"
                                           type="checkbox"
                                           name="jawaban[<?= $qid ?>][]"
                                           id="<?= esc($optId) ?>"
                                           value="<?= esc((string) $optLabel) ?>"
                                           data-q-kode="<?= esc($kode) ?>"
                                           <?= in_array((string) $optLabel, $curOpsi, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="<?= esc($optId) ?>"><?= esc((string) $optLabel) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    <?php elseif ($tipe === 'skala_likert'): ?>
                        <?php
                        $curNum = $oldAns !== null ? (int) $oldAns : (int) round((float) ($prev['nilai_angka'] ?? 0));
                        ?>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <span class="ecc-text-muted-aa small"><?= esc($minLbl) ?></span>
                            <div class="d-flex gap-2">
                                <?php for ($s = 1; $s <= $maxScale; $s++): ?>
                                    <?php $lid = $idPrefix . $qid . '_lk_' . $s; ?>
                                    <input type="radio"
                                           class="btn-check js-ts-input"
                                           name="jawaban[<?= $qid ?>]"
                                           id="<?= esc($lid) ?>"
                                           value="<?= $s ?>"
                                           data-q-kode="<?= esc($kode) ?>"
                                           data-orig-required="<?= ($wajib && $s === 1) ? '1' : '0' ?>"
                                           <?= $curNum === $s ? 'checked' : '' ?>
                                           <?= ($wajib && $s === 1) ? 'required' : '' ?>>
                                    <label class="btn btn-outline-primary ts-likert-option ecc-num" for="<?= esc($lid) ?>"><?= $s ?></label>
                                <?php endfor; ?>
                            </div>
                            <span class="ecc-text-muted-aa small"><?= esc($maxLbl) ?></span>
                        </div>

                    <?php elseif ($tipe === 'teks_panjang'): ?>
                        <textarea name="jawaban[<?= $qid ?>]"
                                  id="<?= esc($fieldId) ?>"
                                  class="form-control mt-2 js-ts-input"
                                  rows="3"
                                  data-q-kode="<?= esc($kode) ?>"
                                  data-orig-required="<?= $wajib ? '1' : '0' ?>"
                                  <?= $wajib ? 'required' : '' ?>
                                  placeholder="Tuliskan jawaban Anda..."><?= esc($curText) ?></textarea>

                    <?php elseif ($tipe === 'angka'): ?>
                        <input type="number"
                               step="any"
                               name="jawaban[<?= $qid ?>]"
                               id="<?= esc($fieldId) ?>"
                               class="form-control ecc-control-sm ecc-num mt-2 js-ts-input"
                               style="max-width: 240px;"
                               value="<?= esc($curText) ?>"
                               data-q-kode="<?= esc($kode) ?>"
                               data-orig-required="<?= $wajib ? '1' : '0' ?>"
                               <?= $wajib ? 'required' : '' ?>>

                    <?php elseif ($tipe === 'tanggal'): ?>
                        <input type="text"
                               name="jawaban[<?= $qid ?>]"
                               id="<?= esc($fieldId) ?>"
                               class="form-control ecc-control-sm bg-white js-ts-q-date ecc-num mt-2 js-ts-input"
                               style="max-width: 240px;"
                               value="<?= esc($curText) ?>"
                               placeholder="YYYY-MM-DD"
                               data-q-kode="<?= esc($kode) ?>"
                               data-orig-required="<?= $wajib ? '1' : '0' ?>"
                               <?= $wajib ? 'required' : '' ?>>

                    <?php else: ?>
                        <input type="<?= esc($inputType) ?>"
                               name="jawaban[<?= $qid ?>]"
                               id="<?= esc($fieldId) ?>"
                               class="form-control ecc-control-sm mt-2 js-ts-input <?= $inputType === 'tel' ? 'ecc-num' : '' ?>"
                               value="<?= esc($curText) ?>"
                               data-q-kode="<?= esc($kode) ?>"
                               data-orig-required="<?= $wajib ? '1' : '0' ?>"
                               <?= $wajib ? 'required' : '' ?>
                               placeholder="Tuliskan jawaban Anda...">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<script>
(function () {
    if (window.__eccTracerDynamicInit) return;
    window.__eccTracerDynamicInit = true;

    function getAnswerValueByCode(form, kode) {
        var inputs = form.querySelectorAll('.js-ts-input[data-q-kode="' + kode + '"]');
        if (!inputs.length) return '';
        var first = inputs[0];
        if (first.type === 'radio') {
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].checked) return inputs[i].value || '';
            }
            return '';
        }
        if (first.type === 'checkbox') {
            var vals = [];
            for (var j = 0; j < inputs.length; j++) {
                if (inputs[j].checked) vals.push(inputs[j].value);
            }
            return vals;
        }
        return first.value || '';
    }

    function mapProdiTokens(selected) {
        var up = String(selected || '').toUpperCase();
        if (up.indexOf('RSTJ') !== -1) {
            return { short: 'RSTJ', full: 'Sarjana Terapan Program Studi RSTJ' };
        }
        if (up.indexOf('TRO') !== -1) {
            return { short: 'TRO', full: 'Sarjana Terapan Program Studi TRO' };
        }
        if (up.indexOf('DIPLOMA III TO') !== -1 || up.indexOf('DIPLOMA 3 TO') !== -1 || up === 'TO') {
            return { short: 'TO', full: 'Diploma III Program Studi TO' };
        }
        return { short: 'PKTJ', full: 'Program Studi PKTJ' };
    }

    function updateDynamicFormState(form) {
        // 1. Toggle input "Yang lain:" pada radio allow_other
        form.querySelectorAll('.js-ts-other-radio').forEach(function (radio) {
            var targetId = radio.getAttribute('data-other-target');
            var input = targetId ? document.getElementById(targetId) : null;
            var wrap  = input ? input.parentElement : null;
            if (!input || !wrap) return;
            var show = radio.checked;
            wrap.classList.toggle('d-none', !show);
            input.required = show;
            if (!show) {
                input.disabled = true;
            } else {
                input.disabled = false;
            }
        });

        // 2. Evaluasi percabangan show_if untuk setiap pertanyaan
        form.querySelectorAll('.js-ts-q-wrap[data-show-if-kode]').forEach(function (qWrap) {
            var depKode = qWrap.getAttribute('data-show-if-kode');
            var allowed = [];
            try { allowed = JSON.parse(qWrap.getAttribute('data-show-if-values') || '[]'); } catch (e) { allowed = []; }
            var actual = getAnswerValueByCode(form, depKode);
            var matched = false;

            if (Array.isArray(actual)) {
                matched = actual.some(function (a) {
                    return allowed.some(function (v) { return String(a).toLowerCase() === String(v).toLowerCase(); });
                });
            } else {
                matched = allowed.some(function (v) { return String(actual).toLowerCase() === String(v).toLowerCase(); });
            }

            qWrap.classList.toggle('d-none', !matched);
            qWrap.querySelectorAll('input, select, textarea').forEach(function (el) {
                if (!matched) {
                    el.disabled = true;
                    el.required = false;
                } else {
                    if (el.classList.contains('js-ts-other-input')) {
                        var qId = qWrap.getAttribute('data-q-id');
                        var rOther = qWrap.querySelector('.js-ts-other-radio');
                        var isOth = rOther && rOther.checked;
                        el.disabled = !isOth;
                        el.required = !!isOth;
                    } else {
                        el.disabled = false;
                        el.required = (el.getAttribute('data-orig-required') === '1');
                    }
                }
            });
        });

        // 3. Sembunyikan section bila seluruh pertanyaannya tersembunyi & perbarui penomoran bagian
        var startNum = parseInt(form.getAttribute('data-sec-start') || '1', 10);
        var curNum = startNum;
        form.querySelectorAll('.ts-dyn-section').forEach(function (sec) {
            var qWraps = sec.querySelectorAll('.js-ts-q-wrap');
            var anyVisible = false;
            qWraps.forEach(function (qw) {
                if (!qw.classList.contains('d-none')) anyVisible = true;
            });
            sec.classList.toggle('d-none', !anyVisible);
            if (anyVisible) {
                var numEl = sec.querySelector('.js-ts-sec-num');
                if (numEl) numEl.textContent = String(curNum++);
            }
        });

        // 4. Interpolasi {PRODI} dan {PRODI_LENGKAP} pada formulir Pengguna Lulusan (PDF 1)
        var prodiVal = getAnswerValueByCode(form, 'TSM25_PRODI_ALUMNI');
        var tokens = mapProdiTokens(prodiVal);
        form.querySelectorAll('.js-ts-prodi-tpl').forEach(function (el) {
            var tpl = el.getAttribute('data-tpl') || '';
            if (tpl) {
                el.textContent = tpl
                    .replace(/\{PRODI_LENGKAP\}/g, tokens.full)
                    .replace(/\{PRODI\}/g, tokens.short);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.js-ts-q-date', { dateFormat: 'Y-m-d', allowInput: true, locale: 'id' });
        }

        document.querySelectorAll('form.js-ts-dynamic-form').forEach(function (form) {
            updateDynamicFormState(form);

            form.addEventListener('change', function (ev) {
                updateDynamicFormState(form);
                if (ev.target && ev.target.classList && ev.target.classList.contains('js-ts-other-radio') && ev.target.checked) {
                    var tid = ev.target.getAttribute('data-other-target');
                    var inp = tid ? document.getElementById(tid) : null;
                    if (inp) inp.focus();
                }
            });

            // Validasi checkbox group wajib (hanya pada pertanyaan yang terlihat)
            form.addEventListener('submit', function (ev) {
                var invalidGroup = null;
                form.querySelectorAll('.js-ts-q-wrap:not(.d-none) .js-ts-checkbox-group[data-required-group="1"]').forEach(function (grp) {
                    var checked = grp.querySelectorAll('input[type="checkbox"]:checked').length;
                    if (checked === 0 && !invalidGroup) {
                        invalidGroup = grp;
                    }
                });
                if (invalidGroup) {
                    ev.preventDefault();
                    invalidGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilihan Belum Lengkap',
                            text: 'Pilih minimal satu opsi pada pertanyaan wajib bertanda centang.'
                        });
                    } else {
                        alert('Pilih minimal satu opsi pada pertanyaan wajib bertanda centang.');
                    }
                }
            });
        });
    });
})();
</script>
