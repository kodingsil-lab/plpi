<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\LoaPdfService;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;

class LoaLetterController extends BaseController
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

        $model = new LoaLetterModel();
        $builder = $model
            ->select('loa_letters.*, journals.name as journal_name')
            ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
            ->orderBy('loa_letters.id', 'DESC');

        if ($status !== '') {
            $builder->where('loa_letters.status', $status);
        }
        if ($journalId > 0) {
            $builder->where('loa_letters.journal_id', $journalId);
        }
        if ($q !== '') {
            $builder->groupStart()
                ->like('loa_letters.loa_number', $q)
                ->orLike('loa_letters.title', $q)
                ->groupEnd();
        }

        return view('admin/loa_letters/index', [
            'title' => 'LoA Terbit',
            'rows' => $builder->paginate($perPage),
            'journals' => (new JournalModel())->orderBy('name', 'ASC')->findAll(),
            'filters' => ['status' => $status, 'journal_id' => $journalId, 'q' => $q],
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function edit(int $id)
    {
        $row = (new LoaLetterModel())
            ->select('loa_letters.*, journals.name as journal_name')
            ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
            ->where('loa_letters.id', $id)
            ->first();
        if (! $row) {
            return redirect()->to(site_url('admin/loa-letters'))->with('error', 'LoA tidak ditemukan.');
        }

        return view('admin/loa_letters/edit', ['title' => 'Edit LoA', 'row' => $row]);
    }

    public function update(int $id)
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'article_url' => 'permit_empty|valid_url|max_length[500]',
            'corresponding_email' => 'permit_empty|valid_email|max_length[255]',
            'status' => 'required|in_list[published,revoked]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form edit LoA.');
        }

        $v = $this->validator->getValidated();
        (new LoaLetterModel())->update($id, [
            'title' => $v['title'],
            'article_url' => $v['article_url'] ?? null,
            'corresponding_email' => $v['corresponding_email'] ?? null,
            'status' => $v['status'],
            'revoked_at' => ($v['status'] === 'revoked') ? date('Y-m-d H:i:s') : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "LoA #{$id} berhasil diperbarui.");
    }

    public function regenerate(int $id)
    {
        $letter = (new LoaLetterModel())->find($id);
        if (! $letter) {
            return redirect()->back()->with('error', 'LoA tidak ditemukan.');
        }

        $pdfPath = (new LoaPdfService())->generate($this->normalizeLetter($letter));
        (new LoaLetterModel())->update($id, [
            'pdf_path' => $pdfPath,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', "PDF LoA #{$id} berhasil dibuat ulang.");
    }

    public function destroy(int $id)
    {
        $model = new LoaLetterModel();
        $letter = $model->find($id);
        if (! $letter) {
            return redirect()->back()->with('error', 'LoA tidak ditemukan.');
        }

        $pdfPath = (string) ($letter['pdf_path'] ?? '');
        if ($pdfPath !== '') {
            $abs = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');
            if (is_file($abs)) {
                @unlink($abs);
            }
        }

        if (! empty($letter['loa_request_id'])) {
            (new LoaRequestModel())->update((int) $letter['loa_request_id'], [
                'status' => 'pending',
                'approved_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $model->delete($id);
        return redirect()->back()->with('success', "LoA #{$id} berhasil dihapus.");
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $model = new LoaLetterModel();
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }
            $letter = $model->find($id);
            if (! $letter) {
                continue;
            }

            $pdfPath = (string) ($letter['pdf_path'] ?? '');
            if ($pdfPath !== '') {
                $abs = WRITEPATH . 'uploads/' . ltrim($pdfPath, '/');
                if (is_file($abs)) {
                    @unlink($abs);
                }
            }

            if (! empty($letter['loa_request_id'])) {
                (new LoaRequestModel())->update((int) $letter['loa_request_id'], [
                    'status' => 'pending',
                    'approved_at' => null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $model->delete($id);
        }

        return redirect()->back()->with('success', 'LoA terpilih berhasil dihapus.');
    }

    public function exportCsv()
    {
        $rows = (new LoaLetterModel())->orderBy('id', 'DESC')->findAll(5000);
        $filename = 'loa-letters-' . date('Ymd-His') . '.csv';

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['loa_number', 'title', 'status', 'published_at', 'public_token']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['loa_number'] ?? '',
                $r['title'] ?? '',
                $r['status'] ?? '',
                $r['published_at'] ?? '',
                $r['public_token'] ?? '',
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
