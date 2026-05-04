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
    $adminPosts = [
        'crear_grupo_quiniela',
        'eliminar_grupo_quiniela',
        'agregar_partido_fijo',
        'agregar_partido_ganadores',
        'eliminar_partido_quiniela',
        'guardar_resultado_partido',
        'actualizar_meta_partido',
    ];
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
        $equiposCrear = [];
        for ($slot = 1; $slot <= 4; $slot++) {
            $nom = trim((string) ($_POST['equipo_' . $slot] ?? ''));
            $isoRaw = strtolower(preg_replace('/[^a-z]/', '', (string) ($_POST['equipo_iso_' . $slot] ?? '')));
            $iso = strlen($isoRaw) === 2 ? $isoRaw : null;
            $equiposCrear[] = ['nombre' => $nom, 'iso' => $iso];
        }
        if ($quinielaModel->crearGrupoSoloEquipos($orden, $nomGrupo, $equiposCrear)) {
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
        $eqA = (int) ($_POST['equipo_a_id'] ?? $_POST['equipo_local_id'] ?? 0);
        $eqB = (int) ($_POST['equipo_b_id'] ?? $_POST['equipo_visitante_id'] ?? 0);
        $etq = trim((string) ($_POST['etiqueta_partido'] ?? ''));
        $etq = $etq === '' ? null : $etq;
        $fase = trim((string) ($_POST['fase_partido'] ?? ''));
        $fase = $fase === '' ? null : $fase;
        if ($quinielaModel->agregarPartidoFijo($grupoId, $eqA, $eqB, $etq, $fase)) {
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
        $fase = trim((string) ($_POST['fase_partido_g'] ?? ''));
        $fase = $fase === '' ? null : $fase;
        if ($quinielaModel->agregarPartidoGanadores($grupoId, $sa, $sb, $etq, $fase)) {
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
    } elseif (isset($_POST['actualizar_meta_partido'])) {
        $pid = (int) ($_POST['meta_partido_id'] ?? 0);
        $ord = (int) ($_POST['meta_orden'] ?? 0);
        $faseM = trim((string) ($_POST['meta_fase'] ?? ''));
        $faseM = $faseM === '' ? null : $faseM;
        $etqM = trim((string) ($_POST['meta_etiqueta'] ?? ''));
        $etqM = $etqM === '' ? null : $etqM;
        if ($pid > 0 && $ord > 0 && $quinielaModel->actualizarPartidoMeta($pid, $ord, $faseM, $etqM)) {
            $mensaje = 'Partido actualizado (orden / fase / etiqueta).';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'No se pudo actualizar el partido.';
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

if (isset($_GET['arma_tu_quiniela']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($quinielaModel->usuarioCartaCerrada($codigoSesion)) {
        $mensaje = 'Su quiniela ya está cerrada; no se puede modificar.';
        $mensajeTipo = 'info';
    } else {
        $mapPost = [];
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'pred_') === 0) {
                $mapPost[(int) substr($k, 5)] = (int) $v;
            }
        }
        if (isset($_POST['guardar_progreso_quiniela'])) {
            if ($quinielaModel->guardarProgresoPredicciones($codigoSesion, $mapPost)) {
                $mensaje = 'Progreso guardado. Los cruces posteriores se actualizan según sus elecciones válidas.';
                $mensajeTipo = 'success';
            } else {
                $mensaje = 'No se pudo guardar el progreso.';
                $mensajeTipo = 'danger';
            }
        } elseif (isset($_POST['confirmar_quiniela'])) {
            if ($quinielaModel->guardarProgresoPredicciones($codigoSesion, $mapPost)
                && $quinielaModel->confirmarCartaColaborador($codigoSesion)) {
                $mensaje = '¡Quiniela confirmada! Ya no podrá modificarla.';
                $mensajeTipo = 'success';
            } else {
                $mensaje = 'No se puede confirmar: complete todos los partidos en orden y elija ganadores válidos en cada cruce (incluido el campeón). Guarde el progreso antes si hace falta.';
                $mensajeTipo = 'danger';
            }
        }
    }
}

$gruposAdmin = $quinielaModel->listarGruposConEquipos();
$partidosAdmin = $quinielaModel->listarTodosPartidosOrdenados();
$equiposSelector = $quinielaModel->listarTodosEquiposParaSelector();
$quinielaIsoPorEquipoId = $quinielaModel->mapaIsoPorEquipoId();
$quinielaEquipoMetaPorId = $quinielaModel->mapaEquipoMetaPorId();
$partidosPorGrupo = [];
foreach ($gruposAdmin as $g) {
    $partidosPorGrupo[$g['id']] = $quinielaModel->listarPartidosPorGrupo((int) $g['id']);
}
$partidosLlave = $quinielaModel->listarPartidosPorGrupo(null);
$totalPartidos = $quinielaModel->totalPartidos();
$cartaCerrada = $quinielaModel->usuarioCartaCerrada($codigoSesion);
$mapaPrediccionesUsuario = $quinielaModel->obtenerMapaPredicciones($codigoSesion);
$prediccionesDetalle = $cartaCerrada ? $quinielaModel->obtenerPrediccionesUsuarioDetalle($codigoSesion) : [];
$colaboradoresLista = [];
$colaboradoresJson = [];
if ($es_administrador_quiniela && isset($_GET['colaboradores_quiniela'])) {
    $colaboradoresLista = $quinielaModel->listarResumenColaboradoresQuiniela($pdo);
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
