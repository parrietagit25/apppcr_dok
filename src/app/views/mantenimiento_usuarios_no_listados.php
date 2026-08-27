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
                Registrar Nuevo Usuario
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
                            <!-- Información Básica -->
                            <h6 class="text-primary mb-3">📋 Información Básica</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="codigo_empleado" class="form-label">Código Empleado *</label>
                                        <input type="text" class="form-control" id="codigo_empleado" name="codigo_empleado" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cedula" class="form-label">Cédula *</label>
                                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado_civil" class="form-label">Estado Civil</label>
                                        <select class="form-select" id="estado_civil" name="estado_civil">
                                            <option value="">Seleccionar...</option>
                                            <option value="Soltero">Soltero</option>
                                            <option value="Casado">Casado</option>
                                            <option value="Divorciado">Divorciado</option>
                                            <option value="Viudo">Viudo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <h6 class="text-primary mb-3 mt-4">📞 Información de Contacto</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono1" class="form-label">Teléfono Principal *</label>
                                        <input type="tel" class="form-control" id="telefono1" name="telefono1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono2" class="form-label">Teléfono Secundario</label>
                                        <input type="tel" class="form-control" id="telefono2" name="telefono2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="direccion1" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion1" name="direccion1">
                                    </div>
                                </div>
                            </div>

                            <!-- Información Laboral -->
                            <h6 class="text-primary mb-3 mt-4">💼 Información Laboral</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre_departamento" class="form-label">Departamento *</label>
                                        <input type="text" class="form-control" id="nombre_departamento" name="nombre_departamento" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre_cargo" class="form-label">Cargo *</label>
                                        <input type="text" class="form-control" id="nombre_cargo" name="nombre_cargo" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                                        <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="salario_pactado" class="form-label">Salario Pactado *</label>
                                        <input type="number" class="form-control" id="salario_pactado" name="salario_pactado" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estatus_empleado" class="form-label">Estatus *</label>
                                        <select class="form-select" id="estatus_empleado" name="estatus_empleado" required>
                                            <option value="A">Activo</option>
                                            <option value="V">Vacaciones</option>
                                            <option value="I">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="seguro_social" class="form-label">Seguro Social</label>
                                        <input type="text" class="form-control" id="seguro_social" name="seguro_social">
                                    </div>
                                </div>
                            </div>

                            <!-- Información Adicional -->
                            <h6 class="text-primary mb-3 mt-4">ℹ️ Información Adicional</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sexo" class="form-label">Sexo</label>
                                        <select class="form-select" id="sexo" name="sexo">
                                            <option value="">Seleccionar...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nacionalidad" class="form-label">Nacionalidad</label>
                                        <input type="text" class="form-control" id="nacionalidad" name="nacionalidad" value="Panameña">
                                    </div>
                                </div>
                            </div>

                            <!-- Acceso al Sistema -->
                            <h6 class="text-primary mb-3 mt-4">🔐 Acceso al Sistema</h6>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
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
                    <th>Código</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Departamento</th>
                    <th>Cargo</th>
                    <th>Salario</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios_no_listados as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['codigo_empleado']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                        <td><?= htmlspecialchars($usuario['email'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_departamento'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_cargo'] ?? 'N/A') ?></td>
                        <td>$<?= number_format($usuario['salario_pactado'] ?? 0, 2) ?></td>
                        <td>
                            <span class="badge <?= ($usuario['estatus_empleado'] ?? '') == 'A' ? 'bg-success' : 'bg-warning' ?>">
                                <?= htmlspecialchars($usuario['estatus_empleado'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary btn-editar-usuario"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-apellido="<?= htmlspecialchars($usuario['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-cedula="<?= htmlspecialchars($usuario['cedula'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-fecha="<?= htmlspecialchars(!empty($usuario['fecha_nacimiento']) ? substr((string) $usuario['fecha_nacimiento'], 0, 10) : '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-fecha-ingreso="<?= htmlspecialchars(!empty($usuario['fecha_ingreso']) ? substr((string) $usuario['fecha_ingreso'], 0, 10) : '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-email="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-telefono1="<?= htmlspecialchars($usuario['telefono1'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-departamento="<?= htmlspecialchars($usuario['nombre_departamento'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-cargo="<?= htmlspecialchars($usuario['nombre_cargo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-salario="<?= htmlspecialchars((string) ($usuario['salario_pactado'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-estatus="<?= htmlspecialchars($usuario['estatus_empleado'] ?? 'A', ENT_QUOTES, 'UTF-8') ?>">
                                Editar User
                            </button>
                            <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPassword"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                Cambiar Contraseña
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStatus"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    data-status="<?= htmlspecialchars((string) ($usuario['stat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                Desactivar usuario
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Editar Usuario (unico, fuera del loop) -->
        <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios_no_listados=1">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarLabel">Editar User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="codigo_empleado" id="editCodigoEmpleado">

                            <h6 class="text-primary mb-3">Información Básica</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editNombre" class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" id="editNombre" name="nombre" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editApellido" class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" id="editApellido" name="apellido" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editCedula" class="form-label">Cédula *</label>
                                        <input type="text" class="form-control" id="editCedula" name="cedula" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editFechaNacimiento" class="form-label">Fecha de Nacimiento *</label>
                                        <input type="date" class="form-control" id="editFechaNacimiento" name="fecha_nacimiento" required>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary mb-3 mt-4">Información de Contacto</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEmail" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="editEmail" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editTelefono1" class="form-label">Teléfono Principal *</label>
                                        <input type="tel" class="form-control" id="editTelefono1" name="telefono1" required>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-primary mb-3 mt-4">Información Laboral</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editDepartamento" class="form-label">Departamento *</label>
                                        <input type="text" class="form-control" id="editDepartamento" name="nombre_departamento" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editCargo" class="form-label">Cargo *</label>
                                        <input type="text" class="form-control" id="editCargo" name="nombre_cargo" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editFechaIngreso" class="form-label">Fecha de Ingreso</label>
                                        <input type="date" class="form-control" id="editFechaIngreso" name="fecha_ingreso">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editSalario" class="form-label">Salario Pactado *</label>
                                        <input type="number" class="form-control" id="editSalario" name="salario_pactado" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEstatus" class="form-label">Estatus *</label>
                                        <select class="form-select" id="editEstatus" name="estatus_empleado" required>
                                            <option value="A">Activo</option>
                                            <option value="V">Vacaciones</option>
                                            <option value="I">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
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

        <!-- Modal -->
        <div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios_no_listados=1">
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
        var modalPassword = document.getElementById('modalPassword');
        if (modalPassword) {
            modalPassword.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;
                var codigo = button.getAttribute('data-code') || '';
                var inputCodigo = modalPassword.querySelector('#codigoEmpleadoInput');
                if (inputCodigo) inputCodigo.value = codigo;
            });
        }

        var modalEstado = document.getElementById('modalStatus');
        if (modalEstado) {
            modalEstado.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;
                var codigo = button.getAttribute('data-code') || '';
                var estado = button.getAttribute('data-status') || '';
                var inputCodigo = modalEstado.querySelector('#codigoEstadoInput');
                var inputEstado = modalEstado.querySelector('#estadoActualInput');
                if (inputCodigo) inputCodigo.value = codigo;
                if (inputEstado) inputEstado.value = estado;
            });
        }

        var modalEditar = document.getElementById('modalEditar');
        if (modalEditar) {
            modalEditar.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;

                var setVal = function (id, value) {
                    var el = modalEditar.querySelector('#' + id);
                    if (el) el.value = value || '';
                };

                setVal('editCodigoEmpleado', button.getAttribute('data-code'));
                setVal('editNombre', button.getAttribute('data-nombre'));
                setVal('editApellido', button.getAttribute('data-apellido'));
                setVal('editCedula', button.getAttribute('data-cedula'));
                setVal('editFechaNacimiento', button.getAttribute('data-fecha'));
                setVal('editFechaIngreso', button.getAttribute('data-fecha-ingreso'));
                setVal('editEmail', button.getAttribute('data-email'));
                setVal('editTelefono1', button.getAttribute('data-telefono1'));
                setVal('editDepartamento', button.getAttribute('data-departamento'));
                setVal('editCargo', button.getAttribute('data-cargo'));
                setVal('editSalario', button.getAttribute('data-salario'));
                setVal('editEstatus', button.getAttribute('data-estatus') || 'A');
            });
        }

        if (window.jQuery && $('#tablaUsuarios').length) {
            $('#tablaUsuarios').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        }
    });
</script>
<?php include __DIR__ . '/footer.php'; ?>
