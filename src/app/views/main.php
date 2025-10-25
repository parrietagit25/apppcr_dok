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
    <!-- Slider (eliminado - no se usa) -->

    <div class="container">

        <div class="row text-center mb-3">
            <div class="col-6 mb-4">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>RRHHController.php" class="text-decoration-none">
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <img width="100" src="<?php echo BASE_URL_IMAGE; ?>mi_espacio.svg" alt="Carnet">
                        <div class="card-title"> <small>Mi Espacio</small> </div>
                    </div>
                </a>
            </div>
            <div class="col-6 mb-4">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>BeneficiosController.php" class="text-decoration-none">
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
                        <img width="100" src="<?php echo BASE_URL_IMAGE; ?>beneficios.svg" alt="Cumpleaños">
                        <div class="card-title"><small>Mis Beneficios</small></div>
                    </div>
                </a>
            </div>
        </div>
        
        
        <div class="row text-center mb-3">
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/CarnetController.php" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>carnet.svg" alt="Carnet">
                        <div class="card-title"> <small>Mi Carnet</small> </div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cumple=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>hb.svg" alt="Cumpleaños">
                        <div class="card-title"><small>Cumpleaños</small></div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="mailto:rrhh@grupopcr.com.pa" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_correo.svg" alt="Correo">
                        <div class="card-title"><small>Correo</small></div>
                    </div>
                </a>
            </div>
        </div>

        
        <div class="row text-center mb-4">
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?poliza=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>mi_poliza.svg" alt="Beneficios">
                        <div class="card-title"><small>Mi Poliza</small></div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="https://www.talentoen360.com/loginForm" class="text-decoration-none" target="_blank">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_evaluacion.svg" alt="Evaluación">
                        <div class="card-title"><small>Evaluación</small></div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="tel:+50763796524" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>ico_linea_apoyo.svg" alt="Línea de Apoyo">
                        <div class="card-title"><small>Línea de Apoyo</small></div>
                    </div>
                </a>
            </div>
            <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/CarnetController.php?verificar_carnet=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>verificar.png" alt="Carnet">
                        <div class="card-title"> <small>Verificar Carnet</small> </div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MetricasController.php?metricas=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>metricas.png" alt="Carnet">
                        <div class="card-title"> <small>Metricas</small> </div>
                    </div>
                </a>
            </div>
            <div class="col-4 mb-3">
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/MetricasController.php?organigrama=1" class="text-decoration-none">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>organigrama.png" alt="Carnet">
                        <div class="card-title"> <small>Organigrama</small> </div>
                    </div>
                </a>
            </div>
            <?php } ?>
            <div class="col-4 mb-3">
                <a href="https://micajadigital.css.gob.pa/Auth/SignIn" class="text-decoration-none" target="_blank">
                    <div class="card-icon">
                        <img src="<?php echo BASE_URL_IMAGE; ?>css.jpg" alt="Carnet">
                        <div class="card-title"> <small>Mi Caja Digital</small> </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
        <div class="bg-white rounded shadow p-3 text-center" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#frase_semana">
            <h6 class="fw-bold text-secondary">🔔 FRASE DE LA SEMANA</h6>
            <blockquote class="fst-italic text-muted mt-2">"<?php echo $frase['frase']; ?>"</blockquote>
        </div>
        <?php } else { ?>
        <div class="bg-white rounded shadow p-3 text-center">
            <h6 class="fw-bold text-secondary">🔔 FRASE DE LA SEMANA</h6>
            <blockquote class="fst-italic text-muted mt-2">"<?php echo $frase['frase']; ?>"</blockquote>
        </div>
        <?php } ?>
    </div>

    <br>
    <br>
    <br>

    <!-- Modal Actualizar Frase de la Semana (solo admin/RRHH) -->
    <?php if ($tipo_usuario == 1 || $tipo_usuario == 4) { ?>
    <div class="modal fade" id="frase_semana" tabindex="-1" aria-labelledby="fraseSemanaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="fraseSemanaLabel">
                        <i class="bi bi-pencil-square"></i> Actualizar Frase de la Semana
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="id_frase" value="<?php echo $frase['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Frase Actual:</label>
                            <p class="fst-italic text-muted">"<?php echo htmlspecialchars($frase['frase']); ?>"</p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="frase_semana" class="form-label fw-bold">Nueva Frase <span class="text-danger">*</span></label>
                            <textarea name="frase_semana" id="frase_semana" class="form-control" rows="4" required maxlength="500" placeholder="Escribe una frase motivacional para toda la empresa..."><?php echo htmlspecialchars($frase['frase']); ?></textarea>
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Nota:</strong> Esta frase será visible para todos los colaboradores en la página principal.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </button>
                            <button type="submit" name="boton_frase_semana" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Actualizar Frase
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="fixed-bottom d-flex justify-content-around fondo-botones-abajo py-2">
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
<?php /*
<footer class="bg-light text-center text-lg-start mt-5">
    <div class="container p-4">
        <p>&copy; 2024 GrupoPCR. Todos los derechos reservados.</p>
    </div>
</footer>
 */ ?>

<?php include __DIR__ . '/footer.php'; ?>
