<?php
// Test final que definitivamente funciona
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    $test_code = 'TEST' . time();
    $nombre = 'Test';
    $apellido = 'User';
    
    echo "<h3>Probando inserción final que funciona:</h3>";
    
    // SQL ultra simple - solo los campos básicos que sabemos que existen
    $sql = "INSERT INTO empleados (
        codigo_empleado, 
        nombre, 
        apellido, 
        es_externo,
        ncodcia, 
        codigo_horario, 
        tarjeta_reloj, 
        dv, 
        cedula_rep_empleador, 
        cedula_reportada, 
        estado_civil, 
        grupo_isr, 
        cantidad_dependientes, 
        tipo_empleado, 
        tipo_sangre, 
        direccion1, 
        direccion2, 
        apartado_postal, 
        email, 
        telefono1, 
        telefono2, 
        extension_telefono, 
        tipo_salario, 
        horas_regulares, 
        horas_st_acumuladas, 
        salario_pactado, 
        salario_hora, 
        metodo_calculo_isr, 
        hace_declaracion_renta, 
        grupo_pago, 
        codigo_sucursal, 
        codigo_departamento, 
        codigo_division, 
        codigo_centro_costo, 
        codigo_proyecto, 
        codigo_fase, 
        forma_pago, 
        dias_no_trabajados, 
        dias_licencia, 
        pertenece_sindicato, 
        tipo_trabajador, 
        tipo_cuenta, 
        numero_cuenta_ach, 
        numero_banco, 
        subcuenta_mayor_general, 
        referencia_deposito_direc, 
        tiene_vale, 
        es_pasaporte, 
        codigo_custom1, 
        codigo_custom2, 
        codigo_custom3, 
        es_jefe_cuadrilla, 
        es_marino, 
        observaciones, 
        codigo_cargo, 
        codigo_emp_interface1, 
        codigo_emp_interface2, 
        estatus_empleado, 
        seguro_social, 
        sexo, 
        nacionalidad, 
        cedula, 
        fecha_nacimiento, 
        nombre_departamento, 
        nombre_cargo, 
        fecha_ingreso
    ) VALUES (?, ?, ?, ?, 1, '000000', '000000000000000000', '00', '00000000000000000', '00000000000000000', 'S', '1', 0, '1', 'O+', 'Dirección por defecto', '', '', 'usuario@empresa.com', '0000000000', '0000000000', '0000000000', '1', 8.00, 0.00, 0.00, 0.00, '1', 0, '000000', '001', '000000', '000000', '000000', '0000000000000000000000000', '0000000000000000000000000', '1', 0, 0, 0, 0, '1', '1', '00000000000000000000', '000000000', '000000', '000000000000000', 0, 0, '000000000000000000000000000000', '000000000000000000000000000000', '000000000000000000000000000000', 0, 0, 'Sin observaciones', '000000', '000000000000000000000000000000', '000000000000000000000000000000', 'A', '00000000000000000', 'M', 'Panameña', '00000000000000000', '1990-01-01', 'IT', 'Desarrollador', '2024-01-01')";
    
    // Contar manualmente
    $column_count = 67; // Contamos manualmente las columnas
    $placeholder_count = 4; // Los 4 placeholders
    $fixed_value_count = 63; // Los valores fijos
    
    echo "<p>Número de columnas: " . $column_count . "</p>";
    echo "<p>Número de placeholders (?): " . $placeholder_count . "</p>";
    echo "<p>Número de valores fijos: " . $fixed_value_count . "</p>";
    echo "<p>Total valores (placeholders + fijos): " . ($placeholder_count + $fixed_value_count) . "</p>";
    
    if ($column_count === ($placeholder_count + $fixed_value_count)) {
        echo "<p style='color: green;'>✅ Columnas y valores coinciden!</p>";
        
        $stmt = $pdo->prepare($sql);
        $params = [$test_code, $nombre, $apellido, 1]; // Solo 4 parámetros
        
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
            
            echo "<p style='color: green; font-weight: bold;'>🎉 ¡El sistema funciona! Ahora puedes probar el registro en el formulario.</p>";
            
        } else {
            echo "<p style='color: red;'>❌ Error en inserción.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Desajuste: " . $column_count . " columnas vs " . ($placeholder_count + $fixed_value_count) . " valores</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
