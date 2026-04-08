<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PublisherJournalDummySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $publishers = [
            [
                'code' => 'UPT-UNISAP',
                'name' => 'UPT Publikasi dan Penerbitan Universitas San Pedro',
                'email' => 'info@ejurnal-unisap.ac.id',
                'phone' => '082213331314',
                'address' => 'Jalan Ir. Soekarno Nomor 06, Kelurahan Fontein, Kecamatan Kota Raja, Kota Kupang, Nusa Tenggara Timur 85112',
            ],
            [
                'code' => 'LPPM-NTT',
                'name' => 'LPPM Nusa Cendana Press',
                'email' => 'admin@lppmntt.or.id',
                'phone' => '081234567801',
                'address' => 'Jl. Adisucipto No. 12, Oesapa, Kota Kupang, NTT',
            ],
            [
                'code' => 'PUSLIT-MANDIRI',
                'name' => 'Pusat Riset Mandiri Nusantara',
                'email' => 'sekretariat@risetmandiri.id',
                'phone' => '081234567802',
                'address' => 'Jl. W.J. Lalamentik No. 89, Kota Kupang, NTT',
            ],
            [
                'code' => 'AKADEMIKA-TIMOR',
                'name' => 'Akademika Timor Publisher',
                'email' => 'office@akademikatimor.id',
                'phone' => '081234567803',
                'address' => 'Jl. Frans Seda No. 77, Kota Kupang, NTT',
            ],
            [
                'code' => 'SINTA-MEDIA',
                'name' => 'Sinta Media Ilmiah Indonesia',
                'email' => 'contact@sintamedia.id',
                'phone' => '081234567804',
                'address' => 'Jl. Timor Raya No. 18, Kota Kupang, NTT',
            ],
        ];

        $publisherTable = $this->db->table('publishers');
        $publisherIdByCode = [];

        foreach ($publishers as $publisher) {
            $existing = $publisherTable->where('code', $publisher['code'])->get()->getRowArray();
            $payload = $publisher;
            $payload['updated_at'] = $now;

            if ($existing) {
                $publisherTable->where('id', (int) $existing['id'])->update($payload);
                $publisherIdByCode[$publisher['code']] = (int) $existing['id'];
            } else {
                $payload['created_at'] = $now;
                $publisherTable->insert($payload);
                $publisherIdByCode[$publisher['code']] = (int) $this->db->insertID();
            }
        }

        $journals = [
            ['publisher_code' => 'UPT-UNISAP', 'code' => 'ABDIUNISAP', 'name' => 'Abdi Unisap: Jurnal Pengabdian Kepada Masyarakat', 'e_issn' => '2987-9175', 'p_issn' => '2987-9183', 'website_url' => 'https://ejurnal-unisap.ac.id/index.php/abdiunisap'],
            ['publisher_code' => 'UPT-UNISAP', 'code' => 'JIPM-UNISAP', 'name' => 'Jurnal Inovasi Pendidikan dan Manajemen', 'e_issn' => '2988-0823', 'p_issn' => '2988-0858', 'website_url' => 'https://ejurnal-unisap.ac.id/index.php/jipm'],
            ['publisher_code' => 'UPT-UNISAP', 'code' => 'JURNAL-HUKUM-SP', 'name' => 'Jurnal Hukum San Pedro', 'e_issn' => '3025-1516', 'p_issn' => '3025-1249', 'website_url' => 'https://ejurnal-unisap.ac.id/index.php/jhsp'],
            ['publisher_code' => 'UPT-UNISAP', 'code' => 'SAINS-TERAPAN-SP', 'name' => 'Jurnal Sains Terapan San Pedro', 'e_issn' => '3089-5111', 'p_issn' => '3089-512X', 'website_url' => 'https://ejurnal-unisap.ac.id/index.php/jsts'],
            ['publisher_code' => 'UPT-UNISAP', 'code' => 'EKONOMI-BISNIS-SP', 'name' => 'Jurnal Ekonomi dan Bisnis San Pedro', 'e_issn' => '3090-1100', 'p_issn' => '3090-1119', 'website_url' => 'https://ejurnal-unisap.ac.id/index.php/jebsp'],
            ['publisher_code' => 'LPPM-NTT', 'code' => 'AGRO-LPPM', 'name' => 'Jurnal Agro LPPM', 'e_issn' => '2776-1201', 'p_issn' => '2776-1198', 'website_url' => 'https://lppmntt.or.id/jurnal/agro'],
            ['publisher_code' => 'LPPM-NTT', 'code' => 'TEKNIK-LPPM', 'name' => 'Jurnal Teknik dan Rekayasa NTT', 'e_issn' => '2776-1309', 'p_issn' => '2776-1295', 'website_url' => 'https://lppmntt.or.id/jurnal/teknik'],
            ['publisher_code' => 'LPPM-NTT', 'code' => 'KESEHATAN-LPPM', 'name' => 'Jurnal Kesehatan Komunitas NTT', 'e_issn' => '2776-1406', 'p_issn' => '2776-1392', 'website_url' => 'https://lppmntt.or.id/jurnal/kesehatan'],
            ['publisher_code' => 'LPPM-NTT', 'code' => 'SOSHUM-LPPM', 'name' => 'Jurnal Sosial Humaniora Nusa', 'e_issn' => '2776-1503', 'p_issn' => '2776-149X', 'website_url' => 'https://lppmntt.or.id/jurnal/soshum'],
            ['publisher_code' => 'LPPM-NTT', 'code' => 'PARIWISATA-NTT', 'name' => 'Jurnal Pariwisata NTT', 'e_issn' => '2776-1600', 'p_issn' => '2776-1598', 'website_url' => 'https://lppmntt.or.id/jurnal/pariwisata'],
            ['publisher_code' => 'PUSLIT-MANDIRI', 'code' => 'MANDIRI-EDU', 'name' => 'Mandiri Journal of Education', 'e_issn' => '2808-2206', 'p_issn' => '2808-2192', 'website_url' => 'https://risetmandiri.id/journal/education'],
            ['publisher_code' => 'PUSLIT-MANDIRI', 'code' => 'MANDIRI-TECH', 'name' => 'Mandiri Journal of Technology', 'e_issn' => '2808-2303', 'p_issn' => '2808-229X', 'website_url' => 'https://risetmandiri.id/journal/technology'],
            ['publisher_code' => 'PUSLIT-MANDIRI', 'code' => 'MANDIRI-BIZ', 'name' => 'Mandiri Journal of Business and Policy', 'e_issn' => '2808-2400', 'p_issn' => '2808-2399', 'website_url' => 'https://risetmandiri.id/journal/business'],
            ['publisher_code' => 'PUSLIT-MANDIRI', 'code' => 'MANDIRI-LAW', 'name' => 'Mandiri Journal of Law and Governance', 'e_issn' => '2808-2508', 'p_issn' => '2808-2496', 'website_url' => 'https://risetmandiri.id/journal/law'],
            ['publisher_code' => 'PUSLIT-MANDIRI', 'code' => 'MANDIRI-HEALTH', 'name' => 'Mandiri Journal of Public Health', 'e_issn' => '2808-2605', 'p_issn' => '2808-2593', 'website_url' => 'https://risetmandiri.id/journal/health'],
            ['publisher_code' => 'AKADEMIKA-TIMOR', 'code' => 'AKT-EDU', 'name' => 'Akademika Timor: Education Review', 'e_issn' => '2830-1104', 'p_issn' => '2830-1090', 'website_url' => 'https://akademikatimor.id/journal/edu-review'],
            ['publisher_code' => 'AKADEMIKA-TIMOR', 'code' => 'AKT-SCI', 'name' => 'Akademika Timor: Science Frontier', 'e_issn' => '2830-1201', 'p_issn' => '2830-1198', 'website_url' => 'https://akademikatimor.id/journal/science'],
            ['publisher_code' => 'AKADEMIKA-TIMOR', 'code' => 'AKT-SOC', 'name' => 'Akademika Timor: Social Insight', 'e_issn' => '2830-1309', 'p_issn' => '2830-1295', 'website_url' => 'https://akademikatimor.id/journal/social'],
            ['publisher_code' => 'AKADEMIKA-TIMOR', 'code' => 'AKT-ECON', 'name' => 'Akademika Timor: Economics and Development', 'e_issn' => '2830-1406', 'p_issn' => '2830-1392', 'website_url' => 'https://akademikatimor.id/journal/economics'],
            ['publisher_code' => 'AKADEMIKA-TIMOR', 'code' => 'AKT-AGRI', 'name' => 'Akademika Timor: Agribusiness Review', 'e_issn' => '2830-1503', 'p_issn' => '2830-149X', 'website_url' => 'https://akademikatimor.id/journal/agribusiness'],
            ['publisher_code' => 'SINTA-MEDIA', 'code' => 'SMI-COMPUTE', 'name' => 'SMI Journal of Computing', 'e_issn' => '2855-1011', 'p_issn' => '2855-1003', 'website_url' => 'https://sintamedia.id/journal/computing'],
            ['publisher_code' => 'SINTA-MEDIA', 'code' => 'SMI-ACCOUNTING', 'name' => 'SMI Journal of Accounting and Finance', 'e_issn' => '2855-1119', 'p_issn' => '2855-1100', 'website_url' => 'https://sintamedia.id/journal/accounting'],
            ['publisher_code' => 'SINTA-MEDIA', 'code' => 'SMI-CIVIL', 'name' => 'SMI Journal of Civil Engineering', 'e_issn' => '2855-1216', 'p_issn' => '2855-1208', 'website_url' => 'https://sintamedia.id/journal/civil'],
            ['publisher_code' => 'SINTA-MEDIA', 'code' => 'SMI-MEDICAL', 'name' => 'SMI Journal of Medical Science', 'e_issn' => '2855-1313', 'p_issn' => '2855-1305', 'website_url' => 'https://sintamedia.id/journal/medical'],
            ['publisher_code' => 'SINTA-MEDIA', 'code' => 'SMI-LINGUISTIC', 'name' => 'SMI Journal of Language and Culture', 'e_issn' => '2855-1410', 'p_issn' => '2855-1402', 'website_url' => 'https://sintamedia.id/journal/language'],
        ];

        $journalTable = $this->db->table('journals');

        foreach ($journals as $journal) {
            $publisherCode = $journal['publisher_code'];
            if (! isset($publisherIdByCode[$publisherCode])) {
                continue;
            }

            $name = trim((string) $journal['name']);
            $slug = $this->createSlug($name);

            $payload = [
                'publisher_id' => $publisherIdByCode[$publisherCode],
                'name' => $name,
                'code' => trim((string) $journal['code']),
                'slug' => $slug,
                'e_issn' => $journal['e_issn'] ?? null,
                'p_issn' => $journal['p_issn'] ?? null,
                'website_url' => $journal['website_url'] ?? null,
                'default_signer_name' => 'Editor in Chief',
                'default_signer_title' => 'Ketua Dewan Redaksi',
                'pdf_sig_left_px' => 120,
                'pdf_sig_top_px' => 120,
                'pdf_sig_height_px' => 110,
                'updated_at' => $now,
            ];

            $existingByCode = $journalTable->where('code', $payload['code'])->get()->getRowArray();
            if ($existingByCode) {
                $journalTable->where('id', (int) $existingByCode['id'])->update($payload);
                continue;
            }

            $payload['slug'] = $this->generateUniqueSlug($slug);
            $payload['created_at'] = $now;
            $journalTable->insert($payload);
        }
    }

    private function createSlug(string $text): string
    {
        $slug = strtolower($text);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'jurnal';
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $suffix = 2;
        $table = $this->db->table('journals');

        while ($table->where('slug', $slug)->countAllResults() > 0) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
