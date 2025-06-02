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
                    <h5 class="fw-bold">RRHH - Calamidades </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calamidad">
            Subir Calamidad
        </button>
    </div>
<div class="row mt-5">
    <h5 class="text-center">Solicitudes de Calamidades</h5>
    <table id="tablaCalamidades" class="table table-striped table-bordered mt-3">
        <thead class="table-dark text-center">
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha de Solicitud</th>
                <th>Estado</th>
                <th>Archivo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $calamidades = $class->calamidades();
            if (!empty($calamidades)) {
                foreach ($calamidades as $row) {
                    $nombre = isset($row['nombre']) ? $row['nombre'] : 'Desconocido';
                    $apellido = isset($row['apellido']) ? $row['apellido'] : '';
                    $nombreCompleto = htmlspecialchars($nombre . ' ' . $apellido);

                    $descripcion = htmlspecialchars($row['descripcion'] ?? '');
                    $fecha = htmlspecialchars($row['fecha_log'] ?? '');
                    $estado = htmlspecialchars($row['estado'] ?? '');

                    if (!empty($row['file_add'])) {
                        $archivo = '<a href="' . BASE_URL_FILES_UPDATE_CALAMIDADES . '/' . $row['file_add'] . '" target="_blank" class="btn btn-sm btn-outline-primary">Ver Archivo</a>';
                    } else {
                        $archivo = 'Sin archivo';
                    }

                    echo "<tr>
                            <td>{$nombreCompleto}</td>
                            <td>{$descripcion}</td>
                            <td>{$fecha}</td>
                            <td>{$estado}</td>
                            <td class='text-center'>{$archivo}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No hay solicitudes registradas.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="calamidad" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Subir Calamidad </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Seleccione un archivo</label>
                        <input type='file' class='form-control' name='archivo_calamidades' id='archivo' required>
                    </div>
                    <div class='mb-3'>
                        <label for='archivo' class='form-label'>Monto del prestamo</label>
                        <input type='text' class='form-control' name='monto_prestamo' id='monto_prestamo' value='' required>
                    </div>
                    <div class='mb-3'>
                        <p>Comentario</p>
                        <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    </div>
                    <br>
                    <!--<input type="submit" class="btn btn-primary" value="Subir Calamidad" name="calamidad">-->

                    <input type="hidden" class="form-control" name="calamidad" value="1">
                        
                    <div class="d-flex align-items-center gap-2">
                        <input type="button" class="btn btn-primary" id="btnCalamidad" value="Solicitar Calamidad">
                        <span id="loaderCalamidad" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
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
    const form = document.querySelector('#calamidad form');
    const btn = document.getElementById("btnCalamidad");
    const loader = document.getElementById("loaderCalamidad");

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
<script>
  document.addEventListener('DOMContentLoaded', function () {
    $('#tablaCalamidades').DataTable({
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
      },
      pageLength: 10,
      ordering: true,
      responsive: true
    });
  });
</script>
<?php include __DIR__ . '/footer.php'; ?>
