<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
$mes_actual = obtenerMesEnEspanol(date('F'));
?>

<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Cumpleañeros de <?php echo $mes_actual; ?></h2>
    </div>

    <div class="row justify-content-center">
        <?php foreach ($cumple as $key => $value) { ?> 
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="date-box text-center p-3 me-3">
                        <h4 class="m-0 fw-bold text-white"><?php echo $value['dia_cumpleaños']; ?></h4>
                        <small class="text-white">Día</small>
                    </div>
                    <div class="birthday-info">
                        <h5 class="mb-0"><?php echo $value['nombre'] . ' ' . $value['apellido']; ?></h5>
                        <small class="text-muted">Feliz cumpleaños 🎉</small>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<br>
<br>
<br>
<!-- Menú de navegación fijo en la parte inferior -->
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

<style>
    body {
        background: linear-gradient(to right, #FFDEE9, #B5FFFC);
        font-family: Arial, sans-serif;
    }
    .card {
        border-radius: 10px;
        border: none;
        transition: transform 0.3s ease-in-out;
    }
    .card:hover {
        transform: scale(1.05);
    }
    .date-box {
        background: #FF7F50;
        color: white;
        font-weight: bold;
        border-radius: 10px;
        min-width: 60px;
    }
    .birthday-info h5 {
        font-weight: bold;
    }
</style>

<?php include __DIR__ . '/footer.php'; ?>
