<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<style>
    .request-detail-card {
        border: 1px solid #d9e4f2;
        border-radius: 14px;
        background: #fff;
    }

    .request-detail-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .request-detail-table {
        border: 1px solid #d9e4f2;
        border-radius: 12px;
        overflow: hidden;
        background: #fbfdff;
    }

    .request-detail-row {
        display: grid;
        grid-template-columns: 220px 28px minmax(0, 1fr);
        align-items: start;
        min-height: 52px;
        border-bottom: 1px dashed #d7e2f1;
    }

    .request-detail-row:last-child {
        border-bottom: 0;
    }

    .request-detail-key,
    .request-detail-sep,
    .request-detail-value {
        padding: 12px 14px;
    }

    .request-detail-key {
        font-weight: 700;
        color: #24466f;
        border-right: 1px dashed #d7e2f1;
        background: #f6f9ff;
    }

    .request-detail-sep {
        text-align: center;
        font-weight: 700;
        color: #3d5f89;
        border-right: 1px dashed #d7e2f1;
        background: #f9fbff;
    }

    .request-detail-value {
        color: #173a67;
        font-weight: 600;
        word-break: break-word;
        line-height: 1.55;
    }

    .request-detail-value .wa-link {
        color: inherit;
        text-decoration: none;
    }

    .request-detail-value .wa-link:hover {
        text-decoration: none;
    }

    .request-detail-value ol,
    .request-detail-value ul {
        margin: 0;
        padding-left: 18px;
    }

    @media (max-width: 768px) {
        .request-detail-row {
            grid-template-columns: 1fr;
        }

        .request-detail-key,
        .request-detail-sep {
            border-right: 0;
            border-bottom: 1px dashed #d7e2f1;
        }

        .request-detail-sep {
            display: none;
        }
    }
</style>
<?php
    helper('status_badge');
    $statusRaw = (string) ($row['status'] ?? 'pending');
    $hasPublishedLetter = (int) ($row['has_published_letter'] ?? 0) === 1;
    $statusMeta = plpi_request_status_meta($statusRaw, $hasPublishedLetter);
    $isActionable = in_array($statusRaw, ['pending', 'revision'], true);
    $articleUrl = trim((string) ($row['article_url'] ?? ''));

    $authors = [];
    $authorsRaw = $row['authors_json'] ?? null;
    if (is_string($authorsRaw) && trim($authorsRaw) !== '') {
        $decodedAuthors = json_decode($authorsRaw, true);
        if (is_array($decodedAuthors)) {
            foreach ($decodedAuthors as $item) {
                if (is_array($item) && isset($item['name'])) {
                    $name = trim((string) $item['name']);
                    if ($name !== '') {
                        if (preg_match('/^[^:]+:\s*(.+)$/', $name, $match)) {
                            $name = trim((string) ($match[1] ?? ''));
                        }
                        $authors[] = $name;
                    }
                } elseif (is_string($item)) {
                    $name = trim($item);
                    if ($name !== '') {
                        if (preg_match('/^[^:]+:\s*(.+)$/', $name, $match)) {
                            $name = trim((string) ($match[1] ?? ''));
                        }
                        $authors[] = $name;
                    }
                }
            }
        }
    }

    $affiliations = [];
    $affRaw = $row['affiliations_json'] ?? null;
    if (is_string($affRaw) && trim($affRaw) !== '') {
        $decodedAffiliations = json_decode($affRaw, true);
        if (is_array($decodedAffiliations)) {
            foreach ($decodedAffiliations as $item) {
                if (is_array($item) && isset($item['affiliation'])) {
                    $line = trim((string) $item['affiliation']);
                    if ($line !== '') {
                        $affiliations[] = $line;
                    }
                } elseif (is_string($item)) {
                    $line = trim($item);
                    if ($line !== '') {
                        $affiliations[] = $line;
                    }
                }
            }
        }
    }

    $volume = trim((string) ($row['volume'] ?? '')) ?: '-';
    $issueNumber = trim((string) ($row['issue_number'] ?? '')) ?: '-';
    $publishedYear = trim((string) ($row['published_year'] ?? '')) ?: '-';

    $whatsappRaw = trim((string) ($row['whatsapp_number'] ?? ''));
    $whatsappDisplay = $whatsappRaw !== '' ? $whatsappRaw : '-';
    $whatsappWaNumber = null;
    if ($whatsappRaw !== '') {
        $digits = preg_replace('/\D+/', '', $whatsappRaw) ?? '';
        if ($digits !== '') {
            if (str_starts_with($digits, '0')) {
                $digits = '62' . substr($digits, 1);
            } elseif (str_starts_with($digits, '8')) {
                $digits = '62' . $digits;
            }

            if (str_starts_with($digits, '62') && strlen($digits) >= 9) {
                $whatsappWaNumber = $digits;
                $whatsappDisplay = '+' . $digits;
            }
        }
    }
?>
<div class="detail-page request-detail-page">
    <div class="dashboard-card letters-table-card myletters-table-card request-detail-card">
        <div class="card-body">
            <div class="request-detail-header">
                <h5 class="section-title mb-0">Detail Permohonan</h5>
                <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-waiting')) ?>">
                    <?= esc((string) ($statusMeta['label'] ?? 'Menunggu')) ?>
                </span>
            </div>

            <div class="request-detail-table">
                <div class="request-detail-row">
                    <div class="request-detail-key">Kode Permohonan</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc((string) ($row['request_code'] ?? '-')) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Jurnal</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc((string) ($row['journal_name'] ?? '-')) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Judul Naskah</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc((string) ($row['title'] ?? '-')) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Email Korespondensi</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc((string) ($row['corresponding_email'] ?? '-')) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Nomor WhatsApp</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value">
                        <?php if ($whatsappWaNumber !== null): ?>
                            <a class="wa-link" href="<?= esc('https://wa.me/' . $whatsappWaNumber) ?>" target="_blank" rel="noopener noreferrer"><?= esc($whatsappDisplay) ?></a>
                        <?php else: ?>
                            <?= esc($whatsappDisplay) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Volume</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc($volume) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Nomor</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc($issueNumber) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Tahun</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc($publishedYear) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Tanggal Pengajuan</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value"><?= esc(plpi_format_date($row['created_at'] ?? null, true)) ?></div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">URL Artikel</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value">
                        <?php if ($articleUrl !== ''): ?>
                            <a href="<?= esc($articleUrl) ?>" target="_blank" rel="noopener noreferrer" class="profile-link-value"><?= esc($articleUrl) ?></a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Identitas Penulis</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value">
                        <?php if ($authors !== []): ?>
                            <ol>
                                <?php foreach ($authors as $author): ?>
                                    <li><?= esc($author) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
                <div class="request-detail-row">
                    <div class="request-detail-key">Afiliasi Penulis</div>
                    <div class="request-detail-sep">:</div>
                    <div class="request-detail-value">
                        <?php if ($affiliations !== []): ?>
                            <ul>
                                <?php foreach ($affiliations as $affiliation): ?>
                                    <li><?= esc($affiliation) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="detail-actions mt-3">
                <?php if ($isActionable): ?>
                    <form method="post" action="<?= site_url('admin/loa-requests/' . (string) ($row['id'] ?? 0) . '/approve') ?>">
                        <button class="btn btn-primary-main" type="submit">Setujui</button>
                    </form>
                    <form method="post" action="<?= site_url('admin/loa-requests/' . (string) ($row['id'] ?? 0) . '/reject') ?>" onsubmit="return confirm('Tolak permohonan ini?')">
                        <button class="btn btn-outline-danger" type="submit">Tolak</button>
                    </form>
                <?php endif; ?>
                <a class="btn btn-light-soft" href="<?= site_url('admin/loa-requests') ?>">Kembali</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
