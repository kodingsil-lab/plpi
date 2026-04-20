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

        $row = $this->normalizeLetter($row);

        return view('admin/loa_letters/edit', [
            'title' => 'Edit LoA',
            'row' => $row,
            'journals' => (new JournalModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $rules = [
            'journal_id' => 'required|is_natural_no_zero',
            'title' => 'required|string|max_length[255]',
            'article_url' => 'permit_empty|valid_url|max_length[500]',
            'corresponding_email' => 'required|valid_email|max_length[255]',
            'volume' => 'permit_empty|max_length[50]',
            'issue_number' => 'permit_empty|max_length[50]',
            'published_year' => 'permit_empty|regex_match[/^[0-9]{4}$/]',
            'authors_text' => 'required|string|min_length[3]|max_length[5000]',
            'affiliations_text' => 'permit_empty|string|max_length[5000]',
            'status' => 'required|in_list[published,revoked]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form edit LoA.');
        }

        $v = $this->validator->getValidated();
        $journalId = (int) ($v['journal_id'] ?? 0);
        if ($journalId <= 0 || ! (new JournalModel())->find($journalId)) {
            return redirect()->back()->withInput()->with('error', 'Silakan pilih jurnal yang valid.');
        }

        [$slug, $articleId] = $this->parseOjsArticleUrl((string) ($v['article_url'] ?? ''));

        $authors = array_values(array_filter(array_map(
            static fn($line) => trim((string) $line),
            preg_split("/\r\n|\n|\r/", trim((string) $v['authors_text'])) ?: []
        )));
        $authorsJson = array_map(static fn($name) => ['name' => $name], $authors);

        $affiliationsJson = null;
        if (! empty($v['affiliations_text'])) {
            $affiliations = array_values(array_filter(array_map(
                static fn($line) => trim((string) $line),
                preg_split("/\r\n|\n|\r/", trim((string) $v['affiliations_text'])) ?: []
            )));
            $affiliationsJson = $affiliations === [] ? null : $affiliations;
        }

        (new LoaLetterModel())->update($id, [
            'journal_id' => $journalId,
            'title' => $v['title'],
            'article_url' => $v['article_url'] ?? null,
            'article_id_external' => $articleId,
            'corresponding_email' => $v['corresponding_email'],
            'volume' => $v['volume'] ?? null,
            'issue_number' => $v['issue_number'] ?? null,
            'published_year' => $v['published_year'] ?? null,
            'authors_json' => json_encode($authorsJson, JSON_UNESCAPED_UNICODE),
            'affiliations_json' => $affiliationsJson ? json_encode($affiliationsJson, JSON_UNESCAPED_UNICODE) : null,
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
            if (! isset($letter[$field]) || $letter[$field] === null) {
                $letter[$field] = [];
            }
        }

        $authorLines = [];
        foreach ((array) $letter['authors_json'] as $author) {
            if (is_array($author) && isset($author['name'])) {
                $authorLines[] = trim((string) $author['name']);
            } elseif (is_string($author)) {
                $authorLines[] = trim($author);
            }
        }
        $letter['authors_text'] = implode("\n", array_filter($authorLines, static fn($value) => $value !== ''));

        $affLines = [];
        foreach ((array) $letter['affiliations_json'] as $aff) {
            if (is_string($aff)) {
                $affLines[] = trim($aff);
            } elseif (is_array($aff) && isset($aff['affiliation'])) {
                $affLines[] = trim((string) $aff['affiliation']);
            }
        }
        $letter['affiliations_text'] = implode("\n", array_filter($affLines, static fn($value) => $value !== ''));

        return $letter;
    }

    private function parseOjsArticleUrl(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if (preg_match('#/index\.php/([^/]+)/article/view/(\d+)#', $path, $m)) {
            return [$m[1], $m[2]];
        }

        if (preg_match('#/([^/]+)/article/view/(\d+)#', $path, $m)) {
            return [$m[1], $m[2]];
        }

        return [null, null];
    }
}
