<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
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
        $rows = $model->orderBy('id', 'DESC')->paginate($perPage);

        return view('admin/users/index', [
            'title' => 'Pengguna',
            'rows' => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('admin/users/form', ['title' => 'Tambah Pengguna', 'row' => null]);
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
        (new UserModel())->insert([
            'username' => trim((string) $v['username']),
            'name' => trim((string) $v['name']),
            'email' => trim((string) $v['email']),
            'role' => (string) $v['role'],
            'password' => password_hash((string) $v['password'], PASSWORD_BCRYPT),
            'is_active' => (int) ($this->request->getPost('is_active') ? 1 : 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = (new UserModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Pengguna tidak ditemukan.');
        }
        return view('admin/users/form', ['title' => 'Edit Pengguna', 'row' => $row]);
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

        $model->update($id, [
            'username' => trim((string) $v['username']),
            'name' => trim((string) $v['name']),
            'email' => trim((string) $v['email']),
            'role' => (string) $v['role'],
            'is_active' => (int) ($this->request->getPost('is_active') ? 1 : 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

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
}
