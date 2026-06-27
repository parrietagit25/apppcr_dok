<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Telemetria.php';

if (isset($_SESSION['code'])) {
    try {
        $pdo = Database::connect();
        Telemetria::registrar($pdo, 'logout', [
            'codigo_empleado' => $_SESSION['code'],
            'type_user' => $_SESSION['type_user'] ?? null,
            'modulo' => 'Login',
            'accion' => 'Cierre de sesión',
        ]);
    } catch (Throwable $e) {
        error_log('salir telemetria: ' . $e->getMessage());
    }
}

session_destroy();
header('Location: /index.php');
exit();
