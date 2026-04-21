<?php

namespace App\Controllers\Admin\Settings;

use App\Controllers\BaseController;
use App\Models\AppSettingModel;

class ApplicationController extends BaseController
{
    public function index()
    {
        $row = (new AppSettingModel())->first();

        return view('admin/settings/application/index', [
            'title' => 'Aplikasi',
            'subtitle' => 'Pengaturan logo, favicon, dan zona waktu aplikasi.',
            'row' => is_array($row) ? $row : [],
            'timezoneOptions' => plpi_timezone_options(),
        ]);
    }

    public function update()
    {
        $model = new AppSettingModel();
        $row = $model->first();
        $rowId = (int) ($row['id'] ?? 0);

        $timezone = (string) $this->request->getPost('app_timezone');
        if (! array_key_exists($timezone, plpi_timezone_options())) {
            return redirect()->back()->withInput()->with('error', 'Zona waktu yang dipilih tidak valid.');
        }

        $payload = ['app_timezone' => $timezone];
        $fileMap = [
            'app_logo' => ['header_logo_path', 'login_logo_path', 'public_logo_path'],
            'favicon' => ['favicon_path'],
        ];

        foreach ($fileMap as $inputName => $fieldNames) {
            $file = $this->request->getFile($inputName);
            if (! $file || ! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = strtolower((string) $file->getExtension());
            $allowedExtensions = $inputName === 'favicon'
                ? ['ico', 'png', 'svg', 'webp']
                : ['png', 'jpg', 'jpeg', 'webp', 'svg'];

            if (! in_array($ext, $allowedExtensions, true)) {
                return redirect()->back()->withInput()->with('error', 'Format file untuk ' . str_replace('_', ' ', $inputName) . ' tidak didukung.');
            }

            $targetDir = FCPATH . 'uploads/app-settings';
            if (! is_dir($targetDir) && ! @mkdir($targetDir, 0775, true) && ! is_dir($targetDir)) {
                return redirect()->back()->withInput()->with('error', 'Folder upload pengaturan aplikasi belum tersedia.');
            }

            $newName = $inputName . '-' . $file->getRandomName();
            $file->move($targetDir, $newName, true);
            $storedPath = 'uploads/app-settings/' . $newName;
            foreach ($fieldNames as $fieldName) {
                $payload[$fieldName] = $storedPath;
            }

            if ($inputName === 'favicon') {
                $sourcePath = $targetDir . DIRECTORY_SEPARATOR . $newName;
                $publicFaviconPath = FCPATH . 'favicon.ico';
                $adminFaviconPath = FCPATH . 'unisap_favicon.ico';

                @copy($sourcePath, $publicFaviconPath);
                @copy($sourcePath, $adminFaviconPath);
            }
        }

        if ($rowId > 0) {
            $model->update($rowId, $payload);
        } else {
            $model->insert($payload);
        }

        return redirect()->to(site_url('superadmin/settings/application'))->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }
}
