<?php
// app/views/mantenimiento_encargados.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container mt-4">

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Mantenimiento - Encargados </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsignarEncargado">
                <i class="bi bi-person-plus"></i> Asignar Encargado
            </button>
        </div>
    </div>

    <div class="row text-center mb-4">
        
        <table id="tablaEncargados" class="table table-bordered table-striped">
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
                            <button class="btn btn-sm btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEdicion"
                                    data-code="<?= $usuario['codigo_empleado'] ?>"
                                    data-nombre="<?= htmlspecialchars($usuario['nombre']) ?>"
                                    data-apellido="<?= htmlspecialchars($usuario['apellido']) ?>">
                                <i class="bi bi-pencil"></i> Edición
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Asignar Encargado -->
        <div class="modal fade" id="modalAsignarEncargado" tabindex="-1" aria-labelledby="modalAsignarEncargadoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_encargados=1">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Asignar Encargado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="usuarioSelect" class="form-label">Buscar Usuario</label>
                                <select id="usuarioSelect" name="codigo_usuario" class="form-control" style="width: 100%;" required>
                                    <option value="">-- Seleccione un usuario --</option>
                                </select>
                                <small class="text-muted">Escriba para buscar por nombre, apellido o código</small>
                            </div>
                            <input type="hidden" name="asignar_encargado" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Asignar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edición -->
        <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edición de Encargado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><strong>Código:</strong></label>
                            <p id="edicionCodigo"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Nombre:</strong></label>
                            <p id="edicionNombre"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Apellido:</strong></label>
                            <p id="edicionApellido"></p>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Funcionalidad de edición en desarrollo.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    <br>
    <br><br><br><br>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimineto=1" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar Select2 con autocompletado
        $('#usuarioSelect').select2({
            placeholder: 'Buscar usuario...',
            allowClear: true,
            ajax: {
                url: '<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?buscar_usuarios_autocomplete=1',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // término de búsqueda
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.codigo_empleado,
                                text: item.codigo_empleado + ' - ' + item.nombre + ' ' + item.apellido
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // Inicializar DataTable
        $('#tablaEncargados').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });

        // Modal de Edición
        $('#modalEdicion').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var codigo = button.data('code');
            var nombre = button.data('nombre');
            var apellido = button.data('apellido');
            
            $('#edicionCodigo').text(codigo);
            $('#edicionNombre').text(nombre);
            $('#edicionApellido').text(apellido);
        });
    });
</script>
<?php include __DIR__ . '/footer.php'; ?>
