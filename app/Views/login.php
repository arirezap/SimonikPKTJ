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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.2.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <style>
        body.login-page {
            background-color: #f8fafc;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 1. Ambient Dynamic Light Orbs in Background */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.65;
            transition: transform 0.3s ease-out;
        }
        .ambient-orb-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, rgba(30, 58, 138, 0.05) 70%, transparent 100%);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            animation: orbFloat1 12s ease-in-out infinite alternate;
        }
        .ambient-orb-2 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.12) 0%, rgba(219, 234, 254, 0.15) 70%, transparent 100%);
            bottom: -60px;
            right: 5%;
            animation: orbFloat2 15s ease-in-out infinite alternate;
        }
        .ambient-orb-3 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            bottom: 10%;
            left: 5%;
            animation: orbFloat1 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes orbFloat1 {
            0% { transform: translate(-50%, 0) scale(1); }
            50% { transform: translate(-45%, 30px) scale(1.08); }
            100% { transform: translate(-55%, -20px) scale(0.95); }
        }
        @keyframes orbFloat2 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-40px, -30px) scale(1.12); }
        }

        /* 2. Main Card Container & 3D Perspective */
        .fit-card-wrapper {
            perspective: 1200px;
            z-index: 1;
            position: relative;
        }

        .fit-card {
            border-radius: 1.35rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            animation: luxuryCardEntrance 0.85s cubic-bezier(0.16, 1, 0.3, 1) both;
            transform-style: preserve-3d;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
            background: #ffffff;
        }

        @keyframes luxuryCardEntrance {
            0% {
                opacity: 0;
                transform: translateY(35px) scale(0.96) rotateX(4deg);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
            }
        }

        /* 3. Error Shake Feedback */
        .login-error-shake {
            animation: errorShake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both !important;
        }

        @keyframes errorShake {
            10%, 90% { transform: translate3d(-3px, 0, 0); }
            20%, 80% { transform: translate3d(5px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-6px, 0, 0); }
            40%, 60% { transform: translate3d(6px, 0, 0); }
        }

        /* 4. Staggered Elements Fade & Slide Sequence */
        .anim-stagger {
            animation: elementReveal 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
            will-change: transform, opacity;
        }
        .anim-stagger-1 { animation-delay: 0.12s; }
        .anim-stagger-2 { animation-delay: 0.22s; }
        .anim-stagger-3 { animation-delay: 0.32s; }
        .anim-stagger-4 { animation-delay: 0.42s; }
        .anim-stagger-5 { animation-delay: 0.52s; }
        .anim-stagger-6 { animation-delay: 0.62s; }

        @keyframes elementReveal {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel & Continuous Floating Illustration */
        .fit-left-panel {
            background: linear-gradient(145deg, #f8fafc 0%, #edf4ff 100%) !important;
            border-right: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        
        .fit-img {
            animation: floatIllustration 5s ease-in-out infinite alternate;
            transition: transform 0.4s ease;
            filter: drop-shadow(0 12px 20px rgba(30, 58, 138, 0.08));
        }

        @keyframes floatIllustration {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-6px) scale(1.015); }
            100% { transform: translateY(2px) scale(0.995); }
        }

        .fit-card:hover .fit-img {
            transform: scale(1.04) translateY(-4px);
        }

        /* 5. Form Ergonomics & Micro-Interactions */
        .fit-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.2px;
            margin-bottom: 0.35rem;
            display: block;
        }
        .fit-input {
            border-radius: 0.6rem;
            border: 1px solid #cbd5e1;
            padding: 0.68rem 0.85rem;
            font-size: 0.9rem;
            color: #0f172a;
            background-color: #ffffff;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease, transform 0.2s ease;
        }
        .fit-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.16);
            background-color: #ffffff;
            outline: none;
            transform: translateY(-1px);
        }

        .fit-toggle-pwd {
            color: #94a3b8;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: color 0.15s ease, transform 0.15s ease;
        }
        .fit-toggle-pwd:hover {
            color: #2563eb;
            transform: translateY(-50%) scale(1.12) !important;
        }
        .fit-toggle-pwd:active {
            transform: translateY(-50%) scale(0.95) !important;
        }

        .fit-checkbox {
            width: 1.05rem;
            height: 1.05rem;
            border-radius: 4px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
        }
        .fit-checkbox:checked {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        .fit-checkbox:active {
            transform: scale(0.9);
        }

        .fit-link {
            color: #2563eb;
            transition: color 0.15s ease, opacity 0.15s ease;
        }
        .fit-link:hover {
            color: #1d4ed8;
            text-decoration: underline !important;
        }

        /* 6. Tactile Button & Processing Spinner */
        .fit-btn-primary {
            background-color: #1e3a8a;
            border: 1px solid #1e3a8a;
            color: #ffffff;
            border-radius: 0.6rem;
            padding: 0.72rem 1.25rem;
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(30, 58, 138, 0.22);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .fit-btn-primary::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }
        .fit-btn-primary:hover::after {
            left: 100%;
        }
        .fit-btn-primary:hover {
            background-color: #172554;
            border-color: #172554;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(30, 58, 138, 0.32);
        }
        .fit-btn-primary:active {
            transform: translateY(0) scale(0.985);
            box-shadow: 0 2px 6px rgba(30, 58, 138, 0.2);
        }

        .fit-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            vertical-align: text-bottom;
            margin-right: 0.4rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* 7. Accessibility: Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            .fit-card,
            .anim-stagger,
            .login-error-shake,
            .fit-img,
            .fit-btn-primary,
            .ambient-orb {
                animation: none !important;
                transition: opacity 0.15s ease !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="login-page m-0 p-0">

    <!-- Ambient Glowing Light Orbs in Background -->
    <div class="ambient-orb ambient-orb-1"></div>
    <div class="ambient-orb ambient-orb-2"></div>
    <div class="ambient-orb ambient-orb-3"></div>

    <div class="container px-3 fit-card-wrapper">
        <div class="fit-card bg-white mx-auto d-flex flex-column flex-lg-row overflow-hidden <?= session()->getFlashdata('error') ? 'login-error-shake' : '' ?>" id="loginCard" style="max-width: 800px; width: 100%;">
            
            <!-- Left Panel: Illustration -->
            <div class="fit-left-panel d-none d-lg-flex flex-column justify-content-center align-items-center p-3" style="width: 50%;">
                <img src="<?= base_url('assets/login_illustration_new.jpg') ?>" alt="Ilustrasi Kampus PKTJ" class="img-fluid fit-img" style="mix-blend-mode: multiply; max-height: 320px; object-fit: contain;" width="360" height="320" loading="eager">
            </div>

            <!-- Right Panel: Login Form -->
            <div class="fit-right-panel p-4 d-flex flex-column justify-content-center" style="width: 100%; flex: 1;">
                <div class="text-start mb-3 anim-stagger anim-stagger-1">
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

                    <div class="mb-2 anim-stagger anim-stagger-2">
                        <label for="username" class="form-label fit-label">Nama Pengguna</label>
                        <input type="text" name="username" id="username" class="form-control fit-input" placeholder="Masukkan username..." value="<?= esc($currentUsername) ?>" required <?= empty($currentUsername) ? 'autofocus' : '' ?>>
                    </div>

                    <div class="mb-3 anim-stagger anim-stagger-3">
                        <label for="password" class="form-label fit-label">Kata Sandi</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password" class="form-control fit-input pe-5" placeholder="Masukkan kata sandi..." required <?= !empty($currentUsername) ? 'autofocus' : '' ?>>
                            <button type="button" id="togglePassword" class="btn btn-link fit-toggle-pwd position-absolute top-50 end-0 translate-middle-y text-decoration-none me-2" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <i class="bi bi-eye-slash" style="font-size: 0.95rem;"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 anim-stagger anim-stagger-4">
                        <div class="form-check m-0 p-0 d-flex align-items-center gap-2">
                            <input class="form-check-input fit-checkbox m-0" type="checkbox" value="1" id="rememberMe" name="remember" <?= $isRemembered ? 'checked' : '' ?> style="margin-left: 0 !important;">
                            <label class="form-check-label text-muted user-select-none" for="rememberMe" style="cursor: pointer; font-size: 0.8rem; line-height: 1;">
                                Ingat sesi
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none fw-medium fit-link" onclick="forgotPassword(); return false;" style="font-size: 0.8rem;">Lupa Password?</a>
                    </div>

                    <div class="d-grid mb-2 anim-stagger anim-stagger-5">
                        <button type="submit" class="btn fit-btn-primary" id="loginBtn">
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="text-center mt-3 anim-stagger anim-stagger-6">
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
        const card = document.getElementById('loginCard');

        // 1. Interactive 3D Card Tilt on Mouse Move (Desktop Only)
        if (card && window.matchMedia('(min-width: 992px)').matches && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let isHovering = false;
            let rafId = null;
            let targetRotateX = 0;
            let targetRotateY = 0;
            let currentRotateX = 0;
            let currentRotateY = 0;

            const updateTilt = () => {
                currentRotateX += (targetRotateX - currentRotateX) * 0.12;
                currentRotateY += (targetRotateY - currentRotateY) * 0.12;
                
                if (isHovering || Math.abs(currentRotateX) > 0.05 || Math.abs(currentRotateY) > 0.05) {
                    card.style.transform = `perspective(1000px) rotateX(${currentRotateX.toFixed(2)}deg) rotateY(${currentRotateY.toFixed(2)}deg)`;
                    rafId = requestAnimationFrame(updateTilt);
                } else {
                    card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
                    rafId = null;
                }
            };

            document.addEventListener('mousemove', function (e) {
                const rect = card.getBoundingClientRect();
                const cardCenterX = rect.left + rect.width / 2;
                const cardCenterY = rect.top + rect.height / 2;

                const distanceX = e.clientX - cardCenterX;
                const distanceY = e.clientY - cardCenterY;

                // Max tilt 3.5 degrees for quiet, elegant depth
                if (Math.abs(distanceX) < 450 && Math.abs(distanceY) < 350) {
                    isHovering = true;
                    targetRotateY = (distanceX / 450) * 3.5;
                    targetRotateX = -(distanceY / 350) * 3.5;
                    if (!rafId) rafId = requestAnimationFrame(updateTilt);
                } else {
                    isHovering = false;
                    targetRotateX = 0;
                    targetRotateY = 0;
                }
            });

            document.addEventListener('mouseleave', function () {
                isHovering = false;
                targetRotateX = 0;
                targetRotateY = 0;
            });
        }

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
                        btn.innerHTML = '<span class="fit-spinner"></span> <span style="letter-spacing: 0.03em; font-weight: 600;">Memproses...</span>';
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