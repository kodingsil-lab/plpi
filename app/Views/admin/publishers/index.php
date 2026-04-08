<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Publisher</h6>
    <div class="d-flex gap-2 align-items-center">
      <a class="btn btn-primary-main" href="<?= site_url('admin/publishers/create') ?>">Tambah Publisher</a>
    </div>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive">
      <table class="table table-hover align-middle mb-0 w-100">
        <thead><tr><th>NO</th><th>KODE</th><th>NAMA PUBLISHER</th><th>EMAIL</th><th>NOMOR WHATSAPP</th><th style="width:22%;">ALAMAT</th><th>AKSI</th></tr></thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
            <td><?= esc((string) ($r['code'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['email'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['phone'] ?? '-')) ?></td>
            <td style="width:22%; word-break:break-word;"><?= esc((string) ($r['address'] ?? '-')) ?></td>
            <td>
              <div class="d-flex gap-1 myletters-actions">
                <a class="btn btn-sm activity-btn user-action-btn user-action-edit" href="<?= site_url('admin/publishers/' . (string) $r['id'] . '/edit') ?>">Edit</a>
                <form method="post" action="<?= site_url('admin/publishers/' . (string) $r['id']) ?>" onsubmit="return confirm('Hapus publisher ini?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <button class="btn btn-sm activity-btn user-action-btn user-action-delete" type="submit">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-center text-muted">Belum ada data publisher.</td></tr>
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


