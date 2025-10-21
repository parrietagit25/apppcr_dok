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
    
    #orden {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .item-orden {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 5px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #dee2e6;
    }
    
    .btn-remove-item {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 12px;
        line-height: 1;
    }
    
    .btn-remove-item:hover {
        background: #bb2d3b;
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
            ➕ Solicitar Uniformes
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
                    <th>Cant.</th>
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
                        echo '<td class="text-center"><strong>' . htmlspecialchars($row['cantidad'] ?? 1) . '</strong></td>';
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
                                            <label class='form-label fw-bold text-primary'>Cantidad</label>
                                            <p class='fs-5'><strong>" . htmlspecialchars($row['cantidad'] ?? 1) . "</strong> unidad(es)</p>
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
                    echo '<tr><td colspan="5" class="text-center text-muted">No hay solicitudes registradas</td></tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Solicitar Uniformes (con orden) -->
<div class="modal fade" id="modalUniforme" tabindex="-1" aria-labelledby="modalUniformeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUniformeLabel">
                    <i class="bi bi-bag-plus"></i> Solicitar Uniformes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                
                <!-- Formulario para agregar productos -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Agregar Producto</strong>
                    </div>
                    <div class="card-body">
                        <!-- Tipo de Uniforme -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Uniforme <span class="text-danger">*</span></label>
                            <select id="tipo_uniforme_sel" class="form-select">
                                <option value="">-- Seleccionar tipo --</option>
                                <option value="camisa">Camisa</option>
                                <option value="pantalon">Pantalón</option>
                                <option value="chaleco">Chaleco</option>
                                <option value="sueter">Suéter</option>
                                <option value="carnet de identificacion">Carnet de Identificación</option>
                                <option value="botas">Botas</option>
                                <option value="gorra">Gorra</option>
                            </select>
                        </div>

                        <!-- Talla (dinámico) -->
                        <div class="mb-3" id="contenedorTalla" style="display: none;">
                            <label class="form-label fw-bold">Talla <span class="text-danger">*</span></label>
                            <select id="talla_sel" class="form-select">
                                <option value="">-- Seleccione primero el tipo --</option>
                            </select>
                        </div>
                        
                        <!-- Cantidad -->
                        <div class="mb-3" id="contenedorCantidad" style="display: none;">
                            <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" id="cantidad_sel" class="form-control" min="1" max="10" value="1">
                            <small class="text-muted">Máximo 10 unidades por producto</small>
                        </div>

                        <!-- Mensaje de Alerta para Gorras -->
                        <div class="alert alert-warning d-none" id="alertaGorra" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Atención:</strong> Las gorras solo están disponibles para auxiliares de mantenimiento.
                        </div>
                        
                        <!-- Botón agregar a la orden -->
                        <button type="button" class="btn btn-success w-100" id="btnAgregarOrden">
                            <i class="bi bi-plus-circle"></i> Agregar a la Orden
                        </button>
                    </div>
                </div>
                
                <!-- Orden de productos -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <strong><i class="bi bi-list-check"></i> Mi Orden de Uniformes</strong>
                        <span class="badge bg-light text-dark float-end" id="totalProductos">0 productos</span>
                    </div>
                    <div class="card-body">
                        <div id="orden">
                            <p class="text-muted text-center" id="ordenVacia">La orden está vacía. Agregue productos arriba.</p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones generales -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Observaciones Generales</label>
                    <textarea id="observacion_gen" class="form-control" rows="2" placeholder="Información adicional sobre tu solicitud (opcional)" maxlength="250"></textarea>
                    <small class="text-muted">Máximo 250 caracteres</small>
                </div>

                <!-- Form oculto para enviar -->
                <form id="formUniformeEnviar" method="POST" style="display: none;">
                    <input type="hidden" name="productos" id="productosJSON">
                    <input type="hidden" name="observacion" id="observacionHidden">
                    <input type="hidden" name="solicitar_uniforme" value="1">
                </form>

                <!-- Botones -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnEnviarSolicitud" disabled>
                        <i class="bi bi-send"></i> Enviar Solicitud
                    </button>
                </div>
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
// Array para almacenar productos de la orden
var ordenProductos = [];

function inicializarModuloUniformes() {
    console.log('Inicializando módulo de uniformes...');
    
    // Inicializar DataTable
    /* if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
        try {
            jQuery('#tablaUniformes').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                pageLength: 10,
                order: [[3, 'desc']]
            });
            console.log('DataTable inicializado');
        } catch(e) {
            console.error('Error DataTable:', e);
        }
    } */

    // Obtener referencias a los elementos
    var tipoSelect = document.getElementById('tipo_uniforme_sel');
    var tallaSelect = document.getElementById('talla_sel');
    var cantidadInput = document.getElementById('cantidad_sel');
    var contenedorTalla = document.getElementById('contenedorTalla');
    var contenedorCantidad = document.getElementById('contenedorCantidad');
    var alertaGorra = document.getElementById('alertaGorra');
    var btnAgregar = document.getElementById('btnAgregarOrden');
    var btnEnviar = document.getElementById('btnEnviarSolicitud');
    
    if (!tipoSelect || !tallaSelect || !cantidadInput) {
        console.error('Elementos no encontrados');
        return;
    }
    
    console.log('Elementos encontrados correctamente');

    // Cambio de tipo de uniforme
    tipoSelect.addEventListener('change', function() {
        console.log('Tipo seleccionado:', this.value);
        var tipo = this.value;
        
        // Limpiar select de tallas
        tallaSelect.innerHTML = '<option value="">-- Seleccione una talla --</option>';
        alertaGorra.classList.add('d-none');
        
        if (!tipo) {
            contenedorTalla.style.display = 'none';
            contenedorCantidad.style.display = 'none';
            return;
        }
        
        var tallas = [];
        
        switch(tipo) {
            case 'camisa':
            case 'chaleco':
            case 'sueter':
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
        
        console.log('Tallas a agregar:', tallas);
        
        // Agregar opciones de tallas
        for (var i = 0; i < tallas.length; i++) {
            var option = document.createElement('option');
            option.value = tallas[i];
            option.textContent = tallas[i];
            tallaSelect.appendChild(option);
        }
        
        // Mostrar contenedores
        contenedorTalla.style.display = 'block';
        contenedorCantidad.style.display = 'block';
        console.log('Campos mostrados');
    });
    
    // Agregar producto a la orden
    btnAgregar.addEventListener('click', function() {
        console.log('Click en agregar a orden');
        
        var tipo = tipoSelect.value;
        var talla = tallaSelect.value;
        var cantidad = parseInt(cantidadInput.value) || 1;
        
        console.log('Datos:', tipo, talla, cantidad);
        
        if (!tipo || !talla) {
            alert('Por favor, seleccione tipo y talla antes de agregar a la orden.');
            return;
        }
        
        if (cantidad < 1 || cantidad > 10) {
            alert('La cantidad debe estar entre 1 y 10.');
            return;
        }
        
        // Agregar a la orden
        ordenProductos.push({
            tipo: tipo,
            talla: talla,
            cantidad: cantidad
        });
        
        console.log('Producto agregado. Total:', ordenProductos.length);
        
        // Actualizar vista de la orden
        actualizarOrden();
        
        // Limpiar formulario
        tipoSelect.value = '';
        tallaSelect.innerHTML = '<option value="">-- Seleccione primero el tipo --</option>';
        cantidadInput.value = 1;
        contenedorTalla.style.display = 'none';
        contenedorCantidad.style.display = 'none';
        alertaGorra.classList.add('d-none');
        
        // Mostrar toast de confirmación
        alert('Producto agregado a la orden correctamente');
    });
    
    // Enviar solicitud
    btnEnviar.addEventListener('click', function() {
        if (ordenProductos.length === 0) {
            alert('La orden está vacía. Agregue al menos un producto.');
            return;
        }
        
        console.log('Enviando orden:', ordenProductos);
        
        // Preparar datos
        document.getElementById('productosJSON').value = JSON.stringify(ordenProductos);
        document.getElementById('observacionHidden').value = document.getElementById('observacion_gen').value;
        
        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
        
        // Enviar formulario
        document.getElementById('formUniformeEnviar').submit();
    });
}

// Función para actualizar la vista de la orden
function actualizarOrden() {
    var ordenDiv = document.getElementById('orden');
    var ordenVacia = document.getElementById('ordenVacia');
    var totalProductos = document.getElementById('totalProductos');
    var btnEnviar = document.getElementById('btnEnviarSolicitud');
    
    if (ordenProductos.length === 0) {
        ordenVacia.style.display = 'block';
        totalProductos.textContent = '0 productos';
        btnEnviar.disabled = true;
        
        // Limpiar items
        var items = ordenDiv.querySelectorAll('.item-orden');
        items.forEach(function(item) { item.remove(); });
        return;
    }
    
    ordenVacia.style.display = 'none';
    totalProductos.textContent = ordenProductos.length + ' producto(s)';
    btnEnviar.disabled = false;
    
    // Limpiar orden
    var items = ordenDiv.querySelectorAll('.item-orden');
    items.forEach(function(item) { item.remove(); });
    
    // Agregar cada producto
    ordenProductos.forEach(function(producto, index) {
        var itemDiv = document.createElement('div');
        itemDiv.className = 'item-orden';
        itemDiv.innerHTML = '<div><strong>' + ucfirst(producto.tipo) + '</strong> - Talla: ' + producto.talla + ' - Cant: <strong>' + producto.cantidad + '</strong></div>' +
            '<button class="btn-remove-item" onclick="eliminarDeOrden(' + index + ')" title="Eliminar" type="button">×</button>';
        ordenDiv.appendChild(itemDiv);
    });
    
    console.log('Orden actualizada. Total productos:', ordenProductos.length);
}

// Función para eliminar producto de la orden
function eliminarDeOrden(index) {
    console.log('Eliminando producto índice:', index);
    ordenProductos.splice(index, 1);
    actualizarOrden();
}

// Función helper para capitalizar primera letra
function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarModuloUniformes);
} else {
    inicializarModuloUniformes();
}

// Resetear al cerrar modal
if (typeof jQuery !== 'undefined') {
    jQuery('#modalUniforme').on('hidden.bs.modal', function () {
        console.log('Modal cerrado, reseteando...');
        ordenProductos = [];
        actualizarOrden();
        document.getElementById('tipo_uniforme_sel').value = '';
        document.getElementById('cantidad_sel').value = 1;
        document.getElementById('observacion_gen').value = '';
        document.getElementById('contenedorTalla').style.display = 'none';
        document.getElementById('contenedorCantidad').style.display = 'none';
        document.getElementById('alertaGorra').classList.add('d-none');
        document.getElementById('btnEnviarSolicitud').disabled = true;
        document.getElementById('btnEnviarSolicitud').innerHTML = '<i class="bi bi-send"></i> Enviar Solicitud';
    });
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
