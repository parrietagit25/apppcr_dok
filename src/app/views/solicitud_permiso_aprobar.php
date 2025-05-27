<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>

<div class="container mt-4">
    <div class="input-group mb-3">
        <!--<input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search"></i>
        </button> -->
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Solicitudes de Permiso</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Cartas de Trabajo</h5>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Tipo de Licencia</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $status = '';
                $permisos = $class->select_permisos_all();
                if (!empty($permisos)) {
                    foreach ($permisos as $row) {

                        if ($row['stat'] == 1) {
                            $status = 'Solicitado';
                        }elseif ($row['stat'] == 2) {
                            $status = 'Aprobado';
                        }else {
                            $status = 'Declinado';
                        }

                       echo "<tr>
                                <td>{$row['nombre']} {$row['apellido']}</td>
                                <td>{$row['tipo_licencia']}</td>
                                <td>{$row['fecha_log']}</td>
                                <td>
                                    <a href='#' data-bs-toggle='modal' data-bs-target='#modalAdjuntar{$row['id']}'>
                                        {$status}
                                    </a>
                                </td>
                            </tr>";

                        $ruta_completa = $row['archivo_adjunto'];
                        $prefijo_a_eliminar = 'var/www/html/';
                        $ruta_relativa = '';

                        if (is_string($ruta_completa) && strpos($ruta_completa, $prefijo_a_eliminar) === 0) {
                            $ruta_relativa = substr($ruta_completa, strlen($prefijo_a_eliminar));
                        } elseif (is_string($ruta_completa)) {
                            $ruta_relativa = $ruta_completa;
                        }

                        echo "
                        <div class='modal fade' id='modalAdjuntar{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='modalLabel{$row['id']}'>Ver detalle de la solicitud</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form method='POST' enctype='multipart/form-data'>
                                            <input type='hidden' name='permiso_id' value='{$row['id']}'>

                                            <div class='mb-3'>
                                                <label class='form-label'>Tipo de licencia</label>
                                                <select class='form-control' disabled>
                                                    <option>{$row['tipo_licencia']}</option>
                                                </select>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label'>Fecha Desde</label>
                                                <b>{$row['fecha_inicio']}</b><br>
                                                <label class='form-label'>Fecha Hasta</label>
                                                <b>{$row['fecha_fin']}</b>
                                            </div>";

                        if ($row['archivo_adjunto'] != '') {
                            echo "<div class='mb-3'>
                                    <label class='form-label'>Archivo</label>
                                    <a href='https://apppcr.net/app/uploads/permisos/{$row['archivo_adjunto']}' target='_blank'>Archivo</a>
                                </div>";
                        }

                                  echo "<div class='mb-3'>
                                            <label class='form-label'>Descripcion</label>
                                            <textarea class='form-control' name='comentario_jefe' rows='3' readonly>{$row['descripcion']}</textarea>
                                        </div>

                                        <div class='mb-3'>
                                            <label class='form-label'>Seleccione una opción</label>
                                            <select name='respuesta_jefe' class='form-control'>
                                                <option>Seleccionar</option>
                                                <option value='A'>Aprobar</option>
                                                <option value='D'>Declinar</option>
                                            </select>
                                        </div>

                                        <div class='mb-3'>
                                            <label class='form-label'>Comentario (opcional)</label>
                                            <textarea class='form-control' name='comentario_jefe' rows='3'></textarea>
                                        </div>

                                        <input type='hidden' name='tipo_licencia' value='{$row['tipo_licencia']}'>
                                        <input type='hidden' name='aprobar_permiso' value='1'>

                                        <div class='d-flex align-items-center gap-2'>
                                            <button type='button' 
                                                    class='btn btn-primary btn-guardar' 
                                                    id='btnGuardar{$row['id']}' 
                                                    data-id='{$row['id']}'>
                                                Guardar
                                            </button>
                                            <span id='loaderGuardar{$row['id']}' class='spinner-border spinner-border-sm text-primary d-none' role='status' aria-hidden='true'></span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-guardar").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const id = btn.getAttribute("data-id");
            const loader = document.getElementById("loaderGuardar" + id);
            const modal = document.getElementById("modalAdjuntar" + id);
            const form = modal.querySelector("form");

            btn.disabled = true;
            btn.textContent = "Enviando...";
            loader.classList.remove("d-none");

            setTimeout(() => {
                form.submit();
            }, 800);
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
