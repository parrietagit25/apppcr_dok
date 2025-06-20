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
                        echo "<tr>
                                <td>" . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td class='text-center'>
                                    <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#modalGenerarCarta{$row['id']}'>
                                        Generar Carta
                                    </button>
                                </td>
                                <td>
                                    <a href='#' data-bs-toggle='modal' data-bs-target='#modalAdjuntar{$row['id']}'>
                                        " . htmlspecialchars($row['estado']) . "
                                    </a>
                                </td>
                            </tr>";

                        echo "<div class='modal fade' id='modalAdjuntar{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <form action='' method='POST'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='modalLabel{$row['id']}'>Enviar carta al colaborador</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <input type='hidden' name='solicitud_id' value='{$row['id']}'>
                                                <p>¿Desea generar y enviar la carta de trabajo al colaborador <strong>{$row['nombre']} {$row['apellido']}</strong>?</p>
                                                <div class='mb-3'>
                                                    <label for='comentario{$row['id']}' class='form-label'>Comentario adicional (opcional)</label>
                                                    <textarea class='form-control' name='comentario' id='comentario{$row['id']}' rows='3'></textarea>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit' class='btn btn-success' name='enviar_carta_pdf'>Generar y Enviar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>";

                            /*

                        echo "<div class='modal fade' id='modalGenerarCarta{$row['id']}' tabindex='-1' aria-labelledby='modalLabelGenerarCarta{$row['id']}' aria-hidden='true'>
                                <div class='modal-dialog modal-lg'>
                                    <div class='modal-content'>
                                        <form method='POST'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='modalLabelGenerarCarta{$row['id']}'>Generar Carta de Trabajo</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <input type='hidden' name='solicitud_id' value='{$row['id']}'>
                                                <div class='mb-3'>
                                                    <label class='form-label'><strong>Descripción editable:</strong></label>
                                                    <textarea class='form-control' name='descripcion' rows='3'>" . htmlspecialchars($row['descripcion'] ?? '') . "</textarea>
                                                    <p class='mt-2'><strong>Fecha de solicitud:</strong> " . htmlspecialchars(date("d-m-Y", strtotime($row['fecha_log'] ?? date('Y-m-d')))) . "</p>
                                                </div>

                                                <div class='row g-3'>
                                                    <div class='col-md-6'><label class='form-label'>Nombre completo</label><input type='text' class='form-control' name='nombre' value='" . htmlspecialchars($row['nombre'] ?? '') . "' required></div>
                                                    <div class='col-md-6'><label class='form-label'>Cédula</label><input type='text' class='form-control' name='cedula' value='" . htmlspecialchars($row['cedula'] ?? '') . "' required></div>
                                                    <div class='col-md-6'><label class='form-label'>Seguro Social</label><input type='text' class='form-control' name='seguro' value='" . htmlspecialchars($row['seguro_social'] ?? '') . "'></div>
                                                    <div class='col-md-6'><label class='form-label'>Fecha de ingreso</label><input type='date' class='form-control' name='fecha_ingreso' value='" . htmlspecialchars($row['fecha_ingreso'] ?? '') . "' required></div>
                                                    <div class='col-md-6'><label class='form-label'>Cargo</label><input type='text' class='form-control' name='cargo' value='" . htmlspecialchars($row['nombre_cargo'] ?? '') . "' required></div>
                                                    <div class='col-md-6'><label class='form-label'>Salario</label><input type='number' id='salario' onkeyup='calcular_deducciones()' step='0.01' class='form-control' name='salario' value='" . htmlspecialchars($row['salario_pactado'] ?? '0.00') . "' required></div>
                                                    <div class='col-md-4'><label class='form-label'>Seguro Social (desc)</label><input type='number' step='0.01' class='form-control' name='desc_seguro' value='" . htmlspecialchars($desc_seguro ?? '0.00') . "'></div>
                                                    <div class='col-md-4'><label class='form-label'>Seguro Educativo</label><input type='number' step='0.01' class='form-control' name='desc_educativo' value='" . htmlspecialchars($desc_educativo ?? '0.00') . "'></div>
                                                    <div class='col-md-4'><label class='form-label'>Imp. Renta</label><input type='number' step='0.01' class='form-control' name='desc_renta' value='" . htmlspecialchars($row['desc_renta'] ?? '0.00') . "'></div>

                                                    <div class='col-12 mt-4'>
                                                        <label class='form-label'><strong>Otros descuentos</strong></label>
                                                        <div id='otros_descuentos_{$row['id']}'>";
                                                        if (!empty($row['otros_descuentos'])) {
                                                            foreach ($row['otros_descuentos'] as $i => $desc) {
                                                                $acreedor = htmlspecialchars($desc['acreedor']);
                                                                $monto = htmlspecialchars($desc['monto']);
                                                                echo "<div class='row g-3 grupo-descuento mt-2 align-items-end' id='grupo_{$row['id']}_{$i}'>
                                                                        <div class='col-md-8'>
                                                                            <input type='text' class='form-control' name='otros_descuentos[{$i}][acreedor]' value='{$acreedor}' placeholder='Nombre del acreedor' required>
                                                                        </div>
                                                                        <div class='col-md-3'>
                                                                            <input type='number' step='0.01' class='form-control' name='otros_descuentos[{$i}][monto]' value='{$monto}' placeholder='Monto' required>
                                                                        </div>
                                                                        <div class='col-md-1 text-end'>
                                                                            <button type='button' class='btn btn-danger btn-sm' onclick=\"eliminarDescuento('grupo_{$row['id']}_{$i}')\">&times;</button>
                                                                        </div>
                                                                    </div>";
                                                            }
                                                        }
                                                echo "</div>
                                                        <button type='button' class='btn btn-outline-secondary mt-2' onclick=\"agregarOtroDescuento('{$row['id']}')\">+ Agregar descuento</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='submit' class='btn btn-primary' name='guardar_formulario'>Guardar</button>
                                                <button type='submit' formaction='/app/views/generar_carta_pdf.php' formtarget='_blank' class='btn btn-success'>Generar PDF</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>";*/
                            ?>

                        <div class="modal fade" id="modalGenerarCarta<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabelGenerarCarta<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabelGenerarCarta<?= $row['id'] ?>">Generar Carta de Trabajo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="solicitud_id" value="<?= $row['id'] ?>">

                                            <div class="mb-3">
                                                <label class="form-label"><strong>Descripción editable:</strong></label>
                                                <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($row['descripcion'] ?? '') ?></textarea>
                                                <p class="mt-2"><strong>Fecha de solicitud:</strong> <?= htmlspecialchars(date("d-m-Y", strtotime($row['fecha_log'] ?? date('Y-m-d')))) ?></p>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6"><label class="form-label">Nombre completo</label><input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Cédula</label><input type="text" class="form-control" name="cedula" value="<?= htmlspecialchars($row['cedula']) ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Seguro Social</label><input type="text" class="form-control" name="seguro" value="<?= htmlspecialchars($row['seguro_social']) ?>"></div>
                                                <div class="col-md-6"><label class="form-label">Fecha de ingreso</label><input type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($row['fecha_ingreso']) ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Cargo</label><input type="text" class="form-control" name="cargo" value="<?= htmlspecialchars($row['nombre_cargo']) ?>" required></div>
                                                <div class="col-md-6"><label class="form-label">Salario</label><input type="number" id="salario" onkeyup="calcular_deducciones()" step="0.01" class="form-control" name="salario" value="<?= htmlspecialchars($row['salario_pactado']) ?>" required></div>
                                                <div class="col-md-4"><label class="form-label">Seguro Social (desc)</label><input type="number" step="0.01" class="form-control" name="desc_seguro" value="<?= htmlspecialchars($desc_seguro) ?>"></div>
                                                <div class="col-md-4"><label class="form-label">Seguro Educativo</label><input type="number" step="0.01" class="form-control" name="desc_educativo" value="<?= htmlspecialchars($desc_educativo) ?>"></div>
                                                <div class="col-md-4"><label class="form-label">Imp. Renta</label><input type="number" step="0.01" class="form-control" name="desc_renta" value="<?= htmlspecialchars($row['desc_renta'] ?? '0.00') ?>"></div>

                                                <div class="col-12 mt-4">
                                                    <label class="form-label"><strong>Otros descuentos</strong></label>
                                                    <div id="otros_descuentos_<?= $row['id'] ?>">
                                                        <?php if (!empty($row['otros_descuentos'])):
                                                            foreach ($row['otros_descuentos'] as $i => $desc): ?>
                                                                <div class="row g-3 grupo-descuento mt-2 align-items-end" id="grupo_<?= $row['id'] ?>_<?= $i ?>">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control" name="otros_descuentos[<?= $i ?>][acreedor]" value="<?= htmlspecialchars($desc['acreedor']) ?>" placeholder="Nombre del acreedor" required>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="number" step="0.01" class="form-control" name="otros_descuentos[<?= $i ?>][monto]" value="<?= htmlspecialchars($desc['monto']) ?>" placeholder="Monto" required>
                                                                    </div>
                                                                    <div class="col-md-1 text-end">
                                                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDescuento('grupo_<?= $row['id'] ?>_<?= $i ?>')">×</button>
                                                                    </div>
                                                                </div>
                                                        <?php endforeach; endif; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-secondary mt-2" onclick="agregarOtroDescuento('<?= $row['id'] ?>')">+ Agregar descuento</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" name="guardar_formulario">Guardar</button>
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

<?php include __DIR__ . '/footer.php'; ?>
