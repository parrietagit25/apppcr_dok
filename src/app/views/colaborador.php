<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php'; ?>


<div class="container mt-4">
    <div class="input-group mb-3">
        <!--<input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="search-button">
        <button class="btn btn-outline-secondary" type="button" id="search-button">
            <i class="bi bi-search"></i>
        </button>-->
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">RRHH - Mis datos</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">

    <?php 
foreach ($todos_datos as $key => $value) {

    $codigo = $value['codigo_empleado'];
    $nombre_compl = $value['nombre'] . ' ' . $value['apellido'];

    echo 'Codigo Empleado: <b>' . $value['codigo_empleado'] . '</b><br>';

    if (isset($value['codigo_horario'])) {
        echo 'Horario: <b>' . $value['codigo_horario'] . '</b><br>';
    }

    if (isset($value['nombre'])) {
        echo 'Nombre: <b>' . $value['nombre'] . '</b><br>';
    }

    if (isset($value['apellido'])) {
        echo 'Apellido: <b>' . $value['apellido'] . '</b><br>';
    }

    if (isset($value['fecha_nacimiento'])) {
        echo 'Fecha Nacimiento: <b>' . $value['fecha_nacimiento'] . '</b><br>';
    }

    if (isset($value['sexo'])) {
        echo 'Sexo: <b>' . $value['sexo'] . '</b><br>';
    }

    if (isset($value['estado_civil'])) {
        echo 'Estado Civil: <b>' . $value['estado_civil'] . '</b><br>';
    }

    if (isset($value['email'])) {
        echo 'Email: <b>' . $value['email'] . '</b><br>';
    }

    if (isset($value['telefono1'])) {
        echo 'Telefono: <b>' . $value['telefono1'] . '</b><br>';
    }

    if (isset($value['direccion1'])) {
        echo 'Direccion: <b>' . $value['direccion1'] . '</b><br>';
    }

    if (isset($value['fecha_ingreso'])) {
        echo 'Fecha de ingreso: <b>' . $value['fecha_ingreso'] . '</b><br>';
    }

    // Puedes seguir agregando más campos si es necesario
}
?>
    <br>
    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudActualizarDatos">
            Solicitar Actualizacion de datos
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="solicitudActualizarDatos" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="solicitudModalLabel">Solicitar Actualizar mis datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo BASE_URL_CONTROLLER; ?>RRHHController.php" method="POST">

                        <div class="mb-3">
                            <label for="estado_civil" class="form-label">Estado Civil</label>
                            <select class="form-control" name="estado_civil" id="estado_civil" required>
                                <option value="">Seleccione</option>
                                <option value="S">Soltero(a)</option>
                                <option value="C">Casado(a)</option>
                                <option value="D">Divorciado(a)</option>
                                <option value="V">Viudo(a)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono">
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" id="direccion" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <p>Especifique si desea actualizar un dato adicional </p>
                            <textarea name="dato_adicional" class="form-control" style="margin:10px;" required></textarea>
                        </div>

                        <input type="hidden" class="form-control" name="code_cola" value="<?php echo $codigo; ?>">
                        <input type="hidden" class="form-control" name="nombre_cola" value="<?php echo $nombre_compl; ?>">

                        <input type="hidden" class="form-control" name="actualizacion_datos" value="1">
                        
                        <div class="d-flex align-items-center gap-2">
                            <input type="button" class="btn btn-primary" id="btnActualizarDatos" value="Solicitar Actualización">
                            <span id="loaderActualizarDatos" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    </div>
</div>
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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector('#solicitudActualizarDatos form');
    const btn = document.getElementById("btnActualizarDatos");
    const loader = document.getElementById("loaderActualizarDatos");

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

<?php 

include __DIR__ . '/footer.php';