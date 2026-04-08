<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaNotificationModel;
use App\Models\LoaRequestModel;

class LoaRequestController extends BaseController
{
    public function index()
    {
        $status = (string) $this->request->getGet('status');
        $journalId = (int) $this->request->getGet('journal_id');
        $q = trim((string) $this->request->getGet('q'));
        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $model = new LoaRequestModel();
        $builder = $model
            ->select("loa_requests.*, journals.name as journal_name, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->orderBy('loa_requests.id', 'DESC');

        if ($status !== '') {
            if ($status === 'menunggu') {
                $builder->whereIn('loa_requests.status', ['pending', 'revision']);
            } elseif ($status === 'disetujui') {
                $builder->where('loa_requests.status', 'approved');
                $builder->where("NOT EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false);
            } elseif ($status === 'terbit') {
                $builder->where("EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false);
            } elseif ($status === 'ditolak') {
                $builder->where('loa_requests.status', 'rejected');
            } else {
                $builder->where('loa_requests.status', $status);
            }
        }
        if ($journalId > 0) {
            $builder->where('loa_requests.journal_id', $journalId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('loa_requests.request_code', $q)
                ->orLike('loa_requests.title', $q)
                ->orLike('loa_requests.corresponding_email', $q)
                ->groupEnd();
        }

        return view('admin/loa_requests/index', [
            'title' => 'Permohonan LoA',
            'rows' => $builder->paginate($perPage),
            'journals' => (new JournalModel())->orderBy('name', 'ASC')->findAll(),
            'filters' => ['status' => $status, 'journal_id' => $journalId, 'q' => $q],
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function show(int $id)
    {
        $row = (new LoaRequestModel())
            ->select("loa_requests.*, journals.name as journal_name, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->where('loa_requests.id', $id)
            ->first();

        if (! $row) {
            return redirect()->to(site_url('admin/loa-requests'))->with('error', 'Data permohonan tidak ditemukan.');
        }

        return view('admin/loa_requests/show', ['title' => 'Detail Permohonan', 'row' => $row]);
    }

    public function approve(int $id)
    {
        $requestModel = new LoaRequestModel();
        $letterModel = new LoaLetterModel();
        $row = $requestModel->find($id);

        if (! $row) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
        if (! in_array((string) $row['status'], ['pending', 'revision'], true)) {
            return redirect()->back()->with('error', 'Status permohonan tidak valid untuk disetujui.');
        }

        $loaNumber = $this->generateLoaNumber((int) $row['journal_id']);
        $publicToken = bin2hex(random_bytes(16));
        $verificationHash = hash('sha256', $loaNumber . '|' . bin2hex(random_bytes(8)));
        $publishedAt = date('Y-m-d H:i:s');

        $letterModel->insert([
            'journal_id' => $row['journal_id'],
            'loa_request_id' => $row['id'],
            'loa_number' => $loaNumber,
            'article_url' => $row['article_url'] ?? '',
            'article_id_external' => $row['article_id_external'] ?? null,
            'title' => $row['title'],
            'authors_json' => $row['authors_json'] ?? null,
            'corresponding_email' => $row['corresponding_email'] ?? null,
            'affiliations_json' => $row['affiliations_json'] ?? null,
            'status' => 'published',
            'verification_hash' => $verificationHash,
            'public_token' => $publicToken,
            'published_at' => $publishedAt,
            'created_at' => $publishedAt,
            'updated_at' => $publishedAt,
        ]);
        $letterId = (int) $letterModel->getInsertID();

        $requestModel->update($id, [
            'status' => 'approved',
            'approved_at' => $publishedAt,
            'updated_at' => $publishedAt,
        ]);

        if ($letterId > 0) {
            $notifModel = new LoaNotificationModel();
            $exists = $notifModel->where('loa_letter_id', $letterId)->first();
            if (! $exists) {
                $notifModel->insert([
                    'loa_letter_id' => $letterId,
                    'status' => 'menunggu',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Permohonan berhasil disetujui dan LoA diterbitkan.');
    }

    public function reject(int $id)
    {
        $requestModel = new LoaRequestModel();
        $row = $requestModel->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
        if ((string) $row['status'] === 'approved') {
            return redirect()->back()->with('error', 'Permohonan yang sudah disetujui tidak bisa langsung ditolak.');
        }

        $requestModel->update($id, [
            'status' => 'rejected',
            'rejection_reason' => trim((string) ($this->request->getPost('rejection_reason') ?? 'Ditolak oleh admin.')),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to(site_url('admin/loa-requests'))->with('success', 'Permohonan berhasil ditolak.');
    }

    public function quickApprove(int $id)
    {
        return $this->approve($id);
    }

    public function exportCsv()
    {
        $rows = (new LoaRequestModel())->orderBy('id', 'DESC')->findAll(5000);
        $filename = 'loa-requests-' . date('Ymd-His') . '.csv';

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['request_code', 'title', 'corresponding_email', 'status', 'created_at']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['request_code'] ?? '',
                $r['title'] ?? '',
                $r['corresponding_email'] ?? '',
                $r['status'] ?? '',
                $r['created_at'] ?? '',
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out) ?: '';
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    private function generateLoaNumber(int $journalId): string
    {
        $db = \Config\Database::connect();
        $journal = $db->table('journals j')
            ->select('j.id, j.code as journal_code, j.publisher_id, p.code as publisher_code')
            ->join('publishers p', 'p.id = j.publisher_id', 'left')
            ->where('j.id', $journalId)
            ->get()
            ->getRowArray();

        $journalCode = $this->normalizeCodeSegment((string) ($journal['journal_code'] ?? ('JRN-' . $journalId)));
        $publisherCode = $this->normalizeCodeSegment((string) ($journal['publisher_code'] ?? ('PUB-' . (int) ($journal['publisher_id'] ?? 0))));
        $monthRoman = $this->monthToRoman((int) date('n'));
        $year = date('Y');

        $suffix = '/LoA/' . $journalCode . '/' . $publisherCode . '/' . $monthRoman . '/' . $year;
        $rows = (new LoaLetterModel())
            ->select('loa_number')
            ->like('loa_number', '/LoA/' . $journalCode . '/')
            ->like('loa_number', '/' . $year, 'before')
            ->findAll();

        $maxSeq = 0;
        foreach ($rows as $r) {
            $num = trim((string) ($r['loa_number'] ?? ''));
            if ($num === '' || ! str_ends_with($num, '/' . $year)) {
                continue;
            }

            $parts = explode('/', $num);
            if (count($parts) < 6) {
                continue;
            }
            if (strcasecmp((string) ($parts[1] ?? ''), 'LoA') !== 0) {
                continue;
            }
            if ((string) ($parts[2] ?? '') !== $journalCode) {
                continue;
            }

            $firstSegment = $parts[0] ?? '';
            if (ctype_digit($firstSegment)) {
                $maxSeq = max($maxSeq, (int) $firstSegment);
            }
        }

        $next = $maxSeq + 1;
        return str_pad((string) $next, 3, '0', STR_PAD_LEFT) . $suffix;
    }

    private function monthToRoman(int $month): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }

    private function normalizeCodeSegment(string $raw): string
    {
        $value = strtoupper(trim($raw));
        if ($value === '') {
            return 'NA';
        }

        $value = preg_replace('/[^A-Z0-9-]+/', '-', $value) ?? '';
        $value = preg_replace('/-+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'NA';
    }
}
