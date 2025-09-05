<?php
// Test para verificar códigos duplicados
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    echo "<h3>Verificando códigos duplicados:</h3>";
    
    // Verificar si hay códigos duplicados
    $sql = "SELECT codigo_empleado, COUNT(*) as count FROM empleados GROUP BY codigo_empleado HAVING COUNT(*) > 1";
    $stmt = $pdo->query($sql);
    $duplicados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($duplicados) > 0) {
        echo "<p style='color: red;'>❌ Se encontraron códigos duplicados:</p>";
        foreach ($duplicados as $dup) {
            echo "<p>Código: " . $dup['codigo_empleado'] . " (aparece " . $dup['count'] . " veces)</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ No hay códigos duplicados en la tabla</p>";
    }
    
    // Verificar restricciones de la tabla
    $sql = "SHOW CREATE TABLE empleados";
    $stmt = $pdo->query($sql);
    $create_table = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h4>Estructura de la tabla:</h4>";
    echo "<pre>" . htmlspecialchars($create_table['Create Table']) . "</pre>";
    
    // Probar inserción con código que sabemos que no existe
    $test_code = 'TEST' . time();
    echo "<h4>Probando inserción con código único: " . $test_code . "</h4>";
    
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
        echo "<p style='color: green;'>✅ Inserción exitosa con código único</p>";
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        echo "<p>Registro de prueba eliminado.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error en inserción con código único</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
