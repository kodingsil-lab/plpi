<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $model = new UserModel();
        $db = \Config\Database::connect();
        $supportsJournalAssignment = $db->fieldExists('journal_id', 'users') || $db->tableExists('user_journals');
        $hasMultiJournalAssignments = $db->tableExists('user_journals');
        $hasSingleJournalAssignment = $db->fieldExists('journal_id', 'users');

        if ($hasMultiJournalAssignments) {
            $rows = $model
                ->select('users.*, (SELECT COUNT(*) FROM user_journals uj WHERE uj.user_id = users.id) AS assigned_journal_count')
                ->orderBy('users.id', 'DESC')
                ->paginate($perPage);
        } elseif ($hasSingleJournalAssignment) {
            $rows = $model
                ->select('users.*, CASE WHEN users.journal_id IS NULL THEN 0 ELSE 1 END AS assigned_journal_count')
                ->orderBy('users.id', 'DESC')
                ->paginate($perPage);
        } else {
            $rows = $model->orderBy('id', 'DESC')->paginate($perPage);
        }

        return view('admin/users/index', [
            'title' => 'Pengguna',
            'rows' => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
            'supportsJournalAssignment' => $supportsJournalAssignment,
            'hasMultiJournalAssignments' => $hasMultiJournalAssignments,
        ]);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $supportsJournalAssignment = $db->fieldExists('journal_id', 'users') || $db->tableExists('user_journals');
        return view('admin/users/form', [
            'title' => 'Tambah Pengguna',
            'row' => null,
            'supportsJournalAssignment' => $supportsJournalAssignment,
            'journals' => $supportsJournalAssignment ? (new JournalModel())->orderBy('name', 'ASC')->findAll() : [],
            'selectedJournalIds' => [],
        ]);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|max_length[80]|is_unique[users.username]',
            'name' => 'required|max_length[191]',
            'email' => 'required|valid_email|max_length[191]|is_unique[users.email]',
            'role' => 'required|in_list[superadmin,admin_jurnal]',
            'password' => 'required|min_length[8]|max_length[100]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form pengguna.');
        }
        $v = $this->validator->getValidated();
        $db = \Config\Database::connect();
        $supportsSingleJournalAssignment = $db->fieldExists('journal_id', 'users');
        $supportsMultiJournalAssignments = $db->tableExists('user_journals');
        $journalIds = [];
        if ((string) $v['role'] === 'admin_jurnal') {
            $journalIds = $this->normalizeJournalIds($this->request->getPost('journal_ids'));
        }

        $payload = [
            'username' => trim((string) $v['username']),
            'name' => trim((string) $v['name']),
            'email' => trim((string) $v['email']),
            'role' => (string) $v['role'],
            'password' => password_hash((string) $v['password'], PASSWORD_BCRYPT),
            'is_active' => (int) ($this->request->getPost('is_active') ? 1 : 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($supportsSingleJournalAssignment) {
            $payload['journal_id'] = $journalIds[0] ?? null;
        }

        $model = new UserModel();
        $model->insert($payload);
        $newUserId = (int) $model->getInsertID();

        if ($supportsMultiJournalAssignments) {
            $this->syncUserJournals($newUserId, (string) $v['role'], $journalIds, $db);
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = (new UserModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }
        $db = \Config\Database::connect();
        $supportsJournalAssignment = $db->fieldExists('journal_id', 'users') || $db->tableExists('user_journals');
        $selectedJournalIds = [];
        if ($db->tableExists('user_journals')) {
            $assignedRows = $db->table('user_journals')->select('journal_id')->where('user_id', $id)->get()->getResultArray();
            foreach ($assignedRows as $assignedRow) {
                $selectedJournalIds[] = (int) ($assignedRow['journal_id'] ?? 0);
            }
        } elseif ($db->fieldExists('journal_id', 'users')) {
            $singleJournalId = (int) ($row['journal_id'] ?? 0);
            if ($singleJournalId > 0) {
                $selectedJournalIds[] = $singleJournalId;
            }
        }

        return view('admin/users/form', [
            'title' => 'Edit Pengguna',
            'row' => $row,
            'supportsJournalAssignment' => $supportsJournalAssignment,
            'journals' => $supportsJournalAssignment ? (new JournalModel())->orderBy('name', 'ASC')->findAll() : [],
            'selectedJournalIds' => $selectedJournalIds,
        ]);
    }

    public function update(int $id)
    {
        $model = new UserModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'username' => 'required|max_length[80]|is_unique[users.username,id,' . $id . ']',
            'name' => 'required|max_length[191]',
            'email' => 'required|valid_email|max_length[191]|is_unique[users.email,id,' . $id . ']',
            'role' => 'required|in_list[superadmin,admin_jurnal]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form pengguna.');
        }
        $v = $this->validator->getValidated();
        $db = \Config\Database::connect();
        $supportsSingleJournalAssignment = $db->fieldExists('journal_id', 'users');
        $supportsMultiJournalAssignments = $db->tableExists('user_journals');

        $journalIds = [];
        if ((string) $v['role'] === 'admin_jurnal') {
            $journalIds = $this->normalizeJournalIds($this->request->getPost('journal_ids'));
        }

        $payload = [
            'username' => trim((string) $v['username']),
            'name' => trim((string) $v['name']),
            'email' => trim((string) $v['email']),
            'role' => (string) $v['role'],
            'is_active' => (int) ($this->request->getPost('is_active') ? 1 : 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($supportsSingleJournalAssignment) {
            $payload['journal_id'] = $journalIds[0] ?? null;
        }

        $newPassword = trim((string) $this->request->getPost('password'));
        if ($newPassword !== '') {
            if (mb_strlen($newPassword) < 8 || mb_strlen($newPassword) > 100) {
                return redirect()->back()->withInput()->with('error', 'Password minimal 8 karakter.');
            }
            $payload['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $model->update($id, $payload);
        if ($supportsMultiJournalAssignments) {
            $this->syncUserJournals($id, (string) $v['role'], $journalIds, $db);
        }

        return redirect()->to(site_url('admin/users'))->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function updatePassword(int $id)
    {
        $model = new UserModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = ['password' => 'required|min_length[8]|max_length[100]'];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $model->update($id, [
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Password pengguna berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $model = new UserModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        if ((int) $row['id'] === (int) session('user_id')) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Akun yang sedang login tidak bisa dihapus.');
        }

        $model->delete($id);
        return redirect()->to(site_url('admin/users'))->with('success', 'Pengguna berhasil dihapus.');
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $userIds = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0 && $id !== (int) session('user_id')) {
                $userIds[] = $id;
            }
        }
        $userIds = array_values(array_unique($userIds));

        if ($userIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih. Akun yang sedang login tidak bisa dihapus.');
        }

        (new UserModel())->delete($userIds);
        return redirect()->to(site_url('admin/users'))->with('success', 'Pengguna terpilih berhasil dihapus.');
    }

    /**
     * @param mixed $journalIdsInput
     * @return int[]
     */
    private function normalizeJournalIds($journalIdsInput): array
    {
        if (! is_array($journalIdsInput)) {
            return [];
        }

        $normalized = [];
        foreach ($journalIdsInput as $journalId) {
            $id = (int) $journalId;
            if ($id > 0) {
                $normalized[] = $id;
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param int[] $journalIds
     */
    private function syncUserJournals(int $userId, string $role, array $journalIds, $db): void
    {
        if (! $db->tableExists('user_journals')) {
            return;
        }

        $builder = $db->table('user_journals');
        $builder->where('user_id', $userId)->delete();

        if ($role !== 'admin_jurnal' || empty($journalIds)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($journalIds as $journalId) {
            $rows[] = [
                'user_id' => $userId,
                'journal_id' => $journalId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (! empty($rows)) {
            $builder->insertBatch($rows);
        }
    }
}
