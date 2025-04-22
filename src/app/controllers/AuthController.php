<?php

// app/controllers/AuthController.php

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rrhh.php';

if (isset($_SESSION['code'])) {
    //require_once __DIR__ . '/../views/main.php';
}

$pdo = Database::connect();
$userModel = new User($pdo);

$pdo_rrhh = Database::connect(); 
$class_rrhh = new Rrhh($pdo_rrhh);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['code'])) {
    $code = $_POST['code'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($code) && !empty($password)) {

        if ($userModel->authenticate($code, $password)) {
            $_SESSION['code'] = $code;

            $frase = $class_rrhh->frase_semana();
            $nombre = $userModel->nombre_colaborador();
            $tipo_usuario = $userModel->get_tyte_user();
            require_once __DIR__ . '/../views/main.php';
            exit();

        } else {
            $error = "Código o contraseña incorrectos.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}

if (isset($_GET['reg_col'])) {

    //$nombre = $userModel->nombre_colaborador();
    

    if (isset($_POST['registro_colaborador'])) {
        //echo 'pasando';
        $userModel->insertar_colaborador($_POST['reg_code'], $_POST['reg_password']);
        //echo 'Colaborador Registrado';
        //sleep(3);
        require_once __DIR__ . '../views/login.php';
        exit();
    } 
    require_once __DIR__ . '/../views/reg_col.php';
    exit();
    /*
    header("Location: http://10.10.2.6/apppcr/pcrapp/app/views/reg_col.php");
    exit(); */
    
}



// Cargar la vista de login
require_once __DIR__ . '/../views/login.php';
