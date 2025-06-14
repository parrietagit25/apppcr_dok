<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>

<div class="container mt-4">

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

        <div class="footer mt-3">
            <p>Grupo PCR</p>
            <p>Líderes Movilizando Panamá</p>
        </div>
    </div>

</div>

<br>
<br>

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
