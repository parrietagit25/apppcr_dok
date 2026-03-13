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
                    <h5 class="fw-bold">Solicitud de Permiso</h5>
                </div>
            </div>
        </div>
    </div>

    <?php
    $error_permiso_duplicado = $error_permiso_duplicado ?? '';
    $permisos_pendientes = $permisos_pendientes ?? [];
    if ($error_permiso_duplicado !== '') {
        echo '<div class="alert alert-warning mb-3"><i class="bi bi-exclamation-triangle"></i> ' . htmlspecialchars($error_permiso_duplicado) . '</div>';
    }
    if (!empty($permisos_pendientes)) {
        echo '<div class="alert alert-info mb-3"><strong>Solicitudes pendientes de respuesta:</strong> Tienes ' . count($permisos_pendientes) . ' solicitud(es) en espera.<ul class="mb-0 mt-2">';
        foreach ($permisos_pendientes as $p) {
            echo '<li>' . htmlspecialchars($p['tipo_licencia']) . ' (' . htmlspecialchars($p['fecha_inicio']) . ' – ' . htmlspecialchars($p['fecha_fin']) . ')</li>';
        }
        echo '</ul></div>';
    }
    ?>
    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permiso">
            Solicitar Permiso
        </button>
        <br><br>
        <p>
            <span style="color:red;"><b>Nota: Recuerde que las vacaciones deben ser aprobadas previamente por su supervisor para su debida autorización.</b></span><br>
            <span class="text-muted"><b>Vacaciones (tipo permiso):</b> Solo se puede solicitar: del 1 al 15 del mes, del 16 al último día del mes (en febrero al 28 o 29), o del 1 al último día del mes (mes completo). No se puede cruzar ambas mitades (ej: del 10 al 20). En el detalle puede indicar los días que tomará realmente.</span>
        </p>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Permiso</h5>
        <table id="tablaPermisos" class="table table-striped table-bordered mt-3" style="width: 100%;">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $status = '';
                
                
                if($tipo_usuario == 6){
                    $permisos = $class->select_permisos_gerentes($_SESSION['code']) ?? [];
                }else{
                    $permisos = $class->select_permisos() ?? [];
                }

                if (is_array($permisos) && count($permisos) > 0) {
                    foreach ($permisos as $row) {
                        $status = match($row['stat']) {
                            1 => 'Solicitado',
                            2 => 'Aprobado',
                            default => 'Declinado'
                        };
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['descripcion']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['fecha_log']) . '</td>';
                        echo '<td>' . htmlspecialchars($status) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    //echo '<tr><td colspan="4" class="text-center">No hay solicitudes registradas.</td></tr>';
                }
                ?>
            </tbody>

        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="permiso" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Seleccione su supervisor</label>
                        <select name="id_jefe" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <?php if (!empty($select_jefe)): ?>
                                <?php foreach ($select_jefe as $value): ?>
                                    <option value="<?php echo htmlspecialchars($value['code_empleado'] ?? $value['codigo_empleado'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($value['nombre'] . ' ' . $value['apellido']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No tiene supervisores asignados</option>
                            <?php endif; ?>
                        </select>
                        <?php if (empty($select_jefe)): ?>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                No tiene supervisores asignados. Contacte a RRHH para asignar un supervisor.
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de licencia</label>
                        <select name="tipo_licencia" class="form-control" required>
                            <option value="">Seleccionar</option>
                            <option value="Vacaciones">Vacaciones (Acumuladas: <?php echo $mis_vacas[0]['dias_vaca_acu_tiempo'] ?? '0'; ?> días)</option>
                            <option value="Enfermedad">Enfermedad</option>
                            <option value="Duelo">Duelo</option>
                            <option value="Tiempo sin pago">Tiempo sin pago</option>
                            <option value="Compensatorio">Compensatorio</option>
                            <option value="Flex day">Flex day</option>
                            <option value="Cita Medica">Cita Médica</option>
                            <option value="Teletrabajo">Teletrabajo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio_permiso" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin_permiso" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar un archivo (opcional)</label>
                        <input type="file" name="archivo_adjunto" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción del Permiso</label>
                        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                    </div>

                    <input type="hidden" name="solicitud_permiso" value="1">

                    <div class="d-flex align-items-center gap-2">
                        <input type="button" class="btn btn-primary" id="btnPermiso" value="Solicitar Permiso">
                        <span id="loaderPermiso" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
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
<!-- JS: Formulario + DataTable -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Envío del formulario con loader
    const form = document.querySelector('#permiso form');
    const btn = document.getElementById("btnPermiso");
    const loader = document.getElementById("loaderPermiso");

    if (form && btn && loader) {
        btn.addEventListener("click", function () {
            btn.disabled = true;
            btn.value = "Enviando...";
            loader.classList.remove("d-none");
            setTimeout(() => form.submit(), 800);
        });
    }

    // Fechas: solo bloquear pasadas si el tipo es Vacaciones; para el resto permitir días anteriores
    var selectTipo = form ? form.querySelector('select[name="tipo_licencia"]') : null;
    var fechaInicioInput = document.getElementById('fecha_inicio_permiso');
    var fechaFinInput = document.getElementById('fecha_fin_permiso');

    function aplicarRestriccionFechasPermiso() {
        if (!fechaInicioInput || !fechaFinInput) return;
        fechaInicioInput.removeAttribute('min');
        fechaFinInput.removeAttribute('min');
    }

    if (selectTipo) {
        selectTipo.addEventListener('change', aplicarRestriccionFechasPermiso);
    }
    aplicarRestriccionFechasPermiso();

    // DataTable en español
    $('#tablaPermisos').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']],
        initComplete: function () {
            console.log("Tabla inicializada correctamente");
        }
    });
    
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
