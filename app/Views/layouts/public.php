<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'PLPI') ?></title>
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <style>
        body{background:#f6f8fc}
        .wrap{max-width:1100px;margin:0 auto;padding:24px}
        .card{background:#fff;border:1px solid #dbe4ef;border-radius:14px;padding:18px}
        .input{width:100%;padding:9px 10px;border:1px solid #c8d6ea;border-radius:10px;background:#fff}
        .btn{display:inline-block;background:#123c6b;color:#fff;padding:8px 12px;border-radius:10px;text-decoration:none;border:0;cursor:pointer}
        .btn2{display:inline-block;background:#fff;color:#123c6b;padding:8px 12px;border-radius:10px;text-decoration:none;border:1px solid #bcd0e9;cursor:pointer}
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
