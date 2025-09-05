<?php
// Script de prueba para verificar la estructura de la tabla empleados
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    // Verificar si el campo es_externo existe
    $stmt = $pdo->query("DESCRIBE empleados");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Estructura de la tabla empleados:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $es_externo_exists = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'es_externo') {
            $es_externo_exists = true;
        }
    }
    echo "</table>";
    
    if ($es_externo_exists) {
        echo "<p style='color: green;'>✅ El campo 'es_externo' existe en la tabla empleados.</p>";
    } else {
        echo "<p style='color: red;'>❌ El campo 'es_externo' NO existe en la tabla empleados.</p>";
        echo "<p>Necesitas ejecutar el script SQL para agregarlo:</p>";
        echo "<pre>ALTER TABLE empleados ADD COLUMN es_externo TINYINT(1) DEFAULT 0;</pre>";
    }
    
    // Probar una inserción simple
    echo "<h3>Probando inserción simple:</h3>";
    try {
        $test_code = 'TEST' . time();
        $stmt = $pdo->prepare("INSERT INTO empleados (codigo_empleado, nombre, apellido, es_externo) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$test_code, 'Test', 'User', 1]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Inserción de prueba exitosa.</p>";
            
            // Limpiar el registro de prueba
            $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
            $stmt->execute([$test_code]);
            echo "<p>Registro de prueba eliminado.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en inserción de prueba.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error en inserción de prueba: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error de conexión: " . $e->getMessage() . "</p>";
}
?>
