<?php
// app/views/rrhh.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Mantenimiento </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mb-4">
        
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>user.png" alt="Noticias" class="mb-2" width="50">
                    <p>Usuarios</p>
                </div>
            </a>
        </div>

        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_permisos=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" alt="Noticias" class="mb-2" width="50">
                    <p>Permisos</p>
                </div>
            </a>
        </div>
        <?php } ?>
    <br>
    <br><br><br><br>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>