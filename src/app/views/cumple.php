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
<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
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
