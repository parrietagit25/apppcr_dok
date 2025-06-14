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
$stmt = $pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = :code");
$stmt->bindParam(':code', $code, PDO::PARAM_STR);
$stmt->execute();

if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo_empleado = $list_code['codigo_empleado'];
    $nombre = $list_code['nombre'];
    $apellido = $list_code['apellido'];
    $departamento = $list_code['nombre_centro_costo'];
    $sangre = $list_code['tipo_sangre'];
}

require_once __DIR__ . '/../views/beneficios.php';
