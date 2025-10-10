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
}

