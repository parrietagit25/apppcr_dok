<?php 
//session_start(); 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/MailService.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Telemetria.php';

$mensaje = '';

$pdo = Database::connect();
$userModel = new User($pdo);

//$tipo_usuario = $userModel->get_tyte_user();

if (isset($_GET['reg_col'])) {
    
    if (isset($_POST['registro_colaborador'])) {
        $regCode = trim((string) ($_POST['reg_code'] ?? ''));
        $validacion = $userModel->puedeRegistrarColaborador($regCode);

        if ($validacion['ok'] && $userModel->insertar_colaborador($regCode, $_POST['reg_password'] ?? '')) {
            Telemetria::registrar($pdo, 'registro', [
                'codigo_empleado' => $regCode,
                'modulo' => 'Registro',
                'accion' => 'Nuevo colaborador registrado',
            ]);
            header('Location: /index.php');
            exit();
        }

        $mensaje = $validacion['mensaje'] !== '' ? $validacion['mensaje'] : 'No se pudo completar el registro.';
    } 
    require_once __DIR__ . '/../views/reg_col.php';
    exit();
    
}

if (isset($_GET['restablecer_password'])) {

    if (isset($_POST['restablecer_pass'])) {

        $userModel->actualizar_colaborador($_POST['new_pass2'], $_POST['restore_code']);
        $mensaje = 'Contraseña actualizada';
        header("Location: https://apppcr.net/index.php?msg=Contraseña+actualizada+correctamente");
        //require_once __DIR__ . '/../views/login.php';
        exit();
        
    }

    $codigo_empleado = $_GET['restablecer_password'];

    require_once __DIR__ . '/../views/recuperar_password.php';
    exit();
    
}

if (isset($_GET['restore_pass'])) {

    if (isset($_POST['restore_col'])) {
        if (isset($_POST['restore_code']) && $_POST['restore_code'] <> '') {
            $email = $_POST['email']; 
            $codigoRecuperacion = $_POST['restore_code']; 
            $mensaje = enviarCorreoRecuperacion($email, $codigoRecuperacion);
            //require_once __DIR__ . '/../views/login.php';
            header("Location: /index.php?msg=" . urlencode($mensaje));
            exit();
        } 
        require_once __DIR__ . '/../views/restore_code.php';
        exit();
    }

    require_once __DIR__ . '/../views/restore_code.php';
    exit();
    
}

function enviarCorreoRecuperacion($emailDestino, $codigoRecuperacion)
{
    $mensaje = '<h3>Ingrese en el siguiente link para poder restablecer su contraseña '
        . '<a href="https://apppcr.net/app/controllers/RegcolaController.php?restablecer_password='
        . htmlspecialchars($codigoRecuperacion, ENT_QUOTES, 'UTF-8')
        . '" target="_blank">Restablecer</a></h3>';

    $resultado = MailService::enviar($emailDestino, [], 'Recuperación de contraseña', $mensaje);
    return $resultado === true ? 'Correo enviado correctamente' : $resultado;
}

// Cargar la vista de login
require_once __DIR__ . '/../views/login.php';
