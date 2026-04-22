<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;

class HomeController extends BaseController
{
    public function index()
    {
        $cache = service('cache');
        $requestModel = new LoaRequestModel();
        $letterModel = new LoaLetterModel();
        $journalModel = new JournalModel();

        $latestRequests = $cache->get('public_home_latest_requests_v1');
        if (! is_array($latestRequests)) {
            $latestRequests = $requestModel
                ->select('loa_requests.request_code, loa_requests.title, loa_requests.status, loa_requests.created_at')
                ->orderBy('loa_requests.id', 'DESC')
                ->findAll(5);
            $cache->save('public_home_latest_requests_v1', $latestRequests, 90);
        }

        $journalProfiles = $cache->get('public_home_journal_profiles_v2');
        if (! is_array($journalProfiles)) {
            $journalProfiles = $journalModel
                ->select('journals.id, journals.name, journals.code, journals.e_issn, journals.p_issn, journals.logo_path, journals.website_url, journals.updated_at, publishers.name as publisher_name')
                ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
                ->orderBy('journals.id', 'DESC')
                ->findAll(8);

            foreach ($journalProfiles as &$journal) {
                $logoPath = trim((string) ($journal['logo_path'] ?? ''));
                if ($logoPath === '' || empty($journal['id'])) {
                    $journal['logo_url'] = null;
                    continue;
                }

                $version = rawurlencode((string) ($journal['updated_at'] ?? $journal['id']));
                $journal['logo_url'] = site_url('journal-logo/' . (int) $journal['id']) . '?size=thumb&v=' . $version;
            }
            unset($journal);

            $cache->save('public_home_journal_profiles_v2', $journalProfiles, 300);
        }

        $requestStats = $cache->get('public_home_request_stats_v1');
        if (! is_array($requestStats)) {
            $requestStats = [
                'total' => (int) $requestModel->countAllResults(),
                'pending' => (int) $requestModel->whereIn('status', ['pending', 'revision'])->countAllResults(),
                'letters' => (int) $letterModel->countAllResults(),
            ];

            $cache->save('public_home_request_stats_v1', $requestStats, 90);
        }

        $isAdminLoggedIn = (bool) session('isLoggedIn')
            && in_array((string) session('role'), ['superadmin', 'admin_jurnal'], true);

        return view('public/home', [
            'title' => 'PLPI',
            'subtitle' => 'Pusat Layanan Publikasi Ilmiah',
            'latestRequests' => $latestRequests,
            'journalProfiles' => $journalProfiles,
            'requestStats' => $requestStats,
            'adminNavUrl' => $isAdminLoggedIn ? site_url('dashboard') : site_url('login'),
            'adminNavLabel' => $isAdminLoggedIn ? 'Kembali ke Dashboard' : 'Login Admin',
            'adminNavIcon' => $isAdminLoggedIn ? 'bi-arrow-left-circle' : 'bi-box-arrow-in-right',
        ]);
    }

    public function journalLogo(int $id)
    {
        $journal = (new JournalModel())
            ->select('id, logo_path')
            ->find($id);
        if (! is_array($journal)) {
            return $this->response->setStatusCode(404);
        }

        $logoPath = trim((string) ($journal['logo_path'] ?? ''));
        if ($logoPath === '') {
            return $this->response->setStatusCode(404);
        }

        $absolutePath = WRITEPATH . 'uploads/' . ltrim($logoPath, '/\\');
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return $this->response->setStatusCode(404);
        }

        $size = strtolower(trim((string) $this->request->getGet('size')));
        if ($size === 'thumb') {
            $thumbAbsolutePath = $this->resolveLogoThumbPath($absolutePath);
            if ($thumbAbsolutePath !== null && ! is_file($thumbAbsolutePath)) {
                $this->generateThumbFromSource($absolutePath, $thumbAbsolutePath, 300, 400);
            }
            if ($thumbAbsolutePath !== null && is_file($thumbAbsolutePath) && is_readable($thumbAbsolutePath)) {
                $absolutePath = $thumbAbsolutePath;
            }
        }

        $mtime = (int) @filemtime($absolutePath);
        $size = (int) @filesize($absolutePath);
        $etag = '"' . sha1((string) $mtime . '|' . (string) $size . '|' . $absolutePath) . '"';
        $lastModified = gmdate('D, d M Y H:i:s', $mtime > 0 ? $mtime : time()) . ' GMT';
        $ifNoneMatch = trim((string) $this->request->getHeaderLine('If-None-Match'));

        if ($ifNoneMatch !== '' && $ifNoneMatch === $etag) {
            return $this->response
                ->setStatusCode(304)
                ->setHeader('Cache-Control', 'public, max-age=86400')
                ->setHeader('ETag', $etag)
                ->setHeader('Last-Modified', $lastModified);
        }

        $binary = @file_get_contents($absolutePath);
        if (! is_string($binary) || $binary === '') {
            return $this->response->setStatusCode(404);
        }

        $mime = @mime_content_type($absolutePath);
        if (! is_string($mime) || $mime === '') {
            $mime = 'image/png';
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setHeader('ETag', $etag)
            ->setHeader('Last-Modified', $lastModified)
            ->setBody($binary);
    }

    private function resolveLogoThumbPath(string $fullAbsolutePath): ?string
    {
        $dir = dirname($fullAbsolutePath);
        $file = pathinfo($fullAbsolutePath, PATHINFO_FILENAME);
        if ($dir === '' || $file === '') {
            return null;
        }

        return $dir . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $file . '-thumb.png';
    }

    private function generateThumbFromSource(string $sourcePath, string $targetPath, int $targetW, int $targetH): void
    {
        $meta = @getimagesize($sourcePath);
        if (! is_array($meta) || count($meta) < 3) {
            return;
        }

        $srcW = (int) ($meta[0] ?? 0);
        $srcH = (int) ($meta[1] ?? 0);
        $imageType = (int) ($meta[2] ?? 0);
        if ($srcW <= 0 || $srcH <= 0) {
            return;
        }

        $source = null;
        if ($imageType === IMAGETYPE_JPEG) {
            $source = @imagecreatefromjpeg($sourcePath);
        } elseif ($imageType === IMAGETYPE_PNG) {
            $source = @imagecreatefrompng($sourcePath);
        } elseif ($imageType === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            $source = @imagecreatefromwebp($sourcePath);
        }

        if (! $source) {
            return;
        }

        $canvas = imagecreatetruecolor($targetW, $targetH);
        if (! $canvas) {
            imagedestroy($source);
            return;
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

        $targetDir = dirname($targetPath);
        if (! is_dir($targetDir) && ! @mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
            imagedestroy($source);
            imagedestroy($canvas);
            return;
        }

        @imagepng($canvas, $targetPath, 8);

        imagedestroy($source);
        imagedestroy($canvas);
    }
}
