<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ECC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
</head>
<body class="login-page d-flex align-items-center justify-content-center vh-100 position-relative">

    <div class="container px-3 px-md-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card login-card mx-auto">
                    <div class="row g-0 h-100">
                        <!-- Panel Kiri: Branding (Disembunyikan di layar kecil) -->
                        <div class="col-md-5 col-lg-5 d-none d-md-flex flex-column justify-content-center align-items-center login-brand p-5 text-center">
                            <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" class="mb-4" style="width: 130px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));">
                            <h3 class="fw-bold mb-2" style="letter-spacing: 1px;">ECC</h3>
                            <h6 class="mb-0 text-white-50 text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px;">Evidence Command Center</h6>
                        </div>

                        <!-- Panel Kanan: Form Login -->
                        <div class="col-md-7 col-lg-7 p-4 p-sm-5 bg-white">
                            <div class="d-md-none text-center mb-4">
                                <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" style="width: 90px;">
                                <h5 class="fw-bold mt-3 mb-1 text-primary">ECC</h5>
                                <p class="text-muted small mb-0">Evidence Command Center</p>
                            </div>

                            <div class="mb-4 pb-3 border-bottom text-center text-md-start">
                                <h4 class="fw-bold text-dark mb-2">Log In</h4>
                                <p class="text-muted small mb-0">Silakan masukkan kredensial Anda untuk mengakses sistem.</p>
                            </div>

                            <form action="<?= base_url('login') ?>" method="POST" autocomplete="off" id="loginForm">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="username" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px;">Nama Pengguna</label>
                                    <div class="input-group-icon">
                                        <i class="bi bi-person-fill form-icon"></i>
                                        <input type="text" name="username" id="username" class="form-control" placeholder="Ketik username Anda..." value="<?= old('username') ?>" required autofocus>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label small fw-bold text-secondary text-uppercase" style="letter-spacing: 0.5px;">Kata Sandi</label>
                                    <div class="input-group-icon">
                                        <i class="bi bi-lock-fill form-icon"></i>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Ketik kata sandi Anda..." required>
                                        <button type="button" id="togglePassword" class="password-toggle-btn" tabindex="-1" title="Tampilkan/Sembunyikan Password">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember">
                                        <label class="form-check-label text-muted small" for="rememberMe" style="cursor: pointer;">
                                            Ingat sesi saya
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none small fw-medium text-primary" onclick="alert('Silakan hubungi Administrator untuk mereset password Anda.'); return false;">Lupa Password?</a>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-login d-flex justify-content-center align-items-center gap-2" id="loginBtn">
                                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="login-footer">
        &copy; <?= date("Y"); ?> Politeknik Keselamatan Transportasi Jalan. Hak Cipta Dilindungi.
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordButton = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePasswordButton) {
            togglePasswordButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye-slash');
                icon.classList.toggle('bi-eye');
            });
        }

        // Loading state pada tombol login
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            btn.disabled = true;
        });
    });

    // Tampilkan Popup SweetAlert Jika Ada Error
    <?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Login Gagal!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#0d6efd'
    });
    <?php endif; ?>

    // Tampilkan Popup SweetAlert Jika Ada Sukses (Misal: setelah logout)
    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#0d6efd'
    });
    <?php endif; ?>
</script>

</body>
</html>