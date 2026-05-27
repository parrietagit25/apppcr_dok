<?php
/**
 * Health check: PHP + MySQL (SELECT 1). Sin sesión, login ni vistas.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$startedAt = microtime(true);
$serverTime = date('Y-m-d H:i:s');
const HEALTH_LOG_MAX_BYTES = 5242880; // 5 MB

/**
 * @param array<string, mixed> $payload
 */
function health_send(int $httpCode, array $payload): void
{
    http_response_code($httpCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function health_get_client_ip(): string
{
    $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($forwardedFor !== '') {
        $parts = explode(',', $forwardedFor);
        $ip = trim($parts[0]);
        if ($ip !== '') {
            return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function health_write_log(
    string $serverTime,
    string $status,
    string $database,
    int $responseMs,
    string $message = ''
): void {
    $logsDir = __DIR__ . '/logs';
    if (!is_dir($logsDir)) {
        @mkdir($logsDir, 0775, true);
    }

    $logPath = $logsDir . '/health.log';
    if (is_file($logPath) && filesize($logPath) > HEALTH_LOG_MAX_BYTES) {
        $rotatedName = $logsDir . '/health-' . date('Ymd-His') . '.log';
        @rename($logPath, $rotatedName);
    }

    $line = $serverTime
        . ' | IP: ' . health_get_client_ip()
        . ' | status: ' . $status
        . ' | database: ' . $database
        . ' | response_ms: ' . $responseMs;

    if ($message !== '') {
        $safeMessage = str_replace(["\r", "\n"], ' ', $message);
        $line .= ' | message: ' . $safeMessage;
    }

    @file_put_contents($logPath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

$phpStatus = 'ok';
$dbStatus = 'fail';
$dbMessage = '';

require_once __DIR__ . '/config/config.php';

try {
    if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
        throw new RuntimeException('Constantes de base de datos no definidas en config.php');
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_CONNECT_TIMEOUT => 3,
        ]
    );
    $pdo->query('SELECT 1');
    $dbStatus = 'ok';
} catch (Throwable $e) {
    $dbMessage = $e->getMessage();
}

$responseMs = (int) round((microtime(true) - $startedAt) * 1000);

if ($dbStatus === 'ok') {
    health_write_log($serverTime, 'ok', $dbStatus, $responseMs);
    health_send(200, [
        'status' => 'ok',
        'php' => $phpStatus,
        'database' => $dbStatus,
        'response_ms' => $responseMs,
        'server_time' => $serverTime,
    ]);
}

health_write_log($serverTime, 'error', $dbStatus, $responseMs, $dbMessage);
health_send(500, [
    'status' => 'error',
    'php' => $phpStatus,
    'database' => $dbStatus,
    'message' => $dbMessage,
    'response_ms' => $responseMs,
    'server_time' => $serverTime,
]);
