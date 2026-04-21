<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Jurnal</h6>
    <?php $isSuperadmin = ((string) session('role') === 'superadmin'); ?>
    <div class="d-flex gap-2 align-items-center">
      <?php if ($isSuperadmin): ?>
      <form
        id="bulk-delete-journals"
        class="bulk-delete-form m-0"
        method="post"
        action="<?= site_url('admin/journals/bulk-delete') ?>"
        data-bulk-target="#journalsTableScope"
        data-confirm="Hapus jurnal yang dipilih?"
      >
        <div class="bulk-hidden-inputs"></div>
        <div class="bulk-actions-bar">
          <span class="bulk-selection-count">Belum ada yang dipilih</span>
          <button type="submit" class="btn btn-danger bulk-delete-trigger" disabled>Hapus Massal</button>
        </div>
      </form>
      <?php endif; ?>
      <?php if ($isSuperadmin): ?>
      <a class="btn btn-primary-main" href="<?= site_url('admin/journals/create') ?>">Tambah Jurnal</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive" id="journalsTableScope">
      <table class="table table-hover align-middle mb-0 w-100 <?= $isSuperadmin ? 'table-layout-journal-bulk' : 'table-layout-journal' ?>">
        <thead>
        <tr>
          <?php if ($isSuperadmin): ?><th class="bulk-check-col"><input type="checkbox" class="bulk-check-input bulk-select-all" aria-label="Pilih semua"></th><?php endif; ?>
          <th>NO</th>
          <th>KODE</th>
          <th>NAMA JURNAL</th>
          <th>PUBLISHER</th>
          <th>E-ISSN</th>
          <th>P-ISSN</th>
          <th>AKSI</th>
        </tr>
        </thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <tr>
            <?php if ($isSuperadmin): ?><td class="bulk-check-col"><input type="checkbox" class="bulk-check-input bulk-row-check" value="<?= esc((string) $r['id']) ?>" aria-label="Pilih jurnal"></td><?php endif; ?>
            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
            <td><?= esc((string) ($r['code'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['publisher_name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['e_issn'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['p_issn'] ?? '-')) ?></td>
            <td>
              <div class="myletters-actions">
                <a class="btn btn-sm activity-btn user-action-btn user-action-edit action-solid action-solid-edit myletters-icon-only" href="<?= site_url('admin/journals/' . (string) $r['id'] . '/edit') ?>" aria-label="Edit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit">
                  <iconify-icon icon="heroicons-outline:pencil" aria-hidden="true"></iconify-icon>
                </a>
                <?php if ($isSuperadmin): ?>
                <form method="post" action="<?= site_url('admin/journals/' . (string) $r['id'] . '/delete') ?>" onsubmit="return confirm('Hapus jurnal ini?')">
                  <button class="btn btn-sm activity-btn user-action-btn user-action-delete action-solid action-solid-delete myletters-icon-only" type="submit" aria-label="Hapus" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hapus">
                    <iconify-icon icon="solar:trash-bin-trash-outline" aria-hidden="true"></iconify-icon>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="<?= $isSuperadmin ? '8' : '7' ?>" class="text-center text-muted">Belum ada data jurnal.</td></tr>
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


