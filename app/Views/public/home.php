<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<?php
    helper('app_settings');
    $latestRequests = is_array($latestRequests ?? null) ? $latestRequests : [];
    $journalProfiles = is_array($journalProfiles ?? null) ? $journalProfiles : [];
    $requestStats = is_array($requestStats ?? null) ? $requestStats : ['total' => 0, 'pending' => 0, 'letters' => 0];
    $publicLogoUrl = plpi_asset_url_versioned((string) plpi_app_setting('public_logo_path', ''), 'assets/img/plpi-geo-logo.svg');
    $adminNavUrl = (string) ($adminNavUrl ?? site_url('login'));
    $adminNavLabel = (string) ($adminNavLabel ?? 'Login Admin');
    $adminNavIcon = (string) ($adminNavIcon ?? 'bi-box-arrow-in-right');

    $mapStatus = static function (string $statusRaw): array {
        $status = strtolower(trim($statusRaw));
        if ($status === 'approved') {
            return ['label' => 'Disetujui', 'class' => 'approved'];
        }
        if (in_array($status, ['rejected', 'revision'], true)) {
            return ['label' => 'Ditolak', 'class' => 'rejected'];
        }
        return ['label' => 'Diproses', 'class' => 'processing'];
    };
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        --plpi-navy: #163b73;
        --plpi-navy-soft: #2b59b5;
        --plpi-text: #1f2a3a;
        --plpi-muted: #607089;
        --plpi-line: #dde6f1;
        --plpi-bg-soft: #f6f9fc;
    }

    body {
        background:
            radial-gradient(circle at 12% 8%, rgba(95, 145, 224, 0.14), transparent 36%),
            radial-gradient(circle at 88% 24%, rgba(22, 59, 115, 0.12), transparent 33%),
            #f2f6fb;
        color: var(--plpi-text);
        font-family: "Inter", sans-serif;
    }

    .wrap {
        max-width: 1180px;
        padding: 22px 20px 30px;
    }

    .plpi-shell {
        background: #fff;
        border: 1px solid #d9e4f2;
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(18, 41, 77, 0.08);
        padding: 0 16px 18px;
    }

    .plpi-header {
        position: sticky;
        top: 0;
        z-index: 30;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(6px);
        border-bottom: 1px solid #e6edf7;
        border-top-left-radius: 18px;
        border-top-right-radius: 18px;
    }

    .plpi-nav {
        min-height: 74px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .plpi-brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: var(--plpi-navy);
        text-decoration: none;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .plpi-brand img {
        width: 48px;
        height: 48px;
        display: block;
        filter: drop-shadow(0 6px 12px rgba(43, 89, 181, 0.18));
    }

    .plpi-brand-text {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.1;
    }

    .plpi-brand-title {
        font-size: 1.56rem;
        font-weight: 800;
        letter-spacing: .35px;
        color: var(--plpi-navy);
    }

    .plpi-brand-subtitle {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .22px;
        color: #6b7f9c;
        margin-top: 2px;
    }

    .plpi-menu {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .plpi-menu-toggle {
        display: none;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #cbd9ee;
        border-radius: 10px;
        background: #f8fbff;
        color: var(--plpi-navy);
        font-size: 1.18rem;
        line-height: 1;
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, color .2s ease;
    }

    .plpi-menu-toggle:hover,
    .plpi-menu-toggle:focus-visible {
        border-color: var(--plpi-navy-soft);
        background: #eef4ff;
        color: var(--plpi-navy-soft);
        outline: none;
    }

    .plpi-menu a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #4a5d7a;
        font-size: .94rem;
        font-weight: 700;
        padding: 8px 3px;
        position: relative;
        transition: color .2s ease;
    }

    .plpi-menu a::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 2px;
        width: 100%;
        height: 2px;
        border-radius: 2px;
        background: var(--plpi-navy-soft);
        transform: scaleX(0);
        transform-origin: center;
        transition: transform .2s ease;
    }

    .plpi-menu a:hover,
    .plpi-menu a.active {
        color: var(--plpi-navy-soft);
    }

    .plpi-menu a:hover::after,
    .plpi-menu a.active::after {
        transform: scaleX(1);
    }

    .plpi-menu a i {
        font-size: .95rem;
        line-height: 1;
    }

    .plpi-menu a.plpi-menu-cta {
        border: 1px solid var(--plpi-navy-soft);
        background: var(--plpi-navy-soft);
        color: #fff;
        border-radius: 10px;
        padding: 8px 14px;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .plpi-menu a.plpi-menu-cta::after {
        display: none;
    }

    .plpi-menu a.plpi-menu-cta:hover,
    .plpi-menu a.plpi-menu-cta:focus-visible {
        background: var(--plpi-navy);
        border-color: var(--plpi-navy);
        color: #fff;
        box-shadow: 0 8px 18px rgba(43, 89, 181, .28);
        transform: translateY(-1px);
    }

    .plpi-hero {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        align-items: center;
        padding: 26px 0 20px;
    }

    .plpi-hero-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .plpi-hero-image-wrap {
        margin: 8px 0 10px;
        display: flex;
        justify-content: center;
    }

    .plpi-hero-image {
        width: min(100%, 300px);
        height: auto;
        display: block;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        background: transparent;
        filter: drop-shadow(0 12px 22px rgba(33, 74, 150, 0.17));
    }

    .plpi-hero h1 {
        margin: 0 0 12px;
        color: var(--plpi-navy);
        font-size: clamp(1.2rem, 1.9vw, 1.65rem);
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: .2px;
    }

    .plpi-hero p {
        margin: 0 0 18px;
        color: var(--plpi-muted);
        line-height: 1.75;
        max-width: 560px;
    }

    .plpi-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .plpi-btn-main,
    .plpi-btn-soft {
        text-decoration: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: .92rem;
        font-weight: 700;
        transition: .2s ease;
    }

    .plpi-btn-main {
        border: 1px solid #2a57b0;
        background: linear-gradient(135deg, #2b59b5 0%, #3768c9 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(43, 89, 181, 0.22);
    }

    .plpi-btn-main:hover {
        border-color: var(--plpi-navy);
        background: var(--plpi-navy);
        color: #fff;
    }

    .plpi-btn-soft {
        border: 1px solid #c8d7ee;
        background: #f8fbff;
        color: var(--plpi-navy-soft);
    }

    .plpi-btn-soft:hover {
        border-color: var(--plpi-navy-soft);
        color: var(--plpi-navy);
    }

    .plpi-mockup {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border: 1px solid #d3e1f2;
        border-radius: 16px;
        box-shadow: 0 18px 36px rgba(20, 45, 84, 0.10);
        padding: 14px;
    }

    .plpi-mockup-top {
        display: flex;
        gap: 6px;
        margin-bottom: 10px;
    }

    .plpi-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #c8d6eb;
    }

    .plpi-dot:nth-child(1) {
        background: #a9c2e8;
    }

    .plpi-dot:nth-child(2) {
        background: #8db0e3;
    }

    .plpi-dot:nth-child(3) {
        background: #6f99d8;
    }

    .plpi-mini-cards {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 10px;
    }

    .plpi-mini-card {
        border: 1px solid var(--plpi-line);
        background: var(--plpi-bg-soft);
        border-radius: 10px;
        padding: 8px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .plpi-mini-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px rgba(18, 41, 77, 0.10);
    }

    .plpi-mini-card strong {
        display: block;
        color: var(--plpi-navy);
        font-size: .9rem;
        line-height: 1.2;
    }

    .plpi-mini-card span {
        color: #6f819d;
        font-size: .76rem;
    }

    .plpi-chart {
        border: 1px solid var(--plpi-line);
        border-radius: 12px;
        padding: 10px;
        background: #fff;
    }

    .plpi-chart svg {
        width: 100%;
        height: 86px;
        display: block;
    }

    .plpi-mockup-list {
        margin-top: 8px;
        border: 1px solid var(--plpi-line);
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }

    .plpi-mockup-row {
        display: grid;
        grid-template-columns: 112px minmax(0, 1fr) 94px;
        gap: 10px;
        align-items: center;
        padding: 8px 10px;
        font-size: .78rem;
        color: #566a8b;
        background: #fff;
    }

    .plpi-mockup-row + .plpi-mockup-row {
        border-top: 1px solid #edf2f8;
    }

    .plpi-mockup-row .code {
        color: #2c446c;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plpi-mockup-row .title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
        min-width: 0;
    }

    .plpi-mockup-row .st {
        font-weight: 700;
        font-size: .7rem;
        border-radius: 999px;
        padding: 2px 7px;
        justify-self: end;
        white-space: nowrap;
    }

    .plpi-mockup-row .st.ok {
        color: #176548;
        background: #e8f8ef;
    }

    .plpi-mockup-row .st.wait {
        color: #1f5a9d;
        background: #eaf2ff;
    }

    .plpi-section {
        margin-top: 32px;
    }

    .plpi-section-title {
        margin: 0 0 12px;
        color: var(--plpi-navy);
        font-size: 1.22rem;
        font-weight: 800;
        text-align: center;
        position: relative;
        padding-bottom: 12px;
    }

    .plpi-section-title::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        width: 76px;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg, #2b59b5 0%, #6f93dd 100%);
    }

    .plpi-section-subtitle {
        margin: -4px auto 16px;
        color: #60779a;
        font-size: .93rem;
        line-height: 1.6;
        text-align: center;
        max-width: 760px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plpi-feature-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        max-width: 980px;
        margin: 0 auto;
    }

    .plpi-feature-card {
        background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        border: 1px solid #d7e2f0;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(18, 41, 77, 0.06);
        padding: 16px 15px 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        position: relative;
        overflow: hidden;
    }

    .plpi-feature-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #2b59b5 0%, #5c84d9 100%);
    }

    .plpi-feature-card::after {
        content: "";
        position: absolute;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        right: -38px;
        top: -38px;
        background: rgba(43, 89, 181, 0.07);
        pointer-events: none;
    }

    .plpi-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 30px rgba(18, 41, 77, 0.12);
        border-color: #b8cae4;
    }

    .plpi-feature-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 2px 0 8px;
        margin: 0 0 6px;
        background: transparent;
        border-bottom: 0;
        position: relative;
        z-index: 1;
    }

    .plpi-feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(145deg, #eaf1ff 0%, #dbe7ff 100%);
        border: 1px solid #c7d8f3;
        color: #214a96;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        margin-bottom: 0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        flex-shrink: 0;
    }

    .plpi-feature-card h3 {
        margin: 0;
        font-size: 1.03rem;
        color: #173b74;
        font-weight: 800;
        letter-spacing: .1px;
    }

    .plpi-feature-card p {
        margin: 0;
        color: #5a6f8f;
        font-size: .95rem;
        line-height: 1.55;
    }

    .plpi-feature-body {
        display: flex;
        flex-direction: column;
        height: 100%;
        gap: 10px;
        position: relative;
        z-index: 1;
    }

    .plpi-feature-body p {
        min-height: 64px;
    }

    .plpi-card-action {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px dashed #d8e4f3;
    }

    .plpi-card-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid #c6d8f0;
        background: #ffffff;
        color: #214a96;
        text-decoration: none;
        font-size: .82rem;
        font-weight: 700;
        line-height: 1;
        padding: 8px 12px;
        border-radius: 10px;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease, color .2s ease;
        align-self: flex-start;
    }

    .plpi-card-link:hover {
        color: #fff;
        background: var(--plpi-navy-soft);
        border-color: var(--plpi-navy);
        box-shadow: 0 8px 18px rgba(43, 89, 181, .24);
        transform: translateY(-1px);
    }

    .plpi-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .plpi-journal-subtitle {
        margin: -4px auto 16px;
        color: #60779a;
        font-size: .93rem;
        line-height: 1.6;
        text-align: center;
        max-width: 760px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .plpi-journal-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .plpi-journal-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border: 1px solid #d5e1ef;
        border-radius: 16px;
        box-shadow: 0 8px 18px rgba(18, 41, 77, 0.06);
        padding: 10px 10px 11px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        position: relative;
        overflow: hidden;
    }

    .plpi-journal-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #2b59b5 0%, #79a0e6 100%);
    }

    .plpi-journal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px rgba(18, 41, 77, 0.10);
        border-color: #bfd2eb;
    }

    .plpi-journal-cover {
        border: 1px solid #d6e0ee;
        border-radius: 12px;
        min-height: 192px;
        padding: 10px 10px 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #edf3fb 0%, #ffffff 62%);
        position: relative;
        overflow: hidden;
    }

    .plpi-journal-logo {
        position: relative;
        z-index: 1;
        width: auto;
        height: auto;
        max-width: 84%;
        max-height: 156px;
        object-fit: contain;
        filter: drop-shadow(0 9px 14px rgba(31, 49, 80, 0.19));
    }

    .plpi-journal-cover::before {
        content: "";
        position: absolute;
        width: 110px;
        height: 110px;
        border-radius: 18px;
        right: -18px;
        top: -18px;
        background: rgba(43, 89, 181, 0.12);
        transform: rotate(18deg);
    }

    .plpi-journal-cover::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        left: -25px;
        bottom: -28px;
        background: rgba(22, 59, 115, 0.10);
    }

    .plpi-journal-badge {
        position: absolute;
        left: 9px;
        bottom: 9px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        background: #ffffff;
        border: 1px solid #cedbee;
        color: var(--plpi-navy);
        font-size: .74rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(18, 41, 77, 0.10);
    }

    @media (max-width: 767.98px) {
        .plpi-journal-cover {
            min-height: 220px;
        }

        .plpi-journal-logo {
            max-height: 178px;
        }
    }

    .plpi-journal-cover.siber .plpi-journal-badge {
        color: #1f5a9d;
    }

    .plpi-journal-cover.edukasi .plpi-journal-badge {
        color: #176548;
    }

    .plpi-journal-cover.leksikon .plpi-journal-badge {
        color: #9f4b11;
    }

    .plpi-journal-cover.abdi .plpi-journal-badge {
        color: #7a3d99;
    }

    .plpi-journal-title {
        margin: 0;
        color: #173b74;
        font-size: .93rem;
        font-weight: 800;
        line-height: 1.38;
    }

    .plpi-journal-card-link {
        position: absolute;
        right: 10px;
        bottom: 10px;
        width: 22px;
        height: 22px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #4f6f9a;
        text-decoration: none;
        transition: color .2s ease, transform .2s ease, background .2s ease;
    }

    .plpi-journal-card-link:hover {
        color: #1e4f9b;
        background: #eaf2ff;
        transform: translateY(-1px);
    }

    .plpi-journal-card-link.is-disabled {
        opacity: .45;
        pointer-events: none;
    }

    .plpi-journal-country {
        margin: -2px 0 0;
        color: #6f819d;
        font-size: .8rem;
    }

    .plpi-journal-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .plpi-journal-pill {
        border-radius: 999px;
        border: 1px solid #d3dfef;
        background: #f4f8fd;
        color: #2a4774;
        padding: 3px 10px;
        font-size: .73rem;
        font-weight: 700;
    }

    .plpi-journal-publisher {
        margin: 0;
        color: #657a99;
        font-size: .79rem;
        line-height: 1.42;
        padding-top: 7px;
        border-top: 1px dashed #d8e4f4;
    }

    .plpi-stat-card {
        background: #fff;
        border: 1px solid var(--plpi-line);
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 8px 20px rgba(18, 41, 77, 0.05);
    }

    .plpi-stat-card span {
        color: #6f819d;
        font-size: .82rem;
        font-weight: 600;
    }

    .plpi-stat-card strong {
        display: block;
        margin-top: 5px;
        color: var(--plpi-navy);
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .plpi-table-card {
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        border: 1px solid #d3e0f0;
        border-radius: 16px;
        box-shadow: 0 12px 26px rgba(18, 41, 77, 0.07);
        overflow: hidden;
        position: relative;
    }

    .plpi-table-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #2b59b5 0%, #6f93dd 100%);
    }

    .plpi-table {
        margin: 0;
    }

    .plpi-table thead th {
        background: #eef4fc;
        color: #365884;
        font-size: .79rem;
        font-weight: 800;
        border-bottom: 1px solid #cfddf0;
        text-transform: uppercase;
        letter-spacing: .35px;
        padding: 12px 10px;
    }

    .plpi-table tbody td {
        font-size: .92rem;
        color: #2b4164;
        border-color: #e5edf7;
        vertical-align: middle;
        padding: 11px 10px;
    }

    .plpi-table tbody tr:nth-child(even) td {
        background: #fbfdff;
    }

    .plpi-table tbody tr:hover td {
        background: #f2f7ff;
    }

    .badge-soft {
        border-radius: 999px;
        font-size: .76rem;
        padding: 5px 10px;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-soft.processing {
        color: #1f5a9d;
        background: #eaf2ff;
        border-color: #c8ddff;
    }

    .badge-soft.approved {
        color: #176548;
        background: #e8f8ef;
        border-color: #bfe8d0;
    }

    .badge-soft.rejected {
        color: #9f2d3a;
        background: #fdeff1;
        border-color: #f5c8d0;
    }

    .plpi-footer {
        margin-top: 28px;
        border-top: 1px solid #e6edf6;
        padding: 14px 0 6px;
        color: #667a98;
        font-size: .88rem;
    }

    .plpi-footer strong {
        color: var(--plpi-navy);
    }

    .plpi-footer p {
        margin: 0;
        line-height: 1.3;
    }

    .plpi-footer p + p {
        margin-top: 2px;
    }

    @media (max-width: 991.98px) {
        .plpi-hero {
            grid-template-columns: 1fr;
        }

        .plpi-hero-image {
            width: min(100%, 360px);
        }

        .plpi-feature-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .plpi-journal-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .plpi-menu {
            gap: 12px;
            flex-wrap: wrap;
        }

        .plpi-mockup-row {
            grid-template-columns: 100px minmax(0, 1fr) 90px;
        }
    }

    @media (max-width: 767.98px) {
        .plpi-brand {
            gap: 10px;
        }

        .plpi-brand img {
            width: 42px;
            height: 42px;
        }

        .plpi-brand-title {
            font-size: 1.38rem;
        }

        .plpi-brand-subtitle {
            font-size: .64rem;
        }

        .plpi-nav {
            flex-wrap: wrap;
            min-height: auto;
            padding: 10px 0;
            align-items: center;
        }

        .plpi-menu-toggle {
            display: inline-flex;
        }

        .plpi-menu {
            width: 100%;
            order: 3;
            justify-content: flex-start;
            flex-direction: column;
            align-items: stretch;
            gap: 4px;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #e8eef8;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            transform: translateY(-4px);
            transition: max-height .24s ease, opacity .2s ease, transform .2s ease;
        }

        .plpi-nav.is-menu-open .plpi-menu {
            max-height: 360px;
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .plpi-menu a {
            width: 100%;
            justify-content: flex-start;
            padding: 10px 8px;
        }

        .plpi-menu a.plpi-menu-cta {
            justify-content: center;
            margin-top: 4px;
        }

        .plpi-feature-grid,
        .plpi-stat-grid,
        .plpi-mini-cards,
        .plpi-journal-grid {
            grid-template-columns: 1fr;
        }

        .plpi-mockup-row {
            grid-template-columns: 92px minmax(0, 1fr) 84px;
            font-size: .75rem;
        }
    }
</style>

<div class="plpi-shell">
    <header class="plpi-header">
        <nav class="plpi-nav">
            <a class="plpi-brand" href="<?= site_url('/') ?>">
                <img src="<?= esc($publicLogoUrl) ?>" alt="PLPI">
                <span class="plpi-brand-text">
                    <span class="plpi-brand-title">PLPI</span>
                    <span class="plpi-brand-subtitle">Pusat Layanan Publikasi Ilmiah</span>
                </span>
            </a>
            <button
                type="button"
                class="plpi-menu-toggle"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="plpiPublicMenu"
            >
                <i class="bi bi-list"></i>
            </button>
            <div class="plpi-menu" id="plpiPublicMenu">
                <a href="<?= site_url('/') ?>" class="active"><i class="bi bi-house-door"></i><span>Beranda</span></a>
                <a href="<?= site_url('loa/request') ?>"><i class="bi bi-send"></i><span>Ajukan LoA</span></a>
                <a href="<?= site_url('loa/verify') ?>"><i class="bi bi-shield-check"></i><span>Verifikasi LoA</span></a>
                <a href="<?= esc($adminNavUrl) ?>" class="plpi-menu-cta"><i class="bi <?= esc($adminNavIcon) ?>"></i><span><?= esc($adminNavLabel) ?></span></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="plpi-hero">
        <div class="plpi-hero-left">
            <div class="plpi-hero-image-wrap">
                <img class="plpi-hero-image" src="<?= base_url('assets/img/hero-laptop.png') ?>" alt="Preview sistem PLPI">
            </div>
            <h1>Pusat Layanan Publikasi Ilmiah</h1>
            <p>
                Pengajuan, verifikasi, dan penerbitan LoA dalam satu sistem yang ringkas dan terintegrasi.
            </p>
            <div class="plpi-hero-actions">
                <a class="plpi-btn-main" href="<?= site_url('loa/request') ?>">Ajukan LoA</a>
                <a class="plpi-btn-soft" href="<?= site_url('loa/verify') ?>">Verifikasi LoA</a>
            </div>
        </div>
        <div class="plpi-mockup">
            <div class="plpi-mockup-top">
                <span class="plpi-dot"></span>
                <span class="plpi-dot"></span>
                <span class="plpi-dot"></span>
            </div>
            <div class="plpi-mini-cards">
                <div class="plpi-mini-card">
                    <strong><?= esc((string) ($requestStats['total'] ?? 0)) ?></strong>
                    <span>Permohonan</span>
                </div>
                <div class="plpi-mini-card">
                    <strong><?= esc((string) ($requestStats['letters'] ?? 0)) ?></strong>
                    <span>LoA Terbit</span>
                </div>
                <div class="plpi-mini-card">
                    <strong><?= esc((string) ($requestStats['pending'] ?? 0)) ?></strong>
                    <span>Menunggu Verifikasi</span>
                </div>
            </div>
            <div class="plpi-chart">
                <svg viewBox="0 0 360 90" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <polyline fill="none" stroke="#cfdced" stroke-width="1.5" points="0,76 360,76"/>
                    <polyline fill="none" stroke="#2b59b5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                        points="0,62 40,58 80,66 120,38 160,44 200,34 240,50 280,30 320,40 360,26"/>
                </svg>
            </div>
            <div class="plpi-mockup-list">
                <?php if (! empty($latestRequests)): ?>
                    <?php foreach (array_slice($latestRequests, 0, 3) as $row): ?>
                        <?php $statusMeta = $mapStatus((string) ($row['status'] ?? 'pending')); ?>
                        <div class="plpi-mockup-row">
                            <span class="code"><?= esc((string) ($row['request_code'] ?? '-')) ?></span>
                            <span class="title"><?= esc((string) ($row['title'] ?? '-')) ?></span>
                            <span class="st <?= $statusMeta['class'] === 'approved' ? 'ok' : 'wait' ?>"><?= esc($statusMeta['label']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="plpi-mockup-row">
                        <span class="code">-</span>
                        <span class="title">Belum ada permohonan</span>
                        <span class="st wait">Diproses</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </section>

        <section class="plpi-section" id="layanan">
        <h2 class="plpi-section-title">Layanan Utama</h2>
        <p class="plpi-section-subtitle">Seluruh alur pengajuan LoA tersedia dalam satu dashboard terintegrasi.</p>
        <div class="plpi-feature-grid">
            <article class="plpi-feature-card">
                <div class="plpi-feature-head">
                    <span class="plpi-feature-icon"><i class="bi bi-file-earmark-text"></i></span>
                    <h3>Pengajuan LoA</h3>
                </div>
                <div class="plpi-feature-body">
                    <p>Ajukan permohonan LoA secara online dengan data naskah yang terstruktur.</p>
                    <div class="plpi-card-action">
                        <a href="<?= site_url('loa/request') ?>" class="plpi-card-link">Lihat &rarr;</a>
                    </div>
                </div>
            </article>
            <article class="plpi-feature-card">
                <div class="plpi-feature-head">
                    <span class="plpi-feature-icon"><i class="bi bi-search"></i></span>
                    <h3>Verifikasi LoA</h3>
                </div>
                <div class="plpi-feature-body">
                    <p>Monitor proses verifikasi dan validasi LoA melalui halaman status pengajuan.</p>
                    <div class="plpi-card-action">
                        <a href="<?= site_url('loa/verify') ?>" class="plpi-card-link">Lihat &rarr;</a>
                    </div>
                </div>
            </article>
        </div>
        </section>

        <section class="plpi-section">
        <h2 class="plpi-section-title">Permohonan Terbaru</h2>
        <p class="plpi-section-subtitle">Permohonan terbaru diurutkan dari data masuk paling baru untuk tindak lanjut.</p>
        <div class="plpi-table-card">
            <div class="table-responsive">
                <table class="table plpi-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Judul Naskah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($latestRequests)): ?>
                            <?php foreach ($latestRequests as $idx => $row): ?>
                                <?php $statusMeta = $mapStatus((string) ($row['status'] ?? 'pending')); ?>
                                <tr>
                                    <td><?= esc((string) ($idx + 1)) ?></td>
                                    <td><?= esc((string) ($row['request_code'] ?? '-')) ?></td>
                                    <td><?= esc((string) ($row['title'] ?? '-')) ?></td>
                                    <td><span class="badge-soft <?= esc($statusMeta['class']) ?>"><?= esc($statusMeta['label']) ?></span></td>
                                    <td><?= esc(plpi_format_date($row['created_at'] ?? null)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data permohonan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </section>

        <section class="plpi-section" id="profil-jurnal">
        <h2 class="plpi-section-title">Profil Jurnal</h2>
        <p class="plpi-journal-subtitle">Profil jurnal memuat identitas utama dan nomor ISSN untuk validasi cepat.</p>
        <div class="plpi-journal-grid">
            <?php if (! empty($journalProfiles)): ?>
                <?php foreach ($journalProfiles as $journal): ?>
                    <?php
                        $journalCode = strtoupper((string) ($journal['code'] ?? 'JURNAL'));
                        $logoDataUri = trim((string) ($journal['logo_data_uri'] ?? ''));
                        $journalUrl = trim((string) ($journal['website_url'] ?? ''));
                    ?>
                    <article class="plpi-journal-card">
                        <div class="plpi-journal-cover">
                            <?php if ($logoDataUri !== ''): ?>
                                <img src="<?= esc($logoDataUri) ?>" alt="<?= esc((string) ($journal['name'] ?? 'Logo Jurnal')) ?>" class="plpi-journal-logo">
                            <?php endif; ?>
                            <span class="plpi-journal-badge"><?= esc($journalCode) ?></span>
                        </div>
                        <h3 class="plpi-journal-title"><?= esc((string) ($journal['name'] ?? '-')) ?></h3>
                        <p class="plpi-journal-country">Indonesia</p>
                        <div class="plpi-journal-meta">
                            <span class="plpi-journal-pill">E-ISSN <?= esc((string) ($journal['e_issn'] ?: '-')) ?></span>
                            <span class="plpi-journal-pill">P-ISSN <?= esc((string) ($journal['p_issn'] ?: '-')) ?></span>
                        </div>
                        <p class="plpi-journal-publisher"><?= esc((string) (($journal['publisher_name'] ?? '-') ?: '-')) ?></p>
                        <a
                            href="<?= $journalUrl !== '' ? esc($journalUrl) : '#' ?>"
                            class="plpi-journal-card-link <?= $journalUrl === '' ? 'is-disabled' : '' ?>"
                            <?= $journalUrl !== '' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
                            aria-label="Buka website jurnal"
                        ><i class="bi bi-arrow-up-right"></i></a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="plpi-journal-card">
                    <div class="plpi-journal-cover">
                        <span class="plpi-journal-badge">JURNAL</span>
                    </div>
                    <h3 class="plpi-journal-title">Belum ada data jurnal</h3>
                    <p class="plpi-journal-country">Indonesia</p>
                    <div class="plpi-journal-meta">
                        <span class="plpi-journal-pill">E-ISSN -</span>
                        <span class="plpi-journal-pill">P-ISSN -</span>
                    </div>
                    <p class="plpi-journal-publisher">Tambahkan jurnal dari panel admin.</p>
                    <span class="plpi-journal-card-link is-disabled" aria-hidden="true"><i class="bi bi-arrow-up-right"></i></span>
                </article>
            <?php endif; ?>
        </div>
        </section>

    </main>

    <footer class="plpi-footer" id="tentang">
        <p><strong>PLPI</strong> &copy; <?= date('Y') ?> - Pusat Layanan Publikasi Ilmiah</p>
        <p>Developed By KSJ <span style="color:#dc3545;">&#10084;</span></p>
    </footer>
</div>

<script>
    (() => {
        const nav = document.querySelector('.plpi-nav');
        const toggle = document.querySelector('.plpi-menu-toggle');
        const menu = document.getElementById('plpiPublicMenu');
        if (!nav || !toggle || !menu) {
            return;
        }

        const mobileQuery = window.matchMedia('(max-width: 767.98px)');

        const closeMenu = () => {
            nav.classList.remove('is-menu-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<i class="bi bi-list"></i>';
            toggle.setAttribute('aria-label', 'Buka menu navigasi');
        };

        const openMenu = () => {
            nav.classList.add('is-menu-open');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.innerHTML = '<i class="bi bi-x-lg"></i>';
            toggle.setAttribute('aria-label', 'Tutup menu navigasi');
        };

        toggle.addEventListener('click', () => {
            if (nav.classList.contains('is-menu-open')) {
                closeMenu();
                return;
            }
            openMenu();
        });

        menu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (mobileQuery.matches) {
                    closeMenu();
                }
            });
        });

        mobileQuery.addEventListener('change', (event) => {
            if (!event.matches) {
                closeMenu();
            }
        });
    })();
</script>

<?= $this->endSection() ?>
