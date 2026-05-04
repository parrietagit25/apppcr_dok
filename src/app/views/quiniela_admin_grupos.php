<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
require_once __DIR__ . '/../config/quiniela_paises_mundial.php';
include __DIR__ . '/header.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$nGrupos = count($gruposAdmin);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .quiniela-ts-wrap .ts-control { min-height: 38px; border-radius: 0.375rem; }
    .quiniela-ts-wrap.form-select-sm .ts-control { min-height: 31px; font-size: 0.875rem; }
    .ts-dropdown .option { padding: 0.35rem 0.5rem; }
    .quiniela-flag-img { object-fit: cover; flex-shrink: 0; }
    .ts-control .item .quiniela-flag-img { vertical-align: middle; }
</style>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-diagram-3"></i> V-Quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Grupos, equipos y definición de partidos
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <div class="alert alert-info small">
        <strong>Cómo armar la quiniela:</strong> (1) Cree cada grupo con 4 equipos.
        (2) Agregue partidos <em>enfrentamiento directo</em> (ej. A vs B).
        (3) Agregue partidos <em>entre ganadores</em> eligiendo dos partidos ya definidos (ej. ganador del partido 1 vs ganador del partido 2).
        (4) Use la sección <strong>Llave / final</strong> para cruces entre grupos (ej. ganador grupo 3 vs ganador grupo 4) hasta el campeón.
        Defina el <strong>orden</strong> de creación coherente: primero los duelos base, luego los que dependen de ellos.
    </div>

    <?php if ($nGrupos < 12) { ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Nuevo grupo (4 equipos)</div>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small">Nº grupo (1–12)</label>
                        <select class="form-select form-select-sm" name="orden_grupo" required>
                            <?php
                            $usados = array_column($gruposAdmin, 'orden_grupo');
                            $hay = false;
                            for ($i = 1; $i <= 12; $i++) {
                                if (in_array($i, $usados, true)) {
                                    continue;
                                }
                                $hay = true;
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                        <?php if (!$hay) { ?><small class="text-danger">No hay números libres.</small><?php } ?>
                    </div>
                    <div class="col-md-10">
                        <label class="form-label small">Nombre del grupo</label>
                        <input type="text" class="form-control form-control-sm" name="nombre_grupo" maxlength="120" required placeholder="Grupo A">
                    </div>
                    <?php for ($n = 1; $n <= 4; $n++) { ?>
                    <div class="col-md-6">
                        <label class="form-label small">Equipo <?php echo $n; ?> (país)</label>
                        <select class="form-select form-select-sm quiniela-ts-pais-nuevo" name="equipo_<?php echo $n; ?>" id="quiniela_equipo_new_<?php echo $n; ?>" autocomplete="off" required>
                            <option value="">Buscar o escribir país…</option>
                            <?php foreach (quiniela_paises_mundial_lista() as $p) { ?>
                            <option value="<?php echo htmlspecialchars($p['nombre']); ?>" data-iso="<?php echo htmlspecialchars($p['iso']); ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php } ?>
                </div>
                <button type="submit" name="crear_grupo_quiniela" value="1" class="btn btn-warning btn-sm mt-3">Crear grupo</button>
            </form>
        </div>
    </div>
    <?php } ?>

    <h6 class="section-title">Grupos (<?php echo (int) $nGrupos; ?>)</h6>
    <?php foreach ($gruposAdmin as $g) {
        $gid = (int) $g['id'];
        $plist = $partidosPorGrupo[$gid] ?? [];
        ?>
    <div class="bg-white rounded shadow-sm p-3 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
                <strong>Grupo <?php echo (int) $g['orden_grupo']; ?>:</strong> <?php echo htmlspecialchars($g['nombre_grupo']); ?>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" class="d-inline" onsubmit="return confirm('¿Eliminar grupo completo?');">
                <input type="hidden" name="grupo_id" value="<?php echo $gid; ?>">
                <button type="submit" name="eliminar_grupo_quiniela" value="1" class="btn btn-outline-danger btn-sm">Eliminar grupo</button>
            </form>
        </div>
        <ul class="small mb-3">
            <?php foreach ($g['equipos'] as $eq) { ?>
            <li><?php echo htmlspecialchars($eq['nombre']); ?> <span class="text-muted">(id <?php echo (int) $eq['id']; ?>)</span></li>
            <?php } ?>
        </ul>

        <h6 class="small text-secondary">Partidos de este grupo</h6>
        <?php if (count($plist) === 0) { ?>
        <p class="text-muted small">Sin partidos aún.</p>
        <?php } else { ?>
        <ul class="list-group list-group-flush mb-3 small">
            <?php foreach ($plist as $p) {
                $pid = (int) $p['id'];
                ?>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span><strong>#<?php echo $pid; ?></strong> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($p)); ?>
                    <?php if ($p['tipo'] === 'fijo') { ?><span class="badge bg-secondary">directo</span><?php } else { ?><span class="badge bg-primary">ganadores</span><?php } ?>
                </span>
                <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" class="ms-2" onsubmit="return confirm('¿Eliminar este partido?');">
                    <input type="hidden" name="partido_id" value="<?php echo $pid; ?>">
                    <button type="submit" name="eliminar_partido_quiniela" value="1" class="btn btn-link text-danger btn-sm p-0">Quitar</button>
                </form>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>

        <div class="row g-2">
            <div class="col-md-6 border rounded p-2">
                <div class="fw-bold small mb-2">+ Enfrentamiento directo</div>
                <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                    <input type="hidden" name="grupo_id_partido" value="<?php echo $gid; ?>">
                    <div class="mb-2 quiniela-ts-wrap form-select-sm">
                        <label class="form-label small">Equipo local</label>
                        <select class="form-select form-select-sm quiniela-ts-equipo-por-id" name="equipo_local_id" required>
                            <option value="">—</option>
                            <?php foreach ($g['equipos'] as $eq) {
                                $isoEq = quiniela_paises_iso_por_nombre($eq['nombre']);
                                ?>
                            <option value="<?php echo (int) $eq['id']; ?>" data-iso="<?php echo htmlspecialchars($isoEq ?? ''); ?>"><?php echo htmlspecialchars($eq['nombre']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-2 quiniela-ts-wrap form-select-sm">
                        <label class="form-label small">Equipo visitante</label>
                        <select class="form-select form-select-sm quiniela-ts-equipo-por-id" name="equipo_visitante_id" required>
                            <option value="">—</option>
                            <?php foreach ($g['equipos'] as $eq) {
                                $isoEq = quiniela_paises_iso_por_nombre($eq['nombre']);
                                ?>
                            <option value="<?php echo (int) $eq['id']; ?>" data-iso="<?php echo htmlspecialchars($isoEq ?? ''); ?>"><?php echo htmlspecialchars($eq['nombre']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Etiqueta (opcional)</label>
                        <input type="text" class="form-control form-control-sm" name="etiqueta_partido" maxlength="200" placeholder="Ej. Semifinal 1">
                    </div>
                    <button type="submit" name="agregar_partido_fijo" value="1" class="btn btn-outline-primary btn-sm">Agregar</button>
                </form>
            </div>
            <div class="col-md-6 border rounded p-2">
                <div class="fw-bold small mb-2">+ Entre ganadores de dos partidos</div>
                <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                    <input type="hidden" name="grupo_id_partido_g" value="<?php echo $gid; ?>">
                    <div class="mb-2">
                        <label class="form-label small">Partido 1 (origen)</label>
                        <select class="form-select form-select-sm quiniela-ts-partido-ref" name="src_partido_a" required>
                            <option value="">—</option>
                            <?php foreach ($plist as $op) { ?>
                            <option value="<?php echo (int) $op['id']; ?>">#<?php echo (int) $op['id']; ?> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($op)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Partido 2 (origen)</label>
                        <select class="form-select form-select-sm quiniela-ts-partido-ref" name="src_partido_b" required>
                            <option value="">—</option>
                            <?php foreach ($plist as $op) { ?>
                            <option value="<?php echo (int) $op['id']; ?>">#<?php echo (int) $op['id']; ?> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($op)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Etiqueta (opcional)</label>
                        <input type="text" class="form-control form-control-sm" name="etiqueta_partido_g" maxlength="200" placeholder="Ej. Final del grupo">
                    </div>
                    <button type="submit" name="agregar_partido_ganadores" value="1" class="btn btn-outline-success btn-sm">Agregar</button>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>

    <h6 class="section-title mt-4">Llave / final (entre grupos o camino al campeón)</h6>
    <p class="small text-muted">Los partidos aquí tienen <strong>grupo nulo</strong>: puede enfrentar equipos de distintos grupos (directo) o ganadores de partidos de cualquier grupo.</p>

    <?php if (count($partidosLlave) === 0) { ?>
    <p class="text-muted small">Aún no hay partidos de llave.</p>
    <?php } else { ?>
    <ul class="list-group mb-3 small">
        <?php foreach ($partidosLlave as $p) {
            $pid = (int) $p['id'];
            ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><strong>#<?php echo $pid; ?></strong> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($p)); ?></span>
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" onsubmit="return confirm('¿Eliminar?');">
                <input type="hidden" name="partido_id" value="<?php echo $pid; ?>">
                <button type="submit" name="eliminar_partido_quiniela" value="1" class="btn btn-link text-danger btn-sm p-0">Quitar</button>
            </form>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>

    <div class="row g-2">
        <div class="col-md-6 border rounded p-2 bg-light">
            <div class="fw-bold small mb-2">+ Llave: enfrentamiento directo (cualquier equipo)</div>
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                <input type="hidden" name="grupo_id_partido" value="">
                <div class="mb-2 quiniela-ts-wrap form-select-sm">
                    <label class="form-label small">Equipo A</label>
                    <select class="form-select form-select-sm quiniela-ts-equipo-por-id" name="equipo_local_id" required>
                        <option value="">—</option>
                        <?php foreach ($equiposSelector as $eq) {
                            $isoEq = quiniela_paises_iso_por_nombre($eq['nombre']);
                            ?>
                        <option value="<?php echo (int) $eq['id']; ?>" data-iso="<?php echo htmlspecialchars($isoEq ?? ''); ?>"><?php echo htmlspecialchars($eq['grupo_nom'] . ' — ' . $eq['nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-2 quiniela-ts-wrap form-select-sm">
                    <label class="form-label small">Equipo B</label>
                    <select class="form-select form-select-sm quiniela-ts-equipo-por-id" name="equipo_visitante_id" required>
                        <option value="">—</option>
                        <?php foreach ($equiposSelector as $eq) {
                            $isoEq = quiniela_paises_iso_por_nombre($eq['nombre']);
                            ?>
                        <option value="<?php echo (int) $eq['id']; ?>" data-iso="<?php echo htmlspecialchars($isoEq ?? ''); ?>"><?php echo htmlspecialchars($eq['grupo_nom'] . ' — ' . $eq['nombre']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" name="etiqueta_partido" maxlength="200" placeholder="Etiqueta opcional">
                </div>
                <button type="submit" name="agregar_partido_fijo" value="1" class="btn btn-primary btn-sm">Agregar a llave</button>
            </form>
        </div>
        <div class="col-md-6 border rounded p-2 bg-light">
            <div class="fw-bold small mb-2">+ Llave: entre ganadores (todos los partidos)</div>
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                <input type="hidden" name="grupo_id_partido_g" value="">
                <div class="mb-2">
                    <label class="form-label small">Partido origen A</label>
                    <select class="form-select form-select-sm quiniela-ts-partido-ref" name="src_partido_a" required>
                        <option value="">—</option>
                        <?php foreach ($partidosAdmin as $op) { ?>
                        <option value="<?php echo (int) $op['id']; ?>">#<?php echo (int) $op['id']; ?> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($op)); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Partido origen B</label>
                    <select class="form-select form-select-sm quiniela-ts-partido-ref" name="src_partido_b" required>
                        <option value="">—</option>
                        <?php foreach ($partidosAdmin as $op) { ?>
                        <option value="<?php echo (int) $op['id']; ?>">#<?php echo (int) $op['id']; ?> — <?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($op)); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" name="etiqueta_partido_g" maxlength="200" placeholder="Ej. G3 vs G4">
                </div>
                <button type="submit" name="agregar_partido_ganadores" value="1" class="btn btn-success btn-sm">Agregar a llave</button>
            </form>
        </div>
    </div>
</div>

<?php
$mapEquipoIso = [];
foreach ($equiposSelector as $eq) {
    $iso = quiniela_paises_iso_por_nombre($eq['nombre']);
    if ($iso !== null) {
        $mapEquipoIso[(string) (int) $eq['id']] = $iso;
    }
}
$mapNombreIsoJson = json_encode(quiniela_paises_mapa_nombre_a_iso(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$mapEquipoIsoJson = json_encode($mapEquipoIso, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof TomSelect === 'undefined') return;

    var ISO_PAIS = <?php echo $mapNombreIsoJson; ?>;
    var ISO_EQUIPO = <?php echo $mapEquipoIsoJson; ?>;

    function quinielaFlagHtml(iso) {
        iso = (iso || '').toString().toLowerCase().replace(/[^a-z]/g, '');
        if (!iso) {
            return '<span class="quiniela-flag-fallback me-2 align-middle d-inline-flex justify-content-center align-items-center rounded border bg-light" style="width:28px;height:21px;font-size:.7rem" title="">⚽</span>';
        }
        var u = 'https://flagcdn.com/w40/' + iso + '.png';
        var u2 = 'https://flagcdn.com/w80/' + iso + '.png';
        return '<img class="quiniela-flag-img me-2 align-middle rounded border bg-light" width="28" height="21" alt="" src="' + u + '" srcset="' + u2 + ' 2x" loading="lazy" referrerpolicy="no-referrer">';
    }

    function rowConBandera(data, escape, mapPais, mapEquipo) {
        var label = (data.text !== undefined && data.text !== null) ? String(data.text) : String(data.value || '');
        var iso = '';
        if (mapPais && data.value && mapPais[data.value]) {
            iso = mapPais[data.value];
        }
        if (!iso && mapEquipo && data.value !== undefined && mapEquipo[String(data.value)]) {
            iso = mapEquipo[String(data.value)];
        }
        return '<div class="d-flex align-items-center py-1">' + quinielaFlagHtml(iso) + '<span>' + escape(label) + '</span></div>';
    }

    document.querySelectorAll('.quiniela-ts-pais-nuevo').forEach(function (sel) {
        new TomSelect(sel, {
            allowEmptyOption: true,
            create: function (input) {
                var t = (input || '').trim();
                if (t.length < 2) return null;
                return { value: t, text: t };
            },
            createOnBlur: true,
            sortField: { field: 'text', direction: 'asc' },
            maxOptions: 500,
            plugins: ['clear_button'],
            placeholder: 'Buscar país…',
            render: {
                option: function (data, escape) {
                    return rowConBandera(data, escape, ISO_PAIS, null);
                },
                item: function (data, escape) {
                    return rowConBandera(data, escape, ISO_PAIS, null);
                }
            }
        });
    });

    document.querySelectorAll('.quiniela-ts-equipo-por-id').forEach(function (sel) {
        new TomSelect(sel, {
            allowEmptyOption: true,
            sortField: { field: 'text', direction: 'asc' },
            maxOptions: null,
            plugins: ['clear_button'],
            placeholder: 'Buscar equipo…',
            render: {
                option: function (data, escape) {
                    return rowConBandera(data, escape, null, ISO_EQUIPO);
                },
                item: function (data, escape) {
                    return rowConBandera(data, escape, null, ISO_EQUIPO);
                }
            }
        });
    });

    document.querySelectorAll('.quiniela-ts-partido-ref').forEach(function (sel) {
        new TomSelect(sel, {
            allowEmptyOption: true,
            sortField: { field: 'text', direction: 'asc' },
            maxOptions: null,
            plugins: ['clear_button'],
            placeholder: 'Buscar partido…'
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
