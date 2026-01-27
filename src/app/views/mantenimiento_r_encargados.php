<?php
// app/views/mantenimiento_r_encargados.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; 
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="container mt-4">

    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">R-Mantenimiento - Supervisores y Personal a Cargo</h5>
                    <p class="mb-0 text-muted"><span class="badge bg-warning">NUEVO SISTEMA</span> Gestiona los supervisores y asigna personal a cargo para aprobación de permisos y vacaciones</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsignarSupervisor">
                <i class="bi bi-person-plus"></i> Asignar Supervisor
            </button>
        </div>
    </div>

    <!-- Lista de Supervisores -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Supervisores Registrados</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($usuarios)): ?>
                        <div class="accordion" id="accordionSupervisores">
                            <?php foreach ($usuarios as $index => $supervisor): 
                                $personal_cargo = $userModel->get_personal_a_cargo($supervisor['codigo_empleado']);
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                        <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $index; ?>">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($supervisor['nombre'] . ' ' . $supervisor['apellido']); ?></strong>
                                                    <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($supervisor['codigo_empleado']); ?></span>
                                                    <span class="badge bg-info ms-2"><?php echo count($personal_cargo); ?> personal a cargo</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionSupervisores">
                                        <div class="accordion-body">
                                            <!-- Personal a Cargo -->
                                            <div class="mb-3">
                                                <h6 class="fw-bold mb-3">
                                                    <i class="bi bi-people-fill"></i> Personal a Cargo
                                                    <button type="button" class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#modalAgregarPersonal" onclick="setSupervisor('<?php echo htmlspecialchars($supervisor['codigo_empleado']); ?>', '<?php echo htmlspecialchars($supervisor['nombre'] . ' ' . $supervisor['apellido']); ?>')">
                                                        <i class="bi bi-person-plus"></i> Agregar Personal
                                                    </button>
                                                </h6>
                                                
                                                <?php if (!empty($personal_cargo)): ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Código</th>
                                                                    <th>Nombre</th>
                                                                    <th>Apellido</th>
                                                                    <th>Departamento</th>
                                                                    <th>Cargo</th>
                                                                    <th>Fecha Asignación</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($personal_cargo as $colaborador): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($colaborador['colaborador_code']); ?></td>
                                                                        <td><?php echo htmlspecialchars($colaborador['nombre']); ?></td>
                                                                        <td><?php echo htmlspecialchars($colaborador['apellido']); ?></td>
                                                                        <td><?php echo htmlspecialchars($colaborador['nombre_departamento'] ?? '-'); ?></td>
                                                                        <td><?php echo htmlspecialchars($colaborador['nombre_cargo'] ?? '-'); ?></td>
                                                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($colaborador['fecha_asignacion']))); ?></td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarPersonalCargo(<?php echo $colaborador['id']; ?>, '<?php echo htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']); ?>')">
                                                                                <i class="bi bi-trash"></i> Eliminar
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-info">
                                                        <i class="bi bi-info-circle"></i> Este supervisor no tiene personal a cargo asignado aún.
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Acciones del Supervisor -->
                                            <hr>
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalRemoverSupervisor" 
                                                        onclick="setSupervisorRemover('<?php echo htmlspecialchars($supervisor['codigo_empleado']); ?>', '<?php echo htmlspecialchars($supervisor['nombre'] . ' ' . $supervisor['apellido']); ?>')">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Remover como Supervisor
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No hay supervisores registrados. Asigna un supervisor para comenzar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Asignar Supervisor -->
    <div class="modal fade" id="modalAsignarSupervisor" tabindex="-1" aria-labelledby="modalAsignarSupervisorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_r_encargados=1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Asignar Supervisor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="usuarioSelect" class="form-label">Seleccionar Usuario</label>
                            <select id="usuarioSelect" name="codigo_usuario" class="form-select" required>
                                <option value="">-- Seleccione un usuario --</option>
                                <?php if (isset($usuarios_disponibles) && !empty($usuarios_disponibles)): ?>
                                    <?php foreach ($usuarios_disponibles as $usuario_disp): ?>
                                        <option value="<?= htmlspecialchars($usuario_disp['codigo_empleado']) ?>">
                                            <?= htmlspecialchars($usuario_disp['codigo_empleado']) ?> - <?= htmlspecialchars($usuario_disp['nombre']) ?> <?= htmlspecialchars($usuario_disp['apellido']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Seleccione un usuario de la lista para asignarlo como supervisor (type_user = 6)</small>
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

    <!-- Modal Agregar Personal a Cargo -->
    <div class="modal fade" id="modalAgregarPersonal" tabindex="-1" aria-labelledby="modalAgregarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_r_encargados=1" id="formAgregarPersonal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Personal a Cargo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Supervisor:</strong> <span id="nombreSupervisorModal"></span>
                        </div>
                        <div class="mb-3">
                            <label for="colaboradorSelect" class="form-label">Seleccionar Colaborador</label>
                            <select id="colaboradorSelect" name="colaborador_code" class="form-select" required>
                                <option value="">-- Seleccione un colaborador --</option>
                            </select>
                            <small class="text-muted">Seleccione el colaborador que estará a cargo de este supervisor</small>
                        </div>
                        <input type="hidden" name="supervisor_code" id="supervisorCodeInput">
                        <input type="hidden" name="asignar_personal_cargo" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Remover Supervisor -->
    <div class="modal fade" id="modalRemoverSupervisor" tabindex="-1" aria-labelledby="modalRemoverSupervisorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remover Supervisor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>¿Está seguro?</strong><br>
                        Esta acción regresará al usuario <strong id="nombreSupervisorRemover"></strong> a tipo usuario 2 (colaborador normal).
                        <br><br>
                        <strong>Nota:</strong> Esto también desactivará todas las relaciones de personal a cargo de este supervisor.
                    </div>
                    <form method="POST" action="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_r_encargados=1" id="formRemoverSupervisor">
                        <input type="hidden" name="codigo_usuario" id="codigoSupervisorRemover">
                        <input type="hidden" name="remover_encargado" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" onclick="document.getElementById('formRemoverSupervisor').submit();">
                        <i class="bi bi-arrow-counterclockwise"></i> Sí, Remover
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Función para establecer el supervisor al abrir modal de agregar personal
    function setSupervisor(supervisorCode, supervisorNombre) {
        document.getElementById('supervisorCodeInput').value = supervisorCode;
        document.getElementById('nombreSupervisorModal').textContent = supervisorNombre;
        
        // Cargar colaboradores disponibles para este supervisor
        cargarColaboradoresDisponibles(supervisorCode);
    }

    // Función para cargar colaboradores disponibles
    function cargarColaboradoresDisponibles(supervisorCode) {
        // Hacer petición AJAX para obtener colaboradores disponibles
        fetch('<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?obtener_colaboradores_disponibles_r=1&supervisor_code=' + supervisorCode)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('colaboradorSelect');
                select.innerHTML = '<option value="">-- Seleccione un colaborador --</option>';
                
                if (data.success && data.colaboradores) {
                    data.colaboradores.forEach(colab => {
                        const option = document.createElement('option');
                        option.value = colab.codigo_empleado;
                        option.textContent = `${colab.codigo_empleado} - ${colab.nombre} ${colab.apellido}`;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar colaboradores disponibles');
            });
    }

    // Función para establecer datos al remover supervisor
    function setSupervisorRemover(codigo, nombre) {
        document.getElementById('codigoSupervisorRemover').value = codigo;
        document.getElementById('nombreSupervisorRemover').textContent = nombre;
    }

    // Función para eliminar personal a cargo
    function eliminarPersonalCargo(idRelacion, nombreColaborador) {
        if (confirm(`¿Está seguro que desea eliminar a ${nombreColaborador} del personal a cargo?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo BASE_URL_CONTROLLER; ?>/MainController.php?mantenimiento_r_encargados=1';
            
            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_relacion';
            inputId.value = idRelacion;
            
            const inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'remover_personal_cargo';
            inputAction.value = '1';
            
            form.appendChild(inputId);
            form.appendChild(inputAction);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Inicializar Select2 cuando el modal se abre
    $('#modalAgregarPersonal').on('shown.bs.modal', function () {
        $('#colaboradorSelect').select2({
            dropdownParent: $('#modalAgregarPersonal'),
            width: '100%'
        });
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
