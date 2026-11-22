<?php
/**
 * Modal Bagikan Tautan Survei Tracer Study (Tautan Publik Tanpa Login + Tautan Login ECC).
 */
$canManageShare = !empty($perms['can_manage']);
?>
<div class="modal fade" id="modalShareTracerLink" tabindex="-1" aria-labelledby="modalShareTracerLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h2 class="modal-title h6 fw-bold text-dark mb-0" id="modalShareTracerLinkLabel">Bagikan Tautan Survei Alumni</h2>
                    <div class="ecc-text-muted-aa small" id="shareModalPeriodeName"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <div id="shareStatusWarning" class="alert alert-warning border-0 rounded-3 py-2 px-3 small d-none mb-3">
                    Periode ini belum berstatus <strong>Dibuka</strong>. Alumni baru dapat mengisi setelah survei dibuka.
                </div>

                <!-- 1. Tautan Publik Tanpa Login -->
                <div class="mb-3">
                    <label for="sharePublicUrlInput" class="form-label fw-semibold small text-dark mb-1">
                        1. Tautan Publik (Tanpa Login — Verifikasi Notar &amp; Tanggal Lahir)
                    </label>
                    <div class="input-group">
                        <input type="text" id="sharePublicUrlInput" class="form-control ecc-control-sm bg-light ecc-num" readonly>
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" onclick="copyShareInput('sharePublicUrlInput', this)">Salin</button>
                    </div>
                    <div class="form-text" style="font-size: 0.75rem;">
                        Cocok disebar ke grup WhatsApp atau email alumni. Otomatis tertutup saat periode ditutup.
                    </div>
                </div>

                <!-- 2. Tautan Login Akun ECC -->
                <div class="mb-3">
                    <label for="shareLoginUrlInput" class="form-label fw-semibold small text-dark mb-1">
                        2. Tautan Khusus Akun Login ECC Alumni
                    </label>
                    <div class="input-group">
                        <input type="text" id="shareLoginUrlInput" class="form-control ecc-control-sm bg-light ecc-num" readonly>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-semibold" onclick="copyShareInput('shareLoginUrlInput', this)">Salin</button>
                    </div>
                    <div class="form-text" style="font-size: 0.75rem;">
                        Alumni yang masuk menggunakan akun ECC langsung diarahkan ke formulir survei ini.
                    </div>
                </div>

                <?php if ($canManageShare): ?>
                    <div class="pt-2 border-top d-flex justify-content-between align-items-center gap-2">
                        <span class="ecc-text-muted-aa" style="font-size: 0.75rem;">Ingin membatalkan tautan publik lama?</span>
                        <form method="post" action="#" id="formResetPublicToken" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size: 0.75rem;">
                                Buat Ulang Token
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <div class="modal-footer border-top px-4 py-3">
                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function openShareTracerModal(btn) {
    var d = btn.dataset;
    var nameEl = document.getElementById('shareModalPeriodeName');
    if (nameEl) nameEl.textContent = d.nama || '';

    var pubIn = document.getElementById('sharePublicUrlInput');
    var logIn = document.getElementById('shareLoginUrlInput');
    if (pubIn) pubIn.value = d.publicUrl || '';
    if (logIn) logIn.value = d.loginUrl || '';

    var warn = document.getElementById('shareStatusWarning');
    if (warn) warn.classList.toggle('d-none', d.status === 'dibuka');

    var resetForm = document.getElementById('formResetPublicToken');
    if (resetForm && d.resetUrl) {
        resetForm.action = d.resetUrl;
    }

    var modalEl = document.getElementById('modalShareTracerLink');
    if (window.bootstrap && modalEl) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function copyShareInput(inputId, btnEl) {
    var input = document.getElementById(inputId);
    if (!input || !input.value) return;
    var origText = btnEl.textContent;
    var done = function() {
        btnEl.textContent = 'Tersalin';
        setTimeout(function() { btnEl.textContent = origText; }, 1600);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value).then(done).catch(function() {
            input.select();
            document.execCommand('copy');
            done();
        });
    } else {
        input.select();
        document.execCommand('copy');
        done();
    }
}
</script>
