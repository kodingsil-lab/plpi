<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

class EmailService
{
    protected Email $email;

    public function __construct()
    {
        // Create fresh email instance each time
        $this->email = \Config\Services::email();
    }

    /**
     * Send LoA Approved Notification Email
     * 
     * @param string $recipientEmail Email penerima
     * @param array $letter Data surat LoA
     * @param string $pdfPath Path ke file PDF LoA
     * @param array $publisher Data penerbit/jurnal
     * @return bool
     */
    public function sendLoaApprovedNotification(
        string $recipientEmail,
        array $letter,
        string $pdfPath,
        array $publisher = []
    ): bool {
        try {
            // Prepare email data
            $journalName = $publisher['journal_name'] ?? $publisher['name'] ?? 'Jurnal';
            $editorName = $publisher['editor_name'] ?? $publisher['signer_name'] ?? 'Pimpinan Redaksi';
            $journalUrl = $publisher['journal_url'] ?? '';

            // Parse authors from JSON
            $authors = $this->parseAuthors($letter['authors_json'] ?? '[]');

            // Set email parameters
            $this->email->setFrom(
                config('Email')->fromEmail ?: env('MAIL_FROM_ADDRESS', 'noreply@plpi.id'),
                config('Email')->fromName ?: env('MAIL_FROM_NAME', 'PLPI - Sistem Manajemen LoA')
            );
            $this->email->setTo($recipientEmail);
            $this->email->setSubject('Notifikasi Letter of Acceptance (LoA) - ' . ($letter['loa_number'] ?? 'LoA'));

            // Render email content
            $emailContent = view('email/loa_approved_notification', [
                'letter' => $letter,
                'authors' => $authors,
                'journalName' => $journalName,
                'editorName' => $editorName,
                'journalUrl' => $journalUrl,
            ]);

            $this->email->setMessage($emailContent);

            // Attach PDF if exists
            if (!empty($pdfPath) && file_exists($pdfPath)) {
                $this->email->attach($pdfPath);
            }

            // Send email
            $result = $this->email->send(false); // false = don't clear
            
            if (!$result) {
                // Log debug information
                log_message('error', 'Email send failed. Debug info: ' . $this->email->printDebugger());
            }
            
            return $result;
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send LoA notification email: ' . $e->getMessage());
            log_message('error', 'Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Parse authors from JSON string
     * 
     * @param string $json JSON string containing authors
     * @return string Formatted author names
     */
    protected function parseAuthors(string $json): string
    {
        try {
            $authors = json_decode($json, true);
            if (!is_array($authors) || empty($authors)) {
                return '-';
            }

            $names = [];
            foreach ($authors as $author) {
                if (is_array($author) && isset($author['name'])) {
                    $name = trim(preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', (string) $author['name']));
                    if ($name !== '') {
                        $names[] = $name;
                    }
                } elseif (is_string($author)) {
                    $name = trim(preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', (string) $author));
                    if ($name !== '') {
                        $names[] = $name;
                    }
                }
            }

            if (empty($names)) {
                return '-';
            }

            $count = count($names);
            if ($count === 1) {
                return $names[0];
            } elseif ($count === 2) {
                return $names[0] . ' dan ' . $names[1];
            } else {
                $last = array_pop($names);
                return implode(', ', $names) . ', dan ' . $last;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error parsing authors: ' . $e->getMessage());
            return '-';
        }
    }

    /**
     * Get email send status
     * 
     * @return string
     */
    public function printDebugger(): string
    {
        return $this->email->printDebugger();
    }
}
