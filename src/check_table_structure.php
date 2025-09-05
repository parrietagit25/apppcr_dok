<?php
// Script para verificar la estructura actual de la tabla empleados
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    echo "<h3>Verificando estructura de la tabla empleados:</h3>";
    
    // Obtener información de la tabla
    $sql = "DESCRIBE empleados";
    $stmt = $pdo->query($sql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>Campos NOT NULL encontrados:</h4>";
    $not_null_fields = [];
    
    foreach ($columns as $column) {
        if ($column['Null'] === 'NO') {
            $not_null_fields[] = $column;
            echo "<p style='color: red;'>❌ {$column['Field']} - {$column['Type']} - NOT NULL</p>";
        }
    }
    
    echo "<h4>Campos que permiten NULL:</h4>";
    foreach ($columns as $column) {
        if ($column['Null'] === 'YES') {
            echo "<p style='color: green;'>✅ {$column['Field']} - {$column['Type']} - NULL</p>";
        }
    }
    
    echo "<h4>Resumen:</h4>";
    echo "<p>Total de campos: " . count($columns) . "</p>";
    echo "<p style='color: red;'>Campos NOT NULL: " . count($not_null_fields) . "</p>";
    echo "<p style='color: green;'>Campos que permiten NULL: " . (count($columns) - count($not_null_fields)) . "</p>";
    
    if (count($not_null_fields) > 0) {
        echo "<h4>¿Quieres modificar los campos NOT NULL?</h4>";
        echo "<p><a href='modify_table.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Sí, modificar tabla</a></p>";
    } else {
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡La tabla ya permite NULL en todos los campos!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
