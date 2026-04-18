<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;

class HomeController extends BaseController
{
    public function index()
    {
        $requestModel = new LoaRequestModel();
        $letterModel = new LoaLetterModel();
        $journalModel = new JournalModel();

        $latestRequests = $requestModel
            ->select('loa_requests.request_code, loa_requests.title, loa_requests.status, loa_requests.created_at')
            ->orderBy('loa_requests.id', 'DESC')
            ->findAll(5);

        $journalProfiles = $journalModel
            ->select('journals.id, journals.name, journals.code, journals.e_issn, journals.p_issn, journals.logo_path, publishers.name as publisher_name')
            ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
            ->orderBy('journals.id', 'DESC')
            ->findAll(8);

        foreach ($journalProfiles as &$journal) {
            $journal['logo_data_uri'] = null;
            $logoPath = trim((string) ($journal['logo_path'] ?? ''));
            if ($logoPath === '') {
                continue;
            }

            $fullPath = WRITEPATH . 'uploads/' . ltrim($logoPath, '/\\');
            if (! is_file($fullPath) || ! is_readable($fullPath)) {
                continue;
            }

            $mime = @mime_content_type($fullPath);
            $bin = @file_get_contents($fullPath);
            if (! is_string($bin) || $bin === '') {
                continue;
            }

            $journal['logo_data_uri'] = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($bin);
        }
        unset($journal);

        $requestStats = [
            'total' => (int) $requestModel->countAllResults(),
            'pending' => (int) $requestModel->whereIn('status', ['pending', 'revision'])->countAllResults(),
            'letters' => (int) $letterModel->countAllResults(),
        ];

        return view('public/home', [
            'title' => 'PLPI',
            'subtitle' => 'Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal',
            'latestRequests' => $latestRequests,
            'journalProfiles' => $journalProfiles,
            'requestStats' => $requestStats,
        ]);
    }
}
