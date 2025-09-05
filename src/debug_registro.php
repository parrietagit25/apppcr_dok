<?php
// Debug del registro de usuarios
require_once 'config/config.php';
require_once 'app/core/Database.php';
require_once 'app/models/User_simple.php';

try {
    $pdo = Database::connect();
    $userSimple = new UserSimple($pdo);
    
    echo "<h3>Debug del registro de usuarios:</h3>";
    
    // Simular datos del formulario exactamente como los recibe el controlador
    $codigo = 'T' . (time() % 1000);
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
    $stat = 1;
    $type_user = 2;
    
    echo "<h4>Datos que se van a insertar:</h4>";
    echo "<p>Código: " . $codigo . "</p>";
    echo "<p>Nombre: " . $nombre . " " . $apellido . "</p>";
    echo "<p>Email: " . $email . "</p>";
    echo "<p>Departamento: " . $nombre_departamento . "</p>";
    echo "<p>Cargo: " . $nombre_cargo . "</p>";
    echo "<p>Es externo: SÍ (por defecto)</p>";
    echo "<p>Stat: " . $stat . "</p>";
    echo "<p>Type user: " . $type_user . "</p>";
    
    // Verificar que el código no existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM empleados WHERE codigo_empleado = ?");
    $stmt->execute([$codigo]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<p style='color: orange;'>⚠️ El código ya existe, generando uno nuevo...</p>";
        $codigo = 'T' . (time() % 10000);
        echo "<p>Nuevo código: " . $codigo . "</p>";
    }
    
    echo "<h4>Intentando registro...</h4>";
    
    $resultado = $userSimple->registrar_usuario_simple(
        $codigo, $nombre, $apellido, $fecha_nacimiento, $password, $cedula, 
        $email, $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso, 
        $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad, 
        $stat, $type_user
    );
    
    if ($resultado) {
        echo "<p style='color: green;'>✅ Usuario registrado exitosamente!</p>";
        
        // Verificar que se insertó correctamente
        $stmt = $pdo->prepare("SELECT codigo_empleado, nombre, apellido, es_externo, ncodcia, nombre_departamento, nombre_cargo FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$codigo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Usuario en empleados: " . $user['nombre'] . " " . $user['apellido'] . "</p>";
            echo "<p>Es externo: " . ($user['es_externo'] ? 'Sí' : 'No') . "</p>";
            echo "<p>Ncodcia: " . $user['ncodcia'] . "</p>";
            echo "<p>Departamento: " . $user['nombre_departamento'] . "</p>";
            echo "<p>Cargo: " . $user['nombre_cargo'] . "</p>";
        }
        
        // Verificar empleado_log
        $stmt = $pdo->prepare("SELECT codigo, stat, type_user FROM empleado_log WHERE codigo = ?");
        $stmt->execute([$codigo]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($log) {
            echo "<p>Usuario en empleado_log: " . $log['codigo'] . " (stat: " . $log['stat'] . ", type: " . $log['type_user'] . ")</p>";
        }
        
        // Limpiar
        $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
        $stmt->execute([$codigo]);
        $stmt = $pdo->prepare("DELETE FROM empleado_log WHERE codigo = ?");
        $stmt->execute([$codigo]);
        echo "<p>Registros de prueba eliminados.</p>";
        
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡El sistema funciona perfectamente! El problema debe estar en el formulario real.</p>";
        
    } else {
        echo "<p style='color: red;'>❌ Error al registrar usuario.</p>";
        echo "<p>Revisa los logs del servidor para más detalles.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
