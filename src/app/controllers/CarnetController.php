<?php
// app/controllers/MainController.php
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

// Lista local de colaboradores
$colaboradores = [
    "00111111" => ["nombre" => "César", "apellido" => "Durufour", "departamento" => "RRHH", "sangre" => "O+"],
    "00111112" => ["nombre" => "Ricardo", "apellido" => "De La Guardia", "departamento" => "IT", "sangre" => "A+"],
    "00111122" => ["nombre" => "Marilin", "apellido" => "Santos", "departamento" => "Cobros", "sangre" => "B+"],
    "00111113" => ["nombre" => "Oscar", "apellido" => "Castillo", "departamento" => "Admin", "sangre" => "AB-"],
    "00111114" => ["nombre" => "Daska", "apellido" => "Vaz", "departamento" => "Mercadeo", "sangre" => "O-"],
    "00111115" => ["nombre" => "Herminda", "apellido" => "Sánchez", "departamento" => "Mina", "sangre" => "A-"],
    "00111116" => ["nombre" => "David", "apellido" => "Jordan", "departamento" => "Operaciones", "sangre" => "B-"],
    "00111117" => ["nombre" => "Luis", "apellido" => "Pinilla", "departamento" => "Operaciones", "sangre" => "O+"],
    "00111118" => ["nombre" => "Rigoberto", "apellido" => "López", "departamento" => "Operaciones", "sangre" => "A+"],
    "00111119" => ["nombre" => "Jaime", "apellido" => "Cedeño", "departamento" => "Operaciones", "sangre" => "AB+"],
    "00111110" => ["nombre" => "Diana", "apellido" => "Rico", "departamento" => "Admin", "sangre" => "B+"],
    "00111120" => ["nombre" => "Giovanni", "apellido" => "Colucci", "departamento" => "Panarenting", "sangre" => "O-"],
];

$nombre = $apellido = $departamento = $sangre = "";
$codigo_empleado = $code;

if (array_key_exists($code, $colaboradores)) {
    // Datos simulados
    $nombre = $colaboradores[$code]["nombre"];
    $apellido = $colaboradores[$code]["apellido"];
    $departamento = $colaboradores[$code]["departamento"];
    $sangre = $colaboradores[$code]["sangre"];
} else {
    // Consulta en base de datos
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
}

if (isset($_GET['verificar_carnet'])) {

    require_once __DIR__ . '/../views/validar_empleado.php';
    exit();
    # code...
}

// Cargar la vista
require_once __DIR__ . '/../views/carnet.php';
