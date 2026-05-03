<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
?>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-success text-white px-3 py-2 rounded-start">
            <i class="bi bi-ui-checks-grid"></i> Arma tu quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Mundial 2026
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-warning">
        La quiniela base aún no está configurada. Cuando RRHH cargue los grupos y equipos en <strong>V-Quiniela</strong>, podrá participar aquí.
    </div>
    <?php } elseif ($cartaCerrada) { ?>
    <p class="text-muted">Su quiniela ya está registrada. Solo puede consultarla.</p>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Grupo</th>
                    <th>Partido</th>
                    <th>Su ganador</th>
                    <th>Resultado oficial</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prediccionesDetalle as $fila) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila['grupo_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['local_nombre']); ?> vs <?php echo htmlspecialchars($fila['visita_nombre']); ?></td>
                    <td><strong><?php echo htmlspecialchars($fila['predicho_nombre']); ?></strong></td>
                    <td><?php echo $fila['resultado_nombre'] ? htmlspecialchars($fila['resultado_nombre']) : '<span class="text-muted">Pendiente</span>'; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <form method="post" action="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" id="formQuiniela">
        <p class="small text-muted">Debe elegir un ganador por cada partido. Al confirmar, <strong>no podrá modificar</strong> su quiniela.</p>
        <?php
        $porGrupo = [];
        foreach ($partidosAdmin as $p) {
            $g = $p['grupo_nombre'] . ' (Grupo ' . (int) $p['orden_grupo'] . ')';
            $porGrupo[$g][] = $p;
        }
        foreach ($porGrupo as $tituloGrupo => $lista) {
            echo '<h6 class="mt-3 text-secondary">' . htmlspecialchars($tituloGrupo) . '</h6>';
            echo '<div class="table-responsive bg-white rounded shadow-sm mb-3"><table class="table table-sm mb-0 align-middle">';
            echo '<thead class="table-light"><tr><th>Local</th><th>Visitante</th><th class="w-50">Tu ganador</th></tr></thead><tbody>';
            foreach ($lista as $p) {
                $pid = (int) $p['id'];
                $lid = (int) $p['local_id'];
                $vid = (int) $p['visita_id'];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($p['local_nombre']) . '</td>';
                echo '<td>' . htmlspecialchars($p['visita_nombre']) . '</td>';
                echo '<td><select class="form-select form-select-sm" name="pred_' . $pid . '" required>';
                echo '<option value="">— Elegir —</option>';
                echo '<option value="' . $lid . '">' . htmlspecialchars($p['local_nombre']) . '</option>';
                echo '<option value="' . $vid . '">' . htmlspecialchars($p['visita_nombre']) . '</option>';
                echo '</select></td></tr>';
            }
            echo '</tbody></table></div>';
        }
        ?>
        <button type="submit" name="confirmar_quiniela" value="1" class="btn btn-success" onclick="return confirm('¿Confirmar quiniela? No podrá cambiarla después.');">
            <i class="bi bi-check2-circle"></i> Guardar mi quiniela
        </button>
    </form>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
