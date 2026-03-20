<?php
// app/controllers/RRHHController.php   
require_once __DIR__ . '/../../vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;        // Se mantiene porque otras funciones lo usan
use Mpdf\Mpdf;            // Se agrega para usar en nuevas funciones

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

if (!function_exists('rrhh_normalize_code')) {
    function rrhh_normalize_code($code) {
        return ltrim((string) $code, '0');
    }
}

if (!function_exists('rrhh_process_incapacidad_upload')) {
    function rrhh_process_incapacidad_upload($file, $upload_dir, &$error_message = '') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $error_message = 'Archivo inválido.';
            return '';
        }

        $safe_name = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file['name']));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed_mimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf'
        ];

        if (!isset($allowed_mimes[$mime])) {
            $error_message = 'Tipo de archivo no permitido.';
            return '';
        }

        $ext = $allowed_mimes[$mime];
        $base_without_ext = pathinfo($safe_name, PATHINFO_FILENAME);
        $unique_name = $base_without_ext . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destination = $upload_dir . $unique_name;

        if (strpos($mime, 'image/') === 0) {
            $img = null;
            if ($mime === 'image/jpeg') {
                $img = @imagecreatefromjpeg($file['tmp_name']);
            } elseif ($mime === 'image/png') {
                $img = @imagecreatefrompng($file['tmp_name']);
            } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                $img = @imagecreatefromwebp($file['tmp_name']);
            }

            if (!$img) {
                $error_message = 'No se pudo procesar la imagen.';
                return '';
            }

            // Re-encode para reducir peso y eliminar metadatos EXIF.
            if ($mime === 'image/jpeg') {
                $ok = imagejpeg($img, $destination, 75);
            } elseif ($mime === 'image/png') {
                imagesavealpha($img, true);
                $ok = imagepng($img, $destination, 6);
            } else {
                $ok = function_exists('imagewebp') ? imagewebp($img, $destination, 75) : false;
            }

            imagedestroy($img);

            if (!$ok) {
                $error_message = 'No se pudo guardar la imagen procesada.';
                return '';
            }
        } else {
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $error_message = 'Error al mover el archivo.';
                return '';
            }
        }

        return $unique_name;
    }
}

// Endpoints JSON para Mi Personal (detalle empleado y permisos)
$codigo_sesion = trim($_SESSION['code'] ?? '');
$puede_acceder_mi_personal = ($tipo_usuario == 1 || $tipo_usuario == 6 || $codigo_sesion === '001558');
if (isset($_GET['obtener_detalle_empleado']) && isset($_GET['codigo'])) {
    header('Content-Type: application/json; charset=utf-8');
    $codigo = trim($_GET['codigo']);
    if (!$puede_acceder_mi_personal) {
        echo json_encode(['success' => false, 'mensaje' => 'Sin permiso']);
        exit;
    }
    $datos = $class->get_datos_empleado_por_codigo($codigo);
    echo json_encode(['success' => true, 'datos' => $datos]);
    exit;
}
if (isset($_GET['obtener_permisos_empleado']) && isset($_GET['codigo'])) {
    header('Content-Type: application/json; charset=utf-8');
    $codigo = trim($_GET['codigo']);
    if (!$puede_acceder_mi_personal) {
        echo json_encode(['success' => false, 'mensaje' => 'Sin permiso']);
        exit;
    }
    $permisos = $class->get_permisos_por_codigo($codigo);
    echo json_encode(['success' => true, 'permisos' => $permisos]);
    exit;
}

$todos_datos = $class->datos_colaborador();
$nombre = "";
foreach ($todos_datos as $key => $value) {
    $nombre = $value['nombre']; //. ' ' .$value['apellido'];
}

if (isset($_GET['mi_personal']) && $_GET['mi_personal'] == 1) {
    // Acceso: admin (tipo 1), supervisores (tipo 6) o solo el usuario 001558 (tipo 4)
    if (!$puede_acceder_mi_personal) {
        header("Location: " . BASE_URL_CONTROLLER . "/RRHHController.php");
        exit();
    }
    // Admin (tipo 1) o usuario 001558 ven todo el personal; supervisores solo su personal a cargo
    $mi_personal_ver_todos = ($tipo_usuario == 1 || $codigo_sesion === '001558');
    if ($mi_personal_ver_todos) {
        $mi_personal_lista = $class->get_todos_empleados_activos();
    } else {
        $mi_personal_lista = $userModel->get_personal_a_cargo($_SESSION['code']);
    }
    require_once __DIR__ . '/../views/mi_personal.php';
    exit();
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

    $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
    //$copia = ["pedro.arrieta@grupopcr.com.pa"];

    $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Actualizacion de Datos", $mensaje);
    echo "<div class='alert alert-success'>Solicitud de actualizacion de datos enviada.</div>";
    $todos_datos = $class->datos_colaborador();
    require_once __DIR__ . '/../views/colaborador.php';
    exit();
    
} elseif (isset($_GET['mis_vacaciones'])) {
    $mis_vacas = $class->mis_vacaciones();
    $all_vacas = [];
    $all_vacas_gerentes = [];
    if ($tipo_usuario == 1 || $tipo_usuario == 4) {
        $all_vacas = $class->mis_vacaciones_all_employe();
    } elseif ($tipo_usuario == 6) {
        $all_vacas_gerentes = $class->mis_vacaciones_all_employe_gerentes($_SESSION['code']);
    }
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

        $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
        //$copia = ["pedroarrieta25@hotmail.com"];

        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Carta de trabajo", $mensaje);
        //echo "<div class='alert alert-success'>Solicitud de Carta de trabajo enviada.</div>";

    }
    $solicitudes = $class->solicitudes();
    require_once __DIR__ . '/../views/carta_trabajo.php';
    exit();
    
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_carta_pdf'])) {
    // Validar entrada
    $id_carta = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);
    $comentario = isset($_POST['comentario']) ? htmlspecialchars(trim($_POST['comentario']), ENT_QUOTES, 'UTF-8') : '';

    $ver_aprobadas = isset($_POST['ver']) && $_POST['ver'] === 'aprobadas';

    if (!$id_carta) {
        echo "<div class='alert alert-danger'>ID de solicitud inválido.</div>";
        $solicitudes = $ver_aprobadas ? $class->solicitudes_aprobadas() : $class->solicitudes_aprobar();
        require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
        exit;
    }

    // Obtener los datos del colaborador y de la carta
    $datos = $class->get_datos_formulario_carta($id_carta);

    if (!$datos) {
        echo "<div class='alert alert-danger'>No se encontraron datos para generar la carta.</div>";
        $solicitudes = $ver_aprobadas ? $class->solicitudes_aprobadas() : $class->solicitudes_aprobar();
        require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
        exit;
    }

    try {
        $fecha_actual = date("d/m/Y");
        
        // Extraer datos (usar acceso explícito en lugar de extract)
        $nombre = $datos['nombre'] ?? '';
        $apellido = $datos['apellido'] ?? '';
        $nombre_completo = !empty(trim($datos['nombre_completo'] ?? '')) ? trim($datos['nombre_completo']) : trim($nombre . ' ' . $apellido);
        $cedula = $datos['cedula'] ?? '';
        $seguro = $datos['seguro'] ?? '';
        $fecha_ingreso = $datos['fecha_ingreso'] ?? '';
        $cargo = $datos['cargo'] ?? '';
        $salario = $datos['salario'] ?? '';
        $desc_seguro = $datos['desc_seguro'] ?? '0';
        $desc_educativo = $datos['desc_educativo'] ?? '0';
        $desc_renta = $datos['desc_renta'] ?? '0';
        $descripcion = $datos['descripcion'] ?? '';
        $codigo_empleado = $datos['codigo_empleado'] ?? '';
        
        $otros_descuentos = $class->get_otros_descuentos_por_carta($id_carta);

        // ========== NUEVO: Integración con sistema de verificación externo ==========
        // Determinar ruta correcta a carta_verificacion
        $carta_verificacion_path = $_SERVER['DOCUMENT_ROOT'] . '/carta_verificacion/';
        
        if (!file_exists($carta_verificacion_path . 'config.php')) {
            throw new Exception("No se encontró el sistema de verificación en: " . $carta_verificacion_path);
        }
        
        require_once $carta_verificacion_path . 'config.php';
        require_once $carta_verificacion_path . 'DatabaseExternal.php';
        require_once $carta_verificacion_path . 'CartaVerificacionService.php';
        
        $verificacionService = new CartaVerificacionService();
        
        // Generar token encriptado para el QR
        $token_qr = $verificacionService->encriptarToken($id_carta . '_' . time());
        
        // Generar hash de verificación
        $hash_verificacion = $verificacionService->generarHashVerificacion($id_carta, $codigo_empleado, $cedula);
        
        // Preparar deducciones para BD externa
        $deducciones_para_bd = [];
        $orden = 1;
        
        if ($desc_seguro > 0) {
            $deducciones_para_bd[] = [
                'tipo' => 'seguro_social',
                'descripcion' => 'Seguro Social',
                'monto' => $desc_seguro,
                'orden' => $orden++
            ];
        }
        
        if ($desc_educativo > 0) {
            $deducciones_para_bd[] = [
                'tipo' => 'seguro_educativo',
                'descripcion' => 'Seguro Educativo',
                'monto' => $desc_educativo,
                'orden' => $orden++
            ];
        }
        
        if ($desc_renta > 0) {
            $deducciones_para_bd[] = [
                'tipo' => 'impuesto_renta',
                'descripcion' => 'Impuesto sobre la Renta',
                'monto' => $desc_renta,
                'orden' => $orden++
            ];
        }
        
        // Agregar otros descuentos
        if (!empty($otros_descuentos)) {
            foreach ($otros_descuentos as $desc) {
                $deducciones_para_bd[] = [
                    'tipo' => 'otro',
                    'descripcion' => $desc['acreedor'],
                    'monto' => $desc['monto'],
                    'orden' => $orden++
                ];
            }
        }
        
        // Obtener email del colaborador
        $get_email_colab = $class->get_email_colaborador($id_carta);
        $email_destino = $get_email_colab['email'] ?? '';
        
        // Preparar datos para insertar en BD externa
        $datos_carta_bd = [
            'id_carta_original' => $id_carta,
            'hash_verificacion' => $hash_verificacion,
            'token_qr' => $token_qr,
            'codigo_empleado' => $codigo_empleado,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'cedula' => $cedula,
            'seguro_social' => $seguro,
            'email' => $email_destino,
            'cargo' => $cargo,
            'fecha_ingreso' => $fecha_ingreso,
            'salario_bruto' => $salario,
            'incluye_desglose_salarial' => !empty($deducciones_para_bd) ? 1 : 0,
            'descripcion' => $descripcion,
            'comentario_rrhh' => $comentario,
            'nombre_archivo_pdf' => '',  // Se actualizará después
            'hash_pdf' => '',  // Se actualizará después
            'estado' => 'activa',
            'fecha_emision' => date('Y-m-d H:i:s'),
            'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+' . DIAS_EXPIRACION_CARTA . ' days')),
            'empresa' => EMPRESA_NOMBRE,
            'ip_generacion' => $verificacionService->obtenerIPCliente()
        ];
        
        // Insertar en BD local (Docker)
        $carta_bd_id = $verificacionService->insertarCarta($datos_carta_bd, $deducciones_para_bd);
        
        if (!$carta_bd_id) {
            throw new Exception("Error al registrar carta en sistema de verificación local");
        }
        
        // Enviar datos a GoDaddy vía API
        try {
            $resultado_godaddy = $verificacionService->enviarAGoDaddy($datos_carta_bd, $deducciones_para_bd);
            error_log("Carta sincronizada con GoDaddy. ID local: $carta_bd_id, ID remoto: " . $resultado_godaddy['carta_id']);
        } catch (Exception $e) {
            // Log del error pero no falla la generación de carta
            error_log("ADVERTENCIA: No se pudo sincronizar con GoDaddy: " . $e->getMessage());
            // La carta ya está guardada localmente, así que continuamos
        }
        
        // Generar URL del QR
        $url_qr = $verificacionService->generarURLQR($token_qr);
        
        // Descargar imagen del QR
        $temp_qr_path = __DIR__ . '/../uploads/carta_trabajo/temp_qr_' . $id_carta . '.png';
        $qr_descargado = $verificacionService->descargarImagenQR($url_qr, $temp_qr_path);
        
        if (!$qr_descargado) {
            throw new Exception("Error al generar código QR");
        }
        
        // ========== FIN: Sistema de verificación ==========

        // Construir HTML dinámico de deducciones
        $html_dinamico = "";
        if (!empty($otros_descuentos)) {
            $html_dinamico .= "<li><strong>Otros descuentos:</strong></li>";
            foreach ($otros_descuentos as $desc) {
                $acreedor = htmlspecialchars($desc['acreedor']);
                $monto = number_format($desc['monto'], 2);
                $html_dinamico .= "<li>$acreedor: B/. $monto</li>";
            }
        }

        // Rutas absolutas a las imágenes para Docker
        $path_logo = $_SERVER['DOCUMENT_ROOT'] . '/public/images/carta/logo.png';
        $path_footer = $_SERVER['DOCUMENT_ROOT'] . '/public/images/carta/foot.png';
        
        // Debug: Log de rutas para troubleshooting
        error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
        error_log("Intentando cargar logo desde: " . $path_logo);
        error_log("Intentando cargar footer desde: " . $path_footer);
        
        // Verificar que las imágenes existen
        if (!file_exists($path_logo)) {
            error_log("Logo no encontrado en: " . $path_logo);
            $logo_src = '';
        } else {
            $logo_src = 'file://' . realpath($path_logo);
            error_log("Logo encontrado: " . $logo_src);
        }
        
        if (!file_exists($path_footer)) {
            error_log("Footer no encontrado en: " . $path_footer);
            error_log("Verificando directorio padre: " . dirname($path_footer));
            error_log("Archivos en directorio carta: " . print_r(scandir(dirname($path_footer)), true));
            $footer_src = '';
        } else {
            $footer_src = 'file://' . realpath($path_footer);
            error_log("Footer encontrado: " . $footer_src);
            error_log("Tamaño del archivo footer: " . filesize($path_footer) . " bytes");
        }
        
        $qr_src = 'file://' . realpath($temp_qr_path);

        // HTML del PDF con diseño mejorado
        $html = "
        <style>
            body { 
                font-family: 'DejaVu Sans', Arial, sans-serif; 
                font-size: 10pt; 
                line-height: 1.4;
                margin: 15px;
            }
            .header {
                width: 100%;
                margin-bottom: 20px;
                border-bottom: 2px solid #0066cc;
                padding-bottom: 10px;
            }
            .header-logo {
                float: left;
                width: 200px;
            }
            .header-info {
                float: right;
                text-align: right;
                font-size: 9pt;
                color: #333;
                margin-top: 10px;
            }
            .clear { clear: both; }
            .content {
                margin-top: 10px;
                text-align: justify;
            }
            .content p {
                margin-bottom: 8pt;
            }
            ul {
                margin-left: 30px;
                line-height: 1.5;
            }
            ul li {
                margin-bottom: 3pt;
            }
            .firma {
                margin-top: 30px;
                text-align: left;
            }
            .qr-container {
                text-align: center;
                margin: 20px 0 10px 0;
                padding: 10px;
                border: 1px solid #ddd;
                background-color: #f9f9f9;
            }
            .qr-container img {
                width: 100px;
                height: 100px;
            }
            .qr-container p {
                margin: 5px 0 0 0;
                font-size: 8pt;
                color: #555;
            }
            .footer {
                text-align: center;
                margin-top: 10px;
            }
            .footer img {
                max-width: 100%;
                height: auto;
            }
            .verification-info {
                margin-top: 10px;
                padding-top: 5px;
                border-top: 1px solid #ccc;
                font-size: 7pt;
                color: #666;
                text-align: center;
            }
        </style>

        <!-- ENCABEZADO -->
        <div class='header'>
            <div class='header-logo'>";
        
        if ($logo_src) {
            $html .= "<img src='$logo_src' width='180' alt='Logo Grupo PCR'>";
        } else {
            $html .= "<div style='width: 180px; height: 60px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 10pt; color: #666;'>LOGO PCR</div>";
        }
        
        $html .= "
            </div>
            <div class='header-info'>
                <strong>Grupo PCR</strong><br>
                Tocumen Commercial Park<br>
                Tel: 279-2700<br>
                www.grupopcr.com.pa
            </div>
            <div class='clear'></div>
        </div>";

        $html .= "
        <!-- CONTENIDO -->
        <div class='content'>
            <p style='text-align: right; margin-bottom: 20pt;'>Panamá, $fecha_actual</p>

            <p><strong>A QUIEN CONCIERNE:</strong></p>

            <p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>" . htmlspecialchars($nombre_completo) . "</strong>, portador(a) de la cédula <strong>" . htmlspecialchars($cedula) . "</strong> y seguro social <strong>" . htmlspecialchars($seguro) . "</strong>, labora en nuestra empresa desde el <strong>" . date('d/m/Y', strtotime($fecha_ingreso)) . "</strong>, desempeñando el cargo de <strong>" . htmlspecialchars($cargo) . "</strong>.</p>

            <p>El salario mensual pactado es de <strong>B/. " . number_format($salario, 2) . "</strong>, con las siguientes deducciones aproximadas:</p>
            
            <ul>
                <li>Seguro Social: <strong>B/. " . number_format($desc_seguro, 2) . "</strong></li>
                <li>Seguro Educativo: <strong>B/. " . number_format($desc_educativo, 2) . "</strong></li>
                <li>Impuesto sobre la Renta: <strong>B/. " . number_format($desc_renta, 2) . "</strong></li>
                $html_dinamico
            </ul>

            <p>" . htmlspecialchars($descripcion) . "</p>

            <p>Se expide la presente para los fines que el(la) interesado(a) estime conveniente.</p>
        
            <!-- FIRMA -->
            <div class='firma'>
                <p><strong>Departamento de Recursos Humanos</strong><br>
                Grupo PCR</p>
            </div>
        </div>
        <!-- CÓDIGO QR DE VERIFICACIÓN -->
        <div class='qr-container' width='100'>
            <img src='$qr_src' alt='Código QR de Verificación'>
            <p><strong>Escanea este código para verificar la autenticidad</strong></p>
            <p style='font-size: 7pt; color: #999;'>Ref: " . substr($hash_verificacion, 0, 12) . "</p>
        </div>

        <!-- PIE DE PÁGINA -->";
        /*
        if ($footer_src) {
            $html .= "<div class='footer'><img src='$footer_src' alt='Footer' style='max-width: 600px;'></div>";
        } else {
            $html .= "<div class='footer'><div style='width: 100%; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 10pt; color: #666;'>FOOTER PCR</div></div>";
        }
        */

        $html .= "

        <!-- INFORMACIÓN DE VERIFICACIÓN -->
        <div class='verification-info'>
            <p><strong>VERIFICACIÓN DE AUTENTICIDAD:</strong> Escanee el código QR o visite https://grupopcr.com.pa/carta/</p>
            <p>Hash: " . substr($hash_verificacion, 0, 16) . "... | Código Empleado: " . htmlspecialchars($codigo_empleado) . " | Fecha Emisión: $fecha_actual</p>
        </div>
        ";

        // Generar PDF con mPDF
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'dejavusans',
            'tempDir' => __DIR__ . '/../../tmp/mpdf'
        ]);

        $mpdf->WriteHTML($html);
        $nombreArchivo = 'Carta_' . preg_replace('/[^a-zA-Z0-9]/', "", $nombre_completo) . '_' . $id_carta . '_' . date('Ymd') . '.pdf';
        $ruta_archivo = __DIR__ . '/../uploads/carta_trabajo/' . $nombreArchivo;
        $mpdf->Output($ruta_archivo, \Mpdf\Output\Destination::FILE);
        
        // Eliminar QR temporal
        if (file_exists($temp_qr_path)) {
            unlink($temp_qr_path);
        }

        // Actualizar BD externa con hash del PDF y nombre de archivo
        $hash_pdf = $verificacionService->generarHashPDF($ruta_archivo);
        $pdo_external = $verificacionService->getConnection();
        $stmt_update = $pdo_external->prepare("UPDATE cartas_trabajo_verificacion 
            SET nombre_archivo_pdf = :nombre, hash_pdf = :hash 
            WHERE id = :id");
        $stmt_update->execute([
            ':nombre' => $nombreArchivo,
            ':hash' => $hash_pdf,
            ':id' => $carta_bd_id
        ]);

        // Enviar correo
        if ($email_destino && filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
            $mensaje_correo = "Estimado(a) " . htmlspecialchars($nombre_completo) . ",<br><br>Adjunto encontrará su carta de trabajo solicitada.<br><br>" . 
                             "Esta carta incluye un código QR que permite verificar su autenticidad escaneándolo con cualquier dispositivo móvil.<br><br>" .
                             htmlspecialchars($comentario) . "<br><br>Saludos,<br>Departamento de RRHH - Grupo PCR";
            
            $copias = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
            //$copias = ["pedroarrieta25@hotmail.com"];

            $class->enviar_correo_con_adjunto($email_destino, $copias, "Carta de Trabajo - Grupo PCR", $mensaje_correo, $ruta_archivo);
            echo "<div class='alert alert-success'>
                    <strong>✓ Carta generada exitosamente</strong><br>
                    - Enviada a: " . htmlspecialchars($email_destino) . "<br>
                    - Código QR de verificación incluido<br>
                    - Registrada en sistema de verificación
                  </div>";
            
            $class->aprobar_carta_trabajo($id_carta);
        } else {
            echo "<div class='alert alert-warning'>Carta generada pero no se pudo obtener un email válido del colaborador.</div>";
        }

    } catch (Exception $e) {
        error_log("Error en enviar_carta_pdf: " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }

    $solicitudes = $ver_aprobadas ? $class->solicitudes_aprobadas() : $class->solicitudes_aprobar();
    require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
    exit;
    
}elseif (isset($_GET['carta_trabajo_aprobar'])) {

    $ver_aprobadas = (isset($_GET['ver']) && $_GET['ver'] === 'aprobadas') || (isset($_POST['ver']) && $_POST['ver'] === 'aprobadas');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_formulario'])) {
        $sql = "INSERT INTO carta_trabajo_formulario 
            (carta_id, nombre, cedula, seguro, fecha_ingreso, cargo, salario, desc_seguro, desc_educativo, desc_renta, descripcion)
            VALUES (:carta_id, :nombre, :cedula, :seguro, :fecha_ingreso, :cargo, :salario, :desc_seguro, :desc_educativo, :desc_renta, :descripcion)
            ON DUPLICATE KEY UPDATE 
                nombre = VALUES(nombre),
                cedula = VALUES(cedula),
                seguro = VALUES(seguro),
                fecha_ingreso = VALUES(fecha_ingreso),
                cargo = VALUES(cargo),
                salario = VALUES(salario),
                desc_seguro = VALUES(desc_seguro),
                desc_educativo = VALUES(desc_educativo),
                desc_renta = VALUES(desc_renta),
                descripcion = VALUES(descripcion)";

        $stmt = $class->pdo->prepare($sql);
        $stmt->execute([
            ':carta_id' => $_POST['solicitud_id'],
            ':nombre' => $_POST['nombre'],
            ':cedula' => $_POST['cedula'],
            ':seguro' => $_POST['seguro'],
            ':fecha_ingreso' => $_POST['fecha_ingreso'],
            ':cargo' => $_POST['cargo'],
            ':salario' => $_POST['salario'],
            ':desc_seguro' => $_POST['desc_seguro'],
            ':desc_educativo' => $_POST['desc_educativo'],
            ':desc_renta' => $_POST['desc_renta'],
            ':descripcion' => $_POST['descripcion']
        ]);

        // Primero eliminar todos los descuentos existentes para esta carta
        $sql_delete_desc = "DELETE FROM carta_trabajo_descuentos WHERE carta_id = :carta_id";
        $stmt_delete = $class->pdo->prepare($sql_delete_desc);
        $stmt_delete->execute([':carta_id' => $_POST['solicitud_id']]);

        // Luego insertar los nuevos descuentos
        if (!empty($_POST['otros_descuentos']) && is_array($_POST['otros_descuentos'])) {
            $sql_insert_desc = "INSERT INTO carta_trabajo_descuentos (carta_id, acreedor, monto) VALUES (:carta_id, :acreedor, :monto)";
            $stmt_desc = $class->pdo->prepare($sql_insert_desc);

            foreach ($_POST['otros_descuentos'] as $descuento) {
                if (!empty($descuento['acreedor']) && !empty($descuento['monto']) && is_numeric($descuento['monto'])) {
                    $stmt_desc->execute([
                        ':carta_id' => $_POST['solicitud_id'],
                        ':acreedor' => trim($descuento['acreedor']),
                        ':monto' => $descuento['monto']
                    ]);
                }
            }
        }


        echo "<script>alert('Datos guardados correctamente');</script>";
    }

    $solicitudes = $ver_aprobadas ? $class->solicitudes_aprobadas() : $class->solicitudes_aprobar();

    require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
    exit();

    /* 
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_carta_pdf'])) {

        $id_carta = $_POST['solicitud_id'];
        $comentario = $_POST['comentario'] ?? '';

        // Obtener los datos del colaborador y de la carta
        $datos = $class->get_datos_formulario_carta($id_carta);
        if (!$datos) {
            echo "<div class='alert alert-danger'>No se encontraron datos para generar la carta.</div>";
        } else {

            $fecha_actual = date("d/m/Y");
            extract($datos); // $nombre, $cedula, $seguro, etc.

            $html = "
                <style> body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; } </style>
                <p>Panamá, $fecha_actual</p>
                <p><strong>A quien pueda interesar:</strong></p>
                <p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>$nombre</strong>, con cédula <strong>$cedula</strong> y seguro social <strong>$seguro</strong>, labora en nuestra empresa desde el <strong>$fecha_ingreso</strong>, desempeñando el cargo de <strong>$cargo</strong>.</p>
                <p>El salario mensual pactado es de B/. $salario, con las siguientes deducciones aproximadas:</p>
                <ul>
                    <li>Seguro Social: B/. $desc_seguro</li>
                    <li>Seguro Educativo: B/. $desc_educativo</li>
                    <li>Impuesto sobre la Renta: B/. $desc_renta</li>
                </ul>
                <p>$descripcion</p>
                <br><br>
                <p><strong>Departamento de Planilla</strong></p>
            ";

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdfOutput = $dompdf->output();
            $nombreArchivo = 'Carta_' . preg_replace("/[^a-zA-Z0-9]/", "", $nombre) . '.pdf';
            $ruta_archivo = __DIR__ . '/../uploads/carta_trabajo/' . $nombreArchivo;
            file_put_contents($ruta_archivo, $pdfOutput);

            // Obtener el correo del colaborador
            $get_email_colab = $class->get_email_colaborador($id_carta);
            $email_destino = $get_email_colab['email'] ?? '';

            if ($email_destino) {
                $mensaje_correo = "Estimado $nombre,<br><br>Adjunto encontrará su carta de trabajo solicitada. $comentario<br><br>Saludos,<br>RRHH";
                $copias = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];
                //$copias = ["pedroarrieta25@hotmail.com"];

                // Enviar con adjunto $email_destino
                $class->enviar_correo_con_adjunto($email_destino, $copias, "Carta de Trabajo", $mensaje_correo, $ruta_archivo);
                echo "<div class='alert alert-success'>Carta generada y enviada exitosamente a $email_destino.</div>";
                // pasar de estatus la carta a aprobada
                $class->aprobar_carta_trabajo($id_carta);

            } else {
                echo "<div class='alert alert-warning'>No se pudo obtener el correo del colaborador.</div>";
            }
        }

    }

    $solicitudes = $class->solicitudes_aprobar();

    require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
    exit();

} */

}elseif(isset($_GET['incapacidad'])){

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
        
        $code_user = isset($_SESSION['code']) ? ltrim($_SESSION['code'], '0') : 0;
        $descripcion = trim($_POST['descripcion']);
        $fecha_retroactiva = !empty($_POST['fecha_retroactiva']) ? $_POST['fecha_retroactiva'] : null;
        $file_add = "";
    
        // Carpeta de almacenamiento
        $upload_dir = __DIR__ . '/../uploads/incapacidades/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
    
        if (isset($_FILES['archivo_incapacidad']) && $_FILES['archivo_incapacidad']['error'] === UPLOAD_ERR_OK) {
            $upload_error = '';
            $file_add = rrhh_process_incapacidad_upload($_FILES['archivo_incapacidad'], $upload_dir, $upload_error);
            if ($file_add === '') {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($upload_error) . "</div>";
                exit;
            }
        }
    
        // Insertar en la base de datos usando el modelo
        if ($class->insertar_incapacidad($code_user, $descripcion, $file_add, 1, 0, $fecha_retroactiva)) {

            $dartos_cola = $class->datos_colaborador();
            foreach ($dartos_cola as $key => $value) {
                $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
                $codigo = $value['codigo_empleado'];
            }

            $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
            ha adjuntado una incapacidad, ingrese a la app pcr para visualizar o descargar la misma. <br>';

            $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
            //$copiacoo = ["pedro.arrieta@grupopcr.com.pa", "rrhhgpcr@grupopcr.com.pa"];
            //$copia = ["pedro.arrieta@grupopcr.com.pa"];
        
            $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Incapacidad de '".$nombre_comple."' ", $mensaje);
            
            echo "<div class='alert alert-success'>Incapacidad guardada correctamente.</div>";

        } else {
            echo "<div class='alert alert-danger'>Error al guardar la incapacidad en la base de datos.</div>";
        }
    }

    $incapacidad = $class->incapacidad();

    require_once __DIR__ . '/../views/incapacidad.php';
    exit();

}elseif(isset($_GET['incapacidad_privada'])){

    $codigo_sesion_privado = trim($_SESSION['code'] ?? '');
    $acceso_incapacidad_privada = ($codigo_sesion_privado === '002475' || rrhh_normalize_code($codigo_sesion_privado) === '2475');
    if (!$acceso_incapacidad_privada) {
        header("Location: " . BASE_URL_CONTROLLER . "/RRHHController.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'])) {
        $code_user = rrhh_normalize_code($codigo_sesion_privado);
        $descripcion = trim($_POST['descripcion']);
        $fecha_retroactiva = !empty($_POST['fecha_retroactiva']) ? $_POST['fecha_retroactiva'] : null;
        $file_add = "";

        $upload_dir = __DIR__ . '/../uploads/incapacidades/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (isset($_FILES['archivo_incapacidad']) && $_FILES['archivo_incapacidad']['error'] === UPLOAD_ERR_OK) {
            $upload_error = '';
            $file_add = rrhh_process_incapacidad_upload($_FILES['archivo_incapacidad'], $upload_dir, $upload_error);
            if ($file_add === '') {
                echo "<div class='alert alert-danger'>" . htmlspecialchars($upload_error) . "</div>";
                $incapacidad = $class->incapacidad_por_code_user($code_user);
                require_once __DIR__ . '/../views/incapacidad_privada.php';
                exit();
            }
        }

        if ($class->insertar_incapacidad($code_user, $descripcion, $file_add, 1, 0, $fecha_retroactiva)) {
            echo "<div class='alert alert-success'>Incapacidad privada guardada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al guardar la incapacidad en la base de datos.</div>";
        }
    }

    $incapacidad = $class->incapacidad_por_code_user(rrhh_normalize_code($codigo_sesion_privado));
    require_once __DIR__ . '/../views/incapacidad_privada.php';
    exit();

}elseif(isset($_GET['incapacidad_vrrhh'])){

    if (isset($_POST['borrar_incapacidad']) && isset($_POST['incapacidad_id'])) {
        $puede_borrar = ($tipo_usuario == 1 || $tipo_usuario == 4);
        if ($puede_borrar) {
            $id_borrar = (int) $_POST['incapacidad_id'];
            if ($id_borrar > 0 && $class->delete_incapacidad($id_borrar)) {
                echo "<div class='alert alert-success'>Incapacidad eliminada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>No se pudo eliminar la incapacidad.</div>";
            }
        }
    }

    if (isset($_POST['incapacidad_id']) && !isset($_POST['borrar_incapacidad'])) {

        //echo 'paso por dentro del controlador '.$_POST['incapacidad_id'];

        $class->update_incapacidad($_POST['incapacidad_id']);

        /*
        $dartos_cola = $class->datos_colaborador();
        foreach ($dartos_cola as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $email = $value['email'];
        }
            */

        // Obtener el correo del colaborador
        $get_email_colab = $class->get_email_colaborador_incapacidad($_POST['incapacidad_id']);

        if ($get_email_colab) {
            $nombre_comple = $get_email_colab['nombre'] . ' ' . $get_email_colab['apellido'];
            $email = $get_email_colab['email'];
        }

        $mensaje = 'Estimado '.$nombre_comple.' <br> 
        Se ha revisado su incapacidad por parte del departamento de RRHH <br>';

        $copiacoo = ["sofia.macias@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];
        //$copiacoo = ["pedroarrieta25@hotmail.com"];

        $class->enviar_correo($email, $copiacoo, "Incapacidad Revisada ", $mensaje);
        
        echo "<div class='alert alert-success'>Incapacidad Revisada.</div>";
    
    }

    $incapacidad = $class->incapacidad_vrrhh();

    require_once __DIR__ . '/../views/incapacidad_vrrhh.php';
    exit();

}elseif (isset($_GET['solicitud_permiso'])) {

    if (isset($_POST['solicitud_permiso'])) {

        $id_jefe = !empty($_POST['id_jefe']) ? (int)$_POST['id_jefe'] : NULL;
        $descripcion = $_POST['descripcion'];
        $tipo_licencia = $_POST['tipo_licencia'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        $archivo_ruta = null;
        $nombre_archivo = null;
        $error_archivo_permiso = null;
        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $err = $_FILES['archivo_adjunto']['error'];
            if ($err !== UPLOAD_ERR_OK) {
                $mensajes_err = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo supera el límite permitido por el servidor.',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande.',
                    UPLOAD_ERR_PARTIAL => 'El archivo se subió solo parcialmente. Intente de nuevo.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Error temporal en el servidor. Intente más tarde.',
                    UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en el servidor.',
                    UPLOAD_ERR_EXTENSION => 'La subida fue bloqueada por una extensión del servidor.',
                ];
                $error_archivo_permiso = $mensajes_err[$err] ?? 'Error al subir el archivo (código ' . $err . ').';
            } else {
                $upload_dir = __DIR__ . '/../uploads/permisos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $nombre_archivo = basename($_FILES['archivo_adjunto']['name']);
                $archivo_destino = $upload_dir . time() . '_' . $nombre_archivo;
                if (move_uploaded_file($_FILES['archivo_adjunto']['tmp_name'], $archivo_destino)) {
                    $archivo_ruta = $archivo_destino;
                    $nombre_archivo = basename($archivo_destino);
                } else {
                    $error_archivo_permiso = 'No se pudo guardar el archivo. Compruebe permisos o espacio en el servidor.';
                }
            }
        }
        if ($error_archivo_permiso !== null) {
            echo "<div class='alert alert-danger'><strong>Error en el archivo:</strong> " . htmlspecialchars($error_archivo_permiso) . " No se guardó la solicitud. Intente de nuevo sin archivo o con uno más pequeño.</div>";
            $select_jefe = $class->select_jefe();
            $permisos = $class->select_permisos();
            $mis_vacas = $class->mis_vacaciones();
            $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);
            require_once __DIR__ . '/../views/solicitud_permiso.php';
            exit;
        }

        if ($class->ya_envio_permiso_tipo_hoy($_SESSION['code'], $tipo_licencia)) {
            $error_permiso_duplicado = 'Solo puedes enviar una solicitud de "' . htmlspecialchars($tipo_licencia) . '" por día. Ya enviaste una hoy; si es otro caso, envía mañana.';
            $select_jefe = $class->select_jefe();
            $permisos = $class->select_permisos();
            $mis_vacas = $class->mis_vacaciones();
            $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);
            require_once __DIR__ . '/../views/solicitud_permiso.php';
            exit;
        }

        $class->insertar_permiso($id_jefe, $descripcion, $tipo_licencia, $fecha_inicio, $fecha_fin, $nombre_archivo);

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

        if($tipo_licencia == 'Vacaciones'){

        /***** ######################### email para el enargado **************************** */

        $cantidad_dias = 0;
        $inicio = new DateTime($fecha_inicio);
        $fin = new DateTime($fecha_fin);
        $diferencia = $inicio->diff($fin);
        $cantidad_dias = $diferencia->days;
        $cantidad_dias = $cantidad_dias + 1;

        $mensaje = '
        <h4 style="color:rgb(250, 11, 2);">Aprobación pendiente: Solicitud de permiso de colaborador </h4>

        <p>El colaborador <strong>' . $nombre_comple . '</strong> (Código de empleado: <strong>' . $codigo . '</strong>) ha solicitado un permiso del tipo <strong>' . $tipo_licencia . '</strong>.</p>

        <p><strong>Periodo solicitado:</strong> desde el <strong>' . $fecha_inicio . '</strong> hasta el <strong>' . $fecha_fin . '</strong></p>

        <p><strong>Cantidad de dias:' . $cantidad_dias . '</strong></p>

        <p><strong>Descripción del permiso:</strong><br>' . nl2br($descripcion) . '</p>

        <h4 style="color:rgb(250, 11, 2);">Para aprobar o declinar esta solicitud, ingrese al app Gente PCR. En la opcion de Administrar Permisos podra ver la solicitud y aprobarla o declinarla.</h4>
        ';

        //<p><a href="https://apppcr.net/app/views/aprobar_vacaciones.php?codigo_empleado=' . $codigo . '&nombre_completo=' . $nombre_comple . '&fecha_desde='.$fecha_inicio.'&fecha_hasta='.$fecha_fin.'&cantidad_dias='.$cantidad_dias.'">Aprobar o Declinar Solicitud de Permiso</a></p>
        $mensaje .= '

        <p><a href="https://apppcr.net/>Gente PCR</a></p>

        <p><strong>Canal de contacto:</strong></p>
        <ul>
            <li>Email: <a href="mailto:abi.pineda@grupopcr.com.pa">abi.pineda@grupopcr.com.pa</a></li>
        </ul>

        <p><em>Este es un mensaje automático. Por favor, no responda a este correo. Utilice los canales indicados para cualquier comunicación.</em></p>
        ';

        $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa", $email_feje];
        //$copia = ["pedroarrieta25@hotmail.com"];

        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje); 

        }else{

        $mensaje = 'El colaborador  '.$nombre_comple.' con codigo de empleado: '.$codigo.'<br> 
        ha solicitado un permiso tipo '.$tipo_licencia.' <br>
        Fechas del permiso desde '.$fecha_inicio.' hasta '.$fecha_fin.' <br>
        Descripcion del permiso: '.$descripcion.'';

        $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa", $email_feje];
        //$copia = ["pedroarrieta25@hotmail.com"];
        //$copia = ["pedro.arrieta@grupopcr.com.pa"];
    
        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje);

        }

        echo "<div class='alert alert-success'>Permiso solicitado correctamente.</div>";
        
    }

    $select_jefe = $class->select_jefe();
    
    $permisos = $class->select_permisos();

    $mis_vacas = $class->mis_vacaciones();

    $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);

    require_once __DIR__ . '/../views/solicitud_permiso.php';
    exit();

// ============================================
// NUEVO SISTEMA R-PERMISOS (PARALELO)
// ============================================
}elseif(isset($_GET['solicitud_r_permiso'])){

    // Accesible para todos los usuarios (antes solo admin)

    if (isset($_POST['solicitud_permiso'])) {

        $id_jefe = !empty($_POST['id_jefe']) ? $_POST['id_jefe'] : NULL;
        $descripcion = $_POST['descripcion'];
        $tipo_licencia = $_POST['tipo_licencia'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $archivo_adjunto = null;
        $error_archivo = null;

        // Manejo de archivo adjunto: validar antes de insertar; si el usuario envió archivo y falla, no guardar registro
        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $err = $_FILES['archivo_adjunto']['error'];
            if ($err !== UPLOAD_ERR_OK) {
                $mensajes_error = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo supera el límite permitido por el servidor.',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo es demasiado grande.',
                    UPLOAD_ERR_PARTIAL => 'El archivo se subió solo parcialmente. Intente de nuevo.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Error temporal en el servidor. Intente más tarde.',
                    UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en el servidor.',
                    UPLOAD_ERR_EXTENSION => 'La subida fue bloqueada por una extensión del servidor.',
                ];
                $error_archivo = $mensajes_error[$err] ?? 'Error al subir el archivo (código ' . $err . ').';
            } else {
                $upload_dir = __DIR__ . '/../uploads/permisos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $archivo_nombre = basename($_FILES['archivo_adjunto']['name']);
                $archivo_destino = $upload_dir . time() . '_' . $archivo_nombre;
                if (move_uploaded_file($_FILES['archivo_adjunto']['tmp_name'], $archivo_destino)) {
                    $archivo_adjunto = basename($archivo_destino);
                } else {
                    $error_archivo = 'No se pudo guardar el archivo. Compruebe permisos o espacio en el servidor.';
                }
            }
        }

        if ($error_archivo !== null) {
            echo "<div class='alert alert-danger'><strong>Error en el archivo:</strong> " . htmlspecialchars($error_archivo) . " No se guardó la solicitud. Intente de nuevo sin archivo o con uno más pequeño.</div>";
            $select_jefe = $class->r_select_jefe();
            $permisos = $class->select_permisos();
            $mis_vacas = $class->mis_vacaciones();
            $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);
            require_once __DIR__ . '/../views/solicitud_r_permiso.php';
            exit;
        }

        if ($class->ya_envio_permiso_tipo_hoy($_SESSION['code'], $tipo_licencia)) {
            $error_permiso_duplicado = 'Solo puedes enviar una solicitud de "' . htmlspecialchars($tipo_licencia) . '" por día. Ya enviaste una hoy; si es otro caso, envía mañana.';
            $select_jefe = $class->r_select_jefe();
            $permisos = $class->select_permisos();
            $mis_vacas = $class->mis_vacaciones();
            $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);
            require_once __DIR__ . '/../views/solicitud_r_permiso.php';
            exit;
        }

        $class->insertar_permiso($id_jefe, $descripcion, $tipo_licencia, $fecha_inicio, $fecha_fin, $archivo_adjunto);

        // Enviar correo si hay jefe asignado
        if (!empty($id_jefe)) {
            $dartos_cola = $class->datos_colaborador();
            $nombre_comple = "";
            $codigo = "";
            if (!empty($dartos_cola) && isset($dartos_cola[0])) {
                $nombre_comple = $dartos_cola[0]['nombre'] . ' ' . $dartos_cola[0]['apellido'];
                $codigo = $dartos_cola[0]['codigo_empleado'];
            }

            $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
            ha solicitado un permiso tipo '.$tipo_licencia.' <br> 
            <br>
            Descripción: '.$descripcion.' <br>
            Fecha inicio: '.$fecha_inicio.' <br>
            Fecha fin: '.$fecha_fin.' <br> ';

            $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
            //$copia = ["pedroarrieta25@hotmail.com"];
            //$copia = ["pedro.arrieta@grupopcr.com.pa"];
        
            $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje);

        }

        echo "<div class='alert alert-success'>Permiso solicitado correctamente.</div>";
        
    }

    // Usar el nuevo método r_select_jefe que consulta supervisores_personal_cargo
    $select_jefe = $class->r_select_jefe();
    
    $permisos = $class->select_permisos();

    $mis_vacas = $class->mis_vacaciones();

    $permisos_pendientes = $class->permisos_pendientes_por_usuario($_SESSION['code']);

    require_once __DIR__ . '/../views/solicitud_r_permiso.php';
    exit();


}elseif(isset($_GET['solicitud_permiso_admin'])){

    // Eliminar permiso
    if (isset($_POST['eliminar_permiso'])) {
        $permiso_id = (int)($_POST['permiso_id'] ?? 0);
        
        if ($permiso_id > 0) {
            if ($class->eliminar_permiso($permiso_id)) {
                echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Solicitud de permiso eliminada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error al eliminar la solicitud.</div>";
            }
        }
    }

    if (isset($_POST['aprobar_permiso'])) {
        $class->update_permiso($_POST['respuesta_jefe'], $_POST['comentario_jefe'], $_POST['permiso_id']);
        //echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

        $id_permiso = $_POST['permiso_id'];

        //$dartos_cola = $class->datos_colaborador();

        $get_email_colab = $class->get_email_permiso($id_permiso);

        if ($get_email_colab) {
            $nombre_comple = $get_email_colab['nombre'] . ' ' . $get_email_colab['apellido']; 
            $email = $get_email_colab['email'];
        } else {
            $nombre_comple = 'Colaborador';
            $email = '';
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


        $copiacoo = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje);
    
        echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

    }

    $permisos = $class->select_permisos_all();

    require_once __DIR__ . '/../views/solicitud_permiso_aprobar.php';
    exit();

}elseif (isset($_GET['administrar_permiso_admin'])) {

    // Obtener el supervisor seleccionado (solo para admin tipo 1)
    $supervisor_seleccionado = null;
    $todos_supervisores = [];
    
    if ($tipo_usuario == 1) {
        $todos_supervisores = $class->get_todos_supervisores();
        
        // Si se seleccionó un supervisor, filtrar por ese
        if (isset($_GET['supervisor']) && !empty($_GET['supervisor'])) {
            $supervisor_seleccionado = $_GET['supervisor'];
        }
    }

    if (isset($_POST['aprobar_permiso'])) {
        $class->update_permiso($_POST['respuesta_jefe'], $_POST['comentario_jefe'], $_POST['permiso_id']);
        //echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

        $id_permiso = $_POST['permiso_id'];

        //$dartos_cola = $class->datos_colaborador();

        $get_email_colab = $class->get_email_permiso($id_permiso);

        if ($get_email_colab) {
            $nombre_comple = $get_email_colab['nombre'] . ' ' . $get_email_colab['apellido']; 
            $email = $get_email_colab['email'];
        } else {
            $nombre_comple = 'Colaborador';
            $email = '';
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


        $copiacoo = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje);
    
        echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

    }

    // Obtener permisos según el filtro
    if ($tipo_usuario == 1 && $supervisor_seleccionado) {
        // Admin seleccionó un supervisor específico
        $permisos = $class->select_permisos_por_supervisor($supervisor_seleccionado);
    } elseif ($tipo_usuario == 6) {
        // Supervisor: ver permisos de su personal a cargo (supervisores_personal_cargo), misma lógica que Mi Personal
        $permisos = $class->select_permisos_por_supervisor($_SESSION['code']);
    } else {
        // Admin sin filtro o RRHH (tipo 3, 4): encargados_colab + id_jefe
        $permisos = $class->select_permisos_all_admin($_SESSION['code']);
    }
    
    // Asegurar que $permisos siempre sea un array
    if (!is_array($permisos)) {
        $permisos = [];
    }

    require_once __DIR__ . '/../views/administrar_permiso_admin.php';
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

// ============================================
// NUEVO SISTEMA R-VACACIONES (PARALELO)
// ============================================
}elseif (isset($_GET['solicitud_r_vacaciones'])) {

    // Solo admin puede acceder (tipo_usuario == 1)
    $tipo_usuario_actual = $userModel->get_tyte_user();
    if ($tipo_usuario_actual != 1) {
        header("Location: " . BASE_URL_CONTROLLER . "/RRHHController.php");
        exit();
    }

    if (isset($_POST['solicitud_vacaciones'])) {

        $id_jefe = !empty($_POST['id_jefe']) ? $_POST['id_jefe'] : NULL;
        $descripcion = $_POST['descripcion'];
    
        $class->insertar_vacaciones($id_jefe, $descripcion);
    
        echo "<div class='alert alert-success'>Vacaciones solicitadas correctamente.</div>";
        
    }

    // Usar el nuevo método r_select_jefe que consulta supervisores_personal_cargo
    $select_jefe = $class->r_select_jefe();
    $permisos = $class->select_vacaciones();

    require_once __DIR__ . '/../views/solicitud_r_vacaciones.php';
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
        $monto = isset($_POST['monto']) ? trim($_POST['monto']) : 0;
        $plazo = isset($_POST['plazo']) ? trim($_POST['plazo']) : 0;
        $forma_pago = isset($_POST['forma_pago']) ? trim($_POST['forma_pago']) : '';
    
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
        if ($class->insertar_calamidades($code_user, $descripcion, $file_add, $monto , $plazo, $forma_pago)) {

            $dartos_cola = $class->datos_colaborador();
            foreach ($dartos_cola as $key => $value) {
                $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
                $codigo = $value['codigo_empleado'];
            }

            $mensaje = 'El colaborador '.$nombre_comple.' con codigo '.$codigo.' 
            ha registrado una calamidad. <br>
            Comentarios del colaborador: '.$descripcion.' <br>
            Monto indicado: '.$monto.' <br>
            Plazo indicado: '.$plazo.' <br>
            Forma de pago indicada: '.$forma_pago.' <br>
            ';

            $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
            //$copia = ["pedro.arrieta@grupopcr.com.pa"];
            //$copia = ["pedroarrieta25@hotmail.com"];
        
            $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Calamidad de '".$nombre_comple."' ", $mensaje);

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
        $calamidad_id = (int) $_POST['calamidad_id'];
        $nuevo_stat = 2;
        $mensaje_flash = '';

        if (isset($_POST['revisado_calamidad'])) {
            $nuevo_stat = 2;
            $mensaje_flash = 'Calamidad marcada como Revisada.';
        } elseif (isset($_POST['aprobado_calamidad'])) {
            $nuevo_stat = 3;
            $mensaje_flash = 'Calamidad Aprobada.';
        } elseif (isset($_POST['rechazar_calamidad'])) {
            $nuevo_stat = 4;
            $mensaje_flash = 'Calamidad Rechazada.';
        }

        if ($calamidad_id > 0 && $mensaje_flash !== '') {
            $class->update_calamidad($calamidad_id, $nuevo_stat);

            $get_email_colab = $class->get_email_calamidad($calamidad_id);
            if ($get_email_colab) {
                $nombre_comple = ($get_email_colab['nombre'] ?? '') . ' ' . ($get_email_colab['apellido'] ?? '');
                $email = $get_email_colab['email'] ?? '';
                $copia = ["sofia.macias@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"];
                if ($nuevo_stat == 2) {
                    $mensaje = 'Estimado(a) ' . $nombre_comple . ' <br><br> Su solicitud de calamidad ha sido <b>revisada</b> por parte del departamento RRHH. <br><br> Saludos.';
                    $class->enviar_correo($email, $copia, "Calamidad revisada", $mensaje);
                } elseif ($nuevo_stat == 3) {
                    $mensaje = 'Estimado(a) ' . $nombre_comple . ' <br><br> Le informamos que su solicitud de calamidad ha sido <b>aprobada</b> por parte del departamento RRHH. <br><br> Saludos.';
                    $class->enviar_correo($email, $copia, "Calamidad aprobada", $mensaje);
                } elseif ($nuevo_stat == 4) {
                    $mensaje = 'Estimado(a) ' . $nombre_comple . ' <br><br> Le informamos que su solicitud de calamidad ha sido <b>rechazada</b> por parte del departamento RRHH. <br><br> Saludos.';
                    $class->enviar_correo($email, $copia, "Calamidad rechazada", $mensaje);
                }
            }

            echo "<div class='alert alert-success'>" . htmlspecialchars($mensaje_flash) . "</div>";
        }
    }

    $calamidades = $class->calamidades_rrhh();

    require_once __DIR__ . '/../views/calamidad_rrhh.php';
    exit();

}elseif(isset($_GET['uniforme'])){

    // Eliminar/Cancelar solicitud de uniforme
    if (isset($_POST['eliminar_uniforme'])) {
        $uniforme_id = (int)($_POST['uniforme_id'] ?? 0);
        
        if ($uniforme_id > 0) {
            if ($class->eliminar_uniforme($uniforme_id)) {
                echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Solicitud cancelada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error al cancelar la solicitud. Solo puede cancelar solicitudes que no hayan sido entregadas.</div>";
            }
        }
    }

    // Procesar solicitud de uniformes (múltiples productos)
    if (isset($_POST['solicitar_uniforme'])) {
        $observacion = $_POST['observacion'] ?? '';
        $productos_json = $_POST['productos'] ?? '';
        
        if (!empty($productos_json)) {
            $productos = json_decode($productos_json, true);
            
            if (is_array($productos) && count($productos) > 0) {
                if ($class->solicitar_uniformes_multiples($productos, $observacion)) {
                    echo "<div class='alert alert-success'>Solicitud de uniformes enviada correctamente. Total: " . count($productos) . " producto(s).</div>";
                    
                    // Enviar correo a RRHH
                    $datos_cola = $class->datos_colaborador();
                    if (!empty($datos_cola)) {
                        $nombre_completo = $datos_cola[0]['nombre'] . ' ' . $datos_cola[0]['apellido'];
                        $codigo_emp = $datos_cola[0]['codigo_empleado'];
                        
                        // Construir lista de productos
                        $lista_productos = "<ul>";
                        foreach ($productos as $producto) {
                            $lista_productos .= "<li><b>" . ucfirst($producto['tipo']) . "</b> - Talla: " . $producto['talla'] . " - Cantidad: " . $producto['cantidad'] . "</li>";
                        }
                        $lista_productos .= "</ul>";
                        
                        $mensaje = "El colaborador <b>$nombre_completo</b> (Código: $codigo_emp) ha solicitado uniformes:<br><br>
                                    <b>Productos solicitados:</b><br>
                                    $lista_productos
                                    <br>
                                    <b>Observaciones:</b> $observacion<br><br>
                                    Por favor, revise la solicitud en el sistema.";
                        
                        $copia = ["abi.pineda@grupopcr.com.pa", "yissell.perez@grupopcr.com.pa"]; 
                        //$copia = ["pedroarrieta25@hotmail.com"];
                        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Nueva solicitud de uniformes", $mensaje);
                    }
                } else {
                    echo "<div class='alert alert-danger'>Error al procesar la solicitud.</div>";
                }
            } else {
                echo "<div class='alert alert-warning'>Debe agregar al menos un producto al carrito.</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Debe agregar al menos un producto al carrito.</div>";
        }
    }

    // Colaborador ve solo sus solicitudes
    $uniformes = $class->uniformes();

    require_once __DIR__ . '/../views/uniforme_rrhh.php';
    exit();

}elseif(isset($_GET['uniforme_vrrhh'])){

    // Eliminar/Cancelar solicitud de uniforme (RRHH)
    if (isset($_POST['eliminar_uniforme'])) {
        $uniforme_id = (int)($_POST['uniforme_id'] ?? 0);
        
        if ($uniforme_id > 0) {
            // RRHH puede eliminar sin restricciones de código de empleado
            $stmt = $pdo->prepare("
                UPDATE uniformes 
                SET stat = 0
                WHERE id = :uniforme_id 
                AND stat IN (1, 2)
            ");
            $stmt->bindParam(':uniforme_id', $uniforme_id, PDO::PARAM_INT);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                echo "<div class='alert alert-success'><i class='bi bi-check-circle'></i> Solicitud eliminada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'><i class='bi bi-x-circle'></i> Error al eliminar la solicitud. Solo puede eliminar solicitudes que no hayan sido entregadas.</div>";
            }
        }
    }

    // Actualizar estado de uniforme (RRHH)
    if (isset($_POST['actualizar_uniforme'])) {
        $uniforme_id = (int)($_POST['uniforme_id'] ?? 0);
        $nuevo_estado = (int)($_POST['nuevo_estado'] ?? 0);
        
        if ($uniforme_id > 0 && in_array($nuevo_estado, [1, 2, 3], true)) {
            if ($class->update_uniforme($uniforme_id, $nuevo_estado)) {
                $estado_texto = match($nuevo_estado) {
                    1 => 'solicitado',
                    2 => 'en proceso',
                    3 => 'entregado',
                    default => 'desconocido'
                };
                echo "<div class='alert alert-success'>Uniforme marcado como <b>$estado_texto</b>.</div>";
                
                // Si el estado es ENTREGADO (3), enviar correo al colaborador
                if ($nuevo_estado == 3) {
                    try {
                        // Obtener datos del uniforme y del colaborador
                        $stmt = $pdo->prepare("
                            SELECT 
                                u.tipo, u.talla, u.cantidad, u.codigo_empleado,
                                e.nombre, e.apellido, e.email
                            FROM uniformes u
                            INNER JOIN empleados e ON u.codigo_empleado COLLATE utf8mb4_unicode_ci = e.codigo_empleado COLLATE utf8mb4_unicode_ci
                            WHERE u.id = :uniforme_id
                        ");
                        $stmt->execute([':uniforme_id' => $uniforme_id]);
                        $uniforme_data = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($uniforme_data && !empty($uniforme_data['email'])) {
                            $nombre_completo = $uniforme_data['nombre'] . ' ' . $uniforme_data['apellido'];
                            $tipo_uniforme = ucfirst($uniforme_data['tipo']);
                            $talla = $uniforme_data['talla'];
                            $cantidad = $uniforme_data['cantidad'] ?? 1;
                            
                            $mensaje = "Estimado(a) <b>$nombre_completo</b>,<br><br>
                                        Le informamos que su uniforme ha sido <b>ENTREGADO</b>.<br><br>
                                        <b>Detalles del uniforme:</b><br>
                                        • Tipo: $tipo_uniforme<br>
                                        • Talla: $talla<br>
                                        • Cantidad: $cantidad unidad(es)<br><br>
                                        Saludos,<br>
                                        <b>Departamento de RRHH</b><br>
                                        Grupo PCR";
                            
                            $copia = [
                                "abi.pineda@grupopcr.com.pa",
                                "sofia.macias@grupopcr.com.pa",
                                "yissell.perez@grupopcr.com.pa"
                                //"pedroarrieta25@hotmail.com"
                            ];
                            
                            $class->enviar_correo($uniforme_data['email'], $copia, "Tu uniforme está listo - Grupo PCR", $mensaje);
                            
                            echo "<div class='alert alert-info'><i class='bi bi-envelope-check'></i> Correo enviado al colaborador.</div>";
                        }
                    } catch (Exception $e) {
                        error_log("Error al enviar correo de uniforme entregado: " . $e->getMessage());
                    }
                }
                
            } else {
                echo "<div class='alert alert-danger'>Error al actualizar el estado.</div>";
            }
        }
    }

    // RRHH ve todas las solicitudes
    $uniformes = $class->uniformes_vrrhh();

    require_once __DIR__ . '/../views/uniforme_vrrhh.php';
    exit();

}else {
    $code_lomg = strlen($_SESSION['code']);
    require_once __DIR__ . '/../views/rrhh.php';
}
