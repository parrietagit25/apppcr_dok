<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold">Solicitudes de Calamidades</h5>
    </div>

    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calamidad">
            Subir Calamidad
        </button>
    </div>

    <table id="tablaCalamidades" class="table table-bordered table-striped">
        <thead class="table-dark text-center">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $calamidades = $class->calamidades();
            foreach ($calamidades as $c):
                $archivo = (!empty($c['file_add']))
                    ? BASE_URL_FILES_UPDATE_CALAMIDADES . '/' . $c['file_add']
                    : 'NULL';
            ?>
            <tr>
                <td><?= htmlspecialchars($c['codigo'] ?? '-') ?></td>
                <td><?= htmlspecialchars(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? '')) ?></td>
                <td><?= htmlspecialchars($c['descripcion'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['fecha_log'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['estado'] ?? '-') ?></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDetalles"
                            data-nombre="<?= htmlspecialchars(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? '')) ?>"
                            data-descripcion="<?= htmlspecialchars($c['descripcion'] ?? '', ENT_QUOTES) ?>"
                            data-fecha="<?= htmlspecialchars($c['fecha_log'] ?? '') ?>"
                            data-estado="<?= htmlspecialchars($c['estado'] ?? '') ?>"
                            data-archivo="<?= $archivo ?>">
                        Ver Detalles
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalles de la Calamidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p><strong>Empleado:</strong> <span id="modalNombre"></span></p>
        <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
        <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
        <p><strong>Descripción:</strong></p>
        <p id="modalDescripcion" class="text-muted"></p>
        <p><strong>Archivo Adjunto:</strong></p>
        <div id="modalArchivo"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Subir Calamidad -->
<div class="modal fade" id="calamidad" tabindex="-1" aria-labelledby="solicitudModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="solicitudModalLabel">Subir Calamidad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="archivo" class="form-label">Seleccione un archivo</label>
            <input type="file" class="form-control" name="archivo_calamidades" id="archivo" required>
          </div>
          <div class="mb-3">
            <label for="monto_prestamo" class="form-label">Monto del préstamo</label>
            <input type="text" class="form-control" name="monto_prestamo" id="monto_prestamo" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Comentario</label>
            <textarea name="descripcion" class="form-control" rows="3"></textarea>
          </div>
          <input type="hidden" name="calamidad" value="1">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalDetalles');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('modalNombre').textContent = button.getAttribute('data-nombre');
        document.getElementById('modalFecha').textContent = button.getAttribute('data-fecha');
        document.getElementById('modalEstado').textContent = button.getAttribute('data-estado');
        document.getElementById('modalDescripcion').textContent = button.getAttribute('data-descripcion');

        const archivo = button.getAttribute('data-archivo');
        const modalArchivo = document.getElementById('modalArchivo');
        if (archivo && archivo !== 'NULL') {
            modalArchivo.innerHTML = `<a href="${archivo}" target="_blank" class="btn btn-outline-primary btn-sm">Ver Archivo</a>`;
        } else {
            modalArchivo.textContent = "No hay archivo adjunto.";
        }
    });

    $('#tablaCalamidades').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        pageLength: 10
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
