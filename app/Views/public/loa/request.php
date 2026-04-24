<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<style>
    .loa-shell {
        background: #fff;
        border: 1px solid #d9e4f2;
        border-radius: 18px;
        box-shadow: 0 16px 34px rgba(18, 41, 77, 0.08);
        padding: 18px;
    }

    .loa-header {
        border-bottom: 1px solid #e5edf8;
        padding-bottom: 12px;
        margin-bottom: 16px;
        text-align: center;
    }

    .loa-header h3 {
        margin: 0;
        color: #163b73;
        font-size: clamp(1.35rem, 2.1vw, 1.8rem);
        font-weight: 800;
    }

    .loa-header p {
        margin: 8px 0 0;
        color: #5f7493;
        font-size: .95rem;
    }

    .loa-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .loa-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .loa-field.full {
        grid-column: 1 / -1;
    }

    .loa-label {
        color: #1c3762;
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
    }

    .loa-input {
        width: 100%;
        border: 1px solid #c7d8ee;
        border-radius: 11px;
        background: #fbfdff;
        color: #1f3658;
        padding: 10px 12px;
        font-size: .95rem;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .loa-input:focus {
        border-color: #4f79cb;
        box-shadow: 0 0 0 .2rem rgba(43, 89, 181, .15);
        background: #fff;
        outline: none;
    }

    .loa-actions {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px dashed #d6e4f5;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .loa-btn-main,
    .loa-btn-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        text-decoration: none;
        font-size: .92rem;
        font-weight: 700;
        padding: 10px 14px;
        line-height: 1;
        transition: .2s ease;
        cursor: pointer;
    }

    .loa-btn-main {
        border: 1px solid #2b59b5;
        background: linear-gradient(135deg, #2b59b5 0%, #3f70cc 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(43, 89, 181, .22);
    }

    .loa-btn-main:hover {
        border-color: #163b73;
        background: #163b73;
        color: #fff;
    }

    .loa-btn-soft {
        border: 1px solid #c5d7ef;
        background: #fff;
        color: #224a92;
    }

    .loa-btn-soft:hover {
        border-color: #2b59b5;
        color: #163b73;
    }

    .loa-author-builder {
        border: 1px solid #d7e3f3;
        border-radius: 12px;
        background: #f9fbff;
        padding: 12px;
    }

    .loa-author-controls {
        display: grid;
        grid-template-columns: 180px 1fr auto;
        gap: 10px;
        align-items: center;
    }

    .loa-author-hint {
        margin: 8px 0 0;
        color: #657b9a;
        font-size: .84rem;
    }

    .loa-author-list {
        margin-top: 12px;
    }

    .loa-data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border: 1px solid #d5e2f3;
        border-radius: 10px;
        background: #fff;
    }

    .loa-data-table thead th {
        background: #eef1f5;
        color: #4a5568;
        font-size: .79rem;
        font-weight: 800;
        text-transform: none;
        letter-spacing: .1px;
        padding: 9px 10px;
        border-bottom: 1px solid #d5e2f3;
    }

    .loa-data-table tbody td {
        padding: 9px 10px;
        font-size: .92rem;
        color: #1f3658;
        border-bottom: 1px solid #e8eff9;
        vertical-align: middle;
    }

    .loa-data-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .loa-data-table .col-action {
        width: 88px;
        text-align: right;
    }

    .loa-author-item {
        color: #1f4a90;
        font-size: .84rem;
        font-weight: 700;
    }

    .loa-author-name {
        color: #1f3658;
        font-size: .92rem;
        font-weight: 600;
    }

    .loa-author-remove {
        border: 1px solid #efc6cc;
        background: #fff5f6;
        color: #9f2d3a;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 700;
        line-height: 1;
        padding: 7px 9px;
        cursor: pointer;
    }

    .loa-author-empty {
        border: 1px dashed #d3e0f1;
        border-radius: 10px;
        color: #6a809f;
        font-size: .9rem;
        padding: 10px;
        text-align: center;
        background: #fff;
    }

    .loa-hidden-field {
        display: none;
    }

    .loa-aff-switch {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .9rem;
        color: #224a92;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .loa-aff-switch input {
        accent-color: #2b59b5;
    }

    .loa-aff-single-wrap,
    .loa-aff-list-wrap {
        border: 1px solid #d7e3f3;
        border-radius: 12px;
        background: #f9fbff;
        padding: 12px;
    }

    .loa-aff-controls {
        display: grid;
        grid-template-columns: 180px 1fr auto;
        gap: 10px;
        align-items: center;
    }

    .loa-aff-list {
        margin-top: 12px;
    }

    .loa-aff-item {
        color: #1f4a90;
        font-size: .84rem;
        font-weight: 700;
    }

    .loa-aff-text {
        color: #1f3658;
        font-size: .92rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .loa-grid {
            grid-template-columns: 1fr;
        }

        .loa-author-controls {
            grid-template-columns: 1fr;
        }

        .loa-aff-controls {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="loa-shell">
    <div class="loa-header">
        <h3>Ajukan Letter of Acceptance</h3>
        <p>Lengkapi data artikel untuk proses pengajuan Letter of Acceptance (LoA)</p>
    </div>

    <form method="post" action="<?= site_url('loa/request') ?>">
        <div class="loa-grid">
            <div class="loa-field full">
                <label class="loa-label">Jurnal</label>
                <select name="journal_id" class="loa-input">
                    <option value="">Pilih Jurnal</option>
                    <?php foreach (($journals ?? []) as $j): ?>
                        <option value="<?= esc((string) $j['id']) ?>" <?= old('journal_id') == $j['id'] ? 'selected' : '' ?>><?= esc((string) $j['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="loa-field full">
                <label class="loa-label">Judul Artikel</label>
                <input type="text" name="title" value="<?= esc((string) old('title')) ?>" class="loa-input" placeholder="Masukkan judul artikel" required>
            </div>

            <div class="loa-field">
                <label class="loa-label">Email Korespondensi</label>
                <input type="email" name="corresponding_email" value="<?= esc((string) old('corresponding_email')) ?>" class="loa-input" placeholder="contoh@email.com" required>
            </div>

            <div class="loa-field">
                <label class="loa-label">Nomor WhatsApp</label>
                <input type="text" name="whatsapp_number" value="<?= esc((string) old('whatsapp_number')) ?>" class="loa-input" placeholder="Contoh: 0812xxxxxx / +62812xxxxxx" required>
            </div>

            <div class="loa-field">
                <label class="loa-label">Volume</label>
                <input type="text" name="volume" value="<?= esc((string) old('volume')) ?>" class="loa-input" placeholder="Contoh: 12">
            </div>

            <div class="loa-field">
                <label class="loa-label">Nomor</label>
                <input type="text" name="issue_number" value="<?= esc((string) old('issue_number')) ?>" class="loa-input" placeholder="Contoh: 2">
            </div>

            <div class="loa-field">
                <label class="loa-label">URL Artikel (Opsional)</label>
                <input type="url" name="article_url" value="<?= esc((string) old('article_url')) ?>" class="loa-input" placeholder="https://...">
            </div>

            <div class="loa-field">
                <label class="loa-label">Tahun</label>
                <input type="text" name="published_year" value="<?= esc((string) old('published_year')) ?>" class="loa-input" placeholder="2026">
            </div>

            <div class="loa-field full">
                <label class="loa-label">Identitas Penulis</label>
                <div class="loa-author-builder">
                    <div class="loa-author-controls">
                        <select id="authorRole" class="loa-input">
                            <option value="ketua">Ketua</option>
                            <option value="anggota">Anggota</option>
                        </select>
                        <input type="text" id="authorName" class="loa-input" placeholder="Isi Nama Penulis Tanpa Gelar">
                        <button type="button" id="addAuthorBtn" class="loa-btn-soft">+ Tambah</button>
                    </div>
                    <p class="loa-author-hint">Pilih peran penulis, isi nama tanpa gelar, lalu klik tambah. Data akan muncul di bawah dan bisa dihapus.</p>
                    <div id="authorList" class="loa-author-list"></div>
                </div>
                <textarea name="authors_text" id="authors_text" class="loa-input loa-hidden-field" rows="5"><?= esc((string) old('authors_text')) ?></textarea>
            </div>

            <div class="loa-field full">
                <label class="loa-label">Afiliasi Penulis</label>
                <label class="loa-aff-switch">
                    <input type="checkbox" id="affSameForAll" checked>
                    Afiliasi sama untuk semua penulis
                </label>

                <div id="affSingleWrap" class="loa-aff-single-wrap">
                    <input type="text" id="affSingleInput" class="loa-input" placeholder="Isi Nama Institusi atau Perguruan Tinggi">
                    <p class="loa-author-hint">Jika semua penulis dari institusi yang sama, cukup isi satu afiliasi ini.</p>
                </div>

                <div id="affListWrap" class="loa-aff-list-wrap" style="display:none;">
                    <div class="loa-aff-controls">
                        <select id="affRoleSelect" class="loa-input">
                            <option value="">Pilih peran penulis</option>
                        </select>
                        <input type="text" id="affLineInput" class="loa-input" placeholder="Ketik afiliasi penulis">
                        <button type="button" id="addAffBtn" class="loa-btn-soft">+ Tambah</button>
                    </div>
                    <p class="loa-author-hint">Gunakan mode ini jika afiliasi penulis berbeda-beda.</p>
                    <div id="affList" class="loa-aff-list"></div>
                </div>

                <textarea name="affiliations_text" id="affiliations_text" class="loa-input loa-hidden-field" rows="4"><?= esc((string) old('affiliations_text')) ?></textarea>
            </div>
        </div>

        <div class="loa-actions">
            <button class="loa-btn-main" type="submit">Kirim Permohonan</button>
            <a class="loa-btn-soft" href="<?= site_url('/') ?>">Kembali</a>
        </div>
    </form>
</div>
<script>
    (() => {
        const form = document.querySelector('form[action$="loa/request"]');
        if (!form) return;

        const roleSelect = document.getElementById('authorRole');
        const nameInput = document.getElementById('authorName');
        const addBtn = document.getElementById('addAuthorBtn');
        const listEl = document.getElementById('authorList');
        const hiddenAuthors = document.getElementById('authors_text');
        const affSameForAll = document.getElementById('affSameForAll');
        const affSingleWrap = document.getElementById('affSingleWrap');
        const affListWrap = document.getElementById('affListWrap');
        const affRoleSelect = document.getElementById('affRoleSelect');
        const affSingleInput = document.getElementById('affSingleInput');
        const affLineInput = document.getElementById('affLineInput');
        const addAffBtn = document.getElementById('addAffBtn');
        const affListEl = document.getElementById('affList');
        const hiddenAffiliations = document.getElementById('affiliations_text');

        const authors = [];
        const affiliations = [];

        const updateAffRoleOptions = () => {
            const current = affRoleSelect.value;
            affRoleSelect.innerHTML = '';
            if (!authors.length) {
                affRoleSelect.innerHTML = '<option value="">Tambahkan penulis dulu</option>';
                return;
            }

            affRoleSelect.innerHTML = '<option value="">Pilih peran penulis</option>';
            authors.forEach((item) => {
                const opt = document.createElement('option');
                opt.value = item.role;
                opt.textContent = item.role;
                affRoleSelect.appendChild(opt);
            });

            if (current && [...affRoleSelect.options].some((opt) => opt.value === current)) {
                affRoleSelect.value = current;
            }
        };

        const renumberAnggota = () => {
            let anggotaIndex = 1;
            authors.forEach((item) => {
                if (item.role === 'Ketua') return;
                item.role = `Anggota ${anggotaIndex}`;
                anggotaIndex += 1;
            });
        };

        const syncHiddenValue = () => {
            hiddenAuthors.value = authors.map((item) => `${item.role}: ${item.name}`).join('\n');
        };

        const renderAuthors = () => {
            listEl.innerHTML = '';

            if (!authors.length) {
                const empty = document.createElement('div');
                empty.className = 'loa-author-empty';
                empty.textContent = 'Belum ada penulis ditambahkan.';
                listEl.appendChild(empty);
                syncHiddenValue();
                updateAffRoleOptions();
                return;
            }

            const table = document.createElement('table');
            table.className = 'loa-data-table';
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Peran</th>
                        <th>Nama Penulis</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;

            const tbody = table.querySelector('tbody');
            authors.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="loa-author-item">${item.role}</span></td>
                    <td><span class="loa-author-name">${item.name}</span></td>
                    <td class="col-action"><button type="button" class="loa-author-remove" data-index="${index}">Hapus</button></td>
                `;
                tbody.appendChild(tr);
            });
            listEl.appendChild(table);

            syncHiddenValue();
            updateAffRoleOptions();
        };

        const parseInitial = () => {
            const raw = (hiddenAuthors.value || '').trim();
            if (!raw) {
                renderAuthors();
                return;
            }

            raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean).forEach((line, index) => {
                const match = line.match(/^([^:|-]+)\s*[:|-]\s*(.+)$/);
                if (match) {
                    const parsedRole = match[1].trim().toLowerCase();
                    const parsedName = match[2].trim();
                    if (parsedRole.startsWith('ketua')) {
                        authors.push({ role: 'Ketua', name: parsedName });
                    } else if (parsedRole.startsWith('anggota')) {
                        authors.push({ role: 'Anggota', name: parsedName });
                    } else {
                        authors.push({ role: index === 0 ? 'Ketua' : 'Anggota', name: parsedName });
                    }
                } else {
                    authors.push({ role: index === 0 ? 'Ketua' : 'Anggota', name: line });
                }
            });

            renumberAnggota();
            renderAuthors();
        };

        const syncAffiliationsValue = () => {
            if (affSameForAll.checked) {
                const single = affSingleInput.value.trim();
                hiddenAffiliations.value = single ? single : '';
                return;
            }
            hiddenAffiliations.value = affiliations.map((item) => `${item.role}: ${item.affiliation}`).join('\n');
        };

        const renderAffiliations = () => {
            affListEl.innerHTML = '';
            if (!affiliations.length) {
                const empty = document.createElement('div');
                empty.className = 'loa-author-empty';
                empty.textContent = 'Belum ada afiliasi ditambahkan.';
                affListEl.appendChild(empty);
                syncAffiliationsValue();
                return;
            }

            const table = document.createElement('table');
            table.className = 'loa-data-table';
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Peran</th>
                        <th>Afiliasi</th>
                        <th class="col-action">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;

            const tbody = table.querySelector('tbody');
            affiliations.forEach((item, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><span class="loa-aff-item">${item.role}</span></td>
                    <td><span class="loa-aff-text">${item.affiliation}</span></td>
                    <td class="col-action"><button type="button" class="loa-author-remove" data-aff-index="${index}">Hapus</button></td>
                `;
                tbody.appendChild(tr);
            });
            affListEl.appendChild(table);
            syncAffiliationsValue();
        };

        const toggleAffMode = () => {
            if (affSameForAll.checked) {
                affSingleWrap.style.display = '';
                affListWrap.style.display = 'none';
            } else {
                affSingleWrap.style.display = 'none';
                affListWrap.style.display = '';
            }
            syncAffiliationsValue();
        };

        const parseInitialAffiliations = () => {
            const raw = (hiddenAffiliations.value || '').trim();
            if (!raw) {
                renderAffiliations();
                return;
            }
            const lines = raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
            if (lines.length <= 1 && !lines[0]?.includes(':')) {
                affSameForAll.checked = true;
                affSingleInput.value = lines[0] || '';
            } else {
                affSameForAll.checked = false;
                lines.forEach((line) => {
                    const match = line.match(/^([^:]+)\s*:\s*(.+)$/);
                    if (match) {
                        affiliations.push({ role: match[1].trim(), affiliation: match[2].trim() });
                    } else {
                        affiliations.push({ role: 'Afiliasi', affiliation: line });
                    }
                });
            }
            renderAffiliations();
            toggleAffMode();
        };

        addBtn.addEventListener('click', () => {
            const roleType = roleSelect.value;
            const name = nameInput.value.trim();

            if (!name) {
                nameInput.focus();
                return;
            }

            if (roleType === 'ketua' && authors.some(item => item.role === 'Ketua')) {
                alert('Ketua sudah ada. Gunakan peran Anggota untuk penulis berikutnya.');
                return;
            }

            if (roleType === 'ketua') {
                authors.push({ role: 'Ketua', name });
            } else {
                authors.push({ role: 'Anggota', name });
                renumberAnggota();
            }

            nameInput.value = '';
            nameInput.focus();
            renderAuthors();
        });

        listEl.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.classList.contains('loa-author-remove')) return;

            const index = Number(target.dataset.index);
            if (Number.isNaN(index)) return;

            authors.splice(index, 1);
            renumberAnggota();
            renderAuthors();
        });

        addAffBtn.addEventListener('click', () => {
            if (!authors.length) {
                alert('Tambahkan identitas penulis terlebih dahulu.');
                return;
            }

            const role = affRoleSelect.value;
            const value = affLineInput.value.trim();
            if (!role) {
                affRoleSelect.focus();
                return;
            }
            if (!value) {
                affLineInput.focus();
                return;
            }
            affiliations.push({ role, affiliation: value });
            affLineInput.value = '';
            affLineInput.focus();
            renderAffiliations();
        });

        affListEl.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.classList.contains('loa-author-remove')) return;
            const index = Number(target.dataset.affIndex);
            if (Number.isNaN(index)) return;
            affiliations.splice(index, 1);
            renderAffiliations();
        });

        affSameForAll.addEventListener('change', toggleAffMode);
        affSingleInput.addEventListener('input', syncAffiliationsValue);

        form.addEventListener('submit', (event) => {
            if (!authors.length) {
                event.preventDefault();
                alert('Tambahkan minimal satu identitas penulis.');
                nameInput.focus();
                return;
            }
            syncHiddenValue();
            syncAffiliationsValue();
        });

        parseInitial();
        parseInitialAffiliations();
    })();
</script>
<?= $this->endSection() ?>
