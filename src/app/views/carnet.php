<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>

<div class="container mt-4">
    <!--
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search"></i>
        </button>
    </div> -->

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Carnet Virtual</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="carnet">
        <h1>GRUPO <b>PCR</b></h1>
        <img src="<?php echo BASE_URL_IMAGE; ?>user.png" alt="Foto del empleado">
        <h3><?php echo $nombre . ' ' . $apellido; ?></h3>
        <p><b>Código:</b> <?php echo $codigo_empleado; ?></p>
        <p><b>Departamento:</b> <?php echo $departamento; ?></p>
        <p><b>Sangre:</b> <?php echo $sangre; ?></p>
        <div class="footer">
            <p>Grupo PCR</p>
            <p>Líderes Movilizando Panamá</p>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="solicitudModal" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Carta de Trabajo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <p>Ingrese la persona o entidad al cual irá dirigida la carta de trabajo</p>
                    <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Solicitar Carta" name="carta_trabajo">
                </form>
            </div>
        </div>
    </div>
</div>

<br>
<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
