<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
    helper('status_badge');
    $statusRaw = (string) ($row['status'] ?? 'pending');
    $hasPublishedLetter = (int) ($row['has_published_letter'] ?? 0) === 1;
    $statusMeta = plpi_request_status_meta($statusRaw, $hasPublishedLetter);
    $isActionable = in_array($statusRaw, ['pending', 'revision'], true);
?>
<div class="detail-page request-detail-page">
    <div class="dashboard-card letters-table-card myletters-table-card">
        <div class="card-body">
            <div class="section-head">
                <h5 class="section-title mb-0">Detail Permohonan</h5>
                <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-waiting')) ?>">
                    <?= esc((string) ($statusMeta['label'] ?? 'Menunggu')) ?>
                </span>
            </div>

            <div class="row g-3 applicant-info-grid">
                <div class="col-lg-6">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-upc-scan"></i></span>
                            <div class="profile-info-label">Kode Permohonan</div>
                        </div>
                        <div class="profile-info-value"><?= esc((string) ($row['request_code'] ?? '-')) ?></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-journal-text"></i></span>
                            <div class="profile-info-label">Jurnal</div>
                        </div>
                        <div class="profile-info-value"><?= esc((string) ($row['journal_name'] ?? '-')) ?></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-card-text"></i></span>
                            <div class="profile-info-label">Judul Naskah</div>
                        </div>
                        <div class="profile-info-value"><?= esc((string) ($row['title'] ?? '-')) ?></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-envelope"></i></span>
                            <div class="profile-info-label">Email Korespondensi</div>
                        </div>
                        <div class="profile-info-value"><?= esc((string) ($row['corresponding_email'] ?? '-')) ?></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-calendar3"></i></span>
                            <div class="profile-info-label">Tanggal Pengajuan</div>
                        </div>
                        <div class="profile-info-value"><?= esc(plpi_format_date($row['created_at'] ?? null, true)) ?></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="profile-info-item applicant-info-item">
                        <div class="applicant-info-head">
                            <span class="applicant-info-icon"><i class="bi bi-link-45deg"></i></span>
                            <div class="profile-info-label">URL Artikel</div>
                        </div>
                        <div class="profile-info-value">
                            <?php $articleUrl = trim((string) ($row['article_url'] ?? '')); ?>
                            <?php if ($articleUrl !== ''): ?>
                                <a href="<?= esc($articleUrl) ?>" target="_blank" rel="noopener noreferrer" class="profile-link-value"><?= esc($articleUrl) ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
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
