<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class ImportHostedLoa extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'plpi:import-hosted-loa';
    protected $description = 'Import published LoA data from a legacy hosting dump database into the current PLPI schema.';

    public function run(array $params)
    {
        $sourceDbName = (string) (CLI::getOption('source') ?? 'loaunisa_import');
        $destDb = Database::connect();
        $sourceDb = Database::connect($this->makeSourceConfig($sourceDbName), false);

        $rows = $sourceDb->table('loa_letters ll')
            ->select('
                ll.id as legacy_letter_id,
                ll.journal_id as legacy_journal_id,
                ll.loa_request_id as legacy_request_id,
                ll.article_url as letter_article_url,
                ll.article_id_external as letter_article_id_external,
                ll.title as letter_title,
                ll.authors_json as letter_authors_json,
                ll.corresponding_email as letter_corresponding_email,
                ll.affiliations_json as letter_affiliations_json,
                ll.volume as letter_volume,
                ll.issue_number as letter_issue_number,
                ll.published_year as letter_published_year,
                ll.status as letter_status,
                ll.verification_hash as letter_verification_hash,
                ll.public_token as letter_public_token,
                ll.pdf_path as legacy_pdf_path,
                ll.published_at as letter_published_at,
                ll.revoked_at as letter_revoked_at,
                ll.revoked_reason as letter_revoked_reason,
                ll.created_at as letter_created_at,
                ll.updated_at as letter_updated_at,
                lr.id as request_id,
                lr.journal_id as request_journal_id,
                lr.request_code as legacy_request_code,
                lr.article_url as request_article_url,
                lr.article_id_external as request_article_id_external,
                lr.volume as request_volume,
                lr.issue_number as request_issue_number,
                lr.published_year as request_published_year,
                lr.title as request_title,
                lr.authors_json as request_authors_json,
                lr.corresponding_email as request_corresponding_email,
                lr.affiliations_json as request_affiliations_json,
                lr.status as request_status,
                lr.notes_admin as request_notes_admin,
                lr.rejection_reason as request_rejection_reason,
                lr.approved_at as request_approved_at,
                lr.created_at as request_created_at,
                lr.updated_at as request_updated_at,
                j.id as journal_id,
                j.publisher_id as legacy_publisher_id,
                j.name as journal_name,
                j.code as journal_code,
                j.slug as journal_slug,
                j.issn as journal_issn,
                j.e_issn as journal_e_issn,
                j.p_issn as journal_p_issn,
                j.logo_path as journal_logo_path,
                j.website_url as journal_website_url,
                j.default_stamp_path as journal_default_stamp_path,
                j.default_signer_name as journal_default_signer_name,
                j.default_signer_position as journal_default_signer_position,
                j.default_signature_path as journal_default_signature_path,
                j.pdf_sig_left_px as journal_pdf_sig_left_px,
                j.pdf_sig_top_px as journal_pdf_sig_top_px,
                j.pdf_sig_height_px as journal_pdf_sig_height_px,
                j.created_at as journal_created_at,
                j.updated_at as journal_updated_at,
                p.id as publisher_id,
                p.name as publisher_name,
                p.code as publisher_code,
                p.logo_path as publisher_logo_path,
                p.address as publisher_address,
                p.email as publisher_email,
                p.phone as publisher_phone,
                p.created_at as publisher_created_at,
                p.updated_at as publisher_updated_at
            ')
            ->join('loa_requests lr', 'lr.id = ll.loa_request_id', 'inner')
            ->join('journals j', 'j.id = ll.journal_id', 'inner')
            ->join('publishers p', 'p.id = j.publisher_id', 'left')
            ->where('ll.status', 'published')
            ->orderBy('ll.published_at', 'ASC')
            ->orderBy('ll.id', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            CLI::write('Tidak ada data LoA published di database sumber.', 'yellow');
            return;
        }

        $destDb->transStart();

        $publisherMap = [];
        $journalMap = [];
        $requestMap = [];
        $insertedPublishers = 0;
        $insertedJournals = 0;
        $insertedRequests = 0;
        $insertedLetters = 0;
        $insertedNotifications = 0;
        $skippedLetters = 0;

        foreach ($rows as $row) {
            $legacyPublisherId = (int) ($row['publisher_id'] ?? 0);
            if ($legacyPublisherId > 0 && ! isset($publisherMap[$legacyPublisherId])) {
                $publisherMap[$legacyPublisherId] = $this->upsertPublisher($destDb, $row, $insertedPublishers);
            }

            $legacyJournalId = (int) ($row['journal_id'] ?? 0);
            if ($legacyJournalId > 0 && ! isset($journalMap[$legacyJournalId])) {
                $destPublisherId = $publisherMap[(int) ($row['legacy_publisher_id'] ?? 0)] ?? null;
                $journalMap[$legacyJournalId] = $this->upsertJournal($destDb, $row, $destPublisherId, $insertedJournals);
            }

            $legacyRequestId = (int) ($row['request_id'] ?? 0);
            if ($legacyRequestId > 0 && ! isset($requestMap[$legacyRequestId])) {
                $destJournalId = $journalMap[(int) ($row['request_journal_id'] ?? 0)] ?? null;
                $requestMap[$legacyRequestId] = $this->upsertRequest($destDb, $row, $destJournalId, $insertedRequests);
            }

            $destRequestId = $requestMap[(int) ($row['legacy_request_id'] ?? 0)] ?? null;
            $destJournalId = $journalMap[(int) ($row['legacy_journal_id'] ?? 0)] ?? null;

            if ($destRequestId === null || $destJournalId === null) {
                throw new \RuntimeException('Gagal memetakan request/journal untuk LoA legacy ID ' . (int) ($row['legacy_letter_id'] ?? 0));
            }

            $existingLetter = $destDb->table('loa_letters')
                ->where('loa_request_id', $destRequestId)
                ->where('status', 'published')
                ->get()
                ->getRowArray();

            if (is_array($existingLetter)) {
                $skippedLetters++;
                continue;
            }

            $publishedAt = $this->normalizeDateTime($row['letter_published_at'] ?? null);
            $createdAt = $this->normalizeDateTime($row['letter_created_at'] ?? null) ?? $publishedAt ?? date('Y-m-d H:i:s');
            $updatedAt = $this->normalizeDateTime($row['letter_updated_at'] ?? null) ?? $publishedAt ?? $createdAt;
            $loaNumber = $this->generateLoaNumber($destDb, $destJournalId, $publishedAt ?? $createdAt);
            $publicToken = trim((string) ($row['letter_public_token'] ?? '')) ?: bin2hex(random_bytes(16));
            $verificationHash = trim((string) ($row['letter_verification_hash'] ?? '')) ?: hash('sha256', $loaNumber . '|' . bin2hex(random_bytes(8)));

            $destDb->table('loa_letters')->insert([
                'journal_id' => $destJournalId,
                'loa_request_id' => $destRequestId,
                'loa_number' => $loaNumber,
                'article_url' => (string) ($row['letter_article_url'] ?: $row['request_article_url'] ?: ''),
                'article_id_external' => $row['letter_article_id_external'] ?: $row['request_article_id_external'] ?: null,
                'title' => (string) ($row['letter_title'] ?: $row['request_title'] ?: ''),
                'authors_json' => $this->normalizeJsonText($row['letter_authors_json'] ?: $row['request_authors_json'] ?: '[]'),
                'corresponding_email' => (string) ($row['letter_corresponding_email'] ?: $row['request_corresponding_email'] ?: ''),
                'affiliations_json' => $this->normalizeNullableJsonText($row['letter_affiliations_json'] ?: $row['request_affiliations_json'] ?: null),
                'volume' => $this->nullableString($row['letter_volume'] ?: $row['request_volume'] ?: null),
                'issue_number' => $this->nullableString($row['letter_issue_number'] ?: $row['request_issue_number'] ?: null),
                'published_year' => $this->normalizeYear($row['letter_published_year'] ?: $row['request_published_year'] ?: null),
                'status' => 'published',
                'verification_hash' => $verificationHash,
                'public_token' => $publicToken,
                // PDF lama dari hosting tidak ikut disalin; file akan diregenerasi saat pertama dibuka.
                'pdf_path' => null,
                'published_at' => $publishedAt,
                'revoked_at' => null,
                'revoked_reason' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            $letterId = (int) $destDb->insertID();
            $insertedLetters++;

            $existingNotification = $destDb->table('loa_notifications')
                ->where('loa_letter_id', $letterId)
                ->get()
                ->getRowArray();

            if (! is_array($existingNotification)) {
                $destDb->table('loa_notifications')->insert([
                    'loa_letter_id' => $letterId,
                    'status' => 'menunggu',
                    'sent_to_email' => null,
                    'sent_at' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
                $insertedNotifications++;
            }
        }

        $destDb->transComplete();

        if (! $destDb->transStatus()) {
            throw new \RuntimeException('Import gagal dan transaksi dibatalkan.');
        }

        CLI::write('Import selesai.', 'green');
        CLI::write('Publisher baru: ' . $insertedPublishers, 'green');
        CLI::write('Jurnal baru: ' . $insertedJournals, 'green');
        CLI::write('Request baru: ' . $insertedRequests, 'green');
        CLI::write('LoA baru: ' . $insertedLetters, 'green');
        CLI::write('Notifikasi baru: ' . $insertedNotifications, 'green');
        CLI::write('LoA dilewati: ' . $skippedLetters, 'yellow');
    }

    private function makeSourceConfig(string $database): array
    {
        $config = config('Database')->default;
        $config['database'] = $database;

        return $config;
    }

    private function upsertPublisher($db, array $row, int &$insertedPublishers): int
    {
        $code = trim((string) ($row['publisher_code'] ?? ''));
        if ($code === '') {
            throw new \RuntimeException('Publisher code kosong pada data legacy.');
        }

        $existing = $db->table('publishers')->where('code', $code)->get()->getRowArray();
        if (is_array($existing)) {
            return (int) $existing['id'];
        }

        $db->table('publishers')->insert([
            'code' => $code,
            'name' => (string) ($row['publisher_name'] ?? ''),
            'email' => $this->nullableString($row['publisher_email'] ?? null),
            'phone' => $this->nullableString($row['publisher_phone'] ?? null),
            'logo_path' => $this->nullableString($row['publisher_logo_path'] ?? null),
            'address' => $this->nullableString($row['publisher_address'] ?? null),
            'created_at' => $this->normalizeDateTime($row['publisher_created_at'] ?? null),
            'updated_at' => $this->normalizeDateTime($row['publisher_updated_at'] ?? null),
        ]);

        $insertedPublishers++;
        return (int) $db->insertID();
    }

    private function upsertJournal($db, array $row, ?int $publisherId, int &$insertedJournals): int
    {
        $code = trim((string) ($row['journal_code'] ?? ''));
        if ($code === '') {
            throw new \RuntimeException('Journal code kosong pada data legacy.');
        }
        if ($publisherId === null) {
            throw new \RuntimeException('Publisher jurnal tidak ditemukan untuk journal code ' . $code);
        }

        $existing = $db->table('journals')->where('code', $code)->get()->getRowArray();
        if (is_array($existing)) {
            return (int) $existing['id'];
        }

        $db->table('journals')->insert([
            'publisher_id' => $publisherId,
            'name' => (string) ($row['journal_name'] ?? ''),
            'code' => $code,
            'slug' => (string) ($row['journal_slug'] ?? strtolower($code)),
            'issn' => $this->nullableString($row['journal_issn'] ?? null),
            'e_issn' => $this->nullableString($row['journal_e_issn'] ?? null),
            'p_issn' => $this->nullableString($row['journal_p_issn'] ?? null),
            'logo_path' => $this->nullableString($row['journal_logo_path'] ?? null),
            'website_url' => $this->nullableString($row['journal_website_url'] ?? null),
            'default_stamp_path' => $this->nullableString($row['journal_default_stamp_path'] ?? null),
            'default_signer_name' => $this->nullableString($row['journal_default_signer_name'] ?? null),
            'default_signer_title' => $this->nullableString($row['journal_default_signer_position'] ?? null),
            'default_signature_path' => $this->nullableString($row['journal_default_signature_path'] ?? null),
            'pdf_sig_left_px' => $this->nullableInt($row['journal_pdf_sig_left_px'] ?? null),
            'pdf_sig_top_px' => $this->nullableInt($row['journal_pdf_sig_top_px'] ?? null),
            'pdf_sig_height_px' => $this->nullableInt($row['journal_pdf_sig_height_px'] ?? null),
            'created_at' => $this->normalizeDateTime($row['journal_created_at'] ?? null),
            'updated_at' => $this->normalizeDateTime($row['journal_updated_at'] ?? null),
        ]);

        $insertedJournals++;
        return (int) $db->insertID();
    }

    private function upsertRequest($db, array $row, ?int $journalId, int &$insertedRequests): int
    {
        if ($journalId === null) {
            throw new \RuntimeException('Journal request tidak ditemukan untuk request legacy ID ' . (int) ($row['request_id'] ?? 0));
        }

        $requestCode = $this->buildImportRequestCode((int) ($row['request_id'] ?? 0));
        $existing = $db->table('loa_requests')->where('request_code', $requestCode)->get()->getRowArray();
        if (is_array($existing)) {
            return (int) $existing['id'];
        }

        $approvedAt = $this->normalizeDateTime($row['request_approved_at'] ?? null)
            ?? $this->normalizeDateTime($row['letter_published_at'] ?? null)
            ?? date('Y-m-d H:i:s');
        $createdAt = $this->normalizeDateTime($row['request_created_at'] ?? null) ?? $approvedAt;
        $updatedAt = $this->normalizeDateTime($row['request_updated_at'] ?? null) ?? $approvedAt;

        $db->table('loa_requests')->insert([
            'journal_id' => $journalId,
            'request_code' => $requestCode,
            'article_url' => (string) ($row['request_article_url'] ?? ''),
            'article_id_external' => $this->nullableString($row['request_article_id_external'] ?? null),
            'title' => (string) ($row['request_title'] ?? ''),
            'authors_json' => $this->normalizeJsonText($row['request_authors_json'] ?? '[]'),
            'corresponding_email' => (string) ($row['request_corresponding_email'] ?? ''),
            'affiliations_json' => $this->normalizeNullableJsonText($row['request_affiliations_json'] ?? null),
            'volume' => $this->nullableString($row['request_volume'] ?? null),
            'issue_number' => $this->nullableString($row['request_issue_number'] ?? null),
            'published_year' => $this->normalizeYear($row['request_published_year'] ?? null),
            'status' => 'approved',
            'notes_admin' => $this->nullableString($row['request_notes_admin'] ?? null),
            'rejection_reason' => null,
            'approved_at' => $approvedAt,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ]);

        $insertedRequests++;
        return (int) $db->insertID();
    }

    private function generateLoaNumber($db, int $journalId, string $dateTime): string
    {
        $journal = $db->table('journals j')
            ->select('j.id, j.code as journal_code, j.publisher_id, p.code as publisher_code')
            ->join('publishers p', 'p.id = j.publisher_id', 'left')
            ->where('j.id', $journalId)
            ->get()
            ->getRowArray();

        if (! is_array($journal)) {
            throw new \RuntimeException('Jurnal tujuan tidak ditemukan untuk generate nomor LoA.');
        }

        $timestamp = strtotime($dateTime);
        if ($timestamp === false) {
            $timestamp = time();
        }

        $journalCode = $this->normalizeCodeSegment((string) ($journal['journal_code'] ?? ('JRN-' . $journalId)));
        $publisherCode = $this->normalizeCodeSegment((string) ($journal['publisher_code'] ?? ('PUB-' . (int) ($journal['publisher_id'] ?? 0))));
        $monthRoman = $this->monthToRoman((int) date('n', $timestamp));
        $year = date('Y', $timestamp);

        $rows = $db->table('loa_letters')
            ->select('loa_number')
            ->like('loa_number', '/LOA/' . $journalCode . '/')
            ->like('loa_number', '/' . $year, 'before')
            ->get()
            ->getResultArray();

        $maxSeq = 0;
        foreach ($rows as $existing) {
            $num = trim((string) ($existing['loa_number'] ?? ''));
            if ($num === '' || ! str_ends_with($num, '/' . $year)) {
                continue;
            }

            $parts = explode('/', $num);
            if (count($parts) < 6) {
                continue;
            }
            if (strcasecmp((string) ($parts[1] ?? ''), 'LOA') !== 0) {
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
        return str_pad((string) $next, 3, '0', STR_PAD_LEFT) . '/LOA/' . $journalCode . '/' . $publisherCode . '/' . $monthRoman . '/' . $year;
    }

    private function buildImportRequestCode(int $legacyRequestId): string
    {
        return 'PLPI-' . str_pad((string) $legacyRequestId, 5, '0', STR_PAD_LEFT);
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

    private function normalizeDateTime($value): ?string
    {
        $text = trim((string) $value);
        if ($text === '' || $text === '0000-00-00 00:00:00') {
            return null;
        }

        $timestamp = strtotime($text);
        return $timestamp === false ? null : date('Y-m-d H:i:s', $timestamp);
    }

    private function normalizeJsonText($value): string
    {
        $text = trim((string) $value);
        return $text !== '' ? $text : '[]';
    }

    private function normalizeNullableJsonText($value): ?string
    {
        $text = trim((string) $value);
        return $text !== '' ? $text : null;
    }

    private function normalizeYear($value): ?string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        return preg_match('/^\d{4}$/', $text) === 1 ? $text : substr($text, 0, 4);
    }

    private function nullableString($value): ?string
    {
        $text = trim((string) $value);
        return $text !== '' ? $text : null;
    }

    private function nullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }
}
