<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-1"><?= esc($row ? 'Edit Publisher' : 'Tambah Publisher') ?></h6>
    <p class="text-muted mb-4">Kelola identitas publisher untuk kebutuhan jurnal dan penerbitan LoA.</p>
    <form method="post" enctype="multipart/form-data" action="<?= $row ? site_url('admin/publishers/' . (int) $row['id']) : site_url('admin/publishers') ?>">
      <?php if ($row): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nama Publisher</label><input class="form-control" name="name" value="<?= esc((string) ($row['name'] ?? old('name'))) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Kode</label><input class="form-control" name="code" value="<?= esc((string) ($row['code'] ?? old('code'))) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" type="email" value="<?= esc((string) ($row['email'] ?? old('email'))) ?>"></div>
        <div class="col-md-6"><label class="form-label">Nomor Whatsapp</label><input class="form-control" name="phone" value="<?= esc((string) ($row['phone'] ?? old('phone'))) ?>"></div>
        <div class="col-md-6"><label class="form-label">Alamat</label><textarea class="form-control" name="address" rows="5"><?= esc((string) ($row['address'] ?? old('address'))) ?></textarea></div>
        <div class="col-md-6">
          <label class="form-label">Logo Publisher</label>
          <input class="form-control" type="file" name="logo" id="logoFileInput" accept="image/png,image/jpeg,image/webp">
          <img id="logoPreviewImage" src="#" alt="Preview logo publisher" style="display:none;max-height:72px;width:auto;margin-top:10px;border:1px solid #dbe3ee;border-radius:8px;padding:4px;background:#fff;">
          <?php if (! empty($row['logo_path'])): ?>
            <small class="text-muted d-block mt-1">Path saat ini: <?= esc((string) $row['logo_path']) ?></small>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4 myletters-actions user-form-actions justify-content-end">
        <button class="btn btn-primary-main user-form-btn-flat" type="submit">Simpan</button>
        <a class="btn btn-light-soft user-form-btn-flat" href="<?= site_url('admin/publishers') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const fileInput = document.getElementById('logoFileInput');
  const previewImage = document.getElementById('logoPreviewImage');
  if (!fileInput || !previewImage) {
    return;
  }

  fileInput.addEventListener('change', function (event) {
    const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
    if (!file) {
      previewImage.style.display = 'none';
      previewImage.removeAttribute('src');
      return;
    }

    if (!file.type.startsWith('image/')) {
      previewImage.style.display = 'none';
      previewImage.removeAttribute('src');
      return;
    }

    const objectUrl = URL.createObjectURL(file);
    previewImage.src = objectUrl;
    previewImage.style.display = 'block';
  });
});
</script>
<?= $this->endSection() ?>
