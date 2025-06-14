<?php
echo "Inicio de la página beneficios.php ";
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}
echo " Usuario autenticado: " . htmlspecialchars($_SESSION['code']) . " ";
error_log("Beneficios cargado para usuario: " . $_SESSION['code']);

include __DIR__ . '/header.php';

?>

<div style="background: #0b3b80; color: white; padding: 20px; border-bottom: 5px solid #004aad;">
    <h5 class="m-0">GENTE <b>PCR</b> <span style="font-size: 0.8em;">MOBILE APP</span></h5>
</div>

<div class="container mt-3">

    <!-- Bienvenida -->
    <div class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded shadow-sm my-3">
        <div>
            <i class="bi bi-person-circle"></i> ¡Hola <?php echo $_SESSION['nombre'] ?? 'Colaborador'; ?>!
        </div>
        <small><?php echo date("d \d\e F \d\e Y"); ?></small>
    </div>

    <!-- Mis Beneficios -->
    <div class="bg-primary text-white rounded p-3 mb-3 text-start">
        <h6><i class="bi bi-gift"></i> Mis Beneficios</h6>
        <p class="mb-0 small">Aprovecha los beneficios que te acompañarán en cada paso. ¡Esto es para ti!</p>
    </div>

    <!-- Presentar Carnet -->
    <div class="bg-light text-center rounded py-3 mb-3">
        <strong>PRESENTA TU CARNET PARA GOZAR DE ESTOS BENEFICIOS EXCLUSIVOS</strong><br>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?verificar_carnet=1" class="text-decoration-underline small text-dark">Si aún no tienes el tuyo, haz clic aquí</a>
    </div>

    <!-- Beneficios PCR -->
    <div class="bg-white rounded p-3 shadow-sm text-center mb-3">
        <h5 class="fw-bold text-primary mb-2">BENEFICIOS PCR</h5>
        <p class="small text-muted">Conoce los beneficios que brindan nuestras marcas aliadas</p>

        <div class="row row-cols-3 g-3 mt-2">
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_hotel.svg" width="40"><br><small>Hoteles</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_rest.svg" width="40"><br><small>Restaurantes</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_salud.svg" width="40"><br><small>Salud</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_edu.svg" width="40"><br><small>Educación</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_serv.svg" width="40"><br><small>Servicios</small></div>
            <div><img src="<?php echo BASE_URL_IMAGE; ?>benef_otros.svg" width="40"><br><small>Otros</small></div>
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
                <a href="salir.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-box-arrow-right fs-4"></i><br><small>Salir</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>