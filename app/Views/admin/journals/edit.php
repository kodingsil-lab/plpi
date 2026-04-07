<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-3">Edit Jurnal: <?= esc((string) ($row['name'] ?? '-')) ?></h6>
    <form method="post" action="<?= site_url('admin/journals/' . (int) ($row['id'] ?? 0)) ?>" enctype="multipart/form-data">
      <input type="hidden" name="_method" value="PUT">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Publisher</label>
          <select name="publisher_id" class="form-select" required>
            <?php foreach (($publishers ?? []) as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= ((int) old('publisher_id', (string) ($row['publisher_id'] ?? 0)) === (int) $p['id']) ? 'selected' : '' ?>>
                <?= esc((string) $p['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Nama Jurnal</label>
          <input class="form-control" name="name" value="<?= esc((string) old('name', (string) ($row['name'] ?? ''))) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Kode</label>
          <input class="form-control" name="code" value="<?= esc((string) old('code', (string) ($row['code'] ?? ''))) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Slug</label>
          <input class="form-control" name="slug" value="<?= esc((string) old('slug', (string) ($row['slug'] ?? ''))) ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">ISSN</label>
          <input class="form-control" name="issn" value="<?= esc((string) old('issn', (string) ($row['issn'] ?? ''))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">E-ISSN</label>
          <input class="form-control" name="e_issn" value="<?= esc((string) old('e_issn', (string) ($row['e_issn'] ?? ''))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">P-ISSN</label>
          <input class="form-control" name="p_issn" value="<?= esc((string) old('p_issn', (string) ($row['p_issn'] ?? ''))) ?>">
        </div>

        <div class="col-md-8">
          <label class="form-label">Website URL</label>
          <input class="form-control" name="website_url" value="<?= esc((string) old('website_url', (string) ($row['website_url'] ?? ''))) ?>" placeholder="https://...">
        </div>

        <div class="col-md-6">
          <label class="form-label">Nama Penandatangan Default</label>
          <input class="form-control" name="default_signer_name" value="<?= esc((string) old('default_signer_name', (string) ($row['default_signer_name'] ?? ''))) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Jabatan Penandatangan</label>
          <input class="form-control" name="default_signer_title" value="<?= esc((string) old('default_signer_title', (string) ($row['default_signer_title'] ?? ''))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Posisi TTD Kiri/Kanan (px)</label>
          <input class="form-control" type="number" name="pdf_sig_left_px" value="<?= esc((string) old('pdf_sig_left_px', (string) ($row['pdf_sig_left_px'] ?? ''))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Posisi TTD Atas/Bawah (px)</label>
          <input class="form-control" type="number" name="pdf_sig_top_px" value="<?= esc((string) old('pdf_sig_top_px', (string) ($row['pdf_sig_top_px'] ?? ''))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Tinggi TTD (px)</label>
          <input class="form-control" type="number" name="pdf_sig_height_px" value="<?= esc((string) old('pdf_sig_height_px', (string) ($row['pdf_sig_height_px'] ?? ''))) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Logo Jurnal (opsional)</label>
          <input class="form-control" type="file" name="logo" accept="image/png,image/jpeg,image/webp">
          <?php if (! empty($row['logo_path'])): ?>
            <small class="text-muted d-block mt-1">Path saat ini: <?= esc((string) $row['logo_path']) ?></small>
          <?php endif; ?>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tanda Tangan Default (opsional)</label>
          <input class="form-control" type="file" name="signature" accept="image/png,image/jpeg,image/webp">
          <?php if (! empty($row['default_signature_path'])): ?>
            <small class="text-muted d-block mt-1">Path saat ini: <?= esc((string) $row['default_signature_path']) ?></small>
          <?php endif; ?>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3 myletters-actions">
        <button class="btn activity-btn user-action-btn user-action-edit" type="submit">Simpan Perubahan</button>
        <a class="btn activity-btn user-action-btn user-action-detail" href="<?= site_url('admin/journals') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
