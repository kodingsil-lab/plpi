<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="dashboard-card letters-table-card myletters-table-card">
  <div class="card-body">
    <h6 class="mb-3"><?= esc($row ? 'Edit Notifikasi' : 'Tambah Notifikasi') ?></h6>
    <form method="post" action="<?= $row ? site_url('admin/notifikasi/' . (int) $row['id']) : site_url('admin/notifikasi') ?>">
      <?php if ($row): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">LoA</label>
          <select name="loa_letter_id" class="form-select" required>
            <option value="">Pilih LoA</option>
            <?php foreach ($letters as $l): ?>
              <?php $selected = (int) old('loa_letter_id', (string) ($row['loa_letter_id'] ?? 0)) === (int) $l['id']; ?>
              <option value="<?= (int) $l['id'] ?>" <?= $selected ? 'selected' : '' ?>>
                <?= esc((string) $l['loa_number']) ?> - <?= esc((string) $l['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <?php $statusValue = (string) old('status', (string) ($row['status'] ?? 'menunggu')); ?>
          <select name="status" class="form-select" required>
            <option value="menunggu" <?= $statusValue === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
            <option value="notifikasi terkirim" <?= $statusValue === 'notifikasi terkirim' ? 'selected' : '' ?>>Notifikasi Terkirim</option>
            <option value="gagal terkirim" <?= $statusValue === 'gagal terkirim' ? 'selected' : '' ?>>Gagal Terkirim</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Waktu Kirim</label>
          <input class="form-control" type="datetime-local" name="sent_at" value="<?= esc((string) old('sent_at', ! empty($row['sent_at']) ? date('Y-m-d\TH:i', strtotime((string) $row['sent_at'])) : '')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Tujuan</label>
          <input class="form-control" type="email" name="sent_to_email" value="<?= esc((string) old('sent_to_email', (string) ($row['sent_to_email'] ?? ''))) ?>" placeholder="contoh@domain.com">
        </div>
      </div>
      <div class="d-flex gap-2 mt-3 myletters-actions">
        <button class="btn activity-btn user-action-btn user-action-edit" type="submit">Simpan</button>
        <a class="btn activity-btn user-action-btn user-action-detail" href="<?= site_url('admin/notifikasi') ?>">Kembali</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
