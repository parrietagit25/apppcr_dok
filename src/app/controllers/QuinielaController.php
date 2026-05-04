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
$qMenu = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';

if (!$es_administrador_quiniela) {
    if (isset($_GET['v_quiniela']) || isset($_GET['v_resultados']) || isset($_GET['colaboradores_quiniela'])) {
        header('Location: ' . $qMenu);
        exit();
    }
}

$quinielaModel = new Quiniela($pdo);

$mensaje = '';
$mensajeTipo = 'info';

if (!$es_administrador_quiniela && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminPosts = ['crear_grupo_quiniela', 'eliminar_grupo_quiniela', 'agregar_partido_fijo', 'agregar_partido_ganadores', 'eliminar_partido_quiniela', 'guardar_resultado_partido'];
    foreach ($adminPosts as $k) {
        if (isset($_POST[$k])) {
            header('Location: ' . $qMenu);
            exit();
        }
    }
}

if ($es_administrador_quiniela && isset($_GET['v_quiniela']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_grupo_quiniela'])) {
        $orden = (int) ($_POST['orden_grupo'] ?? 0);
        $nomGrupo = trim((string) ($_POST['nombre_grupo'] ?? ''));
        $e1 = trim((string) ($_POST['equipo_1'] ?? ''));
        $e2 = trim((string) ($_POST['equipo_2'] ?? ''));
        $e3 = trim((string) ($_POST['equipo_3'] ?? ''));
        $e4 = trim((string) ($_POST['equipo_4'] ?? ''));
        if ($quinielaModel->crearGrupoSoloEquipos($orden, $nomGrupo, [$e1, $e2, $e3, $e4])) {
            $mensaje = 'Grupo y equipos registrados. Ahora defina los partidos (enfrentamientos directos o entre ganadores).';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se pudo crear el grupo. Verifique número 1–12 sin duplicar y los 4 equipos.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['eliminar_grupo_quiniela'])) {
        $gid = (int) ($_POST['grupo_id'] ?? 0);
        if ($gid > 0 && $quinielaModel->eliminarGrupo($gid)) {
            $mensaje = 'Grupo eliminado.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se puede eliminar el grupo (predicciones, partidos referenciados o error).';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['agregar_partido_fijo'])) {
        $gidRaw = $_POST['grupo_id_partido'] ?? '';
        $grupoId = ($gidRaw === '' || $gidRaw === null) ? null : (int) $gidRaw;
        $eqL = (int) ($_POST['equipo_local_id'] ?? 0);
        $eqV = (int) ($_POST['equipo_visitante_id'] ?? 0);
        $etq = trim((string) ($_POST['etiqueta_partido'] ?? ''));
        $etq = $etq === '' ? null : $etq;
        if ($quinielaModel->agregarPartidoFijo($grupoId, $eqL, $eqV, $etq)) {
            $mensaje = 'Partido (enfrentamiento directo) agregado.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se pudo agregar el partido. Revise equipos y grupo.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['agregar_partido_ganadores'])) {
        $gidRaw = $_POST['grupo_id_partido_g'] ?? '';
        $grupoId = ($gidRaw === '' || $gidRaw === null) ? null : (int) $gidRaw;
        $sa = (int) ($_POST['src_partido_a'] ?? 0);
        $sb = (int) ($_POST['src_partido_b'] ?? 0);
        $etq = trim((string) ($_POST['etiqueta_partido_g'] ?? ''));
        $etq = $etq === '' ? null : $etq;
        if ($quinielaModel->agregarPartidoGanadores($grupoId, $sa, $sb, $etq)) {
            $mensaje = 'Partido "entre ganadores" agregado.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se pudo agregar. Revise que los dos partidos existan y, si aplica, pertenezcan al mismo grupo.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['eliminar_partido_quiniela'])) {
        $pid = (int) ($_POST['partido_id'] ?? 0);
        if ($pid > 0 && $quinielaModel->eliminarPartido($pid)) {
            $mensaje = 'Partido eliminado.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se puede eliminar (hay predicciones u otros partidos dependen de este).';
            $mensajeTipo = 'danger';
        }
    }
}

if ($es_administrador_quiniela && isset($_GET['v_resultados']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_resultado_partido'])) {
    $pid = (int) ($_POST['partido_id'] ?? 0);
    $gan = isset($_POST['ganador_id']) ? (int) $_POST['ganador_id'] : 0;
    if ($gan === 0) {
        $quinielaModel->guardarGanadorPartido($pid, null);
        $mensaje = 'Resultado borrado.';
        $mensajeTipo = 'warning';
    } elseif ($quinielaModel->guardarGanadorPartido($pid, $gan)) {
        $mensaje = 'Resultado guardado.';
        $mensajeTipo = 'success';
    } else {
        $mensaje = 'No se pudo guardar: el ganador debe ser uno de los dos equipos en juego (en partidos "entre ganadores", ambos partidos previos deben tener resultado).';
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
            $mensaje = 'Revise todas las selecciones. En partidos entre ganadores, elija solo entre los ganadores que ya eligió en los partidos anteriores.';
            $mensajeTipo = 'danger';
        }
    }
}

$gruposAdmin = $quinielaModel->listarGruposConEquipos();
$partidosAdmin = $quinielaModel->listarTodosPartidosOrdenados();
$equiposSelector = $quinielaModel->listarTodosEquiposParaSelector();
$partidosPorGrupo = [];
foreach ($gruposAdmin as $g) {
    $partidosPorGrupo[$g['id']] = $quinielaModel->listarPartidosPorGrupo((int) $g['id']);
}
$partidosLlave = $quinielaModel->listarPartidosPorGrupo(null);
$totalPartidos = $quinielaModel->totalPartidos();
$cartaCerrada = $quinielaModel->usuarioCartaCerrada($codigoSesion);
$prediccionesDetalle = $cartaCerrada ? $quinielaModel->obtenerPrediccionesUsuarioDetalle($codigoSesion) : [];
$colaboradoresLista = [];
$colaboradoresJson = [];
if ($es_administrador_quiniela && isset($_GET['colaboradores_quiniela'])) {
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
