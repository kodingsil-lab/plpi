<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LoaRequestDummySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $journalTable = $this->db->table('journals');
        $requestTable = $this->db->table('loa_requests');

        $journalA = $journalTable->where('code', 'ABDIUNISAP')->get()->getRowArray();
        $journalB = $journalTable->where('id !=', (int) ($journalA['id'] ?? 0))->orderBy('id', 'ASC')->get()->getRowArray();

        if (! $journalA) {
            $journalA = $journalTable->orderBy('id', 'ASC')->get()->getRowArray();
        }
        if (! $journalB) {
            $journalB = $journalA;
        }

        if (! $journalA) {
            return;
        }

        $rows = [
            [
                'request_code' => 'REQ-LOA-2026-0001',
                'journal_id' => (int) $journalA['id'],
                'article_url' => 'https://ejurnal-unisap.ac.id/index.php/abdiunisap/article/view/101',
                'article_id_external' => '101',
                'title' => 'Pemberdayaan Literasi Digital bagi Guru Sekolah Dasar di Kota Kupang',
                'authors_json' => json_encode([
                    ['name' => 'Konradus Silvester Jenahut', 'affiliation' => 'Universitas San Pedro'],
                    ['name' => 'Maria Theresia Bani', 'affiliation' => 'Universitas Nusa Cendana'],
                ], JSON_UNESCAPED_UNICODE),
                'corresponding_email' => 'silvesterjenahut@gmail.com',
                'affiliations_json' => json_encode([
                    'Universitas San Pedro',
                    'Universitas Nusa Cendana',
                ], JSON_UNESCAPED_UNICODE),
                'volume' => '5',
                'issue_number' => '2',
                'published_year' => '2026',
                'status' => 'pending',
                'notes_admin' => 'Menunggu verifikasi akhir dari admin jurnal.',
                'rejection_reason' => null,
                'approved_at' => null,
            ],
            [
                'request_code' => 'REQ-LOA-2026-0002',
                'journal_id' => (int) $journalB['id'],
                'article_url' => 'https://ejurnal-unisap.ac.id/index.php/abdiunisap/article/view/102',
                'article_id_external' => '102',
                'title' => 'Model Pendampingan UMKM Berbasis Data untuk Peningkatan Daya Saing Produk Lokal',
                'authors_json' => json_encode([
                    ['name' => 'Yohana Seran', 'affiliation' => 'UPT Publikasi dan Penerbitan Universitas San Pedro'],
                    ['name' => 'Benediktus Oematan', 'affiliation' => 'Politeknik Negeri Kupang'],
                    ['name' => 'Ester Nalle', 'affiliation' => 'Universitas San Pedro'],
                ], JSON_UNESCAPED_UNICODE),
                'corresponding_email' => 'yohana.seran@plpi.local',
                'affiliations_json' => json_encode([
                    'UPT Publikasi dan Penerbitan Universitas San Pedro',
                    'Politeknik Negeri Kupang',
                    'Universitas San Pedro',
                ], JSON_UNESCAPED_UNICODE),
                'volume' => '6',
                'issue_number' => '1',
                'published_year' => '2026',
                'status' => 'revision',
                'notes_admin' => 'Perlu perbaikan minor pada metadata penulis.',
                'rejection_reason' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($rows as $row) {
            $existing = $requestTable->where('request_code', $row['request_code'])->get()->getRowArray();
            $payload = $row;
            $payload['updated_at'] = $now;

            if ($existing) {
                $requestTable->where('id', (int) $existing['id'])->update($payload);
            } else {
                $payload['created_at'] = $now;
                $requestTable->insert($payload);
            }
        }
    }
}
