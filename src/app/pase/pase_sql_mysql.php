<?php
// script_php_receptor.php (pase_sql_mysql.php)
// --- Configuración del Archivo de Log ---
define('RUTA_LOG_ERRORES', __DIR__ . '/proceso_carga_empleados.txt'); // El log se guardará en el mismo directorio que este script.
/**
 * Escribe un mensaje en el archivo de log.
 * @param string $mensaje El mensaje a loguear.
 * @param string $nivel Nivel del log (INFO, ERROR, WARNING, DEBUG).
 */
function escribir_log($mensaje, $nivel = 'INFO') {
    $timestamp = date("Y-m-d H:i:s");
    // Formato: [AAAA-MM-DD HH:MM:SS] [NIVEL] Mensaje
    $entrada_log = "[{$timestamp}] [{$nivel}] {$mensaje}" . PHP_EOL;

    // Intentar escribir en el archivo. FILE_APPEND para no sobrescribir logs anteriores.
    // El @ suprime los errores de PHP si file_put_contents falla (ej. por permisos).
    if (@file_put_contents(RUTA_LOG_ERRORES, $entrada_log, FILE_APPEND) === false) {
        // Fallback: Si no se puede escribir en el archivo de log personalizado,
        // al menos intentar escribir en el log de errores del servidor PHP.
        error_log("FALLO AL ESCRIBIR EN LOG PERSONALIZADO (" . RUTA_LOG_ERRORES . "). Mensaje original: " . $entrada_log);
    }
}

// Loguear el inicio de la ejecución
escribir_log("--- INICIO DE EJECUCION DEL SCRIPT ---");
escribir_log("Método de solicitud: " . $_SERVER['REQUEST_METHOD']);

// --- TOKEN SECRETO COMPARTIDO ---
$expected_token = 'p2RqZg9bJkLmNqP_sS_tUvWxYz1A3B5C7D9E2F8G1H0I6J4K7L5M2N1Po3-q_Rs'; // Tu token

// --- Configuración de MySQL ---
$mysql_config = [
    'host' => 'db',
    'username' => 'appuser',
    'password' => 'apppass',
    'database' => 'apppcr',
    'port' => 3306
];

$tabla_temporal = 'temp_empleados';

$columnas_mysql = [ // 105 columnas
    "ncodcia", "codigo_empleado", "codigo_horario", "tarjeta_reloj", "nombre", "apellido",
    "fecha_nacimiento", "cedula", "dv", "cedula_rep_empleador", "cedula_reportada",
    "estado_civil", "sexo", "seguro_social", "grupo_isr", "cantidad_dependientes",
    "tipo_empleado", "nacionalidad", "tipo_sangre", "direccion1", "direccion2",
    "apartado_postal", "email", "telefono1", "telefono2", "extension_telefono",
    "estatus_empleado", "tipo_salario", "horas_regulares", "horas_st_acumuladas",
    "salario_pactado", "salario_hora", "metodo_calculo_isr", "monto_isr_fijo_salario",
    "isr_adicional", "hace_declaracion_renta", "porc_max_descuentos", "otros_ingreso1",
    "otros_ingreso2", "otros_ingreso3", "monto_ajuste", "horas_ajuste",
    "gasto_representacion", "transporte", "viaticos", "otros_gastos1", "otros_gastos2",
    "otros_gastos3", "otros_gastos4", "otros_gastos5", "grupo_pago", "codigo_sucursal",
    "codigo_departamento", "codigo_division", "codigo_centro_costo", "codigo_proyecto",
    "codigo_fase", "forma_pago", "fecha_ingreso", "fecha_vence_contrato",
    "fecha_liquidacion", "fecha_ultimo_aumento", "dias_no_trabajados", "dias_licencia",
    "fecha_vac_inicia", "fecha_vac_final", "vac_dinero_fecha_hasta",
    "vac_tiempo_fecha_hasta", "ultimo_dia_pagado", "ultima_modificacion",
    "pertenece_sindicato", "tipo_trabajador", "tipo_cuenta", "numero_cuenta_ach",
    "numero_banco", "subcuenta_mayor_general", "referencia_deposito_direc", "tiene_vale",
    "es_pasaporte", "codigo_custom1", "codigo_custom2", "codigo_custom3",
    "es_jefe_cuadrilla", "es_marino", "observaciones", "monto_isr_fijo_gastorep",
    "numero_banco_ach", "codigo_cargo", "codigo_emp_interface1", "codigo_emp_interface2",
    "monto_vale", "nombre_departamento", "nombre_centro_costo", "nombre_division",
    "nombre_proyecto", "nombre_fase", "nombre_sucursal", "nombre_cargo",
    "fondo_incapacidad", "salario_acu_vacacion", "gasto_rep_acu_vacacion",
    "dias_vaca_acu_tiempo", "salario_acu_xiii", "gasto_rep_acu_xiii", "dias_vaca_acu_dinero"
];

$mysql_column_bind_types = [ // 105 tipos
    'i', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 'i', 's', 's', 's', 's',
    's', 's', 's', 's', 's', 's', 's', 's', 'd', 'd', 'd', 'd', 's', 'd', 'd', 'i', 'i', 'd', 'd', 'd',
    'd', 'd', 'd', 'd', 'd', 'd', 'd', 'd', 'd', 'd', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's',
    's', 's', 'i', 'i', 's', 's', 's', 's', 's', 's', 'i', 's', 's', 's', 's', 's', 's', 'i', 'i', 's',
    's', 's', 'i', 'i', 's', 'd', 'i', 's', 's', 's', 'd', 's', 's', 's', 's', 's', 's', 's', 'd', 'd',
    'd', 'i', 'd', 'd', 'i'
];
$param_types_string = implode('', $mysql_column_bind_types);

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud incorrecta.'];

// --- Validación del Token ---
$received_token = null;
// Simplificación de la obtención del token, priorizando X-Auth-Token que envía Python
if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
    $received_token = $_SERVER['HTTP_X_AUTH_TOKEN'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    $received_token = $matches[1];
}

if ($received_token === null) {
    escribir_log("Validación de Token FALLIDA: No se recibió ningún token.", 'ERROR');
    http_response_code(401);
    $response['message'] = 'Acceso no autorizado. Token ausente.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if (!hash_equals($expected_token, $received_token)) {
    escribir_log("Validación de Token FALLIDA: Token inválido. Recibido: '{$received_token}'", 'ERROR');
    http_response_code(401);
    $response['message'] = 'Acceso no autorizado. Token inválido.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

escribir_log("Validación de Token EXITOSA.", 'INFO');

// --- Procesamiento de la Solicitud POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    escribir_log("Procesando solicitud POST.", 'INFO');
    $json_data = file_get_contents('php://input');
    
    if ($json_data === false || empty($json_data)) {
        escribir_log("Cuerpo de la solicitud POST vacío o no se pudo leer.", 'ERROR');
        http_response_code(400);
        $response['message'] = 'No se recibieron datos en el cuerpo de la solicitud.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    escribir_log("Datos JSON recibidos (primeros 500 chars): " . substr($json_data, 0, 500), 'DEBUG');

    $data = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        escribir_log("Error al decodificar JSON: " . json_last_error_msg(), 'ERROR');
        http_response_code(400);
        $response['message'] = 'Error al decodificar JSON: ' . json_last_error_msg();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    escribir_log("JSON decodificado exitosamente. Número de registros: " . count($data), 'INFO');

    if (empty($data) || !is_array($data)) {
        escribir_log("Los datos decodificados están vacíos o no son un array.", 'ERROR');
        http_response_code(400);
        $response['message'] = 'No se recibieron datos válidos o el formato es incorrecto.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // --- Conexión a MySQL ---
    escribir_log("Intentando conectar a MySQL: Host: {$mysql_config['host']}, DB: {$mysql_config['database']}", 'INFO');
    $conn = new mysqli(
        $mysql_config['host'],
        $mysql_config['username'],
        $mysql_config['password'],
        $mysql_config['database'],
        $mysql_config['port']
    );

    if ($conn->connect_error) {
        escribir_log("Error de conexión a MySQL: " . $conn->connect_error, 'ERROR');
        http_response_code(500);
        $response['message'] = "Error de conexión a MySQL: " . $conn->connect_error;
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    escribir_log("Conexión a MySQL exitosa.", 'INFO');
    $conn->set_charset("utf8mb4");

    // --- Vaciar la tabla temporal ---
    escribir_log("Truncando tabla temporal: {$tabla_temporal}", 'INFO');
    if (!$conn->query("TRUNCATE TABLE `$tabla_temporal`")) {
        $error_msg = "Error al truncar la tabla temporal `{$tabla_temporal}`: " . $conn->error;
        escribir_log($error_msg, 'ERROR');
        http_response_code(500);
        $response['message'] = $error_msg;
        $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    escribir_log("Tabla temporal `{$tabla_temporal}` truncada exitosamente.", 'INFO');

    // --- Preparar la Sentencia de Inserción ---
    $placeholders = implode(', ', array_fill(0, count($columnas_mysql), '?'));
    $sql_column_names_string = implode(', ', array_map(function($col) { return "`$col`"; }, $columnas_mysql));
    
    $stmt_sql = "INSERT INTO `$tabla_temporal` ($sql_column_names_string) VALUES ($placeholders)";
    escribir_log("Preparando sentencia SQL: {$stmt_sql}", 'DEBUG');
    $stmt = $conn->prepare($stmt_sql);

    if (!$stmt) {
        $error_msg = "Error al preparar la sentencia SQL: " . $conn->error;
        escribir_log($error_msg, 'ERROR');
        http_response_code(500);
        $response['message'] = $error_msg;
        $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    escribir_log("Sentencia SQL preparada exitosamente.", 'INFO');
    
    // --- Inserción de Datos ---
    $filas_insertadas = 0;
    $errores_insercion = [];
    $conn->begin_transaction();
    escribir_log("Iniciando transacción para inserción de datos.", 'INFO');

    try {
        foreach ($data as $indice_fila => $fila) {
            if (!is_array($fila)) {
                $error_msg_fila = "[Fila $indice_fila] Los datos recibidos no son un array. Saltando fila. Datos: " . substr(json_encode($fila), 0, 200);
                escribir_log($error_msg_fila, 'WARNING');
                $errores_insercion[] = $error_msg_fila;
                continue;
            }

            $params_valores = [];
            foreach ($columnas_mysql as $col_index => $col_nombre) {
                $valor = array_key_exists($col_nombre, $fila) ? $fila[$col_nombre] : null;
                
                // *** INICIO: NUEVA LÓGICA GLOBAL PARA NULL -> 0 ***
                if ($valor === null) {
                    $valor = 0; // Si cualquier campo es NULL, se establece a 0.
                    // Descomenta la siguiente línea si quieres loguear cada vez que esto sucede (puede ser muy verboso)
                    // escribir_log("[Fila $indice_fila] Columna '{$col_nombre}' era NULL, se estableció a 0.", 'DEBUG');
                }
                // *** FIN: NUEVA LÓGICA GLOBAL PARA NULL -> 0 ***
                
                // Manejo especial para strings de fecha vacías/problemáticas que NO eran NULL originalmente
                // y que no se convirtieron a 0 arriba (porque no eran NULL).
                // O si después de convertirse a 0, aún necesitas tratarlos diferente (lo cual no es el caso aquí
                // porque si $valor es 0, is_string($valor) será falso).
                if ($mysql_column_bind_types[$col_index] == 's' && $valor !== null && is_string($valor)) { 
                    if (trim($valor) === '' || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00') {
                         if (stripos($col_nombre, 'fecha') !== false || stripos($col_nombre, 'ultimo_dia_pagado') !== false || stripos($col_nombre, 'ultima_modificacion') !== false ) {
                            $valor = null; 
                            escribir_log("[Fila $indice_fila] Columna '{$col_nombre}' string de fecha inválida ('" . ($fila[$col_nombre] ?? 'NO PRESENTE') . "'), convertida a NULL para inserción.", 'DEBUG');
                         }
                    }
                }
                $params_valores[] = $valor;
            }

            $params_referencias = [];
            foreach ($params_valores as $key_val => &$val_ref) {
                $params_referencias[$key_val] = &$val_ref;
            }
            
            if (strlen($param_types_string) !== count($params_referencias)) {
                $error_msg_fila = "[Fila $indice_fila] Discrepancia crítica en el número de parámetros. Tipos: " . strlen($param_types_string) . ", Valores: " . count($params_referencias) . ". Error en config arrays PHP. Datos: " . substr(json_encode($fila), 0, 200);
                escribir_log($error_msg_fila, 'ERROR');
                $errores_insercion[] = $error_msg_fila;
                continue; 
            }
            
            $bind_args = array_merge([$param_types_string], $params_referencias);
            
            if (!call_user_func_array([$stmt, 'bind_param'], $bind_args)) {
                 $error_msg_fila = "[Fila $indice_fila] Error en bind_param: " . $stmt->error . ". Tipos: '$param_types_string'. #Params: ".count($params_referencias).". Datos: " . substr(json_encode($fila), 0, 200);
                 escribir_log($error_msg_fila, 'ERROR');
                 $errores_insercion[] = $error_msg_fila;
                 continue;
            }

            if ($stmt->execute()) {
                $filas_insertadas++;
            } else {
                $error_msg_fila = "[Fila $indice_fila] Error al ejecutar insert: " . $stmt->error . ". Datos: " . substr(json_encode($fila), 0, 200);
                escribir_log("[Fila $indice_fila] Valores intentados (después de defaults): " . substr(json_encode($params_valores),0,500), "DEBUG");
                escribir_log($error_msg_fila, 'ERROR');
                $errores_insercion[] = $error_msg_fila;
            }
        }

        if (empty($errores_insercion)) {
            $conn->commit();
            escribir_log("Transacción COMMIT. Filas insertadas exitosamente: {$filas_insertadas}.", 'INFO');
            $response['status'] = 'success';
            $response['message'] = "Se procesaron los datos. Filas insertadas en `{$tabla_temporal}`: {$filas_insertadas}.";
        } else {
            $conn->rollback();
            escribir_log("Transacción ROLLBACK debido a errores en la inserción. Errores: " . count($errores_insercion), 'ERROR');
            http_response_code(400); 
            $response['message'] = "Se encontraron errores durante la inserción. No se guardaron datos. Total filas con error: " . count($errores_insercion);
            $response['errors_details'] = $errores_insercion;
            $response['filas_intentadas_con_exito_parcial'] = $filas_insertadas;
        }

    } catch (Exception $e) {
        $conn->rollback();
        $exception_msg = "Excepción durante la transacción: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine();
        escribir_log($exception_msg, 'CRITICAL');
        http_response_code(500);
        $response['message'] = "Excepción crítica durante la transacción: " . $e->getMessage();
        $response['errors_details_antes_excepcion'] = $errores_insercion;
    }

    $stmt->close();
    $conn->close();
    escribir_log("Conexión MySQL cerrada.", 'INFO');
} else {
    escribir_log("Método no permitido: {$_SERVER['REQUEST_METHOD']}. Solo se acepta POST.", 'WARNING');
    http_response_code(405);
    $response['message'] = 'Método no permitido. Solo se acepta POST.';
}

escribir_log("--- FIN DE EJECUCION DEL SCRIPT. Respuesta final: " . substr(json_encode($response), 0, 1000) . " ---", "INFO");
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>