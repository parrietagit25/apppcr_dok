<?php
// Test usando el modelo User_simple.php
require_once 'config/config.php';
require_once 'app/core/Database.php';
require_once 'app/models/User_simple.php';

try {
    $pdo = Database::connect();
    $userModel = new UserSimple($pdo);
    
    $test_code = 'T' . (time() % 1000); // Máximo 4 caracteres
    $nombre = 'Test';
    $apellido = 'User';
    $fecha_nacimiento = '1990-01-01';
    $password = 'password123';
    $cedula = '123456789';
    $email = 'test@test.com';
    $telefono1 = '1234567890';
    $nombre_departamento = 'IT';
    $nombre_cargo = 'Desarrollador';
    $fecha_ingreso = '2024-01-01';
    $salario_pactado = 1000;
    $estatus_empleado = 'A';
    $seguro_social = '123456789';
    $sexo = 'M';
    $nacionalidad = 'Panameña';
    
    echo "<h3>Probando registro con modelo User_simple:</h3>";
    
    echo "<p>Código: " . $test_code . " (longitud: " . strlen($test_code) . ")</p>";
    echo "<p>Nombre: " . $nombre . " " . $apellido . "</p>";
    echo "<p>Email: " . $email . "</p>";
    
    $result = $userModel->registrar_usuario_simple(
        $test_code, $nombre, $apellido, $fecha_nacimiento, $password, $cedula, 
        $email, $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso, 
        $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ Usuario registrado exitosamente!</p>";
        
        // Verificar que se insertó en empleados
        $stmt = $pdo->prepare("SELECT codigo_empleado, nombre, apellido, es_externo FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Usuario en empleados: " . $user['nombre'] . " " . $user['apellido'] . "</p>";
            echo "<p>Es externo: " . ($user['es_externo'] ? 'Sí' : 'No') . "</p>";
        }
        
        // Verificar que se insertó en empleado_log
        $stmt = $pdo->prepare("SELECT codigo, stat, type_user FROM empleado_log WHERE codigo = ?");
        $stmt->execute([$test_code]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($log) {
            echo "<p>Usuario en empleado_log: " . $log['codigo'] . " (stat: " . $log['stat'] . ", type: " . $log['type_user'] . ")</p>";
        }
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$test_code]);
        $stmt = $pdo->prepare("DELETE FROM empleado_log WHERE codigo = ?");
        $stmt->execute([$test_code]);
        echo "<p>Registros de prueba eliminados.</p>";
        
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡El sistema funciona! Ahora puedes probar el registro en el formulario.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error al registrar usuario.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
