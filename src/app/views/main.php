<?php
//session_start();
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">

    <!--<div class="text-center mb-4">
        <img src="<?php echo BASE_URL; ?>user.png" alt="User Avatar" class="rounded-circle" width="80">
        <h4>Bienvenido</h4>
        <p><b><?php echo isset($nombre) ? htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : 'Usuario'; ?></b></p>
    </div> 

    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search"></i>
        </button>
    </div>
    -->
    <!-- Slider con frase -->
    <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-toggle="modal" data-bs-target="#frase_semana">
    <?php }else{ ?>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
    <?php } ?>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Frase de la semana</h5>
                    <p class="mb-0">"<?php echo $frase['frase']; ?>"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    Modal frase de la semana
    -->
    <?php /*
    <div class="modal fade" id="frase_semana" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="solicitudModalLabel">Actualizar Frase de la semana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class='mb-3'>
                            <p>Frase de la semana</p>
                            <textarea name="frase_semana" class="form-control" style="margin:10px;"></textarea>
                        </div>
                        <br>
                        <input type="submit" class="btn btn-primary" value="Actualizar Frase" name="boton_frase_semana">
                        <input type="hidden" name="id_frase" value="<?php echo $frase['id']; ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
       */ ?>
    <!-- Iconos de funcionalidades -->
    <?php /*<div class="row text-center mb-4">
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>RRHHController.php" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_rrhh.png" alt="RRHH" class="mb-2" width="50">
                    <p>RRHH</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="<?php echo BENEFICIOS; ?>" target="_blank" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>beneficios.png" alt="Beneficios" class="mb-2" width="50">
                    <p>Beneficios</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/CarnetController.php" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>carnet.png" alt="Servicios" class="mb-2" width="50">
                    <p>Carnet</p>
                </div>
            </a>
        </div>
        <!-- <div class="col-4">
            <a href="#" class="text-decoration-none">
                <div class="p-2">
                    <img src="images/notocias.png" alt="Noticias" class="mb-2" width="50">
                    <p>Noticias</p>
                </div>
            </a>
        </div> -->
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cumple=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>hb.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Cumpleaños del mes</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="mailto:rrhh@grupopcr.com.pa?subject=Solicitud%20de%20información" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_correo.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Correo Electronico</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="tel:+50763796524" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_linea_apoyo.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Linea de apoyo</p>
                </div>
            </a>
        </div>
        <!--
        <div class="col-4">
            <a href="cumple.php" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL; ?>ico_operativa.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Operativa de la empresa</p>
                </div>
            </a>
        </div>
        <div class="col-4">
            <a href="cumple.php" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL; ?>ico_crecimnieto.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Crecimiento interno</p>
                </div>
            </a>
        </div>
        -->
        <div class="col-4">
            <a href="https://www.talentoen360.com/loginForm" class="text-decoration-none" target="_blank">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_evaluacion.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>Evaluacion de desempeño</p>
                </div>
            </a>
        </div>
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/EvaluacionController.php?eval=admin" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ico_evaluacion.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>V-Evaluacion de desempeño</p>
                </div>
            </a>
        </div>
        <?php } ?>
        <?php if ($tipo_usuario == 1) { ?>
        <div class="col-4">
            <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?ia=1" class="text-decoration-none">
                <div class="p-2">
                    <img src="<?php echo BASE_URL_IMAGE; ?>ia.png" alt="Cumpleaños" class="mb-2" width="50">
                    <p>AI PCR-RRHH</p>
                </div>
            </a>
        </div>
        <?php } ?>

    </div> */ ?>

    <div class="container">
        <div class="section-title">Mi Espacio</div>
        <div class="row text-center mb-3">
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/CarnetController.php" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>carnet.png" alt="Carnet">
                        <div class="card-title">Mi Carnet</div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cumple=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>hb.png" alt="Cumpleaños">
                        <div class="card-title">Cumpleaños</div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="mailto:rrhh@grupopcr.com.pa" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_correo.png" alt="Correo">
                        <div class="card-title">Correo</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="section-title">Mis Beneficios</div>
        <div class="row text-center mb-4">
            <div class="col-4 mb-3">
                <a href="<?php echo BENEFICIOS; ?>" class="text-decoration-none" target="_blank">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>beneficios.png" alt="Beneficios">
                        <div class="card-title">Mis Beneficios</div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="https://www.talentoen360.com/loginForm" class="text-decoration-none" target="_blank">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_evaluacion.png" alt="Evaluación">
                        <div class="card-title">Evaluación</div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="tel:+50763796524" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_linea_apoyo.png" alt="Línea de Apoyo">
                        <div class="card-title">Línea de Apoyo</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="bg-white rounded shadow p-3 text-center">
            <h6 class="fw-bold text-secondary">🔔 FRASE DE LA SEMANA</h6>
            <blockquote class="fst-italic text-muted mt-2">"<?php echo $frase['frase']; ?>"</blockquote>
        </div>
    </div>

    <div class="fixed-bottom d-flex justify-content-around bg-primary py-2">
        <a href="#" class="text-white text-center">
            <i class="bi bi-house-door-fill fs-4"></i><br><small>Inicio</small>
        </a>
        <a href="#" class="text-white text-center">
            <i class="bi bi-gear-fill fs-4"></i><br><small>Ajustes</small>
        </a>
        <a href="<?php echo BASE_URL_LINK; ?>/salir.php" class="text-white text-center">
            <i class="bi bi-box-arrow-right fs-4"></i><br><small>Salir</small>
        </a>
    </div>



    <!-- Top beneficios 
    <div class="bg-light p-3 rounded">
        <h5 class="fw-bold">Top Beneficios</h5>
        <div class="d-flex">
            <img src="<?php echo BASE_URL; ?>playablanca.jpg" alt="Playa Blanca Resort" class="me-3" width="100">
            <div>
                <p class="mb-0">Playa Blanca Resort</p>
                <small class="text-muted">Descuento del 7% de la tarifa</small>
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning text-dark me-2">4.9</span>
                    <small>(37 Reviews)</small>
                </div>
            </div>
        </div>
    </div>-->
</div>

<footer class="bg-light text-center text-lg-start mt-5">
    <div class="container p-4">
        <p>&copy; 2024 GrupoPCR. Todos los derechos reservados.</p>
    </div>
</footer>

<?php include __DIR__ . '/footer.php'; ?>
