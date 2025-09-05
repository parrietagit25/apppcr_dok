<?php
// Test corregido - inserción que funciona
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    $test_code = 'TEST' . time();
    $nombre = 'Test';
    $apellido = 'User';
    $fecha_nacimiento = '1990-01-01';
    $cedula = '123456789';
    $email = 'test@test.com';
    $telefono1 = '1234567890';
    $nombre_departamento = 'IT';
    $nombre_cargo = 'Desarrollador';
    $fecha_ingreso = '2024-01-01';
    $salario_pactado = 1000.00;
    $estatus_empleado = 'A';
    $seguro_social = '123456789';
    $sexo = 'M';
    $nacionalidad = 'Panameña';
    
    echo "<h3>Probando inserción corregida:</h3>";
    
    // SQL con exactamente 16 placeholders y 16 valores
    $sql = "INSERT INTO empleados (
        codigo_empleado, nombre, apellido, fecha_nacimiento, cedula, email, 
        telefono1, nombre_departamento, nombre_cargo, fecha_ingreso, 
        salario_pactado, estatus_empleado, seguro_social, sexo, nacionalidad, 
        es_externo,
        ncodcia, codigo_horario, tarjeta_reloj, dv, cedula_rep_empleador, 
        cedula_reportada, estado_civil, grupo_isr, cantidad_dependientes, 
        tipo_empleado, tipo_sangre, direccion1, direccion2, apartado_postal, 
        telefono2, extension_telefono, tipo_salario, horas_regulares, 
        horas_st_acumuladas, salario_hora, metodo_calculo_isr, 
        hace_declaracion_renta, grupo_pago, codigo_sucursal, codigo_departamento, 
        codigo_division, codigo_centro_costo, codigo_proyecto, codigo_fase, 
        forma_pago, dias_no_trabajados, dias_licencia, pertenece_sindicato, 
        tipo_trabajador, tipo_cuenta, numero_cuenta_ach, numero_banco, 
        subcuenta_mayor_general, referencia_deposito_direc, tiene_vale, 
        es_pasaporte, codigo_custom1, codigo_custom2, codigo_custom3, 
        es_jefe_cuadrilla, es_marino, observaciones, codigo_cargo, 
        codigo_emp_interface1, codigo_emp_interface2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        1, '000000', '000000000000000000', '00', '00000000000000000', 
        '00000000000000000', 'S', '1', 0, '1', 'O+', 'Dirección por defecto', 
        '', '', 'usuario@empresa.com', '0000000000', '0000000000', '1', 
        8.00, 0.00, 0.00, '1', 0, '000000', '001', '000000', '000000', 
        '000000', '0000000000000000000000000', '0000000000000000000000000', 
        '1', 0, 0, 0, 0, '1', '1', '00000000000000000000', '000000000', 
        '000000', '000000000000000', 0, 0, '000000000000000000000000000000', 
        '000000000000000000000000000000', '000000000000000000000000000000', 
        0, 0, 'Sin observaciones', '000000', '000000000000000000000000000000', 
        '000000000000000000000000000000')";
    
    // Contar placeholders
    $placeholder_count = substr_count($sql, '?');
    echo "<p>Número de placeholders (?): " . $placeholder_count . "</p>";
    
    $stmt = $pdo->prepare($sql);
    
    // Array con exactamente 16 parámetros (agregando el que faltaba)
    $params = [
        $test_code, $nombre, $apellido, $fecha_nacimiento, $cedula, $email,
        $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso,
        $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad,
        // Agregando el parámetro que faltaba para es_externo
        1  // Este es el parámetro 16 para es_externo
    ];
    
    echo "<p>Número de parámetros: " . count($params) . "</p>";
    echo "<p>Parámetros: " . implode(', ', $params) . "</p>";
    
    $result = $stmt->execute($params);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Inserción exitosa!</p>";
        
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
    } else {
        echo "<p style='color: red;'>❌ Error en inserción.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
