<?= $this->extend('layouts/public_survei') ?>
<?= $this->section('title') ?>Survei Pemetaan SDM PKTJ<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stepper-scroll {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }

    .stepper-scroll::-webkit-scrollbar {
        height: 4px;
    }

    .stepper-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .step-pill {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 4px 12px;
        border-radius: 50rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.8125rem;
        font-weight: 500;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .step-pill:hover {
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .step-pill.is-active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
        font-weight: 600;
    }

    .step-pill.is-complete:not(.is-active) {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #15803d;
    }

    .q-block {
        padding: 20px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .q-block:first-child {
        padding-top: 8px;
    }

    .q-block:last-child {
        border-bottom: 0;
        padding-bottom: 8px;
    }

    .q-block.has-missing,
    .matrix-row.has-missing {
        background-color: #fef2f2;
        margin-left: -12px;
        margin-right: -12px;
        padding-left: 12px;
        padding-right: 12px;
        border-radius: 12px;
        border-bottom-color: transparent;
    }

    .q-number-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.8125rem;
        flex-shrink: 0;
    }

    .option-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 14px;
        min-height: 44px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #ffffff;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .option-card:hover {
        border-color: #93c5fd;
        background-color: #f8fafc;
    }

    .option-card.is-selected {
        border-color: #2563eb;
        background-color: #eff6ff;
    }

    .option-card.is-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .option-card .form-check-input {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        cursor: pointer;
    }

    /* Responsive Single-DOM Likert Scale (No. 43 - 55 & Skala 1 - 5) */
    .scale-pill-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    .scale-choice-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-height: 56px;
        padding: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }

    .scale-choice-label:hover {
        border-color: #93c5fd;
        background: #f8fafc;
    }

    .scale-choice-input:checked + .scale-choice-label {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.2);
    }

    .scale-choice-input:focus-visible + .scale-choice-label {
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .scale-choice-num {
        font-weight: 700;
        font-size: 0.9375rem;
        line-height: 1.2;
    }

    .scale-choice-caption {
        font-size: 0.6875rem;
        line-height: 1.2;
        margin-top: 4px;
        color: #64748b;
    }

    .scale-choice-input:checked + .scale-choice-label .scale-choice-caption {
        color: #dbeafe;
    }

    /* Matrix Table Layout (Desktop >= 768px, Reflows to Stacked Cards on Mobile < 768px) */
    .matrix-legend-bar {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 16px;
        padding: 12px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
    }

    .matrix-row {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 16px;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .matrix-btns {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    .matrix-pill-lbl {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        font-weight: 700;
        font-size: 0.875rem;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .matrix-pill-lbl:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    .matrix-radio:checked + .matrix-pill-lbl {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
    }

    .locked-input-bg {
        background-color: #f8fafc !important;
        color: #334155 !important;
    }

    .wizard-action-bar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 24px;
        margin-top: 24px;
    }

    .declaration-box {
        margin-top: 16px;
        padding: 16px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        background-color: #f8fafc;
        transition: border-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .declaration-box.is-checked {
        border-color: #16a34a;
        background-color: #f0fdf4;
        box-shadow: 0 0 0 1px #16a34a, 0 1px 3px rgba(22, 163, 74, 0.08);
    }

    .declaration-box.is-checked .declaration-title {
        color: #15803d !important;
    }

    .declaration-box.has-missing {
        border-color: #ef4444;
        background-color: #fef2f2;
        box-shadow: 0 0 0 1px #ef4444;
    }

    .declaration-check-label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        min-height: 48px;
        cursor: pointer;
        margin-bottom: 0;
    }

    .declaration-check-label .form-check-input {
        width: 20px;
        height: 20px;
        margin-top: 2px;
        flex-shrink: 0;
        cursor: pointer;
    }

    #btnSubmitFinal.is-unconfirmed {
        opacity: 0.65;
    }

    @media (prefers-reduced-motion: reduce) {
        .step-pill,
        .option-card,
        .scale-choice-label,
        .matrix-pill-lbl,
        .declaration-box {
            transition: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .matrix-legend-bar {
            display: none;
        }
        .matrix-row {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .scale-pill-grid {
            grid-template-columns: 1fr;
        }
        .scale-choice-label {
            flex-direction: row;
            justify-content: flex-start;
            gap: 12px;
            min-height: 44px;
            padding: 8px 14px;
            text-align: left;
        }
        .scale-choice-caption {
            margin-top: 0;
            font-size: 0.8125rem;
        }
        .wizard-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: 0;
            border-radius: 16px 16px 0 0;
            padding: 12px 16px;
            box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.08);
            z-index: 1015;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (!$isSurveyOpen): ?>
    <div class="card-bento-survei text-center py-5">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-4 border" style="width: 48px; height: 48px; background: #f8fafc;">
            <i class="bi bi-lock text-secondary fs-5"></i>
        </div>
        <h1 class="h5 fw-bold text-dark mb-2">Periode Survei Ditutup</h1>
        <p class="text-secondary mb-0 mx-auto" style="max-width: 480px; font-size: 0.875rem;">
            Pengisian Survei Pemetaan Potensi, Kompetensi, dan Kapasitas Pegawai PKTJ saat ini sedang ditutup. Silakan hubungi Bagian Keuangan, Umum, dan Kerja Sama (KUK) apabila memerlukan informasi lebih lanjut.
        </p>
    </div>
<?php else: ?>

    <!-- =================================================================== -->
    <!-- TAHAP 0: KARTU CEK IDENTITAS (KHUSUS 18 DIGIT NIP PEGAWAI ASN PKTJ) -->
    <!-- =================================================================== -->
    <div id="identityCheckView" class="<?= $initialSession ? 'd-none' : '' ?>">
        <div class="card-bento-survei mb-3">
            <span class="badge rounded-pill text-primary border border-primary-subtle fw-semibold mb-2" style="background: #eff6ff; padding: 4px 12px; font-size: 0.75rem;">
                Pemetaan SDM PKTJ
            </span>
            <h1 class="h4 fw-bold text-dark mb-2">Survei Pemetaan Potensi, Kompetensi, dan Kapasitas Pegawai</h1>
            <p class="text-secondary mb-3" style="font-size: 0.875rem; line-height: 1.6;">
                Survei ini bertujuan memetakan kompetensi, keahlian tambahan, kapasitas kerja, dan aspirasi pengembangan pegawai di lingkungan PKTJ. Jawaban Anda tersimpan otomatis di setiap bagian sehingga dapat dilanjutkan kembali kapan saja.
            </p>

            <div class="p-3 rounded-3 border mb-4" style="background: #f8fafc; font-size: 0.8125rem;">
                <div class="fw-semibold text-dark mb-1">Pegawai</div>
                <div class="text-secondary">Masukkan <strong>18 digit NIP</strong> Anda. Data profil akun ECC Anda akan terisi otomatis.</div>
            </div>

            <form id="formCekIdentitas" novalidate>
                <!-- Honeypot Anti-Bot -->
                <div class="visually-hidden" aria-hidden="true">
                    <label for="hpWebsiteUrl">Jangan diisi</label>
                    <input type="text" id="hpWebsiteUrl" name="website_url" tabindex="-1" autocomplete="off">
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label for="inputNomorIdentitas" class="form-label fw-semibold text-dark mb-0" style="font-size: 0.875rem;">
                            NIP Pegawai (18 Digit)
                        </label>
                        <span id="digitCountBadge" class="badge bg-light text-secondary border rounded-pill tabular-nums" style="padding: 4px 12px; font-size: 0.75rem;">
                            0 digit
                        </span>
                    </div>
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="18"
                        class="form-control tabular-nums"
                        id="inputNomorIdentitas"
                        placeholder="Ketik 18 digit NIP Anda"
                        autocomplete="off"
                        required
                    >
                </div>

                <div id="identityAlertBox" class="alert d-none py-2 px-3 mb-3 rounded-3" style="font-size: 0.8125rem;" role="alert"></div>

                <button type="submit" id="btnCekIdentitas" class="btn btn-primary fw-semibold rounded-3 w-100 btn-tactile" style="min-height: 44px;">
                    Mulai / Lanjutkan Survei
                </button>
            </form>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAHAP 1 - 10: WIZARD 10 BAGIAN (83 PERTANYAAN)                       -->
    <!-- =================================================================== -->
    <div id="surveyWizardView" class="<?= $initialSession ? '' : 'd-none' ?>">
        <!-- Strip Identitas & Progres -->
        <div class="card-bento-survei mb-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span id="badgeTipeResponden" class="badge rounded-pill text-primary border border-primary-subtle fw-semibold" style="background: #eff6ff; padding: 4px 12px; font-size: 0.75rem;">
                        ASN
                    </span>
                    <span id="labelRespondenIdentitas" class="fw-semibold text-dark tabular-nums" style="font-size: 0.875rem;">-</span>
                    <span id="labelRespondenNama" class="text-secondary" style="font-size: 0.8125rem;"></span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span id="draftSaveIndicator" class="text-secondary" style="font-size: 0.75rem;">Draf tersimpan</span>
                    <button type="button" id="btnGantiResponden" class="btn btn-link btn-sm text-secondary text-decoration-none p-0 fw-medium" style="font-size: 0.75rem;">
                        Ganti NIP
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-2">
                <span id="labelActiveSectionIndicator" class="fw-semibold text-dark" style="font-size: 0.8125rem;">Bagian 1 dari 10</span>
                <span id="labelProgressPercent" class="fw-semibold text-primary tabular-nums" style="font-size: 0.8125rem;">0% Terisi</span>
            </div>
            <div class="progress rounded-pill mb-3" style="height: 8px; background-color: #e2e8f0;">
                <div id="wizardProgressBar" class="progress-bar rounded-pill" role="progressbar" style="width: 0%; background-color: #1d4ed8;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <!-- Navigasi Pill 10 Bagian -->
            <div class="stepper-scroll" id="sectionStepperBar" role="tablist" aria-label="Bagian Survei">
                <?php foreach ($sections as $stepNo => $sec): ?>
                    <button
                        type="button"
                        class="step-pill btn-tactile <?= $stepNo === 1 ? 'is-active' : '' ?>"
                        data-step-target="<?= (int) $stepNo ?>"
                    >
                        <span class="step-pill-check d-none"><i class="bi bi-check-circle-fill"></i></span>
                        <span class="tabular-nums"><?= esc($sec['roman']) ?>.</span>
                        <span><?= esc($sec['short_title']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Datalist Saran Pangkat/Golongan (Nomor 6) -->
        <datalist id="pangkatSuggestionsList">
            <?php foreach (\App\Libraries\SurveiPemetaanConfig::PANGKAT_GOLONGAN_SUGGESTIONS as $pSugg): ?>
                <option value="<?= esc($pSugg) ?>"></option>
            <?php endforeach; ?>
        </datalist>

        <!-- Kontainer 10 Panel Bagian -->
        <form id="formSurveiWizard" novalidate>
            <?php foreach ($sections as $stepNo => $sec): ?>
                <section
                    class="card-bento-survei survey-step-panel <?= $stepNo === 1 ? '' : 'd-none' ?>"
                    id="surveyStepPanel_<?= (int) $stepNo ?>"
                    data-step="<?= (int) $stepNo ?>"
                >
                    <div class="pb-3 mb-2 border-bottom">
                        <div class="fw-bold text-primary mb-1" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                            BAGIAN <?= esc($sec['roman']) ?> DARI X
                        </div>
                        <h2 class="h5 fw-bold text-dark mb-1"><?= esc($sec['short_title']) ?></h2>
                        <p class="text-secondary mb-0" style="font-size: 0.8125rem;"><?= esc($sec['subtitle']) ?></p>

                        <?php if ($stepNo === 1): ?>
                            <div id="emptyProfileHintBanner" class="d-none mt-3 p-2 px-3 rounded-3 border" style="background: #eff6ff; border-color: #bfdbfe !important; font-size: 0.8125rem; color: #1e40af;">
                                Data profil yang sudah terdaftar terisi otomatis. Kolom profil yang masih kosong dapat Anda isi untuk sekaligus memperbarui profil akun Anda.
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $matrixRenderedInStep6 = false;
                    ?>

                    <?php foreach ($sec['questions'] as $qNo): ?>
                        <?php
                        $meta = $questions[$qNo] ?? null;
                        if (!$meta) {
                            continue;
                        }
                        $type = $meta['type'];

                        // Render Matriks Likert 43 - 55 secara terpadu pada Bagian VI
                        if (!empty($meta['is_matrix'])) {
                            if ($matrixRenderedInStep6) {
                                continue;
                            }
                            $matrixRenderedInStep6 = true;
                            ?>
                            <div class="q-block" id="matrixLikertBlock">
                                <div class="mb-3">
                                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.9375rem;">
                                        Pernyataan Perilaku &amp; Sikap Kerja (Nomor 43&ndash;55)
                                    </div>
                                    <div class="text-secondary" style="font-size: 0.8125rem;">
                                        Pilih skala 1 sampai 5 yang paling menggambarkan kondisi Anda (1 = Sangat tidak setuju, 2 = Tidak setuju, 3 = Netral, 4 = Setuju, 5 = Sangat setuju).
                                    </div>
                                </div>

                                <div class="matrix-legend-bar">
                                    <div>Pernyataan</div>
                                    <div class="matrix-btns text-center">
                                        <span title="Sangat tidak setuju">1</span>
                                        <span title="Tidak setuju">2</span>
                                        <span title="Netral">3</span>
                                        <span title="Setuju">4</span>
                                        <span title="Sangat setuju">5</span>
                                    </div>
                                </div>

                                <?php for ($mNo = 43; $mNo <= 55; $mNo++): ?>
                                    <?php $mMeta = $questions[$mNo]; ?>
                                    <div class="matrix-row q-item-container" id="qBlock_<?= $mNo ?>" data-qno="<?= $mNo ?>" data-qtype="likert_1_5">
                                        <div>
                                            <div class="d-flex align-items-start gap-2">
                                                <span class="q-number-badge tabular-nums"><?= $mNo ?></span>
                                                <div class="fw-medium text-dark" style="font-size: 0.875rem; line-height: 1.5;">
                                                    <?= esc($mMeta['label']) ?>
                                                </div>
                                            </div>
                                            <div class="matrix-selected-caption text-primary fw-medium ps-5 mt-1 d-md-none" id="matrixCaption_<?= $mNo ?>" style="font-size: 0.75rem;"></div>
                                        </div>
                                        <div class="matrix-btns">
                                            <?php foreach ($mMeta['scale_labels'] as $sVal => $sText): ?>
                                                <div>
                                                    <input
                                                        type="radio"
                                                        class="visually-hidden matrix-radio q-input-scale"
                                                        name="q_<?= $mNo ?>"
                                                        id="q_<?= $mNo ?>_<?= $sVal ?>"
                                                        data-qno="<?= $mNo ?>"
                                                        data-label="<?= esc($sText) ?>"
                                                        value="<?= (int) $sVal ?>"
                                                    >
                                                    <label class="matrix-pill-lbl tabular-nums btn-tactile" for="q_<?= $mNo ?>_<?= $sVal ?>" title="<?= esc($sVal . ' - ' . $sText) ?>">
                                                        <?= (int) $sVal ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <?php
                            continue;
                        }
                        ?>

                        <!-- Pertanyaan Standar / Kondisional -->
                        <div
                            class="q-block q-item-container"
                            id="qBlock_<?= (int) $qNo ?>"
                            data-qno="<?= (int) $qNo ?>"
                            data-qtype="<?= esc($type) ?>"
                            data-conditional="<?= esc((string) ($meta['conditional'] ?? '')) ?>"
                            data-max-select="<?= (int) ($meta['max_select'] ?? 0) ?>"
                            data-exclusive="<?= esc((string) ($meta['exclusive_option'] ?? '')) ?>"
                            data-has-other="<?= !empty($meta['has_other']) ? '1' : '0' ?>"
                            data-other-value="<?= esc((string) ($meta['other_value'] ?? 'Lainnya')) ?>"
                        >
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <label class="d-flex align-items-start gap-2 mb-0" for="qInput_<?= (int) $qNo ?>">
                                    <span class="q-number-badge tabular-nums"><?= (int) $qNo ?></span>
                                    <span class="fw-semibold text-dark" style="font-size: 0.9375rem; line-height: 1.45;">
                                        <?= esc($meta['label']) ?>
                                    </span>
                                </label>
                                <span class="badge bg-light text-secondary border rounded-pill d-none flex-shrink-0" id="qLockBadge_<?= (int) $qNo ?>" style="padding: 4px 12px; font-size: 0.6875rem;">
                                    Terisi Otomatis
                                </span>
                                <?php if (!empty($meta['max_select'])): ?>
                                    <span class="badge bg-light text-primary border border-primary-subtle rounded-pill flex-shrink-0 tabular-nums" id="qQuotaBadge_<?= (int) $qNo ?>" style="padding: 4px 12px; font-size: 0.75rem;">
                                        0 / <?= (int) $meta['max_select'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($meta['helper'])): ?>
                                <div class="text-secondary ps-4 ms-2 mb-2" id="qHelper_<?= (int) $qNo ?>" style="font-size: 0.8125rem;">
                                    <?= esc($meta['helper']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="ps-0 ps-sm-4 ms-sm-2 mt-2">
                                <?php if ($type === 'text' || $type === 'identitas'): ?>
                                    <input
                                        type="text"
                                        class="form-control q-input-text <?= $type === 'identitas' ? 'locked-input-bg tabular-nums' : '' ?>"
                                        id="qInput_<?= (int) $qNo ?>"
                                        data-qno="<?= (int) $qNo ?>"
                                        maxlength="<?= (int) ($meta['max_length'] ?? 200) ?>"
                                        placeholder="<?= esc((string) ($meta['placeholder'] ?? '')) ?>"
                                        <?= $type === 'identitas' ? 'readonly' : '' ?>
                                    >

                                <?php elseif ($type === 'datalist'): ?>
                                    <input
                                        type="text"
                                        class="form-control q-input-text"
                                        id="qInput_<?= (int) $qNo ?>"
                                        data-qno="<?= (int) $qNo ?>"
                                        list="pangkatSuggestionsList"
                                        maxlength="<?= (int) ($meta['max_length'] ?? 100) ?>"
                                        placeholder="<?= esc((string) ($meta['placeholder'] ?? '')) ?>"
                                    >

                                <?php elseif ($type === 'unit_select'): ?>
                                    <select class="form-select q-input-select" id="qInput_<?= (int) $qNo ?>" data-qno="<?= (int) $qNo ?>">
                                        <option value="">-- Pilih Unit Kerja --</option>
                                        <?php foreach ($unitList as $uItem): ?>
                                            <option value="<?= esc($uItem['nama_unit']) ?>"><?= esc($uItem['nama_unit']) ?></option>
                                        <?php endforeach; ?>
                                        <option value="Pimpinan Institusi" id="optPimpinanInstitusi" class="d-none">Pimpinan Institusi</option>
                                    </select>

                                <?php elseif ($type === 'number'): ?>
                                    <div class="input-group" style="max-width: 240px;">
                                        <input
                                            type="number"
                                            inputmode="numeric"
                                            class="form-control tabular-nums q-input-number"
                                            id="qInput_<?= (int) $qNo ?>"
                                            data-qno="<?= (int) $qNo ?>"
                                            min="<?= (int) ($meta['min'] ?? 17) ?>"
                                            max="<?= (int) ($meta['max'] ?? 75) ?>"
                                            placeholder="<?= esc((string) ($meta['placeholder'] ?? '')) ?>"
                                        >
                                        <?php if (!empty($meta['unit_suffix'])): ?>
                                            <span class="input-group-text bg-light text-secondary" style="font-size: 0.875rem;"><?= esc($meta['unit_suffix']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                <?php elseif ($type === 'date'): ?>
                                    <div style="max-width: 320px;">
                                        <input
                                            type="date"
                                            class="form-control tabular-nums q-input-date"
                                            id="qInput_<?= (int) $qNo ?>"
                                            data-qno="<?= (int) $qNo ?>"
                                            max="<?= date('Y-m-d') ?>"
                                            placeholder="<?= esc((string) ($meta['placeholder'] ?? 'Pilih tanggal')) ?>"
                                        >
                                    </div>

                                <?php elseif ($type === 'auto_tenure'): ?>
                                    <input
                                        type="text"
                                        class="form-control locked-input-bg tabular-nums"
                                        id="qInput_<?= (int) $qNo ?>"
                                        value="Pilih tanggal pada Nomor 9 terlebih dahulu"
                                        readonly
                                    >

                                <?php elseif ($type === 'textarea'): ?>
                                    <textarea
                                        class="form-control q-input-textarea"
                                        id="qInput_<?= (int) $qNo ?>"
                                        data-qno="<?= (int) $qNo ?>"
                                        rows="3"
                                        maxlength="2500"
                                        placeholder="<?= esc((string) ($meta['placeholder'] ?? '')) ?>"
                                    ></textarea>

                                <?php elseif ($type === 'triple_text'): ?>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach (($meta['placeholders'] ?? ['', '', '']) as $tIdx => $tPh): ?>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light fw-semibold text-secondary tabular-nums" style="width: 40px; justify-content: center;"><?= $tIdx + 1 ?></span>
                                                <input
                                                    type="text"
                                                    class="form-control q-input-triple"
                                                    data-qno="<?= (int) $qNo ?>"
                                                    data-triple-index="<?= (int) $tIdx ?>"
                                                    maxlength="300"
                                                    placeholder="<?= esc($tPh) ?>"
                                                >
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                <?php elseif (in_array($type, ['radio', 'radio_scored', 'radio_optional_note'], true)): ?>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach (($meta['options'] ?? []) as $oIdx => $optLabel): ?>
                                            <?php $optId = 'q_' . $qNo . '_opt_' . $oIdx; ?>
                                            <label class="option-card" for="<?= $optId ?>">
                                                <input
                                                    type="radio"
                                                    class="form-check-input q-input-radio"
                                                    name="q_<?= (int) $qNo ?>"
                                                    id="<?= $optId ?>"
                                                    data-qno="<?= (int) $qNo ?>"
                                                    value="<?= esc($optLabel) ?>"
                                                >
                                                <span class="text-dark" style="font-size: 0.875rem; line-height: 1.45;"><?= esc($optLabel) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (!empty($meta['has_other'])): ?>
                                        <div class="mt-2 d-none" id="qOtherWrap_<?= (int) $qNo ?>">
                                            <input
                                                type="text"
                                                class="form-control q-input-other"
                                                id="qOtherInput_<?= (int) $qNo ?>"
                                                data-qno="<?= (int) $qNo ?>"
                                                maxlength="300"
                                                placeholder="Tuliskan keterangan lainnya..."
                                            >
                                        </div>
                                    <?php elseif ($type === 'radio_optional_note'): ?>
                                        <div class="mt-2 d-none" id="qNoteWrap_<?= (int) $qNo ?>" data-trigger="<?= esc((string) ($meta['note_trigger_value'] ?? '')) ?>">
                                            <input
                                                type="text"
                                                class="form-control q-input-other"
                                                id="qOtherInput_<?= (int) $qNo ?>"
                                                data-qno="<?= (int) $qNo ?>"
                                                maxlength="300"
                                                placeholder="<?= esc((string) ($meta['note_placeholder'] ?? 'Keterangan tambahan (opsional)')) ?>"
                                            >
                                        </div>
                                    <?php endif; ?>

                                <?php elseif ($type === 'checkbox'): ?>
                                    <div
                                        class="d-flex flex-column gap-2"
                                        data-conflicts='<?= esc(json_encode($meta['exclusive_conflicts'] ?? [], JSON_UNESCAPED_UNICODE)) ?>'
                                        id="qCheckboxGroup_<?= (int) $qNo ?>"
                                    >
                                        <?php foreach (($meta['options'] ?? []) as $oIdx => $optLabel): ?>
                                            <?php $optId = 'q_' . $qNo . '_chk_' . $oIdx; ?>
                                            <label class="option-card" for="<?= $optId ?>">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input q-input-checkbox"
                                                    id="<?= $optId ?>"
                                                    data-qno="<?= (int) $qNo ?>"
                                                    value="<?= esc($optLabel) ?>"
                                                >
                                                <span class="text-dark" style="font-size: 0.875rem; line-height: 1.45;"><?= esc($optLabel) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (!empty($meta['has_other'])): ?>
                                        <div class="mt-2 d-none" id="qOtherWrap_<?= (int) $qNo ?>">
                                            <input
                                                type="text"
                                                class="form-control q-input-other"
                                                id="qOtherInput_<?= (int) $qNo ?>"
                                                data-qno="<?= (int) $qNo ?>"
                                                maxlength="300"
                                                placeholder="Sebutkan lainnya..."
                                            >
                                        </div>
                                    <?php endif; ?>

                                <?php elseif (in_array($type, ['likert_1_5', 'scale_custom'], true)): ?>
                                    <div class="scale-pill-grid">
                                        <?php foreach (($meta['scale_labels'] ?? []) as $sVal => $sText): ?>
                                            <?php $sId = 'q_' . $qNo . '_sc_' . $sVal; ?>
                                            <div>
                                                <input
                                                    type="radio"
                                                    class="visually-hidden scale-choice-input q-input-scale"
                                                    name="q_<?= (int) $qNo ?>"
                                                    id="<?= $sId ?>"
                                                    data-qno="<?= (int) $qNo ?>"
                                                    data-label="<?= esc($sText) ?>"
                                                    value="<?= (int) $sVal ?>"
                                                >
                                                <label class="scale-choice-label btn-tactile" for="<?= $sId ?>">
                                                    <span class="scale-choice-num tabular-nums"><?= (int) $sVal ?></span>
                                                    <span class="scale-choice-caption"><?= esc($sText) ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ((int) $stepNo === 10): ?>
                        <div class="declaration-box" id="declarationBox">
                            <div class="fw-bold text-dark declaration-title mb-2" style="font-size: 0.875rem;">
                                Pernyataan Tanggung Jawab
                            </div>
                            <label class="declaration-check-label" for="chkPernyataanTanggungJawab">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="chkPernyataanTanggungJawab"
                                    value="1"
                                    required
                                >
                                <span class="text-dark" style="font-size: 0.875rem; line-height: 1.5;">
                                    Saya menyatakan bahwa seluruh data dan jawaban yang saya sampaikan adalah benar serta saya bertanggung jawab penuh atas isian tersebut.
                                </span>
                            </label>
                            <div id="declarationErrorHint" class="text-danger fw-medium d-none mt-2 ps-4 ms-2" style="font-size: 0.8125rem;">
                                Centang pernyataan ini sebelum mengirim survei.
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>

            <!-- Bilah Tombol Navigasi Bawah -->
            <div class="wizard-action-bar d-flex align-items-center justify-content-between gap-2">
                <button type="button" id="btnPrevStep" class="btn btn-outline-secondary rounded-pill fw-semibold btn-tactile d-none" style="height: 40px; padding: 4px 16px;">
                    Sebelumnya
                </button>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" id="btnSaveDraft" class="btn btn-light border rounded-pill fw-semibold text-dark btn-tactile" style="height: 40px; padding: 4px 16px;">
                        Simpan Draf
                    </button>
                    <button type="button" id="btnNextStep" class="btn btn-primary rounded-pill fw-semibold btn-tactile" style="height: 40px; padding: 4px 20px;">
                        Simpan &amp; Lanjut
                    </button>
                    <button type="button" id="btnSubmitFinal" class="btn btn-success rounded-pill fw-semibold btn-tactile d-none is-unconfirmed" aria-disabled="true" style="height: 40px; padding: 4px 24px;">
                        Kirim Survei
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- =================================================================== -->
    <!-- MODAL VERIFIKASI KATA SANDI AKUN ECC (KHUSUS ASN PINDAH PERANGKAT)   -->
    <!-- =================================================================== -->
    <div class="modal fade" id="modalVerifikasiAsn" tabindex="-1" aria-labelledby="modalVerifikasiAsnLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalVerifikasiAsnLabel">Lanjutkan dengan Akun ECC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formVerifikasiAsn" novalidate>
                    <div class="modal-body px-4 py-3">
                        <p id="verifAsnInfoText" class="text-secondary mb-3" style="font-size: 0.8125rem;">
                            Progres pengisian ditemukan. Masukkan Kata Sandi akun ECC Anda untuk melanjutkan.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.8125rem;">NIP Pegawai</label>
                            <input type="text" id="verifAsnNip" class="form-control locked-input-bg tabular-nums" readonly>
                        </div>
                        <div class="mb-2">
                            <label for="verifAsnPassword" class="form-label fw-semibold text-dark" style="font-size: 0.8125rem;">Kata Sandi Akun ECC</label>
                            <div class="input-group">
                                <input type="password" id="verifAsnPassword" class="form-control" placeholder="Masukkan Kata Sandi ECC" required>
                                <button type="button" class="btn btn-outline-secondary btn-tactile" id="btnToggleVerifPass" style="min-height: 40px; min-width: 44px;" aria-label="Tampilkan Kata Sandi">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div id="verifAsnErrorBox" class="alert alert-danger d-none py-2 px-3 mt-3 mb-0 rounded-3" style="font-size: 0.8125rem;"></div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-1">
                        <button type="button" class="btn btn-light border rounded-pill fw-semibold px-3" style="height: 36px;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitVerifAsn" class="btn btn-primary rounded-pill fw-semibold px-4 btn-tactile" style="height: 36px;">Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if ($isSurveyOpen): ?>
<script>
(function () {
    'use strict';

    const URLS = {
        cekIdentitas:   <?= json_encode(site_url('survei-pemetaan/cek-identitas')) ?>,
        verifikasiAkun: <?= json_encode(site_url('survei-pemetaan/verifikasi-akun')) ?>,
        saveStep:       <?= json_encode(site_url('survei-pemetaan/save-step')) ?>,
        submitFinal:    <?= json_encode(site_url('survei-pemetaan/submit')) ?>,
        keluarSesi:     <?= json_encode(site_url('survei-pemetaan/keluar-sesi')) ?>,
        pingCsrf:       <?= json_encode(site_url('survei-pemetaan/ping-csrf')) ?>
    };

    const SECTIONS_META = <?= json_encode($sections, JSON_UNESCAPED_UNICODE) ?>;
    const INITIAL_SESSION = <?= json_encode($initialSession, JSON_UNESCAPED_UNICODE) ?>;

    let csrfTokenName = <?= json_encode(csrf_token()) ?>;
    let csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || <?= json_encode(csrf_hash()) ?>;

    let activeSession = null;
    let currentStep = 1;
    let answersState = {};
    let isSaving = false;
    let q9Picker = null;

    function updateCsrf(data) {
        if (data && data.csrf_token) {
            csrfTokenName = data.csrf_token;
        }
        if (data && data.csrf_hash) {
            csrfHash = data.csrf_hash;
            const meta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
            if (meta) meta.setAttribute('content', csrfHash);
        }
    }

    function showDialog(opts) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: opts.title || 'Informasi',
                text: opts.text || '',
                icon: opts.icon || 'info',
                showCancelButton: !!opts.showCancel,
                confirmButtonText: opts.confirmText || 'OK',
                cancelButtonText: opts.cancelText || 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-4 shadow border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-secondary small mb-3',
                    confirmButton: 'btn ' + (opts.confirmClass || 'btn-primary') + ' rounded-pill px-4 py-2 fw-semibold ms-2 btn-tactile',
                    cancelButton: 'btn btn-light border rounded-pill px-4 py-2 fw-semibold btn-tactile'
                }
            });
        }
        if (opts.showCancel) {
            const ok = window.confirm((opts.title ? opts.title + '\n' : '') + (opts.text || ''));
            return Promise.resolve({ isConfirmed: ok });
        }
        window.alert((opts.title ? opts.title + '\n' : '') + (opts.text || ''));
        return Promise.resolve({ isConfirmed: true });
    }

    function postForm(url, params) {
        const body = new URLSearchParams();
        body.append(csrfTokenName, csrfHash);
        Object.keys(params).forEach(k => {
            if (params[k] !== undefined && params[k] !== null) {
                body.append(k, String(params[k]));
            }
        });

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfHash,
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString()
        }).then(async (res) => {
            let json = {};
            try {
                json = await res.json();
            } catch (e) {
                json = { status: 'error', message: 'Koneksi terputus. Silakan coba lagi.' };
            }
            if (res.status >= 500 && !json.status) {
                json = { status: 'error', message: 'Gagal memproses permintaan. Silakan coba lagi.' };
            }
            updateCsrf(json);
            json._httpStatus = res.status;
            return json;
        });
    }

    function getLocalBackupKey() {
        return activeSession && activeSession.respon ? ('ecc_survei_draft_' + activeSession.respon.id) : null;
    }

    function saveLocalBackup() {
        const key = getLocalBackupKey();
        if (!key) return;
        try {
            localStorage.setItem(key, JSON.stringify({
                ts: Date.now(),
                answers: answersState
            }));
        } catch (e) {}
    }

    function clearLocalBackup() {
        const key = getLocalBackupKey();
        if (!key) return;
        try {
            localStorage.removeItem(key);
        } catch (e) {}
    }

    // Hitung Masa Kerja otomatis dari YYYY-MM-DD (selaras SurveiPemetaanConfig::computeTenureFromDate)
    function computeTenureDisplay(ymd) {
        if (!ymd || !/^\d{4}-\d{2}-\d{2}$/.test(ymd)) {
            return 'Pilih tanggal pada Nomor 9 terlebih dahulu';
        }
        const parts = ymd.split('-').map(Number);
        const start = new Date(parts[0], parts[1] - 1, parts[2]);
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        if (isNaN(start.getTime()) || start > today) {
            return 'Tanggal tidak boleh melebihi hari ini';
        }

        let years = today.getFullYear() - start.getFullYear();
        let months = today.getMonth() - start.getMonth();
        if (today.getDate() < start.getDate()) {
            months--;
        }
        if (months < 0) {
            years--;
            months += 12;
        }

        const dec = years + (months / 12);
        let bucket = 'Lebih dari 10 tahun';
        if (dec < 1) bucket = 'Kurang dari 1 tahun';
        else if (years <= 3) bucket = '1–3 tahun';
        else if (years <= 6) bucket = '4–6 tahun';
        else if (years <= 10) bucket = '7–10 tahun';

        const segs = [];
        if (years > 0) segs.push(years + ' Tahun');
        if (months > 0 || segs.length === 0) segs.push(months + ' Bulan');

        return segs.join(' ') + ' (' + bucket + ')';
    }

    // Evaluasi visibilitas cabang kondisional (No. 14, 22-25, 28, 30)
    function evaluateConditionals() {
        const q13 = (answersState[13]?.teks || '').trim();
        const q21 = (answersState[21]?.teks || '').trim();
        const q27 = (answersState[27]?.teks || '').trim();
        const q29 = (answersState[29]?.teks || '').trim();

        toggleBlock(14, q13 === 'Ya');

        const showSkillBranch = (q21 === 'Ya, memiliki beberapa keahlian tambahan' || q21 === 'Ya, memiliki satu keahlian tambahan');
        [22, 23, 24, 25].forEach(no => toggleBlock(no, showSkillBranch));

        toggleBlock(28, q27 !== 'Tidak bersedia');
        toggleBlock(30, q29 !== '' && q29 !== 'Belum pernah');
    }

    function toggleBlock(qNo, visible) {
        const el = document.getElementById('qBlock_' + qNo);
        if (!el) return;
        if (visible) {
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
            el.classList.remove('has-missing');
        }
    }

    function applyProfileLocks(locks) {
        if (!locks) return;
        const map = {
            1: !!locks.q1_locked,
            2: true,
            6: !!locks.q6_locked,
            7: !!locks.q7_locked,
            8: !!locks.q8_locked
        };

        Object.keys(map).forEach(qStr => {
            const qNo = Number(qStr);
            const isLocked = map[qNo];
            const input = document.getElementById('qInput_' + qNo);
            const badge = document.getElementById('qLockBadge_' + qNo);

            if (badge) {
                badge.classList.toggle('d-none', !isLocked);
            }
            if (!input) return;

            if (qNo === 8 && input.tagName === 'SELECT') {
                const val8 = (answersState[8]?.teks || '').trim();
                const optPimp = document.getElementById('optPimpinanInstitusi');
                if (val8 === 'Pimpinan Institusi' && optPimp) {
                    optPimp.classList.remove('d-none');
                }
                input.disabled = isLocked;
                input.classList.toggle('locked-input-bg', isLocked);
            } else {
                input.readOnly = isLocked;
                input.classList.toggle('locked-input-bg', isLocked);
            }
        });

        const hintBanner = document.getElementById('emptyProfileHintBanner');
        if (hintBanner) {
            const isAsn = activeSession?.respon?.tipe_responden === 'akun_ecc';
            hintBanner.classList.toggle('d-none', !(isAsn && locks.has_empty_profile_fields));
        }
    }

    function hydrateDomFromAnswers() {
        document.querySelectorAll('.q-item-container').forEach(container => {
            const qNo = Number(container.dataset.qno);
            const qType = container.dataset.qtype;
            const item = answersState[qNo] || { teks: '', angka: null, pilihan: [], lainnya: '' };

            if (qType === 'text' || qType === 'identitas' || qType === 'datalist') {
                const inp = document.getElementById('qInput_' + qNo);
                if (inp) inp.value = item.teks || '';
            } else if (qType === 'unit_select') {
                const sel = document.getElementById('qInput_' + qNo);
                if (sel) sel.value = item.teks || '';
            } else if (qType === 'number') {
                const inp = document.getElementById('qInput_' + qNo);
                if (inp) inp.value = (item.angka !== null && item.angka !== undefined && item.angka !== '') ? Math.round(Number(item.angka)) : (item.teks || '');
            } else if (qType === 'date') {
                const inp = document.getElementById('qInput_' + qNo);
                if (inp) {
                    inp.value = item.teks || '';
                    if (q9Picker) {
                        q9Picker.setDate(item.teks || '', false);
                    }
                }
                const q10Inp = document.getElementById('qInput_10');
                if (q10Inp) {
                    q10Inp.value = computeTenureDisplay(item.teks || '');
                }
            } else if (qType === 'textarea') {
                const ta = document.getElementById('qInput_' + qNo);
                if (ta) ta.value = item.teks || '';
            } else if (qType === 'triple_text') {
                const arr = Array.isArray(item.pilihan) ? item.pilihan : [];
                container.querySelectorAll('.q-input-triple').forEach(inp => {
                    const idx = Number(inp.dataset.tripleIndex || 0);
                    inp.value = arr[idx] || '';
                });
            } else if (qType === 'radio' || qType === 'radio_scored' || qType === 'radio_optional_note') {
                container.querySelectorAll('.q-input-radio').forEach(rad => {
                    const checked = (rad.value === (item.teks || ''));
                    rad.checked = checked;
                    rad.closest('.option-card')?.classList.toggle('is-selected', checked);
                });
                const otherInp = document.getElementById('qOtherInput_' + qNo);
                if (otherInp) otherInp.value = item.lainnya || '';
                refreshOtherVisibility(qNo);
            } else if (qType === 'checkbox') {
                const arr = Array.isArray(item.pilihan) ? item.pilihan : [];
                container.querySelectorAll('.q-input-checkbox').forEach(chk => {
                    const checked = arr.includes(chk.value);
                    chk.checked = checked;
                    chk.closest('.option-card')?.classList.toggle('is-selected', checked);
                });
                const otherInp = document.getElementById('qOtherInput_' + qNo);
                if (otherInp) otherInp.value = item.lainnya || '';
                refreshCheckboxQuotaAndOther(qNo);
            } else if (qType === 'likert_1_5' || qType === 'scale_custom') {
                const scoreVal = item.angka !== null && item.angka !== undefined ? Number(item.angka) : 0;
                let matchedLabel = '';
                container.querySelectorAll('.q-input-scale').forEach(rad => {
                    const checked = (Number(rad.value) === scoreVal);
                    rad.checked = checked;
                    if (checked) matchedLabel = rad.dataset.label || '';
                });
                const cap = document.getElementById('matrixCaption_' + qNo);
                if (cap) {
                    cap.textContent = scoreVal > 0 ? ('Terpilih: ' + scoreVal + ' — ' + matchedLabel) : '';
                }
            }
        });

        evaluateConditionals();
    }

    function refreshOtherVisibility(qNo) {
        const block = document.getElementById('qBlock_' + qNo);
        if (!block) return;
        const qType = block.dataset.qtype;
        const selectedVal = answersState[qNo]?.teks || '';

        if (block.dataset.hasOther === '1') {
            const otherVal = block.dataset.otherValue || 'Lainnya';
            const wrap = document.getElementById('qOtherWrap_' + qNo);
            if (wrap) wrap.classList.toggle('d-none', selectedVal !== otherVal);
        } else if (qType === 'radio_optional_note') {
            const wrap = document.getElementById('qNoteWrap_' + qNo);
            if (wrap) {
                const trigger = wrap.dataset.trigger || '';
                wrap.classList.toggle('d-none', selectedVal !== trigger);
            }
        }
    }

    function refreshCheckboxQuotaAndOther(qNo) {
        const block = document.getElementById('qBlock_' + qNo);
        if (!block) return;

        const maxSelect = Number(block.dataset.maxSelect || 0);
        const selectedArr = Array.isArray(answersState[qNo]?.pilihan) ? answersState[qNo].pilihan : [];

        const quotaBadge = document.getElementById('qQuotaBadge_' + qNo);
        if (quotaBadge && maxSelect > 0) {
            quotaBadge.textContent = selectedArr.length + ' / ' + maxSelect;
        }

        if (maxSelect > 0) {
            const reachedMax = selectedArr.length >= maxSelect;
            block.querySelectorAll('.q-input-checkbox').forEach(chk => {
                const disableIt = reachedMax && !chk.checked;
                chk.disabled = disableIt;
                chk.closest('.option-card')?.classList.toggle('is-disabled', disableIt);
            });
        }

        if (block.dataset.hasOther === '1') {
            const otherVal = block.dataset.otherValue || 'Lainnya';
            const wrap = document.getElementById('qOtherWrap_' + qNo);
            if (wrap) wrap.classList.toggle('d-none', !selectedArr.includes(otherVal));
        }
    }

    function renderProgressHeader(progress) {
        if (!progress) return;
        const pct = Math.min(100, Math.round(Number(progress.progress_percent || 0)));
        const bar = document.getElementById('wizardProgressBar');
        const lblPct = document.getElementById('labelProgressPercent');

        if (bar) {
            bar.style.width = pct + '%';
            bar.setAttribute('aria-valuenow', String(pct));
        }
        if (lblPct) {
            lblPct.textContent = pct + '% Terisi (' + (progress.total_answered || 0) + '/' + (progress.total_applicable || 83) + ')';
        }

        const secComplete = progress.section_complete || {};
        document.querySelectorAll('#sectionStepperBar .step-pill').forEach(btn => {
            const s = Number(btn.dataset.stepTarget);
            const done = !!secComplete[s];
            btn.classList.toggle('is-complete', done);
            btn.querySelector('.step-pill-check')?.classList.toggle('d-none', !done);
        });
    }

    function showStep(stepNo, scrollTop = true) {
        currentStep = Math.max(1, Math.min(10, Number(stepNo) || 1));

        document.querySelectorAll('.survey-step-panel').forEach(panel => {
            const pStep = Number(panel.dataset.step);
            panel.classList.toggle('d-none', pStep !== currentStep);
        });

        document.querySelectorAll('#sectionStepperBar .step-pill').forEach(pill => {
            const pStep = Number(pill.dataset.stepTarget);
            const active = (pStep === currentStep);
            pill.classList.toggle('is-active', active);
            if (active) {
                pill.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        });

        const secMeta = SECTIONS_META[currentStep] || {};
        const lblSec = document.getElementById('labelActiveSectionIndicator');
        if (lblSec) {
            lblSec.textContent = 'Bagian ' + currentStep + ' dari 10 — ' + (secMeta.short_title || '');
        }

        document.getElementById('btnPrevStep')?.classList.toggle('d-none', currentStep <= 1);
        document.getElementById('btnNextStep')?.classList.toggle('d-none', currentStep >= 10);
        document.getElementById('btnSubmitFinal')?.classList.toggle('d-none', currentStep !== 10);

        if (scrollTop) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function mountSession(sessionData) {
        activeSession = sessionData;
        answersState = Object.assign({}, sessionData.answers || {});

        // Pulihkan cadangan lokal apabila ada jawaban draf yang belum sempat terkirim
        const backupKey = getLocalBackupKey();
        if (backupKey) {
            try {
                const rawBk = localStorage.getItem(backupKey);
                if (rawBk) {
                    const parsedBk = JSON.parse(rawBk);
                    if (parsedBk && parsedBk.answers && typeof parsedBk.answers === 'object') {
                        Object.keys(parsedBk.answers).forEach(qKey => {
                            const current = answersState[qKey];
                            const hasServerVal = current && (
                                (current.teks && current.teks.trim() !== '') ||
                                (current.angka !== null && current.angka !== undefined && current.angka !== '') ||
                                (Array.isArray(current.pilihan) && current.pilihan.length > 0)
                            );
                            if (!hasServerVal && parsedBk.answers[qKey]) {
                                answersState[qKey] = parsedBk.answers[qKey];
                            }
                        });
                    }
                }
            } catch (e) {}
        }

        const badgeTipe = document.getElementById('badgeTipeResponden');
        if (badgeTipe) {
            badgeTipe.textContent = 'ASN';
        }

        const lblId = document.getElementById('labelRespondenIdentitas');
        if (lblId) {
            lblId.textContent = sessionData.respon?.nomor_identitas || '-';
        }

        const lblNama = document.getElementById('labelRespondenNama');
        if (lblNama) {
            const nm = answersState[1]?.teks || sessionData.respon?.snapshot_nama || '';
            lblNama.textContent = nm ? ('· ' + nm) : '';
        }

        document.getElementById('identityCheckView')?.classList.add('d-none');
        document.getElementById('surveyWizardView')?.classList.remove('d-none');

        const chkDecl = document.getElementById('chkPernyataanTanggungJawab');
        if (chkDecl) {
            chkDecl.checked = false;
        }
        syncDeclarationState();

        hydrateDomFromAnswers();
        applyProfileLocks(sessionData.locks);
        renderProgressHeader(sessionData.progress);
        showStep(sessionData.respon?.last_step || 1, false);
    }

    function syncDeclarationState() {
        const chk = document.getElementById('chkPernyataanTanggungJawab');
        const box = document.getElementById('declarationBox');
        const errHint = document.getElementById('declarationErrorHint');
        const btnSubmit = document.getElementById('btnSubmitFinal');
        const checked = !!(chk && chk.checked);

        if (box) {
            box.classList.toggle('is-checked', checked);
            if (checked) {
                box.classList.remove('has-missing');
            }
        }
        if (errHint && checked) {
            errHint.classList.add('d-none');
        }
        if (btnSubmit) {
            btnSubmit.classList.toggle('is-unconfirmed', !checked);
            btnSubmit.setAttribute('aria-disabled', checked ? 'false' : 'true');
        }
    }

    function flagMissingDeclaration() {
        if (currentStep !== 10) {
            showStep(10, false);
        }
        const box = document.getElementById('declarationBox');
        const errHint = document.getElementById('declarationErrorHint');
        const chk = document.getElementById('chkPernyataanTanggungJawab');

        if (box) {
            box.classList.remove('is-checked');
            box.classList.add('has-missing');
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        if (errHint) {
            errHint.classList.remove('d-none');
        }
        if (chk) {
            chk.focus({ preventScroll: true });
        }
    }

    function collectStepAnswersPayload(stepNo) {
        const payload = {};
        const sec = SECTIONS_META[stepNo];
        const qList = sec && Array.isArray(sec.questions) ? sec.questions : [];
        qList.forEach(qNo => {
            if (qNo === 10) return;
            if (answersState[qNo] !== undefined) {
                payload[qNo] = answersState[qNo];
            }
        });
        return payload;
    }

    function collectAllAnswersPayload() {
        const payload = {};
        Object.keys(answersState).forEach(qNo => {
            if (Number(qNo) !== 10) {
                payload[qNo] = answersState[qNo];
            }
        });
        return payload;
    }

    function highlightMissingQuestions(missingArr, targetStepNo) {
        document.querySelectorAll('.q-item-container.has-missing').forEach(el => el.classList.remove('has-missing'));
        if (!Array.isArray(missingArr) || missingArr.length === 0) return;

        if (targetStepNo && targetStepNo !== currentStep) {
            showStep(targetStepNo, false);
        }

        missingArr.forEach(qNo => {
            document.getElementById('qBlock_' + qNo)?.classList.add('has-missing');
        });

        const firstEl = document.getElementById('qBlock_' + missingArr[0]);
        if (firstEl) {
            firstEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    async function persistCurrentStep(mode, targetStep) {
        if (isSaving) return false;
        isSaving = true;

        const ind = document.getElementById('draftSaveIndicator');
        if (ind) ind.textContent = 'Menyimpan...';

        const btnNext = document.getElementById('btnNextStep');
        const btnDraft = document.getElementById('btnSaveDraft');
        if (btnNext) btnNext.disabled = true;
        if (btnDraft) btnDraft.disabled = true;

        try {
            const stepAnswers = collectStepAnswersPayload(currentStep);
            const res = await postForm(URLS.saveStep, {
                step: currentStep,
                target_step: targetStep,
                mode: mode,
                answers_json: JSON.stringify(stepAnswers)
            });

            if (res.status === 'already_submitted' && res.redirect_url) {
                clearLocalBackup();
                window.location.href = res.redirect_url;
                return false;
            }

            if (res.status === 'session_expired') {
                saveLocalBackup();
                await showDialog({
                    title: 'Sesi Perlu Dipulihkan',
                    text: res.message || 'Masukkan kembali 18 digit NIP Anda untuk melanjutkan.',
                    icon: 'warning'
                });
                document.getElementById('surveyWizardView')?.classList.add('d-none');
                document.getElementById('identityCheckView')?.classList.remove('d-none');
                return false;
            }

            if (res.answers) {
                answersState = Object.assign({}, res.answers);
            }
            if (res.progress) {
                renderProgressHeader(res.progress);
            }

            if (res.status === 'incomplete_step') {
                if (ind) ind.textContent = 'Draf tersimpan sebagian';
                highlightMissingQuestions(res.missing || [], res.step || currentStep);
                await showDialog({
                    title: 'Belum Lengkap',
                    text: res.message || 'Mohon lengkapi pertanyaan pada bagian ini terlebih dahulu.',
                    icon: 'warning'
                });
                return false;
            }

            if (res.status === 'success') {
                clearLocalBackup();
                if (ind) ind.textContent = 'Draf tersimpan';

                const nm = (answersState[1]?.teks || '').trim();
                const lblNama = document.getElementById('labelRespondenNama');
                if (lblNama && nm) lblNama.textContent = '· ' + nm;

                if (mode === 'draft') {
                    await showDialog({
                        title: 'Tersimpan',
                        text: 'Draf jawaban Anda berhasil disimpan. Anda dapat melanjutkan kapan saja.',
                        icon: 'success'
                    });
                } else {
                    document.querySelectorAll('.q-item-container.has-missing').forEach(el => el.classList.remove('has-missing'));
                    showStep(targetStep, true);
                }
                return true;
            }

            await showDialog({
                title: 'Periksa Input',
                text: res.message || 'Gagal menyimpan jawaban. Silakan coba lagi.',
                icon: 'error'
            });
            return false;
        } catch (err) {
            saveLocalBackup();
            if (ind) ind.textContent = 'Tersimpan di peramban';
            await showDialog({
                title: 'Gagal Menyimpan',
                text: 'Koneksi sedang tidak stabil. Jawaban sementara disimpan di peramban Anda, silakan coba lagi.',
                icon: 'error'
            });
            return false;
        } finally {
            isSaving = false;
            if (btnNext) btnNext.disabled = false;
            if (btnDraft) btnDraft.disabled = false;
        }
    }

    async function handleFinalSubmit() {
        if (isSaving) return;

        const chkDecl = document.getElementById('chkPernyataanTanggungJawab');
        if (!chkDecl || !chkDecl.checked) {
            flagMissingDeclaration();
            await showDialog({
                title: 'Pernyataan Belum Dicentang',
                text: 'Centang pernyataan tanggung jawab sebelum mengirim survei.',
                icon: 'warning'
            });
            return;
        }

        const confirmRes = await showDialog({
            title: 'Kirim Survei?',
            text: 'Jawaban yang dikirim akan dikunci sesuai pernyataan Anda.',
            icon: 'question',
            showCancel: true,
            confirmText: 'Ya, Kirim',
            cancelText: 'Batal',
            confirmClass: 'btn-success'
        });

        if (!confirmRes.isConfirmed) return;

        if (!chkDecl.checked) {
            flagMissingDeclaration();
            return;
        }

        isSaving = true;
        const btnSubmit = document.getElementById('btnSubmitFinal');
        if (btnSubmit) btnSubmit.disabled = true;

        try {
            const res = await postForm(URLS.submitFinal, {
                step: currentStep,
                pernyataan_setuju: chkDecl.checked ? '1' : '0',
                answers_json: JSON.stringify(collectAllAnswersPayload())
            });

            if (res.progress) {
                renderProgressHeader(res.progress);
            }

            if (res.status === 'incomplete_survey') {
                highlightMissingQuestions(res.missing || [], res.step || 1);
                await showDialog({
                    title: 'Jawaban Belum Lengkap',
                    text: res.message || 'Masih terdapat pertanyaan yang belum diisi.',
                    icon: 'warning'
                });
                return;
            }

            if (res.status === 'missing_declaration') {
                flagMissingDeclaration();
                await showDialog({
                    title: 'Pernyataan Belum Dicentang',
                    text: res.message || 'Centang pernyataan tanggung jawab sebelum mengirim survei.',
                    icon: 'warning'
                });
                return;
            }

            if ((res.status === 'success' || res.status === 'already_submitted') && res.redirect_url) {
                clearLocalBackup();
                window.location.href = res.redirect_url;
                return;
            }

            await showDialog({
                title: 'Gagal Mengirim',
                text: res.message || 'Gagal mengirim survei. Silakan coba lagi.',
                icon: 'error'
            });
        } catch (err) {
            saveLocalBackup();
            await showDialog({
                title: 'Gagal Mengirim',
                text: 'Silakan periksa koneksi Anda lalu coba kirim kembali.',
                icon: 'error'
            });
        } finally {
            isSaving = false;
            if (btnSubmit) btnSubmit.disabled = false;
        }
    }

    // =====================================================================
    // INISIALISASI EVENT LISTENERS FORMULIR & MODAL
    // =====================================================================
    document.addEventListener('DOMContentLoaded', function () {
        // Flatpickr Tanggal Mulai Bekerja (Nomor 9)
        const q9Input = document.getElementById('qInput_9');
        if (q9Input && typeof flatpickr !== 'undefined') {
            q9Picker = flatpickr(q9Input, {
                locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                maxDate: 'today',
                allowInput: false,
                disableMobile: false,
                onChange: function (selectedDates, dateStr) {
                    answersState[9] = { teks: dateStr, angka: null, pilihan: [], lainnya: '' };
                    const q10 = document.getElementById('qInput_10');
                    if (q10) q10.value = computeTenureDisplay(dateStr);
                    document.getElementById('qBlock_9')?.classList.remove('has-missing');
                    saveLocalBackup();
                }
            });
        }

        // Input NIP 18 Digit Tahap 0
        const inpId = document.getElementById('inputNomorIdentitas');
        const badgeDigits = document.getElementById('digitCountBadge');
        if (inpId) {
            inpId.addEventListener('input', function () {
                const digits = this.value.replace(/\D+/g, '');
                if (this.value !== digits) this.value = digits;
                if (badgeDigits) {
                    badgeDigits.textContent = digits.length + ' digit';
                    badgeDigits.className = (digits.length === 18)
                        ? 'badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill tabular-nums'
                        : 'badge bg-light text-secondary border rounded-pill tabular-nums';
                }
            });
        }

        // Submit Cek Identitas (Khusus 18 Digit NIP ASN)
        const formCek = document.getElementById('formCekIdentitas');
        if (formCek) {
            formCek.addEventListener('submit', async function (e) {
                e.preventDefault();
                const alertBox = document.getElementById('identityAlertBox');
                const btn = document.getElementById('btnCekIdentitas');
                const val = (inpId?.value || '').replace(/\D+/g, '');

                if (alertBox) alertBox.classList.add('d-none');

                if (val.length !== 18) {
                    if (alertBox) {
                        alertBox.className = 'alert alert-warning py-2 px-3 mb-3 rounded-3';
                        alertBox.textContent = 'Masukkan 18 digit NIP pegawai PKTJ.';
                    }
                    return;
                }

                if (btn) btn.disabled = true;
                try {
                    const res = await postForm(URLS.cekIdentitas, {
                        nomor_identitas: val,
                        website_url: document.getElementById('hpWebsiteUrl')?.value || ''
                    });

                    if (res.state === 'ready' || res.state === 'resumed') {
                        mountSession(res.session);
                        return;
                    }

                    if (res.state === 'already_submitted_redirect' && res.redirect_url) {
                        window.location.href = res.redirect_url;
                        return;
                    }

                    if (res.state === 'already_submitted') {
                        if (alertBox) {
                            alertBox.className = 'alert alert-info py-2 px-3 mb-3 rounded-3';
                            alertBox.textContent = res.message || 'Data survei sudah tercatat.';
                        }
                        return;
                    }

                    if (res.state === 'require_asn_login') {
                        document.getElementById('verifAsnNip').value = res.nip || val;
                        document.getElementById('verifAsnPassword').value = '';
                        document.getElementById('verifAsnInfoText').textContent = res.message || 'Masukkan Kata Sandi akun ECC Anda untuk melanjutkan.';
                        document.getElementById('verifAsnErrorBox')?.classList.add('d-none');
                        const modalEl = document.getElementById('modalVerifikasiAsn');
                        if (modalEl && typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getOrCreateInstance(modalEl).show();
                        }
                        return;
                    }

                    if (alertBox) {
                        alertBox.className = 'alert alert-danger py-2 px-3 mb-3 rounded-3';
                        alertBox.textContent = res.message || 'NIP tidak dapat diproses.';
                    }
                } finally {
                    if (btn) btn.disabled = false;
                }
            });
        }

        // Toggle tampil/sembunyi Kata Sandi pada Modal Verifikasi ASN
        const btnTogglePass = document.getElementById('btnToggleVerifPass');
        if (btnTogglePass) {
            btnTogglePass.addEventListener('click', function () {
                const inp = document.getElementById('verifAsnPassword');
                if (!inp) return;
                const isPwd = inp.type === 'password';
                inp.type = isPwd ? 'text' : 'password';
                this.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        }

        // Submit Modal Verifikasi Kata Sandi ECC (ASN)
        const formVerifAsn = document.getElementById('formVerifikasiAsn');
        if (formVerifAsn) {
            formVerifAsn.addEventListener('submit', async function (e) {
                e.preventDefault();
                const nip = document.getElementById('verifAsnNip')?.value || '';
                const pwd = document.getElementById('verifAsnPassword')?.value || '';
                const errBox = document.getElementById('verifAsnErrorBox');
                const btn = document.getElementById('btnSubmitVerifAsn');

                if (errBox) errBox.classList.add('d-none');
                if (!pwd) {
                    if (errBox) {
                        errBox.textContent = 'Masukkan Kata Sandi akun ECC Anda.';
                        errBox.classList.remove('d-none');
                    }
                    return;
                }

                if (btn) btn.disabled = true;
                try {
                    const res = await postForm(URLS.verifikasiAkun, {
                        nomor_identitas: nip,
                        kata_sandi: pwd
                    });

                    if (res.state === 'already_submitted_redirect' && res.redirect_url) {
                        window.location.href = res.redirect_url;
                        return;
                    }

                    if (res.status === 'success' && res.session) {
                        const modalEl = document.getElementById('modalVerifikasiAsn');
                        if (modalEl && typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modalEl)?.hide();
                        }
                        mountSession(res.session);
                        return;
                    }

                    if (errBox) {
                        errBox.textContent = res.message || 'Kata sandi akun ECC tidak sesuai.';
                        errBox.classList.remove('d-none');
                    }
                } finally {
                    if (btn) btn.disabled = false;
                }
            });
        }

        // Perubahan Centang Pernyataan Tanggung Jawab (Bagian X)
        const chkPernyataan = document.getElementById('chkPernyataanTanggungJawab');
        if (chkPernyataan) {
            chkPernyataan.addEventListener('change', function () {
                syncDeclarationState();
            });
        }

        // Delegasi Perubahan Input di Dalam Wizard
        const wizardForm = document.getElementById('formSurveiWizard');
        if (wizardForm) {
            wizardForm.addEventListener('submit', function (e) {
                e.preventDefault();
            });
            wizardForm.addEventListener('input', function (e) {
                const target = e.target;
                const qNo = Number(target.dataset.qno || 0);
                if (!qNo) return;

                answersState[qNo] = answersState[qNo] || { teks: '', angka: null, pilihan: [], lainnya: '' };

                if (target.classList.contains('q-input-text') || target.classList.contains('q-input-textarea')) {
                    answersState[qNo].teks = target.value;
                } else if (target.classList.contains('q-input-number')) {
                    answersState[qNo].teks = target.value;
                    answersState[qNo].angka = target.value !== '' ? Number(target.value) : null;
                } else if (target.classList.contains('q-input-other')) {
                    answersState[qNo].lainnya = target.value;
                } else if (target.classList.contains('q-input-triple')) {
                    const block = document.getElementById('qBlock_' + qNo);
                    const arr = ['', '', ''];
                    block?.querySelectorAll('.q-input-triple').forEach(inp => {
                        const idx = Number(inp.dataset.tripleIndex || 0);
                        arr[idx] = inp.value;
                    });
                    answersState[qNo].pilihan = arr;
                    answersState[qNo].teks = arr.filter(Boolean).join(' | ');
                }

                document.getElementById('qBlock_' + qNo)?.classList.remove('has-missing');
                saveLocalBackup();
            });

            wizardForm.addEventListener('change', function (e) {
                const target = e.target;
                const qNo = Number(target.dataset.qno || 0);
                if (!qNo) return;

                answersState[qNo] = answersState[qNo] || { teks: '', angka: null, pilihan: [], lainnya: '' };

                if (target.classList.contains('q-input-select')) {
                    answersState[qNo].teks = target.value;
                } else if (target.classList.contains('q-input-date')) {
                    answersState[qNo].teks = target.value;
                    const q10 = document.getElementById('qInput_10');
                    if (q10) q10.value = computeTenureDisplay(target.value);
                } else if (target.classList.contains('q-input-radio')) {
                    answersState[qNo].teks = target.value;
                    const block = document.getElementById('qBlock_' + qNo);
                    block?.querySelectorAll('.q-input-radio').forEach(r => {
                        r.closest('.option-card')?.classList.toggle('is-selected', r.checked);
                    });
                    refreshOtherVisibility(qNo);
                    evaluateConditionals();
                    if (answersState[qNo].teks === (block?.dataset.otherValue || 'Lainnya')) {
                        document.getElementById('qOtherInput_' + qNo)?.focus();
                    }
                } else if (target.classList.contains('q-input-checkbox')) {
                    const block = document.getElementById('qBlock_' + qNo);
                    const exclusiveOpt = block?.dataset.exclusive || '';
                    const grp = document.getElementById('qCheckboxGroup_' + qNo);
                    let conflicts = [];
                    try {
                        conflicts = JSON.parse(grp?.dataset.conflicts || '[]');
                    } catch (err) {}

                    // Aturan eksklusif (No. 31 & No. 57)
                    if (exclusiveOpt && target.checked) {
                        if (target.value === exclusiveOpt) {
                            block?.querySelectorAll('.q-input-checkbox').forEach(chk => {
                                if (conflicts.includes(chk.value)) chk.checked = false;
                            });
                        } else if (conflicts.includes(target.value)) {
                            block?.querySelectorAll('.q-input-checkbox').forEach(chk => {
                                if (chk.value === exclusiveOpt) chk.checked = false;
                            });
                        }
                    }

                    const selected = [];
                    block?.querySelectorAll('.q-input-checkbox').forEach(chk => {
                        chk.closest('.option-card')?.classList.toggle('is-selected', chk.checked);
                        if (chk.checked) selected.push(chk.value);
                    });
                    answersState[qNo].pilihan = selected;
                    answersState[qNo].teks = selected.join('; ');
                    refreshCheckboxQuotaAndOther(qNo);

                    const otherVal = block?.dataset.otherValue || 'Lainnya';
                    if (target.checked && target.value === otherVal) {
                        document.getElementById('qOtherInput_' + qNo)?.focus();
                    }
                } else if (target.classList.contains('q-input-scale')) {
                    const score = Number(target.value);
                    const lbl = target.dataset.label || '';
                    answersState[qNo].angka = score;
                    answersState[qNo].teks = lbl;
                    const cap = document.getElementById('matrixCaption_' + qNo);
                    if (cap) cap.textContent = 'Terpilih: ' + score + ' — ' + lbl;
                }

                document.getElementById('qBlock_' + qNo)?.classList.remove('has-missing');
                saveLocalBackup();
            });
        }

        // Klik Tombol Navigasi Wizard
        document.getElementById('btnPrevStep')?.addEventListener('click', function () {
            if (currentStep > 1) {
                persistCurrentStep('prev', currentStep - 1);
            }
        });

        document.getElementById('btnSaveDraft')?.addEventListener('click', function () {
            persistCurrentStep('draft', currentStep);
        });

        document.getElementById('btnNextStep')?.addEventListener('click', function () {
            if (currentStep < 10) {
                persistCurrentStep('next', currentStep + 1);
            }
        });

        document.getElementById('btnSubmitFinal')?.addEventListener('click', function () {
            handleFinalSubmit();
        });

        // Klik Pill Bagian di Atas
        document.querySelectorAll('#sectionStepperBar .step-pill').forEach(pill => {
            pill.addEventListener('click', function () {
                const target = Number(this.dataset.stepTarget || 1);
                if (target === currentStep) return;
                // Jika berpindah maju 1 langkah gunakan validasi, jika mundur / loncat simpan draf ringan
                const mode = (target > currentStep) ? 'next' : 'jump';
                persistCurrentStep(mode, target);
            });
        });

        // Klik Ganti NIP (Untuk Komputer Bersama)
        document.getElementById('btnGantiResponden')?.addEventListener('click', async function () {
            const conf = await showDialog({
                title: 'Ganti NIP?',
                text: 'Progres Anda tetap tersimpan. Halaman akan kembali ke kolom input NIP.',
                icon: 'question',
                showCancel: true,
                confirmText: 'Ya, Ganti',
                cancelText: 'Batal'
            });
            if (!conf.isConfirmed) return;

            const res = await postForm(URLS.keluarSesi, {});
            window.location.href = res.redirect_url || <?= json_encode(site_url('survei-pemetaan?ganti=1')) ?>;
        });

        // Penyegar Token CSRF Otomatis setiap 15 Menit saat Tab Terbuka
        setInterval(function () {
            if (document.hidden) return;
            fetch(URLS.pingCsrf, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(d => updateCsrf(d))
                .catch(() => {});
        }, 900000);

        // Apabila halaman dimuat dengan sesi aktif dari Cookie, langsung pasang!
        if (INITIAL_SESSION) {
            mountSession(INITIAL_SESSION);
        }
    });
})();
</script>
<?php endif; ?>
<?= $this->endSection() ?>

