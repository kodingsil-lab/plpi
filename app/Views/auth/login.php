<?php
$bootstrapCssPath = FCPATH . 'assets/vendor/bootstrap/css/bootstrap.min.css';
$bootstrapIconsCssPath = FCPATH . 'assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css';
$logoPath = FCPATH . 'assets/img/logo-unisap.png';
$faviconPath = FCPATH . 'favicon.ico';

$bootstrapCssVersion = is_file($bootstrapCssPath) ? (string) filemtime($bootstrapCssPath) : '1';
$bootstrapIconsCssVersion = is_file($bootstrapIconsCssPath) ? (string) filemtime($bootstrapIconsCssPath) : '1';
$logoVersion = is_file($logoPath) ? (string) filemtime($logoPath) : '1';
$faviconVersion = is_file($faviconPath) ? (string) filemtime($faviconPath) : '1';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc((string) ($title ?? 'Login PLPI')) ?></title>
    <link rel="icon" type="image/x-icon" href="<?= esc(base_url('favicon.ico?v=' . $faviconVersion)) ?>">
    <link href="<?= esc(base_url('assets/vendor/bootstrap/css/bootstrap.min.css?v=' . $bootstrapCssVersion)) ?>" rel="stylesheet">
    <link href="<?= esc(base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css?v=' . $bootstrapIconsCssVersion)) ?>" rel="stylesheet">
    <style>
        :root {
            --auth-primary: #2b59b5;
            --auth-primary-dark: #244c9b;
            --auth-bg: #edf4fb;
            --auth-border: #dfe5ef;
            --auth-text: #0f172a;
            --auth-muted: #64748b;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at 8% 12%, rgba(43, 89, 181, 0.18), transparent 34%),
                radial-gradient(circle at 90% 82%, rgba(59, 130, 246, 0.14), transparent 32%),
                radial-gradient(circle at 84% 18%, rgba(14, 165, 233, 0.12), transparent 24%),
                linear-gradient(135deg, #ecf2fb 0%, #e8eef8 48%, #f4f8ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: "Inter", "Segoe UI", sans-serif;
            color: var(--auth-text);
            position: relative;
            overflow: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            width: 460px;
            height: 460px;
            top: -220px;
            left: -150px;
            background: radial-gradient(circle, rgba(43, 89, 181, 0.24), rgba(43, 89, 181, 0));
        }

        body::after {
            width: 500px;
            height: 500px;
            right: -220px;
            bottom: -210px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0));
        }

        .auth-card {
            width: min(100%, 560px);
            border: 1px solid var(--auth-border);
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(19, 46, 92, 0.05);
            position: relative;
            z-index: 1;
        }

        .auth-card .card-body {
            padding: 42px 34px 30px;
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 14px;
        }

        .logo-wrap img {
            width: 88px;
            height: 88px;
            object-fit: contain;
        }

        .auth-subtitle {
            text-align: center;
            color: var(--auth-muted);
            font-size: 1.03rem;
            margin-bottom: 24px;
            line-height: 1.45;
        }

        .auth-app-title {
            color: var(--auth-primary);
            font-weight: 600;
            display: block;
            font-size: 2rem;
            line-height: 1.1;
            margin-bottom: 10px;
        }

        .form-label {
            color: var(--auth-text);
            font-weight: 400;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-control {
            border-radius: 8px;
            border-color: #d6dee8;
            padding: 12px 14px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 0.2rem rgba(43, 89, 181, 0.15);
        }

        .btn-signin {
            background: var(--auth-primary);
            border: 1px solid var(--auth-primary);
            color: #fff;
            border-radius: 8px;
            padding-top: 11px;
            padding-bottom: 11px;
            font-weight: 500;
        }

        .btn-signin:hover {
            background: var(--auth-primary-dark);
            border-color: var(--auth-primary-dark);
            color: #fff;
        }

        .auth-note {
            margin-top: 14px;
            text-align: center;
            color: var(--auth-muted);
            font-size: 0.86rem;
            line-height: 1.35;
        }

        @media (max-width: 575.98px) {
            body {
                padding: 12px;
                align-items: flex-start;
                overflow-y: auto;
            }

            .auth-card {
                border-radius: 18px;
                width: min(100%, 420px);
                margin: 8px auto;
            }

            .auth-card .card-body {
                padding: 28px 20px 22px;
            }

            .logo-wrap img {
                width: 68px;
                height: 68px;
            }

            .auth-subtitle {
                font-size: 0.88rem;
                margin-bottom: 18px;
            }

            .auth-app-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card card">
        <div class="card-body">
            <div class="logo-wrap">
                <img src="<?= esc(base_url('assets/img/logo-unisap.png?v=' . $logoVersion)) ?>" alt="Logo UNISAP">
            </div>
            <p class="auth-subtitle">
                <span class="auth-app-title">PLPI</span>
                Pusat Layanan Publikasi Ilmiah<br>
                Sistem Informasi LoA, Invoice, dan Layanan Jurnal
            </p>

            <?php if (session('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc((string) session('error')) ?>
                </div>
            <?php endif; ?>
            <?php if (session('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= esc((string) session('success')) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('login') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="usernameField" class="form-label">Username / Email</label>
                    <input
                        type="text"
                        name="username"
                        id="usernameField"
                        class="form-control"
                        placeholder="Masukkan username atau email"
                        value="<?= esc((string) old('username')) ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-4">
                    <label for="passwordField" class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="passwordField"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-signin w-100">Masuk</button>
            </form>

            <div class="auth-note">
                Gunakan akun admin yang sudah terdaftar untuk mengakses dashboard PLPI.
            </div>
        </div>
    </div>
</body>
</html>
