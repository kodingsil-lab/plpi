<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Daftar Jurnal</h6>
    <div class="d-flex gap-2 align-items-center">
      <form method="get" class="d-flex align-items-center gap-2">
        <label class="small text-muted mb-0">Per halaman</label>
        <select name="perPage" class="form-select form-select-sm" onchange="this.form.submit()">
          <?php foreach ([10, 25, 50] as $opt): ?>
            <option value="<?= $opt ?>" <?= ((int) ($perPage ?? 10) === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a class="btn btn-sm btn-primary" href="<?= site_url('admin/journals/create') ?>">Tambah Jurnal</a>
    </div>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive">
      <table class="table table-hover align-middle mb-0 w-100">
        <thead>
        <tr>
          <th>NO</th>
          <th>KODE</th>
          <th>NAMA JURNAL</th>
          <th>PUBLISHER</th>
          <th>SLUG</th>
          <th>ISSN</th>
          <th>E-ISSN</th>
          <th>P-ISSN</th>
          <th>AKSI</th>
        </tr>
        </thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
            <td class="fw-semibold text-primary"><?= esc((string) ($r['code'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['publisher_name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['slug'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['issn'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['e_issn'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['p_issn'] ?? '-')) ?></td>
            <td>
              <a class="btn btn-sm activity-btn user-action-btn user-action-edit" href="<?= site_url('admin/journals/' . (string) $r['id'] . '/edit') ?>">Edit</a>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="9" class="text-center text-muted">Belum ada data jurnal.</td></tr>
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


