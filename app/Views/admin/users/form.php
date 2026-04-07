<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-3"><?= esc($row ? 'Edit Pengguna' : 'Tambah Pengguna') ?></h6>
    <form method="post" action="<?= $row ? site_url('admin/users/' . (int) $row['id']) : site_url('admin/users') ?>">
      <?php if ($row): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Username</label><input class="form-control" name="username" value="<?= esc((string) ($row['username'] ?? old('username'))) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc((string) ($row['name'] ?? old('name'))) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= esc((string) ($row['email'] ?? old('email'))) ?>" required></div>
        <div class="col-md-4"><label class="form-label">Role</label>
          <select name="role" class="form-select">
            <option value="superadmin" <?= (($row['role'] ?? old('role')) === 'superadmin') ? 'selected' : '' ?>>superadmin</option>
            <option value="admin_jurnal" <?= (($row['role'] ?? old('role')) === 'admin_jurnal') ? 'selected' : '' ?>>admin_jurnal</option>
          </select>
        </div>
        <div class="col-md-4"><label class="form-label">Aktif</label>
          <select name="is_active" class="form-select">
            <option value="1" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '1') ? 'selected' : '' ?>>Ya</option>
            <option value="0" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '0') ? 'selected' : '' ?>>Tidak</option>
          </select>
        </div>
        <?php if (! $row): ?>
        <div class="col-md-4"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
        <?php endif; ?>
      </div>
      <div class="d-flex gap-2 mt-3 myletters-actions">
        <button class="btn activity-btn user-action-btn user-action-edit" type="submit">Simpan</button>
        <a class="btn activity-btn user-action-btn user-action-detail" href="<?= site_url('admin/users') ?>">Kembali</a>
      </div>
    </form>

    <?php if ($row): ?>
    <hr>
    <form method="post" action="<?= site_url('admin/users/' . (string) $row['id'] . '/password') ?>" class="row g-2 align-items-end">
      <div class="col-md-4"><label class="form-label">Password Baru</label><input class="form-control" type="password" name="password" required></div>
      <div class="col-md-2"><button class="btn activity-btn user-action-btn user-action-edit" type="submit">Update Password</button></div>
    </form>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>
