<?php
helper('app_settings');
$bootstrapCssPath = FCPATH . 'assets/vendor/bootstrap/css/bootstrap.min.css';
$bootstrapIconsCssPath = FCPATH . 'assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css';

$bootstrapCssVersion = is_file($bootstrapCssPath) ? (string) filemtime($bootstrapCssPath) : '1';
$bootstrapIconsCssVersion = is_file($bootstrapIconsCssPath) ? (string) filemtime($bootstrapIconsCssPath) : '1';
$loginLogoUrl = plpi_asset_url_versioned((string) plpi_app_setting('login_logo_path', ''), 'assets/img/plpi-geo-logo.svg');
$faviconUrl = plpi_asset_url_versioned((string) plpi_app_setting('favicon_path', ''), 'favicon.ico');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc((string) ($title ?? 'Login PLPI')) ?></title>
    <link rel="icon" href="<?= esc($faviconUrl) ?>">
    <link href="<?= esc(base_url('assets/vendor/bootstrap/css/bootstrap.min.css?v=' . $bootstrapCssVersion)) ?>" rel="stylesheet">
    <link href="<?= esc(base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css?v=' . $bootstrapIconsCssVersion)) ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-primary: #2b59b5;
            --auth-primary-dark: #244c9b;
            --auth-navy: #163b73;
            --auth-bg: #edf4fb;
            --auth-border: #dfe5ef;
            --auth-text: #0f172a;
            --auth-muted: #64748b;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at 12% 8%, rgba(95, 145, 224, 0.14), transparent 36%),
                radial-gradient(circle at 88% 24%, rgba(22, 59, 115, 0.12), transparent 33%),
                #f2f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: "Inter", "Segoe UI", sans-serif;
            color: var(--auth-text);
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 14px;
        }

        .logo-wrap img {
            width: 54px;
            height: 54px;
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
            color: var(--auth-navy);
            font-weight: 800;
            display: inline-block;
            font-size: 2.2rem;
            line-height: 1;
            margin-bottom: 0;
            letter-spacing: .2px;
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
                width: 46px;
                height: 46px;
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
            <div style="text-align:center;">
                <div class="logo-wrap">
                    <img src="<?= esc($loginLogoUrl) ?>" alt="Logo PLPI">
                    <span class="auth-app-title">PLPI</span>
                </div>
            </div>
            <p class="auth-subtitle">Pusat Layanan Publikasi Ilmiah</p>

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
                    <label for="usernameField" class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        id="usernameField"
                        class="form-control"
                        placeholder="Masukkan username"
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
        </div>
    </div>
</body>
</html>
