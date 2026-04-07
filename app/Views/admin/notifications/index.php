<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Daftar Notifikasi</h6>
    <div class="d-flex gap-2 align-items-center">
      <form method="get" class="d-flex align-items-center gap-2">
        <label class="small text-muted mb-0">Per halaman</label>
        <select name="perPage" class="form-select form-select-sm" onchange="this.form.submit()">
          <?php foreach ([10, 25, 50] as $opt): ?>
            <option value="<?= $opt ?>" <?= ((int) ($perPage ?? 10) === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a class="btn btn-sm btn-primary" href="<?= site_url('admin/notifikasi/create') ?>">Tambah Notifikasi</a>
    </div>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive">
      <table class="table table-hover align-middle mb-0 w-100">
        <thead><tr><th>NO</th><th>NOMOR LOA</th><th>JUDUL</th><th>STATUS</th><th>TERKIRIM KE</th><th>SENT AT</th><th>AKSI</th></tr></thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
            <td class="fw-semibold text-primary"><?= esc((string) ($r['loa_number'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['title'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['status'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['sent_to_email'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['sent_at'] ?? '-')) ?></td>
            <td>
              <div class="d-flex gap-1">
                <form method="post" action="<?= site_url('admin/notifikasi/' . (string) $r['id'] . '/kirim-email') ?>"><button class="btn btn-sm activity-btn user-action-btn user-action-detail" type="submit">Kirim</button></form>
                <a class="btn btn-sm activity-btn user-action-btn user-action-edit" href="<?= site_url('admin/notifikasi/' . (string) $r['id'] . '/edit') ?>">Edit</a>
                <form method="post" action="<?= site_url('admin/notifikasi/' . (string) $r['id']) ?>" onsubmit="return confirm('Hapus notifikasi ini?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <button class="btn btn-sm activity-btn user-action-btn user-action-delete" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-center text-muted">Belum ada notifikasi.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (isset($pager)): ?>
    <div class="table-pagination-footer">
      <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
      <?= $pager->links('default', 'table_plpi') ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>


