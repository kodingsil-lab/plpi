<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\LoaRequestModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class LoaRequestController extends BaseController
{
    public function create()
    {
        $journals = (new JournalModel())->orderBy('name', 'ASC')->findAll();

        return view('public/loa/request', [
            'title' => 'Ajukan LoA',
            'journals' => $journals,
        ]);
    }

    public function store()
    {
        $rules = [
            'article_url'         => 'permit_empty|valid_url|max_length[500]',
            'journal_id'          => 'permit_empty|is_natural_no_zero',
            'title'               => 'required|string|max_length[255]',
            'corresponding_email' => 'required|valid_email|max_length[255]',
            'volume'              => 'permit_empty|max_length[50]',
            'issue_number'        => 'permit_empty|max_length[50]',
            'published_year'      => 'permit_empty|regex_match[/^[0-9]{4}$/]',
            'authors_text'        => 'required|string|min_length[3]|max_length[5000]',
            'affiliations_text'   => 'permit_empty|string|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data permohonan.');
        }

        $validated = $this->validator->getValidated();
        [$slug, $articleId] = $this->parseOjsArticleUrl((string) ($validated['article_url'] ?? ''));

        $journalModel = new JournalModel();
        $journalId = (int) ($validated['journal_id'] ?? 0);
        if ($journalId <= 0 && $slug) {
            $journal = $journalModel->where('slug', $slug)->first();
            if (is_array($journal)) {
                $journalId = (int) ($journal['id'] ?? 0);
            }
        }

        if ($journalId <= 0 || ! $journalModel->find($journalId)) {
            return redirect()->back()->withInput()->with('error', 'Silakan pilih jurnal terlebih dahulu.');
        }

        $authors = array_values(array_filter(array_map(
            static fn($line) => trim((string) $line),
            preg_split("/\r\n|\n|\r/", trim((string) $validated['authors_text'])) ?: []
        )));
        $authorsJson = array_map(static fn($name) => ['name' => $name], $authors);

        $affiliationsJson = null;
        if (! empty($validated['affiliations_text'])) {
            $affiliations = array_values(array_filter(array_map(
                static fn($line) => trim((string) $line),
                preg_split("/\r\n|\n|\r/", trim((string) $validated['affiliations_text'])) ?: []
            )));
            $affiliationsJson = $affiliations === [] ? null : $affiliations;
        }

        $requestModel = new LoaRequestModel();
        $requestCode = $this->generateRequestCode($requestModel);

        $requestModel->insert([
            'journal_id'          => $journalId,
            'request_code'        => $requestCode,
            'article_url'         => (string) ($validated['article_url'] ?? ''),
            'article_id_external' => $articleId,
            'title'               => (string) $validated['title'],
            'authors_json'        => json_encode($authorsJson, JSON_UNESCAPED_UNICODE),
            'corresponding_email' => (string) $validated['corresponding_email'],
            'affiliations_json'   => $affiliationsJson ? json_encode($affiliationsJson, JSON_UNESCAPED_UNICODE) : null,
            'volume'              => $validated['volume'] ?? null,
            'issue_number'        => $validated['issue_number'] ?? null,
            'published_year'      => $validated['published_year'] ?? null,
            'status'              => 'pending',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('loa/status/' . $requestCode))->with('success', 'Permohonan LoA berhasil dikirim.');
    }

    public function status(string $requestCode)
    {
        $row = (new LoaRequestModel())
            ->select('loa_requests.*, journals.name as journal_name')
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->where('loa_requests.request_code', $requestCode)
            ->first();

        if (! $row) {
            throw new PageNotFoundException('Permohonan tidak ditemukan.');
        }

        $row['authors_json'] = $this->decodeJsonField($row['authors_json'] ?? null);
        $row['affiliations_json'] = $this->decodeJsonField($row['affiliations_json'] ?? null);

        return view('public/loa/status', [
            'title' => 'Status LoA',
            'loaRequest' => $row,
        ]);
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

    private function decodeJsonField($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function generateRequestCode(LoaRequestModel $requestModel): string
    {
        $rows = $requestModel->select('request_code')->findAll();
        $maxSeq = 0;

        foreach ($rows as $row) {
            $code = trim((string) ($row['request_code'] ?? ''));
            if (! preg_match('/^(?:PLPI|IMP|MPL|REQ)-(\d+)$/', $code, $matches)) {
                continue;
            }

            $seq = (int) $matches[1];
            if ($seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        return 'PLPI-' . str_pad((string) ($maxSeq + 1), 5, '0', STR_PAD_LEFT);
    }
}
