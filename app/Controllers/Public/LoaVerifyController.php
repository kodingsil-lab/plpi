<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\LoaLetterModel;
use App\Models\JournalModel;

class LoaVerifyController extends BaseController
{
    public function form()
    {
        return view('public/loa/verify', ['title' => 'Verifikasi LoA']);
    }

    public function submit()
    {
        $rules = [
            'number' => 'required|string|min_length[5]|max_length[100]|regex_match[/^[A-Za-z0-9\\/\\-. ]+$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('loa/verify'))->withInput()->with('error', 'Format Nomor LoA tidak valid.');
        }

        $number = strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->request->getPost('number'))));
        return redirect()->to(site_url('loa/verify/result') . '?number=' . urlencode($number));
    }

    public function result()
    {
        $number = strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->request->getGet('number'))));
        if ($number === '') {
            return redirect()->to(site_url('loa/verify'))->with('error', 'Nomor LoA wajib diisi.');
        }

        if (! preg_match('/^[A-Z0-9\/\-. ]{5,100}$/', $number)) {
            return redirect()->to(site_url('loa/verify'))->with('error', 'Format Nomor LoA tidak valid.');
        }

        $normalized = str_replace(' ', '', $number);
        $letter = (new LoaLetterModel())
            ->where("REPLACE(UPPER(loa_number), ' ', '') =", $normalized)
            ->where('status', 'published')
            ->first();

        $journal = null;
        if ($letter && ! empty($letter['journal_id'])) {
            $journal = (new JournalModel())->find((int) $letter['journal_id']);
        }

        return view('public/loa/verify_result', [
            'title' => 'Hasil Verifikasi',
            'number' => $number,
            'letter' => $letter,
            'journal' => $journal,
        ]);
    }
}
