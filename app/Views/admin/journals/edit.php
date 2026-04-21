<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$isEdit = ! empty($row['id']);
$formAction = $isEdit
  ? site_url('admin/journals/' . (int) ($row['id'] ?? 0))
  : site_url('admin/journals');
$heading = $isEdit ? 'Edit Jurnal' : 'Tambah Jurnal';
$description = $isEdit
  ? 'Kelola identitas jurnal, penandatangan, dan konfigurasi berkas pendukung.'
  : 'Lengkapi identitas jurnal baru, penandatangan, dan konfigurasi berkas pendukung.';
$pdfDefaults = is_array($pdfDefaults ?? null) ? $pdfDefaults : ['left' => 20, 'top' => 10, 'height' => 85];

$logoPreview = null;
if (! empty($row['logo_path'])) {
  $logoFullPath = WRITEPATH . 'uploads/' . ltrim((string) $row['logo_path'], '/\\');
  if (is_file($logoFullPath) && is_readable($logoFullPath)) {
    $logoMime = @mime_content_type($logoFullPath) ?: 'image/png';
    $logoData = @file_get_contents($logoFullPath);
    if ($logoData !== false) {
      $logoPreview = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
    }
  }
}

$signaturePreview = null;
if (! empty($row['default_signature_path'])) {
  $signatureFullPath = WRITEPATH . 'uploads/' . ltrim((string) $row['default_signature_path'], '/\\');
  if (is_file($signatureFullPath) && is_readable($signatureFullPath)) {
    $signatureMime = @mime_content_type($signatureFullPath) ?: 'image/png';
    $signatureData = @file_get_contents($signatureFullPath);
    if ($signatureData !== false) {
      $signaturePreview = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
    }
  }
}
?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-1"><?= esc($heading) ?></h6>
    <p class="text-muted mb-4"><?= esc($description) ?></p>
    <form method="post" action="<?= $formAction ?>" enctype="multipart/form-data">
      <?php if ($isEdit): ?>
      <input type="hidden" name="_method" value="PUT">
      <?php endif; ?>

      <h6 class="mb-3">Identitas Jurnal</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Publisher</label>
          <select name="publisher_id" class="form-select" required>
            <?php foreach (($publishers ?? []) as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= ((int) old('publisher_id', (string) ($row['publisher_id'] ?? 0)) === (int) $p['id']) ? 'selected' : '' ?>>
                <?= esc((string) $p['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Nama Jurnal</label>
          <input class="form-control" name="name" value="<?= esc((string) old('name', (string) ($row['name'] ?? ''))) ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">E-ISSN</label>
          <input class="form-control" name="e_issn" value="<?= esc((string) old('e_issn', (string) ($row['e_issn'] ?? ''))) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">P-ISSN</label>
          <input class="form-control" name="p_issn" value="<?= esc((string) old('p_issn', (string) ($row['p_issn'] ?? ''))) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Kode Jurnal</label>
          <input class="form-control" name="code" value="<?= esc((string) old('code', (string) ($row['code'] ?? ''))) ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">URL Website Jurnal</label>
          <input class="form-control" name="website_url" value="<?= esc((string) old('website_url', (string) ($row['website_url'] ?? ''))) ?>" placeholder="https://...">
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Identitas Pimpinan Redaksi</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Pimpinan Redaksi</label>
          <input class="form-control" name="default_signer_name" value="<?= esc((string) old('default_signer_name', (string) ($row['default_signer_name'] ?? ''))) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Jabatan</label>
          <input class="form-control" name="default_signer_title" value="<?= esc((string) old('default_signer_title', (string) ($row['default_signer_title'] ?? ''))) ?>">
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Pengaturan PDF</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Posisi TTD Kiri/Kanan (px)</label>
          <input class="form-control" type="number" name="pdf_sig_left_px" value="<?= esc((string) old('pdf_sig_left_px', (string) ($row['pdf_sig_left_px'] ?? $pdfDefaults['left']))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Posisi TTD Atas/Bawah (px)</label>
          <input class="form-control" type="number" name="pdf_sig_top_px" value="<?= esc((string) old('pdf_sig_top_px', (string) ($row['pdf_sig_top_px'] ?? $pdfDefaults['top']))) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Tinggi TTD (px)</label>
          <input class="form-control" type="number" name="pdf_sig_height_px" value="<?= esc((string) old('pdf_sig_height_px', (string) ($row['pdf_sig_height_px'] ?? $pdfDefaults['height']))) ?>">
        </div>
      </div>

      <hr class="my-4">
      <h6 class="mb-3">Berkas Jurnal</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Logo Jurnal</label>
          <input class="form-control" type="file" name="logo" id="logoFileInput" accept="image/png,image/jpeg,image/webp">
          <div class="mt-2">
            <img
              id="logoPreviewImage"
              src="<?= esc((string) ($logoPreview ?? '')) ?>"
              alt="Preview Logo Jurnal"
              style="max-height:80px; width:auto; border:1px solid #dbe3ee; border-radius:8px; padding:4px; <?= $logoPreview ? '' : 'display:none;' ?>"
            >
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Cap + Tanda Tangan Digital</label>
          <input class="form-control" type="file" name="signature" id="signatureFileInput" accept="image/png,image/jpeg,image/webp">
          <div class="mt-2">
            <img
              id="signaturePreviewImage"
              src="<?= esc((string) ($signaturePreview ?? '')) ?>"
              alt="Preview Cap dan Tanda Tangan Digital"
              style="max-height:80px; width:auto; border:1px solid #dbe3ee; border-radius:8px; padding:4px; <?= $signaturePreview ? '' : 'display:none;' ?>"
            >
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4 myletters-actions user-form-actions justify-content-end">
        <button class="btn btn-primary-main user-form-btn-flat" type="submit">Simpan</button>
        <a class="btn btn-light-soft user-form-btn-flat" href="<?= site_url('admin/journals') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  function bindPreview(inputId, imageId) {
    var input = document.getElementById(inputId);
    var image = document.getElementById(imageId);
    if (!input || !image) return;

    input.addEventListener('change', function () {
      var file = input.files && input.files[0] ? input.files[0] : null;
      if (!file) return;
      if (!file.type || file.type.indexOf('image/') !== 0) return;

      var reader = new FileReader();
      reader.onload = function (e) {
        image.src = e.target && e.target.result ? e.target.result : '';
        image.style.display = image.src ? 'inline-block' : 'none';
      };
      reader.readAsDataURL(file);
    });
  }

  bindPreview('logoFileInput', 'logoPreviewImage');
  bindPreview('signatureFileInput', 'signaturePreviewImage');
});
</script>
<?= $this->endSection() ?>
