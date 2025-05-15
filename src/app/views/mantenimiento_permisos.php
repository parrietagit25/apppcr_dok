<?php
// app/views/solicitudes_registradas.php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold">Solicitudes Registradas</h5>
        <a href="exportar_solicitudes_excel.php" class="btn btn-success">📥 Exportar a Excel</a>
    </div>

    <table id="tablaSolicitudes" class="table table-bordered table-striped">
        <thead class="table-dark text-center">
            <tr>
                <th>Tipo</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['tipo']) ?></td>
                    <td><?= htmlspecialchars($s['codigo']) ?></td>
                    <td><?= htmlspecialchars($s['nombre']) ?></td>
                    <td><?= htmlspecialchars($s['apellido']) ?></td>
                    <td><?= htmlspecialchars($s['fecha_log']) ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetalles"
                                data-tipo="<?= $s['tipo'] ?>"
                                data-nombre="<?= $s['nombre'] . ' ' . $s['apellido'] ?>"
                                data-fecha="<?= $s['fecha_log'] ?>"
                                data-descripcion="<?= htmlspecialchars($s['descripcion'], ENT_QUOTES) ?>"
                                data-archivo="<?= $s['file_add'] ?>">
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
        <h5 class="modal-title">Detalles de la Solicitud</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p><strong>Tipo:</strong> <span id="modalTipo"></span></p>
        <p><strong>Empleado:</strong> <span id="modalNombre"></span></p>
        <p><strong>Fecha de Registro:</strong> <span id="modalFecha"></span></p>
        <p><strong>Descripción:</strong></p>
        <p id="modalDescripcion" class="text-muted"></p>
        <p><strong>Archivo Adjunto:</strong></p>
        <div id="modalArchivo"></div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalDetalles');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('modalTipo').textContent = button.getAttribute('data-tipo');
        document.getElementById('modalNombre').textContent = button.getAttribute('data-nombre');
        document.getElementById('modalFecha').textContent = button.getAttribute('data-fecha');
        document.getElementById('modalDescripcion').textContent = button.getAttribute('data-descripcion');

        const archivo = button.getAttribute('data-archivo');
        const modalArchivo = document.getElementById('modalArchivo');
        if (archivo && archivo !== 'NULL') {
            modalArchivo.innerHTML = `<a href="${archivo}" target="_blank" class="btn btn-outline-primary btn-sm">Ver Archivo</a>`;
        } else {
            modalArchivo.textContent = "No hay archivo adjunto.";
        }
    });

    $('#tablaSolicitudes').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
