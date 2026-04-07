<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
    <div class="card-body">
        <h5 class="mb-3">Detail Permohonan</h5>
        <div class="row g-3">
            <div class="col-md-6"><strong>Kode:</strong><div><?= esc((string) ($row['request_code'] ?? '-')) ?></div></div>
            <div class="col-md-6"><strong>Jurnal:</strong><div><?= esc((string) ($row['journal_name'] ?? '-')) ?></div></div>
            <div class="col-md-12"><strong>Judul:</strong><div><?= esc((string) ($row['title'] ?? '-')) ?></div></div>
            <div class="col-md-6"><strong>Email:</strong><div><?= esc((string) ($row['corresponding_email'] ?? '-')) ?></div></div>
            <div class="col-md-6"><strong>Status:</strong><div><?= esc((string) ($row['status'] ?? '-')) ?></div></div>
            <div class="col-md-12"><strong>URL Artikel:</strong><div><?= esc((string) ($row['article_url'] ?? '-')) ?></div></div>
        </div>
        <div class="d-flex gap-2 mt-4 myletters-actions">
            <form method="post" action="<?= site_url('admin/loa-requests/' . (string) ($row['id'] ?? 0) . '/approve') ?>">
                <button class="btn btn-primary-main" type="submit">Setujui</button>
            </form>
            <form method="post" action="<?= site_url('admin/loa-requests/' . (string) ($row['id'] ?? 0) . '/reject') ?>" onsubmit="return confirm('Tolak permohonan ini?')">
                <button class="btn btn-outline-danger" type="submit">Tolak</button>
            </form>
            <a class="btn btn-light-soft" href="<?= site_url('admin/loa-requests') ?>">Kembali</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
