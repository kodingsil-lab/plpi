<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-3"><?= esc($row ? 'Edit Publisher' : 'Tambah Publisher') ?></h6>
    <form method="post" enctype="multipart/form-data" action="<?= $row ? site_url('admin/publishers/' . (int) $row['id']) : site_url('admin/publishers') ?>">
      <?php if ($row): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Kode</label><input class="form-control" name="code" value="<?= esc((string) ($row['code'] ?? old('code'))) ?>" required></div>
        <div class="col-md-8"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc((string) ($row['name'] ?? old('name'))) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" type="email" value="<?= esc((string) ($row['email'] ?? old('email'))) ?>"></div>
        <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?= esc((string) ($row['phone'] ?? old('phone'))) ?>"></div>
        <div class="col-md-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address" rows="3"><?= esc((string) ($row['address'] ?? old('address'))) ?></textarea></div>
        <div class="col-md-6">
          <label class="form-label">Logo Publisher</label>
          <input class="form-control" type="file" name="logo" accept="image/png,image/jpeg,image/webp">
          <?php if (! empty($row['logo_path'])): ?>
            <small class="text-muted d-block mt-1">Path saat ini: <?= esc((string) $row['logo_path']) ?></small>
          <?php endif; ?>
        </div>
      </div>
      <div class="d-flex gap-2 mt-3 myletters-actions">
        <button class="btn activity-btn user-action-btn user-action-edit" type="submit">Simpan</button>
        <a class="btn activity-btn user-action-btn user-action-detail" href="<?= site_url('admin/publishers') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
