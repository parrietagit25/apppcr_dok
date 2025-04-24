<?php
// app/views/rrhh.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">

    <!--<div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search"></i>
        </button>
    </div>-->

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Colaborador </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mb-4">
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>RRHHController.php?mis_datos=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>misdatos.png" alt="RRHH" class="mb-2" width="50">
                    <p>Mis datos</p>
                </div>
            </a>
        </div>
        <?php ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?mis_vacaciones=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_vacaciones.png" alt="Beneficios" class="mb-2" width="50">
                    <p>Mis Vacaciones</p>
                </div>
            </a>
        </div>
        <?php if($code_lomg >7){}else{ ?> 
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?carta_trabajo=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_carta_trabajo.png" alt="Noticias" class="mb-2" width="50">
                    <p>Carta de Trabajo</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?carta_trabajo_aprobar=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_carta_trabajo.png" alt="Noticias" class="mb-2" width="50">
                    <p>V-RRHH Carta de Trabajo</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if($code_lomg >7){}else{ ?> 
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?incapacidad=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_incapacidades.png" alt="Noticias" class="mb-2" width="50">
                    <p>Incapacidades</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?incapacidad_vrrhh=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_incapacidades.png" alt="Noticias" class="mb-2" width="50">
                    <p>V-RRHH Incapacidades</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <!--<div class="col-4">
            <a href="#" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL; ?>carta.png" alt="Noticias" class="mb-2" width="50">
                    <p>RRHH consulta</p>
                </div>
            </a>
        </div>-->
        <div class="col-4">
            <a href="https://www.konzerta.com/empleos-busqueda-panama-car-rental.html" target="_BLANK" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_vacantes.png" alt="Noticias" class="mb-2" width="50">
                    <p>Vacantes</p>
                </div>
            </a>
        </div>
        <?php if($code_lomg >7){}else{ ?> 
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?solicitud_permiso=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" alt="Noticias" class="mb-2" width="50">
                    <p>Solicitud de Permiso</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 3 || $tipo_usuario == 4) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?solicitud_permiso_admin=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" alt="Noticias" class="mb-2" width="50">
                    <p>V-Solicitud de Permiso</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if($code_lomg >7){}else{ ?> 
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" alt="Noticias" class="mb-2" width="50">
                    <p>Solicitud de Calamidades</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
            <?php if($code_lomg >7){}else{ ?> 
            <div class="col-4">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad_vrrhh=1" class="text-decoration-none">
                    <div class="p-2">
                        <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" alt="Noticias" class="mb-2" width="50">
                        <p>V-Solicitud de Calamidades</p>
                    </div>
                </a>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
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