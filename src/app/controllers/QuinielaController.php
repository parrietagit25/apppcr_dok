<?php

session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Quiniela.php';

if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

$pdo = Database::connect();
$userModel = new User($pdo);
$tipo_usuario = $userModel->get_tyte_user();
$nombre = $userModel->nombre_colaborador();
$codigoSesion = trim((string) ($_SESSION['code'] ?? ''));

$es_administrador_quiniela = ((int) ($tipo_usuario ?? 0) === 1);
if (!$es_administrador_quiniela) {
    header('Location: ' . rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php');
    exit();
}

$quinielaModel = new Quiniela($pdo);

$mensaje = '';
$mensajeTipo = 'info';

if (isset($_GET['v_quiniela']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_grupo_quiniela'])) {
        $orden = (int) ($_POST['orden_grupo'] ?? 0);
        $nomGrupo = trim((string) ($_POST['nombre_grupo'] ?? ''));
        $e1 = trim((string) ($_POST['equipo_1'] ?? ''));
        $e2 = trim((string) ($_POST['equipo_2'] ?? ''));
        $e3 = trim((string) ($_POST['equipo_3'] ?? ''));
        $e4 = trim((string) ($_POST['equipo_4'] ?? ''));
        if ($quinielaModel->crearGrupoConEquipos($orden, $nomGrupo, [$e1, $e2, $e3, $e4])) {
            $mensaje = 'Grupo registrado correctamente con sus partidos de fase de grupos.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se pudo crear el grupo. Verifique que el número de grupo (1–12) no esté duplicado y que los 4 países estén completos.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['eliminar_grupo_quiniela'])) {
        $gid = (int) ($_POST['grupo_id'] ?? 0);
        if ($gid > 0 && $quinielaModel->eliminarGrupo($gid)) {
            $mensaje = 'Grupo eliminado.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se puede eliminar: hay predicciones de colaboradores o el grupo no existe.';
            $mensajeTipo = 'danger';
        }
    }
}

if (isset($_GET['v_resultados']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_resultado_partido'])) {
    $pid = (int) ($_POST['partido_id'] ?? 0);
    $gan = isset($_POST['ganador_id']) ? (int) $_POST['ganador_id'] : 0;
    if ($gan === 0) {
        $quinielaModel->guardarGanadorPartido($pid, null);
        $mensaje = 'Resultado del partido borrado.';
        $mensajeTipo = 'warning';
    } elseif ($quinielaModel->guardarGanadorPartido($pid, $gan)) {
        $mensaje = 'Resultado guardado.';
        $mensajeTipo = 'success';
    } else {
        $mensaje = 'Error al guardar el ganador.';
        $mensajeTipo = 'danger';
    }
}

if (isset($_GET['arma_tu_quiniela']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_quiniela'])) {
    if ($quinielaModel->usuarioCartaCerrada($codigoSesion)) {
        $mensaje = 'Su quiniela ya fue registrada.';
        $mensajeTipo = 'info';
    } else {
        $map = [];
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'pred_') === 0) {
                $map[(int) substr($k, 5)] = (int) $v;
            }
        }
        if ($quinielaModel->guardarQuinielaUsuario($codigoSesion, $map)) {
            $mensaje = '¡Quiniela guardada! Ya no podrá modificarla.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'Complete todos los partidos con un ganador elegido entre los dos equipos.';
            $mensajeTipo = 'danger';
        }
    }
}

$gruposAdmin = $quinielaModel->listarGruposConEquipos();
$partidosAdmin = $quinielaModel->listarPartidosParaResultados();
$totalPartidos = $quinielaModel->totalPartidos();
$cartaCerrada = $quinielaModel->usuarioCartaCerrada($codigoSesion);
$prediccionesDetalle = $cartaCerrada ? $quinielaModel->obtenerPrediccionesUsuarioDetalle($codigoSesion) : [];
$colaboradoresLista = [];
$colaboradoresJson = [];
if (isset($_GET['colaboradores_quiniela'])) {
    $colaboradoresLista = $quinielaModel->listarColaboradoresConQuiniela($pdo);
    foreach ($colaboradoresLista as $c) {
        $colaboradoresJson[$c['codigo_empleado']] = array_merge(
            $quinielaModel->detalleJsonColaborador($c['codigo_empleado']),
            ['nombre' => $c['nombre']]
        );
    }
}

if (isset($_GET['arma_tu_quiniela'])) {
    require_once __DIR__ . '/../views/quiniela_arma.php';
    exit();
}
if (isset($_GET['resultados'])) {
    require_once __DIR__ . '/../views/quiniela_resultados.php';
    exit();
}
if (isset($_GET['v_quiniela'])) {
    require_once __DIR__ . '/../views/quiniela_admin_grupos.php';
    exit();
}
if (isset($_GET['v_resultados'])) {
    require_once __DIR__ . '/../views/quiniela_admin_resultados.php';
    exit();
}
if (isset($_GET['colaboradores_quiniela'])) {
    require_once __DIR__ . '/../views/quiniela_admin_colaboradores.php';
    exit();
}

require_once __DIR__ . '/../views/quiniela_menu.php';
