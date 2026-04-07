<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<style>
    .verify-shell {
        background: #fff;
        border: 1px solid #d9e4f2;
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(18, 41, 77, 0.08);
        padding: 18px;
    }

    .verify-header {
        border-bottom: 1px solid #e5edf8;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .verify-header h3 {
        margin: 0;
        color: #163b73;
        font-size: clamp(1.35rem, 2.1vw, 1.8rem);
        font-weight: 800;
    }

    .verify-header p {
        margin: 8px 0 0;
        color: #5f7493;
        font-size: .95rem;
    }

    .verify-label {
        color: #1c3762;
        font-size: .95rem;
        font-weight: 700;
        margin-bottom: 6px;
        display: block;
    }

    .verify-input {
        width: 100%;
        border: 1px solid #c7d8ee;
        border-radius: 11px;
        background: #fbfdff;
        color: #1f3658;
        padding: 10px 12px;
        font-size: .95rem;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .verify-input:focus {
        border-color: #4f79cb;
        box-shadow: 0 0 0 .2rem rgba(43, 89, 181, .15);
        background: #fff;
        outline: none;
    }

    .verify-actions {
        margin-top: 12px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .verify-btn-main,
    .verify-btn-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        text-decoration: none;
        font-size: .92rem;
        font-weight: 700;
        padding: 10px 14px;
        line-height: 1;
        transition: .2s ease;
        cursor: pointer;
    }

    .verify-btn-main {
        border: 1px solid #2b59b5;
        background: linear-gradient(135deg, #2b59b5 0%, #3f70cc 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(43, 89, 181, .22);
    }

    .verify-btn-main:hover {
        border-color: #163b73;
        background: #163b73;
        color: #fff;
    }

    .verify-btn-soft {
        border: 1px solid #c5d7ef;
        background: #fff;
        color: #224a92;
    }

    .verify-btn-soft:hover {
        border-color: #2b59b5;
        color: #163b73;
    }
</style>

<div class="verify-shell">
    <div class="verify-header">
        <h3>Verifikasi LoA</h3>
        <p>Masukkan nomor LoA untuk memeriksa keaslian dokumen.</p>
    </div>
    <form method="post" action="<?= site_url('loa/verify') ?>">
        <label class="verify-label">Nomor LoA</label>
        <input class="verify-input" type="text" name="number" value="<?= esc((string) old('number')) ?>" placeholder="Contoh: 001/LOA/JRN-1/04/2026" required>
        <div class="verify-actions">
            <button class="verify-btn-main" type="submit">Verifikasi</button>
            <a class="verify-btn-soft" href="<?= site_url('/') ?>">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
