<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $requestModel = new LoaRequestModel();
        $letterModel = new LoaLetterModel();
        $db = \Config\Database::connect();

        $pending = $requestModel->whereIn('status', ['pending', 'revision'])->countAllResults();
        $approvedRow = $db->query("
            SELECT COUNT(*) AS total
            FROM loa_requests lr
            WHERE lr.status = 'approved'
              AND NOT EXISTS (
                  SELECT 1 FROM loa_letters ll
                  WHERE ll.loa_request_id = lr.id
                    AND ll.status = 'published'
              )
        ")->getRowArray();
        $approved = (int) ($approvedRow['total'] ?? 0);
        $rejected = $requestModel->where('status', 'rejected')->countAllResults();
        $published = $letterModel->where('status', 'published')->countAllResults();

        $latest = $requestModel
            ->select("loa_requests.*, journals.name as journal_name, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->orderBy('loa_requests.id', 'DESC')
            ->findAll(8);

        $data = [
            'title' => 'Dashboard PLPI',
            'subtitle' => 'Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal',
            'stats' => [
                'menunggu' => $pending,
                'disetujui' => $approved,
                'ditolak' => $rejected,
                'loa_terbit' => $published,
            ],
            'latest' => $latest,
        ];

        return view('admin/dashboard', $data);
    }
}
