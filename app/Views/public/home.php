<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h2 style="margin-top:0">PUSAT LAYANAN PUBLIKASI ILMIAH (PLPI)</h2>
    <p>Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal</p>
    <p>
        <a class="btn" href="<?= site_url('loa/request') ?>">Ajukan LoA</a>
        <a class="btn2" href="<?= site_url('loa/verify') ?>">Verifikasi LoA</a>
        <a class="btn2" href="<?= site_url('login') ?>">Login Admin</a>
    </p>
</div>
<?= $this->endSection() ?>
