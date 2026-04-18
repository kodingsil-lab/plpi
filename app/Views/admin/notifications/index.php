<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php helper('status_badge'); ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Notifikasi</h6>
  </div>
  <div class="card-body pt-2">
    <div class="activity-table-wrap myletters-table-wrap table-responsive">
      <table class="table table-hover align-middle mb-0 w-100">
        <thead><tr><th>NO</th><th>NOMOR LOA</th><th>JURNAL</th><th>JUDUL</th><th>STATUS</th><th>TANGGAL</th><th>AKSI</th></tr></thead>
        <tbody>
        <?php if (! empty($rows)): foreach ($rows as $i => $r): ?>
          <tr>
            <td><?= esc((string) (($startNumber ?? 1) + $i)) ?></td>
            <td class="fw-semibold text-primary"><?= esc((string) ($r['loa_number'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['journal_name'] ?? '-')) ?></td>
            <td><?= esc((string) ($r['title'] ?? '-')) ?></td>
            <td>
              <?php
              $statusMeta = plpi_notification_status_meta((string) ($r['status'] ?? 'menunggu'));
              ?>
              <span class="status-pill status-table-pill myletters-status-pill <?= esc((string) ($statusMeta['class'] ?? 'myletters-status-waiting')) ?>">
                <?= esc((string) ($statusMeta['label'] ?? 'Menunggu')) ?>
              </span>
            </td>
            <td><?= esc((string) (($r['sent_at'] ?? $r['published_at'] ?? '-'))) ?></td>
            <td>
              <div class="myletters-actions">
                <form method="post" action="<?= site_url('admin/notifikasi/' . (string) $r['id'] . '/kirim-email') ?>" class="d-inline">
                  <button
                    class="btn btn-sm activity-btn user-action-btn user-action-detail action-solid action-solid-mail myletters-icon-only"
                    type="submit"
                    aria-label="Kirim Email"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    data-bs-title="Kirim Email"
                    <?= strtolower(trim((string) ($r['status'] ?? ''))) === 'notifikasi terkirim' ? 'disabled' : '' ?>
                  >
                    <iconify-icon icon="heroicons-outline:mail" aria-hidden="true"></iconify-icon>
                  </button>
                </form>
                <form method="post" action="<?= site_url('admin/notifikasi/' . (string) $r['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Hapus item notifikasi ini?')">
                  <button class="btn btn-sm activity-btn user-action-btn user-action-delete action-solid action-solid-delete myletters-icon-only" type="submit" aria-label="Hapus" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Hapus">
                    <iconify-icon icon="solar:trash-bin-trash-outline" aria-hidden="true"></iconify-icon>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-center text-muted">Belum ada item notifikasi. Item notifikasi akan muncul dari LoA yang sudah terbit dan siap dikirim ke email penulis.</td></tr>
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


