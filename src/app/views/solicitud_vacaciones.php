<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>

<div class="container mt-4">
    <div class="input-group mb-3">
        
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Solicitud de Vacaciones</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permiso">
            Solicitar Vacaciones
        </button>
    </div>
    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Vacaciones </h5>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $status = '';
                $permisos = $class->select_vacaciones();
                if (!empty($permisos)) {
                    foreach ($permisos as $row) {

                        if ($row['stat'] == 1) {
                            $status = 'Solicitado';
                        }elseif ($row['stat'] == 2) {
                            $status = 'Aprobado';
                        }else {
                            $status = 'Declinado';
                        }

                        echo "<tr>
                                <td>{$row['nombre']} {$row['apellido']}</td>
                                <td>{$row['comentario_cola']}</td>
                                <td>{$row['fecha_log']}</td>
                                <td>{$status}</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No hay solicitudes registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="permiso" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Vacaciones </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Seleccione Su jefe inmediato</label>
                        <input list="jefes" id="id_jefe" name="id_jefe">
                        <datalist id="jefes">
                            <option value="">Seleccionar</option>
                            <?php foreach ($select_jefe as $key => $value) { ?>
                                <option value="<?php echo $value['codigo_empleado']; ?>"><?php echo $value['nombre'].' '.$value['apellido']; ?></option>
                            <?php } ?>
                        </datalist>
                    </div>
                    <div class='mb-3'>
                        <p>Indicar el periodo y detalles</p>
                        <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-primary" value="Solicitar" name="solicitud_vacaciones">
                </form>
            </div>
        </div>
    </div>
</div>

<br>
<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
        <a href="#" class="navbar-brand text-center" style="width: 25%;"></a>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
