<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<?php
    $statusRaw = strtolower(trim((string) ($loaRequest['status'] ?? 'pending')));
    $statusLabel = 'Menunggu';
    $statusClass = 'status-waiting';
    $statusIcon = 'bi-hourglass-split';

    if ($statusRaw === 'approved') {
        $statusLabel = 'Disetujui';
        $statusClass = 'status-approved';
        $statusIcon = 'bi-check-circle';
    } elseif ($statusRaw === 'rejected' || $statusRaw === 'revision') {
        $statusLabel = 'Ditolak';
        $statusClass = 'status-rejected';
        $statusIcon = 'bi-x-circle';
    } elseif ($statusRaw === 'published') {
        $statusLabel = 'Terbit';
        $statusClass = 'status-published';
        $statusIcon = 'bi-patch-check';
    }
?>
<style>
    .status-page {
        max-width: 980px;
        margin: 0 auto;
    }

    .status-header {
        background: linear-gradient(145deg, #ffffff 0%, #f7faff 100%);
        border: 1px solid #dbe5f2;
        border-radius: 16px;
        box-shadow: 0 12px 24px rgba(18, 41, 77, 0.08);
        padding: 18px 20px;
        margin-bottom: 12px;
    }

    .status-title {
        margin: 0;
        color: #143f74;
        font-size: clamp(1.45rem, 2.1vw, 2rem);
        font-weight: 800;
        letter-spacing: .2px;
    }

    .status-sub {
        margin: 8px 0 0;
        color: #5f7594;
        font-size: .95rem;
    }

    .status-sub strong {
        color: #1f3f66;
    }

    .status-card {
        background: #ffffff;
        border: 1px solid #dbe4ef;
        border-radius: 16px;
        box-shadow: 0 10px 22px rgba(18, 41, 77, 0.06);
        padding: 18px 20px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
        padding: 6px 12px;
        border: 1px solid transparent;
    }

    .status-waiting {
        color: #1f5a9d;
        background: #eaf2ff;
        border-color: #c8ddff;
    }

    .status-approved,
    .status-published {
        color: #176548;
        background: #e8f8ef;
        border-color: #bfe8d0;
    }

    .status-rejected {
        color: #9f2d3a;
        background: #fdeff1;
        border-color: #f5c8d0;
    }

    .status-grid {
        margin-top: 14px;
        display: grid;
        gap: 10px;
    }

    .status-item {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 12px;
        align-items: start;
        padding: 8px 0;
        border-bottom: 1px dashed #e2eaf4;
    }

    .status-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .status-label {
        color: #4e6483;
        font-size: .86rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .status-value {
        color: #1f2f43;
        font-size: 1.05rem;
        line-height: 1.5;
    }

    .status-actions {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media (max-width: 767.98px) {
        .status-item {
            grid-template-columns: 1fr;
            gap: 6px;
        }
    }
</style>

<div class="status-page">
    <div class="status-header">
        <h1 class="status-title">Status Permohonan LoA</h1>
        <p class="status-sub">Kode Permohonan: <strong><?= esc((string) ($loaRequest['request_code'] ?? '-')) ?></strong></p>
    </div>

    <div class="status-card">
        <span class="status-badge <?= esc($statusClass) ?>">
            <i class="bi <?= esc($statusIcon) ?>" aria-hidden="true"></i>
            <span><?= esc($statusLabel) ?></span>
        </span>

        <div class="status-grid">
            <div class="status-item">
                <div class="status-label">Jurnal</div>
                <div class="status-value"><?= esc((string) ($loaRequest['journal_name'] ?? '-')) ?></div>
            </div>
            <div class="status-item">
                <div class="status-label">Judul</div>
                <div class="status-value"><?= esc((string) ($loaRequest['title'] ?? '-')) ?></div>
            </div>
            <div class="status-item">
                <div class="status-label">Email</div>
                <div class="status-value"><?= esc((string) ($loaRequest['corresponding_email'] ?? '-')) ?></div>
            </div>
            <div class="status-item">
                <div class="status-label">Tanggal Pengajuan</div>
                <div class="status-value"><?= esc(plpi_format_date($loaRequest['created_at'] ?? null, true)) ?></div>
            </div>
        </div>

        <div class="status-actions">
            <a class="btn2" href="<?= site_url('loa/request') ?>">Ajukan Lagi</a>
            <a class="btn" href="<?= site_url('/') ?>">Kembali ke Beranda</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
