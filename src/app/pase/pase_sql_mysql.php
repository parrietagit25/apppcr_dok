<?php
// script_php_receptor.php (pase_sql_mysql.php)

define('RUTA_LOG_ERRORES', __DIR__ . '/proceso_carga_empleados.txt');

function escribir_log($mensaje, $nivel = 'INFO') {
    $timestamp = date("Y-m-d H:i:s");
    $entrada_log = "[{$timestamp}] [{$nivel}] {$mensaje}" . PHP_EOL;
    if (@file_put_contents(RUTA_LOG_ERRORES, $entrada_log, FILE_APPEND) === false) {
        error_log("FALLO AL ESCRIBIR EN LOG PERSONALIZADO (" . RUTA_LOG_ERRORES . "). Mensaje original: " . $entrada_log);
    }
}

escribir_log("--- INICIO DE EJECUCION DEL SCRIPT ---");
escribir_log("Método de solicitud: " . $_SERVER['REQUEST_METHOD']);

$expected_token = 'p2RqZg9bJkLmNqP_sS_tUvWxYz1A3B5C7D9E2F8G1H0I6J4K7L5M2N1Po3-q_Rs'; // Tu token

$mysql_config = [
    'host' => 'db',
    'username' => 'appuser',
    'password' => 'apppass',
    'database' => 'apppcr',
    'port' => 3306
];

$tabla_temporal = 'temp_empleados';
$tabla_produccion = 'empleados'; // Nombre de tu tabla de producción

$columnas_mysql = [ // 105 columnas (sin incluir fecha_update si es auto-gestionada en ambas)
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

$mapa_defaults_para_no_nulos = [
    'pertenece_sindicato' => 0, 'hace_declaracion_renta' => 0, 'tiene_vale' => 0,
    'es_pasaporte' => 0, 'es_jefe_cuadrilla' => 0, 'es_marino' => 0,
    // ¡COMPLETA ESTA LISTA SEGÚN TU ESQUEMA MYSQL PARA TODAS LAS COLUMNAS NOT NULL!
    // 'observaciones' => '', // Ejemplo TEXT NOT NULL
];

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud incorrecta.'];

$received_token = null;
if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
    $received_token = $_SERVER['HTTP_X_AUTH_TOKEN'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    $received_token = $matches[1];
}

if ($received_token === null) {
    escribir_log("Validación de Token FALLIDA: No se recibió ningún token.", 'ERROR');
    http_response_code(401); $response['message'] = 'Acceso no autorizado. Token ausente.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
}
if (!hash_equals($expected_token, $received_token)) {
    escribir_log("Validación de Token FALLIDA: Token inválido. Recibido: '{$received_token}'", 'ERROR');
    http_response_code(401); $response['message'] = 'Acceso no autorizado. Token inválido.';
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
}
escribir_log("Validación de Token EXITOSA.", 'INFO');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    escribir_log("Procesando solicitud POST.", 'INFO');
    $json_data = file_get_contents('php://input');
    
    if ($json_data === false || empty($json_data)) {
        escribir_log("Cuerpo de la solicitud POST vacío o no se pudo leer.", 'ERROR');
        http_response_code(400); $response['message'] = 'No se recibieron datos en el cuerpo de la solicitud.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    escribir_log("Datos JSON recibidos (primeros 500 chars): " . substr($json_data, 0, 500), 'DEBUG');

    $data = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        escribir_log("Error al decodificar JSON: " . json_last_error_msg(), 'ERROR');
        http_response_code(400); $response['message'] = 'Error al decodificar JSON: ' . json_last_error_msg();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    escribir_log("JSON decodificado exitosamente. Número de registros: " . count($data), 'INFO');

    if (empty($data) && count($data) === 0) { // Modificado para permitir un array vacío si no hay datos
        escribir_log("El array de datos decodificado está vacío. No hay datos para procesar.", 'INFO');
        // No es un error, simplemente no hay datos.
        $response['status'] = 'success';
        $response['message'] = 'No se recibieron datos para procesar. Las tablas no fueron modificadas.';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    if (!is_array($data)){
        escribir_log("Los datos decodificados no son un array.", 'ERROR');
        http_response_code(400); $response['message'] = 'El formato de los datos es incorrecto (no es un array).';
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }


    escribir_log("Intentando conectar a MySQL: Host: {$mysql_config['host']}, DB: {$mysql_config['database']}", 'INFO');
    $conn = new mysqli($mysql_config['host'], $mysql_config['username'], $mysql_config['password'], $mysql_config['database'], $mysql_config['port']);

    if ($conn->connect_error) {
        escribir_log("Error de conexión a MySQL: " . $conn->connect_error, 'ERROR');
        http_response_code(500); $response['message'] = "Error de conexión a MySQL: " . $conn->connect_error;
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    escribir_log("Conexión a MySQL exitosa.", 'INFO');
    $conn->set_charset("utf8mb4");

    escribir_log("Truncando tabla temporal: {$tabla_temporal}", 'INFO');
    if (!$conn->query("TRUNCATE TABLE `$tabla_temporal`")) {
        $error_msg = "Error al truncar la tabla temporal `{$tabla_temporal}`: " . $conn->error;
        escribir_log($error_msg, 'ERROR');
        http_response_code(500); $response['message'] = $error_msg; $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    escribir_log("Tabla temporal `{$tabla_temporal}` truncada exitosamente.", 'INFO');

    $placeholders = implode(', ', array_fill(0, count($columnas_mysql), '?'));
    $sql_column_names_string = implode(', ', array_map(function($col) { return "`$col`"; }, $columnas_mysql));
    
    $stmt_sql = "INSERT IGNORE INTO `$tabla_temporal` ($sql_column_names_string) VALUES ($placeholders)";
    escribir_log("Preparando sentencia SQL (con IGNORE): {$stmt_sql}", 'DEBUG');
    $stmt = $conn->prepare($stmt_sql);

    if (!$stmt) {
        $error_msg = "Error al preparar la sentencia SQL: " . $conn->error;
        escribir_log($error_msg, 'ERROR');
        http_response_code(500); $response['message'] = $error_msg; $conn->close();
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); exit;
    }
    escribir_log("Sentencia SQL preparada exitosamente.", 'INFO');
    
    $filas_intentadas_temp = 0;
    $filas_realmente_insertadas_temp = 0;
    $filas_ignoradas_temp = 0;
    $errores_carga_temporal = [];

    $conn->begin_transaction(); // Transacción para la carga en temp_empleados
    escribir_log("Iniciando transacción para carga en `{$tabla_temporal}`.", 'INFO');

    try {
        if (!empty($data)) { // Solo procesar si hay datos
            foreach ($data as $indice_fila => $fila) {
                if (!is_array($fila)) {
                    $error_msg_fila = "[Fila $indice_fila en TEMPORAL] Los datos recibidos no son un array. Saltando fila. Datos: " . substr(json_encode($fila), 0, 200);
                    escribir_log($error_msg_fila, 'WARNING'); $errores_carga_temporal[] = $error_msg_fila; continue;
                }

                $params_valores = [];
                foreach ($columnas_mysql as $col_index => $col_nombre) {
                    $valor_original_json = array_key_exists($col_nombre, $fila) ? $fila[$col_nombre] : null;
                    $valor_a_insertar = $valor_original_json;

                    $tipo_bind_actual = $mysql_column_bind_types[$col_index];
                    if ($tipo_bind_actual == 's' && $valor_a_insertar !== null && is_string($valor_a_insertar)) {
                        if (trim($valor_a_insertar) === '' || $valor_a_insertar === '0000-00-00' || $valor_a_insertar === '0000-00-00 00:00:00') {
                            if (stripos($col_nombre, 'fecha') !== false || stripos($col_nombre, 'ultimo_dia_pagado') !== false || stripos($col_nombre, 'ultima_modificacion') !== false) {
                                $valor_a_insertar = null; 
                                escribir_log("[Fila $indice_fila en TEMPORAL] Columna '{$col_nombre}' string de fecha ('{$valor_original_json}') convertida a NULL real.", 'DEBUG');
                            }
                        }
                    }

                    if ($valor_a_insertar === null) {
                        if (array_key_exists($col_nombre, $mapa_defaults_para_no_nulos)) {
                            $valor_a_insertar = $mapa_defaults_para_no_nulos[$col_nombre];
                            escribir_log("[Fila $indice_fila en TEMPORAL] Columna '{$col_nombre}' era NULL y es NOT NULL, se usó valor por defecto: '" . var_export($valor_a_insertar, true) . "'.", 'DEBUG');
                        }
                    }
                    $params_valores[] = $valor_a_insertar;
                }

                $params_referencias = [];
                foreach ($params_valores as $key_val => &$val_ref) { $params_referencias[$key_val] = &$val_ref; }
                
                if (strlen($param_types_string) !== count($params_referencias)) {
                    $error_msg_fila = "[Fila $indice_fila en TEMPORAL] Discrepancia crítica en parámetros. Tipos: " . strlen($param_types_string) . ", Valores: " . count($params_referencias) . ". Datos: " . substr(json_encode($fila), 0, 200);
                    escribir_log($error_msg_fila, 'ERROR'); $errores_carga_temporal[] = $error_msg_fila; continue; 
                }
                
                $bind_args = array_merge([$param_types_string], $params_referencias);
                
                if (!call_user_func_array([$stmt, 'bind_param'], $bind_args)) {
                    $error_msg_fila = "[Fila $indice_fila en TEMPORAL] Error en bind_param: " . $stmt->error . ". Datos: " . substr(json_encode($fila), 0, 200);
                    escribir_log($error_msg_fila, 'ERROR'); $errores_carga_temporal[] = $error_msg_fila; continue;
                }

                $filas_intentadas_temp++;
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $filas_realmente_insertadas_temp++;
                    } else {
                        $filas_ignoradas_temp++;
                        escribir_log("[Fila $indice_fila en TEMPORAL] Fila ignorada por MySQL (duplicado PK). Datos: " . substr(json_encode($fila), 0, 200), 'WARNING');
                    }
                } else {
                    $error_msg_fila = "[Fila $indice_fila en TEMPORAL] Error al ejecutar insert: " . $stmt->error . ". Datos: " . substr(json_encode($fila), 0, 200);
                    escribir_log("[Fila $indice_fila en TEMPORAL] Valores intentados: " . substr(json_encode($params_valores),0,500), "DEBUG");
                    escribir_log($error_msg_fila, 'ERROR'); $errores_carga_temporal[] = $error_msg_fila;
                }
            }
        } // Fin if (!empty($data))

        if (empty($errores_carga_temporal)) {
            $conn->commit(); // COMMIT para temp_empleados
            escribir_log("Transacción COMMIT para `{$tabla_temporal}`. Filas intentadas: {$filas_intentadas_temp}. Insertadas: {$filas_realmente_insertadas_temp}. Ignoradas: {$filas_ignoradas_temp}.", 'INFO');
            
            // --- INICIO: Transferencia a Tabla de Producción ---
            escribir_log("Iniciando transferencia de datos de `{$tabla_temporal}` a `{$tabla_produccion}`.", 'INFO');
            
            if ($filas_realmente_insertadas_temp > 0 || ($filas_intentadas_temp > 0 && $filas_ignoradas_temp == $filas_intentadas_temp && empty($data) == false )) { 
                // Proceder si se insertaron filas O si se intentaron filas y todas fueron ignoradas (lo que significa que temp_empleados podría no estar vacía si hubo datos previos válidos)
                // Una comprobación más simple es si temp_empleados no está vacía, pero usemos las filas insertadas por ahora.
                // O, mejor aún, solo proceder si la carga a temporal se considera "exitosa en su conjunto",
                // y si `temp_empleados` efectivamente tiene datos que transferir.
                // El `INSERT IGNORE` significa que `$errores_carga_temporal` estará vacío si solo hubo duplicados.

                $conn->begin_transaction(); // Nueva transacción para la tabla de producción
                escribir_log("Iniciando transacción para `{$tabla_produccion}`.", 'INFO');
                try {
                    escribir_log("Truncando tabla de producción: `{$tabla_produccion}`", 'INFO');
                    if (!$conn->query("TRUNCATE TABLE `{$tabla_produccion}`")) {
                        throw new Exception("Error al truncar `{$tabla_produccion}`: " . $conn->error);
                    }
                    escribir_log("Tabla `{$tabla_produccion}` truncada exitosamente.", 'INFO');

                    $sql_transfer = "INSERT INTO `{$tabla_produccion}` ({$sql_column_names_string}) SELECT {$sql_column_names_string} FROM `{$tabla_temporal}`";
                    escribir_log("Ejecutando transferencia: {$sql_transfer}", 'DEBUG');
                    
                    if (!$conn->query($sql_transfer)) {
                        throw new Exception("Error al transferir datos a `{$tabla_produccion}`: " . $conn->error);
                    }
                    
                    $filas_transferidas_produccion = $conn->affected_rows;
                    escribir_log("Datos transferidos exitosamente a `{$tabla_produccion}`. Filas afectadas: {$filas_transferidas_produccion}.", 'INFO');
                    
                    $conn->commit(); // COMMIT para la tabla de producción
                    escribir_log("Transacción COMMIT para `{$tabla_produccion}`.", 'INFO');
                    
                    $response['status'] = 'success';
                    $response['message'] = "Datos cargados a `{$tabla_temporal}` (Insertadas: {$filas_realmente_insertadas_temp}, Ignoradas: {$filas_ignoradas_temp}) y transferidos a `{$tabla_produccion}` (Transferidas: {$filas_transferidas_produccion}).";
                    $response['temporal_insertadas'] = $filas_realmente_insertadas_temp;
                    $response['temporal_ignoradas'] = $filas_ignoradas_temp;
                    $response['produccion_transferidas'] = $filas_transferidas_produccion;

                } catch (Exception $e_prod) {
                    $conn->rollback(); // ROLLBACK para la tabla de producción
                    $exception_msg_prod = "Error durante la transferencia a `{$tabla_produccion}`: " . $e_prod->getMessage();
                    escribir_log($exception_msg_prod, 'CRITICAL');
                    $response['status'] = 'error';
                    $response['message'] = "Carga a `{$tabla_temporal}` correcta (Insertadas: {$filas_realmente_insertadas_temp}, Ignoradas: {$filas_ignoradas_temp}), PERO FALLÓ transferencia a producción. Error: " . $e_prod->getMessage();
                    $response['production_transfer_error'] = $e_prod->getMessage();
                    if (!headers_sent()) { http_response_code(500); }
                }
            } else if (empty($data) && $filas_intentadas_temp == 0) {
                // No hubo datos para empezar
                 $response['status'] = 'success'; // O 'info'
                 $response['message'] = "No se recibieron datos para procesar. Las tablas no fueron modificadas.";
                 escribir_log("No se recibieron datos para la tabla temporal. No se realiza transferencia a producción.", "INFO");
            }
            else {
                 // No se insertaron filas nuevas en temp_empleados (quizás todas eran duplicados o hubo otros problemas no críticos)
                 // O el array $data original estaba vacío.
                escribir_log("No hay filas nuevas en `{$tabla_temporal}` para transferir a `{$tabla_produccion}` (Insertadas: {$filas_realmente_insertadas_temp}). La tabla de producción no fue modificada.", 'INFO');
                $response['status'] = 'success'; // La carga a temporal se considera éxito si no hubo errores críticos
                $response['message'] = "Carga a `{$tabla_temporal}` completada (Insertadas: {$filas_realmente_insertadas_temp}, Ignoradas: {$filas_ignoradas_temp}). No hay filas nuevas para transferir o no se insertaron filas en temporal. Tabla de producción no modificada.";
                $response['temporal_insertadas'] = $filas_realmente_insertadas_temp;
                $response['temporal_ignoradas'] = $filas_ignoradas_temp;
                $response['produccion_transferidas'] = 0;
            }
            // --- FIN: Transferencia a Tabla de Producción ---

        } else { // $errores_carga_temporal NO está vacío
            $conn->rollback(); // ROLLBACK para temp_empleados
            escribir_log("Transacción ROLLBACK para `{$tabla_temporal}` debido a errores. Errores: " . count($errores_carga_temporal), 'ERROR');
            http_response_code(400); 
            $response['status'] = 'error';
            $response['message'] = "Se encontraron errores durante la carga a `{$tabla_temporal}`. No se guardaron datos. Errores: " . count($errores_carga_temporal);
            $response['errors_details_temporal'] = $errores_carga_temporal;
        }

    } catch (Exception $e) { // Captura excepciones generales del proceso de carga a temporal
        if ($conn->in_transaction) { // Verificar si la conexión sigue activa y hay transacción
             $conn->rollback();
        }
        $exception_msg = "Excepción durante carga a `{$tabla_temporal}`: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine();
        escribir_log($exception_msg, 'CRITICAL');
        http_response_code(500);
        $response['status'] = 'error';
        $response['message'] = "Excepción crítica durante carga a `{$tabla_temporal}`: " . $e->getMessage();
        $response['errors_details_temporal_exception'] = $errores_carga_temporal; // Errores de fila antes de esta excepción general
    }

    if (isset($stmt)) $stmt->close();
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