<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

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
        background: #f2f6fb;
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
        gap: 10px;
        color: var(--plpi-navy);
        font-size: 1.38rem;
        font-weight: 800;
        letter-spacing: .2px;
        text-decoration: none;
    }

    .plpi-brand img {
        width: 40px;
        height: 40px;
        display: block;
    }

    .plpi-menu {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .plpi-menu a {
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

    .plpi-login-btn {
        text-decoration: none;
        border: 1px solid var(--plpi-navy-soft);
        background: var(--plpi-navy-soft);
        color: #fff;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: .9rem;
        font-weight: 700;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .plpi-login-btn:hover {
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

    .plpi-hero h1 {
        margin: 0 0 12px;
        color: var(--plpi-navy);
        font-size: clamp(1.35rem, 2.2vw, 1.95rem);
        line-height: 1.2;
        font-weight: 800;
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
        border: 1px solid var(--plpi-navy-soft);
        background: var(--plpi-navy-soft);
        color: #fff;
    }

    .plpi-btn-main:hover {
        border-color: var(--plpi-navy);
        background: var(--plpi-navy);
        color: #fff;
    }

    .plpi-btn-soft {
        border: 1px solid #c8d7ee;
        background: #fff;
        color: var(--plpi-navy-soft);
    }

    .plpi-btn-soft:hover {
        border-color: var(--plpi-navy-soft);
        color: var(--plpi-navy);
    }

    .plpi-mockup {
        background: #fff;
        border: 1px solid var(--plpi-line);
        border-radius: 16px;
        box-shadow: 0 14px 34px rgba(20, 45, 84, 0.08);
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
    }

    .plpi-mockup-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        align-items: center;
        padding: 7px 10px;
        font-size: .76rem;
        color: #566a8b;
        background: #fff;
    }

    .plpi-mockup-row + .plpi-mockup-row {
        border-top: 1px solid #edf2f8;
    }

    .plpi-mockup-row .code {
        color: #2c446c;
        font-weight: 700;
    }

    .plpi-mockup-row .st {
        font-weight: 700;
        font-size: .7rem;
        border-radius: 999px;
        padding: 2px 7px;
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
    }

    .plpi-feature-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .plpi-feature-card {
        background: #ffffff;
        border: 1px solid var(--plpi-line);
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(18, 41, 77, 0.05);
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
    }

    .plpi-feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(18, 41, 77, 0.08);
        border-color: #ced9ea;
    }

    .plpi-feature-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        margin: -20px -20px 12px;
        background: #fbfdff;
        border-bottom: 1px solid var(--plpi-line);
    }

    .plpi-feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eef2ff;
        color: #1e3a8a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 12px;
    }

    .plpi-feature-card h3 {
        margin: 0;
        font-size: 16px;
        color: #1f2937;
        font-weight: 600;
    }

    .plpi-feature-card p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.55;
    }

    .plpi-feature-body {
        display: flex;
        flex-direction: column;
        height: 100%;
        gap: 8px;
    }

    .plpi-feature-body p {
        min-height: 72px;
    }

    .plpi-card-action {
        margin-top: 0;
        padding-top: 6px;
        border-top: 1px dashed #e5ecf6;
    }

    .plpi-card-link {
        margin-top: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--plpi-navy-soft);
        background: var(--plpi-navy-soft);
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        align-self: flex-start;
    }

    .plpi-card-link:hover {
        color: #fff;
        background: var(--plpi-navy);
        border-color: var(--plpi-navy);
        box-shadow: 0 6px 14px rgba(43, 89, 181, .25);
        transform: translateY(-1px);
    }

    .plpi-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
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
        background: #fff;
        border: 1px solid var(--plpi-line);
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(18, 41, 77, 0.05);
        overflow: hidden;
    }

    .plpi-table-card .card-head {
        padding: 12px 14px;
        border-bottom: 1px solid var(--plpi-line);
        background: #fbfdff;
        color: var(--plpi-navy);
        font-weight: 700;
    }

    .plpi-table {
        margin: 0;
    }

    .plpi-table thead th {
        background: #f3f7fc;
        color: #476185;
        font-size: .8rem;
        font-weight: 700;
        border-bottom: 1px solid var(--plpi-line);
        text-transform: uppercase;
        letter-spacing: .2px;
    }

    .plpi-table tbody td {
        font-size: .9rem;
        color: #2f415f;
        border-color: #edf2f8;
        vertical-align: middle;
    }

    .badge-soft {
        border-radius: 999px;
        font-size: .74rem;
        padding: 5px 9px;
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
        padding: 16px 0 8px;
        color: #667a98;
        font-size: .88rem;
    }

    .plpi-footer strong {
        color: var(--plpi-navy);
    }

    @media (max-width: 991.98px) {
        .plpi-hero {
            grid-template-columns: 1fr;
        }

        .plpi-feature-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .plpi-menu {
            gap: 12px;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 767.98px) {
        .plpi-nav {
            flex-wrap: wrap;
            min-height: auto;
            padding: 10px 0;
        }

        .plpi-menu {
            width: 100%;
            order: 3;
            justify-content: flex-start;
            padding-top: 6px;
        }

        .plpi-feature-grid,
        .plpi-stat-grid,
        .plpi-mini-cards {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="plpi-shell">
    <header class="plpi-header">
        <nav class="plpi-nav">
            <a class="plpi-brand" href="<?= site_url('/') ?>">
                <img src="<?= base_url('assets/img/plpi-geo-logo.svg') ?>" alt="PLPI">
                <span>PLPI</span>
            </a>
            <div class="plpi-menu">
                <a href="<?= site_url('/') ?>" class="active">Beranda</a>
                <a href="#layanan">Layanan</a>
                <a href="<?= site_url('loa/verify') ?>">Cek Status</a>
                <a href="#tentang">Tentang</a>
            </div>
            <a class="plpi-login-btn" href="<?= site_url('login') ?>">Login Admin</a>
        </nav>
    </header>

    <main>
        <section class="plpi-hero">
        <div>
            <h1>Pusat Layanan Publikasi Ilmiah</h1>
            <p>
                Pengelolaan LoA, invoice, dan layanan jurnal dalam satu sistem yang ringkas dan terintegrasi.
            </p>
            <div class="plpi-hero-actions">
                <a class="plpi-btn-main" href="<?= site_url('loa/request') ?>">Ajukan LoA</a>
                <a class="plpi-btn-soft" href="<?= site_url('loa/verify') ?>">Cek Status</a>
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
                    <strong>128</strong>
                    <span>Permohonan</span>
                </div>
                <div class="plpi-mini-card">
                    <strong>42</strong>
                    <span>LoA Terbit</span>
                </div>
                <div class="plpi-mini-card">
                    <strong>17</strong>
                    <span>Invoice</span>
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
                <div class="plpi-mockup-row">
                    <span class="code">REQ-2026-019</span>
                    <span>Jurnal Sains</span>
                    <span class="st wait">Diproses</span>
                </div>
                <div class="plpi-mockup-row">
                    <span class="code">REQ-2026-018</span>
                    <span>Jurnal Manajemen</span>
                    <span class="st ok">Disetujui</span>
                </div>
            </div>
        </div>
        </section>

        <section class="plpi-section" id="layanan">
        <h2 class="plpi-section-title">Layanan Utama</h2>
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
                    <h3>Cek Status Naskah</h3>
                </div>
                <div class="plpi-feature-body">
                    <p>Monitor proses review dan validasi LoA melalui halaman status publik.</p>
                    <div class="plpi-card-action">
                        <a href="<?= site_url('loa/verify') ?>" class="plpi-card-link">Lihat &rarr;</a>
                    </div>
                </div>
            </article>
            <article class="plpi-feature-card">
                <div class="plpi-feature-head">
                    <span class="plpi-feature-icon"><i class="bi bi-receipt"></i></span>
                    <h3>Kelola Invoice</h3>
                </div>
                <div class="plpi-feature-body">
                    <p>Pengelolaan invoice penerbitan dilakukan dalam alur administrasi yang rapi.</p>
                    <div class="plpi-card-action">
                        <a href="<?= site_url('login') ?>" class="plpi-card-link">Lihat &rarr;</a>
                    </div>
                </div>
            </article>
            <article class="plpi-feature-card">
                <div class="plpi-feature-head">
                    <span class="plpi-feature-icon"><i class="bi bi-archive"></i></span>
                    <h3>Arsip Dokumen</h3>
                </div>
                <div class="plpi-feature-body">
                    <p>Dokumen LoA tersimpan dan dapat ditelusuri kembali dengan mudah.</p>
                    <div class="plpi-card-action">
                        <a href="<?= site_url('loa/verify') ?>" class="plpi-card-link">Lihat &rarr;</a>
                    </div>
                </div>
            </article>
        </div>
        </section>

        <section class="plpi-section" id="tentang">
        <h2 class="plpi-section-title">Ringkasan Sistem</h2>
        <div class="plpi-stat-grid">
            <article class="plpi-stat-card">
                <span>Pengajuan Diproses</span>
                <strong>128</strong>
            </article>
            <article class="plpi-stat-card">
                <span>Menunggu Verifikasi</span>
                <strong>36</strong>
            </article>
            <article class="plpi-stat-card">
                <span>LoA Terbit</span>
                <strong>42</strong>
            </article>
        </div>
        </section>

        <section class="plpi-section">
        <div class="plpi-table-card">
            <div class="card-head">Permohonan Terbaru</div>
            <div class="table-responsive">
                <table class="table plpi-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Jurnal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>REQ-2026-019</td>
                            <td>Jurnal Pendidikan Sains</td>
                            <td><span class="badge-soft processing">Diproses</span></td>
                            <td>07 Apr 2026</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>REQ-2026-018</td>
                            <td>Jurnal Manajemen Publik</td>
                            <td><span class="badge-soft approved">Disetujui</span></td>
                            <td>06 Apr 2026</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>REQ-2026-017</td>
                            <td>Jurnal Teknologi Terapan</td>
                            <td><span class="badge-soft rejected">Ditolak</span></td>
                            <td>05 Apr 2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </section>
    </main>

    <footer class="plpi-footer">
        <p><strong>PLPI</strong> &copy; <?= date('Y') ?> - Pusat Layanan Publikasi Ilmiah.</p>
        <p>Sistem layanan untuk pengelolaan LoA, Invoice, dan administrasi jurnal secara terintegrasi.</p>
    </footer>
</div>

<?= $this->endSection() ?>
