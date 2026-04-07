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

        $pending = $requestModel->where('status', 'pending')->countAllResults();
        $approved = $requestModel->where('status', 'approved')->countAllResults();
        $rejected = $requestModel->where('status', 'rejected')->countAllResults();
        $published = $letterModel->where('status', 'published')->countAllResults();

        $latest = $requestModel
            ->select('loa_requests.*, journals.name as journal_name')
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
