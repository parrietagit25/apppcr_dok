<?php
// Versión simplificada para registro de usuarios externos
class UserSimple {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar_usuario_simple($codigo, $nombre, $apellido, $fecha_nacimiento, $password, $cedula, $email, $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad, $stat = 1, $type_user = 2){

        try {
            $this->pdo->beginTransaction();

            // Insertar en empleados con campos mínimos requeridos
            $stmt1 = $this->pdo->prepare("INSERT INTO empleados (
                codigo_empleado, nombre, apellido, fecha_nacimiento, cedula, email, 
                telefono1, nombre_departamento, nombre_cargo, fecha_ingreso, 
                salario_pactado, estatus_empleado, seguro_social, sexo, nacionalidad, 
                es_externo,
                -- Campos NOT NULL con valores por defecto
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
                -- Valores por defecto para campos NOT NULL
                1, '000000', '000000000000000000', '00', '00000000000000000', 
                '00000000000000000', 'S', '1', 0, '1', 'O+', 'Dirección por defecto', 
                '', '', 'usuario@empresa.com', '0000000000', '0000000000', '1', 
                8.00, 0.00, 0.00, '1', 0, '000000', '001', '000000', '000000', 
                '000000', '0000000000000000000000000', '0000000000000000000000000', 
                '1', 0, 0, 0, 0, '1', '1', '00000000000000000000', '000000000', 
                '000000', '000000000000000', 0, 0, '000000000000000000000000000000', 
                '000000000000000000000000000000', '000000000000000000000000000000', 
                0, 0, 'Sin observaciones', '000000', '000000000000000000000000000000', 
                '000000000000000000000000000000')");
            
            // Solo 16 parámetros (los primeros 16 campos con ?)
            $stmt1->execute([
                $codigo, $nombre, $apellido, $fecha_nacimiento, $cedula, $email,
                $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso,
                $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad
            ]);

            // Encriptar contraseña
            $pass_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insertar en empleado_log
            $stmt2 = $this->pdo->prepare("INSERT INTO empleado_log (codigo, pass, stat, type_user) 
                                        VALUES (?, ?, ?, ?)");
            $stmt2->execute([$codigo, $pass_hash, $stat, $type_user]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al registrar usuario simple: " . $e->getMessage());
            return false;
        }
    }
}
?>
