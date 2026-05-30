<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="row text-center mb-3">
        <div class="col-4 mb-3">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?telemedicina=1" class="text-decoration-none">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>telemedicina.png" alt="Telemedicina">
                    <div class="card-title"><small>Telemedicina</small></div>
                </div>
            </a>
        </div>
        <div class="col-4 mb-3">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?instructivos_asegurado=1" class="text-decoration-none">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>instructivos_asegurado.svg" alt="Instructivos para el asegurado">
                    <div class="card-title"><small>Instructivos para el asegurado</small></div>
                </div>
            </a>
        </div>
        <div class="col-4 mb-3">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?portal_asegurados=1" class="text-decoration-none">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>portal_asegurados.svg" alt="Portal de asegurados">
                    <div class="card-title"><small>Portal de asegurados</small></div>
                </div>
            </a>
        </div>
    </div>
</div>

<br>
<br>
<br>

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
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
