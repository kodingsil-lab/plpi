<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h3 style="margin-top:0">Verifikasi LoA</h3>
    <p>Masukkan nomor LoA untuk memeriksa keaslian dokumen.</p>
    <form method="post" action="<?= site_url('loa/verify') ?>">
        <label>Nomor LoA</label>
        <input class="input" type="text" name="number" value="<?= esc((string) old('number')) ?>" placeholder="Contoh: 001/LOA/JRN-1/04/2026" required>
        <div style="margin-top:12px;display:flex;gap:8px">
            <button class="btn" type="submit">Verifikasi</button>
            <a class="btn2" href="<?= site_url('/') ?>">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
