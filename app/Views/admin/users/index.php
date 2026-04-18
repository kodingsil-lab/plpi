<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Pengguna</h6>
    <div class="d-flex gap-2 align-items-center">
      <a class="btn btn-primary-main" href="<?= site_url('admin/users/create') ?>">Tambah Pengguna</a>
    </div>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive">
      <table class="table table-hover align-middle mb-0 w-100 users-table-full">
        <thead><tr><th>USERNAME</th><th>NAMA ADMIN</th><th>EMAIL</th><th>ROLE</th><th>JURNAL YANG DITUGASKAN</th><th>AKSI</th></tr></thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <?php
            $roleRaw = (string) ($r['role'] ?? '-');
            $roleLabel = $roleRaw === 'admin_jurnal' ? 'adminjurnal' : $roleRaw;
            $assignedJournal = '-';
            if ($roleRaw === 'admin_jurnal') {
              $assignedCount = (int) ($r['assigned_journal_count'] ?? 0);
              $assignedJournal = $assignedCount . ' Jurnal';
            }
          ?>
          <tr>
            <td><?= esc((string) ($r['username'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['email'] ?? '-')) ?></td>
            <td><?= esc($roleLabel) ?></td>
            <td><?= esc($assignedJournal) ?></td>
            <td>
              <div class="myletters-actions">
                <a class="btn btn-sm activity-btn user-action-btn user-action-edit action-solid action-solid-edit myletters-icon-only" href="<?= site_url('admin/users/' . (string) $r['id'] . '/edit') ?>" aria-label="Edit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                  <iconify-icon icon="heroicons-outline:pencil" aria-hidden="true"></iconify-icon>
                </a>
                <form method="post" action="<?= site_url('admin/users/' . (string) $r['id']) ?>" onsubmit="return confirm('Hapus pengguna ini?')">
                  <input type="hidden" name="_method" value="DELETE">
                  <button class="btn btn-sm activity-btn user-action-btn user-action-delete action-solid action-solid-delete myletters-icon-only" type="submit" aria-label="Hapus" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hapus">
                    <iconify-icon icon="solar:trash-bin-trash-outline" aria-hidden="true"></iconify-icon>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="text-center text-muted">Belum ada data pengguna.</td></tr>
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


