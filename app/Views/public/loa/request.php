<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<style>
    .loa-shell {
        background: #fff;
        border: 1px solid #d9e4f2;
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(18, 41, 77, 0.08);
        padding: 18px;
    }

    .loa-header {
        border-bottom: 1px solid #e5edf8;
        padding-bottom: 12px;
        margin-bottom: 16px;
        text-align: center;
    }

    .loa-header h3 {
        margin: 0;
        color: #163b73;
        font-size: clamp(1.35rem, 2.1vw, 1.8rem);
        font-weight: 800;
    }

    .loa-header p {
        margin: 8px 0 0;
        color: #5f7493;
        font-size: .95rem;
    }

    .loa-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .loa-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .loa-field.full {
        grid-column: 1 / -1;
    }

    .loa-label {
        color: #1c3762;
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
    }

    .loa-input {
        width: 100%;
        border: 1px solid #c7d8ee;
        border-radius: 11px;
        background: #fbfdff;
        color: #1f3658;
        padding: 10px 12px;
        font-size: .95rem;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .loa-input:focus {
        border-color: #4f79cb;
        box-shadow: 0 0 0 .2rem rgba(43, 89, 181, .15);
        background: #fff;
        outline: none;
    }

    .loa-actions {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed #d6e4f5;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .loa-btn-main,
    .loa-btn-soft {
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

    .loa-btn-main {
        border: 1px solid #2b59b5;
        background: linear-gradient(135deg, #2b59b5 0%, #3f70cc 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(43, 89, 181, .22);
    }

    .loa-btn-main:hover {
        border-color: #163b73;
        background: #163b73;
        color: #fff;
    }

    .loa-btn-soft {
        border: 1px solid #c5d7ef;
        background: #fff;
        color: #224a92;
    }

    .loa-btn-soft:hover {
        border-color: #2b59b5;
        color: #163b73;
    }

    @media (max-width: 768px) {
        .loa-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="loa-shell">
    <div class="loa-header">
        <h3>Ajuka Letter of Acceptance</h3>
        <p>Lengkapi data artikel untuk proses pengajuan Letter of Acceptance (LoA)</p>
    </div>

    <form method="post" action="<?= site_url('loa/request') ?>">
        <div class="loa-grid">
            <div class="loa-field">
                <label class="loa-label">Jurnal</label>
                <select name="journal_id" class="loa-input">
                    <option value="">Pilih Jurnal</option>
                    <?php foreach (($journals ?? []) as $j): ?>
                        <option value="<?= esc((string) $j['id']) ?>" <?= old('journal_id') == $j['id'] ? 'selected' : '' ?>><?= esc((string) $j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="loa-field">
                <label class="loa-label">Email Korespondensi</label>
                <input type="email" name="corresponding_email" value="<?= esc((string) old('corresponding_email')) ?>" class="loa-input" placeholder="contoh@email.com" required>
            </div>

            <div class="loa-field full">
                <label class="loa-label">Judul Artikel</label>
                <input type="text" name="title" value="<?= esc((string) old('title')) ?>" class="loa-input" placeholder="Masukkan judul artikel" required>
            </div>

            <div class="loa-field">
                <label class="loa-label">Volume</label>
                <input type="text" name="volume" value="<?= esc((string) old('volume')) ?>" class="loa-input" placeholder="Contoh: 12">
            </div>

            <div class="loa-field">
                <label class="loa-label">Nomor</label>
                <input type="text" name="issue_number" value="<?= esc((string) old('issue_number')) ?>" class="loa-input" placeholder="Contoh: 2">
            </div>

            <div class="loa-field">
                <label class="loa-label">URL Artikel (Opsional)</label>
                <input type="url" name="article_url" value="<?= esc((string) old('article_url')) ?>" class="loa-input" placeholder="https://...">
            </div>

            <div class="loa-field">
                <label class="loa-label">Tahun</label>
                <input type="text" name="published_year" value="<?= esc((string) old('published_year')) ?>" class="loa-input" placeholder="2026">
            </div>

            <div class="loa-field full">
                <label class="loa-label">Nama Penulis (1 baris 1 nama)</label>
                <textarea name="authors_text" class="loa-input" rows="5" placeholder="Nama Penulis 1&#10;Nama Penulis 2&#10;Nama Penulis 3" required><?= esc((string) old('authors_text')) ?></textarea>
            </div>

            <div class="loa-field full">
                <label class="loa-label">Afiliasi (1 baris 1 data)</label>
                <textarea name="affiliations_text" class="loa-input" rows="4" placeholder="Universitas A&#10;Fakultas B&#10;Program Studi C"><?= esc((string) old('affiliations_text')) ?></textarea>
            </div>
        </div>

        <div class="loa-actions">
            <button class="loa-btn-main" type="submit">Kirim Permohonan</button>
            <a class="loa-btn-soft" href="<?= site_url('/') ?>">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
