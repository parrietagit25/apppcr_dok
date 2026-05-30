<?php
if (!isset($_SESSION['code'])) {
    header('Location: salir.php');
    exit();
}

include __DIR__ . '/header.php';

$tiene_acceso_rrhh_instructivos = $tiene_acceso_rrhh_instructivos ?? (
    ((int) ($tipo_usuario ?? 0) === 1 || (int) ($tipo_usuario ?? 0) === 4
    || in_array(trim($_SESSION['code'] ?? ''), ['001404', '001688'], true))
);

$mensajes = [
    'asignado' => ['success', 'Colaborador asignado correctamente.'],
    'quitado' => ['success', 'Asignación eliminada.'],
    'error_asignar' => ['danger', 'No se pudo asignar el colaborador.'],
    'error_quitar' => ['danger', 'No se pudo eliminar la asignación.'],
];
$msg_key = $_GET['msg'] ?? '';
$url_volver_instructivos = (isset($_GET['from']) && $_GET['from'] === 'poliza')
    ? rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php?poliza=1'
    : rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php?n_poliza=1';
?>

<div class="container mt-4 mb-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <h4 class="fw-bold text-primary mb-4">Instructivos para el asegurado</h4>

            <?php if (isset($mensajes[$msg_key])): ?>
            <div class="alert alert-<?php echo $mensajes[$msg_key][0]; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($mensajes[$msg_key][1], ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (empty($documentos_instructivos)): ?>
            <div class="alert alert-info">No tiene instructivos disponibles en este momento.</div>
            <?php else: ?>
                <?php foreach ($documentos_instructivos as $doc): ?>
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($doc['titulo'], ENT_QUOTES, 'UTF-8'); ?></h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo htmlspecialchars($doc['url_pdf'], ENT_QUOTES, 'UTF-8'); ?>"
                               target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                            </a>
                            <?php if ($tiene_acceso_rrhh_instructivos && !empty($doc['restringido'])): ?>
                            <button type="button" class="btn btn-outline-success btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalAsignarInstructivo"
                                    data-documento="<?php echo htmlspecialchars($doc['codigo'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-titulo="<?php echo htmlspecialchars($doc['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="bi bi-person-plus"></i> Asignar
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-ver-asignados"
                                    data-bs-toggle="modal" data-bs-target="#modalVerAsignados"
                                    data-documento="<?php echo htmlspecialchars($doc['codigo'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-titulo="<?php echo htmlspecialchars($doc['titulo'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="bi bi-people"></i> Ver asignados
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($tiene_acceso_rrhh_instructivos): ?>
<style>
.instructivos-autocomplete-wrap { position: relative; }
.instructivos-autocomplete-list {
    position: absolute;
    z-index: 1060;
    left: 0;
    right: 0;
    max-height: 220px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    display: none;
}
.instructivos-autocomplete-list.show { display: block; }
.instructivos-autocomplete-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f1f3f5;
}
.instructivos-autocomplete-item:hover,
.instructivos-autocomplete-item.active { background: #e7f1ff; }
.instructivos-autocomplete-item:last-child { border-bottom: none; }
</style>
<div class="modal fade" id="modalAsignarInstructivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo rtrim(BASE_URL_CONTROLLER, '/'); ?>/MainController.php?instructivos_asegurado=1">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3" id="asignarDocTitulo"></p>
                    <input type="hidden" name="instructivos_asignar" value="1">
                    <input type="hidden" name="documento_codigo" id="asignarDocumentoCodigo" value="">
                    <label for="buscar_colaborador_instructivo" class="form-label">Buscar colaborador</label>
                    <div class="instructivos-autocomplete-wrap">
                        <input type="text"
                               id="buscar_colaborador_instructivo"
                               class="form-control"
                               placeholder="Código, nombre o apellido (mín. 2 caracteres)"
                               autocomplete="off">
                        <input type="hidden" name="codigo_empleado" id="codigo_empleado" value="">
                        <div id="listaColaboradoresInstructivo" class="instructivos-autocomplete-list" role="listbox"></div>
                    </div>
                    <small class="text-muted">Escriba al menos 2 caracteres y seleccione de la lista.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalVerAsignados" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Colaboradores asignados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3" id="verAsignadosDocTitulo"></p>
                <div id="verAsignadosContenido" class="text-center text-muted py-3">Cargando...</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const baseController = <?php echo json_encode(rtrim(BASE_URL_CONTROLLER, '/') . '/MainController.php'); ?>;

    const inputBuscar = document.getElementById('buscar_colaborador_instructivo');
    const inputCodigo = document.getElementById('codigo_empleado');
    const listaAutocomplete = document.getElementById('listaColaboradoresInstructivo');
    let debounceTimer = null;
    let resultadosActuales = [];

    function resetAutocompleteAsignar() {
        if (inputBuscar) inputBuscar.value = '';
        if (inputCodigo) inputCodigo.value = '';
        if (listaAutocomplete) {
            listaAutocomplete.innerHTML = '';
            listaAutocomplete.classList.remove('show');
        }
        resultadosActuales = [];
    }

    function renderAutocomplete(items) {
        resultadosActuales = items;
        if (!items.length) {
            listaAutocomplete.innerHTML = '<div class="instructivos-autocomplete-item text-muted">Sin resultados</div>';
            listaAutocomplete.classList.add('show');
            return;
        }
        listaAutocomplete.innerHTML = items.map(function (u, idx) {
            const label = escapeHtml(u.codigo_empleado + ' - ' + (u.nombre || '') + ' ' + (u.apellido || ''));
            return '<div class="instructivos-autocomplete-item" data-idx="' + idx + '" role="option">' + label + '</div>';
        }).join('');
        listaAutocomplete.classList.add('show');
        listaAutocomplete.querySelectorAll('.instructivos-autocomplete-item[data-idx]').forEach(function (el) {
            el.addEventListener('click', function () {
                seleccionarColaborador(resultadosActuales[parseInt(el.getAttribute('data-idx'), 10)]);
            });
        });
    }

    function seleccionarColaborador(u) {
        if (!u) return;
        inputCodigo.value = u.codigo_empleado;
        inputBuscar.value = u.codigo_empleado + ' - ' + (u.nombre || '') + ' ' + (u.apellido || '');
        listaAutocomplete.classList.remove('show');
    }

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            inputCodigo.value = '';
            const q = inputBuscar.value.trim();
            if (q.length < 2) {
                listaAutocomplete.classList.remove('show');
                listaAutocomplete.innerHTML = '';
                return;
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                fetch(baseController + '?instructivos_buscar_colaborador=1&q=' + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderAutocomplete(Array.isArray(data) ? data : []); })
                    .catch(function () {
                        listaAutocomplete.innerHTML = '<div class="instructivos-autocomplete-item text-danger">Error al buscar</div>';
                        listaAutocomplete.classList.add('show');
                    });
            }, 300);
        });

        inputBuscar.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                listaAutocomplete.classList.remove('show');
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.instructivos-autocomplete-wrap')) {
            listaAutocomplete.classList.remove('show');
        }
    });

    const modalAsignar = document.getElementById('modalAsignarInstructivo');
    if (modalAsignar) {
        modalAsignar.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('asignarDocumentoCodigo').value = btn.getAttribute('data-documento') || '';
            document.getElementById('asignarDocTitulo').textContent = btn.getAttribute('data-titulo') || '';
            resetAutocompleteAsignar();
        });

        modalAsignar.querySelector('form').addEventListener('submit', function (e) {
            if (!inputCodigo.value.trim()) {
                e.preventDefault();
                alert('Seleccione un colaborador de la lista de sugerencias.');
            }
        });
    }

    document.querySelectorAll('.btn-ver-asignados').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const doc = btn.getAttribute('data-documento');
            const titulo = btn.getAttribute('data-titulo');
            document.getElementById('verAsignadosDocTitulo').textContent = titulo || '';
            const cont = document.getElementById('verAsignadosContenido');
            cont.innerHTML = '<div class="text-muted py-3">Cargando...</div>';

            fetch(baseController + '?instructivos_asignados_json=1&documento=' + encodeURIComponent(doc))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success || !data.asignados || data.asignados.length === 0) {
                        cont.innerHTML = '<p class="text-muted mb-0">No hay colaboradores asignados.</p>';
                        return;
                    }
                    let html = '<div class="table-responsive"><table class="table table-sm table-striped align-middle"><thead><tr><th>Código</th><th>Nombre</th><th>Asignado</th><th></th></tr></thead><tbody>';
                    data.asignados.forEach(function (row) {
                        const nombre = ((row.nombre || '') + ' ' + (row.apellido || '')).trim() || '—';
                        const fecha = row.fecha_asignacion || '';
                        html += '<tr><td>' + escapeHtml(row.codigo_empleado) + '</td><td>' + escapeHtml(nombre) + '</td><td><small>' + escapeHtml(fecha) + '</small></td><td>';
                        html += '<form method="POST" action="' + baseController + '?instructivos_asegurado=1" class="d-inline" onsubmit="return confirm(\'¿Quitar asignación?\');">';
                        html += '<input type="hidden" name="instructivos_quitar" value="1"><input type="hidden" name="id_asignacion" value="' + row.id + '">';
                        html += '<button type="submit" class="btn btn-outline-danger btn-sm">Quitar</button></form></td></tr>';
                    });
                    html += '</tbody></table></div>';
                    cont.innerHTML = html;
                })
                .catch(function () {
                    cont.innerHTML = '<p class="text-danger mb-0">Error al cargar asignados.</p>';
                });
        });
    });

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text == null ? '' : String(text);
        return d.innerHTML;
    }
})();
</script>
<?php endif; ?>

<br><br><br>

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
                <a href="<?php echo htmlspecialchars($url_volver_instructivos, ENT_QUOTES, 'UTF-8'); ?>" class="text-white text-decoration-none d-block py-2">
                    <i class="bi bi-arrow-left-square-fill fs-4"></i><br><small>Volver</small>
                </a>
            </div>
        </div>
    </div>
</nav>

<?php include __DIR__ . '/footer.php'; ?>
