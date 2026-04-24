<!doctype html>
<html lang="id">
<head>
    <?php
        helper('app_settings');
        $publicFaviconUrl = plpi_asset_url_versioned((string) plpi_app_setting('favicon_path', ''), 'favicon.ico');
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'PLPI') ?></title>
    <link rel="icon" href="<?= esc($publicFaviconUrl) ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <style>
        body{background:#f6f8fc;font-family:"Inter",sans-serif}
        .wrap{max-width:1100px;margin:0 auto;padding:24px}
        .card{background:#fff;border:1px solid #dbe4ef;border-radius:14px;padding:18px}
        .input{width:100%;padding:9px 10px;border:1px solid #c8d6ea;border-radius:10px;background:#fff}
        .btn{display:inline-block;background:#356acc;color:#fff;padding:8px 12px;border-radius:0.375rem;text-decoration:none;border:1px solid #356acc;cursor:pointer;transition:all .18s ease}
        .btn:hover{background:#2f5fb8;border-color:#2c59ad;color:#fff;transform:translateY(-1px);box-shadow:0 6px 14px rgba(16,47,96,.16)}
        .btn2{display:inline-block;background:#6c757d;color:#fff;padding:8px 12px;border-radius:0.375rem;text-decoration:none;border:1px solid #6c757d;cursor:pointer;transition:all .18s ease}
        .btn2:hover{background:#5c636a;border-color:#565e64;color:#fff;transform:translateY(-1px);box-shadow:0 6px 14px rgba(16,47,96,.16)}
    </style>
</head>
<body>
<div class="wrap">
    <?php if (session('error')): ?><div class="card" style="border-color:#fecaca;background:#fff1f2;margin-bottom:12px"><?= esc(session('error')) ?></div><?php endif; ?>
    <?php if (session('success')): ?><div class="card" style="border-color:#bbf7d0;background:#f0fdf4;margin-bottom:12px"><?= esc(session('success')) ?></div><?php endif; ?>
    <?= $this->renderSection('content') ?>
</div>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
