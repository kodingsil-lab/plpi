<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h3 style="margin-top:0">Detail LoA Terbit</h3>
    <p><b>Nomor LoA:</b> <?= esc((string) ($letter['loa_number'] ?? '-')) ?></p>
    <p><b>Jurnal:</b> <?= esc((string) ($journal['name'] ?? '-')) ?></p>
    <p><b>Judul:</b> <?= esc((string) ($letter['title'] ?? '-')) ?></p>
    <p><b>Status:</b> <?= esc((string) ($letter['status'] ?? '-')) ?></p>
    <p><b>Diterbitkan:</b> <?= esc(plpi_format_date($letter['published_at'] ?? null, true)) ?></p>
    <div style="display:flex;gap:8px;margin-top:12px">
        <a class="btn2" href="<?= site_url('loa/v/' . (string) ($letter['public_token'] ?? '') . '/preview') ?>" target="_blank">Preview PDF</a>
        <a class="btn" href="<?= site_url('loa/v/' . (string) ($letter['public_token'] ?? '') . '/download') ?>">Unduh PDF</a>
    </div>
</div>
<?= $this->endSection() ?>
