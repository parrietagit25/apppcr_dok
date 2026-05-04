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
            Ganador oficial por partido
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-warning">No hay partidos. Configúrelos en V-Quiniela.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Partido</th>
                    <th>Ganador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidosAdmin as $p) {
                    $pid = (int) $p['id'];
                    $opts = $quinielaModel->candidatosOficialesGanador($pid);
                    ?>
                <tr>
                    <td><?php echo $pid; ?></td>
                    <td><?php echo htmlspecialchars($quinielaModel->etiquetaPartidoVista($p)); ?>
                        <?php if ($p['tipo'] === 'ganadores') { ?>
                        <div class="small text-muted">Entre ganadores: partidos #<?php echo (int) $p['src_partido_local_id']; ?> y #<?php echo (int) $p['src_partido_der_id']; ?></div>
                        <?php } ?>
                    </td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="d-flex flex-wrap gap-1 align-items-center">
                            <input type="hidden" name="partido_id" value="<?php echo $pid; ?>">
                            <select class="form-select form-select-sm" name="ganador_id" style="min-width:12rem;" <?php echo count($opts) !== 2 ? 'disabled' : ''; ?>>
                                <option value="0">— Pendiente —</option>
                                <?php
                                if (count($opts) === 2) {
                                    foreach ($opts as $eid) {
                                        $nom = $quinielaModel->nombreEquipo((int) $eid);
                                        $sel = ((int) ($p['ganador_id'] ?? 0) === (int) $eid) ? 'selected' : '';
                                        echo '<option value="' . (int) $eid . '" ' . $sel . '>' . htmlspecialchars($nom) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <?php if (count($opts) === 2) { ?>
                            <button type="submit" name="guardar_resultado_partido" value="1" class="btn btn-primary btn-sm">Guardar</button>
                            <?php } else { ?>
                            <span class="small text-muted">Defina antes el ganador de los partidos previos.</span>
                            <?php } ?>
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
