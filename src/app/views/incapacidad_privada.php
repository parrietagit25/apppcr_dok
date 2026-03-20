<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>

<style>
    #overlayCargaIncapacidadPrivada {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-direction: column;
        gap: 10px;
    }
</style>

<div id="overlayCargaIncapacidadPrivada">
    <div class="spinner-border text-light" role="status" aria-hidden="true"></div>
    <div>Procesando archivo, por favor espere...</div>
</div>

<div class="container mt-4">
    <div class="input-group mb-3"></div>

    <div class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Incapacidad Privada</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incapacidadPrivadaModal">
            Subir incapacidad privada
        </button>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Mis incapacidades privadas</h5>
        <table id="tablaIncapacidadPrivada" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Archivo</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha Retroactiva</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($incapacidad)) {
                    foreach ($incapacidad as $row) {
                        $link = (!empty($row['file_add']))
                            ? '<a href="' . BASE_URL_FILES_UPDATE_INCAPACIDAD . '/' . $row['file_add'] . '" target="_blank">Incapacidad</a>'
                            : '';
                        $fecha_retro = !empty($row['fecha_retroactiva']) ? htmlspecialchars($row['fecha_retroactiva']) : '-';
                        echo "<tr>
                                <td>$link</td>
                                <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td>$fecha_retro</td>
                                <td>" . htmlspecialchars($row['estado']) . "</td>
                            </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="incapacidadPrivadaModal" tabindex="-1" aria-labelledby="incapacidadPrivadaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incapacidadPrivadaLabel">Subir Incapacidad Privada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data" id="formIncapacidadPrivada">
                    <div class="mb-3">
                        <label for="archivoPrivado" class="form-label">Seleccione un archivo</label>
                        <input type="file" class="form-control" name="archivo_incapacidad" id="archivoPrivado" required>
                    </div>
                    <div class="mb-3">
                        <label for="fechaRetroPrivada" class="form-label">Fecha Retroactiva</label>
                        <input type="date" class="form-control" name="fecha_retroactiva" id="fechaRetroPrivada">
                    </div>
                    <div class="mb-3">
                        <label for="descripcionPrivada" class="form-label">Comentario</label>
                        <textarea name="descripcion" id="descripcionPrivada" class="form-control" style="margin:10px;"></textarea>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="button" class="btn btn-primary" id="btnIncapacidadPrivada" value="Subir incapacidad">
                        <span id="loaderIncapacidadPrivada" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formIncapacidadPrivada");
    const btn = document.getElementById("btnIncapacidadPrivada");
    const loader = document.getElementById("loaderIncapacidadPrivada");
    const overlay = document.getElementById("overlayCargaIncapacidadPrivada");

    if (form && btn && loader && overlay) {
        btn.addEventListener("click", function () {
            btn.disabled = true;
            btn.value = "Procesando...";
            loader.classList.remove("d-none");
            overlay.style.display = "flex";
            setTimeout(function () {
                form.submit();
            }, 200);
        });
    }

    $('#tablaIncapacidadPrivada').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']]
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
