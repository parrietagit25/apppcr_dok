<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';

$metaJs = [];
foreach ($partidosAdmin as $p) {
    if ($p['tipo'] === 'fijo') {
        $metaJs[] = [
            'id' => (int) $p['id'],
            'tipo' => 'fijo',
            'a' => (int) $p['equipo_local_id'],
            'b' => (int) $p['equipo_visitante_id'],
            'na' => $p['eq_loc_nom'] ?? '',
            'nb' => $p['eq_vis_nom'] ?? '',
        ];
    } else {
        $metaJs[] = [
            'id' => (int) $p['id'],
            'tipo' => 'ganadores',
            'sa' => (int) $p['src_partido_local_id'],
            'sb' => (int) $p['src_partido_der_id'],
        ];
    }
}
$metaJson = json_encode($metaJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>

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
    <p class="text-muted">Su quiniela ya está registrada. Solo consulta.</p>
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
                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                    <td><strong><?php echo htmlspecialchars($fila['predicho_nombre']); ?></strong></td>
                    <td><?php echo $fila['resultado_nombre'] ? htmlspecialchars($fila['resultado_nombre']) : '<span class="text-muted">Pendiente</span>'; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" id="formQuiniela">
        <p class="small text-muted">Complete <strong>todos</strong> los partidos. En los cruces “entre ganadores”, elija solo entre los equipos que ya eligió en los partidos anteriores.</p>
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
                        ?>
                    <tr>
                        <td><?php echo $pid; ?></td>
                        <td><?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($p)); ?></td>
                        <td>
                            <?php if ($p['tipo'] === 'fijo') {
                                $lid = (int) $p['equipo_local_id'];
                                $vid = (int) $p['equipo_visitante_id'];
                                ?>
                            <select class="form-select form-select-sm pred-fijo" name="pred_<?php echo $pid; ?>" id="pred_<?php echo $pid; ?>" required>
                                <option value="">— Elegir —</option>
                                <option value="<?php echo $lid; ?>"><?php echo htmlspecialchars($p['eq_loc_nom'] ?? ''); ?></option>
                                <option value="<?php echo $vid; ?>"><?php echo htmlspecialchars($p['eq_vis_nom'] ?? ''); ?></option>
                            </select>
                            <?php } else { ?>
                            <select class="form-select form-select-sm pred-ganadores" name="pred_<?php echo $pid; ?>" id="pred_<?php echo $pid; ?>"
                                data-pid="<?php echo $pid; ?>"
                                data-sa="<?php echo (int) $p['src_partido_local_id']; ?>"
                                data-sb="<?php echo (int) $p['src_partido_der_id']; ?>"
                                disabled>
                                <option value="">— Primero los partidos previos —</option>
                            </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button type="submit" name="confirmar_quiniela" value="1" class="btn btn-success" id="btnConfirmarQuiniela">
            <i class="bi bi-check2-circle"></i> Guardar mi quiniela
        </button>
    </form>

    <script type="application/json" id="quiniela-meta-partidos"><?php echo $metaJson; ?></script>
    <script>
    (function () {
        var raw = document.getElementById('quiniela-meta-partidos');
        if (!raw) return;
        var meta = [];
        try { meta = JSON.parse(raw.textContent || '[]'); } catch (e) { meta = []; }

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
                var va = val(m.sa), vb = val(m.sb);
                if (!va || !vb) {
                    sel.innerHTML = '<option value="">— Complete partidos #' + m.sa + ' y #' + m.sb + ' —</option>';
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
                var o1 = document.createElement('option'); o1.value = va; o1.textContent = labelFor(m.sa, va);
                var o2 = document.createElement('option'); o2.value = vb; o2.textContent = labelFor(m.sb, vb);
                sel.appendChild(o1); sel.appendChild(o2);
                sel.disabled = false;
                if (cur === va || cur === vb) sel.value = cur;
            });
        }
        var formQ = document.getElementById('formQuiniela');
        formQ.addEventListener('change', function (e) {
            if (e.target && e.target.id && e.target.id.indexOf('pred_') === 0) syncGanadores();
        });
        formQ.addEventListener('submit', function (e) {
            syncGanadores();
            var ok = true;
            formQ.querySelectorAll('select[name^="pred_"]').forEach(function (s) {
                if (s.disabled || !s.value) ok = false;
            });
            if (!ok) {
                e.preventDefault();
                alert('Complete todos los partidos, incluidos los cruces entre ganadores.');
                return;
            }
            if (!confirm('¿Confirmar quiniela? No podrá cambiarla después.')) e.preventDefault();
        });
        document.addEventListener('DOMContentLoaded', syncGanadores);
    })();
    </script>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
