<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<style>
    .verify-result-shell {
        background: #fff;
        border: 1px solid #d9e4f2;
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(18, 41, 77, 0.08);
        padding: 18px;
    }

    .verify-result-header {
        border-bottom: 1px solid #e5edf8;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .verify-result-header h3 {
        margin: 0;
        color: #163b73;
        font-size: clamp(1.35rem, 2.1vw, 1.8rem);
        font-weight: 800;
    }

    .verify-result-number {
        margin: 8px 0 0;
        color: #2b4164;
        font-size: .95rem;
    }

    .verify-result-status {
        border-radius: 11px;
        padding: 10px 12px;
        margin-bottom: 12px;
        font-size: .92rem;
        font-weight: 700;
    }

    .verify-result-status.ok {
        color: #176548;
        background: #e8f8ef;
        border: 1px solid #bfe8d0;
    }

    .verify-result-status.no {
        color: #9f2d3a;
        background: #fdeff1;
        border: 1px solid #f5c8d0;
    }

    .verify-result-meta {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 8px 12px;
        margin-bottom: 14px;
        color: #2b4164;
        font-size: .92rem;
    }

    .verify-result-meta strong {
        color: #163b73;
    }

    .verify-result-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .verify-btn-main,
    .verify-btn-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        text-decoration: none;
        font-size: .92rem;
        font-weight: 700;
        padding: 10px 14px;
        line-height: 1;
        transition: .2s ease;
        cursor: pointer;
    }

    .verify-btn-main {
        border: 1px solid #2b59b5;
        background: linear-gradient(135deg, #2b59b5 0%, #3f70cc 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(43, 89, 181, .22);
    }

    .verify-btn-main:hover {
        border-color: #163b73;
        background: #163b73;
        color: #fff;
    }

    .verify-btn-soft {
        border: 1px solid #c5d7ef;
        background: #fff;
        color: #224a92;
    }

    .verify-btn-soft:hover {
        border-color: #2b59b5;
        color: #163b73;
    }
</style>

<div class="verify-result-shell">
    <div class="verify-result-header">
        <h3>Hasil Verifikasi LoA</h3>
        <p class="verify-result-number"><strong>Nomor yang dicek:</strong> <?= esc((string) ($number ?? '-')) ?></p>
    </div>

    <?php if (! empty($letter)): ?>
        <div class="verify-result-status ok">Valid. Nomor LoA ditemukan di sistem.</div>
        <div class="verify-result-meta">
            <strong>Judul</strong><span><?= esc((string) ($letter['title'] ?? '-')) ?></span>
            <strong>Jurnal</strong><span><?= esc((string) ($journal['name'] ?? '-')) ?></span>
            <strong>Status</strong><span><?= esc((string) ($letter['status'] ?? '-')) ?></span>
            <strong>Diterbitkan</strong><span><?= esc((string) ($letter['published_at'] ?? '-')) ?></span>
        </div>
        <div class="verify-result-actions">
            <a class="verify-btn-main" href="<?= site_url('loa/v/' . (string) ($letter['public_token'] ?? '')) ?>" target="_blank">Lihat Dokumen</a>
            <a class="verify-btn-soft" href="<?= site_url('loa/verify') ?>">Cek Lagi</a>
            <a class="verify-btn-soft" href="<?= site_url('/') ?>">Beranda</a>
        </div>
    <?php else: ?>
        <div class="verify-result-status no">Tidak valid. Nomor LoA tidak ditemukan.</div>
        <div class="verify-result-actions">
            <a class="verify-btn-main" href="<?= site_url('loa/verify') ?>">Coba Lagi</a>
            <a class="verify-btn-soft" href="<?= site_url('/') ?>">Beranda</a>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
