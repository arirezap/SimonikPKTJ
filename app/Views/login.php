<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Evidence Command Center (ECC)</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/logo_pktj.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.4.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <style>
        body.login-page {
            background-color: #f1f5f9;
            background-image: radial-gradient(at 50% 0%, #e2e8f0 0%, #f1f5f9 65%);
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 1.5rem;
        }

        /* 1. Main Card Container (100% Solid, Stabil & Elegan - Zero Motion) */
        .fit-card-wrapper {
            width: 100%;
            max-width: 820px;
            z-index: 1;
            position: relative;
        }

        .fit-card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.07), 0 0 0 1px rgba(255, 255, 255, 0.9) inset;
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

        /* 3. Form Ergonomics & Stable Controls */
        .fit-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.01em;
            margin-bottom: 0.375rem;
            display: block;
        }
        
        .fit-input {
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.875rem;
            font-size: 0.9rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            height: 42px;
        }
        
        .fit-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            background-color: #ffffff;
            outline: none;
        }

        .fit-input.is-invalid-auth {
            border-color: #ef4444;
            background-color: #fffaf0;
        }
        
        .fit-input.is-invalid-auth:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .fit-toggle-pwd {
            color: #94a3b8;
            padding: 0.35rem 0.6rem;
            border-radius: 0.375rem;
            transition: color 0.15s ease;
        }
        
        .fit-toggle-pwd:hover {
            color: #2563eb;
        }

        .fit-toggle-pwd:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 1px;
        }

        .fit-checkbox {
            width: 1.1rem;
            height: 1.1rem;
            border-radius: 0.3rem;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }
        
        .fit-checkbox:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .fit-checkbox:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }

        .fit-link {
            color: #2563eb;
            transition: color 0.15s ease;
        }
        
        .fit-link:hover {
            color: #1d4ed8;
            text-decoration: underline !important;
        }

        .fit-link:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }

        /* 4. Primary CTA Button (Kokoh, Berbobot, Tanpa Lompat) */
        .fit-btn-primary {
            background-color: #1e3a8a;
            border: 1px solid #1e3a8a;
            color: #ffffff;
            border-radius: 0.5rem;
            padding: 0.7rem 1.25rem;
            font-size: 0.925rem;
            font-weight: 600;
            letter-spacing: 0.015em;
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
            outline: 3px solid rgba(37, 99, 235, 0.4);
            outline-offset: 2px;
        }

        .fit-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            vertical-align: text-bottom;
            margin-right: 0.45rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* 5. Clean Error Alert Box */
        .fit-error-alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 0.5rem;
            padding: 0.65rem 0.85rem;
            font-size: 0.825rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="login-page">

    <main class="fit-card-wrapper" role="main">
        <div class="fit-card bg-white mx-auto d-flex flex-column flex-lg-row overflow-hidden shadow-sm" id="loginCard">
            
            <!-- Left Panel: Illustration -->
            <div class="fit-left-panel d-none d-lg-flex flex-column justify-content-center align-items-center p-4" style="width: 48%;">
                <img src="<?= base_url('assets/login_illustration_new.jpg') ?>" alt="Ilustrasi Kampus Politeknik Keselamatan Transportasi Jalan" class="img-fluid fit-img" style="mix-blend-mode: multiply;" width="360" height="300" loading="eager">
            </div>

            <!-- Right Panel: Login Form -->
            <div class="fit-right-panel p-4 p-sm-5 d-flex flex-column justify-content-center" style="width: 100%; flex: 1;">
                <div class="text-start mb-3">
                    <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ" class="mb-2" style="height: 44px; width: auto;" width="44" height="44">
                    <h1 class="fw-bold text-dark mb-1" style="letter-spacing: -0.025em; font-size: 1.45rem;">Masuk ke Akun</h1>
                    <p class="text-muted mb-0 fw-medium" style="font-size: 0.8125rem;">Evidence Command Center (ECC) &bull; PKTJ Tegal</p>
                </div>

                <!-- Inline Error Alert (Tenang & Jelas Tanpa Efek Gerak) -->
                <?php if (session()->getFlashdata('error')): ?>
                <div class="fit-error-alert d-flex align-items-center gap-2" role="alert" id="authErrorAlert">
                    <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0" style="font-size: 0.95rem;"></i>
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
                               placeholder="Masukkan username..." 
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
                        <div class="position-relative">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control fit-input pe-5 <?= $hasError ? 'is-invalid-auth' : '' ?>" 
                                   placeholder="Masukkan kata sandi..." 
                                   autocomplete="current-password" 
                                   required 
                                   <?= $hasError ? 'aria-describedby="authErrorAlert" aria-invalid="true"' : 'aria-invalid="false"' ?>
                                   <?= !empty($currentUsername) ? 'autofocus' : '' ?>>
                            <button type="button" 
                                    id="togglePassword" 
                                    class="btn btn-link fit-toggle-pwd position-absolute top-50 end-0 translate-middle-y text-decoration-none me-2" 
                                    aria-label="Tampilkan kata sandi">
                                <i class="bi bi-eye-slash" style="font-size: 0.95rem;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check m-0 p-0 d-flex align-items-center gap-2">
                            <input class="form-check-input fit-checkbox m-0" type="checkbox" value="1" id="rememberMe" name="remember" <?= $isRemembered ? 'checked' : '' ?> style="margin-left: 0 !important;">
                            <label class="form-check-label text-muted user-select-none" for="rememberMe" style="cursor: pointer; font-size: 0.825rem; line-height: 1.2;">
                                Ingat sesi
                            </label>
                        </div>
                        <button type="button" class="btn btn-link p-0 text-decoration-none fw-medium fit-link border-0 align-baseline" onclick="forgotPassword();" style="font-size: 0.825rem;">
                            Lupa Password?
                        </button>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn fit-btn-primary d-flex align-items-center justify-content-center" id="loginBtn">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            <span>Masuk</span>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3 pt-1">
                    <p class="text-muted mb-0" style="font-size: 0.75rem; color: #64748b;">&copy; <?= date("Y"); ?> Evidence Command Center (ECC) &bull; PKTJ Tegal &bull; <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.7rem; font-variant-numeric: tabular-nums;">v 1.4</span></p>
                </div>
            </div>
            
        </div>
    </main>

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
                const isPassword = passwordInput.getAttribute('type') === 'password';
                const newType = isPassword ? 'text' : 'password';
                passwordInput.setAttribute('type', newType);
                this.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');

                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye-slash', !isPassword);
                    icon.classList.toggle('bi-eye', isPassword);
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
                        btn.innerHTML = '<span class="fit-spinner"></span> <span>Memproses...</span>';
                        btn.style.opacity = '0.9';
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
                title: 'Lupa Kata Sandi?',
                text: 'Silakan hubungi Administrator atau Unit Kepegawaian untuk mereset kata sandi akun Anda.',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Mengerti',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-muted small mb-3',
                    confirmButton: 'btn btn-primary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm'
                },
                buttonsStyling: false
            });
        } else {
            alert('Lupa Kata Sandi?\n\nSilakan hubungi Administrator atau Unit Kepegawaian untuk mereset kata sandi akun Anda.');
        }
    }

    // Tampilkan Toast Elegan Jika Ada Sukses (Misal: setelah logout) - Non-blocking
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