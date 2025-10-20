<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
    .badge-solicitado { background-color: #ffc107; color: #000; }
    .badge-proceso { background-color: #0dcaf0; color: #000; }
    .badge-entregado { background-color: #198754; color: #fff; }
    
    /* Optimización para móvil */
    @media (max-width: 768px) {
        .table { font-size: 0.85rem; }
        .btn-sm { font-size: 0.7rem; padding: 0.25rem 0.5rem; }
    }
</style>

<div class="container mt-4">
    
    <!-- Título -->
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">👕 Solicitud de Uniformes</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Solicitar -->
    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUniforme">
            ➕ Solicitar Uniforme
        </button>
    </div>

    <!-- Tabla de Solicitudes -->
    <div class="row mt-5">
        <div class="col-12">
            <h5 class="text-center mb-3">Mis Solicitudes</h5>
            <div class="table-responsive">
                <table id="tablaUniformes" class="table table-striped table-bordered table-hover mt-3" style="width: 100%;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Tipo</th>
                            <th>Talla</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <?php if ($tipo_usuario == 1 || $tipo_usuario == 4): ?>
                            <th>Acción</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (is_array($uniformes) && count($uniformes) > 0) {
                            foreach ($uniformes as $row) {
                                $status_badge = match($row['stat']) {
                                    1 => '<span class="badge badge-solicitado">Solicitado</span>',
                                    2 => '<span class="badge badge-proceso">En Proceso</span>',
                                    3 => '<span class="badge badge-entregado">Entregado</span>',
                                    default => '<span class="badge bg-secondary">Desconocido</span>'
                                };
                                
                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars(ucfirst($row['tipo'])) . '</strong></td>';
                                echo '<td class="text-center">' . htmlspecialchars($row['talla']) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['fecha_log'])) . '</td>';
                                echo '<td class="text-center">' . $status_badge . '</td>';
                                
                                if ($tipo_usuario == 1 || $tipo_usuario == 4):
                                    echo '<td class="text-center text-nowrap">';
                                    if ($row['stat'] == 1): // Solicitado
                                        echo '<button class="btn btn-sm btn-info mb-1" onclick="cambiarEstado(' . $row['id'] . ', 2)" title="Marcar en proceso">
                                                ⚙️ Proceso
                                              </button><br>';
                                        echo '<button class="btn btn-sm btn-success" onclick="cambiarEstado(' . $row['id'] . ', 3)" title="Marcar como entregado">
                                                ✓ Entregar
                                              </button>';
                                    elseif ($row['stat'] == 2): // En proceso
                                        echo '<button class="btn btn-sm btn-success" onclick="cambiarEstado(' . $row['id'] . ', 3)" title="Marcar como entregado">
                                                ✓ Entregar
                                              </button>';
                                    else: // Entregado
                                        echo '<span class="text-muted small">Completado</span>';
                                    endif;
                                    echo '</td>';
                                endif;
                                
                                echo '</tr>';
                            }
                        } else {
                            $colspan = ($tipo_usuario == 1 || $tipo_usuario == 4) ? '5' : '4';
                            echo '<tr><td colspan="' . $colspan . '" class="text-center text-muted">No hay solicitudes registradas</td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Solicitar Uniforme -->
<div class="modal fade" id="modalUniforme" tabindex="-1" aria-labelledby="modalUniformeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUniformeLabel">
                    <i class="bi bi-bag-plus"></i> Solicitar Uniforme
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formUniforme" method="POST">
                    
                    <!-- Tipo de Uniforme -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Uniforme <span class="text-danger">*</span></label>
                        <select name="tipo_uniforme" id="tipo_uniforme" class="form-select" required>
                            <option value="">-- Seleccionar tipo --</option>
                            <option value="camisa">Camisa</option>
                            <option value="pantalon">Pantalón</option>
                            <option value="chaleco">Chaleco</option>
                            <option value="carnet de identificacion">Carnet de Identificación</option>
                            <option value="botas">Botas</option>
                            <option value="gorra">Gorra</option>
                        </select>
                    </div>

                    <!-- Talla (dinámico) -->
                    <div class="mb-3" id="contenedorTalla" style="display: none;">
                        <label class="form-label fw-bold">Talla <span class="text-danger">*</span></label>
                        <select name="talla" id="talla" class="form-select" required>
                            <option value="">-- Seleccione primero el tipo --</option>
                        </select>
                    </div>

                    <!-- Mensaje de Alerta para Gorras -->
                    <div class="alert alert-warning d-none" id="alertaGorra" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Atención:</strong> Las gorras solo están disponibles para auxiliares de mantenimiento.
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea name="observacion" id="observacion" class="form-control" rows="3" placeholder="Información adicional sobre tu solicitud (opcional)" maxlength="250"></textarea>
                        <small class="text-muted">Máximo 250 caracteres</small>
                    </div>

                    <input type="hidden" name="solicitar_uniforme" value="1">

                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnEnviarSolicitud">
                            <i class="bi bi-send"></i> Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navegación inferior -->
<br><br><br>
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

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#tablaUniformes').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']], // Ordenar por fecha (columna 2)
        responsive: true
    });

    // Cambio de tipo de uniforme - actualizar tallas
    $('#tipo_uniforme').on('change', function() {
        const tipo = $(this).val();
        const $talla = $('#talla');
        const $contenedorTalla = $('#contenedorTalla');
        const $alertaGorra = $('#alertaGorra');
        
        // Limpiar select de tallas
        $talla.empty().append('<option value="">-- Seleccione una talla --</option>');
        
        // Ocultar alerta de gorra
        $alertaGorra.addClass('d-none');
        
        if (!tipo) {
            $contenedorTalla.hide();
            $talla.prop('required', false);
            return;
        }
        
        let tallas = [];
        
        switch(tipo) {
            case 'camisa':
            case 'chaleco':
                tallas = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'];
                break;
                
            case 'pantalon':
                tallas = ['30', '32', '34', '36', '38', '40', '42', '44', '46', '48'];
                break;
                
            case 'botas':
                tallas = ['38', '39', '40', '41', '42', '43', '44', '45', '46'];
                break;
                
            case 'carnet de identificacion':
                tallas = ['Única'];
                break;
                
            case 'gorra':
                tallas = ['Única'];
                // Mostrar alerta para gorras
                $alertaGorra.removeClass('d-none');
                break;
        }
        
        // Agregar opciones de tallas
        tallas.forEach(function(talla) {
            $talla.append(`<option value="${talla}">${talla}</option>`);
        });
        
        // Mostrar contenedor de tallas
        $contenedorTalla.show();
        $talla.prop('required', true);
    });
    
    // Validar formulario antes de enviar
    $('#formUniforme').on('submit', function(e) {
        const tipo = $('#tipo_uniforme').val();
        const talla = $('#talla').val();
        
        if (!tipo || !talla) {
            e.preventDefault();
            alert('Por favor, complete todos los campos requeridos.');
            return false;
        }
        
        // Deshabilitar botón para evitar doble clic
        $('#btnEnviarSolicitud').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enviando...');
    });
    
    // Resetear formulario al cerrar modal
    $('#modalUniforme').on('hidden.bs.modal', function () {
        $('#formUniforme')[0].reset();
        $('#contenedorTalla').hide();
        $('#alertaGorra').addClass('d-none');
        $('#btnEnviarSolicitud').prop('disabled', false).html('<i class="bi bi-send"></i> Enviar Solicitud');
    });
});

// Función para cambiar estado (RRHH)
function cambiarEstado(uniformeId, nuevoEstado) {
    const estadoTexto = {
        1: 'solicitado',
        2: 'en proceso',
        3: 'entregado'
    };
    
    const mensaje = `¿Está seguro de marcar este uniforme como "${estadoTexto[nuevoEstado]}"?`;
    
    if (confirm(mensaje)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="update_uniforme" value="1">
            <input type="hidden" name="uniforme_id" value="${uniformeId}">
            <input type="hidden" name="nuevo_estado" value="${nuevoEstado}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include __DIR__ . '/footer.php'; ?>
