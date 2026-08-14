<?php
/**
 * API para recibir datos de cartas desde AWS
 * Ubicación: /public_html/carta/api_recibir_carta.php en GoDaddy
 */

header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

/**
 * Lee X-API-Key (Apache, Nginx/FPM y proxies suelen exponerlo distinto).
 */
function carta_api_obtener_clave_request(): string
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    if (!is_array($headers)) {
        $headers = [];
    }
    $normalized = [];
    foreach ($headers as $name => $value) {
        $normalized[strtolower((string) $name)] = trim((string) $value);
    }
    if (!empty($normalized['x-api-key'])) {
        return $normalized['x-api-key'];
    }
    if (!empty($_SERVER['HTTP_X_API_KEY'])) {
        return trim((string) $_SERVER['HTTP_X_API_KEY']);
    }
    return '';
}

// Verificar autenticación
$auth_key = carta_api_obtener_clave_request();

if ($auth_key === '' || !defined('API_SECRET_KEY') || $auth_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Validar datos requeridos
$required = ['id_carta_original', 'hash_verificacion', 'token_qr', 'codigo_empleado', 'nombre', 'apellido', 'cedula'];
foreach ($required as $field) {
    if (empty($datos['carta'][$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Campo requerido faltante: $field"]);
        exit;
    }
}

try {
    require_once __DIR__ . '/DatabaseExternal.php';
    
    $db = DatabaseExternal::getInstance();
    $pdo = $db->getConnection();
    
    $pdo->beginTransaction();
    
    // Insertar carta
    $carta = $datos['carta'];
    
    $sql = "INSERT INTO cartas_trabajo_verificacion (
        id_carta_original, hash_verificacion, token_qr, codigo_empleado,
        nombre, apellido, cedula, seguro_social, email, cargo, fecha_ingreso,
        salario_bruto, incluye_desglose_salarial, descripcion, comentario_rrhh,
        nombre_archivo_pdf, hash_pdf, estado, fecha_emision, fecha_expiracion,
        empresa, ip_generacion
    ) VALUES (
        :id_carta_original, :hash_verificacion, :token_qr, :codigo_empleado,
        :nombre, :apellido, :cedula, :seguro_social, :email, :cargo, :fecha_ingreso,
        :salario_bruto, :incluye_desglose_salarial, :descripcion, :comentario_rrhh,
        :nombre_archivo_pdf, :hash_pdf, :estado, :fecha_emision, :fecha_expiracion,
        :empresa, :ip_generacion
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_carta_original' => $carta['id_carta_original'],
        ':hash_verificacion' => $carta['hash_verificacion'],
        ':token_qr' => $carta['token_qr'],
        ':codigo_empleado' => $carta['codigo_empleado'],
        ':nombre' => $carta['nombre'],
        ':apellido' => $carta['apellido'],
        ':cedula' => $carta['cedula'],
        ':seguro_social' => $carta['seguro_social'] ?? null,
        ':email' => $carta['email'] ?? null,
        ':cargo' => $carta['cargo'],
        ':fecha_ingreso' => $carta['fecha_ingreso'],
        ':salario_bruto' => $carta['salario_bruto'] ?? null,
        ':incluye_desglose_salarial' => $carta['incluye_desglose_salarial'] ?? 0,
        ':descripcion' => $carta['descripcion'] ?? null,
        ':comentario_rrhh' => $carta['comentario_rrhh'] ?? null,
        ':nombre_archivo_pdf' => $carta['nombre_archivo_pdf'] ?? null,
        ':hash_pdf' => $carta['hash_pdf'] ?? null,
        ':estado' => $carta['estado'] ?? 'activa',
        ':fecha_emision' => $carta['fecha_emision'],
        ':fecha_expiracion' => $carta['fecha_expiracion'] ?? null,
        ':empresa' => $carta['empresa'] ?? 'Automarket',
        ':ip_generacion' => $carta['ip_generacion'] ?? null
    ]);
    
    $carta_id = $pdo->lastInsertId();
    
    // Insertar deducciones si existen
    if (!empty($datos['deducciones']) && is_array($datos['deducciones'])) {
        $sql_ded = "INSERT INTO cartas_deducciones (
            carta_id, tipo_deduccion, descripcion, monto, orden
        ) VALUES (
            :carta_id, :tipo_deduccion, :descripcion, :monto, :orden
        )";
        
        $stmt_ded = $pdo->prepare($sql_ded);
        
        foreach ($datos['deducciones'] as $ded) {
            $stmt_ded->execute([
                ':carta_id' => $carta_id,
                ':tipo_deduccion' => $ded['tipo'],
                ':descripcion' => $ded['descripcion'],
                ':monto' => $ded['monto'],
                ':orden' => $ded['orden']
            ]);
        }
    }
    
    $pdo->commit();
    
    // Respuesta exitosa
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Carta registrada exitosamente',
        'carta_id' => $carta_id,
        'id_carta_original' => $carta['id_carta_original']
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error API recibir carta: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al guardar en base de datos',
        'message' => $e->getMessage() // Solo en desarrollo, quitar en producción
    ]);
    
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log("Error API recibir carta: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'message' => $e->getMessage()
    ]);
}

