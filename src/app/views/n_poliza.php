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
            <a href="https://attest.palig.com/as/authorization.oauth2?response_type=code&amp;client_id=9effd84c2b0a471b9f1d2a4dfd4114c8&amp;redirect_uri=https%3A%2F%2Fpalig.my.site.com%2Fmembersportal%2Fservices%2Fauthcallback%2FMemberPortal&amp;scope=openid&amp;state=CAAAAZ51fKOnMDAwMDAwMDAwMDAwMDAwAAABBBfRJX3fWRF6JQJ892zWcA8Sj8rlGxGIf3b4k8Xo00OMfIy3a9d4XJ7RW-zZV8YiIQ9kGBAMCW1cYZfyMt3xWXz6jiWcGfThDhQtX8d5ymt8DkSjQnTTb-84rBuDToNX8gkPH6vliYJT3A8wfXBVvD7ftCGo9ai2NnRaW84s3GMFUNC1n_oEm5xzdpHSYljqGqfbX8GrbkEb5yMLeK0Z9Gsn9LA2BcllbmIPuhC1xW4Z78f80qBnibKjgiFo5irXevIuUo6yuYPjdP63C7p0BvE_PbMFcXtb3iNP7pcp1s5-" class="text-decoration-none" target="_blank" rel="noopener noreferrer">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>portal_asegurados.svg" alt="Portal de asegurados">
                    <div class="card-title"><small>Portal de asegurados</small></div>
                </div>
            </a>
        </div>
        <div class="col-4 mb-3">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?info_palig=1" class="text-decoration-none">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>palig.jpg" alt="Info Palig" style="max-height: 80px; width: auto; object-fit: contain;">
                    <div class="card-title"><small>Info Palig</small></div>
                </div>
            </a>
        </div>
        <div class="col-4 mb-3">
            <a href="https://palig.com/es/pa/busca-un-proveedor-medico" class="text-decoration-none" target="_blank" rel="noopener noreferrer">
                <div class="card-icon">
                    <img src="<?php echo BASE_URL_IMAGE; ?>red_proveedores_medicos.svg" alt="Red de proveedores médicos">
                    <div class="card-title"><small>Red de proveedores médicos</small></div>
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
