<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

include __DIR__ . '/header.php';

$codigo_sesion = trim($_SESSION['code'] ?? '');
$carnet_dir = __DIR__ . '/../../public/images/codigo_carnet_plan/';
$candidatos_codigo = array_values(array_unique(array_filter([
    $codigo_sesion,
    strlen($codigo_sesion) > 6 ? substr($codigo_sesion, 2) : '',
])));

$carnet_codigo = '';
foreach ($candidatos_codigo as $codigo) {
    if (is_file($carnet_dir . $codigo . '.pdf')) {
        $carnet_codigo = $codigo;
        break;
    }
}

$carnet_url = $carnet_codigo !== ''
    ? BASE_URL_IMAGE . 'codigo_carnet_plan/' . rawurlencode($carnet_codigo . '.pdf')
    : '';
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h4 class="fw-bold text-primary mb-4 text-center">Mi carnet</h4>

            <?php if ($carnet_url !== ''): ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                        <a href="<?php echo htmlspecialchars($carnet_url, ENT_QUOTES, 'UTF-8'); ?>"
                           target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir carnet
                        </a>
                        <a href="<?php echo htmlspecialchars($carnet_url, ENT_QUOTES, 'UTF-8'); ?>"
                           download class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download"></i> Descargar PDF
                        </a>
                    </div>
                    <iframe src="<?php echo htmlspecialchars($carnet_url, ENT_QUOTES, 'UTF-8'); ?>"
                            title="Mi carnet"
                            style="width: 100%; height: 70vh; border: 0; border-radius: 0.375rem; background: #f8f9fa;"
                            loading="lazy"></iframe>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <img src="<?php echo BASE_URL_IMAGE; ?>carnet.svg" alt="Mi carnet" width="100" class="mb-4 opacity-75">
                <p class="fs-5 text-muted mb-2">No se encontró tu carnet disponible.</p>
                <p class="text-muted small mb-0">Código consultado: <?php echo htmlspecialchars($codigo_sesion, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<br><br><br>

<nav class="navbar fixed-bottom navbar-light bg-primary">
    <div class="container-fluid text-center text-white">
        <div class="row w-100">
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-house-door-fill fs-4"></i><br><small>Inicio</small>
                </a>
            </div>
            <div class="col">
                <a href="#" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-gear-fill fs-4"></i><br><small>Ajustes</small>
                </a>
            </div>
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?n_poliza=1" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
