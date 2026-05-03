<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
$nGrupos = count($gruposAdmin);
?>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
        <div class="bg-warning text-dark px-3 py-2 rounded-start">
            <i class="bi bi-diagram-3"></i> V-Quiniela
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Administración — grupos y equipos (máx. 12 grupos)
        </div>
        <a href="<?php echo htmlspecialchars($qBase); ?>" class="btn btn-outline-secondary btn-sm">Menú quiniela</a>
    </div>

    <?php if ($mensaje !== '') { ?>
    <div class="alert alert-<?php echo htmlspecialchars($mensajeTipo); ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php } ?>

    <?php if ($nGrupos < 12) { ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Agregar grupo</div>
        <div class="card-body">
            <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small">Nº grupo (1–12)</label>
                        <select class="form-select form-select-sm" name="orden_grupo" required>
                            <?php
                            $usados = array_column($gruposAdmin, 'orden_grupo');
                            for ($i = 1; $i <= 12; $i++) {
                                if (in_array($i, $usados, true)) {
                                    continue;
                                }
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-10">
                        <label class="form-label small">Nombre del grupo (ej. Grupo A)</label>
                        <input type="text" class="form-control form-control-sm" name="nombre_grupo" maxlength="120" required placeholder="Grupo A">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">País / equipo 1</label>
                        <input type="text" class="form-control form-control-sm" name="equipo_1" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">País / equipo 2</label>
                        <input type="text" class="form-control form-control-sm" name="equipo_2" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">País / equipo 3</label>
                        <input type="text" class="form-control form-control-sm" name="equipo_3" maxlength="120" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">País / equipo 4</label>
                        <input type="text" class="form-control form-control-sm" name="equipo_4" maxlength="120" required>
                    </div>
                </div>
                <button type="submit" name="crear_grupo_quiniela" value="1" class="btn btn-warning btn-sm mt-3">
                    <i class="bi bi-plus-lg"></i> Crear grupo y partidos
                </button>
                <p class="small text-muted mt-2 mb-0">Al guardar se generan automáticamente los 6 partidos de fase de grupos (todos contra todos).</p>
            </form>
        </div>
    </div>
    <?php } else { ?>
    <div class="alert alert-success">Ya tiene los 12 grupos configurados.</div>
    <?php } ?>

    <h6 class="section-title">Grupos registrados (<?php echo (int) $nGrupos; ?>)</h6>
    <?php if ($nGrupos === 0) { ?>
    <p class="text-muted">Ningún grupo aún.</p>
    <?php } else { ?>
        <?php foreach ($gruposAdmin as $g) { ?>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <strong>Grupo <?php echo (int) $g['orden_grupo']; ?>:</strong>
                    <?php echo htmlspecialchars($g['nombre_grupo']); ?>
                </div>
                <form method="post" action="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este grupo? Solo si nadie ha enviado quiniela aún.');">
                    <input type="hidden" name="grupo_id" value="<?php echo (int) $g['id']; ?>">
                    <button type="submit" name="eliminar_grupo_quiniela" value="1" class="btn btn-outline-danger btn-sm">Eliminar</button>
                </form>
            </div>
            <ul class="mb-0 mt-2 small">
                <?php foreach ($g['equipos'] as $eq) { ?>
                <li><?php echo htmlspecialchars($eq['nombre']); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
