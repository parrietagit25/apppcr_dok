<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>

<div class="container mt-4">
    <div class="input-group mb-3"></div>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Carta de trabajo</h5>
                </div>
            </div>
        </div>
    </div>

    <?php
    $ver_aprobadas = isset($ver_aprobadas) ? $ver_aprobadas : false;
    $url_base = BASE_URL_CONTROLLER . 'RRHHController.php?carta_trabajo_aprobar=1';
    ?>
    <div class="mb-3">
        <a href="<?php echo $url_base; ?>" class="btn <?php echo !$ver_aprobadas ? 'btn-primary' : 'btn-outline-primary'; ?> me-2">Pendientes de aprobar</a>
        <a href="<?php echo $url_base; ?>&ver=aprobadas" class="btn <?php echo $ver_aprobadas ? 'btn-primary' : 'btn-outline-primary'; ?>">Cartas aprobadas</a>
    </div>

    <div class="row mt-3">
        <h5 class="text-center"><?php echo $ver_aprobadas ? 'Cartas de Trabajo Aprobadas' : 'Solicitudes de Cartas de Trabajo'; ?></h5>
        <table id="tablaCartasTrabajo" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Fecha de Solicitud</th>
                    <th>Carta</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!isset($solicitudes)) {
                    $solicitudes = $class->solicitudes_aprobar();
                }
                if (!empty($solicitudes)) {
                    foreach ($solicitudes as $row) {
                        $desc_seguro = isset($row['salario_pactado']) && $row['salario_pactado'] !== null ? $row['salario_pactado'] * 0.0975 : 0;
                        $desc_educativo = isset($row['salario_pactado']) && $row['salario_pactado'] !== null ? $row['salario_pactado'] * 0.0125 : 0;
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre_completo'] ?? trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_log']); ?></td>
                            <td class="text-center">
                                <?php if ($ver_aprobadas): ?>
                                    <a href="<?php echo BASE_URL_LINK; ?>/generar_carta_pdf_user.php?id=<?php echo (int)$row['id']; ?>" target="_blank" class="btn btn-sm btn-success">Ver PDF</a>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1" data-bs-toggle="modal" data-bs-target="#modalGenerarCarta<?php echo $row['id']; ?>">Editar carta</button>
                                    <button type="button" class="btn btn-sm btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#modalAdjuntar<?php echo $row['id']; ?>">Enviar por correo</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalGenerarCarta<?php echo $row['id']; ?>">Generar Carta</button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($ver_aprobadas): ?>
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                    <a href="#" class="ms-1 small" data-bs-toggle="modal" data-bs-target="#modalAdjuntar<?php echo $row['id']; ?>">(Enviar por correo)</a>
                                <?php else: ?>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalAdjuntar<?php echo $row['id']; ?>">
                                        <?php echo htmlspecialchars($row['estado']); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Modal Enviar carta al colaborador (visible en Pendientes y en Aprobadas) -->
                        <div class="modal fade" id="modalAdjuntar<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?php echo $row['id']; ?>">Enviar carta al colaborador</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="solicitud_id" value="<?php echo $row['id']; ?>">
                                            <?php if ($ver_aprobadas): ?><input type="hidden" name="ver" value="aprobadas"><?php endif; ?>
                                            <p>
                                                ¿Desea generar y enviar la carta de trabajo al colaborador
                                                <strong><?php echo htmlspecialchars($row['nombre_completo'] ?? trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''))); ?></strong>?
                                            </p>
                                            <div class="mb-3">
                                                <label for="comentario<?php echo $row['id']; ?>" class="form-label">Comentario adicional (opcional)</label>
                                                <textarea class="form-control" name="comentario" id="comentario<?php echo $row['id']; ?>" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success" name="enviar_carta_pdf">Generar y Enviar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Generar/Editar Carta (visible en Pendientes y en Aprobadas) -->
                        <div class="modal fade" id="modalGenerarCarta<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabelGenerarCarta<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabelGenerarCarta<?php echo $row['id']; ?>"><?php echo $ver_aprobadas ? 'Editar Carta de Trabajo' : 'Generar Carta de Trabajo'; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="solicitud_id" value="<?php echo $row['id']; ?>">
                                            <?php if ($ver_aprobadas): ?><input type="hidden" name="ver" value="aprobadas"><?php endif; ?>

                                            <div class="mb-3">
                                                <label class="form-label"><strong>Descripción editable:</strong></label>
                                                <textarea class="form-control" name="descripcion" rows="3"><?php echo htmlspecialchars($row['descripcion'] ?? ''); ?></textarea>
                                                <p class="mt-2"><strong>Fecha de solicitud:</strong> <?php echo htmlspecialchars(date("d-m-Y", strtotime($row['fecha_log'] ?? date('Y-m-d')))); ?></p>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6"><label class="form-label">Nombre completo</label><input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Cédula</label><input type="text" class="form-control" name="cedula" value="<?php echo htmlspecialchars($row['cedula']); ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Seguro Social</label><input type="text" class="form-control" name="seguro" value="<?php echo htmlspecialchars($row['seguro_social']); ?>"></div>
                                                <div class="col-md-6"><label class="form-label">Fecha de ingreso</label><input type="date" class="form-control" name="fecha_ingreso" value="<?php echo htmlspecialchars($row['fecha_ingreso']); ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Cargo</label><input type="text" class="form-control" name="cargo" value="<?php echo htmlspecialchars($row['nombre_cargo']); ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Salario</label><input type="number" id="salario" onkeyup="calcular_deducciones()" step="0.01" class="form-control" name="salario" value="<?php echo htmlspecialchars($row['salario_pactado']); ?>" required></div>
                                                <div class="col-md-4"><label class="form-label">Seguro Social (desc)</label><input type="number" step="0.01" class="form-control" name="desc_seguro" value="<?php echo htmlspecialchars($desc_seguro); ?>"></div>
                                                <div class="col-md-4"><label class="form-label">Seguro Educativo</label><input type="number" step="0.01" class="form-control" name="desc_educativo" value="<?php echo htmlspecialchars($desc_educativo); ?>"></div>
                                                <div class="col-md-4"><label class="form-label">Imp. Renta</label><input type="number" step="0.01" class="form-control" name="desc_renta" value="<?php echo htmlspecialchars($row['desc_renta'] ?? '0.00'); ?>"></div>

                                                <div class="col-12 mt-4">
                                                    <label class="form-label"><strong>Otros descuentos</strong></label>
                                                    <div id="otros_descuentos_<?php echo $row['id']; ?>">  
                                                        <?php 
                                                        $otros_descuentos = $class->get_otros_descuentos_por_carta($row['id']);
                                                        $i = 0;
                                                        if (!empty($otros_descuentos)) {
                                                            foreach ($otros_descuentos as $desc) { ?>
                                                                <div class="row g-3 grupo-descuento mt-2 align-items-end" id="grupo_<?php echo $row['id']; ?>_<?php echo $i; ?>">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control" name="otros_descuentos[<?php echo $i; ?>][acreedor]" value="<?php echo htmlspecialchars($desc['acreedor']); ?>" placeholder="Nombre del acreedor" required>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="number" step="0.01" class="form-control" name="otros_descuentos[<?php echo $i; ?>][monto]" value="<?php echo htmlspecialchars($desc['monto']); ?>" placeholder="Monto" required>
                                                                    </div>
                                                                    <div class="col-md-1 text-end">
                                                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDescuento('grupo_<?php echo $row['id']; ?>_<?php echo $i; ?>')" title="Eliminar descuento">×</button>
                                                                    </div>
                                                                </div>
                                                        <?php $i++; } } ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-secondary mt-2" onclick="agregarOtroDescuento('<?php echo $row['id']; ?>')">+ Agregar descuento</button>
                                                    <script>
                                                        // Inicializar contador cuando se carga el modal
                                                        if (typeof contadorDescuentos === 'undefined') {
                                                            contadorDescuentos = {};
                                                        }
                                                        contadorDescuentos[<?php echo $row['id']; ?>] = <?php echo $i; ?>;
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" onclick="guardarCartaSinForm(<?php echo $row['id']; ?>, <?php echo $ver_aprobadas ? 'true' : 'false'; ?>)">Guardar</button>
                                            <button type="submit" formaction="/app/views/generar_carta_pdf.php" formtarget="_blank" class="btn btn-success">Generar PDF</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                            <?php 

                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

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

<script>
function calcular_deducciones() {
    const salario = parseFloat(document.getElementById('salario').value) || 0;
    const descSeguro = (salario * 0.0975).toFixed(2);
    const descEducativo = (salario * 0.0125).toFixed(2);
    document.querySelector('input[name="desc_seguro"]').value = descSeguro;
    document.querySelector('input[name="desc_educativo"]').value = descEducativo;
}

// Contador global para índices únicos
let contadorDescuentos = {};

function agregarOtroDescuento(id) {
    const container = document.getElementById(`otros_descuentos_${id}`);
    
    // Inicializar contador si no existe
    if (!contadorDescuentos[id]) {
        contadorDescuentos[id] = container.querySelectorAll('.grupo-descuento').length;
    }
    
    const index = contadorDescuentos[id]++;
    const grupoId = `grupo_${id}_${index}`;
    
    const html = `
        <div class="row g-3 grupo-descuento mt-2 align-items-end" id="${grupoId}">
            <div class="col-md-8">
                <input type="text" class="form-control" name="otros_descuentos[${index}][acreedor]" placeholder="Nombre del acreedor" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" class="form-control" name="otros_descuentos[${index}][monto]" placeholder="Monto" required>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDescuento('${grupoId}')">&times;</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function eliminarDescuento(grupoId) {
    const elemento = document.getElementById(grupoId);
    if (elemento) {
        // Remover el elemento del DOM
        elemento.remove();
        
        // Reindexar los elementos restantes para evitar problemas con los índices
        const container = elemento.closest('[id^="otros_descuentos_"]');
        if (container) {
            const grupos = container.querySelectorAll('.grupo-descuento');
            grupos.forEach((grupo, nuevoIndex) => {
                // Actualizar los atributos name de los inputs
                const acreedorInput = grupo.querySelector('input[name*="[acreedor]"]');
                const montoInput = grupo.querySelector('input[name*="[monto]"]');
                
                if (acreedorInput) {
                    acreedorInput.name = `otros_descuentos[${nuevoIndex}][acreedor]`;
                }
                if (montoInput) {
                    montoInput.name = `otros_descuentos[${nuevoIndex}][monto]`;
                }
                
                // Actualizar el ID del grupo
                const idMatch = grupo.id.match(/^grupo_(\d+)_\d+$/);
                if (idMatch) {
                    grupo.id = `grupo_${idMatch[1]}_${nuevoIndex}`;
                    
                    // Actualizar el onclick del botón de eliminar
                    const btnEliminar = grupo.querySelector('button[onclick*="eliminarDescuento"]');
                    if (btnEliminar) {
                        btnEliminar.setAttribute('onclick', `eliminarDescuento('grupo_${idMatch[1]}_${nuevoIndex}')`);
                    }
                }
            });
        }
    }
}

   $('#tablaCartasTrabajo').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']]
    });
</script>
<script>
function guardarCartaSinForm(id, verAprobadas) {
    const formData = new FormData();

    // Campos fijos (seleccionados por ID o name dentro del modal)
    const prefix = `modalGenerarCarta${id}`;
    const modal = document.getElementById(prefix);

    formData.append('guardar_formulario', '1');
    formData.append('solicitud_id', id);
    if (verAprobadas) {
        formData.append('ver', 'aprobadas');
    }
    formData.append('nombre', modal.querySelector('[name="nombre"]').value);
    formData.append('cedula', modal.querySelector('[name="cedula"]').value);
    formData.append('seguro', modal.querySelector('[name="seguro"]').value);
    formData.append('fecha_ingreso', modal.querySelector('[name="fecha_ingreso"]').value);
    formData.append('cargo', modal.querySelector('[name="cargo"]').value);
    formData.append('salario', modal.querySelector('[name="salario"]').value);
    formData.append('desc_seguro', modal.querySelector('[name="desc_seguro"]').value);
    formData.append('desc_educativo', modal.querySelector('[name="desc_educativo"]').value);
    formData.append('desc_renta', modal.querySelector('[name="desc_renta"]').value);
    formData.append('descripcion', modal.querySelector('[name="descripcion"]').value);

    // Campos dinámicos de descuentos
    const descuentosContainer = modal.querySelector(`#otros_descuentos_${id}`);
    const grupos = descuentosContainer.querySelectorAll('.grupo-descuento');

    grupos.forEach((grupo, index) => {
        const acreedorInput = grupo.querySelector('input[name*="[acreedor]"]');
        const montoInput = grupo.querySelector('input[name*="[monto]"]');
        
        if (acreedorInput && montoInput) {
            const acreedor = acreedorInput.value.trim();
            const monto = montoInput.value.trim();
            
            // Solo agregar si ambos campos tienen valor
            if (acreedor && monto) {
                formData.append(`otros_descuentos[${index}][acreedor]`, acreedor);
                formData.append(`otros_descuentos[${index}][monto]`, monto);
            }
        }
    });

    // Enviar al backend
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        alert("Datos guardados correctamente");
        // Recargar la página para mostrar los cambios
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Hubo un error al guardar");
    });
}
</script>


<?php include __DIR__ . '/footer.php'; ?>
