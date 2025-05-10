<?php
// script_php_receptor.php
// --- TOKEN SECRETO COMPARTIDO ---
$expected_token = 'p2RqZg9bJkLmNqP_sS_tUvWxYz1A3B5C7D9E2F8G1H0I6J4K7L5M2N1Po3-q_Rs';
// --- Configuración de MySQL ---

$mysql_config = [
    'host' => 'db',       // ej. localhost, o la IP/hostname del servidor MySQL
    'username' => 'appuser',     // Usuario de tu base de datos MySQL
    'password' => 'apppass',  // Contraseña del usuario MySQL
    'database' => 'apppcr',// Nombre de tu base de datos en MySQL
    'port' => 3306                        // Puerto por defecto de MySQL
];

// --- Nombre de la tabla temporal ---
$tabla_temporal = 'temp_empleados'; // Asegúrate que esta tabla exista en tu MySQL

// --- Columnas MySQL (basado en tu DESCRIBE) ---
// Este array debe coincidir con los nombres de las claves del JSON que envía Python
// y con las columnas de tu tabla temp_empleados.
$columnas_mysql = [
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
    // El campo 'fecha_update' en MySQL se actualiza automáticamente si está configurado como ON UPDATE CURRENT_TIMESTAMP
];

// --- Tipos de datos para bind_param ---
// Esta es una estimación basada en tu DESCRIBE. Ajusta si es necesario.
// 'i' para enteros, 'd' para decimales/dobles, 's' para strings (incluyendo fechas/datetime desde JSON), 'b' para blobs.
$mysql_column_bind_types = [
    'i', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 'i', 's', 's', 's', 's',
    's', 's', 's', 's', 's', 's', 's', 's', 'd', 'd', 'd', 'd', 's', 'd', 'd', 'i', 'i', 'd', 'd', 'd',
    'd', 'd', 'd', 'd', 'd', 'd', 'd', 'd', 'd', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's',
    's', 'i', 'i', 's', 's', 's', 's', 's', 's', 'i', 's', 's', 's', 's', 's', 's', 'i', 'i', 's', 's',
    's', 'i', 'i', 's', 'd', 'i', 's', 's', 's', 'd', 's', 's', 's', 's', 's', 's', 's', 'd', 'd', 'd',
    'i', 'd', 'd', 'i'
];
$param_types_string = implode('', $mysql_column_bind_types);


// --- Respuesta HTTP ---
header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Solicitud incorrecta.'];

// --- Validación del Token ---
$received_token = null;
// Apache y Nginx pueden cambiar los nombres de los encabezados.
// HTTP_X_AUTH_TOKEN es común para X-Auth-Token.
if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
    $received_token = $_SERVER['HTTP_X_AUTH_TOKEN'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Para 'Authorization: Bearer TOKEN'
    if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        $received_token = $matches[1];
    }
}

// Usar hash_equals para comparación segura contra ataques de temporización
if ($received_token === null || !hash_equals($expected_token, $received_token)) {
    http_response_code(401); // Unauthorized
    $response['message'] = 'Acceso no autorizado. Token inválido o ausente.';
    echo json_encode($response);
    exit;
}
// --- Fin de la Validación del Token ---


// --- Procesamiento de la Solicitud POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true); // true para array asociativo

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad Request
        $response['message'] = 'Error al decodificar JSON: ' . json_last_error_msg();
        echo json_encode($response);
        exit;
    }

    if (empty($data) || !is_array($data)) {
        http_response_code(400); // Bad Request
        $response['message'] = 'No se recibieron datos válidos o el formato es incorrecto.';
        echo json_encode($response);
        exit;
    }

    // --- Conexión a MySQL ---
    $conn = new mysqli(
        $mysql_config['host'],
        $mysql_config['username'],
        $mysql_config['password'],
        $mysql_config['database'],
        $mysql_config['port']
    );

    if ($conn->connect_error) {
        http_response_code(500); // Internal Server Error
        $response['message'] = "Error de conexión a MySQL: " . $conn->connect_error;
        echo json_encode($response);
        exit;
    }
    $conn->set_charset("utf8mb4"); // Recomendado para compatibilidad de caracteres

    // --- Vaciar la tabla temporal ---
    // Esto asegura que la tabla temporal solo contenga los datos de la carga actual.
    if (!$conn->query("TRUNCATE TABLE `$tabla_temporal`")) {
        http_response_code(500);
        $response['message'] = "Error al truncar la tabla temporal: " . $conn->error;
        $conn->close();
        echo json_encode($response);
        exit;
    }

    // --- Preparar la Sentencia de Inserción ---
    $placeholders = implode(', ', array_fill(0, count($columnas_mysql), '?'));
    $sql_column_names_string = implode(', ', array_map(function($col) { return "`$col`"; }, $columnas_mysql));
    
    $stmt_sql = "INSERT INTO `$tabla_temporal` ($sql_column_names_string) VALUES ($placeholders)";
    $stmt = $conn->prepare($stmt_sql);

    if (!$stmt) {
        http_response_code(500);
        $response['message'] = "Error al preparar la sentencia SQL: " . $conn->error . " SQL: " . $stmt_sql;
        $conn->close();
        echo json_encode($response);
        exit;
    }
    
    // --- Inserción de Datos ---
    $filas_insertadas = 0;
    $errores_insercion = [];

    $conn->begin_transaction();
    try {
        // Asumimos que $data es una lista de arrays asociativos (filas)
        foreach ($data as $indice_fila => $fila) {
            if (!is_array($fila)) {
                $errores_insercion[] = "Elemento en el índice $indice_fila no es un array. Datos: " . json_encode($fila);
                continue;
            }

            $params_referencias = []; // Array para pasar por referencia a bind_param
            $params_valores = [];   // Array para almacenar los valores reales
            
            foreach ($columnas_mysql as $col_nombre) {
                // Si la columna no existe en la fila JSON, o es explícitamente null, se insertará NULL
                $valor = array_key_exists($col_nombre, $fila) ? $fila[$col_nombre] : null;
                
                // Para fechas vacías o strings '0000-00-00' que MySQL podría rechazar en modo estricto.
                // Convertir a NULL si es una columna de fecha y el valor es problemático.
                // Esto es un ejemplo, ajusta según tu lógica de datos para fechas.
                $col_index_for_type = array_search($col_nombre, $columnas_mysql);
                if ($mysql_column_bind_types[$col_index_for_type] == 's') { // Asumiendo que las fechas son strings 's'
                    if (is_string($valor) && (trim($valor) === '' || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00')) {
                        // Verifica si la columna en MySQL permite NULL. Si no, esto dará error.
                        // El DESCRIBE que proporcionaste indica NULL para fechas.
                         if (stripos($col_nombre, 'fecha') !== false) { // heurística simple para columnas de fecha
                            $valor = null;
                         }
                    }
                }
                $params_valores[] = $valor;
            }

            // Crear referencias para bind_param
            foreach ($params_valores as $key_val => &$val_ref) { // pasar por referencia
                $params_referencias[$key_val] = &$val_ref;
            }
            
            // Validar que el número de tipos y parámetros coincida
            if (strlen($param_types_string) !== count($params_referencias)) {
                $errores_insercion[] = "Discrepancia en el número de parámetros para la fila $indice_fila. Esperados: " . strlen($param_types_string) . ", Obtenidos: " . count($params_referencias) . ". Datos: " . json_encode($fila);
                continue; // Saltar esta fila
            }

            // Enlazar parámetros dinámicamente
            // call_user_func_array necesita un array de parámetros donde el primero es la cadena de tipos
            $bind_args = array_merge([$param_types_string], $params_referencias);
            call_user_func_array([$stmt, 'bind_param'], $bind_args);

            if ($stmt->execute()) {
                $filas_insertadas++;
            } else {
                $errores_insercion[] = "Error al insertar fila $indice_fila: " . $stmt->error . ". Datos: " . json_encode($fila);
            }
        }

        if (empty($errores_insercion)) {
            $conn->commit();
            $response['status'] = 'success';
            $response['message'] = "Se procesaron los datos. Filas insertadas en $tabla_temporal: $filas_insertadas.";
        } else {
            $conn->rollback();
            http_response_code(400); // Bad Request (o 500 si es error del servidor)
            $response['message'] = "Se encontraron errores durante la inserción. No se guardaron datos.";
            $response['errors_details'] = $errores_insercion;
            $response['filas_intentadas_con_exito_parcial'] = $filas_insertadas; // Filas que se hubieran insertado antes del error
        }

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        $response['message'] = "Excepción durante la transacción: " . $e->getMessage();
        $response['errors_details'] = $errores_insercion; // Incluir errores acumulados
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Método no permitido. Solo se acepta POST.';
}

echo json_encode($response);
?>