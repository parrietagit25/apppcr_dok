<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
    .badge-solicitado { background-color: #ffc107; color: #000; }
    .badge-proceso { background-color: #0dcaf0; color: #000; }
    .badge-entregado { background-color: #198754; color: #fff; }
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
        <br><br>
    </div>

    <!-- Tabla de Mis Solicitudes -->
    <div class="row mt-5">
        <h5 class="text-center">Mis Solicitudes de Uniformes</h5>
        <table id="tablaUniformes" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Tipo</th>
                    <th>Talla</th>
                    <th>Fecha</th>
                    <th>Estado</th>
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
                        echo '<td class="text-center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDetalle' . $row['id'] . '" style="text-decoration: none;">
                                    ' . $status_badge . '
                                </a>
                              </td>';
                        echo '</tr>';
                        
                        // Modal de detalle por cada solicitud
                        $estado_texto = match($row['stat']) {
                            1 => 'Solicitado',
                            2 => 'En Proceso',
                            3 => 'Entregado',
                            default => 'Desconocido'
                        };
                        
                        echo "
                        <div class='modal fade' id='modalDetalle{$row['id']}' tabindex='-1' aria-labelledby='modalDetalleLabel{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header bg-info text-white'>
                                        <h5 class='modal-title' id='modalDetalleLabel{$row['id']}'>
                                            <i class='bi bi-info-circle'></i> Detalle de mi Solicitud
                                        </h5>
                                        <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                                    </div>
                                    <div class='modal-body'>
                                        
                                        <div class='mb-3'>
                                            <label class='form-label fw-bold text-primary'>Tipo de Uniforme</label>
                                            <p class='fs-5'>" . htmlspecialchars(ucfirst($row['tipo'])) . "</p>
                                        </div>
                                        
                                        <div class='mb-3'>
                                            <label class='form-label fw-bold text-primary'>Talla</label>
                                            <p class='fs-5'>" . htmlspecialchars($row['talla']) . "</p>
                                        </div>
                                        
                                        <div class='mb-3'>
                                            <label class='form-label fw-bold text-primary'>Fecha de Solicitud</label>
                                            <p>" . date('d/m/Y H:i', strtotime($row['fecha_log'])) . "</p>
                                        </div>
                                        
                                        <div class='mb-3'>
                                            <label class='form-label fw-bold text-primary'>Observaciones</label>
                                            <p>" . (!empty($row['observacion']) ? htmlspecialchars($row['observacion']) : '<em class=\"text-muted\">Sin observaciones</em>') . "</p>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class='mb-3'>
                                            <label class='form-label fw-bold text-primary'>Estado Actual</label>
                                            <p class='fs-4'>
                                                " . $status_badge . "
                                            </p>
                                        </div>
                                        
                                        <div class='alert alert-info'>
                                            <i class='bi bi-info-circle'></i> 
                                            <strong>Información:</strong> El departamento de RRHH está procesando tu solicitud. 
                                            Recibirás una notificación cuando tu uniforme esté listo para ser entregado.
                                        </div>
                                        
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>
                                            <i class='bi bi-x-circle'></i> Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center text-muted">No hay solicitudes registradas</td></tr>';
                }
            ?>
            </tbody>
        </table>
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
                        <select name="talla" id="talla" class="form-select">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar DataTable
    /*if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        jQuery('#tablaUniformes').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            pageLength: 10,
            order: [[2, 'desc']]
        });
    }*/

    // Cambio de tipo de uniforme - actualizar tallas
    document.getElementById('tipo_uniforme').addEventListener('change', function() {
        const tipo = this.value;
        const tallaSelect = document.getElementById('talla');
        const contenedorTalla = document.getElementById('contenedorTalla');
        const alertaGorra = document.getElementById('alertaGorra');
        
        // Limpiar select de tallas
        tallaSelect.innerHTML = '<option value="">-- Seleccione una talla --</option>';
        
        // Ocultar alerta de gorra
        alertaGorra.classList.add('d-none');
        
        if (!tipo) {
            contenedorTalla.style.display = 'none';
            tallaSelect.required = false;
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
                alertaGorra.classList.remove('d-none');
                break;
        }
        
        // Agregar opciones de tallas
        tallas.forEach(function(talla) {
            const option = document.createElement('option');
            option.value = talla;
            option.textContent = talla;
            tallaSelect.appendChild(option);
        });
        
        // Mostrar contenedor de tallas
        contenedorTalla.style.display = 'block';
        tallaSelect.required = true;
    });
    
    // Validar formulario antes de enviar
    document.getElementById('formUniforme').addEventListener('submit', function(e) {
        const tipo = document.getElementById('tipo_uniforme').value;
        const talla = document.getElementById('talla').value;
        
        if (!tipo || !talla) {
            e.preventDefault();
            alert('Por favor, complete todos los campos requeridos.');
            return false;
        }
        
        // Deshabilitar botón para evitar doble clic
        const btn = document.getElementById('btnEnviarSolicitud');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
