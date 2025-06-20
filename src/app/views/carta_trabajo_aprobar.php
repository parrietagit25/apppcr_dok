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

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Cartas de Trabajo</h5>
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
                $solicitudes = $class->solicitudes_aprobar();
                if (!empty($solicitudes)) {
                    foreach ($solicitudes as $row) {
                        $desc_seguro = $row['salario_pactado'] * 0.0975;
                        $desc_educativo = $row['salario_pactado'] * 0.0125;
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_log']); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalGenerarCarta<?php echo $row['id']; ?>">Generar Carta</button>
                            </td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalAdjuntar<?php echo $row['id']; ?>">
                                    <?php echo htmlspecialchars($row['estado']); ?>
                                </a>
                            </td>
                        </tr>

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
                                            <p>
                                                ¿Desea generar y enviar la carta de trabajo al colaborador
                                                <strong><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></strong>?
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


                        <div class="modal fade" id="modalGenerarCarta<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabelGenerarCarta<?php echo $row['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabelGenerarCarta<?php echo $row['id']; ?>">Generar Carta de Trabajo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="solicitud_id" value="<?php echo $row['id']; ?>">

                                            <div class="mb-3">
                                                <label class="form-label"><strong>Descripción editable:</strong></label>
                                                <textarea class="form-control" name="descripcion" rows="3"><?php htmlspecialchars($row['descripcion'] ?? '') ?></textarea>
                                                <p class="mt-2"><strong>Fecha de solicitud:</strong> <?php htmlspecialchars(date("d-m-Y", strtotime($row['fecha_log'] ?? date('Y-m-d')))) ?></p>
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
                                                        <?php if (!empty($row['otros_descuentos'])){
                                                            foreach ($row['otros_descuentos'] as $i => $desc){ ?>
                                                                <div class="row g-3 grupo-descuento mt-2 align-items-end" id="grupo_<?php echo $row['id']; ?>_<?php echo $i; ?>">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control" name="otros_descuentos[<?php echo $i; ?>][acreedor]" value="<?php echo htmlspecialchars($desc['acreedor']); ?>" placeholder="Nombre del acreedor" required>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="number" step="0.01" class="form-control" name="otros_descuentos[<?php echo $i; ?>][monto]" value="<?php echo htmlspecialchars($desc['monto']); ?>" placeholder="Monto" required>
                                                                    </div>
                                                                    <div class="col-md-1 text-end">
                                                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDescuento('grupo_<?php echo $row['id']; ?>_<?php echo $i; ?>')">×</button>
                                                                    </div>
                                                                </div>
                                                        <?php } ?> 
                                                        <?php } ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-secondary mt-2" onclick="agregarOtroDescuento('<?php echo $row['id']; ?>')">+ Agregar descuento</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" onclick="guardarCartaSinForm(<?php echo $row['id']; ?>)">Guardar</button>
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

function agregarOtroDescuento(id) {
    const container = document.getElementById(`otros_descuentos_${id}`);
    const index = container.querySelectorAll('.grupo-descuento').length;
    const html = `
        <div class="row g-3 grupo-descuento mt-2 align-items-end" id="grupo_${id}_${index}">
            <div class="col-md-8">
                <input type="text" class="form-control" name="otros_descuentos[${index}][acreedor]" placeholder="Nombre del acreedor" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" class="form-control" name="otros_descuentos[${index}][monto]" placeholder="Monto" required>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDescuento('grupo_${id}_${index}')">&times;</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function eliminarDescuento(grupoId) {
    const elemento = document.getElementById(grupoId);
    if (elemento) {
        elemento.remove();
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
function guardarCartaSinForm(id) {
    const formData = new FormData();

    // Campos fijos (seleccionados por ID o name dentro del modal)
    const prefix = `modalGenerarCarta${id}`;
    const modal = document.getElementById(prefix);

    formData.append('guardar_formulario', '1');
    formData.append('solicitud_id', id);
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
        const acreedor = grupo.querySelector('[name^="otros_descuentos"][name*="[acreedor]"]').value;
        const monto = grupo.querySelector('[name^="otros_descuentos"][name*="[monto]"]').value;
        formData.append(`otros_descuentos[${index}][acreedor]`, acreedor);
        formData.append(`otros_descuentos[${index}][monto]`, monto);
    });

    // Enviar al backend
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        console.log('Resultado del servidor:', result);
        alert("Datos guardados correctamente");
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Hubo un error al guardar");
    });
}
</script>


<?php include __DIR__ . '/footer.php'; ?>
