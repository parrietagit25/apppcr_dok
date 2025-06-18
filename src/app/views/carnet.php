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

        <img src="<?php echo BASE_URL_IMAGE; ?>imagen_carnet/<?php echo $resultado = substr($_SESSION['code'], 2); ?>.jpeg" alt="Foto del empleado">

        <h3><?php echo $nombre . ' ' . $apellido; ?></h3>
        <p><b>Código:</b> <?php echo $codigo_empleado; ?></p>
        <p><b>Departamento:</b> <?php echo $departamento; ?></p>
        <p><b>Sangre:</b> <?php echo $sangre; ?></p>

        <!-- Código QR generado -->
        <div class="mt-3 text-center">
            <p><b>QR del Empleado</b></p>
            <img class="qr" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($codigo_empleado); ?>" alt="QR del empleado">
        </div>

        <div class="footer mt-3">
            <p>Grupo PCR</p>
            <p>Líderes Movilizando Panamá</p>
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
                <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
