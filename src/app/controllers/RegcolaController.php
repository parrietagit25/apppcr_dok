<?php 
//session_start(); 
require_once __DIR__ . '/../../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

$mensaje = '';

$pdo = Database::connect();
$userModel = new User($pdo);

//$tipo_usuario = $userModel->get_tyte_user();

if (isset($_GET['reg_col'])) {
    
    if (isset($_POST['registro_colaborador'])) {

        $result_bus = $userModel->buscar_code_col_reg($_POST['reg_code']);

        if ($result_bus == 1) {
        
            //echo 'pasando';
            $userModel->insertar_colaborador($_POST['reg_code'], $_POST['reg_password']);
            $mensaje = 'Colaborador Registrado';
            sleep(3);
            header("Location: /index.php");
            //require_once __DIR__ . '/../views/login.php';
            exit();
        }else {
            $mensaje = 'Ya usted esta registrado';
        }


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
            require_once __DIR__ . '/index.php?msg=' . urlencode($mensaje);
            exit();
        } 
        require_once __DIR__ . '/../views/restore_code.php';
        exit();
    }

    require_once __DIR__ . '/../views/restore_code.php';
    exit();
    
}

function enviarCorreoRecuperacion($emailDestino, $codigoRecuperacion) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com'; // Cambia esto según tu proveedor
        $mail->SMTPAuth = true;
        $mail->Username = 'notificaciones@grupopcr.com.pa';
        $mail->Password = EMAIL_GLOBAL;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('notificaciones@grupopcr.com.pa', 'PCR notificaciones');
        $mail->addAddress($emailDestino);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña';
        $mail->Body = '<h3>Ingrese en el siguiente link para poder restablecer su contraseña <a href="https://apppcr.net/app/controllers/RegcolaController.php?restablecer_password='.$codigoRecuperacion.'" target="_blank">Restablecer</a> </h3>';

        $mail->send();
        return 'Correo enviado correctamente';
    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

// Cargar la vista de login
require_once __DIR__ . '/../views/login.php';
