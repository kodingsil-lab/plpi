<!doctype html>
<html lang="id">
<head>
    <?php
        helper('app_settings');
        $adminFaviconUrl = plpi_asset_url_versioned((string) plpi_app_setting('favicon_path', ''), 'unisap_favicon.ico');
        $adminHeaderLogoUrl = plpi_asset_url_versioned((string) plpi_app_setting('header_logo_path', ''), 'assets/img/plpi-geo-logo-white.svg');
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'PLPI') ?></title>
    <link rel="icon" href="<?= esc($adminFaviconUrl) ?>">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
    <style>
        .brand-plpi-title{font-weight:800!important;letter-spacing:.2px}
        .brand-plpi-sub{font-size:12px!important;line-height:1.3}
        .plpi-page-title{font-size:30px;font-weight:700;color:#103f72}
        .plpi-kicker{font-size:13px;color:#5f758e}

        /* Final global button override (loaded last) to avoid legacy CSS collisions */
        .main-content .btn{
            border-radius:.375rem!important;
            font-weight:700!important;
            transition: all .18s ease!important;
        }
        .main-content .btn:hover{
            transform: translateY(-1px);
            filter: brightness(1.03);
            box-shadow: 0 6px 14px rgba(16, 47, 96, 0.16)!important;
        }
        .main-content .btn:active{
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(16, 47, 96, 0.14)!important;
        }
        .main-content .btn:focus-visible{
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(53, 106, 204, .22)!important;
        }
        .main-content .btn-primary,
        .main-content .btn-primary-main{
            color:#fff!important;
            background:#356acc!important;
            border-color:#356acc!important;
        }
        .main-content .btn-primary:hover,
        .main-content .btn-primary-main:hover{
            color:#fff!important;
            background:#2f5fb8!important;
            border-color:#2c59ad!important;
        }
        .main-content .btn-secondary{
            color:#fff!important;
            background:#6c757d!important;
            border-color:#6c757d!important;
        }
        .main-content .btn-danger{
            color:#fff!important;
            background:#dc3545!important;
            border-color:#dc3545!important;
        }
        .main-content .btn-light,
        .main-content .btn-light-soft{
            color:#212529!important;
            background:#f8f9fa!important;
            border-color:#f8f9fa!important;
        }
    </style>
</head>
<body class="myletters-page">
<?php
    $isSettingsActive = url_is('admin/journals*')
        || url_is('admin/publishers*')
        || url_is('admin/notifikasi*')
        || url_is('admin/users*')
        || url_is('superadmin/settings/application*');
    $sessionName = trim((string) session('name'));
    $topbarName = $sessionName !== '' ? $sessionName : 'Super Admin';
    $topbarRole = trim((string) session('role')) ?: 'superadmin';
    $nameParts = preg_split('/\s+/', $topbarName) ?: [];
    $initials = '';
    foreach ($nameParts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($initials) >= 2) {
            break;
        }
    }
    if ($initials === '') {
        $initials = 'SA';
    }
    $profileUrl = session('user_id') ? site_url('admin/users/' . (int) session('user_id') . '/edit') : site_url('admin/users');
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand-wrap">
        <a href="<?= site_url('/') ?>" class="sidebar-brand text-decoration-none">
            <div class="brand-icon">
                <img src="<?= esc($adminHeaderLogoUrl) ?>" alt="Logo PLPI" class="brand-logo-white">
            </div>
            <div class="brand-text">
                <h5 class="brand-plpi-title">PLPI</h5>
                <p class="brand-plpi-sub">PUSAT LAYANAN PUBLIKASI ILMIAH</p>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li><a href="<?= site_url('dashboard') ?>" class="sidebar-link <?= url_is('dashboard') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-grid-1x2-fill"></i></span><span class="sidebar-link-text">Dashboard</span></a></li>
            <li><a href="<?= site_url('admin/loa-requests') ?>" class="sidebar-link <?= url_is('admin/loa-requests*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-send-fill"></i></span><span class="sidebar-link-text">Permohonan LoA</span></a></li>
            <li><a href="<?= site_url('admin/loa-letters') ?>" class="sidebar-link <?= url_is('admin/loa-letters*') ? 'active' : '' ?>"><span class="sidebar-icon-wrap"><i class="bi bi-folder2-open"></i></span><span class="sidebar-link-text">LoA Terbit</span></a></li>
            <li class="sidebar-section-label">Layanan</li>
            <li class="has-submenu">
                <button
                    type="button"
                    class="submenu-toggle <?= $isSettingsActive ? 'active' : '' ?>"
                    aria-controls="settingsSubmenu"
                    aria-expanded="<?= $isSettingsActive ? 'true' : 'false' ?>"
                >
                    <span class="link-left">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan</span>
                    </span>
                    <i class="bi bi-chevron-down submenu-arrow"></i>
                </button>
                <ul id="settingsSubmenu" class="sidebar-submenu <?= $isSettingsActive ? 'show' : '' ?>">
                    <li><a href="<?= site_url('superadmin/settings/application') ?>" class="sidebar-sublink <?= url_is('superadmin/settings/application*') ? 'active' : '' ?>"><i class="bi bi-sliders"></i><span>Aplikasi</span></a></li>
                    <li><a href="<?= site_url('admin/journals') ?>" class="sidebar-sublink <?= url_is('admin/journals*') ? 'active' : '' ?>"><i class="bi bi-journal-text"></i><span>Data Jurnal</span></a></li>
                    <li><a href="<?= site_url('admin/publishers') ?>" class="sidebar-sublink <?= url_is('admin/publishers*') ? 'active' : '' ?>"><i class="bi bi-building-gear"></i><span>Publisher</span></a></li>
                    <li><a href="<?= site_url('admin/notifikasi') ?>" class="sidebar-sublink <?= url_is('admin/notifikasi*') ? 'active' : '' ?>"><i class="bi bi-bell-fill"></i><span>Notifikasi</span></a></li>
                    <li><a href="<?= site_url('admin/users') ?>" class="sidebar-sublink <?= url_is('admin/users*') ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Pengguna</span></a></li>
                </ul>
            </li>
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
                <div class="topbar-appsub">Pusat Layanan Publikasi Ilmiah</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user dropdown">
                <button type="button" class="topbar-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="topbar-avatar-shell">
                        <span class="topbar-avatar-img topbar-avatar-generated"><?= esc($initials) ?></span>
                    </span>
                    <span class="topbar-user-meta">
                        <span class="topbar-user-name"><?= esc($topbarName) ?></span>
                        <span class="topbar-user-role"><?= esc($topbarRole) ?></span>
                    </span>
                    <span class="topbar-user-caret"><i class="bi bi-chevron-down"></i></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end topbar-dropdown topbar-account-menu">
                    <div class="topbar-account-summary">
                        <div class="topbar-account-summary-name"><?= esc($topbarName) ?></div>
                        <div class="topbar-account-summary-role"><?= esc($topbarRole) ?></div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="<?= $profileUrl ?>" class="dropdown-item topbar-account-item">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="post" action="<?= site_url('logout') ?>" class="m-0">
                        <button type="submit" class="dropdown-item topbar-account-item topbar-account-item-logout w-100 border-0 bg-transparent text-start">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
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
        <div class="footer-left">PLPI &copy; <?= date('Y') ?> - Pusat Layanan Publikasi Ilmiah</div>
        <div class="footer-right">Developed By <span class="footer-dev-link">KSJ</span> <span class="footer-heart">?</span></div>
    </footer>
</div>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const appSidebar = document.getElementById('appSidebar');
    if (sidebarToggle && appSidebar) {
        sidebarToggle.addEventListener('click', function () { appSidebar.classList.toggle('collapsed'); });
    }

    if (window.bootstrap && window.bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new window.bootstrap.Tooltip(el);
        });
    }

    document.querySelectorAll('.myletters-table-card .myletters-table-wrap table').forEach(function (tableEl) {
        const headerCells = Array.from(tableEl.querySelectorAll('thead tr:first-child th'));
        if (!headerCells.length) {
            return;
        }

        const headerLabels = headerCells.map(function (th) {
            return (th.textContent || '').trim();
        });

        tableEl.querySelectorAll('tbody tr').forEach(function (row) {
            const cells = Array.from(row.children).filter(function (cell) {
                return cell.tagName === 'TD';
            });

            cells.forEach(function (cell, idx) {
                if (cell.hasAttribute('data-label')) {
                    return;
                }
                if (cell.colSpan && cell.colSpan > 1) {
                    cell.setAttribute('data-label', '');
                    return;
                }
                cell.setAttribute('data-label', headerLabels[idx] || '');
            });
        });
    });

    const badgeIconMap = [
        { cls: 'myletters-status-waiting', icon: 'heroicons-outline:clock' },
        { cls: 'myletters-status-approved', icon: 'heroicons-outline:check-badge' },
        { cls: 'myletters-status-issued', icon: 'heroicons-outline:check-circle' },
        { cls: 'myletters-status-revision', icon: 'heroicons-outline:exclamation-triangle' },
        { cls: 'myletters-status-ready', icon: 'heroicons-outline:sparkles' },
    ];

    document.querySelectorAll('.status-pill').forEach(function (pill) {
        if (pill.querySelector('.status-pill-icon')) {
            return;
        }

        const match = badgeIconMap.find(function (item) {
            return pill.classList.contains(item.cls);
        });

        if (!match) {
            return;
        }

        const iconEl = document.createElement('iconify-icon');
        iconEl.setAttribute('icon', match.icon);
        iconEl.setAttribute('aria-hidden', 'true');
        iconEl.className = 'status-pill-icon';
        pill.prepend(iconEl);
    });

    document.querySelectorAll('.bulk-delete-form').forEach(function (form) {
        const targetSelector = form.getAttribute('data-bulk-target') || '';
        const scope = targetSelector ? document.querySelector(targetSelector) : null;
        if (!scope) {
            return;
        }

        const selectAll = scope.querySelector('.bulk-select-all');
        const rowCheckboxes = Array.from(scope.querySelectorAll('.bulk-row-check'));
        const enabledCheckboxes = rowCheckboxes.filter(function (checkbox) {
            return !checkbox.disabled;
        });
        const hiddenContainer = form.querySelector('.bulk-hidden-inputs');
        const submitBtn = form.querySelector('.bulk-delete-trigger');
        const countLabel = form.querySelector('.bulk-selection-count');

        const syncState = function () {
            const checked = rowCheckboxes.filter(function (checkbox) {
                return !checkbox.disabled && checkbox.checked;
            });

            if (selectAll) {
                selectAll.checked = enabledCheckboxes.length > 0 && checked.length === enabledCheckboxes.length;
                selectAll.indeterminate = checked.length > 0 && checked.length < enabledCheckboxes.length;
            }

            if (submitBtn) {
                submitBtn.disabled = checked.length === 0;
            }

            if (countLabel) {
                countLabel.textContent = checked.length > 0
                    ? checked.length + ' dipilih'
                    : 'Belum ada yang dipilih';
            }
        };

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                enabledCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
                syncState();
            });
        }

        rowCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', syncState);
        });

        form.addEventListener('submit', function (event) {
            const checked = rowCheckboxes.filter(function (checkbox) {
                return !checkbox.disabled && checkbox.checked;
            });

            if (checked.length === 0) {
                event.preventDefault();
                return;
            }

            const message = form.getAttribute('data-confirm') || 'Hapus data terpilih?';
            if (!window.confirm(message)) {
                event.preventDefault();
                return;
            }

            if (hiddenContainer) {
                hiddenContainer.innerHTML = '';
                checked.forEach(function (checkbox) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = checkbox.value;
                    hiddenContainer.appendChild(input);
                });
            }
        });

        syncState();
    });
</script>
</body>
</html>
