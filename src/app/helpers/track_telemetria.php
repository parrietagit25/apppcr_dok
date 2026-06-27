<?php
/**
 * Registro automático de page views / acciones (incluir desde header.php).
 */
if (!isset($_SESSION['code'])) {
    return;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Telemetria.php';

try {
    $pdoTel = Database::connect();
    Telemetria::trackPaginaActual($pdoTel);
} catch (Throwable $e) {
    error_log('track_telemetria: ' . $e->getMessage());
}
