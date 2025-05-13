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
                    <h5 class="fw-bold">Solicitud de Permiso</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permiso">
            Solicitar Permiso
        </button>
        <br>
        <br>
        <p>
            <spam style="color:red;"><b>Nota: Recuerde que las vacaciones deben ser aprobadas previamente por su supervisor para su debida autorización.</b></spam>
        </p>
    </div>
    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Permiso </h5>
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
                $permisos = $class->select_permisos();
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
                                <td>{$row['descripcion']}</td>
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
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Permiso </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Seleccione Su jefe inmediato</label>
                        <select name="id_jefe" id="" class="form-control">
                            <option value="">Seleccionar</option>
                            <?php foreach ($select_jefe as $key => $value) { ?>
                                <option value="<?php echo $value['code_empleado']; ?>"><?php echo $value['nombre'].' '.$value['apellido']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Tipo de licencia</label>
                        <select name="tipo_licencia" id="" class="form-control">
                            <option value="">Seleccionar</option>
                            <option value="Vacaciones">Vacaciones // <?php foreach ($mis_vacas as $key => $value) { ?><p> Vacaciones Acumuladas: <b><?php echo $value['dias_vaca_acu_tiempo']; ?></b> Días</p> <?php } ?></option>
                            <option value="Enfermedad">Enfermedad</option>
                            <option value="Duelo">Duelo</option>
                            <option value="Tiempo sin pago">Tiempo sin pago</option>
                            <option value="Compensatorio">Compensatorio</option>
                            <option value="Flex day">Flex day</option>
                            <option value="Cita Medica">Cita Medica</option>
                            <option value="Teletrabajo">Teletrabajo</option>
                        </select>
                    </div>
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="" class="form-control bloquear-pasado">
                    </div>
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="" class="form-control bloquear-pasado">
                    </div>
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Adjuntar un archivo</label>
                        <input type="file" name="archivo_adjunto" id="" class="form-control bloquear-pasado">
                    </div>
                    <div class='mb-3'>
                        <p>Descripcion del Permiso</p>
                        <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    </div>
                    <br>
                    <!--<input type="submit" class="btn btn-primary" value="Solicitar" name="solicitud_permiso">-->

                    <input type="hidden" class="form-control" name="solicitud_permiso" value="1">
                        
                    <div class="d-flex align-items-center gap-2">
                        <input type="button" class="btn btn-primary" id="btnPermiso" value="Solicitar Permiso">
                        <span id="loaderPermiso" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                    </div>

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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('#permiso form');
    const btn = document.getElementById("btnPermiso");
    const loader = document.getElementById("loaderPermiso");

    if (form && btn && loader) {
      btn.addEventListener("click", function (e) {
        // Desactiva el botón y muestra el loader
        btn.disabled = true;
        btn.value = "Enviando...";
        loader.classList.remove("d-none");

        // Espera 800ms y luego envía el formulario normalmente
        setTimeout(() => {
          form.submit();
        }, 800);
      });
    }
  });
</script>
<?php include __DIR__ . '/footer.php'; ?>
