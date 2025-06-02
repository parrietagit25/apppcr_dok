<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Rrhh {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function get_departamentos() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $stmt = $this->pdo->prepare("SELECT DISTINCT(nombre_departamento) FROM empleados");
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function datos_colaborador() {

        $code = $_SESSION['code'];
    
        // Lista local de colaboradores
        $colaboradores = [
            "00111111" => ["nombre" => "César", "apellido" => "Durufour"],
            "00111112" => ["nombre" => "Ricardo", "apellido" => "De La Guardia"],
            "00111122" => ["nombre" => "Marilin", "apellido" => "Santos"],
            "00111113" => ["nombre" => "Oscar", "apellido" => "Castillo"],
            "00111114"  => ["nombre" => "Daska", "apellido" => "Vaz"],
            "00111115"  => ["nombre" => "Herminda", "apellido" => "Sánchez"],
            "00111116"  => ["nombre" => "David", "apellido" => "Jordan"],
            "00111117"  => ["nombre" => "Luis", "apellido" => "Pinilla"],
            "00111118"  => ["nombre" => "Rigoberto", "apellido" => "López"],
            "00111119"  => ["nombre" => "Jaime", "apellido" => "Cedeño"],
            "00111110"  => ["nombre" => "Diana", "apellido" => "Rico"],
            "00111120"  => ["nombre" => "Giovanni", "apellido" => "Colucci"],
        ];
    
        // Si está en la lista local, devolver datos simulados
        if (array_key_exists($code, $colaboradores)) {
            return [
                [
                    "codigo_empleado" => $code,
                    "nombre" => $colaboradores[$code]["nombre"],
                    "apellido" => $colaboradores[$code]["apellido"]
                ]
            ];
        }
    
        // Si no está, consultar en la base de datos
        $stmt = $this->pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
    
        $array_datos = [];
    
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
    
        return $array_datos;
    }

    public function datos_jefes($codigo) {

        $code = $codigo;
        
        // ✅ Usar `$this->pdo` en lugar de `$pdo`
        $stmt = $this->pdo->prepare("SELECT * FROM encargados_colab WHERE code_empleado = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();

        // ✅ Inicializar `$array_datos` antes de usarlo
        $array_datos = [];

        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }

        return $array_datos;
    }

    public function mis_vacaciones() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        $stmt = $this->pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function carta_trabajo($descripcion) {
        
        $code_user = $_SESSION['code']; 
        $stat = 1;            
        $file_add = "";       
        $id_user_aprobado = 0; 
    
        $sql = "INSERT INTO carta_trabajo (code_user, descripcion, fecha_log, stat, file_add, id_user_aprobado) 
                VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :id_user_aprobado)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code_user', $code_user, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':file_add', $file_add, PDO::PARAM_STR);
        $stmt->bindParam(':id_user_aprobado', $id_user_aprobado, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Carta solicitada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al generar la carta.</div>";
        }
    }

    public function solicitudes() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        $stmt = $this->pdo->prepare("SELECT id, descripcion, fecha_log, 
                                        CASE stat
                                            WHEN 1 THEN 'Solicitado'
                                            WHEN 2 THEN 'Aprobado'
                                            WHEN 3 THEN 'Borrado'
                                        END AS estado, 
                                        file_add FROM carta_trabajo WHERE stat in(1,2) AND code_user = :code");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }


    public function solicitudes_aprobar() {

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log,
                                            CASE ct.stat
                                                WHEN 1 THEN 'Solicitado'
                                                WHEN 2 THEN 'Aprobado'
                                                WHEN 3 THEN 'Borrado'
                                            END AS estado,
                                            c.nombre,
                                            c.apellido,
                                            ct.file_add
                                        FROM carta_trabajo ct
                                        INNER JOIN empleados c
                                        ON ct.code_user COLLATE utf8mb4_unicode_ci = c.codigo_empleado COLLATE utf8mb4_unicode_ci
                                        WHERE ct.stat IN (1, 2)
                                        ORDER BY ct.id DESC;");
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function aprobar_carta_trabajo($id_carta, $archivo, $comentario) {
        $id_user_aprobado = $_SESSION['code'];
        $stat = 2;
        
        $stmt = $this->pdo->prepare("UPDATE carta_trabajo 
                                      SET stat = :stat, file_add = :archivo, id_user_aprobado = :id_user_aprobado 
                                      WHERE id = :id_carta");
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':archivo', $archivo, PDO::PARAM_STR);
        $stmt->bindParam(':id_user_aprobado', $id_user_aprobado, PDO::PARAM_INT);
        $stmt->bindParam(':id_carta', $id_carta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function get_email_colaborador($id_carta) {
        $stmt = $this->pdo->prepare("SELECT c.email FROM carta_trabajo ct 
                                                    INNER JOIN empleados c ON ct.code_user COLLATE utf8mb4_unicode_ci = c.codigo_empleado COLLATE utf8mb4_unicode_ci 
                                                    WHERE ct.id = :id_carta");
        $stmt->bindParam(':id_carta', $id_carta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_email_permiso($id_permiso) {
        $stmt = $this->pdo->prepare("SELECT e.email, e.nombre, e.apellido FROM solicitud_permiso sp 
                                                    INNER JOIN empleados e ON sp.code COLLATE utf8mb4_unicode_ci = e.codigo_empleado COLLATE utf8mb4_unicode_ci 
                                                    WHERE sp.id = :id_permiso");
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incapacidad_vrrhh() {

        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Enviado'
                                            WHEN 2 THEN 'Revisado'
                                            WHEN 3 THEN 'Anulado'
                                        END AS estado, 
                                        c.nombre,
                                        ct.file_add FROM incapacidad ct inner join col_datos_generales c on ct.code_user = c.codigo");
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function incapacidad() {

        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Enviado'
                                            WHEN 2 THEN 'Revisado'
                                            WHEN 3 THEN 'Anulado'
                                        END AS estado, 
                                        c.nombre,
                                        ct.file_add FROM incapacidad ct inner join col_datos_generales c on ct.code_user = c.codigo  
                                        WHERE 
                                        ct.stat in(1,2)
                                        AND 
                                        ct.code_user = '".$code."'");
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function insertar_incapacidad($code_user, $descripcion, $file_add, $stat = 1, $id_user_aprobado = 0) {
        $sql = "INSERT INTO incapacidad (code_user, descripcion, fecha_log, stat, file_add, id_user_aprobado) 
                VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :id_user_aprobado)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code_user', $code_user, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':file_add', $file_add, PDO::PARAM_STR);
        $stmt->bindParam(':id_user_aprobado', $id_user_aprobado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update_incapacidad($incapacidad){

        $stat = 2;
        $stmt = $this->pdo->prepare("UPDATE incapacidad 
                                      SET stat = :stat
                                      WHERE id = :id_incapacidad");
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':id_incapacidad', $incapacidad, PDO::PARAM_INT);
        
        return $stmt->execute();

    }

    public function calamidades() {

        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Solicitado'
                                            WHEN 2 THEN 'Revisado'
                                        END AS estado, 
                                        c.nombre,
                                        ct.file_add FROM calamidades ct inner join col_datos_generales c on ct.code_user = c.codigo  
                                        WHERE 
                                        ct.stat in(1, 2) 
                                        and 
                                        ct.code_user = '".$code."'");

        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function calamidades_rrhh() {

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Solicitado'
                                            WHEN 2 THEN 'Revisado'
                                        END AS estado, 
                                        c.nombre,
                                        ct.file_add FROM calamidades ct inner join col_datos_generales c on ct.code_user = c.codigo  
                                        WHERE 
                                        ct.stat in(1, 2)");

        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function insertar_calamidades($code_user, $descripcion, $file_add, $monto, $stat = 1, $user_update = 0){

        $sql = "INSERT INTO calamidades (code_user, descripcion, fecha_log, stat, file_add, user_update, monto) 
        VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :user_update, :monto)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code_user', $code_user, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':file_add', $file_add, PDO::PARAM_STR);
        $stmt->bindParam(':user_update', $user_update, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);

        return $stmt->execute();

    }

    public function update_calamidad($calamidad){

        $stat = 2;
        $stmt = $this->pdo->prepare("UPDATE calamidades 
                                      SET stat = :stat
                                      WHERE id = :id_calamidad");
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':id_calamidad', $calamidad, PDO::PARAM_INT);
        
        return $stmt->execute();

    }

    public function frase_semana(){

        $stmt_frase = $this->pdo->prepare("SELECT id, frase FROM frase_semana WHERE stat = 1 and id = (select max(id) from frase_semana WHERE stat = 1)");
        $stmt_frase->execute();
        $frase = "";
        if ($list_frase = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            
            return $list_frase;
        }

    }

    public function update_frase($frase_semana, $id_frase) {
        $stmt = $this->pdo->prepare("UPDATE frase_semana SET frase = :frase WHERE id = :id");
        $stmt->bindParam(':frase', $frase_semana, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_frase, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function insertar_eval($titulo_eva, $select_departamento, $link_eval){
        $sql = "INSERT INTO rrhh_evaluaciones(titulo, departamento, link, stat) 
        VALUES (:titulo, :departamento, :link, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo_eva, PDO::PARAM_STR);
        $stmt->bindParam(':departamento', $select_departamento, PDO::PARAM_STR);
        $stmt->bindParam(':link', $link_eval, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function select_eval(){

        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT * FROM rrhh_evaluaciones WHERE stat = 1");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;

    }

    public function get_departamento($codigo){

        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT nombre_departamento FROM empleados WHERE codigo_empleado  = '".$codigo."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;

    }

    public function select_eval_departamento($departamneto){
        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT * FROM rrhh_evaluaciones WHERE departamento = :departamento");
        $stmt_frase->bindParam(':departamento', $departamneto, PDO::PARAM_STR);
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }
    /*
    public function dia_cumple(){

        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT *, DAY(fecha_nacimiento) AS dia_cumpleaños
                                            FROM empleados 
                                            WHERE MONTH(fecha_nacimiento) = MONTH(CURDATE()) AND DAY(fecha_nacimiento) >= DAY(CURDATE()) AND estatus_empleado = 'A'
                                            ORDER BY DAY(fecha_nacimiento);
                                            ");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;

    } */

    public function dia_cumple() {
        $array_datos = [];

        $sql = "
            SELECT 
                codigo_empleado COLLATE utf8mb4_unicode_ci AS codigo_empleado,
                nombre COLLATE utf8mb4_unicode_ci AS nombre,
                apellido COLLATE utf8mb4_unicode_ci AS apellido,
                fecha_nacimiento,
                'empleado' AS tipo,
                DAY(fecha_nacimiento) AS dia_cumpleaños
            FROM empleados
            WHERE MONTH(fecha_nacimiento) = MONTH(CURDATE())
            AND DAY(fecha_nacimiento) >= DAY(CURDATE())
            AND estatus_empleado = 'A'

            UNION ALL

            SELECT 
                codigo_empleado COLLATE utf8mb4_unicode_ci AS codigo_empleado,
                nombre COLLATE utf8mb4_unicode_ci AS nombre,
                apellido COLLATE utf8mb4_unicode_ci AS apellido,
                fecha_nacimiento,
                'externo' AS tipo,
                DAY(fecha_nacimiento) AS dia_cumpleaños
            FROM colaboradores_externos
            WHERE MONTH(fecha_nacimiento) = MONTH(CURDATE())
            AND DAY(fecha_nacimiento) >= DAY(CURDATE())

            ORDER BY dia_cumpleaños;
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $row;
        }

        return $array_datos;
    }


    public function insertar_permiso($id_jefe, $descripcion, $tipo_licencia, $fecha_inicio, $fecha_fin, $archivo_adjunto = null){

        $code = $_SESSION['code'];
        $sql = "INSERT INTO solicitud_permiso(descripcion, id_jefe, stat, code, tipo_licencia, fecha_inicio, fecha_fin, archivo_adjunto) 
                VALUES (:descripcion, :id_jefe, 1, :code, :tipo_licencia, :fecha_inicio, :fecha_fin, :archivo_adjunto)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':id_jefe', $id_jefe, is_null($id_jefe) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_licencia', $tipo_licencia, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $stmt->bindParam(':archivo_adjunto', $archivo_adjunto, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function select_permisos(){
        $array_datos = [];
        $code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido FROM solicitud_permiso p inner join empleados e on p.code = e.codigo_empleado  where p.code = '".$code."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }


    public function select_jefe() {
        $nombre_departamento = ''; // ✅ Inicialización por defecto
    
        $stmt_departamento = $this->pdo->prepare("SELECT * FROM `empleados` WHERE `codigo_empleado` = :codigo;");
        $stmt_departamento->bindParam(':codigo', $_SESSION['code']);
        $stmt_departamento->execute();
    
        while ($list_code = $stmt_departamento->fetch(PDO::FETCH_ASSOC)) {
            $nombre_departamento = $list_code['nombre_departamento'];
        }
    
        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT * FROM `encargados_colab` 
                                           WHERE departamento LIKE :departamento");
        $param_departamento = '%' . $nombre_departamento . '%';
        $stmt_frase->bindParam(':departamento', $param_departamento);
        $stmt_frase->execute();
    
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
    
        return $array_datos;
    }
    
    
    public function select_permisos_all() {

        /*

        $code = $_SESSION['code'];
    
        // Lista local de colaboradores con sus departamentos (puedes mover a archivo externo si crece)
        $departamentos_fijos = [
            "00111111" => "OPERACIONES",
            "00111112" => "VENTAS DE AUTOS",
            "00111122" => "ADMINISTRACION",
            "00111113" => "ADMINISTRACION",
            "001142"    => "MERCADEO",
            "002015"    => "CONTAB-COBROS",
            "001082"    => "CONTABILIDAD",
            "00111114"  => "MERCADEO",
            "00111115"  => "TALLER",
            "00111116"  => "OPERACIONES",
            "00111117"  => "OPERACIONES",
            "00111118"  => "OPERACIONES",
            "00111119"  => "OPERACIONES",
            "00111110"  => "OPERACIONES",
            "00111120"  => "RENTALS",
        ];
    
        // Intentar obtener el departamento desde la tabla empleados
        $stmt_departamento = $this->pdo->prepare("SELECT nombre_departamento FROM empleados WHERE codigo_empleado = :code");
        $stmt_departamento->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt_departamento->execute();
    
        $nombre_departamento = "";
        if ($result = $stmt_departamento->fetch(PDO::FETCH_ASSOC)) {
            $nombre_departamento = $result['nombre_departamento'];
        }
    
        // Si no se encuentra en empleados y el código está en la lista, usar el valor de la lista
        if (empty($nombre_departamento) && isset($departamentos_fijos[$code])) {
            $nombre_departamento = $departamentos_fijos[$code];
        }
    
        // Si aún no hay nombre_departamento, buscar en encargados_colab
        if (empty($nombre_departamento)) {
            $stmt_alt = $this->pdo->prepare("SELECT departamento FROM encargados_colab WHERE code_empleado = :code");
            $stmt_alt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt_alt->execute();
    
            if ($encargado = $stmt_alt->fetch(PDO::FETCH_ASSOC)) {
                $nombre_departamento = $encargado['departamento'];
            }
        }
    
        // Consulta final si se tiene un departamento válido
        $array_datos = [];
    
        if (!empty($nombre_departamento)) {
            $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido
                                               FROM solicitud_permiso p
                                               INNER JOIN empleados e ON p.code = e.codigo_empleado
                                               WHERE e.nombre_departamento LIKE :departamento");
            $like_dep = '%' . $nombre_departamento . '%';
            $stmt_frase->bindParam(':departamento', $like_dep, PDO::PARAM_STR);
            $stmt_frase->execute();
    
            while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
                $array_datos[] = $list_code;
            }
        } 

        */

        // solo usuario rrhh y admin pueden ver los permisos, el codigo que esta arriba es para que el jefe lo vea pero ya esto cambio 

        $array_datos = [];

        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido
                                            FROM solicitud_permiso p
                                            INNER JOIN empleados e ON p.code = e.codigo_empleado");
                
        $stmt_frase->execute();

        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
        $array_datos[] = $list_code;
        }
    
        return $array_datos;
    }
    

    public function update_permiso($respuesta, $comentario, $permiso_id){

        if ($respuesta == 'A') {
            $stat = 2;
        }elseif ($respuesta == 'D') {
            $stat = 3;
        }
        
        $stmt = $this->pdo->prepare("UPDATE solicitud_permiso 
                                      SET 
                                      stat = :stat, 
                                      respuesta_jefe = :respuesta_jefe, 
                                      comentario_jefe = :comentario_jefe
                                      WHERE 
                                      id = :permiso_id");

        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':respuesta_jefe', $respuesta, PDO::PARAM_STR);
        $stmt->bindParam(':comentario_jefe', $comentario, PDO::PARAM_STR);
        $stmt->bindParam(':permiso_id', $permiso_id, PDO::PARAM_INT);
        
        return $stmt->execute();

    }

    public function correo_solicitud_permiso($id_permiso){

        $array_datos = [];
        $code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT 
                                                p.*, 
                                                e.nombre, 
                                                e.apellido, 
                                                p.tipo_licencia,
                                                (SELECT CONCAT(nombre, ' ', apellido) 
                                                FROM empleados 
                                                WHERE codigo_empleado = p.id_jefe) AS nombre_jefe
                                            FROM 
                                                solicitud_permiso p 
                                            INNER JOIN 
                                                empleados e ON p.code = e.codigo_empleado 
                                            WHERE 
                                                p.id ='".$id_permiso."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $colaborador = $list_code['nombre']. ' ' .$list_code['apellido'];
            $nombre_jefe = $list_code['nombre_jefe'];
            $descripcion = $list_code['descripcion'];
            $comentario_jefe = $list_code['comentario_jefe'];
            $respuesta_jefe = $list_code['respuesta_jefe'];
            $tipo_licencia = $list_code['tipo_licencia'];
        }
        

        $mensaje_correo = 'Se ha realizado una solicitud de permiso desde el APP PCR <br>
                          el colaborador <b>'.$colaborador .'</b> ha solicitado un permiso de tipo <b>'.$tipo_licencia.'</b> y estos son sus <br> 
                          comentarios <b>'.$descripcion.'</b> <br>
                          Su encargado directo <b>'.$nombre_jefe.'</b> ha aprobado su solicitud y estos son sus <br>
                          comentarios <b>'.$comentario_jefe.'</b>';

        return $mensaje_correo;

    } 

    public function select_vacaciones(){

        $array_datos = [];
        $code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido FROM solicitud_vacaciones p inner join empleados e on p.code_cola = e.codigo_empleado where p.code_cola = '".$code."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;

    }

    public function insertar_vacaciones($id_jefe, $descripcion){
        $code = $_SESSION['code'];
        $sql = "INSERT INTO solicitud_vacaciones(comentario_cola, code_jefe, stat, code_cola) 
                VALUES (:descripcion, :code_jefe, 1, :code_cola)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindValue(':code_jefe', $id_jefe, is_null($id_jefe) ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':code_cola', $code, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function select_vacaciones_all(){
        $array_datos = [];
        $code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido FROM solicitud_vacaciones p inner join empleados e on p.code_cola = e.codigo_empleado where p.code_jefe = '".$code."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function update_vacaciones($respuesta, $comentario, $permiso_id){

        if ($respuesta == 'A') {
            $stat = 2;
        }elseif ($respuesta == 'D') {
            $stat = 3;
        }
        
        $stmt = $this->pdo->prepare("UPDATE solicitud_vacaciones 
                                      SET 
                                      stat = :stat, 
                                      resp_jefe = :respuesta_jefe, 
                                      comentario_jefe = :comentario_jefe
                                      WHERE 
                                      id = :permiso_id");

        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':respuesta_jefe', $respuesta, PDO::PARAM_STR);
        $stmt->bindParam(':comentario_jefe', $comentario, PDO::PARAM_STR);
        $stmt->bindParam(':permiso_id', $permiso_id, PDO::PARAM_INT);
        
        return $stmt->execute();
        
    }

    public function correo_solicitud_vacaciones($id){

        $array_datos = [];
        $code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT 
                                                p.*, 
                                                e.nombre, 
                                                e.apellido, 

                                                (SELECT CONCAT(nombre, ' ', apellido) 
                                                FROM empleados 
                                                WHERE codigo_empleado = p.code_jefe) AS nombre_jefe
                                            FROM 
                                                solicitud_vacaciones p 
                                            INNER JOIN 
                                                empleados e ON p.code_cola = e.codigo_empleado 
                                            WHERE 
                                                p.id ='".$id."'");
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $colaborador = $list_code['nombre']. ' ' .$list_code['apellido'];
            $nombre_jefe = $list_code['nombre_jefe'];
            $descripcion = $list_code['comentario_cola'];
            $comentario_jefe = $list_code['comentario_jefe'];
            $respuesta_jefe = $list_code['resp_jefe'];
        }

        $mensaje_correo = 'Se ha realizado una solicitud de vacaciones desde el APP PCR <br>
                           el colaborador <b>'.$colaborador .'</b> ha solicitado vacaciones y estos son sus <br> 
                           comentarios <b>'.$descripcion.'</b> <br>
                           Su encargado directo <b>'.$nombre_jefe.'</b> ha aprobado su solicitud y estos son sus <br>
                           comentarios <b>'.$comentario_jefe.'</b>';

        return $mensaje_correo;

    } 

    public function enviar_correo($email, $mail_copia, $asunto, $mensaje){

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8'; 

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-mail.outlook.com'; // Cambia esto según tu proveedor
            $mail->SMTPAuth = true;
            $mail->Username = 'notificaciones@grupopcr.com.pa';
            $mail->Password = EMAIL_GLOBAL;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('notificaciones@grupopcr.com.pa', 'PCR notificaciones');
            $mail->addAddress($email);
            //$mail->addCC('rrhh@grupopcr.com.pa', $mail_copia);
            foreach ($mail_copia as $cc) {
                $mail->addCC($cc);
            }

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            $mail->send();
            //return 'Correo enviado correctamente';
        } catch (Exception $e) {
            return "Error al enviar el correo: {$mail->ErrorInfo}";
        } 
    }

    public function obtenerSolicitudesUnificadas() {
        $sql = "
            SELECT 
                'Calamidad' AS tipo,
                e.codigo_empleado AS codigo,
                e.nombre,
                e.apellido,
                c.fecha_log,
                c.descripcion,
                c.file_add
            FROM calamidades c
            INNER JOIN empleados e 
                ON CAST(e.codigo_empleado AS UNSIGNED) = CAST(c.code_user AS UNSIGNED)
            WHERE c.stat = 1

            UNION ALL

            SELECT 
                'Carta de Trabajo' AS tipo,
                e.codigo_empleado AS codigo,
                e.nombre,
                e.apellido,
                ct.fecha_log,
                ct.descripcion,
                ct.file_add
            FROM carta_trabajo ct
            INNER JOIN empleados e 
                ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci
            WHERE ct.stat = 1

            UNION ALL

            SELECT 
                'Permiso' AS tipo,
                e.codigo_empleado AS codigo,
                e.nombre,
                e.apellido,
                sp.fecha_log,
                CONCAT(sp.tipo_licencia, ' - ', sp.descripcion) AS descripcion,
                sp.archivo_adjunto AS file_add
            FROM solicitud_permiso sp
            INNER JOIN empleados e 
                ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
            WHERE sp.stat = 1

            ORDER BY fecha_log DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}



?>
