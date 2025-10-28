<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function authenticate($code, $password) {

        if ($code == '001326') {
            $code = '1326'; // Código de administrador
        }

        try {
            $stmt = $this->pdo->prepare("SELECT el.pass
                                            FROM   empleado_log el
                                            JOIN   empleados    e   ON e.codigo_empleado = el.codigo
                                            WHERE  el.codigo = :code
                                            AND  el.stat  = 1
                                            AND (
                                                e.estatus_empleado IN ('A','V')
                                                OR EXISTS (
                                                    SELECT 1
                                                    FROM   usuarios_fuera_planilla ufp
                                                    WHERE  ufp.codigo_empleado COLLATE utf8mb4_unicode_ci
                                                                = e.codigo_empleado COLLATE utf8mb4_unicode_ci
                                                        AND  ufp.stat = 1
                                                )
                                            )");
                                                
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
    
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['pass'])) {
                return 1; // La autenticación fue exitosa
            } else {
                return false; // Código incorrecto o contraseña inválida
            }
        } catch (PDOException $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            return false;
        }
    }


    public function cambiarEstadoUsuario($codigo, $nuevoEstado) {
        $sql = "UPDATE empleado_log SET stat = ? WHERE codigo = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nuevoEstado, $codigo]);
    }


public function nombre_colaborador() {
    $code = $_SESSION['code'];

    // Buscar primero en la tabla de empleados normal
    $stmt = $this->pdo->prepare("SELECT nombre, apellido FROM empleados WHERE codigo_empleado = :code");
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['nombre']; //. ' ' . $row['apellido'];
    }

    // Si no se encuentra, buscar en colaboradores_externos
    $stmt = $this->pdo->prepare("SELECT nombre, apellido FROM colaboradores_externos WHERE codigo_empleado = :code");
    $stmt->bindParam(':code', $code, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $row['nombre'] . ' ' . $row['apellido'];
    }

    return "";
}  

    public function get_tyte_user(){
        if (isset($_SESSION['code'])) {
            
            $code = $_SESSION['code'];
            $stmt = $this->pdo->prepare("SELECT * FROM empleado_log WHERE codigo  = :code");
            $stmt->bindParam(':code', $code, PDO::PARAM_INT);
            $stmt->execute();
            $nombre = "";
            if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $list_code['type_user'];
            }

        }

    } 

    public function buscar_code_col_reg($codigo){

        $code = $codigo;
        $stmt = $this->pdo->prepare("SELECT count(*) as contar FROM empleados WHERE codigo_empleado = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();

        $nombre = "";
        if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($list_code['contar'] == 1) {
                return 1;
            }elseif($list_code['contar'] == 0){

                $stmt = $this->pdo->prepare("SELECT count(*) as contar FROM encargados_colab WHERE code_empleado = :code");
                $stmt->bindParam(':code', $code, PDO::PARAM_INT);
                $stmt->execute();

                if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($list_code['contar'] >= 1) {
                        return 1;
                    }else {
                        return 0;
                    }
                }else {
                    return 0;
                }
            }else{
                return 0;
            } 

        }

    }

    public function buscar_code_col($codigo){

        $code = $codigo;
        $stmt = $this->pdo->prepare("SELECT count(*) as contar FROM empleados WHERE codigo_empleado  = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();

        $nombre = "";
        if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($list_code['nombre'] == 0) {
                return '<small style="color:green;">Puede registrarse con ese codigo</small>';
            }else{
                return '<small style="color:red;">Colaborador ya registrado con ese codigo</small>';
            } 

           //return $list_code['nombre'];
        }

    }

    public function insertar_colaborador($codigo, $password) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Encripta la contraseña
    
            $sql = "INSERT INTO empleado_log (codigo, pass, stat, type_user) VALUES (:codigo, :password, 1, 2)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    
            return $stmt->execute(); // Devuelve true si la inserción fue exitosa
        } catch (PDOException $e) {
            error_log("Error en la inserción: " . $e->getMessage()); // Guarda errores en el log
            return false;
        }
    }

    public function actualizar_colaborador($pass, $code) {
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
    
        $sql = "UPDATE empleado_log SET pass = ? WHERE codigo = ?";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute([$hashedPass, $code])) {
            return true; // Éxito
        } else {
            return false; // Error
        }
    }

    public function usuarios() {
        $stmt = $this->pdo->prepare("SELECT * FROM empleado_log el inner join empleados e on el.codigo = e.codigo_empleado WHERE el.stat = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function usuarios_encargados() {
        $stmt = $this->pdo->prepare("SELECT * FROM empleado_log el INNER JOIN empleados e ON el.codigo = e.codigo_empleado WHERE el.stat = 1 AND el.type_user = 6");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar_usuarios_autocomplete($termino) {
        try {
            if (empty($termino)) {
                return [];
            }
            
            $termino = '%' . trim($termino) . '%';
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT 
                    e.codigo_empleado, 
                    e.nombre, 
                    e.apellido 
                FROM empleados e 
                INNER JOIN empleado_log el ON e.codigo_empleado = el.codigo 
                WHERE el.stat = 1 
                AND (el.type_user IS NULL OR el.type_user in (2))
                AND (el.type_user IS NULL OR el.type_user <> 6)
                AND (e.nombre LIKE :termino OR e.apellido LIKE :termino OR e.codigo_empleado LIKE :termino)
                ORDER BY e.nombre ASC 
            ");
            $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Asegurar que todos los campos estén presentes
            return array_map(function($row) {
                return [
                    'codigo_empleado' => $row['codigo_empleado'] ?? '',
                    'nombre' => $row['nombre'] ?? '',
                    'apellido' => $row['apellido'] ?? ''
                ];
            }, $resultados);
        } catch (PDOException $e) {
            error_log("Error en buscar_usuarios_autocomplete: " . $e->getMessage());
            return [];
        }
    }

    public function asignar_tipo_encargado($codigo) {
        $stmt = $this->pdo->prepare("UPDATE empleado_log SET type_user = 6 WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function usuarios_no_listados(){
        $stmt = $this->pdo->prepare("SELECT * FROM empleado_log el 
                                    INNER JOIN empleados e ON el.codigo = e.codigo_empleado 
                                    WHERE el.stat = 1 AND e.es_externo = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar_usuario($code, $nombre, $apellido, $fecha_nacimiento) {
        $sql = "UPDATE colaboradores_externos SET nombre = ?, apellido = ?, fecha_nacimiento = ? WHERE codigo_empleado = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nombre, $apellido, $fecha_nacimiento, $code]);
    }

    public function editar_usuario_completo($code, $nombre, $apellido, $fecha_nacimiento, $cedula, $email, $telefono1, $telefono2, $direccion1, $estado_civil, $nombre_departamento, $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad) {
        $sql = "UPDATE empleados SET 
                nombre = ?, apellido = ?, fecha_nacimiento = ?, cedula = ?, email = ?,
                telefono1 = ?, telefono2 = ?, direccion1 = ?, estado_civil = ?, 
                nombre_departamento = ?, nombre_cargo = ?, fecha_ingreso = ?, 
                salario_pactado = ?, estatus_empleado = ?, seguro_social = ?, 
                sexo = ?, nacionalidad = ?
                WHERE codigo_empleado = ? AND es_externo = 1";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $nombre, $apellido, $fecha_nacimiento, $cedula, $email,
            $telefono1, $telefono2, $direccion1, $estado_civil,
            $nombre_departamento, $nombre_cargo, $fecha_ingreso,
            $salario_pactado, $estatus_empleado, $seguro_social,
            $sexo, $nacionalidad, $code
        ]);
    }

    public function registrar_usuario_no_listado($codigo, $nombre, $apellido, $fecha_nacimiento, $password, $stat = 1, $type_user = 2){

        try {
            $this->pdo->beginTransaction();

            // Insertar en colaboradores_externos
            $stmt1 = $this->pdo->prepare("INSERT INTO colaboradores_externos (codigo_empleado, nombre, apellido, fecha_nacimiento) 
                                        VALUES (?, ?, ?, ?)");
            $stmt1->execute([$codigo, $nombre, $apellido, $fecha_nacimiento]);

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
            return false;
        }
    }

    public function registrar_usuario_completo($codigo, $nombre, $apellido, $fecha_nacimiento, $password, $cedula, $email, $telefono1, $telefono2, $direccion1, $estado_civil, $nombre_departamento, $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad, $stat = 1, $type_user = 2){

        try {
            $this->pdo->beginTransaction();

            // Insertar en empleados con todos los campos (marcado como externo)
            $stmt1 = $this->pdo->prepare("INSERT INTO empleados (
                codigo_empleado, nombre, apellido, fecha_nacimiento, cedula, email, 
                telefono1, telefono2, direccion1, estado_civil, nombre_departamento, 
                nombre_cargo, fecha_ingreso, salario_pactado, estatus_empleado, 
                seguro_social, sexo, nacionalidad, es_externo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            
            $stmt1->execute([
                $codigo, $nombre, $apellido, $fecha_nacimiento, $cedula, $email,
                $telefono1, $telefono2, $direccion1, $estado_civil, $nombre_departamento,
                $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado,
                $seguro_social, $sexo, $nacionalidad
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
            // Log del error para debugging
            error_log("Error al registrar usuario completo: " . $e->getMessage());
            error_log("Código: " . $codigo . ", Nombre: " . $nombre);
            return false;
        }
    }

}
