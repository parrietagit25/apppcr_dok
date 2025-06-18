<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<!-- Estilos y scripts de DataTables -->
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
    
    <div class="text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudModal">
            Solicitar Carta de Trabajo
        </button>
        <br><br><br>
        <p>
            <span style="color:red;"><b>Nota: Las cartas se gestionan únicamente los días jueves. Por favor, una vez solicitadas, espere hasta el siguiente jueves para su procesamiento.</b></span>
        </p>
    </div>

    <div class="row mt-5">
        <h5 class="text-center">Solicitudes de Cartas de Trabajo</h5>
        <table id="tablaCartas" class="table table-striped table-bordered mt-3">
            <thead class="table-dark text-center">
                <tr>
                    <th>Carta</th>
                    <th>Descripción</th>
                    <th>Fecha de Solicitud</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $key => $value): ?>
                <tr>
                    <td class="text-center">
                        <?php if (!empty($value['carta_generada']) && $value['carta_generada'] == 1): ?>
                            <a href="/app/views/generar_carta_pdf_user.php?id=<?php echo $value['id']; ?>" target="_blank" class="btn btn-sm btn-success">
                                Descargar PDF
                            </a>
                        <?php else: ?>
                            <span class="text-muted">No disponible</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($value['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($value['fecha_log']); ?></td>
                    <td><?php echo htmlspecialchars($value['estado']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Solicitud -->
<div class="modal fade" id="solicitudModal" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitar Carta de Trabajo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <p>Ingrese la persona o entidad al cual irá dirigida la carta de trabajo</p>
                    <textarea name="descripcion" class="form-control" style="margin:10px;"></textarea>
                    <input type="hidden" name="carta_trabajo" value="1">
                    <div class="d-flex align-items-center gap-2 mt-3">
                        <input type="button" class="btn btn-primary" id="btnSolicitarCarta" value="Solicitar Carta">
                        <span id="loaderSolicitarCarta" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></span>
                    </div>
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

<!-- JS: Solicitud de carta + DataTables -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Solicitar carta
    const form = document.querySelector('#solicitudModal form');
    const btn = document.getElementById("btnSolicitarCarta");
    const loader = document.getElementById("loaderSolicitarCarta");

    if (form && btn && loader) {
      btn.addEventListener("click", function () {
        btn.disabled = true;
        btn.value = "Enviando...";
        loader.classList.remove("d-none");

        setTimeout(() => {
          form.submit();
        }, 800);
      });
    }

    // Inicializar DataTable con orden por fecha descendente
    $('#tablaCartas').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10,
        order: [[2, 'desc']]
    });
  });
</script>


<?php include __DIR__ . '/footer.php'; ?>
