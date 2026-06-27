<?php

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Telemetria.php';

if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

$pdo = Database::connect();
$userModel = new User($pdo);
$tipo_usuario = (int) $userModel->get_tyte_user();

if ($tipo_usuario !== 1) {
    header('Location: ' . BASE_URL_CONTROLLER . '/MainController.php');
    exit();
}

$nombre = $userModel->nombre_colaborador();
$telemetria = new Telemetria($pdo);
$tablaOk = Telemetria::tablaDisponible($pdo);

$fecha_desde = !empty($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-01');
$fecha_hasta = !empty($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-d');

if ($fecha_desde > $fecha_hasta) {
    $tmp = $fecha_desde;
    $fecha_desde = $fecha_hasta;
    $fecha_hasta = $tmp;
}

$kpis = $tablaOk ? $telemetria->getKpis($fecha_desde, $fecha_hasta) : [];
$actividadDiaria = $tablaOk ? $telemetria->getActividadDiaria($fecha_desde, $fecha_hasta) : [];
$topModulos = $tablaOk ? $telemetria->getTopModulos($fecha_desde, $fecha_hasta) : [];
$topUsuarios = $tablaOk ? $telemetria->getTopUsuarios($fecha_desde, $fecha_hasta) : [];
$eventosPorTipo = $tablaOk ? $telemetria->getEventosPorTipo($fecha_desde, $fecha_hasta) : [];
$actividadPorHora = $tablaOk ? $telemetria->getActividadPorHora($fecha_desde, $fecha_hasta) : [];
$usuariosPorDia = $tablaOk ? $telemetria->getUsuariosActivosPorDia($fecha_desde, $fecha_hasta) : [];
$eventosRecientes = $tablaOk ? $telemetria->getEventosRecientes($fecha_desde, $fecha_hasta) : [];

require_once __DIR__ . '/../views/telemetria.php';
