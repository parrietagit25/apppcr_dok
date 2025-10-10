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

$todos_datos = $class->datos_colaborador();
$nombre = "";
foreach ($todos_datos as $key => $value) {
    $nombre = $value['nombre']; //. ' ' .$value['apellido'];
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

    $copia = ["abi.pineda@grupopcr.com.pa"];
    //$copia = ["pedro.arrieta@grupopcr.com.pa"];

    $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Actualizacion de Datos", $mensaje);
    echo "<div class='alert alert-success'>Solicitud de actualizacion de datos enviada.</div>";
    $todos_datos = $class->datos_colaborador();
    require_once __DIR__ . '/../views/colaborador.php';
    exit();
    
} elseif (isset($_GET['mis_vacaciones'])) {
    $mis_vacas = $class->mis_vacaciones();
    $all_vacas = $class->mis_vacaciones_all_employe();
    $all_vacas_gerentes = $class->mis_vacaciones_all_employe_gerentes($_SESSION['code']);
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

        $copia = ["abi.pineda@grupopcr.com.pa"];
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

    if (!$id_carta) {
        echo "<div class='alert alert-danger'>ID de solicitud inválido.</div>";
        $solicitudes = $class->solicitudes_aprobar();
        require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
        exit;
    }

    // Obtener los datos del colaborador y de la carta
    $datos = $class->get_datos_formulario_carta($id_carta);

    if (!$datos) {
        echo "<div class='alert alert-danger'>No se encontraron datos para generar la carta.</div>";
        $solicitudes = $class->solicitudes_aprobar();
        require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
        exit;
    }

    try {
        $fecha_actual = date("d/m/Y");
        
        // Extraer datos (usar acceso explícito en lugar de extract)
        $nombre = $datos['nombre'] ?? '';
        $apellido = $datos['apellido'] ?? '';
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
                font-size: 11pt; 
                line-height: 1.5;
                margin: 20px;
            }
            .header {
                width: 100%;
                margin-bottom: 30px;
                border-bottom: 2px solid #0066cc;
                padding-bottom: 15px;
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
                margin-top: 20px;
                text-align: justify;
            }
            .content p {
                margin-bottom: 12pt;
            }
            ul {
                margin-left: 30px;
                line-height: 1.8;
            }
            ul li {
                margin-bottom: 5pt;
            }
            .firma {
                margin-top: 50px;
                text-align: left;
            }
            .qr-container {
                text-align: center;
                margin: 40px 0 20px 0;
                padding: 15px;
                border: 1px solid #ddd;
                background-color: #f9f9f9;
            }
            .qr-container img {
                width: 130px;
                height: 130px;
            }
            .qr-container p {
                margin: 8px 0 0 0;
                font-size: 9pt;
                color: #555;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
            }
            .footer img {
                max-width: 100%;
                height: auto;
            }
            .verification-info {
                margin-top: 15px;
                padding-top: 10px;
                border-top: 1px solid #ccc;
                font-size: 8pt;
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

            <p><strong>A QUIEN PUEDA INTERESAR:</strong></p>

            <p>Por medio de la presente, hacemos constar que el(la) Sr(a). <strong>" . htmlspecialchars($nombre . ' ' . $apellido) . "</strong>, portador(a) de la cédula <strong>" . htmlspecialchars($cedula) . "</strong> y seguro social <strong>" . htmlspecialchars($seguro) . "</strong>, labora en nuestra empresa desde el <strong>" . date('d/m/Y', strtotime($fecha_ingreso)) . "</strong>, desempeñando el cargo de <strong>" . htmlspecialchars($cargo) . "</strong>.</p>

            <p>El salario mensual pactado es de <strong>B/. " . number_format($salario, 2) . "</strong>, con las siguientes deducciones aproximadas:</p>
            
            <ul>
                <li>Seguro Social: <strong>B/. " . number_format($desc_seguro, 2) . "</strong></li>
                <li>Seguro Educativo: <strong>B/. " . number_format($desc_educativo, 2) . "</strong></li>
                <li>Impuesto sobre la Renta: <strong>B/. " . number_format($desc_renta, 2) . "</strong></li>
                $html_dinamico
            </ul>

            <p>" . htmlspecialchars($descripcion) . "</p>

            <p>Se expide la presente para los fines que el(la) interesado(a) estime conveniente.</p>
        </div>

        <!-- FIRMA -->
        <div class='firma'>
            <p><strong>Atentamente,</strong></p>
            <br><br><br>
            <p style='border-top: 1px solid #000; width: 250px; display: inline-block;'></p><br>
            <p><strong>Departamento de Recursos Humanos</strong><br>
            Grupo PCR</p>
        </div>

        <!-- CÓDIGO QR DE VERIFICACIÓN -->
        <div class='qr-container'>
            <img src='$qr_src' alt='Código QR de Verificación'>
            <p><strong>Escanea este código para verificar la autenticidad</strong></p>
            <p style='font-size: 7pt; color: #999;'>Ref: " . substr($hash_verificacion, 0, 12) . "</p>
        </div>

        <!-- PIE DE PÁGINA -->";
        
        if ($footer_src) {
            $html .= "<div class='footer'><img src='$footer_src' alt='Footer' style='max-width: 600px;'></div>";
        } else {
            $html .= "<div class='footer'><div style='width: 100%; height: 80px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 10pt; color: #666;'>FOOTER PCR</div></div>";
        }

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
        $nombreArchivo = 'Carta_' . preg_replace('/[^a-zA-Z0-9]/', "", $nombre) . '_' . $id_carta . '_' . date('Ymd') . '.pdf';
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
            $mensaje_correo = "Estimado " . htmlspecialchars($nombre) . ",<br><br>Adjunto encontrará su carta de trabajo solicitada.<br><br>" . 
                             "Esta carta incluye un código QR que permite verificar su autenticidad escaneándolo con cualquier dispositivo móvil.<br><br>" .
                             htmlspecialchars($comentario) . "<br><br>Saludos,<br>Departamento de RRHH - Grupo PCR";
            
            //$copias = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];
            $copias = ["pedroarrieta25@hotmail.com"];

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

    $solicitudes = $class->solicitudes_aprobar();
    require_once __DIR__ . '/../views/carta_trabajo_aprobar.php';
    exit;
    
}elseif (isset($_GET['carta_trabajo_aprobar'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_formulario'])) {
        echo 'Pasando por aqui 1<br>';
        var_dump($_POST);
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

        if (!empty($_POST['otros_descuentos']) && is_array($_POST['otros_descuentos'])) {
            echo 'Pasando por aqui 2<br>';
            $sql_insert_desc = "INSERT INTO carta_trabajo_descuentos (carta_id, acreedor, monto) VALUES (:carta_id, :acreedor, :monto)";
            $stmt_desc = $class->pdo->prepare($sql_insert_desc);

            foreach ($_POST['otros_descuentos'] as $descuento) {
                echo 'Pasando por aqui 3<br>';
                if (!empty($descuento['acreedor']) && is_numeric($descuento['monto'])) {
                    echo 'Pasando por aqui 4<br>';
                    $stmt_desc->execute([
                        ':carta_id' => $_POST['solicitud_id'],
                        ':acreedor' => $descuento['acreedor'],
                        ':monto' => $descuento['monto']
                    ]);
                }
            }
        }


        echo "<script>alert('Datos guardados correctamente');</script>";
    }

    $solicitudes = $class->solicitudes_aprobar();

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

            $copia = ["abi.pineda@grupopcr.com.pa"];
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

}elseif(isset($_GET['incapacidad_vrrhh'])){

    if (isset($_POST['incapacidad_id'])) {

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
        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
            $nombre_tmp = $_FILES['archivo_adjunto']['tmp_name'];
            $nombre_archivo = basename($_FILES['archivo_adjunto']['name']);
            $destino = 'archivos/' . time() . '_' . $nombre_archivo;

            // Carpeta de almacenamiento
            $upload_dir = __DIR__ . '/../uploads/permisos/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $archivo_destino = $upload_dir . $nombre_archivo;

            if (move_uploaded_file($nombre_tmp, $archivo_destino)) {
                $archivo_ruta = $archivo_destino; // Ruta que se guardará en la base de datos
            } else {
                echo "<div class='alert alert-danger'>Error al mover el archivo.</div>";
                exit;
            }
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

        <h4 style="color:rgb(250, 11, 2);">Para aprobar o declinar esta solicitud, ingrese en el siguiente link.</h4>

        <p><a href="https://apppcr.net/app/views/aprobar_vacaciones.php?codigo_empleado=' . $codigo . '&nombre_completo=' . $nombre_comple . '&fecha_desde='.$fecha_inicio.'&fecha_hasta='.$fecha_fin.'&cantidad_dias='.$cantidad_dias.'">Aprobar o Declinar Solicitud de Permiso</a></p>

        <p><strong>Canal de contacto:</strong></p>
        <ul>
            <li>Email: <a href="mailto:abi.pineda@grupopcr.com.pa">abi.pineda@grupopcr.com.pa</a></li>
        </ul>

        <p><em>Este es un mensaje automático. Por favor, no responda a este correo. Utilice los canales indicados para cualquier comunicación.</em></p>
        ';

        $copia = ["abi.pineda@grupopcr.com.pa", $email_feje];
        //$copia = ["pedroarrieta25@hotmail.com"];

        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje); 

        }else{

        $mensaje = 'El colaborador  '.$nombre_comple.' con codigo de empleado: '.$codigo.'<br> 
        ha solicitado un permiso tipo '.$tipo_licencia.' <br>
        Fechas del permiso desde '.$fecha_inicio.' hasta '.$fecha_fin.' <br>
        Descripcion del permiso: '.$descripcion.'';

        $copia = ["abi.pineda@grupopcr.com.pa", $email_feje];
        //$copia = ["pedroarrieta25@hotmail.com"];
        //$copia = ["pedro.arrieta@grupopcr.com.pa"];
    
        $class->enviar_correo("sofia.macias@grupopcr.com.pa", $copia, "Solicitud de permiso tipo '".$tipo_licencia."'", $mensaje);

        }

        echo "<div class='alert alert-success'>Permiso solicitado correctamente.</div>";
        
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

        $id_permiso = $_POST['permiso_id'];

        //$dartos_cola = $class->datos_colaborador();

        $get_email_colab = $class->get_email_permiso($id_permiso);

        foreach ($get_email_colab as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
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


        $copiacoo = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje);
    
        echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

    }

    $permisos = $class->select_permisos_all();

    require_once __DIR__ . '/../views/solicitud_permiso_aprobar.php';
    exit();

}elseif (isset($_GET['administrar_permiso_admin'])) {

    if (isset($_POST['aprobar_permiso'])) {
        $class->update_permiso($_POST['respuesta_jefe'], $_POST['comentario_jefe'], $_POST['permiso_id']);
        //echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

        $id_permiso = $_POST['permiso_id'];

        //$dartos_cola = $class->datos_colaborador();

        $get_email_colab = $class->get_email_permiso($id_permiso);

        foreach ($get_email_colab as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
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


        $copiacoo = ["abi.pineda@grupopcr.com.pa", "sofia.macias@grupopcr.com.pa"];
        //$copiacoo = ["pedro.arrieta@grupopcr.com.pa"];

        $class->enviar_correo($email, $copiacoo, "Respuesta a la solicitud de permiso", $mensaje);
    
        echo "<div class='alert alert-success'>Permiso actualizado correctamente.</div>";

    }

    $permisos = $class->select_permisos_all_admin($_SESSION['code']);

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

            $copia = ["abi.pineda@grupopcr.com.pa"];
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

        $class->update_calamidad($_POST['calamidad_id']);

        $get_email_colab = $class->get_email_calamidad($_POST['calamidad_id']);
        foreach ($get_email_colab as $key => $value) {
            $nombre_comple = $value['nombre']. ' ' .$value['apellido']; 
            $email = $value['email'];
        }

        $mensaje = 'Estimado '.$nombre_comple.' <br> 
        La calamidad ha sido revisada por parte del departamento RRHH. <br>';

        $copia = ["sofia.macias@grupopcr.com.pa", "abi.pineda@grupopcr.com.pa"];
        //$copia = ["pedroarrieta25@hotmail.com"];
    
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
