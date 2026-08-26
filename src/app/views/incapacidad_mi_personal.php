<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>

<div class="container mt-4">
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Incapacidades de mi personal</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <h5 class="text-center">Solicitudes de incapacidades de tu personal a cargo</h5>
        <table id="tablaIncapacidadMiPersonal" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nº Colaborador</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha Retroactiva</th>
                    <th>Estado</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($incapacidad)) {
                    foreach ($incapacidad as $row) {
                        $fecha_retro = !empty($row['fecha_retroactiva']) ? htmlspecialchars($row['fecha_retroactiva']) : '-';
                        $codigo_colab = htmlspecialchars($row['codigo_empleado'] ?? '-');
                        $link = (!empty($row['file_add']))
                            ? '<a href="' . BASE_URL_FILES_UPDATE_INCAPACIDAD . '/' . htmlspecialchars($row['file_add']) . '" target="_blank" class="btn btn-outline-primary btn-sm">Ver</a>'
                            : '-';
                        echo "<tr>
                                <td>$codigo_colab</td>
                                <td>" . htmlspecialchars($row['nombre']) . "</td>
                                <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td>$fecha_retro</td>
                                <td>" . htmlspecialchars($row['estado']) . "</td>
                                <td class='text-center'>$link</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No hay incapacidades registradas de tu personal a cargo.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<br><br><br>
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery && $('#tablaIncapacidadMiPersonal').length) {
        $('#tablaIncapacidadMiPersonal').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            pageLength: 10,
            order: [[3, 'desc']]
        });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
