<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card admin-user-form-page">
  <div class="card-body">
    <h6 class="mb-1"><?= esc($row ? 'Edit Pengguna' : 'Tambah Pengguna') ?></h6>
    <p class="text-muted mb-4">Kelola data akun pengguna dan hak akses sistem.</p>

    <form method="post" action="<?= $row ? site_url('admin/users/' . (int) $row['id']) : site_url('admin/users') ?>">
      <?php if ($row): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input class="form-control" name="username" value="<?= esc((string) ($row['username'] ?? old('username'))) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nama</label>
          <input class="form-control" name="name" value="<?= esc((string) ($row['name'] ?? old('name'))) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" value="<?= esc((string) ($row['email'] ?? old('email'))) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Role</label>
          <select name="role" class="form-select">
            <option value="superadmin" <?= (($row['role'] ?? old('role')) === 'superadmin') ? 'selected' : '' ?>>superadmin</option>
            <option value="admin_jurnal" <?= (($row['role'] ?? old('role')) === 'admin_jurnal') ? 'selected' : '' ?>>admin_jurnal</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Aktif</label>
          <select name="is_active" class="form-select">
            <option value="1" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '1') ? 'selected' : '' ?>>Ya</option>
            <option value="0" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '0') ? 'selected' : '' ?>>Tidak</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label"><?= $row ? 'Password Baru (opsional)' : 'Password' ?></label>
          <input class="form-control" type="password" name="password" <?= $row ? '' : 'required' ?>>
          <?php if ($row): ?>
            <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengubah password.</small>
          <?php endif; ?>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4 myletters-actions user-form-actions justify-content-end">
        <button class="btn btn-primary-main user-form-btn-flat" type="submit">Simpan</button>
        <a class="btn btn-light-soft user-form-btn-flat" href="<?= site_url('admin/users') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
