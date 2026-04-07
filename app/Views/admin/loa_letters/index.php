<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
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
                <button type="submit" class="btn btn-primary-main myletters-btn">Terapkan</button>
            </div>
            <div class="myletters-filter-btn-item">
                <label class="form-label form-label-ghost">Aksi</label>
                <a href="<?= site_url('admin/loa-letters') ?>" class="btn btn-light-soft myletters-btn">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar LoA Terbit</h6>
        <a class="btn btn-light-soft" href="<?= site_url('admin/loa-letters/export/csv') ?>">Export CSV</a>
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
                        <tr>
                            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
                            <td class="fw-semibold text-primary"><?= esc((string) ($r['loa_number'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['journal_name'] ?? '-')) ?></td>
                            <td><?= esc((string) ($r['title'] ?? '-')) ?></td>
                            <td>
                                <span class="status-pill status-table-pill myletters-status-pill <?= esc($status === 'revoked' ? 'myletters-status-revision' : 'myletters-status-issued') ?>">
                                    <?= esc($status === 'revoked' ? 'Dicabut' : 'LoA Terbit') ?>
                                </span>
                            </td>
                            <td><?= esc((string) ($r['published_at'] ?? '-')) ?></td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap myletters-actions">
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-detail" href="<?= site_url('loa/v/' . (string) ($r['public_token'] ?? '')) ?>" target="_blank">Detail</a>
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-detail" href="<?= site_url('loa/v/' . (string) ($r['public_token'] ?? '') . '/preview') ?>" target="_blank">Preview</a>
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-edit" href="<?= site_url('loa/v/' . (string) ($r['public_token'] ?? '') . '/download') ?>">Unduh</a>
                                    <a class="btn btn-sm activity-btn user-action-btn user-action-edit" href="<?= site_url('admin/loa-letters/' . (string) $r['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= site_url('admin/loa-letters/' . (string) $r['id'] . '/regenerate') ?>" class="d-inline">
                                        <button type="submit" class="btn btn-sm activity-btn user-action-btn user-action-edit">Regenerate</button>
                                    </form>
                                    <form method="post" action="<?= site_url('admin/loa-letters/' . (string) $r['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Hapus LoA ini?')">
                                        <button type="submit" class="btn btn-sm activity-btn user-action-btn user-action-delete">Hapus</button>
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


