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

// Zona administrativa: admin (tipo 1), RRHH (tipo 4) o supervisores autorizados
$es_administrador_quiniela = (
    (int) ($tipo_usuario ?? 0) === 1
    || (int) ($tipo_usuario ?? 0) === 4
    || in_array($codigoSesion, ['001404', '001688'], true)
);
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
        'guardar_oficial_grupos',
        'guardar_oficial_fase',
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
            $mensaje = 'Grupo y equipos registrados.';
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
            $mensaje = 'No se puede eliminar el grupo (hay selecciones de quiniela u oficial que lo referencian).';
            $mensajeTipo = 'danger';
        }
    }
}

if ($es_administrador_quiniela && isset($_GET['v_resultados']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar_oficial_grupos'])) {
        $gruposAdminPost = $quinielaModel->listarGruposConEquipos();
        $filas = [];
        $err = false;
        foreach ($gruposAdminPost as $g) {
            $gid = (int) $g['id'];
            $key = 'of_grupo_' . $gid;
            $sel = isset($_POST[$key]) && is_array($_POST[$key]) ? array_map('intval', $_POST[$key]) : [];
            $sel = array_values(array_unique($sel));
            if (count($sel) !== 2) {
                $mensaje = 'Debe elegir exactamente 2 clasificados por grupo.';
                $mensajeTipo = 'danger';
                $err = true;
                break;
            }
            $valid = array_column($g['equipos'], 'id');
            foreach ($sel as $eid) {
                if (!in_array($eid, $valid, true)) {
                    $mensaje = 'Equipo inválido en grupo.';
                    $mensajeTipo = 'danger';
                    $err = true;
                    break 2;
                }
                $filas[] = ['equipo_id' => $eid, 'grupo_id' => $gid];
            }
        }
        if (!$err && count($filas) > 0 && $quinielaModel->guardarOficialFase(Quiniela::F_GRUPOS, $filas)) {
            $mensaje = 'Clasificados oficiales por grupo guardados.';
            $mensajeTipo = 'success';
        } elseif (!$err) {
            $mensaje = 'No se pudo guardar.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['guardar_oficial_fase'])) {
        $fase = trim((string) ($_POST['fase_oficial'] ?? ''));
        $ids = isset($_POST['equipo_oficial']) && is_array($_POST['equipo_oficial'])
            ? array_map('intval', $_POST['equipo_oficial'])
            : [];
        if ($ids === [] && $fase === Quiniela::F_FINAL && isset($_POST['equipo_oficial_rad']) && is_numeric($_POST['equipo_oficial_rad'])) {
            $ids = [(int) $_POST['equipo_oficial_rad']];
        }
        $esperado = Quiniela::cuentaEsperadaFase($fase);
        if ($esperado <= 0 || count($ids) !== $esperado) {
            $mensaje = 'Cantidad incorrecta de equipos para esta fase.';
            $mensajeTipo = 'danger';
        } else {
            $filas = [];
            foreach ($ids as $eid) {
                $row = ['equipo_id' => $eid];
                if ($fase === Quiniela::F_MEJORES_TERCEROS) {
                    $gid = null;
                    $st = $pdo->prepare('SELECT grupo_id FROM quiniela_equipo WHERE id = ?');
                    $st->execute([$eid]);
                    $gid = $st->fetchColumn();
                    $row['grupo_id'] = $gid !== false ? (int) $gid : null;
                }
                $filas[] = $row;
            }
            if ($quinielaModel->guardarOficialFase($fase, $filas)) {
                $mensaje = 'Resultado oficial guardado.';
                $mensajeTipo = 'success';
            } else {
                $mensaje = 'No se pudo guardar.';
                $mensajeTipo = 'danger';
            }
        }
    }
}

if (isset($_GET['arma_tu_quiniela']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($quinielaModel->quinielaEstaCerrada($codigoSesion)) {
        $mensaje = 'Su quiniela está cerrada; no se puede modificar.';
        $mensajeTipo = 'info';
    } elseif (isset($_POST['guardar_fase_grupos'])) {
        $gruposPost = $quinielaModel->listarGruposConEquipos();
        $porGrupo = [];
        foreach ($gruposPost as $g) {
            $gid = (int) $g['id'];
            $key = 'grupo_' . $gid;
            $sel = isset($_POST[$key]) && is_array($_POST[$key]) ? array_map('intval', $_POST[$key]) : [];
            $porGrupo[$gid] = array_values(array_unique($sel));
        }
        if ($quinielaModel->guardarSeleccionGrupos($codigoSesion, $porGrupo)) {
            $mensaje = 'Fase de grupos guardada. Continúe con la siguiente fase.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'Revise que marque exactamente 2 equipos por grupo.';
            $mensajeTipo = 'danger';
        }
    } elseif (isset($_POST['guardar_fase_seleccion'])) {
        $fase = trim((string) ($_POST['fase_guardar'] ?? ''));
        $ids = [];
        if (isset($_POST['equipo_sel']) && is_array($_POST['equipo_sel'])) {
            $ids = array_map('intval', $_POST['equipo_sel']);
        } elseif (isset($_POST['equipo_sel']) && is_numeric($_POST['equipo_sel']) && $fase === Quiniela::F_FINAL) {
            $ids = [(int) $_POST['equipo_sel']];
        }
        if ($quinielaModel->guardarSeleccionFaseLista($codigoSesion, $fase, $ids)) {
            $mensaje = $fase === Quiniela::F_FINAL
                ? '¡Campeón registrado! Su quiniela ha quedado cerrada.'
                : 'Fase guardada.';
            $mensajeTipo = 'success';
        } else {
            $mensaje = 'Selección inválida para esta fase o fase incorrecta.';
            $mensajeTipo = 'danger';
        }
    }
}

$gruposAdmin = $quinielaModel->listarGruposConEquipos();
$equiposSelector = $quinielaModel->listarTodosEquiposParaSelector();
$quinielaIsoPorEquipoId = $quinielaModel->mapaIsoPorEquipoId();
$quinielaEquipoMetaPorId = $quinielaModel->mapaEquipoMetaPorId();

if (isset($_GET['arma_tu_quiniela'])) {
    $quinielaModel->crearCartaSiNoExiste($codigoSesion);
}

$cartaColaborador = $quinielaModel->obtenerCartaPorCodigo($codigoSesion);
$faseActualUsuario = $cartaColaborador ? (string) $cartaColaborador['fase_actual'] : Quiniela::F_GRUPOS;
$cartaCerrada = $quinielaModel->quinielaEstaCerrada($codigoSesion);
$resumenQuinielaUsuario = $cartaColaborador ? $quinielaModel->obtenerResumenQuiniela($codigoSesion) : ['fases' => [], 'cerrada' => false];

$poolDisponibleArma = null;
if ($cartaColaborador && !$cartaCerrada) {
    $poolDisponibleArma = $quinielaModel->obtenerEquiposDisponiblesPorFase($codigoSesion, $faseActualUsuario);
}

$resumenOficialPublico = $quinielaModel->obtenerResumenOficial();
$adminOficialIdsPorFase = [];
$adminPoolOficial = [
    'mejores_terceros' => $quinielaModel->poolTercerosDesdeOficialGrupos(),
    'dieciseisavos' => $quinielaModel->poolDieciseisavosOficial(),
    'octavos' => null,
    'cuartos' => null,
    'semifinal' => null,
    'final' => null,
];
foreach (Quiniela::ordenFases() as $f) {
    $adminOficialIdsPorFase[$f] = $quinielaModel->obtenerIdsOficialFase($f);
}
$idsDieciO = $adminOficialIdsPorFase[Quiniela::F_DIECISEISAVOS] ?? [];
$adminPoolOficial['octavos'] = count($idsDieciO) === 16 ? $idsDieciO : null;
$idsOct = $adminOficialIdsPorFase[Quiniela::F_OCTAVOS] ?? [];
$adminPoolOficial['cuartos'] = count($idsOct) === 8 ? $idsOct : null;
$idsCua = $adminOficialIdsPorFase[Quiniela::F_CUARTOS] ?? [];
$adminPoolOficial['semifinal'] = count($idsCua) === 4 ? $idsCua : null;
$idsSemi = $adminOficialIdsPorFase[Quiniela::F_SEMIFINAL] ?? [];
$adminPoolOficial['final'] = count($idsSemi) === 2 ? $idsSemi : null;

$oficialGruposPorId = $quinielaModel->oficialGruposPorGrupo();

$mapSelGruposUsuario = [];
$idsSeleccionFasePantalla = [];
if ($cartaColaborador) {
    $cidu = (int) $cartaColaborador['id'];
    $mapSelGruposUsuario = $quinielaModel->seleccionGruposPorGrupo($cidu);
    $idsSeleccionFasePantalla = $quinielaModel->obtenerIdsSeleccionFase($cidu, $faseActualUsuario);
}

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
if (isset($_GET['v_resultados'])) {
    require_once __DIR__ . '/../views/quiniela_admin_resultados.php';
    exit();
}
if (isset($_GET['v_quiniela'])) {
    require_once __DIR__ . '/../views/quiniela_admin_grupos.php';
    exit();
}
if (isset($_GET['colaboradores_quiniela'])) {
    require_once __DIR__ . '/../views/quiniela_admin_colaboradores.php';
    exit();
}

require_once __DIR__ . '/../views/quiniela_menu.php';
