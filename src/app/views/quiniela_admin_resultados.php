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
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-pencil-square"></i> V-Resultados
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Indicar ganador por partido
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-warning">No hay partidos. Configure primero los grupos en V-Quiniela.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Grupo</th>
                    <th>Local</th>
                    <th>Visitante</th>
                    <th>Ganador oficial</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidosAdmin as $p) {
                    $pid = (int) $p['id'];
                    $lid = (int) $p['local_id'];
                    $vid = (int) $p['visita_id'];
                    ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['grupo_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['local_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['visita_nombre']); ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="row g-1 align-items-center flex-nowrap">
                            <input type="hidden" name="partido_id" value="<?php echo $pid; ?>">
                            <div class="col-auto">
                                <select class="form-select form-select-sm" name="ganador_id" style="min-width:11rem;">
                                    <option value="0">— Pendiente —</option>
                                    <option value="<?php echo $lid; ?>" <?php echo ((int) ($p['ganador_id'] ?? 0) === $lid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['local_nombre']); ?></option>
                                    <option value="<?php echo $vid; ?>" <?php echo ((int) ($p['ganador_id'] ?? 0) === $vid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['visita_nombre']); ?></option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="guardar_resultado_partido" value="1" class="btn btn-primary btn-sm">Guardar</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
