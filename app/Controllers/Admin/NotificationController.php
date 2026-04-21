<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\EmailService;
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

        // Check if PDF exists
        $pdfPath = $letter['pdf_path'] ?? null;
        
        // Try different path possibilities
        $possiblePaths = [
            FCPATH . 'uploads/' . $pdfPath,  // public/uploads/
            ROOTPATH . 'writable/uploads/' . $pdfPath,  // writable/uploads/
            dirname(FCPATH) . '/writable/uploads/' . $pdfPath,  // ../writable/uploads/
        ];
        
        $pdfFullPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $pdfFullPath = $path;
                break;
            }
        }
        
        if (empty($pdfPath) || empty($pdfFullPath)) {
            // Debug info
            log_message('error', 'PDF not found. Checked paths: ' . json_encode($possiblePaths));
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'File PDF LoA tidak ditemukan. Pastikan PDF telah di-generate.');
        }

        try {
            // Get journal and publisher info
            $db = \Config\Database::connect();
            $journalData = $db->table('journals')
                ->select('journals.name, journals.website_url, publishers.name as publisher_name, publishers.email, publishers.phone, publishers.address, journals.default_signer_name, journals.default_signer_title')
                ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
                ->where('journals.id', $letter['journal_id'])
                ->get()
                ->getRowArray();

            // Send email using EmailService
            $emailService = new EmailService();
            $publisherData = [
                'journal_name' => $journalData['name'] ?? 'Jurnal',
                'journal_url' => $journalData['website_url'] ?? '',
                'name' => $journalData['publisher_name'] ?? 'Penerbit',
                'email' => $journalData['email'] ?? '',
                'phone' => $journalData['phone'] ?? '',
                'address' => $journalData['address'] ?? '',
                'editor_name' => $journalData['default_signer_name'] ?? 'Pimpinan Redaksi',
                'signer_name' => $journalData['default_signer_title'] ?? 'Pimpinan Redaksi',
            ];

            // $pdfFullPath is already determined in the path check above
            $sent = $emailService->sendLoaApprovedNotification(
                $email,
                $letter,
                $pdfFullPath,
                $publisherData
            );

            if ($sent) {
                // Update notification status
                $model->update($id, [
                    'status' => 'notifikasi terkirim',
                    'sent_to_email' => $email,
                    'sent_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Email notifikasi berhasil dikirim ke penulis/pengaju LoA.');
            } else {
                return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Gagal mengirim email notifikasi. Silakan periksa konfigurasi email dan coba lagi.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Notification email error: ' . $e->getMessage());
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage());
        }
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

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $notificationIds = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $notificationIds[] = $id;
            }
        }
        $notificationIds = array_values(array_unique($notificationIds));

        if ($notificationIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new LoaNotificationModel())->delete($notificationIds);

        return redirect()->to(site_url('admin/notifikasi'))->with('success', 'Item notifikasi terpilih berhasil dihapus.');
    }
}
