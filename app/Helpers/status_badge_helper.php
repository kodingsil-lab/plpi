<?php

if (! function_exists('plpi_request_status_filter_options')) {
    function plpi_request_status_filter_options(): array
    {
        return [
            'menunggu' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'terbit' => 'Terbit',
            'ditolak' => 'Ditolak',
        ];
    }
}

if (! function_exists('plpi_request_status_meta')) {
    function plpi_request_status_meta(string $statusRaw, bool $hasPublishedLetter = false): array
    {
        $status = strtolower(trim($statusRaw));
        if ($status === '') {
            $status = 'pending';
        }

        if ($status === 'rejected') {
            return ['label' => 'Ditolak', 'class' => 'myletters-status-revision'];
        }
        if ($hasPublishedLetter) {
            return ['label' => 'Terbit', 'class' => 'myletters-status-issued'];
        }
        if ($status === 'approved') {
            return ['label' => 'Disetujui', 'class' => 'myletters-status-approved'];
        }

        return ['label' => 'Menunggu', 'class' => 'myletters-status-waiting'];
    }
}

if (! function_exists('plpi_letter_status_meta')) {
    function plpi_letter_status_meta(string $statusRaw): array
    {
        $status = strtolower(trim($statusRaw));
        if ($status === 'revoked') {
            return ['label' => 'Dicabut', 'class' => 'myletters-status-revision'];
        }

        return ['label' => 'Terbit', 'class' => 'myletters-status-issued'];
    }
}

if (! function_exists('plpi_notification_status_meta')) {
    function plpi_notification_status_meta(string $statusRaw): array
    {
        $status = strtolower(trim($statusRaw));

        $map = [
            'menunggu' => ['label' => 'Menunggu', 'class' => 'myletters-status-waiting'],
            '-' => ['label' => 'Menunggu', 'class' => 'myletters-status-waiting'],
            'notifikasi terkirim' => ['label' => 'Notifikasi Terkirim', 'class' => 'myletters-status-approved'],
            'gagal terkirim' => ['label' => 'Gagal Terkirim', 'class' => 'myletters-status-revision'],
        ];

        return $map[$status] ?? ['label' => ucfirst($statusRaw !== '' ? $statusRaw : 'Menunggu'), 'class' => 'myletters-status-waiting'];
    }
}

