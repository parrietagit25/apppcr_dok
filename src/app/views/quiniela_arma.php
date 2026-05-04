<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
include __DIR__ . '/quiniela_include_banderas.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';

$mapaPred = $mapaPrediccionesUsuario ?? [];
$equipoMetaJson = json_encode($quinielaEquipoMetaPorId ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);

$metaJs = [];
foreach ($partidosAdmin as $p) {
    if ($p['tipo'] === 'fijo') {
        $metaJs[] = [
            'id' => (int) $p['id'],
            'tipo' => 'fijo',
            'a' => (int) $p['equipo_a_id'],
            'b' => (int) $p['equipo_b_id'],
            'na' => $p['eq_a_nom'] ?? '',
            'nb' => $p['eq_b_nom'] ?? '',
        ];
    } else {
        $metaJs[] = [
            'id' => (int) $p['id'],
            'tipo' => 'ganadores',
            'sa' => (int) $p['src_partido_a_id'],
            'sb' => (int) $p['src_partido_b_id'],
        ];
    }
}
$metaJson = json_encode($metaJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .quiniela-ts-wrap .ts-control { min-height: 31px; font-size: 0.875rem; }
    .ts-dropdown .option { padding: 0.35rem 0.5rem; }
</style>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-success text-white px-3 py-2 rounded-start">
            <i class="bi bi-ui-checks-grid"></i> Arma tu quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Elige ganador en cada partido (en orden)
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-warning">
        La quiniela aún no está lista. Espere a que administración cargue grupos y partidos.
    </div>
    <?php } elseif ($cartaCerrada) { ?>
    <p class="text-muted">Su quiniela ya está confirmada. Solo consulta.</p>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Partido</th>
                    <th>Su ganador</th>
                    <th>Resultado oficial</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prediccionesDetalle as $fila) { ?>
                <tr>
                    <td><?php echo $fila['descripcion_html'] ?? htmlspecialchars($fila['descripcion']); ?></td>
                    <td><strong><?php echo $fila['predicho_html'] ?? htmlspecialchars($fila['predicho_nombre']); ?></strong></td>
                    <td><?php echo isset($fila['resultado_html']) && $fila['resultado_html'] !== null
                        ? $fila['resultado_html']
                        : '<span class="text-muted">Pendiente</span>'; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" id="formQuiniela">
        <p class="small text-muted">
            Use <strong>Guardar progreso</strong> para ir avanzando; el servidor descartará predicciones que ya no sean coherentes si cambia un partido anterior.
            Cuando esté todo listo, pulse <strong>Confirmar quiniela</strong> (no podrá editar después).
        </p>
        <div class="table-responsive bg-white rounded shadow-sm mb-3">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Partido</th>
                        <th style="min-width:12rem;">Tu ganador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidosAdmin as $p) {
                        $pid = (int) $p['id'];
                        $predSel = isset($mapaPred[$pid]) ? (int) $mapaPred[$pid] : 0;
                        ?>
                    <tr>
                        <td><?php echo $pid; ?></td>
                        <td><?php echo $quinielaModel->etiquetaPartidoHtml($p); ?></td>
                        <td class="quiniela-ts-wrap">
                            <?php if ($p['tipo'] === 'fijo') {
                                $aid = (int) $p['equipo_a_id'];
                                $bid = (int) $p['equipo_b_id'];
                                $isoA = htmlspecialchars((string) ($p['eq_a_iso'] ?? ''), ENT_QUOTES, 'UTF-8');
                                $isoB = htmlspecialchars((string) ($p['eq_b_iso'] ?? ''), ENT_QUOTES, 'UTF-8');
                                ?>
                            <select class="form-select form-select-sm pred-fijo quiniela-ts-pred" name="pred_<?php echo $pid; ?>" id="pred_<?php echo $pid; ?>">
                                <option value="">— Elegir —</option>
                                <option value="<?php echo $aid; ?>" data-iso="<?php echo $isoA; ?>"<?php echo $predSel === $aid ? ' selected' : ''; ?>><?php echo htmlspecialchars($p['eq_a_nom'] ?? ''); ?></option>
                                <option value="<?php echo $bid; ?>" data-iso="<?php echo $isoB; ?>"<?php echo $predSel === $bid ? ' selected' : ''; ?>><?php echo htmlspecialchars($p['eq_b_nom'] ?? ''); ?></option>
                            </select>
                            <?php } else {
                                $sa = (int) $p['src_partido_a_id'];
                                $sb = (int) $p['src_partido_b_id'];
                                $ga = $mapaPred[$sa] ?? null;
                                $gb = $mapaPred[$sb] ?? null;
                                $optsOk = ($ga !== null && $gb !== null && (int) $ga !== (int) $gb);
                                $da = $optsOk ? $quinielaModel->datosEquipo((int) $ga) : null;
                                $db = $optsOk ? $quinielaModel->datosEquipo((int) $gb) : null;
                                $isoGa = $da['iso'] ?? '';
                                $isoGb = $db['iso'] ?? '';
                                ?>
                            <select class="form-select form-select-sm pred-ganadores quiniela-ts-pred" name="pred_<?php echo $pid; ?>" id="pred_<?php echo $pid; ?>"
                                data-pid="<?php echo $pid; ?>"
                                data-sa="<?php echo $sa; ?>"
                                data-sb="<?php echo $sb; ?>"
                                <?php echo $optsOk ? '' : 'disabled'; ?>>
                                <?php if (!$optsOk) { ?>
                                <option value="">Primero debes seleccionar los ganadores de los partidos anteriores</option>
                                <?php } else { ?>
                                <option value="">— Elegir —</option>
                                <option value="<?php echo (int) $ga; ?>" data-iso="<?php echo htmlspecialchars((string) $isoGa, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $predSel === (int) $ga ? ' selected' : ''; ?>><?php echo htmlspecialchars($quinielaModel->nombreEquipo((int) $ga)); ?></option>
                                <option value="<?php echo (int) $gb; ?>" data-iso="<?php echo htmlspecialchars((string) $isoGb, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $predSel === (int) $gb ? ' selected' : ''; ?>><?php echo htmlspecialchars($quinielaModel->nombreEquipo((int) $gb)); ?></option>
                                <?php } ?>
                            </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" name="guardar_progreso_quiniela" value="1" class="btn btn-outline-primary" id="btnGuardarProgreso">
                <i class="bi bi-save"></i> Guardar progreso
            </button>
            <button type="submit" name="confirmar_quiniela" value="1" class="btn btn-success" id="btnConfirmarQuiniela">
                <i class="bi bi-check2-circle"></i> Confirmar quiniela
            </button>
        </div>
    </form>

    <script type="application/json" id="quiniela-meta-partidos"><?php echo $metaJson; ?></script>
    <script type="application/json" id="quiniela-equipo-meta"><?php echo $equipoMetaJson; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    (function () {
        var rawMeta = document.getElementById('quiniela-meta-partidos');
        var rawEq = document.getElementById('quiniela-equipo-meta');
        var meta = [];
        var EQUIPO_META = {};
        try { meta = JSON.parse(rawMeta.textContent || '[]'); } catch (e) { meta = []; }
        try { EQUIPO_META = JSON.parse(rawEq.textContent || '{}'); } catch (e) { EQUIPO_META = {}; }

        function normIso(iso) {
            iso = (iso || '').toString().toLowerCase().replace(/[^a-z]/g, '');
            return (iso.length === 2) ? iso : 'un';
        }
        function flagRowHtml(iso, text, escape) {
            var i = normIso(iso);
            var u = 'https://flagcdn.com/w40/' + i + '.png';
            return '<div class="d-flex align-items-center">' +
                '<img class="flag-icon" src="' + u + '" width="20" height="15" alt="" loading="lazy" referrerpolicy="no-referrer">' +
                '<span>' + escape(text) + '</span></div>';
        }
        function isoForEquipoId(id) {
            var m = EQUIPO_META[String(id)];
            return (m && m.iso) ? m.iso : '';
        }
        function destroyTs(sel) {
            if (sel && sel.tomselect) {
                sel.tomselect.destroy();
                sel.removeAttribute('data-quiniela-ts');
            }
        }
        function initPredTom(sel) {
            if (!sel || sel.disabled || !sel.name || sel.name.indexOf('pred_') !== 0) return;
            destroyTs(sel);
            sel.setAttribute('data-quiniela-ts', '1');
            new TomSelect(sel, {
                allowEmptyOption: true,
                plugins: ['clear_button'],
                render: {
                    option: function (data, escape) {
                        var iso = data.iso || '';
                        if (!iso && data.value) {
                            iso = isoForEquipoId(data.value);
                        }
                        return flagRowHtml(iso, data.text, escape);
                    },
                    item: function (data, escape) {
                        var iso = data.iso || '';
                        if (!iso && data.value) {
                            iso = isoForEquipoId(data.value);
                        }
                        return flagRowHtml(iso, data.text, escape);
                    }
                }
            });
        }

        function val(pid) {
            var s = document.getElementById('pred_' + pid);
            return s ? s.value : '';
        }
        function labelFor(pid, teamId) {
            var s = document.getElementById('pred_' + pid);
            if (!s) return teamId;
            var o = s.querySelector('option[value="' + teamId + '"]');
            return o ? o.textContent : teamId;
        }
        function syncGanadores() {
            meta.filter(function (m) { return m.tipo === 'ganadores'; }).forEach(function (m) {
                var sel = document.getElementById('pred_' + m.id);
                if (!sel) return;
                destroyTs(sel);
                var va = val(m.sa), vb = val(m.sb);
                if (!va || !vb) {
                    sel.innerHTML = '<option value="">Primero debes seleccionar los ganadores de los partidos anteriores</option>';
                    sel.value = '';
                    sel.disabled = true;
                    return;
                }
                if (va === vb) {
                    sel.innerHTML = '<option value="">Error: mismo equipo en ambos lados</option>';
                    sel.disabled = true;
                    return;
                }
                var cur = sel.value;
                sel.innerHTML = '<option value="">— Elegir —</option>';
                var o1 = document.createElement('option');
                o1.value = va;
                o1.textContent = labelFor(m.sa, va);
                o1.setAttribute('data-iso', normIso(isoForEquipoId(va)));
                var o2 = document.createElement('option');
                o2.value = vb;
                o2.textContent = labelFor(m.sb, vb);
                o2.setAttribute('data-iso', normIso(isoForEquipoId(vb)));
                sel.appendChild(o1);
                sel.appendChild(o2);
                sel.disabled = false;
                if (cur === va || cur === vb) sel.value = cur;
                initPredTom(sel);
            });
        }
        var formQ = document.getElementById('formQuiniela');
        if (!formQ) return;

        document.querySelectorAll('select.pred-fijo').forEach(function (sel) {
            initPredTom(sel);
        });
        document.querySelectorAll('select.pred-ganadores:not([disabled])').forEach(function (sel) {
            initPredTom(sel);
        });

        formQ.addEventListener('change', function (e) {
            if (e.target && e.target.id && e.target.id.indexOf('pred_') === 0) syncGanadores();
        });
        formQ.addEventListener('submit', function (e) {
            var sub = e.submitter;
            var isConfirm = sub && (sub.name === 'confirmar_quiniela' || sub.id === 'btnConfirmarQuiniela');
            syncGanadores();
            if (isConfirm) {
                var ok = true;
                formQ.querySelectorAll('select[name^="pred_"]').forEach(function (s) {
                    if (s.disabled || !s.value) ok = false;
                });
                if (!ok) {
                    e.preventDefault();
                    alert('Complete todos los partidos, incluidos los cruces entre ganadores, antes de confirmar.');
                    return;
                }
                if (!confirm('¿Confirmar quiniela? No podrá cambiarla después.')) e.preventDefault();
            }
        });
        document.addEventListener('DOMContentLoaded', syncGanadores);
    })();
    </script>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
