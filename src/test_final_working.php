<?php
// Test final que funciona con la tabla modificada
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    $test_code = 'T' . (time() % 1000); // Máximo 4 caracteres
    $nombre = 'Test';
    $apellido = 'User';
    
    echo "<h3>Probando inserción con tabla modificada:</h3>";
    
    // SQL con solo los campos NOT NULL que realmente necesitamos
    $sql = "INSERT INTO empleados (
        codigo_empleado, 
        nombre, 
        apellido, 
        es_externo,
        ncodcia,
        cedula,
        sexo,
        seguro_social,
        nacionalidad,
        email,
        telefono1,
        estatus_empleado,
        salario_pactado,
        nombre_departamento,
        nombre_centro_costo,
        nombre_division,
        nombre_proyecto,
        nombre_fase,
        nombre_sucursal,
        nombre_cargo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    echo "<p>SQL con 20 campos NOT NULL</p>";
    echo "<p>Código: " . $test_code . " (longitud: " . strlen($test_code) . ")</p>";
    
    $stmt = $pdo->prepare($sql);
    $params = [
        $test_code,           // codigo_empleado
        $nombre,              // nombre
        $apellido,            // apellido
        1,                    // es_externo
        1,                    // ncodcia
        '123456789',          // cedula
        'M',                  // sexo
        '123456789',          // seguro_social
        'Panameña',           // nacionalidad
        'test@test.com',      // email
        '1234567890',         // telefono1
        'A',                  // estatus_empleado
        1000,                 // salario_pactado
        'IT',                 // nombre_departamento
        'Centro Costo',       // nombre_centro_costo
        'División',           // nombre_division
        'Proyecto',           // nombre_proyecto
        'Fase',               // nombre_fase
        'Sucursal',           // nombre_sucursal
        'Desarrollador'       // nombre_cargo
    ];
    
    echo "<p>Parámetros: " . count($params) . " valores</p>";
    
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Inserción exitosa!</p>";
        
        // Verificar que se insertó
        $stmt = $pdo->prepare("SELECT codigo_empleado, nombre, apellido, es_externo, ncodcia FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Usuario encontrado: " . $user['nombre'] . " " . $user['apellido'] . "</p>";
            echo "<p>Es externo: " . ($user['es_externo'] ? 'Sí' : 'No') . "</p>";
            echo "<p>Ncodcia: " . $user['ncodcia'] . "</p>";
        }
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        echo "<p>Registro de prueba eliminado.</p>";
        
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡El sistema funciona! Ahora puedes probar el registro en el formulario.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error en inserción.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>