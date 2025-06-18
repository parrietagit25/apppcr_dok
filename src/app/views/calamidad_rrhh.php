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
                    <h5 class="fw-bold">RRHH - Incapacidad</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Calamidades</h5>
        <table id="tablaCalamidadesRRHH" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 $calamidades = $class->calamidades_rrhh();
                 if (!empty($calamidades)) {
                     foreach ($calamidades as $row) {
                         echo "<tr>
                                <td>" . htmlspecialchars($row['nombre']) . "</td>
                                <td>" . htmlspecialchars($row['descripcion']) . "</td>
                                <td>" . htmlspecialchars($row['fecha_log']) . "</td>
                                <td>
                                    <a href='#' data-bs-toggle='modal' data-bs-target='#modalAdjuntar{$row['id']}'>
                                        " . htmlspecialchars($row['estado']) . "
                                    </a>
                                </td>
                              </tr>";

                        echo "
                        <div class='modal fade' id='modalAdjuntar{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='modalLabel{$row['id']}'>Marcar como recibido</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <form action='' method='POST' enctype='multipart/form-data'>
                                            <input type='hidden' name='calamidad_id' value='{$row['id']}'>
                                            <div class='mb-3'>";
                                                
                                            if (!empty($row['file_add'])) {
                                                echo '<a href="' . BASE_URL_FILES_UPDATE_CALAMIDADES . '/' . $row['file_add'] . '" target="_blank" class="btn btn-outline-primary btn-sm">Ver Archivo</a>';
                                            } else {
                                                echo '<p>No hay archivo adjunto.</p>';
                                            }

                        echo "              </div>

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Colaborador</b> </label>
                                                <p>{$row['nombre']}</p>
                                            </div>
                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Fecha de la solicitud </b></label>
                                                <p>{$row['fecha_log']}</p>
                                            </div>

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Departamento </b></label>
                                                <p>{$row['departamento']}</p>
                                            </div>
                                            
                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Motivo de su solicitud </b></label>
                                                <p>{$row['descripcion']}</p>
                                            </div>

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Monto solicitado </b></label>
                                                <p>{$row['monto']}</p>
                                            </div>

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Plazo </b></label>
                                                <p>{$row['plazo']}</p>
                                            </div>

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'> <b>Forma de pago </b></label>
                                                <p>{$row['forma_pago']}</p>
                                            </div>
                                            

                                            <div class='mb-3'>
                                                <label for='comentario' class='form-label'><b>Comentario (opcional)</b></label>
                                                <textarea class='form-control' name='comentario' rows='3'></textarea>
                                            </div>
                                            <button type='submit' class='btn btn-primary' name='aprobar_calamidad'>Revisado</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <br>
    </div>
        <br>
        <br>
        <br>
        <br>
</div>

<!-- Modal oculto (puedes eliminar si no se usa en esta vista) -->
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
                    <br>
                    <input type="submit" class="btn btn-primary" value="Solicitar Carta" name="carta_trabajo">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navegación inferior -->
<br>
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

<!-- Inicializar DataTable -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#tablaCalamidadesRRHH').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']] // Ordenar por "Fecha de Solicitud" descendente
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
