<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

include __DIR__ . '/header.php';

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
];
$mesActual = $meses[(int) date('n')] ?? date('F');
$tablaLista = $cumple_mantenimiento ?? [];
$tablaDisponible = $cumple_config_disponible ?? false;
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1">Mant Cumple</h5>
            <p class="text-muted small mb-0">
                Cumpleañeros de <?php echo htmlspecialchars($mesActual); ?> (desde hoy).
                Lo que ocultes aquí deja de verse en la pantalla pública de cumpleaños.
            </p>
        </div>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cumple=1" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
            Ver pantalla pública
        </a>
    </div>

    <?php if (!$tablaDisponible) { ?>
        <div class="alert alert-warning">
            Falta crear la tabla <code>cumple_config</code>. Ejecute el script
            <code>src/sql/cumple_config.sql</code> en la base de datos.
        </div>
    <?php } ?>

    <?php if (!empty($mensaje_cumple)) { ?>
        <div class="alert alert-<?php echo htmlspecialchars($mensaje_cumple_tipo ?? 'info'); ?>">
            <?php echo htmlspecialchars($mensaje_cumple); ?>
        </div>
    <?php } ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Día</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Estado lista</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tablaLista)) { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay cumpleañeros pendientes este mes.</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($tablaLista as $row) {
                                $visible = (int) ($row['visible'] ?? 0) === 1;
                                $forzado = (int) ($row['forzado'] ?? 0) === 1;
                                ?>
                                <tr class="<?php echo $visible ? '' : 'table-secondary'; ?>">
                                    <td><?php echo (int) $row['dia_cumpleaños']; ?></td>
                                    <td><?php echo htmlspecialchars($row['codigo_empleado']); ?></td>
                                    <td><?php echo htmlspecialchars(trim($row['nombre'] . ' ' . $row['apellido'])); ?></td>
                                    <td>
                                        <?php echo $row['tipo'] === 'externo' ? 'Externo' : 'Planilla'; ?>
                                        <?php if ($forzado) { ?>
                                            <span class="badge bg-info text-dark">Forzado</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($visible) { ?>
                                            <span class="badge bg-success">Visible</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php } ?>
                                        <?php if (!empty($row['motivo'])) { ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($row['motivo']); ?></small>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <form method="post" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_cumple=1" class="d-inline">
                                            <input type="hidden" name="codigo_empleado" value="<?php echo htmlspecialchars($row['codigo_empleado']); ?>">
                                            <?php if ($visible) { ?>
                                                <input type="hidden" name="accion_cumple" value="ocultar">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('¿Quitar a este colaborador de la lista pública de cumpleaños?');">
                                                    Quitar de lista
                                                </button>
                                            <?php } else { ?>
                                                <input type="hidden" name="accion_cumple" value="mostrar">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Mostrar en lista
                                                </button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-muted small mt-3 mb-0">
        Tip: al quitar a alguien solo se oculta en la app; no modifica su fecha de nacimiento en planilla.
    </p>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimineto=1" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
