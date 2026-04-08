<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$selectedJournalIdsFromServer = isset($selectedJournalIds) && is_array($selectedJournalIds)
  ? array_map('strval', $selectedJournalIds)
  : [];
$selectedJournalIds = old('journal_ids');
if (! is_array($selectedJournalIds)) {
  $selectedJournalIds = $selectedJournalIds !== null ? [(string) $selectedJournalIds] : [];
}
if (empty($selectedJournalIds)) {
  $selectedJournalIds = $selectedJournalIdsFromServer;
}
?>
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
          <select name="role" id="roleSelect" class="form-select">
            <option value="superadmin" <?= (($row['role'] ?? old('role')) === 'superadmin') ? 'selected' : '' ?>>superadmin</option>
            <option value="admin_jurnal" <?= (($row['role'] ?? old('role')) === 'admin_jurnal') ? 'selected' : '' ?>>adminjurnal</option>
          </select>
        </div>
        <?php if (! empty($supportsJournalAssignment)): ?>
        <div class="col-md-6" id="journalAssignmentWrap">
          <label class="form-label">Jurnal Yang Ditugaskan</label>
          <div class="journal-suggest-box" id="journalSuggestBox">
            <input
              type="text"
              id="journalSearchInput"
              class="form-control journal-suggest-search"
              placeholder="Cari jurnal..."
              autocomplete="off"
            >
            <div class="journal-suggest-list" id="journalSuggestList">
              <?php foreach (($journals ?? []) as $j): ?>
                <?php
                  $journalId = (string) ($j['id'] ?? '');
                  $journalName = (string) ($j['name'] ?? '-');
                ?>
                <label class="journal-suggest-item" data-journal-name="<?= esc(strtolower($journalName), 'attr') ?>">
                  <input
                    type="checkbox"
                    name="journal_ids[]"
                    value="<?= esc($journalId) ?>"
                    class="journal-checkbox"
                    <?= in_array($journalId, $selectedJournalIds, true) ? 'checked' : '' ?>
                  >
                  <span><?= esc($journalName) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="journal-selected-badges" id="journalSelectedBadges"></div>
          </div>
          <small class="text-muted d-block mt-1">Bisa pilih lebih dari satu jurnal dengan checklist.</small>
        </div>
        <?php endif; ?>
        <div class="col-md-6">
          <label class="form-label">Aktif</label>
          <select name="is_active" class="form-select">
            <option value="1" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '1') ? 'selected' : '' ?>>Ya</option>
            <option value="0" <?= ((string) ($row['is_active'] ?? old('is_active', '1')) === '0') ? 'selected' : '' ?>>Tidak</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label"><?= $row ? 'Password Baru (opsional)' : 'Password' ?></label>
          <div class="input-group">
            <input id="passwordInput" class="form-control" type="password" name="password" <?= $row ? '' : 'required' ?>>
            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" aria-label="Tampilkan password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  var roleSelect = document.getElementById('roleSelect');
  var journalWrap = document.getElementById('journalAssignmentWrap');
  var journalSearchInput = document.getElementById('journalSearchInput');
  var journalSuggestList = document.getElementById('journalSuggestList');
  var journalSelectedBadges = document.getElementById('journalSelectedBadges');
  var journalCheckboxes = document.querySelectorAll('.journal-checkbox');
  var journalSuggestItems = document.querySelectorAll('.journal-suggest-item');
  var passwordInput = document.getElementById('passwordInput');
  var togglePasswordBtn = document.getElementById('togglePasswordBtn');

  function renderSelectedBadges() {
    if (!journalSelectedBadges) {
      return;
    }
    var html = '';
    journalCheckboxes.forEach(function (checkbox) {
      if (checkbox.checked) {
        var labelText = '';
        var parent = checkbox.closest('.journal-suggest-item');
        if (parent) {
          var span = parent.querySelector('span');
          labelText = span ? span.textContent.trim() : '';
        }
        if (labelText) {
          html += '<span class="journal-selected-badge">' + labelText + '</span>';
        }
      }
    });
    journalSelectedBadges.innerHTML = html || '<span class="journal-selected-empty">Belum ada jurnal dipilih.</span>';
  }

  function filterJournalSuggestions() {
    if (!journalSearchInput || !journalSuggestItems.length) {
      return;
    }
    var query = journalSearchInput.value.toLowerCase().trim();
    journalSuggestItems.forEach(function (item) {
      var name = (item.getAttribute('data-journal-name') || '').toLowerCase();
      item.style.display = name.indexOf(query) !== -1 ? '' : 'none';
    });
  }

  function syncJournalFieldByRole() {
    if (!roleSelect || !journalWrap) {
      return;
    }
    var isSuperadmin = roleSelect.value === 'superadmin';
    journalWrap.style.display = isSuperadmin ? 'none' : '';
    if (journalSearchInput) {
      journalSearchInput.disabled = isSuperadmin;
    }
    if (isSuperadmin) {
      journalCheckboxes.forEach(function (checkbox) { checkbox.checked = false; });
      renderSelectedBadges();
    }
  }

  if (roleSelect) {
    roleSelect.addEventListener('change', syncJournalFieldByRole);
    syncJournalFieldByRole();
  }

  if (journalSearchInput) {
    journalSearchInput.addEventListener('input', filterJournalSuggestions);
  }

  if (journalCheckboxes.length) {
    journalCheckboxes.forEach(function (checkbox) {
      checkbox.addEventListener('change', renderSelectedBadges);
    });
    renderSelectedBadges();
  }

  if (passwordInput && togglePasswordBtn) {
    togglePasswordBtn.addEventListener('click', function () {
      var isMasked = passwordInput.type === 'password';
      passwordInput.type = isMasked ? 'text' : 'password';
      togglePasswordBtn.innerHTML = isMasked ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      togglePasswordBtn.setAttribute('aria-label', isMasked ? 'Sembunyikan password' : 'Tampilkan password');
    });
  }
});
</script>
<?= $this->endSection() ?>
