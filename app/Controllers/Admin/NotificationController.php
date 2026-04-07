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
            ->select('loa_notifications.*, loa_letters.loa_number, loa_letters.title, loa_letters.public_token, loa_letters.published_at, journals.name as journal_name')
            ->join('loa_letters', 'loa_letters.id = loa_notifications.loa_letter_id', 'left')
            ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
            ->orderBy('loa_notifications.id', 'DESC')
            ->paginate($perPage);

        return view('admin/notifications/index', [
            'title' => 'Notifikasi',
            'subtitle' => 'Daftar LoA terbit yang siap dikirim ke email penulis.',
            'rows' => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function sendEmail(int $id)
    {
        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }
        if ((string) ($row['status'] ?? '') === 'notifikasi terkirim') {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi untuk LoA ini sudah pernah dikirim.');
        }

        $letter = (new LoaLetterModel())->find((int) $row['loa_letter_id']);
        $email = $letter['corresponding_email'] ?? null;

        if (empty($email)) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Email penulis/pengaju LoA belum tersedia.');
        }

        // TODO: Integrasikan SMTP service nyata.
        $model->update($id, [
            'status' => 'notifikasi terkirim',
            'sent_to_email' => $email,
            'sent_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Email notifikasi berhasil dikirim ke penulis/pengaju LoA.');
    }

    public function destroy(int $id)
    {
        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Item notifikasi berhasil dihapus.');
    }
}
