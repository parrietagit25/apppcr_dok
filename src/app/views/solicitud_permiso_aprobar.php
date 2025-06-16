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
                    <h5 class="fw-bold">Solicitudes de Permiso</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Cartas de Trabajo</h5>
        <table id="tablaPermisosAprobar" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
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
                        $status = $row['stat'] == 1 ? 'Solicitado' : ($row['stat'] == 2 ? 'Aprobado' : 'Declinado');

                        $cantidad_dias = 0;
                        if($row['tipo_licencia'] == 'Vacaciones'){
                            $inicio = new DateTime($row['fecha_inicio']);
                            $fin = new DateTime($row['fecha_fin']);
                            $diferencia = $inicio->diff($fin);
                            $cantidad_dias = $diferencia->days;
                        }

                        echo "<tr>
                                <td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>
                                <td>" . htmlspecialchars($row['tipo_licencia']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td>
                                    <a href='#' data-bs-toggle='modal' data-bs-target='#modalAdjuntar{$row['id']}'>
                                        " . htmlspecialchars($status) . "
                                    </a>
                                </td>
                            </tr>";

                        $archivo = !empty($row['archivo_adjunto'])
                            ? "<div class='mb-3'>
                                    <label class='form-label'>Archivo</label>
                                    <a href='https://apppcr.net/app/uploads/permisos/{$row['archivo_adjunto']}' target='_blank'>Ver Archivo</a>
                               </div>"
                            : "";

                        echo "
                        <div class='modal fade' id='modalAdjuntar{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='modalLabel{$row['id']}'>Ver detalle de la solicitud</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form method='POST' enctype='multipart/form-data'>
                                            <input type='hidden' name='permiso_id' value='{$row['id']}'>

                                            <div class='mb-3'>
                                                <label class='form-label'>Tipo de licencia</label>
                                                <input type='text' class='form-control' value='{$row['tipo_licencia']}' readonly>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label'>Fecha Desde</label>
                                                <b>{$row['fecha_inicio']}</b><br>
                                                <label class='form-label'>Fecha Hasta</label>
                                                <b>{$row['fecha_fin']}</b><br>";
                                        if ($row['tipo_licencia'] == 'Vacaciones') {
                                         echo " <label class='form-label'>Cantidad de dias</label>
                                                <b>{$cantidad_dias}</b>";
                                        }
                                           echo " </div>

                                            {$archivo}

                                            <div class='mb-3'>
                                                <label class='form-label'>Descripción</label>
                                                <textarea class='form-control' rows='3' readonly>{$row['descripcion']}</textarea>
                                            </div>

                                            <div class='mb-3'>
                                                <label class='form-label'>Seleccione una opción</label>
                                                <select name='respuesta_jefe' class='form-control' required>
                                                    <option value=''>Seleccionar</option>
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
                                                <button type='button' class='btn btn-primary btn-guardar' data-id='{$row['id']}' id='btnGuardar{$row['id']}'>
                                                    Guardar
                                                </button>
                                                <span id='loaderGuardar{$row['id']}' class='spinner-border spinner-border-sm text-primary d-none' role='status'></span>
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

<!-- Script: Guardar respuesta jefe + DataTable -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-guardar").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const id = btn.dataset.id;
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

    // Inicializar DataTable
    $('#tablaPermisosAprobar').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']] // Ordenar por la columna "Fecha de Solicitud" (índice 2)
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
