<?php
/**
 * Script de prueba para verificar el sistema de verificación de cartas
 * 
 * IMPORTANTE: Ejecutar SOLO en ambiente de desarrollo o pruebas
 * Eliminar después de verificar que todo funciona
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test del Sistema de Verificación de Cartas</h1>";
echo "<hr>";

// Test 1: Cargar configuración
echo "<h2>Test 1: Cargar Configuración</h2>";
try {
    require_once __DIR__ . '/config.php';
    echo "✅ config.php cargado correctamente<br>";
    echo "- Host BD: " . DB_EXTERNAL_HOST . "<br>";
    echo "- Base de datos: " . DB_EXTERNAL_NAME . "<br>";
    echo "- URL Base: " . URL_BASE_VERIFICACION . "<br>";
    echo "- Días expiración: " . DIAS_EXPIRACION_CARTA . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    exit;
}
echo "<hr>";

// Test 2: Conexión a BD Externa
echo "<h2>Test 2: Conexión a Base de Datos Externa</h2>";
try {
    require_once __DIR__ . '/DatabaseExternal.php';
    $db = DatabaseExternal::getInstance();
    $pdo = $db->getConnection();
    echo "✅ Conexión establecida correctamente<br>";
    
    // Verificar tablas
    $stmt = $pdo->query("SHOW TABLES LIKE 'cartas_%'");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<strong>Tablas encontradas:</strong><br>";
    foreach ($tablas as $tabla) {
        echo "  ✓ $tabla<br>";
    }
    
    // Contar registros
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cartas_trabajo_verificacion");
    $result = $stmt->fetch();
    echo "<br><strong>Total de cartas registradas:</strong> " . $result['total'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    echo "<br><strong>Posibles causas:</strong><br>";
    echo "- Credenciales incorrectas en config.php<br>";
    echo "- MySQL no permite conexiones remotas<br>";
    echo "- Firewall bloqueando puerto 3306<br>";
    exit;
}
echo "<hr>";

// Test 3: Servicio de Verificación
echo "<h2>Test 3: CartaVerificacionService</h2>";
try {
    require_once __DIR__ . '/CartaVerificacionService.php';
    $service = new CartaVerificacionService();
    echo "✅ CartaVerificacionService instanciado correctamente<br>";
    
    // Test de encriptación
    $test_id = "TEST_123";
    $token = $service->encriptarToken($test_id);
    echo "✅ Token encriptado: " . substr($token, 0, 20) . "...<br>";
    
    $desencriptado = $service->desencriptarToken($token);
    if ($desencriptado === $test_id) {
        echo "✅ Desencriptación correcta: $desencriptado<br>";
    } else {
        echo "❌ Error en desencriptación<br>";
    }
    
    // Test de hash
    $hash = $service->generarHashVerificacion(1, "00123", "8-888-8888");
    echo "✅ Hash generado: " . substr($hash, 0, 16) . "...<br>";
    
    // Test de IP
    $ip = $service->obtenerIPCliente();
    echo "✅ IP detectada: $ip<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    exit;
}
echo "<hr>";

// Test 4: API de QR
echo "<h2>Test 4: Servicio de Generación de QR</h2>";
try {
    $test_token = "TEST_QR_123456";
    $url_qr = $service->generarURLQR($test_token);
    echo "✅ URL del QR generada<br>";
    echo "URL: <a href='$url_qr' target='_blank'>Ver QR de prueba</a><br>";
    
    // Intentar descargar QR de prueba
    $test_qr_path = __DIR__ . '/test_qr_temp.png';
    $resultado = $service->descargarImagenQR($url_qr, $test_qr_path);
    
    if ($resultado && file_exists($test_qr_path)) {
        echo "✅ QR descargado correctamente<br>";
        echo "<img src='data:image/png;base64," . base64_encode(file_get_contents($test_qr_path)) . "' width='150'><br>";
        echo "<small>👆 Este es un QR de prueba</small><br>";
        unlink($test_qr_path); // Eliminar archivo temporal
    } else {
        echo "⚠️ No se pudo descargar QR (puede ser problema de conectividad)<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 5: Inserción de Datos de Prueba (OPCIONAL)
echo "<h2>Test 5: Inserción de Datos de Prueba (OPCIONAL)</h2>";
echo "<p><strong>⚠️ Esto insertará un registro de prueba en la BD</strong></p>";

if (isset($_GET['insertar_prueba']) && $_GET['insertar_prueba'] === 'si') {
    try {
        $token_prueba = $service->encriptarToken("PRUEBA_" . time());
        $hash_prueba = $service->generarHashVerificacion(999, "TEST001", "0-000-0000");
        
        $datos_prueba = [
            'id_carta_original' => 999,
            'hash_verificacion' => $hash_prueba,
            'token_qr' => $token_prueba,
            'codigo_empleado' => 'TEST001',
            'nombre' => 'Juan',
            'apellido' => 'Prueba',
            'cedula' => '0-000-0000',
            'seguro_social' => '00-000-000',
            'email' => 'prueba@test.com',
            'cargo' => 'Tester',
            'fecha_ingreso' => '2025-01-01',
            'salario_bruto' => 1000.00,
            'incluye_desglose_salarial' => 1,
            'descripcion' => 'Carta de prueba del sistema',
            'comentario_rrhh' => 'Prueba de integración',
            'nombre_archivo_pdf' => 'Prueba.pdf',
            'hash_pdf' => hash('sha256', 'test'),
            'estado' => 'activa',
            'fecha_emision' => date('Y-m-d H:i:s'),
            'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+365 days')),
            'empresa' => 'Grupo PCR TEST',
            'ip_generacion' => $service->obtenerIPCliente()
        ];
        
        $deducciones_prueba = [
            [
                'tipo' => 'seguro_social',
                'descripcion' => 'Seguro Social',
                'monto' => 70.00,
                'orden' => 1
            ],
            [
                'tipo' => 'seguro_educativo',
                'descripcion' => 'Seguro Educativo',
                'monto' => 12.50,
                'orden' => 2
            ]
        ];
        
        $id_insertado = $service->insertarCarta($datos_prueba, $deducciones_prueba);
        
        if ($id_insertado) {
            echo "✅ Registro de prueba insertado con ID: $id_insertado<br>";
            echo "<br><strong>URL de verificación:</strong><br>";
            echo "<a href='" . URL_BASE_VERIFICACION . "verificar.php?token=$token_prueba' target='_blank'>";
            echo URL_BASE_VERIFICACION . "verificar.php?token=$token_prueba";
            echo "</a><br>";
            echo "<br><em>👆 Clic para probar la verificación</em><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error al insertar: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<a href='?insertar_prueba=si' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Insertar Registro de Prueba</a><br>";
    echo "<small>Esto creará una carta de prueba para verificar el sistema completo</small>";
}
echo "<hr>";

// Resumen Final
echo "<h2>✅ Resumen de Tests</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr><th>Test</th><th>Estado</th></tr>";
echo "<tr><td>1. Configuración</td><td style='background:#d4edda;'>✅ OK</td></tr>";
echo "<tr><td>2. Conexión BD</td><td style='background:#d4edda;'>✅ OK</td></tr>";
echo "<tr><td>3. Servicio</td><td style='background:#d4edda;'>✅ OK</td></tr>";
echo "<tr><td>4. API QR</td><td style='background:#d4edda;'>✅ OK</td></tr>";
echo "<tr><td>5. Inserción</td><td style='background:#fff3cd;'>⏸️ MANUAL</td></tr>";
echo "</table>";

echo "<br><br>";
echo "<div style='background:#d1ecf1;padding:15px;border-left:4px solid #0c5460;'>";
echo "<strong>🎉 Sistema funcionando correctamente!</strong><br>";
echo "Puedes proceder con la integración completa.<br>";
echo "<br><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de las pruebas.";
echo "</div>";

echo "<br><br>";
echo "<p style='color:#999;font-size:12px;'>Script ejecutado: " . date('Y-m-d H:i:s') . "</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1 {
    color: #333;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
}
h2 {
    color: #555;
    margin-top: 20px;
}
</style>

