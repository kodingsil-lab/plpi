<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\EmailService;
use App\Libraries\LoaPdfService;
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

        $isResend = strtolower(trim((string) ($row['status'] ?? ''))) === 'notifikasi terkirim';

        $letterModel = new LoaLetterModel();
        $letter = $letterModel->find((int) $row['loa_letter_id']);
        if (! $letter) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Data LoA tidak ditemukan.');
        }
        $email = $letter['corresponding_email'] ?? null;

        if (empty($email)) {
            return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Email penulis/pengaju LoA belum tersedia.');
        }

        try {
            $letterId = (int) ($letter['id'] ?? 0);
            $loaNumber = (string) ($letter['loa_number'] ?? '-');
            $notificationId = (int) ($row['id'] ?? 0);

            log_message('info', sprintf(
                '[LoA Notification] Start send flow. notification_id=%d, loa_letter_id=%d, loa_number=%s, recipient=%s',
                $notificationId,
                $letterId,
                $loaNumber,
                (string) $email
            ));

            // Always regenerate PDF first to ensure latest document is sent.
            $newPdfPath = (new LoaPdfService())->generate($letter);
            $letterModel->update((int) $letter['id'], [
                'pdf_path' => $newPdfPath,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $letter['pdf_path'] = $newPdfPath;
            log_message('info', sprintf(
                '[LoA Notification] PDF regenerated. notification_id=%d, loa_letter_id=%d, loa_number=%s, pdf_path=%s',
                $notificationId,
                $letterId,
                $loaNumber,
                (string) $newPdfPath
            ));

            $pdfFullPath = WRITEPATH . 'uploads/' . ltrim((string) ($letter['pdf_path'] ?? ''), '/\\');
            if (! is_file($pdfFullPath)) {
                log_message('error', 'Regenerated PDF not found at path: ' . $pdfFullPath);
                return redirect()->to(site_url('admin/notifikasi'))->with('error', 'Gagal menyiapkan file PDF terbaru untuk notifikasi.');
            }

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
                log_message('info', sprintf(
                    '[LoA Notification] Email sent. notification_id=%d, loa_letter_id=%d, loa_number=%s, recipient=%s',
                    $notificationId,
                    $letterId,
                    $loaNumber,
                    (string) $email
                ));

                $successMessage = $isResend
                    ? 'Email notifikasi berhasil dikirim ulang ke penulis/pengaju LoA.'
                    : 'Email notifikasi berhasil dikirim ke penulis/pengaju LoA.';

                return redirect()->to(site_url('admin/notifikasi'))->with('success', $successMessage);
            } else {
                log_message('error', sprintf(
                    '[LoA Notification] Email send failed. notification_id=%d, loa_letter_id=%d, loa_number=%s, recipient=%s',
                    $notificationId,
                    $letterId,
                    $loaNumber,
                    (string) $email
                ));
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
