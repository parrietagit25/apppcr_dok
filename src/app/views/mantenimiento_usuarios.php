<?php
// app/views/rrhh.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<div class="container mt-4">

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Mantenimiento - Usuarios </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row text-center mb-4">
        
        <table id="tablaUsuarios" class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha Log</th>
                    <th>Código Empleado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['fecha_log']) ?></td>
                        <td><?= htmlspecialchars($usuario['codigo_empleado']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEstatusEmpleado"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado']) ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                    data-estatus="<?= htmlspecialchars($usuario['estatus_empleado'] ?? '') ?>">
                                Status
                            </button>
                            <button class="btn btn-sm btn-warning me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalPassword" 
                                    data-code="<?= $usuario['codigo_empleado'] ?>">
                                Cambiar Contraseña
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStatus"
                                    data-code="<?= $usuario['codigo_empleado'] ?>"
                                    data-status="<?= $usuario['stat'] ?>">
                                Desactivar usuario
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios=1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="codigo_empleado" id="codigoEmpleadoInput">
                        <div class="mb-3">
                            <label for="nuevaPassword" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="nuevaPassword" name="nueva_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
        </div>

        <!-- Modal Estatus empleado (tabla empleados) -->
        <div class="modal fade" id="modalEstatusEmpleado" tabindex="-1" aria-labelledby="modalEstatusEmpleadoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios=1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEstatusEmpleadoLabel">Estatus del colaborador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="actualizar_estatus_empleado" value="1">
                        <input type="hidden" name="codigo_empleado" id="estatusEmpleadoCodigoInput">
                        <p class="mb-2"><strong>Colaborador:</strong> <span id="estatusEmpleadoNombreText"></span></p>
                        <p class="mb-3"><strong>Estatus actual:</strong> <span id="estatusEmpleadoActualText" class="badge bg-secondary"></span></p>
                        <div class="mb-3">
                            <label for="estatus_empleado_select" class="form-label">Nuevo estatus</label>
                            <select class="form-select" id="estatus_empleado_select" name="estatus_empleado" required>
                                <?php
                                $estatus_lista = $estatus_empleados ?? [];
                                foreach ($estatus_lista as $est): ?>
                                    <option value="<?= htmlspecialchars($est) ?>"><?= htmlspecialchars($est) ?></option>
                                <?php endforeach; ?>
                                <?php if (empty($estatus_lista)): ?>
                                    <option value="A">A</option>
                                    <option value="C">C</option>
                                    <option value="V">V</option>
                                    <option value="I">I</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
        </div>

        <!-- Modal Estado -->
        <div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cambiar_estado_usuario=1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Desactivar usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="codigo_empleado" id="codigoEstadoInput">
                        <input type="hidden" name="estado_actual" id="estadoActualInput">
                        <p>¿Está seguro que desea desactivar usuario?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Confirmar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
        </div>



    <br>
    <br><br><br><br>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios=1" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('modalPassword');
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var codigo = button.getAttribute('data-code');
            var inputCodigo = modal.querySelector('#codigoEmpleadoInput');
            inputCodigo.value = codigo;
        });

        // Modal Estatus empleado
        var modalEstatus = document.getElementById('modalEstatusEmpleado');
        if (modalEstatus) {
            modalEstatus.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var codigo = button.getAttribute('data-code');
                var nombre = button.getAttribute('data-nombre') || '';
                var estatus = button.getAttribute('data-estatus') || '';
                modalEstatus.querySelector('#estatusEmpleadoCodigoInput').value = codigo || '';
                modalEstatus.querySelector('#estatusEmpleadoNombreText').textContent = nombre;
                modalEstatus.querySelector('#estatusEmpleadoActualText').textContent = estatus || 'N/A';
                var select = modalEstatus.querySelector('#estatus_empleado_select');
                if (select) {
                    select.value = estatus || (select.options.length ? select.options[0].value : '');
                }
            });
        }

        // Inicializar DataTable
        $('#tablaUsuarios').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });
    });

    var modalEstado = document.getElementById('modalStatus');
    modalEstado.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var codigo = button.getAttribute('data-code');
    var estado = button.getAttribute('data-status');
    modalEstado.querySelector('#codigoEstadoInput').value = codigo;
    modalEstado.querySelector('#estadoActualInput').value = estado;
});
</script>
<?php include __DIR__ . '/footer.php'; ?>