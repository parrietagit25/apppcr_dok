<?php
// Script para modificar la tabla empleados y quitar NOT NULL
require_once 'config/config.php';
require_once 'app/core/Database.php';

try {
    $pdo = Database::connect();
    
    echo "<h3>Modificando tabla empleados para quitar NOT NULL:</h3>";
    
    // Lista de campos que probablemente tienen NOT NULL
    $fields_to_modify = [
        'ncodcia',
        'codigo_horario',
        'tarjeta_reloj',
        'dv',
        'cedula_rep_empleador',
        'cedula_reportada',
        'estado_civil',
        'grupo_isr',
        'cantidad_dependientes',
        'tipo_empleado',
        'tipo_sangre',
        'direccion1',
        'direccion2',
        'apartado_postal',
        'telefono2',
        'extension_telefono',
        'tipo_salario',
        'horas_regulares',
        'horas_st_acumuladas',
        'salario_hora',
        'metodo_calculo_isr',
        'hace_declaracion_renta',
        'grupo_pago',
        'codigo_sucursal',
        'codigo_departamento',
        'codigo_division',
        'codigo_centro_costo',
        'codigo_proyecto',
        'codigo_fase',
        'forma_pago',
        'dias_no_trabajados',
        'dias_licencia',
        'pertenece_sindicato',
        'tipo_trabajador',
        'tipo_cuenta',
        'numero_cuenta_ach',
        'numero_banco',
        'subcuenta_mayor_general',
        'referencia_deposito_direc',
        'tiene_vale',
        'es_pasaporte',
        'codigo_custom1',
        'codigo_custom2',
        'codigo_custom3',
        'es_jefe_cuadrilla',
        'es_marino',
        'observaciones',
        'codigo_cargo',
        'codigo_emp_interface1',
        'codigo_emp_interface2'
    ];
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($fields_to_modify as $field) {
        try {
            // Modificar campo para permitir NULL
            $sql = "ALTER TABLE empleados MODIFY COLUMN `$field` VARCHAR(255) NULL";
            $pdo->exec($sql);
            echo "<p style='color: green;'>✅ Campo '$field' modificado exitosamente</p>";
            $success_count++;
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠️ Campo '$field': " . $e->getMessage() . "</p>";
            $error_count++;
        }
    }
    
    echo "<h4>Resumen:</h4>";
    echo "<p style='color: green;'>✅ Campos modificados exitosamente: $success_count</p>";
    echo "<p style='color: orange;'>⚠️ Campos con errores: $error_count</p>";
    
    if ($success_count > 0) {
        echo "<p style='color: green; font-weight: bold;'>🎉 ¡Tabla modificada! Ahora puedes probar la inserción simple.</p>";
        
        // Probar inserción simple
        echo "<h4>Probando inserción simple:</h4>";
        
        $test_code = 'T' . (time() % 1000);
        $sql = "INSERT INTO empleados (codigo_empleado, nombre, apellido, es_externo) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$test_code, 'Test', 'User', 1]);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Inserción simple exitosa!</p>";
            
            // Limpiar
            $stmt = $pdo->prepare("DELETE FROM empleados WHERE codigo_empleado = ?");
            $stmt->execute([$test_code]);
            echo "<p>Registro de prueba eliminado.</p>";
        } else {
            echo "<p style='color: red;'>❌ Error en inserción simple.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error general: " . $e->getMessage() . "</p>";
    echo "<p>Detalles: " . $e->getTraceAsString() . "</p>";
}
?>
