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
$departamento='';
$nombre = $userModel->nombre_colaborador();
$tipo_usuario = $userModel->get_tyte_user();
$seleccionar_evaluacion = $class_rrhh->select_eval();
$get_departamneto = $class_rrhh->get_departamento($_SESSION['code']);
foreach ($get_departamneto as $key => $value) {
    $departamento = $value['nombre_departamento'];
}

$seleccionar_evaluacion_departamento = $class_rrhh->select_eval_departamento($departamento);

/* update frase de la semana */
if (isset($_POST['frase_semana'])) {
    try {
        $userModel->update_frase($_POST['frase_semana'], $_POST['id_frase']);
    } catch (\Throwable $th) {
        echo 'Error en el controlador actualizar frase';
    }
    
}

if (isset($_GET['eval']) && $tipo_usuario == 1) {

    if (isset($_POST['bo_reg_eval'])) {

        $insertar_eval = $class_rrhh -> insertar_eval($_POST['titulo_eva'], $_POST['select_departamento'], $_POST['link_eval']);
        
    }

    $evaluacion = $class_rrhh -> get_departamentos();

    require_once __DIR__ . '/../views/evaluacion_vrrhh.php';
    exit();
}


// Cargar la vista
require_once __DIR__ . '/../views/evaluacion.php';
