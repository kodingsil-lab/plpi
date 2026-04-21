<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
    helper('app_settings');
    $row = is_array($row ?? null) ? $row : [];
    $timezoneOptions = is_array($timezoneOptions ?? null) ? $timezoneOptions : [];
    $currentTimezone = (string) ($row['app_timezone'] ?? 'Asia/Jakarta');
    $appLogoPath = (string) ($row['header_logo_path'] ?? $row['login_logo_path'] ?? $row['public_logo_path'] ?? '');
    $appLogoUrl = $appLogoPath !== '' ? plpi_asset_url($appLogoPath) : '';
    $faviconUrl = ! empty($row['favicon_path']) ? plpi_asset_url((string) $row['favicon_path']) : '';
?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-1">Pengaturan Aplikasi</h6>
    <p class="text-muted mb-4">Kelola identitas visual aplikasi dan zona waktu yang dipakai sistem.</p>
    <form method="post" action="<?= site_url('superadmin/settings/application') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="row g-4">
        <div class="col-lg-6">
          <label class="form-label">Logo Aplikasi</label>
          <input class="form-control" type="file" name="app_logo" id="appLogoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml">
          <div class="form-text">Satu logo ini akan dipakai untuk header admin, halaman login, dan halaman beranda publik.</div>
          <img
            id="appLogoPreview"
            src="<?= esc($appLogoUrl) ?>"
            alt="Preview Logo Aplikasi"
            style="max-height:72px;width:auto;margin-top:12px;border:1px solid #dbe3ee;border-radius:8px;padding:6px;background:#fff;<?= $appLogoUrl !== '' ? '' : 'display:none;' ?>"
          >
        </div>
        <div class="col-lg-6">
          <label class="form-label">Favicon</label>
          <input class="form-control" type="file" name="favicon" id="faviconInput" accept=".ico,image/png,image/webp,image/svg+xml">
          <div class="form-text">Digunakan pada tab browser dan bookmark aplikasi.</div>
          <img
            id="faviconPreview"
            src="<?= esc($faviconUrl) ?>"
            alt="Preview Favicon"
            style="max-height:48px;width:auto;margin-top:12px;border:1px solid #dbe3ee;border-radius:8px;padding:6px;background:#fff;<?= $faviconUrl !== '' ? '' : 'display:none;' ?>"
          >
        </div>
        <div class="col-lg-6">
          <label class="form-label">Zona Waktu</label>
          <select class="form-select" name="app_timezone" required>
            <?php foreach ($timezoneOptions as $value => $label): ?>
              <option value="<?= esc($value) ?>" <?= $currentTimezone === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4 myletters-actions user-form-actions justify-content-end">
        <button class="btn btn-primary-main user-form-btn-flat" type="submit">Simpan</button>
        <a class="btn btn-light-soft user-form-btn-flat" href="<?= site_url('dashboard') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  function bindPreview(inputId, imageId) {
    const input = document.getElementById(inputId);
    const image = document.getElementById(imageId);
    if (!input || !image) {
      return;
    }

    input.addEventListener('change', function (event) {
      const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
      if (!file) {
        return;
      }

      if (file.type && !file.type.startsWith('image/')) {
        return;
      }

      const objectUrl = URL.createObjectURL(file);
      image.src = objectUrl;
      image.style.display = 'block';
    });
  }

  bindPreview('appLogoInput', 'appLogoPreview');
  bindPreview('faviconInput', 'faviconPreview');
});
</script>
<?= $this->endSection() ?>
