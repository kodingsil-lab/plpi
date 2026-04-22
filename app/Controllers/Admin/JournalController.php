<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\PublisherModel;
use function url_title;

class JournalController extends BaseController
{
    private const LOGO_TARGET_WIDTH = 900;
    private const LOGO_TARGET_HEIGHT = 1200;
    private const LOGO_THUMB_WIDTH = 300;
    private const LOGO_THUMB_HEIGHT = 400;
    private const DEFAULT_PDF_SIG_LEFT = 20;
    private const DEFAULT_PDF_SIG_TOP = 10;
    private const DEFAULT_PDF_SIG_HEIGHT = 85;
    private const HOME_JOURNAL_CACHE_KEY = 'public_home_journal_profiles_v2';

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
        return view('admin/journals/edit', [
            'title'      => 'Tambah Jurnal',
            'row'        => null,
            'publishers' => (new PublisherModel())->orderBy('name', 'ASC')->findAll(),
            'pdfDefaults' => $this->getPdfDefaults(),
        ]);
    }

    public function store()
    {
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

        $model = new JournalModel();
        $v = $this->validator->getValidated();

        $code = trim((string) $v['code']);
        $sameCode = $model->where('code', $code)->first();
        if ($sameCode) {
            return redirect()->back()->withInput()->with('error', 'Kode jurnal sudah digunakan.');
        }

        $slugSource = trim((string) ($v['name'] ?? ''));
        if ($slugSource === '') {
            $slugSource = $code;
        }
        $baseSlug = url_title($slugSource, '-', true);
        if ($baseSlug === '') {
            $baseSlug = 'jurnal';
        }

        $slug = $baseSlug;
        $suffix = 2;
        while ($model->where('slug', $slug)->first()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $data = [
            'publisher_id'           => (int) $v['publisher_id'],
            'name'                   => trim((string) $v['name']),
            'code'                   => $code,
            'slug'                   => $slug,
            'e_issn'                 => $v['e_issn'] ?? null,
            'p_issn'                 => $v['p_issn'] ?? null,
            'website_url'            => $v['website_url'] ?? null,
            'default_signer_name'    => $v['default_signer_name'] ?? null,
            'default_signer_title'   => $v['default_signer_title'] ?? null,
            'pdf_sig_left_px'        => ($v['pdf_sig_left_px'] ?? '') !== '' ? (int) $v['pdf_sig_left_px'] : self::DEFAULT_PDF_SIG_LEFT,
            'pdf_sig_top_px'         => ($v['pdf_sig_top_px'] ?? '') !== '' ? (int) $v['pdf_sig_top_px'] : self::DEFAULT_PDF_SIG_TOP,
            'pdf_sig_height_px'      => ($v['pdf_sig_height_px'] ?? '') !== '' ? (int) $v['pdf_sig_height_px'] : self::DEFAULT_PDF_SIG_HEIGHT,
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
            $logoAbsPath = $logoDir . DIRECTORY_SEPARATOR . $newName;
            $normalizedPath = $this->standardizeLogoImage($logoAbsPath);
            if (! is_string($normalizedPath) || $normalizedPath === '') {
                return redirect()->back()->withInput()->with('error', 'Logo jurnal gagal diproses ke PNG transparan 900x1200.');
            }
            $this->createLogoThumbnail($normalizedPath);
            $data['logo_path'] = 'journals/logos/' . basename($normalizedPath);
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

        $model->insert($data);
        $this->invalidateHomeJournalCache();

        return redirect()->to(site_url('admin/journals'))->with('success', 'Data jurnal berhasil ditambahkan.');
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
            'pdfDefaults' => $this->getPdfDefaults(),
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
            'pdf_sig_left_px'        => ($v['pdf_sig_left_px'] ?? '') !== '' ? (int) $v['pdf_sig_left_px'] : self::DEFAULT_PDF_SIG_LEFT,
            'pdf_sig_top_px'         => ($v['pdf_sig_top_px'] ?? '') !== '' ? (int) $v['pdf_sig_top_px'] : self::DEFAULT_PDF_SIG_TOP,
            'pdf_sig_height_px'      => ($v['pdf_sig_height_px'] ?? '') !== '' ? (int) $v['pdf_sig_height_px'] : self::DEFAULT_PDF_SIG_HEIGHT,
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
            $logoAbsPath = $logoDir . DIRECTORY_SEPARATOR . $newName;
            $normalizedPath = $this->standardizeLogoImage($logoAbsPath);
            if (! is_string($normalizedPath) || $normalizedPath === '') {
                return redirect()->back()->withInput()->with('error', 'Logo jurnal gagal diproses ke PNG transparan 900x1200.');
            }
            $this->createLogoThumbnail($normalizedPath);
            $data['logo_path'] = 'journals/logos/' . basename($normalizedPath);
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
        $this->invalidateHomeJournalCache();

        return redirect()->to(site_url('admin/journals'))->with('success', 'Data jurnal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $model = new JournalModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('admin/journals'))->with('error', 'Jurnal tidak ditemukan.');
        }

        $model->delete($id);
        $this->invalidateHomeJournalCache();
        return redirect()->to(site_url('admin/journals'))->with('success', 'Jurnal berhasil dihapus.');
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $journalIds = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $journalIds[] = $id;
            }
        }
        $journalIds = array_values(array_unique($journalIds));

        if ($journalIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new JournalModel())->delete($journalIds);
        $this->invalidateHomeJournalCache();
        return redirect()->to(site_url('admin/journals'))->with('success', 'Jurnal terpilih berhasil dihapus.');
    }

    private function standardizeLogoImage(string $filePath): ?string
    {
        $meta = @getimagesize($filePath);
        if (! is_array($meta) || count($meta) < 3) {
            return null;
        }

        $srcW = (int) ($meta[0] ?? 0);
        $srcH = (int) ($meta[1] ?? 0);
        $imageType = (int) ($meta[2] ?? 0);
        if ($srcW <= 0 || $srcH <= 0) {
            return null;
        }

        $source = null;
        if ($imageType === IMAGETYPE_JPEG) {
            $source = @imagecreatefromjpeg($filePath);
        } elseif ($imageType === IMAGETYPE_PNG) {
            $source = @imagecreatefrompng($filePath);
        } elseif ($imageType === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            $source = @imagecreatefromwebp($filePath);
        }

        if (! $source) {
            return null;
        }

        $targetW = self::LOGO_TARGET_WIDTH;
        $targetH = self::LOGO_TARGET_HEIGHT;
        $canvas = imagecreatetruecolor($targetW, $targetH);
        if (! $canvas) {
            imagedestroy($source);
            return null;
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        $scale = min($targetW / $srcW, $targetH / $srcH);
        $drawW = max(1, (int) round($srcW * $scale));
        $drawH = max(1, (int) round($srcH * $scale));
        $offsetX = (int) floor(($targetW - $drawW) / 2);
        $offsetY = (int) floor(($targetH - $drawH) / 2);

        imagecopyresampled($canvas, $source, $offsetX, $offsetY, 0, 0, $drawW, $drawH, $srcW, $srcH);
        $pngPath = dirname($filePath) . DIRECTORY_SEPARATOR . pathinfo($filePath, PATHINFO_FILENAME) . '.png';
        $saved = imagepng($canvas, $pngPath, 6);

        imagedestroy($source);
        imagedestroy($canvas);

        if (! $saved) {
            return null;
        }

        if (realpath($filePath) !== realpath($pngPath) && is_file($filePath)) {
            @unlink($filePath);
        }

        return $pngPath;
    }

    private function createLogoThumbnail(string $sourcePath): ?string
    {
        if (! is_file($sourcePath) || ! is_readable($sourcePath)) {
            return null;
        }

        $meta = @getimagesize($sourcePath);
        if (! is_array($meta) || count($meta) < 3) {
            return null;
        }

        $srcW = (int) ($meta[0] ?? 0);
        $srcH = (int) ($meta[1] ?? 0);
        if ($srcW <= 0 || $srcH <= 0) {
            return null;
        }

        $source = $this->createImageResource($sourcePath, (int) ($meta[2] ?? 0));
        if (! $source) {
            return null;
        }

        $targetW = self::LOGO_THUMB_WIDTH;
        $targetH = self::LOGO_THUMB_HEIGHT;
        $canvas = imagecreatetruecolor($targetW, $targetH);
        if (! $canvas) {
            imagedestroy($source);
            return null;
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        $scale = min($targetW / $srcW, $targetH / $srcH);
        $drawW = max(1, (int) round($srcW * $scale));
        $drawH = max(1, (int) round($srcH * $scale));
        $offsetX = (int) floor(($targetW - $drawW) / 2);
        $offsetY = (int) floor(($targetH - $drawH) / 2);
        imagecopyresampled($canvas, $source, $offsetX, $offsetY, 0, 0, $drawW, $drawH, $srcW, $srcH);

        $thumbDir = dirname($sourcePath) . DIRECTORY_SEPARATOR . 'thumbs';
        if (! is_dir($thumbDir) && ! @mkdir($thumbDir, 0775, true) && ! is_dir($thumbDir)) {
            imagedestroy($source);
            imagedestroy($canvas);
            return null;
        }

        $thumbPath = $thumbDir . DIRECTORY_SEPARATOR . pathinfo($sourcePath, PATHINFO_FILENAME) . '-thumb.png';
        $saved = imagepng($canvas, $thumbPath, 8);

        imagedestroy($source);
        imagedestroy($canvas);

        return $saved ? $thumbPath : null;
    }

    private function createImageResource(string $path, int $imageType)
    {
        if ($imageType === IMAGETYPE_JPEG) {
            return @imagecreatefromjpeg($path);
        }
        if ($imageType === IMAGETYPE_PNG) {
            return @imagecreatefrompng($path);
        }
        if ($imageType === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($path);
        }

        return null;
    }

    private function getPdfDefaults(): array
    {
        return [
            'left' => self::DEFAULT_PDF_SIG_LEFT,
            'top' => self::DEFAULT_PDF_SIG_TOP,
            'height' => self::DEFAULT_PDF_SIG_HEIGHT,
        ];
    }

    private function invalidateHomeJournalCache(): void
    {
        service('cache')->delete(self::HOME_JOURNAL_CACHE_KEY);
    }
}
