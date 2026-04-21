<?php

use App\Models\AppSettingModel;

if (! function_exists('plpi_app_settings')) {
    function plpi_app_settings(): array
    {
        static $cached = null;

        if (is_array($cached)) {
            return $cached;
        }

        $cached = [];

        try {
            $db = db_connect();
            if (! $db->tableExists('app_settings')) {
                return $cached;
            }

            $row = (new AppSettingModel())->first();
            $cached = is_array($row) ? $row : [];
        } catch (Throwable $e) {
            $cached = [];
        }

        return $cached;
    }
}

if (! function_exists('plpi_app_setting')) {
    function plpi_app_setting(string $key, $default = null)
    {
        $settings = plpi_app_settings();

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('plpi_asset_url')) {
    function plpi_asset_url(?string $path, string $fallback = ''): string
    {
        $target = trim((string) $path);
        if ($target === '') {
            $target = $fallback;
        }

        return $target !== '' ? base_url($target) : '';
    }
}

if (! function_exists('plpi_asset_url_versioned')) {
    function plpi_asset_url_versioned(?string $path, string $fallback = ''): string
    {
        $target = trim((string) $path);
        if ($target === '') {
            $target = $fallback;
        }

        if ($target === '') {
            return '';
        }

        $absolutePath = FCPATH . ltrim($target, '/\\');
        $version = is_file($absolutePath) ? (string) filemtime($absolutePath) : '1';

        return base_url($target . '?v=' . $version);
    }
}

if (! function_exists('plpi_timezone_options')) {
    function plpi_timezone_options(): array
    {
        return [
            'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
            'Asia/Makassar' => 'Asia/Makassar (WITA)',
            'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
        ];
    }
}

if (! function_exists('plpi_apply_timezone_from_settings')) {
    function plpi_apply_timezone_from_settings(): void
    {
        $timezone = (string) plpi_app_setting('app_timezone', 'Asia/Jakarta');
        if (array_key_exists($timezone, plpi_timezone_options())) {
            date_default_timezone_set($timezone);
        }
    }
}
