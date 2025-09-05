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

        <div class="text-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                Registrar Nuevo Usuario (Fuera de Planilla)
            </button>
        </div>

        <!-- Modal Registrar Usuario -->
        <div class="modal fade" id="modalRegistrar" tabindex="-1" aria-labelledby="modalRegistrarLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action=""> <!-- <?php echo BASE_URL_CONTROLLER; ?>/MainController.php?registrar_usuario_no_listado=1-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Registrar Nuevo Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="codigo_empleado" class="form-label">Código Empleado</label>
                                <input type="text" class="form-control" id="codigo_empleado" name="codigo_empleado" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                            <div class="mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" class="form-control" id="departamento" name="departamento" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="gerente_area" class="form-label">Gerente de Área</label>
                                <input type="text" class="form-control" id="gerente_area" name="gerente_area">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" name="registrar_usuario">Registrar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <table id="tablaUsuarios" class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Departamento</th>
                    <th>Email</th>
                    <th>Código Empleado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_no_listados as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['departamento']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['code_empleado']) ?></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar" 
                                    data-code="<?= $usuario['code_empleado'] ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                    data-apellido="<?= htmlspecialchars($usuario['apellido']) ?>"
                                    data-departamento="<?= htmlspecialchars($usuario['departamento']) ?>"
                                    data-email="<?= htmlspecialchars($usuario['email']) ?>"
                                    data-gerente="<?= htmlspecialchars($usuario['gerente_area']) ?>">
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
                                                <input type="hidden" name="codigo_empleado" id="codigoEmpleadoInput" value="<?= htmlspecialchars($usuario['code_empleado']) ?>">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="apellido" class="form-label">Apellido</label>
                                                    <input type="text" class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="departamento" class="form-label">Departamento</label>
                                                    <input type="text" class="form-control" id="departamento" name="departamento" value="<?= htmlspecialchars($usuario['departamento']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="gerente_area" class="form-label">Gerente de Área</label>
                                                    <input type="text" class="form-control" id="gerente_area" name="gerente_area" value="<?= htmlspecialchars($usuario['gerente_area']) ?>">
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
                                    data-code="<?= $usuario['code_empleado'] ?>">
                                Cambiar Contraseña
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStatus"
                                    data-code="<?= $usuario['code_empleado'] ?>"
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

    var modalEditar = document.getElementById('modalEditar');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var codigo = button.getAttribute('data-code');
        var nombre = button.getAttribute('data-nombre');
        var apellido = button.getAttribute('data-apellido');
        var departamento = button.getAttribute('data-departamento');
        var email = button.getAttribute('data-email');
        var gerente = button.getAttribute('data-gerente');
        
        modalEditar.querySelector('#codigoEmpleadoInput').value = codigo;
        modalEditar.querySelector('#nombre').value = nombre;
        modalEditar.querySelector('#apellido').value = apellido;
        modalEditar.querySelector('#departamento').value = departamento;
        modalEditar.querySelector('#email').value = email;
        modalEditar.querySelector('#gerente_area').value = gerente;
    });
</script>
<?php include __DIR__ . '/footer.php'; ?>