<?php

if (!function_exists('parse_dotenv_lines')) {
    /**
     * @return array<string, string>
     */
    function parse_dotenv_lines(array $lines): array
    {
        $vars = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key, " \t\r\n\0\x0B");
            $value = trim($value, " \t\r\n\0\x0B");
            if (str_starts_with($key, "\xEF\xBB\xBF")) {
                $key = substr($key, 3);
            }
            if ($key === '') {
                continue;
            }
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }
            $vars[$key] = $value;
        }
        return $vars;
    }
}

if (!function_exists('dotenv_file_paths')) {
    /**
     * @return list<string>
     */
    function dotenv_file_paths(): array
    {
        $srcRoot = dirname(__DIR__, 2);
        return [
            $srcRoot . '/.env',
            dirname($srcRoot) . '/.env',
        ];
    }
}

if (!function_exists('read_env_file_lines')) {
    /**
     * @return list<string>
     */
    function read_env_file_lines(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }
        if (str_starts_with($raw, "\xFF\xFE")) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
        } elseif (str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-16BE');
        }
        $raw = str_replace("\0", '', $raw);
        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        return explode("\n", $raw);
    }
}

if (!function_exists('dotenv_vars_from_disk')) {
    /**
     * @return array<string, string>
     */
    function dotenv_vars_from_disk(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $cache = [];
        foreach (dotenv_file_paths() as $path) {
            if (!is_readable($path)) {
                continue;
            }
            $cache = parse_dotenv_lines(read_env_file_lines($path));
            return $cache;
        }
        return $cache;
    }
}

if (!function_exists('load_dotenv_file')) {
    function load_dotenv_file(array $paths): void
    {
        foreach ($paths as $path) {
            if (!is_readable($path)) {
                continue;
            }
            foreach (parse_dotenv_lines(read_env_file_lines($path)) as $key => $value) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
            return;
        }
    }
}

if (!function_exists('resend_env')) {
    function resend_env(string $key, string $default = ''): string
    {
        $vars = dotenv_vars_from_disk();
        if (isset($vars[$key]) && trim((string) $vars[$key]) !== '') {
            return trim((string) $vars[$key]);
        }

        $g = getenv($key);
        if ($g !== false && trim((string) $g) !== '') {
            return trim((string) $g);
        }

        if (function_exists('apache_getenv')) {
            $a = apache_getenv($key, true);
            if ($a !== false && trim((string) $a) !== '') {
                return trim((string) $a);
            }
        }

        foreach ([$_ENV[$key] ?? null, $_SERVER[$key] ?? null] as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        if (defined($key)) {
            $const = constant($key);
            if (is_string($const) && trim($const) !== '') {
                return trim($const);
            }
        }

        return $default;
    }
}

if (!defined('RESEND_API_KEY')) {
    define('RESEND_API_KEY', resend_env('RESEND_API_KEY', ''));
}
if (!defined('RESEND_FROM_EMAIL')) {
    define('RESEND_FROM_EMAIL', resend_env('RESEND_FROM_EMAIL', 'notificaciones@automarket.com.pa'));
}
if (!defined('RESEND_FROM_NAME')) {
    define('RESEND_FROM_NAME', resend_env('RESEND_FROM_NAME', 'AM Gente notificaciones'));
}
