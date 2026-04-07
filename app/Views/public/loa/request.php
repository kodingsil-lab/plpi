<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card">
    <h3 style="margin-top:0">Ajukan LoA</h3>
    <p>Isi data artikel untuk permohonan Letter of Acceptance.</p>
    <form method="post" action="<?= site_url('loa/request') ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
                <label>Jurnal</label>
                <select name="journal_id" class="input">
                    <option value="">Pilih Jurnal</option>
                    <?php foreach (($journals ?? []) as $j): ?>
                        <option value="<?= esc((string) $j['id']) ?>" <?= old('journal_id') == $j['id'] ? 'selected' : '' ?>><?= esc((string) $j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Email Korespondensi</label>
                <input type="email" name="corresponding_email" value="<?= esc((string) old('corresponding_email')) ?>" class="input" required>
            </div>
            <div style="grid-column:1/-1">
                <label>Judul Artikel</label>
                <input type="text" name="title" value="<?= esc((string) old('title')) ?>" class="input" required>
            </div>
            <div style="grid-column:1/-1">
                <label>URL Artikel</label>
                <input type="url" name="article_url" value="<?= esc((string) old('article_url')) ?>" class="input" placeholder="https://...">
            </div>
            <div>
                <label>Volume</label>
                <input type="text" name="volume" value="<?= esc((string) old('volume')) ?>" class="input">
            </div>
            <div>
                <label>Nomor</label>
                <input type="text" name="issue_number" value="<?= esc((string) old('issue_number')) ?>" class="input">
            </div>
            <div>
                <label>Tahun</label>
                <input type="text" name="published_year" value="<?= esc((string) old('published_year')) ?>" class="input" placeholder="2026">
            </div>
            <div style="grid-column:1/-1">
                <label>Nama Penulis (1 baris 1 nama)</label>
                <textarea name="authors_text" class="input" rows="5" required><?= esc((string) old('authors_text')) ?></textarea>
            </div>
            <div style="grid-column:1/-1">
                <label>Afiliasi (opsional, 1 baris 1 data)</label>
                <textarea name="affiliations_text" class="input" rows="4"><?= esc((string) old('affiliations_text')) ?></textarea>
            </div>
        </div>
        <div style="margin-top:14px;display:flex;gap:8px">
            <button class="btn" type="submit">Kirim Permohonan</button>
            <a class="btn2" href="<?= site_url('/') ?>">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
