<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/DatabaseExternal.php';

class CartaVerificacionService {
    private $pdo;

    public function __construct() {
        $db = DatabaseExternal::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Obtiene la conexión PDO (para uso interno cuando sea necesario)
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Encripta el ID de la carta para generar el token del QR
     */
    public function encriptarToken($id_carta) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(ENCRYPTION_METHOD));
        $encrypted = openssl_encrypt($id_carta, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        $token = base64_encode($encrypted . '::' . $iv);
        // Hacer URL-safe
        return strtr($token, '+/', '-_');
    }

    /**
     * Desencripta el token para obtener el ID
     */
    public function desencriptarToken($token) {
        try {
            // Revertir URL-safe
            $token = strtr($token, '-_', '+/');
            $data = base64_decode($token);
            list($encrypted_data, $iv) = explode('::', $data, 2);
            return openssl_decrypt($encrypted_data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Genera hash de verificación único
     */
    public function generarHashVerificacion($id_carta, $codigo_empleado, $cedula) {
        $data = $id_carta . $codigo_empleado . $cedula . time();
        return hash('sha256', $data);
    }

    /**
     * Genera hash del PDF para verificar integridad
     */
    public function generarHashPDF($ruta_pdf) {
        if (file_exists($ruta_pdf)) {
            return hash_file('sha256', $ruta_pdf);
        }
        return null;
    }

    /**
     * Inserta los datos de la carta en la base de datos externa
     */
    public function insertarCarta($datos_carta, $deducciones = []) {
        try {
            $this->pdo->beginTransaction();

            // Insertar carta principal
            $sql = "INSERT INTO cartas_trabajo_verificacion (
                id_carta_original,
                hash_verificacion,
                token_qr,
                codigo_empleado,
                nombre,
                apellido,
                cedula,
                seguro_social,
                email,
                cargo,
                fecha_ingreso,
                salario_bruto,
                incluye_desglose_salarial,
                descripcion,
                comentario_rrhh,
                nombre_archivo_pdf,
                hash_pdf,
                estado,
                fecha_emision,
                fecha_expiracion,
                empresa,
                ip_generacion
            ) VALUES (
                :id_carta_original,
                :hash_verificacion,
                :token_qr,
                :codigo_empleado,
                :nombre,
                :apellido,
                :cedula,
                :seguro_social,
                :email,
                :cargo,
                :fecha_ingreso,
                :salario_bruto,
                :incluye_desglose_salarial,
                :descripcion,
                :comentario_rrhh,
                :nombre_archivo_pdf,
                :hash_pdf,
                :estado,
                :fecha_emision,
                :fecha_expiracion,
                :empresa,
                :ip_generacion
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_carta_original' => $datos_carta['id_carta_original'],
                ':hash_verificacion' => $datos_carta['hash_verificacion'],
                ':token_qr' => $datos_carta['token_qr'],
                ':codigo_empleado' => $datos_carta['codigo_empleado'],
                ':nombre' => $datos_carta['nombre'],
                ':apellido' => $datos_carta['apellido'],
                ':cedula' => $datos_carta['cedula'],
                ':seguro_social' => $datos_carta['seguro_social'],
                ':email' => $datos_carta['email'],
                ':cargo' => $datos_carta['cargo'],
                ':fecha_ingreso' => $datos_carta['fecha_ingreso'],
                ':salario_bruto' => $datos_carta['salario_bruto'],
                ':incluye_desglose_salarial' => $datos_carta['incluye_desglose_salarial'],
                ':descripcion' => $datos_carta['descripcion'],
                ':comentario_rrhh' => $datos_carta['comentario_rrhh'],
                ':nombre_archivo_pdf' => $datos_carta['nombre_archivo_pdf'],
                ':hash_pdf' => $datos_carta['hash_pdf'],
                ':estado' => $datos_carta['estado'],
                ':fecha_emision' => $datos_carta['fecha_emision'],
                ':fecha_expiracion' => $datos_carta['fecha_expiracion'],
                ':empresa' => $datos_carta['empresa'],
                ':ip_generacion' => $datos_carta['ip_generacion']
            ]);

            $carta_id = $this->pdo->lastInsertId();

            // Insertar deducciones si existen
            if (!empty($deducciones)) {
                $sql_ded = "INSERT INTO cartas_deducciones (
                    carta_id, tipo_deduccion, descripcion, monto, orden
                ) VALUES (
                    :carta_id, :tipo_deduccion, :descripcion, :monto, :orden
                )";
                
                $stmt_ded = $this->pdo->prepare($sql_ded);
                
                foreach ($deducciones as $deduccion) {
                    $stmt_ded->execute([
                        ':carta_id' => $carta_id,
                        ':tipo_deduccion' => $deduccion['tipo'],
                        ':descripcion' => $deduccion['descripcion'],
                        ':monto' => $deduccion['monto'],
                        ':orden' => $deduccion['orden']
                    ]);
                }
            }

            $this->pdo->commit();
            return $carta_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error insertando carta en BD externa: " . $e->getMessage());
            throw new Exception("Error al guardar en base de datos externa");
        }
    }

    /**
     * Genera la URL del QR usando servicio externo
     */
    public function generarURLQR($token) {
        $url_verificacion = URL_BASE_VERIFICACION . 'verificar.php?token=' . urlencode($token);
        $qr_url = QR_API_URL . '?size=' . QR_SIZE . '&format=' . QR_FORMAT . '&data=' . urlencode($url_verificacion);
        return $qr_url;
    }

    /**
     * Descarga la imagen del QR desde el servicio externo
     */
    public function descargarImagenQR($qr_url, $ruta_destino) {
        try {
            $imagen = file_get_contents($qr_url);
            if ($imagen === false) {
                throw new Exception("No se pudo obtener la imagen del QR");
            }
            file_put_contents($ruta_destino, $imagen);
            return true;
        } catch (Exception $e) {
            error_log("Error descargando QR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la IP del cliente
     */
    public function obtenerIPCliente() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }

    /**
     * Envía los datos de la carta a GoDaddy vía API
     */
    public function enviarAGoDaddy($datos_carta, $deducciones = []) {
        // URL de la API en GoDaddy (desde config.php)
        $api_url = API_GODADDY_URL;
        
        // Clave de API (desde config.php)
        $api_key = API_SECRET_KEY;
        
        // Preparar datos para enviar
        $payload = [
            'carta' => $datos_carta,
            'deducciones' => $deducciones
        ];
        
        $json_data = json_encode($payload);
        
        // Configurar cURL
        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-API-Key: ' . $api_key,
                'Content-Length: ' . strlen($json_data)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        // Ejecutar request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Verificar respuesta
        if ($curl_error) {
            error_log("Error cURL enviando a GoDaddy: " . $curl_error);
            throw new Exception("Error de conexión con servidor de verificación: " . $curl_error);
        }
        
        if ($http_code !== 201 && $http_code !== 200) {
            error_log("Error HTTP al enviar a GoDaddy. Código: $http_code. Respuesta: $response");
            throw new Exception("Error al sincronizar con servidor de verificación (HTTP $http_code)");
        }
        
        $result = json_decode($response, true);
        
        if (!$result || !isset($result['success'])) {
            error_log("Respuesta inválida de GoDaddy: " . $response);
            throw new Exception("Respuesta inválida del servidor de verificación");
        }
        
        return $result;
    }
}

