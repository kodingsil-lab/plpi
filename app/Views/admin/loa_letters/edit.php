<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-body">
        <h5 class="mb-3">Edit LoA #<?= esc((string) ($row['id'] ?? 0)) ?></h5>
        <form method="post" action="<?= site_url('admin/loa-letters/' . (string) ($row['id'] ?? 0)) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nomor LoA</label>
                    <input class="form-control" type="text" value="<?= esc((string) ($row['loa_number'] ?? '-')) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="published" <?= (($row['status'] ?? '') === 'published') ? 'selected' : '' ?>>LoA Terbit</option>
                        <option value="revoked" <?= (($row['status'] ?? '') === 'revoked') ? 'selected' : '' ?>>Dicabut</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Judul</label>
                    <input name="title" class="form-control" type="text" value="<?= esc((string) ($row['title'] ?? '')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">URL Artikel</label>
                    <input name="article_url" class="form-control" type="url" value="<?= esc((string) ($row['article_url'] ?? '')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Korespondensi</label>
                    <input name="corresponding_email" class="form-control" type="email" value="<?= esc((string) ($row['corresponding_email'] ?? '')) ?>">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3 myletters-actions">
                <button class="btn activity-btn user-action-btn user-action-edit" type="submit">Simpan</button>
                <a class="btn activity-btn user-action-btn user-action-detail" target="_blank" href="<?= site_url('loa/v/' . (string) ($row['public_token'] ?? '') . '/preview') ?>">Preview PDF</a>
                <button class="btn activity-btn user-action-btn user-action-edit" type="submit" formaction="<?= site_url('admin/loa-letters/' . (string) ($row['id'] ?? 0) . '/regenerate') ?>">Regenerate PDF</button>
                <a class="btn activity-btn user-action-btn user-action-detail" href="<?= site_url('admin/loa-letters') ?>">Kembali</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
