<?= $this->extend('layouts/ecc2_layout') ?>

<?= $this->section('title') ?>Ubah Kata Sandi<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .akun-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .btn-toggle-pw {
        min-width: 44px;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-color: #dee2e6;
        color: #64748b;
    }
    .btn-toggle-pw:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0" style="max-width: 720px;">
    <div class="mb-3">
        <a href="<?= esc($backUrl ?? site_url('ketarunaan/taruna')) ?>" class="text-decoration-none small text-primary fw-semibold d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Profil Saya</span>
        </a>
        <h1 class="h3 fw-bold text-dark mt-2 mb-1">Ubah Kata Sandi</h1>
        <p class="text-muted small mb-0">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-3 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between small">
            <div><i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger border-0 rounded-3 shadow-sm py-2 px-3 mb-3 small">
            <ul class="mb-0 ps-3">
                <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="akun-card p-3 p-md-4">
        <form method="post" action="<?= site_url('ketarunaan/keamanan-akun/kata-sandi') ?>">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold text-dark">Nama Pengguna</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light fw-semibold" value="<?= esc($user['username'] ?? '') ?>" readonly style="height: 40px;">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                    </div>
                </div>

                <div class="col-12">
                    <label for="currentPassword" class="form-label small fw-semibold text-dark">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="currentPassword" class="form-control border-end-0" required autocomplete="current-password" placeholder="Masukkan kata sandi saat ini" style="height: 40px;">
                        <button type="button" class="btn btn-outline-secondary btn-toggle-pw border-start-0" data-target="currentPassword" aria-label="Tampilkan atau sembunyikan kata sandi saat ini">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="newPassword" class="form-label small fw-semibold text-dark">Kata Sandi Baru <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="newPassword" class="form-control border-end-0" required minlength="6" maxlength="100" autocomplete="new-password" placeholder="Minimal 6 karakter" style="height: 40px;">
                        <button type="button" class="btn btn-outline-secondary btn-toggle-pw border-start-0" data-target="newPassword" aria-label="Tampilkan atau sembunyikan kata sandi baru">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="confirmPassword" class="form-label small fw-semibold text-dark">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password_confirm" id="confirmPassword" class="form-control border-end-0" required minlength="6" maxlength="100" autocomplete="new-password" placeholder="Ulangi kata sandi baru" style="height: 40px;">
                        <button type="button" class="btn btn-outline-secondary btn-toggle-pw border-start-0" data-target="confirmPassword" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end pt-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-tactile" style="height: 40px;">
                        Simpan Kata Sandi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-toggle-pw').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.getAttribute('data-target'));
            if (!target) return;
            const isHidden = target.type === 'password';
            target.type = isHidden ? 'text' : 'password';
            const icon = this.querySelector('i');
            if (icon) {
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
