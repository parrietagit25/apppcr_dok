<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function authenticate($code, $password) {

        try {
            $stmt = $this->pdo->prepare("SELECT pass FROM empleado_log WHERE codigo = :code AND stat = 1");
            $stmt->bindParam(':code', $code, PDO::PARAM_INT);
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
    

    public function nombre_colaborador() {
        $code = $_SESSION['code'];
    
        // Lista de empleados que no aparecen en el datawere house de payday
        $colaboradores = [
            "00111111" => ["César", "Durufour"],
            "00111112" => ["Ricardo", "De La Guardia"],
            "00111122" => ["Marilin", "Santos"],
            "00111113" => ["Oscar", "Castillo"],
            "001142"    => ["Michelle", "de la Guardia"],
            "002015"    => ["Ivette", "Romero"],
            "001082"    => ["Jorge", "Juan De La Guardia"],
            "00111114"  => ["Daska", "Vaz"],
            "00111115"  => ["Herminda", "Sánchez"],
            "00111116"  => ["David", "Jordan"],
            "00111117"  => ["Luis", "Pinilla"],
            "00111118"  => ["Rigoberto", "López"],
            "00111119"  => ["Jaime", "Cedeño"],
            "00111110"  => ["Diana", "Rico"],
            "00111120"  => ["Giovanni", "Colucci"],
        ];
    
        if (array_key_exists($code, $colaboradores)) {
            return $colaboradores[$code][0] . ' ' . $colaboradores[$code][1];
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
    
        if ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $list_code['nombre'] . ' ' . $list_code['apellido'];
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
    
    
}
