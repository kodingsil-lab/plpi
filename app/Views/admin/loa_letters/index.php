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
                    <?php foreach (['published' => 'LoA Terbit', 'revoked' => 'Dicabut'] as $v => $l): ?>
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
                <input type="text" name="q" value="<?= esc((string) ($filters['q'] ?? '')) ?>" class="form-control" placeholder="Nomor LoA / judul">
            </div>
            <div class="myletters-filter-btn-item">
                <label class="form-label form-label-ghost">Aksi</label>
                <button type="submit" class="btn btn-primary-main myletters-btn myletters-icon-btn myletters-icon-btn-primary">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>
            </div>
            <div class="myletters-filter-btn-item">
                <label class="form-label form-label-ghost">Aksi</label>
                <a href="<?= site_url('admin/loa-letters') ?>" class="btn btn-light-soft myletters-btn myletters-icon-btn myletters-icon-btn-light">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar LoA Terbit</h6>
        <a class="btn btn-light-soft myletters-icon-btn myletters-icon-btn-light" href="<?= site_url('admin/loa-letters/export/csv') ?>">
            <i class="bi bi-filetype-csv" aria-hidden="true"></i>
            <span>Export CSV</span>
        </a>
    </div>
    <div class="card-body pt-2">
        <div class="activity-table-wrap myletters-table-wrap table-responsive">
            <table class="table table-hover align-middle mb-0 w-100">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>NOMOR LOA</th>
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
                        <?php $status = (string) ($r['status'] ?? 'published'); ?>
                        <?php $statusMeta = plpi_letter_status_meta($status); ?>
                        <tr>
                            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
                            <td class="fw-semibold text-primary"><?= esc((string) ($r['loa_number'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['journal_name'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['title'] ?? '-')) ?></td>
                            <td>
                                <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-issued')) ?>">
                                    <?= esc((string) ($statusMeta['label'] ?? 'Terbit')) ?>
                                </span>
                            </td>
                            <td><?= esc(plpi_format_date($r['published_at'] ?? null, true)) ?></td>
                            <td>
                                <div class="myletters-actions">
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-detail user-action-preview action-solid action-solid-preview myletters-icon-only" href="<?= site_url('loa/v/' . (string) ($r['public_token'] ?? '') . '/preview') ?>" target="_blank" aria-label="Preview" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Preview">
                                        <iconify-icon icon="heroicons-outline:eye" aria-hidden="true"></iconify-icon>
                                    </a>
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-edit user-action-download action-solid action-solid-download myletters-icon-only" href="<?= site_url('loa/v/' . (string) ($r['public_token'] ?? '') . '/download') ?>" aria-label="Unduh" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Unduh">
                                        <iconify-icon icon="heroicons-outline:download" aria-hidden="true"></iconify-icon>
                                    </a>
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-edit action-solid action-solid-edit myletters-icon-only" href="<?= site_url('admin/loa-letters/' . (string) $r['id'] . '/edit') ?>" aria-label="Edit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                                        <iconify-icon icon="heroicons-outline:pencil" aria-hidden="true"></iconify-icon>
                                    </a>
                                    <form method="post" action="<?= site_url('admin/loa-letters/' . (string) $r['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Hapus LoA ini?')">
                                        <button type="submit" class="btn btn-sm activity-btn user-action-btn user-action-delete action-solid action-solid-delete myletters-icon-only" aria-label="Hapus" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hapus">
                                            <iconify-icon icon="solar:trash-bin-trash-outline" aria-hidden="true"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data LoA.</td></tr>
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


