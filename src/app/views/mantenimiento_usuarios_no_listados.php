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
                <?php foreach ($usuarios_no_listados as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['fecha_log']) ?></td>
                        <td><?= htmlspecialchars($usuario['codigo_empleado']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar" 
                                    data-code="<?= $usuario['codigo_empleado'] ?>">
                                Editar User
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios_no_listados=1">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="codigo_empleado" id="codigoEmpleadoInput" value="<?= htmlspecialchars($usuario['codigo_empleado']) ?>">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="apellido" class="form-label">Apellido</label>
                                                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($usuario['fecha_log']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" name="editar_usuario">Guardar</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-warning" 
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
                <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cambiar_estado_usuario_no_listado=1">
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
                            <button type="submit" class="btn btn-primary" name="actualizar_password">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Estado -->
        <div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?cambiar_estado_usuario_no_listado=1">
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

    var modalEstado = document.getElementById('modalEditar');
    modalEstado.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var codigo = button.getAttribute('data-code');
        var fecha_nacimiento = button.getAttribute('data-fecha');
        modalfecha_nacimiento.querySelector('#codigoEstadoInput').value = codigo;
        modalEstado.querySelector('#fecha_nacimiento').value = fecha_nacimiento;
    });
</script>
<?php include __DIR__ . '/footer.php'; ?>