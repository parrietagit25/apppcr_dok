<?php
// Test para verificar el campo es_externo
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    echo "<h3>Verificando campo es_externo:</h3>";
    
    // Verificar si el campo es_externo existe
    $sql = "SHOW COLUMNS FROM empleados LIKE 'es_externo'";
    $stmt = $pdo->query($sql);
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "<p style='color: green;'>✅ Campo 'es_externo' existe en la tabla</p>";
        echo "<p>Tipo: " . $column['Type'] . "</p>";
        echo "<p>Null: " . $column['Null'] . "</p>";
        echo "<p>Default: " . $column['Default'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Campo 'es_externo' NO existe en la tabla</p>";
    }
    
    // Probar inserción simple con es_externo
    $test_code = 'T' . (time() % 1000);
    echo "<h4>Probando inserción con es_externo:</h4>";
    
    $sql = "INSERT INTO empleados (
        codigo_empleado, nombre, apellido, es_externo, ncodcia, cedula, 
        sexo, seguro_social, nacionalidad, email, telefono1, estatus_empleado, 
        salario_pactado, nombre_departamento, nombre_centro_costo, 
        nombre_division, nombre_proyecto, nombre_fase, nombre_sucursal, 
        nombre_cargo, observaciones
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $params = [
        $test_code, 'Test', 'User', 1, 1, '123456789', 'M', '123456789', 
        'Panameña', 'test@test.com', '1234567890', 'A', 1000, 'IT', 
        'Centro Costo', 'División', 'Proyecto', 'Fase', 'Sucursal', 
        'Desarrollador', 'Sin observaciones'
    ];
    
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Inserción exitosa con es_externo=1</p>";
        
        // Verificar el valor insertado
        $stmt = $pdo->prepare("SELECT codigo_empleado, nombre, apellido, es_externo FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Usuario: " . $user['nombre'] . " " . $user['apellido'] . "</p>";
            echo "<p>Es externo: " . ($user['es_externo'] ? 'Sí' : 'No') . "</p>";
        }
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        echo "<p>Registro de prueba eliminado.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error en inserción</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
