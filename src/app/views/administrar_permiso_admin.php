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

    <?php if ($tipo_usuario == 1 && !empty($todos_supervisores)): ?>
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" action="" class="d-flex gap-2">
                <input type="hidden" name="administrar_permiso_admin" value="1">
                <label for="selectSupervisor" class="form-label align-self-center mb-0">Filtrar por Supervisor:</label>
                <select name="supervisor" id="selectSupervisor" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Todos los permisos --</option>
                    <?php foreach ($todos_supervisores as $supervisor): ?>
                        <option value="<?php echo htmlspecialchars($supervisor['codigo_empleado']); ?>" 
                                <?php echo (isset($supervisor_seleccionado) && $supervisor_seleccionado == $supervisor['codigo_empleado']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($supervisor['codigo_empleado'] . ' - ' . $supervisor['nombre'] . ' ' . $supervisor['apellido']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php if (isset($supervisor_seleccionado) && $supervisor_seleccionado): ?>
        <div class="col-md-6">
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> 
                Mostrando permisos del supervisor seleccionado. 
                <a href="?administrar_permiso_admin=1" class="alert-link">Ver todos</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Permiso</h5>
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
                // $permisos ya viene del controlador con el filtro aplicado
                if (!isset($permisos)) {
                    $permisos = [];
                }
                if (!empty($permisos)) {
                    foreach ($permisos as $row) {
                        $status = $row['stat'] == 1 ? 'Solicitado' : ($row['stat'] == 2 ? 'Aprobado' : 'Declinado');

                        $cantidad_dias = 0;
                        if($row['tipo_licencia'] == 'Vacaciones'){
                            $inicio = new DateTime($row['fecha_inicio']);
                            $fin = new DateTime($row['fecha_fin']);
                            $diferencia = $inicio->diff($fin);
                            $cantidad_dias = $diferencia->days;
                            $cantidad_dias = $cantidad_dias + 1;
                        }

                        $fecha_log = isset($row['fecha_log']) && !empty($row['fecha_log']) 
                            ? htmlspecialchars($row['fecha_log']) 
                            : (isset($row['fecha_inicio']) ? htmlspecialchars($row['fecha_inicio']) : 'N/A');
                        
                        echo "<tr>
                                <td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>
                                <td>" . htmlspecialchars($row['tipo_licencia']) . "</td>
                                <td>" . $fecha_log . "</td>
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
    var tabla = $('#tablaPermisosAprobar');
    if (tabla.length) {
        // Verificar que la tabla tenga la estructura correcta
        var numColumnas = tabla.find('thead tr th').length;
        var numFilas = tabla.find('tbody tr').length;
        
        if (numColumnas === 4) {
            try {
                tabla.DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    },
                    pageLength: 10,
                    order: [[2, 'desc']], // Ordenar por la columna "Fecha de Solicitud" (índice 2)
                    responsive: false,
                    autoWidth: false
                });
            } catch (e) {
                console.error('Error al inicializar DataTable:', e);
            }
        } else {
            console.warn('La tabla no tiene 4 columnas. Columnas encontradas:', numColumnas);
        }
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
