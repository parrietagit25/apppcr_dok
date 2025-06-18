<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- Estilos y scripts de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>

<div class="container mt-4">
    <div class="input-group mb-3"></div>

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
        <?php foreach ($mis_vacas as $key => $value): ?> 
            <p>Vacaciones Acumuladas: <b><?php echo $value['dias_vaca_acu_tiempo']; ?></b> Días</p>
        <?php endforeach; ?>

        <?php if ($tipo_usuario == 1 || $tipo_usuario == 4): ?>
            <div class="row mt-5">
                <h5 class="text-center">Vacaciones Acumuladas todos.</h5>
                <table id="tablaVacaciones" class="table table-striped table-bordered mt-3">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Días Acu.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_vacas)) {
                            foreach ($all_vacas as $value) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($value['codigo_empleado']) . "</td>
                                        <td>" . htmlspecialchars($value['nombre']) . "</td>
                                        <td>" . htmlspecialchars($value['apellido']) . "</td>
                                        <td>" . htmlspecialchars($value['dias_vaca_acu_tiempo']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay registros registrados.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Navegación inferior -->
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

<!-- Inicialización del DataTable -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaVacaciones').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
