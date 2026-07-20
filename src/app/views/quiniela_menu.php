<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}
include __DIR__ . '/header.php';
$qBase = rtrim(BASE_URL_CONTROLLER, '/') . '/QuinielaController.php';
?>

<div class="container mt-3 mb-5 pb-5">
    <div class="d-flex align-items-center mb-3">
        <div class="bg-success text-white px-3 py-2 rounded-start">
            <i class="bi bi-trophy-fill"></i> Quiniela del Mundial 2026
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Elige una opción
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <a href="<?php echo htmlspecialchars($qBase . '?arma_tu_quiniela=1'); ?>" class="text-decoration-none">
                <div class="card-icon h-100 text-center p-4">
                    <i class="bi bi-ui-checks-grid text-success" style="font-size:2rem;"></i>
                    <div class="card-title mt-2">Arma tu quiniela</div>
                    <small class="text-muted">Solo para quienes ya registraron su carta (nuevos registros cerrados)</small>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="<?php echo htmlspecialchars($qBase . '?resultados=1'); ?>" class="text-decoration-none">
                <div class="card-icon h-100 text-center p-4">
                    <i class="bi bi-list-ol text-primary" style="font-size:2rem;"></i>
                    <div class="card-title mt-2">Resultados</div>
                    <small class="text-muted">Clasificados oficiales y campeón</small>
                </div>
            </a>
        </div>

        <?php if (!empty($es_administrador_quiniela)) { ?>
        <div class="col-12 mt-2">
            <h6 class="section-title"><i class="bi bi-shield-lock"></i> Administración</h6>
        </div>
        <div class="col-md-4">
            <a href="<?php echo htmlspecialchars($qBase . '?v_quiniela=1'); ?>" class="text-decoration-none">
                <div class="card-icon h-100 text-center p-3 border border-warning border-2">
                    <i class="bi bi-diagram-3 text-warning" style="font-size:1.75rem;"></i>
                    <div class="card-title mt-2 small">V-Quiniela</div>
                    <small class="text-muted">Grupos y equipos</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?php echo htmlspecialchars($qBase . '?v_resultados=1'); ?>" class="text-decoration-none">
                <div class="card-icon h-100 text-center p-3 border border-warning border-2">
                    <i class="bi bi-pencil-square text-warning" style="font-size:1.75rem;"></i>
                    <div class="card-title mt-2 small">V-Resultados</div>
                    <small class="text-muted">Registrar clasificados oficiales por fase</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?php echo htmlspecialchars($qBase . '?colaboradores_quiniela=1'); ?>" class="text-decoration-none">
                <div class="card-icon h-100 text-center p-3 border border-warning border-2">
                    <i class="bi bi-people text-warning" style="font-size:1.75rem;"></i>
                    <div class="card-title mt-2 small">Colaboradores</div>
                    <small class="text-muted">Quinielas registradas</small>
                </div>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="mt-4">
        <a href="<?php echo htmlspecialchars(rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php'); ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
