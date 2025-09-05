<?php
// Test ultra simple que definitivamente funciona
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    $test_code = 'T' . (time() % 1000); // Máximo 4 caracteres
    $nombre = 'Test';
    $apellido = 'User';
    
    echo "<h3>Probando inserción ultra simple:</h3>";
    
    // SQL con solo 4 campos básicos
    $sql = "INSERT INTO empleados (codigo_empleado, nombre, apellido, es_externo) VALUES (?, ?, ?, ?)";
    
    echo "<p>SQL: " . $sql . "</p>";
    echo "<p>Código: " . $test_code . " (longitud: " . strlen($test_code) . ")</p>";
    echo "<p>4 columnas, 4 placeholders - debería funcionar</p>";
    
    $stmt = $pdo->prepare($sql);
    $params = [$test_code, $nombre, $apellido, 1];
    
    echo "<p>Parámetros: " . implode(', ', $params) . "</p>";
    
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Inserción ultra simple exitosa!</p>";
        
        // Verificar que se insertó
        $stmt = $pdo->prepare("SELECT codigo_empleado, nombre, apellido, es_externo FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Usuario encontrado: " . $user['nombre'] . " " . $user['apellido'] . "</p>";
            echo "<p>Es externo: " . ($user['es_externo'] ? 'Sí' : 'No') . "</p>";
        }
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        echo "<p>Registro de prueba eliminado.</p>";
        
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡La inserción básica funciona! Ahora probemos con más campos.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error en inserción ultra simple.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>