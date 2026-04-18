<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php helper('status_badge'); ?>
<div class="dashboard-card mb-3 myletters-filter-card">
    <div class="card-body">
        <form method="get" class="myletters-filter-form">
            <div class="myletters-filter-item">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <?php foreach (plpi_request_status_filter_options() as $v => $l): ?>
                        <option value="<?= esc($v) ?>" <?= (($filters['status'] ?? '') === $v) ? 'selected' : '' ?>><?= esc($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="myletters-filter-item">
                <label class="form-label">Jurnal</label>
                <select name="journal_id" class="form-select">
                    <option value="">Semua Jurnal</option>
                    <?php foreach (($journals ?? []) as $j): ?>
                        <option value="<?= esc((string) $j['id']) ?>" <?= ((int) ($filters['journal_id'] ?? 0) === (int) $j['id']) ? 'selected' : '' ?>><?= esc((string) $j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="myletters-filter-item">
                <label class="form-label">Pencarian</label>
                <input type="text" name="q" value="<?= esc((string) ($filters['q'] ?? '')) ?>" class="form-control" placeholder="Kode / judul / email">
            </div>
            <div class="myletters-filter-btn-item">
                <label class="form-label form-label-ghost">Aksi</label>
                <button type="submit" class="btn btn-primary-main myletters-btn">Terapkan</button>
            </div>
            <div class="myletters-filter-btn-item">
                <label class="form-label form-label-ghost">Aksi</label>
                <a href="<?= site_url('admin/loa-requests') ?>" class="btn btn-light-soft myletters-btn">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Permohonan LoA</h6>
        <a class="btn btn-light-soft" href="<?= site_url('admin/loa-requests/export/csv') ?>">Export CSV</a>
    </div>
    <div class="card-body pt-2">
        <div class="activity-table-wrap myletters-table-wrap table-responsive">
            <table class="table table-hover align-middle mb-0 w-100">
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
                <?php if (! empty($rows)): ?>
                    <?php foreach ($rows as $i => $r): ?>
                        <?php
                            $status = (string) ($r['status'] ?? 'pending');
                            $hasPublishedLetter = (int) ($r['has_published_letter'] ?? 0) === 1;
                            $statusMeta = plpi_request_status_meta($status, $hasPublishedLetter);
                        ?>
                        <tr>
                            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
                            <td class="fw-semibold text-primary"><?= esc((string) ($r['request_code'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['journal_name'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['title'] ?? '-')) ?></td>
                            <td>
                                <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-waiting')) ?>">
                                    <?= esc((string) ($statusMeta['label'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td><?= esc((string) ($r['created_at'] ?? '-')) ?></td>
                            <td>
                                <div class="myletters-actions">
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-detail action-solid action-solid-detail myletters-icon-only" href="<?= site_url('admin/loa-requests/' . (string) $r['id']) ?>" aria-label="Detail" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Detail">
                                        <iconify-icon icon="heroicons-outline:information-circle" aria-hidden="true"></iconify-icon>
                                    </a>
                                    <?php if (in_array($status, ['pending', 'revision'], true)): ?>
                                        <form method="post" action="<?= site_url('admin/loa-requests/' . (string) $r['id'] . '/approve') ?>" class="d-inline">
                                            <button class="btn btn-sm activity-btn user-action-btn user-action-edit action-solid action-solid-approve myletters-icon-only" type="submit" aria-label="Setujui" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Setujui">
                                                <iconify-icon icon="heroicons-outline:check" aria-hidden="true"></iconify-icon>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= site_url('admin/loa-requests/' . (string) $r['id'] . '/reject') ?>" class="d-inline" onsubmit="return confirm('Tolak permohonan ini?')">
                                            <button class="btn btn-sm activity-btn user-action-btn user-action-delete action-solid action-solid-delete myletters-icon-only" type="submit" aria-label="Tolak" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tolak">
                                                <iconify-icon icon="heroicons-outline:x-mark" aria-hidden="true"></iconify-icon>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'table_plpi') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>


