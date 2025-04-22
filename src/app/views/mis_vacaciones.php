<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>


<div class="container mt-4">
    <div class="input-group mb-3">
        
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Mis Vacaciones</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">

        <?php 
        
        foreach ($mis_vacas as $key => $value) { ?> 

            <p>Vacaciones Acumuladas: <b><?php echo $value['dias_vaca_acu_tiempo']; ?></b> Días</p>
            
            <?php
        }
        
        ?>

    </div>
</div>
<br>
<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php 

include __DIR__ . '/footer.php';