<?php 

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rrhh.php';

$pdo = Database::connect();
$userModel = new User($pdo);
$pdo_rrhh = Database::connect();
$class_rrhh = new Rrhh($pdo_rrhh);

/* nombre de usuario */
$nombre = $userModel->nombre_colaborador();
$tipo_usuario = $userModel->get_tyte_user();

if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

$tipo_usuario = $userModel->get_tyte_user();

if (isset($_GET['metricas'])) {

    $cartas_trabajos = $class_rrhh->count_cartas_trabajo();

}

require_once __DIR__ . '/../views/metricas.php';