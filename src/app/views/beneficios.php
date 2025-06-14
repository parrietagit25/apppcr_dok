<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';

?>


<div class="container mt-3">

    <!-- Sección: Mis Beneficios -->
    <div class="d-flex align-items-center bg-white rounded shadow-sm p-3 mb-3">
        <div class="me-3">
            <img src="<?php echo BASE_URL_IMAGE; ?>mis_beneficios_app.svg" alt="Mis Beneficios" width="100">
        </div>
        <div class="text-start">
            <p class="mb-0 small text-muted">
                Aprovecha los beneficios que te acompañarán en cada paso. Desde educación hasta bienestar, ¡esto es para ti!
            </p>
        </div>
    </div>

    <!-- Presentar Carnet -->
    <div class="bg-light text-center rounded py-3 mb-3" style="background-color:rgb(0, 54, 216); color: black;">
        <strong>PRESENTA TU CARNET PARA GOZAR DE ESTOS BENEFICIOS EXCLUSIVOS</strong><br>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?verificar_carnet=1" class="text-decoration-underline small text-dark">Si aún no tienes el tuyo, haz clic aquí</a>
    </div>

    <!-- Beneficios PCR -->
    <div class="bg-white rounded p-3 shadow-sm text-center mb-3">
        <h5 class="fw-bold text-primary mb-2">BENEFICIOS PCR</h5>
        <p class="small text-muted">Conoce los beneficios que brindan nuestras marcas aliadas</p>

        <div class="row row-cols-3 g-3 mt-2">
            <a href="https://apppcr.net/beneficios/hoteles.php"><div><img src="<?php echo BASE_URL_IMAGE; ?>hoteles_app.svg" width="40"><br><small>Hoteles</small></div></a>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>restaurantes_app.svg" width="40"><br><small>Restaurantes</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>salud_app.svg" width="40"><br><small>Salud</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>educacion_app.svg" width="40"><br><small>Educación</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>servicios_app.svg" width="40"><br><small>Servicios</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>otros_comercios.svg" width="40"><br><small>Otros</small></div>
        </div>
    </div>

</div>

<!-- Footer de navegación -->
<nav class="navbar fixed-bottom bg-primary text-white">
    <div class="container-fluid text-center">
        <div class="row w-100">
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-house-fill fs-4"></i><br><small>Inicio</small>
                </a>
            </div>
            <div class="col">
                <a href="#" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-gear-fill fs-4"></i><br><small>Ajustes</small>
                </a>
            </div>
            <div class="col">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-box-arrow-right fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>