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
    <div class="input-group mb-3"></div>

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Incapacidad</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incapacidad">
            Subir incapacidad
        </button>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Incapacidad</h5>
        <table id="tablaIncapacidad" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Archivo</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $incapacidad = $class->incapacidad();
                if (!empty($incapacidad)) {
                    foreach ($incapacidad as $row) {
                        $link = (!empty($row['file_add']))
                            ? '<a href="' . BASE_URL_FILES_UPDATE_INCAPACIDAD . '/' . $row['file_add'] . '" target="_blank">Incapacidad</a>'
                            : '';
                        echo "<tr>
                                <td>$link</td>
                                <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td>" . htmlspecialchars($row['estado']) . "</td>
                            </tr>";
                    }
                } else {
                    //echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="incapacidad" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Subir Incapacidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Seleccione un archivo</label>
                        <input type="file" class="form-control" name="archivo_incapacidad" id="archivo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Comentario</label>
                        <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    </div>
                    <input type="hidden" name="incapacidad" value="1">
                    <div class="d-flex align-items-center gap-2">
                        <input type="button" class="btn btn-primary" id="btnIncapacidad" value="Subir Incapacidad">
                        <span id="loaderIncapacidad" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                    </div>
                </form>
            </div>
        </div>
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

<!-- JS: Formulario y DataTable -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Subir incapacidad
    const form = document.querySelector('#incapacidad form');
    const btn = document.getElementById("btnIncapacidad");
    const loader = document.getElementById("loaderIncapacidad");

    if (form && btn && loader) {
        btn.addEventListener("click", function () {
            btn.disabled = true;
            btn.value = "Enviando...";
            loader.classList.remove("d-none");

            setTimeout(() => {
                form.submit();
            }, 800);
        });
    }

    // Inicializar DataTable
    $('#tablaIncapacidad').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']] // Ordena por "Fecha de Solicitud" (índice 2) descendente
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
