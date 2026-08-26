<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Evidence Command Center (ECC)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.2.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <style>
        body.login-page {
            background: #f8fafc;
            background: radial-gradient(circle at 50% 20%, #eff6ff 0%, #f8fafc 80%);
        }
        .fit-card {
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07), 0 0 0 1px rgba(0, 0, 0, 0.02);
            animation: loginCardFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes loginCardFadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fit-left-panel {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
            border-right: 1px solid #e2e8f0;
        }
        .fit-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.2px;
            margin-bottom: 0.35rem;
        }
        .fit-input {
            border-radius: 0.6rem;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.85rem;
            font-size: 0.9rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .fit-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            background-color: #ffffff;
        }
        .fit-toggle-pwd {
            color: #94a3b8;
            padding: 0.25rem 0.5rem;
            transition: color 0.15s ease;
        }
        .fit-toggle-pwd:hover {
            color: #2563eb;
        }
        .fit-checkbox {
            width: 1.05rem;
            height: 1.05rem;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
        }
        .fit-checkbox:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        .fit-link {
            color: #2563eb;
            transition: color 0.15s ease;
        }
        .fit-link:hover {
            color: #1d4ed8;
            text-decoration: underline !important;
        }
        .fit-btn-primary {
            background-color: #1e3a8a;
            border: 1px solid #1e3a8a;
            color: #ffffff;
            border-radius: 0.6rem;
            padding: 0.7rem 1.25rem;
            font-size: 0.92rem;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .fit-btn-primary:hover {
            background-color: #172554;
            border-color: #172554;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.28);
        }
        .fit-btn-primary:active {
            transform: translateY(0);
        }
        .fit-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid #ffffff;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            vertical-align: text-bottom;
            margin-right: 0.35rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="login-page m-0 p-0 d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <div class="container px-3">
        <div class="fit-card bg-white mx-auto d-flex flex-column flex-lg-row overflow-hidden" style="max-width: 800px; width: 100%;">
            
            <!-- Left Panel: Illustration -->
            <div class="fit-left-panel d-none d-lg-flex flex-column justify-content-center align-items-center p-3" style="width: 50%;">
                <img src="<?= base_url('assets/login_illustration_new.jpg') ?>" alt="Ilustrasi Kampus PKTJ" class="img-fluid fit-img" style="mix-blend-mode: multiply; max-height: 320px; object-fit: contain;" width="360" height="320" loading="eager">
            </div>

            <!-- Right Panel: Login Form -->
            <div class="fit-right-panel p-4 d-flex flex-column justify-content-center" style="width: 100%; flex: 1;">
                <div class="text-start mb-3">
                    <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" class="mb-2" style="height: 45px; width: auto;" width="45" height="45">
                    <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em; font-size: 1.4rem;">Login</h2>
                    <p class="text-muted mb-0 fw-medium" style="font-size: 0.8125rem; letter-spacing: 0.01em;">Evidence Command Center (ECC)</p>
                </div>

                <?php 
                    $currentUsername = old('username') ?? ($savedUsername ?? ''); 
                    $isRemembered = !empty(old('remember')) || !empty($savedUsername);
                ?>
                <form action="<?= base_url('login') ?>" method="POST" autocomplete="off" id="loginForm">
                    <?= csrf_field() ?>

                    <div class="mb-2">
                        <label for="username" class="form-label fit-label">Nama Pengguna</label>
                        <input type="text" name="username" id="username" class="form-control fit-input" placeholder="Masukkan username..." value="<?= esc($currentUsername) ?>" required <?= empty($currentUsername) ? 'autofocus' : '' ?>>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fit-label">Kata Sandi</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control fit-input pe-5" placeholder="Masukkan kata sandi..." required <?= !empty($currentUsername) ? 'autofocus' : '' ?>>
                            <button type="button" id="togglePassword" class="btn btn-link fit-toggle-pwd position-absolute top-50 end-0 translate-middle-y text-decoration-none me-2" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i class="bi bi-eye-slash" style="font-size: 0.95rem;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check m-0 p-0 d-flex align-items-center gap-2">
                            <input class="form-check-input fit-checkbox m-0" type="checkbox" value="1" id="rememberMe" name="remember" <?= $isRemembered ? 'checked' : '' ?> style="margin-left: 0 !important;">
                            <label class="form-check-label text-muted user-select-none" for="rememberMe" style="cursor: pointer; font-size: 0.8rem; line-height: 1;">
                                Ingat sesi
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none fw-medium fit-link" onclick="forgotPassword(); return false;" style="font-size: 0.8rem;">Lupa Password?</a>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn fit-btn-primary fw-bold" id="loginBtn">
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="text-muted mb-0" style="font-size: 0.75rem; color: #64748b;">&copy; <?= date("Y"); ?> PKTJ Tegal. Hak Cipta Dilindungi.</p>
                </div>
            </div>
            
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordButton = document.getElementById('togglePassword');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        // Otomatis fokus ke input password jika username sudah terisi
        if (usernameInput && passwordInput && usernameInput.value.trim() !== '') {
            passwordInput.focus();
        }

        if (togglePasswordButton && passwordInput) {
            togglePasswordButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye-slash');
                    icon.classList.toggle('bi-eye');
                }
            });
        }

        // Loading state pada tombol login (hanya jika form valid)
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (loginForm.checkValidity()) {
                    const btn = document.getElementById('loginBtn');
                    if (btn) {
                        btn.innerHTML = '<span class="fit-spinner"></span> <span style="letter-spacing: 0.05em; font-weight: 500;">Memproses...</span>';
                        btn.style.opacity = '0.85';
                        btn.disabled = true;
                    }
                }
            });
        }
    });

    function forgotPassword() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Lupa Password?',
                text: 'Silakan hubungi Administrator untuk mereset password Anda.',
                confirmButtonColor: '#1e3a8a',
                confirmButtonText: 'Mengerti'
            });
        } else {
            alert('Lupa Password?\n\nSilakan hubungi Administrator untuk mereset password Anda.');
        }
    }

    // Tampilkan Popup SweetAlert Jika Ada Error
    <?php if (session()->getFlashdata('error')): ?>
    const errText = '<?= esc(session()->getFlashdata('error'), 'js') ?>';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal!',
            text: errText,
            confirmButtonColor: '#1e3a8a'
        });
    } else {
        alert('Login Gagal!\n\n' + errText);
    }
    <?php endif; ?>

    // Tampilkan Popup SweetAlert Jika Ada Sukses (Misal: setelah logout)
    <?php if (session()->getFlashdata('success')): ?>
    const successText = '<?= esc(session()->getFlashdata('success'), 'js') ?>';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: successText,
            confirmButtonColor: '#1e3a8a'
        });
    } else {
        alert('Berhasil!\n\n' + successText);
    }
    <?php endif; ?>
</script>

</body>
</html>