<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('plpi_format_date')) {
    function plpi_format_date($value, bool $withTime = false): string
    {
        if ($value === null) {
            return '-';
        }

        $raw = trim((string) $value);
        if ($raw === '' || $raw === '-') {
            return '-';
        }

        $ts = strtotime($raw);
        if ($ts === false) {
            return '-';
        }

        return $withTime ? date('d-m-Y H:i:s', $ts) : date('d-m-Y', $ts);
    }
}

if (! function_exists('plpi_format_loa_number')) {
    function plpi_format_loa_number($value): string
    {
        $raw = trim((string) $value);
        if ($raw === '' || $raw === '-') {
            return '-';
        }

        return preg_replace_callback('/(?<=^|\/)(loa)(?=\/|$)/i', static function ($match) {
            return strtoupper($match[1]);
        }, $raw);
    }
}
