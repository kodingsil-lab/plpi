<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h3 style="margin-top:0">Hasil Verifikasi LoA</h3>
    <p><b>Nomor yang dicek:</b> <?= esc((string) ($number ?? '-')) ?></p>
    <hr>
    <?php if (! empty($letter)): ?>
        <p class="text-success"><b>Valid.</b> Nomor LoA ditemukan di sistem.</p>
        <p><b>Judul:</b> <?= esc((string) ($letter['title'] ?? '-')) ?></p>
        <p><b>Jurnal:</b> <?= esc((string) ($journal['name'] ?? '-')) ?></p>
        <p><b>Status:</b> <?= esc((string) ($letter['status'] ?? '-')) ?></p>
        <p><b>Diterbitkan:</b> <?= esc((string) ($letter['published_at'] ?? '-')) ?></p>
        <a class="btn2" href="<?= site_url('loa/v/' . (string) ($letter['public_token'] ?? '')) ?>" target="_blank">Lihat Dokumen</a>
    <?php else: ?>
        <p class="text-danger"><b>Tidak valid.</b> Nomor LoA tidak ditemukan.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
