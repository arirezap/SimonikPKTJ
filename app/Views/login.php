<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>Masuk ke Akun - Evidence Command Center (ECC)</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/logo_pktj.png') ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=2.1.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <style>
        body.login-page {
            background-color: #f1f5f9;
            background-image: radial-gradient(at 50% 0%, #e2e8f0 0%, #f1f5f9 65%);
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }

        /* 1. Main Card Container (Solid & Stabil — 8-Point Grid) */
        .fit-card-wrapper {
            width: 100%;
            max-width: 824px;
            z-index: 1;
            position: relative;
        }

        .fit-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.9) inset;
            background: #ffffff;
        }

        /* 2. Left Panel & Static Illustration */
        .fit-left-panel {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-right: 1px solid #e2e8f0;
            position: relative;
        }

        .fit-img {
            max-height: 300px;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(15, 23, 42, 0.06));
        }

        .fit-heading {
            font-size: 1.75rem;
            line-height: 1.25;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .fit-subtitle {
            font-size: 0.875rem;
            color: #475569;
        }

        /* 3. Form Ergonomics & Stable Controls (4px / 8-Point Grid) */
        .fit-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            display: block;
        }

        .fit-input {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 0.875rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            height: 44px;
        }

        .fit-input-password {
            padding-right: 48px;
        }

        .fit-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            background-color: #ffffff;
            outline: none;
        }

        .fit-input.is-invalid-auth {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .fit-input.is-invalid-auth:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        /* Centered without transform so prefers-reduced-motion never shifts it */
        .fit-password-wrap {
            position: relative;
        }

        .fit-toggle-pwd {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 4px;
            margin: auto 0;
            height: 36px;
            min-width: 44px;
            padding: 4px 8px;
            border: 0;
            background: transparent;
            border-radius: 8px;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: color 0.15s ease, background-color 0.15s ease;
        }

        .fit-toggle-pwd:hover {
            color: #0d6efd;
            background-color: #f1f5f9;
        }

        .fit-toggle-pwd:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 1px;
        }

        .fit-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .fit-checkbox:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .fit-checkbox:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }

        .fit-option-label {
            cursor: pointer;
            font-size: 0.875rem;
            color: #475569;
            line-height: 1.25;
        }

        .fit-link {
            color: #1e40af;
            font-size: 0.875rem;
            transition: color 0.15s ease;
        }

        .fit-link:hover {
            color: #1e3a8a;
            text-decoration: underline !important;
        }

        .fit-link:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* 4. Primary CTA Button */
        .fit-btn-primary {
            background-color: #1e3a8a;
            border: 1px solid #1e3a8a;
            color: #ffffff;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.12);
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
            min-height: 44px;
        }

        .fit-btn-primary:hover {
            background-color: #172554;
            border-color: #172554;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(30, 58, 138, 0.2);
        }

        .fit-btn-primary:active {
            background-color: #0f172a;
            border-color: #0f172a;
            transform: scale(0.99);
        }

        .fit-btn-primary:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.35);
        }

        .fit-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: text-bottom;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* 5. Clean Error Alert Box */
        .fit-error-alert {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.875rem;
            line-height: 1.4;
            margin-bottom: 16px;
        }

        .fit-footer {
            font-size: 0.75rem;
            color: #475569;
        }

        .fit-version-badge {
            font-size: 0.75rem;
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }

        @media (max-width: 767.98px) {
            body.login-page {
                padding: 16px;
            }
            .fit-input {
                font-size: var(--bs-body-font-size, 1rem);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .fit-spinner,
            .fit-input,
            .fit-toggle-pwd,
            .fit-checkbox,
            .fit-link,
            .fit-btn-primary {
                animation: none !important;
                transition: none !important;
            }
            .fit-btn-primary:active {
                transform: none !important;
            }
        }
    </style>
</head>
<body class="login-page">

    <main class="fit-card-wrapper" role="main">
        <div class="fit-card bg-white mx-auto d-flex flex-column flex-lg-row overflow-hidden shadow-sm" id="loginCard">
            
            <!-- Left Panel: Illustration -->
            <div class="fit-left-panel d-none d-lg-flex flex-column justify-content-center align-items-center p-4" style="width: 48%;">
                <img src="<?= base_url('assets/login_illustration_new.jpg') ?>" alt="Ilustrasi Kampus Politeknik Keselamatan Transportasi Jalan" class="img-fluid fit-img" style="mix-blend-mode: multiply;" width="360" height="300" loading="eager" fetchpriority="high">
            </div>

            <!-- Right Panel: Login Form -->
            <div class="fit-right-panel p-4 p-sm-5 d-flex flex-column justify-content-center" style="width: 100%; flex: 1;">
                <div class="text-start mb-3">
                    <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" class="mb-2" style="height: 44px; width: auto;" width="44" height="44" decoding="async">
                    <h1 class="fw-bold mb-1 fit-heading">Masuk ke Akun</h1>
                    <p class="mb-0 fw-medium fit-subtitle">Evidence Command Center (ECC) &middot; PKTJ Tegal</p>
                </div>

                <!-- Inline Error Alert -->
                <?php if (session()->getFlashdata('error')): ?>
                <div class="fit-error-alert d-flex align-items-center gap-2" role="alert" id="authErrorAlert">
                    <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0" style="font-size: 0.95rem;" aria-hidden="true"></i>
                    <div class="fw-medium">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php 
                    $currentUsername = old('username') ?? ($savedUsername ?? ''); 
                    $isRemembered = !empty(old('remember')) || !empty($savedUsername);
                    $hasError = (bool) session()->getFlashdata('error');
                ?>
                <form action="<?= site_url('login') ?>" method="POST" autocomplete="on" id="loginForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fit-label">Nama Pengguna</label>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-control fit-input <?= $hasError ? 'is-invalid-auth' : '' ?>" 
                               placeholder="Masukkan nama pengguna..." 
                               value="<?= esc($currentUsername) ?>" 
                               autocomplete="username" 
                               autocapitalize="none" 
                               spellcheck="false" 
                               required 
                               <?= $hasError ? 'aria-describedby="authErrorAlert" aria-invalid="true"' : 'aria-invalid="false"' ?>
                               <?= empty($currentUsername) ? 'autofocus' : '' ?>>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fit-label">Kata Sandi</label>
                        <div class="fit-password-wrap">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control fit-input fit-input-password <?= $hasError ? 'is-invalid-auth' : '' ?>" 
                                   placeholder="Masukkan kata sandi..." 
                                   autocomplete="current-password" 
                                   required 
                                   <?= $hasError ? 'aria-describedby="authErrorAlert" aria-invalid="true"' : 'aria-invalid="false"' ?>
                                   <?= !empty($currentUsername) ? 'autofocus' : '' ?>>
                            <button type="button" 
                                    id="togglePassword" 
                                    class="fit-toggle-pwd" 
                                    aria-label="Tampilkan kata sandi"
                                    aria-pressed="false">
                                <i class="bi bi-eye-slash" style="font-size: 0.95rem;" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                        <div class="form-check m-0 p-0 d-flex align-items-center gap-2">
                            <input class="form-check-input fit-checkbox m-0" type="checkbox" value="1" id="rememberMe" name="remember" <?= $isRemembered ? 'checked' : '' ?> style="margin-left: 0 !important;">
                            <label class="form-check-label user-select-none fit-option-label" for="rememberMe">
                                Ingat sesi
                            </label>
                        </div>
                        <button type="button" id="btnForgotPassword" class="btn btn-link p-0 text-decoration-none fw-medium fit-link border-0 align-baseline">
                            Lupa Kata Sandi?
                        </button>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn fit-btn-primary d-flex align-items-center justify-content-center" id="loginBtn">
                            <i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i>
                            <span>Masuk</span>
                        </button>
                    </div>
                </form>

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mt-4 pt-1 fit-footer">
                    <span>&copy; <?= date('Y') ?> Evidence Command Center (ECC) &middot; PKTJ Tegal</span>
                    <span class="badge bg-light text-secondary-emphasis border rounded-pill px-2 py-1 fit-version-badge">v 2.0</span>
                </div>
            </div>
            
        </div>
    </main>

<script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordButton = document.getElementById('togglePassword');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const btnForgotPassword = document.getElementById('btnForgotPassword');
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const defaultLoginBtnHtml = loginBtn ? loginBtn.innerHTML : '';
        let isSubmitting = false;

        // Otomatis fokus ke input kata sandi jika nama pengguna sudah terisi
        if (usernameInput && passwordInput && usernameInput.value.trim() !== '') {
            passwordInput.focus();
        }

        if (togglePasswordButton && passwordInput) {
            togglePasswordButton.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                this.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                this.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye-slash', !isPassword);
                    icon.classList.toggle('bi-eye', isPassword);
                }
            });
        }

        if (btnForgotPassword) {
            btnForgotPassword.addEventListener('click', forgotPassword);
        }

        // Loading state pada tombol masuk (anti-double submit + reset saat kembali via bfcache)
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                if (loginForm.checkValidity()) {
                    isSubmitting = true;
                    if (loginBtn) {
                        loginBtn.innerHTML = '<span class="fit-spinner" aria-hidden="true"></span><span>Memproses...</span>';
                        loginBtn.style.opacity = '0.9';
                        setTimeout(function() {
                            loginBtn.disabled = true;
                        }, 10);
                    }
                }
            });
        }

        window.addEventListener('pageshow', function () {
            isSubmitting = false;
            if (loginBtn && defaultLoginBtnHtml) {
                loginBtn.disabled = false;
                loginBtn.style.opacity = '';
                loginBtn.innerHTML = defaultLoginBtnHtml;
            }
        });
    });

    function forgotPassword() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Lupa Kata Sandi?',
                text: 'Hubungi Administrator atau Bagian Kepegawaian untuk mengatur ulang kata sandi akun Anda.',
                confirmButtonText: '<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Mengerti',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-muted small mb-3',
                    confirmButton: 'btn btn-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm'
                },
                buttonsStyling: false
            });
        } else {
            alert('Lupa Kata Sandi?\n\nHubungi Administrator atau Bagian Kepegawaian untuk mengatur ulang kata sandi akun Anda.');
        }
    }

    // Toast non-blocking setelah keluar dari ECC
    <?php if (session()->getFlashdata('success')): ?>
    const successText = '<?= esc(session()->getFlashdata('success'), 'js') ?>';
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'rounded-4 shadow border-0'
            }
        });
        Toast.fire({
            icon: 'success',
            title: 'Berhasil Keluar',
            text: successText
        });
    } else {
        alert('Berhasil Keluar!\n\n' + successText);
    }
    <?php endif; ?>
</script>

</body>
</html>