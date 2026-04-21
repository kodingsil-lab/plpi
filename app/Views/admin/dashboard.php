<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php helper('status_badge'); ?>
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="dashboard-card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted fw-semibold">Menunggu</div>
                    <div class="fs-1 fw-bold text-primary"><?= esc((string) ($stats['menunggu'] ?? 0)) ?></div>
                </div>
                <div class="stat-icon stat-icon-blue"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="dashboard-card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted fw-semibold">Disetujui</div>
                    <div class="fs-1 fw-bold text-success"><?= esc((string) ($stats['disetujui'] ?? 0)) ?></div>
                </div>
                <div class="stat-icon stat-icon-green"><i class="bi bi-check2-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="dashboard-card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted fw-semibold">Ditolak</div>
                    <div class="fs-1 fw-bold text-danger"><?= esc((string) ($stats['ditolak'] ?? 0)) ?></div>
                </div>
                <div class="stat-icon stat-icon-red"><i class="bi bi-x-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="dashboard-card stat-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted fw-semibold">LoA Terbit</div>
                    <div class="fs-1 fw-bold text-info"><?= esc((string) ($stats['loa_terbit'] ?? 0)) ?></div>
                </div>
                <div class="stat-icon stat-icon-cyan"><i class="bi bi-patch-check"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-header border-0 pb-0 bg-transparent">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Permohonan Terbaru</h6>
    </div>
    <div class="card-body pt-2">
        <div class="activity-table-wrap myletters-table-wrap table-responsive">
            <table class="table table-hover align-middle mb-0 w-100 dashboard-latest-table">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>KODE</th>
                    <th>JURNAL</th>
                    <th>JUDUL</th>
                    <th>STATUS</th>
                    <th>TANGGAL</th>
                    <th>AKSI</th>
                </tr>
                </thead>
                <tbody>
                <?php if (! empty($latest)): ?>
                    <?php foreach ($latest as $i => $row): ?>
                        <?php
                            $statusRaw = (string) ($row['status'] ?? 'pending');
                            $hasPublishedLetter = (int) ($row['has_published_letter'] ?? 0) === 1;
                            $statusMeta = plpi_request_status_meta($statusRaw, $hasPublishedLetter);
                        ?>
                        <tr>
                            <td><?= esc((string) ($i + 1)) ?></td>
                            <td class="fw-semibold text-primary"><?= esc((string) ($row['request_code'] ?? '-')) ?></td>
                            <td><?= esc((string) ($row['journal_name'] ?? '-')) ?></td>
                            <td><?= esc((string) ($row['title'] ?? '-')) ?></td>
                            <td>
                                <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-waiting')) ?>">
                                    <?= esc((string) ($statusMeta['label'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td><?= esc(plpi_format_date($row['created_at'] ?? null, true)) ?></td>
                            <td>
                                <div class="myletters-actions">
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-detail action-solid action-solid-detail myletters-icon-only" href="<?= site_url('admin/loa-requests/' . (string) $row['id']) ?>" aria-label="Detail" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Detail">
                                        <iconify-icon icon="heroicons-outline:information-circle" aria-hidden="true"></iconify-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data permohonan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
