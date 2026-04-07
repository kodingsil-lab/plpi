<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Libraries\LoaPdfService;
use App\Models\LoaLetterModel;
use App\Models\JournalModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class LoaLetterController extends BaseController
{
    public function show(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $journal = (new JournalModel())->find((int) $letter['journal_id']);

        return view('public/loa/show', [
            'title' => 'LoA Publik',
            'letter' => $this->normalizeLetter($letter),
            'journal' => $journal ?: [],
        ]);
    }

    public function preview(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $pdfPath = $this->ensurePdf($letter, (bool) $this->request->getGet('refresh'));
        $absolute = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');

        if (! is_file($absolute)) {
            throw new PageNotFoundException('PDF LoA tidak ditemukan.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $letter['loa_number']) ?: 'loa_letter';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $safeName . '.pdf"')
            ->setBody((string) file_get_contents($absolute));
    }

    public function download(string $token)
    {
        $letter = $this->findLetterByToken($token);
        $pdfPath = $this->ensurePdf($letter, false);
        $absolute = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');

        if (! is_file($absolute)) {
            throw new PageNotFoundException('PDF LoA tidak ditemukan.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $letter['loa_number']) ?: 'loa_letter';
        return $this->response->download($absolute, null)->setFileName($safeName . '.pdf');
    }

    private function findLetterByToken(string $token): array
    {
        $letter = (new LoaLetterModel())
            ->where('public_token', $token)
            ->where('status', 'published')
            ->first();

        if (! $letter) {
            throw new PageNotFoundException('LoA tidak ditemukan.');
        }

        return $letter;
    }

    private function ensurePdf(array $letter, bool $forceRefresh): string
    {
        $current = (string) ($letter['pdf_path'] ?? '');
        $absoluteCurrent = $current !== '' ? WRITEPATH . 'uploads/' . ltrim($current, '/') : '';
        if (! $forceRefresh && $absoluteCurrent !== '' && is_file($absoluteCurrent)) {
            return $current;
        }

        $service = new LoaPdfService();
        $newPath = $service->generate($this->normalizeLetter($letter));

        (new LoaLetterModel())->update((int) $letter['id'], [
            'pdf_path' => $newPath,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $newPath;
    }

    private function normalizeLetter(array $letter): array
    {
        foreach (['authors_json', 'affiliations_json'] as $field) {
            if (isset($letter[$field]) && is_string($letter[$field])) {
                $decoded = json_decode($letter[$field], true);
                $letter[$field] = is_array($decoded) ? $decoded : [];
            }
        }
        return $letter;
    }
}
