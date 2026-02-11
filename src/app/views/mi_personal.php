<?php
if (!isset($_SESSION['code'])) {
    header("Location: salir.php");
    exit();
}

include __DIR__ . '/header.php';
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>.cursor-pointer { cursor: pointer; }</style>

<div class="container mt-4">

    <div class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="p-3 bg-light rounded">
                    <h5 class="fw-bold">Mi Personal</h5>
                    <p class="mb-0 text-muted"><?php echo ($tipo_usuario == 1) ? 'Todo el personal (vista admin)' : 'Personal a tu cargo'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($mi_personal_lista)): ?>
            <div class="table-responsive">
                <table id="tablaMiPersonal" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mi_personal_lista as $row):
                            $codigo = $row['codigo_empleado'] ?? $row['colaborador_code'] ?? '';
                            $nombre = trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''));
                        ?>
                        <tr>
                            <td>
                                <span class="text-primary cursor-pointer text-decoration-underline" role="button" tabindex="0"
                                    data-bs-toggle="modal" data-bs-target="#modalFotoCarnet" data-codigo="<?php echo htmlspecialchars($codigo); ?>"
                                    title="Ver foto"><?php echo htmlspecialchars($codigo); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($nombre); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Detalles"
                                    data-codigo="<?php echo htmlspecialchars($codigo); ?>"
                                    data-nombre="<?php echo htmlspecialchars($nombre); ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalDetalle">
                                    <i class="bi bi-person-lines-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" title="Permisos"
                                    data-codigo="<?php echo htmlspecialchars($codigo); ?>"
                                    data-nombre="<?php echo htmlspecialchars($nombre); ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalPermisos">
                                    <i class="bi bi-calendar-check"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No hay personal asignado.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Foto -->
    <div class="modal fade" id="modalFotoCarnet" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Foto del colaborador</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <img id="modalFotoCarnetImg" src="" alt="Foto" class="rounded-circle mx-auto d-block" style="width: 180px; height: 180px; object-fit: cover; border: 3px solid #dee2e6;">
                    <p class="small text-muted mt-2 mb-0" id="modalFotoCarnetCodigo"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-lines-fill"></i> Detalle del colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalDetalleBody">
                    <p class="text-muted text-center py-4">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Permisos -->
    <div class="modal fade" id="modalPermisos" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-check"></i> Permisos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong id="modalPermisosNombre"></strong></p>
                    <div id="modalPermisosBody">
                        <p class="text-muted text-center py-4">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br>
</div>

<nav class="navbar fixed-bottom navbar-light bg-light border-top">
    <div class="container-fluid">
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/MainController.php" class="navbar-brand text-center" style="width: 25%;">INICIO</a>
        <a href="<?php echo BASE_URL_CONTROLLER; ?>/RRHHController.php" class="navbar-brand text-center" style="width: 25%;">VOLVER</a>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>
<script>
(function() {
    var baseUrlImage = '<?php echo addslashes(BASE_URL_IMAGE ?? ""); ?>';
    var baseUrlCtrl = '<?php echo addslashes(BASE_URL_CONTROLLER ?? ""); ?>';

    // Tabla DataTables
    if (document.getElementById('tablaMiPersonal') && document.querySelector('#tablaMiPersonal tbody tr')) {
        $('#tablaMiPersonal').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" },
            pageLength: 10,
            order: [[1, 'asc']]
        });
    }

    // Modal foto
    var modalFoto = document.getElementById('modalFotoCarnet');
    if (modalFoto) {
        modalFoto.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            var codigo = btn && btn.getAttribute('data-codigo') ? btn.getAttribute('data-codigo') : '';
            var codigoSufijo = codigo.length >= 2 ? codigo.substring(2) : codigo;
            var img = document.getElementById('modalFotoCarnetImg');
            var codigoText = document.getElementById('modalFotoCarnetCodigo');
            if (img) {
                img.src = baseUrlImage + 'imagen_carnet/' + codigoSufijo + '.jpeg';
                img.onerror = function() { this.src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="180" height="180" viewBox="0 0 180 180"><rect fill="%23ddd" width="180" height="180"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%23999" font-size="14">Sin foto</text></svg>'; };
            }
            if (codigoText) codigoText.textContent = 'Código: ' + (codigo || '');
        });
    }

    // Modal detalles (AJAX)
    var modalDetalle = document.getElementById('modalDetalle');
    if (modalDetalle) {
        modalDetalle.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            var codigo = btn && btn.getAttribute('data-codigo') ? btn.getAttribute('data-codigo') : '';
            var body = document.getElementById('modalDetalleBody');
            body.innerHTML = '<p class="text-muted text-center py-4">Cargando...</p>';
            if (!codigo) {
                body.innerHTML = '<p class="text-danger">Código no indicado.</p>';
                return;
            }
            fetch(baseUrlCtrl + 'RRHHController.php?obtener_detalle_empleado=1&codigo=' + encodeURIComponent(codigo))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.success || !data.datos) {
                        body.innerHTML = '<p class="text-danger">No se encontraron datos.</p>';
                        return;
                    }
                    var d = data.datos;
                    var html = '<table class="table table-sm table-borderless">';
                    if (d.codigo_empleado) html += '<tr><td class="text-muted">Código</td><td><strong>' + escapeHtml(d.codigo_empleado) + '</strong></td></tr>';
                    if (d.nombre) html += '<tr><td class="text-muted">Nombre</td><td>' + escapeHtml(d.nombre) + '</td></tr>';
                    if (d.apellido) html += '<tr><td class="text-muted">Apellido</td><td>' + escapeHtml(d.apellido) + '</td></tr>';
                    if (d.cedula) html += '<tr><td class="text-muted">Cédula</td><td>' + escapeHtml(d.cedula) + '</td></tr>';
                    if (d.fecha_nacimiento) html += '<tr><td class="text-muted">Fecha nacimiento</td><td>' + escapeHtml(d.fecha_nacimiento) + '</td></tr>';
                    if (d.email) html += '<tr><td class="text-muted">Email</td><td>' + escapeHtml(d.email) + '</td></tr>';
                    if (d.telefono1) html += '<tr><td class="text-muted">Teléfono</td><td>' + escapeHtml(d.telefono1) + '</td></tr>';
                    if (d.telefono2) html += '<tr><td class="text-muted">Teléfono 2</td><td>' + escapeHtml(d.telefono2) + '</td></tr>';
                    if (d.nombre_departamento) html += '<tr><td class="text-muted">Departamento</td><td>' + escapeHtml(d.nombre_departamento) + '</td></tr>';
                    if (d.nombre_cargo) html += '<tr><td class="text-muted">Cargo</td><td>' + escapeHtml(d.nombre_cargo) + '</td></tr>';
                    if (d.codigo_horario) html += '<tr><td class="text-muted">Horario</td><td>' + escapeHtml(d.codigo_horario) + '</td></tr>';
                    if (d.fecha_ingreso) html += '<tr><td class="text-muted">Fecha ingreso</td><td>' + escapeHtml(d.fecha_ingreso) + '</td></tr>';
                    if (d.seguro_social) html += '<tr><td class="text-muted">Seguro social</td><td>' + escapeHtml(d.seguro_social) + '</td></tr>';
                    if (d.nacionalidad) html += '<tr><td class="text-muted">Nacionalidad</td><td>' + escapeHtml(d.nacionalidad) + '</td></tr>';
                    if (d.estatus_empleado) html += '<tr><td class="text-muted">Estatus</td><td>' + escapeHtml(d.estatus_empleado) + '</td></tr>';
                    html += '</table>';
                    body.innerHTML = html;
                })
                .catch(function() {
                    body.innerHTML = '<p class="text-danger">Error al cargar los datos.</p>';
                });
        });
    }

    // Modal permisos (AJAX)
    var modalPermisos = document.getElementById('modalPermisos');
    if (modalPermisos) {
        modalPermisos.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            var codigo = btn && btn.getAttribute('data-codigo') ? btn.getAttribute('data-codigo') : '';
            var nombre = btn && btn.getAttribute('data-nombre') ? btn.getAttribute('data-nombre') : '';
            document.getElementById('modalPermisosNombre').textContent = nombre ? (nombre + ' (' + codigo + ')') : codigo;
            var body = document.getElementById('modalPermisosBody');
            body.innerHTML = '<p class="text-muted text-center py-4">Cargando...</p>';
            if (!codigo) {
                body.innerHTML = '<p class="text-danger">Código no indicado.</p>';
                return;
            }
            fetch(baseUrlCtrl + 'RRHHController.php?obtener_permisos_empleado=1&codigo=' + encodeURIComponent(codigo))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data.success) {
                        body.innerHTML = '<p class="text-danger">No se pudieron cargar los permisos.</p>';
                        return;
                    }
                    var permisos = data.permisos || [];
                    if (permisos.length === 0) {
                        body.innerHTML = '<p class="text-muted">Sin solicitudes de permiso.</p>';
                        return;
                    }
                    var statLabel = function(s) {
                        if (s == 1) return '<span class="badge bg-warning">Pendiente</span>';
                        if (s == 2) return '<span class="badge bg-success">Aprobado</span>';
                        return '<span class="badge bg-secondary">Declinado</span>';
                    };
                    var html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Tipo</th><th>Inicio</th><th>Fin</th><th>Fecha solicitud</th><th>Estado</th><th>Descripción</th></tr></thead><tbody>';
                    permisos.forEach(function(p) {
                        html += '<tr><td>' + escapeHtml(p.tipo_licencia || '-') + '</td><td>' + escapeHtml(p.fecha_inicio || '-') + '</td><td>' + escapeHtml(p.fecha_fin || '-') + '</td><td>' + escapeHtml(p.fecha_log || '-') + '</td><td>' + statLabel(p.stat) + '</td><td>' + escapeHtml((p.descripcion || '').substring(0, 50)) + (p.descripcion && p.descripcion.length > 50 ? '…' : '') + '</td></tr>';
                    });
                    html += '</tbody></table></div>';
                    body.innerHTML = html;
                })
                .catch(function() {
                    body.innerHTML = '<p class="text-danger">Error al cargar los permisos.</p>';
                });
        });
    }

    function escapeHtml(str) {
        if (str == null || str === '') return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();
</script>

<?php include __DIR__ . '/footer.php'; ?>
