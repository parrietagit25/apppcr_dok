<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Rrhh {
    public $pdo;

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

    public function mis_vacaciones_all_employe() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }

        $stmt = $this->pdo->prepare("SELECT codigo_empleado, nombre, apellido, dias_vaca_acu_tiempo FROM empleados WHERE estatus_empleado = 'A'");
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function mis_vacaciones_all_employe_gerentes($code) {

        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }

        $shit_get_departamento = $this->get_departamento($code);
        $departamento = $shit_get_departamento[0]['nombre_departamento'];

        $stmt = $this->pdo->prepare("SELECT codigo_empleado, nombre, apellido, dias_vaca_acu_tiempo FROM empleados WHERE estatus_empleado = 'A' AND nombre_departamento = :departamento");
        $stmt->bindParam(':departamento', $departamento, PDO::PARAM_STR);
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

    /*
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
    } */

    public function solicitudes() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("
            SELECT 
                ct.id,
                ct.descripcion,
                ct.fecha_log,
                CASE ct.stat
                    WHEN 1 THEN 'Solicitado'
                    WHEN 2 THEN 'Aprobado'
                    WHEN 3 THEN 'Borrado'
                END AS estado,
                ct.file_add,
                -- Indicador si existe carta generada
                CASE 
                    WHEN ct.stat = 2 AND ctf.id IS NOT NULL THEN 1
                    ELSE 0
                END AS carta_generada
            FROM carta_trabajo ct
            LEFT JOIN carta_trabajo_formulario ctf ON ctf.carta_id = ct.id
            WHERE ct.stat IN (1, 2) AND ct.code_user = :code
            ORDER BY ct.fecha_log DESC
        ");
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /* public function solicitudes_aprobar() {

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log,
                                            CASE ct.stat
                                                WHEN 1 THEN 'Solicitado'
                                                WHEN 2 THEN 'Aprobado'
                                                WHEN 3 THEN 'Borrado'
                                            END AS estado,
                                            c.nombre,
                                            c.apellido,
                                            ct.file_add, 
                                            c.codigo_empleado, 
                                            c.salario_pactado, 
                                            c.fecha_ingreso, 
                                            c.cedula, 
                                            c.seguro_social, 
                                            c.nombre_cargo
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
    } */

    public function solicitudes_aprobar() {
        $stmt = $this->pdo->prepare("
            SELECT 
                ct.id,
                IFNULL(ctf.descripcion, ct.descripcion) AS descripcion,
                ct.fecha_log,
                CASE ct.stat
                    WHEN 1 THEN 'Solicitado'
                    WHEN 2 THEN 'Aprobado'
                    WHEN 3 THEN 'Borrado'
                END AS estado,
                IFNULL(ctf.nombre, c.nombre) AS nombre,
                c.apellido,
                IFNULL(TRIM(ctf.nombre), CONCAT(IFNULL(c.nombre,''), ' ', IFNULL(c.apellido,''))) AS nombre_completo,
                ct.file_add, 
                c.codigo_empleado, 
                IFNULL(ctf.salario, c.salario_pactado) AS salario_pactado,
                IFNULL(ctf.fecha_ingreso, c.fecha_ingreso) AS fecha_ingreso,
                IFNULL(ctf.cedula, c.cedula) AS cedula,
                IFNULL(ctf.seguro, c.seguro_social) AS seguro_social,
                IFNULL(ctf.cargo, c.nombre_cargo) AS nombre_cargo,
                ctf.desc_seguro,
                ctf.desc_educativo,
                ctf.desc_renta
            FROM carta_trabajo ct
            INNER JOIN empleados c
                ON CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                CONVERT(c.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            LEFT JOIN carta_trabajo_formulario ctf
                ON ctf.carta_id = ct.id
            WHERE ct.stat IN (1)
            ORDER BY ct.id DESC;
        ");
        
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    /**
     * Obtener cartas de trabajo ya aprobadas (stat = 2)
     */
    public function solicitudes_aprobadas() {
        $stmt = $this->pdo->prepare("
            SELECT 
                ct.id,
                IFNULL(ctf.descripcion, ct.descripcion) AS descripcion,
                ct.fecha_log,
                CASE ct.stat
                    WHEN 1 THEN 'Solicitado'
                    WHEN 2 THEN 'Aprobado'
                    WHEN 3 THEN 'Borrado'
                END AS estado,
                IFNULL(ctf.nombre, c.nombre) AS nombre,
                c.apellido,
                IFNULL(TRIM(ctf.nombre), CONCAT(IFNULL(c.nombre,''), ' ', IFNULL(c.apellido,''))) AS nombre_completo,
                ct.file_add, 
                c.codigo_empleado, 
                IFNULL(ctf.salario, c.salario_pactado) AS salario_pactado,
                IFNULL(ctf.fecha_ingreso, c.fecha_ingreso) AS fecha_ingreso,
                IFNULL(ctf.cedula, c.cedula) AS cedula,
                IFNULL(ctf.seguro, c.seguro_social) AS seguro_social,
                IFNULL(ctf.cargo, c.nombre_cargo) AS nombre_cargo,
                ctf.desc_seguro,
                ctf.desc_educativo,
                ctf.desc_renta
            FROM carta_trabajo ct
            INNER JOIN empleados c
                ON CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                CONVERT(c.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            LEFT JOIN carta_trabajo_formulario ctf
                ON ctf.carta_id = ct.id
            WHERE ct.stat IN (2)
            ORDER BY ct.id DESC;
        ");
        
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }


     /* public function aprobar_carta_trabajo($id_carta, $archivo, $comentario) {
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
    } */

    public function aprobar_carta_trabajo($id_carta) {
        $id_user_aprobado = $_SESSION['code'];
        $stat = 2;
        
        $stmt = $this->pdo->prepare("UPDATE carta_trabajo 
                                      SET stat = :stat, id_user_aprobado = :id_user_aprobado 
                                      WHERE id = :id_carta");
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':id_user_aprobado', $id_user_aprobado, PDO::PARAM_INT);
        $stmt->bindParam(':id_carta', $id_carta, PDO::PARAM_INT);
        
        return $stmt->execute();
    } 

    public function get_email_colaborador($id_carta) {
        $stmt = $this->pdo->prepare("SELECT c.email FROM carta_trabajo ct 
                                                    INNER JOIN empleados c 
                                                    ON CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                                                    CONVERT(c.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
                                                    WHERE ct.id = :id_carta");
        $stmt->bindParam(':id_carta', $id_carta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_email_colaborador_incapacidad($id_incapacidad) {
        $stmt = $this->pdo->prepare("SELECT 
                                        c.email, 
                                        c.nombre, 
                                        c.apellido 
                                    FROM incapacidad ct
                                    INNER JOIN empleados c 
                                        ON LPAD(ct.code_user, CHAR_LENGTH(c.codigo_empleado), '0') = c.codigo_empleado
                                    WHERE ct.id = :id_incapacidad");
        $stmt->bindParam(':id_incapacidad', $id_incapacidad, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_email_permiso($id_permiso) {
        $stmt = $this->pdo->prepare("SELECT e.email, e.nombre, e.apellido FROM solicitud_permiso sp 
                                                    INNER JOIN empleados e 
                                                    ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                                                    CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
                                                    WHERE sp.id = :id_permiso");
        $stmt->bindParam(':id_permiso', $id_permiso, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incapacidad_vrrhh() {

        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("SELECT 
                                        ct.id, 
                                        ct.descripcion, 
                                        ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Enviado'
                                            WHEN 2 THEN 'Revisado'
                                            WHEN 3 THEN 'Anulado'
                                        END AS estado, 
                                        CONCAT(e.nombre, ' ', e.apellido) AS nombre,
                                        ct.file_add
                                    FROM incapacidad ct
                                    INNER JOIN empleados e 
                                        ON ct.code_user = RIGHT(e.codigo_empleado, CHAR_LENGTH(e.codigo_empleado) - 2)");


        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }

    public function incapacidad() {

        $code = $_SESSION['code'];

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, ct.fecha_retroactiva,
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

    public function insertar_incapacidad($code_user, $descripcion, $file_add, $stat = 1, $id_user_aprobado = 0, $fecha_retroactiva = null) {
        $sql = "INSERT INTO incapacidad (code_user, descripcion, fecha_log, stat, file_add, id_user_aprobado, fecha_retroactiva) 
                VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :id_user_aprobado, :fecha_retroactiva)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code_user', $code_user, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':file_add', $file_add, PDO::PARAM_STR);
        $stmt->bindParam(':id_user_aprobado', $id_user_aprobado, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_retroactiva', $fecha_retroactiva, $fecha_retroactiva !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        
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
        $code = substr($code, 2);

        $stmt = $this->pdo->prepare("SELECT ct.id, ct.descripcion, ct.fecha_log, ct.code_user, 
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

    public function calamidades_gerentes($code) {

        $shit_get_departamento = $this->get_departamento($code);
        $departamento = $shit_get_departamento[0]['nombre_departamento'];

        $stmt = $this->pdo->prepare("SELECT * FROM calamidades 
                                     inner join 
                                     empleados ON CONCAT('00', calamidades.code_user) = empleados.codigo_empleado 
                                     WHERE 
                                     empleados.nombre_departamento = :departamento");

        $stmt->bindParam(':departamento', $departamento, PDO::PARAM_STR);
        $stmt->execute();
        $array_datos = [];
        while ($list_code = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    }



    public function calamidades_rrhh() {

        $stmt = $this->pdo->prepare("SELECT 
                                        ct.id, 
                                        ct.descripcion, 
                                        ct.fecha_log, 
                                        CASE ct.stat
                                            WHEN 1 THEN 'Solicitado'
                                            WHEN 2 THEN 'Revisado'
                                        END AS estado, 
                                        c.departamento,
                                        ct.monto,
                                        ct.plazo,
                                        ct.forma_pago,
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

    public function insertar_calamidades($code_user, $descripcion, $file_add, $monto, $plazo, $forma_pago, $stat = 1, $user_update = 0){

        $sql = "INSERT INTO calamidades (code_user, descripcion, fecha_log, stat, file_add, user_update, monto, plazo, forma_pago) 
        VALUES (:code_user, :descripcion, CURRENT_TIMESTAMP(), :stat, :file_add, :user_update, :monto, :monto, :forma_pago)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':code_user', $code_user, PDO::PARAM_INT);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':stat', $stat, PDO::PARAM_INT);
        $stmt->bindParam(':file_add', $file_add, PDO::PARAM_STR);
        $stmt->bindParam(':user_update', $user_update, PDO::PARAM_INT);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':plazo', $plazo, PDO::PARAM_STR);
        $stmt->bindParam(':forma_pago', $forma_pago, PDO::PARAM_STR);

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

    /**
     * Departamentos donde el código está como encargado.
     * Comparación por valor numérico para que 0013 y 13 coincidan (id_jefe/code puede ser int o string con/sin ceros).
     */
    public function get_departamento_encargados($codigo){
        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT departamento FROM encargados_colab WHERE CAST(CAST(code_empleado AS CHAR) AS UNSIGNED) = CAST(:codigo AS UNSIGNED)");
        $stmt_frase->bindParam(':codigo', $codigo, PDO::PARAM_STR);
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
            AND codigo_empleado not in('002567', '001023')

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

            UNION ALL

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
            AND codigo_empleado in('002465')

            ORDER BY dia_cumpleaños;
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $row;
        }

        return $array_datos;
    }


    /**
     * Valida que el rango de fechas para permiso tipo Vacaciones cumpla la regla:
     * - Del 1 al 15 del mes, O
     * - Del 16 al último día del mes (en febrero al 28/29), O
     * - Del 1 al último día del mes (mes completo).
     * No se puede cruzar el límite 15/16 salvo que sea el mes completo (ej: 10 al 20 es inválido).
     * @return array ['valido' => bool, 'mensaje' => string]
     */
    public static function validar_rango_vacaciones($fecha_inicio, $fecha_fin) {
        $inicio = \DateTime::createFromFormat('Y-m-d', $fecha_inicio);
        $fin = \DateTime::createFromFormat('Y-m-d', $fecha_fin);
        if (!$inicio || !$fin || $inicio > $fin) {
            return ['valido' => false, 'mensaje' => 'Fechas inválidas.'];
        }
        // Opción adicional: del 1 al último día del mismo mes (mes completo)
        $ultimo_del_mes = (clone $inicio)->modify('last day of this month');
        if ($inicio->format('j') == 1 && $fin->format('Y-m-d') === $ultimo_del_mes->format('Y-m-d')) {
            return ['valido' => true, 'mensaje' => ''];
        }
        $dia_inicio = (int) $inicio->format('j');
        $bloque_inicio = $dia_inicio <= 15 ? '1-15' : '16-fin';
        $intervalo = new \DateInterval('P1D');
        $fin->modify('+1 day');
        $periodo = new \DatePeriod($inicio, $intervalo, $fin);
        foreach ($periodo as $fecha) {
            $dia = (int) $fecha->format('j');
            if ($bloque_inicio === '1-15') {
                if ($dia > 15) {
                    return ['valido' => false, 'mensaje' => 'Para vacaciones solo puede elegir del 1 al 15 del mes, del 16 al último día del mes, o del 1 al último día del mes (mes completo). No puede cruzar ambas mitades (ej: del 10 al 20).'];
                }
            } else {
                if ($dia < 16) {
                    return ['valido' => false, 'mensaje' => 'Para vacaciones solo puede elegir del 1 al 15 del mes, del 16 al último día del mes, o del 1 al último día del mes (mes completo). No puede cruzar ambas mitades (ej: del 10 al 20).'];
                }
            }
        }
        return ['valido' => true, 'mensaje' => ''];
    }

    /**
     * Indica si el usuario ya envió hoy una solicitud de permiso de este tipo.
     * Límite: 1 solicitud por tipo de permiso por día (pueden ser casos distintos otro día).
     */
    public function ya_envio_permiso_tipo_hoy($code, $tipo_licencia) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM solicitud_permiso WHERE code = :code AND tipo_licencia = :tipo_licencia AND DATE(fecha_log) = CURDATE() LIMIT 1");
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_licencia', $tipo_licencia, PDO::PARAM_STR);
        $stmt->execute();
        return (bool) $stmt->fetch();
    }

    /**
     * Lista de solicitudes de permiso pendientes (stat = 1) del usuario, para mostrarlas en el formulario.
     */
    public function permisos_pendientes_por_usuario($code) {
        $stmt = $this->pdo->prepare("SELECT id, tipo_licencia, fecha_inicio, fecha_fin, fecha_log, descripcion 
            FROM solicitud_permiso WHERE code = :code AND stat = 1 ORDER BY fecha_log DESC");
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $row;
        }
        return $out;
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

    /**
     * Datos de un empleado por código (para modal detalles en Mi Personal).
     * La vista no debe mostrar estado_civil, sexo, direccion1.
     */
    public function get_datos_empleado_por_codigo($codigo) {
        $stmt = $this->pdo->prepare("SELECT * FROM empleados WHERE codigo_empleado = :codigo");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        unset($row['estado_civil'], $row['sexo'], $row['direccion1'], $row['direccion2']);
        return $row;
    }

    /**
     * Solicitudes de permiso de un empleado por código (para modal permisos en Mi Personal).
     */
    public function get_permisos_por_codigo($codigo) {
        $stmt = $this->pdo->prepare("SELECT p.id, p.tipo_licencia, p.fecha_inicio, p.fecha_fin, p.fecha_log, p.descripcion, p.stat FROM solicitud_permiso p WHERE p.code = :codigo ORDER BY p.fecha_log DESC");
        $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
        $stmt->execute();
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Todos los empleados activos (para Mi Personal cuando es admin).
     */
    public function get_todos_empleados_activos() {
        $stmt = $this->pdo->prepare("SELECT codigo_empleado, nombre, apellido, nombre_departamento, nombre_cargo FROM empleados WHERE estatus_empleado = 'A' ORDER BY nombre ASC, apellido ASC");
        $stmt->execute();
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = $row;
        }
        return $out;
    }

        public function select_permisos_gerentes($code){

        $shit_get_departamento = $this->get_departamento($code);
        $departamento = $shit_get_departamento[0]['nombre_departamento'];

        $array_datos = [];
        //$code = $_SESSION['code'];
        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido FROM solicitud_permiso p inner join empleados e on p.code = e.codigo_empleado  where p.id_jefe = '".$code."'");
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

    /**
     * NUEVO MÉTODO R-SELECT_JEFE - Usa la tabla supervisores_personal_cargo
     * Este método es paralelo al anterior, no lo reemplaza
     */
    public function r_select_jefe() {
        // Obtener el código del colaborador actual desde la sesión
        $colaborador_code = $_SESSION['code'] ?? '';
        
        if (empty($colaborador_code)) {
            return [];
        }
        
        // Consultar los supervisores asignados a este colaborador desde la nueva tabla
        $array_datos = [];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    spc.supervisor_code as code_empleado,
                    spc.supervisor_code as codigo_empleado,
                    e.nombre,
                    e.apellido,
                    e.nombre_departamento as departamento,
                    e.email
                FROM supervisores_personal_cargo spc
                INNER JOIN empleados e ON spc.supervisor_code = e.codigo_empleado
                INNER JOIN empleado_log el ON e.codigo_empleado = el.codigo
                WHERE spc.colaborador_code = :colaborador_code
                AND spc.activo = 1
                AND el.stat = 1
                AND el.type_user = 6
                ORDER BY e.nombre ASC, e.apellido ASC
            ");
            $stmt->bindParam(':colaborador_code', $colaborador_code, PDO::PARAM_STR);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $array_datos[] = $row;
            }
        } catch (PDOException $e) {
            // Si la tabla no existe aún, devolver array vacío
            error_log("Error al consultar supervisores_personal_cargo en r_select_jefe: " . $e->getMessage());
            return [];
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

    public function select_permisos_all_admin($code){

        $deptos = $this->get_departamento_encargados($code);
        $departamentos = array_column($deptos, 'departamento');
    
        if (empty($departamentos)) {
            return [];
        }
    
        // Crear ?,?,? dinámicos
        $placeholders = implode(',', array_fill(0, count($departamentos), '?'));
    
        // Comparar id_jefe por valor numérico para que 0013 y 13 coincidan (campo puede ser int o string)
        $sql = "
            SELECT p.*, e.nombre, e.apellido
            FROM solicitud_permiso p
            INNER JOIN empleados e 
                ON p.code = e.codigo_empleado
            WHERE CAST(CAST(COALESCE(p.id_jefe,0) AS CHAR) AS UNSIGNED) = CAST(?) AS UNSIGNED
              AND e.nombre_departamento IN ($placeholders)
        ";
    
        $stmt = $this->pdo->prepare($sql);
    
        // unir parámetros
        $params = array_merge([$code], $departamentos);
    
        $stmt->execute($params);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener permisos filtrados por supervisor usando supervisores_personal_cargo
     * Este método obtiene los permisos del personal a cargo de un supervisor específico
     */
    public function select_permisos_por_supervisor($supervisor_code) {
        try {
            // Obtener todos los permisos del personal a cargo del supervisor
            // Comparación por valor numérico para que 0013 y 13 coincidan (códigos con/sin ceros a la izquierda)
            $sql = "
                SELECT DISTINCT p.*, e.nombre, e.apellido
                FROM solicitud_permiso p
                INNER JOIN empleados e ON p.code = e.codigo_empleado
                INNER JOIN supervisores_personal_cargo spc
                    ON CAST(CAST(COALESCE(e.codigo_empleado,'') AS CHAR) AS UNSIGNED) = CAST(CAST(COALESCE(spc.colaborador_code,'') AS CHAR) AS UNSIGNED)
                WHERE CAST(CAST(COALESCE(spc.supervisor_code,'') AS CHAR) AS UNSIGNED) = CAST(:supervisor_code AS UNSIGNED)
                AND spc.activo = 1
                ORDER BY p.fecha_log DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $supervisor_code_trimmed = trim($supervisor_code);
            $stmt->bindParam(':supervisor_code', $supervisor_code_trimmed, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug temporal
            error_log("select_permisos_por_supervisor - Supervisor: " . $supervisor_code_trimmed . " - Resultados: " . count($resultados));
            
            return $resultados;
        } catch (PDOException $e) {
            error_log("Error al consultar permisos por supervisor: " . $e->getMessage());
            // Si la tabla no existe, devolver array vacío
            return [];
        }
    }

    /**
     * Obtener todos los supervisores activos (type_user = 6)
     */
    public function get_todos_supervisores() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT
                    e.codigo_empleado,
                    e.nombre,
                    e.apellido,
                    e.nombre_departamento
                FROM empleados e
                INNER JOIN empleado_log el ON e.codigo_empleado = el.codigo
                WHERE el.type_user = 6
                AND el.stat = 1
                ORDER BY e.nombre ASC, e.apellido ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener supervisores: " . $e->getMessage());
            return [];
        }
    }
    

    /* public function select_permisos_all_admin($code) {

        $shit_get_departamento = $this->get_departamento($code);
        $departamento = $shit_get_departamento[0]['nombre_departamento'];

        $array_datos = [];
        $stmt_frase = $this->pdo->prepare("SELECT p.*, e.nombre, e.apellido FROM solicitud_permiso p inner join empleados e on p.code = e.codigo_empleado
        WHERE e.nombre_departamento LIKE :departamento");
        $stmt_frase->bindParam(':departamento', $departamento, PDO::PARAM_STR);
        $stmt_frase->execute();
        while ($list_code = $stmt_frase->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $list_code;
        }
        return $array_datos;
    } */
    

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

    public function eliminar_permiso($permiso_id) {
        // Eliminar físicamente el registro
        $stmt = $this->pdo->prepare("DELETE FROM solicitud_permiso WHERE id = :permiso_id");
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
                sp.tipo_licencia AS tipo,
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

    public function get_email_calamidad($id_calamidad) {
        $stmt = $this->pdo->prepare("SELECT c.email, c.nombre, c.apellido
                                        FROM calamidades ca 
                                        INNER JOIN empleados c 
                                            ON LPAD(ca.code_user, 6, '0') = c.codigo_empleado
                                        WHERE ca.id = :id_calamidad;");
        $stmt->bindParam(':id_calamidad', $id_calamidad, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    public function get_datos_formulario_carta($id_carta) {
        $stmt = $this->pdo->prepare("SELECT 
            IFNULL(ctf.descripcion, ct.descripcion) AS descripcion,
            IFNULL(ctf.nombre, c.nombre) AS nombre,
            c.apellido,
            c.codigo_empleado, 
            IFNULL(ctf.salario, c.salario_pactado) AS salario,
            IFNULL(ctf.fecha_ingreso, c.fecha_ingreso) AS fecha_ingreso,
            IFNULL(ctf.cedula, c.cedula) AS cedula,
            IFNULL(ctf.seguro, c.seguro_social) AS seguro,
            IFNULL(ctf.cargo, c.nombre_cargo) AS cargo,
            IFNULL(ctf.desc_seguro, 0) AS desc_seguro,
            IFNULL(ctf.desc_educativo, 0) AS desc_educativo,
            IFNULL(ctf.desc_renta, 0) AS desc_renta,
            IFNULL(ctf.nombre, CONCAT(IFNULL(c.nombre,''), ' ', IFNULL(c.apellido,''))) AS nombre_completo
            FROM carta_trabajo ct
            INNER JOIN empleados c 
            ON CONVERT(ct.code_user USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
            CONVERT(c.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            LEFT JOIN carta_trabajo_formulario ctf ON ctf.carta_id = ct.id
            WHERE ct.id = :id");
        $stmt->bindParam(':id', $id_carta);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function enviar_correo_con_adjunto($email, $mail_copia, $asunto, $mensaje, $ruta_archivo) {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8'; 

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-mail.outlook.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'notificaciones@grupopcr.com.pa';
            $mail->Password = EMAIL_GLOBAL;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('notificaciones@grupopcr.com.pa', 'PCR notificaciones');
            $mail->addAddress($email);

            foreach ($mail_copia as $cc) {
                $mail->addCC($cc);
            }

            $mail->addAttachment($ruta_archivo); // adjunto PDF

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            $mail->send();
        } catch (Exception $e) {
            return "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }

    public function get_otros_descuentos_por_carta($carta_id) {
        $stmt = $this->pdo->prepare("SELECT acreedor, monto FROM carta_trabajo_descuentos WHERE carta_id = :id");
        $stmt->execute([':id' => $carta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_cartas_trabajo($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM carta_trabajo WHERE 1=1";
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function count_calamidades($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM calamidades WHERE 1=1";
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function count_permisos($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM solicitud_permiso WHERE 1=1";
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function count_incapacidades($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM incapacidad WHERE 1=1";
        $params = [];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Total de solicitudes de uniformes en un período (stat 1, 2, 3)
     */
    public function count_uniformes($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM uniformes WHERE stat IN (1, 2, 3)";
        $params = [];
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Conteo de uniformes por estado: 1=Solicitado, 2=En proceso, 3=Entregado
     */
    public function count_uniformes_por_estado($stat, $fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM uniformes WHERE stat = :stat";
        $params = [':stat' => $stat];
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Total de piezas (suma de cantidad) de uniformes en el período (stat 1,2,3)
     */
    public function sum_uniformes_cantidad($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COALESCE(SUM(cantidad), 0) AS total FROM uniformes WHERE stat IN (1, 2, 3)";
        $params = [];
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Uniformes agrupados por tipo (tipo => cantidad de solicitudes, suma de piezas)
     * Retorna array [ ['tipo' => string, 'solicitudes' => int, 'piezas' => int], ... ]
     */
    public function uniformes_por_tipo($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT tipo, COUNT(*) AS solicitudes, COALESCE(SUM(cantidad), 0) AS piezas 
                FROM uniformes WHERE stat IN (1, 2, 3)";
        $params = [];
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        $sql .= " GROUP BY tipo ORDER BY piezas DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Uniformes agrupados por talla (talla => cantidad de solicitudes, suma de piezas)
     */
    public function uniformes_por_talla($fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT talla, COUNT(*) AS solicitudes, COALESCE(SUM(cantidad), 0) AS piezas 
                FROM uniformes WHERE stat IN (1, 2, 3)";
        $params = [];
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        $sql .= " GROUP BY talla ORDER BY piezas DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_type_permiso($type_permiso, $fecha_desde = null, $fecha_hasta = null) {
        $sql = "SELECT COUNT(*) AS total FROM solicitud_permiso WHERE tipo_licencia = :type_permiso";
        $params = [':type_permiso' => $type_permiso];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function count_permisos_by_type(string $codigo, $fecha_desde = null, $fecha_hasta = null){
        $sql = "SELECT COUNT(*) FROM solicitud_permiso WHERE tipo_licencia = ?";
        $params = [$codigo];
        
        if ($fecha_desde) {
            $sql .= " AND DATE(fecha_log) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $sql .= " AND DATE(fecha_log) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function reporte_vacaciones(){
        $sql = "SELECT 
                    sp.id,
                    e.codigo_empleado,
                    e.nombre,
                    e.apellido,
                    sp.fecha_log,
                    sp.descripcion,
                    sp.tipo_licencia,
                    sp.fecha_inicio,
                    sp.fecha_fin,
                    sp.stat,
                    sp.respuesta_jefe,
                    sp.comentario_jefe,
                    sp.archivo_adjunto
                FROM solicitud_permiso sp
                INNER JOIN empleados e 
                    ON CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
                    CONVERT(sp.code USING utf8mb4) COLLATE utf8mb4_unicode_ci
                WHERE sp.tipo_licencia = 'Vacaciones'
                ORDER BY sp.fecha_log DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ========== MÓDULO DE UNIFORMES ==========
    
    /**
     * Obtener solicitudes de uniformes del colaborador actual
     */
    public function uniformes() {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        
        $stmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.tipo,
                u.talla,
                u.cantidad,
                u.stat,
                u.fecha_log,
                u.fecha_proceso,
                u.fecha_entrega,
                u.codigo_empleado,
                u.observacion,
                e.nombre,
                e.apellido,
                e.nombre_departamento
            FROM uniformes u
            INNER JOIN empleados e 
            ON CONVERT(u.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
            CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            WHERE u.codigo_empleado = :code
            ORDER BY u.fecha_log DESC
        ");
        
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        
        $array_datos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $row;
        }
        
        return $array_datos;
    }
    
    /**
     * Obtener todas las solicitudes de uniformes (para RRHH)
     */
    public function uniformes_todas() {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.tipo,
                u.talla,
                u.cantidad,
                u.stat,
                u.fecha_log,
                u.codigo_empleado,
                u.observacion,
                e.nombre,
                e.apellido,
                e.nombre_departamento,
                e.nombre_cargo
            FROM uniformes u
            INNER JOIN empleados e 
            ON CONVERT(u.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
            CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            WHERE u.stat IN (1, 2, 3)
            ORDER BY u.fecha_log DESC
        ");
        
        $stmt->execute();
        
        $array_datos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $row;
        }
        
        return $array_datos;
    }
    
    /**
     * Crear nueva solicitud de uniforme (un solo producto)
     */
    public function solicitar_uniforme($tipo, $talla, $cantidad = 1, $observacion = '') {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO uniformes 
            (codigo_empleado, tipo, talla, cantidad, observacion, stat, fecha_log)
            VALUES 
            (:codigo_empleado, :tipo, :talla, :cantidad, :observacion, 1, NOW())
        ");
        
        $stmt->bindParam(':codigo_empleado', $code, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':talla', $talla, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':observacion', $observacion, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Crear múltiples solicitudes de uniformes en una sola transacción
     */
    public function solicitar_uniformes_multiples($productos, $observacion = '') {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                INSERT INTO uniformes 
                (codigo_empleado, tipo, talla, cantidad, observacion, stat, fecha_log)
                VALUES 
                (:codigo_empleado, :tipo, :talla, :cantidad, :observacion, 1, NOW())
            ");
            
            foreach ($productos as $producto) {
                $stmt->execute([
                    ':codigo_empleado' => $code,
                    ':tipo' => $producto['tipo'],
                    ':talla' => $producto['talla'],
                    ':cantidad' => $producto['cantidad'],
                    ':observacion' => $observacion
                ]);
            }
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error al insertar uniformes múltiples: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualizar estado de solicitud de uniforme
     */
    public function update_uniforme($uniforme_id, $nuevo_estado) {
        // Preparar campos adicionales según el estado
        $campos_adicionales = "";
        
        if ($nuevo_estado == 2) {
            // En proceso - registrar fecha_proceso
            $campos_adicionales = ", fecha_proceso = NOW()";
        } elseif ($nuevo_estado == 3) {
            // Entregado - registrar fecha_entrega
            $campos_adicionales = ", fecha_entrega = NOW()";
        }
        
        $sql = "UPDATE uniformes 
                SET stat = :stat" . $campos_adicionales . "
                WHERE id = :uniforme_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':stat', $nuevo_estado, PDO::PARAM_INT);
        $stmt->bindParam(':uniforme_id', $uniforme_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar/Cancelar solicitud de uniforme (solo si no está entregado)
     */
    public function eliminar_uniforme($uniforme_id) {
        if (!isset($_SESSION['code'])) {
            die("Error: No hay sesión iniciada.");
        }
        $code = $_SESSION['code'];
        
        // Cambiar stat a 0 (cancelado) solo si pertenece al usuario y no está entregado (stat 3)
        $stmt = $this->pdo->prepare("
            UPDATE uniformes 
            SET stat = 0
            WHERE id = :uniforme_id 
            AND codigo_empleado = :codigo_empleado
            AND stat IN (1, 2)
        ");
        
        $stmt->bindParam(':uniforme_id', $uniforme_id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo_empleado', $code, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener todas las solicitudes de uniformes para RRHH (verificación)
     */
    public function uniformes_vrrhh() {
        $stmt = $this->pdo->prepare("
            SELECT 
                u.id,
                u.tipo,
                u.talla,
                u.cantidad,
                u.stat,
                u.fecha_log,
                u.fecha_proceso,
                u.fecha_entrega,
                u.codigo_empleado,
                u.observacion,
                e.nombre,
                e.apellido,
                e.nombre_departamento,
                e.nombre_cargo
            FROM uniformes u
            INNER JOIN empleados e 
            ON CONVERT(u.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci = 
            CONVERT(e.codigo_empleado USING utf8mb4) COLLATE utf8mb4_unicode_ci
            WHERE u.stat IN (1, 2, 3)
            ORDER BY u.stat ASC, u.fecha_log DESC
        ");
        
        $stmt->execute();
        
        $array_datos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $array_datos[] = $row;
        }
        
        return $array_datos;
    }
    
}



?>
