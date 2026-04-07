<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PublisherModel;

class PublisherController extends BaseController
{
    public function index()
    {
        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $model = new PublisherModel();
        $rows = $model->orderBy('id', 'DESC')->paginate($perPage);

        return view('admin/publishers/index', [
            'title' => 'Publisher',
            'rows' => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('admin/publishers/form', ['title' => 'Tambah Publisher', 'row' => null]);
    }

    public function store()
    {
        $rules = [
            'code' => 'required|max_length[50]|is_unique[publishers.code]',
            'name' => 'required|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[191]',
            'phone' => 'permit_empty|max_length[50]',
            'address' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form publisher.');
        }
        $v = $this->validator->getValidated();
        $payload = [
            'code' => strtoupper(trim((string) $v['code'])),
            'name' => trim((string) $v['name']),
            'email' => $v['email'] ?? null,
            'phone' => $v['phone'] ?? null,
            'address' => $v['address'] ?? null,
        ];
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && $logo->getError() === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'webp'];
            $ext = strtolower((string) $logo->getExtension());
            if (! in_array($ext, $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'Format logo harus PNG/JPG/JPEG/WEBP.');
            }
            $newName = $logo->getRandomName();
            $logo->move(WRITEPATH . 'uploads/publishers', $newName, true);
            $payload['logo_path'] = 'publishers/' . $newName;
        }
        (new PublisherModel())->insert($payload);

        return redirect()->to(site_url('admin/publishers'))->with('success', 'Publisher berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = (new PublisherModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/publishers'))->with('error', 'Publisher tidak ditemukan.');
        }
        return view('admin/publishers/form', ['title' => 'Edit Publisher', 'row' => $row]);
    }

    public function update(int $id)
    {
        $model = new PublisherModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/publishers'))->with('error', 'Publisher tidak ditemukan.');
        }

        $rules = [
            'code' => 'required|max_length[50]',
            'name' => 'required|max_length[255]',
            'email' => 'permit_empty|valid_email|max_length[191]',
            'phone' => 'permit_empty|max_length[50]',
            'address' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form publisher.');
        }
        $v = $this->validator->getValidated();

        $payload = [
            'code' => strtoupper(trim((string) $v['code'])),
            'name' => trim((string) $v['name']),
            'email' => $v['email'] ?? null,
            'phone' => $v['phone'] ?? null,
            'address' => $v['address'] ?? null,
        ];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && $logo->getError() === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'webp'];
            $ext = strtolower((string) $logo->getExtension());
            if (! in_array($ext, $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'Format logo harus PNG/JPG/JPEG/WEBP.');
            }
            $newName = $logo->getRandomName();
            $logo->move(WRITEPATH . 'uploads/publishers', $newName, true);
            $payload['logo_path'] = 'publishers/' . $newName;
        }

        $model->update($id, $payload);

        return redirect()->to(site_url('admin/publishers/' . $id . '/edit'))->with('success', 'Publisher berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $model = new PublisherModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/publishers'))->with('error', 'Publisher tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to(site_url('admin/publishers'))->with('success', 'Publisher berhasil dihapus.');
    }
}
