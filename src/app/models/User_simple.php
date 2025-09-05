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

            // Insertar en empleados con solo campos NOT NULL requeridos
            $stmt1 = $this->pdo->prepare("INSERT INTO empleados (
                codigo_empleado, nombre, apellido, es_externo, ncodcia, cedula, 
                sexo, seguro_social, nacionalidad, email, telefono1, estatus_empleado, 
                salario_pactado, nombre_departamento, nombre_centro_costo, 
                nombre_division, nombre_proyecto, nombre_fase, nombre_sucursal, 
                nombre_cargo, observaciones, fecha_nacimiento, fecha_ingreso
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // 23 parámetros para los campos NOT NULL
            $stmt1->execute([
                $codigo, $nombre, $apellido, 1, 1, $cedula,  // es_externo=1, ncodcia=1
                $sexo, $seguro_social, $nacionalidad, $email, $telefono1, $estatus_empleado,
                $salario_pactado, $nombre_departamento, 'Centro Costo', 'División', 
                'Proyecto', 'Fase', 'Sucursal', $nombre_cargo, 'Sin observaciones', 
                $fecha_nacimiento, $fecha_ingreso
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
