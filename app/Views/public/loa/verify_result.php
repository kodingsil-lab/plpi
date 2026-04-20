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
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
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

    .verify-result-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 8px 12px;
        background: #e8f8ef;
        color: #176548;
        font-weight: 700;
        font-size: .85rem;
        border: 1px solid #bfe8d0;
        white-space: nowrap;
    }

    .verify-result-badge .verify-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #176548;
        color: #fff;
        font-size: 0.9rem;
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
        margin-bottom: 16px;
        color: #2b4164;
        font-size: .92rem;
    }

    .verify-result-row {
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 8px 12px;
        padding: 10px 0;
        border-bottom: 1px dashed #d9e4f2;
        align-items: start;
    }

    .verify-result-row:last-child {
        border-bottom: none;
    }

    .verify-result-row strong {
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
        <div>
            <h3>Hasil Verifikasi LoA</h3>
            <p class="verify-result-number"><strong>Nomor yang dicek:</strong> <?= esc((string) ($number ?? '-')) ?></p>
        </div>
        <?php if (! empty($letter)): ?>
            <div class="verify-result-badge">
                <span class="verify-icon">✓</span>
                VALID
            </div>
        <?php endif; ?>
    </div>

    <?php if (! empty($letter)): ?>
        <?php
            $authors = [];
            if (! empty($letter['authors_json'])) {
                $decoded = is_array($letter['authors_json']) ? $letter['authors_json'] : json_decode((string) $letter['authors_json'], true);
                $authors = is_array($decoded) ? $decoded : [];
            }
            $affiliations = [];
            if (! empty($letter['affiliations_json'])) {
                $decoded = is_array($letter['affiliations_json']) ? $letter['affiliations_json'] : json_decode((string) $letter['affiliations_json'], true);
                $affiliations = is_array($decoded) ? $decoded : [];
            }
            $loaNumber = plpi_format_loa_number($letter['loa_number'] ?? '-');
            $articleUrl = trim((string) ($letter['article_url'] ?? ''));
            $articleId = trim((string) ($letter['article_id_external'] ?? ''));
        ?>

        <div class="verify-result-status ok">Valid. Nomor LoA ditemukan di sistem.</div>
        <?php
                $normalizeText = static function ($value): string {
                    if (is_string($value)) {
                        $value = trim($value);
                    } elseif (is_scalar($value)) {
                        $value = trim((string) $value);
                    } elseif (is_array($value) && isset($value['name'])) {
                        $value = trim((string) $value['name']);
                    } else {
                        return '';
                    }

                    return preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*:\s*/iu', '', $value);
                };

                $authorText = implode(', ', array_filter(array_map($normalizeText, $authors)));
                $affiliationText = implode(', ', array_filter(array_map($normalizeText, $affiliations)));
            ?>
        <div class="verify-result-meta">
            <div class="verify-result-row"><strong>Nomor LoA</strong><span><?= esc($loaNumber) ?></span></div>
            <div class="verify-result-row"><strong>Judul</strong><span><?= esc((string) ($letter['title'] ?? '-')) ?></span></div>
            <div class="verify-result-row"><strong>Jurnal</strong><span><?= esc((string) ($journal['name'] ?? '-')) ?></span></div>
            <div class="verify-result-row"><strong>Terbit</strong><span><span style="display:inline-flex; align-items:center; gap:0.35rem; padding:4px 8px; border-radius:999px; background:#e8f8ef; color:#176548; border:1px solid #bfe8d0; font-weight:700;">Terbit</span></span></div>
            <div class="verify-result-row"><strong>Diterbitkan</strong><span><?= esc(plpi_format_date($letter['published_at'] ?? null, true)) ?></span></div>
            <div class="verify-result-row"><strong>Volume</strong><span><?= esc((string) ($letter['volume'] ?? '-')) ?></span></div>
            <div class="verify-result-row"><strong>Nomor Edisi</strong><span><?= esc((string) ($letter['issue_number'] ?? '-')) ?></span></div>
            <div class="verify-result-row"><strong>Tahun Terbit</strong><span><?= esc((string) ($letter['published_year'] ?? '-')) ?></span></div>
            <?php if ($authorText !== ''): ?>
                <div class="verify-result-row"><strong>Penulis</strong><span><?= esc($authorText) ?></span></div>
            <?php endif; ?>
            <?php if ($affiliationText !== ''): ?>
                <div class="verify-result-row"><strong>Afiliasi</strong><span><?= esc($affiliationText) ?></span></div>
            <?php endif; ?>
        </div>

        <div class="verify-result-actions">
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
