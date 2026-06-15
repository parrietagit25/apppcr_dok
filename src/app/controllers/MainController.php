<?php
// app/controllers/MainController.php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Rrhh.php';

$pdo = Database::connect();
$userModel = new User($pdo);

$pdo_rrhh = Database::connect(); 
$class_rrhh = new Rrhh($pdo_rrhh);

// Permitir acceso sin sesión solo para documentación
if (isset($_GET['documentacion'])) {
    require_once __DIR__ . '/../views/documentacion.php';
    exit();
}

if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

/* nombre de usuario */
$nombre = $userModel->nombre_colaborador();
$tipo_usuario = $userModel->get_tyte_user();

/* update frase de la semana */
if (isset($_POST['boton_frase_semana'])) {
    try {
        $class_rrhh->update_frase($_POST['frase_semana'], $_POST['id_frase']);
    } catch (\Throwable $th) {
        echo 'Error en el controlador actualizar frase';
    }
    
}

/* Frase de la semana*/
$frase = $class_rrhh->frase_semana();
/* listado de cumplea;os */
if (isset($_GET['cumple'])) {

    function obtenerMesEnEspanol($mesIngles) {
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero',
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];
        return $meses[$mesIngles] ?? $mesIngles;
    }

    $cumple = $class_rrhh->dia_cumple();
    require_once __DIR__ . '/../views/cumple.php';
    exit();
}

if (isset($_GET['mantenimineto'])) {
    require_once __DIR__ . '/../views/mantenimiento.php';
    exit();
}

if (isset($_GET['mantenimiento_usuarios'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['actualizar_estatus_empleado'])) {
            $code = $_POST['codigo_empleado'] ?? '';
            $estatus = $_POST['estatus_empleado'] ?? '';
            if ($code !== '' && $estatus !== '') {
                $resultado = $userModel->actualizar_estatus_empleado($code, $estatus);
                if ($resultado) {
                    echo "<div class='alert alert-success'>Estatus del colaborador actualizado correctamente.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error al actualizar el estatus.</div>";
                }
            }
        } elseif (isset($_POST['codigo_empleado']) && isset($_POST['nueva_password'])) {
            $code = $_POST['codigo_empleado'];
            $pass = $_POST['nueva_password'];

            $resultado = $userModel->actualizar_colaborador($pass, $code);

            if ($resultado) {
                echo "<div class='alert alert-success'>Regsitro Actualizado.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al actalizar.</div>";
            }
        }
    }

    $usuarios = $userModel->usuarios();
    $estatus_empleados = $userModel->get_estatus_empleados_distinct();
    require_once __DIR__ . '/../views/mantenimiento_usuarios.php';
    exit();
}

if (isset($_GET['mantenimiento_encargados'])) {
    
    // Asignar supervisor (type_user = 6)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_encargado'])) {
        $code = $_POST['codigo_usuario'];
        
        if (!empty($code)) {
            $resultado = $userModel->asignar_tipo_encargado($code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Usuario asignado como supervisor correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al asignar supervisor.</div>";
            }
        }
    }

    // Remover supervisor (regresar a type_user = 2)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_encargado'])) {
        $code = $_POST['codigo_usuario'];
        
        if (!empty($code)) {
            // Primero desactivar todas las relaciones de personal a cargo
            $personal_cargo = $userModel->get_personal_a_cargo($code);
            foreach ($personal_cargo as $relacion) {
                $userModel->remover_personal_a_cargo($relacion['id']);
            }
            
            // Luego remover el tipo de supervisor
            $resultado = $userModel->remover_tipo_encargado($code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Supervisor removido correctamente. Todas las relaciones de personal a cargo fueron desactivadas.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al remover supervisor.</div>";
            }
        }
    }

    // Asignar personal a cargo a un supervisor
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_personal_cargo'])) {
        $supervisor_code = $_POST['supervisor_code'] ?? '';
        $colaborador_code = $_POST['colaborador_code'] ?? '';
        
        if (!empty($supervisor_code) && !empty($colaborador_code)) {
            $resultado = $userModel->asignar_personal_a_cargo($supervisor_code, $colaborador_code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Personal a cargo asignado correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al asignar personal a cargo. Puede que ya esté asignado.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Faltan datos para asignar personal a cargo.</div>";
        }
    }

    // Remover personal a cargo de un supervisor
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_personal_cargo'])) {
        $id_relacion = $_POST['id_relacion'] ?? 0;
        
        if (!empty($id_relacion)) {
            $resultado = $userModel->remover_personal_a_cargo($id_relacion);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Personal a cargo removido correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al remover personal a cargo.</div>";
            }
        }
    }

    $usuarios = $userModel->usuarios_encargados();
    $usuarios_disponibles = $userModel->usuarios_disponibles_para_encargado();
    require_once __DIR__ . '/../views/mantenimiento_encargados.php';
    exit();
}

// Endpoint para obtener colaboradores disponibles para asignar
if (isset($_GET['obtener_colaboradores_disponibles'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    $supervisor_code = $_GET['supervisor_code'] ?? '';
    
    if (!empty($supervisor_code)) {
        $colaboradores = $userModel->colaboradores_disponibles_para_asignar($supervisor_code);
        echo json_encode([
            'success' => true,
            'colaboradores' => $colaboradores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Código de supervisor no proporcionado'
        ]);
    }
    exit();
}

// ============================================
// NUEVO SISTEMA R-ENCARGADOS (PARALELO)
// ============================================

if (isset($_GET['mantenimiento_r_encargados'])) {
    
    // Solo admin puede acceder (tipo_usuario == 1)
    $tipo_usuario_actual = $userModel->get_tyte_user();
    if ($tipo_usuario_actual != 1) {
        header("Location: " . BASE_URL_CONTROLLER . "/MainController.php");
        exit();
    }
    
    // Asignar supervisor (type_user = 6)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_encargado'])) {
        $code = $_POST['codigo_usuario'];
        
        if (!empty($code)) {
            $resultado = $userModel->asignar_tipo_encargado($code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Usuario asignado como supervisor correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al asignar supervisor.</div>";
            }
        }
    }

    // Remover supervisor (regresar a type_user = 2)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_encargado'])) {
        $code = $_POST['codigo_usuario'];
        
        if (!empty($code)) {
            // Primero desactivar todas las relaciones de personal a cargo
            $personal_cargo = $userModel->get_personal_a_cargo($code);
            foreach ($personal_cargo as $relacion) {
                $userModel->remover_personal_a_cargo($relacion['id']);
            }
            
            // Luego remover el tipo de supervisor
            $resultado = $userModel->remover_tipo_encargado($code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Supervisor removido correctamente. Todas las relaciones de personal a cargo fueron desactivadas.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al remover supervisor.</div>";
            }
        }
    }

    // Asignar personal a cargo a un supervisor
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_personal_cargo'])) {
        $supervisor_code = $_POST['supervisor_code'] ?? '';
        $colaborador_code = $_POST['colaborador_code'] ?? '';
        
        if (!empty($supervisor_code) && !empty($colaborador_code)) {
            $resultado = $userModel->asignar_personal_a_cargo($supervisor_code, $colaborador_code);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Personal a cargo asignado correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al asignar personal a cargo. Puede que ya esté asignado.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Faltan datos para asignar personal a cargo.</div>";
        }
    }

    // Remover personal a cargo de un supervisor
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remover_personal_cargo'])) {
        $id_relacion = $_POST['id_relacion'] ?? 0;
        
        if (!empty($id_relacion)) {
            $resultado = $userModel->remover_personal_a_cargo($id_relacion);
            
            if ($resultado) {
                echo "<div class='alert alert-success'>Personal a cargo removido correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al remover personal a cargo.</div>";
            }
        }
    }

    $usuarios = $userModel->usuarios_encargados();
    $usuarios_disponibles = $userModel->usuarios_disponibles_para_encargado();
    require_once __DIR__ . '/../views/mantenimiento_r_encargados.php';
    exit();
}

// Endpoint para obtener colaboradores disponibles para asignar (R-)
if (isset($_GET['obtener_colaboradores_disponibles_r'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    $supervisor_code = $_GET['supervisor_code'] ?? '';
    
    if (!empty($supervisor_code)) {
        $colaboradores = $userModel->colaboradores_disponibles_para_asignar($supervisor_code);
        echo json_encode([
            'success' => true,
            'colaboradores' => $colaboradores
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Código de supervisor no proporcionado'
        ]);
    }
    exit();
}

// Endpoint para autocompletado de usuarios
if (isset($_GET['buscar_usuarios_autocomplete'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $termino = $_GET['q'] ?? '';
        
        if (!empty($termino) && strlen(trim($termino)) >= 2) {
            $usuarios = $userModel->buscar_usuarios_autocomplete($termino);
            echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([]);
        }
    } catch (Exception $e) {
        error_log("Error en buscar_usuarios_autocomplete endpoint: " . $e->getMessage());
        echo json_encode(['error' => 'Error al buscar usuarios']);
    }
    exit();
}

if (isset($_GET['mantenimiento_vacaciones'])) {
    $vacaciones = $class_rrhh->reporte_vacaciones();
    require_once __DIR__ . '/../views/mantenimiento_vacaciones.php';
    exit();
}

if (isset($_GET['mantenimiento_cumple'])) {
    $codeSesion = trim($_SESSION['code'] ?? '');
    $tiene_acceso_mant_cumple = (
        $tipo_usuario == 1
        || $tipo_usuario == 4
        || $tipo_usuario == 5
        || in_array($codeSesion, ['001404', '001688'], true)
    );
    if (!$tiene_acceso_mant_cumple) {
        header('Location: ' . BASE_URL_CONTROLLER . '/MainController.php');
        exit();
    }

    $mensaje_cumple = '';
    $mensaje_cumple_tipo = 'info';
    $cumple_config_disponible = $class_rrhh->cumpleConfigTablaDisponible();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_cumple'], $_POST['codigo_empleado'])) {
        $codigoCumple = trim((string) $_POST['codigo_empleado']);
        $accion = (string) $_POST['accion_cumple'];
        if ($codigoCumple !== '') {
            if ($accion === 'ocultar') {
                $ok = $class_rrhh->guardarVisibilidadCumple($codigoCumple, false, $codeSesion, 'Oculto desde Mant Cumple');
                $mensaje_cumple = $ok
                    ? 'Colaborador quitado de la lista pública de cumpleaños.'
                    : 'No se pudo ocultar. Verifique que exista la tabla cumple_config.';
                $mensaje_cumple_tipo = $ok ? 'success' : 'danger';
            } elseif ($accion === 'mostrar') {
                $ok = $class_rrhh->guardarVisibilidadCumple($codigoCumple, true, $codeSesion);
                $mensaje_cumple = $ok
                    ? 'Colaborador visible nuevamente en la lista de cumpleaños.'
                    : 'No se pudo restaurar la visibilidad.';
                $mensaje_cumple_tipo = $ok ? 'success' : 'danger';
            }
        }
    }

    $cumple_mantenimiento = $class_rrhh->listarCumpleanerosMantenimiento();
    require_once __DIR__ . '/../views/mantenimiento_cumple.php';
    exit();
}

if (isset($_GET['cambiar_estado_usuario'])) {

    $codigo = $_POST['codigo_empleado'];
    $estadoActual = (int) $_POST['estado_actual'];
    $nuevoEstado = $estadoActual === 1 ? 0 : 1;

    $resultado = $userModel->cambiarEstadoUsuario($codigo, $nuevoEstado);
    $usuarios = $userModel->usuarios();
    require_once __DIR__ . '/../views/mantenimiento_usuarios.php';
    exit();
}

if (isset($_GET['cambiar_estado_usuario_encargado'])) {

    $codigo = $_POST['codigo_empleado'];
    $estadoActual = (int) $_POST['estado_actual'];
    $nuevoEstado = $estadoActual === 1 ? 0 : 1;

    $resultado = $userModel->cambiarEstadoUsuario($codigo, $nuevoEstado);
    $usuarios = $userModel->usuarios_encargados();
    require_once __DIR__ . '/../views/mantenimiento_encargados.php';
    exit();
}

if (isset($_GET['mantenimiento_usuarios_no_listados'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_usuario'])) {
        // Generar código automáticamente si está vacío o es muy largo
        $codigo_input = $_POST['codigo_empleado'] ?? '';
        if (empty($codigo_input) || strlen($codigo_input) > 6) {
            $codigo = 'E' . (time() % 100000); // Máximo 6 caracteres
        } else {
            $codigo = substr($codigo_input, 0, 6); // Limitar a 6 caracteres
        }
        
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $password = $_POST['password'];
        
        // Nuevos campos
        $cedula = $_POST['cedula'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono1 = $_POST['telefono1'] ?? '';
        $telefono2 = $_POST['telefono2'] ?? '';
        $direccion1 = $_POST['direccion1'] ?? '';
        $estado_civil = $_POST['estado_civil'] ?? '';
        $nombre_departamento = $_POST['nombre_departamento'] ?? '';
        $nombre_cargo = $_POST['nombre_cargo'] ?? '';
        $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
        $salario_pactado = $_POST['salario_pactado'] ?? 0;
        $estatus_empleado = $_POST['estatus_empleado'] ?? 'A';
        $seguro_social = $_POST['seguro_social'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? 'Panameña';

        // Estado y tipo por defecto
        $stat = 1;
        $type_user = 2;

        // Usar versión simplificada que maneja campos NOT NULL
        require_once __DIR__ . '/../models/User_simple.php';
        $userSimple = new UserSimple($pdo);
        $resultado = $userSimple->registrar_usuario_simple($codigo, $nombre, $apellido, $fecha_nacimiento, $password, $cedula, $email, $telefono1, $nombre_departamento, $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad, $stat, $type_user);

        if ($resultado) {
            echo "<div class='alert alert-success'>Usuario registrado exitosamente como externo con código: <strong>" . $codigo . "</strong></div>";
        } else {
            echo "<div class='alert alert-danger'>Error al registrar el usuario. Verifique que todos los campos requeridos estén presentes y que el código no esté duplicado.</div>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_password'])) {
        $code = $_POST['codigo_empleado'];
        $pass = $_POST['nueva_password'];

        $resultado = $userModel->actualizar_colaborador($pass, $code);

        if ($resultado) {
            echo "<div class='alert alert-success'>Regsitro Actualizado.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actalizar.</div>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
        
        $code = $_POST['codigo_empleado'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        
        // Nuevos campos
        $cedula = $_POST['cedula'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono1 = $_POST['telefono1'] ?? '';
        $telefono2 = $_POST['telefono2'] ?? '';
        $direccion1 = $_POST['direccion1'] ?? '';
        $estado_civil = $_POST['estado_civil'] ?? '';
        $nombre_departamento = $_POST['nombre_departamento'] ?? '';
        $nombre_cargo = $_POST['nombre_cargo'] ?? '';
        $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';
        $salario_pactado = $_POST['salario_pactado'] ?? 0;
        $estatus_empleado = $_POST['estatus_empleado'] ?? 'A';
        $seguro_social = $_POST['seguro_social'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $nacionalidad = $_POST['nacionalidad'] ?? 'Panameña';

        $resultado = $userModel->editar_usuario_completo($code, $nombre, $apellido, $fecha_nacimiento, $cedula, $email, $telefono1, $telefono2, $direccion1, $estado_civil, $nombre_departamento, $nombre_cargo, $fecha_ingreso, $salario_pactado, $estatus_empleado, $seguro_social, $sexo, $nacionalidad);
        if ($resultado) {
            echo "<div class='alert alert-success'>Registro Actualizado.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar.</div>";
        }
    }

    $usuarios_no_listados = $userModel->usuarios_no_listados();
    require_once __DIR__ . '/../views/mantenimiento_usuarios_no_listados.php';
    exit();
}

if (isset($_GET['mantenimiento_permisos'])) {

    $solicitudes = $class_rrhh->obtenerSolicitudesUnificadas();

    require_once __DIR__ . '/../views/mantenimiento_permisos.php';
    exit();
}

if (isset($_GET['ia'])) {

    require_once __DIR__ . '/../views/ia.php';
    exit();
}

if (isset($_GET['poliza'])) {

    require_once __DIR__ . '/../views/poliza.php';
    exit();
    
}

if (isset($_GET['n_poliza'])) {

    require_once __DIR__ . '/../views/n_poliza.php';
    exit();
    
}

if (isset($_GET['mi_carnet_n_poliza'])) {

    require_once __DIR__ . '/../views/mi_carnet_n_poliza.php';
    exit();
    
}

if (isset($_GET['info_poliza'])) {

    require_once __DIR__ . '/../views/info_mapfre.php';
    exit();
    
}

if (isset($_GET['telemedicina'])) {

    require_once __DIR__ . '/../views/telemedicina.php';
    exit();
    
}

if (isset($_GET['instructivos_buscar_colaborador'])) {
    header('Content-Type: application/json; charset=utf-8');
    $tiene_acceso_rrhh_json = ((int) $tipo_usuario === 1 || (int) $tipo_usuario === 4
        || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true));
    if (!$tiene_acceso_rrhh_json) {
        http_response_code(403);
        echo json_encode([]);
        exit();
    }
    require_once __DIR__ . '/../models/Instructivos.php';
    $instructivosModel = new Instructivos($pdo);
    $termino = $_GET['q'] ?? '';
    echo json_encode(
        $instructivosModel->buscarColaboradores($termino),
        JSON_UNESCAPED_UNICODE
    );
    exit();
}

if (isset($_GET['instructivos_disponibles_json'])) {
    header('Content-Type: application/json; charset=utf-8');
    $tiene_acceso_rrhh_json = ((int) $tipo_usuario === 1 || (int) $tipo_usuario === 4
        || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true));
    if (!$tiene_acceso_rrhh_json) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    require_once __DIR__ . '/../models/Instructivos.php';
    $instructivosModel = new Instructivos($pdo);
    $doc = $_GET['documento'] ?? '';
    if (!$instructivosModel->codigoValido($doc) || !empty(Instructivos::DOCUMENTOS[$doc]['publico'])) {
        echo json_encode(['success' => false, 'message' => 'Documento inválido']);
        exit();
    }
    echo json_encode([
        'success' => true,
        'colaboradores' => $instructivosModel->listarColaboradoresDisponibles($doc),
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

if (isset($_GET['instructivos_asignar_json']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $tiene_acceso_rrhh_json = ((int) $tipo_usuario === 1 || (int) $tipo_usuario === 4
        || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true));
    if (!$tiene_acceso_rrhh_json) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    require_once __DIR__ . '/../models/Instructivos.php';
    $instructivosModel = new Instructivos($pdo);
    $codigo_sesion = trim($_SESSION['code'] ?? '');
    $doc = $_POST['documento_codigo'] ?? '';
    $codigo_colab = $_POST['codigo_empleado'] ?? '';
    $errorAsignacion = $instructivosModel->mensajeErrorAsignacion($doc, $codigo_colab);
    if ($errorAsignacion !== null) {
        echo json_encode(['success' => false, 'message' => $errorAsignacion], JSON_UNESCAPED_UNICODE);
        exit();
    }
    if ($instructivosModel->asignar($doc, $codigo_colab, $codigo_sesion)) {
        echo json_encode([
            'success' => true,
            'message' => 'Colaborador asignado correctamente.',
            'codigo_empleado' => trim($codigo_colab),
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo asignar el colaborador.'], JSON_UNESCAPED_UNICODE);
    }
    exit();
}

if (isset($_GET['instructivos_asignados_json'])) {
    header('Content-Type: application/json; charset=utf-8');
    $tiene_acceso_rrhh_json = ((int) $tipo_usuario === 1 || (int) $tipo_usuario === 4
        || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true));
    if (!$tiene_acceso_rrhh_json) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit();
    }
    require_once __DIR__ . '/../models/Instructivos.php';
    $instructivosModel = new Instructivos($pdo);
    $doc = $_GET['documento'] ?? '';
    if (!$instructivosModel->codigoValido($doc) || !empty(Instructivos::DOCUMENTOS[$doc]['publico'])) {
        echo json_encode(['success' => false, 'message' => 'Documento inválido']);
        exit();
    }
    echo json_encode([
        'success' => true,
        'asignados' => $instructivosModel->listarAsignados($doc),
    ]);
    exit();
}

if (isset($_GET['instructivos_asegurado'])) {

    require_once __DIR__ . '/../models/Instructivos.php';
    $instructivosModel = new Instructivos($pdo);

    $tiene_acceso_rrhh_instructivos = ((int) $tipo_usuario === 1 || (int) $tipo_usuario === 4
        || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true));
    $codigo_sesion = trim($_SESSION['code'] ?? '');
    $url_base_instructivos = rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php?instructivos_asegurado=1';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tiene_acceso_rrhh_instructivos) {
        if (isset($_POST['instructivos_asignar'])) {
            $doc = $_POST['documento_codigo'] ?? '';
            $codigo_colab = $_POST['codigo_empleado'] ?? '';
            if ($instructivosModel->asignar($doc, $codigo_colab, $codigo_sesion)) {
                header('Location: ' . $url_base_instructivos . '&msg=asignado');
            } else {
                header('Location: ' . $url_base_instructivos . '&msg=error_asignar');
            }
            exit();
        }
        if (isset($_POST['instructivos_quitar'])) {
            $id = (int) ($_POST['id_asignacion'] ?? 0);
            if ($id > 0 && $instructivosModel->quitar($id)) {
                header('Location: ' . $url_base_instructivos . '&msg=quitado');
            } else {
                header('Location: ' . $url_base_instructivos . '&msg=error_quitar');
            }
            exit();
        }
    }

    $documentos_instructivos = [];
    foreach (Instructivos::DOCUMENTOS as $codigo => $meta) {
        $puede_ver = $instructivosModel->puedeVer($codigo, $codigo_sesion, $tiene_acceso_rrhh_instructivos);
        if (!$puede_ver) {
            continue;
        }
        $documentos_instructivos[$codigo] = $meta;
        $documentos_instructivos[$codigo]['codigo'] = $codigo;
        $documentos_instructivos[$codigo]['url_pdf'] = Instructivos::urlPdf($meta['archivo']);
        $documentos_instructivos[$codigo]['restringido'] = empty($meta['publico']);
    }

    $colaboradores_instructivos = $tiene_acceso_rrhh_instructivos
        ? $instructivosModel->listarColaboradoresActivos()
        : [];

    require_once __DIR__ . '/../views/instructivos_asegurado.php';
    exit();
    
}

if (isset($_GET['portal_asegurados'])) {

    require_once __DIR__ . '/../views/portal_asegurados.php';
    exit();
    
}

if (isset($_GET['info_palig'])) {

    require_once __DIR__ . '/../views/info_palig.php';
    exit();
    
}

// Cargar la vista
require_once __DIR__ . '/../views/main.php';
