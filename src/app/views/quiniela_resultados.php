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
        <div class="bg-primary text-white px-3 py-2 rounded-start">
            <i class="bi bi-list-ol"></i> Resultados
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Partidos oficiales
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($totalPartidos === 0) { ?>
    <div class="alert alert-info">Aún no hay partidos configurados.</div>
    <?php } else { ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-sm table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Grupo</th>
                    <th>Local</th>
                    <th>Visitante</th>
                    <th>Ganador</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidosAdmin as $p) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['grupo_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['local_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['visita_nombre']); ?></td>
                    <td>
                        <?php
                        if (!empty($p['ganador_id'])) {
                            if ((int) $p['ganador_id'] === (int) $p['local_id']) {
                                echo '<strong>' . htmlspecialchars($p['local_nombre']) . '</strong>';
                            } else {
                                echo '<strong>' . htmlspecialchars($p['visita_nombre']) . '</strong>';
                            }
                        } else {
                            echo '<span class="text-muted">Pendiente</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
