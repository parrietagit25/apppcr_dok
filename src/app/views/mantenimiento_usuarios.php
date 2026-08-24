<?php
// app/views/rrhh.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>
<style>
    .btn-accion-usuario {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
    }
</style>

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
                        <td class="text-center text-nowrap">
                            <button type="button" class="btn btn-sm btn-info btn-accion-usuario me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEstatusEmpleado"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado']) ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                    data-estatus="<?= htmlspecialchars($usuario['estatus_empleado'] ?? '') ?>"
                                    title="Estatus del colaborador"
                                    aria-label="Estatus del colaborador">
                                <i class="bi bi-person-badge"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning btn-accion-usuario me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalPassword"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado']) ?>"
                                    title="Cambiar contraseña"
                                    aria-label="Cambiar contraseña">
                                <i class="bi bi-key-fill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary btn-accion-usuario me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalFotoColaborador"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado']) ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                    title="Registrar / actualizar foto"
                                    aria-label="Registrar o actualizar foto">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-accion-usuario"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalStatus"
                                    data-code="<?= htmlspecialchars($usuario['codigo_empleado']) ?>"
                                    data-status="<?= htmlspecialchars($usuario['stat']) ?>"
                                    title="Desactivar usuario"
                                    aria-label="Desactivar usuario">
                                <i class="bi bi-person-x-fill"></i>
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

        <!-- Modal Foto colaborador -->
        <div class="modal fade" id="modalFotoColaborador" tabindex="-1" aria-labelledby="modalFotoColaboradorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_usuarios=1" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFotoColaboradorLabel">Registrar / actualizar foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <input type="hidden" name="actualizar_foto_colaborador" value="1">
                        <input type="hidden" name="codigo_empleado" id="fotoCodigoInput">
                        <p class="mb-1"><strong>Colaborador:</strong> <span id="fotoNombreText"></span></p>
                        <p class="mb-3 text-muted small">La foto se guarda como <span class="font-monospace" id="fotoNombreArchivoText"></span> en el directorio de carnet.</p>
                        <img id="fotoPreviewActual" src="" alt="Foto actual" class="rounded-circle mx-auto d-block mb-3" style="width: 160px; height: 160px; object-fit: cover; border: 3px solid #dee2e6;">
                        <div class="mb-0 text-start">
                            <label for="foto_colaborador" class="form-label">Nueva foto</label>
                            <input type="file" class="form-control" id="foto_colaborador" name="foto_colaborador" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif" required>
                            <div class="form-text">JPG, PNG, WEBP o GIF. Se guardará como JPEG. Máximo 8 MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar foto</button>
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
        var placeholderFoto = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160"><rect fill="%23ddd" width="160" height="160"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23999" font-size="13">Sin foto</text></svg>';
        var baseUrlImage = '<?php echo addslashes(BASE_URL_IMAGE ?? ""); ?>';

        function numeroFotoCarnet(codigo) {
            var soloDigitos = String(codigo || '').replace(/\D/g, '');
            if (!soloDigitos) {
                return '';
            }
            return soloDigitos.length > 2 ? soloDigitos.substring(2) : soloDigitos;
        }

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

        var modalFoto = document.getElementById('modalFotoColaborador');
        var inputFoto = document.getElementById('foto_colaborador');
        if (modalFoto) {
            modalFoto.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var codigo = button && button.getAttribute('data-code') ? button.getAttribute('data-code') : '';
                var nombre = button && button.getAttribute('data-nombre') ? button.getAttribute('data-nombre') : '';
                var numero = numeroFotoCarnet(codigo);
                var img = document.getElementById('fotoPreviewActual');
                modalFoto.querySelector('#fotoCodigoInput').value = codigo;
                modalFoto.querySelector('#fotoNombreText').textContent = nombre || codigo;
                modalFoto.querySelector('#fotoNombreArchivoText').textContent = numero ? ('imagen_carnet/' + numero + '.jpeg') : '';
                if (inputFoto) {
                    inputFoto.value = '';
                }
                if (img) {
                    img.src = numero ? (baseUrlImage + 'imagen_carnet/' + numero + '.jpeg?t=' + Date.now()) : placeholderFoto;
                    img.onerror = function () {
                        this.onerror = null;
                        this.src = placeholderFoto;
                    };
                }
            });
        }
        if (inputFoto) {
            inputFoto.addEventListener('change', function () {
                var img = document.getElementById('fotoPreviewActual');
                var file = this.files && this.files[0];
                if (!img || !file) {
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // Inicializar DataTable
        $('#tablaUsuarios').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
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