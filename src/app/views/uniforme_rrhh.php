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
    
    #carrito {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .item-carrito {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 5px;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
                                            <p class='fs-5'>" . htmlspecialchars($row['cantidad'] ?? 1) . "</p>
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

<!-- Modal Solicitar Uniformes (con carrito) -->
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
                            <select id="tipo_uniforme" class="form-select">
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
                            <select id="talla" class="form-select">
                                <option value="">-- Seleccione primero el tipo --</option>
                            </select>
                        </div>
                        
                        <!-- Cantidad -->
                        <div class="mb-3" id="contenedorCantidad" style="display: none;">
                            <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" id="cantidad" class="form-control" min="1" max="10" value="1">
                            <small class="text-muted">Máximo 10 unidades por producto</small>
                        </div>

                        <!-- Mensaje de Alerta para Gorras -->
                        <div class="alert alert-warning d-none" id="alertaGorra" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Atención:</strong> Las gorras solo están disponibles para auxiliares de mantenimiento.
                        </div>
                        
                        <!-- Botón agregar al carrito -->
                        <button type="button" class="btn btn-success w-100" id="btnAgregarCarrito">
                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
                
                <!-- Carrito de productos -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <strong><i class="bi bi-cart3"></i> Carrito de Uniformes</strong>
                        <span class="badge bg-light text-dark float-end" id="totalProductos">0 productos</span>
                    </div>
                    <div class="card-body">
                        <div id="carrito" class="mb-2">
                            <p class="text-muted text-center" id="carritoVacio">El carrito está vacío. Agregue productos arriba.</p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones generales -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Observaciones Generales</label>
                    <textarea id="observacion" class="form-control" rows="2" placeholder="Información adicional sobre tu solicitud (opcional)" maxlength="250"></textarea>
                    <small class="text-muted">Máximo 250 caracteres</small>
                </div>

                <!-- Form oculto para enviar -->
                <form id="formUniforme" method="POST" style="display: none;">
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
// Array para almacenar productos del carrito
let carritoProductos = [];

document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar DataTable
    if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        jQuery('#tablaUniformes').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            pageLength: 10,
            order: [[3, 'desc']]
        });
    }

    // Cambio de tipo de uniforme - actualizar tallas
    document.getElementById('tipo_uniforme').addEventListener('change', function() {
        const tipo = this.value;
        const tallaSelect = document.getElementById('talla');
        const contenedorTalla = document.getElementById('contenedorTalla');
        const contenedorCantidad = document.getElementById('contenedorCantidad');
        const alertaGorra = document.getElementById('alertaGorra');
        
        // Limpiar select de tallas
        tallaSelect.innerHTML = '<option value="">-- Seleccione una talla --</option>';
        
        // Ocultar alerta de gorra
        alertaGorra.classList.add('d-none');
        
        if (!tipo) {
            contenedorTalla.style.display = 'none';
            contenedorCantidad.style.display = 'none';
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
        
        // Mostrar contenedores
        contenedorTalla.style.display = 'block';
        contenedorCantidad.style.display = 'block';
        tallaSelect.required = true;
    });
    
    // Agregar producto al carrito
    document.getElementById('btnAgregarCarrito').addEventListener('click', function() {
        const tipo = document.getElementById('tipo_uniforme').value;
        const talla = document.getElementById('talla').value;
        const cantidad = parseInt(document.getElementById('cantidad').value) || 1;
        
        if (!tipo || !talla) {
            alert('Por favor, seleccione tipo y talla antes de agregar al carrito.');
            return;
        }
        
        if (cantidad < 1 || cantidad > 10) {
            alert('La cantidad debe estar entre 1 y 10.');
            return;
        }
        
        // Agregar al carrito
        carritoProductos.push({
            tipo: tipo,
            talla: talla,
            cantidad: cantidad
        });
        
        // Actualizar vista del carrito
        actualizarCarrito();
        
        // Limpiar formulario
        document.getElementById('tipo_uniforme').value = '';
        document.getElementById('talla').innerHTML = '<option value="">-- Seleccione primero el tipo --</option>';
        document.getElementById('cantidad').value = 1;
        document.getElementById('contenedorTalla').style.display = 'none';
        document.getElementById('contenedorCantidad').style.display = 'none';
        document.getElementById('alertaGorra').classList.add('d-none');
        
        // Mostrar mensaje
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = '<i class="bi bi-check-circle"></i> Producto agregado al carrito';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
    
    // Enviar solicitud
    document.getElementById('btnEnviarSolicitud').addEventListener('click', function() {
        if (carritoProductos.length === 0) {
            alert('El carrito está vacío. Agregue al menos un producto.');
            return;
        }
        
        // Preparar datos
        document.getElementById('productosJSON').value = JSON.stringify(carritoProductos);
        document.getElementById('observacionHidden').value = document.getElementById('observacion').value;
        
        // Deshabilitar botón
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
        
        // Enviar formulario
        document.getElementById('formUniforme').submit();
    });
    
    // Resetear al cerrar modal
    jQuery('#modalUniforme').on('hidden.bs.modal', function () {
        carritoProductos = [];
        actualizarCarrito();
        document.getElementById('tipo_uniforme').value = '';
        document.getElementById('cantidad').value = 1;
        document.getElementById('observacion').value = '';
        document.getElementById('contenedorTalla').style.display = 'none';
        document.getElementById('contenedorCantidad').style.display = 'none';
        document.getElementById('alertaGorra').classList.add('d-none');
        document.getElementById('btnEnviarSolicitud').disabled = true;
        document.getElementById('btnEnviarSolicitud').innerHTML = '<i class="bi bi-send"></i> Enviar Solicitud';
    });
});

// Función para actualizar la vista del carrito
function actualizarCarrito() {
    const carritoDiv = document.getElementById('carrito');
    const carritoVacio = document.getElementById('carritoVacio');
    const totalProductos = document.getElementById('totalProductos');
    const btnEnviar = document.getElementById('btnEnviarSolicitud');
    
    if (carritoProductos.length === 0) {
        carritoVacio.style.display = 'block';
        totalProductos.textContent = '0 productos';
        btnEnviar.disabled = true;
        
        // Limpiar items
        const items = carritoDiv.querySelectorAll('.item-carrito');
        items.forEach(item => item.remove());
        return;
    }
    
    carritoVacio.style.display = 'none';
    totalProductos.textContent = carritoProductos.length + ' producto(s)';
    btnEnviar.disabled = false;
    
    // Limpiar carrito
    const items = carritoDiv.querySelectorAll('.item-carrito');
    items.forEach(item => item.remove());
    
    // Agregar cada producto
    carritoProductos.forEach((producto, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'item-carrito';
        itemDiv.innerHTML = `
            <div>
                <strong>${ucfirst(producto.tipo)}</strong> - Talla: ${producto.talla} - Cant: <strong>${producto.cantidad}</strong>
            </div>
            <button class="btn-remove-item" onclick="eliminarDelCarrito(${index})" title="Eliminar">
                <i class="bi bi-x"></i>
            </button>
        `;
        carritoDiv.appendChild(itemDiv);
    });
}

// Función para eliminar producto del carrito
function eliminarDelCarrito(index) {
    carritoProductos.splice(index, 1);
    actualizarCarrito();
}

// Función helper para capitalizar primera letra
function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
