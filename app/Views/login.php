<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMONIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="login-page">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg login-card">
            <div class="card-body p-4 p-md-5">
                <div class="logo-container">
                    <img src="<?= base_url('assets/logo_pktj.png') ?>" alt="Logo PKTJ">
                </div>
                <h4 class="fw-bold mb-1">Selamat Datang!</h4>
                <p class="text-muted mb-1">Login ke akun SIMONIK Anda</p>
                <p class="text-muted mb-4">SISTEM INFORMASI MONITORING KINERJA</p>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">...</div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">...</div>
                <?php endif; ?>
                <form action="<?= base_url('login') ?>" method="POST" autocomplete="off" class="w-100">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group-icon">
                            <i class="bi bi-person-fill form-icon"></i>
                            <input type="text" name="username" id="username" class="form-control" value="<?= old('username') ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group-icon">
                            <i class="bi bi-lock-fill form-icon"></i>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button type="button" id="togglePassword" class="btn password-toggle-btn">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-login">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="login-footer text-center w-100">
        <p>&copy; <?= date("Y"); ?> Politeknik Keselamatan Transportasi Jalan. Hak Cipta Dilindungi.</p>
    </div>

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
    });
</script>

</body>
</html>