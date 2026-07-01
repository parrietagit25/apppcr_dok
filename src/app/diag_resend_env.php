<?php
/**
 * Diagnóstico Resend — ejecutar dentro del contenedor:
 *   php /var/www/html/app/diag_resend_env.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo CLI');
}

require_once __DIR__ . '/../config/config.php';

$envPath = realpath(__DIR__ . '/../.env') ?: (__DIR__ . '/../.env');

echo "=== Diagnóstico Resend (apppcr) ===\n\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "Archivo .env: {$envPath}\n";
echo "Existe: " . (is_readable($envPath) ? 'sí' : 'no') . "\n\n";

if (is_readable($envPath)) {
    echo "--- grep RESEND (cat -A) ---\n";
    passthru('grep RESEND ' . escapeshellarg($envPath) . ' | cat -A');
    echo "\n";
}

$apiKey = resend_env('RESEND_API_KEY', '');
$fromEmail = resend_env('RESEND_FROM_EMAIL', '');
$fromName = resend_env('RESEND_FROM_NAME', '');

echo "--- resend_env() ---\n";
echo 'RESEND_API_KEY len: ' . strlen($apiKey) . "\n";
echo 'RESEND_API_KEY prefix: ' . ($apiKey !== '' ? substr($apiKey, 0, 7) . '...' : '(vacío)') . "\n";
echo 'RESEND_FROM_EMAIL: ' . ($fromEmail !== '' ? $fromEmail : '(vacío)') . "\n";
echo 'RESEND_FROM_NAME: ' . ($fromName !== '' ? $fromName : '(vacío)') . "\n\n";

echo "--- getenv / apache_getenv ---\n";
$g = getenv('RESEND_API_KEY');
echo 'getenv len: ' . ($g !== false ? strlen((string) $g) : 0) . "\n";
if (function_exists('apache_getenv')) {
    $a = apache_getenv('RESEND_API_KEY', true);
    echo 'apache_getenv len: ' . ($a !== false ? strlen((string) $a) : 0) . "\n";
}

echo "\n--- constantes PHP ---\n";
echo 'RESEND_API_KEY defined: ' . (defined('RESEND_API_KEY') ? 'sí' : 'no') . ', len: ' . (defined('RESEND_API_KEY') ? strlen((string) RESEND_API_KEY) : 0) . "\n";
echo 'RESEND_FROM_EMAIL: ' . (defined('RESEND_FROM_EMAIL') ? RESEND_FROM_EMAIL : '(no definida)') . "\n";

$vars = dotenv_vars_from_disk();
echo "\n--- dotenv_vars_from_disk ---\n";
echo 'Claves RESEND: ' . implode(', ', array_keys(array_filter(
    $vars,
    static fn ($k) => str_starts_with((string) $k, 'RESEND_'),
    ARRAY_FILTER_USE_KEY
))) . "\n";

echo "\n=== Fin ===\n";
