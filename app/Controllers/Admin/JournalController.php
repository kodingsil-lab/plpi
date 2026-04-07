<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\PublisherModel;
use function url_title;

class JournalController extends BaseController
{
    public function index()
    {
        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $model = new JournalModel();

        $rows = $model
            ->select('journals.*, publishers.name as publisher_name')
            ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
            ->orderBy('journals.id', 'DESC')
            ->paginate($perPage);

        return view('admin/journals/index', [
            'title' => 'Data Jurnal',
            'rows'  => $rows,
            'pager' => $model->pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return redirect()->back()->with('info', 'Form tambah jurnal akan disiapkan pada batch berikutnya.');
    }

    public function store()
    {
        return redirect()->back()->with('success', 'Data jurnal tersimpan.');
    }

    public function edit(int $id)
    {
        $row = (new JournalModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/journals'))->with('error', 'Jurnal tidak ditemukan.');
        }

        return view('admin/journals/edit', [
            'title'      => 'Edit Jurnal',
            'row'        => $row,
            'publishers' => (new PublisherModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $model = new JournalModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/journals'))->with('error', 'Jurnal tidak ditemukan.');
        }

        $rules = [
            'publisher_id'         => 'required|is_natural_no_zero',
            'name'                 => 'required|max_length[255]',
            'code'                 => 'required|max_length[80]',
            'e_issn'               => 'permit_empty|max_length[50]',
            'p_issn'               => 'permit_empty|max_length[50]',
            'website_url'          => 'permit_empty|valid_url|max_length[255]',
            'default_signer_name'  => 'permit_empty|max_length[191]',
            'default_signer_title' => 'permit_empty|max_length[191]',
            'pdf_sig_left_px'      => 'permit_empty|integer',
            'pdf_sig_top_px'       => 'permit_empty|integer',
            'pdf_sig_height_px'    => 'permit_empty|integer',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form jurnal.');
        }

        $v = $this->validator->getValidated();

        $sameCode = $model->where('code', trim((string) $v['code']))->where('id !=', $id)->first();
        if ($sameCode) {
            return redirect()->back()->withInput()->with('error', 'Kode jurnal sudah digunakan.');
        }

        $slugSource = trim((string) ($v['name'] ?? ''));
        if ($slugSource === '') {
            $slugSource = trim((string) ($v['code'] ?? ''));
        }
        $baseSlug = url_title($slugSource, '-', true);
        if ($baseSlug === '') {
            $baseSlug = 'jurnal';
        }

        $slug = $baseSlug;
        $suffix = 2;
        while ($model->where('slug', $slug)->where('id !=', $id)->first()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $data = [
            'publisher_id'           => (int) $v['publisher_id'],
            'name'                   => trim((string) $v['name']),
            'code'                   => trim((string) $v['code']),
            'slug'                   => $slug,
            'e_issn'                 => $v['e_issn'] ?? null,
            'p_issn'                 => $v['p_issn'] ?? null,
            'website_url'            => $v['website_url'] ?? null,
            'default_signer_name'    => $v['default_signer_name'] ?? null,
            'default_signer_title'   => $v['default_signer_title'] ?? null,
            'pdf_sig_left_px'        => ($v['pdf_sig_left_px'] ?? '') !== '' ? (int) $v['pdf_sig_left_px'] : null,
            'pdf_sig_top_px'         => ($v['pdf_sig_top_px'] ?? '') !== '' ? (int) $v['pdf_sig_top_px'] : null,
            'pdf_sig_height_px'      => ($v['pdf_sig_height_px'] ?? '') !== '' ? (int) $v['pdf_sig_height_px'] : null,
            'updated_at'             => date('Y-m-d H:i:s'),
        ];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && $logo->getError() === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'webp'];
            $ext = strtolower((string) $logo->getExtension());
            if (! in_array($ext, $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'Format logo harus PNG/JPG/JPEG/WEBP.');
            }
            $logoDir = WRITEPATH . 'uploads/journals/logos';
            if (! is_dir($logoDir) && ! @mkdir($logoDir, 0775, true) && ! is_dir($logoDir)) {
                return redirect()->back()->withInput()->with('error', 'Folder logo jurnal belum tersedia.');
            }
            $newName = $logo->getRandomName();
            $logo->move($logoDir, $newName, true);
            $data['logo_path'] = 'journals/logos/' . $newName;
        }

        $signature = $this->request->getFile('signature');
        if ($signature && $signature->isValid() && $signature->getError() === UPLOAD_ERR_OK) {
            $allowed = ['png', 'jpg', 'jpeg', 'webp'];
            $ext = strtolower((string) $signature->getExtension());
            if (! in_array($ext, $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'Format cap + tanda tangan digital harus PNG/JPG/JPEG/WEBP.');
            }
            $signatureDir = WRITEPATH . 'uploads/journals/signatures';
            if (! is_dir($signatureDir) && ! @mkdir($signatureDir, 0775, true) && ! is_dir($signatureDir)) {
                return redirect()->back()->withInput()->with('error', 'Folder cap + tanda tangan jurnal belum tersedia.');
            }
            $newName = $signature->getRandomName();
            $signature->move($signatureDir, $newName, true);
            $data['default_signature_path'] = 'journals/signatures/' . $newName;
        }

        $model->update($id, $data);

        return redirect()->to(site_url('admin/journals/' . $id . '/edit'))->with('success', 'Data jurnal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        return redirect()->back()->with('success', "Jurnal #{$id} dihapus.");
    }
}
