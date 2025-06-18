<?php
//session_start();
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">

    <!-- Slider con frase -->
    <?php /* if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-toggle="modal" data-bs-target="#frase_semana">
    <?php }else{ ?>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
    <?php } */ ?>
    
    <div class="container">

        <div class="row text-center mb-3">
            <div class="col-6 mb-4">
                <a href="<?php echo BASE_URL_IMAGE; ?>imagen_carnet_mapfre/<?php  echo substr($_SESSION['code'], 2); ?>.pdf" class="text-decoration-none" target="_blank">
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <img width="100" src="<?php echo BASE_URL_IMAGE; ?>carnet.svg" alt="Carnet">
                        <div class="card-title"> <small>Mi Carnet de Mapfre</small> </div>
                    </div>
                </a>
            </div>
            <div class="col-6 mb-4">
                <a href="https://seguros.doctor-online.co/webm/views/login.php?resource=b60aa9f45cca11ebae930242ac130002" target="_blank" class="text-decoration-none">
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <img width="100" src="<?php echo BASE_URL_IMAGE; ?>dr_minutos.svg" alt="Cumpleaños">
                        <div class="card-title"><small>Doctor en Minutos</small></div>
                    </div>
                </a>
            </div>
            <div class="col-6 mb-4">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?info_poliza=1" target="_blank" class="text-decoration-none">
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <img width="100" src="<?php echo BASE_URL_IMAGE; ?>info.svg" alt="Cumpleaños">
                        <div class="card-title"><small>Info Mapfre</small></div>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <br>
    <br>
    <br>

    <!-- Footer navegación -->
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
</div>
<?php include __DIR__ . '/footer.php'; ?>
