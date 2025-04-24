<?php
// app/controllers/RRHHController.php   
require_once __DIR__ . '/../../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Rrhh.php';
require_once __DIR__ . '/../models/User.php';



if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}
/*
echo '<pre>';
echo var_dump($_POST);
echo '</pre>';
*/ 
$pdo = Database::connect(); 
$class = new Rrhh($pdo);

$pdo = Database::connect();
$userModel = new User($pdo);

$tipo_usuario = $userModel->get_tyte_user();

$todos_datos = $class->datos_colaborador();
$nombre = "";
foreach ($todos_datos as $key => $value) {
    $nombre = $value['nombre']. ' ' .$value['apellido'];
}

if (isset($_GET['mis_datos']) && $_GET['mis_datos'] == 1) {
    $todos_datos = $class->datos_colaborador();
    require_once __DIR__ . '/../views/colaborador.php';
    exit();

}elseif (isset($_POST['actualizacion_datos'])) {

    $mensaje = 'El colaborador '.$_POST['nombre_cola'].' con codigo '.$_POST['code_cola'].' 
    ha solicitado, actualizacion de datos <br> 
    <br>
    Estado Civil: '.$_POST['estado_civil'].' <br>
    Email: '.$_POST['email'].' <br>
    Telefono: '.$_POST['telefono'].' <br>
    Direccion: '.$_POST['direccion'].' <br>
    Comentarios del colaborador: '.$_POST['dato_adicional'].' <br> ';

    $copia = ["pedro.arrieta@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa"];
    //$copia = ["pedro.arrieta@grupopcr.com.pa"];

    $class->enviar_correo("rrhhgpcr@grupopcr.com.pa", $copia, "Actualizacion de Datos", $mensaje);
    echo "<div class='alert alert-success'>Solicitud de actualizacion de datos enviada.</div>";
    $todos_datos = $class->datos_colaborador();
    require_once __DIR__ . '/../views/colaborador.php';
    exit();
    
} elseif (isset($_GET['mis_vacaciones'])) {
    $mis_vacas = $class->mis_vacaciones();
    require_once __DIR__ . '/../views/mis_vacaciones.php';
    exit();
    
} elseif (isset($_GET['carta_trabajo'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carta_trabajo'])) {
        $descripcion = $_POST['descripcion'];
        $carta_trabajo = $class->carta_trabajo($descripcion);

        $dartos_cola = $class->datos_colaborador();
        $nombre_comple = "";
        $codigo = "";
        if (!empty($dartos_cola) && isset($dartos_cola[0])) {
            $nombre_comple = $dartos_cola[0]['nombre'] . ' ' . $dartos_cola[0]['apellido'];
            $codigo = $dartos_cola[0]['codigo_empleado'];
        }        

        $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
        ha solicitado, una carta de trabajo <br> 
        <br>
        Comentarios del colaborador: '.$descripcion.' <br> ';

        $copia = ["abi.pineda@grupopcr.com.pa", "pedro.arrieta@grupopcr.com.pa"];
        //$copia = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo("rrhhgpcr@grupopcr.com.pa", $copia, "Carta de trabajo", $mensaje);
        //echo "<div class='alert alert-success'>Solicitud de Carta de trabajo enviada.</div>";

    }
    $solicitudes = $class->solicitudes();
    require_once __DIR__ . '/../views/carta_trabajo.php';
    exit();
    
} elseif (isset($_GET['carta_trabajo_aprobar'])) {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aprobar_carta'])) {
        
        $id_carta = $_POST['solicitud_id'];
        $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';
        
        // Carpeta de almacenamiento
        $upload_dir = __DIR__ . '/../uploads/carta_trabajo/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivo_tmp = $_FILES['archivo']['tmp_name'];
            $archivo_nombre = basename($_FILES['archivo']['name']);
            $archivo_destino = $upload_dir . $archivo_nombre;

            if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
                if ($class->aprobar_carta_trabajo($id_carta, $archivo_nombre, $comentario)) {
                    echo "<div class='alert alert-success'>Carta aprobada y archivo guardado correctamente.</div>";

                    $dartos_cola = $class->datos_colaborador();
                    foreach ($dartos_cola as $key => $value) {
                        $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
                        $codigo = $value['codigo_empleado'];
                        $email = $value['email'];
                    }
                    
                    $mensaje = 'Estimado '.$nombre_comple.'  
                    ha solicitado, una carta de trabajo la cual fue enviada al app pcr, por favor ingrese a la plataforma para ver o descargar la carta solicitada<br> 
                    <br> ';

                    $copia = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
                    //$copia = ["pedro.arrieta@grupopcr.com.pa"];

                    $class->enviar_correo($email, $copia, "Carta de trabajo Enviada", $mensaje);

                } else {
                    echo "<div class='alert alert-danger'>Error al actualizar la base de datos.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Error al mover el archivo.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No se subió ningún archivo válido.</div>";
        }
    }
    $solicitudes = $class->solicitudes_aprobar();

    require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
    exit();

}elseif(isset($_GET['incapacidad'])){

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
        
        $code_user = isset($_SESSION['code']) ? ltrim($_SESSION['code'], '0') : 0;
        $descripcion = trim($_POST['descripcion']);
        $file_add = "";
    
        // Carpeta de almacenamiento
        $upload_dir = __DIR__ . '/../uploads/incapacidades/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        if (isset($_FILES['archivo_incapacidad']) && $_FILES['archivo_incapacidad']['error'] === UPLOAD_ERR_OK) {
            $archivo_tmp = $_FILES['archivo_incapacidad']['tmp_name'];
            $archivo_nombre = basename($_FILES['archivo_incapacidad']['name']);
            $archivo_destino = $upload_dir . $archivo_nombre;
    
            if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
                $file_add = $archivo_nombre;
            } else {
                echo "<div class='alert alert-danger'>Error al mover el archivo.</div>";
                exit;
            }
        }
    
        // Insertar en la base de datos usando el modelo
        if ($class->insertar_incapacidad($code_user, $descripcion, $file_add)) {

            $dartos_cola = $class->datos_colaborador();
            foreach ($dartos_cola as $key => $value) {
                $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
                $codigo = $value['codigo_empleado'];
            }

            $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
            ha adjuntado una incapacidad, ingrese a la app pcr para visualizar o descargar la misma. <br>';

            $copia = ["pedro.arrieta@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa"];
            //$copiacoo = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
            //$copia = ["pedro.arrieta@grupopcr.com.pa"];
        
            $class->enviar_correo("rrhhgpcr@grupopcr.com.pa", $copia, "Incapacidad de '".$nombre_comple."' ", $mensaje);
            
            echo "<div class='alert alert-success'>Incapacidad guardada correctamente.</div>";

        } else {
            echo "<div class='alert alert-danger'>Error al guardar la incapacidad en la base de datos.</div>";
        }
    }

    $incapacidad = $class->incapacidad();

    require_once __DIR__ . '/../views/incapacidad.php';
    exit();

}elseif(isset($_GET['incapacidad_vrrhh'])){

    if (isset($_POST['incapacidad_id'])) {

        //echo 'paso por dentro del controlador '.$_POST['incapacidad_id'];

        $class->update_incapacidad($_POST['incapacidad_id']);

        $dartos_cola = $class->datos_colaborador();
        foreach ($dartos_cola as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $email = $value['email'];
        }

        $mensaje = 'Estimado '.$nombre_comple.' <br> 
        Se ha revisado su incapacidad por parte del departamento de RRHH <br>';

        $copiacoo = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Incapacidad Revisada ", $mensaje);
        
        echo "<div class='alert alert-success'>Incapacidad Revisada.</div>";
    
    }

    $incapacidad = $class->incapacidad();

    require_once __DIR__ . '/../views/incapacidad_vrrhh.php';
    exit();

}elseif (isset($_GET['solicitud_permiso'])) {

    if (isset($_POST['solicitud_permiso'])) {

        $id_jefe = !empty($_POST['id_jefe']) ? (int)$_POST['id_jefe'] : NULL;
        $descripcion = $_POST['descripcion'];
        $tipo_licencia = $_POST['tipo_licencia'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        $class->insertar_permiso($id_jefe, $descripcion, $tipo_licencia, $fecha_inicio, $fecha_fin);

        $email_jefe = $class->datos_jefes($id_jefe);

        foreach ($email_jefe as $key => $value) {
             $email_feje = $value['email'];
        }

        $dartos_cola = $class->datos_colaborador();
        foreach ($dartos_cola as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $codigo = $value['codigo_empleado'];
            $email = $value['email'];
        }

        $mensaje = 'El colaborador  '.$nombre_comple.' con codigo de empleado: '.$codigo.'<br> 
        ha solicitado un permiso tipo '.$tipo_licencia.' <br>
        Fechas del permiso desde '.$fecha_inicio.' hasta '.$fecha_fin.' <br>
        Descripcion del permiso: '.$descripcion.'';

        $copia = ["pedro.arrieta@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa", $email_feje];

        //$copia = ["pedro.arrieta@grupopcr.com.pa"];
    
        $class->enviar_correo("rrhhgpcr@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje);

        //$copia = "pedro.arrieta@grupopcr.com.pa "."abi.pineda@grupopcr.com.pa ".$email_feje;

        echo "<div class='alert alert-success'>Permiso solicitado correctamente.</div>";

        /* $to = "pedro.arrieta@grupopcr.com.pa";
        $subject = "Solicitud de permiso tipo '".$tipo_licencia."'";
        //$message = "Este es el contenido del mensaje.";
        $headers = "From: notificaciones@grupopcr.com.pa\r\n";
        $headers .= "Reply-To: notificaciones@grupopcr.com.pa\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if(mail($to, $subject, $mensaje, $headers)) {
            echo "<div class='alert alert-success'>Permiso solicitado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Fallo al enviar el correo.</div>";
        } */
    
        
        
    }

    $select_jefe = $class->select_jefe();
    
    $permisos = $class->select_permisos();

    $mis_vacas = $class->mis_vacaciones();

    require_once __DIR__ . '/../views/solicitud_permiso.php';
    exit();


}elseif(isset($_GET['solicitud_permiso_admin'])){

    if (isset($_POST['aprobar_permiso'])) {
        $class->update_permiso($_POST['respuesta_jefe'], $_POST['comentario_jefe'], $_POST['permiso_id']);
        //echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

        $dartos_cola = $class->datos_colaborador();
        foreach ($dartos_cola as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $codigo = $value['codigo_empleado'];
            $email = $value['email'];
        }

        if ($_POST['respuesta_jefe'] == 'A') {
            $rep = 'Solicitud Aceptada';
        }else {
            $rep = 'Solicitud declinada';
        }

        $mensaje = 'Estimado  '.$nombre_comple.' <br> 
        ha solicitado un permiso tipo '.$_POST['tipo_licencia'].' <br>
        La respuesta de su jefe directo fue '.$rep.' <br>
        Los comentarios de su jefe directo son: '.$_POST['comentario_jefe'].'';

        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
        $copiacoo = ["pedro.arrieta@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje);
    
        echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

    }

    $permisos = $class->select_permisos_all();

    require_once __DIR__ . '/../views/solicitud_permiso_aprobar.php';
    exit();

}elseif (isset($_GET['solicitus_vacaciones'])) {

    if (isset($_POST['solicitud_vacaciones'])) {

        $id_jefe = !empty($_POST['id_jefe']) ? (int)$_POST['id_jefe'] : NULL;
        $descripcion = $_POST['descripcion'];
    
        $class->insertar_vacaciones($id_jefe, $descripcion);
    
        echo "<div class='alert alert-success'>Vacaciones solicitadas correctamente.</div>";
        
    }

    $select_jefe = $class->select_jefe();
    $permisos = $class->select_vacaciones();

    require_once __DIR__ . '/../views/solicitud_vacaciones.php';
    exit();
    
}elseif (isset($_GET['solicitus_vacaciones_admin'])) {

    if (isset($_POST['aprobar_vacaciones'])) {
        $class->update_vacaciones($_POST['respuesta_jefe'], $_POST['comentario_jefe'], $_POST['permiso_id']);
        echo "<div class='alert alert-success'>Vacaciones solicitadas correctamente.</div>";

        if ($_POST['respuesta_jefe'] == 'A') {

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
                $mail->addAddress('pedro.arrieta@grupopcr.com.pa');

                $mail->isHTML(true);
                $mail->Subject = 'Solicitud de Permiso';
                $mail->Body = $class->correo_solicitud_vacaciones($_POST['permiso_id']);

                $mail->send();
                //return 'Correo enviado correctamente';
            } catch (Exception $e) {
                //return "Error al enviar el correo: {$mail->ErrorInfo}";
            } 

        }

    }

    $vacaciones = $class->select_vacaciones_all();

    require_once __DIR__ . '/../views/solicitud_vacaciones_aprobar.php';
    exit();

}elseif(isset($_GET['calamidad'])){

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
        
        $code_user = isset($_SESSION['code']) ? ltrim($_SESSION['code'], '0') : 0;
        $descripcion = trim($_POST['descripcion']);
        $file_add = "";
    
        // Carpeta de almacenamiento
        $upload_dir = __DIR__ . '/../uploads/calamidades/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        if (isset($_FILES['archivo_calamidades']) && $_FILES['archivo_calamidades']['error'] === UPLOAD_ERR_OK) {
            $archivo_tmp = $_FILES['archivo_calamidades']['tmp_name'];
            $archivo_nombre = basename($_FILES['archivo_calamidades']['name']);
            $archivo_destino = $upload_dir . $archivo_nombre;
    
            if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
                $file_add = $archivo_nombre;
            } else {
                echo "<div class='alert alert-danger'>Error al mover el archivo.</div>";
                exit;
            }
        }
    
        // Insertar en la base de datos usando el modelo
        if ($class->insertar_calamidades($code_user, $descripcion, $file_add)) {

            $dartos_cola = $class->datos_colaborador();
            foreach ($dartos_cola as $key => $value) {
                $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
                $codigo = $value['codigo_empleado'];
            }

            $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
            ha registrado una calamidad. <br>
            Comentarios del colaborador: '.$descripcion;

            $copia = ["abi.pineda@grupopcr.com.pa", "pedro.arrieta@grupopcr.com.pa"];
            //$copia = ["pedro.arrieta@grupopcr.com.pa"];
        
            $class->enviar_correo("rrhhgpcr@grupopcr.com.pa", $copia, "Calamidad de '".$nombre_comple."' ", $mensaje);

            echo "<div class='alert alert-success'>Calamidad guardada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al guardar la calamidad en la base de datos.</div>";
        }
    }

    $calamidades = $class->calamidades();

    require_once __DIR__ . '/../views/calamidades.php';
    exit();

}elseif(isset($_GET['calamidad_vrrhh'])){

    if (isset($_POST['calamidad_id'])) {

        //echo 'paso por dentro del controlador '.$_POST['calamidad_id'];

        $class->update_calamidad($_POST['calamidad_id']);

        $dartos_cola = $class->datos_colaborador();
        foreach ($dartos_cola as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $codigo = $value['codigo_empleado'];
            $email = $value['email'];
        }

        $mensaje = 'Estimado '.$nombre_comple.' <br> 
        La calamidad ha sido revisada por parte del departamento RRHH. <br>';

        $copia = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
        //$copia = ["pedro.arrieta@grupopcr.com.pa"];
    
        $class->enviar_correo($email, $copia, "Calamidad revisada", $mensaje);
        
        echo "<div class='alert alert-success'>Calamidad Revisada.</div>";
    
    }

    $calamidades = $class->calamidades();

    require_once __DIR__ . '/../views/calamidad_rrhh.php';
    exit();

}else {
    $code_lomg = strlen($_SESSION['code']);
    require_once __DIR__ . '/../views/rrhh.php';
}
