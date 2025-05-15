<?php
// app/controllers/MainController.php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rrhh.php';

$pdo = Database::connect();
$userModel = new User($pdo);

$pdo_rrhh = Database::connect(); 
$class_rrhh = new Rrhh($pdo_rrhh);

if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

/* nombre de usuario */
$nombre = $userModel->nombre_colaborador();
$tipo_usuario = $userModel->get_tyte_user();

/* update frase de la semana */
if (isset($_POST['boton_frase_semana'])) {
    try {
        $class_rrhh->update_frase($_POST['frase_semana'], $_POST['id_frase']);
    } catch (\Throwable $th) {
        echo 'Error en el controlador actualizar frase';
    }
    
}

/* Frase de la semana*/
$frase = $class_rrhh->frase_semana();
/* listado de cumplea;os */
if (isset($_GET['cumple'])) {

    function obtenerMesEnEspanol($mesIngles) {
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];
        return $meses[$mesIngles] ?? $mesIngles;
    }

    $cumple = $class_rrhh->dia_cumple();
    require_once __DIR__ . '/../views/cumple.php';
    exit();
}

if (isset($_GET['mantenimineto'])) {
    require_once __DIR__ . '/../views/mantenimiento.php';
    exit();
}

if (isset($_GET['mantenimiento_usuarios'])) {

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = $_POST['codigo_empleado'];
        $pass = $_POST['nueva_password'];

        $resultado = $userModel->actualizar_colaborador($pass, $code);

        if ($resultado) {
            echo "<div class='alert alert-success'>Regsitro Actualizado.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actalizar.</div>";
        }
    }

    $usuarios = $userModel->usuarios();
    require_once __DIR__ . '/../views/mantenimiento_usuarios.php';
    exit();
}

if (isset($_GET['mantenimiento_permisos'])) {
    require_once __DIR__ . '/../views/mantenimiento_permisos.php';
    exit();
}

// Cargar la vista
require_once __DIR__ . '/../views/main.php';
