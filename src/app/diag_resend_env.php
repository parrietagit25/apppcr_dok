<?php
/**
 * Diagnóstico Resend — ejecutar dentro del contenedor:
 *   docker exec apppcr_php php /var/www/html/app/diag_resend_env.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo CLI');
}

$configPath = __DIR__ . '/../config/config.php';
if (is_readable($configPath)) {
    require_once $configPath;
}

$envPath = realpath(__DIR__ . '/../.env') ?: (__DIR__ . '/../.env');

/**
 * @return array<string, string>
 */
function diag_parse_env_file(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return [];
    }
    $raw = str_replace("\0", '', $raw);
    $raw = str_replace(["\r\n", "\r"], "\n", $raw);
    $vars = [];
    foreach (explode("\n", $raw) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key, " \t\r\n\0\x0B");
        $value = trim($value, " \t\r\n\0\x0B");
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

function diag_leer(string $key, string $default = ''): string
{
    if (function_exists('resend_env')) {
        $v = resend_env($key, $default);
        if (trim($v) !== '') {
            return $v;
        }
    }
    if (function_exists('cfg_env')) {
        $v = cfg_env($key, $default);
        if (trim($v) !== '') {
            return $v;
        }
    }
    $vars = diag_parse_env_file(realpath(__DIR__ . '/../.env') ?: (__DIR__ . '/../.env'));
    if (isset($vars[$key]) && trim($vars[$key]) !== '') {
        return trim($vars[$key]);
    }
    $g = getenv($key);
    if ($g !== false && trim((string) $g) !== '') {
        return trim((string) $g);
    }
    if (defined($key)) {
        $c = constant($key);
        if (is_string($c) && trim($c) !== '') {
            return trim($c);
        }
    }
    return $default;
}

echo "=== Diagnóstico Resend (apppcr) ===\n\n";
echo 'Fecha: ' . date('Y-m-d H:i:s') . "\n";
echo "config.php: {$configPath}\n";
echo 'resend_env(): ' . (function_exists('resend_env') ? 'sí' : 'NO — suba src/config/config.php actualizado') . "\n";
echo "Archivo .env: {$envPath}\n";
echo 'Existe: ' . (is_readable($envPath) ? 'sí' : 'no') . "\n\n";

if (is_readable($envPath)) {
    echo "--- grep RESEND (cat -A) ---\n";
    passthru('grep RESEND ' . escapeshellarg($envPath) . ' | cat -A');
    echo "\n";
}

$apiKey = diag_leer('RESEND_API_KEY', '');
$fromEmail = diag_leer('RESEND_FROM_EMAIL', '');
$fromName = diag_leer('RESEND_FROM_NAME', '');

echo "--- lectura efectiva ---\n";
echo 'RESEND_API_KEY len: ' . strlen($apiKey) . "\n";
echo 'RESEND_API_KEY prefix: ' . ($apiKey !== '' ? substr($apiKey, 0, 7) . '...' : '(vacío)') . "\n";
echo 'RESEND_FROM_EMAIL: ' . ($fromEmail !== '' ? $fromEmail : '(vacío)') . "\n";
echo 'RESEND_FROM_NAME: ' . ($fromName !== '' ? $fromName : '(vacío)') . "\n\n";

echo "--- getenv ---\n";
$g = getenv('RESEND_API_KEY');
echo 'getenv len: ' . ($g !== false ? strlen((string) $g) : 0) . "\n";

echo "\n--- constantes PHP ---\n";
echo 'RESEND_API_KEY defined: ' . (defined('RESEND_API_KEY') ? 'sí' : 'no') . ', len: ' . (defined('RESEND_API_KEY') ? strlen((string) RESEND_API_KEY) : 0) . "\n";
echo 'RESEND_FROM_EMAIL: ' . (defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : '(no definida)') . "\n";

$fileVars = diag_parse_env_file($envPath);
echo "\n--- parse directo del .env ---\n";
echo 'Claves RESEND: ' . implode(', ', array_keys(array_filter(
    $fileVars,
    static fn ($k) => str_starts_with((string) $k, 'RESEND_'),
    ARRAY_FILTER_USE_KEY
))) . "\n";

if ($apiKey !== '') {
    echo "\nOK: la API key se lee correctamente. Pruebe enviar correo desde Mantenimiento.\n";
} else {
    echo "\nERROR: RESEND_API_KEY vacía. Revise .env y despliegue src/config/config.php\n";
}

echo "\n=== Fin ===\n";
