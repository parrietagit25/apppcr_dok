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

                        // Modal de cada solicitud
                        echo "
                        <div class='modal fade' id='modalAdjuntar{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='modalLabel{$row['id']}'>Adjuntar archivo a la solicitud</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form action='' method='POST' enctype='multipart/form-data'>
                                            <input type='hidden' name='solicitud_id' value='{$row['id']}'>
                                            <div class='mb-3'>
                                                <label for='archivo{$row['id']}' class='form-label'>Seleccione un archivo</label>
                                                <input type='file' class='form-control' name='archivo' id='archivo{$row['id']}' required>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='comentario{$row['id']}' class='form-label'>Comentario (opcional)</label>
                                                <textarea class='form-control' name='comentario' id='comentario{$row['id']}' rows='3'></textarea>
                                            </div>
                                            <button type='submit' class='btn btn-primary' name='aprobar_carta'>Subir archivo</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        

                        <div class='modal fade' id='modalGenerarCarta{$row['id']}' tabindex='-1' aria-labelledby='modalLabelGenerarCarta{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog modal-lg'>
                                <div class='modal-content'>
                                    <form action='' method='POST'>
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
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Nombre completo</label>
                                                    <input type='text' class='form-control' name='nombre' value='" . htmlspecialchars($row['nombre'] ?? '') . "' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Cédula</label>
                                                    <input type='text' class='form-control' name='cedula' value='" . htmlspecialchars($row['cedula'] ?? '') . "' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Seguro Social</label>
                                                    <input type='text' class='form-control' name='seguro' value='" . htmlspecialchars($row['seguro'] ?? '') . "' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Fecha de ingreso</label>
                                                    <input type='date' class='form-control' name='fecha_ingreso' value='" . htmlspecialchars($row['fecha_ingreso'] ?? '') . "' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Cargo</label>
                                                    <input type='text' class='form-control' name='cargo' value='" . htmlspecialchars($row['cargo'] ?? '') . "' required>
                                                </div>
                                                <div class='col-md-6'>
                                                    <label class='form-label'>Salario</label>
                                                    <input type='number' step='0.01' class='form-control' name='salario' value='" . htmlspecialchars($row['salario'] ?? '0.00') . "' required>
                                                </div>
                                                <div class='col-md-4'>
                                                    <label class='form-label'>Seguro Social (desc)</label>
                                                    <input type='number' step='0.01' class='form-control' name='desc_seguro' value='" . htmlspecialchars($row['desc_seguro'] ?? '0.00') . "'>
                                                </div>
                                                <div class='col-md-4'>
                                                    <label class='form-label'>Seguro Educativo</label>
                                                    <input type='number' step='0.01' class='form-control' name='desc_educativo' value='" . htmlspecialchars($row['desc_educativo'] ?? '0.00') . "'>
                                                </div>
                                                <div class='col-md-4'>
                                                    <label class='form-label'>Imp. Renta</label>
                                                    <input type='number' step='0.01' class='form-control' name='desc_renta' value='" . htmlspecialchars($row['desc_renta'] ?? '0.00') . "'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='submit' class='btn btn-primary' name='guardar_formulario'>Guardar</button>
                                            <button formaction='generar_carta_pdf.php' formtarget='_blank' class='btn btn-success' name='generar_pdf'>Generar PDF</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


";

                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de solicitud nueva (si lo usas desde aquí) -->
<div class="modal fade" id="solicitudModal" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Carta de Trabajo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <p>Ingrese la persona o entidad al cual irá dirigida la carta de trabajo</p>
                    <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    <input type="submit" class="btn btn-primary" value="Solicitar Carta" name="carta_trabajo">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navegación inferior -->
<br>
<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<!-- Inicializar DataTable -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaCartasTrabajo').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
