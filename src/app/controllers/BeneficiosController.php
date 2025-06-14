<?php 
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

$pdo = Database::connect();
$userModel = new User($pdo);

$tipo_usuario = $userModel->get_tyte_user();

if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

$code = $_SESSION['code'];
$nombre = $apellido = $departamento = $sangre = "";
$codigo_empleado = $code;

require_once __DIR__ . '/../views/beneficios.php';
