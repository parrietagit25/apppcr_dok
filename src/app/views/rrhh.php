<?php
// app/views/rrhh.php
/*
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
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?carta_trabajo=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_carta_trabajo.png" alt="Noticias" class="mb-2" width="50">
                    <p>Carta de Trabajo</p>
                </div>
            </a>
        </div>
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
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?incapacidad=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_incapacidades.png" alt="Noticias" class="mb-2" width="50">
                    <p>Incapacidades</p>
                </div>
            </a>
        </div>
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
        <div class="col-4">
            <a href="https://www.konzerta.com/empleos-busqueda-panama-car-rental.html" target="_BLANK" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_vacantes.png" alt="Noticias" class="mb-2" width="50">
                    <p>Vacantes</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?solicitud_permiso=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" alt="Noticias" class="mb-2" width="50">
                    <p>Solicitud de Permiso</p>
                </div>
            </a>
        </div>
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
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" alt="Noticias" class="mb-2" width="50">
                    <p>Solicitud de Calamidades</p>
                </div>
            </a>
        </div>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
            <div class="col-4">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad_vrrhh=1" class="text-decoration-none">
                    <div class="p-2">
                        <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" alt="Noticias" class="mb-2" width="50">
                        <p>V-Solicitud de Calamidades</p>
                    </div>
                </a>
            </div>
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

<?php include __DIR__ . '/footer.php'; */ ?>

<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-3">

    <!-- Tarjeta introductoria -->
    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary text-white px-3 py-2 rounded-start">
            <i class="bi bi-person-circle"></i> Mi Espacio
        </div>
        <div class="bg-white text-muted px-3 py-2 rounded-end flex-grow-1">
            Accede fácilmente a tus gestiones personales y solicitudes con solo un clic.
        </div>
    </div>

    <!-- INFORMACIÓN PERSONAL -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary">INFORMACIÓN PERSONAL</h6>
        <div class="row text-center mt-3">
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>RRHHController.php?mis_datos=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>misdatos.png" width="45">
                    <div class="text-muted small">Mis Datos</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?carta_trabajo=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_carta_trabajo.png" width="45">
                    <div class="text-muted small">Solicitar Carta</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" width="45">
                    <div class="text-muted small">Solicitar Calamidad</div>
                </a>
            </div>
            <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?carta_trabajo_aprobar=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_carta_trabajo.png" width="45">
                    <div class="text-muted small">V-Carta Trabajo</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?calamidad_vrrhh=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>solicitudva.png" width="45">
                    <div class="text-muted small">V-Calamidades</div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- GESTIÓN DE TIEMPO Y AUSENCIAS -->
    <div class="bg-white p-3 rounded shadow-sm mb-4">
        <h6 class="fw-bold text-secondary">GESTIÓN DE TIEMPO Y AUSENCIAS</h6>
        <div class="row text-center mt-3">
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?mis_vacaciones=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_vacaciones.png" width="45">
                    <div class="text-muted small">Mis Vacaciones</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?solicitud_permiso=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" width="45">
                    <div class="text-muted small">Solicitar Permiso</div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?incapacidad=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_incapacidades.png" width="45">
                    <div class="text-muted small">Mis Incapacidades</div>
                </a>
            </div>
            <?php if ($tipo_usuario == 1 || $tipo_usuario == 3 || $tipo_usuario == 4) { ?>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?solicitud_permiso_admin=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>permiso1.png" width="45">
                    <div class="text-muted small">V-Permisos</div>
                </a>
            </div>
            <?php } ?>
            <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php?incapacidad_vrrhh=1" class="text-decoration-none">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_incapacidades.png" width="45">
                    <div class="text-muted small">V-Incapacidades</div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Vacantes -->
    <div class="bg-white p-3 rounded shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div>
            <p class="mb-1 text-muted small">Da el siguiente paso en tu carrera,<br>explora nuestras vacantes internas.</p>
        </div>
        <div>
            <a href="https://www.konzerta.com/empleos-busqueda-panama-car-rental.html" target="_blank">
                <img src="<?php echo BASE_URL_IMAGE; ?>ico_vacantes.png" width="65" alt="Vacantes PCR">
            </a>
        </div>
    </div>
    <br>
    <br>
    <br>

</div>

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

<?php include __DIR__ . '/footer.php'; ?>
