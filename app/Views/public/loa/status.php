<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h3 style="margin-top:0">Status Permohonan LoA</h3>
    <p>Kode Permohonan: <b><?= esc((string) ($loaRequest['request_code'] ?? '-')) ?></b></p>
    <hr>
    <p><b>Jurnal:</b> <?= esc((string) ($loaRequest['journal_name'] ?? '-')) ?></p>
    <p><b>Judul:</b> <?= esc((string) ($loaRequest['title'] ?? '-')) ?></p>
    <p><b>Email:</b> <?= esc((string) ($loaRequest['corresponding_email'] ?? '-')) ?></p>
    <p><b>Status:</b> <?= esc(ucfirst((string) ($loaRequest['status'] ?? '-'))) ?></p>
    <p><b>Tanggal Pengajuan:</b> <?= esc((string) ($loaRequest['created_at'] ?? '-')) ?></p>
</div>
<?= $this->endSection() ?>
