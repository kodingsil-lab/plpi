<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'PLPI') ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('unisap_favicon.ico') ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
    <style>
        .brand-plpi-title{font-weight:800!important;letter-spacing:.2px}
        .brand-plpi-sub{font-size:12px!important;line-height:1.3}
        .plpi-page-title{font-size:30px;font-weight:700;color:#103f72}
        .plpi-kicker{font-size:13px;color:#5f758e}
    </style>
</head>
<body class="myletters-page">
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand-wrap">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-building"></i></div>
            <div class="brand-text">
                <h5 class="brand-plpi-title">PLPI</h5>
                <p class="brand-plpi-sub">PUSAT LAYANAN PUBLIKASI ILMIAH</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li><a href="<?= site_url('dashboard') ?>" class="sidebar-link <?= url_is('dashboard') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-grid-1x2-fill"></i></span><span class="sidebar-link-text">Dashboard</span></a></li>
            <li><a href="<?= site_url('admin/loa-requests') ?>" class="sidebar-link <?= url_is('admin/loa-requests*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-send-fill"></i></span><span class="sidebar-link-text">Permohonan LoA</span></a></li>
            <li><a href="<?= site_url('admin/loa-letters') ?>" class="sidebar-link <?= url_is('admin/loa-letters*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-folder2-open"></i></span><span class="sidebar-link-text">LoA Terbit</span></a></li>
            <li class="sidebar-section-label">Layanan</li>
            <li><a href="<?= site_url('admin/journals') ?>" class="sidebar-link <?= url_is('admin/journals*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-journal-text"></i></span><span class="sidebar-link-text">Data Jurnal</span></a></li>
            <li><a href="<?= site_url('admin/publishers') ?>" class="sidebar-link <?= url_is('admin/publishers*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-building-gear"></i></span><span class="sidebar-link-text">Publisher</span></a></li>
            <li><a href="<?= site_url('admin/notifikasi') ?>" class="sidebar-link <?= url_is('admin/notifikasi*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-bell-fill"></i></span><span class="sidebar-link-text">Notifikasi</span></a></li>
            <li><a href="<?= site_url('admin/users') ?>" class="sidebar-link <?= url_is('admin/users*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-people-fill"></i></span><span class="sidebar-link-text">Pengguna</span></a></li>
            <li>
                <form method="post" action="<?= site_url('logout') ?>" class="m-0">
                    <button type="submit" class="sidebar-link sidebar-link-logout w-100 border-0 bg-transparent text-start">
                        <span class="sidebar-icon-wrap"><i class="bi bi-box-arrow-right"></i></span>
                        <span class="sidebar-link-text">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<div class="main-content">
    <header class="app-topbar">
        <div class="topbar-left">
            <button type="button" class="sidebar-toggle-btn" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <span class="topbar-accent-badge" aria-hidden="true"></span>
            <div class="topbar-appmeta">
                <div class="topbar-appname">PLPI</div>
                <div class="topbar-appsub">LoA, Invoice, dan Layanan Jurnal</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user-btn">
                <span class="topbar-user-meta">
                    <span class="topbar-user-name"><?= esc((string) session('name') ?: 'Super Admin') ?></span>
                    <span class="topbar-user-role"><?= esc((string) session('role') ?: 'superadmin') ?></span>
                </span>
            </div>
        </div>
    </header>

    <div class="container-fluid px-3 px-lg-4 py-3 py-lg-4">
        <div class="mb-3">
            <div class="plpi-page-title"><?= esc($title ?? 'PLPI') ?></div>
            <?php if (! empty($subtitle)): ?><div class="plpi-kicker"><?= esc($subtitle) ?></div><?php endif; ?>
        </div>

        <?php if (session('error')): ?><div class="alert alert-danger"><?= esc(session('error')) ?></div><?php endif; ?>
        <?php if (session('success')): ?><div class="alert alert-success"><?= esc(session('success')) ?></div><?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <footer class="app-footer">
        <div class="footer-left">PLPI &copy; <?= date('Y') ?> - Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal</div>
        <div class="footer-right">Developed By <span class="footer-dev-link">KSJ</span> <span class="footer-heart">?</span></div>
    </footer>
</div>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const appSidebar = document.getElementById('appSidebar');
    if (sidebarToggle && appSidebar) {
        sidebarToggle.addEventListener('click', function () { appSidebar.classList.toggle('collapsed'); });
    }
</script>
</body>
</html>
