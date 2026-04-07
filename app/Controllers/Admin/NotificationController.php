<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoaLetterModel;
use App\Models\LoaNotificationModel;

class NotificationController extends BaseController
{
    public function index()
    {
        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $model = new LoaNotificationModel();

        $rows = $model
            ->select('loa_notifications.*, loa_letters.loa_number, loa_letters.title, loa_letters.public_token')
            ->join('loa_letters', 'loa_letters.id = loa_notifications.loa_letter_id', 'left')
            ->orderBy('loa_notifications.id', 'DESC')
            ->paginate($perPage);

        return view('admin/notifications/index', [
            'title' => 'Notifikasi',
            'rows' => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $letters = (new LoaLetterModel())
            ->select('id, loa_number, title')
            ->where('status', 'published')
            ->orderBy('id', 'DESC')
            ->findAll(200);

        return view('admin/notifications/form', [
            'title' => 'Tambah Notifikasi',
            'row' => null,
            'letters' => $letters,
        ]);
    }

    public function store()
    {
        $rules = [
            'loa_letter_id' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[menunggu,notifikasi terkirim,gagal terkirim]',
            'sent_to_email' => 'permit_empty|valid_email|max_length[191]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form notifikasi.');
        }
        $v = $this->validator->getValidated();

        (new LoaNotificationModel())->insert([
            'loa_letter_id' => (int) $v['loa_letter_id'],
            'status' => (string) $v['status'],
            'sent_to_email' => $v['sent_to_email'] ?? null,
            'sent_at' => ! empty($this->request->getPost('sent_at')) ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('sent_at'))) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Notifikasi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $row = (new LoaNotificationModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }
        $letters = (new LoaLetterModel())
            ->select('id, loa_number, title')
            ->where('status', 'published')
            ->orderBy('id', 'DESC')
            ->findAll(200);

        return view('admin/notifications/form', [
            'title' => 'Edit Notifikasi',
            'row' => $row,
            'letters' => $letters,
        ]);
    }

    public function update(int $id)
    {
        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }

        $rules = [
            'loa_letter_id' => 'required|is_natural_no_zero',
            'status' => 'required|in_list[menunggu,notifikasi terkirim,gagal terkirim]',
            'sent_to_email' => 'permit_empty|valid_email|max_length[191]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form notifikasi.');
        }
        $v = $this->validator->getValidated();

        $model->update($id, [
            'loa_letter_id' => (int) $v['loa_letter_id'],
            'status' => (string) $v['status'],
            'sent_to_email' => $v['sent_to_email'] ?? null,
            'sent_at' => ! empty($this->request->getPost('sent_at')) ? date('Y-m-d H:i:s', strtotime((string) $this->request->getPost('sent_at'))) : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Notifikasi berhasil diperbarui.');
    }

    public function sendEmail(int $id)
    {
        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }

        $letter = (new LoaLetterModel())->find((int) $row['loa_letter_id']);
        $email = $letter['corresponding_email'] ?? null;

        // TODO: Integrasikan SMTP service nyata.
        $model->update($id, [
            'status' => 'notifikasi terkirim',
            'sent_to_email' => $email,
            'sent_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Notifikasi ditandai sebagai terkirim.');
    }

    public function destroy(int $id)
    {
        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Notifikasi berhasil dihapus.');
    }
}
